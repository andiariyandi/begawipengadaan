<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\AnswerModel;
use App\Models\AssessmentModel;
use App\Models\ModuleModel;
use App\Models\QuestionModel;
use App\Services\AuditService;

class AssessmentController extends BaseController
{
    public function index(int $moduleId)
    {
        $module          = (new ModuleModel())->find($moduleId);
        $assessmentModel = new AssessmentModel();

        return view('learning/assessments/index', [
            'title'       => 'Assessment Modul',
            'subTitle'    => 'Assessment Modul',
            'module'      => $module,
            'assessments' => $assessmentModel->forModule($moduleId),
        ]);
    }

    public function store(int $moduleId)
    {
        $rules = [
            'title'         => 'required|max_length[200]',
            'type'          => 'required|in_list[quiz,exam,assignment]',
            'passing_score' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Judul, tipe, dan nilai kelulusan wajib diisi dengan benar.');
        }

        $assessmentModel = new AssessmentModel();

        $assessmentId = $assessmentModel->insert([
            'module_id'     => $moduleId,
            'title'         => $this->request->getPost('title'),
            'type'          => $this->request->getPost('type'),
            'passing_score' => $this->request->getPost('passing_score'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'assessments', $assessmentId);

        return redirect()->to(route_to('learning.assessments', $moduleId))->with('message', 'Assessment berhasil ditambahkan.');
    }

    /**
     * Kelola soal + opsi jawaban. UI pengisian di sini hanya untuk tipe
     * multiple_choice (auto-graded). Assessment exam/assignment diproses
     * melalui workflow submission dan evaluator manual.
     */
    public function questions(int $assessmentId)
    {
        $assessmentModel = new AssessmentModel();
        $assessment      = $assessmentModel->find($assessmentId);
        $questionModel   = new QuestionModel();

        return view('learning/assessments/questions', [
            'title'      => 'Kelola Soal',
            'subTitle'   => 'Kelola Soal',
            'assessment' => $assessment,
            'questions'  => $questionModel->withAnswers($assessmentId),
        ]);
    }

    public function storeQuestion(int $assessmentId)
    {
        $rules = [
            'question_text' => 'required',
            'question_type' => 'required|in_list[multiple_choice,essay]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Teks dan tipe soal wajib diisi.');
        }

        $questionId = (new QuestionModel())->insert([
            'assessment_id' => $assessmentId,
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'created_at'    => date('Y-m-d H:i:s'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'questions', $questionId);

        return redirect()->to(route_to('learning.assessments.questions', $assessmentId))->with('message', 'Soal berhasil ditambahkan.');
    }

    public function storeAnswer(int $assessmentId, int $questionId)
    {
        $rules = ['answer_text' => 'required'];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Teks jawaban wajib diisi.');
        }

        $answerModel = new AnswerModel();

        $answerId = $answerModel->insert([
            'question_id' => $questionId,
            'answer_text' => $this->request->getPost('answer_text'),
            'is_correct'  => $this->request->getPost('is_correct') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ], true);

        // Hanya boleh 1 jawaban benar per soal multiple_choice — kalau opsi
        // ini ditandai benar, pastikan opsi lain di soal yang sama tidak.
        if ($this->request->getPost('is_correct')) {
            $answerModel->where('question_id', $questionId)
                ->where('id !=', $answerId)
                ->set(['is_correct' => 0])
                ->update();
        }

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'answers', $answerId);

        return redirect()->to(route_to('learning.assessments.questions', $assessmentId))->with('message', 'Opsi jawaban berhasil ditambahkan.');
    }
}
