<?php

namespace App\Commands;

use App\Controllers\Certificate\CertificateTemplateController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificateGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use ReflectionClass;
use RuntimeException;

class CertificateHierarchyTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:certificate-hierarchy';
    protected $description = 'Verify certificate template hierarchy, anti-conflict locks, and event 1:1 uniqueness without migrations';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   TESTING CERTIFICATE HIERARCHY, LOCKS & EVENT 1:1 UNIQUENESS   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $passCount = 0;
        $failCount = 0;

        $db = Database::connect();
        $db->transBegin();

        try {
            $templateModel = new CertificateTemplateModel();

            // -------------------------------------------------------------
            // TEST 1: Hierarchy Setup - Program Level Lock Propagation
            // -------------------------------------------------------------
            CLI::write("[TEST 1] Testing Program Level Template and Lock Propagation...", "yellow");

            // Create fresh distinct test Program, Course, Class
            $progUnique = 'prog-hier-' . time();
            $db->table('programs')->insert([
                'title'       => 'Program Uji Hierarki Sertifikat',
                'slug'        => $progUnique,
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $progId = (int) $db->insertID();

            $courseUnique = 'course-hier-' . time();
            $db->table('courses')->insert([
                'program_id'  => $progId,
                'title'       => 'Course Uji Hierarki Sertifikat',
                'slug'        => $courseUnique,
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $courseId = (int) $db->insertID();

            $db->table('learning_classes')->insert([
                'course_id'   => $courseId,
                'code'        => 'CLS-HIER-' . time(),
                'name'        => 'Kelas Uji Hierarki Sertifikat',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $classId = (int) $db->insertID();

            // Create a Program-level template
            $progTemplateId = $templateModel->insert([
                'name'             => 'Template Program Uji #' . $progId,
                'source_type'      => 'learning',
                'template_payload' => json_encode([
                    'target_scope' => 'program',
                    'program_id'   => $progId,
                    'title'        => 'Sertifikat Program',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ], true);

            $locks = $templateModel->getLearningHierarchyLockInfo();

            if (isset($locks['locked_programs'][$progId])) {
                CLI::write("  [PASS] Program #{$progId} tercatat locked.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Program #{$progId} TIDAK tercatat locked.", "red");
                $failCount++;
            }

            if (isset($locks['course_locks'][$courseId]) && $locks['course_locks'][$courseId]['locked_by'] === 'program') {
                CLI::write("  [PASS] Course #{$courseId} otomatis terkunci oleh parent Program #{$progId}.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Course #{$courseId} TIDAK terkunci oleh parent Program.", "red");
                $failCount++;
            }

            if (isset($locks['class_locks'][$classId]) && $locks['class_locks'][$classId]['locked_by'] === 'program') {
                CLI::write("  [PASS] Kelas #{$classId} otomatis terkunci oleh parent Program #{$progId}.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Kelas #{$classId} TIDAK terkunci oleh parent Program.", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 2: Template Resolution Priority (Program > Course > Class > Global)
            // -------------------------------------------------------------
            CLI::write("[TEST 2] Testing Template Resolution Priority...", "yellow");

            $resolved = $templateModel->findForLearning($classId, $courseId, $progId);
            if ($resolved && (int) $resolved['id'] === (int) $progTemplateId) {
                CLI::write("  [PASS] findForLearning() sukses memilih template Program sebagai prioritas tertinggi.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] findForLearning() gagal memilih template Program.", "red");
                $failCount++;
            }

            // Now deactivate the program template and create a Course-level template
            $templateModel->update($progTemplateId, ['is_active' => 0]);

            $courseTemplateId = $templateModel->insert([
                'name'             => 'Template Course Uji #' . $courseId,
                'source_type'      => 'learning',
                'template_payload' => json_encode([
                    'target_scope' => 'course',
                    'course_id'    => $courseId,
                    'title'        => 'Sertifikat Course',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ], true);

            $locksAfterCourse = $templateModel->getLearningHierarchyLockInfo();
            if (isset($locksAfterCourse['locked_courses'][$courseId])) {
                CLI::write("  [PASS] Course #{$courseId} tercatat locked.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Course #{$courseId} TIDAK tercatat locked.", "red");
                $failCount++;
            }

            if (isset($locksAfterCourse['class_locks'][$classId]) && $locksAfterCourse['class_locks'][$classId]['locked_by'] === 'course') {
                CLI::write("  [PASS] Kelas #{$classId} otomatis terkunci oleh Course #{$courseId}.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Kelas #{$classId} TIDAK terkunci oleh Course.", "red");
                $failCount++;
            }

            $resolvedCourse = $templateModel->findForLearning($classId, $courseId, $progId);
            if ($resolvedCourse && (int) $resolvedCourse['id'] === (int) $courseTemplateId) {
                CLI::write("  [PASS] findForLearning() sukses memilih template Course ketika Program tidak aktif.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] findForLearning() gagal memilih template Course.", "red");
                $failCount++;
            }

            // Now deactivate course template and create a Class-level template
            $templateModel->update($courseTemplateId, ['is_active' => 0]);

            $classTemplateId = $templateModel->insert([
                'name'             => 'Template Kelas Uji #' . $classId,
                'source_type'      => 'learning',
                'template_payload' => json_encode([
                    'target_scope' => 'class',
                    'class_id'     => $classId,
                    'title'        => 'Sertifikat Kelas',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ], true);

            $resolvedClass = $templateModel->findForLearning($classId, $courseId, $progId);
            if ($resolvedClass && (int) $resolvedClass['id'] === (int) $classTemplateId) {
                CLI::write("  [PASS] findForLearning() sukses memilih template Kelas spesifik ketika Program & Course kosong.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] findForLearning() gagal memilih template Kelas.", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 3: Event & Agenda 1:1 Template Uniqueness & Anti-Sharing
            // -------------------------------------------------------------
            CLI::write("[TEST 3] Testing Event & Agenda 1:1 Unique Template Enforcement...", "yellow");

            $existingEvent = $db->table('events')->where('deleted_at IS NULL')->get()->getFirstRow('array');
            if ($existingEvent) {
                $testEventId = (int) $existingEvent['id'];
            } else {
                $db->table('events')->insert([
                    'title'       => 'Webinar Uji Sertifikat 1:1 Hierarki',
                    'slug'        => 'webinar-uji-sertifikat-' . time(),
                    'type'        => 'webinar',
                    'mode'        => 'online',
                    'status'      => 'published',
                    'created_by'  => 1,
                    'start_at'    => date('Y-m-d H:i:s'),
                    'end_at'      => date('Y-m-d H:i:s', strtotime('+2 hours')),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
                $testEventId = (int) $db->insertID();
            }

            // Create Event template 1
            $eventTemplateId = $templateModel->insert([
                'name'             => 'Template Event Uji #' . $testEventId,
                'source_type'      => 'event',
                'template_payload' => json_encode([
                    'target_scope' => 'event',
                    'event_id'     => $testEventId,
                    'title'        => 'Sertifikat Webinar',
                ]),
                'is_active'        => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ], true);

            $assignedEvents = $templateModel->getAssignedEventMap();
            if (isset($assignedEvents[$testEventId])) {
                CLI::write("  [PASS] Event #{$testEventId} tercatat pada getAssignedEventMap().", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Event #{$testEventId} TIDAK tercatat pada getAssignedEventMap().", "red");
                $failCount++;
            }

            $resolvedEv = $templateModel->findForEvent($testEventId);
            if ($resolvedEv && (int) $resolvedEv['id'] === (int) $eventTemplateId) {
                CLI::write("  [PASS] findForEvent({$testEventId}) sukses menemukan template unik 1:1.", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] findForEvent({$testEventId}) gagal menemukan template unik.", "red");
                $failCount++;
            }

            // Test Controller Scope & Lock Validation
            $controller = new CertificateTemplateController();
            $reflection = new ReflectionClass($controller);
            $validateMethod = $reflection->getMethod('validateScopeAndLock');
            $validateMethod->setAccessible(true);

            // Attempting to assign another active template to the same event should throw RuntimeException
            $conflictCaught = false;
            try {
                $evPayload = [
                    'target_scope' => 'event',
                    'event_id'     => $testEventId,
                ];
                $args = ['event', &$evPayload, null];
                $validateMethod->invokeArgs($controller, $args);
            } catch (RuntimeException $e) {
                $conflictCaught = true;
                CLI::write("  [PASS] Validasi menolak duplikasi template event (1:1 terlindungi): {$e->getMessage()}", "green");
                $passCount++;
            }

            if (! $conflictCaught) {
                CLI::write("  [FAIL] Validasi membiarkan template event ganda (1:1 bocor).", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 4: Backend Lock Validation for Learning Hierarchy
            // -------------------------------------------------------------
            CLI::write("[TEST 4] Testing Backend Lock Validation for Learning Hierarchy...", "yellow");

            // Re-activate Program template
            $templateModel->update($progTemplateId, ['is_active' => 1]);

            // Attempt to assign course template under locked program should fail
            $courseConflictCaught = false;
            try {
                $coursePayload = [
                    'target_scope' => 'course',
                    'course_id'    => $courseId,
                ];
                $args = ['learning', &$coursePayload, null];
                $validateMethod->invokeArgs($controller, $args);
            } catch (RuntimeException $e) {
                $courseConflictCaught = true;
                CLI::write("  [PASS] Validasi menolak template Course karena Program sudah terkunci: {$e->getMessage()}", "green");
                $passCount++;
            }

            if (! $courseConflictCaught) {
                CLI::write("  [FAIL] Validasi membiarkan template Course dibuat saat Program terkunci.", "red");
                $failCount++;
            }

            // Attempt to assign class template under locked program should fail
            $classConflictCaught = false;
            try {
                $classPayload = [
                    'target_scope' => 'class',
                    'class_id'     => $classId,
                ];
                $args = ['learning', &$classPayload, null];
                $validateMethod->invokeArgs($controller, $args);
            } catch (RuntimeException $e) {
                $classConflictCaught = true;
                CLI::write("  [PASS] Validasi menolak template Kelas karena Program sudah terkunci: {$e->getMessage()}", "green");
                $passCount++;
            }

            if (! $classConflictCaught) {
                CLI::write("  [FAIL] Validasi membiarkan template Kelas dibuat saat Program terkunci.", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 5: Verify CertificateGenerator Source Resolution
            // -------------------------------------------------------------
            CLI::write("[TEST 5] Testing CertificateGenerator Source Resolution with Metadata...", "yellow");

            $existingEnrollment = $db->table('enrollments')->orderBy('id', 'DESC')->get()->getFirstRow('array');
            if ($existingEnrollment) {
                $enrollmentId = (int) $existingEnrollment['id'];
            } else {
                $db->table('enrollments')->insert([
                    'class_id'     => $classId,
                    'user_id'      => 14,
                    'status'       => 'passed',
                    'final_score'  => 95.0,
                    'enrolled_at'  => date('Y-m-d H:i:s'),
                    'passed_at'    => date('Y-m-d H:i:s'),
                ]);
                $enrollmentId = (int) $db->insertID();
            }

            $certGen = new CertificateGenerator();
            $sourceMeta = $certGen->resolveSourceDirect('learning_enrollment', $enrollmentId);
            if (array_key_exists('program_id', $sourceMeta) && array_key_exists('course_id', $sourceMeta) && array_key_exists('class_id', $sourceMeta)) {
                CLI::write("  [PASS] CertificateGenerator::resolveSourceDirect menyertakan metadata program_id (" . ($sourceMeta['program_id'] ?? 'null') . "), course_id ({$sourceMeta['course_id']}), dan class_id ({$sourceMeta['class_id']}).", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] CertificateGenerator::resolveSourceDirect TIDAK menyertakan metadata hierarki lengkap.", "red");
                $failCount++;
            }

        } catch (\Throwable $e) {
            CLI::write("  [EXCEPTION] " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine(), "red");
            $failCount++;
        } finally {
            // Always rollback test transaction so database remains 100% clean
            $db->transRollback();
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("                     HASIL PENGUJIAN AKHIR                        ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");

        if ($failCount === 0) {
            CLI::write("STATUS: SELURUH PENGUJIAN HIERARKI & EVENT 1:1 LULUS 100%!", "green");
        } else {
            CLI::write("STATUS: ADA PENGUJIAN YANG GAGAL. SILAKAN CEK LOG DI ATAS.", "red");
        }
        CLI::write("==================================================================", "cyan");

        return $failCount === 0 ? 0 : 1;
    }
}
