<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AuditPrune extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'audit:prune';
    protected $description = 'Pembersihan berkala riwayat jejak audit (audit logs) lama (Zero-Migration)';
    protected $usage       = 'audit:prune [--days <number>] [--dry-run]';
    protected $arguments   = [];
    protected $options     = [
        '--days'    => 'Batas usia jejak audit dalam hari (default: 180 hari)',
        '--dry-run' => 'Hanya simulasi tanpa menghapus data riil dari database',
    ];

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        $days = (int) ($params['days'] ?? CLI::getOption('days') ?? 180);
        if ($days < 1) {
            $days = 180;
        }

        $dryRun = CLI::getOption('dry-run') !== null;
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        CLI::write("==================================================================", 'cyan');
        CLI::write("      PEMBERSIHAN BERKALA JEJAK AUDIT (AUDIT LOGS) BEGAWI        ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::write("Batas Hari Retensi: {$days} hari");
        CLI::write("Cutoff Timestamp  : {$cutoffDate}");
        CLI::write("Mode Operasi      : " . ($dryRun ? '[SIMULASI / DRY-RUN]' : '[EKSEKUSI RIIL]'), $dryRun ? 'yellow' : 'green');
        CLI::newLine();

        $builder = $db->table('audit_logs')
            ->where('created_at <', $cutoffDate);

        $eligibleCount = (int) $builder->countAllResults(false);

        CLI::write("Ditemukan {$eligibleCount} baris log audit yang berusia lebih lama dari cutoff.");

        if ($eligibleCount === 0) {
            CLI::write("[OK] Tidak ada baris log audit lama yang perlu dibersihkan saat ini.", 'green');
            return 0;
        }

        if ($dryRun) {
            CLI::write("[DRY-RUN] {$eligibleCount} baris log audit memenuhi syarat untuk dipangkas.", 'yellow');
            return 0;
        }

        $db->table('audit_logs')
            ->where('created_at <', $cutoffDate)
            ->delete();

        $affected = $db->affectedRows();

        CLI::write("[SUCCESS] {$affected} baris log audit lama berhasil dibersihkan dari database.", 'green');
        return 0;
    }
}
