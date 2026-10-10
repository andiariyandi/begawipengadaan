<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Communication\NewsCategoryModel;
use App\Models\Communication\NewsModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class NewsPublicController extends BaseController
{
    private ConnectionInterface $db;
    private NewsModel $newsModel;
    private NewsCategoryModel $categoryModel;

    public function __construct()
    {
        $this->db            = db_connect();
        $this->newsModel     = new NewsModel();
        $this->categoryModel = new NewsCategoryModel();
    }

    public function index(): string
    {
        $keyword = $this->request->getGet('q');
        $rawNews = $this->newsModel->listNews('published', null, $keyword);

        $items = [];
        foreach ($rawNews as $n) {
            $items[] = [
                'title'    => $n['title'],
                'slug'     => $n['slug'],
                'text'     => $n['summary'] ?: mb_strimwidth(strip_tags((string) $n['content']), 0, 150, '...'),
                'category' => $n['category_name'] ?? 'Berita PBJ',
                'author'   => $n['author_name'] ?? 'BegawiPBJ',
                'date'     => ! empty($n['published_at']) ? date('d M Y', strtotime($n['published_at'])) : date('d M Y'),
                'cover'    => ! empty($n['cover_path']) ? base_url($n['cover_path']) : null,
            ];
        }

        return view('landing/pages/public_catalog', [
            'title'       => 'Berita PBJ | BegawiPBJ',
            'pageTitle'   => 'Berita PBJ Lampung',
            'catalogType' => 'news',
            'description' => 'Kabar terbaru, pengumuman, dan informasi terkini Pengadaan Barang/Jasa Provinsi Lampung.',
            'items'       => $items,
        ]);
    }

    public function category(string $slug): string
    {
        $category = $this->categoryModel->where('slug', $slug)->first();
        if (! $category) {
            throw PageNotFoundException::forPageNotFound('Kategori berita tidak ditemukan.');
        }

        $rawNews = $this->newsModel->listNews('published', (int) $category['id'], $this->request->getGet('q'));

        $items = [];
        foreach ($rawNews as $n) {
            $items[] = [
                'title'    => $n['title'],
                'slug'     => $n['slug'],
                'text'     => $n['summary'] ?: mb_strimwidth(strip_tags((string) $n['content']), 0, 150, '...'),
                'category' => $category['name'],
                'author'   => $n['author_name'] ?? 'BegawiPBJ',
                'date'     => ! empty($n['published_at']) ? date('d M Y', strtotime($n['published_at'])) : date('d M Y'),
                'cover'    => ! empty($n['cover_path']) ? base_url($n['cover_path']) : null,
            ];
        }

        return view('landing/pages/public_catalog', [
            'title'       => 'Berita ' . $category['name'] . ' | BegawiPBJ',
            'pageTitle'   => 'Kategori: ' . $category['name'],
            'catalogType' => 'news',
            'description' => 'Daftar berita dalam kategori ' . $category['name'] . '.',
            'items'       => $items,
        ]);
    }

    public function detail(string $slug): string
    {
        $news = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, nc.slug AS category_slug, u.name AS author_name, fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.slug', $slug)
            ->where('n.deleted_at', null)
            ->get()->getRowArray();

        if (! $news) {
            throw PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        // Increment view count safely
        try {
            $this->db->table('news')->where('id', $news['id'])->increment('view_count');
        } catch (\Throwable $e) {
            // Non-critical
        }

        return view('landing/pages/public_detail', [
            'title'       => $news['title'] . ' | BegawiPBJ',
            'pageTitle'   => 'Detail Berita',
            'itemTitle'   => $news['title'],
            'category'    => $news['category_name'] ?? 'Berita PBJ',
            'description' => $news['summary'] ?? '',
            'content'     => $news['content'] ?? '',
            'authorLabel' => $news['author_name'] ?? 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
            'dateLabel'   => ! empty($news['published_at']) ? date('d F Y', strtotime($news['published_at'])) : date('d F Y', strtotime($news['created_at'])),
            'statusLabel' => 'Publikasi Resmi',
            'viewCount'   => (int) ($news['view_count'] ?? 0) + 1,
            'coverUrl'    => ! empty($news['cover_path']) ? base_url($news['cover_path']) : null,
            'backUrl'     => 'berita',
        ]);
    }
}
