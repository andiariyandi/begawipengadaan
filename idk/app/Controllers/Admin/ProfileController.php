<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function index(): string
    {
        return $this->profileView(
            'Profil Saya',
            'profile',
            'index'
        );
    }

    public function edit(): string
    {
        return $this->profileView(
            'Ubah Profil',
            'profile',
            'edit'
        );
    }

    public function password(): string
    {
        return $this->profileView(
            'Ubah Kata Sandi',
            'password',
            'password'
        );
    }

    public function preferences(): string
    {
        return $this->profileView(
            'Preferensi Akun',
            'preferences',
            'preferences'
        );
    }

    private function profileView(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName
    ): string {
        return view('dashboard/profile/' . $viewName, [
            'title' => $pageTitle . ' | BegawiPBJ',
            'pageTitle' => $pageTitle,
            'activeMenu' => 'profile',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs' => [
                [
                    'label' => 'Dashboard',
                    'url' => site_url('dashboard'),
                ],
                ['label' => 'Akun Saya'],
                ['label' => $pageTitle],
            ],
        ]);
    }
}
