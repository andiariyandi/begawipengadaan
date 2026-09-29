<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\RolePermissionModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Services\AuditService;
use RuntimeException;

class RoleController extends BaseController
{
    public function index()
    {
        $roleModel = new RoleModel();

        return view('roleAndAccess/roleAccess', [
            'title'    => 'Role & Access',
            'subTitle' => 'Role & Access',
            'roles'    => $roleModel->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function permissions(int $roleId)
    {
        $role = (new RoleModel())->find($roleId);

        if (! $role) {
            return redirect()->to(route_to('roleAaccess'))->with('error', 'Role tidak ditemukan.');
        }

        $permissions = (new PermissionModel())
            ->orderBy('module', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
        $assignedIds = array_column(
            (new RolePermissionModel())->where('role_id', $roleId)->findAll(),
            'permission_id'
        );

        return view('roleAndAccess/permissions', [
            'title'        => 'Atur Permission',
            'subTitle'     => 'Atur Permission',
            'role'         => $role,
            'permissions'  => $permissions,
            'assignedIds'  => array_map('intval', $assignedIds),
        ]);
    }

    public function updatePermissions(int $roleId)
    {
        $role = (new RoleModel())->find($roleId);

        if (! $role) {
            return redirect()->to(route_to('roleAaccess'))->with('error', 'Role tidak ditemukan.');
        }

        $permissionIds = $this->request->getPost('permission_ids');
        $permissionIds = is_array($permissionIds) ? $permissionIds : [];
        $permissionIds = array_values(array_unique(array_filter(
            array_map('intval', $permissionIds),
            static fn (int $permissionId): bool => $permissionId > 0
        )));

        $validIds = array_map(
            'intval',
            array_column((new PermissionModel())->select('id')->findAll(), 'id')
        );
        $invalidIds = array_diff($permissionIds, $validIds);

        if ($invalidIds !== []) {
            return redirect()->back()->with('error', 'Terdapat permission yang tidak valid.');
        }

        $db = db_connect();
        $db->transStart();

        try {
            (new RolePermissionModel())->where('role_id', $roleId)->delete();

            if ($permissionIds !== []) {
                $rows = array_map(static fn (int $permissionId): array => [
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId,
                ], $permissionIds);
                (new RolePermissionModel())->insertBatch($rows);
            }

            $db->transComplete();
        } catch (RuntimeException $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Permission gagal diperbarui.');
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Permission gagal diperbarui.');
        }

        (new AuditService())->log(
            session()->get('user_id'),
            'update_permissions',
            'rbac',
            'roles',
            $roleId,
            ['permission_ids' => $permissionIds]
        );

        return redirect()->to(route_to('role.permissions', $roleId))
            ->with('message', 'Permission role berhasil diperbarui.');
    }

    public function store()
    {
        $rules = ['name' => 'required|min_length[3]|max_length[50]|is_unique[roles.name]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama role wajib diisi dan belum dipakai.');
        }

        $name = $this->request->getPost('name');
        $slug = strtolower(str_replace(' ', '_', $name));

        (new RoleModel())->insert(['name' => $name, 'slug' => $slug]);

        (new AuditService())->log(session()->get('user_id'), 'create', 'rbac', 'roles');

        return redirect()->to(route_to('roleAaccess'))->with('message', 'Role berhasil ditambahkan.');
    }

    public function delete(int $roleId)
    {
        $roleModel = new RoleModel();
        $role      = $roleModel->find($roleId);

        $protectedSlugs = ['super_administrator', 'administrator', 'operator'];

        if (! $role || in_array($role['slug'], $protectedSlugs, true)) {
            return redirect()->back()->with('error', 'Role ini tidak dapat dihapus.');
        }

        $roleModel->delete($roleId);

        (new AuditService())->log(session()->get('user_id'), 'delete', 'rbac', 'roles', $roleId);

        return redirect()->to(route_to('roleAaccess'))->with('message', 'Role berhasil dihapus.');
    }

    public function assignPage()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();

        $users = $userModel->db->table('users')
            ->select('users.id, users.username, users.user_type, GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ", ") as role_names', false)
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->where('users.deleted_at', null)
            ->groupBy('users.id, users.username, users.user_type')
            ->get()
            ->getResultArray();

        return view('roleAndAccess/assignRole', [
            'title'    => 'Assign Role',
            'subTitle' => 'Assign Role',
            'users'    => $users,
            'allRoles' => $roleModel->findAll(),
        ]);
    }

    public function assign()
    {
        $rules = [
            'user_id' => 'required|is_natural_no_zero',
            'role_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $userId = (int) $this->request->getPost('user_id');
        $roleId = (int) $this->request->getPost('role_id');
        $user   = (new UserModel())->find($userId);
        $role   = (new RoleModel())->find($roleId);

        if (! $user || ! $role) {
            return redirect()->back()->with('error', 'Pengguna atau role tidak ditemukan.');
        }

        $asnOnlyRoles = ['super_administrator', 'administrator', 'operator'];
        if (in_array($role['slug'], $asnOnlyRoles, true) && $user['user_type'] !== 'asn') {
            return redirect()->back()->with('error', 'Role administratif hanya dapat diberikan kepada pengguna ASN.');
        }

        (new UserRoleModel())->assignRole($userId, $roleId);

        (new AuditService())->log(session()->get('user_id'), 'assign_role', 'rbac', 'user_roles', $userId, [
            'assigned_role_id' => $roleId,
            'role_slug'        => $role['slug'],
        ]);

        return redirect()->to(route_to('assignRole'))->with('message', 'Role berhasil ditambahkan tanpa menghapus role lain.');
    }
}
