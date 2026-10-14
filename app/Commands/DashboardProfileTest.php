<?php

namespace App\Commands;

use App\Controllers\Admin\ProfileController;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class DashboardProfileTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'dashboard:profile:test';
    protected $description = 'Comprehensive test suite for Dashboard Profile feature (/dashboard/profile and /dashboard/profile/*)';
    protected $usage       = 'dashboard:profile:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPENGADAAN DASHBOARD PROFILE — COMPREHENSIVE TEST SUITE   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // 1. Setup mock session with active admin user
        $userModel = new UserModel();
        $admin = $userModel->where('username', 'admin')->where('account_status', 'active')->first();
        if (! $admin) {
            $admin = $userModel->where('account_status', 'active')->first();
        }

        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => (int) $admin['id'],
            'name'           => $admin['name'],
            'username'       => $admin['username'],
            'account_status' => $admin['account_status'],
            'roles'          => ['administrator'],
            'permissions'    => ['profile.view', 'profile.edit'],
        ]);

        // Setup mock request
        $siteUri = new SiteURI(config('App'), 'dashboard/profile');
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        \Config\Services::injectMock('request', $request);

        $controller = new ProfileController();
        $controller->initController($request, service('response'), service('logger'));

        // Test 1: GET /dashboard/profile (Index)
        $htmlIndex = '';
        $this->testModule("GET /dashboard/profile (Index Overview Rendering)", function () use ($controller, $admin, &$htmlIndex) {
            $htmlIndex = $controller->index();
            $this->assert(is_string($htmlIndex) && strlen($htmlIndex) > 1000, "Profile index produced valid HTML");
            $this->assert(str_contains($htmlIndex, 'Profil Saya'), "Title Profil Saya present");
            $this->assert(str_contains($htmlIndex, esc($admin['name'])), "Admin real name present");
            $this->assert(str_contains($htmlIndex, esc($admin['username'])), "Admin real username present");
            $this->assert(str_contains($htmlIndex, 'Ringkasan Profil'), "Navigation tab Ringkasan Profil present");
            $this->assert(str_contains($htmlIndex, 'dashboard/profile/edit'), "Edit profile link present");
            $this->assert(str_contains($htmlIndex, 'dashboard/profile/password'), "Password link present");
            $this->assert(str_contains($htmlIndex, 'dashboard/profile/preferences'), "Preferences link present");
        });

        // Test 2: GET /dashboard/profile/edit (Edit Form)
        $htmlEdit = '';
        $this->testModule("GET /dashboard/profile/edit (Form Rendering)", function () use ($controller, $admin, &$htmlEdit) {
            $htmlEdit = $controller->edit();
            $this->assert(is_string($htmlEdit) && strlen($htmlEdit) > 1000, "Profile edit produced valid HTML");
            $this->assert(str_contains($htmlEdit, 'Ubah Profil'), "Title Ubah Profil present");
            $this->assert(str_contains($htmlEdit, 'name="name"'), "Input name present");
            $this->assert(str_contains($htmlEdit, 'name="username"'), "Input username present");
            $this->assert(str_contains($htmlEdit, 'name="email"'), "Input email present");
            $this->assert(str_contains($htmlEdit, 'name="phone"'), "Input phone present");
            $this->assert(str_contains($htmlEdit, 'type="submit"'), "Real submit button present");
            $this->assert(str_contains($htmlEdit, 'method="post"'), "POST method present");
            $this->assert(str_contains($htmlEdit, 'csrf_test_name') || str_contains($htmlEdit, 'csrf'), "CSRF field present");
        });

        // Test 3: GET /dashboard/profile/password (Password Form)
        $htmlPassword = '';
        $this->testModule("GET /dashboard/profile/password (Password Form Rendering)", function () use ($controller, &$htmlPassword) {
            $htmlPassword = $controller->password();
            $this->assert(is_string($htmlPassword) && strlen($htmlPassword) > 1000, "Password view produced valid HTML");
            $this->assert(str_contains($htmlPassword, 'Ubah Kata Sandi'), "Title Ubah Kata Sandi present");
            $this->assert(str_contains($htmlPassword, 'name="current_password"'), "Input current_password present");
            $this->assert(str_contains($htmlPassword, 'name="password"'), "Input password present");
            $this->assert(str_contains($htmlPassword, 'name="password_confirmation"'), "Input password_confirmation present");
            $this->assert(! str_contains($htmlPassword, 'Form ini masih UI-only'), "Zero dummy 'UI-only' disclaimer");
            $this->assert(str_contains($htmlPassword, 'type="submit"'), "Real submit button present");
        });

        // Test 4: GET /dashboard/profile/preferences (Preferences Form)
        $htmlPrefs = '';
        $this->testModule("GET /dashboard/profile/preferences (Preferences Form Rendering)", function () use ($controller, &$htmlPrefs) {
            $htmlPrefs = $controller->preferences();
            $this->assert(is_string($htmlPrefs) && strlen($htmlPrefs) > 1000, "Preferences view produced valid HTML");
            $this->assert(str_contains($htmlPrefs, 'Preferensi Akun'), "Title Preferensi Akun present");
            $this->assert(str_contains($htmlPrefs, 'name="language"'), "Language select present");
            $this->assert(str_contains($htmlPrefs, 'name="theme"'), "Theme select present");
            $this->assert(str_contains($htmlPrefs, 'name="email_notification"'), "Email notification toggle present");
            $this->assert(str_contains($htmlPrefs, 'name="in_app_notification"'), "In-app notification toggle present");
            $this->assert(str_contains($htmlPrefs, 'name="report_notification"'), "Report notification toggle present");
            $this->assert(str_contains($htmlPrefs, 'type="submit"'), "Real submit button present");
        });

        // Test 5: POST /dashboard/profile/edit validation
        $this->testModule("POST /dashboard/profile/edit (Validation & Update Execution)", function () use ($controller, $admin, $userModel) {
            // Test updating profile name
            $testName = 'Administrator Sistem Pengadaan';
            $_POST = [
                'name'          => $testName,
                'username'      => $admin['username'],
                'email'         => $admin['email'],
                'phone'         => '085117779783',
                'unit_name'     => 'Biro Pengadaan Barang dan Jasa',
                'position_name' => 'Koordinator Pengadaan',
                'address_line'  => 'Kompleks Pemprov Lampung',
            ];

            $siteUri = new SiteURI(config('App'), 'dashboard/profile/edit');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request = $request->withMethod('POST');
            $request->setGlobal('post', $_POST);
            \Config\Services::injectMock('request', $request);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->update();
            $errors = session()->getFlashdata('errors') ?: session()->getFlashdata('error');
            $errMsg = $errors ? (is_array($errors) ? json_encode($errors) : $errors) : 'No error flash';

            // Verify in DB
            $updated = $userModel->find($admin['id']);
            $this->assert($updated['name'] === $testName, "User name successfully updated in database. Details: " . $errMsg);

            // Restore original name
            $userModel->update($admin['id'], ['name' => $admin['name']]);
        });

        // Test 6: POST /dashboard/profile/password validation
        $this->testModule("POST /dashboard/profile/password (Mismatch Current Password Check)", function () use ($controller) {
            $_POST = [
                'current_password'      => 'wrongpassword123',
                'password'              => 'NewSecurePass2026!',
                'password_confirmation' => 'NewSecurePass2026!',
            ];

            $siteUri = new SiteURI(config('App'), 'dashboard/profile/password');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request = $request->withMethod('POST');
            $request->setGlobal('post', $_POST);
            \Config\Services::injectMock('request', $request);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->updatePassword();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Password update returned redirect response");
            $this->assert(session()->getFlashdata('error') !== null, "Invalid current password correctly rejected");
        });

        // Test 7: POST /dashboard/profile/preferences
        $this->testModule("POST /dashboard/profile/preferences (Persistence Verification)", function () use ($controller) {
            $_POST = [
                'language'            => 'id',
                'theme'               => 'dark',
                'email_notification'  => '1',
                'in_app_notification' => '1',
                'report_notification' => '0',
            ];

            $siteUri = new SiteURI(config('App'), 'dashboard/profile/preferences');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request = $request->withMethod('POST');
            $request->setGlobal('post', $_POST);
            \Config\Services::injectMock('request', $request);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->updatePreferences();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Preferences returned redirect response");
            $this->assert(session()->getFlashdata('success') !== null, "Preferences success message set");

            $sessPrefs = session()->get('user_preferences');
            $this->assert($sessPrefs['theme'] === 'dark', "Session theme set to dark");
        });

        // Test 8: POST /dashboard/profile/password (Valid Update & Hash Change)
        $this->testModule("POST /dashboard/profile/password (Valid Update & Hash Change)", function () use ($controller, $admin, $userModel) {
            $origHash = $admin['password_hash'];
            $tempCurrentPass = 'OldSecurePass2026!';
            $newPass = 'BrandNewPass2026!';
            $userModel->skipValidation(true)->update($admin['id'], [
                'password_hash' => password_hash($tempCurrentPass, PASSWORD_DEFAULT),
            ]);

            $_POST = [
                'current_password'      => $tempCurrentPass,
                'password'              => $newPass,
                'password_confirmation' => $newPass,
            ];

            $siteUri = new SiteURI(config('App'), 'dashboard/profile/password');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request = $request->withMethod('POST');
            $request->setGlobal('post', $_POST);
            \Config\Services::injectMock('request', $request);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->updatePassword();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Password update redirected");
            $this->assert(session()->getFlashdata('success') !== null, "Success flash message set");

            // Verify hash changed and verifies new password
            $freshAdmin = $userModel->find($admin['id']);
            $this->assert(password_verify($newPass, $freshAdmin['password_hash']), "New password verified with password_verify");

            // Restore original hash
            $userModel->skipValidation(true)->update($admin['id'], ['password_hash' => $origHash]);
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
            CLI::write("  [FAIL] {$name} - " . $e->getMessage(), "red");
            $this->failed++;
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }
}
