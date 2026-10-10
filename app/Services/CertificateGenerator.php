<?php

namespace App\Services;

use App\Models\ApprovalModel;
use App\Models\Certificate\CertificateModel;
use App\Models\Certificate\CertificateSequenceModel;
use App\Models\Certificate\CertificateTemplateModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateGenerator — entry point tunggal penerbitan sertifikat.
 *
 * Driver lifecycle:
 *   1. Approval yang sudah berstatus 'approved' (resource_type=certificate,
 *      requested_action=issue_certificate) → generate certificate.
 *   2. TIDAK ADA controller lain yang insert certificates.
 *
 * Pipeline:
 *   load approval (approved)
 *      → resolve source (enrollment / event_registration)
 *      → idempotency check (UNIQUE source_type+source_id di certificates)
 *      → pilih template (certificate_templates.is_active=1)
 *      → generate certificate_number via certificate_sequences
 *      → generate verification token (random_bytes(32)) — DB simpan sha256
 *      → insert certificates (status='issued')
 *      → render HTML (CertificateTemplateRenderer)
 *      → inject QR PNG (CertificateQrGenerator)
 *      → render PDF (CertificatePdfGenerator)
 *      → persist file_assets (visibility=restricted, lifecycle=available,
 *                              resource_type=certificate)
 *      → update certificates.pdf_file_id
 *      → audit certificate.created
 *
 * Semua dalam 1 DB transaction. Rollback total bila ada exception.
 */
