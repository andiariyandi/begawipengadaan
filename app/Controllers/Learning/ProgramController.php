<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class ProgramController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/programs/index', [
            'title' => 'Program | Dashboard BegawiPBJ',
            'pageTitle' => 'Program',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/programs/form', [
            'title' => 'Tambah Program | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Program',
            'formMode' => 'create',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/programs/detail', [
            'title' => 'Detail Program | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Program',
            'programId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/learning/programs/form', [
            'title' => 'Ubah Program | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Program',
            'formMode' => 'edit',
            'programId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }
}
