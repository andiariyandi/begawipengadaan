<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LegacyRestoreSeeder extends Seeder
{
    public function run()
    {
        $file = APPPATH . 'Database/Seeds/legacy_seed.sql';

        if (! is_file($file)) {
            throw new \RuntimeException('legacy_seed.sql tidak ditemukan');
        }

        $sql = file_get_contents($file);

        // Jangan pernah menyentuh schema dan migration CI4.
        $sql = preg_replace('/DROP\s+TABLE.*?;/is', '', $sql);
        $sql = preg_replace('/CREATE\s+TABLE.*?;/is', '', $sql);
        $sql = preg_replace('/CREATE\s+DATABASE.*?;/is', '', $sql);
        $sql = preg_replace('/USE\s+.*?;/is', '', $sql);

        // Ambil INSERT saja.
        preg_match_all('/INSERT\s+INTO.*?;/is', $sql, $queries);

        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($queries[0] as $query) {

            // Lindungi tabel migration CI4
            if (stripos($query, 'migrations') !== false) {
                continue;
            }

            try {
                $this->db->query($query);
            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'Legacy import failed: ' . $e->getMessage()
                );
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
