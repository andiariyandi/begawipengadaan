<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;

class EmailVerificationController extends BaseController
{
    public function verify(string $token): string
    {
        return view('dashboard/auth/account_status', [
            'title' => 'Verifikasi Email | BegawiPBJ',
            'user' => [],
        ]);
    }

    public function resend()
    {
        return redirect()->back()->with('message', 'Tautan verifikasi akan dikirim ulang jika alamat email terdaftar.');
    }
}
