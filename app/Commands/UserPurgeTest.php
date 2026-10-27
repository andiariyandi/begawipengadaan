<?php

namespace App\Commands;

use App\Services\Admin\UserPurgeService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UserPurgeTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-purge';
    protected $description = 'Verifies User Purge Cascading Foreign Key Deletion with Guardrails';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FITUR HAPUS PENGGUNA (CASCADE FOREIGN KEY) ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();
        $purgeService = new UserPurgeService();

        // 1. Guardrail Test: Master ID 14
        try {
            $purgeService->purge(14, 13, 'HAPUS');
            CLI::error("[FAIL] ID 14 harusnya dilindungi!");
            $failCount++;
        } catch (\InvalidArgumentException $e) {
            CLI::write("[PASS] Guardrail ID 14 dilindungi: " . $e->getMessage(), 'yellow');
            $passCount++;
        }

        // 2. Guardrail Test: Master ID 13
        try {
            $purgeService->purge(13, 14, 'HAPUS');
            CLI::error("[FAIL] ID 13 harusnya dilindungi!");
            $failCount++;
        } catch (\InvalidArgumentException $e) {
            CLI::write("[PASS] Guardrail ID 13 dilindungi: " . $e->getMessage(), 'yellow');
            $passCount++;
        }

        // 3. Guardrail Test: Self-deletion
        try {
            $purgeService->purge(300, 300, 'HAPUS');
            CLI::error("[FAIL] Self-deletion harusnya dicegah!");
            $failCount++;
        } catch (\InvalidArgumentException $e) {
            CLI::write("[PASS] Guardrail Self-deletion dicegah: " . $e->getMessage(), 'yellow');
            $passCount++;
        }

        // 4. Guardrail Test: Teks konfirmasi harus HAPUS
        try {
            $purgeService->purge(300, 14, 'TIDAK_VALID');
            CLI::error("[FAIL] Konfirmasi teks salah harusnya ditolak!");
            $failCount++;
        } catch (\InvalidArgumentException $e) {
            CLI::write("[PASS] Guardrail Konfirmasi teks salah ditolak: " . $e->getMessage(), 'yellow');
            $passCount++;
        }

        // 5. Setup dummy user dengan relasi lengkap
        CLI::write("\n--- Menyiapkan Data Pengujian User & Relasi Lengkap ---", 'cyan');
        // Bersihkan jika ada sisa uji sebelumnya menggunakan PurgeService
        $oldUsers = $db->table('users')->select('id')->like('username', 'purge_e2e_')->get()->getResultArray();
        foreach ($oldUsers as $ou) {
            try {
                $purgeService->purge((int) $ou['id'], 14, 'HAPUS');
            } catch (\Throwable $ignored) {
                $db->table('user_profiles')->where('user_id', (int) $ou['id'])->delete();
                $db->table('asn_profiles')->where('user_id', (int) $ou['id'])->delete();
                $db->table('users')->where('id', (int) $ou['id'])->delete();
            }
        }
        $time = time();
        $dummyUsername = "purge_e2e_{$time}";
        $dummyEmail    = "purge_e2e_{$time}@example.com";

        $db->table('users')->insert([
            'username'       => $dummyUsername,
            'email'          => $dummyEmail,
            'password_hash'  => password_hash('Secret123!', PASSWORD_BCRYPT),
            'name'           => 'Subjek Uji Purge Cascade',
            'phone'          => '081234567890',
            'profile_type'   => 'asn',
            'account_status' => 'active',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
        $dummyUserId = (int) $db->insertID();

        // Profil & Peran
        $db->table('user_profiles')->insert([
            'user_id'                   => $dummyUserId,
            'address_line'              => 'Jl. Uji Coba No. 1',
            'province_code'             => '18',
            'regency_code'              => '18.71',
            'district_code'             => '18.71.01',
            'profile_completion_status' => 'complete',
            'created_at'                => date('Y-m-d H:i:s'),
            'updated_at'                => date('Y-m-d H:i:s'),
        ]);
        $db->table('asn_profiles')->insert([
            'user_id'             => $dummyUserId,
            'origin_type'         => 'pemprov_lampung',
            'employee_identifier' => '199501012022011001',
            'institution_name'    => 'Pemerintah Provinsi Lampung',
            'unit_name'           => 'Biro Pengadaan Barang dan Jasa',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);
        $db->table('user_roles')->insert([
            'user_id'    => $dummyUserId,
            'role_id'    => 15,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Notifikasi & Bookmark
        $db->table('notifications')->insert([
            'user_id'    => $dummyUserId,
            'channel'    => 'database',
            'type'       => 'system',
            'title'      => 'Notifikasi Purge',
            'body'       => 'Pesan uji coba',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $db->table('bookmarks')->insert([
            'user_id'           => $dummyUserId,
            'bookmarkable_type' => 'news',
            'bookmarkable_id'   => 1,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // LMS: Enrollment, Quiz Attempt, Quiz Answer, Assignment Submission, Files
        $class = $db->table('learning_classes')->select('id')->get()->getRowArray();
        $classId = $class ? (int) $class['id'] : 1;

        $db->table('enrollments')->insert([
            'user_id'     => $dummyUserId,
            'class_id'    => $classId,
            'status'      => 'active',
            'enrolled_at' => date('Y-m-d H:i:s'),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $enrollmentId = (int) $db->insertID();

        $quiz = $db->table('quizzes')->select('id')->where('class_id', $classId)->get()->getRowArray();
        $quizId = $quiz ? (int) $quiz['id'] : 1;

        $db->table('quiz_attempts')->insert([
            'enrollment_id'  => $enrollmentId,
            'quiz_id'        => $quizId,
            'attempt_number' => 1,
            'status'         => 'submitted',
            'started_at'     => date('Y-m-d H:i:s'),
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
        $attemptId = (int) $db->insertID();

        $qQuestion = $db->table('quiz_questions')->select('id')->where('quiz_id', $quizId)->get()->getRowArray();
        $questionId = $qQuestion ? (int) $qQuestion['id'] : 1;

        $db->table('quiz_answers')->insert([
            'attempt_id'  => $attemptId,
            'question_id' => $questionId,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $assign = $db->table('assignments')->select('id')->where('class_id', $classId)->get()->getRowArray();
        $assignId = $assign ? (int) $assign['id'] : 1;

        $db->table('assignment_submissions')->insert([
            'enrollment_id'  => $enrollmentId,
            'assignment_id'  => $assignId,
            'attempt_number' => 1,
            'status'         => 'submitted',
            'submitted_at'   => date('Y-m-d H:i:s'),
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
        $subId = (int) $db->insertID();

        $fileAsset = $db->table('file_assets')->select('id')->get()->getRowArray();
        if ($fileAsset) {
            $db->table('assignment_submission_files')->insert([
                'submission_id' => $subId,
                'file_id'       => (int) $fileAsset['id'],
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // Sertifikat
        $db->table('certificates')->insert([
            'source_type'            => 'learning_enrollment',
            'source_id'              => $enrollmentId,
            'user_id'                => $dummyUserId,
            'certificate_number'     => 'CERT-E2E-' . $time,
            'verification_token_hash'=> hash('sha256', 'dummy_' . $time),
            'status'                 => 'issued',
            'issued_at'              => date('Y-m-d H:i:s'),
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);

        // Event Registration & Kehadiran
        $event = $db->table('events')->select('id')->get()->getRowArray();
        if ($event) {
            $eventId = (int) $event['id'];
            $db->table('event_registrations')->insert([
                'user_id'       => $dummyUserId,
                'event_id'      => $eventId,
                'status'        => 'registered',
                'registered_at' => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $evRegId = (int) $db->insertID();
            $db->table('attendances')->insert([
                'event_registration_id' => $evRegId,
                'status'                => 'present',
                'recorded_at'           => date('Y-m-d H:i:s'),
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);
        }

        // Bunga Rampai Artikel & Berita (ditulis oleh dummy user)
        $db->table('knowledge_articles')->insert([
            'type'            => 'article',
            'title'           => 'Artikel PBJ Uji Purge ' . $time,
            'slug'            => 'artikel-pbj-uji-purge-' . $time,
            'content'         => 'Konten artikel PBJ penting',
            'author_id'       => $dummyUserId,
            'workflow_status' => 'published',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        $articleId = (int) $db->insertID();

        // 6. Impact Summary Test
        $summary = $purgeService->getImpactSummary($dummyUserId);
        if ($summary['counts']['enrollments'] >= 1 && $summary['counts']['certificates'] >= 1 && $summary['counts']['knowledge_articles'] >= 1) {
            CLI::write("[PASS] Impact Summary akurat: " . json_encode($summary['counts']), 'green');
            $passCount++;
        } else {
            CLI::error("[FAIL] Perhitungan impact summary tidak akurat!");
            $failCount++;
        }

        // 7. Eksekusi Purge
        CLI::write("\n--- Mengeksekusi Purge Service ---", 'cyan');
        try {
            $res = $purgeService->purge($dummyUserId, 14, 'HAPUS');
            CLI::write("[PASS] Purge berhasil: " . $res['message'], 'green');
            $passCount++;
        } catch (\Throwable $e) {
            CLI::error("[FAIL] Purge melempar exception: " . $e->getMessage());
            $failCount++;
        }

        // 8. Verifikasi Data Terhapus Bersih
        $checkUser = $db->table('users')->where('id', $dummyUserId)->get()->getRowArray();
        if ($checkUser === null) {
            CLI::write("[PASS] Baris pengguna di tabel `users` telah terhapus fisik permanen.", 'green');
            $passCount++;
        } else {
            CLI::error("[FAIL] Baris pengguna masih ada di `users`!");
            $failCount++;
        }

        $checkChild = $db->table('enrollments')->where('user_id', $dummyUserId)->countAllResults()
            + $db->table('certificates')->where('user_id', $dummyUserId)->countAllResults()
            + $db->table('user_profiles')->where('user_id', $dummyUserId)->countAllResults()
            + $db->table('asn_profiles')->where('user_id', $dummyUserId)->countAllResults()
            + $db->table('user_roles')->where('user_id', $dummyUserId)->countAllResults();

        if ($checkChild === 0) {
            CLI::write("[PASS] Seluruh baris tabel anak (enrollments, certificates, profiles, roles) telah bersih.", 'green');
            $passCount++;
        } else {
            CLI::error("[FAIL] Masih ditemukan sisa data anak ($checkChild baris)!");
            $failCount++;
        }

        // 9. Verifikasi Artikel PBJ Dialihkan (Reassigned) ke ID 14
        $checkArt = $db->table('knowledge_articles')->where('id', $articleId)->get()->getRowArray();
        if ($checkArt && (int) $checkArt['author_id'] === 14) {
            CLI::write("[PASS] Artikel Bunga Rampai TIDAK terhapus dan author_id berhasil dialihkan ke Super Admin Master (ID 14).", 'green');
            $passCount++;
            // Bersihkan dummy article
            $db->table('knowledge_articles')->where('id', $articleId)->delete();
        } else {
            CLI::error("[FAIL] Reassign artikel gagal!");
            $failCount++;
        }

        // 10. Verifikasi Audit Log Tercatat
        $auditRow = $db->table('audit_logs')
            ->where('action', 'user.purge_permanent')
            ->where('resource_id', $dummyUserId)
            ->get()->getRowArray();
        if ($auditRow) {
            CLI::write("[PASS] Log audit `user.purge_permanent` berhasil terekam di sistem audit.", 'green');
            $passCount++;
        } else {
            CLI::error("[FAIL] Audit log tidak ditemukan!");
            $failCount++;
        }

        CLI::write("\n=======================================================", 'green');
        CLI::write("HASIL PENGUJIAN USER PURGE: $passCount PASS, $failCount FAIL", $failCount === 0 ? 'green' : 'red');
        CLI::write("=======================================================\n", 'green');

        return $failCount === 0 ? 0 : 1;
    }
}
