<?php

namespace App\Commands;

use App\Controllers\Landing\NewsPublicController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Exceptions\PageNotFoundException;

class NewsLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'news:landing:test';
    protected $description = 'Comprehensive test suite for the redesigned BegawiPBJ Berita Landing Page';
    protected $usage       = 'news:landing:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    BEGAWIPBJ BERITA LANDING PAGE — COMPREHENSIVE TEST SUITE     ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // 1. Test News Index Rendering
        $html = '';
        $this->testModule("Berita Controller Index Execution", function () use (&$html) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->index();
            $this->assert(is_string($html) && strlen($html) > 1000, "Berita HTML output produced and length > 1000 chars");
        });

        // 2. Test Hero Banner & Search
        $this->testModule("Hero Banner, Search & Breadcrumb", function () use ($html) {
            $this->assert(str_contains($html, 'Kabar &amp; Publikasi Resmi'), "Hero title present");
            $this->assert(str_contains($html, 'Pengadaan Barang/Jasa Provinsi Lampung'), "Hero subtitle present");
            $this->assert(str_contains($html, 'news-search-box'), "Hero search form present");
            $this->assert(str_contains($html, 'Beranda'), "Breadcrumb beranda present");
        });

        // 3. Test Live KPI Metrics Ribbon
        $this->testModule("Live KPI Metrics Ribbon", function () use ($html) {
            $this->assert(str_contains($html, 'Berita Publikasi Resmi'), "KPI News label present");
            $this->assert(str_contains($html, 'Kategori Liputan PBJ'), "KPI Categories label present");
            $this->assert(str_contains($html, 'Total Pembaca Terjangkau'), "KPI Readers label present");
            $this->assert(str_contains($html, 'Rilis Bulan Ini'), "KPI Monthly label present");
        });

        // 4. Test Category Filter & Headline
        $this->testModule("Category Pills & Headline Hero Card", function () use ($html) {
            $this->assert(str_contains($html, 'Semua Kategori'), "All categories pill present");
            $this->assert(str_contains($html, 'news-cat-pill'), "Category pills present");
            $this->assert(str_contains($html, 'Berita Utama'), "Headline badge present");
            $this->assert(str_contains($html, 'news-headline-card'), "Headline card present");
        });

        // 5. Test News Grid & Sidebar Widgets
        $this->testModule("News Grid & Sidebar Widgets", function () use ($html) {
            $this->assert(str_contains($html, 'news-grid-card'), "News grid cards present");
            $this->assert(str_contains($html, 'Berita Terpopuler'), "Trending widget present");
            $this->assert(str_contains($html, 'Kategori Liputan'), "Category directory widget present");
            $this->assert(str_contains($html, 'Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung'), "Official bulletin present");
            $this->assert(str_contains($html, 'Topik Populer'), "Tag cloud present");
        });

        // 6. Test Category Filtered Route
        $this->testModule("Category Route Execution (/berita/kategori/{slug})", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita/kategori/kebijakan-regulasi');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $catHtml = $controller->category('kebijakan-regulasi');
            $this->assert(is_string($catHtml) && strlen($catHtml) > 1000, "Category page rendered");
            $this->assert(str_contains($catHtml, 'Kebijakan &amp; Regulasi') || str_contains($catHtml, 'Kebijakan & Regulasi'), "Category title rendered");
        });

        // 7. Test Detail Page Rendering
        $this->testModule("Detail Route Execution (/berita/{slug})", function () {
            $db = db_connect();
            $news = $db->table('news')->where('workflow_status', 'published')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($news), "At least one published news found in DB");

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita/' . $news['slug']);
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $detailHtml = $controller->detail($news['slug']);
            $this->assert(is_string($detailHtml) && strlen($detailHtml) > 1000, "Detail page rendered");
            $this->assert(str_contains($detailHtml, esc($news['title'])), "Article title present in detail view");
            $this->assert(str_contains($detailHtml, 'news-lead-summary-box') || str_contains($detailHtml, 'news-article-body'), "Article content present");
            $this->assert(str_contains($detailHtml, 'news-share-bar'), "Share widget present");
            $this->assert(str_contains($detailHtml, 'Poin Penting untuk Pemangku Kepentingan'), "Key takeaways present");
            $this->assert(str_contains($detailHtml, 'Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah'), "Author identity present");
        });

        // 8. Test 404 for Invalid Slugs
        $this->testModule("404 Exception Handling for Invalid Slugs", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita/non-existent-slug-xyz');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $caught = false;
            try {
                $controller->detail('non-existent-slug-xyz-999');
            } catch (PageNotFoundException $e) {
                $caught = true;
            }
            $this->assert($caught, "PageNotFoundException thrown for invalid news slug");
        });

        // 9. Test Header Sub-menu
        $this->testModule("Navigation Header Sub-menu for Berita", function () {
            $headerFile = APPPATH . 'Views/landing/components/header.php';
            $content = file_get_contents($headerFile);
            $this->assert(str_contains($content, '<li><a href="<?= site_url(\'berita\') ?>">Semua Berita PBJ</a></li>'), "Semua Berita PBJ menu item present");
            $this->assert(str_contains($content, 'site_url(\'berita/kategori/kebijakan-regulasi\')'), "Kebijakan & Regulasi sub-menu present");
            $this->assert(str_contains($content, 'site_url(\'berita/kategori/e-katalog-digital\')'), "E-Katalog sub-menu present");
        });

        // 10. Test View Count Increments by +1 on Every Open (Non-unique)
        $this->testModule("View Counter (+1 on Every Open)", function () {
            $db = db_connect();
            $news = $db->table('news')->where('workflow_status', 'published')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($news), "Published news found for view count test");

            $initialCount = (int) ($news['view_count'] ?? 0);

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita/' . $news['slug']);
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            // Visit 1
            $controller->detail($news['slug']);
            $countAfter1 = (int) ($db->table('news')->where('id', $news['id'])->get()->getRowArray()['view_count'] ?? 0);
            $this->assert($countAfter1 === $initialCount + 1, "View count incremented by +1 on first open (now {$countAfter1})");

            // Visit 2
            $controller->detail($news['slug']);
            $countAfter2 = (int) ($db->table('news')->where('id', $news['id'])->get()->getRowArray()['view_count'] ?? 0);
            $this->assert($countAfter2 === $initialCount + 2, "View count incremented by another +1 on second open (now {$countAfter2})");
        });

        // 11. Test Multi-Headline Carousel (Up to 5 Featured Articles)
        $this->testModule("Multi-Headline Carousel (Up to 5 Featured Articles)", function () {
            $db = db_connect();
            // Ensure up to 5 articles are marked as is_featured = 1
            $articles = $db->table('news')->where('workflow_status', 'published')->where('deleted_at', null)->limit(5)->get()->getResultArray();
            $ids = array_column($articles, 'id');
            if (! empty($ids)) {
                $db->table('news')->whereIn('id', $ids)->update(['is_featured' => 1]);
            }

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'berita');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NewsPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->index();
            $this->assert(str_contains($html, 'headlineCarousel'), "Headline carousel container present");
            $this->assert(str_contains($html, 'carousel-item') || str_contains($html, 'news-headline-card'), "Headline carousel items present");
            $this->assert(str_contains($html, 'Utama #') || str_contains($html, 'Berita Utama'), "Headline indicator pills present");
        });

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("   TEST RESULTS: {$this->passed} PASSED, {$this->failed} FAILED", $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $name, callable $callback): void
    {
        try {
            $callback();
            $this->passed++;
            CLI::write("  [PASS] {$name}", "green");
        } catch (\Throwable $e) {
            $this->failed++;
            CLI::write("  [FAIL] {$name} - " . $e->getMessage(), "red");
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }
}
