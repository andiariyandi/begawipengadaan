<?php

namespace App\Services;

class SystemSettingService
{
    private string $storagePath;
    private static ?array $runtimeCache = null;

    private array $defaults = [
        'general' => [
            'app_name'        => 'BegawiPengadaan',
            'app_short_name'  => 'BegawiPengadaan',
            'default_locale'  => 'id',
            'timezone'        => 'Asia/Jakarta',
            'date_format'     => 'd F Y',
            'sender_email'    => 'noreply@begawipbj.lampungprov.go.id',
            'app_description' => 'Platform pembelajaran dan pengelolaan pengadaan barang/jasa Pemerintah Provinsi Lampung.',
            'tagline'         => 'Transformasi Digital Terpadu Pengadaan Barang & Jasa',
            'hero_description'=> 'Integrasi Arena Belajar aparatur, repositori dokumen Bunga Rampai, dan pusat regulasi PBJ terpadu Pemerintah Provinsi Lampung.',
        ],
        'organization' => [
            'agency_name'      => 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung',
            'agency_code'      => 'PEMPROV-LPG',
            'address'          => 'Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35211',
            'phone'            => '+62 851-1777-9783',
            'email'            => 'biropbj@lampungprov.go.id',
            'provincial_unit'  => 'Biro Pengadaan Barang dan Jasa',
            'operational_hours'=> 'Senin - Jumat, 08.00 - 16.00 WIB',
            'institution_level'=> 'UKPBJ Level Proaktif (Tingkat Kematangan Proaktif)',
            'institution_zone' => 'Zona Integritas Wilayah Bebas Korupsi & Gratifikasi',
            'google_maps_url'  => 'https://maps.google.com/?q=Biro+Pengadaan+Barang+dan+Jasa+Provinsi+Lampung',
        ],
        'social' => [
            'facebook_url'  => 'https://facebook.com/biropbjlampung',
            'instagram_url' => 'https://instagram.com/biropbj_lampung',
            'youtube_url'   => 'https://youtube.com/@biropbjsetdaprovinsilampung',
            'linkedin_url'  => 'https://linkedin.com/company/biropbj-lampung',
        ],
        'footer' => [
            'copyright_text' => 'Copyright © {year} BegawiPengadaan. Pemerintah Provinsi Lampung.',
            'subtext'        => 'Pengadaan kompeten, transparan, dan berdampak.',
        ],
        'vision_mission' => [
            'vision_title'     => 'Visi Utama 2024–2029',
            'vision_statement' => 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.',
            'vision_focus'     => 'Fokus: Kematangan Proaktif & E-Katalog Lokal',
            'mission_items'    => "[\"Peningkatan Kompetensi Berkelanjutan: Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.\",\"Penguatan Tata Kelola & Advokasi: Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.\",\"Akselerasi E-Purchasing & UMKM: Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).\",\"Pusat Literasi & Regulasi Terbuka: Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.\",\"Government Intelligence PBJ: Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.\"]",
        ],
        'philosophy' => [
            'philosophy_title' => 'Makna "Begawi" dalam Pengadaan Modern',
            'philosophy_p1'    => 'Kata "Begawi" berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.',
            'philosophy_p2'    => 'Dalam konteks pengadaan barang dan jasa modern, BegawiPengadaan hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai.',
            'philosophy_quote' => '"Pengadaan Barang dan Jasa bukan sekadar membelanjakan anggaran negara, melainkan instrumen strategis pembangunan yang menggerakkan ekonomi lokal dan mensejahterakan rakyat."',
        ],
        'pledge' => [
            'maklumat_pelayanan' => '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."',
        ],
        'announcement' => [
            'popup_enabled' => '1',
            'popup_badge'   => 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG',
        ],
        'helpdesk' => [
            'wa_enabled'      => '1',
            'wa_phone'        => '6285117779783',
            'wa_title'        => 'Helpdesk BegawiPengadaan',
            'wa_subtitle'     => 'Biro PBJ Setda Prov. Lampung',
            'wa_welcome_text' => "Halo! Selamat datang di BegawiPengadaan 👋\n\nAda yang bisa kami bantu terkait regulasi PBJ, pelatihan di Arena Belajar, atau template dokumen kerja?",
            'wa_teaser_text'  => 'Butuh bantuan seputar PBJ? Chat kami! 👋',
        ],
        'learning' => [
            'enrollment_mode'          => 'open',
            'auto_approval'            => '1',
            'passing_grade_default'    => '70.00',
            'quiz_weight_default'      => '30.00',
            'post_test_weight_default' => '70.00',
            'max_quiz_attempts'        => '3',
            'spotlight_mode'           => 'all',
            'spotlight_limit'          => '6',
        ],
        'knowledge' => [
            'spotlight_mode'  => 'all',
            'spotlight_limit' => '6',
        ],
        'regulation' => [
            'spotlight_mode'  => 'all',
            'spotlight_limit' => '6',
        ],
        'news' => [
            'spotlight_mode'  => 'all',
            'spotlight_limit' => '5',
        ],
        'event' => [
            'default_capacity'      => '100',
            'waitlist_enabled'      => '1',
            'auto_attendance'       => '0',
            'reminder_hours_before' => '24',
            'spotlight_mode'        => 'all',
            'spotlight_limit'       => '5',
        ],
        'certificate' => [
            'sequence_prefix'    => 'SERT',
            'valid_period_years' => '3',
            'auto_numbering'     => '1',
            'qr_verification'    => '1',
            'signature_officer'  => 'Kepala Biro PBJ Provinsi Lampung',
            'signature_role'     => 'Pengguna Anggaran / Penanggung Jawab',
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

    public static function flushCache(): void
    {
        self::$runtimeCache = null;
    }

    public function getAll(): array
    {
        if (self::$runtimeCache !== null) {
            return self::$runtimeCache;
        }

        $all = $this->defaults;

        // 1. Try reading from database table site_settings
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('site_settings')) {
                $rows = $db->table('site_settings')->get()->getResultArray();
                foreach ($rows as $row) {
                    $grp = $row['setting_group'];
                    $k   = $row['setting_key'];
                    $v   = $row['setting_value'];

                    if (! isset($all[$grp])) {
                        $all[$grp] = [];
                    }
                    $all[$grp][$k] = $v;
                }
            }
        } catch (\Throwable $e) {
            // Log or ignore if db unavailable, fall back to JSON
            log_message('warning', 'SystemSettingService db read error: ' . $e->getMessage());
        }

        // 2. Read from legacy JSON file if exists for any overrides
        if (file_exists($this->storagePath)) {
            $raw = @file_get_contents($this->storagePath);
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $all = array_replace_recursive($all, $decoded);
                }
            }
        }

        self::$runtimeCache = $all;
        return $all;
    }

    public function getGroup(string $group): array
    {
        $all = $this->getAll();
        return $all[$group] ?? ($this->defaults[$group] ?? []);
    }

    public function get(string $group, mixed $key = null, mixed $default = null): mixed
    {
        if (str_contains($group, '.') && func_num_args() <= 2) {
            return $this->getSetting($group, $key);
        }

        $grp = $this->getGroup($group);
        return $grp[$key] ?? $default;
    }

    /**
     * Universal getter supporting dotted keys like 'organization.agency_name' or direct key 'agency_name'
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $all = $this->getAll();

        if (str_contains($key, '.')) {
            [$group, $subKey] = explode('.', $key, 2);
            if (isset($all[$group][$subKey])) {
                return $all[$group][$subKey];
            }
        }

        // Search through all groups if not found or direct key
        foreach ($all as $groupData) {
            if (is_array($groupData) && array_key_exists($key, $groupData)) {
                return $groupData[$key];
            }
        }

        return $default;
    }

    public function saveGroup(string $group, array $values): bool
    {
        $all = $this->getAll();
        $currentGroup = $all[$group] ?? ($this->defaults[$group] ?? []);

        // Filter and update known keys
        $cleanValues = [];
        foreach ($values as $k => $v) {
            if ($k !== 'csrf_test_name' && ! str_starts_with($k, '_')) {
                $cleanVal = is_string($v) ? trim($v) : $v;
                $currentGroup[$k] = $cleanVal;
                $cleanValues[$k] = $cleanVal;
            }
        }

        $all[$group] = $currentGroup;

        // 1. Persist to database if available
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('site_settings')) {
                foreach ($cleanValues as $k => $val) {
                    $valString = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) $val;

                    $exists = $db->table('site_settings')
                        ->where('setting_group', $group)
                        ->where('setting_key', $k)
                        ->get()->getRow();

                    if ($exists) {
                        $db->table('site_settings')
                            ->where('setting_group', $group)
                            ->where('setting_key', $k)
                            ->update([
                                'setting_value' => $valString,
                                'updated_at'    => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        $db->table('site_settings')->insert([
                            'setting_group' => $group,
                            'setting_key'   => $k,
                            'setting_value' => $valString,
                            'field_type'    => 'text',
                            'description'   => ucfirst(str_replace('_', ' ', $k)),
                            'created_at'    => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'SystemSettingService db save error: ' . $e->getMessage());
        }

        // 2. Persist to JSON storage
        $json = json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $saved = (bool) @file_put_contents($this->storagePath, $json);

        self::$runtimeCache = $all;
        return $saved;
    }
}
