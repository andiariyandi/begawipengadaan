<?php

namespace App\Services\Identity;

use App\Models\UserModel;
use App\Services\AuditService;

/**
 * Identity registration service.
 *
 * Owns the atomic write sequence:
 *   users -> user_profiles -> profile-type table -> profile_verifications
 *
 * Fixes applied for QA Report 2026-10-04:
 *   - BUG-002: created_at / updated_at supplied on every raw insert.
 *   - BUG-003: explicit view->DB column mapping per profile type.
 *   - BUG-004: pre-insert validation; explicit error message on failure.
 */
class RegistrationService
{
    /**
     * View-form field name → DB column name per profile type.
     * Centralizes BUG-003 fix in one place so neither the view nor the
     * controller whitelist needs to be re-aligned.
     */
    private const FIELD_MAP = [
        'asn' => [
            'employee_identifier'   => 'nip_nippk',
            'employment_status'     => 'employment_status',
            'origin_type'           => 'asn_origin',
            'organization_unit_id'  => 'organization_unit_id',
            'institution_name'      => 'institution_name',
            'unit_name'             => 'external_unit_name',
            'position_name'         => 'position',
            'rank_grade'            => 'rank',
            'official_email'        => 'official_email',
        ],
        'provider' => [
            'business_name'         => 'company_name',
            'business_form'         => 'business_entity_type',
            'business_field'        => 'business_field',
            'business_identifier'   => 'nib',
            'registrant_position'   => 'registrant_position',
            'responsible_person'    => 'responsible_person',
            'business_email'        => 'company_email',
            'business_address'      => 'company_address',
        ],
        'academic' => [
            'institution_name'       => 'institution_name',
            'institution_type'       => 'institution_type',
            'role_name'              => 'academic_role',
            'expertise'              => 'expertise',
            'institution_email'      => 'institution_email',
            'professional_identifier' => 'professional_identifier',
            'portfolio_url'          => 'portfolio_url',
        ],
        'public' => [
            'national_identifier'    => 'nik',
            'occupation_status'      => 'occupation_status',
            'organization_name'      => 'organization_name',
            'interest_areas'         => 'procurement_interest',
            'usage_purpose'          => 'usage_purpose',
        ],
    ];

    private const REQUIRED_PROFILE_FIELDS = [
        'asn'      => ['origin_type', 'employment_status', 'position_name'],
        'provider' => ['business_name', 'business_form', 'registrant_position', 'responsible_person'],
        'academic' => ['institution_name', 'institution_type', 'role_name'],
        'public'   => ['occupation_status'],
    ];

    public function register(array $data): int
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $profileType = (string) ($data['profile_type'] ?? '');
        if (! isset(self::FIELD_MAP[$profileType])) {
            throw new \InvalidArgumentException("Tipe profil tidak dikenali: {$profileType}");
        }

        // Pre-flight: validate wilayah chain against master table.
        $this->assertWilayahChain(
            (string) ($data['province_code'] ?? ''),
            (string) ($data['regency_code']  ?? ''),
            (string) ($data['district_code'] ?? ''),
            $data['village_code'] ?? null,
        );

        $mappedProfile = $this->mapProfile($profileType, (array) ($data['profile'] ?? []));
        $this->assertRequiredFields($profileType, $mappedProfile);

        $db->transBegin();

