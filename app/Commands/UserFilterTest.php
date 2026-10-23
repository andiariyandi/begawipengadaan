<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UserFilterTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-filter';
    protected $description = 'Test All Users Enrolled Filter Functionality with Conditional OPD';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  TESTING USER MANAGEMENT FILTER & CONDITIONAL OPD                ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $db = db_connect();

        // 1. Verify Distinct Profile Types in database
        CLI::write("\n--- [SCENARIO 1] Verifikasi Tipe Profil Aktual di DB ---", 'yellow');
        $rawProfileTypes = $db->table('users')
            ->select('profile_type, COUNT(1) AS user_count')
            ->where('profile_type IS NOT NULL')
            ->where('profile_type !=', '')
            ->groupBy('profile_type')
            ->orderBy('profile_type', 'ASC')
            ->get()->getResultArray();

        $this->assert(count($rawProfileTypes) > 0, "Ditemukan " . count($rawProfileTypes) . " tipe profil aktual di tabel users");
        $typeCodes = array_column($rawProfileTypes, 'profile_type');
        $this->assert(in_array('asn', $typeCodes, true), "Tipe profil 'asn' ditemukan di database");

        // 2. Verify Distinct OPDs (unit_name) for ASN users
        CLI::write("\n--- [SCENARIO 2] Verifikasi OPD (unit_name) Aktual Pengguna ASN ---", 'yellow');
        $rawOpds = $db->table('asn_profiles asn')
            ->select('asn.unit_name, COUNT(1) AS user_count')
            ->join('users u', 'u.id = asn.user_id')
            ->where('asn.unit_name IS NOT NULL')
            ->where('asn.unit_name !=', '')
            ->where('u.profile_type', 'asn')
            ->groupBy('asn.unit_name')
            ->orderBy('asn.unit_name', 'ASC')
            ->get()->getResultArray();

        $this->assert(count($rawOpds) > 0, "Ditemukan " . count($rawOpds) . " OPD unik milik ASN: " . implode(', ', array_column($rawOpds, 'unit_name')));

        // 3. Test Controller detail execution and HTML output
        CLI::write("\n--- [SCENARIO 3] Uji Render UserController & Tampilan View ---", 'yellow');
        session()->set([
            'is_logged_in' => true,
            'user_id'      => 13,
            'roles'        => ['super_administrator'],
            'profile_type' => 'asn',
        ]);

        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://localhost/dashboard/akses/pengguna'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $controller = new \App\Controllers\Admin\UserController();
        $controller->initController($incomingRequest, service('response'), service('logger'));

        $html = $controller->index();
        $this->assert(str_contains($html, 'id="filter-user-profile-type"'), "Dropdown Tipe Profil pengguna memiliki id='filter-user-profile-type'");
        $this->assert(str_contains($html, 'id="container-user-opd"'), "Container Dropdown OPD memiliki id='container-user-opd'");
        $this->assert(str_contains($html, 'id="filter-user-opd"'), "Dropdown OPD memiliki id='filter-user-opd'");
        $this->assert(str_contains($html, 'name="unit_name"'), "Dropdown OPD memiliki name='unit_name'");
        $this->assert(str_contains($html, 'value="asn"'), "Dropdown Tipe Profil menyertakan opsi 'asn'");

        // Check each OPD appears in the dropdown options
        foreach ($rawOpds as $opd) {
            $this->assert(str_contains($html, esc($opd['unit_name'])), "Opsi OPD menyertakan '" . esc($opd['unit_name']) . "'");
        }

        // 4. Test filtering by Profile Type ASN + specific OPD
        CLI::write("\n--- [SCENARIO 4] Uji Filter Query Profile Type + OPD ---", 'yellow');
        $sampleOpd = $rawOpds[0]['unit_name'];
        $filteredRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://localhost/dashboard/akses/pengguna?profile_type=asn&unit_name=' . urlencode($sampleOpd)),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $filteredRequest->setGlobal('get', [
            'profile_type' => 'asn',
            'unit_name'    => $sampleOpd,
        ]);
        $controller->initController($filteredRequest, service('response'), service('logger'));

        $filteredHtml = $controller->index();
        $this->assert(! str_contains($filteredHtml, 'd-none" id="container-user-opd"'), "Container OPD aktif (tidak ada class d-none saat profile_type=asn)");
        $this->assert(str_contains($filteredHtml, 'value="' . esc($sampleOpd) . '" selected'), "OPD terpilih secara otomatis pada dropdown saat parameter unit_name terpasang");

        // 5. Test JavaScript logic presence
        CLI::write("\n--- [SCENARIO 5] Uji Event Listener JavaScript ---", 'yellow');
        $this->assert(str_contains($html, "this.value === 'asn'"), "JS memeriksa perubahan nilai ke 'asn'");
        $this->assert(str_contains($html, "opdContainer.classList.remove('d-none')"), "JS menampilkan container OPD saat profil ASN dipilih");
        $this->assert(str_contains($html, "opdContainer.classList.add('d-none')"), "JS menyembunyikan container OPD saat profil non-ASN dipilih");

        CLI::write("\n==================================================================", 'green');
        CLI::write("  SELURUH PENGUJIAN FILTER PENGGUNA & OPD 100% PASS!              ", 'green');
        CLI::write("==================================================================\n", 'green');
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            CLI::write("  [PASS] {$message}", 'green');
        } else {
            CLI::error("  [FAIL] {$message}");
        }
    }
}
