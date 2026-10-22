<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;
use App\Services\Admin\IntelligenceAnalyticsService;

class CompetencyCertificateController extends BaseController
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
            'type_id' => $req->getGet('type_id'),
            'status'  => $req->getGet('status'),
            'q'       => trim((string) ($req->getGet('q') ?? '')),
        ];

        $certificates = $this->analyticsService->getCompetencyCertificates($filters);
        $types        = $this->analyticsService->getCertificateTypesList();

        return view('dashboard/intelligence/competency_certificates/index', [
            'title'         => 'Sertifikat Kompetensi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Direktori Sertifikat Kompetensi PBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'competency-certificates',
            'certificates'  => $certificates,
            'types'         => $types,
            'filters'       => $filters,
        ]);
    }

    public function detail(string $id)
    {
        $cert = db_connect()->table('competency_certificates cc')
            ->select([
                'cc.*',
                't.name AS type_name',
                't.description AS type_description',
                'r.name_snapshot AS owner_name',
                'r.employee_identifier AS owner_nip',
                'ou.name AS organization_unit_name',
                'u.email AS owner_email',
                'v.name AS verified_by_name',
            ])
            ->join('competency_certificate_types t', 't.id = cc.certificate_type_id', 'left')
            ->join('jf_ppbj_records r', 'r.id = cc.jf_ppbj_record_id', 'left')
            ->join('organization_units ou', 'ou.id = r.organization_unit_id', 'left')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('users v', 'v.id = cc.verified_by', 'left')
            ->where('cc.id', (int) $id)
            ->get()
            ->getRowArray();

        if (! $cert) {
            return redirect()->to(site_url('dashboard/intelligence/sertifikat-kompetensi'))
                ->with('error', 'Sertifikat kompetensi tidak ditemukan.');
        }

        return view('dashboard/intelligence/competency_certificates/detail', [
            'title'                   => 'Detail Sertifikat #' . esc($cert['certificate_number']) . ' | Dashboard BegawiPengadaan',
            'pageTitle'               => 'Detail Sertifikat Kompetensi',
            'competencyCertificateId' => $id,
            'certificate'             => $cert,
            'activeMenu'              => 'intelligence',
            'activeSubmenu'           => 'competency-certificates',
        ]);
    }

    public function types(): string
    {
        $types = $this->analyticsService->getCertificateTypesList();

        return view('dashboard/intelligence/competency_certificate_types/index', [
            'title'         => 'Standar & Jenis Sertifikat Kompetensi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Standar Jenis Sertifikat Kompetensi PBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'competency-certificate-types',
            'types'         => $types,
        ]);
    }
}
