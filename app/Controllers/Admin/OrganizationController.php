<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrganizationController extends BaseController
{
    public function index(): string
    {
        $builder = db_connect()->table('organization_units u')->select('u.*,p.name parent_name')->join('organization_units p','p.id=u.parent_id','left')->orderBy('u.sort_order')->orderBy('u.name');
        $page = max(1, (int) ($this->request->getGet('page_units') ?: 1)); $perPage = 15;
        $pager = service('pager'); $pager->store('units', $page, $perPage, (clone $builder)->countAllResults(), 0);
        $units = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        return view('dashboard/access/organization_units', [
            'title' => 'Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Unit Organisasi',
            'activeMenu' => 'access',
            'activeSubmenu' => 'organization-units',
            'units' => $units,
            'pager' => $pager,
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
        $unit = db_connect()->table('organization_units u')->select('u.*,p.name parent_name')->join('organization_units p','p.id=u.parent_id','left')->where('u.id',ctype_digit($id) ? (int) $id : 0)->get()->getRowArray();
        return view('dashboard/access/organization_unit_detail', [
            'title' => 'Detail Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Unit Organisasi',
            'organizationId' => $id,
            'unit' => $unit,
            'activeMenu' => 'access',
            'activeSubmenu' => 'organization-units',
        ]);
    }
}
