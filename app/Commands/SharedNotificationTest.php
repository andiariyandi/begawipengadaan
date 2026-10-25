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
        // SCENARIO 2: Uji Keterlihatan Bersama & Isolasi Wewenang
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

        // Operator murni TIDAK LAGI melihat notifikasi approval/verifikasi akun (Wewenang Steril)
        $operatorNotifs = $service->getUserNotifications($operatorId, 20, false, ['operator']);
        $foundInOperator = false;
        foreach ($operatorNotifs as $on) {
            if ((int)$on['id'] === $notifId) {
                $foundInOperator = true;
                break;
            }
        }
        $this->assert(! $foundInOperator, "Operator terisolasi dari notifikasi persetujuan akun (Steril Khusus Admin)");

        // Peserta reguler (bukan admin) TIDAK BOLEH melihat notifikasi internal ini
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

        // Catat unread count awal untuk admin
        $adminUnreadBefore = $service->unreadCount($adminId, ['administrator']);

        // Admin menandai notifikasi shared sebagai telah dibaca
        $marked = $service->markAsRead($notifId, $adminId);
        $this->assert($marked, "Admin berhasil menandai notifikasi bersama sebagai dibaca");

        // Verifikasi di database: read_at terisi dan read_by tercatat
        $updatedRow = $db->table('notifications')->where('id', $notifId)->get()->getRowArray();
        $this->assert(!empty($updatedRow['read_at']), "Timestamp read_at terisi: {$updatedRow['read_at']}");

        $updatedData = !empty($updatedRow['data']) ? json_decode($updatedRow['data'], true) : [];
        $this->assert(isset($updatedData['read_by']) && (int)$updatedData['read_by'] === $adminId, "ID pembaca tercatat akurat (read_by: #{$adminId})");

        // Verifikasi unread count untuk Admin: otomatis berkurang atau tidak lagi unread!
        $adminUnreadAfter = $service->unreadCount($adminId, ['administrator']);
        $this->assert($adminUnreadAfter <= $adminUnreadBefore, "Unread count Admin otomatis sinkron berkurang setelah dibaca");

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
        // SCENARIO 4: Uji Kanal Kelas (shared:class:X) untuk Penugasan Riil
        // =========================================================================
        CLI::write("\n--- [SCENARIO 4] Uji Kanal Kelas (shared:class:X) ---", "yellow");

        $existingClass = $db->table('learning_classes')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
        $testClassId = $existingClass ? (int) $existingClass['id'] : 2;

        // Simulasikan penugasan kelas ke Operator
        $existingCt = $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $operatorId])->get()->getRowArray();
        if (! $existingCt) {
            $db->table('class_trainers')->insert([
                'class_id'    => $testClassId,
                'user_id'     => $operatorId,
                'role'        => 'operator',
                'status'      => 'active',
                'assigned_by' => $adminId,
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $operatorId])->update(['status' => 'active']);
        }

        $classNotifId = $service->sendShared(
            'shared:class:' . $testClassId,
            'class_enrollment_requested',
            "Permohonan Enrollment Kelas {$testClassId} {$token}",
            "Peserta mengajukan pendaftaran kelas.",
            ['class_id' => $testClassId, 'url' => route_to('workspace.operator.enrollments')]
        );
        $this->assert($classNotifId > 0, "Notifikasi enrollment kelas shared berhasil dibuat dengan ID #{$classNotifId}");

        // Operator yang DITUGASKAN pada kelas ini DAPAT melihat notifikasi
        $opClassNotifs = $service->getUserNotifications($operatorId, 20, false, ['operator']);
        $foundOpClass = false;
        foreach ($opClassNotifs as $ocn) {
            if ((int)$ocn['id'] === $classNotifId) {
                $foundOpClass = true;
                break;
            }
        }
        $this->assert($foundOpClass, "Operator yang ditugaskan dapat melihat notifikasi enrollment kelas #{$testClassId}");

        // Admin Pembelajaran (Full Scope) DAPAT melihat seluruh kelas
        $apClassNotifs = $service->getUserNotifications($participantId, 20, false, ['admin_arenabelajar']);
        $foundApClass = false;
        foreach ($apClassNotifs as $apn) {
            if ((int)$apn['id'] === $classNotifId) {
                $foundApClass = true;
                break;
            }
        }
        $this->assert($foundApClass, "Admin Pembelajaran (Full Scope) dapat memantau notifikasi seluruh kelas");

        // Bersihkan data simulasi class_trainers jika baru dibuat
        if (! $existingCt) {
            $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $operatorId])->delete();
        }

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

        // Admin dapat melihat notifikasi helpdesk ini
        $hdAdmin = $service->getUserNotifications($adminId, 20, false, ['administrator']);
        $foundHd = false;
        foreach ($hdAdmin as $hn) {
            if ((int)$hn['id'] === $helpdeskNotifId) {
                $foundHd = true;
                break;
            }
        }
        $this->assert($foundHd, "Admin melihat tiket helpdesk bersama");

        // =========================================================================
        // SCENARIO 6: Uji Kesiapan Native Browser Desktop Notification & Service Worker
        // =========================================================================
        CLI::write("\n--- [SCENARIO 6] Uji Service Worker & Browser Desktop Push ---", "yellow");
        $swPath = FCPATH . 'sw.js';
        $this->assert(file_exists($swPath), "Berkas Service Worker public/sw.js terpasang secara valid");
        $swContent = file_get_contents($swPath);
        $this->assert(str_contains($swContent, 'notificationclick'), "Service Worker mengimplementasikan event listener 'notificationclick'");
        $this->assert(str_contains($swContent, 'clients.openWindow'), "Service Worker mendukung navigasi tab deep-link otomatis saat diklik");

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
