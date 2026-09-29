<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class KnowledgeMediaController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/media/index', [
            'title' => 'Media dan Galeri | Dashboard BegawiPBJ',
            'pageTitle' => 'Media dan Galeri',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/media/form', [
            'title' => 'Tambah Media | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Media',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/media/detail', [
            'title' => 'Detail Media | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Media',
            'mediaId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'media-gallery',
        ]);
    }
}
