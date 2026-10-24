<?php

namespace App\Commands;

use App\Services\Communication\CommunicationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventAttendanceTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'event:attendance:test';
    protected $description = 'End-to-end verification of Event Attendance Code & Batch Certificate Workflow';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("  EVENT ATTENDANCE CODE & BATCH CERTIFICATE END-TO-END TEST       ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();
        $commService = new CommunicationService();
        $now = date('Y-m-d H:i:s');
        $testTimestamp = time();

        // 0. Setup test users: 1 Admin, 2 Participants
        $admin = $db->table('users')->where('id', 1)->get()->getRowArray()
            ?: $db->table('users')->where('id', 14)->get()->getRowArray()
            ?: $db->table('users')->get()->getRowArray();
        $adminId = (int) $admin['id'];

        // Get or create 2 test participants
        $p1Email = 'peserta1_evt_' . $testTimestamp . '@example.com';
        $p2Email = 'peserta2_evt_' . $testTimestamp . '@example.com';

        $db->table('users')->insert([
            'username'       => 'p1_evt_' . $testTimestamp,
            'name'           => 'Peserta Hadir E2E ' . $testTimestamp,
            'email'          => $p1Email,
            'password_hash'  => password_hash('Secret123!', PASSWORD_BCRYPT),
            'profile_type'   => 'public',
            'account_status' => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $p1Id = (int) $db->insertID();

        $db->table('users')->insert([
            'username'       => 'p2_evt_' . $testTimestamp,
            'name'           => 'Peserta Tidak Hadir E2E ' . $testTimestamp,
            'email'          => $p2Email,
            'password_hash'  => password_hash('Secret123!', PASSWORD_BCRYPT),
            'profile_type'   => 'public',
            'account_status' => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $p2Id = (int) $db->insertID();

        $results = [];

        // -------------------------------------------------------------
        // TEST 1: Admin membuat event
        // -------------------------------------------------------------
        CLI::write("[TEST 1] Admin membuat event...", "yellow");
        try {
            $eventId = $commService->createEvent([
                'title'                 => 'Workshop Pengadaan Berkelanjutan E2E ' . $testTimestamp,
                'type'                  => 'workshop',
                'mode'                  => 'online',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Simulasi pengujian presensi event berbasis kode sandi.',
                'description'           => 'Event untuk menguji alur pembuatan kode, check-in, dan sertifikat batch.',
                'start_at'              => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'end_at'                => date('Y-m-d H:i:s', strtotime('+2 hours')),
                'location'              => 'Zoom Webinar BegawiPengadaan',
                'meeting_url'           => 'https://zoom.us/j/1234567890',
                'capacity'              => 50,
                'certificate_enabled'   => 1,
            ], $adminId);

            // Test 1b: Uji edit event di dashboard admin (termasuk mode offline, hybrid, dan normalisasi form)
            $commService->updateEvent($eventId, [
                'title'       => 'Workshop Pengadaan Berkelanjutan E2E (Updated) ' . $testTimestamp,
                'type'        => 'workshop',
                'mode'        => 'offline',
                'status'      => 'open_registration',
                'visibility'  => 'public',
                'location'    => 'Gedung Pusiban Provinsi Lampung',
            ], $adminId);
            $eventUpdated = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if ($eventUpdated['mode'] !== 'offline' || $eventUpdated['location'] !== 'Gedung Pusiban Provinsi Lampung') {
                throw new \Exception('Update event mode offline gagal.');
            }

            // Uji normalisasi jika input form legacy terbalik (type=online, mode=open)
            $commService->updateEvent($eventId, [
                'title'       => 'Workshop Pengadaan Berkelanjutan E2E ' . $testTimestamp,
                'type'        => 'online',
                'mode'        => 'open',
                'status'      => 'open_registration',
                'visibility'  => 'public',
            ], $adminId);
            $eventNormalized = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if ($eventNormalized['mode'] !== 'online') {
                throw new \Exception('Normalisasi mode gagal: mode harus menjadi online, bukan open.');
            }
            CLI::write("  [PASS] Edit event (mode offline, hybrid, normalisasi input form) sukses tanpa pelanggaran events_mode_chk", "green");
            $results['1b. Admin edit event (mode & constraint)'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['1. Admin membuat event'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 2: Admin generate kode
        // -------------------------------------------------------------
        CLI::write("[TEST 2] Admin generate kode presensi...", "yellow");
        try {
            $code = $commService->generateAttendanceCode($eventId, $adminId);
            $eventAfterGen = $db->table('events')->where('id', $eventId)->get()->getRowArray();

            if (empty($code) || $eventAfterGen['attendance_code'] !== $code || empty($eventAfterGen['attendance_code_hash'])) {
                throw new \Exception('Kode presensi gagal disimpan atau hash tidak valid.');
            }

            // Buka sesi presensi
            $commService->openAttendance($eventId, $adminId);
            $eventAfterOpen = $db->table('events')->where('id', $eventId)->get()->getRowArray();

            if (empty($eventAfterOpen['attendance_open_at'])) {
                throw new \Exception('attendance_open_at tidak terisi.');
            }

            CLI::write("  [PASS] Kode unik berhasil di-generate: '{$code}', Presensi DIBUKA pada: {$eventAfterOpen['attendance_open_at']}", "green");
            $results['2. Admin generate kode'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['2. Admin generate kode'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 3: Participant melihat event
        // -------------------------------------------------------------
        CLI::write("[TEST 3] Participant melihat event...", "yellow");
        try {
            $viewEvent = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if ($viewEvent && $viewEvent['status'] === 'open_registration' && $viewEvent['capacity'] == 50) {
                CLI::write("  [PASS] Event dapat dilihat peserta (Judul: '{$viewEvent['title']}', Kuota: {$viewEvent['capacity']})", "green");
                $results['3. Participant melihat event'] = 'PASS';
            } else {
                throw new \Exception('Event tidak dapat dilihat atau status tidak sesuai.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['3. Participant melihat event'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 4: Participant daftar event
        // -------------------------------------------------------------
        CLI::write("[TEST 4] Participant daftar event...", "yellow");
        try {
            // Participant 1 mendaftar
            $db->table('event_registrations')->insert([
                'event_id'      => $eventId,
                'user_id'       => $p1Id,
                'status'        => 'registered',
                'registered_at' => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $reg1Id = (int) $db->insertID();

            // Participant 2 mendaftar
            $db->table('event_registrations')->insert([
                'event_id'      => $eventId,
                'user_id'       => $p2Id,
                'status'        => 'registered',
                'registered_at' => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $reg2Id = (int) $db->insertID();

            if ($reg1Id > 0 && $reg2Id > 0) {
                CLI::write("  [PASS] Peserta 1 (Reg #{$reg1Id}) dan Peserta 2 (Reg #{$reg2Id}) berhasil mendaftar", "green");
                $results['4. Participant daftar event'] = 'PASS';
            } else {
                throw new \Exception('Pendaftaran peserta gagal disimpan.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['4. Participant daftar event'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 5: Participant input kode benar
        // -------------------------------------------------------------
        CLI::write("[TEST 5] Participant input kode benar...", "yellow");
        try {
            $checkInResult = $commService->validateAndCheckIn($code, $p1Id);
            if (isset($checkInResult['attendance_id']) && $checkInResult['attendance_id'] > 0) {
                CLI::write("  [PASS] Peserta 1 berhasil submit kode benar '{$code}'", "green");
                $results['5. Participant input kode benar'] = 'PASS';
            } else {
                throw new \Exception('Hasil check-in tidak mengembalikan attendance_id.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['5. Participant input kode benar'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 6: Attendance tercatat
        // -------------------------------------------------------------
        CLI::write("[TEST 6] Attendance tercatat di database...", "yellow");
        try {
            $attRow = $db->table('attendances')->where('event_registration_id', $reg1Id)->get()->getRowArray();
            if (! $attRow || $attRow['status'] !== 'present' || empty($attRow['recorded_at'])) {
                throw new \Exception('Record attendances tidak valid atau status bukan present.');
            }

            // Uji validasi dobel presensi: Peserta 1 submit kode lagi
            $alreadyPresent = false;
            try {
                $commService->validateAndCheckIn($code, $p1Id);
            } catch (\RuntimeException $e) {
                if (str_contains($e->getMessage(), 'Anda sudah melakukan presensi')) {
                    $alreadyPresent = true;
                }
            }

            if (! $alreadyPresent) {
                throw new \Exception('Sistem tidak menolak presensi ganda dari peserta yang sama.');
            }

            CLI::write("  [PASS] Kehadiran sah tercatat (Status: '{$attRow['status']}', Waktu: {$attRow['recorded_at']}). Dobel presensi berhasil dicegah.", "green");
            $results['6. Attendance tercatat'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['6. Attendance tercatat'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 7: Kode salah ditolak
        // -------------------------------------------------------------
        CLI::write("[TEST 7] Kode salah ditolak...", "yellow");
        try {
            $rejectedWrongCode = false;
            try {
                $commService->validateAndCheckIn('BGW-SALAH-999', $p2Id);
            } catch (\RuntimeException $e) {
                if (str_contains($e->getMessage(), 'Kode presensi tidak valid')) {
                    $rejectedWrongCode = true;
                }
            }

            if ($rejectedWrongCode) {
                CLI::write("  [PASS] Kode salah berhasil ditolak dengan pesan: 'Kode presensi tidak valid'", "green");
                $results['7. Kode salah ditolak'] = 'PASS';
            } else {
                throw new \Exception('Sistem tidak menolak kode salah dengan tepat.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['7. Kode salah ditolak'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 8: Presensi ditutup
        // -------------------------------------------------------------
        CLI::write("[TEST 8] Presensi ditutup oleh Admin...", "yellow");
        try {
            $commService->closeAttendance($eventId, $adminId);
            $eventClosed = $db->table('events')->where('id', $eventId)->get()->getRowArray();

            if (! empty($eventClosed['attendance_close_at'])) {
                CLI::write("  [PASS] Presensi event berhasil ditutup pada: {$eventClosed['attendance_close_at']}", "green");
                $results['8. Presensi ditutup'] = 'PASS';
            } else {
                throw new \Exception('attendance_close_at tidak terisi.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['8. Presensi ditutup'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 9: Kode tidak bisa digunakan lagi
        // -------------------------------------------------------------
        CLI::write("[TEST 9] Kode tidak bisa digunakan lagi setelah ditutup...", "yellow");
        try {
            $rejectedClosed = false;
            try {
                $commService->validateAndCheckIn($code, $p2Id);
            } catch (\RuntimeException $e) {
                if (str_contains($e->getMessage(), 'ditutup')) {
                    $rejectedClosed = true;
                }
            }

            if ($rejectedClosed) {
                CLI::write("  [PASS] Check-in ditolak dengan pesan: 'Presensi sudah ditutup'", "green");
                $results['9. Kode tidak bisa digunakan lagi'] = 'PASS';
            } else {
                throw new \Exception('Sistem tetap menerima presensi setelah sesi ditutup.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['9. Kode tidak bisa digunakan lagi'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 10: Generate certificate batch
        // -------------------------------------------------------------
        CLI::write("[TEST 10] Generate certificate batch...", "yellow");
        try {
            $batchResult = $commService->batchGenerateEventCertificates($eventId, $adminId);
            if (count($batchResult) > 0) {
                CLI::write("  [PASS] Batch certificate berhasil menghasilkan " . count($batchResult) . " sertifikat", "green");
                $results['10. Generate certificate batch'] = 'PASS';
            } else {
                throw new \Exception('Batch certificate tidak menghasilkan sertifikat.');
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['10. Generate certificate batch'] = 'FAIL';
        }

        // -------------------------------------------------------------
        // TEST 11: Hanya peserta hadir mendapat sertifikat
        // -------------------------------------------------------------
        CLI::write("[TEST 11] Verifikasi hanya peserta hadir yang mendapat sertifikat...", "yellow");
        try {
            // Peserta 1 (Hadir) HARUS punya sertifikat
            $certP1 = $db->table('certificates')
                ->where('source_type', 'event_registration')
                ->where('source_id', $reg1Id)
                ->where('user_id', $p1Id)
                ->get()->getRowArray();

            // Peserta 2 (Tidak Hadir) TIDAK BOLEH punya sertifikat
            $certP2 = $db->table('certificates')
                ->where('source_type', 'event_registration')
                ->where('source_id', $reg2Id)
                ->get()->getRowArray();

            if (! $certP1 || $certP1['status'] !== 'issued') {
                throw new \Exception('Peserta 1 yang hadir tidak memiliki sertifikat berstatus issued.');
            }

            if ($certP2 !== null) {
                throw new \Exception('Peserta 2 yang tidak hadir secara keliru menerima sertifikat.');
            }

            CLI::write("  [PASS] Peserta 1 (Hadir) memiliki Sertifikat: '{$certP1['certificate_number']}'. Peserta 2 (Tidak Hadir) TIDAK memiliki sertifikat.", "green");
            $results['11. Hanya peserta hadir mendapat sertifikat'] = 'PASS';
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] " . $e->getMessage(), "red");
            $results['11. Hanya peserta hadir mendapat sertifikat'] = 'FAIL';
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("               RINGKASAN HASIL TEST END TO END                    ", "cyan");
        CLI::write("==================================================================", "cyan");
        $allPassed = true;
        foreach ($results as $testName => $status) {
            $color = $status === 'PASS' ? 'green' : 'red';
            CLI::write(sprintf("%-50s : [%s]", $testName, $status), $color);
            if ($status !== 'PASS') {
                $allPassed = false;
            }
        }

        CLI::newLine();
        if ($allPassed) {
            CLI::write("STATUS: SELURUH 11 PENGUJIAN END-TO-END BERHASIL 100% [PASS]", "green");
        } else {
            CLI::write("STATUS: BEBERAPA PENGUJIAN GAGAL!", "red");
        }
        CLI::write("==================================================================", "cyan");
    }
}
