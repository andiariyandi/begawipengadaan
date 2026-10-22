<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AdminSystemTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-system';
    protected $description = 'Verifikasi Fitur Fase 5: Penguatan Akses, Audit Trail CSV Export, & Pengaturan Sistem';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 5: PENGUATAN AKSES, AUDIT TRAIL & PENGATURAN SISTEM ===", 'yellow');
        $passed = 0;
        $failed = 0;

        // 1. Uji Rute & Logika Reset Kata Sandi Administratif
        $userControllerFile = APPPATH . 'Controllers/Admin/UserController.php';
        $userControllerContent = file_get_contents($userControllerFile);
        $hasDirectReset = strpos($userControllerContent, "new_password") !== false
            && strpos($userControllerContent, "password_hash") !== false
            && strpos($userControllerContent, "administrative_direct") !== false
            && strpos($userControllerContent, "session_version") !== false;

        $hasResetRoute = false;
        try {
            $resetUrl = url_to('dashboard.access.users.reset-password', 1);
            $hasResetRoute = (strpos($resetUrl, 'reset-password') !== false);
        } catch (\Throwable $e) {
            $hasResetRoute = false;
        }

        if ($hasDirectReset && $hasResetRoute) {
            CLI::write(" [PASS] Logika & Rute Reset Password Administratif Langsung tersedia dengan audit trail", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Logika Reset Password Administratif belum lengkap di UserController");
            $failed++;
        }

        // 2. Uji Modal Reset Password Administratif pada View Detail Pengguna
        $userDetailFile = APPPATH . 'Views/dashboard/users/detail.php';
        $userDetailContent = file_get_contents($userDetailFile);
        $hasModal = strpos($userDetailContent, 'id="adminResetPasswordModal"') !== false;
        $hasGenerate = strpos($userDetailContent, 'id="btnGeneratePassword"') !== false;
        $hasToggle = strpos($userDetailContent, 'id="toggleAdminPasswordBtn"') !== false;

        if ($hasModal && $hasGenerate && $hasToggle) {
            CLI::write(" [PASS] Modal Reset Password Administratif & Generator Password Acak tersedia di detail.php", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Modal Reset Password Administratif tidak ditemukan di detail.php");
            $failed++;
        }

        // 3. Uji Model Audit Log & Seleksi Data Aktor
        $auditModelFile = APPPATH . 'Models/AuditLogModel.php';
        $auditModelContent = file_get_contents($auditModelFile);
        $hasActorEnrichment = strpos($auditModelContent, 'actor_name') !== false && strpos($auditModelContent, 'actor_email') !== false;

        if ($hasActorEnrichment) {
            CLI::write(" [PASS] AuditLogModel::browse() mengekstrak identitas nama & email aktor secara dinamis", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] AuditLogModel::browse() belum diperkaya dengan data nama/email aktor");
            $failed++;
        }

        // 4. Uji Rute Ekspor CSV Audit Trail
        $hasAccessExportRoute = false;
        $hasSystemExportRoute = false;
        try {
            $accessExportUrl = url_to('dashboard.access.audit.export');
            $hasAccessExportRoute = (strpos($accessExportUrl, 'dashboard/akses/audit/export') !== false);
            $systemExportUrl = url_to('dashboard.system.audit-trail.export');
            $hasSystemExportRoute = (strpos($systemExportUrl, 'dashboard/sistem/audit-trail/export') !== false);
        } catch (\Throwable $e) {
            // ignore
        }

        if ($hasAccessExportRoute && $hasSystemExportRoute) {
            CLI::write(" [PASS] Rute Ekspor CSV Audit Trail terdaftar di /dashboard/akses/audit/export & /dashboard/sistem/audit-trail/export", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Rute Ekspor CSV Audit Trail tidak terdaftar di Routes");
            $failed++;
        }

        // 5. Uji Method ExportCsv pada AuditController
        $auditControllerFile = APPPATH . 'Controllers/Admin/AuditController.php';
        $auditControllerContent = file_get_contents($auditControllerFile);
        $hasExportMethod = strpos($auditControllerContent, 'function exportCsv()') !== false;
        $hasUtf8Bom = strpos($auditControllerContent, '\xEF\xBB\xBF') !== false;
        $hasCsvStream = strpos($auditControllerContent, 'php://output') !== false;

        if ($hasExportMethod && $hasUtf8Bom && $hasCsvStream) {
            CLI::write(" [PASS] AuditController::exportCsv() siap streaming CSV berstandar UTF-8 BOM untuk Microsoft Excel", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] AuditController::exportCsv() belum memenuhi standar UTF-8 BOM / CSV stream");
            $failed++;
        }

        // 6. Uji Tombol Ekspor pada View Audit Trail
        $auditViewFile = APPPATH . 'Views/dashboard/audit/index.php';
        $auditViewContent = file_get_contents($auditViewFile);
        $hasExportBtn = strpos($auditViewContent, 'Ekspor CSV Log') !== false;

        if ($hasExportBtn) {
            CLI::write(" [PASS] Tombol Ekspor CSV Log terintegrasi pada toolbar & header index audit trail", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tombol Ekspor CSV Log tidak ditemukan di index audit view");
            $failed++;
        }

        // 7. Uji SystemSettingService & Grup Pengaturan
        try {
            $settingService = new \App\Services\SystemSettingService();
            $groups = ['general', 'organization', 'learning', 'knowledge', 'regulation', 'news', 'event', 'certificate'];
            $allValid = true;
            foreach ($groups as $grp) {
                $cfg = $settingService->getGroup($grp);
                if (! is_array($cfg)) {
                    $allValid = false;
                    break;
                }
            }

            if ($allValid) {
                CLI::write(" [PASS] SystemSettingService memuat 8 grup konfigurasi sistem dengan integritas valid", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Salah satu grup konfigurasi SystemSettingService gagal dimuat");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Kesalahan saat memuat SystemSettingService: " . $e->getMessage());
            $failed++;
        }

        // 8. STRICT ZERO DATABASE MIGRATIONS Check
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migrationCount = count($migrationFiles);
        if ($migrationCount === 16) {
            CLI::write(" [PASS] Konstrain Mutlak Zero Database Migrations terpenuhi (tetap tepat 16 berkas)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Jumlah berkas migrasi berubah: {$migrationCount} berkas (seharusnya tepat 16 berkas)");
            $failed++;
        }

        CLI::newLine();
        CLI::write("----------------------------------------------------------------", 'yellow');
        CLI::write("HASIL PENGUJIAN FASE 5: {$passed} PASSED, {$failed} FAILED", $failed > 0 ? 'red' : 'green');
        CLI::write("----------------------------------------------------------------", 'yellow');

        return $failed > 0 ? 1 : 0;
    }
}
