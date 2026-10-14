<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\KnowledgeCategoryModel;

class CategoryController extends BaseKnowledgeController
{
    public function index(): string
    {
        $items = (new KnowledgeCategoryModel())->activeFlat();
        $counts = $this->contentCountsByCategory();
        return view('dashboard/knowledge/categories/index', [
            'title'        => 'Kategori Bunga Rampai | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Kategori',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'categories',
            'items'        => $items,
            'counts'       => $counts,
        ]);
    }

    public function create(): string
    {
        $parents = (new KnowledgeCategoryModel())->activeFlat();
        return view('dashboard/knowledge/categories/form', [
            'title'        => 'Tambah Kategori | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Kategori',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'categories',
            'category'     => null,
            'parents'      => $parents,
        ]);
    }

    public function store()
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $rules = [
            'name' => 'required|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }
        $model = new KnowledgeCategoryModel();
        $name = trim((string) $this->request->getPost('name'));
        $slug = $this->generateUniqueSlug($model, $name);
        $payload = [
            'parent_id'   => $this->request->getPost('parent_id') !== '' ? (int) $this->request->getPost('parent_id') : null,
            'name'        => $name,
            'slug'        => $slug,
            'description' => (string) $this->request->getPost('description'),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
        $model->insert($payload);
        return redirect()->to(route_to('dashboard.knowledge.categories'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(string $id): string
    {
        $model = new KnowledgeCategoryModel();
        $category = $model->find((int) $id);
        if ($category === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }
        $parents = $model->where('id !=', (int) $id)->orderBy('name', 'ASC')->findAll();
        return view('dashboard/knowledge/categories/form', [
            'title'        => 'Ubah Kategori | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Ubah Kategori',
            'formMode'     => 'edit',
            'categoryId'   => (int) $id,
            'category'     => $category,
            'parents'      => $parents,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'categories',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new KnowledgeCategoryModel();
        $category = $model->find((int) $id);
        if ($category === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }
        $rules = [
            'name' => 'required|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }
        $name = trim((string) $this->request->getPost('name'));
        $payload = [
            'parent_id'   => $this->request->getPost('parent_id') !== '' ? (int) $this->request->getPost('parent_id') : null,
            'name'        => $name,
            'slug'        => $this->generateUniqueSlug($model, $name, (int) $id),
            'description' => (string) $this->request->getPost('description'),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
        $model->update((int) $id, $payload);
        return redirect()->to(route_to('dashboard.knowledge.categories'))->with('success', 'Kategori diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new KnowledgeCategoryModel();
        $category = $model->find((int) $id);
        if ($category === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }
        try {
            $model->delete((int) $id);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->back()->with('error', 'Kategori masih dipakai konten lain.');
        }
        return redirect()->to(route_to('dashboard.knowledge.categories'))->with('success', 'Kategori dihapus.');
    }

    private function generateUniqueSlug(KnowledgeCategoryModel $model, string $name, ?int $ignoreId = null): string
    {
        $base = url_title($name, '-', true);
        if ($base === '') {
            $base = 'kategori-' . substr(md5(uniqid('', true)), 0, 6);
        }
        $slug = $base;
        $i = 1;
        while ($this->slugExists($model, $slug, $ignoreId)) {
            $i++;
            $slug = $base . '-' . $i;
        }
        return $slug;
    }

    private function slugExists(KnowledgeCategoryModel $model, string $slug, ?int $ignoreId): bool
    {
        $builder = $model->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * @return array<int, int> map category_id => jumlah konten
     */
    private function contentCountsByCategory(): array
    {
        $rows = $this->db->table('knowledge_article_categories')
            ->select('category_id, COUNT(*) AS cnt')
            ->groupBy('category_id')
            ->get()->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['category_id']] = (int) $r['cnt'];
        }
        return $out;
    }
}
