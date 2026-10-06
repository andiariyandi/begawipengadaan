<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/home/index', $this->buildHomeContext(
            'Ringkasan Dashboard | BegawiPBJ',
            'Ringkasan',
            'dashboard'
        ));
    }

    public function internalKpi(): string
    {
        return view('dashboard/home/summary', $this->buildHomeContext(
            'KPI Internal | BegawiPBJ',
            'KPI Internal',
            'kpi'
        ));
    }

    public function approvalQueue(): string
    {
        $context = $this->buildHomeContext(
            'Antrean Persetujuan | BegawiPBJ',
            'Antrean Persetujuan',
            'approval-queue'
        );
        $context['variant']        = 'approval-queue';
        $context['approvalQueue']  = $this->fetchApprovalQueue(10);
        $context['approvedToday']  = $this->countApprovalsByDate('approved');
        $context['rejectedToday']  = $this->countApprovalsByDate('rejected');

        return view('dashboard/home/summary', $context);
    }

    public function recentActivity(): string
    {
        $context = $this->buildHomeContext(
            'Aktivitas Terbaru | BegawiPBJ',
            'Aktivitas Terbaru',
            'recent-activity'
        );
        $context['variant']      = 'recent-activity';
        $context['recentActivity'] = $this->fetchRecentActivity(10);

        return view('dashboard/home/summary', $context);
    }

    public function systemIndicators(): string
    {
        $context = $this->buildHomeContext(
            'Indikator Sistem | BegawiPBJ',
            'Indikator Sistem',
            'system-indicators'
        );
        $context['variant']              = 'system-indicators';
        $context['usersByProfile']       = $this->countByProfile();
        $context['usersByAccountStatus'] = $this->countByAccountStatus();

        return view('dashboard/home/summary', $context);
    }

    public function learningReport(): string
    {
        return $this->reportView('Laporan Pembelajaran', 'learning', 'learning');
    }

    public function eventReport(): string
    {
        return $this->reportView('Laporan Event', 'events', 'events');
    }

    public function certificateReport(): string
    {
        return $this->reportView('Laporan Sertifikat', 'certificates', 'certificates');
    }

    public function competencyReport(): string
    {
        return $this->reportView('Laporan Kompetensi', 'competency', 'competency');
    }

    public function userReport(): string
    {
        return $this->reportView('Laporan Pengguna', 'users', 'users');
    }

    public function exportHistory(): string
    {
        return $this->reportView(
            'Riwayat Export',
            'export-history',
            'export_history'
        );
    }

    /**
     * Build the shared context used by every dashboard home view:
     *  - title, pageTitle, activeMenu, activeSubmenu, breadcrumbs
     *  - KPI numbers queried live from the database
     */
    private function buildHomeContext(string $title, string $pageTitle, string $activeSubmenu): array
    {
        $kpis = $this->fetchKpis();

        return [
            'title'        => $title,
            'pageTitle'    => $pageTitle,
            'activeMenu'   => 'summary',
            'activeSubmenu'=> $activeSubmenu,
            'breadcrumbs'  => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => $pageTitle],
            ],
            'kpis'              => $kpis,
            'usersByProfile'    => $kpis['usersByProfile'],
            'usersByAccount'    => $kpis['usersByAccount'],
            'accountStatusDonut'=> $kpis['accountStatusDonut'],
            'enrollmentMonthly' => $kpis['enrollmentMonthly'],
            'otherUsers'        => max(0, $kpis['totalUsers'] - $kpis['activeUsers'] - $kpis['pendingUsers']),
            'approvalQueue'     => $this->fetchApprovalQueue(5),
            'recentActivity'    => $this->fetchRecentActivity(5),
        ];
    }

    private function countApprovalsByDate(string $status): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        return (int) db_connect()->table('approvals')
            ->where('status', $status)
            ->where('reviewed_at >=', $todayStart)
            ->where('reviewed_at <=', $todayEnd)
            ->countAllResults();
    }

    private function fetchKpis(): array
    {
        $db = db_connect();

        $totalUsers   = (int) $db->table('users')->where('deleted_at', null)->countAllResults();
        $activeUsers  = (int) $db->table('users')->where('deleted_at', null)->where('account_status', 'active')->countAllResults();
        $pendingUsers = (int) $db->table('users')->where('deleted_at', null)->whereIn('account_status', ['pending_verification', 'submitted'])->countAllResults();
        $otherUsers   = max(0, $totalUsers - $activeUsers - $pendingUsers);

        $activeEnrollments = (int) $db->table('enrollments')
            ->where('status', 'active')
            ->countAllResults();

        $issuedCertificates = (int) $db->table('certificates')
            ->where('status', 'issued')
            ->countAllResults();

        $pendingApprovals = (int) $db->table('approvals')
            ->where('status', 'pending')
            ->countAllResults();

        $monthStart = date('Y-m-01 00:00:00');
        $newUsersThisMonth = (int) $db->table('users')
            ->where('deleted_at', null)
            ->where('created_at >=', $monthStart)
            ->countAllResults();

        $newEnrollmentsThisMonth = (int) $db->table('enrollments')
            ->where('enrolled_at >=', $monthStart)
            ->countAllResults();

        $newCertificatesThisMonth = (int) $db->table('certificates')
            ->where('created_at >=', $monthStart)
            ->countAllResults();

        return [
            'totalUsers'             => $totalUsers,
            'activeUsers'            => $activeUsers,
            'pendingUsers'           => $pendingUsers,
            'activeEnrollments'      => $activeEnrollments,
            'issuedCertificates'     => $issuedCertificates,
            'pendingApprovals'       => $pendingApprovals,
            'newUsersThisMonth'      => $newUsersThisMonth,
            'newEnrollmentsThisMonth'=> $newEnrollmentsThisMonth,
            'newCertificatesThisMonth'=> $newCertificatesThisMonth,
            'usersByProfile'         => $this->countByProfile(),
            'usersByAccount'         => $this->countByAccountStatus(),
            'accountStatusDonut'     => $this->buildAccountStatusDonut($activeUsers, $pendingUsers, $otherUsers),
            'enrollmentMonthly'      => $this->fetchMonthlyEnrollment(),
        ];
    }

    private function countByProfile(): array
    {
        $rows = db_connect()->table('users')
            ->select('profile_type, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('profile_type')
            ->orderBy('profile_type')
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[(string) $row['profile_type']] = (int) $row['total'];
        }
        return $out;
    }

    private function countByAccountStatus(): array
    {
        $rows = db_connect()->table('users')
            ->select('account_status, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('account_status')
            ->orderBy('account_status')
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[(string) $row['account_status']] = (int) $row['total'];
        }
        return $out;
    }

    private function buildAccountStatusDonut(int $active, int $pending, int $other): array
    {
        return [
            'series' => [$active, $pending, $other],
            'labels' => ['Aktif', 'Pending', 'Lainnya'],
            'colors' => ['#487FFF', '#FF9F29', '#EF4A4A'],
            'total'  => $active + $pending + $other,
        ];
    }

    /**
     * Last-12-months enrollment counts and user-registration counts
     * keyed by month label ('Jan'..'Des'), oldest to newest.
     */
    private function fetchMonthlyEnrollment(): array
    {
        $db = db_connect();
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $now = new \DateTimeImmutable('first day of this month');
        $registration = array_fill(0, 12, 0);
        $enrollment   = array_fill(0, 12, 0);

        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->modify("-{$i} months")->format('Y-m-01 00:00:00');
            $monthEnd   = $now->modify("-{$i} months")->modify('+1 month')->format('Y-m-01 00:00:00');

            $registration[11 - $i] = (int) $db->table('users')
                ->where('deleted_at', null)
                ->where('created_at >=', $monthStart)
                ->where('created_at <', $monthEnd)
                ->countAllResults();

            $enrollment[11 - $i] = (int) $db->table('enrollments')
                ->where('enrolled_at >=', $monthStart)
                ->where('enrolled_at <', $monthEnd)
                ->countAllResults();
        }

        return [
            'categories'   => $labels,
            'registration' => $registration,
            'enrollment'   => $enrollment,
        ];
    }

    /**
     * Pending approvals joined with requester name. Limited to $limit.
     */
    private function fetchApprovalQueue(int $limit): array
    {
        $db = db_connect();

        $rows = $db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.created_at, u.username AS requester_username, u.email AS requester_email')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->where('a.status', 'pending')
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id'                => (int) $row['id'],
                'resource_type'     => (string) $row['resource_type'],
                'requested_action'  => (string) $row['requested_action'],
                'status'            => (string) $row['status'],
                'created_at'        => (string) $row['created_at'],
                'requester'         => (string) ($row['requester_username'] ?? $row['requester_email'] ?? '-'),
            ];
        }
        return $out;
    }

    /**
     * Recent audit_logs joined with actor username. Limited to $limit.
     */
    private function fetchRecentActivity(int $limit): array
    {
        $db = db_connect();

        $rows = $db->table('audit_logs al')
            ->select('al.id, al.actor_id, al.action, al.resource_type, al.resource_id, al.result, al.reason, al.created_at, u.username AS actor_username')
            ->join('users u', 'u.id = al.actor_id', 'left')
            ->orderBy('al.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id'            => (int) $row['id'],
                'actor_id'      => $row['actor_id'] !== null ? (int) $row['actor_id'] : null,
                'actor'         => (string) ($row['actor_username'] ?? 'sistem'),
                'action'        => (string) $row['action'],
                'resource_type' => (string) ($row['resource_type'] ?? ''),
                'resource_id'   => $row['resource_id'] !== null ? (int) $row['resource_id'] : null,
                'result'        => (string) ($row['result'] ?? ''),
                'reason'        => $row['reason'] !== null ? (string) $row['reason'] : null,
                'created_at'    => (string) $row['created_at'],
            ];
        }
        return $out;
    }

    private function summaryView(
        string $pageTitle,
        string $activeSubmenu
    ): string {
        return view('dashboard/home/summary', $this->buildHomeContext(
            $pageTitle . ' | BegawiPBJ',
            $pageTitle,
            $activeSubmenu
        ));
    }

    private function reportView(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName
    ): string {
        return view('dashboard/reports/' . $viewName, [
            'title' => $pageTitle . ' | BegawiPBJ',
            'pageTitle' => $pageTitle,
            'activeMenu' => 'reports',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Laporan'],
                ['label' => $pageTitle],
            ],
        ]);
    }
}
