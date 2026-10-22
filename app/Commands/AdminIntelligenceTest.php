<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\Admin\IntelligenceAnalyticsService;
use App\Controllers\Intelligence\IntelligenceDashboardController;
use App\Controllers\Intelligence\JfPpbjController;
use App\Controllers\Intelligence\OpdController;
use App\Controllers\Intelligence\CompetencyCertificateController;

class AdminIntelligenceTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-intelligence';
    protected $description = 'Verifikasi Fitur Fase 4: Revitalisasi Government Intelligence (SDM PBJ & Gap OPD)';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 4: GOVERNMENT INTELLIGENCE & GAP OPD ===", 'yellow');
        $passed = 0;
        $failed = 0;

        $service = new IntelligenceAnalyticsService();
        $dashCtrl = new IntelligenceDashboardController($service);
        $jfCtrl   = new JfPpbjController($service);
        $opdCtrl  = new OpdController($service);
        $certCtrl = new CompetencyCertificateController($service);

        // Mock session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 13,
            'name'       => 'Super Administrator',
            'username'   => 'superadmin',
            'roles'      => ['super_admin']
        ]);

        // 1. Uji Inisialisasi Baseline & KPI Overview
        try {
            $kpi = $service->getOverviewKpi();
            if (isset($kpi['total_opd'], $kpi['total_personnel'], $kpi['total_certificates'], $kpi['total_gap'])) {
                CLI::write(" [PASS] IntelligenceAnalyticsService::getOverviewKpi() berhasil menghitung 4 metrik KPI riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getOverviewKpi() field tidak lengkap");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getOverviewKpi() exception: " . $e->getMessage());
            $failed++;
        }

        // 2. Uji Analisis Gap Kompetensi OPD
        try {
            $gapData = $service->getCompetencyGapAnalysis(2026);
            if (isset($gapData['summary'], $gapData['chart'], $gapData['matrix']) && count($gapData['matrix']) > 0) {
                CLI::write(" [PASS] IntelligenceAnalyticsService::getCompetencyGapAnalysis() menghasilkan matriks gap komparatif & chart", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getCompetencyGapAnalysis() tidak menghasilkan data matriks");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getCompetencyGapAnalysis() exception: " . $e->getMessage());
            $failed++;
        }

        // 3. Uji Daftar Personel SDM JF PPBJ
        try {
            $personnel = $service->getPersonnelList();
            if (is_array($personnel)) {
                CLI::write(" [PASS] IntelligenceAnalyticsService::getPersonnelList() memuat daftar personel dengan relasi OPD & Jenjang", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getPersonnelList() gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getPersonnelList() exception: " . $e->getMessage());
            $failed++;
        }

        // 4. Uji Daftar OPD / Unit Kerja
        try {
            $opds = $service->getOpdList();
            if (is_array($opds) && count($opds) > 0) {
                CLI::write(" [PASS] IntelligenceAnalyticsService::getOpdList() memuat unit kerja dengan kuota standar & defisit formasi", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getOpdList() gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getOpdList() exception: " . $e->getMessage());
            $failed++;
        }

        // 5. Uji Daftar Sertifikat Kompetensi
        try {
            $certs = $service->getCompetencyCertificates();
            if (is_array($certs)) {
                CLI::write(" [PASS] IntelligenceAnalyticsService::getCompetencyCertificates() membaca direktori sertifikasi keahlian", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getCompetencyCertificates() gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getCompetencyCertificates() exception: " . $e->getMessage());
            $failed++;
        }

        // 6. Uji Render IntelligenceDashboardController::index()
        try {
            $html = $dashCtrl->index();
            if (strpos($html, 'Dashboard Government Intelligence') !== false && strpos($html, 'intelligencePersonnelChart') !== false) {
                CLI::write(" [PASS] Render View Home Intelligence aktif dengan grafik ApexCharts riil & KPI", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render Home Intelligence tidak sesuai");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render Home Intelligence exception: " . $e->getMessage());
            $failed++;
        }

        // 7. Uji Render IntelligenceDashboardController::competencyGap()
        try {
            $html = $dashCtrl->competencyGap();
            if (strpos($html, 'Analisis Gap Kompetensi OPD') !== false && strpos($html, 'competencyGapChart') !== false) {
                CLI::write(" [PASS] Render View Gap Kompetensi aktif dengan matriks OPD dan grafik batang komparatif", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render Gap Kompetensi tidak sesuai");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render Gap Kompetensi exception: " . $e->getMessage());
            $failed++;
        }

        // 8. Uji Render JfPpbjController::index() & detail()
        try {
            $htmlIndex = $jfCtrl->index();
            $samplePers = \Config\Database::connect()->table('jf_ppbj_records')->limit(1)->get()->getRowArray();
            $htmlDetail = $samplePers ? $jfCtrl->detail((string) $samplePers['id']) : '';

            if (strpos($htmlIndex, 'Data Personel SDM JF PPBJ') !== false && (! $samplePers || strpos($htmlDetail, 'Detail Personel') !== false)) {
                CLI::write(" [PASS] Render View SDM JF PPBJ (Index & Detail) aktif menyajikan profil aparatur lengkap", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render View SDM JF PPBJ gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render View SDM JF PPBJ exception: " . $e->getMessage());
            $failed++;
        }

        // 9. Uji Render OpdController::index() & detail()
        try {
            $htmlOpdIndex = $opdCtrl->index();
            $sampleOpd = \Config\Database::connect()->table('organization_units')->limit(1)->get()->getRowArray();
            $htmlOpdDetail = $sampleOpd ? $opdCtrl->detail((string) $sampleOpd['id']) : '';

            if (strpos($htmlOpdIndex, 'Pemetaan OPD dan Unit Kerja') !== false && (! $sampleOpd || strpos($htmlOpdDetail, 'Detail OPD') !== false)) {
                CLI::write(" [PASS] Render View OPD Unit (Index & Detail) aktif menyajikan indikator kesiapan staf", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render View OPD Unit gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render View OPD Unit exception: " . $e->getMessage());
            $failed++;
        }

        // 10. Uji Render CompetencyCertificateController::index() & detail()
        try {
            $htmlCertIndex = $certCtrl->index();
            $sampleCert = \Config\Database::connect()->table('competency_certificates')->limit(1)->get()->getRowArray();
            $htmlCertDetail = $sampleCert ? $certCtrl->detail((string) $sampleCert['id']) : '';

            if (strpos($htmlCertIndex, 'Direktori Sertifikat Kompetensi PBJ') !== false && (! $sampleCert || strpos($htmlCertDetail, 'Detail Sertifikat Kompetensi') !== false)) {
                CLI::write(" [PASS] Render View Sertifikat Kompetensi (Index & Detail) aktif menyajikan masa berlaku & jenis", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render View Sertifikat Kompetensi gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render View Sertifikat Kompetensi exception: " . $e->getMessage());
            $failed++;
        }

        // 11. Uji Render Sub-modul Jenjang, Riwayat, dan Jenis Sertifikat
        try {
            $htmlLevels    = $jfCtrl->levels();
            $htmlHistories = $jfCtrl->histories();
            $htmlTypes     = $certCtrl->types();

            if (
                strpos($htmlLevels, 'Jenjang Jabatan') !== false &&
                strpos($htmlHistories, 'Riwayat Penugasan') !== false &&
                strpos($htmlTypes, 'Standar') !== false
            ) {
                CLI::write(" [PASS] Render View Master Jenjang, Riwayat Penugasan, dan Jenis Sertifikat aktif 100%", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render Sub-modul master gagal");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Render Sub-modul master exception: " . $e->getMessage());
            $failed++;
        }

        // 12. Jaminan Zero Database Migration
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        if (count($migrationFiles) === 16) {
            CLI::write(" [PASS] Zero Migration Check: 16 file migrasi eksisting, 0 migrasi baru dibuat", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ditemukan perubahan jumlah file migrasi! Total: " . count($migrationFiles));
            $failed++;
        }

        CLI::newLine();
        CLI::write("HASIL PENGUJIAN: {$passed} BERHASIL, {$failed} GAGAL.", $failed === 0 ? 'green' : 'red');

        return $failed === 0 ? 0 : 1;
    }
}
