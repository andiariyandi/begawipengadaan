<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $password = env('seed.admin_password');

        if (ENVIRONMENT === 'production' && (! is_string($password) || strlen($password) < 12)) {
            throw new \RuntimeException('seed.admin_password wajib diisi dengan password kuat pada production.');
        }

        if (! is_string($password) || $password === '') {
            $password = 'ChangeMe123!';
        }

        $existingUser = $this->db->table('users')
            ->select('id')
            ->where('email', 'superadmin@begawipbj.go.id')
            ->get()
            ->getRowArray();

        if ($existingUser) {
            $userId = (int) $existingUser['id'];
        } else {
            $this->db->table('users')->insert([
            'username'      => 'superadmin',
            'email'         => 'superadmin@begawipbj.go.id',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'user_type'     => 'asn',
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            ]);

            $userId = $this->db->insertID();
        }
        $roleId = $this->db->table('roles')->select('id')->where('slug', 'super_administrator')->get()->getRow('id');

        if (! $roleId) {
            throw new \RuntimeException('Required role "super_administrator" was not found.');
        }

        $this->db->table('user_roles')->ignore(true)->insert([
            'user_id'    => $userId,
            'role_id'    => $roleId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
