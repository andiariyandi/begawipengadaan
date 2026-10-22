<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table         = 'user_roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id',
        'role_id',
        'status',
        'granted_by',
        'granted_at',
        'revoked_by',
        'revoked_at',
        'reason',
        'created_at',
        'updated_at',
    ];

    public function assignRole(int $userId, int $roleId): void
    {
        if ($this->where(['user_id' => $userId, 'role_id' => $roleId])->first()) {
            return;
        }

        $this->insert(['user_id' => $userId, 'role_id' => $roleId, 'created_at' => date('Y-m-d H:i:s')]);
    }

    public function replaceRole(int $userId, int $roleId): void
    {
        $this->where('user_id', $userId)->delete();
        $this->assignRole($userId, $roleId);
    }
}
