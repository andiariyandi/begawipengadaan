<?php

namespace App\Commands;

use App\Controllers\Admin\RegulationController;
use App\Models\Regulation\RegulationModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RegulationAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'regulation:admin:test';
    protected $description = 'Comprehensive integration test suite for Fitur Regulasi Admin Dashboard (/dashboard/regulasi/*)';
    protected $usage       = 'regulation:admin:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   REGULASI ADMIN DASHBOARD — COMPREHENSIVE TEST SUITE            ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $session = service('session');
        $db = db_connect();

        // Inject IncomingRequest mock with SiteURI
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/regulasi');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        // Set admin session for authorization
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 1;

        $session->set([
            'isLoggedIn' => true,
            'user_id'    => $adminId,
            'role'           => 'super_administrator',
            'roles'          => ['super_administrator'],
            'account_status' => 'active',
        ]);

        $controller = new RegulationController();
        $controller->initController($incomingRequest, service('response'), service('logger'));

        // Test 1: Dashboard Index / Regulation Document Listing
        $this->testModule("Dokumen Regulasi - Index View", function () use ($controller) {
            $response = $controller->index();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Regulasi PBJ'), "Header title present");
            $this->assert(str_contains($body, 'Tambah Regulasi'), "Button create present");
            $this->assert(str_contains($body, 'dashboard/regulasi/relasi'), "Relasi link correct");
        });

        // Test 2: Regulation Document Create View
        $this->testModule("Dokumen Regulasi - Form Create View", function () use ($controller) {
            $response = $controller->create();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Tahapan Siklus PBJ yang Diatur:'), "Tahapan PBJ checkboxes present");
            $this->assert(str_contains($body, '1200 x 630 px'), "Recommended dimension for cover banner present");
            $this->assert(str_contains($body, 'Kompetensi SDM Terkait:'), "Kompetensi checkboxes present");
            $this->assert(str_contains($body, 'name="is_featured"'), "Featured switch present");
        });

        // Test 3 & 4: Regulation Document Detail & Edit Views
        $sampleReg = (new RegulationModel())->orderBy('id', 'ASC')->first();
        if ($sampleReg) {
            $this->testModule("Dokumen Regulasi - Detail View", function () use ($controller, $sampleReg) {
                $response = $controller->show((string) $sampleReg['id']);
                $body = $this->extractBody($response);
                $this->assert(str_contains($body, 'Detail Dokumen Regulasi'), "Detail header present");
                $this->assert(str_contains($body, 'Tahapan PBJ Terkait') || str_contains($body, 'nav-link'), "Tahapan PBJ or tabs present");
            });

            $this->testModule("Dokumen Regulasi - Edit View", function () use ($controller, $sampleReg) {
                $response = $controller->edit((string) $sampleReg['id']);
                $body = $this->extractBody($response);
                $this->assert(str_contains($body, '1200 x 630 px'), "Banner dimension guide present");
                $this->assert(str_contains($body, 'Unggah Berkas Baru'), "File upload present");
            });
        }

        // Test 5: Category Index
        $this->testModule("Kategori Regulasi - Index View", function () use ($controller) {
            $response = $controller->categories();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Kategori Regulasi'), "Kategori header present");
            $this->assert(str_contains($body, 'Tambah Kategori'), "Tambah kategori button present");
        });

        // Test 6: Category Create Form
        $this->testModule("Kategori Regulasi - Form Create View", function () use ($controller) {
            $response = $controller->categoryCreate();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Tambah Kategori'), "Form create kategori present");
            $this->assert(str_contains($body, '200 x 200 px'), "Recommended dimension for icon 200x200 present");
            $this->assert(str_contains($body, 'icon_class'), "Icon class input present");
        });

        // Test 7: Relations Index
        $this->testModule("Relasi Regulasi - Index View", function () use ($controller) {
            $response = $controller->relations();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Relasi Regulasi'), "Relasi header present");
            $this->assert(str_contains($body, 'Tambah Relasi Baru'), "Tambah relasi button present");
        });

        // Test 8: Relations Create Form
        $this->testModule("Relasi Regulasi - Form Create View", function () use ($controller) {
            $response = $controller->relationCreate();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Tambah Relasi Antar-Regulasi'), "Relasi form header present");
            $this->assert(str_contains($body, 'Mengubah'), "Indonesian relation type present");
        });

        // Test 9: Procurement Stages Index
        $this->testModule("Tahapan PBJ - Index View", function () use ($controller) {
            $response = $controller->procurementStages();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Tahapan PBJ'), "Tahapan PBJ header present");
            $this->assert(str_contains($body, 'Tambah Tahapan'), "Tambah tahapan button present");
        });

        // Test 10: Procurement Stages Create Form
        $this->testModule("Tahapan PBJ - Form Create View", function () use ($controller) {
            $response = $controller->procurementStageCreate();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Tahapan PBJ'), "Form stage present");
        });

        // Test 11: Competencies Index
        $this->testModule("Kompetensi PBJ - Index View", function () use ($controller) {
            $response = $controller->competencies();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Kompetensi'), "Kompetensi header present");
        });

        // Test 12: Competencies Create Form
        $this->testModule("Kompetensi PBJ - Form Create View", function () use ($controller) {
            $response = $controller->competencyCreate();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Kompetensi'), "Kompetensi form present");
        });

        // Test 13: Versions Index
        $this->testModule("Versi Dokumen Regulasi - Index View", function () use ($controller) {
            $response = $controller->versions();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Versi Dokumen Regulasi'), "Versi header present");
        });

        // Test 14: Download Logs Index
        $this->testModule("Log Unduhan Regulasi - Index View", function () use ($controller) {
            $response = $controller->downloadLogs();
            $body = $this->extractBody($response);
            $this->assert(str_contains($body, 'Log Unduhan Regulasi'), "Download logs header present");
        });

        // Summary
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("   TEST RESULTS: %d PASSED, %d FAILED", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $name, callable $callback): void
    {
        try {
            $callback();
            CLI::write("  [PASS] {$name}", "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] {$name}: " . $e->getMessage(), "red");
            $this->failed++;
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
        if (is_object($response) && method_exists($response, 'getBody')) {
            return (string) $response->getBody();
        }
        return '';
    }
}
