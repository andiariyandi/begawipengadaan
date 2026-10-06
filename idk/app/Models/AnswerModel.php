<?php

namespace App\Models;

use CodeIgniter\Model;

class AnswerModel extends Model
{
    protected $table         = 'answers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // tabel answers hanya punya created_at, tanpa updated_at
    protected $allowedFields = ['question_id', 'answer_text', 'is_correct', 'created_at'];

    protected $validationRules = [
        'answer_text' => 'required',
    ];

    public function forQuestion(int $questionId): array
    {
        return $this->where('question_id', $questionId)->orderBy('id', 'ASC')->findAll();
    }

    public function correctAnswerId(int $questionId): ?int
    {
        $row = $this->where('question_id', $questionId)->where('is_correct', 1)->first();
        return $row ? (int) $row['id'] : null;
    }
}
