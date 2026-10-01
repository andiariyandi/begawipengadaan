<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;

class AccountStatusController extends BaseController
{
    public function show(): string
    {
        return view('dashboard/auth/account_status', [
            'title' => 'Status Akun | BegawiPBJ',
            'user' => [],
        ]);
    }
}
