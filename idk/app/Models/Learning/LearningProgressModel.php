<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class LearningProgressModel extends Model
{
    protected $table         = 'learning_progress';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'enrollment_id', 'lesson_id', 'status',
        'started_at', 'completed_at', 'progress_percent',
    ];
}