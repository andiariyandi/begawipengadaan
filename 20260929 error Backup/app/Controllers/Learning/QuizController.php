<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\AssessmentModel;
use App\Models\EnrollmentModel;
use App\Models\ModuleModel;
use App\Models\QuestionModel;
use App\Services\ProgressService;
use App\Services\QuizService;
use RuntimeException;

class QuizController extends BaseController
{
    /**
     * Pastikan enrollment ini milik user yang login, sudah approved, dan
     * assessment yang dituju benar-benar milik kelas dari enrollment itu.
     */
    private function authorize(int $enrollmentId, int $assessmentId): array
    {
        $enrollment = (new EnrollmentModel())->find($enrollmentId);

        if (
            ! $enrollment
            || (int) $enrollment['user_id'] !== (int) session()->get('user_id')
            || $enrollment['enrollment_status'] !== 'approved'
        ) {
            throw new RuntimeException('Anda tidak memiliki akses ke quiz ini.');
        }

        $assessment = (new AssessmentModel())->find($assessmentId);
        $module     = $assessment ? (new ModuleModel())->find((int) $assessment['module_id']) : null;

        if (
            ! $assessment
            || $assessment['type'] !== 'quiz'
            || ! $module
            || (int) $module['class_id'] !== (int) $enrollment['class_id']
        ) {
            throw new RuntimeException('Quiz tidak ditemukan pada kelas ini.');
        }

        return ['enrollment' => $enrollment, 'assessment' => $assessment];
    }

    public function take(int $enrollmentId, int $assessmentId)
    {
        try {
            $ctx = $this->authorize($enrollmentId, $assessmentId);
        } catch (RuntimeException $e) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', $e->getMessage());
        }

        $questions = (new QuestionModel())->withAnswers($assessmentId);

        return view('learning/study/quiz_take', [
            'title'        => 'Kerjakan Quiz',
            'subTitle'     => 'Kerjakan Quiz',
            'assessment'   => $ctx['assessment'],
            'questions'    => $questions,
            'enrollmentId' => $enrollmentId,
        ]);
    }

    public function submit(int $enrollmentId, int $assessmentId)
    {
        try {
            $this->authorize($enrollmentId, $assessmentId);
        } catch (RuntimeException $e) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', $e->getMessage());
        }

        $answers = $this->request->getPost('answers') ?? []; // [question_id => answer_id]
        $answers = array_map('intval', $answers);

        try {
            $result = (new QuizService())->submit($enrollmentId, $assessmentId, $answers, session()->get('user_id'));
            (new ProgressService())->recomputeFinalStatus($enrollmentId);
        } catch (RuntimeException $e) {
            return redirect()->to(route_to('learning.study.modules', $enrollmentId))->with('error', $e->getMessage());
        }

        return view('learning/study/quiz_result', [
            'title'        => 'Hasil Quiz',
            'subTitle'     => 'Hasil Quiz',
            'result'       => $result,
            'enrollmentId' => $enrollmentId,
        ]);
    }
}
