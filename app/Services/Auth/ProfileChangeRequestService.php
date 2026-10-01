<?php

namespace App\Services\Auth;

use App\Models\AuditLogModel;
use App\Models\ProfileChangeRequestModel;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;
use Throwable;

class ProfileChangeRequestService
{
    private const USER_FIELDS = ['name', 'phone', 'profile_type'];
    private const PROFILE_FIELDS = [
        'address_line', 'province_code', 'regency_code', 'district_code',
        'village_code', 'profile_completion_status',
    ];

    private ProfileChangeRequestModel $requestModel;
    private UserModel $userModel;
    private UserProfileModel $profileModel;
    private AuditLogModel $auditLogModel;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->requestModel = new ProfileChangeRequestModel();
        $this->userModel = new UserModel();
        $this->profileModel = new UserProfileModel();
        $this->auditLogModel = new AuditLogModel();
        $this->db = db_connect();
    }

    public function submit(int $userId, array $payload, ?string $reason = null): int
    {
        $cleanPayload = $this->whitelistPayload($payload);
        if ($cleanPayload === []) {
            throw new RuntimeException('Tidak ada field profil yang dapat diajukan.');
        }

        return (int) $this->requestModel->insert([
            'user_id' => $userId,
            'status' => 'submitted',
            'requested_payload' => json_encode($cleanPayload, JSON_THROW_ON_ERROR),
            'reason' => $reason,
        ], true);
    }

    public function approve(int $requestId, int $reviewedBy, ?string $notes = null): bool
    {
        return $this->review($requestId, $reviewedBy, 'approved', null, $notes, true);
    }

    public function reject(int $requestId, int $reviewedBy, string $reason): bool
    {
        return $this->review($requestId, $reviewedBy, 'rejected', $reason);
    }

    public function revision(int $requestId, int $reviewedBy, string $reason): bool
    {
        return $this->review($requestId, $reviewedBy, 'revision_required', $reason);
    }

    private function review(int $requestId, int $reviewedBy, string $status, ?string $reason, ?string $notes = null, bool $apply = false): bool
    {
        $request = $this->requestModel->find($requestId);
        if (! $request || ! in_array($request['status'], ['submitted', 'in_review'], true)) {
            throw new RuntimeException('Permohonan perubahan profil tidak ditemukan atau sudah diproses.');
        }

        $payload = json_decode($request['requested_payload'], true);
        if (! is_array($payload)) {
            throw new RuntimeException('Payload perubahan profil tidak valid.');
        }

        $this->db->transBegin();
        try {
            $now = date('Y-m-d H:i:s');
            if ($apply) {
                $this->applyPayload((int) $request['user_id'], $payload);
            }

            $this->requestModel->update($requestId, [
                'status' => $status,
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => $now,
                'review_notes' => $notes,
                'reason' => $reason ?? $request['reason'],
                'applied_at' => $apply ? $now : null,
            ]);
            $this->auditLogModel->insert([
                'actor_id' => $reviewedBy,
                'action' => 'profile_change_request_' . $status,
                'resource_type' => 'profile_change_request',
                'resource_id' => $requestId,
                'reason' => $reason ?? $notes,
                'result' => 'success',
                'created_at' => $now,
            ]);

            $this->assertTransaction('Perubahan profil gagal disimpan.');
            $this->db->transCommit();
            return true;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function applyPayload(int $userId, array $payload): void
    {
        $userData = array_intersect_key($payload, array_flip(self::USER_FIELDS));
        $profileData = array_intersect_key($payload, array_flip(self::PROFILE_FIELDS));
        if ($userData !== []) {
            $this->userModel->update($userId, $userData);
        }
        if ($profileData !== []) {
            $profile = $this->profileModel->where('user_id', $userId)->first();
            if (! $profile) {
                throw new RuntimeException('Profil pengguna tidak ditemukan.');
            }
            $this->profileModel->update($profile['id'], $profileData);
        }
    }

    private function whitelistPayload(array $payload): array
    {
        return array_filter(
            array_intersect_key($payload, array_flip(array_merge(self::USER_FIELDS, self::PROFILE_FIELDS))),
            static fn ($value): bool => $value !== null
        );
    }

    private function assertTransaction(string $message): void
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException($message);
        }
    }
}
