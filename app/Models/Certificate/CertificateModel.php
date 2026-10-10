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
    public function listForAdmin(?string $keyword = null, ?string $status = null): array
    {
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
                'u.name      AS user_name',
                'u.email     AS user_email',
                't.name      AS template_name',
            ])
            ->join('users u',                 'u.id = c.user_id', 'left')
            ->join('certificate_templates t', 't.id = c.template_id', 'left')
            ->where('c.status', 'issued')
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
        $row['course_title']  = $source['course_title']  ?? null;
        $row['class_name']    = $source['class_name']    ?? null;
        $row['event_title']   = $source['event_title']   ?? null;
        $row['event_start_at']= $source['event_start_at']?? null;
        $row['event_end_at']  = $source['event_end_at']  ?? null;
        $row['source_label']  = $source['source_label']  ?? '#' . (int) $row['source_id'];

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
    private function resolveSourceRow(string $sourceType, int $sourceId): array
    {
        if ($sourceType === 'learning_enrollment') {
            $row = $this->db->table('enrollments e')
                ->select(['e.id', 'lc.name AS class_name', 'co.title AS course_title'])
                ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
                ->join('courses co',          'co.id = lc.course_id', 'left')
                ->where('e.id', $sourceId)
                ->get()
                ->getRowArray();
            if ($row) {
                return [
                    'class_name'   => $row['class_name']   ?? null,
                    'course_title' => $row['course_title'] ?? null,
                    'source_label' => $row['course_title'] ?? ($row['class_name'] ?? ('Enrollment #' . $sourceId)),
                ];
            }
            return [];
        }

        if ($sourceType === 'event_registration') {
            $row = $this->db->table('event_registrations er')
                ->select(['er.id', 'ev.title AS event_title', 'ev.start_at AS event_start_at', 'ev.end_at AS event_end_at'])
                ->join('events ev', 'ev.id = er.event_id', 'left')
                ->where('er.id', $sourceId)
                ->get()
                ->getRowArray();
            if ($row) {
                return [
                    'event_title'    => $row['event_title']    ?? null,
                    'event_start_at' => $row['event_start_at'] ?? null,
                    'event_end_at'   => $row['event_end_at']   ?? null,
                    'source_label'   => $row['event_title']    ?? ('Event Registration #' . $sourceId),
                ];
            }
            return [];
        }

        return [];
    }
}
