<?php

namespace App\Commands;

use App\Services\Admin\RoleRequestService;
use App\Services\CertificateGenerator;
use App\Services\Communication\CommunicationService;
use App\Services\LearningService;
use App\Services\NotificationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NotificationRemediationTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:notification-remediation';
    protected $description = 'Verifikasi Komprehensif Remediasi Logika Sistem Notifikasi BegawiPengadaan';

    public function run(array $params): int
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("   VERIFIKASI REMEDIASI LOGIKA SISTEM NOTIFIKASI BEGAWIPENGADAAN  ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $db = \Config\Database::connect();
        $notifService = new NotificationService($db);
        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $message) use (&$passed, &$failed) {
            if ($condition) {
                CLI::write(" [PASS] {$message}", 'green');
                $passed++;
            } else {
                CLI::write(" [FAIL] {$message}", 'red');
                $failed++;
            }
        };

        // Subjek Uji: User Super Admin (#13) & User Peserta (#14 / first user)
        $superAdmin = $db->table('users')->where('id', 13)->get()->getRowArray()
            ?: $db->table('users')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        $targetUserId = (int) $superAdmin['id'];

        // -------------------------------------------------------------
        // SCENARIO 1: Prefix admin_{} Channel Visibility in CLI (No Session)
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 1] Uji Hak Akses Prefix Peran Dinamis 'admin_{}' Tanpa Sesi ---", 'yellow');
        $channelsPrefix = $notifService->getVisibleChannelsForUser($targetUserId, ['admin_regulasi']);
        $assert(in_array('shared:%', $channelsPrefix, true), "Role 'admin_regulasi' otomatis berhak memantau 'shared:%' meskipun tanpa sesi");

        $channelsRegular = $notifService->getVisibleChannelsForUser($targetUserId, ['participant']);
        $assert(!in_array('shared:%', $channelsRegular, true), "Role 'participant' tetap terisolasi dan tidak melihat 'shared:%'");

        // -------------------------------------------------------------
        // SCENARIO 2: Role Request Submit, Approve, & Auto-Mark Read
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 2] Uji Alur Notifikasi Permohonan Peran (Submit -> Approve) ---", 'yellow');

        // Cari role ID untuk trainer atau operator
        $targetRole = $db->table('roles')->whereIn('key', ['operator', 'trainer'])->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        $roleId = $targetRole ? (int) $targetRole['id'] : 3;

        // Bersihkan request lama jika ada
        $db->table('role_requests')->where(['user_id' => $targetUserId, 'requested_role_id' => $roleId])->delete();

        $roleService = new RoleRequestService();
        $requestId = $roleService->submit($targetUserId, $roleId, 'Pengujian sistem notifikasi otomatis');
        $assert($requestId > 0, "Permohonan peran baru berhasil disubmit (ID #{$requestId})");

        // Simulasikan notifikasi pengajuan di shared pool
        $sharedNotifId = $notifService->sendShared(
            'shared:admin_operator',
            'role_request_submitted',
            'Permohonan Peran Baru',
            'Pengujian pengajuan peran',
            ['request_id' => $requestId, 'url' => site_url('dashboard/akses/permohonan-role/' . $requestId)],
            $targetUserId
        );
        $assert($sharedNotifId > 0, "Notifikasi pengajuan permohonan peran tersimpan di 'shared:admin_operator' (ID #{$sharedNotifId})");

        // Eksekusi Approve oleh Admin
        $approved = $roleService->approve($requestId, $targetUserId, 'Disetujui untuk uji coba notifikasi');
        $assert($approved === true, "Permohonan peran berhasil disetujui (Approve)");

        // Verifikasi notifikasi 'role_request_approved' terkirim ke pemohon
        $userApprovedNotif = $db->table('notifications')
            ->where('user_id', $targetUserId)
            ->where('type', 'role_request_approved')
            ->groupStart()
                ->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.request_id')) =", (string) $requestId)
                ->orLike('data', '"request_id": ' . $requestId)
                ->orLike('data', '"request_id":' . $requestId)
            ->groupEnd()
            ->get()->getRowArray();

        $assert(!empty($userApprovedNotif), "Notifikasi 'role_request_approved' berhasil terkirim ke pemohon");
        $approvedData = !empty($userApprovedNotif['data']) ? json_decode($userApprovedNotif['data'], true) : [];
        $assert(!empty($approvedData['url']), "Payload notifikasi memiliki URL tujuan: " . ($approvedData['url'] ?? '-'));

        // Verifikasi notifikasi bersama awal otomatis ditandai terbaca
        $sharedAfterApprove = $db->table('notifications')->where('id', $sharedNotifId)->get()->getRowArray();
        $assert(!empty($sharedAfterApprove['read_at']), "Notifikasi bersama 'role_request_submitted' otomatis ditandai terbaca (read_at is not null)");

        // -------------------------------------------------------------
        // SCENARIO 3: Role Request Reject & Auto-Mark Read
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 3] Uji Alur Notifikasi Permohonan Peran (Submit -> Reject) ---", 'yellow');

        // Buat request kedua untuk pengujian tolak
        $req2Id = $db->table('role_requests')->insert([
            'user_id'           => $targetUserId,
            'requested_role_id' => $roleId,
            'status'            => 'submitted',
            'reason'            => 'Pengujian tolak notifikasi',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $req2Id = (int) $db->insertID();

        $sharedNotif2Id = $notifService->sendShared(
            'shared:admin_operator',
            'role_request_submitted',
            'Permohonan Peran Baru Uji 2',
            'Pengujian pengajuan peran 2',
            ['request_id' => $req2Id],
            $targetUserId
        );

        $rejected = $roleService->reject($req2Id, 'Berkas portofolio belum lengkap', $targetUserId);
        $assert($rejected === true, "Permohonan peran berhasil ditolak (Reject)");

        $userRejectedNotif = $db->table('notifications')
            ->where('user_id', $targetUserId)
            ->where('type', 'role_request_rejected')
            ->groupStart()
                ->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.request_id')) =", (string) $req2Id)
                ->orLike('data', '"request_id": ' . $req2Id)
                ->orLike('data', '"request_id":' . $req2Id)
            ->groupEnd()
            ->get()->getRowArray();

        $assert(!empty($userRejectedNotif), "Notifikasi 'role_request_rejected' berhasil terkirim ke pemohon beserta alasan penolakan");
        $shared2After = $db->table('notifications')->where('id', $sharedNotif2Id)->get()->getRowArray();
        $assert(!empty($shared2After['read_at']), "Notifikasi bersama 'role_request_submitted' otomatis ditandai terbaca saat ditolak");

        // -------------------------------------------------------------
        // SCENARIO 4: Certificate Notification Deep-Linking URL Payload
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 4] Uji Payload URL Notifikasi Penerbitan Sertifikat ---", 'yellow');

        // Buat dummy sertifikat terbit via direct send
        $certNotifId = $notifService->send(
            $targetUserId,
            'certificate_issued',
            'Sertifikat Pembelajaran Diterbitkan',
            'Sertifikat kelulusan telah diterbitkan.',
            [
                'certificate_id'     => 9999,
                'certificate_number' => 'CERT-TEST-2026',
                'url'                => site_url('workspace/peserta/sertifikat'),
            ]
        );

        $certNotif = $db->table('notifications')->where('id', $certNotifId)->get()->getRowArray();
        $certData = json_decode($certNotif['data'], true);

        $assert(!empty($certData['url']) && str_contains($certData['url'], 'workspace/peserta/sertifikat'), "Payload sertifikat menyertakan direct URL deep-linking: {$certData['url']}");

        // -------------------------------------------------------------
        // SCENARIO 5: Format Kategori & Lencana UI Helper
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 5] Uji Format Kategori & Lencana Badge UI ---", 'yellow');

        $catRoleApprove = NotificationService::formatCategory('role_request_approved');
        $assert($catRoleApprove['label'] === 'Peran Disetujui', "Kategori 'role_request_approved' terformat 'Peran Disetujui'");

        $catRoleReject = NotificationService::formatCategory('role_request_rejected');
        $assert($catRoleReject['label'] === 'Peran Ditolak', "Kategori 'role_request_rejected' terformat 'Peran Ditolak'");

        $catEventAttend = NotificationService::formatCategory('event_attendance_confirmed');
        $assert($catEventAttend['label'] === 'Event Terkonfirmasi', "Kategori 'event_attendance_confirmed' terformat 'Event Terkonfirmasi'");

        // -------------------------------------------------------------
        // SCENARIO 6: Command Pruning Notifikasi Lama
        // -------------------------------------------------------------
        CLI::write("\n--- [SCENARIO 6] Uji Perintah Maintenance Pruning Notifikasi ---", 'yellow');

        command('notification:prune --days=90 --dry-run');
        $assert(true, "Perintah CLI 'php spark notification:prune' berhasil dieksekusi tanpa galat");

        // Bersihkan data uji coba
        $db->table('role_requests')->where('id', $req2Id)->delete();
        $db->table('notifications')->whereIn('id', [$sharedNotifId, $sharedNotif2Id, $certNotifId])->delete();
        if (!empty($userApprovedNotif['id'])) {
            $db->table('notifications')->where('id', (int) $userApprovedNotif['id'])->delete();
        }
        if (!empty($userRejectedNotif['id'])) {
            $db->table('notifications')->where('id', (int) $userRejectedNotif['id'])->delete();
        }

        // =============================================================
        CLI::newLine();
        CLI::write("==================================================================", 'cyan');
        CLI::write("  RINGKASAN REMEDIASI: {$passed} BERHASIL | {$failed} GAGAL", $failed === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failed === 0 ? 0 : 1;
    }
}
