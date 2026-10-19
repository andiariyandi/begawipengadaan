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

    public function log(
        ?int $userId,
        string $action,
        ?string $module = null,
        ?string $targetType = null,
        $targetId = null,
        $metadata = []
    ): void {
        $beforeSnapshot = null;
        $afterSnapshot  = null;

        if (is_array($targetId)) {
            // Alternative signature: ($userId, $action, $resourceType, $resourceId, $beforeSnapshot, $afterSnapshot)
            $resourceType   = $module ?: $targetType;
            $resourceId     = is_numeric($targetType) ? (int) $targetType : null;
            $beforeSnapshot = json_encode($this->redact($targetId), JSON_UNESCAPED_UNICODE);
            $afterSnapshot  = is_array($metadata) ? json_encode($this->redact($metadata), JSON_UNESCAPED_UNICODE) : null;
            $reasonData     = is_array($metadata) ? $metadata : [];
        } else {
            // Canonical signature: ($userId, $action, $module, $targetType, $targetId, $metadata)
            if (is_numeric($targetType) && $targetId === null) {
                // Handles calls like: log($actorId, 'created', 'team_members', (string) $insertId, null, $data)
                $resourceType = $module;
                $resourceId   = (int) $targetType;
            } else {
                $resourceType = $targetType ?: $module;
                $resourceId   = is_numeric($targetId) ? (int) $targetId : null;
            }

            $metaArray = is_array($metadata) ? $metadata : [];
            if (isset($metaArray['before']) && is_array($metaArray['before'])) {
                $beforeSnapshot = json_encode($this->redact($metaArray['before']), JSON_UNESCAPED_UNICODE);
            }
            if (isset($metaArray['after']) && is_array($metaArray['after'])) {
                $afterSnapshot = json_encode($this->redact($metaArray['after']), JSON_UNESCAPED_UNICODE);
            }
            $reasonData = $metaArray;
        }

        $reason = ! empty($reasonData) ? json_encode($this->redact($reasonData), JSON_UNESCAPED_UNICODE) : null;

        $request = service('request');
        $actorId = null;
        if ($userId !== null && $userId > 0) {
            $userExists = \Config\Database::connect()->table('users')->where('id', $userId)->countAllResults() > 0;
            if ($userExists) {
                $actorId = $userId;
            }
        }

        $this->auditLogModel->insert([
            'actor_id'          => $actorId,
            'action'            => $action,
            'resource_type'     => $resourceType,
            'resource_id'       => $resourceId,
            'result'            => 'success',
            'before_snapshot'   => $beforeSnapshot,
            'after_snapshot'    => $afterSnapshot,
            'reason'            => $reason,
            'ip_hash'           => hash('sha256', $this->safeIp($request)),
            'user_agent_hash'   => hash('sha256', $this->safeUserAgent($request)),
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

    /**
     * Safe IP retrieval. IncomingRequest exposes getIPAddress(); other
     * request types (CLIRequest, etc.) may not. Fall back to 'unknown'.
     */
    private function safeIp($request): string
    {
        try {
            if (method_exists($request, 'getIPAddress')) {
                $ip = $request->getIPAddress();
                return (string) ($ip !== null && $ip !== '' ? $ip : 'unknown');
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return 'unknown';
    }

    /**
     * Safe user agent retrieval. CLIRequest lacks getUserAgent(); fall back.
     */
    private function safeUserAgent($request): string
    {
        try {
            if (method_exists($request, 'getUserAgent')) {
                $ua = $request->getUserAgent();
                return (string) ($ua !== null && $ua !== '' ? $ua : 'unknown');
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return 'unknown';
    }
}
