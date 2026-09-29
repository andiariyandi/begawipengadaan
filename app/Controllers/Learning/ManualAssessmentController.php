<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\AssessmentResultModel;
use App\Services\ManualAssessmentService;
use RuntimeException;

class ManualAssessmentController extends BaseController
{
    public function show(int $enrollmentId, int $assessmentId)
    {
        try {
            (new ManualAssessmentService())->assertParticipantAccess(
                $enrollmentId,
                $assessmentId,
                (int) session()->get('user_id')
            );
        } catch (RuntimeException $e) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', $e->getMessage());
        }

        return view('learning/study/manual_assessment', [
            'title'        => 'Submission Assessment',
            'subTitle'     => 'Submission Assessment',
            'enrollmentId' => $enrollmentId,
            'assessmentId' => $assessmentId,
        ]);
    }

    public function submit(int $enrollmentId, int $assessmentId)
    {
        try {
            (new ManualAssessmentService())->submit(
                $enrollmentId,
                $assessmentId,
                (int) session()->get('user_id'),
                (string) $this->request->getPost('submission_text')
            );
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('learning.study.modules', $enrollmentId))
            ->with('message', 'Submission berhasil dikirim dan menunggu penilaian evaluator.');
    }

    public function pending()
    {
        return view('learning/evaluator/pending', [
            'title'        => 'Penilaian Assessment',
            'subTitle'     => 'Penilaian Assessment',
            'submissions'  => (new AssessmentResultModel())->pendingManualEvaluations(),
        ]);
    }

    public function evaluate(int $resultId)
    {
        $score = $this->request->getPost('score');
        if (! is_numeric($score)) {
            return redirect()->back()->with('error', 'Nilai wajib berupa angka.');
        }

        try {
            (new ManualAssessmentService())->evaluate(
                $resultId,
                (int) session()->get('user_id'),
                (float) $score,
                (string) $this->request->getPost('feedback')
            );
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('learning.evaluator.pending'))
            ->with('message', 'Assessment berhasil dinilai.');
    }
}
