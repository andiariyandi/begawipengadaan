<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\RoleManagementService;

/**
 * Admin Manajemen Akses → Role dan Permission (roles).
 *
 * List/detail/create/edit/delete + role-permission matrix view.
 */
class RoleController extends BaseController
{
    public function index()
    {
        $roles = (new RoleManagementService())->listRoles();
        $service = new RoleManagementService();
        $pager = service('pager');
        $page = max(1, (int) ($this->request->getGet('page_roles') ?: 1));
        $perPage = 15;
        $pager->store('roles', $page, $perPage, count($roles), 0);

        return view('dashboard/access/roles', [
            'title'         => 'Role dan Permission | Dashboard BegawiPBJ',
            'pageTitle'     => 'Role dan Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'roles'         => $roles,
            'pager'         => $pager,
        ]);
    }

    public function create()
    {
        return view('dashboard/access/role_form', [
            'title'         => 'Tambah Role | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Role',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permissions'   => (new RoleManagementService())->listPermissions(),
        ]);
    }

    public function store()
    {
        $rules = [
            'key'         => 'required|max_length[64]|is_unique[roles.key]',
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $id = (new RoleManagementService())->createRole($this->request->getPost(), $actorId);
        return redirect()->to('dashboard/akses/role/' . $id)->with('success', 'Role dibuat.');
    }

    public function detail(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $service = new RoleManagementService();
        $role = $service->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/role')->with('error', 'Role tidak ditemukan.');
        }
        $permissions = $service->permissionsForRole($roleId);

        return view('dashboard/access/role_detail', [
            'title'         => 'Detail Role | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Role',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'role'          => $role,
            'permissions'   => array_column($permissions, 'key'),
            'roleId'        => $roleId,
        ]);
    }

    public function edit(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $role = (new RoleManagementService())->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/role')->with('error', 'Role tidak ditemukan.');
        }

        $service = new RoleManagementService();
        return view('dashboard/access/role_form', [
            'title'           => 'Ubah Role | Dashboard BegawiPBJ',
            'pageTitle'       => 'Ubah Role',
            'formMode'        => 'edit',
            'activeMenu'      => 'access',
            'activeSubmenu'   => 'roles',
            'role'            => $role,
            'roleId'          => $roleId,
            'permissions'     => $service->listPermissions(),
            'assignedKeys'    => array_column($service->permissionsForRole($roleId), 'key'),
        ]);
    }

    public function update(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $service = new RoleManagementService();
        $service->updateRole($roleId, $this->request->getPost(), $actorId);

        // Optional: sync permissions if posted
        $permissionIds = (array) $this->request->getPost('permission_ids');
        if ($permissionIds !== []) {
            $service->syncRolePermissions($roleId, array_map('intval', $permissionIds), $actorId);
        }
        return redirect()->to('dashboard/akses/role/' . $roleId)->with('success', 'Role diperbarui.');
    }

    public function delete(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        try {
            (new RoleManagementService())->deleteRole($roleId, $actorId);
            return redirect()->to('dashboard/akses/role')->with('success', 'Role dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Role vs Permission matrix (read view).
     */
    public function matrix()
    {
        $db = db_connect();
        $roles = $db->table('roles')->orderBy('name')->get()->getResultArray();
        $permissions = (new RoleManagementService())->listPermissions();
        $matrix = [];
        foreach ($roles as $role) {
            $rows = $db->table('role_permissions')->where('role_id', $role['id'])->get()->getResultArray();
            $matrix[$role['id']] = array_column($rows, 'permission_id');
        }

        return view('dashboard/access/role_permission_matrix', [
            'title'         => 'Role-Permission Matrix | Dashboard BegawiPBJ',
            'pageTitle'     => 'Role vs Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'roles'         => $roles,
            'permissions'   => $permissions,
            'matrix'        => $matrix,
        ]);
    }

    public function syncMatrix()
    {
        $actorId = (int) session()->get('user_id');
        $service = new RoleManagementService();
        $payload = (array) $this->request->getPost('matrix');

        foreach ($payload as $roleId => $permissionIds) {
            $service->syncRolePermissions((int) $roleId, array_map('intval', (array) $permissionIds), $actorId);
        }

        return redirect()->to('dashboard/akses/role/matrix')->with('success', 'Matrix disinkronkan.');
    }
}