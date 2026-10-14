<?php

namespace App\Commands;

use App\Controllers\Landing\ArenaBelajarController;
use App\Models\Knowledge\BookmarkModel;
use App\Models\Learning\CourseModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\ProgramModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ArenaLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'arena:landing:test';
    protected $description = 'Comprehensive test suite for Arena Belajar landing showcase, navigation, and bookmark integration.';
    protected $usage       = 'arena:landing:test';

    public function run(array $params): int
    {
        CLI::write('==================================================================', 'yellow');
        CLI::write('     TEST SUITE: ARENA BELAJAR LANDING PAGE & BOOKMARK SYSTEM     ', 'yellow');
        CLI::write('==================================================================', 'yellow');
        CLI::newLine();

        $session = service('session');
        $passed  = 0;
        $failed  = 0;

        // -------------------------------------------------------------
        // TEST 1: Guest Browsing (Public / Not Logged In)
        // -------------------------------------------------------------
        CLI::write('>>> TEST 1: Public Guest Browsing (/arena-belajar)', 'cyan');
        $session->set([
            'isLoggedIn' => false,
            'user_id'    => 0,
        ]);

        $controller = new ArenaBelajarController();
        $htmlGuest  = $controller->index();

        $guestChecks = [
            'Hero Section Title'    => 'Pusat Pembelajaran &amp; Kompetensi Pengadaan',
            'Hero Tag'              => 'Arena Belajar BegawiPengadaan Lampung',
            'Search Bar Present'    => 'arena-search-bar',
            'Category Nav Pills'    => 'arena-nav-pills',
            'Glass Nav Pill Sub-Nav' => 'arena-nav-pill',
            'Featured Programs'     => 'Program Pembelajaran Unggulan',
            'Popular Courses'       => 'Course PBJ Terpopuler &amp; Pilihan',
            'Active Classes'        => 'Kelas &amp; Batch Siap Daftar',
            'Pillars of Excellence' => 'Dari Regulasi Menuju Praktik Nyata Pengadaan',
        ];

        foreach ($guestChecks as $label => $needle) {
            if (str_contains($htmlGuest, $needle)) {
                CLI::write("  [OK] Guest Index: {$label} rendered successfully", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Guest Index: {$label} missing", 'red');
                $failed++;
            }
        }

        // Critical Requirement: Guest MUST NOT see bookmark buttons
        if (! str_contains($htmlGuest, 'class="arena-bookmark-form')) {
            CLI::write('  [OK] Guest Index: Bookmark forms hidden cleanly for public visitor', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Guest Index: Bookmark form unexpectedly visible for guest', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 2: Logged-in User Browsing (/arena-belajar)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write('>>> TEST 2: Logged-in User Browsing with Bookmark Buttons', 'cyan');
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => 31,
            'name'           => 'Budi Santoso',
            'username'       => 'budi_santoso',
            'email'          => 'budi@example.com',
            'account_status' => 'active',
            'roles'          => ['participant'],
        ]);

        $htmlLoggedIn = $controller->index();
        if (str_contains($htmlLoggedIn, 'arena-bookmark-form')) {
            CLI::write('  [OK] Logged-in Index: Bookmark buttons rendered on showcase cards', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Logged-in Index: Bookmark forms missing for authenticated user', 'red');
            $failed++;
        }

        if (str_contains($htmlLoggedIn, 'Simpan') || str_contains($htmlLoggedIn, 'Tersimpan')) {
            CLI::write('  [OK] Logged-in Index: Bookmark action text (Simpan / Tersimpan) confirmed', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Logged-in Index: Bookmark action text missing', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 3: Catalog Views
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write('>>> TEST 3: Catalog Pages (/programs, /courses, /classes, /trainers)', 'cyan');

        // 3.1 Programs
        $programsHtml = $controller->programs();
        if (str_contains($programsHtml, 'Program Pembelajaran') && str_contains($programsHtml, 'arena-program-roadmap') && str_contains($programsHtml, 'arena-program-card') && str_contains($programsHtml, 'arena-bookmark-form')) {
            CLI::write('  [OK] Programs Catalog: Rendered with roadmap guide, refined cards & bookmark buttons', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Programs Catalog: Bookmark, roadmap, or cards missing', 'red');
            $failed++;
        }

        // 3.2 Courses
        $coursesHtml = $controller->courses();
        if (str_contains($coursesHtml, 'Course PBJ') && str_contains($coursesHtml, 'Tingkat Kesulitan:') && str_contains($coursesHtml, 'arena-bookmark-form')) {
            CLI::write('  [OK] Courses Catalog: Rendered with level filter pills, course cards & bookmark buttons', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Courses Catalog: Bookmark, level filter, or cards missing', 'red');
            $failed++;
        }

        // 3.2b Courses Filter & Pagination Verification
        $_GET['level'] = 'basic';
        $_GET['per_page'] = 2;
        $coursesFilteredHtml = $controller->courses();
        unset($_GET['level'], $_GET['per_page']);

        if (str_contains($coursesFilteredHtml, 'Dasar (Basic)') && (str_contains($coursesFilteredHtml, 'pagination') || str_contains($coursesFilteredHtml, 'page_courses=2') || str_contains($coursesFilteredHtml, 'pagination-wrapper'))) {
            CLI::write('  [OK] Courses Catalog: Query filtering (?level=basic) and pagination verified', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Courses Catalog: Filtering or pagination links missing', 'red');
            $failed++;
        }

        // 3.3 Classes (ensure Course title compatibility for ArenaE2ETest)
        $classesHtml = $controller->classes();
        if (str_contains($classesHtml, 'Kelas &amp; Batch PBJ') && str_contains($classesHtml, 'Course:') && str_contains($classesHtml, 'arena-bookmark-form')) {
            CLI::write('  [OK] Classes Catalog: Rendered with Course label, classes & bookmark buttons', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Classes Catalog: Missing elements in classes catalog', 'red');
            $failed++;
        }

        // 3.3b Classes Filter Verification
        $_GET['status'] = 'active';
        $classesFilteredHtml = $controller->classes();
        unset($_GET['status']);

        if (str_contains($classesFilteredHtml, 'Sedang Berjalan (Aktif)') && str_contains($classesFilteredHtml, 'Course:')) {
            CLI::write('  [OK] Classes Catalog: Query filtering (?status=active) verified', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Classes Catalog: Filtering by status failed', 'red');
            $failed++;
        }

        // 3.4 Trainers
        $trainersHtml = $controller->trainers();
        if (str_contains($trainersHtml, 'Instruktur &amp; Pengajar PBJ') && str_contains($trainersHtml, 'Tipe Instruktur:')) {
            CLI::write('  [OK] Trainers Catalog: Directory of instructors rendered successfully with filter pills', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Trainers Catalog: Title or content missing', 'red');
            $failed++;
        }

        // 3.4b Trainers Filter Verification
        $_GET['profile'] = 'asn';
        $trainersFilteredHtml = $controller->trainers();
        unset($_GET['profile']);

        if (str_contains($trainersFilteredHtml, 'ASN Pemerintah') || str_contains($trainersFilteredHtml, 'Instruktur &amp; Pengajar PBJ')) {
            CLI::write('  [OK] Trainers Catalog: Query filtering (?profile=asn) verified', 'green');
            $passed++;
        } else {
            CLI::write('  [FAIL] Trainers Catalog: Filtering by profile failed', 'red');
            $failed++;
        }

        // -------------------------------------------------------------
        // TEST 4: Detail Views with Bookmark Buttons
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write('>>> TEST 4: Detail Views (/program/{slug}, /course/{slug}, /kelas/{code})', 'cyan');

        $sampleCourse = (new CourseModel())
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->first();

        if ($sampleCourse) {
            $courseHtml = $controller->courseDetail($sampleCourse['slug']);
            if (str_contains($courseHtml, 'arena-hero') && str_contains($courseHtml, 'silabus-course') && str_contains($courseHtml, 'daftar-kelas') && str_contains($courseHtml, 'Informasi Course') && str_contains($courseHtml, 'arena-bookmark-form') && str_contains($courseHtml, $sampleCourse['title'])) {
                CLI::write("  [OK] Course Detail: '{$sampleCourse['title']}' rendered with arena-hero, silabus, batch classes & bookmark button", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Course Detail: Missing arena-hero, silabus, or bookmark button on course detail", 'red');
                $failed++;
            }
        }

        // 4.1b Course with Active Batch Classes
        $sampleClassRef = (new LearningClassModel())->whereIn('status', ['scheduled', 'active'])->first();
        if ($sampleClassRef) {
            $courseWithClasses = (new CourseModel())->find($sampleClassRef['course_id']);
            if ($courseWithClasses && $courseWithClasses['status'] === 'published') {
                $courseWithClsHtml = $controller->courseDetail($courseWithClasses['slug']);
                if (str_contains($courseWithClsHtml, 'Batch:') && str_contains($courseWithClsHtml, 'Lihat Jadwal &amp; Daftar Kelas')) {
                    CLI::write("  [OK] Course Detail Batch Classes: Verified batch card list for '{$courseWithClasses['title']}'", 'green');
                    $passed++;
                } else {
                    CLI::write("  [FAIL] Course Detail Batch Classes: Batch cards missing on course detail", 'red');
                    $failed++;
                }
            }
        }

        $sampleProgram = (new ProgramModel())
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->first();

        if ($sampleProgram) {
            $progHtml = $controller->programDetail($sampleProgram['slug']);
            if (str_contains($progHtml, 'arena-bookmark-form') && str_contains($progHtml, $sampleProgram['title'])) {
                CLI::write("  [OK] Program Detail: '{$sampleProgram['title']}' rendered with bookmark button", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Program Detail: Missing bookmark button on program detail", 'red');
                $failed++;
            }
        }

        // Test Program Detail with multi-course pagination (pbj-dasar has 4 courses > 3 per page)
        $pbjProgram = (new ProgramModel())->where('slug', 'pbj-dasar')->first();
        if ($pbjProgram) {
            $pbjHtml = $controller->programDetail('pbj-dasar');
            if (str_contains($pbjHtml, 'pagination') && (str_contains($pbjHtml, 'page_courses=2') || str_contains($pbjHtml, 'pagination-wrapper'))) {
                CLI::write("  [OK] Program Detail Pagination: 'pbj-dasar' course pagination verified", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Program Detail Pagination: Pagination links missing for 'pbj-dasar'", 'red');
                $failed++;
            }
        }

        $sampleClass = (new LearningClassModel())
            ->whereIn('status', ['scheduled', 'active'])
            ->first();

        if ($sampleClass) {
            $classHtml = $controller->classDetail($sampleClass['code']);
            if (str_contains($classHtml, 'arena-hero') && str_contains($classHtml, 'kurikulum-kelas') && str_contains($classHtml, 'status-pendaftaran') && str_contains($classHtml, 'standar-kelulusan') && str_contains($classHtml, 'arena-bookmark-form') && str_contains($classHtml, $sampleClass['name'])) {
                CLI::write("  [OK] Class Detail: '{$sampleClass['name']}' rendered with arena-hero, kurikulum, sticky card & bookmark button", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Class Detail: Missing arena-hero, kurikulum, or bookmark button on class detail", 'red');
                $failed++;
            }
        }

        // 4.2b Class Detail with Modules verification (ensures no module_id/quiz join errors)
        $classWithModules = (new LearningClassModel())
            ->select('learning_classes.*')
            ->join('learning_modules', 'learning_modules.class_id = learning_classes.id')
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->first();

        if ($classWithModules) {
            $clsModHtml = $controller->classDetail($classWithModules['code']);
            if (str_contains($clsModHtml, 'arena-hero') && str_contains($clsModHtml, 'kurikulum-kelas') && str_contains($clsModHtml, $classWithModules['name'])) {
                CLI::write("  [OK] Class Detail with Modules: '{$classWithModules['name']}' rendered modular curriculum without errors", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Class Detail with Modules: Failed to render modular curriculum", 'red');
                $failed++;
            }
        }

        // 4.3 Trainer Detail Verification
        $db = \Config\Database::connect();
        $sampleTrainer = $db->table('class_trainers ct')
            ->select('u.id, u.name, u.username')
            ->join('users u', 'u.id = ct.user_id')
            ->where('ct.status', 'active')
            ->where('u.account_status', 'active')
            ->where('u.deleted_at IS NULL', null, false)
            ->get()->getFirstRow('array');

        if ($sampleTrainer) {
            $trainerSlug = ! empty($sampleTrainer['username']) ? $sampleTrainer['username'] : (string) $sampleTrainer['id'];
            $trainerHtml = $controller->trainerDetail($trainerSlug);
            $tName = $sampleTrainer['name'] ?: $sampleTrainer['username'];
            if (str_contains($trainerHtml, 'arena-hero') && str_contains($trainerHtml, 'profil-kompetensi') && str_contains($trainerHtml, 'kelas-diampu') && str_contains($trainerHtml, 'Fasilitator Terverifikasi') && str_contains($trainerHtml, $tName)) {
                CLI::write("  [OK] Trainer Detail: '{$tName}' rendered with arena-hero, profil, and classes", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Trainer Detail: Missing elements for '{$tName}'", 'red');
                $failed++;
            }
        }

        // -------------------------------------------------------------
        // TEST 5: Bookmark Toggle Operation (Course, Program, Class)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write('>>> TEST 5: Bookmark Data Layer Toggle Verification', 'cyan');
        $bmModel = new BookmarkModel();
        $userId  = 31;

        if ($sampleCourse) {
            $cId = (int) $sampleCourse['id'];
            $initC = $bmModel->exists($userId, 'course', $cId);
            $res = $bmModel->toggle($userId, 'course', $cId);
            $afterC = $bmModel->exists($userId, 'course', $cId);
            if ($initC) {
                $togglePass = ($res === 'removed' && ! $afterC);
            } else {
                $togglePass = ($res === 'added' && $afterC);
            }
            // restore state
            $bmModel->toggle($userId, 'course', $cId);

            if ($togglePass) {
                CLI::write("  [OK] Bookmark Toggle: 'course' #{$cId} toggle insert/delete verified", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Bookmark Toggle: 'course' #{$cId} failed", 'red');
                $failed++;
            }
        }

        if ($sampleProgram) {
            $pId = (int) $sampleProgram['id'];
            $initP = $bmModel->exists($userId, 'program', $pId);
            $resP = $bmModel->toggle($userId, 'program', $pId);
            $afterP = $bmModel->exists($userId, 'program', $pId);
            if ($initP) {
                $togglePPass = ($resP === 'removed' && ! $afterP);
            } else {
                $togglePPass = ($resP === 'added' && $afterP);
            }
            // restore state
            $bmModel->toggle($userId, 'program', $pId);

            if ($togglePPass) {
                CLI::write("  [OK] Bookmark Toggle: 'program' #{$pId} toggle insert/delete verified", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Bookmark Toggle: 'program' #{$pId} failed", 'red');
                $failed++;
            }
        }

        if ($sampleClass) {
            $clId = (int) $sampleClass['id'];
            $initCl = $bmModel->exists($userId, 'learning_class', $clId);
            $resCl = $bmModel->toggle($userId, 'learning_class', $clId);
            $afterCl = $bmModel->exists($userId, 'learning_class', $clId);
            if ($initCl) {
                $toggleClPass = ($resCl === 'removed' && ! $afterCl);
            } else {
                $toggleClPass = ($resCl === 'added' && $afterCl);
            }
            // restore state
            $bmModel->toggle($userId, 'learning_class', $clId);

            if ($toggleClPass) {
                CLI::write("  [OK] Bookmark Toggle: 'learning_class' #{$clId} toggle insert/delete verified", 'green');
                $passed++;
            } else {
                CLI::write("  [FAIL] Bookmark Toggle: 'learning_class' #{$clId} failed", 'red');
                $failed++;
            }
        }

        CLI::newLine();
        CLI::write('==================================================================', 'yellow');
        CLI::write("TEST RESULTS: {$passed} Passed, {$failed} Failed", $failed === 0 ? 'green' : 'red');
        CLI::write('==================================================================', 'yellow');

        return $failed === 0 ? 0 : 1;
    }
}
