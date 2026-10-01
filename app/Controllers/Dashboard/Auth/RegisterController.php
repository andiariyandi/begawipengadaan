<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;

class RegisterController extends BaseController
{
    public function account(): string
    {
        return view('dashboard/auth/register', [
            'title' => 'Daftar | BegawiPBJ',
            'pageTitle' => 'Buat Akun BegawiPBJ',
            'currentStep' => 1,
            'currentKey' => 'akun',
        ]);
    }

    public function region(): string
    {
        return view('dashboard/auth/register_address', [
            'title' => 'Wilayah Pendaftaran | BegawiPBJ',
            'pageTitle' => 'Wilayah dan Alamat Ringkas',
            'currentStep' => 2,
            'currentKey' => 'wilayah',
            'provinces' => [],
        ]);
    }

    public function review(): string
    {
        return view('dashboard/auth/register_review', [
            'title' => 'Tinjau Pendaftaran | BegawiPBJ',
            'pageTitle' => 'Tinjau Data Pendaftaran',
            'currentStep' => 4,
            'currentKey' => 'tinjau',
        ]);
    }

    public function success(): string
    {
        return view('dashboard/auth/register_success', [
            'title' => 'Pendaftaran Berhasil | BegawiPBJ',
            'pageTitle' => 'Pendaftaran Berhasil Dikirim',
        ]);
    }

    public function storeAccount()
    {
        return redirect()->to(route_to('dashboard.auth.register.region'));
    }

    public function storeRegion()
    {
        return redirect()->to(route_to('dashboard.auth.register.profile', 'public'));
    }

    public function submit()
    {
        return redirect()->to(route_to('dashboard.auth.register.success'));
    }
}
