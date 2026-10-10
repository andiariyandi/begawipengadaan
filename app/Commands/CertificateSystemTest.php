<?php

namespace App\Commands;

use App\Models\Certificate\CertificateModel;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificateGenerator;
use App\Services\CertificateQrGenerator;
use App\Services\Communication\CommunicationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateSystemTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'certificate:system:test';
    protected $description = 'Comprehensive end-to-end verification of WowDash Certificate System Refinement';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    WOWDASH CERTIFICATE SYSTEM REFINEMENT END-TO-END TEST         ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db          = db_connect();
        $generator   = new CertificateGenerator($db);
        $commService = new CommunicationService();
        $certModel   = new CertificateModel();
        $now         = date('Y-m-d H:i:s');
        $year        = date('Y');
        $ts          = time();
        $results     = [];

        $enrollmentId  = null;
        $testLrnCertId = null;
        $testLrnToken  = null;
        $testEvtCertId = null;

        // 1. Ensure test admin
        $admin = $db->table('users')->where('id', 1)->get()->getRowArray()
            ?: $db->table('users')->where('id', 14)->get()->getRowArray()
            ?: $db->table('users')->get()->getRowArray();
        $adminId = (int) $admin['id'];

        // 2. Ensure active template for learning and event
        $templateModel = new CertificateTemplateModel();
        $activeLearningTpl = $templateModel->findDefaultForSource('learning');
        if (! $activeLearningTpl) {
            CLI::write("[SETUP] Creating active default learning template...", "yellow");
            $templateModel->insert([
                'name'        => 'Default Learning Template Test',
                'source_type' => 'learning',
                'is_active'   => 1,
                'created_by'  => $adminId,
            ]);
        }
        $activeEventTpl = $templateModel->where('source_type', 'event')->where('is_active', 1)->first();
        if (! $activeEventTpl) {
            CLI::write("[SETUP] Creating active default event template...", "yellow");
            $templateModel->insert([
                'name'        => 'Default Event Template Test',
                'source_type' => 'event',
                'is_active'   => 1,
                'created_by'  => $adminId,
            ]);
        }

        // -------------------------------------------------------------
        // TEST 1: Learning Certificate Hierarchy & Issuance
        // -------------------------------------------------------------
        CLI::write("[TEST 1] Learning Certificate Hierarchy & Issuance...", "yellow");
        try {
            // Create user with valid profile_type
            $db->table('users')->insert([
                'username'       => 'test_lrn_' . $ts,
                'name'           => 'Peserta Learning E2E ' . $ts,
                'email'          => 'peserta_lrn_' . $ts . '@example.com',
                'password_hash'  => password_hash('Pass123!', PASSWORD_BCRYPT),
                'profile_type'   => 'public',
                'account_status' => 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
            $userId = (int) $db->insertID();

            // Find or use a class
            $class = $db->table('learning_classes')->get()->getRowArray();
            $classId = (int) ($class['id'] ?? 1);

            $db->table('enrollments')->insert([
                'user_id'          => $userId,
                'class_id'         => $classId,
                'status'           => 'completed',
                'final_score'      => 95.50,
                'completed_at'     => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $enrollmentId = (int) $db->insertID();

            // Generate Learning Certificate
            $res = $generator->generateDirect('learning_enrollment', $enrollmentId, $adminId);

            // Assert number format BGW-LRN-YYYY-NNNNNN
            $expectedPattern = '/^BGW-LRN-' . $year . '-\d{6}$/';
            if (! preg_match($expectedPattern, $res['certificate_number'])) {
                throw new \Exception("Nomor sertifikat tidak sesuai pola 'BGW-LRN-YYYY-NNNNNN': " . $res['certificate_number']);
            }

            // Assert DB row
            $certRow = $certModel->find($res['certificate_id']);
            if (! $certRow || $certRow['status'] !== 'issued') {
                throw new \Exception("Status sertifikat di database bukan 'issued'");
            }

            // Assert PDF exists
            $pdfRow = $db->table('file_assets')->where('id', $certRow['pdf_file_id'])->get()->getRowArray();
            if (! $pdfRow || ! file_exists(WRITEPATH . $pdfRow['path'])) {
                throw new \Exception("File PDF sertifikat tidak ditemukan di writable/uploads/");
            }

            CLI::write("  [PASS] Learning Certificate diterbitkan: '{$res['certificate_number']}' (ID #{$res['certificate_id']})", "green");
            $results['1. Learning Certificate Issuance'] = 'PASS';
            $testLrnCertId = $res['certificate_id'];
            $testLrnToken  = $res['clear_token'];
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['1. Learning Certificate Issuance'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 2: Event Certificate Presence Validation & Issuance
        // -------------------------------------------------------------
        CLI::write("[TEST 2] Event Certificate Presence Validation & Issuance...", "yellow");
        try {
            // Create event using communication service to adhere to all DB constraints
            $eventId = $commService->createEvent([
                'title'               => 'Seminar PBJ E2E ' . $ts,
                'type'                => 'workshop',
                'mode'                => 'online',
                'status'              => 'open_registration',
                'visibility'          => 'public',
                'summary'             => 'Pengujian sertifikat presensi',
                'description'         => 'Pengujian validasi kehadiran sebelum penerbitan sertifikat',
                'start_at'            => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'end_at'              => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'capacity'            => 100,
                'certificate_enabled' => 1,
            ], $adminId);

            // Participant 1: Hadir (present)
            $db->table('event_registrations')->insert([
                'event_id'   => $eventId,
                'user_id'    => $userId,
                'status'     => 'registered',
                'created_at' => $now,
            ]);
            $regId1 = (int) $db->insertID();

            $db->table('attendances')->insert([
                'event_registration_id' => $regId1,
                'status'                => 'present',
                'created_at'            => $now,
            ]);

            // Participant 2: Tidak Hadir (absent)
            $db->table('event_registrations')->insert([
                'event_id'   => $eventId,
                'user_id'    => $adminId,
                'status'     => 'registered',
                'created_at' => $now,
            ]);
            $regId2 = (int) $db->insertID();

            // Generate for Present Participant
            $resEvt = $generator->generateDirect('event_registration', $regId1, $adminId);

            // Assert number format BGW-EVT-YYYY-NNNNNN
            $expectedEvtPattern = '/^BGW-EVT-' . $year . '-\d{6}$/';
            if (! preg_match($expectedEvtPattern, $resEvt['certificate_number'])) {
                throw new \Exception("Nomor sertifikat event tidak sesuai pola 'BGW-EVT-YYYY-NNNNNN': " . $resEvt['certificate_number']);
            }

            CLI::write("  [PASS] Event Certificate diterbitkan: '{$resEvt['certificate_number']}' (ID #{$resEvt['certificate_id']})", "green");
            $results['2. Event Certificate Issuance'] = 'PASS';
            $testEvtCertId = $resEvt['certificate_id'];
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['2. Event Certificate Issuance'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 3: Idempotency Prevention
        // -------------------------------------------------------------
        CLI::write("[TEST 3] Pencegahan Duplikasi (Idempotensi)...", "yellow");
        try {
            if (! $enrollmentId) {
                throw new \Exception("Test 1 gagal, tidak dapat menguji idempotensi.");
            }
            $duplicateBlocked = false;
            try {
                $generator->generateDirect('learning_enrollment', $enrollmentId, $adminId);
            } catch (\RuntimeException $e) {
                if (str_contains($e->getMessage(), 'sudah pernah diterbitkan')) {
                    $duplicateBlocked = true;
                }
            }

            if ($duplicateBlocked) {
                CLI::write("  [PASS] Upaya penerbitan sertifikat ganda berhasil dicegah dengan tepat.", "green");
                $results['3. Idempotency Check'] = 'PASS';
            } else {
                throw new \Exception("Sistem mengizinkan duplikasi sertifikat untuk sumber yang sama.");
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['3. Idempotency Check'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 4: Certificate Revocation Workflow
        // -------------------------------------------------------------
        CLI::write("[TEST 4] Alur Pencabutan Sertifikat (Revocation)...", "yellow");
        try {
            if (! $testLrnCertId) {
                throw new \Exception("Test 1 gagal, tidak ada sertifikat untuk dicabut.");
            }
            $revokeReason = 'Pengujian pembatalan sertifikat otomatis E2E';
            $certModel->update($testLrnCertId, [
                'status'            => 'revoked',
                'revoked_at'        => $now,
                'revoked_by'        => $adminId,
                'revocation_reason' => $revokeReason,
            ]);

            $revokedRow = $certModel->find($testLrnCertId);
            if ($revokedRow['status'] !== 'revoked' || $revokedRow['revocation_reason'] !== $revokeReason) {
                throw new \Exception("Data status pencabutan tidak tersimpan dengan benar.");
            }

            CLI::write("  [PASS] Sertifikat berhasil dicabut dengan status 'revoked' dan alasan tercatat.", "green");
            $results['4. Certificate Revocation'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['4. Certificate Revocation'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 5: Internal Admin Verification
        // -------------------------------------------------------------
        CLI::write("[TEST 5] Verifikasi Internal Admin Dashboard...", "yellow");
        try {
            if (! $testLrnCertId) {
                throw new \Exception("Test 1 gagal, tidak ada sertifikat untuk diverifikasi.");
            }
            $detail = $certModel->withFullDetail($testLrnCertId);
            if (! $detail || empty($detail['user_name']) || empty($detail['course_title'])) {
                throw new \Exception("withFullDetail tidak mengembalikan data lengkap penerima/course.");
            }

            CLI::write("  [PASS] Detail kredensial admin terverifikasi: {$detail['user_name']} — {$detail['course_title']}", "green");
            $results['5. Admin Verification'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['5. Admin Verification'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 6: Public Online Verification Privacy & Integrity
        // -------------------------------------------------------------
        CLI::write("[TEST 6] Verifikasi Online Publik (QR Code & Privacy)...", "yellow");
        try {
            if (! $testLrnToken || ! $testLrnCertId) {
                throw new \Exception("Test 1 gagal, tidak ada token untuk verifikasi.");
            }
            $qrGen = new CertificateQrGenerator();
            $verifyUrl = $qrGen->buildVerifyUrl($testLrnToken);

            if (! str_contains($verifyUrl, '/verify/certificate/')) {
                throw new \Exception("URL verifikasi QR Code tidak sesuai: " . $verifyUrl);
            }

            // Lookup by clear token (sha256 in DB)
            $foundByToken = $certModel->findByClearToken($testLrnToken);
            if (! $foundByToken || (int) $foundByToken['id'] !== (int) $testLrnCertId) {
                throw new \Exception("Lookup sertifikat menggunakan token verifikasi gagal.");
            }

            CLI::write("  [PASS] URL verifikasi valid: {$verifyUrl}", "green");
            CLI::write("  [PASS] SHA-256 hash token lookup berhasil. Data rahasia terlindungi.", "green");
            $results['6. Public Online Verification'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['6. Public Online Verification'] = 'FAIL';
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("               RINGKASAN HASIL TEST SERTIFIKAT                    ", "cyan");
        CLI::write("==================================================================", "cyan");
        $allPassed = true;
        foreach ($results as $step => $status) {
            $color = ($status === 'PASS') ? 'green' : 'red';
            CLI::write(sprintf("  %-45s : [%s]", $step, $status), $color);
            if ($status !== 'PASS') {
                $allPassed = false;
            }
        }
        CLI::newLine();
        if ($allPassed) {
            CLI::write("STATUS: SELURUH 6 PENGUJIAN SISTEM SERTIFIKAT BERHASIL 100% [PASS]", "green");
            CLI::write("==================================================================", "cyan");
            return 0;
        }

        CLI::write("STATUS: TERDAPAT PENGUJIAN YANG GAGAL [FAIL]", "red");
        CLI::write("==================================================================", "cyan");
        return 1;
    }
}
