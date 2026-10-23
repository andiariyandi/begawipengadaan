<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuditService;
use App\Services\SystemSettingService;

class ProfileController extends BaseController
{
    protected UserModel $userModel;
    protected AuditService $auditService;
    protected SystemSettingService $settingService;

    public function __construct()
    {
        $this->userModel      = new UserModel();
        $this->auditService   = new AuditService();
        $this->settingService = new SystemSettingService();
    }

    /**
     * Display the main profile overview page.
     */
    public function index(): string
    {
        $userData = $this->getCurrentUserData();

        return view('dashboard/profile/index', array_merge($userData, [
            'title'         => 'Profil Saya | BegawiPengadaan',
            'pageTitle'     => 'Profil Saya',
            'activeMenu'    => 'profile',
            'activeSubmenu' => 'profile',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Akun Saya'],
                ['label' => 'Profil Saya'],
            ],
        ]));
    }

    /**
     * Display the profile edit form.
     */
    public function edit(): string
    {
        $userData = $this->getCurrentUserData();

        return view('dashboard/profile/edit', array_merge($userData, [
            'title'         => 'Ubah Profil | BegawiPengadaan',
            'pageTitle'     => 'Ubah Profil',
            'activeMenu'    => 'profile',
            'activeSubmenu' => 'profile',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Akun Saya', 'url' => site_url('dashboard/profile')],
                ['label' => 'Ubah Profil'],
            ],
        ]));
    }

    /**
     * Process profile edit submission.
     */
    public function update()
    {
        $user = $this->getCurrentUser();
        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Sesi tidak valid.');
        }

        $userId = (int) $user['id'];

        $rules = [
            'name'          => 'required|min_length[2]|max_length[150]',
            'username'      => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,' . $userId . ']',
            'email'         => 'required|valid_email|max_length[150]|is_unique[users.email,id,' . $userId . ']',
            'phone'         => 'permit_empty|max_length[32]',
            'unit_name'     => 'permit_empty|max_length[255]',
            'position_name' => 'permit_empty|max_length[255]',
            'address_line'  => 'permit_empty|max_length[500]',
        ];

        if (! $this->validateData((array) $this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        // 1. Update basic user data
        $name     = trim((string) $this->request->getPost('name'));
        $username = trim((string) $this->request->getPost('username'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $phone    = trim((string) $this->request->getPost('phone'));

        $this->userModel->skipValidation(true)->update($userId, [
            'name'       => $name,
            'username'   => $username,
            'email'      => $email,
            'phone'      => $phone,
            'updated_at' => $now,
        ]);

        // 2. Update or insert address in user_profiles
        $addressLine = trim((string) $this->request->getPost('address_line'));
        if ($addressLine !== '') {
            $existingProfile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
            if ($existingProfile) {
                $db->table('user_profiles')->where('user_id', $userId)->update([
                    'address_line' => $addressLine,
                    'updated_at'   => $now,
                ]);
            } else {
                $db->table('user_profiles')->insert([
                    'user_id'                   => $userId,
                    'address_line'              => $addressLine,
                    'province_code'             => '18',
                    'regency_code'              => '18.71',
                    'district_code'             => '18.71.01',
                    'profile_completion_status' => 'complete',
                    'created_at'                => $now,
                    'updated_at'                => $now,
                ]);
            }
        }

        // 3. Update unit_name and position_name in asn_profiles if applicable
        $unitName     = trim((string) $this->request->getPost('unit_name'));
        $positionName = trim((string) $this->request->getPost('position_name'));

        if ($user['profile_type'] === 'asn' || ($unitName !== '' || $positionName !== '')) {
            $asnRow = $db->table('asn_profiles')->where('user_id', $userId)->get()->getRowArray();
            if ($asnRow) {
                $updateData = ['updated_at' => $now];
                if ($unitName !== '') {
                    $updateData['unit_name'] = $unitName;
                }
                if ($positionName !== '') {
                    $updateData['position_name'] = $positionName;
                }
                $db->table('asn_profiles')->where('user_id', $userId)->update($updateData);
            } elseif ($unitName !== '' || $positionName !== '') {
                $db->table('asn_profiles')->insert([
                    'user_id'       => $userId,
                    'origin_type'   => 'pemprov_lampung',
                    'unit_name'     => $unitName ?: 'Biro Pengadaan Barang dan Jasa',
                    'position_name' => $positionName ?: 'Pengelola Pengadaan',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        // 4. Update session data if current user is logged in
        if ((int) session()->get('user_id') === $userId) {
            session()->set([
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
            ]);
        }

        // 5. Audit log
        $this->auditService->log($userId, 'update_profile', 'profile', 'users', $userId);

        return redirect()->to(site_url('dashboard/profile'))->with('success', 'Profil akun Anda berhasil diperbarui.');
    }

    /**
     * Display change password form.
     */
    public function password(): string
    {
        $userData = $this->getCurrentUserData();

        return view('dashboard/profile/password', array_merge($userData, [
            'title'         => 'Ubah Kata Sandi | BegawiPengadaan',
            'pageTitle'     => 'Ubah Kata Sandi',
            'activeMenu'    => 'profile',
            'activeSubmenu' => 'password',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Akun Saya', 'url' => site_url('dashboard/profile')],
                ['label' => 'Ubah Kata Sandi'],
            ],
        ]));
    }

    /**
     * Process password change submission.
     */
    public function updatePassword()
    {
        $user = $this->getCurrentUser();
        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Sesi tidak valid.');
        }

        $userId = (int) $user['id'];

        $rules = [
            'current_password'      => 'required',
            'password'              => 'required|min_length[8]|max_length[255]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validateData((array) $this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('password');

        // Verify current password
        if (! password_verify($currentPassword, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->back()->withInput()->with('error', 'Kata sandi saat ini yang Anda masukkan tidak sesuai.');
        }

        // Prevent setting the exact same password
        if (password_verify($newPassword, (string) ($user['password_hash'] ?? ''))) {
            return redirect()->back()->withInput()->with('error', 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
        }

        // Update password hash
        $this->userModel->skipValidation(true)->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // Audit log
        $this->auditService->log($userId, 'change_password', 'auth', 'users', $userId);

        return redirect()->to(site_url('dashboard/profile'))->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan gunakan kata sandi baru untuk akses berikutnya.');
    }

    /**
     * Display account preferences page.
     */
    public function preferences(): string
    {
        $userData    = $this->getCurrentUserData();
        $userId      = (int) ($userData['user']['id'] ?? 0);
        $prefKey     = 'user_preferences_' . $userId;
        $savedPrefs  = $this->settingService->getGroup($prefKey);
        $sessPrefs   = (array) session()->get('user_preferences');

        $preferences = [
            'language'            => $sessPrefs['language'] ?? $savedPrefs['language'] ?? 'id',
            'theme'               => $sessPrefs['theme'] ?? $savedPrefs['theme'] ?? 'system',
            'email_notification'  => (bool) ($sessPrefs['email_notification'] ?? $savedPrefs['email_notification'] ?? true),
            'in_app_notification' => (bool) ($sessPrefs['in_app_notification'] ?? $savedPrefs['in_app_notification'] ?? true),
            'report_notification' => (bool) ($sessPrefs['report_notification'] ?? $savedPrefs['report_notification'] ?? false),
        ];

        return view('dashboard/profile/preferences', array_merge($userData, [
            'title'         => 'Preferensi Akun | BegawiPengadaan',
            'pageTitle'     => 'Preferensi Akun',
            'activeMenu'    => 'profile',
            'activeSubmenu' => 'preferences',
            'preferences'   => $preferences,
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Akun Saya', 'url' => site_url('dashboard/profile')],
                ['label' => 'Preferensi Akun'],
            ],
        ]));
    }

    /**
     * Process account preferences submission.
     */
    public function updatePreferences()
    {
        $user = $this->getCurrentUser();
        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Sesi tidak valid.');
        }

        $userId = (int) $user['id'];

        $language   = in_array($this->request->getPost('language'), ['id', 'en'], true) ? (string) $this->request->getPost('language') : 'id';
        $theme      = in_array($this->request->getPost('theme'), ['system', 'light', 'dark'], true) ? (string) $this->request->getPost('theme') : 'system';
        $emailNotif = (bool) $this->request->getPost('email_notification');
        $inAppNotif = (bool) $this->request->getPost('in_app_notification');
        $reportNotif = (bool) $this->request->getPost('report_notification');

        $prefData = [
            'language'            => $language,
            'theme'               => $theme,
            'email_notification'  => $emailNotif ? 1 : 0,
            'in_app_notification' => $inAppNotif ? 1 : 0,
            'report_notification' => $reportNotif ? 1 : 0,
        ];

        // Save to SystemSettingService
        $prefKey = 'user_preferences_' . $userId;
        $this->settingService->saveGroup($prefKey, $prefData);

        // Save to current session
        session()->set('user_preferences', [
            'language'            => $language,
            'theme'               => $theme,
            'email_notification'  => $emailNotif,
            'in_app_notification' => $inAppNotif,
            'report_notification' => $reportNotif,
        ]);

        // Audit log
        $this->auditService->log($userId, 'update_preferences', 'profile', 'users', $userId);

        return redirect()->to(site_url('dashboard/profile/preferences'))->with('success', 'Preferensi akun Anda berhasil disimpan.');
    }

    /**
     * Helper to get authenticated user or active user fallback.
     */
    private function getCurrentUser(): ?array
    {
        $userId = (int) session()->get('user_id');
        if ($userId > 0) {
            $user = $this->userModel->find($userId);
            if ($user) {
                return $user;
            }
        }

        // Fallback to primary administrator account if session is absent (e.g. CLI runner or testing)
        $admin = $this->userModel->where('username', 'admin')->where('account_status', 'active')->first();
        if ($admin) {
            return $admin;
        }

        return $this->userModel->where('account_status', 'active')->first();
    }

    /**
     * Helper to assemble user profile data, roles, and initials.
     */
    private function getCurrentUserData(): array
    {
        $user = $this->getCurrentUser();
        $db   = db_connect();

        if (! $user) {
            $user = [
                'id'             => 0,
                'name'           => 'Administrator',
                'username'       => 'administrator',
                'email'          => 'admin@begawipbj.go.id',
                'phone'          => '+62 851-1777-9783',
                'profile_type'   => 'asn',
                'account_status' => 'active',
                'last_login_at'  => date('Y-m-d H:i:s'),
                'created_at'     => '2026-01-01 00:00:00',
            ];
        }

        $userId = (int) $user['id'];

        // Get roles
        $roles = [];
        if ($userId > 0) {
            $roles = $this->userModel->getRoles($userId);
        }
        if (empty($roles)) {
            $roles = [['name' => 'Administrator Sistem', 'key' => 'administrator']];
        }

        // Generate initials
        $nameParts = preg_split('/\s+/', trim((string) $user['name']));
        $initials  = '';
        if (! empty($nameParts[0])) {
            $initials .= mb_strtoupper(mb_substr($nameParts[0], 0, 1));
        }
        if (! empty($nameParts[1])) {
            $initials .= mb_strtoupper(mb_substr($nameParts[1], 0, 1));
        }
        if ($initials === '') {
            $initials = 'AD';
        }

        // Get user profile
        $profile = [];
        if ($userId > 0) {
            $profile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray() ?: [];
        }

        // Get detail profile based on type
        $detail = [];
        if ($userId > 0) {
            $detailTables = [
                'asn'      => 'asn_profiles',
                'provider' => 'provider_profiles',
                'academic' => 'academic_profiles',
                'public'   => 'public_profiles',
            ];
            $profileType = $user['profile_type'] ?? 'asn';
            if (isset($detailTables[$profileType])) {
                $detail = $db->table($detailTables[$profileType])->where('user_id', $userId)->get()->getRowArray() ?: [];
            }
        }

        // Default unit and position if empty
        $unitName = $detail['unit_name'] ?? $detail['institution_name'] ?? $detail['business_name'] ?? 'Biro Pengadaan Barang dan Jasa';
        $positionName = $detail['position_name'] ?? $detail['role_name'] ?? $detail['registrant_position'] ?? 'Administrator Platform';

        return [
            'user'         => $user,
            'roles'        => $roles,
            'initials'     => $initials,
            'profile'      => $profile,
            'detail'       => $detail,
            'unitName'     => $unitName,
            'positionName' => $positionName,
        ];
    }
}
