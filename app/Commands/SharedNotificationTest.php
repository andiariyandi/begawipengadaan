<?php

namespace App\Commands;

use App\Models\UserModel;
use App\Services\NotificationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SharedNotificationTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:shared-notifications';
    protected $description = 'Uji komprehensif Arsitektur Notifikasi Berbagi Pakai (Shared Pool Engine) BegawiPengadaan.';
    protected $usage       = 'test:shared-notifications';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("  PENGUJIAN ARSITEKTUR NOTIFIKASI BERBAGI PAKAI (SHARED POOL)    ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $service = new NotificationService();
        $db = db_connect();
        $userModel = new UserModel();

        // Cari atau pastikan ada pengguna admin, operator, dan peserta
        $admin = $userModel->where('account_status', 'active')->where('username', 'admin')->first()
            ?: $userModel->where('account_status', 'active')->first();
        $adminId = (int) $admin['id'];

        $operator = $db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('users u', 'u.id = ur.user_id')
            ->where('r.key', 'operator')
            ->where('u.account_status', 'active')
            ->select('u.id, u.name, u.username')
            ->get()->getRowArray();

        $operatorId = $operator ? (int) $operator['id'] : 0;
        if ($operatorId === 0) {
            // Fallback cari role peserta jika belum ada operator terpisah
            $operatorId = $adminId;
        }

        $participant = $db->table('users')
            ->where('account_status', 'active')
            ->where('profile_type', 'asn')
            ->get()->getRowArray() ?: $admin;
        $participantId = (int) $participant['id'];

        CLI::write("Subjek Uji:", "yellow");
        CLI::write(" - Admin ID: #{$adminId} ({$admin['name']})");
        CLI::write(" - Operator ID: #{$operatorId}");
        CLI::write(" - Peserta ID: #{$participantId}");
        CLI::newLine();

        // =========================================================================
        // SCENARIO 1: Uji Pembuatan Notifikasi Shared Pool (1 Baris Tunggal)
        // =========================================================================
        CLI::write("--- [SCENARIO 1] Uji Pembuatan Notifikasi Shared Pool ---", "yellow");
        $token = bin2hex(random_bytes(5));
        $sharedTitle = "Uji Shared Admin Operator {$token}";

        $notifId = $service->sendShared(
            'shared:admin_operator',
            'account_registered',
            $sharedTitle,
            'Pendaftar baru sedang menunggu persetujuan berkas.',
            ['token' => $token, 'url' => site_url('workspace/operator/verifikasi-user')],
            $participantId
        );

        $this->assert($notifId > 0, "Notifikasi shared berhasil dibuat dengan ID #{$notifId}");

        // Verifikasi hanya 1 baris yang tercipta di database
        $rowCount = $db->table('notifications')->where('title', $sharedTitle)->countAllResults();
        $this->assert($rowCount === 1, "Tepat 1 baris tercipta di database (TIDAK ADA DUPLIKASI DATA)");

        // =========================================================================
        // SCENARIO 2: Uji Keterlihatan Bersama (Shared Visibility)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 2] Uji Keterlihatan Bersama di Pool ---", "yellow");

        // Admin harus dapat melihat notifikasi shared:admin_operator
        $adminNotifs = $service->getUserNotifications($adminId, 20, false, ['administrator']);
        $foundInAdmin = false;
        foreach ($adminNotifs as $an) {
            if ((int)$an['id'] === $notifId) {
                $foundInAdmin = true;
                break;
            }
        }
        $this->assert($foundInAdmin, "Admin dapat melihat notifikasi bersama di daftarnya");

        // Operator harus dapat melihat notifikasi shared:admin_operator
        $operatorNotifs = $service->getUserNotifications($operatorId, 20, false, ['operator']);
        $foundInOperator = false;
        foreach ($operatorNotifs as $on) {
            if ((int)$on['id'] === $notifId) {
                $foundInOperator = true;
                break;
            }
        }
        $this->assert($foundInOperator, "Operator dapat melihat notifikasi bersama di daftarnya");

        // Peserta reguler (bukan admin/operator) TIDAK BOLEH melihat notifikasi internal ini
        // Asumsikan peserta hanya memiliki peran 'peserta'
        $regularNotifs = $service->getUserNotifications($participantId, 20, false, ['peserta']);
        $foundInRegular = false;
        foreach ($regularNotifs as $rn) {
            if ((int)$rn['id'] === $notifId) {
                $foundInRegular = true;
                break;
            }
        }
        $this->assert(!$foundInRegular, "Peserta reguler terisolasi (tidak melihat notifikasi internal verifikator)");

        // =========================================================================
        // SCENARIO 3: Uji 'Satu Membaca, Terbaca Semua' (One Reads, All Read)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 3] Uji 'Satu Membaca, Terbaca Semua' ---", "yellow");

        // Catat unread count awal untuk admin dan operator
        $adminUnreadBefore = $service->unreadCount($adminId, ['administrator']);
        $operatorUnreadBefore = $service->unreadCount($operatorId, ['operator']);

        // Operator menandai notifikasi shared sebagai telah dibaca
        $marked = $service->markAsRead($notifId, $operatorId);
        $this->assert($marked, "Operator berhasil menandai notifikasi bersama sebagai dibaca");

        // Verifikasi di database: read_at terisi dan read_by tercatat
        $updatedRow = $db->table('notifications')->where('id', $notifId)->get()->getRowArray();
        $this->assert(!empty($updatedRow['read_at']), "Timestamp read_at terisi: {$updatedRow['read_at']}");

        $updatedData = !empty($updatedRow['data']) ? json_decode($updatedRow['data'], true) : [];
        $this->assert(isset($updatedData['read_by']) && (int)$updatedData['read_by'] === $operatorId, "ID pembaca tercatat akurat (read_by: #{$operatorId})");

        // Verifikasi unread count untuk Admin: otomatis berkurang atau tidak lagi unread!
        $adminUnreadAfter = $service->unreadCount($adminId, ['administrator']);
        $this->assert($adminUnreadAfter <= $adminUnreadBefore, "Unread count Admin otomatis sinkron berkurang setelah dibaca Operator");

        // Notifikasi tidak lagi muncul di filter onlyUnread
        $adminUnreadList = $service->getUserNotifications($adminId, 20, true, ['administrator']);
        $foundUnread = false;
        foreach ($adminUnreadList as $un) {
            if ((int)$un['id'] === $notifId) {
                $foundUnread = true;
                break;
            }
        }
        $this->assert(!$foundUnread, "Notifikasi otomatis lenyap dari daftar 'Belum Dibaca' milik Admin");

        // =========================================================================
        // SCENARIO 4: Uji Kanal Kelas (shared:class:X) untuk Trainer & Operator
        // =========================================================================
        CLI::write("\n--- [SCENARIO 4] Uji Kanal Kelas (shared:class:X) ---", "yellow");
        $classNotifId = $service->sendShared(
            'shared:class:99',
            'class_enrollment_requested',
            "Permohonan Enrollment Kelas 99 {$token}",
            "Peserta mengajukan pendaftaran kelas.",
            ['class_id' => 99, 'url' => site_url('workspace/operator/kelas-trainer')]
        );
        $this->assert($classNotifId > 0, "Notifikasi enrollment kelas shared berhasil dibuat dengan ID #{$classNotifId}");

        // Operator dapat melihat notifikasi kelas ini
        $opClassNotifs = $service->getUserNotifications($operatorId, 20, false, ['operator']);
        $foundOpClass = false;
        foreach ($opClassNotifs as $ocn) {
            if ((int)$ocn['id'] === $classNotifId) {
                $foundOpClass = true;
                break;
            }
        }
        $this->assert($foundOpClass, "Operator dapat melihat notifikasi enrollment kelas");

        // Trainer dapat melihat notifikasi kelas ini
        $trClassNotifs = $service->getUserNotifications($operatorId, 20, false, ['trainer']);
        $foundTrClass = false;
        foreach ($trClassNotifs as $tcn) {
            if ((int)$tcn['id'] === $classNotifId) {
                $foundTrClass = true;
                break;
            }
        }
        $this->assert($foundTrClass, "Trainer dapat melihat notifikasi enrollment kelas");

        // =========================================================================
        // SCENARIO 5: Uji Kanal Helpdesk (shared:helpdesk)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 5] Uji Kanal Helpdesk (shared:helpdesk) ---", "yellow");
        $helpdeskNotifId = $service->sendShared(
            'shared:helpdesk',
            'ticket_created',
            "Tiket Bantuan Baru {$token}",
            "Pesan masuk konsultasi dari pengguna.",
            ['ticket_number' => 'TKT-TEST', 'url' => site_url('dashboard/komunikasi/pesan-masuk')]
        );
        $this->assert($helpdeskNotifId > 0, "Notifikasi helpdesk shared berhasil dibuat dengan ID #{$helpdeskNotifId}");

        // Admin & Operator dapat melihat notifikasi helpdesk ini
        $hdAdmin = $service->getUserNotifications($adminId, 20, false, ['administrator']);
        $foundHd = false;
        foreach ($hdAdmin as $hn) {
            if ((int)$hn['id'] === $helpdeskNotifId) {
                $foundHd = true;
                break;
            }
        }
        $this->assert($foundHd, "Admin melihat tiket helpdesk bersama");

        // Clean up test notifications
        $db->table('notifications')->whereIn('id', [$notifId, $classNotifId, $helpdeskNotifId])->delete();

        // =========================================================================
        // RINGKASAN
        // =========================================================================
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("  RINGKASAN UJI NOTIFIKASI BERBAGI PAKAI: {$this->passed} BERHASIL | {$this->failed} GAGAL", $this->failed > 0 ? "red" : "green");
        CLI::write("==================================================================", "cyan");

        return $this->failed > 0 ? 1 : 0;
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            CLI::write(" [PASS] {$message}", "green");
            $this->passed++;
        } else {
            CLI::error(" [FAIL] {$message}");
            $this->failed++;
        }
    }
}
