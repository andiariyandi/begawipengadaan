<?php

namespace App\Commands;

use App\Filters\AuthenticationFilter;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class RbacCustomRoleTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:rbac-custom-roles';
    protected $description = 'Comprehensive E2E test verifying flexible modular RBAC with custom roles and permission gates';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("      BEGAWIPENGADAAN RBAC MODULAR CUSTOM ROLES TEST SUITE        ", "yellow");
        CLI::write("==================================================================", "yellow");

        $db = \Config\Database::connect();
        // Setup dedicated fixture test user & custom role in database to prevent SuperAdmin session overwrite
        $fixtureUser = $db->table('users')->where('username', 'rbac_custom_fixture')->get()->getRowArray();
        if (! $fixtureUser) {
            $db->table('users')->insert([
                'username'       => 'rbac_custom_fixture',
                'email'          => 'rbac_custom_fixture@test.local',
                'password_hash'  => password_hash('Secret123!', PASSWORD_BCRYPT),
                'name'           => 'RBAC Custom Fixture User',
                'phone'          => '081234567890',
                'profile_type'   => 'asn',
                'account_status' => 'active',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $testUserId = (int) $db->insertID();
        } else {
            $testUserId = (int) $fixtureUser['id'];
        }

        $roleRow = $db->table('roles')->where('key', 'rbac_test_role')->get()->getRowArray();
        if (! $roleRow) {
            $db->table('roles')->insert([
                'key'         => 'rbac_test_role',
                'name'        => 'RBAC Test Custom Role',
                'description' => 'Role for testing RBAC modular access',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $testRoleId = (int) $db->insertID();
        } else {
            $testRoleId = (int) $roleRow['id'];
        }

        $userRole = $db->table('user_roles')->where(['user_id' => $testUserId, 'role_id' => $testRoleId])->get()->getRowArray();
        if (! $userRole) {
            $db->table('user_roles')->insert([
                'user_id'    => $testUserId,
                'role_id'    => $testRoleId,
                'status'     => 'active',
                'granted_by' => 13,
                'granted_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('user_roles')->where('id', $userRole['id'])->update(['status' => 'active']);
        }

        $syncDbPerms = function (array $permKeys) use ($db, $testRoleId): void {
            $db->table('role_permissions')->where('role_id', $testRoleId)->delete();
            if (! empty($permKeys)) {
                $permRows = $db->table('permissions')->whereIn('key', $permKeys)->get()->getResultArray();
                $batch = [];
                $now = date('Y-m-d H:i:s');
                foreach ($permRows as $p) {
                    $batch[] = [
                        'role_id'       => $testRoleId,
                        'permission_id' => (int) $p['id'],
                        'created_at'    => $now,
                    ];
                }
                if (! empty($batch)) {
                    $db->table('role_permissions')->insertBatch($batch);
                }
            }
        };

        $createdRoleIds = [];
        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $message) use (&$passed, &$failed): void {
            if ($condition) {
                CLI::write("  [PASS] " . $message, "green");
                $passed++;
            } else {
                CLI::write("  [FAIL] " . $message, "red");
                $failed++;
            }
        };

        // Helper to execute simulated request through AuthenticationFilter & Controller
        $simulate = function (string $method, string $path, array $post = []) use ($testUserId): array {
            $routes = Services::routes();
            $config = config('App');

            $siteUri = new \CodeIgniter\HTTP\SiteURI($config, $path);
            $req = (new \CodeIgniter\HTTP\IncomingRequest($config, $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))
                ->withMethod($method);

            if (strtoupper($method) === 'POST') {
                $req->setGlobal('post', $post);
            }

            Services::injectMock('request', $req);

            // Step 1: Run through AuthenticationFilter
            $filter = new AuthenticationFilter();
            $filterResponse = $filter->before($req);

            if ($filterResponse !== null) {
                $statusCode = 200;
                if ($filterResponse instanceof \CodeIgniter\HTTP\RedirectResponse) {
                    $statusCode = 302;
                } elseif ($filterResponse instanceof \CodeIgniter\HTTP\ResponseInterface) {
                    $statusCode = $filterResponse->getStatusCode();
                }

                return [
                    'status' => $statusCode,
                    'stage'  => 'filter',
                    'body'   => (string) $filterResponse->getBody(),
                ];
            }

            // Step 2: Route & Execute Controller
            $router = Services::router($routes, $req);
            try {
                $controllerName = $router->handle($path);
                $methodName     = $router->methodName();
                $params         = $router->params();

                if (! class_exists($controllerName)) {
                    return ['status' => 500, 'stage' => 'router', 'error' => "Controller not found: {$controllerName}"];
                }

                $controller = new $controllerName();
                $controller->initController($req, Services::response(), Services::logger());

                ob_start();
                try {
                    $response = $controller->$methodName(...$params);
                    $output   = ob_get_clean();

                    $statusCode = 200;
                    if ($response instanceof \CodeIgniter\HTTP\RedirectResponse) {
                        $statusCode = 302;
                    } elseif ($response instanceof \CodeIgniter\HTTP\ResponseInterface) {
                        $statusCode = $response->getStatusCode();
                    }

                    return [
                        'status' => $statusCode,
                        'stage'  => 'controller',
                        'body'   => is_string($response) ? $response : (string) ($output ?: ''),
                    ];
                } catch (\Throwable $e) {
                    ob_end_clean();
                    return ['status' => 500, 'stage' => 'controller_exception', 'error' => $e->getMessage()];
                }
            } catch (\Throwable $e) {
                return ['status' => 500, 'stage' => 'router_exception', 'error' => $e->getMessage()];
            }
        };

        try {
            // -------------------------------------------------------------
            // SCENARIO 1: Custom Role "Khusus Bunga Rampai"
            // -------------------------------------------------------------
            CLI::write("\n--- [SCENARIO 1] Pengujian Role Kustom: 'Khusus Bunga Rampai' ---", "cyan");
            $bungaPerms = ['dashboard.view', 'bunga.view', 'bunga.article.manage'];
            $syncDbPerms($bungaPerms);

            session()->set([
                'user_id'          => $testUserId,
                'role'             => 'rbac_test_role',
                'roles'            => ['rbac_test_role'],
                'permissions'      => $bungaPerms,
                'isLoggedIn'       => true,
                'account_status'   => 'active',
                'dashboard_access' => true,
            ]);

            $r1 = $simulate('GET', 'dashboard/bunga-rampai/artikel');
            $assert($r1['status'] === 200, "Akses GET /dashboard/bunga-rampai/artikel sukses (HTTP 200 OK)");

            $r2 = $simulate('GET', 'dashboard/regulasi/dokumen');
            $assert($r2['status'] === 403, "Akses GET /dashboard/regulasi/dokumen ditolak (HTTP 403 Forbidden)");

            $r3 = $simulate('GET', 'dashboard/pembelajaran/kelas');
            $assert($r3['status'] === 403, "Akses GET /dashboard/pembelajaran/kelas ditolak (HTTP 403 Forbidden)");

            $r4 = $simulate('GET', 'dashboard/sistem/pengaturan');
            $assert($r4['status'] === 403, "Akses GET /dashboard/sistem/pengaturan ditolak (HTTP 403 Forbidden)");

            // Sidebar visibility test for Scenario 1
            $sidebarHtml = view('dashboard/components/sidebar');
            $hasBungaMenu = str_contains($sidebarHtml, '<span>Bunga Rampai</span>');
            $hasDashboardMenu = str_contains($sidebarHtml, '<span>Dashboard</span>');
            $hasRegulasiMenu = str_contains($sidebarHtml, '<span>Regulasi PBJ</span>');
            $hasLearningMenu = str_contains($sidebarHtml, '<span>Arena Belajar</span>');
            $hasSettingsMenu = str_contains($sidebarHtml, 'Pusat Pengaturan');

            $assert($hasBungaMenu && $hasDashboardMenu, "Sidebar menampilkan menu Dashboard & Bunga Rampai");
            $assert(! $hasRegulasiMenu && ! $hasLearningMenu && ! $hasSettingsMenu, "Sidebar menyembunyikan modul Regulasi, Arena Belajar, & Pengaturan");

            // -------------------------------------------------------------
            // SCENARIO 2: Custom Role "Khusus Regulasi PBJ"
            // -------------------------------------------------------------
            CLI::write("\n--- [SCENARIO 2] Pengujian Role Kustom: 'Khusus Regulasi PBJ' ---", "cyan");
            $regPerms = ['dashboard.view', 'regulation.view', 'regulation.manage'];
            $syncDbPerms($regPerms);

            session()->set([
                'user_id'          => $testUserId,
                'role'             => 'rbac_test_role',
                'roles'            => ['rbac_test_role'],
                'permissions'      => $regPerms,
                'isLoggedIn'       => true,
                'account_status'   => 'active',
                'dashboard_access' => true,
            ]);

            $r5 = $simulate('GET', 'dashboard/regulasi/dokumen');
            $assert($r5['status'] === 200, "Akses GET /dashboard/regulasi/dokumen sukses (HTTP 200 OK)");

            $r6 = $simulate('GET', 'dashboard/bunga-rampai/artikel');
            $assert($r6['status'] === 403, "Akses GET /dashboard/bunga-rampai/artikel ditolak (HTTP 403 Forbidden)");

            // -------------------------------------------------------------
            // SCENARIO 3: Custom Role "Khusus Arena Belajar"
            // -------------------------------------------------------------
            CLI::write("\n--- [SCENARIO 3] Pengujian Role Kustom: 'Khusus Arena Belajar' ---", "cyan");
            $learningPerms = ['dashboard.view', 'learning.view', 'learning.class.view'];
            $syncDbPerms($learningPerms);

            session()->set([
                'user_id'          => $testUserId,
                'role'             => 'rbac_test_role',
                'roles'            => ['rbac_test_role'],
                'permissions'      => $learningPerms,
                'isLoggedIn'       => true,
                'account_status'   => 'active',
                'dashboard_access' => true,
            ]);

            $r7 = $simulate('GET', 'dashboard/pembelajaran/kelas');
            $assert($r7['status'] === 200, "Akses GET /dashboard/pembelajaran/kelas sukses (HTTP 200 OK)");

            $r8 = $simulate('GET', 'dashboard/komunikasi/berita');
            $assert($r8['status'] === 403, "Akses GET /dashboard/komunikasi/berita ditolak (HTTP 403 Forbidden)");

            // -------------------------------------------------------------
            // SCENARIO 4: Custom Role "Auditor Read-Only"
            // -------------------------------------------------------------
            CLI::write("\n--- [SCENARIO 4] Pengujian Role Kustom: 'Auditor Read-Only' ---", "cyan");
            $auditorPerms = ['dashboard.view', 'audit.view', 'reports.view'];
            $syncDbPerms($auditorPerms);

            session()->set([
                'user_id'          => $testUserId,
                'role'             => 'rbac_test_role',
                'roles'            => ['rbac_test_role'],
                'permissions'      => $auditorPerms,
                'isLoggedIn'       => true,
                'account_status'   => 'active',
                'dashboard_access' => true,
            ]);

            $r9 = $simulate('GET', 'dashboard/sistem/audit-trail');
            $assert($r9['status'] === 200, "Akses GET /dashboard/sistem/audit-trail sukses (HTTP 200 OK)");

            $r10 = $simulate('GET', 'dashboard/laporan/pembelajaran');
            $assert($r10['status'] === 200, "Akses GET /dashboard/laporan/pembelajaran sukses (HTTP 200 OK)");

            // Attempt write/mutating POST action
            $r11 = $simulate('POST', 'dashboard/landing/slider/simpan', ['title' => 'Test']);
            $assert($r11['status'] === 403, "Aksi POST mutasi tanpa izin kelola ditolak (HTTP 403 Forbidden)");

            // Attempt mutating GET action (e.g. create form)
            $r12 = $simulate('GET', 'dashboard/pembelajaran/kelas/tambah');
            $assert($r12['status'] === 403, "Aksi GET rute mutasi /tambah tanpa izin kelola ditolak (HTTP 403 Forbidden)");

        } finally {
            // Cleanup test fixture from DB
            if (isset($testRoleId)) {
                $db->table('role_permissions')->where('role_id', $testRoleId)->delete();
                $db->table('user_roles')->where('role_id', $testRoleId)->delete();
                $db->table('roles')->where('id', $testRoleId)->delete();
            }
            if (isset($testUserId)) {
                $db->table('users')->where('id', $testUserId)->delete();
            }
            // Restore Super Administrator session for regression audit
            $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
            $adminId = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 13;
            session()->set([
                'user_id'          => $adminId,
                'role'             => 'super_administrator',
                'roles'            => ['super_administrator'],
                'permissions'      => [],
                'isLoggedIn'       => true,
                'account_status'   => 'active',
                'dashboard_access' => true,
            ]);
        }

        CLI::write("\n==================================================================", "yellow");
        CLI::write("                 RBAC TEST SUMMARY RESULTS                        ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write(" Total Assertions : " . ($passed + $failed), "white");
        CLI::write(" Passed           : {$passed}", "green");
        CLI::write(" Failed           : {$failed}", $failed > 0 ? "red" : "green");
        CLI::write("==================================================================", "yellow");

        return $failed > 0 ? 1 : 0;
    }
}
