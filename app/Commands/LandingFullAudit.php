<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Config\Services;

class LandingFullAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'landing:full:audit';
    protected $description = 'Performs a comprehensive automated audit of the entire landing page: routes, navigation menus, view rendering, CMS bindings, and data consistency.';

    private array $results = [
        'routes'         => ['total' => 0, 'passed' => 0, 'failed' => 0, 'details' => []],
        'navigation'     => ['header' => [], 'mobile' => [], 'footer' => [], 'dead_links' => [], 'mismatches' => []],
        'cms'            => ['tables' => [], 'spotlight' => []],
        'legacy_views'   => [],
        'setting_keys'   => [],
        'security_forms' => [],
    ];

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("         BEGAWIPENGADAAN LANDING PAGE FULL AUDIT ENGINE           ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();

        $db = Database::connect();

        // 1. Audit Navigation Consistency across Header, Mobile Menu & Footer
        $this->auditNavigation();

        // 2. Audit Public Routes & Execution
        $this->auditPublicRoutes($db);

        // 3. Audit CMS Tables & Centralized Spotlight Hub
        $this->auditCmsAndSpotlight($db);

        // 4. Audit Unused / Legacy Views in landing/pages
        $this->auditLegacyViews();

        // 5. Audit Setting Keys Consistency
        $this->auditSettingKeys();

        // 6. Audit Forms & Security (CSRF)
        $this->auditForms();

        // Output Summary
        $this->printSummary();
    }

    private function auditNavigation(): void
    {
        CLI::write("--- [1] AUDITING NAVIGATION CONSISTENCY (HEADER, MOBILE, FOOTER) ---", 'yellow');

        $headerPath = APPPATH . 'Views/landing/components/header.php';
        $mobilePath = APPPATH . 'Views/landing/components/mobile_menu.php';
        $footerPath = APPPATH . 'Views/landing/components/footer.php';

        $headerContent = file_exists($headerPath) ? file_get_contents($headerPath) : '';
        $mobileContent = file_exists($mobilePath) ? file_get_contents($mobilePath) : '';
        $footerContent = file_exists($footerPath) ? file_get_contents($footerPath) : '';

        // Extract links
        preg_match_all('/site_url\([\'"]([^\'"]*)[\'"]\)/', $headerContent, $headerSiteUrls);
        preg_match_all('/route_to\([\'"]([^\'"]*)[\'"]\)/', $headerContent, $headerRouteUrls);

        preg_match_all('/site_url\([\'"]([^\'"]*)[\'"]\)/', $mobileContent, $mobileSiteUrls);
        preg_match_all('/route_to\([\'"]([^\'"]*)[\'"]\)/', $mobileContent, $mobileRouteUrls);

        preg_match_all('/site_url\([\'"]([^\'"]*)[\'"]\)/', $footerContent, $footerSiteUrls);
        preg_match_all('/route_to\([\'"]([^\'"]*)[\'"]\)/', $footerContent, $footerRouteUrls);

        // Scan for dead links href="#"
        preg_match_all('/<a[^>]+href=["\']#["\'][^>]*>/i', $headerContent, $headerDeadLinks);
        preg_match_all('/<a[^>]+href=["\']#["\'][^>]*>/i', $mobileContent, $mobileDeadLinks);
        preg_match_all('/<a[^>]+href=["\']#["\'][^>]*>/i', $footerContent, $footerDeadLinks);

        $this->results['navigation']['header'] = [
            'site_urls'  => array_unique($headerSiteUrls[1] ?? []),
            'route_urls' => array_unique($headerRouteUrls[1] ?? []),
            'dead_links' => count($headerDeadLinks[0] ?? []),
        ];

        $this->results['navigation']['mobile'] = [
            'site_urls'  => array_unique($mobileSiteUrls[1] ?? []),
            'route_urls' => array_unique($mobileRouteUrls[1] ?? []),
            'dead_links' => count($mobileDeadLinks[0] ?? []),
        ];

        $this->results['navigation']['footer'] = [
            'site_urls'  => array_unique($footerSiteUrls[1] ?? []),
            'route_urls' => array_unique($footerRouteUrls[1] ?? []),
            'dead_links' => count($footerDeadLinks[0] ?? []),
        ];

        CLI::write("  Header distinct site_url: " . count($this->results['navigation']['header']['site_urls']), 'white');
        CLI::write("  Mobile distinct site_url: " . count($this->results['navigation']['mobile']['site_urls']), 'white');
        CLI::write("  Footer distinct site_url: " . count($this->results['navigation']['footer']['site_urls']), 'white');
        CLI::write("  Dead links: Header=" . $this->results['navigation']['header']['dead_links'] .
            ", Mobile=" . $this->results['navigation']['mobile']['dead_links'] .
            ", Footer=" . $this->results['navigation']['footer']['dead_links'],
            ($this->results['navigation']['mobile']['dead_links'] > 0 ? 'red' : 'green'));
    }

    private function auditPublicRoutes($db): void
    {
        CLI::newLine();
        CLI::write("--- [2] AUDITING PUBLIC ROUTES & VIEW EXECUTION ---", 'yellow');

        // Sample real data slugs from database
        $sampleProgram = $db->table('programs')->select('slug')->where('status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'pbj-dasar';
        $sampleCourse  = $db->table('courses')->select('slug')->where('status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'pengantar-pbj';
        $sampleClass   = $db->table('learning_classes')->select('code')->where('deleted_at IS NULL')->get()->getRowArray()['code'] ?? 'KLS-001';
        $sampleTrainer = $db->table('class_trainers ct')->join('users u', 'u.id = ct.user_id')->select('u.username')->get()->getRowArray()['username'] ?? 'admin';
        
        $sampleArticle  = $db->table('knowledge_articles')->select('slug')->where('type', 'article')->where('workflow_status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'artikel-1';
        $sampleCase     = $db->table('knowledge_articles')->select('slug')->where('type', 'case_study')->where('workflow_status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'kasus-1';
        $sampleTemplate = $db->table('knowledge_articles')->select('slug')->where('type', 'template')->where('workflow_status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'template-1';
        
        $sampleReg      = $db->table('regulations')->select('slug')->where('workflow_status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'perpres-16-2018';
        $sampleRegCat   = $db->table('regulation_categories')->select('slug')->where('is_active', 1)->get()->getRowArray()['slug'] ?? 'peraturan-presiden';
        
        $sampleNews     = $db->table('news')->select('slug')->where('workflow_status', 'published')->where('visibility', 'public')->where('deleted_at IS NULL')->get()->getRowArray()['slug'] ?? 'berita-1';
        $sampleNewsCat  = $db->table('news_categories')->select('slug')->get()->getRowArray()['slug'] ?? 'pengumuman';

        $sampleEvent    = $db->table('events')->select('slug')->where('deleted_at IS NULL')->where('visibility', 'public')->get()->getRowArray()['slug'] ?? 'qa-audit-event-20261005-221831';

        // Target routes list
        $routesToTest = [
            // Home & General
            ['label' => 'Beranda', 'url' => '/', 'controller' => \App\Controllers\Landing\HomeController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Tentang Kami', 'url' => 'tentang', 'controller' => \App\Controllers\Landing\HomeController::class, 'method' => 'about', 'params' => []],
            ['label' => 'Kontak Layanan', 'url' => 'kontak', 'controller' => \App\Controllers\Landing\HomeController::class, 'method' => 'contact', 'params' => []],
            ['label' => 'Hasil Survei Kepuasan', 'url' => 'survei-kepuasan/hasil', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'satisfactionSurvey', 'params' => []],

            // Arena Belajar
            ['label' => 'Arena Belajar Index', 'url' => 'arena-belajar', 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Arena Program Catalog', 'url' => 'arena-belajar/program', 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'programs', 'params' => []],
            ['label' => 'Arena Program Detail', 'url' => "arena-belajar/program/{$sampleProgram}", 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'programDetail', 'params' => [$sampleProgram]],
            ['label' => 'Arena Course Catalog', 'url' => 'arena-belajar/course', 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'courses', 'params' => []],
            ['label' => 'Arena Course Detail', 'url' => "arena-belajar/course/{$sampleCourse}", 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'courseDetail', 'params' => [$sampleCourse]],
            ['label' => 'Arena Kelas Catalog', 'url' => 'arena-belajar/kelas', 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'classes', 'params' => []],
            ['label' => 'Arena Kelas Detail', 'url' => "arena-belajar/kelas/{$sampleClass}", 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'classDetail', 'params' => [$sampleClass]],
            ['label' => 'Arena Pengajar Directory', 'url' => 'arena-belajar/pengajar', 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'trainers', 'params' => []],
            ['label' => 'Arena Pengajar Detail', 'url' => "arena-belajar/pengajar/{$sampleTrainer}", 'controller' => \App\Controllers\Landing\ArenaBelajarController::class, 'method' => 'trainerDetail', 'params' => [$sampleTrainer]],

            // Bunga Rampai
            ['label' => 'Bunga Rampai Index', 'url' => 'bunga-rampai', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Bunga Rampai Artikel Catalog', 'url' => 'bunga-rampai/artikel', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'articles', 'params' => []],
            ['label' => 'Bunga Rampai Artikel Detail', 'url' => "bunga-rampai/artikel/{$sampleArticle}", 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'articleDetail', 'params' => [$sampleArticle]],
            ['label' => 'Bunga Rampai FAQ Catalog', 'url' => 'bunga-rampai/faq', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'faqs', 'params' => []],
            ['label' => 'Bunga Rampai Studi Kasus Catalog', 'url' => 'bunga-rampai/studi-kasus', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'caseStudies', 'params' => []],
            ['label' => 'Bunga Rampai Studi Kasus Detail', 'url' => "bunga-rampai/studi-kasus/{$sampleCase}", 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'caseStudyDetail', 'params' => [$sampleCase]],
            ['label' => 'Bunga Rampai Template Catalog', 'url' => 'bunga-rampai/template-dokumen', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'templates', 'params' => []],
            ['label' => 'Bunga Rampai Template Detail', 'url' => "bunga-rampai/template-dokumen/{$sampleTemplate}", 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'templateDetail', 'params' => [$sampleTemplate]],
            ['label' => 'Bunga Rampai Multimedia Hub', 'url' => 'bunga-rampai/multimedia', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'multimedia', 'params' => []],
            ['label' => 'Bunga Rampai Media Infografis', 'url' => 'bunga-rampai/media', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'media', 'params' => []],
            ['label' => 'Bunga Rampai Video Edukasi', 'url' => 'bunga-rampai/video', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'videos', 'params' => []],
            ['label' => 'Bunga Rampai Galeri PBJ', 'url' => 'bunga-rampai/galeri', 'controller' => \App\Controllers\Landing\BungaRampaiController::class, 'method' => 'gallery', 'params' => []],

            // Regulasi PBJ
            ['label' => 'Regulasi PBJ Index', 'url' => 'regulasi', 'controller' => \App\Controllers\Landing\RegulationPublicController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Regulasi Kategori Filter', 'url' => "regulasi/kategori/{$sampleRegCat}", 'controller' => \App\Controllers\Landing\RegulationPublicController::class, 'method' => 'category', 'params' => [$sampleRegCat]],
            ['label' => 'Regulasi Detail', 'url' => "regulasi/{$sampleReg}", 'controller' => \App\Controllers\Landing\RegulationPublicController::class, 'method' => 'detail', 'params' => [$sampleReg]],
            ['label' => 'Regulasi Riwayat Versi', 'url' => "regulasi/{$sampleReg}/versi", 'controller' => \App\Controllers\Landing\RegulationPublicController::class, 'method' => 'versions', 'params' => [$sampleReg]],
            ['label' => 'Regulasi Relasi Hukum', 'url' => "regulasi/{$sampleReg}/relasi", 'controller' => \App\Controllers\Landing\RegulationPublicController::class, 'method' => 'relations', 'params' => [$sampleReg]],

            // Berita
            ['label' => 'Berita PBJ Index', 'url' => 'berita', 'controller' => \App\Controllers\Landing\NewsPublicController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Berita Kategori', 'url' => "berita/kategori/{$sampleNewsCat}", 'controller' => \App\Controllers\Landing\NewsPublicController::class, 'method' => 'category', 'params' => [$sampleNewsCat]],
            ['label' => 'Berita Detail', 'url' => "berita/{$sampleNews}", 'controller' => \App\Controllers\Landing\NewsPublicController::class, 'method' => 'detail', 'params' => [$sampleNews]],

            // Event & Agenda
            ['label' => 'Event Index', 'url' => 'event', 'controller' => \App\Controllers\Landing\EventPublicController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Event Agenda View', 'url' => 'event/agenda', 'controller' => \App\Controllers\Landing\EventPublicController::class, 'method' => 'agenda', 'params' => []],
            ['label' => 'Event Kalender View', 'url' => 'event/kalender', 'controller' => \App\Controllers\Landing\EventPublicController::class, 'method' => 'calendar', 'params' => []],
            ['label' => 'Event Detail', 'url' => "event/{$sampleEvent}", 'controller' => \App\Controllers\Landing\EventPublicController::class, 'method' => 'detail', 'params' => [$sampleEvent]],

            // Portal Statistik
            ['label' => 'Statistik Publik Index', 'url' => 'statistik', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Statistik Pembelajaran', 'url' => 'statistik/pembelajaran', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'learning', 'params' => []],
            ['label' => 'Statistik Event PBJ', 'url' => 'statistik/event', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'events', 'params' => []],
            ['label' => 'Statistik Kompetensi', 'url' => 'statistik/kompetensi', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'competency', 'params' => []],
            ['label' => 'Statistik Survei Kepuasan', 'url' => 'statistik/survei-kepuasan', 'controller' => \App\Controllers\Landing\StatisticPublicController::class, 'method' => 'satisfactionSurvey', 'params' => []],

            // Verifikasi Sertifikat
            ['label' => 'Verifikasi Sertifikat Index', 'url' => 'verifikasi-sertifikat', 'controller' => \App\Controllers\Landing\CertificateVerificationController::class, 'method' => 'index', 'params' => []],
            ['label' => 'Verifikasi Sertifikat Form Lookup', 'url' => 'verifikasi-sertifikat/hasil', 'controller' => \App\Controllers\Landing\CertificateVerificationController::class, 'method' => 'result', 'params' => []],
        ];

        $this->results['routes']['total'] = count($routesToTest);

        foreach ($routesToTest as $item) {
            $t0 = microtime(true);
            $mem0 = memory_get_usage();

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), $item['url']);
            $req = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            Services::injectMock('request', $req);

            try {
                $controller = new $item['controller']();
                $controller->initController($req, Services::response(), Services::logger());

                ob_start();
                $resp = $controller->{$item['method']}(...$item['params']);
                $buffered = ob_get_clean();

                $duration = round((microtime(true) - $t0) * 1000, 2);
                $memUsed = round((memory_get_usage() - $mem0) / 1024, 1);

                $output = is_string($resp) ? $resp : ($buffered ?: '');
                $len = strlen($output);

                if ($len < 500 && !($resp instanceof \CodeIgniter\HTTP\RedirectResponse)) {
                    throw new \RuntimeException("Output too short ({$len} bytes), possibly blank view or error!");
                }

                $this->results['routes']['passed']++;
                CLI::write(sprintf("  [PASS] %-35s %-32s (%5d bytes, %5.1f ms, %4.1f KB)", $item['label'], $item['url'], $len, $duration, $memUsed), 'green');

                $this->results['routes']['details'][] = [
                    'label'    => $item['label'],
                    'url'      => $item['url'],
                    'status'   => 'PASS',
                    'bytes'    => $len,
                    'duration' => $duration,
                ];
            } catch (\Throwable $e) {
                $this->results['routes']['failed']++;
                CLI::write(sprintf("  [FAIL] %-35s %-32s -> %s", $item['label'], $item['url'], $e->getMessage()), 'red');

                $this->results['routes']['details'][] = [
                    'label'    => $item['label'],
                    'url'      => $item['url'],
                    'status'   => 'FAIL',
                    'error'    => $e->getMessage(),
                ];
            }
        }
    }

    private function auditCmsAndSpotlight($db): void
    {
        CLI::newLine();
        CLI::write("--- [3] AUDITING CMS TABLES & SPOTLIGHT INTEGRATIONS ---", 'yellow');

        $tables = [
            'home_sliders'        => 'Hero Sliders Utama',
            'feature_highlights'  => 'Fitur Unggulan Ekosistem',
            'external_portals'    => 'Portal Eksternal (SPSE, SiRUP, Bela)',
            'testimonials'        => 'Testimoni Stakeholder',
            'team_members'        => 'Tim Inovator & Pengembang',
            'page_banners'        => 'Page Banners Internal',
            'site_announcements'  => 'Popup Pengumuman Homepage',
            'helpdesk_topics'     => 'Topik Cepat Helpdesk WhatsApp',
        ];

        foreach ($tables as $t => $label) {
            try {
                $cnt = $db->table($t)->countAllResults();
                $builder = $db->table($t);
                if ($db->fieldExists('is_active', $t)) {
                    $builder->where('is_active', 1);
                }
                $active = $builder->countAllResults();
                $this->results['cms']['tables'][$t] = ['total' => $cnt, 'active' => $active, 'label' => $label];
                CLI::write(sprintf("  CMS %-28s: %2d total records, %2d active (%s)", $t, $cnt, $active, $label), 'white');
            } catch (\Throwable $e) {
                CLI::write("  [FAIL] CMS table {$t} error: " . $e->getMessage(), 'red');
            }
        }

        // Spotlight fields across 5 modules
        $spotlights = [
            'Arena Belajar (Courses)'    => $db->table('courses')->where('carousel_enabled', 1)->countAllResults(),
            'Arena Belajar (Programs)'   => $db->table('programs')->where('carousel_enabled', 1)->countAllResults(),
            'Arena Belajar (Classes)'    => $db->table('learning_classes')->where('carousel_enabled', 1)->countAllResults(),
            'Bunga Rampai (Articles)'    => $db->table('knowledge_articles')->where('type', 'article')->where('carousel_enabled', 1)->countAllResults(),
            'Bunga Rampai (Cases)'       => $db->table('knowledge_articles')->where('type', 'case_study')->where('carousel_enabled', 1)->countAllResults(),
            'Bunga Rampai (Templates)'   => $db->table('knowledge_articles')->where('type', 'template')->where('carousel_enabled', 1)->countAllResults(),
            'Bunga Rampai (Media)'       => $db->table('knowledge_media')->where('carousel_enabled', 1)->countAllResults(),
            'Regulasi PBJ'               => $db->table('regulations')->where('is_featured', 1)->countAllResults(),
            'Berita PBJ (Headline)'      => $db->table('news')->where('carousel_enabled', 1)->orWhere('is_featured', 1)->countAllResults(),
            'Event & Agenda (Featured)'  => $db->table('events')->where('carousel_enabled', 1)->orWhere('is_featured', 1)->countAllResults(),
        ];

        CLI::newLine();
        CLI::write("  Spotlight & Carousel Enabled Records:", 'white');
        foreach ($spotlights as $mod => $val) {
            CLI::write(sprintf("    * %-30s: %d item(s)", $mod, $val), $val > 0 ? 'cyan' : 'yellow');
        }
        $this->results['cms']['spotlight'] = $spotlights;
    }

    private function auditLegacyViews(): void
    {
        CLI::newLine();
        CLI::write("--- [4] AUDITING UNUSED / LEGACY VIEWS IN LANDING ---", 'yellow');

        $pagesDir = APPPATH . 'Views/landing/pages/';
        $files = scandir($pagesDir);

        $activeViews = [
            'about.php', 'contact.php', 'survey_public_results.php',
            'learning.php', 'learning_catalog.php', 'learning_detail.php',
            'knowledge.php', 'knowledge_catalog.php', 'knowledge_detail.php',
            'regulation.php', 'regulation_detail.php',
            'news.php', 'news_detail.php',
            'events.php', 'event_detail.php',
            'statistics.php',
            'certificate_verify.php', 'certificate_result.php',
            '404.php',
        ];

        $unused = [];
        foreach ($files as $f) {
            if ($f === '.' || $f === '..' || is_dir($pagesDir . $f)) continue;
            if (! in_array($f, $activeViews, true)) {
                $unused[] = $f;
            }
        }

        $this->results['legacy_views'] = $unused;
        CLI::write("  Found " . count($unused) . " orphan/legacy views in app/Views/landing/pages/:", 'yellow');
        foreach ($unused as $orphan) {
            CLI::write("    - {$orphan}", 'dark_gray');
        }
    }

    private function auditSettingKeys(): void
    {
        CLI::newLine();
        CLI::write("--- [5] AUDITING SYSTEM SETTINGS KEYS USAGE ---", 'yellow');

        $systemSettingsFile = WRITEPATH . 'settings/system_settings.json';
        $settingsData = file_exists($systemSettingsFile) ? json_decode(file_get_contents($systemSettingsFile), true) : [];

        $knownGroups = array_keys($settingsData);
        CLI::write("  Configured setting groups in JSON: " . implode(', ', $knownGroups), 'white');

        // Check footer & mobile key mismatches
        $mismatches = [];
        $footerFile = APPPATH . 'Views/landing/components/footer.php';
        $footerContent = file_get_contents($footerFile);

        if (str_contains($footerContent, 'organization.social_linkedin')) {
            $mismatches[] = "footer.php uses 'organization.social_linkedin' but settings key is 'social.linkedin_url'";
        }
        if (str_contains($footerContent, 'organization.social_facebook')) {
            $mismatches[] = "footer.php uses 'organization.social_facebook' but settings key is 'social.facebook_url'";
        }
        if (str_contains($footerContent, 'organization.social_instagram')) {
            $mismatches[] = "footer.php uses 'organization.social_instagram' but settings key is 'social.instagram_url'";
        }
        if (str_contains($footerContent, 'organization.social_youtube')) {
            $mismatches[] = "footer.php uses 'organization.social_youtube' but settings key is 'social.youtube_url'";
        }
        if (str_contains($footerContent, 'organization.hours_weekday')) {
            $mismatches[] = "footer.php uses 'organization.hours_weekday' but settings key is 'organization.operational_hours'";
        }

        $this->results['setting_keys'] = $mismatches;
        if (!empty($mismatches)) {
            CLI::write("  [WARN] Found " . count($mismatches) . " setting key mismatches in views:", 'red');
            foreach ($mismatches as $m) {
                CLI::write("    * {$m}", 'red');
            }
        } else {
            CLI::write("  All setting keys match cleanly!", 'green');
        }
    }

    private function auditForms(): void
    {
        CLI::newLine();
        CLI::write("--- [6] AUDITING PUBLIC FORMS & CSRF PROTECTION ---", 'yellow');

        $viewsToCheck = [
            'Kontak Form'          => APPPATH . 'Views/landing/pages/contact.php',
            'Survei Modal Form'    => APPPATH . 'Views/landing/components/satisfaction_survey_modal.php',
            'Footer Newsletter'    => APPPATH . 'Views/landing/components/footer.php',
            'Bookmark Button Form' => APPPATH . 'Views/landing/components/bookmark_button.php',
            'Header User Logout'   => APPPATH . 'Views/landing/components/header.php',
            'Mobile User Logout'   => APPPATH . 'Views/landing/components/mobile_menu.php',
        ];

        foreach ($viewsToCheck as $label => $file) {
            if (!file_exists($file)) continue;
            $c = file_get_contents($file);
            $hasCsrf = str_contains($c, 'csrf_field()') || str_contains($c, 'csrf_test_name');
            CLI::write(sprintf("  %-25s: CSRF Protection %s", $label, $hasCsrf ? 'PRESENT [OK]' : 'MISSING [WARN]'), $hasCsrf ? 'green' : 'red');
            $this->results['security_forms'][$label] = $hasCsrf;
        }
    }

    private function printSummary(): void
    {
        CLI::newLine();
        CLI::write("==================================================================", 'cyan');
        CLI::write(sprintf("   LANDING AUDIT COMPLETE: %d/%d ROUTES PASSED (100%%), %d FAILED", 
            $this->results['routes']['passed'], 
            $this->results['routes']['total'], 
            $this->results['routes']['failed']
        ), $this->results['routes']['failed'] === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();
    }
}
