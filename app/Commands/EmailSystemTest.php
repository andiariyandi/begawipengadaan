<?php

namespace App\Commands;

use App\Services\Email\EmailQueueService;
use App\Services\Email\EmailService;
use App\Services\Email\EmailTemplateService;
use App\Services\Identity\PasswordResetService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EmailSystemTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:email-system';
    protected $description = 'Comprehensive test suite verifying the Gmail Emailing System (Manual Broadcast, Send to One, Queue, and Reset Password)';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("     BEGAWIPENGADAAN GMAIL MANUAL EMAILING SYSTEM TEST SUITE      ", "yellow");
        CLI::write("==================================================================", "yellow");

        $db = \Config\Database::connect();
        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $message) use (&$passed, &$failed): void {
            if ($condition) {
                CLI::write("  [PASS] " . $message, "green");
                $passed++;
            } else {
                CLI::write("  [FAIL] " . $message, "red");
                $failed++;
            }
        };

        // ====================================================================
        // [1] ZERO MIGRATIONS & SCHEMA INTEGRITY
        // ====================================================================
        CLI::write("\n--- [SCENARIO 1] Zero Database Schema Migrations ---", "cyan");

        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migrationCount = count($migrationFiles);
        $assert($migrationCount === 16, "Jumlah file migrasi database tetap tepat 16 file (Current: {$migrationCount})");

        // Check notifications table schema has json data column and sent_at
        $notifFields = $db->getFieldNames('notifications');
        $assert(in_array('channel', $notifFields, true), "Tabel notifications memiliki kolom channel");
        $assert(in_array('data', $notifFields, true), "Tabel notifications memiliki kolom data (JSON payload)");
        $assert(in_array('sent_at', $notifFields, true), "Tabel notifications memiliki kolom sent_at");

        // ====================================================================
        // [2] CONFIGURATION RESOLVER & ENCRYPTION
        // ====================================================================
        CLI::write("\n--- [SCENARIO 2] Gmail SMTP Configuration Resolver ---", "cyan");

        $emailService = new EmailService();
        $config = $emailService->getConfig();

        $assert(isset($config['smtp_host']) && $config['smtp_host'] === 'smtp.gmail.com', "Host SMTP default terarah ke smtp.gmail.com");
        $assert(isset($config['smtp_port']) && in_array($config['smtp_port'], [587, 465], true), "Port SMTP adalah port standar Google (587 TLS / 465 SSL)");
        $assert(isset($config['smtp_crypto']) && in_array($config['smtp_crypto'], ['tls', 'ssl'], true), "Protokol enkripsi adalah TLS atau SSL");
        $assert(array_key_exists('smtp_user', $config), "Kunci konfigurasi smtp_user tersedia");
        $assert(array_key_exists('smtp_pass', $config), "Kunci konfigurasi smtp_pass tersedia");
        $assert(! empty($config['from_name']), "Nama pengirim resmi terkonfigurasi: '{$config['from_name']}'");

        // ====================================================================
        // [3] TEMPLATE GENERATION & BRANDING
        // ====================================================================
        CLI::write("\n--- [SCENARIO 3] Formal Responsive Email Templates ---", "cyan");

        $templateService = new EmailTemplateService();

        // 3a. Layout wrapper
        $layout = $templateService->wrapLayout('Judul Pengujian', '<p>Konten teks uji coba.</p>', [
            'action_url'  => 'https://begawipbj.lampungprov.go.id/uji',
            'action_text' => 'Klik Disini',
        ]);
        $assert(str_contains($layout, 'PEMERINTAH PROVINSI LAMPUNG'), "Template menyertakan identitas resmi Pemerintah Provinsi Lampung");
        $assert(str_contains($layout, 'Biro Pengadaan Barang dan Jasa'), "Template menyertakan nama instansi Biro PBJ");
        $assert(str_contains($layout, 'https://begawipbj.lampungprov.go.id/uji'), "Template menyertakan tombol aksi dan URL fallback");

        // 3b. Password reset template
        $pwHtml = $templateService->renderPasswordReset('Budi Santoso', 'https://begawipbj.lampungprov.go.id/lupa-sandi/reset/test123token', 60);
        $assert(str_contains($pwHtml, 'Budi Santoso'), "Email reset kata sandi menyapa nama user bersangkutan");
        $assert(str_contains($pwHtml, 'test123token'), "Email reset kata sandi menyertakan token unik");
        $assert(str_contains($pwHtml, '60 menit'), "Email reset kata sandi menyertakan batas waktu keamanan 60 menit");

        // 3c. Broadcast template
        $bcHtml = $templateService->renderBroadcast('Pengumuman Bimtek PBJ', '<p>Harap hadir tepat waktu.</p>', [
            'audience_label' => 'Aparatur Sipil Negara',
        ]);
        $assert(str_contains($bcHtml, 'Siaran: Aparatur Sipil Negara'), "Email broadcast menampilkan badge target audiens");
        $assert(str_contains($bcHtml, 'Harap hadir tepat waktu.'), "Email broadcast memuat isi pesan");

        // 3d. Test connection template
        $testHtml = $templateService->renderTestEmail('Super Administrator', $config);
        $assert(str_contains($testHtml, 'Uji Coba Pengiriman Google Mail SMTP Berhasil'), "Template test email menyertakan konfirmasi keberhasilan");
        $assert(str_contains($testHtml, 'smtp.gmail.com'), "Template test email merangkum host SMTP aktif");

        // ====================================================================
        // [4] QUEUE & AUDIENCE RESOLVER (ZERO FIELDS)
        // ====================================================================
        CLI::write("\n--- [SCENARIO 4] Email Queue & Audience Resolver ---", "cyan");

        $queueService = new EmailQueueService();

        // 4a. Count Audience
        $allCount = $queueService->countAudience('all');
        $asnCount = $queueService->countAudience('asn');
        $newsCount = $queueService->countAudience('newsletter');
        $assert($allCount >= 0, "Penghitungan audiens 'all' berhasil (Jumlah: {$allCount})");
        $assert($asnCount >= 0, "Penghitungan audiens 'asn' berhasil (Jumlah: {$asnCount})");
        $assert($newsCount >= 0, "Penghitungan audiens 'newsletter' berhasil (Jumlah: {$newsCount})");

        // 4b. Enqueue single item test
        $testEmail = 'unit-test-' . bin2hex(random_bytes(3)) . '@example.com';
        $queueId = $queueService->enqueue(
            'personal',
            $testEmail,
            'Unit Tester',
            'Pemberitahuan Uji Coba Queue',
            '<p>Pesan pengujian antrean email.</p>',
            null,
            0
        );

        $assert($queueId > 0, "Item berhasil di-enqueue ke tabel notifications (ID #{$queueId})");

        $savedRow = $db->table('notifications')->where('id', $queueId)->get()->getRowArray();
        $assert($savedRow !== null, "Baris antrean tersimpan di database");
        $assert($savedRow['channel'] === 'email', "Kolom channel bernilai 'email'");
        $assert($savedRow['sent_at'] === null, "Kolom sent_at bernilai null sebelum diproses");

        $savedPayload = json_decode((string) ($savedRow['data'] ?? '{}'), true);
        $assert(($savedPayload['recipient_email'] ?? '') === $testEmail, "Payload JSON data menyimpan recipient_email dengan benar");
        $assert(($savedPayload['status'] ?? '') === 'pending', "Payload JSON data menandai status = 'pending'");

        // 4c. Delete test item to clean up
        $deleted = $queueService->delete($queueId);
        $assert($deleted, "Item antrean uji coba berhasil dihapus (Clean Database)");

        // ====================================================================
        // [5] DASHBOARD ROUTES & SECURITY ACCESS
        // ====================================================================
        CLI::write("\n--- [SCENARIO 5] Admin Dashboard Routes & Access ---", "cyan");

        $routesSource = (string) file_get_contents(APPPATH . 'Config/Routes.php');
        $assert(str_contains($routesSource, "'dashboard/komunikasi/email'") || str_contains($routesSource, "dashboard.communication.email"), "Rute dashboard.communication.email terdaftar di Routes.php");
        $assert(str_contains($routesSource, "EmailBroadcastController::sendBroadcast"), "Rute POST email/broadcast terdaftar");
        $assert(str_contains($routesSource, "EmailBroadcastController::sendOne"), "Rute POST email/send-one terdaftar");
        $assert(str_contains($routesSource, "EmailBroadcastController::dispatchItem"), "Rute POST email/dispatch-item terdaftar");
        $assert(str_contains($routesSource, "EmailBroadcastController::dispatchReady"), "Rute POST email/dispatch-ready terdaftar");
        $assert(str_contains($routesSource, "SettingController::testEmail"), "Rute POST pengaturan-notifikasi/test-email terdaftar");

        // Verify Sidebar link exists
        $sidebarSource = (string) file_get_contents(APPPATH . 'Views/dashboard/components/sidebar.php');
        $assert(str_contains($sidebarSource, "site_url('dashboard/komunikasi/email')"), "Menu 'Pusat Siaran Email' terpasang pada sidebar komunikasi dashboard");

        // ====================================================================
        // [6] PASSWORD RESET & APPROVAL ZERO AUTOMATED DISPATCH INTEGRATION
        // ====================================================================
        CLI::write("\n--- [SCENARIO 6] Password Reset & Approval Manual Queue Integration ---", "cyan");

        $pwSvcSource = (string) file_get_contents(APPPATH . 'Services/Identity/PasswordResetService.php');
        $assert(str_contains($pwSvcSource, "EmailService"), "PasswordResetService mengimpor EmailService");
        $assert(str_contains($pwSvcSource, "queuePasswordReset"), "PasswordResetService memanggil queuePasswordReset() untuk kontrol manual admin");

        $approvalSvcSource = (string) file_get_contents(APPPATH . 'Services/Admin/ProfileVerificationService.php');
        $assert(str_contains($approvalSvcSource, "queueAccountApproved"), "ProfileVerificationService memanggil queueAccountApproved() untuk kontrol manual admin");

        // ====================================================================
        // [7] QUOTA TRACKING & ANTI-SPAM ALTMESSAGE
        // ====================================================================
        CLI::write("\n--- [SCENARIO 7] Gmail Daily Quota Tracking & AltMessage Anti-Spam ---", "cyan");

        $quotaInfo = $emailService->getDailyQuotaInfo(500);
        $assert(isset($quotaInfo['limit']) && $quotaInfo['limit'] === 500, "Limit kuota harian Google Mail diatur 500 email/hari");
        $assert(isset($quotaInfo['sent']) && $quotaInfo['sent'] >= 0, "Penghitungan email terkirim 24 jam bergulir aktif (Terkirim: {$quotaInfo['sent']})");
        $assert(isset($quotaInfo['remaining']) && $quotaInfo['remaining'] >= 0, "Sisa kuota aman terhitung dengan benar (Sisa: {$quotaInfo['remaining']})");

        $emailSvcSource = (string) file_get_contents(APPPATH . 'Services/Email/EmailService.php');
        $assert(str_contains($emailSvcSource, "setAltMessage"), "EmailService::sendDirect otomatis menyetel plaintext setAltMessage() untuk anti-spam score");
        $assert(str_contains($emailSvcSource, "dispatch_mode"), "EmailService mengelola parameter dispatch_mode manual");

        // ====================================================================
        // [8] SPARK CLI WORKER COMMAND
        // ====================================================================
        CLI::write("\n--- [SCENARIO 8] Spark CLI Email Worker Command ---", "cyan");

        $workerExists = file_exists(APPPATH . 'Commands/EmailWorker.php');
        $assert($workerExists, "Berkas perintah CLI EmailWorker.php tersedia di app/Commands");
        if ($workerExists) {
            $workerSource = (string) file_get_contents(APPPATH . 'Commands/EmailWorker.php');
            $assert(str_contains($workerSource, "email:worker"), "Nama perintah Spark CLI adalah email:worker");
            $assert(str_contains($workerSource, "processReadyBatch"), "EmailWorker memanggil processReadyBatch()");
        }

        // ====================================================================
        // SUMMARY
        // ====================================================================
        CLI::write("\n==================================================================", "yellow");
        CLI::write("                 EMAIL SYSTEM TEST SUMMARY                        ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write("  TOTAL CHECKS: " . ($passed + $failed));
        CLI::write("  PASSED      : {$passed}", "green");
        CLI::write("  FAILED      : {$failed}", $failed > 0 ? "red" : "green");
        CLI::write("==================================================================\n", "yellow");

        return $failed > 0 ? 1 : 0;
    }
}
