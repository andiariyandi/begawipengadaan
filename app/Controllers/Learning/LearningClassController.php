<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class LearningClassController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/classes/index', [
            'title' => 'Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Kelas',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/classes/form', [
            'title' => 'Tambah Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Kelas',
            'formMode' => 'create',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/classes/detail', [
            'title' => 'Detail Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Kelas',
            'classId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/learning/classes/form', [
            'title' => 'Ubah Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Kelas',
            'formMode' => 'edit',
            'classId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }
}
