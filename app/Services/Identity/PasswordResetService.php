<?php

namespace App\Services\Identity;

use App\Models\PasswordResetModel;
use App\Models\UserModel;
use App\Services\AuditService;

/**
 * Password reset flow for unified Auth boundary.
 *
 * Generic response model: callers MUST NOT reveal whether an email is
 * registered. Audit logs MUST NOT store plain token, password, or hash.
 *
 * Tokens:
 *   - random_bytes(32) -> bin2hex -> 64-char plain (sent through email)
 *   - sha256(plain) stored as token_hash
 *   - expires_at = now + 1 hour
 *   - one-time use: token marked used_at on successful reset
 *
 * Storage: password_resets table.
 */
class PasswordResetService
{
    public const TOKEN_TTL_SECONDS = 3600;

    public function __construct(
        private readonly PasswordResetModel $resets = new PasswordResetModel(),
        private readonly UserModel $users = new UserModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    /**
     * Request a password reset.
     *
     * Returns true if a token was issued, false otherwise.
     * The caller MUST respond with the same generic message regardless.
     */
    public function requestReset(string $email): bool
    {
        $email = strtolower(trim($email));
        $user = $this->users->findByEmail($email);

        if (! $user) {
            return false;
        }

        $userId = (int) $user['id'];
        $plainToken = bin2hex(random_bytes(32));

        $this->resets->insert([
            'user_id'    => $userId,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => date('Y-m-d H:i:s', time() + self::TOKEN_TTL_SECONDS),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log($userId, 'password_reset_requested', 'auth', 'users', $userId);

        return true;
    }

    /**
     * Validate a plain token. Returns the token row (with user_id) or null.
     * Used by controller to load form view AND by update to perform reset.
     */
    public function findValidToken(string $plainToken): ?array
    {
        $hash = hash('sha256', $plainToken);

        $row = $this->resets
            ->where('token_hash', $hash)
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        return $row ?: null;
    }

    /**
     * Update password using a validated token.
     *
     * Returns true on success, false if token invalid/expired/used.
     */
    public function resetPassword(string $plainToken, string $newPassword): bool
    {
        $row = $this->findValidToken($plainToken);
        if (! $row) {
            return false;
        }

        $userId = (int) $row['user_id'];

        $this->users->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        $this->resets->update((int) $row['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log($userId, 'password_reset_completed', 'auth', 'users', $userId);

        return true;
    }
}