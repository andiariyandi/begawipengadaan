<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;

class NewsController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/communication/news/index', [
            'title' => 'Berita | Dashboard BegawiPBJ',
            'pageTitle' => 'Berita',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'news',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/communication/news/form', [
            'title' => 'Tambah Berita | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Berita',
            'formMode' => 'create',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'news',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/communication/news/detail', [
            'title' => 'Detail Berita | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Berita',
            'newsId' => $id,
            'activeMenu' => 'communication',
            'activeSubmenu' => 'news',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/communication/news/form', [
            'title' => 'Ubah Berita | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Berita',
            'formMode' => 'edit',
            'newsId' => $id,
            'activeMenu' => 'communication',
            'activeSubmenu' => 'news',
        ]);
    }

    public function categories(): string
    {
        return view('dashboard/communication/news_categories/index', [
            'title' => 'Kategori Berita | Dashboard BegawiPBJ',
            'pageTitle' => 'Kategori Berita',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'news-categories',
        ]);
    }
}
