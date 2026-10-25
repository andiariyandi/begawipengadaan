<?php

namespace App\Services\Admin;

use App\Models\RoleRequestModel;
use App\Models\UserRoleModel;
use App\Services\AuditService;
use App\Services\NotificationService;

/**
 * Role request review service (Manajemen Akses → Permohonan Role).
 *
 * ASN participants can request trainer or operator roles (CLAUDE.md §15).
 * Approve => insert into user_roles with status='active' atomically.
 * Reject/revision require a note.
 */
class RoleRequestService
{
    public const ALLOWED_SELF_REQUEST_ROLES = ['trainer', 'operator'];

    public function __construct(
        private readonly RoleRequestModel $requests = new RoleRequestModel(),
        private readonly UserRoleModel $userRoles = new UserRoleModel(),
        private readonly AuditService $audit = new AuditService(),
        private readonly UserManagementService $userService = new UserManagementService(),
    ) {}

    public function queue(array $filters = []): array
    {
        $builder = db_connect()->table('role_requests rr')
            ->select('rr.*, u.name AS user_name, u.email, u.username, r.name AS role_name, r.key AS role_key')
            ->join('users u', 'u.id = rr.user_id', 'left')
            ->join('roles r', 'r.id = rr.requested_role_id', 'left')
            ->orderBy('rr.created_at', 'DESC');

        if (! empty($filters['status'])) {
            $builder->where('rr.status', $filters['status']);
        }

        return $builder->get()->getResultArray();
    }

    public function find(int $id): ?array
    {
        return $this->requests->find($id);
    }

    public function submit(int $userId, int $requestedRoleId, ?string $reason = null): int
    {
        return (int) $this->requests->insert([
            'user_id'           => $userId,
            'requested_role_id' => $requestedRoleId,
            'status'            => 'submitted',
            'reason'            => $reason,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ], true);
    }

    public function approve(int $requestId, ?int $actorId = null, ?string $notes = null): bool
    {
        $row = $this->requests->find($requestId);
        if (! $row) {
            return false;
        }

        $userId = (int) $row['user_id'];
        $roleId = (int) $row['requested_role_id'];
        $now = date('Y-m-d H:i:s');

        $db = db_connect();
        $db->transStart();

        $this->requests->update($requestId, [
            'status'       => 'approved',
            'reviewed_by'  => $actorId,
            'reviewed_at'  => $now,
            'review_notes' => $notes,
            'updated_at'   => $now,
        ]);

        // Atomic role grant (UNIQUE constraint user_id+role_id handled by UserManagementService).
        $this->userService->assignRole($userId, $roleId, $actorId);

        $this->userService->recordTransition(
            'role_request',
            $requestId,
            (string) $row['status'],
            'approved',
            'role_request.approve',
            $actorId,
            null,
            $notes,
        );

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal menyetujui permohonan role.');
        }

        $this->audit->log($actorId, 'role_request.approve', 'role_requests', 'role_request', $requestId);

        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        $roleName = $role['name'] ?? 'Peran Baru';

        try {
            (new NotificationService($db))->send(
                $userId,
                'role_request_approved',
                'Permohonan Peran Disetujui',
                "Selamat! Permohonan penambahan peran Anda sebagai {$roleName} telah disetujui oleh Administrator." . ($notes ? " Catatan: {$notes}" : ''),
                [
                    'role_id'    => $roleId,
                    'request_id' => $requestId,
                    'url'        => site_url('workspace/profil/permohonan-role'),
                ]
            );

            // Tandai notifikasi bersama pengajuan peran sebagai terbaca bagi seluruh pool verifikator
            $db->table('notifications')
                ->where('channel', 'shared:admin_operator')
                ->where('type', 'role_request_submitted')
                ->groupStart()
                    ->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.request_id')) =", (string) $requestId)
                    ->orLike('data', '"request_id": ' . $requestId)
                    ->orLike('data', '"request_id":' . $requestId)
                ->groupEnd()
                ->where('read_at IS NULL')
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {
            log_message('warning', '[RoleRequestService::approve] notification failed: ' . $e->getMessage());
        }
        return true;
    }

