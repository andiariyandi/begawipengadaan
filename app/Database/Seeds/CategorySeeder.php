<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Pembelajaran PBJ', 'type' => 'kms'],
            ['name' => 'Regulasi Pengadaan', 'type' => 'regulation'],
            ['name' => 'Studi Kasus', 'type' => 'kms'],
            ['name' => 'Panduan Teknis', 'type' => 'kms'],
            ['name' => 'Template Dokumen', 'type' => 'kms'],
        ];

        foreach ($categories as $category) {
            $exists = $this->db->table('categories')
                ->where('name', $category['name'])
                ->where('type', $category['type'])
                ->countAllResults();

            if ($exists > 0) {
                continue;
            }

            $category['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->ignore(true)->insert($category);
        }
    }
}
