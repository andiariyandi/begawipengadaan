<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;

class ArticleController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/knowledge/articles/index', [
            'title' => 'Artikel | Dashboard BegawiPBJ',
            'pageTitle' => 'Artikel',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'articles',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/articles/form', [
            'title' => 'Tambah Artikel | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Artikel',
            'formMode' => 'create',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'articles',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/knowledge/articles/detail', [
            'title' => 'Detail Artikel | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Artikel',
            'articleId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'articles',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/knowledge/articles/form', [
            'title' => 'Ubah Artikel | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Artikel',
            'formMode' => 'edit',
            'articleId' => $id,
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'articles',
        ]);
    }

    public function categories(): string
    {
        return view('dashboard/knowledge/categories/index', [
            'title' => 'Kategori Bunga Rampai | Dashboard BegawiPBJ',
            'pageTitle' => 'Kategori',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'categories',
        ]);
    }

    public function tags(): string
    {
        return view('dashboard/knowledge/tags/index', [
            'title' => 'Tag Bunga Rampai | Dashboard BegawiPBJ',
            'pageTitle' => 'Tag',
            'activeMenu' => 'knowledge',
            'activeSubmenu' => 'tags',
        ]);
    }
}
