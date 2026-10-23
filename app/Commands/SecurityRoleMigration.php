<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SecurityRoleMigration extends BaseCommand
{
    protected $group       = 'Security';
    protected $name        = 'security:clean-roles';
    protected $description = 'Clean up duplicate roles/permissions and migrate user_roles to canonical taxonomy';

    public function run(array $params): int
    {
        $db = \Config\Database::connect();

        CLI::write("==================================================================", "yellow");
        CLI::write("   CANONICAL ROLE STANDARDIZATION & DATABASE CLEANUP             ", "yellow");
        CLI::write("==================================================================", "yellow");

        // 1. Inspect existing roles
        $roles = $db->table('roles')->get()->getResultArray();
        CLI::write("Existing Roles in DB:", "cyan");
        foreach ($roles as $r) {
            CLI::write(" - ID: {$r['id']} | Key: '{$r['key']}' | Name: '{$r['name']}'");
        }

        // 2. Inspect invalid permissions
        $badPerms = $db->table('permissions')
            ->where('key', '')
            ->orWhere('domain', 'test')
            ->orWhereIn('id', [42, 43])
            ->get()->getResultArray();
        CLI::write("Target invalid permissions: " . count($badPerms), "cyan");
        foreach ($badPerms as $bp) {
            CLI::write(" - ID: {$bp['id']} | Key: '{$bp['key']}' | Name: '{$bp['name']}' | Domain: '{$bp['domain']}'");
        }

        // 3. User roles migration: 20 -> 10, 21 -> 11, 22 -> 15
        $db->transStart();

        $roleMappings = [
            20 => 10, // superadmin -> super_administrator
            21 => 11, // admin -> administrator
            22 => 15, // user -> peserta
        ];

        foreach ($roleMappings as $oldId => $newId) {
            // Check rows in user_roles with oldId
            $oldUserRoles = $db->table('user_roles')->where('role_id', $oldId)->get()->getResultArray();
            CLI::write("Found " . count($oldUserRoles) . " user_roles with role_id {$oldId} (mapping to {$newId})", "cyan");

            foreach ($oldUserRoles as $ur) {
                // Check if user already has newId
                $hasNew = $db->table('user_roles')
                    ->where('user_id', $ur['user_id'])
                    ->where('role_id', $newId)
                    ->countAllResults();

                if ($hasNew > 0) {
                    // Just delete the old duplicate mapping
                    $db->table('user_roles')
                        ->where('user_id', $ur['user_id'])
                        ->where('role_id', $oldId)
                        ->delete();
                    CLI::write("  [DEDUP] Deleted duplicate old role {$oldId} for user {$ur['user_id']}", "green");
                } else {
                    // Update oldId to newId
                    $db->table('user_roles')
                        ->where('user_id', $ur['user_id'])
                        ->where('role_id', $oldId)
                        ->update(['role_id' => $newId]);
                    CLI::write("  [MIGRATED] User {$ur['user_id']} role {$oldId} -> {$newId}", "green");
                }
            }
        }

        // 4. Delete role_permissions for old roles (20, 21, 22) and invalid permissions (42, 43)
        $badPermIds = array_column($badPerms, 'id');
        if (empty($badPermIds)) {
            $badPermIds = [42, 43];
        }

        $deletedRp1 = $db->table('role_permissions')->whereIn('role_id', [20, 21, 22])->delete();
        $deletedRp2 = $db->table('role_permissions')->whereIn('permission_id', $badPermIds)->delete();
        CLI::write("Deleted role_permissions associations for old roles and invalid perms", "green");

        // 5. Delete invalid permissions
        $db->table('permissions')->whereIn('id', $badPermIds)->delete();
        $db->table('permissions')->where('key', '')->orWhere('domain', 'test')->delete();
        CLI::write("Deleted invalid permissions from permissions table", "green");

        // 6. Delete old roles (20, 21, 22 or key superadmin, admin, user)
        $db->table('roles')->whereIn('id', [20, 21, 22])->delete();
        $db->table('roles')->whereIn('key', ['superadmin', 'admin', 'user'])->delete();
        CLI::write("Deleted legacy roles (superadmin, admin, user) from roles table", "green");

        $db->transComplete();

        if ($db->transStatus() === false) {
            CLI::write("[FAILED] Database transaction rolled back!", "red");
            return 1;
        }

        CLI::write("[SUCCESS] Database role cleanup & canonical migration completed!", "green");

        // Verify final roles
        $finalRoles = $db->table('roles')->get()->getResultArray();
        CLI::write("\nFinal Canonical Roles:", "yellow");
        foreach ($finalRoles as $r) {
            CLI::write(" - ID: {$r['id']} | Key: '{$r['key']}' | Name: '{$r['name']}'");
        }

        return 0;
    }
}
