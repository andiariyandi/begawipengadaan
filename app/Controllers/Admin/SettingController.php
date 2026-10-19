<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\SystemSettingService;

class SettingController extends BaseController
{
    private SystemSettingService $settingService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->settingService = new SystemSettingService();
        $this->auditService = new AuditService();
    }

    public function index(): string
    {
        return $this->view('Pengaturan Sistem', 'settings', 'index');
    }

    public function general(): string
    {
        return $this->view('Pengaturan Umum', 'general-settings', 'general', [
            'settings' => $this->settingService->getGroup('general'),
            'group'    => 'general',
        ]);
    }

    public function organization(): string
    {
        return $this->view('Pengaturan Organisasi', 'organization-settings', 'organization', [
            'settings' => $this->settingService->getGroup('organization'),
            'group'    => 'organization',
        ]);
    }

    public function learning(): string
    {
        return $this->view('Pengaturan Pembelajaran', 'learning-settings', 'learning', [
            'settings' => $this->settingService->getGroup('learning'),
            'group'    => 'learning',
        ]);
    }

    public function knowledge(): string
    {
        return $this->view('Pengaturan Bunga Rampai', 'knowledge-settings', 'knowledge', [
            'settings' => $this->settingService->getGroup('knowledge'),
            'group'    => 'knowledge',
        ]);
    }

    public function regulation(): string
    {
        return $this->view('Pengaturan Regulasi', 'regulation-settings', 'regulation', [
            'settings'   => $this->settingService->getGroup('regulation'),
            'categories' => (new \App\Models\Regulation\RegulationCategoryModel())->listAll(),
            'group'      => 'regulation',
        ]);
    }

    public function news(): string
    {
        return $this->view('Pengaturan Berita & Artikel', 'news-settings', 'news', [
            'settings'   => $this->settingService->getGroup('news'),
            'categories' => (new \App\Models\Communication\NewsCategoryModel())->findAll(),
            'group'      => 'news',
        ]);
    }

    public function event(): string
    {
        return $this->view('Pengaturan Event & Agenda', 'event-settings', 'event', [
            'settings' => $this->settingService->getGroup('event'),
            'group'    => 'event',
        ]);
    }

    public function certificate(): string
    {
        return $this->view('Pengaturan Sertifikat', 'certificate-settings', 'certificate', [
            'settings' => $this->settingService->getGroup('certificate'),
            'group'    => 'certificate',
        ]);
    }

    public function notification(): string
    {
        return $this->view('Pengaturan Notifikasi', 'notification-settings', 'notification', [
            'settings' => $this->settingService->getGroup('notification'),
            'group'    => 'notification',
        ]);
    }

    public function integration(): string
    {
        return $this->view('Integrasi Sistem', 'integration-settings', 'integration', [
            'settings' => $this->settingService->getGroup('integration'),
            'group'    => 'integration',
        ]);
    }

    public function security(): string
    {
        return $this->view('Keamanan Sistem', 'security-settings', 'security', [
            'settings' => $this->settingService->getGroup('security'),
            'group'    => 'security',
        ]);
    }

    public function maintenance(): string
    {
        return $this->view('Maintenance Sistem', 'maintenance', 'maintenance', [
            'settings' => $this->settingService->getGroup('maintenance'),
            'group'    => 'maintenance',
        ]);
    }

    public function groupView(string $segment): string
    {
        return match ($segment) {
            'general'      => $this->general(),
            'organization' => $this->organization(),
            'learning'     => $this->learning(),
            'knowledge', 'bunga-rampai' => $this->knowledge(),
            'regulation', 'regulasi'    => $this->regulation(),
            'news', 'berita'            => $this->news(),
            'event'        => $this->event(),
            'certificate'  => $this->certificate(),
            'notification' => $this->notification(),
            'integration'  => $this->integration(),
            'security'     => $this->security(),
            'maintenance'  => $this->maintenance(),
            default        => $this->index(),
        };
    }

    public function save(string $group)
    {
        $actorId = (int) (session()->get('user_id') ?: 1);
        $postData = $this->request->getPost();

        // Normalize group alias
        $groupKey = match ($group) {
            'pengaturan-umum', 'general-settings', 'general'             => 'general',
            'pengaturan-organisasi', 'organization-settings', 'organization' => 'organization',
            'pengaturan-pembelajaran', 'learning-settings', 'learning'   => 'learning',
            'pengaturan-bunga-rampai', 'knowledge-settings', 'knowledge', 'bunga-rampai' => 'knowledge',
            'pengaturan-regulasi', 'regulation-settings', 'regulation', 'regulasi' => 'regulation',
            'pengaturan-berita', 'news-settings', 'news', 'berita'       => 'news',
            'pengaturan-event', 'event-settings', 'event'                => 'event',
            'pengaturan-sertifikat', 'certificate-settings', 'certificate' => 'certificate',
            'pengaturan-notifikasi', 'notification-settings', 'notification' => 'notification',
            'pengaturan-integrasi', 'integration-settings', 'integration' => 'integration',
            'pengaturan-keamanan', 'security-settings', 'security'       => 'security',
            'maintenance'                                                => 'maintenance',
            default                                                      => $group,
        };

        $ok = $this->settingService->saveGroup($groupKey, $postData);

        if ($ok) {
            $this->auditService->log(
                $actorId,
                'system.settings.updated',
                'system_settings',
                $groupKey,
                null,
                [
                    'group'   => $groupKey,
                    'updated' => array_keys($postData),
                ]
            );

            return redirect()->back()->with('message', 'Pengaturan ' . ucfirst($groupKey) . ' berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengaturan.');
    }

    private function view(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName,
        array $data = []
    ): string {
        return view('dashboard/settings/' . $viewName, array_merge([
            'title' => $pageTitle . ' | BegawiPengadaan',
            'pageTitle' => $pageTitle,
            'activeMenu' => 'system',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Sistem', 'url' => site_url('dashboard/pengaturan')],
                ['label' => $pageTitle],
            ],
        ], $data));
    }
}
