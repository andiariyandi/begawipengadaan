<?php

namespace App\Services;

class SystemSettingService
{
    private string $storagePath;
    private array $defaults = [
        'general' => [
            'app_name'        => 'BegawiPengadaan',
            'app_short_name'  => 'BegawiPengadaan',
            'default_locale'  => 'id',
            'timezone'        => 'Asia/Jakarta',
            'date_format'     => 'd F Y',
            'sender_email'    => 'noreply@begawipbj.lampungprov.go.id',
            'app_description' => 'Platform pembelajaran dan pengelolaan pengadaan barang/jasa Pemerintah Provinsi Lampung.',
        ],
        'organization' => [
            'agency_name'     => 'Pemerintah Provinsi Lampung',
            'agency_code'     => 'PEMPROV-LPG',
            'address'         => 'Jl. Wolter Monginsidi No. 69, Bandar Lampung',
            'phone'           => '(0721) 481103',
            'email'           => 'pbj@lampungprov.go.id',
            'provincial_unit' => 'Biro Pengadaan Barang dan Jasa',
        ],
        'learning' => [
            'enrollment_mode'       => 'open',
            'auto_approval'         => '1',
            'passing_grade_default' => '70.00',
            'quiz_weight_default'   => '30.00',
            'post_test_weight_default' => '70.00',
            'max_quiz_attempts'     => '3',
        ],
        'event' => [
            'default_capacity'      => '100',
            'waitlist_enabled'      => '1',
            'auto_attendance'       => '0',
            'reminder_hours_before' => '24',
        ],
        'certificate' => [
            'sequence_prefix'      => 'SERT',
            'valid_period_years'   => '3',
            'auto_numbering'       => '1',
            'qr_verification'      => '1',
            'signature_officer'    => 'Kepala Biro PBJ Provinsi Lampung',
            'signature_role'       => 'Pengguna Anggaran / Penanggung Jawab',
        ],
        'notification' => [
            'email_enabled'    => '1',
            'in_app_enabled'   => '1',
            'sender_email'     => 'noreply@begawipbj.lampungprov.go.id',
            'sender_name'      => 'BegawiPengadaan',
            'daily_digest'     => '0',
            'digest_frequency' => 'instant',
        ],
        'integration' => [
            'lkpp_api_endpoint' => 'https://api.lkpp.go.id/v1',
            'lkpp_api_key'      => '',
            'webhook_url'       => '',
            'sso_enabled'       => '0',
        ],
        'security' => [
            'min_password_length'      => '8',
            'require_numbers'          => '1',
            'require_special_chars'    => '0',
            'session_lifetime_minutes' => '120',
            'max_login_attempts'       => '5',
        ],
        'maintenance' => [
            'maintenance_mode'    => '0',
            'allowed_ips'         => '127.0.0.1, ::1',
            'maintenance_message' => 'Sistem sedang dalam pemeliharaan berkala. Silakan coba beberapa saat lagi.',
        ],
    ];

    public function __construct()
    {
        $this->storagePath = WRITEPATH . 'settings/system_settings.json';
        $dir = dirname($this->storagePath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    public function getAll(): array
    {
        $stored = [];
        if (file_exists($this->storagePath)) {
            $raw = @file_get_contents($this->storagePath);
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $stored = $decoded;
                }
            }
        }
        return array_replace_recursive($this->defaults, $stored);
    }

    public function getGroup(string $group): array
    {
        $all = $this->getAll();
        return $all[$group] ?? ($this->defaults[$group] ?? []);
    }

    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $grp = $this->getGroup($group);
        return $grp[$key] ?? $default;
    }

    public function saveGroup(string $group, array $values): bool
    {
        $all = $this->getAll();
        $currentGroup = $all[$group] ?? ($this->defaults[$group] ?? []);

        // Filter and update known keys
        foreach ($values as $k => $v) {
            if ($k !== 'csrf_test_name' && ! str_starts_with($k, '_')) {
                $currentGroup[$k] = is_string($v) ? trim($v) : $v;
            }
        }

        $all[$group] = $currentGroup;

        $json = json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return (bool) @file_put_contents($this->storagePath, $json);
    }
}
