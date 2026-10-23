<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class WorkspaceHomeController extends BaseController
{
    public function index()
    {
        $role = $this->workspaceRole();

        if ($role === 'super_administrator' || $role === 'administrator') {
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
            'title'          => 'Aktivitas Saya | Workspace BegawiPengadaan',
            'pageTitle'      => 'Aktivitas Saya',
            'breadcrumbTail' => [['label' => 'Aktivitas Saya']],
            'userName'       => (string) (session()->get('name') ?: 'Pengguna BegawiPengadaan'),
            'primaryRole'    => $role,
            'roleLabel'      => $this->roleLabel($role),
            'logs'           => $logs,
        ]);
    }

    /**
     * Endpoint API Command Palette (Ctrl + K) - Quick Spotlight Search.
     * Pencarian instan lintas navigasi, kelas pembelajaran, event & agenda, serta regulasi.
     */
    public function spotlightSearch()
    {
        $role = $this->workspaceRole();
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $db = db_connect();

        $categories = [];

        // 1. Navigasi & Menu Cepat berdasarkan Hak Akses Role
        $navShortcuts = [
            [
                'title'    => 'Dashboard Workspace',
                'subtitle' => 'Halaman utama ringkasan ruang kerja personal',
                'url'      => route_to('workspace.home'),
                'icon'     => 'fas fa-home',
                'badge'    => 'Umum',
                'roles'    => ['participant', 'trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Kelas Saya (Pembelajaran)',
                'subtitle' => 'Daftar seluruh kelas yang sedang aktif atau telah selesai',
                'url'      => route_to('workspace.participant.classes'),
                'icon'     => 'fas fa-book-reader',
                'badge'    => 'Peserta',
                'roles'    => ['participant', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Katalog Arena Belajar PBJ',
                'subtitle' => 'Jelajahi dan daftar program pelatihan pengadaan barang/jasa',
                'url'      => site_url('arena-belajar'),
                'icon'     => 'fas fa-graduation-cap',
                'badge'    => 'Katalog',
                'roles'    => ['participant', 'trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Transkrip Nilai Akademik PBJ',
                'subtitle' => 'Rekapitulasi pencapaian nilai, kuis, tugas, dan sertifikat',
                'url'      => route_to('workspace.participant.classes'),
                'icon'     => 'fas fa-file-invoice',
                'badge'    => 'Nilai',
                'roles'    => ['participant', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Event & Agenda PBJ Saya',
                'subtitle' => 'Webinar, Bimtek, dan Workshop PBJ terdaftar',
                'url'      => route_to('workspace.participant.events'),
                'icon'     => 'fas fa-calendar-check',
                'badge'    => 'Agenda',
                'roles'    => ['participant', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Sertifikat Digital Saya',
                'subtitle' => 'Unduh dan cetak e-sertifikat ber-QR resmi Pemprov Lampung',
                'url'      => route_to('workspace.participant.certificates'),
                'icon'     => 'fas fa-award',
                'badge'    => 'Sertifikat',
                'roles'    => ['participant', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Konsultasi & Helpdesk PBJ',
                'subtitle' => 'Layanan bantuan dua arah dengan tim Biro PBJ',
                'url'      => route_to('workspace.consultation.index'),
                'icon'     => 'fas fa-comments',
                'badge'    => 'Bantuan',
                'roles'    => ['participant', 'trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Profil & Pengaturan Akun',
                'subtitle' => 'Kelola identitas ASN, Penyedia, NIP/NIK, dan kata sandi',
                'url'      => route_to('workspace.profile.index'),
                'icon'     => 'fas fa-user-cog',
                'badge'    => 'Akun',
                'roles'    => ['participant', 'trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            // Trainer Shortcuts
            [
                'title'    => 'Ruang Pengajar / Kelas Diampu',
                'subtitle' => 'Manajemen materi, kuis, tugas, dan buku nilai kelas',
                'url'      => route_to('workspace.trainer.home'),
                'icon'     => 'fas fa-chalkboard-teacher',
                'badge'    => 'Trainer',
                'roles'    => ['trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Buku Nilai Kelas (Gradebook Matrix)',
                'subtitle' => 'Rekapitulasi nilai dan ekspor CSV kelas bimbingan',
                'url'      => route_to('workspace.trainer.home'),
                'icon'     => 'fas fa-table',
                'badge'    => 'Trainer',
                'roles'    => ['trainer', 'operator', 'administrator', 'super_administrator'],
            ],
            // Operator Shortcuts
            [
                'title'    => 'Pusat Operasional Pelatihan',
                'subtitle' => 'Antrean persetujuan, verifikasi profil, dan kelas',
                'url'      => route_to('workspace.operator.home'),
                'icon'     => 'fas fa-cogs',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Pusat Approval Akun & Pendaftaran',
                'subtitle' => 'Aksi massal persetujuan registrasi dan verifikasi berkas',
                'url'      => route_to('workspace.operator.approvals'),
                'icon'     => 'fas fa-user-check',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Verifikasi Tiket Profil (Diff Viewer)',
                'subtitle' => 'Pemeriksaan permohonan perubahan data identitas ASN/Penyedia',
                'url'      => route_to('workspace.operator.profile-change-requests'),
                'icon'     => 'fas fa-id-card',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Penerbitan Sertifikat Massal',
                'subtitle' => 'Verifikasi kelayakan dan terbitkan e-sertifikat serentak',
                'url'      => route_to('workspace.operator.certificates'),
                'icon'     => 'fas fa-stamp',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Monitoring Pembelajaran Lintas Kelas',
                'subtitle' => 'Pantau tingkat kelulusan dan progres seluruh kelas aktif',
                'url'      => route_to('workspace.operator.learning-monitoring'),
                'icon'     => 'fas fa-chart-line',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Daftar Hadir & Peserta Event (Roster Cetak)',
                'subtitle' => 'Manajemen presensi dan cetak daftar hadir resmi Pemprov Lampung',
                'url'      => route_to('workspace.operator.events'),
                'icon'     => 'fas fa-clipboard-list',
                'badge'    => 'Operator',
                'roles'    => ['operator', 'administrator', 'super_administrator'],
            ],
            // Executive Admin
            [
                'title'    => 'Pusat Kendali Eksekutif Workspace',
                'subtitle' => 'Executive health radar dan tata kelola ekosistem PBJ',
                'url'      => route_to('workspace.admin.home'),
                'icon'     => 'fas fa-crown',
                'badge'    => 'Eksekutif',
                'roles'    => ['administrator', 'super_administrator'],
            ],
            [
                'title'    => 'Dashboard Utama Administrasi (WowDash)',
                'subtitle' => 'Akses panel administrator penuh BegawiPengadaan',
                'url'      => route_to('dashboard.home'),
                'icon'     => 'fas fa-tachometer-alt',
                'badge'    => 'Admin',
                'roles'    => ['administrator', 'super_administrator'],
            ],
        ];

        // Saring menu navigasi berdasarkan peran dan kata kunci
        $matchedNav = [];
        foreach ($navShortcuts as $item) {
            if (! in_array($role, $item['roles'], true)) {
                continue;
            }
            if ($q !== '') {
                $match = (stripos($item['title'], $q) !== false)
                    || (stripos($item['subtitle'], $q) !== false)
                    || (stripos($item['badge'], $q) !== false);
                if (! $match) {
                    continue;
                }
            }
            $matchedNav[] = $item;
        }

        if (! empty($matchedNav)) {
            $categories[] = [
                'name'  => 'Navigasi & Menu Cepat',
                'icon'  => 'fas fa-compass',
                'items' => array_slice($matchedNav, 0, 8),
            ];
        }

        // 2. Kelas Pembelajaran Aktif
        $classQuery = $db->table('learning_classes lc')
            ->select('lc.id, lc.name AS class_name, lc.code AS class_code, lc.learning_method, courses.title AS course_title')
            ->join('courses', 'courses.id = lc.course_id')
            ->whereIn('lc.status', ['active', 'scheduled', 'ongoing', 'published', 'open']);

        if ($q !== '') {
            $classQuery->groupStart()
                ->like('lc.name', $q)
                ->orLike('lc.code', $q)
                ->orLike('courses.title', $q)
                ->groupEnd();
        }
        $matchedClasses = $classQuery->orderBy('lc.id', 'DESC')->limit(5)->get()->getResultArray();

        if (! empty($matchedClasses)) {
            $classItems = [];
            foreach ($matchedClasses as $mc) {
                $targetUrl = match ($role) {
                    'trainer'   => route_to('workspace.trainer.class.detail', (string) $mc['id']),
                    'operator', 'administrator', 'super_administrator' => route_to('workspace.operator.learning-monitoring'),
                    default     => route_to('workspace.participant.class.detail', (string) $mc['id']),
                };
                $classItems[] = [
                    'title'    => $mc['class_name'],
                    'subtitle' => ($mc['course_title'] ?? '') . ' (' . ($mc['class_code'] ?? '') . ')',
                    'url'      => $targetUrl,
                    'icon'     => 'fas fa-graduation-cap',
                    'badge'    => strtoupper((string) ($mc['learning_method'] ?? 'Kelas')),
                ];
            }
            $categories[] = [
                'name'  => 'Kelas Pembelajaran',
                'icon'  => 'fas fa-chalkboard',
                'items' => $classItems,
            ];
        }

        // 3. Event & Agenda Kegiatan
        $eventQuery = $db->table('events')
            ->select('id, title, slug, mode, start_at, location')
            ->whereIn('status', ['open_registration', 'coming_soon', 'published', 'ongoing']);

        if ($q !== '') {
            $eventQuery->groupStart()
                ->like('title', $q)
                ->orLike('location', $q)
                ->groupEnd();
        }
        $matchedEvents = $eventQuery->orderBy('start_at', 'ASC')->limit(4)->get()->getResultArray();

        if (! empty($matchedEvents)) {
            $eventItems = [];
            foreach ($matchedEvents as $ev) {
                $eventItems[] = [
                    'title'    => $ev['title'],
                    'subtitle' => date('d M Y, H:i', strtotime($ev['start_at'])) . ' WIB • ' . ($ev['location'] ?: 'Online'),
                    'url'      => site_url('event/' . ($ev['slug'] ?: $ev['id'])),
                    'icon'     => 'fas fa-calendar-alt',
                    'badge'    => strtoupper((string) ($ev['mode'] ?? 'Agenda')),
                ];
            }
            $categories[] = [
                'name'  => 'Event & Agenda PBJ',
                'icon'  => 'fas fa-calendar-day',
                'items' => $eventItems,
            ];
        }

        // 4. Regulasi PBJ & Dokumen Acuan (hanya jika ada kata kunci pencarian)
        if ($q !== '') {
            $regQuery = $db->table('regulations r')
                ->select('r.id, r.title, r.document_number, rc.name AS category_name')
                ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
                ->where('r.deleted_at', null)
                ->groupStart()
                ->like('r.title', $q)
                ->orLike('r.document_number', $q)
                ->groupEnd()
                ->limit(3)
                ->get()->getResultArray();

            if (! empty($regQuery)) {
                $regItems = [];
                foreach ($regQuery as $reg) {
                    $regItems[] = [
                        'title'    => ($reg['document_number'] ? $reg['document_number'] . ' - ' : '') . $reg['title'],
                        'subtitle' => 'Kategori: ' . ($reg['category_name'] ?: 'Regulasi PBJ'),
                        'url'      => site_url('regulasi'),
                        'icon'     => 'fas fa-book',
                        'badge'    => 'Regulasi',
                    ];
                }
                $categories[] = [
                    'name'  => 'Regulasi & Dokumen Acuan',
                    'icon'  => 'fas fa-file-alt',
                    'items' => $regItems,
                ];
            }
        }

        $totalCount = 0;
        foreach ($categories as $cat) {
            $totalCount += count($cat['items']);
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'query'      => $q,
            'categories' => $categories,
            'totalCount' => $totalCount,
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
            'title'          => ucfirst($section) . ' | Workspace BegawiPengadaan',
            'pageTitle'      => ucfirst($section) . ' Workspace',
            'userName'       => (string) (session()->get('name') ?: 'Pengguna BegawiPengadaan'),
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
                ->select('e.id AS enrollment_id, e.class_id, e.status AS enrollment_status, e.final_score, lc.code AS class_code, lc.name AS class_name, lc.passing_grade, lc.learning_method, lc.location_or_link, lc.description AS class_description, lc.target_audience AS class_target_audience, lc.start_at, lc.end_at, courses.title AS course_title, courses.slug AS course_slug, courses.level AS course_level, courses.duration_minutes, courses.cover_url AS course_cover_url, fa.path AS cover_path, (SELECT COUNT(*) FROM learning_modules lm WHERE lm.class_id = lc.id) AS module_count')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->join('courses', 'courses.id = lc.course_id')
                ->join('file_assets fa', 'fa.id = courses.cover_file_id', 'left')
                ->where('e.user_id', $userId)
                ->orderBy('e.updated_at', 'DESC')
                ->limit(16)
                ->get()->getResultArray();

            $enrollmentIds = array_map(static fn($r) => (int) $r['enrollment_id'], $recentClasses);
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
            $enrolledClassIds = array_map(static fn($r) => (int) ($r['class_id'] ?? 0), $recentClasses);
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

            // Consolidated Calendar Schedule & Deadlines (Learning, Tasks & Events)
            $calendarEvents = [];
            foreach ($recentClasses as $cl) {
                if (! empty($cl['start_at'])) {
                    $d = date('Y-m-d', strtotime($cl['start_at']));
                    $calendarEvents[] = [
                        'date'  => $d,
                        'time'  => date('H:i', strtotime($cl['start_at'])),
                        'type'  => 'class',
                        'badge' => 'Kelas Dimulai',
                        'title' => $cl['class_name'] ?? 'Kelas Pembelajaran',
                        'meta'  => $cl['course_title'] ?? '',
                        'url'   => route_to('workspace.participant.class.detail', (string) $cl['class_id']),
                    ];
                }
                if (! empty($cl['end_at'])) {
                    $d = date('Y-m-d', strtotime($cl['end_at']));
                    $calendarEvents[] = [
                        'date'  => $d,
                        'time'  => date('H:i', strtotime($cl['end_at'])),
                        'type'  => 'class',
                        'badge' => 'Batas Akhir Kelas',
                        'title' => $cl['class_name'] ?? 'Kelas Pembelajaran',
                        'meta'  => $cl['course_title'] ?? '',
                        'url'   => route_to('workspace.participant.class.detail', (string) $cl['class_id']),
                    ];
                }
            }
            foreach ($recentTasks as $task) {
                if (! empty($task['due_at'])) {
                    $d = date('Y-m-d', strtotime($task['due_at']));
                    $calendarEvents[] = [
                        'date'  => $d,
                        'time'  => date('H:i', strtotime($task['due_at'])),
                        'type'  => 'task',
                        'badge' => 'Batas Tugas',
                        'title' => $task['title'],
                        'meta'  => $task['class_name'] ?? '',
                        'url'   => route_to('workspace.participant.assignments', (string) $task['class_id']),
                    ];
                }
            }
            foreach ($myEvents as $ev) {
                if (! empty($ev['start_at'])) {
                    $d = date('Y-m-d', strtotime($ev['start_at']));
                    $calendarEvents[] = [
                        'date'  => $d,
                        'time'  => date('H:i', strtotime($ev['start_at'])),
                        'type'  => 'event',
                        'badge' => 'Agenda Acara',
                        'title' => $ev['event_title'],
                        'meta'  => ($ev['delivery_mode'] ?? 'Online') . ' • ' . (date('H:i', strtotime($ev['start_at'])) . ' WIB'),
                        'url'   => site_url('workspace/peserta/event-saya/' . ($ev['event_slug'] ?: $ev['event_id'])),
                    ];
                }
            }
            usort($calendarEvents, static fn($a, $b) => strcmp($a['date'], $b['date']));
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
            'calendarEvents'    => $calendarEvents ?? [],
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

        if (in_array('super_administrator', $roles, true) || in_array('super_administrator', $roles, true)) {
            return 'super_administrator';
        }
        if (in_array('administrator', $roles, true) || in_array('administrator', $roles, true)) {
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
            'super_administrator' => 'Super Administrator',
            'administrator'       => 'Administrator',
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
