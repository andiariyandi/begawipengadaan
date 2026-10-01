<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table         = 'user_roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'role_id', 'status', 'granted_by', 'granted_at', 'revoked_by', 'revoked_at', 'reason'];

    public function assignRole(int $userId, int $roleId, ?int $grantedBy = null, ?string $reason = null): int
    {
        $existing = $this->where(['user_id' => $userId, 'role_id' => $roleId])->first();
        $data = [
            'status' => 'active',
            'granted_by' => $grantedBy,
            'granted_at' => date('Y-m-d H:i:s'),
            'revoked_by' => null,
            'revoked_at' => null,
            'reason' => $reason,
        ];
        if ($existing) {
            $this->update($existing['id'], $data);
            return (int) $existing['id'];
        }

        return (int) $this->insert(array_merge(['user_id' => $userId, 'role_id' => $roleId], $data), true);
    }

    public function revokeRole(int $userId, int $roleId, ?int $revokedBy = null, ?string $reason = null): bool
    {
        return $this->where(['user_id' => $userId, 'role_id' => $roleId, 'status' => 'active'])
            ->set([
                'status' => 'revoked',
                'revoked_by' => $revokedBy,
                'revoked_at' => date('Y-m-d H:i:s'),
                'reason' => $reason,
            ])->update();
    }

    public function replaceRole(int $userId, int $roleId, ?int $grantedBy = null, ?string $reason = null): int
    {
        $this->where('user_id', $userId)->where('status', 'active')->set([
            'status' => 'revoked',
            'revoked_by' => $grantedBy,
            'revoked_at' => date('Y-m-d H:i:s'),
            'reason' => $reason,
        ])->update();

        return $this->assignRole($userId, $roleId, $grantedBy, $reason);
    }
}
