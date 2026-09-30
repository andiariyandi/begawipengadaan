<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class CaseStudyController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/case_studies/index', [
            'title' => 'Studi Kasus | Dashboard BegawiPBJ',
            'pageTitle' => 'Studi Kasus',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'case-studies',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/case_studies/form', [
            'title' => 'Tambah Studi Kasus | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Studi Kasus',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'case-studies',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/case_studies/detail', [
            'title' => 'Detail Studi Kasus | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Studi Kasus',
            'caseStudyId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'case-studies',
        ]);
    }
}
