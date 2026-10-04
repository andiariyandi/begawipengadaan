<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class BungaRampaiController extends BaseController
{
    private const ARTICLES_LIMIT  = 9;
    private const CASE_LIMIT      = 6;
    private const MEDIA_LIMIT     = 9;
    private const LIST_LIMIT      = 12;

    // ------------------------------------------------------------------
    // Beranda
    // ------------------------------------------------------------------
    public function index(): string
    {
        $articles  = $this->publishedArticles(6);
        $cases     = $this->publishedArticlesByType('case_study', self::CASE_LIMIT);
        $mediaAll  = $this->publishedMedia(null, self::MEDIA_LIMIT);
        $featuredArticles = array_filter($articles, static fn ($a) => (int) ($a['featured'] ?? 0) === 1);

        return view('landing/pages/knowledge', [
            'title'           => 'Bunga Rampai | BegawiPBJ',
            'metaDescription' => 'Pusat pengetahuan, artikel, studi kasus, FAQ, template, dan media Pengadaan Barang/Jasa.',
            'metaKeywords'    => 'Bunga Rampai, artikel PBJ, studi kasus PBJ, BegawiPBJ',
            'pageTitle'       => 'Bunga Rampai',
            'description'     => 'Referensi pengetahuan Pengadaan Barang/Jasa untuk aparatur dan pemangku kepentingan.',
            'featured'        => array_values(array_slice($featuredArticles, 0, 4)),
            'articles'        => $articles,
            'caseStudies'     => $cases,
            'mediaItems'      => $mediaAll,
        ]);
    }

    // ------------------------------------------------------------------
    // Listing
    // ------------------------------------------------------------------
    public function articles(): string
    {
        $items = $this->publishedArticlesByType('article');
        return $this->renderCatalog(
            'Artikel PBJ',
            'Wawasan dan praktik baik untuk mendukung proses Pengadaan Barang/Jasa.',
            'articles',
            $items,
            'knowledge_article',
            'knowledge_articles'
        );
    }

    public function faqs(): string
    {
        $items = $this->publishedArticlesByType('faq');
        return $this->renderCatalog(
            'Tanya Jawab PBJ',
            'Jawaban singkat untuk pertanyaan yang sering muncul.',
            'faqs',
            $items,
            'knowledge_faq',
            'knowledge_articles'
        );
    }

    public function caseStudies(): string
    {
        $items = $this->publishedArticlesByType('case_study');
        return $this->renderCatalog(
            'Studi Kasus',
            'Pelajaran dari pengalaman dan tantangan Pengadaan Barang/Jasa di lapangan.',
            'cases',
            $items,
            'knowledge_case',
            'knowledge_articles'
        );
    }

    public function templates(): string
    {
        $items = $this->publishedArticlesByType('template');
        return $this->renderCatalog(
            'Template Dokumen',
            'Contoh dokumen pendukung proses PBJ.',
            'templates',
            $items,
            'knowledge_template',
            'knowledge_articles'
        );
    }

    public function media(): string
    {
        $items = $this->publishedMedia(null, self::LIST_LIMIT);
        return $this->renderCatalog(
            'Media PBJ',
            'Infografik dan materi visual yang mudah dipahami.',
            'media',
            $items,
            'knowledge_media',
            'knowledge_media'
        );
    }

    public function videos(): string
    {
        $items = $this->publishedMedia(['video_file', 'youtube'], self::LIST_LIMIT);
        return $this->renderCatalog(
            'Video PBJ',
            'Sajian video pembelajaran dan diskusi.',
            'videos',
            $items,
            'knowledge_media',
            'knowledge_media'
        );
    }

    public function gallery(): string
    {
        $items = $this->publishedMedia(['gallery', 'image'], self::LIST_LIMIT);
        return $this->renderCatalog(
            'Galeri Kegiatan',
            'Dokumentasi kolaborasi ekosistem PBJ Lampung.',
            'gallery',
            $items,
            'knowledge_media',
            'knowledge_media'
        );
    }

    // ------------------------------------------------------------------
    // Detail
    // ------------------------------------------------------------------
    public function articleDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('article', $slug);
        return $this->renderDetail('Artikel', 'articles', $row, 'knowledge_article');
    }

    public function caseStudyDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('case_study', $slug);
        return $this->renderDetail('Studi Kasus', 'cases', $row, 'knowledge_case');
    }

    public function templateDetail(string $slug)
    {
        $row = $this->publishedArticleBySlug('template', $slug);
        return $this->renderDetail('Template Dokumen', 'templates', $row, 'knowledge_template');
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
        $absolutePath = WRITEPATH . $file['path'];
        if (! is_file($absolutePath)) {
            return redirect()->back()->with('error', 'Berkas fisik template belum diunggah ke server.');
        }
        return $this->response->download($absolutePath, null)
            ->setFileName($file['original_name']);
    }

    // ------------------------------------------------------------------
    // Helpers — query knowledge_articles (published & public)
    // ------------------------------------------------------------------
    /**
     * @return array<int, array<string, mixed>>
     */
    private function publishedArticles(int $limit = self::LIST_LIMIT): array
    {
        return $this->publishedArticlesByType(null, $limit);
    }

    private function publishedArticlesByType(string $type = null, int $limit = self::LIST_LIMIT): array
    {
        $builder = $this->db->table('knowledge_articles')
            ->select('knowledge_articles.id, knowledge_articles.type, knowledge_articles.title, knowledge_articles.slug, knowledge_articles.summary, knowledge_articles.content, knowledge_articles.workflow_status, knowledge_articles.visibility, knowledge_articles.featured, knowledge_articles.cover_file_id, knowledge_articles.published_at, knowledge_articles.author_id, knowledge_articles.updated_at, users.name AS author_name')
            ->join('users', 'users.id = knowledge_articles.author_id', 'left')
            ->where('knowledge_articles.workflow_status', 'published')
            ->where('knowledge_articles.visibility', 'public')
            ->where('knowledge_articles.deleted_at IS NULL', null, false)
            ->orderBy('knowledge_articles.published_at', 'DESC')
            ->orderBy('knowledge_articles.updated_at', 'DESC')
            ->limit($limit);
        if ($type !== null) {
            $builder->where('knowledge_articles.type', $type);
        }
        return $builder->get()->getResultArray();
    }

    private function publishedArticleBySlug(string $type, string $slug): ?array
    {
        return $this->db->table('knowledge_articles')
            ->select('knowledge_articles.*, users.name AS author_name')
            ->join('users', 'users.id = knowledge_articles.author_id', 'left')
            ->where('knowledge_articles.type', $type)
            ->where('knowledge_articles.slug', $slug)
            ->where('knowledge_articles.workflow_status', 'published')
            ->where('knowledge_articles.visibility', 'public')
            ->where('knowledge_articles.deleted_at IS NULL', null, false)
            ->get()->getRowArray() ?: null;
    }

    /**
     * Query knowledge_media (published & public).
     * @param string|array<string>|null $types  null = semua
     */
    private function publishedMedia($types = null, int $limit = self::LIST_LIMIT): array
    {
        $builder = $this->db->table('knowledge_media')
            ->select('knowledge_media.id, knowledge_media.type, knowledge_media.title, knowledge_media.caption, knowledge_media.alt_text, knowledge_media.file_id, knowledge_media.external_url, knowledge_media.thumbnail_file_id, knowledge_media.workflow_status, knowledge_media.visibility, knowledge_media.sort_order, knowledge_media.featured, knowledge_media.carousel_enabled, knowledge_media.published_at, knowledge_media.updated_at, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, ft.original_name AS thumbnail_original_name')
            ->join('file_assets fa', 'fa.id = knowledge_media.file_id', 'left')
            ->join('file_assets ft', 'ft.id = knowledge_media.thumbnail_file_id', 'left')
            ->where('knowledge_media.workflow_status', 'published')
            ->where('knowledge_media.visibility', 'public')
            ->orderBy('knowledge_media.published_at', 'DESC')
            ->orderBy('knowledge_media.sort_order', 'ASC')
            ->limit($limit);
        if ($types !== null) {
            $builder->whereIn('knowledge_media.type', (array) $types);
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
        string $bookmarkType,
        string $bookmarkSourceTable
    ): string {
        return view('landing/pages/knowledge_catalog', [
            'title'              => $pageTitle . ' | Bunga Rampai BegawiPBJ',
            'metaDescription'    => $description,
            'metaKeywords'       => 'Bunga Rampai, ' . strtolower($pageTitle) . ', BegawiPBJ',
            'pageTitle'          => $pageTitle,
            'catalogType'        => $catalogType,
            'description'        => $description,
            'items'              => $items,
            'bookmarkType'       => $bookmarkType,
            'bookmarkSourceTable'=> $bookmarkSourceTable,
        ]);
    }

    private function renderDetail(string $pageTitle, string $catalogType, ?array $row, string $bookmarkType)
    {
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Konten Bunga Rampai tidak ditemukan.');
        }
        $related = [];
        if (in_array($bookmarkType, ['knowledge_article', 'knowledge_case', 'knowledge_template'], true)) {
            $related = $this->publishedArticlesByType($row['type'], 4);
        }
        $fileMeta = null;
        if ($bookmarkType === 'knowledge_template' && ! empty($row['cover_file_id'])) {
            $fileMeta = $this->db->table('file_assets')->where('id', (int) $row['cover_file_id'])->get()->getRowArray() ?: null;
        }
        return view('landing/pages/knowledge_detail', [
            'title'           => ($row['title'] ?? 'Bunga Rampai') . ' | Bunga Rampai BegawiPBJ',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Bunga Rampai BegawiPBJ.',
            'metaKeywords'    => 'Bunga Rampai, BegawiPBJ, ' . strtolower($pageTitle),
            'pageTitle'       => $pageTitle,
            'itemTitle'       => $row['title'] ?? 'Tanpa Judul',
            'catalogType'     => $catalogType,
            'item'            => $row,
            'related'         => $related,
            'fileMeta'        => $fileMeta,
            'bookmarkType'    => $bookmarkType,
        ]);
    }
}