<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;

class RegulationVersionController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/regulation/versions/index', [
            'title' => 'Versi Dokumen | Dashboard BegawiPBJ',
            'pageTitle' => 'Versi Dokumen',
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'versions',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/regulation/versions/detail', [
            'title' => 'Detail Versi Dokumen | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Versi Dokumen',
            'versionId' => $id,
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'versions',
        ]);
    }
}
