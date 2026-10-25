<?php

namespace App\Commands;

use App\Controllers\Dashboard\Auth\AccountStatusController;
use App\Controllers\Dashboard\Auth\LoginController;
use App\Controllers\Dashboard\Auth\PasswordController;
use App\Controllers\Dashboard\Auth\RegisterController;
use App\Models\PasswordResetModel;
use App\Models\ProfileVerificationModel;
use App\Models\UserModel;
use App\Services\Admin\ProfileVerificationService;
use App\Services\Email\EmailService;
use App\Services\Identity\PasswordResetService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Method;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class AuthSystemTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'auth:system:test';
    protected $description = 'Comprehensive test suite for BegawiPengadaan Auth System (/dashboard/masuk, /dashboard/daftar, /dashboard/lupa-kata-sandi)';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("     BEGAWIPENGADAAN AUTH SYSTEM — COMPREHENSIVE TEST SUITE       ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $userModel = new UserModel();
        $admin = $userModel->where('username', 'admin')->first();

        if (! $admin) {
            CLI::error("Admin user not found in database. Cannot run auth tests.");
            return 1;
        }

        // -------------------------------------------------------------
        // MODULE 1: /dashboard/masuk (LOGIN) & ANTI-SPAM
        // -------------------------------------------------------------

        // Test 1: GET /dashboard/masuk
        $this->testModule("GET /dashboard/masuk (Form Rendering, Brand Elements & Captcha)", function () {
            $this->clearAuthSession();
            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $output = $controller->index();
            $this->assert(is_string($output) && strlen($output) > 1000, "Login page returned HTML");
            $this->assert(str_contains($output, 'BegawiPengadaan'), "Contains BegawiPengadaan branding");
            $this->assert(str_contains($output, 'name="email"'), "Email input field present");
            $this->assert(str_contains($output, 'name="password"'), "Password input field present");
            $this->assert(str_contains($output, 'csrf_test_name') || str_contains($output, 'csrf'), "CSRF token present");
            $this->assert(str_contains($output, 'dashboard/lupa-kata-sandi'), "Link to forgot password present");
            $this->assert(str_contains($output, 'dashboard/daftar'), "Link to register present");
            $this->assert(str_contains($output, 'toggle-password'), "Password eye toggle present");
            $this->assert(str_contains($output, 'website_url'), "Honeypot field present");
            $this->assert(str_contains($output, 'name="captcha_answer"'), "Math captcha field present");
        });

        // Test 2: POST /dashboard/masuk (Validation Rejection)
        $this->testModule("POST /dashboard/masuk (Validation Rejection for Empty Inputs)", function () {
            $this->clearAuthSession();
            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'POST', [
                'email'    => '',
                'password' => '',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attemptLogin();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on validation fail");
            $errors = session()->getFlashdata('errors');
            $this->assert(! empty($errors), "Validation errors flashed to session");
            $this->assert(isset($errors['email']) || in_array('Alamat email wajib diisi.', $errors, true), "Email required error present");
        });

        // Test 3: POST /dashboard/masuk (Anti-Spam Honeypot Rejection)
        $this->testModule("POST /dashboard/masuk (Anti-Spam Honeypot Bot Rejection)", function () {
            $this->clearAuthSession();
            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'POST', [
                'email'       => 'spammer@bot.com',
                'password'    => 'BotSecret123!',
                'website_url' => 'https://spam-bot.xyz',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attemptLogin();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Bot redirected");
            $error = session()->getFlashdata('error') ?? session()->get('error');
            $this->assert($error !== null && (str_contains($error, 'tidak valid') || str_contains($error, 'mencurigakan')), "Bot suspicious error triggered: '{$error}'");
        });

        // Test 4: POST /dashboard/masuk (Anti-Spam Math Captcha Incorrect Rejection)
        $this->testModule("POST /dashboard/masuk (Anti-Spam Captcha Mismatch Rejection)", function () {
            $this->clearAuthSession();
            session()->set('login_captcha_answer', 15);

            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'POST', [
                'email'          => 'user@lampungprov.go.id',
                'password'       => 'AnyPassword123!',
                'captcha_answer' => '99', // Incorrect answer
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attemptLogin();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on wrong captcha");
            $error = session()->getFlashdata('error') ?? session()->get('error');
            $this->assert($error !== null && (str_contains($error, 'matematika') || str_contains($error, 'keamanan')), "Captcha error message caught: '{$error}'");
        });

        // Test 5: POST /dashboard/masuk (Wrong Credentials Rejection)
        $this->testModule("POST /dashboard/masuk (Wrong Credentials Rejection)", function () {
            $this->clearAuthSession();
            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'POST', [
                'email'    => 'nonexistent_user@lampungprov.go.id',
                'password' => 'WrongPass123!@#',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attemptLogin();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on invalid login");
            $errorFlash = session()->getFlashdata('error') ?? session()->get('error');
            $this->assert($errorFlash !== null, "Error message flashed");
            $this->assert(! session()->get('isLoggedIn'), "User is NOT logged in");
        });

        // Test 6: POST /dashboard/masuk (Successful Authentication)
        $this->testModule("POST /dashboard/masuk (Successful Admin Authentication)", function () use ($admin, $userModel) {
            $this->clearAuthSession();
            $tempPass = 'SuperSecurePass2026!';
            $origHash = $admin['password_hash'];
            $userModel->skipValidation(true)->update($admin['id'], [
                'password_hash'  => password_hash($tempPass, PASSWORD_DEFAULT),
                'account_status' => 'active',
            ]);

            $controller = new LoginController();
            $request = $this->createMockRequest('dashboard/masuk', 'POST', [
                'email'    => $admin['email'],
                'password' => $tempPass,
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attemptLogin();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected on successful login");
            $this->assert(session()->get('isLoggedIn') === true, "Session isLoggedIn set to true");
            $this->assert(session()->get('user_id') === (int) $admin['id'], "Session user_id matches admin");

            // Verify last_login_at timestamp in database
            $freshAdmin = $userModel->find($admin['id']);
            $this->assert(! empty($freshAdmin['last_login_at']), "last_login_at timestamp updated in database");

            // Restore original hash
            $userModel->skipValidation(true)->update($admin['id'], ['password_hash' => $origHash]);
        });

        // -------------------------------------------------------------
        // MODULE 2: /dashboard/lupa-kata-sandi & RESET
        // -------------------------------------------------------------

        // Test 7: GET /dashboard/lupa-kata-sandi
        $this->testModule("GET /dashboard/lupa-kata-sandi (Form Rendering, Honeypot & Captcha)", function () {
            $this->clearAuthSession();
            $controller = new PasswordController();
            $request = $this->createMockRequest('dashboard/lupa-kata-sandi', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $output = $controller->forgotForm();
            $this->assert(is_string($output) && strlen($output) > 800, "Forgot password returned HTML");
            $this->assert(str_contains($output, 'Lupa Kata Sandi'), "Contains Lupa Kata Sandi title");
            $this->assert(str_contains($output, 'name="email"'), "Email input field present");
            $this->assert(str_contains($output, 'name="website_url"'), "Honeypot field present");
            $this->assert(str_contains($output, 'name="captcha_answer"'), "Math captcha field present");
            $this->assert(str_contains($output, 'csrf_test_name') || str_contains($output, 'csrf'), "CSRF token present");
            $this->assert(str_contains($output, 'dashboard/masuk'), "Link to login present");
        });

        // Test 8: POST /dashboard/lupa-kata-sandi (Anti-Spam Honeypot Bot Rejection)
        $this->testModule("POST /dashboard/lupa-kata-sandi (Anti-Spam Honeypot Bot Rejection)", function () {
            $this->clearAuthSession();
            $controller = new PasswordController();
            $request = $this->createMockRequest('dashboard/lupa-kata-sandi', 'POST', [
                'email'       => 'spammer@bot.com',
                'website_url' => 'https://spam-bot.xyz',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->sendReset();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Bot redirected");
            $msg = session()->getFlashdata('message') ?? session()->get('message');
            $this->assert($msg !== null, "Honeypot returned anti-disclosure message without issuing token");
        });

        // Test 9: POST /dashboard/lupa-kata-sandi (Token Generation & Invalidation of Previous Tokens)
        $this->testModule("POST /dashboard/lupa-kata-sandi (Token Generation & Invalidate Previous Tokens)", function () use ($admin) {
            $this->clearAuthSession();
            $resetModel = new PasswordResetModel();

            // First request via service
            $resetService = new PasswordResetService();
            $resetService->requestReset($admin['email']);

            $firstToken = $resetModel->where('user_id', $admin['id'])->orderBy('id', 'DESC')->first();
            $this->assert($firstToken !== null, "First reset token record created");
            $firstId = (int) $firstToken['id'];

            // Second request via service
            $resetService->requestReset($admin['email']);

            $secondToken = (new PasswordResetModel())->where('user_id', $admin['id'])->orderBy('id', 'DESC')->first();
            $secondId = (int) ($secondToken['id'] ?? 0);
            $this->assert($secondId > $firstId, "New reset token created. First: {$firstId}, Second: {$secondId}");

            // Verify first token was invalidated (used_at is not null)
            $oldTokenCheck = (new PasswordResetModel())->find($firstId);
            $this->assert(! empty($oldTokenCheck['used_at']), "Old reset token was automatically invalidated (used_at marked)");
        });

        // Test 10: GET & POST /dashboard/reset-kata-sandi (End-to-End Reset Flow)
        $this->testModule("GET & POST /dashboard/reset-kata-sandi (Valid Token & Reset Execution)", function () use ($admin, $userModel) {
            $this->clearAuthSession();
            $origHash = $admin['password_hash'];
            $plainToken = bin2hex(random_bytes(32));

            // Seed valid token
            $resetModel = new PasswordResetModel();
            $resetModel->insert([
                'user_id'    => $admin['id'],
                'token_hash' => hash('sha256', $plainToken),
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $controller = new PasswordController();

            // Part A: GET form with valid token
            $request = $this->createMockRequest('dashboard/reset-kata-sandi/' . $plainToken, 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->resetForm($plainToken);
            $this->assert(is_string($html) && strlen($html) > 800, "Reset form HTML rendered");
            $this->assert(str_contains($html, 'Atur Ulang Kata Sandi'), "Title rendered");
            $this->assert(str_contains($html, 'name="password"'), "Password field present");
            $this->assert(str_contains($html, 'name="password_confirmation"'), "Confirmation field present");
            $this->assert(str_contains($html, 'csrf_test_name') || str_contains($html, 'csrf'), "CSRF token present");

            // Part B: POST update password
            $newPassword = 'BrandNewPass2026!#$';
            $request = $this->createMockRequest('dashboard/reset-kata-sandi/' . $plainToken, 'POST', [
                'password'              => $newPassword,
                'password_confirmation' => $newPassword,
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->updatePassword($plainToken);
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected after password reset");

            // Verify hash changed and verifies with password_verify
            $freshAdmin = $userModel->find($admin['id']);
            $this->assert(password_verify($newPassword, $freshAdmin['password_hash']), "Database hash successfully updated to new password");

            // Clean up: restore original hash
            $userModel->skipValidation(true)->update($admin['id'], ['password_hash' => $origHash]);
        });

        // Test 11: Broken Link Fix & Route Aliases
        $this->testModule("Broken Link Fix & Route Aliases (/dashboard/reset-kata-sandi and /lupa-sandi/reset)", function () {
            $routes = service('routes');
            $routesList = $routes->getRoutes(Method::GET);
            $keys = array_keys($routesList);

            $matchedReset = array_filter($keys, static fn($k) => str_contains($k, 'reset-kata-sandi'));
            $this->assert(! empty($matchedReset), "Route containing reset-kata-sandi registered: " . implode(', ', $matchedReset));

            $matchedLegacy = array_filter($keys, static fn($k) => str_contains($k, 'lupa-sandi/reset'));
            $this->assert(! empty($matchedLegacy), "Legacy catch route lupa-sandi/reset registered: " . implode(', ', $matchedLegacy));

            $this->assert(isset($routesList['masuk']), "Friendly route alias /masuk registered");
            $this->assert(isset($routesList['daftar']), "Friendly route alias /daftar registered");
            $this->assert(isset($routesList['lupa-password']), "Friendly route alias /lupa-password registered");
        });

        // -------------------------------------------------------------
        // MODULE 3: /dashboard/daftar (WIZARD REGISTRATION)
        // -------------------------------------------------------------

        // Test 12: GET /dashboard/daftar (Step 1 Rendering)
        $this->testModule("GET /dashboard/daftar (Step 1 Form & Multi-step Components)", function () {
            $this->clearAuthSession();
            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $output = $controller->index();
            $this->assert(is_string($output) && strlen($output) > 1000, "Register page produced valid HTML");
            $this->assert(str_contains($output, 'Buat Akun BegawiPengadaan'), "Page heading present");
            $this->assert(str_contains($output, 'name="full_name"'), "Full name field present");
            $this->assert(str_contains($output, 'name="username"'), "Username field present");
            $this->assert(str_contains($output, 'name="email"'), "Email field present");
            $this->assert(str_contains($output, 'name="phone"'), "Phone field present");
            $this->assert(str_contains($output, 'name="password"'), "Password field present");
            $this->assert(str_contains($output, 'name="profile_type"'), "Profile type select options present");
            $this->assert(str_contains($output, 'asn') && str_contains($output, 'provider'), "All 4 profile types selectable");
        });

        // Test 13: POST /dashboard/daftar (Password Confirmation Mismatch Check)
        $this->testModule("POST /dashboard/daftar (Password Confirmation Mismatch Check)", function () {
            $this->clearAuthSession();
            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar', 'POST', [
                'full_name'             => 'Test User Registration',
                'username'              => 'test_mismatch_user',
                'email'                 => 'mismatch@lampungprov.go.id',
                'phone'                 => '081234567890',
                'password'              => 'ValidPassword123!@#',
                'password_confirmation' => 'DifferentPassword123!@#',
                'profile_type'          => 'asn',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on mismatch");
            $errors = session()->getFlashdata('errors');
            $this->assert(! empty($errors['password_confirmation']), "Password confirmation mismatch error recorded");
        });

        // Test 14: POST /dashboard/daftar (Valid Step 1 Submission & Encrypted Session Storage)
        $this->testModule("POST /dashboard/daftar (Valid Step 1 Submission & Encrypted Session Storage)", function () {
            $this->clearAuthSession();
            $controller = new RegisterController();
            $plainPass = 'SuperValidPassword123!@#';
            $request = $this->createMockRequest('dashboard/daftar', 'POST', [
                'full_name'             => 'Aparatur Sipil Negara Test',
                'username'              => 'asn_testing_' . time(),
                'email'                 => 'asntesting_' . time() . '@lampungprov.go.id',
                'phone'                 => '081234567890',
                'password'              => $plainPass,
                'password_confirmation' => $plainPass,
                'profile_type'          => 'asn',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected to Step 2");
            $loc = $response->getHeaderLine('Location');
            $this->assert(str_contains($loc, 'wilayah'), "Redirect location is wilayah: '{$loc}'");

            $sessData = session('registration_data');
            $this->assert(! empty($sessData), "registration_data stored in session");
            $this->assert(empty($sessData['password']), "Raw plain password NOT stored in session");
            $this->assert(! empty($sessData['password_enc']), "Password safely encrypted in session (password_enc)");
        });

        // Test 15: GET /dashboard/daftar/wilayah (Step 2 Rendering)
        $this->testModule("GET /dashboard/daftar/wilayah (Step 2 Rendering with Asterisk on Alamat Ringkas)", function () {
            session()->set('registration_data', [
                'full_name'    => 'Pendaftar Uji',
                'username'     => 'pendaftar_uji',
                'email'        => 'pendaftar@lampungprov.go.id',
                'phone'        => '081234567890',
                'profile_type' => 'asn',
            ]);

            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar/wilayah', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->registerAddress();
            $this->assert(is_string($html) && strlen($html) > 1000, "Address step produced valid HTML");
            $this->assert(str_contains($html, 'Wilayah dan Alamat Ringkas'), "Step heading present");
            $this->assert(str_contains($html, 'name="province"'), "Province select present");
            $this->assert(str_contains($html, 'name="regency"'), "Regency select present");
            $this->assert(str_contains($html, 'name="district"'), "District select present");
            $this->assert(str_contains($html, 'Alamat Ringkas <span class="text-danger">*</span>'), "Alamat Ringkas clearly shows mandatory asterisk (*)");
        });

        // Test 16: Step 3 Format Validations (NIK, NIB, NIP)
        $this->testModule("POST /dashboard/daftar/profil (Format Validations for NIK, NIB, NIP)", function () {
            $db = \Config\Database::connect();
            $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
            $opdId = $opd ? (int) $opd['id'] : 1;

            session()->set('registration_data', [
                'full_name'    => 'Pendaftar Format Test',
                'username'     => 'format_test',
                'email'        => 'format@lampungprov.go.id',
                'phone'        => '081234567890',
                'profile_type' => 'asn',
            ]);
            session()->set('registration_address', [
                'province' => '18',
                'regency'  => '18.71',
                'district' => '18.71.01',
                'village'  => null,
                'address'  => 'Jl. Wolter Monginsidi No. 69',
            ]);

            $controller = new RegisterController();

            // Test Invalid NIP (too short: 10 digits)
            $request = $this->createMockRequest('dashboard/daftar/profil/asn', 'POST', [
                'asn_origin'           => 'pemprov_lampung',
                'nip_nippk'            => '1234567890', // invalid: < 18 digits
                'employment_status'    => 'pns',
                'organization_unit_id' => $opdId,
                'position'             => 'Pranata Komputer',
            ]);
            $controller->initController($request, service('response'), service('logger'));
            $res = $controller->registerProfileAttempt('asn');
            $this->assert($res instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on invalid NIP");
            $err = session()->getFlashdata('error');
            $this->assert($err !== null && str_contains($err, 'NIP / NIPPK'), "Invalid NIP error caught: '{$err}'");
        });

        // Test 17: GET /dashboard/daftar/tinjau (Review Step Rendering with Math Captcha & Honeypot)
        $this->testModule("GET /dashboard/daftar/tinjau (Review Step Rendering with Math Captcha & Honeypot)", function () {
            $db = \Config\Database::connect();
            $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
            $role = $db->table('procurement_roles')->where('is_active', 1)->get()->getRowArray();

            session()->set('registration_data', [
                'full_name'    => 'Pendaftar ASN Review',
                'username'     => 'asn_review',
                'email'        => 'review@lampungprov.go.id',
                'phone'        => '081234567890',
                'profile_type' => 'asn',
            ]);
            session()->set('registration_address', [
                'province' => '18',
                'regency'  => '18.71',
                'district' => '18.71.01',
                'village'  => null,
                'address'  => 'Jl. Wolter Monginsidi No. 69',
            ]);
            session()->set('registration_profile', [
                'profile_type' => 'asn',
                'table'        => 'asn_profiles',
                'fields'       => [
                    'asn_origin'           => 'pemprov_lampung',
                    'nip_nippk'            => '198501012010011005',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => $opd['id'],
                    'position'             => 'Pejabat Pembuat Komitmen Muda',
                    'has_procurement_role' => 'yes',
                    'procurement_roles'    => json_encode([(string) $role['id']]),
                ],
            ]);

            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar/tinjau', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->registerReview();
            $this->assert(is_string($html) && strlen($html) > 1000, "Review produced valid HTML");
            $this->assert(str_contains($html, 'Tinjau Data Pendaftaran'), "Title Tinjau present");
            $this->assert(str_contains($html, esc($opd['name'])), "Real OPD name from database displayed in review");
            $this->assert(str_contains($html, esc($role['name'])), "Real Procurement Role name from database displayed as badge");
            $this->assert(str_contains($html, 'name="website_url"'), "Honeypot anti-spam field present in review form");
            $this->assert(str_contains($html, 'name="register_captcha_answer"'), "Math captcha field present in review form");
            $this->assert(str_contains($html, 'csrf_test_name') || str_contains($html, 'csrf'), "CSRF token present");
        });

        // Test 18: POST /dashboard/daftar/tinjau (Anti-Spam Bot Rejection via Honeypot & Captcha)
        $this->testModule("POST /dashboard/daftar/tinjau (Anti-Spam Bot Rejection on Review Submit)", function () {
            session()->set('registration_data', [
                'full_name'    => 'Bot Spammer',
                'username'     => 'bot_spammer',
                'email'        => 'bot@spam.com',
                'phone'        => '081234567890',
                'profile_type' => 'public',
            ]);
            session()->set('registration_address', [
                'province' => '18',
                'regency'  => '18.71',
                'district' => '18.71.01',
                'village'  => null,
                'address'  => 'Jl. Spammer',
            ]);
            session()->set('registration_profile', [
                'profile_type' => 'public',
                'table'        => 'public_profiles',
                'fields'       => ['occupation_status' => 'swasta'],
            ]);
            session()->set('register_captcha_answer', 15);

            $controller = new RegisterController();

            // Part A: Honeypot filled
            $requestA = $this->createMockRequest('dashboard/daftar/tinjau', 'POST', [
                'terms'                   => '1',
                'privacy'                 => '1',
                'website_url'             => 'https://bot-farm.xyz',
                'register_captcha_answer' => '15',
            ]);
            $controller->initController($requestA, service('response'), service('logger'));
            $resA = $controller->registerReviewAttempt();
            $this->assert($resA instanceof \CodeIgniter\HTTP\RedirectResponse, "Honeypot submission redirected to login");
            $errA = session()->getFlashdata('error') ?? session()->get('error');
            $this->assert($errA !== null && str_contains($errA, 'mencurigakan'), "Honeypot suspicious error: '{$errA}'");

            // Part B: Wrong Captcha
            $requestB = $this->createMockRequest('dashboard/daftar/tinjau', 'POST', [
                'terms'                   => '1',
                'privacy'                 => '1',
                'register_captcha_answer' => '99', // Incorrect answer
            ]);
            $controller->initController($requestB, service('response'), service('logger'));
            $resB = $controller->registerReviewAttempt();
            $this->assert($resB instanceof \CodeIgniter\HTTP\RedirectResponse, "Wrong captcha redirected back");
            $errB = session()->getFlashdata('error') ?? session()->get('error');
            $this->assert($errB !== null && str_contains($errB, 'penjumlahan'), "Captcha error message caught: '{$errB}'");
        });

        // Test 19: Complete Atomic Registration Execution with Captcha & Decrypted Password
        $this->testModule("POST /dashboard/daftar/tinjau (Atomic Registration with Captcha & Decrypted Password)", function () {
            $db = \Config\Database::connect();
            $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
            $role = $db->table('procurement_roles')->where('is_active', 1)->get()->getRowArray();

            $uniqueUsername = 'asn_atomic_' . time() . '_' . rand(100, 999);
            $uniqueEmail    = 'asn_atomic_' . time() . '_' . rand(100, 999) . '@lampungprov.go.id';

            $controller = new RegisterController();

            // Reflection to call private encryptSecret
            $refMethod = new \ReflectionMethod($controller, 'encryptSecret');
            $refMethod->setAccessible(true);
            $encPassword = $refMethod->invoke($controller, 'SuperStrongSecret2026!#$');

            session()->set('registration_data', [
                'full_name'    => 'Aparatur Sipil Negara Berintegritas',
                'username'     => $uniqueUsername,
                'email'        => $uniqueEmail,
                'phone'        => '081299998888',
                'password_enc' => $encPassword,
                'profile_type' => 'asn',
            ]);
            session()->set('registration_address', [
                'province' => '18',
                'regency'  => '18.71',
                'district' => '18.71.01',
                'village'  => null,
                'address'  => 'Kompleks Kantor Gubernur Lampung',
            ]);
            session()->set('registration_profile', [
                'profile_type' => 'asn',
                'table'        => 'asn_profiles',
                'fields'       => [
                    'asn_origin'           => 'pemprov_lampung',
                    'nip_nippk'            => '199001012015011002',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => $opd['id'],
                    'position'             => 'Pengelola PBJ Ahli Pertama',
                    'has_procurement_role' => 'yes',
                    'procurement_roles'    => json_encode([(string) $role['id']]),
                ],
            ]);
            session()->set('register_captcha_answer', 15);

            $request = $this->createMockRequest('dashboard/daftar/tinjau', 'POST', [
                'terms'                   => '1',
                'privacy'                 => '1',
                'register_captcha_answer' => '15',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->registerReviewAttempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected after registration submit");
            $loc = $response->getHeaderLine('Location');
            $this->assert(str_contains($loc, 'berhasil'), "Redirect location is berhasil: '{$loc}'");

            // Verify in Database: users table
            $newUser = $db->table('users')->where('username', $uniqueUsername)->get()->getRowArray();
            $this->assert($newUser !== null, "User successfully inserted in users table");
            $this->assert(password_verify('SuperStrongSecret2026!#$', $newUser['password_hash']), "Decrypted password hashed correctly into database");

            // Verify in Database: asn_profiles table
            $asnProfile = $db->table('asn_profiles')->where('user_id', $newUser['id'])->get()->getRowArray();
            $this->assert($asnProfile !== null, "ASN profile inserted in asn_profiles table");
            $this->assert((int) $asnProfile['organization_unit_id'] === (int) $opd['id'], "organization_unit_id matches selected OPD");

            // Verify in Database: user_procurement_roles table
            $assignment = $db->table('user_procurement_roles')
                ->where('user_id', $newUser['id'])
                ->where('procurement_role_id', $role['id'])
                ->get()
                ->getRowArray();
            $this->assert($assignment !== null, "Role recorded in user_procurement_roles table");
        });

        // Test 20: Auto Trigger Email When Account is Approved
        $this->testModule("Trigger Email Notification on Profile Verification Approval", function () use ($admin) {
            $db = \Config\Database::connect();

            // Buat user dummy pending verification beserta profile_verification pending
            $testUserId = (new UserModel())->insert([
                'name'           => 'Calon Pengguna Disetujui',
                'username'       => 'pending_user_' . time() . '_' . rand(10, 99),
                'email'          => 'pending_' . time() . '_' . rand(10, 99) . '@lampungprov.go.id',
                'phone'          => '081234567890',
                'password_hash'  => password_hash('SecretPass123!', PASSWORD_DEFAULT),
                'profile_type'   => 'asn',
                'account_status' => 'pending_verification',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ], true);

            $verifId = (new ProfileVerificationModel())->insert([
                'user_id'    => $testUserId,
                'status'     => 'pending',
                'notes'      => 'Menunggu verifikasi pendaftaran',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], true);

            $service = new ProfileVerificationService();
            $ok = $service->approve($verifId, (int) $admin['id'], 'Akun memenuhi syarat verifikasi administrasi.');
            $this->assert($ok === true, "ProfileVerificationService::approve executed successfully");

            $updatedUser = (new UserModel())->find($testUserId);
            $this->assert($updatedUser['account_status'] === 'active', "Account status became 'active' on approval");

            // Verify EmailService method sendAccountApproved executes without exception
            $emailService = new EmailService();
            $sent = $emailService->sendAccountApproved($updatedUser['email'], $updatedUser['name']);
            $this->assert(is_array($sent) && isset($sent['success']), "sendAccountApproved method executed cleanly and returned result array");
        });

        // Test 21: GET /dashboard/daftar/berhasil (Registration Success Page)
        $this->testModule("GET /dashboard/daftar/berhasil (Registration Success Rendering)", function () {
            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar/berhasil', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->registerSuccess();
            $this->assert(is_string($html) && strlen($html) > 800, "Success page produced valid HTML");
            $this->assert(str_contains($html, 'Pendaftaran Berhasil Dikirim'), "Heading present");
            $this->assert(str_contains($html, 'Tahapan Verifikasi Akun'), "3-step verification flow present");
            $this->assert(str_contains($html, 'dashboard/masuk'), "Link to login present");
        });

        // Test 22: GET /dashboard/status-akun (Account Status View)
        $this->testModule("GET /dashboard/status-akun (Rendering Account Status)", function () use ($admin) {
            $this->clearAuthSession();
            session()->setFlashdata('pending_user_id', $admin['id']);
            $controller = new AccountStatusController();
            $request = $this->createMockRequest('dashboard/status-akun', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->show();
            $this->assert(is_string($html) && strlen($html) > 500, "Account status rendered HTML");
            $this->assert(str_contains($html, 'Status Akun'), "Title Status Akun present");
            $this->assert(str_contains($html, $admin['name']) || str_contains($html, $admin['username']), "User name or username rendered");
        });

        // Summary
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("   AUTH TEST RESULTS: %d PASSED, %d FAILED", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function createMockRequest(string $path, string $method = 'GET', array $post = []): IncomingRequest
    {
        $siteUri = new SiteURI(config('App'), $path);
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        $request = $request->withMethod($method);
        $_SERVER['HTTP_REFERER']   = site_url($path);
        $_SERVER['REQUEST_METHOD'] = $method;

        if ($method === 'POST') {
            $_POST = $post;
            $request->setGlobal('post', $post);
        } else {
            $_POST = [];
            $request->setGlobal('post', []);
        }

        \Config\Services::injectMock('request', $request);

        return $request;
    }

    private function clearAuthSession(): void
    {
        $_SESSION = [];
    }

    private function testModule(string $name, callable $callback): void
    {
        try {
            \Config\Services::resetSingle('session');
            \Config\Services::resetSingle('validation');
            \Config\Services::resetSingle('renderer');
            \Config\Services::resetSingle('request');
            \Config\Services::resetSingle('superglobals');
            $_SESSION = [];
            $_POST    = [];
            $_GET     = [];

            $callback();
            CLI::write("  [PASS] {$name}", "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] {$name} - " . $e->getMessage() . " at " . basename($e->getFile()) . ":" . $e->getLine(), "red");
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
