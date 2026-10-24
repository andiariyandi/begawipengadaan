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
        $keyword     = trim((string) ($this->request->getGet('q') ?: ''));
        $type        = trim((string) ($this->request->getGet('type') ?: 'all'));
        $channel     = trim((string) ($this->request->getGet('channel') ?: 'all'));
        $status      = trim((string) ($this->request->getGet('status') ?: 'all'));
        $profileType = trim((string) ($this->request->getGet('profile_type') ?: 'all'));
        $from        = trim((string) ($this->request->getGet('from') ?: ''));
        $to          = trim((string) ($this->request->getGet('to') ?: ''));
        $sort        = (string) ($this->request->getGet('sort') ?: 'created_at');
        $dir         = strtolower((string) ($this->request->getGet('dir') ?: 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage     = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 25)));
        $page        = max(1, (int) ($this->request->getGet('page_notifications') ?: ($this->request->getGet('page_notif') ?: ($this->request->getGet('page') ?: 1))));

        $filters = [
            'q'            => $keyword,
            'type'         => $type,
            'channel'      => $channel,
            'status'       => $status,
            'profile_type' => $profileType,
            'from'         => $from,
            'to'           => $to,
            'sort'         => $sort,
            'dir'          => $dir,
            'per_page'     => $perPage,
        ];

        $result = $this->service->getAdminNotifications($filters, $perPage, $page);
        $stats  = $this->service->getNotificationStats();

        $pager = service('pager');
        $pager->setPath('dashboard/sistem/notifikasi', 'notifications');
        $pager->store('notifications', $page, $perPage, $result['total'], 0);

        // Ambil daftar pengguna aktif untuk opsi broadcast spesifik
        $userModel = new UserModel();
        $targetUsers = $userModel->select('id, name, email, username, profile_type')
            ->where('deleted_at IS NULL')
            ->where('account_status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll(500);

        // URL Ekspor CSV
        $exportParams = array_filter($filters, static fn($v) => $v !== null && $v !== '' && $v !== 'all');
        unset($exportParams['per_page']);
        $filterQuery = http_build_query($exportParams);
        $exportUrl = site_url('dashboard/sistem/notifikasi/export' . ($filterQuery !== '' ? '?' . $filterQuery : ''));

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
            'page'          => $page,
            'perPage'       => $perPage,
            'targetUsers'   => $targetUsers,
            'exportUrl'     => $exportUrl,
            'baseUrl'       => site_url('dashboard/sistem/notifikasi'),
        ]);
    }

    /**
     * Ekspor CSV Riwayat Log Notifikasi (layaknya Audit Trail log).
     */
    public function exportCsv()
    {
        $keyword     = trim((string) ($this->request->getGet('q') ?: ''));
        $type        = trim((string) ($this->request->getGet('type') ?: 'all'));
        $channel     = trim((string) ($this->request->getGet('channel') ?: 'all'));
        $status      = trim((string) ($this->request->getGet('status') ?: 'all'));
        $profileType = trim((string) ($this->request->getGet('profile_type') ?: 'all'));
        $from        = trim((string) ($this->request->getGet('from') ?: ''));
        $to          = trim((string) ($this->request->getGet('to') ?: ''));
        $sort        = (string) ($this->request->getGet('sort') ?: 'created_at');
        $dir         = strtolower((string) ($this->request->getGet('dir') ?: 'desc')) === 'asc' ? 'asc' : 'desc';

        $filters = [
            'q'            => $keyword,
            'type'         => $type,
            'channel'      => $channel,
            'status'       => $status,
            'profile_type' => $profileType,
            'from'         => $from,
            'to'           => $to,
            'sort'         => $sort,
            'dir'          => $dir,
        ];

        // Ekspor mengambil hingga 5.000 log notifikasi hasil filter
        $result = $this->service->getAdminNotifications($filters, 5000, 1);
        $items  = $result['items'] ?? [];

        $actorId = (int) (session()->get('user_id') ?: 1);
        try {
            $this->auditService->log(
                $actorId,
                'notification.export_csv',
                'notifications',
                'notifications',
                null,
                [
                    'filters'   => array_filter($filters),
                    'row_count' => count($items),
                ]
            );
        } catch (\Throwable $e) {
            // Silently continue export even if audit write fails
        }

        $filename = 'log-notifikasi-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM untuk Excel
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'ID Notifikasi',
            'ID Penerima',
            'Nama Penerima',
            'Email Penerima',
            'Username',
            'Tipe Profil',
            'Kategori / Tipe',
            'Judul Notifikasi',
            'Isi Pesan',
            'Kanal',
            'Status Baca',
            'Waktu Dibaca',
            'Waktu Kirim (WIB)',
        ]);

        foreach ($items as $row) {
            $isRead = !empty($row['read_at']);
            fputcsv($output, [
                $row['id'] ?? '',
                $row['user_id'] ?? '',
                $row['user_name'] ?? '',
                $row['user_email'] ?? '',
                $row['user_username'] ?? '',
                strtoupper($row['user_profile_type'] ?? 'USER'),
                $row['type'] ?? '',
                $row['title'] ?? '',
                $row['body'] ?? '',
                ucfirst($row['channel'] ?? 'database'),
                $isRead ? 'Sudah Dibaca' : 'Belum Dibaca',
                $isRead ? date('Y-m-d H:i:s', strtotime($row['read_at'])) : '-',
                !empty($row['created_at']) ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : '-',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Ambil data detail sebuah notifikasi (untuk AJAX modal atau halaman rincian langsung).
     */
    public function detail(string $id)
    {
        $notifId = (int) $id;
        $db = db_connect();
        $item = $db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email, users.username as user_username, users.profile_type as user_profile_type, users.account_status as user_status')
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
            return redirect()->to(site_url('dashboard/sistem/notifikasi'))->with('error', 'Notifikasi #' . $notifId . ' tidak ditemukan.');
        }

        // Auto mark as read jika notifikasi dibuka / dilihat rinciannya
        if (empty($item['read_at'])) {
            $this->service->markAsRead($notifId);
            $item['read_at'] = date('Y-m-d H:i:s');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data'    => $item,
            ]);
        }

        // Ambil riwayat notifikasi terkait dari penerima yang sama
        $relatedNotifs = $db->table('notifications')
            ->select('id, type, title, created_at, read_at')
            ->where('user_id', (int) $item['user_id'])
            ->where('id !=', $notifId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('dashboard/audit/notification_detail', [
            'title'         => 'Rincian Notifikasi #' . $notifId . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Notifikasi Sistem',
            'activeMenu'    => 'system',
            'activeSubmenu' => 'notifications',
            'item'          => $item,
            'relatedNotifs' => $relatedNotifs,
            'backUrl'       => site_url('dashboard/sistem/notifikasi'),
        ]);
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

        if ($targetGroup === 'user') {
            if (!$targetUserId || $targetUserId <= 0) {
                return redirect()->back()->withInput()->with('error', 'Validasi gagal: Anda memilih sasaran spesifik, silakan tentukan pengguna penerima.');
            }
            $targetUser = (new UserModel())->find($targetUserId);
            if (!$targetUser) {
                return redirect()->back()->withInput()->with('error', 'Pengguna target tidak ditemukan dalam sistem.');
            }
        }

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

        return redirect()->route('dashboard.system.notifications')->with('message', "Siaran notifikasi berhasil dibuat persisten dan dikirimkan kepada {$sentCount} penerima.");
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
        $authService = new \App\Services\Identity\AuthorizationService();
        $isSuperAdmin = $authService->isSuperAdmin();
        $roles = session()->get('roles') ?? [];
        $isAdmin = $isSuperAdmin 
            || in_array('super_administrator', $roles, true) 
            || in_array('administrator', $roles, true);

        // Untuk lonceng notifikasi bar: HANYA tampilkan notifikasi yang BELUM dibaca (unread only)
        $items = $isAdmin
            ? $this->service->getRecentNotifications(5, true)
            : ($userId > 0 ? $this->service->getUserNotifications($userId, 5, true) : $this->service->getRecentNotifications(5, true));

        $unread = $isAdmin
            ? $this->service->unreadCountAll()
            : ($userId > 0 ? $this->service->unreadCount($userId) : $this->service->unreadCountAll());

        $formattedItems = [];
        foreach ($items as $item) {
            $catInfo = NotificationService::formatCategory($item['type'] ?? 'system');
            $timeAgo = NotificationService::timeAgo($item['created_at'] ?? null);
            $itemData = !empty($item['data']) ? (is_array($item['data']) ? $item['data'] : json_decode($item['data'], true)) : [];
            $targetUrl = !empty($itemData['url']) ? $itemData['url'] : site_url('dashboard/sistem/notifikasi/' . $item['id']);

            $formattedItems[] = [
                'id'         => (int) $item['id'],
                'title'      => (string) ($item['title'] ?? '-'),
                'body'       => (string) ($item['body'] ?? ''),
                'type'       => (string) ($item['type'] ?? 'system'),
                'channel'    => (string) ($item['channel'] ?? 'database'),
                'is_read'    => !empty($item['read_at']),
                'read_at'    => $item['read_at'] ?? null,
                'created_at' => $item['created_at'] ?? null,
                'time_ago'   => $timeAgo,
                'badge'      => $catInfo['badge'] ?? 'bg-primary-50 text-primary-600',
                'icon'       => $catInfo['icon'] ?? 'solar:bell-bing-outline',
                'url'        => $targetUrl,
                'detail_url' => site_url('dashboard/sistem/notifikasi/' . $item['id']),
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'unread'  => (int) $unread,
            'items'   => $formattedItems,
        ]);
    }
}
