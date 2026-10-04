<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\RoleRequestService;

/**
 * Admin Manajemen Akses → Permohonan Role.
 */
class RoleRequestReviewController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $items = (new RoleRequestService())->queue(
            $status !== '' ? ['status' => $status] : [],
        );

        return view('dashboard/users/role_requests', [
            'title'         => 'Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle'     => 'Permohonan Role',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'role-requests',
            'items'         => $items,
            'filters'       => ['status' => $status],
            'statuses'      => ['submitted', 'in_review', 'revision_required', 'approved', 'rejected', 'archived'],
        ]);
    }

    public function detail(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $service = new RoleRequestService();
        $row = $service->find($requestId);

        if (! $row) {
            return redirect()->to('dashboard/akses/permohonan-role')
                ->with('error', 'Permohonan role tidak ditemukan.');
        }

        $db = db_connect();
        $user = $db->table('users u')
            ->select('u.id, u.name, u.email, u.username, u.profile_type,
                      GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") AS current_roles')
            ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = "active"', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.id', $row['user_id'])
            ->groupBy('u.id')
            ->get()->getRowArray();

        $requested = $db->table('roles')->where('id', $row['requested_role_id'])->get()->getRowArray();

        $history = $db->table('workflow_transitions')
            ->where('resource_type', 'role_request')
            ->where('resource_id', $requestId)
            ->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('dashboard/users/role_request_detail', [
            'title'         => 'Detail Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Permohonan Role',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'role-requests',
            'request'       => $row,
            'user'          => $user,
            'requestedRole' => $requested,
            'history'       => $history,
        ]);
    }

    public function approve(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        $notes = (string) $this->request->getPost('notes');
        try {
            (new RoleRequestService())->approve($requestId, $actorId, $notes);
            return redirect()->to('dashboard/akses/permohonan-role')->with('success', 'Permohonan role disetujui.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $note = trim((string) $this->request->getPost('reason'));
        $actorId = (int) session()->get('user_id');
        try {
            (new RoleRequestService())->reject($requestId, $note, $actorId);
            return redirect()->to('dashboard/akses/permohonan-role')->with('success', 'Permohonan role ditolak.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
    }

    public function requestRevision(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $note = trim((string) $this->request->getPost('revision_note'));
        $actorId = (int) session()->get('user_id');
        try {
            (new RoleRequestService())->requestRevision($requestId, $note, $actorId);
            return redirect()->to('dashboard/akses/permohonan-role')->with('success', 'Permintaan revisi dikirim.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
    }
}