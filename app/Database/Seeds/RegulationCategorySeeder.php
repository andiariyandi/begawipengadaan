<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegulationCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Undang-Undang',
            'Peraturan Presiden',
            'Peraturan LKPP',
            'Surat Edaran',
            'Pedoman Teknis',
        ];

        foreach ($categories as $name) {
            if ($this->db->table('regulation_categories')->where('name', $name)->countAllResults() > 0) {
                continue;
            }

            $this->db->table('regulation_categories')->ignore(true)->insert([
                'name'       => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
