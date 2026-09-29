<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table          = 'classes';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['course_id', 'trainer_id', 'start_date', 'end_date', 'quota', 'status'];

    protected $validationRules = [
        'trainer_id' => 'required|is_natural_no_zero',
        'start_date' => 'required|valid_date',
        'end_date'   => 'required|valid_date',
        'quota'      => 'required|is_natural_no_zero',
    ];

    public function withCourseTitle()
    {
        return $this->select('classes.*, courses.title as course_title, users.username as trainer_name')
            ->join('courses', 'courses.id = classes.course_id')
            ->join('users', 'users.id = classes.trainer_id');
    }

    public function countApproved(int $classId): int
    {
        return (new \App\Models\EnrollmentModel())
            ->where('class_id', $classId)
            ->where('enrollment_status', 'approved')
            ->countAllResults();
    }

    public function isFull(int $classId): bool
    {
        $class = $this->find($classId);
        return $class && $this->countApproved($classId) >= $class['quota'];
    }
}
