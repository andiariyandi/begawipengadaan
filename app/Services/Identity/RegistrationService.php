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

        $db->transStart();

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
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal membuat akun pengguna: ' . $e->getMessage(), 0, $e);
        }

        if (! $userId) {
            $db->transRollback();
            throw new \RuntimeException('User gagal dibuat (insert returned no id).');
        }

        try {
            $db->table('user_profiles')->insert([
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
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal menyimpan user_profiles: ' . $e->getMessage(), 0, $e);
        }

        $profileTable = $this->profileTable($profileType);
        if ($profileTable) {
            try {
                $row = $this->buildProfileRow($profileType, $userId, $mappedProfile, $now);
                $db->table($profileTable)->insert($row);
            } catch (\Throwable $e) {
                $db->transRollback();
                throw new \RuntimeException("Gagal menyimpan {$profileTable}: " . $e->getMessage(), 0, $e);
            }
        }

        try {
            $db->table('profile_verifications')->insert([
                'user_id'          => $userId,
                'verification_type'=> 'profile_registration',
                'status'           => 'pending',
                'requested_by'     => $userId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal menyimpan profile_verifications: ' . $e->getMessage(), 0, $e);
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            throw new \RuntimeException('Transaksi registrasi gagal (transStatus=false).');
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
            if (! array_key_exists($formKey, $profile)) {
                continue;
            }
            $value = $profile[$formKey];
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
            return trim($value);
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
                foreach (['employee_identifier', 'employment_status', 'organization_unit_id',
                            'institution_name', 'unit_name', 'position_name',
                            'rank_grade', 'official_email'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $base[$col] = $profile[$col];
                    }
                }
                return $base;

            case 'provider':
                foreach (['business_name', 'business_form', 'business_field', 'business_identifier',
                            'registrant_position', 'responsible_person',
                            'business_email', 'business_address'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $base[$col] = $profile[$col];
                    }
                }
                return $base;

            case 'academic':
                foreach (['institution_name', 'institution_type', 'role_name', 'expertise',
                            'institution_email', 'professional_identifier', 'portfolio_url'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $base[$col] = $profile[$col];
                    }
                }
                return $base;

            case 'public':
                foreach (['national_identifier', 'occupation_status', 'organization_name',
                            'interest_areas', 'usage_purpose'] as $col) {
                    if (array_key_exists($col, $profile)) {
                        $base[$col] = $profile[$col];
                    }
                }
                return $base;

            default:
                return $base;
        }
    }
}