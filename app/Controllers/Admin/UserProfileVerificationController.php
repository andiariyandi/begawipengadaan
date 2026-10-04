<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\ProfileVerificationService;
use App\Services\Admin\UserManagementService;

/**
 * Admin Manajemen Akses → Verifikasi Profil.
 *
 * Provides approve / reject / revision actions. All three update both
 * profile_verifications and users.account_status in a single workflow
 * step, and emit audit_logs + workflow_transitions via the service layer.
 */
class UserProfileVerificationController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $items = (new ProfileVerificationService())->queue(
            $status !== '' ? ['status' => $status] : [],
        );

        return view('dashboard/users/profile_verifications', [
            'title'         => 'Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle'     => 'Antrean Verifikasi Profil',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'profile-verifications',
            'items'         => $items,
            'filters'       => ['status' => $status],
            'statuses'      => ['pending', 'in_review', 'approved', 'rejected', 'revision_required'],
        ]);
    }

    public function detail(string $id)
    {
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $service = new ProfileVerificationService();
        $verification = $service->find($verificationId);

        if (! $verification) {
            return redirect()->to('dashboard/akses/verifikasi-profil')
                ->with('error', 'Verifikasi profil tidak ditemukan.');
        }

        $userId = (int) $verification['user_id'];
        $db = db_connect();

        $user = $db->table('users u')
            ->select('u.*, up.province_code, up.regency_code, up.district_code, up.address_line,
                      wp.nama AS province_name, wr.nama AS regency_name, wd.nama AS district_name,
                      GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") AS roles')
            ->join('user_profiles up', 'up.user_id = u.id', 'left')
            ->join('wilayah wp', 'wp.kode = up.province_code', 'left')
            ->join('wilayah wr', 'wr.kode = up.regency_code', 'left')
            ->join('wilayah wd', 'wd.kode = up.district_code', 'left')
            ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = "active"', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.id', $userId)
            ->groupBy('u.id')
            ->get()->getRowArray();

        $typeProfile = match ($user['profile_type'] ?? '') {
            'asn'      => $db->table('asn_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'provider' => $db->table('provider_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'academic' => $db->table('academic_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'public'   => $db->table('public_profiles')->where('user_id', $userId)->get()->getRowArray(),
            default    => null,
        };

        $transitions = (new UserManagementService());
        $history = $db->table('workflow_transitions')
            ->where('resource_type', 'profile_verification')
            ->where('resource_id', $verificationId)
            ->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('dashboard/users/profile_verification_detail', [
            'title'         => 'Detail Verifikasi Profil | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Verifikasi Profil',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'profile-verifications',
            'verification'  => $verification,
            'user'          => $user,
            'typeProfile'   => $typeProfile,
            'history'       => $history,
            'transitions'   => $transitions,
        ]);
    }

    public function approve(string $id)
    {
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        $notes = (string) $this->request->getPost('notes');

        if ((new ProfileVerificationService())->approve($verificationId, $actorId, $notes)) {
            return redirect()->to('dashboard/akses/verifikasi-profil')->with('success', 'Verifikasi disetujui.');
        }
        return redirect()->back()->with('error', 'Verifikasi tidak ditemukan.');
    }

    public function reject(string $id)
    {
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $reason = trim((string) $this->request->getPost('reason'));
        $notes = (string) $this->request->getPost('notes');
        $actorId = (int) session()->get('user_id');

        try {
            (new ProfileVerificationService())->reject($verificationId, $reason, $actorId, $notes);
            return redirect()->to('dashboard/akses/verifikasi-profil')->with('success', 'Verifikasi ditolak.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Verifikasi tidak ditemukan.');
        }
    }

    public function requestRevision(string $id)
    {
        $verificationId = ctype_digit($id) ? (int) $id : 0;
        $note = trim((string) $this->request->getPost('revision_note'));
        $actorId = (int) session()->get('user_id');

        try {
            (new ProfileVerificationService())->requestRevision($verificationId, $note, $actorId);
            return redirect()->to('dashboard/akses/verifikasi-profil')->with('success', 'Permintaan revisi dikirim.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Verifikasi tidak ditemukan.');
        }
    }

    public function changeRequests()
    {
        $items = db_connect()->table('profile_change_requests pcr')
            ->select('pcr.*, u.name AS user_name, u.email, u.username')
            ->join('users u', 'u.id = pcr.user_id', 'left')
            ->orderBy('pcr.created_at', 'DESC')
            ->get()->getResultArray();

        return view('dashboard/users/profile_change_requests', [
            'title'         => 'Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle'     => 'Permohonan Perubahan Profil',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'profile-change-requests',
            'items'         => $items,
        ]);
    }

    public function changeRequestDetail(string $id)
    {
        $requestId = ctype_digit($id) ? (int) $id : 0;
        $db = db_connect();

        $request = $db->table('profile_change_requests pcr')
            ->select('pcr.*, u.name AS user_name, u.email, u.username, u.profile_type')
            ->join('users u', 'u.id = pcr.user_id', 'left')
            ->where('pcr.id', $requestId)
            ->get()->getRowArray();

        if (! $request) {
            return redirect()->to('dashboard/akses/permohonan-perubahan-profil')
                ->with('error', 'Permohonan perubahan tidak ditemukan.');
        }

        $payload = json_decode((string) ($request['requested_payload'] ?? '{}'), true) ?: [];
        $typeTable = match ($request['profile_type'] ?? '') {
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
            default    => null,
        };

        $current = [];
        if ($typeTable) {
            $current = $db->table($typeTable)->where('user_id', $request['user_id'])->get()->getRowArray() ?: [];
        } else {
            $current = $db->table('user_profiles')->where('user_id', $request['user_id'])->get()->getRowArray() ?: [];
        }

        $history = $db->table('workflow_transitions')
            ->where('resource_type', 'profile_change_request')
            ->where('resource_id', $requestId)
            ->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('dashboard/users/profile_change_request_detail', [
            'title'         => 'Detail Permohonan Perubahan Profil | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Permohonan Perubahan Profil',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'profile-change-requests',
            'request'       => $request,
            'payload'       => $payload,
            'current'       => $current,
            'history'       => $history,
        ]);
    }
}