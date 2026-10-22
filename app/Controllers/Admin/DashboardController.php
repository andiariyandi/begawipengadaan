<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\ReportAnalyticsService;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    private DashboardService $dashboardService;
    private ReportAnalyticsService $reportService;

    public function __construct(
        ?DashboardService $dashboardService = null,
        ?ReportAnalyticsService $reportService = null
    ) {
        $this->dashboardService = $dashboardService ?? new DashboardService();
        $this->reportService    = $reportService ?? new ReportAnalyticsService();
    }

    private function getRequest()
    {
        return $this->request ?? service('request');
    }

    public function index(): string
    {
        $summary = $this->dashboardService->getFullDashboardSummary();

        $context = [
            'title'              => 'Ringkasan Dashboard | BegawiPengadaan',
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
            'KPI Internal | BegawiPengadaan',
            'KPI Internal',
            'kpi'
        ));
    }

    public function approvalQueue(): string
    {
        $context = $this->buildHomeContext(
            'Antrean Persetujuan | BegawiPengadaan',
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
            'Aktivitas Terbaru | BegawiPengadaan',
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
            'Indikator Sistem | BegawiPengadaan',
            'Indikator Sistem',
            'system-indicators'
        );
        $accessSummary                   = $this->dashboardService->getAccessSummary();
        $context['variant']              = 'system-indicators';
        $context['usersByProfile']       = $accessSummary['usersByProfile'];
        $context['usersByAccountStatus'] = $accessSummary['usersByAccount'];

        return view('dashboard/home/summary', $context);
    }

    // =========================================================================
    // MODUL PUSAT LAPORAN & EKSPOR UNIVERSAL (FASE 2)
    // =========================================================================

    public function learningReport()
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        if ($req->getGet('export') === 'csv') {
            return $this->reportService->streamCsv('pembelajaran', $year);
        }
        $data = $this->reportService->getLearningReport($year);
        return $this->reportView('Laporan Pembelajaran', 'learning', 'learning', $data);
    }

    public function eventReport()
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        if ($req->getGet('export') === 'csv') {
            return $this->reportService->streamCsv('event', $year);
        }
        $data = $this->reportService->getEventReport($year);
        return $this->reportView('Laporan Event', 'events', 'events', $data);
    }

    public function certificateReport()
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        if ($req->getGet('export') === 'csv') {
            return $this->reportService->streamCsv('sertifikat', $year);
        }
        $data = $this->reportService->getCertificateReport($year);
        return $this->reportView('Laporan Sertifikat', 'certificates', 'certificates', $data);
    }

    public function competencyReport()
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        if ($req->getGet('export') === 'csv') {
            return $this->reportService->streamCsv('kompetensi', $year);
        }
        $data = $this->reportService->getCompetencyReport($year);
        return $this->reportView('Laporan Kompetensi', 'competency', 'competency', $data);
    }

    public function userReport()
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        if ($req->getGet('export') === 'csv') {
            return $this->reportService->streamCsv('pengguna', $year);
        }
        $data = $this->reportService->getUserReport($year);
        return $this->reportView('Laporan Pengguna', 'users', 'users', $data);
    }

    public function exportHistory()
    {
        $data = $this->reportService->getExportHistory();
        return $this->reportView(
            'Riwayat Export',
            'export-history',
            'export_history',
            $data
        );
    }

    public function exportReport(string $type)
    {
        $req  = $this->getRequest();
        $year = (int) ($req->getGet('year') ?: date('Y'));
        return $this->reportService->streamCsv($type, $year);
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
        string $viewName,
        array $reportData = []
    ): string {
        return view('dashboard/reports/' . $viewName, array_merge([
            'title'         => $pageTitle . ' | BegawiPengadaan',
            'pageTitle'     => $pageTitle,
            'activeMenu'    => 'reports',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Laporan'],
                ['label' => $pageTitle],
            ],
        ], $reportData));
    }
}
