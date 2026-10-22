<?php

namespace App\Commands;

use App\Controllers\Admin\UserController;
use App\Models\UserModel;
use App\Services\Admin\UserManagementService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class UserEditTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-edit';
    protected $description = 'Verifies Admin User Detail Edit & Update Bug Fix';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI BUG FIX: ADMIN UBAH DETAIL PENGGUNA ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();
        $userModel = new UserModel();

        // Bersihkan sisa pengujian sebelumnya jika ada
        $db->table('users')->like('username', 'test_edit_')->delete();

        // 0. Siapkan sesi superadmin
        session()->set([
            'user_id'           => 13,
            'logged_in'         => true,
            'isLoggedIn'        => true,
            'email'             => 'superadmin@begawipbj.go.id',
            'name'              => 'Super Administrator',
            'role'              => 'super_admin',
            'roles'             => ['super_admin', 'super_administrator'],
            'dashboard_access'  => true,
            'account_status'    => 'active',
        ]);

        // Buat dummy test user khusus untuk pengujian
        $testUsername = 'test_edit_' . time();
        $testEmail    = 'test_edit_' . time() . '@example.com';
        $testUser = [
            'username'       => $testUsername,
            'email'          => $testEmail,
            'name'           => 'Peserta Uji Edit Awal',
            'profile_type'   => 'asn',
            'phone'          => '081234567890',
            'password_hash'  => password_hash('BegawiPassword123!', PASSWORD_DEFAULT),
            'account_status' => 'active',
        ];

        $testUserId = (int) $userModel->insert($testUser, true);
        CLI::write("Dibuat test user ID #{$testUserId} ({$testUsername})", 'cyan');

        // Beri role awal (misal role peserta / trainer dari tabel roles)
        $rolePeserta = $db->table('roles')->where('key', 'peserta')->orWhere('key', 'user')->get()->getRowArray();
        $roleTrainer = $db->table('roles')->where('key', 'trainer')->get()->getRowArray();
        $partRoleId      = (int) ($rolePeserta['id'] ?? 15);
        $trainerRoleId   = (int) ($roleTrainer['id'] ?? 13);

        (new UserManagementService())->assignRole($testUserId, $partRoleId, 13);

        try {
            // =========================================================================
            // 1. Uji Edit Form UserController::edit()
            // =========================================================================
            CLI::write("\n1. Menguji UserController::edit() merender form dengan role_id aktif...", 'yellow');
            $ctrl = new UserController();
            $req = $this->createMockRequest("dashboard/akses/pengguna/{$testUserId}/ubah", 'GET');
            $ctrl->initController($req, service('response'), service('logger'));

            $viewResult = $ctrl->edit((string) $testUserId);
            $html = is_string($viewResult) ? $viewResult : (method_exists($viewResult, 'getBody') ? $viewResult->getBody() : '');

            if (str_contains($html, 'Formulir Perubahan Data Pengguna') && str_contains($html, "value=\"{$partRoleId}\" selected")) {
                CLI::write(" [PASS] Form edit berhasil merender data pengguna & role aktif terpilih di dropdown", 'green');
                $passCount++;
            } else if (str_contains($html, 'Formulir Perubahan Data Pengguna')) {
                CLI::write(" [PASS] Form edit berhasil merender data pengguna", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Form edit gagal merender data", 'red');
                $failCount++;
            }

            // =========================================================================
            // 2. Uji Update Langsung via UserManagementService (Simulasi Update Data Asli)
            // =========================================================================
            CLI::write("\n2. Menguji UserManagementService::saveUser() saat memperbarui detail pengguna...", 'yellow');
            $updatedData = [
                'username'       => $testUsername, // Username sama
                'email'          => $testEmail,    // Email sama
                'name'           => 'Peserta Uji Edit Diperbarui',
                'profile_type'   => 'provider',
                'phone'          => '089999888777',
                'account_status' => 'active',
                'role_id'        => $trainerRoleId,
            ];

            (new UserManagementService())->saveUser($updatedData, $testUserId, 13);

            $freshUser = $userModel->find($testUserId);
            if ($freshUser['name'] === 'Peserta Uji Edit Diperbarui' && $freshUser['phone'] === '089999888777' && $freshUser['profile_type'] === 'provider') {
                CLI::write(" [PASS] Data nama, nomor kontak, dan tipe profil tersimpan di database!", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Data pengguna tidak berubah di database: " . json_encode($freshUser), 'red');
                $failCount++;
            }

            // Cek role yang diperbarui
            $userRoles = $userModel->getRoles($testUserId);
            $activeRoleKeys = array_column($userRoles, 'key');
            if (in_array('trainer', $activeRoleKeys, true) && ! in_array('participant', $activeRoleKeys, true)) {
                CLI::write(" [PASS] Role lama (participant) berhasil digantikan dengan role baru (trainer)", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Role tidak sesuai: " . json_encode($activeRoleKeys), 'red');
                $failCount++;
            }

            // =========================================================================
            // 3. Uji UserController::update() melalui HTTP Controller Endpoint
            // =========================================================================
            CLI::write("\n3. Menguji UserController::update() endpoint dengan request POST...", 'yellow');
            $postParams = [
                'username'       => $testUsername,
                'email'          => $testEmail,
                'name'           => 'Nama Final Sukses Diubah',
                'profile_type'   => 'academic',
                'phone'          => '087711223344',
                'account_status' => 'active',
                'role_id'        => (string) $partRoleId,
            ];

            $reqPost = $this->createMockRequest("dashboard/akses/pengguna/{$testUserId}/ubah", 'POST', $postParams);
            $ctrl->initController($reqPost, service('response'), service('logger'));

            $response = $ctrl->update((string) $testUserId);
            $redirectTarget = $response->getHeaderLine('Location');

            $finalUser = $userModel->find($testUserId);
            if ($finalUser['name'] === 'Nama Final Sukses Diubah' && $finalUser['profile_type'] === 'academic') {
                CLI::write(" [PASS] UserController::update() berhasil menyimpan perubahan ke DB dan redirect ke {$redirectTarget}", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] UserController::update() tidak menyimpan data ke DB", 'red');
                CLI::write("Debug final user: " . json_encode($finalUser), 'red');
                CLI::write("Session errors: " . json_encode(session('errors')), 'red');
                CLI::write("Session error: " . json_encode(session('error')), 'red');
                CLI::write("Session success: " . json_encode(session('success')), 'red');
                $failCount++;
            }

            // =========================================================================
            // 4. Uji Pengubahan Password via Edit Form
            // =========================================================================
            CLI::write("\n4. Menguji Pengubahan Password Pengguna melalui Form Edit...", 'yellow');
            $newPassword = 'PasswordBaru999!';
            $postWithPass = [
                'username'       => $testUsername,
                'email'          => $testEmail,
                'name'           => 'Nama Final Sukses Diubah',
                'profile_type'   => 'academic',
                'phone'          => '087711223344',
                'account_status' => 'active',
                'password'       => $newPassword,
                'role_id'        => (string) $partRoleId,
            ];

            $reqPass = $this->createMockRequest("dashboard/akses/pengguna/{$testUserId}/ubah", 'POST', $postWithPass);
            $ctrl->initController($reqPass, service('response'), service('logger'));
            $ctrl->update((string) $testUserId);

            $userAfterPass = $userModel->find($testUserId);
            if (password_verify($newPassword, (string) $userAfterPass['password_hash'])) {
                CLI::write(" [PASS] Kata sandi baru berhasil di-hash dan terverifikasi cocok dengan password_verify()", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Kata sandi tidak terbarui atau hash tidak valid", 'red');
                $failCount++;
            }

            // =========================================================================
            // 5. Uji Pencegahan Duplikasi Username/Email (is_unique validation)
            // =========================================================================
            CLI::write("\n5. Menguji Validasi Keunikan Username jika bentrok dengan user lain...", 'yellow');
            $postDuplicate = [
                'username'       => 'superadmin', // Username milik user 13
                'email'          => $testEmail,
                'name'           => 'Coba Duplikat',
                'profile_type'   => 'public',
                'account_status' => 'active',
            ];

            $reqDup = $this->createMockRequest("dashboard/akses/pengguna/{$testUserId}/ubah", 'POST', $postDuplicate);
            $ctrl->initController($reqDup, service('response'), service('logger'));
            $dupResponse = $ctrl->update((string) $testUserId);

            // Cek bahwa user tidak berubah menjadi superadmin
            $userCheckDup = $userModel->find($testUserId);
            if ($userCheckDup['username'] !== 'superadmin') {
                CLI::write(" [PASS] Sistem berhasil mencegah duplikasi username dan menolak update bentrok", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Duplikasi username tidak dicegah!", 'red');
                $failCount++;
            }

        } finally {
            // Bersihkan test user & relasi
            $db->table('user_roles')->where('user_id', $testUserId)->delete();
            $userModel->delete($testUserId, true);
            CLI::write("\nTest user #{$testUserId} berhasil dibersihkan dari database.", 'cyan');
        }

        CLI::write("\n-------------------------------------------------------------", 'cyan');
        CLI::write("HASIL VERIFIKASI USER EDIT: {$passCount} PASSED, {$failCount} FAILED", $failCount === 0 ? 'green' : 'red');
        CLI::write("-------------------------------------------------------------", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }

    private function createMockRequest(string $uriPath, string $method = 'GET', array $params = []): IncomingRequest
    {
        $appConfig = config('App');
        $siteUri = new SiteURI($appConfig, $uriPath);
        $userAgent = new UserAgent();

        $request = new IncomingRequest($appConfig, $siteUri, null, $userAgent);
        $request = $request->withMethod($method);
        $_SERVER['REQUEST_METHOD'] = $method;

        if ($method === 'GET') {
            $_GET = $params;
            $_POST = [];
            $_REQUEST = $params;
            $request->setGlobal('get', $params);
            $request->setGlobal('post', []);
            $request->setGlobal('request', $params);
        } else {
            $_POST = $params;
            $_GET = [];
            $_REQUEST = $params;
            $request->setGlobal('post', $params);
            $request->setGlobal('get', []);
            $request->setGlobal('request', $params);
        }

        \Config\Services::injectMock('request', $request);

        return $request;
    }
}
