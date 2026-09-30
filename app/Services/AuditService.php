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
            'actor_user_id' => $userId,
            'action'        => $action,
            'subject_type'  => $targetType,
            'subject_id'    => $targetId,
            'route_name'    => service('router')->getMatchedRoute()[0] ?? null,
            'ip_address'    => service('request')->getIPAddress(),
            'user_agent'    => (string) service('request')->getUserAgent(),
            'context_data'  => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
        ]);
    }
}
