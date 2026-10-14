<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Services\AuditService;
use App\Services\NotificationService;

/**
 * Controller Admin untuk Manajemen Notifikasi & Siaran Sistem.
 */
class NotificationController extends BaseController
{
    private NotificationService $service;
    private AuditService $auditService;
    private NotificationModel $model;

    public function __construct()
    {
        $this->service = new NotificationService();
        $this->auditService = new AuditService();
        $this->model = new NotificationModel();
    }

    /**
     * Tampilan Pusat Log & Manajemen Notifikasi.
     */
    public function index(): string
    {
        $filters = [
            'q'            => $this->request->getGet('q') ?: null,
            'type'         => $this->request->getGet('type') ?: 'all',
            'channel'      => $this->request->getGet('channel') ?: 'all',
            'status'       => $this->request->getGet('status') ?: 'all',
            'profile_type' => $this->request->getGet('profile_type') ?: 'all',
        ];

        $page = max(1, (int) ($this->request->getGet('page_notif') ?: 1));
        $perPage = 15;

        $result = $this->service->getAdminNotifications($filters, $perPage, $page);
        $stats  = $this->service->getNotificationStats();

        $pager = service('pager');
        $pager->store('notifications', $page, $perPage, $result['total'], 0);

        // Ambil daftar pengguna aktif untuk opsi broadcast spesifik
        $userModel = new UserModel();
        $targetUsers = $userModel->select('id, name, email, profile_type')
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'ASC')
            ->findAll(100);

        return view('dashboard/audit/notifications', [
            'title'         => 'Log & Siaran Notifikasi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Log Notifikasi Sistem',
            'activeMenu'    => 'system',
            'activeSubmenu' => 'notifications',
            'items'         => $result['items'],
            'total'         => $result['total'],
            'stats'         => $stats,
            'filters'       => $filters,
            'pager'         => $pager,
            'targetUsers'   => $targetUsers,
        ]);
    }

    /**
     * Ambil data detail sebuah notifikasi (untuk AJAX modal).
     */
    public function detail(string $id)
    {
        $notifId = (int) $id;
        $db = db_connect();
        $item = $db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email, users.username as user_username, users.profile_type as user_profile_type')
            ->join('users', 'users.id = notifications.user_id', 'left')
            ->where('notifications.id', $notifId)
            ->get()->getRowArray();

        if (!$item) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan.',
                ]);
            }
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data'    => $item,
            ]);
        }

        return redirect()->route('dashboard.system.notifications');
    }

    /**
     * Kirim broadcast notifikasi baru ke pengguna.
     */
    public function sendBroadcast()
    {
        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'body'         => 'required|min_length[3]',
            'type'         => 'required',
            'target_group' => 'required|in_list[all,asn,provider,academic,public,user]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: Harap isi semua bidang wajib dengan benar.');
        }

        $targetGroup  = (string) $this->request->getPost('target_group');
        $targetUserId = $this->request->getPost('target_user_id') ? (int) $this->request->getPost('target_user_id') : null;
        $title        = trim((string) $this->request->getPost('title'));
        $body         = trim((string) $this->request->getPost('body'));
        $type         = (string) $this->request->getPost('type');
        $channel      = (string) ($this->request->getPost('channel') ?: 'database');

        $actorId = (int) (session()->get('user_id') ?: 1);

        $sentCount = $this->service->broadcast(
            $type,
            $title,
            $body,
            $targetGroup,
            $targetUserId,
            ['sender_id' => $actorId, 'broadcast_at' => date('Y-m-d H:i:s')],
            $channel
        );

        $this->auditService->log(
            $actorId,
            'notification.broadcast',
            'notifications',
            $targetGroup,
            $targetUserId,
            [
                'title'        => $title,
                'type'         => $type,
                'target_group' => $targetGroup,
                'sent_count'   => $sentCount,
            ]
        );

        return redirect()->route('dashboard.system.notifications')->with('message', "Siaran notifikasi berhasil dikirimkan kepada {$sentCount} penerima.");
    }

    /**
     * Tandai sebuah notifikasi telah dibaca.
     */
    public function markAsRead(string $id)
    {
        $notifId = (int) $id;
        $this->service->markAsRead($notifId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notifikasi telah ditandai sebagai telah dibaca.',
            ]);
        }

        return redirect()->back()->with('message', 'Notifikasi telah ditandai sebagai telah dibaca.');
    }

    /**
     * Tandai seluruh notifikasi telah dibaca.
     */
    public function markAllAsRead()
    {
        $this->service->markAllAsRead();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semua notifikasi telah ditandai sebagai telah dibaca.',
            ]);
        }

        return redirect()->back()->with('message', 'Semua notifikasi telah ditandai sebagai telah dibaca.');
    }

    /**
     * Hapus sebuah notifikasi.
     */
    public function delete(string $id)
    {
        $notifId = (int) $id;
        $actorId = (int) (session()->get('user_id') ?: 1);

        $this->service->delete($notifId);

        $this->auditService->log(
            $actorId,
            'notification.delete',
            'notifications',
            'notification',
            $notifId
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('message', 'Notifikasi berhasil dihapus.');
    }

    /**
     * API feed untuk update lonceng notifikasi header secara live.
     */
    public function feed()
    {
        $userId = (int) (session()->get('user_id') ?: 0);
        $items = $userId > 0
            ? $this->service->getUserNotifications($userId, 5)
            : $this->service->getRecentNotifications(5);

        $unread = $userId > 0
            ? $this->service->unreadCount($userId)
            : $this->service->unreadCountAll();

        return $this->response->setJSON([
            'success' => true,
            'unread'  => $unread,
            'items'   => $items,
        ]);
    }
}
