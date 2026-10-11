<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
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

    public function alreadyEnrolled(int $userId, int $classId): bool
    {
        return (bool) $this->where('user_id', $userId)->where('class_id', $classId)->first();
    }

    public function withClassDetail(): self
    {
        return $this->select('enrollments.*, learning_classes.name AS class_name, learning_classes.code AS class_code, courses.title AS course_title, users.name AS user_name, users.username')
            ->join('learning_classes', 'learning_classes.id = enrollments.class_id', 'left')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left');
    }

    public function pendingList(): array
    {
        return $this->withClassDetail()->where('enrollments.status', 'pending')->findAll();
    }

    public function myEnrollments(int $userId): array
    {
        return $this->withClassDetail()->where('enrollments.user_id', $userId)->findAll();
    }
}
