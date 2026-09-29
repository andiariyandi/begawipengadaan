<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RoleRequestReviewController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/users/role_requests', [
            'title' => 'Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Permohonan Role',
            'activeMenu' => 'access',
            'activeSubmenu' => 'role-requests',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/users/role_request_detail', [
            'title' => 'Detail Permohonan Role | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permohonan Role',
            'requestId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'role-requests',
        ]);
    }
}
