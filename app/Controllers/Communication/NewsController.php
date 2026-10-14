<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\NewsCategoryModel;
use App\Models\Communication\NewsModel;
use App\Services\Communication\CommunicationService;

class NewsController extends BaseController
{
    private NewsModel $newsModel;
    private NewsCategoryModel $categoryModel;
    private CommunicationService $service;

    public function __construct()
    {
        $this->newsModel     = new NewsModel();
        $this->categoryModel = new NewsCategoryModel();
        $this->service       = new CommunicationService();
    }

    public function index(): string
    {
        $status     = $this->request->getGet('status') ?: null;
        $keyword    = trim((string) ($this->request->getGet('q') ?: ''));
        $category   = $this->request->getGet('category') ? (int) $this->request->getGet('category') : null;
        $featured   = $this->request->getGet('featured') !== null && $this->request->getGet('featured') !== '' ? (int) $this->request->getGet('featured') : null;
        $viewMode   = trim((string) ($this->request->getGet('view') ?: 'grid'));
        $sort       = trim((string) ($this->request->getGet('sort') ?: 'newest'));
        $page       = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage    = 9;

        // Query dengan pagination lengkap
        $result = $this->newsModel->listNewsPaginated($status, $category, $keyword, $featured, $perPage, $page, $sort);

        $db = db_connect();
        $stats = [
            'total'       => (int) $db->table('news')->where('deleted_at', null)->countAllResults(),
            'published'   => (int) $db->table('news')->where('deleted_at', null)->where('workflow_status', 'published')->countAllResults(),
            'in_review'   => (int) $db->table('news')->where('deleted_at', null)->whereIn('workflow_status', ['in_review', 'submitted'])->countAllResults(),
            'draft'       => (int) $db->table('news')->where('deleted_at', null)->where('workflow_status', 'draft')->countAllResults(),
            'featured'    => (int) $db->table('news')->where('deleted_at', null)->where('is_featured', 1)->countAllResults(),
            'total_views' => (int) ($db->table('news')->where('deleted_at', null)->selectSum('view_count')->get()->getRow()->view_count ?? 0),
        ];

        return view('dashboard/communication/news/index', [
            'title'          => 'Berita & Publikasi | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Berita & Publikasi',
            'activeMenu'     => 'communication',
            'activeSubmenu'  => 'news',
            'items'          => $result['items'],
            'pagerData'      => $result,
            'stats'          => $stats,
            'categories'     => $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'filterStatus'   => $status,
            'filterKeyword'  => $keyword,
            'filterCategory' => $category,
            'filterFeatured' => $featured,
            'filterView'     => $viewMode,
            'filterSort'     => $sort,
        ]);
    }

    public function create(): string
    {
        $categories    = $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $headlineCount = (int) db_connect()->table('news')->where('deleted_at', null)->where('is_featured', 1)->countAllResults();

        return view('dashboard/communication/news/form', [
            'title'                 => 'Tambah Berita | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Tambah Berita',
            'formMode'              => 'create',
            'activeMenu'            => 'communication',
            'activeSubmenu'         => 'news',
            'item'                  => null,
            'newsCategories'        => $categories,
            'categories'            => $categories,
            'headlineCount'         => $headlineCount,
            'recommendedDimensions' => '1200 x 630 px (16:9 Landscape)',
            'maxFileSize'           => '5 MB',
            'allowedFormats'        => 'JPG, JPEG, PNG, WEBP',
        ]);
    }

