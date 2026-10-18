<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table         = 'quizzes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'class_id', 'lesson_id', 'type', 'title', 'instructions',
        'duration_minutes', 'attempt_limit', 'is_required', 'status',
    ];
}