<?php

namespace App\Services;

use App\Models\ApprovalModel;
use App\Models\Certificate\CertificateModel;
use App\Models\Communication\EventAttendanceModel;
use App\Models\Communication\EventModel;
use App\Models\Communication\EventRegistrationModel;
use App\Models\EnrollmentModel;
use App\Models\Learning\LearningProgressModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;

/**
 * Eligibility detector untuk modul Certificate.
 *
 * Bertugas:
 *  - Memindai sumber sertifikat (enrollments / event_registrations) yang
 *    MEMENUHI SYARAT eligibility.
 *  - Menerjemahkan kelayakan menjadi APPROVAL (resource_type=certificate)
 *    dengan status pending. Approvals-lah yang menyalakan siklus, BUKAN
 *    baris certificates.
 *  - Menjamin idempoten: enrollment / registration yang sama untuk action
 *    yang sama dan status pending/approved TIDAK dibuat approval kedua.
 *
 * Sumber:
 *
 *   Learning  → learning_enrollment
 *     eligible jika learning_progress.progress_percent = 100
 *               dan learning_progress.status = completed
 *               (ATAU enrollments.status = completed)
 *
 *   Event     → event_registration
 *     eligible jika event.certificate_enabled = 1
 *               DAN event end_at < NOW()
 *               DAN event_registrations.status ∈ {registered}
 *               DAN attendances.status = present
 *
 * Approval yang dihasilkan:
 *   resource_type      = 'certificate'
 *   resource_id        = id enrollment / event_registration
 *   requested_action   = 'issue_certificate'
 *   status             = 'pending'
 *
 * Catatan:
 *  - Caller yang sudah menulis approval (mis. manual dari administrator) akan
 *    di-skip — eligibleFor...() menangani idempotency dengan
 *    ApprovalModel::hasActive().
 */
class CertificateEligibilityService
{
    public const SOURCE_LEARNING = 'learning_enrollment';
    public const SOURCE_EVENT    = 'event_registration';
    public const APPROVAL_ACTION = 'issue_certificate';
    public const APPROVAL_TYPE   = 'certificate';

