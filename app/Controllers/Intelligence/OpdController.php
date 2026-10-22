<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;
use App\Services\Admin\IntelligenceAnalyticsService;

class OpdController extends BaseController
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
            'type' => $req->getGet('type'),
            'q'    => trim((string) ($req->getGet('q') ?? '')),
        ];

        $opds = $this->analyticsService->getOpdList($filters);

        return view('dashboard/intelligence/opd/index', [
            'title'         => 'OPD dan Unit Kerja | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pemetaan OPD dan Unit Kerja',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'organization-units',
            'opds'          => $opds,
            'filters'       => $filters,
        ]);
    }

    public function create()
    {
        $req = $this->request ?? service('request');

        if (strtoupper($req->getMethod()) === 'POST') {
            $code = trim((string) $req->getPost('code'));
            $name = trim((string) $req->getPost('name'));
            $type = trim((string) $req->getPost('type')) ?: 'Dinas';
            $parentId = (int) $req->getPost('parent_id') ?: null;

            if ($code !== '' && $name !== '') {
                $now = date('Y-m-d H:i:s');
                db_connect()->table('organization_units')->insert([
                    'code'       => strtoupper($code),
                    'name'       => $name,
                    'type'       => $type,
                    'parent_id'  => $parentId,
                    'is_active'  => 1,
                    'sort_order' => 99,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                return redirect()->to(site_url('dashboard/intelligence/opd-unit'))
                    ->with('message', 'Unit kerja ' . esc($name) . ' berhasil ditambahkan.');
            }
        }

        $parentUnits = db_connect()->table('organization_units')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard/intelligence/opd/form', [
            'title'         => 'Tambah OPD atau Unit | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah OPD atau Unit Kerja',
            'formMode'      => 'create',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'organization-units',
            'parentUnits'   => $parentUnits,
        ]);
    }

    public function detail(string $id)
    {
        $opd = $this->analyticsService->getOpdDetail((int) $id);

        if (! $opd) {
            return redirect()->to(site_url('dashboard/intelligence/opd-unit'))
                ->with('error', 'Unit organisasi tidak ditemukan.');
        }

        return view('dashboard/intelligence/opd/detail', [
            'title'         => 'Detail OPD ' . esc($opd['name']) . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail OPD / Unit Kerja',
            'opdId'         => $id,
            'opd'           => $opd,
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'organization-units',
        ]);
    }
}
