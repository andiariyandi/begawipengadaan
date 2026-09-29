<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/users/index', [
            'title' => 'Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Pengguna',
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna'],
            ],
        ]);
    }

    public function create(): string
    {
        return view('dashboard/users/form', [
            'title' => 'Tambah Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Pengguna',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Tambah Pengguna'],
            ],
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/users/detail', [
            'title' => 'Detail Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Pengguna',
            'userId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Detail'],
            ],
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/users/form', [
            'title' => 'Ubah Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Pengguna',
            'formMode' => 'edit',
            'userId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Ubah'],
            ],
        ]);
    }
}
