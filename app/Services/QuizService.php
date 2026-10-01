<?php

namespace App\Services;

use App\Models\AnswerModel;
use App\Models\AssessmentModel;
use App\Models\AssessmentResultModel;
use App\Models\EnrollmentModel;
use App\Models\ModuleModel;
use App\Models\QuestionModel;
use RuntimeException;

/**
 * Auto-grading untuk assessment tipe QUIZ dengan soal multiple_choice saja.
 * Tipe exam/assignment (termasuk soal essay) sengaja tidak ditangani di
 * sini — butuh alur penilaian manual Evaluator yang belum ada di ERD.
 */
class QuizService
{
    protected AssessmentModel $assessmentModel;
    protected QuestionModel $questionModel;
    protected AnswerModel $answerModel;
    protected AssessmentResultModel $resultModel;
    protected EnrollmentModel $enrollmentModel;
    protected ModuleModel $moduleModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->questionModel   = new QuestionModel();
        $this->answerModel     = new AnswerModel();
        $this->resultModel     = new AssessmentResultModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->moduleModel     = new ModuleModel();
        $this->auditService    = new AuditService();
    }

    /**
     * @param array<int,int> $submittedAnswers [question_id => answer_id]
     */
    public function submit(int $enrollmentId, int $assessmentId, array $submittedAnswers, ?int $actedBy): array
    {
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        $assessment = $this->assessmentModel->find($assessmentId);

        if (! $enrollment || $enrollment['enrollment_status'] !== 'approved') {
            throw new RuntimeException('Enrollment tidak valid atau belum disetujui.');
        }

        if (! $assessment || $assessment['type'] !== 'quiz') {
            throw new RuntimeException('Assessment tidak valid atau bukan tipe quiz.');
        }

        $module = $this->moduleModel->find((int) $assessment['module_id']);

        if (! $module || (int) $module['class_id'] !== (int) $enrollment['class_id']) {
            throw new RuntimeException('Quiz tidak terdaftar pada kelas enrollment ini.');
        }

        if ($assessment['passing_score'] === null || $assessment['passing_score'] === '' || (float) $assessment['passing_score'] < 0 || (float) $assessment['passing_score'] > 100) {
            throw new RuntimeException('Nilai kelulusan quiz belum dikonfigurasi dengan benar.');
        }

        $questions = $this->questionModel->forAssessment($assessmentId);

        if (empty($questions)) {
            throw new RuntimeException('Quiz ini belum memiliki soal.');
        }

        $total   = count($questions);
        $correct = 0;
        $graded  = 0; // BARU: hanya soal multiple_choice yang dinilai otomatis
        $questionIds = array_map(static fn(array $question): int => (int) $question['id'], $questions);

        foreach ($questions as $question) {
            // Soal essay tidak ikut dihitung otomatis — dilewati (nilai 0 untuk soal itu).
            if ($question['question_type'] !== 'multiple_choice') {
                continue;
            }
            $graded++; // BARU

            $selected     = $submittedAnswers[$question['id']] ?? null;
            $correctAnsId = $this->answerModel->correctAnswerId((int) $question['id']);

            if (
                $selected !== null
                && $correctAnsId !== null
                && $this->answerBelongsToQuestion((int) $selected, (int) $question['id'])
                && (int) $selected === $correctAnsId
            ) {
                $correct++;
            }
        }
        if ($graded === 0) {
            throw new RuntimeException('Quiz belum memiliki soal pilihan ganda yang dapat dinilai.');
        }

        $submittedAnswers = array_intersect_key($submittedAnswers, array_flip($questionIds));
        $score = round(($correct / $graded) * 100, 2);
        $isPassed = $score >= (float) $assessment['passing_score'];

        $resultId = $this->resultModel->insert([
            'enrollment_id'    => $enrollmentId,
            'assessment_id'    => $assessmentId,
            'total_question'   => $total,
            'correct_answer'   => $correct,
            'score'            => $score,
            'is_passed'        => $isPassed ? 1 : 0,
            'answers_snapshot' => json_encode($submittedAnswers),
            'created_at'       => date('Y-m-d H:i:s'),
        ], true);

        $this->auditService->log($actedBy, 'submit_quiz', 'lms', 'assessment_results', $resultId, [
            'assessment_id' => $assessmentId,
            'score'         => $score,
            'is_passed'     => $isPassed,
        ]);

        return [
            'score'     => $score,
            'is_passed' => $isPassed,
            'correct'   => $correct,
            'total'     => $total,
        ];
    }

    private function answerBelongsToQuestion(int $answerId, int $questionId): bool
    {
        return (bool) $this->answerModel
            ->where('id', $answerId)
            ->where('question_id', $questionId)
            ->first();
    }
}
