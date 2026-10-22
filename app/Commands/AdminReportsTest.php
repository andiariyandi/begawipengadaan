<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Admin\DashboardController;
use App\Services\Admin\ReportAnalyticsService;

class AdminReportsTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-reports';
    protected $description = 'Verifikasi Fitur Fase 2: Transformasi Pusat Laporan & Generator Ekspor Universal Data Riil';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 2: PUSAT LAPORAN & EKSPOR DATA RIIL ===", 'yellow');
        $passed = 0;
        $failed = 0;

        $service = new ReportAnalyticsService();
        $controller = new DashboardController(null, $service);

        // Mock session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 1,
            'name'       => 'Administrator Test',
            'username'   => 'admin_test',
            'roles'      => ['super_admin']
        ]);

        // 1. Uji ReportAnalyticsService::getLearningReport()
        try {
            $learningData = $service->getLearningReport();
            if (isset($learningData['active_classes'], $learningData['total_participants'], $learningData['classes'], $learningData['monthly_trend'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getLearningReport() berhasil menghitung metrik agregasi riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getLearningReport() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getLearningReport() error: " . $e->getMessage());
            $failed++;
        }

        // 2. Uji ReportAnalyticsService::getEventReport()
        try {
            $eventData = $service->getEventReport();
            if (isset($eventData['total_events'], $eventData['upcoming_events'], $eventData['total_registrations'], $eventData['events'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getEventReport() berhasil menghitung metrik event riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getEventReport() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getEventReport() error: " . $e->getMessage());
            $failed++;
        }

        // 3. Uji ReportAnalyticsService::getCertificateReport()
        try {
            $certData = $service->getCertificateReport();
            if (isset($certData['issued_certs'], $certData['active_certs'], $certData['programs'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getCertificateReport() berhasil menghitung metrik sertifikat riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getCertificateReport() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getCertificateReport() error: " . $e->getMessage());
            $failed++;
        }

        // 4. Uji ReportAnalyticsService::getCompetencyReport()
        try {
            $compData = $service->getCompetencyReport();
            if (isset($compData['mapped_personnel'], $compData['available_competencies'], $compData['gap_table'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getCompetencyReport() berhasil menghitung analisis gap OPD riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getCompetencyReport() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getCompetencyReport() error: " . $e->getMessage());
            $failed++;
        }

        // 5. Uji ReportAnalyticsService::getUserReport()
        try {
            $userData = $service->getUserReport();
            if (isset($userData['total_users'], $userData['active_users'], $userData['opd_users'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getUserReport() berhasil menghitung demografi pengguna riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getUserReport() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getUserReport() error: " . $e->getMessage());
            $failed++;
        }

        // 6. Uji ReportAnalyticsService::getExportHistory()
        try {
            $exportData = $service->getExportHistory();
            if (isset($exportData['total'], $exportData['history'])) {
                CLI::write(" [PASS] ReportAnalyticsService::getExportHistory() membaca jejak audit export riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getExportHistory() tidak memuat field yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getExportHistory() error: " . $e->getMessage());
            $failed++;
        }

        // 7. Uji Render HTML Seluruh 6 Halaman Laporan di Controller
        $reports = [
            'learningReport'    => 'Laporan Pembelajaran',
            'eventReport'       => 'Laporan Event',
            'certificateReport' => 'Laporan Sertifikat',
            'competencyReport'  => 'Laporan Kompetensi',
            'userReport'        => 'Laporan Pengguna',
            'exportHistory'     => 'Riwayat Export',
        ];

        foreach ($reports as $method => $label) {
            try {
                $html = $controller->$method();
                if (is_string($html) && strpos($html, $label) !== false && strpos($html, 'Export CSV') !== false) {
                    CLI::write(" [PASS] Render View {$label} (DashboardController::{$method}) aktif dengan tombol Export CSV & chart", 'green');
                    $passed++;
                } elseif ($method === 'exportHistory' && is_string($html) && strpos($html, 'Riwayat Export') !== false) {
                    CLI::write(" [PASS] Render View {$label} (DashboardController::{$method}) aktif dengan riwayat audit log", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] Render View {$label} tidak memuat elemen yang diharapkan");
                    $failed++;
                }
            } catch (\Throwable $e) {
                CLI::error(" [FAIL] Render {$label} gagal: " . $e->getMessage());
                $failed++;
            }
        }

        CLI::newLine();
        CLI::write("-------------------------------------------------------------", 'yellow');
        CLI::write("HASIL VERIFIKASI FASE 2: {$passed} PASSED, {$failed} FAILED", $failed === 0 ? 'green' : 'red');
        CLI::write("-------------------------------------------------------------", 'yellow');
    }
}
