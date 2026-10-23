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
            'title'         => 'Peran Pengadaan | Dashboard BegawiPengadaan',
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
            'title'         => 'Tambah Peran Pengadaan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Peran Pengadaan',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
        ]);
    }

    public function store()
    {
        $postData = (array) ($this->request->getPost() ?: $_POST);
        $rules = [
            'key'         => 'required|max_length[64]|is_unique[procurement_roles.key]',
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
            'is_active'   => 'permit_empty|in_list[0,1]',
        ];
        if (! $this->validateData($postData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id') ?: null;
        $filteredData = [
            'key'         => trim((string) ($postData['key'] ?? '')),
            'name'        => trim((string) ($postData['name'] ?? '')),
            'description' => trim((string) ($postData['description'] ?? '')),
            'is_active'   => isset($postData['is_active']) ? (int) $postData['is_active'] : 1,
        ];
        $id = (new ProcurementRoleService())->createRole($filteredData, $actorId);
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $id)->with('success', 'Peran pengadaan berhasil dibuat.');
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
            'title'         => 'Detail Peran Pengadaan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Peran Pengadaan',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
            'role'          => $role,
            'assignments'   => array_values(array_filter($assignments, static fn($a) => (int) $a['procurement_role_id'] === $roleId)),
            'procurementRoleId' => $roleId,
            'usersCount'        => count(array_filter($assignments, static fn($a) => (int) $a['procurement_role_id'] === $roleId)),
        ]);
    }

    public function edit(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $role = (new ProcurementRoleService())->findRole($roleId);
        if (! $role) {
            return redirect()->to('dashboard/akses/peran-pengadaan')->with('error', 'Peran tidak ditemukan.');
        }

        return view('dashboard/access/procurement_role_form', [
            'title'         => 'Ubah Peran Pengadaan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Peran Pengadaan',
            'formMode'      => 'edit',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'procurement-roles',
            'role'          => $role,
            'roleId'        => $roleId,
        ]);
    }

    public function update(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $postData = (array) ($this->request->getPost() ?: $_POST);
        $rules = [
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
            'is_active'   => 'permit_empty|in_list[0,1]',
        ];
        if (! $this->validateData($postData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id') ?: null;
        $filteredData = [
            'name'        => trim((string) ($postData['name'] ?? '')),
            'description' => trim((string) ($postData['description'] ?? '')),
            'is_active'   => isset($postData['is_active']) ? (int) $postData['is_active'] : 1,
        ];
        (new ProcurementRoleService())->updateRole($roleId, $filteredData, $actorId);
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $roleId)->with('success', 'Peran pengadaan berhasil diperbarui.');
    }

    public function activate(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id') ?: null;
        (new ProcurementRoleService())->updateRole($roleId, ['is_active' => 1], $actorId);
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $roleId)->with('success', 'Peran pengadaan berhasil diaktifkan.');
    }

    public function deactivate(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id') ?: null;
        (new ProcurementRoleService())->updateRole($roleId, ['is_active' => 0], $actorId);
        return redirect()->to('dashboard/akses/peran-pengadaan/' . $roleId)->with('success', 'Peran pengadaan dinonaktifkan.');
    }

    public function delete(string $id)
    {
        $roleId = ctype_digit($id) ? (int) $id : 0;
        $service = new ProcurementRoleService();
        $role = $service->findRole($roleId);

        if (! $role) {
            return redirect()->to('dashboard/akses/peran-pengadaan')->with('error', 'Peran pengadaan tidak ditemukan.');
        }

        $userCount = $service->getUserCountForRole($roleId);

        if ($userCount > 0) {
            if (!empty($role['is_active'])) {
                return redirect()->to('dashboard/akses/peran-pengadaan')->with('warning', 
                    "Peran '{$role['name']}' tidak dapat dihapus karena sedang digunakan oleh {$userCount} pengguna. Silakan gunakan opsi nonaktifkan peran agar tidak dapat dipilih lagi tanpa merusak riwayat penugasan."
                );
            }

            return redirect()->to('dashboard/akses/peran-pengadaan')->with('info', 
                "Peran '{$role['name']}' memiliki riwayat {$userCount} pengguna dan sudah dalam status nonaktif. Data peran dipertahankan untuk integritas audit."
            );
        }

        $actorId = (int) session()->get('user_id') ?: null;
        $service->deleteRole($roleId, $actorId);

        return redirect()->to('dashboard/akses/peran-pengadaan')->with('success', 
            "Peran pengadaan '{$role['name']}' berhasil dihapus secara permanen."
        );
    }

    /**
     * Penugasan Peran Pengadaan (assignment view + actions).
     */
    public function assignments()
    {
        $service = new ProcurementRoleService();
        $items = $service->listAssignments(['verification_status' => $this->request->getGet('verification_status')]);

        return view('dashboard/access/procurement_role_assignments', [
            'title'         => 'Penugasan Peran Pengadaan | Dashboard BegawiPengadaan',
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
