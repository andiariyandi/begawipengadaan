<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;

class RegulationRelationController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/regulation/relations/index', [
            'title' => 'Relasi Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Relasi Regulasi',
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'relations',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/regulation/relations/detail', [
            'title' => 'Detail Relasi Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Relasi Regulasi',
            'relationId' => $id,
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'relations',
        ]);
    }
}
