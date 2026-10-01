<?php

namespace App\Services\Identity;

use App\Models\UserModel;
use App\Services\AuditService;

class AuthenticationService
{
    public function __construct(
        private readonly UserModel $users = new UserModel(),
        private readonly AuditService $audit = new AuditService()
    ) {
    }

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

            return ['ok' => false, 'reason' => 'inactive_account'];
        }

        $roles = $this->users->getRoles((int) $user['id']);
        $permissions = $this->users->getPermissions((int) $user['id']);

        session()->regenerate(true);
        session()->set([
            'isLoggedIn' => true,
            'user_id' => (int) $user['id'],
            'username' => $user['username'],
            'account_status' => $user['account_status'],
            'roles' => array_column($roles, 'code'),
            'permissions' => $permissions,
        ]);

        $this->users->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
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
