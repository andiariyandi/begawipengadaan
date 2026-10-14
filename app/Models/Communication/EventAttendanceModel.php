<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class EventAttendanceModel extends Model
{
    protected $table         = 'attendances';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'event_registration_id', 'status', 'recorded_by',
        'recorded_at', 'notes',
    ];

    public function listAttendance(
        ?string $status = null,
        ?int $eventId = null,
        ?string $keyword = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $builder = $this->db->table('attendances a')
            ->select('a.*, er.event_id, er.user_id, er.status AS registration_status, er.registered_at,
                      e.title AS event_title, e.start_at AS event_start_at, e.mode AS event_mode, e.type AS event_type,
                      u.username, u.name AS user_full_name, u.email, u.phone,
                      r.username AS recorder_username, r.name AS recorder_name')
            ->join('event_registrations er', 'er.id = a.event_registration_id', 'left')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->join('users r', 'r.id = a.recorded_by', 'left')
            ->orderBy('a.recorded_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('a.status', $status);
        }
        if ($eventId !== null && $eventId > 0) {
            $builder->where('er.event_id', $eventId);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('e.title', $keyword)
                ->groupEnd();
        }

        if ($limit !== null && $limit > 0) {
            $builder->limit($limit, $offset ?? 0);
        }

        return $builder->get()->getResultArray();
    }

    public function countAttendance(
        ?string $status = null,
        ?int $eventId = null,
        ?string $keyword = null
    ): int {
        $builder = $this->db->table('attendances a')
            ->join('event_registrations er', 'er.id = a.event_registration_id', 'left')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left');

        if ($status !== null && $status !== '') {
            $builder->where('a.status', $status);
        }
        if ($eventId !== null && $eventId > 0) {
            $builder->where('er.event_id', $eventId);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('e.title', $keyword)
                ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }
}