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
            ->select('e.id AS enrollment_id, e.class_id, e.status AS enrollment_status, e.final_score, lc.id AS class_id, lc.code AS class_code, lc.name AS class_name, lc.status AS class_status, lc.start_at, lc.end_at, courses.title AS course_title, programs.title AS program_title')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('e.user_id', $userId)
            ->orderBy('e.updated_at', 'DESC')
            ->get()->getResultArray();

        return $this->learningPage('Kelas Saya', 'Kelas terdaftar dan status enrollment Anda.', $items);
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

        $items = db_connect()->table('learning_modules m')
            ->select('m.title AS module_title, lessons.title AS lesson_title')
            ->join('lessons', 'lessons.module_id = m.id', 'left')
            ->where('m.class_id', $classId)->orderBy('m.sort_order')->orderBy('lessons.sort_order')
            ->get()->getResultArray();

        return $this->learningPage($class['name'], 'Course: ' . ($class['course_title'] ?? '-'), array_map(
            static fn (array $item): string => ($item['module_title'] ?? '-') . ' · ' . ($item['lesson_title'] ?? 'Belum ada lesson'),
            $items
        ));
    }

    public function study(string $classId): string
    {
        return $this->classDetail($classId);
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
        return $this->runAction(fn () => $this->learning->startQuiz($this->userId(), $quizId), 'Quiz dimulai.');
    }

    public function submitQuiz(int $attemptId)
    {
        $answers = (array) $this->request->getPost('answers');
        return $this->runAction(fn () => $this->learning->submitQuiz($this->userId(), $attemptId, $answers), 'Quiz berhasil dinilai.');
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
