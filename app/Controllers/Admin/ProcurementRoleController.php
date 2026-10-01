<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ProcurementRoleController extends BaseController
{
    public function index(): string
    {
        $builder = db_connect()->table('jf_ppbj_levels')->orderBy('sort_order');
        $page = max(1, (int) ($this->request->getGet('page_procurement_roles') ?: 1)); $perPage = 15;
        $pager = service('pager'); $pager->store('procurement_roles', $page, $perPage, (clone $builder)->countAllResults(), 0);
        $roles = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        return view('dashboard/access/procurement_roles', [
            'title' => 'Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle' => 'Peran Pengadaan',
            'activeMenu' => 'access',
            'activeSubmenu' => 'procurement-roles',
            'roles' => $roles,
            'pager' => $pager,
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
