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
        $now = date('Y-m-d H:i:s');
        $id = (int) $this->requests->insert([
            'user_id'           => $userId,
            'status'            => 'submitted',
            'requested_payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'reason'            => $reason,
            'created_at'        => $now,
            'updated_at'        => $now,
        ], true);

        try {
            $this->userService->recordTransition(
                'profile_change_request',
                $id,
                'draft',
                'submitted',
                'profile_change.submit',
                $userId,
                $reason,
                null,
                ['payload_keys' => array_keys($payload)]
            );
        } catch (\Throwable) {
        }

        try {
            $this->audit->log($userId, 'profile_change.submit', 'profile_change_requests', 'profile_change_request', $id, [
                'user_id' => $userId,
                'keys'    => array_keys($payload),
            ]);
        } catch (\Throwable) {
        }

        return $id;
    }

    /**
     * Approve the change request: apply the JSON payload atomically to the
     * appropriate tables (users, user_profiles, and profile-type specific table),
     * then mark request approved.
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

        try {
            $now = date('Y-m-d H:i:s');
            $userRow = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $profileType = $userRow['profile_type'] ?? 'public';

            // 1. Update users table if relevant fields present
            $userUpdate = [];
            if (! empty($payload['name'])) {
                $userUpdate['name'] = trim((string) $payload['name']);
            } elseif (! empty($payload['full_name'])) {
                $userUpdate['name'] = trim((string) $payload['full_name']);
            }
            if (! empty($payload['email'])) {
                $userUpdate['email'] = trim((string) $payload['email']);
            }
            if (isset($payload['phone'])) {
                $userUpdate['phone'] = trim((string) $payload['phone']);
            }
            if (! empty($payload['username'])) {
                $userUpdate['username'] = trim((string) $payload['username']);
            }
            if (! empty($userUpdate)) {
                $userUpdate['updated_at'] = $now;
                $db->table('users')->where('id', $userId)->update($userUpdate);
            }

            // 2. Update user_profiles table if relevant fields present
            $profileUpdate = [];
            $userProfileKeys = ['address_line', 'province_code', 'regency_code', 'district_code', 'village_code'];
            foreach ($userProfileKeys as $k) {
                if (array_key_exists($k, $payload)) {
                    $profileUpdate[$k] = $payload[$k];
                }
            }
            if (! empty($profileUpdate)) {
                $profileUpdate['updated_at'] = $now;
                $existingUp = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
                if ($existingUp) {
                    $db->table('user_profiles')->where('user_id', $userId)->update($profileUpdate);
                } else {
                    $profileUpdate['user_id']    = $userId;
                    $profileUpdate['created_at'] = $now;
                    $db->table('user_profiles')->insert($profileUpdate);
                }
            }

            // 3. Update specific profile table based on profile_type
            $typeTable = match ($profileType) {
                'asn'      => 'asn_profiles',
                'provider' => 'provider_profiles',
                'academic' => 'academic_profiles',
                'public'   => 'public_profiles',
                default    => null,
            };

            if ($typeTable) {
                $specificKeys = [
                    'asn_profiles'      => ['employee_identifier', 'origin_type', 'unit_name', 'position_name', 'rank_grade', 'official_email', 'employment_status', 'institution_name'],
                    'provider_profiles' => ['business_name', 'business_form', 'business_field', 'business_identifier', 'registrant_position', 'responsible_person', 'business_email', 'business_address'],
                    'academic_profiles' => ['institution_name', 'institution_type', 'role_name', 'expertise', 'institution_email', 'professional_identifier', 'portfolio_url'],
                    'public_profiles'   => ['national_identifier', 'occupation_status', 'organization_name', 'interest_areas', 'usage_purpose'],
                ][$typeTable] ?? [];

                $specificUpdate = [];
                foreach ($specificKeys as $sk) {
                    if (array_key_exists($sk, $payload)) {
                        $specificUpdate[$sk] = $payload[$sk];
                    }
                }

                if (! empty($specificUpdate)) {
                    $specificUpdate['updated_at'] = $now;
                    $existingSpec = $db->table($typeTable)->where('user_id', $userId)->get()->getRowArray();
                    if ($existingSpec) {
                        $db->table($typeTable)->where('user_id', $userId)->update($specificUpdate);
                    } else {
                        $specificUpdate['user_id']    = $userId;
                        $specificUpdate['created_at'] = $now;
                        $db->table($typeTable)->insert($specificUpdate);
                    }
                }
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
                ['applied_tables' => array_filter(['users' => !empty($userUpdate), 'user_profiles' => !empty($profileUpdate), 'specific' => $typeTable])],
            );

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menerapkan transaksi perubahan profil.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal menerapkan perubahan profil: ' . $e->getMessage(), 0, $e);
        }

        $this->audit->log($actorId, 'profile_change.approve', 'profile_change_requests', 'profile_change_request', $requestId, [
            'user_id' => $userId,
            'applied' => array_keys($payload),
        ]);

        // Send notification to user
        try {
            (new \App\Services\NotificationService())->send(
                $userId,
                'profile_change_approved',
                'Permohonan Perubahan Profil Disetujui',
                'Permohonan perubahan data profil Anda telah disetujui dan diterapkan secara resmi ke akun Anda.' . ($notes ? " Catatan verifikator: {$notes}" : ''),
                ['request_id' => $requestId, 'url' => route_to('workspace.profile.index')]
            );
        } catch (\Throwable) {
        }

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

        // Send notification to user
        try {
            (new \App\Services\NotificationService())->send(
                (int) $row['user_id'],
                'profile_change_rejected',
                'Permohonan Perubahan Profil Ditolak',
                'Permohonan perubahan data profil Anda ditolak. Alasan: ' . $reason . ($notes ? " (Catatan: {$notes})" : ''),
                ['request_id' => $requestId, 'url' => route_to('workspace.profile.index')]
            );
        } catch (\Throwable) {
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

        // Send notification to user
        try {
            (new \App\Services\NotificationService())->send(
                (int) $row['user_id'],
                'profile_change_revision',
                'Permintaan Revisi Perubahan Profil',
                'Permohonan perubahan data profil Anda memerlukan perbaikan. Catatan revisi: ' . $note,
                ['request_id' => $requestId, 'url' => route_to('workspace.profile.index')]
            );
        } catch (\Throwable) {
        }

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