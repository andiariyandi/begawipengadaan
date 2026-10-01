<?php

namespace App\Services;

use App\Models\UserRoleModel;
use RuntimeException;

class RoleAssignmentService
{
    public function __construct(private readonly ?UserRoleModel $userRoleModel = null)
    {
    }

    public function assign(int $userId, int $roleId, ?int $grantedBy = null, ?string $reason = null): int
    {
        return ($this->userRoleModel ?? new UserRoleModel())->assignRole($userId, $roleId, $grantedBy, $reason);
    }

    public function revoke(int $userId, int $roleId, ?int $revokedBy = null, ?string $reason = null): bool
    {
        $result = ($this->userRoleModel ?? new UserRoleModel())->revokeRole($userId, $roleId, $revokedBy, $reason);
        if (! $result) {
            throw new RuntimeException('Assignment role aktif tidak ditemukan.');
        }
        return true;
    }

    public function replace(int $userId, int $roleId, ?int $grantedBy = null, ?string $reason = null): int
    {
        return ($this->userRoleModel ?? new UserRoleModel())->replaceRole($userId, $roleId, $grantedBy, $reason);
    }
}