    protected ConnectionInterface $db;
    protected EnrollmentModel $enrollmentModel;
    protected LearningProgressModel $progressModel;
    protected EventModel $eventModel;
    protected EventRegistrationModel $eventRegistrationModel;
    protected EventAttendanceModel $eventAttendanceModel;
    protected ApprovalModel $approvalModel;
    protected CertificateModel $certificateModel;
    protected AuditService $auditService;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->enrollmentModel      = new EnrollmentModel();
        $this->progressModel        = new LearningProgressModel();
        $this->eventModel           = new EventModel();
        $this->eventRegistrationModel = new EventRegistrationModel();
        $this->eventAttendanceModel = new EventAttendanceModel();
        $this->approvalModel        = new ApprovalModel();
        $this->certificateModel     = new CertificateModel();
        $this->auditService         = new AuditService();
    }

    // ===============================================================
    // Learning eligibility
    // ===============================================================

    /**
     * Pemindaian learning: cari enrollment yang eligible berdasarkan:
     *  - learning_progress.progress_percent = 100.00
     *    dan status = 'completed'
     *  ATAU enrollments.status = 'completed'
     *
     * Implementasi: dua query sederhana (satu per path) lalu di-merge dan
     * di-dedupe by enrollment.id — lebih tahan schema drift dibanding
     * satu query OR-besar dengan join kondisional.
     *
     * Idempoten terhadap approval 'issue_certificate' yang masih aktif
     * dan terhadap certificates row yang sudah ada.
     *
     * @return array<int, array<string, mixed>> daftar approval yang baru dibuat
     */
    public function scanLearningEligible(?int $requestedBy = null): array
    {
        // Hanya seleksi enrollment yang benar-benar memenuhi syarat kelulusan dan kelasnya mengaktifkan sertifikat
        $rows = $this->db->table('enrollments e')
            ->distinct()
            ->select([
                'e.id   AS enrollment_id',
                'e.user_id',
                'e.class_id',
                'e.status AS enrollment_status',
                'e.completed_at',
                'u.name  AS user_name',
                'u.email AS user_email',
                'lc.name AS class_name',
                'c.title AS course_title',
            ])
            ->join('learning_classes lc', 'lc.id = e.class_id',  'left')
            ->join('courses c',           'c.id = lc.course_id',  'left')
            ->join('users u',             'u.id = e.user_id',     'left')
            ->where('lc.certificate_enabled', 1)
            ->groupStart()
                ->where('e.certificate_eligible_at IS NOT NULL')
                ->orGroupStart()
                    ->whereIn('e.status', ['completed', 'passed'])
                    ->where('e.passed_at IS NOT NULL')
                    ->where('e.final_score >= lc.passing_grade', null, false)
                ->groupEnd()
            ->groupEnd()
            ->get()
            ->getResultArray();

        $byId = [];
        foreach ($rows as $r) {
            $byId[(int) $r['enrollment_id']] = $r;
        }
        $rows = array_values($byId);

        $created = [];
        foreach ($rows as $row) {
            $approvalId = $this->createApprovalIfMissing(
                self::SOURCE_LEARNING,
                (int) $row['enrollment_id'],
                $requestedBy,
                'Peserta telah menyelesaikan pembelajaran (course: '
                    . ($row['course_title'] ?? '—') . ', kelas: '
                    . ($row['class_name'] ?? '—') . ').'
            );
            if ($approvalId !== null) {
                $created[] = [
                    'approval_id' => $approvalId,
                    'source_type' => self::SOURCE_LEARNING,
                    'source_id'   => (int) $row['enrollment_id'],
                    'user_name'   => $row['user_name']  ?? null,
                    'user_email'  => $row['user_email'] ?? null,
                    'course'      => $row['course_title'] ?? null,
                    'class'       => $row['class_name']    ?? null,
                ];
            }
        }
        unset($rows);

        return $created;
    }

    /**
     * Buat SATU approval untuk enrollment tertentu, idempoten.
     * Return id approval yang baru dibuat, atau NULL jika sudah ada active.
     */
    public function createLearningApproval(int $enrollmentId, ?int $requestedBy = null): ?int
    {
        $row = $this->db->table('enrollments e')
            ->select([
                'e.id',
                'e.user_id',
                'e.status AS enrollment_status',
                'lc.name  AS class_name',
                'c.title  AS course_title',
            ])
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses c',           'c.id = lc.course_id', 'left')
            ->where('e.id', $enrollmentId)
            ->get()
            ->getRowArray();

        if (! $row) {
            throw new RuntimeException('Enrollment tidak ditemukan.');
        }

        $eligible = $this->isLearningEnrollmentEligible((int) $row['id']);
        if (! $eligible) {
            throw new RuntimeException('Enrollment belum memenuhi syarat kelayakan sertifikat.');
        }

        return $this->createApprovalIfMissing(
            self::SOURCE_LEARNING,
            $enrollmentId,
            $requestedBy,
            'Peserta telah menyelesaikan pembelajaran (course: '
                . ($row['course_title'] ?? '—') . ', kelas: '
                . ($row['class_name'] ?? '—') . ').'
        );
    }

    /**
     * Cek satu enrollment apakah eligible.
     */
    public function isLearningEnrollmentEligible(int $enrollmentId): bool
    {
        $row = $this->db->table('enrollments e')
            ->select('e.status, e.final_score, e.passed_at, e.certificate_eligible_at, lc.passing_grade, lc.certificate_enabled')
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->where('e.id', $enrollmentId)
            ->get()
            ->getRowArray();

        if (! $row) {
            return false;
        }

        // Kelas wajib mengaktifkan penerbitan sertifikat
        if ((int) ($row['certificate_enabled'] ?? 0) !== 1) {
            return false;
        }

        // Eligible jika sudah di-flag langsung oleh evaluasi kelulusan
        if (! empty($row['certificate_eligible_at'])) {
            return true;
        }

        // Atau status completed/passed dengan passed_at dan nilai akhir mencapai passing grade
        $passingGrade = (float) ($row['passing_grade'] ?? 70.0);
        $finalScore   = (float) ($row['final_score'] ?? 0);
        $isPassed     = in_array($row['status'], ['completed', 'passed'], true)
            && ! empty($row['passed_at'])
            && ($finalScore >= $passingGrade);

        return $isPassed;
    }

    // ===============================================================
    // Event eligibility
    // ===============================================================

    /**
     * Pemindaian event selesai: untuk TEPAT SATU event, scan peserta yang
     * attended=present dan ticket=registered, lalu buat approval untuk
     * masing-masing.
     *
     * Dipakai oleh CertificateEventController::generate($eventId).
     *
     * @return array<int, array<string, mixed>>
     */
    public function scanEventParticipantsEligible(int $eventId, ?int $requestedBy = null): array
    {
        $event = $this->eventModel->find($eventId);
        if (! $event) {
            throw new RuntimeException('Event tidak ditemukan.');
        }
        if ((int) ($event['certificate_enabled'] ?? 0) !== 1) {
            throw new RuntimeException('Event tidak mengaktifkan sertifikat.');
        }
        if (empty($event['end_at']) || strtotime((string) $event['end_at']) >= time()) {
            throw new RuntimeException('Event belum selesai (end_at belum lewat).');
        }

        $rows = $this->db->table('event_registrations er')
            ->select([
                'er.id          AS registration_id',
                'er.user_id',
                'er.status      AS registration_status',
                'a.status       AS attendance_status',
                'u.name         AS user_name',
                'u.email        AS user_email',
                'e.id           AS event_id',
                'e.title        AS event_title',
            ])
            ->join('events e',         'e.id = er.event_id',                 'left')
            ->join('attendances a',     'a.event_registration_id = er.id',    'left')
            ->join('users u',           'u.id = er.user_id',                  'left')
            ->where('er.event_id', $eventId)
            // eligible: registration teregistrasi & attendance present
            ->where('er.status', 'registered')
            ->where('a.status', 'present')
            ->orderBy('er.id', 'ASC')
            ->get()
            ->getResultArray();

        $created = [];
        foreach ($rows as $row) {
            $approvalId = $this->createApprovalIfMissing(
                self::SOURCE_EVENT,
                (int) $row['registration_id'],
                $requestedBy,
                'Peserta hadir pada event "'
                    . ($row['event_title'] ?? '—') . '".'
            );
            if ($approvalId !== null) {
                $created[] = [
                    'approval_id'    => $approvalId,
                    'source_type'    => self::SOURCE_EVENT,
                    'source_id'      => (int) $row['registration_id'],
                    'user_name'      => $row['user_name']  ?? null,
                    'user_email'     => $row['user_email'] ?? null,
                    'event_title'    => $row['event_title'] ?? null,
                ];
            }
        }

        return $created;
    }

    /**
     * Daftar event yang eligible untuk batch generate:
     *  certificate_enabled = 1 dan end_at < NOW().
     */
    public function listEligibleEvents(): array
    {
        $now = date('Y-m-d H:i:s');
        return $this->db->table('events e')
            ->select([
                'e.id',
                'e.title',
                'e.slug',
                'e.start_at',
                'e.end_at',
                'e.certificate_enabled',
                'e.status',
            ])
            ->where('e.certificate_enabled', 1)
            ->where('e.end_at <', $now)
            ->where('e.deleted_at IS NULL', null, false)
            ->orderBy('e.end_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ===============================================================
    // Helpers
    // ===============================================================

    /**
     * Idempoten: insert approvals jika belum ada active untuk
     * (resource_type, resource_id, requested_action). Return id baru,
     * atau NULL jika sudah ada.
     */
    private function createApprovalIfMissing(
        string $sourceType,
        int $sourceId,
        ?int $requestedBy,
        string $notes
    ): ?int {
        // Idempotency terhadap approvals aktif (pending/approved)
        if ($this->approvalModel->hasActive('certificate', $sourceId, self::APPROVAL_ACTION)) {
            return null;
        }

        // Idempotency terhadap certificates row yang sudah ada
        $exists = $this->db->table('certificates')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->countAllResults();
        if ($exists > 0) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        // Ensure requested_by references an existing user or is null (foreign key safe)
        $validActorId = null;
        if ($requestedBy) {
            $userExists = $this->db->table('users')->where('id', $requestedBy)->countAllResults();
            if ($userExists > 0) {
                $validActorId = $requestedBy;
            }
        }
        if ($validActorId === null) {
            $firstUser = $this->db->table('users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
            $validActorId = $firstUser ? (int) $firstUser['id'] : null;
        }

        $taggedNotes = "[source:{$sourceType}] " . $notes;

        $data = [
            'resource_type'    => 'certificate',
            'resource_id'      => $sourceId,
            'requested_action' => self::APPROVAL_ACTION,
            'status'           => 'pending',
            'requested_by'     => $validActorId,
            'notes'            => $taggedNotes,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];

        $ok = $this->db->table('approvals')->insert($data, true);
        if (! $ok) {
            throw new RuntimeException('Gagal membuat approval sertifikat.');
        }
        $approvalId = (int) $this->db->insertID();

        // Audit creation di luar transaction (tidak boleh menggagalkan insert)
        try {
            $this->auditService->log($requestedBy, 'approval.created', 'approval', 'approval', $approvalId, [
                'resource_type'    => 'certificate',
                'resource_id'      => $sourceId,
                'source_type'      => $sourceType,
                'requested_action' => self::APPROVAL_ACTION,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[CertificateEligibility] audit failed: ' . $e->getMessage());
        }

        return $approvalId;
    }
}
