<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentResultModel extends Model
{
    protected $table         = 'assessment_results';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // hanya created_at — setiap baris adalah 1 percobaan, immutable
    protected $allowedFields = [
        'enrollment_id', 'assessment_id', 'total_question', 'correct_answer',
        'score', 'is_passed', 'answers_snapshot', 'submission_text',
        'evaluation_status', 'evaluator_id', 'evaluator_feedback',
        'evaluated_at', 'created_at',
    ];

    /**
     * Percobaan terbaru peserta untuk satu assessment. Dipakai sebagai
     * status "resmi" (lulus/tidak) assessment tersebut — lihat catatan
     * di migration soal kenapa tidak unique per (enrollment, assessment).
     */
    public function latestAttempt(int $enrollmentId, int $assessmentId): ?array
    {
        return $this->where('enrollment_id', $enrollmentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function historyForEnrollment(int $enrollmentId, int $assessmentId): array
    {
        return $this->where('enrollment_id', $enrollmentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function pendingManualEvaluations(): array
    {
        return $this->select('assessment_results.*, assessments.title, assessments.type, assessments.passing_score, users.username, courses.title AS course_title')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->join('enrollments', 'enrollments.id = assessment_results.enrollment_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->join('courses', 'courses.id = classes.course_id')
            ->whereIn('assessments.type', ['exam', 'assignment'])
            ->where('assessment_results.evaluation_status', 'pending')
            ->orderBy('assessment_results.created_at', 'ASC')
            ->findAll();
    }

    public function latestManualResult(int $enrollmentId, int $assessmentId): ?array
    {
        return $this->where('enrollment_id', $enrollmentId)
            ->where('assessment_id', $assessmentId)
            ->whereIn('evaluation_status', ['pending', 'evaluated'])
            ->orderBy('id', 'DESC')
            ->first();
    }
}
