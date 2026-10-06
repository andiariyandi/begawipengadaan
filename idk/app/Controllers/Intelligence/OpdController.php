<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;

class OpdController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/intelligence/opd/index', [
            'title' => 'OPD dan Unit | Dashboard BegawiPBJ',
            'pageTitle' => 'OPD dan Unit',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'organization-units',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/intelligence/opd/form', [
            'title' => 'Tambah OPD atau Unit | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah OPD atau Unit',
            'formMode' => 'create',
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'organization-units',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/intelligence/opd/detail', [
            'title' => 'Detail OPD atau Unit | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail OPD atau Unit',
            'opdId' => $id,
            'activeMenu' => 'intelligence',
            'activeSubmenu' => 'organization-units',
        ]);
    }
}