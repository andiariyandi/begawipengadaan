<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTimezone extends BaseCommand
{
    protected $group       = 'Diagnostic';
    protected $name        = 'check:timezone';
    protected $description = 'Displays current PHP and MySQL timezone configuration';

    public function run(array $params): int
    {
        $appConfig = config('App');
        $db = \Config\Database::connect();
        $row = $db->query('SELECT @@global.time_zone as gz, @@session.time_zone as sz, NOW() as now_val, UTC_TIMESTAMP() as utc_val')->getRowArray();

        CLI::write("=== TIMEZONE DIAGNOSTIC ===", "cyan");
        CLI::write("AppConfig appTimezone:         " . $appConfig->appTimezone);
        CLI::write("PHP date_default_timezone_get: " . date_default_timezone_get());
        CLI::write("PHP date('Y-m-d H:i:s'):       " . date('Y-m-d H:i:s'));
        CLI::write("MySQL @@global.time_zone:      " . $row['gz']);
        CLI::write("MySQL @@session.time_zone:     " . $row['sz']);
        CLI::write("MySQL NOW():                   " . $row['now_val']);
        CLI::write("MySQL UTC_TIMESTAMP():         " . $row['utc_val']);
        CLI::write("Expected Local Time (WIB):     " . (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s'));

        return 0;
    }
}
