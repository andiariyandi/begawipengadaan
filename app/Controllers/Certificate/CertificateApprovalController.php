<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\ApprovalModel;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use App\Services\CertificateEligibilityService;
use App\Services\CertificateGenerator;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * Certificate approval queue (dashboard back-office).
 *
 * Driver:
 *   Sumber sertifikat (learning / event) yang eligible tidak membuat
 *   baris certificates secara langsung — melainkan membuat baris
 *   approvals (resource_type='certificate', requested_action=
 *   'issue_certificate', status='pending'). Halaman ini adalah
 *   antrean yang ditampilkan ke admin/operator.
 *
 *   approve (issue)  → CertificateGenerator::generateFromApproval()
 *   reject          → ApprovalModel::markRejected() (no certificate)
 *
 *   $id pada URL adalah approvals.id, BUKAN certificates.id.
 */
class CertificateApprovalController extends BaseController
{
    public const APPROVAL_TYPE   = 'certificate';
    public const APPROVAL_ACTION = 'issue_certificate';

    private ConnectionInterface $db;
    private ApprovalModel $approvalModel;
    private CertificateModel $certificateModel;
    private CertificateGenerator $generator;
    private CertificateEligibilityService $eligibility;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db = db_connect();
        $this->approvalModel    = new ApprovalModel();
        $this->certificateModel = new CertificateModel();
        $this->generator        = new CertificateGenerator();
        $this->eligibility      = new CertificateEligibilityService();
        $this->auditService     = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/approval
     *
     * Saat halaman dibuka:
     *  1. Scan sumber learning yang eligible → buat baris approvals
     *     (idempoten via ApprovalModel::hasActive() + certificates check)
     *  2. Load approvals queue (status='pending', resource_type='certificate',
     *     requested_action='issue_certificate') dan render view.
     *
     * Sertifikat (certificates row) TIDAK dibuat di sini.
     */
    public function index()
    {
        // 1. Trigger eligibility scan agar queue terisi otomatis.
        try {
            $this->eligibility->scanLearningEligible(null);
        } catch (\Throwable $e) {
            log_message('warning', '[CertificateApproval] scanLearningEligible failed: ' . $e->getMessage());
        }

        // 2. Load antrean pending (sumber: tabel approvals)
        $rows = $this->db->table('approvals a')
            ->select([
                'a.id',
                'a.resource_type',
                'a.resource_id',
                'a.requested_action',
                'a.status',
                'a.requested_by',
                'a.notes',
                'a.created_at',
                'ru.name AS requested_by_name',
                'ru.email AS requested_by_email',
            ])
            ->join('users ru', 'ru.id = a.requested_by', 'left')
            ->where('a.resource_type', self::APPROVAL_TYPE)
            ->where('a.requested_action', self::APPROVAL_ACTION)
            ->where('a.status', 'pending')
            ->orderBy('a.id', 'DESC')
            ->get()
            ->getResultArray();

        // 3. Resolve label source per baris (course / event)
        $items = $this->resolveSources($rows);

        return view('dashboard/certificates/approvals/index', [
            'title'         => 'Approval Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Approval Sertifikat',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'approvals',
            'items'         => $items,
        ]);
    }

    /**
     * GET /dashboard/sertifikat/approval/{id}
     *
     * $id adalah approvals.id (BUKAN certificates.id).
     */
    public function detail(string $id)
    {
        $approval = $this->db->table('approvals a')
            ->select([
                'a.*',
                'ru.name  AS requested_by_name',
                'ru.email AS requested_by_email',
            ])
            ->join('users ru', 'ru.id = a.requested_by', 'left')
            ->where('a.id', (int) $id)
            ->get()
            ->getRowArray();

        if (! $approval) {
            return $this->notFound('Approval tidak ditemukan.');
        }
        if (($approval['resource_type'] ?? '') !== self::APPROVAL_TYPE) {
            return $this->notFound('Tipe approval tidak sesuai modul sertifikat.');
        }

        $source = $this->loadSourceContext((int) $approval['resource_id']);
        $hasCertificate = $this->db->table('certificates')
            ->where('source_type', (string) ($source['source_type'] ?? ''))
            ->where('source_id',   (int)    ($source['source_id']   ?? 0))
            ->countAllResults() > 0;

        return view('dashboard/certificates/approvals/detail', [
            'title'         => 'Detail Approval Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Approval Sertifikat',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'approvals',
            'approval'      => $approval,
            'approvalId'    => (int) $id,
            'source'        => $source,
            'hasCertificate' => $hasCertificate,
        ]);
    }

