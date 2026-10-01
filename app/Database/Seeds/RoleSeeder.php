<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Administrator', 'slug' => 'super_administrator'],
            ['name' => 'Administrator',       'slug' => 'administrator'],
            ['name' => 'Operator',            'slug' => 'operator'],
            ['name' => 'Trainer',             'slug' => 'trainer'],
            ['name' => 'Evaluator',           'slug' => 'evaluator'],
            ['name' => 'Peserta',             'slug' => 'peserta'],
            ['name' => 'Public User',         'slug' => 'public_user'],
        ];

        foreach ($roles as $role) {
            $role['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('roles')->ignore(true)->insert($role);
        }
    }
}
