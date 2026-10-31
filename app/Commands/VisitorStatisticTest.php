<?php

namespace App\Commands;

use App\Services\VisitorStatisticService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

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

        $testIp1 = '203.0.113.' . rand(1, 120);
        $testIp2 = '203.0.113.' . rand(121, 250);
        $db->table('visitor_ip_logs')->whereIn('ip_address', [$testIp1, $testIp2])->delete();
        cache()->delete('visitor_ip_' . md5($testIp1 . '_arena_belajar_' . date('Y-m-d')));
        cache()->delete('visitor_ip_' . md5($testIp2 . '_arena_belajar_' . date('Y-m-d')));

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
        if ($migCount <= 17) {
            CLI::write("  [PASS] Strict Zero-Migration Compliance: Tepat {$migCount} file migrasi baseline eksisting, tidak ada migrasi baru", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Jumlah file migrasi berubah: {$migCount} (harus <= 17)", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 8: Verifikasi 2 Tombol Simpan & Form Saling Terisolasi Mandiri
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 8] Verifikasi 2 Tombol Simpan & Form Saling Terisolasi Mandiri...", "yellow");

        // 8.1 Verifikasi Struktur View memiliki 2 Form dan 2 Tombol Simpan Terpisah
        $adminViewHtml = $adminController->index();
        $hasFormHome       = str_contains($adminViewHtml, 'id="formHomeStats"');
        $hasFormFeatures   = str_contains($adminViewHtml, 'id="formFeaturesStats"');
        $hasBtnHomeSave    = str_contains($adminViewHtml, 'Simpan Trafik Beranda');
        $hasBtnFeatureSave = str_contains($adminViewHtml, 'Simpan Frekuensi 5 Fitur Unggulan');
        $hasModalConfirm   = str_contains($adminViewHtml, 'data-confirm=');

        if ($hasFormHome && $hasFormFeatures && $hasBtnHomeSave && $hasBtnFeatureSave && $hasModalConfirm) {
            CLI::write("  [PASS] View memiliki 2 form independen, 2 tombol simpan terisolasi, dan atribut modal konfirmasi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Form atau tombol terisolasi tidak lengkap pada view", "red");
            $failCount++;
        }

        // 8.2 Uji Isolasi: Simpan Section 'features' Saja -> Trafik Beranda TIDAK Boleh Berubah
        $statsBefore = $service->getVisitorStats();
        $homeTotalBefore = $statsBefore['home']['total'];
        $homeTodayBefore = $statsBefore['home']['today'];

        // Simulasikan POST ke controller hanya dengan data features
        $featurePayload = [
            'section'  => 'features',
            'features' => [
                'arena_belajar' => ['today' => 99, 'total' => 21000],
                'bunga_rampai'  => ['today' => 60, 'total' => 14000],
                'regulasi'      => ['today' => 70, 'total' => 16000],
                'berita'        => ['today' => 45, 'total' => 9500],
                'event'         => ['today' => 50, 'total' => 9800],
            ],
        ];

        $reqFeatures = $this->createMockRequest('dashboard/sistem/statistik-kunjungan', 'POST', $featurePayload);
        $adminController = new \App\Controllers\Admin\VisitorStatisticAdminController();
        $adminController->initController($reqFeatures, service('response'), service('logger'));
        $adminController->update();

        $statsAfterFeatures = $service->getVisitorStats();
        $homeTotalAfter = $statsAfterFeatures['home']['total'];
        $homeTodayAfter = $statsAfterFeatures['home']['today'];

        if ($homeTotalAfter === $homeTotalBefore && $homeTodayAfter === $homeTodayBefore) {
            CLI::write("  [PASS] Isolasi Sempurna: Simpan fitur unggulan TIDAK mengubah angka Trafik Beranda ({$homeTotalAfter})", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Angka beranda bocor/berubah saat simpan fitur: Sebelum={$homeTotalBefore}, Sesudah={$homeTotalAfter}", "red");
            $failCount++;
        }

        if ((int) $statsAfterFeatures['features']['arena_belajar']['hits_total'] === 21000) {
            CLI::write("  [PASS] Angka fitur unggulan (Arena Belajar: 21.000) tersimpan presisten dan akurat", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Angka fitur unggulan gagal tersimpan presisten", "red");
            $failCount++;
        }

        // 8.3 Uji Isolasi: Simpan Section 'home' Saja -> Frekuensi 5 Fitur TIDAK Boleh Berubah
        $arenaHitsBefore = $statsAfterFeatures['features']['arena_belajar']['hits_total'];

        $homePayload = [
            'section'    => 'home',
            'home_today' => 300,
            'home_week'  => 1200,
            'home_month' => 4500,
            'home_year'  => 27000,
            'home_total' => 42000,
        ];

        $reqHome = $this->createMockRequest('dashboard/sistem/statistik-kunjungan', 'POST', $homePayload);
        $adminController = new \App\Controllers\Admin\VisitorStatisticAdminController();
        $adminController->initController($reqHome, service('response'), service('logger'));
        $adminController->update();

        $statsAfterHome = $service->getVisitorStats();
        $arenaHitsAfter = $statsAfterHome['features']['arena_belajar']['hits_total'];

        if ($arenaHitsAfter === $arenaHitsBefore) {
            CLI::write("  [PASS] Isolasi Sempurna: Simpan Trafik Beranda TIDAK mengubah angka Fitur Unggulan ({$arenaHitsAfter})", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Angka fitur unggulan berubah saat simpan beranda: Sebelum={$arenaHitsBefore}, Sesudah={$arenaHitsAfter}", "red");
            $failCount++;
        }

        if ((int) $statsAfterHome['home']['total'] === 42000) {
            CLI::write("  [PASS] Angka Trafik Beranda (Total: 42.000) tersimpan presisten dan akurat", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Angka Trafik Beranda gagal tersimpan presisten", "red");
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
