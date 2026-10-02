<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class LearningClassModel extends Model
{
    protected $table = 'learning_classes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'course_id',
        'code',
        'name',
        'status',
        'registration_open_at',
        'registration_close_at',
        'start_at',
        'end_at',
        'capacity',
        'certificate_enabled',
        'passing_grade',
        'quiz_weight',
        'assignment_weight',
        'post_test_weight',
        'created_by',
        'updated_by',
    ];
}
