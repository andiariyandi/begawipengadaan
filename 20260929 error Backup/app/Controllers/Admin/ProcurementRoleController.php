<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ProcurementRoleController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/access/procurement_roles', [
            'title' => 'Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle' => 'Peran Pengadaan',
            'activeMenu' => 'access',
            'activeSubmenu' => 'procurement-roles',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/access/procurement_role_form', [
            'title' => 'Tambah Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Peran Pengadaan',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'procurement-roles',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/access/procurement_role_detail', [
            'title' => 'Detail Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Peran Pengadaan',
            'procurementRoleId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'procurement-roles',
        ]);
    }
}
