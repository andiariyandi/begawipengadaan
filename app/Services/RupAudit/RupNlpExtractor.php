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
        $text = strtolower(trim($raw));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', (string) $text));
    }

    /**
     * Ekstraksi Objek Utama Pengadaan (nama_paket_objek)
     * 1. Hapus keterangan di dalam tanda kurung (...)
     * 2. Potong klausa konteks setelah "sub kegiatan ...", "sub bagian ...", atau "dalam rangka ..."
     */
    public static function extractObject(string $raw): string
    {
        $text = trim($raw);
        // 1. Hapus kurung dan isinya
        $text = preg_replace('/\([^\)]*\)/', ' ', $text);

        // 2. Potong klausa sub kegiatan jika klausa di depannya cukup representatif (>= 10 karakter)
        $lower = strtolower((string) $text);
        $splitPatterns = ['sub kegiatan', 'sub bagian', 'dalam rangka'];
        foreach ($splitPatterns as $splitWord) {
            $pos = strpos($lower, $splitWord);
            if ($pos !== false && $pos >= 10) {
                $text = substr((string) $text, 0, $pos);
                $lower = strtolower((string) $text);
            }
        }

        return self::clean((string) $text);
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
