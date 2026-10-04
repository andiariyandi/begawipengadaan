<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\ProcurementRoleService;

/**
 * Admin Manajemen Akses → Peran Pengadaan.
 *
 * CRUD for procurement_roles (PA, KPA, PPK, ...) plus
 * user_procurement_roles assignment management.
 */
class ProcurementRoleController extends BaseController
{
    public function index()
    {
        $roles = (new ProcurementRoleService())->listRoles();
        $pager = service('pager');
        $page = max(1, (int) ($this->request->getGet('page_procurement_roles') ?: 1));
        $perPage = 15;
        $pager->store('procurement_roles', $page, $perPage, count($roles), 0);

        return view('dashboard/access/procurement_roles', [
            'title'         => 'Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle'     => 'Peran Pengadaan',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
            'roles'         => $roles,
            'pager'         => $pager,
        ]);
    }

    public function create()
    {
        return view('dashboard/access/procurement_role_form', [
            'title'         => 'Tambah Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Peran Pengadaan',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
        ]);
    }

    public function store()
    {
        $rules = [
            'key'         => 'required|max_length[64]|is_unique[procurement_roles.key]',
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $id = (new ProcurementRoleService())->createRole($this->request->getPost(), (int) session()->get('user_id'));
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $id)->with('success', 'Peran pengadaan dibuat.');
    }

    public function detail(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $role = (new ProcurementRoleService())->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/peran-pengadaan')->with('error', 'Peran tidak ditemukan.');
        }

        $assignments = (new ProcurementRoleService())->listAssignments(['user_id' => null]);

        return view('dashboard/access/procurement_role_detail', [
            'title'         => 'Detail Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Peran Pengadaan',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
            'role'          => $role,
            'assignments'   => array_values(array_filter($assignments, static fn($a) => (int) $a['procurement_role_id'] === $roleId)),
            'procurementRoleId' => $roleId,
        ]);
    }

    public function update(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        (new ProcurementRoleService())->updateRole($roleId, $this->request->getPost(), (int) session()->get('user_id'));
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $roleId)->with('success', 'Peran diperbarui.');
    }

    /**
     * Penugasan Peran Pengadaan (assignment view + actions).
     */
    public function assignments()
    {
        $service = new ProcurementRoleService();
        $items = $service->listAssignments(['verification_status' => $this->request->getGet('verification_status')]);

        return view('dashboard/access/procurement_role_assignments', [
            'title'         => 'Penugasan Peran Pengadaan | Dashboard BegawiPBJ',
            'pageTitle'     => 'Penugasan Peran Pengadaan',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
            'items'         => $items,
            'filters'       => ['verification_status' => $this->request->getGet('verification_status')],
            'roles'         => $service->listRoles(),
        ]);
    }

    public function storeAssignment()
    {
        $rules = [
            'user_id'             => 'required|is_natural_no_zero',
            'procurement_role_id' => 'required|is_natural_no_zero',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $unitId = $this->request->getPost('organization_unit_id');
        $unitId = is_numeric($unitId) ? (int) $unitId : null;
        $id = (new ProcurementRoleService())->assign(
            (int) $this->request->getPost('user_id'),
            (int) $this->request->getPost('procurement_role_id'),
            $unitId,
            (int) session()->get('user_id'),
        );
        return redirect()->to('dashboard/akses/penugasan-pengadaan')->with('success', 'Penugasan peran disimpan (id ' . $id . ').');
    }

    public function verifyAssignment(string $id)
    {
        $assignmentId = ctype_digit($id) ? (int) $id : 0;
        $decision = (string) $this->request->getPost('decision');
        $newStatus = $decision === 'verify' ? 'verified' : 'rejected';
        try {
            (new ProcurementRoleService())->verify($assignmentId, $newStatus, (int) session()->get('user_id'));
            return redirect()->to('dashboard/akses/penugasan-pengadaan')->with('success', 'Status penugasan diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}