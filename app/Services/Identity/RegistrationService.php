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

        $table = [
            'asn' => 'asn_profiles',
            'provider' => 'provider_profiles',
            'academic_practitioner' => 'academic_profiles',
            'public' => 'public_profiles',
        ][$data['profile_type']] ?? null;

        if ($table) {
            $profile = ['user_id' => $userId];

            if ($data['profile_type'] === 'asn') {
                $profile = array_merge($profile, [
                    'employee_identifier' => $data['profile']['identity_number'] ?? null,
                    'employment_status'  => $data['profile']['asn_status'] ?? null,
                    'institution_name'   => $data['profile']['institution'] ?? null,
                    'unit_name'          => $data['profile']['work_unit'] ?? null,
                    'position_name'     => $data['profile']['position'] ?? null,
                    'rank_grade'         => $data['profile']['grade'] ?? null,
                    'origin_type'        => 'registration',
                ]);
            } else {
                foreach ($data['profile'] ?? [] as $key => $value) {
                    $profile[$key] = $value;
                }
            }

            $db->table($table)->insert($profile);
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
}
