<?php

namespace App\Services\RupAudit;

class RupTaxonomy
{
    // DICT_PPPK_DAN_IURAN_ASN: Komoditas Kepegawaian ASN Sah (UU 20/2023 & BPJS)
    public const DICT_PPPK_DAN_IURAN_ASN = [
        'pppk',
        'pegawai pemerintah dengan perjanjian kerja',
        'paruh waktu',
        'iuran jaminan kesehatan',
        'iuran jaminan kecelakaan kerja',
        'iuran jaminan kematian',
    ];

    // DICT_01: Tarif Resmi Pemerintah & Kewajiban Statutori
    public const DICT_01_DIKECUALIKAN = [
        'tagihan listrik',
        'tagihan air',
        'tagihan telepon',
        'langganan daya',
        'kawat/faksimili',
        'internet',
        'pembayaran pajak',
        'pajak kendaraan',
        'stnk',
        'bpkb',
        'bea, dan perizinan',
        'bea dan perizinan',
        'retribusi',
        'benda pos',
        'materai',
        'penerimaan negara bukan pajak',
        'pnbp',
        'tera ulang',
        'publikasi media massa pemerintah',
        'pppk',
        'pegawai pemerintah dengan perjanjian kerja',
        'paruh waktu',
        'iuran jaminan kesehatan',
        'iuran jaminan kecelakaan kerja',
        'iuran jaminan kematian',
    ];

    // DICT_02: Jasa Tenaga Alih Daya (Outsourcing) / Pendukung Non-ASN
    public const DICT_02_OUTSOURCING = [
        'tenaga pelayanan umum',
        'tenaga kebersihan',
        'cleaning service',
        'tenaga keamanan',
        'satpam',
        'penjaga malam',
        'tenaga operator',
        'tenaga sopir',
        'pengemudi',
        'pramubakti',
        'caraka',
        'tenaga administrasi',
        'tenaga teknis',
        'juru masak',
        'alih daya',
        'outsourcing',
        'jasa tenaga kerja',
        'petugas taman',
        'pthl',
        'tks',
        'tenaga kerja sukarela',
        'tenaga harian lepas',
        'tenaga pendukung',
        'jaga leuweung',
    ];

    public const NEG_LOOKAHEAD_02 = self::DICT_PPPK_DAN_IURAN_ASN;

    // DICT_03: Belanja Pegawai, Honorarium & Administrasi Internal
    public const DICT_03_SWAKELOLA_MURNI = [
        'perjalanan dinas',
        'uang harian',
        'honorarium',
        'uang lembur',
        'tunjangan',
        'bimbingan teknis',
        'bimtek',
        'sosialisasi',
        'kontribusi',
        'iuran asosiasi',
        'uang saku',
        'jasa pelayanan kesehatan bagi asn',
        'gaji dan tunjangan',
        'insentif pemungutan',
        'pppk',
        'pegawai pemerintah dengan perjanjian kerja',
        'paruh waktu',
        'iuran jaminan kesehatan',
        'iuran jaminan kecelakaan kerja',
        'iuran jaminan kematian',
    ];

    public const NEG_LOOKAHEAD_03 = [
        'makan',
        'minum',
        'cetak',
        'alat tulis',
        'atk',
        'spanduk',
        'umbul',
        'event organizer',
        'hotel',
        'akomodasi',
        'sewa',
        'pihak ketiga',
        'paket meeting',
        'fullboard',
        'fullday',
        'halfday',
        'penginapan',
        'uptd pelatihan',
        'balai pelatihan',
        'alat pelatihan',
        'bahan praktek',
        'konsumsi',
        'jasa kebersihan',
        'jasa keamanan',
        'tenaga kebersihan',
        'tenaga keamanan',
    ];

    // DICT_04: Komoditas Wajib E-Purchasing Katalog V6
    public const DICT_04_KATALOG_STANDAR = [
        'alat tulis kantor',
        'atk',
        'bahan cetak',
        'kertas dan cover',
        'alat listrik',
        'makan dan minuman',
        'makanan dan minuman',
        'makan minuman',
        'jamuan',
        'katering',
        'bahan bakar',
        'pelumas',
        'bbm',
        'sewa kendaraan',
        'pemeliharaan kendaraan',
        'pemeliharaan alat angkutan',
        'pemeliharaan komputer',
        'pemeliharaan printer',
        'pemeliharaan ac',
        'alat pendingin',
        'laptop',
        'personal computer',
        'printer',
        'obat-obatan',
        'bahan medis habis pakai',
        'bmhp',
        'daya tahan tubuh',
        'vitamin',
        'aspal',
        'hotmix',
        'beton ready mix',
        'pakaian olahraga',
        'pakaian kerja',
        'pakaian dinas',
        'seragam',
        'meubelair',
        'mebel',
    ];

