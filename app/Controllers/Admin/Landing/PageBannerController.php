<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;

class PageBannerController extends BaseController
{
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $banners = $this->db->table('page_banners')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $pageNames = [
            'about'              => 'Tentang Kami',
            'learning'           => 'Arena Belajar',
            'knowledge'          => 'Bunga Rampai',
            'regulation'         => 'Regulasi PBJ',
            'news'               => 'Berita & Warta',
            'events'             => 'Event & Agenda',
            'statistics'         => 'Statistik & Metrik',
            'certificate_verify' => 'Verifikasi Sertifikat',
            'contact'            => 'Kontak & Helpdesk',
            'survey'             => 'Survei Kepuasan (IKM)',
        ];

        return view('dashboard/landing/banner/index', [
            'title'         => 'Kelola Banner Halaman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Banner Hero & Closing CTA Halaman',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'banner-halaman',
            'banners'       => $banners,
            'pageNames'     => $pageNames,
        ]);
    }

    public function edit($pageKey)
    {
        $banner = $this->db->table('page_banners')->where('page_key', $pageKey)->get()->getRowArray();
        if (! $banner) {
            return redirect()->to('dashboard/landing/banner-halaman')->with('error', 'Halaman tidak ditemukan.');
        }

        $pageNames = [
            'about'              => 'Tentang Kami (/tentang)',
            'learning'           => 'Arena Belajar (/arena-belajar)',
            'knowledge'          => 'Bunga Rampai (/bunga-rampai)',
            'regulation'         => 'Regulasi PBJ (/regulasi)',
            'news'               => 'Berita & Warta (/berita)',
            'events'             => 'Event & Agenda (/event)',
            'statistics'         => 'Statistik & Metrik (/statistik)',
            'certificate_verify' => 'Verifikasi Sertifikat (/verifikasi-sertifikat)',
            'contact'            => 'Kontak & Helpdesk (/kontak)',
            'survey'             => 'Survei Kepuasan (/survei-kepuasan)',
        ];

        return view('dashboard/landing/banner/form', [
            'title'         => 'Ubah Banner ' . ($pageNames[$pageKey] ?? $pageKey) . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Banner: ' . ($pageNames[$pageKey] ?? $pageKey),
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'banner-halaman',
            'banner'        => $banner,
            'pageName'      => $pageNames[$pageKey] ?? $pageKey,
        ]);
    }

    public function update($pageKey)
    {
        $banner = $this->db->table('page_banners')->where('page_key', $pageKey)->get()->getRowArray();
        if (! $banner) {
            return redirect()->to('dashboard/landing/banner-halaman')->with('error', 'Halaman tidak ditemukan.');
        }

        $heroBg = trim((string) $this->request->getPost('hero_bg_image'));
        $heroBgFile = $this->request->getFile('hero_bg_file');
        $currentBg = $banner['hero_bg_image'] ?? '';
        $uploadedHeroBg = LandingImageUploadService::upload($heroBgFile, 'banners', $heroBg ?: $currentBg, 'page_banner');
        if ($uploadedHeroBg) {
            $heroBg = $uploadedHeroBg;
        }

        $data = [
            'hero_badge'             => trim((string) $this->request->getPost('hero_badge')),
            'hero_title'             => trim((string) $this->request->getPost('hero_title')),
            'hero_subtitle'          => trim((string) $this->request->getPost('hero_subtitle')),
            'hero_bg_image'          => $heroBg,
            'search_placeholder'     => trim((string) $this->request->getPost('search_placeholder')),
            'cta_badge'              => trim((string) $this->request->getPost('cta_badge')),
            'cta_title'              => trim((string) $this->request->getPost('cta_title')),
            'cta_lead'               => trim((string) $this->request->getPost('cta_lead')),
            'cta_primary_btn_text'   => trim((string) $this->request->getPost('cta_primary_btn_text')),
            'cta_primary_btn_url'    => trim((string) $this->request->getPost('cta_primary_btn_url')),
            'cta_secondary_btn_text' => trim((string) $this->request->getPost('cta_secondary_btn_text')),
            'cta_secondary_btn_url'  => trim((string) $this->request->getPost('cta_secondary_btn_url')),
            'updated_at'             => date('Y-m-d H:i:s'),
        ];

        $this->db->table('page_banners')->where('page_key', $pageKey)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.banner.updated', 'landing', 'page_banners', null, ['page_key' => $pageKey, 'before' => $banner, 'after' => $data]);

        return redirect()->to('dashboard/landing/banner-halaman')->with('success', 'Banner halaman berhasil diperbarui.');
    }
}
