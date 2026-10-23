<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\RoleManagementService;
use App\Services\Identity\AuthorizationService;

/**
 * Admin Manajemen Akses → Permission management.
 */
class PermissionController extends BaseController
{
    private function checkSuperAdmin(): ?\CodeIgniter\HTTP\ResponseInterface
    {
        if (! (new AuthorizationService())->isSuperAdmin()) {
            return redirect()->to('dashboard')->with('error', 'Hanya Super Administrator yang berhak mengelola Role & Permission.');
        }
        return null;
    }

    public function index()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $allPermissions = (new RoleManagementService())->listPermissions();
        $keyword = trim((string) $this->request->getGet('keyword'));
        if ($keyword !== '') {
            $allPermissions = array_filter($allPermissions, static function ($p) use ($keyword) {
                return stripos($p['name'] ?? '', $keyword) !== false
                    || stripos($p['module'] ?? '', $keyword) !== false
                    || stripos($p['description'] ?? '', $keyword) !== false;
            });
        }
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 20)));
        $page = max(1, (int) ($this->request->getGet('page_permissions') ?: 1));
        $total = count($allPermissions);
        $permissions = array_slice(array_values($allPermissions), ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('permissions', $page, $perPage, $total, 0);

        return view('dashboard/access/permissions', [
            'title'         => 'Permission | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Daftar Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permissions'   => $permissions,
            'pager'         => $pager,
            'keyword'       => $keyword,
            'perPage'       => $perPage,
        ]);
    }

    public function create()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        return view('dashboard/access/permission_form', [
            'title'         => 'Tambah Permission | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Permission',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
        ]);
    }

    public function store()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $rules = [
            'key'         => 'required|max_length[128]|is_unique[permissions.key]',
            'name'        => 'required|max_length[255]',
            'domain'      => 'required|max_length[64]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $payload = [
            'key'         => trim((string) $this->request->getPost('key')),
            'name'        => trim((string) $this->request->getPost('name')),
            'domain'      => trim((string) $this->request->getPost('domain')),
            'description' => trim((string) $this->request->getPost('description')),
        ];
        $id = (new RoleManagementService())->createPermission($payload, $actorId);
        return redirect()->to('dashboard/akses/permission/' . $id)->with('success', 'Permission dibuat.');
    }

    public function detail(string $id)
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $permId = ctype_digit($id) ? (int) $id : 0;
        $permission = (new RoleManagementService())->findPermission($permId);
        if (! $permission) {
            return redirect()->to('dashboard/akses/permission')->with('error', 'Permission tidak ditemukan.');
        }

        $roles = db_connect()->table('role_permissions rp')
            ->select('r.id, r.key, r.name')
            ->join('roles r', 'r.id = rp.role_id')
            ->where('rp.permission_id', $permId)
            ->get()->getResultArray();

        return view('dashboard/access/permission_detail', [
            'title'         => 'Detail Permission | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Permission',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'roles',
            'permission'    => $permission,
            'roles'         => $roles,
            'permissionId'  => $permId,
        ]);
    }

    public function update(string $id)
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $permId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'        => 'required|max_length[255]',
            'domain'      => 'required|max_length[64]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $payload = [
            'name'        => trim((string) $this->request->getPost('name')),
            'domain'      => trim((string) $this->request->getPost('domain')),
            'description' => trim((string) $this->request->getPost('description')),
        ];
        (new RoleManagementService())->updatePermission($permId, $payload, $actorId);
        return redirect()->to('dashboard/akses/permission/' . $permId)->with('success', 'Permission diperbarui.');
    }

    public function delete(string $id)
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }
        $permId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        $service = new RoleManagementService();
        $perm = $service->findPermission($permId);

        if ($perm && (int) ($perm['is_system'] ?? 0) === 1) {
            return redirect()->back()->with('error', 'Permission sistem dilindungi (is_system = 1) dan tidak dapat dihapus.');
        }

        try {
            $service->deletePermission($permId, $actorId);
            return redirect()->to('dashboard/akses/permission')->with('success', 'Permission dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Sinkronkan 90 katalog permission sistem langsung dari Dashboard.
     * Tidak memerlukan akses SSH / terminal di server production/deploy.
     */
    public function syncCatalog()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }

        try {
            $seeder = new \App\Database\Seeds\PermissionCatalogSeeder(new \Config\Database());
            $seeder->run();

            return redirect()->to('dashboard/akses/permission')
                ->with('success', 'Katalog permission sistem (termasuk 82 key modular baru) berhasil disinkronkan ke database dan hak akses role kanonikal telah diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyinkronkan katalog permission: ' . $e->getMessage());
        }
    }

    /**
     * Import custom permission dari berkas JSON atau CSV via Dashboard.
     */
    public function import()
    {
        if ($res = $this->checkSuperAdmin()) {
            return $res;
        }

        $file = $this->request->getFile('import_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Silakan pilih berkas file JSON atau CSV yang valid.');
        }

        $ext = strtolower($file->getClientExtension());
        $actorId = (int) session()->get('user_id');
        $service = new RoleManagementService();
        $imported = 0;

        try {
            if ($ext === 'json') {
                $raw = file_get_contents($file->getTempName());
                $data = json_decode($raw, true);
                if (! is_array($data)) {
                    return redirect()->back()->with('error', 'Format berkas JSON tidak valid.');
                }
                foreach ($data as $item) {
                    if (empty($item['key']) || empty($item['name']) || empty($item['domain'])) {
                        continue;
                    }
                    $existing = db_connect()->table('permissions')->where('key', trim((string) $item['key']))->get()->getRowArray();
                    if (! $existing) {
                        $service->createPermission([
                            'key'         => trim((string) $item['key']),
                            'name'        => trim((string) $item['name']),
                            'domain'      => trim((string) $item['domain']),
                            'description' => $item['description'] ?? null,
                        ], $actorId);
                        $imported++;
                    }
                }
            } elseif ($ext === 'csv') {
                if (($handle = fopen($file->getTempName(), 'r')) !== false) {
                    $firstLine = fgets($handle);
                    $delimiter = str_contains($firstLine, ';') ? ';' : ',';
                    rewind($handle);

                    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                        if (count($row) >= 3) {
                            $key = trim((string) $row[0]);
                            $name = trim((string) $row[1]);
                            $domain = trim((string) $row[2]);
                            $desc = $row[3] ?? null;

                            if (strtolower($key) === 'key' || strtolower($key) === 'kode kunci' || strtolower($key) === 'id') {
                                continue;
                            }
                            if ($key === '' || $name === '' || $domain === '') {
                                continue;
                            }

                            $existing = db_connect()->table('permissions')->where('key', $key)->get()->getRowArray();
                            if (! $existing) {
                                $service->createPermission([
                                    'key'         => $key,
                                    'name'        => $name,
                                    'domain'      => $domain,
                                    'description' => $desc,
                                ], $actorId);
                                $imported++;
                            }
                        }
                    }
                    fclose($handle);
                }
            } else {
                return redirect()->back()->with('error', 'Format berkas harus berupa .json atau .csv');
            }

            return redirect()->to('dashboard/akses/permission')
                ->with('success', "Berhasil mengimpor {$imported} permission baru ke dalam database.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses impor: ' . $e->getMessage());
        }
    }
}
