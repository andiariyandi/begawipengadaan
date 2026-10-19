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

        return view('main/workspace/pages/profil', [
            'title'       => 'Profil Saya',
            'subTitle'    => 'Profil Saya',
            'user'        => $user,
            'profile'     => $db->table('user_profiles')->where('user_id', $user['id'])->get()->getRowArray() ?: [],
            'detail'      => isset($detailTables[$profileType])
                ? ($db->table($detailTables[$profileType])->where('user_id', $user['id'])->get()->getRowArray() ?: [])
                : [],
            'profileType' => $profileType,
            'provinces'   => (new WilayahModel())->provinces(),
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
        $detailTables = $this->detailTables();

        $rules = [
            'username'      => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'         => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'full_name'     => 'required|max_length[150]',
            'phone'         => 'permit_empty|max_length[25]',
            'province_code' => 'permit_empty|regex_match[/^\d{2}$/]|is_not_unique[wilayah.kode]',
            'regency_code'  => 'permit_empty|regex_match[/^\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'district_code' => 'permit_empty|regex_match[/^\d{2}\.\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'village_code'  => 'permit_empty|regex_match[/^\d{2}\.\d{2}\.\d{2}\.\d{4}$/]|is_not_unique[wilayah.kode]',
        ];

        if ($profileType === 'asn') {
            $rules['identity_number'] = 'permit_empty|max_length[25]|is_unique[asn_profiles.identity_number,user_id,' . $userId . ']';
        } elseif ($profileType === 'provider') {
            $rules['nib'] = 'permit_empty|max_length[30]|is_unique[provider_profiles.nib,user_id,' . $userId . ']';
        } elseif ($profileType === 'public') {
            $rules['nik'] = 'permit_empty|max_length[32]|is_unique[public_profiles.nik,user_id,' . $userId . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Data profil tidak valid atau sudah digunakan pengguna lain.');
        }

        $provinceCode = trim((string) $this->request->getPost('province_code'));
        $regencyCode  = trim((string) $this->request->getPost('regency_code'));
        $districtCode = trim((string) $this->request->getPost('district_code'));
        $villageCode  = trim((string) $this->request->getPost('village_code')) ?: null;

        if ($provinceCode !== '' && $regencyCode !== '' && $districtCode !== '') {
            if (! (new WilayahModel())->isValidHierarchy(
                $provinceCode,
                $regencyCode,
                $districtCode,
                $villageCode
            )) {
                return redirect()->back()->withInput()
                    ->with('error', 'Kombinasi wilayah domisili tidak valid.');
            }
        }

        $now = date('Y-m-d H:i:s');
        $userData = [
            'username'   => trim((string) $this->request->getPost('username')),
            'email'      => trim((string) $this->request->getPost('email')),
            'name'       => trim((string) $this->request->getPost('full_name')),
            'phone'      => trim((string) $this->request->getPost('phone')),
            'updated_at' => $now,
        ];

        if (! $model->update($userId, $userData)) {
            return redirect()->back()->withInput()
                ->with('error', 'Profil gagal diperbarui.');
        }

        $addressVal = trim((string) ($this->request->getPost('address_line') ?? $this->request->getPost('address') ?? ''));

        $profileData = [
            'updated_at'   => $now,
            'address_line' => $addressVal,
        ];
        if ($provinceCode !== '') {
            $profileData['province_code'] = $provinceCode;
        }
        if ($regencyCode !== '') {
            $profileData['regency_code'] = $regencyCode;
        }
        if ($districtCode !== '') {
            $profileData['district_code'] = $districtCode;
        }
        if ($villageCode !== null) {
            $profileData['village_code'] = $villageCode;
        }

        $existingProfile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
        if ($existingProfile) {
            $db->table('user_profiles')->where('user_id', $userId)->update($profileData);
        } else {
            $profileData['user_id']    = $userId;
            $profileData['created_at'] = $now;
            $db->table('user_profiles')->insert($profileData);
        }

        if (isset($detailTables[$profileType])) {
            $table = $detailTables[$profileType];
            $detail = $db->table($table)->where('user_id', $userId)->get()->getRowArray();
            if ($detail) {
                $detailData = ['updated_at' => $now];
                foreach (array_keys($detail) as $field) {
                    if (in_array($field, ['id', 'user_id', 'created_at', 'updated_at'], true)) {
                        continue;
                    }
                    if ($this->request->getPost($field) !== null) {
                        $detailData[$field] = trim((string) $this->request->getPost($field));
                    }
                }
                $db->table($table)->where('user_id', $userId)->update($detailData);
            }
        }

        session()->set([
            'username' => $userData['username'],
            'email'    => $userData['email'],
            'name'     => $userData['name'],
        ]);

        (new AuditService())->log($userId, 'update_profile', 'auth', 'users', $userId);

        return redirect()->to(route_to('workspace.profile.index'))
            ->with('message', 'Profil berhasil diperbarui.');
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