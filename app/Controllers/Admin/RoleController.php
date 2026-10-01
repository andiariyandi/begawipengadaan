<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RoleController extends BaseController
{
    public function index(): string
    {
        $builder = db_connect()->table('roles r')
            ->select('r.*,COUNT(DISTINCT rp.permission_id) permission_count')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->groupBy('r.id')->orderBy('r.name');
        $page = max(1, (int) ($this->request->getGet('page_roles') ?: 1)); $perPage = 15;
        $pager = service('pager'); $pager->store('roles', $page, $perPage, (clone $builder)->countAllResults(), 0);
        $roles = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        return view('dashboard/access/roles', [
            'title' => 'Role dan Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Role dan Permission',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
            'roles' => $roles,
            'pager' => $pager,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/access/role_form', [
            'title' => 'Tambah Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Role',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }

    public function detail(string $id): string
    {
        $role = db_connect()->table('roles')->where('id', ctype_digit($id) ? (int) $id : 0)->get()->getRowArray();
        $permissions = db_connect()->table('permissions p')->select('p.key,p.name')->join('role_permissions rp', 'rp.permission_id=p.id')->where('rp.role_id', $role['id'] ?? 0)->orderBy('p.key')->get()->getResultArray();
        return view('dashboard/access/role_detail', [
            'title' => 'Detail Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Role',
            'roleId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/access/role_form', [
            'title' => 'Ubah Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Role',
            'formMode' => 'edit',
            'roleId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }
}
