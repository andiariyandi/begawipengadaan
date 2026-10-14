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
     */
    public function getUserNotifications(int $userId, int $limit = 30): array
    {
        return $this->db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Ambil notifikasi sistem terbaru secara global.
     */
    public function getRecentNotifications(int $limit = 5): array
    {
        return $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email')
            ->join('users', 'users.id = notifications.user_id', 'left')
            ->orderBy('notifications.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Ambil notifikasi terpaginasi untuk panel dashboard admin dengan filter dinamis.
     */
    public function getAdminNotifications(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $builder = $this->db->table('notifications')
            ->select('notifications.*, users.name as user_name, users.email as user_email, users.username as user_username, users.profile_type as user_profile_type')
            ->join('users', 'users.id = notifications.user_id', 'left');

        // Filter: pencarian teks (judul, pesan, penerima)
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
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

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $offset = max(0, ($page - 1) * $perPage);
        $items = $builder->orderBy('notifications.created_at', 'DESC')
            ->limit($perPage, $offset)
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
     * Format badge kategori notifikasi.
     */
    public static function formatCategory(string $type): array
    {
        return match ($type) {
            'broadcast'                               => ['label' => 'Siaran Pengumuman', 'badge' => 'bg-primary-50 text-primary-600 border border-primary-200'],
            'system', 'system_announcement'           => ['label' => 'Sistem', 'badge' => 'bg-neutral-100 text-neutral-700 border border-neutral-300'],
            'account_approved'                        => ['label' => 'Akun Disetujui', 'badge' => 'bg-success-50 text-success-600 border border-success-200'],
            'account_rejected'                        => ['label' => 'Akun Ditolak', 'badge' => 'bg-danger-50 text-danger-600 border border-danger-200'],
            'assignment_graded'                       => ['label' => 'Penilaian Tugas', 'badge' => 'bg-info-50 text-info-600 border border-info-200'],
            'certificate_issued'                      => ['label' => 'Penerbitan Sertifikat', 'badge' => 'bg-success-50 text-success-700 border border-success-300'],
            'class_enrolled', 'class_reminder'        => ['label' => 'Pembelajaran', 'badge' => 'bg-lilac-50 text-lilac-600 border border-lilac-200'],
            'event_info', 'event_reminder'            => ['label' => 'Event & Agenda', 'badge' => 'bg-warning-50 text-warning-700 border border-warning-200'],
            'regulation_info'                         => ['label' => 'Regulasi & Kebijakan', 'badge' => 'bg-indigo-50 text-indigo-700 border border-indigo-200'],
            default                                   => ['label' => ucfirst(str_replace('_', ' ', $type)), 'badge' => 'bg-neutral-100 text-neutral-800 border border-neutral-200'],
        };
    }
}