class CertificateGenerator
{
    public const APPROVAL_TYPE   = 'certificate';
    public const APPROVAL_ACTION = 'issue_certificate';

    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private CertificateSequenceModel $sequenceModel;
    private CertificateTemplateModel $templateModel;
    private ApprovalModel $approvalModel;
    private CertificateTemplateRenderer $templateRenderer;
    private CertificatePdfGenerator $pdfGenerator;
    private CertificateQrGenerator $qrGenerator;
    private AuditService $auditService;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->certificateModel  = new CertificateModel();
        $this->sequenceModel     = new CertificateSequenceModel();
        $this->templateModel     = new CertificateTemplateModel();
        $this->approvalModel     = new ApprovalModel();
        $this->templateRenderer  = new CertificateTemplateRenderer();
        $this->pdfGenerator      = new CertificatePdfGenerator();
        $this->qrGenerator       = new CertificateQrGenerator();
        $this->auditService      = new AuditService();
    }

    /**
     * Public entry. Mengembalikan struktur siap-dipakai controller.
     *
     * @return array{
     *     certificate_id: int,
     *     pdf_file_id: int|null,
     *     clear_token: string,
     *     certificate_number: string,
     *     source_type: string,
     *     source_id: int
     * }
     */
    public function generateFromApproval(int $approvalId, int $actorId): array
    {
        $approval = $this->approvalModel->find($approvalId);
        if (! $approval) {
            throw new RuntimeException('Approval tidak ditemukan.');
        }
        if (($approval['resource_type']    ?? '') !== self::APPROVAL_TYPE) {
            throw new RuntimeException('Tipe approval tidak sesuai.');
        }
        if (($approval['requested_action'] ?? '') !== self::APPROVAL_ACTION) {
            throw new RuntimeException('Aksi approval tidak sesuai.');
        }
        if (($approval['status'] ?? '') !== 'approved') {
            throw new RuntimeException('Approval belum berstatus approved.');
        }

        $source     = $this->resolveSource((int) $approval['resource_id']);
        $sourceType = (string) $source['source_type'];
        $sourceId   = (int)    $source['source_id'];
        $userId     = (int)    $source['user_id'];
        $issuerName = 'BegawiPBJ — Pemerintah Provinsi Lampung';
        $now        = date('Y-m-d H:i:s');

        // Idempotency — UNIQUE(source_type, source_id)
        $exists = $this->db->table('certificates')
            ->where('source_type', $sourceType)
            ->where('source_id',   $sourceId)
            ->countAllResults();
        if ($exists > 0) {
            throw new RuntimeException('Sertifikat untuk sumber ini sudah pernah diterbitkan.');
        }

        // Template selection
        $templateSource = CertificateModel::TEMPLATE_SOURCE_FOR_CERTIFICATE[$sourceType] ?? null;
        if (! $templateSource) {
            throw new RuntimeException('Tipe sumber sertifikat tidak dikenali.');
        }
        $template = $this->templateModel->findDefaultForSource($templateSource);
        if (! $template) {
            throw new RuntimeException(
                'Template sertifikat untuk source_type "' . $templateSource
                . '" belum tersedia atau belum diaktifkan.'
            );
        }

        // Number & token (pre-transaction: random + sequence atomik).
        $year       = (int) date('Y');
        $seq        = $this->sequenceModel->nextForYear($year);
        $prefix     = ($sourceType === 'learning_enrollment') ? 'LRN' : 'EVT';
        $certNumber = sprintf('BGW-%s-%d-%06d', $prefix, $year, $seq);
        $clearToken = bin2hex(random_bytes(32));
        $tokenHash  = hash('sha256', $clearToken);

        $this->db->transBegin();
        try {
            // Insert certificates
            $inserted = $this->db->table('certificates')->insert([
                'source_type'             => $sourceType,
                'source_id'               => $sourceId,
                'user_id'                 => $userId,
                'template_id'             => (int) $template['id'],
                'certificate_number'      => $certNumber,
                'verification_token_hash' => $tokenHash,
                'status'                  => 'issued',
                'issued_at'               => $now,
                'issued_by'               => $actorId,
            ], true);

            if (! $inserted) {
                throw new RuntimeException('Insert certificates gagal.');
            }
            $certificateId = (int) $this->db->insertID();
            if ($certificateId < 1) {
                throw new RuntimeException('Insert certificates tidak mengembalikan ID.');
            }

            // Build certificate data
            $issuedAtForRender = $now;
            $certificateData = [
                'certificate_id'    => $certificateId,
                'participant_name'  => (string) ($source['user_name'] ?? '—'),
                'certificate_number'=> $certNumber,
                'certificate_title' => (string) ($source['title'] ?? 'Sertifikat'),
                'source_label'      => (string) ($source['title'] ?? '—'),
                'issued_at'         => $issuedAtForRender,
                'issuer_name'       => $issuerName,
                'verification_url'  => $this->qrGenerator->buildVerifyUrl($clearToken),
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'user_id'           => $userId,
                'template_id'       => (int) $template['id'],
            ];

            // Render HTML template
            $html = $this->templateRenderer->render($certificateData, $template);

            // Build QR data URI and inject
            $qrDataUri = $this->qrGenerator->generate($clearToken);
            $qrImgTag  = '<img alt="QR verifikasi publik" src="' . htmlspecialchars($qrDataUri, ENT_QUOTES, 'UTF-8')
                . '" style="width:160px;height:160px;border:1px solid #e5e7eb;padding:4px;background:#fff;" />';
            $html = str_replace('<!--CERTIFICATE_QR_PLACEHOLDER-->', $qrImgTag, $html);

            // Untuk default layout yang menyimpan posisi 'right' tapi marker di body:
            $html = str_replace('__QR_BLOCK__', $qrImgTag, $html);

            // PDF render
            $pdfBytes = $this->pdfGenerator->generate($html);

            // Persist PDF as file_assets
            $pdfFileId = $this->persistPdfAsset(
                $certificateId,
                $certNumber,
                $pdfBytes,
                $actorId
            );

            // Update certificates.pdf_file_id
            $this->db->table('certificates')
                ->where('id', $certificateId)
                ->update([
                    'pdf_file_id' => $pdfFileId,
                    'updated_at'  => $now,
                ]);

            // Audit
            $this->auditService->log($actorId, 'certificate.created', 'certificate', 'certificate', $certificateId, [
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'certificate_number'=> $certNumber,
                'template_id'       => (int) $template['id'],
                'pdf_file_id'       => $pdfFileId,
            ]);
            $this->auditService->log($actorId, 'certificate.pdf_generated', 'certificate', 'certificate', $certificateId, [
                'pdf_file_id' => $pdfFileId,
                'size_bytes'  => strlen($pdfBytes),
            ]);

            $this->db->transCommit();

            // Notifikasi internal penerbitan sertifikat ke peserta
            try {
                (new NotificationService($this->db))->send(
                    (int) $source['user_id'],
                    'certificate_issued',
                    'Sertifikat Pembelajaran Diterbitkan',
                    "Sertifikat Anda untuk '" . ($source['title'] ?? 'Pembelajaran') . "' dengan nomor {$certNumber} telah berhasil diterbitkan dan siap diunduh.",
                    ['certificate_id' => $certificateId, 'certificate_number' => $certNumber]
                );
            } catch (\Throwable $e) {
                log_message('warning', 'Failed to send certificate notification: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        return [
            'certificate_id'    => $certificateId,
            'pdf_file_id'       => $pdfFileId ?? null,
            'clear_token'       => $clearToken,
            'certificate_number'=> $certNumber,
            'source_type'       => $sourceType,
            'source_id'         => $sourceId,
        ];
    }

    /**
     * Penerbitan langsung sertifikat (Learning completion / Event attendance).
     *
     * @param string $sourceType 'learning_enrollment' | 'event_registration'
     * @param int    $sourceId   enrollments.id | event_registrations.id
     * @param int    $actorId    user yang menerbitkan
     * @param array  $options    ['template_id' => int] optional
     *
     * @return array{
     *     certificate_id: int,
     *     pdf_file_id: int|null,
     *     clear_token: string,
     *     certificate_number: string,
     *     source_type: string,
     *     source_id: int,
     *     user_id: int
     * }
     */
    public function generateDirect(string $sourceType, int $sourceId, int $actorId, array $options = []): array
    {
        if (! in_array($sourceType, ['learning_enrollment', 'event_registration'], true)) {
            throw new RuntimeException('Tipe sumber sertifikat tidak valid: ' . $sourceType);
        }

        // Idempotency check: UNIQUE(source_type, source_id)
        $existing = $this->db->table('certificates')
            ->where('source_type', $sourceType)
            ->where('source_id',   $sourceId)
            ->get()
            ->getRowArray();

        if ($existing) {
            throw new RuntimeException('Sertifikat untuk kegiatan ini sudah pernah diterbitkan (No: ' . ($existing['certificate_number'] ?? '—') . ').');
        }

        $source     = $this->resolveSourceDirect($sourceType, $sourceId);
        $userId     = (int) $source['user_id'];
        $issuerName = 'BegawiPBJ — Pemerintah Provinsi Lampung';
        $now        = date('Y-m-d H:i:s');

        // Template resolution
        $template = null;
        if (! empty($options['template_id'])) {
            $template = $this->templateModel->find((int) $options['template_id']);
        }

        if (! $template) {
            $templateSource = CertificateModel::TEMPLATE_SOURCE_FOR_CERTIFICATE[$sourceType] ?? 'learning';
            $template       = $this->templateModel->findDefaultForSource($templateSource);
        }

        if (! $template) {
            throw new RuntimeException('Template sertifikat belum tersedia atau belum diaktifkan.');
        }

        // Sequence number & verification token
        $year       = (int) date('Y');
        $seq        = $this->sequenceModel->nextForYear($year);
        $prefix     = ($sourceType === 'learning_enrollment') ? 'LRN' : 'EVT';
        $certNumber = sprintf('BGW-%s-%d-%06d', $prefix, $year, $seq);
        $clearToken = bin2hex(random_bytes(32));
        $tokenHash  = hash('sha256', $clearToken);

        $this->db->transBegin();
        try {
            $inserted = $this->db->table('certificates')->insert([
                'source_type'             => $sourceType,
                'source_id'               => $sourceId,
                'user_id'                 => $userId,
                'template_id'             => (int) $template['id'],
                'certificate_number'      => $certNumber,
                'verification_token_hash' => $tokenHash,
                'status'                  => 'issued',
                'issued_at'               => $now,
                'issued_by'               => $actorId,
                'created_at'              => $now,
                'updated_at'              => $now,
            ], true);

            if (! $inserted) {
                $dbErr = $this->db->error();
                throw new RuntimeException('Gagal menyimpan sertifikat ke database: ' . ($dbErr['message'] ?? ''));
            }

            $certificateId = (int) $this->db->insertID();
            if ($certificateId < 1) {
                throw new RuntimeException('ID sertifikat tidak valid.');
            }

            // Build certificate data for renderer
            $certificateData = [
                'certificate_id'     => $certificateId,
                'participant_name'   => (string) ($source['user_name'] ?? 'Peserta'),
                'certificate_number' => $certNumber,
                'certificate_title'  => (string) ($source['title'] ?? 'Sertifikat'),
                'source_label'       => (string) ($source['title'] ?? '—'),
                'issued_at'          => $now,
                'issuer_name'        => $issuerName,
                'verification_url'   => $this->qrGenerator->buildVerifyUrl($clearToken),
                'source_type'        => $sourceType,
                'source_id'          => $sourceId,
                'user_id'            => $userId,
                'template_id'        => (int) $template['id'],
            ];

            // Render HTML template
            $html = $this->templateRenderer->render($certificateData, $template);

            // Generate & inject QR image
            $qrDataUri = $this->qrGenerator->generate($clearToken);
            $qrImgTag  = '<img alt="QR verifikasi publik" src="' . htmlspecialchars($qrDataUri, ENT_QUOTES, 'UTF-8')
                . '" style="width:160px;height:160px;border:1px solid #e5e7eb;padding:4px;background:#fff;" />';
            $html = str_replace('<!--CERTIFICATE_QR_PLACEHOLDER-->', $qrImgTag, $html);
            $html = str_replace('__QR_BLOCK__', $qrImgTag, $html);

            // PDF render
            $pdfBytes = $this->pdfGenerator->generate($html);

            // Persist PDF as file_assets
            $pdfFileId = $this->persistPdfAsset($certificateId, $certNumber, $pdfBytes, $actorId);

            // Update certificates.pdf_file_id
            $this->db->table('certificates')
                ->where('id', $certificateId)
                ->update([
                    'pdf_file_id' => $pdfFileId,
                    'updated_at'  => $now,
                ]);

            // Audit log
            $this->auditService->log($actorId, 'certificate.created', 'certificate', 'certificate', $certificateId, [
                'source_type'        => $sourceType,
                'source_id'          => $sourceId,
                'certificate_number' => $certNumber,
                'template_id'        => (int) $template['id'],
                'pdf_file_id'        => $pdfFileId,
            ]);
            $this->auditService->log($actorId, 'certificate.pdf_generated', 'certificate', 'certificate', $certificateId, [
                'pdf_file_id' => $pdfFileId,
                'size_bytes'  => strlen($pdfBytes),
            ]);

            $this->db->transCommit();

            // Kirim notifikasi ke peserta
            try {
                (new NotificationService($this->db))->send(
                    $userId,
                    'certificate_issued',
                    'Sertifikat Diterbitkan',
                    "Sertifikat Anda untuk '{$source['title']}' dengan nomor {$certNumber} telah berhasil diterbitkan dan siap diunduh.",
                    ['certificate_id' => $certificateId, 'certificate_number' => $certNumber]
                );
            } catch (\Throwable $e) {
                log_message('warning', 'Notification failed: ' . $e->getMessage());
            }

            return [
                'certificate_id'     => $certificateId,
                'pdf_file_id'        => $pdfFileId ?? null,
                'clear_token'        => $clearToken,
                'certificate_number' => $certNumber,
                'source_type'        => $sourceType,
                'source_id'          => $sourceId,
                'user_id'            => $userId,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Resolve data sumber langsung untuk penerbitan direct.
     */
    public function resolveSourceDirect(string $sourceType, int $sourceId): array
    {
        if ($sourceType === 'learning_enrollment') {
            $enrollment = $this->db->table('enrollments e')
                ->select([
                    'e.id',
                    'e.user_id',
                    'e.status',
                    'e.completed_at',
                    'lc.name AS class_name',
                    'co.title AS course_title',
                    'p.title AS program_title',
                ])
                ->join('learning_classes lc', 'lc.id = e.class_id',  'left')
                ->join('courses co',          'co.id = lc.course_id', 'left')
                ->join('programs p',          'p.id = co.program_id', 'left')
                ->where('e.id', $sourceId)
                ->get()
                ->getRowArray();

            if (! $enrollment) {
                throw new RuntimeException('Enrollment pembelajaran #' . $sourceId . ' tidak ditemukan.');
            }

            return [
                'source_type'   => 'learning_enrollment',
                'source_id'     => (int) $enrollment['id'],
                'user_id'       => (int) $enrollment['user_id'],
                'title'         => (string) ($enrollment['course_title'] ?? 'Sertifikat Pembelajaran'),
                'subtitle'      => (string) ($enrollment['class_name'] ?? ''),
                'program_title' => (string) ($enrollment['program_title'] ?? ''),
                'course_title'  => (string) ($enrollment['course_title'] ?? ''),
                'class_name'    => (string) ($enrollment['class_name'] ?? ''),
                'user_name'     => $this->lookupUserName((int) $enrollment['user_id']),
            ];
        }

        if ($sourceType === 'event_registration') {
            $registration = $this->db->table('event_registrations er')
                ->select([
                    'er.id',
                    'er.user_id',
                    'er.status',
                    'ev.title AS event_title',
                    'ev.start_at AS event_start_at',
                    'ev.end_at AS event_end_at',
                    'a.status AS attendance_status',
                ])
                ->join('events ev',     'ev.id = er.event_id', 'left')
                ->join('attendances a', 'a.event_registration_id = er.id', 'left')
                ->where('er.id', $sourceId)
                ->get()
                ->getRowArray();

            if (! $registration) {
                throw new RuntimeException('Registrasi event #' . $sourceId . ' tidak ditemukan.');
            }

            return [
                'source_type'       => 'event_registration',
                'source_id'         => (int) $registration['id'],
                'user_id'           => (int) $registration['user_id'],
                'title'             => (string) ($registration['event_title'] ?? 'Sertifikat Event'),
                'subtitle'          => '',
                'event_title'       => (string) ($registration['event_title'] ?? ''),
                'event_start_at'    => $registration['event_start_at'] ?? null,
                'event_end_at'      => $registration['event_end_at'] ?? null,
                'attendance_status' => $registration['attendance_status'] ?? null,
                'user_name'         => $this->lookupUserName((int) $registration['user_id']),
            ];
        }

        throw new RuntimeException('Tipe sumber sertifikat tidak dikenali: ' . $sourceType);
    }

    // ===============================================================
    // Source resolution
    // ===============================================================

    /**
     * Cari sumber (enrollment / event_registration) dari approval.resource_id.
     * Returns: source_type, source_id, user_id, title, user_name.
     */
    private function resolveSource(int $resourceId): array
    {
        // Learning enrollment
        $enrollment = $this->db->table('enrollments e')
            ->select(['e.id', 'e.user_id', 'lc.name AS class_name', 'co.title AS course_title'])
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses co',          'co.id = lc.course_id', 'left')
            ->where('e.id', $resourceId)
            ->get()
            ->getRowArray();
        if ($enrollment) {
            return [
                'source_type' => 'learning_enrollment',
                'source_id'   => (int) $enrollment['id'],
                'user_id'     => (int) $enrollment['user_id'],
                'title'       => (string) ($enrollment['course_title'] ?? 'Enrollment'),
                'subtitle'    => (string) ($enrollment['class_name'] ?? ''),
                'user_name'   => $this->lookupUserName((int) $enrollment['user_id']),
            ];
        }

        // Event registration
        $registration = $this->db->table('event_registrations er')
            ->select(['er.id', 'er.user_id', 'ev.title AS event_title'])
            ->join('events ev', 'ev.id = er.event_id', 'left')
            ->where('er.id', $resourceId)
            ->get()
            ->getRowArray();
        if ($registration) {
            return [
                'source_type' => 'event_registration',
                'source_id'   => (int) $registration['id'],
                'user_id'     => (int) $registration['user_id'],
                'title'       => (string) ($registration['event_title'] ?? 'Event Registration'),
                'subtitle'    => '',
                'user_name'   => $this->lookupUserName((int) $registration['user_id']),
            ];
        }

        throw new RuntimeException('Sumber sertifikat (enrollment / event_registration) tidak ditemukan.');
    }

    private function lookupUserName(int $userId): string
    {
        $row = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return $row['name'] ?? ('User #' . $userId);
    }

    // ===============================================================
    // PDF persistence
    // ===============================================================

    /**
     * Simpan file PDF ke writable/uploads/certificate/ dan catat file_assets.
     */
    private function persistPdfAsset(int $certificateId, string $certNumber, string $pdfBytes, int $uploaderId): int
    {
        $resourceType = 'certificate';
        $targetDir    = WRITEPATH . 'uploads/' . $resourceType;
        if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori upload sertifikat tidak dapat dibuat.');
        }

        $storedName   = 'cert_' . $certificateId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $relativePath = 'uploads/' . $resourceType . '/' . $storedName;
        $fullPath     = $targetDir . DIRECTORY_SEPARATOR . $storedName;

        if (file_put_contents($fullPath, $pdfBytes) === false) {
            throw new RuntimeException('Gagal menulis file PDF ke disk.');
        }

        $now          = date('Y-m-d H:i:s');
        $size         = (int) filesize($fullPath);
        $checksum     = @hash_file('sha256', $fullPath) ?: null;
        $originalName = 'Sertifikat ' . $certNumber . '.pdf';

        $inserted = $this->db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $originalName,
            'stored_name'      => $storedName,
            'extension'        => 'pdf',
            'mime_type'        => 'application/pdf',
            'size_bytes'       => $size,
            'checksum'         => $checksum,
            'visibility'       => 'restricted',
            'lifecycle_status' => 'available',
            'resource_type'    => $resourceType,
            'resource_id'      => $certificateId,
            'uploaded_by'      => $uploaderId,
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ], true);

        if (! $inserted) {
            @unlink($fullPath);
            throw new RuntimeException('Gagal mencatat file_assets untuk PDF sertifikat.');
        }

        return (int) $this->db->insertID();
    }
}
