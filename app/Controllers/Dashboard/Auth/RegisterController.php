<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Models\WilayahModel;
use App\Services\Identity\RegistrationService;

class RegisterController extends BaseController
{
    /**
     * Canonical profile-type registry.
     *
     * Single source of truth for every registration profile variant. Adding,
     * renaming, or retiring a profile type only requires editing this array.
     *
     *   - profile_type   internal value stored on users.profile_type and the
     *                    RegistrationService payload
     *   - segment        URL segment used by dashboard/daftar/profil/{segment}
     *   - view           profile form view (mint or redirect target)
     *   - table          profile-specific table the row is written into
     *   - label          human-readable label shown in the UI
     *   - page_title     page heading shown on the profile form
     *   - fields         whitelist of POST keys accepted on step 3 submit
     *   - required        keys that must be non-empty after step 3 submit
     *   - json_fields    keys whose values must be JSON-encoded
     */
    private const PROFILE_REGISTRY = [
        'asn' => [
            'profile_type' => 'asn',
            'segment'      => 'asn',
            'view'         => 'dashboard/auth/register_asn',
            'table'        => 'asn_profiles',
            'label'        => 'ASN',
            'page_title'   => 'Profil ASN',
            'fields'       => [
                'asn_origin',
                'nip_nippk',
                'employment_status',
                'organization_unit_id',
                'institution_name',
                'external_unit_name',
                'institution_province',
                'institution_regency',
                'institution_district_village',
                'position',
                'rank',
                'official_email',
                'has_procurement_role',
                'procurement_roles',
            ],
            'required'     => ['asn_origin', 'employment_status', 'position'],
            'json_fields'  => ['procurement_roles'],
        ],
        'provider' => [
            'profile_type' => 'provider',
            'segment'      => 'penyedia',
            'view'         => 'dashboard/auth/register_provider',
            'table'        => 'provider_profiles',
            'label'        => 'Penyedia Barang/Jasa',
            'page_title'   => 'Profil Penyedia Barang/Jasa',
            'fields'       => [
                'nik',
                'company_name',
                'business_entity_type',
                'registrant_position',
                'responsible_person',
                'nib',
                'company_email',
                'company_address',
            ],
            'required'     => ['company_name', 'business_entity_type', 'registrant_position', 'responsible_person'],
            'json_fields'  => [],
        ],
        'academic' => [
            'profile_type' => 'academic',
            'segment'      => 'akademisi',
            'view'         => 'dashboard/auth/register_academic',
            'table'        => 'academic_profiles',
            'label'        => 'Akademisi',
            'page_title'   => 'Profil Akademisi',
            'fields'       => [
                'nik',
                'institution_name',
                'institution_type',
                'academic_role',
                'expertise',
                'institution_email',
                'professional_identifier',
                'portfolio_url',
            ],
            'required'     => ['institution_name', 'institution_type', 'academic_role'],
            'json_fields'  => ['expertise'],
        ],
        'public' => [
            'profile_type' => 'public',
            'segment'      => 'masyarakat',
            'view'         => 'dashboard/auth/register_public',
            'table'        => 'public_profiles',
            'label'        => 'Masyarakat Umum',
            'page_title'   => 'Profil Masyarakat Umum',
            'fields'       => [
                'nik',
                'occupation_status',
                'organization_name',
                'procurement_interest',
                'usage_purpose',
            ],
            'required'     => ['occupation_status'],
            'json_fields'  => ['procurement_interest', 'usage_purpose'],
        ],
    ];

    private function registry(?string $profileType = null): array
    {
        if ($profileType === null) {
            return self::PROFILE_REGISTRY;
        }
        return self::PROFILE_REGISTRY[$profileType] ?? [];
    }

    private function segmentFor(string $profileType): ?string
    {
        return self::PROFILE_REGISTRY[$profileType]['segment'] ?? null;
    }

    private function profileTypeForSegment(string $segment): ?string
    {
        foreach (self::PROFILE_REGISTRY as $profileType => $cfg) {
            if ($cfg['segment'] === $segment) {
                return $profileType;
            }
        }
        return null;
    }

    public function index()
    {
        $options = array_map(static fn(array $cfg): array => [
            'value' => $cfg['profile_type'],
            'label' => $cfg['label'],
        ], self::PROFILE_REGISTRY);

        return view('dashboard/auth/register', [
            'provinces' => (new WilayahModel())->provinces(),
            'profileOptions' => $options,
        ]);
    }

