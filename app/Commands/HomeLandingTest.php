<?php

namespace App\Commands;

use App\Controllers\Landing\HomeController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class HomeLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'home:landing:test';
    protected $description = 'Comprehensive test suite for the redesigned BegawiPBJ Homepage Landing Page';
    protected $usage       = 'home:landing:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPBJ HOMEPAGE LANDING PAGE — COMPREHENSIVE TEST SUITE     ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // Setup mock request with SiteURI
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), '');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        $controller = new HomeController();
        $controller->initController($incomingRequest, service('response'), service('logger'));

        // Test 1: Home Index Rendering
        $html = '';
        $this->testModule("Homepage Controller Execution", function () use ($controller, &$html) {
            $html = $controller->index();
            $this->assert(is_string($html) && strlen($html) > 1000, "Homepage HTML produced and length > 1000 chars");
        });

        // Test 2: Hero Section Elements
        $this->testModule("Hero Section & Unified Search", function () use ($html) {
            $this->assert(str_contains($html, 'Pemerintah Provinsi Lampung &middot; Biro PBJ Setda'), "Hero badge present");
            $this->assert(str_contains($html, 'Ekosistem Digital Terpadu'), "Hero title present");
            $this->assert(str_contains($html, 'heroSearchForm'), "Unified search form present");
            $this->assert(str_contains($html, 'heroSearchTarget'), "Search target dropdown present");
        });

        // Test 3: KPI Metrics Ribbon
        $this->testModule("Live KPI Metrics Ribbon", function () use ($html) {
            $this->assert(str_contains($html, 'Regulasi PBJ Terbit'), "KPI Regulations label present");
            $this->assert(str_contains($html, 'Kursus &amp; Materi Belajar'), "KPI Courses label present");
            $this->assert(str_contains($html, 'Artikel &amp; Template Dokumen'), "KPI Knowledge label present");
            $this->assert(str_contains($html, 'Aparatur &amp; Peserta Terdaftar'), "KPI Users label present");
        });

        // Test 4: 5 Pillars of BegawiPBJ
        $this->testModule("5 Pillars Ecosystem Grid", function () use ($html) {
            $this->assert(str_contains($html, 'Arena Belajar'), "Pillar Arena Belajar present");
            $this->assert(str_contains($html, 'Bunga Rampai PBJ'), "Pillar Bunga Rampai present");
            $this->assert(str_contains($html, 'Regulasi &amp; Payung Hukum'), "Pillar Regulasi present");
            $this->assert(str_contains($html, 'Klinik Konsultasi &amp; Tanya Jawab'), "Pillar Klinik PBJ present");
            $this->assert(str_contains($html, 'Verifikasi Sertifikat Digital'), "Pillar Verifikasi present");
        });

        // Test 5: Procurement Lifecycle 5 Stages
        $this->testModule("Procurement Stages Lifecycle Roadmap", function () use ($html) {
            $this->assert(str_contains($html, 'Perencanaan Pengadaan'), "Stage 1 present");
            $this->assert(str_contains($html, 'Persiapan Pengadaan'), "Stage 2 present");
            $this->assert(str_contains($html, 'Pemilihan Penyedia'), "Stage 3 present");
            $this->assert(str_contains($html, 'Pelaksanaan Kontrak'), "Stage 4 present");
            $this->assert(str_contains($html, 'Serah Terima Hasil'), "Stage 5 present");
        });

        // Test 6: Bunga Rampai Multi-Tab Showcase
        $this->testModule("Bunga Rampai Multi-Tab Showcase", function () use ($html) {
            $this->assert(str_contains($html, 'tab-articles'), "Articles tab pane present");
            $this->assert(str_contains($html, 'tab-templates'), "Templates tab pane present");
            $this->assert(str_contains($html, 'tab-cases'), "Case studies tab pane present");
            $this->assert(str_contains($html, 'tab-faqs'), "FAQs tab pane present");
        });

        // Test 7: Sub-Menu in Navigation
        $this->testModule("Navigation Sub-Menus (Regulasi & Bunga Rampai)", function () use ($html) {
            $this->assert(str_contains($html, 'Peraturan Presiden (Perpres)'), "Regulasi sub-menu Perpres present in navbar");
            $this->assert(str_contains($html, 'Peraturan LKPP (PerLKPP)'), "Regulasi sub-menu PerLKPP present in navbar");
            $this->assert(str_contains($html, 'Template Dokumen Kerja'), "Bunga Rampai sub-menu template present in navbar");
            $this->assert(str_contains($html, 'Studi Kasus Lapangan'), "Bunga Rampai sub-menu studi kasus present in navbar");
        });

        // Test 8: Biro PBJ & Partner Links
        $this->testModule("Biro PBJ Info & External Portals", function () use ($html) {
            $this->assert(str_contains($html, 'LPSE Provinsi Lampung'), "LPSE link present");
            $this->assert(str_contains($html, 'SiRUP LKPP'), "SiRUP link present");
            $this->assert(str_contains($html, 'Katalog Elektronik (E-Katalog LKPP)'), "E-Katalog link present");
        });

        // Test 9: Upcoming Events & Agenda PBJ Integration
        $this->testModule("Upcoming Events & Agenda Integration", function () use ($html) {
            $this->assert(str_contains($html, 'Event, Webinar &amp; Bimtek Mendatang'), "Event section heading present");
            $this->assert(str_contains($html, 'event-card-modern'), "Modern event card class present");
            $this->assert(str_contains($html, 'event-date-badge'), "Event date badge present");
            $this->assert(str_contains($html, 'site_url(\'event') || str_contains($html, '/event'), "Event detail route link present");
        });

        // Test 10: Regulation & Course Badges and Actions
        $this->testModule("Card Metadata (JP Duration, Legal Status & Downloads)", function () use ($html) {
            $this->assert(str_contains($html, 'JP'), "JP duration badge present on courses");
            $this->assert(str_contains($html, 'reg-status-badge'), "Legal status badge present on regulations");
            $this->assert(str_contains($html, '/unduh'), "Direct download links present");
        });

        // Test 11: Unified Search Targets Alignment
        $this->testModule("Search Gateway 5-Module Targeting", function () use ($html) {
            $this->assert(str_contains($html, 'value="event"'), "Event search option present");
            $this->assert(str_contains($html, 'value="verifikasi-sertifikat"'), "Certificate verification search option present");
            $this->assert(str_contains($html, 'value="regulasi"'), "Regulasi search option present");
            $this->assert(str_contains($html, 'value="arena-belajar/course"'), "Course search option present");
        });

        // Test 12: Zero Dummy & Warning Integrity
        $this->testModule("Content Integrity & Zero Dummy", function () use ($html) {
            $this->assert(! str_contains(strtolower($html), 'lorem ipsum'), "Zero lorem ipsum in rendered HTML");
            $this->assert(! str_contains($html, 'bbbbbbb'), "Zero test dummy 'bbbbbbb' in rendered HTML");
            $this->assert(! str_contains($html, 'zfdfdfsdfd'), "Zero test dummy 'zfdfdfsdfd' in rendered HTML");
            $this->assert(! str_contains($html, 'xvxxb'), "Zero test dummy 'xvxxb' in rendered HTML");
            $this->assert(! str_contains($html, 'Warning:'), "Zero PHP warnings in rendered HTML");
            $this->assert(! str_contains($html, 'Notice:'), "Zero PHP notices in rendered HTML");
            $this->assert(! str_contains($html, 'Fatal error:'), "Zero Fatal errors in rendered HTML");
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
}
