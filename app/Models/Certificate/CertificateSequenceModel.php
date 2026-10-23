<?php

namespace App\Models\Certificate;

use CodeIgniter\Model;

/**
 * Penghitung nomor urut sertifikat per tahun.
 * Tabel: certificate_sequences(year PK, last_number, timestamps).
 *
 * nextForYear() dipakai CertificateGenerator::generateFromApproval() untuk
 * membuat certificate_number berformat SERT/{tahun}/{5 digit}.
 * Concurrency-safe via INSERT ... ON DUPLICATE KEY UPDATE.
 *
 * Tidak ada regenerate/MAX(...) lagi — pendekatan lama (MAX + UPDATE) racy
 * dan sudah dihapus.
 */
class CertificateSequenceModel extends Model
{
    protected $table         = 'certificate_sequences';
    protected $primaryKey    = 'year';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['year', 'last_number'];

    /**
     * Ambil nomor urut berikutnya untuk tahun tertentu, atomik.
     * Memastikan (year, last_number) selalu bertambah +1 per panggilan,
     * tidak pernah kembali dan tidak pernah duplikat walau dipanggil
     * bersamaan.
     */
    public function nextForYear(int $year): int
    {
        if ($year < 1900 || $year > 2999) {
            throw new \InvalidArgumentException('Tahun sertifikat tidak valid.');
        }

        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        $db->transStart();

        // ON DUPLICATE KEY UPDATE last_number = last_number + 1 — atomic
        $sql = 'INSERT INTO certificate_sequences (year, last_number, created_at, updated_at)
                VALUES (?, 1, ?, ?)
                ON DUPLICATE KEY UPDATE last_number = last_number + 1, updated_at = ?';
        $db->query($sql, [$year, $now, $now, $now]);

        $row = $db->query(
            'SELECT last_number FROM certificate_sequences WHERE year = ? FOR UPDATE',
            [$year]
        )->getRowArray();

        $db->transComplete();

        if ($db->transStatus() === false || ! $row) {
            throw new \RuntimeException('Gagal membuat nomor urut sertifikat.');
        }

        return (int) $row['last_number'];
    }
}