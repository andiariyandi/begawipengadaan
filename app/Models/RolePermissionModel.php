<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table         = 'role_permissions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['role_id', 'permission_id', 'granted_by', 'created_at'];

    public function syncPermissions(int $roleId, array $permissionIds, ?int $grantedBy = null): void
    {
        $this->where('role_id', $roleId)->delete();
        $permissionIds = array_values(array_unique(array_filter(
            array_map('intval', $permissionIds),
            static fn (int $permissionId): bool => $permissionId > 0
        )));

        $rows = array_map(fn ($permissionId) => [
            'role_id'       => $roleId,
            'permission_id' => $permissionId,
            'granted_by'    => $grantedBy,
            'created_at'    => date('Y-m-d H:i:s'),
        ], $permissionIds);

        if ($rows) {
            $this->insertBatch($rows);
        }
    }
}
