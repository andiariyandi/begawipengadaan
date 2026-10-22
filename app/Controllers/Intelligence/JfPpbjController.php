<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;
use App\Services\Admin\IntelligenceAnalyticsService;

class JfPpbjController extends BaseController
{
    protected IntelligenceAnalyticsService $analyticsService;

    public function __construct(?IntelligenceAnalyticsService $service = null)
    {
        $this->analyticsService = $service ?? new IntelligenceAnalyticsService();
    }

    public function index(): string
    {
        $req = $this->request ?? service('request');
        $filters = [
            'level_id'             => $req->getGet('level_id'),
            'organization_unit_id' => $req->getGet('opd_id'),
            'status'               => $req->getGet('status'),
            'q'                    => trim((string) ($req->getGet('q') ?? '')),
        ];

        $personnelList = $this->analyticsService->getPersonnelList($filters);
        $levels        = $this->analyticsService->getLevelsList();
        $opds          = $this->analyticsService->getOpdList();

        return view('dashboard/intelligence/personnel/index', [
            'title'         => 'SDM JF PPBJ | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Data Personel SDM JF PPBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'personnel',
            'personnelList' => $personnelList,
            'levels'        => $levels,
            'opds'          => $opds,
            'filters'       => $filters,
        ]);
    }

    public function detail(string $id)
    {
        $personnel = $this->analyticsService->getPersonnelDetail((int) $id);

        if (! $personnel) {
            return redirect()->to(site_url('dashboard/intelligence/sdm-jf-ppbj'))
                ->with('error', 'Data personel JF PPBJ tidak ditemukan.');
        }

        return view('dashboard/intelligence/personnel/detail', [
            'title'         => 'Detail Personel ' . esc($personnel['name_snapshot']) . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Personel JF PPBJ',
            'personnelId'   => $id,
            'personnel'     => $personnel,
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'personnel',
        ]);
    }

    public function histories(): string
    {
        $histories = $this->analyticsService->getHistoriesList();

        return view('dashboard/intelligence/histories/index', [
            'title'         => 'Riwayat Penugasan JF PPBJ | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Riwayat Penugasan & Karir JF PPBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'personnel-histories',
            'histories'     => $histories,
        ]);
    }

    public function levels(): string
    {
        $levels = $this->analyticsService->getLevelsList();

        return view('dashboard/intelligence/levels/index', [
            'title'         => 'Jenjang Jabatan JF PPBJ | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Jenjang Jabatan JF PPBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'personnel-levels',
            'levels'        => $levels,
        ]);
    }
}
