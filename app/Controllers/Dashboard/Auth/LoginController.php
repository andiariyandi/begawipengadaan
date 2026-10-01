<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Services\Identity\AuthenticationService;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('dashboard/auth/login', [
            'title' => 'Masuk | BegawiPBJ',
            'pageTitle' => 'Masuk ke Akun Anda',
            'pageDescription' => 'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPBJ.',
        ]);
    }

    public function attemptLogin()
    {
        if (! $this->validate([
            'email' => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = (new AuthenticationService())->authenticate(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('password')
        );

        if (! $result['ok']) {
            $message = $result['reason'] === 'inactive_account'
                ? 'Akun belum aktif atau sedang tidak dapat digunakan.'
                : 'Email atau kata sandi salah.';

            return redirect()->back()->withInput()->with('error', $message);
        }

        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        (new AuthenticationService())->logout();

        return redirect()->to(site_url('dashboard/masuk'))->with('message', 'Anda telah keluar dari akun.');
    }

    public function logoutPreview()
    {
        return $this->logout();
    }
}
