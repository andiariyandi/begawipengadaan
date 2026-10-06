<?php

namespace App\Services\Identity;

class AuthorizationService
{
    public function can(string $permission): bool
    {
        return in_array($permission, (array) session()->get('permissions'), true);
    }

    public function hasDashboardAccess(): bool
    {
        if (session()->get('account_status') !== 'active') {
            return false;
        }

        $permissions = (array) session()->get('permissions');

        return (bool) array_filter($permissions, static fn (string $permission): bool => str_contains($permission, '.'));
    }
}
