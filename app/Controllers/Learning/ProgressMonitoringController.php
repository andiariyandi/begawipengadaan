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

        // Calculate progress & assessment metrics per participant
        foreach ($participants as &$p) {
            $clsId = (int) $p['class_id'];
            $enrId = (int) $p['id'];
            $usrId = (int) $p['user_id'];

            // Lessons count (hanya modul & materi published)
            $totalLessons = (int) $db->table('lessons l')
                ->join('learning_modules lm', 'lm.id = l.module_id')
                ->where('lm.class_id', $clsId)
                ->where('lm.status', 'published')
                ->where('l.status', 'published')
                ->countAllResults();

            $completedLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $enrId)
                ->where('status', 'completed')
                ->countAllResults();

            $p['total_lessons'] = $totalLessons;
            $p['completed_lessons'] = $completedLessons;
            $p['progress_pct'] = $totalLessons > 0
                ? (int) round(($completedLessons / $totalLessons) * 100)
                : ($p['status'] === 'completed' || $p['status'] === 'passed' ? 100 : 0);

            // Quizzes metrics (hanya published)
            $totalQuizzes = (int) $db->table('quizzes')->where('class_id', $clsId)->where('status', 'published')->countAllResults();
            $attemptedQuizzes = (int) $db->table('quiz_attempts qa')
                ->join('quizzes q', 'q.id = qa.quiz_id')
                ->where('q.class_id', $clsId)
                ->where('q.status', 'published')
                ->where('qa.enrollment_id', $enrId)
                ->where('qa.status', 'graded')
                ->select('COUNT(DISTINCT qa.quiz_id) as cnt')
                ->get()->getRow()->cnt ?? 0;
            $p['total_quizzes'] = $totalQuizzes;
            $p['attempted_quizzes'] = $attemptedQuizzes;

            // Assignments metrics (hanya published)
            $totalAssignments = (int) $db->table('assignments')->where('class_id', $clsId)->where('status', 'published')->countAllResults();
            $gradedAssignments = (int) $db->table('assignment_submissions asub')
                ->where('asub.enrollment_id', $enrId)
                ->where('asub.status', 'graded')
                ->countAllResults();
            $p['total_assignments'] = $totalAssignments;
            $p['graded_assignments'] = $gradedAssignments;

            // Certificate check
            $p['has_certificate'] = $db->table('certificates')
                ->where('source_type', 'learning_enrollment')
                ->where('source_id', $enrId)
                ->where('status', 'issued')
                ->countAllResults() > 0;
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

        foreach ($modules as &$mod) {
            $lessons = $db->table('lessons l')
                ->select('l.*, lp.status AS progress_status, lp.completed_at, lp.progress_percent')
                ->join('learning_progress lp', 'lp.lesson_id = l.id AND lp.enrollment_id = ' . (int) $enrollmentId, 'left')
                ->where('l.module_id', $mod['id'])
                ->where('l.status', 'published')
                ->orderBy('l.sort_order', 'ASC')
                ->get()->getResultArray();
            $mod['lessons'] = $lessons;
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
