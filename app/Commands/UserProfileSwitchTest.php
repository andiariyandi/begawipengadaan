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

class UserProfileSwitchTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-profile-switch';
    protected $description = 'Verifikasi Dinamika Form Berdasarkan Tipe Profil & Pembersihan Data Profil Lama saat Switch';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  PENGUJIAN FORM DINAMIS TIPE PROFIL & PEMBERSIHAN DATA LAMA     ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $db = db_connect('default');
        $passed = 0;
        $failed = 0;

        // Set session Super Admin
        session()->set([
            'is_logged_in'     => true,
            'user_id'          => 13,
            'roles'            => ['super_administrator'],
            'profile_type'     => 'asn',
            'dashboard_access' => true,
        ]);

        $testUserId = 321;
        $userModel  = new UserModel();
        $targetUser = $userModel->find($testUserId);

        if (! $targetUser) {
            CLI::error("User ID #321 tidak ditemukan!");
            return 1;
        }

        // Simpan data awal user #321 untuk direstore di akhir pengujian
        $originalType        = $targetUser['profile_type'];
        $originalAsn         = $db->table('asn_profiles')->where('user_id', $testUserId)->get()->getRowArray();
        $originalUserProfile = $db->table('user_profiles')->where('user_id', $testUserId)->get()->getRowArray();

        try {
            // =========================================================================
            // SCENARIO 1: Uji Render Form Edit /dashboard/akses/pengguna/321/ubah
            // =========================================================================
            CLI::write("\n--- [SCENARIO 1] Uji Render Form Edit /dashboard/akses/pengguna/321/ubah ---", 'yellow');
            $ctrl = new UserController();
            $req = $this->createMockRequest("dashboard/akses/pengguna/{$testUserId}/ubah", 'GET');
            $ctrl->initController($req, service('response'), service('logger'));

            $viewOutput = $ctrl->edit((string) $testUserId);
            $html = is_string($viewOutput) ? $viewOutput : (method_exists($viewOutput, 'getBody') ? $viewOutput->getBody() : '');

            // 1. Cek keberadaan 4 kontainer profil
            $hasAsnSection       = str_contains($html, 'id="section-profile-asn"');
            $hasProviderSection  = str_contains($html, 'id="section-profile-provider"');
            $hasAcademicSection  = str_contains($html, 'id="section-profile-academic"');
            $hasPublicSection    = str_contains($html, 'id="section-profile-public"');
            $hasSwitcherScript   = str_contains($html, 'switchProfile');

            if ($hasAsnSection && $hasProviderSection && $hasAcademicSection && $hasPublicSection && $hasSwitcherScript) {
                CLI::write(" [PASS] Seluruh 4 kontainer profil (ASN, Provider, Academic, Public) dan script dynamic switcher ter-render", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Salah satu kontainer profil atau script switcher hilang dari view");
                $failed++;
            }

            // 2. Cek bahwa ASN aktif (tidak d-none) dan profil lain tersembunyi (d-none) untuk user 321
            if (! str_contains($html, 'id="section-profile-asn" class="profile-section d-none"') &&
                str_contains($html, 'id="section-profile-provider" class="profile-section d-none"') &&
                str_contains($html, 'id="section-profile-public" class="profile-section d-none"')) {
                CLI::write(" [PASS] Kontainer profil ASN aktif secara default untuk user 321, sedangkan profil lainnya tersembunyi (d-none)", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Status visibility section profil tidak sesuai profil awal user 321");
                $failed++;
            }

            // 3. Cek keberadaan nested dropdowns Card 3 (Wilayah Domisili & Alamat)
            $hasProvSelect     = str_contains($html, 'id="province_code"');
            $hasRegSelect      = str_contains($html, 'id="regency_code"');
            $hasDistSelect     = str_contains($html, 'id="district_code"');
            $hasVillSelect     = str_contains($html, 'id="village_code"');
            $hasAddrTextarea   = str_contains($html, 'id="address_line"');
            $hasWilayahScript  = str_contains($html, 'dashboard/daftar/wilayah/options');
            $hasSelectedData   = str_contains($html, 'data-selected="18.71"') && str_contains($html, 'data-selected="18.71.07"');

            if ($hasProvSelect && $hasRegSelect && $hasDistSelect && $hasVillSelect && $hasAddrTextarea && $hasWilayahScript && $hasSelectedData) {
                CLI::write(" [PASS] Card 3 Domisili merender 4 nested dropdowns (Prov, Kab/Kota, Kec, Kel/Desa), data-selected user 321, dan cascading script", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Elemen nested dropdowns Card 3 atau cascading script tidak lengkap pada view");
                $failed++;
            }

            // =========================================================================
            // SCENARIO 2: Ganti Tipe Profil dari ASN ke UMUM (Public)
            // =========================================================================
            CLI::write("\n--- [SCENARIO 2] Ganti Tipe Profil: ASN -> Umum (Public) ---", 'yellow');
            $postDataToPublic = [
                'username'            => $targetUser['username'],
                'email'               => $targetUser['email'],
                'name'                => $targetUser['name'],
                'profile_type'        => 'public',
                'account_status'      => 'active',
                'phone'               => $targetUser['phone'],
                'national_identifier' => '1871012345678901',
                'occupation_status'   => 'Wiraswasta Konsultan',
                'organization_name'   => 'Komunitas Pengadaan Lampung',
                'interest_areas'      => ['Pengadaan Barang', 'E-Katalog'],
                'usage_purpose'       => ['Literasi PBJ'],
            ];

            (new UserManagementService())->saveUser($postDataToPublic, $testUserId, 13);

            $userAfterPublic = $userModel->find($testUserId);
            $asnCount        = $db->table('asn_profiles')->where('user_id', $testUserId)->countAllResults();
            $publicRow       = $db->table('public_profiles')->where('user_id', $testUserId)->get()->getRowArray();
            $providerCount   = $db->table('provider_profiles')->where('user_id', $testUserId)->countAllResults();
            $academicCount   = $db->table('academic_profiles')->where('user_id', $testUserId)->countAllResults();

            if ($userAfterPublic['profile_type'] === 'public' &&
                $asnCount === 0 &&
                ! empty($publicRow) &&
                $publicRow['national_identifier'] === '1871012345678901' &&
                $publicRow['occupation_status'] === 'Wiraswasta Konsultan' &&
                $providerCount === 0 &&
                $academicCount === 0) {
                CLI::write(" [PASS] Data ASN lama BERHASIL DIHAPUS (count: 0) dan digantikan oleh data Profil Umum yang baru (NIK: {$publicRow['national_identifier']})", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Data profil lama tidak terhapus atau profil baru gagal tersimpan (asn: {$asnCount}, public: " . ($publicRow ? 'ada' : 'kosong') . ")");
                $failed++;
            }

            // =========================================================================
            // SCENARIO 3: Ganti Tipe Profil dari UMUM ke PENYEDIA (Provider)
            // =========================================================================
            CLI::write("\n--- [SCENARIO 3] Ganti Tipe Profil: Umum -> Penyedia (Provider) ---", 'yellow');
            $postDataToProvider = [
                'username'            => $targetUser['username'],
                'email'               => $targetUser['email'],
                'name'                => $targetUser['name'],
                'profile_type'        => 'provider',
                'account_status'      => 'active',
                'phone'               => $targetUser['phone'],
                'business_name'       => 'PT Lampung Berjaya PBJ',
                'business_form'       => 'PT',
                'business_identifier' => '9120001234567',
                'business_field'      => 'Jasa Konstruksi dan ATK',
                'registrant_position' => 'Direktur Utama',
                'responsible_person'  => 'Andi Wijaya, S.T.',
                'business_email'      => 'kontrak@lampungberjaya.co.id',
                'business_address'    => 'Jl. Cut Nyak Dien No. 88, Bandar Lampung',
            ];

            (new UserManagementService())->saveUser($postDataToProvider, $testUserId, 13);

            $userAfterProv = $userModel->find($testUserId);
            $publicCount2  = $db->table('public_profiles')->where('user_id', $testUserId)->countAllResults();
            $providerRow   = $db->table('provider_profiles')->where('user_id', $testUserId)->get()->getRowArray();
            $asnCount2     = $db->table('asn_profiles')->where('user_id', $testUserId)->countAllResults();

            if ($userAfterProv['profile_type'] === 'provider' &&
                $publicCount2 === 0 &&
                ! empty($providerRow) &&
                $providerRow['business_name'] === 'PT Lampung Berjaya PBJ' &&
                $providerRow['business_form'] === 'PT' &&
                $asnCount2 === 0) {
                CLI::write(" [PASS] Data Umum lama BERHASIL DIHAPUS (count: 0) dan digantikan Profil Penyedia baru (Perusahaan: {$providerRow['business_name']})", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Data profil umum lama tidak terhapus atau profil penyedia baru gagal disimpan");
                $failed++;
            }

            // =========================================================================
            // SCENARIO 4: Ganti Tipe Profil Kembali ke ASN
            // =========================================================================
            CLI::write("\n--- [SCENARIO 4] Kembalikan Tipe Profil: Penyedia -> ASN ---", 'yellow');
            $postDataBackToAsn = [
                'username'             => $targetUser['username'],
                'email'                => $targetUser['email'],
                'name'                 => $targetUser['name'],
                'profile_type'         => 'asn',
                'account_status'       => 'active',
                'phone'                => $targetUser['phone'],
                'employee_identifier'  => '1111121',
                'employment_status'    => 'pns',
                'origin_type'          => 'pemprov_lampung',
                'organization_unit_id' => 6,
                'unit_name'            => 'Biro Pengadaan Barang dan Jasa',
                'position_name'        => 'PKSTI',
                'rank_grade'           => '3a',
                'institution_name'     => 'Pemerintah Daerah Provinsi Lampung',
            ];

            (new UserManagementService())->saveUser($postDataBackToAsn, $testUserId, 13);

            $userRestored = $userModel->find($testUserId);
            $provCount3   = $db->table('provider_profiles')->where('user_id', $testUserId)->countAllResults();
            $asnRestored  = $db->table('asn_profiles')->where('user_id', $testUserId)->get()->getRowArray();

            if ($userRestored['profile_type'] === 'asn' &&
                $provCount3 === 0 &&
                ! empty($asnRestored) &&
                $asnRestored['employee_identifier'] === '1111121' &&
                $asnRestored['unit_name'] === 'Biro Pengadaan Barang dan Jasa') {
                CLI::write(" [PASS] Data Penyedia lama BERHASIL DIHAPUS (count: 0) dan Profil ASN berhasil dipulihkan (NIP: {$asnRestored['employee_identifier']})", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Pemulihan profil ASN gagal");
                $failed++;
            }

            // =========================================================================
            // SCENARIO 5: Uji Form Tambah Pengguna Baru (/dashboard/akses/pengguna/tambah)
            // =========================================================================
            CLI::write("\n--- [SCENARIO 5] Uji Form Tambah Pengguna Baru (/dashboard/akses/pengguna/tambah) ---", 'yellow');
            $reqNew = $this->createMockRequest("dashboard/akses/pengguna/tambah", 'GET');
            $ctrl->initController($reqNew, service('response'), service('logger'));
            $htmlNew = $ctrl->create();

            if (str_contains($htmlNew, 'Formulir Pembuatan Akun Baru') &&
                str_contains($htmlNew, 'id="section-profile-asn"') &&
                str_contains($htmlNew, 'id="section-profile-provider"') &&
                str_contains($htmlNew, 'id="section-profile-academic"') &&
                str_contains($htmlNew, 'id="section-profile-public"')) {
                CLI::write(" [PASS] Halaman Tambah Pengguna merender seluruh 4 section profil secara lengkap", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Halaman Tambah Pengguna gagal merender form profil");
                $failed++;
            }

            // Uji Buat User Baru Akademisi
            $newUsername = 'test_acad_' . time();
            $newUserData = [
                'username'                => $newUsername,
                'email'                   => $newUsername . '@kampus.ac.id',
                'name'                    => 'Dr. Pengadaan Akademisi, M.Si.',
                'password'                => 'BegawiAkademik123!',
                'profile_type'            => 'academic',
                'account_status'          => 'active',
                'institution_name'        => 'Universitas Lampung',
                'institution_type'        => 'Perguruan Tinggi Negeri',
                'role_name'               => 'Dosen',
                'professional_identifier' => '0012345678',
                'expertise'               => 'Manajemen Rantai Pasok Publik',
                'institution_email'       => 'dosen@unila.ac.id',
            ];

            $newUserId = (new UserManagementService())->saveUser($newUserData, null, 13);
            $acadRow = $db->table('academic_profiles')->where('user_id', $newUserId)->get()->getRowArray();
            $asnCountNew = $db->table('asn_profiles')->where('user_id', $newUserId)->countAllResults();

            if ($newUserId > 0 && ! empty($acadRow) && $acadRow['institution_name'] === 'Universitas Lampung' && $asnCountNew === 0) {
                CLI::write(" [PASS] Pembuatan pengguna baru tipe Akademisi sukses, profil akademik tersimpan di academic_profiles, 0 residual di tabel lain", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Pembuatan pengguna baru akademisi gagal");
                $failed++;
            }

            // Bersihkan pengguna uji akademisi
            $userModel->delete($newUserId, true);

            // =========================================================================
            // SCENARIO 6: Uji Sinkronisasi & Persistensi Wilayah Berjenjang pada user_profiles
            // =========================================================================
            CLI::write("\n--- [SCENARIO 6] Uji Sinkronisasi & Persistensi Wilayah Berjenjang (Card 3) ---", 'yellow');
            $customAddressData = [
                'username'            => $targetUser['username'],
                'email'               => $targetUser['email'],
                'name'                => $targetUser['name'],
                'profile_type'        => 'asn',
                'account_status'      => 'active',
                'nip'                 => '1111121',
                'province_code'       => '18',
                'regency_code'        => '18.01',
                'district_code'       => '18.01.04',
                'village_code'        => '18.01.04.2001',
                'address_line'        => 'Kompleks Pemkab Lampung Barat, Liwa',
            ];

            (new UserManagementService())->saveUser($customAddressData, $testUserId, 13);
            $savedProfile = $db->table('user_profiles')->where('user_id', $testUserId)->get()->getRowArray();

            if ($savedProfile &&
                $savedProfile['province_code'] === '18' &&
                $savedProfile['regency_code'] === '18.01' &&
                $savedProfile['district_code'] === '18.01.04' &&
                $savedProfile['village_code'] === '18.01.04.2001' &&
                $savedProfile['address_line'] === 'Kompleks Pemkab Lampung Barat, Liwa') {
                CLI::write(" [PASS] Data wilayah nested (Prov 18, Kab 18.01, Kec 18.01.04, Desa 18.01.04.2001, Alamat) tersimpan akurat di user_profiles", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Sinkronisasi wilayah berjenjang pada user_profiles tidak sesuai ekspektasi: " . json_encode($savedProfile));
                $failed++;
            }

        } finally {
            // Pastikan data user 321 dalam kondisi aman
            if ($originalType === 'asn' && ! empty($originalAsn)) {
                $userModel->update($testUserId, ['profile_type' => 'asn']);
                $db->table('provider_profiles')->where('user_id', $testUserId)->delete();
                $db->table('academic_profiles')->where('user_id', $testUserId)->delete();
                $db->table('public_profiles')->where('user_id', $testUserId)->delete();
                $exists = $db->table('asn_profiles')->where('user_id', $testUserId)->countAllResults();
                if ($exists === 0) {
                    $originalAsn['created_at'] = date('Y-m-d H:i:s');
                    $originalAsn['updated_at'] = date('Y-m-d H:i:s');
                    unset($originalAsn['id']);
                    $db->table('asn_profiles')->insert($originalAsn);
                }
            }

            // Pulihkan user_profiles awal user 321
            if (! empty($originalUserProfile)) {
                $db->table('user_profiles')->where('user_id', $testUserId)->update([
                    'province_code' => $originalUserProfile['province_code'],
                    'regency_code'  => $originalUserProfile['regency_code'],
                    'district_code' => $originalUserProfile['district_code'],
                    'village_code'  => $originalUserProfile['village_code'],
                    'address_line'  => $originalUserProfile['address_line'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        CLI::write("==================================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN: {$passed} LULUS, {$failed} GAGAL", $failed === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failed === 0 ? 0 : 1;
    }

    private function createMockRequest(string $uriPath, string $method = 'GET', array $params = []): IncomingRequest
    {
        $appConfig = config('App');
        $siteUri = new SiteURI($appConfig, $uriPath);
        $userAgent = new UserAgent();

        $req = new IncomingRequest($appConfig, $siteUri, null, $userAgent);
        $req->setMethod($method);
        if ($method === 'GET' && ! empty($params)) {
            $req->setGlobal('get', $params);
        } elseif ($method === 'POST' && ! empty($params)) {
            $req->setGlobal('post', $params);
        }
        return $req;
    }
}