    public function attempt()
    {
        $data = (array) ($this->request->getPost() ?: $_POST);

        $allowedProfileTypes = array_keys(self::PROFILE_REGISTRY);

        $rules = [
            'full_name'             => 'required|min_length[3]|max_length[150]',
            'username'              => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'                 => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'phone'                 => 'required|min_length[8]|max_length[20]',
            'password'              => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/\d/]|regex_match[/[^\w\s]/]',
            'password_confirmation' => 'required|matches[password]',
            'profile_type'          => 'required|in_list[' . implode(',', $allowedProfileTypes) . ']',
        ];

        $messages = [
            'full_name' => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
            ],
            'username' => [
                'required'   => 'Username akun wajib diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'is_unique'  => 'Username ini sudah digunakan oleh akun lain.',
            ],
            'email' => [
                'required'    => 'Alamat email wajib diisi.',
                'valid_email' => 'Format alamat email tidak valid.',
                'is_unique'   => 'Alamat email ini sudah terdaftar di sistem.',
            ],
            'phone' => [
                'required' => 'Nomor telepon/HP wajib diisi.',
            ],
            'password' => [
                'required'    => 'Kata sandi wajib diisi.',
                'min_length'  => 'Kata sandi minimal harus 12 karakter.',
                'regex_match' => 'Kata sandi wajib memadukan huruf besar, huruf kecil, angka, dan simbol/karakter khusus.',
            ],
            'password_confirmation' => [
                'required' => 'Konfirmasi kata sandi wajib diisi.',
                'matches'  => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
            ],
            'profile_type' => [
                'required' => 'Silakan pilih tipe profil pendaftaran Anda.',
                'in_list'  => 'Tipe profil yang dipilih tidak valid.',
            ],
        ];

        if (! $this->validateData($data, $rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $profileType = (string) $data['profile_type'];
        if (! isset(self::PROFILE_REGISTRY[$profileType])) {
            return redirect()->back()->withInput()
                ->with('error', 'Tipe profil tidak dikenali.');
        }

        $existing = session('registration_data') ?? [];
        $incoming = [
            'full_name'    => trim((string) ($data['full_name'] ?? '')),
            'username'     => trim((string) ($data['username'] ?? '')),
            'email'        => trim((string) ($data['email'] ?? '')),
            'phone'        => trim((string) ($data['phone'] ?? '')),
            'password'     => (string) ($data['password'] ?? ''),
            'profile_type' => $profileType,
        ];

        foreach ($incoming as $key => $value) {
            $existing[$key] = $value;
        }

        session()->set('registration_data', $existing);

        return redirect()->to(site_url('dashboard/daftar/wilayah'));
    }

    public function registerAddress()
    {
        return view('dashboard/auth/register_address', [
            'title' => 'Wilayah dan Alamat | BegawiPengadaan',
            'pageTitle' => 'Wilayah dan Alamat Ringkas',
            'pageDescription' => 'Langkah 2 dari 4 — Lengkapi wilayah domisili dan alamat ringkas Anda.',
            'currentStep' => 2,
            'currentKey' => 'wilayah',
            'provinces' => (new WilayahModel())->provinces(),
        ]);
    }

    public function registerAddressAttempt()
    {
        if (! session()->has('registration_data')) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Silakan lengkapi data akun dasar terlebih dahulu.');
        }

        $rules = [
            'province' => 'required',
            'regency'  => 'required',
            'district' => 'required',
            'address'  => 'required|max_length[500]',
        ];

        if (! $this->validateData((array) $this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $address = $this->request->getPost([
            'province',
            'regency',
            'district',
            'village',
            'address',
        ]);

        if (! $this->isValidWilayahChain(
            (string) $address['province'],
            (string) $address['regency'],
            (string) $address['district'],
            isset($address['village']) ? (string) $address['village'] : null
        )) {
            return redirect()->back()->withInput()
                ->with('error', 'Pilihan wilayah tidak berjenjang atau sudah tidak tersedia.');
        }

        session()->set('registration_address', $address);

        $profileType = (string) (session('registration_data.profile_type') ?? '');
        $segment     = $this->segmentFor($profileType);

        if ($segment === null) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Tipe profil tidak valid. Silakan ulangi langkah 1.');
        }

        return redirect()->to(site_url('dashboard/daftar/profil/' . $segment));
    }

    public function wilayahOptions(?string $parentCode = null)
    {
        $rows = (new WilayahModel())->children($parentCode ?? '');

        return $this->response->setJSON(array_map(static fn(array $row): array => [
            'code' => $row['kode'],
            'name' => $row['nama'],
        ], $rows));
    }

    public function registerAsn()
    {
        return $this->renderProfileStep('asn');
    }

    public function registerProvider()
    {
        return $this->renderProfileStep('penyedia');
    }

    public function registerAcademic()
    {
        return $this->renderProfileStep('akademisi');
    }

    public function registerPublic()
    {
        return $this->renderProfileStep('masyarakat');
    }

    private function renderProfileStep(string $segment): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $profileType = $this->profileTypeForSegment($segment);

        if ($profileType === null) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Segment profil tidak dikenali.');
        }

        if (! session()->has('registration_data') || ! session()->has('registration_address')) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Lengkapi data akun dasar dan wilayah sebelum mengisi profil.');
        }

        $sessionProfileType = (string) (session('registration_data.profile_type') ?? '');

        if ($sessionProfileType !== $profileType) {
            $correctSegment = $this->segmentFor($sessionProfileType);
            if ($correctSegment === null) {
                return redirect()->to(site_url('dashboard/daftar'))
                    ->with('error', 'Tipe profil pada langkah 1 tidak valid. Silakan ulangi.');
            }
            return redirect()->to(site_url('dashboard/daftar/profil/' . $correctSegment))
                ->with('error', 'Halaman profil tidak sesuai dengan tipe profil yang dipilih.');
        }

        $cfg = self::PROFILE_REGISTRY[$profileType];

        $extraData = [];
        if ($segment === 'asn') {
            $extraData['organizationUnits'] = (new \App\Models\OrganizationUnitModel())
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('name', 'ASC')
                ->findAll();
            $extraData['procurementRoles'] = (new \App\Models\ProcurementRoleModel())
                ->where('is_active', 1)
                ->orderBy('id', 'ASC')
                ->findAll();
            $extraData['registrationProfile'] = session('registration_profile.fields') ?? [];
        }

        return view($cfg['view'], array_merge([
            'title' => $cfg['page_title'] . ' | BegawiPengadaan',
            'pageTitle' => $cfg['page_title'],
            'pageDescription' => 'Langkah 3 dari 4 — Lengkapi data spesifik untuk tipe profil Anda.',
            'currentStep' => 3,
            'currentKey' => 'profil',
            'profileType' => $cfg['label'],
            'profileTable' => $cfg['table'],
        ], $extraData));
    }

    public function registerProfileAttempt(string $type)
    {
        $profileType = $this->profileTypeForSegment($type);

        if (
            $profileType === null
            || ! session()->has('registration_data')
            || ! session()->has('registration_address')
        ) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Lengkapi data akun dasar dan wilayah sebelum mengisi profil.');
        }

        $sessionProfileType = (string) (session('registration_data.profile_type') ?? '');

        if ($sessionProfileType !== $profileType) {
            $correctSegment = $this->segmentFor($sessionProfileType);
            if ($correctSegment === null) {
                return redirect()->to(site_url('dashboard/daftar'))
                    ->with('error', 'Tipe profil pada langkah 1 tidak valid. Silakan ulangi.');
            }
            return redirect()->to(site_url('dashboard/daftar/profil/' . $correctSegment))
                ->with('error', 'Tipe profil pada langkah 1 tidak sesuai dengan halaman yang dibuka.');
        }

        $cfg = self::PROFILE_REGISTRY[$profileType];
        $incoming = (array) ($this->request->getPost() ?: $_POST);
        $sanitized = $this->extractProfileFields($profileType, is_array($incoming) ? $incoming : []);

        if ($profileType === 'asn') {
            $origin = (string) ($sanitized['asn_origin'] ?? '');
            if ($origin === 'pemprov_lampung') {
                $opdId = (int) ($sanitized['organization_unit_id'] ?? 0);
                if ($opdId <= 0) {
                    return redirect()->to(site_url('dashboard/daftar/profil/asn'))
                        ->withInput()
                        ->with('error', 'Silakan pilih OPD/Perangkat Daerah Pemerintah Provinsi Lampung.');
                }
                $opd = (new \App\Models\OrganizationUnitModel())->find($opdId);
                if (! $opd || empty($opd['is_active'])) {
                    return redirect()->to(site_url('dashboard/daftar/profil/asn'))
                        ->withInput()
                        ->with('error', 'OPD/Perangkat Daerah yang dipilih tidak valid atau sedang tidak aktif.');
                }
                $sanitized['organization_unit_id'] = $opd['id'];
                $sanitized['institution_name']      = 'Pemerintah Daerah Provinsi Lampung';
                $sanitized['external_unit_name']    = $opd['name'];
            }
        }

        $missing = [];
        foreach ($cfg['required'] as $requiredKey) {
            $value = $sanitized[$requiredKey] ?? null;
            if (is_array($value)) {
                if ($value === []) {
                    $missing[] = $requiredKey;
                }
            } elseif ($value === null || (is_string($value) && trim($value) === '')) {
                $missing[] = $requiredKey;
            }
        }

        if ($missing !== []) {
            return redirect()->to(site_url('dashboard/daftar/profil/' . $cfg['segment']))
                ->withInput()
                ->with('errors', ['Field berikut wajib diisi: ' . implode(', ', $missing)]);
        }

        session()->set('registration_profile', [
            'profile_type' => $profileType,
            'table'        => $cfg['table'],
            'fields'       => $sanitized,
        ]);

        return redirect()->to(site_url('dashboard/daftar/tinjau'));
    }

    public function registerReview()
    {
        if (
            ! session()->has('registration_data')
            || ! session()->has('registration_address')
            || ! session()->has('registration_profile')
        ) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Data pendaftaran belum lengkap. Silakan ulangi dari langkah pertama.');
        }

        $wilayah = new WilayahModel();
        $address = session('registration_address');

        $addressDisplay = [
            'province_code' => $address['province'] ?? '',
            'regency_code'  => $address['regency'] ?? '',
            'district_code' => $address['district'] ?? '',
            'village_code'  => $address['village'] ?? null,
            'address_line'  => $address['address'] ?? '',
            'province'      => $wilayah->nameOf($address['province'] ?? null) ?? ($address['province'] ?? '-'),
            'regency'       => $wilayah->nameOf($address['regency']  ?? null) ?? ($address['regency']  ?? '-'),
            'district'      => $wilayah->nameOf($address['district'] ?? null) ?? ($address['district'] ?? '-'),
            'village'       => $wilayah->nameOf($address['village']  ?? null) ?? ($address['village']  ?? null),
            'address'       => $address['address'] ?? '',
        ];

        $profileType = (string) (session('registration_data.profile_type') ?? '');
        $cfg         = self::PROFILE_REGISTRY[$profileType] ?? null;

        $fields = session('registration_profile.fields') ?? [];
        if ($profileType === 'asn') {
            if (! empty($fields['organization_unit_id'])) {
                $opd = (new \App\Models\OrganizationUnitModel())->find((int) $fields['organization_unit_id']);
                if ($opd) {
                    $fields['opd_name'] = $opd['name'];
                }
            }
            if (! empty($fields['procurement_roles'])) {
                $rawRoles = $fields['procurement_roles'];
                if (is_string($rawRoles)) {
                    $decoded = json_decode($rawRoles, true);
                    $list = is_array($decoded) ? $decoded : [$rawRoles];
                } elseif (is_array($rawRoles)) {
                    $list = $rawRoles;
                } else {
                    $list = [];
                }
                $roleNames = [];
                foreach ($list as $rItem) {
                    if (empty($rItem)) continue;
                    if (is_numeric($rItem)) {
                        $r = (new \App\Models\ProcurementRoleModel())->find((int) $rItem);
                        if ($r) $roleNames[] = $r['name'];
                    } else {
                        $r = (new \App\Models\ProcurementRoleModel())->where('key', (string) $rItem)->first();
                        if ($r) $roleNames[] = $r['name'];
                    }
                }
                $fields['procurement_role_names'] = $roleNames;
            }
        }

        $profileDisplay = [
            'profile_type' => $profileType,
            'label'        => $cfg['label'] ?? '-',
            'table'        => $cfg['table'] ?? '-',
            'fields'       => $fields,
        ];

        return view('dashboard/auth/register_review', [
            'title' => 'Tinjau Pendaftaran | BegawiPengadaan',
            'pageTitle' => 'Tinjau Data Pendaftaran',
            'pageDescription' => 'Langkah 4 dari 4 — Pastikan seluruh data yang Anda masukkan sudah benar.',
            'currentStep' => 4,
            'currentKey' => 'tinjau',
            'registrationData'    => session('registration_data'),
            'registrationAddress' => $addressDisplay,
            'registrationProfile' => $profileDisplay,
            'profileSegment'      => $cfg['segment'] ?? 'asn',
        ]);
    }

    public function registerReviewAttempt()
    {
        $data    = session('registration_data');
        $address = session('registration_address');
        $profile = session('registration_profile');

        if (! $data || ! $address || ! $profile) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Data pendaftaran tidak lengkap. Silakan ulangi dari langkah pertama.');
        }

        if (! $this->request->getPost('terms') || ! $this->request->getPost('privacy')) {
            return redirect()->back()
                ->with('error', 'Anda harus menyetujui Syarat Penggunaan dan Kebijakan Privasi.');
        }

        $profileType = (string) ($data['profile_type'] ?? '');
        $cfg         = self::PROFILE_REGISTRY[$profileType] ?? null;

        if ($cfg === null) {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Tipe profil tidak valid.');
        }

        $province = (string) ($address['province'] ?? '');
        $regency  = (string) ($address['regency']  ?? '');
        $district = (string) ($address['district'] ?? '');
        $village  = $address['village'] ?? null;

        if ($profileType === '' || $province === '' || $regency === '' || $district === '') {
            return redirect()->to(site_url('dashboard/daftar'))
                ->with('error', 'Data wilayah atau tipe profil belum lengkap. Silakan ulangi dari langkah pertama.');
        }

        $profileFields = is_array($profile['fields'] ?? null) ? $profile['fields'] : [];

        $payload = [
            'name'          => trim((string) ($data['full_name'] ?? '')),
            'username'      => trim((string) ($data['username'] ?? '')),
            'email'         => trim((string) ($data['email'] ?? '')),
            'phone'         => trim((string) ($data['phone'] ?? '')),
            'password'      => (string) ($data['password'] ?? ''),
            'profile_type'  => $cfg['profile_type'],
            'address_line'  => trim((string) ($address['address'] ?? '')),
            'province_code' => $province,
            'regency_code'  => $regency,
            'district_code' => $district,
            'village_code'  => ($village === '' ? null : $village),
            'profile'       => $profileFields,
        ];

        log_message('debug', '[Register payload] profile_type=' . $cfg['profile_type']
            . ' table=' . $cfg['table']
            . ' profile_keys=' . json_encode(array_keys($profileFields), JSON_UNESCAPED_UNICODE)
            . ' payload=' . json_encode($payload, JSON_UNESCAPED_UNICODE));

        try {
            (new RegistrationService())->register($payload);
        } catch (\Throwable $e) {
            log_message('error', '[Register] submit failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Pendaftaran gagal diproses. Silakan periksa kembali data Anda.');
        }

        session()->remove(['registration_data', 'registration_address', 'registration_profile']);

        return redirect()->to(site_url('dashboard/daftar/berhasil'))
            ->with('message', 'Pendaftaran berhasil dikirim. Mohon tunggu proses verifikasi Operator/Admin.');
    }

    public function registerSuccess()
    {
        return view('dashboard/auth/register_success', [
            'title' => 'Pendaftaran Berhasil | BegawiPengadaan',
            'pageTitle' => 'Pendaftaran Berhasil Dikirim',
            'pageDescription' => 'Akun Anda sedang menunggu verifikasi Operator/Admin.',
        ]);
    }

    private function extractProfileFields(string $profileType, array $incoming): array
    {
        $cfg = self::PROFILE_REGISTRY[$profileType] ?? null;
        if ($cfg === null) {
            return [];
        }

        $out = [];
        foreach ($cfg['fields'] as $field) {
            if (! array_key_exists($field, $incoming)) {
                continue;
            }
            $value = $incoming[$field];

            if (in_array($field, $cfg['json_fields'], true)) {
                $out[$field] = $this->encodeJsonArray($value);
            } elseif (is_array($value)) {
                $out[$field] = array_map(static fn($v) => (string) $v, $value);
            } elseif (is_string($value)) {
                $out[$field] = trim($value);
            } else {
                $out[$field] = $value;
            }
        }

        return $out;
    }

    private function encodeJsonArray(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            return json_encode(array_values($value), JSON_THROW_ON_ERROR);
        }
        return json_encode([(string) $value], JSON_THROW_ON_ERROR);
    }

    private function isValidWilayahChain(string $province, string $regency, string $district, ?string $village): bool
    {
        if (strlen($province) !== 2 || strlen($regency) !== 5 || strlen($district) !== 8) {
            return false;
        }

        if (! str_starts_with($regency, $province . '.') || ! str_starts_with($district, $regency . '.')) {
            return false;
        }

        if ($village !== null && $village !== '' && ! str_starts_with($village, $district . '.')) {
            return false;
        }

        $codes = array_filter([$province, $regency, $district, $village]);
        if ($codes === []) {
            return false;
        }

        return count($codes) === db_connect()->table('wilayah')->whereIn('kode', $codes)->countAllResults();
    }
}
