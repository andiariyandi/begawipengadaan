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
        $activeClasses  = $db->table('learning_classes')->where('status', 'active')->countAllResults();
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

        // 6. Ringkasan Kelas Teraktif (Mendukung client-side pagination)
        $topClasses = $db->table('learning_classes lc')
            ->select('lc.*, courses.title AS course_title, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = lc.id) AS total_students')
            ->join('courses', 'courses.id = lc.course_id')
            ->where('lc.status', 'active')
            ->orderBy('total_students', 'DESC')
            ->limit(12)
            ->get()->getResultArray();

        // 7. Audit Log Aktivitas Sistem Terkini (Mendukung client-side pagination)
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
