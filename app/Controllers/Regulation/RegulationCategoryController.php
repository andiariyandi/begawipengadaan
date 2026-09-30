<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;

class RegulationCategoryController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/regulation/categories/index', [
            'title' => 'Kategori Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Kategori Regulasi',
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'categories',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/regulation/categories/form', [
            'title' => 'Tambah Kategori Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Kategori Regulasi',
            'formMode' => 'create',
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'categories',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/regulation/categories/detail', [
            'title' => 'Detail Kategori Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Kategori Regulasi',
            'categoryId' => $id,
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'categories',
        ]);
    }
}
