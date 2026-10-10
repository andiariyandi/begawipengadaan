<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WilayahModel;
use App\Services\AuditService;
use Config\ProfileOptions;

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
            'options'     => new ProfileOptions(),
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
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'full_name' => 'required|max_length[150]',
            'phone' => 'required|max_length[25]',
            'province_code' => 'required|regex_match[/^\d{2}$/]|is_not_unique[wilayah.kode]',
            'regency_code'  => 'required|regex_match[/^\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'district_code' => 'required|regex_match[/^\d{2}\.\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'village_code'  => 'permit_empty|regex_match[/^\d{2}\.\d{2}\.\d{2}\.\d{4}$/]|is_not_unique[wilayah.kode]',
        ];

        if ($profileType === 'asn') {
            $rules['identity_number'] = 'required|max_length[25]|is_unique[user_asn_profiles.identity_number,user_id,' . $userId . ']';
        } elseif ($profileType === 'provider') {
            $rules['nib'] = 'required|max_length[30]|is_unique[user_provider_profiles.nib,user_id,' . $userId . ']';
        } elseif ($profileType === 'public') {
            $rules['nik'] = 'required|max_length[32]|is_unique[user_public_profiles.nik,user_id,' . $userId . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Data profil tidak valid atau sudah digunakan pengguna lain.');
        }

        if (! (new WilayahModel())->isValidHierarchy(
            (string) $this->request->getPost('province_code'),
            (string) $this->request->getPost('regency_code'),
            (string) $this->request->getPost('district_code'),
            (string) $this->request->getPost('village_code')
        )) {
            return redirect()->back()->withInput()
                ->with('error', 'Kombinasi wilayah domisili tidak valid.');
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

        $profileData = [
            'province_code' => trim((string) $this->request->getPost('province_code')),
            'regency_code'  => trim((string) $this->request->getPost('regency_code')),
            'district_code' => trim((string) $this->request->getPost('district_code')),
            'village_code'  => trim((string) $this->request->getPost('village_code')) ?: null,
            'updated_at'    => $now,
        ];
        if ($this->request->getPost('address_line') !== null) {
            $profileData['address_line'] = trim((string) $this->request->getPost('address_line'));
        }
        $db->table('user_profiles')->where('user_id', $userId)->update($profileData);

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

        $rules = [
            'current_password'      => 'required',
            'password'              => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Password baru minimal 12 karakter dengan huruf besar, kecil, angka, dan simbol. Konfirmasi harus sama.');
        }

        if (! password_verify((string) $this->request->getPost('current_password'), (string) ($user['password_hash'] ?? ''))) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        if (! $model->update($userId, [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
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