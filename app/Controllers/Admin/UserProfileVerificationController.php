<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth\ProfileChangeRequestService;
use App\Services\Auth\ProfileVerificationService;
use Throwable;

class UserProfileVerificationController extends BaseController
{
    public function index(): string
    {
        $items = db_connect()->table('profile_verifications v')
            ->select('v.id,v.id verification_id,v.status verification_status,v.verification_type,v.created_at,v.user_id,u.name,u.email,u.account_status,u.profile_type,p.address_line,p.province_code,p.regency_code,p.district_code,p.village_code')
            ->join('users u', 'u.id = v.user_id')
            ->join('user_profiles p', 'p.user_id = u.id', 'left')
            ->whereIn('v.status', ['pending', 'in_review'])
            ->orderBy('v.created_at', 'DESC')
            ->get()->getResultArray();

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
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $verification = db_connect()->table('profile_verifications v')
            ->select('v.*,u.name,u.email,u.account_status,u.profile_type,p.address_line,p.province_code,p.regency_code,p.district_code,p.village_code')
            ->join('users u', 'u.id = v.user_id')
            ->join('user_profiles p', 'p.user_id = u.id', 'left')
            ->where('v.id', $verificationId)
            ->get()->getRowArray();

        return view('dashboard/users/profile_verification_detail', [
            'title' => 'Detail Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Verifikasi Profil',
            'verificationId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-verifications',
            'verification' => $verification,
        ]);
    }

    public function changeRequests(): string
    {
        $requests = db_connect()->table('profile_change_requests r')
            ->select('r.*,u.name,u.email,u.profile_type')
            ->join('users u', 'u.id = r.user_id')
            ->whereIn('r.status', ['submitted', 'in_review', 'revision_required'])
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultArray();

        return view('dashboard/users/profile_change_requests', [
            'title' => 'Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Permohonan Perubahan Profil',
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-change-requests',
            'requests' => $requests,
        ]);
    }

    public function changeRequestDetail(string $id): string
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $request = db_connect()->table('profile_change_requests r')
            ->select('r.*,u.name,u.email,u.profile_type')
            ->join('users u', 'u.id = r.user_id')
            ->where('r.id', $requestId)
            ->get()->getRowArray();

        return view('dashboard/users/profile_change_request_detail', [
            'title' => 'Detail Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Permohonan Perubahan Profil',
            'requestId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'profile-change-requests',
            'request' => $request,
        ]);
    }

    public function decide(string $id)
    {
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $action = (string) ($this->request->getPost('action') ?: $this->request->getPost('decision'));
        $reason = trim((string) ($this->request->getPost('reason') ?: $this->request->getPost('notes')));
        $actorId = (int) session()->get('user_id');

        try {
            $service = new ProfileVerificationService();
            if ($action === 'approve') {
                $service->approve($verificationId, $actorId, $reason ?: null);
            } elseif ($action === 'reject') {
                $service->reject($verificationId, $actorId, $reason);
            } elseif (in_array($action, ['revision', 'revision_required'], true)) {
                $service->revisionRequired($verificationId, $actorId, $reason);
            } else {
                throw new \RuntimeException('Aksi verifikasi profil tidak valid.');
            }

            return redirect()->to(site_url('dashboard/akses/verifikasi-profil'))->with('success', 'Verifikasi profil berhasil diproses.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function decideChangeRequest(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $action = (string) ($this->request->getPost('action') ?: $this->request->getPost('decision'));
        $reason = trim((string) ($this->request->getPost('reason') ?: $this->request->getPost('notes')));
        $actorId = (int) session()->get('user_id');

        try {
            $service = new ProfileChangeRequestService();
            if ($action === 'approve') {
                $service->approve($requestId, $actorId, $reason ?: null);
            } elseif ($action === 'reject') {
                $service->reject($requestId, $actorId, $reason);
            } elseif (in_array($action, ['revision', 'revision_required'], true)) {
                $service->revision($requestId, $actorId, $reason);
            } else {
                throw new \RuntimeException('Aksi perubahan profil tidak valid.');
            }

            return redirect()->to(site_url('dashboard/akses/permohonan-perubahan-profil'))->with('success', 'Permohonan perubahan profil berhasil diproses.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
