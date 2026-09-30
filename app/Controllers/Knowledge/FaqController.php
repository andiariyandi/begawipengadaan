<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class FaqController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/faqs/index', [
            'title' => 'FAQ | Dashboard BegawiPBJ',
            'pageTitle' => 'FAQ',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'faqs',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/faqs/form', [
            'title' => 'Tambah FAQ | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah FAQ',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'faqs',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/faqs/detail', [
            'title' => 'Detail FAQ | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail FAQ',
            'faqId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'faqs',
        ]);
    }
}
