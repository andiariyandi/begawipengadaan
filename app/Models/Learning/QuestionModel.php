<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table         = 'quiz_questions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'quiz_id', 'type', 'prompt', 'points', 'sort_order', 'explanation',
    ];
}