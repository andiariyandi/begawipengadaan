<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\AssignmentModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LessonModel;
use App\Models\Learning\QuestionModel;
use App\Models\Learning\QuestionOptionModel;
use App\Models\Learning\QuizModel;

class CurriculumController extends BaseController
{
    protected LearningClassModel $classModel;
    protected LearningModuleModel $moduleModel;
    protected LessonModel $lessonModel;
    protected QuizModel $quizModel;
    protected QuestionModel $questionModel;
    protected QuestionOptionModel $optionModel;
    protected AssignmentModel $assignmentModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel      = new LearningClassModel();
        $this->moduleModel     = new LearningModuleModel();
        $this->lessonModel     = new LessonModel();
        $this->quizModel       = new QuizModel();
        $this->questionModel   = new QuestionModel();
        $this->optionModel     = new QuestionOptionModel();
        $this->assignmentModel = new AssignmentModel();
    }

    public function index(?string $classId = null): string
    {
        $db = db_connect();
        $classes = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->orderBy('learning_classes.created_at', 'DESC')
            ->findAll();

        $selectedClassId = (int) ($classId ?? $this->request->getGet('class_id') ?? ($classes[0]['id'] ?? 0));
        $selectedClass = null;

        foreach ($classes as $c) {
            if ((int) $c['id'] === $selectedClassId) {
                $selectedClass = $c;
                break;
            }
        }

        if (! $selectedClass && ! empty($classes)) {
            $selectedClass = $classes[0];
            $selectedClassId = (int) $selectedClass['id'];
        }

        $preTest          = null;
        $postTest         = null;
        $modules          = [];
        $assignments      = [];
        $totalLessons     = 0;
        $totalQuizzes     = 0;
        $totalAssignments = 0;

        if ($selectedClassId > 0) {
            // 1. Pre-Test
            $preTest = $db->table('quizzes')
                ->where(['class_id' => $selectedClassId, 'type' => 'pre_test'])
                ->get()->getRowArray();

            if ($preTest) {
                $preTest['questions']       = $this->loadQuizQuestionsWithOptions((int) $preTest['id']);
                $preTest['total_questions'] = count($preTest['questions']);
                $preTest['total_points']    = (float) array_sum(array_column($preTest['questions'], 'points'));
                $totalQuizzes++;

                $ptRow = $db->table('pre_tests')->where('class_id', $selectedClassId)->get()->getRowArray();
                $preTest['is_enabled'] = $ptRow ? (int) $ptRow['is_enabled'] : ($preTest['status'] === 'published' ? 1 : 0);
            }

            // 2. Post-Test
            $postTest = $db->table('quizzes')
                ->where(['class_id' => $selectedClassId, 'type' => 'post_test'])
                ->get()->getRowArray();

            if ($postTest) {
                $postTest['questions']       = $this->loadQuizQuestionsWithOptions((int) $postTest['id']);
                $postTest['total_questions'] = count($postTest['questions']);
                $postTest['total_points']    = (float) array_sum(array_column($postTest['questions'], 'points'));
                $totalQuizzes++;
            }

            // 3. Modules & Lessons & Module Quizzes
            $modules = $this->moduleModel
                ->where('class_id', $selectedClassId)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $allModuleQuizzes = $db->table('quizzes')
                ->where(['class_id' => $selectedClassId, 'type' => 'lesson_quiz'])
                ->get()->getResultArray();

            foreach ($modules as $mIdx => &$mod) {
                $mid = (int) $mod['id'];
                $lessons = $this->lessonModel
                    ->where('module_id', $mid)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
                $mod['lessons'] = $lessons;
                $totalLessons += count($lessons);

                $lessonIds = array_map(static fn($l): int => (int) $l['id'], $lessons);

                // Match quiz for this module
                $matchedQuiz = null;
                foreach ($allModuleQuizzes as $q) {
                    if (! empty($q['lesson_id']) && in_array((int) $q['lesson_id'], $lessonIds, true)) {
                        $matchedQuiz = $q;
                        break;
                    }
                }
                if ($matchedQuiz === null && count($allModuleQuizzes) > $mIdx && empty($allModuleQuizzes[$mIdx]['lesson_id'])) {
                    $matchedQuiz = $allModuleQuizzes[$mIdx];
                }

                if ($matchedQuiz) {
                    $matchedQuiz['questions']       = $this->loadQuizQuestionsWithOptions((int) $matchedQuiz['id']);
                    $matchedQuiz['total_questions'] = count($matchedQuiz['questions']);
                    $matchedQuiz['total_points']    = (float) array_sum(array_column($matchedQuiz['questions'], 'points'));
                    $totalQuizzes++;
                }
                $mod['quiz'] = $matchedQuiz;
            }
            unset($mod);

            // 4. Assignments
            $assignments = $db->table('assignments a')
                ->select('a.*, l.title AS lesson_title, l.module_id, lm.title AS module_title,
                          (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) AS submission_count,
                          (SELECT COUNT(1) FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.status = "submitted") AS pending_count')
                ->join('lessons l', 'l.id = a.lesson_id', 'left')
                ->join('learning_modules lm', 'lm.id = l.module_id', 'left')
                ->where('a.class_id', $selectedClassId)
                ->orderBy('a.created_at', 'DESC')
                ->get()->getResultArray();

            $totalAssignments = count($assignments);
        }

        return view('dashboard/learning/curriculum/index', [
            'title'            => 'Manajemen Kurikulum | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Manajemen Kurikulum Pembelajaran',
            'classes'          => $classes,
            'selectedClass'    => $selectedClass,
            'selectedClassId'  => $selectedClassId,
            'modules'          => $modules,
            'preTest'          => $preTest,
            'postTest'         => $postTest,
            'assignments'      => $assignments,
            'totalLessons'     => $totalLessons,
            'totalQuizzes'     => $totalQuizzes,
            'totalAssignments' => $totalAssignments,
            'activeMenu'       => 'learning',
            'activeSubmenu'    => 'curriculum',
        ]);
    }

    protected function loadQuizQuestionsWithOptions(int $quizId): array
    {
        $db = db_connect();
        $questions = $db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        if (empty($questions)) {
            return [];
        }

        $qIds = array_map(static fn($q): int => (int) $q['id'], $questions);
        $options = $db->table('quiz_options')
            ->whereIn('question_id', $qIds)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $optionsMap = [];
        foreach ($options as $opt) {
            $optionsMap[(int) $opt['question_id']][] = $opt;
        }

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[(int) $q['id']] ?? [];
        }
        unset($q);

        return $questions;
    }

    // =========================================================================
    // MODUL & LESSON CRUD
    // =========================================================================

    public function storeModule()
    {
        $classId   = (int) $this->request->getPost('class_id');
        $title     = trim((string) $this->request->getPost('title'));
        $sortOrder = (int) $this->request->getPost('sort_order');

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Nama modul dan kelas wajib diisi.');
        }

        $this->moduleModel->insert([
            'class_id'    => $classId,
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => $sortOrder > 0 ? $sortOrder : 1,
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status'      => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Modul pembelajaran berhasil ditambahkan.');
    }

    public function updateModule(string $id)
    {
        $module = $this->moduleModel->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Nama modul tidak boleh kosong.');
        }

        $this->moduleModel->update($id, [
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status'      => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $module['class_id']))
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function deleteModule(string $id)
    {
        $module = $this->moduleModel->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        $classId     = $module['class_id'];
        $lessonCount = $this->lessonModel->where('module_id', $id)->countAllResults();
        if ($lessonCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus modul: Masih ada {$lessonCount} materi/lesson di dalam modul ini. Hapus materi terlebih dahulu.");
        }

        $this->moduleModel->delete($id);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Modul berhasil dihapus.');
    }

    public function storeLesson()
    {
        $moduleId    = (int) $this->request->getPost('module_id');
        $title       = trim((string) $this->request->getPost('title'));
        $contentType = $this->request->getPost('content_type') ?: 'text';

        if ($moduleId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul materi dan modul wajib diisi.');
        }

        $module = $this->moduleModel->find($moduleId);
        if ($module === null) {
            return redirect()->back()->with('error', 'Modul tidak ditemukan.');
        }

        $slug   = url_title($title, '-', true) . '-' . substr(uniqid(), -4);
        $userId = session()->get('user_id');

        $this->lessonModel->insert([
            'module_id'    => $moduleId,
            'title'        => $title,
            'slug'         => $slug,
            'content_type' => $contentType,
            'content'      => $this->request->getPost('content') ?: null,
            'external_url' => $this->request->getPost('external_url') ?: null,
            'sort_order'   => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'status'       => $this->request->getPost('status') ?: 'published',
            'visibility'   => 'internal',
            'created_by'   => $userId ? (int) $userId : null,
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $module['class_id']))
            ->with('success', 'Materi pembelajaran (lesson) berhasil ditambahkan.');
    }

    public function updateLesson(string $id)
    {
        $lesson = $this->lessonModel->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan.');
        }

        $module = $this->moduleModel->find($lesson['module_id']);
        $title  = trim((string) $this->request->getPost('title'));

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul materi tidak boleh kosong.');
        }

        $this->lessonModel->update($id, [
            'title'        => $title,
            'content_type' => $this->request->getPost('content_type') ?: $lesson['content_type'],
            'content'      => $this->request->getPost('content') ?: null,
            'external_url' => $this->request->getPost('external_url') ?: null,
            'sort_order'   => (int) $this->request->getPost('sort_order'),
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'status'       => $this->request->getPost('status') ?: 'published',
            'updated_by'   => (int) session()->get('user_id'),
        ]);

        $redirectClass = $module ? $module['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function deleteLesson(string $id)
    {
        $lesson = $this->lessonModel->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan.');
        }

        $module = $this->moduleModel->find($lesson['module_id']);
        $this->lessonModel->delete($id);

        $redirectClass = $module ? $module['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Materi berhasil dihapus.');
    }

    // =========================================================================
    // QUIZ (PRE-TEST, POST-TEST, MODULE QUIZ) CRUD
    // =========================================================================

    public function storeQuiz()
    {
        $classId      = (int) $this->request->getPost('class_id');
        $type         = (string) ($this->request->getPost('type') ?: 'lesson_quiz');
        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $durationPost = $this->request->getPost('duration_minutes');
        $durationMinutes = ($durationPost !== null && $durationPost !== '') ? max(0, (int) $durationPost) : null;
        $attemptLimit = max(1, (int) ($this->request->getPost('attempt_limit') ?: 1));
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $status       = (string) ($this->request->getPost('status') ?: 'published');
        $moduleId     = (int) $this->request->getPost('module_id');
        $lessonId     = (int) $this->request->getPost('lesson_id') ?: null;

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Kelas dan judul ujian/kuis wajib diisi.');
        }

        if (! in_array($type, ['pre_test', 'post_test', 'lesson_quiz'], true)) {
            return redirect()->back()->withInput()->with('error', 'Tipe ujian/kuis tidak valid.');
        }

        $db = db_connect();

        // Check if pre_test or post_test already exists for this class
        if (in_array($type, ['pre_test', 'post_test'], true)) {
            $existing = $db->table('quizzes')
                ->where(['class_id' => $classId, 'type' => $type])
                ->get()->getRowArray();
            if ($existing) {
                return redirect()->back()->withInput()->with('error', ucfirst(str_replace('_', ' ', $type)) . ' untuk kelas ini sudah ada. Silakan ubah yang sudah tersedia.');
            }
        }

        // If lesson_quiz and lesson_id not provided but module_id is provided
        if ($type === 'lesson_quiz' && empty($lessonId) && $moduleId > 0) {
            $firstLesson = $db->table('lessons')->where('module_id', $moduleId)->orderBy('sort_order', 'ASC')->get()->getRowArray();
            if ($firstLesson) {
                $lessonId = (int) $firstLesson['id'];
            } else {
                $userId = session()->get('user_id');
                $slug   = url_title($title, '-', true) . '-' . substr(uniqid(), -4);
                $db->table('lessons')->insert([
                    'module_id'    => $moduleId,
                    'title'        => 'Kuis: ' . $title,
                    'slug'         => $slug,
                    'content_type' => 'text',
                    'content'      => 'Evaluasi pemahaman materi modul.',
                    'sort_order'   => 1,
                    'is_required'  => $isRequired,
                    'status'       => 'published',
                    'visibility'   => 'internal',
                    'created_by'   => $userId ? (int) $userId : null,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
                $lessonId = (int) $db->insertID();
            }
        }

        $now = date('Y-m-d H:i:s');
        $db->table('quizzes')->insert([
            'class_id'         => $classId,
            'lesson_id'        => $lessonId,
            'type'             => $type,
            'title'            => $title,
            'instructions'     => $instructions ?: null,
            'duration_minutes' => $durationMinutes,
            'attempt_limit'    => $attemptLimit,
            'is_required'      => $isRequired,
            'status'           => $status,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

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

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Ujian/Kuis (' . esc($title) . ') berhasil dibuat.');
    }

    public function updateQuiz(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuis/Ujian tidak ditemukan.');
        }

        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $durationPost = $this->request->getPost('duration_minutes');
        $durationMinutes = ($durationPost !== null && $durationPost !== '') ? max(0, (int) $durationPost) : null;
        $attemptLimit = max(1, (int) ($this->request->getPost('attempt_limit') ?: $quiz['attempt_limit']));
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $status       = (string) ($this->request->getPost('status') ?: $quiz['status']);

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul tidak boleh kosong.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('quizzes')->where('id', (int) $id)->update([
            'title'            => $title,
            'instructions'     => $instructions ?: null,
            'duration_minutes' => $durationMinutes,
            'attempt_limit'    => $attemptLimit,
            'is_required'      => $isRequired,
            'status'           => $status,
            'updated_at'       => $now,
        ]);

        if ($quiz['type'] === 'pre_test') {
            $isEnabled = ($status === 'published') ? 1 : 0;
            $ptRow     = $db->table('pre_tests')->where('class_id', $quiz['class_id'])->get()->getRowArray();
            if ($ptRow) {
                $db->table('pre_tests')->where('class_id', $quiz['class_id'])->update([
                    'is_enabled'    => $isEnabled,
                    'attempt_limit' => $attemptLimit,
                    'status'        => $status,
                    'updated_at'    => $now,
                ]);
            } else {
                $db->table('pre_tests')->insert([
                    'class_id'      => $quiz['class_id'],
                    'is_enabled'    => $isEnabled,
                    'attempt_limit' => $attemptLimit,
                    'status'        => $status,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $quiz['class_id']))
            ->with('success', 'Pengaturan kuis/ujian berhasil diperbarui.');
    }

    public function toggleQuizStatus(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuis/Ujian tidak ditemukan.');
        }

        $newStatus = ($quiz['status'] === 'published') ? 'draft' : 'published';
        $now       = date('Y-m-d H:i:s');
        $db->table('quizzes')->where('id', (int) $id)->update([
            'status'     => $newStatus,
            'updated_at' => $now,
        ]);

        if ($quiz['type'] === 'pre_test') {
            $isEnabled = ($newStatus === 'published') ? 1 : 0;
            $db->table('pre_tests')->where('class_id', $quiz['class_id'])->update([
                'is_enabled' => $isEnabled,
                'status'     => $newStatus,
                'updated_at' => $now,
            ]);
        }

        $label = ($newStatus === 'published') ? 'dipublikasikan / diaktifkan' : 'dijadikan draft / dinonaktifkan';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $quiz['class_id']))
            ->with('success', "Status {$quiz['title']} berhasil {$label}.");
    }

    public function deleteQuiz(string $id)
    {
        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', (int) $id)->get()->getRowArray();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuis/Ujian tidak ditemukan.');
        }

        $attemptsCount = $db->table('quiz_attempts')->where('quiz_id', (int) $id)->countAllResults();
        if ($attemptsCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus kuis: Sudah ada {$attemptsCount} percobaan/pengerjaan peserta. Ubah status menjadi draft untuk menonaktifkan.");
        }

        $questions = $db->table('quiz_questions')->where('quiz_id', (int) $id)->get()->getResultArray();
        if (! empty($questions)) {
            $qIds = array_map(static fn($q): int => (int) $q['id'], $questions);
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

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $quiz['class_id']))
            ->with('success', 'Kuis/Ujian berhasil dihapus.');
    }

    // =========================================================================
    // SOAL & PILIHAN (QUESTIONS & OPTIONS) CRUD
    // =========================================================================

    public function storeQuestion()
    {
        $quizId        = (int) $this->request->getPost('quiz_id');
        $prompt        = trim((string) $this->request->getPost('prompt'));
        $points        = max(0.01, (float) ($this->request->getPost('points') ?: 1.0));
        $explanation   = trim((string) $this->request->getPost('explanation'));
        $options       = (array) $this->request->getPost('options');
        $correctOption = (int) $this->request->getPost('correct_option');

        $db   = db_connect();
        $quiz = $db->table('quizzes')->where('id', $quizId)->get()->getRowArray();
        if (! $quiz) {
            return redirect()->back()->withInput()->with('error', 'Kuis/Ujian tidak ditemukan.');
        }

        if ($prompt === '') {
            return redirect()->back()->withInput()->with('error', 'Pertanyaan soal wajib diisi.');
        }

        $cleanedOptions = [];
        foreach ($options as $opt) {
            $t = trim((string) $opt);
            if ($t !== '') {
                $cleanedOptions[] = $t;
            }
        }

        if (count($cleanedOptions) < 2) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus menyediakan 2 pilihan jawaban.');
        }

        if ($correctOption < 0 || $correctOption >= count($cleanedOptions)) {
            return redirect()->back()->withInput()->with('error', 'Pilih salah satu jawaban sebagai kunci jawaban yang benar.');
        }

        $maxOrder = (int) ($db->table('quiz_questions')->selectMax('sort_order')->where('quiz_id', $quizId)->get()->getRowArray()['sort_order'] ?? 0);
        $now      = date('Y-m-d H:i:s');

        $db->table('quiz_questions')->insert([
            'quiz_id'     => $quizId,
            'type'        => 'multiple_choice',
            'prompt'      => $prompt,
            'points'      => $points,
            'sort_order'  => $maxOrder + 1,
            'explanation' => $explanation ?: null,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
        $questionId = (int) $db->insertID();

        foreach ($cleanedOptions as $idx => $optText) {
            $db->table('quiz_options')->insert([
                'question_id' => $questionId,
                'option_text' => $optText,
                'is_correct'  => ($idx === $correctOption) ? 1 : 0,
                'sort_order'  => $idx + 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $quiz['class_id']))
            ->with('success', 'Soal pilihan ganda berhasil ditambahkan.');
    }

    public function updateQuestion(string $id)
    {
        $db       = db_connect();
        $question = $db->table('quiz_questions')->where('id', (int) $id)->get()->getRowArray();
        if (! $question) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Soal tidak ditemukan.');
        }

        $quiz          = $db->table('quizzes')->where('id', $question['quiz_id'])->get()->getRowArray();
        $prompt        = trim((string) $this->request->getPost('prompt'));
        $points        = max(0.01, (float) ($this->request->getPost('points') ?: 1.0));
        $explanation   = trim((string) $this->request->getPost('explanation'));
        $options       = (array) $this->request->getPost('options');
        $correctOption = (int) $this->request->getPost('correct_option');

        if ($prompt === '') {
            return redirect()->back()->withInput()->with('error', 'Pertanyaan tidak boleh kosong.');
        }

        $cleanedOptions = [];
        foreach ($options as $opt) {
            $t = trim((string) $opt);
            if ($t !== '') {
                $cleanedOptions[] = $t;
            }
        }

        if (count($cleanedOptions) < 2) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus menyediakan 2 pilihan jawaban.');
        }

        if ($correctOption < 0 || $correctOption >= count($cleanedOptions)) {
            return redirect()->back()->withInput()->with('error', 'Pilih salah satu jawaban yang benar sebagai kunci jawaban.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('quiz_questions')->where('id', (int) $id)->update([
            'prompt'      => $prompt,
            'points'      => $points,
            'explanation' => $explanation ?: null,
            'updated_at'  => $now,
        ]);

        // Replace options
        $db->table('quiz_options')->where('question_id', (int) $id)->delete();
        foreach ($cleanedOptions as $idx => $optText) {
            $db->table('quiz_options')->insert([
                'question_id' => (int) $id,
                'option_text' => $optText,
                'is_correct'  => ($idx === $correctOption) ? 1 : 0,
                'sort_order'  => $idx + 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $redirectClass = $quiz ? $quiz['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Soal pilihan ganda berhasil diperbarui.');
    }

    public function deleteQuestion(string $id)
    {
        $db       = db_connect();
        $question = $db->table('quiz_questions')->where('id', (int) $id)->get()->getRowArray();
        if (! $question) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Soal tidak ditemukan.');
        }

        $quiz = $db->table('quizzes')->where('id', $question['quiz_id'])->get()->getRowArray();
        $db->table('quiz_options')->where('question_id', (int) $id)->delete();
        $db->table('quiz_questions')->where('id', (int) $id)->delete();

        $redirectClass = $quiz ? $quiz['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Soal berhasil dihapus.');
    }

    // =========================================================================
    // ASSIGNMENT / TUGAS CRUD
    // =========================================================================

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
            return redirect()->back()->withInput()->with('error', 'Kelas dan judul tugas wajib diisi.');
        }

        $db       = db_connect();
        $lessonId = null;

        if ($level === 'module' && $moduleId > 0) {
            $firstLesson = $db->table('lessons')->where('module_id', $moduleId)->orderBy('sort_order', 'ASC')->get()->getRowArray();
            if ($firstLesson) {
                $lessonId = (int) $firstLesson['id'];
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

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Tugas baru berhasil ditambahkan.');
    }

    public function updateAssignment(string $id)
    {
        $db         = db_connect();
        $assignment = $db->table('assignments')->where('id', (int) $id)->get()->getRowArray();
        if (! $assignment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tugas tidak ditemukan.');
        }

        $title        = trim((string) $this->request->getPost('title'));
        $instructions = trim((string) $this->request->getPost('instructions'));
        $dueAt        = $this->request->getPost('due_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('due_at'))) : null;
        $isRequired   = $this->request->getPost('is_required') ? 1 : 0;
        $maxAttempts  = max(1, (int) ($this->request->getPost('max_attempts') ?: 1));
        $status       = (string) ($this->request->getPost('status') ?: $assignment['status']);
        $moduleId     = (int) $this->request->getPost('module_id');
        $level        = (string) ($this->request->getPost('level') ?: 'class');

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul tugas tidak boleh kosong.');
        }

        $lessonId = $assignment['lesson_id'];
        if ($level === 'class') {
            $lessonId = null;
        } elseif ($level === 'module' && $moduleId > 0) {
            $firstLesson = $db->table('lessons')->where('module_id', $moduleId)->orderBy('sort_order', 'ASC')->get()->getRowArray();
            if ($firstLesson) {
                $lessonId = (int) $firstLesson['id'];
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
            'updated_by'   => (int) session()->get('user_id'),
            'updated_at'   => $now,
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $assignment['class_id']))
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function deleteAssignment(string $id)
    {
        $db         = db_connect();
        $assignment = $db->table('assignments')->where('id', (int) $id)->get()->getRowArray();
        if (! $assignment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tugas tidak ditemukan.');
        }

        $submissionsCount = $db->table('assignment_submissions')->where('assignment_id', (int) $id)->countAllResults();
        if ($submissionsCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus tugas: Sudah ada {$submissionsCount} submisi dari peserta. Ubah status menjadi draft untuk menonaktifkan.");
        }

        $db->table('assignments')->where('id', (int) $id)->delete();

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $assignment['class_id']))
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
