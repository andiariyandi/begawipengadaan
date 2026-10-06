<?php

namespace App\Services\Admin;

use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\RolePermissionModel;
use App\Services\AuditService;

/**
 * Role + Permission management service.
 *
 * Used by Admin/RoleController and Admin/PermissionController and the
 * role-permission matrix page.
 *
 * Writes audit_logs for every mutation. System roles (is_system=1) cannot
 * be deleted (CLAUDE.md §17); the controller should hide the delete control.
 */
class RoleManagementService
{
    public function __construct(
        private readonly RoleModel $roles = new RoleModel(),
        private readonly PermissionModel $permissions = new PermissionModel(),
        private readonly RolePermissionModel $rolePermissions = new RolePermissionModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    public function listRoles(): array
    {
        return db_connect()->table('roles r')
            ->select('r.*, COUNT(rp.permission_id) AS permission_count')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->groupBy('r.id')
            ->orderBy('r.name')
            ->get()
            ->getResultArray();
    }

    public function findRole(int $id): ?array
    {
        return $this->roles->find($id);
    }

    public function permissionsForRole(int $roleId): array
    {
        return db_connect()->table('permissions p')
            ->select('p.id, p.key, p.name, p.domain')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->orderBy('p.domain, p.key')
            ->get()
            ->getResultArray();
    }

    public function createRole(array $data, ?int $actorId = null): int
    {
        $payload = [
            'key'         => trim((string) ($data['key'] ?? '')),
            'name'        => trim((string) ($data['name'] ?? '')),
            'description' => $data['description'] ?? null,
            'is_system'   => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $id = (int) $this->roles->insert($payload, true);
        $this->audit->log($actorId, 'role.create', 'roles', 'role', $id);
        return $id;
    }

    public function updateRole(int $id, array $data, ?int $actorId = null): bool
    {
        $payload = array_filter([
            'name'        => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ], static fn($v) => $v !== null);

        $ok = (bool) $this->roles->update($id, $payload);
        if ($ok) {
            $this->audit->log($actorId, 'role.update', 'roles', 'role', $id);
        }
        return $ok;
    }

    public function deleteRole(int $id, ?int $actorId = null): bool
    {
        $role = $this->roles->find($id);
        if (! $role) {
            return false;
        }
        if ((int) ($role['is_system'] ?? 0) === 1) {
            throw new \RuntimeException('Role sistem tidak dapat dihapus.');
        }

        $this->rolePermissions->where('role_id', $id)->delete();
        $ok = (bool) $this->roles->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'role.delete', 'roles', 'role', $id);
        }
        return $ok;
    }

    public function listPermissions(): array
    {
        return $this->permissions->orderBy('domain, key')->findAll();
    }

    public function findPermission(int $id): ?array
    {
        return $this->permissions->find($id);
    }

    public function createPermission(array $data, ?int $actorId = null): int
    {
        $payload = [
            'key'         => trim((string) ($data['key'] ?? '')),
            'name'        => trim((string) ($data['name'] ?? '')),
            'domain'      => trim((string) ($data['domain'] ?? '')),
            'description' => $data['description'] ?? null,
            'is_system'   => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $id = (int) $this->permissions->insert($payload, true);
        $this->audit->log($actorId, 'permission.create', 'permissions', 'permission', $id);
        return $id;
    }

    public function updatePermission(int $id, array $data, ?int $actorId = null): bool
    {
        $payload = array_filter([
            'name'        => $data['name'] ?? null,
            'domain'      => $data['domain'] ?? null,
            'description' => $data['description'] ?? null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ], static fn($v) => $v !== null);

        $ok = (bool) $this->permissions->update($id, $payload);
        if ($ok) {
            $this->audit->log($actorId, 'permission.update', 'permissions', 'permission', $id);
        }
        return $ok;
    }

    public function deletePermission(int $id, ?int $actorId = null): bool
    {
        $permission = $this->permissions->find($id);
        if (! $permission) {
            return false;
        }
        if ((int) ($permission['is_system'] ?? 0) === 1) {
            throw new \RuntimeException('Permission sistem tidak dapat dihapus.');
        }

        $this->rolePermissions->where('permission_id', $id)->delete();
        $ok = (bool) $this->permissions->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'permission.delete', 'permissions', 'permission', $id);
        }
        return $ok;
    }

    /**
     * Replace the full permission set for a role (sync).
     */
    public function syncRolePermissions(int $roleId, array $permissionIds, ?int $actorId = null): void
    {
        $db = db_connect();
        $db->transStart();

        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        $rows = [];
        foreach ($permissionIds as $pid) {
            $pid = (int) $pid;
            if ($pid <= 0) {
                continue;
            }
            $rows[] = [
                'role_id'       => $roleId,
                'permission_id' => $pid,
                'granted_by'    => $actorId,
                'created_at'    => date('Y-m-d H:i:s'),
            ];
        }
        if ($rows !== []) {
            $db->table('role_permissions')->insertBatch($rows);
        }

        $db->transComplete();

        $this->audit->log($actorId, 'role.permission.sync', 'role_permissions', 'role', $roleId);
    }
}