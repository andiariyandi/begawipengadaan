<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run()
    {
        $path = ROOTPATH . 'wilayah.sql';

        if (! is_file($path)) {
            throw new \RuntimeException('File wilayah.sql tidak ditemukan di root project.');
        }

        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new \RuntimeException('File wilayah.sql tidak dapat dibaca.');
        }

        $this->db->table('wilayah')->truncate();

        preg_match_all('/INSERT INTO\s+`?wilayah`?\s*\(`?kode`?\s*,\s*`?nama`?\)\s*VALUES\s*(.*?);/is', $sql, $matches);

        if ($matches[0] === []) {
            throw new \RuntimeException('Tidak ada statement INSERT wilayah yang valid.');
        }

        foreach ($matches[0] as $statement) {
            $this->db->query($statement);
        }
    }
}
