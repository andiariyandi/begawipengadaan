<?php

namespace App\Commands;

use App\Services\Email\EmailQueueService;
use App\Services\Email\EmailService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * CLI Command for Controlled Manual Email Dispatch.
 * Complies with 100% Zero Automated Background Daemon policy.
 */
class EmailWorker extends BaseCommand
{
    protected $group       = 'Communication';
    protected $name        = 'email:worker';
    protected $description = 'Pemrosesan manual antrean email BegawiPengadaan via CLI (Controlled Manual Dispatch, Quota-Safe)';
    protected $usage       = 'email:worker [--limit <number>] [--dry-run]';
    protected $arguments   = [];
    protected $options     = [
        '--limit'   => 'Batas maksimal pengiriman email dalam batch ini (default: 25, max: 100)',
        '--dry-run' => 'Cek daftar antrean yang siap kirim tanpa melakukan pengiriman riil',
    ];

    public function run(array $params): int
    {
        $queueService = new EmailQueueService();
        $emailService = new EmailService();

        $limit = (int) ($params['limit'] ?? CLI::getOption('limit') ?? 25);
        if ($limit < 1) {
            $limit = 25;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $dryRun = CLI::getOption('dry-run') !== null;

        CLI::write("==================================================================", 'cyan');
        CLI::write("   KONTROL MANUAL PENGIRIMAN ANTREAN EMAIL BEGAWIPENGADAAN        ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $quota = $emailService->getDailyQuotaInfo(500);
        CLI::write("Batas Kuota Google Mail : 500 email / 24 jam");
        CLI::write("Terkirim 24 Jam Terakhir: {$quota['sent']} email");
        CLI::write("Sisa Kuota Aman         : {$quota['remaining']} email", $quota['is_safe'] ? 'green' : 'yellow');
        CLI::write("Batas Batch Saat Ini    : {$limit} email");
        CLI::write("Mode Eksekusi           : " . ($dryRun ? '[SIMULASI / DRY-RUN]' : '[EKSEKUSI MANUAL RIIL]'), $dryRun ? 'yellow' : 'green');
        CLI::newLine();

        if ($quota['remaining'] <= 0) {
            CLI::write("[STOP] Kuota pengiriman harian Google Mail (500 email) telah habis. Eksekusi dibatalkan demi keamanan akun.", 'red');
            return 1;
        }

        $queue = $queueService->getQueue(100);
        $readyItems = array_filter($queue, fn($i) => empty($i['sent_at']) && $i['is_due']);
        $readyCount = count($readyItems);

        CLI::write("Ditemukan {$readyCount} email yang berstatus siap kirim / telah jatuh tempo.");

        if ($readyCount === 0) {
            CLI::write("[OK] Tidak ada antrean email yang siap kirim saat ini.", 'green');
            return 0;
        }

        if ($dryRun) {
            CLI::write("[DRY-RUN] Pratinjau 5 antrean teratas yang akan diproses:");
            $sample = array_slice($readyItems, 0, 5);
            foreach ($sample as $item) {
                CLI::write(" - ID #{$item['id']}: {$item['recipient_email']} | {$item['title']}");
            }
            return 0;
        }

        CLI::write("Memulai pemrosesan batch (maksimal {$limit} email, delay 0.2s)...");
        $result = $queueService->processReadyBatch($limit);

        CLI::newLine();
        CLI::write("Hasil Eksekusi:", 'cyan');
        CLI::write(" - Total Diproses: {$result['processed']}");
        CLI::write(" - Berhasil      : {$result['success']}", 'green');
        CLI::write(" - Gagal         : {$result['failed']}", $result['failed'] > 0 ? 'red' : 'white');

        if (! empty($result['errors'])) {
            CLI::write("Catatan Kendala:", 'yellow');
            foreach (array_slice($result['errors'], 0, 3) as $err) {
                CLI::write(" * {$err}", 'red');
            }
        }

        $newQuota = $emailService->getDailyQuotaInfo(500);
        CLI::newLine();
        CLI::write("[SELESAI] Sisa kuota aman Google Mail saat ini: {$newQuota['remaining']} email.", 'green');

        return $result['failed'] > 0 ? 1 : 0;
    }
}
