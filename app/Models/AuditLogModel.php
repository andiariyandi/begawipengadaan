<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'action', 'module', 'target_type', 'target_id', 'ip_address', 'metadata'];

    public function browse(?string $module = null, ?int $userId = null, ?string $from = null, ?string $to = null)
    {
        $builder = $this->select('audit_logs.*, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        if ($module) $builder->where('audit_logs.module', $module);
        if ($userId) $builder->where('audit_logs.user_id', $userId);
        if ($from)   $builder->where('audit_logs.created_at >=', $from . ' 00:00:00');
        if ($to)     $builder->where('audit_logs.created_at <=', $to . ' 23:59:59');

        return $builder;
    }

    public function distinctModules(): array
    {
        return $this->select('module')->distinct()->where('module IS NOT NULL')->findAll();
    }
}
