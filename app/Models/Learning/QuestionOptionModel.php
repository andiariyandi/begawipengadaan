<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class QuestionOptionModel extends Model
{
    protected $table         = 'quiz_options';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'question_id', 'option_text', 'is_correct', 'sort_order',
    ];
}