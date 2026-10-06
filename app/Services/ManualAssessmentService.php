<?php

namespace App\Services;

use App\Models\AssessmentModel;
use App\Models\AssessmentResultModel;
use App\Models\EnrollmentModel;
use App\Models\ModuleModel;
use RuntimeException;

class ManualAssessmentService
{
    protected AssessmentModel $assessmentModel;
    protected AssessmentResultModel $resultModel;
    protected EnrollmentModel $enrollmentModel;
    protected ModuleModel $moduleModel;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->resultModel     = new AssessmentResultModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->moduleModel     = new ModuleModel();
    }

    public function submit(int $enrollmentId, int $assessmentId, int $userId, string $submission): int
    {
        $this->ownedAssessment($enrollmentId, $assessmentId, $userId);

        if (trim($submission) === '') {
            throw new RuntimeException('Jawaban atau uraian submission wajib diisi.');
        }

        $latest = $this->resultModel->latestManualResult($enrollmentId, $assessmentId);
        if ($latest && $latest['evaluation_status'] === 'pending') {
            throw new RuntimeException('Submission sebelumnya masih menunggu penilaian evaluator.');
        }

        $resultId = (int) $this->resultModel->insert([
            'enrollment_id'   => $enrollmentId,
            'assessment_id'   => $assessmentId,
            'total_question'  => 0,
            'correct_answer'  => 0,
            'score'           => 0,
            'is_passed'       => 0,
            'submission_text' => trim($submission),
            'evaluation_status' => 'pending',
            'created_at'      => date('Y-m-d H:i:s'),
        ], true);

        (new ProgressService())->recomputeFinalStatus($enrollmentId);

        return $resultId;
    }

    public function evaluate(int $resultId, int $evaluatorId, float $score, string $feedback): void
    {
        if ($score < 0 || $score > 100) {
            throw new RuntimeException('Nilai evaluator harus berada pada rentang 0 sampai 100.');
        }

        $result = $this->resultModel
            ->select('assessment_results.*, assessments.type, assessments.passing_score')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->find($resultId);

        if (! $result || ! in_array($result['type'], ['exam', 'assignment'], true)) {
            throw new RuntimeException('Submission manual tidak ditemukan.');
        }
        if ($result['evaluation_status'] !== 'pending') {
            throw new RuntimeException('Submission ini sudah dinilai.');
        }

        $this->resultModel->update($resultId, [
            'score'              => round($score, 2),
            'is_passed'          => $score >= (float) $result['passing_score'] ? 1 : 0,
            'evaluation_status'  => 'evaluated',
            'evaluator_id'       => $evaluatorId,
            'evaluator_feedback' => trim($feedback) !== '' ? trim($feedback) : null,
            'evaluated_at'       => date('Y-m-d H:i:s'),
        ]);

        (new ProgressService())->recomputeFinalStatus((int) $result['enrollment_id']);
    }

    public function assertParticipantAccess(int $enrollmentId, int $assessmentId, int $userId): void
    {
        $this->ownedAssessment($enrollmentId, $assessmentId, $userId);
    }

    private function ownedAssessment(int $enrollmentId, int $assessmentId, int $userId): array
    {
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        $assessment = $this->assessmentModel->find($assessmentId);
        $module     = $assessment ? $this->moduleModel->find((int) $assessment['module_id']) : null;

        if (
            ! $enrollment
            || (int) $enrollment['user_id'] !== $userId
            || $enrollment['enrollment_status'] !== 'approved'
            || ! $assessment
            || ! in_array($assessment['type'], ['exam', 'assignment'], true)
            || ! $module
            || (int) $module['class_id'] !== (int) $enrollment['class_id']
        ) {
            throw new RuntimeException('Assessment tidak tersedia untuk enrollment ini.');
        }

        return [$enrollment, $assessment];
    }
}
