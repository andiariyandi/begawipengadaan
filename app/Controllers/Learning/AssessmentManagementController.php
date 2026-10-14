<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LessonModel;
use App\Services\EvaluatorService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class AssessmentManagementController extends BaseController
{
    protected LearningClassModel $classModel;
    protected LearningModuleModel $moduleModel;
    protected LessonModel $lessonModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel  = new LearningClassModel();
        $this->moduleModel = new LearningModuleModel();
        $this->lessonModel = new LessonModel();
    }

    public function index(): string
    {
        $db = db_connect();

        $tab      = (string) ($this->request->getGet('tab') ?: 'quizzes');
        $classId  = (int) $this->request->getGet('class_id');
        $keyword  = trim((string) $this->request->getGet('keyword'));
        $status   = trim((string) $this->request->getGet('status'));
        $quizType = trim((string) $this->request->getGet('quiz_type'));
        $perPage  = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page     = max(1, (int) ($this->request->getGet('page') ?: 1));

        // 1. Quizzes Query
        $quizBuilder = $db->table('quizzes q')
            ->select('q.*, lc.name AS class_name, lc.code AS class_code, c.title AS course_title,
                      l.title AS lesson_title, lm.title AS module_title,
                      (SELECT COUNT(1) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count,
                      (SELECT COALESCE(SUM(points), 0) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS total_points,
                      (SELECT COUNT(1) FROM quiz_attempts qa WHERE qa.quiz_id = q.id) AS attempt_count')
            ->join('learning_classes lc', 'lc.id = q.class_id', 'left')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('lessons l', 'l.id = q.lesson_id', 'left')
            ->join('learning_modules lm', 'lm.id = l.module_id', 'left')
            ->orderBy('q.created_at', 'DESC');

        if ($classId > 0) {
            $quizBuilder->where('q.class_id', $classId);
        }
        if ($quizType !== '') {
            $quizBuilder->where('q.type', $quizType);
        }
        if ($status !== '') {
            $quizBuilder->where('q.status', $status);
        }
        if ($keyword !== '') {
            $quizBuilder->groupStart()
                ->like('q.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->orLike('c.title', $keyword)
                ->groupEnd();
        }

        // 2. Assignments Query
        $assignBuilder = $db->table('assignments a')
            ->select('a.*, lc.name AS class_name, lc.code AS class_code, c.title AS course_title,
                      l.title AS lesson_title, lm.title AS module_title,
                      (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) AS submission_count,
                      (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.status = "submitted") AS pending_grade_count')
            ->join('learning_classes lc', 'lc.id = a.class_id', 'left')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('lessons l', 'l.id = a.lesson_id', 'left')
            ->join('learning_modules lm', 'lm.id = l.module_id', 'left')
            ->orderBy('a.created_at', 'DESC');

        if ($classId > 0) {
            $assignBuilder->where('a.class_id', $classId);
        }
        if ($status !== '') {
            $assignBuilder->where('a.status', $status);
        }
        if ($keyword !== '') {
            $assignBuilder->groupStart()
                ->like('a.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->groupEnd();
        }

        // 3. Submissions Query
        $subBuilder = $db->table('assignment_submissions asub')
            ->select('asub.*, a.title AS assignment_title, a.due_at, a.class_id,
                      u.name AS student_name, u.username, u.email AS student_email,
                      lc.name AS class_name, evaluator.name AS evaluator_name,
                      lm.title AS module_title,
                      (SELECT COUNT(1) FROM assignment_submission_files asf WHERE asf.submission_id = asub.id) AS file_count')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('enrollments e', 'e.id = asub.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('lessons l', 'l.id = a.lesson_id', 'left')
            ->join('learning_modules lm', 'lm.id = l.module_id', 'left')
            ->join('users evaluator', 'evaluator.id = asub.graded_by', 'left')
            ->orderBy('asub.created_at', 'DESC');

        if ($classId > 0) {
            $subBuilder->where('lc.id', $classId);
        }
        if ($status !== '') {
            $subBuilder->where('asub.status', $status);
        }
        if ($keyword !== '') {
            $subBuilder->groupStart()
                ->like('a.title', $keyword)
                ->orLike('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->groupEnd();
        }

        // 4. Bank Soal Query (jika tab == 'bank_soal')
        $questionBuilder = $db->table('quiz_questions qq')
            ->select('qq.*, q.title AS quiz_title, q.type AS quiz_type, lc.name AS class_name, lc.id AS class_id,
                      (SELECT COUNT(1) FROM quiz_options qo WHERE qo.question_id = qq.id) AS option_count')
            ->join('quizzes q', 'q.id = qq.quiz_id')
            ->join('learning_classes lc', 'lc.id = q.class_id', 'left')
            ->orderBy('qq.id', 'DESC');

        if ($classId > 0) {
            $questionBuilder->where('lc.id', $classId);
        }
        if ($keyword !== '') {
            $questionBuilder->groupStart()
                ->like('qq.prompt', $keyword)
                ->orLike('q.title', $keyword)
                ->groupEnd();
        }

        $quizzes     = [];
        $assignments = [];
        $submissions = [];
        $questions   = [];
        $totalItems  = 0;

        if ($tab === 'quizzes') {
            $totalItems  = (clone $quizBuilder)->countAllResults();
            $quizzes     = $quizBuilder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
            $assignments = (clone $assignBuilder)->limit(5)->get()->getResultArray();
            $submissions = (clone $subBuilder)->limit(5)->get()->getResultArray();
        } elseif ($tab === 'assignments') {
            $totalItems  = (clone $assignBuilder)->countAllResults();
            $assignments = $assignBuilder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
            $quizzes     = (clone $quizBuilder)->limit(5)->get()->getResultArray();
            $submissions = (clone $subBuilder)->limit(5)->get()->getResultArray();
        } elseif ($tab === 'submissions') {
            $totalItems  = (clone $subBuilder)->countAllResults();
            $submissions = $subBuilder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
            $quizzes     = (clone $quizBuilder)->limit(5)->get()->getResultArray();
            $assignments = (clone $assignBuilder)->limit(5)->get()->getResultArray();
        } elseif ($tab === 'bank_soal') {
            $totalItems  = (clone $questionBuilder)->countAllResults();
            $questions   = $questionBuilder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

            // Load options for questions
            if (! empty($questions)) {
                $qIds    = array_column($questions, 'id');
                $options = $db->table('quiz_options')
                    ->whereIn('question_id', $qIds)
                    ->orderBy('sort_order', 'ASC')
                    ->get()->getResultArray();
                $optsMap = [];
                foreach ($options as $opt) {
                    $optsMap[(int) $opt['question_id']][] = $opt;
                }
                foreach ($questions as &$qRow) {
                    $qRow['options'] = $optsMap[(int) $qRow['id']] ?? [];
                }
                unset($qRow);
            }
        }

        $pager = service('pager');
        $pager->store('assessments', $page, $perPage, $totalItems, 0);

        // Stats summary
        $stats = [
            'total_quizzes'        => (int) $db->table('quizzes')->countAllResults(),
            'pre_test_count'       => (int) $db->table('quizzes')->where('type', 'pre_test')->countAllResults(),
            'post_test_count'      => (int) $db->table('quizzes')->where('type', 'post_test')->countAllResults(),
            'module_quiz_count'    => (int) $db->table('quizzes')->where('type', 'lesson_quiz')->countAllResults(),
            'total_questions'      => (int) $db->table('quiz_questions')->countAllResults(),
            'total_assignments'    => (int) $db->table('assignments')->countAllResults(),
            'total_submissions'    => (int) $db->table('assignment_submissions')->countAllResults(),
            'pending_submissions'  => (int) $db->table('assignment_submissions')->whereIn('status', ['submitted', 'in_review'])->countAllResults(),
            'graded_submissions'   => (int) $db->table('assignment_submissions')->where('status', 'graded')->countAllResults(),
        ];

        // Classes and Modules grouped by class
        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();
        $allModules = $this->moduleModel->orderBy('sort_order', 'ASC')->findAll();
        $modulesByClass = [];
        foreach ($allModules as $m) {
            $modulesByClass[(int) $m['class_id']][] = [
                'id'    => (int) $m['id'],
                'title' => $m['title'],
            ];
        }

        return view('dashboard/learning/assessments/management', [
            'title'          => 'Assessment & Penilaian | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Assessment & Penilaian Pembelajaran',
            'currentTab'     => $tab,
            'quizzes'        => $quizzes,
            'assignments'    => $assignments,
            'submissions'    => $submissions,
            'questions'      => $questions,
            'stats'          => $stats,
            'classes'        => $classes,
            'modulesByClass' => $modulesByClass,
            'pager'          => $pager,
            'filters'        => [
                'class_id'  => $classId,
                'keyword'   => $keyword,
                'status'    => $status,
                'quiz_type' => $quizType,
                'tab'       => $tab,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }

    public function storeQuiz()
    {
        $classId      = (int) $this->request->getPost('class_id');
        $title        = trim((string) $this->request->getPost('title'));
        $type         = (string) ($this->request->getPost('type') ?: 'lesson_quiz');
        $instructions = trim((string) $this->request->getPost('instructions'));
        $attemptLimit = max(1, (int) ($this->request->getPost('attempt_limit') ?: 1));
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $status       = (string) ($this->request->getPost('status') ?: 'published');

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Kelas pembelajaran dan judul kuis wajib diisi.');
        }

        $validTypes = ['pre_test', 'post_test', 'lesson_quiz'];
        if (! in_array($type, $validTypes, true)) {
            $type = 'lesson_quiz';
        }

        $now = date('Y-m-d H:i:s');
        $db  = db_connect();

        $db->table('quizzes')->insert([
            'class_id'      => $classId,
            'lesson_id'     => null,
            'type'          => $type,
            'title'         => $title,
            'instructions'  => $instructions ?: null,
            'attempt_limit' => $attemptLimit,
            'is_required'   => $isRequired,
            'status'        => $status,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // Sync with pre_tests table if pre_test
        if ($type === 'pre_test') {
            $isEnabled = ($status === 'published') ? 1 : 0;
            $ptRow     = $db->table('pre_tests')->where('class_id', $classId)->get()->getRowArray();
            if ($ptRow) {
                $db->table('pre_tests')->where('class_id', $classId)->update([
                    'is_enabled'    => $isEnabled,
                    'attempt_limit' => $attemptLimit,
                    'status'        => $status,
                    'updated_at'    => $now,
                ]);
            } else {
                $db->table('pre_tests')->insert([
                    'class_id'      => $classId,
                    'is_enabled'    => $isEnabled,
                    'attempt_limit' => $attemptLimit,
                    'status'        => $status,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=quizzes&class_id=' . $classId))
            ->with('success', 'Kuis (' . esc($title) . ') berhasil dibuat.');
    }

    public function updateQuiz(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan.');
        }

        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $attemptLimit = max(1, (int) ($this->request->getPost('attempt_limit') ?: $quiz['attempt_limit']));
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $status       = (string) ($this->request->getPost('status') ?: $quiz['status']);
        $type         = (string) ($this->request->getPost('type') ?: $quiz['type']);

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul kuis tidak boleh kosong.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('quizzes')->where('id', (int) $id)->update([
            'title'         => $title,
            'type'          => $type,
            'instructions'  => $instructions ?: null,
            'attempt_limit' => $attemptLimit,
            'is_required'   => $isRequired,
            'status'        => $status,
            'updated_at'    => $now,
        ]);

        if ($type === 'pre_test' || $quiz['type'] === 'pre_test') {
            $isEnabled = ($status === 'published') ? 1 : 0;
            $ptRow     = $db->table('pre_tests')->where('class_id', $quiz['class_id'])->get()->getRowArray();
            if ($ptRow) {
                $db->table('pre_tests')->where('class_id', $quiz['class_id'])->update([
                    'is_enabled'    => $isEnabled,
                    'attempt_limit' => $attemptLimit,
                    'status'        => $status,
                    'updated_at'    => $now,
                ]);
            }
        }

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=quizzes&class_id=' . $quiz['class_id']))
            ->with('success', 'Pengaturan kuis berhasil diperbarui.');
    }

    public function toggleQuizStatus(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan.');
        }

        $newStatus = ($quiz['status'] === 'published') ? 'draft' : 'published';
        $now       = date('Y-m-d H:i:s');

        $db->table('quizzes')->where('id', (int) $id)->update([
            'status'     => $newStatus,
            'updated_at' => $now,
        ]);

        if ($quiz['type'] === 'pre_test') {
            $db->table('pre_tests')->where('class_id', $quiz['class_id'])->update([
                'is_enabled' => ($newStatus === 'published') ? 1 : 0,
                'status'     => $newStatus,
                'updated_at' => $now,
            ]);
        }

        return redirect()->back()->with('success', 'Status kuis berhasil diubah menjadi ' . $newStatus . '.');
    }

    public function deleteQuiz(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan.');
        }

        // Safety check: cek pengerjaan peserta
        $attemptsCount = $db->table('quiz_attempts')->where('quiz_id', (int) $id)->countAllResults();
        if ($attemptsCount > 0) {
            return redirect()->back()->with('error', "Kuis tidak dapat dihapus karena telah dikerjakan oleh {$attemptsCount} kali oleh peserta. Anda dapat menonaktifkannya menjadi Draft.");
        }

        // Hapus opsi dan pertanyaan
        $questions = $db->table('quiz_questions')->where('quiz_id', (int) $id)->get()->getResultArray();
        if (! empty($questions)) {
            $qIds = array_column($questions, 'id');
            $db->table('quiz_options')->whereIn('question_id', $qIds)->delete();
            $db->table('quiz_questions')->where('quiz_id', (int) $id)->delete();
        }

        $db->table('quizzes')->where('id', (int) $id)->delete();

        if ($quiz['type'] === 'pre_test') {
            $db->table('pre_tests')->where('class_id', $quiz['class_id'])->update([
                'is_enabled' => 0,
                'status'     => 'draft',
            ]);
        }

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=quizzes'))
            ->with('success', 'Kuis berhasil dihapus.');
    }

    public function storeAssignment()
    {
        $classId      = (int) $this->request->getPost('class_id');
        $level        = (string) ($this->request->getPost('level') ?: 'class');
        $moduleId     = (int) $this->request->getPost('module_id');
        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $dueAt        = $this->request->getPost('due_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('due_at'))) : null;
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $maxAttempts  = max(1, (int) ($this->request->getPost('max_attempts') ?: 1));
        $status       = (string) ($this->request->getPost('status') ?: 'published');

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Kelas dan judul penugasan wajib diisi.');
        }

        $lessonId = null;
        $db = db_connect();

        if ($level === 'module' && $moduleId > 0) {
            $lesson = $db->table('lessons')->where('module_id', $moduleId)->orderBy('sort_order', 'ASC')->limit(1)->get()->getRowArray();
            if ($lesson) {
                $lessonId = (int) $lesson['id'];
            }
        }

        $userId = session()->get('user_id');
        $now    = date('Y-m-d H:i:s');

        $db->table('assignments')->insert([
            'class_id'     => $classId,
            'lesson_id'    => $lessonId,
            'title'        => $title,
            'instructions' => $instructions ?: null,
            'due_at'       => $dueAt,
            'is_required'  => $isRequired,
            'max_attempts' => $maxAttempts,
            'status'       => $status,
            'created_by'   => $userId ? (int) $userId : null,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=assignments&class_id=' . $classId))
            ->with('success', 'Penugasan (' . esc($title) . ') berhasil dibuat.');
    }

    public function updateAssignment(string $id)
    {
        $db   = db_connect();
        $asgn = $db->table('assignments')->where('id', (int) $id)->get()->getRowArray();
        if (! $asgn) {
            throw PageNotFoundException::forPageNotFound('Penugasan tidak ditemukan.');
        }

        $level        = (string) ($this->request->getPost('level') ?: 'class');
        $moduleId     = (int) $this->request->getPost('module_id');
        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $dueAt        = $this->request->getPost('due_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('due_at'))) : null;
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $maxAttempts  = max(1, (int) ($this->request->getPost('max_attempts') ?: 1));
        $status       = (string) ($this->request->getPost('status') ?: $asgn['status']);

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul penugasan tidak boleh kosong.');
        }

        $lessonId = null;
        if ($level === 'module' && $moduleId > 0) {
            $lesson = $db->table('lessons')->where('module_id', $moduleId)->orderBy('sort_order', 'ASC')->limit(1)->get()->getRowArray();
            if ($lesson) {
                $lessonId = (int) $lesson['id'];
            }
        }

        $now = date('Y-m-d H:i:s');
        $db->table('assignments')->where('id', (int) $id)->update([
            'lesson_id'    => $lessonId,
            'title'        => $title,
            'instructions' => $instructions ?: null,
            'due_at'       => $dueAt,
            'is_required'  => $isRequired,
            'max_attempts' => $maxAttempts,
            'status'       => $status,
            'updated_at'   => $now,
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=assignments&class_id=' . $asgn['class_id']))
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function deleteAssignment(string $id)
    {
        $db   = db_connect();
        $asgn = $db->table('assignments')->where('id', (int) $id)->get()->getRowArray();
        if (! $asgn) {
            throw PageNotFoundException::forPageNotFound('Penugasan tidak ditemukan.');
        }

        // Safety check: cek submisi peserta
        $submissionsCount = $db->table('assignment_submissions')->where('assignment_id', (int) $id)->countAllResults();
        if ($submissionsCount > 0) {
            return redirect()->back()->with('error', "Penugasan tidak dapat dihapus karena telah memiliki {$submissionsCount} submisi dari peserta. Ubah status menjadi Draft untuk mengarsipkan.");
        }

        $db->table('assignments')->where('id', (int) $id)->delete();

        return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=assignments'))
            ->with('success', 'Penugasan berhasil dihapus.');
    }

    public function gradeSubmission(string $id)
    {
        $submissionId = (int) $id;
        $score        = (float) $this->request->getPost('score');
        $status       = (string) ($this->request->getPost('status') ?: 'graded');
        $feedback     = trim((string) $this->request->getPost('feedback'));

        if ($score < 0.0 || $score > 100.0) {
            return redirect()->back()->withInput()->with('error', 'Nilai harus berada dalam rentang 0.00 hingga 100.00.');
        }

        $evaluatorId = (int) (session()->get('user_id') ?: 1);

        try {
            $evaluatorService = new EvaluatorService();
            $evaluatorService->gradeSubmission($evaluatorId, $submissionId, $score, $status, $feedback ?: null);

            return redirect()->to(site_url('dashboard/pembelajaran/assessment?tab=submissions'))
                ->with('success', "Penilaian submisi #{$submissionId} berhasil disimpan dengan skor {$score}.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    public function questionBank(): string
    {
        $db      = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $classId = (int) $this->request->getGet('class_id');
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 20)));
        $page    = max(1, (int) ($this->request->getGet('page') ?: 1));

        $builder = $db->table('quiz_questions qq')
            ->select('qq.*, q.title AS quiz_title, q.type AS quiz_type, lc.name AS class_name, lc.id AS class_id, c.title AS course_title')
            ->join('quizzes q', 'q.id = qq.quiz_id')
            ->join('learning_classes lc', 'lc.id = q.class_id', 'left')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->orderBy('qq.id', 'DESC');

        if ($classId > 0) {
            $builder->where('lc.id', $classId);
        }
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('qq.prompt', $keyword)
                ->orLike('q.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->groupEnd();
        }

        $total     = (clone $builder)->countAllResults();
        $questions = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        // Load options for questions
        if (! empty($questions)) {
            $qIds    = array_column($questions, 'id');
            $options = $db->table('quiz_options')
                ->whereIn('question_id', $qIds)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
            $optsMap = [];
            foreach ($options as $opt) {
                $optsMap[(int) $opt['question_id']][] = $opt;
            }
            foreach ($questions as &$qRow) {
                $qRow['options'] = $optsMap[(int) $qRow['id']] ?? [];
            }
            unset($qRow);
        }

        $pager = service('pager');
        $pager->store('question_bank', $page, $perPage, $total, 0);

        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        return view('dashboard/learning/assessments/question_bank', [
            'title'     => 'Bank Soal | Dashboard BegawiPengadaan',
            'pageTitle' => 'Bank Soal Evaluasi Pembelajaran',
            'questions' => $questions,
            'classes'   => $classes,
            'pager'     => $pager,
            'filters'   => [
                'keyword'  => $keyword,
                'class_id' => $classId,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}
