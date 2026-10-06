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

    public function listRegistrations(?int $eventId = null, ?string $status = null): array
    {
        $builder = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title, e.start_at AS event_start_at,
                      u.username, u.name AS user_full_name, u.email')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->orderBy('er.registered_at', 'DESC');

        if ($eventId !== null) {
            $builder->where('er.event_id', $eventId);
        }
        if ($status !== null && $status !== '') {
            $builder->where('er.status', $status);
        }
        return $builder->get()->getResultArray();
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('event_registrations er')
            ->select('er.*, e.title AS event_title, e.start_at AS event_start_at, e.end_at AS event_end_at,
                      u.username, u.name AS user_full_name, u.email, u.phone')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->where('er.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }
}