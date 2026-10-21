<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\Learning\AssignmentModel;
use App\Models\Learning\AssignmentSubmissionModel;
use App\Models\Learning\ClassTrainerModel;
use App\Models\Learning\EnrollmentModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LearningProgressModel;
use App\Models\Learning\LessonModel;
use App\Models\Learning\QuestionModel;
use App\Models\Learning\QuestionOptionModel;
use App\Models\Learning\QuizModel;
use App\Services\Learning\QuizQuestionImportService;
use App\Services\LearningService;
use Throwable;

class TrainerController extends BaseController
{
    protected LearningService $learning;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->learning = new LearningService();
    }

    /**
     * 1. TRAINER HOME
     * Tampilan dashboard operasional pengajar (bukan admin).
     */
    public function index()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $assignedClasses = $db->table('class_trainers ct')
            ->select('ct.class_id, ct.assigned_at, ct.role AS trainer_role, lc.code, lc.name, lc.status AS class_status, lc.start_at, lc.end_at, lc.learning_method, lc.location_or_link, lc.description AS class_description, lc.target_audience AS class_target_audience, courses.title AS course_title, courses.cover_url AS course_cover_url, fa.path AS cover_path, courses.level AS course_level')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->where('ct.user_id', $userId)
            ->where('ct.status', 'active')
            ->orderBy('lc.start_at', 'DESC')
            ->get()->getResultArray();

        if ($assignedClasses === [] && $this->isPrivilegedOperator()) {
            $assignedClasses = $db->table('learning_classes lc')
                ->select('lc.id AS class_id, lc.created_at AS assigned_at, "primary" AS trainer_role, lc.code, lc.name, lc.status AS class_status, lc.start_at, lc.end_at, lc.learning_method, lc.location_or_link, lc.description AS class_description, lc.target_audience AS class_target_audience, courses.title AS course_title, courses.cover_url AS course_cover_url, fa.path AS cover_path, courses.level AS course_level')
                ->join('courses', 'courses.id = lc.course_id')
                ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
                ->orderBy('lc.start_at', 'DESC')
                ->limit(20)
                ->get()->getResultArray();
        }

        $classIds = array_map(static fn ($c): int => (int) $c['class_id'], $assignedClasses);

        if ($classIds === []) {
            return $this->render('Beranda Pengajar', 'trainer/home', [
                'hasClasses'            => false,
                'assignedClasses'       => [],
                'stats'                 => [
                    'totalClasses'       => 0,
                    'activeParticipants' => 0,
                    'avgProgress'        => 0.0,
                    'pendingReviews'     => 0,
                ],
                'recentActivities'      => [],
            ], 'trainer');
        }

        // Statistik agregat
        $totalClasses = count($assignedClasses);
        $activeParticipants = (int) $db->table('enrollments')
            ->whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->countAllResults();

        // Hitung rata-rata progres kelas dari learning_progress
        $progressAvgRow = $db->table('enrollments e')
            ->selectAvg('lp.progress_percent', 'avg_progress')
            ->join('learning_progress lp', 'lp.enrollment_id = e.id', 'inner')
            ->whereIn('e.class_id', $classIds)
            ->get()->getRowArray();
        $avgProgress = round((float) ($progressAvgRow['avg_progress'] ?? 0), 1);

        // Tugas yang perlu di-review pengajar
        $pendingReviews = (int) $db->table('assignment_submissions s')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->whereIn('a.class_id', $classIds)
            ->whereIn('s.status', ['submitted', 'in_review'])
            ->countAllResults();

        // Total assessment / kuis di kelas pengajar
        $totalQuizzes = (int) $db->table('quizzes')->whereIn('class_id', $classIds)->countAllResults();

        // Submisi tugas menunggu evaluasi (Mendukung client-side pagination)
        $pendingSubmissionsList = $db->table('assignment_submissions s')
            ->select('s.id AS submission_id, s.submitted_at, s.status, u.name AS student_name, a.title AS assignment_title, a.class_id, lc.name AS class_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->whereIn('a.class_id', $classIds)
            ->whereIn('s.status', ['submitted', 'in_review'])
            ->orderBy('s.submitted_at', 'DESC')
            ->limit(15)->get()->getResultArray();

        // Kelas dengan metrik ringkas
        $classesWithMetrics = [];
        foreach ($assignedClasses as $ac) {
            $cid = (int) $ac['class_id'];
            $pCount   = (int) $db->table('enrollments')->where('class_id', $cid)->where('status', 'active')->countAllResults();
            $mCount   = (int) $db->table('learning_modules')->where('class_id', $cid)->countAllResults();
            $asgCount = (int) $db->table('assignments')->where('class_id', $cid)->countAllResults();
            $qzCount  = (int) $db->table('quizzes')->where('class_id', $cid)->countAllResults();
            
            $clsAvgProg = (float) ($db->table('enrollments e')
                ->selectAvg('lp.progress_percent', 'avg_p')
                ->join('learning_progress lp', 'lp.enrollment_id = e.id')
                ->where('e.class_id', $cid)
                ->get()->getRowArray()['avg_p'] ?? 0.0);

            $ac['participant_count'] = $pCount;
            $ac['module_count']      = $mCount;
            $ac['assignment_count']  = $asgCount;
            $ac['quiz_count']        = $qzCount;
            $ac['avg_progress']      = round($clsAvgProg, 1);
            $classesWithMetrics[]    = $ac;
        }

        // Aktivitas terbaru:
        // 1. Peserta masuk kelas (enrollments)
        $recentEnrollments = $db->table('enrollments e')
            ->select('e.enrolled_at AS time, u.name AS user_name, lc.name AS class_name, "enrollment" AS type')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->whereIn('e.class_id', $classIds)
            ->where('e.status', 'active')
            ->orderBy('e.enrolled_at', 'DESC')
            ->limit(6)->get()->getResultArray();

        // 2. Peserta menyelesaikan materi (learning_progress)
        $recentLessons = $db->table('learning_progress lp')
            ->select('lp.completed_at AS time, u.name AS user_name, l.title AS lesson_title, lc.name AS class_name, "lesson_completed" AS type')
            ->join('enrollments e', 'e.id = lp.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('lessons l', 'l.id = lp.lesson_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->whereIn('e.class_id', $classIds)
            ->where('lp.status', 'completed')
            ->where('lp.completed_at IS NOT NULL')
            ->orderBy('lp.completed_at', 'DESC')
            ->limit(6)->get()->getResultArray();

        // 3. Submission baru (assignment_submissions)
        $recentSubmissions = $db->table('assignment_submissions s')
            ->select('s.id AS submission_id, s.submitted_at AS time, s.status, u.name AS user_name, a.title AS assignment_title, lc.name AS class_name, "submission" AS type')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->whereIn('a.class_id', $classIds)
            ->orderBy('s.submitted_at', 'DESC')
            ->limit(6)->get()->getResultArray();

        // 4. Kuis selesai dikerjakan (quiz_attempts)
        $recentQuizAttempts = $db->table('quiz_attempts qa')
            ->select('qa.submitted_at AS time, u.name AS user_name, q.title AS quiz_title, lc.name AS class_name, "quiz_completed" AS type')
            ->join('quizzes q', 'q.id = qa.quiz_id')
            ->join('enrollments e', 'e.id = qa.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = q.class_id')
            ->whereIn('q.class_id', $classIds)
            ->where('qa.status', 'completed')
            ->where('qa.submitted_at IS NOT NULL')
            ->orderBy('qa.submitted_at', 'DESC')
            ->limit(6)->get()->getResultArray();

        $allActivities = array_merge($recentEnrollments, $recentLessons, $recentSubmissions, $recentQuizAttempts);
        usort($allActivities, static fn ($a, $b) => strcmp((string) ($b['time'] ?? ''), (string) ($a['time'] ?? '')));
        $allActivities = array_slice($allActivities, 0, 16);

        return $this->render('Beranda Pengajar', 'trainer/home', [
            'hasClasses'            => true,
            'assignedClasses'       => $classesWithMetrics,
            'pendingSubmissionsList'=> $pendingSubmissionsList,
            'stats'                 => [
                'totalClasses'       => $totalClasses,
                'activeParticipants' => $activeParticipants,
                'avgProgress'        => $avgProgress,
                'pendingReviews'     => $pendingReviews,
                'totalQuizzes'       => $totalQuizzes,
            ],
            'recentActivities'      => $allActivities,
        ], 'trainer');
    }

    /**
     * 2. KELAS SAYA
     * Daftar kelas yang menjadi tanggung jawab trainer.
     */
    public function myClasses()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $items = $db->table('class_trainers ct')
            ->select('ct.class_id, ct.assigned_at, ct.status AS ct_status, ct.role AS trainer_role, lc.code, lc.name, lc.status AS class_status, lc.start_at, lc.end_at, lc.passing_grade, lc.learning_method, lc.location_or_link, lc.cover_url AS class_cover_url, lc.description AS class_description, lc.target_audience AS class_target_audience, courses.title AS course_title, courses.summary AS course_summary, courses.level AS course_level, courses.cover_url AS course_cover_url, fa.path AS cover_path, programs.title AS program_title')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('ct.user_id', $userId)
            ->where('ct.status', 'active')
            ->orderBy('lc.start_at', 'DESC')
            ->get()->getResultArray();

        if ($items === [] && $this->isPrivilegedOperator()) {
            $items = $db->table('learning_classes lc')
                ->select('lc.id AS class_id, lc.created_at AS assigned_at, lc.status AS ct_status, "primary" AS trainer_role, lc.code, lc.name, lc.status AS class_status, lc.start_at, lc.end_at, lc.passing_grade, lc.learning_method, lc.location_or_link, lc.cover_url AS class_cover_url, lc.description AS class_description, lc.target_audience AS class_target_audience, courses.title AS course_title, courses.summary AS course_summary, courses.level AS course_level, courses.cover_url AS course_cover_url, fa.path AS cover_path, programs.title AS program_title')
                ->join('courses', 'courses.id = lc.course_id')
                ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
                ->join('programs', 'programs.id = courses.program_id', 'left')
                ->orderBy('lc.start_at', 'DESC')
                ->get()->getResultArray();
        }

        foreach ($items as &$row) {
            $cid = (int) $row['class_id'];
            $row['participant_count'] = (int) $db->table('enrollments')->where('class_id', $cid)->where('status', 'active')->countAllResults();
            $row['modules_count']     = (int) $db->table('learning_modules')->where('class_id', $cid)->countAllResults();
            $row['quizzes_count']     = (int) $db->table('quizzes')->where('class_id', $cid)->countAllResults();
            $row['assignments_count'] = (int) $db->table('assignments')->where('class_id', $cid)->countAllResults();

            $clsAvgProg = (float) ($db->table('enrollments e')
                ->selectAvg('lp.progress_percent', 'avg_p')
                ->join('learning_progress lp', 'lp.enrollment_id = e.id')
                ->where('e.class_id', $cid)
                ->get()->getRowArray()['avg_p'] ?? 0.0);
            $row['avg_progress'] = round($clsAvgProg, 1);
        }
        unset($row);

        return $this->render('Kelas Saya', 'trainer/kelas_saya', ['classes' => $items], 'trainer');
    }

    /**
     * 3. DETAIL KELAS TRAINER (Learning Management Room versi Pengajar)
     * Dilengkapi tab: Overview, Materi, Peserta, Assessment, Assignment, Progress.
     */
    public function classDetail(string $classId)
    {
        $userId   = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();

        $class = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, courses.description AS course_desc, courses.summary AS course_summary, courses.level AS course_level, courses.duration_minutes, courses.learning_objectives AS course_learning_objectives, courses.prerequisites AS course_prerequisites, courses.target_audience AS course_target_audience, courses.cover_url AS course_cover_url, fa.path AS cover_path, programs.title AS program_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('lc.id', $classIdInt)->get()->getRowArray();
        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $activeTab = (string) ($this->request->getGet('tab') ?: 'overview');
        if (! in_array($activeTab, ['overview', 'materi', 'peserta', 'assessment', 'assignment', 'progres'], true)) {
            $activeTab = 'overview';
        }

        // Trainer pengampu kelas
        $trainers = $db->table('class_trainers ct')
            ->select('ct.assigned_at, ct.status, ct.role AS trainer_role, u.id AS user_id, u.name, u.email')
            ->join('users u', 'u.id = ct.user_id')
            ->where('ct.class_id', $classIdInt)
            ->where('ct.status', 'active')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        usort($trainers, static function ($a, $b) {
            $roleWeights = ['primary' => 1, 'co_trainer' => 2, 'guest' => 3];
            $wA = $roleWeights[$a['trainer_role'] ?? ''] ?? 99;
            $wB = $roleWeights[$b['trainer_role'] ?? ''] ?? 99;
            return $wA <=> $wB;
        });

        // 1. Modul & Lessons (Materi)
        $modules = $db->table('learning_modules')
            ->where('class_id', $classIdInt)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $lessons = [];
        if ($modules !== []) {
            $modIds = array_map(static fn ($m): int => (int) $m['id'], $modules);
            $lessons = $db->table('lessons')
                ->whereIn('module_id', $modIds)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        }

        $modulesWithLessons = [];
        foreach ($modules as $mod) {
            $mid = (int) $mod['id'];
            $mod['lessons'] = array_values(array_filter($lessons, static fn ($l): bool => (int) $l['module_id'] === $mid));
            $modulesWithLessons[] = $mod;
        }

        // 2. Peserta & Progress
        $participants = $db->table('enrollments e')
            ->select('e.id AS enrollment_id, e.status AS enrollment_status, e.enrolled_at, e.activated_at, e.completed_at, e.final_score, e.passed_at, u.id AS user_id, u.name, u.username, u.email')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.class_id', $classIdInt)
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()->getResultArray();

        $allLessonsCount = count($lessons);
        foreach ($participants as &$p) {
            $eid = (int) $p['enrollment_id'];
            $completedLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $eid)
                ->where('status', 'completed')
                ->countAllResults();

            $p['completed_lessons'] = $completedLessons;
            $p['total_lessons']     = $allLessonsCount;
            $p['progress_percent']   = $allLessonsCount > 0 ? round(($completedLessons / $allLessonsCount) * 100, 1) : 0.0;
        }
        unset($p);

        // 3. Quizzes (Assessment)
        $quizzes = $db->table('quizzes')
            ->where('class_id', $classIdInt)
            ->orderBy('type', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($quizzes as &$q) {
            $qid = (int) $q['id'];
            $q['question_count'] = (int) $db->table('quiz_questions')->where('quiz_id', $qid)->countAllResults();
            $q['attempts_count'] = (int) $db->table('quiz_attempts')->where('quiz_id', $qid)->countAllResults();
        }
        unset($q);

        // 4. Assignments (Tugas)
        $assignments = $db->table('assignments a')
            ->select('a.*, l.title AS lesson_title')
            ->join('lessons l', 'l.id = a.lesson_id', 'left')
            ->where('a.class_id', $classIdInt)
            ->orderBy('a.created_at', 'DESC')
            ->get()->getResultArray();

        foreach ($assignments as &$asg) {
            $aid = (int) $asg['id'];
            $asg['total_submissions'] = (int) $db->table('assignment_submissions')->where('assignment_id', $aid)->countAllResults();
            $asg['pending_reviews']   = (int) $db->table('assignment_submissions')->where('assignment_id', $aid)->whereIn('status', ['submitted', 'in_review'])->countAllResults();
            $asg['guide']             = parse_assignment_guide($asg['instructions'] ?? '');
        }
        unset($asg);

        // Ringkasan metrik kelas
        $activeParticipantCount = count(array_filter($participants, static fn ($p): bool => $p['enrollment_status'] === 'active'));
        $completedParticipantCount = count(array_filter($participants, static fn ($p): bool => in_array($p['enrollment_status'], ['completed'], true) || ! empty($p['passed_at'])));
        $completionRate = count($participants) > 0 ? round(($completedParticipantCount / count($participants)) * 100, 1) : 0.0;

        return $this->render($class['name'] ?? 'Detail Kelas', 'trainer/detail_kelas', [
            'classCtx'                  => $class,
            'trainers'                  => $trainers,
            'activeTab'                 => $activeTab,
            'modules'                   => $modulesWithLessons,
            'participants'              => $participants,
            'quizzes'                   => $quizzes,
            'assignments'               => $assignments,
            'stats'                     => [
                'participantCount'       => count($participants),
                'activeParticipantCount' => $activeParticipantCount,
                'completedCount'         => $completedParticipantCount,
                'completionRate'         => $completionRate,
                'moduleCount'            => count($modules),
                'lessonCount'            => $allLessonsCount,
                'quizCount'              => count($quizzes),
                'assignmentCount'        => count($assignments),
            ],
        ], 'trainer', [['label' => 'Kelas Saya', 'url' => route_to('workspace.trainer.classes')]]);
    }

    /**
     * MATERI PEMBELAJARAN (Agregat view untuk Sidebar)
     */
    public function allMaterials()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $assignedClasses = $this->getTrainerClasses($userId);
        if ($assignedClasses === []) {
            return $this->render('Materi Pembelajaran', 'trainer/all_materials', [
                'classes'      => [],
                'selectedClass'=> null,
                'modules'      => [],
            ], 'trainer');
        }

        $classIdParam = (int) ($this->request->getGet('class_id') ?: $assignedClasses[0]['id']);
        $this->assertTrainerOf($userId, $classIdParam);

        $selectedClass = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('lc.id', $classIdParam)->get()->getRowArray();

        $modules = $db->table('learning_modules')
            ->where('class_id', $classIdParam)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $modIds = array_map(static fn ($m): int => (int) $m['id'], $modules);
        $lessons = $modIds !== [] ? $db->table('lessons')->whereIn('module_id', $modIds)->orderBy('sort_order', 'ASC')->get()->getResultArray() : [];

        foreach ($modules as &$mod) {
            $mid = (int) $mod['id'];
            $mod['lessons'] = array_values(array_filter($lessons, static fn ($l): bool => (int) $l['module_id'] === $mid));
        }
        unset($mod);

        return $this->render('Materi Pembelajaran', 'trainer/all_materials', [
            'classes'       => $assignedClasses,
            'selectedClass' => $selectedClass,
            'modules'       => $modules,
        ], 'trainer');
    }

    /**
     * ASSESSMENT (Agregat view untuk Sidebar)
     */
    public function allQuizzes()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $assignedClasses = $this->getTrainerClasses($userId);
        if ($assignedClasses === []) {
            return $this->render('Assessment & Ujian', 'trainer/all_quizzes', [
                'classes'      => [],
                'selectedClass'=> null,
                'quizzes'      => [],
            ], 'trainer');
        }

        $classIdParam = (int) ($this->request->getGet('class_id') ?: $assignedClasses[0]['id']);
        $this->assertTrainerOf($userId, $classIdParam);

        $selectedClass = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('lc.id', $classIdParam)->get()->getRowArray();

        $quizzes = $db->table('quizzes')
            ->where('class_id', $classIdParam)
            ->orderBy('type', 'ASC')
            ->get()->getResultArray();

        foreach ($quizzes as &$q) {
            $qid = (int) $q['id'];
            $q['question_count'] = (int) $db->table('quiz_questions')->where('quiz_id', $qid)->countAllResults();
            $q['attempts_count'] = (int) $db->table('quiz_attempts')->where('quiz_id', $qid)->countAllResults();
        }
        unset($q);

        return $this->render('Assessment & Ujian', 'trainer/all_quizzes', [
            'classes'       => $assignedClasses,
            'selectedClass' => $selectedClass,
            'quizzes'       => $quizzes,
        ], 'trainer');
    }

    /**
     * PESERTA (Agregat view untuk Sidebar)
     */
    public function allParticipants()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $assignedClasses = $this->getTrainerClasses($userId);
        $classIds = array_map(static fn ($c): int => (int) $c['id'], $assignedClasses);

        if ($classIds === []) {
            return $this->render('Peserta Kelas', 'trainer/all_participants', [
                'classes'      => [],
                'selectedClassId' => null,
                'participants' => [],
            ], 'trainer');
        }

        $selectedClassId = (int) ($this->request->getGet('class_id') ?: 0);
        $targetClassIds = ($selectedClassId > 0 && in_array($selectedClassId, $classIds, true)) ? [$selectedClassId] : $classIds;

        $query = $db->table('enrollments e')
            ->select('e.id AS enrollment_id, e.class_id, e.status AS enrollment_status, e.enrolled_at, e.final_score, e.passed_at, lc.name AS class_name, lc.code AS class_code, u.id AS user_id, u.name, u.username, u.email')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->whereIn('e.class_id', $targetClassIds)
            ->orderBy('e.enrolled_at', 'DESC');

        $participants = $query->get()->getResultArray();

        foreach ($participants as &$p) {
            $eid = (int) $p['enrollment_id'];
            $cid = (int) $p['class_id'];
            $totalLessons = (int) $db->table('lessons l')
                ->join('learning_modules m', 'm.id = l.module_id')
                ->where('m.class_id', $cid)->countAllResults();

            $doneLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $eid)
                ->where('status', 'completed')->countAllResults();

            $p['total_lessons']   = $totalLessons;
            $p['done_lessons']    = $doneLessons;
            $p['progress_percent'] = $totalLessons > 0 ? round(($doneLessons / $totalLessons) * 100, 1) : 0.0;
        }
        unset($p);

        return $this->render('Peserta Kelas', 'trainer/all_participants', [
            'classes'         => $assignedClasses,
            'selectedClassId' => $selectedClassId,
            'participants'    => $participants,
        ], 'trainer');
    }

    /**
     * PROGRESS PESERTA (Agregat view untuk Sidebar)
     */
    public function allProgress()
    {
        $userId = $this->userId();
        $db     = db_connect();

        $assignedClasses = $this->getTrainerClasses($userId);
        $classIds = array_map(static fn ($c): int => (int) $c['id'], $assignedClasses);

        if ($classIds === []) {
            return $this->render('Progress Peserta', 'trainer/all_progress', [
                'classes'      => [],
                'classSummaries'=> [],
            ], 'trainer');
        }

        $classSummaries = [];
        foreach ($assignedClasses as $ac) {
            $cid = (int) $ac['id'];
            $totalEnrollments = (int) $db->table('enrollments')->where('class_id', $cid)->countAllResults();
            $activeEnrollments = (int) $db->table('enrollments')->where('class_id', $cid)->where('status', 'active')->countAllResults();
            $completedEnrollments = (int) $db->table('enrollments')->where('class_id', $cid)->where('status', 'completed')->countAllResults();

            $avgProg = (float) ($db->table('enrollments e')
                ->selectAvg('lp.progress_percent', 'avg_p')
                ->join('learning_progress lp', 'lp.enrollment_id = e.id')
                ->where('e.class_id', $cid)
                ->get()->getRowArray()['avg_p'] ?? 0.0);

            $ac['total_enrollments']     = $totalEnrollments;
            $ac['active_enrollments']    = $activeEnrollments;
            $ac['completed_enrollments'] = $completedEnrollments;
            $ac['avg_progress']          = round($avgProg, 1);
            $ac['completion_rate']       = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0.0;
            $classSummaries[]            = $ac;
        }

        return $this->render('Progress Monitoring', 'trainer/all_progress', [
            'classes'        => $assignedClasses,
            'classSummaries' => $classSummaries,
        ], 'trainer');
    }

    /**
     * DETAIL PESERTA & FEEDBACK CATATAN
     */
    public function participantDetail(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $cid    = (int) $classId;
        $eid    = (int) $enrollmentId;
        $this->assertTrainerOf($userId, $cid);

        $db = db_connect();

        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name, u.username, u.email, lc.name AS class_name, lc.code AS class_code')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->where('e.id', $eid)
            ->where('e.class_id', $cid)
            ->get()->getRowArray();
        if (! $enrollment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Peserta tidak ditemukan.');
        }

        // Lesson progress
        $lessonsProgress = $db->table('lessons l')
            ->select('l.id AS lesson_id, l.title AS lesson_title, l.content_type, m.title AS module_title, m.sort_order AS module_order, l.sort_order AS lesson_order, lp.status, lp.progress_percent, lp.started_at, lp.completed_at')
            ->join('learning_modules m', 'm.id = l.module_id')
            ->join('learning_progress lp', 'lp.lesson_id = l.id AND lp.enrollment_id = ' . $eid, 'left')
            ->where('m.class_id', $cid)
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('l.sort_order', 'ASC')
            ->get()->getResultArray();

        // Quiz attempts
        $quizAttempts = $db->table('quiz_attempts qa')
            ->select('qa.*, q.title AS quiz_title, q.type AS quiz_type')
            ->join('quizzes q', 'q.id = qa.quiz_id')
            ->where('qa.enrollment_id', $eid)
            ->orderBy('qa.started_at', 'DESC')
            ->get()->getResultArray();

        // Assignment submissions
        $assignmentSubmissions = $db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->where('s.enrollment_id', $eid)
            ->orderBy('s.submitted_at', 'DESC')
            ->get()->getResultArray();

        return $this->render('Detail Peserta: ' . $enrollment['name'], 'trainer/participant_detail', [
            'classId'              => $cid,
            'enrollment'           => $enrollment,
            'lessonsProgress'      => $lessonsProgress,
            'quizAttempts'         => $quizAttempts,
            'assignmentSubmissions'=> $assignmentSubmissions,
            'className'            => $enrollment['class_name'],
        ], 'trainer', $this->trainerBreadcrumb($cid, 'Detail Peserta'));
    }

    /**
     * SUBMIT CATATAN FEEDBACK UNTUK PESERTA
     */
    public function submitParticipantFeedback(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $cid    = (int) $classId;
        $eid    = (int) $enrollmentId;
        $this->assertTrainerOf($userId, $cid);

        $note = trim((string) $this->request->getPost('trainer_note'));
        if ($note !== '') {
            // Catat feedback pada submissions terbaru atau log
            db_connect()->table('assignment_submissions')
                ->where('enrollment_id', $eid)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->update(['feedback' => $note]);
        }

        return redirect()->back()->with('success', 'Catatan evaluasi untuk peserta berhasil disimpan.');
    }

    // =========================================================================
    // 4. MANAGEMENT MODULE & LESSON
    // =========================================================================

    public function modules(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi');
    }

    public function materials(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi');
    }

    public function createModule(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        if ($this->request->getMethod() === 'POST') {
            $title       = trim((string) $this->request->getPost('title'));
            $description = trim((string) $this->request->getPost('description'));
            $sortOrder   = (int) $this->request->getPost('sort_order');
            $status      = (string) $this->request->getPost('status');

            if ($title === '') {
                return redirect()->back()->withInput()->with('error', 'Judul modul wajib diisi.');
            }
            if (! in_array($status, ['draft', 'published', 'archived'], true)) {
                $status = 'draft';
            }

            $db         = db_connect();
            $classIdInt = (int) $classId;

            $maxRow   = $db->table('learning_modules')->selectMax('sort_order')->where('class_id', $classIdInt)->get()->getRowArray();
            $maxOrder = (int) ($maxRow['sort_order'] ?? 0);
            $finalSortOrder = $sortOrder > 0 ? $sortOrder : ($maxOrder + 1);

            $exists = (int) $db->table('learning_modules')->where('class_id', $classIdInt)->where('sort_order', $finalSortOrder)->countAllResults();
            if ($exists > 0) {
                $finalSortOrder = $maxOrder + 1;
            }

            try {
                $moduleModel = new LearningModuleModel();
                $moduleModel->insert([
                    'class_id'    => $classIdInt,
                    'title'       => $title,
                    'description' => $description,
                    'sort_order'  => $finalSortOrder,
                    'is_required' => $this->request->getPost('is_required') ? 1 : 0,
                    'status'      => $status,
                ]);
            } catch (Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Modul gagal dibuat: ' . $e->getMessage());
            }

            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Modul berhasil dibuat.');
        }

        return $this->render('Tambah Modul', 'trainer/modul_form', [
            'classId'   => (int) $classId,
            'mode'      => 'create',
            'module'    => null,
            'className' => $this->className((int) $classId),
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

        if ($this->request->getMethod() === 'POST') {
            $moduleModel->update((int) $moduleId, [
                'title'       => trim((string) $this->request->getPost('title')),
                'description' => (string) $this->request->getPost('description'),
                'sort_order'  => (int) $this->request->getPost('sort_order'),
                'status'      => (string) ($this->request->getPost('status') ?: $module['status']),
                'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            ]);
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Modul berhasil diperbarui.');
        }

        return $this->render('Ubah Modul', 'trainer/modul_form', [
            'classId'   => (int) $classId,
            'mode'      => 'edit',
            'module'    => $module,
            'className' => $this->className((int) $classId),
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
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Modul dihapus.');
    }

    public function toggleModuleStatus(string $classId, string $moduleId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $module = (new LearningModuleModel())->where('id', (int) $moduleId)->where('class_id', (int) $classId)->first();
        if ($module) {
            $newStatus = ($module['status'] === 'published') ? 'draft' : 'published';
            (new LearningModuleModel())->update((int) $moduleId, ['status' => $newStatus]);
            return redirect()->back()->with('success', 'Status modul diperbarui menjadi ' . ucfirst($newStatus));
        }
        return redirect()->back()->with('error', 'Modul tidak ditemukan.');
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

        if ($this->request->getMethod() === 'POST') {
            $payload = $this->lessonPayload((int) $moduleId, (int) $userId);
            if (($msg = $payload['error'] ?? null) !== null) {
                return redirect()->back()->withInput()->with('error', $msg);
            }
            try {
                $lessonModel = new LessonModel();
                $lessonModel->insert($payload['data']);
            } catch (Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Lesson gagal dibuat: ' . $e->getMessage());
            }
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Materi berhasil ditambahkan.');
        }

        return $this->render('Tambah Materi', 'trainer/lesson_form', [
            'classId'   => (int) $classId,
            'module'    => $module,
            'mode'      => 'create',
            'lesson'    => null,
            'className' => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Materi'));
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
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan.');
        }
        $module = (new LearningModuleModel())->find((int) $lesson['module_id']);

        $fileAsset = null;
        if (! empty($lesson['file_id'])) {
            $fileAsset = db_connect()->table('file_assets')->where('id', (int) $lesson['file_id'])->get()->getRowArray();
        }

        if ($this->request->getMethod() === 'POST') {
            $payload = $this->lessonPayload((int) $lesson['module_id'], (int) $userId, $lesson);
            if (($msg = $payload['error'] ?? null) !== null) {
                return redirect()->back()->withInput()->with('error', $msg);
            }
            try {
                $lessonModel->update((int) $lessonId, $payload['data']);
            } catch (Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Lesson gagal diperbarui: ' . $e->getMessage());
            }
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Materi diperbarui.');
        }

        return $this->render('Ubah Materi', 'trainer/lesson_form', [
            'classId'   => (int) $classId,
            'module'    => $module,
            'mode'      => 'edit',
            'lesson'    => $lesson,
            'fileAsset' => $fileAsset,
            'className' => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Ubah Materi'));
    }

    public function deleteLesson(string $classId, string $lessonId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        try {
            (new LessonModel())->where('id', (int) $lessonId)->delete();
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Materi gagal dihapus: ' . $e->getMessage());
        }
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=materi')->with('success', 'Materi dihapus.');
    }

    public function toggleLessonStatus(string $classId, string $lessonId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $lesson = (new LessonModel())->find((int) $lessonId);
        if ($lesson) {
            $newStatus = ($lesson['status'] === 'published') ? 'draft' : 'published';
            (new LessonModel())->update((int) $lessonId, ['status' => $newStatus]);
            return redirect()->back()->with('success', 'Status materi diperbarui menjadi ' . ucfirst($newStatus));
        }
        return redirect()->back()->with('error', 'Materi tidak ditemukan.');
    }

    // =========================================================================
    // 5. QUIZ MANAGEMENT
    // =========================================================================

    public function quizzes(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=assessment');
    }

    public function createQuiz(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $lessons = $this->loadLessonsForClass((int) $classId);

        if ($this->request->getMethod() === 'POST') {
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
                    return redirect()->back()->withInput()->with('error', 'Quiz modul wajib memilih lesson/materi.');
                }
            }

            $durationMinutesPost = $this->request->getPost('duration_minutes');
            $durationMinutes     = ($durationMinutesPost !== null && $durationMinutesPost !== '') ? max(0, (int) $durationMinutesPost) : null;

            $quizId = (new QuizModel())->insert([
                'class_id'         => (int) $classId,
                'lesson_id'        => $lessonId,
                'type'             => $type,
                'title'            => $title,
                'instructions'     => (string) $this->request->getPost('instructions'),
                'duration_minutes' => $durationMinutes,
                'attempt_limit'    => max(1, (int) $this->request->getPost('attempt_limit')),
                'is_required'      => $this->request->getPost('is_required') ? 1 : 0,
                'status'           => (string) ($this->request->getPost('status') ?: 'draft'),
            ], true);

            return redirect()->to(route_to('workspace.trainer.quiz.edit', $classId, (string) $quizId))->with('success', 'Ujian dibuat. Silakan tambahkan soal.');
        }

        return $this->render('Tambah Ujian', 'trainer/quiz_form', [
            'classId'   => (int) $classId,
            'className' => $this->className((int) $classId),
            'mode'      => 'create',
            'quiz'      => null,
            'lessons'   => $lessons,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Ujian'));
    }

    public function editQuiz(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $quiz = (new QuizModel())->where('id', (int) $quizId)->where('class_id', (int) $classId)->first();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'POST' && $this->request->getPost('_action') === 'update_meta') {
            $durationMinutesPost = $this->request->getPost('duration_minutes');
            $durationMinutes     = ($durationMinutesPost !== null && $durationMinutesPost !== '') ? max(0, (int) $durationMinutesPost) : null;

            (new QuizModel())->update((int) $quizId, [
                'title'            => trim((string) $this->request->getPost('title')),
                'instructions'     => (string) $this->request->getPost('instructions'),
                'duration_minutes' => $durationMinutes,
                'attempt_limit'    => max(1, (int) $this->request->getPost('attempt_limit')),
                'is_required'      => $this->request->getPost('is_required') ? 1 : 0,
                'status'           => (string) ($this->request->getPost('status') ?: $quiz['status']),
            ]);
            return redirect()->back()->with('success', 'Ujian berhasil diperbarui.');
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

        return $this->render('Kelola Soal: ' . $quiz['title'], 'trainer/quiz_edit', [
            'classId'           => (int) $classId,
            'className'         => $this->className((int) $classId),
            'mode'              => 'edit',
            'quiz'              => $quiz,
            'questions'         => $questions,
            'optionsByQuestion' => $optionsByQuestion,
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Soal Ujian'));
    }

    public function addQuestion(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $quiz = (new QuizModel())->where('id', (int) $quizId)->where('class_id', (int) $classId)->first();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'POST') {
            $prompt = trim((string) $this->request->getPost('prompt'));
            if ($prompt === '') {
                return redirect()->back()->withInput()->with('error', 'Pertanyaan wajib diisi.');
            }
            $options = (array) $this->request->getPost('options');
            $correct = (int) $this->request->getPost('correct_option');
            $options = array_values(array_filter(array_map('trim', $options), static fn ($o): bool => $o !== ''));
            if (count($options) < 2) {
                return redirect()->back()->withInput()->with('error', 'Minimal sediakan dua pilihan jawaban.');
            }
            if ($correct < 0 || $correct >= count($options)) {
                return redirect()->back()->withInput()->with('error', 'Tandai satu pilihan sebagai jawaban benar.');
            }

            $maxOrder = (int) (db_connect()->table('quiz_questions')->selectMax('sort_order')->where('quiz_id', (int) $quizId)->get()->getRowArray()['sort_order'] ?? 0);
            $questionId = (new QuestionModel())->insert([
                'quiz_id'    => (int) $quizId,
                'type'       => 'multiple_choice',
                'prompt'     => $prompt,
                'points'     => max(0.01, (float) ($this->request->getPost('points') ?: 1.0)),
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

            return redirect()->to(route_to('workspace.trainer.quiz.edit', $classId, $quizId))->with('success', 'Soal berhasil ditambahkan.');
        }

        return $this->render('Tambah Soal', 'trainer/question_form', [
            'classId'   => (int) $classId,
            'quiz'      => $quiz,
            'className' => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Tambah Soal'));
    }

    public function downloadQuestionTemplate(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        $service = new QuizQuestionImportService();
        $csvContent = $service->generateTemplateCsv();

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_soal_pilihan_ganda_ujian.csv"')
            ->setBody($csvContent);
    }

    public function importQuestions(string $classId, string $quizId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $quizIdInt = (int) $quizId;
        $this->assertTrainerOf($userId, $classIdInt);

        $quiz = (new QuizModel())->where('id', $quizIdInt)->where('class_id', $classIdInt)->first();
        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak ditemukan.');
        }

        $file = $this->request->getFile('question_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Harap pilih berkas soal (.csv atau .xlsx) untuk diimpor.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, ['csv', 'xlsx', 'txt'], true)) {
            return redirect()->back()->with('error', 'Format berkas tidak valid. Harap gunakan berkas CSV (.csv) atau Excel (.xlsx).');
        }

        $service = new QuizQuestionImportService();
        try {
            $tempPath = $file->getTempName();
            $questions = $service->parseFile($tempPath, $ext);
            $count = $service->importQuestionsToQuiz($quizIdInt, $questions);
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses berkas impor: ' . $e->getMessage());
        }

        return redirect()->to(route_to('workspace.trainer.quiz.edit', $classId, $quizId))
            ->with('success', "Berhasil mengimpor {$count} butir soal pilihan ganda. Seluruh soal lama telah ditimpa sesuai berkas impor.");
    }

    public function deleteQuestion(string $classId, string $quizId, string $questionId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        try {
            db_connect()->table('quiz_options')->where('question_id', (int) $questionId)->delete();
            (new QuestionModel())->where('id', (int) $questionId)->where('quiz_id', (int) $quizId)->delete();
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Soal gagal dihapus: ' . $e->getMessage());
        }

        return redirect()->to(route_to('workspace.trainer.quiz.edit', $classId, $quizId))->with('success', 'Soal dihapus.');
    }

    /**
     * 5.b. REKAP HASIL QUIZ / UJIAN (Pre-Test, Quiz Modul, Post-Test)
     */
    public function quizAttempts(string $classId, string $quizId)
    {
        $userId     = $this->userId();
        $classIdInt = (int) $classId;
        $quizIdInt  = (int) $quizId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();

        $quiz = $db->table('quizzes')
            ->where('id', $quizIdInt)
            ->where('class_id', $classIdInt)
            ->get()->getRowArray();

        if (! $quiz) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian atau kuis tidak ditemukan.');
        }

        $class = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('lc.id', $classIdInt)
            ->get()->getRowArray();

        $attempts = $db->table('quiz_attempts qa')
            ->select('qa.*, e.user_id, e.class_id, u.name AS participant_name, u.email AS participant_email, u.username')
            ->join('enrollments e', 'e.id = qa.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('qa.quiz_id', $quizIdInt)
            ->orderBy('qa.submitted_at', 'DESC')
            ->orderBy('qa.id', 'DESC')
            ->get()->getResultArray();

        $passingGrade       = (float) ($class['passing_grade'] ?? 70.0);
        $totalAttempts      = count($attempts);
        $uniqueUsers        = count(array_unique(array_column($attempts, 'user_id')));
        $totalScore         = 0.0;
        $maxScore           = 0.0;
        $passedCount        = 0;
        $completedAttempts  = 0;

        foreach ($attempts as $att) {
            $sc = (float) ($att['score'] ?? 0.0);
            if ($att['status'] === 'graded' || $att['status'] === 'submitted') {
                $totalScore += $sc;
                $completedAttempts++;
                if ($sc > $maxScore) {
                    $maxScore = $sc;
                }
                if ($sc >= $passingGrade) {
                    $passedCount++;
                }
            }
        }

        $avgScore = $completedAttempts > 0 ? round($totalScore / $completedAttempts, 1) : 0.0;

        return $this->render('Hasil Pengerjaan Ujian', 'trainer/quiz_attempts', [
            'classId'      => $classIdInt,
            'className'    => $this->className($classIdInt),
            'class'        => $class,
            'quiz'         => $quiz,
            'attempts'     => $attempts,
            'passingGrade' => $passingGrade,
            'stats'        => [
                'totalAttempts' => $totalAttempts,
                'uniqueUsers'   => $uniqueUsers,
                'avgScore'      => $avgScore,
                'maxScore'      => $maxScore,
                'passedCount'   => $passedCount,
            ],
        ], 'trainer', $this->trainerBreadcrumb($classIdInt, 'Hasil Ujian'));
    }

    // =========================================================================
    // 6. ASSIGNMENT MANAGEMENT
    // =========================================================================

    public function assignments(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=assignment');
    }

    public function createAssignment(string $classId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $lessons = $this->loadLessonsForClass((int) $classId);

        if ($this->request->getMethod() === 'POST') {
            $title = trim((string) $this->request->getPost('title'));
            if ($title === '') {
                return redirect()->back()->withInput()->with('error', 'Judul tugas wajib diisi.');
            }

            $lessonId = (int) $this->request->getPost('lesson_id') ?: null;
            $dueAt    = $this->request->getPost('due_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('due_at'))) : null;
            $status   = (string) ($this->request->getPost('status') ?: 'published');

            $guideUrl = null;
            $guideFilename = null;
            $guideSize = null;

            $guideFile = $this->request->getFile('guide_file');
            if ($guideFile && $guideFile->isValid() && ! $guideFile->hasMoved()) {
                $ext = strtolower($guideFile->getClientExtension());
                $mime = $guideFile->getClientMimeType();
                if ($ext !== 'pdf' && ! str_contains($mime, 'pdf')) {
                    return redirect()->back()->withInput()->with('error', 'File petunjuk tugas wajib berformat PDF (.pdf).');
                }

                $targetDir = FCPATH . 'uploads/learning/assignments/';
                if (! is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $newFilename = 'guide_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
                $guideFile->move($targetDir, $newFilename);
                $guideUrl = 'uploads/learning/assignments/' . $newFilename;
                $guideFilename = $guideFile->getClientName();
                $guideSize = $guideFile->getSize();

                db_connect()->table('file_assets')->insert([
                    'user_id'        => $userId,
                    'collection'     => 'assignment_guides',
                    'filename'       => $newFilename,
                    'original_name'  => $guideFilename,
                    'mime_type'      => 'application/pdf',
                    'size_bytes'     => $guideSize,
                    'path'           => $guideUrl,
                    'visibility'     => 'public',
                    'storage_driver' => 'local',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }

            $cleanInstructions = (string) $this->request->getPost('instructions');
            $finalInstructions = build_assignment_instructions($cleanInstructions, $guideUrl, $guideFilename, $guideSize);

            try {
                (new AssignmentModel())->insert([
                    'class_id'     => (int) $classId,
                    'lesson_id'    => $lessonId,
                    'title'        => $title,
                    'instructions' => $finalInstructions,
                    'due_at'       => $dueAt,
                    'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
                    'max_attempts' => max(1, (int) ($this->request->getPost('max_attempts') ?: 1)),
                    'status'       => $status,
                    'created_by'   => $userId,
                    'updated_by'   => $userId,
                ]);
            } catch (Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Tugas gagal dibuat: ' . $e->getMessage());
            }

            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=assignment')->with('success', 'Tugas berhasil dibuat.');
        }

        return $this->render('Buat Tugas Baru', 'trainer/assignment_form', [
            'classId'       => (int) $classId,
            'mode'          => 'create',
            'assignment'    => null,
            'existingGuide' => null,
            'lessons'       => $lessons,
            'className'     => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Buat Tugas'));
    }

    public function editAssignment(string $classId, string $assignmentId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $assignment = (new AssignmentModel())->where('id', (int) $assignmentId)->where('class_id', (int) $classId)->first();
        if (! $assignment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tugas tidak ditemukan.');
        }
        $lessons = $this->loadLessonsForClass((int) $classId);
        $existingGuide = parse_assignment_guide($assignment['instructions'] ?? '');

        if ($this->request->getMethod() === 'POST') {
            $title = trim((string) $this->request->getPost('title'));
            if ($title === '') {
                return redirect()->back()->withInput()->with('error', 'Judul tugas wajib diisi.');
            }

            $lessonId = (int) $this->request->getPost('lesson_id') ?: null;
            $dueAt    = $this->request->getPost('due_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('due_at'))) : null;
            $status   = (string) ($this->request->getPost('status') ?: $assignment['status']);

            $guideUrl = $existingGuide['guide_url'];
            $guideFilename = $existingGuide['guide_filename'];
            $guideSize = $existingGuide['guide_size'];

            if ($this->request->getPost('remove_guide_file')) {
                $guideUrl = null;
                $guideFilename = null;
                $guideSize = null;
            }

            $guideFile = $this->request->getFile('guide_file');
            if ($guideFile && $guideFile->isValid() && ! $guideFile->hasMoved()) {
                $ext = strtolower($guideFile->getClientExtension());
                $mime = $guideFile->getClientMimeType();
                if ($ext !== 'pdf' && ! str_contains($mime, 'pdf')) {
                    return redirect()->back()->withInput()->with('error', 'File petunjuk tugas wajib berformat PDF (.pdf).');
                }

                $targetDir = FCPATH . 'uploads/learning/assignments/';
                if (! is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $newFilename = 'guide_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
                $guideFile->move($targetDir, $newFilename);
                $guideUrl = 'uploads/learning/assignments/' . $newFilename;
                $guideFilename = $guideFile->getClientName();
                $guideSize = $guideFile->getSize();

                db_connect()->table('file_assets')->insert([
                    'user_id'        => $userId,
                    'collection'     => 'assignment_guides',
                    'filename'       => $newFilename,
                    'original_name'  => $guideFilename,
                    'mime_type'      => 'application/pdf',
                    'size_bytes'     => $guideSize,
                    'path'           => $guideUrl,
                    'visibility'     => 'public',
                    'storage_driver' => 'local',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }

            $cleanInstructions = (string) $this->request->getPost('instructions');
            $finalInstructions = build_assignment_instructions($cleanInstructions, $guideUrl, $guideFilename, $guideSize);

            (new AssignmentModel())->update((int) $assignmentId, [
                'lesson_id'    => $lessonId,
                'title'        => $title,
                'instructions' => $finalInstructions,
                'due_at'       => $dueAt,
                'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
                'max_attempts' => max(1, (int) ($this->request->getPost('max_attempts') ?: 1)),
                'status'       => $status,
                'updated_by'   => $userId,
            ]);

            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=assignment')->with('success', 'Tugas berhasil diperbarui.');
        }

        return $this->render('Ubah Tugas', 'trainer/assignment_form', [
            'classId'       => (int) $classId,
            'mode'          => 'edit',
            'assignment'    => $assignment,
            'existingGuide' => $existingGuide,
            'lessons'       => $lessons,
            'className'     => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Ubah Tugas'));
    }

    public function deleteAssignment(string $classId, string $assignmentId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);

        try {
            (new AssignmentModel())->where('id', (int) $assignmentId)->where('class_id', (int) $classId)->delete();
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Tugas gagal dihapus: ' . $e->getMessage());
        }

        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=assignment')->with('success', 'Tugas berhasil dihapus.');
    }

    public function allSubmissions()
    {
        $userId = $this->userId();
        $db = db_connect();

        $assignedClasses = $this->getTrainerClasses($userId);
        if ($assignedClasses === []) {
            return $this->render('Review Pengumpulan Tugas', 'trainer/assignment_submissions', [
                'classId'     => 0,
                'assignment'  => ['title' => 'Semua Pengumpulan Tugas'],
                'submissions' => [],
                'className'   => 'Semua Kelas',
            ], 'trainer');
        }

        $classIds = array_column($assignedClasses, 'id');
        $selectedClassId = (int) ($this->request->getGet('class_id') ?: 0);
        $statusFilter    = (string) $this->request->getGet('status');

        $query = $db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, lc.name AS class_name, u.name AS user_name, u.email AS user_email')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->whereIn('a.class_id', $classIds);

        if ($selectedClassId > 0 && in_array($selectedClassId, $classIds, true)) {
            $query->where('a.class_id', $selectedClassId);
        }
        if ($statusFilter !== '') {
            $query->where('s.status', $statusFilter);
        }

        $submissions = $query->orderBy('s.submitted_at', 'DESC')->get()->getResultArray();

        return $this->render('Review Pengumpulan Tugas', 'trainer/assignment_submissions', [
            'classId'     => $selectedClassId ?: (int) $assignedClasses[0]['id'],
            'assignment'  => ['title' => 'Daftar Semua Submission Tugas'],
            'submissions' => $submissions,
            'className'   => $selectedClassId > 0 ? $this->className($selectedClassId) : 'Seluruh Kelas Binaan',
        ], 'trainer');
    }

    public function assignmentSubmissions(string $classId, string $assignmentId)
    {
        $userId = $this->userId();
        $this->assertTrainerOf($userId, (int) $classId);
        $db = db_connect();

        $assignment = (new AssignmentModel())->where('id', (int) $assignmentId)->where('class_id', (int) $classId)->first();
        if (! $assignment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tugas tidak ditemukan.');
        }

        $submissions = $db->table('assignment_submissions s')
            ->select('s.*, u.name AS user_name, u.email AS user_email')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('s.assignment_id', (int) $assignmentId)
            ->orderBy('s.submitted_at', 'DESC')
            ->get()->getResultArray();

        return $this->render('Submission: ' . $assignment['title'], 'trainer/assignment_submissions', [
            'classId'     => (int) $classId,
            'assignment'  => $assignment,
            'submissions' => $submissions,
            'className'   => $this->className((int) $classId),
        ], 'trainer', $this->trainerBreadcrumb((int) $classId, 'Submission'));
    }

    public function reviewSubmission(string $submissionId)
    {
        $userId = $this->userId();
        $db = db_connect();

        $submission = $db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.instructions AS assignment_instructions, a.class_id, u.name AS user_name, u.email AS user_email')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();
        if (! $submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Submission tidak ditemukan.');
        }

        $this->assertTrainerOf($userId, (int) $submission['class_id']);

        $files = $db->table('assignment_submission_files sf')
            ->select('f.*')
            ->join('file_assets f', 'f.id = sf.file_id')
            ->where('sf.submission_id', (int) $submissionId)
            ->get()->getResultArray();

        return $this->render('Review Tugas: ' . $submission['user_name'], 'trainer/submission_review', [
            'submission' => $submission,
            'files'      => $files,
            'classId'    => (int) $submission['class_id'],
            'className'  => $this->className((int) $submission['class_id']),
        ], 'trainer', $this->trainerBreadcrumb((int) $submission['class_id'], 'Review Tugas'));
    }

    public function submitReviewSubmission(string $submissionId)
    {
        $userId = $this->userId();
        $db = db_connect();

        $submission = $db->table('assignment_submissions s')
            ->select('s.*, a.class_id')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();
        if (! $submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Submission tidak ditemukan.');
        }

        $this->assertTrainerOf($userId, (int) $submission['class_id']);

        $score    = (float) $this->request->getPost('score');
        $feedback = trim((string) $this->request->getPost('feedback'));
        $status   = (string) $this->request->getPost('status');

        if (! in_array($status, ['graded', 'revision_required', 'in_review'], true)) {
            $status = 'graded';
        }
        $score = max(0.0, min(100.0, $score));

        (new AssignmentSubmissionModel())->update((int) $submissionId, [
            'score'        => $score,
            'feedback'     => $feedback,
            'status'       => $status,
            'graded_by'    => $userId,
            'graded_at'    => date('Y-m-d H:i:s'),
        ]);

        if ($status === 'graded') {
            try {
                $this->learning->evaluateCompletion((int) $submission['enrollment_id']);
            } catch (Throwable $e) {
                log_message('error', 'evaluateCompletion failed: {m}', ['m' => $e->getMessage()]);
            }
        }

        return redirect()->to(route_to('workspace.trainer.assignment.submissions', (string) $submission['class_id'], (string) $submission['assignment_id']))
            ->with('success', 'Penilaian dan feedback tugas berhasil disimpan.');
    }

    // =========================================================================
    // 7 & 8. PESERTA & PROGRESS
    // =========================================================================

    public function participants(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta');
    }

    public function approveParticipant(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name')
            ->join('users u', 'u.id = e.user_id')
            ->where(['e.id' => (int) $enrollmentId, 'e.class_id' => $classIdInt])
            ->get()->getRowArray();

        if (! $enrollment) {
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
                ->with('error', 'Peserta tidak ditemukan pada kelas ini.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('enrollments')->where('id', (int) $enrollmentId)->update([
            'status'       => 'active',
            'activated_at' => $now,
            'cancelled_at' => null,
            'withdrawn_at' => null,
            'updated_at'   => $now,
        ]);

        $name = $enrollment['user_name'] ?? 'Peserta';
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
            ->with('success', "Pendaftaran {$name} berhasil disetujui dan statusnya diaktifkan.");
    }

    public function rejectParticipant(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name')
            ->join('users u', 'u.id = e.user_id')
            ->where(['e.id' => (int) $enrollmentId, 'e.class_id' => $classIdInt])
            ->get()->getRowArray();

        if (! $enrollment) {
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
                ->with('error', 'Peserta tidak ditemukan pada kelas ini.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('enrollments')->where('id', (int) $enrollmentId)->update([
            'status'       => 'cancelled',
            'cancelled_at' => $now,
            'activated_at' => null,
            'updated_at'   => $now,
        ]);

        $name = $enrollment['user_name'] ?? 'Peserta';
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
            ->with('success', "Pendaftaran {$name} telah ditolak.");
    }

    public function kickParticipant(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name')
            ->join('users u', 'u.id = e.user_id')
            ->where(['e.id' => (int) $enrollmentId, 'e.class_id' => $classIdInt])
            ->get()->getRowArray();

        if (! $enrollment) {
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
                ->with('error', 'Peserta tidak ditemukan pada kelas ini.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('enrollments')->where('id', (int) $enrollmentId)->update([
            'status'       => 'withdrawn',
            'withdrawn_at' => $now,
            'updated_at'   => $now,
        ]);

        $name = $enrollment['user_name'] ?? 'Peserta';
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
            ->with('success', "Peserta {$name} berhasil dikeluarkan dari kelas.");
    }

    public function reactivateParticipant(string $classId, string $enrollmentId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name')
            ->join('users u', 'u.id = e.user_id')
            ->where(['e.id' => (int) $enrollmentId, 'e.class_id' => $classIdInt])
            ->get()->getRowArray();

        if (! $enrollment) {
            return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
                ->with('error', 'Peserta tidak ditemukan pada kelas ini.');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('enrollments')->where('id', (int) $enrollmentId)->update([
            'status'       => 'active',
            'activated_at' => $now,
            'withdrawn_at' => null,
            'cancelled_at' => null,
            'updated_at'   => $now,
        ]);

        $name = $enrollment['user_name'] ?? 'Peserta';
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=peserta')
            ->with('success', "Peserta {$name} berhasil diaktifkan kembali ke dalam kelas.");
    }

    public function editClass(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $class = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, courses.slug AS course_slug, courses.level AS course_level, courses.duration_minutes, programs.title AS program_title, fa.path AS cover_path')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('file_assets fa', 'fa.id = lc.cover_file_id', 'left')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('lc.id', $classIdInt)->get()->getRowArray();

        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $parsedDesc = parse_class_batch_data($class['description'] ?? '');

        return $this->render('Ubah Detail Kelas', 'trainer/class_edit', [
            'class'      => $class,
            'parsedDesc' => $parsedDesc,
            'className'  => $class['name'] ?? ('Kelas #' . $classIdInt),
        ], 'trainer', $this->trainerBreadcrumb($classIdInt, 'Ubah Detail Kelas'));
    }

    public function updateClass(string $classId)
    {
        $userId = $this->userId();
        $classIdInt = (int) $classId;
        $this->assertTrainerOf($userId, $classIdInt);

        $db = db_connect();
        $class = $db->table('learning_classes')->where('id', $classIdInt)->get()->getRowArray();
        if (! $class) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Nama kelas wajib diisi.');
        }

        $code = trim((string) ($this->request->getPost('code') ?: $class['code']));
        $learningMethod = (string) ($this->request->getPost('learning_method') ?: 'online');
        $locationOrLink = trim((string) $this->request->getPost('location_or_link'));
        $targetAudience = trim((string) $this->request->getPost('target_audience'));

        $rawStatus = (string) ($this->request->getPost('status') ?: ($class['status'] ?? 'active'));
        $status = match (strtolower(trim($rawStatus))) {
            'open', 'scheduled', 'published', 'terbuka' => 'scheduled',
            'active', 'ongoing', 'berlangsung', 'aktif'  => 'active',
            'completed', 'closed', 'selesai'             => 'completed',
            'draft', 'archived', 'draf'                  => 'draft',
            default                                      => in_array($rawStatus, ['scheduled', 'active', 'completed', 'draft'], true) ? $rawStatus : ($class['status'] ?: 'active'),
        };

        $startAt = $this->request->getPost('start_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('start_at'))) : null;
        $endAt = $this->request->getPost('end_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('end_at'))) : null;
        $regOpenAt = $this->request->getPost('registration_open_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('registration_open_at'))) : null;
        $regCloseAt = $this->request->getPost('registration_close_at') ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('registration_close_at'))) : null;

        $capacity = max(0, (int) $this->request->getPost('capacity'));
        $passingGrade = max(0.0, min(100.0, (float) ($this->request->getPost('passing_grade') ?: 70.0)));
        $certificateEnabled = $this->request->getPost('certificate_enabled') ? 1 : 0;
        $quizWeight = (float) ($this->request->getPost('quiz_weight') ?? 20.0);
        $assignmentWeight = (float) ($this->request->getPost('assignment_weight') ?? 40.0);
        $postTestWeight = (float) ($this->request->getPost('post_test_weight') ?? 40.0);

        // Dynamic Batch Stages
        $rawStages = (array) $this->request->getPost('stages');
        $stagesSubtitle = (string) $this->request->getPost('stages_subtitle');
        $customDurationMinutes = $this->request->getPost('custom_duration_minutes') ? (int) $this->request->getPost('custom_duration_minutes') : null;
        $cleanDesc = (string) $this->request->getPost('description');
        $finalDescription = build_class_batch_description($cleanDesc, $rawStages, $stagesSubtitle, $customDurationMinutes);

        $updateData = [
            'name'                  => $name,
            'code'                  => $code,
            'learning_method'       => $learningMethod,
            'location_or_link'      => $locationOrLink,
            'target_audience'       => $targetAudience,
            'description'           => $finalDescription,
            'status'                => $status,
            'start_at'              => $startAt,
            'end_at'                => $endAt,
            'registration_open_at'  => $regOpenAt,
            'registration_close_at' => $regCloseAt,
            'capacity'              => $capacity,
            'passing_grade'         => $passingGrade,
            'certificate_enabled'   => $certificateEnabled,
            'quiz_weight'           => $quizWeight,
            'assignment_weight'     => $assignmentWeight,
            'post_test_weight'      => $postTestWeight,
            'updated_by'            => $userId,
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        // Cover image upload
        $coverFile = $this->request->getFile('cover_image');
        if ($coverFile && $coverFile->isValid() && ! $coverFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/learning/covers/';
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $newFilename = 'class_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $coverFile->getClientExtension();
            $coverFile->move($targetDir, $newFilename);
            $coverUrl = 'uploads/learning/covers/' . $newFilename;

            $db->table('file_assets')->insert([
                'user_id'         => $userId,
                'collection'      => 'class_covers',
                'filename'        => $newFilename,
                'original_name'   => $coverFile->getClientName(),
                'mime_type'       => $coverFile->getClientMimeType(),
                'size_bytes'      => $coverFile->getSize(),
                'path'            => $coverUrl,
                'visibility'      => 'public',
                'storage_driver'  => 'local',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $assetId = (int) $db->insertID();
            $updateData['cover_file_id'] = $assetId > 0 ? $assetId : null;
            $updateData['cover_url'] = $coverUrl;
        }

        $db->table('learning_classes')->where('id', $classIdInt)->update($updateData);

        return redirect()->to(route_to('workspace.trainer.class.detail', $classId))
            ->with('success', 'Informasi detail kelas berhasil diperbarui.');
    }

    public function progress(string $classId)
    {
        return redirect()->to(route_to('workspace.trainer.class.detail', $classId) . '?tab=progres');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function getTrainerClasses(int $userId): array
    {
        $classes = db_connect()->table('class_trainers ct')
            ->select('lc.id, lc.name, lc.code, lc.status, lc.learning_method, lc.location_or_link, courses.title AS course_title, courses.cover_url AS course_cover_url, fa.path AS cover_path')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses', 'courses.id = lc.course_id')
            ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
            ->where('ct.user_id', $userId)
            ->where('ct.status', 'active')
            ->orderBy('lc.name', 'ASC')
            ->get()->getResultArray();

        if ($classes === [] && $this->isPrivilegedOperator()) {
            $classes = db_connect()->table('learning_classes lc')
                ->select('lc.id, lc.name, lc.code, lc.status, lc.learning_method, lc.location_or_link, courses.title AS course_title, courses.cover_url AS course_cover_url, fa.path AS cover_path')
                ->join('courses', 'courses.id = lc.course_id')
                ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
                ->orderBy('lc.name', 'ASC')
                ->get()->getResultArray();
        }

        return $classes;
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

    private function lessonPayload(int $moduleId, int $actorId, ?array $existingLesson = null): array
    {
        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return ['error' => 'Judul materi wajib diisi.'];
        }
        $contentType = (string) $this->request->getPost('content_type');
        if (! in_array($contentType, ['text', 'document', 'video', 'link'], true)) {
            return ['error' => 'Tipe konten materi tidak valid.'];
        }

        $content     = (string) $this->request->getPost('content');
        $externalUrl = null;
        $fileId      = $existingLesson ? ((int) ($existingLesson['file_id'] ?? 0) ?: null) : null;
        $rawAccess   = (string) ($this->request->getPost('document_access') ?: ($existingLesson['visibility'] ?? 'internal'));
        if ($rawAccess === 'preview_only' || $rawAccess === 'restricted') {
            $visibility = 'restricted';
        } elseif ($rawAccess === 'public') {
            $visibility = 'public';
        } else {
            $visibility = 'internal';
        }

        if ($contentType === 'document') {
            // Cek apakah ada permintaan hapus file fisik yang tersambung
            if ($this->request->getPost('remove_pdf') && $fileId) {
                $fileId = null;
            }

            // Cek apakah ada berkas PDF baru yang diunggah
            $pdfFile = $this->request->getFile('pdf_file');
            if ($pdfFile && $pdfFile->isValid() && ! $pdfFile->hasMoved()) {
                $ext  = strtolower($pdfFile->getClientExtension());
                $mime = $pdfFile->getMimeType();
                if ($ext !== 'pdf' && $mime !== 'application/pdf') {
                    return ['error' => 'Berkas materi harus berupa dokumen berformat PDF (.pdf).'];
                }

                $uploadDir = WRITEPATH . 'uploads/lessons/';
                if (! is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0775, true);
                    @chmod($uploadDir, 0775);
                }

                $storedName = $pdfFile->getRandomName();
                try {
                    $pdfFile->move($uploadDir, $storedName);
                    @chmod($uploadDir . $storedName, 0664);
                } catch (\Throwable $e) {
                    log_message('error', '[TrainerController] PDF move failed: ' . $e->getMessage());
                    return ['error' => 'Gagal memindahkan berkas PDF: ' . $e->getMessage()];
                }

                $now = date('Y-m-d H:i:s');
                $fileRow = [
                    'disk'             => 'local',
                    'path'             => 'uploads/lessons/' . $storedName,
                    'original_name'    => $pdfFile->getClientName() ?: $storedName,
                    'stored_name'      => $storedName,
                    'extension'        => 'pdf',
                    'mime_type'        => 'application/pdf',
                    'size_bytes'       => (int) ($pdfFile->getSize() ?: 0),
                    'visibility'       => $visibility,
                    'lifecycle_status' => 'available',
                    'resource_type'    => 'lesson',
                    'uploaded_by'      => $actorId > 0 ? $actorId : (int) (session()->get('user_id') ?: 1),
                    'available_at'     => $now,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
                $db = db_connect();
                $db->table('file_assets')->insert($fileRow);
                $fileId = (int) $db->insertID();
            } elseif ($fileId === null && $this->request->getPost('document_label')) {
                // Fallback register placeholder jika nama dokumen diisi manual
                $fileId = $this->registerFileAsset((string) $this->request->getPost('document_label'), 'document');
            }

            // Sinkronkan visibilitas aset berkas jika ada fileId
            if ($fileId) {
                db_connect()->table('file_assets')->where('id', $fileId)->update([
                    'visibility' => $visibility,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } elseif ($contentType === 'video' || $contentType === 'link') {
            $externalUrl = trim((string) $this->request->getPost('external_url'));
            if ($externalUrl === '') {
                return ['error' => 'URL wajib diisi untuk tipe video atau link.'];
            }
        }

        if ($contentType !== 'document') {
            $fileId = null;
        } elseif ($fileId !== null) {
            // Verifikasi integritas FK: pastikan fileId benar-benar ada di tabel file_assets
            $exists = db_connect()->table('file_assets')->where('id', $fileId)->countAllResults() > 0;
            if (! $exists) {
                $fileId = null;
            }
        }

        $maxRow = db_connect()->table('lessons')
            ->selectMax('sort_order')
            ->where('module_id', $moduleId)
            ->get()->getRowArray();
        $maxOrder = (int) ($maxRow['sort_order'] ?? 0);
        $sortOrder = (int) $this->request->getPost('sort_order') ?: ($existingLesson ? (int) $existingLesson['sort_order'] : ($maxOrder + 1));

        $status = (string) ($this->request->getPost('status') ?: 'published');

        return ['data' => [
            'module_id'    => $moduleId,
            'title'        => $title,
            'slug'         => $existingLesson['slug'] ?? (url_title($title, '-', true) . '-' . substr((string) time(), -4)),
            'content_type' => $contentType,
            'content'      => $content,
            'file_id'      => $fileId,
            'external_url' => $externalUrl,
            'sort_order'   => $sortOrder,
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'status'       => $status,
            'visibility'   => $visibility,
            'created_by'   => $existingLesson['created_by'] ?? $actorId,
            'updated_by'   => $actorId,
        ]];
    }

    private function registerFileAsset(string $label, string $kind): ?int
    {
        $label = trim($label);
        if ($label === '') {
            return null;
        }
        $now  = date('Y-m-d H:i:s');
        $path = 'lessons/' . url_title($label, '-', true) . '-' . substr((string) time(), -4);
        $row  = [
            'disk'            => 'local',
            'path'            => $path,
            'original_name'   => $label,
            'stored_name'     => basename($path),
            'extension'       => 'bin',
            'mime_type'       => 'application/octet-stream',
            'size_bytes'      => 0,
            'visibility'      => 'internal',
            'lifecycle_status'=> 'pending_upload',
            'resource_type'   => 'lesson',
            'created_at'      => $now,
            'updated_at'      => $now,
        ];
        $db = db_connect();
        $db->table('file_assets')->insert($row);
        $id = (int) $db->insertID();
        return $id > 0 ? $id : null;
    }

    private function isPrivilegedOperator(): bool
    {
        $roles = (array) session()->get('roles');
        return array_intersect(['operator', 'admin', 'super_admin', 'administrator'], $roles) !== [];
    }

    private function assertTrainerOf(int $userId, int $classId): void
    {
        if ($this->isPrivilegedOperator()) {
            return; // Operator/Admin memiliki hak setara/override untuk mengelola seluruh kelas
        }
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
        $roles   = (array) session()->get('roles');
        $allowed = ['trainer', 'operator', 'admin', 'super_admin', 'administrator'];
        if (array_intersect($allowed, $roles) === []) {
            redirect()->to('/dashboard/masuk')->with('error', 'Anda tidak memiliki akses Trainer/Operator.')->send();
            exit;
        }
        return $id;
    }

    private function render(string $pageTitle, string $viewPage, array $data, string $activeMenu = 'trainer', array $breadcrumbTail = []): string
    {
        $roleLabel = $this->isPrivilegedOperator() ? 'Operator / Pengajar' : 'Pengajar';
        $data = array_merge($data, [
            'pageTitle'      => $pageTitle,
            'breadcrumbTail' => $breadcrumbTail,
            'activeMenu'     => $activeMenu,
            'activeSubmenu'  => '',
            'userName'       => session()->get('username') ?? 'Pengajar',
            'roleLabel'      => $roleLabel,
            'innerView'      => 'main/workspace/pages/' . $viewPage,
        ]);
        return view('main/workspace/layouts/workspace_inner', $data);
    }
}