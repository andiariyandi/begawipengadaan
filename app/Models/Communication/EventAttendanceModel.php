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

    public function listAttendance(?string $status = null): array
    {
        $builder = $this->db->table('attendances a')
            ->select('a.*, er.event_id, er.user_id, er.status AS registration_status,
                      e.title AS event_title, e.start_at AS event_start_at,
                      u.username, u.name AS user_full_name,
                      r.username AS recorder_username')
            ->join('event_registrations er', 'er.id = a.event_registration_id', 'left')
            ->join('events e', 'e.id = er.event_id', 'left')
            ->join('users u', 'u.id = er.user_id', 'left')
            ->join('users r', 'r.id = a.recorded_by', 'left')
            ->orderBy('a.recorded_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('a.status', $status);
        }
        return $builder->get()->getResultArray();
    }
}