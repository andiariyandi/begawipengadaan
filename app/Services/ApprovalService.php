<?php

namespace App\Services;

use App\Models\ApprovalModel;
use App\Models\ApprovalHistoryModel;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;
use Throwable;

class ApprovalService
{
    protected ApprovalModel $approvalModel;
    protected ApprovalHistoryModel $historyModel;
    protected AuditService $auditService;
    protected ConnectionInterface $db;

    public function __construct()
    {
        $this->approvalModel = new ApprovalModel();
        $this->historyModel  = new ApprovalHistoryModel();
        $this->auditService  = new AuditService();
        $this->db            = db_connect();
    }

    public function createApproval(string $type, int $approvableId, int $requestedBy): int
    {
        return (int) $this->approvalModel->insert([
            'approvable_type' => $type,
            'approvable_id'   => $approvableId,
            'requested_by'    => $requestedBy,
            'current_status'  => 'pending',
        ], true);
    }

    /**
     * @param callable $onApprove menerima (int $approvableId), dijalankan setelah status tercatat 'approved'.
     */
    public function approve(string $type, int $approvableId, int $actedBy, ?string $notes, callable $onApprove): void
    {
        $approval = $this->approvalModel->findByApprovable($type, $approvableId);

        if (! $approval || $approval['current_status'] !== 'pending') {
            throw new RuntimeException('Approval tidak ditemukan atau sudah diproses.');
        }

        $this->db->transBegin();

        try {
            $this->approvalModel->update($approval['id'], ['current_status' => 'approved']);
            $this->historyModel->insert([
                'approval_id' => $approval['id'],
                'acted_by'    => $actedBy,
                'action'      => 'approve',
                'notes'       => $notes,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            $onApprove($approvableId);
            $this->auditService->log($actedBy, 'approve', 'approval', $type, $approvableId);

            if (! $this->db->transStatus()) {
                throw new RuntimeException('Approval gagal disimpan.');
            }

            $this->db->transCommit();
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * @param callable $onReject menerima (int $approvableId), dijalankan setelah status tercatat 'rejected'.
     */
    public function reject(string $type, int $approvableId, int $actedBy, ?string $notes, callable $onReject): void
    {
        $approval = $this->approvalModel->findByApprovable($type, $approvableId);

        if (! $approval || $approval['current_status'] !== 'pending') {
            throw new RuntimeException('Approval tidak ditemukan atau sudah diproses.');
        }

        $this->db->transBegin();

        try {
            $this->approvalModel->update($approval['id'], ['current_status' => 'rejected']);
            $this->historyModel->insert([
                'approval_id' => $approval['id'],
                'acted_by'    => $actedBy,
                'action'      => 'reject',
                'notes'       => $notes,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            $onReject($approvableId);
            $this->auditService->log($actedBy, 'reject', 'approval', $type, $approvableId);

            if (! $this->db->transStatus()) {
                throw new RuntimeException('Approval gagal disimpan.');
            }

            $this->db->transCommit();
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
