<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name'=>'super_admin'],
            ['name'=>'admin'],
            ['name'=>'operator'],
            ['name'=>'trainer'],
            ['name'=>'evaluator'],
            ['name'=>'peserta'],
            ['name'=>'public_user'],
        ];

        // Map to existing role table structure before production import.
    }
}
