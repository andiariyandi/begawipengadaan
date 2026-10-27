<?php

namespace App\Commands;

use App\Controllers\Admin\UserController;
use App\Models\UserModel;
use App\Services\Admin\UserManagementService;
use App\Services\CertificateGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class UserProfileSafetyTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-profile-safety';
    protected $description = 'Verifies ASN and Academic Profile Isolation, Sanitization, and Certificate Data Integrity';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  VERIFIKASI INTEGRITAS DATA PROFIL USER & PENERBITAN SERTIFIKAT  ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $db         = db_connect();
        $userModel  = new UserModel();
        $userMgmt   = new UserManagementService();
        $generator  = new CertificateGenerator();
        $passCount  = 0;
        $failCount  = 0;

        // Bersihkan data sampah pengujian sebelumnya jika ada
        $purgeService = new \App\Services\Admin\UserPurgeService();
        $leftoverUsers = $db->table('users')->like('username', 'safety_test_')->select('id')->get()->getResultArray();
        foreach ($leftoverUsers as $u) {
            $purgeService->purge((int) $u['id'], 13, 'HAPUS');
        }

        // 1. Setup session Superadmin
        session()->set([
            'is_logged_in'     => true,
            'user_id'          => 13,
            'roles'            => ['super_administrator'],
            'profile_type'     => 'asn',
            'dashboard_access' => true,
        ]);

        try {
            // =========================================================================
            // TEST 1: Form View Inspection (Disambiguation and Disabling Logic)
            // =========================================================================
            CLI::write("\n[TEST 1] Memeriksa Kode Sumber View Form Pengguna (users/form.php)...", 'yellow');
            $formPath = APPPATH . 'Views/dashboard/users/form.php';
            $formContent = file_get_contents($formPath);

            $hasAsnName       = str_contains($formContent, 'name="asn_institution_name"');
            $hasAcademicName  = str_contains($formContent, 'name="academic_institution_name"');
            $hasNoUnilaDefault = ! str_contains($formContent, "?? 'Universitas Lampung'");
            $hasDisableLogic  = str_contains($formContent, 'ctrl.disabled = !isActive');

            if ($hasAsnName && $hasAcademicName && $hasNoUnilaDefault && $hasDisableLogic) {
                CLI::write(" [PASS] Form view memiliki pemisahan name, penghapusan hardcoded fallback, dan disabled toggle pada section tersembunyi.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Form view belum lengkap disanitasi: ASN=$hasAsnName, Academic=$hasAcademicName, NoUnila=$hasNoUnilaDefault, Disabled=$hasDisableLogic");
                $failCount++;
            }

            // =========================================================================
            // TEST 2: Update ASN Pemprov Lampung via UserManagementService
            // =========================================================================
            CLI::write("\n[TEST 2] Menguji Simpan / Update ASN Pemprov Lampung...", 'yellow');
            $asnUserPayload = [
                'username'       => 'safety_test_asn_' . time(),
                'email'          => 'safety_test_asn_' . time() . '@lampungprov.go.id',
                'name'           => 'Pegawai ASN Pemprov Berintegritas',
                'profile_type'   => 'asn',
                'phone'          => '081199887766',
                'password_hash'  => password_hash('BegawiPassword123!', PASSWORD_DEFAULT),
                'account_status' => 'active',
            ];
            $asnUserId = (int) $userModel->insert($asnUserPayload, true);

            // Simulasikan payload update ASN dengan origin pemprov_lampung
            $updateDataAsn = [
                'username'             => $asnUserPayload['username'],
                'email'                => $asnUserPayload['email'],
                'name'                 => 'Pegawai ASN Pemprov Berintegritas Updated',
                'profile_type'         => 'asn',
                'phone'                => '081199887766',
                'account_status'       => 'active',
                'employee_identifier'  => '198501012010011005',
                'origin_type'          => 'pemprov_lampung',
                'unit_name'            => 'Biro Pengadaan Barang dan Jasa',
                'position_name'        => 'Pengelola Pengadaan Barang/Jasa Ahli Muda',
                'rank_grade'           => 'Penata Tk. I (III/d)',
                // Simulasikan jika ada input legacy 'institution_name' bernilai 'Universitas Lampung'
                'institution_name'     => 'Universitas Lampung',
            ];

            $userMgmt->saveUser($updateDataAsn, $asnUserId);

            $savedAsn = $db->table('asn_profiles')->where('user_id', $asnUserId)->get()->getRowArray();
            if ($savedAsn && $savedAsn['institution_name'] === 'Pemerintah Provinsi Lampung') {
                CLI::write(" [PASS] UserManagementService berhasil memblokir kontaminasi 'Universitas Lampung' dan menetapkan 'Pemerintah Provinsi Lampung'.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Institusi ASN masih terkontaminasi: " . ($savedAsn['institution_name'] ?? 'NULL'));
                $failCount++;
            }

            // =========================================================================
            // TEST 3: Update ASN Luar Daerah (origin_type = pemerintah_daerah_lain)
            // =========================================================================
            CLI::write("\n[TEST 3] Menguji ASN Luar Daerah dengan Institusi Kustom...", 'yellow');
            $updateDataLuar = [
                'username'             => $asnUserPayload['username'],
                'email'                => $asnUserPayload['email'],
                'name'                 => 'Pegawai ASN Luar Daerah',
                'profile_type'         => 'asn',
                'phone'                => '081199887766',
                'account_status'       => 'active',
                'employee_identifier'  => '198702022012021002',
                'origin_type'          => 'pemerintah_daerah_lain',
                'asn_institution_name' => 'Pemerintah Kabupaten Lampung Selatan',
                'unit_name'            => 'Bagian Pengadaan Barang dan Jasa',
                'position_name'        => 'Staff Pengadaan',
            ];

            $userMgmt->saveUser($updateDataLuar, $asnUserId);
            $savedLuar = $db->table('asn_profiles')->where('user_id', $asnUserId)->get()->getRowArray();
            if ($savedLuar && $savedLuar['institution_name'] === 'Pemerintah Kabupaten Lampung Selatan') {
                CLI::write(" [PASS] ASN Luar Daerah berhasil menyimpan institusi spesifiknya dengan presisi.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Gagal menyimpan institusi ASN Luar Daerah: " . ($savedLuar['institution_name'] ?? 'NULL'));
                $failCount++;
            }

            // =========================================================================
            // TEST 4: Update Akademisi (Academic Profile)
            // =========================================================================
            CLI::write("\n[TEST 4] Menguji Profil Akademisi (academic_profiles)...", 'yellow');
            $academicUserPayload = [
                'username'       => 'safety_test_acad_' . time(),
                'email'          => 'safety_test_acad_' . time() . '@unila.ac.id',
                'name'           => 'Dosen Peneliti Unila',
                'profile_type'   => 'academic',
                'phone'          => '081233445566',
                'password_hash'  => password_hash('BegawiPassword123!', PASSWORD_DEFAULT),
                'account_status' => 'active',
            ];
            $acadUserId = (int) $userModel->insert($academicUserPayload, true);

            $updateDataAcad = [
                'username'                  => $academicUserPayload['username'],
                'email'                     => $academicUserPayload['email'],
                'name'                      => 'Dosen Peneliti Unila M.T.',
                'profile_type'              => 'academic',
                'phone'                     => '081233445566',
                'account_status'            => 'active',
                'academic_institution_name' => 'Universitas Lampung',
                'institution_type'          => 'Perguruan Tinggi Negeri',
                'role_name'                 => 'Dosen Fakultas Teknik',
            ];

            $userMgmt->saveUser($updateDataAcad, $acadUserId);
            $savedAcad = $db->table('academic_profiles')->where('user_id', $acadUserId)->get()->getRowArray();
            $asnResidual = $db->table('asn_profiles')->where('user_id', $acadUserId)->countAllResults();

            if ($savedAcad && $savedAcad['institution_name'] === 'Universitas Lampung' && $asnResidual === 0) {
                CLI::write(" [PASS] Profil Akademisi tersimpan akurat di academic_profiles tanpa meninggalkan jejak di asn_profiles.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Profil akademisi tidak sesuai atau terdapat residual ASN: count=$asnResidual");
                $failCount++;
            }

            // =========================================================================
            // TEST 5: Lookup User Profile for Certificate Generator
            // =========================================================================
            CLI::write("\n[TEST 5] Menguji Resolusi Profil Sertifikat (lookupUserProfile)...", 'yellow');
            // Kembalikan asnUser ke pemprov_lampung
            $db->table('asn_profiles')->where('user_id', $asnUserId)->update([
                'origin_type'      => 'pemprov_lampung',
                'institution_name' => 'Pemerintah Provinsi Lampung',
            ]);

            // Gunakan ReflectionMethod untuk memanggil lookupUserProfile (private method)
            $reflection = new \ReflectionClass(CertificateGenerator::class);
            $method = $reflection->getMethod('lookupUserProfile');
            $method->setAccessible(true);

            $resolvedAsn = $method->invoke($generator, $asnUserId);
            if (
                $resolvedAsn['profile_type'] === 'asn' &&
                $resolvedAsn['institution_name'] === 'Pemerintah Provinsi Lampung' &&
                $resolvedAsn['institution_name'] !== 'Universitas Lampung'
            ) {
                CLI::write(" [PASS] lookupUserProfile menyelesaikan profil ASN Pemprov dengan tepat ke 'Pemerintah Provinsi Lampung'.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] lookupUserProfile mengembalikan data tidak valid: " . json_encode($resolvedAsn));
                $failCount++;
            }

            // Uji jika di tabel asn_profiles tersimpan string korup "Universitas Lampung" untuk origin pemprov_lampung
            $db->table('asn_profiles')->where('user_id', $asnUserId)->update([
                'institution_name' => 'Universitas Lampung',
            ]);
            $resolvedCorruptedAsn = $method->invoke($generator, $asnUserId);
            if ($resolvedCorruptedAsn['institution_name'] === 'Pemerintah Provinsi Lampung') {
                CLI::write(" [PASS] Safety Net CertificateGenerator berhasil memperbaiki nilai korup secara defensif menjadi 'Pemerintah Provinsi Lampung'.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Safety net gagal menormalkan institusi korup: " . $resolvedCorruptedAsn['institution_name']);
                $failCount++;
            }

            // =========================================================================
            // TEST 6: Audit Integritas Database Seluruh Tabel asn_profiles
            // =========================================================================
            CLI::write("\n[TEST 6] Memindai Seluruh Database asn_profiles (Sweep Anti-Korup)...", 'yellow');
            // Kembalikan test ASN ke status bersih sebelum scan
            $db->table('asn_profiles')->where('user_id', $asnUserId)->update([
                'institution_name' => 'Pemerintah Provinsi Lampung',
            ]);

            $corruptRows = $db->table('asn_profiles')
                ->where('origin_type', 'pemprov_lampung')
                ->where('institution_name', 'Universitas Lampung')
                ->get()
                ->getResultArray();

            if (empty($corruptRows)) {
                CLI::write(" [PASS] Zero-Corruption: Tidak ada baris asn_profiles Pemprov Lampung yang berisi 'Universitas Lampung'.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Ditemukan " . count($corruptRows) . " baris korup di tabel asn_profiles!");
                $failCount++;
            }

            // Bersihkan test data
            $purgeService = new \App\Services\Admin\UserPurgeService();
            foreach ([$asnUserId, $acadUserId] as $uid) {
                if ($uid > 0) {
                    $purgeService->purge($uid, 13, 'HAPUS');
                }
            }
            CLI::write("\n[CLEANUP] Data user uji berhasil dibersihkan dari database.", 'cyan');

        } catch (\Throwable $e) {
            CLI::error("Terjadi exception saat pengujian: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $failCount++;
        }

        CLI::write("==================================================================", 'cyan');
        CLI::write("HASIL VERIFIKASI: {$passCount} PASSED, {$failCount} FAILED", $failCount === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }
}
