<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuthTokenModel;
use App\Models\UserModel;
use App\Services\AuditService;

class AdminAuthController extends BaseController
{
    public function index(): string
    {
        return $this->authView(
            'Masuk | BegawiPBJ',
            'Masuk ke Akun Anda',
            'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPBJ.',
            'login'
        );
    }

    public function attemptLogin()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $rules = [
            'email' => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (! $user || ! password_verify($password, (string) $user['password_hash'])) {
            (new AuditService())->log(null, 'login_failed', 'auth', 'users', null, ['email' => $email]);

            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
        }

        if (($user['account_status'] ?? null) !== 'active') {
            (new AuditService())->log((int) $user['id'], 'login_blocked', 'auth', 'users', (int) $user['id'], [
                'account_status' => $user['account_status'] ?? null,
            ]);

            return redirect()->back()->withInput()->with('error', 'Akun belum aktif atau sedang tidak dapat digunakan.');
        }

        $roles = $userModel->getRoles((int) $user['id']);
        $permissions = $userModel->getPermissions((int) $user['id']);

        session()->set([
            'isLoggedIn' => true,
            'user_id' => (int) $user['id'],
            'username' => $user['username'],
            'account_status' => $user['account_status'],
            'roles' => array_column($roles, 'key'),
            'permissions' => $permissions,
        ]);

        $userModel->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        (new AuditService())->log((int) $user['id'], 'login', 'auth', 'users', (int) $user['id']);

        return redirect()->to(site_url('dashboard'));
    }

    public function register(): string
    {
        return $this->registerView(
            'Daftar Akun | BegawiPBJ',
            'Buat Akun BegawiPBJ',
            'Langkah 1 dari 4 — Lengkapi akun dasar dan pilih tipe profil Anda.',
            'register',
            1,
            'akun',
            ['registrationData' => session('registration_data') ?? []]
        );
    }

    public function registerAddress(): string
    {
        return $this->registerView(
            'Wilayah dan Alamat | BegawiPBJ',
            'Wilayah dan Alamat Ringkas',
            'Langkah 2 dari 4 — Lengkapi wilayah domisili dan alamat ringkas Anda.',
            'register_address',
            2,
            'wilayah',
            [
                'provinces' => $this->wilayahChildren(),
                'registrationAddress' => session('registration_address') ?? [],
            ]
        );
    }

    public function wilayahOptions(?string $parentCode = null)
    {
        $rows = $this->wilayahChildren($parentCode);

        return $this->response->setJSON(array_map(static fn(array $row): array => [
            'code' => $row['kode'],
            'name' => $row['nama'],
        ], $rows));
    }

    public function registerAsn(): string
    {
        return $this->registerView(
            'Profil ASN | BegawiPBJ',
            'Lengkapi Profil ASN',
            'Langkah 3 dari 4 — Lengkapi data kepegawaian dan instansi Anda.',
            'register_asn',
            3,
            'profil',
            ['profileType' => 'ASN']
        );
    }

    public function registerProvider(): string
    {
        return $this->registerView(
            'Profil Penyedia Barang/Jasa | BegawiPBJ',
            'Lengkapi Profil Penyedia Barang/Jasa',
            'Langkah 3 dari 4 — Lengkapi data badan usaha dan penanggung jawab.',
            'register_provider',
            3,
            'profil',
            ['profileType' => 'Penyedia Barang/Jasa']
        );
    }

    public function registerAcademic(): string
    {
        return $this->registerView(
            'Profil Akademisi | BegawiPBJ',
            'Lengkapi Profil Akademisi',
            'Langkah 3 dari 4 — Lengkapi institusi, peran, dan bidang keahlian Anda.',
            'register_academic',
            3,
            'profil',
            ['profileType' => 'Akademisi']
        );
    }

    public function registerPublic(): string
    {
        return $this->registerView(
            'Profil Masyarakat Umum | BegawiPBJ',
            'Lengkapi Profil Masyarakat Umum',
            'Langkah 3 dari 4 — Lengkapi pekerjaan atau status serta minat Anda.',
            'register_public',
            3,
            'profil',
            ['profileType' => 'Masyarakat Umum']
        );
    }

    public function registerReview(): string
    {
        return $this->registerView(
            'Tinjau Pendaftaran | BegawiPBJ',
            'Tinjau Data Pendaftaran',
            'Langkah 4 dari 4 — Pastikan seluruh data yang Anda masukkan sudah benar.',
            'register_review',
            4,
            'tinjau',
            [
                'registrationData' => session('registration_data') ?? [],
                'registrationAddress' => session('registration_address') ?? [],
                'registrationProfile' => session('registration_profile') ?? [],
            ]
        );
    }

    public function registerSuccess(): string
    {
        return view('dashboard/auth/register_success', [
            'title' => 'Pendaftaran Berhasil | BegawiPBJ',
            'pageTitle' => 'Pendaftaran Berhasil Dikirim',
            'pageDescription' => 'Akun Anda sedang menunggu verifikasi email dan pemeriksaan Operator/Admin.',
        ]);
    }

