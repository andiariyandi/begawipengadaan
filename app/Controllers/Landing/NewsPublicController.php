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

    /**
     * Index listing for Berita PBJ
     */
    public function index(): string
    {
        try {
            (new \App\Services\VisitorStatisticService())->recordHit('berita');
        } catch (\Throwable) {
        }

        $keyword      = trim((string) ($this->request->getGet('q') ?: ''));
        $categorySlug = trim((string) ($this->request->getGet('kategori') ?: $this->request->getGet('category') ?: ''));
        $sort         = trim((string) ($this->request->getGet('sort') ?: 'newest'));
        $page         = max(1, (int) ($this->request->getGet('page') ?: 1));

        return $this->renderCatalog($keyword, $categorySlug, $sort, $page);
    }

    /**
     * Category filtered listing for Berita PBJ (/berita/kategori/{slug})
     */
    public function category(string $slug): string
    {
        $category = $this->categoryModel->where('slug', $slug)->first();
        if (! $category) {
            throw PageNotFoundException::forPageNotFound('Kategori berita tidak ditemukan: ' . esc($slug));
        }

        $keyword = trim((string) ($this->request->getGet('q') ?: ''));
        $sort    = trim((string) ($this->request->getGet('sort') ?: 'newest'));
        $page    = max(1, (int) ($this->request->getGet('page') ?: 1));

        return $this->renderCatalog($keyword, $slug, $sort, $page, $category);
    }

    /**
     * Shared catalog renderer with pagination, metrics, filters, and sidebar data
     */
    private function renderCatalog(string $keyword, string $categorySlug, string $sort, int $page, ?array $currentCategory = null): string
    {
        $perPage = 6;

        // Base builder for published public news
        $builder = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, nc.slug AS category_slug, 
                      COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS author_name, 
                      fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->where('n.workflow_status', 'published');

        // Filter by category
        if (! empty($categorySlug)) {
            $builder->where('nc.slug', $categorySlug);
        }

        // Filter by search keyword
        if (! empty($keyword)) {
            $builder->groupStart()
                ->like('n.title', $keyword)
                ->orLike('n.summary', $keyword)
                ->orLike('n.content', $keyword)
                ->orLike('nc.name', $keyword)
                ->groupEnd();
        }

        // Count total matching items
        $countBuilder = clone $builder;
        $totalItems   = (int) $countBuilder->countAllResults(false);
        $totalPages   = max(1, (int) ceil($totalItems / $perPage));
        $currentPage  = min($page, $totalPages);
        $offset       = ($currentPage - 1) * $perPage;

        // Apply sorting
        match ($sort) {
            'popular' => $builder->orderBy('n.view_count', 'DESC')->orderBy('n.published_at', 'DESC'),
            'oldest'  => $builder->orderBy('n.published_at', 'ASC')->orderBy('n.id', 'ASC'),
            default   => $builder->orderBy('n.published_at', 'DESC')->orderBy('n.id', 'DESC'),
        };

        $rawItems = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Format items with reading time & date
        $items = array_map(function ($row) {
            $words = str_word_count(strip_tags((string) ($row['content'] ?? '')));
            $readTime = max(1, (int) ceil($words / 180)) . ' menit';

            return [
                'id'            => (int) $row['id'],
                'title'         => $row['title'],
                'slug'          => $row['slug'],
                'summary'       => $row['summary'] ?: mb_strimwidth(strip_tags((string) $row['content']), 0, 160, '...'),
                'content'       => $row['content'],
                'category_name' => $row['category_name'] ?? 'Berita PBJ',
                'category_slug' => $row['category_slug'] ?? 'umum',
                'author_name'   => $row['author_name'] ?: 'Biro PBJ Setda Lampung',
                'published_at'  => $row['published_at'] ?? $row['created_at'],
                'date_formatted' => ! empty($row['published_at']) ? date('d M Y', strtotime($row['published_at'])) : date('d M Y'),
                'view_count'    => (int) ($row['view_count'] ?? 0),
                'read_time'     => $readTime,
                'cover_url'     => ! empty($row['cover_file_id']) ? site_url('file/' . (int) $row['cover_file_id']) : (! empty($row['cover_path']) ? (str_starts_with($row['cover_path'], 'http') ? $row['cover_path'] : base_url($row['cover_path'])) : null),
            ];
        }, $rawItems);

        // Featured / Headline News (respect spotlight_mode & spotlight_limit settings)
        $headlineArticles = [];
        $spotlightMode  = (string) (setting('news.spotlight_mode') ?? 'all');
        $spotlightLimit = (int) (setting('news.spotlight_limit') ?? 5);
        if ($spotlightLimit < 3) {
            $spotlightLimit = 3;
        } elseif ($spotlightLimit > 15) {
            $spotlightLimit = 15;
        }

        if ($currentPage === 1 && empty($keyword) && empty($categorySlug)) {
            $hBuilder = $this->db->table('news n')
                ->select('n.id, n.title, n.slug, n.summary, n.content, n.published_at, n.view_count, n.reading_time, n.cover_file_id, n.carousel_enabled, n.carousel_order, n.is_featured, nc.name AS category_name, nc.slug AS category_slug, fa.path AS cover_path')
                ->join('news_categories nc', 'nc.id = n.category_id', 'left')
                ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
                ->where('n.deleted_at', null)
                ->where('n.workflow_status', 'published');

            if ($spotlightMode === 'featured') {
                $hBuilder->where('n.is_featured', 1);
            } elseif ($spotlightMode !== 'all' && ! empty($spotlightMode)) {
                $hBuilder->where('nc.slug', $spotlightMode);
            }

            $rawHeadlines = $hBuilder
                ->orderBy('n.carousel_enabled', 'DESC')
                ->orderBy('CASE WHEN n.carousel_order IS NULL THEN 9999 ELSE n.carousel_order END', 'ASC', false)
                ->orderBy('n.is_featured', 'DESC')
                ->orderBy('n.published_at', 'DESC')
                ->limit($spotlightLimit)
                ->get()->getResultArray();

            // If no articles match the spotlight filter, fallback to latest published items
            if (empty($rawHeadlines) && ! empty($rawItems)) {
                $rawHeadlines = array_slice($rawItems, 0, min($spotlightLimit, count($rawItems)));
            }

            $headlineIds = array_column($rawHeadlines, 'id');

            $headlineArticles = array_map(function ($row) {
                $words = str_word_count(strip_tags((string) ($row['content'] ?? '')));
                $readTime = ! empty($row['reading_time']) ? $row['reading_time'] : (max(1, (int) ceil($words / 180)) . ' menit');

                $coverUrl = null;
                if (! empty($row['cover_file_id'])) {
                    $coverUrl = site_url('file/' . (int) $row['cover_file_id']);
                } elseif (! empty($row['cover_path'])) {
                    $coverUrl = str_starts_with($row['cover_path'], 'http') ? $row['cover_path'] : base_url($row['cover_path']);
                }

                return [
                    'id'            => (int) $row['id'],
                    'title'         => $row['title'],
                    'slug'          => $row['slug'],
                    'summary'       => $row['summary'] ?: mb_strimwidth(strip_tags((string) $row['content']), 0, 180, '...'),
                    'content'       => $row['content'],
                    'category_name' => $row['category_name'] ?? 'Berita PBJ',
                    'category_slug' => $row['category_slug'] ?? 'umum',
                    'published_at'  => $row['published_at'] ?? date('Y-m-d H:i:s'),
                    'date_formatted' => ! empty($row['published_at']) ? date('d M Y', strtotime($row['published_at'])) : date('d M Y'),
                    'view_count'    => (int) ($row['view_count'] ?? 0),
                    'read_time'     => $readTime,
                    'cover_url'     => $coverUrl,
                ];
            }, $rawHeadlines);

            // Filter out headline items from regular grid so they don't duplicate on page 1
            $items = array_filter($items, function ($item) use ($headlineIds) {
                return ! in_array($item['id'], $headlineIds, true);
            });
            $items = array_values($items);
        }

        $headlineArticle = ! empty($headlineArticles) ? $headlineArticles[0] : null;

        // Active Categories with item counts
        $categoriesWithCount = $this->db->table('news_categories nc')
            ->select('nc.id, nc.name, nc.slug, nc.description, COUNT(n.id) AS news_count')
            ->join('news n', 'n.category_id = nc.id AND n.deleted_at IS NULL AND n.workflow_status = "published"', 'left')
            ->where('nc.is_active', 1)
            ->groupBy('nc.id')
            ->orderBy('news_count', 'DESC')
            ->orderBy('nc.name', 'ASC')
            ->get()->getResultArray();

        // Popular / Trending News (top 4)
        $popularNews = $this->db->table('news n')
            ->select('n.id, n.title, n.slug, n.summary, n.published_at, n.view_count, nc.name AS category_name, fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->where('n.workflow_status', 'published')
            ->orderBy('n.view_count', 'DESC')
            ->orderBy('n.published_at', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        // Metrics ribbon
        $stats = [
            'total_news'       => (int) $this->db->table('news')->where('deleted_at', null)->where('workflow_status', 'published')->countAllResults(),
            'total_categories' => (int) $this->db->table('news_categories')->where('is_active', 1)->countAllResults(),
            'total_views'      => (int) ($this->db->table('news')->where('deleted_at', null)->selectSum('view_count')->get()->getRow()->view_count ?? 0),
            'this_month'       => (int) $this->db->table('news')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->where('published_at >=', date('Y-m-01 00:00:00'))
                ->countAllResults(),
        ];

        // Page title and meta description
        $pageTitle = 'Berita PBJ Lampung';
        $metaDesc  = 'Pusat publikasi berita, pengumuman, dan kabar terkini pengadaan barang dan jasa Pemerintah Provinsi Lampung.';
        if ($currentCategory) {
            $pageTitle = 'Berita: ' . $currentCategory['name'];
            $metaDesc  = 'Kumpulan kabar dan publikasi pengadaan untuk kategori ' . $currentCategory['name'] . ' Provinsi Lampung.';
        } elseif (! empty($keyword)) {
            $pageTitle = 'Pencarian: "' . esc($keyword) . '"';
        }

        return view('landing/pages/news', [
            'title'               => $pageTitle . ' | BegawiPengadaan Provinsi Lampung',
            'metaDescription'     => $metaDesc,
            'pageTitle'           => $pageTitle,
            'items'               => $items,
            'headlineArticle'     => $headlineArticle,
            'headlineArticles'    => $headlineArticles,
            'totalItems'          => $totalItems,
            'currentPage'         => $currentPage,
            'totalPages'          => $totalPages,
            'perPage'             => $perPage,
            'keyword'             => $keyword,
            'selectedCategory'    => $categorySlug,
            'currentCategory'     => $currentCategory,
            'sort'                => $sort,
            'categories'          => $categoriesWithCount,
            'popularNews'         => $popularNews,
            'stats'               => $stats,
        ]);
    }

    /**
     * Detail page for a news article (/berita/{slug})
     */
    public function detail(string $slug): string
    {
        $news = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, nc.slug AS category_slug, 
                      COALESCE(u.name, u.username, "Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung") AS author_name, 
                      fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.slug', $slug)
            ->where('n.deleted_at', null)
            ->get()->getRowArray();

        if (! $news) {
            throw PageNotFoundException::forPageNotFound('Berita pengadaan tidak ditemukan: ' . esc($slug));
        }

        // Increment view count: setiap kali berita dibuka/dibaca, tambahkan +1 (bukan user unik)
        try {
            $this->db->query('UPDATE news SET view_count = COALESCE(view_count, 0) + 1 WHERE id = ?', [(int) $news['id']]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal increment view_count: ' . $e->getMessage());
        }

        $news['view_count'] = (int) ($news['view_count'] ?? 0) + 1;

        // Reading time
        $words = str_word_count(strip_tags((string) ($news['content'] ?? '')));
        $readTime = max(1, (int) ceil($words / 180)) . ' menit';

        // Related news from same category or latest
        $relatedQuery = $this->db->table('news n')
            ->select('n.id, n.title, n.slug, n.summary, n.published_at, n.view_count, nc.name AS category_name, fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->where('n.workflow_status', 'published')
            ->where('n.id !=', $news['id']);

        if (! empty($news['category_id'])) {
            $relatedQuery->where('n.category_id', $news['category_id']);
        }
        $relatedNews = $relatedQuery->orderBy('n.published_at', 'DESC')->limit(3)->get()->getResultArray();

        // If not enough related in same category, fill with other published news
        if (count($relatedNews) < 3) {
            $excludeIds = array_merge([(int) $news['id']], array_column($relatedNews, 'id'));
            $fillers = $this->db->table('news n')
                ->select('n.id, n.title, n.slug, n.summary, n.published_at, n.view_count, nc.name AS category_name, fa.path AS cover_path')
                ->join('news_categories nc', 'nc.id = n.category_id', 'left')
                ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
                ->where('n.deleted_at', null)
                ->where('n.workflow_status', 'published')
                ->whereNotIn('n.id', $excludeIds)
                ->orderBy('n.published_at', 'DESC')
                ->limit(3 - count($relatedNews))
                ->get()->getResultArray();
            $relatedNews = array_merge($relatedNews, $fillers);
        }

        // Previous and Next news links
        $prevNews = $this->db->table('news')
            ->select('title, slug')
            ->where('deleted_at', null)
            ->where('workflow_status', 'published')
            ->where('published_at <', $news['published_at'] ?? $news['created_at'])
            ->orderBy('published_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $nextNews = $this->db->table('news')
            ->select('title, slug')
            ->where('deleted_at', null)
            ->where('workflow_status', 'published')
            ->where('published_at >', $news['published_at'] ?? $news['created_at'])
            ->orderBy('published_at', 'ASC')
            ->limit(1)
            ->get()->getRowArray();

        // Popular news for sidebar
        $popularNews = $this->db->table('news n')
            ->select('n.id, n.title, n.slug, n.summary, n.published_at, n.view_count, nc.name AS category_name, fa.path AS cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->where('n.workflow_status', 'published')
            ->orderBy('n.view_count', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        // Categories list with counts
        $categoriesWithCount = $this->db->table('news_categories nc')
            ->select('nc.id, nc.name, nc.slug, COUNT(n.id) AS news_count')
            ->join('news n', 'n.category_id = nc.id AND n.deleted_at IS NULL AND n.workflow_status = "published"', 'left')
            ->where('nc.is_active', 1)
            ->groupBy('nc.id')
            ->orderBy('nc.name', 'ASC')
            ->get()->getResultArray();

        return view('landing/pages/news_detail', [
            'title'           => $news['title'] . ' | Berita PBJ Lampung',
            'metaDescription' => $news['summary'] ?: mb_strimwidth(strip_tags((string) $news['content']), 0, 160, '...'),
            'pageTitle'       => 'Detail Berita',
            'news'            => $news,
            'readTime'        => $readTime,
            'relatedNews'     => $relatedNews,
            'prevNews'        => $prevNews,
            'nextNews'        => $nextNews,
            'popularNews'     => $popularNews,
            'categories'      => $categoriesWithCount,
            'shareUrl'        => site_url('berita/' . $news['slug']),
        ]);
    }
}
