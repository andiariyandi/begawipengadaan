<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model
{
    protected $table         = 'approvals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['resource_type', 'resource_id', 'requested_action', 'status', 'requested_by', 'reviewed_by', 'reviewed_at', 'reason', 'notes'];

    public function findPendingByType(string $type): array
    {
        return $this->where('resource_type', $type)
            ->where('status', 'pending')
            ->findAll();
    }

    public function findByApprovable(string $type, int $id): ?array
    {
        return $this->where('resource_type', $type)
            ->where('resource_id', $id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function hasActiveApproval(string $type, int $id): bool
    {
        return (bool) $this->where('resource_type', $type)
            ->where('resource_id', $id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
    }
}
