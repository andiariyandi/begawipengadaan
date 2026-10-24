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

    public function create()
    {
        $req = $this->request ?? service('request');
        $db  = db_connect();

        if (strtoupper($req->getMethod()) === 'POST') {
            $nameSnapshot = trim((string) $req->getPost('name_snapshot'));
            $nip          = trim((string) $req->getPost('employee_identifier'));
            $opdId        = (int) $req->getPost('organization_unit_id');
            $levelId      = (int) $req->getPost('level_id');
            $userId       = (int) $req->getPost('user_id') ?: null;
            $effectiveAt  = trim((string) $req->getPost('effective_at')) ?: date('Y-m-d');
            $changeType   = trim((string) $req->getPost('change_type')) ?: 'addition';
            $status       = trim((string) $req->getPost('status')) ?: 'active';

            if ($nameSnapshot === '' || $nip === '' || $opdId < 1 || $levelId < 1) {
                return redirect()->back()->withInput()
                    ->with('error', 'Nama personel, NIP, OPD penugasan, dan jenjang JF PPBJ wajib diisi.');
            }

            $now     = date('Y-m-d H:i:s');
            $actorId = (int) (session()->get('user_id') ?: 13);

            $db->table('jf_ppbj_records')->insert([
                'user_id'              => $userId,
                'organization_unit_id' => $opdId,
                'employee_identifier'  => $nip,
                'name_snapshot'        => $nameSnapshot,
                'level_id'             => $levelId,
                'status'               => $status,
                'effective_at'         => date('Y-m-d H:i:s', strtotime($effectiveAt)),
                'change_type'          => $changeType,
                'created_by'           => $actorId,
                'updated_by'           => $actorId,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);

            return redirect()->to(site_url('dashboard/intelligence/sdm-jf-ppbj'))
                ->with('message', 'Data personel JF PPBJ ' . esc($nameSnapshot) . ' berhasil ditambahkan.');
        }

        $levels   = $this->analyticsService->getLevelsList();
        $opds     = $this->analyticsService->getOpdList();
        $asnUsers = $db->table('users')
            ->select('id, name, email')
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard/intelligence/personnel/form', [
            'title'         => 'Tambah Personel JF PPBJ | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Personel SDM JF PPBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'personnel',
            'levels'        => $levels,
            'opds'          => $opds,
            'asnUsers'      => $asnUsers,
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
