<?php

namespace App\Services\Identity;

use App\Models\UserModel;
use App\Services\AuditService;

class AuthenticationService
{
    public function __construct(
        private readonly UserModel $users = new UserModel(),
        private readonly AuditService $audit = new AuditService()
    ) {}

    /**
     * @return array{ok: bool, reason?: string, user?: array}
     */
    public function authenticate(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $user = $this->users->findByEmail($email);

        if (! $user || ! password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $this->audit->log(null, 'login_failed', 'auth', 'users');

            return ['ok' => false, 'reason' => 'invalid_credentials'];
        }

        if (($user['account_status'] ?? null) !== 'active') {
            $this->audit->log((int) $user['id'], 'login_blocked', 'auth', 'users', (int) $user['id'], [
                'account_status' => $user['account_status'] ?? null,
            ]);

            return ['ok' => false, 'reason' => 'inactive_account', 'user' => $user];
        }

        $roles = $this->users->getRoles((int) $user['id']);
        $permissions = $this->users->getPermissions((int) $user['id']);

        if (session_status() === PHP_SESSION_ACTIVE && ! is_cli()) {
            session()->regenerate(true);
        }

        $roleKeys = array_column($roles, 'key');
        $hasDashboardAccess = (new AuthorizationService())->hasDashboardAccess(
            (int) $user['id'],
            $roleKeys,
            $permissions,
            $user['account_status'] ?? null
        );

        session()->set([
            'isLoggedIn'       => true,
            'user_id'          => (int) $user['id'],
            'name'             => $user['name'],
            'username'         => $user['username'],
            'email'            => $user['email'] ?? '',
            'phone'            => $user['phone'] ?? null,
            'account_status'   => $user['account_status'],
            'roles'            => $roleKeys,
            'permissions'      => $permissions,
            'dashboard_access' => $hasDashboardAccess,
        ]);
        $this->users->skipValidation(true)->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->audit->log((int) $user['id'], 'login', 'auth', 'users', (int) $user['id']);

        return ['ok' => true, 'user' => $user];
    }

    public function logout(): void
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $this->audit->log((int) $userId, 'logout', 'auth', 'users', (int) $userId);
        }

        session()->destroy();
    }
}
