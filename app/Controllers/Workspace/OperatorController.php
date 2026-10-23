<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use CodeIgniter\Database\ConnectionInterface;

class OperatorController extends BaseController
{
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->db      = db_connect();
        $this->request = service('request');
    }

    private function clientIp(): string
    {
        $req = $this->request ?? service('request');
        if ($req instanceof \CodeIgniter\HTTP\IncomingRequest) {
            return $req->getIPAddress();
        }
        return '127.0.0.1';
    }

    private function clientUserAgent(): string
    {
        $req = $this->request ?? service('request');
        if ($req instanceof \CodeIgniter\HTTP\IncomingRequest) {
            return mb_substr((string) $req->getUserAgent(), 0, 255);
        }
        return 'CLI/BegawiPengadaan';
    }

    private function logAudit(int $actorId, string $action, string $resourceType, int $resourceId, array $snapshot = [], ?string $reason = null): void
    {
        $this->db->table('audit_logs')->insert([
            'actor_id'        => $actorId,
            'action'          => $action,
            'resource_type'   => $resourceType,
            'resource_id'     => $resourceId,
            'result'          => 'success',
            'before_snapshot' => null,
            'after_snapshot'  => ! empty($snapshot) ? json_encode($snapshot, JSON_UNESCAPED_SLASHES) : null,
            'reason'          => $reason,
            'ip_hash'         => hash('sha256', $this->clientIp()),
            'user_agent_hash' => hash('sha256', $this->clientUserAgent()),
            'request_id'      => null,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Pastikan pengguna memiliki hak akses Operator atau Administrator.
     * Mengikuti prinsip hierarki: administrator/super_administrator mewarisi semua hak operator.
     */
    private function assertOperatorAccess(): int
    {
        if (session()->get('isLoggedIn') !== true) {
            redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk terlebih dahulu.')->send();
            exit;
        }

        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            redirect()->to('/dashboard/masuk')->with('error', 'Sesi tidak valid. Silakan masuk ulang.')->send();
            exit;
        }

        $roles = (array) session()->get('roles');
        $allowed = ['operator', 'administrator', 'super_administrator'];
        $hasAccess = false;
        foreach ($allowed as $r) {
            if (in_array($r, $roles, true)) {
                $hasAccess = true;
                break;
            }
        }

        if (! $hasAccess) {
            redirect()->to('/workspace')->with('error', 'Akses ditolak. Halaman khusus Operator / Administrator.')->send();
            exit;
        }

        return $userId;
    }

    /**
     * 1. BERANDA / OPERATIONAL DASHBOARD
     * Pusat operasional harian: 7 KPI stats dan 4 ringkasan feed aktivitas.
     */
    public function index()
    {
        $this->assertOperatorAccess();

        $pendingVerif = $this->db->table('profile_verifications')->where('status', 'pending')->countAllResults();
        $pendingEnroll = $this->db->table('enrollments')->where('status', 'pending')->countAllResults();
        $pendingApprov = $this->db->table('approvals')->where('status', 'pending')->countAllResults();
        $activeEvents  = $this->db->table('events')->whereIn('status', ['open_registration', 'coming_soon', 'published', 'ongoing'])->countAllResults();
        $activeClasses = $this->db->table('learning_classes')->whereIn('status', ['active', 'scheduled', 'ongoing', 'published', 'open'])->countAllResults();

        $activeTrainers = $this->db->table('class_trainers')
            ->select('user_id')
            ->where('status', 'active')
            ->distinct()
            ->countAllResults();
        if ($activeTrainers === 0) {
            $activeTrainers = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'trainer')
                ->where('ur.status', 'active')
                ->countAllResults();
        }

        $attendedRows = $this->db->table('attendances')->select('event_registration_id')->get()->getResultArray();
        $attendedIds  = array_filter(array_column($attendedRows, 'event_registration_id'));
        $attBuilder   = $this->db->table('event_registrations')->where('status', 'registered');
        if (! empty($attendedIds)) {
            $attBuilder->whereNotIn('id', $attendedIds);
        }
        $pendingAttendance = $attBuilder->countAllResults();

        $issuedCertRows  = $this->db->table('certificates')
            ->select('source_id')
            ->where('source_type', 'learning_enrollment')
            ->where('status', 'issued')
            ->get()->getResultArray();
        $issuedSourceIds = array_filter(array_column($issuedCertRows, 'source_id'));
        $certBuilder     = $this->db->table('enrollments')->where('certificate_eligible_at IS NOT NULL');
        if (! empty($issuedSourceIds)) {
            $certBuilder->whereNotIn('id', $issuedSourceIds);
        }
        $pendingCertificates = $certBuilder->countAllResults();

        // 4 Feeds Aktivitas Terkini (Mendukung client-side pagination)
        $recentApprovals = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.created_at, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        $recentAssignments = $this->db->table('class_trainers ct')
            ->select('ct.class_id, ct.user_id, ct.status, ct.assigned_at, u.name AS trainer_name, lc.name AS class_name, lc.code AS class_code')
            ->join('users u', 'u.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->orderBy('ct.assigned_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        $recentEvents = $this->db->table('events')
            ->select('id, title, type, status, start_at')
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        $recentOverrides = $this->db->table('audit_logs a')
            ->select('a.id, a.action, a.resource_id, a.created_at, u.name AS actor_name')
            ->join('users u', 'u.id = a.actor_id', 'left')
            ->where('a.action LIKE', '%override%')
            ->orderBy('a.created_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/home', [
            'title'             => 'Dashboard Operasional | Operator BegawiPengadaan',
            'pageTitle'         => 'Dashboard Operasional',
            'breadcrumbTail'    => [['label' => 'Dashboard Operasional']],
            'userName'          => $userName,
            'roles'             => ['operator'],
            'primaryRole'       => 'operator',
            'roleLabel'         => 'Operator',
            'stats'             => [
                'pendingApproval'     => $pendingApprov,
                'userPendingVerif'    => $pendingVerif,
                'activeClasses'       => $activeClasses,
                'activeTrainers'      => $activeTrainers,
                'ongoingEvents'       => $activeEvents,
                'pendingAttendance'   => $pendingAttendance,
                'pendingCertificates' => $pendingCertificates,
            ],
            'recentApprovals'   => $recentApprovals,
            'recentAssignments' => $recentAssignments,
            'recentEvents'      => $recentEvents,
            'recentOverrides'   => $recentOverrides,
        ]);
    }

    /**
     * 2. APPROVAL LIST
     */
    public function approvals()
    {
        $this->assertOperatorAccess();

        $pending  = $this->db->table('approvals')->where('status', 'pending')->countAllResults();
        $approved = $this->db->table('approvals')->where('status', 'approved')->countAllResults();
        $rejected = $this->db->table('approvals')->where('status', 'rejected')->countAllResults();

        $list = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.reason, a.created_at, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'approved' => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected' => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                default    => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $isPending = ($item['status'] === 'pending');
            $chkHtml = $isPending
                ? '<input type="checkbox" name="selected_ids[]" value="' . $item['id'] . '" class="form-check-input ws-bulk-check">'
                : '<span class="text-muted">&ndash;</span>';

            $actionBtn = '-';
            if ($isPending) {
                $approveUrl = route_to('workspace.operator.approvals.approve', $item['id']);
                $rejectUrl  = route_to('workspace.operator.approvals.reject', $item['id']);
                $csrf       = csrf_field();
                $actionBtn  = '<div class="d-flex gap-1">' .
                    '<form action="' . $approveUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-success py-0 px-2">Setujui</button></form>' .
                    '<form action="' . $rejectUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="reason" value="Approval ditolak oleh Operator."><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Tolak approval ini?\')">Tolak</button></form>' .
                    '</div>';
            }

            $rows[] = [
                $chkHtml,
                '#' . $item['id'],
                '<span class="badge bg-light text-dark">' . esc($item['resource_type'] ?? '-') . '</span>',
                '<strong>' . esc($item['requested_action'] ?? '-') . '</strong>',
                esc($item['requester_name'] ?? 'Sistem'),
                $badge,
                esc(date('d/m/Y H:i', strtotime($item['created_at']))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Antrean Persetujuan',
            'title'       => 'Antrean Approval',
            'description' => 'Persetujuan operasional sertifikat, verifikasi profil, dan perubahan hak akses.',
            'bulkConfig'  => [
                'url'        => route_to('workspace.operator.approvals.bulk'),
                'entityName' => 'Pengajuan Approval',
                'actions'    => [
                    ['action' => 'approve', 'label' => 'Setujui Terpilih', 'class' => 'btn-success', 'icon' => 'fas fa-check-double'],
                    ['action' => 'reject', 'label' => 'Tolak Terpilih', 'class' => 'btn-outline-danger', 'icon' => 'fas fa-times'],
                ],
            ],
            'cards'       => [
                ['title' => 'Menunggu Approval', 'value' => sprintf('%02d', $pending), 'meta' => 'perlu tindakan'],
                ['title' => 'Telah Disetujui', 'value' => sprintf('%02d', $approved), 'meta' => 'disetujui'],
                ['title' => 'Ditolak', 'value' => sprintf('%02d', $rejected), 'meta' => 'ditolak'],
            ],
            'tableTitle'  => 'Daftar Pengajuan Approval Sistem',
            'table'       => [
                'headers' => ['<input type="checkbox" id="wsBulkSelectAll" class="form-check-input" title="Pilih Semua">', 'ID', 'Tipe Objek', 'Tindakan', 'Diajukan Oleh', 'Status', 'Waktu Pengajuan', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Buka Verifikasi Sertifikat Pembelajaran', 'url' => route_to('workspace.operator.certificates'), 'btn' => 'Buka Sertifikat'],
                ['label' => 'Buka Permohonan Perubahan Profil Pengguna', 'url' => route_to('workspace.operator.profile-change-requests'), 'btn' => 'Buka Perubahan Profil'],
            ],
        ];

        return $this->renderOperasional('Antrean Approval', $sectionContent);
    }

    /**
     * TINDAKAN MASSAL (BULK ACTION): APPROVAL
     */
    public function bulkApproval()
    {
        $actorId = $this->assertOperatorAccess();
        $action  = (string) $this->request->getPost('bulk_action');
        $rawIds  = (array) $this->request->getPost('selected_ids');
        $notes   = trim((string) $this->request->getPost('notes')) ?: ($action === 'approve' ? 'Disetujui massal oleh Operator.' : 'Ditolak massal oleh Operator.');

        $ids = array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0);
        if ($ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data approval yang dipilih.');
        }

        if (! in_array($action, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Tindakan massal tidak valid.');
        }

        $now = date('Y-m-d H:i:s');
        $status = $action === 'approve' ? 'approved' : 'rejected';

        $approvals = $this->db->table('approvals')->whereIn('id', $ids)->get()->getResultArray();
        $successCount = 0;
        $certCount = 0;

        foreach ($approvals as $app) {
            $appId = (int) $app['id'];
            $updateData = [
                'status'      => $status,
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'updated_at'  => $now,
            ];
            if ($action === 'approve') {
                $updateData['notes'] = $notes;
            } else {
                $updateData['reason'] = $notes;
            }

            $this->db->table('approvals')->where('id', $appId)->update($updateData);
            $successCount++;

            if ($action === 'approve' && ($app['resource_type'] ?? '') === 'certificate' && ($app['requested_action'] ?? '') === 'issue_certificate') {
                try {
                    $generator = new \App\Services\CertificateGenerator();
                    $generator->generateFromApproval($appId, $actorId);
                    $certCount++;
                } catch (\Throwable $e) {
                    log_message('error', 'Bulk approval certificate failed for ID ' . $appId . ': ' . $e->getMessage());
                }
            }
        }

        $msg = "Berhasil memproses {$successCount} pengajuan approval ({$status}).";
        if ($certCount > 0) {
            $msg .= " Serta berhasil menerbitkan {$certCount} sertifikat secara otomatis.";
        }

        return redirect()->back()->with('message', $msg);
    }

    public function approveApproval(string $id)
    {
        $actorId    = $this->assertOperatorAccess();
        $approvalId = (int) $id;
        $notes      = (string) $this->request->getPost('notes') ?: 'Disetujui oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('approvals')->where('id', $approvalId)->update([
            'status'      => 'approved',
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'notes'       => $notes,
            'updated_at'  => $now,
        ]);

        if (($approval['resource_type'] ?? '') === 'certificate' && ($approval['requested_action'] ?? '') === 'issue_certificate') {
            try {
                $generator = new \App\Services\CertificateGenerator();
                $result = $generator->generateFromApproval($approvalId, $actorId);
                return redirect()->back()->with('message', 'Approval disetujui dan Sertifikat #' . ($result['certificate_number'] ?? '') . ' berhasil diterbitkan.');
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Approval disetujui, namun pembuatan berkas sertifikat mengalami kendala: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' berhasil disetujui.');
    }

    public function rejectApproval(string $id)
    {
        $actorId    = $this->assertOperatorAccess();
        $approvalId = (int) $id;
        $reason     = trim((string) $this->request->getPost('reason')) ?: 'Pengajuan ditolak oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('approvals')->where('id', $approvalId)->update([
            'status'      => 'rejected',
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'reason'      => $reason,
            'updated_at'  => $now,
        ]);

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' telah ditolak.');
    }

    /**
     * 3. VERIFIKASI USER / PROFIL
     */
    public function profileVerifications()
    {
        $this->assertOperatorAccess();

        $pending  = $this->db->table('profile_verifications')->where('status', 'pending')->countAllResults();
        $approved = $this->db->table('profile_verifications')->where('status', 'approved')->countAllResults();
        $rejected = $this->db->table('profile_verifications')->where('status', 'rejected')->countAllResults();

        $list = $this->db->table('profile_verifications pv')
            ->select('pv.id, pv.verification_type, pv.status, pv.created_at, u.name, u.email')
            ->join('users u', 'u.id = pv.user_id')
            ->orderBy('pv.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'approved' => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected' => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                default    => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $isPending = ($item['status'] === 'pending');
            $chkHtml = $isPending
                ? '<input type="checkbox" name="selected_ids[]" value="' . $item['id'] . '" class="form-check-input ws-bulk-check">'
                : '<span class="text-muted">&ndash;</span>';

            $actionBtn = '-';
            if ($isPending) {
                $approveUrl = route_to('workspace.operator.profile-verifications.approve', $item['id']);
                $rejectUrl  = route_to('workspace.operator.profile-verifications.reject', $item['id']);
                $csrf       = csrf_field();
                $actionBtn  = '<div class="d-flex gap-1">' .
                    '<form action="' . $approveUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-success py-0 px-2">Setujui</button></form>' .
                    '<form action="' . $rejectUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="reason" value="Data profil ditolak oleh Operator."><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Tolak verifikasi ini?\')">Tolak</button></form>' .
                    '</div>';
            }

            $rows[] = [
                $chkHtml,
                '#' . $item['id'],
                '<strong>' . esc($item['name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['email'] ?? '') . '</small>',
                '<span class="badge bg-light text-dark">' . esc($item['verification_type'] ?? 'umum') . '</span>',
                $badge,
                esc(date('d/m/Y H:i', strtotime($item['created_at']))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Akses',
            'title'       => 'Verifikasi User',
            'description' => 'Daftar pengajuan verifikasi identitas dan kesiapan akun pengguna sistem.',
            'bulkConfig'  => [
                'url'        => route_to('workspace.operator.profile-verifications.bulk'),
                'entityName' => 'Verifikasi Profil',
                'actions'    => [
                    ['action' => 'approve', 'label' => 'Setujui Terpilih', 'class' => 'btn-success', 'icon' => 'fas fa-check-double'],
                    ['action' => 'reject', 'label' => 'Tolak Terpilih', 'class' => 'btn-outline-danger', 'icon' => 'fas fa-times'],
                ],
            ],
            'cards'       => [
                ['title' => 'Menunggu Verifikasi', 'value' => sprintf('%02d', $pending), 'meta' => 'pengajuan aktif'],
                ['title' => 'Telah Disetujui', 'value' => sprintf('%02d', $approved), 'meta' => 'terverifikasi'],
                ['title' => 'Ditolak / Revisi', 'value' => sprintf('%02d', $rejected), 'meta' => 'membutuhkan perbaikan'],
            ],
            'tableTitle'  => 'Daftar Pengajuan Verifikasi Profil Pengguna',
            'table'       => [
                'headers' => ['<input type="checkbox" id="wsBulkSelectAll" class="form-check-input" title="Pilih Semua">', 'ID', 'Nama / Email', 'Tipe Verifikasi', 'Status', 'Tanggal Pengajuan', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Kelola Seluruh Profil Pengguna di Dashboard Akses', 'url' => site_url('dashboard/akses/verifikasi-profil'), 'btn' => 'Buka Admin'],
            ],
        ];

        return $this->renderOperasional('Verifikasi User', $sectionContent);
    }

    /**
     * TINDAKAN MASSAL (BULK ACTION): VERIFIKASI PROFIL
     */
    public function bulkProfileVerification()
    {
        $actorId = $this->assertOperatorAccess();
        $action  = (string) $this->request->getPost('bulk_action');
        $rawIds  = (array) $this->request->getPost('selected_ids');
        $notes   = trim((string) $this->request->getPost('notes')) ?: ($action === 'approve' ? 'Diverifikasi massal oleh Operator.' : 'Ditolak massal oleh Operator.');

        $ids = array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0);
        if ($ids === []) {
            return redirect()->back()->with('error', 'Tidak ada verifikasi profil yang dipilih.');
        }

        if (! in_array($action, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Tindakan massal tidak valid.');
        }

        $service = new \App\Services\Admin\ProfileVerificationService();
        $successCount = 0;

        foreach ($ids as $vid) {
            try {
                if ($action === 'approve') {
                    if ($service->approve($vid, $actorId, $notes)) {
                        $successCount++;
                    }
                } else {
                    if ($service->reject($vid, $notes, $actorId, $notes)) {
                        $successCount++;
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', "Bulk profile verification failed for ID {$vid}: " . $e->getMessage());
            }
        }

        $statusLabel = ($action === 'approve' ? 'disetujui' : 'ditolak');
        return redirect()->back()->with('message', "Berhasil memproses {$successCount} verifikasi profil ({$statusLabel}).");
    }

    public function approveVerification(string $id)
    {
        $actorId        = $this->assertOperatorAccess();
        $verificationId = (int) $id;
        $notes          = (string) $this->request->getPost('notes');

        $service = new \App\Services\Admin\ProfileVerificationService();
        if ($service->approve($verificationId, $actorId, $notes)) {
            return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' berhasil disetujui.');
        }
        return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
    }

    public function rejectVerification(string $id)
    {
        $actorId        = $this->assertOperatorAccess();
        $verificationId = (int) $id;
        $reason         = trim((string) $this->request->getPost('reason')) ?: 'Data profil tidak sesuai.';
        $notes          = (string) $this->request->getPost('notes');

        $service = new \App\Services\Admin\ProfileVerificationService();
        try {
            if ($service->reject($verificationId, $reason, $actorId, $notes)) {
                return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' telah ditolak.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
    }

    /**
     * PERMOHONAN PERUBAHAN PROFIL
     * Verifikasi perubahan data profil mandiri pengguna melalui workspace/dashboard.
     */
    public function profileChangeRequests()
    {
        $this->assertOperatorAccess();

        $pending  = $this->db->table('profile_change_requests')->whereIn('status', ['submitted', 'in_review'])->countAllResults();
        $approved = $this->db->table('profile_change_requests')->where('status', 'approved')->countAllResults();
        $rejected = $this->db->table('profile_change_requests')->whereIn('status', ['rejected', 'revision_required'])->countAllResults();

        $list = $this->db->table('profile_change_requests pcr')
            ->select('pcr.*, u.name AS user_name, u.email, u.profile_type')
            ->join('users u', 'u.id = pcr.user_id', 'left')
            ->orderBy('pcr.created_at', 'DESC')
            ->limit(35)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'approved'          => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected'          => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                'revision_required' => '<span class="badge bg-warning text-dark">Perlu Revisi</span>',
                default             => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $detailUrl  = route_to('workspace.operator.profile-change-requests.detail', $item['id']);
            $approveUrl = route_to('workspace.operator.profile-change-requests.approve', $item['id']);
            $rejectUrl  = route_to('workspace.operator.profile-change-requests.reject', $item['id']);
            $csrf       = csrf_field();

            $actionBtn = '<div class="d-flex align-items-center gap-1">' .
                '<a href="' . $detailUrl . '" class="btn btn-sm btn-outline-primary py-0 px-2" title="Tinjau Detail"><i class="fas fa-eye me-1"></i>Review</a>';

            if (in_array($item['status'], ['submitted', 'in_review'], true)) {
                $actionBtn .= '<form action="' . $approveUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-success py-0 px-2" onclick="return confirm(\'Setujui dan terapkan perubahan profil ini?\')">Setujui</button></form>' .
                    '<form action="' . $rejectUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="reason" value="Perubahan profil tidak memenuhi ketentuan."><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Tolak permohonan ini?\')">Tolak</button></form>';
            }

            $actionBtn .= '</div>';

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['user_name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['email'] ?? '') . '</small>',
                '<span class="badge bg-light text-dark">' . esc(strtoupper($item['profile_type'] ?? 'UMUM')) . '</span>',
                esc(mb_strimwidth($item['reason'] ?: 'Pengajuan mandiri', 0, 40, '...')),
                $badge,
                esc(date('d/m/Y H:i', strtotime($item['created_at']))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Akses',
            'title'       => 'Permohonan Perubahan Profil',
            'description' => 'Verifikasi dan persetujuan perubahan data profil pengguna sistem sebelum diterapkan.',
            'cards'       => [
                ['title' => 'Menunggu Persetujuan', 'value' => sprintf('%02d', $pending), 'meta' => 'perlu ditelaah'],
                ['title' => 'Telah Disetujui', 'value' => sprintf('%02d', $approved), 'meta' => 'diterapkan resmi'],
                ['title' => 'Ditolak / Revisi', 'value' => sprintf('%02d', $rejected), 'meta' => 'ditolak atau revisi'],
            ],
            'tableTitle'  => 'Daftar Pengajuan Perubahan Data Profil Pengguna',
            'table'       => [
                'headers' => ['ID', 'Nama / Email', 'Tipe Profil', 'Alasan Pengajuan', 'Status', 'Tanggal Pengajuan', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Buka Modul Perubahan Profil di Dashboard WowDash Admin', 'url' => site_url('dashboard/akses/permohonan-perubahan-profil'), 'btn' => 'Buka Admin'],
            ],
        ];

        return $this->renderOperasional('Permohonan Perubahan Profil', $sectionContent);
    }

    public function profileChangeRequestDetail(string $id)
    {
        $actorId   = $this->assertOperatorAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;

        $request = $this->db->table('profile_change_requests pcr')
            ->select('pcr.*, u.name AS user_name, u.email, u.username, u.profile_type')
            ->join('users u', 'u.id = pcr.user_id', 'left')
            ->where('pcr.id', $requestId)
            ->get()->getRowArray();

        if (! $request) {
            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('error', 'Permohonan perubahan tidak ditemukan.');
        }

        $payload = json_decode((string) ($request['requested_payload'] ?? '{}'), true) ?: [];
        $typeTable = match ($request['profile_type'] ?? '') {
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
            default    => null,
        };

        $userRow = $this->db->table('users')->where('id', $request['user_id'])->get()->getRowArray() ?: [];
        $profileRow = $this->db->table('user_profiles')->where('user_id', $request['user_id'])->get()->getRowArray() ?: [];
        $specificRow = $typeTable ? ($this->db->table($typeTable)->where('user_id', $request['user_id'])->get()->getRowArray() ?: []) : [];

        $current = array_merge($userRow, $profileRow, $specificRow);
        $current['full_name'] = $userRow['name'] ?? '';

        $history = $this->db->table('workflow_transitions')
            ->where('resource_type', 'profile_change_request')
            ->where('resource_id', $requestId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        return view('main/workspace/pages/operator/profile_change_request_detail', [
            'title'          => 'Detail Perubahan Profil #' . $requestId . ' | Operator BegawiPengadaan',
            'pageTitle'      => 'Detail Permohonan Perubahan Profil #' . $requestId,
            'breadcrumbTail' => [
                ['label' => 'Perubahan Profil', 'url' => route_to('workspace.operator.profile-change-requests')],
                ['label' => '#' . $requestId],
            ],
            'request'        => $request,
            'payload'        => $payload,
            'current'        => $current,
            'history'        => $history,
        ]);
    }

    public function approveProfileChangeRequest(string $id)
    {
        $actorId   = $this->assertOperatorAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $notes     = (string) $this->request->getPost('notes');

        try {
            (new \App\Services\Admin\ProfileChangeRequestService())->approve($requestId, $actorId, $notes);
            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permohonan perubahan profil #' . $requestId . ' berhasil disetujui dan diterapkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectProfileChangeRequest(string $id)
    {
        $actorId   = $this->assertOperatorAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $reason    = trim((string) $this->request->getPost('reason')) ?: 'Permohonan perubahan profil ditolak oleh Operator.';
        $notes     = (string) $this->request->getPost('notes');

        try {
            (new \App\Services\Admin\ProfileChangeRequestService())->reject($requestId, $reason, $actorId, $notes);
            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permohonan perubahan profil #' . $requestId . ' ditolak.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function revisionProfileChangeRequest(string $id)
    {
        $actorId   = $this->assertOperatorAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $note      = trim((string) $this->request->getPost('revision_note'));

        if ($note === '') {
            return redirect()->back()->with('error', 'Catatan revisi wajib diisi.');
        }

        try {
            (new \App\Services\Admin\ProfileChangeRequestService())->requestRevision($requestId, $note, $actorId);
            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permintaan revisi perubahan profil #' . $requestId . ' berhasil dikirim ke pengguna.');
        } catch (\Throwable) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * 4. KELAS & TRAINER ASSIGNMENT
     * Menghubungkan kelas pembelajaran dengan trainer pengampu.
     * Langsung tersinkronisasi ke menu "Kelas Saya" di Trainer Workspace.
     */
    public function classTrainers()
    {
        $this->assertOperatorAccess();

        $rawClasses = $this->db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id')
            ->orderBy('lc.created_at', 'DESC')
            ->get()->getResultArray();

        $classes = [];
        foreach ($rawClasses as $c) {
            $cid = (int) $c['id'];
            $c['student_count'] = $this->db->table('enrollments')->where('class_id', $cid)->countAllResults();

            $trainers = $this->db->table('class_trainers ct')
                ->select('ct.user_id, ct.status, u.name')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.class_id', $cid)
                ->get()->getResultArray();

            $c['trainers'] = $trainers;
            $classes[] = $c;
        }

        $rawTrainers = $this->db->table('users u')
            ->select('u.id, u.name, u.email, u.account_status')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('r.key', 'trainer')
            ->where('ur.status', 'active')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        $trainers = [];
        foreach ($rawTrainers as $t) {
            $tid = (int) $t['id'];
            $t['active_classes_count'] = $this->db->table('class_trainers')
                ->where('user_id', $tid)
                ->where('status', 'active')
                ->countAllResults();
            $trainers[] = $t;
        }

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/class_trainers', [
            'title'          => 'Kelas & Penugasan Trainer | Operator BegawiPengadaan',
            'pageTitle'      => 'Kelas & Trainer Assignment',
            'breadcrumbTail' => [['label' => 'Kelas & Trainer Assignment']],
            'userName'       => $userName,
            'roles'          => ['operator'],
            'primaryRole'    => 'operator',
            'roleLabel'      => 'Operator',
            'classes'        => $classes,
            'trainers'       => $trainers,
        ]);
    }

    public function assignTrainer()
    {
        $actorId = $this->assertOperatorAccess();
        $classId = (int) $this->request->getPost('class_id');
        $userId  = (int) $this->request->getPost('user_id');

        if ($classId < 1 || $userId < 1) {
            return redirect()->back()->with('error', 'Pilih kelas dan trainer yang valid.');
        }

        $class = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if (! $class) {
            return redirect()->back()->with('error', 'Kelas pembelajaran tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $existing = $this->db->table('class_trainers')
            ->where('class_id', $classId)
            ->where('user_id', $userId)
            ->get()->getRowArray();

        if ($existing) {
            $this->db->table('class_trainers')
                ->where('class_id', $classId)
                ->where('user_id', $userId)
                ->update([
                    'status'      => 'active',
                    'assigned_by' => $actorId,
                    'assigned_at' => $now,
                ]);
        } else {
            $this->db->table('class_trainers')->insert([
                'class_id'    => $classId,
                'user_id'     => $userId,
                'assigned_by' => $actorId,
                'assigned_at' => $now,
                'status'      => 'active',
            ]);
        }

        $this->logAudit($actorId, 'class_trainer.assign', 'class_trainer', $classId, ['class_id' => $classId, 'user_id' => $userId]);

        return redirect()->back()->with('message', 'Trainer berhasil ditugaskan ke kelas ' . esc($class['name']) . '. Penugasan langsung aktif di ruang kerja trainer.');
    }

    public function revokeTrainer()
    {
        $actorId = $this->assertOperatorAccess();
        $classId = (int) $this->request->getPost('class_id');
        $userId  = (int) $this->request->getPost('user_id');

        if ($classId < 1 || $userId < 1) {
            return redirect()->back()->with('error', 'Parameter penugasan tidak valid.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('class_trainers')
            ->where('class_id', $classId)
            ->where('user_id', $userId)
            ->update([
                'status' => 'revoked',
            ]);

        $this->logAudit($actorId, 'class_trainer.revoke', 'class_trainer', $classId, ['class_id' => $classId, 'user_id' => $userId]);

        return redirect()->back()->with('message', 'Penugasan trainer untuk kelas ini berhasil dicabut.');
    }

    /**
     * 5. OPERASIONAL EVENT
     */
    public function events()
    {
        $this->assertOperatorAccess();

        $published = $this->db->table('events')->whereIn('status', ['open_registration', 'coming_soon', 'published'])->countAllResults();
        $draft     = $this->db->table('events')->where('status', 'draft')->countAllResults();
        $ongoing   = $this->db->table('events')->whereIn('status', ['open_registration', 'ongoing'])->countAllResults();

        $list = $this->db->table('events')
            ->select('id, title, type, mode, status, start_at, capacity')
            ->orderBy('created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'open_registration', 'published' => '<span class="badge bg-success-subtle text-success">Registrasi Dibuka</span>',
                'coming_soon'                    => '<span class="badge bg-info-subtle text-info">Segera Hadir</span>',
                'ongoing'                        => '<span class="badge bg-primary-subtle text-primary">Berlangsung</span>',
                'finished', 'completed'          => '<span class="badge bg-secondary-subtle text-secondary">Selesai</span>',
                default                          => '<span class="badge bg-warning-subtle text-warning">' . esc(ucfirst($item['status'])) . '</span>',
            };

            $partUrl   = route_to('workspace.operator.events.participants', (string) $item['id']);
            $actionBtn = '<a href="' . $partUrl . '" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="fas fa-users me-1"></i>Peserta</a>';

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['title'] ?? '-') . '</strong>',
                '<span class="badge bg-light text-dark">' . esc($item['type'] ?? '-') . ' / ' . esc($item['mode'] ?? '-') . '</span>',
                $badge,
                (int) ($item['capacity'] ?? 0) . ' Kursi',
                $item['start_at'] ? esc(date('d/m/Y H:i', strtotime($item['start_at']))) : '-',
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Event',
            'title'       => 'Operasional Event & Workshop PBJ',
            'description' => 'Operasional seminar, workshop, bimbingan teknis, dan event PBJ serta peserta.',
            'cards'       => [
                ['title' => 'Event Aktif / Dibuka', 'value' => sprintf('%02d', $published), 'meta' => 'pendaftaran terbuka'],
                ['title' => 'Event Berjalan', 'value' => sprintf('%02d', $ongoing), 'meta' => 'sedang diselenggarakan'],
                ['title' => 'Draft Event', 'value' => sprintf('%02d', $draft), 'meta' => 'dalam persiapan'],
            ],
            'tableTitle'  => 'Daftar Event & Workshop PBJ',
            'table'       => [
                'headers' => ['ID', 'Judul Event', 'Tipe / Moda', 'Status', 'Kapasitas', 'Jadwal Mulai', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Lihat Agenda Event Publik', 'url' => site_url('event'), 'btn' => 'Buka Event Publik'],
            ],
        ];

        return $this->renderOperasional('Operasional Event', $sectionContent);
    }

    public function eventParticipants(string $eventId)
    {
        $this->assertOperatorAccess();
        $eid = (int) $eventId;

        $event = $this->db->table('events')->where('id', $eid)->get()->getRowArray();
        if (! $event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $participants = $this->db->table('event_registrations er')
            ->select('er.id, er.registered_at, er.status, u.name AS user_name, u.email AS user_email, u.username, a.status AS attendance_status, a.recorded_at AS attendance_recorded_at,
                      asn.institution_name AS asn_instansi, asn.position_name AS asn_jabatan, asn.employee_identifier AS asn_nip,
                      prov.business_name AS prov_instansi, prov.registrant_position AS prov_jabatan,
                      acad.institution_name AS acad_instansi, acad.role_name AS acad_jabatan,
                      pub.organization_name AS pub_instansi')
            ->join('users u', 'u.id = er.user_id')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->join('provider_profiles prov', 'prov.user_id = u.id', 'left')
            ->join('academic_profiles acad', 'acad.user_id = u.id', 'left')
            ->join('public_profiles pub', 'pub.user_id = u.id', 'left')
            ->where('er.event_id', $eid)
            ->orderBy('er.registered_at', 'DESC')
            ->get()->getResultArray();

        foreach ($participants as &$p) {
            $p['institution'] = $p['asn_instansi'] ?: ($p['prov_instansi'] ?: ($p['acad_instansi'] ?: ($p['pub_instansi'] ?: '-')));
            $p['position']    = $p['asn_jabatan'] ?: ($p['prov_jabatan'] ?: ($p['acad_jabatan'] ?: '-'));
            $p['nip']         = $p['asn_nip'] ?: ($p['username'] ?: '-');
        }
        unset($p);

        $totalRegistered = count($participants);
        $presentCount    = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'present'));
        $excusedCount    = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'excused'));
        $absentCount     = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'absent'));
        $unrecordedCount = count(array_filter($participants, static fn($p): bool => empty($p['attendance_status'])));
        $attendanceRate  = $totalRegistered > 0 ? round(($presentCount / $totalRegistered) * 100, 1) : 0.0;

        $metrics = [
            'total'           => $totalRegistered,
            'present'         => $presentCount,
            'excused'         => $excusedCount,
            'absent'          => $absentCount,
            'unrecorded'      => $unrecordedCount,
            'attendance_rate' => $attendanceRate,
            'quota'           => (int) ($event['quota'] ?? 0),
        ];

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/event_participants', [
            'title'             => 'Peserta Event: ' . esc($event['title']) . ' | Operator BegawiPengadaan',
            'pageTitle'         => 'Peserta Event',
            'breadcrumbTail'    => [
                ['label' => 'Operasional Event', 'url' => route_to('workspace.operator.events')],
                ['label' => esc($event['title'])],
            ],
            'userName'          => $userName,
            'roles'             => ['operator'],
            'primaryRole'       => 'operator',
            'roleLabel'         => 'Operator',
            'event'             => $event,
            'participants'      => $participants,
            'attendanceMetrics' => $metrics,
        ]);
    }

    /**
     * CETAK DAFTAR HADIR RESMI ACARA (OFFICIAL ROSTER)
     * Format lampiran dinas resmi Biro PBJ Pemprov Lampung.
     */
    public function printEventRoster(string $eventId)
    {
        $this->assertOperatorAccess();
        $eid = (int) $eventId;

        $event = $this->db->table('events')->where('id', $eid)->get()->getRowArray();
        if (! $event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $participants = $this->db->table('event_registrations er')
            ->select('er.id, er.registered_at, er.status, u.name AS user_name, u.email AS user_email, u.username, a.status AS attendance_status, a.recorded_at,
                      asn.institution_name AS asn_instansi, asn.position_name AS asn_jabatan, asn.employee_identifier AS asn_nip,
                      prov.business_name AS prov_instansi, prov.registrant_position AS prov_jabatan,
                      acad.institution_name AS acad_instansi, acad.role_name AS acad_jabatan,
                      pub.organization_name AS pub_instansi')
            ->join('users u', 'u.id = er.user_id')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->join('provider_profiles prov', 'prov.user_id = u.id', 'left')
            ->join('academic_profiles acad', 'acad.user_id = u.id', 'left')
            ->join('public_profiles pub', 'pub.user_id = u.id', 'left')
            ->where('er.event_id', $eid)
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        foreach ($participants as &$p) {
            $p['institution'] = $p['asn_instansi'] ?: ($p['prov_instansi'] ?: ($p['acad_instansi'] ?: ($p['pub_instansi'] ?: '-')));
            $p['position']    = $p['asn_jabatan'] ?: ($p['prov_jabatan'] ?: ($p['acad_jabatan'] ?: '-'));
            $p['nip']         = $p['asn_nip'] ?: ($p['username'] ?: '-');
        }
        unset($p);

        $presentCount    = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'present'));
        $totalRegistered = count($participants);
        $attendanceRate  = $totalRegistered > 0 ? round(($presentCount / $totalRegistered) * 100, 1) : 0.0;

        return view('main/workspace/pages/operator/roster_daftar_hadir', [
            'event'           => $event,
            'participants'    => $participants,
            'stats'           => [
                'total'           => $totalRegistered,
                'present'         => $presentCount,
                'attendance_rate' => $attendanceRate,
                'quota'           => (int) ($event['quota'] ?? 0),
            ],
            'operatorName'    => (string) (session()->get('name') ?: session()->get('username') ?: 'Operator PBJ'),
        ]);
    }

    /**
     * 6. PRESENSI
     */
    public function attendance()
    {
        $this->assertOperatorAccess();

        $recorded      = $this->db->table('attendances')->countAllResults();
        $registrations = $this->db->table('event_registrations')->countAllResults();

        $list = $this->db->table('attendances a')
            ->select('a.id, a.status, a.recorded_at, a.notes, ev.title AS event_title, u.name AS user_name, u.email AS user_email')
            ->join('event_registrations er', 'er.id = a.event_registration_id')
            ->join('events ev', 'ev.id = er.event_id')
            ->join('users u', 'u.id = er.user_id')
            ->orderBy('a.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'present' => '<span class="badge bg-success-subtle text-success">Hadir</span>',
                'absent'  => '<span class="badge bg-danger-subtle text-danger">Tidak Hadir</span>',
                'excused' => '<span class="badge bg-warning-subtle text-warning">Izin</span>',
                default   => '<span class="badge bg-secondary-subtle text-secondary">' . esc($item['status']) . '</span>',
            };

            $updateUrl = route_to('workspace.operator.attendance.update', (string) $item['id']);
            $csrf      = csrf_field();
            $actionBtn = '<div class="d-flex gap-1">' .
                '<form action="' . $updateUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="status" value="present"><button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Tandai Hadir"><i class="fas fa-check"></i></button></form>' .
                '<form action="' . $updateUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="status" value="absent"><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Tandai Absen"><i class="fas fa-times"></i></button></form>' .
                '</div>';

            $rows[] = [
                '#' . $item['id'],
                esc($item['event_title'] ?? '-'),
                '<strong>' . esc($item['user_name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['user_email'] ?? '') . '</small>',
                $badge,
                $item['recorded_at'] ? esc(date('d/m/Y H:i', strtotime($item['recorded_at']))) : '-',
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Presensi & Kehadiran',
            'title'       => 'Pencatatan Kehadiran',
            'description' => 'Monitoring presensi peserta dalam kegiatan pembelajaran dan event PBJ.',
            'cards'       => [
                ['title' => 'Total Presensi Tercatat', 'value' => sprintf('%02d', $recorded), 'meta' => 'catatan kehadiran'],
                ['title' => 'Pendaftaran Event', 'value' => sprintf('%02d', $registrations), 'meta' => 'total peserta terdaftar'],
            ],
            'tableTitle'  => 'Catatan Presensi Terkini',
            'table'       => [
                'headers' => ['ID', 'Nama Event', 'Peserta', 'Status', 'Waktu Tercatat', 'Aksi Cepat'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Kelola Event & Pendaftaran Peserta', 'url' => route_to('workspace.operator.events'), 'btn' => 'Buka Event'],
            ],
        ];

        return $this->renderOperasional('Presensi & Kehadiran', $sectionContent);
    }

    public function updateAttendance(string $attendanceId)
    {
        $actorId = $this->assertOperatorAccess();
        $aid     = (int) $attendanceId;
        $status  = (string) $this->request->getPost('status');
        $notes   = (string) $this->request->getPost('notes');

        if (! in_array($status, ['present', 'absent', 'excused'], true)) {
            $status = 'present';
        }

        $attendance = $this->db->table('attendances')->where('id', $aid)->get()->getRowArray();
        if (! $attendance) {
            return redirect()->back()->with('error', 'Catatan presensi tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('attendances')->where('id', $aid)->update([
            'status'      => $status,
            'notes'       => $notes,
            'recorded_by' => $actorId,
            'recorded_at' => $now,
            'updated_at'  => $now,
        ]);

        $this->logAudit($actorId, 'attendance.operator_update', 'attendance', $aid, ['attendance_id' => $aid, 'status' => $status], $notes);

        return redirect()->back()->with('message', 'Catatan presensi berhasil diperbarui.');
    }

    /**
     * 7. MONITORING PEMBELAJARAN
     */
    public function learningMonitoring()
    {
        $this->assertOperatorAccess();

        $classes = $this->db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id')
            ->orderBy('lc.created_at', 'DESC')
            ->get()->getResultArray();

        $monitoring = [];
        foreach ($classes as $c) {
            $cid = (int) $c['id'];

            $studentCount = $this->db->table('enrollments')->where('class_id', $cid)->countAllResults();

            $passedCount = $this->db->table('enrollments')
                ->where('class_id', $cid)
                ->groupStart()
                ->where('status', 'completed')
                ->orWhere('passed_at IS NOT NULL')
                ->groupEnd()
                ->countAllResults();

            $trainers = $this->db->table('class_trainers ct')
                ->select('u.name')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.class_id', $cid)
                ->where('ct.status', 'active')
                ->get()->getResultArray();
            $trainersText = implode(', ', array_column($trainers, 'name'));

            $enrollmentIds = $this->db->table('enrollments')->select('id')->where('class_id', $cid)->get()->getResultArray();
            $avgProgress = 0.0;
            if (! empty($enrollmentIds)) {
                $eids = array_column($enrollmentIds, 'id');
                $progRows = $this->db->table('learning_progress')
                    ->select('progress_percent')
                    ->whereIn('enrollment_id', $eids)
                    ->get()->getResultArray();
                if (! empty($progRows)) {
                    $sum = array_sum(array_column($progRows, 'progress_percent'));
                    $avgProgress = round($sum / count($progRows), 1);
                }
            }

            $c['student_count'] = $studentCount;
            $c['passed_count']  = $passedCount;
            $c['trainers_text'] = $trainersText;
            $c['avg_progress']  = $avgProgress;

            $monitoring[] = $c;
        }

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/learning_monitoring', [
            'title'          => 'Monitoring Pembelajaran | Operator BegawiPengadaan',
            'pageTitle'      => 'Monitoring Pembelajaran',
            'breadcrumbTail' => [['label' => 'Monitoring Pembelajaran']],
            'userName'       => $userName,
            'roles'          => ['operator'],
            'primaryRole'    => 'operator',
            'roleLabel'      => 'Operator',
            'monitoring'     => $monitoring,
        ]);
    }

    /**
     * 8. VERIFIKASI SERTIFIKAT
     */
    public function certificateVerification()
    {
        $this->assertOperatorAccess();

        $issuedCertRows  = $this->db->table('certificates')
            ->select('source_id')
            ->where('source_type', 'learning_enrollment')
            ->where('status', 'issued')
            ->get()->getResultArray();
        $issuedSourceIds = array_filter(array_column($issuedCertRows, 'source_id'));

        $pendingBuilder = $this->db->table('enrollments e')
            ->select('e.id AS enrollment_id, e.final_score, e.certificate_eligible_at, u.name AS user_name, u.email AS user_email, lc.name AS class_name, c.title AS course_title')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses c', 'c.id = lc.course_id')
            ->where('e.certificate_eligible_at IS NOT NULL');

        if (! empty($issuedSourceIds)) {
            $pendingBuilder->whereNotIn('e.id', $issuedSourceIds);
        }

        $pendingQueue = $pendingBuilder->orderBy('e.certificate_eligible_at', 'DESC')->get()->getResultArray();

        $issuedCertificates = $this->db->table('certificates cert')
            ->select('cert.id, cert.certificate_number, cert.verification_token_hash, cert.status, cert.issued_at, u.name AS user_name, u.email AS user_email, issuer.name AS issuer_name')
            ->join('users u', 'u.id = cert.user_id')
            ->join('users issuer', 'issuer.id = cert.issued_by', 'left')
            ->orderBy('cert.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/certificates', [
            'title'              => 'Verifikasi Sertifikat | Operator BegawiPengadaan',
            'pageTitle'          => 'Verifikasi Sertifikat',
            'breadcrumbTail'     => [['label' => 'Verifikasi Sertifikat']],
            'userName'           => $userName,
            'roles'              => ['operator'],
            'primaryRole'        => 'operator',
            'roleLabel'          => 'Operator',
            'pendingQueue'       => $pendingQueue,
            'issuedCertificates' => $issuedCertificates,
        ]);
    }

    public function issueCertificate(string $enrollmentId)
    {
        $actorId = $this->assertOperatorAccess();
        $eid     = (int) $enrollmentId;

        $certNumber = $this->issueSingleCertificate($eid, $actorId);
        if (! $certNumber) {
            return redirect()->back()->with('error', 'Enrollment peserta tidak ditemukan.');
        }

        return redirect()->back()->with('message', 'Sertifikat #' . $certNumber . ' berhasil diterbitkan dan status kelulusan peserta telah diverifikasi.');
    }

    /**
     * TINDAKAN MASSAL (BULK ACTION): PENERBITAN SERTIFIKAT
     */
    public function bulkIssueCertificates()
    {
        $actorId = $this->assertOperatorAccess();
        $rawIds  = (array) $this->request->getPost('enrollment_ids');

        $ids = array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0);
        if ($ids === []) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih untuk penerbitan sertifikat.');
        }

        $successCount = 0;
        foreach ($ids as $eid) {
            $certNum = $this->issueSingleCertificate($eid, $actorId);
            if ($certNum !== null) {
                $successCount++;
            }
        }

        return redirect()->back()->with('message', "Berhasil menerbitkan {$successCount} sertifikat kelulusan secara massal.");
    }

    /**
     * HELPER LOGIKA PENERBITAN SERTIFIKAT TUNGGAL
     */
    private function issueSingleCertificate(int $eid, int $actorId): ?string
    {
        $enrollment = $this->db->table('enrollments')->where('id', $eid)->get()->getRowArray();
        if (! $enrollment) {
            return null;
        }

        $existing = $this->db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('source_id', $eid)
            ->get()->getRowArray();

        $now = date('Y-m-d H:i:s');
        $tokenHash = hash('sha256', bin2hex(random_bytes(16)) . $eid . $now);

        if ($existing) {
            $certNumber = $existing['certificate_number'];
            $this->db->table('certificates')->where('id', $existing['id'])->update([
                'status'     => 'issued',
                'issued_at'  => $now,
                'issued_by'  => $actorId,
                'updated_at' => $now,
            ]);
        } else {
            $seq = (new \App\Models\Certificate\CertificateSequenceModel())->nextForYear((int) date('Y'));
            $certNumber = \App\Services\CertificateGenerator::formatCertificateNumber($seq, 'learning_enrollment', [
                'class_id'  => (int) ($enrollment['class_id'] ?? 1),
                'source_id' => $eid,
            ]);
            $this->db->table('certificates')->insert([
                'source_type'             => 'learning_enrollment',
                'source_id'               => $eid,
                'user_id'                 => (int) $enrollment['user_id'],
                'certificate_number'      => $certNumber,
                'verification_token_hash' => $tokenHash,
                'status'                  => 'issued',
                'issued_at'               => $now,
                'issued_by'               => $actorId,
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);
        }

        $this->db->table('enrollments')->where('id', $eid)->update([
            'status'                  => 'completed',
            'completed_at'            => $enrollment['completed_at'] ?? $now,
            'passed_at'               => $enrollment['passed_at'] ?? $now,
            'certificate_eligible_at' => $enrollment['certificate_eligible_at'] ?? $now,
            'updated_at'              => $now,
        ]);

        $this->logAudit($actorId, 'certificate.operator_issue', 'certificate', $eid, ['enrollment_id' => $eid, 'certificate_number' => $certNumber]);

        return $certNumber;
    }

    public function revokeCertificate(string $certificateId)
    {
        $actorId = $this->assertOperatorAccess();
        $cid     = (int) $certificateId;
        $reason  = trim((string) $this->request->getPost('reason')) ?: 'Dibatalkan oleh Operator.';

        $cert = $this->db->table('certificates')->where('id', $cid)->get()->getRowArray();
        if (! $cert) {
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('certificates')->where('id', $cid)->update([
            'status'            => 'revoked',
            'revoked_at'        => $now,
            'revoked_by'        => $actorId,
            'revocation_reason' => $reason,
            'updated_at'        => $now,
        ]);

        $this->logAudit($actorId, 'certificate.operator_revoke', 'certificate', $cid, ['certificate_id' => $cid], $reason);

        return redirect()->back()->with('message', 'Sertifikat #' . ($cert['certificate_number'] ?? $cid) . ' telah berhasil dicabut/dibatalkan.');
    }

    /**
     * 9. EVALUASI PEMBELAJARAN & OVERRIDE
     */
    public function evaluations()
    {
        $this->assertOperatorAccess();

        $pendingReviews   = $this->db->table('assignment_submissions')->whereIn('status', ['submitted', 'in_review'])->countAllResults();
        $gradedCount      = $this->db->table('assignment_submissions')->where('status', 'graded')->countAllResults();
        $revisionCount    = $this->db->table('assignment_submissions')->where('status', 'revision_required')->countAllResults();
        $totalSubmissions = $this->db->table('assignment_submissions')->countAllResults();

        $list = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users grader', 'grader.id = s.graded_by', 'left')
            ->orderBy('s.submitted_at', 'DESC')
            ->limit(35)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'graded'            => '<span class="badge bg-success-subtle text-success">Graded</span>',
                'revision_required' => '<span class="badge bg-danger-subtle text-danger">Revisi</span>',
                'in_review'         => '<span class="badge bg-info-subtle text-info">In Review</span>',
                default             => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $reviewUrl = route_to('workspace.operator.submission.review', (string) $item['id']);
            $actionBtn = '<a href="' . $reviewUrl . '" class="btn btn-sm btn-outline-warning py-0 px-2 fw-medium text-dark"><i class="fas fa-edit me-1"></i>Review / Override</a>';

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['participant_name'] ?? 'Peserta') . '</strong><br><small class="text-muted">' . esc($item['participant_email'] ?? '') . '</small>',
                '<strong>' . esc($item['assignment_title'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['class_name'] ?? '-') . '</small>',
                $badge,
                $item['score'] !== null ? '<strong>' . number_format((float) $item['score'], 1) . '</strong>' : '<span class="text-muted">-</span>',
                esc($item['grader_name'] ?? 'Belum dinilai'),
                $item['submitted_at'] ? esc(date('d/m/Y H:i', strtotime($item['submitted_at']))) : '-',
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Kewenangan Override Operasional',
            'title'       => 'Evaluasi Pembelajaran & Override Nilai',
            'description' => 'Monitoring seluruh penugasan dan hasil evaluasi trainer dengan hak override operasional.',
            'cards'       => [
                ['title' => 'Perlu Review', 'value' => sprintf('%02d', $pendingReviews), 'meta' => 'antrean review submission'],
                ['title' => 'Telah Dinilai', 'value' => sprintf('%02d', $gradedCount), 'meta' => 'selesai evaluasi'],
                ['title' => 'Minta Revisi', 'value' => sprintf('%02d', $revisionCount), 'meta' => 'perlu perbaikan peserta'],
                ['title' => 'Total Submission', 'value' => sprintf('%02d', $totalSubmissions), 'meta' => 'arsip tugas terkumpul'],
            ],
            'tableTitle'  => 'Antrean Evaluasi Tugas & Submisi Lintas Kelas',
            'table'       => [
                'headers' => ['ID', 'Peserta', 'Tugas & Kelas', 'Status', 'Nilai', 'Penilai', 'Waktu Submit', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Monitoring Kelulusan Peserta pada Enrollment Kelas', 'url' => route_to('workspace.operator.enrollments'), 'btn' => 'Buka Enrollment'],
                ['label' => 'Persetujuan Sertifikat & Hasil Kelulusan', 'url' => route_to('workspace.operator.certificates'), 'btn' => 'Buka Sertifikat'],
            ],
        ];

        return $this->renderOperasional('Evaluasi & Override', $sectionContent);
    }

    public function reviewSubmission(string $submissionId)
    {
        $this->assertOperatorAccess();

        $submission = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.instructions AS assignment_instructions, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users grader', 'grader.id = s.graded_by', 'left')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Submission tidak ditemukan.');
        }

        $files = $this->db->table('assignment_submission_files sf')
            ->select('sf.*, fa.original_name, fa.mime_type, fa.size_bytes')
            ->join('file_assets fa', 'fa.id = sf.file_id', 'left')
            ->where('sf.submission_id', (int) $submissionId)
            ->get()->getResultArray();

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/override_submission', [
            'title'       => 'Override Penilaian #' . $submissionId . ' | Operator BegawiPengadaan',
            'pageTitle'   => 'Override Penilaian Tugas',
            'userName'    => $userName,
            'roles'       => ['operator'],
            'primaryRole' => 'operator',
            'roleLabel'   => 'Operator',
            'submission'  => $submission,
            'files'       => $files,
        ]);
    }

    public function overrideSubmission(string $submissionId)
    {
        $actorId = $this->assertOperatorAccess();
        $score   = (float) $this->request->getPost('score');
        $status  = (string) $this->request->getPost('status');
        $reason  = trim((string) $this->request->getPost('override_reason')) ?: 'Penyesuaian nilai oleh kewenangan Operator.';

        $submission = $this->db->table('assignment_submissions s')
            ->select('s.*, a.class_id')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            return redirect()->back()->with('error', 'Submission tugas tidak ditemukan.');
        }

        if (! in_array($status, ['graded', 'revision_required', 'in_review'], true)) {
            $status = 'graded';
        }
        $score = max(0.0, min(100.0, $score));

        $now = date('Y-m-d H:i:s');
        $existingFeedback = trim((string) ($submission['feedback'] ?? ''));
        $overrideTag = "[OVERRIDE OPERATOR " . date('d/m/Y H:i') . "]: " . $reason;
        $updatedFeedback = $existingFeedback !== '' ? ($existingFeedback . "\n\n" . $overrideTag) : $overrideTag;

        $this->db->table('assignment_submissions')->where('id', (int) $submissionId)->update([
            'score'      => $score,
            'status'     => $status,
            'feedback'   => $updatedFeedback,
            'graded_by'  => $actorId,
            'graded_at'  => $now,
            'updated_at' => $now,
        ]);

        if ($status === 'graded') {
            try {
                (new \App\Services\LearningService($this->db))->evaluateCompletion((int) $submission['enrollment_id']);
            } catch (\Throwable $e) {
                log_message('error', 'evaluateCompletion failed on override: {m}', ['m' => $e->getMessage()]);
            }
        }

        $this->logAudit(
            $actorId,
            'evaluation.operator_override',
            'assignment_submission',
            (int) $submissionId,
            [
                'old_score'  => $submission['score'],
                'new_score'  => $score,
                'old_status' => $submission['status'],
                'new_status' => $status,
            ],
            $reason
        );

        return redirect()->to(route_to('workspace.operator.evaluations'))
            ->with('message', 'Penilaian submission #' . $submissionId . ' berhasil di-override oleh Operator (Nilai: ' . $score . ', Status: ' . $status . ').');
    }

    public function overrideEnrollmentScore(string $enrollmentId)
    {
        $actorId = $this->assertOperatorAccess();
        $score   = (float) $this->request->getPost('final_score');
        $status  = (string) $this->request->getPost('status') ?: 'completed';
        $reason  = trim((string) $this->request->getPost('reason')) ?: 'Penyesuaian nilai akhir / kelulusan khusus oleh Operator.';

        $enrollment = $this->db->table('enrollments')->where('id', (int) $enrollmentId)->get()->getRowArray();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'Enrollment tidak ditemukan.');
        }

        if (! in_array($status, ['active', 'completed', 'withdrawn', 'cancelled'], true)) {
            $status = 'completed';
        }
        $score = max(0.0, min(100.0, $score));
        $now = date('Y-m-d H:i:s');

        $updateData = [
            'final_score' => $score,
            'status'      => $status,
            'updated_at'  => $now,
        ];

        if ($status === 'completed') {
            $updateData['completed_at'] = $now;
            $updateData['passed_at']    = $now;
            $updateData['certificate_eligible_at'] = $now;
        }

        $this->db->table('enrollments')->where('id', (int) $enrollmentId)->update($updateData);

        $this->logAudit(
            $actorId,
            'enrollment.operator_override',
            'enrollment',
            (int) $enrollmentId,
            [
                'old_score'  => $enrollment['final_score'],
                'new_score'  => $score,
                'old_status' => $enrollment['status'],
                'new_status' => $status,
            ],
            $reason
        );

        return redirect()->back()->with('message', 'Nilai akhir dan status kelulusan enrollment #' . $enrollmentId . ' berhasil di-override oleh Operator.');
    }

    /**
     * 10. AKTIVITAS / AUDIT TRAIL MONITORING
     */
    public function activityMonitoring()
    {
        $this->assertOperatorAccess();

        $logs = $this->db->table('audit_logs a')
            ->select('a.*, u.name AS actor_name, u.email AS actor_email')
            ->join('users u', 'u.id = a.actor_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $rows = [];
        foreach ($logs as $log) {
            $badge = match ($log['result']) {
                'success'           => '<span class="badge bg-success-subtle text-success">Berhasil</span>',
                'failed', 'failure' => '<span class="badge bg-danger-subtle text-danger">Gagal</span>',
                default             => '<span class="badge bg-info-subtle text-info">' . esc($log['result']) . '</span>',
            };
            $rows[] = [
                '#' . $log['id'],
                esc($log['actor_name'] ?: 'Sistem'),
                esc($log['action']),
                esc($log['resource_type'] ? $log['resource_type'] . ' #' . $log['resource_id'] : '-'),
                $badge,
                esc(date('d/m/Y H:i', strtotime($log['created_at']))),
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Monitoring Operasional',
            'title'       => 'Aktivitas & Log Sistem',
            'description' => 'Jejak audit log dan aktivitas operasional seluruh pengguna dan sistem.',
            'cards'       => [
                ['title' => 'Total Log Terkini', 'value' => sprintf('%02d', count($logs)), 'meta' => '50 aktivitas terakhir'],
            ],
            'tableTitle'  => 'Log Aktivitas dan Jejak Audit Sistem',
            'table'       => [
                'headers' => ['ID', 'Pengguna', 'Aktivitas', 'Objek', 'Hasil', 'Waktu'],
                'rows'    => $rows,
            ],
            'items'       => [],
        ];

        return $this->renderOperasional('Aktivitas Sistem', $sectionContent);
    }

    /**
     * OPERASIONAL ENROLLMENT KELAS
     */
    public function enrollments()
    {
        $this->assertOperatorAccess();

        $pending   = $this->db->table('enrollments')->where('status', 'pending')->countAllResults();
        $active    = $this->db->table('enrollments')->where('status', 'active')->countAllResults();
        $completed = $this->db->table('enrollments')->where('status', 'completed')->countAllResults();

        $list = $this->db->table('enrollments e')
            ->select('e.id, e.status, e.enrolled_at, e.final_score, u.name, u.email, lc.name AS class_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->orderBy('e.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'active'    => '<span class="badge bg-primary-subtle text-primary">Aktif</span>',
                'completed' => '<span class="badge bg-success-subtle text-success">Selesai</span>',
                'cancelled' => '<span class="badge bg-danger-subtle text-danger">Dibatalkan</span>',
                default     => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $actionBtn = '-';
            if ($item['status'] === 'pending') {
                $actUrl    = route_to('workspace.operator.enrollments.activate', $item['id']);
                $csrf      = csrf_field();
                $actionBtn = '<form action="' . $actUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-primary py-0 px-2">Aktifkan</button></form>';
            }

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['email'] ?? '') . '</small>',
                esc($item['class_name'] ?? '-'),
                $badge,
                $item['final_score'] !== null ? number_format((float) $item['final_score'], 1) : '-',
                esc(date('d/m/Y H:i', strtotime($item['enrolled_at'] ?? 'now'))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Pembelajaran',
            'title'       => 'Enrollment Kelas',
            'description' => 'Monitoring kepesertaan kelas dan progres enrollment peserta.',
            'cards'       => [
                ['title' => 'Pending Enrollment', 'value' => sprintf('%02d', $pending), 'meta' => 'pendaftaran menunggu aktivasi'],
                ['title' => 'Enrollment Aktif', 'value' => sprintf('%02d', $active), 'meta' => 'peserta aktif belajar'],
                ['title' => 'Lulus / Selesai', 'value' => sprintf('%02d', $completed), 'meta' => 'telah menyelesaikan kurikulum'],
            ],
            'tableTitle'  => 'Daftar Kepesertaan Kelas Terkini',
            'table'       => [
                'headers' => ['ID', 'Peserta', 'Kelas', 'Status', 'Nilai Akhir', 'Waktu Daftar', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Lihat Katalog Kelas Pembelajaran', 'url' => site_url('katalog'), 'btn' => 'Buka Katalog'],
            ],
        ];

        return $this->renderOperasional('Enrollment Kelas', $sectionContent);
    }

    public function activateEnrollment(string $id)
    {
        $this->assertOperatorAccess();
        $enrollmentId = (int) $id;
        $learning = new \App\Services\LearningService($this->db);
        try {
            $learning->activateEnrollment($enrollmentId);
            return redirect()->back()->with('message', 'Enrollment #' . $enrollmentId . ' berhasil diaktifkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function renderOperasional(string $title, array $sectionContent): string
    {
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operasional', [
            'title'          => $title . ' | Operator BegawiPengadaan',
            'pageTitle'      => $title,
            'breadcrumbTail' => [['label' => $title]],
            'userName'       => $userName,
            'roles'          => ['operator'],
            'primaryRole'    => 'operator',
            'roleLabel'      => 'Operator',
            'menuItems'      => $this->menuItems(),
            'sectionContent' => $sectionContent,
        ]);
    }

    private function menuItems(): array
    {
        return [
            ['label' => 'Dashboard', 'items' => [
                ['label' => 'Beranda', 'url' => route_to('workspace.operator.home')],
                ['label' => 'Aktivitas', 'url' => route_to('workspace.operator.activity-monitoring')],
            ]],
            ['label' => 'Operasional', 'items' => [
                ['label' => 'Approval', 'url' => route_to('workspace.operator.approvals')],
                ['label' => 'Verifikasi User', 'url' => route_to('workspace.operator.profile-verifications')],
                ['label' => 'Kelas & Trainer Assignment', 'url' => route_to('workspace.operator.class-trainers')],
                ['label' => 'Operasional Event', 'url' => route_to('workspace.operator.events')],
                ['label' => 'Presensi', 'url' => route_to('workspace.operator.attendance')],
                ['label' => 'Monitoring Pembelajaran', 'url' => route_to('workspace.operator.learning-monitoring')],
                ['label' => 'Verifikasi Sertifikat', 'url' => route_to('workspace.operator.certificates')],
                ['label' => 'Evaluasi & Override', 'url' => route_to('workspace.operator.evaluations')],
                ['label' => 'Profil', 'url' => route_to('workspace.profile.index')],
            ]],
        ];
    }
}
