<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table            = 'contact_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'ticket_number',
        'sender_name',
        'sender_email',
        'sender_phone',
        'service_category',
        'subject',
        'message',
        'status',
        'admin_notes',
        'replied_at',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const STATUS_UNREAD        = 'unread';
    public const STATUS_READ          = 'read';
    public const STATUS_IN_PROGRESS   = 'in_progress';
    public const STATUS_WAITING_USER  = 'waiting_user';
    public const STATUS_WAITING_ADMIN = 'waiting_admin';
    public const STATUS_REPLIED       = 'replied';
    public const STATUS_CLOSED        = 'closed';
    public const STATUS_ARCHIVED      = 'archived';

    /**
     * Safely parse admin_notes JSON with fallback for legacy plain text notes.
     */
    public static function parseAdminNotes(?string $raw): array
    {
        $default = [
            'user_id'       => null,
            'priority'      => 'normal', // 'low', 'normal', 'high', 'urgent'
            'assigned_to'   => null,
            'internal_note' => '',
            'threads'       => [],
            'logs'          => [],
        ];

        if (empty($raw)) {
            return $default;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return array_merge($default, $decoded);
        }

        // If legacy plain text was stored
        $default['internal_note'] = (string) $raw;
        return $default;
    }

    /**
     * Encode structured data back into JSON string for admin_notes column.
     */
    public static function encodeAdminNotes(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Append a message to the ticket conversation thread and record tracking log.
     */
    public function appendThread(int $ticketId, array $threadMessage, ?string $newStatus = null, ?int $actorUserId = null): bool
    {
        $ticket = $this->find($ticketId);
        if (! $ticket) {
            return false;
        }

        $notes = self::parseAdminNotes($ticket['admin_notes'] ?? null);

        $now = date('Y-m-d H:i:s');
        $msgId = 'msg_' . time() . '_' . substr(bin2hex(random_bytes(3)), 0, 4);

        $channel    = $threadMessage['channel'] ?? 'portal';
        $senderType = $threadMessage['sender_type'] ?? 'admin'; // 'admin', 'user', 'system'
        $senderName = $threadMessage['sender_name'] ?? 'Petugas Helpdesk';
        $senderId   = $threadMessage['sender_id'] ?? $actorUserId;
        $content    = trim((string) ($threadMessage['message'] ?? ''));

        if ($content === '') {
            return false;
        }

        // Append to threads
        $notes['threads'][] = [
            'id'          => $msgId,
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'sender_id'   => $senderId,
            'channel'     => $channel,
            'message'     => $content,
            'created_at'  => $now,
        ];

        // Determine action description for tracking log
        $actionTitle = match ($channel) {
            'email'    => 'Balasan via Email',
            'whatsapp' => 'Balasan via WhatsApp',
            'internal' => 'Catatan Internal Petugas',
            default    => ($senderType === 'user') ? 'Tanggapan Pemohon' : 'Balasan Helpdesk (Portal)',
        };

        $notes['logs'][] = [
            'time'       => $now,
            'actor'      => $senderName,
            'actor_role' => $senderType,
            'action'     => $actionTitle,
            'detail'     => mb_strimwidth($content, 0, 120, '...'),
        ];

        $updateData = [
            'admin_notes' => self::encodeAdminNotes($notes),
            'updated_at'  => $now,
        ];

        if (! empty($newStatus)) {
            $updateData['status'] = $newStatus;
        }

        // Set replied_at if an official reply is sent to the user
        if (in_array($senderType, ['admin', 'system'], true) && in_array($channel, ['portal', 'email', 'whatsapp'], true)) {
            $updateData['replied_at'] = $now;
        }

        $res = $this->update($ticketId, $updateData);

        // Record audit trail
        try {
            $auditService = new \App\Services\AuditService();
            $auditService->log(
                $actorUserId ?: (int) (session()->get('user_id') ?: 1),
                'contact.ticket.reply',
                'communication',
                'contact_messages',
                $ticketId,
                [
                    'ticket_number' => $ticket['ticket_number'],
                    'sender_type'   => $senderType,
                    'channel'       => $channel,
                    'new_status'    => $newStatus ?? $ticket['status'],
                ]
            );
        } catch (\Throwable) {
        }

        return (bool) $res;
    }

    /**
     * Update ticket lifecycle metadata (status, priority, assignee, internal note).
     */
    public function updateTicketMeta(int $ticketId, array $meta, ?int $actorUserId = null): bool
    {
        $ticket = $this->find($ticketId);
        if (! $ticket) {
            return false;
        }

        $notes = self::parseAdminNotes($ticket['admin_notes'] ?? null);
        $now = date('Y-m-d H:i:s');
        $actorName = (string) (session()->get('name') ?: 'Administrator');

        $statusChanged = false;
        $oldStatus = $ticket['status'];

        if (isset($meta['priority']) && in_array($meta['priority'], ['low', 'normal', 'high', 'urgent'], true)) {
            if ($notes['priority'] !== $meta['priority']) {
                $notes['logs'][] = [
                    'time'       => $now,
                    'actor'      => $actorName,
                    'actor_role' => 'admin',
                    'action'     => 'Prioritas Diubah',
                    'detail'     => "Prioritas diubah dari {$notes['priority']} ke {$meta['priority']}",
                ];
                $notes['priority'] = $meta['priority'];
            }
        }

        if (isset($meta['assigned_to'])) {
            $newAssignee = trim((string) $meta['assigned_to']);
            if ($notes['assigned_to'] !== $newAssignee) {
                $notes['logs'][] = [
                    'time'       => $now,
                    'actor'      => $actorName,
                    'actor_role' => 'admin',
                    'action'     => 'Petugas Ditugaskan',
                    'detail'     => "Tiket ditugaskan kepada: " . ($newAssignee ?: 'Belum ditugaskan'),
                ];
                $notes['assigned_to'] = $newAssignee ?: null;
            }
        }

        if (isset($meta['internal_note']) && $meta['internal_note'] !== '') {
            $notes['internal_note'] = trim((string) $meta['internal_note']);
        }

        $updateData = [
            'admin_notes' => self::encodeAdminNotes($notes),
            'updated_at'  => $now,
        ];

        if (isset($meta['status']) && ! empty($meta['status']) && $meta['status'] !== $oldStatus) {
            $updateData['status'] = $meta['status'];
            $statusChanged = true;

            $notes['logs'][] = [
                'time'       => $now,
                'actor'      => $actorName,
                'actor_role' => 'admin',
                'action'     => 'Status Diperbarui',
                'detail'     => "Status tiket diubah dari '{$oldStatus}' menjadi '{$meta['status']}'",
            ];
            $updateData['admin_notes'] = self::encodeAdminNotes($notes);

            if ($meta['status'] === self::STATUS_REPLIED && empty($ticket['replied_at'])) {
                $updateData['replied_at'] = $now;
            }
        }

        $res = $this->update($ticketId, $updateData);

        try {
            $auditService = new \App\Services\AuditService();
            $auditService->log(
                $actorUserId ?: (int) (session()->get('user_id') ?: 1),
                'contact.ticket.meta_updated',
                'communication',
                'contact_messages',
                $ticketId,
                [
                    'ticket_number' => $ticket['ticket_number'],
                    'old_status'    => $oldStatus,
                    'new_status'    => $meta['status'] ?? $oldStatus,
                    'priority'      => $notes['priority'],
                    'assigned_to'   => $notes['assigned_to'],
                ]
            );
        } catch (\Throwable) {
        }

        return (bool) $res;
    }

    /**
     * Retrieve all tickets for a specific logged-in user by email or user ID.
     */
    public function getUserTickets(string $email, ?int $userId = null): array
    {
        $builder = $this->builder();
        $builder->groupStart();
        $builder->where('sender_email', $email);
        if ($userId) {
            $builder->orLike('admin_notes', '"user_id":' . $userId, 'both');
            $builder->orLike('admin_notes', '"user_id": "' . $userId . '"', 'both');
        }
        $builder->groupEnd();
        $builder->orderBy('created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Find a ticket by its unique ticket number.
     */
    public function findByTicketNumber(string $ticketNumber): ?array
    {
        return $this->where('ticket_number', trim($ticketNumber))->first();
    }
}
