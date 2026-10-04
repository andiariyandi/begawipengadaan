<?php

namespace App\Services\Workflow;

use App\Services\AuditService;

/**
 * Generic approval workflow service.
 *
 * Used by Admin/ApprovalController and any other controller that needs
 * a pending-decision pattern (approvals table).
 *
 * Note: this is a thin wrapper; the heavy workflow logic for
 * profile_verifications lives in App\Services\Admin\ProfileVerificationService,
 * and for role_requests in App\Services\Admin\RoleRequestService.
 */
class ApprovalService
{
    public function __construct(
        private readonly AuditService $audit = new AuditService(),
    ) {}

    public function queue(array $filters = []): array
    {
        $builder = db_connect()->table('approvals')
            ->orderBy('created_at', 'DESC');

        if (! empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (! empty($filters['resource_type'])) {
            $builder->where('resource_type', $filters['resource_type']);
        }
        return $builder->get()->getResultArray();
    }

    public function history(array $filters = []): array
    {
        $builder = db_connect()->table('approvals')
            ->where('status !=', 'pending')
            ->orderBy('reviewed_at', 'DESC');

        if (! empty($filters['resource_type'])) {
            $builder->where('resource_type', $filters['resource_type']);
        }
        return $builder->get()->getResultArray();
    }

    public function find(int $id): ?array
    {
        return db_connect()->table('approvals')->where('id', $id)->get()->getRowArray();
    }

    public function decide(int $approvalId, string $decision, ?int $actorId = null, ?string $notes = null, ?string $reason = null): bool
    {
        if (! in_array($decision, ['approve', 'reject', 'revision'], true)) {
            throw new \InvalidArgumentException("Keputusan tidak valid: {$decision}");
        }

        $row = $this->find($approvalId);
        if (! $row) {
            return false;
        }

        $newStatus = match ($decision) {
            'approve'  => 'approved',
            'reject'   => 'rejected',
            'revision' => 'revision_required',
        };

        $now = date('Y-m-d H:i:s');

        db_connect()->table('approvals')->where('id', $approvalId)->update([
            'status'      => $newStatus,
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'reason'      => $reason,
            'notes'       => $notes,
            'updated_at'  => $now,
        ]);

        $this->audit->log(
            $actorId,
            "approval.{$decision}",
            'approvals',
            'approval',
            $approvalId,
            ['from' => $row['status'], 'to' => $newStatus],
        );

        return true;
    }
}