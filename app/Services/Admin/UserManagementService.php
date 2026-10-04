<?php

namespace App\Services\Admin;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\UserRoleModel;
use App\Services\AuditService;

/**
 * User management service for the Admin Dashboard.
 *
 * Encapsulates user + profile + role persistence for the Manajemen Akses
 * menu. Every mutating call writes audit_logs and workflow_transitions.
 *
 * Status transitions are validated server-side per CLAUDE.md §Account Status
 * Lifecycle. Roles are managed exclusively through UserRoleModel; this
 * service does not bypass it.
 */
class UserManagementService
{
    public const ALLOWED_PROFILE_TYPES = ['asn', 'provider', 'academic', 'public'];
    public const ALLOWED_STATUSES = [
        'draft', 'submitted', 'pending_verification', 'revision_required',
        'active', 'rejected', 'inactive', 'suspended', 'archived',
    ];

    public function __construct(
        private readonly UserModel $users = new UserModel(),
        private readonly UserProfileModel $profiles = new UserProfileModel(),
        private readonly UserRoleModel $userRoles = new UserRoleModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    /**
     * Create or update a user record (admin-side, not self-registration).
     *
     * @param array $data validated input (username, email, name, profile_type,
     *                    phone, password_hash, account_status, optional role_id)
     * @param int|null $userId null = create, int = update
     * @return int user id
     */
    public function saveUser(array $data, ?int $userId = null, ?int $actorId = null): int
    {
        $account = [
            'username'       => trim((string) ($data['username'] ?? '')),
            'email'          => strtolower(trim((string) ($data['email'] ?? ''))),
            'name'           => trim((string) ($data['name'] ?? $data['full_name'] ?? '')),
            'profile_type'   => (string) ($data['profile_type'] ?? 'public'),
            'phone'          => trim((string) ($data['phone'] ?? '')),
            'account_status' => (string) ($data['account_status'] ?? 'draft'),
        ];

        if (! empty($data['password_hash'])) {
            $account['password_hash'] = (string) $data['password_hash'];
        }

        if ($userId === null) {
            $userId = (int) $this->users->insert($account, true);
        } else {
            $this->users->update($userId, $account);
        }

        // Role assignment (single-role for admin-side create; multi-role supported via direct DB).
        if (! empty($data['role_id'])) {
            $this->assignRole((int) $userId, (int) $data['role_id'], $actorId);
        }

        $this->audit->log(
            $actorId,
            $userId ? 'user.update' : 'user.create',
            'users',
            'user',
            $userId,
        );

        return (int) $userId;
    }

    public function assignRole(int $userId, int $roleId, ?int $actorId = null): void
    {
        $existing = $this->userRoles
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->first();

        if ($existing) {
            $this->userRoles->update($existing['id'], [
                'status'     => 'active',
                'revoked_by' => null,
                'revoked_at' => null,
            ]);
            return;
        }

        $this->userRoles->insert([
            'user_id'    => $userId,
            'role_id'    => $roleId,
            'status'     => 'active',
            'granted_by' => $actorId,
            'granted_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log($actorId, 'user.role.grant', 'user_roles', 'user_role', $userId);
    }

    /**
     * Update account_status — every transition is audited and recorded as
     * a workflow_transition for the user resource.
     */
    public function updateStatus(int $userId, string $newStatus, ?int $actorId = null, ?string $reason = null): void
    {
        if (! in_array($newStatus, self::ALLOWED_STATUSES, true)) {
            throw new \InvalidArgumentException("Status akun tidak valid: {$newStatus}");
        }

        $current = $this->users->find($userId);
        if (! $current) {
            throw new \RuntimeException('Pengguna tidak ditemukan.');
        }

        $oldStatus = (string) ($current['account_status'] ?? 'draft');

        $this->users->update($userId, [
            'account_status' => $newStatus,
            'status_reason'  => $reason,
        ]);

        $this->audit->log(
            $actorId,
            'user.status.update',
            'users',
            'user',
            $userId,
            ['from' => $oldStatus, 'to' => $newStatus, 'reason' => $reason],
        );

        $this->recordTransition('user', $userId, $oldStatus, $newStatus, 'status.update', $actorId, $reason);
    }

    /**
     * Suspend helper — sets account_status to suspended with reason.
     */
    public function suspend(int $userId, string $reason, ?int $actorId = null): void
    {
        $this->updateStatus($userId, 'suspended', $actorId, $reason);
    }

    /**
     * Activate helper.
     */
    public function activate(int $userId, ?int $actorId = null, ?string $reason = null): void
    {
        $this->updateStatus($userId, 'active', $actorId, $reason);
    }

    public function recordTransition(
        string $resourceType,
        int $resourceId,
        string $fromState,
        string $toState,
        string $action,
        ?int $actorId = null,
        ?string $reason = null,
        ?string $notes = null,
        ?array $metadata = null,
    ): int {
        $db = db_connect();
        return (int) $db->table('workflow_transitions')->insert([
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'from_state'    => $fromState,
            'to_state'      => $toState,
            'action'        => $action,
            'actor_id'      => $actorId,
            'actor_type'    => 'user',
            'reason'        => $reason,
            'notes'         => $notes,
            'metadata'      => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
            'created_at'    => date('Y-m-d H:i:s'),
        ], true);
    }
}