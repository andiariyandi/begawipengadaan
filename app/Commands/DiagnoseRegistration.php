<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\Identity\RegistrationService;

class DiagnoseRegistration extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:registration';
    protected $description = 'Diagnose registration flows across all profile types and ASN origins';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("           DIAGNOSTIC TEST REGISTRASI SEMUA JALUR                ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();

        // Find a valid wilayah chain in DB
        $district = $db->table('wilayah')->where('CHAR_LENGTH(kode)', 8)->get()->getRowArray();
        if (! $district) {
            CLI::error("No district found in wilayah table");
            return 1;
        }
        $dKode = $district['kode'];
        $rKode = substr($dKode, 0, 5);
        $pKode = substr($dKode, 0, 2);

        $village = $db->table('wilayah')->where('CHAR_LENGTH(kode)', 13)->like('kode', $dKode . '.', 'after')->get()->getRowArray();
        $vKode = $village ? $village['kode'] : null;

        CLI::write("Using Wilayah Chain: Province={$pKode}, Regency={$rKode}, District={$dKode}, Village={$vKode}", "cyan");
        CLI::newLine();

        // Get an active OPD
        $opd = $db->table('organization_units')->where('is_active', 1)->get()->getRowArray();
        $opdId = $opd ? (int)$opd['id'] : 1;

        $scenarios = [
            '1. ASN Pemprov Lampung (Valid OPD)' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Dr. Warsito No. 4, Teluk Betung',
                'name'          => 'Test ASN Pemprov',
                'username'      => 'test_asn_pemprov_' . time(),
                'email'         => 'test_pemprov_' . time() . '@example.com',
                'phone'         => '081234567801',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'pemprov_lampung',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => $opdId,
                    'position'             => 'Pranata Komputer Ahli Muda',
                    'nip_nippk'            => '199001012015011001',
                ],
            ],
            '2. ASN Pemerintah Daerah Lain (organization_unit_id is empty string)' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Sudirman No. 10',
                'name'          => 'Test ASN Pemda Lain',
                'username'      => 'test_asn_pemda_' . time(),
                'email'         => 'test_pemda_' . time() . '@example.com',
                'phone'         => '081234567802',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'pemerintah_daerah_lain',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => '',
                    'institution_name'     => 'Pemerintah Kabupaten Lampung Selatan',
                    'external_unit_name'   => 'Bagian Pengadaan Barang dan Jasa',
                    'position'             => 'Pejabat Pengadaan',
                    'nip_nippk'            => '199202022016021002',
                ],
            ],
            '3. ASN Kementerian / Lembaga' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Medan Merdeka Barat',
                'name'          => 'Test ASN KL',
                'username'      => 'test_asn_kl_' . time(),
                'email'         => 'test_kl_' . time() . '@example.com',
                'phone'         => '081234567803',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'kementerian_lembaga',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => '',
                    'institution_name'     => 'Kementerian Keuangan RI',
                    'external_unit_name'   => 'DJKN Lampung',
                    'position'             => 'Analis Anggaran',
                ],
            ],
            '4. ASN Pemerintah Desa' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Balai Desa Way Galih',
                'name'          => 'Test Pemdes',
                'username'      => 'test_pemdes_' . time(),
                'email'         => 'test_pemdes_' . time() . '@example.com',
                'phone'         => '081234567804',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'pemerintah_desa',
                    'employment_status'    => 'pppk',
                    'organization_unit_id' => '',
                    'institution_name'     => 'Pemerintah Desa Way Galih',
                    'external_unit_name'   => 'Sekretariat Desa',
                    'position'             => 'Sekretaris Desa',
                ],
            ],
            '5. Pegawai BUMN / BUMD' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Raden Intan',
                'name'          => 'Test BUMD',
                'username'      => 'test_bumd_' . time(),
                'email'         => 'test_bumd_' . time() . '@example.com',
                'phone'         => '081234567805',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'bumn_bumd',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => '',
                    'institution_name'     => 'Bank Lampung',
                    'external_unit_name'   => 'Divisi Umum & Pengadaan',
                    'position'             => 'Staf Pengadaan',
                ],
            ],
            '6. ASN Lainnya' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Gatot Subroto',
                'name'          => 'Test ASN Lain',
                'username'      => 'test_asn_lain_' . time(),
                'email'         => 'test_lain_' . time() . '@example.com',
                'phone'         => '081234567806',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'lainnya',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => '',
                    'institution_name'     => 'Instansi Lainnya',
                    'external_unit_name'   => 'Unit Pengadaan',
                    'position'             => 'Staf Teknis',
                ],
            ],
            '6b. ASN Pemda Lain MINIMAL (All optional fields empty / omitted)' => [
                'profile_type'  => 'asn',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => null,
                'address_line'  => 'Jl. Lintas Sumatera No. 1',
                'name'          => 'Test ASN Minimal',
                'username'      => 'test_asn_min_' . time(),
                'email'         => 'test_min_' . time() . '@example.com',
                'phone'         => '081234567866',
                'password'      => 'Password123#',
                'profile'       => [
                    'asn_origin'           => 'pemerintah_daerah_lain',
                    'employment_status'    => 'pns',
                    'organization_unit_id' => '',
                    'position'             => 'Staf Pelaksana',
                    'nip_nippk'            => '',
                    'official_email'       => '',
                    'rank'                 => '',
                ],
            ],
            '7. Penyedia (Provider - Full)' => [
                'profile_type'  => 'provider',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Antasari No. 88',
                'name'          => 'Test Penyedia',
                'username'      => 'test_provider_' . time(),
                'email'         => 'test_provider_' . time() . '@example.com',
                'phone'         => '081234567807',
                'password'      => 'Password123#',
                'profile'       => [
                    'business_name'       => 'PT Begawi Konstruksi Jaya',
                    'business_form'       => 'pt',
                    'business_field'      => 'Konstruksi & Pengadaan Alat',
                    'business_identifier' => '1234567890123',
                    'registrant_position' => 'Direktur Utama',
                    'responsible_person'  => 'Ahmad Syarif',
                    'business_email'      => 'info@begawikonstruksi.com',
                    'business_address'    => 'Jl. Antasari No. 88, Bandar Lampung',
                ],
            ],
            '7b. Penyedia (Provider MINIMAL - Only required fields)' => [
                'profile_type'  => 'provider',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => null,
                'address_line'  => 'Jl. Kartini No. 5',
                'name'          => 'Test Penyedia Minimal',
                'username'      => 'test_prov_min_' . time(),
                'email'         => 'test_prov_min_' . time() . '@example.com',
                'phone'         => '081234567877',
                'password'      => 'Password123#',
                'profile'       => [
                    'company_name'         => 'CV Sahabat Pengadaan',
                    'business_entity_type' => 'cv',
                    'registrant_position'  => 'Wakil Direktur',
                    'responsible_person'   => 'Budi Santoso',
                    'nib'                  => '',
                    'company_email'        => '',
                    'company_address'      => '',
                ],
            ],
            '8. Akademisi (Academic - Full)' => [
                'profile_type'  => 'academic',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Prof. Dr. Sumantri Brojonegoro No. 1',
                'name'          => 'Test Akademisi',
                'username'      => 'test_academic_' . time(),
                'email'         => 'test_academic_' . time() . '@example.com',
                'phone'         => '081234567808',
                'password'      => 'Password123#',
                'profile'       => [
                    'institution_name' => 'Universitas Lampung (UNILA)',
                    'institution_type' => 'universitas',
                    'role_name'        => 'Dosen / Peneliti PBJ',
                    'expertise'        => ['Hukum Kontrak Pengadaan', 'Manajemen Risiko PBJ'],
                ],
            ],
            '8b. Akademisi (Academic MINIMAL - Empty expertise, nidn, portfolio)' => [
                'profile_type'  => 'academic',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => null,
                'address_line'  => 'Kampus Itera',
                'name'          => 'Test Akademisi Minimal',
                'username'      => 'test_acad_min_' . time(),
                'email'         => 'test_acad_min_' . time() . '@example.com',
                'phone'         => '081234567888',
                'password'      => 'Password123#',
                'profile'       => [
                    'institution_name' => 'Institut Teknologi Sumatera',
                    'institution_type' => 'perguruan_tinggi',
                    'academic_role'    => 'dosen',
                    'expertise'        => '',
                    'institution_email' => '',
                    'professional_identifier' => '',
                    'portfolio_url'    => '',
                ],
            ],
            '9. Masyarakat Umum (Public - Full)' => [
                'profile_type'  => 'public',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => $vKode,
                'address_line'  => 'Jl. Teuku Umar No. 25',
                'name'          => 'Test Masyarakat',
                'username'      => 'test_public_' . time(),
                'email'         => 'test_public_' . time() . '@example.com',
                'phone'         => '081234567809',
                'password'      => 'Password123#',
                'profile'       => [
                    'occupation_status' => 'Wiraswasta / Pemerhati Kebijakan Publik',
                    'organization_name' => 'Komunitas PBJ Berintegritas',
                ],
            ],
            '9b. Masyarakat Umum (Public MINIMAL - Empty optional fields)' => [
                'profile_type'  => 'public',
                'province_code' => $pKode,
                'regency_code'  => $rKode,
                'district_code' => $dKode,
                'village_code'  => null,
                'address_line'  => 'Jl. Pagar Alam',
                'name'          => 'Test Masyarakat Minimal',
                'username'      => 'test_pub_min_' . time(),
                'email'         => 'test_pub_min_' . time() . '@example.com',
                'phone'         => '081234567899',
                'password'      => 'Password123#',
                'profile'       => [
                    'occupation_status'    => 'masyarakat',
                    'nik'                  => '',
                    'organization_name'    => '',
                    'procurement_interest' => '',
                    'usage_purpose'        => '',
                ],
            ],
        ];

        $service = new RegistrationService();

        foreach ($scenarios as $name => $payload) {
            try {
                $userId = $service->register($payload);
                CLI::write(" [PASS] {$name} => Created User ID {$userId}", "green");

                // Clean up created test user
                $db->table('user_procurement_roles')->where('user_id', $userId)->delete();
                $db->table('user_roles')->where('user_id', $userId)->delete();
                $db->table('profile_verifications')->where('user_id', $userId)->delete();
                $db->table('asn_profiles')->where('user_id', $userId)->delete();
                $db->table('provider_profiles')->where('user_id', $userId)->delete();
                $db->table('academic_profiles')->where('user_id', $userId)->delete();
                $db->table('public_profiles')->where('user_id', $userId)->delete();
                $db->table('user_profiles')->where('user_id', $userId)->delete();
                $db->table('users')->where('id', $userId)->delete();
            } catch (\Throwable $e) {
                CLI::write(" [FAIL] {$name} => ERROR: " . $e->getMessage(), "red");
            }
        }

        return 0;
    }
}
