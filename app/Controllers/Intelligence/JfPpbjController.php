<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;

class JfPpbjController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/intelligence/personnel/index', [
            'title' => 'SDM JF PPBJ | Dashboard BegawiPBJ',
            'pageTitle' => 'SDM JF PPBJ',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'personnel',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/intelligence/personnel/detail', [
            'title' => 'Detail SDM JF PPBJ | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail SDM JF PPBJ',
            'personnelId' => $id,
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'personnel',
        ]);
    }

    public function histories(): string
    {
        return view('dashboard/intelligence/histories/index', [
            'title' => 'Riwayat JF PPBJ | Dashboard BegawiPBJ',
            'pageTitle' => 'Riwayat JF PPBJ',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'personnel-histories',
        ]);
    }

    public function levels(): string
    {
        return view('dashboard/intelligence/levels/index', [
            'title' => 'Jenjang JF PPBJ | Dashboard BegawiPBJ',
            'pageTitle' => 'Jenjang JF PPBJ',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'personnel-levels',
        ]);
    }
}