<?php

namespace App\Services\Admin;

use App\Models\ProfileChangeRequestModel;
use App\Services\AuditService;

/**
 * Profile change request service (Manajemen Akses → Perubahan Profil).
 *
 * Workflow: draft → submitted → in_review → approved | revision_required | rejected → archived
 *
 * Approve applies the requested_payload atomically to the appropriate
 * profile-type table (asn_profiles / provider_profiles / academic_profiles /
 * public_profiles / user_profiles). Every decision writes audit_logs AND
 * workflow_transitions.
 */
class ProfileChangeRequestService
{
    public function __construct(
        private readonly ProfileChangeRequestModel $requests = new ProfileChangeRequestModel(),
        private readonly AuditService $audit = new AuditService(),
        private readonly UserManagementService $userService = new UserManagementService(),
    ) {}

    public function queue(array $filters = []): array
    {
        $builder = db_connect()->table('profile_change_requests pcr')
            ->select('pcr.*, u.name AS user_name, u.email, u.username, u.profile_type')
            ->join('users u', 'u.id = pcr.user_id', 'left')
            ->orderBy('pcr.created_at', 'DESC');

        if (! empty($filters['status'])) {
            $builder->where('pcr.status', $filters['status']);
        }

        return $builder->get()->getResultArray();
    }

    public function find(int $id): ?array
    {
        return $this->requests->find($id);
    }

    public function submit(int $userId, array $payload, ?string $reason = null): int
    {
        return (int) $this->requests->insert([
            'user_id'           => $userId,
            'status'            => 'submitted',
            'requested_payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'reason'            => $reason,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ], true);
    }

    /**
     * Approve the change request: apply the JSON payload atomically to the
     * appropriate profile-type table, then mark request approved.
     */
    public function approve(int $requestId, ?int $actorId = null, ?string $notes = null): bool
    {
        $row = $this->requests->find($requestId);
        if (! $row) {
            return false;
        }

        $userId = (int) $row['user_id'];
        $payload = json_decode((string) ($row['requested_payload'] ?? '{}'), true);
        if (! is_array($payload)) {
            throw new \InvalidArgumentException('Payload permohonan perubahan tidak valid.');
        }

        $db = db_connect();
        $db->transStart();

        $table = $this->resolveTableForPayload($payload);

        try {
            $existing = $db->table($table)->where('user_id', $userId)->get()->getRowArray();
            $now = date('Y-m-d H:i:s');

            if ($existing) {
                // Merge with existing row, then add timestamp.
                $merged = array_merge($existing, $payload);
                $merged['updated_at'] = $now;
                unset($merged['id'], $merged['created_at']);
                $db->table($table)->where('user_id', $userId)->update($merged);
            } else {
                $payload['user_id'] = $userId;
                $payload['created_at'] = $now;
                $payload['updated_at'] = $now;
                $db->table($table)->insert($payload);
            }

            $this->requests->update($requestId, [
                'status'        => 'approved',
                'reviewed_by'   => $actorId,
                'reviewed_at'   => $now,
                'review_notes'  => $notes,
                'applied_at'    => $now,
                'updated_at'    => $now,
            ]);

            $this->userService->recordTransition(
                'profile_change_request',
                $requestId,
                (string) $row['status'],
                'approved',
                'profile_change.approve',
                $actorId,
                null,
                $notes,
                ['applied_table' => $table],
            );

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal apply perubahan: ' . $e->getMessage(), 0, $e);
        }

        $this->audit->log($actorId, 'profile_change.approve', 'profile_change_requests', 'profile_change_request', $requestId, [
            'user_id' => $userId,
            'table'   => $table,
        ]);

        return true;
    }

    public function reject(int $requestId, string $reason, ?int $actorId = null, ?string $notes = null): bool
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan penolakan wajib diisi.');
        }

        $row = $this->requests->find($requestId);
        if (! $row) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $this->requests->update($requestId, [
            'status'        => 'rejected',
            'reviewed_by'   => $actorId,
            'reviewed_at'   => $now,
            'reason'        => $reason,
            'review_notes'  => $notes,
            'updated_at'    => $now,
        ]);

        $this->userService->recordTransition(
            'profile_change_request',
            $requestId,
            (string) $row['status'],
            'rejected',
            'profile_change.reject',
            $actorId,
            $reason,
            $notes,
        );

        $this->audit->log($actorId, 'profile_change.reject', 'profile_change_requests', 'profile_change_request', $requestId);
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
            'status'        => 'revision_required',
            'reviewed_by'   => $actorId,
            'reviewed_at'   => $now,
            'review_notes'  => $note,
            'updated_at'    => $now,
        ]);

        $this->userService->recordTransition(
            'profile_change_request',
            $requestId,
            (string) $row['status'],
            'revision_required',
            'profile_change.revision',
            $actorId,
            $note,
        );

        $this->audit->log($actorId, 'profile_change.revision', 'profile_change_requests', 'profile_change_request', $requestId);
        return true;
    }

    /**
     * Pick target table based on which keys are present in the payload.
     * Order: specific table wins over generic user_profiles.
     */
    private function resolveTableForPayload(array $payload): string
    {
        $specificKeys = [
            'asn_profiles'      => ['employee_identifier', 'origin_type', 'unit_name', 'position_name', 'rank_grade', 'official_email'],
            'provider_profiles' => ['business_name', 'business_form', 'business_field', 'business_identifier', 'registrant_position', 'responsible_person', 'business_email', 'business_address'],
            'academic_profiles' => ['institution_name', 'institution_type', 'role_name', 'expertise', 'institution_email', 'professional_identifier', 'portfolio_url'],
            'public_profiles'   => ['national_identifier', 'occupation_status', 'organization_name', 'interest_areas', 'usage_purpose'],
        ];

        foreach ($specificKeys as $table => $keys) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $payload)) {
                    return $table;
                }
            }
        }

        return 'user_profiles';
    }
}