    public function store()
    {
        $post  = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;

        $rules = [
            'title'           => 'required|max_length[255]',
            'category_id'     => 'permit_empty',
            'summary'         => 'permit_empty',
            'content'         => 'permit_empty',
            'workflow_status' => 'required',
            'visibility'      => 'required',
            'reading_time'    => 'permit_empty|max_length[50]',
            'tags'            => 'permit_empty|max_length[255]',
            'source_name'     => 'permit_empty|max_length[150]',
            'source_url'      => 'permit_empty|max_length[500]',
            'cover_caption'   => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa isian formulir: ' . implode(' ', $this->validator->getErrors()));
        }

        try {
            $id = $this->service->createNews($post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat berita: ' . $e->getMessage());
        }

        return redirect()->to(route_to('dashboard.communication.news.detail', (string) $id))
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function detail(string $id)
    {
        $row = $this->newsModel->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        return view('dashboard/communication/news/detail', [
            'title'         => 'Detail Berita | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Berita',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news',
            'item'          => $row,
            'newsId'        => (int) $id,
        ]);
    }

    public function edit(string $id)
    {
        $row = $this->newsModel->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        $categories    = $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $headlineCount = (int) db_connect()->table('news')->where('deleted_at', null)->where('is_featured', 1)->countAllResults();

        return view('dashboard/communication/news/form', [
            'title'                 => 'Ubah Berita | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Ubah Berita',
            'formMode'              => 'edit',
            'activeMenu'            => 'communication',
            'activeSubmenu'         => 'news',
            'item'                  => $row,
            'newsId'                => (int) $id,
            'newsCategories'        => $categories,
            'categories'            => $categories,
            'headlineCount'         => $headlineCount,
            'recommendedDimensions' => '1200 x 630 px (16:9 Landscape)',
            'maxFileSize'           => '5 MB',
            'allowedFormats'        => 'JPG, JPEG, PNG, WEBP',
        ]);
    }

    public function update(string $id)
    {
        $post  = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;

        $rules = [
            'title'           => 'required|max_length[255]',
            'category_id'     => 'permit_empty',
            'summary'         => 'permit_empty',
            'content'         => 'permit_empty',
            'workflow_status' => 'required',
            'visibility'      => 'required',
            'reading_time'    => 'permit_empty|max_length[50]',
            'tags'            => 'permit_empty|max_length[255]',
            'source_name'     => 'permit_empty|max_length[150]',
            'source_url'      => 'permit_empty|max_length[500]',
            'cover_caption'   => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa isian formulir: ' . implode(' ', $this->validator->getErrors()));
        }

        try {
            $this->service->updateNews((int) $id, $post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah berita: ' . $e->getMessage());
        }

        return redirect()->to(route_to('dashboard.communication.news.detail', (string) $id))
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        try {
            $this->service->deleteNews((int) $id, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('dashboard.communication.news'))
            ->with('success', 'Berita berhasil dihapus.');
    }

    public function categories(): string
    {
        $items = $this->service->listActiveCategoriesWithCount();

        return view('dashboard/communication/news_categories/index', [
            'title'         => 'Kategori Berita | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Kategori Berita',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news-categories',
            'items'         => $items,
        ]);
    }

    public function categoryCreate(): string
    {
        return view('dashboard/communication/news_categories/form', [
            'title'         => 'Tambah Kategori | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Kategori',
            'formMode'      => 'create',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'news-categories',
            'item'          => null,
        ]);
    }

    public function categoryStore()
    {
        $post = $this->request->getPost();
        $name = trim((string) ($post['name'] ?? ''));
        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Nama kategori wajib diisi.');
        }

        try {
            $id = $this->service->createCategory($post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function categoryEdit(string $id)
    {
        $row = $this->categoryModel->find((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return view('dashboard/communication/news_categories/form', [
            'title'         => 'Ubah Kategori | Dashboard BegawiPengadaan',
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
        try {
            $this->service->updateCategory((int) $id, $this->request->getPost(), (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function categoryDelete(string $id)
    {
        try {
            $this->service->deleteCategory((int) $id, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/dashboard/komunikasi/kategori-berita')->with('success', 'Kategori berhasil dihapus.');
    }

    public function listActiveCategoriesWithCount(): array
    {
        return $this->categoryModel->listAllWithCount();
    }
}
