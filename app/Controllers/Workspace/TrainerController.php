<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\Learning\ClassTrainerModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LessonModel;
use App\Models\Learning\QuizModel;
use App\Models\Learning\QuestionModel;
use App\Models\Learning\QuestionOptionModel;
use Throwable;

class TrainerController extends BaseController
{
    public function index()
    {
        return $this->render('Dashboard Trainer', 'trainer/home', [], 'trainer');
    }

    public function myClasses()
    {
        $userId = $this->userId();
        $items = db_connect()->table('class_trainers ct')
            ->select('ct.class_id, ct.assigned_at, ct.status AS ct_status, lc.code, lc.name, lc.status AS class_status, lc.start_at, lc.end_at, courses.title AS course_title, programs.title AS program_title')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('ct.user_id', $userId)
            ->where('ct.status', 'active')
            ->orderBy('lc.start_at', 'DESC')
            ->get()->getResultArray();

        return $this->render('Kelas Saya', 'trainer/kelas_saya', ['classes' => $items], 'trainer');
    }

    public function classDetail(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $class = db_connect()->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, programs.title AS program_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('lc.id', $classId)->get()->getRowArray();
        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $modules = db_connect()->table('learning_modules')
            ->where('class_id', (int) $classId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $participantCount = (int) db_connect()->table('enrollments')
            ->where('class_id', (int) $classId)
            ->where('status', 'active')->countAllResults();

        $quizCount = (int) db_connect()->table('quizzes')
            ->where('class_id', (int) $classId)->countAllResults();

        return $this->render($class['name'] ?? 'Detail Kelas', 'trainer/detail_kelas', [
            'classCtx'        => $class,
            'modules'         => $modules,
            'participantCount'=> $participantCount,
            'quizCount'       => $quizCount,
        ], 'trainer', [['label' => 'Kelas Saya', 'url' => route_to('workspace.trainer.classes')]]);
    }

    public function participants(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $items = db_connect()->table('enrollments e')
            ->select('e.id AS enrollment_id, e.status AS enrollment_status, e.enrolled_at, e.activated_at, u.id AS user_id, u.name, u.username, u.email')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.class_id', (int) $classId)
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()->getResultArray();

        return $this->render('Peserta', 'trainer/peserta', [
            'classId'     => (int) $classId,
            'participants'=> $items,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId));
    }

    public function modules(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $modules = db_connect()->table('learning_modules')
            ->where('class_id', (int) $classId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return $this->render('Modul', 'trainer/modul_list', [
            'classId' => (int) $classId,
            'modules' => $modules,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Modul'));
    }

    public function createModule(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        if ($this->request->getMethod() === 'post') {
            $title = trim((string) $this->request->getPost('title'));
            $description = (string) $this->request->getPost('description');
            $sortOrder = (int) $this->request->getPost('sort_order');
            if ($title === '') {
                return redirect()->back()->withInput()->with('error', 'Judul modul wajib diisi.');
            }
            $maxOrder = (int) db_connect()->table('learning_modules')
                ->selectMax('sort_order')->where('class_id', (int) $classId)->get()->getRowArray()['sort_order'] ?? 0;
            (new LearningModuleModel())->insert([
                'class_id'    => (int) $classId,
                'title'       => $title,
                'description' => $description,
                'sort_order'  => $sortOrder > 0 ? $sortOrder : ($maxOrder + 1),
                'is_required' => 1,
                'status'      => 'draft',
            ]);
            return redirect()->to(route_to('workspace.trainer.modules', $classId))->with('success', 'Modul berhasil dibuat.');
        }

        return $this->render('Tambah Modul', 'trainer/modul_form', [
            'classId' => (int) $classId,
            'mode'    => 'create',
            'module'  => null,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Modul'));
    }

    public function editModule(string $classId, string $moduleId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $moduleModel = new LearningModuleModel();
        $module = $moduleModel->where('id', (int) $moduleId)->where('class_id', (int) $classId)->first();
        if (! $module) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $moduleModel->update((int) $moduleId, [
                'title'       => trim((string) $this->request->getPost('title')),
                'description' => (string) $this->request->getPost('description'),
                'sort_order'  => (int) $this->request->getPost('sort_order'),
                'status'      => (string) ($this->request->getPost('status') ?: $module['status']),
                'is_required' => isset($this->getRequest()->getPost()->is_required) ? 1 : 0,
            ]);
            return redirect()->to(route_to('workspace.trainer.modules', $classId))->with('success', 'Modul berhasil diperbarui.');
        }

        return $this->render('Ubah Modul', 'trainer/modul_form', [
            'classId' => (int) $classId,
            'mode'    => 'edit',
            'module'  => $module,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Ubah Modul'));
    }

    public function deleteModule(string $classId, string $moduleId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        try {
            db_connect()->table('lessons')->where('module_id', (int) $moduleId)->delete();
            (new LearningModuleModel())->where('id', (int) $moduleId)->where('class_id', (int) $classId)->delete();
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Modul gagal dihapus: ' . $e->getMessage());
        }
        return redirect()->to(route_to('workspace.trainer.modules', $classId))->with('success', 'Modul dihapus.');
    }

    public function materials(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $modules = db_connect()->table('learning_modules m')
            ->select('m.id AS module_id, m.title, lessons.id AS lesson_id, lessons.title AS lesson_title, lessons.content_type, lessons.status')
            ->join('lessons', 'lessons.module_id = m.id', 'left')
            ->where('m.class_id', (int) $classId)
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('lessons.sort_order', 'ASC')
            ->get()->getResultArray();

        return $this->render('Materi', 'trainer/materi_list', [
            'classId'   => (int) $classId,
            'className' => $this->className($classId),
            'items'     => $modules,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Materi'));
    }

    public function createLesson(string $classId, string $moduleId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $moduleModel = new LearningModuleModel();
        $module = $moduleModel->where('id', (int) $moduleId)->where('class_id', (int) $classId)->first();
        if (! $module) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $payload = $this->lessonPayload((int) $moduleId, (int) $userId);
            if (($msg = $payload['error'] ?? null) !== null) {
                return redirect()->back()->withInput()->with('error', $msg);
            }
            (new LessonModel())->insert($payload['data']);
            return redirect()->to(route_to('workspace.trainer.materials', $classId))->with('success', 'Lesson berhasil dibuat.');
        }

        return $this->render('Tambah Lesson', 'trainer/lesson_form', [
            'classId'   => (int) $classId,
            'module'    => $module,
            'mode'      => 'create',
            'lesson'    => null,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Lesson'));
    }

    public function editLesson(string $classId, string $lessonId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $lessonModel = new LessonModel();
        $lesson = $lessonModel
            ->select('lessons.*, learning_modules.class_id')
            ->join('learning_modules', 'learning_modules.id = lessons.module_id')
            ->where('lessons.id', (int) $lessonId)
            ->where('learning_modules.class_id', (int) $classId)
            ->get()->getRowArray();
        if (! $lesson) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lesson tidak ditemukan.');
        }
        $module = (new LearningModuleModel())->find((int) $lesson['module_id']);

        if ($this->request->getMethod() === 'post') {
            $payload = $this->lessonPayload((int) $lesson['module_id'], (int) $userId);
            if (($msg = $payload['error'] ?? null) !== null) {
                return redirect()->back()->withInput()->with('error', $msg);
            }
            $lessonModel->update((int) $lessonId, $payload['data']);
            return redirect()->to(route_to('workspace.trainer.materials', $classId))->with('success', 'Lesson diperbarui.');
        }

        return $this->render('Ubah Lesson', 'trainer/lesson_form', [
            'classId'   => (int) $classId,
            'module'    => $module,
            'mode'      => 'edit',
            'lesson'    => $lesson,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Ubah Lesson'));
    }

    public function deleteLesson(string $classId, string $lessonId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        try {
            (new LessonModel())->where('id', (int) $lessonId)->delete();
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Lesson gagal dihapus: ' . $e->getMessage());
        }
        return redirect()->to(route_to('workspace.trainer.materials', $classId))->with('success', 'Lesson dihapus.');
    }

    public function quizzes(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $quizzes = db_connect()->table('quizzes')
            ->where('class_id', (int) $classId)
            ->orderBy('type', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        return $this->render('Ujian', 'trainer/quiz_list', [
            'classId'   => (int) $classId,
            'className' => $this->className($classId),
            'quizzes'   => $quizzes,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Ujian'));
    }

    public function createQuiz(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $lessons = $this->loadLessonsForClass((int) $classId);

        if ($this->request->getMethod() === 'post') {
            $type = (string) $this->request->getPost('type');
            if (! in_array($type, ['pre_test', 'lesson_quiz', 'post_test'], true)) {
                return redirect()->back()->withInput()->with('error', 'Tipe ujian tidak valid.');
            }
            $title = trim((string) $this->request->getPost('title'));
            if ($title === '') {
                return redirect()->back()->withInput()->with('error', 'Judul ujian wajib diisi.');
            }
            $lessonId = null;
            if ($type === 'lesson_quiz') {
                $lessonId = (int) $this->request->getPost('lesson_id');
                if ($lessonId < 1) {
                    return redirect()->back()->withInput()->with('error', 'Quiz lesson wajib memilih lesson.');
                }
                $lessonExists = false;
                foreach ($lessons as $row) {
                    if ((int) $row['id'] === $lessonId) {
                        $lessonExists = true;
                        break;
                    }
                }
                if (! $lessonExists) {
                    return redirect()->back()->withInput()->with('error', 'Lesson tidak valid untuk kelas ini.');
                }
            }
            $quizId = (new QuizModel())->insert([
                'class_id'      => (int) $classId,
                'lesson_id'     => $lessonId,
                'type'          => $type,
                'title'         => $title,
                'instructions'  => (string) $this->request->getPost('instructions'),
                'attempt_limit' => max(1, (int) $this->request->getPost('attempt_limit')),
                'is_required'   => $this->request->getPost('is_required') ? 1 : 0,
                'status'        => 'draft',
            ], true);
            return redirect()->to(route_to('workspace.trainer.editQuiz', $classId, (string) $quizId))->with('success', 'Ujian dibuat. Tambahkan soal.');
        }

        return $this->render('Tambah Ujian', 'trainer/quiz_form', [
            'classId'   => (int) $classId,
            'className' => $this->className($classId),
            'mode'      => 'create',
            'quiz'      => null,
            'lessons'   => $lessons,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Ujian'));
    }

    private function loadLessonsForClass(int $classId): array
    {
        return db_connect()->table('lessons l')
            ->select('l.id, l.title, learning_modules.title AS module_title')
            ->join('learning_modules', 'learning_modules.id = l.module_id')
            ->where('learning_modules.class_id', $classId)
            ->orderBy('learning_modules.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    public function editQuiz(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $quiz = (new QuizModel())->where('id', (int) $quizId)->where('class_id', (int) $classId)->first();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post' && $this->request->getPost('_action') === 'update_meta') {
            (new QuizModel())->update((int) $quizId, [
                'title'         => trim((string) $this->request->getPost('title')),
                'instructions'  => (string) $this->request->getPost('instructions'),
                'attempt_limit' => max(1, (int) $this->request->getPost('attempt_limit')),
                'is_required'   => $this->request->getPost('is_required') ? 1 : 0,
                'status'        => (string) ($this->request->getPost('status') ?: $quiz['status']),
            ]);
            return redirect()->back()->with('success', 'Ujian diperbarui.');
        }

        $questions = db_connect()->table('quiz_questions')
            ->where('quiz_id', (int) $quizId)
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

        return $this->render('Soal Ujian', 'trainer/quiz_edit', [
            'classId'   => (int) $classId,
            'className' => $this->className($classId),
            'mode'      => 'edit',
            'quiz'      => $quiz,
            'questions' => $questions,
            'optionsByQuestion' => $optionsByQuestion,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Soal'));
    }

    public function addQuestion(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $quiz = (new QuizModel())->where('id', (int) $quizId)->where('class_id', (int) $classId)->first();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $prompt = trim((string) $this->request->getPost('prompt'));
            if ($prompt === '') {
                return redirect()->back()->withInput()->with('error', 'Pertanyaan wajib diisi.');
            }
            $options = (array) $this->request->getPost('options');
            $correct = (int) $this->request->getPost('correct_option');
            $options = array_values(array_filter(array_map('trim', $options), static fn ($o): bool => $o !== ''));
            if (count($options) < 2) {
                return redirect()->back()->withInput()->with('error', 'Minimal dua pilihan jawaban.');
            }
            if ($correct < 0 || $correct >= count($options)) {
                return redirect()->back()->withInput()->with('error', 'Tandai satu jawaban benar.');
            }

            $maxOrder = (int) (db_connect()->table('quiz_questions')->selectMax('sort_order')->where('quiz_id', (int) $quizId)->get()->getRowArray()['sort_order'] ?? 0);
            $questionId = (new QuestionModel())->insert([
                'quiz_id'    => (int) $quizId,
                'type'       => 'multiple_choice',
                'prompt'     => $prompt,
                'points'     => max(0.01, (float) $this->request->getPost('points')),
                'sort_order' => $maxOrder + 1,
                'explanation'=> (string) $this->request->getPost('explanation'),
            ], true);

            foreach ($options as $i => $optionText) {
                (new QuestionOptionModel())->insert([
                    'question_id' => (int) $questionId,
                    'option_text' => $optionText,
                    'is_correct'  => $i === $correct ? 1 : 0,
                    'sort_order'  => $i + 1,
                ]);
            }

            return redirect()->to(route_to('workspace.trainer.editQuiz', $classId, $quizId))->with('success', 'Soal ditambahkan.');
        }

        return $this->render('Tambah Soal', 'trainer/question_form', [
            'classId'   => (int) $classId,
            'quiz'      => $quiz,
            'className' => $this->className($classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Soal'));
    }

    public function progress(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $rows = db_connect()->table('enrollments e')
            ->select('u.name, u.username, u.email, lp.status, lp.progress_percent, lp.started_at, lp.completed_at, lessons.title AS lesson_title')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_progress lp', 'lp.enrollment_id = e.id', 'left')
            ->join('lessons', 'lessons.id = lp.lesson_id', 'left')
            ->where('e.class_id', (int) $classId)
            ->orderBy('u.name', 'ASC')
            ->orderBy('lessons.sort_order', 'ASC')
            ->get()->getResultArray();

        return $this->render('Progress Peserta', 'trainer/progress', [
            'classId'   => (int) $classId,
            'className' => $this->className($classId),
            'items'     => $rows,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Progress'));
    }

    private function lessonPayload(int $moduleId, int $actorId): array
    {
        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return ['error' => 'Judul lesson wajib diisi.'];
        }
        $contentType = (string) $this->request->getPost('content_type');
        if (! in_array($contentType, ['text', 'document', 'video', 'link'], true)) {
            return ['error' => 'Tipe konten tidak valid.'];
        }
        $content = (string) $this->request->getPost('content');
        $fileId  = null;
        $externalUrl = null;
        if ($contentType === 'document') {
            $fileId = (int) $this->request->getPost('file_id');
            if ($fileId < 1) {
                $fileId = $this->registerFileAsset((string) $this->request->getPost('document_label'), 'document');
                if ($fileId === null) {
                    return ['error' => 'Lengkapi data file materi.'];
                }
            }
        } elseif ($contentType === 'video' || $contentType === 'link') {
            $externalUrl = trim((string) $this->request->getPost('external_url'));
            if ($externalUrl === '') {
                return ['error' => 'URL wajib diisi untuk tipe video/link.'];
            }
        }

        $maxOrder = (int) (db_connect()->table('lessons')
            ->selectMax('sort_order')->where('module_id', $moduleId)->get()->getRowArray()['sort_order'] ?? 0);

        return ['data' => [
            'module_id'    => $moduleId,
            'title'         => $title,
            'slug'          => url_title($title, '-', true),
            'content_type'  => $contentType,
            'content'       => $content,
            'file_id'       => $fileId,
            'external_url'  => $externalUrl,
            'sort_order'    => $maxOrder + 1,
            'is_required'   => 1,
            'status'        => 'draft',
            'visibility'    => 'internal',
            'created_by'    => $actorId,
            'updated_by'    => $actorId,
        ]];
    }

    private function registerFileAsset(string $label, string $kind): ?int
    {
        $label = trim($label);
        if ($label === '') {
            return null;
        }
        $now = date('Y-m-d H:i:s');
        $path = 'lessons/' . url_title($label, '-', true) . '-' . substr((string) time(), -4);
        $row = [
            'disk'            => 'local',
            'path'            => $path,
            'original_name'    => $label,
            'stored_name'      => basename($path),
            'extension'        => 'bin',
            'mime_type'        => 'application/octet-stream',
            'size_bytes'       => 0,
            'visibility'       => 'internal',
            'lifecycle_status' => 'pending_upload',
            'resource_type'    => 'lesson',
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        return (int) db_connect()->table('file_assets')->insert($row, true);
    }

    private function assertTrainerOf(int $userId, int $classId): void
    {
        $row = (new ClassTrainerModel())
            ->where('class_id', $classId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
        if (! $row) {
            redirect()->to(route_to('workspace.trainer.classes'))
                ->with('error', 'Anda tidak ditugaskan pada kelas tersebut.')
                ->send();
            exit;
        }
    }

    private function className(int $classId): string
    {
        $row = (new LearningClassModel())->find($classId);
        return $row['name'] ?? ('Kelas #' . $classId);
    }

    private function trainerBreadcrumb(int $classId, ?string $tail = null): array
    {
        $crumb = [
            ['label' => 'Kelas Saya', 'url' => route_to('workspace.trainer.classes')],
            ['label' => 'Kelas', 'url' => route_to('workspace.trainer.class.detail', (string) $classId)],
        ];
        if ($tail !== null) {
            $crumb[] = ['label' => $tail];
        }
        return $crumb;
    }

    private function userId(): int
    {
        if (session()->get('isLoggedIn') !== true) {
            redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk terlebih dahulu.')->send();
            exit;
        }
        $id = (int) session()->get('user_id');
        if ($id < 1) {
            redirect()->to('/dashboard/masuk')->with('error', 'Sesi tidak valid. Silakan masuk ulang.')->send();
            exit;
        }
        if ((string) session()->get('account_status') !== 'active') {
            redirect()->to('/dashboard/status-akun')->with('error', 'Akun Anda belum aktif.')->send();
            exit;
        }
        $roles = (array) session()->get('roles');
        if (! in_array('trainer', $roles, true)) {
            redirect()->to('/dashboard/masuk')->with('error', 'Anda tidak memiliki akses Trainer.')->send();
            exit;
        }
        return $id;
    }

    private function render(string $pageTitle, string $viewPage, array $data, string $activeMenu = 'trainer', array $breadcrumbTail = []): string
    {
        $data = array_merge($data, [
            'pageTitle'      => $pageTitle,
            'breadcrumbTail'  => $breadcrumbTail,
            'activeMenu'     => $activeMenu,
            'activeSubmenu'   => '',
            'userName'       => session()->get('username') ?? 'Pengguna',
            'roleLabel'       => 'Pengajar',
            'innerView'       => 'main/workspace/pages/' . $viewPage,
        ]);
        return view('main/workspace/layouts/workspace_inner', $data);
    }
}