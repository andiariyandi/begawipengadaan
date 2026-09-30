<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Services\AuditService;

class CategoryController extends BaseController
{
    public function index()
    {
        return view('knowledge/categories/index', [
            'title'      => 'Kategori KMS',
            'subTitle'   => 'Kategori KMS',
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function store()
    {
        $rules = ['name' => 'required|max_length[100]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Nama kategori wajib diisi.');
        }

        $categoryModel = new CategoryModel();

        $categoryId = $categoryModel->insert([
            'name' => $this->request->getPost('name'),
            'type' => 'kms',
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'kms', 'categories', $categoryId);

        return redirect()->to(route_to('knowledge.categories'))->with('message', 'Kategori berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->find($id);

        if (! $category || $category['type'] !== 'kms') {
            return redirect()->back()->with('error', 'Kategori tidak ditemukan.');
        }

        // Dicegah lewat FK RESTRICT di database kalau masih dipakai artikel/FAQ/dsb —
        // di sini kita tangkap exception-nya supaya pesannya ramah, bukan error SQL mentah.
        try {
            $categoryModel->delete($id);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai konten lain.');
        }

        (new AuditService())->log(session()->get('user_id'), 'delete', 'kms', 'categories', $id);

        return redirect()->to(route_to('knowledge.categories'))->with('message', 'Kategori berhasil dihapus.');
    }
}
