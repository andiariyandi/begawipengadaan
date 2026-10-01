<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WilayahModel;
use App\Services\ApprovalService;
use App\Services\AuditService;

class RegisterController extends BaseController
{
    public function account(): string
    {
        return view('dashboard/auth/register', [
            'title' => 'Daftar | BegawiPBJ',
            'pageTitle' => 'Buat Akun BegawiPBJ',
            'currentStep' => 1,
            'currentKey' => 'akun',
            'registrationData' => session('registration_data') ?? [],
        ]);
    }

    public function region(): string
    {
        return view('dashboard/auth/register_address', [
            'title' => 'Wilayah Pendaftaran | BegawiPBJ',
            'pageTitle' => 'Wilayah dan Alamat Ringkas',
            'currentStep' => 2,
            'currentKey' => 'wilayah',
            'provinces' => (new WilayahModel())->provinces(),
            'registrationAddress' => session('registration_address') ?? [],
        ]);
    }

    public function review(): string
    {
        return view('dashboard/auth/register_review', [
            'title' => 'Tinjau Pendaftaran | BegawiPBJ',
            'pageTitle' => 'Tinjau Data Pendaftaran',
            'currentStep' => 4,
            'currentKey' => 'tinjau',
            'registrationData' => session('registration_data') ?? [],
            'registrationAddress' => session('registration_address') ?? [],
            'registrationProfile' => session('registration_profile') ?? [],
        ]);
    }

    public function success(): string
    {
        return view('dashboard/auth/register_success', [
            'title' => 'Pendaftaran Berhasil | BegawiPBJ',
            'pageTitle' => 'Pendaftaran Berhasil Dikirim',
        ]);
    }

    public function storeAccount()
    {
        $rules = [
            'full_name' => 'required|max_length[255]',
            'username' => 'required|min_length[3]|max_length[255]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|max_length[32]',
            'password' => 'required|min_length[12]',
            'password_confirmation' => 'required|matches[password]',
            'profile_type' => 'required|in_list[asn,provider,academic,public]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        session()->set('registration_data', $this->request->getPost([
            'full_name', 'username', 'email', 'phone', 'password', 'profile_type',
        ]));

        return redirect()->to(route_to('dashboard.auth.register.region'));
    }

    public function storeRegion()
    {
        if (! session()->has('registration_data')) {
            return redirect()->to(route_to('dashboard.auth.register.account'));
        }
        if (! $this->validate([
            'province' => 'required',
            'regency' => 'required',
            'district' => 'required',
            'address' => 'required|max_length[500]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $address = $this->request->getPost(['province', 'regency', 'district', 'village', 'address']);
        if (! (new WilayahModel())->isValidHierarchy(
            (string) $address['province'], (string) $address['regency'],
            (string) $address['district'], (string) ($address['village'] ?? '')
        )) {
            return redirect()->back()->withInput()->with('error', 'Pilihan wilayah tidak valid.');
        }
        $wilayah = new WilayahModel();
        foreach (['province', 'regency', 'district', 'village'] as $key) {
            $code = (string) ($address[$key] ?? '');
            $address[$key . '_name'] = $code !== '' ? ($wilayah->find($code)['nama'] ?? $code) : '';
        }
        session()->set('registration_address', $address);
        $type = session('registration_data.profile_type');
        return redirect()->to(route_to('dashboard.auth.register.profile', $type === 'provider' ? 'penyedia' : ($type === 'academic' ? 'akademisi' : $type)));
    }

    public function submit()
    {
        $data = session('registration_data');
        $address = session('registration_address');
        $profile = session('registration_profile');
        if (! $data || ! $address || ! $profile || ! $this->request->getPost('terms') || ! $this->request->getPost('privacy')) {
            return redirect()->back()->with('error', 'Lengkapi seluruh data dan persetujuan sebelum mengirim pendaftaran.');
        }
        $db = db_connect();
        $db->transStart();
        $userId = (new UserModel())->insert([
            'name' => trim($data['full_name']),
            'username' => trim($data['username']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'profile_type' => $data['profile_type'],
            'account_status' => 'pending_verification',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], true);
        $db->table('user_profiles')->insert([
            'user_id' => $userId,
            'address_line' => trim($address['address']),
            'province_code' => $address['province'],
            'regency_code' => $address['regency'],
            'district_code' => $address['district'],
            'village_code' => $address['village'] ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $profileTable = ['asn' => 'asn_profiles', 'provider' => 'provider_profiles', 'academic' => 'academic_profiles', 'public' => 'public_profiles'][$data['profile_type']];
        $profileRow = ['user_id' => $userId, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        $profileRow += match ($data['profile_type']) {
            'public' => ['national_identifier' => $profile['nik'] ?? null, 'occupation_status' => $profile['occupation_status'] ?? 'masyarakat', 'organization_name' => $profile['organization_name'] ?? null],
            'provider' => ['business_name' => $profile['company_name'] ?? '', 'business_form' => $profile['business_entity_type'] ?? 'lainnya', 'registrant_position' => $profile['registrant_position'] ?? '', 'responsible_person' => $profile['responsible_person'] ?? '', 'business_identifier' => $profile['nib'] ?? null, 'business_email' => $profile['company_email'] ?? null, 'business_address' => $profile['company_address'] ?? null],
            'academic' => ['institution_name' => $profile['institution_name'] ?? '', 'institution_type' => $profile['institution_type'] ?? 'lainnya', 'role_name' => $profile['academic_role'] ?? '', 'expertise' => json_encode($profile['expertise'] ?? [])],
            default => ['employee_identifier' => $profile['nip_nippk'] ?? null, 'employment_status' => $profile['employment_status'] ?? null, 'origin_type' => $profile['asn_origin'] ?? 'lainnya', 'institution_name' => $profile['institution_name'] ?? null, 'unit_name' => $profile['external_unit_name'] ?? null, 'position_name' => $profile['position'] ?? null, 'rank_grade' => $profile['rank'] ?? null, 'official_email' => $profile['official_email'] ?? null],
        };
        $db->table($profileTable)->insert($profileRow);
        (new ApprovalService())->createApproval('user_registration', (int) $userId, (int) $userId);
        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Pendaftaran gagal disimpan. Silakan periksa kembali data Anda.');
        }
        (new AuditService())->log((int) $userId, 'register', 'auth', 'users', (int) $userId);
        session()->remove(['registration_data', 'registration_address', 'registration_profile']);
        return redirect()->to(route_to('dashboard.auth.register.success'));
    }
}
