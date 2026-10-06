<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\OrganizationService;

/**
 * Admin Manajemen Akses → Unit Organisasi.
 */
class OrganizationController extends BaseController
{
    public function index()
    {
        $units = (new OrganizationService())->listTree();
        $pager = service('pager');
        $page = max(1, (int) ($this->request->getGet('page_units') ?: 1));
        $perPage = 15;
        $pager->store('units', $page, $perPage, count($units), 0);

        return view('dashboard/access/organization_units', [
            'title'         => 'Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Unit Organisasi',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'organization-units',
            'units'         => $units,
            'pager'         => $pager,
        ]);
    }

    public function create()
    {
        return view('dashboard/access/organization_unit_form', [
            'title'         => 'Tambah Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Unit Organisasi',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'organization-units',
            'units'         => (new OrganizationService())->listTree(),
        ]);
    }

    public function store()
    {
        $rules = [
            'code'  => 'required|max_length[64]|is_unique[organization_units.code]',
            'name'  => 'required|max_length[255]',
            'type'  => 'required|max_length[64]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $id = (new OrganizationService())->create($this->request->getPost(), (int) session()->get('user_id'));
            return redirect()->to('dashboard/akses/unit-organisasi/' . $id)->with('success', 'Unit organisasi dibuat.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function detail(string $id)
    {
        $unitId = ctype_digit($id) ? (int) $id : 0;
        $service = new OrganizationService();
        $unit = $service->find($unitId);
        if (! $unit) {
            return redirect()->to('dashboard/akses/unit-organisasi')->with('error', 'Unit organisasi tidak ditemukan.');
        }

        $parent = null;
        if (! empty($unit['parent_id'])) {
            $parent = $service->find((int) $unit['parent_id']);
        }

        return view('dashboard/access/organization_unit_detail', [
            'title'         => 'Detail Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Unit Organisasi',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'organization-units',
            'unit'          => $unit,
            'parent'        => $parent,
            'organizationId' => $unitId,
        ]);
    }

    public function edit(string $id)
    {
        $unitId = ctype_digit($id) ? (int) $id : 0;
        $service = new OrganizationService();
        $unit = $service->find($unitId);
        if (! $unit) {
            return redirect()->to('dashboard/akses/unit-organisasi')->with('error', 'Unit tidak ditemukan.');
        }

        return view('dashboard/access/organization_unit_form', [
            'title'         => 'Ubah Unit Organisasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Unit Organisasi',
            'formMode'      => 'edit',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'organization-units',
            'unit'          => $unit,
            'unitId'        => $unitId,
            'units'         => $service->listTree(),
        ]);
    }

    public function update(string $id)
    {
        $unitId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name' => 'required|max_length[255]',
            'type' => 'required|max_length[64]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            (new OrganizationService())->update($unitId, $this->request->getPost(), (int) session()->get('user_id'));
            return redirect()->to('dashboard/akses/unit-organisasi/' . $unitId)->with('success', 'Unit diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function deactivate(string $id)
    {
        $unitId = ctype_digit($id) ? (int) $id : 0;
        (new OrganizationService())->deactivate($unitId, (int) session()->get('user_id'));
        return redirect()->to('dashboard/akses/unit-organisasi/' . $unitId)->with('success', 'Unit dinonaktifkan.');
    }

    public function activate(string $id)
    {
        $unitId = ctype_digit($id) ? (int) $id : 0;
        (new OrganizationService())->activate($unitId, (int) session()->get('user_id'));
        return redirect()->to('dashboard/akses/unit-organisasi/' . $unitId)->with('success', 'Unit diaktifkan.');
    }
}