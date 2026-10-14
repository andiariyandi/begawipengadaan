<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class EventRegistrationModel extends Model
{
    protected $table         = 'event_registrations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'event_id', 'user_id', 'status',
        'registered_at', 'cancelled_at', 'promoted_at', 'status_reason',
    ];

    public function listRegistrations(
        ?int $eventId = null,
        ?string $status = null,
        ?string $keyword = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $builder = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title, e.start_at AS event_start_at, e.end_at AS event_end_at,
                      e.mode AS event_mode, e.type AS event_type,
                      u.username, u.name AS user_full_name, u.email, u.phone,
                      a.status AS attendance_status, a.recorded_at AS attendance_recorded_at, a.notes AS attendance_notes,
                      c.id AS certificate_id, c.certificate_number, c.status AS certificate_status')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_id = er.id AND c.source_type = \'event_registration\'', 'left')
            ->orderBy('er.registered_at', 'DESC');

        if ($eventId !== null && $eventId > 0) {
            $builder->where('er.event_id', $eventId);
        }
        if ($status !== null && $status !== '') {
            $builder->where('er.status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('u.phone', $keyword)
                ->orLike('e.title', $keyword)
                ->groupEnd();
        }

        if ($limit !== null && $limit > 0) {
            $builder->limit($limit, $offset ?? 0);
        }

        return $builder->get()->getResultArray();
    }

    public function countRegistrations(
        ?int $eventId = null,
        ?string $status = null,
        ?string $keyword = null
    ): int {
        $builder = $this->db->table('event_registrations er')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left');

        if ($eventId !== null && $eventId > 0) {
            $builder->where('er.event_id', $eventId);
        }
        if ($status !== null && $status !== '') {
            $builder->where('er.status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('u.phone', $keyword)
                ->orLike('e.title', $keyword)
                ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title, e.start_at AS event_start_at, e.end_at AS event_end_at,
                      e.mode AS event_mode, e.type AS event_type, e.location AS event_location, e.meeting_url,
                      e.capacity AS event_capacity, e.status AS event_status,
                      u.username, u.name AS user_full_name, u.email, u.phone,
                      a.id AS attendance_id, a.status AS attendance_status, a.recorded_at AS attendance_recorded_at,
                      a.notes AS attendance_notes, rec.username AS recorder_name,
                      c.id AS certificate_id, c.certificate_number, c.status AS certificate_status, c.issued_at AS certificate_issued_at')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('users rec', 'rec.id = a.recorded_by', 'left')
            ->join('certificates c', 'c.source_id = er.id AND c.source_type = \'event_registration\'', 'left')
            ->where('er.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }
}