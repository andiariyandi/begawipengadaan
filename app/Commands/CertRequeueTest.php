<?php

namespace App\Commands;

use App\Controllers\Certificate\CertificateController;
use App\Models\Certificate\CertificateModel;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificateGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertRequeueTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:cert-requeue';
    protected $description = 'Comprehensive test for certificate delete & re-queue (regenerate) for both Learning and Event';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    TESTING CERTIFICATE DELETE & RE-QUEUE (REGENERATE) AUDIT     ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db        = db_connect();
        $generator = new CertificateGenerator($db);
        $certModel = new CertificateModel();
        $now       = date('Y-m-d H:i:s');
        $ts        = time();

        // 1. Find Admin user
        $admin = $db->table('users')->where('id', 1)->get()->getRowArray()
            ?: $db->table('users')->where('id', 14)->get()->getRowArray()
            ?: $db->table('users')->get()->getRowArray();
        $adminId = (int) $admin['id'];
        $adminName = $admin['name'];

        // Ensure active templates
        $templateModel = new CertificateTemplateModel();
        if (! $templateModel->findDefaultForSource('learning')) {
            $templateModel->insert([
                'name'        => 'Default Learning Template',
                'source_type' => 'learning',
                'is_active'   => 1,
                'created_by'  => $adminId,
            ]);
        }
        if (! $templateModel->findDefaultForSource('event')) {
            $templateModel->insert([
                'name'        => 'Default Event Template',
                'source_type' => 'event',
                'is_active'   => 1,
                'created_by'  => $adminId,
            ]);
        }

        // Setup session as admin
        session()->set('user_id', $adminId);
        session()->set('role', 'superadmin');

        $certController = new CertificateController();
        $certController->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        // Clean up any stale test users from previous runs
        $staleUsers = $db->table('users')
            ->select('id')
            ->where('username LIKE', 'learn_rq_%')
            ->orWhere('username LIKE', 'event_rq_%')
            ->orWhere('username LIKE', 'test_rq_%')
            ->get()->getResultArray();
        if ($staleUsers) {
            $staleIds = array_column($staleUsers, 'id');
            $db->table('certificates')->whereIn('user_id', $staleIds)->delete();
            $db->table('event_registrations')->whereIn('user_id', $staleIds)->delete();
            $db->table('enrollments')->whereIn('user_id', $staleIds)->delete();
            $db->table('notifications')->whereIn('user_id', $staleIds)->delete();
            $db->table('users')->whereIn('id', $staleIds)->delete();
        }

        $passedCount = 0;
        $totalCount  = 0;

        // ==================================================================
        // PART 1: LEARNING CERTIFICATE REQUEUE & PARTICIPANT PRESERVATION
        // ==================================================================
        CLI::write("--- [PART 1] LEARNING (ARENA BELAJAR) REQUEUE & REGENERATE ---", "yellow");

        // 1a. Create Learning Participant User
        $learningUserName = 'Peserta Belajar Requeue ' . $ts;
        $learningUserEmail = 'peserta_learn_' . $ts . '@example.com';
        $db->table('users')->insert([
            'username'       => 'learn_rq_' . $ts,
            'name'           => $learningUserName,
            'email'          => $learningUserEmail,
            'password_hash'  => password_hash('Pass123!', PASSWORD_BCRYPT),
            'profile_type'   => 'asn',
            'account_status' => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $learnUserId = (int) $db->insertID();

        // 1b. Create Enrollment
        $class = $db->table('learning_classes')->get()->getRowArray();
        $classId = (int) ($class['id'] ?? 1);

        $db->table('enrollments')->insert([
            'user_id'                 => $learnUserId,
            'class_id'                => $classId,
            'status'                  => 'completed',
            'final_score'             => 95.50,
            'completed_at'            => $now,
            'passed_at'               => $now,
            'certificate_eligible_at' => $now,
            'created_at'              => $now,
            'updated_at'              => $now,
        ]);
        $enrollmentId = (int) $db->insertID();

        // 1c. Generate Certificate
        $resLearn = $generator->generateDirect('learning_enrollment', $enrollmentId, $adminId);
        $learnCertId = (int) $resLearn['certificate_id'];
        $learnCertNum = $resLearn['certificate_number'];
        CLI::write("  [OK] Generated Learning Certificate #{$learnCertId} [{$learnCertNum}]", "green");

        $pdfRow = $db->table('file_assets')->where('id', $certModel->find($learnCertId)['pdf_file_id'])->get()->getRowArray();
        $learnPdfPath = WRITEPATH . ($pdfRow['path'] ?? '');

        // Create linked approval row
        $db->table('approvals')->insert([
            'resource_type'    => 'certificate',
            'resource_id'      => $enrollmentId,
            'requested_action' => 'issue_certificate',
            'status'           => 'approved',
            'requested_by'     => $learnUserId,
            'reviewed_by'      => $adminId,
            'reviewed_at'      => $now,
            'notes'            => '[source:learning_enrollment] Penerbitan sertifikat',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        $learnApprovalId = (int) $db->insertID();

        // 1d. Execute deleteAndRequeue
        $certController->deleteAndRequeue((string) $learnCertId);

        // Verification for Learning
        $totalCount++;
        $isLearnCertDeleted = ($certModel->find($learnCertId) === null);
        if ($isLearnCertDeleted) {
            $passedCount++;
            CLI::write("  [TEST 1.1] Learning certificate row deleted from DB: PASS", "green");
        } else {
            CLI::write("  [TEST 1.1] Learning certificate row deleted from DB: FAIL", "red");
        }

        $totalCount++;
        $isLearnPdfDeleted = ! file_exists($learnPdfPath);
        if ($isLearnPdfDeleted) {
            $passedCount++;
            CLI::write("  [TEST 1.2] Learning certificate PDF unlinked: PASS", "green");
        } else {
            CLI::write("  [TEST 1.2] Learning certificate PDF unlinked: FAIL", "red");
        }

        $totalCount++;
        $learnAppRow = $db->table('approvals')->where('id', $learnApprovalId)->get()->getRowArray();
        $isLearnPending = ($learnAppRow && $learnAppRow['status'] === 'pending' && $learnAppRow['reviewed_by'] === null);
        if ($isLearnPending) {
            $passedCount++;
            CLI::write("  [TEST 1.3] Learning approval status reset to 'pending': PASS", "green");
        } else {
            CLI::write("  [TEST 1.3] Learning approval status reset to 'pending': FAIL", "red");
        }

        $totalCount++;
        $isLearnReqByParticipant = ((int) ($learnAppRow['requested_by'] ?? 0) === $learnUserId);
        if ($isLearnReqByParticipant) {
            $passedCount++;
            CLI::write("  [TEST 1.4] Approval requested_by is Participant User ID #{$learnUserId} (NOT Admin #{$adminId}): PASS", "green");
        } else {
            CLI::write("  [TEST 1.4] Approval requested_by is Participant User ID: FAIL (Got: " . ($learnAppRow['requested_by'] ?? 'null') . ")", "red");
        }

        $totalCount++;
        $hasLearnTag = str_contains((string) ($learnAppRow['notes'] ?? ''), '[source:learning_enrollment]');
        if ($hasLearnTag) {
            $passedCount++;
            CLI::write("  [TEST 1.5] Approval notes contains explicit '[source:learning_enrollment]' tag: PASS", "green");
        } else {
            CLI::write("  [TEST 1.5] Approval notes contains explicit tag: FAIL", "red");
        }

        $totalCount++;
        $enrAfter = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        $isEnrEligible = ! empty($enrAfter['certificate_eligible_at']) && $enrAfter['status'] === 'completed';
        if ($isEnrEligible) {
            $passedCount++;
            CLI::write("  [TEST 1.6] Enrollment certificate_eligible_at and status restored: PASS", "green");
        } else {
            CLI::write("  [TEST 1.6] Enrollment eligibility restored: FAIL", "red");
        }

        // Test Regeneration for Learning
        $totalCount++;
        $reGenLearn = $generator->generateDirect('learning_enrollment', $enrollmentId, $adminId);
        $newLearnCertId = (int) $reGenLearn['certificate_id'];
        $newLearnCertNum = $reGenLearn['certificate_number'];
        $newLearnCertRow = $certModel->find($newLearnCertId);
        $isNewLearnValid = (! empty($newLearnCertId) && (int) ($newLearnCertRow['user_id'] ?? 0) === $learnUserId);
        if ($isNewLearnValid) {
            $passedCount++;
            CLI::write("  [TEST 1.7] Regenerated Learning Certificate #{$newLearnCertId} attached to Participant #{$learnUserId}: PASS", "green");
        } else {
            CLI::write("  [TEST 1.7] Regenerated Learning Certificate attachment: FAIL", "red");
        }

        CLI::newLine();

        // ==================================================================
        // PART 2: EVENT (AGENDA PBJ) REQUEUE & PARTICIPANT PRESERVATION
        // ==================================================================
        CLI::write("--- [PART 2] EVENT (AGENDA PBJ) REQUEUE & REGENERATE ---", "yellow");

        // 2a. Find or create an event with certificate_enabled = 1
        $event = $db->table('events')->where('certificate_enabled', 1)->get()->getRowArray();
        if (! $event) {
            $db->table('events')->insert([
                'title'               => 'Workshop PBJ Strategis ' . $ts,
                'slug'                => 'workshop-pbj-' . $ts,
                'type'                => 'workshop',
                'mode'                => 'online',
                'location'            => 'Zoom Meeting Prov Lampung',
                'quota'               => 100,
                'status'              => 'published',
                'certificate_enabled' => 1,
                'start_at'            => date('Y-m-d H:i:s', strtotime('-1 day')),
                'end_at'              => date('Y-m-d H:i:s', strtotime('+1 day')),
                'created_by'          => $adminId,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            $eventId = (int) $db->insertID();
        } else {
            $eventId = (int) $event['id'];
        }

        // 2b. Create Event Participant User
        $eventUserName = 'Peserta Event Requeue ' . $ts;
        $eventUserEmail = 'peserta_event_' . $ts . '@example.com';
        $db->table('users')->insert([
            'username'       => 'event_rq_' . $ts,
            'name'           => $eventUserName,
            'email'          => $eventUserEmail,
            'password_hash'  => password_hash('Pass123!', PASSWORD_BCRYPT),
            'profile_type'   => 'public',
            'account_status' => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $eventUserId = (int) $db->insertID();

        // 2c. Register participant to Event & record attendance
        $db->table('event_registrations')->insert([
            'event_id'         => $eventId,
            'user_id'          => $eventUserId,
            'status'           => 'registered',
            'registered_at'    => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        $eventRegId = (int) $db->insertID();

        $db->table('attendances')->insert([
            'event_registration_id' => $eventRegId,
            'status'                => 'present',
            'recorded_by'           => $adminId,
            'recorded_at'           => $now,
            'notes'                 => 'Hadir validasi awal',
            'created_at'            => $now,
            'updated_at'            => $now,
        ]);
        $attendanceId = (int) $db->insertID();

        // 2d. Generate Event Certificate
        $resEvent = $generator->generateDirect('event_registration', $eventRegId, $adminId);
        $eventCertId = (int) $resEvent['certificate_id'];
        $eventCertNum = $resEvent['certificate_number'];
        CLI::write("  [OK] Generated Event Certificate #{$eventCertId} [{$eventCertNum}]", "green");

        $pdfRowEvent = $db->table('file_assets')->where('id', $certModel->find($eventCertId)['pdf_file_id'])->get()->getRowArray();
        $eventPdfPath = WRITEPATH . ($pdfRowEvent['path'] ?? '');

        // Create linked approval row for Event
        $db->table('approvals')->insert([
            'resource_type'    => 'certificate',
            'resource_id'      => $eventRegId,
            'requested_action' => 'issue_certificate',
            'status'           => 'approved',
            'requested_by'     => $eventUserId,
            'reviewed_by'      => $adminId,
            'reviewed_at'      => $now,
            'notes'            => '[source:event_registration] Penerbitan sertifikat event',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        $eventApprovalId = (int) $db->insertID();

        // 2e. Execute deleteAndRequeue for Event
        $certController->deleteAndRequeue((string) $eventCertId);

        // Verification for Event
        $totalCount++;
        $isEventCertDeleted = ($certModel->find($eventCertId) === null);
        if ($isEventCertDeleted) {
            $passedCount++;
            CLI::write("  [TEST 2.1] Event certificate row deleted from DB: PASS", "green");
        } else {
            CLI::write("  [TEST 2.1] Event certificate row deleted from DB: FAIL", "red");
        }

        $totalCount++;
        $isEventPdfDeleted = ! file_exists($eventPdfPath);
        if ($isEventPdfDeleted) {
            $passedCount++;
            CLI::write("  [TEST 2.2] Event certificate PDF unlinked: PASS", "green");
        } else {
            CLI::write("  [TEST 2.2] Event certificate PDF unlinked: FAIL", "red");
        }

        $totalCount++;
        $eventAppRow = $db->table('approvals')->where('id', $eventApprovalId)->get()->getRowArray();
        $isEventPending = ($eventAppRow && $eventAppRow['status'] === 'pending' && $eventAppRow['reviewed_by'] === null);
        if ($isEventPending) {
            $passedCount++;
            CLI::write("  [TEST 2.3] Event approval status reset to 'pending': PASS", "green");
        } else {
            CLI::write("  [TEST 2.3] Event approval status reset to 'pending': FAIL", "red");
        }

        $totalCount++;
        $isEventReqByParticipant = ((int) ($eventAppRow['requested_by'] ?? 0) === $eventUserId);
        if ($isEventReqByParticipant) {
            $passedCount++;
            CLI::write("  [TEST 2.4] Event approval requested_by is Participant User ID #{$eventUserId} (NOT Admin): PASS", "green");
        } else {
            CLI::write("  [TEST 2.4] Event approval requested_by: FAIL (Got: " . ($eventAppRow['requested_by'] ?? 'null') . ")", "red");
        }

        $totalCount++;
        $hasEventTag = str_contains((string) ($eventAppRow['notes'] ?? ''), '[source:event_registration]');
        if ($hasEventTag) {
            $passedCount++;
            CLI::write("  [TEST 2.5] Event approval notes contains '[source:event_registration]': PASS", "green");
        } else {
            CLI::write("  [TEST 2.5] Event approval notes contains '[source:event_registration]': FAIL", "red");
        }

        $totalCount++;
        $attAfter = $db->table('attendances')->where('event_registration_id', $eventRegId)->get()->getRowArray();
        $isAttPresent = ($attAfter && $attAfter['status'] === 'present');
        if ($isAttPresent) {
            $passedCount++;
            CLI::write("  [TEST 2.6] Event participant attendance remains 'present': PASS", "green");
        } else {
            CLI::write("  [TEST 2.6] Event participant attendance: FAIL", "red");
        }

        // Test Regeneration for Event
        $totalCount++;
        $reGenEvent = $generator->generateDirect('event_registration', $eventRegId, $adminId);
        $newEventCertId = (int) $reGenEvent['certificate_id'];
        $newEventCertNum = $reGenEvent['certificate_number'];
        $newEventCertRow = $certModel->find($newEventCertId);
        $isNewEventValid = (! empty($newEventCertId) && (int) ($newEventCertRow['user_id'] ?? 0) === $eventUserId);
        if ($isNewEventValid) {
            $passedCount++;
            CLI::write("  [TEST 2.7] Regenerated Event Certificate #{$newEventCertId} attached to Participant #{$eventUserId}: PASS", "green");
        } else {
            CLI::write("  [TEST 2.7] Regenerated Event Certificate attachment: FAIL", "red");
        }

        CLI::newLine();

        // ==================================================================
        // PART 3: APPROVAL CONTROLLER DISPLAY & PARTICIPANT RESOLUTION
        // ==================================================================
        CLI::write("--- [PART 3] APPROVAL CONTROLLER PARTICIPANT RESOLUTION AUDIT ---", "yellow");

        // Verify that resolveResourceContext in ApprovalController resolves participant correctly
        $approvalController = new \App\Controllers\Admin\ApprovalController();
        $refMethod = new \ReflectionMethod($approvalController, 'resolveResourceContext');
        $refMethod->setAccessible(true);

        $totalCount++;
        $learnContext = $refMethod->invoke($approvalController, 'certificate', $enrollmentId, [
            'notes'        => '[source:learning_enrollment]',
            'requested_by' => $learnUserId,
        ]);
        $learnContextOk = ($learnContext !== null
            && $learnContext['category'] === 'Pembelajaran Arena Belajar'
            && str_contains((string) ($learnContext['participant'] ?? ''), $learningUserName));
        if ($learnContextOk) {
            $passedCount++;
            CLI::write("  [TEST 3.1] Learning approval context displays participant '{$learningUserName}': PASS", "green");
        } else {
            CLI::write("  [TEST 3.1] Learning approval context participant: FAIL (Got: " . ($learnContext['participant'] ?? 'null') . ")", "red");
        }

        $totalCount++;
        $eventContext = $refMethod->invoke($approvalController, 'certificate', $eventRegId, [
            'notes'        => '[source:event_registration]',
            'requested_by' => $eventUserId,
        ]);
        $eventContextOk = ($eventContext !== null
            && $eventContext['category'] === 'Agenda / Event PBJ'
            && str_contains((string) ($eventContext['participant'] ?? ''), $eventUserName));
        if ($eventContextOk) {
            $passedCount++;
            CLI::write("  [TEST 3.2] Event approval context displays participant '{$eventUserName}': PASS", "green");
        } else {
            CLI::write("  [TEST 3.2] Event approval context participant: FAIL (Got: " . ($eventContext['participant'] ?? 'null') . ")", "red");
        }

        // ==================================================================
        // CLEANUP TEST DATA
        // ==================================================================
        $db->table('certificates')->whereIn('id', [$newLearnCertId, $newEventCertId])->delete();
        $db->table('approvals')->whereIn('id', [$learnApprovalId, $eventApprovalId])->delete();
        $db->table('attendances')->where('event_registration_id', $eventRegId)->delete();
        $db->table('event_registrations')->where('id', $eventRegId)->delete();
        $db->table('enrollments')->where('id', $enrollmentId)->delete();
        $db->table('notifications')->whereIn('user_id', [$learnUserId, $eventUserId])->delete();
        $db->table('users')->whereIn('id', [$learnUserId, $eventUserId])->delete();

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("    TEST SUMMARY: {$passedCount} / {$totalCount} TESTS PASSED                     ", $passedCount === $totalCount ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        if ($passedCount !== $totalCount) {
            CLI::error("Beberapa pengujian gagal. Silakan periksa log di atas.");
            return 1;
        }

        CLI::write("SELURUH ALUR RE-QUEUE & REGENERATE TEREVALUASI 100% AMAN & AKURAT!", "green");
        return 0;
    }
}
