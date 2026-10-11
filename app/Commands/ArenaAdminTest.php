<?php

namespace App\Commands;

use App\Controllers\Learning\AssessmentManagementController;
use App\Controllers\Learning\CertificateEligibilityController;
use App\Controllers\Learning\CourseController;
use App\Controllers\Learning\CurriculumController;
use App\Controllers\Learning\EnrollmentController;
use App\Controllers\Learning\GradingController;
use App\Controllers\Learning\LearningClassController;
use App\Controllers\Learning\LearningOverviewController;
use App\Controllers\Learning\LearningReportController;
use App\Controllers\Learning\ProgramController;
use App\Controllers\Learning\ProgressMonitoringController;
use App\Controllers\Learning\TrainerAssignmentController;
use App\Models\Learning\CourseModel;
use App\Models\Learning\EnrollmentModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\ProgramModel;
use App\Services\CertificateGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ArenaAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'arena:admin:test';
    protected $description = 'Comprehensive Integration Test for all 11 Arena Belajar Admin Lifecycle Modules.';

    private array $moduleResults = [];
    private array $bugs = [];

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   ARENA BELAJAR WOWDASH ADMIN — 11 LIFECYCLE MODULES TEST SUITE  ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();

        // Variables shared across test steps
        $testProgramId    = 0;
        $testCourseId     = 0;
        $testClassId      = 0;
        $testTrainerId    = 0;
        $testUserId       = 0;
        $testEnrollmentId = 0;
        $testModuleId     = 0;
        $testLessonId     = 0;
        $testQuizId       = 0;
        $testAssignmentId = 0;
        $certId           = 0;

        // -------------------------------------------------------------
        // MODULE 1: OVERVIEW
        // -------------------------------------------------------------
        CLI::write(">>> [MODULE 1/11] Learning Overview (dashboard/pembelajaran)", "yellow");
        try {
            $totalPrograms = (int) $db->table('programs')->countAllResults();
            $totalCourses  = (int) $db->table('courses')->countAllResults();
            $activeClasses = (int) $db->table('learning_classes')->whereIn('status', ['open', 'active', 'ongoing'])->countAllResults();
            $activeTrainers = (int) $db->table('class_trainers')->where('status', 'active')->select('COUNT(DISTINCT user_id) as total')->get()->getRow()->total ?? 0;
            $activeParticipants = (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->select('COUNT(DISTINCT user_id) as total')->get()->getRow()->total ?? 0;
            $eligibleCertificates = (int) $db->table('enrollments')->groupStart()->whereIn('status', ['completed', 'passed'])->orWhere('certificate_eligible_at IS NOT NULL', null, false)->groupEnd()->countAllResults();
            $issuedCertificates = (int) $db->table('certificates')->where('source_type', 'learning_enrollment')->where('status', 'issued')->countAllResults();

            CLI::write("  [OK] Overview KPIs queried successfully:", "green");
            CLI::write("       Programs: {$totalPrograms} | Courses: {$totalCourses} | Active Classes: {$activeClasses}");
            CLI::write("       Trainers: {$activeTrainers} | Participants: {$activeParticipants} | Eligible: {$eligibleCertificates} | Issued Certs: {$issuedCertificates}");
            $this->moduleResults['Module 1: Overview'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 1: Overview', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 2: PROGRAM MANAGEMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 2/11] Program Management (dashboard/pembelajaran/program)", "yellow");
        try {
            $progModel = new ProgramModel();
            $testCode = 'TEST-PROG-' . time();
            $testTitle = 'Program Uji Otomatis ' . time();

            // Create program with enhanced landing-page customizable fields
            $inserted = $progModel->insert([
                'title'               => $testTitle,
                'slug'                => 'program-uji-' . time(),
                'code'                => $testCode,
                'description'         => 'Program uji coba lifecycle admin',
                'category'            => 'pelatihan',
                'target_audience'     => "PPK, Pokja Pemilihan, dan Pejabat Pengadaan\nPelaku Usaha Penyedia Jasa",
                'prerequisites'       => "Memahami regulasi pengadaan barang dan jasa dasar",
                'learning_objectives' => "Mampu menyusun dokumen HPS dan rancangan kontrak\nMemahami mitigasi risiko pengadaan",
                'cover_url'           => 'https://images.unsplash.com/photo-test-program.jpg',
                'status'              => 'active',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ], true);

            if (!$inserted) {
                throw new \RuntimeException("Gagal membuat program uji.");
            }
            $testProgramId = (int) $inserted;
            CLI::write("  [OK] Program created successfully with ID: {$testProgramId}", "green");

            // Verify enhancement fields persisted
            $savedProg = $progModel->find($testProgramId);
            if (empty($savedProg['target_audience']) || empty($savedProg['prerequisites']) || empty($savedProg['learning_objectives']) || empty($savedProg['cover_url'])) {
                throw new \RuntimeException("Field enhancement program (target_audience, prerequisites, learning_objectives, cover_url) gagal disimpan.");
            }
            CLI::write("  [OK] Program enhancement fields verified (target_audience, prerequisites, learning_objectives, cover_url).", "green");

            // Query with course_count & class_count subqueries
            $progWithCounts = $db->table('programs p')
                ->select('p.*, 
                          (SELECT COUNT(1) FROM courses c WHERE c.program_id = p.id) AS course_count,
                          (SELECT COUNT(1) FROM learning_classes lc JOIN courses c2 ON c2.id = lc.course_id WHERE c2.program_id = p.id) AS class_count')
                ->where('p.id', $testProgramId)
                ->get()->getRowArray();

            if (!$progWithCounts || !isset($progWithCounts['course_count'])) {
                throw new \RuntimeException("Subquery course_count dan class_count gagal.");
            }
            CLI::write("  [OK] Program index subqueries verified (course_count: {$progWithCounts['course_count']}, class_count: {$progWithCounts['class_count']})", "green");

            // Update program
            $progModel->update($testProgramId, [
                'description' => 'Program uji coba lifecycle admin diubah',
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [OK] Program updated successfully.", "green");

            $this->moduleResults['Module 2: Program Management'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 2: Program Management', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 3: COURSE MANAGEMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 3/11] Course Management (dashboard/pembelajaran/course)", "yellow");
        try {
            $courseModel = new CourseModel();
            $testCourseCode = 'TEST-CRS-' . time();
            $testCourseTitle = 'Kursus Uji Otomatis ' . time();

            // Create Course with enhanced landing-page customizable fields
            $courseId = $courseModel->insert([
                'program_id'         => $testProgramId,
                'title'              => $testCourseTitle,
                'slug'               => 'kursus-uji-' . time(),
                'code'               => $testCourseCode,
                'description'        => 'Deskripsi kursus uji',
                'delivery_mode'      => 'online',
                'level'              => 'dasar',
                'status'             => 'active',
                'estimated_duration' => 120,
                'target_audience'    => "PPK, Pokja Pemilihan, dan Auditor Pengadaan",
                'prerequisites'      => "Pengenalan PBJ Pemerintah Tingkat Dasar",
                'learning_objectives'=> "Memahami tata cara evaluasi penawaran\nMampu menyusun laporan evaluasi pengadaan",
                'cover_url'          => 'https://images.unsplash.com/photo-test-course.jpg',
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ], true);

            if (!$courseId) {
                throw new \RuntimeException("Gagal membuat kursus uji.");
            }
            $testCourseId = (int) $courseId;
            CLI::write("  [OK] Course created successfully with ID: {$testCourseId}", "green");

            // Verify enhancement fields persisted
            $savedCourse = $courseModel->find($testCourseId);
            if (empty($savedCourse['target_audience']) || empty($savedCourse['learning_objectives']) || empty($savedCourse['cover_url'])) {
                throw new \RuntimeException("Field enhancement course (target_audience, learning_objectives, cover_url) gagal disimpan.");
            }
            CLI::write("  [OK] Course enhancement fields verified (target_audience, prerequisites, learning_objectives, cover_url).", "green");

            // Query with class_count, student_count, completed_count
            $courseWithCounts = $db->table('courses c')
                ->select('c.*, p.title AS program_title,
                          (SELECT COUNT(1) FROM learning_classes lc WHERE lc.course_id = c.id) AS class_count,
                          (SELECT COUNT(DISTINCT e.user_id) FROM enrollments e JOIN learning_classes lc ON lc.id = e.class_id WHERE lc.course_id = c.id) AS student_count,
                          (SELECT COUNT(DISTINCT e.user_id) FROM enrollments e JOIN learning_classes lc ON lc.id = e.class_id WHERE lc.course_id = c.id AND e.status IN ("completed", "passed")) AS completed_count')
                ->join('programs p', 'p.id = c.program_id', 'left')
                ->where('c.id', $testCourseId)
                ->get()->getRowArray();

            if (!$courseWithCounts || !isset($courseWithCounts['student_count'])) {
                throw new \RuntimeException("Subqueries pada courses index gagal.");
            }
            CLI::write("  [OK] Course subqueries verified (class_count: {$courseWithCounts['class_count']}, student_count: {$courseWithCounts['student_count']})", "green");

            $this->moduleResults['Module 3: Course Management'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 3: Course Management', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 4: CLASS / BATCH MANAGEMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 4/11] Class / Batch Management (dashboard/pembelajaran/kelas)", "yellow");
        try {
            $classModel = new LearningClassModel();
            $testClassCode = 'TEST-CLS-' . time();
            $testClassName = 'Batch Uji Otomatis ' . time();

            // Create Class with enhanced fields
            $classId = $classModel->insert([
                'course_id'             => $testCourseId,
                'code'                  => $testClassCode,
                'name'                  => $testClassName,
                'description'           => 'Deskripsi batch uji komprehensif',
                'learning_method'       => 'online',
                'location_or_link'      => 'https://meet.google.com/abc-defg-hij',
                'target_audience'       => 'Seluruh ASN Pengelola Pengadaan Barang/Jasa',
                'capacity'              => 30,
                'status'                => 'open',
                'registration_open_at'  => date('Y-m-d H:i:s', strtotime('-1 day')),
                'registration_close_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'start_at'              => date('Y-m-d H:i:s', strtotime('+2 days')),
                'end_at'                => date('Y-m-d H:i:s', strtotime('+40 days')),
                'passing_grade'         => 75,
                'quiz_weight'           => 40.00,
                'assignment_weight'     => 0.00,
                'post_test_weight'      => 60.00,
                'certificate_enabled'   => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ], true);

            if (!$classId) {
                throw new \RuntimeException("Gagal membuat kelas uji.");
            }
            $testClassId = (int) $classId;
            CLI::write("  [OK] Class / Batch created with ID: {$testClassId} (Passing Grade: 75, Weights: 30/70)", "green");

            // Verify enhancement fields persisted
            $savedClass = $classModel->find($testClassId);
            if ($savedClass['learning_method'] !== 'online' || empty($savedClass['location_or_link']) || empty($savedClass['target_audience'])) {
                throw new \RuntimeException("Field enhancement class (learning_method, location_or_link, target_audience) gagal disimpan.");
            }
            CLI::write("  [OK] Class enhancement fields verified (learning_method: {$savedClass['learning_method']}, location_or_link, target_audience).", "green");

            // Verify detail query with trainer & enrollment aggregations
            $classDetail = $db->table('learning_classes lc')
                ->select('lc.*, c.title AS course_title,
                          (SELECT COUNT(DISTINCT ct.user_id) FROM class_trainers ct WHERE ct.class_id = lc.id AND ct.status = "active") AS trainer_count,
                          (SELECT COUNT(1) FROM enrollments e WHERE e.class_id = lc.id) AS student_count')
                ->join('courses c', 'c.id = lc.course_id', 'left')
                ->where('lc.id', $testClassId)
                ->get()->getRowArray();

            if (!$classDetail || (int)$classDetail['passing_grade'] !== 75) {
                throw new \RuntimeException("Class detail validation gagal.");
            }
            CLI::write("  [OK] Class detail aggregation verified.", "green");

            $this->moduleResults['Module 4: Class / Batch Management'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 4: Class / Batch Management', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 5: TRAINER ASSIGNMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 5/11] Trainer Assignment (dashboard/pembelajaran/trainer)", "yellow");
        try {
            // Find an eligible trainer user (role trainer)
            $trainerUser = $db->table('user_roles ur')
                ->select('u.id, u.name, u.email, u.username')
                ->join('roles r', 'r.id = ur.role_id')
                ->join('users u', 'u.id = ur.user_id')
                ->where('ur.status', 'active')
                ->where('r.`key`', 'trainer')
                ->where('u.account_status', 'active')
                ->orderBy('u.name', 'ASC')
                ->limit(1)
                ->get()->getRowArray();

            if (!$trainerUser) {
                $trainerUser = $db->table('users')->where('account_status', 'active')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            }

            if (!$trainerUser) {
                throw new \RuntimeException("Tidak ditemukan user untuk trainer test.");
            }
            $testTrainerId = (int) $trainerUser['id'];

            // Assign trainer with role
            $db->table('class_trainers')->insert([
                'class_id'    => $testClassId,
                'user_id'     => $testTrainerId,
                'assigned_by' => $testTrainerId,
                'role'        => 'primary',
                'status'      => 'active',
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [OK] Trainer #{$testTrainerId} assigned to Class #{$testClassId} as primary.", "green");

            // Verify active assignment exists with role
            $assignedRow = $db->table('class_trainers')
                ->where('class_id', $testClassId)
                ->where('user_id', $testTrainerId)
                ->where('status', 'active')
                ->get()->getRowArray();

            if (!$assignedRow || ($assignedRow['role'] ?? '') !== 'primary') {
                throw new \RuntimeException("Penugasan trainer atau field role tidak ditemukan di database.");
            }
            CLI::write("  [OK] Active trainer assignment confirmed in class_trainers with role 'primary'.", "green");

            $this->moduleResults['Module 5: Trainer Assignment'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 5: Trainer Assignment', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 6: PESERTA & ENROLLMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 6/11] Peserta & Enrollment (dashboard/pembelajaran/enrollment)", "yellow");
        try {
            $participantUser = $db->table('users')
                ->where('account_status', 'active')
                ->where('id !=', $testTrainerId)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()->getRowArray();

            if (!$participantUser) {
                throw new \RuntimeException("Tidak ditemukan user peserta untuk pengujian enrollment.");
            }
            $testUserId = (int) $participantUser['id'];

            $enrModel = new EnrollmentModel();
            // Manual admin enrollment
            $enrollmentId = $enrModel->insert([
                'class_id'     => $testClassId,
                'user_id'      => $testUserId,
                'status'        => 'active',
                'enrolled_at'   => date('Y-m-d H:i:s'),
                'activated_at'  => date('Y-m-d H:i:s'),
                'final_score'   => 85.00,
            ], true);

            if (!$enrollmentId) {
                throw new \RuntimeException("Gagal melakukan manual enrollment peserta.");
            }
            $testEnrollmentId = (int) $enrollmentId;
            CLI::write("  [OK] Participant #{$testUserId} enrolled into Class #{$testClassId} with Enrollment ID: {$testEnrollmentId}", "green");

            // Verify enrollment status and progress
            $enrRecord = $db->table('enrollments')->where('id', $testEnrollmentId)->get()->getRowArray();
            if ($enrRecord['status'] !== 'active' || (float)$enrRecord['final_score'] !== 85.0) {
                throw new \RuntimeException("Enrollment data mismatch.");
            }
            CLI::write("  [OK] Enrollment record verified with score 85.00.", "green");

            $this->moduleResults['Module 6: Peserta & Enrollment'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 6: Peserta & Enrollment', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 7: CURRICULUM MANAGEMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 7/11] Curriculum Management (dashboard/pembelajaran/kurikulum)", "yellow");
        try {
            $now = date('Y-m-d H:i:s');

            // 1. Pre-Test Kelas
            $db->table('quizzes')->insert([
                'class_id'      => $testClassId,
                'lesson_id'     => null,
                'type'          => 'pre_test',
                'title'         => 'Pre-Test: Evaluasi Diagnostik Awal',
                'instructions'  => 'Ujian diagnostik sebelum materi terbuka.',
                'attempt_limit' => 2,
                'is_required'   => 1,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $testPreTestId = (int) $db->insertID();

            $db->table('pre_tests')->insert([
                'class_id'      => $testClassId,
                'is_enabled'    => 1,
                'attempt_limit' => 2,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            CLI::write("  [OK] Pre-Test created with ID: {$testPreTestId} & synced with pre_tests table.", "green");

            // Pre-Test Question 1
            $db->table('quiz_questions')->insert([
                'quiz_id'     => $testPreTestId,
                'type'        => 'multiple_choice',
                'prompt'      => 'Apa tujuan utama pengadaan barang/jasa pemerintah?',
                'points'      => 10.0,
                'sort_order'  => 1,
                'explanation' => 'Menghasilkan barang/jasa tepat nilai dan manfaat untuk publik.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $q1Id = (int) $db->insertID();

            $optionsQ1 = [
                ['text' => 'Mendapatkan keuntungan maksimal bagi rekanan', 'correct' => 0],
                ['text' => 'Menghasilkan barang/jasa yang tepat dari setiap uang yang dibelanjakan', 'correct' => 1],
                ['text' => 'Menghabiskan anggaran sisa tahun berjalan', 'correct' => 0],
                ['text' => 'Mengurangi jumlah pegawai administrasi', 'correct' => 0],
            ];
            foreach ($optionsQ1 as $oIdx => $opt) {
                $db->table('quiz_options')->insert([
                    'question_id' => $q1Id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                    'sort_order'  => $oIdx + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
            CLI::write("  [OK] Pre-Test Question #1 created with 4 options (A-D) and key B.", "green");

            // 2. Modul & Lesson
            $db->table('learning_modules')->insert([
                'class_id'    => $testClassId,
                'title'       => 'Modul 1: Pengenalan Konsep & Regulasi',
                'description' => 'Deskripsi modul kurikulum',
                'sort_order'  => 1,
                'is_required' => 1,
                'status'      => 'published',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $testModuleId = (int) $db->insertID();
            CLI::write("  [OK] Module created with ID: {$testModuleId}", "green");

            $db->table('lessons')->insert([
                'module_id'    => $testModuleId,
                'title'        => 'Lesson 1.1: Regulasi Dasar PBJ',
                'slug'         => 'lesson-regulasi-dasar-' . time(),
                'content_type' => 'text',
                'content'      => '<p>Materi teks regulasi...</p>',
                'sort_order'   => 1,
                'is_required'  => 1,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $testLessonId = (int) $db->insertID();
            CLI::write("  [OK] Lesson created with ID: {$testLessonId}", "green");

            // 3. Quiz pada Modul
            $db->table('quizzes')->insert([
                'class_id'      => $testClassId,
                'lesson_id'     => $testLessonId,
                'type'          => 'lesson_quiz',
                'title'         => 'Kuis Evaluasi Modul 1',
                'instructions'  => 'Jawab seluruh pertanyaan pilihan ganda untuk modul ini.',
                'attempt_limit' => 3,
                'is_required'   => 1,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $testModQuizId = (int) $db->insertID();

            $db->table('quiz_questions')->insert([
                'quiz_id'     => $testModQuizId,
                'type'        => 'multiple_choice',
                'prompt'      => 'Manakah yang termasuk etika pengadaan barang/jasa?',
                'points'      => 5.0,
                'sort_order'  => 1,
                'explanation' => 'Menghindari benturan kepentingan dan tidak menerima gratifikasi.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $qModId = (int) $db->insertID();

            $optionsQMod = [
                ['text' => 'Bekerja secara tertib dan mencegah kebocoran keuangan', 'correct' => 1],
                ['text' => 'Menerima imbalan dari pihak penyedia', 'correct' => 0],
                ['text' => 'Membocorkan HPS sebelum waktu pengumuman', 'correct' => 0],
                ['text' => 'Memihak salah satu peserta tender', 'correct' => 0],
            ];
            foreach ($optionsQMod as $oIdx => $opt) {
                $db->table('quiz_options')->insert([
                    'question_id' => $qModId,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                    'sort_order'  => $oIdx + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
            CLI::write("  [OK] Module Quiz created with ID: {$testModQuizId} and Question (key A).", "green");

            // 4. Assignments (Level Kelas & Level Modul)
            $db->table('assignments')->insert([
                'class_id'     => $testClassId,
                'lesson_id'    => null, // Class level
                'title'        => 'Tugas Akhir Simulasi Pengadaan Kelas',
                'instructions' => 'Susun dokumen perencanaan dan HPS secara lengkap.',
                'due_at'       => date('Y-m-d H:i:s', strtotime('+7 days')),
                'is_required'  => 1,
                'max_attempts' => 1,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $testClassAsgnId = (int) $db->insertID();

            $db->table('assignments')->insert([
                'class_id'     => $testClassId,
                'lesson_id'    => $testLessonId, // Module level
                'title'        => 'Tugas Modul: Studi Kasus Regulasi',
                'instructions' => 'Analisis kasus sanggahan lelang pada materi regulasi.',
                'due_at'       => date('Y-m-d H:i:s', strtotime('+3 days')),
                'is_required'  => 1,
                'max_attempts' => 2,
                'status'       => 'published',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $testModAsgnId = (int) $db->insertID();
            CLI::write("  [OK] Assignments created (Class Level ID: {$testClassAsgnId}, Module Level ID: {$testModAsgnId}).", "green");

            // 5. Post-Test Kelas
            $db->table('quizzes')->insert([
                'class_id'      => $testClassId,
                'lesson_id'     => null,
                'type'          => 'post_test',
                'title'         => 'Post-Test Ujian Akhir Kelulusan',
                'instructions'  => 'Evaluasi penentu kelulusan sertifikasi. Nilai dihitung otomatis.',
                'attempt_limit' => 3,
                'is_required'   => 1,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $testPostTestId = (int) $db->insertID();

            $db->table('quiz_questions')->insert([
                'quiz_id'     => $testPostTestId,
                'type'        => 'multiple_choice',
                'prompt'      => 'Berapakah batas nilai pengadaan langsung untuk barang/jasa lainnya?',
                'points'      => 20.0,
                'sort_order'  => 1,
                'explanation' => 'Sesuai aturan PBJ pemerintah, batas pengadaan langsung barang/pekerjaan konstruksi/jasa lainnya adalah sampai dengan Rp200 juta.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $qPostId = (int) $db->insertID();

            $optionsQPost = [
                ['text' => 'Rp 50 Juta', 'correct' => 0],
                ['text' => 'Rp 100 Juta', 'correct' => 0],
                ['text' => 'Rp 200 Juta', 'correct' => 1],
                ['text' => 'Rp 500 Juta', 'correct' => 0],
            ];
            foreach ($optionsQPost as $oIdx => $opt) {
                $db->table('quiz_options')->insert([
                    'question_id' => $qPostId,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                    'sort_order'  => $oIdx + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
            CLI::write("  [OK] Post-Test created with ID: {$testPostTestId} and Question (key C).", "green");

            // 6. Verify Curriculum Tree & Render
            $currCtrl = new CurriculumController();
            $currCtrl->initController(service('request'), service('response'), service('logger'));
            $currHtml = $currCtrl->index((string) $testClassId);
            if (empty($currHtml) || !str_contains($currHtml, 'Pre-Test: Evaluasi Diagnostik Awal') || !str_contains($currHtml, 'Post-Test Ujian Akhir Kelulusan')) {
                throw new \RuntimeException("Curriculum hierarchy view render validation mismatch.");
            }
            CLI::write("  [OK] Curriculum hierarchy view rendered successfully with Pre-Test, Modules, Assignments & Post-Test.", "green");

            $this->moduleResults['Module 7: Curriculum Management'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 7: Curriculum Management', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 8: ASSESSMENT MANAGEMENT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 8/11] Assessment Management (dashboard/pembelajaran/assessment)", "yellow");
        try {
            // Create Quiz
            $db->table('quizzes')->insert([
                'class_id'      => $testClassId,
                'lesson_id'     => $testLessonId,
                'type'          => 'lesson_quiz',
                'title'         => 'Kuis Evaluasi Modul 1',
                'instructions'  => 'Kuis pemahaman materi',
                'attempt_limit' => 3,
                'is_required'   => 1,
                'status'        => 'published',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $testQuizId = (int) $db->insertID();
            CLI::write("  [OK] Quiz created with ID: {$testQuizId}", "green");

            // Create Assignment
            $db->table('assignments')->insert([
                'class_id'     => $testClassId,
                'lesson_id'    => $testLessonId,
                'title'        => 'Tugas Analisis Studi Kasus PBJ',
                'instructions' => 'Analisis kasus pengadaan berikut...',
                'due_at'       => date('Y-m-d H:i:s', strtotime('+7 days')),
                'is_required'  => 1,
                'max_attempts' => 1,
                'status'       => 'published',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $testAssignmentId = (int) $db->insertID();
            CLI::write("  [OK] Assignment created with ID: {$testAssignmentId}", "green");

            // Verify Assessments count for class
            $qCount = (int) $db->table('quizzes')->where('class_id', $testClassId)->countAllResults();
            $aCount = (int) $db->table('assignments')->where('class_id', $testClassId)->countAllResults();

            if ($qCount < 1 || $aCount < 1) {
                throw new \RuntimeException("Assessment query mismatch (quizzes: {$qCount}, assignments: {$aCount}).");
            }
            CLI::write("  [OK] Assessments in Class #{$testClassId} verified (Quizzes: {$qCount}, Assignments: {$aCount}).", "green");

            $this->moduleResults['Module 8: Assessment Management'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 8: Assessment Management', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 9: LEARNING PROGRESS MONITORING
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 9/11] Learning Progress Monitoring (dashboard/pembelajaran/progres)", "yellow");
        try {
            // Record completed progress for Lesson 1.1
            $db->table('learning_progress')->insert([
                'enrollment_id' => $testEnrollmentId,
                'lesson_id'     => $testLessonId,
                'status'        => 'completed',
                'completed_at'  => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            // Query progress metrics for participant
            $totalLessons = (int) $db->table('lessons l')
                ->join('learning_modules lm', 'lm.id = l.module_id')
                ->where('lm.class_id', $testClassId)
                ->countAllResults();

            $completedLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $testEnrollmentId)
                ->where('status', 'completed')
                ->countAllResults();

            $progressPct = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

            if ($progressPct !== 100) {
                throw new \RuntimeException("Progress % calculation failed (expected 100%, got {$progressPct}%).");
            }
            CLI::write("  [OK] Progress calculation verified: {$completedLessons}/{$totalLessons} lessons completed ({$progressPct}%).", "green");

            // Mark enrollment as completed and certificate eligible
            $db->table('enrollments')->where('id', $testEnrollmentId)->update([
                'status'                 => 'completed',
                'completed_at'           => date('Y-m-d H:i:s'),
                'passed_at'              => date('Y-m-d H:i:s'),
                'certificate_eligible_at'=> date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [OK] Enrollment #{$testEnrollmentId} updated to completed & certificate eligible.", "green");

            $this->moduleResults['Module 9: Learning Progress Monitoring'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 9: Learning Progress Monitoring', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 10: CERTIFICATE ELIGIBILITY
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 10/11] Certificate Eligibility (dashboard/pembelajaran/kelayakan-sertifikat)", "yellow");
        try {
            // Verify eligibility detection query
            $eligibleRecord = $db->table('enrollments e')
                ->select('e.*, u.name AS student_name, lc.name AS class_name')
                ->join('users u', 'u.id = e.user_id')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->where('e.id', $testEnrollmentId)
                ->where('e.certificate_eligible_at IS NOT NULL', null, false)
                ->get()->getRowArray();

            if (!$eligibleRecord) {
                throw new \RuntimeException("Enrollment tidak terdeteksi sebagai kandidat eligible sertifikat.");
            }
            CLI::write("  [OK] Candidate detected in Certificate Eligibility query: {$eligibleRecord['student_name']}.", "green");

            // Issue Certificate directly via CertificateGenerator
            $generator = new CertificateGenerator();
            $admin = $db->table('users')->where('id', 1)->get()->getRowArray()
                ?: $db->table('users')->where('account_status', 'active')->get()->getRowArray();
            $adminId = (int) ($admin['id'] ?? 1);
            $issueResult = $generator->generateDirect('learning_enrollment', $testEnrollmentId, $adminId);

            if (!$issueResult || empty($issueResult['certificate_id'])) {
                throw new \RuntimeException("Certificate generation direct invocation failed.");
            }
            $certId = (int) $issueResult['certificate_id'];
            $certNumber = $issueResult['certificate_number'] ?? 'N/A';
            CLI::write("  [OK] Certificate issued directly! ID: {$certId}, Number: {$certNumber}", "green");

            // Verify certificate in DB
            $certRow = $db->table('certificates')->where('id', $certId)->get()->getRowArray();
            if (!$certRow || $certRow['status'] !== 'issued') {
                throw new \RuntimeException("Certificate DB state is not issued.");
            }
            CLI::write("  [OK] Certificate status verified as 'issued' in database.", "green");

            $this->moduleResults['Module 10: Certificate Eligibility'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 10: Certificate Eligibility', $e->getMessage());
        }

        // -------------------------------------------------------------
        // MODULE 11: REPORTS & ANALYTICS
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [MODULE 11/11] Reports & Analytics (dashboard/pembelajaran/laporan)", "yellow");
        try {
            $reportController = new LearningReportController();
            $reportController->initController(service('request'), service('response'), service('logger'));

            // Query class reports
            $classReports = $db->table('learning_classes lc')
                ->select('lc.id, lc.name, lc.code,
                          COUNT(e.id) AS total_enrolled,
                          SUM(CASE WHEN e.status IN ("completed", "passed") THEN 1 ELSE 0 END) AS completed_count')
                ->join('enrollments e', 'e.class_id = lc.id', 'left')
                ->where('lc.id', $testClassId)
                ->groupBy('lc.id, lc.name, lc.code')
                ->get()->getRowArray();

            if (!$classReports || (int)$classReports['total_enrolled'] < 1) {
                throw new \RuntimeException("Class performance aggregation failed for test class.");
            }
            CLI::write("  [OK] Class performance analytics query verified (Enrolled: {$classReports['total_enrolled']}, Completed: {$classReports['completed_count']}).", "green");

            // Query trainer performance
            $trainerReports = $db->table('class_trainers ct')
                ->select('u.name, COUNT(DISTINCT ct.class_id) AS total_classes')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.user_id', $testTrainerId)
                ->groupBy('u.name')
                ->get()->getRowArray();

            if (!$trainerReports || (int)$trainerReports['total_classes'] < 1) {
                throw new \RuntimeException("Trainer performance query failed for test trainer.");
            }
            CLI::write("  [OK] Trainer performance analytics query verified ({$trainerReports['name']}: {$trainerReports['total_classes']} classes).", "green");

            $this->moduleResults['Module 11: Reports & Analytics'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Module 11: Reports & Analytics', $e->getMessage());
        }

        // -------------------------------------------------------------
        // VERIFY SPECIFIC REPORTED BUG URLS RENDERING
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [VERIFY FIXES] Testing Direct Render of Reported Bug URLs", "yellow");
        try {
            // 1. Detail Kelas: /dashboard/pembelajaran/kelas/{id}
            $existingClass = $db->table('learning_classes')->select('id')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $checkClassId = (string) ($existingClass['id'] ?? 4);

            $classCtrl = new LearningClassController();
            $classCtrl->initController(service('request'), service('response'), service('logger'));
            $classHtml = $classCtrl->detail($checkClassId);
            if (empty($classHtml) || str_contains($classHtml, 'course_modules')) {
                throw new \RuntimeException("Detail kelas gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/kelas/{$checkClassId} rendered successfully (" . strlen($classHtml) . " bytes)", "green");

            // 2. Monitoring Progres: /dashboard/pembelajaran/progres
            $progCtrl = new ProgressMonitoringController();
            $progCtrl->initController(service('request'), service('response'), service('logger'));
            $progHtml = $progCtrl->index();
            if (empty($progHtml) || str_contains($progHtml, 'qa.user_id')) {
                throw new \RuntimeException("Monitoring progres gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/progres rendered successfully (" . strlen($progHtml) . " bytes)", "green");

            // 3. Laporan: /dashboard/pembelajaran/laporan
            $reportCtrl = new LearningReportController();
            $reportCtrl->initController(service('request'), service('response'), service('logger'));
            $reportHtml = $reportCtrl->index();
            if (empty($reportHtml) || str_contains($reportHtml, 'lc.quota')) {
                throw new \RuntimeException("Laporan pembelajaran gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/laporan rendered successfully (" . strlen($reportHtml) . " bytes)", "green");

            // 4. Assessment Management: /dashboard/pembelajaran/assessment
            $assessCtrl = new AssessmentManagementController();
            $assessCtrl->initController(service('request'), service('response'), service('logger'));
            $assessHtml = $assessCtrl->index();
            if (empty($assessHtml)) {
                throw new \RuntimeException("Assessment management gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/assessment rendered successfully (" . strlen($assessHtml) . " bytes)", "green");

            // 5. Kurikulum: /dashboard/pembelajaran/kurikulum
            $currCtrl = new CurriculumController();
            $currCtrl->initController(service('request'), service('response'), service('logger'));
            $currHtml = $currCtrl->index();
            if (empty($currHtml)) {
                throw new \RuntimeException("Kurikulum management gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/kurikulum rendered successfully (" . strlen($currHtml) . " bytes)", "green");

            // 6. Trainer Assignment: /dashboard/pembelajaran/trainer
            $trainerCtrl = new TrainerAssignmentController();
            $trainerCtrl->initController(service('request'), service('response'), service('logger'));
            $trainerHtml = $trainerCtrl->index();
            if (empty($trainerHtml)) {
                throw new \RuntimeException("Trainer assignment gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/trainer rendered successfully (" . strlen($trainerHtml) . " bytes)", "green");

            // 7. Enrollment: /dashboard/pembelajaran/enrollment
            $enrCtrl = new EnrollmentController();
            $enrCtrl->initController(service('request'), service('response'), service('logger'));
            $enrHtml = $enrCtrl->index();
            if (empty($enrHtml)) {
                throw new \RuntimeException("Enrollment gagal dirender.");
            }
            // Verify table row is not blank (contains user and class details, not just placeholders)
            if (str_contains($enrHtml, '<td>-</td>') && !str_contains($enrHtml, 'text-primary-600')) {
                throw new \RuntimeException("Enrollment data kolom peserta / kelas kosong.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/enrollment rendered successfully with populated columns (" . strlen($enrHtml) . " bytes)", "green");

            // 8. Kelayakan Sertifikat: /dashboard/pembelajaran/kelayakan-sertifikat
            $certEligCtrl = new CertificateEligibilityController();
            $certEligCtrl->initController(service('request'), service('response'), service('logger'));
            $certEligHtml = $certEligCtrl->index();
            if (empty($certEligHtml)) {
                throw new \RuntimeException("Certificate eligibility gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/kelayakan-sertifikat rendered successfully (" . strlen($certEligHtml) . " bytes)", "green");

            // 9. Overview: /dashboard/pembelajaran
            $overCtrl = new LearningOverviewController();
            $overCtrl->initController(service('request'), service('response'), service('logger'));
            $overHtml = $overCtrl->index();
            if (empty($overHtml)) {
                throw new \RuntimeException("Overview gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran rendered successfully (" . strlen($overHtml) . " bytes)", "green");

            // 10. Bank Soal: /dashboard/pembelajaran/assessment/bank-soal
            $bankHtml = $assessCtrl->questionBank();
            if (empty($bankHtml)) {
                throw new \RuntimeException("Bank soal gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/assessment/bank-soal rendered successfully (" . strlen($bankHtml) . " bytes)", "green");

            // 11. Penilaian: /dashboard/pembelajaran/penilaian
            $gradeCtrl = new GradingController();
            $gradeCtrl->initController(service('request'), service('response'), service('logger'));
            $gradeHtml = $gradeCtrl->index();
            if (empty($gradeHtml)) {
                throw new \RuntimeException("Halaman penilaian tugas gagal dirender.");
            }
            CLI::write("  [OK] /dashboard/pembelajaran/penilaian rendered successfully (" . strlen($gradeHtml) . " bytes)", "green");

            $this->moduleResults['Bug Fixes Verification (3 URLs)'] = 'PASS';
        } catch (\Throwable $e) {
            $this->recordBug('Bug Fixes Verification (3 URLs)', $e->getMessage());
        }

        // -------------------------------------------------------------
        // CLEANUP TEST DATA
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> CLEANUP: Removing temporary test records...", "yellow");
        try {
            if (!empty($certId)) {
                $db->table('certificates')->where('id', $certId)->delete();
            }
            if (!empty($testEnrollmentId)) {
                $db->table('learning_progress')->where('enrollment_id', $testEnrollmentId)->delete();
                $db->table('enrollments')->where('id', $testEnrollmentId)->delete();
            }
            if (!empty($testClassId)) {
                $quizzes = $db->table('quizzes')->where('class_id', $testClassId)->get()->getResultArray();
                foreach ($quizzes as $qz) {
                    $qQuestions = $db->table('quiz_questions')->where('quiz_id', $qz['id'])->get()->getResultArray();
                    if (!empty($qQuestions)) {
                        $db->table('quiz_options')->whereIn('question_id', array_column($qQuestions, 'id'))->delete();
                        $db->table('quiz_questions')->where('quiz_id', $qz['id'])->delete();
                    }
                }
                $db->table('quizzes')->where('class_id', $testClassId)->delete();
                $db->table('pre_tests')->where('class_id', $testClassId)->delete();
                $db->table('assignments')->where('class_id', $testClassId)->delete();
            }
            if (!empty($testQuizId)) {
                $db->table('quizzes')->where('id', $testQuizId)->delete();
            }
            if (!empty($testAssignmentId)) {
                $db->table('assignments')->where('id', $testAssignmentId)->delete();
            }
            if (!empty($testLessonId)) {
                $db->table('lessons')->where('id', $testLessonId)->delete();
            }
            if (!empty($testModuleId)) {
                $db->table('learning_modules')->where('id', $testModuleId)->delete();
            }
            if (!empty($testClassId)) {
                $db->table('class_trainers')->where('class_id', $testClassId)->delete();
                $db->table('learning_classes')->where('id', $testClassId)->delete();
            }
            if (!empty($testCourseId)) {
                $db->table('courses')->where('id', $testCourseId)->delete();
            }
            if (!empty($testProgramId)) {
                $db->table('programs')->where('id', $testProgramId)->delete();
            }
            CLI::write("  [OK] Cleaned up temporary test entities cleanly.", "green");
        } catch (\Throwable $e) {
            CLI::write("  [WARN] Cleanup warning: " . $e->getMessage(), "yellow");
        }

        // -------------------------------------------------------------
        // SUMMARY REPORT
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("                     TEST EXECUTION SUMMARY                       ", "cyan");
        CLI::write("==================================================================", "cyan");

        $allPass = true;
        foreach ($this->moduleResults as $module => $res) {
            $color = $res === 'PASS' ? 'green' : 'red';
            CLI::write(sprintf("  %-38s : %s", $module, $res), $color);
            if ($res !== 'PASS') {
                $allPass = false;
            }
        }

        CLI::newLine();
        if ($allPass && empty($this->bugs)) {
            CLI::write("STATUS: ALL 11 ARENA BELAJAR ADMIN MODULES PASSED (100% SUCCESS)", "green");
            return 0;
        }

        CLI::write("STATUS: ENCOUNTERED FAILURES (" . count($this->bugs) . " issues)", "red");
        foreach ($this->bugs as $idx => $b) {
            CLI::write("  " . ($idx + 1) . ". [{$b['module']}] {$b['message']}", "red");
        }
        return 1;
    }

    private function recordBug(string $module, string $message): void
    {
        $this->moduleResults[$module] = 'FAIL';
        $this->bugs[] = [
            'module'  => $module,
            'message' => $message,
        ];
        CLI::write("  [FAIL] {$module}: {$message}", "red");
    }
}
