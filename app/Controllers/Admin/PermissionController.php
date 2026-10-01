<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PermissionController extends BaseController
{
    public function index(): string
    {
        $builder = db_connect()->table('permissions')->orderBy('domain')->orderBy('key');
        $page = max(1, (int) ($this->request->getGet('page_permissions') ?: 1)); $perPage = 20;
        $pager = service('pager'); $pager->store('permissions', $page, $perPage, (clone $builder)->countAllResults(), 0);
        $permissions = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        return view('dashboard/access/permissions', [
            'title' => 'Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Permission',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
            'permissions' => $permissions,
            'pager' => $pager,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/access/permission_form', [
            'title' => 'Tambah Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Permission',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }

    public function detail(string $id): string
    {
        $permission = db_connect()->table('permissions')->where('id', ctype_digit($id) ? (int) $id : 0)->orWhere('key', str_replace('-', '.', $id))->get()->getRowArray();
        return view('dashboard/access/permission_detail', [
            'title' => 'Detail Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permission',
            'permissionId' => $id,
            'permission' => $permission,
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }
}
