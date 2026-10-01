<?php

namespace App\Services;

use App\Models\AuditLogModel;

class AuditService
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    public function log(?int $userId, string $action, ?string $module = null, ?string $targetType = null, ?int $targetId = null, array $metadata = []): void
    {
        $this->auditLogModel->insert([
            'actor_id'        => $userId,
            'action'          => $action,
            'resource_type'   => $targetType,
            'resource_id'     => $targetId,
            'ip_hash'         => hash('sha256', service('request')->getIPAddress()),
            'user_agent_hash' => hash('sha256', (string) service('request')->getUserAgent()),
            'reason'          => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
            'result'          => str_ends_with($action, '_failed') ? 'failed' : 'success',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }
}
