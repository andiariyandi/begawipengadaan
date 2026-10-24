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
        'result',
        'before_snapshot',
        'after_snapshot',
        'reason',
        'ip_hash',
        'user_agent_hash',
        'request_id',
        'created_at',
    ];

    public function browse(
        ?string $module = null,
        ?int $userId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $keyword = null,
        ?string $result = null,
        ?string $sort = 'created_at',
        ?string $dir = 'desc',
        ?string $action = null
    ) {
        $builder = $this->select('audit_logs.*, users.username, users.name as actor_name, users.email as actor_email')
            ->join('users', 'users.id = audit_logs.actor_id', 'left');

        if ($module !== null && $module !== '') {
            $builder->where('audit_logs.resource_type', $module);
        }

        if ($userId !== null && $userId > 0) {
            $builder->where('audit_logs.actor_id', $userId);
        }

        if ($result !== null && $result !== '') {
            $builder->where('audit_logs.result', $result);
        }

        if ($action !== null && $action !== '') {
            $builder->where('audit_logs.action', $action);
        }

        if ($from !== null && $from !== '') {
            $builder->where('audit_logs.created_at >=', $from . ' 00:00:00');
        }

        if ($to !== null && $to !== '') {
            $builder->where('audit_logs.created_at <=', $to . ' 23:59:59');
        }

        if ($keyword !== null && trim($keyword) !== '') {
            $kw = trim($keyword);
            $builder->groupStart()
                ->like('users.name', $kw)
                ->orLike('users.username', $kw)
                ->orLike('users.email', $kw)
                ->orLike('audit_logs.action', $kw)
                ->orLike('audit_logs.resource_type', $kw)
                ->orLike('audit_logs.resource_id', $kw)
                ->orLike('audit_logs.reason', $kw)
                ->groupEnd();
        }

        $allowedSorts = [
            'created_at'    => 'audit_logs.created_at',
            'actor'         => 'users.name',
            'action'        => 'audit_logs.action',
            'resource_type' => 'audit_logs.resource_type',
            'result'        => 'audit_logs.result',
        ];
        $direction = strtolower((string) ($dir ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

        if (($sort ?? '') === 'actor') {
            $builder->orderBy('COALESCE(NULLIF(users.name, ""), users.username, "Sistem / Anonim")', $direction, false);
        } else {
            $orderCol = $allowedSorts[$sort ?? ''] ?? 'audit_logs.created_at';
            $builder->orderBy($orderCol, $direction);
        }
        $builder->orderBy('audit_logs.id', $direction);

        return $builder;
    }

    public function distinctModules(): array
    {
        return $this->select('resource_type')
            ->distinct()
            ->where('resource_type IS NOT NULL')
            ->findAll();
    }
}
