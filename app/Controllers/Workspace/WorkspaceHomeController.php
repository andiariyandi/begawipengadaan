<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class WorkspaceHomeController extends BaseController
{
    public function index()
    {
        $role = $this->workspaceRole();

        if ($role === 'super_admin' || $role === 'admin') {
            return redirect()->to(route_to('workspace.admin.home'));
        }
        if ($role === 'trainer') {
            return redirect()->to(route_to('workspace.trainer.home'));
        }
        if ($role === 'operator') {
            return redirect()->to(route_to('workspace.operator.home'));
        }

        return $this->renderWorkspace('dashboard');
    }

    public function section(string $section)
    {
        return $this->renderWorkspace($section);
    }

    public function activity()
    {
        $userId = $this->userId();
        $role = $this->workspaceRole();
        $db = db_connect();

        $logs = $db->table('audit_logs')
            ->where('actor_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        return view('main/workspace/pages/aktivitas', [
            'title'          => 'Aktivitas Saya | Workspace BegawiPBJ',
            'pageTitle'      => 'Aktivitas Saya',
            'breadcrumbTail' => [['label' => 'Aktivitas Saya']],
            'userName'       => (string) (session()->get('name') ?: 'Pengguna BegawiPBJ'),
            'primaryRole'    => $role,
            'roleLabel'      => $this->roleLabel($role),
            'logs'           => $logs,
        ]);
    }

    private function renderWorkspace(string $section)
    {
        $section = $this->normalizeSection($section);
        $role = $this->workspaceRole();
        $userId = $this->userId();
        $db = db_connect();

        $view = $section === 'dashboard' ? 'home' : $section;
        $dashboardData = [];

        if ($section === 'dashboard') {
            $dashboardData = $this->fetchRoleDashboardData($role, $userId, $db);
        }

        return view('main/workspace/pages/' . $view, array_merge([
            'title'          => ucfirst($section) . ' | Workspace BegawiPBJ',
            'pageTitle'      => ucfirst($section) . ' Workspace',
            'userName'       => (string) (session()->get('name') ?: 'Pengguna BegawiPBJ'),
            'roles'          => [$role],
            'primaryRole'    => $role,
            'roleLabel'      => $this->roleLabel($role),
            'menuItems'      => $this->menuItems($role),
            'sectionContent' => $this->sectionContent($section),
        ], $dashboardData));
    }

    private function fetchRoleDashboardData(string $role, int $userId, $db): array
    {
        $cards = [];
        $recentClasses = [];
        $recentActivities = [];
        $recentTasks = [];

        if ($role === 'participant') {
            // Participant Real KPIs
            $enrolledCount = $db->table('enrollments')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->countAllResults();

            $completedCount = $db->table('enrollments')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->countAllResults();

            $certCount = $db->table('certificates')
                ->where('user_id', $userId)
                ->where('status', 'issued')
                ->countAllResults();

            $eventCount = $db->table('event_registrations')
                ->where('user_id', $userId)
                ->countAllResults();

            $completedLessonsCount = $db->table('learning_progress lp')
                ->join('enrollments e', 'e.id = lp.enrollment_id')
                ->where('e.user_id', $userId)
                ->where('lp.status', 'completed')
                ->countAllResults();

            $cards = [
                ['title' => 'Kelas Aktif', 'value' => sprintf('%02d', $enrolledCount), 'meta' => 'pembelajaran sedang diikuti', 'icon' => 'fas fa-graduation-cap'],
                ['title' => 'Materi Selesai', 'value' => sprintf('%02d', $completedLessonsCount), 'meta' => 'lesson telah diselesaikan', 'icon' => 'fas fa-book-reader'],
                ['title' => 'Sertifikat Terbit', 'value' => sprintf('%02d', $certCount), 'meta' => 'pencapaian kompetensi', 'icon' => 'fas fa-award'],
                ['title' => 'Event Terdaftar', 'value' => sprintf('%02d', $eventCount), 'meta' => 'agenda kegiatan & pelatihan', 'icon' => 'fas fa-calendar-alt'],
            ];

            // User Account info
            $userAccount = $db->table('users')
                ->select('id, name, username, email, phone, profile_type, account_status')
                ->where('id', $userId)
                ->get()->getRowArray() ?: [];

            // Recent Enrolled Classes
            $recentClasses = $db->table('enrollments e')
                ->select('e.id AS enrollment_id, e.class_id, e.status AS enrollment_status, e.final_score, lc.code AS class_code, lc.name AS class_name, lc.passing_grade, lc.start_at, lc.end_at, courses.title AS course_title, courses.level AS course_level, courses.duration_minutes, (SELECT COUNT(*) FROM learning_modules lm WHERE lm.class_id = lc.id) AS module_count')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->join('courses', 'courses.id = lc.course_id')
                ->where('e.user_id', $userId)
                ->orderBy('e.updated_at', 'DESC')
                ->limit(16)
                ->get()->getResultArray();

            $enrollmentIds = array_map(static fn ($r) => (int) $r['enrollment_id'], $recentClasses);
            $progressMap = [];
            if ($enrollmentIds !== []) {
                $progRows = $db->table('learning_progress')
                    ->select('enrollment_id, progress_percent')
                    ->whereIn('enrollment_id', $enrollmentIds)
                    ->get()->getResultArray();
                $counts = [];
                foreach ($progRows as $p) {
                    $eid = (int) $p['enrollment_id'];
                    $progressMap[$eid] = ($progressMap[$eid] ?? 0.0) + (float) ($p['progress_percent'] ?? 0);
                    $counts[$eid] = ($counts[$eid] ?? 0) + 1;
                }
                foreach ($progressMap as $eid => $sum) {
                    $progressMap[$eid] = $counts[$eid] > 0 ? round($sum / $counts[$eid], 1) : 0.0;
                }
            }
            foreach ($recentClasses as &$cl) {
                $eid = (int) $cl['enrollment_id'];
                $cl['progress_percent'] = $progressMap[$eid] ?? (($cl['enrollment_status'] ?? '') === 'completed' ? 100 : 0);
            }
            unset($cl);

            // Active Class to Resume
            $resumeClass = null;
            foreach ($recentClasses as $rc) {
                if (($rc['enrollment_status'] ?? '') === 'active' && ($rc['progress_percent'] ?? 0) < 100) {
                    $resumeClass = $rc;
                    break;
                }
            }
            if ($resumeClass === null && ! empty($recentClasses)) {
                $resumeClass = $recentClasses[0];
            }

            // Recent Tasks with submission status
            $recentTasks = $db->table('assignments a')
                ->select('a.id, a.class_id, a.title, a.due_at, lc.name AS class_name, sub.status AS submission_status, sub.score AS submission_score')
                ->join('learning_classes lc', 'lc.id = a.class_id')
                ->join('enrollments e', 'e.class_id = lc.id AND e.user_id = ' . (int) $userId)
                ->join('assignment_submissions sub', 'sub.assignment_id = a.id AND sub.enrollment_id = e.id', 'left')
                ->where('a.status', 'published')
                ->orderBy('a.due_at', 'ASC')
                ->limit(16)
                ->get()->getResultArray();

            // Registered Events
            $myEvents = $db->table('event_registrations er')
                ->select('er.*, ev.title AS event_title, ev.slug AS event_slug, ev.start_at, ev.end_at, ev.location, ev.mode AS delivery_mode, ev.meeting_url, a.status AS attendance_status')
                ->join('events ev', 'ev.id = er.event_id')
                ->join('attendances a', 'a.event_registration_id = er.id', 'left')
                ->where('er.user_id', $userId)
                ->orderBy('ev.start_at', 'DESC')
                ->limit(12)
                ->get()->getResultArray();

            // Certificates
            $myCertificates = $db->table('certificates c')
                ->select('c.*, lc.name AS class_name, ev.title AS event_title')
                ->join('learning_classes lc', 'lc.id = c.source_id AND c.source_type = \'learning_enrollment\'', 'left')
                ->join('events ev', 'ev.id = c.source_id AND c.source_type = \'event_attendance\'', 'left')
                ->where('c.user_id', $userId)
                ->where('c.status', 'issued')
                ->orderBy('c.issued_at', 'DESC')
                ->limit(12)
                ->get()->getResultArray();

            // Recent Quizzes / Assessments
            $recentQuizzes = [];
            $enrolledClassIds = array_map(static fn ($r) => (int) ($r['class_id'] ?? 0), $recentClasses);
            if ($enrolledClassIds !== []) {
                $recentQuizzes = $db->table('quizzes q')
                    ->select('q.id, q.class_id, q.title, q.type, lc.passing_grade, lc.name AS class_name, (SELECT MAX(qa.score) FROM quiz_attempts qa JOIN enrollments e2 ON e2.id = qa.enrollment_id WHERE qa.quiz_id = q.id AND e2.user_id = ' . (int) $userId . ') AS highest_score')
                    ->join('learning_classes lc', 'lc.id = q.class_id')
                    ->whereIn('q.class_id', $enrolledClassIds)
                    ->where('q.status', 'published')
                    ->orderBy('q.id', 'DESC')
                    ->limit(12)
                    ->get()->getResultArray();
            }

            // Trainer Real KPIs
            $classCount = $db->table('class_trainers')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->countAllResults();

            $trainerClassIds = $db->table('class_trainers')
                ->select('class_id')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->get()->getResultArray();
            $cIds = array_column($trainerClassIds, 'class_id');

            $studentCount = 0;
            $moduleCount = 0;
            if ($cIds !== []) {
                $studentCount = $db->table('enrollments')->whereIn('class_id', $cIds)->countAllResults();
                $moduleCount = $db->table('learning_modules')->whereIn('class_id', $cIds)->countAllResults();
            }

            $cards = [
                ['title' => 'Kelas Aktif', 'value' => sprintf('%02d', $enrolledCount), 'meta' => 'pembelajaran sedang diikuti', 'icon' => 'fas fa-graduation-cap'],
                ['title' => 'Materi Selesai', 'value' => sprintf('%02d', $completedLessonsCount), 'meta' => 'lesson telah diselesaikan', 'icon' => 'fas fa-book-reader'],
                ['title' => 'Sertifikat Terbit', 'value' => sprintf('%02d', $certCount), 'meta' => 'pencapaian kompetensi', 'icon' => 'fas fa-award'],
                ['title' => 'Event Terdaftar', 'value' => sprintf('%02d', $eventCount), 'meta' => 'agenda kegiatan & pelatihan', 'icon' => 'fas fa-calendar-alt'],
            ];

            if ($cIds !== []) {
                $recentClasses = $db->table('learning_classes lc')
                    ->select('lc.*, c.title AS course_title, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = lc.id) AS student_count')
                    ->join('courses c', 'c.id = lc.course_id', 'left')
                    ->whereIn('lc.id', $cIds)
                    ->orderBy('lc.created_at', 'DESC')
                    ->limit(12)
                    ->get()->getResultArray();
            }

        } elseif ($role === 'operator') {
            // Operator Real KPIs
            $pendingVerif = $db->table('profile_verifications')->where('status', 'pending')->countAllResults();
            $pendingEnroll = $db->table('enrollments')->where('status', 'pending')->countAllResults();
            $pendingApprov = $db->table('approvals')->where('status', 'pending')->countAllResults();
            $activeEvents = $db->table('events')->whereIn('status', ['open_registration', 'coming_soon'])->countAllResults();

            $cards = [
                ['title' => 'Verifikasi Profil', 'value' => sprintf('%02d', $pendingVerif), 'meta' => 'menunggu persetujuan', 'icon' => 'fas fa-id-card'],
                ['title' => 'Enrollment Kelas', 'value' => sprintf('%02d', $pendingEnroll), 'meta' => 'antrean pendaftaran', 'icon' => 'fas fa-user-plus'],
                ['title' => 'Antrean Approval', 'value' => sprintf('%02d', $pendingApprov), 'meta' => 'perlu tindakan', 'icon' => 'fas fa-stamp'],
                ['title' => 'Event Aktif', 'value' => sprintf('%02d', $activeEvents), 'meta' => 'kegiatan operasional', 'icon' => 'fas fa-calendar-check'],
            ];
        }

        // Recent Audit Activities for current actor
        $recentActivities = $db->table('audit_logs')
            ->where('actor_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return [
            'kpiCards'          => $cards,
            'recentClasses'     => $recentClasses,
            'resumeClass'       => $resumeClass ?? null,
            'userAccount'       => $userAccount ?? [],
            'recentTasks'       => $recentTasks,
            'recentQuizzes'     => $recentQuizzes ?? [],
            'recentActivities'  => $recentActivities,
            'myEvents'          => $myEvents ?? [],
            'myCertificates'    => $myCertificates ?? [],
        ];
    }

    private function userId(): int
    {
        $id = (int) session()->get('user_id');
        if ($id > 0) {
            return $id;
        }
        $first = db_connect()->table('users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
        return $first ? (int) $first['id'] : 1;
    }

    private function workspaceRole(): string
    {
        $roles = (array) session()->get('roles');

        if (in_array('super_admin', $roles, true) || in_array('super_administrator', $roles, true)) {
            return 'super_admin';
        }
        if (in_array('admin', $roles, true) || in_array('administrator', $roles, true)) {
            return 'admin';
        }
        if (in_array('operator', $roles, true)) {
            return 'operator';
        }
        if (in_array('trainer', $roles, true)) {
            return 'trainer';
        }

        return 'participant';
    }

    private function roleLabel(string $role): string
    {
        return [
            'super_admin' => 'Super Administrator',
            'admin'       => 'Administrator',
            'operator'    => 'Operator',
            'trainer'     => 'Pengajar',
            'participant' => 'Peserta',
        ][$role] ?? 'Pengguna';
    }

    private function normalizeSection(string $section): string
    {
        $allowed = [
            'dashboard',
            'pembelajaran',
            'tugas',
            'penilaian',
            'bookmark',
            'notifikasi',
            'profil',
            'aktivitas',
        ];

        return in_array($section, $allowed, true) ? $section : 'dashboard';
    }

    private function menuItems(string $role): array
    {
        return [];
    }

    private function sectionContent(string $section): array
    {
        $content = [
            'dashboard' => [
                'eyebrow'     => 'Ringkasan Workspace',
                'title'       => 'Dashboard Workspace',
                'description' => 'Pantau pembelajaran, tugas, dan aktivitas personal Anda dari satu ruang kerja terpadu.',
            ],
            'pembelajaran' => [
                'eyebrow'     => 'Area Pembelajaran',
                'title'       => 'Pembelajaran Saya',
                'description' => 'Temukan program, course, kelas, dan materi yang sedang Anda ikuti.',
            ],
            'tugas' => [
                'eyebrow'     => 'Aktivitas Belajar',
                'title'       => 'Tugas Pembelajaran',
                'description' => 'Daftar tugas dan aktivitas yang perlu Anda kerjakan dan perhatikan.',
            ],
            'penilaian' => [
                'eyebrow'     => 'Aktivitas Belajar',
                'title'       => 'Penilaian & Evaluasi',
                'description' => 'Ringkasan penilaian pre-test, quiz, tugas, dan kelulusan pembelajaran Anda.',
            ],
            'bookmark' => [
                'eyebrow'     => 'Personal',
                'title'       => 'Bookmark Saya',
                'description' => 'Konten dan referensi yang Anda simpan untuk dibaca kembali.',
            ],
            'notifikasi' => [
                'eyebrow'     => 'Pusat Informasi',
                'title'       => 'Notifikasi',
                'description' => 'Informasi terbaru terkait aktivitas Workspace Anda.',
            ],
            'profil' => [
                'eyebrow'     => 'Personal',
                'title'       => 'Profil Workspace',
                'description' => 'Kelola informasi dan preferensi personal Anda.',
            ],
            'aktivitas' => [
                'eyebrow'     => 'Riwayat Aktivitas',
                'title'       => 'Aktivitas Terakhir',
                'description' => 'Jejak aktivitas Workspace yang tercatat untuk pengguna.',
            ],
        ];

        return $content[$section] ?? $content['dashboard'];
    }
}
