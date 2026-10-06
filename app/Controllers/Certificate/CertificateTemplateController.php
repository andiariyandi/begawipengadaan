<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;

class CertificateTemplateController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/certificates/templates/index', [
            'title' => 'Template Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Template Sertifikat',
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'templates',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/certificates/templates/form', [
            'title' => 'Tambah Template Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Template Sertifikat',
            'formMode' => 'create',
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'templates',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/certificates/templates/detail', [
            'title' => 'Detail Template Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Template Sertifikat',
            'templateId' => $id,
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'templates',
        ]);
    }
}
