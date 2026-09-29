<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/home/index', [
            'title' => 'Ringkasan Dashboard | BegawiPBJ',
            'activeMenu' => 'summary',
            'activeSubmenu' => 'dashboard',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Ringkasan'],
            ],
        ]);
    }

    public function internalKpi(): string
    {
        return $this->summaryView('KPI Internal', 'kpi');
    }

    public function approvalQueue(): string
    {
        return $this->summaryView('Antrean Persetujuan', 'approval-queue');
    }

    public function recentActivity(): string
    {
        return $this->summaryView('Aktivitas Terbaru', 'recent-activity');
    }

    public function systemIndicators(): string
    {
        return $this->summaryView('Indikator Sistem', 'system-indicators');
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

    private function summaryView(
        string $pageTitle,
        string $activeSubmenu
    ): string {
        return view('dashboard/home/summary', [
            'title' => $pageTitle . ' | BegawiPBJ',
            'pageTitle' => $pageTitle,
            'activeMenu' => 'summary',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => $pageTitle],
            ],
        ]);
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
