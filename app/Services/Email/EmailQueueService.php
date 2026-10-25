<?php

namespace App\Services\Email;

use App\Services\AuditService;
use CodeIgniter\Database\BaseConnection;

/**
 * Service to manage Email Queue, Scheduled Drafts, and Logs
 * utilizing the existing `notifications` table (Zero Migration).
 */
class EmailQueueService
{
    private BaseConnection $db;
    private EmailService $emailService;
    private EmailTemplateService $templateService;
    private AuditService $auditService;

    public function __construct(
        ?BaseConnection $db = null,
        ?EmailService $emailService = null,
        ?EmailTemplateService $templateService = null,
        ?AuditService $auditService = null
    ) {
        $this->db              = $db ?? db_connect();
        $this->emailService    = $emailService ?? new EmailService();
        $this->templateService = $templateService ?? new EmailTemplateService();
        $this->auditService    = $auditService ?? new AuditService();
    }

    /**
     * Enqueue a single email for manual dispatch or scheduled release.
     */
    public function enqueue(
        string $type,
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $bodyHtml,
        ?string $scheduledAt = null,
        ?int $userId = null,
        array $options = []
    ): int {
        $now = date('Y-m-d H:i:s');
        $isScheduled = (! empty($scheduledAt) && strtotime($scheduledAt) > time());
        $status = $isScheduled ? 'scheduled' : 'pending';

        $payload = [
            'recipient_email' => trim($recipientEmail),
            'recipient_name'  => trim($recipientName ?: 'Bapak/Ibu'),
            'body_html'       => $bodyHtml,
            'status'          => $status,
            'scheduled_at'    => $scheduledAt ? date('Y-m-d H:i:s', strtotime($scheduledAt)) : null,
            'attempts'        => 0,
            'max_attempts'    => 3,
            'last_error'      => null,
            'action_url'      => $options['action_url'] ?? null,
            'action_text'     => $options['action_text'] ?? null,
            'audience_label'  => $options['audience_label'] ?? null,
            'batch_id'        => $options['batch_id'] ?? null,
        ];

        $summary = substr(strip_tags($bodyHtml), 0, 500);
        $defaultAdminId = (int) (session()->get('user_id') ?: 13);
        $targetUserId = ($userId && $userId > 0) ? $userId : $defaultAdminId;

        $this->db->table('notifications')->insert([
            'user_id'    => $targetUserId,
            'channel'    => 'email',
            'type'       => $type,
            'title'      => substr($subject, 0, 255),
            'body'       => $summary,
            'data'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'read_at'    => null,
            'sent_at'    => null,
            'created_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * Enqueue broadcast email to a target audience segment.
     */
    public function enqueueBroadcast(
        string $subject,
        string $bodyHtml,
        string $targetGroup,
        ?int $targetId = null,
        ?string $scheduledAt = null,
        int $actorId = 1,
        array $options = []
    ): array {
        $recipients = $this->resolveAudienceRecipients($targetGroup, $targetId);

        if (empty($recipients)) {
            return [
                'success' => false,
                'count'   => 0,
                'message' => 'Tidak ditemukan calon penerima aktif untuk kelompok audiens yang dipilih.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        $batchId = 'BC-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        $isScheduled = (! empty($scheduledAt) && strtotime($scheduledAt) > time());
        $status = $isScheduled ? 'scheduled' : 'pending';

        $options['batch_id'] = $batchId;
        $options['audience_label'] = $this->getAudienceLabel($targetGroup, $targetId);

        // Wrap with official template
        $fullHtml = $this->templateService->renderBroadcast($subject, $bodyHtml, $options);
        $summary = substr(strip_tags($bodyHtml), 0, 500);

        $batch = [];
        $total = 0;

        foreach ($recipients as $rec) {
            $payload = [
                'recipient_email' => $rec['email'],
                'recipient_name'  => $rec['name'],
                'body_html'       => $fullHtml,
                'status'          => $status,
                'scheduled_at'    => $scheduledAt ? date('Y-m-d H:i:s', strtotime($scheduledAt)) : null,
                'attempts'        => 0,
                'max_attempts'    => 3,
                'last_error'      => null,
                'action_url'      => $options['action_url'] ?? null,
                'action_text'     => $options['action_text'] ?? null,
                'audience_label'  => $options['audience_label'],
                'batch_id'        => $batchId,
            ];

            $batch[] = [
                'user_id'    => ! empty($rec['user_id']) ? (int) $rec['user_id'] : (int) ($actorId ?: 13),
                'channel'    => 'email',
                'type'       => 'broadcast',
                'title'      => substr($subject, 0, 255),
                'body'       => $summary,
                'data'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'read_at'    => null,
                'sent_at'    => null,
                'created_at' => $now,
            ];

            if (count($batch) >= 100) {
                $this->db->table('notifications')->insertBatch($batch);
                $total += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            $this->db->table('notifications')->insertBatch($batch);
            $total += count($batch);
        }

        try {
            $this->auditService->log(
                $actorId,
                'email.broadcast_enqueued',
                'notifications',
                $batchId,
                $total,
                [
                    'subject'      => $subject,
                    'target_group' => $targetGroup,
                    'target_id'    => $targetId,
                    'count'        => $total,
                    'status'       => $status,
                    'scheduled_at' => $scheduledAt,
                ]
            );
        } catch (\Throwable) {
        }

        return [
            'success'  => true,
            'count'    => $total,
            'batch_id' => $batchId,
            'status'   => $status,
            'message'  => "Berhasil mendaftarkan {$total} calon penerima siaran ke antrean email.",
        ];
    }

    /**
     * Resolve target audience into a list of valid recipients.
     */
    public function resolveAudienceRecipients(string $targetGroup, ?int $targetId = null): array
    {
        $recipients = [];

        if ($targetGroup === 'newsletter') {
            $rows = $this->db->table('newsletter_subscribers')
                ->select('email')
                ->where('status', 'subscribed')
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();

            foreach ($rows as $r) {
                if (filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'user_id' => null,
                        'name'    => 'Pelanggan Newsletter',
                        'email'   => strtolower(trim($r['email'])),
                    ];
                }
            }
            return $recipients;
        }

        if ($targetGroup === 'class' && $targetId > 0) {
            $rows = $this->db->table('enrollments e')
                ->select('u.id as user_id, u.name, u.email')
                ->join('users u', 'u.id = e.user_id')
                ->where('e.class_id', $targetId)
                ->whereIn('e.status', ['active', 'completed'])
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->get()->getResultArray();

            foreach ($rows as $r) {
                if (filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'user_id' => (int) $r['user_id'],
                        'name'    => $r['name'],
                        'email'   => strtolower(trim($r['email'])),
                    ];
                }
            }
            return $recipients;
        }

        if ($targetGroup === 'event' && $targetId > 0) {
            $rows = $this->db->table('event_registrations er')
                ->select('u.id as user_id, u.name, u.email')
                ->join('users u', 'u.id = er.user_id')
                ->where('er.event_id', $targetId)
                ->whereIn('er.status', ['registered', 'waitlisted'])
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->get()->getResultArray();

            foreach ($rows as $r) {
                if (filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'user_id' => (int) $r['user_id'],
                        'name'    => $r['name'],
                        'email'   => strtolower(trim($r['email'])),
                    ];
                }
            }
            return $recipients;
        }

        // Target users by profile type or all
        $builder = $this->db->table('users')
            ->select('id as user_id, name, email')
            ->where('account_status', 'active')
            ->where('deleted_at IS NULL', null, false);

        if (in_array($targetGroup, ['asn', 'provider', 'academic', 'public'], true)) {
            $builder->where('profile_type', $targetGroup);
        }

        $rows = $builder->orderBy('id', 'ASC')->get()->getResultArray();

        foreach ($rows as $r) {
            if (filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
                $recipients[] = [
                    'user_id' => (int) $r['user_id'],
                    'name'    => $r['name'],
                    'email'   => strtolower(trim($r['email'])),
                ];
            }
        }

        return $recipients;
    }

    /**
     * Count recipients without loading full objects.
     */
    public function countAudience(string $targetGroup, ?int $targetId = null): int
    {
        if ($targetGroup === 'newsletter') {
            return (int) $this->db->table('newsletter_subscribers')->where('status', 'subscribed')->countAllResults();
        }
        if ($targetGroup === 'class' && $targetId > 0) {
            return (int) $this->db->table('enrollments e')
                ->join('users u', 'u.id = e.user_id')
                ->where('e.class_id', $targetId)
                ->whereIn('e.status', ['active', 'completed'])
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->countAllResults();
        }
        if ($targetGroup === 'event' && $targetId > 0) {
            return (int) $this->db->table('event_registrations er')
                ->join('users u', 'u.id = er.user_id')
                ->where('er.event_id', $targetId)
                ->whereIn('er.status', ['registered', 'waitlisted'])
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->countAllResults();
        }

        $b = $this->db->table('users')->where('account_status', 'active')->where('deleted_at IS NULL', null, false);
        if (in_array($targetGroup, ['asn', 'provider', 'academic', 'public'], true)) {
            $b->where('profile_type', $targetGroup);
        }
        return (int) $b->countAllResults();
    }

    /**
     * Retrieve scheduled and pending email queue.
     */
    public function getQueue(int $limit = 100): array
    {
        $rows = $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email')
            ->join('users', 'users.id = notifications.user_id', 'left')
            ->where('notifications.channel', 'email')
            ->where('notifications.sent_at IS NULL', null, false)
            ->orderBy('notifications.id', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();

        $items = [];
        $now = time();

        foreach ($rows as $r) {
            $d = json_decode((string) ($r['data'] ?? '{}'), true) ?: [];
            $scheduledAt = ! empty($d['scheduled_at']) ? $d['scheduled_at'] : null;
            $isDue = ($scheduledAt === null || strtotime($scheduledAt) <= $now);

            $r['recipient_email'] = $d['recipient_email'] ?? $r['user_email'] ?? '-';
            $r['recipient_name']  = $d['recipient_name'] ?? $r['user_name'] ?? '-';
            $r['queue_status']    = $d['status'] ?? 'pending';
            $r['scheduled_at']    = $scheduledAt;
            $r['is_due']          = $isDue;
            $r['attempts']        = (int) ($d['attempts'] ?? 0);
            $r['last_error']      = $d['last_error'] ?? null;
            $r['audience_label']  = $d['audience_label'] ?? null;
            $r['batch_id']        = $d['batch_id'] ?? null;

            $items[] = $r;
        }

        return $items;
    }

    /**
     * Retrieve delivery history / logs.
     */
    public function getDeliveryLogs(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $builder = $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email')
            ->join('users', 'users.id = notifications.user_id', 'left')
            ->where('notifications.channel', 'email');

        if (! empty($filters['q'])) {
            $q = trim($filters['q']);
            $builder->groupStart()
                ->like('notifications.title', $q)
                ->orLike('notifications.body', $q)
                ->orLike('users.name', $q)
                ->orLike('users.email', $q)
                ->groupEnd();
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'sent') {
                $builder->where('notifications.sent_at IS NOT NULL', null, false);
            } elseif ($filters['status'] === 'pending') {
                $builder->where('notifications.sent_at IS NULL', null, false);
            }
        }

        $totalBuilder = clone $builder;
        $total = (int) $totalBuilder->countAllResults();

        $offset = ($page - 1) * $perPage;
        $rows = $builder->orderBy('notifications.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        $items = [];
        foreach ($rows as $r) {
            $d = json_decode((string) ($r['data'] ?? '{}'), true) ?: [];
            $r['recipient_email'] = $d['recipient_email'] ?? $r['user_email'] ?? '-';
            $r['recipient_name']  = $d['recipient_name'] ?? $r['user_name'] ?? '-';
            $r['queue_status']    = (! empty($r['sent_at'])) ? 'sent' : ($d['status'] ?? 'pending');
            $r['scheduled_at']    = $d['scheduled_at'] ?? null;
            $r['attempts']        = (int) ($d['attempts'] ?? 0);
            $r['last_error']      = $d['last_error'] ?? null;
            $r['audience_label']  = $d['audience_label'] ?? null;
            $r['batch_id']        = $d['batch_id'] ?? null;

            $items[] = $r;
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Process a single queued email manually (Triggered by Admin).
     */
    public function processItem(int $id): array
    {
        $quota = $this->emailService->getDailyQuotaInfo(500);
        if ($quota['remaining'] <= 0) {
            return [
                'success' => false,
                'message' => 'Batas kuota harian Google Mail (500 email/24 jam) telah tercapai. Pengiriman ditangguhkan demi keamanan akun.',
            ];
        }

        $item = $this->db->table('notifications')
            ->where('id', $id)
            ->where('channel', 'email')
            ->get()->getRowArray();

        if (! $item) {
            return ['success' => false, 'message' => 'Antrean email tidak ditemukan.'];
        }

        $data = json_decode((string) ($item['data'] ?? '{}'), true) ?: [];
        $recipientEmail = $data['recipient_email'] ?? null;
        $bodyHtml = $data['body_html'] ?? $item['body'];

        if (empty($recipientEmail) || ! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $data['status'] = 'failed';
            $data['last_error'] = 'Alamat email penerima tidak valid.';
            $this->db->table('notifications')->where('id', $id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);
            return ['success' => false, 'message' => 'Alamat email penerima tidak valid: ' . $recipientEmail];
        }

        $subject = $item['title'];
        $result = $this->emailService->sendDirect($recipientEmail, $subject, $bodyHtml);

        $now = date('Y-m-d H:i:s');
        $attempts = (int) ($data['attempts'] ?? 0) + 1;
        $data['attempts'] = $attempts;

        if ($result['success']) {
            $data['status'] = 'sent';
            $data['last_error'] = null;

            $this->db->table('notifications')->where('id', $id)->update([
                'sent_at' => $now,
                'data'    => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            return [
                'success' => true,
                'message' => "Email berhasil dikirimkan ke {$recipientEmail}.",
            ];
        }

        $data['status'] = ($attempts >= 3) ? 'failed' : 'pending';
        $data['last_error'] = $result['message'];

        $this->db->table('notifications')->where('id', $id)->update([
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);

        return [
            'success'  => false,
            'message'  => "Gagal mengirim ke {$recipientEmail}: " . $result['message'],
            'debugger' => $result['debugger'] ?? null,
        ];
    }

    /**
     * Process all queued emails that are ready (due) up to limit (Manual Batch Dispatch by Admin).
     */
    public function processReadyBatch(int $limit = 25): array
    {
        $quota = $this->emailService->getDailyQuotaInfo(500);
        if ($quota['remaining'] <= 0) {
            return [
                'processed' => 0,
                'success'   => 0,
                'failed'    => 0,
                'errors'    => ['Batas kuota harian Google Mail (500 email/24 jam) telah tercapai. Pengiriman ditangguhkan demi keamanan akun.'],
                'message'   => 'Batas kuota harian Google Mail tercapai.',
            ];
        }

        $limit = min($limit, $quota['remaining']);
        $now = date('Y-m-d H:i:s');

        // Fetch up to $limit pending or due items
        $rows = $this->db->table('notifications')
            ->select('id, data')
            ->where('channel', 'email')
            ->where('sent_at IS NULL', null, false)
            ->orderBy('id', 'ASC')
            ->limit($limit * 2)
            ->get()->getResultArray();

        $processed = 0;
        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($rows as $r) {
            if ($processed >= $limit) {
                break;
            }

            $d = json_decode((string) ($r['data'] ?? '{}'), true) ?: [];
            $scheduledAt = $d['scheduled_at'] ?? null;

            // Only send if no future schedule or schedule has arrived
            if (! empty($scheduledAt) && strtotime($scheduledAt) > time()) {
                continue;
            }

            $res = $this->processItem((int) $r['id']);
            $processed++;

            if ($res['success']) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = $res['message'];
            }

            // Safe burst pause (0.2s) to protect Gmail rate limits
            usleep(200000);
        }

        return [
            'processed' => $processed,
            'success'   => $successCount,
            'failed'    => $failCount,
            'errors'    => $errors,
        ];
    }

    /**
     * Reset a failed email to pending and retry sending.
     */
    public function retry(int $id): array
    {
        $item = $this->db->table('notifications')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return ['success' => false, 'message' => 'Data tidak ditemukan.'];
        }

        $d = json_decode((string) ($item['data'] ?? '{}'), true) ?: [];
        $d['status'] = 'pending';
        $d['attempts'] = 0;
        $d['last_error'] = null;

        $this->db->table('notifications')->where('id', $id)->update([
            'sent_at' => null,
            'data'    => json_encode($d, JSON_UNESCAPED_UNICODE),
        ]);

        return $this->processItem($id);
    }

    /**
     * Delete an email record from queue/log.
     */
    public function delete(int $id): bool
    {
        return $this->db->table('notifications')
            ->where('id', $id)
            ->where('channel', 'email')
            ->delete();
    }

    /**
     * Return human-friendly audience label.
     */
    private function getAudienceLabel(string $group, ?int $targetId = null): string
    {
        $labels = [
            'all'        => 'Seluruh Pengguna Aktif',
            'asn'        => 'Aparatur Sipil Negara (ASN)',
            'provider'   => 'Penyedia Barang/Jasa',
            'academic'   => 'Akademisi & Mahasiswa',
            'public'     => 'Masyarakat Umum',
            'newsletter' => 'Pelanggan Newsletter',
            'class'      => 'Peserta Kelas ID #' . ($targetId ?: '0'),
            'event'      => 'Peserta Event ID #' . ($targetId ?: '0'),
        ];

        return $labels[$group] ?? 'Kustom';
    }
}
