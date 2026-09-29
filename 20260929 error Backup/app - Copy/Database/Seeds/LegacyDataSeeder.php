<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LegacyDataSeeder extends Seeder
{
    public function run()
    {
        $file = APPPATH . 'Database/Seeds/legacy_seed.sql';

        if (! is_file($file)) {
            throw new \RuntimeException('legacy_seed.sql tidak ditemukan');
        }

        $sql = file_get_contents($file);

        /*
         * Import hanya INSERT data.
         * Schema tetap dikontrol oleh migration CI4.
         */

        preg_match_all(
            '/INSERT\s+INTO\s+`?([^`\\s]+)`?.*?;/is',
            $sql,
            $matches
        );

        foreach ($matches[0] as $query) {

            // Jangan sentuh tabel sistem CI4
            if (stripos($query, 'migrations') !== false) {
                continue;
            }

            try {
                $this->db->query($query);
            } catch (\Throwable $e) {
                // lanjut tabel berikutnya agar proses seed tidak berhenti
                log_message('error', 'Legacy seed failed: '.$e->getMessage());
            }
        }
    }
}
