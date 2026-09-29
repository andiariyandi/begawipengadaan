<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminAuthController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/auth/login', [
            'title' => 'Masuk Dashboard | BegawiPBJ',
            'activeMenu' => null,
            'activeSubmenu' => null,
        ]);
    }

    public function forgotPassword(): string
    {
        return view('dashboard/auth/forgot_password', [
            'title' => 'Lupa Kata Sandi Dashboard | BegawiPBJ',
            'activeMenu' => null,
            'activeSubmenu' => null,
        ]);
    }

    public function logoutPreview(): string
    {
        return view('dashboard/auth/login', [
            'title' => 'Keluar Dashboard | BegawiPBJ',
            'activeMenu' => null,
            'activeSubmenu' => null,
            'flash' => [
                'type' => 'info',
                'message' => 'Pratinjau UI: proses keluar belum diaktifkan.',
            ],
        ]);
    }
}
