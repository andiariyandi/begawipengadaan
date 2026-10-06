<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;

class IntelligenceDashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/intelligence/home/index', [
            'title' => 'Dashboard Intelligence | Dashboard BegawiPBJ',
            'pageTitle' => 'Dashboard Intelligence',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'dashboard',
        ]);
    }

    public function competencyGap(): string
    {
        return view('dashboard/intelligence/competency_gap/index', [
            'title' => 'Gap Kompetensi | Dashboard BegawiPBJ',
            'pageTitle' => 'Gap Kompetensi',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'competency-gap',
        ]);
    }
}