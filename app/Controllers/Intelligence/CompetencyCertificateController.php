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

    public function create()
    {
        $req = $this->request ?? service('request');
        $db  = db_connect();

        if (strtoupper($req->getMethod()) === 'POST') {
            $recordId   = (int) $req->getPost('jf_ppbj_record_id');
            $typeId     = (int) $req->getPost('certificate_type_id');
            $certNumber = trim((string) $req->getPost('certificate_number'));
            $issuedAt   = trim((string) $req->getPost('issued_at')) ?: date('Y-m-d');
            $expiresAt  = trim((string) $req->getPost('expires_at')) ?: date('Y-m-d', strtotime('+3 years'));
            $status     = trim((string) $req->getPost('status')) ?: 'verified';

            if ($recordId < 1 || $typeId < 1 || $certNumber === '') {
                return redirect()->back()->withInput()
                    ->with('error', 'Personel JF PPBJ, jenis sertifikat kompetensi, dan nomor sertifikat wajib diisi.');
            }

            $now     = date('Y-m-d H:i:s');
            $actorId = (int) (session()->get('user_id') ?: 13);

            $db->table('competency_certificates')->insert([
                'jf_ppbj_record_id'   => $recordId,
                'certificate_type_id' => $typeId,
                'certificate_number'  => $certNumber,
                'issued_at'           => date('Y-m-d H:i:s', strtotime($issuedAt)),
                'expires_at'          => date('Y-m-d H:i:s', strtotime($expiresAt)),
                'status'              => $status,
                'verified_by'         => $actorId,
                'verified_at'         => $now,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            return redirect()->to(site_url('dashboard/intelligence/sertifikat-kompetensi'))
                ->with('message', 'Sertifikat kompetensi ' . esc($certNumber) . ' berhasil dicatat.');
        }

        $personnelList = $this->analyticsService->getPersonnelList();
        $types         = $this->analyticsService->getCertificateTypesList();

        return view('dashboard/intelligence/competency_certificates/form', [
            'title'         => 'Catat Sertifikat Kompetensi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Catat Sertifikat Kompetensi PBJ',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'competency-certificates',
            'personnelList' => $personnelList,
            'types'         => $types,
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
