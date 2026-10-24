<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\Admin\RoleRequestService;
use App\Services\NotificationService;

class RoleRequestController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = (int) session()->get('user_id');
        $db     = db_connect();

        // Daftar pengajuan role milik pengguna ini
        $myRequests = $db->table('role_requests rr')
            ->select('rr.*, r.name AS role_name, r.key AS role_key, rev.name AS reviewer_name')
            ->join('roles r', 'r.id = rr.requested_role_id', 'left')
            ->join('users rev', 'rev.id = rr.reviewed_by', 'left')
            ->where('rr.user_id', $userId)
            ->orderBy('rr.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Daftar role yang dapat diajukan oleh pengguna (Trainer / Operator)
        $availableRoles = $db->table('roles')
            ->whereIn('key', RoleRequestService::ALLOWED_SELF_REQUEST_ROLES)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        // Role yang sudah dimiliki saat ini
        $currentRoles = $db->table('user_roles ur')
            ->select('r.id, r.key, r.name, ur.status')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.status', 'active')
            ->get()
            ->getResultArray();

        return view('main/workspace/pages/role_requests', [
            'title'          => 'Pengajuan Peran Tambahan | Workspace BegawiPengadaan',
            'pageTitle'      => 'Pengajuan Peran Tambahan',
            'breadcrumbTail' => [
                ['label' => 'Profil Saya', 'url' => route_to('workspace.profile.index')],
                ['label' => 'Pengajuan Peran'],
            ],
            'myRequests'     => $myRequests,
            'availableRoles' => $availableRoles,
            'currentRoles'   => $currentRoles,
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId          = (int) session()->get('user_id');
        $requestedRoleId = (int) $this->request->getPost('requested_role_id');
        $reason          = trim((string) $this->request->getPost('reason'));

        if ($requestedRoleId < 1 || mb_strlen($reason) < 10) {
            return redirect()->back()->withInput()
                ->with('error', 'Pilih peran yang diajukan dan sertakan alasan/kompetensi pendukung minimal 10 karakter.');
        }

        $db   = db_connect();
        $role = $db->table('roles')->where('id', $requestedRoleId)->get()->getRowArray();
        if (! $role || ! in_array($role['key'] ?? '', RoleRequestService::ALLOWED_SELF_REQUEST_ROLES, true)) {
            return redirect()->back()->withInput()
                ->with('error', 'Peran yang dipilih tidak dapat diajukan secara mandiri.');
        }

        // Cek apakah sudah memiliki role tersebut
        $alreadyHas = $db->table('user_roles')
            ->where(['user_id' => $userId, 'role_id' => $requestedRoleId, 'status' => 'active'])
            ->countAllResults() > 0;
        if ($alreadyHas) {
            return redirect()->back()->with('error', 'Anda sudah memiliki peran ' . $role['name'] . ' yang aktif.');
        }

        // Cek apakah masih ada pengajuan pending untuk role yang sama
        $pending = $db->table('role_requests')
            ->where('user_id', $userId)
            ->where('requested_role_id', $requestedRoleId)
            ->whereIn('status', ['submitted', 'in_review', 'revision_required'])
            ->countAllResults() > 0;
        if ($pending) {
            return redirect()->back()->with('error', 'Pengajuan untuk peran ' . $role['name'] . ' masih sedang dalam proses peninjauan.');
        }

        $service   = new RoleRequestService();
        $requestId = $service->submit($userId, $requestedRoleId, $reason);

        try {
            $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
            (new NotificationService($db))->sendShared(
                'shared:admin_operator',
                'role_request_submitted',
                'Permohonan Peran Baru: ' . $role['name'],
                "{$userName} mengajukan permohonan penambahan peran sebagai {$role['name']}.",
                [
                    'request_id' => $requestId,
                    'url'        => site_url('dashboard/akses/permohonan-role/' . $requestId),
                ],
                $userId
            );
        } catch (\Throwable) {
        }

        return redirect()->to(route_to('workspace.profile.role-requests'))
            ->with('message', 'Permohonan peran ' . $role['name'] . ' berhasil dikirim dan sedang menunggu peninjauan Administrator.');
    }
}
