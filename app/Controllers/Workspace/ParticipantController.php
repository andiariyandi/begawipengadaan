<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\LearningService;
use Throwable;

class ParticipantController extends BaseController
{
    private LearningService $learning;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->learning = new LearningService();
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
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $db = db_connect();

        $enrollment = $db->table('enrollments')->where(['class_id' => $classIdInt, 'user_id' => $userId])->get()->getRowArray();
        $assignments = $db->table('assignments')->where('class_id', $classIdInt)->orderBy('due_at', 'ASC')->get()->getResultArray();

        $assignmentsList = [];
        foreach ($assignments as $asg) {
            $sub = null;
            if ($enrollment) {
                $sub = $db->table('assignment_submissions')
                    ->where(['assignment_id' => $asg['id'], 'enrollment_id' => $enrollment['id']])
                    ->orderBy('attempt_number', 'DESC')
                    ->get()->getRowArray();
            }

            $status = $sub['status'] ?? 'not_submitted';
            $label = match ($status) {
                'graded'            => 'Telah Dinilai',
                'submitted'         => 'Telah Dikumpulkan',
                'revision_required' => 'Perlu Revisi',
                'in_review'         => 'Sedang Diperiksa',
                default             => 'Belum Mengumpulkan',
            };

            $asg['submission_status'] = $status;
            $asg['submission_status_label'] = $label;
            $asg['score'] = $sub['score'] ?? null;
            $assignmentsList[] = $asg;
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'       => 'Tugas Kelas',
            'assignmentsList' => $assignmentsList,
            'sectionContent'  => [
                'eyebrow'     => 'Aktivitas Belajar',
                'description' => 'Daftar penugasan dan status pengumpulan untuk kelas ini.',
                'items'       => [],
            ],
        ]);
    }

    public function allAssignments(): string
    {
        $userId = $this->userId();
        $db = db_connect();

        $assignments = $db->table('assignments a')
            ->select('a.*, lc.name AS class_name, lc.code AS class_code, e.id AS enrollment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.class_id = lc.id AND e.user_id = ' . (int) $userId)
            ->where('a.status', 'published')
            ->orderBy('a.due_at', 'ASC')
            ->get()->getResultArray();

        $assignmentsList = [];
        $activeCount = 0;
        $gradedCount = 0;
        foreach ($assignments as $asg) {
            $sub = $db->table('assignment_submissions')
                ->where(['assignment_id' => $asg['id'], 'enrollment_id' => $asg['enrollment_id']])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();

            $status = $sub['status'] ?? 'not_submitted';
            $label = match ($status) {
                'graded'            => 'Telah Dinilai',
                'submitted'         => 'Telah Dikumpulkan',
                'revision_required' => 'Perlu Revisi',
                'in_review'         => 'Sedang Diperiksa',
                default             => 'Belum Mengumpulkan',
            };

            if ($status === 'graded') {
                $gradedCount++;
            } else {
                $activeCount++;
            }

            $asg['submission_status'] = $status;
            $asg['submission_status_label'] = $label;
            $asg['score'] = $sub['score'] ?? null;
            $assignmentsList[] = $asg;
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'       => 'Tugas Saya',
            'assignmentsList' => $assignmentsList,
            'sectionContent'  => [
                'eyebrow'     => 'Aktivitas Belajar',
                'title'       => 'Tugas Saya',
                'description' => 'Daftar seluruh tugas dari kelas pembelajaran yang Anda ikuti.',
                'cards'       => [
                    ['title' => 'Tugas Aktif', 'value' => sprintf('%02d', $activeCount), 'meta' => 'perlu dikerjakan/direvisi'],
                    ['title' => 'Telah Dinilai', 'value' => sprintf('%02d', $gradedCount), 'meta' => 'tugas selesai'],
                    ['title' => 'Total Tugas', 'value' => sprintf('%02d', count($assignmentsList)), 'meta' => 'dari semua kelas aktif'],
                ],
                'items'       => [],
            ],
        ]);
    }

    public function allAssessments(): string
    {
        $userId = $this->userId();
        $db = db_connect();

        $quizzes = $db->table('quizzes q')
            ->select('q.*, lc.name AS class_name, lc.code AS class_code, e.id AS enrollment_id')
            ->join('learning_classes lc', 'lc.id = q.class_id')
            ->join('enrollments e', 'e.class_id = lc.id AND e.user_id = ' . (int) $userId)
            ->where('q.status', 'published')
            ->orderBy('q.type', 'ASC')
            ->orderBy('q.id', 'ASC')
            ->get()->getResultArray();

        $assessmentsList = [];
        $completedCount = 0;
        $pendingCount = 0;
        foreach ($quizzes as $quiz) {
            $attempt = $db->table('quiz_attempts')
                ->where(['quiz_id' => $quiz['id'], 'enrollment_id' => $quiz['enrollment_id']])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();

            $status = $attempt['status'] ?? 'not_started';
            if (in_array($status, ['submitted', 'graded', 'passed', 'completed'], true)) {
                $completedCount++;
            } else {
                $pendingCount++;
            }

            $quiz['attempt_status'] = $status;
            $quiz['score'] = $attempt['score'] ?? null;
            $quiz['attempt_id'] = $attempt['id'] ?? null;
            $assessmentsList[] = $quiz;
        }

        return view('main/workspace/pages/assessment', [
            'pageTitle'        => 'Assessment Saya',
            'assessmentsList'  => $assessmentsList,
            'summary'          => [
                'total'     => count($assessmentsList),
                'completed' => $completedCount,
                'pending'   => $pendingCount,
            ],
            'sectionContent'   => [
                'eyebrow'     => 'Evaluasi Pembelajaran',
                'title'       => 'Assessment Saya',
                'description' => 'Pre-test, Quiz berkala, dan Post-test untuk mengukur pemahaman materi.',
                'cards'       => [
                    ['title' => 'Total Assessment', 'value' => sprintf('%02d', count($assessmentsList)), 'meta' => 'ujian & kuis aktif'],
                    ['title' => 'Selesai Dikerjakan', 'value' => sprintf('%02d', $completedCount), 'meta' => 'telah diselesaikan'],
                    ['title' => 'Belum Dikerjakan', 'value' => sprintf('%02d', $pendingCount), 'meta' => 'menunggu dikerjakan'],
                ],
            ],
        ]);
    }

    public function myEvents(): string
    {
        $userId = $this->userId();
        $db = db_connect();

        $events = $db->table('event_registrations er')
            ->select('er.id AS registration_id, er.status AS registration_status, er.registered_at, e.id AS event_id, e.title, e.slug, e.type, e.mode, e.status AS event_status, e.start_at, e.end_at, e.location, e.meeting_url, e.certificate_enabled')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.user_id', $userId)
            ->where('e.deleted_at IS NULL')
            ->orderBy('e.start_at', 'DESC')
            ->get()->getResultArray();

        $activeCount = 0;
        $attendedCount = 0;
        foreach ($events as $ev) {
            if ($ev['registration_status'] === 'attended' || strtotime($ev['end_at']) < time()) {
                $attendedCount++;
            } else {
                $activeCount++;
            }
        }

        return view('main/workspace/pages/event_saya', [
            'pageTitle'      => 'Event Saya',
            'events'         => $events,
            'summary'        => [
                'total'    => count($events),
                'active'   => $activeCount,
                'attended' => $attendedCount,
            ],
            'sectionContent' => [
                'eyebrow'     => 'Kegiatan & Pelatihan',
                'title'       => 'Event Saya',
                'description' => 'Daftar seminar, workshop, webinar, dan bimbingan teknis yang Anda ikuti.',
                'cards'       => [
                    ['title' => 'Total Terdaftar', 'value' => sprintf('%02d', count($events)), 'meta' => 'event terdaftar'],
                    ['title' => 'Mendatang / Aktif', 'value' => sprintf('%02d', $activeCount), 'meta' => 'siap diikuti'],
                    ['title' => 'Selesai', 'value' => sprintf('%02d', $attendedCount), 'meta' => 'event terlaksana'],
                ],
            ],
        ]);
    }

    public function myAttendance(): string
    {
        $userId = $this->userId();
        $db = db_connect();

        $attendances = $db->table('attendances a')
            ->select('a.*, er.status AS registration_status, e.title AS event_title, e.mode AS event_mode, e.start_at, e.end_at, e.location')
            ->join('event_registrations er', 'er.id = a.event_registration_id')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.user_id', $userId)
            ->orderBy('a.recorded_at', 'DESC')
            ->get()->getResultArray();

        $presentCount = 0;
        foreach ($attendances as $att) {
            if (in_array($att['status'], ['present', 'hadir'], true)) {
                $presentCount++;
            }
        }

        return view('main/workspace/pages/presensi_saya', [
            'pageTitle'      => 'Presensi Saya',
            'attendances'    => $attendances,
            'summary'        => [
                'total'   => count($attendances),
                'present' => $presentCount,
            ],
            'sectionContent' => [
                'eyebrow'     => 'Catatan Kehadiran',
                'title'       => 'Presensi Saya',
                'description' => 'Rekap log presensi dan kehadiran Anda pada event serta pembelajaran.',
                'cards'       => [
                    ['title' => 'Total Catatan', 'value' => sprintf('%02d', count($attendances)), 'meta' => 'sesi kehadiran'],
                    ['title' => 'Hadir Terverifikasi', 'value' => sprintf('%02d', $presentCount), 'meta' => 'kehadiran sah'],
                ],
            ],
        ]);
    }

    public function assignmentSubmission(string $classId): string
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $db = db_connect();

        $enrollment = $db->table('enrollments')->where(['class_id' => $classIdInt, 'user_id' => $userId])->get()->getRowArray();
        $assignmentId = (int) ($this->request->getGet('assignment_id') ?: 0);

        $assignmentQuery = $db->table('assignments')->where('class_id', $classIdInt);
        if ($assignmentId > 0) {
            $assignmentQuery->where('id', $assignmentId);
        }
        $assignment = $assignmentQuery->orderBy('id', 'ASC')->get()->getRowArray();

        if (! $assignment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tugas tidak ditemukan.');
        }

        $latestSubmission = null;
        if ($enrollment) {
            $latestSubmission = $db->table('assignment_submissions')
                ->where(['assignment_id' => $assignment['id'], 'enrollment_id' => $enrollment['id']])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'        => 'Pengumpulan Tugas: ' . ($assignment['title'] ?? ''),
            'activeAssignment' => $assignment,
            'latestSubmission' => $latestSubmission,
            'sectionContent'   => [
                'eyebrow'     => 'Pengumpulan Tugas',
                'description' => 'Kirim laporan atau lembar kerja penugasan pembelajaran.',
            ],
        ]);
    }

    public function assignmentRevision(string $classId): string
    {
        return $this->assignmentSubmission($classId);
    }

    public function submitAssignment(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;

        $assignmentId = (int) $this->request->getPost('assignment_id');
        $content = (string) $this->request->getPost('content');
        if (trim($content) === '') {
            return redirect()->back()->withInput()->with('error', 'Konten jawaban tugas tidak boleh kosong.');
        }

        $fileData = null;
        $file = $this->request->getFile('submission_file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/assignments', $newName);
            $fileData = [
                'path'          => 'uploads/assignments/' . $newName,
                'original_name' => $file->getClientName(),
                'mime_type'     => $file->getClientMimeType(),
                'size_bytes'    => $file->getSize(),
            ];
        }

        try {
            $this->learning->submitAssignment($userId, $classIdInt, $assignmentId, $content, $fileData);
            return redirect()->to(site_url('workspace/peserta/kelas/' . $classId . '/tugas'))
                ->with('message', 'Tugas berhasil dikumpulkan untuk dievaluasi.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function feedback(string $classId): string
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $db = db_connect();

        $enrollment = $db->table('enrollments')->where(['class_id' => $classIdInt, 'user_id' => $userId])->get()->getRowArray();
        $existingFeedback = null;
        if ($enrollment) {
            $existingFeedback = $db->table('feedbacks')->where('enrollment_id', $enrollment['id'])->get()->getRowArray();
        }

        $items = [];
        if ($existingFeedback) {
            $items[] = 'Ulasan Anda (Rating ' . ($existingFeedback['rating'] ?? 5) . '/5): "' . ($existingFeedback['comment'] ?? '-') . '"';
        }

        return view('main/workspace/pages/penilaian', [
            'pageTitle'      => 'Feedback Pembelajaran Kelas #' . $classId,
            'sectionContent' => [
                'eyebrow'     => 'Ulasan & Feedback',
                'description' => 'Berikan masukan dan evaluasi untuk penyelenggaraan kelas ini.',
                'cards'       => [
                    ['title' => 'Status Feedback', 'value' => $existingFeedback ? 'Terkirim' : 'Belum Ada', 'meta' => 'partisipasi peserta'],
                ],
                'items'       => $items ?: ['Silakan kirimkan kritik dan saran pembelajaran di bawah ini.'],
            ],
        ]);
    }

    public function submitFeedback(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $db = db_connect();

        $enrollment = $db->table('enrollments')->where(['class_id' => $classIdInt, 'user_id' => $userId])->get()->getRowArray();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'Anda belum terdaftar pada kelas ini.');
        }

        $rating = (int) ($this->request->getPost('rating') ?: 5);
        $comment = (string) $this->request->getPost('comment');

        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }

        $now = date('Y-m-d H:i:s');
        $existing = $db->table('feedbacks')->where('enrollment_id', $enrollment['id'])->get()->getRowArray();

        if ($existing) {
            $db->table('feedbacks')->where('id', $existing['id'])->update([
                'rating'       => $rating,
                'comment'      => $comment,
                'status'       => 'submitted',
                'submitted_at' => $now,
                'updated_at'   => $now,
            ]);
        } else {
            $db->table('feedbacks')->insert([
                'enrollment_id' => $enrollment['id'],
                'rating'        => $rating,
                'comment'       => $comment,
                'status'        => 'submitted',
                'submitted_at'  => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        return redirect()->back()->with('message', 'Terima kasih, ulasan pembelajaran Anda telah tersimpan.');
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

        return view('main/workspace/pages/sertifikat', [
            'title'          => 'Sertifikat Saya | Workspace BegawiPBJ',
            'pageTitle'      => 'Sertifikat Saya',
            'breadcrumbTail' => [['label' => 'Sertifikat Saya']],
            'certificates'   => $certificates,
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
