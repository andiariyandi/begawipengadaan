<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrganizationController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/access/organization_units', [
            'title' => 'Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Unit Organisasi',
            'activeMenu' => 'access',
            'activeSubmenu' => 'organization-units',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/access/organization_unit_form', [
            'title' => 'Tambah Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Unit Organisasi',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'organization-units',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/access/organization_unit_detail', [
            'title' => 'Detail Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Unit Organisasi',
            'organizationId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'organization-units',
        ]);
    }
}
