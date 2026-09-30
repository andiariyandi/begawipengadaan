<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table         = 'role_permissions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['role_id', 'permission_id'];

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->where('role_id', $roleId)->delete();

        $rows = array_map(fn ($permissionId) => [
            'role_id'       => $roleId,
            'permission_id' => $permissionId,
        ], $permissionIds);

        if ($rows) {
            $this->insertBatch($rows);
        }
    }
}
