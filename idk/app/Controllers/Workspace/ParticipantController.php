<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\LearningVerticalSliceService;
use Throwable;

class ParticipantController extends BaseController
{
    private LearningVerticalSliceService $learning;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->learning = new LearningVerticalSliceService();
    }

    public function index()
    {
        return redirect()->to(route_to('workspace.home'));
    }

    public function enroll(int $classId)
    {
        return $this->runAction(fn () => $this->learning->enroll($this->userId(), $classId), 'Enrollment berhasil dikirim.');
    }

    public function myClasses(): string
    {
        $userId = $this->userId();
        $items = db_connect()->table('enrollments e')
            ->select('e.id AS enrollment_id, e.class_id, e.status AS enrollment_status, e.enrolled_at, e.activated_at, e.cancelled_at, e.completed_at, lc.code AS class_code, lc.name AS class_name, lc.status AS class_status, lc.start_at, lc.end_at, courses.title AS course_title, programs.title AS program_title')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('e.user_id', $userId)
            ->orderBy('e.updated_at', 'DESC')
            ->get()->getResultArray();

        $enrollmentIds = array_map(static fn (array $row): int => (int) $row['enrollment_id'], $items);
        $progressByEnrollment = [];
        if ($enrollmentIds !== []) {
            $progressRows = db_connect()->table('learning_progress')
                ->select('enrollment_id, status, progress_percent')
                ->whereIn('enrollment_id', $enrollmentIds)
                ->get()->getResultArray();
            foreach ($progressRows as $row) {
                $eid = (int) $row['enrollment_id'];
                if (! isset($progressByEnrollment[$eid])) {
                    $progressByEnrollment[$eid] = ['completed' => 0, 'total' => 0, 'percent' => 0.0];
                }
                $progressByEnrollment[$eid]['total']++;
                $progressByEnrollment[$eid]['percent'] += (float) ($row['progress_percent'] ?? 0);
                if (($row['status'] ?? '') === 'completed') {
                    $progressByEnrollment[$eid]['completed']++;
                }
            }
            foreach ($progressByEnrollment as $eid => &$agg) {
                $agg['percent'] = $agg['total'] > 0 ? round($agg['percent'] / $agg['total'], 2) : 0.0;
            }
            unset($agg);
        }
        foreach ($items as &$row) {
            $eid = (int) $row['enrollment_id'];
            $row['progress_summary'] = $progressByEnrollment[$eid] ?? ['completed' => 0, 'total' => 0, 'percent' => 0.0];
        }
        unset($row);

        $summary = [
            'pending'   => 0,
            'active'    => 0,
            'completed' => 0,
            'cancelled' => 0,
            'withdrawn' => 0,
        ];
        foreach ($items as $row) {
            $key = (string) ($row['enrollment_status'] ?? '');
            if (isset($summary[$key])) {
                $summary[$key]++;
            }
        }

        return view('main/workspace/pages/kelas_saya', [
            'pageTitle'    => 'Kelas Saya',
            'breadcrumbTail' => [['label' => 'Kelas Saya']],
            'enrollments'  => $items,
            'summary'      => $summary,
        ]);
    }

    public function classDetail(string $classId): string
    {
        $userId = $this->userId();
        $enrollment = db_connect()->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if (! $enrollment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $class = db_connect()->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, programs.title AS program_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('lc.id', $classId)->get()->getRowArray();
        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $moduleRows = db_connect()->table('learning_modules m')
            ->select('m.id AS module_id, m.title AS module_title, m.sort_order AS module_order, l.id AS lesson_id, l.title AS lesson_title, l.sort_order AS lesson_order, l.status AS lesson_status')
            ->join('lessons l', 'l.module_id = m.id AND l.status = \'published\'', 'left')
            ->where('m.class_id', $classId)
            ->where('m.status', 'published')
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();

        $lessonIds = [];
        foreach ($moduleRows as $row) {
            if (! empty($row['lesson_id'])) {
                $lessonIds[] = (int) $row['lesson_id'];
            }
        }
        $progressMap = $this->loadProgressMap((int) $enrollment['id'], $lessonIds);

        $modules = [];
        foreach ($moduleRows as $row) {
            $mid = (int) ($row['module_id'] ?? 0);
            if ($mid < 1) {
                continue;
            }
            if (! isset($modules[$mid])) {
                $modules[$mid] = [
                    'id'      => $mid,
                    'title'   => (string) ($row['module_title'] ?? 'Modul'),
                    'order'   => (int) ($row['module_order'] ?? 0),
                    'lessons' => [],
                ];
            }
            if (! empty($row['lesson_id'])) {
                $lid = (int) $row['lesson_id'];
                $modules[$mid]['lessons'][] = [
                    'id'        => $lid,
                    'title'     => (string) ($row['lesson_title'] ?? ''),
                    'order'     => (int) ($row['lesson_order'] ?? 0),
                    'progress'  => $progressMap[$lid] ?? null,
                    'can_enter' => ($enrollment['status'] ?? null) === 'active',
                ];
            }
        }
        $moduleTree = array_values($modules);
        usort($moduleTree, static fn (array $a, array $b): int => $a['order'] <=> $b['order']);
        foreach ($moduleTree as &$mod) {
            usort($mod['lessons'], static fn (array $a, array $b): int => $a['order'] <=> $b['order']);
        }
        unset($mod);

        $progress = $this->summarizeProgress((int) $enrollment['id']);

        return view('main/workspace/pages/detail_kelas', [
            'pageTitle'      => $class['name'] ?? 'Detail Kelas',
            'classContext'   => [
                'name'          => $class['name'] ?? '-',
                'code'          => $class['code'] ?? '-',
                'course_title'  => $class['course_title'] ?? null,
                'program_title' => $class['program_title'] ?? null,
                'start_at'      => $class['start_at'] ?? null,
                'end_at'        => $class['end_at'] ?? null,
            ],
            'modules'      => $moduleTree,
            'enrollment'   => $enrollment,
            'progressInfo' => $progress,
            'mode'         => 'detail',
        ]);
    }

    public function study(string $classId): string
    {
        $userId = $this->userId();
        $enrollment = db_connect()->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if (! $enrollment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        if (($enrollment['status'] ?? null) !== 'active') {
            return redirect()->to(route_to('workspace.participant.class.detail', $classId))
                ->with('error', 'Kelas belum aktif. Selesaikan approval terlebih dahulu.');
        }

        $modules = $this->loadModuleTree($classId, (int) $enrollment['id']);
        $progress = $this->summarizeProgress((int) $enrollment['id']);
        $quizzes  = $this->loadQuizzesForClass($classId);

        return view('main/workspace/pages/belajar', [
            'pageTitle'      => 'Learning Room',
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => 'Belajar'],
            ],
            'enrollment'   => $enrollment,
            'modules'      => $modules,
            'progressInfo' => $progress,
            'classContext' => $this->loadClassContext($classId),
            'quizzes'      => $quizzes,
        ]);
    }

    public function lessonDetail(string $lessonId): string
    {
        $userId = $this->userId();
        $lessonId = (int) $lessonId;

        $lesson = db_connect()->table('lessons l')
            ->select('l.*, learning_modules.id AS module_id, learning_modules.title AS module_title, learning_modules.class_id AS class_id, learning_modules.status AS module_status')
            ->join('learning_modules', 'learning_modules.id = l.module_id')
            ->where('l.id', $lessonId)
            ->where('l.status', 'published')
            ->where('learning_modules.status', 'published')
            ->get()->getRowArray();
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lesson tidak ditemukan atau belum dipublikasikan.');
        }

        $enrollment = db_connect()->table('enrollments')
            ->where(['class_id' => (int) $lesson['class_id'], 'user_id' => $userId])
            ->get()->getRowArray();
        if ($enrollment === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Enrollment tidak ditemukan.');
        }
        if (($enrollment['status'] ?? null) !== 'active') {
            return redirect()->to(route_to('workspace.participant.classes'))
                ->with('error', 'Lesson tidak dapat dibuka. Enrollment belum aktif.');
        }

        try {
            $this->learning->startLesson($userId, $lessonId);
        } catch (Throwable $e) {
            log_message('error', 'lesson.startLesson failed: {message}', ['message' => $e->getMessage()]);
        }

        $progress = db_connect()->table('learning_progress')
            ->where(['enrollment_id' => (int) $enrollment['id'], 'lesson_id' => $lessonId])
            ->get()->getRowArray() ?: [];

        $siblings = db_connect()->table('lessons l')
            ->select('l.id, l.title, l.sort_order, learning_modules.id AS module_id, learning_modules.sort_order AS module_order')
            ->join('learning_modules', 'learning_modules.id = l.module_id')
            ->where('learning_modules.class_id', (int) $lesson['class_id'])
            ->orderBy('learning_modules.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();

        $prev = null;
        $next = null;
        $count = count($siblings);
        for ($i = 0; $i < $count; $i++) {
            if ((int) $siblings[$i]['id'] === $lessonId) {
                if ($i > 0) {
                    $prev = $siblings[$i - 1];
                }
                if ($i + 1 < $count) {
                    $next = $siblings[$i + 1];
                }
                break;
            }
        }

        $classInfo = db_connect()->table('learning_classes lc')
            ->select('lc.id, lc.name, lc.code')
            ->where('lc.id', (int) $lesson['class_id'])
            ->get()->getRowArray() ?: [];

        return view('main/workspace/pages/lesson', [
            'pageTitle'      => $lesson['title'] ?? 'Lesson',
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => 'Belajar', 'url' => route_to('workspace.participant.study', $lesson['class_id'])],
                ['label' => $lesson['title'] ?? 'Lesson'],
            ],
            'lesson'    => $lesson,
            'enrollment'=> $enrollment,
            'progress'  => $progress,
            'prev'      => $prev,
            'next'      => $next,
            'classInfo' => $classInfo,
        ]);
    }

    public function progress(string $classId): string
    {
        $this->userId();
        $items = db_connect()->table('learning_progress lp')
            ->select('lessons.title, lp.status, lp.progress_percent')
            ->join('lessons', 'lessons.id = lp.lesson_id')
            ->join('enrollments e', 'e.id = lp.enrollment_id')
            ->where(['e.user_id' => session()->get('user_id'), 'e.class_id' => $classId])
            ->orderBy('lessons.sort_order')->get()->getResultArray();

        return $this->learningPage('Progress Belajar', 'Kemajuan lesson pada kelas yang Anda ikuti.', array_map(
            static fn (array $item): string => ($item['title'] ?? '-') . ' · ' . ($item['status'] ?? '-') . ' · ' . ($item['progress_percent'] ?? 0) . '%',
            $items
        ));
    }

    public function preTest(string $classId): string
    {
        return $this->assessmentPage('Pre-test', 'Baseline kemampuan sebelum pembelajaran dimulai.', $classId, 'pre_test');
    }

    public function quiz(string $classId): string
    {
        return $this->assessmentPage('Lesson Quiz', 'Quiz yang terkait dengan lesson pembelajaran.', $classId, 'lesson_quiz');
    }

    public function postTest(string $classId): string
    {
        return $this->assessmentPage('Post-test', 'Evaluasi akhir sebagai komponen nilai pembelajaran.', $classId, 'post_test');
    }

    public function preTestResult(string $classId): string
    {
        return $this->preTest($classId);
    }

    public function quizResult(string $classId): string
    {
        return $this->quiz($classId);
    }

    public function postTestResult(string $classId): string
    {
        return $this->postTest($classId);
    }

    public function assignments(string $classId): string
    {
        $this->userId();
        return view('main/workspace/pages/tugas', [
            'sectionContent' => ['cards' => [], 'items' => ['Belum ada tugas pada vertical slice ini.']],
        ]);
    }

    public function assignmentSubmission(string $classId): string
    {
        return $this->assignments($classId);
    }

    public function assignmentRevision(string $classId): string
    {
        return $this->assignments($classId);
    }

    public function feedback(string $classId): string
    {
        $this->userId();
        return view('main/workspace/pages/penilaian', [
            'sectionContent' => ['cards' => [], 'items' => ['Feedback belum tersedia.']],
        ]);
    }

    public function startQuiz(int $quizId)
    {
        try {
            $attemptId = $this->learning->startQuiz($this->userId(), $quizId);
            return redirect()->to(route_to('workspace.participant.quiz.take', $quizId) . '?attempt=' . $attemptId)
                ->with('success', 'Quiz dimulai.');
        } catch (Throwable $e) {
            log_message('error', 'Participant startQuiz failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function takeQuiz(string $quizId)
    {
        $userId = $this->userId();
        $quizId = (int) $quizId;
        $quiz = db_connect()->table('quizzes')
            ->select('quizzes.*, learning_classes.name AS class_name, learning_classes.code AS class_code')
            ->join('learning_classes', 'learning_classes.id = quizzes.class_id')
            ->where('quizzes.id', $quizId)
            ->where('quizzes.status', 'published')
            ->get()->getRowArray();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Quiz tidak ditemukan atau belum dipublikasikan.');
        }

        $enrollment = db_connect()->table('enrollments')
            ->where(['class_id' => (int) $quiz['class_id'], 'user_id' => $userId, 'status' => 'active'])
            ->get()->getRowArray();
        if (! $enrollment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Enrollment aktif tidak ditemukan.');
        }

        $questions = db_connect()->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
        $optionsByQuestion = [];
        if ($questions !== []) {
            $rows = db_connect()->table('quiz_options')
                ->whereIn('question_id', array_map(static fn ($q): int => (int) $q['id'], $questions))
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $optionsByQuestion[(int) $row['question_id']][] = $row;
            }
        }

        $attemptId = (int) ($this->request->getGet('attempt') ?: 0);
        $attempt = null;
        if ($attemptId > 0) {
            $attempt = db_connect()->table('quiz_attempts')
                ->where(['id' => $attemptId, 'enrollment_id' => (int) $enrollment['id'], 'quiz_id' => $quizId])
                ->get()->getRowArray();
        }

        $latest = db_connect()->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => (int) $enrollment['id']])
            ->orderBy('attempt_number', 'DESC')
            ->get()->getRowArray();
        $attemptCount = (int) db_connect()->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => (int) $enrollment['id']])
            ->countAllResults();

        return view('main/workspace/pages/quiz_take', [
            'pageTitle'      => $quiz['title'] ?? 'Quiz',
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => $quiz['class_name'] ?? 'Kelas', 'url' => route_to('workspace.participant.study', $quiz['class_id'])],
                ['label' => $quiz['title'] ?? 'Quiz'],
            ],
            'quiz'             => $quiz,
            'questions'        => $questions,
            'optionsByQuestion'=> $optionsByQuestion,
            'attempt'          => $attempt,
            'attemptCount'     => $attemptCount,
            'attemptLimit'     => (int) ($quiz['attempt_limit'] ?? 1),
            'latest'           => $latest,
            'remaining'        => max(0, (int) ($quiz['attempt_limit'] ?? 1) - $attemptCount),
        ]);
    }

    public function submitQuiz(int $attemptId)
    {
        return $this->runAction(function () use ($attemptId) {
            $answers = (array) $this->request->getPost('answers');
            $normalized = [];
            foreach ($answers as $qid => $oid) {
                $normalized[(int) $qid] = (int) $oid;
            }
            $this->learning->submitQuiz($this->userId(), (int) $attemptId, $normalized);
            $attempt = db_connect()->table('quiz_attempts')->where('id', (int) $attemptId)->get()->getRowArray();
            $quizId  = $attempt['quiz_id'] ?? 0;
            if ($quizId > 0) {
                return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quizId) . '?attempt=' . $attemptId)
                    ->with('success', 'Quiz berhasil dinilai. Skor: ' . ($attempt['score'] ?? '-'));
            }
        }, 'Quiz berhasil dinilai.');
    }

    public function startLesson(int $lessonId)
    {
        return $this->runAction(fn () => $this->learning->startLesson($this->userId(), $lessonId), 'Lesson dibuka.');
    }

    public function completeLesson(int $lessonId)
    {
        return $this->runAction(function () use ($lessonId) {
            $this->learning->completeLesson($this->userId(), $lessonId);
        }, 'Lesson selesai.');
    }

    public function certificates(): string
    {
        $userId = $this->userId();
        $certificates = db_connect()->table('certificates c')
            ->select('c.*, lc.name AS class_name, courses.title AS course_title')
            ->join('enrollments e', 'e.id = c.source_id AND c.source_type = \'learning_enrollment\'')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('c.user_id', $userId)
            ->whereIn('c.status', ['pending', 'issued'])
            ->orderBy('c.created_at', 'DESC')
            ->get()->getResultArray();

        return view('main/participant/certificates', [
            'title' => 'Sertifikat Saya | Workspace BegawiPBJ',
            'certificates' => $certificates,
        ]);
    }

    public function certificateDetail(string $id): string
    {
        $userId = $this->userId();
        $certificate = db_connect()->table('certificates c')
            ->select('c.*, lc.name AS class_name, courses.title AS course_title')
            ->join('enrollments e', 'e.id = c.source_id AND c.source_type = \'learning_enrollment\'')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->where(['c.id' => $id, 'c.user_id' => $userId])
            ->whereIn('c.status', ['pending', 'issued'])
            ->get()->getRowArray();

        if (! $certificate) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Certificate tidak ditemukan.');
        }

        return view('main/participant/certificates', [
            'title' => 'Detail Sertifikat | Workspace BegawiPBJ',
            'certificates' => [$certificate],
        ]);
    }

    private function userId(): int
    {
        $id = (int) session()->get('user_id');
        if ($id < 1 || session()->get('isLoggedIn') !== true) {
            throw new \RuntimeException('Silakan masuk terlebih dahulu.');
        }
        return $id;
    }

    private function loadProgressMap(int $enrollmentId, array $lessonIds): array
    {
        if ($lessonIds === []) {
            return [];
        }
        $rows = db_connect()->table('learning_progress')
            ->select('lesson_id, status, progress_percent, started_at, completed_at')
            ->where('enrollment_id', $enrollmentId)
            ->whereIn('lesson_id', $lessonIds)
            ->get()->getResultArray();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['lesson_id']] = $row;
        }
        return $map;
    }

    private function loadModuleTree(int $classId, int $enrollmentId): array
    {
        $moduleRows = db_connect()->table('learning_modules m')
            ->select('m.id AS module_id, m.title AS module_title, m.sort_order AS module_order, l.id AS lesson_id, l.title AS lesson_title, l.sort_order AS lesson_order, l.status AS lesson_status')
            ->join('lessons l', 'l.module_id = m.id AND l.status = \'published\'', 'left')
            ->where('m.class_id', $classId)
            ->where('m.status', 'published')
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();

        $lessonIds = [];
        foreach ($moduleRows as $row) {
            if (! empty($row['lesson_id'])) {
                $lessonIds[] = (int) $row['lesson_id'];
            }
        }
        $progressMap = $this->loadProgressMap($enrollmentId, $lessonIds);

        $modules = [];
        foreach ($moduleRows as $row) {
            $mid = (int) ($row['module_id'] ?? 0);
            if ($mid < 1) {
                continue;
            }
            if (! isset($modules[$mid])) {
                $modules[$mid] = [
                    'id'      => $mid,
                    'title'   => (string) ($row['module_title'] ?? 'Modul'),
                    'order'   => (int) ($row['module_order'] ?? 0),
                    'lessons' => [],
                ];
            }
            if (! empty($row['lesson_id'])) {
                $lid = (int) $row['lesson_id'];
                $modules[$mid]['lessons'][] = [
                    'id'    => $lid,
                    'title' => (string) ($row['lesson_title'] ?? ''),
                    'order' => (int) ($row['lesson_order'] ?? 0),
                    'progress' => $progressMap[$lid] ?? null,
                ];
            }
        }
        $tree = array_values($modules);
        usort($tree, static fn (array $a, array $b): int => $a['order'] <=> $b['order']);
        foreach ($tree as &$mod) {
            usort($mod['lessons'], static fn (array $a, array $b): int => $a['order'] <=> $b['order']);
        }
        unset($mod);
        return $tree;
    }

    private function loadClassContext(string $classId): array
    {
        $row = db_connect()->table('learning_classes lc')
            ->select('lc.id, lc.name AS class_name, lc.code AS class_code, courses.title AS course_title, programs.title AS program_title')
            ->join('courses', 'courses.id = lc.course_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('lc.id', (int) $classId)
            ->get()->getRowArray();
        return $row ?: [];
    }

    private function loadQuizzesForClass(string $classId): array
    {
        return db_connect()->table('quizzes')
            ->select('id, type, title, attempt_limit, is_required, status')
            ->where('class_id', (int) $classId)
            ->where('status', 'published')
            ->orderBy('type', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    private function summarizeProgress(int $enrollmentId): array
    {
        $rows = db_connect()->table('learning_progress')
            ->select('status, progress_percent')
            ->where('enrollment_id', $enrollmentId)
            ->get()->getResultArray();
        $total = count($rows);
        $completed = 0;
        $inProgress = 0;
        $percentSum = 0.0;
        foreach ($rows as $row) {
            $percentSum += (float) ($row['progress_percent'] ?? 0);
            if (($row['status'] ?? '') === 'completed') {
                $completed++;
            } elseif (($row['status'] ?? '') === 'in_progress') {
                $inProgress++;
            }
        }
        $overall = $total > 0 ? round($percentSum / $total, 2) : 0.0;
        return [
            'total'      => $total,
            'completed'  => $completed,
            'in_progress'=> $inProgress,
            'percent'    => $overall,
        ];
    }

    private function learningPage(string $title, string $description, array $items): string
    {
        return view('main/workspace/pages/pembelajaran', [
            'sectionContent' => [
                'eyebrow' => 'Pembelajaran',
                'description' => $description,
                'cards' => [
                    ['title' => 'Item pembelajaran', 'value' => (string) count($items), 'meta' => 'Data dari enrollment aktif Anda'],
                ],
                'items' => $items ?: ['Belum ada data pembelajaran.'],
            ],
            'pageTitle' => $title,
        ]);
    }

    private function assessmentPage(string $title, string $description, string $classId, string $type): string
    {
        $this->userId();
        $items = db_connect()->table('quizzes')
            ->select('title, status, attempt_limit')
            ->where(['class_id' => $classId, 'type' => $type])
            ->orderBy('id')->get()->getResultArray();

        return view('main/workspace/pages/penilaian', [
            'sectionContent' => [
                'cards' => [['title' => $title, 'value' => (string) count($items), 'meta' => $description]],
                'items' => array_map(static fn (array $item): string => $item['title'] . ' · ' . $item['status'] . ' · ' . $item['attempt_limit'] . ' percobaan', $items) ?: ['Belum tersedia.'],
            ],
        ]);
    }

    private function runAction(callable $action, string $success)
    {
        try {
            $action();
            return redirect()->back()->with('success', $success);
        } catch (Throwable $e) {
            log_message('error', 'Participant learning action failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
