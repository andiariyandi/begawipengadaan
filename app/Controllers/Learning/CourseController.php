<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class CourseController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/courses/index', [
            'title' => 'Course | Dashboard BegawiPBJ',
            'pageTitle' => 'Course',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/courses/form', [
            'title' => 'Tambah Course | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Course',
            'formMode' => 'create',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/courses/detail', [
            'title' => 'Detail Course | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Course',
            'courseId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/learning/courses/form', [
            'title' => 'Ubah Course | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Course',
            'formMode' => 'edit',
            'courseId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }
}
