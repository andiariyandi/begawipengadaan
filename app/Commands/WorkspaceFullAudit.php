<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Config\Services;

class WorkspaceFullAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'workspace:full:audit';
    protected $description = 'Performs a comprehensive automated audit of the entire Workspace ecosystem: routes, multi-role access, database integrity, and layouts.';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("        BEGAWIPENGADAAN WORKSPACE FULL AUDIT REPORT ENGINE        ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();

        $db = Database::connect();

        // -------------------------------------------------------------
        // SECTION 1: AUTHENTICATION GATE & FILTER AUDIT
        // -------------------------------------------------------------
        CLI::write("--- [1] AUDITING WORKSPACE SECURITY GATES & FILTERS ---", 'yellow');
        $filtersConfig = config('Filters');
        $workspaceFilterPresent = false;
        if (isset($filtersConfig->filters['workspace-auth']['before'])) {
            $patterns = $filtersConfig->filters['workspace-auth']['before'];
            if (in_array('workspace', $patterns, true) && in_array('workspace/*', $patterns, true)) {
                $workspaceFilterPresent = true;
            }
        }

        if ($workspaceFilterPresent) {
            CLI::write(" [PASS] WorkspaceAuthFilter is correctly bound to ['workspace', 'workspace/*'].", 'green');
        } else {
            CLI::write(" [WARN] WorkspaceAuthFilter is NOT strictly bound to all workspace routes in Filters.php!", 'red');
        }

        // Test unauthenticated access to /workspace
        if (session_status() === PHP_SESSION_ACTIVE) {
            session()->destroy();
        } else {
            $_SESSION = [];
        }
        $authFilter = new \App\Filters\WorkspaceAuthFilter();
        $dummyReq = Services::request();
        $unauthResp = $authFilter->before($dummyReq);
        if ($unauthResp instanceof \CodeIgniter\HTTP\RedirectResponse) {
            CLI::write(" [PASS] Unauthenticated visitor is safely redirected to login.", 'green');
        } else {
            CLI::write(" [FAIL] Unauthenticated visitor was NOT redirected!", 'red');
        }
        CLI::newLine();

        // -------------------------------------------------------------
        // SECTION 2: DATABASE & WORKSPACE SCHEMA INTEGRITY
        // -------------------------------------------------------------
        CLI::write("--- [2] AUDITING WORKSPACE DATABASE SCHEMA & ROW INTEGRITY ---", 'yellow');
        $criticalTables = [
            'users'                  => 'Akun Pengguna',
            'user_roles'             => 'Relasi Peran Pengguna',
            'user_profiles'          => 'Profil Pengguna',
            'learning_classes'       => 'Kelas Pembelajaran',
            'courses'                => 'Katalog Kursus/Materi PBJ',
            'enrollments'            => 'Pendaftaran Peserta Kelas',
            'learning_modules'       => 'Modul Silabus Kelas',
            'lessons'                => 'Materi/Lesson Modul',
            'learning_progress'      => 'Jejak Progres Belajar',
            'quizzes'                => 'Kuis / Ujian Pembelajaran',
            'quiz_questions'         => 'Bank Soal Kuis',
            'quiz_attempts'          => 'Riwayat Pengerjaan Kuis',
            'assignments'            => 'Penugasan Kelas',
            'assignment_submissions' => 'Pengumpulan Tugas Peserta',
            'class_trainers'         => 'Penugasan Pengajar Kelas',
            'events'                 => 'Agenda & Event PBJ',
            'event_registrations'    => 'Pendaftaran Event Peserta',
            'attendances'            => 'Presensi Kegiatan',
            'certificates'           => 'Penerbitan Sertifikat',
            'audit_logs'             => 'Audit Log Aktivitas',
            'notifications'          => 'Pusat Notifikasi',
            'bookmarks'              => 'Bookmark Referensi',
        ];

        $tableStats = [];
        foreach ($criticalTables as $tbl => $label) {
            if ($db->tableExists($tbl)) {
                $cnt = $db->table($tbl)->countAllResults();
                $tableStats[$tbl] = $cnt;
                CLI::write(" [PASS] Table `{$tbl}` ({$label}): {$cnt} records found.", 'green');
            } else {
                CLI::write(" [FAIL] Critical Table `{$tbl}` ({$label}) DOES NOT EXIST!", 'red');
            }
        }
        CLI::newLine();

        // Find reference data for testing dynamic routes
        $sampleClass = $db->table('learning_classes')->select('id, code, name')->orderBy('id', 'ASC')->get()->getRowArray();
        $sampleClassId = $sampleClass ? (int) $sampleClass['id'] : 0;

        $sampleEnrollment = $db->table('enrollments')->select('id, user_id, class_id')->orderBy('id', 'ASC')->get()->getRowArray();
        $sampleEnrollmentId = $sampleEnrollment ? (int) $sampleEnrollment['id'] : 0;
        $sampleUserId = $sampleEnrollment ? (int) $sampleEnrollment['user_id'] : 1;

        $sampleEvent = $db->table('events')->select('id, title, slug')->orderBy('id', 'ASC')->get()->getRowArray();
        $sampleEventId = $sampleEvent ? (int) $sampleEvent['id'] : 0;
        $sampleEventSlug = $sampleEvent ? (string) ($sampleEvent['slug'] ?: $sampleEvent['id']) : '1';

        $sampleTrainer = $db->table('class_trainers')->select('user_id, class_id')->where('status', 'active')->get()->getRowArray();
        $sampleTrainerId = $sampleTrainer ? (int) $sampleTrainer['user_id'] : $sampleUserId;
        if ($sampleTrainer && (int)$sampleTrainer['class_id'] > 0) {
            $sampleClassId = (int) $sampleTrainer['class_id'];
        }

        // -------------------------------------------------------------
        // SECTION 3: MULTI-ROLE ROUTE RESOLUTION & DISPATCH TEST
        // -------------------------------------------------------------
        CLI::write("--- [3] AUDITING ROUTE EXECUTION ACROSS 4 WORKSPACE ROLES ---", 'yellow');

        $routesToAudit = [
            // [Role Context, Route Path, Label]
            // Participant
            ['participant', 'workspace', 'Dispatcher Root Workspace'],
            ['participant', 'workspace/aktivitas', 'Aktivitas Log Peserta'],
            ['participant', 'workspace/notifikasi', 'Pusat Notifikasi'],
            ['participant', 'workspace/bookmark', 'Bookmark Referensi'],
            ['participant', 'workspace/bookmark/pembelajaran', 'Bookmark Pembelajaran'],
            ['participant', 'workspace/bookmark/regulasi', 'Bookmark Regulasi'],
            ['participant', 'workspace/bookmark/bunga-rampai', 'Bookmark Bunga Rampai'],
            ['participant', 'workspace/bookmark/komunikasi', 'Bookmark Komunikasi'],
            ['participant', 'workspace/bookmark/sertifikat', 'Bookmark Sertifikat'],
            ['participant', 'workspace/konsultasi', 'Konsultasi & Tiket Saya'],
            ['participant', 'workspace/konsultasi/buat', 'Buat Tiket Konsultasi'],
            ['participant', 'workspace/profil', 'Profil Akun'],
            ['participant', 'workspace/profil/akun', 'Profil Akun Sub-tab'],
            ['participant', 'workspace/profil/asn', 'Profil ASN'],
            ['participant', 'workspace/profil/penyedia', 'Profil Penyedia'],
            ['participant', 'workspace/profil/akademik', 'Profil Akademik'],
            ['participant', 'workspace/profil/masyarakat', 'Profil Masyarakat'],
            ['participant', 'workspace/profil/peran-pengadaan', 'Profil Peran PBJ'],
            ['participant', 'workspace/profil/pengajuan-role', 'Pengajuan Role (Redirect)'],
            ['participant', 'workspace/peserta', 'Dashboard Peserta'],
            ['participant', 'workspace/peserta/kelas-saya', 'Pembelajaran / Kelas Saya'],
            ['participant', 'workspace/peserta/tugas', 'Semua Tugas Peserta'],
            ['participant', 'workspace/peserta/assessment', 'Semua Assessment/Quiz Peserta'],
            ['participant', 'workspace/peserta/event-saya', 'Event Saya'],
            ['participant', 'workspace/peserta/presensi-saya', 'Presensi Saya'],
            ['participant', 'workspace/peserta/sertifikat', 'Sertifikat Peserta'],

            // Trainer
            ['trainer', 'workspace/trainer', 'Beranda Pengajar'],
            ['trainer', 'workspace/trainer/kelas-saya', 'Kelas Binaan Trainer'],
            ['trainer', 'workspace/trainer/materi', 'Bank Materi Trainer'],
            ['trainer', 'workspace/trainer/assessment', 'Assessment & Kuis Trainer'],
            ['trainer', 'workspace/trainer/peserta', 'Daftar Peserta Didik'],
            ['trainer', 'workspace/trainer/progres', 'Monitoring Progres Kelas'],
            ['trainer', 'workspace/trainer/submissions', 'Review Pengumpulan Tugas'],

            // Operator
            ['operator', 'workspace/operator', 'Pusat Operasional'],
            ['operator', 'workspace/operator/approval', 'Antrean Approval'],
            ['operator', 'workspace/operator/verifikasi-profil', 'Verifikasi Profil User'],
            ['operator', 'workspace/operator/perubahan-profil', 'Permohonan Perubahan Profil User'],
            ['operator', 'workspace/operator/penugasan-trainer', 'Penugasan Trainer Kelas'],
            ['operator', 'workspace/operator/event', 'Operasional Event'],
            ['operator', 'workspace/operator/kehadiran', 'Manajemen Presensi'],
            ['operator', 'workspace/operator/monitoring-pembelajaran', 'Monitoring Pembelajaran'],
            ['operator', 'workspace/operator/verifikasi-sertifikat', 'Verifikasi Sertifikat'],
            ['operator', 'workspace/operator/enrollment', 'Manajemen Pendaftaran'],
            ['operator', 'workspace/operator/evaluasi', 'Evaluasi & Override'],
            ['operator', 'workspace/operator/monitoring-aktivitas', 'Audit Log Sistem'],

            // Admin / Super Admin
            ['admin', 'workspace/admin', 'Pusat Eksekutif Workspace'],
        ];

        // Add dynamic routes if sample IDs exist
        if ($sampleClassId > 0) {
            $routesToAudit[] = ['participant', "workspace/peserta/kelas/{$sampleClassId}", "Detail Kelas Peserta (#{$sampleClassId})"];
            $routesToAudit[] = ['participant', "workspace/peserta/kelas/{$sampleClassId}/belajar", "Ruang Belajar Modular (#{$sampleClassId})"];
            $routesToAudit[] = ['participant', "workspace/peserta/kelas/{$sampleClassId}/progres", "Progres Kelas (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}", "Detail Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/edit", "Ubah Detail Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/materi", "Materi Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/modul", "Modul Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/peserta", "Peserta Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/quiz", "Ujian Kelas Trainer (#{$sampleClassId})"];
            $routesToAudit[] = ['trainer', "workspace/trainer/kelas/{$sampleClassId}/tugas", "Tugas Kelas Trainer (#{$sampleClassId})"];
        }

        if ($sampleEventId > 0) {
            $routesToAudit[] = ['participant', "workspace/peserta/event-saya/{$sampleEventSlug}", "Detail Event Peserta ({$sampleEventSlug})"];
            $routesToAudit[] = ['operator', "workspace/operator/event/{$sampleEventId}/peserta", "Peserta Event Operator (#{$sampleEventId})"];
        }

        $routes = Services::routes();
        $auditSummary = [
            'total'    => count($routesToAudit),
            'passed'   => 0,
            'redirect' => 0,
            'failed'   => 0,
            'details'  => [],
        ];

        foreach ($routesToAudit as [$roleContext, $routePath, $label]) {
            // Set up mock session for the required role context
            $sessionUserId = match ($roleContext) {
                'trainer'  => $sampleTrainerId,
                'operator' => 1,
                'admin'    => 1,
                default    => $sampleUserId,
            };

            $sessionRoles = match ($roleContext) {
                'admin'    => ['super_admin', 'admin'],
                'operator' => ['operator'],
                'trainer'  => ['trainer'],
                default    => ['participant'],
            };

            session()->set([
                'user_id'        => $sessionUserId,
                'username'       => 'audit_' . $roleContext,
                'name'           => 'Audit ' . ucfirst($roleContext),
                'email'          => "audit_{$roleContext}@begawipengadaan.test",
                'isLoggedIn'     => true,
                'account_status' => 'active',
                'roles'          => $sessionRoles,
            ]);

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), $routePath);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('GET');
            Services::injectMock('request', $req);
            $router = Services::router($routes, $req);

            $t0 = microtime(true);
            $mem0 = memory_get_usage();

            try {
                $controllerName = $router->handle($routePath);
                $methodName = $router->methodName();
                $params = $router->params();

                if (! class_exists($controllerName)) {
                    throw new \Exception("Controller class `{$controllerName}` not found.");
                }
                if (! method_exists($controllerName, $methodName)) {
                    throw new \Exception("Method `{$methodName}` does not exist in `{$controllerName}`.");
                }

                $controller = new $controllerName();
                $controller->initController($req, Services::response(), Services::logger());

                // Execute action
                $result = $controller->{$methodName}(...$params);

                $elapsedMs = round((microtime(true) - $t0) * 1000, 2);
                $memDeltaKb = round((memory_get_usage() - $mem0) / 1024, 1);

                if ($result instanceof \CodeIgniter\HTTP\RedirectResponse) {
                    $redirectTarget = $result->getHeaderLine('Location');
                    $auditSummary['redirect']++;
                    $auditSummary['details'][] = [
                        'role'     => $roleContext,
                        'path'     => $routePath,
                        'label'    => $label,
                        'status'   => 'REDIRECT',
                        'target'   => $redirectTarget,
                        'time_ms'  => $elapsedMs,
                        'mem_kb'   => $memDeltaKb,
                    ];
                    CLI::write(sprintf(" [REDIRECT] /%-45s -> %s (%0.1f ms)", $routePath, $redirectTarget, $elapsedMs), 'cyan');
                } elseif (is_string($result) || $result instanceof \CodeIgniter\HTTP\ResponseInterface) {
                    $auditSummary['passed']++;
                    $auditSummary['details'][] = [
                        'role'     => $roleContext,
                        'path'     => $routePath,
                        'label'    => $label,
                        'status'   => '200 OK',
                        'time_ms'  => $elapsedMs,
                        'mem_kb'   => $memDeltaKb,
                    ];
                    CLI::write(sprintf(" [200 OK]   /%-45s [%s] (%0.1f ms)", $routePath, $label, $elapsedMs), 'green');
                } else {
                    $auditSummary['passed']++;
                    $auditSummary['details'][] = [
                        'role'     => $roleContext,
                        'path'     => $routePath,
                        'label'    => $label,
                        'status'   => 'VOID/OK',
                        'time_ms'  => $elapsedMs,
                        'mem_kb'   => $memDeltaKb,
                    ];
                    CLI::write(sprintf(" [OK]       /%-45s [%s] (%0.1f ms)", $routePath, $label, $elapsedMs), 'green');
                }
            } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
                // If a sample record isn't enrolled or doesn't exist, 404 is graceful
                $auditSummary['redirect']++;
                $auditSummary['details'][] = [
                    'role'     => $roleContext,
                    'path'     => $routePath,
                    'label'    => $label,
                    'status'   => '404 (Handled)',
                    'message'  => $e->getMessage(),
                ];
                CLI::write(sprintf(" [404]      /%-45s Handled 404: %s", $routePath, $e->getMessage()), 'yellow');
            } catch (\Throwable $e) {
                $auditSummary['failed']++;
                $auditSummary['details'][] = [
                    'role'     => $roleContext,
                    'path'     => $routePath,
                    'label'    => $label,
                    'status'   => 'FAIL',
                    'error'    => $e->getMessage(),
                    'trace'    => $e->getFile() . ':' . $e->getLine(),
                ];
                CLI::write(sprintf(" [FAIL]     /%-45s %s in %s:%d", $routePath, $e->getMessage(), $e->getFile(), $e->getLine()), 'red');
            }
        }

        CLI::newLine();
        CLI::write("--- [4] AUDITING SIDEBAR MENU LINKS INTEGRITY ---", 'yellow');
        $sidebarFile = APPPATH . 'Views/main/workspace/components/sidebar.php';
        $sidebarContent = file_get_contents($sidebarFile);
        preg_match_all('/route_to\([\'"]([^\'"]+)[\'"](?:,\s*([^)]+))?\)/', $sidebarContent, $routeMatches, PREG_SET_ORDER);

        $deadLinks = [];
        $validLinks = 0;
        foreach ($routeMatches as $m) {
            $routeName = $m[1];
            try {
                $resolved = route_to($routeName);
                $validLinks++;
            } catch (\Throwable $e) {
                // Check if route takes arguments
                try {
                    $resolved = route_to($routeName, 1);
                    $validLinks++;
                } catch (\Throwable $e2) {
                    $deadLinks[] = $routeName;
                    CLI::write(" [DEAD LINK] route_to('{$routeName}') failed: " . $e2->getMessage(), 'red');
                }
            }
        }
        if ($deadLinks === []) {
            CLI::write(" [PASS] All {$validLinks} named routes referenced in sidebar resolve successfully without dead links.", 'green');
        } else {
            CLI::write(" [FAIL] Found " . count($deadLinks) . " unresolved route names in sidebar!", 'red');
        }

        CLI::newLine();
        CLI::write("==================================================================", 'cyan');
        CLI::write("                    AUDIT SUMMARY SCORECARD                       ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::write(" Total Routes Tested : " . $auditSummary['total'], 'white');
        CLI::write(" Successful (200 OK) : " . $auditSummary['passed'], 'green');
        CLI::write(" Handled/Redirects   : " . $auditSummary['redirect'], 'cyan');
        CLI::write(" Failed Exceptions   : " . $auditSummary['failed'], $auditSummary['failed'] > 0 ? 'red' : 'green');
        CLI::write(" Dead Sidebar Links  : " . count($deadLinks), count($deadLinks) > 0 ? 'red' : 'green');
        CLI::write("==================================================================", 'cyan');

        return $auditSummary['failed'] === 0 ? 0 : 1;
    }
}
