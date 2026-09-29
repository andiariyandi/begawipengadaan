<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RoleController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/access/roles', [
            'title' => 'Role dan Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Role dan Permission',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
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
        return view('dashboard/access/role_detail', [
            'title' => 'Detail Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Role',
            'roleId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
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
