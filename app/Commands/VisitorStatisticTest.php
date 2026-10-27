<?php

namespace App\Commands;

use App\Services\VisitorStatisticService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VisitorStatisticTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:visitor-statistics';
    protected $description = 'Verifikasi Fitur Manajemen Statistik Kunjungan & Tracker Berbasis IP Client';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("   UJI SISTEM STATISTIK KUNJUNGAN & TRACKER BERBASIS IP CLIENT    ", "yellow");
        CLI::write("==================================================================", "yellow");

        $passCount = 0;
        $failCount = 0;
        $service   = new VisitorStatisticService();

        // -------------------------------------------------------------
        // TEST 1: Verifikasi Tabel visitor_ip_logs & Schema
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 1] Verifikasi Ketersediaan Tabel visitor_ip_logs...", "yellow");
        $db = db_connect();
        if ($db->tableExists('visitor_ip_logs')) {
            CLI::write("  [PASS] Tabel visitor_ip_logs berhasil dibuat otomatis tanpa migrasi baru", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Tabel visitor_ip_logs tidak ditemukan di database", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 2: Verifikasi Logika Perekaman Berbasis IP Client
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 2] Verifikasi Deduplikasi Kunjungan Berbasis IP Client (Unique Daily IP)...", "yellow");

        $testIp1 = '203.0.113.' . rand(10, 99);
        $testIp2 = '203.0.113.' . rand(100, 200);

        // Hit pertama dari IP testIp1 harus tercatat dan menambah counter
        $recorded1 = $service->recordHit('arena_belajar', $testIp1, 'PHPUnit-CLI-Test');
        if ($recorded1 === true) {
            CLI::write("  [PASS] Kunjungan pertama dari IP {$testIp1} sukses tercatat (1 hit baru)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Kunjungan pertama gagal tercatat", "red");
            $failCount++;
        }

        // Hit kedua dari IP yang sama pada hari yang sama HARUS ditolak/diabaikan
        $recorded2 = $service->recordHit('arena_belajar', $testIp1, 'PHPUnit-CLI-Test');
        if ($recorded2 === false) {
            CLI::write("  [PASS] Kunjungan kedua dari IP {$testIp1} berhasil difilter/diabaikan (Anti-Duplicate)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Kunjungan duplikat tidak terfilter!", "red");
            $failCount++;
        }

        // Hit dari IP berbeda (testIp2) HARUS dihitung sebagai kunjungan baru
        $recorded3 = $service->recordHit('arena_belajar', $testIp2, 'PHPUnit-CLI-Test');
        if ($recorded3 === true) {
            CLI::write("  [PASS] Kunjungan dari IP berbeda {$testIp2} berhasil tercatat secara independen", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Kunjungan dari IP berbeda gagal tercatat", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 3: Verifikasi Fitur Update Statistik dari Admin Form
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 3] Verifikasi Pembaruan Angka Statistik dari Dashboard Admin...", "yellow");

        $targetHomeToday = 275;
        $targetHomeWeek  = 1150;
        $targetHomeMonth = 4300;
        $targetHomeYear  = 26500;
        $targetHomeTotal = 41200;

        $targetFeatures = [
            'arena_belajar' => ['today' => 88, 'total' => 19500],
            'bunga_rampai'  => ['today' => 52, 'total' => 13400],
            'regulasi'      => ['today' => 64, 'total' => 15800],
            'berita'        => ['today' => 37, 'total' => 9200],
            'event'         => ['today' => 41, 'total' => 9100],
        ];

        $updateSuccess = $service->updateStats([
            'home_today' => $targetHomeToday,
            'home_week'  => $targetHomeWeek,
            'home_month' => $targetHomeMonth,
            'home_year'  => $targetHomeYear,
            'home_total' => $targetHomeTotal,
            'features'   => $targetFeatures,
        ]);

        if ($updateSuccess) {
            CLI::write("  [PASS] Method updateStats() berhasil mengeksekusi penyesuaian data database", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Method updateStats() gagal dieksekusi", "red");
            $failCount++;
        }

        // Verifikasi hasil getVisitorStats()
        $freshStats = $service->getVisitorStats();

        if ($freshStats['home']['today'] === $targetHomeToday) {
            CLI::write("  [PASS] Trafik Beranda Hari Ini presisi: {$freshStats['home']['today']}", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Trafik Hari Ini mismatch. Ekspektasi: {$targetHomeToday}, Real: {$freshStats['home']['today']}", "red");
            $failCount++;
        }

        if ($freshStats['home']['this_week'] === $targetHomeWeek) {
            CLI::write("  [PASS] Trafik Beranda Minggu Ini presisi: {$freshStats['home']['this_week']}", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Trafik Minggu Ini mismatch. Ekspektasi: {$targetHomeWeek}, Real: {$freshStats['home']['this_week']}", "red");
            $failCount++;
        }

        if ($freshStats['home']['this_month'] === $targetHomeMonth) {
            CLI::write("  [PASS] Trafik Beranda Bulan Ini presisi: {$freshStats['home']['this_month']}", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Trafik Bulan Ini mismatch. Ekspektasi: {$targetHomeMonth}, Real: {$freshStats['home']['this_month']}", "red");
            $failCount++;
        }

        if ($freshStats['home']['this_year'] === $targetHomeYear) {
            CLI::write("  [PASS] Trafik Beranda Tahun Ini presisi: {$freshStats['home']['this_year']}", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Trafik Tahun Ini mismatch. Ekspektasi: {$targetHomeYear}, Real: {$freshStats['home']['this_year']}", "red");
            $failCount++;
        }

        if ($freshStats['home']['total'] === $targetHomeTotal) {
            CLI::write("  [PASS] Total Akumulasi Pembukaan presisi: {$freshStats['home']['total']}", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Total Akumulasi mismatch. Ekspektasi: {$targetHomeTotal}, Real: {$freshStats['home']['total']}", "red");
            $failCount++;
        }

        // Verifikasi fitur
        $allFeaturesMatch = true;
        foreach ($targetFeatures as $k => $vals) {
            $fToday = $freshStats['features'][$k]['hits_today'] ?? 0;
            $fTotal = $freshStats['features'][$k]['hits_total'] ?? 0;
            if ($fToday !== $vals['today'] || $fTotal !== $vals['total']) {
                $allFeaturesMatch = false;
                CLI::write("  [FAIL] Modul {$k} mismatch. Today: {$fToday} (Exp {$vals['today']}), Total: {$fTotal} (Exp {$vals['total']})", "red");
                break;
            }
        }
        if ($allFeaturesMatch) {
            CLI::write("  [PASS] Seluruh 5 fitur unggulan (Arena, Bunga Rampai, Regulasi, Berita, Event) sinkron 1:1", "green");
            $passCount++;
        } else {
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: Verifikasi Landing Page Render
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 4] Verifikasi Output Tampilan Landing Page Home...", "yellow");
        $homeController = new \App\Controllers\Landing\HomeController();
        $homeController->initController(service('request'), service('response'), service('logger'));
        $homeHtml = $homeController->index();

        if (str_contains($homeHtml, 'id="visitor-stats"') && str_contains($homeHtml, 'Statistik Trafik &amp; Kunjungan Portal')) {
            CLI::write("  [PASS] Section 'Statistik Trafik & Kunjungan Portal' ter-render dengan id='visitor-stats'", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Section statistik tidak ditemukan pada output HTML home", "red");
            $failCount++;
        }

        $latestHomeStats = $service->getVisitorStats();
        $expectedTotalFormatted = number_format($latestHomeStats['home']['total']);
        if (str_contains($homeHtml, $expectedTotalFormatted)) {
            CLI::write("  [PASS] Angka akumulasi terkini ({$expectedTotalFormatted}) tampil presisi di kartu landing page", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Angka akumulasi ({$expectedTotalFormatted}) tidak muncul di kartu landing page", "red");
            $failCount++;
        }

        if (str_contains($homeHtml, 'Unique Daily IP Visits')) {
            CLI::write("  [PASS] Keterangan perhitungan IP Client unik harian tertera di footer kartu", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Keterangan IP Client tidak ditemukan", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 5: Verifikasi Rendering View Dashboard Admin & Submenu
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 5] Verifikasi View Admin Dashboard (/dashboard/sistem/statistik-kunjungan)...", "yellow");

        // Simulate admin session
        session()->set([
            'isLoggedIn' => true,
            'user'       => [
                'id'       => 14,
                'role'     => 'super_administrator',
                'username' => 'admin',
                'email'    => 'admin@begawipbj.go.id',
            ],
            'roles'      => ['super_administrator', 'administrator'],
        ]);

        $adminController = new \App\Controllers\Admin\VisitorStatisticAdminController();
        $adminController->initController(service('request'), service('response'), service('logger'));
        $adminViewHtml = $adminController->index();

        if (is_string($adminViewHtml) && str_contains($adminViewHtml, 'Statistik Trafik &amp; Kunjungan Portal')) {
            CLI::write("  [PASS] Halaman form manajemen statistik sukses ter-render (HTTP 200)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Halaman form manajemen statistik gagal ter-render", "red");
            $failCount++;
        }

        if (is_string($adminViewHtml) && str_contains($adminViewHtml, 'dashboard/sistem/statistik-kunjungan')) {
            CLI::write("  [PASS] Action form submit dan sub-menu terdaftar pada template admin", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] URL action form tidak ditemukan", "red");
            $failCount++;
        }

        if (is_string($adminViewHtml) && str_contains($adminViewHtml, 'Audit Log Riwayat Kunjungan IP Client Terkini')) {
            CLI::write("  [PASS] Tabel audit log riwayat IP terpasang lengkap di dashboard", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Tabel audit log IP tidak ditemukan", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 6: Verifikasi Opsi Reset Data Pengunjung (Hanya Bulan Ini)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 6] Verifikasi Opsi Reset Data: Hanya Ambil Data Bulan Ini...", "yellow");

        $prevMonthDate = date('Y-m-d', strtotime('-45 days'));
        $monthStart    = date('Y-m-01');

        // Sisipkan row dummy di bulan sebelumnya
        $db->table('visitor_statistics')->ignore(true)->insert([
            'feature'    => 'home',
            'visit_date' => $prevMonthDate,
            'hit_count'  => 500,
            'created_at' => $prevMonthDate . ' 10:00:00',
            'updated_at' => $prevMonthDate . ' 10:00:00',
        ]);

        $db->table('visitor_ip_logs')->ignore(true)->insert([
            'ip_address' => '198.51.100.99',
            'feature'    => 'home',
            'visit_date' => $prevMonthDate,
            'user_agent' => 'Old-Month-Agent',
            'created_at' => $prevMonthDate . ' 10:00:00',
        ]);

        // Eksekusi reset: Hanya ambil data bulan ini (hapus sebelum bulan berjalan)
        $resetResult = $service->resetVisitorData('before_this_month', 'all');

        if ($resetResult['success']) {
            CLI::write("  [PASS] Eksekusi resetVisitorData('before_this_month') berhasil", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Eksekusi resetVisitorData() gagal", "red");
            $failCount++;
        }

        // Pastikan tidak ada data yang memiliki visit_date < monthStart
        $remainingOldStats = $db->table('visitor_statistics')->where('visit_date <', $monthStart)->countAllResults();
        $remainingOldIps   = $db->table('visitor_ip_logs')->where('visit_date <', $monthStart)->countAllResults();

        if ($remainingOldStats === 0 && $remainingOldIps === 0) {
            CLI::write("  [PASS] Seluruh data sebelum tanggal {$monthStart} berhasil dihapus bersih (0 row tersisa)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Masih terdapat data sebelum bulan ini! Stats: {$remainingOldStats}, IP: {$remainingOldIps}", "red");
            $failCount++;
        }

        // Pastikan data bulan berjalan tetap utuh
        $currentMonthStats = $db->table('visitor_statistics')->where('visit_date >=', $monthStart)->countAllResults();
        if ($currentMonthStats > 0) {
            CLI::write("  [PASS] Data bulan berjalan tetap tersimpan utuh ({$currentMonthStats} records aktif)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Data bulan berjalan ikut terhapus!", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 7: Verifikasi Strict Zero-Migration Compliance
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 7] Verifikasi Kepatuhan Zero-Migration...", "yellow");
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migCount       = count($migrationFiles);
        if ($migCount === 16) {
            CLI::write("  [PASS] Strict Zero-Migration Compliance: Tepat 16 file migrasi, tidak ada migrasi baru", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Jumlah file migrasi berubah: {$migCount} (harus tepat 16)", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // HASIL AKHIR
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "yellow");
        CLI::write("                     HASIL PENGUJIAN AKHIR                        ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");
        if ($failCount === 0) {
            CLI::write("STATUS: SELURUH FITUR STATISTIK KUNJUNGAN & TRACKER IP LULUS 100%!", "green");
        }
        CLI::write("==================================================================", "yellow");

        return $failCount === 0 ? 0 : 1;
    }
}
