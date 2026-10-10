<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class LearningOverviewController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        // 1. KPI Stats
        $totalPrograms = (int) $db->table('programs')->countAllResults();
        $totalCourses  = (int) $db->table('courses')->countAllResults();
        $activeClasses = (int) $db->table('learning_classes')
            ->whereIn('status', ['open', 'active', 'ongoing'])
            ->countAllResults();

        $activeTrainers = (int) $db->table('class_trainers')
            ->where('status', 'active')
            ->select('COUNT(DISTINCT user_id) as total')
            ->get()->getRow()->total ?? 0;

        $activeParticipants = (int) $db->table('enrollments')
            ->whereIn('status', ['active', 'enrolled', 'in_progress'])
            ->select('COUNT(DISTINCT user_id) as total')
            ->get()->getRow()->total ?? 0;

        $totalEnrollments = (int) $db->table('enrollments')->countAllResults();
        $completedEnrollments = (int) $db->table('enrollments')
            ->whereIn('status', ['completed', 'passed'])
            ->countAllResults();

        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
            : 0;

        $eligibleCertificates = (int) $db->table('enrollments')
            ->groupStart()
                ->whereIn('status', ['completed', 'passed'])
                ->orWhere('certificate_eligible_at IS NOT NULL', null, false)
            ->groupEnd()
            ->countAllResults();

        $issuedCertificates = (int) $db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('status', 'issued')
            ->countAllResults();

        $stats = [
            'total_programs'       => $totalPrograms,
            'total_courses'        => $totalCourses,
            'active_classes'       => $activeClasses,
            'active_trainers'      => $activeTrainers,
            'active_participants'  => $activeParticipants,
            'completion_rate'      => $completionRate,
            'eligible_certificates'=> $eligibleCertificates,
            'issued_certificates'  => $issuedCertificates,
        ];

        // 2. Recent Classes (5)
        $recentClasses = $db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title,
                      (SELECT COUNT(1) FROM enrollments e WHERE e.class_id = lc.id) AS enrolled_count')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->orderBy('lc.created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 3. Recent Participants (5)
        $recentParticipants = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email, u.username, lc.name AS class_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->orderBy('e.enrolled_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 4. Popular Courses (top 5)
        $popularCourses = $db->table('courses c')
            ->select('c.*, p.title AS program_title,
                      (SELECT COUNT(DISTINCT e.user_id) FROM enrollments e JOIN learning_classes lc ON lc.id = e.class_id WHERE lc.course_id = c.id) AS student_count,
                      (SELECT COUNT(1) FROM learning_classes lc WHERE lc.course_id = c.id) AS class_count')
            ->join('programs p', 'p.id = c.program_id', 'left')
            ->orderBy('student_count', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 5. Recent Learning Activities (5)
        $recentActivities = $db->table('learning_progress lp')
            ->select('lp.*, u.name AS user_name, l.title AS lesson_title, lc.name AS class_name')
            ->join('enrollments e', 'e.id = lp.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('lessons l', 'l.id = lp.lesson_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->orderBy('lp.updated_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('dashboard/learning/overview', [
            'title'              => 'Overview Arena Belajar | Dashboard BegawiPBJ',
            'pageTitle'          => 'Overview Arena Belajar',
            'stats'              => $stats,
            'recentClasses'      => $recentClasses,
            'recentParticipants' => $recentParticipants,
            'popularCourses'     => $popularCourses,
            'recentActivities'   => $recentActivities,
            'activeMenu'         => 'learning',
            'activeSubmenu'      => 'overview',
        ]);
    }
}
