<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table         = 'questions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // tabel questions hanya punya created_at, tanpa updated_at
    protected $allowedFields = ['assessment_id', 'question_text', 'question_type', 'created_at'];

    protected $validationRules = [
        'question_text' => 'required',
        'question_type' => 'required|in_list[multiple_choice,essay]',
    ];

    public function forAssessment(int $assessmentId): array
    {
        return $this->where('assessment_id', $assessmentId)->orderBy('id', 'ASC')->findAll();
    }

    public function withAnswers(int $assessmentId): array
    {
        $questions   = $this->forAssessment($assessmentId);
        $answerModel = new AnswerModel();

        foreach ($questions as &$question) {
            $question['answers'] = $answerModel->forQuestion($question['id']);
        }
        unset($question);

        return $questions;
    }
}
