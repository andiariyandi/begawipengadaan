<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table         = 'bookmarks';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'bookmarkable_type', 'bookmarkable_id', 'created_at',
    ];

    /**
     * Daftar type yang didukung controller bookmark.
     */
    public const SUPPORTED_TYPES = [
        'knowledge_article' => 'Artikel Bunga Rampai',
        'knowledge_faq'     => 'FAQ',
        'knowledge_case'    => 'Studi Kasus',
        'knowledge_template'=> 'Template Dokumen',
        'knowledge_media'   => 'Media dan Galeri',
        'program'           => 'Program',
        'course'            => 'Course',
        'learning_class'    => 'Kelas',
        'regulation'        => 'Regulasi PBJ',
        'news'              => 'Berita',
        'event'             => 'Event',
    ];

    /**
     * Cek apakah user sudah bookmark resource tertentu.
     */
    public function exists(int $userId, string $type, int $resourceId): bool
    {
        return $this->db->table($this->table)
            ->where([
                'user_id'           => $userId,
                'bookmarkable_type' => $type,
                'bookmarkable_id'   => $resourceId,
            ])
            ->countAllResults() > 0;
    }

    /**
     * Toggle bookmark: insert jika belum ada, hapus jika sudah.
     * Mengembalikan state akhir: 'added' atau 'removed'.
     */
    public function toggle(int $userId, string $type, int $resourceId): string
    {
        if (! array_key_exists($type, self::SUPPORTED_TYPES)) {
            throw new \InvalidArgumentException('Tipe bookmark tidak didukung: ' . $type);
        }
        $existing = $this->db->table($this->table)
            ->where([
                'user_id'           => $userId,
                'bookmarkable_type' => $type,
                'bookmarkable_id'   => $resourceId,
            ])
            ->get()->getRowArray();
        if ($existing !== null) {
            $this->db->table($this->table)->where('id', (int) $existing['id'])->delete();
            return 'removed';
        }
        $this->db->table($this->table)->insert([
            'user_id'           => $userId,
            'bookmarkable_type' => $type,
            'bookmarkable_id'   => $resourceId,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
        return 'added';
    }

    /**
     * Hapus bookmark tertentu (untuk endpoint konsistensi).
     */
    public function remove(int $userId, string $type, int $resourceId): bool
    {
        return (bool) $this->db->table($this->table)
            ->where([
                'user_id'           => $userId,
                'bookmarkable_type' => $type,
                'bookmarkable_id'   => $resourceId,
            ])
            ->delete();
    }

    /**
     * Daftar bookmark user dengan grouping per tipe,
     * di-resolve ke label/judul manusiawi via UNION.
     */
    public function myBookmarks(int $userId): array
    {
        $rows = $this->db->table('bookmarks b')
            ->select('b.id, b.bookmarkable_type, b.bookmarkable_id, b.created_at')
            ->where('b.user_id', $userId)
            ->orderBy('b.bookmarkable_type', 'ASC')
            ->orderBy('b.created_at', 'DESC')
            ->get()->getResultArray();

        // Resolve judul resource dengan union per type.
        $grouped = [];
        foreach (self::SUPPORTED_TYPES as $type => $label) {
            $ids = array_values(array_filter(array_map(
                static fn ($r) => (int) $r['bookmarkable_id'],
                array_filter($rows, static fn ($r) => $r['bookmarkable_type'] === $type)
            )));
            if ($ids === []) {
                $grouped[$type] = ['label' => $label, 'items' => []];
                continue;
            }
            $titles = $this->resolveTitles($type, $ids);
            $items = [];
            foreach ($rows as $r) {
                if ($r['bookmarkable_type'] !== $type) {
                    continue;
                }
                $items[] = [
                    'id'         => (int) $r['id'],
                    'type'       => $type,
                    'label'      => $label,
                    'resource_id'=> (int) $r['bookmarkable_id'],
                    'title'      => $titles[(int) $r['bookmarkable_id']] ?? ('Item #' . (int) $r['bookmarkable_id']),
                    'created_at' => $r['created_at'],
                ];
            }
            $grouped[$type] = ['label' => $label, 'items' => $items];
        }
        return $grouped;
    }

    /**
     * Map id → title untuk tipe tertentu.
     */
    private function resolveTitles(string $type, array $ids): array
    {
        $map = [];
        switch ($type) {
            case 'knowledge_article':
            case 'knowledge_faq':
            case 'knowledge_case':
            case 'knowledge_template':
                $rows = $this->db->table('knowledge_articles')
                    ->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'knowledge_media':
                $rows = $this->db->table('knowledge_media')
                    ->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title'] ?: ('Media #' . $r['id']); }
                break;
            case 'program':
                $rows = $this->db->table('programs')->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'course':
                $rows = $this->db->table('courses')->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'learning_class':
                $rows = $this->db->table('learning_classes')->select('id, name AS title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'regulation':
                $rows = $this->db->table('regulations')->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'news':
                $rows = $this->db->table('news')->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
            case 'event':
                $rows = $this->db->table('events')->select('id, title')->whereIn('id', $ids)->get()->getResultArray();
                foreach ($rows as $r) { $map[(int) $r['id']] = $r['title']; }
                break;
        }
        return $map;
    }

    /**
     * Resolve route tujuan untuk link bookmark dari grouping.
     */
    public static function routeFor(string $type, int $resourceId): ?string
    {
        switch ($type) {
            case 'knowledge_article': return site_url('bunga-rampai/artikel/' . $resourceId);
            case 'knowledge_faq':     return site_url('bunga-rampai/faq/' . $resourceId);
            case 'knowledge_case':    return site_url('bunga-rampai/studi-kasus/' . $resourceId);
            case 'knowledge_template':return site_url('bunga-rampai/template-dokumen/' . $resourceId);
            case 'knowledge_media':   return site_url('bunga-rampai/media/' . $resourceId);
            case 'program':           return site_url('arena-belajar/program/' . $resourceId);
            case 'course':            return site_url('arena-belajar/course/' . $resourceId);
            case 'learning_class':    return site_url('arena-belajar/kelas/' . $resourceId);
            case 'regulation':        return site_url('regulasi/' . $resourceId);
            case 'news':              return site_url('berita/' . $resourceId);
            case 'event':             return site_url('event/' . $resourceId);
        }
        return null;
    }
}