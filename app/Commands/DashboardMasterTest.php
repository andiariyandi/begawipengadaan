<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\DashboardService;
use App\Controllers\Admin\DashboardController;
use Config\Services;

class DashboardMasterTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:dashboard-master';
    protected $description = 'Verify Unified Single Master Dashboard, Route Cleanup, and 7-Pillar Analytics';

    public function run(array $params)
    {
        CLI::write("=== TESTING UNIFIED SINGLE MASTER DASHBOARD ===", 'yellow');
        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $msg) use (&$passed, &$failed) {
            if ($condition) {
                CLI::write("[PASS] {$msg}", 'green');
                $passed++;
            } else {
                CLI::write("[FAIL] {$msg}", 'red');
                $failed++;
            }
        };

        // 1. Verify Migration Count stays strictly at 16
        $db = db_connect();
        $migrationCount = $db->table('migrations')->countAllResults();
        $assert($migrationCount === 16, "Database migrations count must be exactly 16 (Actual: {$migrationCount})");

        // 2. Verify Routes: only dashboard.home exists, sub-routes removed
        $routes = Services::routes();
        $routesList = $routes->getRoutes('get');
        
        $hasDashboardHome = isset($routesList['dashboard']) || !empty(route_to('dashboard.home'));
        $assert($hasDashboardHome, "Route dashboard (dashboard.home) is registered");

        $assert(!isset($routesList['dashboard/kpi-internal']), "Route dashboard/kpi-internal is removed");
        $assert(!isset($routesList['dashboard/antrean-persetujuan']), "Route dashboard/antrean-persetujuan is removed");
        $assert(!isset($routesList['dashboard/aktivitas-terbaru']), "Route dashboard/aktivitas-terbaru is removed");
        $assert(!isset($routesList['dashboard/indikator-sistem']), "Route dashboard/indikator-sistem is removed");

        // 3. Verify AuthenticationFilter does not have zone 19
        $filterContent = file_get_contents(APPPATH . 'Filters/AuthenticationFilter.php');
        $assert(strpos($filterContent, 'indikator-sistem') === false, "AuthenticationFilter zone 19 for indikator-sistem removed");

        // 4. Verify Sidebar has single dashboard link without dropdown
        $sidebarContent = file_get_contents(APPPATH . 'Views/dashboard/components/sidebar.php');
        $assert(strpos($sidebarContent, 'Ringkasan') === false, "Sidebar submenu Ringkasan removed");
        $assert(strpos($sidebarContent, 'indikator-sistem') === false, "Sidebar submenu Indikator Sistem removed");
        $assert(strpos($sidebarContent, "href=\"<?= site_url('dashboard') ?>\"") !== false, "Sidebar has direct link to site_url('dashboard')");

        // 5. Verify Spotlight Modal cleaned
        $spotlightContent = file_get_contents(APPPATH . 'Views/dashboard/components/spotlight_modal.php');
        $assert(strpos($spotlightContent, 'dashboard/kpi-internal') === false, "Spotlight modal does not link to kpi-internal");
        $assert(strpos($spotlightContent, 'dashboard/indikator-sistem') === false, "Spotlight modal does not link to indikator-sistem");

        // 5b. Verify Header Mobile Responsiveness
        $headerContent = file_get_contents(APPPATH . 'Views/dashboard/components/header.php');
        $assert(strpos($headerContent, 'navbar-search d-none d-lg-flex') !== false, "Navbar search bar hidden on mobile (< lg) with d-none d-lg-flex");
        $assert(strpos($headerContent, 'd-lg-none') !== false && strpos($headerContent, 'data-bs-target="#adminSpotlightModal"') !== false, "Header has compact mobile search trigger icon button (< lg)");
        $assert(strpos($headerContent, 'dropdown-menu-end to-top dropdown-menu-lg') !== false, "Notification dropdown menu has dropdown-menu-end for mobile viewport containment");
        $assert(strpos($headerContent, 'dropdown-menu-end to-top dropdown-menu-sm') !== false, "User profile dropdown menu has dropdown-menu-end");
        $assert(strpos($headerContent, '@media (max-width: 575.98px)') !== false, "Header includes dedicated mobile responsive media query styling");

        // 6. Verify DashboardService data integrity
        $dashboardService = new DashboardService();
        $summary = $dashboardService->getFullDashboardSummary();

        $assert(isset($summary['kpis']), "FullDashboardSummary has 'kpis'");
        $assert(isset($summary['learning']), "FullDashboardSummary has 'learning'");
        $assert(isset($summary['knowledge']), "FullDashboardSummary has 'knowledge'");
        $assert(isset($summary['regulations']), "FullDashboardSummary has 'regulations'");
        $assert(isset($summary['communication']), "FullDashboardSummary has 'communication'");
        $assert(isset($summary['certificate']), "FullDashboardSummary has 'certificate'");
        $assert(isset($summary['visitors']), "FullDashboardSummary has 'visitors'");

        // Check 12-month dynamic series in learning
        $monthly = $summary['learning']['enrollmentMonthly'] ?? [];
        $assert(isset($monthly['categories']) && count($monthly['categories']) === 12, "Monthly categories has 12 items");
        $assert(isset($monthly['registration']) && count($monthly['registration']) === 12, "Monthly registration series has 12 items");
        $assert(isset($monthly['enrollment']) && count($monthly['enrollment']) === 12, "Monthly enrollment series has 12 items");
        $assert(isset($monthly['eventRegistration']) && count($monthly['eventRegistration']) === 12, "Monthly eventRegistration series has 12 items");

        // Check visitors stats
        $visitors = $summary['visitors'];
        $assert(isset($visitors['home']['today']), "Visitors home has 'today'");
        $assert(isset($visitors['home']['this_week']), "Visitors home has 'this_week'");
        $assert(isset($visitors['home']['this_month']), "Visitors home has 'this_month'");
        $assert(isset($visitors['home']['total']), "Visitors home has 'total'");
        $assert(isset($visitors['features']) && is_array($visitors['features']), "Visitors has 'features' array");

        // Check Bunga Rampai
        $knowledge = $summary['knowledge'];
        $assert(isset($knowledge['totalMedia']), "Knowledge has 'totalMedia'");
        $assert(isset($knowledge['latestTemplates']), "Knowledge has 'latestTemplates'");
        $assert(isset($knowledge['latestArticles']), "Knowledge has 'latestArticles'");

        // Check Regulasi PBJ
        $regulations = $summary['regulations'];
        $assert(isset($regulations['activeRegulations']), "Regulations has 'activeRegulations'");
        $assert(isset($regulations['revokedRegulations']), "Regulations has 'revokedRegulations'");
        $assert(isset($regulations['latestRegulations']), "Regulations has 'latestRegulations'");

        // Check Komunikasi
        $comm = $summary['communication'];
        $assert(isset($comm['totalBroadcasts']), "Communication has 'totalBroadcasts'");
        $assert(isset($comm['upcomingEvents']), "Communication has 'upcomingEvents'");
        $assert(isset($comm['latestNews']), "Communication has 'latestNews'");

        // Check Sertifikat
        $cert = $summary['certificate'];
        $assert(isset($cert['issuedCertificates']), "Certificate has 'issuedCertificates'");
        $assert(isset($cert['latestCertificates']), "Certificate has 'latestCertificates'");

        // 7. Verify DashboardController::index renders view without errors
        $controller = new DashboardController($dashboardService);
        
        // Mock session if needed
        session()->set('user_id', 14);
        session()->set('user_role', 'superadmin');

        try {
            $html = $controller->index();
            $assert(is_string($html) && strlen($html) > 500, "DashboardController::index renders HTML response (" . strlen($html) . " bytes)");
            $assert(strpos($html, 'Dashboard Utama') !== false, "HTML contains 'Dashboard Utama'");
            $assert(strpos($html, 'Statistik Pengunjung Landing Page') !== false, "HTML contains 'Statistik Pengunjung Landing Page'");
            $assert(strpos($html, 'begawiEnrollmentChart') !== false, "HTML contains 'begawiEnrollmentChart'");
            $assert(strpos($html, 'begawiAccountStatusChart') !== false, "HTML contains 'begawiAccountStatusChart'");
            $assert(strpos($html, 'Arena Belajar') !== false, "HTML contains 'Arena Belajar'");
            $assert(strpos($html, 'Daftar Event &amp; Agenda Mendatang') !== false || strpos($html, 'Daftar Event & Agenda Mendatang') !== false, "HTML contains 'Daftar Event & Agenda Mendatang'");
            $assert(strpos($html, 'Bunga Rampai') !== false, "HTML contains 'Bunga Rampai'");
            $assert(strpos($html, 'Regulasi PBJ') !== false, "HTML contains 'Regulasi PBJ'");
            $assert(strpos($html, 'Sertifikat PBJ') !== false, "HTML contains 'Sertifikat PBJ'");
        } catch (\Throwable $e) {
            $assert(false, "DashboardController::index threw exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
        }

        CLI::write("\nSUMMARY: Passed: {$passed}, Failed: {$failed}", $failed === 0 ? 'green' : 'red');
        return $failed === 0 ? 0 : 1;
    }
}
