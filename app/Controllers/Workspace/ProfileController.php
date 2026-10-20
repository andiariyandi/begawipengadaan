<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WilayahModel;
use App\Services\AuditService;

/**
 * Workspace Profile controller.
 *
 * Ported from App\Controllers\Auth\ProfileController (CLAUDE.md §29 — legacy
 * boundary cleanup). Routes migrated from /profil/* to /workspace/profil/* per
 * route-contract.md §Workspace. View path preserved as landing/user/profile to
 * avoid view churn; eventual migration to main/workspace/profile/* is a
 * separate task.
 */
class ProfileController extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('user_id');
        $user   = $userId > 0 ? (new UserModel())->find($userId) : null;

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $db = db_connect();
        $detailTables = $this->detailTables();
        $profileType = $user['profile_type'] ?? 'public';

        $latestRequest = $db->table('profile_change_requests')
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->get()->getRowArray();

        return view('main/workspace/pages/profil', [
            'title'         => 'Profil Saya',
            'subTitle'      => 'Profil Saya',
            'user'          => $user,
            'profile'       => $db->table('user_profiles')->where('user_id', $user['id'])->get()->getRowArray() ?: [],
            'detail'        => isset($detailTables[$profileType])
                ? ($db->table($detailTables[$profileType])->where('user_id', $user['id'])->get()->getRowArray() ?: [])
                : [],
            'profileType'   => $profileType,
            'provinces'     => (new WilayahModel())->provinces(),
            'latestRequest' => $latestRequest,
        ]);
    }

    public function account()
    {
        return $this->index();
    }

    public function asn()
    {
        return $this->index();
    }

    public function provider()
    {
        return $this->index();
    }

    public function academic()
    {
        return $this->index();
    }

    public function public()
    {
        return $this->index();
    }

    public function procurementRoles()
    {
        return $this->index();
    }

    public function update()
    {
        $userId = (int) session()->get('user_id');
        $model  = new UserModel();
        $user   = $userId > 0 ? $model->find($userId) : null;

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $db = db_connect();
        $profileType = $user['profile_type'] ?? 'public';

        $rules = [
            'username'      => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'         => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'full_name'     => 'required|max_length[150]',
            'phone'         => 'permit_empty|max_length[25]',
            'province_code' => 'permit_empty',
            'address_line'  => 'permit_empty|max_length[500]',
            'reason'        => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Data yang Anda masukkan tidak valid: ' . implode(' ', $this->validator->getErrors()));
        }

        $now = date('Y-m-d H:i:s');
        $newValues = [
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'name'          => trim((string) $this->request->getPost('full_name')),
            'phone'         => trim((string) $this->request->getPost('phone')),
            'province_code' => trim((string) $this->request->getPost('province_code')),
            'address_line'  => trim((string) ($this->request->getPost('address_line') ?? $this->request->getPost('address') ?? '')),
        ];

        // Specific fields per profile type
        if ($profileType === 'asn') {
            foreach (['employee_identifier', 'unit_name', 'position_name', 'institution_name', 'rank_grade'] as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        } elseif ($profileType === 'provider') {
            foreach (['business_name', 'business_identifier', 'registrant_position'] as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        } elseif ($profileType === 'academic') {
            foreach (['institution_name', 'role_name', 'expertise'] as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        } elseif ($profileType === 'public') {
            foreach (['national_identifier', 'organization_name'] as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        }

        // Compare against current state
        $currProfile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray() ?: [];
        $detailTables = $this->detailTables();
        $typeTable = $detailTables[$profileType] ?? null;
        $currSpec = $typeTable ? ($db->table($typeTable)->where('user_id', $userId)->get()->getRowArray() ?: []) : [];

        $currentMerged = array_merge($user, $currProfile, $currSpec);
        $currentMerged['full_name'] = $user['name'] ?? '';

        $changedPayload = [];
        foreach ($newValues as $k => $val) {
            $oldVal = trim((string) ($currentMerged[$k] ?? ''));
            if ($val !== $oldVal) {
                $changedPayload[$k] = $val;
            }
        }

        if (empty($changedPayload)) {
            return redirect()->to(route_to('workspace.profile.index'))
                ->with('info', 'Tidak ada perubahan data yang diajukan. Data profil Anda saat ini masih sama.');
        }

        $reason = trim((string) $this->request->getPost('reason')) ?: 'Pengajuan pembaruan data identitas profil melalui workspace.';

        // Check if there is already an active pending request
        $existingPending = $db->table('profile_change_requests')
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'in_review'])
            ->get()->getRowArray();

        $service = new \App\Services\Admin\ProfileChangeRequestService();

        if ($existingPending) {
            $db->table('profile_change_requests')->where('id', $existingPending['id'])->update([
                'requested_payload' => json_encode($changedPayload, JSON_THROW_ON_ERROR),
                'reason'            => $reason,
                'status'            => 'submitted',
                'updated_at'        => $now,
            ]);
            $requestId = (int) $existingPending['id'];
        } else {
            $requestId = $service->submit($userId, $changedPayload, $reason);
        }

        // Send notification to Admins and Operators
        try {
            (new \App\Services\NotificationService())->notifyAdmins(
                'profile_change_request',
                'Permohonan Perubahan Profil Baru',
                "Pengguna {$user['name']} telah mengajukan permohonan perubahan profil.",
                ['request_id' => $requestId, 'url' => site_url('dashboard/akses/permohonan-perubahan-profil/' . $requestId)]
            );
        } catch (\Throwable) {
        }

        return redirect()->to(route_to('workspace.profile.index'))
            ->with('success', 'Permohonan perubahan profil Anda berhasil diajukan dan sedang menunggu verifikasi serta persetujuan oleh Administrator atau Operator.');
    }

    public function updatePassword()
    {
        $userId = (int) session()->get('user_id');
        $model  = new UserModel();
        $user   = $userId > 0 ? $model->find($userId) : null;

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $currPassword    = (string) $this->request->getPost('current_password');
        $newPassword     = (string) ($this->request->getPost('password') ?: $this->request->getPost('new_password'));
        $confirmPassword = (string) ($this->request->getPost('password_confirmation') ?: $this->request->getPost('confirm_password'));

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validateData([
            'current_password' => $currPassword,
            'new_password'     => $newPassword,
            'confirm_password' => $confirmPassword,
        ], $rules)) {
            return redirect()->back()
                ->with('error', 'Password baru minimal 8 karakter dan konfirmasi password harus sesuai.');
        }

        if (! password_verify($currPassword, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        if (! $model->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ])) {
            return redirect()->back()->with('error', 'Password gagal diperbarui.');
        }

        (new AuditService())->log($userId, 'change_password', 'auth', 'users', $userId);

        return redirect()->to(route_to('workspace.profile.index'))
            ->with('message', 'Password berhasil diperbarui.');
    }

    private function detailTables(): array
    {
        return [
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
        ];
    }
}