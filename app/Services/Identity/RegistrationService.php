<?php

namespace App\Services\Identity;

use App\Models\UserModel;
use App\Services\AuditService;

class RegistrationService
{
    public function register(array $data): int
    {
        $db = db_connect();
        $db->transStart();

        $userId = (new UserModel())->insert([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'profile_type' => $data['profile_type'],
            'account_status' => 'pending_verification',
        ], true);

        if (!$userId) {
            throw new \RuntimeException('User gagal dibuat');
        }

        $db->table('user_profiles')->insert([
            'user_id' => $userId,
            'address_line' => $data['address_line'] ?? '',
            'province_code' => $data['province_code'],
            'regency_code' => $data['regency_code'],
            'district_code' => $data['district_code'],
            'village_code' => $data['village_code'] ?: null,
            'profile_completion_status' => 'draft',
        ]);

        $profileType = $data['profile_type'];
        $profile = $data['profile'] ?? [];
        $table = $this->profileTable($profileType);

        if ($table) {
            $row = $this->buildProfileRow($profileType, $userId, $profile);
            $db->table($table)->insert($row);
        }

        $db->table('profile_verifications')->insert([
            'user_id' => $userId,
            'verification_type' => 'profile_registration',
            'status' => 'pending',
            'requested_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new \RuntimeException('Registrasi gagal');
        }

        (new AuditService())->log($userId, 'register', 'auth', 'users', $userId);

        return (int) $userId;
    }

    private function profileTable(string $profileType): ?string
    {
        return [
            'asn' => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic' => 'academic_profiles',
            'public' => 'public_profiles',
        ][$profileType] ?? null;
    }

    private function buildProfileRow(string $profileType, int $userId, array $profile): array
    {
        switch ($profileType) {
            case 'asn':
                return [
                    'user_id' => $userId,
                    'employee_identifier' => $profile['employee_identifier'] ?? null,
                    'employment_status' => $profile['employment_status'] ?? null,
                    'origin_type' => $profile['origin_type'] ?? 'lainnya',
                    'institution_name' => $profile['institution_name'] ?? null,
                    'unit_name' => $profile['unit_name'] ?? null,
                    'position_name' => $profile['position_name'] ?? null,
                    'rank_grade' => $profile['rank_grade'] ?? null,
                    'official_email' => $profile['official_email'] ?? null,
                ];

            case 'provider':
                return [
                    'user_id' => $userId,
                    'business_name' => $profile['business_name'] ?? null,
                    'business_form' => $profile['business_form'] ?? null,
                    'business_field' => $profile['business_field'] ?? null,
                    'business_identifier' => $profile['business_identifier'] ?? null,
                    'registrant_position' => $profile['registrant_position'] ?? null,
                    'responsible_person' => $profile['responsible_person'] ?? null,
                    'business_email' => $profile['business_email'] ?? null,
                    'business_address' => $profile['business_address'] ?? null,
                ];

            case 'academic':
                return [
                    'user_id' => $userId,
                    'institution_name' => $profile['institution_name'] ?? null,
                    'institution_type' => $profile['institution_type'] ?? null,
                    'role_name' => $profile['role_name'] ?? null,
                    'expertise' => $this->encodeJsonArray($profile['expertise'] ?? null),
                    'institution_email' => $profile['institution_email'] ?? null,
                    'professional_identifier' => $profile['professional_identifier'] ?? null,
                    'portfolio_url' => $profile['portfolio_url'] ?? null,
                ];

            case 'public':
                return [
                    'user_id' => $userId,
                    'national_identifier' => $profile['national_identifier'] ?? null,
                    'occupation_status' => $profile['occupation_status'] ?? null,
                    'organization_name' => $profile['organization_name'] ?? null,
                    'interest_areas' => $this->encodeJsonArray($profile['interest_areas'] ?? null),
                    'usage_purpose' => $this->encodeJsonArray($profile['usage_purpose'] ?? null),
                ];

            default:
                return ['user_id' => $userId];
        }
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
}
