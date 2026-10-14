<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\TagModel;

class TagController extends BaseKnowledgeController
{
    public function index(): string
    {
        $items = (new TagModel())->listAll();
        return view('dashboard/knowledge/tags/index', [
            'title'        => 'Tag Bunga Rampai | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tag',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'tags',
            'items'        => $items,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/tags/form', [
            'title'        => 'Tambah Tag | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Tag',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'tags',
            'tag'          => null,
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
            return redirect()->back()->withInput()->with('error', 'Nama tag wajib diisi.');
        }
        $model = new TagModel();
        $name = trim((string) $this->request->getPost('name'));
        $slug = $this->generateUniqueSlug($model, $name);
        $model->insert([
            'name' => $name,
            'slug' => $slug,
        ]);
        return redirect()->to(route_to('dashboard.knowledge.tags'))->with('success', 'Tag berhasil ditambahkan.');
    }

    public function edit(string $id): string
    {
        $tag = (new TagModel())->find((int) $id);
        if ($tag === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }
        return view('dashboard/knowledge/tags/form', [
            'title'        => 'Ubah Tag | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Ubah Tag',
            'formMode'     => 'edit',
            'tagId'        => (int) $id,
            'tag'          => $tag,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'tags',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new TagModel();
        $tag = $model->find((int) $id);
        if ($tag === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }
        $rules = [
            'name' => 'required|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama tag wajib diisi.');
        }
        $name = trim((string) $this->request->getPost('name'));
        $model->update((int) $id, [
            'name' => $name,
            'slug' => $this->generateUniqueSlug($model, $name, (int) $id),
        ]);
        return redirect()->to(route_to('dashboard.knowledge.tags'))->with('success', 'Tag diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new TagModel();
        $tag = $model->find((int) $id);
        if ($tag === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }
        try {
            $model->delete((int) $id);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->back()->with('error', 'Tag masih dipakai artikel lain.');
        }
        return redirect()->to(route_to('dashboard.knowledge.tags'))->with('success', 'Tag dihapus.');
    }

    private function generateUniqueSlug(TagModel $model, string $name, ?int $ignoreId = null): string
    {
        $base = url_title($name, '-', true);
        if ($base === '') {
            $base = 'tag-' . substr(md5(uniqid('', true)), 0, 6);
        }
        $slug = $base;
        $i = 1;
        while ($this->slugExists($model, $slug, $ignoreId)) {
            $i++;
            $slug = $base . '-' . $i;
        }
        return $slug;
    }

    private function slugExists(TagModel $model, string $slug, ?int $ignoreId): bool
    {
        $builder = $model->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() > 0;
    }
}
