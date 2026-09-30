<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class BookmarkController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/bookmarks/index', [
            'title' => 'Bookmark Bunga Rampai | Dashboard BegawiPBJ',
            'pageTitle' => 'Bookmark Bunga Rampai',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => null,
        ]);
    }
}
