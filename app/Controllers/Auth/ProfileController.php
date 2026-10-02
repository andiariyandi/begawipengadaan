<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuditService;
use Config\ProfileOptions;
use App\Models\WilayahModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $user = (new UserModel())->find((int) session()->get('user_id'));

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Data pengguna tidak ditemukan.');
        }
        $db = db_connect();
        $detailTables = [
            'asn' => 'user_asn_profiles',
            'provider' => 'user_provider_profiles',
            'academic_practitioner' => 'user_academic_profiles',
            'public' => 'user_public_profiles',
        ];

        return view('landing/user/profile', [
            'title'    => 'Profil Saya',
            'subTitle' => 'Profil Saya',
            'user'     => $user,
            'profile'  => $db->table('user_profiles')->where('user_id', $user['id'])->get()->getRowArray() ?: [],
            'detail'   => $db->table($detailTables[$user['profile_type'] ?? 'public'])->where('user_id', $user['id'])->get()->getRowArray() ?: [],
            'profileType' => $user['profile_type'] ?? 'public',
            'options' => new ProfileOptions(),
            'provinces' => (new WilayahModel())->provinces(),
        ]);
    }

    public function update()
    {
        $userId = (int) session()->get('user_id');
        $model  = new UserModel();
        $user   = $model->find($userId);

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Data pengguna tidak ditemukan.');
        }
        $db = db_connect();
        $profileType = $user['profile_type'] ?? 'public';
        $detailTables = ['asn' => 'user_asn_profiles', 'provider' => 'user_provider_profiles', 'academic_practitioner' => 'user_academic_profiles', 'public' => 'user_public_profiles'];

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'full_name' => 'required|max_length[150]',
            'phone' => 'required|max_length[25]',
            'province_code' => 'required|regex_match[/^\d{2}$/]|is_not_unique[wilayah.kode]',
            'regency_code' => 'required|regex_match[/^\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'district_code' => 'required|regex_match[/^\d{2}\.\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'village_code' => 'permit_empty|regex_match[/^\d{2}\.\d{2}\.\d{2}\.\d{4}$/]|is_not_unique[wilayah.kode]',
        ];
        if ($profileType === 'asn') {
            $rules['identity_number'] = 'required|max_length[25]|is_unique[user_asn_profiles.identity_number,user_id,' . $userId . ']';
        } elseif ($profileType === 'provider') {
            $rules['nib'] = 'required|max_length[30]|is_unique[user_provider_profiles.nib,user_id,' . $userId . ']';
        } elseif ($profileType === 'public') {
            $rules['nik'] = 'required|max_length[32]|is_unique[user_public_profiles.nik,user_id,' . $userId . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data profil tidak valid atau sudah digunakan pengguna lain.');
        }
        if (! (new WilayahModel())->isValidHierarchy(
            (string) $this->request->getPost('province_code'),
            (string) $this->request->getPost('regency_code'),
            (string) $this->request->getPost('district_code'),
            (string) $this->request->getPost('village_code')
        )) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi wilayah domisili tidak valid.');
        }

        $data = [
            'username' => trim((string) $this->request->getPost('username')),
            'email'    => trim((string) $this->request->getPost('email')),
        ];
        if (! $model->update($userId, $data)) {
            return redirect()->back()->withInput()->with('error', 'Profil gagal diperbarui.');
        }
        $common = ['full_name', 'title_prefix', 'title_suffix', 'phone', 'province_code', 'regency_code', 'district_code', 'village_code'];
        $profileData = [];
        foreach ($common as $field) {
            $profileData[$field] = trim((string) $this->request->getPost($field));
        }
        $db->table('user_profiles')->where('user_id', $userId)->update($profileData);
        $detail = $db->table($detailTables[$profileType])->where('user_id', $userId)->get()->getRowArray();
        $detailData = [];
        foreach (array_keys($detail ?: []) as $field) {
            if (! in_array($field, ['id', 'user_id', 'created_at', 'updated_at'], true)) {
                $detailData[$field] = trim((string) $this->request->getPost($field));
            }
        }
        if ($detail) {
            $db->table($detailTables[$profileType])->where('user_id', $userId)->update($detailData);
        }

        session()->set([
            'username' => $data['username'],
            'email'    => $data['email'],
        ]);

        (new AuditService())->log($userId, 'update_profile', 'auth', 'users', $userId);

        return redirect()->to(route_to('profile'))->with('message', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = (int) session()->get('user_id');
        $model  = new UserModel();
        $user   = $model->find($userId);

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'current_password'      => 'required',
            'password'              => 'required|min_length[8]|max_length[255]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Password baru minimal 8 karakter dan konfirmasi harus sama.');
        }

        if (! password_verify((string) $this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        if (! $model->update($userId, [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
        ])) {
            return redirect()->back()->with('error', 'Password gagal diperbarui.');
        }

        (new AuditService())->log($userId, 'change_password', 'auth', 'users', $userId);

        return redirect()->to(route_to('profile'))->with('message', 'Password berhasil diperbarui.');
    }
}
