<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;

class AssessmentManagementController extends BaseController
{
    protected LearningClassModel $classModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel = new LearningClassModel();
    }

    public function index(): string
    {
        $db = db_connect();
        $tab = $this->request->getGet('tab') ?: 'quizzes';
        $classId = (int) $this->request->getGet('class_id');
        $keyword = trim((string) $this->request->getGet('keyword'));

        // 1. Quizzes Query
        $quizBuilder = $db->table('quizzes q')
            ->select('q.*, lc.name AS class_name, lc.code AS class_code, c.title AS course_title, l.title AS lesson_title,
                      (SELECT COUNT(1) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count,
                      (SELECT COUNT(1) FROM quiz_attempts qa WHERE qa.quiz_id = q.id) AS attempt_count')
            ->join('learning_classes lc', 'lc.id = q.class_id', 'left')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('lessons l', 'l.id = q.lesson_id', 'left')
            ->orderBy('q.created_at', 'DESC');

        if ($classId > 0) {
            $quizBuilder->where('q.class_id', $classId);
        }
        if ($keyword !== '') {
            $quizBuilder->groupStart()
                ->like('q.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->orLike('c.title', $keyword)
                ->groupEnd();
        }
        $quizzes = $quizBuilder->get()->getResultArray();

        // 2. Assignments Query
        $assignBuilder = $db->table('assignments a')
            ->select('a.*, lc.name AS class_name, lc.code AS class_code, c.title AS course_title, l.title AS lesson_title,
                      (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) AS submission_count,
                      (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.status = "submitted") AS pending_grade_count')
            ->join('learning_classes lc', 'lc.id = a.class_id', 'left')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('lessons l', 'l.id = a.lesson_id', 'left')
            ->orderBy('a.created_at', 'DESC');

        if ($classId > 0) {
            $assignBuilder->where('a.class_id', $classId);
        }
        if ($keyword !== '') {
            $assignBuilder->groupStart()
                ->like('a.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->groupEnd();
        }
        $assignments = $assignBuilder->get()->getResultArray();

        // 3. Submissions Query
        $subBuilder = $db->table('assignment_submissions asub')
            ->select('asub.*, a.title AS assignment_title, u.name AS student_name, u.username,
                      lc.name AS class_name, evaluator.name AS evaluator_name')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('enrollments e', 'e.id = asub.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('users evaluator', 'evaluator.id = asub.graded_by', 'left')
            ->orderBy('asub.created_at', 'DESC')
            ->limit(30);

        if ($classId > 0) {
            $subBuilder->where('lc.id', $classId);
        }
        $submissions = $subBuilder->get()->getResultArray();

        // Stats
        $stats = [
            'total_quizzes'     => (int) $db->table('quizzes')->countAllResults(),
            'total_questions'   => (int) $db->table('quiz_questions')->countAllResults(),
            'total_assignments' => (int) $db->table('assignments')->countAllResults(),
            'total_submissions' => (int) $db->table('assignment_submissions')->countAllResults(),
        ];

        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        return view('dashboard/learning/assessments/management', [
            'title'         => 'Assessment & Penilaian | Dashboard BegawiPBJ',
            'pageTitle'     => 'Assessment & Penilaian Pembelajaran',
            'currentTab'    => $tab,
            'quizzes'       => $quizzes,
            'assignments'   => $assignments,
            'submissions'   => $submissions,
            'stats'         => $stats,
            'classes'       => $classes,
            'filters'       => [
                'class_id' => $classId,
                'keyword'  => $keyword,
                'tab'      => $tab,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }

    public function storeQuiz()
    {
        $classId = (int) $this->request->getPost('class_id');
        $title = trim((string) $this->request->getPost('title'));
        $type = $this->request->getPost('type') ?: 'lesson_quiz';

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Kelas dan judul quiz wajib diisi.');
        }

        $db = db_connect();
        $db->table('quizzes')->insert([
            'class_id'      => $classId,
            'lesson_id'     => $this->request->getPost('lesson_id') ?: null,
            'type'          => $type,
            'title'         => $title,
            'instructions'  => $this->request->getPost('instructions') ?: null,
            'attempt_limit' => (int) ($this->request->getPost('attempt_limit') ?: 1),
            'is_required'   => $this->request->getPost('is_required') ? 1 : 0,
            'status'        => $this->request->getPost('status') ?: 'published',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=quizzes'))
            ->with('success', 'Kuis baru berhasil dibuat.');
    }

    public function storeAssignment()
    {
        $classId = (int) $this->request->getPost('class_id');
        $title = trim((string) $this->request->getPost('title'));

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Kelas dan judul tugas wajib diisi.');
        }

        $userId = session()->get('user_id');
        $db = db_connect();
        $db->table('assignments')->insert([
            'class_id'     => $classId,
            'lesson_id'    => $this->request->getPost('lesson_id') ?: null,
            'title'        => $title,
            'instructions' => $this->request->getPost('instructions') ?: null,
            'due_at'       => $this->request->getPost('due_at') ?: null,
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'max_attempts' => (int) ($this->request->getPost('max_attempts') ?: 1),
            'status'       => $this->request->getPost('status') ?: 'published',
            'created_by'   => $userId ? (int) $userId : null,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=assignments'))
            ->with('success', 'Tugas baru berhasil dibuat.');
    }

    public function questionBank(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('quiz_questions qq')
            ->select('qq.*, q.title AS quiz_title, lc.name AS class_name')
            ->join('quizzes q', 'q.id = qq.quiz_id', 'left')
            ->join('learning_classes lc', 'lc.id = q.class_id', 'left')
            ->orderBy('qq.id', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('qq.question_text', $keyword)
                ->orLike('q.title', $keyword)
                ->groupEnd();
        }

        $questions = $builder->get()->getResultArray();

        return view('dashboard/learning/assessments/question_bank', [
            'title'         => 'Bank Soal | Dashboard BegawiPBJ',
            'pageTitle'     => 'Bank Soal Evaluasi',
            'questions'     => $questions,
            'filters'       => ['keyword' => $keyword],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}
