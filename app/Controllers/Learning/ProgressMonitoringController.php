<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;

class ProgressMonitoringController extends BaseController
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
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));
        $classId = (int) $this->request->getGet('class_id');

        $builder = $db->table('enrollments e')
            ->select('e.*, u.name AS student_name, u.username, u.email AS student_email,
                      lc.name AS class_name, lc.code AS class_code, lc.passing_grade, c.title AS course_title')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->orderBy('e.enrolled_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('lc.name', $keyword)
                ->orLike('c.title', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('e.status', $status);
        }
        if ($classId > 0) {
            $builder->where('e.class_id', $classId);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_progress') ?: $this->request->getGet('page') ?: 1));
        $total = (clone $builder)->countAllResults();
        $participants = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('progress', $page, $perPage, $total, 0);

        // Pre-calculate class-level metrics once (eliminates 3 * N queries)
        $clsId = (int) $class['id'];
        $totalLessons = (int) $db->table('lessons l')
            ->join('learning_modules lm', 'lm.id = l.module_id')
            ->where('lm.class_id', $clsId)
            ->where('lm.status', 'published')
            ->where('l.status', 'published')
            ->countAllResults();

        $totalQuizzes = (int) $db->table('quizzes')
            ->where('class_id', $clsId)
            ->where('status', 'published')
            ->countAllResults();

        $totalAssignments = (int) $db->table('assignments')
            ->where('class_id', $clsId)
            ->where('status', 'published')
            ->countAllResults();

        // Batch pre-fetch per-enrollment metrics (eliminates 4 * N queries)
        $enrIds = array_column($participants, 'id');
        $compLessonsMap = [];
        $attemptedQuizzesMap = [];
        $gradedAsgMap = [];
        $hasCertMap = [];

        if (! empty($enrIds)) {
            $lpRows = $db->table('learning_progress')
                ->select('enrollment_id, COUNT(*) as cnt')
                ->whereIn('enrollment_id', $enrIds)
                ->where('status', 'completed')
                ->groupBy('enrollment_id')
                ->get()->getResultArray();
            foreach ($lpRows as $lpr) {
                $compLessonsMap[(int) $lpr['enrollment_id']] = (int) $lpr['cnt'];
            }

            $qaRows = $db->table('quiz_attempts qa')
                ->join('quizzes q', 'q.id = qa.quiz_id')
                ->where('q.class_id', $clsId)
                ->where('q.status', 'published')
                ->whereIn('qa.enrollment_id', $enrIds)
                ->where('qa.status', 'graded')
                ->select('qa.enrollment_id, COUNT(DISTINCT qa.quiz_id) as cnt')
                ->groupBy('qa.enrollment_id')
                ->get()->getResultArray();
            foreach ($qaRows as $qar) {
                $attemptedQuizzesMap[(int) $qar['enrollment_id']] = (int) $qar['cnt'];
            }

            $asubRows = $db->table('assignment_submissions')
                ->select('enrollment_id, COUNT(*) as cnt')
                ->whereIn('enrollment_id', $enrIds)
                ->where('status', 'graded')
                ->groupBy('enrollment_id')
                ->get()->getResultArray();
            foreach ($asubRows as $asubr) {
                $gradedAsgMap[(int) $asubr['enrollment_id']] = (int) $asubr['cnt'];
            }

            $certRows = $db->table('certificates')
                ->select('source_id')
                ->where('source_type', 'learning_enrollment')
                ->whereIn('source_id', $enrIds)
                ->where('status', 'issued')
                ->get()->getResultArray();
            foreach ($certRows as $cr) {
                $hasCertMap[(int) $cr['source_id']] = true;
            }
        }

        // Calculate progress & assessment metrics per participant
        foreach ($participants as &$p) {
            $enrId = (int) $p['id'];
            $completedLessons = $compLessonsMap[$enrId] ?? 0;

            $p['total_lessons'] = $totalLessons;
            $p['completed_lessons'] = $completedLessons;
            $p['progress_pct'] = $totalLessons > 0
                ? (int) round(($completedLessons / $totalLessons) * 100)
                : ($p['status'] === 'completed' || $p['status'] === 'passed' ? 100 : 0);

            $p['total_quizzes'] = $totalQuizzes;
            $p['attempted_quizzes'] = $attemptedQuizzesMap[$enrId] ?? 0;

            $p['total_assignments'] = $totalAssignments;
            $p['graded_assignments'] = $gradedAsgMap[$enrId] ?? 0;

            $p['has_certificate'] = isset($hasCertMap[$enrId]);
        }
        unset($p);

        $stats = [
            'total'     => (int) $db->table('enrollments')->countAllResults(),
            'passed'    => (int) $db->table('enrollments')->whereIn('status', ['passed', 'completed'])->countAllResults(),
            'ongoing'   => (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->countAllResults(),
            'eligible'  => (int) $db->table('enrollments')
                ->groupStart()
                ->whereIn('status', ['passed', 'completed'])
                ->orWhere('certificate_eligible_at IS NOT NULL', null, false)
                ->groupEnd()
                ->countAllResults(),
        ];

        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        return view('dashboard/learning/progress/monitoring', [
            'title'        => 'Monitoring Progres Pembelajaran | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Monitoring Progres Pembelajaran',
            'participants' => $participants,
            'stats'        => $stats,
            'classes'      => $classes,
            'pager'        => $pager,
            'filters'      => [
                'keyword'  => $keyword,
                'status'   => $status,
                'class_id' => $classId,
            ],
            'activeMenu'   => 'learning',
            'activeSubmenu' => 'progress',
        ]);
    }

    public function detail(string $enrollmentId): string
    {
        $db = db_connect();
        $enrollment = $db->table('enrollments e')
            ->select('e.*, u.name AS student_name, u.username, u.email AS student_email,
                      lc.name AS class_name, lc.code AS class_code, lc.passing_grade, c.title AS course_title')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('e.id', (int) $enrollmentId)
            ->get()->getRowArray();

        if (! $enrollment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data progres peserta tidak ditemukan.');
        }

        $classId = (int) $enrollment['class_id'];
        $modules = $db->table('learning_modules')
            ->where('class_id', $classId)
            ->where('status', 'published')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $moduleIds = array_column($modules, 'id');
        $lessonsByModule = [];
        if (! empty($moduleIds)) {
            $allLessons = $db->table('lessons l')
                ->select('l.*, lp.status AS progress_status, lp.completed_at, lp.progress_percent')
                ->join('learning_progress lp', 'lp.lesson_id = l.id AND lp.enrollment_id = ' . (int) $enrollmentId, 'left')
                ->whereIn('l.module_id', $moduleIds)
                ->where('l.status', 'published')
                ->orderBy('l.sort_order', 'ASC')
                ->get()->getResultArray();
            foreach ($allLessons as $les) {
                $lessonsByModule[(int) $les['module_id']][] = $les;
            }
        }

        foreach ($modules as &$mod) {
            $mod['lessons'] = $lessonsByModule[(int) $mod['id']] ?? [];
        }
        unset($mod);

        $certificate = $db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('source_id', $enrollmentId)
            ->get()->getRowArray();

        return view('dashboard/learning/progress/detail', [
            'title'         => 'Detail Progres Peserta | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Progres: ' . esc($enrollment['student_name']),
            'enrollment'    => $enrollment,
            'modules'       => $modules,
            'certificate'   => $certificate,
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'progress',
        ]);
    }
}
