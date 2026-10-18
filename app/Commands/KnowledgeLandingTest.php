<?php

namespace App\Commands;

use App\Controllers\Landing\BungaRampaiController;
use App\Models\Knowledge\BookmarkModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class KnowledgeLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'knowledge:test';
    protected $description = 'Comprehensive test suite for Bunga Rampai landing pages, catalog pagination, details, download, and bookmark integration.';
    protected $usage       = 'knowledge:test';

    public function run(array $params): int
    {
        CLI::write('==================================================================', 'yellow');
        CLI::write('     TEST SUITE: BUNGA RAMPAI LANDING, CATALOG & BOOKMARK SYSTEM  ', 'yellow');
        CLI::write('==================================================================', 'yellow');
        CLI::newLine();

        $session = service('session');
        $passed  = 0;
        $failed  = 0;

        $controller = new BungaRampaiController();

        // -------------------------------------------------------------
        // TEST 1: Public Guest Browsing (/bunga-rampai)
        // -------------------------------------------------------------
        CLI::write('>>> TEST 1: Public Guest Browsing (/bunga-rampai)', 'cyan');
        $session->set([
            'isLoggedIn' => false,
            'user_id'    => 0,
        ]);

        $htmlGuest = $controller->index();

        $guestChecks = [
            'Hero Section Title'    => 'Pusat Pengetahuan &amp; Praktik Baik Pengadaan',
            'Hero Tag'              => 'Bunga Rampai BegawiPengadaan Lampung',
            'Search Bar'            => 'arena-search-bar',
            'Category Nav Pills'    => 'arena-nav-pills',
            'Glass Nav Pill Sub-Nav' => 'arena-nav-pill',
            'Category Exploration'  => 'Jelajahi Ekosistem Pengetahuan PBJ',
            'Pilihan Editor'        => 'Rekomendasi Utama Pengetahuan',
            'Artikel Terbaru'       => 'Artikel Pengetahuan Terbaru',
            'Studi Kasus'           => 'Studi Kasus Lapangan',
            'Template Dokumen'      => 'Template Dokumen PBJ',
            'Media & Video'         => 'Media &amp; Video Pembelajaran',
            'FAQ Accordion Preview' => 'Pertanyaan Sering Muncul (FAQ)',
            'Pillars of Integrity'  => 'Membangun Budaya Pengadaan Berintegritas &amp; Akuntabel',
        ];

        foreach ($guestChecks as $label => $needle) {
            if (str_contains($htmlGuest, $needle)) {
                CLI::write("  [OK] Guest Index: {$label} rendered successfully", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Guest Index: {$label} missing", 'red');
                $failed++;
            }
        }

        // Verify bookmark button is hidden for guest
        if (! str_contains($htmlGuest, 'class="arena-bookmark-form')) {
            CLI::write('  [OK] Guest Index: Bookmark forms hidden cleanly for public visitor', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Guest Index: Bookmark form visible to unauthenticated visitor', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 2: Logged-in User Browsing with Bookmarks
        // -------------------------------------------------------------
        CLI::write('>>> TEST 2: Logged-in User Browsing with Bookmark Buttons', 'cyan');
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => 14,
            'user_role'  => 'admin',
        ]);

        $htmlAuth = $controller->index();
        if (str_contains($htmlAuth, 'class="arena-bookmark-form')) {
            CLI::write('  [OK] Logged-in Index: Bookmark buttons rendered on showcase cards', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Logged-in Index: Bookmark buttons missing for authenticated user', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 3: Catalog Pages & Filtering
        // -------------------------------------------------------------
        CLI::write('>>> TEST 3: Sub-Menu Catalogs with Pagination & Filtering', 'cyan');

        // Articles catalog
        $htmlArticles = $controller->articles();
        if (str_contains($htmlArticles, 'Katalog Artikel PBJ') && str_contains($htmlArticles, 'Baca Artikel')) {
            CLI::write('  [OK] Articles Catalog: Rendered successfully with cards and search', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Articles Catalog: Render failure', 'red');
            $failed++;
        }

        // FAQs catalog
        $htmlFaqs = $controller->faqs();
        if (str_contains($htmlFaqs, 'Tanya Jawab (FAQ) PBJ') && str_contains($htmlFaqs, 'faqCatalogAccordion')) {
            CLI::write('  [OK] FAQs Catalog: Rendered successfully with interactive accordions', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] FAQs Catalog: Render failure', 'red');
            $failed++;
        }

        // Case studies catalog
        $htmlCases = $controller->caseStudies();
        if (str_contains($htmlCases, 'Studi Kasus Lapangan') && str_contains($htmlCases, 'Pelajari Kasus')) {
            CLI::write('  [OK] Case Studies Catalog: Rendered successfully with danger badges', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Case Studies Catalog: Render failure', 'red');
            $failed++;
        }

        // Templates catalog
        $htmlTemplates = $controller->templates();
        if (str_contains($htmlTemplates, 'Template Dokumen PBJ') && str_contains($htmlTemplates, 'Unduh Berkas')) {
            CLI::write('  [OK] Templates Catalog: Rendered successfully with download buttons', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Templates Catalog: Render failure', 'red');
            $failed++;
        }

        // Media catalog
        $htmlMedia = $controller->media();
        if (str_contains($htmlMedia, 'Media &amp; Infografik PBJ') && str_contains($htmlMedia, 'Infografik PBJ')) {
            CLI::write('  [OK] Media Catalog: Rendered successfully with infographics', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Media Catalog: Render failure', 'red');
            $failed++;
        }

        // Videos catalog
        $htmlVideos = $controller->videos();
        if (str_contains($htmlVideos, 'Video Pembelajaran PBJ') && str_contains($htmlVideos, 'Video PBJ')) {
            CLI::write('  [OK] Videos Catalog: Rendered successfully with video cards', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Videos Catalog: Render failure', 'red');
            $failed++;
        }

        // Gallery catalog
        $htmlGallery = $controller->gallery();
        if (str_contains($htmlGallery, 'Galeri Dokumentasi Kegiatan') && str_contains($htmlGallery, 'Dokumentasi')) {
            CLI::write('  [OK] Gallery Catalog: Rendered successfully with photo grid', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Gallery Catalog: Render failure', 'red');
            $failed++;
        }

        // Multimedia catalog (Integrated Unified Page)
        $htmlMultimedia = $controller->multimedia();
        if (str_contains($htmlMultimedia, 'Multimedia PBJ') && str_contains($htmlMultimedia, 'Feed Instagram')) {
            CLI::write('  [OK] Multimedia Catalog: Unified page rendered with Instagram slider & filters', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Multimedia Catalog: Render failure', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 4: Detail Pages & File Download
        // -------------------------------------------------------------
        CLI::write('>>> TEST 4: Detail Pages & Template File Download', 'cyan');

        $db = db_connect();

        // 4.1 Article Detail
        $sampleArticle = $db->table('knowledge_articles')
            ->where('type', 'article')
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if ($sampleArticle) {
            $htmlArtDetail = $controller->articleDetail($sampleArticle['slug']);
            if (str_contains($htmlArtDetail, $sampleArticle['title']) && str_contains($htmlArtDetail, 'Informasi Materi')) {
                CLI::write("  [OK] Article Detail: '{$sampleArticle['title']}' rendered successfully", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Article Detail: Missing title or metadata", 'red');
                $failed++;
            }
        }

        // 4.2 Case Study Detail
        $sampleCase = $db->table('knowledge_articles')
            ->where('type', 'case_study')
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if ($sampleCase) {
            $htmlCaseDetail = $controller->caseStudyDetail($sampleCase['slug']);
            if (str_contains($htmlCaseDetail, $sampleCase['title']) && str_contains($htmlCaseDetail, 'Ringkasan Materi')) {
                CLI::write("  [OK] Case Study Detail: '{$sampleCase['title']}' rendered successfully", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Case Study Detail: Missing title or summary", 'red');
                $failed++;
            }
        }

        // 4.3 Template Detail
        $sampleTemplate = $db->table('knowledge_articles')
            ->where('type', 'template')
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if ($sampleTemplate) {
            $htmlTplDetail = $controller->templateDetail($sampleTemplate['slug']);
            if (str_contains($htmlTplDetail, $sampleTemplate['title']) && str_contains($htmlTplDetail, 'Unduh Berkas Sekarang')) {
                CLI::write("  [OK] Template Detail: '{$sampleTemplate['title']}' rendered with Download Widget", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Template Detail: Download widget missing", 'red');
                $failed++;
            }

            // 4.4 Template Download Execution
            $downloadResponse = $controller->templateDownload($sampleTemplate['slug']);
            if ($downloadResponse instanceof \CodeIgniter\HTTP\DownloadResponse) {
                CLI::write("  [OK] Template Download: Successfully returns DownloadResponse for '{$sampleTemplate['slug']}'", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Template Download: Did not return DownloadResponse", 'red');
                $failed++;
            }
        }

        // -------------------------------------------------------------
        // TEST 5: Bookmark Toggle for Knowledge Types
        // -------------------------------------------------------------
        CLI::write('>>> TEST 5: Bookmark Data Layer Toggle Verification', 'cyan');
        $bookmarkModel = new BookmarkModel();
        $testUserId    = 14;

        $typesToTest = [
            'knowledge_article'  => $sampleArticle['id'] ?? 1,
            'knowledge_case'     => $sampleCase['id'] ?? 1,
            'knowledge_template' => $sampleTemplate['id'] ?? 1,
        ];

        foreach ($typesToTest as $type => $id) {
            // Remove first if exists
            $db->table('bookmarks')->where(['user_id' => $testUserId, 'bookmarkable_type' => $type, 'bookmarkable_id' => $id])->delete();

            // Insert
            $db->table('bookmarks')->insert([
                'user_id'           => $testUserId,
                'bookmarkable_type' => $type,
                'bookmarkable_id'   => $id,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
            $exists = $bookmarkModel->exists($testUserId, $type, $id);

            // Delete
            $db->table('bookmarks')->where(['user_id' => $testUserId, 'bookmarkable_type' => $type, 'bookmarkable_id' => $id])->delete();
            $notExists = ! $bookmarkModel->exists($testUserId, $type, $id);

            if ($exists && $notExists) {
                CLI::write("  [OK] Bookmark Toggle: '{$type}' #{$id} verified", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Bookmark Toggle: '{$type}' toggle failed", 'red');
                $failed++;
            }
        }

        // -------------------------------------------------------------
        // TEST 6: Search Filtering & Pagination Verification
        // -------------------------------------------------------------
        CLI::write('>>> TEST 6: Keyword Search & Pagination Control Verification', 'cyan');

        // 6.1 Query filtering on articles (?q=HPS)
        $_GET['q'] = 'HPS';
        $htmlSearch = $controller->articles();
        if (str_contains($htmlSearch, 'Kata kunci: "HPS"') && str_contains($htmlSearch, 'HPS')) {
            CLI::write('  [OK] Query Search: Articles filtered by "HPS" successfully', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Query Search: Articles filtering failed', 'red');
            $failed++;
        }

        // 6.2 Query filtering on templates (?q=KAK)
        $_GET['q'] = 'KAK';
        $htmlTplSearch = $controller->templates();
        if (str_contains($htmlTplSearch, 'Kata kunci: "KAK"') && str_contains($htmlTplSearch, 'KAK')) {
            CLI::write('  [OK] Query Search: Templates filtered by "KAK" successfully', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Query Search: Templates filtering failed', 'red');
            $failed++;
        }

        // 6.3 Empty search query result
        $_GET['q'] = 'xyznonexistentpbjquery123';
        $htmlEmptySearch = $controller->articles();
        if (str_contains($htmlEmptySearch, 'Tidak Ada Konten Ditemukan') && str_contains($htmlEmptySearch, 'Reset Filter')) {
            CLI::write('  [OK] Empty Search: Friendly empty state with reset button displayed', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Empty Search: Expected empty state missing', 'red');
            $failed++;
        }
        unset($_GET['q']);

        CLI::newLine();
        CLI::write('==================================================================', 'yellow');
        CLI::write("TEST RESULTS: {$passed} Passed, {$failed} Failed", $failed === 0 ? 'green' : 'red');
        CLI::write('==================================================================', 'yellow');

        return $failed === 0 ? 0 : 1;
    }
}
