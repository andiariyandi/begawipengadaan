<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class ModuleController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/modules/index', [
            'title' => 'Materi dan Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Materi dan Modul',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/modules/form', [
            'title' => 'Tambah Materi atau Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Materi atau Modul',
            'formMode' => 'create',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/modules/detail', [
            'title' => 'Detail Materi atau Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Materi atau Modul',
            'moduleId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }
}
