<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class QuizAttemptModel extends Model
{
    protected $table         = 'quiz_attempts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'quiz_id', 'enrollment_id', 'attempt_number', 'status',
        'started_at', 'submitted_at', 'graded_at', 'score',
        'invalidated_by', 'invalidated_reason',
    ];
}