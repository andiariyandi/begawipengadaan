<?php

namespace App\Services\RupAudit;

class RupRuleEngine
{
    /**
     * Helper evaluasi cepat untuk satu paket (khusus testing atau audit baris satuan).
     *
     * @param array $pkg
     * @return array
     */
    public static function evaluatePackage(array $pkg): array
    {
        $packages = [$pkg];
        $packages[0]['row_index_csv']   = $pkg['row_index_csv'] ?? 1;
        $packages[0]['nama_opd_clean']  = $pkg['nama_opd_clean'] ?? 'OPD UJI';
        $packages[0]['kode_satker_sipd']= $pkg['kode_satker_sipd'] ?? '1.00.00';
        $packages[0]['kode_rup']        = (string) ($pkg['kode_rup'] ?? '123456');
        $packages[0]['nama_paket_clean']= $pkg['nama_paket_clean'] ?? RupNlpExtractor::clean($pkg['nama_paket'] ?? '');
        $packages[0]['nama_paket_objek']= $pkg['nama_paket_objek'] ?? RupNlpExtractor::extractObject($pkg['nama_paket'] ?? '');
        $coreData = RupNlpExtractor::extractCoreAndLocation($packages[0]['nama_paket_objek']);
        $packages[0]['nama_paket_core'] = $pkg['nama_paket_core'] ?? $coreData['nama_paket_core'];
        $packages[0]['tokens']          = $pkg['tokens'] ?? $coreData['tokens'];
        $packages[0]['token_count']     = $pkg['token_count'] ?? $coreData['token_count'];
        $packages[0]['lokasi_extracted']= $pkg['lokasi_extracted'] ?? $coreData['lokasi_extracted'];
        $packages[0]['metode_arr']      = $pkg['metode_arr'] ?? (! empty($pkg['metode_pengadaan']) ? [$pkg['metode_pengadaan']] : []);
        $packages[0]['metode_pengadaan']= $pkg['metode_pengadaan'] ?? ($packages[0]['metode_arr'][0] ?? '-');
        $packages[0]['jenis_arr']       = $pkg['jenis_arr'] ?? (! empty($pkg['jenis_pengadaan']) ? [$pkg['jenis_pengadaan']] : []);
        $packages[0]['jenis_pengadaan'] = $pkg['jenis_pengadaan'] ?? ($packages[0]['jenis_arr'][0] ?? '-');
        $packages[0]['budget_cycle']    = $pkg['budget_cycle'] ?? 'MURNI';
        $packages[0]['sumber_dana']     = $pkg['sumber_dana'] ?? 'APBD';
        $packages[0]['source_funding_profile'] = $pkg['source_funding_profile'] ?? 'APBD';
        $packages[0]['tahun_anggaran']  = (int) ($pkg['tahun_anggaran'] ?? 2026);
        $packages[0]['total_nilai']     = (float) ($pkg['total_nilai'] ?? 0);
        $packages[0]['cara_pengadaan']  = $pkg['cara_pengadaan'] ?? 'Penyedia';
        $packages[0]['status_umk']      = $pkg['status_umk'] ?? 'Ya';
        $packages[0]['produk_dalam_negeri'] = $pkg['produk_dalam_negeri'] ?? 'Ya';
        $packages[0]['is_blud']         = $pkg['is_blud'] ?? false;
        $packages[0]['is_dak']          = $pkg['is_dak'] ?? false;
        $packages[0]['serial_sequence_number'] = $pkg['serial_sequence_number'] ?? null;
        $packages[0]['location_identity_key']  = $pkg['location_identity_key'] ?? null;

        $engine = new self();
        $res = $engine->evaluateAll($packages, []);
        return $res['violations'] ?? [];
    }

