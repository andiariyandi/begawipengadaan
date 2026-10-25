<?php

namespace App\Controllers\Admin\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\ContactMessageModel;
use App\Services\AuditService;

class ContactMessageController extends BaseController
{
    protected $helpers = ['text', 'url', 'form'];
    private AuditService $auditService;
    private ContactMessageModel $messageModel;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->messageModel = new ContactMessageModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $status   = trim((string) $this->request->getGet('status'));
        $category = trim((string) $this->request->getGet('category'));
        $search   = trim((string) $this->request->getGet('q'));

        $builder = $this->messageModel->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, ['unread', 'read', 'in_progress', 'waiting_admin', 'waiting_user', 'replied', 'closed', 'archived'], true)) {
            $builder->where('status', $status);
        }

        if ($category !== '') {
            $builder->where('service_category', $category);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('ticket_number', $search)
                ->orLike('sender_name', $search)
                ->orLike('sender_email', $search)
                ->orLike('subject', $search)
                ->orLike('message', $search)
                ->groupEnd();
        }

        $messages = $builder->findAll();

        // Enrich messages with parsed notes & thread count
        foreach ($messages as &$msg) {
            $notes = ContactMessageModel::parseAdminNotes($msg['admin_notes'] ?? null);
            $msg['parsed_notes'] = $notes;
            $msg['priority']     = $notes['priority'] ?? 'normal';
            $msg['assigned_to']  = $notes['assigned_to'] ?? null;
            $msg['threads_count'] = count($notes['threads'] ?? []);

            // Check if sender is a registered user
            $regUser = $this->db->table('users')->select('id, name, profile_type, account_status')->where('email', $msg['sender_email'])->get()->getRowArray();
            $msg['is_registered'] = ! empty($regUser);
            $msg['user_profile']  = $regUser;
        }
        unset($msg);

        // Status counters
        $totalCount        = $this->messageModel->countAllResults(false);
        $unreadCount       = $this->messageModel->where('status', 'unread')->countAllResults(false);
        $inProgressCount   = $this->messageModel->whereIn('status', ['read', 'in_progress', 'waiting_user'])->countAllResults(false);
        $waitingAdminCount = $this->messageModel->where('status', 'waiting_admin')->countAllResults(false);
        $repliedCount      = $this->messageModel->where('status', 'replied')->countAllResults(false);
        $closedCount       = $this->messageModel->where('status', 'closed')->countAllResults(false);

        // Distinct categories for filter dropdown
        $categories = $this->db->table('contact_messages')
            ->select('service_category')
            ->where('service_category IS NOT NULL', null, false)
            ->where('service_category !=', '')
            ->distinct()
            ->get()->getResultArray();

        return view('dashboard/communication/inbox/index', [
            'title'             => 'Pesan Masuk & Ticketing Helpdesk | Dashboard BegawiPengadaan',
            'pageTitle'         => 'Pesan Masuk (Ticketing Helpdesk)',
            'activeMenu'        => 'communication',
            'activeSubmenu'     => 'inbox',
            'messages'          => $messages,
            'currentStatus'     => $status,
            'currentCategory'   => $category,
            'searchQuery'       => $search,
            'totalCount'        => $totalCount,
            'unreadCount'       => $unreadCount,
            'inProgressCount'   => $inProgressCount,
            'waitingAdminCount' => $waitingAdminCount,
            'repliedCount'      => $repliedCount,
            'closedCount'       => $closedCount,
            'categories'        => array_column($categories, 'service_category'),
        ]);
    }

    public function detail($id)
    {
        $message = $this->messageModel->find($id);
        if (! $message) {
            return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('error', 'Pesan / tiket tidak ditemukan.');
        }

        $notes = ContactMessageModel::parseAdminNotes($message['admin_notes'] ?? null);

        // Auto mark as read if currently unread
        if ($message['status'] === 'unread') {
            $notes['logs'][] = [
                'time'       => date('Y-m-d H:i:s'),
                'actor'      => (string) (session()->get('name') ?: 'Administrator'),
                'actor_role' => 'administrator',
                'action'     => 'Tiket Dibuka',
                'detail'     => 'Status tiket otomatis diperbarui menjadi Sudah Dibaca (Read).',
            ];
            $this->messageModel->update($id, [
                'status'      => 'read',
                'admin_notes' => ContactMessageModel::encodeAdminNotes($notes),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $message['status'] = 'read';
            $message['admin_notes'] = ContactMessageModel::encodeAdminNotes($notes);
        }

        // Check if sender is a registered user
        $senderUser = $this->db->table('users')
            ->select('id, name, username, email, phone, profile_type, account_status, created_at')
            ->where('email', $message['sender_email'])
            ->get()->getRowArray();

        // Get administrator / operator staff list for assignment
        $staffUsers = $this->db->table('users')
            ->select('id, name, email, username')
            ->where('account_status', 'active')
            ->where('deleted_at IS NULL', null, false)
            ->get()->getResultArray();

        return view('dashboard/communication/inbox/detail', [
            'title'         => 'Detail Tiket #' . ($message['ticket_number'] ?? $message['id']) . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Tiket Konsultasi #' . ($message['ticket_number'] ?? $message['id']),
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'inbox',
            'message'       => $message,
            'notes'         => $notes,
            'senderUser'    => $senderUser,
            'staffUsers'    => $staffUsers,
        ]);
    }

    public function reply($id)
    {
        $message = $this->messageModel->find($id);
        if (! $message) {
            return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('error', 'Tiket tidak ditemukan.');
        }

        $replyText = trim((string) $this->request->getPost('reply_message'));
        $channel   = trim((string) $this->request->getPost('channel')) ?: 'portal';
        $newStatus = trim((string) $this->request->getPost('new_status')) ?: ContactMessageModel::STATUS_REPLIED;

        if ($replyText === '') {
            return redirect()->back()->with('error', 'Isi pesan balasan tidak boleh kosong.');
        }

        $actorId   = (int) (session()->get('user_id') ?: 1);
        $actorName = (string) (session()->get('name') ?: 'Petugas Helpdesk PBJ');

        $threadMessage = [
            'sender_type' => 'administrator',
            'sender_name' => $actorName,
            'sender_id'   => $actorId,
            'channel'     => $channel, // 'portal', 'email', 'whatsapp', 'internal'
            'message'     => $replyText,
        ];

        // If internal note, status doesn't necessarily change to replied unless requested
        if ($channel === 'internal') {
            $newStatus = null;
        }

        $success = $this->messageModel->appendThread((int) $id, $threadMessage, $newStatus, $actorId);

        if (! $success) {
            return redirect()->back()->with('error', 'Gagal menyimpan balasan pesan.');
        }

        // Kirim notifikasi sistem jika pemohon adalah pengguna terdaftar
        $notes = ContactMessageModel::parseAdminNotes($message['admin_notes'] ?? null);
        if ($channel !== 'internal' && ! empty($notes['user_id'])) {
            try {
                (new \App\Services\NotificationService())->notifyTicketReply(
                    (int) $notes['user_id'],
                    $message['ticket_number'],
                    $actorName,
                    $replyText
                );
            } catch (\Throwable) {
            }
        }

        // WhatsApp redirect handling
        if ($channel === 'whatsapp') {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $message['sender_phone']);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $prefilled = "Halo {$message['sender_name']},\n\nMenindaklanjuti tiket konsultasi *#{$message['ticket_number']}* ({$message['subject']}):\n\n{$replyText}\n\nSalam,\nBiro Pengadaan Barang dan Jasa Provinsi Lampung";
            $waUrl = 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($prefilled);

            return redirect()->to('dashboard/komunikasi/pesan-masuk/detail/' . $id)
                ->with('success', 'Balasan berhasil dicatat di log tiket.')
                ->with('wa_url', $waUrl);
        }

        // Email mailto handling
        if ($channel === 'email') {
            $subject = 'Re: [' . $message['ticket_number'] . '] ' . $message['subject'];
            $mailtoUrl = 'mailto:' . rawurlencode($message['sender_email']) . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($replyText);

            return redirect()->to('dashboard/komunikasi/pesan-masuk/detail/' . $id)
                ->with('success', 'Balasan email berhasil dicatat di thread percakapan tiket.')
                ->with('mailto_url', $mailtoUrl);
        }

        $channelLabel = match ($channel) {
            'internal' => 'Catatan internal berhasil disimpan.',
            default    => 'Balasan berhasil dikirim dan dicatat pada thread percakapan dua arah.',
        };

        return redirect()->to('dashboard/komunikasi/pesan-masuk/detail/' . $id)->with('success', $channelLabel);
    }

    public function updateStatus($id)
    {
        $message = $this->messageModel->find($id);
        if (! $message) {
            return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('error', 'Tiket tidak ditemukan.');
        }

        $newStatus   = trim((string) $this->request->getPost('status'));
        $priority    = trim((string) $this->request->getPost('priority'));
        $assignedTo  = trim((string) $this->request->getPost('assigned_to'));
        $internalNote = trim((string) $this->request->getPost('admin_notes'));

        $meta = [
            'status'        => $newStatus,
            'priority'      => $priority ?: 'normal',
            'assigned_to'   => $assignedTo ?: null,
            'internal_note' => $internalNote,
        ];

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->messageModel->updateTicketMeta((int) $id, $meta, $actorId);

        // Kirim notifikasi jika tiket ditutup/diselesaikan dan pemohon adalah user terdaftar
        $notes = ContactMessageModel::parseAdminNotes($message['admin_notes'] ?? null);
        if (in_array($newStatus, ['closed', 'resolved'], true) && ! empty($notes['user_id'])) {
            try {
                (new \App\Services\NotificationService())->send(
                    (int) $notes['user_id'],
                    'ticket_closed',
                    "Tiket Konsultasi #{$message['ticket_number']} Selesai",
                    "Tiket konsultasi Anda telah diselesaikan dan ditutup oleh petugas helpdesk. Terima kasih atas kerja sama Anda.",
                    [
                        'ticket_number' => $message['ticket_number'],
                        'url'           => site_url('workspace/konsultasi/' . $message['ticket_number']),
                    ]
                );
            } catch (\Throwable $e) {
                log_message('warning', 'Ticket closed notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->to('dashboard/komunikasi/pesan-masuk/detail/' . $id)
            ->with('success', 'Pengaturan tiket, prioritas, dan status berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $message = $this->messageModel->find($id);
        if ($message) {
            $this->messageModel->delete($id);
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'contact.ticket.deleted', 'communication', 'contact_messages', (int) $id, ['deleted' => $message]);
        }

        return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('success', 'Pesan tiket berhasil dihapus.');
    }
}
