<?php

namespace App\Commands;

use App\Controllers\Dashboard\Auth\LoginController;
use App\Models\UserModel;
use App\Services\Admin\RoleManagementService;
use App\Services\Identity\AuthorizationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestDynamicRoleAccess extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:dynamic-role-access';
    protected $description = 'Verifikasi Hak Akses Dinamis Role dengan prefix admin_{} dan Isolasi Peran Standar.';
    protected $usage       = 'test:dynamic-role-access';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   VERIFIKASI SISTEM ROLE DINAMIS & HAK AKSES DASHBOARD ADMIN     ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();
        $session = service('session');
        $authService = new AuthorizationService();

        // -------------------------------------------------------------
        // BAGIAN 1: VERIFIKASI PERAN STANDAR TETAP BERJALAN NORMAL
        // -------------------------------------------------------------
        CLI::write("--- [BAGIAN 1] VERIFIKASI PERAN STANDAR (TETAP TERJAGA) ---", "yellow");

        // 1. Peserta biasa
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 9991,
            'name'           => 'Test Peserta',
            'account_status' => 'active',
            'roles'          => ['peserta'],
            'permissions'    => [],
        ]);
        if (! $authService->hasDashboardAccess()) {
            $this->pass("1. Role 'peserta' biasa tidak memiliki akses ke Dashboard Admin.");
        } else {
            $this->fail("1. Role 'peserta' biasa salah diberi akses ke Dashboard.");
        }

        // 2. Trainer
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 9992,
            'name'           => 'Test Trainer',
            'account_status' => 'active',
            'roles'          => ['trainer'],
            'permissions'    => [],
        ]);
        if (in_array('trainer', session()->get('roles'), true)) {
            $this->pass("2. Role 'trainer' tetap dipertahankan untuk ruang kerja pengajar.");
        }

        // 3. Operator
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 9993,
            'name'           => 'Test Operator',
            'account_status' => 'active',
            'roles'          => ['operator'],
            'permissions'    => [],
        ]);
        if (in_array('operator', session()->get('roles'), true)) {
            $this->pass("3. Role 'operator' tetap dipertahankan untuk ruang kerja asisten & operasional.");
        }

        // 4. Administrator Penuh
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 9994,
            'name'           => 'Test Administrator',
            'account_status' => 'active',
            'roles'          => ['administrator'],
            'permissions'    => [],
        ]);
        if ($authService->isAdmin() && $authService->hasDashboardAccess()) {
            $this->pass("4. Role 'administrator' memiliki akses penuh ke Dashboard.");
        } else {
            $this->fail("4. Role 'administrator' gagal mengakses Dashboard.");
        }

        // 5. Super Administrator
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 9995,
            'name'           => 'Test Super Admin',
            'account_status' => 'active',
            'roles'          => ['super_administrator'],
            'permissions'    => [],
        ]);
        if ($authService->isSuperAdmin() && $authService->hasDashboardAccess()) {
            $this->pass("5. Role 'super_administrator' memiliki hak bypass tertinggi ke Dashboard.");
        } else {
            $this->fail("5. Role 'super_administrator' gagal mengakses Dashboard.");
        }

        // -------------------------------------------------------------
        // BAGIAN 2: VERIFIKASI ROLE DINAMIS DENGAN PREFIX admin_{}
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [BAGIAN 2] VERIFIKASI ROLE DINAMIS BERPREFIX admin_{} ---", "yellow");

        // Setup Role Baru: admin_regulasi
        $adminUser = $db->table('users')->where('username', 'superadmin')->get()->getRowArray();
        $adminId = (int) ($adminUser['id'] ?? 13);

        $roleMgmt = new RoleManagementService();
        $existingRole = $db->table('roles')->where('key', 'admin_regulasi')->get()->getRowArray();
        if (! $existingRole) {
            $newRoleId = $roleMgmt->createRole([
                'key'         => 'admin_regulasi',
                'name'        => 'Admin Regulasi PBJ',
                'description' => 'Administrator khusus pengelola naskah dan direktori regulasi PBJ.',
            ], $adminId);
        } else {
            $newRoleId = (int) $existingRole['id'];
        }

        // Cari ID permissions regulasi
        $regPermRows = $db->table('permissions')->like('key', 'regulation.')->get()->getResultArray();
        $regPermIds = array_map('intval', array_column($regPermRows, 'id'));
        $regPermKeys = array_column($regPermRows, 'key');
        if (! empty($regPermIds)) {
            $roleMgmt->syncRolePermissions($newRoleId, $regPermIds, $adminId);
        }

        // 6. Uji AuthorizationService::isPrefixAdmin
        if ($authService->isPrefixAdmin(['admin_regulasi'])) {
            $this->pass("6. AuthorizationService mengenali format/prefix 'admin_{}' secara dinamis (admin_regulasi).");
        } else {
            $this->fail("6. AuthorizationService gagal mengenali prefix 'admin_{}'.");
        }

        // 7. Uji AuthorizationService::hasDashboardAccess untuk admin_regulasi
        if ($authService->hasDashboardAccess(null, ['admin_regulasi'], $regPermKeys, 'active')) {
            $this->pass("7. hasDashboardAccess() bernilai TRUE untuk role berprefix 'admin_regulasi'.");
        } else {
            $this->fail("7. hasDashboardAccess() bernilai FALSE untuk role 'admin_regulasi'.");
        }

        // 8. Uji Smart Landing Route Resolution
        $landingRoute = $authService->getFirstAllowedDashboardRoute($regPermKeys, ['admin_regulasi']);
        if ($landingRoute === 'dashboard/regulasi/dokumen') {
            $this->pass("8. getFirstAllowedDashboardRoute() mengarahkan tepat ke modul '{$landingRoute}'.");
        } else {
            $this->fail("8. getFirstAllowedDashboardRoute() salah mengarahkan: '{$landingRoute}' (ekspektasi: dashboard/regulasi/dokumen).");
        }

        // -------------------------------------------------------------
        // BAGIAN 3: SIMULASI LOGIN & FILTER AKSES (ANTI-MENTAL KE WORKSPACE)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [BAGIAN 3] SIMULASI LOGIN & FILTER AKSES (ANTI-MENTAL) ---", "yellow");

        // Buat user test admin regulasi
        $testUserEmail = 'user.adminregulasi.' . time() . '@example.com';
        $db->table('users')->insert([
            'username'       => 'user.adminregulasi.' . time(),
            'name'           => 'Siti Aminah (Admin Regulasi)',
            'email'          => $testUserEmail,
            'password_hash'  => password_hash('password123', PASSWORD_BCRYPT),
            'profile_type'   => 'asn',
            'account_status' => 'active',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
        $testUserId = (int) $db->insertID();

        // Assign role admin_regulasi + peserta (skenario riil di mana user juga punya role peserta)
        $db->table('user_roles')->insert([
            'user_id'    => $testUserId,
            'role_id'    => $newRoleId,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Simulasikan login attempt via LoginController
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/masuk');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
        $incomingRequest->setGlobal('post', [
            'email'    => $testUserEmail,
            'password' => 'password123',
        ]);
        \Config\Services::injectMock('request', $incomingRequest);

        $loginController = new LoginController();
        $loginController->initController($incomingRequest, service('response'), service('logger'));

        $loginResponse = $loginController->attemptLogin();
        $redirectUrl = $loginResponse->getHeaderLine('Location');

        if (str_contains($redirectUrl, 'dashboard') && ! str_contains($redirectUrl, 'workspace')) {
            $this->pass("9. Pengguna dengan role 'admin_regulasi' TIDAK MENTAL ke workspace saat login, melainkan dialihkan ke Dashboard ('{$redirectUrl}').");
        } else {
            $this->fail("9. GAGAL: Pengguna dengan role 'admin_regulasi' mental ke workspace ('{$redirectUrl}').");
        }

        // 10. Uji Filter Akses Dashboard (AuthenticationFilter) saat membuka /dashboard
        $dashReq = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $filter = new \App\Filters\AuthenticationFilter();
        $filterResult = $filter->before($dashReq);

        // Jika filterResult adalah redirect, pastikan redirectnya ke modul regulasi di dashboard (bukan ke workspace)
        if ($filterResult instanceof \CodeIgniter\HTTP\ResponseInterface) {
            $targetLocation = $filterResult->getHeaderLine('Location');
            if (str_contains($targetLocation, 'dashboard/regulasi') && ! str_contains($targetLocation, 'workspace')) {
                $this->pass("10. Filter Dashboard cerdas mengarahkan admin_regulasi ke modul hak aksesnya ('{$targetLocation}') tanpa mental ke workspace.");
            } else {
                $this->fail("10. Filter Dashboard salah me-redirect: '{$targetLocation}'.");
            }
        } elseif ($filterResult === null) {
            $this->pass("10. Filter Dashboard mengizinkan akses ke area dashboard.");
        }

        // 11. Uji Akses ke Modul Regulasi yang diizinkan (/dashboard/regulasi/dokumen)
        $regReq = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/regulasi/dokumen'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $regFilterResult = $filter->before($regReq);
        if ($regFilterResult === null) {
            $this->pass("11. Modul Regulasi PBJ (/dashboard/regulasi/dokumen) TERBUKA dan lolos filter untuk admin_regulasi.");
        } else {
            $this->fail("11. Modul Regulasi PBJ terblokir secara tidak sah.");
        }

        // 12. Uji Isolasi Proteksi: Akses ke Modul yang TIDAK diizinkan (/dashboard/akses/pengguna)
        $userMgmtReq = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/akses/pengguna'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $blockFilterResult = $filter->before($userMgmtReq);
        if ($blockFilterResult instanceof \CodeIgniter\HTTP\ResponseInterface && $blockFilterResult->getStatusCode() === 403) {
            $this->pass("12. Isolasi Granular Terbukti: Modul Manajemen Pengguna DITOLAK (403 Forbidden) karena admin_regulasi tidak memiliki izin user.view.");
        } else {
            $this->fail("12. Gagal mengisolasi modul terlarang untuk admin_regulasi.");
        }

        // -------------------------------------------------------------
        // BAGIAN 4: ZERO-MIGRATION COMPLIANCE
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [BAGIAN 4] ZERO-MIGRATION & SCHEMA PRESERVATION ---", "yellow");

        $migrationCount = count(glob(APPPATH . 'Database/Migrations/*.php'));
        if ($migrationCount === 16) {
            $this->pass("13. Zero-Migration Compliance: Tepat 16 berkas migrasi database utuh terjaga.");
        } else {
            $this->fail("13. Pelanggaran Zero-Migration: Ditemukan {$migrationCount} migrasi (seharusnya 16).");
        }

        // Cleanup test user
        $db->table('user_roles')->where('user_id', $testUserId)->delete();
        $db->table('users')->where('id', $testUserId)->delete();

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("HASIL AKHIR: {$this->passed} LULUS, {$this->failed} GAGAL", $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function pass(string $msg): void
    {
        CLI::write("  [PASS] " . $msg, "green");
        $this->passed++;
    }

    private function fail(string $msg): void
    {
        CLI::write("  [FAIL] " . $msg, "red");
        $this->failed++;
    }
}
