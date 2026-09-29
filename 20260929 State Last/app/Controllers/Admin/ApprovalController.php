<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ApprovalController extends BaseController
{
    public function index(): string
    {
        return $this->view('Antrean Persetujuan', 'queue', 'index');
    }

    public function detail(string $approvalId): string
    {
        return $this->view('Detail Persetujuan', 'queue', 'detail', [
            'approvalId' => $approvalId,
        ]);
    }

    public function history(): string
    {
        return $this->view('Riwayat Persetujuan', 'history', 'history');
    }

    public function approve(string $approvalId): string
    {
        return $this->view('Konfirmasi Persetujuan', 'queue', 'action', [
            'approvalId' => $approvalId,
            'action' => 'approve',
            'actionLabel' => 'Setujui',
        ]);
    }

    public function reject(string $approvalId): string
    {
        return $this->view('Konfirmasi Penolakan', 'queue', 'action', [
            'approvalId' => $approvalId,
            'action' => 'reject',
            'actionLabel' => 'Tolak',
        ]);
    }

    private function view(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName,
        array $data = []
    ): string {
        return view('dashboard/approvals/' . $viewName, array_merge([
            'title' => $pageTitle . ' | BegawiPBJ',
            'pageTitle' => $pageTitle,
            'activeMenu' => 'system',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Sistem'],
                ['label' => $pageTitle],
            ],
        ], $data));
    }
}
