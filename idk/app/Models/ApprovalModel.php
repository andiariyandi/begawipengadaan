<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model
{
    protected $table         = 'approvals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['approvable_type', 'approvable_id', 'requested_by', 'current_status'];

    public function findPendingByType(string $type): array
    {
        return $this->where('approvable_type', $type)
            ->where('current_status', 'pending')
            ->findAll();
    }

    public function findByApprovable(string $type, int $id): ?array
    {
        return $this->where('approvable_type', $type)
            ->where('approvable_id', $id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function hasActiveApproval(string $type, int $id): bool
    {
        return (bool) $this->where('approvable_type', $type)
            ->where('approvable_id', $id)
            ->whereIn('current_status', ['pending', 'approved'])
            ->first();
    }
}
