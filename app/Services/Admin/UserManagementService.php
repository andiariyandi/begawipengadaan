<?php

namespace App\Services\Admin;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\UserRoleModel;
use App\Services\AuditService;

/**
 * User management service for the Admin Dashboard.
 *
 * Encapsulates user + profile + role persistence for the Manajemen Akses
 * menu. Every mutating call writes audit_logs and workflow_transitions.
 *
 * Status transitions are validated server-side per CLAUDE.md §Account Status
 * Lifecycle. Roles are managed exclusively through UserRoleModel; this
 * service does not bypass it.
 */
class UserManagementService
{
    public const ALLOWED_PROFILE_TYPES = ['asn', 'provider', 'academic', 'public'];
    public const ALLOWED_STATUSES = [
        'draft',
        'submitted',
        'pending_verification',
        'revision_required',
        'active',
        'rejected',
        'inactive',
        'suspended',
        'archived',
    ];

    public function __construct(
        private readonly UserModel $users = new UserModel(),
        private readonly UserProfileModel $profiles = new UserProfileModel(),
        private readonly UserRoleModel $userRoles = new UserRoleModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    /**
     * Create or update a user record (administrator-side, not self-registration).
     *
     * @param array $data validated input (username, email, name, profile_type,
     *                    phone, password_hash, account_status, optional role_id)
     * @param int|null $userId null = create, int = update
     * @return int user id
     */
    public function saveUser(array $data, ?int $userId = null, ?int $actorId = null): int
    {
        $account = [
            'username'       => trim((string) ($data['username'] ?? '')),
            'email'          => strtolower(trim((string) ($data['email'] ?? ''))),
            'name'           => trim((string) ($data['name'] ?? $data['full_name'] ?? '')),
            'profile_type'   => (string) ($data['profile_type'] ?? 'public'),
            'phone'          => trim((string) ($data['phone'] ?? '')),
            'account_status' => (string) ($data['account_status'] ?? 'draft'),
        ];

        if (! empty($data['password_hash'])) {
            $account['password_hash'] = (string) $data['password_hash'];
        } elseif (! empty($data['password'])) {
            $account['password_hash'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        $fromStatus = null;
        if ($userId !== null) {
            $current = $this->users->find($userId);
            if ($current) {
                $fromStatus = (string) ($current['account_status'] ?? 'draft');
            }
        }

        if ($userId === null) {
            $userId = (int) $this->users->insert($account, true);
            if ($userId <= 0) {
                $errs = $this->users->errors();
                throw new \RuntimeException($errs ? implode(', ', $errs) : 'Gagal membuat pengguna baru.');
            }
        } else {
            $account['id'] = $userId;
            $ok = $this->users->update($userId, $account);
            if ($ok === false) {
                $errs = $this->users->errors();
                throw new \RuntimeException($errs ? implode(', ', $errs) : 'Gagal memperbarui data pengguna.');
            }
            if ($fromStatus !== null && $fromStatus !== $account['account_status']) {
                $this->recordTransition('user', $userId, $fromStatus, $account['account_status'], 'administrator.user_update', $actorId);
            }
        }

        // Role assignment (single-role for administrator-side form; multi-role supported via direct DB).
        if (isset($data['role_id'])) {
            $roleId = (int) $data['role_id'];
            if ($roleId > 0) {
                $this->assignRole((int) $userId, $roleId, $actorId, true);
            } elseif ($userId !== null && $data['role_id'] === '') {
                // Admin memilih tanpa role spesifik -> nonaktifkan role lama
                $this->userRoles
                    ->where('user_id', $userId)
                    ->set([
                        'status'     => 'revoked',
                        'revoked_by' => $actorId,
                        'revoked_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ])
                    ->update();
            }
        }

        $this->audit->log(
            $actorId,
            $userId ? 'user.update' : 'user.create',
            'users',
            'user',
            $userId,
        );

        // Sinkronisasi Data Profil Khusus & Pembersihan Profil Lama (Zero Residual Cross-Profile Data)
        $this->syncUserProfileData((int) $userId, $account['profile_type'], $data);

        return (int) $userId;
    }

    /**
     * Menyimpan data profil spesifik (ASN, Provider, Academic, Public) dan
     * menghapus sisa data profil lama saat tipe profil berubah.
     */
    public function syncUserProfileData(int $userId, string $targetProfileType, array $data): void
    {
        $db = db_connect();
        $targetProfileType = strtolower(trim($targetProfileType));
        $profileTables = [
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
        ];

        if (! isset($profileTables[$targetProfileType])) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        // 1. Hapus data profil lama dari tabel tipe profil lainnya
        foreach ($profileTables as $type => $tableName) {
            if ($type !== $targetProfileType) {
                $db->table($tableName)->where('user_id', $userId)->delete();
            }
        }

        // 2. Simpan atau perbarui data profil pada tabel target
        $targetTable = $profileTables[$targetProfileType];
        $existingSpecific = $db->table($targetTable)->where('user_id', $userId)->get()->getRowArray();
        $specificData = [];

        if ($targetProfileType === 'asn') {
            $orgUnitId = ! empty($data['organization_unit_id']) ? (int) $data['organization_unit_id'] : null;
            $unitName  = trim((string) ($data['unit_name'] ?? ''));
            if ($orgUnitId && empty($unitName)) {
                $ou = $db->table('organization_units')->select('name')->where('id', $orgUnitId)->get()->getRowArray();
                if ($ou) {
                    $unitName = $ou['name'];
                }
            }

            $originType = (string) ($data['origin_type'] ?? 'pemprov_lampung');
            $allowedOrigins = ['pemprov_lampung', 'kementerian_lembaga', 'pemerintah_daerah_lain', 'pemerintah_desa', 'bumn_bumd', 'lainnya'];
            if (! in_array($originType, $allowedOrigins, true)) {
                $originType = 'pemprov_lampung';
            }

            $specificData = [
                'employee_identifier'  => trim((string) ($data['employee_identifier'] ?? $data['nip'] ?? '')),
                'organization_unit_id' => $orgUnitId,
                'unit_name'            => $unitName ?: null,
                'position_name'        => trim((string) ($data['position_name'] ?? '')) ?: null,
                'rank_grade'           => trim((string) ($data['rank_grade'] ?? '')) ?: null,
                'employment_status'    => trim((string) ($data['employment_status'] ?? 'pns')) ?: 'pns',
                'origin_type'          => $originType,
                'institution_name'     => trim((string) ($data['institution_name'] ?? 'Pemerintah Provinsi Lampung')) ?: 'Pemerintah Provinsi Lampung',
                'official_email'       => trim((string) ($data['official_email'] ?? '')) ?: null,
            ];
        } elseif ($targetProfileType === 'provider') {
            $defaultCompanyName = trim((string) ($data['name'] ?? 'Perusahaan Penyedia'));
            $specificData = [
                'business_name'        => trim((string) ($data['business_name'] ?? $data['company_name'] ?? $defaultCompanyName)) ?: $defaultCompanyName,
                'business_form'        => trim((string) ($data['business_form'] ?? 'PT')) ?: 'PT',
                'business_field'       => trim((string) ($data['business_field'] ?? '')) ?: null,
                'business_identifier'  => trim((string) ($data['business_identifier'] ?? $data['nib'] ?? '')) ?: null,
                'registrant_position'  => trim((string) ($data['registrant_position'] ?? 'Direktur')) ?: 'Direktur',
                'responsible_person'   => trim((string) ($data['responsible_person'] ?? $data['name'] ?? 'Penanggung Jawab')) ?: 'Penanggung Jawab',
                'business_email'       => trim((string) ($data['business_email'] ?? $data['email'] ?? '')) ?: null,
                'business_address'     => trim((string) ($data['business_address'] ?? '')) ?: null,
            ];
        } elseif ($targetProfileType === 'academic') {
            $expertise = $data['expertise'] ?? null;
            if (is_array($expertise)) {
                $expertise = json_encode(array_values($expertise), JSON_UNESCAPED_UNICODE);
            } elseif (is_string($expertise) && trim($expertise) !== '' && ! str_starts_with(trim($expertise), '[')) {
                $expertise = json_encode([trim($expertise)], JSON_UNESCAPED_UNICODE);
            }

            $specificData = [
                'institution_name'        => trim((string) ($data['institution_name'] ?? 'Perguruan Tinggi')) ?: 'Perguruan Tinggi',
                'institution_type'        => trim((string) ($data['institution_type'] ?? 'Perguruan Tinggi Negeri')) ?: 'Perguruan Tinggi Negeri',
                'role_name'               => trim((string) ($data['role_name'] ?? 'Dosen')) ?: 'Dosen',
                'expertise'               => $expertise ?: null,
                'institution_email'       => trim((string) ($data['institution_email'] ?? '')) ?: null,
                'professional_identifier' => trim((string) ($data['professional_identifier'] ?? '')) ?: null,
                'portfolio_url'           => trim((string) ($data['portfolio_url'] ?? '')) ?: null,
            ];
        } elseif ($targetProfileType === 'public') {
            $interestAreas = $data['interest_areas'] ?? null;
            if (is_array($interestAreas)) {
                $interestAreas = json_encode(array_values($interestAreas), JSON_UNESCAPED_UNICODE);
            } elseif (is_string($interestAreas) && trim($interestAreas) !== '' && ! str_starts_with(trim($interestAreas), '[')) {
                $interestAreas = json_encode([trim($interestAreas)], JSON_UNESCAPED_UNICODE);
            }

            $usagePurpose = $data['usage_purpose'] ?? null;
            if (is_array($usagePurpose)) {
                $usagePurpose = json_encode(array_values($usagePurpose), JSON_UNESCAPED_UNICODE);
            } elseif (is_string($usagePurpose) && trim($usagePurpose) !== '' && ! str_starts_with(trim($usagePurpose), '[')) {
                $usagePurpose = json_encode([trim($usagePurpose)], JSON_UNESCAPED_UNICODE);
            }

            $specificData = [
                'national_identifier' => trim((string) ($data['national_identifier'] ?? $data['nik'] ?? '')) ?: null,
                'occupation_status'   => trim((string) ($data['occupation_status'] ?? 'Masyarakat Umum')) ?: 'Masyarakat Umum',
                'organization_name'   => trim((string) ($data['organization_name'] ?? '')) ?: null,
                'interest_areas'      => $interestAreas ?: null,
                'usage_purpose'       => $usagePurpose ?: null,
            ];
        }

        if (! empty($specificData)) {
            $specificData['updated_at'] = $now;
            if ($existingSpecific) {
                $db->table($targetTable)->where('user_id', $userId)->update($specificData);
            } else {
                $specificData['user_id']    = $userId;
                $specificData['created_at'] = $now;
                $db->table($targetTable)->insert($specificData);
            }
        }

        // 3. Sinkronisasi data dasar user_profiles (Alamat & Wilayah)
        $existingUp = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();

        $hasProv = array_key_exists('province_code', $data) || array_key_exists('province', $data);
        $hasReg  = array_key_exists('regency_code', $data) || array_key_exists('regency', $data);
        $hasDist = array_key_exists('district_code', $data) || array_key_exists('district', $data);
        $hasVill = array_key_exists('village_code', $data) || array_key_exists('village', $data);
        $hasAddr = array_key_exists('address_line', $data) || array_key_exists('address', $data);

        $provInput = trim((string) ($data['province_code'] ?? $data['province'] ?? ''));
        $regInput  = trim((string) ($data['regency_code'] ?? $data['regency'] ?? ''));
        $distInput = trim((string) ($data['district_code'] ?? $data['district'] ?? ''));
        $villInput = trim((string) ($data['village_code'] ?? $data['village'] ?? ''));
        $addrInput = trim((string) ($data['address_line'] ?? $data['address'] ?? ''));

        $provinceCode = $hasProv ? ($provInput !== '' ? $provInput : '18') : ($existingUp['province_code'] ?? '18');

        // Regency
        if ($hasReg && $regInput !== '') {
            $regencyCode = $regInput;
        } elseif ($existingUp && ! empty($existingUp['regency_code']) && str_starts_with((string) $existingUp['regency_code'], $provinceCode . '.')) {
            $regencyCode = (string) $existingUp['regency_code'];
        } else {
            $regencyCode = $provinceCode === '18' ? '18.71' : null;
        }

        // District
        if ($hasDist && $distInput !== '') {
            $districtCode = $distInput;
        } elseif ($existingUp && ! empty($existingUp['district_code']) && $regencyCode && str_starts_with((string) $existingUp['district_code'], $regencyCode . '.')) {
            $districtCode = (string) $existingUp['district_code'];
        } else {
            $districtCode = ($regencyCode === '18.71') ? '18.71.01' : null;
        }

        // Village
        if ($hasVill) {
            $villageCode = ($villInput !== '' && $districtCode && str_starts_with($villInput, $districtCode . '.')) ? $villInput : null;
        } elseif ($existingUp && ! empty($existingUp['village_code']) && $districtCode && str_starts_with((string) $existingUp['village_code'], $districtCode . '.')) {
            $villageCode = (string) $existingUp['village_code'];
        } else {
            $villageCode = null;
        }

        $addressLine = $hasAddr ? ($addrInput !== '' ? $addrInput : 'Alamat belum diatur') : ($existingUp['address_line'] ?? 'Alamat belum diatur');

        $upData = [
            'address_line'              => $addressLine ?: 'Alamat belum diatur',
            'province_code'             => $provinceCode ?: '18',
            'regency_code'              => $regencyCode ?: '18.71',
            'district_code'             => $districtCode ?: '18.71.01',
            'village_code'              => $villageCode ?: null,
            'profile_completion_status' => 'complete',
            'updated_at'                => $now,
        ];

        if ($existingUp) {
            $db->table('user_profiles')->where('user_id', $userId)->update($upData);
        } else {
            $upData['user_id']    = $userId;
            $upData['created_at'] = $now;
            $db->table('user_profiles')->insert($upData);
        }
    }

    public function assignRole(int $userId, int $roleId, ?int $actorId = null, bool $replaceExisting = false): void
    {
        if ($replaceExisting) {
            $this->userRoles
                ->where('user_id', $userId)
                ->where('role_id !=', $roleId)
                ->set([
                    'status'     => 'revoked',
                    'revoked_by' => $actorId,
                    'revoked_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ])
                ->update();
        }

        $existing = $this->userRoles
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->first();

        if ($existing) {
            $this->userRoles->update($existing['id'], [
                'status'     => 'active',
                'revoked_by' => null,
                'revoked_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return;
        }

        $this->userRoles->insert([
            'user_id'    => $userId,
            'role_id'    => $roleId,
            'status'     => 'active',
            'granted_by' => $actorId,
            'granted_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log($actorId, 'user.role.grant', 'user_roles', 'user_role', $userId);
    }

    /**
     * Update account_status — every transition is audited and recorded as
     * a workflow_transition for the user resource.
     */
    public function updateStatus(int $userId, string $newStatus, ?int $actorId = null, ?string $reason = null): void
    {
        if (! in_array($newStatus, self::ALLOWED_STATUSES, true)) {
            throw new \InvalidArgumentException("Status akun tidak valid: {$newStatus}");
        }

        $current = $this->users->find($userId);
        if (! $current) {
            throw new \RuntimeException('Pengguna tidak ditemukan.');
        }

        $oldStatus = (string) ($current['account_status'] ?? 'draft');

        $this->users->update($userId, [
            'account_status' => $newStatus,
            'status_reason'  => $reason,
        ]);

        $this->audit->log(
            $actorId,
            'user.status.update',
            'users',
            'user',
            $userId,
            ['from' => $oldStatus, 'to' => $newStatus, 'reason' => $reason],
        );

        $this->recordTransition('user', $userId, $oldStatus, $newStatus, 'status.update', $actorId, $reason);
    }

    /**
     * Suspend helper — sets account_status to suspended with reason.
     */
    public function suspend(int $userId, string $reason, ?int $actorId = null): void
    {
        $this->updateStatus($userId, 'suspended', $actorId, $reason);
    }

    /**
     * Activate / Approve helper.
     * Mengaktifkan akun pengguna sekaligus menyetujui (approve) seluruh berkas verifikasi profil yang pending.
     */
    public function activate(int $userId, ?int $actorId = null, ?string $reason = null): void
    {
        $this->updateStatus($userId, 'active', $actorId, $reason);

        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        // Jika email_verified_at masih kosong, tandai terverifikasi agar akun bisa langsung aktif lancar
        $user = $this->users->find($userId);
        if ($user && empty($user['email_verified_at'])) {
            $this->users->update($userId, [
                'email_verified_at' => $now,
            ]);
        }

        // APPROVE AKUN ARTINYA APPROVE PROFILE JUGA:
        // Setujui seluruh antrean verifikasi profil pengguna ini
        $pendingVerifications = $db->table('profile_verifications')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_review', 'revision_required', 'submitted'])
            ->get()->getResultArray();

        foreach ($pendingVerifications as $pv) {
            $db->table('profile_verifications')->where('id', $pv['id'])->update([
                'status'      => 'approved',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'notes'       => $reason ?: 'Disetujui bersamaan dengan persetujuan akun pengguna.',
                'updated_at'  => $now,
            ]);

            $this->recordTransition(
                'profile_verification',
                (int) $pv['id'],
                (string) ($pv['status'] ?? 'pending'),
                'approved',
                'profile_verification.approve',
                $actorId,
                $reason ?: 'Persetujuan akun & profil terpadu',
                $reason ?: 'Disetujui bersamaan dengan aktivasi akun.',
            );

            $this->audit->log($actorId, 'profile_verification.approve', 'profile_verifications', 'profile_verification', (int) $pv['id']);
        }

        // Tandai kelengkapan profil pada tabel user_profiles jika belum lengkap
        $userProfile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
        if ($userProfile && ($userProfile['profile_completion_status'] ?? '') !== 'complete') {
            $db->table('user_profiles')->where('user_id', $userId)->update([
                'profile_completion_status' => 'complete',
                'updated_at'                => $now,
            ]);
        }
    }

    /**
     * Reject helper — menolak akun pengguna dan seluruh berkas verifikasi profil yang pending.
     */
    public function reject(int $userId, string $reason, ?int $actorId = null): void
    {
        $this->updateStatus($userId, 'rejected', $actorId, $reason);

        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $pendingVerifications = $db->table('profile_verifications')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_review', 'revision_required', 'submitted'])
            ->get()->getResultArray();

        foreach ($pendingVerifications as $pv) {
            $db->table('profile_verifications')->where('id', $pv['id'])->update([
                'status'      => 'rejected',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'notes'       => $reason,
                'updated_at'  => $now,
            ]);

            $this->recordTransition(
                'profile_verification',
                (int) $pv['id'],
                (string) ($pv['status'] ?? 'pending'),
                'rejected',
                'profile_verification.reject',
                $actorId,
                $reason,
                $reason,
            );

            $this->audit->log($actorId, 'profile_verification.reject', 'profile_verifications', 'profile_verification', (int) $pv['id']);
        }
    }

    public function recordTransition(
        string $resourceType,
        int $resourceId,
        string $fromState,
        string $toState,
        string $action,
        ?int $actorId = null,
        ?string $reason = null,
        ?string $notes = null,
        ?array $metadata = null,
    ): int {
        $db = db_connect();
        return (int) $db->table('workflow_transitions')->insert([
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'from_state'    => $fromState,
            'to_state'      => $toState,
            'action'        => $action,
            'actor_id'      => $actorId,
            'actor_type'    => 'user',
            'reason'        => $reason,
            'notes'         => $notes,
            'metadata'      => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
            'created_at'    => date('Y-m-d H:i:s'),
        ], true);
    }
}
