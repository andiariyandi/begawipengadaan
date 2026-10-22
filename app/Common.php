<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

// Enforce Asia/Jakarta (WIB / GMT+7) across the entire application runtime
if (date_default_timezone_get() !== 'Asia/Jakarta') {
    date_default_timezone_set('Asia/Jakarta');
}

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (is_cli() && ! (service('request') instanceof \CodeIgniter\HTTP\IncomingRequest)) {
            return config('Security')->tokenName ?? 'csrf_test_name';
        }
        return service('security')->getTokenName();
    }
}

if (! function_exists('csrf_hash')) {
    function csrf_hash(): string
    {
        if (is_cli() && ! (service('request') instanceof \CodeIgniter\HTTP\IncomingRequest)) {
            return 'cli_dummy_csrf_hash';
        }
        return service('security')->getHash();
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(?string $id = null): string
    {
        return '<input type="hidden"' . ($id !== null ? ' id="' . esc($id, 'attr') . '"' : '') . ' name="' . csrf_token() . '" value="' . csrf_hash() . '" />';
    }
}

if (! function_exists('old')) {
    /**
     * CLI-safe wrapper for old input retrieval.
     */
    function old(string $key, $default = null, $escape = 'html')
    {
        $request = service('request');
        if (! ($request instanceof \CodeIgniter\HTTP\IncomingRequest)) {
            return $default;
        }

        if (session_status() === PHP_SESSION_NONE && ENVIRONMENT !== 'testing') {
            session();
        }

        $value = $request->getOldInput($key);
        if ($value === null) {
            return $default;
        }

        return $escape === false ? $value : esc($value, $escape);
    }
}

if (! function_exists('setting')) {
    /**
     * Retrieve a site setting by key, e.g. setting('organization.agency_name') or setting('agency_name')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $service = null;
        if ($service === null) {
            $service = new \App\Services\SystemSettingService();
        }
        return $service->getSetting($key, $default);
    }
}

if (! function_exists('learning_jp_count')) {
    /**
     * Hitung total Jam Pelajaran (JP) dengan standar kurikulum PBJ: 1 JP = 60 menit pembelajaran.
     */
    function learning_jp_count(int|float|null $minutes): float|int
    {
        $min = (float) ($minutes ?? 0);
        if ($min <= 0) {
            return 0;
        }
        $jp = $min / 60.0;
        return (fmod($min, 60.0) == 0.0) ? (int) $jp : round($jp, 1);
    }
}

if (! function_exists('format_learning_jp')) {
    /**
     * Format informasi durasi belajar & JP pembelajaran (1 JP = 60 menit pembelajaran).
     * Contoh: 180 menit -> "3 JP (180 Menit)"
     */
    function format_learning_jp(int|float|null $minutes, bool $withNote = false): string
    {
        $min = (int) ($minutes ?? 0);
        if ($min <= 0) {
            return 'Mandiri / Fleksibel';
        }
        $jp = learning_jp_count($min);
        $formatted = "{$jp} JP ({$min} Menit)";
        if ($withNote) {
            $formatted .= ' (1 JP = 60 Menit)';
        }
        return $formatted;
    }
}

if (! function_exists('default_batch_stages')) {
    /**
     * Template tahapan standar alur pembelajaran batch PBJ (4 Tahapan Utama).
     */
    function default_batch_stages(): array
    {
        return [
            [
                'number'      => 1,
                'title'       => 'Orientasi & Pre-Test',
                'description' => 'Pengenalan silabus dan asesmen diagnostik awal guna memetakan pemahaman dasar peserta sebelum memasuki modul materi.',
            ],
            [
                'number'      => 2,
                'title'       => 'Modul Pembelajaran & Kuis',
                'description' => 'Mempelajari materi regulasi, studi kasus LKPP, video tutorial, serta menyelesaikan kuis formatif pada setiap modul.',
            ],
            [
                'number'      => 3,
                'title'       => 'Tugas Praktik & Evaluasi',
                'description' => 'Menyusun dokumen praktikum (RUP, HPS, spesifikasi teknis, atau draft kontrak) yang dievaluasi langsung oleh instruktur fasilitator.',
            ],
            [
                'number'      => 4,
                'title'       => 'Post-Test & Penerbitan Sertifikat',
                'description' => 'Ujian komprehensif penentu kelulusan. Peserta yang melampaui passing grade berhak mengunduh sertifikat resmi ber-QR Code verifikasi.',
            ],
        ];
    }
}

if (! function_exists('parse_class_batch_data')) {
    /**
     * Membaca dan mengekstrak data metadata batch (tahapan pembelajaran & custom duration)
     * yang tersimpan di dalam field deskripsi kelas tanpa migrasi atau perubahan skema tabel.
     */
    function parse_class_batch_data(?string $rawDescription): array
    {
        $defaultSubtitle = 'Alur pengerjaan yang harus dilalui oleh peserta selama periode kelas berlangsung:';
        if (empty($rawDescription)) {
            return [
                'description'             => '',
                'stages_subtitle'         => $defaultSubtitle,
                'stages'                  => default_batch_stages(),
                'custom_duration_minutes' => null,
                'has_custom_stages'       => false,
            ];
        }

        // Cek apakah terdapat blok JSON metadata di dalam deskripsi
        if (preg_match('/<!--BATCH_STAGES:(.*?):BATCH_STAGES-->/s', $rawDescription, $matches)) {
            $meta = json_decode(trim($matches[1]), true);
            $cleanDescription = trim(preg_replace('/<!--BATCH_STAGES:.*?:BATCH_STAGES-->/s', '', $rawDescription));

            if (is_array($meta)) {
                $stages = [];
                if (! empty($meta['stages']) && is_array($meta['stages'])) {
                    foreach ($meta['stages'] as $idx => $stg) {
                        $stages[] = [
                            'number'      => (int) ($stg['number'] ?? ($idx + 1)),
                            'title'       => trim((string) ($stg['title'] ?? '')),
                            'description' => trim((string) ($stg['description'] ?? '')),
                        ];
                    }
                }
                if (empty($stages)) {
                    $stages = default_batch_stages();
                }

                return [
                    'description'             => $cleanDescription,
                    'stages_subtitle'         => ! empty($meta['subtitle']) ? trim((string) $meta['subtitle']) : $defaultSubtitle,
                    'stages'                  => $stages,
                    'custom_duration_minutes' => ! empty($meta['custom_duration']) ? (int) $meta['custom_duration'] : null,
                    'has_custom_stages'       => true,
                ];
            }
        }

        return [
            'description'             => $rawDescription,
            'stages_subtitle'         => $defaultSubtitle,
            'stages'                  => default_batch_stages(),
            'custom_duration_minutes' => null,
            'has_custom_stages'       => false,
        ];
    }
}

if (! function_exists('parse_class_batch_description')) {
    /**
     * Alias for parse_class_batch_data.
     */
    function parse_class_batch_description(?string $rawDescription): array
    {
        return parse_class_batch_data($rawDescription);
    }
}

if (! function_exists('build_class_batch_description')) {
    /**
     * Mengemas deskripsi teks dan metadata tahapan batch ke dalam satu string tersimpan
     * yang kompatibel dan aman tanpa migrasi tabel.
     */
    function build_class_batch_description(
        string $cleanDescription,
        array $stages = [],
        string $stagesSubtitle = '',
        ?int $customDurationMinutes = null
    ): string {
        $cleanDescription = trim($cleanDescription);
        $cleanStages = [];
        foreach ($stages as $idx => $stg) {
            $title = trim((string) ($stg['title'] ?? ''));
            $desc  = trim((string) ($stg['description'] ?? ''));
            if ($title !== '' || $desc !== '') {
                $cleanStages[] = [
                    'number'      => count($cleanStages) + 1,
                    'title'       => $title,
                    'description' => $desc,
                ];
            }
        }

        if (empty($cleanStages) && empty($stagesSubtitle) && empty($customDurationMinutes)) {
            return $cleanDescription;
        }

        $payload = [
            'subtitle'        => trim($stagesSubtitle),
            'stages'          => $cleanStages,
            'custom_duration' => $customDurationMinutes > 0 ? $customDurationMinutes : null,
        ];

        $marker = "\n\n<!--BATCH_STAGES:" . json_encode($payload, JSON_UNESCAPED_UNICODE) . ":BATCH_STAGES-->";
        return $cleanDescription . $marker;
    }
}

if (! function_exists('parse_assignment_guide')) {
    /**
     * Mengekstrak metadata file petunjuk tugas PDF dari kolom instructions tanpa migrasi tabel.
     *
     * @return array{clean_instructions: string, guide_url: ?string, guide_filename: ?string, guide_size: ?int}
     */
    function parse_assignment_guide(?string $rawInstructions): array
    {
        $rawInstructions = (string) $rawInstructions;
        $pattern = '/<!--ASSIGNMENT_GUIDE:(.*?):ASSIGNMENT_GUIDE-->/s';
        if (preg_match($pattern, $rawInstructions, $matches)) {
            $cleanInstructions = trim(preg_replace($pattern, '', $rawInstructions));
            $payload = json_decode(trim($matches[1]), true);
            if (is_array($payload)) {
                $guideUrl = ! empty($payload['url']) ? (string) $payload['url'] : null;
                return [
                    'clean_instructions' => $cleanInstructions,
                    'instructions'       => $cleanInstructions,
                    'has_guide'          => ! empty($guideUrl),
                    'guide_url'          => $guideUrl,
                    'guide_filename'     => ! empty($payload['filename']) ? (string) $payload['filename'] : null,
                    'guide_size'         => isset($payload['size']) ? (int) $payload['size'] : null,
                ];
            }
        }

        return [
            'clean_instructions' => $rawInstructions,
            'instructions'       => $rawInstructions,
            'has_guide'          => false,
            'guide_url'          => null,
            'guide_filename'     => null,
            'guide_size'         => null,
        ];
    }
}

if (! function_exists('build_assignment_instructions')) {
    /**
     * Membungkus teks instruksi tugas dan metadata file petunjuk PDF ke dalam string instructions.
     */
    function build_assignment_instructions(string $cleanInstructions, ?string $guideUrl = null, ?string $guideFilename = null, ?int $guideSize = null): string
    {
        $cleanInstructions = trim($cleanInstructions);
        if (empty($guideUrl)) {
            return $cleanInstructions;
        }

        $payload = [
            'url'      => $guideUrl,
            'filename' => $guideFilename ?: basename($guideUrl),
            'size'     => $guideSize,
        ];

        $marker = "\n\n<!--ASSIGNMENT_GUIDE:" . json_encode($payload, JSON_UNESCAPED_UNICODE) . ":ASSIGNMENT_GUIDE-->";
        return $cleanInstructions . $marker;
    }
}

