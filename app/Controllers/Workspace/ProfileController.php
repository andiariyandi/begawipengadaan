<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WilayahModel;
use App\Services\AuditService;

/**
 * Workspace Profile controller.
 *
 * Implements Dynamic Profile Management per profile_type (asn, provider, academic, public)
 * with locked profile_type and mandatory Admin/Operator Approval Workflow via profile_change_requests.
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

        $profile = $db->table('user_profiles')->where('user_id', $user['id'])->get()->getRowArray() ?: [];
        $detail  = isset($detailTables[$profileType])
            ? ($db->table($detailTables[$profileType])->where('user_id', $user['id'])->get()->getRowArray() ?: [])
            : [];

        $wilayahModel = new WilayahModel();
        $provinces    = $wilayahModel->provinces();
        $regencies    = ! empty($profile['province_code']) ? $wilayahModel->children((string) $profile['province_code']) : [];
        $districts    = ! empty($profile['regency_code']) ? $wilayahModel->children((string) $profile['regency_code']) : [];
        $villages     = ! empty($profile['district_code']) ? $wilayahModel->children((string) $profile['district_code']) : [];

        $organizationUnits = [];
        $userProcurementRoles = [];
        if ($profileType === 'asn') {
            if ($db->tableExists('organization_units')) {
                $organizationUnits = $db->table('organization_units')
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->get()->getResultArray();
            }
            if ($db->tableExists('user_procurement_roles') && $db->tableExists('procurement_roles')) {
                $userProcurementRoles = $db->table('user_procurement_roles upr')
                    ->select('upr.*, pr.name AS role_name, pr.key AS role_key')
                    ->join('procurement_roles pr', 'pr.id = upr.procurement_role_id', 'left')
                    ->where('upr.user_id', $user['id'])
                    ->orderBy('upr.id', 'DESC')
                    ->get()->getResultArray();
            }
        }

        $requestHistory = $db->table('profile_change_requests')
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $latestRequest = $requestHistory[0] ?? null;

        return view('main/workspace/pages/profil', [
            'title'                => 'Profil Saya | Workspace BegawiPengadaan',
            'pageTitle'            => 'Manajemen Profil Dinamis',
            'subTitle'             => 'Profil Saya',
            'user'                 => $user,
            'profile'              => $profile,
            'detail'               => $detail,
            'profileType'          => $profileType,
            'provinces'            => $provinces,
            'regencies'            => $regencies,
            'districts'            => $districts,
            'villages'             => $villages,
            'organizationUnits'    => $organizationUnits,
            'userProcurementRoles' => $userProcurementRoles,
            'latestRequest'        => $latestRequest,
            'requestHistory'       => $requestHistory,
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
        // Kunci Statis: profile_type selalu diambil dari record DB user, tidak pernah dari input form
        $profileType = $user['profile_type'] ?? 'public';

        $rules = [
            'username'      => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'         => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'full_name'     => 'required|max_length[150]',
            'phone'         => 'permit_empty|max_length[25]',
            'province_code' => 'permit_empty|max_length[20]',
            'regency_code'  => 'permit_empty|max_length[20]',
            'district_code' => 'permit_empty|max_length[20]',
            'village_code'  => 'permit_empty|max_length[20]',
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
            'regency_code'  => trim((string) $this->request->getPost('regency_code')),
            'district_code' => trim((string) $this->request->getPost('district_code')),
            'village_code'  => trim((string) $this->request->getPost('village_code')) ?: null,
            'address_line'  => trim((string) ($this->request->getPost('address_line') ?? $this->request->getPost('address') ?? '')),
        ];

        // Dynamic schema fields per profile_type (matching Register Bertahap)
        if ($profileType === 'asn') {
            $asnFields = [
                'origin_type',
                'employee_identifier',
                'employment_status',
                'organization_unit_id',
                'institution_name',
                'unit_name',
                'position_name',
                'rank_grade',
                'official_email',
            ];
            foreach ($asnFields as $f) {
                if ($this->request->getPost($f) !== null) {
                    $val = trim((string) $this->request->getPost($f));
                    $newValues[$f] = ($f === 'organization_unit_id') ? ($val !== '' ? (int) $val : null) : $val;
                }
            }

            if (($newValues['origin_type'] ?? '') === 'pemprov_lampung' && ! empty($newValues['organization_unit_id'])) {
                $opd = $db->table('organization_units')->where('id', (int) $newValues['organization_unit_id'])->get()->getRowArray();
                if ($opd) {
                    $newValues['institution_name'] = 'Pemerintah Daerah Provinsi Lampung';
                    $newValues['unit_name']        = (string) $opd['name'];
                }
            } elseif (isset($newValues['origin_type']) && $newValues['origin_type'] !== 'pemprov_lampung') {
                $newValues['organization_unit_id'] = null;
            }
        } elseif ($profileType === 'provider') {
            $providerFields = [
                'business_name',
                'business_form',
                'business_field',
                'business_identifier',
                'registrant_position',
                'responsible_person',
                'business_email',
                'business_address',
            ];
            foreach ($providerFields as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        } elseif ($profileType === 'academic') {
            $academicFields = [
                'institution_name',
                'institution_type',
                'role_name',
                'expertise',
                'institution_email',
                'professional_identifier',
                'portfolio_url',
            ];
            foreach ($academicFields as $f) {
                if ($this->request->getPost($f) !== null) {
                    $newValues[$f] = trim((string) $this->request->getPost($f));
                }
            }
        } elseif ($profileType === 'public') {
            $publicFields = [
                'national_identifier',
                'occupation_status',
                'organization_name',
                'interest_areas',
                'usage_purpose',
            ];
            foreach ($publicFields as $f) {
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
            $oldVal = $currentMerged[$k] ?? null;
            if ((string) $val !== trim((string) $oldVal)) {
                $changedPayload[$k] = $val;
            }
        }

        if (empty($changedPayload)) {
            return redirect()->to(route_to('workspace.profile.index'))
                ->with('info', 'Tidak ada perubahan data yang diajukan. Data profil Anda saat ini masih sama.');
        }

        $reason = trim((string) $this->request->getPost('reason')) ?: 'Pengajuan pembaruan data identitas profil melalui Workspace.';

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

        // Send shared notification to Pool Verifikator (Super Admin, Admin, & Operator)
        try {
            (new \App\Services\NotificationService())->sendShared(
                'shared:admin_operator',
                'profile_change_request',
                'Permohonan Perubahan Profil Baru',
                "Pengguna {$user['name']} (" . strtoupper($profileType) . ") mengajukan permohonan perubahan profil (" . count($changedPayload) . " field).",
                [
                    'request_id' => $requestId,
                    'user_id'    => $userId,
                    'url'        => site_url('workspace/operator/perubahan-profil/' . $requestId),
                ],
                $userId
            );
        } catch (\Throwable) {
        }

        return redirect()->to(route_to('workspace.profile.index'))
            ->with('success', 'Permohonan perubahan profil Anda berhasil diajukan ke status Menunggu Persetujuan (Pending Approval) dan akan diverifikasi oleh Administrator atau Operator.');
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
