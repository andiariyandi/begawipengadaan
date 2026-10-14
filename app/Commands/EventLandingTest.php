<?php

namespace App\Commands;

use App\Controllers\Landing\EventPublicController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Exceptions\PageNotFoundException;

class EventLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'event:landing:test';
    protected $description = 'Comprehensive test suite for the redesigned BegawiPBJ Event & Agenda Landing Page';
    protected $usage       = 'event:landing:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    BEGAWIPBJ EVENT & AGENDA LANDING — COMPREHENSIVE TEST SUITE   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // 1. Test Event Index Rendering
        $html = '';
        $this->testModule("Event Controller Index Execution", function () use (&$html) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'event');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new EventPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->index();
            $this->assert(is_string($html) && strlen($html) > 1000, "Event index HTML output produced and length > 1000 chars");
        });

        // 2. Test Hero Banner & Search
        $this->testModule("Hero Banner, Search & Breadcrumb", function () use ($html) {
            $this->assert(str_contains($html, 'Agenda Kegiatan &amp; Pembinaan') || str_contains($html, 'Agenda Kegiatan & Pembinaan'), "Hero title present");
            $this->assert(str_contains($html, 'Pengadaan Barang/Jasa Provinsi Lampung'), "Hero subtitle present");
            $this->assert(str_contains($html, 'evt-search-box'), "Hero search form present");
            $this->assert(str_contains($html, 'Beranda'), "Breadcrumb beranda present");
        });

        // 3. Test Live KPI Metrics Ribbon
        $this->testModule("Live KPI Metrics Ribbon", function () use ($html) {
            $this->assert(str_contains($html, 'Total Agenda Terdaftar'), "KPI Total Events label present");
            $this->assert(str_contains($html, 'Kegiatan Mendatang'), "KPI Upcoming Events label present");
            $this->assert(str_contains($html, 'Pendaftaran Dibuka'), "KPI Open Registration label present");
            $this->assert(str_contains($html, 'Peserta Terdaftar'), "KPI Attendees label present");
        });

        // 4. Test View Mode Tabs & Filter Pills
        $this->testModule("View Mode Tabs & Filter Pills", function () use ($html) {
            $this->assert(str_contains($html, 'Semua Kegiatan'), "All events tab present");
            $this->assert(str_contains($html, 'Agenda Mendatang'), "Upcoming agenda tab present");
            $this->assert(str_contains($html, 'Kalender Kegiatan'), "Calendar tab present");
            $this->assert(str_contains($html, 'evt-pill'), "Filter pills present");
        });

        // 5. Test Agenda & Calendar Routes
        $this->testModule("Agenda & Calendar Route Execution", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'event/agenda');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new EventPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $agendaHtml = $controller->agenda();
            $this->assert(is_string($agendaHtml) && strlen($agendaHtml) > 1000, "Agenda page rendered");

            $calHtml = $controller->calendar();
            $this->assert(is_string($calHtml) && strlen($calHtml) > 1000, "Calendar page rendered");
            $this->assert(str_contains($calHtml, 'Kalender Kegiatan'), "Calendar title rendered");
        });

        // 6. Test Event Detail Page Rendering
        $this->testModule("Event Detail Page Execution (/event/{slug})", function () {
            $db = db_connect();
            $event = $db->table('events')->where('visibility', 'public')->where('deleted_at', null)->get()->getRowArray();
            $this->assert(! empty($event), "At least one public event found in DB");

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'event/' . $event['slug']);
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new EventPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $detailHtml = $controller->detail($event['slug']);
            $this->assert(is_string($detailHtml) && strlen($detailHtml) > 1000, "Detail page rendered");
            $this->assert(str_contains($detailHtml, esc($event['title'])), "Event title present in detail view");
            $this->assert(str_contains($detailHtml, 'Susunan Acara (Rundown Kegiatan)'), "Rundown table present");
            $this->assert(str_contains($detailHtml, 'Pendaftaran Peserta') || str_contains($detailHtml, 'evt-register-widget'), "Registration widget present");
            $this->assert(str_contains($detailHtml, 'Narasumber &amp; Fasilitator Ahli') || str_contains($detailHtml, 'Narasumber & Fasilitator Ahli'), "Speakers section present");
            $this->assert(str_contains($detailHtml, 'Fasilitas Peserta'), "Facilities section present");
        });

        // 7. Test 404 Exception Handling for Invalid Slugs
        $this->testModule("404 Exception Handling for Invalid Event Slugs", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'event/non-existent-event-slug');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new EventPublicController();
            $controller->initController($request, service('response'), service('logger'));

            $caught = false;
            try {
                $controller->detail('non-existent-event-slug-xyz');
            } catch (PageNotFoundException $e) {
                $caught = true;
            }
            $this->assert($caught, "PageNotFoundException thrown for invalid event slug");
        });

        // 8. Test Navigation Header Sub-menu
        $this->testModule("Navigation Header Sub-menu for Event & Agenda", function () {
            $headerFile = APPPATH . 'Views/landing/components/header.php';
            $content = file_get_contents($headerFile);
            $this->assert(str_contains($content, '<li><a href="<?= site_url(\'event\') ?>">Semua Event &amp; Agenda</a></li>'), "Semua Event menu item present");
            $this->assert(str_contains($content, 'site_url(\'event/agenda\')'), "Agenda Mendatang sub-menu present");
            $this->assert(str_contains($content, 'site_url(\'event?tipe=webinar\')'), "Webinar sub-menu present");
            $this->assert(str_contains($content, 'site_url(\'event?tipe=bimtek\')'), "Bimtek sub-menu present");
            $this->assert(str_contains($content, 'site_url(\'event/kalender\')'), "Kalender sub-menu present");
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
