<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\RoleManagementService;

/**
 * Admin Manajemen Akses → Permission management.
 */
class PermissionController extends BaseController
{
    public function index()
    {
        $permissions = (new RoleManagementService())->listPermissions();
        $pager = service('pager');
        $page = max(1, (int) ($this->request->getGet('page_permissions') ?: 1));
        $perPage = 20;
        $pager->store('permissions', $page, $perPage, count($permissions), 0);

        return view('dashboard/access/permissions', [
            'title'         => 'Permission | Dashboard BegawiPBJ',
            'pageTitle'     => 'Daftar Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permissions'   => $permissions,
            'pager'         => $pager,
        ]);
    }

    public function create()
    {
        return view('dashboard/access/permission_form', [
            'title'         => 'Tambah Permission | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Permission',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }

    public function store()
    {
        $rules = [
            'key'         => 'required|max_length[128]|is_unique[permissions.key]',
            'name'        => 'required|max_length[255]',
            'domain'      => 'required|max_length[64]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $id = (new RoleManagementService())->createPermission($this->request->getPost(), $actorId);
        return redirect()->to('dashboard/akses/permission/' . $id)->with('success', 'Permission dibuat.');
    }

    public function detail(string $id)
    {
        $permId = ctype_digit($id) ? (int) $id : 0;
        $permission = (new RoleManagementService())->findPermission($permId);
        if (! $permission) {
            return redirect()->to('dashboard/akses/permission')->with('error', 'Permission tidak ditemukan.');
        }

        $roles = db_connect()->table('role_permissions rp')
            ->select('r.id, r.key, r.name')
            ->join('roles r', 'r.id = rp.role_id')
            ->where('rp.permission_id', $permId)
            ->get()->getResultArray();

        return view('dashboard/access/permission_detail', [
            'title'         => 'Detail Permission | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permission'    => $permission,
            'roles'         => $roles,
            'permissionId'  => $permId,
        ]);
    }

    public function update(string $id)
    {
        $permId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'        => 'required|max_length[255]',
            'domain'      => 'required|max_length[64]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        (new RoleManagementService())->updatePermission($permId, $this->request->getPost(), $actorId);
        return redirect()->to('dashboard/akses/permission/' . $permId)->with('success', 'Permission diperbarui.');
    }

    public function delete(string $id)
    {
        $permId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        try {
            (new RoleManagementService())->deletePermission($permId, $actorId);
            return redirect()->to('dashboard/akses/permission')->with('success', 'Permission dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}