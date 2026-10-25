<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NotificationPrune extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'notification:prune';
    protected $description = 'Pembersihan berkala notifikasi lama yang telah dibaca (Zero-Migration)';
    protected $usage       = 'notification:prune [--days <number>] [--dry-run]';
    protected $arguments   = [];
    protected $options     = [
        '--days'    => 'Batas usia notifikasi dalam hari (default: 90 hari)',
        '--dry-run' => 'Hanya simulasi tanpa menghapus data riil dari database',
    ];

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        $days = (int) ($params['days'] ?? CLI::getOption('days') ?? 90);
        if ($days < 1) {
            $days = 90;
        }

        $dryRun = CLI::getOption('dry-run') !== null;
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        CLI::write("==================================================================", 'cyan');
        CLI::write("      PEMBERSIHAN BERKALA NOTIFIKASI SISTEM BEGAWIPENGADAAN        ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::write("Batas Hari Retensi: {$days} hari");
        CLI::write("Cutoff Timestamp  : {$cutoffDate}");
        CLI::write("Mode Operasi      : " . ($dryRun ? '[SIMULASI / DRY-RUN]' : '[EKSEKUSI RIIL]'), $dryRun ? 'yellow' : 'green');
        CLI::newLine();

        // Cari notifikasi yang sudah dibaca dan berusia lebih lama dari cutoff
        $builder = $db->table('notifications')
            ->where('read_at IS NOT NULL', null, false)
            ->where('created_at <', $cutoffDate);

        $eligibleCount = (int) $builder->countAllResults(false);

        CLI::write("Ditemukan {$eligibleCount} notifikasi lama berstatus terbaca (read_at is not null).");

        if ($eligibleCount === 0) {
            CLI::write("[OK] Tidak ada notifikasi lama yang perlu dibersihkan saat ini.", 'green');
            return 0;
        }

        if ($dryRun) {
            CLI::write("[DRY-RUN] {$eligibleCount} notifikasi memenuhi syarat untuk dipangkas.", 'yellow');
            return 0;
        }

        // Hapus data yang memenuhi kriteria
        $deleted = $db->table('notifications')
            ->where('read_at IS NOT NULL', null, false)
            ->where('created_at <', $cutoffDate)
            ->delete();

        $affected = $db->affectedRows();

        CLI::write("[SUCCESS] {$affected} notifikasi lama berhasil dibersihkan dari database.", 'green');
        return 0;
    }
}