    /**
     * Jalankan evaluasi Tier 1 (Row-Level), Tier 2 (Cluster-Level), dan Tier 3 (Macro-Level).
     *
     * @param array<int, array> $packages Reference ke seluruh paket
     * @param array<int, array> $clusters Hasil klaster komoditas
     * @return array{
     *   violations: array<int, array>,
     *   opd_summaries: array<string, array>,
     *   macro_institution: array
     * }
     */
    public function evaluateAll(array &$packages, array $clusters): array
    {
        $allViolations = [];

        // Map cluster metadata by cluster_uuid
        $clustersByUuid = [];
        foreach ($clusters as $cls) {
            $clustersByUuid[$cls['cluster_uuid']] = $cls;
        }

        // Agregat pendahuluan per OPD untuk rule kontekstual
        $opdIdenticalPackages  = [];
        $kodeRupCounts         = [];
        $opdKonstruksiPagu     = [];
        $opdHasPengawasan      = [];
        $opdCleanNamesMap      = [];
        $nameAcrossFundingMap  = [];

        foreach ($packages as $idx => $pkg) {
            $opd  = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';
            $kRup = (string) ($pkg['kode_rup'] ?? '');
            if ($kRup !== '') {
                $kodeRupCounts[$kRup] = ($kodeRupCounts[$kRup] ?? 0) + 1;
            }

            // Identitas paket untuk R-21b
            $sig = md5($opd . '|' . ($pkg['nama_paket_clean'] ?? '') . '|' . ($pkg['source_funding_profile'] ?? $pkg['sumber_dana'] ?? '') . '|' . ($pkg['total_nilai'] ?? 0));
            $opdIdenticalPackages[$sig][] = $idx;

            // Tracking nama paket per OPD untuk R-23
            $opdCleanNamesMap[$opd][$pkg['nama_paket_clean']][] = [
                'idx'    => $idx,
                'metode' => $pkg['metode_pengadaan'] ?? '-',
                'jenis'  => $pkg['jenis_pengadaan'] ?? '-',
            ];

            // Tracking nama paket lintas sumber dana per OPD untuk R-39
            $nameAcrossFundingMap[$opd][$pkg['nama_paket_clean']][] = [
                'idx'         => $idx,
                'sumber_dana' => $pkg['source_funding_profile'] ?? $pkg['sumber_dana'] ?? 'APBD',
                'kode_rup'    => $kRup,
            ];

            if (in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true)) {
                $opdKonstruksiPagu[$opd] = ($opdKonstruksiPagu[$opd] ?? 0.0) + (float) $pkg['total_nilai'];
            }
            if (in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true)) {
                $cClean = strtolower((string) ($pkg['nama_paket_clean'] ?? ''));
                if (preg_match('/(pengawasan|perencanaan|manajemen konstruksi|ded)/', $cClean)) {
                    $opdHasPengawasan[$opd] = true;
                }
            }
        }

        // =====================================================================
        // PASS 1: Evaluasi Row-Level & Context-Level per Paket
        // =====================================================================
        foreach ($packages as $idx => &$pkg) {
            $pkgViolations = [];
            $tahunAnggaran = (int) ($pkg['tahun_anggaran'] ?? 2026);
            $totalNilai    = (float) ($pkg['total_nilai'] ?? 0.0);
            $cara          = (string) ($pkg['cara_pengadaan'] ?? 'Penyedia');
            $metode        = (string) ($pkg['metode_pengadaan'] ?? '-');
            $jenis         = (string) ($pkg['jenis_pengadaan'] ?? '-');
            $metodeArr     = $pkg['metode_arr'] ?? (! empty($metode) && $metode !== '-' ? [$metode] : []);
            $jenisArr      = $pkg['jenis_arr'] ?? (! empty($jenis) && $jenis !== '-' ? [$jenis] : []);
            $statusUmk     = (string) ($pkg['status_umk'] ?? '-');
            $pdn           = (string) ($pkg['produk_dalam_negeri'] ?? '-');
            $sumberDana    = (string) ($pkg['sumber_dana'] ?? 'APBD');
            $fundingProfile= (string) ($pkg['source_funding_profile'] ?? $sumberDana);
            $namaPaketRaw  = (string) ($pkg['nama_paket'] ?? '');
            $namaClean     = (string) ($pkg['nama_paket_clean'] ?? RupNlpExtractor::clean($namaPaketRaw));
            $namaObjek     = (string) ($pkg['nama_paket_objek'] ?? RupNlpExtractor::extractObject($namaPaketRaw));
            $isBlud        = ! empty($pkg['is_blud']);
            $kRup          = (string) ($pkg['kode_rup'] ?? '');

            // -----------------------------------------------------------------
            // KELOMPOK A: Integritas Data & Validitas RUP
            // -----------------------------------------------------------------

            // R-01: Pagu Tidak Valid (<= 0 atau NaN) -> ANOMALI_DATA, TINGGI, TINGGI
            if ($totalNilai <= 0.0) {
                $pkgViolations[] = $this->buildFinding(
                    'R-01',
                    'ANOMALI_DATA',
                    'TINGGI',
                    'TINGGI',
                    'Integritas Data',
                    'Pasal 11 PerLKPP 11/2021',
                    'Nilai Pagu RUP Tidak Valid: Nilai pagu tercantum <= Rp0 atau kosong pada CSV.',
                    'Periksa format rupiah dan pastikan nilai RUP diisi sesuai pagu rencana belanja. Jika paket batal dilaksanakan, hapus/perbarui di SIRUP.',
                    'Temuan menguji nilai yang tercantum dalam CSV; bukan verifikasi dokumen DPA/RKA.',
                    [
                        ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                    ],
                    [
                        ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 11', 'norm_summary' => 'Pengumuman RUP memuat informasi pagu pengadaan yang valid'],
                    ],
                    ['total_nilai' => 'Sesuai DPA']
                );
            }

            // R-02: Atribut Penyedia Tidak Lengkap -> ANOMALI_DATA, TINGGI, TINGGI
            if ($cara === 'Penyedia') {
                $hasMetode = ! empty($metode) && $metode !== '-' && $metode !== 'null';
                $hasJenis  = ! empty($jenis) && $jenis !== '-' && $jenis !== 'null';
                if (! $hasMetode || ! $hasJenis) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-02',
                        'ANOMALI_DATA',
                        'TINGGI',
                        'TINGGI',
                        'Kelengkapan Atribut',
                        'Pasal 22 Perpres 16/2018 jo. Perpres 46/2025; PerLKPP 11/2021',
                        'Atribut Paket Penyedia Tidak Lengkap: Paket Penyedia tidak mencantumkan metode pemilihan atau jenis pengadaan yang valid.',
                        'Lengkapi metode pemilihan penyedia dan jenis pengadaan pada SIRUP sebelum pelaksanaan pemilihan.',
                        'Kelengkapan atribut dinilai dari 12 kolom CSV RUP tanpa memeriksa dokumen persiapan pengadaan eksternal.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 22', 'norm_summary' => 'RUP memuat cara, metode, dan jenis pengadaan penyedia'],
                            ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 11', 'norm_summary' => 'Kelengkapan atribut informasi paket dalam SIRUP'],
                        ],
                        [
                            'metode_pengadaan' => 'E-Purchasing / Pengadaan Langsung / Tender / Seleksi',
                            'jenis_pengadaan'  => 'Barang / Pekerjaan Konstruksi / Jasa Konsultansi / Jasa Lainnya',
                        ]
                    );
                }
            }

            // R-03: Pagu Sangat Kecil / Anomali Nilai Rendah -> SINYAL_RISIKO, RENDAH, TINGGI
            if ($totalNilai > 0.0 && $totalNilai < 10000.0) {
                $pkgViolations[] = $this->buildFinding(
                    'R-03',
                    'SINYAL_RISIKO',
                    'RENDAH',
                    'TINGGI',
                    'Kewajaran Nilai',
                    'Prinsip Akurasi Data RUP / Pedoman Input SIRUP',
                    'Pagu Sangat Kecil (< Rp10.000): Nilai paket mengindikasikan kemungkinan salah ketik satuan (mis. ribuan/jutaan tertulis rupiah satuan).',
                    'Verifikasi kembali kesesuaian digit nilai pagu terhadap anggaran riil dan perbaiki jika terdapat kesalahan pengetikan.',
                    'Nilai sangat kecil merupakan indikator kualitas data internal; bukan pelanggaran hukum materiil.',
                    [
                        ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                    ],
                    [
                        ['regulation' => 'Pedoman Input SIRUP LKPP', 'article' => 'Petunjuk Pengisian', 'norm_summary' => 'Akurasi nominal angka pagu anggaran dalam satuan Rupiah penuh'],
                    ]
                );
            }

            // R-34: Penamaan Paket Terlalu Generik / Opaque Naming -> SINYAL_RISIKO, RENDAH, SEDANG
            if (RupNlpExtractor::isOpaquePackageName($pkg)) {
                $pkgViolations[] = $this->buildFinding(
                    'R-34',
                    'SINYAL_RISIKO',
                    'RENDAH',
                    'SEDANG',
                    'Transparansi Penamaan',
                    'Pasal 6 Perpres PBJ & PerLKPP 11/2021',
                    'Penamaan Paket Terlalu Generik (Opaque Naming): Nama paket tidak menggambarkan komoditas/keluaran spesifik yang diadakan.',
                    'Perjelas nama paket agar mencerminkan objek barang/jasa utama tanpa mengubah substansi kegiatan.',
                    'Penilaian penamaan didasarkan pada analisis teks nama paket pada CSV; rincian sub-rincian belanja pada DPA tidak terbaca pada file ini.',
                    [
                        ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                    ],
                    [
                        ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 6', 'norm_summary' => 'Prinsip keterbukaan dan transparansi dalam perencanaan pengadaan'],
                        ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 12', 'norm_summary' => 'Uraian pekerjaan pada RUP harus spesifik dan informatif'],
                    ]
                );
            }

            // R-35: Nilai Enum Tidak Dikenal -> ANOMALI_DATA, TINGGI, TINGGI
            $validCara = ['Penyedia', 'Swakelola'];
            $validUmk  = ['Ya', 'Tidak', '-'];
            $validPdn  = ['Ya', 'Tidak', '-'];
            $isEnumBroken = false;
            $brokenEnums  = [];
            if (! in_array($cara, $validCara, true)) {
                $isEnumBroken = true;
                $brokenEnums[] = "cara_pengadaan='{$cara}'";
            }
            if ($statusUmk !== '' && ! in_array($statusUmk, $validUmk, true)) {
                $isEnumBroken = true;
                $brokenEnums[] = "status_umk='{$statusUmk}'";
            }
            if ($pdn !== '' && ! in_array($pdn, $validPdn, true)) {
                $isEnumBroken = true;
                $brokenEnums[] = "produk_dalam_negeri='{$pdn}'";
            }
            if ($isEnumBroken) {
                $pkgViolations[] = $this->buildFinding(
                    'R-35',
                    'ANOMALI_DATA',
                    'TINGGI',
                    'TINGGI',
                    'Validitas Skema Enum',
                    'Kontrak Standar Skema Data SIRUP LKPP',
                    'Nilai Enum Tidak Dikenal: Terdapat nilai atribut yang berada di luar pilihan standar (' . implode(', ', $brokenEnums) . ').',
                    'Koreksi nilai atribut pada SIRUP agar menggunakan nilai standar yang telah ditentukan.',
                    'Evaluasi berdasarkan validasi nilai teks terhadap skema resmi CSV 12 kolom.',
                    [
                        ['field' => 'enum_values', 'raw_value' => implode('; ', $brokenEnums)],
                    ],
                    [
                        ['regulation' => 'Standar Data SIRUP LKPP', 'article' => 'Data Dictionary', 'norm_summary' => 'Nilai kolom berjenis enumerasi harus mengikuti domain nilai yang sah'],
                    ]
                );
            }

            // R-21a: Kode RUP Bermasalah / Duplikasi -> ANOMALI_DATA, TINGGI, TINGGI
            $isTruncated = ! empty($pkg['is_scientific_truncated']);
            $isDupCode   = ($kRup !== '' && ($kodeRupCounts[$kRup] ?? 0) > 1);
            $isCodeEmpty = ($kRup === '' || $kRup === '-');
            if ($isTruncated || $isDupCode || $isCodeEmpty) {
                $alasanR21a = $isCodeEmpty
                    ? 'Kode RUP Kosong pada baris paket.'
                    : ($isDupCode ? "Kode RUP '{$kRup}' muncul lebih dari satu kali dalam file." : "Kode RUP '{$kRup}' rusak (notasi ilmiah/akhiran .0).");
                $pkgViolations[] = $this->buildFinding(
                    'R-21a',
                    'ANOMALI_DATA',
                    'TINGGI',
                    'TINGGI',
                    'Integritas Kode RUP',
                    'Standar Unik Kode Pengenal SIRUP LKPP (INAPROC)',
                    'Kode RUP Bermasalah / Duplikasi: ' . $alasanR21a,
                    'Pastikan ekspor data memperlakukan kode_rup sebagai teks (string murni) tanpa konversi angka dan hapus duplikasi kode jika ada.',
                    'Penilaian hanya terhadap format dan keunikan kode_rup pada file CSV yang diunggah.',
                    [
                        ['field' => 'kode_rup', 'raw_value' => $kRup],
                    ],
                    [
                        ['regulation' => 'Sistem Informasi Rencana Umum Pengadaan (SIRUP)', 'article' => 'Standar Kodefikasi', 'norm_summary' => 'Setiap paket RUP memiliki Kode RUP unik sebagai identifier tunggal nasional'],
                    ]
                );
            }

            // R-37: Ketentuan Regulasi Transisi TA 2025 Tidak Dapat Ditentukan -> TIDAK_DAPAT_DINILAI_DARI_CSV, INFO, TINGGI
            if ($tahunAnggaran === 2025 && in_array('Pengadaan Langsung', $metodeArr, true) && in_array('Pekerjaan Konstruksi', $jenisArr, true) && $totalNilai > 200000000.0 && $totalNilai <= 400000000.0) {
                $pkgViolations[] = $this->buildFinding(
                    'R-37',
                    'TIDAK_DAPAT_DINILAI_DARI_CSV',
                    'INFO',
                    'TINGGI',
                    'Ketentuan Regulasi Transisi',
                    'Pasal 93A Perpres 46/2025 & Ketentuan Peralihan',
                    'Ketentuan Transisi TA 2025: Paket PL Konstruksi Rp200 Juta - Rp400 Juta pada TA 2025 bergantung pada tanggal proses pemilihan (sebelum atau setelah berlakunya Perpres 46/2025 pada 30 April 2025).',
                    'Verifikasi tanggal pelaksanaan persiapan/pemilihan pada SPSE untuk memastikan apakah tunduk pada batas Perpres 12/2021 (Rp200 Jt) atau Perpres 46/2025 (Rp400 Jt).',
                    'Kolom tanggal proses pemilihan tidak tersedia pada 12 kolom inti CSV RUP; status hukum tidak dapat dipastikan dari file ini.',
                    [
                        ['field' => 'tahun_anggaran', 'raw_value' => (string) $tahunAnggaran],
                        ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                    ],
                    [
                        ['regulation' => 'Perpres 46 Tahun 2025', 'article' => 'Pasal 93A', 'norm_summary' => 'Ketentuan peralihan paket pengadaan tahun 2025 sebelum pengundangan'],
                    ]
                );
            }

            // -----------------------------------------------------------------
            // KELOMPOK B: Ambang Pengadaan Langsung & Kesesuaian Metode
            // -----------------------------------------------------------------
            if ($cara === 'Penyedia') {
                $isPL             = in_array('Pengadaan Langsung', $metodeArr, true);
                $isBarangJasaLain = in_array('Barang', $jenisArr, true) || in_array('Jasa Lainnya', $jenisArr, true);
                $isKonstruksi     = in_array('Pekerjaan Konstruksi', $jenisArr, true);
                $isKonsultansi    = in_array('Jasa Konsultansi', $jenisArr, true);
                $isBarang         = in_array('Barang', $jenisArr, true);
                $isJasaLainnya    = in_array('Jasa Lainnya', $jenisArr, true);

                // R-04: PL Barang/Jasa Lainnya > Rp200 Juta -> POTENSI_TIDAK_SESUAI, TINGGI, TINGGI
                // Operator ketat > (Rp200.000.000 tepat TIDAK memicu; Rp200.000.001 memicu). Tidak mengecualikan BLUD otomatis!
                if ($isPL && $isBarangJasaLain && ! $isKonstruksi && $totalNilai > 200000000.0) {
                    $contextBLUD = $isBlud ? ' [Catatan Konteks: Paket bersumber BLUD - verifikasi apakah berpedoman pada Peraturan Pemimpin BLUD yang sah].' : '';
                    $pkgViolations[] = $this->buildFinding(
                        'R-04',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        'Batas Pagu Metode',
                        'Pasal 38 ayat (3) huruf a Perpres 16/2018 jo. Perpres 46/2025',
                        "Potensi Pelampauan Batas Pengadaan Langsung Barang/Jasa Lainnya: Nilai pagu (Rp " . number_format($totalNilai, 0, ',', '.') . ") melebihi ambang tertinggi Rp200.000.000.{$contextBLUD}",
                        'Reviu nilai dan klasifikasi paket pada RUP. Jika nilai dan jenis benar, sesuaikan metode ke E-Purchasing (jika tersedia di Katalog) atau Tender/Tender Cepat.',
                        'Temuan menguji rencana yang tercantum dalam CSV; bukan bukti proses pemilihan telah dilaksanakan dengan metode tersebut.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'tahun_anggaran', 'raw_value' => (string) $tahunAnggaran],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (3) huruf a', 'norm_summary' => 'Pengadaan Langsung Barang/Jasa Lainnya dilaksanakan untuk nilai pagu paling banyak Rp200.000.000'],
                        ],
                        ['metode_pengadaan' => 'E-Purchasing / Tender']
                    );
                }

                // R-04b: PL Jasa Lainnya > Rp50 Juta & Jasa Tenaga Perorangan -> PERLU_VERIFIKASI, RENDAH, SEDANG
                if ($isPL && $isJasaLainnya && $totalNilai > 50000000.0) {
                    $teksObjek   = strtolower($namaObjek !== '' ? $namaObjek : $namaClean);
                    $teksLengkap = strtolower($namaClean . ' ' . $namaPaketRaw);

                    $isExcluded = false;
                    foreach (RupTaxonomy::EXCLUSION_R04B as $exc) {
                        if (str_contains($teksObjek, $exc) || str_contains($teksLengkap, $exc)) {
                            $isExcluded = true;
                            break;
                        }
                    }

                    if (! $isExcluded) {
                        $isTenagaOperasional = RupTaxonomy::match($teksObjek, RupTaxonomy::DICT_02_OUTSOURCING)
                            || str_contains($teksObjek, 'perorangan')
                            || str_contains($teksObjek, 'tenaga');

                        if ($isTenagaOperasional) {
                            $pkgViolations[] = $this->buildFinding(
                                'R-04b',
                                'PERLU_VERIFIKASI',
                                'RENDAH',
                                'SEDANG',
                                'Bentuk Kontrak & Fitur SPSE',
                                'Pasal 3 ayat (3), (4) huruf b, dan ayat (5) Perlem LKPP 3/2026',
                                'Verifikasi Bentuk Kontrak & Fitur Transaksional SPSE: PL Jasa Lainnya > Rp50 Juta wajib berbentuk SPK via Fitur Transaksional SPSE. Jika menggunakan penyedia Jasa Lainnya Perorangan via Fitur Pencatatan, wajib telah memperoleh Berita Acara Reviu APIP.',
                                'Pastikan menggunakan Fitur Transaksional SPSE (kontrak SPK), atau lampirkan Berita Acara Reviu APIP jika menggunakan skema Pencatatan Jasa Perorangan.',
                                'Data CSV tidak memuat bukti transaksi SPSE atau dokumen reviu APIP; kepatuhan wajib diverifikasi oleh PPK/Inspektorat.',
                                [
                                    ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                                    ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                                    ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                                    ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                                ],
                                [
                                    ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pasal 3 ayat (3)-(5)', 'norm_summary' => 'Kewajiban Fitur Transaksional SPSE dan Reviu APIP untuk PL Jasa Lainnya Perorangan > Rp50 Juta'],
                                ]
                            );
                        }
                    }
                }

                // R-05: PL Pekerjaan Konstruksi > Batas Ambang -> POTENSI_TIDAK_SESUAI, TINGGI, TINGGI
                // TA 2026 / Perpres 46/2025: ambang = Rp400.000.000. TA <= 2024: ambang = Rp200.000.000.
                // Strict > operator! (Rp400.000.000 tepat TIDAK memicu; Rp400.000.001 memicu).
                $batasPlKonstruksi = ($tahunAnggaran > 0 && $tahunAnggaran <= 2024) ? 200000000.0 : 400000000.0;
                if ($isPL && $isKonstruksi && ! $isKonsultansi && $totalNilai > $batasPlKonstruksi) {
                    $contextBLUD = $isBlud ? ' [Catatan Konteks: Paket bersumber BLUD - verifikasi apakah berpedoman pada Peraturan Pemimpin BLUD yang sah].' : '';
                    $pkgViolations[] = $this->buildFinding(
                        'R-05',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        'Batas Pagu Metode',
                        'Pasal 38 ayat (3) huruf b Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        "Potensi Pelampauan Batas Pengadaan Langsung Pekerjaan Konstruksi: Nilai pagu (Rp " . number_format($totalNilai, 0, ',', '.') . ") melebihi ambang tertinggi Rp" . number_format($batasPlKonstruksi, 0, ',', '.') . ".{$contextBLUD}",
                        'Pastikan objek benar pekerjaan konstruksi fisik. Jika benar, sesuaikan metode ke Tender atau E-Purchasing Katalog Konstruksi.',
                        'Temuan menguji rencana pada CSV RUP; bukan bukti pemilihan telah dilaksanakan.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'tahun_anggaran', 'raw_value' => (string) $tahunAnggaran],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (3) huruf b', 'norm_summary' => 'Pengadaan Langsung Pekerjaan Konstruksi dilaksanakan untuk nilai pagu paling banyak Rp400.000.000'],
                        ],
                        ['metode_pengadaan' => 'Tender / E-Purchasing Katalog Konstruksi']
                    );
                }

                // R-06: PL Jasa Konsultansi > Rp100 Juta -> POTENSI_TIDAK_SESUAI, TINGGI, TINGGI
                // Strict > operator! (Rp100.000.000 tepat TIDAK memicu; Rp100.000.001 memicu).
                if ($isPL && $isKonsultansi && $totalNilai > 100000000.0) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-06',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        'Batas Pagu Metode',
                        'Pasal 41 ayat (3) Perpres 16/2018 jo. Perpres 46/2025',
                        "Potensi Pelampauan Batas Pengadaan Langsung Jasa Konsultansi: Nilai pagu (Rp " . number_format($totalNilai, 0, ',', '.') . ") melebihi batas tertinggi Rp100.000.000.",
                        'Reviu apakah objek memang Jasa Konsultansi. Jika benar, sesuaikan metode pemilihan ke Seleksi atau E-Purchasing.',
                        'Temuan menguji rencana pada CSV RUP; bukan pembuktian proses pemilihan riil.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 41 ayat (3)', 'norm_summary' => 'Pengadaan Langsung Jasa Konsultansi dilaksanakan untuk nilai pagu paling banyak Rp100.000.000'],
                        ],
                        ['metode_pengadaan' => 'Seleksi / E-Purchasing']
                    );
                }

                // R-07: DIHAPUS / RETIRED dari ruleset untuk syarat teknologi sederhana umum PL Konstruksi Rp200-400 Jt.
                // Jika mengandung DICT_KONSTRUKSI_STRUKTUR_BERAT, catat sebagai PERLU_VERIFIKASI
                if ($isPL && $isKonstruksi && $totalNilai > 200000000.0 && $totalNilai <= 400000000.0) {
                    if (RupTaxonomy::match($namaObjek !== '' ? $namaObjek : $namaClean, RupTaxonomy::DICT_KONSTRUKSI_STRUKTUR_BERAT)) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-07',
                            'PERLU_VERIFIKASI',
                            'RENDAH',
                            'SEDANG',
                            'Kesesuaian Risiko Konstruksi',
                            'Pedoman Penilaian Risiko Teknis Pekerjaan Konstruksi',
                            'Indikasi Struktur Berat pada PL Konstruksi: Nama paket memuat kata kunci struktur berat (misal: jembatan, bore pile, bendungan, gedung bertingkat) yang umumnya memerlukan teknologi dan risiko tidak kecil.',
                            'Verifikasi tingkat risiko dan teknologi pekerjaan bersama tenaga teknis/PPK; jika berisiko menengah/tinggi, pertimbangkan Tender.',
                            'Data CSV tidak memuat dokumen perencanaan teknis/DED; penilaian risiko riil harus dilakukan oleh ahli konstruksi.',
                            [
                                ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                                ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ],
                            [
                                ['regulation' => 'Perpres 46/2025 jo. Perlem LKPP 3/2026', 'article' => 'Pedoman Teknis', 'norm_summary' => 'Kesesuaian metode terhadap risiko dan teknologi pekerjaan konstruksi'],
                            ]
                        );
                    }
                }

                // R-08: Penunjukan Langsung Tanpa Alasan yang Tersedia di CSV -> PERLU_VERIFIKASI, SEDANG, TINGGI
                if (in_array('Penunjukan Langsung', $metodeArr, true)) {
                    $isSahKondisi = RupTaxonomy::match($namaClean, RupTaxonomy::DICT_11_PENUNJUKAN_LANGSUNG_SAH);
                    $isSahDikecualikan = RupTaxonomy::match($namaClean, RupTaxonomy::DICT_01_DIKECUALIKAN);

                    $isKonsultansiPL = in_array('Jasa Konsultansi', $jenisArr, true);
                    $batasPpPL       = $isKonsultansiPL ? 100000000.0 : 200000000.0;
                    $isKewenanganPokjaPL = ($totalNilai > $batasPpPL);

                    $kewenanganTeksPL = $isKewenanganPokjaPL
                        ? 'Wajib dilaksanakan oleh Pokja Pemilihan UKPBJ (Pagu > ' . ($isKonsultansiPL ? 'Rp100 Juta' : 'Rp200 Juta') . ' sesuai Lampiran I & II Angka 1.4 Perlem LKPP 3/2026).'
                        : 'Dapat dilaksanakan oleh Pejabat Pengadaan (Pagu <= ' . ($isKonsultansiPL ? 'Rp100 Juta' : 'Rp200 Juta') . ' sesuai Lampiran I & II Angka 1.4 Perlem LKPP 3/2026).';

                    if (! $isSahKondisi && ! $isSahDikecualikan) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-08',
                            'PERLU_VERIFIKASI',
                            'SEDANG',
                            'TINGGI',
                            'Justifikasi Penunjukan Langsung',
                            'Pasal 38 ayat (4)-(5) & Pasal 41 ayat (4)-(5) Perpres 46/2025 jo. Perlem LKPP 3/2026',
                            "Kebutuhan Verifikasi Justifikasi Penunjukan Langsung: Alasan/justifikasi keadaan tertentu tidak tersedia pada data RUP. {$kewenanganTeksPL}",
                            "Pastikan Berita Acara Justifikasi Keadaan Tertentu telah disusun oleh PPK sebelum pemilihan. {$kewenanganTeksPL}",
                            'Data CSV RUP tidak memiliki kolom alasan keadaan tertentu; sistem tidak menyimpulkan pelanggaran hanya dari metode.',
                            [
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                                ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ],
                            [
                                ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (4)-(5), Pasal 41 ayat (4)-(5)', 'norm_summary' => 'Kriteria dan keadaan tertentu untuk penggunaan metode Penunjukan Langsung'],
                                ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Lampiran I & II Angka 1.4', 'norm_summary' => 'Pembagian kewenangan Pejabat Pengadaan vs Pokja Pemilihan pada Penunjukan Langsung'],
                            ],
                            ['pelaksana_pemilihan' => $isKewenanganPokjaPL ? 'Pokja Pemilihan UKPBJ' : 'Pejabat Pengadaan']
                        );
                    }

                    // R-08b: Repeat Order -> TIDAK_DAPAT_DINILAI_DARI_CSV, INFO, SEDANG
                    if (str_contains(strtolower($namaClean), 'repeat order') || str_contains(strtolower($namaPaketRaw), 'repeat order')) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-08b',
                            'TIDAK_DAPAT_DINILAI_DARI_CSV',
                            'INFO',
                            'SEDANG',
                            'Verifikasi Repeat Order',
                            'Pasal 38 ayat (5) Perpres 46/2025 jo. Lampiran I Angka 5.2.1 Perlem LKPP 3/2026',
                            'Pemeriksaan 5 Syarat Kumulatif Repeat Order: (1) Paket awal hasil Tender/Seleksi; (2) HPS RO <= nilai kontrak paket awal; (3) Maksimal 2x dalam 3 tahun; (4) Kinerja penyedia Sangat Baik; (5) Team Leader tetap sama.',
                            'Lakukan verifikasi berkas kontrak awal dan kinerja SIKaP penyedia untuk memenuhi 5 syarat kumulatif Repeat Order.',
                            'CSV RUP tidak memuat riwayat kontrak awal, nilai HPS, riwayat kinerja, atau personel konsultan.',
                            [
                                ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ],
                            [
                                ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Lampiran I Angka 5.2.1', 'norm_summary' => 'Lima syarat kumulatif Repeat Order untuk Jasa Konsultansi'],
                            ]
                        );
                    }
                }

                // R-09: Ketidakcocokan Metode vs Jenis -> POTENSI_TIDAK_SESUAI / PERLU_VERIFIKASI, TINGGI, TINGGI
                $isSeleksi   = in_array('Seleksi', $metodeArr, true);
                $isTender    = in_array('Tender', $metodeArr, true) || in_array('Tender Cepat', $metodeArr, true);
                $isKontes    = in_array('Kontes', $metodeArr, true);
                $onlyKonsult = $isKonsultansi && count($jenisArr) === 1;

                if ($isSeleksi && ! $isKonsultansi) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-09',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        'Kompatibilitas Metode',
                        'Pasal 38 & Pasal 41 Perpres 16/2018 jo. Perpres 46/2025',
                        'Inkompatibilitas Metode Pemilihan: Metode Seleksi hanya diperbolehkan untuk Jasa Konsultansi, namun paket tercatat sebagai non-Konsultansi.',
                        'Koreksi metode ke Tender/E-Purchasing, atau koreksi jenis ke Jasa Konsultansi jika objek memang konsultansi.',
                        'Penilaian berdasarkan kecocokan atribut normatif pada kolom metode dan jenis dalam CSV.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 41 ayat (1)', 'norm_summary' => 'Metode Seleksi digunakan khusus untuk pemilihan Penyedia Jasa Konsultansi'],
                        ]
                    );
                } elseif ($isTender && $onlyKonsult) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-09',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        'Kompatibilitas Metode',
                        'Pasal 38 & Pasal 41 Perpres 16/2018 jo. Perpres 46/2025',
                        'Inkompatibilitas Metode Pemilihan: Metode Tender/Tender Cepat tidak diperuntukkan bagi paket murni Jasa Konsultansi (seharusnya Seleksi/Pengadaan Langsung Konsultansi).',
                        'Ubah metode menjadi Seleksi (jika > Rp100 Juta) atau Pengadaan Langsung (jika <= Rp100 Juta) atau E-Purchasing.',
                        'Penilaian berdasarkan perbandingan nilai kolom metode dan jenis.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 41', 'norm_summary' => 'Metode pemilihan penyedia Jasa Konsultansi terdiri atas Seleksi, Pengadaan Langsung, dan Penunjukan Langsung'],
                        ]
                    );
                } elseif ($isKontes && RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_04_KATALOG_STANDAR)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-09',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Kesesuaian Metode Kontes',
                        'Pasal 38 ayat (8) Perpres PBJ',
                        'Metode Kontes untuk Komoditas Standar: Kontes diperuntukkan bagi pengadaan barang/jasa lainnya yang tidak mempunyai tarif resmi dan tidak dapat ditetapkan rincian teknisnya secara tepat.',
                        'Verifikasi kesesuaian metode; gunakan E-Purchasing atau Tender untuk barang standar.',
                        'Penilaian berdasarkan pencocokan teks nama paket dengan kamus komoditas standar.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (8)', 'norm_summary' => 'Kriteria penggunaan metode Kontes atau Sayembara'],
                        ]
                    );
                }

                // R-27a & R-27b: Statutory Threshold Hugging 95% - 100% Batas PL -> SINYAL_RISIKO, RENDAH, TINGGI
                // R-27a: EXACT_THRESHOLD (182 paket) | R-27b: NEAR_THRESHOLD (27 paket) -> Total 209 paket
                $batasPl = RupGraphClusterer::getBatasPl($jenis, $tahunAnggaran);
                if ($isPL && $totalNilai >= (0.95 * $batasPl) && $totalNilai <= $batasPl) {
                    $isExact = (abs($totalNilai - $batasPl) < 0.01);
                    if ($isExact) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-27a',
                            'SINYAL_RISIKO',
                            'RENDAH',
                            'TINGGI',
                            'Sinyal Pagu Ambang',
                            'Pasal 20 ayat (2) Perpres PBJ & Pedoman Reviu Perencanaan',
                            "Sinyal Nilai Pagu Tepat Batas Pengadaan Langsung (EXACT_THRESHOLD): Pagu paket berada tepat pada ambang tertinggi (Rp " . number_format($batasPl, 0, ',', '.') . ").",
                            'Lakukan reviu kertas kerja HPS/RAB untuk memastikan kewajaran estimasi dan tidak terjadi pemecahan kebutuhan yang disengaja.',
                            'Mendekati ambang batas bukan pelanggaran hukum; nilai tepat pada batas masih termasuk cakupan Pengadaan Langsung.',
                            [
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                                ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                                ['field' => 'ambang_batas', 'raw_value' => (string) $batasPl],
                            ],
                            [
                                ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 20 ayat (2)', 'norm_summary' => 'Larangan memecah pengadaan untuk menghindari tender'],
                            ]
                        );
                    } else {
                        $pkgViolations[] = $this->buildFinding(
                            'R-27b',
                            'SINYAL_RISIKO',
                            'RENDAH',
                            'TINGGI',
                            'Sinyal Pagu Ambang',
                            'Pasal 20 ayat (2) Perpres PBJ & Pedoman Reviu Perencanaan',
                            "Sinyal Nilai Pagu Mendekati Batas Pengadaan Langsung (NEAR_THRESHOLD): Pagu paket berada pada rentang 95% s.d. <100% dari ambang tertinggi (Rp " . number_format($batasPl, 0, ',', '.') . ").",
                            'Lakukan reviu kertas kerja HPS/RAB untuk memastikan kewajaran estimasi dan tidak terjadi pemecahan kebutuhan yang disengaja.',
                            'Mendekati ambang batas bukan pelanggaran hukum; nilai mendekati batas masih termasuk cakupan Pengadaan Langsung.',
                            [
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                                ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                                ['field' => 'ambang_batas', 'raw_value' => (string) $batasPl],
                            ],
                            [
                                ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 20 ayat (2)', 'norm_summary' => 'Larangan memecah pengadaan untuk menghindari tender'],
                            ]
                        );
                    }
                }

                // R-27c: Sub-Threshold Hugging Konstruksi TA >= 2025 (Rp190 Juta s.d. Rp200 Juta) -> SINYAL_RISIKO, RENDAH, TINGGI
                if ($tahunAnggaran >= 2025 && $isPL && $isKonstruksi && $totalNilai >= 190000000.0 && $totalNilai <= 200000000.0) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-27c',
                        'SINYAL_RISIKO',
                        'RENDAH',
                        'TINGGI',
                        'Sinyal Pagu Ambang Konstruksi',
                        'Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025',
                        "Pagu Konstruksi Mendekati Ambang Lama (Rp190 Jt - Rp200 Jt): Paket berada pada batas lama PL konstruksi ketentuan yang berlaku pada Perpres 12/2021 (Rp200 Juta).",
                        'Periksa apakah paket dipatok pada batas Rp200 Juta akibat kebiasaan anggaran lama; pada TA 2026 batas PL Konstruksi adalah Rp400 Juta.',
                        'Indikator pola penetapan pagu historis; bukan pelanggaran hukum.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                        ],
                        [
                            ['regulation' => 'Perpres 46 Tahun 2025', 'article' => 'Pasal 38 ayat (3) huruf b', 'norm_summary' => 'Ambang batas baru PL Konstruksi paling banyak Rp400 Juta'],
                        ]
                    );
                }

                // R-28: Tender/Seleksi pada Pagu Kecil -> INFORMATIF, INFO, TINGGI
                if (($isTender && ! $isKonsultansi && $totalNilai <= 200000000.0) ||
                    ($isSeleksi && $isKonsultansi && $totalNilai <= 100000000.0)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-28',
                        'INFORMATIF',
                        'INFO',
                        'TINGGI',
                        'Sinyal Efisiensi Metode',
                        'Prinsip Efisiensi Pengadaan (Pasal 6 Perpres PBJ)',
                        'Metode Kompetitif pada Pagu di Bawah Ambang PL: Paket bernilai di bawah ambang Pengadaan Langsung direncanakan via Tender/Seleksi.',
                        'Dapat dipertahankan jika ditujukan untuk memperluas kompetisi atau standarisasi teknis; tidak wajib diubah menjadi Pengadaan Langsung.',
                        'Penggunaan metode kompetitif pada pagu kecil tidak dilarang regulasi; ini disajikan sebagai indikator efisiensi.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 6', 'norm_summary' => 'Prinsip efisiensi dan efektivitas dalam pemilihan metode pengadaan'],
                        ]
                    );
                }

                // R-29: Validasi Tender Cepat -> PERLU_VERIFIKASI (Konstruksi) / TIDAK_DAPAT_DINILAI_DARI_CSV (Non-Konstruksi)
                if (in_array('Tender Cepat', $metodeArr, true)) {
                    if ($isKonstruksi) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-29',
                            'PERLU_VERIFIKASI',
                            'TINGGI',
                            'TINGGI',
                            'Kesesuaian Metode Tender Cepat',
                            'Pasal 38 ayat (6) Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                            'Tender Cepat pada Pekerjaan Konstruksi Perlu Penelaahan: Pekerjaan Konstruksi umumnya menuntut evaluasi teknis dan metode pelaksanaan yang tidak dapat distandarisasi secara penuh dalam SIKaP.',
                            'Reviu kelayakan metode; jika pekerjaan memerlukan penilaian proposal teknis, gunakan metode Tender konvensional sesuai Pasal 38 ayat (1).',
                            'Tender Cepat hanya dapat digunakan jika spesifikasi teknis dan volume pekerjaan telah dapat distandarisasi.',
                            [
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                                ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ],
                            [
                                ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (6)', 'norm_summary' => 'Syarat penggunaan metode Tender Cepat untuk barang/pekerjaan yang terstandarisasi'],
                                ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pedoman Tender Cepat', 'norm_summary' => 'Batasan dan tata cara evaluasi metode Tender Cepat'],
                            ],
                            ['metode_pengadaan' => 'Tender']
                        );
                    } else {
                        $pkgViolations[] = $this->buildFinding(
                            'R-29',
                            'TIDAK_DAPAT_DINILAI_DARI_CSV',
                            'INFO',
                            'SEDANG',
                            'Verifikasi Tender Cepat',
                            'Pasal 38 ayat (6) Perpres 46/2025 jo. Perlem LKPP 3/2026',
                            'Kebutuhan Verifikasi Kelayakan Tender Cepat: Tender Cepat mensyaratkan spesifikasi teknis dan kualifikasi penyedia telah terstandarisasi dalam SIKaP.',
                            'Pastikan spesifikasi teknis telah distandarisasi dan pelaku usaha terdaftar serta terkualifikasi dalam SIKaP sebelum proses dimulai.',
                            'CSV RUP tidak memuat rincian spesifikasi atau status kualifikasi penyedia di SIKaP.',
                            [
                                ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ],
                            [
                                ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (6)', 'norm_summary' => 'Syarat penggunaan metode Tender Cepat'],
                            ]
                        );
                    }
                }

                // R-33: Jenis Terintegrasi atau PL untuk Terintegrasi -> PERLU_VERIFIKASI, SEDANG, SEDANG
                $isJenisTerintegrasi = (strtolower(trim($jenis)) === 'terintegrasi');
                $isIntegratedPackage = ! empty($pkg['is_integrated_package']);
                if ($isPL && ($isIntegratedPackage || $isJenisTerintegrasi)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-33',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Pekerjaan Terintegrasi',
                        'Pasal 54 Perpres 16/2018 jo. Perpres 46/2025',
                        'Pengadaan Terintegrasi Tidak Tepat Menggunakan Pengadaan Langsung: Pekerjaan terintegrasi (Design & Build) umumnya berisiko tinggi dan wajib melalui Tender/Seleksi.',
                        'Reviu ruang lingkup pekerjaan; jika objek hanya pengadaan barang/jasa biasa, ubah jenis menjadi Barang atau Jasa Lainnya.',
                        'Data CSV hanya menyajikan label jenis; ruang lingkup fisik sebenarnya harus diperiksa pada dokumen perencanaan.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 54', 'norm_summary' => 'Ketentuan pelaksanaan pengadaan pekerjaan terintegrasi'],
                        ],
                        ['jenis_pengadaan' => 'Barang / Jasa Lainnya']
                    );
                } elseif ($isJenisTerintegrasi) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-33',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Penyelarasan Jenis Terintegrasi',
                        'Pasal 54 Perpres 16/2018 jo. Perpres 46/2025',
                        'Indikasi Kesalahan Pemilihan Jenis Terintegrasi: Paket memilih jenis Terintegrasi namun nama objek mengarah pada komoditas sederhana.',
                        'Periksa apakah lingkup pekerjaan benar terintegrasi (rancang bangun); jika belanja rutin biasa, sesuaikan jenisnya.',
                        'Penilaian berdasarkan perbandingan label jenis Terintegrasi dengan frasa nama paket.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 54', 'norm_summary' => 'Definisi dan cakupan pekerjaan terintegrasi'],
                        ]
                    );
                }

                // R-32: Paket Konstruksi > Rp400 Juta Tanpa Konsultan Pengawasan di RUP -> INFORMATIF / TIDAK_DAPAT_DINILAI, INFO, RENDAH
                if ($isKonstruksi && $totalNilai > 400000000.0) {
                    $opdKey = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';
                    if (($opdKonstruksiPagu[$opdKey] ?? 0.0) > 400000000.0 && empty($opdHasPengawasan[$opdKey])) {
                        $pkgViolations[] = $this->buildFinding(
                            'R-32',
                            'INFORMATIF',
                            'INFO',
                            'RENDAH',
                            'Sinyal Pengawasan Konstruksi',
                            'Pasal 7 Perlem LKPP 3/2026 & PP 16/2021',
                            'Paket Konstruksi > Rp400 Juta Tanpa Terlihat Paket Konsultan Pengawasan pada RUP OPD.',
                            'Pastikan paket jasa pengawasan telah diinput atau tetapkan Tim Pengawas Internal yang sah.',
                            'Pengawasan fisik mungkin dilaksanakan swakelola atau terdaftar dengan judul berbeda; CSV tidak membuktikan ada/tidaknya tim pengawas.',
                            [
                                ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                                ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ],
                            [
                                ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pasal 7', 'norm_summary' => 'Kewajiban pengawasan pelaksanaan pekerjaan konstruksi'],
                            ]
                        );
                    }
                }
            }

            // -----------------------------------------------------------------
            // KELOMPOK C: Cara Pengadaan & Sumber Dana
            // -----------------------------------------------------------------

            // R-10: Kandidat Pengadaan Dikecualikan -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('Dikecualikan', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_01_DIKECUALIKAN)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-10',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Pengadaan Dikecualikan',
                        'Pasal 62 Perpres PBJ & PerLKPP 5/2021',
                        'Kandidat Pengadaan Dikecualikan: Objek paket memuat indikasi belanja tarif resmi/utilitas (misal: listrik, air, telepon, pos, internet berlangganan) yang tidak diinput dengan metode Dikecualikan.',
                        'Periksa apakah transaksi mengikuti tarif resmi penyedia tunggal monopoli/BUMN; jika ya, pertimbangkan metode Dikecualikan.',
                        'Kata listrik/internet tidak otomatis membuktikan pengadaan tarif resmi tanpa memeriksa kontrak berlangganan.',
                        [
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 62', 'norm_summary' => 'Pengadaan barang/jasa yang dikecualikan dari ketentuan pemilihan reguler'],
                            ['regulation' => 'PerLKPP Nomor 5 Tahun 2021', 'article' => 'Pasal 3', 'norm_summary' => 'Pedoman pengadaan yang dilaksanakan dengan tarif resmi yang dipublikasikan'],
                        ]
                    );
                }
            }

            // R-11: Jasa Tenaga Alih Daya dalam Swakelola -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Swakelola') {
                $teksSwakelola = (string) ($namaObjek !== '' ? $namaObjek : $namaClean);
                if (RupTaxonomy::match($teksSwakelola, RupTaxonomy::DICT_02_OUTSOURCING, RupTaxonomy::NEG_LOOKAHEAD_02)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-11',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Kesesuaian Cara Swakelola',
                        'PerLKPP 3/2021 & UU 20/2023 Pasal 65-66',
                        'Istilah Jasa Tenaga Pendukung pada Swakelola: Nama paket memuat istilah tenaga kebersihan, keamanan, sopir, atau PTHL/TKS pada cara Swakelola.',
                        'Klarifikasi apakah pekerjaan dilaksanakan sendiri oleh tim swakelola atau merupakan penyediaan tenaga melalui badan usaha penyedia (Penyedia Jasa Lainnya).',
                        'Munculnya kata tenaga/sopir pada nama paket belum membuktikan pelanggaran kepegawaian UU ASN; evaluasi hubungan kerja memerlukan dokumen kontrak/SK.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'PerLKPP Nomor 3 Tahun 2021', 'article' => 'Pedoman Swakelola', 'norm_summary' => 'Ruang lingkup pekerjaan yang dapat dikerjakan secara swakelola'],
                            ['regulation' => 'UU Nomor 20 Tahun 2023', 'article' => 'Pasal 65-66', 'norm_summary' => 'Penataan tenaga non-ASN pada instansi pemerintah'],
                        ]
                    );
                }
            }

            // R-12: Swakelola Berindikasi Pekerjaan Pihak Ketiga -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Swakelola') {
                $isKonsultansi = RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_06_MURNI_KONSULTANSI, RupTaxonomy::NEG_LOOKAHEAD_06);
                $isKonstruksi  = RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_05_MURNI_KONSTRUKSI, RupTaxonomy::NEG_LOOKAHEAD_05);
                $hasPihak3     = str_contains($namaObjek, 'pihak ketiga') || str_contains($namaObjek, 'kalibrasi') || str_contains($namaObjek, 'belanja modal');

                if ($isKonsultansi || $isKonstruksi || $hasPihak3) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-12',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Kesesuaian Swakelola',
                        'Perpres 16/2018 jo. Perpres 46/2025 & PerLKPP 3/2021',
                        'Objek Pekerjaan Berindikasi Pihak Ketiga pada Swakelola: Paket swakelola memuat indikasi pekerjaan fisik/konstruksi, studi konsultansi, atau belanja modal.',
                        'Verifikasi apakah kegiatan dikerjakan instansi lain/ormas/pokmas (Swakelola Tipe II/III/IV) atau sebenarnya harus diadakan melalui vendor penyedia.',
                        'Kolom tipe swakelola tidak tersedia pada CSV 12 kolom; klarifikasi tipe harus dilakukan ke OPD terkait.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'PerLKPP Nomor 3 Tahun 2021', 'article' => 'Pasal 5', 'norm_summary' => 'Kriteria dan pembagian tipe swakelola'],
                        ]
                    );
                }
            }

            // R-13: Objek Administrasi / Internal pada Cara Penyedia -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('Dikecualikan', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_03_SWAKELOLA_MURNI, RupTaxonomy::NEG_LOOKAHEAD_03)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-13',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Kesesuaian Cara Belanja',
                        'PerLKPP 11/2021 tentang Perencanaan PBJ',
                        'Objek Administrasi Internal pada Penyedia: Nama paket memuat indikasi belanja perjalanan dinas, honorarium internal, atau uang lembur.',
                        'Periksa apakah paket merupakan jasa pihak ketiga (misal travel agent/EO) atau biaya langsung personel internal yang semestinya dicatat di Swakelola.',
                        'Penilaian berdasarkan analisis nama paket; tidak dapat dipastikan tanpa melihat DPA dan mekanisme pencairan belanja.',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 10', 'norm_summary' => 'Pemisahan cara pengadaan melalui Penyedia dan Swakelola'],
                        ]
                    );
                }
            }

            // R-14: Validasi Hibah / Bantuan Sosial -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if (RupTaxonomy::match($namaClean, RupTaxonomy::DICT_09_HIBAH_BANSOS)) {
                $isBarangH = str_contains($namaClean, 'barang');
                $isUangH   = str_contains($namaClean, 'uang');
                if (($isBarangH && $cara === 'Swakelola') || ($isUangH && $cara === 'Penyedia')) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-14',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Tata Kelola Hibah/Bansos',
                        'Permendagri 77/2020 & Perpres PBJ',
                        'Ketidaksesuaian Cara Belanja Hibah/Bansos: Hibah barang tercatat Swakelola atau hibah uang tercatat Penyedia.',
                        'Pastikan hibah barang yang dibeli oleh OPD diadakan via Penyedia (E-Purchasing/Tender); transfer uang hibah (NPHD) dicatat via mekanisme Swakelola/Non-Penyedia.',
                        'Perbedaan antara hibah uang dan hibah barang memerlukan verifikasi naskah perjanjian hibah daerah (NPHD).',
                        [
                            ['field' => 'cara_pengadaan', 'raw_value' => $cara],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Permendagri Nomor 77 Tahun 2020', 'article' => 'Pedoman Teknis Pengelolaan Keuangan Daerah', 'norm_summary' => 'Mekanisme belanja hibah barang dan belanja hibah uang'],
                        ]
                    );
                }
            }

            // R-30: Label Sumber Dana BLUD/DAK/BOSP -> INFORMATIF, INFO, TINGGI
            if ($isBlud || ! empty($pkg['is_dak']) || str_contains($sumberDana, 'BOSP') || str_contains($sumberDana, 'DAK')) {
                $tagSource = $isBlud ? 'BLUD' : (! empty($pkg['is_dak']) ? 'DAK' : 'BOSP/Khusus');
                $pkgViolations[] = $this->buildFinding(
                    'R-30',
                    'INFORMATIF',
                    'INFO',
                    'TINGGI',
                    'Konteks Sumber Dana Khusus',
                    'Perpres 16/2018 jo. Perpres 46/2025 & PerLKPP 5/2021',
                    "Konteks Sumber Dana Khusus ({$tagSource}): Paket didanai dari sumber dana khusus. Ketentuan ambang batas tetap berlaku kecuali diatur tersendiri dalam regulasi yang sah.",
                    'Pastikan penggunaan fleksibilitas BLUD didasarkan pada Peraturan Kepala Daerah / Peraturan Pemimpin BLUD yang telah diundangkan.',
                    'Data CSV hanya memuat label sumber dana; sistem tidak otomatis mengecualikan paket dari pengawasan.',
                    [
                        ['field' => 'sumber_dana', 'raw_value' => $sumberDana],
                    ],
                    [
                        ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 61', 'norm_summary' => 'Ketentuan pengadaan pada Badan Layanan Umum / BLUD'],
                    ]
                );
            }

            // R-36: Profil Sumber Dana Multi-Tag -> INFORMATIF, INFO, TINGGI
            if (! empty($pkg['is_multi_source']) || str_contains($fundingProfile, '+')) {
                $pkgViolations[] = $this->buildFinding(
                    'R-36',
                    'INFORMATIF',
                    'INFO',
                    'TINGGI',
                    'Profil Sumber Dana Multi-Tag',
                    'Standar Pencatatan SIRUP LKPP',
                    "Paket dengan Sumber Dana Campuran ({$fundingProfile}): Paket dibiayai dari kombinasi beberapa sumber pendanaan.",
                    'Pertahankan sebagai satu paket pendanaan campuran; jangan memecah baris data atau menggandakan nilai pagu.',
                    'Label multi-tag adalah fakta administratif pada CSV; bukan bukti perubahan pagu APBD Perubahan.',
                    [
                        ['field' => 'sumber_dana', 'raw_value' => $sumberDana],
                        ['field' => 'source_funding_profile', 'raw_value' => $fundingProfile],
                    ],
                    [
                        ['regulation' => 'Pedoman Input SIRUP LKPP', 'article' => 'Sumber Dana', 'norm_summary' => 'Pencatatan sumber dana majemuk pada satu paket pengadaan'],
                    ]
                );
            }

            // -----------------------------------------------------------------
            // KELOMPOK D: Katalog, Klasifikasi Jenis, Afirmasi UMK & PDN
            // -----------------------------------------------------------------

            // R-15: Kandidat Komoditas Katalog -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('E-Purchasing', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_04_KATALOG_STANDAR)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-15',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Peluang E-Purchasing',
                        'Pasal 38 ayat (2) & Pasal 50 Perpres 46/2025 jo. PerLKPP 2/2026',
                        'Kandidat Komoditas Katalog: Objek paket merupakan komoditas yang umum tersedia dalam Katalog Elektronik.',
                        'Periksa ketersediaan barang/jasa pada Katalog Elektronik LKPP; jika tersedia dan tidak terdapat alasan pengecualian, prioritaskan E-Purchasing.',
                        'CSV RUP tidak memuat ID katalog atau status ketersediaan live; temuan ini merupakan sinyal verifikasi, bukan pelanggaran terbukti.',
                        [
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 38 ayat (2)', 'norm_summary' => 'E-Purchasing wajib dilaksanakan untuk barang/jasa yang tercantum dalam Katalog Elektronik'],
                            ['regulation' => 'Perlem LKPP Nomor 2 Tahun 2026', 'article' => 'Pedoman Katalog Elektronik', 'norm_summary' => 'Tata cara pelaksanaan pembelian melalui Katalog Elektronik'],
                        ]
                    );
                }
            }

            // R-16: Kandidat Salah Klasifikasi Jasa Konsultansi -> PERLU_VERIFIKASI / POTENSI_TIDAK_SESUAI
            if ($cara === 'Penyedia' && ! in_array('Jasa Konsultansi', $jenisArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_06_MURNI_KONSULTANSI, RupTaxonomy::NEG_LOOKAHEAD_06)) {
                    $isPlEvasion = $isPL && $totalNilai > 100000000.0;
                    $statusFinding      = $isPlEvasion ? 'POTENSI_TIDAK_SESUAI' : 'PERLU_VERIFIKASI';
                    $severityFinding    = $isPlEvasion ? 'TINGGI' : 'SEDANG';
                    $confidenceFinding  = $isPlEvasion ? 'TINGGI' : 'SEDANG';
                    $deskripsiFinding   = $isPlEvasion
                        ? 'Objek paket menunjukkan Jasa Konsultansi/DED/Tenaga Ahli dengan pagu di atas batas Pengadaan Langsung Konsultansi (Rp100.000.000), namun diklasifikasikan sebagai non-Konsultansi sehingga berpotensi menghindari kewajiban metode Seleksi (Pasal 41 ayat (3) Perpres 16/2018 jo. Perpres 46/2025).'
                        : 'Kandidat Salah Klasifikasi Jasa Konsultansi: Objek paket memuat frasa perencanaan teknis/pengawasan teknis/kajian/DED/tenaga ahli namun tercatat non-Konsultansi.';
                    $rekomendasiFinding = $isPlEvasion
                        ? 'Segera batalkan metode Pengadaan Langsung dan ubah jenis pengadaan menjadi Jasa Konsultansi serta gunakan metode Seleksi sesuai ketentuan Pasal 41 ayat (3) Perpres 16/2018 jo. Perpres 46/2025.'
                        : 'Periksa objek utama; jika merupakan layanan keahlian intelektual (konsultansi), koreksi jenis_pengadaan menjadi Jasa Konsultansi.';

                    $pkgViolations[] = $this->buildFinding(
                        'R-16',
                        $statusFinding,
                        $severityFinding,
                        $confidenceFinding,
                        'Klasifikasi Jenis Pengadaan',
                        'Pasal 41 Perpres 16/2018 jo. Perpres 46/2025',
                        $deskripsiFinding,
                        $rekomendasiFinding,
                        'Penilaian berdasarkan kata kunci nama paket; verifikasi KAK diperlukan untuk memastikan dominasi lingkup pekerjaan.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'metode_pengadaan', 'raw_value' => $metode],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 41', 'norm_summary' => 'Batasan dan ruang lingkup jenis pengadaan Jasa Konsultansi'],
                        ],
                        ['jenis_pengadaan' => 'Jasa Konsultansi']
                    );
                }
            }

            // R-17: Kandidat Salah Klasifikasi Pekerjaan Konstruksi -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('Pekerjaan Konstruksi', $jenisArr, true) && ! in_array('Dikecualikan', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_05_MURNI_KONSTRUKSI, RupTaxonomy::NEG_LOOKAHEAD_05)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-17',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Klasifikasi Jenis Pengadaan',
                        'Pasal 38 Perpres 16/2018 jo. Perpres 46/2025',
                        'Kandidat Salah Klasifikasi Pekerjaan Konstruksi: Objek paket memuat indikasi pekerjaan fisik bangunan gedung/jalan/irigasi namun tidak tercatat Pekerjaan Konstruksi.',
                        'Periksa apakah yang dibeli adalah pekerjaan fisik konstruksi utuh atau sekadar material/jasa pemeliharaan; jika fisik utuh, koreksi menjadi Pekerjaan Konstruksi.',
                        'Kata rehabilitasi/pembangunan dapat merujuk pada pemeliharaan atau bahan; klarifikasi dokumen KAK/RAB diperlukan.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 3', 'norm_summary' => 'Definisi dan klasifikasi Pekerjaan Konstruksi'],
                        ],
                        ['jenis_pengadaan' => 'Pekerjaan Konstruksi']
                    );
                }
            }

            // R-18: Kandidat Jasa Lainnya Diklasifikasikan Non-Jasa Lainnya -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('Jasa Lainnya', $jenisArr, true) && ! in_array('Dikecualikan', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_07_MURNI_JASA_LAINNYA, RupTaxonomy::NEG_LOOKAHEAD_07)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-18',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Klasifikasi Jenis Pengadaan',
                        'Pasal 3 Perpres 16/2018 jo. Perpres 46/2025',
                        "Kandidat Jasa Lainnya Tercatat Selain Jasa Lainnya: Objek paket berupa layanan sewa/pemeliharaan/kebersihan/keamanan namun dipilih jenis '{$jenis}'.",
                        'Reviu apakah substansi kontrak adalah layanan jasa non-intelektual (sewa alat/jasa pemeliharaan); jika ya, koreksi jenis menjadi Jasa Lainnya.',
                        'Penilaian berdasarkan nama paket; jika pengadaan barang disertai layanan purna jual, jenis Barang dapat tetap tepat.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 3', 'norm_summary' => 'Pemisahan definisi Barang dan Jasa Lainnya'],
                        ],
                        ['jenis_pengadaan' => 'Jasa Lainnya']
                    );
                }
            }

            // R-19: Kandidat Barang Diklasifikasikan Non-Barang -> PERLU_VERIFIKASI, SEDANG, SEDANG
            if ($cara === 'Penyedia' && ! in_array('Barang', $jenisArr, true) && ! in_array('Dikecualikan', $metodeArr, true)) {
                if (RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_08_MURNI_BARANG, RupTaxonomy::NEG_LOOKAHEAD_08)) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-19',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Klasifikasi Jenis Pengadaan',
                        'Pasal 3 Perpres 16/2018 jo. Perpres 46/2025',
                        "Kandidat Barang Tercatat Selain Barang: Objek paket jelas berupa pembelian komoditas barang habis pakai, material rutin, atau peralatan komputer namun tercatat jenis '{$jenis}'.",
                        'Reviu apakah objek utama yang diserahterimakan adalah wujud benda/komoditas barang; jika ya, sesuaikan jenis menjadi Barang.',
                        'Penilaian didasarkan pada kata kunci komoditas fisik pada nama paket.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'nama_paket', 'raw_value' => $namaPaketRaw],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 3', 'norm_summary' => 'Ruang lingkup pengadaan Barang'],
                        ],
                        ['jenis_pengadaan' => 'Barang']
                    );
                }
            }

            // R-24: Afirmasi Pencadangan UMK/Koperasi (Pasal 65 ayat (3) & (4)) -> POTENSI_TIDAK_SESUAI, SEDANG, TINGGI
            // Berdasarkan Master Plan: Berlaku untuk Penyedia, jenis Barang/Pekerjaan Konstruksi/Jasa Lainnya, pagu <= 15 M, status_umk bukan 'Ya', bukan Dikecualikan.
            // CATATAN PENTING: Jasa Konsultansi TIDAK termasuk dalam Pasal 65 ayat (3)!
            $isEligibleUmkJenis = in_array('Barang', $jenisArr, true) || in_array('Pekerjaan Konstruksi', $jenisArr, true) || in_array('Jasa Lainnya', $jenisArr, true);
            if ($cara === 'Penyedia' && $isEligibleUmkJenis && ! in_array('Jasa Konsultansi', $jenisArr, true) && ! in_array('Dikecualikan', $metodeArr, true)) {
                $isUtilitas = RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_01_DIKECUALIKAN);
                $isImpor    = RupTaxonomy::match($namaObjek, RupTaxonomy::DICT_10_TEKNOLOGI_TINGGI_IMPOR);

                if ($totalNilai > 0.0 && $totalNilai <= 15000000000.0 && $statusUmk !== 'Ya' && ! $isUtilitas && ! $isImpor) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-24',
                        'POTENSI_TIDAK_SESUAI',
                        'SEDANG',
                        'TINGGI',
                        'Pencadangan UMK-Koperasi',
                        'Pasal 65 ayat (3) & ayat (4) Perpres 16/2018 jo. Perpres 46/2025',
                        "Kandidat Pencadangan Paket untuk Usaha Mikro, Kecil, dan Koperasi: Paket bernilai <= Rp15 Miliar tercatat status_umk '{$statusUmk}'.",
                        "Reviu apakah paket dapat dicadangkan bagi UMK/Koperasi (status_umk = Ya). Catat bahwa pengecualian kemampuan teknis Pasal 65 ayat (4) tidak dapat dinilai dari data CSV RUP.",
                        'Data CSV RUP tidak memuat justifikasi teknis Pasal 65 ayat (4); PPK berwenang menetapkan status non-kecil bila kompetensi teknis tidak dapat dipenuhi UMK.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'status_umk', 'raw_value' => $statusUmk],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 65 ayat (3)', 'norm_summary' => 'Paket pengadaan Barang, Konstruksi, dan Jasa Lainnya sampai Rp15 Miliar dicadangkan untuk UMK'],
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 65 ayat (4)', 'norm_summary' => 'Pengecualian bagi pekerjaan yang menuntut kemampuan teknis yang tidak dapat dipenuhi UMK'],
                        ],
                        ['status_umk' => 'Ya (kecuali ada justifikasi teknis Pasal 65 ayat 4)']
                    );
                }
            }

            // R-24b: Segmentasi Kualifikasi Usaha & Afirmasi UMK Konsultansi (Pasal 65 ayat (4) jo. Pasal 4 PerLKPP 3/2026)
            if ($cara === 'Penyedia') {
                $isKonsultansiJenis = in_array('Jasa Konsultansi', $jenisArr, true);
                $isKonsultansiKonstruksi = RupTaxonomy::isKonsultansiKonstruksi($pkg);

                // Skenario 1: Jasa Konsultansi Konstruksi > Rp1.000.000.000 yang ditandai status_umk == 'Ya'
                // Wajib Kualifikasi Usaha Menengah (> Rp1 M - Rp2,5 M) atau Besar (> Rp2,5 M) sesuai Pasal 4 ayat (2) PerLKPP 3/2026
                if ($isKonsultansiJenis && $totalNilai > 1000000000.0 && $statusUmk === 'Ya') {
                    $judulFinding1 = $isKonsultansiKonstruksi
                        ? 'Segmentasi Kualifikasi Usaha Konsultansi Konstruksi'
                        : 'Segmentasi Kualifikasi Usaha Non-Kecil';
                    $deskripsiFinding1 = $isKonsultansiKonstruksi
                        ? "Jasa Konsultansi Konstruksi > Rp1 Miliar Tercatat UMK=Ya: Paket bernilai Rp " . number_format($totalNilai, 0, ',', '.') . " wajib diperuntukkan bagi Penyedia Kualifikasi Usaha Menengah (> Rp1 M s.d. Rp2,5 M) atau Besar (> Rp2,5 M) sesuai Pasal 4 ayat (2) Perlem LKPP 3/2026."
                        : "Paket Jasa Konsultansi > Rp1 Miliar Tercatat UMK=Ya: Paket bernilai Rp " . number_format($totalNilai, 0, ',', '.') . " umumnya diperuntukkan bagi Penyedia Kualifikasi Usaha Non-Kecil (Menengah/Besar).";

                    $pkgViolations[] = $this->buildFinding(
                        'R-24b',
                        'POTENSI_TIDAK_SESUAI',
                        'TINGGI',
                        'TINGGI',
                        $judulFinding1,
                        'Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 ayat (2) PerLKPP 3/2026',
                        $deskripsiFinding1,
                        "Koreksi status_umk menjadi 'Tidak' pada SIRUP dan pastikan dokumen pemilihan mempersyaratkan Kualifikasi Usaha Non-Kecil (Menengah/Besar).",
                        'Batas statutory kualifikasi usaha kecil untuk Jasa Konsultansi Konstruksi adalah Rp1.000.000.000 (Pasal 4 ayat 2 huruf a).',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'status_umk', 'raw_value' => $statusUmk],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 65 ayat (4)', 'norm_summary' => 'Pengecualian usaha kecil untuk paket yang menuntut kualifikasi teknis tertentu'],
                            ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pasal 4 ayat (2)', 'norm_summary' => 'Segmentasi kualifikasi usaha Jasa Konsultansi Konstruksi (Kecil <= Rp1 M, Menengah Rp1-2,5 M, Besar > Rp2,5 M)'],
                        ],
                        ['status_umk' => 'Tidak (Kualifikasi Usaha Menengah/Besar)']
                    );
                }

                // Skenario 2: Jasa Konsultansi <= Rp1.000.000.000 (non-Dikecualikan) yang ditandai status_umk != 'Ya'
                // Wajib dicadangkan bagi Usaha Kecil sesuai Pasal 4 ayat (2) huruf a PerLKPP 3/2026
                $isPerorangan = str_contains($namaClean, 'individu') || str_contains($namaClean, 'perorangan');
                if ($isKonsultansiJenis && ! in_array('Dikecualikan', $metodeArr, true) && $totalNilai > 0.0 && $totalNilai <= 1000000000.0 && $statusUmk !== 'Ya' && ! $isPerorangan) {
                    $judulFinding2 = $isKonsultansiKonstruksi
                        ? 'Pencadangan UMK Konsultansi Konstruksi'
                        : 'Pencadangan UMK Konsultansi';
                    $deskripsiFinding2 = $isKonsultansiKonstruksi
                        ? "Kandidat Pencadangan Usaha Kecil Konsultansi Konstruksi: Paket Jasa Konsultansi Konstruksi bernilai <= Rp1 Miliar (Rp " . number_format($totalNilai, 0, ',', '.') . ") tercatat status_umk '{$statusUmk}'."
                        : "Kandidat Pencadangan Usaha Kecil Konsultansi: Paket Jasa Konsultansi bernilai <= Rp1 Miliar (Rp " . number_format($totalNilai, 0, ',', '.') . ") tercatat status_umk '{$statusUmk}'.";

                    $pkgViolations[] = $this->buildFinding(
                        'R-24b',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        $judulFinding2,
                        'Pasal 4 ayat (2) huruf a PerLKPP 3/2026',
                        $deskripsiFinding2,
                        "Reviu status pencadangan UMK; jika pekerjaan dapat dilaksanakan oleh Penyedia Kualifikasi Usaha Kecil, cadangkan bagi Usaha Kecil sesuai Pasal 4 ayat (2) huruf a Perlem LKPP 3/2026.",
                        'Paket Jasa Konsultansi Konstruksi sampai dengan nilai Rp1 Miliar dicadangkan bagi Kualifikasi Usaha Kecil.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'status_umk', 'raw_value' => $statusUmk],
                        ],
                        [
                            ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pasal 4 ayat (2) huruf a', 'norm_summary' => 'Paket Jasa Konsultansi Konstruksi sampai dengan nilai Rp1.000.000.000 diperuntukkan bagi Penyedia Jasa Konsultansi Konstruksi kualifikasi usaha kecil'],
                        ],
                        ['status_umk' => 'Ya (Kualifikasi Usaha Kecil)']
                    );
                }

                // Skenario 3: Seluruh Paket Penyedia > Rp15.000.000.000 yang masih ditandai status_umk == 'Ya'
                // Umumnya diperuntukkan bagi Usaha Menengah/Besar (kecuali pagu agregat katalog/BOSP)
                if (! $isKonsultansiJenis && $totalNilai > 15000000000.0 && $statusUmk === 'Ya') {
                    $pkgViolations[] = $this->buildFinding(
                        'R-24b',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Segmentasi Usaha Non-Kecil',
                        'Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 PerLKPP 3/2026',
                        "Paket Penyedia > Rp15 Miliar Tercatat UMK=Ya: Paket bernilai Rp " . number_format($totalNilai, 0, ',', '.') . " umumnya diperuntukkan bagi Kualifikasi Usaha Menengah/Besar.",
                        'Periksa apakah paket merupakan paket tunggal (wajib Non-Kecil) atau agregat pagu (seperti Katalog/BOSP) yang transaksinya terpecah ke pelaku usaha kecil; sesuaikan status_umk bila paket diperuntukkan bagi Non-Kecil.',
                        'Evaluasi berdasarkan batas statutory afirmasi UMK Rp15 Miliar pada Pasal 65 ayat (3) & (4); reviu KAK/RAB diperlukan.',
                        [
                            ['field' => 'jenis_pengadaan', 'raw_value' => $jenis],
                            ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                            ['field' => 'status_umk', 'raw_value' => $statusUmk],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 65 ayat (4)', 'norm_summary' => 'Batasan pencadangan paket usaha kecil maksimal Rp15 Miliar'],
                            ['regulation' => 'Perlem LKPP Nomor 3 Tahun 2026', 'article' => 'Pasal 4', 'norm_summary' => 'Segmentasi kualifikasi usaha non-kecil untuk paket di atas Rp15 Miliar'],
                        ]
                    );
                }
            }

            // R-25: Verifikasi Label PDN pada RUP -> PERLU_VERIFIKASI, RENDAH, TINGGI
            if ($cara === 'Penyedia' && ! in_array('Dikecualikan', $metodeArr, true)) {
                if ($pdn === 'Tidak' || $pdn === '-' || $pdn === '') {
                    $labelPdn = ($pdn === 'Tidak') ? 'Tidak (Produk Luar Negeri/Impor)' : 'Belum Terisi (-)';
                    $pkgViolations[] = $this->buildFinding(
                        'R-25',
                        'PERLU_VERIFIKASI',
                        'RENDAH',
                        'TINGGI',
                        'Prioritas PDN',
                        'Pasal 66 Perpres 16/2018 jo. Perpres 46/2025 & Inpres 2/2022',
                        "Verifikasi Prioritas Produk Dalam Negeri: Label PDN tercatat {$labelPdn}.",
                        "Pastikan prioritas PDN telah dipertimbangkan. Jika memerlukan produk impor, pastikan telah memenuhi ketentuan justifikasi PDN.",
                        'Label PDN pada CSV mencerminkan komitmen perencanaan RUP; bukan sertifikat verifikasi TKDN aktual.',
                        [
                            ['field' => 'produk_dalam_negeri', 'raw_value' => $pdn],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 66', 'norm_summary' => 'Kewajiban penggunaan produk dalam negeri pada pengadaan pemerintah'],
                            ['regulation' => 'Inpres Nomor 2 Tahun 2022', 'article' => 'Diktum Kesatu', 'norm_summary' => 'Percepatan peningkatan penggunaan produk dalam negeri'],
                        ]
                    );
                }
            }

            // -----------------------------------------------------------------
            // KELOMPOK E: Cluster, Splitting, Duplikasi & Konsistensi
            // -----------------------------------------------------------------

            // R-20: Indikasi Splitting dari Klaster -> SINYAL_RISIKO, SEDANG, SEDANG
            if (! empty($pkg['cluster_id']) && isset($clustersByUuid[$pkg['cluster_id']])) {
                $cls = $clustersByUuid[$pkg['cluster_id']];
                $totalPaguKlaster = (float) ($cls['total_pagu_klaster'] ?? $cls['total_pagu_terakumulasi'] ?? 0.0);
                if (str_starts_with($cls['kategori_temuan'] ?? '', 'INDIKASI_SPLITTING')) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-20',
                        'SINYAL_RISIKO',
                        'SEDANG',
                        'SEDANG',
                        'Pencegahan Splitting',
                        'Pasal 20 ayat (2) & Pasal 21 Perpres 16/2018 jo. Perpres 46/2025',
                        "Sinyal Indikasi Pemecahan Paket Pengadaan Langsung: Paket sejenis dalam klaster " . esc($cls['cluster_uuid'] ?? '') . " terakumulasi mencapai Rp " . number_format($totalPaguKlaster, 0, ',', '.') . " (melebihi ambang PL).",
                        "Lakukan reviu pemaketan; periksa apakah kebutuhan dapat dikonsolidasikan menjadi satu paket pemilihan melalui Tender atau E-Purchasing.",
                        "Kemiripan teks nama dan akumulasi pagu pada CSV adalah alat deteksi indikasi; pembuktian pemecahan paket memerlukan pemeriksaan kesamaan lokasi, waktu, dan keluaran riil.",
                        [
                            ['field' => 'cluster_uuid', 'raw_value' => (string) ($cls['cluster_uuid'] ?? '')],
                            ['field' => 'total_akumulasi_klaster', 'raw_value' => (string) $totalPaguKlaster],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 20 ayat (2)', 'norm_summary' => 'Larangan memecah pengadaan menjadi beberapa paket untuk menghindari tender'],
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 21', 'norm_summary' => 'Konsolidasi pengadaan barang/jasa'],
                        ],
                        ['metode_pengadaan' => 'Konsolidasi Tender / E-Purchasing']
                    );
                } elseif (str_contains($cls['kategori_temuan'] ?? '', 'R-22')) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-22',
                        'INFORMATIF',
                        'INFO',
                        'SEDANG',
                        'Peluang Konsolidasi',
                        'Pasal 21 Perpres 16/2018 jo. Perpres 46/2025',
                        "Peluang Konsolidasi Paket Sejenis: Terdapat >= 3 paket sejenis atau paket bernilai mikro yang dapat dipertimbangkan untuk digabungkan.",
                        "Pertimbangkan penggabungan paket sejenis pada tahap perencanaan tahun berikutnya untuk meningkatkan efisiensi proses.",
                        "Banyaknya paket atau nilai mikro bukan pelanggaran hukum; pertimbangan geografis dan waktu pelaksanaan dapat menjustifikasi pemisahan paket.",
                        [
                            ['field' => 'cluster_uuid', 'raw_value' => (string) ($cls['cluster_uuid'] ?? '')],
                        ],
                        [
                            ['regulation' => 'Perpres 16/2018 jo. Perpres 46/2025', 'article' => 'Pasal 21', 'norm_summary' => 'Konsolidasi pengadaan barang/jasa'],
                        ]
                    );
                }
            }

            // R-21b: Indikasi Paket Identik Lintas Kode RUP -> SINYAL_RISIKO, SEDANG, SEDANG
            $sig21 = md5(($pkg['nama_opd_clean'] ?? '') . '|' . $namaClean . '|' . $fundingProfile . '|' . $totalNilai);
            if (isset($opdIdenticalPackages[$sig21]) && count($opdIdenticalPackages[$sig21]) > 1 && $totalNilai >= 50000000.0) {
                $pkgViolations[] = $this->buildFinding(
                    'R-21b',
                    'SINYAL_RISIKO',
                    'SEDANG',
                    'SEDANG',
                    'Sinyal Duplikasi Konten',
                    'Prinsip Akurasi Anggaran SIRUP',
                    "Indikasi Paket Identik Lintas Kode RUP: Nama, OPD, sumber dana, dan pagu paket sama persis dengan paket lain berkode berbeda.",
                    'Verifikasi apakah merupakan pencatatan ganda atau dua kebutuhan terpisah yang membutuhkan penamaan pembeda.',
                    'Kemiripan atribut nama dan pagu tidak otomatis membuktikan double-counting tanpa konfirmasi DPA.',
                    [
                        ['field' => 'nama_paket_clean', 'raw_value' => $namaClean],
                        ['field' => 'total_nilai', 'raw_value' => (string) $totalNilai],
                    ],
                    [
                        ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 13', 'norm_summary' => 'Akurasi perencanaan belanja pengadaan'],
                    ]
                );
            }

            // R-23: Inkonsistensi Nama Sama, Metode/Jenis Berbeda -> PERLU_VERIFIKASI, SEDANG, SEDANG
            $opdNameKey = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';
            if (isset($opdCleanNamesMap[$opdNameKey][$namaClean]) && count($opdCleanNamesMap[$opdNameKey][$namaClean]) > 1) {
                $allVariants = $opdCleanNamesMap[$opdNameKey][$namaClean];
                $metodesSeen = array_unique(array_column($allVariants, 'metode'));
                $jenisSeen   = array_unique(array_column($allVariants, 'jenis'));
                if (count($metodesSeen) > 1 || count($jenisSeen) > 1) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-23',
                        'PERLU_VERIFIKASI',
                        'SEDANG',
                        'SEDANG',
                        'Konsistensi Atribut Paket',
                        'PerLKPP 11/2021 tentang Konsistensi Perencanaan',
                        "Inkonsistensi Metode/Jenis pada Paket Bernama Sama: Terdapat paket dengan judul sama pada OPD ini namun menggunakan metode atau jenis pengadaan yang berbeda.",
                        'Lakukan penelaahan apakah kebutuhan memiliki karakteristik berbeda atau terjadi inkonsistensi input pada SIRUP.',
                        'Nama yang sama dapat merujuk pada komponen belanja berbeda (mis. belanja barang vs sewa); perlu klarifikasi objek spesifik.',
                        [
                            ['field' => 'nama_paket_clean', 'raw_value' => $namaClean],
                            ['field' => 'variasi_metode', 'raw_value' => implode(', ', $metodesSeen)],
                            ['field' => 'variasi_jenis', 'raw_value' => implode(', ', $jenisSeen)],
                        ],
                        [
                            ['regulation' => 'PerLKPP Nomor 11 Tahun 2021', 'article' => 'Pasal 11', 'norm_summary' => 'Konsistensi pemaketan dan metode pemilihan pada RUP'],
                        ]
                    );
                }
            }

            // R-38: Keluarga Paket Bernomor / Serial -> INFORMATIF / FILTER_FALSE_POSITIVE, INFO, TINGGI
            if (! empty($pkg['serial_sequence_number']) || ! empty($pkg['location_identity_key'])) {
                $pkgViolations[] = $this->buildFinding(
                    'R-38',
                    'INFORMATIF',
                    'INFO',
                    'TINGGI',
                    'Serial Package Family',
                    'Teknik Pencegahan False-Positive Clustering',
                    "Keluarga Paket Bernomor/Berlokasi: Paket teridentifikasi memiliki nomor urut seri (#{$pkg['serial_sequence_number']}) atau penanda lokasi khusus ('{$pkg['location_identity_key']}').",
                    'Paket ini dipisahkan dari kandidat penggabungan splitting otomatis untuk mencegah false positive.',
                    'Penanda nomor atau lokasi diekstrak langsung dari teks nama paket.',
                    [
                        ['field' => 'serial_sequence_number', 'raw_value' => (string) ($pkg['serial_sequence_number'] ?? '-')],
                        ['field' => 'location_identity_key', 'raw_value' => (string) ($pkg['location_identity_key'] ?? '-')],
                    ],
                    [
                        ['regulation' => 'Pedoman Engine v4.0', 'article' => 'Pencegahan False Positive', 'norm_summary' => 'Pemisahan paket seri bernomor dan berlokasi beda sebelum clustering'],
                    ]
                );
            }

            // R-39: Nama Berulang Lintas Sumber Dana -> SINYAL_RISIKO, RENDAH, SEDANG
            if (isset($nameAcrossFundingMap[$opdNameKey][$namaClean])) {
                $fundingsSeen = array_unique(array_column($nameAcrossFundingMap[$opdNameKey][$namaClean], 'sumber_dana'));
                if (count($fundingsSeen) > 1) {
                    $pkgViolations[] = $this->buildFinding(
                        'R-39',
                        'SINYAL_RISIKO',
                        'RENDAH',
                        'SEDANG',
                        'Sinyal Nama Lintas Sumber Dana',
                        'Prinsip Akurasi Rekonsiliasi APBD vs APBD-P',
                        "Nama Paket Berulang Lintas Sumber Dana (" . implode(' vs ', $fundingsSeen) . "): Paket bernama serupa muncul pada sumber dana yang berbeda.",
                        'Periksa apakah paket merupakan penambahan pagu pada perubahan anggaran atau dua paket belanja yang berbeda.',
                        'CSV RUP tidak membuktikan riwayat perubahan DPA; status perubahan harus dikonfirmasi pada dokumen anggaran.',
                        [
                            ['field' => 'nama_paket_clean', 'raw_value' => $namaClean],
                            ['field' => 'sumber_dana_terkait', 'raw_value' => implode('; ', $fundingsSeen)],
                        ],
                        [
                            ['regulation' => 'Prinsip Akuntansi Belanja Daerah', 'article' => 'Permendagri 77/2020', 'norm_summary' => 'Penatausahaan paket belanja murni dan perubahan anggaran'],
                        ]
                    );
                }
            }

            // Simpan temuan ke dalam paket dan list akumulasi
            $currentMaxSeverity = 'INFO';
            $sevWeightMap = ['INFO' => 0, 'RENDAH' => 1, 'SEDANG' => 2, 'TINGGI' => 3, 'KRITIS' => 3];
            $maxW = 0;

            // Prioritas status temuan:
            // POTENSI_TIDAK_SESUAI > ANOMALI_DATA > PERLU_VERIFIKASI > SINYAL_RISIKO > TIDAK_DAPAT_DINILAI_DARI_CSV > INFORMATIF > AMAN
            $statusPriorityMap = [
                'POTENSI_TIDAK_SESUAI'        => 6,
                'ANOMALI_DATA'                => 5,
                'PERLU_VERIFIKASI'            => 4,
                'SINYAL_RISIKO'               => 3,
                'TIDAK_DAPAT_DINILAI_DARI_CSV'=> 2,
                'INFORMATIF'                  => 1,
            ];
            $primaryStatus   = 'AMAN';
            $maxStatusWeight = 0;
            $statusList      = [];

            foreach ($pkgViolations as $v) {
                $st = $v['finding_status'] ?? 'INFORMATIF';
                $statusList[] = $st;
                $stWeight = $statusPriorityMap[$st] ?? 0;
                if ($stWeight > $maxStatusWeight) {
                    $maxStatusWeight = $stWeight;
                    $primaryStatus   = $st;
                }

                $w = $sevWeightMap[$v['severity']] ?? 0;
                if ($w > $maxW) {
                    $maxW = $w;
                    $currentMaxSeverity = $v['severity'];
                }
                $v['row_index_csv'] = $pkg['row_index_csv'];
                $v['kode_rup']      = $kRup;
                $v['nama_paket']    = $namaPaketRaw;
                $v['total_nilai']   = $totalNilai;
                $allViolations[]    = $v;
            }

            $pkg['max_severity']           = $maxW > 0 ? $currentMaxSeverity : 'AMAN';
            $pkg['violation_count']        = count($pkgViolations);
            $pkg['primary_finding_status'] = $primaryStatus;
            $pkg['finding_status_list']    = array_values(array_unique($statusList));
        }
        unset($pkg);

        // =====================================================================
        // PASS 2: Evaluasi Macro / RUP Risk Profile & OPD Summaries
        // =====================================================================
        $rciCalculator = new RupRciCalculator();
        $opdSummaries  = $rciCalculator->calculateOpdSummaries($packages, $clusters);
        $macroSummary  = $rciCalculator->calculateInstitutionSummary($opdSummaries, $packages);

        return [
            'violations'        => $allViolations,
            'opd_summaries'     => $opdSummaries,
            'macro_institution' => $macroSummary,
        ];
    }

    /**
     * Membangun representasi array finding v4.0 lengkap dengan fallback v3.x.
     */
    protected function buildFinding(
        string $ruleId,
        string $findingStatus,
        string $severity,
        string $confidenceLevel,
        string $kategori,
        string $regulasi,
        string $deskripsi,
        string $rekomendasi,
        string $limitationNote,
        array $evidence = [],
        array $regulatoryBasis = [],
        ?array $suggestedCorrection = null
    ): array {
        return [
            'rule_id'                  => $ruleId,
            'finding_status'           => $findingStatus,
            'severity'                 => $severity,
            'confidence_level'         => $confidenceLevel,
            'kategori'                 => $kategori,
            'regulasi'                 => $regulasi,
            'deskripsi_pelanggaran'    => $deskripsi,
            'rekomendasi_perbaikan'    => $rekomendasi,
            'limitation_note'          => $limitationNote,
            'evidence_json'            => ! empty($evidence) ? json_encode($evidence) : null,
            'regulatory_basis_json'    => ! empty($regulatoryBasis) ? json_encode($regulatoryBasis) : null,
            'recommendation_text'      => $rekomendasi,
            'suggested_correction_json'=> ! empty($suggestedCorrection) ? json_encode($suggestedCorrection) : null,
            'target_perubahan'         => $suggestedCorrection, // backward compatibility
        ];
    }
}
