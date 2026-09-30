<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AuditController extends BaseController
{
    public function index(): string
    {
        return $this->view('Audit Trail', 'audit-trail', 'index');
    }

    public function detail(string $auditId): string
    {
        return $this->view('Detail Audit Trail', 'audit-trail', 'detail', [
            'auditId' => $auditId,
        ]);
    }

    public function files(): string
    {
        return $this->view('File dan Media', 'files', 'files');
    }

    public function notifications(): string
    {
        return $this->view('Notifikasi Sistem', 'notifications', 'notifications');
    }

    private function view(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName,
        array $data = []
    ): string {
        return view('dashboard/audit/' . $viewName, array_merge([
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
