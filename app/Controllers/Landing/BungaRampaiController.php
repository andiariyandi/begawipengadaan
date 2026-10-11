<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class BungaRampaiController extends BaseController
{
    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    private const ARTICLES_PER_PAGE = 9;
    private const FAQS_PER_PAGE     = 10;
    private const CASES_PER_PAGE    = 9;
    private const TEMPLATES_PER_PAGE= 9;
    private const MEDIA_PER_PAGE    = 9;
    private const GALLERY_PER_PAGE  = 12;
    private const VIDEOS_PER_PAGE   = 9;

    // ------------------------------------------------------------------
    // Beranda Bunga Rampai
    // ------------------------------------------------------------------
    public function index(): string
    {
        $stats = $this->getKnowledgeStats();

        // Featured items across articles, cases, and templates
        $featured = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, fa.path AS cover_path, fa.original_name AS file_original_name')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.workflow_status', 'published')
            ->where('ka.visibility', 'public')
            ->where('ka.deleted_at IS NULL', null, false)
            ->where('ka.featured', 1)
            ->orderBy('ka.published_at', 'DESC')
            ->orderBy('ka.updated_at', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        // If no items explicitly marked as featured, fallback to newest articles/cases
        if (empty($featured)) {
            $featured = $this->db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name, fa.path AS cover_path, fa.original_name AS file_original_name')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at IS NULL', null, false)
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.updated_at', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        }

        $articles    = $this->publishedArticlesByType('article', 6);
        $caseStudies = $this->publishedArticlesByType('case_study', 4);
        $templates   = $this->publishedArticlesByType('template', 4);
        $faqs        = $this->publishedArticlesByType('faq', 6);
        $mediaItems  = $this->publishedMedia(null, 6);

        return view('landing/pages/knowledge', [
            'title'           => 'Bunga Rampai PBJ Lampung | Pusat Pengetahuan & Praktik Baik',
            'metaDescription' => 'Pusat repositori pengetahuan, artikel teknis, studi kasus audit, FAQ pengadaan, template dokumen, dan multimedia Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords'    => 'Bunga Rampai, artikel PBJ, regulasi pengadaan, studi kasus PBJ, template dokumen pengadaan, FAQ pengadaan, BegawiPBJ Lampung',
            'pageTitle'       => 'Bunga Rampai',
            'description'     => 'Referensi pengetahuan Pengadaan Barang/Jasa terlengkap untuk aparatur pemerintah daerah, pokja pemilihan, penyedia, dan pemangku kepentingan.',
            'stats'           => $stats,
            'featured'        => $featured,
            'articles'        => $articles,
            'caseStudies'     => $caseStudies,
            'templates'       => $templates,
            'faqs'            => $faqs,
            'mediaItems'      => $mediaItems,
        ]);
    }

    // ------------------------------------------------------------------
    // Listing Sub-Menus (Catalog with Pagination & Filter)
    // ------------------------------------------------------------------
    public function articles(): string
    {
        $q        = $this->getQueryParam('q');
        $featured = $this->getQueryParam('featured');
        $page     = $this->getQueryInt('page');
        $perPage  = self::ARTICLES_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateArticles('article', $q, $featured, $page, $perPage, 'articles');

        return $this->renderCatalog(
            'Artikel PBJ',
            'Wawasan, analisis regulasi LKPP, opini ahli, dan praktik baik untuk mendukung proses Pengadaan Barang/Jasa yang akuntabel.',
            'articles',
            $items,
            $totalItems,
            $pager,
            $q,
            $featured,
            'knowledge_article',
            'knowledge_articles'
        );
    }

    public function faqs(): string
    {
        $q       = $this->getQueryParam('q');
        $page    = $this->getQueryInt('page');
        $perPage = self::FAQS_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateArticles('faq', $q, '', $page, $perPage, 'faqs');

        return $this->renderCatalog(
            'Tanya Jawab (FAQ) PBJ',
            'Jawaban ringkas, jelas, dan berlandaskan regulasi atas pertanyaan yang paling sering dihadapi PPK, Pokja, dan penyedia barang/jasa.',
            'faqs',
            $items,
            $totalItems,
            $pager,
            $q,
            '',
            'knowledge_faq',
            'knowledge_articles'
        );
    }

    public function caseStudies(): string
    {
        $q        = $this->getQueryParam('q');
        $featured = $this->getQueryParam('featured');
        $page     = $this->getQueryInt('page');
        $perPage  = self::CASES_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateArticles('case_study', $q, $featured, $page, $perPage, 'cases');

        return $this->renderCatalog(
            'Studi Kasus Lapangan',
            'Pembelajaran nyata dari dinamika, kendala lapangan, mitigasi risiko audit, penanganan sanggahan, dan solusi kepengadaan di Provinsi Lampung.',
            'cases',
            $items,
            $totalItems,
            $pager,
            $q,
            $featured,
            'knowledge_case',
            'knowledge_articles'
        );
    }

    public function templates(): string
    {
        $q        = $this->getQueryParam('q');
        $featured = $this->getQueryParam('featured');
        $page     = $this->getQueryInt('page');
        $perPage  = self::TEMPLATES_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateArticles('template', $q, $featured, $page, $perPage, 'templates');

        return $this->renderCatalog(
            'Template Dokumen PBJ',
            'Format baku KAK, kertas kerja HPS, SPK, kontrak, form evaluasi pokja, dan BAST siap unduh yang terstandardisasi dan adaptif.',
            'templates',
            $items,
            $totalItems,
            $pager,
            $q,
            $featured,
            'knowledge_template',
            'knowledge_articles'
        );
    }

    public function media(): string
    {
        $q       = $this->getQueryParam('q');
        $page    = $this->getQueryInt('page');
        $perPage = self::MEDIA_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateMedia(null, $q, $page, $perPage, 'media');

        return $this->renderCatalog(
            'Media & Infografik PBJ',
            'Infografik edukatif, diagram alir proses pemilihan, dan materi visual yang mempermudah pemahaman ekosistem pengadaan.',
            'media',
            $items,
            $totalItems,
            $pager,
            $q,
            '',
            'knowledge_media',
            'knowledge_media'
        );
    }

    public function videos(): string
    {
        $q       = $this->getQueryParam('q');
        $page    = $this->getQueryInt('page');
        $perPage = self::VIDEOS_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateMedia(['video_file', 'youtube'], $q, $page, $perPage, 'videos');

        return $this->renderCatalog(
            'Video Pembelajaran PBJ',
            'Koleksi video tutorial aplikasi SPSE, rekaman webinar bedah regulasi, dan diskusi teknis bersama narasumber ahli.',
            'videos',
            $items,
            $totalItems,
            $pager,
            $q,
            '',
            'knowledge_media',
            'knowledge_media'
        );
    }

    public function gallery(): string
    {
        $q       = $this->getQueryParam('q');
        $page    = $this->getQueryInt('page');
        $perPage = self::GALLERY_PER_PAGE;

        [$items, $totalItems, $pager] = $this->paginateMedia(['gallery', 'image'], $q, $page, $perPage, 'gallery');

        return $this->renderCatalog(
            'Galeri Dokumentasi Kegiatan',
            'Dokumentasi visual kolaborasi ekosistem PBJ Lampung: bimbingan teknis, forum pokja, workshop e-katalog, dan monev pengadaan.',
            'gallery',
            $items,
            $totalItems,
            $pager,
            $q,
            '',
            'knowledge_media',
            'knowledge_media'
        );
    }

    private function getQueryParam(string $key, string $default = ''): string
    {
        $request = $this->request ?? service('request');
        $val = $request->getGet($key);
        if (($val === null || $val === '') && isset($_GET[$key])) {
            $val = $_GET[$key];
        }
        return trim((string) ($val ?? $default));
    }

    private function getQueryInt(string $key, int $default = 1): int
    {
        $request = $this->request ?? service('request');
        $val = $request->getGet($key);
        if (($val === null || $val === '') && isset($_GET[$key])) {
            $val = $_GET[$key];
        }
        return max(1, (int) ($val ?: $default));
    }

    // ------------------------------------------------------------------
    // Detail Pages
    // ------------------------------------------------------------------
    public function articleDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('article', $slug);
        return $this->renderDetail('Artikel PBJ', 'articles', $row, 'knowledge_article');
    }

    public function caseStudyDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('case_study', $slug);
        return $this->renderDetail('Studi Kasus PBJ', 'cases', $row, 'knowledge_case');
    }

    public function templateDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('template', $slug);
        return $this->renderDetail('Template Dokumen PBJ', 'templates', $row, 'knowledge_template');
    }

    public function templateDownload(string $slug)
    {
        $row = $this->publishedArticleBySlug('template', $slug);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Template tidak ditemukan.');
        }

        if (empty($row['cover_file_id'])) {
            return redirect()->back()->with('error', 'File lampiran belum tersedia untuk template ini.');
        }

        $file = $this->db->table('file_assets')->where('id', (int) $row['cover_file_id'])->get()->getRowArray();
        if ($file === null) {
            return redirect()->back()->with('error', 'File template tidak ditemukan di sistem.');
        }

        $response = $this->response ?? response();

        $absolutePath = WRITEPATH . $file['path'];
        if (is_file($absolutePath)) {
            return $response->download($absolutePath, null)
                ->setFileName($file['original_name']);
        }

        // Graceful fallback for generated template content
        $content = "DOKUMEN TEMPLATE BEGAWIPBJ LAMPUNG\n";
        $content .= "Judul: " . ($row['title'] ?? '') . "\n";
        $content .= "Tanggal: " . date('d F Y') . "\n";
        $content .= "--------------------------------------------------------\n\n";
        $content .= "RINGKASAN:\n" . ($row['summary'] ?? '') . "\n\n";
        $content .= "PETUNJUK PENGGUNAAN:\n" . ($row['content'] ?? '') . "\n\n";
        $content .= "Disiapkan oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung.";

        $downloadFilename = ! empty($file['original_name']) ? $file['original_name'] : ($row['slug'] . '.txt');
        return $response->download($downloadFilename, $content);
    }

    // ------------------------------------------------------------------
    // Helpers — Statistics & Pagination
    // ------------------------------------------------------------------
    /**
     * Compute live counts for each knowledge category.
     *
     * @return array<string, int>
     */
    private function getKnowledgeStats(): array
    {
        $articleCounts = $this->db->table('knowledge_articles')
            ->select('type, COUNT(*) AS total')
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->where('deleted_at IS NULL', null, false)
            ->groupBy('type')
            ->get()->getResultArray();

        $map = [
            'article'    => 'total_articles',
            'faq'        => 'total_faqs',
            'case_study' => 'total_cases',
            'template'   => 'total_templates',
        ];

        $stats = [
            'total_articles'  => 0,
            'total_faqs'      => 0,
            'total_cases'     => 0,
            'total_templates' => 0,
            'total_media'     => 0,
            'total_videos'    => 0,
            'total_gallery'   => 0,
            'total_all'       => 0,
        ];

        foreach ($articleCounts as $ac) {
            $t = (string) $ac['type'];
            if (isset($map[$t])) {
                $stats[$map[$t]] = (int) $ac['total'];
            }
        }

        // Media counts
        $mediaCounts = $this->db->table('knowledge_media')
            ->select('type, COUNT(*) AS total')
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->groupBy('type')
            ->get()->getResultArray();

        $totalMediaAll = 0;
        foreach ($mediaCounts as $mc) {
            $count = (int) $mc['total'];
            $totalMediaAll += $count;
            if (in_array($mc['type'], ['video_file', 'youtube'], true)) {
                $stats['total_videos'] += $count;
            }
            if (in_array($mc['type'], ['gallery', 'image'], true)) {
                $stats['total_gallery'] += $count;
            }
        }
        $stats['total_media'] = $totalMediaAll;

        $stats['total_all'] = $stats['total_articles']
            + $stats['total_faqs']
            + $stats['total_cases']
            + $stats['total_templates']
            + $stats['total_media'];

        return $stats;
    }

    /**
     * Paginated articles/faqs/cases/templates query with search and filters.
     *
     * @return array{0: array<int, array<string, mixed>>, 1: int, 2: \CodeIgniter\Pager\Pager}
     */
    private function paginateArticles(
        string $type,
        string $q,
        string $featured,
        int $page,
        int $perPage,
        string $pagerGroup = 'default'
    ): array {
        $builder = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, fa.path AS cover_path, fa.original_name AS file_original_name, fa.size_bytes AS file_size_bytes, fa.extension AS file_extension')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.workflow_status', 'published')
            ->where('ka.visibility', 'public')
            ->where('ka.deleted_at IS NULL', null, false)
            ->where('ka.type', $type);

        if ($q !== '') {
            $builder->groupStart()
                ->like('ka.title', $q)
                ->orLike('ka.summary', $q)
                ->orLike('ka.content', $q)
            ->groupEnd();
        }

        if ($featured === '1') {
            $builder->where('ka.featured', 1);
        }

        $totalItems = $builder->countAllResults(false);
        $offset     = ($page - 1) * $perPage;

        $items = $builder
            ->orderBy('ka.featured', 'DESC')
            ->orderBy('ka.published_at', 'DESC')
            ->orderBy('ka.updated_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        // Enrich items with computed metrics (e.g. read time)
        foreach ($items as &$item) {
            $text = strip_tags(($item['content'] ?? '') . ' ' . ($item['summary'] ?? ''));
            $wordCount = str_word_count($text);
            $item['reading_time'] = max(1, (int) ceil($wordCount / 180));
        }
        unset($item);

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $totalItems, 'default_full', 0, $pagerGroup);

        return [$items, $totalItems, $pager];
    }

    /**
     * Paginated media query with search and filters.
     *
     * @param string|array<string>|null $types
     * @return array{0: array<int, array<string, mixed>>, 1: int, 2: \CodeIgniter\Pager\Pager}
     */
    private function paginateMedia(
        $types,
        string $q,
        int $page,
        int $perPage,
        string $pagerGroup = 'default'
    ): array {
        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.size_bytes AS file_size_bytes, ft.original_name AS thumb_original_name, ft.path AS thumb_path')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->where('km.workflow_status', 'published')
            ->where('km.visibility', 'public');

        if ($types !== null) {
            $builder->whereIn('km.type', (array) $types);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('km.title', $q)
                ->orLike('km.caption', $q)
                ->orLike('km.alt_text', $q)
            ->groupEnd();
        }

        $totalItems = $builder->countAllResults(false);
        $offset     = ($page - 1) * $perPage;

        $items = $builder
            ->orderBy('km.sort_order', 'ASC')
            ->orderBy('km.published_at', 'DESC')
            ->orderBy('km.updated_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $totalItems, 'default_full', 0, $pagerGroup);

        return [$items, $totalItems, $pager];
    }

    private function publishedArticlesByType(string $type, int $limit = 6): array
    {
        $items = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, fa.path AS cover_path, fa.original_name AS file_original_name, fa.size_bytes AS file_size_bytes, fa.extension AS file_extension')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.workflow_status', 'published')
            ->where('ka.visibility', 'public')
            ->where('ka.deleted_at IS NULL', null, false)
            ->where('ka.type', $type)
            ->orderBy('ka.featured', 'DESC')
            ->orderBy('ka.published_at', 'DESC')
            ->orderBy('ka.updated_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        foreach ($items as &$item) {
            $text = strip_tags(($item['content'] ?? '') . ' ' . ($item['summary'] ?? ''));
            $wordCount = str_word_count($text);
            $item['reading_time'] = max(1, (int) ceil($wordCount / 180));
        }
        unset($item);

        return $items;
    }

    private function publishedArticleBySlug(string $type, string $slug): ?array
    {
        $row = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, fa.path AS cover_path, fa.original_name AS file_original_name, fa.size_bytes AS file_size_bytes, fa.extension AS file_extension')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.type', $type)
            ->where('ka.slug', $slug)
            ->where('ka.workflow_status', 'published')
            ->where('ka.visibility', 'public')
            ->where('ka.deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if (! $row) {
            return null;
        }

        $text = strip_tags(($row['content'] ?? '') . ' ' . ($row['summary'] ?? ''));
        $row['reading_time'] = max(1, (int) ceil(str_word_count($text) / 180));

        return $row;
    }

    private function publishedMedia($types = null, int $limit = 6): array
    {
        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, ft.original_name AS thumb_original_name, ft.path AS thumb_path')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->where('km.workflow_status', 'published')
            ->where('km.visibility', 'public')
            ->orderBy('km.sort_order', 'ASC')
            ->orderBy('km.published_at', 'DESC')
            ->limit($limit);

        if ($types !== null) {
            $builder->whereIn('km.type', (array) $types);
        }

        return $builder->get()->getResultArray();
    }

    // ------------------------------------------------------------------
    // Render helpers
    // ------------------------------------------------------------------
    private function renderCatalog(
        string $pageTitle,
        string $description,
        string $catalogType,
        array $items,
        int $totalItems,
        $pager,
        string $q,
        string $featuredFilter,
        string $bookmarkType,
        string $bookmarkSourceTable
    ): string {
        $stats = $this->getKnowledgeStats();

        return view('landing/pages/knowledge_catalog', [
            'title'               => $pageTitle . ' | Bunga Rampai BegawiPBJ Lampung',
            'metaDescription'     => $description,
            'metaKeywords'        => 'Bunga Rampai, ' . strtolower($pageTitle) . ', BegawiPBJ Lampung, referensi pengadaan',
            'pageTitle'           => $pageTitle,
            'catalogType'         => $catalogType,
            'description'         => $description,
            'items'               => $items,
            'totalItems'          => $totalItems,
            'pager'               => $pager,
            'q'                   => $q,
            'featuredFilter'      => $featuredFilter,
            'stats'               => $stats,
            'bookmarkType'        => $bookmarkType,
            'bookmarkSourceTable' => $bookmarkSourceTable,
        ]);
    }

    private function renderDetail(string $pageTitle, string $catalogType, ?array $row, string $bookmarkType)
    {
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Konten Bunga Rampai tidak ditemukan.');
        }

        $related = [];
        if (in_array($bookmarkType, ['knowledge_article', 'knowledge_case', 'knowledge_template'], true)) {
            $related = $this->db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name, fa.path AS cover_path')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.type', $row['type'])
                ->where('ka.id !=', (int) $row['id'])
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at IS NULL', null, false)
                ->orderBy('ka.published_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();

            foreach ($related as &$rel) {
                $text = strip_tags(($rel['content'] ?? '') . ' ' . ($rel['summary'] ?? ''));
                $rel['reading_time'] = max(1, (int) ceil(str_word_count($text) / 180));
            }
            unset($rel);
        }

        $fileMeta = null;
        if (! empty($row['cover_file_id'])) {
            $fileMeta = $this->db->table('file_assets')->where('id', (int) $row['cover_file_id'])->get()->getRowArray() ?: null;
        }

        $stats = $this->getKnowledgeStats();

        return view('landing/pages/knowledge_detail', [
            'title'           => ($row['title'] ?? 'Bunga Rampai') . ' | Bunga Rampai BegawiPBJ Lampung',
            'metaDescription' => ! empty($row['summary']) ? $row['summary'] : ('Informasi ' . strtolower($pageTitle) . ' pada Bunga Rampai BegawiPBJ.'),
            'metaKeywords'    => 'Bunga Rampai, BegawiPBJ, ' . strtolower($pageTitle) . ', pengadaan barang jasa lampung',
            'pageTitle'       => $pageTitle,
            'itemTitle'       => $row['title'] ?? 'Tanpa Judul',
            'catalogType'     => $catalogType,
            'item'            => $row,
            'related'         => $related,
            'fileMeta'        => $fileMeta,
            'stats'           => $stats,
            'bookmarkType'    => $bookmarkType,
        ]);
    }
}