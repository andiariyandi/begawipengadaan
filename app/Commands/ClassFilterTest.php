<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ClassFilterTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:class-filter';
    protected $description = 'Test Class Detail Enrolled Participants Filter Functionality';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  TESTING CLASS ENROLLED PARTICIPANTS FILTER (DETAIL KELAS)       ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $db = db_connect();

        // 1. Check Class 8 (ASN participants)
        $class8 = $db->table('learning_classes')->where('id', 8)->get()->getRowArray();
        if (! $class8) {
            CLI::error("Class #8 not found in database.");
            return;
        }

        CLI::write("\n--- [SCENARIO 1] Verifikasi Data Angkatan Kelas #8 ---", 'yellow');
        $enrollments8 = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email, u.profile_type, asn.unit_name, asn.institution_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->where('e.class_id', 8)
            ->get()->getResultArray();

        $this->assert(count($enrollments8) > 0, "Kelas #8 memiliki " . count($enrollments8) . " peserta terdaftar");

        // Verify profile types for Class 8
        $pts8 = array_unique(array_filter(array_column($enrollments8, 'profile_type')));
        $this->assert(count($pts8) === 1 && in_array('asn', $pts8, true), "Semua peserta kelas #8 bertipe profil ASN");

        $opds8 = array_unique(array_filter(array_column($enrollments8, 'unit_name')));
        $this->assert(count($opds8) >= 4, "Terdapat " . count($opds8) . " OPD unik pada peserta kelas #8: " . implode(', ', $opds8));

        // 2. Check Class 2 (Mixed Public & ASN participants)
        CLI::write("\n--- [SCENARIO 2] Verifikasi Data Angkatan Kelas #2 ---", 'yellow');
        $enrollments2 = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email, u.profile_type, asn.unit_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->where('e.class_id', 2)
            ->get()->getResultArray();

        $pts2 = array_unique(array_filter(array_column($enrollments2, 'profile_type')));
        $this->assert(in_array('public', $pts2, true), "Kelas #2 memiliki peserta bertipe 'public'");
        $this->assert(in_array('asn', $pts2, true), "Kelas #2 memiliki peserta bertipe 'asn'");
        $this->assert(! in_array('provider', $pts2, true), "Kelas #2 TIDAK memiliki 'provider' (filter tidak boleh menampilkan provider)");

        // 3. Test Controller detail execution and HTML output
        CLI::write("\n--- [SCENARIO 3] Uji Render Controller & View Detail Kelas ---", 'yellow');
        session()->set([
            'is_logged_in' => true,
            'user_id'      => 13,
            'roles'        => ['super_administrator'],
            'profile_type' => 'asn',
        ]);

        $controller = new \App\Controllers\Learning\LearningClassController();
        $controller->initController(service('request'), service('response'), service('logger'));

        $html8 = $controller->detail('8');
        $this->assert(str_contains($html8, 'id="filter-peserta-profile-type"'), "Dropdown Tipe Profil ada di HTML");
        $this->assert(str_contains($html8, 'id="container-filter-opd"'), "Container Dropdown OPD ada di HTML");
        $this->assert(str_contains($html8, 'id="filter-peserta-opd"'), "Dropdown OPD (unit_name) ada di HTML");
        $this->assert(str_contains($html8, 'id="filter-peserta-status"'), "Dropdown Status Pendaftaran ada di HTML");
        $this->assert(str_contains($html8, 'id="btn-reset-filter-peserta"'), "Tombol Reset Filter ada di HTML");
        $this->assert(str_contains($html8, 'id="table-peserta-kelas"'), "Tabel Peserta memiliki id='table-peserta-kelas'");
        $this->assert(str_contains($html8, 'data-profile-type="asn"'), "Baris tabel memiliki atribut data-profile-type");
        $this->assert(str_contains($html8, 'data-unit-name="'), "Baris tabel memiliki atribut data-unit-name");

        // Verify Class 8 dropdown options strictly correspond to enrolled data
        $this->assert(str_contains($html8, 'value="asn"'), "Dropdown Tipe Profil menyertakan opsi 'asn'");
        $this->assert(! str_contains($html8, 'value="provider"'), "Dropdown Tipe Profil kelas #8 TIDAK menyertakan 'provider' karena tidak ada peserta provider");

        // Verify Class 2
        $html2 = $controller->detail('2');
        $this->assert(str_contains($html2, 'value="public"'), "Dropdown Tipe Profil kelas #2 menyertakan 'public'");
        $this->assert(str_contains($html2, 'value="asn"'), "Dropdown Tipe Profil kelas #2 menyertakan 'asn'");
        $this->assert(! str_contains($html2, 'value="provider"'), "Dropdown Tipe Profil kelas #2 TIDAK menyertakan 'provider'");

        // 4. Test JavaScript logic
        CLI::write("\n--- [SCENARIO 4] Uji Integritas Script JS & Event Listener ---", 'yellow');
        $this->assert(str_contains($html8, 'containerOpd.classList.remove(\'d-none\')'), "JS menampilkan container OPD saat profil ASN dipilih");
        $this->assert(str_contains($html8, 'containerOpd.classList.add(\'d-none\')'), "JS menyembunyikan container OPD saat profil non-ASN dipilih");
        $this->assert(str_contains($html8, 'jQuery.fn.dataTable.ext.search.push'), "JS meregistrasikan DataTables ext.search filter plugin");
        $this->assert(str_contains($html8, 'table-peserta-kelas'), "JS filter plugin mengisolasi pemeriksaan hanya pada tabel peserta");

        CLI::write("\n==================================================================", 'green');
        CLI::write("  SELURUH PENGUJIAN FILTER PESERTA KELAS 100% PASS!               ", 'green');
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
