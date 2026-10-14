<?php

namespace App\Commands;

use App\Services\VisitorStatisticService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VisitorStatsPrint extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'stats:print';
    protected $description = 'Print visitor statistics status';

    public function run(array $params): int
    {
        $service = new VisitorStatisticService();
        $stats   = $service->getVisitorStats();

        CLI::write("=== VISITOR STATS (HOME) ===", "yellow");
        foreach ($stats['home'] as $k => $v) {
            CLI::write(sprintf("  %-12s: %s", $k, number_format($v)), "green");
        }

        CLI::newLine();
        CLI::write("=== VISITOR STATS (FEATURES) ===", "yellow");
        foreach ($stats['features'] as $k => $f) {
            CLI::write(sprintf(
                "  %-16s: Total=%-6s Today=%-4s Month=%-6s Share=%s%%",
                $f['label'],
                number_format($f['hits_total']),
                number_format($f['hits_today']),
                number_format($f['hits_month']),
                $f['percentage']
            ), "cyan");
        }
        CLI::write("Total feature hits: " . number_format($stats['total_feature_hits']), "white");

        return 0;
    }
}
