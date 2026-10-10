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
     * Kirim notifikasi internal berbasis database (tanpa email).
     */
    public function send(int $userId, string $type, string $title, ?string $body = null, ?array $data = null): int
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('notifications')->insert([
            'user_id'    => $userId,
            'channel'    => 'database',
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
     * Ambil daftar notifikasi milik pengguna.
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
     * Hitung jumlah notifikasi yang belum dibaca.
     * Menerima int (user_id) atau array untuk kompatibilitas.
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
     * Tandai satu notifikasi sebagai telah dibaca.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return (bool) $this->db->table('notifications')
            ->where(['id' => $notificationId, 'user_id' => $userId])
            ->update(['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Tandai seluruh notifikasi milik pengguna sebagai telah dibaca.
     */
    public function markAllAsRead(int $userId): bool
    {
        return (bool) $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('read_at IS NULL', null, false)
            ->update(['read_at' => date('Y-m-d H:i:s')]);
    }
}
