<?php

namespace App\Services;

class AccessControlService
{
    public function canAccess(array $roles, string $requiredRole): bool
    {
        return in_array($requiredRole, $roles, true);
    }

    public function isAdmin(array $roles): bool
    {
        return in_array('administrator', $roles, true)
            || in_array('super_administrator', $roles, true);
    }
}
