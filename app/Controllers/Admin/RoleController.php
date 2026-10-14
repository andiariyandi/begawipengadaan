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
    private function checkSuperAdmin(): ?\CodeIgniter\HTTP\ResponseInterface
    {
        if (! (new \App\Services\Identity\AuthorizationService())->isSuperAdmin()) {
            return redirect()->to('dashboard')->with('error', 'Hanya Super Administrator yang berhak mengelola Role & Permission.');
        }
        return null;
    }

    public function index()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $allRoles = (new RoleManagementService())->listRoles();
        $keyword = trim((string) $this->request->getGet('keyword'));
        if ($keyword !== '') {
            $allRoles = array_filter($allRoles, static function ($r) use ($keyword) {
                return stripos($r['name'] ?? '', $keyword) !== false
                    || stripos($r['key'] ?? '', $keyword) !== false
                    || stripos($r['description'] ?? '', $keyword) !== false;
            });
        }
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_roles') ?: 1));
        $total = count($allRoles);
        $roles = array_slice(array_values($allRoles), ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('roles', $page, $perPage, $total, 0);

        return view('dashboard/access/roles', [
            'title'         => 'Role dan Permission | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Role dan Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'roles'         => $roles,
            'pager'         => $pager,
            'keyword'       => $keyword,
            'perPage'       => $perPage,
        ]);
    }

    public function create()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        return view('dashboard/access/role_form', [
            'title'         => 'Tambah Role | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Role',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permissions'   => (new RoleManagementService())->listPermissions(),
        ]);
    }

    public function store()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $service = new RoleManagementService();
        $role = $service->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/role')->with('error', 'Role tidak ditemukan.');
        }
        $permissions = $service->permissionsForRole($roleId);

        return view('dashboard/access/role_detail', [
            'title'         => 'Detail Role | Dashboard BegawiPengadaan',
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $role = (new RoleManagementService())->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/role')->with('error', 'Role tidak ditemukan.');
        }

        $service = new RoleManagementService();
        return view('dashboard/access/role_form', [
            'title'           => 'Ubah Role | Dashboard BegawiPengadaan',
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $db = db_connect();
        $roles = $db->table('roles')->orderBy('name')->get()->getResultArray();
        $permissions = (new RoleManagementService())->listPermissions();
        $matrix = [];
        foreach ($roles as $role) {
            $rows = $db->table('role_permissions')->where('role_id', $role['id'])->get()->getResultArray();
            $matrix[$role['id']] = array_column($rows, 'permission_id');
        }

        return view('dashboard/access/role_permission_matrix', [
            'title'         => 'Role-Permission Matrix | Dashboard BegawiPengadaan',
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
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $actorId = (int) session()->get('user_id');
        $service = new RoleManagementService();
        $payload = (array) $this->request->getPost('matrix');

        foreach ($payload as $roleId => $permissionIds) {
            $service->syncRolePermissions((int) $roleId, array_map('intval', (array) $permissionIds), $actorId);
        }

        return redirect()->to('dashboard/akses/role/matrix')->with('success', 'Matrix disinkronkan.');
    }
}
