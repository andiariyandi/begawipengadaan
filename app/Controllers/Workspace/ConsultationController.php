<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\Communication\ContactMessageModel;
use App\Services\AuditService;

class ConsultationController extends BaseController
{
    protected $helpers = ['url', 'form', 'text'];
    private ContactMessageModel $messageModel;
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->messageModel = new ContactMessageModel();
        $this->auditService = new AuditService();
        $this->db = \Config\Database::connect();
    }

    private function checkAuth()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses layanan konsultasi.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');

        $tickets = $this->messageModel->getUserTickets($email, $userId);

        $activeCount = 0;
        $repliedCount = 0;
        foreach ($tickets as &$t) {
            $notes = ContactMessageModel::parseAdminNotes($t['admin_notes'] ?? null);
            // Count only public messages (exclude internal notes)
            $publicThreads = array_filter($notes['threads'] ?? [], static fn($th) => ($th['channel'] ?? 'portal') !== 'internal');
            $t['threads_count'] = count($publicThreads);
            $t['parsed_notes']  = $notes;

            if (in_array($t['status'], ['unread', 'read', 'in_progress', 'waiting_admin', 'waiting_user'], true)) {
                $activeCount++;
            }
            if ($t['status'] === 'replied' || $t['status'] === 'closed') {
                $repliedCount++;
            }
        }
        unset($t);

        // Service categories for new ticket form modal
        $categories = [
            'Konsultasi Perencanaan Pengadaan',
            'Konsultasi E-Katalog & Toko Daring',
            'Konsultasi Pemilihan Penyedia / Tender',
            'Konsultasi Kontrak & SPMK',
            'Bimbingan Teknis & Pelatihan PBJ',
            'Aspirasi, Saran & Pengaduan Layanan',
            'Lainnya / Umum',
        ];

        return view('main/workspace/pages/konsultasi_index', [
            'title'          => 'Konsultasi & Tiket Saya | Workspace BegawiPengadaan',
            'pageTitle'      => 'Konsultasi & Tiket Helpdesk',
            'breadcrumbTail' => [['label' => 'Konsultasi & Tiket']],
            'tickets'        => $tickets,
            'totalTickets'   => count($tickets),
            'activeCount'    => $activeCount,
            'repliedCount'   => $repliedCount,
            'categories'     => $categories,
        ]);
    }

    public function detail(string $ticketNumber)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');

        $ticket = $this->messageModel->findByTicketNumber($ticketNumber);
        if (! $ticket) {
            // Also attempt by ID if numeric
            if (is_numeric($ticketNumber)) {
                $ticket = $this->messageModel->find((int) $ticketNumber);
            }
        }

        if (! $ticket) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Tiket konsultasi tidak ditemukan.');
        }

        $notes = ContactMessageModel::parseAdminNotes($ticket['admin_notes'] ?? null);

        // Security check: verify this ticket belongs to the user
        $isOwner = strcasecmp($ticket['sender_email'], $email) === 0
            || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);

        $isAdmin = (bool) session()->get('dashboard_access')
            || in_array('admin', (array) session()->get('roles'), true)
            || in_array('super_admin', (array) session()->get('roles'), true);

        if (! $isOwner && ! $isAdmin) {
            return redirect()->to(route_to('workspace.consultation.index'))
                ->with('error', 'Anda tidak memiliki hak akses untuk membuka tiket ini.');
        }

        // Filter out internal notes for public user view
        $publicThreads = [];
        foreach ($notes['threads'] ?? [] as $th) {
            if (($th['channel'] ?? 'portal') !== 'internal' || $isAdmin) {
                $publicThreads[] = $th;
            }
        }
        $notes['threads'] = $publicThreads;

        return view('main/workspace/pages/konsultasi_detail', [
            'title'          => 'Tiket #' . $ticket['ticket_number'] . ' | Workspace BegawiPengadaan',
            'pageTitle'      => 'Percakapan Konsultasi #' . $ticket['ticket_number'],
            'breadcrumbTail' => [
                ['label' => 'Konsultasi & Tiket', 'url' => route_to('workspace.consultation.index')],
                ['label' => '#' . $ticket['ticket_number']],
            ],
            'ticket'         => $ticket,
            'notes'          => $notes,
            'isAdmin'        => $isAdmin,
        ]);
    }

    public function reply(string $ticketNumber)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');

        $ticket = $this->messageModel->findByTicketNumber($ticketNumber);
        if (! $ticket) {
            if (is_numeric($ticketNumber)) {
                $ticket = $this->messageModel->find((int) $ticketNumber);
            }
        }

        if (! $ticket) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Tiket tidak ditemukan.');
        }

        $notes = ContactMessageModel::parseAdminNotes($ticket['admin_notes'] ?? null);
        $isOwner = strcasecmp($ticket['sender_email'], $email) === 0
            || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);

        $isAdmin = (bool) session()->get('dashboard_access')
            || in_array('admin', (array) session()->get('roles'), true)
            || in_array('super_admin', (array) session()->get('roles'), true);

        if (! $isOwner && ! $isAdmin) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Akses ditolak.');
        }

        $message = trim((string) $this->request->getPost('reply_message'));
        if ($message === '' || mb_strlen($message) < 3) {
            return redirect()->back()->with('error', 'Pesan balasan terlalu pendek (minimal 3 karakter).');
        }

        $senderName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
        $senderType = $isAdmin ? 'admin' : 'user';
        $newStatus  = $isAdmin ? ContactMessageModel::STATUS_REPLIED : ContactMessageModel::STATUS_WAITING_ADMIN;

        $threadMessage = [
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'sender_id'   => $userId,
            'channel'     => 'portal',
            'message'     => $message,
        ];

        $success = $this->messageModel->appendThread((int) $ticket['id'], $threadMessage, $newStatus, $userId);

        if (! $success) {
            return redirect()->back()->with('error', 'Gagal mengirimkan balasan pesan.');
        }

        // Notifikasi ke admin bila balasan dari pemohon
        if (! $isAdmin) {
            try {
                (new \App\Services\NotificationService())->notifyAdmins(
                    'ticket_reply',
                    "Tanggapan Baru untuk Tiket #{$ticket['ticket_number']}",
                    "{$senderName} telah membalas tiket konsultasi: \"" . mb_strimwidth($message, 0, 100, '...') . "\"",
                    ['ticket_number' => $ticket['ticket_number'], 'url' => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $ticket['id'])]
                );
            } catch (\Throwable) {
            }
        }

        return redirect()->to(route_to('workspace.consultation.detail', $ticket['ticket_number']))
            ->with('success', 'Balasan pesan Anda berhasil terkirim ke Helpdesk PBJ Lampung.');
    }

    public function create()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        return redirect()->to(site_url('kontak#form-pesan'));
    }

    public function store()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $rules = [
            'service_type' => 'required|max_length[100]',
            'subject'      => 'required|min_length[3]|max_length[200]',
            'message'      => 'required|min_length[10]|max_length[3000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi formulir dengan benar.');
        }

        $userId   = (int) session()->get('user_id');
        $userName = (string) (session()->get('name') ?: session()->get('username'));
        $userEmail= (string) session()->get('email');
        $userPhone= (string) (session()->get('phone') ?: $this->request->getPost('phone'));

        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $initialNotes = [
            'user_id'       => $userId,
            'priority'      => 'normal',
            'assigned_to'   => null,
            'internal_note' => '',
            'threads'       => [],
            'logs'          => [
                [
                    'time'       => date('Y-m-d H:i:s'),
                    'actor'      => $userName,
                    'actor_role' => 'user',
                    'action'     => 'Tiket Dibuat',
                    'detail'     => 'Dibuat dari Ruang Kerja (Workspace) Pengguna Terverifikasi.',
                ],
            ],
        ];

        try {
            $insertId = $this->messageModel->insert([
                'ticket_number'    => $ticketNumber,
                'sender_name'      => $userName,
                'sender_email'     => $userEmail,
                'sender_phone'     => $userPhone ?: null,
                'service_category' => substr((string) $this->request->getPost('service_type'), 0, 100),
                'subject'          => substr((string) $this->request->getPost('subject'), 0, 255),
                'message'          => (string) $this->request->getPost('message'),
                'status'           => ContactMessageModel::STATUS_UNREAD,
                'admin_notes'      => ContactMessageModel::encodeAdminNotes($initialNotes),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            $this->auditService->log(
                $userId,
                'contact.ticket.created',
                'communication',
                'contact_messages',
                $insertId,
                ['ticket_number' => $ticketNumber, 'subject' => (string) $this->request->getPost('subject')]
            );

            // Notifikasi ke admin helpdesk bahwa tiket baru telah dibuat
            try {
                (new \App\Services\NotificationService())->notifyAdmins(
                    'ticket_created',
                    "Tiket Konsultasi Baru #{$ticketNumber}",
                    "Permohonan baru dari {$userName}: \"" . (string) $this->request->getPost('subject') . "\"",
                    ['ticket_number' => $ticketNumber, 'url' => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $insertId)]
                );
            } catch (\Throwable) {
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat tiket konsultasi: ' . $e->getMessage());
        }

        return redirect()->to(route_to('workspace.consultation.detail', $ticketNumber))
            ->with('success', "Tiket konsultasi #{$ticketNumber} berhasil dibuat! Petugas Helpdesk akan segera menindaklanjuti dan Anda dapat saling membalas pesan di halaman ini.");
    }
}
