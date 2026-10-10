<?php

namespace App\Services\Identity;

class AuthorizationService
{
    public function can(string $permission): bool
    {
        return in_array($permission, (array) session()->get('permissions'), true);
    }

    public function isSuperAdmin(): bool
    {
        $roles = (array) session()->get('roles');
        return in_array('super_administrator', $roles, true);
    }

    public function isAdmin(): bool
    {
        $roles = (array) session()->get('roles');
        return $this->isSuperAdmin() || in_array('administrator', $roles, true);
    }

    public function hasDashboardAccess(): bool
    {
        if (session()->get('account_status') !== 'active') {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        $permissions = (array) session()->get('permissions');

        return (bool) array_filter($permissions, static fn (string $permission): bool => str_contains($permission, '.'));
    }
}
