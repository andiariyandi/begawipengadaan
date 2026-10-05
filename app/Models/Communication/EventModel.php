<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'title', 'slug', 'type', 'mode', 'status', 'visibility',
        'summary', 'description', 'cover_file_id',
        'start_at', 'end_at', 'location', 'meeting_url',
        'registration_open_at', 'registration_close_at',
        'capacity', 'waitlist_enabled', 'certificate_enabled',
        'created_by', 'updated_by', 'cancelled_by', 'cancelled_at',
        'cancellation_reason',
    ];

    public function listEvents(?string $status = null, ?string $keyword = null): array
    {
        $builder = $this->db->table('events e')
            ->select('e.*, u.username AS creator_name,
                      fa.path AS cover_path, fa.disk AS cover_disk')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.deleted_at', null)
            ->orderBy('e.start_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('e.status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('e.title', $keyword)
                ->orLike('e.summary', $keyword)
                ->orLike('e.location', $keyword)
                ->groupEnd();
        }
        return $builder->get()->getResultArray();
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('events e')
            ->select('e.*, u.username AS creator_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      fa.mime_type AS cover_mime_type, fa.original_name AS cover_original_name')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }

    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'event-' . date('YmdHis');
        }
        $base = $slug;
        $i = 1;
        while (true) {
            $check = $this->where('slug', $slug)->first();
            if ($check === null || ($ignoreId !== null && (int) $check['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}