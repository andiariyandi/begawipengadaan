<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;

class RegulationBookmarkController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/regulation/bookmarks/index', [
            'title' => 'Bookmark Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Bookmark Regulasi',
            'activeMenu' => 'regulations',
            'activeSubmenu' => null,
        ]);
    }
}
