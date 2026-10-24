<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;
use App\Services\SystemSettingService;
use CodeIgniter\Database\ConnectionInterface;

class InstitutionProfileController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->auditService   = new AuditService();
        $this->settingService = new SystemSettingService();
        $this->db             = \Config\Database::connect();
    }

    public function index()
    {
        $all = $this->settingService->getAll();

        $org           = $all['organization'] ?? [];
        $social        = $all['social'] ?? [];
        $visionMission = $all['vision_mission'] ?? [];
        $philosophy    = $all['philosophy'] ?? [];
        $pledge        = $all['pledge'] ?? [];
        $footer        = $all['footer'] ?? [];

        // Calculate completeness metrics
        $keyFields = [
            'agency_name'        => ! empty($org['agency_name']),
            'phone'              => ! empty($org['phone']),
            'email'              => ! empty($org['email']),
            'address'            => ! empty($org['address']),
            'logo_url'           => ! empty($org['logo_url']),
            'seal_url'           => ! empty($org['seal_url']),
            'vision_statement'   => ! empty($visionMission['vision_statement']),
            'mission_items'      => ! empty($visionMission['mission_items']),
            'philosophy_p1'      => ! empty($philosophy['philosophy_p1']),
            'maklumat_pelayanan' => ! empty($pledge['maklumat_pelayanan']),
        ];

        $completedCount = count(array_filter($keyFields));
        $totalCheck     = count($keyFields);
        $completionPct  = round(($completedCount / $totalCheck) * 100);

        // Social channels connected
        $socialChannels = array_filter([
            'facebook' => ! empty($social['facebook_url']),
            'instagram' => ! empty($social['instagram_url']),
            'youtube'   => ! empty($social['youtube_url']),
            'linkedin'  => ! empty($social['linkedin_url']),
            'twitter'   => ! empty($social['twitter_url']),
            'tiktok'    => ! empty($social['tiktok_url']),
        ]);

        // Mission points count
        $missionRaw = $visionMission['mission_items'] ?? '[]';
        $missions   = json_decode((string) $missionRaw, true) ?: [];
        if (empty($missions) && ! empty($missionRaw) && is_string($missionRaw)) {
            $missions = array_filter(array_map('trim', explode("\n", $missionRaw)));
        }

        // Last updated timestamp from audit log or site_settings
        $lastLog = $this->db->table('audit_logs')
            ->where('action', 'landing.profile.updated')
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();
        $lastUpdated = $lastLog['created_at'] ?? null;

        $stats = [
            'completion_pct'   => $completionPct,
            'completed_count'  => $completedCount,
            'total_check'      => $totalCheck,
            'social_count'     => count($socialChannels),
            'mission_count'    => count($missions),
            'last_updated'     => $lastUpdated,
        ];

        return view('dashboard/landing/profile/index', [
            'title'         => 'Profil Lembaga & Visi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Profil Lembaga, Visi Misi & Filosofi',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'profil-lembaga',
            'general'       => $all['general'] ?? [],
            'organization'  => $org,
            'social'        => $social,
            'visionMission' => $visionMission,
            'philosophy'    => $philosophy,
            'pledge'        => $pledge,
            'footer'        => $footer,
            'stats'         => $stats,
        ]);
    }

    public function save()
    {
        $post = $this->request->getPost();

        // 1. Normalize flat inputs if sent directly without grouping (for backward compatibility & API tests)
        if (! isset($post['organization']) && (isset($post['agency_name']) || isset($post['phone']) || isset($post['address']))) {
            $post['organization'] = [
                'agency_name'        => $post['agency_name'] ?? '',
                'agency_tagline'     => $post['agency_tagline'] ?? '',
                'address'            => $post['address'] ?? '',
                'phone'              => $post['phone'] ?? '',
                'email'              => $post['email'] ?? '',
                'provincial_unit'    => $post['provincial_unit'] ?? '',
                'institution_level'  => $post['institution_level'] ?? '',
                'institution_zone'   => $post['institution_zone'] ?? '',
                'operational_hours'  => $post['operational_hours'] ?? '',
                'google_maps_url'    => $post['google_maps_url'] ?? '',
                'welcome_lead'       => $post['welcome_lead'] ?? '',
                'motto_title'        => $post['motto_title'] ?? '',
                'motto_desc'         => $post['motto_desc'] ?? '',
                'vision'             => $post['vision'] ?? '',
                'mission'            => $post['mission'] ?? '',
            ];
        }

        // 2. Organization & Identity (including Logo & Seal Uploads)
        if (isset($post['organization'])) {
            $org = $post['organization'];

            // Handle Logo File Upload
            $logoFile     = $this->request->getFile('logo_file');
            $currLogo     = $this->settingService->get('organization', 'logo_url', 'assets/main/images/resources/logo-1.png');
            $manualLogo   = trim((string) ($org['logo_url'] ?? ''));
            $uploadedLogo = LandingImageUploadService::upload($logoFile, 'profile', $manualLogo ?: $currLogo, 'institution_logo');
            if ($uploadedLogo) {
                $org['logo_url'] = $uploadedLogo;
            } elseif (! empty($manualLogo)) {
                $org['logo_url'] = $manualLogo;
            } else {
                $org['logo_url'] = $currLogo;
            }

            // Handle Official Seal / Cap Lembaga File Upload
            $sealFile     = $this->request->getFile('seal_file');
            $currSeal     = $this->settingService->get('organization', 'seal_url', 'assets/main/images/resources/seal-prov-lampung.png');
            $manualSeal   = trim((string) ($org['seal_url'] ?? ''));
            $uploadedSeal = LandingImageUploadService::upload($sealFile, 'profile', $manualSeal ?: $currSeal, 'institution_seal');
            if ($uploadedSeal) {
                $org['seal_url'] = $uploadedSeal;
            } elseif (! empty($manualSeal)) {
                $org['seal_url'] = $manualSeal;
            } else {
                $org['seal_url'] = $currSeal;
            }

            // Cross-sync vision, philosophy and pledge if passed in sub-forms
            if (isset($post['vision_mission']['vision_statement']) && ! empty($post['vision_mission']['vision_statement'])) {
                $org['vision'] = $post['vision_mission']['vision_statement'];
            }
            if (isset($post['philosophy']['philosophy_p1']) && ! empty($post['philosophy']['philosophy_p1'])) {
                $p1 = $post['philosophy']['philosophy_p1'];
                $p2 = $post['philosophy']['philosophy_p2'] ?? '';
                $org['philosophy'] = trim($p1 . "\n\n" . $p2);
            }
            if (isset($post['pledge']['maklumat_pelayanan']) && ! empty($post['pledge']['maklumat_pelayanan'])) {
                $org['service_pledge'] = $post['pledge']['maklumat_pelayanan'];
            }

            $this->settingService->saveGroup('organization', $org);
        }

        // 3. Social Links
        if (isset($post['social'])) {
            $this->settingService->saveGroup('social', $post['social']);
        }

        // 4. Vision & Mission
        if (isset($post['vision_mission'])) {
            $vm = $post['vision_mission'];
            if (isset($vm['mission_items_raw'])) {
                $lines = array_filter(array_map('trim', explode("\n", (string) $vm['mission_items_raw'])));
                $vm['mission_items'] = json_encode(array_values($lines), JSON_UNESCAPED_UNICODE);
                unset($vm['mission_items_raw']);
            }
            $this->settingService->saveGroup('vision_mission', $vm);
        }

        // 5. Philosophy
        if (isset($post['philosophy'])) {
            $this->settingService->saveGroup('philosophy', $post['philosophy']);
        }

        // 6. Pledge
        if (isset($post['pledge'])) {
            $this->settingService->saveGroup('pledge', $post['pledge']);
        }

        // 7. Footer
        if (isset($post['footer'])) {
            $this->settingService->saveGroup('footer', $post['footer']);
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.profile.updated', 'site_settings', 'institution_profile', null, ['updated' => true]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profil lembaga, visi-misi, dan filosofi berhasil diperbarui.',
            ]);
        }

        return redirect()->to('dashboard/landing/profil-lembaga')
            ->with('success', 'Profil lembaga, visi-misi, dan filosofi berhasil diperbarui.');
    }

    /**
     * Restore canonical standard profiles for Biro PBJ Setda Provinsi Lampung
     */
    public function resetDefault()
    {
        $defaults = [
            'organization' => [
                'agency_name'        => 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung',
                'agency_code'        => 'PEMPROV-LPG',
                'address'            => 'Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35211',
                'phone'              => '+62 851-1777-9783',
                'email'              => 'biropbj@lampungprov.go.id',
                'provincial_unit'    => 'Pemerintah Provinsi Lampung',
                'operational_hours'  => 'Senin - Jumat, 08.00 - 16.00 WIB',
                'institution_level'  => 'UKPBJ Level Proaktif (Tingkat Kematangan Proaktif)',
                'institution_zone'   => 'Zona Integritas Wilayah Bebas Korupsi & Gratifikasi',
                'google_maps_url'    => 'https://maps.google.com/?q=Biro+Pengadaan+Barang+dan+Jasa+Provinsi+Lampung',
                'logo_url'           => 'assets/main/images/resources/logo-1.png',
                'seal_url'           => 'assets/main/images/resources/seal-prov-lampung.png',
                'vision'             => 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.',
                'service_pledge'     => '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."',
            ],
            'social' => [
                'facebook_url'  => 'https://facebook.com/biropbjlampung',
                'instagram_url' => 'https://instagram.com/biropbj_lampung',
                'youtube_url'   => 'https://youtube.com/@biropbjsetdaprovinsilampung',
                'linkedin_url'  => 'https://linkedin.com/company/biropbj-lampung',
            ],
            'footer' => [
                'copyright_text' => 'Copyright © {year} BegawiPengadaan. Pemerintah Provinsi Lampung.',
                'subtext'        => 'Pengadaan kompeten, transparan, dan berdampak.',
            ],
            'vision_mission' => [
                'vision_title'     => 'Visi Utama 2024–2029',
                'vision_statement' => 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.',
                'vision_focus'     => 'Fokus: Kematangan Proaktif & E-Katalog Lokal',
                'mission_items'    => "[\"Peningkatan Kompetensi Berkelanjutan: Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.\",\"Penguatan Tata Kelola & Advokasi: Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.\",\"Akselerasi E-Purchasing & UMKM: Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).\",\"Pusat Literasi & Regulasi Terbuka: Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.\",\"Government Intelligence PBJ: Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.\"]",
            ],
            'philosophy' => [
                'philosophy_title' => 'Makna "Begawi" dalam Pengadaan Modern',
                'philosophy_tag'   => 'Filosofi Daerah',
                'philosophy_p1'    => 'Kata "Begawi" berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.',
                'philosophy_p2'    => 'Dalam konteks pengadaan barang dan jasa modern, BegawiPengadaan hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai.',
                'philosophy_quote' => '"Pengadaan Barang dan Jasa bukan sekadar membelanjakan anggaran negara, melainkan instrumen strategis pembangunan yang menggerakkan ekonomi lokal dan mensejahterakan rakyat."',
            ],
            'pledge' => [
                'pledge_title'        => 'Janji Integritas Pelayanan UKPBJ Lampung',
                'pledge_tag'          => 'Maklumat Pelayanan Publik',
                'maklumat_pelayanan'  => '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."',
                'point_1'             => 'Layanan Cepat 1x24 Jam',
                'point_2'             => 'Bebas Biaya (Tanpa Pungli)',
                'point_3'             => 'Perlindungan Kerahasiaan Pemohon',
            ],
        ];

        foreach ($defaults as $grp => $vals) {
            $this->settingService->saveGroup($grp, $vals);
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.profile.reset_defaults', 'site_settings', 'institution_profile', null, ['reset' => true]);

        return redirect()->to('dashboard/landing/profil-lembaga')
            ->with('success', 'Pengaturan profil lembaga berhasil dipulihkan ke standar resmi Biro PBJ Lampung.');
    }
}
