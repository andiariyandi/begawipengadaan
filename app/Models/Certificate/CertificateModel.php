<?php

namespace App\Models\Certificate;

use CodeIgniter\Model;

/**
 * certificates (v4).
 *
 * Kolom v4:
 *  - source_type               ENUM('learning_enrollment','event_registration')
 *  - source_id                 UNSIGNED
 *  - UNIQUE(source_type, source_id)
 *  - user_id, template_id, certificate_number (UNIQUE), verification_token_hash (UNIQUE)
 *  - status                    ENUM('pending','issued','revoked','expired')
 *  - issued_at, expires_at, issued_by
 *  - revoked_at, revoked_by, revocation_reason
 *  - pdf_file_id               → file_assets.id
 *  - replaced_by_certificate_id→ certificates.id
 *
 * Life-cycle driver: baris certificates dibuat oleh CertificateGenerator
 * dari approval yang sudah approved. TIDAK ada create manual dari form admin.
 */
class CertificateModel extends Model
{
    protected $table         = 'certificates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'source_type',
        'source_id',
        'user_id',
        'template_id',
        'certificate_number',
        'verification_token_hash',
        'status',
        'issued_at',
        'expires_at',
        'issued_by',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
        'pdf_file_id',
        'replaced_by_certificate_id',
    ];

    /**
     * v4 source_type certificates → certificate_templates.source_type.
     */
    public const TEMPLATE_SOURCE_FOR_CERTIFICATE = [
        'learning_enrollment' => 'learning',
        'event_registration'  => 'event',
    ];

    /**
     * Daftar sertifikat untuk halaman index dashboard admin
     * (issued/revoked/expired — bukan pending, karena pending hanya
     * ada di antrean approvals).
     */
    /**
     * Daftar sertifikat untuk halaman index / riwayat dashboard admin.
     */
    public function listForAdmin(
        ?string $keyword = null,
        ?string $status = null,
        ?string $category = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $builder = $this->db->table('certificates c')
            ->select([
                'c.id',
                'c.certificate_number',
                'c.user_id',
                'c.template_id',
                'c.source_type',
                'c.source_id',
                'c.status',
                'c.issued_at',
                'c.expires_at',
                'c.revoked_at',
                'c.revocation_reason',
                'u.name      AS user_name',
                'u.email     AS user_email',
                't.name      AS template_name',
            ])
            ->join('users u',                 'u.id = c.user_id', 'left')
            ->join('certificate_templates t', 't.id = c.template_id', 'left')
            ->orderBy('c.id', 'DESC');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('c.certificate_number', $keyword)
                ->orLike('u.name', $keyword)
                ->orLike('u.email', $keyword)
                ->groupEnd();
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $builder->where('c.status', $status);
        }

        if ($category !== null && $category !== '' && $category !== 'all') {
            if ($category === 'learning') {
                $builder->where('c.source_type', 'learning_enrollment');
            } elseif ($category === 'event') {
                $builder->where('c.source_type', 'event_registration');
            }
        }

        if ($dateFrom !== null && $dateFrom !== '') {
            $builder->where('c.issued_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== null && $dateTo !== '') {
            $builder->where('c.issued_at <=', $dateTo . ' 23:59:59');
        }

        $rows = $builder->get()->getResultArray();

        // Tambahkan source_label per baris (learning_enrollment vs event_registration).
        foreach ($rows as &$row) {
            $row['source_label'] = $this->resolveSourceLabel(
                (string) $row['source_type'],
                (int)    $row['source_id']
            );
        }
        unset($row);

        return $rows;
    }

    /**
     * Ambil satu sertifikat dengan semua label siap pakai di view.
     */
    public function withFullDetail(int $id): ?array
    {
        $row = $this->db->table('certificates c')
            ->select([
                'c.*',
                'u.name      AS user_name',
                'u.email     AS user_email',
                't.name      AS template_name',
                'ru.name     AS revoked_by_name',
                'ib.name     AS issued_by_name',
            ])
            ->join('users u',                 'u.id = c.user_id', 'left')
            ->join('certificate_templates t', 't.id = c.template_id', 'left')
            ->join('users ru',                'ru.id = c.revoked_by', 'left')
            ->join('users ib',                'ib.id = c.issued_by',  'left')
            ->where('c.id', $id)
            ->get()
            ->getRowArray();

        if (! $row) {
            return null;
        }

        if (! empty($row['verification_token_hash'])) {
            $row['token_preview'] = strtoupper(substr((string) $row['verification_token_hash'], 0, 8));
        } else {
            $row['token_preview'] = null;
        }

        $source = $this->resolveSourceRow((string) $row['source_type'], (int) $row['source_id']);
        foreach ($source as $k => $v) {
            $row[$k] = $v;
        }
        $row['source_label'] = $source['source_label'] ?? '#' . (int) $row['source_id'];

        return $row;
    }

    /**
     * Lookup sertifikat by token (cleartext).
     * Hash sha256 → DB lookup → return null kalau tidak ada.
     * Dipakai CertificateVerificationController public.
     */
    public function findByClearToken(string $clearToken): ?array
    {
        $hash = hash('sha256', $clearToken);
        $row  = $this->db->table('certificates')
            ->where('verification_token_hash', $hash)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    // ===============================================================
    // Source resolution (single-source-type aware)
    // ===============================================================

    /**
     * Resolve label untuk index list (cukup nama course / event).
     */
    private function resolveSourceLabel(string $sourceType, int $sourceId): string
    {
        $r = $this->resolveSourceRow($sourceType, $sourceId);
        return $r['source_label'] ?? ('#' . $sourceId);
    }

    /**
     * Resolve satu source jadi struktur siap-view.
     */
    public function resolveSourceRow(string $sourceType, int $sourceId): array
    {
        if ($sourceType === 'learning_enrollment') {
            $row = $this->db->table('enrollments e')
                ->select([
                    'e.id',
                    'e.completed_at',
                    'e.final_score',
                    'lc.name AS class_name',
                    'lc.code AS class_code',
                    'lc.start_at AS class_start_date',
                    'lc.end_at AS class_end_date',
                    'co.title AS course_title',
                    'p.title AS program_title',
                    '(SELECT GROUP_CONCAT(u.name SEPARATOR ", ") FROM class_trainers ct JOIN users u ON u.id = ct.user_id WHERE ct.class_id = lc.id) AS trainers_names',
                ])
                ->join('learning_classes lc', 'lc.id = e.class_id',  'left')
                ->join('courses co',          'co.id = lc.course_id', 'left')
                ->join('programs p',          'p.id = co.program_id', 'left')
                ->where('e.id', $sourceId)
                ->get()
                ->getRowArray();

            if ($row) {
                return [
                    'class_name'       => $row['class_name']       ?? null,
                    'class_start_date' => $row['class_start_date'] ?? null,
                    'class_end_date'   => $row['class_end_date']   ?? null,
                    'course_title'     => $row['course_title']     ?? null,
                    'course_code'      => $row['course_code']      ?? null,
                    'program_title'    => $row['program_title']    ?? null,
                    'completed_at'     => $row['completed_at']     ?? null,
                    'final_score'      => $row['final_score']      ?? null,
                    'trainers_names'   => $row['trainers_names']   ?? null,
                    'source_label'     => $row['course_title']     ?? ($row['class_name'] ?? ('Enrollment #' . $sourceId)),
                ];
            }
            return [];
        }

        if ($sourceType === 'event_registration') {
            $row = $this->db->table('event_registrations er')
                ->select([
                    'er.id',
                    'ev.title AS event_title',
                    'ev.start_at AS event_start_at',
                    'ev.end_at AS event_end_at',
                    'ev.mode AS event_mode',
                    'ev.location AS event_location',
                    'a.status AS attendance_status',
                    'a.created_at AS attended_at',
                ])
                ->join('events ev',     'ev.id = er.event_id',              'left')
                ->join('attendances a', 'a.event_registration_id = er.id', 'left')
                ->where('er.id', $sourceId)
                ->get()
                ->getRowArray();

            if ($row) {
                return [
                    'event_title'       => $row['event_title']       ?? null,
                    'event_start_at'    => $row['event_start_at']    ?? null,
                    'event_end_at'      => $row['event_end_at']      ?? null,
                    'event_mode'        => $row['event_mode']        ?? null,
                    'event_location'    => $row['event_location']    ?? null,
                    'attendance_status' => $row['attendance_status'] ?? null,
                    'attended_at'       => $row['attended_at']       ?? null,
                    'source_label'      => $row['event_title']       ?? ('Event Registration #' . $sourceId),
                ];
            }
            return [];
        }

        return [];
    }
}
