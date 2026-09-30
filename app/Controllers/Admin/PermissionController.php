<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PermissionController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/access/permissions', [
            'title' => 'Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Permission',
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
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
        return view('dashboard/access/permission_detail', [
            'title' => 'Detail Permission | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permission',
            'permissionId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }
}
