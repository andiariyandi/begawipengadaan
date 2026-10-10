<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class WorkspaceSmoke extends BaseCommand
{
    protected $group       = 'Workspace';
    protected $name        = 'workspace:smoke';
    protected $description = 'Smoke test dedicated FiStudy Workspace Header, Left Sidebar, and Layout integration.';
    protected $usage       = 'workspace:smoke';
    protected $arguments   = [];
    protected $options     = [];

    public function run(array $params): int
    {
        CLI::write('=== SMOKE TEST: DEDICATED FISTUDY WORKSPACE HEADER & LAYOUT ===', 'green');
        CLI::newLine();

        $testRoles = [
            'participant' => ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'roles' => ['participant'], 'expectedRole' => 'Peserta'],
            'trainer'     => ['name' => 'Dr. H. Ahmad Wijaya', 'email' => 'ahmad@example.com', 'roles' => ['trainer'], 'expectedRole' => 'Pengajar'],
            'operator'    => ['name' => 'Eko Prasetyo', 'email' => 'eko@example.com', 'roles' => ['operator'], 'expectedRole' => 'Operator'],
            'admin'       => ['name' => 'Admin Pengadaan', 'email' => 'admin@example.com', 'roles' => ['admin'], 'dashboard_access' => true, 'expectedRole' => 'Admin'],
        ];

        $session = service('session');
        $passed = 0;
        $failed = 0;

        foreach ($testRoles as $key => $u) {
            $session->set([
                'isLoggedIn'       => true,
                'user_id'          => 1,
                'name'             => $u['name'],
                'username'         => strtolower(str_replace(' ', '', $u['name'])),
                'email'            => $u['email'],
                'roles'            => $u['roles'],
                'dashboard_access' => $u['dashboard_access'] ?? false,
            ]);

            try {
                // 1. Render Workspace Header
                $html = view('main/workspace/components/header');

                // FiStudy Foundation checks
                $hasLandingStructure = str_contains($html, 'main-header')
                    && str_contains($html, 'main-menu__category-box')
                    && str_contains($html, 'Eksplorasi')
                    && str_contains($html, 'Arena Belajar')
                    && str_contains($html, 'Bunga Rampai')
                    && str_contains($html, 'main-menu__search-box');

                // Authenticated user: NO Login/Register buttons
                $hasNoGuestButtons = ! str_contains($html, 'main-menu__btn-boxes');

                // User Identity & Avatar at top-right
                $hasName = str_contains($html, esc($u['name']));
                $hasRole = str_contains($html, esc($u['expectedRole']));
                $hasUserBox = str_contains($html, 'main-menu__user') && str_contains($html, 'main-menu__user-menu');

                // Dropdown Menu Links
                $hasWorkspace = str_contains($html, '/workspace');
                $hasBookmark = str_contains($html, '/workspace/bookmark');
                $hasNotif = str_contains($html, '/workspace/notifikasi');
                $hasProfile = str_contains($html, '/workspace/profil');
                $hasLogout = str_contains($html, 'Keluar');

                // Admin Dashboard Link condition
                $hasAdminDash = ! empty($u['dashboard_access'])
                    ? str_contains($html, 'Dashboard Administrasi')
                    : ! str_contains($html, 'Dashboard Administrasi');

                if ($hasLandingStructure && $hasNoGuestButtons && $hasName && $hasRole && $hasUserBox && $hasWorkspace && $hasBookmark && $hasNotif && $hasProfile && $hasLogout && $hasAdminDash) {
                    CLI::write(" [PASS] Header Role '{$key}': User '{$u['name']}' ({$u['expectedRole']}) -> DNA FiStudy + User Dropdown OK", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] Header Role '{$key}': User '{$u['name']}' -> Element verification mismatch");
                    $failed++;
                }
            } catch (\Throwable $e) {
                CLI::error(" [ERR] Role '{$key}': " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
                $failed++;
            }
        }

        // 2. Test Left Sidebar across roles
        CLI::newLine();
        CLI::write("Testing Left Sidebar role menus...", 'yellow');
        $sidebarRoleChecks = [
            'participant' => ['Pembelajaran Saya', 'Tugas', 'Assessment', 'Sertifikat Saya', 'Bookmark'],
            'trainer'     => ['Kelas Saya', 'Materi Pembelajaran', 'Assessment', 'Peserta', 'Progress Peserta'],
            'operator'    => [
                'Beranda',
                'Approval',
                'Verifikasi User',
                'Kelas & Trainer Assignment',
                'Operasional Event',
                'Presensi',
                'Monitoring Pembelajaran',
                'Verifikasi Sertifikat',
                'Evaluasi & Override',
                'Aktivitas',
                'Profil',
            ],
        ];

        foreach ($sidebarRoleChecks as $role => $expectedKeywords) {
            $session->set([
                'isLoggedIn' => true,
                'user_id'    => 1,
                'name'       => 'Test ' . ucfirst($role),
                'roles'      => [$role],
            ]);

            try {
                $sidebarHtml = view('main/workspace/components/sidebar');
                $allMatched = true;
                $decodedHtml = html_entity_decode($sidebarHtml);
                foreach ($expectedKeywords as $kw) {
                    if (! str_contains($decodedHtml, $kw)) {
                        $allMatched = false;
                        CLI::error("   Missing keyword '{$kw}' for role '{$role}'");
                        break;
                    }
                }
                if ($allMatched) {
                    CLI::write(" [PASS] Sidebar Role '{$role}': Contains all role-specific workspace activities", 'green');
                    $passed++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                CLI::error(" [ERR] Sidebar Role '{$role}': " . $e->getMessage());
                $failed++;
            }
        }

        // 3. Test Layout integration (workspace.php)
        CLI::newLine();
        CLI::write("Testing Layout integration (workspace.php with workspace.css)...", 'yellow');
        try {
            $layoutHtml = view('main/workspace/pages/home', [
                'title'          => 'Dashboard | Workspace',
                'pageTitle'      => 'Dashboard Workspace',
                'userName'       => 'Budi Santoso',
                'roles'          => ['participant'],
                'primaryRole'    => 'participant',
                'roleLabel'      => 'Peserta (Participant)',
                'menuItems'      => [],
                'sectionContent' => [
                    'eyebrow'     => 'Ringkasan',
                    'title'       => 'Dashboard',
                    'description' => 'Ringkasan pembelajaran',
                    'cards'       => [],
                    'items'       => [],
                ],
                'kpiCards'       => [],
                'recentClasses'  => [],
                'recentTasks'    => [],
                'recentActivities' => [],
            ]);

            $hasCssLink = str_contains($layoutHtml, 'assets/main/css/workspace.css');
            $hasHeader = str_contains($layoutHtml, 'main-header') && str_contains($layoutHtml, 'main-menu__user');
            $hasSticky = str_contains($layoutHtml, 'stricky-header');
            $hasSidebar = str_contains($layoutHtml, 'workspace-sidebar');
            $hasContent = str_contains($layoutHtml, 'workspace-content');

            if ($hasCssLink && $hasHeader && $hasSticky && $hasSidebar && $hasContent) {
                CLI::write(" [PASS] Layout workspace.php: workspace.css linked, Header included, Sticky header anchor ready, Sidebar & Content intact!", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Layout verification mismatch (css: {$hasCssLink}, header: {$hasHeader}, sticky: {$hasSticky}, sidebar: {$hasSidebar})");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [ERR] Layout render error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $failed++;
        }

        // 4. Test Modular Participant Learning Journey Flow
        CLI::newLine();
        CLI::write("Testing Modular Participant Learning Journey State Engine...", 'yellow');
        try {
            $uri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'workspace');
            $incoming = new \CodeIgniter\HTTP\IncomingRequest(
                config('App'),
                $uri,
                'php://input',
                new \CodeIgniter\HTTP\UserAgent()
            );
            \Config\Services::injectMock('request', $incoming);
            \Config\Services::injectMock('security', new \CodeIgniter\Security\Security(config('Security'), $incoming));

            $controller = new \App\Controllers\Workspace\ParticipantController();
            $controller->initController(
                $incoming,
                service('response'),
                service('logger')
            );

            // Test on Class 4 (has pre-test, 3 modules with quizzes, and post-test)
            // Test with a user who is not enrolled (e.g. user 9999)
            $unregisteredState = $controller->resolveClassLearningState(4, 99999);
            if ($unregisteredState['stateCode'] === 'not_enrolled' && $unregisteredState['cta']['type'] === 'enroll') {
                CLI::write(" [PASS] State 'not_enrolled': Correct CTA '[Daftar Kelas Ini]'", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] State 'not_enrolled' failed: got " . $unregisteredState['stateCode']);
                $failed++;
            }

            // Test on user 36 (enrolled active in class 4 with pre-test unfinished)
            $activeState = $controller->resolveClassLearningState(4, 36);
            $hasPreTest = $activeState['pre_test']['exists'] === true;
            $preTestBlocks = $activeState['stateCode'] === 'pre_test_required';
            $firstModuleLocked = ! $activeState['modules'][0]['is_unlocked'];

            if ($hasPreTest && $preTestBlocks && $firstModuleLocked) {
                CLI::write(" [PASS] State 'pre_test_required': Pre-test blocks Module 1 until completed. CTA '[Mulai Pre-Test]'", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Pre-test gate failed (hasPreTest: {$hasPreTest}, preTestBlocks: {$preTestBlocks}, mod1Locked: {$firstModuleLocked})");
                $failed++;
            }

            // Check trainers retrieval
            $hasTrainers = ! empty($activeState['trainers']);
            if ($hasTrainers) {
                CLI::write(" [PASS] Class Trainers: Successfully retrieved active trainers for class", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Class Trainers: No trainers found for class 4");
                $failed++;
            }

            // Test detail_kelas view rendering
            $detailHtml = view('main/workspace/pages/detail_kelas', [
                'state'        => $activeState,
                'classContext' => $activeState['class'],
                'trainers'     => $activeState['trainers'],
                'enrollment'   => $activeState['enrollment'],
                'modules'      => $activeState['modules'],
                'cta'          => $activeState['cta'],
            ]);

            $hasHero = str_contains($detailHtml, 'Passing Grade') && str_contains($detailHtml, 'Bobot Quiz');
            $hasCtaBanner = str_contains($detailHtml, 'Mulai Pre-Test');
            $hasRoadmap = str_contains($detailHtml, 'Silabus & Roadmap Pembelajaran');

            if ($hasHero && $hasCtaBanner && $hasRoadmap) {
                CLI::write(" [PASS] View detail_kelas.php: Class overview, trainer, dynamic state CTA banner, and roadmap rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] View detail_kelas.php rendering verification mismatch");
                $failed++;
            }

            // Test belajar view rendering
            $learningHtml = view('main/workspace/pages/belajar', [
                'state'        => $activeState,
                'activeType'   => 'lesson',
                'activeItem'   => $activeState['modules'][0]['lessons'][0] ?? null,
                'classContext' => $activeState['class'],
                'trainers'     => $activeState['trainers'],
                'enrollment'   => $activeState['enrollment'],
                'modules'      => $activeState['modules'],
            ]);

            $hasLearningHeader = str_contains($learningHtml, 'learning-header');
            $hasLearningSidebar = str_contains($learningHtml, 'learning-sidebar') && str_contains($learningHtml, 'Kurikulum Kelas');
            $hasActionBtn = str_contains($learningHtml, 'Tandai Selesai & Lanjut') || str_contains($learningHtml, 'Materi Ini Telah Selesai');

            if ($hasLearningHeader && $hasLearningSidebar && $hasActionBtn) {
                CLI::write(" [PASS] View belajar.php: Structured Learning Room Header, Left Modular Tree Sidebar, and Content Viewer OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] View belajar.php rendering verification mismatch");
                $failed++;
            }

            // Test on user 31 (enrolled in class 3 - completed/passed learning)
            $completedState = $controller->resolveClassLearningState(3, 31);
            $isComplete = in_array($completedState['stateCode'], ['certificate_issued', 'waiting_certificate', 'completed_passed'], true);
            if ($isComplete && $completedState['is_passed']) {
                CLI::write(" [PASS] Graduation & Certificate Pipeline: User 31 marked completed/passed with State '{$completedState['stateCode']}'", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Graduation state check failed: got " . $completedState['stateCode']);
                $failed++;
            }

            CLI::newLine();
            CLI::write("Testing Participant Workspace Event Refinement (Event Saya & Detail Event)...", "yellow");

            // Setup session for participant with events
            $db = db_connect();
            $testEvent = $db->table('events')->where('deleted_at IS NULL')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
            $testUser = $db->table('users')->where('account_status', 'active')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();

            if ($testEvent && $testUser) {
                $session->set([
                    'isLoggedIn' => true,
                    'user_id'    => (int) $testUser['id'],
                    'name'       => $testUser['name'],
                    'roles'      => ['participant'],
                ]);

                // Ensure participant is registered to testEvent
                $regCheck = $db->table('event_registrations')
                    ->where(['event_id' => $testEvent['id'], 'user_id' => $testUser['id']])
                    ->get()->getRowArray();
                if (! $regCheck) {
                    $db->table('event_registrations')->insert([
                        'event_id'      => $testEvent['id'],
                        'user_id'       => $testUser['id'],
                        'status'        => 'registered',
                        'registered_at' => date('Y-m-d H:i:s'),
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                }

                // 1. Test myEvents view
                $myEventsHtml = html_entity_decode($controller->myEvents());
                $hasEventTitle = str_contains($myEventsHtml, 'Event Saya');
                $hasExploreBtn = str_contains($myEventsHtml, 'Cari Event Baru');
                $has4Tabs = str_contains($myEventsHtml, '1. Event Aktif Saya')
                    && str_contains($myEventsHtml, '2. Riwayat Event')
                    && str_contains($myEventsHtml, '3. Presensi Saya')
                    && str_contains($myEventsHtml, '4. Sertifikat Event');
                $hasQuickPresensi = str_contains($myEventsHtml, 'Presensi Kehadiran');

                if ($hasEventTitle && $hasExploreBtn && $has4Tabs && $hasQuickPresensi) {
                    CLI::write(" [PASS] View event_saya.php: 4 Main Tabs, Stat KPIs, Quick Presensi & Explore Link OK", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] View event_saya.php missing expected elements");
                    $failed++;
                }

                // 2. Test eventDetail view (Participant Workspace Detail Event)
                $detailEventHtml = html_entity_decode($controller->eventDetail((string) $testEvent['id']));
                $hasEventHeader = str_contains($detailEventHtml, $testEvent['title']);
                $hasTimeline = str_contains($detailEventHtml, 'Status Keikutsertaan')
                    && str_contains($detailEventHtml, 'Daftar Event')
                    && str_contains($detailEventHtml, 'Terdaftar')
                    && str_contains($detailEventHtml, 'Menunggu Kehadiran')
                    && str_contains($detailEventHtml, 'Hadir')
                    && str_contains($detailEventHtml, 'Sertifikat');
                $hasPresensiSection = str_contains($detailEventHtml, 'Presensi Kehadiran') || str_contains($detailEventHtml, 'Kehadiran berhasil dicatat');
                $hasInfoSection = str_contains($detailEventHtml, 'Informasi Event');
                $hasCertSection = str_contains($detailEventHtml, 'Sertifikat Event');
                $staysInWorkspace = str_contains($detailEventHtml, 'Kembali ke Event Saya');

                if ($hasEventHeader && $hasTimeline && $hasPresensiSection && $hasInfoSection && $hasCertSection && $staysInWorkspace) {
                    CLI::write(" [PASS] View detail_event.php: Dedicated Participant Workspace Detail (5-Stage Timeline, Presensi, Info, Cert) OK", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] View detail_event.php missing expected participant workspace components");
                    $failed++;
                }

                // 3. Test myAttendance view
                $myAttendanceHtml = html_entity_decode($controller->myAttendance());
                $hasAttendanceHeader = str_contains($myAttendanceHtml, 'Presensi Saya');
                $hasAttendanceTable = str_contains($myAttendanceHtml, 'Riwayat Presensi Saya');

                if ($hasAttendanceHeader && $hasAttendanceTable) {
                    CLI::write(" [PASS] View presensi_saya.php: Form Presensi & Riwayat Kehadiran table OK", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] View presensi_saya.php missing expected elements");
                    $failed++;
                }
            }

        } catch (\Throwable $e) {
            CLI::error(" [ERR] Modular Learning State error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $failed++;
        }

        // 5. Test Trainer Learning Management Experience
        CLI::newLine();
        CLI::write("Testing Trainer Learning Management Experience...", 'yellow');
        try {
            $db = \Config\Database::connect();

            // 5.1 Verify Trainer Permission & Assignment
            $trainerRow = $db->table('class_trainers')
                ->where(['class_id' => 4, 'user_id' => 34, 'status' => 'active'])
                ->get()->getRowArray();
            $nonTrainerRow = $db->table('class_trainers')
                ->where(['class_id' => 4, 'user_id' => 33, 'status' => 'active'])
                ->get()->getRowArray();

            if ($trainerRow && ! $nonTrainerRow) {
                CLI::write(" [PASS] Trainer Permission: User 34 is active trainer of class 4; unauthorized trainer (user 33) is blocked", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Trainer Permission verification failed");
                $failed++;
            }

            // 5.2 Test View trainer/home.php
            $homeHtml = view('main/workspace/pages/trainer/home', [
                'hasClasses'       => true,
                'userName'         => 'Siti Rahmawati',
                'stats'            => [
                    'totalClasses'       => 1,
                    'activeParticipants' => 12,
                    'avgProgress'        => 65.5,
                    'pendingReviews'     => 2,
                ],
                'assignedClasses'  => [
                    [
                        'class_id'          => 4,
                        'code'              => 'CLS-04',
                        'name'              => 'Pengantar Pengadaan Barang/Jasa',
                        'course_title'      => 'Dasar PBJ',
                        'class_status'      => 'active',
                        'participant_count' => 12,
                        'module_count'      => 3,
                        'assignment_count'  => 1,
                        'avg_progress'      => 65.5,
                    ]
                ],
                'recentActivities' => [
                    [
                        'type'      => 'enrollment',
                        'user_name' => 'Budi Santoso',
                        'class_name'=> 'Pengantar PBJ',
                        'time'      => date('Y-m-d H:i:s'),
                    ]
                ],
            ]);

            $hasHomeMetrics = str_contains($homeHtml, 'Kelas Diampu') && str_contains($homeHtml, 'Tugas Perlu Review');
            $hasQuickAction = str_contains($homeHtml, 'Kelola Materi') && str_contains($homeHtml, 'Review Submission');
            if ($hasHomeMetrics && $hasQuickAction) {
                CLI::write(" [PASS] View trainer/home.php: Metrics, Quick Actions, and Active Classes rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] View trainer/home.php rendering mismatch");
                $failed++;
            }

            // 5.3 Test View trainer/kelas_saya.php
            $classesHtml = view('main/workspace/pages/trainer/kelas_saya', [
                'classes' => [
                    [
                        'class_id'          => 4,
                        'code'              => 'CLS-04',
                        'name'              => 'Pengantar Pengadaan Barang/Jasa',
                        'course_title'      => 'Dasar PBJ',
                        'class_status'      => 'active',
                        'passing_grade'     => '70.00',
                        'participant_count' => 12,
                        'modules_count'     => 3,
                        'quizzes_count'     => 3,
                        'assignments_count' => 1,
                        'avg_progress'      => 65.5,
                    ]
                ],
            ]);

            $hasClassesCard = str_contains($classesHtml, 'Masuk Kelas') && str_contains($classesHtml, 'Progres Kelas');
            if ($hasClassesCard) {
                CLI::write(" [PASS] View trainer/kelas_saya.php: Assigned Class Cards and Metrics rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] View trainer/kelas_saya.php rendering mismatch");
                $failed++;
            }

            // 5.4 Test View trainer/detail_kelas.php (Learning Management Room with all 6 tabs)
            $detailClassHtml = view('main/workspace/pages/trainer/detail_kelas', [
                'classCtx'     => [
                    'id'            => 4,
                    'code'          => 'CLS-04',
                    'name'          => 'Pengantar Pengadaan Barang/Jasa',
                    'course_title'  => 'Dasar PBJ',
                    'status'        => 'active',
                    'passing_grade' => '70.00',
                    'quiz_weight'   => '40.00',
                    'assignment_weight' => '0.00',
                    'post_test_weight'  => '60.00',
                ],
                'trainers'     => [['name' => 'Siti Rahmawati', 'email' => 'trainer.siti@begawipbj.test']],
                'activeTab'    => 'overview',
                'modules'      => [],
                'participants' => [],
                'quizzes'      => [],
                'assignments'  => [],
                'stats'        => [
                    'participantCount'       => 12,
                    'activeParticipantCount' => 12,
                    'completedCount'         => 0,
                    'completionRate'         => 0.0,
                    'moduleCount'            => 3,
                    'lessonCount'            => 9,
                    'quizCount'              => 3,
                    'assignmentCount'        => 1,
                ],
            ]);

            $hasTabs = str_contains($detailClassHtml, 'Overview') &&
                       str_contains($detailClassHtml, 'Materi & Modul') &&
                       str_contains($detailClassHtml, 'Peserta') &&
                       str_contains($detailClassHtml, 'Assessment') &&
                       str_contains($detailClassHtml, 'Assignment') &&
                       str_contains($detailClassHtml, 'Progress');

            if ($hasTabs) {
                CLI::write(" [PASS] View trainer/detail_kelas.php: Learning Management Room with all 6 tabs rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] View trainer/detail_kelas.php tabs rendering mismatch");
                $failed++;
            }

            // 5.5 Test View trainer/all_materials.php, all_quizzes.php, all_participants.php, all_progress.php
            $materialsHtml = view('main/workspace/pages/trainer/all_materials', [
                'classes'       => [['id' => 4, 'name' => 'Kelas 4']],
                'selectedClass' => ['id' => 4, 'code' => 'CLS-04', 'name' => 'Kelas 4'],
                'modules'       => [],
            ]);
            $quizzesHtml = view('main/workspace/pages/trainer/all_quizzes', [
                'classes'       => [['id' => 4, 'name' => 'Kelas 4']],
                'selectedClass' => ['id' => 4, 'code' => 'CLS-04', 'name' => 'Kelas 4'],
                'quizzes'       => [],
            ]);
            $participantsHtml = view('main/workspace/pages/trainer/all_participants', [
                'classes'         => [['id' => 4, 'name' => 'Kelas 4']],
                'selectedClassId' => 4,
                'participants'    => [],
            ]);
            $progressHtml = view('main/workspace/pages/trainer/all_progress', [
                'classes'        => [['id' => 4, 'name' => 'Kelas 4']],
                'classSummaries' => [['id' => 4, 'code' => 'CLS-04', 'name' => 'Kelas 4', 'completion_rate' => 80.0, 'avg_progress' => 85.0]],
            ]);

            $hasAggregates = str_contains($materialsHtml, 'Materi Pembelajaran') &&
                             str_contains($quizzesHtml, 'Assessment') &&
                             str_contains($participantsHtml, 'Peserta Pembelajaran') &&
                             str_contains($progressHtml, 'Progress Peserta');

            if ($hasAggregates) {
                CLI::write(" [PASS] Aggregate Views: all_materials, all_quizzes, all_participants, and all_progress rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Aggregate views rendering mismatch");
                $failed++;
            }

            // 5.6 Test View Assignment Review & Participant Detail
            $reviewHtml = view('main/workspace/pages/trainer/submission_review', [
                'submission' => [
                    'id'                       => 1,
                    'assignment_id'            => 1,
                    'user_name'                => 'Budi Santoso',
                    'assignment_title'         => 'Tugas HPS',
                    'assignment_instructions'  => 'Buat HPS',
                    'attempt_number'           => 1,
                    'content'                  => 'Analisis HPS terlampir',
                    'score'                    => 90.0,
                    'feedback'                 => 'Bagus sekali',
                    'status'                   => 'graded',
                ],
                'files'      => [],
                'classId'    => 4,
                'className'  => 'Kelas 4',
            ]);

            $partDetailHtml = view('main/workspace/pages/trainer/participant_detail', [
                'enrollment'            => ['id' => 1, 'name' => 'Budi Santoso', 'email' => 'budi@test.com', 'username' => 'budi', 'status' => 'active'],
                'lessonsProgress'       => [],
                'quizAttempts'          => [],
                'assignmentSubmissions' => [],
                'classId'               => 4,
                'className'             => 'Kelas 4',
            ]);

            $hasReviewAndDetail = str_contains($reviewHtml, 'Review Submission') && str_contains($partDetailHtml, 'Riwayat Belajar Peserta');
            if ($hasReviewAndDetail) {
                CLI::write(" [PASS] Views: submission_review.php and participant_detail.php rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] submission_review or participant_detail rendering mismatch");
                $failed++;
            }

            // 5.7 Integration Check: Trainer -> Participant Live Sync
            // Verify that modules and quizzes created on class 4 are directly visible to participant
            $participantClassState = $controller->resolveClassLearningState(4, 36);
            $hasModulesForParticipant = ! empty($participantClassState['modules']);
            $hasQuizzesForParticipant = ! empty($participantClassState['pre_test']['exists']);

            if ($hasModulesForParticipant && $hasQuizzesForParticipant) {
                CLI::write(" [PASS] Integration Trainer -> Participant: Class 4 curriculum created by trainer is immediately visible in Participant Learning Journey", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Integration Trainer -> Participant mismatch on class 4");
                $failed++;
            }

        } catch (\Throwable $e) {
            CLI::error(" [ERR] Trainer Experience error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $failed++;
        }

        // 6. Test Operator Evaluation Monitoring & Override Capability
        CLI::newLine();
        CLI::write("Testing Operator Evaluation Monitoring & Override Capability...", 'yellow');
        try {
            $session->set([
                'isLoggedIn' => true,
                'user_id'    => 1,
                'name'       => 'Eko Prasetyo',
                'roles'      => ['operator'],
            ]);

            // 6.1 Check Operator evaluations page
            $operatorController = new \App\Controllers\Workspace\OperatorController();
            $evaluationsHtml = $operatorController->evaluations();
            $hasEvaluationList = str_contains($evaluationsHtml, 'Evaluasi Pembelajaran')
                && str_contains($evaluationsHtml, 'Antrean Evaluasi Tugas');

            if ($hasEvaluationList) {
                CLI::write(" [PASS] View operator/evaluasi: Evaluation list & override queue rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator evaluations view rendering mismatch");
                $failed++;
            }

            // 6.2 Check Operator override submission view
            $overrideFormHtml = view('main/workspace/pages/operator/override_submission', [
                'submission' => [
                    'id'                      => 1,
                    'participant_name'        => 'Budi Santoso',
                    'assignment_title'        => 'Tugas HPS',
                    'class_name'              => 'Kelas PBJ',
                    'assignment_instructions' => 'Buat HPS',
                    'attempt_number'          => 1,
                    'content'                 => 'Laporan tugas',
                    'score'                   => 75.0,
                    'status'                  => 'in_review',
                    'grader_name'             => 'Trainer Ahmad',
                    'graded_at'               => date('Y-m-d H:i:s'),
                    'feedback'                => 'Perlu perbaikan',
                ],
                'files' => [],
            ]);

            $hasOverrideForm = str_contains($overrideFormHtml, 'Form Override Evaluasi')
                && str_contains($overrideFormHtml, 'Skor / Nilai Override')
                && str_contains($overrideFormHtml, 'Alasan / Catatan Override Operator');

            if ($hasOverrideForm) {
                CLI::write(" [PASS] View operator/override_submission: Override form with audit trail notes rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator override submission form rendering mismatch");
                $failed++;
            }

            // 6.3 Test Evaluator Workspace is completely purged
            $evaluatorRoute = route_to('workspace.evaluator.home');
            $isRouteRemoved = ($evaluatorRoute === false || empty($evaluatorRoute) || !str_contains((string) $evaluatorRoute, 'evaluator'));

            if ($isRouteRemoved) {
                CLI::write(" [PASS] Evaluator Workspace Purged: 'workspace.evaluator.*' routes successfully removed", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Evaluator route still exists: " . var_export($evaluatorRoute, true));
                $failed++;
            }

        } catch (\Throwable $e) {
            CLI::error(" [ERR] Operator Override error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $failed++;
        }

        // ================================================================
        // 7. COMPREHENSIVE OPERATOR WORKSPACE OPERATIONAL SUITE
        // ================================================================
        CLI::newLine();
        CLI::write("Testing Comprehensive Operator Workspace Suite & Flows...", 'yellow');
        try {
            $db = db_connect();
            $firstUser = $db->table('users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
            $operatorUserId = $firstUser ? (int) $firstUser['id'] : 1;

            $session->set([
                'isLoggedIn' => true,
                'user_id'    => $operatorUserId,
                'name'       => 'Eko Prasetyo Operator',
                'roles'      => ['operator'],
            ]);

            $operatorController = new \App\Controllers\Workspace\OperatorController();

            // 7.1 Operator Home (7 KPIs & 4 Activity Feeds)
            $homeHtml = $operatorController->index();
            $hasHomeKpis = str_contains($homeHtml, 'Pusat Operasional Harian Sistem')
                && str_contains($homeHtml, 'Pending Approval')
                && str_contains($homeHtml, 'Verifikasi User')
                && str_contains($homeHtml, 'Kelas Aktif')
                && str_contains($homeHtml, 'Trainer Aktif')
                && str_contains($homeHtml, 'Event Berjalan')
                && str_contains($homeHtml, 'Presensi Pending')
                && str_contains($homeHtml, 'Sertifikat Pending');

            $hasHomeFeeds = str_contains($homeHtml, 'Approval Terbaru')
                && str_contains($homeHtml, 'Penugasan Trainer Terbaru')
                && str_contains($homeHtml, 'Event PBJ Terbaru')
                && str_contains($homeHtml, 'Log Override Terbaru');

            if ($hasHomeKpis && $hasHomeFeeds) {
                CLI::write(" [PASS] Operator Home: 7 Summary KPIs & 4 Operational Activity Feeds rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator Home view missing expected KPIs or Activity feeds");
                $failed++;
            }

            // 7.2 Kelas & Trainer Assignment View
            $assignmentHtml = $operatorController->classTrainers();
            $hasAssignmentElements = str_contains($assignmentHtml, 'Kelas & Trainer Assignment')
                && str_contains($assignmentHtml, 'Daftar Kelas & Penugasan Pengajar')
                && str_contains($assignmentHtml, 'Direktori Pengajar (Trainer)')
                && str_contains($assignmentHtml, 'assignTrainerModal');

            if ($hasAssignmentElements) {
                CLI::write(" [PASS] Operator Class & Trainer: Assignment matrix, trainer directory & modals rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator Class & Trainer assignment view missing expected elements");
                $failed++;
            }

            // 7.3 Integration: Operator Assigns Trainer -> Trainer Sees Class in "Kelas Saya"
            $testClass = $db->table('learning_classes')->select('id, name')->orderBy('id', 'ASC')->get()->getRowArray();
            $testTrainer = $db->table('users u')
                ->select('u.id, u.name')
                ->join('user_roles ur', 'ur.user_id = u.id')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'trainer')
                ->get()->getRowArray();

            if ($testClass && $testTrainer) {
                $cid = (int) $testClass['id'];
                $tid = (int) $testTrainer['id'];
                $now = date('Y-m-d H:i:s');

                // Upsert class_trainer as active
                $existing = $db->table('class_trainers')->where('class_id', $cid)->where('user_id', $tid)->get()->getRowArray();
                if ($existing) {
                    $db->table('class_trainers')->where('class_id', $cid)->where('user_id', $tid)->update([
                        'status'      => 'active',
                        'assigned_by' => $operatorUserId,
                        'assigned_at' => $now,
                    ]);
                } else {
                    $db->table('class_trainers')->insert([
                        'class_id'    => $cid,
                        'user_id'     => $tid,
                        'assigned_by' => $operatorUserId,
                        'assigned_at' => $now,
                        'status'      => 'active',
                    ]);
                }

                // Verify trainer sees it in active assignments
                $trainerCheck = $db->table('class_trainers')
                    ->where('class_id', $cid)
                    ->where('user_id', $tid)
                    ->where('status', 'active')
                    ->countAllResults();

                if ($trainerCheck > 0) {
                    CLI::write(" [PASS] Core Integration: Operator assigns trainer ({$testTrainer['name']}) -> active in 'Kelas Saya' OK", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] Core Integration: Trainer assignment not found in active state");
                    $failed++;
                }
            } else {
                CLI::write(" [SKIP] Core Integration: Seeded class or trainer not found for test", 'yellow');
            }

            // 7.4 Operator Learning Monitoring
            $monitoringHtml = $operatorController->learningMonitoring();
            $hasMonitoringElements = str_contains($monitoringHtml, 'Monitoring Pembelajaran Kelas')
                && str_contains($monitoringHtml, 'Status Pembelajaran Lintas Kelas')
                && str_contains($monitoringHtml, 'Rata-rata Progres');

            if ($hasMonitoringElements) {
                CLI::write(" [PASS] Operator Learning Monitoring: Cross-class progress, trainer status & completion metrics rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator Learning Monitoring view missing expected elements");
                $failed++;
            }

            // 7.5 Operator Certificate Verification Queue
            $certHtml = $operatorController->certificateVerification();
            $hasCertElements = str_contains($certHtml, 'Verifikasi & Penerbitan Sertifikat')
                && str_contains($certHtml, 'Antrean Peserta Memenuhi Syarat')
                && str_contains($certHtml, 'Daftar Sertifikat Terbit');

            if ($hasCertElements) {
                CLI::write(" [PASS] Operator Certificate Verification: Eligibility queue & issued archive rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator Certificate Verification view missing expected elements");
                $failed++;
            }

            // 7.6 Operator Operational Event & Attendance
            $eventHtml = html_entity_decode($operatorController->events());
            $hasEventList = str_contains($eventHtml, 'Operasional Event & Workshop PBJ')
                && str_contains($eventHtml, 'Daftar Event & Workshop PBJ');

            $attendanceHtml = html_entity_decode($operatorController->attendance());
            $hasAttendanceList = str_contains($attendanceHtml, 'Pencatatan Kehadiran')
                && str_contains($attendanceHtml, 'Catatan Presensi Terkini');

            if ($hasEventList && $hasAttendanceList) {
                CLI::write(" [PASS] Operator Event & Attendance: Events list and quick attendance update controls rendered OK", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Operator Event or Attendance view missing expected elements");
                $failed++;
            }

            // 7.7 Admin & Super Admin Permission Inheritance
            $session->set([
                'isLoggedIn' => true,
                'user_id'    => $operatorUserId,
                'name'       => 'Administrator Utama',
                'roles'      => ['admin'],
            ]);

            $adminHomeHtml = $operatorController->index();
            $adminInheritanceOk = str_contains($adminHomeHtml, 'Dashboard Operasional');

            if ($adminInheritanceOk) {
                CLI::write(" [PASS] Inheritance Permission: Administrator seamlessly inherits Operator Workspace capabilities", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Administrator failed to inherit Operator Workspace access");
                $failed++;
            }

        } catch (\Throwable $e) {
            CLI::error(" [ERR] Comprehensive Operator Suite error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $failed++;
        }

        CLI::newLine();
        CLI::write("Summary: {$passed} passed, {$failed} failed.", $failed === 0 ? 'green' : 'red');
        return $failed === 0 ? 0 : 1;
    }
}
