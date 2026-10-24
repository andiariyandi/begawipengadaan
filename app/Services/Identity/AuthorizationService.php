<?php

namespace App\Services\Identity;

class AuthorizationService
{
    public function can(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, (array) session()->get('permissions'), true);
    }

    public function canAny(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userPermissions = (array) session()->get('permissions');
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions, true)) {
                return true;
            }
        }

        return false;
    }

    public function canModule(string $domainPrefix): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $prefix = rtrim($domainPrefix, '.') . '.';
        $userPermissions = (array) session()->get('permissions');
        foreach ($userPermissions as $p) {
            if (str_starts_with((string) $p, $prefix)) {
                return true;
            }
        }

        return false;
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

    public function hasDashboardAccess(
        ?int $userId = null,
        ?array $userRoles = null,
        ?array $userPermissions = null,
        ?string $accountStatus = null
    ): bool {
        $status = $accountStatus ?? session()->get('account_status');
        if ($status !== null && $status !== 'active') {
            return false;
        }

        $roles = $userRoles ?? (array) session()->get('roles');

        if (in_array('super_administrator', $roles, true) || in_array('administrator', $roles, true)) {
            return true;
        }

        $internalRoles = ['super_administrator', 'administrator', 'operator', 'trainer'];
        if (array_intersect($roles, $internalRoles) !== []) {
            return true;
        }

        $permissions = $userPermissions ?? (array) session()->get('permissions');
        $validPrefixes = [
            'dashboard.',
            'admin.',
            'administrator.',
            'system.',
            'access.',
            'landing.',
            'content.',
            'regulation.',
            'learning.',
            'lms.',
            'bunga.',
            'kms.',
            'knowledge.',
            'communication.',
            'event.',
            'certificate.',
            'intelligence.',
            'reports.',
            'survey.',
            'approval.',
            'approvals.',
            'notification.',
            'user.',
            'role.',
            'audit.',
            'profile.',
        ];

        foreach ($permissions as $p) {
            foreach ($validPrefixes as $prefix) {
                if (str_starts_with((string) $p, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
