<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    private DashboardService $dashboardService;

    public function __construct(?DashboardService $dashboardService = null)
    {
        $this->dashboardService = $dashboardService ?? new DashboardService();
    }

    public function index(): string
    {
        $summary = $this->dashboardService->getFullDashboardSummary();

        $context = [
            'title'              => 'Ringkasan Dashboard | BegawiPBJ',
            'pageTitle'          => 'Ringkasan',
            'activeMenu'         => 'summary',
            'activeSubmenu'      => 'dashboard',
            'breadcrumbs'        => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Ringkasan'],
            ],
            'kpis'               => $summary['kpis'],
            'access'             => $summary['access'],
            'learning'           => $summary['learning'],
            'knowledge'          => $summary['knowledge'],
            'regulations'        => $summary['regulations'],
            'communication'      => $summary['communication'],
            'certificate'        => $summary['certificate'],
            'usersByProfile'     => $summary['access']['usersByProfile'],
            'usersByAccount'     => $summary['access']['usersByAccount'],
            'accountStatusDonut' => $summary['access']['accountStatusDonut'],
            'enrollmentMonthly'  => $summary['learning']['enrollmentMonthly'],
            'otherUsers'         => $summary['access']['otherUsers'],
            'approvalQueue'      => $summary['approvalQueue'],
            'recentActivity'     => $summary['recentActivity'],
        ];

        return view('dashboard/home/index', $context);
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
        $certSummary               = $this->dashboardService->getCertificateSummary();
        $context['variant']        = 'approval-queue';
        $context['approvalQueue']  = $certSummary['approvalQueue'];
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
        $context['variant']        = 'recent-activity';
        $context['recentActivity'] = $this->dashboardService->getAuditSummary(15);

        return view('dashboard/home/summary', $context);
    }

    public function systemIndicators(): string
    {
        $context = $this->buildHomeContext(
            'Indikator Sistem | BegawiPBJ',
            'Indikator Sistem',
            'system-indicators'
        );
        $accessSummary                   = $this->dashboardService->getAccessSummary();
        $context['variant']              = 'system-indicators';
        $context['usersByProfile']       = $accessSummary['usersByProfile'];
        $context['usersByAccountStatus'] = $accessSummary['usersByAccount'];

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
     * Build the shared context used by dashboard summary views
     */
    private function buildHomeContext(string $title, string $pageTitle, string $activeSubmenu): array
    {
        $summary = $this->dashboardService->getFullDashboardSummary();

        return [
            'title'              => $title,
            'pageTitle'          => $pageTitle,
            'activeMenu'         => 'summary',
            'activeSubmenu'      => $activeSubmenu,
            'breadcrumbs'        => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => $pageTitle],
            ],
            'kpis'               => $summary['kpis'],
            'access'             => $summary['access'],
            'learning'           => $summary['learning'],
            'knowledge'          => $summary['knowledge'],
            'regulations'        => $summary['regulations'],
            'communication'      => $summary['communication'],
            'certificate'        => $summary['certificate'],
            'usersByProfile'     => $summary['access']['usersByProfile'],
            'usersByAccount'     => $summary['access']['usersByAccount'],
            'accountStatusDonut' => $summary['access']['accountStatusDonut'],
            'enrollmentMonthly'  => $summary['learning']['enrollmentMonthly'],
            'otherUsers'         => $summary['access']['otherUsers'],
            'approvalQueue'      => $summary['approvalQueue'],
            'recentActivity'     => $summary['recentActivity'],
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

    private function reportView(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName
    ): string {
        return view('dashboard/reports/' . $viewName, [
            'title'         => $pageTitle . ' | BegawiPBJ',
            'pageTitle'     => $pageTitle,
            'activeMenu'    => 'reports',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Laporan'],
                ['label' => $pageTitle],
            ],
        ]);
    }
}
