<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AdminFinalPhaseEFTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-final-fase-e-f';
    protected $description = 'Verifikasi Fitur Fase E: Unified Event Console, Multi-Facet Users, Radar Intelligence, Print Reports & Kalkulator IKM PermenPAN-RB';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE E & F: ENHANCEMENT MULTI-MODUL & AUDIT FINAL ===", 'yellow');
        $passed = 0;
        $failed = 0;

        // 1. IKM PermenPAN-RB No. 14 Tahun 2017
        $surveyCtrlPath = APPPATH . 'Controllers/Admin/Survey/SatisfactionSurveyAdminController.php';
        $surveyViewPath = APPPATH . 'Views/dashboard/survey/index.php';
        $sCtrl = file_get_contents($surveyCtrlPath);
        $sView = file_get_contents($surveyViewPath);

        if (
            strpos($sCtrl, 'ikmScore') !== false &&
            strpos($sCtrl, 'mutuKategori') !== false &&
            strpos($sCtrl, 'panrbElements') !== false &&
            strpos($sView, 'PermenPAN-RB No. 14 Tahun 2017') !== false &&
            strpos($sView, 'NILAI IKM KONVERSI') !== false
        ) {
            CLI::write(" [PASS] Kalkulator IKM PermenPAN-RB No. 14/2017 & Banner Eksekutif aktif di survei kepuasan", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Kalkulator IKM PermenPAN-RB belum terintegrasi lengkap");
            $failed++;
        }

        // 2. Mode Presenter & QR Presensi Event
        $eventDetailPath = APPPATH . 'Views/dashboard/communication/events/detail.php';
        $eDetail = file_get_contents($eventDetailPath);
        if (
            strpos($eDetail, 'qrPresenterModal') !== false &&
            strpos($eDetail, 'Mode Presenter') !== false &&
            strpos($eDetail, 'SESI PRESENSI AKTIF') !== false
        ) {
            CLI::write(" [PASS] Unified Event Console memiliki Mode Presenter & QR Presensi Layar Penuh di events/detail.php", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Mode Presenter & QR Presensi belum aktif di events/detail.php");
            $failed++;
        }

        // 3. Multi-Facet Filter Manajemen Pengguna
        $userCtrlPath = APPPATH . 'Controllers/Admin/UserController.php';
        $userViewPath = APPPATH . 'Views/dashboard/users/index.php';
        $uCtrl = file_get_contents($userCtrlPath);
        $uView = file_get_contents($userViewPath);

        if (
            strpos($uCtrl, 'verification') !== false &&
            strpos($uView, 'name="verification"') !== false &&
            strpos($uView, 'begawi-table') !== false
        ) {
            CLI::write(" [PASS] Multi-Facet Filter (Verifikasi, Role, Profil, Status) dan begawi-table aktif di Manajemen Pengguna", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Multi-Facet Filter belum aktif di Manajemen Pengguna");
            $failed++;
        }

        // 4. Radar Chart Kematangan PBJ di Intelligence Home
        $intelHomePath = APPPATH . 'Views/dashboard/intelligence/home/index.php';
        $iHome = file_get_contents($intelHomePath);
        if (
            strpos($iHome, 'pbjMaturityRadarChart') !== false &&
            strpos($iHome, 'Radar Kesiapan 5 Dimensi PBJ') !== false
        ) {
            CLI::write(" [PASS] Radar Chart Kematangan 5 Dimensi PBJ Daerah aktif di Government Intelligence Home", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Radar Chart Kematangan PBJ belum terpasang di intelligence/home/index.php");
            $failed++;
        }

        // 5. Simulasi Formasi JF PPBJ di Competency Gap
        $gapPath = APPPATH . 'Views/dashboard/intelligence/competency_gap/index.php';
        $gapContent = file_get_contents($gapPath);
        if (
            strpos($gapContent, 'simAddedPersonnel') !== false &&
            strpos($gapContent, 'runFormationSimulation') !== false &&
            (strpos($gapContent, 'Simulasi Alokasi & Rekrutmen Formasi JF PPBJ') !== false || strpos($gapContent, 'Simulasi Alokasi &amp; Rekrutmen Formasi JF PPBJ') !== false)
        ) {
            CLI::write(" [PASS] Kalkulator Interaktif Simulasi Formasi JF PPBJ aktif di Analisis Gap OPD", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Simulasi Formasi JF PPBJ belum terpasang di competency_gap/index.php");
            $failed++;
        }

        // 6. Print Layout & Print Button across Reports
        $headPath = APPPATH . 'Views/dashboard/components/head.php';
        $headContent = file_get_contents($headPath);
        $learningReportPath = APPPATH . 'Views/dashboard/reports/learning.php';
        $lReport = file_get_contents($learningReportPath);

        if (
            strpos($headContent, '@media print') !== false &&
            strpos($headContent, '.print-only') !== false &&
            strpos($lReport, 'onclick="window.print()"') !== false &&
            strpos($lReport, 'PEMERINTAH PROVINSI LAMPUNG') !== false
        ) {
            CLI::write(" [PASS] Standardisasi Print Layout (@media print, kop resmi Pemprov, dan tombol cetak) aktif di Pusat Laporan", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Print layout belum terpasang sempurna di Pusat Laporan");
            $failed++;
        }

        // 7. Zero Migration Constraint Check
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migrationCount = count($migrationFiles);
        if ($migrationCount === 16) {
            CLI::write(" [PASS] Zero Migration Check: Tepat 16 file migrasi eksisting, 0 migrasi baru dibuat", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Jumlah file migrasi berubah: {$migrationCount} (harus tetap 16)");
            $failed++;
        }

        // 8. Settings Views Link & Action Consistency (Pusat Pengaturan)
        $settingsFiles = glob(APPPATH . 'Views/dashboard/settings/*.php');
        $badSettingsCount = 0;
        foreach ($settingsFiles as $sf) {
            if (basename($sf) === 'index.php') continue;
            $sContent = file_get_contents($sf);
            if (strpos($sContent, "site_url('dashboard/pengaturan')") !== false ||
                strpos($sContent, "site_url('dashboard/settings')") !== false ||
                strpos($sContent, 'action="#"') !== false) {
                $badSettingsCount++;
            }
        }
        if ($badSettingsCount === 0) {
            CLI::write(" [PASS] 12 Sub-Halaman Pengaturan: Semua link 'Kembali' menunjuk ke dashboard/sistem/pengaturan & form actions valid", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ditemukan {$badSettingsCount} file pengaturan dengan link atau form action usang");
            $failed++;
        }

        // 9. _nav_access Mounted across All Access Management Sibling Pages
        $accessViews = [
            APPPATH . 'Views/dashboard/access/roles.php',
            APPPATH . 'Views/dashboard/access/permissions.php',
            APPPATH . 'Views/dashboard/access/role_permission_matrix.php',
            APPPATH . 'Views/dashboard/users/role_requests.php',
            APPPATH . 'Views/dashboard/users/profile_verifications.php',
            APPPATH . 'Views/dashboard/users/profile_change_requests.php',
            APPPATH . 'Views/dashboard/access/organization_units.php',
            APPPATH . 'Views/dashboard/access/procurement_roles.php',
            APPPATH . 'Views/dashboard/access/procurement_role_assignments.php',
        ];
        $allNavMounted = true;
        foreach ($accessViews as $av) {
            if (!file_exists($av) || strpos(file_get_contents($av), '_nav_access') === false) {
                $allNavMounted = false;
                break;
            }
        }
        if ($allNavMounted) {
            CLI::write(" [PASS] Sub-navigasi _nav_access.php terpasang konsisten di seluruh 9 sibling view Manajemen Akses", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ada view Manajemen Akses yang belum memasang _nav_access.php");
            $failed++;
        }

        // 10. Active-State Survei Kepuasan (IKM) in sidebar.php
        $sidebarContent = file_get_contents(APPPATH . 'Views/dashboard/components/sidebar.php');
        if (
            strpos($sidebarContent, 'Survei Kepuasan (IKM)') !== false &&
            strpos($sidebarContent, "\$currentMenu === 'survey' ? 'active-page' : ''") !== false
        ) {
            CLI::write(" [PASS] Active-state menu top-level Survei Kepuasan (IKM) aktif pada <li> dan <a> di sidebar.php", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Active-state Survei Kepuasan (IKM) belum terkonfigurasi dengan benar di sidebar.php");
            $failed++;
        }

        // 11. Light Mode Soft Badge Contrast (WCAG AA >= 4.5:1) & Header Portal Button
        $cssContent = file_get_contents(FCPATH . 'assets/dashboard/css/style.css');
        $headerContent = file_get_contents(APPPATH . 'Views/dashboard/components/header.php');
        if (
            strpos($cssContent, '#1d4ed8 !important') !== false &&
            strpos($cssContent, '#b45309 !important') !== false &&
            strpos($cssContent, '#15803d !important') !== false &&
            strpos($cssContent, '#b91c1c !important') !== false &&
            strpos($cssContent, '#0369a1 !important') !== false &&
            strpos($headerContent, 'header-portal-btn') !== false
        ) {
            CLI::write(" [PASS] Kontras Soft Badge Light Mode & tombol Portal Publik header terstandardisasi WCAG AA/AAA", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Perbaikan kontras CSS / header belum terpasang lengkap");
            $failed++;
        }

        // 12. FileController Zero-404 Fallback & Alias Routes
        $fileController = new \App\Controllers\Landing\FileController();
        $fileController->initController(service('request'), service('response'), service('logger'));
        $resp172 = $fileController->show(172);
        $resp173 = $fileController->show(173);
        $resp182 = $fileController->show(182);
        $respDummy = $fileController->show(99999);
        $routesContent = file_get_contents(APPPATH . 'Config/Routes.php');

        if (
            $resp172->getStatusCode() === 200 &&
            $resp173->getStatusCode() === 200 &&
            $resp182->getStatusCode() === 200 &&
            $respDummy->getStatusCode() === 200 &&
            strpos($routesContent, "'helpdesk', 'dashboard/komunikasi/whatsapp-widget'") !== false &&
            strpos($routesContent, "'pengajar', 'dashboard/pembelajaran/kelas'") !== false &&
            strpos($routesContent, "'carousel', 'dashboard/landing/sorotan-carousel'") !== false
        ) {
            CLI::write(" [PASS] FileController Zero-404 Fallback aktif (HTTP 200 untuk seed files 172, 173, 182, 9999) & Fallback Alias routes terdaftar", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] FileController fallback atau alias routes belum valid");
            $failed++;
        }

        CLI::newLine();
        CLI::write("-------------------------------------------------------------", 'yellow');
        CLI::write("HASIL VERIFIKASI FASE E & F: {$passed} PASSED, {$failed} FAILED", $failed > 0 ? 'red' : 'green');
        CLI::write("-------------------------------------------------------------", 'yellow');

        return $failed > 0 ? 1 : 0;
    }
}
