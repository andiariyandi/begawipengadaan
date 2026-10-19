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
        'is_featured', 'carousel_enabled', 'carousel_order',
        'start_at', 'end_at', 'location', 'meeting_url',
        'registration_open_at', 'registration_close_at',
        'capacity', 'waitlist_enabled', 'certificate_enabled',
        'attendance_code', 'attendance_code_hash', 'attendance_open_at', 'attendance_close_at',
        'created_by', 'updated_by', 'cancelled_by', 'cancelled_at',
        'cancellation_reason',
    ];

    public function listEvents(
        ?string $status = null,
        ?string $keyword = null,
        ?string $mode = null,
        ?string $type = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $builder = $this->db->table('events e')
            ->select('e.*, u.username AS creator_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status IN (\'registered\', \'attended\', \'confirmed\', \'promoted\')) AS registered_count,
                      (SELECT COUNT(*) FROM event_registrations er JOIN attendances a ON a.event_registration_id = er.id WHERE er.event_id = e.id AND a.status = \'present\') AS present_count')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.deleted_at', null)
            ->orderBy('e.start_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('e.status', $status);
        }
        if ($mode !== null && $mode !== '') {
            $builder->where('e.mode', $mode);
        }
        if ($type !== null && $type !== '') {
            $builder->where('e.type', $type);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('e.title', $keyword)
                ->orLike('e.summary', $keyword)
                ->orLike('e.location', $keyword)
                ->groupEnd();
        }

        if ($limit !== null && $limit > 0) {
            $builder->limit($limit, $offset ?? 0);
        }

        return $builder->get()->getResultArray();
    }

    public function countEvents(
        ?string $status = null,
        ?string $keyword = null,
        ?string $mode = null,
        ?string $type = null
    ): int {
        $builder = $this->db->table('events e')
            ->where('e.deleted_at', null);

        if ($status !== null && $status !== '') {
            $builder->where('e.status', $status);
        }
        if ($mode !== null && $mode !== '') {
            $builder->where('e.mode', $mode);
        }
        if ($type !== null && $type !== '') {
            $builder->where('e.type', $type);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('e.title', $keyword)
                ->orLike('e.summary', $keyword)
                ->orLike('e.location', $keyword)
                ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('events e')
            ->select('e.*, u.username AS creator_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      fa.mime_type AS cover_mime_type, fa.original_name AS cover_original_name,
                      (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.status IN (\'registered\', \'attended\', \'confirmed\', \'promoted\')) AS registered_count,
                      (SELECT COUNT(*) FROM event_registrations er JOIN attendances a ON a.event_registration_id = er.id WHERE er.event_id = e.id AND a.status = \'present\') AS present_count')
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
            $check = $this->withDeleted()->where('slug', $slug)->first();
            if ($check === null || ($ignoreId !== null && (int) $check['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}