<?php

namespace App\Services\RupAudit;

class RupNlpExtractor
{
    private static array $synonyms = [
        '/\bmamin\b/i'  => 'makan minum',
        '/\batk\b/i'    => 'alat tulis kantor',
        '/\bbmhp\b/i'   => 'bahan medis habis pakai',
        '/\bac\b/i'     => 'alat pendingin',
        '/\bpc\b/i'     => 'komputer',
    ];

    private static array $stopwords = [
        'dan', 'atau', 'pada', 'di', 'ke', 'dari', 'untuk', 'dengan', 'yang', 'oleh',
        'ini', 'itu', 'tahun', 'anggaran', 'ta', 'tahap', 'triwulan', 'tw', 'bulan',
        'paket', 'kegiatan', 'sub', 'bagian', 'belanja', 'pengadaan', 'penyediaan',
        'biaya', 'jasa', 'barang', 'pekerjaan', 'modal', 'operasional', 'rutin',
    ];

    /**
     * Daftar akronim teknis/resmi yang dilindungi dari de-obfuskasi angka leetspeak.
     */
    private static array $preservedAcronyms = [
        'a4', 'f4', 's1', 's2', 's3', 'd1', 'd3', 'd4', 'p3d', 'p3dw', 'p3jb', 'p3k',
        'tps3r', 'sp3or', 'o2sn', 'lp2b', 'sp2d', 'h2m', 'pd3i', 'co2', '4g', '5g',
        'k1', 'k2', 'k3', 'r2', 'r4', 'l300',
    ];

    /**
     * Kamus normalisasi salah ketik (typo) umum pada paket pengadaan daerah.
     */
    private static array $typoReplacements = [
        'belanaja'      => 'belanja',
        'runag'         => 'ruang',
        'frakssi'       => 'fraksi',
        'oprassional'   => 'operasional',
        'opersional'    => 'operasional',
        'pemeliharan'   => 'pemeliharaan',
        'boerorientasi' => 'berorientasi',
        'penysunan'     => 'penyusunan',
        'interiro'      => 'interior',
    ];

    /**
     * De-obfuskasi angka leetspeak dan normalisasi kata salah ketik pada nama paket pengadaan.
     */
    public static function normalizeTyposAndLeetspeak(string $text): string
    {
        if (trim($text) === '') {
            return '';
        }

        static $preservedMap = null;
        if ($preservedMap === null) {
            $preservedMap = array_fill_keys(self::$preservedAcronyms, true);
        }

        // 1. De-obfuskasi substitusi leetspeak pada kata huruf yang disisipi angka (4->a, 0->o, 1->i, 3->e, 5->s)
        $normalized = preg_replace_callback('/\b[a-zA-Z0-9]+\b/', static function (array $m) use ($preservedMap): string {
            $word  = $m[0];
            $lower = strtolower($word);

            // Lindungi akronim resmi/teknis
            if (isset($preservedMap[$lower])) {
                return $word;
            }

            // Lindungi angka murni
            if (ctype_digit($word)) {
                return $word;
            }

            // Jika kata mengandung kombinasi huruf dan angka, lakukan de-obfuskasi
            if (preg_match('/[a-zA-Z]/', $word) && preg_match('/[0-9]/', $word)) {
                return strtr($word, [
                    '4' => 'a',
                    '0' => 'o',
                    '1' => 'i',
                    '3' => 'e',
                    '5' => 's',
                ]);
            }

            return $word;
        }, $text);

        // 2. Normalisasi kata typo pengadaan
        foreach (self::$typoReplacements as $typo => $fix) {
            $normalized = preg_replace('/\b' . preg_quote($typo, '/') . '\b/i', $fix, (string) $normalized);
        }

        return (string) $normalized;
    }

    /**
     * Memisahkan nama OPD bersih dan kode satker SIPD (22 digit) dari nama satuan kerja.
     */
    public static function extractOpdAndSipd(string $rawSatker): array
    {
        $rawSatker = trim($rawSatker);
        $cleanOpd  = $rawSatker;
        $sipdCode  = '';

        // Deteksi pola strip diikuti kode numerik/titik di akhir string (misal: " - 1.03.0.00.0.00.06.0000")
        if (preg_match('/^(.*?)\s*-\s*([0-9\.]+)$/', $rawSatker, $matches)) {
            $cleanOpd = trim($matches[1]);
            $sipdCode = trim($matches[2]);
        }

        return [
            'nama_opd_clean'   => strtoupper($cleanOpd),
            'kode_satker_sipd' => $sipdCode,
        ];
    }

    /**
     * Menghasilkan nama_paket_clean (lowercase, alfanumerik & spasi tunggal).
     */
    public static function clean(string $raw): string
    {
        $text = self::normalizeTyposAndLeetspeak($raw);
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', (string) $text));
    }

