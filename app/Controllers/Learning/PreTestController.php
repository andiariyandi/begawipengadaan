<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class PreTestController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/assessments/index', [
            'title' => 'Pre-Test | Dashboard BegawiPengadaan',
            'pageTitle' => 'Pre-Test',
            'assessmentType' => 'Pre-Test',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}