        try {
            $userId = (new UserModel())->insert([
                'name'           => $data['name'],
                'username'       => $data['username'],
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'password_hash'  => password_hash((string) $data['password'], PASSWORD_DEFAULT),
                'profile_type'   => $profileType,
                'account_status' => 'pending_verification',
                'created_at'     => $now,
                'updated_at'     => $now,
            ], true);

            if (! $userId) {
                throw new \RuntimeException('User gagal dibuat (insert returned no id).');
            }

            $ok = $db->table('user_profiles')->insert([
                'user_id'                  => $userId,
                'address_line'             => $data['address_line'] ?? '',
                'province_code'            => $data['province_code'],
                'regency_code'             => $data['regency_code'],
                'district_code'            => $data['district_code'],
                'village_code'             => ($data['village_code'] ?? '') ?: null,
                'profile_completion_status' => 'draft',
                'created_at'               => $now,
                'updated_at'               => $now,
            ]);
            if (! $ok) {
                $err = $db->error();
                throw new \RuntimeException('Gagal menyimpan user_profiles: ' . ($err['message'] ?: 'Insert returned false'));
            }

            $profileTable = $this->profileTable($profileType);
            if ($profileTable) {
                $row = $this->buildProfileRow($profileType, $userId, $mappedProfile, $now);
                $ok = $db->table($profileTable)->insert($row);
                if (! $ok) {
                    $err = $db->error();
                    throw new \RuntimeException("Gagal menyimpan {$profileTable}: " . ($err['message'] ?: 'Insert returned false'));
                }
            }

            $ok = $db->table('profile_verifications')->insert([
                'user_id'          => $userId,
                'verification_type'=> 'profile_registration',
                'status'           => 'pending',
                'requested_by'     => $userId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            if (! $ok) {
                $err = $db->error();
                throw new \RuntimeException('Gagal menyimpan profile_verifications: ' . ($err['message'] ?: 'Insert returned false'));
            }

            $role = $db->table('roles')->where('key', 'peserta')->get()->getRowArray();
            if ($role) {
                $ok = $db->table('user_roles')->insert([
                    'user_id'    => $userId,
                    'role_id'    => (int) $role['id'],
                    'status'     => 'active',
                    'granted_by' => null,
                    'granted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                if (! $ok) {
                    $err = $db->error();
                    throw new \RuntimeException('Gagal menetapkan peran pengguna: ' . ($err['message'] ?: 'Insert returned false'));
                }
            }

            // Record requested procurement roles into user_procurement_roles with pending verification
            if ($profileType === 'asn' && ! empty($data['profile']['procurement_roles'])) {
                $rawRoles = $data['profile']['procurement_roles'];
                if (is_string($rawRoles)) {
                    $decoded = json_decode($rawRoles, true);
                    $roleList = is_array($decoded) ? $decoded : [$rawRoles];
                } elseif (is_array($rawRoles)) {
                    $roleList = $rawRoles;
                } else {
                    $roleList = [];
                }

                $roleIds = [];
                foreach ($roleList as $rItem) {
                    if (empty($rItem)) {
                        continue;
                    }
                    if (is_numeric($rItem)) {
                        $roleRow = $db->table('procurement_roles')->where('id', (int) $rItem)->get()->getRowArray();
                        if ($roleRow) {
                            $roleIds[] = (int) $roleRow['id'];
                        }
                    } else {
                        $roleRow = $db->table('procurement_roles')->where('key', (string) $rItem)->get()->getRowArray();
                        if ($roleRow) {
                            $roleIds[] = (int) $roleRow['id'];
                        }
                    }
                }

                $roleIds = array_unique($roleIds);
                foreach ($roleIds as $rId) {
                    $okRole = $db->table('user_procurement_roles')->insert([
                        'user_id'             => $userId,
                        'procurement_role_id' => $rId,
                        'verification_status' => 'pending',
                        'assignment_start_at' => $now,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);
                    if (! $okRole) {
                        $err = $db->error();
                        throw new \RuntimeException('Gagal mencatat penugasan peran pengadaan: ' . ($err['message'] ?: 'Insert returned false'));
                    }
                }
            }

            if ($db->transStatus() === false) {
                $err = $db->error();
                throw new \RuntimeException('Transaksi registrasi gagal: ' . ($err['message'] ?: 'transStatus=false'));
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        // Audit (post-commit; failures here must not roll back user creation).
        try {
            (new AuditService())->log($userId, 'register', 'auth', 'users', $userId, [
                'profile_type'   => $profileType,
                'account_status' => 'pending_verification',
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[Register] audit log failed: ' . $e->getMessage());
        }

        // Shared Notification ke Pool Verifikator (Super Admin, Admin, & Operator)
        try {
            $profileLabel = match ($profileType) {
                'asn'      => 'Aparatur Sipil Negara (ASN)',
                'provider' => 'Penyedia Barang/Jasa',
                'academic' => 'Akademisi / Lembaga Pendidikan',
                'public'   => 'Masyarakat Umum / Publik',
                default    => ucfirst($profileType),
            };

            (new \App\Services\NotificationService())->sendShared(
                'shared:admin_operator',
                'account_registered',
                'Pendaftaran Akun Baru Menunggu Verifikasi',
                "Pengguna baru {$data['name']} ({$profileLabel}) telah mendaftar dan berkasnya siap diverifikasi.",
                [
                    'user_id'      => (int) $userId,
                    'name'         => $data['name'],
                    'profile_type' => $profileType,
                    'url'          => site_url('workspace/operator/verifikasi-user'),
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', '[Register] shared notification failed: ' . $e->getMessage());
        }

        return (int) $userId;
    }

    private function profileTable(string $profileType): ?string
    {
        return [
            'asn'      => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public'   => 'public_profiles',
        ][$profileType] ?? null;
    }

    /**
     * Map view-form field names to DB column names (BUG-003 fix).
     */
    private function mapProfile(string $profileType, array $profile): array
    {
        $out = [];
        foreach (self::FIELD_MAP[$profileType] as $dbCol => $formKey) {
            $value = null;
            if (array_key_exists($formKey, $profile)) {
                $value = $profile[$formKey];
            } elseif (array_key_exists($dbCol, $profile)) {
                $value = $profile[$dbCol];
            } else {
                continue;
            }
            $out[$dbCol] = $this->normalizeValue($dbCol, $value);
        }
        return $out;
    }

    private function normalizeValue(string $dbCol, mixed $value): mixed
    {
        if (is_array($value)) {
            return json_encode(array_values($value), JSON_THROW_ON_ERROR);
        }
        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }
        return $value;
    }

    /**
     * Pre-insert schema constraint validation (BUG-004 fix).
     */
    private function assertRequiredFields(string $profileType, array $mapped): void
    {
        $missing = [];
        foreach (self::REQUIRED_PROFILE_FIELDS[$profileType] ?? [] as $field) {
            $value = $mapped[$field] ?? null;
            if ($value === null) {
                $missing[] = $field;
                continue;
            }
            if (is_string($value) && trim($value) === '') {
                $missing[] = $field;
            }
        }
        if ($missing !== []) {
            throw new \InvalidArgumentException(
                'Field wajib belum diisi: ' . implode(', ', $missing),
            );
        }
    }

    /**
     * Validate that province → regency → district → village chain is
     * hierarchical and every code exists in the wilayah master table.
     * Throws InvalidArgumentException if any code is missing or the
     * chain is broken.
     */
    private function assertWilayahChain(string $province, string $regency, string $district, ?string $village): void
    {
        if (strlen($province) !== 2 || strlen($regency) !== 5 || strlen($district) !== 8) {
            throw new \InvalidArgumentException('Format kode wilayah tidak valid.');
        }
        if (! str_starts_with($regency, $province . '.') || ! str_starts_with($district, $regency . '.')) {
            throw new \InvalidArgumentException('Kode wilayah tidak berjenjang (province/regency/district).');
        }
        if ($village !== null && $village !== '' && ! str_starts_with($village, $district . '.')) {
            throw new \InvalidArgumentException('Kode desa/kelurahan tidak berada di bawah kecamatan.');
        }

        $codes = array_filter([$province, $regency, $district, $village]);
        $found = (int) db_connect()->table('wilayah')->whereIn('kode', $codes)->countAllResults();
        if ($found !== count($codes)) {
            throw new \InvalidArgumentException('Salah satu kode wilayah tidak ditemukan di master.');
        }
    }

    private function buildProfileRow(string $profileType, int $userId, array $profile, string $now): array
    {
        $base = [
            'user_id'    => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        switch ($profileType) {
            case 'asn':
                $base['origin_type'] = $profile['origin_type'] ?? 'lainnya';
                $opdId = ! empty($profile['organization_unit_id']) ? (int) $profile['organization_unit_id'] : null;
                if ($opdId && $opdId > 0) {
                    $unitRow = db_connect()->table('organization_units')->where('id', $opdId)->where('is_active', 1)->get()->getRowArray();
                    if ($unitRow) {
                        $base['organization_unit_id'] = (int) $unitRow['id'];
                        if (empty($profile['unit_name'])) {
                            $base['unit_name'] = $unitRow['name'];
                        }
                        if (empty($profile['institution_name'])) {
                            $base['institution_name'] = 'Pemerintah Daerah Provinsi Lampung';
                        }
                    } else {
                        $base['organization_unit_id'] = null;
                    }
                } else {
                    $base['organization_unit_id'] = null;
                }
                foreach (['employee_identifier', 'employment_status',
                            'institution_name', 'unit_name', 'position_name',
                            'rank_grade', 'official_email'] as $col) {
                    if (array_key_exists($col, $profile) && ! isset($base[$col])) {
                        $val = $profile[$col];
                        $base[$col] = ($val === '' || $val === null) ? null : $val;
                    }
                }
                return $base;

            case 'provider':
                foreach (['business_name', 'business_form', 'business_field', 'business_identifier',
                            'registrant_position', 'responsible_person',
                            'business_email', 'business_address'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $val = $profile[$col];
                        $base[$col] = ($val === '' || $val === null) ? null : $val;
                    }
                }
                return $base;

            case 'academic':
                foreach (['institution_name', 'institution_type', 'role_name', 'expertise',
                            'institution_email', 'professional_identifier', 'portfolio_url'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $val = $profile[$col];
                        $base[$col] = ($val === '' || $val === null) ? null : $val;
                    }
                }
                return $base;

            case 'public':
                foreach (['national_identifier', 'occupation_status', 'organization_name',
                            'interest_areas', 'usage_purpose'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $val = $profile[$col];
                        $base[$col] = ($val === '' || $val === null) ? null : $val;
                    }
                }
                return $base;

            default:
                return $base;
        }
    }
}