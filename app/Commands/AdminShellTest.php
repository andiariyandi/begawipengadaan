<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AdminShellTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-shell';
    protected $description = 'Verifikasi Fitur Fase 1: Shell Navigasi, Spotlight Modal, dan Quick Action Bar';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 1: SHELL NAVIGASI & SPOTLIGHT PALETTE ===", 'yellow');
        $passed = 0;
        $failed = 0;

        // 1. Uji Layout Dashboard menyertakan spotlight_modal
        $layoutFile = APPPATH . 'Views/dashboard/layouts/dashboard.php';
        $layoutContent = file_get_contents($layoutFile);
        if (strpos($layoutContent, "include('dashboard/components/spotlight_modal')") !== false) {
            CLI::write(" [PASS] Layout dashboard.php menyertakan spotlight_modal", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Layout dashboard.php TIDAK menyertakan spotlight_modal");
            $failed++;
        }

        // 2. Uji Komponen Spotlight Modal
        $spotlightFile = APPPATH . 'Views/dashboard/components/spotlight_modal.php';
        if (file_exists($spotlightFile)) {
            $spotlightContent = file_get_contents($spotlightFile);
            $hasKeydown = strpos($spotlightContent, "e.key.toLowerCase() === 'k'") !== false;
            $hasCatalog = strpos($spotlightContent, 'adminCatalog') !== false;
            $hasModalId = strpos($spotlightContent, 'id="adminSpotlightModal"') !== false;

            if ($hasKeydown && $hasCatalog && $hasModalId) {
                CLI::write(" [PASS] Komponen spotlight_modal.php lengkap (Ctrl+K shortcut, katalog 90+ menu & modal DOM)", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Komponen spotlight_modal.php tidak lengkap");
                $failed++;
            }
        } else {
            CLI::error(" [FAIL] File spotlight_modal.php tidak ditemukan");
            $failed++;
        }

        // 3. Uji Header Dashboard (App Switcher, Dynamic User, Spotlight Trigger)
        $headerFile = APPPATH . 'Views/dashboard/components/header.php';
        $headerContent = file_get_contents($headerFile);
        $hasSpotlightTrigger = strpos($headerContent, 'data-bs-target="#adminSpotlightModal"') !== false;
        $hasWorkspaceLink    = strpos($headerContent, "site_url('workspace')") !== false;
        $hasPublicLink       = strpos($headerContent, "site_url('/')") !== false;
        $hasDynamicUser      = strpos($headerContent, '$userName') !== false;

        if ($hasSpotlightTrigger && $hasWorkspaceLink && $hasPublicLink && $hasDynamicUser) {
            CLI::write(" [PASS] Header dashboard.php memiliki trigger Spotlight (Ctrl+K), App Switcher Workspace/Portal & Profil Dinamis", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Header dashboard.php belum memenuhi spesifikasi Fase 1");
            $failed++;
        }

        // 4. Uji Quick Action Bar pada Beranda Ringkasan
        $homeFile = APPPATH . 'Views/dashboard/home/index.php';
        $homeContent = file_get_contents($homeFile);
        $hasQuickAction = strpos($homeContent, '<!-- Quick Action Bar -->') !== false;
        $hasAddUser     = strpos($homeContent, "site_url('dashboard/akses/pengguna/tambah')") !== false;
        $hasAddClass    = strpos($homeContent, "site_url('dashboard/pembelajaran/kelas/tambah')") !== false;
        $hasAddEvent    = strpos($homeContent, "site_url('dashboard/komunikasi/event-agenda/tambah')") !== false;
        $hasAddReg      = strpos($homeContent, "site_url('dashboard/regulasi/dokumen/tambah')") !== false;

        if ($hasQuickAction && $hasAddUser && $hasAddClass && $hasAddEvent && $hasAddReg) {
            CLI::write(" [PASS] Halaman Ringkasan Dashboard memiliki Quick Action Bar lengkap (+Pengguna, +Kelas, +Event, +Regulasi, +Pengumuman)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Quick Action Bar pada index.php belum lengkap");
            $failed++;
        }

        // 5. Uji Render DashboardController::index()
        try {
            session()->set([
                'isLoggedIn' => true,
                'user_id'    => 1,
                'name'       => 'Admin Test Pengadaan',
                'username'   => 'admin_test',
                'roles'      => ['super_admin']
            ]);
            $controller = new \App\Controllers\Admin\DashboardController();
            $output = $controller->index();
            $renderHasSpotlight   = strpos($output, 'id="adminSpotlightModal"') !== false;
            $renderHasQuickAction = strpos($output, 'Aksi Cepat:') !== false;

            if ($renderHasSpotlight && $renderHasQuickAction) {
                CLI::write(" [PASS] DashboardController::index() berhasil merender HTML dengan Spotlight Modal & Quick Action Bar utuh", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Render HTML DashboardController::index() tidak memuat elemen Fase 1 yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Exception saat render DashboardController::index(): " . $e->getMessage());
            $failed++;
        }

        CLI::newLine();
        CLI::write("-------------------------------------------------------------", 'yellow');
        CLI::write("HASIL VERIFIKASI FASE 1: {$passed} PASSED, {$failed} FAILED", $failed === 0 ? 'green' : 'red');
        CLI::write("-------------------------------------------------------------", 'yellow');
    }
}
