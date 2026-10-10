<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class StudyController extends BaseController
{
    public function progress(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $db->table('enrollments e')
            ->select('e.*, u.name AS student_name, u.username, lc.name AS class_name, lc.code AS class_code, c.title AS course_title')
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

        $participants = $builder->get()->getResultArray();

        // Calculate progress percentage and lesson stats for each participant
        foreach ($participants as &$p) {
            $enrollmentId = (int) $p['id'];
            $totalLessons = (int) $db->table('lessons l')
                ->join('learning_modules lm', 'lm.id = l.module_id')
                ->join('learning_classes lc', 'lc.course_id = lm.course_id')
                ->where('lc.id', $p['class_id'])
                ->countAllResults();

            $completedLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $enrollmentId)
                ->where('status', 'completed')
                ->countAllResults();

            $p['total_lessons'] = $totalLessons;
            $p['completed_lessons'] = $completedLessons;
            $p['progress_pct'] = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : ($p['status'] === 'completed' || $p['status'] === 'passed' ? 100 : 0);
        }
        unset($p);

        $stats = [
            'total'     => (int) $db->table('enrollments')->countAllResults(),
            'passed'    => (int) $db->table('enrollments')->whereIn('status', ['passed', 'completed'])->countAllResults(),
            'ongoing'   => (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->countAllResults(),
            'eligible'  => (int) $db->table('enrollments')->whereNotNull('certificate_eligible_at')->countAllResults(),
        ];

        return view('dashboard/learning/progress/index', [
            'title'        => 'Progres dan Kelulusan | Dashboard BegawiPBJ',
            'pageTitle'    => 'Monitoring Progres dan Kelulusan',
            'participants' => $participants,
            'stats'        => $stats,
            'filters'      => [
                'keyword' => $keyword,
                'status'  => $status,
            ],
            'activeMenu'   => 'learning',
            'activeSubmenu'=> 'progress-completion',
        ]);
    }

    public function feedback(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('feedbacks f')
            ->select('f.*, u.name AS student_name, u.username, lc.name AS class_name, c.title AS course_title')
            ->join('enrollments e', 'e.id = f.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->orderBy('f.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('f.comment', $keyword)
                ->orLike('lc.name', $keyword)
                ->groupEnd();
        }

        $feedbacks = $builder->get()->getResultArray();

        $stats = [
            'total'     => (int) $db->table('feedbacks')->countAllResults(),
            'avgRating' => round((float) ($db->table('feedbacks')->selectAvg('rating')->get()->getRowArray()['rating'] ?? 0), 1),
        ];

        return view('dashboard/learning/feedback/index', [
            'title'        => 'Feedback Peserta | Dashboard BegawiPBJ',
            'pageTitle'    => 'Umpan Balik Peserta Pelatihan',
            'feedbacks'    => $feedbacks,
            'stats'        => $stats,
            'filters'      => ['keyword' => $keyword],
            'activeMenu'   => 'learning',
            'activeSubmenu'=> 'participant-feedback',
        ]);
    }
}