    /**
     * POST /dashboard/sertifikat/approval/{id}/terbitkan
     */
    public function issue(string $id)
    {
        try {
            $this->assertApproverAccess();
            $actorId = (int) session()->get('user_id');

            $approvalId = (int) $id;
            $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
            if (! $approval) {
                throw new RuntimeException('Approval tidak ditemukan.');
            }
            if (($approval['resource_type']    ?? '') !== self::APPROVAL_TYPE) {
                throw new RuntimeException('Tipe approval tidak sesuai.');
            }
            if (($approval['requested_action'] ?? '') !== self::APPROVAL_ACTION) {
                throw new RuntimeException('Aksi approval tidak sesuai.');
            }
            if (($approval['status'] ?? '') !== 'pending') {
                throw new RuntimeException('Hanya approval pending yang dapat diterbitkan.');
            }

            // Tandai approval approved dulu (idempotent), audit.
            $this->approvalModel->markApproved($approvalId, $actorId, 'Disetujui dan diteruskan ke CertificateGenerator.');
            $this->auditService->log($actorId, 'approval.approved', 'approval', 'approval', $approvalId, [
                'resource_type'    => self::APPROVAL_TYPE,
                'requested_action' => self::APPROVAL_ACTION,
            ]);

            // Generate certificate dari approval (transactional di service).
            $result = $this->generator->generateFromApproval($approvalId, $actorId);

            // Token clear hanya SEKALI tampil di flash (untuk admin simpan saat perlu).
            session()->setFlashdata('certificate_issued_token', [
                'id'    => (int) $result['certificate_id'],
                'token' => (string) $result['clear_token'],
                'number' => (string) $result['certificate_number'],
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/' . (int) $result['certificate_id']))
                ->with('success', 'Sertifikat ' . $result['certificate_number'] . ' berhasil diterbitkan.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * POST /dashboard/sertifikat/approval/{id}/tolak
     */
    public function reject(string $id)
    {
        try {
            $this->assertApproverAccess();

            $approvalId = (int) $id;
            $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
            if (! $approval) {
                throw new RuntimeException('Approval tidak ditemukan.');
            }
            if (($approval['resource_type'] ?? '') !== self::APPROVAL_TYPE) {
                throw new RuntimeException('Tipe approval tidak sesuai.');
            }
            if (($approval['status'] ?? '') === 'approved') {
                throw new RuntimeException('Approval sudah disetujui dan tidak dapat ditolak.');
            }
            if (($approval['status'] ?? '') === 'rejected') {
                throw new RuntimeException('Approval sudah ditolak.');
            }

            $reason = trim((string) $this->request->getPost('reason'));
            if ($reason === '' || mb_strlen($reason) < 5) {
                throw new RuntimeException('Alasan penolakan wajib diisi (minimal 5 karakter).');
            }
            if (mb_strlen($reason) > 1000) {
                throw new RuntimeException('Alasan penolakan maksimal 1000 karakter.');
            }

            $actorId = (int) session()->get('user_id');
            $this->approvalModel->markRejected($approvalId, $actorId, $reason);

            $this->auditService->log($actorId, 'approval.rejected', 'approval', 'approval', $approvalId, [
                'resource_type' => self::APPROVAL_TYPE,
                'reason'        => $reason,
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/approval'))
                ->with('success', 'Approval sertifikat ditolak. Tidak ada sertifikat yang diterbitkan.');
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ===============================================================
    // Helpers
    // ===============================================================

    /**
     * Resolve setiap baris approval ke label siap-view (course / event).
     * Menggunakan resource_id untuk lookup enrollment / event_registration.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function resolveSources(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }
        $enrollIds = [];
        $eventRegIds = [];
        foreach ($rows as $r) {
            $rid = (int) $r['resource_id'];
            if ($rid > 0) {
                // Kita belum tahu tipe sumber di sini (resource_type hanya "certificate").
                // Coba keduanya — yang match akan dipakai, yang null akan diabaikan.
                $enrollIds[$rid]  = true;
                $eventRegIds[$rid] = true;
            }
        }

        $enrollments = [];
        if (! empty($enrollIds)) {
            $enrollments = $this->db->table('enrollments e')
                ->select([
                    'e.id AS source_id',
                    'u.name AS user_name',
                    'u.email AS user_email',
                    'lc.name AS class_name',
                    'co.title AS course_title'
                ])
                ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
                ->join('courses co',          'co.id = lc.course_id', 'left')
                ->join('users u',             'u.id = e.user_id', 'left')
                ->whereIn('e.id', array_keys($enrollIds))
                ->get()
                ->getResultArray();
        }

        $eventRegs = [];
        if (! empty($eventRegIds)) {
            $eventRegs = $this->db->table('event_registrations er')
                ->select([
                    'er.id AS source_id',
                    'u.name AS user_name',
                    'u.email AS user_email',
                    'ev.title AS event_title',
                    'ev.start_at AS event_start_at',
                    'ev.end_at AS event_end_at'
                ])
                ->join('events ev', 'ev.id = er.event_id', 'left')
                ->join('users u',   'u.id = er.user_id', 'left')
                ->whereIn('er.id', array_keys($eventRegIds))
                ->get()
                ->getResultArray();
        }

        $bySource = [];
        foreach ($enrollments as $e) {
            $bySource['learning_enrollment'][(int) $e['source_id']] = ['type' => 'learning_enrollment', 'data' => $e];
        }
        foreach ($eventRegs    as $r) {
            $bySource['event_registration'][(int) $r['source_id']] = ['type' => 'event_registration',  'data' => $r];
        }

        $out = [];
        foreach ($rows as $row) {
            $rid = (int) $row['resource_id'];
            $matched = $bySource['learning_enrollment'][$rid]
                ?? $bySource['event_registration'][$rid]
                ?? null;

            $row['source_type']   = $matched['type'] ?? null;
            $row['user_name']     = $matched['data']['user_name']    ?? null;
            $row['user_email']    = $matched['data']['user_email']   ?? null;
            $row['course_title']  = $matched['data']['course_title'] ?? null;
            $row['class_name']    = $matched['data']['class_name']   ?? null;
            $row['event_title']   = $matched['data']['event_title']  ?? null;
            $row['event_start_at'] = $matched['data']['event_start_at'] ?? null;
            $row['event_end_at']  = $matched['data']['event_end_at']   ?? null;
            $row['source_label']  = $matched['data']['course_title']
                ?? $matched['data']['event_title']
                ?? ('Resource #' . $rid);
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Load row sumber (enrollment atau event_registration) dari approval.resource_id.
     * Digunakan untuk halaman detail approval.
     */
    private function loadSourceContext(int $resourceId): array
    {
        $enrollment = $this->db->table('enrollments e')
            ->select([
                'e.id AS source_id',
                'e.user_id',
                'e.class_id',
                'e.status AS enrollment_status',
                'e.completed_at',
                'u.name AS user_name',
                'u.email AS user_email',
                'lc.name AS class_name',
                'co.title AS course_title'
            ])
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses co',          'co.id = lc.course_id', 'left')
            ->join('users u',             'u.id = e.user_id', 'left')
            ->where('e.id', $resourceId)
            ->get()
            ->getRowArray();
        if ($enrollment) {
            $enrollment['source_type'] = 'learning_enrollment';
            $enrollment['title']       = $enrollment['course_title'] ?? null;
            return $enrollment;
        }

        $registration = $this->db->table('event_registrations er')
            ->select([
                'er.id AS source_id',
                'er.user_id',
                'er.event_id',
                'er.status AS registration_status',
                'u.name AS user_name',
                'u.email AS user_email',
                'ev.title AS event_title',
                'ev.start_at',
                'ev.end_at',
                'a.status AS attendance_status'
            ])
            ->join('events ev',     'ev.id = er.event_id',              'left')
            ->join('attendances a', 'a.event_registration_id = er.id',  'left')
            ->join('users u',       'u.id = er.user_id',                'left')
            ->where('er.id', $resourceId)
            ->get()
            ->getRowArray();
        if ($registration) {
            $registration['source_type'] = 'event_registration';
            $registration['title']       = $registration['event_title'] ?? null;
            return $registration;
        }

        return [
            'source_type' => null,
            'source_id'   => $resourceId,
            'user_name'   => '—',
        ];
    }

    /**
     * Permission check. certificate.approve via role_permissions.
     */
    private function assertApproverAccess(): void
    {
        $actorId = (int) session()->get('user_id');
        if (session()->get('isLoggedIn') !== true || $actorId < 1) {
            throw new RuntimeException('Akses ditolak.');
        }

        $actor = $this->db->table('users')->where('id', $actorId)->get()->getRowArray();
        if (! $actor || ($actor['account_status'] ?? '') !== 'active') {
            throw new RuntimeException('Akun tidak aktif.');
        }

        $hasPerm = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('role_permissions rp', 'rp.role_id = r.id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where([
                'ur.user_id' => $actorId,
                'ur.status'  => 'active',
                'p.name'     => 'certificate.approve',
            ])
            ->countAllResults() > 0;

        if (! $hasPerm) {
            throw new RuntimeException('Anda tidak memiliki permission certificate.approve.');
        }
    }

    private function notFound(string $msg)
    {
        return view('dashboard/errors/404', [
            'title'      => 'Approval tidak ditemukan',
            'pageTitle'  => 'Approval tidak ditemukan',
            'activeMenu' => 'certificates',
            'message'    => $msg,
        ]);
    }
}
