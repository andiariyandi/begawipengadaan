<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder utama — jalankan dengan: php spark db:seed DatabaseSeeder
 * Urutan penting: Role dulu, baru Permission (butuh role_id), baru Admin User (butuh role_id).
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\RoleSeeder');
        $this->call('App\Database\Seeds\PermissionSeeder');
        $this->call('App\Database\Seeds\RegulationCategorySeeder');
        $this->call('App\Database\Seeds\AdminUserSeeder');
        $this->call('App\Database\Seeds\CategorySeeder');
        $this->call('App\Database\Seeds\WilayahSeeder');
    }
}
