<?php

namespace App\Commands;

use App\Controllers\Knowledge\ArticleController;
use App\Controllers\Knowledge\BookmarkController;
use App\Controllers\Knowledge\CaseStudyController;
use App\Controllers\Knowledge\CategoryController;
use App\Controllers\Knowledge\DocumentTemplateController;
use App\Controllers\Knowledge\FaqController;
use App\Controllers\Knowledge\KnowledgeCarouselController;
use App\Controllers\Knowledge\KnowledgeMediaController;
use App\Controllers\Knowledge\KnowledgeOverviewController;
use App\Controllers\Knowledge\TagController;
use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\BookmarkModel;
use App\Models\Knowledge\KnowledgeCategoryModel;
use App\Models\Knowledge\KnowledgeMediaItemModel;
use App\Models\Knowledge\TagModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class KnowledgeAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'knowledge:admin:test';
    protected $description = 'Comprehensive integration test suite for Bunga Rampai Admin Dashboard (/dashboard/bunga-rampai/*)';
    protected $usage       = 'knowledge:admin:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BUNGA RAMPAI ADMIN DASHBOARD — COMPREHENSIVE TEST SUITE        ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $session = service('session');
        $db = db_connect();

        // Inject IncomingRequest mock with SiteURI so that views calling old(...), current_url(), etc. work in CLI
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/bunga-rampai');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        // 1. Set admin session for authorization
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 1;

        $session->set([
            'isLoggedIn' => true,
            'user_id'    => $adminId,
            'role'       => 'superadmin',
            'email'      => $adminUser['email'] ?? 'admin@begawipbj.test',
            'name'       => $adminUser['name'] ?? 'Super Administrator',
        ]);

        $createdArticleId  = 0;
        $createdFaqId      = 0;
        $createdCaseId     = 0;
        $createdTemplateId = 0;
        $createdMediaId    = 0;
        $createdCatId      = 0;
        $createdTagId      = 0;

        // -------------------------------------------------------------
        // MODULE 1: Overview Dashboard (/dashboard/bunga-rampai)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 1] Overview Dashboard (/dashboard/bunga-rampai)", "yellow");
        try {
            $controller = $this->init(new KnowledgeOverviewController());
            $html = $controller->index();

            $checks = [
                'Page Title'               => 'Pusat kendali pengetahuan',
                'Shortcut Tulis Artikel'   => '+ Tulis Artikel',
                'Shortcut Tambah FAQ'      => '+ Tambah FAQ',
                'Shortcut Studi Kasus'     => '+ Studi Kasus',
                'Shortcut Unggah Template' => '+ Unggah Template',
                'Shortcut Media'           => '+ Media &amp; Infografik',
                'Shortcut Video'           => '+ Video PBJ',
                'Shortcut Galeri'          => '+ Galeri Kegiatan',
                'KPI Artikel PBJ'          => 'Artikel PBJ',
                'KPI Tanya Jawab'          => 'Tanya Jawab (FAQ)',
                'KPI Studi Kasus'          => 'Studi Kasus PBJ',
                'KPI Template Dokumen'     => 'Template Dokumen',
                'KPI Media & Infografik'   => 'Media &amp; Infografik',
                'KPI Video PBJ'            => 'Video PBJ',
                'KPI Galeri Kegiatan'      => 'Galeri Kegiatan',
                'Recent Table'             => 'Konten Pengetahuan Terbaru',
                'Visual Showcase'          => 'Media, Video &amp; Galeri Terbaru',
            ];

            foreach ($checks as $label => $needle) {
                if (str_contains($html, $needle)) {
                    $this->ok("Overview: {$label} rendered successfully");
                } else {
                    $this->fail("Overview: {$label} missing needle [{$needle}]");
                }
            }
        } catch (\Throwable $e) {
            $this->fail("Overview Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 2: Artikel PBJ (/dashboard/bunga-rampai/artikel)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 2] Artikel PBJ (/dashboard/bunga-rampai/artikel)", "yellow");
        try {
            $artController = $this->init(new ArticleController());

            // Index
            $indexHtml = $artController->index();
            $this->assertContains($indexHtml, 'Artikel & Praktik Baik', 'Articles Index Title');
            $this->assertContains($indexHtml, 'Tambah Artikel', 'Articles Create Button');

            // Create Form
            $createHtml = $artController->create();
            $this->assertContains($createHtml, 'Rekomendasi Format Cover', 'Articles Cover Recommendation Alert');
            $this->assertContains($createHtml, '1200 x 630 px', 'Articles Recommended Dimensions (1200x630 px)');
            $this->assertContains($createHtml, 'articleCoverPreview', 'Articles Live Preview Element');
            $this->assertContains($createHtml, 'Estimasi Waktu Baca', 'Articles Reading Time Field');
            $this->assertContains($createHtml, 'Sumber / Tautan Referensi', 'Articles Source URL Field');

            // Store new article
            $articleModel = new ArticleModel();
            $testSlug = 'test-artikel-pbj-' . time();
            $createdArticleId = $articleModel->insert([
                'type'            => 'article',
                'title'           => 'Panduan E-Katalog V6 Pengadaan Komprehensif',
                'slug'            => $testSlug,
                'summary'         => 'Rangkuman tata cara implementasi katalog elektronik versi terbaru.',
                'content'         => '<p>Uraian teknis implementasi e-katalog...</p>',
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'reading_time'    => 7,
                'source_url'      => 'https://jdih.lkpp.go.id',
                'author_id'       => $adminId,
                'published_at'    => date('Y-m-d H:i:s'),
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $this->ok("Articles Insert: Successfully created Article ID #{$createdArticleId}");

            // Detail
            $detailHtml = $artController->detail((string) $createdArticleId);
            $this->assertContains($detailHtml, 'Panduan E-Katalog V6', 'Articles Detail Content');
            $this->assertContains($detailHtml, '7 Menit', 'Articles Detail Reading Time');
            $this->assertContains($detailHtml, 'jdih.lkpp.go.id', 'Articles Detail Source URL');

            // Edit Form
            $editHtml = $artController->edit((string) $createdArticleId);
            $this->assertContains($editHtml, 'Ubah Artikel', 'Articles Edit Title');
            $this->assertContains($editHtml, '1200 x 630 px', 'Articles Edit Dimension Guide');
        } catch (\Throwable $e) {
            $this->fail("Articles Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 3: Tanya Jawab / FAQ (/dashboard/bunga-rampai/faq)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 3] Tanya Jawab FAQ (/dashboard/bunga-rampai/faq)", "yellow");
        try {
            $faqController = $this->init(new FaqController());

            // Index
            $faqIndex = $faqController->index();
            $this->assertContains($faqIndex, 'Tanya Jawab (FAQ)', 'FAQ Index Title');
            $this->assertContains($faqIndex, 'Tambah FAQ', 'FAQ Create Button');

            // Create Form
            $faqForm = $faqController->create();
            $this->assertContains($faqForm, 'Dasar Hukum &amp; Referensi Regulasi', 'FAQ Legal Basis Field');
            $this->assertContains($faqForm, 'Urutan Prioritas Tampil', 'FAQ Sort Order Field');
            $this->assertContains($faqForm, 'Ringkasan Jawaban Singkat', 'FAQ Summary Field');

            // Store new FAQ
            $articleModel = new ArticleModel();
            $createdFaqId = $articleModel->insert([
                'type'            => 'faq',
                'title'           => 'Bagaimana mekanisme penyesuaian harga kontrak tahun jamak?',
                'slug'            => 'faq-penyesuaian-harga-' . time(),
                'summary'         => 'Penyesuaian harga diperbolehkan untuk kontrak tahun jamak di atas 18 bulan.',
                'content'         => 'Penyesuaian harga dihitung berdasarkan indeks BPS sesuai tata cara Perlem LKPP.',
                'legal_basis'     => 'Pasal 54 Perpres 16/2018 jo. Perpres 12/2021',
                'sort_order'      => 1,
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'author_id'       => $adminId,
                'published_at'    => date('Y-m-d H:i:s'),
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $this->ok("FAQ Insert: Successfully created FAQ ID #{$createdFaqId}");

            // Detail
            $faqDetail = $faqController->detail((string) $createdFaqId);
            $this->assertContains($faqDetail, 'Dasar Hukum &amp; Referensi Regulasi', 'FAQ Detail Legal Section');
            $this->assertContains($faqDetail, 'Perpres 16/2018', 'FAQ Detail Legal Content');

            // Edit Form
            $faqEdit = $faqController->edit((string) $createdFaqId);
            $this->assertContains($faqEdit, 'Pasal 54 Perpres', 'FAQ Edit Legal Value Pre-filled');
        } catch (\Throwable $e) {
            $this->fail("FAQ Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 4: Studi Kasus (/dashboard/bunga-rampai/studi-kasus)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 4] Studi Kasus (/dashboard/bunga-rampai/studi-kasus)", "yellow");
        try {
            $caseController = $this->init(new CaseStudyController());

            // Index
            $caseIndex = $caseController->index();
            $this->assertContains($caseIndex, 'Studi Kasus PBJ', 'Case Study Index Title');
            $this->assertContains($caseIndex, 'Tambah Studi Kasus', 'Case Study Create Button');

            // Create Form
            $caseForm = $caseController->create();
            $this->assertContains($caseForm, 'Rekomendasi Format Gambar', 'Case Study Cover Dimension Alert');
            $this->assertContains($caseForm, '1200 x 630 px', 'Case Study Cover 1200x630 px Guide');
            $this->assertContains($caseForm, 'caseCoverPreview', 'Case Study Live Preview Element');
            $this->assertContains($caseForm, 'Tingkat Risiko / Dampak Kasus', 'Case Study Severity Field');
            $this->assertContains($caseForm, 'Tahapan Pengadaan (PBJ)', 'Case Study Stage Field');
            $this->assertContains($caseForm, 'Solusi Penyelesaian &amp; Pelajaran', 'Case Study Resolution Summary');

            // Store new Case Study
            $articleModel = new ArticleModel();
            $createdCaseId = $articleModel->insert([
                'type'               => 'case_study',
                'title'              => 'Mitigasi Wanprestasi Penyedia Konstruksi Gedung Rawat Inap',
                'slug'               => 'kasus-mitigasi-wanprestasi-' . time(),
                'summary'            => 'Studi penanganan keterlambatan proyek kritis dengan pemberian kesempatan 50 hari.',
                'content'            => '<p>Analisis mendalam terhadap kendala supply chain material semen...</p>',
                'organization_unit'  => 'Dinas Kesehatan Provinsi Lampung',
                'procurement_stage'  => 'pelaksanaan_kontrak',
                'case_severity'      => 'high',
                'resolution_summary' => 'Penerbitan SCM level 3, penyesuaian kurva S, dan penambahan tenaga kerja.',
                'reading_time'       => 8,
                'workflow_status'    => 'published',
                'visibility'         => 'public',
                'author_id'          => $adminId,
                'published_at'       => date('Y-m-d H:i:s'),
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
            $this->ok("Case Study Insert: Successfully created Case ID #{$createdCaseId}");

            // Detail
            $caseDetail = $caseController->detail((string) $createdCaseId);
            $this->assertContains($caseDetail, 'Dinas Kesehatan Provinsi Lampung', 'Case Study Detail Unit');
            $this->assertContains($caseDetail, 'Risiko Tinggi', 'Case Study Detail Severity');
            $this->assertContains($caseDetail, 'Penerbitan SCM level 3', 'Case Study Detail Resolution');

            // Edit Form
            $caseEdit = $caseController->edit((string) $createdCaseId);
            $this->assertContains($caseEdit, 'Ubah Studi Kasus', 'Case Study Edit Title');
            $this->assertContains($caseEdit, '1200 x 630 px', 'Case Study Edit Dimension Guide');
        } catch (\Throwable $e) {
            $this->fail("Case Study Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 5: Template Dokumen (/dashboard/bunga-rampai/template-dokumen)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 5] Template Dokumen (/dashboard/bunga-rampai/template-dokumen)", "yellow");
        try {
            $tplController = $this->init(new DocumentTemplateController());

            // Index
            $tplIndex = $tplController->index();
            $this->assertContains($tplIndex, 'Template Dokumen PBJ', 'Template Index Title');
            $this->assertContains($tplIndex, 'Tambah Template', 'Template Create Button');

            // Create Form
            $tplForm = $tplController->create();
            $this->assertContains($tplForm, 'Ketentuan Berkas Template', 'Template Upload Guide Alert');
            $this->assertContains($tplForm, 'DOCX, XLSX, PDF, PPTX', 'Template Supported Formats');
            $this->assertContains($tplForm, '25 MB', 'Template Max File Size (25 MB)');
            $this->assertContains($tplForm, 'Kelompok Dokumen PBJ', 'Template Category Field');
            $this->assertContains($tplForm, 'Versi Dokumen', 'Template Version Field');

            // Store new Template
            $articleModel = new ArticleModel();
            $createdTemplateId = $articleModel->insert([
                'type'              => 'template',
                'title'             => 'Formulir Standar Kerangka Acuan Kerja (KAK) Jasa Konsultansi',
                'slug'              => 'template-kak-konsultansi-' . time(),
                'summary'           => 'Format baku penyusunan KAK untuk pengadaan jasa konsultansi.',
                'content'           => 'Panduan pengisian bagian latar belakang, maksud, tujuan, dan output pekerjaan.',
                'document_category' => 'Kerangka Acuan Kerja (KAK)',
                'document_version'  => 'v2.1',
                'workflow_status'   => 'published',
                'visibility'        => 'public',
                'author_id'         => $adminId,
                'published_at'      => date('Y-m-d H:i:s'),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
            $this->ok("Template Insert: Successfully created Template ID #{$createdTemplateId}");

            // Detail
            $tplDetail = $tplController->detail((string) $createdTemplateId);
            $this->assertContains($tplDetail, 'Kerangka Acuan Kerja (KAK)', 'Template Detail Category');
            $this->assertContains($tplDetail, 'v2.1', 'Template Detail Version');

            // Edit Form
            $tplEdit = $tplController->edit((string) $createdTemplateId);
            $this->assertContains($tplEdit, 'Ubah Template Dokumen', 'Template Edit Title');
            $this->assertContains($tplEdit, '25 MB', 'Template Edit Upload Guide');
        } catch (\Throwable $e) {
            $this->fail("Template Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 6: Media & Galeri (/dashboard/bunga-rampai/media-galeri, /media, /video, /galeri)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 6] Media, Video & Galeri (/dashboard/bunga-rampai/media-galeri)", "yellow");
        try {
            $mediaController = $this->init(new KnowledgeMediaController());

            // All media index
            $mediaIndex = $mediaController->index();
            $this->assertContains($mediaIndex, 'Media &amp; Galeri PBJ', 'Media Gallery Index Title');
            $this->assertContains($mediaIndex, '+ Tambah Media', 'Media Gallery Create Button');

            // Scoped listings
            $infoHtml = $mediaController->infographics();
            $this->assertContains($infoHtml, 'Media &amp; Infografik PBJ', 'Infographics Scoped Header');

            $vidHtml = $mediaController->videos();
            $this->assertContains($vidHtml, 'Video Edukasi &amp; Podcast PBJ', 'Videos Scoped Header');

            $galHtml = $mediaController->gallery();
            $this->assertContains($galHtml, 'Galeri Kegiatan &amp; Dokumentasi PBJ', 'Gallery Scoped Header');

            // Create Form
            $mediaForm = $mediaController->create();
            $this->assertContains($mediaForm, 'Unggah File Media Gambar / Video', 'Media Upload Box Header');
            $this->assertContains($mediaForm, 'Rekomendasi Format Infografis', 'Media Infographic Recommendation');
            $this->assertContains($mediaForm, '1080 x 1080 px', 'Media 1080x1080 px Recommendation');
            $this->assertContains($mediaForm, '1080 x 1350 px', 'Media 1080x1350 px Recommendation');
            $this->assertContains($mediaForm, 'mediaFilePreview', 'Media Live Preview Element');
            $this->assertContains($mediaForm, 'Pengaturan Interaksi Sosial Media &amp; Tagar', 'Media Social Section');
            $this->assertContains($mediaForm, 'likes_count', 'Media Likes Input');
            $this->assertContains($mediaForm, 'views_count', 'Media Views Input');
            $this->assertContains($mediaForm, 'shares_count', 'Media Shares Input');
            $this->assertContains($mediaForm, 'hashtags', 'Media Hashtags Input');

            // Store new Media item
            $mediaModel = new KnowledgeMediaItemModel();
            $createdMediaId = $mediaModel->insert([
                'title'           => 'Infografik Alur E-Purchasing Katalog Lokal',
                'type'            => 'infographic',
                'caption'         => 'Visual alur transaksi pembelian katalog elektronik dari pemesanan hingga BAST.',
                'hashtags'        => '#KatalogLokal #PengadaanDigital #PBJLampung',
                'likes_count'     => 45,
                'views_count'     => 320,
                'shares_count'    => 28,
                'location'        => 'Bandar Lampung',
                'event_date'      => '2026-10-12',
                'duration'        => null,
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $this->ok("Media Insert: Successfully created Media ID #{$createdMediaId}");

            // Detail
            $mediaDetail = $mediaController->detail((string) $createdMediaId);
            $this->assertContains($mediaDetail, 'Infografik Alur E-Purchasing', 'Media Detail Title');
            $this->assertContains($mediaDetail, '45</strong> Suka', 'Media Detail Likes');
            $this->assertContains($mediaDetail, '320</strong> Tayangan', 'Media Detail Views');
            $this->assertContains($mediaDetail, '#KatalogLokal', 'Media Detail Hashtag');

            // Edit Form
            $mediaEdit = $mediaController->edit((string) $createdMediaId);
            $this->assertContains($mediaEdit, 'Unggah File Media Gambar / Video', 'Media Edit Header');
            $this->assertContains($mediaEdit, '1080 x 1080 px', 'Media Edit Dimension Guide');
        } catch (\Throwable $e) {
            $this->fail("Media Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 7: Carousel & Konten Unggulan
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 7] Carousel & Unggulan (/dashboard/bunga-rampai/carousel-konten-unggulan)", "yellow");
        try {
            $carouselController = $this->init(new KnowledgeCarouselController());

            // Index
            $carIndex = $carouselController->index();
            $this->assertContains($carIndex, 'Carousel dan Konten Unggulan', 'Carousel Index Title');
            $this->assertContains($carIndex, '1920 x 600 px', 'Carousel Panoramic Dimension Guide (1920x600 px)');
            $this->assertContains($carIndex, '1200 x 630 px', 'Carousel Landscape Dimension Guide (1200x630 px)');
            $this->assertContains($carIndex, 'Banner Cover', 'Carousel Table Preview Column');

            // Form
            $carForm = $carouselController->create();
            $this->assertContains($carForm, 'Rekomendasi Format &amp; Ukuran Banner', 'Carousel Form Guide Box');
            $this->assertContains($carForm, '1920 x 600 px', 'Carousel Form Panoramic Guide');
            $this->assertContains($carForm, 'previewCoverImg', 'Carousel Form Live Preview Image Element');
            $this->assertContains($carForm, 'target_id_article', 'Carousel Form Article Selector');
            $this->assertContains($carForm, 'target_id_media', 'Carousel Form Media Selector');

            // Enable article in carousel
            if ($createdArticleId > 0) {
                (new ArticleModel())->update($createdArticleId, [
                    'carousel_enabled' => 1,
                    'carousel_order'   => 1,
                ]);
                $this->ok("Carousel Enable: Article #{$createdArticleId} successfully marked active");
            }

            // Verify it renders in index
            $carIndexAfter = $carouselController->index();
            $this->assertContains($carIndexAfter, 'Panduan E-Katalog V6', 'Carousel Index shows active article');
        } catch (\Throwable $e) {
            $this->fail("Carousel Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 8: Kategori & Tagar
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 8] Kategori & Tagar (/kategori & /tag)", "yellow");
        try {
            $catController = $this->init(new CategoryController());
            $tagController = $this->init(new TagController());

            // Categories Index & Form
            $catIndex = $catController->index();
            $this->assertContains($catIndex, 'Kategori Konten PBJ', 'Categories Index Title');

            $catForm = $catController->create();
            $this->assertContains($catForm, 'Nama Kategori', 'Categories Form Name Field');

            // Insert Category
            $catModel = new KnowledgeCategoryModel();
            $createdCatId = $catModel->insert([
                'name'        => 'Uji Integrasi Kategori ' . time(),
                'slug'        => 'uji-integrasi-' . time(),
                'description' => 'Topik uji integrasi otomatis Bunga Rampai.',
                'sort_order'  => 99,
                'is_active'   => 1,
            ]);
            $this->ok("Categories Insert: Created Category ID #{$createdCatId}");

            // Tags Index & Form
            $tagIndex = $tagController->index();
            $this->assertContains($tagIndex, 'Tagar Konten (Hashtags)', 'Tags Index Title');

            $tagForm = $tagController->create();
            $this->assertContains($tagForm, 'Nama Tagar (Hashtag)', 'Tags Form Field');

            // Insert Tag
            $tagModel = new TagModel();
            $createdTagId = $tagModel->insert([
                'name' => 'UjiTag' . time(),
                'slug' => 'ujitag-' . time(),
            ]);
            $this->ok("Tags Insert: Created Tag ID #{$createdTagId}");
        } catch (\Throwable $e) {
            $this->fail("Categories/Tags Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 9: Bookmark Koleksi PBJ (/dashboard/bunga-rampai/bookmark)
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 9] Bookmark Koleksi (/dashboard/bunga-rampai/bookmark)", "yellow");
        try {
            $bmController = $this->init(new BookmarkController());
            $bmModel = new BookmarkModel();

            // Ensure a bookmark exists
            if ($createdArticleId > 0) {
                $bmModel->toggle($adminId, 'knowledge_article', $createdArticleId);
            }

            $bmIndex = $bmController->index();
            $this->assertContains($bmIndex, 'Bookmark Koleksi PBJ', 'Bookmark Index Title');
            $this->assertContains($bmIndex, 'Buka Konten', 'Bookmark Item Action Button');
        } catch (\Throwable $e) {
            $this->fail("Bookmark Module Exception: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // CLEANUP TEST DATA
        // -------------------------------------------------------------
        CLI::write(">>> [CLEANUP] Cleaning temporary test records...", "yellow");
        try {
            $articleModel = new ArticleModel();
            if ($createdArticleId > 0) $articleModel->delete($createdArticleId, true);
            if ($createdFaqId > 0) $articleModel->delete($createdFaqId, true);
            if ($createdCaseId > 0) $articleModel->delete($createdCaseId, true);
            if ($createdTemplateId > 0) $articleModel->delete($createdTemplateId, true);

            if ($createdMediaId > 0) (new KnowledgeMediaItemModel())->delete($createdMediaId, true);
            if ($createdCatId > 0) (new KnowledgeCategoryModel())->delete($createdCatId, true);
            if ($createdTagId > 0) (new TagModel())->delete($createdTagId, true);

            $this->ok("Cleanup: All temporary test records purged successfully");
        } catch (\Throwable $e) {
            $this->fail("Cleanup Exception: " . $e->getMessage());
        }

        // Summary
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("TEST RESULTS: %d Passed, %d Failed", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function ok(string $msg): void
    {
        CLI::write("  [OK] " . $msg, "green");
        $this->passed++;
    }

    private function fail(string $msg): void
    {
        CLI::write("  [FAIL] " . $msg, "red");
        $this->failed++;
    }

    private function assertContains(string $haystack, string $needle, string $label): void
    {
        if (str_contains($haystack, $needle)) {
            $this->ok("{$label}: rendered correctly containing '{$needle}'");
        } else {
            $this->fail("{$label}: expected to contain '{$needle}'");
        }
    }

    private function init(\App\Controllers\Knowledge\BaseKnowledgeController $controller): \App\Controllers\Knowledge\BaseKnowledgeController
    {
        $controller->initController(service('request'), service('response'), service('logger'));
        return $controller;
    }
}
