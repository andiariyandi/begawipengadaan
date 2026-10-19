<?php

namespace App\Commands;

use App\Controllers\Admin\Landing\SpotlightHubController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SpotlightHubTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'spotlight:hub:test';
    protected $description = 'Comprehensive test suite for Centralized Spotlight Hub and Carousel Management across 5 modules';
    protected $usage       = 'spotlight:hub:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    PUSAT MANAJEMEN CAROUSEL & SOROTAN — COMPREHENSIVE TEST       ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();

        // 1. Test Index Execution
        $html = '';
        $this->testModule("Spotlight Hub Controller Index Execution", function () use (&$html, $db) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            // Mock session user
            $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
            $adminId   = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 13;
            session()->set(['user_id' => $adminId, 'role' => 'superadmin', 'is_logged_in' => true]);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $html = (string) $controller->index();
            $this->assert(is_string($html) && strlen($html) > 1000, "Spotlight hub rendered with length > 1000 chars");
        });

        // 2. Test Header, Metrics & Tabs
        $this->testModule("Spotlight Hub Layout, Metrics & 5-Module Tabs", function () use ($html) {
            $this->assert(str_contains($html, 'Pusat Manajemen Carousel &amp; Sorotan') || str_contains($html, 'Pusat Manajemen Carousel & Sorotan'), "Page Title rendered");
            $this->assert(str_contains($html, 'Aktif di Hero Slider'), "KPI Carousel rendered");
            $this->assert(str_contains($html, 'Total Item Unggulan'), "KPI Featured rendered");
            $this->assert(str_contains($html, 'tab-overview'), "Overview tab present");
            $this->assert(str_contains($html, 'tab-arena'), "Arena tab present");
            $this->assert(str_contains($html, 'tab-knowledge'), "Knowledge tab present");
            $this->assert(str_contains($html, 'tab-regulation'), "Regulation tab present");
            $this->assert(str_contains($html, 'tab-news'), "News tab present");
            $this->assert(str_contains($html, 'tab-event'), "Event tab present");
        });

        // 3. Test Partial Row Controls
        $this->testModule("Spotlight Hub Table Rows and Switches", function () use ($html) {
            $this->assert(str_contains($html, 'spotlight-toggle-switch'), "Toggle switches present in tables");
            $this->assert(str_contains($html, 'spotlight-order-input'), "Order inputs present in tables");
            $this->assert(str_contains($html, 'settingsModal'), "Settings modal present");
        });

        // 4. Test Carousel Toggle Endpoint (AJAX)
        $this->testModule("AJAX Toggle carousel_enabled for Program", function () use ($db) {
            $program = $db->table('programs')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($program), "Found program record for test");

            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
            $_POST['feature'] = 'program';
            $_POST['id']      = (string) $program['id'];
            $_POST['field']   = 'carousel_enabled';
            $_POST['value']   = '1';

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel/toggle');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request = $request->withMethod('POST')->setHeader('X-Requested-With', 'XMLHttpRequest');
            \Config\Services::injectMock('request', $request);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->toggle();
            $body = json_decode((string) $response->getBody(), true);

            $this->assert(! empty($body['success']), "Toggle response successful: " . ($body['message'] ?? 'no message'));
            $this->assert((int) $body['new_state'] === 1, "New state is 1");

            // Verify in DB
            $updated = $db->table('programs')->where('id', $program['id'])->get()->getRowArray();
            $this->assert((int) $updated['carousel_enabled'] === 1, "Database record updated to carousel_enabled = 1");

            // Revert back
            $_POST['value'] = (string) ($program['carousel_enabled'] ?? 0);
            $controller->toggle();
        });

        // 5. Test Featured Toggle Endpoint (AJAX)
        $this->testModule("AJAX Toggle is_featured for Regulation", function () use ($db) {
            $reg = $db->table('regulations')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($reg), "Found regulation record for test");

            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
            $_POST['feature'] = 'regulation';
            $_POST['id']      = (string) $reg['id'];
            $_POST['field']   = 'is_featured';
            $_POST['value']   = '1';

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel/toggle');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request = $request->withMethod('POST')->setHeader('X-Requested-With', 'XMLHttpRequest');
            \Config\Services::injectMock('request', $request);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->toggle();
            $body = json_decode((string) $response->getBody(), true);

            $this->assert(! empty($body['success']), "Toggle featured response successful: " . ($body['message'] ?? 'no message'));
            $this->assert((int) $body['new_state'] === 1, "New state is 1");

            // Verify in DB
            $updated = $db->table('regulations')->where('id', $reg['id'])->get()->getRowArray();
            $this->assert((int) $updated['is_featured'] === 1, "Database record updated to is_featured = 1");

            // Revert back
            $_POST['value'] = (string) ($reg['is_featured'] ?? 0);
            $controller->toggle();
        });

        // 6. Test Update Order Endpoint (AJAX)
        $this->testModule("AJAX Update Slide Order for News", function () use ($db) {
            $news = $db->table('news')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($news), "Found news record for test");

            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
            $_POST['feature'] = 'news';
            $_POST['id']      = (string) $news['id'];
            $_POST['order']   = '3';

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel/update-order');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request = $request->withMethod('POST')->setHeader('X-Requested-With', 'XMLHttpRequest');
            \Config\Services::injectMock('request', $request);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->updateOrder();
            $body = json_decode((string) $response->getBody(), true);

            $this->assert(! empty($body['success']), "Order update response successful: " . ($body['message'] ?? 'no message'));
            $this->assert((int) $body['order'] === 3, "New order is 3");

            // Verify in DB
            $updated = $db->table('news')->where('id', $news['id'])->get()->getRowArray();
            $this->assert((int) $updated['carousel_order'] === 3, "Database record updated to carousel_order = 3");

            // Revert back
            $_POST['order'] = $news['carousel_order'] !== null ? (string) $news['carousel_order'] : '';
            $controller->updateOrder();
        });

        // 7. Test Save Settings
        $this->testModule("Save Settings for Feature Spotlight Mode & Limit", function () {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST['group']           = 'learning';
            $_POST['spotlight_mode']  = 'all';
            $_POST['spotlight_limit'] = '6';

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel/pengaturan');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request = $request->withMethod('POST');
            \Config\Services::injectMock('request', $request);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->saveSettings();
            $this->assert(in_array($response->getStatusCode(), [301, 302, 303, 307, 308], true), "Settings saved and redirects back with 302");

            $settingService = new \App\Services\SystemSettingService();
            $mode = $settingService->get('learning', 'spotlight_mode');
            $this->assert($mode === 'all', "Saved spotlight_mode verified in settings");
        });

        // 8. Test Search Available Items
        $this->testModule("Search Available Items for Carousel (AJAX)", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/sorotan-carousel/search-available?feature=program&q=');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $_GET['feature'] = 'program';
            $_GET['q']       = '';
            \Config\Services::injectMock('request', $request);

            $controller = new SpotlightHubController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->searchAvailable();
            $body = json_decode((string) $response->getBody(), true);

            $this->assert(! empty($body['success']), "Search response successful");
            $this->assert(is_array($body['results']), "Search results array returned");
        });

        // 9. Test Sidebar Navigation Entry
        $this->testModule("Sidebar Navigation Entry for Centralized Carousel", function () {
            $sidebarPath = APPPATH . 'Views/dashboard/components/sidebar.php';
            $content = file_get_contents($sidebarPath);
            $this->assert(str_contains($content, 'dashboard/landing/sorotan-carousel'), "Sidebar contains route URL");
            $this->assert(str_contains($content, 'Pusat Carousel &amp; Sorotan') || str_contains($content, 'Pusat Carousel & Sorotan'), "Sidebar contains label");
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
            CLI::write(sprintf("  [PASS] %s", $name), "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write(sprintf("  [FAIL] %s: %s", $name, $e->getMessage()), "red");
            CLI::write(sprintf("         in %s:%d", $e->getFile(), $e->getLine()), "yellow");
            $this->failed++;
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException("Assertion failed: " . $message);
        }
    }
}