    // DICT_05: Pekerjaan Fisik Konstruksi
    public const DICT_05_MURNI_KONSTRUKSI = [
        'pembangunan ',
        'rehabilitasi ',
        'renovasi ',
        'renovasi',
        'rekonstruksi ',
        'peningkatan jalan',
        'pemeliharaan rutin jalan',
        'pemeliharaan berkala jalan',
        'lapis penetrasi',
        'pagar ',
        'irigasi',
        'jaringan irigasi',
        'drainase',
        'talud',
        'bronjong',
        'sumur bor',
        'instalasi pengolahan air',
        'gedung dan bangunan',
        'pekerjaan konstruksi',
        'jalan dan jembatan',
        'pengerukan',
        'normalisasi sungai',
    ];

    public const NEG_LOOKAHEAD_05 = [
        'konsultansi',
        'konsultan',
        'perencanaan',
        'pengawasan',
        'manajemen konstruksi',
        'studi',
        'ded',
        'suku cadang',
        'alat angkutan',
        'kendaraan',
        'sistem informasi',
        'aplikasi',
        'publikasi',
        'iklan',
        'media',
        'tv',
        'radio',
        'advetorial',
        'rehabilitasi medis',
        'rehabilitasi sosial',
        'alat kesehatan',
        'sewa ',
        'bahan bangunan',
        'bahan material',
        'bahan baku',
        'pertukangan',
        'alat tulis',
        'bahan cetak',
        'makan',
        'penyusunan',
        'dokumen',
        'survey',
        'survei',
        'inventarisasi',
        'maket',
        'bahan bakar',
        'bbm',
        'pelumas',
        'belanja hotmix',
        'material hotmix',
        'bahan/material',
        'bahan-bahan lainnya',
        'penyambungan listrik',
        'tunda bayar',
        'pembebasan tanah',
        'pengadaan tanah',
        'appraisal',
        'uptd pengelolaan jalan dan jembatan',
        'uptd pengelolaan sumber daya air',
    ];

    // DICT_06: Jasa Konsultansi Konstruksi & Non-Konstruksi
    public const DICT_06_MURNI_KONSULTANSI = [
        'jasa konsultansi',
        'jasa konsultasi',
        'tenaga ahli',
        'perencanaan konstruksi',
        'perencanaan teknis',
        'pengawasan konstruksi',
        'pengawasan teknis',
        'manajemen konstruksi',
        'studi kelayakan',
        'feasibility study',
        'ded ',
        'detail engineering',
        'penyusunan dokumen',
        'masterplan',
        'kajian',
        'naskah akademik',
        'audit',
        'survailance',
        'surveilans',
        'appraisal',
        'penilaian aset',
        'survey kepuasan',
        'amdal',
        'ukl-upl',
        'andalalin',
    ];

    public const NEG_LOOKAHEAD_06 = [
        'alat tulis',
        'bahan cetak',
        'makan',
        'minum',
        'honorarium',
        'penyelenggaraan acara',
        'penyelenggara acara',
        'event organizer',
        'mebelair',
        'mebel',
        'meubelair',
        'suvenir',
        'souvenir',
        'cinderamata',
        'pelatihan tenaga ahli',
        'sertifikasi kompetensi',
        'kit surveilans',
        'kit ',
        'reagen',
        'pembebasan tanah',
        'pengadaan tanah',
        'honorarium narasumber',
    ];

    // DICT_07: Jasa Non-Konsultansi / Operasional
    public const DICT_07_MURNI_JASA_LAINNYA = [
        'belanja sewa',
        'sewa ',
        'pemeliharaan alat',
        'pemeliharaan kendaraan',
        'pemeliharaan peralatan',
        'perawatan kendaraan',
        'jasa kebersihan',
        'jasa keamanan',
        'jasa kalibrasi',
        'jasa pengolahan',
        'jasa pencucian',
        'jasa penjahitan',
        'event organizer',
        'penyelenggaraan acara',
        'publikasi',
        'iklan',
        'pengiriman',
        'ekspedisi',
        'asuransi',
        'medical check up',
        'langganan jurnal',
        'jasa pembasmian',
        'pest control',
    ];

    public const NEG_LOOKAHEAD_07 = [
        'belanja modal',
        'pengadaan alat',
    ];

    // DICT_08: Aset Modal Peralatan/Mesin & Barang Habis Pakai
    public const DICT_08_MURNI_BARANG = [
        'belanja modal alat',
        'belanja modal komputer',
        'belanja modal kendaraan',
        'belanja modal mebel',
        'belanja peralatan dan mesin',
        'suku cadang',
        'bahan kimia',
        'bahan bangunan',
        'bahan bibit',
        'bahan pakan',
        'obat-obatan',
        'alat tulis kantor',
        'kertas dan cover',
        'alat listrik',
        'souvenir',
        'cinderamata',
        'hadiah',
        'pupuk',
        'alat peraga',
    ];

