<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class PermissionService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function can(int $userId, string $permission): bool
    {
        return $this->resolve($userId, $permission) !== null;
    }

    public function resolve(int $userId, string $permission): ?array
    {
        return $this->db->table('permissions')
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.status', 'active')
            ->groupStart()
                ->where('permissions.key', $permission)
                ->orWhere('permissions.name', $permission)
            ->groupEnd()
            ->get()
            ->getFirstRow('array') ?: null;
    }

    public function resolveFromActiveRoles(int $userId): array
    {
        return $this->db->table('permissions')
            ->distinct()
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.status', 'active')
            ->orderBy('permissions.domain', 'ASC')
            ->orderBy('permissions.key', 'ASC')
            ->get()
            ->getResultArray();
    }
}
