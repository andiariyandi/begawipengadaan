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
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'ip_address'  => service('request')->getIPAddress(),
            'metadata'    => $metadata ? json_encode($metadata) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
