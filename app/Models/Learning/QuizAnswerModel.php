<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class QuizAnswerModel extends Model
{
    protected $table         = 'quiz_answers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'attempt_id', 'question_id', 'answer_payload', 'is_correct', 'score_awarded',
    ];
}