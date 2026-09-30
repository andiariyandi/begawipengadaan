<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class DocumentTemplateController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/templates/index', [
            'title' => 'Template Dokumen | Dashboard BegawiPBJ',
            'pageTitle' => 'Template Dokumen',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'document-templates',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/templates/form', [
            'title' => 'Tambah Template Dokumen | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Template Dokumen',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'document-templates',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/templates/detail', [
            'title' => 'Detail Template Dokumen | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Template Dokumen',
            'templateId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'document-templates',
        ]);
    }
}
