<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class PostTestController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/assessments/index', [
            'title' => 'Post-Test | Dashboard BegawiPengadaan',
            'pageTitle' => 'Post-Test',
            'assessmentType' => 'Post-Test',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}
