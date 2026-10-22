<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class AdminWorkspaceController extends BaseController
{
    /**
     * Pastikan pengguna memiliki hak akses Admin atau Super Admin.
     */
    private function assertAdminAccess(): int
    {
        if (session()->get('isLoggedIn') !== true) {
            redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk terlebih dahulu.')->send();
            exit;
        }

        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            redirect()->to('/dashboard/masuk')->with('error', 'Sesi tidak valid. Silakan masuk ulang.')->send();
            exit;
        }

        $roles   = (array) session()->get('roles');
        $allowed = ['super_admin', 'super_administrator', 'admin', 'administrator'];
        if (array_intersect($allowed, $roles) === []) {
            redirect()->to('/workspace')->with('error', 'Akses ditolak. Halaman khusus Administrator / Super Administrator.')->send();
            exit;
        }

        return $userId;
    }

    /**
     * Executive Overview Workspace untuk Super Admin & Admin.
     */
    public function index()
    {
        $this->assertAdminAccess();
        $db = db_connect();

        $roles = (array) session()->get('roles');
        $isSuperAdmin = in_array('super_admin', $roles, true) || in_array('super_administrator', $roles, true);

        // 1. Metrik Pengguna & Verifikasi
        $totalUsers    = $db->table('users')->countAllResults();
        $activeUsers   = $db->table('users')->where('account_status', 'active')->countAllResults();
        $pendingVerif  = $db->table('profile_verifications')->where('status', 'pending')->countAllResults();
        $pendingApprov = $db->table('approvals')->where('status', 'pending')->countAllResults();

        // 2. Metrik Pembelajaran
        $totalClasses   = $db->table('learning_classes')->countAllResults();
        $activeClasses  = $db->table('learning_classes')->whereIn('status', ['active', 'scheduled', 'ongoing', 'published', 'open'])->countAllResults();
        $totalEnroll    = $db->table('enrollments')->countAllResults();
        $activeEnroll   = $db->table('enrollments')->where('status', 'active')->countAllResults();
        $completedEnroll= $db->table('enrollments')->where('status', 'completed')->countAllResults();

        // 3. Metrik Trainer
        $activeTrainers = $db->table('class_trainers')
            ->select('user_id')
            ->where('status', 'active')
            ->distinct()
            ->countAllResults();
        if ($activeTrainers === 0) {
            $activeTrainers = $db->table('user_roles ur')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'trainer')
                ->where('ur.status', 'active')
                ->countAllResults();
        }

        // 4. Metrik Event & Agenda
        $totalEvents  = $db->table('events')->countAllResults();
        $activeEvents = $db->table('events')->whereIn('status', ['open_registration', 'coming_soon', 'published', 'ongoing'])->countAllResults();
        $totalEventReg = $db->table('event_registrations')->countAllResults();

        // 5. Metrik Sertifikat
        $totalCerts   = $db->table('certificates')->where('status', 'issued')->countAllResults();
        $learningCerts= $db->table('certificates')->where('status', 'issued')->where('source_type', 'learning_enrollment')->countAllResults();
        $eventCerts   = $db->table('certificates')->where('status', 'issued')->where('source_type', 'event_attendance')->countAllResults();
        $pendingCerts = $db->table('enrollments')->where('certificate_eligible_at IS NOT NULL')->countAllResults();

        // 6. EXECUTIVE HEALTH RADAR (4 DOMAIN PBJ)
        // Domain 1: Tingkat Kelulusan Akademik (Completion Rate)
        $completionRate = ($totalEnroll > 0) ? round(($completedEnroll / $totalEnroll) * 100, 1) : 0;
        $completionStatus = match (true) {
            $completionRate >= 70 => ['label' => 'Optimal', 'badge' => 'bg-success text-white', 'icon' => 'fas fa-check-circle', 'note' => 'Kelulusan peserta di atas target standar kompetensi'],
            $completionRate >= 40 => ['label' => 'Normal', 'badge' => 'bg-primary text-white', 'icon' => 'fas fa-info-circle', 'note' => 'Sebagian peserta sedang menyelesaikan modul dan tugas'],
            default               => ['label' => 'Perhatian', 'badge' => 'bg-warning text-dark', 'icon' => 'fas fa-exclamation-triangle', 'note' => 'Banyak peserta belum menuntaskan kurikulum'],
        };

        // Domain 2: Efektivitas Evaluasi Penilaian Trainer (Grading Turnaround Rate)
        $totalSubmissions = $db->table('assignment_submissions')->countAllResults();
        $gradedSubmissions = $db->table('assignment_submissions')->whereIn('status', ['graded', 'approved', 'completed'])->countAllResults();
        $pendingSubmissions = max(0, $totalSubmissions - $gradedSubmissions);
        $turnaroundRate = ($totalSubmissions > 0) ? round(($gradedSubmissions / $totalSubmissions) * 100, 1) : 100;
        $turnaroundStatus = match (true) {
            $turnaroundRate >= 80 => ['label' => 'Responsif', 'badge' => 'bg-success text-white', 'icon' => 'fas fa-bolt', 'note' => 'Antrean review tugas diperiksa cepat oleh pengajar'],
            $turnaroundRate >= 50 => ['label' => 'Moderat', 'badge' => 'bg-info text-white', 'icon' => 'fas fa-clock', 'note' => 'Sebagian tugas menunggu giliran penilaian pengajar'],
            default               => ['label' => 'Antrean Menumpuk', 'badge' => 'bg-warning text-dark', 'icon' => 'fas fa-hourglass-half', 'note' => 'Perlu dorongan percepatan penilaian kepada trainer'],
        };

        // Domain 3: Volume & Kecepatan Penerbitan Sertifikat (Certification Velocity)
        $startOfMonth = date('Y-m-01 00:00:00');
        $certsThisMonth = $db->table('certificates')->where('status', 'issued')->where('issued_at >=', $startOfMonth)->countAllResults();
        $certPool = $totalCerts + $pendingCerts;
        $certIssuanceRatio = ($certPool > 0) ? round(($totalCerts / $certPool) * 100, 1) : 100;
        $certStatus = match (true) {
            $certIssuanceRatio >= 85 => ['label' => 'Terkendali', 'badge' => 'bg-success text-white', 'icon' => 'fas fa-shield-alt', 'note' => 'Penerbitan tertib ber-QR resmi Pemprov Lampung'],
            $certIssuanceRatio >= 60 => ['label' => 'Berjalan Normal', 'badge' => 'bg-primary text-white', 'icon' => 'fas fa-award', 'note' => 'Penerbitan berjalan berkesinambungan'],
            default                  => ['label' => 'Antrean Kelayakan', 'badge' => 'bg-warning text-dark', 'icon' => 'fas fa-stamp', 'note' => 'Terdapat peserta lulus yang belum diterbitkan sertifikatnya'],
        };

        // Domain 4: Indeks Partisipasi & Presensi Event (Event Attendance Index)
        $totalAttended = $db->table('attendances')->where('status', 'present')->countAllResults();
        $attendanceRate = ($totalEventReg > 0) ? round(($totalAttended / $totalEventReg) * 100, 1) : 0;
        $attendanceStatus = match (true) {
            $attendanceRate >= 70 => ['label' => 'Tinggi', 'badge' => 'bg-success text-white', 'icon' => 'fas fa-users', 'note' => 'Tingkat kehadiran sah peserta event sangat aktif'],
            $attendanceRate >= 40 => ['label' => 'Sedang', 'badge' => 'bg-primary text-white', 'icon' => 'fas fa-user-check', 'note' => 'Partisipasi kehadiran dalam batas normal operasional'],
            default               => ['label' => 'Perlu Peningkatan', 'badge' => 'bg-warning text-dark', 'icon' => 'fas fa-user-clock', 'note' => 'Banyak peserta terdaftar belum tercatat hadir'],
        };

        $healthRadar = [
            'completion' => [
                'rate'      => $completionRate,
                'completed' => $completedEnroll,
                'total'     => $totalEnroll,
                'status'    => $completionStatus['label'],
                'badge'     => $completionStatus['badge'],
                'icon'      => $completionStatus['icon'],
                'note'      => $completionStatus['note'],
            ],
            'turnaround' => [
                'rate'      => $turnaroundRate,
                'graded'    => $gradedSubmissions,
                'total'     => $totalSubmissions,
                'pending'   => $pendingSubmissions,
                'status'    => $turnaroundStatus['label'],
                'badge'     => $turnaroundStatus['badge'],
                'icon'      => $turnaroundStatus['icon'],
                'note'      => $turnaroundStatus['note'],
            ],
            'certVelocity' => [
                'rate'      => $certIssuanceRatio,
                'thisMonth' => $certsThisMonth,
                'total'     => $totalCerts,
                'pending'   => $pendingCerts,
                'status'    => $certStatus['label'],
                'badge'     => $certStatus['badge'],
                'icon'      => $certStatus['icon'],
                'note'      => $certStatus['note'],
            ],
            'attendance' => [
                'rate'      => $attendanceRate,
                'attended'  => $totalAttended,
                'total'     => $totalEventReg,
                'status'    => $attendanceStatus['label'],
                'badge'     => $attendanceStatus['badge'],
                'icon'      => $attendanceStatus['icon'],
                'note'      => $attendanceStatus['note'],
            ],
        ];

        // 7. Ringkasan Utilisasi Berkas & Aset Pembelajaran
        $totalFileAssets = $db->table('file_assets')->countAllResults();
        $sumSizeBytesRow = $db->table('file_assets')->selectSum('size_bytes')->get()->getRowArray();
        $totalStorageBytes = (int) ($sumSizeBytesRow['size_bytes'] ?? 0);
        $totalLessons = $db->table('lessons')->countAllResults();
        $totalQuizzes = $db->table('quizzes')->countAllResults();

        $assetStats = [
            'totalFiles'        => $totalFileAssets,
            'storageBytes'      => $totalStorageBytes,
            'storageFormatted'  => $totalStorageBytes > 1073741824 ? round($totalStorageBytes / 1073741824, 2) . ' GB' : ($totalStorageBytes > 1048576 ? round($totalStorageBytes / 1048576, 1) . ' MB' : round($totalStorageBytes / 1024, 0) . ' KB'),
            'totalLessons'      => $totalLessons,
            'totalQuizzes'      => $totalQuizzes,
        ];

        // 8. Ringkasan Kelas Teraktif (Mendukung client-side pagination)
        $topClasses = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = lc.id) AS total_students')
            ->join('courses', 'courses.id = lc.course_id')
            ->whereIn('lc.status', ['active', 'scheduled', 'ongoing', 'published', 'open'])
            ->orderBy('total_students', 'DESC')
            ->limit(12)
            ->get()->getResultArray();

        // 9. Audit Log Aktivitas Sistem Terkini (Mendukung client-side pagination)
        $recentAuditLogs = $db->table('audit_logs a')
            ->select('a.*, u.name AS actor_name, u.email AS actor_email')
            ->join('users u', 'u.id = a.actor_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return view('main/workspace/pages/admin/home', [
            'pageTitle'       => 'Pusat Kendali Eksekutif Workspace',
            'userName'        => (string) (session()->get('name') ?: session()->get('username') ?: 'Administrator'),
            'isSuperAdmin'    => $isSuperAdmin,
            'roleLabel'       => $isSuperAdmin ? 'Super Administrator' : 'Administrator',
            'healthRadar'     => $healthRadar,
            'assetStats'      => $assetStats,
            'stats'           => [
                'totalUsers'      => $totalUsers,
                'activeUsers'     => $activeUsers,
                'pendingVerif'    => $pendingVerif,
                'pendingApprov'   => $pendingApprov,
                'totalClasses'    => $totalClasses,
                'activeClasses'   => $activeClasses,
                'totalEnroll'     => $totalEnroll,
                'activeEnroll'    => $activeEnroll,
                'completedEnroll' => $completedEnroll,
                'activeTrainers'  => $activeTrainers,
                'totalEvents'     => $totalEvents,
                'activeEvents'    => $activeEvents,
                'totalEventReg'   => $totalEventReg,
                'totalCerts'      => $totalCerts,
                'learningCerts'   => $learningCerts,
                'eventCerts'      => $eventCerts,
                'pendingCerts'    => $pendingCerts,
            ],
            'topClasses'      => $topClasses,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}
