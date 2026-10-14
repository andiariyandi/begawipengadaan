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
            'title'           => 'Lupa Kata Sandi | BegawiPengadaan',
            'pageTitle'       => 'Lupa Kata Sandi Akun',
            'pageDescription' => 'Masukkan alamat email akun Anda untuk menerima tautan instruksi pemulihan kata sandi.',
        ]);
    }

    public function sendReset()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $rules = [
            'email' => 'required|valid_email|max_length[150]',
        ];

        $messages = [
            'email' => [
                'required'    => 'Silakan masukkan alamat email akun Anda.',
                'valid_email' => 'Format alamat email yang Anda masukkan tidak valid.',
            ],
        ];

        $postData = (array) ($this->request->getPost() ?: $_POST);

        if (! $this->validateData($postData, $rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) ($postData['email'] ?? '')));

        // Generic path — no account-existence disclosure per CLAUDE.md §9.
        (new PasswordResetService())->requestReset($email);

        return redirect()->back()
            ->with('message', 'Permintaan pemulihan telah diterima. Jika alamat email terdaftar di sistem, tautan pengaturan ulang kata sandi telah diproses.');
    }

    public function resetForm(string $token)
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $row = (new PasswordResetService())->findValidToken($token);
        if (! $row) {
            return redirect()->to(site_url('dashboard/lupa-kata-sandi'))
                ->with('error', 'Tautan pemulihan kata sandi tidak valid atau sudah kedaluwarsa. Silakan ajukan permohonan baru.');
        }

        return view('dashboard/auth/reset_password', [
            'title'           => 'Atur Ulang Kata Sandi | BegawiPengadaan',
            'pageTitle'       => 'Atur Ulang Kata Sandi',
            'pageDescription' => 'Silakan masukkan kata sandi baru yang memenuhi standar keamanan sistem.',
            'token'           => $token,
        ]);
    }

    public function updatePassword(string $token)
    {
        $rules = [
            'password'              => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
            'password_confirmation' => 'required|matches[password]',
        ];

        $messages = [
            'password' => [
                'required'    => 'Kata sandi baru wajib diisi.',
                'min_length'  => 'Kata sandi baru minimal harus terdiri dari 12 karakter.',
                'regex_match' => 'Kata sandi baru wajib memadukan huruf besar, huruf kecil, angka, dan karakter khusus/simbol.',
            ],
            'password_confirmation' => [
                'required' => 'Konfirmasi kata sandi baru wajib diisi.',
                'matches'  => 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.',
            ],
        ];

        $postData = (array) ($this->request->getPost() ?: $_POST);

        if (! $this->validateData($postData, $rules, $messages)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors());
        }

        $newPassword = (string) ($postData['password'] ?? '');

        $ok = (new PasswordResetService())->resetPassword($token, $newPassword);

        if (! $ok) {
            return redirect()->to(site_url('dashboard/lupa-kata-sandi'))
                ->with('error', 'Tautan pengaturan ulang sudah tidak valid atau telah digunakan sebelumnya.');
        }

        return redirect()->to(site_url('dashboard/masuk'))
            ->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk menggunakan kata sandi baru.');
    }
}
