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

    /**
     * Peta singkatan kode tipe kegiatan event untuk penomoran sertifikat resmi.
     * Standar: SERT-{number}/{tipe}-{ID}/05/{YEAR}
     */
    public const EVENT_TYPE_CODE_MAP = [
        'webinar'     => 'WB',
        'bimtek'      => 'BT',
        'workshop'    => 'WS',
        'sosialisasi' => 'SO',
        'seminar'     => 'SM',
        'pelatihan'   => 'PL',
        'rakor'       => 'RK',
        'klinik'      => 'KL',
    ];

    /**
     * Format baku nomor sertifikat BegawiPengadaan:
     * SERT-{number}/{tipe}-{ID}/05/{YEAR}
     *
     * Contoh:
     *   - Arena Belajar (Class ID 5): SERT-00035/AB-5/05/2026
     *   - Event Umum (Event ID 10)  : SERT-05264/EV-10/05/2026
     *   - Webinar (Event ID 1)       : SERT-00021/WB-1/05/2026
     *   - Bimtek (Event ID 3)        : SERT-00042/BT-3/05/2026
     *
     * @param int                  $seq        Nomor urut tahunan
     * @param string               $sourceType 'learning_enrollment'|'event_registration'|'learning'|'event'
     * @param array<string, mixed> $sourceData Informasi source (class_id, event_id, event_type, dll.)
     * @param int|null             $year       Tahun penerbitan (default tahun berjalan)
     */
    public static function formatCertificateNumber(int $seq, string $sourceType, array $sourceData, ?int $year = null): string
    {
        $year         = $year ?: (int) date('Y');
        $seqFormatted = sprintf('%05d', $seq);

        $isLearning = in_array($sourceType, ['learning_enrollment', 'learning', 'arena_belajar'], true);

        if ($isLearning) {
            $tipeCode = 'AB';
            $targetId = (int) ($sourceData['class_id'] ?? $sourceData['learning_class_id'] ?? $sourceData['source_id'] ?? 1);
        } else {
            $rawType  = strtolower(trim((string) ($sourceData['event_type'] ?? $sourceData['type'] ?? '')));
            $tipeCode = self::EVENT_TYPE_CODE_MAP[$rawType] ?? 'EV';
            $targetId = (int) ($sourceData['event_id'] ?? $sourceData['source_id'] ?? 1);
        }

        if ($targetId < 1) {
            $targetId = 1;
        }

        return sprintf('SERT-%s/%s-%d/05/%d', $seqFormatted, $tipeCode, $targetId, $year);
    }

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
        $issuerName = 'BegawiPengadaan — Pemerintah Provinsi Lampung';
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
        $certNumber = self::formatCertificateNumber($seq, $sourceType, $source, $year);
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
            $certificateData = array_merge($source, [
                'certificate_id'     => $certificateId,
                'participant_name'   => (string) ($source['user_name'] ?? '—'),
                'certificate_number' => $certNumber,
                'certificate_title'  => (string) ($source['title'] ?? 'Sertifikat'),
                'source_label'       => (string) ($source['title'] ?? '—'),
                'issued_at'          => $issuedAtForRender,
                'issuer_name'        => $issuerName,
                'verification_url'   => $this->qrGenerator->buildVerifyUrl($clearToken),
                'source_type'        => $sourceType,
                'source_id'          => $sourceId,
                'user_id'            => $userId,
                'template_id'        => (int) $template['id'],
            ]);

            // Render HTML template
            $html = $this->templateRenderer->render($certificateData, $template);

            // Build QR data URI and inject
            $qrDataUri = $this->qrGenerator->generate($clearToken);
            $qrImgTag  = '<img alt="QR verifikasi publik" src="' . htmlspecialchars($qrDataUri, ENT_QUOTES, 'UTF-8')
                . '" style="width:140px;height:140px;border:1px solid #e2e8f0;padding:4px;background:#fff;" />';
            $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

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
                'certificate_number' => $certNumber,
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
            'certificate_number' => $certNumber,
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
        $issuerName = 'BegawiPengadaan — Pemerintah Provinsi Lampung';
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
        $certNumber = self::formatCertificateNumber($seq, $sourceType, $source, $year);
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
            $certificateData = array_merge($source, [
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
            ]);

            // Render HTML template
            $html = $this->templateRenderer->render($certificateData, $template);

            // Generate & inject QR image
            $qrDataUri = $this->qrGenerator->generate($clearToken);
            $qrImgTag  = '<img alt="QR verifikasi publik" src="' . htmlspecialchars($qrDataUri, ENT_QUOTES, 'UTF-8')
                . '" style="width:140px;height:140px;border:1px solid #e2e8f0;padding:4px;background:#fff;" />';
            $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

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
                    'e.class_id',
                    'e.user_id',
                    'e.status',
                    'e.final_score',
                    'e.completed_at',
                    'e.passed_at',
                    'e.enrolled_at',
                    'lc.name AS class_name',
                    'lc.course_id',
                    'co.title AS course_title',
                    'co.duration_minutes',
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

            $profile         = $this->lookupUserProfile((int) $enrollment['user_id']);
            $durationMinutes = (int) ($enrollment['duration_minutes'] ?? 0);
            $jpVal           = $durationMinutes > 0 ? max(1, (int) ceil($durationMinutes / 60)) : 24;
            $finalScore      = ! empty($enrollment['final_score']) ? (float) $enrollment['final_score'] : null;

            $predicate = 'Lulus';
            if ($finalScore !== null) {
                if ($finalScore >= 90) {
                    $predicate = 'Sangat Baik';
                } elseif ($finalScore >= 80) {
                    $predicate = 'Baik';
                } elseif ($finalScore >= 70) {
                    $predicate = 'Cukup';
                }
            }

            return array_merge($profile, [
                'source_type'        => 'learning_enrollment',
                'source_id'          => (int) $enrollment['id'],
                'class_id'           => (int) ($enrollment['class_id'] ?? 1),
                'learning_class_id'  => (int) ($enrollment['class_id'] ?? 1),
                'course_id'          => (int) ($enrollment['course_id'] ?? 1),
                'user_id'            => (int) $enrollment['user_id'],
                'title'              => (string) ($enrollment['course_title'] ?? 'Sertifikat Arena Belajar PBJ'),
                'subtitle'           => (string) ($enrollment['class_name'] ?? ''),
                'program_title'      => (string) ($enrollment['program_title'] ?? ''),
                'course_title'       => (string) ($enrollment['course_title'] ?? ''),
                'class_name'         => (string) ($enrollment['class_name'] ?? ''),
                'activity_type'      => 'Arena Belajar PBJ',
                'tipe_kegiatan'      => 'Arena Belajar',
                'kode_tipe'          => 'AB',
                'id_kegiatan'        => (int) ($enrollment['class_id'] ?? 1),
                'jam_pelajaran'      => $jpVal . ' JP',
                'jp'                 => $jpVal . ' JP',
                'duration_minutes'   => $durationMinutes,
                'final_score'        => $finalScore !== null ? number_format($finalScore, 1, ',', '.') : '—',
                'predicate'          => $predicate,
                'completed_at'       => $enrollment['completed_at'] ?? null,
                'passed_at'          => $enrollment['passed_at'] ?? null,
            ]);
        }

        if ($sourceType === 'event_registration') {
            $registration = $this->db->table('event_registrations er')
                ->select([
                    'er.id',
                    'er.event_id',
                    'er.user_id',
                    'er.status',
                    'ev.title AS event_title',
                    'ev.type AS event_type',
                    'ev.mode AS event_mode',
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

            $profile   = $this->lookupUserProfile((int) $registration['user_id']);
            $rawType   = strtolower(trim((string) ($registration['event_type'] ?? 'event')));
            $tipeCode  = self::EVENT_TYPE_CODE_MAP[$rawType] ?? 'EV';
            $typeLabel = match ($rawType) {
                'webinar'     => 'Webinar',
                'bimtek'      => 'Bimbingan Teknis (Bimtek)',
                'workshop'    => 'Workshop',
                'sosialisasi' => 'Sosialisasi Regulasi',
                'seminar'     => 'Seminar',
                'pelatihan'   => 'Pelatihan PBJ',
                'rakor'       => 'Rapat Koordinasi (Rakor)',
                'klinik'      => 'Klinik Pengadaan',
                default       => 'Event / Agenda PBJ',
            };

            return array_merge($profile, [
                'source_type'       => 'event_registration',
                'source_id'         => (int) $registration['id'],
                'event_id'          => (int) ($registration['event_id'] ?? 1),
                'user_id'           => (int) $registration['user_id'],
                'title'             => (string) ($registration['event_title'] ?? 'Sertifikat Kegiatan'),
                'subtitle'          => $typeLabel,
                'event_title'       => (string) ($registration['event_title'] ?? ''),
                'event_type'        => $rawType,
                'activity_type'     => $typeLabel,
                'tipe_kegiatan'     => $typeLabel,
                'kode_tipe'         => $tipeCode,
                'id_kegiatan'       => (int) ($registration['event_id'] ?? 1),
                'event_start_at'    => $registration['event_start_at'] ?? null,
                'event_end_at'      => $registration['event_end_at'] ?? null,
                'attendance_status' => $registration['attendance_status'] ?? null,
                'jam_pelajaran'     => '4 JP',
                'jp'                => '4 JP',
                'duration_minutes'  => 0,
                'final_score'       => '—',
                'predicate'         => 'Hadir Aktif',
            ]);
        }

        throw new RuntimeException('Tipe sumber sertifikat tidak dikenali: ' . $sourceType);
    }

    // ===============================================================
    // Source resolution
    // ===============================================================

    /**
     * Cari sumber (enrollment / event_registration) dari approval.resource_id.
     * Returns: source_type, source_id, user_id, title, user_name, dll.
     */
    private function resolveSource(int $resourceId): array
    {
        // Coba cari dari learning enrollments
        $enrollment = $this->db->table('enrollments e')
            ->select([
                'e.id',
                'e.class_id',
                'e.user_id',
                'e.final_score',
                'e.completed_at',
                'e.passed_at',
                'lc.name AS class_name',
                'co.title AS course_title',
                'co.duration_minutes',
            ])
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses co',          'co.id = lc.course_id', 'left')
            ->where('e.id', $resourceId)
            ->get()
            ->getRowArray();

        if ($enrollment) {
            $profile         = $this->lookupUserProfile((int) $enrollment['user_id']);
            $durationMinutes = (int) ($enrollment['duration_minutes'] ?? 0);
            $jpVal           = $durationMinutes > 0 ? max(1, (int) ceil($durationMinutes / 60)) : 24;
            $finalScore      = ! empty($enrollment['final_score']) ? (float) $enrollment['final_score'] : null;

            return array_merge($profile, [
                'source_type'        => 'learning_enrollment',
                'source_id'          => (int) $enrollment['id'],
                'class_id'           => (int) ($enrollment['class_id'] ?? 1),
                'learning_class_id'  => (int) ($enrollment['class_id'] ?? 1),
                'user_id'            => (int) $enrollment['user_id'],
                'title'              => (string) ($enrollment['course_title'] ?? 'Sertifikat Pembelajaran'),
                'subtitle'           => (string) ($enrollment['class_name'] ?? ''),
                'activity_type'      => 'Arena Belajar PBJ',
                'tipe_kegiatan'      => 'Arena Belajar',
                'kode_tipe'          => 'AB',
                'id_kegiatan'        => (int) ($enrollment['class_id'] ?? 1),
                'jam_pelajaran'      => $jpVal . ' JP',
                'jp'                 => $jpVal . ' JP',
                'final_score'        => $finalScore !== null ? number_format($finalScore, 1, ',', '.') : '—',
                'predicate'          => 'Lulus',
            ]);
        }

        // Coba cari dari event registration
        $registration = $this->db->table('event_registrations er')
            ->select([
                'er.id',
                'er.event_id',
                'er.user_id',
                'ev.title AS event_title',
                'ev.type AS event_type',
                'ev.start_at AS event_start_at',
                'ev.end_at AS event_end_at',
            ])
            ->join('events ev', 'ev.id = er.event_id', 'left')
            ->where('er.id', $resourceId)
            ->get()
            ->getRowArray();

        if ($registration) {
            $profile   = $this->lookupUserProfile((int) $registration['user_id']);
            $rawType   = strtolower(trim((string) ($registration['event_type'] ?? 'event')));
            $tipeCode  = self::EVENT_TYPE_CODE_MAP[$rawType] ?? 'EV';
            $typeLabel = match ($rawType) {
                'webinar'     => 'Webinar',
                'bimtek'      => 'Bimbingan Teknis (Bimtek)',
                'workshop'    => 'Workshop',
                'sosialisasi' => 'Sosialisasi Regulasi',
                'seminar'     => 'Seminar',
                'pelatihan'   => 'Pelatihan PBJ',
                'rakor'       => 'Rapat Koordinasi (Rakor)',
                'klinik'      => 'Klinik Pengadaan',
                default       => 'Event PBJ',
            };

            return array_merge($profile, [
                'source_type'       => 'event_registration',
                'source_id'         => (int) $registration['id'],
                'event_id'          => (int) ($registration['event_id'] ?? 1),
                'user_id'           => (int) $registration['user_id'],
                'title'             => (string) ($registration['event_title'] ?? 'Event Registration'),
                'subtitle'          => $typeLabel,
                'activity_type'     => $typeLabel,
                'tipe_kegiatan'     => $typeLabel,
                'kode_tipe'         => $tipeCode,
                'id_kegiatan'       => (int) ($registration['event_id'] ?? 1),
                'jam_pelajaran'     => '4 JP',
                'jp'                => '4 JP',
                'duration_minutes'  => 0,
                'final_score'       => '—',
                'predicate'         => 'Hadir Aktif',
            ]);
        }

        throw new RuntimeException('Sumber sertifikat (enrollment / event_registration) tidak ditemukan.');
    }

    /**
     * Look up profil pengguna (ASN profile / User) untuk kelengkapan identitas sertifikat.
     *
     * @return array<string, string>
     */
    private function lookupUserProfile(int $userId): array
    {
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray() ?: [];
        $asn  = $this->db->table('asn_profiles')->where('user_id', $userId)->get()->getRowArray() ?: [];

        return [
            'user_name'        => (string) ($user['name'] ?? ('Peserta #' . $userId)),
            'user_email'       => (string) ($user['email'] ?? ''),
            'user_phone'       => (string) ($user['phone'] ?? ''),
            'nip'              => (string) ($asn['employee_identifier'] ?? ''),
            'institution_name' => (string) ($asn['institution_name'] ?? 'Pemerintah Provinsi Lampung'),
            'unit_name'        => (string) ($asn['unit_name'] ?? ''),
            'position_name'    => (string) ($asn['position_name'] ?? ''),
            'rank_grade'       => (string) ($asn['rank_grade'] ?? ''),
        ];
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
