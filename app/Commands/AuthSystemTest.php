<?php

namespace App\Commands;

use App\Controllers\Dashboard\Auth\AccountStatusController;
use App\Controllers\Dashboard\Auth\LoginController;
use App\Controllers\Dashboard\Auth\PasswordController;
use App\Controllers\Dashboard\Auth\RegisterController;
use App\Models\PasswordResetModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
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
        // MODULE 1: /dashboard/masuk (LOGIN)
        // -------------------------------------------------------------

        // Test 1: GET /dashboard/masuk
        $this->testModule("GET /dashboard/masuk (Form Rendering & Brand Elements)", function () {
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

        // Test 3: POST /dashboard/masuk (Wrong Credentials Rejection)
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
            $this->assert($errorFlash !== null, "Error message flashed. Session: " . json_encode($_SESSION ?? []));
            $this->assert(! session()->get('isLoggedIn'), "User is NOT logged in");
        });

        // Test 4: POST /dashboard/masuk (Successful Authentication)
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
            $this->assert(session()->get('isLoggedIn') === true, "Session isLoggedIn set to true. Session: " . json_encode($_SESSION ?? []) . ", Location: " . $response->getHeaderLine('Location'));
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

        // Test 5: GET /dashboard/lupa-kata-sandi
        $this->testModule("GET /dashboard/lupa-kata-sandi (Form Rendering)", function () {
            $this->clearAuthSession();
            $controller = new PasswordController();
            $request = $this->createMockRequest('dashboard/lupa-kata-sandi', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $output = $controller->forgotForm();
            $this->assert(is_string($output) && strlen($output) > 800, "Forgot password returned HTML");
            $this->assert(str_contains($output, 'Lupa Kata Sandi'), "Contains Lupa Kata Sandi title");
            $this->assert(str_contains($output, 'name="email"'), "Email input field present");
            $this->assert(str_contains($output, 'csrf_test_name') || str_contains($output, 'csrf'), "CSRF token present");
            $this->assert(str_contains($output, 'dashboard/masuk'), "Link to login present");
        });

        // Test 6: POST /dashboard/lupa-kata-sandi (Generic Token Issuance)
        $this->testModule("POST /dashboard/lupa-kata-sandi (Token Generation & Anti-disclosure)", function () use ($admin) {
            $this->clearAuthSession();
            $controller = new PasswordController();
            $request = $this->createMockRequest('dashboard/lupa-kata-sandi', 'POST', [
                'email' => $admin['email'],
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->sendReset();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back");
            $message = session()->getFlashdata('message') ?? session()->get('message');
            $this->assert($message !== null, "Generic confirmation message set");

            // Verify a token was generated in password_resets
            $resets = (new PasswordResetModel())->where('user_id', $admin['id'])->orderBy('id', 'DESC')->first();
            $this->assert($resets !== null, "Token record created in password_resets table");
            $this->assert(! empty($resets['token_hash']), "token_hash stored securely as sha256");
        });

        // Test 7: GET & POST /dashboard/reset-kata-sandi (End-to-End Reset Flow)
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

        // -------------------------------------------------------------
        // MODULE 3: /dashboard/daftar (REGISTRATION FLOW)
        // -------------------------------------------------------------

        // Test 8: GET /dashboard/daftar (Step 1 Rendering)
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
            $this->assert(str_contains($output, 'name="password_confirmation"'), "Confirmation field present");
            $this->assert(str_contains($output, 'name="profile_type"'), "Profile type select present");
            $this->assert(str_contains($output, 'csrf_test_name') || str_contains($output, 'csrf'), "CSRF token present");
        });

        // Test 9: POST /dashboard/daftar (Password Confirmation & Validation)
        $this->testModule("POST /dashboard/daftar (Password Confirmation Mismatch Check)", function () {
            $this->clearAuthSession();
            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar', 'POST', [
                'full_name'             => 'Peserta Baru Pengadaan',
                'username'              => 'peserta_baru_' . time(),
                'email'                 => 'peserta_' . time() . '@lampungprov.go.id',
                'phone'                 => '081234567899',
                'password'              => 'SecurePass2026!#$',
                'password_confirmation' => 'DifferentPass2026!#$',
                'profile_type'          => 'asn',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on mismatch");
            $errors = session()->getFlashdata('errors') ?? session()->get('errors');
            $this->assert(! empty($errors), "Validation errors flashed");
            $this->assert(isset($errors['password_confirmation']) || in_array('Konfirmasi kata sandi tidak cocok dengan kata sandi.', $errors, true), "Password confirmation mismatch caught");
        });

        // Test 10: POST /dashboard/daftar (Step 1 Success -> Progress to Step 2)
        $this->testModule("POST /dashboard/daftar (Valid Step 1 Submission & Session Storage)", function () {
            $this->clearAuthSession();
            $controller = new RegisterController();
            $testUsername = 'pendaftar_test_' . time();
            $testEmail    = 'pendaftar_' . time() . '@lampungprov.go.id';
            $request = $this->createMockRequest('dashboard/daftar', 'POST', [
                'full_name'             => 'Pendaftar Uji Coba ASN',
                'username'              => $testUsername,
                'email'                 => $testEmail,
                'phone'                 => '081234567800',
                'password'              => 'SuperSecurePass2026!#$',
                'password_confirmation' => 'SuperSecurePass2026!#$',
                'profile_type'          => 'asn',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->attempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected to Step 2");
            $loc = $response->getHeaderLine('Location');
            $errs = session()->getFlashdata('errors') ?: session()->getFlashdata('error');
            $this->assert(str_contains($loc, 'dashboard/daftar/wilayah') || str_contains($loc, 'wilayah'), "Redirect location is wilayah. Location: '{$loc}', errors: " . json_encode($errs));

            $sessData = session('registration_data');
            $this->assert($sessData['username'] === $testUsername, "Registration data username stored in session");
            $this->assert($sessData['profile_type'] === 'asn', "Profile type stored in session");
        });

        // Test 11: GET /dashboard/daftar/wilayah (Step 2 Rendering)
        $this->testModule("GET /dashboard/daftar/wilayah (Step 2 Rendering with CSRF)", function () {
            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar/wilayah', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->registerAddress();
            $this->assert(is_string($html) && strlen($html) > 800, "Wilayah view produced HTML");
            $this->assert(str_contains($html, 'Wilayah dan Alamat'), "Title Wilayah present");
            $this->assert(str_contains($html, 'name="province"'), "Province select present");
            $this->assert(str_contains($html, 'name="regency"'), "Regency select present");
            $this->assert(str_contains($html, 'name="district"'), "District select present");
            $this->assert(str_contains($html, 'name="address"'), "Address textarea present");
            $this->assert(str_contains($html, 'csrf_test_name') || str_contains($html, 'csrf'), "CSRF token present");
        });

        // Test 12: GET /dashboard/daftar/profil/asn (Step 3 ASN Profile Form with Database OPD & Roles)
        $this->testModule("GET /dashboard/daftar/profil/asn (Step 3 Database OPD & Roles Rendering)", function () {
            session()->set('registration_data', [
                'full_name'    => 'Pendaftar ASN',
                'username'     => 'asn_pendaftar',
                'email'        => 'asn@lampungprov.go.id',
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
            $request = $this->createMockRequest('dashboard/daftar/profil/asn', 'GET');
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->registerAsn();
            $this->assert(is_string($html) && strlen($html) > 800, "ASN profile produced HTML");
            $this->assert(str_contains($html, 'Profil ASN'), "Title Profil ASN present");
            $this->assert(str_contains($html, 'name="asn_origin"'), "Asn origin select present");
            $this->assert(str_contains($html, 'name="employment_status"'), "Employment status select present");
            $this->assert(str_contains($html, 'name="organization_unit_id"'), "Database OPD select present");
            $this->assert(str_contains($html, 'Biro Pengadaan Barang dan Jasa') || str_contains($html, 'BPPBJ'), "Active OPD from database rendered in select options");
            $this->assert(str_contains($html, 'name="procurement_roles[]"'), "Database procurement roles checkboxes present");
            $this->assert(str_contains($html, 'Pejabat Pembuat Komitmen') || str_contains($html, 'PPK'), "Canonical procurement roles from database rendered");
            $this->assert(str_contains($html, 'csrf_test_name') || str_contains($html, 'csrf'), "CSRF token present");
        });

        // Test 13: POST /dashboard/daftar/profil/asn (Validation when Pemprov Lampung OPD is empty)
        $this->testModule("POST /dashboard/daftar/profil/asn (Rejection when OPD Pemprov Lampung is Empty)", function () {
            session()->set('registration_data', [
                'full_name'    => 'Pendaftar ASN',
                'username'     => 'asn_pendaftar',
                'email'        => 'asn@lampungprov.go.id',
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
            $request = $this->createMockRequest('dashboard/daftar/profil/asn', 'POST', [
                'asn_origin'           => 'pemprov_lampung',
                'employment_status'    => 'pns',
                'organization_unit_id' => '', // Empty OPD
                'position'             => 'Analis Kebijakan',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->registerProfileAttempt('asn');
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected back on missing OPD");
            $error = session()->getFlashdata('error');
            $this->assert($error !== null && str_contains($error, 'OPD/Perangkat Daerah'), "Missing OPD error message caught: '{$error}'");
        });

        // Test 14: POST /dashboard/daftar/profil/asn (Valid Submission with DB OPD and Roles)
        $this->testModule("POST /dashboard/daftar/profil/asn (Valid Submission with DB OPD & Roles)", function () {
            $db = \Config\Database::connect();
            $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
            $role = $db->table('procurement_roles')->where('is_active', 1)->get()->getRowArray();

            $this->assert($opd !== null, "At least one active OPD in database");
            $this->assert($role !== null, "At least one active procurement role in database");

            session()->set('registration_data', [
                'full_name'    => 'Pendaftar ASN Sukses',
                'username'     => 'asn_sukses_' . time(),
                'email'        => 'asn_' . time() . '@lampungprov.go.id',
                'phone'        => '081234567890',
                'password'     => 'SuperSecurePass2026!#$',
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
            $request = $this->createMockRequest('dashboard/daftar/profil/asn', 'POST', [
                'asn_origin'           => 'pemprov_lampung',
                'nip_nippk'            => '198501012010011005',
                'employment_status'    => 'pns',
                'organization_unit_id' => $opd['id'],
                'position'             => 'Pejabat Pembuat Komitmen Muda',
                'has_procurement_role' => 'yes',
                'procurement_roles'    => [$role['id']],
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->registerProfileAttempt('asn');
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected to Step 4 Tinjau");
            $loc = $response->getHeaderLine('Location');
            $this->assert(str_contains($loc, 'tinjau'), "Redirect location is tinjau: '{$loc}'");

            $profileSess = session('registration_profile');
            $this->assert(! empty($profileSess), "registration_profile stored in session");
            $this->assert((int) $profileSess['fields']['organization_unit_id'] === (int) $opd['id'], "Selected OPD ID stored");
            $this->assert(! empty($profileSess['fields']['institution_name']), "Auto-filled Pemprov Lampung as institution");
        });

        // Test 15: GET /dashboard/daftar/tinjau (Review Step Rendering with OPD Name & Role Badges)
        $this->testModule("GET /dashboard/daftar/tinjau (Review Step Rendering with OPD Name & Role Badges)", function () {
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
            $this->assert(str_contains($html, 'dashboard/daftar/profil/asn'), "Dynamic link to profil/asn present");
            $this->assert(str_contains($html, 'Verifikasi Keamanan Terjamin'), "Authentic security badge present");
            $this->assert(str_contains($html, 'csrf_test_name') || str_contains($html, 'csrf'), "CSRF token present");
        });

        // Test 16: Complete Atomic Registration Execution with Database OPD & user_procurement_roles
        $this->testModule("POST /dashboard/daftar/tinjau (Atomic Registration with OPD & user_procurement_roles)", function () {
            $db = \Config\Database::connect();
            $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
            $role = $db->table('procurement_roles')->where('is_active', 1)->get()->getRowArray();

            $uniqueUsername = 'asn_atomic_' . time() . '_' . rand(100, 999);
            $uniqueEmail    = 'asn_atomic_' . time() . '_' . rand(100, 999) . '@lampungprov.go.id';

            session()->set('registration_data', [
                'full_name'    => 'Aparatur Sipil Negara Berintegritas',
                'username'     => $uniqueUsername,
                'email'        => $uniqueEmail,
                'phone'        => '081299998888',
                'password'     => 'SuperStrongSecret2026!#$',
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

            $controller = new RegisterController();
            $request = $this->createMockRequest('dashboard/daftar/tinjau', 'POST', [
                'terms'   => '1',
                'privacy' => '1',
            ]);
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->registerReviewAttempt();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected after registration submit");
            $loc = $response->getHeaderLine('Location');
            $this->assert(str_contains($loc, 'berhasil'), "Redirect location is berhasil: '{$loc}'");

            // Verify in Database: users table
            $newUser = $db->table('users')->where('username', $uniqueUsername)->get()->getRowArray();
            $this->assert($newUser !== null, "User successfully inserted in users table");

            // Verify in Database: asn_profiles table
            $asnProfile = $db->table('asn_profiles')->where('user_id', $newUser['id'])->get()->getRowArray();
            $this->assert($asnProfile !== null, "ASN profile inserted in asn_profiles table");
            $this->assert((int) $asnProfile['organization_unit_id'] === (int) $opd['id'], "organization_unit_id matches selected OPD: " . $asnProfile['organization_unit_id']);
            $this->assert($asnProfile['unit_name'] === $opd['name'], "unit_name auto-populated with OPD name: " . $asnProfile['unit_name']);

            // Verify in Database: user_procurement_roles table
            $assignment = $db->table('user_procurement_roles')
                ->where('user_id', $newUser['id'])
                ->where('procurement_role_id', $role['id'])
                ->get()
                ->getRowArray();
            $this->assert($assignment !== null, "Role recorded in user_procurement_roles table");
            $this->assert($assignment['verification_status'] === 'pending', "Assignment status is pending verification");
        });

        // Test 17: Admin Management for Procurement Roles (Add, Edit, Toggle)
        $this->testModule("Admin Procurement Roles Management (Store, Edit, Toggle Active/Inactive)", function () {
            $controller = new \App\Controllers\Admin\ProcurementRoleController();
            $uniqueKey = 'ROLE_' . time() . '_' . rand(10, 99);

            // 1. Store
            $request = $this->createMockRequest('dashboard/akses/peran-pengadaan/tambah', 'POST', [
                'key'         => $uniqueKey,
                'name'        => 'Peran Pengujian Otomatis',
                'description' => 'Deskripsi peran otomatis',
                'is_active'   => '1',
            ]);
            $controller->initController($request, service('response'), service('logger'));
            $res = $controller->store();
            $this->assert($res instanceof \CodeIgniter\HTTP\RedirectResponse, "Role created redirected");

            $db = \Config\Database::connect();
            $createdRole = $db->table('procurement_roles')->where('key', $uniqueKey)->get()->getRowArray();
            $this->assert($createdRole !== null, "Role successfully inserted in procurement_roles table");

            // 2. Edit view
            $request = $this->createMockRequest('dashboard/akses/peran-pengadaan/' . $createdRole['id'] . '/ubah', 'GET');
            $controller->initController($request, service('response'), service('logger'));
            $editHtml = $controller->edit((string) $createdRole['id']);
            $this->assert(is_string($editHtml) && str_contains($editHtml, 'Ubah Peran Pengadaan'), "Edit view rendered successfully");

            // 3. Deactivate
            $request = $this->createMockRequest('dashboard/akses/peran-pengadaan/' . $createdRole['id'] . '/nonaktifkan', 'POST');
            $controller->initController($request, service('response'), service('logger'));
            $controller->deactivate((string) $createdRole['id']);
            $updated = $db->table('procurement_roles')->where('id', $createdRole['id'])->get()->getRowArray();
            $this->assert((int) $updated['is_active'] === 0, "Role successfully deactivated");

            // 4. Activate
            $request = $this->createMockRequest('dashboard/akses/peran-pengadaan/' . $createdRole['id'] . '/aktifkan', 'POST');
            $controller->initController($request, service('response'), service('logger'));
            $controller->activate((string) $createdRole['id']);
            $reactivated = $db->table('procurement_roles')->where('id', $createdRole['id'])->get()->getRowArray();
            $this->assert((int) $reactivated['is_active'] === 1, "Role successfully reactivated");
        });

        // Test 18: GET /dashboard/daftar/berhasil (Registration Success Page)
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

        // Test 19: GET /dashboard/status-akun (Account Status View)
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
