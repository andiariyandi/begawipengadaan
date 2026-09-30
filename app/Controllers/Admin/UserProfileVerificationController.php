<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserProfileVerificationController extends BaseController
{
    public function index(): string
    {
        $items = db_connect()->table('users u')->select('u.id,u.account_status,u.created_at,p.full_name,p.profile_category')->join('user_profiles p','p.user_id=u.id','left')->whereIn('u.account_status',['pending_verification','submitted','revision_required'])->orderBy('u.created_at','DESC')->get()->getResultArray();
        return view('dashboard/users/profile_verifications', [
            'title' => 'Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Verifikasi Profil',
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-verifications',
            'items' => $items,
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
