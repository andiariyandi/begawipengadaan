<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'class_id', 'enrollment_status', 'final_status'];

    public function alreadyEnrolled(int $userId, int $classId): bool
    {
        return (bool) $this->where('user_id', $userId)->where('class_id', $classId)->first();
    }

    public function withClassDetail(): self
    {
        return $this->select('enrollments.*, classes.start_date, classes.end_date, courses.title as course_title, users.username')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->join('courses', 'courses.id = classes.course_id')
            ->join('users', 'users.id = enrollments.user_id');
    }

    public function pendingList(): array
    {
        return $this->withClassDetail()->where('enrollments.enrollment_status', 'pending')->findAll();
    }

    public function myEnrollments(int $userId): array
    {
        return $this->withClassDetail()->where('enrollments.user_id', $userId)->findAll();
    }
}
