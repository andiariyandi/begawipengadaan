<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class KnowledgeCarouselController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/carousel/index', [
            'title' => 'Carousel dan Konten Unggulan | Dashboard BegawiPBJ',
            'pageTitle' => 'Carousel dan Konten Unggulan',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'carousel',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/carousel/form', [
            'title' => 'Tambah Carousel | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Carousel',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'carousel',
        ]);
    }
}
