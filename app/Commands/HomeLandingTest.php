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
            $this->assert(str_contains($html, 'Transformasi Digital Terpadu'), "Hero title present");
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

        // Test 13: Visitor Statistics & Feature Access Breakdown
        $this->testModule("Visitor Statistics & Feature Access Analytics", function () use ($html) {
            $this->assert(str_contains($html, 'Statistik Trafik &amp; Kunjungan Portal'), "Visitor statistics heading present");
            $this->assert(str_contains($html, 'visitor-pulse-dot'), "Live realtime pulse dot present");
            $this->assert(str_contains($html, 'Hari Ini'), "Home today metric present");
            $this->assert(str_contains($html, 'Minggu Ini'), "Home this week metric present");
            $this->assert(str_contains($html, 'Bulan Ini'), "Home this month metric present");
            $this->assert(str_contains($html, 'Tahun Ini'), "Home this year metric present");
            $this->assert(str_contains($html, 'Total Akumulasi Pembukaan'), "Home total accumulation present");
            $this->assert(str_contains($html, 'Frekuensi Akses Fitur Unggulan'), "Feature stats card present");
            $this->assert(str_contains($html, 'Arena Belajar'), "Arena Belajar feature metric present");
            $this->assert(str_contains($html, 'Bunga Rampai PBJ'), "Bunga Rampai feature metric present");
            $this->assert(str_contains($html, 'Regulasi PBJ'), "Regulasi feature metric present");
            $this->assert(str_contains($html, 'Berita &amp; Informasi'), "Berita feature metric present");
            $this->assert(str_contains($html, 'Event &amp; Agenda'), "Event feature metric present");
        });

        // Test 14: Interactive Testimonies Spotlight Slider Section
        $this->testModule("Interactive Testimonies Spotlight Slider Section", function () use ($html) {
            $this->assert(str_contains($html, 'section-testimonies'), "Testimonies section container present");
            $this->assert(str_contains($html, 'Testimoni &amp; Pengalaman Nyata Pengguna BegawiPengadaan'), "Testimonies heading present");
            $this->assert(str_contains($html, 'testimony-card'), "Testimony cards present");
            $this->assert(str_contains($html, 'spotlight-photo-frame'), "Prominent spotlight photo frame present");
            $this->assert(str_contains($html, 'Ahmad Zulkarnaen, S.T., M.T.'), "PPK testimony author present");
            $this->assert(str_contains($html, 'Rina Widyastuti, S.E., M.Si.'), "Pokja testimony author present");
            $this->assert(str_contains($html, 'testimonyDetailModal'), "Interactive testimony detail modal present");
        });

        // Test 15: Team Inovator & Pengembang Spotlight Slider Section
        $this->testModule("Team Inovator & Pengembang Spotlight Slider Section", function () use ($html) {
            $this->assert(str_contains($html, 'section-team'), "Team section container present");
            $this->assert(str_contains($html, 'Tim Inovator di Balik Ekosistem BegawiPengadaan'), "Team heading present");
            $this->assert(str_contains($html, 'team-card'), "Team cards present");
            $this->assert(str_contains($html, 'spotlight-photo-frame'), "Prominent spotlight photo frame present");
            $this->assert(str_contains($html, 'Dr. Ir. Fahrizal Darminto, M.A.'), "Sekretaris Daerah team member present");
            $this->assert(str_contains($html, 'Slamet Riadi, S.Sos., M.M.'), "Kepala Biro PBJ team member present");
            $this->assert(str_contains($html, 'Software Engineering &amp; UI/UX Crew'), "Software engineering team present");
            $this->assert(str_contains($html, 'team-values-banner'), "Team values banner present");
        });

        // Test 16: WhatsApp Floating Chat Widget (Bottom-Left)
        $this->testModule("WhatsApp Helpdesk Floating Chat Widget", function () use ($html) {
            $this->assert(str_contains($html, 'bp-wa-widget'), "WhatsApp widget container present");
            $this->assert(str_contains($html, 'bpWaTriggerBtn'), "WhatsApp trigger button present");
            $this->assert(str_contains($html, 'bpWaBox'), "WhatsApp popup box window present");
            $this->assert(str_contains($html, 'Helpdesk BegawiPengadaan'), "Helpdesk title present in chat header");
            $this->assert(str_contains($html, 'wa.me/6285117779783'), "Official WhatsApp destination URL present");
            $this->assert(str_contains($html, 'bp-wa-chip'), "Quick topic chips present in chat body");
        });

        // Test 17: Home Landing Page Exclusive Popup & Responsive Slider
        $this->testModule("Home Exclusive Popup Announcement & Responsive Slider", function () use ($html) {
            $this->assert(str_contains($html, 'home-promo-overlay'), "Home exclusive popup overlay present");
            $this->assert(str_contains($html, 'home-promo-carousel'), "Popup responsive slider carousel present");
            $this->assert(str_contains($html, 'homePromoCloseBtn'), "Close button present in popup");
            $this->assert(str_contains($html, 'home-promo-img'), "Slider images present in popup");
            $this->assert(str_contains($html, 'home-promo-indicators'), "Carousel indicators present in popup");
            $this->assert(str_contains($html, 'home-promo-reopen-btn'), "Reopen trigger button present");
        });

        // ==========================================
        // 18. TEST NAVBAR SIMPLIFICATION & SURVEI KEPUASAN PENGGUNA (BEFORE & AFTER SUBMIT)
        // ==========================================
        $this->testModule("Navbar Simplification & Survei Kepuasan (Single Button & Hasil Publik)", function () use ($html) {
            $headerContent = file_get_contents(APPPATH . 'Views/landing/components/header.php');

            // 1. Eksplorasi and Search removed from header
            $this->assert(! str_contains($headerContent, 'main-menu__category-box'), "Eksplorasi category box removed from header");
            $this->assert(! str_contains($headerContent, 'main-menu__search-cart-box'), "Search cart box removed from header");

            // 2. No redundant menu list link, exactly single button trigger in right header
            $this->assert(! str_contains($headerContent, 'main-menu__survey-nav-item'), "Redundant survey nav-item removed from list");
            $this->assert(str_contains($headerContent, 'main-menu__survey-btn'), "Single Survei Kepuasan button trigger present in header");
            $this->assert(str_contains($headerContent, 'site_url(\'statistik/survei-kepuasan\')'), "Public survey results link present in Lainnya dropdown");

            // 3. Survey Modal Present on Landing Page
            $this->assert(str_contains($html, 'id="modalSurveyKepuasan"'), "Survey modal container present on landing page");

            // 4. Form State (Before Submit)
            $this->assert(str_contains($html, 'id="surveyFormBeforeSubmit"'), "Survey Form state before submit present");
            $this->assert(str_contains($html, 'survey-emoji-card'), "Interactive rating emojis present");
            $this->assert(str_contains($html, 'survey-aspect-item'), "4 Service aspects ratings present");
            $this->assert(str_contains($html, 'surveyIsAnonymous'), "Anonymous toggle switch present");
            $this->assert(str_contains($html, 'btn-quick-tag'), "Quick suggestion tags present");
            $this->assert(str_contains($html, 'survey-btn-submit'), "Survey submit button present");

            // 5. Success State (After Submit)
            $this->assert(str_contains($html, 'id="surveySuccessAfterSubmit"'), "Survey Success state after submit present");
            $this->assert(str_contains($html, 'survey-receipt-card'), "Survey receipt summary card present");
            $this->assert(str_contains($html, 'receiptTicketNumber'), "Ticket number receipt field present");
            $this->assert(str_contains($html, 'surveyBtnFillAgain'), "Fill again / reset action button present");

            // 6. Test Model & Database persistence
            $model = new \App\Models\SatisfactionSurveyModel();
            $ticket = $model->generateTicketNumber();
            $this->assert(str_starts_with($ticket, 'IKM-'), "Ticket number correctly formatted with IKM prefix");

            $inserted = $model->insert([
                'ticket_number'              => $ticket,
                'rating_overall'             => 5,
                'service_access_rating'      => 5,
                'service_regulation_rating'  => 5,
                'service_learning_rating'    => 5,
                'service_helpdesk_rating'    => 5,
                'respondent_role'            => 'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)',
                'is_anonymous'               => 1,
                'feedback'                   => 'Layanan BegawiPengadaan sangat informatif dan mempermudah akses PBJ Provinsi Lampung.',
                'recommendation'             => 'Pasti Merekomendasikan',
            ]);
            $this->assert($inserted !== false, "Satisfaction survey record persisted to database");

            // Cleanup test record
            $model->where('ticket_number', $ticket)->delete();

            // 7. Verify Public Survey Results UI (/statistik/survei-kepuasan)
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'statistik/survei-kepuasan');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $statController = new \App\Controllers\Landing\StatisticPublicController();
            $statController->initController($request, service('response'), service('logger'));
            $surveyResultsHtml = $statController->satisfactionSurvey();

            $this->assert(str_contains($surveyResultsHtml, 'Hasil Survei Kepuasan Publik (IKM)'), "Survey results title present");
            $this->assert(str_contains($surveyResultsHtml, 'Capaian Indeks IKM'), "Capaian Indeks IKM card present");
            $this->assert(str_contains($surveyResultsHtml, 'Penilaian 4 Dimensi Aspek Mutu Layanan'), "4 Dimensions present");
            $this->assert(str_contains($surveyResultsHtml, 'Distribusi Kategori Responden'), "Respondent distribution present");
            $this->assert(str_contains($surveyResultsHtml, 'Aspirasi &amp; Ulasan Publik Terverifikasi') || str_contains($surveyResultsHtml, 'Aspirasi & Ulasan'), "Public feedback section present");
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
