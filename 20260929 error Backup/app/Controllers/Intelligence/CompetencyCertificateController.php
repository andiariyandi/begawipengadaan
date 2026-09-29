<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;

class CompetencyCertificateController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/intelligence/competency_certificates/index', [
            'title' => 'Sertifikat Kompetensi | Dashboard BegawiPBJ',
            'pageTitle' => 'Sertifikat Kompetensi',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'competency-certificates',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/intelligence/competency_certificates/detail', [
            'title' => 'Detail Sertifikat Kompetensi | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Sertifikat Kompetensi',
            'competencyCertificateId' => $id,
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'competency-certificates',
        ]);
    }

    public function types(): string
    {
        return view('dashboard/intelligence/competency_certificate_types/index', [
            'title' => 'Jenis Sertifikat Kompetensi | Dashboard BegawiPBJ',
            'pageTitle' => 'Jenis Sertifikat Kompetensi',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'competency-certificate-types',
        ]);
    }
}