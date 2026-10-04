<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Services\Identity\PasswordResetService;

/**
 * Forgot/Reset password — unified Auth boundary.
 *
 * Per CLAUDE.md §9 + registration-policy: generic responses; never disclose
 * whether an email is registered. Password policy: 12+ chars, uppercase,
 * lowercase, digit, symbol.
 */
class PasswordController extends BaseController
{
    public function forgotForm()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('dashboard/auth/forgot_password', [
            'title' => 'Lupa Kata Sandi | BegawiPBJ',
            'pageTitle' => 'Lupa Kata Sandi',
            'pageDescription' => 'Masukkan alamat email akun Anda untuk menerima instruksi pengaturan ulang kata sandi.',
        ]);
    }

    public function sendReset()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Alamat email tidak valid.');
        }

        // Generic path — no account-existence disclosure.
        (new PasswordResetService())->requestReset($email);

        return redirect()->back()
            ->with('message', 'Jika email terdaftar, instruksi pengaturan ulang akan diproses.');
    }

    public function resetForm(string $token)
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $row = (new PasswordResetService())->findValidToken($token);
        if (! $row) {
            return redirect()->to(site_url('dashboard/lupa-kata-sandi'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan ajukan ulang.');
        }

        return view('dashboard/auth/reset_password', [
            'title' => 'Reset Kata Sandi | BegawiPBJ',
            'token' => $token,
        ]);
    }

    public function updatePassword(string $token)
    {
        $rules = [
            'password'              => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors());
        }

        $newPassword = (string) $this->request->getPost('password');

        $ok = (new PasswordResetService())->resetPassword($token, $newPassword);

        if (! $ok) {
            return redirect()->to(site_url('dashboard/lupa-kata-sandi'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        return redirect()->to(site_url('dashboard/masuk'))
            ->with('message', 'Kata sandi berhasil diubah. Silakan masuk.');
    }
}