<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;

class PasswordController extends BaseController
{
    public function forgotForm(): string
    {
        return view('dashboard/auth/forgot_password', [
            'title' => 'Lupa Kata Sandi | BegawiPBJ',
            'pageTitle' => 'Lupa Kata Sandi',
        ]);
    }

    public function resetForm(string $token): string
    {
        return view('dashboard/auth/reset_password', [
            'title' => 'Reset Kata Sandi | BegawiPBJ',
            'token' => $token,
        ]);
    }

    public function sendReset()
    {
        return redirect()->back()->with('message', 'Jika email terdaftar, instruksi reset akan dikirim.');
    }

    public function updatePassword(string $token)
    {
        return redirect()->to(route_to('dashboard.auth.login'))->with('message', 'Kata sandi berhasil diperbarui.');
    }
}
