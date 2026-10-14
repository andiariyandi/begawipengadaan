<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTimezone extends BaseCommand
{
    protected $group       = 'Diagnostic';
    protected $name        = 'check:timezone';
    protected $description = 'Displays current PHP and MySQL timezone configuration and validates alignment';

    public function run(array $params): int
    {
        $appConfig = config('App');
        $db = \Config\Database::connect();
        $db->query("SET time_zone = '+07:00'");

        $row = $db->query('SELECT @@global.time_zone as gz, @@session.time_zone as sz, NOW() as now_val, UTC_TIMESTAMP() as utc_val')->getRowArray();
        $phpTz = date_default_timezone_get();
        $phpNow = date('Y-m-d H:i:s');
        $expectedWib = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPENGADAAN TIMEZONE & DATABASE ALIGNMENT DIAGNOSTIC       ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("AppConfig appTimezone:         " . $appConfig->appTimezone);
        CLI::write("PHP date_default_timezone_get: " . $phpTz);
        CLI::write("PHP date('Y-m-d H:i:s'):       " . $phpNow);
        CLI::write("MySQL @@global.time_zone:      " . $row['gz']);
        CLI::write("MySQL @@session.time_zone:     " . $row['sz']);
        CLI::write("MySQL NOW():                   " . $row['now_val']);
        CLI::write("MySQL UTC_TIMESTAMP():         " . $row['utc_val']);
        CLI::write("Expected Local Time (WIB):     " . $expectedWib);
        CLI::newLine();

        $diff = abs(strtotime($phpNow) - strtotime($row['now_val']));
        if ($phpTz === 'Asia/Jakarta' && $diff <= 1) {
            CLI::write("[SUCCESS] PHP Timezone and MySQL NOW() are perfectly synchronized in GMT+7 (WIB)!", "green");
            return 0;
        }

        CLI::write("[ERROR] Timezone mismatch detected between PHP and MySQL!", "red");
        return 1;
    }
}
