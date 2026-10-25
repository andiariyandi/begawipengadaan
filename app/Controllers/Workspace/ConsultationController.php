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

    /**
     * Service categories shared between Workspace Helpdesk and Landing Page Contact form.
     */
    public static function getCategories(): array
    {
        return [
            'Konsultasi Perencanaan Pengadaan',
            'Konsultasi E-Katalog & Toko Daring',
            'Konsultasi Pemilihan Penyedia / Tender',
            'Konsultasi Kontrak & SPMK',
            'Bimbingan Teknis & Pelatihan PBJ',
            'Aspirasi, Saran & Pengaduan Layanan',
            'Lainnya / Umum',
        ];
    }

    /**
     * Handle optional file attachment upload for consultation tickets & chat replies.
     */
    private function handleAttachmentUpload(?int $actorId = null): ?array
    {
        $file = $this->request->getFile('attachment');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'];
        $ext = strtolower((string) $file->getClientExtension());
        if (! in_array($ext, $allowedExt, true)) {
            throw new \RuntimeException('Format berkas lampiran tidak didukung. Gunakan PDF, Office, Gambar, atau ZIP.');
        }

        $mimeType = $file->getMimeType();
        $disallowedMimes = ['application/x-php', 'text/x-php', 'application/x-httpd-php', 'application/x-sh', 'application/x-msdos-program', 'application/x-executable'];
        if (in_array(strtolower($mimeType), $disallowedMimes, true) || str_contains($mimeType, 'php') || str_contains($mimeType, 'script')) {
            throw new \RuntimeException('Tipe berkas tidak diizinkan demi alasan keamanan sistem.');
        }

        // Max 1 MB
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \RuntimeException('Ukuran berkas lampiran maksimal 1 MB.');
        }

        $origName = $file->getClientName();
        $mimeType = $file->getClientMimeType();
        $sizeBytes = (int) $file->getSize();
        $newName = 'tkt_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $targetDir = FCPATH . 'uploads/consultations';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $file->move($targetDir, $newName);

        $sizeHuman = $sizeBytes >= 1048576
            ? number_format($sizeBytes / 1048576, 2) . ' MB'
            : number_format(max(1, $sizeBytes / 1024), 1) . ' KB';

        $relativePath = 'uploads/consultations/' . $newName;
        $fileAssetId = null;

        try {
            if ($this->db->tableExists('file_assets')) {
                $this->db->table('file_assets')->insert([
                    'storage_disk'  => 'public',
                    'file_path'     => $relativePath,
                    'original_name' => $origName,
                    'mime_type'     => $mimeType,
                    'file_size'     => $sizeBytes,
                    'uploaded_by'   => $actorId ?: null,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
                $fileAssetId = (int) $this->db->insertID();
            }
        } catch (\Throwable) {
        }

        return [
            'file_id'    => $fileAssetId,
            'name'       => $origName,
            'path'       => $relativePath,
            'url'        => base_url($relativePath),
            'ext'        => strtoupper($ext),
            'mime'       => $mimeType,
            'size_bytes' => $sizeBytes,
            'size_human' => $sizeHuman,
        ];
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');
        if (empty($email) && $userId > 0) {
            $u = $this->db->table('users')->select('email')->where('id', $userId)->get()->getRowArray();
            $email = $u['email'] ?? '';
        }

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
        $categories = self::getCategories();

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
        if (empty($email) && $userId > 0) {
            $u = $this->db->table('users')->select('email')->where('id', $userId)->get()->getRowArray();
            $email = $u['email'] ?? '';
        }

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
        $isOwner = (! empty($email) && ! empty($ticket['sender_email']) && strcasecmp($ticket['sender_email'], $email) === 0)
            || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);

        $roles = (array) session()->get('roles');
        $isAdmin = (bool) session()->get('dashboard_access')
            || in_array('administrator', $roles, true)
            || in_array('super_administrator', $roles, true)
            || in_array('operator', $roles, true);

        if (! $isOwner && ! $isAdmin) {
            return redirect()->to(route_to('workspace.consultation.index'))
                ->with('error', 'Anda tidak memiliki hak akses untuk membuka tiket ini.');
        }

        // Filter out internal notes for public user view
        $publicThreads = [];
        foreach ($notes['threads'] ?? [] as $th) {
            if (($th['channel'] ?? 'portal') !== 'internal' || ($isAdmin && ! $isOwner)) {
                $publicThreads[] = $th;
            }
        }
        $notes['threads'] = $publicThreads;

        return view('main/workspace/pages/konsultasi_detail', [
            'title'          => 'Tiket #' . $ticket['ticket_number'] . ' | Workspace BegawiPengadaan',
            'pageTitle'      => 'Ruang Konsultasi #' . $ticket['ticket_number'],
            'breadcrumbTail' => [
                ['label' => 'Konsultasi & Tiket', 'url' => route_to('workspace.consultation.index')],
                ['label' => '#' . $ticket['ticket_number']],
            ],
            'ticket'         => $ticket,
            'notes'          => $notes,
            'isAdmin'        => $isAdmin && ! $isOwner,
        ]);
    }

    public function reply(string $ticketNumber)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');
        if (empty($email) && $userId > 0) {
            $u = $this->db->table('users')->select('email')->where('id', $userId)->get()->getRowArray();
            $email = $u['email'] ?? '';
        }

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
        $isOwner = (! empty($email) && ! empty($ticket['sender_email']) && strcasecmp($ticket['sender_email'], $email) === 0)
            || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);

        $roles = (array) session()->get('roles');
        $isAdminRole = (bool) session()->get('dashboard_access')
            || in_array('administrator', $roles, true)
            || in_array('super_administrator', $roles, true)
            || in_array('operator', $roles, true);

        if (! $isOwner && ! $isAdminRole) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Akses ditolak.');
        }

        $actingAsAdmin = $isAdminRole && ! $isOwner;

        // Closed ticket response restriction - view only for workspace sender/non-administrator
        if (! $actingAsAdmin && in_array($ticket['status'], ['closed', ContactMessageModel::STATUS_CLOSED], true)) {
            return redirect()->to(route_to('workspace.consultation.detail', $ticket['ticket_number']))
                ->with('error', 'Tiket konsultasi ini telah ditutup oleh Administrator. Anda hanya dapat melihat riwayat percakapan.');
        }

        try {
            $uploadedAttachment = $this->handleAttachmentUpload($userId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $message = trim((string) $this->request->getPost('reply_message'));
        if ($message === '' && ! $uploadedAttachment) {
            return redirect()->back()->with('error', 'Silakan tulis pesan balasan atau lampirkan berkas pendukung.');
        }
        if ($message !== '' && mb_strlen($message) < 2 && ! $uploadedAttachment) {
            return redirect()->back()->with('error', 'Pesan balasan terlalu pendek.');
        }

        $senderName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
        $senderType = $actingAsAdmin ? 'administrator' : 'user';
        $newStatus  = $actingAsAdmin ? ContactMessageModel::STATUS_REPLIED : ContactMessageModel::STATUS_WAITING_ADMIN;

        $threadMessage = [
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'sender_id'   => $userId,
            'channel'     => 'portal',
            'message'     => $message ?: '[Melampirkan berkas pendukung]',
            'attachments' => $uploadedAttachment ? [$uploadedAttachment] : [],
        ];

        $success = $this->messageModel->appendThread((int) $ticket['id'], $threadMessage, $newStatus, $userId);

        if (! $success) {
            return redirect()->back()->with('error', 'Gagal mengirimkan balasan pesan.');
        }

        // Notifikasi ke pool helpdesk (Super Admin, Admin, & Operator) bila balasan dari pemohon
        if (! $actingAsAdmin) {
            try {
                (new \App\Services\NotificationService())->sendShared(
                    'shared:helpdesk',
                    'ticket_reply',
                    "Tanggapan Baru untuk Tiket #{$ticket['ticket_number']}",
                    "{$senderName} telah membalas tiket konsultasi: \"" . mb_strimwidth($message ?: 'Melampirkan berkas', 0, 100, '...') . "\"",
                    [
                        'ticket_number' => $ticket['ticket_number'],
                        'sender_name'   => $senderName,
                        'url'           => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $ticket['id']),
                    ],
                    $userId
                );
            } catch (\Throwable) {
            }
        }

        return redirect()->to(route_to('workspace.consultation.detail', $ticket['ticket_number']))
            ->with('success', 'Pesan balasan Anda berhasil dikirim.');
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
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi formulir pengajuan tiket dengan benar.');
        }

        $userId    = (int) session()->get('user_id');
        $userName  = (string) (session()->get('name') ?: session()->get('username'));
        $userEmail = (string) session()->get('email');
        $userPhone = (string) (session()->get('phone') ?: $this->request->getPost('phone'));

        if (empty($userEmail) && $userId > 0) {
            $u = $this->db->table('users')->select('email, phone')->where('id', $userId)->get()->getRowArray();
            $userEmail = $u['email'] ?? '';
            if (empty($userPhone)) {
                $userPhone = $u['phone'] ?? '';
            }
        }

        try {
            $uploadedAttachment = $this->handleAttachmentUpload($userId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $detailLog = 'Dibuat dari Ruang Kerja (Workspace) Pengguna Terverifikasi.';
        if ($uploadedAttachment) {
            $detailLog .= ' Disertai lampiran: ' . $uploadedAttachment['name'];
        }

        $initialNotes = [
            'user_id'       => $userId,
            'priority'      => 'normal',
            'assigned_to'   => null,
            'internal_note' => '',
            'attachments'   => $uploadedAttachment ? [$uploadedAttachment] : [],
            'threads'       => [],
            'logs'          => [
                [
                    'time'       => date('Y-m-d H:i:s'),
                    'actor'      => $userName,
                    'actor_role' => 'user',
                    'action'     => 'Tiket Dibuat',
                    'detail'     => $detailLog,
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

            // Notifikasi bersama ke pool helpdesk bahwa tiket baru telah dibuat
            try {
                (new \App\Services\NotificationService())->sendShared(
                    'shared:helpdesk',
                    'ticket_created',
                    "Tiket Konsultasi Baru #{$ticketNumber}",
                    "Permohonan baru dari {$userName}: \"" . (string) $this->request->getPost('subject') . "\"",
                    [
                        'ticket_number' => $ticketNumber,
                        'user_id'       => $userId,
                        'url'           => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $insertId),
                    ],
                    $userId
                );
            } catch (\Throwable) {
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat tiket konsultasi: ' . $e->getMessage());
        }

        return redirect()->to(route_to('workspace.consultation.detail', $ticketNumber))
            ->with('success', "Tiket konsultasi #{$ticketNumber} berhasil dibuat! Petugas Helpdesk akan segera menindaklanjuti di Ruang Konsultasi ini.");
    }

    /**
     * Mark ticket as closed/resolved by ticket owner or administrator.
     */
    public function resolve(string $ticketNumber)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $email  = (string) session()->get('email');
        $userId = (int) session()->get('user_id');
        if (empty($email) && $userId > 0) {
            $u = $this->db->table('users')->select('email')->where('id', $userId)->get()->getRowArray();
            $email = $u['email'] ?? '';
        }

        $ticket = $this->messageModel->findByTicketNumber($ticketNumber);
        if (! $ticket && is_numeric($ticketNumber)) {
            $ticket = $this->messageModel->find((int) $ticketNumber);
        }

        if (! $ticket) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Tiket tidak ditemukan.');
        }

        $notes = ContactMessageModel::parseAdminNotes($ticket['admin_notes'] ?? null);
        $isOwner = (! empty($email) && ! empty($ticket['sender_email']) && strcasecmp($ticket['sender_email'], $email) === 0)
            || (isset($notes['user_id']) && (int) $notes['user_id'] === $userId);

        $roles = (array) session()->get('roles');
        $isAdminRole = (bool) session()->get('dashboard_access')
            || in_array('administrator', $roles, true)
            || in_array('super_administrator', $roles, true)
            || in_array('operator', $roles, true);

        if (! $isOwner && ! $isAdminRole) {
            return redirect()->to(route_to('workspace.consultation.index'))->with('error', 'Akses ditolak.');
        }

        if (in_array($ticket['status'], ['closed', ContactMessageModel::STATUS_CLOSED], true)) {
            return redirect()->to(route_to('workspace.consultation.detail', $ticket['ticket_number']))
                ->with('info', 'Tiket ini sudah ditutup sebelumnya.');
        }

        $now = date('Y-m-d H:i:s');
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pemohon');

        $notes['logs'][] = [
            'time'       => $now,
            'actor'      => $userName,
            'actor_role' => $isAdminRole && ! $isOwner ? 'administrator' : 'user',
            'action'     => 'Tiket Selesai',
            'detail'     => 'Konsultasi ditandai selesai dan ditutup oleh ' . ($isOwner ? 'pemohon' : 'petugas') . '.',
        ];

        $updateData = [
            'status'      => ContactMessageModel::STATUS_CLOSED,
            'admin_notes' => ContactMessageModel::encodeAdminNotes($notes),
            'updated_at'  => $now,
        ];

        $this->messageModel->update((int) $ticket['id'], $updateData);

        if ($isOwner) {
            try {
                (new \App\Services\NotificationService())->sendShared(
                    'shared:helpdesk',
                    'ticket_resolved_by_user',
                    "Tiket #{$ticket['ticket_number']} Telah Selesai",
                    "Pemohon ({$userName}) telah menandai konsultasi \"{$ticket['subject']}\" selesai.",
                    [
                        'ticket_number' => $ticket['ticket_number'],
                        'url'           => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $ticket['id']),
                    ],
                    $userId
                );
            } catch (\Throwable) {
            }
        }

        return redirect()->to(route_to('workspace.consultation.detail', $ticket['ticket_number']))
            ->with('success', 'Terima kasih! Tiket konsultasi ini telah berhasil ditandai selesai dan ditutup. Mohon luangkan waktu 1 menit untuk mengisi survei kepuasan.')
            ->with('show_survey_modal', true);
    }
}
