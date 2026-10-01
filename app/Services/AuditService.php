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

        $request = service('request');

        $this->auditLogModel->insert([
            'actor_id'          => $userId,
            'action'            => $action,
            'resource_type'     => $targetType,
            'resource_id'       => $targetId,
            'result'            => 'success',
            'before_snapshot'   => null,
            'after_snapshot'    => null,
            'reason'            => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
            'ip_hash'           => hash('sha256', $request->getIPAddress()),
            'user_agent_hash'   => hash('sha256', (string) $request->getUserAgent()),
            'request_id'        => null,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }
    private function redact(array $metadata): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'password_hash',
            'token',
            'plain_token',
            'session_id',
            'secret',
            'credential',
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
