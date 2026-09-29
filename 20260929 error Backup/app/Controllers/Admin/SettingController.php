<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingController extends BaseController
{
    public function index(): string
    {
        return $this->settingView(
            'Pengaturan Sistem',
            'settings',
            'index'
        );
    }

    public function general(): string
    {
        return $this->settingView(
            'Pengaturan Umum',
            'general-settings',
            'general'
        );
    }

    public function organization(): string
    {
        return $this->settingView(
            'Pengaturan Organisasi',
            'organization-settings',
            'organization'
        );
    }

    public function learning(): string
    {
        return $this->settingView(
            'Pengaturan Pembelajaran',
            'learning-settings',
            'learning'
        );
    }

    public function event(): string
    {
        return $this->settingView(
            'Pengaturan Event',
            'event-settings',
            'event'
        );
    }

    public function certificate(): string
    {
        return $this->settingView(
            'Pengaturan Sertifikat',
            'certificate-settings',
            'certificate'
        );
    }

    public function notification(): string
    {
        return $this->settingView(
            'Pengaturan Notifikasi',
            'notification-settings',
            'notification'
        );
    }

    public function integration(): string
    {
        return $this->settingView(
            'Integrasi Sistem',
            'integration-settings',
            'integration'
        );
    }

    public function security(): string
    {
        return $this->settingView(
            'Keamanan Sistem',
            'security-settings',
            'security'
        );
    }

    public function maintenance(): string
    {
        return $this->settingView(
            'Maintenance Sistem',
            'maintenance',
            'maintenance'
        );
    }

    private function settingView(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName,
        array $data = []
    ): string {
        return view(
            'dashboard/settings/' . $viewName,
            array_merge([
                'title' => $pageTitle . ' | BegawiPBJ',
                'pageTitle' => $pageTitle,
                'activeMenu' => 'system',
                'activeSubmenu' => $activeSubmenu,
                'breadcrumbs' => [
                    [
                        'label' => 'Dashboard',
                        'url' => site_url('dashboard'),
                    ],
                    [
                        'label' => 'Sistem',
                        'url' => site_url('dashboard/sistem/pengaturan'),
                    ],
                    [
                        'label' => $pageTitle,
                    ],
                ],
            ], $data)
        );
    }
}
