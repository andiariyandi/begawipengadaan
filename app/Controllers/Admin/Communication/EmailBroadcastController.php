<?php

namespace App\Controllers\Admin\Communication;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\Email\EmailQueueService;
use App\Services\Email\EmailService;
use App\Services\Email\EmailTemplateService;

/**
 * Admin Controller for Manual Email Hub: Broadcast, Send-to-One, Scheduled Queue, and Logs.
 */
class EmailBroadcastController extends BaseController
{
    private EmailService $emailService;
    private EmailQueueService $queueService;
    private EmailTemplateService $templateService;
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->emailService    = new EmailService();
        $this->queueService    = new EmailQueueService();
        $this->templateService = new EmailTemplateService();
        $this->auditService    = new AuditService();
        $this->db              = \Config\Database::connect();
    }

    /**
     * Main Email Hub Interface.
     */
    public function index()
    {
        $activeTab = (string) ($this->request->getGet('tab') ?: 'broadcast');
        if (! in_array($activeTab, ['broadcast', 'send_one', 'queue', 'logs'], true)) {
            $activeTab = 'broadcast';
        }

        // Fetch Queue items
        $queueItems = $this->queueService->getQueue(100);

        // Fetch Delivery Logs
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filters = [
            'q'      => trim((string) $this->request->getGet('q')),
            'status' => trim((string) $this->request->getGet('status')),
        ];
        $logsData = $this->queueService->getDeliveryLogs($filters, 15, $page);

        // Setup Pager
        $pager = service('pager');
        $pager->store('email_logs', $page, 15, $logsData['total'], 0);

        // Fetch learning classes for target dropdown
        $classes = $this->db->table('learning_classes lc')
            ->select('lc.id, lc.name, lc.code, c.title as course_title')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('lc.deleted_at IS NULL', null, false)
            ->orderBy('lc.id', 'DESC')
            ->get()->getResultArray();

        // Fetch events for target dropdown
        $events = $this->db->table('events')
            ->select('id, title, start_at')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('id', 'DESC')
            ->limit(30)
            ->get()->getResultArray();

        $config = $this->emailService->getConfig();

        // Count pending vs sent
        $totalPending = count(array_filter($queueItems, fn($i) => empty($i['sent_at'])));
        $totalReady   = count(array_filter($queueItems, fn($i) => empty($i['sent_at']) && $i['is_due']));

        return view('dashboard/communication/email/index', [
            'title'         => 'Pusat Siaran Email & Antrean | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pusat Siaran Email & Komunikasi',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'email',
            'activeTab'     => $activeTab,
            'config'        => $config,
            'queueItems'    => $queueItems,
            'logs'          => $logsData['items'],
            'totalLogs'     => $logsData['total'],
            'pager'         => $pager,
            'filters'       => $filters,
            'classes'       => $classes,
            'events'        => $events,
            'totalPending'  => $totalPending,
            'totalReady'    => $totalReady,
        ]);
    }

    /**
     * Process manual broadcast creation & dispatch.
     */
    public function sendBroadcast()
    {
        $rules = [
            'subject'      => 'required|min_length[3]|max_length[255]',
            'body_message' => 'required|min_length[10]',
            'target_group' => 'required|in_list[all,asn,provider,academic,public,newsletter,class,event]',
            'send_mode'    => 'required|in_list[now,schedule]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('dashboard/komunikasi/email?tab=broadcast'))
                ->withInput()
                ->with('error', 'Validasi gagal: Harap lengkapi semua bidang wajib dengan benar.');
        }

        $subject     = trim((string) $this->request->getPost('subject'));
        $bodyMessage = (string) $this->request->getPost('body_message');
        $targetGroup = (string) $this->request->getPost('target_group');
        $sendMode    = (string) $this->request->getPost('send_mode');
        $actionUrl   = trim((string) $this->request->getPost('action_url'));
        $actionText  = trim((string) $this->request->getPost('action_text'));

        $targetId = null;
        if ($targetGroup === 'class') {
            $targetId = (int) $this->request->getPost('class_id');
        } elseif ($targetGroup === 'event') {
            $targetId = (int) $this->request->getPost('event_id');
        }

        $scheduledAt = null;
        if ($sendMode === 'schedule') {
            $scheduledDate = trim((string) $this->request->getPost('scheduled_date'));
            $scheduledTime = trim((string) $this->request->getPost('scheduled_time'));
            if (! empty($scheduledDate)) {
                $timeStr = ! empty($scheduledTime) ? $scheduledTime : '08:00';
                $scheduledAt = date('Y-m-d H:i:s', strtotime("{$scheduledDate} {$timeStr}:00"));
            }
        }

        $actorId = (int) (session()->get('user_id') ?: 1);

        $options = [
            'action_url'  => $actionUrl ?: null,
            'action_text' => $actionText ?: null,
        ];

        $enqueueRes = $this->queueService->enqueueBroadcast(
            $subject,
            $bodyMessage,
            $targetGroup,
            $targetId,
            $scheduledAt,
            $actorId,
            $options
        );

        if (! $enqueueRes['success']) {
            return redirect()->to(site_url('dashboard/komunikasi/email?tab=broadcast'))
                ->withInput()
                ->with('error', $enqueueRes['message']);
        }

        $count = $enqueueRes['count'];

        // If Send Mode is 'now', immediately process the batch manually
        if ($sendMode === 'now') {
            $batchResult = $this->queueService->processReadyBatch(50);
            $msg = "Siaran email berhasil didaftarkan ({$count} penerima). Telah diproses langsung: {$batchResult['success']} terkirim, {$batchResult['failed']} gagal.";
            if ($batchResult['processed'] < $count) {
                $rem = $count - $batchResult['processed'];
                $msg .= " Sisanya ({$rem} email) tersimpan di antrean untuk diproses bertahap.";
            }

            return redirect()->to(site_url('dashboard/komunikasi/email?tab=queue'))
                ->with('success', $msg);
        }

        // If scheduled
        $formattedDate = date('d F Y, H:i', strtotime((string) $scheduledAt)) . ' WIB';
        return redirect()->to(site_url('dashboard/komunikasi/email?tab=queue'))
            ->with('success', "Draf siaran email berhasil dijadwalkan untuk {$count} penerima pada {$formattedDate}. Anda dapat mengeksekusi pengiriman kapan saja melalui tombol manual di tab Antrean.");
    }

    /**
     * Send personal email (Send to One).
     */
    public function sendOne()
    {
        $rules = [
            'recipient_email' => 'required|valid_email|max_length[150]',
            'recipient_name'  => 'required|min_length[2]|max_length[150]',
            'subject'         => 'required|min_length[3]|max_length[255]',
            'message'         => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('dashboard/komunikasi/email?tab=send_one'))
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(' ', $this->validator->getErrors()));
        }

        $toEmail = strtolower(trim((string) $this->request->getPost('recipient_email')));
        $toName  = trim((string) $this->request->getPost('recipient_name'));
        $subject = trim((string) $this->request->getPost('subject'));
        $message = (string) $this->request->getPost('message');

        $actionUrl  = trim((string) $this->request->getPost('action_url'));
        $actionText = trim((string) $this->request->getPost('action_text'));

        $options = [
            'action_url'  => $actionUrl ?: null,
            'action_text' => $actionText ?: null,
        ];

        // Format and send via EmailService
        $html = $this->templateService->renderPersonal($toName, $subject, $message, $options);
        $result = $this->emailService->sendDirect($toEmail, $subject, $html);

        $now = date('Y-m-d H:i:s');
        $actorId = (int) (session()->get('user_id') ?: 13);

        // Record in notifications table as log
        $this->db->table('notifications')->insert([
            'user_id'    => $actorId,
            'channel'    => 'email',
            'type'       => 'personal',
            'title'      => substr($subject, 0, 255),
            'body'       => substr(strip_tags($message), 0, 500),
            'data'       => json_encode([
                'recipient_email' => $toEmail,
                'recipient_name'  => $toName,
                'body_html'       => $html,
                'status'          => $result['success'] ? 'sent' : 'failed',
                'attempts'        => 1,
                'last_error'      => $result['success'] ? null : $result['message'],
            ], JSON_UNESCAPED_UNICODE),
            'sent_at'    => $result['success'] ? $now : null,
            'created_at' => $now,
        ]);

        try {
            $this->auditService->log(
                $actorId,
                'email.sent_personal',
                'notifications',
                $toEmail,
                $result['success'] ? 1 : 0,
                [
                    'recipient' => $toEmail,
                    'subject'   => $subject,
                    'result'    => $result['success'] ? 'success' : 'failed',
                ]
            );
        } catch (\Throwable) {
        }

        if ($result['success']) {
            return redirect()->to(site_url('dashboard/komunikasi/email?tab=logs'))
                ->with('success', "Email personal berhasil dikirimkan ke {$toEmail}.");
        }

        return redirect()->to(site_url('dashboard/komunikasi/email?tab=send_one'))
            ->withInput()
            ->with('error', "Gagal mengirimkan email: {$result['message']}");
    }

    /**
     * Dispatch 1 single item manually from the queue.
     */
    public function dispatchItem(int $id)
    {
        $res = $this->queueService->processItem($id);
        if ($res['success']) {
            return redirect()->back()->with('success', $res['message']);
        }
        return redirect()->back()->with('error', $res['message']);
    }

    /**
     * Dispatch all ready / due emails in queue (manual batch).
     */
    public function dispatchReady()
    {
        $res = $this->queueService->processReadyBatch(50);
        $msg = "Pemrosesan batch selesai: {$res['success']} berhasil terkirim, {$res['failed']} gagal dari {$res['processed']} email.";
        if (! empty($res['errors'])) {
            $msg .= ' Beberapa kendala: ' . implode(', ', array_slice($res['errors'], 0, 2));
        }

        return redirect()->to(site_url('dashboard/komunikasi/email?tab=queue'))
            ->with($res['failed'] > 0 ? 'warning' : 'success', $msg);
    }

    /**
     * Retry sending a failed email.
     */
    public function retry(int $id)
    {
        $res = $this->queueService->retry($id);
        if ($res['success']) {
            return redirect()->back()->with('success', $res['message']);
        }
        return redirect()->back()->with('error', $res['message']);
    }

    /**
     * Delete an item from queue or logs.
     */
    public function delete(int $id)
    {
        $this->queueService->delete($id);
        return redirect()->back()->with('success', 'Data antrean email berhasil dihapus.');
    }

    /**
     * AJAX endpoint to return recipient count for an audience segment.
     */
    public function countAudience()
    {
        $targetGroup = (string) $this->request->getGet('group');
        $targetId    = (int) $this->request->getGet('target_id');

        $count = $this->queueService->countAudience($targetGroup, $targetId);

        return $this->response->setJSON([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * AJAX endpoint to preview email HTML with layout.
     */
    public function preview()
    {
        $subject = (string) ($this->request->getPost('subject') ?: 'Judul Subjek Pratinjau');
        $body    = (string) ($this->request->getPost('body') ?: '<p>Ini adalah contoh tampilan pesan siaran email resmi BegawiPengadaan.</p>');
        $actionUrl  = (string) $this->request->getPost('action_url');
        $actionText = (string) $this->request->getPost('action_text');

        $html = $this->templateService->renderBroadcast($subject, $body, [
            'action_url'  => $actionUrl ?: null,
            'action_text' => $actionText ?: null,
        ]);

        return $this->response->setBody($html);
    }
}
