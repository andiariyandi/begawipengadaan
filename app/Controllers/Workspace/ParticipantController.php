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
        return $this->runAction(fn() => $this->learning->enroll($this->userId(), $classId), 'Enrollment berhasil dikirim.');
    }

    public function myClasses()
    {
        $userId = $this->userId();
        $db = db_connect();

        $items = $db->table('enrollments e')
            ->select('
                e.id AS enrollment_id,
                e.class_id,
                e.status AS enrollment_status,
                e.enrolled_at,
                e.activated_at,
                e.cancelled_at,
                e.completed_at,
                e.final_score,
                e.passed_at,
                e.certificate_eligible_at,
                e.withdrawn_at,
                lc.code AS class_code,
                lc.name AS class_name,
                lc.status AS class_status,
                lc.learning_method,
                lc.location_or_link,
                lc.description AS class_description,
                lc.target_audience AS class_target_audience,
                lc.start_at,
                lc.end_at,
                lc.capacity,
                lc.passing_grade,
                lc.certificate_enabled,
                courses.title AS course_title,
                courses.slug AS course_slug,
                courses.level AS course_level,
                courses.duration_minutes,
                courses.summary AS course_summary,
                courses.description AS course_description,
                courses.cover_url AS course_cover_url,
                courses.target_audience AS course_target_audience,
                courses.learning_objectives AS course_learning_objectives,
                courses.prerequisites AS course_prerequisites,
                programs.title AS program_title,
                programs.slug AS program_slug,
                programs.cover_url AS program_cover_url,
                fa.path AS cover_path,
                cert.id AS certificate_id,
                cert.certificate_number,
                cert.status AS certificate_status,
                cert.issued_at AS certificate_issued_at
            ')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id', 'left')
            ->where('e.user_id', $userId)
            ->orderBy('e.updated_at', 'DESC')
            ->get()->getResultArray();

        $activeClasses = [];
        $completedClasses = [];
        $pendingClasses = [];
        $issuedCertsCount = 0;

        foreach ($items as &$row) {
            $classId = (int) $row['class_id'];
            $state = $this->resolveClassLearningState($classId, $userId);
            $row['state'] = $state;

            // Shortcut fields for easy access in views
            $row['state_code']               = $state['stateCode'];
            $row['cta']                      = $state['cta'];
            $row['current_step']             = $state['current_step'];
            $row['pre_test']                 = $state['pre_test'];
            $row['post_test']                = $state['post_test'];
            $row['modules']                  = $state['modules'];
            $row['all_lessons_count']        = $state['all_lessons_count'];
            $row['completed_lessons_count']  = $state['completed_lessons_count'];
            $row['overall_percent']          = $state['overall_percent'];
            $row['is_passed']                = $state['is_passed'];
            $row['trainers']                 = $state['trainers'];

            // Harmonize certificate info
            if (! empty($state['certificate'])) {
                $row['certificate_id']        = $state['certificate']['id'];
                $row['certificate_number']    = $state['certificate']['certificate_number'];
                $row['certificate_status']    = $state['certificate']['status'];
                $row['certificate_issued_at'] = $state['certificate']['issued_at'] ?? $row['certificate_issued_at'];
            }

            // Backward compatibility progress_summary for existing tests/views
            $row['progress_summary'] = [
                'completed' => (int) $state['completed_lessons_count'],
                'total'     => (int) $state['all_lessons_count'],
                'percent'   => (float) $state['overall_percent'],
            ];

            if (($row['certificate_status'] ?? '') === 'issued') {
                $issuedCertsCount++;
            }

            $eStatus = (string) ($row['enrollment_status'] ?? 'pending');
            if ($eStatus === 'active') {
                $activeClasses[] = $row;
            } elseif ($eStatus === 'completed') {
                $completedClasses[] = $row;
            } elseif ($eStatus === 'pending') {
                $pendingClasses[] = $row;
            }
        }
        unset($row);

        $summary = [
            'pending'             => count($pendingClasses),
            'active'              => count($activeClasses),
            'completed'           => count($completedClasses),
            'cancelled'           => 0,
            'withdrawn'           => 0,
            'issued_certificates' => $issuedCertsCount,
        ];
        foreach ($items as $row) {
            $key = (string) ($row['enrollment_status'] ?? '');
            if (in_array($key, ['cancelled', 'withdrawn'], true)) {
                $summary[$key]++;
            }
        }

        return view('main/workspace/pages/kelas_saya', [
            'pageTitle'        => 'Pembelajaran & Kelas Saya',
            'breadcrumbTail'   => [['label' => 'Kelas Saya']],
            'enrollments'      => $items,
            'activeClasses'    => $activeClasses,
            'completedClasses' => $completedClasses,
            'pendingClasses'   => $pendingClasses,
            'summary'          => $summary,
            'sectionContent'   => [
                'eyebrow'     => 'Arena Belajar & Kompetensi',
                'title'       => 'Pembelajaran & Kelas Saya',
                'description' => 'Kelola seluruh proses pembelajaran Anda dari awal pendaftaran, materi modul, kuis bab, tugas praktik, ujian akhir, hingga sertifikat kelulusan resmi.',
                'cards'       => [
                    [
                        'title' => 'Kelas Aktif',
                        'value' => sprintf('%02d', count($activeClasses)),
                        'meta'  => 'sedang dipelajari',
                        'icon'  => 'fas fa-book-reader',
                    ],
                    [
                        'title' => 'Kelas Selesai',
                        'value' => sprintf('%02d', count($completedClasses)),
                        'meta'  => 'tuntas & lulus',
                        'icon'  => 'fas fa-graduation-cap',
                    ],
                    [
                        'title' => 'Sertifikat Terbit',
                        'value' => sprintf('%02d', $issuedCertsCount),
                        'meta'  => 'resmi terverifikasi',
                        'icon'  => 'fas fa-award',
                    ],
                    [
                        'title' => 'Menunggu Approval',
                        'value' => sprintf('%02d', count($pendingClasses)),
                        'meta'  => 'dalam antrean operator',
                        'icon'  => 'fas fa-user-clock',
                    ],
                ],
            ],
        ]);
    }

    public function classDetail(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        if ($classIdInt <= 0 || ! is_numeric($classId)) {
            $classRow = db_connect()->table('learning_classes')->select('id')->where('code', $classId)->get()->getRowArray();
            if ($classRow) {
                $classIdInt = (int) $classRow['id'];
            }
        }
        $state = $this->resolveClassLearningState($classIdInt, $userId);

        return view('main/workspace/pages/detail_kelas', [
            'pageTitle'      => ($state['class']['name'] ?? 'Detail Kelas'),
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => $state['class']['name'] ?? 'Detail Kelas'],
            ],
            'state'          => $state,
            'classContext'   => $state['class'],
            'trainers'       => $state['trainers'],
            'enrollment'     => $state['enrollment'],
            'modules'        => $state['modules'],
            'cta'            => $state['cta'],
            'progressInfo'   => [
                'total'     => $state['all_lessons_count'],
                'completed' => $state['completed_lessons_count'],
                'percent'   => $state['overall_percent'],
            ],
        ]);
    }

    public function study(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        if ($classIdInt <= 0 || ! is_numeric($classId)) {
            $classRow = db_connect()->table('learning_classes')->select('id')->where('code', $classId)->get()->getRowArray();
            if ($classRow) {
                $classIdInt = (int) $classRow['id'];
            }
        }
        $state = $this->resolveClassLearningState($classIdInt, $userId);

        if ($state['enrollmentStatus'] === 'pending') {
            return redirect()->to(route_to('workspace.participant.class.detail', (string) $classIdInt))
                ->with('error', 'Kelas belum aktif. Selesaikan persetujuan pendaftaran terlebih dahulu.');
        }

        // Support explicit selection via query parameters
        $reqLessonId = (int) ($this->request->getGet('lesson') ?: 0);
        if ($reqLessonId > 0) {
            return $this->lessonDetail((string) $reqLessonId);
        }

        $reqQuizId = (int) ($this->request->getGet('quiz') ?: 0);
        if ($reqQuizId > 0) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $reqQuizId));
        }

        $reqAsgId = (int) ($this->request->getGet('assignment') ?: 0);
        if ($reqAsgId > 0) {
            return redirect()->to(route_to('workspace.participant.assignment.submission', (string) $classIdInt) . '?assignment_id=' . $reqAsgId);
        }

        // Automatically guide user to their next pending item
        $activeType = $state['current_step']['type'] ?? 'overview';
        $activeItem = $state['current_step']['item'] ?? null;

        if ($activeType === 'lesson' && ! empty($activeItem['id'])) {
            return $this->lessonDetail((string) $activeItem['id']);
        }

        if ($activeType === 'pre_test' && ! empty($state['pre_test']['quiz']['id'])) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $state['pre_test']['quiz']['id']))
                ->with('info', 'Silakan selesaikan Pre-Test terlebih dahulu sebelum memulai materi pembelajaran.');
        }

        if ($activeType === 'post_test' && ! empty($state['post_test']['quiz']['id'])) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $state['post_test']['quiz']['id']))
                ->with('info', 'Seluruh modul telah selesai! Silakan kerjakan Post-Test evaluasi akhir.');
        }

        if ($activeType === 'quiz' && ! empty($activeItem['id'])) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $activeItem['id']))
                ->with('info', 'Materi modul selesai! Silakan selesaikan Quiz Modul untuk membuka modul berikutnya.');
        }

        if ($activeType === 'assignment' && ! empty($activeItem['id'])) {
            return redirect()->to(route_to('workspace.participant.assignment.submission', (string) $classIdInt) . '?assignment_id=' . $activeItem['id']);
        }

        // Renders unified Learning Room
        return view('main/workspace/pages/belajar', [
            'pageTitle'      => 'Learning Room | ' . ($state['class']['name'] ?? 'Kelas'),
            'hideSidebar'    => true,
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => $state['class']['name'] ?? 'Kelas', 'url' => route_to('workspace.participant.class.detail', (string) $classIdInt)],
                ['label' => 'Belajar'],
            ],
            'state'          => $state,
            'activeType'     => $activeType,
            'activeItem'     => $activeItem,
            'classContext'   => $state['class'],
            'trainers'       => $state['trainers'],
            'enrollment'     => $state['enrollment'],
            'modules'        => $state['modules'],
            'progressInfo'   => [
                'total'     => $state['all_lessons_count'],
                'completed' => $state['completed_lessons_count'],
                'percent'   => $state['overall_percent'],
            ],
        ]);
    }

    public function lessonDetail(string $lessonId)
    {
        $userId = $this->userId();
        $db = db_connect();
        $lessonIdInt = (int) $lessonId;

        $lesson = $db->table('lessons l')
            ->select('l.*, m.id AS module_id, m.title AS module_title, m.class_id AS class_id, m.sort_order AS module_order')
            ->join('learning_modules m', 'm.id = l.module_id')
            ->where('l.id', $lessonIdInt)
            ->where('l.status', 'published')
            ->where('m.status', 'published')
            ->get()->getRowArray();

        if (! $lesson) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi lesson tidak ditemukan atau belum dipublikasikan.');
        }

        $classIdInt = (int) $lesson['class_id'];
        $state = $this->resolveClassLearningState($classIdInt, $userId);

        if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
            return redirect()->to(route_to('workspace.participant.class.detail', (string) $classIdInt))
                ->with('error', 'Pendaftaran kelas belum aktif.');
        }

        // Enforce Pre-Test gate
        if ($state['pre_test']['exists'] && ! $state['pre_test']['completed']) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $state['pre_test']['quiz']['id']))
                ->with('warning', 'Anda wajib menyelesaikan Pre-Test terlebih dahulu sebelum mengakses materi modul.');
        }

        // Check if this lesson is unlocked
        $isUnlocked = false;
        foreach ($state['modules'] as $mod) {
            foreach ($mod['lessons'] as $les) {
                if ((int) $les['id'] === $lessonIdInt) {
                    $isUnlocked = $les['is_unlocked'];
                    break 2;
                }
            }
        }

        if (! $isUnlocked && $state['enrollmentStatus'] !== 'completed') {
            return redirect()->to(route_to('workspace.participant.study', (string) $classIdInt))
                ->with('error', 'Materi ini masih terkunci. Silakan selesaikan materi dan quiz modul sebelumnya terlebih dahulu.');
        }

        // Auto-start lesson progress
        try {
            $this->learning->startLesson($userId, $lessonIdInt);
        } catch (Throwable $e) {
            log_message('error', 'lessonDetail startLesson failed: {message}', ['message' => $e->getMessage()]);
        }

        // Find sibling lessons for navigation
        $allLessons = [];
        foreach ($state['modules'] as $mod) {
            foreach ($mod['lessons'] as $les) {
                $allLessons[] = $les;
            }
        }
        $prevLesson = null;
        $nextLesson = null;
        $totalL = count($allLessons);
        for ($i = 0; $i < $totalL; $i++) {
            if ((int) $allLessons[$i]['id'] === $lessonIdInt) {
                if ($i > 0) {
                    $prevLesson = $allLessons[$i - 1];
                }
                if ($i + 1 < $totalL) {
                    $nextLesson = $allLessons[$i + 1];
                }
                break;
            }
        }

        $fileAsset = null;
        if (! empty($lesson['file_id'])) {
            $fileAsset = $db->table('file_assets')->where('id', (int) $lesson['file_id'])->get()->getRowArray();
        }

        return view('main/workspace/pages/belajar', [
            'pageTitle'      => $lesson['title'] . ' | ' . ($state['class']['name'] ?? 'Learning Room'),
            'hideSidebar'    => true,
            'breadcrumbTail' => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => $state['class']['name'] ?? 'Kelas', 'url' => route_to('workspace.participant.class.detail', (string) $classIdInt)],
                ['label' => 'Belajar', 'url' => route_to('workspace.participant.study', (string) $classIdInt)],
                ['label' => $lesson['title']],
            ],
            'state'          => $state,
            'activeType'     => 'lesson',
            'activeItem'     => $lesson,
            'fileAsset'      => $fileAsset,
            'prevLesson'     => $prevLesson,
            'nextLesson'     => $nextLesson,
            'classContext'   => $state['class'],
            'trainers'       => $state['trainers'],
            'enrollment'     => $state['enrollment'],
            'modules'        => $state['modules'],
            'progressInfo'   => [
                'total'     => $state['all_lessons_count'],
                'completed' => $state['completed_lessons_count'],
                'percent'   => $state['overall_percent'],
            ],
        ]);
    }

    public function progress(string $classId)
    {
        $this->userId();
        $items = db_connect()->table('learning_progress lp')
            ->select('lessons.title, lp.status, lp.progress_percent')
            ->join('lessons', 'lessons.id = lp.lesson_id')
            ->join('enrollments e', 'e.id = lp.enrollment_id')
            ->where(['e.user_id' => session()->get('user_id'), 'e.class_id' => $classId])
            ->orderBy('lessons.sort_order')->get()->getResultArray();

        return $this->learningPage('Progress Belajar', 'Kemajuan lesson pada kelas yang Anda ikuti.', array_map(
            static fn(array $item): string => ($item['title'] ?? '-') . ' · ' . ($item['status'] ?? '-') . ' · ' . ($item['progress_percent'] ?? 0) . '%',
            $items
        ));
    }

    public function preTest(string $classId)
    {
        $this->userId();
        $quiz = db_connect()->table('quizzes')
            ->where(['class_id' => (int) $classId, 'type' => 'pre_test', 'status' => 'published'])
            ->get()->getRowArray();
        if ($quiz) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quiz['id']));
        }
        return redirect()->to(route_to('workspace.participant.study', $classId))
            ->with('info', 'Kelas ini tidak memiliki Pre-Test.');
    }

    public function quiz(string $classId)
    {
        return redirect()->to(route_to('workspace.participant.study', $classId));
    }

    public function postTest(string $classId)
    {
        $this->userId();
        $quiz = db_connect()->table('quizzes')
            ->where(['class_id' => (int) $classId, 'type' => 'post_test', 'status' => 'published'])
            ->get()->getRowArray();
        if ($quiz) {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quiz['id']));
        }
        return redirect()->to(route_to('workspace.participant.study', $classId))
            ->with('info', 'Kelas ini tidak memiliki Post-Test.');
    }

    public function preTestResult(string $classId)
    {
        return $this->preTest($classId);
    }

    public function quizResult(string $classId)
    {
        return $this->quiz($classId);
    }

    public function postTestResult(string $classId)
    {
        return $this->postTest($classId);
    }

    public function assignments(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $db = db_connect();

        $state = $this->resolveClassLearningState($classIdInt, $userId);
        $enrollment = $state['enrollment'] ?? $db->table('enrollments')->where(['class_id' => $classIdInt, 'user_id' => $userId])->get()->getRowArray();
        $assignments = $db->table('assignments')->where('class_id', $classIdInt)->where('status', 'published')->orderBy('due_at', 'ASC')->get()->getResultArray();

        $assignmentsList = [];
        $activeCount = 0;
        $gradedCount = 0;
        $lockedCount = 0;

        foreach ($assignments as $asg) {
            $aid = (int) $asg['id'];
            $sub = null;
            if ($enrollment) {
                $sub = $db->table('assignment_submissions')
                    ->where(['assignment_id' => $aid, 'enrollment_id' => $enrollment['id']])
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

            // Check lock status based on learning progress
            $isUnlocked = true;
            $lockReason = '';

            if ($state) {
                if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
                    $isUnlocked = false;
                    $lockReason = 'Pendaftaran kelas belum aktif.';
                } elseif ($state['stateCode'] === 'pre_test_required') {
                    $isUnlocked = false;
                    $lockReason = 'Wajib menyelesaikan Pre-Test kelas terlebih dahulu.';
                } else {
                    $foundMod = null;
                    foreach ($state['modules'] as $mod) {
                        if (! empty($mod['assignment']) && (int) $mod['assignment']['id'] === $aid) {
                            $foundMod = $mod;
                            break;
                        }
                    }
                    if ($foundMod !== null) {
                        $isUnlocked = (bool) $foundMod['assignment']['is_unlocked'];
                        if (! $isUnlocked) {
                            if (! $foundMod['is_unlocked']) {
                                $lockReason = 'Selesaikan modul sebelumnya terlebih dahulu.';
                            } else {
                                $lockReason = 'Selesaikan materi pada modul ini terlebih dahulu.';
                            }
                        }
                    }
                }
            }

            if ($status === 'graded') {
                $gradedCount++;
            } elseif (! $isUnlocked) {
                $lockedCount++;
            } else {
                $activeCount++;
            }

            $asg['class_name'] = $state['class']['name'] ?? '';
            $asg['submission_status'] = $status;
            $asg['submission_status_label'] = $label;
            $asg['score'] = $sub['score'] ?? null;
            $asg['is_unlocked'] = $isUnlocked;
            $asg['lock_reason'] = $lockReason;
            $asg['study_url'] = route_to('workspace.participant.study', (string) $classIdInt);
            $asg['submit_url'] = site_url('workspace/peserta/kelas/' . $classIdInt . '/tugas/kirim?assignment_id=' . $aid);
            $assignmentsList[] = $asg;
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'       => 'Tugas Kelas',
            'assignmentsList' => $assignmentsList,
            'sectionContent'  => [
                'eyebrow'     => 'Aktivitas Belajar',
                'title'       => 'Tugas Kelas',
                'description' => 'Daftar penugasan dan status pengumpulan untuk kelas ini mengikuti alur kurikulum.',
                'cards'       => [
                    ['title' => 'Tugas Terbuka', 'value' => sprintf('%02d', $activeCount), 'meta' => 'siap dikerjakan/direvisi'],
                    ['title' => 'Terkunci', 'value' => sprintf('%02d', $lockedCount), 'meta' => 'mengikuti progress modul'],
                    ['title' => 'Telah Dinilai', 'value' => sprintf('%02d', $gradedCount), 'meta' => 'tugas selesai dinilai'],
                ],
                'items'       => [],
            ],
        ]);
    }

    public function allAssignments()
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

        $classStates = [];
        foreach ($assignments as $asg) {
            $cid = (int) $asg['class_id'];
            if (! isset($classStates[$cid])) {
                $classStates[$cid] = $this->resolveClassLearningState($cid, $userId);
            }
        }

        $assignmentsList = [];
        $activeCount = 0;
        $gradedCount = 0;
        $lockedCount = 0;
        foreach ($assignments as $asg) {
            $cid = (int) $asg['class_id'];
            $aid = (int) $asg['id'];
            $state = $classStates[$cid] ?? null;

            $sub = $db->table('assignment_submissions')
                ->where(['assignment_id' => $aid, 'enrollment_id' => $asg['enrollment_id']])
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

            // Check lock status based on learning progress
            $isUnlocked = true;
            $lockReason = '';

            if ($state) {
                if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
                    $isUnlocked = false;
                    $lockReason = 'Pendaftaran kelas belum aktif.';
                } elseif ($state['stateCode'] === 'pre_test_required') {
                    $isUnlocked = false;
                    $lockReason = 'Wajib menyelesaikan Pre-Test kelas terlebih dahulu.';
                } else {
                    $foundMod = null;
                    foreach ($state['modules'] as $mod) {
                        if (! empty($mod['assignment']) && (int) $mod['assignment']['id'] === $aid) {
                            $foundMod = $mod;
                            break;
                        }
                    }
                    if ($foundMod !== null) {
                        $isUnlocked = (bool) $foundMod['assignment']['is_unlocked'];
                        if (! $isUnlocked) {
                            if (! $foundMod['is_unlocked']) {
                                $lockReason = 'Selesaikan modul sebelumnya terlebih dahulu.';
                            } else {
                                $lockReason = 'Selesaikan materi pada modul ini terlebih dahulu.';
                            }
                        }
                    }
                }
            }

            if ($status === 'graded') {
                $gradedCount++;
            } elseif (! $isUnlocked) {
                $lockedCount++;
            } else {
                $activeCount++;
            }

            $asg['submission_status'] = $status;
            $asg['submission_status_label'] = $label;
            $asg['score'] = $sub['score'] ?? null;
            $asg['is_unlocked'] = $isUnlocked;
            $asg['lock_reason'] = $lockReason;
            $asg['study_url'] = route_to('workspace.participant.study', (string) $cid);
            $asg['submit_url'] = site_url('workspace/peserta/kelas/' . $cid . '/tugas/kirim?assignment_id=' . $aid);
            $assignmentsList[] = $asg;
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'       => 'Tugas Saya',
            'assignmentsList' => $assignmentsList,
            'sectionContent'  => [
                'eyebrow'     => 'Aktivitas Belajar',
                'title'       => 'Tugas Saya',
                'description' => 'Daftar seluruh tugas dari kelas pembelajaran Anda yang terbuka mengikuti alur kurikulum.',
                'cards'       => [
                    ['title' => 'Tugas Terbuka', 'value' => sprintf('%02d', $activeCount), 'meta' => 'siap dikerjakan/direvisi'],
                    ['title' => 'Terkunci', 'value' => sprintf('%02d', $lockedCount), 'meta' => 'mengikuti progress modul'],
                    ['title' => 'Telah Dinilai', 'value' => sprintf('%02d', $gradedCount), 'meta' => 'tugas selesai dinilai'],
                ],
                'items'       => [],
            ],
        ]);
    }

    public function allAssessments()
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

        $classStates = [];
        foreach ($quizzes as $quiz) {
            $cid = (int) $quiz['class_id'];
            if (! isset($classStates[$cid])) {
                $classStates[$cid] = $this->resolveClassLearningState($cid, $userId);
            }
        }

        $assessmentsList = [];
        $completedCount = 0;
        $pendingCount = 0;
        $lockedCount = 0;
        foreach ($quizzes as $quiz) {
            $cid = (int) $quiz['class_id'];
            $qid = (int) $quiz['id'];
            $qType = (string) $quiz['type'];
            $state = $classStates[$cid] ?? null;

            $attempt = $db->table('quiz_attempts')
                ->where(['quiz_id' => $qid, 'enrollment_id' => $quiz['enrollment_id']])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();

            $status = $attempt['status'] ?? 'not_started';
            $isCompleted = in_array($status, ['submitted', 'graded', 'passed', 'completed'], true);

            // Compute lock status based on learning progress
            $isUnlocked = true;
            $lockReason = '';

            if ($state) {
                if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
                    $isUnlocked = false;
                    $lockReason = 'Pendaftaran kelas belum aktif.';
                } elseif ($qType === 'pre_test') {
                    $isUnlocked = true;
                } elseif ($state['stateCode'] === 'pre_test_required') {
                    $isUnlocked = false;
                    $lockReason = 'Wajib menyelesaikan Pre-Test kelas terlebih dahulu.';
                } elseif ($qType === 'post_test') {
                    $allModulesDone = true;
                    foreach ($state['modules'] as $mod) {
                        if (! $mod['is_completed']) {
                            $allModulesDone = false;
                            break;
                        }
                    }
                    $isUnlocked = $allModulesDone || $state['enrollmentStatus'] === 'completed';
                    if (! $isUnlocked) {
                        $lockReason = 'Selesaikan seluruh modul pembelajaran untuk membuka Post-Test.';
                    }
                } else { // lesson_quiz
                    $foundMod = null;
                    foreach ($state['modules'] as $mod) {
                        if (! empty($mod['quiz']) && (int) $mod['quiz']['id'] === $qid) {
                            $foundMod = $mod;
                            break;
                        }
                    }
                    if ($foundMod !== null) {
                        $isUnlocked = (bool) $foundMod['quiz']['is_unlocked'];
                        if (! $isUnlocked) {
                            if (! $foundMod['is_unlocked']) {
                                $lockReason = 'Selesaikan modul sebelumnya terlebih dahulu.';
                            } else {
                                $lockReason = 'Selesaikan seluruh materi pada modul ini untuk membuka kuis.';
                            }
                        }
                    }
                }
            }

            if ($isCompleted) {
                $completedCount++;
            } elseif (! $isUnlocked) {
                $lockedCount++;
            } else {
                $pendingCount++;
            }

            $quiz['attempt_status'] = $status;
            $quiz['score'] = $attempt['score'] ?? null;
            $quiz['attempt_id'] = $attempt['id'] ?? null;
            $quiz['is_unlocked'] = $isUnlocked;
            $quiz['is_completed'] = $isCompleted;
            $quiz['lock_reason'] = $lockReason;
            $quiz['study_url'] = route_to('workspace.participant.study', (string) $cid);
            $quiz['take_url'] = route_to('workspace.participant.quiz.take', (string) $qid);
            $assessmentsList[] = $quiz;
        }

        return view('main/workspace/pages/assessment', [
            'pageTitle'        => 'Assessment Saya',
            'assessmentsList'  => $assessmentsList,
            'summary'          => [
                'total'     => count($assessmentsList),
                'completed' => $completedCount,
                'pending'   => $pendingCount,
                'locked'    => $lockedCount,
            ],
            'sectionContent'   => [
                'eyebrow'     => 'Evaluasi Pembelajaran',
                'title'       => 'Assessment Saya',
                'description' => 'Pre-test, Quiz modul, dan Post-test yang terbuka secara bertahap mengikuti kemajuan belajar Anda.',
                'cards'       => [
                    ['title' => 'Siap Dikerjakan', 'value' => sprintf('%02d', $pendingCount), 'meta' => 'terbuka & aktif'],
                    ['title' => 'Terkunci', 'value' => sprintf('%02d', $lockedCount), 'meta' => 'mengikuti alur modul'],
                    ['title' => 'Selesai Dikerjakan', 'value' => sprintf('%02d', $completedCount), 'meta' => 'telah diselesaikan'],
                ],
            ],
        ]);
    }

    public function myEvents()
    {
        $userId = $this->userId();
        $db = db_connect();

        $events = $db->table('event_registrations er')
            ->select('
                er.id AS registration_id,
                er.status AS registration_status,
                er.registered_at,
                e.id AS event_id,
                e.title,
                e.slug,
                e.type,
                e.mode,
                e.status AS event_status,
                e.start_at,
                e.end_at,
                e.location,
                e.meeting_url,
                e.certificate_enabled,
                e.attendance_open_at,
                e.attendance_close_at,
                e.summary,
                fa.path AS cover_path,
                u.name AS creator_name,
                a.id AS attendance_id,
                a.status AS attendance_status,
                a.recorded_at AS attendance_recorded_at,
                c.id AS certificate_id,
                c.certificate_number,
                c.status AS certificate_status
            ')
            ->join('events e', 'e.id = er.event_id')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_type = \'event_registration\' AND c.source_id = er.id', 'left')
            ->where('er.user_id', $userId)
            ->where('e.deleted_at IS NULL')
            ->orderBy('e.start_at', 'DESC')
            ->get()->getResultArray();

        $now = time();
        $activeEvents = [];
        $historyEvents = [];
        $attendanceEvents = [];
        $certificateEvents = [];

        foreach ($events as &$ev) {
            $startAt = ! empty($ev['start_at']) ? strtotime($ev['start_at']) : $now;
            $endAt = ! empty($ev['end_at']) ? strtotime($ev['end_at']) : $now;
            $attOpenAt = ! empty($ev['attendance_open_at']) ? strtotime((string) $ev['attendance_open_at']) : null;
            $attCloseAt = ! empty($ev['attendance_close_at']) ? strtotime((string) $ev['attendance_close_at']) : null;

            $hasAttended = (! empty($ev['attendance_status']) && in_array(strtolower($ev['attendance_status']), ['present', 'hadir'], true));
            $isFinished = ($ev['event_status'] === 'finished' || $endAt < $now);
            $isOngoing = (! $isFinished && $startAt <= $now && $endAt >= $now);
            $isUpcoming = (! $isFinished && ! $isOngoing);

            $isPresensiActive = ($attOpenAt !== null && $attOpenAt <= $now && ($attCloseAt === null || $attCloseAt > $now));
            $isPresensiClosed = ($attCloseAt !== null && $attCloseAt <= $now);

            $ev['has_attended'] = $hasAttended;
            $ev['is_finished'] = $isFinished;
            $ev['is_ongoing'] = $isOngoing;
            $ev['is_upcoming'] = $isUpcoming;
            $ev['is_presensi_active'] = $isPresensiActive;
            $ev['is_presensi_closed'] = $isPresensiClosed;

            if ($isFinished || $hasAttended) {
                $historyEvents[] = $ev;
            } else {
                $activeEvents[] = $ev;
            }

            if ($hasAttended) {
                $attendanceEvents[] = $ev;
            }

            if (! empty($ev['certificate_status']) && $ev['certificate_status'] === 'issued') {
                $certificateEvents[] = $ev;
            }
        }
        unset($ev);

        return view('main/workspace/pages/event_saya', [
            'pageTitle'          => 'Event Saya',
            'breadcrumbTail'     => [['label' => 'Event Saya']],
            'events'             => $events,
            'activeEvents'       => $activeEvents,
            'historyEvents'      => $historyEvents,
            'attendanceEvents'   => $attendanceEvents,
            'certificateEvents'  => $certificateEvents,
            'summary'            => [
                'total'        => count($events),
                'active'       => count($activeEvents),
                'history'      => count($historyEvents),
                'attended'     => count($attendanceEvents),
                'certificates' => count($certificateEvents),
            ],
            'sectionContent'     => [
                'eyebrow'     => 'Kegiatan & Pelatihan',
                'title'       => 'Event Saya',
                'description' => 'Daftar seminar, workshop, webinar, dan bimbingan teknis yang Anda ikuti.',
                'cards'       => [
                    ['title' => 'Total Terdaftar', 'value' => sprintf('%02d', count($events)), 'meta' => 'event terdaftar'],
                    ['title' => 'Event Aktif', 'value' => sprintf('%02d', count($activeEvents)), 'meta' => 'siap diikuti'],
                    ['title' => 'Selesai / Hadir', 'value' => sprintf('%02d', count($historyEvents)), 'meta' => 'riwayat kegiatan'],
                    ['title' => 'Sertifikat Terbit', 'value' => sprintf('%02d', count($certificateEvents)), 'meta' => 'sertifikat resmi'],
                ],
            ],
        ]);
    }

    public function eventDetail(string $idOrSlug)
    {
        $userId = $this->userId();
        $db = db_connect();

        $query = $db->table('events e')
            ->select('e.*, fa.path AS cover_path, u.name AS creator_name, u.email AS creator_email')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->where('e.deleted_at IS NULL');

        if (is_numeric($idOrSlug)) {
            $query->where('e.id', (int) $idOrSlug);
        } else {
            $query->where('e.slug', $idOrSlug);
        }

        $event = $query->get()->getRowArray();
        if (! $event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $eventId = (int) $event['id'];

        // Get participant registration
        $registration = $db->table('event_registrations')
            ->where(['event_id' => $eventId, 'user_id' => $userId])
            ->get()->getRowArray();

        if (! $registration) {
            return redirect()->to(site_url('event/' . esc($event['slug'] ?: $event['id'])))
                ->with('info', 'Anda belum terdaftar pada event ini. Silakan mendaftar terlebih dahulu.');
        }

        // Get attendance
        $attendance = $db->table('attendances')
            ->where('event_registration_id', (int) $registration['id'])
            ->get()->getRowArray();

        // Get certificate
        $certificate = $db->table('certificates')
            ->where('source_type', 'event_registration')
            ->where('source_id', (int) $registration['id'])
            ->get()->getRowArray();

        // Presensi states
        $now = time();
        $openAt = ! empty($event['attendance_open_at']) ? strtotime((string) $event['attendance_open_at']) : null;
        $closeAt = ! empty($event['attendance_close_at']) ? strtotime((string) $event['attendance_close_at']) : null;

        $isPresensiActive = ($openAt !== null && $openAt <= $now && ($closeAt === null || $closeAt > $now));
        $isPresensiClosed = ($closeAt !== null && $closeAt <= $now);
        $isPresensiNotOpenYet = ($openAt === null || $openAt > $now);

        $hasAttended = ($attendance && in_array(strtolower($attendance['status']), ['present', 'hadir'], true));

        // Event schedule state
        $startAt = ! empty($event['start_at']) ? strtotime($event['start_at']) : $now;
        $endAt = ! empty($event['end_at']) ? strtotime($event['end_at']) : $now;

        $eventState = 'upcoming'; // Belum Dimulai
        if ($event['status'] === 'finished' || $endAt < $now) {
            $eventState = 'finished'; // Selesai
        } elseif ($startAt <= $now && $endAt >= $now) {
            $eventState = 'ongoing'; // Berlangsung
        }

        return view('main/workspace/pages/detail_event', [
            'pageTitle'            => $event['title'] . ' | Event Saya',
            'breadcrumbTail'       => [
                ['label' => 'Event Saya', 'url' => route_to('workspace.participant.events')],
                ['label' => $event['title']],
            ],
            'event'                => $event,
            'registration'         => $registration,
            'attendance'           => $attendance,
            'certificate'          => $certificate,
            'hasAttended'          => $hasAttended,
            'isPresensiActive'     => $isPresensiActive,
            'isPresensiClosed'     => $isPresensiClosed,
            'isPresensiNotOpenYet' => $isPresensiNotOpenYet,
            'eventState'           => $eventState,
        ]);
    }

    public function myAttendance()
    {
        $userId = $this->userId();
        $db = db_connect();

        $attendances = $db->table('attendances a')
            ->select('a.*, er.status AS registration_status, er.registered_at, e.id AS event_id, e.title AS event_title, e.slug AS event_slug, e.mode AS event_mode, e.type AS event_type, e.start_at, e.end_at, e.location')
            ->join('event_registrations er', 'er.id = a.event_registration_id')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.user_id', $userId)
            ->orderBy('a.recorded_at', 'DESC')
            ->get()->getResultArray();

        $presentCount = 0;
        foreach ($attendances as $att) {
            if (in_array(strtolower($att['status']), ['present', 'hadir'], true)) {
                $presentCount++;
            }
        }

        return view('main/workspace/pages/presensi_saya', [
            'pageTitle'      => 'Presensi Saya',
            'breadcrumbTail' => [['label' => 'Presensi Saya']],
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

    public function submitAttendanceCode()
    {
        $userId = $this->userId();
        $code = trim((string) $this->request->getPost('attendance_code'));

        if ($code === '') {
            return redirect()->back()->with('error', 'Silakan masukkan kode presensi event.');
        }

        $commService = new \App\Services\Communication\CommunicationService();
        try {
            $result = $commService->validateAndCheckIn($code, $userId);
            $eventTitle = $result['event']['title'] ?? 'Event';
            $successMsg = '✓ Kehadiran berhasil dicatat';
            return redirect()->back()
                ->with('message', $successMsg)
                ->with('attendance_success', [
                    'event_title' => $eventTitle,
                    'recorded_at' => date('d M Y, H:i') . ' WIB',
                    'status'      => 'Hadir (Terverifikasi)',
                ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function assignmentSubmission(string $classId)
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

        // Lock check via learning progress state
        $state = $this->resolveClassLearningState($classIdInt, $userId);
        if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
            return redirect()->to(route_to('workspace.participant.class.detail', (string) $classIdInt))
                ->with('error', 'Pendaftaran kelas belum aktif.');
        }
        if ($state['stateCode'] === 'pre_test_required') {
            return redirect()->to(route_to('workspace.participant.study', (string) $classIdInt))
                ->with('error', 'Pre-Test wajib dikerjakan terlebih dahulu sebelum mengakses pengumpulan tugas.');
        }
        $foundMod = null;
        foreach ($state['modules'] as $mod) {
            if (! empty($mod['assignment']) && (int) $mod['assignment']['id'] === (int) $assignment['id']) {
                $foundMod = $mod;
                break;
            }
        }
        if ($foundMod !== null && empty($foundMod['assignment']['is_unlocked'])) {
            $lockMsg = ! $foundMod['is_unlocked']
                ? 'Tugas ini masih terkunci. Selesaikan modul sebelumnya terlebih dahulu.'
                : 'Tugas ini masih terkunci. Selesaikan materi pembelajaran pada modul ini terlebih dahulu.';
            return redirect()->to(route_to('workspace.participant.study', (string) $classIdInt))
                ->with('error', $lockMsg);
        }

        $latestSubmission = null;
        $submittedFile = null;
        if ($enrollment) {
            $latestSubmission = $db->table('assignment_submissions')
                ->where(['assignment_id' => $assignment['id'], 'enrollment_id' => $enrollment['id']])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();

            if ($latestSubmission) {
                $submittedFile = $db->table('assignment_submission_files asf')
                    ->select('fa.*')
                    ->join('file_assets fa', 'fa.id = asf.file_id')
                    ->where('asf.submission_id', $latestSubmission['id'])
                    ->get()->getRowArray();
            }
        }

        return view('main/workspace/pages/tugas', [
            'pageTitle'        => 'Pengumpulan Tugas: ' . ($assignment['title'] ?? ''),
            'activeAssignment' => $assignment,
            'latestSubmission' => $latestSubmission,
            'submittedFile'    => $submittedFile,
            'sectionContent'   => [
                'eyebrow'     => 'Pengumpulan Tugas',
                'description' => 'Kirim laporan atau lembar kerja penugasan pembelajaran.',
            ],
        ]);
    }

    public function assignmentRevision(string $classId)
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

        // Lock check via learning progress state
        $state = $this->resolveClassLearningState($classIdInt, $userId);
        if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
            return redirect()->to(route_to('workspace.participant.class.detail', (string) $classIdInt))
                ->with('error', 'Pendaftaran kelas belum aktif.');
        }
        if ($state['stateCode'] === 'pre_test_required') {
            return redirect()->to(route_to('workspace.participant.study', (string) $classIdInt))
                ->with('error', 'Pre-Test wajib dikerjakan terlebih dahulu sebelum mengakses pengumpulan tugas.');
        }
        $foundMod = null;
        foreach ($state['modules'] as $mod) {
            if (! empty($mod['assignment']) && (int) $mod['assignment']['id'] === $assignmentId) {
                $foundMod = $mod;
                break;
            }
        }
        if ($foundMod !== null && empty($foundMod['assignment']['is_unlocked'])) {
            $lockMsg = ! $foundMod['is_unlocked']
                ? 'Tugas ini masih terkunci. Selesaikan modul sebelumnya terlebih dahulu.'
                : 'Tugas ini masih terkunci. Selesaikan materi pembelajaran pada modul ini terlebih dahulu.';
            return redirect()->to(route_to('workspace.participant.study', (string) $classIdInt))
                ->with('error', $lockMsg);
        }

        $fileData = null;
        $file = $this->request->getFile('submission_file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadDir = WRITEPATH . 'uploads/assignments';
            if (! is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
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

    public function feedback(string $classId)
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
        $db = db_connect();

        $quiz = $db->table('quizzes')
            ->select('quizzes.*, learning_classes.name AS class_name, learning_classes.code AS class_code')
            ->join('learning_classes', 'learning_classes.id = quizzes.class_id')
            ->where('quizzes.id', $quizId)
            ->where('quizzes.status', 'published')
            ->get()->getRowArray();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Quiz tidak ditemukan atau belum dipublikasikan.');
        }

        $classId = (int) $quiz['class_id'];
        $state = $this->resolveClassLearningState($classId, $userId);

        if ($state['enrollmentStatus'] !== 'active' && $state['enrollmentStatus'] !== 'completed') {
            return redirect()->to(route_to('workspace.participant.class.detail', (string) $classId))
                ->with('error', 'Pendaftaran kelas belum aktif.');
        }

        if ($state['stateCode'] === 'pre_test_required' && $quiz['type'] !== 'pre_test') {
            return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                ->with('error', 'Pre-Test wajib dikerjakan terlebih dahulu sebelum mengakses kuis ini.');
        }

        // Gate checks based on quiz type
        if ($quiz['type'] === 'post_test') {
            // Must have completed all modules
            $allDone = true;
            foreach ($state['modules'] as $mod) {
                if (! $mod['is_completed']) {
                    $allDone = false;
                    break;
                }
            }
            if (! $allDone && $state['enrollmentStatus'] !== 'completed') {
                return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                    ->with('error', 'Post-Test masih terkunci. Anda wajib menyelesaikan seluruh materi dan quiz modul terlebih dahulu.');
            }
        } elseif ($quiz['type'] === 'lesson_quiz') {
            // Find which module this quiz belongs to and check its lessons
            foreach ($state['modules'] as $mod) {
                if (! empty($mod['quiz']) && (int) $mod['quiz']['id'] === $quizId) {
                    if (! $mod['is_unlocked']) {
                        return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                            ->with('error', 'Modul kuis ini masih terkunci. Selesaikan modul sebelumnya terlebih dahulu.');
                    }
                    foreach ($mod['lessons'] as $les) {
                        if (! $les['is_completed']) {
                            return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                                ->with('error', 'Quiz modul ini masih terkunci. Silakan selesaikan seluruh materi pada modul ini terlebih dahulu.');
                        }
                    }
                    break;
                }
            }
        }

        $enrollment = $state['enrollment'];

        $questions = $db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
        $optionsByQuestion = [];
        if ($questions !== []) {
            $rows = $db->table('quiz_options')
                ->whereIn('question_id', array_map(static fn($q): int => (int) $q['id'], $questions))
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $optionsByQuestion[(int) $row['question_id']][] = $row;
            }
        }

        $attemptId = (int) ($this->request->getGet('attempt') ?: 0);
        $attempt = null;
        if ($attemptId > 0) {
            $attempt = $db->table('quiz_attempts')
                ->where(['id' => $attemptId, 'enrollment_id' => (int) $enrollment['id'], 'quiz_id' => $quizId])
                ->get()->getRowArray();
        }

        // If no specific attempt requested or attempt was omitted in URL, check for ongoing active attempt
        if ($attempt === null) {
            $attempt = $db->table('quiz_attempts')
                ->where(['quiz_id' => $quizId, 'enrollment_id' => (int) $enrollment['id'], 'status' => 'started'])
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
        }

        // Timer calculation
        $durationMinutes   = (int) ($quiz['duration_minutes'] ?? 0);
        $remainingSeconds  = null;
        $deadlineTimestamp = null;

        if ($attempt !== null && (string) ($attempt['status'] ?? '') === 'started') {
            if ($durationMinutes > 0 && ! empty($attempt['started_at'])) {
                $startedTimestamp  = strtotime($attempt['started_at']);
                $deadlineTimestamp = $startedTimestamp + ($durationMinutes * 60);
                $remainingSeconds  = $deadlineTimestamp - time();

                // If timer expired while away or on reload
                if ($remainingSeconds <= 0) {
                    try {
                        $this->learning->submitQuiz($userId, (int) $attempt['id'], []);
                        return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quizId) . '?attempt=' . $attempt['id'])
                            ->with('error', 'Waktu pengerjaan ujian telah habis. Jawaban Anda telah otomatis dikumpulkan.');
                    } catch (\Throwable $e) {
                        log_message('error', 'Auto-submit on expired timer failed: ' . $e->getMessage());
                    }
                }
            }
        }

        $latest = $db->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => (int) $enrollment['id']])
            ->orderBy('attempt_number', 'DESC')
            ->get()->getRowArray();
        $attemptCount = (int) $db->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => (int) $enrollment['id']])
            ->countAllResults();

        return view('main/workspace/pages/quiz_take', [
            'pageTitle'         => $quiz['title'] ?? 'Quiz',
            'hideSidebar'       => true,
            'breadcrumbTail'    => [
                ['label' => 'Kelas Saya', 'url' => route_to('workspace.participant.classes')],
                ['label' => $quiz['class_name'] ?? 'Kelas', 'url' => route_to('workspace.participant.class.detail', (string) $classId)],
                ['label' => 'Belajar', 'url' => route_to('workspace.participant.study', (string) $classId)],
                ['label' => $quiz['title'] ?? 'Quiz'],
            ],
            'quiz'              => $quiz,
            'questions'         => $questions,
            'optionsByQuestion' => $optionsByQuestion,
            'attempt'           => $attempt,
            'attemptCount'      => $attemptCount,
            'attemptLimit'      => (int) ($quiz['attempt_limit'] ?? 1),
            'latest'            => $latest,
            'remaining'         => max(0, (int) ($quiz['attempt_limit'] ?? 1) - $attemptCount),
            'classContext'      => $state['class'],
            'state'             => $state,
            'durationMinutes'   => $durationMinutes,
            'remainingSeconds'  => $remainingSeconds,
            'deadlineTimestamp' => $deadlineTimestamp,
        ]);
    }

    public function submitQuiz(int $attemptId)
    {
        $userId = $this->userId();
        $db = db_connect();

        $attempt = $db->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
        if (! $attempt) {
            return redirect()->back()->with('error', 'Percobaan quiz tidak ditemukan.');
        }

        $quiz = $db->table('quizzes')->where('id', (int) $attempt['quiz_id'])->get()->getRowArray();
        if (! $quiz) {
            return redirect()->back()->with('error', 'Quiz tidak ditemukan.');
        }

        // If attempt was already submitted or graded
        if ((string) ($attempt['status'] ?? '') !== 'started') {
            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quiz['id']) . '?attempt=' . $attemptId)
                ->with('info', 'Ujian telah selesai dikumpulkan sebelumnya.');
        }
        $classId = (int) $quiz['class_id'];

        try {
            $answers = (array) $this->request->getPost('answers');
            $normalized = [];
            foreach ($answers as $qid => $oid) {
                $normalized[(int) $qid] = (int) $oid;
            }
            $result = $this->learning->submitQuiz($userId, $attemptId, $normalized);
            $score = $result['score'] ?? 0;

            // Re-resolve state to determine intelligent next destination
            $state = $this->resolveClassLearningState($classId, $userId);

            if ($quiz['type'] === 'pre_test') {
                if ($state['stateCode'] === 'in_learning' && ! empty($state['current_step']['item']['id'])) {
                    return redirect()->to(route_to('workspace.participant.lesson.detail', (string) $state['current_step']['item']['id']))
                        ->with('success', "Pre-Test berhasil diselesaikan (Skor: {$score})! Modul 1 telah dibuka.");
                }
                return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                    ->with('success', "Pre-Test berhasil diselesaikan (Skor: {$score})! Silakan mulai materi pembelajaran.");
            }

            if ($quiz['type'] === 'lesson_quiz') {
                if ($state['stateCode'] === 'in_learning' && ! empty($state['current_step']['item']['id'])) {
                    return redirect()->to(route_to('workspace.participant.lesson.detail', (string) $state['current_step']['item']['id']))
                        ->with('success', "Quiz Modul berhasil diselesaikan (Skor: {$score})! Melanjutkan ke materi berikutnya: " . esc($state['current_step']['title']));
                }
                if ($state['stateCode'] === 'post_test_required' && ! empty($state['current_step']['item']['id'])) {
                    return redirect()->to(route_to('workspace.participant.quiz.take', (string) $state['current_step']['item']['id']))
                        ->with('success', "Quiz Modul selesai (Skor: {$score})! Seluruh modul telah selesai. Silakan kerjakan Post-Test.");
                }
                return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                    ->with('success', "Quiz Modul berhasil diselesaikan (Skor: {$score})! Modul berikutnya telah terbuka.");
            }

            if ($quiz['type'] === 'post_test') {
                return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                    ->with('success', "Post-Test berhasil diselesaikan (Skor: {$score})! Evaluasi kelulusan pembelajaran Anda telah diproses.");
            }

            return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quiz['id']) . '?attempt=' . $attemptId)
                ->with('success', "Quiz berhasil dinilai. Skor: {$score}");
        } catch (Throwable $e) {
            log_message('error', 'submitQuiz failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function startLesson(int $lessonId)
    {
        return $this->runAction(fn() => $this->learning->startLesson($this->userId(), $lessonId), 'Lesson dibuka.');
    }

    public function completeLesson(int $lessonId)
    {
        $userId = $this->userId();
        $db = db_connect();

        $lesson = $db->table('lessons')->where('id', $lessonId)->get()->getRowArray();
        if (! $lesson) {
            return redirect()->back()->with('error', 'Materi tidak ditemukan.');
        }

        $module = $db->table('learning_modules')->where('id', (int) $lesson['module_id'])->get()->getRowArray();
        $classId = (int) ($module['class_id'] ?? 0);

        try {
            $this->learning->completeLesson($userId, $lessonId);

            // Re-resolve state to determine intelligent next destination
            $state = $this->resolveClassLearningState($classId, $userId);

            if ($state['stateCode'] === 'in_learning' && ! empty($state['current_step']['item']['id'])) {
                $nextLessonId = (int) $state['current_step']['item']['id'];
                return redirect()->to(route_to('workspace.participant.lesson.detail', (string) $nextLessonId))
                    ->with('success', 'Materi selesai! Melanjutkan ke materi: ' . esc($state['current_step']['title']));
            }

            if ($state['stateCode'] === 'module_quiz_required' && ! empty($state['current_step']['item']['id'])) {
                $quizId = (int) $state['current_step']['item']['id'];
                return redirect()->to(route_to('workspace.participant.quiz.take', (string) $quizId))
                    ->with('info', 'Materi modul selesai! Silakan selesaikan Quiz Modul untuk membuka modul berikutnya.');
            }

            if ($state['stateCode'] === 'assignment_required' && ! empty($state['current_step']['item']['id'])) {
                $asgId = (int) $state['current_step']['item']['id'];
                return redirect()->to(route_to('workspace.participant.assignment.submission', (string) $classId) . '?assignment_id=' . $asgId)
                    ->with('info', 'Materi modul selesai! Silakan selesaikan tugas mandiri berikut.');
            }

            if ($state['stateCode'] === 'post_test_required' && ! empty($state['current_step']['item']['id'])) {
                $postTestId = (int) $state['current_step']['item']['id'];
                return redirect()->to(route_to('workspace.participant.quiz.take', (string) $postTestId))
                    ->with('success', 'Seluruh modul pembelajaran telah selesai! Silakan selesaikan Post-Test evaluasi akhir.');
            }

            return redirect()->to(route_to('workspace.participant.study', (string) $classId))
                ->with('success', 'Materi selesai dipelajari.');
        } catch (Throwable $e) {
            log_message('error', 'completeLesson failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function certificates()
    {
        $userId = $this->userId();
        $db = db_connect();

        $classCerts = $db->table('certificates c')
            ->select('c.*, lc.name AS class_name, courses.title AS course_title, NULL AS event_title, \'class\' AS cert_category')
            ->join('enrollments e', 'e.id = c.source_id AND c.source_type = \'learning_enrollment\'')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('c.user_id', $userId)
            ->whereIn('c.status', ['pending', 'issued'])
            ->get()->getResultArray();

        $eventCerts = $db->table('certificates c')
            ->select('c.*, NULL AS class_name, NULL AS course_title, e.title AS event_title, \'event\' AS cert_category')
            ->join('event_registrations er', 'er.id = c.source_id AND c.source_type = \'event_registration\'')
            ->join('events e', 'e.id = er.event_id')
            ->where('c.user_id', $userId)
            ->whereIn('c.status', ['pending', 'issued'])
            ->get()->getResultArray();

        $certificates = array_merge($classCerts, $eventCerts);
        usort($certificates, static fn($a, $b) => strtotime($b['created_at'] ?? 'now') <=> strtotime($a['created_at'] ?? 'now'));

        return view('main/workspace/pages/sertifikat', [
            'title'          => 'Sertifikat Saya | Workspace BegawiPengadaan',
            'pageTitle'      => 'Sertifikat Saya',
            'breadcrumbTail' => [['label' => 'Sertifikat Saya']],
            'certificates'   => $certificates,
        ]);
    }

    public function certificateDetail(string $id)
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

        return view('main/workspace/pages/sertifikat', [
            'pageTitle'      => 'Detail Sertifikat',
            'breadcrumbTail' => [['label' => 'Sertifikat Saya', 'url' => route_to('workspace.participant.certificates')], ['label' => 'Detail']],
            'title' => 'Detail Sertifikat | Workspace BegawiPengadaan',
            'certificates' => [$certificate],
        ]);
    }

    /**
     * Resolves complete modular learning journey state for a participant in a class.
     * Enforces:
     *  1. Pre-test requirement (configurable, blocks modules if uncompleted)
     *  2. Sequential module & lesson unlocking
     *  3. Module quiz requirement (configurable, blocks next module if uncompleted)
     *  4. Module assignment requirement (configurable, optional)
     *  5. Post-test requirement (configurable, unlocked only when all modules completed)
     *  6. Evaluation passing grade & certificate approval pipeline
     *
     * @return array<string, mixed>
     */
    public function resolveClassLearningState(int $classId, int $userId): array
    {
        $db = db_connect();

        // 1. Class & Course & Program Context
        $class = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, courses.slug AS course_slug, courses.summary AS course_summary, courses.description AS course_description, courses.level AS course_level, courses.duration_minutes, courses.cover_url AS course_cover_url, courses.target_audience AS course_target_audience, courses.learning_objectives AS course_learning_objectives, courses.prerequisites AS course_prerequisites, programs.title AS program_title, programs.slug AS program_slug, programs.cover_url AS program_cover_url, fa.path AS cover_path')
            ->join('courses', 'courses.id = lc.course_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->where('lc.id', $classId)
            ->get()->getRowArray();

        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        // 2. Active Trainers
        $trainers = $db->table('class_trainers ct')
            ->select('ct.user_id, ct.assigned_at, ct.status AS trainer_status, ct.role AS trainer_role, u.name, u.email, u.phone, u.profile_type')
            ->join('users u', 'u.id = ct.user_id')
            ->where(['ct.class_id' => $classId, 'ct.status' => 'active'])
            ->get()->getResultArray();

        // 3. Participant Enrollment
        $enrollment = $db->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();

        $enrollmentId = $enrollment ? (int) $enrollment['id'] : 0;
        $enrollmentStatus = (string) ($enrollment['status'] ?? 'not_enrolled');

        // 4. Pre-Test & Attempts
        $preTest = $db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'pre_test', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        $preTestAttempt = null;
        $preTestCompleted = false;
        if ($preTest && $enrollmentId > 0) {
            $preTestAttempt = $db->table('quiz_attempts')
                ->where(['quiz_id' => (int) $preTest['id'], 'enrollment_id' => $enrollmentId])
                ->whereIn('status', ['submitted', 'graded', 'passed', 'completed'])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();
            $preTestCompleted = $preTestAttempt !== null;
        }

        // 5. Post-Test & Attempts
        $postTest = $db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'post_test', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        $postTestAttempt = null;
        $postTestCompleted = false;
        if ($postTest && $enrollmentId > 0) {
            $postTestAttempt = $db->table('quiz_attempts')
                ->where(['quiz_id' => (int) $postTest['id'], 'enrollment_id' => $enrollmentId])
                ->whereIn('status', ['submitted', 'graded', 'passed', 'completed'])
                ->orderBy('attempt_number', 'DESC')
                ->get()->getRowArray();
            $postTestCompleted = $postTestAttempt !== null;
        }

        // 6. Lesson Quizzes & Attempts
        $lessonQuizzes = $db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'lesson_quiz', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $quizAttemptsMap = [];
        if ($lessonQuizzes !== [] && $enrollmentId > 0) {
            $quizIds = array_map(static fn($q): int => (int) $q['id'], $lessonQuizzes);
            $attempts = $db->table('quiz_attempts')
                ->where('enrollment_id', $enrollmentId)
                ->whereIn('quiz_id', $quizIds)
                ->whereIn('status', ['submitted', 'graded', 'passed', 'completed'])
                ->orderBy('score', 'DESC')
                ->get()->getResultArray();
            foreach ($attempts as $att) {
                $qid = (int) $att['quiz_id'];
                if (! isset($quizAttemptsMap[$qid])) {
                    $quizAttemptsMap[$qid] = $att;
                }
            }
        }

        // 7. Assignments & Submissions
        $assignments = $db->table('assignments')
            ->where(['class_id' => $classId, 'status' => 'published'])
            ->orderBy('due_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $assignmentSubmissionsMap = [];
        if ($assignments !== [] && $enrollmentId > 0) {
            $asgIds = array_map(static fn($a): int => (int) $a['id'], $assignments);
            $subs = $db->table('assignment_submissions')
                ->where('enrollment_id', $enrollmentId)
                ->whereIn('assignment_id', $asgIds)
                ->orderBy('attempt_number', 'DESC')
                ->get()->getResultArray();
            foreach ($subs as $sub) {
                $aid = (int) $sub['assignment_id'];
                if (! isset($assignmentSubmissionsMap[$aid])) {
                    $assignmentSubmissionsMap[$aid] = $sub;
                }
            }
        }

        // 8. Modules & Lessons
        $moduleRows = $db->table('learning_modules m')
            ->select('m.*')
            ->where(['m.class_id' => $classId, 'm.status' => 'published'])
            ->orderBy('m.sort_order', 'ASC')
            ->get()->getResultArray();

        $lessonRows = $db->table('lessons l')
            ->select('l.*, learning_modules.sort_order AS module_order')
            ->join('learning_modules', 'learning_modules.id = l.module_id')
            ->where(['learning_modules.class_id' => $classId, 'l.status' => 'published', 'learning_modules.status' => 'published'])
            ->orderBy('learning_modules.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();

        $lessonIds = array_map(static fn($l): int => (int) $l['id'], $lessonRows);
        $progressMap = $enrollmentId > 0 ? $this->loadProgressMap($enrollmentId, $lessonIds) : [];

        // Build Modules Structure and compute Sequential Unlocking
        $lessonsByModule = [];
        foreach ($lessonRows as $l) {
            $mid = (int) $l['module_id'];
            $lessonsByModule[$mid][] = $l;
        }

        $allLessonsCount = count($lessonRows);
        $completedLessonsCount = 0;
        foreach ($progressMap as $prog) {
            if (($prog['status'] ?? '') === 'completed') {
                $completedLessonsCount++;
            }
        }

        // Pre-Test gate: if pre-test exists and is not completed, modules are locked
        $preTestBlocksModules = ($preTest !== null && ! $preTestCompleted);
        $prevModuleCompleted = ! $preTestBlocksModules;
        $activeItemFound = false;
        $nextActionItem = null;

        $isGraduated = ($enrollmentStatus === 'completed' || ! empty($enrollment['passed_at']));

        if (! $isGraduated && $preTestBlocksModules && $enrollmentStatus === 'active') {
            $activeItemFound = true;
            $nextActionItem = [
                'type'  => 'pre_test',
                'title' => $preTest['title'] ?? 'Pre-Test',
                'item'  => $preTest,
            ];
        }

        $modulesTree = [];
        foreach ($moduleRows as $mIdx => $m) {
            $mid = (int) $m['id'];
            $modLessons = $lessonsByModule[$mid] ?? [];
            $modLessonIds = array_map(static fn($l): int => (int) $l['id'], $modLessons);

            // Match quiz to this module
            $modQuiz = null;
            foreach ($lessonQuizzes as $q) {
                if (! empty($q['lesson_id']) && in_array((int) $q['lesson_id'], $modLessonIds, true)) {
                    $modQuiz = $q;
                    break;
                }
            }
            if ($modQuiz === null && count($lessonQuizzes) > $mIdx && empty($lessonQuizzes[$mIdx]['lesson_id'])) {
                $modQuiz = $lessonQuizzes[$mIdx];
            }

            // Match assignment to this module
            $modAssignment = null;
            foreach ($assignments as $a) {
                if (! empty($a['lesson_id']) && in_array((int) $a['lesson_id'], $modLessonIds, true)) {
                    $modAssignment = $a;
                    break;
                }
            }
            if ($modAssignment === null) {
                $unassignedAssignments = array_values(array_filter($assignments, static fn($a) => empty($a['lesson_id'])));
                $unassignedCount = count($unassignedAssignments);
                if ($unassignedCount > 0) {
                    if ($unassignedCount >= count($moduleRows)) {
                        if (isset($unassignedAssignments[$mIdx])) {
                            $modAssignment = $unassignedAssignments[$mIdx];
                        }
                    } elseif ($mIdx === count($moduleRows) - 1) {
                        // Capstone class-level assignment belongs to the final module
                        $modAssignment = $unassignedAssignments[0];
                    }
                }
            }

            $isModuleUnlocked = $isGraduated || ($enrollmentStatus === 'active' && $prevModuleCompleted);
            $moduleLessonsData = [];
            $allModLessonsDone = true;
            $prevLessonDone = true;

            foreach ($modLessons as $l) {
                $lid = (int) $l['id'];
                $prog = $progressMap[$lid] ?? null;
                $pStatus = (string) ($prog['status'] ?? 'not_started');
                $isLessonCompleted = $isGraduated || ($pStatus === 'completed');

                $isLessonUnlocked = $isGraduated || ($isModuleUnlocked && $prevLessonDone);
                $isLessonActive = false;

                if (! $isLessonCompleted) {
                    $allModLessonsDone = false;
                    if ($isLessonUnlocked && ! $activeItemFound && $enrollmentStatus === 'active') {
                        $isLessonActive = true;
                        $activeItemFound = true;
                        $nextActionItem = [
                            'type'   => 'lesson',
                            'title'  => $l['title'],
                            'item'   => $l,
                            'module' => $m,
                        ];
                    }
                }

                $moduleLessonsData[] = [
                    'id'           => $lid,
                    'title'        => $l['title'],
                    'content_type' => $l['content_type'],
                    'sort_order'   => (int) $l['sort_order'],
                    'status'       => $isGraduated ? 'completed' : $pStatus,
                    'is_completed' => $isLessonCompleted,
                    'is_unlocked'  => $isLessonUnlocked,
                    'is_active'    => $isLessonActive,
                    'progress'     => $prog,
                    'raw'          => $l,
                ];

                $prevLessonDone = $isLessonCompleted;
            }

            // Quiz state for this module
            $quizData = null;
            $isQuizCompleted = true;
            if ($modQuiz !== null) {
                $qid = (int) $modQuiz['id'];
                $qAttempt = $quizAttemptsMap[$qid] ?? null;
                $isQuizCompleted = $isGraduated || ($qAttempt !== null);
                $isQuizUnlocked = $isGraduated || ($isModuleUnlocked && $allModLessonsDone);
                $isQuizActive = false;

                if (! $isGraduated && $isQuizUnlocked && ! $isQuizCompleted && ! $activeItemFound && $enrollmentStatus === 'active') {
                    $isQuizActive = true;
                    $activeItemFound = true;
                    $nextActionItem = [
                        'type'   => 'quiz',
                        'title'  => $modQuiz['title'],
                        'item'   => $modQuiz,
                        'module' => $m,
                    ];
                }

                $quizData = [
                    'id'           => $qid,
                    'title'        => $modQuiz['title'],
                    'is_required'  => (bool) ($modQuiz['is_required'] ?? 0),
                    'is_unlocked'  => $isQuizUnlocked,
                    'is_completed' => $isQuizCompleted,
                    'is_active'    => $isQuizActive,
                    'attempt'      => $qAttempt,
                    'score'        => $qAttempt['score'] ?? null,
                    'raw'          => $modQuiz,
                ];
            }

            // Assignment state for this module
            $asgData = null;
            $isAsgSatisfied = true;
            if ($modAssignment !== null) {
                $aid = (int) $modAssignment['id'];
                $aSub = $assignmentSubmissionsMap[$aid] ?? null;
                $isSubDone = $aSub !== null && in_array($aSub['status'], ['submitted', 'in_review', 'graded'], true);
                $isAsgRequired = (bool) ($modAssignment['is_required'] ?? 0);
                if ($isAsgRequired && ! $isSubDone && ! $isGraduated) {
                    $isAsgSatisfied = false;
                }
                $isAsgUnlocked = $isGraduated || ($isModuleUnlocked && $allModLessonsDone);
                $isAsgActive = false;

                if (! $isGraduated && $isAsgUnlocked && ! $isSubDone && ! $activeItemFound && $enrollmentStatus === 'active') {
                    $isAsgActive = true;
                    $activeItemFound = true;
                    $nextActionItem = [
                        'type'   => 'assignment',
                        'title'  => $modAssignment['title'],
                        'item'   => $modAssignment,
                        'module' => $m,
                    ];
                }

                $asgData = [
                    'id'           => $aid,
                    'title'        => $modAssignment['title'],
                    'is_required'  => $isAsgRequired,
                    'is_unlocked'  => $isAsgUnlocked,
                    'is_submitted' => $isSubDone,
                    'is_active'    => $isAsgActive,
                    'submission'   => $aSub,
                    'score'        => $aSub['score'] ?? null,
                    'raw'          => $modAssignment,
                ];
            }

            $isModuleCompleted = $isGraduated || ($isModuleUnlocked && $allModLessonsDone);

            $modulesTree[] = [
                'id'           => $mid,
                'title'        => $m['title'],
                'sort_order'   => (int) $m['sort_order'],
                'is_unlocked'  => $isModuleUnlocked,
                'is_completed' => $isModuleCompleted,
                'lessons'      => $moduleLessonsData,
                'quiz'         => $quizData,
                'assignment'   => $asgData,
            ];

            // Next module unlocks only when this module is completed!
            $prevModuleCompleted = $isModuleCompleted;
        }

        $allModulesCompleted = $isGraduated || ($prevModuleCompleted && ! $preTestBlocksModules);

        // Post-Test Unlocking
        $isPostTestUnlocked = ($isGraduated || $allModulesCompleted) && in_array($enrollmentStatus, ['active', 'completed'], true);
        $isPostTestActive = false;
        if ($postTest !== null) {
            if (! $isGraduated && $isPostTestUnlocked && ! $postTestCompleted && ! $activeItemFound && $enrollmentStatus === 'active') {
                $isPostTestActive = true;
                $activeItemFound = true;
                $nextActionItem = [
                    'type'  => 'post_test',
                    'title' => $postTest['title'] ?? 'Post-Test',
                    'item'  => $postTest,
                ];
            }
        }

        // Graduation Evaluation & Certificate Pipeline
        $isFinishedAllActivities = $isGraduated || ($allModulesCompleted && ($postTest === null || $postTestCompleted));
        if ($isFinishedAllActivities && $enrollmentStatus === 'active') {
            try {
                $this->learning->evaluateCompletion($enrollmentId);
                $enrollment = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray() ?: $enrollment;
                $enrollmentStatus = (string) ($enrollment['status'] ?? 'active');
            } catch (Throwable $e) {
                log_message('error', 'evaluateCompletion trigger failed: {message}', ['message' => $e->getMessage()]);
            }
        }

        $certificate = null;
        $approval = null;
        if ($enrollmentId > 0) {
            $certificate = $db->table('certificates')
                ->where(['source_type' => 'learning_enrollment', 'source_id' => $enrollmentId])
                ->get()->getRowArray();
            $approval = $db->table('approvals')
                ->where(['resource_type' => 'certificate', 'resource_id' => $enrollmentId])
                ->get()->getRowArray();
        }

        $isPassed = ! empty($enrollment['passed_at']) || ((float) ($enrollment['final_score'] ?? 0) >= (float) ($class['passing_grade'] ?? 70.0));
        $certEnabled = (int) ($class['certificate_enabled'] ?? 0) === 1;

        // Compute overall progress summary
        $totalItems = $allLessonsCount;
        $completedItems = $completedLessonsCount;
        if ($preTest) {
            $totalItems++;
            if ($preTestCompleted) {
                $completedItems++;
            }
        }
        if ($postTest) {
            $totalItems++;
            if ($postTestCompleted) {
                $completedItems++;
            }
        }
        foreach ($modulesTree as $mTree) {
            if (! empty($mTree['quiz'])) {
                $totalItems++;
                if ($mTree['quiz']['is_completed']) {
                    $completedItems++;
                }
            }
            if (! empty($mTree['assignment'])) {
                $totalItems++;
                if ($mTree['assignment']['is_submitted']) {
                    $completedItems++;
                }
            }
        }
        if ($isGraduated) {
            $completedItems = $totalItems;
            $overallPercent = 100.0;
        } else {
            $overallPercent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0.0;
        }

        // Compute State Code and Dynamic Action CTA
        $stateCode = 'in_learning';
        $cta = [];

        if ($enrollmentStatus === 'not_enrolled') {
            $stateCode = 'not_enrolled';
            $cta = [
                'label'    => 'Daftar Kelas Ini',
                'url'      => route_to('workspace.participant.enroll', (string) $classId),
                'method'   => 'POST',
                'type'     => 'enroll',
                'class'    => 'btn btn-primary',
                'disabled' => false,
            ];
        } elseif ($enrollmentStatus === 'pending') {
            $stateCode = 'enrollment_pending';
            $cta = [
                'label'    => 'Menunggu Persetujuan Operator',
                'url'      => '#',
                'type'     => 'pending',
                'class'    => 'btn btn-secondary disabled',
                'disabled' => true,
            ];
        } elseif ($isGraduated || $isFinishedAllActivities) {
            if ($isPassed) {
                if ($certificate && $certificate['status'] === 'issued') {
                    $stateCode = 'certificate_issued';
                    $cta = [
                        'label'    => 'Unduh Sertifikat',
                        'url'      => route_to('workspace.participant.certificate.download', (string) $certificate['id']),
                        'type'     => 'certificate_download',
                        'class'    => 'btn btn-success',
                        'disabled' => false,
                    ];
                } elseif ($certEnabled) {
                    $stateCode = 'waiting_certificate';
                    $cta = [
                        'label'    => 'Menunggu Persetujuan Sertifikat',
                        'url'      => '#',
                        'type'     => 'certificate_pending',
                        'class'    => 'btn btn-secondary disabled',
                        'disabled' => true,
                    ];
                } else {
                    $stateCode = 'completed_passed';
                    $cta = [
                        'label'    => 'Pembelajaran Selesai (Lulus)',
                        'url'      => route_to('workspace.participant.study', (string) $classId),
                        'type'     => 'completed',
                        'class'    => 'btn btn-success',
                        'disabled' => false,
                    ];
                }
            } else {
                $stateCode = 'completed_failed';
                $cta = [
                    'label'    => 'Belum Memenuhi Passing Grade',
                    'url'      => route_to('workspace.participant.study', (string) $classId),
                    'type'     => 'failed',
                    'class'    => 'btn btn-danger disabled',
                    'disabled' => true,
                ];
            }
        } elseif ($preTestBlocksModules) {
            $stateCode = 'pre_test_required';
            $cta = [
                'label'    => 'Mulai Pre-Test',
                'url'      => route_to('workspace.participant.quiz.take', (string) $preTest['id']),
                'type'     => 'pre_test',
                'class'    => 'btn btn-primary',
                'disabled' => false,
            ];
        } elseif ($postTest !== null && ! $postTestCompleted && $allModulesCompleted) {
            $stateCode = 'post_test_required';
            $cta = [
                'label'    => 'Kerjakan Post-Test',
                'url'      => route_to('workspace.participant.quiz.take', (string) $postTest['id']),
                'type'     => 'post_test',
                'class'    => 'btn btn-primary',
                'disabled' => false,
            ];
        } elseif ($nextActionItem && $nextActionItem['type'] === 'lesson') {
            $stateCode = 'in_learning';
            $isFirstLesson = ($completedLessonsCount === 0);
            $cta = [
                'label'    => $isFirstLesson ? 'Mulai Kelas' : 'Lanjutkan Belajar: ' . $nextActionItem['title'],
                'url'      => route_to('workspace.participant.lesson.detail', (string) $nextActionItem['item']['id']),
                'type'     => 'lesson',
                'class'    => 'btn btn-primary',
                'disabled' => false,
            ];
        } elseif ($nextActionItem && $nextActionItem['type'] === 'quiz') {
            $stateCode = 'module_quiz_required';
            $cta = [
                'label'    => 'Kerjakan Quiz: ' . $nextActionItem['title'],
                'url'      => route_to('workspace.participant.quiz.take', (string) $nextActionItem['item']['id']),
                'type'     => 'quiz',
                'class'    => 'btn btn-warning text-dark',
                'disabled' => false,
            ];
        } elseif ($nextActionItem && $nextActionItem['type'] === 'assignment') {
            $stateCode = 'assignment_required';
            $cta = [
                'label'    => 'Submit Assignment: ' . $nextActionItem['title'],
                'url'      => route_to('workspace.participant.assignment.submission', (string) $classId) . '?assignment_id=' . $nextActionItem['item']['id'],
                'type'     => 'assignment',
                'class'    => 'btn btn-info text-dark',
                'disabled' => false,
            ];
        }

        return [
            'class'                  => $class,
            'trainers'               => $trainers,
            'enrollment'             => $enrollment,
            'enrollmentStatus'       => $enrollmentStatus,
            'stateCode'              => $stateCode,
            'cta'                    => $cta,
            'current_step'           => $nextActionItem,
            'pre_test'               => [
                'exists'    => $preTest !== null,
                'quiz'      => $preTest,
                'completed' => $preTestCompleted,
                'attempt'   => $preTestAttempt,
            ],
            'post_test'              => [
                'exists'      => $postTest !== null,
                'quiz'        => $postTest,
                'is_unlocked' => $isPostTestUnlocked,
                'completed'   => $postTestCompleted,
                'attempt'     => $postTestAttempt,
            ],
            'modules'                => $modulesTree,
            'has_assignments'        => $assignments !== [],
            'assignments'            => $assignments,
            'all_lessons_count'      => $allLessonsCount,
            'completed_lessons_count' => $completedLessonsCount,
            'overall_percent'        => $overallPercent,
            'is_passed'              => $isPassed,
            'certificate'            => $certificate,
            'approval'               => $approval,
            'certificate_enabled'    => $certEnabled,
        ];
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
        usort($tree, static fn(array $a, array $b): int => $a['order'] <=> $b['order']);
        foreach ($tree as &$mod) {
            usort($mod['lessons'], static fn(array $a, array $b): int => $a['order'] <=> $b['order']);
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
            'in_progress' => $inProgress,
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
                'items' => array_map(static fn(array $item): string => $item['title'] . ' · ' . $item['status'] . ' · ' . $item['attempt_limit'] . ' percobaan', $items) ?: ['Belum tersedia.'],
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
