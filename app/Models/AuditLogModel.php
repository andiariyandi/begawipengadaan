<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'actor_id',
        'action',
        'resource_type',
        'resource_id',
        'request_id',
        'ip_hash',
        'user_agent_hash',
        'before_snapshot',
        'after_snapshot',
        'reason',
        'result',
        'created_at',
    ];

    public function browse(?string $module = null, ?int $userId = null, ?string $from = null, ?string $to = null)
    {
        $builder = $this->select('audit_logs.*, users.username')
            ->join('users', 'users.id = audit_logs.actor_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        if ($module) $builder->where('audit_logs.resource_type', $module);
        if ($userId) $builder->where('audit_logs.actor_id', $userId);
        if ($from)   $builder->where('audit_logs.created_at >=', $from . ' 00:00:00');
        if ($to)     $builder->where('audit_logs.created_at <=', $to . ' 23:59:59');

        return $builder;
    }

    public function distinctModules(): array
    {
        return $this->select('resource_type')->distinct()->where('resource_type IS NOT NULL')->findAll();
    }
}
