<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserProfileVerificationController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/users/profile_verifications', [
            'title' => 'Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Verifikasi Profil',
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-verifications',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/users/profile_verification_detail', [
            'title' => 'Detail Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Verifikasi Profil',
            'verificationId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-verifications',
        ]);
    }

    public function changeRequests(): string
    {
        return view('dashboard/users/profile_change_requests', [
            'title' => 'Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Permohonan Perubahan Profil',
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-change-requests',
        ]);
    }

    public function changeRequestDetail(string $id): string
    {
        return view('dashboard/users/profile_change_request_detail', [
            'title' => 'Detail Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permohonan Perubahan Profil',
            'requestId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-change-requests',
        ]);
    }
}
