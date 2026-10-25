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

    private function isUnrestrictedAdmin(): bool
    {
        $roles = (array) session()->get('roles');
        return ! empty(array_intersect(['administrator', 'super_administrator'], $roles));
    }

    private function hasFullClassScope(): bool
    {
        $roles = (array) session()->get('roles');
        return ! empty(array_intersect(['administrator', 'super_administrator', 'admin_pembelajaran'], $roles));
    }

    private function hasFullEventScope(): bool
    {
        $roles = (array) session()->get('roles');
        return ! empty(array_intersect(['administrator', 'super_administrator', 'admin_event'], $roles));
    }

    /**
     * Mengembalikan daftar class_id yang ditugaskan kepada Operator.
     * Mengembalikan null jika pengguna adalah Administrator / Super Admin / Admin Pembelajaran (akses penuh semua kelas).
     *
     * @return list<int>|null
     */
    private function getAssignedClassIds(?int $userId = null): ?array
    {
        if ($this->hasFullClassScope()) {
            return null;
        }

        $uid = $userId ?? (int) session()->get('user_id');
        if ($uid < 1) {
            return [0];
        }

        $rows = $this->db->table('class_trainers')
            ->select('class_id')
            ->where('user_id', $uid)
            ->where('status', 'active')
            ->get()->getResultArray();

        $ids = array_values(array_unique(array_filter(array_map(static fn($r): int => (int) ($r['class_id'] ?? 0), $rows))));

        return $ids !== [] ? $ids : [0];
    }

    /**
     * Mengembalikan daftar event_id yang ditugaskan kepada Operator.
     * Mengembalikan null jika pengguna adalah Administrator / Super Admin / Admin Event (akses penuh semua event).
     *
     * @return list<int>|null
     */
    private function getAssignedEventIds(?int $userId = null): ?array
    {
        if ($this->hasFullEventScope()) {
            return null;
        }

        $uid = $userId ?? (int) session()->get('user_id');
        if ($uid < 1) {
            return [0];
        }

        // 1. Events created by this user
        $createdRows = $this->db->table('events')
            ->select('id')
            ->where('created_by', $uid)
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();
        $createdIds = array_column($createdRows, 'id');

        // 2. Events where this user is assigned via event_registrations
        $assignedRows = $this->db->table('event_registrations')
            ->select('event_id')
            ->where('user_id', $uid)
            ->groupStart()
                ->whereIn('status', ['operator', 'trainer', 'speaker'])
                ->orLike('status_reason', 'operator')
                ->orLike('status_reason', 'assigned')
            ->groupEnd()
            ->get()->getResultArray();
        $assignedIds = array_column($assignedRows, 'event_id');

        $allIds = array_values(array_unique(array_filter(array_map('intval', array_merge($createdIds, $assignedIds)))));
        return $allIds !== [] ? $allIds : [0];
    }

    /**
     * Pastikan Operator berhak mengakses / mengelola kelas tertentu.
     */
    private function assertCanManageClass(int $classId): int
    {
        $actorId = $this->assertOperatorAccess();
        $allowedClassIds = $this->getAssignedClassIds($actorId);
        if ($allowedClassIds !== null && ! in_array($classId, $allowedClassIds, true)) {
            redirect()->back()
                ->with('error', 'Akses ditolak: Anda hanya berwenang mengelola kelas yang telah ditugaskan (assigned) kepada Anda oleh Administrator.')
                ->send();
            exit;
        }
        return $actorId;
    }

    /**
     * Pastikan Operator berhak mengakses / mengelola event tertentu.
     */
    private function assertCanManageEvent(int $eventId): int
    {
        $actorId = $this->assertOperatorAccess();
        $allowedEventIds = $this->getAssignedEventIds($actorId);
        if ($allowedEventIds !== null && ! in_array($eventId, $allowedEventIds, true)) {
            redirect()->to(route_to('workspace.operator.events'))
                ->with('error', 'Akses ditolak: Anda hanya berwenang mengelola event yang telah ditugaskan (assigned) kepada Anda.')
                ->send();
            exit;
        }
        return $actorId;
    }

    /**
     * Pastikan pengguna memiliki hak akses penuh Administrator / Super Administrator.
     * Digunakan untuk memproteksi antrean approval sistem dan verifikasi profil akun.
     */
    private function assertAdminAccess(): int
    {
        $actorId = $this->assertOperatorAccess();
        if (! $this->isUnrestrictedAdmin()) {
            redirect()->to('/workspace')
                ->with('error', 'Akses ditolak: Area ini dikhususkan secara penuh untuk Administrator & Super Administrator.')
                ->send();
            exit;
        }
        return $actorId;
    }

    /**
     * Pastikan pengguna memiliki hak akses Operator, Pengelola Pembelajaran/Event, atau Administrator.
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
        $allowed = ['operator', 'administrator', 'super_administrator', 'admin_pembelajaran', 'admin_event'];
        $hasAccess = false;
        foreach ($allowed as $r) {
            if (in_array($r, $roles, true)) {
                $hasAccess = true;
                break;
            }
        }

        if (! $hasAccess) {
            foreach ($roles as $r) {
                if (str_starts_with(strtolower(trim((string) $r)), 'admin_')) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if (! $hasAccess) {
            redirect()->to('/workspace')->with('error', 'Akses ditolak. Halaman khusus Operator / Pengelola Platform.')->send();
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
        $actorId = $this->assertOperatorAccess();
        $assignedClassIds = $this->getAssignedClassIds($actorId);

        $pendingVerif = $this->db->table('profile_verifications')->where('status', 'pending')->countAllResults();

        $enrollPendingBuilder = $this->db->table('enrollments')->where('status', 'pending');
        if ($assignedClassIds !== null) {
            $enrollPendingBuilder->whereIn('class_id', $assignedClassIds);
        }
        $pendingEnroll = $enrollPendingBuilder->countAllResults();

        $pendingApprov = $this->db->table('approvals')->where('status', 'pending')->countAllResults();
        $activeEvents  = $this->db->table('events')->whereIn('status', ['open_registration', 'coming_soon', 'published', 'ongoing'])->countAllResults();

        $classBuilder = $this->db->table('learning_classes')->whereIn('status', ['active', 'scheduled', 'ongoing', 'published', 'open']);
        if ($assignedClassIds !== null) {
            $classBuilder->whereIn('id', $assignedClassIds);
        }
        $activeClasses = $classBuilder->countAllResults();

        $trainerBuilder = $this->db->table('class_trainers')
            ->select('user_id')
            ->where('status', 'active')
            ->where('role !=', 'operator');
        if ($assignedClassIds !== null) {
            $trainerBuilder->whereIn('class_id', $assignedClassIds);
        }
        $activeTrainers = $trainerBuilder->distinct()->countAllResults();
        if ($activeTrainers === 0 && $assignedClassIds === null) {
            $activeTrainers = $this->db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'trainer')
                ->where('ur.status', 'active')
                ->countAllResults();
        }

        $attendedRows = $this->db->table('attendances')->select('event_registration_id')->get()->getResultArray();
        $attendedIds  = array_filter(array_column($attendedRows, 'event_registration_id'));
        $attBuilder   = $this->db->table('event_registrations')->whereIn('status', ['registered', 'confirmed']);
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
        if ($assignedClassIds !== null) {
            $certBuilder->whereIn('class_id', $assignedClassIds);
        }
        if (! empty($issuedSourceIds)) {
            $certBuilder->whereNotIn('id', $issuedSourceIds);
        }
        $pendingCertificates = $certBuilder->countAllResults();

        // 4 Feeds Aktivitas Terkini
        $recentApprovals = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.created_at, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        $recentAssignBuilder = $this->db->table('class_trainers ct')
            ->select('ct.class_id, ct.user_id, ct.status, ct.role, ct.assigned_at, u.name AS trainer_name, lc.name AS class_name, lc.code AS class_code')
            ->join('users u', 'u.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id');
        if ($assignedClassIds !== null) {
            $recentAssignBuilder->whereIn('ct.class_id', $assignedClassIds);
        }
        $recentAssignments = $recentAssignBuilder
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
        $this->assertAdminAccess();

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
                ? '<input type="checkbox" name="selected_ids[]" value="' . $item['id'] . '" class="form-check-input ws-bulk-check" form="wsBulkActionForm">'
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
        $actorId = $this->assertAdminAccess();
        $action  = (string) $this->request->getPost('bulk_action');
        $rawIds  = (array) $this->request->getPost('selected_ids');
        $notes   = trim((string) $this->request->getPost('notes')) ?: ($action === 'approve' ? 'Disetujui massal oleh Operator.' : 'Ditolak massal oleh Operator.');

        $ids = array_values(array_unique(array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0)));
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
        $failedCount = 0;
        $certCount = 0;

        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);

        foreach ($approvals as $app) {
            $appId = (int) $app['id'];

            // Status Guard: hanya proses yang berstatus pending
            if (($app['status'] ?? '') !== 'pending') {
                $failedCount++;
                continue;
            }

            // Anti Self-Approval: tolak jika pemohon sama dengan operator (kecuali super_administrator)
            if (! $isSuperAdmin && (int) ($app['requested_by'] ?? 0) === $actorId) {
                $failedCount++;
                continue;
            }

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

            try {
                $this->db->transStart();
                $this->db->table('approvals')
                    ->where('id', $appId)
                    ->where('status', 'pending')
                    ->update($updateData);

                if ($this->db->affectedRows() < 1) {
                    $this->db->transRollback();
                    $failedCount++;
                    continue;
                }

                if ($action === 'approve' && ($app['resource_type'] ?? '') === 'certificate' && ($app['requested_action'] ?? '') === 'issue_certificate') {
                    $generator = new \App\Services\CertificateGenerator();
                    $generator->generateFromApproval($appId, $actorId);
                    $certCount++;
                }

                $this->db->transComplete();

                if ($this->db->transStatus()) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Throwable $e) {
                $this->db->transRollback();
                $failedCount++;
                log_message('error', 'Bulk approval item failed for ID {appId}: {message}', [
                    'appId'   => $appId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $msg = "Berhasil memproses {$successCount} pengajuan approval ({$status}).";
        if ($failedCount > 0) {
            $msg .= " Terdapat {$failedCount} pengajuan dilewati (sudah diproses atau melanggar prinsip anti self-approval).";
        }
        if ($certCount > 0) {
            $msg .= " Serta berhasil menerbitkan {$certCount} sertifikat secara otomatis.";
        }

        return redirect()->back()->with('message', $msg);
    }

    public function approveApproval(string $id)
    {
        $actorId    = $this->assertAdminAccess();
        $approvalId = (int) $id;
        $notes      = (string) $this->request->getPost('notes') ?: 'Disetujui oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        // Status Guard
        if (($approval['status'] ?? '') !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan approval ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($approval['requested_by'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat menyetujui permohonan yang Anda ajukan sendiri.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $this->db->table('approvals')
            ->where('id', $approvalId)
            ->where('status', 'pending')
            ->update([
                'status'      => 'approved',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'notes'       => $notes,
                'updated_at'  => $now,
            ]);

        if ($this->db->affectedRows() < 1) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Pengajuan approval ini sudah pernah diproses atau tidak lagi berstatus pending.');
        }

        $certMessage = '';
        if (($approval['resource_type'] ?? '') === 'certificate' && ($approval['requested_action'] ?? '') === 'issue_certificate') {
            try {
                $generator = new \App\Services\CertificateGenerator();
                $result = $generator->generateFromApproval($approvalId, $actorId);
                $certMessage = ' dan Sertifikat #' . ($result['certificate_number'] ?? '') . ' berhasil diterbitkan.';
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Approval disetujui, namun pembuatan berkas sertifikat mengalami kendala: ' . $e->getMessage());
            }
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal memproses persetujuan approval.');
        }

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' berhasil disetujui' . $certMessage);
    }

    public function rejectApproval(string $id)
    {
        $actorId    = $this->assertAdminAccess();
        $approvalId = (int) $id;
        $reason     = trim((string) $this->request->getPost('reason')) ?: 'Pengajuan ditolak oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        // Status Guard
        if (($approval['status'] ?? '') !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan approval ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($approval['requested_by'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat memproses permohonan yang Anda ajukan sendiri.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $this->db->table('approvals')
            ->where('id', $approvalId)
            ->where('status', 'pending')
            ->update([
                'status'      => 'rejected',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'reason'      => $reason,
                'updated_at'  => $now,
            ]);

        if ($this->db->affectedRows() < 1) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Pengajuan approval ini sudah pernah diproses atau tidak lagi berstatus pending.');
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan approval.');
        }

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' telah ditolak.');
    }

    /**
     * 3. VERIFIKASI USER / PROFIL
     */
    public function profileVerifications()
    {
        $this->assertAdminAccess();

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
                ? '<input type="checkbox" name="selected_ids[]" value="' . $item['id'] . '" class="form-check-input ws-bulk-check" form="wsBulkActionForm">'
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
        $actorId = $this->assertAdminAccess();
        $action  = (string) $this->request->getPost('bulk_action');
        $rawIds  = (array) $this->request->getPost('selected_ids');
        $notes   = trim((string) $this->request->getPost('notes')) ?: ($action === 'approve' ? 'Diverifikasi massal oleh Operator.' : 'Ditolak massal oleh Operator.');

        $ids = array_values(array_unique(array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0)));
        if ($ids === []) {
            return redirect()->back()->with('error', 'Tidak ada verifikasi profil yang dipilih.');
        }

        if (! in_array($action, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Tindakan massal tidak valid.');
        }

        $service = new \App\Services\Admin\ProfileVerificationService();
        $successCount = 0;
        $failedCount = 0;

        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);

        foreach ($ids as $vid) {
            $ver = $this->db->table('profile_verifications')->where('id', $vid)->get()->getRowArray();
            if (! $ver || ($ver['status'] ?? '') !== 'pending') {
                $failedCount++;
                continue;
            }

            if (! $isSuperAdmin && (int) ($ver['user_id'] ?? 0) === $actorId) {
                $failedCount++;
                continue;
            }

            try {
                $this->db->transStart();
                $processed = false;
                if ($action === 'approve') {
                    $processed = $service->approve($vid, $actorId, $notes);
                } else {
                    $processed = $service->reject($vid, $notes, $actorId, $notes);
                }
                $this->db->transComplete();

                if ($processed && $this->db->transStatus()) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Throwable $e) {
                $this->db->transRollback();
                $failedCount++;
                log_message('error', 'Bulk profile verification failed for ID {vid}: {message}', [
                    'vid'     => $vid,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $statusLabel = ($action === 'approve' ? 'disetujui' : 'ditolak');
        $msg = "Berhasil memproses {$successCount} verifikasi profil ({$statusLabel}).";
        if ($failedCount > 0) {
            $msg .= " Sebanyak {$failedCount} pengajuan dilewati karena sudah diproses atau melanggar prinsip anti self-approval.";
        }

        return redirect()->back()->with('message', $msg);
    }

    public function approveVerification(string $id)
    {
        $actorId        = $this->assertAdminAccess();
        $verificationId = (int) $id;
        $notes          = (string) $this->request->getPost('notes');

        $ver = $this->db->table('profile_verifications')->where('id', $verificationId)->get()->getRowArray();
        if (! $ver) {
            return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
        }

        // Status Guard
        if (($ver['status'] ?? '') !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan verifikasi profil ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($ver['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat memverifikasi profil akun Anda sendiri.');
        }

        $this->db->transStart();
        $service = new \App\Services\Admin\ProfileVerificationService();
        $ok = $service->approve($verificationId, $actorId, $notes);
        if (! $ok) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Pengajuan verifikasi profil ini sudah pernah diproses atau tidak lagi berstatus pending.');
        }
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' berhasil disetujui.');
        }

        return redirect()->back()->with('error', 'Gagal memproses persetujuan verifikasi profil.');
    }

    public function rejectVerification(string $id)
    {
        $actorId        = $this->assertAdminAccess();
        $verificationId = (int) $id;
        $reason         = trim((string) $this->request->getPost('reason')) ?: 'Data profil tidak sesuai.';
        $notes          = (string) $this->request->getPost('notes');

        $ver = $this->db->table('profile_verifications')->where('id', $verificationId)->get()->getRowArray();
        if (! $ver) {
            return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
        }

        // Status Guard
        if (($ver['status'] ?? '') !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan verifikasi profil ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($ver['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat menolak verifikasi profil akun Anda sendiri.');
        }

        $this->db->transStart();
        $service = new \App\Services\Admin\ProfileVerificationService();
        try {
            $ok = $service->reject($verificationId, $reason, $actorId, $notes);
            if (! $ok) {
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Pengajuan verifikasi profil ini sudah pernah diproses atau tidak lagi berstatus pending.');
            }
            $this->db->transComplete();
            if ($this->db->transStatus()) {
                return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' telah ditolak.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal memproses penolakan verifikasi profil.');
    }

    /**
     * PERMOHONAN PERUBAHAN PROFIL
     * Verifikasi perubahan data profil mandiri pengguna melalui workspace/dashboard.
     */
    public function profileChangeRequests()
    {
        $this->assertAdminAccess();

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
        $actorId   = $this->assertAdminAccess();
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
        $actorId   = $this->assertAdminAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $notes     = (string) $this->request->getPost('notes');

        $req = $this->db->table('profile_change_requests')->where('id', $requestId)->get()->getRowArray();
        if (! $req) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil tidak ditemukan.');
        }

        // Status Guard
        if (! in_array($req['status'] ?? '', ['submitted', 'in_review'], true)) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($req['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat memverifikasi permohonan perubahan profil milik Anda sendiri.');
        }

        try {
            $this->db->transStart();
            (new \App\Services\Admin\ProfileChangeRequestService())->approve($requestId, $actorId, $notes);
            $this->db->transComplete();

            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Gagal menerapkan perubahan profil ke database.');
            }

            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permohonan perubahan profil #' . $requestId . ' berhasil disetujui dan diterapkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectProfileChangeRequest(string $id)
    {
        $actorId   = $this->assertAdminAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $reason    = trim((string) $this->request->getPost('reason')) ?: 'Permohonan perubahan profil ditolak oleh Operator.';
        $notes     = (string) $this->request->getPost('notes');

        $req = $this->db->table('profile_change_requests')->where('id', $requestId)->get()->getRowArray();
        if (! $req) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil tidak ditemukan.');
        }

        // Status Guard
        if (! in_array($req['status'] ?? '', ['submitted', 'in_review'], true)) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($req['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat menolak permohonan perubahan profil milik Anda sendiri.');
        }

        try {
            $this->db->transStart();
            (new \App\Services\Admin\ProfileChangeRequestService())->reject($requestId, $reason, $actorId, $notes);
            $this->db->transComplete();

            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Gagal memproses penolakan perubahan profil.');
            }

            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permohonan perubahan profil #' . $requestId . ' ditolak.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function revisionProfileChangeRequest(string $id)
    {
        $actorId   = $this->assertAdminAccess();
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $note      = trim((string) $this->request->getPost('revision_note'));

        if ($note === '') {
            return redirect()->back()->with('error', 'Catatan revisi wajib diisi.');
        }

        $req = $this->db->table('profile_change_requests')->where('id', $requestId)->get()->getRowArray();
        if (! $req) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil tidak ditemukan.');
        }

        // Status Guard
        if (! in_array($req['status'] ?? '', ['submitted', 'in_review'], true)) {
            return redirect()->back()->with('error', 'Permohonan perubahan profil ini sudah pernah diproses sebelumnya.');
        }

        // Anti Self-Approval
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($req['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat memproses permohonan milik Anda sendiri.');
        }

        try {
            $this->db->transStart();
            (new \App\Services\Admin\ProfileChangeRequestService())->requestRevision($requestId, $note, $actorId);
            $this->db->transComplete();

            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Gagal memproses permintaan revisi perubahan profil.');
            }

            return redirect()->to(route_to('workspace.operator.profile-change-requests'))
                ->with('message', 'Permintaan revisi perubahan profil #' . $requestId . ' berhasil dikirim ke pengguna.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * 4. KELAS & TRAINER ASSIGNMENT (Hierarki: Admin -> Operator -> Multi-Trainer)
     * Operator HANYA melihat dan mengelola kelas yang ditugaskan kepadanya.
     * Admin/Superadmin dapat menugaskan Operator dan/atau Multi-Trainer ke seluruh kelas.
     */
    public function classTrainers()
    {
        $actorId          = $this->assertOperatorAccess();
        $isAdmin          = $this->hasFullClassScope();
        $assignedClassIds = $this->getAssignedClassIds($actorId);

        $classBuilder = $this->db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id');

        if ($assignedClassIds !== null) {
            $classBuilder->whereIn('lc.id', $assignedClassIds);
        }

        $rawClasses = $classBuilder
            ->orderBy('lc.created_at', 'DESC')
            ->get()->getResultArray();

        $classes = [];
        foreach ($rawClasses as $c) {
            $cid = (int) $c['id'];
            $c['student_count'] = $this->db->table('enrollments')->where('class_id', $cid)->countAllResults();

            $allAssigned = $this->db->table('class_trainers ct')
                ->select('ct.user_id, ct.status, ct.role, ct.assigned_at, u.name, u.email')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.class_id', $cid)
                ->orderBy('ct.status', 'ASC')
                ->orderBy('u.name', 'ASC')
                ->get()->getResultArray();

            $c['operators'] = array_values(array_filter($allAssigned, static fn($row): bool => ($row['role'] ?? '') === 'operator'));
            $c['trainers']  = array_values(array_filter($allAssigned, static fn($row): bool => ($row['role'] ?? '') !== 'operator'));
            $classes[] = $c;
        }

        $rawTrainers = $this->db->table('users u')
            ->select('u.id, u.name, u.email, u.account_status')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('r.key', 'trainer')
            ->where('ur.status', 'active')
            ->where('u.deleted_at IS NULL')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        $trainers = [];
        foreach ($rawTrainers as $t) {
            $tid = (int) $t['id'];
            $t['active_classes_count'] = $this->db->table('class_trainers')
                ->where('user_id', $tid)
                ->where('status', 'active')
                ->where('role !=', 'operator')
                ->countAllResults();
            $trainers[] = $t;
        }

        $operators = [];
        if ($isAdmin) {
            $rawOperators = $this->db->table('users u')
                ->select('u.id, u.name, u.email, u.account_status')
                ->join('user_roles ur', 'ur.user_id = u.id')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'operator')
                ->where('ur.status', 'active')
                ->where('u.deleted_at IS NULL')
                ->orderBy('u.name', 'ASC')
                ->get()->getResultArray();

            foreach ($rawOperators as $op) {
                $oid = (int) $op['id'];
                $op['active_classes_count'] = $this->db->table('class_trainers')
                    ->where('user_id', $oid)
                    ->where('status', 'active')
                    ->where('role', 'operator')
                    ->countAllResults();
                $operators[] = $op;
            }
        }

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/class_trainers', [
            'title'          => 'Kelas & Penugasan Trainer | Operator BegawiPengadaan',
            'pageTitle'      => $isAdmin ? 'Hierarki Penugasan Kelas, Operator & Trainer' : 'Kelas Kelolaan & Penugasan Trainer',
            'breadcrumbTail' => [['label' => 'Kelas & Trainer Assignment']],
            'userName'       => $userName,
            'roles'          => (array) session()->get('roles'),
            'primaryRole'    => $isAdmin ? 'administrator' : 'operator',
            'roleLabel'      => $isAdmin ? 'Administrator' : 'Operator',
            'isAdmin'        => $isAdmin,
            'classes'        => $classes,
            'trainers'       => $trainers,
            'operators'      => $operators,
        ]);
    }

    public function assignTrainer()
    {
        $classId = (int) $this->request->getPost('class_id');
        if ($classId < 1) {
            return redirect()->back()->with('error', 'Pilih kelas pembelajaran terlebih dahulu.');
        }

        $actorId = $this->assertCanManageClass($classId);
        $isAdmin = $this->hasFullClassScope();

        $class = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if (! $class) {
            return redirect()->back()->with('error', 'Kelas pembelajaran tidak ditemukan.');
        }

        // Ambil daftar trainer_ids (mendukung single user_id maupun multi trainer_ids[])
        $rawTrainerIds = (array) ($this->request->getPost('trainer_ids') ?: []);
        $singleUserId  = (int) $this->request->getPost('user_id');
        if ($singleUserId > 0) {
            $rawTrainerIds[] = $singleUserId;
        }
        $trainerIds = array_values(array_unique(array_filter(array_map('intval', $rawTrainerIds), static fn($id) => $id > 0)));

        // Ambil daftar operator_ids (khusus Admin/Superadmin: mendukung penugasan Operator tersendiri atau bersamaan dengan Trainer)
        $rawOperatorIds = [];
        if ($isAdmin) {
            $rawOperatorIds = (array) ($this->request->getPost('operator_ids') ?: []);
            $singleOpId     = (int) $this->request->getPost('operator_id');
            if ($singleOpId > 0) {
                $rawOperatorIds[] = $singleOpId;
            }
        }
        $operatorIds = array_values(array_unique(array_filter(array_map('intval', $rawOperatorIds), static fn($id) => $id > 0)));

        if ($trainerIds === [] && $operatorIds === []) {
            return redirect()->back()->with('error', 'Pilih minimal satu Trainer atau Operator untuk ditugaskan ke kelas.');
        }

        $trainerRole = (string) ($this->request->getPost('trainer_role') ?: 'primary');
        if (! in_array($trainerRole, ['primary', 'co_trainer', 'guest'], true)) {
            $trainerRole = 'primary';
        }
        $replaceExisting = (bool) $this->request->getPost('replace_existing');

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $assignedCount = 0;

        // 1. Proses penugasan Operator (jika diinput oleh Admin/Superadmin)
        if ($isAdmin && $operatorIds !== []) {
            foreach ($operatorIds as $opId) {
                $isValidOp = $this->db->table('users u')
                    ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = \'active\'')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->where('u.id', $opId)
                    ->where('u.account_status', 'active')
                    ->where('u.deleted_at IS NULL')
                    ->where('r.key', 'operator')
                    ->countAllResults() > 0;

                if (! $isValidOp) {
                    continue;
                }

                $existingOp = $this->db->table('class_trainers')
                    ->where('class_id', $classId)
                    ->where('user_id', $opId)
                    ->get()->getRowArray();

                if ($existingOp) {
                    $this->db->table('class_trainers')
                        ->where('class_id', $classId)
                        ->where('user_id', $opId)
                        ->update([
                            'status'      => 'active',
                            'role'        => 'operator',
                            'assigned_by' => $actorId,
                            'assigned_at' => $now,
                        ]);
                } else {
                    $this->db->table('class_trainers')->insert([
                        'class_id'    => $classId,
                        'user_id'     => $opId,
                        'assigned_by' => $actorId,
                        'assigned_at' => $now,
                        'status'      => 'active',
                        'role'        => 'operator',
                    ]);
                }
                $assignedCount++;
                $this->logAudit($actorId, 'class_operator.assign', 'class_trainer', $classId, ['class_id' => $classId, 'operator_id' => $opId]);

                try {
                    (new \App\Services\NotificationService($this->db))->send(
                        $opId,
                        'class_assigned',
                        'Penugasan Pengelolaan Kelas Baru',
                        'Anda telah ditugaskan sebagai Operator Pengelola / Asisten Trainer pada kelas "' . ($class['name'] ?? '') . '".',
                        ['class_id' => $classId, 'url' => route_to('workspace.operator.class-trainers')]
                    );
                } catch (\Throwable) {
                }
            }
        }

        // 2. Proses penugasan Multi-Trainer
        if ($trainerIds !== []) {
            if ($replaceExisting) {
                $this->db->table('class_trainers')
                    ->where('class_id', $classId)
                    ->where('role !=', 'operator')
                    ->whereNotIn('user_id', $trainerIds)
                    ->update(['status' => 'revoked']);
            }

            foreach ($trainerIds as $idx => $tid) {
                $isTrainer = $this->db->table('users u')
                    ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = \'active\'')
                    ->join('roles r', 'r.id = ur.role_id')
                    ->where('u.id', $tid)
                    ->where('u.account_status', 'active')
                    ->where('u.deleted_at IS NULL')
                    ->where('r.key', 'trainer')
                    ->countAllResults() > 0;

                if (! $isTrainer) {
                    continue;
                }

                $roleToAssign = (count($trainerIds) > 1 && $idx > 0 && $trainerRole === 'primary') ? 'co_trainer' : $trainerRole;

                $existingTr = $this->db->table('class_trainers')
                    ->where('class_id', $classId)
                    ->where('user_id', $tid)
                    ->get()->getRowArray();

                if ($existingTr) {
                    $this->db->table('class_trainers')
                        ->where('class_id', $classId)
                        ->where('user_id', $tid)
                        ->update([
                            'status'      => 'active',
                            'role'        => $roleToAssign,
                            'assigned_by' => $actorId,
                            'assigned_at' => $now,
                        ]);
                } else {
                    $this->db->table('class_trainers')->insert([
                        'class_id'    => $classId,
                        'user_id'     => $tid,
                        'assigned_by' => $actorId,
                        'assigned_at' => $now,
                        'status'      => 'active',
                        'role'        => $roleToAssign,
                    ]);
                }
                $assignedCount++;
                $this->logAudit($actorId, 'class_trainer.assign', 'class_trainer', $classId, ['class_id' => $classId, 'user_id' => $tid, 'role' => $roleToAssign]);

                try {
                    (new \App\Services\NotificationService($this->db))->send(
                        $tid,
                        'class_assigned',
                        'Penugasan Pengajar Kelas Baru',
                        'Anda telah ditugaskan sebagai Pengajar (' . strtoupper($roleToAssign) . ') pada kelas "' . ($class['name'] ?? '') . '".',
                        ['class_id' => $classId, 'url' => route_to('workspace.trainer.class.detail', (string) $classId)]
                    );
                } catch (\Throwable) {
                }
            }
        }

        $this->db->transComplete();

        if (! $this->db->transStatus() || $assignedCount === 0) {
            return redirect()->back()->with('error', 'Penugasan gagal disimpan atau pengguna yang dipilih tidak memiliki role aktif yang sesuai.');
        }

        return redirect()->back()->with('message', 'Berhasil menyimpan ' . $assignedCount . ' penugasan pada kelas ' . esc($class['name']) . '.');
    }

    public function revokeTrainer()
    {
        $classId = (int) $this->request->getPost('class_id');
        $userId  = (int) $this->request->getPost('user_id');

        if ($classId < 1 || $userId < 1) {
            return redirect()->back()->with('error', 'Parameter penugasan tidak valid.');
        }

        $actorId = $this->assertCanManageClass($classId);
        $targetRow = $this->db->table('class_trainers')
            ->where('class_id', $classId)
            ->where('user_id', $userId)
            ->get()->getRowArray();

        if ($targetRow && ($targetRow['role'] ?? '') === 'operator' && ! $this->hasFullClassScope()) {
            return redirect()->back()->with('error', 'Hanya Administrator / Admin Pembelajaran yang dapat mencabut penugasan Operator pada kelas.');
        }

        $this->db->table('class_trainers')
            ->where('class_id', $classId)
            ->where('user_id', $userId)
            ->update([
                'status' => 'revoked',
            ]);

        $this->logAudit($actorId, 'class_trainer.revoke', 'class_trainer', $classId, ['class_id' => $classId, 'user_id' => $userId]);

        return redirect()->back()->with('message', 'Penugasan pada kelas ini berhasil dicabut.');
    }

    /**
     * 5. OPERASIONAL EVENT
     */
    public function events()
    {
        $actorId          = $this->assertOperatorAccess();
        $assignedEventIds = $this->getAssignedEventIds($actorId);

        $eventBuilder = $this->db->table('events')->where('deleted_at IS NULL');
        if ($assignedEventIds !== null) {
            if ($assignedEventIds === []) {
                $eventBuilder->where('1', '0');
            } else {
                $eventBuilder->whereIn('id', $assignedEventIds);
            }
        }

        $published = (clone $eventBuilder)->whereIn('status', ['open_registration', 'coming_soon', 'published'])->countAllResults();
        $draft     = (clone $eventBuilder)->where('status', 'draft')->countAllResults();
        $ongoing   = (clone $eventBuilder)->whereIn('status', ['open_registration', 'ongoing'])->countAllResults();

        $list = (clone $eventBuilder)
            ->select('id, title, type, mode, status, start_at, capacity')
            ->orderBy('created_at', 'DESC')
            ->limit(50)
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
        $actorId = $this->assertOperatorAccess();
        $eid     = (int) $eventId;
        $this->assertCanManageEvent($eid);

        $event = $this->db->table('events')->where('id', $eid)->get()->getRowArray();
        if (! $event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $this->assertCanManageEvent($eid);

        $participants = $this->db->table('event_registrations er')
            ->select('er.id, er.user_id, er.registered_at, er.status, u.name AS user_name, u.email AS user_email, u.username, a.id AS attendance_id, a.status AS attendance_status, a.recorded_at AS attendance_recorded_at,
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

        $trainerUserIds = [];
        if ($participants !== []) {
            $userIds = array_values(array_unique(array_map(static fn($p) => (int) $p['user_id'], $participants)));
            $trRows = $this->db->table('user_roles ur')
                ->select('ur.user_id')
                ->join('roles r', 'r.id = ur.role_id')
                ->whereIn('ur.user_id', $userIds)
                ->where('r.key', 'trainer')
                ->where('ur.status', 'active')
                ->get()->getResultArray();
            $trainerUserIds = array_map(static fn($r) => (int) $r['user_id'], $trRows);
        }

        foreach ($participants as &$p) {
            $p['institution'] = $p['asn_instansi'] ?: ($p['prov_instansi'] ?: ($p['acad_instansi'] ?: ($p['pub_instansi'] ?: '-')));
            $p['position']    = $p['asn_jabatan'] ?: ($p['prov_jabatan'] ?: ($p['acad_jabatan'] ?: '-'));
            $p['nip']         = $p['asn_nip'] ?: ($p['username'] ?: '-');
            $p['role_badge']  = in_array((int) ($p['user_id'] ?? 0), $trainerUserIds, true) ? 'Trainer' : 'Peserta';
        }
        unset($p);

        $totalRegistered = count($participants);
        $pendingApproval = count(array_filter($participants, static fn($p): bool => in_array((string) ($p['status'] ?? ''), ['waitlisted', 'waitlist', 'waiting', 'pending'], true)));
        $approvedCount   = count(array_filter($participants, static fn($p): bool => in_array((string) ($p['status'] ?? ''), ['registered', 'confirmed', 'attended', 'present', 'completed', 'promoted'], true)));
        $presentCount    = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'present'));
        $excusedCount    = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'excused'));
        $absentCount     = count(array_filter($participants, static fn($p): bool => ($p['attendance_status'] ?? '') === 'absent'));
        $unrecordedCount = count(array_filter($participants, static fn($p): bool => empty($p['attendance_status'])));
        $attendanceRate  = $totalRegistered > 0 ? round(($presentCount / $totalRegistered) * 100, 1) : 0.0;

        $metrics = [
            'total'            => $totalRegistered,
            'pending_approval' => $pendingApproval,
            'approved'         => $approvedCount,
            'present'          => $presentCount,
            'excused'          => $excusedCount,
            'absent'           => $absentCount,
            'unrecorded'       => $unrecordedCount,
            'attendance_rate'  => $attendanceRate,
            'quota'            => (int) ($event['quota'] ?? $event['capacity'] ?? 0),
        ];

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/event_participants', [
            'title'             => 'Peserta Event: ' . esc($event['title']) . ' | Operator BegawiPengadaan',
            'pageTitle'         => 'Peserta & Presensi Event',
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
     * PERSETUJUAN PENDAFTARAN EVENT (APPROVAL REGISTRASI PESERTA / TRAINER)
     */
    public function approveEventRegistration(string $regId)
    {
        $actorId = $this->assertOperatorAccess();
        $rid     = (int) $regId;

        $reg = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.id', $rid)
            ->get()->getRowArray();

        if (! $reg) {
            return redirect()->back()->with('error', 'Pendaftaran event tidak ditemukan.');
        }

        $this->assertCanManageEvent((int) $reg['event_id']);

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $this->db->table('event_registrations')->where('id', $rid)->update([
            'status'        => 'registered',
            'promoted_at'   => $now,
            'status_reason' => 'approved',
            'updated_at'    => $now,
        ]);

        $this->logAudit($actorId, 'event_registration.approve', 'event_registration', $rid, [
            'event_id' => $reg['event_id'],
            'user_id'  => $reg['user_id'],
            'status'   => 'registered',
        ]);

        try {
            (new \App\Services\NotificationService($this->db))->send(
                (int) $reg['user_id'],
                'event_registration_approved',
                'Pendaftaran Event Disetujui',
                'Pendaftaran Anda pada event "' . ($reg['event_title'] ?? '') . '" telah disetujui (Confirmed). Anda kini dapat mengakses ruang kegiatan event.',
                ['event_id' => (int) $reg['event_id'], 'url' => route_to('workspace.participant.event.detail', (string) $reg['event_id'])]
            );
        } catch (\Throwable) {
        }

        $this->db->transComplete();

        return redirect()->back()->with('message', 'Pendaftaran peserta pada event berhasil disetujui (Approved).');
    }

    public function rejectEventRegistration(string $regId)
    {
        $actorId = $this->assertOperatorAccess();
        $rid     = (int) $regId;
        $reason  = trim((string) $this->request->getPost('reason')) ?: 'Ditolak/dibatalkan oleh Panitia Event.';

        $reg = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.id', $rid)
            ->get()->getRowArray();

        if (! $reg) {
            return redirect()->back()->with('error', 'Pendaftaran event tidak ditemukan.');
        }

        $this->assertCanManageEvent((int) $reg['event_id']);

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $this->db->table('event_registrations')->where('id', $rid)->update([
            'status'       => 'cancelled',
            'cancelled_at' => $now,
            'updated_at'   => $now,
        ]);

        $this->logAudit($actorId, 'event_registration.reject', 'event_registration', $rid, [
            'event_id' => $reg['event_id'],
            'user_id'  => $reg['user_id'],
            'status'   => 'cancelled',
        ], $reason);

        try {
            (new \App\Services\NotificationService($this->db))->send(
                (int) $reg['user_id'],
                'event_registration_rejected',
                'Pendaftaran Event Ditolak / Dibatalkan',
                'Pendaftaran Anda pada event "' . ($reg['event_title'] ?? '') . '" tidak disetujui. Catatan: ' . $reason,
                ['event_id' => (int) $reg['event_id'], 'url' => route_to('workspace.participant.events')]
            );
        } catch (\Throwable) {
        }

        $this->db->transComplete();

        return redirect()->back()->with('message', 'Pendaftaran event telah ditolak/dibatalkan.');
    }

    public function bulkEventRegistration(string $eventId)
    {
        $actorId = $this->assertOperatorAccess();
        $eid     = (int) $eventId;
        $action  = (string) ($this->request->getPost('bulk_action') ?: 'approve_all_pending');

        $event = $this->db->table('events')->where('id', $eid)->get()->getRowArray();
        if (! $event) {
            return redirect()->back()->with('error', 'Event tidak ditemukan.');
        }

        $this->assertCanManageEvent($eid);

        $pendingRows = $this->db->table('event_registrations')
            ->where('event_id', $eid)
            ->whereIn('status', ['waitlisted', 'waitlist', 'waiting', 'pending'])
            ->get()->getResultArray();

        if ($pendingRows === []) {
            return redirect()->back()->with('info', 'Tidak ada pendaftaran yang berstatus Menunggu Persetujuan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $notifService = new \App\Services\NotificationService($this->db);
        $count = 0;
        foreach ($pendingRows as $row) {
            $rid = (int) $row['id'];
            if ($action === 'approve_all_pending') {
                $this->db->table('event_registrations')->where('id', $rid)->update([
                    'status'        => 'registered',
                    'promoted_at'   => $now,
                    'status_reason' => 'approved',
                    'updated_at'    => $now,
                ]);
                try {
                    $notifService->send(
                        (int) $row['user_id'],
                        'event_registration_approved',
                        'Pendaftaran Event Disetujui',
                        'Pendaftaran Anda pada event "' . ($event['title'] ?? '') . '" telah disetujui.',
                        ['event_id' => $eid, 'url' => route_to('workspace.participant.event.detail', (string) $eid)]
                    );
                } catch (\Throwable) {
                }
                $count++;
            }
        }

        $this->logAudit($actorId, 'event_registration.bulk_approve', 'event', $eid, ['approved_count' => $count]);
        $this->db->transComplete();

        return redirect()->back()->with('message', 'Berhasil menyetujui ' . $count . ' pendaftaran event sekaligus.');
    }

    public function recordEventAttendance(string $regId)
    {
        $actorId = $this->assertOperatorAccess();
        $rid     = (int) $regId;
        $status  = (string) ($this->request->getPost('status') ?: 'present');
        $notes   = trim((string) $this->request->getPost('notes'));

        if (! in_array($status, ['present', 'excused', 'absent', 'late'], true)) {
            $status = 'present';
        }

        $reg = $this->db->table('event_registrations')->where('id', $rid)->get()->getRowArray();
        if (! $reg) {
            return redirect()->back()->with('error', 'Data registrasi peserta event tidak ditemukan.');
        }

        $this->assertCanManageEvent((int) $reg['event_id']);

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        // Jika registrasi masih pending/waitlist tetapi langsung dicatat hadir, otomatis konfirmasi registrasinya
        if ($status === 'present' && in_array((string) ($reg['status'] ?? ''), ['waitlisted', 'waitlist', 'waiting', 'pending'], true)) {
            $this->db->table('event_registrations')->where('id', $rid)->update([
                'status'        => 'registered',
                'promoted_at'   => $now,
                'status_reason' => 'present',
                'updated_at'    => $now,
            ]);
        }

        $existingAtt = $this->db->table('attendances')
            ->where('event_registration_id', $rid)
            ->get()->getRowArray();

        if ($existingAtt) {
            $this->db->table('attendances')->where('id', (int) $existingAtt['id'])->update([
                'status'      => $status,
                'notes'       => $notes ?: ($existingAtt['notes'] ?? 'Dicatat oleh Operator'),
                'recorded_by' => $actorId,
                'recorded_at' => $now,
                'updated_at'  => $now,
            ]);
            $aid = (int) $existingAtt['id'];
        } else {
            $this->db->table('attendances')->insert([
                'event_registration_id' => $rid,
                'status'                => $status,
                'notes'                 => $notes ?: 'Dicatat oleh Operator',
                'recorded_by'           => $actorId,
                'recorded_at'           => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            $aid = (int) $this->db->insertID();
        }

        $this->logAudit($actorId, 'attendance.operator_record', 'attendance', $aid, [
            'event_registration_id' => $rid,
            'status'                => $status,
        ], $notes);

        $this->db->transComplete();

        return redirect()->back()->with('message', 'Status presensi kehadiran peserta berhasil disimpan (' . strtoupper($status) . ').');
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

        $this->assertCanManageEvent($eid);

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
        $actorId          = $this->assertOperatorAccess();
        $assignedEventIds = $this->getAssignedEventIds($actorId);

        $attBuilder = $this->db->table('attendances a')
            ->join('event_registrations er', 'er.id = a.event_registration_id')
            ->join('events ev', 'ev.id = er.event_id')
            ->join('users u', 'u.id = er.user_id');

        if ($assignedEventIds !== null) {
            if ($assignedEventIds === []) {
                $attBuilder->where('1', '0');
            } else {
                $attBuilder->whereIn('ev.id', $assignedEventIds);
            }
        }

        $recorded   = (clone $attBuilder)->countAllResults();
        $regBuilder = $this->db->table('event_registrations er');
        if ($assignedEventIds !== null) {
            if ($assignedEventIds === []) {
                $regBuilder->where('1', '0');
            } else {
                $regBuilder->whereIn('er.event_id', $assignedEventIds);
            }
        }
        $registrations = $regBuilder->countAllResults();

        $list = (clone $attBuilder)
            ->select('a.id, a.status, a.recorded_at, a.notes, ev.title AS event_title, u.name AS user_name, u.email AS user_email')
            ->orderBy('a.created_at', 'DESC')
            ->limit(50)
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

        $allowedStatuses = ['present', 'absent', 'excused', 'late'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'present';
        }

        $attendance = $this->db->table('attendances a')
            ->select('a.*, er.event_id, er.user_id AS participant_id, e.status AS event_status')
            ->join('event_registrations er', 'er.id = a.event_registration_id', 'left')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->where('a.id', $aid)
            ->get()->getRowArray();

        if (! $attendance) {
            return redirect()->back()->with('error', 'Catatan presensi tidak ditemukan.');
        }

        if (empty($attendance['event_registration_id']) || empty($attendance['event_id'])) {
            return redirect()->back()->with('error', 'Data presensi tidak terhubung ke registrasi event yang sah.');
        }

        $this->assertCanManageEvent((int) $attendance['event_id']);

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $this->db->table('attendances')->where('id', $aid)->update([
            'status'      => $status,
            'notes'       => $notes,
            'recorded_by' => $actorId,
            'recorded_at' => $now,
            'updated_at'  => $now,
        ]);

        $this->logAudit($actorId, 'attendance.operator_update', 'attendance', $aid, ['attendance_id' => $aid, 'status' => $status], $notes);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal memperbarui catatan presensi.');
        }

        return redirect()->back()->with('message', 'Catatan presensi berhasil diperbarui.');
    }

    /**
     * PENUGASAN TRAINER / NARASUMBER KE EVENT
     * Operator yang ditugaskan ke event berhak menugaskan Trainer/Narasumber ke event tersebut.
     */
    public function assignEventTrainer()
    {
        $actorId   = $this->assertOperatorAccess();
        $eventId   = (int) $this->request->getPost('event_id');
        $trainerId = (int) $this->request->getPost('trainer_id');
        $roleLabel = trim((string) ($this->request->getPost('role_label') ?: 'Narasumber / Instruktur'));

        if ($eventId < 1 || $trainerId < 1) {
            return redirect()->back()->with('error', 'Pilih event dan trainer yang valid.');
        }

        $this->assertCanManageEvent($eventId);

        $event = $this->db->table('events')->where('id', $eventId)->get()->getRowArray();
        if (! $event) {
            return redirect()->back()->with('error', 'Event tidak ditemukan.');
        }

        $trainer = $this->db->table('users u')
            ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = \'active\'')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('u.id', $trainerId)
            ->where('r.key', 'trainer')
            ->where('u.account_status', 'active')
            ->where('u.deleted_at IS NULL')
            ->select('u.id, u.name, u.email')
            ->get()->getRowArray();

        if (! $trainer) {
            return redirect()->back()->with('error', 'Trainer tidak valid atau akun tidak aktif.');
        }

        $now = date('Y-m-d H:i:s');
        $existing = $this->db->table('event_registrations')
            ->where('event_id', $eventId)
            ->where('user_id', $trainerId)
            ->get()->getRowArray();

        if ($existing) {
            $this->db->table('event_registrations')
                ->where('id', $existing['id'])
                ->update([
                    'status'        => 'registered',
                    'status_reason' => 'assigned_trainer: ' . $roleLabel,
                    'updated_at'    => $now,
                ]);
        } else {
            $this->db->table('event_registrations')->insert([
                'event_id'      => $eventId,
                'user_id'       => $trainerId,
                'status'        => 'registered',
                'status_reason' => 'assigned_trainer: ' . $roleLabel,
                'registered_at' => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        $this->logAudit($actorId, 'event.assign_trainer', 'events', $eventId, [
            'trainer_id'   => $trainerId,
            'trainer_name' => $trainer['name'],
            'role_label'   => $roleLabel,
        ]);

        return redirect()->back()->with('message', "Trainer {$trainer['name']} berhasil ditugaskan sebagai {$roleLabel} pada event ini.");
    }

    /**
     * 7. MONITORING PEMBELAJARAN
     */
    public function learningMonitoring()
    {
        $this->assertOperatorAccess();
        $assignedClassIds = $this->getAssignedClassIds();

        $classesBuilder = $this->db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id')
            ->orderBy('lc.created_at', 'DESC');

        if ($assignedClassIds !== null) {
            if ($assignedClassIds === []) {
                $classesBuilder->where('1', '0');
            } else {
                $classesBuilder->whereIn('lc.id', $assignedClassIds);
            }
        }

        $classes = $classesBuilder->get()->getResultArray();

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
        $assignedClassIds = $this->getAssignedClassIds();

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

        if ($assignedClassIds !== null) {
            if ($assignedClassIds === []) {
                $pendingBuilder->where('1', '0');
            } else {
                $pendingBuilder->whereIn('e.class_id', $assignedClassIds);
            }
        }

        if (! empty($issuedSourceIds)) {
            $pendingBuilder->whereNotIn('e.id', $issuedSourceIds);
        }

        $pendingQueue = $pendingBuilder->orderBy('e.certificate_eligible_at', 'DESC')->get()->getResultArray();

        $issuedCertBuilder = $this->db->table('certificates cert')
            ->select('cert.id, cert.certificate_number, cert.verification_token_hash, cert.status, cert.issued_at, u.name AS user_name, u.email AS user_email, issuer.name AS issuer_name')
            ->join('users u', 'u.id = cert.user_id')
            ->join('users issuer', 'issuer.id = cert.issued_by', 'left');

        if ($assignedClassIds !== null) {
            $issuedCertBuilder->join('enrollments e_cert', "e_cert.id = cert.source_id AND cert.source_type = 'learning_enrollment'", 'left');
            if ($assignedClassIds === []) {
                $issuedCertBuilder->where('1', '0');
            } else {
                $issuedCertBuilder->whereIn('e_cert.class_id', $assignedClassIds);
            }
        }

        $issuedCertificates = $issuedCertBuilder
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
        $actorId       = $this->assertOperatorAccess();
        $eid           = (int) $enrollmentId;
        $justification = trim((string) $this->request->getPost('justification'));

        try {
            $certNumber = $this->issueSingleCertificate($eid, $actorId, $justification ?: null);
            if (! $certNumber) {
                return redirect()->back()->with('error', 'Enrollment peserta tidak ditemukan.');
            }
            return redirect()->back()->with('message', 'Sertifikat #' . $certNumber . ' berhasil diterbitkan dan status kelulusan peserta telah diverifikasi.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * TINDAKAN MASSAL (BULK ACTION): PENERBITAN SERTIFIKAT
     */
    public function bulkIssueCertificates()
    {
        $actorId       = $this->assertOperatorAccess();
        $rawIds        = (array) $this->request->getPost('enrollment_ids');
        $justification = trim((string) $this->request->getPost('justification'));

        $ids = array_filter(array_map('intval', $rawIds), static fn($id) => $id > 0);
        if ($ids === []) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih untuk penerbitan sertifikat.');
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($ids as $eid) {
            try {
                $certNum = $this->issueSingleCertificate($eid, $actorId, $justification ?: null);
                if ($certNum !== null) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Throwable $e) {
                $failedCount++;
                log_message('error', 'Bulk issue certificate failed for enrollment #{eid}: {message}', [
                    'eid'     => $eid,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $msg = "Berhasil menerbitkan {$successCount} sertifikat kelulusan.";
        if ($failedCount > 0) {
            $msg .= " Sebanyak {$failedCount} peserta dilewati karena belum memenuhi syarat kelulusan atau data tidak valid.";
        }

        return redirect()->back()->with('message', $msg);
    }

    /**
     * HELPER LOGIKA PENERBITAN SERTIFIKAT TUNGGAL
     * H-2: Evaluasi kelayakan kelulusan & catat justifikasi override jika belum memenuhi syarat
     */
    private function issueSingleCertificate(int $eid, int $actorId, ?string $justification = null): ?string
    {
        $enrollment = $this->db->table('enrollments')->where('id', $eid)->get()->getRowArray();
        if (! $enrollment) {
            return null;
        }

        $this->assertCanManageClass((int) $enrollment['class_id']);

        // H-2: Panggil evaluasi kelayakan riil via LearningService
        try {
            $learningService = new \App\Services\LearningService($this->db);
            $learningService->evaluateCompletion($eid);
        } catch (\Throwable $e) {
            log_message('warning', 'Evaluasi kelayakan sertifikat enrollment #{eid} gagal: {message}', [
                'eid'     => $eid,
                'message' => $e->getMessage(),
            ]);
        }

        // Ambil data enrollment terbaru setelah evaluasi
        $freshEnrollment = $this->db->table('enrollments')->where('id', $eid)->get()->getRowArray();
        $isEligible = ! empty($freshEnrollment['certificate_eligible_at']) && ($freshEnrollment['status'] ?? '') === 'completed';

        $isOverride = false;
        if (! $isEligible) {
            // Anti self-issuance: non-admin operators cannot issue override certificates to themselves
            $roles = (array) session()->get('roles');
            $isAdmin = ! empty(array_intersect(['administrator', 'super_administrator'], $roles));
            if (! $isAdmin && (int) ($freshEnrollment['user_id'] ?? $enrollment['user_id']) === $actorId) {
                throw new \RuntimeException('Operator tidak diizinkan menerbitkan sertifikat override untuk diri sendiri.');
            }

            // H-2: Jika belum memenuhi syarat kelulusan, wajib ada justifikasi override minimal 10 karakter
            if ($justification === null || mb_strlen($justification) < 10) {
                throw new \RuntimeException("Peserta #{$eid} belum memenuhi syarat kelulusan kelas. Penerbitan hanya diizinkan dengan justifikasi override resmi (minimal 10 karakter).");
            }
            $isOverride = true;
        }

        $now = date('Y-m-d H:i:s');

        // Pastikan status enrollment completed terlebih dahulu
        $this->db->table('enrollments')->where('id', $eid)->update([
            'status'                  => 'completed',
            'completed_at'            => $freshEnrollment['completed_at'] ?? $now,
            'passed_at'               => $freshEnrollment['passed_at'] ?? $now,
            'certificate_eligible_at' => $freshEnrollment['certificate_eligible_at'] ?? $now,
            'updated_at'              => $now,
        ]);

        $existing = $this->db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('source_id', $eid)
            ->get()->getRowArray();

        $generator = new \App\Services\CertificateGenerator();
        if ($existing) {
            $this->db->table('certificates')->where('id', $existing['id'])->update([
                'status'     => 'issued',
                'issued_at'  => $now,
                'issued_by'  => $actorId,
                'updated_at' => $now,
            ]);
            if (empty($existing['pdf_file_id'])) {
                $regen = $generator->regeneratePdfForCertificate((int) $existing['id'], $actorId, true);
                $certNumber = $regen['certificate_number'];
            } else {
                $certNumber = $existing['certificate_number'];
            }
        } else {
            $issued = $generator->generateDirect('learning_enrollment', $eid, $actorId);
            $certNumber = $issued['certificate_number'];
        }

        $auditPayload = [
            'enrollment_id'        => $eid,
            'certificate_number'   => $certNumber,
            'is_override'          => $isOverride,
            'requires_admin_audit' => $isOverride,
            'eligibility_snapshot' => [
                'final_score' => $freshEnrollment['final_score'],
                'status'      => $freshEnrollment['status'],
            ],
        ];

        $this->logAudit(
            $actorId,
            $isOverride ? 'certificate.operator_override_issue' : 'certificate.operator_issue',
            'certificate',
            $eid,
            $auditPayload,
            $justification
        );

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
        $this->db->transStart();

        $this->db->table('certificates')->where('id', $cid)->update([
            'status'            => 'revoked',
            'revoked_at'        => $now,
            'revoked_by'        => $actorId,
            'revocation_reason' => $reason,
            'updated_at'        => $now,
        ]);

        $this->logAudit($actorId, 'certificate.operator_revoke', 'certificate', $cid, ['certificate_id' => $cid], $reason);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal mencabut sertifikat.');
        }

        return redirect()->back()->with('message', 'Sertifikat #' . ($cert['certificate_number'] ?? $cid) . ' telah berhasil dicabut/dibatalkan.');
    }

    /**
     * 9. EVALUASI PEMBELAJARAN & OVERRIDE
     */
    public function evaluations()
    {
        $this->assertOperatorAccess();
        $assignedClassIds = $this->getAssignedClassIds();

        $applyClassScope = function ($builder) use ($assignedClassIds) {
            if ($assignedClassIds !== null) {
                $builder->join('assignments a_sc', 'a_sc.id = assignment_submissions.assignment_id');
                if ($assignedClassIds === []) {
                    $builder->where('1', '0');
                } else {
                    $builder->whereIn('a_sc.class_id', $assignedClassIds);
                }
            }
            return $builder;
        };

        $pendingReviews   = $applyClassScope($this->db->table('assignment_submissions')->whereIn('assignment_submissions.status', ['submitted', 'in_review']))->countAllResults();
        $gradedCount      = $applyClassScope($this->db->table('assignment_submissions')->where('assignment_submissions.status', 'graded'))->countAllResults();
        $revisionCount    = $applyClassScope($this->db->table('assignment_submissions')->where('assignment_submissions.status', 'revision_required'))->countAllResults();
        $totalSubmissions = $applyClassScope($this->db->table('assignment_submissions'))->countAllResults();

        $listBuilder = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users grader', 'grader.id = s.graded_by', 'left');

        if ($assignedClassIds !== null) {
            if ($assignedClassIds === []) {
                $listBuilder->where('1', '0');
            } else {
                $listBuilder->whereIn('a.class_id', $assignedClassIds);
            }
        }

        $list = $listBuilder
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
            ->select('s.*, a.class_id, a.title AS assignment_title, a.instructions AS assignment_instructions, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
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

        $this->assertCanManageClass((int) $submission['class_id']);

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
            ->select('s.*, a.class_id, e.user_id AS participant_user_id')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id', 'left')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            return redirect()->back()->with('error', 'Submission tugas tidak ditemukan.');
        }

        $this->assertCanManageClass((int) $submission['class_id']);

        // Anti Self-Override
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($submission['participant_user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat meng-override penilaian tugas milik Anda sendiri.');
        }

        if (! in_array($status, ['graded', 'revision_required', 'in_review'], true)) {
            $status = 'graded';
        }
        $score = max(0.0, min(100.0, $score));

        $now = date('Y-m-d H:i:s');
        $existingFeedback = trim((string) ($submission['feedback'] ?? ''));
        $overrideTag = "[OVERRIDE OPERATOR " . date('d/m/Y H:i') . "]: " . $reason;
        $updatedFeedback = $existingFeedback !== '' ? ($existingFeedback . "\n\n" . $overrideTag) : $overrideTag;

        $this->db->transStart();
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
                log_message('error', 'evaluateCompletion failed on override: {message}', ['message' => $e->getMessage()]);
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

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan override penilaian tugas.');
        }

        return redirect()->to(route_to('workspace.operator.evaluations'))
            ->with('message', 'Penilaian submission #' . $submissionId . ' berhasil di-override oleh Operator (Nilai: ' . $score . ', Status: ' . $status . ').');
    }

    public function overrideEnrollmentScore(string $enrollmentId)
    {
        $actorId = $this->assertOperatorAccess();
        $eid     = (int) $enrollmentId;
        $score   = (float) $this->request->getPost('final_score');
        $status  = (string) $this->request->getPost('status') ?: 'completed';
        $reason  = trim((string) $this->request->getPost('reason'));

        // H-3: Wajibkan input alasan/justifikasi minimal 10 karakter
        if (mb_strlen($reason) < 10) {
            return redirect()->back()->with('error', 'Alasan / justifikasi override nilai wajib diisi minimal 10 karakter.');
        }

        $enrollment = $this->db->table('enrollments')->where('id', $eid)->get()->getRowArray();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'Enrollment tidak ditemukan.');
        }

        $this->assertCanManageClass((int) $enrollment['class_id']);

        // H-3: Cegah operator meng-override nilai miliknya sendiri (Anti Self-Override)
        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_administrator', $roles, true);
        if (! $isSuperAdmin && (int) ($enrollment['user_id'] ?? 0) === $actorId) {
            return redirect()->back()->with('error', 'Sesuai prinsip Segregation of Duties, Anda tidak dapat mengubah/meng-override nilai milik Anda sendiri.');
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

        $this->db->transStart();
        $this->db->table('enrollments')->where('id', $eid)->update($updateData);

        $this->logAudit(
            $actorId,
            'enrollment.operator_override',
            'enrollment',
            $eid,
            [
                'old_score'  => $enrollment['final_score'],
                'new_score'  => $score,
                'old_status' => $enrollment['status'],
                'new_status' => $status,
            ],
            $reason
        );

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan override nilai ke database.');
        }

        // H-3: Evaluasi kelulusan & kelayakan sertifikat secara otomatis setelah override nilai
        try {
            $learningService = new \App\Services\LearningService($this->db);
            $learningService->evaluateCompletion($eid);
        } catch (\Throwable $e) {
            log_message('warning', 'Evaluasi otomatis completion setelah override nilai enrollment #{eid} gagal: {message}', [
                'eid'     => $eid,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('message', 'Nilai akhir dan status kelulusan enrollment #' . $eid . ' berhasil di-override oleh Operator.');
    }

    /**
     * 10. AKTIVITAS / AUDIT TRAIL MONITORING
     */
    public function activityMonitoring()
    {
        $this->assertAdminAccess();

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
        $assignedClassIds = $this->getAssignedClassIds();

        $applyEnrollmentScope = function ($builder, string $col = 'class_id') use ($assignedClassIds) {
            if ($assignedClassIds !== null) {
                if ($assignedClassIds === []) {
                    $builder->where('1', '0');
                } else {
                    $builder->whereIn($col, $assignedClassIds);
                }
            }
            return $builder;
        };

        $pending   = $applyEnrollmentScope($this->db->table('enrollments')->where('status', 'pending'))->countAllResults();
        $active    = $applyEnrollmentScope($this->db->table('enrollments')->where('status', 'active'))->countAllResults();
        $completed = $applyEnrollmentScope($this->db->table('enrollments')->where('status', 'completed'))->countAllResults();

        $listBuilder = $this->db->table('enrollments e')
            ->select('e.id, e.status, e.enrolled_at, e.final_score, u.name, u.email, lc.name AS class_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id');

        $applyEnrollmentScope($listBuilder, 'e.class_id');

        $list = $listBuilder
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
        $enrollment = $this->db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'Enrollment tidak ditemukan.');
        }
        $this->assertCanManageClass((int) $enrollment['class_id']);

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
