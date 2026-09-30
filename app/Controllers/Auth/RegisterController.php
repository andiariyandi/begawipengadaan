<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuditService;
use App\Services\ApprovalService;
use Config\ProfileOptions;
use App\Models\WilayahModel;

class RegisterController extends BaseController
{
    public function index()
    {
        $options = new ProfileOptions();
        $provinces = (new WilayahModel())->provinces();

        return view('authentication/signup', ['options' => $options, 'provinces' => $provinces]);
    }

    public function attempt()
    {
        $type = (string) $this->request->getPost('profile_type');
        $options = new ProfileOptions();
        if (! array_key_exists($type, $options->profileTypes)) {
            return redirect()->back()->withInput()->with('error', 'Kategori profil tidak valid.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|max_length[150]',
            'phone' => 'required|max_length[25]',
            'province_code' => 'required|regex_match[/^\d{2}$/]|is_not_unique[wilayah.kode]',
            'regency_code' => 'required|regex_match[/^\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'district_code' => 'required|regex_match[/^\d{2}\.\d{2}\.\d{2}$/]|is_not_unique[wilayah.kode]',
            'village_code' => 'permit_empty|regex_match[/^\d{2}\.\d{2}\.\d{2}\.\d{4}$/]|is_not_unique[wilayah.kode]',
        ];
        $rules = array_merge($rules, match ($type) {
            'asn' => [
                'identity_number' => 'required|max_length[25]|is_unique[user_asn_profiles.identity_number]',
                'identity_type' => 'required|in_list[NIP,NI PPPK]',
                'asn_status' => 'required|in_list[PNS,PPPK]',
                'institution' => 'required|max_length[150]',
                'work_unit' => 'required|max_length[150]',
                'position' => 'required|max_length[100]',
            ],
            'provider' => [
                'nib' => 'required|max_length[30]|is_unique[user_provider_profiles.nib]',
                'business_name' => 'required|max_length[180]',
                'responsible_person' => 'required|max_length[150]',
                'company_email' => 'required|valid_email',
                'company_phone' => 'required|max_length[25]',
            ],
            'academic_practitioner' => [
                'institution' => 'required|max_length[180]',
                'expertise' => 'required|max_length[120]',
                'email_contact' => 'required|valid_email',
                'phone_contact' => 'required|max_length[25]',
            ],
            default => [
                'nik' => 'required|max_length[32]|is_unique[user_public_profiles.nik]',
            ],
        });

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if (! (new WilayahModel())->isValidHierarchy(
            (string) $this->request->getPost('province_code'),
            (string) $this->request->getPost('regency_code'),
            (string) $this->request->getPost('district_code'),
            (string) $this->request->getPost('village_code')
        )) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi wilayah domisili tidak valid.');
        }

        $userModel = new UserModel();

        $db = db_connect();
        $db->transStart();
        $userId = $userModel->insert([
            'username' => trim((string) $this->request->getPost('username')),
            'email' => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'user_type' => $type === 'asn' ? 'asn' : 'umum',
            'profile_type' => $type,
            'status' => 'pending',
        ], true);
        if (! $userId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Akun gagal dibuat.');
        }

        $common = [
            'user_id' => $userId,
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'title_prefix' => trim((string) $this->request->getPost('title_prefix')),
            'title_suffix' => trim((string) $this->request->getPost('title_suffix')),
            'phone' => trim((string) $this->request->getPost('phone')),
            'province_code' => trim((string) $this->request->getPost('province_code')),
            'regency_code' => trim((string) $this->request->getPost('regency_code')),
            'district_code' => trim((string) $this->request->getPost('district_code')),
            'village_code' => trim((string) $this->request->getPost('village_code')),
        ];
        $db->table('user_profiles')->insert($common);
        $detailTable = [
            'asn' => 'user_asn_profiles', 'provider' => 'user_provider_profiles',
            'academic_practitioner' => 'user_academic_profiles', 'public' => 'user_public_profiles',
        ][$type];
        $detail = ['user_id' => $userId];
        foreach ($db->getFieldNames($detailTable) as $field) {
            if ($field !== 'user_id' && $field !== 'id' && ! in_array($field, ['created_at', 'updated_at'], true)) {
                $detail[$field] = trim((string) $this->request->getPost($field));
            }
        }
        $db->table($detailTable)->insert($detail);
        (new ApprovalService())->createApproval('user_registration', $userId, $userId);
        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Registrasi gagal diproses.');
        }

        (new AuditService())->log($userId, 'register', 'auth', 'users', $userId);

        return redirect()->to(route_to('signin'))
            ->with('error', 'Registrasi berhasil. Akun Anda menunggu verifikasi Operator sebelum bisa login.');
    }
}
