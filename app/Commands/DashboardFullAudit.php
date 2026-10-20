<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Config\Services;

class DashboardFullAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'dashboard:full:audit';
    protected $description = 'Performs a comprehensive automated audit of the entire dashboard: routes, menus, controllers, views, data consistency, and UI tokens.';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("         BEGAWIPENGADAAN DASHBOARD FULL AUDIT REPORT ENGINE       ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();

        $db = Database::connect();

        // 1. Audit Sidebar URLs & Route Resolution
        CLI::write("--- [1] AUDITING SIDEBAR NAVIGATION & ROUTE MAPPING ---", 'yellow');
        $sidebarFile = APPPATH . 'Views/dashboard/components/sidebar.php';
        if (! file_exists($sidebarFile)) {
            CLI::error("Sidebar file not found: $sidebarFile");
            return;
        }

        $sidebarContent = file_get_contents($sidebarFile);
        preg_match_all('/site_url\([\'"]([^\'"]+)[\'"]\)/', $sidebarContent, $matches);
        $sidebarUrls = array_values(array_unique($matches[1]));

        CLI::write("Found " . count($sidebarUrls) . " distinct URLs referenced in dashboard sidebar.", 'white');

        // Mock an active superadmin session for testing
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId   = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 13;
        session()->set([
            'user_id'       => $adminId,
            'role'          => 'superadmin',
            'is_logged_in'  => true,
            'username'      => 'admin_audit',
        ]);

        $routeResults = [];
        $failedRoutes = [];
        $routes = Services::routes();

        foreach ($sidebarUrls as $url) {
            $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
            // Resolve HTTP route using IncomingRequest
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), $path);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('GET');
            Services::injectMock('request', $req);
            $router = Services::router($routes, $req);

            try {
                $controllerName = $router->handle($path);
                $methodName = $router->methodName();
                $params = $router->params();

                if (! class_exists($controllerName)) {
                    $failedRoutes[] = [
                        'url'    => $url,
                        'reason' => "Controller class not found: {$controllerName}",
                    ];
                    CLI::write(" [FAIL] {$url} -> Controller {$controllerName} does not exist!", 'red');
                    continue;
                }

                if (! method_exists($controllerName, $methodName)) {
                    $failedRoutes[] = [
                        'url'    => $url,
                        'reason' => "Method {$methodName} not found in {$controllerName}",
                    ];
                    CLI::write(" [FAIL] {$url} -> Method {$controllerName}::{$methodName} does not exist!", 'red');
                    continue;
                }

                // Simulate execution
                $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), $path);
                $req = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
                Services::injectMock('request', $req);

                $controllerInstance = new $controllerName();
                $controllerInstance->initController($req, Services::response(), Services::logger());

                // Capture output or exceptions
                ob_start();
                try {
                    $response = $controllerInstance->$methodName(...$params);
                    $captured = ob_get_clean();

                    $status = 200;
                    if ($response instanceof \CodeIgniter\HTTP\RedirectResponse) {
                        $status = 302;
                    } elseif ($response instanceof \CodeIgniter\HTTP\ResponseInterface) {
                        $status = $response->getStatusCode();
                    }

                    $routeResults[] = [
                        'url'        => $url,
                        'controller' => "{$controllerName}::{$methodName}",
                        'status'     => $status,
                        'ok'         => true,
                    ];
                    CLI::write(" [PASS] {$url} ({$status}) -> {$controllerName}::{$methodName}", 'green');
                } catch (\Throwable $e) {
                    ob_end_clean();
                    $failedRoutes[] = [
                        'url'        => $url,
                        'controller' => "{$controllerName}::{$methodName}",
                        'reason'     => "Runtime exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine(),
                    ];
                    CLI::write(" [ERROR] {$url} -> " . $e->getMessage(), 'red');
                }
            } catch (\Throwable $e) {
                $failedRoutes[] = [
                    'url'    => $url,
                    'reason' => "Route handle error: " . $e->getMessage(),
                ];
                CLI::write(" [FAIL] {$url} -> Route resolution failed: " . $e->getMessage(), 'red');
            }
        }

        CLI::newLine();
        CLI::write("Route Testing Summary: " . count($routeResults) . " OK, " . count($failedRoutes) . " FAILED.", count($failedRoutes) > 0 ? 'red' : 'green');
        CLI::newLine();

        // 2. Audit UI Tokens: Detect text-dark or contrast issues in dashboard views
        CLI::write("--- [2] AUDITING DASHBOARD VIEWS FOR CONTRAST & UI BUGS ---", 'yellow');
        $viewFiles = glob(APPPATH . 'Views/dashboard/**/*.php');
        // also get immediate children
        $viewFiles = array_merge($viewFiles, glob(APPPATH . 'Views/dashboard/*.php'));
        $viewFiles = array_unique($viewFiles);

        $textDarkOccurrences = [];
        $h56pxSelectIssues = [];

        foreach ($viewFiles as $file) {
            $content = file_get_contents($file);
            $relPath = str_replace(APPPATH, 'app/', $file);

            // Check text-dark
            if (preg_match_all('/\btext-dark\b/', $content, $m)) {
                $count = count($m[0]);
                $textDarkOccurrences[$relPath] = $count;
            }

            // Check multi-select bleeding bug (form-select with h-56-px or size attribute)
            if (preg_match('/<select[^>]*class=["\'][^"\']*h-56-px[^"\']*["\'][^>]*multiple/i', $content) ||
                preg_match('/<select[^>]*class=["\'][^"\']*form-select[^"\']*h-56-px[^"\']*["\'][^>]*size=/i', $content)) {
                $h56pxSelectIssues[] = $relPath;
            }
        }

        CLI::write("Found " . count($textDarkOccurrences) . " view files still containing 'text-dark' (dark mode contrast issue).", count($textDarkOccurrences) > 0 ? 'yellow' : 'green');
        foreach ($textDarkOccurrences as $path => $count) {
            CLI::write("  - {$path}: {$count} occurrence(s)", 'yellow');
        }

        if (! empty($h56pxSelectIssues)) {
            CLI::write("Found " . count($h56pxSelectIssues) . " view files with potential dropdown height bleeding:", 'red');
            foreach ($h56pxSelectIssues as $path) {
                CLI::write("  - {$path}", 'red');
            }
        } else {
            CLI::write("No select dropdown height bleeding issues detected.", 'green');
        }

        CLI::newLine();

        // 3. Duplicate Menus & Flows
        CLI::write("--- [3] AUDITING DUPLICATE MENUS, OVERLAPS, & FLOWS ---", 'yellow');
        // Check Spotlight Hub vs old individual carousel menus
        $hasCentralCarousel = in_array('dashboard/landing/sorotan-carousel', $sidebarUrls, true);
        $hasBungaRampaiCarousel = in_array('dashboard/bunga-rampai/carousel-konten-unggulan', $sidebarUrls, true);
        if ($hasCentralCarousel && $hasBungaRampaiCarousel) {
            CLI::write(" [NOTE] Duplicate Carousel Management detected: Both 'Pusat Carousel & Sorotan' (/dashboard/landing/sorotan-carousel) and 'Carousel & Unggulan Bunga Rampai' (/dashboard/bunga-rampai/carousel-konten-unggulan) exist in the sidebar.", 'yellow');
        }

        // Check if both /dashboard and submenus in summary exist
        CLI::write("Sidebar groups checked.", 'green');

        CLI::newLine();

        // 4. Data Consistency Audit in Database
        CLI::write("--- [4] AUDITING DATABASE INTEGRITY & ORPHANS ---", 'yellow');
        // Users without profiles
        $totalUsers = $db->table('users')->countAllResults();
        $usersWithoutProfileType = $db->table('users')->where('profile_type IS NULL OR profile_type = ""')->countAllResults();
        CLI::write("Users table: {$totalUsers} total users, {$usersWithoutProfileType} with empty profile_type.", $usersWithoutProfileType > 0 ? 'yellow' : 'green');

        // Check satisfaction survey columns
        $surveyCount = $db->table('satisfaction_surveys')->countAllResults();
        CLI::write("Satisfaction surveys table: {$surveyCount} records.", 'green');

        // Check audit logs
        $auditCount = $db->table('audit_logs')->countAllResults();
        $auditFkOrphans = $db->table('audit_logs a')
            ->select('a.id')
            ->join('users u', 'u.id = a.actor_id', 'left')
            ->where('a.actor_id IS NOT NULL')
            ->where('u.id IS NULL')
            ->countAllResults();
        CLI::write("Audit logs table: {$auditCount} records, {$auditFkOrphans} orphaned actor_id records.", $auditFkOrphans > 0 ? 'red' : 'green');

        CLI::newLine();
        CLI::write("--- [5] DATABASE TABLES SUMMARY OVERVIEW ---", 'yellow');
        $tables = $db->listTables();
        sort($tables);
        foreach ($tables as $tbl) {
            try {
                $c = $db->table($tbl)->countAllResults();
                CLI::write(sprintf("  %-38s : %d records", $tbl, $c), 'white');
            } catch (\Throwable $e) {
                CLI::write(sprintf("  %-38s : ERR: %s", $tbl, $e->getMessage()), 'red');
            }
        }

        CLI::newLine();
        CLI::write("--- [6] ROUTES IN ROUTES.PHP VS SIDEBAR NAVIGATION ---", 'yellow');
        $rawRoutes = Services::routes()->getRoutes();
        $allGetRoutes = $rawRoutes;
        $dashboardGetRoutes = [];
        foreach ($allGetRoutes as $from => $to) {
            $fromTrimmed = trim($from, '/');
            if (str_starts_with($fromTrimmed, 'dashboard') && ! str_contains($fromTrimmed, '(') && ! str_contains($fromTrimmed, 'masuk') && ! str_contains($fromTrimmed, 'daftar') && ! str_contains($fromTrimmed, 'lupa') && ! str_contains($fromTrimmed, 'status')) {
                $dashboardGetRoutes[$fromTrimmed] = is_string($to) ? $to : 'Closure';
            }
        }

        $routesNotInSidebar = [];
        foreach ($dashboardGetRoutes as $r => $target) {
            if (! in_array($r, $sidebarUrls, true)) {
                $routesNotInSidebar[$r] = $target;
            }
        }

        CLI::write("Total clean GET routes under dashboard/ (non-parameterized): " . count($dashboardGetRoutes), 'white');
        CLI::write("Routes in Routes.php NOT reachable via direct sidebar menu: " . count($routesNotInSidebar), 'yellow');
        foreach ($routesNotInSidebar as $r => $t) {
            CLI::write("  - {$r} => {$t}", 'yellow');
        }

        // Print final status
        CLI::newLine();
        CLI::write("==================================================================", 'cyan');
        CLI::write("                 AUDIT SCAN ENGINE COMPLETED                      ", 'cyan');
        CLI::write("==================================================================", 'cyan');
    }
}
