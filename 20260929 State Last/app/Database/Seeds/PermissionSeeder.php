<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'user.verify', 'module' => 'user'],
            ['name' => 'user.manage', 'module' => 'user'],
            ['name' => 'role.manage', 'module' => 'rbac'],
            ['name' => 'audit.view', 'module' => 'audit'],
            ['name' => 'dashboard.view', 'module' => 'dashboard'],
            ['name' => 'lms.manage', 'module' => 'lms'],
            ['name' => 'enrollment.verify', 'module' => 'lms'],
            ['name' => 'kms.manage', 'module' => 'kms'],
            ['name' => 'regulation.manage', 'module' => 'regulation'],
            ['name' => 'certificate.manage', 'module' => 'certificate'],
            ['name' => 'certificate.approve', 'module' => 'certificate'],
            ['name' => 'assessment.evaluate', 'module' => 'lms'],
        ];

        foreach ($permissions as $permission) {
            $this->db->table('permissions')->ignore(true)->insert($permission);
        }

        $mapping = [
            'super_administrator' => array_column($permissions, 'name'),
            'administrator'       => ['dashboard.view', 'user.manage', 'user.verify', 'role.manage', 'lms.manage', 'enrollment.verify', 'kms.manage', 'regulation.manage', 'certificate.manage', 'certificate.approve', 'assessment.evaluate'],
            'operator'            => ['dashboard.view', 'user.manage', 'user.verify', 'enrollment.verify', 'regulation.manage'],
            'trainer'             => ['dashboard.view', 'lms.manage', 'kms.manage'],
            'evaluator'           => ['dashboard.view', 'certificate.manage', 'certificate.approve', 'assessment.evaluate'],
        ];

        foreach ($mapping as $roleSlug => $permissionNames) {
            $roleId = $this->db->table('roles')->select('id')->where('slug', $roleSlug)->get()->getRow('id');

            if (! $roleId) {
                continue;
            }

            foreach ($permissionNames as $permissionName) {
                $permissionId = $this->db->table('permissions')->select('id')->where('name', $permissionName)->get()->getRow('id');

                if ($permissionId) {
                    $this->db->table('role_permissions')->ignore(true)->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }
}
