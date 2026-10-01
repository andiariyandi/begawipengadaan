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
        $metadata = $this->redact($metadata);

        $this->auditLogModel->insert([
            'actor_id'      => $userId,
            'action'        => $action,
            'resource_type' => $targetType,
            'resource_id'   => $targetId,
            'result'        => str_ends_with($action, '_failed') ? 'failed' : 'success',
            'reason'        => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
            'ip_hash'       => hash('sha256', (string) service('request')->getIPAddress()),
            'user_agent_hash' => hash('sha256', (string) service('request')->getUserAgent()),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    private function redact(array $metadata): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', 'password_hash',
            'token', 'plain_token', 'session_id', 'secret', 'credential',
        ];

        foreach ($metadata as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $metadata[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $metadata[$key] = $this->redact($value);
            }
        }

        return $metadata;
    }
}