    /**
     * Ekstraksi Objek Utama Pengadaan (nama_paket_objek)
     * 1. Hapus keterangan di dalam tanda kurung (...)
     * 2. Potong klausa konteks setelah: sub kegiatan, sub bagian, dalam rangka, pada kegiatan, kegiatan pelaksanaan, guna kegiatan (jika teks di depannya >= 10 karakter alfanumerik)
     */
    public static function extractObject(string $raw): string
    {
        $text = self::normalizeTyposAndLeetspeak(trim($raw));
        // 1. Hapus kurung dan isinya
        $text = preg_replace('/\([^\)]*\)/', ' ', $text);

        // 2. Potong klausa konteks kegiatan jika teks objek di depannya sudah >= 10 karakter alfanumerik
        if (preg_match('/\b(sub\s+kegiatan|sub\s+bagian|dalam\s+rangka|pada\s+kegiatan|kegiatan\s+pelaksanaan|guna\s+kegiatan)\b/i', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = (int) $matches[0][1];
            $preceding = substr($text, 0, $pos);
            $alnumCount = preg_match_all('/[a-zA-Z0-9]/', $preceding);
            if ($alnumCount >= 10) {
                $text = $preceding;
            }
        }

        return self::clean((string) $text);
    }

    /**
     * Alias resmi untuk ekstraksi objek utama pengadaan (extractPrimaryObject).
     */
    public static function extractPrimaryObject(string $raw): string
    {
        return self::extractObject($raw);
    }

    /**
     * Kalibrasi Presisi Penamaan Paket Kabur / Opaque Naming (R-34)
     * Picu R-34 HANYA JIKA:
     * a) Panjang nama_paket_objek < 6 karakter atau token_count == 0; ATAU
     * b) Cocok dengan REGEX_DICT_12_OPAQUE; ATAU
     * c) nama_paket_objek termasuk dalam daftar generik non-komoditas
     */
    public static function isOpaquePackageName(array $pkg): bool
    {
        $objek = strtolower(trim((string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '')));
        $clean = strtolower(trim((string) ($pkg['nama_paket_clean'] ?? '')));
        $tokenCount = (int) ($pkg['token_count'] ?? 0);

        // a) Panjang nama_paket_objek < 6 karakter atau token_count == 0
        if (strlen($objek) < 6 || $tokenCount === 0) {
            return true;
        }

        // b) Cocok dengan REGEX_DICT_12_OPAQUE
        if (RupTaxonomy::isOpaque($objek) || RupTaxonomy::isOpaque($clean)) {
            return true;
        }

        // c) nama_paket_objek termasuk dalam daftar generik non-komoditas
        if (in_array($objek, RupTaxonomy::LIST_OPAQUE_GENERIC, true) || in_array($clean, RupTaxonomy::LIST_OPAQUE_GENERIC, true)) {
            return true;
        }

        return false;
    }

    /**
     * Ekstraksi Inti Komoditas (nama_paket_core) dan Entitas Lokasi
     */
    public static function extractCoreAndLocation(string $objekText): array
    {
        $text = strtolower(trim($objekText));

        // 1. Pemetaan sinonim baku
        foreach (self::$synonyms as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, (string) $text);
        }

        // 2. Hapus prefiks generik pengadaan di awal
        $text = preg_replace('/^(belanja|pengadaan|biaya|pekerjaan|penyediaan|sub kegiatan)\s+(modal|barang|jasa|operasional)?\s*/i', '', (string) $text);

        // 3. Ekstraksi sufiks lokasi
        $lokasiExtracted = null;
        if (preg_match('/\s+(pada|di|lokasi|kecamatan|kelurahan|desa|ruas)\s+([a-z0-9\s]+)$/i', (string) $text, $locMatches)) {
            $lokasiExtracted = trim($locMatches[2]);
            $text = substr((string) $text, 0, -strlen($locMatches[0]));
        }

        // 4. Hapus referensi waktu, tahun, dan tahap
        $text = preg_replace('/\b(tahap\s+[0-9]+|triwulan\s+[0-9]+|tw\s+[0-9]+|202[0-9]|203[0-9])\b/i', '', (string) $text);

        $coreClean = self::clean((string) $text);

        // 5. Hitung token bermakna
        $tokens = array_filter(explode(' ', $coreClean), static function ($token) {
            return strlen($token) > 1 && ! in_array($token, self::$stopwords, true);
        });

        return [
            'nama_paket_core'  => implode(' ', array_values($tokens)) ?: $coreClean,
            'lokasi_extracted' => $lokasiExtracted,
            'token_count'      => count($tokens),
            'tokens'           => array_values($tokens),
        ];
    }
}
