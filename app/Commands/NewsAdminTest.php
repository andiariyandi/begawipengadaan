<?php

namespace App\Commands;

use App\Controllers\Communication\NewsController;
use App\Models\Communication\NewsCategoryModel;
use App\Models\Communication\NewsModel;
use App\Services\Communication\CommunicationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'news:admin:test';
    protected $description = 'Comprehensive integration test suite for Berita Admin Dashboard (/dashboard/komunikasi/berita/*)';
    protected $usage       = 'news:admin:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BERITA ADMIN DASHBOARD — COMPREHENSIVE TEST SUITE              ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $session = service('session');
        $db = db_connect();

        // Inject IncomingRequest mock with SiteURI
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/komunikasi/berita');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $incomingRequest->setGlobal('get', []);
        \Config\Services::injectMock('request', $incomingRequest);

        // Set admin session for authorization
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 1;

        $session->set([
            'isLoggedIn' => true,
            'user_id'    => $adminId,
            'role'       => 'superadmin',
        ]);

        $controller = new NewsController();
        $controller->initController($incomingRequest, service('response'), service('logger'));
        $communicationService = new CommunicationService();

        // -------------------------------------------------------------
        // Test 1: News Index View (Default Grid Mode)
        // -------------------------------------------------------------
        $this->testModule("Berita Admin - Index View (Grid Mode)", function () use ($controller) {
            $response = $controller->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Berita &amp; Publikasi PBJ') || str_contains($body, 'Berita & Publikasi PBJ'), "Header title present");
            $this->assert(str_contains($body, 'Tambah Berita Baru'), "Button 'Tambah Berita Baru' present");
            $this->assert(str_contains($body, 'Total Berita'), "KPI Total Berita card present");
            $this->assert(str_contains($body, 'Dipublikasikan'), "KPI Dipublikasikan card present");
            $this->assert(str_contains($body, 'Berita Utama'), "KPI Berita Utama card present");
            $this->assert(str_contains($body, 'Total Dibaca'), "KPI Total Dibaca card present");
            $this->assert(str_contains($body, 'name="q"'), "Search keyword input present");
            $this->assert(str_contains($body, 'name="status"'), "Filter status dropdown present");
            $this->assert(str_contains($body, 'name="category"'), "Filter category dropdown present");
            $this->assert(str_contains($body, 'name="featured"'), "Filter featured dropdown present");
            $this->assert(str_contains($body, 'col-xxl-4 col-md-6') || str_contains($body, 'Belum Ada Berita'), "Grid cards or empty state present");
        });

        // -------------------------------------------------------------
        // Test 2: News Index View (Table Mode)
        // -------------------------------------------------------------
        $this->testModule("Berita Admin - Index View (Table Mode)", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/komunikasi/berita?view=table');
            $req = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $req->setGlobal('get', ['view' => 'table']);
            \Config\Services::injectMock('request', $req);

            $ctrl = new NewsController();
            $ctrl->initController($req, service('response'), service('logger'));

            $response = $ctrl->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'TABLE VIEW') || str_contains($body, '<table class="table bordered-table') || str_contains($body, 'Belum Ada Berita'), "Table view or container rendered");
            $this->assert(str_contains($body, 'view=grid'), "Switch to grid view toggle link present");
        });

        // Reset request mock back to default
        \Config\Services::injectMock('request', $incomingRequest);

        // -------------------------------------------------------------
        // Test 3: News Form Create View
        // -------------------------------------------------------------
        $this->testModule("Berita Admin - Form Create View", function () use ($controller) {
            $response = $controller->create();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Tambah Berita'), "Create page heading present");
            $this->assert(str_contains($body, '1200 x 630 px'), "Recommended image dimensions present (1200 x 630 px)");
            $this->assert(str_contains($body, 'previewCoverImg'), "Live image preview container present");
            $this->assert(str_contains($body, 'name="title"'), "Title input present");
            $this->assert(str_contains($body, 'name="summary"'), "Summary textarea present");
            $this->assert(str_contains($body, 'name="content"'), "Content editor textarea present");
            $this->assert(str_contains($body, 'name="reading_time"'), "Reading time input present");
            $this->assert(str_contains($body, 'name="tags"'), "Tags input present");
            $this->assert(str_contains($body, 'name="source_name"'), "Source name input present");
            $this->assert(str_contains($body, 'name="source_url"'), "Source URL input present");
            $this->assert(str_contains($body, 'name="cover_caption"'), "Cover caption input present");
            $this->assert(str_contains($body, 'name="is_featured"'), "Headline / Featured switch present");
            $this->assert(str_contains($body, 'name="workflow_status"'), "Workflow status selector present");
        });

        // -------------------------------------------------------------
        // Test 4: News Create & Store Database Execution
        // -------------------------------------------------------------
        $testSlug = 'test-berita-ci-' . time();
        $createdNewsId = 0;
        $this->testModule("Berita Admin - Store News Execution", function () use (&$createdNewsId, $testSlug, $adminId, $communicationService) {
            $createdNewsId = $communicationService->createNews([
                'title'           => 'Automated Test Berita CI ' . time(),
                'slug'            => $testSlug,
                'summary'         => 'Ringkasan berita otomatis untuk testing CI admin dashboard.',
                'content'         => '<p>Isi lengkap berita otomatis untuk verifikasi sistem.</p>',
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'is_featured'     => 1,
                'reading_time'    => '4 menit',
                'tags'            => 'Pengadaan, Digitalisasi, Testing',
                'source_name'     => 'BegawiPBJ Media',
                'source_url'      => 'https://begawipbj.test/source',
                'cover_caption'   => 'Dokumentasi kegiatan peluncuran sistem',
            ], $adminId);

            $this->assert($createdNewsId > 0, "News record created successfully with ID: {$createdNewsId}");

            $db = db_connect();
            $row = $db->table('news')->where('id', $createdNewsId)->get()->getRowArray();
            $this->assert(! empty($row), "News row exists in DB");
            $this->assert((int) $row['is_featured'] === 1, "is_featured field correctly saved as 1");
            $this->assert($row['reading_time'] === '4 menit', "reading_time correctly saved");
            $this->assert($row['tags'] === 'Pengadaan, Digitalisasi, Testing', "tags correctly saved");
            $this->assert($row['source_name'] === 'BegawiPBJ Media', "source_name correctly saved");
            $this->assert($row['source_url'] === 'https://begawipbj.test/source', "source_url correctly saved");
            $this->assert($row['cover_caption'] === 'Dokumentasi kegiatan peluncuran sistem', "cover_caption correctly saved");
        });

        // -------------------------------------------------------------
        // Test 5: News Detail View
        // -------------------------------------------------------------
        if ($createdNewsId > 0) {
            $this->testModule("Berita Admin - Detail View", function () use ($controller, $createdNewsId) {
                $response = $controller->detail((string) $createdNewsId);
                $body = $this->extractBody($response);

                $this->assert(str_contains($body, 'Detail Informasi Berita'), "Detail title present");
                $this->assert(str_contains($body, 'Berita Utama'), "Headline badge present");
                $this->assert(str_contains($body, 'Dokumentasi kegiatan peluncuran sistem'), "Cover caption present");
                $this->assert(str_contains($body, 'BegawiPBJ Media'), "Source name present");
                $this->assert(str_contains($body, '4 menit'), "Reading time badge present");
                $this->assert(str_contains($body, 'Pengadaan'), "Tag badge present");
                $this->assert(str_contains($body, 'Ubah Berita'), "Edit button present");
                $this->assert(str_contains($body, 'Buka di Web Publik'), "Public preview link present");
            });

            // -------------------------------------------------------------
            // Test 6: News Edit Form View
            // -------------------------------------------------------------
            $this->testModule("Berita Admin - Edit Form View", function () use ($controller, $createdNewsId) {
                $response = $controller->edit((string) $createdNewsId);
                $body = $this->extractBody($response);

                $this->assert(str_contains($body, 'Ubah Berita'), "Edit form heading present");
                $this->assert(str_contains($body, 'value="4 menit"'), "Pre-filled reading_time present");
                $this->assert(str_contains($body, 'Pengadaan, Digitalisasi, Testing'), "Pre-filled tags present");
                $this->assert(str_contains($body, 'BegawiPBJ Media'), "Pre-filled source_name present");
                $this->assert(str_contains($body, 'Dokumentasi kegiatan peluncuran sistem'), "Pre-filled cover_caption present");
                $this->assert(str_contains($body, 'Simpan Pembaruan Berita'), "Submit update button present");
            });

            // -------------------------------------------------------------
            // Test 7: News Update Execution
            // -------------------------------------------------------------
            $this->testModule("Berita Admin - Update Execution", function () use ($createdNewsId, $adminId, $communicationService) {
                $uniqueTitle = 'Automated Test Berita CI Updated ' . time();
                $communicationService->updateNews($createdNewsId, [
                    'title'           => $uniqueTitle,
                    'slug'            => 'test-berita-ci-updated-' . time(),
                    'summary'         => 'Ringkasan yang diperbarui.',
                    'content'         => '<p>Konten yang diperbarui.</p>',
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'is_featured'     => 0,
                    'reading_time'    => '6 menit',
                    'tags'            => 'Pembaruan, Sukses',
                    'source_name'     => 'Sumber Terverifikasi',
                    'source_url'      => 'https://begawipbj.test/updated',
                    'cover_caption'   => 'Foto caption revisi',
                ], $adminId);

                $db = db_connect();
                $updatedRow = $db->table('news')->where('id', $createdNewsId)->get()->getRowArray();
                $this->assert($updatedRow['title'] === $uniqueTitle, "Title updated");
                $this->assert((int) $updatedRow['is_featured'] === 0, "is_featured updated to 0");
                $this->assert($updatedRow['reading_time'] === '6 menit', "reading_time updated");
                $this->assert($updatedRow['tags'] === 'Pembaruan, Sukses', "tags updated");
            });

            // -------------------------------------------------------------
            // Test 8: News Delete Execution
            // -------------------------------------------------------------
            $this->testModule("Berita Admin - Delete Execution", function () use ($createdNewsId, $adminId, $communicationService) {
                $communicationService->deleteNews($createdNewsId, $adminId);

                $db = db_connect();
                $deletedRow = $db->table('news')->where('id', $createdNewsId)->get()->getRowArray();
                $this->assert(empty($deletedRow) || ! empty($deletedRow['deleted_at']), "News successfully soft-deleted or removed");
            });
        }

        // -------------------------------------------------------------
        // Test 9: News Categories Index View
        // -------------------------------------------------------------
        $this->testModule("Kategori Berita Admin - Index View", function () use ($controller) {
            $response = $controller->categories();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Kategori Berita'), "Kategori Berita title present");
            $this->assert(str_contains($body, 'Total Kategori'), "KPI Total Kategori present");
            $this->assert(str_contains($body, 'Kategori Aktif'), "KPI Kategori Aktif present");
            $this->assert(str_contains($body, 'Total Artikel Terkait'), "KPI Total Artikel Terkait present");
            $this->assert(str_contains($body, 'Tambah Kategori'), "Button Tambah Kategori present");
            $this->assert(str_contains($body, 'categorySearchInput'), "Category search input present");
        });

        // -------------------------------------------------------------
        // Test 10: News Category Create Form & Store
        // -------------------------------------------------------------
        $testCatSlug = 'kategori-ci-' . time();
        $createdCatId = 0;
        $this->testModule("Kategori Berita Admin - Form Create & Store", function () use ($controller, &$createdCatId, $testCatSlug, $adminId, $communicationService) {
            $formResponse = $controller->categoryCreate();
            $body = $this->extractBody($formResponse);
            $this->assert(str_contains($body, 'Tambah Kategori Berita Baru'), "Create category form title present");
            $this->assert(str_contains($body, 'name="slug"'), "Slug input present");
            $this->assert(str_contains($body, 'name="is_active"'), "Active switch present");

            $createdCatId = $communicationService->createCategory([
                'name'        => 'Kategori Test CI ' . time(),
                'slug'        => $testCatSlug,
                'description' => 'Deskripsi kategori untuk automated testing CI.',
                'is_active'   => 1,
            ], $adminId);

            $this->assert($createdCatId > 0, "Category created successfully with ID: {$createdCatId}");

            $db = db_connect();
            $catRow = $db->table('news_categories')->where('id', $createdCatId)->get()->getRowArray();
            $this->assert(! empty($catRow), "Category exists in DB");
            $this->assert($catRow['slug'] === $testCatSlug, "Category slug matches");
        });

        // -------------------------------------------------------------
        // Test 11: News Category Edit & Update
        // -------------------------------------------------------------
        if ($createdCatId > 0) {
            $this->testModule("Kategori Berita Admin - Edit & Update", function () use ($controller, $createdCatId, $adminId, $communicationService) {
                $editResponse = $controller->categoryEdit((string) $createdCatId);
                $body = $this->extractBody($editResponse);
                $this->assert(str_contains($body, 'Ubah Kategori Berita'), "Edit category heading present");

                $communicationService->updateCategory($createdCatId, [
                    'name'        => 'Kategori Test CI Updated',
                    'slug'        => 'kategori-ci-updated-' . time(),
                    'description' => 'Deskripsi kategori yang telah diperbarui.',
                    'is_active'   => 0,
                ], $adminId);

                $db = db_connect();
                $updatedCat = $db->table('news_categories')->where('id', $createdCatId)->get()->getRowArray();
                $this->assert($updatedCat['name'] === 'Kategori Test CI Updated', "Category name updated");
                $this->assert((int) $updatedCat['is_active'] === 0, "Category is_active updated to 0");
            });

            // -------------------------------------------------------------
            // Test 12: News Category Delete
            // -------------------------------------------------------------
            $this->testModule("Kategori Berita Admin - Delete Execution", function () use ($createdCatId, $adminId, $communicationService) {
                $communicationService->deleteCategory($createdCatId, $adminId);

                $db = db_connect();
                $deletedCat = $db->table('news_categories')->where('id', $createdCatId)->get()->getRowArray();
                $this->assert(empty($deletedCat) || ! empty($deletedCat['deleted_at']), "Category successfully removed or soft-deleted");
            });
        }

        // Summary
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("   HASIL PENGUJIAN: %d BERHASIL | %d GAGAL", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $name, callable $callback): void
    {
        CLI::write("[RUNNING] {$name}...", "yellow");
        try {
            $callback();
            $this->passed++;
            CLI::write("[PASS]    {$name}", "green");
        } catch (\Throwable $e) {
            $this->failed++;
            CLI::write("[FAIL]    {$name}: " . $e->getMessage(), "red");
            CLI::write("          File: " . $e->getFile() . " (Line: " . $e->getLine() . ")", "light_gray");
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException("Assertion failed: {$message}");
        }
    }

    private function extractBody($response): string
    {
        if (is_string($response)) {
            return $response;
        }
        if ($response instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return (string) $response->getBody();
        }
        return '';
    }
}
