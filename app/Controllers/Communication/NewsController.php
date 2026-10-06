<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\NewsModel;
use App\Services\Communication\CommunicationService;

class NewsController extends BaseController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $category = $this->request->getGet('category') ? (int) $this->request->getGet('category') : null;

        $items = (new NewsModel())->listNews($status, $category, $keyword);

        return view('dashboard/communication/news/index', [
            'title'         => 'Berita | Dashboard BegawiPBJ',
            'pageTitle'     => 'Berita',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news',
            'items'         => $items,
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
            'filterCategory'=> $category,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/communication/news/form', [
            'title'         => 'Tambah Berita | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Berita',
            'formMode'      => 'create',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news',
            'item'          => null,
        ]);
    }

    public function store()
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;

        $rules = [
            'title'    => 'required|max_length[255]',
            'summary'  => 'permit_empty',
            'content'  => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa isian formulir.');
        }

        try {
            $id = $svc->createNews($post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat berita: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.news.detail', (string) $id))
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function detail(string $id)
    {
        $row = (new NewsModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }
        return view('dashboard/communication/news/detail', [
            'title'         => 'Detail Berita | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Berita',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news',
            'item'          => $row,
            'newsId'        => (int) $id,
        ]);
    }

    public function edit(string $id)
    {
        $row = (new NewsModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }
        return view('dashboard/communication/news/form', [
            'title'         => 'Ubah Berita | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Berita',
            'formMode'      => 'edit',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news',
            'item'          => $row,
            'newsId'        => (int) $id,
        ]);
    }

    public function update(string $id)
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;

        $rules = [
            'title'    => 'required|max_length[255]',
            'summary'  => 'permit_empty',
            'content'  => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa isian formulir.');
        }

        try {
            $svc->updateNews((int) $id, $post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah berita: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.news.detail', (string) $id))
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $svc = new CommunicationService();
        try {
            $svc->deleteNews((int) $id, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.news'))
            ->with('success', 'Berita dihapus.');
    }

    public function categories(): string
    {
        $svc = new CommunicationService();
        $items = $svc->listActiveCategoriesWithCount();
        return view('dashboard/communication/news_categories/index', [
            'title'         => 'Kategori Berita | Dashboard BegawiPBJ',
            'pageTitle'     => 'Kategori Berita',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news-categories',
            'items'         => $items,
        ]);
    }

    public function categoryCreate(): string
    {
        return view('dashboard/communication/news_categories/form', [
            'title'         => 'Tambah Kategori | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Kategori',
            'formMode'      => 'create',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news-categories',
            'item'          => null,
        ]);
    }

    public function categoryStore()
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $name = trim((string) ($post['name'] ?? ''));
        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }
        try {
            $id = $svc->createCategory($post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori ditambahkan.');
    }

    public function categoryEdit(string $id)
    {
        $row = (new \App\Models\Communication\NewsCategoryModel())->find((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }
        return view('dashboard/communication/news_categories/form', [
            'title'         => 'Ubah Kategori | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Kategori',
            'formMode'      => 'edit',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news-categories',
            'item'          => $row,
            'catId'         => (int) $id,
        ]);
    }

    public function categoryUpdate(string $id)
    {
        $svc = new CommunicationService();
        try {
            $svc->updateCategory((int) $id, $this->request->getPost(), (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori diperbarui.');
    }

    public function categoryDelete(string $id)
    {
        $svc = new CommunicationService();
        try {
            $svc->deleteCategory((int) $id, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori dihapus.');
    }

    /**
     * Helper: list active categories with count.
     */
    public function listActiveCategoriesWithCount(): array
    {
        return (new \App\Models\Communication\NewsCategoryModel())->listAllWithCount();
    }
}