    public function forgotPassword(): string
    {
        return $this->authView(
            'Lupa Kata Sandi | BegawiPBJ',
            'Lupa Kata Sandi',
            'Masukkan alamat email akun Anda untuk menerima instruksi pengaturan ulang kata sandi.',
            'forgot_password'
        );
    }

    public function logout()
    {
        $userId = session()->get('user_id');

        if ($userId) {
            (new AuditService())->log((int) $userId, 'logout', 'auth', 'users', (int) $userId);
        }

        session()->destroy();

        return redirect()->to(site_url('dashboard/masuk'))->with('message', 'Anda telah keluar dari akun.');
    }

    public function logoutPreview()
    {
        return $this->logout();
    }

    private function authView(
        string $title,
        string $pageTitle,
        string $pageDescription,
        string $viewName
    ): string {
        return view('dashboard/auth/' . $viewName, [
            'title' => $title,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
        ]);
    }

    public function registerAttempt()
    {
        $rules = [
            'full_name' => 'required|max_length[150]',
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'phone' => 'required|max_length[25]',
            'password' => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
            'password_confirmation' => 'required|matches[password]',
            'profile_type' => 'required|in_list[asn,provider,academic,public]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        session()->set('registration_data', $this->request->getPost([
            'full_name',
            'username',
            'email',
            'phone',
            'password',
            'profile_type',
        ]));

        return redirect()->to(site_url('dashboard/daftar/wilayah'));
    }

    public function registerAddressAttempt()
    {
        if (! session()->has('registration_data')) {
            return redirect()->to(site_url('dashboard/daftar'));
        }

        if (! $this->validate([
            'province' => 'required',
            'regency' => 'required',
            'district' => 'required',
            'address' => 'required|max_length[500]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $address = $this->request->getPost([
            'province',
            'regency',
            'district',
            'village',
            'address',
        ]);
        if (! $this->validWilayahChain($address['province'], $address['regency'], $address['district'], $address['village'] ?? null)) {
            return redirect()->back()->withInput()->with('error', 'Pilihan wilayah tidak berjenjang atau sudah tidak tersedia.');
        }
        session()->set('registration_address', $address);
        $profileRoute = [
            'asn' => 'asn',
            'provider' => 'penyedia',
            'academic' => 'akademisi',
            'public' => 'masyarakat',
        ][session('registration_data.profile_type')] ?? 'masyarakat';

        return redirect()->to(site_url('dashboard/daftar/profil/' . $profileRoute));
    }

    public function registerProfileAttempt(string $type)
    {
        $type = ['asn' => 'asn', 'penyedia' => 'provider', 'akademisi' => 'academic', 'masyarakat' => 'public'][$type] ?? null;
        if (! $type || ! session()->has('registration_data') || ! session()->has('registration_address')) {
            return redirect()->to(site_url('dashboard/daftar'));
        }

        session()->set('registration_profile', $this->request->getPost());
        session()->set('registration_data.profile_type', $type);

        return redirect()->to(site_url('dashboard/daftar/tinjau'));
    }

    public function registerReviewAttempt()
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
            'name'           => trim($data['full_name']),
            'username'       => trim($data['username']),
            'email'          => strtolower(trim($data['email'])),
            'phone'          => trim($data['phone']),
            'password_hash'  => password_hash($data['password'], PASSWORD_DEFAULT),
            'profile_type'   => $data['profile_type'],
            'account_status' => 'pending_verification',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], true);
        $db->table('user_profiles')->insert([
            'user_id' => $userId,
            'address_line' => trim($address['address']),
            'province_code' => $address['province'],
            'regency_code' => $address['regency'],
            'district_code' => $address['district'],
            'village_code' => $address['village'] ?: null,
            'profile_completion_status' => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $profileTable = [
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
        ][$data['profile_type']];
        $profileRow = ['user_id' => $userId];
        if ($data['profile_type'] === 'asn') {
            $profileRow += ['nip' => $profile['nip_nippk'] ?: null, 'employment_status' => $profile['employment_status'], 'position_name' => $profile['position'] ?: null, 'rank_name' => $profile['rank'] ?: null];
        } elseif ($data['profile_type'] === 'provider') {
            $profileRow += ['nib' => $profile['nib'] ?: null, 'business_name' => $profile['company_name'], 'responsible_person' => $profile['responsible_person']];
        } elseif ($data['profile_type'] === 'academic') {
            $profileRow += [
                'institution' => $profile['institution_name'] ?? '',
                'expertise' => is_array($profile['expertise'] ?? null) ? implode(', ', $profile['expertise']) : ($profile['expertise'] ?? ''),
            ];
        } else {
            $profileRow += ['identity_number' => $profile['nik'] ?: null];
        }
        $db->table($profileTable)->insert($profileRow);
        $this->issueToken((int) $userId);
        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Pendaftaran gagal disimpan. Silakan periksa kembali data Anda.');
        }
        (new AuditService())->log((int) $userId, 'register', 'auth', 'users', (int) $userId);
        session()->remove(['registration_data', 'registration_address', 'registration_profile']);

        return redirect()->to(site_url('dashboard/daftar/berhasil'));
    }

    public function forgotPasswordAttempt()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Alamat email tidak valid.');
        }

        $user = (new UserModel())->findByEmail($email);
        if ($user) {
            $this->issueToken((int) $user['id'], $email, 'password_reset');
            (new AuditService())->log((int) $user['id'], 'password_reset_requested', 'auth', 'users', (int) $user['id']);
        }

        return redirect()->back()->with('message', 'Jika email terdaftar, instruksi pengaturan ulang akan diproses.');
    }

    public function resetPassword(string $token)
    {
        $tokenRow = $this->findToken($token, 'password_reset');
        if (! $tokenRow) {
            return redirect()->to(site_url('dashboard/lupa-kata-sandi'))->with('error', 'Token reset tidak valid atau sudah kedaluwarsa.');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'password' => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
                'password_confirmation' => 'required|matches[password]',
            ];
            if (! $this->validate($rules)) {
                return redirect()->back()->with('errors', $this->validator->getErrors());
            }

            (new UserModel())->update((int) $tokenRow['user_id'], [
                'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);
            (new AuthTokenModel())->update((int) $tokenRow['id'], ['used_at' => date('Y-m-d H:i:s')]);
            (new AuditService())->log((int) $tokenRow['user_id'], 'password_reset_completed', 'auth', 'users', (int) $tokenRow['user_id']);

            return redirect()->to(site_url('dashboard/masuk'))->with('message', 'Kata sandi berhasil diubah. Silakan masuk.');
        }

        return view('dashboard/auth/reset_password', ['title' => 'Reset Kata Sandi | BegawiPBJ', 'token' => $token]);
    }

    public function verifyEmail(string $token)
    {
        $tokenRow = $this->findToken($token, 'email_verification');
        if (! $tokenRow) {
            return redirect()->to(site_url('dashboard/status-akun'))->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        (new UserModel())->update((int) $tokenRow['user_id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'account_status' => 'submitted',
        ]);
        (new AuthTokenModel())->update((int) $tokenRow['id'], ['used_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(site_url('dashboard/status-akun'))->with('message', 'Email berhasil diverifikasi. Akun menunggu pemeriksaan operator.');
    }

    public function accountStatus(): string
    {
        $user = session()->get('user_id') ? (new UserModel())->find((int) session()->get('user_id')) : null;

        return view('dashboard/auth/account_status', [
            'title' => 'Status Akun | BegawiPBJ',
            'user' => $user,
        ]);
    }


    private function registerView(
        string $title,
        string $pageTitle,
        string $pageDescription,
        string $viewName,
        int $currentStep,
        string $currentKey,
        array $data = []
    ): string {
        return view('dashboard/auth/' . $viewName, array_merge([
            'title' => $title,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'currentStep' => $currentStep,
            'currentKey' => $currentKey,
            'registrationProfile' => session('registration_profile') ?? [],
        ], $data));
    }

    private function wilayahChildren(?string $parentCode = null): array
    {
        $builder = db_connect()->table('wilayah')->select('kode, nama')->orderBy('nama', 'ASC');
        if ($parentCode === null || $parentCode === '') {
            $builder->where('LENGTH(kode)', 2, false);
        } else {
            $builder->like('kode', rtrim($parentCode, '.') . '.', 'after');
            $childLength = strlen($parentCode) === 8 ? 13 : strlen($parentCode) + 3;
            $builder->where('LENGTH(kode)', $childLength, false);
        }

        return $builder->get()->getResultArray();
    }

    private function validWilayahChain(string $province, string $regency, string $district, ?string $village): bool
    {
        if (strlen($province) !== 2 || strlen($regency) !== 5 || strlen($district) !== 8) {
            return false;
        }
        if (strpos($regency, $province . '.') !== 0 || strpos($district, $regency . '.') !== 0) {
            return false;
        }
        if ($village !== null && $village !== '' && strpos($village, $district . '.') !== 0) {
            return false;
        }

        $codes = array_filter([$province, $regency, $district, $village]);
        return count($codes) === db_connect()->table('wilayah')->whereIn('kode', $codes)->countAllResults();
    }

    private function issueToken(int $userId): string
    {
        $plainToken = bin2hex(random_bytes(32));

        db_connect()->table('email_verifications')->insert([
            'user_id'    => $userId,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $plainToken;
    }

    private function findToken(string $plainToken, string $type): ?array
    {
        return (new AuthTokenModel())
            ->where('token_hash', hash('sha256', $plainToken))
            ->where('token_type', $type)
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();
    }
}
