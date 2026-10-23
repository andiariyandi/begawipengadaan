<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'class_id',
        'user_id',
        'status',
        'enrolled_at',
        'activated_at',
        'completed_at',
        'final_score',
        'passed_at',
        'certificate_eligible_at',
        'withdrawn_at',
        'cancelled_at',
    ];

    /**
     * Cek apakah user sudah terdaftar pada kelas (status apapun).
     * Dipakai ArenaBelajarController::enroll() untuk mencegah duplikat.
     */
    public function alreadyEnrolled(int $userId, int $classId): bool
    {
        return $this->db->table($this->table)
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->countAllResults() > 0;
    }

    /**
     * Daftar enrollment + class + course + program.
     * Dipakai EnrollmentController administrator (dashboard) untuk list & detail.
     */
    public function withClassDetail()
    {
        return $this->db->table('enrollments e')
            ->select('e.*, lc.code AS class_code, lc.name AS class_name, lc.status AS class_status, courses.title AS course_title, programs.title AS program_title, users.name AS user_name, users.username, users.email')
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses', 'courses.id = lc.course_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->join('users', 'users.id = e.user_id', 'left');
    }

    /**
     * Daftar enrollment pending.
     * Dipakai antrian approval.
     */
    public function pendingList(): array
    {
        return $this->db->table('enrollments e')
            ->select('e.*, lc.name AS class_name, lc.code AS class_code, users.name AS user_name, users.email')
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('users', 'users.id = e.user_id', 'left')
            ->where('e.status', 'pending')
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Daftar enrollment milik user.
     */
    public function myEnrollments(int $userId): array
    {
        return $this->db->table('enrollments')
            ->where('user_id', $userId)
            ->orderBy('enrolled_at', 'DESC')
            ->get()->getResultArray();
    }
}