    public const NEG_LOOKAHEAD_08 = [
        'sewa',
        'pemeliharaan',
        'perbaikan',
        'kalibrasi',
        'jasa',
    ];

    // DICT_09: Belanja Hibah / Bantuan Sosial Barang/Uang
    public const DICT_09_HIBAH_BANSOS = [
        'belanja hibah',
        'hibah barang',
        'hibah uang',
        'bantuan sosial',
        'bansos',
        'bantuan keuangan',
        'diserahkan kepada masyarakat',
        'diserahkan kepada pihak ketiga',
    ];

    // DICT_10: Komoditas Spesifik / Dominan Impor
    public const DICT_10_TEKNOLOGI_TINGGI_IMPOR = [
        'bahan kimia',
        'reagen',
        'alat laboratorium',
        'instrumen analisis',
        'alat kesehatan',
        'alat kedokteran',
        'server',
        'lisensi',
        'spektrofotometer',
        'kromatografi',
        'radiologi',
        'hemodialisa',
        'mri',
        'ct scan',
        'cathlab',
    ];

    // DICT_11: Komoditas Keadaan Tertentu / Hotel / RO sesuai Perpres 46/2025
    public const DICT_11_PENUNJUKAN_LANGSUNG_SAH = [
        'repeat order',
        'sewa hotel',
        'penginapan',
        'ruang rapat hotel',
        'akomodasi rapat',
        'darurat bencana',
        'penanganan darurat',
        'hak paten',
        'penyedia tunggal',
        'penugasan presiden',
        'penugasan pemerintah',
        'rahasia negara',
        'saksi ahli',
        'bantuan hukum',
    ];

    // DICT_12: Pola Regex Eksak Penamaan Kabur / Opaque Naming
    public const REGEX_DICT_12_OPAQUE = '/^(belanja|pengadaan|penyediaan)\s+(barang|jasa|barang dan jasa|modal|operasional|rutin|kebutuhan kantor|peralatan|perlengkapan)$/i';

    // LIST_OPAQUE_GENERIC: Daftar Generik Non-Komoditas Pemicu R-34
    public const LIST_OPAQUE_GENERIC = [
        'belanja barang',
        'belanja jasa',
        'belanja barang dan jasa',
        'belanja modal',
        'pengadaan barang',
        'pengadaan jasa',
        'belanja operasional',
        'belanja rutin',
        'tunda bayar',
        'belanja honorarium',
        'bopd sma',
        'bopd smk',
        'bopd slb',
        'bopd',
        'belanja bahan-bahan lainnya',
        'belanja bahan bahan lainnya',
        'belanja bahan lainnya',
        'pelayanan nonmedik lainnya',
        'pemeliharaan aset tetap lainnya',
        'penyediaan jasa konsultan',
        'belanja jasa non personil',
        'belanja jasa yang diberikan kepada pihak ketiga/pihak lain',
    ];

    // DICT_KONSTRUKSI_KOMPLEKS: Kata Kunci Konstruksi Kompleks / Berisiko / Tidak Sederhana (Perlem LKPP 3/2026)
    public const DICT_KONSTRUKSI_KOMPLEKS = [
        'jembatan',
        'tiang pancang',
        'bore pile',
        'gedung bertingkat',
        'bendung',
        'bendungan',
        'instalasi pengolahan air limbah',
        'ipal',
        'bunker',
        'radioterapi',
    ];

    // DICT_KONSULTANSI_KONSTRUKSI: Kata Kunci Jasa Konsultansi Konstruksi (Pasal 4 ayat (2) Perlem LKPP 3/2026)
    public const DICT_KONSULTANSI_KONSTRUKSI = [
        'konstruksi',
        'perencanaan',
        'pengawasan',
        'ded',
        'detail engineering',
        'manajemen konstruksi',
        'arsitektur',
        'gedung',
        'jalan',
        'jembatan',
        'irigasi',
        'drainase',
        'sungai',
        'bangunan',
    ];

    // EXCLUSION_R04B: Pengecualian Anti-False Positive untuk Sub-Rule R-04b
    public const EXCLUSION_R04B = [
        'kendaraan dinas bermotor perorangan',
        'kendaraan perorangan',
        'tenaga listrik',
        'tambah daya',
        'alat angkutan',
    ];

