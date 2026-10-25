<?php

namespace App\Commands;

use App\Controllers\Admin\Survey\SatisfactionSurveyAdminController;
use App\Controllers\Landing\HomeController;
use App\Models\SatisfactionSurveyModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SurveyAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'survey:admin:test';
    protected $description = 'Comprehensive test suite for Satisfaction Survey (IKM) system across Landing, Workspace & Dashboard';

    public function run(array $params): int
    {
        CLI::write("=== MENJALANKAN TEST SUITE SISTEM SURVEI KEPUASAN (IKM) ===", "yellow");
        $passCount = 0;
        $failCount = 0;

        // -------------------------------------------------------------
        // TEST 1: SatisfactionSurveyAdminController::index Render & PermenPAN-RB Table
        // -------------------------------------------------------------
        CLI::write("\n[1/6] Menguji Render Dashboard Admin & Tabel Unsur PermenPAN-RB...", "cyan");
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), '');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        $adminController = new SatisfactionSurveyAdminController();
        $adminController->initController($incomingRequest, service('response'), service('logger'));

        $html = $adminController->index();
        if (is_string($html) && strlen($html) > 0) {
            $hasPanrb = str_contains($html, 'PermenPAN-RB No. 14 / 2017') || str_contains($html, 'PermenPAN-RB No. 14 Tahun 2017');
            $hasElements = str_contains($html, 'NRR Tertimbang') && str_contains($html, 'U1') && str_contains($html, 'U2');
            $hasExportBtn = str_contains($html, 'Unduh CSV Responden') || str_contains($html, 'dashboard/survei-kepuasan/export');
            $hasDetailModal = str_contains($html, 'modalDetailResp');

            if ($hasPanrb && $hasElements && $hasExportBtn && $hasDetailModal) {
                CLI::write("  [PASS] Dashboard admin merender tabel PermenPAN-RB, tombol CSV, dan modal detail dengan sempurna (" . strlen($html) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::error("  [FAIL] Dashboard admin tidak memuat elemen PermenPAN-RB lengkap.");
                $failCount++;
            }
        } else {
            CLI::error("  [FAIL] Dashboard index gagal merender HTML.");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 2: SatisfactionSurveyModel PermenPAN-RB Harmonized Calculation
        // -------------------------------------------------------------
        CLI::write("\n[2/6] Menguji Model Kalkulasi IKM PermenPAN-RB No. 14/2017...", "cyan");
        $surveyModel = new SatisfactionSurveyModel();
        $analytics = $surveyModel->getPublicSurveyResults();

        if (isset($analytics['total_responses'], $analytics['avg_overall'], $analytics['score_pct'], $analytics['predicate'])) {
            $predicateGrade = $analytics['predicate']['grade'] ?? '';
            CLI::write("  [PASS] SatisfactionSurveyModel::getPublicSurveyResults() valid (Total: {$analytics['total_responses']}, Skor: {$analytics['score_pct']}, Mutu: {$predicateGrade})", "green");
            $passCount++;
        } else {
            CLI::error("  [FAIL] Format analitik SatisfactionSurveyModel tidak lengkap.");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 3: Anti-Spam Honeypot Verification on HomeController::submitSurvey
        // -------------------------------------------------------------
        CLI::write("\n[3/6] Menguji Penolakan Bot via Honeypot Field...", "cyan");
        session()->set('isLoggedIn', false);

        $honeypotPost = [
            'rating_overall'            => 5,
            'service_access_rating'     => 5,
            'service_regulation_rating' => 5,
            'service_learning_rating'   => 5,
            'service_helpdesk_rating'   => 5,
            'respondent_role'           => 'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)',
            'website_url'               => 'http://spambot-trap.com', // Honeypot filled!
            'feedback'                  => 'Test honeypot bot trap',
        ];

        $_POST = $honeypotPost;
        $reqHoneypot = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqHoneypot->setMethod('POST');
        $reqHoneypot->setHeader('X-Requested-With', 'XMLHttpRequest');
        $reqHoneypot->setHeader('Accept', 'application/json');
        $reqHoneypot->setGlobal('server', ['REMOTE_ADDR' => '127.0.0.3']);
        $reqHoneypot->setGlobal('post', $honeypotPost);
        $reqHoneypot->setGlobal('request', $honeypotPost);
        \Config\Services::injectMock('request', $reqHoneypot);

        $homeController = new HomeController();
        $homeController->initController($reqHoneypot, service('response')->setStatusCode(200), service('logger'));

        $resHoneypot = $homeController->submitSurvey();
        $statusHoneypot = $resHoneypot->getStatusCode();
        $bodyHoneypot = json_decode($resHoneypot->getBody(), true);

        if ($statusHoneypot === 422 && ($bodyHoneypot['success'] ?? true) === false) {
            CLI::write("  [PASS] Bot dengan isian honeypot berhasil ditolak (HTTP 422: {$bodyHoneypot['message']})", "green");
            $passCount++;
        } else {
            CLI::error("  [FAIL] Honeypot gagal menolak spam bot. Status: {$statusHoneypot}");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: Anti-Spam Simple Math Captcha Verification
        // -------------------------------------------------------------
        CLI::write("\n[4/6] Menguji Verifikasi Simple Math Captcha...", "cyan");
        session()->set('isLoggedIn', false);
        session()->set('survey_captcha_answer', 14); // Expected: 14

        // A. Wrong Answer (10 instead of 14)
        $wrongCaptchaPost = [
            'rating_overall'            => 5,
            'service_access_rating'     => 5,
            'service_regulation_rating' => 5,
            'service_learning_rating'   => 5,
            'service_helpdesk_rating'   => 5,
            'respondent_role'           => 'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)',
            'website_url'               => '', // Clean honeypot
            'captcha_answer'            => 10, // WRONG!
            'feedback'                  => 'Test wrong captcha',
        ];

        $_POST = $wrongCaptchaPost;
        $reqWrong = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqWrong->setMethod('POST');
        $reqWrong->setHeader('X-Requested-With', 'XMLHttpRequest');
        $reqWrong->setHeader('Accept', 'application/json');
        $reqWrong->setGlobal('server', ['REMOTE_ADDR' => '127.0.0.4']);
        $reqWrong->setGlobal('post', $wrongCaptchaPost);
        $reqWrong->setGlobal('request', $wrongCaptchaPost);
        \Config\Services::injectMock('request', $reqWrong);

        $homeController = new HomeController();
        $homeController->initController($reqWrong, service('response')->setStatusCode(200), service('logger'));

        $resWrong = $homeController->submitSurvey();
        $statusWrong = $resWrong->getStatusCode();
        $bodyWrong = json_decode($resWrong->getBody(), true);

        if ($statusWrong === 422 && str_contains($bodyWrong['message'] ?? '', 'captcha')) {
            CLI::write("  [PASS] Jawaban captcha salah berhasil ditolak (HTTP 422: {$bodyWrong['message']})", "green");
            $passCount++;
        } else {
            CLI::error("  [FAIL] Captcha salah tidak ditolak. Status: {$statusWrong}, Msg: " . ($bodyWrong['message'] ?? ''));
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 5: Valid Survey Submission & Database Insertion
        // -------------------------------------------------------------
        CLI::write("\n[5/6] Menguji Pengiriman Survei Valid & Pencatatan Database...", "cyan");
        session()->set('isLoggedIn', false);
        session()->set('survey_captcha_answer', 15); // Expected: 15

        $validPost = [
            'rating_overall'            => 5,
            'service_access_rating'     => 5,
            'service_regulation_rating' => 5,
            'service_learning_rating'   => 5,
            'service_helpdesk_rating'   => 5,
            'respondent_role'           => 'Penyedia Barang / Jasa (Vendor / Rekanan / UMKM)',
            'is_anonymous'              => '0',
            'respondent_name'           => 'PT Uji Otomasi PBJ Lampung',
            'respondent_institution'    => 'Vendor Ekosistem Pengadaan',
            'respondent_contact'        => '081234567890',
            'website_url'               => '',
            'captcha_answer'            => 15, // CORRECT!
            'feedback'                  => 'Sistem survei kepuasan berjalan sangat responsif dan aman.',
            'recommendation'            => 'Pasti Merekomendasikan',
        ];

        $_POST = $validPost;
        $reqValid = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqValid->setMethod('POST');
        $reqValid->setHeader('X-Requested-With', 'XMLHttpRequest');
        $reqValid->setHeader('Accept', 'application/json');
        $reqValid->setGlobal('server', ['REMOTE_ADDR' => '127.0.0.5']);
        $reqValid->setGlobal('post', $validPost);
        $reqValid->setGlobal('request', $validPost);
        \Config\Services::injectMock('request', $reqValid);

        $homeController = new HomeController();
        $homeController->initController($reqValid, service('response')->setStatusCode(200), service('logger'));

        $resValid = $homeController->submitSurvey();
        $statusValid = $resValid->getStatusCode();
        $bodyValid = json_decode($resValid->getBody(), true);

        $insertedTicket = $bodyValid['ticket_number'] ?? null;
        if ($statusValid === 200 && ($bodyValid['success'] ?? false) === true && str_starts_with((string) $insertedTicket, 'IKM-')) {
            $db = \Config\Database::connect();
            $dbRow = $db->table('satisfaction_surveys')->where('ticket_number', $insertedTicket)->get()->getRowArray();
            if ($dbRow && $dbRow['respondent_name'] === 'PT Uji Otomasi PBJ Lampung') {
                CLI::write("  [PASS] Survei berhasil tercatat di database dengan No Tiket: {$insertedTicket}", "green");
                $passCount++;

                // Clean up test row
                $db->table('satisfaction_surveys')->where('ticket_number', $insertedTicket)->delete();
            } else {
                CLI::error("  [FAIL] Data survei tidak ditemukan di tabel satisfaction_surveys.");
                $failCount++;
            }
        } else {
            CLI::error("  [FAIL] Pengiriman survei valid gagal. Status: {$statusValid}, Response: " . json_encode($bodyValid));
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 6: Render Enhanced Landing Page /statistik/survei-kepuasan
        // -------------------------------------------------------------
        CLI::write("\n[6/7] Menguji Render Halaman Publik /statistik/survei-kepuasan...", "cyan");
        try {
            $publicCtrl = new \App\Controllers\Landing\StatisticPublicController();
            $publicCtrl->initController(service('request'), service('response'), service('logger'));
            $publicHtml = $publicCtrl->satisfactionSurvey();

            $hasPublicPanrbTable = str_contains($publicHtml, 'tabel-panrb');
            $hasApexCharts       = str_contains($publicHtml, 'chartQuarterlyTrend') && str_contains($publicHtml, 'chartAspectRadar');
            $hasBenchmarks       = str_contains($publicHtml, 'Matriks Klasifikasi &amp; Interpretasi Mutu IKM');

            if ($hasPublicPanrbTable && $hasApexCharts && $hasBenchmarks) {
                CLI::write("  [PASS] Halaman publik /statistik/survei-kepuasan merender tabel PermenPAN-RB, matriks standar, dan ApexCharts interaktif (" . strlen($publicHtml) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::error("  [FAIL] Komponen tabel PermenPAN-RB / ApexCharts tidak lengkap pada halaman publik.");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::error("  [FAIL] Terjadi exception saat render /statistik/survei-kepuasan: " . $e->getMessage());
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 7: Zero Migration Compliance Verification
        // -------------------------------------------------------------
        CLI::write("\n[7/7] Memverifikasi Kepatuhan Zero-Migration (16 Migrasi Intact)...", "cyan");
        $runner = service('migrations');
        $history = $runner->findMigrations();
        $migrationCount = count($history);

        if ($migrationCount === 16) {
            CLI::write("  [PASS] Tepat 16 berkas migrasi database eksisting terjaga utuh tanpa migrasi baru.", "green");
            $passCount++;
        } else {
            CLI::error("  [FAIL] Jumlah migrasi tidak sesuai (terdapat {$migrationCount} berkas, wajib 16).");
            $failCount++;
        }

        CLI::write("\n=======================================================", "yellow");
        CLI::write("HASIL PENGUJIAN: {$passCount} LULUS / " . ($passCount + $failCount) . " TOTAL", $failCount === 0 ? "green" : "red");
        CLI::write("=======================================================", "yellow");

        return $failCount === 0 ? 0 : 1;
    }
}
