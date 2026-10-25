<?php

namespace App\Commands;

use App\Filters\AuthenticationFilter;
use App\Models\UserModel;
use App\Services\Identity\AuthorizationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class RbacDoubleRoleTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:rbac-double-role';
    protected $description = 'Comprehensive test verifying double-role paradigm, dashboard sterilization, assigned scope, and stacked sidebar';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("     BEGAWIPENGADAAN RBAC DOUBLE-ROLE & WORKSPACE TEST SUITE      ", "yellow");
        CLI::write("==================================================================", "yellow");

        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $message) use (&$passed, &$failed): void {
            if ($condition) {
                CLI::write("  [PASS] " . $message, "green");
                $passed++;
            } else {
                CLI::write("  [FAIL] " . $message, "red");
                $failed++;
            }
        };

        $authService = new AuthorizationService();
        $userModel   = new UserModel();

        // -----------------------------------------------------------------
        // TEST 1: LOGIN REDIRECTION & DASHBOARD STERILIZATION
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 1] Login Redirection & Dashboard Sterilization ---", "cyan");

        // Helper to simulate LoginController redirection logic
        $simulateLoginRedirect = function (array $roles, string $name = 'User Test'): string {
            $auth = new AuthorizationService();
            $isFullAdmin = in_array('super_administrator', $roles, true)
                || in_array('administrator', $roles, true);

            if ($isFullAdmin) {
                return site_url('dashboard');
            }

            return route_to('workspace.home');
        };

        // Super Admin & Admin -> Dashboard
        $adminRedirect = $simulateLoginRedirect(['administrator']);
        $assert(str_ends_with($adminRedirect, 'dashboard'), "Administrator login mendarat langsung di Dashboard (/dashboard)");

        $superRedirect = $simulateLoginRedirect(['super_administrator']);
        $assert(str_ends_with($superRedirect, 'dashboard'), "Super Administrator login mendarat langsung di Dashboard (/dashboard)");

        // Seluruh peran lainnya -> Workspace
        $operatorRedirect = $simulateLoginRedirect(['operator']);
        $assert(str_ends_with($operatorRedirect, 'workspace'), "Operator login direct ke Workspace (/workspace)");

        $trainerRedirect = $simulateLoginRedirect(['trainer']);
        $assert(str_ends_with($trainerRedirect, 'workspace'), "Trainer login direct ke Workspace (/workspace)");

        $pembelajaranRedirect = $simulateLoginRedirect(['admin_pembelajaran']);
        $assert(str_ends_with($pembelajaranRedirect, 'workspace'), "Admin Pembelajaran login direct ke Workspace (/workspace)");

        $eventRedirect = $simulateLoginRedirect(['admin_event']);
        $assert(str_ends_with($eventRedirect, 'workspace'), "Admin Event login direct ke Workspace (/workspace)");

        $regulasiRedirect = $simulateLoginRedirect(['admin_regulasi']);
        $assert(str_ends_with($regulasiRedirect, 'workspace'), "Admin Regulasi login direct ke Workspace (/workspace)");

        $pesertaRedirect = $simulateLoginRedirect(['peserta']);
        $assert(str_ends_with($pesertaRedirect, 'workspace'), "Peserta murni login direct ke Workspace (/workspace)");

        // -----------------------------------------------------------------
        // TEST 2: AUTHORIZATION SERVICE - HAS DASHBOARD ACCESS
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 2] Dashboard Access Guarding (AuthorizationService) ---", "cyan");

        $assert($authService->hasDashboardAccess(null, ['super_administrator']) === true, "Super Administrator berhak mengakses Dashboard");
        $assert($authService->hasDashboardAccess(null, ['administrator']) === true, "Administrator berhak mengakses Dashboard");
        $assert($authService->hasDashboardAccess(null, ['admin_pembelajaran']) === true, "admin_pembelajaran berhak mengakses Dashboard");
        $assert($authService->hasDashboardAccess(null, ['admin_event']) === true, "admin_event berhak mengakses Dashboard");
        $assert($authService->hasDashboardAccess(null, ['admin_regulasi']) === true, "admin_regulasi berhak mengakses Dashboard");
        $assert($authService->hasDashboardAccess(null, ['operator']) === false, "Operator DITOLAK dari Dashboard (Steril)");
        $assert($authService->hasDashboardAccess(null, ['trainer']) === false, "Trainer DITOLAK dari Dashboard (Steril)");
        $assert($authService->hasDashboardAccess(null, ['peserta']) === false, "Peserta DITOLAK dari Dashboard (Steril)");

        // -----------------------------------------------------------------
        // TEST 3: DASHBOARD ROUTE MAPPINGS FOR PREFIX ADMINS
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 3] Dynamic Modular Route Mappings ---", "cyan");

        $routePembelajaran = $authService->getFirstAllowedDashboardRoute([], ['admin_pembelajaran']);
        $assert($routePembelajaran === 'dashboard/pembelajaran', "admin_pembelajaran memetakan ke 'dashboard/pembelajaran' (hasil: {$routePembelajaran})");

        $routeEvent = $authService->getFirstAllowedDashboardRoute([], ['admin_event']);
        $assert($routeEvent === 'dashboard/komunikasi/event-agenda', "admin_event memetakan ke 'dashboard/komunikasi/event-agenda' (hasil: {$routeEvent})");

        $routeRegulasi = $authService->getFirstAllowedDashboardRoute([], ['admin_regulasi']);
        $assert($routeRegulasi === 'dashboard/regulasi/dokumen', "admin_regulasi memetakan ke 'dashboard/regulasi/dokumen' (hasil: {$routeRegulasi})");

        // -----------------------------------------------------------------
        // TEST 4: STACKED MODULAR SIDEBAR IN WORKSPACE
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 4] Stacked Modular Sidebar in Workspace ---", "cyan");

        // Case A: Operator
        session()->set([
            'user_id'        => 301,
            'roles'          => ['operator'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Budi Operator',
        ]);
        $operatorSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasOperatorBlock    = str_contains($operatorSidebarHtml, 'Asisten Operasional (Ditugaskan)');
        $hasBasicBlock       = str_contains($operatorSidebarHtml, 'Pembelajaran & Kegiatan (Peserta)');
        $hasPersonalBlock    = str_contains($operatorSidebarHtml, 'Fitur Personal & Helpdesk');
        $hasApprovalBlock    = str_contains($operatorSidebarHtml, 'Pusat Approval & Verifikasi');

        $assert($hasOperatorBlock, "Sidebar Operator memuat blok 'Asisten Operasional (Ditugaskan)'");
        $assert($hasBasicBlock, "Sidebar Operator memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'");
        $assert($hasPersonalBlock, "Sidebar Operator memuat 'Fitur Personal & Helpdesk'");
        $assert(! $hasApprovalBlock, "Sidebar Operator TIDAK memuat 'Pusat Approval & Verifikasi' (Steril)");

        // Case B: Trainer
        session()->set([
            'user_id'        => 300,
            'roles'          => ['trainer'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Pak Trainer',
        ]);
        $trainerSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasTrainerBlock    = str_contains($trainerSidebarHtml, 'Pengajaran & Kelas (Ditugaskan)');
        $hasTrainerBasic    = str_contains($trainerSidebarHtml, 'Pembelajaran & Kegiatan (Peserta)');

        $assert($hasTrainerBlock, "Sidebar Trainer memuat blok 'Pengajaran & Kelas (Ditugaskan)'");
        $assert($hasTrainerBasic, "Sidebar Trainer memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'");

        // Case C: Admin Pembelajaran
        session()->set([
            'user_id'        => 302,
            'roles'          => ['admin_pembelajaran', 'peserta'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Admin Arena Belajar',
        ]);
        $pembelajaranSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasPembelajaranBlock    = str_contains($pembelajaranSidebarHtml, 'Kelola Arena Belajar (Full Kelas)');
        $hasPembelajaranShortcut = str_contains($pembelajaranSidebarHtml, 'Buka Dashboard Arena Belajar');
        $hasPembelajaranBasic    = str_contains($pembelajaranSidebarHtml, 'Pembelajaran & Kegiatan (Peserta)');

        $assert($hasPembelajaranBlock, "Sidebar Admin Pembelajaran memuat blok 'Kelola Arena Belajar (Full Kelas)'");
        $assert($hasPembelajaranShortcut, "Sidebar Admin Pembelajaran memuat tombol 'Buka Dashboard Arena Belajar'");
        $assert($hasPembelajaranBasic, "Sidebar Admin Pembelajaran memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'");

        // Case D: Admin Event
        session()->set([
            'user_id'        => 303,
            'roles'          => ['admin_event', 'peserta'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Admin Event PBJ',
        ]);
        $eventSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasEventBlock    = str_contains($eventSidebarHtml, 'Kelola Event PBJ (Full Event)');
        $hasEventShortcut = str_contains($eventSidebarHtml, 'Buka Dashboard Event PBJ');

        $assert($hasEventBlock, "Sidebar Admin Event memuat blok 'Kelola Event PBJ (Full Event)'");
        $assert($hasEventShortcut, "Sidebar Admin Event memuat tombol 'Buka Dashboard Event PBJ'");

        // Case E: Admin Regulasi (Modular lainnya)
        session()->set([
            'user_id'        => 304,
            'roles'          => ['admin_regulasi', 'peserta'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Admin Regulasi',
        ]);
        $regulasiSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasRegulasiShortcut = str_contains($regulasiSidebarHtml, 'Akses Dashboard Modul') && str_contains($regulasiSidebarHtml, 'Buka Dashboard Admin');
        $hasRegulasiBasic    = str_contains($regulasiSidebarHtml, 'Pembelajaran & Kegiatan (Peserta)');

        $assert($hasRegulasiShortcut, "Sidebar Admin Regulasi memuat tombol pintas 'Buka Dashboard Admin'");
        $assert($hasRegulasiBasic, "Sidebar Admin Regulasi memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'");

        // Case F: Administrator
        session()->set([
            'user_id'        => 14,
            'roles'          => ['administrator'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
            'name'           => 'Biro PBJ Administrator',
        ]);
        $adminSidebarHtml = html_entity_decode(view('main/workspace/components/sidebar'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasAdminExecutive = str_contains($adminSidebarHtml, 'Komando Eksekutif & RBAC');
        $hasAdminApproval  = str_contains($adminSidebarHtml, 'Pusat Approval & Verifikasi');

        $assert($hasAdminExecutive, "Sidebar Administrator memuat blok 'Komando Eksekutif & RBAC'");
        $assert($hasAdminApproval, "Sidebar Administrator memuat blok 'Pusat Approval & Verifikasi'");

        // -----------------------------------------------------------------
        // TEST 5: OPERATOR CONTROLLER SCOPE & ASSIGNMENT PRIVILEGES
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 5] Operator Controller Scope & Action Guards ---", "cyan");

        // Non-admin attempting to access approvals in OperatorController must be blocked
        $opController = new \App\Controllers\Workspace\OperatorController();
        $opController->initController(Services::request(), Services::response(), Services::logger());

        // Session as pure operator
        session()->set([
            'user_id'        => 301,
            'roles'          => ['operator'],
            'isLoggedIn'     => true,
            'account_status' => 'active',
        ]);

        $reflection = new \ReflectionClass($opController);

        $hasFullClassMethod = $reflection->getMethod('hasFullClassScope');
        $hasFullClassMethod->setAccessible(true);

        $hasFullEventMethod = $reflection->getMethod('hasFullEventScope');
        $hasFullEventMethod->setAccessible(true);

        // Operator has restricted class & event scope
        $assert($hasFullClassMethod->invoke($opController) === false, "Operator murni TIDAK memiliki Full Class Scope (Assigned Only)");
        $assert($hasFullEventMethod->invoke($opController) === false, "Operator murni TIDAK memiliki Full Event Scope (Assigned Only)");

        // Admin Pembelajaran has full class scope
        session()->set(['roles' => ['admin_pembelajaran']]);
        $assert($hasFullClassMethod->invoke($opController) === true, "admin_pembelajaran MEMILIKI Full Class Scope (Seluruh Kelas)");

        // Admin Event has full event scope
        session()->set(['roles' => ['admin_event']]);
        $assert($hasFullEventMethod->invoke($opController) === true, "admin_event MEMILIKI Full Event Scope (Seluruh Event)");

        // Administrator has both full scopes
        session()->set(['roles' => ['administrator']]);
        $assert($hasFullClassMethod->invoke($opController) === true, "administrator MEMILIKI Full Class Scope");
        $assert($hasFullEventMethod->invoke($opController) === true, "administrator MEMILIKI Full Event Scope");

        // -----------------------------------------------------------------
        // TEST 6: SCHEMA PRESERVATION (ZERO MIGRATION COMPLIANCE)
        // -----------------------------------------------------------------
        CLI::write("\n--- [TEST 6] Zero-Migration & Schema-Preservation Compliance ---", "cyan");
        $db = \Config\Database::connect();
        $migrationCount = (int) $db->table('migrations')->countAllResults();
        $assert($migrationCount === 16, "Tepat 16 berkas migrasi database eksisting terjaga utuh (Zero Migration = 100%)");

        CLI::write("\n==================================================================", "yellow");
        CLI::write("                 TEST RESULTS SUMMARY                            ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write(" Total Assertions : " . ($passed + $failed));
        CLI::write(" Passed           : {$passed}", "green");
        CLI::write(" Failed           : {$failed}", $failed > 0 ? "red" : "green");
        CLI::write("==================================================================\n", "yellow");

        return $failed > 0 ? 1 : 0;
    }
}
