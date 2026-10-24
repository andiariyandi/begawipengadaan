<?php

namespace App\Services;

use App\Models\NotificationModel;
use CodeIgniter\Database\BaseConnection;

class NotificationService
{
    protected BaseConnection $db;
    protected NotificationModel $model;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db    = $db ?? db_connect();
        $this->model = new NotificationModel();
    }

    /**
     * Kirim notifikasi internal berbasis database.
     */
    public function send(int $userId, string $type, string $title, ?string $body = null, ?array $data = null, string $channel = 'database'): int
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('notifications')->insert([
            'user_id'    => $userId,
            'channel'    => $channel,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'data'       => $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
            'read_at'    => null,
            'sent_at'    => $now,
            'created_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * Broadcast notifikasi ke seluruh pengguna atau berdasarkan profil / segmen tertentu.
     *
     * @param string $type
     * @param string $title
     * @param string|null $body
     * @param string $targetGroup 'all', 'asn', 'provider', 'academic', 'public', or 'user'
     * @param int|null $targetUserId
     * @param array|null $data
     * @param string $channel
     * @return int Jumlah notifikasi yang berhasil dikirim
     */
    public function broadcast(
        string $type,
        string $title,
        ?string $body = null,
        string $targetGroup = 'all',
        ?int $targetUserId = null,
        ?array $data = null,
        string $channel = 'database'
    ): int {
        $now = date('Y-m-d H:i:s');

        // Jika target adalah spesifik satu pengguna
        if ($targetGroup === 'user' && $targetUserId) {
            $this->send($targetUserId, $type, $title, $body, $data, $channel);
            return 1;
        }

        // Tentukan query target pengguna
        $builder = $this->db->table('users')
            ->select('id')
            ->where('deleted_at IS NULL');

        if (in_array($targetGroup, ['asn', 'provider', 'academic', 'public'], true)) {
            $builder->where('profile_type', $targetGroup);
        }

        $users = $builder->get()->getResultArray();
        if (empty($users)) {
            return 0;
        }

        $encodedData = $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;
        $batch = [];
        $count = 0;

        foreach ($users as $user) {
            $batch[] = [
                'user_id'    => (int) $user['id'],
                'channel'    => $channel,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'data'       => $encodedData,
                'read_at'    => null,
                'sent_at'    => $now,
                'created_at' => $now,
            ];

            // Insert in chunks of 100 for memory and SQL performance
            if (count($batch) >= 100) {
                $this->db->table('notifications')->insertBatch($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->db->table('notifications')->insertBatch($batch);
            $count += count($batch);
        }

        return $count;
    }

    /**
     * Ambil daftar notifikasi milik pengguna tertentu.
     *
     * @param int $userId
     * @param int $limit
     * @param bool $onlyUnread Jika true, hanya ambil notifikasi yang belum dibaca (misal untuk lonceng notifikasi bar)
     */
    public function getUserNotifications(int $userId, int $limit = 30, bool $onlyUnread = false): array
    {
        $builder = $this->db->table('notifications')
            ->where('user_id', $userId);

        if ($onlyUnread) {
            $builder->where('read_at IS NULL', null, false);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Ambil notifikasi sistem terbaru secara global.
     *
     * @param int $limit
     * @param bool $onlyUnread Jika true, hanya ambil notifikasi yang belum dibaca (misal untuk lonceng notifikasi bar)
     */
    public function getRecentNotifications(int $limit = 5, bool $onlyUnread = false): array
    {
        $builder = $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email')
            ->join('users', 'users.id = notifications.user_id', 'left');

        if ($onlyUnread) {
            $builder->where('notifications.read_at IS NULL', null, false);
        }

        return $builder->orderBy('notifications.created_at', 'DESC')
            ->orderBy('notifications.id', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Helper privat untuk menerapkan multi-facet filter secara konsisten pada builder notifikasi.
     */
    protected function applyNotificationFilters(\CodeIgniter\Database\BaseBuilder $builder, array $filters): \CodeIgniter\Database\BaseBuilder
    {
        // Filter: pencarian teks (judul, pesan, penerima)
        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $builder->groupStart()
                ->like('notifications.title', $q)
                ->orLike('notifications.body', $q)
                ->orLike('users.name', $q)
                ->orLike('users.email', $q)
                ->orLike('users.username', $q)
                ->groupEnd();
        }

        // Filter: kategori / type
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $builder->where('notifications.type', $filters['type']);
        }

        // Filter: kanal
        if (!empty($filters['channel']) && $filters['channel'] !== 'all') {
            $builder->where('notifications.channel', $filters['channel']);
        }

        // Filter: status baca
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'unread') {
                $builder->where('notifications.read_at IS NULL', null, false);
            } elseif ($filters['status'] === 'read') {
                $builder->where('notifications.read_at IS NOT NULL', null, false);
            }
        }

        // Filter: tipe profil pengguna
        if (!empty($filters['profile_type']) && $filters['profile_type'] !== 'all') {
            $builder->where('users.profile_type', $filters['profile_type']);
        }

        // Filter: Dari Tanggal (from)
        if (!empty($filters['from'])) {
            $builder->where('notifications.created_at >=', $filters['from'] . ' 00:00:00');
        }

        // Filter: Sampai Tanggal (to)
        if (!empty($filters['to'])) {
            $builder->where('notifications.created_at <=', $filters['to'] . ' 23:59:59');
        }

        return $builder;
    }

    /**
     * Ambil notifikasi terpaginasi untuk panel dashboard administrator dengan filter dinamis dan sorting global server-side.
     */
    public function getAdminNotifications(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        // 1. Count query terisolasi (mencegah reset builder clause)
        $countBuilder = $this->db->table('notifications')
            ->join('users', 'users.id = notifications.user_id', 'left');
        $this->applyNotificationFilters($countBuilder, $filters);
        $total = $countBuilder->countAllResults();

        // 2. Data query terisolasi
        $builder = $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email, users.username as user_username, users.profile_type as user_profile_type')
            ->join('users', 'users.id = notifications.user_id', 'left');
        $this->applyNotificationFilters($builder, $filters);

        // Sorting mapping
        $sort = (string) ($filters['sort'] ?? 'created_at');
        $dir  = strtolower((string) ($filters['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

        $allowedSorts = [
            'created_at' => 'notifications.created_at',
            'recipient'  => 'COALESCE(NULLIF(users.name, ""), users.username, "Pengguna")',
            'title'      => 'notifications.title',
            'type'       => 'notifications.type',
            'channel'    => 'notifications.channel',
            'status'     => '(CASE WHEN notifications.read_at IS NOT NULL THEN 1 ELSE 0 END)',
        ];

        if ($sort === 'recipient') {
            $builder->orderBy('COALESCE(NULLIF(users.name, ""), users.username, "Pengguna")', $dir, false);
        } elseif ($sort === 'status') {
            $builder->orderBy('(CASE WHEN notifications.read_at IS NOT NULL THEN 1 ELSE 0 END)', $dir, false);
        } else {
            $orderCol = $allowedSorts[$sort] ?? 'notifications.created_at';
            $builder->orderBy($orderCol, $dir);
        }

        // Selalu sertakan tie-breaker deterministik ID DESC agar urutan data stabil dan konsisten
        $builder->orderBy('notifications.id', $dir);

        $offset = max(0, ($page - 1) * $perPage);
        $items = $builder->limit($perPage, $offset)
            ->get()->getResultArray();

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Ambil metrik statistik notifikasi sistem secara riil.
     */
    public function getNotificationStats(): array
    {
        $today = date('Y-m-d 00:00:00');

        $total = (int) $this->db->table('notifications')->countAllResults();
        $unread = (int) $this->db->table('notifications')->where('read_at IS NULL', null, false)->countAllResults();
        $todayCount = (int) $this->db->table('notifications')->where('created_at >=', $today)->countAllResults();
        $broadcast = (int) $this->db->table('notifications')->whereIn('type', ['broadcast', 'system', 'system_announcement'])->countAllResults();

        return [
            'total'     => $total,
            'unread'    => $unread,
            'today'     => $todayCount,
            'broadcast' => $broadcast,
        ];
    }

    /**
     * Hitung jumlah notifikasi yang belum dibaca.
     */
    public function unreadCount(int|array $target): int
    {
        if (is_array($target)) {
            return count(array_filter($target, static fn($item) => empty($item['read_at']) && empty($item['is_read'])));
        }

        return $this->db->table('notifications')
            ->where('user_id', $target)
            ->where('read_at IS NULL', null, false)
            ->countAllResults();
    }

    /**
     * Hitung seluruh notifikasi belum dibaca di dalam sistem.
     */
    public function unreadCountAll(): int
    {
        return $this->db->table('notifications')
            ->where('read_at IS NULL', null, false)
            ->countAllResults();
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca.
     */
    public function markAsRead(int $notificationId, ?int $userId = null): bool
    {
        $builder = $this->db->table('notifications')->where('id', $notificationId);
        if ($userId !== null && $userId > 0) {
            $builder->where('user_id', $userId);
        }

        return (bool) $builder->update(['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Tandai seluruh notifikasi sebagai telah dibaca (milik user atau sistem).
     */
    public function markAllAsRead(?int $userId = null): bool
    {
        $builder = $this->db->table('notifications')->where('read_at IS NULL', null, false);
        if ($userId !== null && $userId > 0) {
            $builder->where('user_id', $userId);
        }

        return (bool) $builder->update(['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Hapus notifikasi berdasarkan ID.
     */
    public function delete(int $id): bool
    {
        return (bool) $this->db->table('notifications')->where('id', $id)->delete();
    }

    /**
     * Format relatif waktu Indonesia.
     */
    public static function timeAgo(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }

        $time = strtotime($datetime);
        if ($time === false) {
            return $datetime;
        }

        $diff = time() - $time;
        if ($diff < 60) {
            return 'Baru saja';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return $m . ' mnt lalu';
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return $h . ' jam lalu';
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return $d . ' hari lalu';
        }

        return date('d M Y', $time);
    }

    /**
     * Kirim notifikasi balasan tiket konsultasi kepada pengguna terdaftar.
     */
    public function notifyTicketReply(int $userId, string $ticketNumber, string $senderName, string $messageSnippet): int
    {
        return $this->send(
            $userId,
            'ticket_reply',
            "Tanggapan Baru untuk Tiket #{$ticketNumber}",
            "{$senderName} telah membalas: \"" . mb_strimwidth($messageSnippet, 0, 110, '...') . "\"",
            [
                'ticket_number' => $ticketNumber,
                'sender_name'   => $senderName,
                'url'           => route_to('workspace.consultation.detail', $ticketNumber),
            ]
        );
    }

    /**
     * Kirim notifikasi penting kepada seluruh Administrator / Super Administrator.
     */
    public function notifyAdmins(string $type, string $title, ?string $body = null, ?array $data = null): int
    {
        $adminUsers = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->whereIn('r.key', ['administrator', 'super_administrator'])
            ->select('DISTINCT(ur.user_id) AS uid', false)
            ->get()->getResultArray();

        $count = 0;
        foreach ($adminUsers as $adm) {
            $this->send((int) $adm['uid'], $type, $title, $body, $data);
            $count++;
        }

        // Fallback jika belum ada relasi user_roles
        if ($count === 0) {
            $admins = $this->db->table('users')
                ->select('id')
                ->where('account_status', 'active')
                ->where('deleted_at IS NULL')
                ->limit(5)
                ->get()->getResultArray();

            foreach ($admins as $adm) {
                $this->send((int) $adm['id'], $type, $title, $body, $data);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Ambil notifikasi pengguna dengan filter kategori / status.
     */
    public function getUserNotificationsFiltered(int $userId, array $filters = [], int $limit = 30): array
    {
        $builder = $this->db->table('notifications')
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'unread') {
                $builder->where('read_at IS NULL', null, false);
            } elseif ($filters['status'] === 'read') {
                $builder->where('read_at IS NOT NULL', null, false);
            }
        }

        if (! empty($filters['category'])) {
            match ($filters['category']) {
                'learning'     => $builder->whereIn('type', ['class_enrolled', 'class_reminder', 'lesson_completed', 'assignment_graded', 'quiz_graded']),
                'consultation' => $builder->whereIn('type', ['ticket_reply', 'ticket_created', 'ticket_closed']),
                'certificate'  => $builder->whereIn('type', ['certificate_issued', 'certificate_eligible']),
                'system'       => $builder->whereIn('type', ['broadcast', 'system', 'system_announcement', 'account_approved', 'account_rejected']),
                default        => null,
            };
        }

        return $builder->orderBy('created_at', 'DESC')->limit($limit)->get()->getResultArray();
    }

    /**
     * Format badge kategori notifikasi beserta ikon pendukung.
     */
    public static function formatCategory(string $type): array
    {
        return match ($type) {
            'ticket_reply', 'ticket_created', 'ticket_closed' => [
                'label' => 'Konsultasi & Tiket',
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon'  => 'solar:chat-round-line-bold',
                'fa'    => 'fas fa-comments text-info',
            ],
            'class_enrolled', 'class_reminder', 'lesson_completed' => [
                'label' => 'Pembelajaran',
                'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon'  => 'solar:book-bookmark-bold',
                'fa'    => 'fas fa-graduation-cap text-primary',
            ],
            'assignment_graded', 'assignment_submitted', 'quiz_graded' => [
                'label' => 'Tugas & Penilaian',
                'badge' => 'bg-warning-subtle text-warning-main border border-warning-subtle',
                'icon'  => 'solar:diploma-bold',
                'fa'    => 'fas fa-tasks text-warning',
            ],
            'certificate_issued', 'certificate_eligible' => [
                'label' => 'Penerbitan Sertifikat',
                'badge' => 'bg-success-subtle text-success border border-success-subtle',
                'icon'  => 'solar:verified-check-bold',
                'fa'    => 'fas fa-award text-success',
            ],
            'account_approved', 'profile_verified', 'role_request_approved' => [
                'label' => 'Akun Disetujui',
                'badge' => 'bg-success-subtle text-success border border-success-subtle',
                'icon'  => 'solar:user-check-bold',
                'fa'    => 'fas fa-user-check text-success',
            ],
            'account_rejected', 'role_request_rejected' => [
                'label' => 'Akun / Role Ditolak',
                'badge' => 'bg-danger-subtle text-danger-main border border-danger-subtle',
                'icon'  => 'solar:user-cross-bold',
                'fa'    => 'fas fa-user-times text-danger',
            ],
            'event_info', 'event_reminder', 'event_registered' => [
                'label' => 'Event & Agenda',
                'badge' => 'bg-warning-subtle text-warning-main border border-warning-subtle',
                'icon'  => 'solar:calendar-bold',
                'fa'    => 'fas fa-calendar-alt text-warning',
            ],
            'broadcast' => [
                'label' => 'Siaran Pengumuman',
                'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon'  => 'solar:megaphone-bold',
                'fa'    => 'fas fa-bullhorn text-primary',
            ],
            'system', 'system_announcement' => [
                'label' => 'Sistem',
                'badge' => 'bg-neutral-200 text-neutral-800 border border-neutral-300',
                'icon'  => 'solar:bell-bing-bold',
                'fa'    => 'fas fa-bell text-secondary',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', $type)),
                'badge' => 'bg-neutral-100 text-neutral-800 border border-neutral-200',
                'icon'  => 'solar:bell-linear',
                'fa'    => 'fas fa-bell text-primary',
            ],
        };
    }
}
