<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class KnowledgeMediaItemController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/media/items/index', [
            'title' => 'Item Media | Dashboard BegawiPBJ',
            'pageTitle' => 'Item Media',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/media/items/form', [
            'title' => 'Tambah Item Media | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Item Media',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/media/items/detail', [
            'title' => 'Detail Item Media | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Item Media',
            'mediaItemId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }
}
