<?php

namespace App\Services\Auth;

use App\Models\AuditLogModel;
use App\Models\RoleRequestModel;
use App\Models\UserRoleModel;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;
use Throwable;

class RoleRequestService
{
    private RoleRequestModel $requestModel;
    private UserRoleModel $userRoleModel;
    private AuditLogModel $auditLogModel;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->requestModel = new RoleRequestModel();
        $this->userRoleModel = new UserRoleModel();
        $this->auditLogModel = new AuditLogModel();
        $this->db = db_connect();
    }

    public function review(int $requestId, int $reviewedBy, string $status, ?string $notes = null): bool
    {
        if ($status === 'approved') {
            return $this->approve($requestId, $reviewedBy, $notes);
        }
        if ($status === 'rejected') {
            return $this->reject($requestId, $reviewedBy, $notes ?? 'Permohonan ditolak.');
        }
        if ($status === 'revision_required') {
            return $this->revision($requestId, $reviewedBy, $notes ?? 'Permohonan memerlukan revisi.');
        }
        throw new RuntimeException('Status review role tidak valid.');
    }

    public function approve(int $requestId, int $reviewedBy, ?string $notes = null): bool
    {
        $request = $this->getPendingRequest($requestId);
        $this->db->transBegin();
        try {
            $now = date('Y-m-d H:i:s');
            $this->userRoleModel->assignRole((int) $request['user_id'], (int) $request['requested_role_id'], $reviewedBy, $notes);
            $this->requestModel->update($requestId, [
                'status' => 'approved', 'reviewed_by' => $reviewedBy,
                'reviewed_at' => $now, 'review_notes' => $notes,
            ]);
            $this->audit($requestId, $reviewedBy, 'approved', $notes, $now);
            $this->assertTransaction();
            $this->db->transCommit();
            return true;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function reject(int $requestId, int $reviewedBy, string $reason): bool
    {
        return $this->setReviewStatus($requestId, $reviewedBy, 'rejected', $reason);
    }

    public function revision(int $requestId, int $reviewedBy, string $reason): bool
    {
        return $this->setReviewStatus($requestId, $reviewedBy, 'revision_required', $reason);
    }

    private function setReviewStatus(int $requestId, int $reviewedBy, string $status, string $notes): bool
    {
        $this->getPendingRequest($requestId);
        $this->db->transBegin();
        try {
            $now = date('Y-m-d H:i:s');
            $this->requestModel->update($requestId, [
                'status' => $status, 'reviewed_by' => $reviewedBy,
                'reviewed_at' => $now, 'review_notes' => $notes,
            ]);
            $this->audit($requestId, $reviewedBy, $status, $notes, $now);
            $this->assertTransaction();
            $this->db->transCommit();
            return true;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function getPendingRequest(int $requestId): array
    {
        $request = $this->requestModel->find($requestId);
        if (! $request || ! in_array($request['status'], ['submitted', 'in_review'], true)) {
            throw new RuntimeException('Permohonan role tidak ditemukan atau sudah diproses.');
        }
        return $request;
    }

    private function audit(int $requestId, int $actorId, string $status, ?string $reason, string $now): void
    {
        $this->auditLogModel->insert([
            'actor_id' => $actorId, 'action' => 'role_request_' . $status,
            'resource_type' => 'role_request', 'resource_id' => $requestId,
            'reason' => $reason, 'result' => 'success', 'created_at' => $now,
        ]);
    }

    private function assertTransaction(): void
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException('Permohonan role gagal disimpan.');
        }
    }
}
