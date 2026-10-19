<?php

namespace App\Controllers\Admin\Communication;

use App\Controllers\BaseController;
use App\Services\AuditService;

class ContactMessageController extends BaseController
{
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $builder = $this->db->table('contact_messages')->orderBy('created_at', 'DESC');

        if ($status && in_array($status, ['unread', 'read', 'replied', 'archived'], true)) {
            $builder->where('status', $status);
        }

        $messages = $builder->get()->getResultArray();

        $unreadCount = $this->db->table('contact_messages')->where('status', 'unread')->countAllResults();
        $repliedCount = $this->db->table('contact_messages')->where('status', 'replied')->countAllResults();

        return view('dashboard/communication/inbox/index', [
            'title'         => 'Pesan Masuk (Inbox) | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pesan Masuk (Inbox)',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'inbox',
            'messages'      => $messages,
            'currentStatus' => $status,
            'unreadCount'   => $unreadCount,
            'repliedCount'  => $repliedCount,
        ]);
    }

    public function detail($id)
    {
        $message = $this->db->table('contact_messages')->where('id', $id)->get()->getRowArray();
        if (! $message) {
            return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('error', 'Pesan tidak ditemukan.');
        }

        // Auto mark as read if currently unread
        if ($message['status'] === 'unread') {
            $this->db->table('contact_messages')->where('id', $id)->update([
                'status'     => 'read',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $message['status'] = 'read';
        }

        return view('dashboard/communication/inbox/detail', [
            'title'         => 'Detail Pesan #' . ($message['ticket_number'] ?? $message['id']) . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Pesan Masuk',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'inbox',
            'message'       => $message,
        ]);
    }

    public function updateStatus($id)
    {
        $message = $this->db->table('contact_messages')->where('id', $id)->get()->getRowArray();
        if (! $message) {
            return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('error', 'Pesan tidak ditemukan.');
        }

        $newStatus  = trim((string) $this->request->getPost('status'));
        $adminNotes = trim((string) $this->request->getPost('admin_notes'));

        $update = [
            'status'      => $newStatus,
            'admin_notes' => $adminNotes,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($newStatus === 'replied' && empty($message['replied_at'])) {
            $update['replied_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('contact_messages')->where('id', $id)->update($update);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'contact.message.status_updated', 'communication', 'contact_messages', (int) $id, ['before' => $message, 'after' => $update]);

        return redirect()->to('dashboard/komunikasi/pesan-masuk/detail/' . $id)
            ->with('success', 'Catatan dan status pesan berhasil disimpan.');
    }

    public function delete($id)
    {
        $message = $this->db->table('contact_messages')->where('id', $id)->get()->getRowArray();
        if ($message) {
            $this->db->table('contact_messages')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'contact.message.deleted', 'communication', 'contact_messages', (int) $id, ['deleted' => $message]);
        }

        return redirect()->to('dashboard/komunikasi/pesan-masuk')->with('success', 'Pesan berhasil dihapus.');
    }
}
