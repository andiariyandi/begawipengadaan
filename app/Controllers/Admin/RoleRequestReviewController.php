<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleRequestModel;
use App\Services\Auth\RoleRequestService;
use Throwable;

class RoleRequestReviewController extends BaseController
{
    public function index(): string
    {
        $requests = db_connect()->table('role_requests r')
            ->select('r.*,u.name,u.email,roles.key role_key,roles.name role_name')
            ->join('users u', 'u.id = r.user_id')
            ->join('roles', 'roles.id = r.requested_role_id')
            ->whereIn('r.status', ['submitted', 'in_review', 'revision_required'])
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultArray();

        return view('dashboard/users/role_requests', [
            'title' => 'Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Permohonan Role',
            'activeMenu' => 'access',
            'activeSubmenu' => 'role-requests',
            'requests' => $requests,
        ]);
    }

    public function detail(string $id): string
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $request = (new RoleRequestModel())->select('role_requests.*,users.name,users.email,roles.key role_key,roles.name role_name')
            ->join('users', 'users.id = role_requests.user_id')
            ->join('roles', 'roles.id = role_requests.requested_role_id')
            ->find($requestId);

        return view('dashboard/users/role_request_detail', [
            'title' => 'Detail Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permohonan Role',
            'requestId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'role-requests',
            'request' => $request,
        ]);
    }

    public function show(string $id): string
    {
        return $this->detail($id);
    }

    public function decide(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $action = (string) ($this->request->getPost('action') ?: $this->request->getPost('decision'));
        $notes = trim((string) ($this->request->getPost('reason') ?: $this->request->getPost('notes')));
        $actorId = (int) session()->get('user_id');

        try {
            (new RoleRequestService())->review(
                $requestId,
                $actorId,
                $action === 'revision' ? 'revision_required' : $action,
                $notes ?: null
            );

            return redirect()->to(site_url('dashboard/akses/permohonan-role'))->with('success', 'Permohonan role berhasil diproses.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
