<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;
use App\Services\SystemSettingService;

class InstitutionProfileController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->settingService = new SystemSettingService();
    }

    public function index()
    {
        $all = $this->settingService->getAll();

        return view('dashboard/landing/profile/index', [
            'title'         => 'Profil Lembaga & Visi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Profil Lembaga, Visi Misi & Filosofi',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'profil-lembaga',
            'general'       => $all['general'] ?? [],
            'organization'  => $all['organization'] ?? [],
            'social'        => $all['social'] ?? [],
            'visionMission' => $all['vision_mission'] ?? [],
            'philosophy'    => $all['philosophy'] ?? [],
            'pledge'        => $all['pledge'] ?? [],
            'footer'        => $all['footer'] ?? [],
        ]);
    }

    public function save()
    {
        $post = $this->request->getPost();

        // 1. General & Organization (including Logo & Seal Uploads)
        if (isset($post['organization'])) {
            $org = $post['organization'];

            // Handle Logo File Upload
            $logoFile = $this->request->getFile('logo_file');
            $currLogo = $this->settingService->get('organization', 'logo_url', 'assets/main/images/resources/logo-1.png');
            $manualLogo = trim((string) ($org['logo_url'] ?? ''));
            $uploadedLogo = LandingImageUploadService::upload($logoFile, 'profile', $manualLogo ?: $currLogo, 'institution_logo');
            if ($uploadedLogo) {
                $org['logo_url'] = $uploadedLogo;
            } elseif (! empty($manualLogo)) {
                $org['logo_url'] = $manualLogo;
            } else {
                $org['logo_url'] = $currLogo;
            }

            // Handle Official Seal / Cap Lembaga File Upload
            $sealFile = $this->request->getFile('seal_file');
            $currSeal = $this->settingService->get('organization', 'seal_url', 'assets/main/images/resources/seal-prov-lampung.png');
            $manualSeal = trim((string) ($org['seal_url'] ?? ''));
            $uploadedSeal = LandingImageUploadService::upload($sealFile, 'profile', $manualSeal ?: $currSeal, 'institution_seal');
            if ($uploadedSeal) {
                $org['seal_url'] = $uploadedSeal;
            } elseif (! empty($manualSeal)) {
                $org['seal_url'] = $manualSeal;
            } else {
                $org['seal_url'] = $currSeal;
            }

            $this->settingService->saveGroup('organization', $org);
        }

        // 2. Social Links
        if (isset($post['social'])) {
            $this->settingService->saveGroup('social', $post['social']);
        }

        // 3. Vision & Mission
        if (isset($post['vision_mission'])) {
            $vm = $post['vision_mission'];
            if (isset($vm['mission_items_raw'])) {
                $lines = array_filter(array_map('trim', explode("\n", (string) $vm['mission_items_raw'])));
                $vm['mission_items'] = json_encode(array_values($lines), JSON_UNESCAPED_UNICODE);
                unset($vm['mission_items_raw']);
            }
            $this->settingService->saveGroup('vision_mission', $vm);
        }

        // 4. Philosophy
        if (isset($post['philosophy'])) {
            $this->settingService->saveGroup('philosophy', $post['philosophy']);
        }

        // 5. Pledge
        if (isset($post['pledge'])) {
            $this->settingService->saveGroup('pledge', $post['pledge']);
        }

        // 6. Footer
        if (isset($post['footer'])) {
            $this->settingService->saveGroup('footer', $post['footer']);
        }

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.profile.updated', 'site_settings', 'institution_profile', null, ['updated' => true]);

        return redirect()->to('dashboard/landing/profil-lembaga')->with('success', 'Profil lembaga, visi-misi, dan filosofi berhasil diperbarui.');
    }
}
