<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class LessonController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/lessons/index', [
            'title' => 'Lesson | Dashboard BegawiPBJ',
            'pageTitle' => 'Lesson',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/lessons/form', [
            'title' => 'Tambah Lesson | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Lesson',
            'formMode' => 'create',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/lessons/detail', [
            'title' => 'Detail Lesson | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Lesson',
            'lessonId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }
}
