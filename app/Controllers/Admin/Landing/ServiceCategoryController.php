<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;

class ServiceCategoryController extends BaseController
{
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $categories = $this->db->table('service_categories')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/category/index', [
            'title'         => 'Kelola Kategori Layanan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Kategori Layanan PBJ',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'kategori-layanan',
            'categories'    => $categories,
        ]);
    }

    public function create()
    {
        return view('dashboard/landing/category/form', [
            'title'         => 'Tambah Kategori Layanan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Kategori Layanan',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'kategori-layanan',
            'category'      => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[150]',
            'code' => 'required|max_length[50]|is_unique[service_categories.code]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'code'        => trim((string) $this->request->getPost('code')),
            'description' => trim((string) $this->request->getPost('description')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('service_categories')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.service_category.created', 'landing', 'service_categories', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/kategori-layanan')->with('success', 'Kategori layanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $category = $this->db->table('service_categories')->where('id', $id)->get()->getRowArray();
        if (! $category) {
            return redirect()->to('dashboard/landing/kategori-layanan')->with('error', 'Kategori tidak ditemukan.');
        }

        return view('dashboard/landing/category/form', [
            'title'         => 'Ubah Kategori Layanan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Kategori Layanan',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'kategori-layanan',
            'category'      => $category,
        ]);
    }

    public function update($id)
    {
        $category = $this->db->table('service_categories')->where('id', $id)->get()->getRowArray();
        if (! $category) {
            return redirect()->to('dashboard/landing/kategori-layanan')->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|max_length[150]',
            'code' => "required|max_length[50]|is_unique[service_categories.code,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'code'        => trim((string) $this->request->getPost('code')),
            'description' => trim((string) $this->request->getPost('description')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('service_categories')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.service_category.updated', 'landing', 'service_categories', (int) $id, ['before' => $category, 'after' => $data]);

        return redirect()->to('dashboard/landing/kategori-layanan')->with('success', 'Kategori layanan berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $category = $this->db->table('service_categories')->where('id', $id)->get()->getRowArray();
        if ($category) {
            $this->db->table('service_categories')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.service_category.deleted', 'landing', 'service_categories', (int) $id, ['deleted' => $category]);
        }

        return redirect()->to('dashboard/landing/kategori-layanan')->with('success', 'Kategori layanan berhasil dihapus.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $category = $this->db->table('service_categories')->where('id', $id)->get()->getRowArray();
        if ($category) {
            $newStatus = $category['is_active'] ? 0 : 1;
            $this->db->table('service_categories')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/kategori-layanan')->with('success', 'Status kategori berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/kategori-layanan')->with('error', 'Kategori tidak ditemukan.');
    }
}