    public function reject(int $requestId, string $note, ?int $actorId = null): bool
    {
        if (trim($note) === '') {
            throw new \InvalidArgumentException('Alasan penolakan wajib diisi.');
        }

        $row = $this->requests->find($requestId);
        if (! $row) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $this->requests->update($requestId, [
            'status'       => 'rejected',
            'reviewed_by'  => $actorId,
            'reviewed_at'  => $now,
            'review_notes' => $note,
            'updated_at'   => $now,
        ]);

        $this->userService->recordTransition(
            'role_request',
            $requestId,
            (string) $row['status'],
            'rejected',
            'role_request.reject',
            $actorId,
            $note,
        );

        $this->audit->log($actorId, 'role_request.reject', 'role_requests', 'role_request', $requestId);

        $db = db_connect();
        $role = $db->table('roles')->where('id', (int) $row['requested_role_id'])->get()->getRowArray();
        $roleName = $role['name'] ?? 'Peran Baru';

        try {
            (new NotificationService($db))->send(
                (int) $row['user_id'],
                'role_request_rejected',
                'Permohonan Peran Ditolak',
                "Mohon maaf, permohonan penambahan peran Anda sebagai {$roleName} belum disetujui. Alasan: {$note}",
                [
                    'role_id'    => (int) $row['requested_role_id'],
                    'request_id' => $requestId,
                    'url'        => site_url('workspace/profil/permohonan-role'),
                ]
            );

            // Tandai notifikasi bersama pengajuan peran sebagai terbaca bagi seluruh pool verifikator
            $db->table('notifications')
                ->where('channel', 'shared:admin_operator')
                ->where('type', 'role_request_submitted')
                ->groupStart()
                    ->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.request_id')) =", (string) $requestId)
                    ->orLike('data', '"request_id": ' . $requestId)
                    ->orLike('data', '"request_id":' . $requestId)
                ->groupEnd()
                ->where('read_at IS NULL')
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {
            log_message('warning', '[RoleRequestService::reject] notification failed: ' . $e->getMessage());
        }
        return true;
    }

    public function requestRevision(int $requestId, string $note, ?int $actorId = null): bool
    {
        if (trim($note) === '') {
            throw new \InvalidArgumentException('Catatan revisi wajib diisi.');
        }

        $row = $this->requests->find($requestId);
        if (! $row) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $this->requests->update($requestId, [
            'status'       => 'revision_required',
            'reviewed_by'  => $actorId,
            'reviewed_at'  => $now,
            'review_notes' => $note,
            'updated_at'   => $now,
        ]);

        $this->userService->recordTransition(
            'role_request',
            $requestId,
            (string) $row['status'],
            'revision_required',
            'role_request.revision',
            $actorId,
            $note,
        );

        $this->audit->log($actorId, 'role_request.revision', 'role_requests', 'role_request', $requestId);

        $db = db_connect();
        $role = $db->table('roles')->where('id', (int) $row['requested_role_id'])->get()->getRowArray();
        $roleName = $role['name'] ?? 'Peran Baru';

        try {
            (new NotificationService($db))->send(
                (int) $row['user_id'],
                'role_request_revision',
                'Permohonan Peran Memerlukan Revisi',
                "Permohonan penambahan peran Anda sebagai {$roleName} memerlukan perbaikan berkas/keterangan. Catatan verifikator: {$note}",
                [
                    'role_id'    => (int) $row['requested_role_id'],
                    'request_id' => $requestId,
                    'url'        => site_url('workspace/profil/permohonan-role'),
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', '[RoleRequestService::requestRevision] notification failed: ' . $e->getMessage());
        }
        return true;
    }
}