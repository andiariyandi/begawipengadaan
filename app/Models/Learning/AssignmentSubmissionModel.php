<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table            = 'assignment_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assignment_id',
        'enrollment_id',
        'attempt_number',
        'status',
        'content',
        'submitted_at',
        'graded_at',
        'graded_by',
        'score',
        'feedback',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
