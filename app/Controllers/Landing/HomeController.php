<?php

declare(strict_types=1);

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        return view('landing/home', [
            'title' => 'BegawiPBJ | Ekosistem Digital PBJ Provinsi Lampung',
            'activeMenu' => 'home',
        ]);
    }
}