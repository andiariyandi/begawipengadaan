<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class GradingController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/gradings/index', [
            'title' => 'Penilaian | Dashboard BegawiPBJ',
            'pageTitle' => 'Penilaian',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'gradings',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/gradings/detail', [
            'title' => 'Detail Penilaian | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Penilaian',
            'gradingId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'gradings',
        ]);
    }
}