    /**
     * Mengevaluasi apakah suatu teks cocok dengan salah satu kata kunci di kamus
     * dengan mempertimbangkan Negative Lookahead jika ada.
     */
    public static function match(string $text, array $keywords, array $negativeLookahead = []): bool
    {
        $textLower = strtolower(trim($text));
        if ($textLower === '') {
            return false;
        }

        // Cek negative lookahead terlebih dahulu
        foreach ($negativeLookahead as $neg) {
            if (str_contains($textLower, strtolower($neg))) {
                return false;
            }
        }

        // Cek apakah ada kata kunci yang cocok
        foreach ($keywords as $kw) {
            if (str_contains($textLower, strtolower($kw))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah nama paket tergolong Opaque / Kabur
     */
    public static function isOpaque(string $textClean): bool
    {
        $textClean = trim(strtolower($textClean));
        if ($textClean === '') {
            return true;
        }
        return (bool) preg_match(self::REGEX_DICT_12_OPAQUE, $textClean);
    }

    /**
     * Memeriksa apakah suatu paket pengadaan merupakan Jasa Konsultansi Konstruksi
     * berdasarkan jenis pengadaan dan objek belanja (Pasal 4 ayat (2) Perlem LKPP 3/2026).
     */
    public static function isKonsultansiKonstruksi(array $pkg): bool
    {
        $jenisArr = $pkg['jenis_arr'] ?? [];
        $isKonsultansi = in_array('Jasa Konsultansi', $jenisArr, true);
        if (! $isKonsultansi && isset($pkg['jenis_pengadaan'])) {
            $isKonsultansi = str_contains(strtolower((string) $pkg['jenis_pengadaan']), 'konsultansi');
        }

        if (! $isKonsultansi) {
            return false;
        }

        $textToSearch = strtolower(trim(($pkg['nama_paket_objek'] ?? '') . ' ' . ($pkg['nama_paket_clean'] ?? '') . ' ' . ($pkg['nama_paket'] ?? '')));
        return self::match($textToSearch, self::DICT_KONSULTANSI_KONSTRUKSI);
    }

    /**
     * Menentukan subkualifikasi usaha dan ketentuan afirmasi domisili pekerjaan konstruksi
     * berdasarkan Pasal 4 ayat (1) dan Lampiran II Angka 1.1 Peraturan LKPP Nomor 3 Tahun 2026.
     */
    public static function getSubkualifikasiKonstruksi(float $nilai): array
    {
        if ($nilai <= 2000000000.0) {
            return [
                'subkualifikasi'    => 'Kualifikasi Kecil Subkualifikasi K1',
                'afirmasi_domisili' => 'Prioritas Penyedia berdomisili di Kabupaten/Kota lokasi pekerjaan',
                'kode_sub'          => 'K1',
                'keterangan'        => 'Pasal 4 ayat (1) huruf a jo. Lampiran II Angka 1.1 Perlem LKPP 3/2026',
            ];
        }

        if ($nilai <= 7500000000.0) {
            return [
                'subkualifikasi'    => 'Kualifikasi Kecil Subkualifikasi K2',
                'afirmasi_domisili' => 'Prioritas Penyedia berdomisili di Provinsi lokasi pekerjaan',
                'kode_sub'          => 'K2',
                'keterangan'        => 'Pasal 4 ayat (1) huruf b jo. Lampiran II Angka 1.1 Perlem LKPP 3/2026',
            ];
        }

        if ($nilai <= 15000000000.0) {
            return [
                'subkualifikasi'    => 'Kualifikasi Kecil Subkualifikasi K3',
                'afirmasi_domisili' => 'Penyedia Kualifikasi Usaha Kecil Nasional',
                'kode_sub'          => 'K3',
                'keterangan'        => 'Pasal 4 ayat (1) huruf c jo. Lampiran II Angka 1.1 Perlem LKPP 3/2026',
            ];
        }

        if ($nilai <= 50000000000.0) {
            $subkontrakWajib = $nilai > 25000000000.0 ? ' (Wajib Subkontrak ke Usaha Kecil sesuai Lampiran II Angka 2.3.8)' : '';
            return [
                'subkualifikasi'    => 'Kualifikasi Usaha Menengah' . $subkontrakWajib,
                'afirmasi_domisili' => 'Penyedia Kualifikasi Usaha Menengah',
                'kode_sub'          => 'Menengah',
                'keterangan'        => 'Pasal 4 ayat (1) huruf d jo. Lampiran II Angka 1.1 & 2.3.8 Perlem LKPP 3/2026',
            ];
        }

        return [
            'subkualifikasi'    => 'Kualifikasi Usaha Besar',
            'afirmasi_domisili' => 'Penyedia Kualifikasi Usaha Besar (Wajib Kerjasama/Subkontrak Usaha Kecil)',
            'kode_sub'          => 'Besar',
            'keterangan'        => 'Pasal 4 ayat (1) huruf e jo. Lampiran II Angka 1.1 Perlem LKPP 3/2026',
        ];
    }
}
