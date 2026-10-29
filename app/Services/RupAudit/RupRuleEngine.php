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
        $packages[0]['kode_rup']        = $pkg['kode_rup'] ?? '123456';
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
        $packages[0]['total_nilai']     = (float) ($pkg['total_nilai'] ?? 0);
        $packages[0]['cara_pengadaan']  = $pkg['cara_pengadaan'] ?? 'Penyedia';
        $packages[0]['status_umk']      = $pkg['status_umk'] ?? 'Ya';
        $packages[0]['produk_dalam_negeri'] = $pkg['produk_dalam_negeri'] ?? 'Ya';

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

        // Hitung frekuensi nama_paket_clean per OPD untuk R-21b dan R-23
        $opdIdenticalPackages = [];
        $kodeRupCounts        = [];
        $opdKonstruksiPagu    = [];
        $opdHasPengawasan     = [];

        foreach ($packages as $idx => $pkg) {
            $opd  = $pkg['nama_opd_clean'];
            $kRup = $pkg['kode_rup'];
            if ($kRup !== '') {
                $kodeRupCounts[$kRup] = ($kodeRupCounts[$kRup] ?? 0) + 1;
            }

            $sig = md5($opd . '|' . $pkg['nama_paket_clean'] . '|' . $pkg['sumber_dana'] . '|' . $pkg['total_nilai']);
            $opdIdenticalPackages[$sig][] = $idx;

            if (in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true)) {
                $opdKonstruksiPagu[$opd] = ($opdKonstruksiPagu[$opd] ?? 0.0) + (float) $pkg['total_nilai'];
            }
            if (in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true)) {
                $cClean = strtolower((string) $pkg['nama_paket_clean']);
                if (preg_match('/(pengawasan|perencanaan|manajemen konstruksi|ded)/', $cClean)) {
                    $opdHasPengawasan[$opd] = true;
                }
            }
        }

        // =====================================================================
        // PASS 1 & PASS 2: Evaluasi Row-Level & Cluster Rules per Paket
        // =====================================================================
        foreach ($packages as $idx => &$pkg) {
            $pkgViolations = [];

            // -------------------------------------------------------------
            // KELOMPOK A: Integritas Data & Penamaan
            // -------------------------------------------------------------
            // R-01: Pagu Nol atau Negatif (KRITIS)
            if ($pkg['total_nilai'] <= 0) {
                $pkgViolations[] = [
                    'rule_id'               => 'R-01',
                    'severity'              => 'KRITIS',
                    'kategori'              => 'Integritas Data',
                    'regulasi'              => 'Pasal 11 Perlem LKPP 11/2021',
                    'deskripsi_pelanggaran' => 'Pagu Anggaran Nol atau Negatif: Paket RUP diumumkan tanpa nilai pagu yang valid (<= Rp0).',
                    'rekomendasi_perbaikan' => 'Lengkapi nilai pagu sesuai DPA/RKA atau hapus paket jika batal dilaksanakan.',
                    'target_perubahan'      => ['total_nilai_saran' => 'Sesuai DPA'],
                ];
            }

            // R-02: Atribut Penyedia Tidak Lengkap (KRITIS)
            if ($pkg['cara_pengadaan'] === 'Penyedia') {
                $hasMetode = ! empty($pkg['metode_pengadaan']) && $pkg['metode_pengadaan'] !== '-';
                $hasJenis  = ! empty($pkg['jenis_pengadaan']) && $pkg['jenis_pengadaan'] !== '-';
                if (! $hasMetode || ! $hasJenis) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-02',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Kelengkapan Atribut',
                        'regulasi'              => 'Pasal 22 Perpres 16/2018 jo. Perpres 46/2025',
                        'deskripsi_pelanggaran' => 'Atribut Paket Penyedia Tidak Lengkap: Paket Penyedia tidak mencantumkan metode pemilihan atau jenis pengadaan.',
                        'rekomendasi_perbaikan' => 'Lengkapi metode pemilihan penyedia dan jenis pengadaan pada SIRUP sebelum paket dieksekusi.',
                        'target_perubahan'      => [
                            'metode_pengadaan_saran' => 'E-Purchasing / Pengadaan Langsung / Tender',
                            'jenis_pengadaan_saran'  => 'Barang / Pekerjaan Konstruksi / Jasa Konsultansi / Jasa Lainnya',
                        ],
                    ];
                }
            }

            // R-03: Anomali Nilai Pagu Ekstrem Rendah (SEDANG)
            if ($pkg['total_nilai'] > 0 && $pkg['total_nilai'] < 10000) {
                $pkgViolations[] = [
                    'rule_id'               => 'R-03',
                    'severity'              => 'SEDANG',
                    'kategori'              => 'Kewajaran Nilai',
                    'regulasi'              => 'Prinsip Akuntabilitas Data SIRUP',
                    'deskripsi_pelanggaran' => 'Anomali Nilai Pagu Ekstrem Rendah (< Rp10.000): Mengindikasikan salah input satuan (misal ribuan/jutaan) atau salah ketik.',
                    'rekomendasi_perbaikan' => 'Verifikasi kembali kesesuaian digit nilai pagu terhadap DPA.',
                    'target_perubahan'      => null,
                ];
            }

            // R-34: Penamaan Paket Kabur / Opaque Naming (SEDANG)
            if (RupNlpExtractor::isOpaquePackageName($pkg)) {
                $pkgViolations[] = [
                    'rule_id'               => 'R-34',
                    'severity'              => 'SEDANG',
                    'kategori'              => 'Transparansi Penamaan',
                    'regulasi'              => 'Pasal 6 Perpres PBJ & Perlem LKPP 11/2021',
                    'deskripsi_pelanggaran' => 'Penamaan Paket Kabur / Terlalu Generik (Opaque Naming): Nama paket tidak menyebutkan objek belanja secara spesifik.',
                    'rekomendasi_perbaikan' => 'Perjelas nama paket sesuai rincian objek belanja/komoditas pada DPA dan lengkapi spesifikasi pekerjaan.',
                    'target_perubahan'      => null,
                ];
            }

            // -------------------------------------------------------------
            // KELOMPOK B: Batas Nilai & Metode Pemilihan Penyedia
            // -------------------------------------------------------------
            if ($pkg['cara_pengadaan'] === 'Penyedia' && empty($pkg['is_blud'])) {
                $isPL = in_array('Pengadaan Langsung', $pkg['metode_arr'] ?? [], true);
                $isBarangJasaLain = in_array('Barang', $pkg['jenis_arr'] ?? [], true) || in_array('Jasa Lainnya', $pkg['jenis_arr'] ?? [], true);
                $isKonstruksi     = in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true);
                $isKonsultansi    = in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true);

                // R-04: PL Barang/Jasa Lainnya > Rp200 Juta (KRITIS)
                if ($isPL && $isBarangJasaLain && ! $isKonstruksi && $pkg['total_nilai'] > 200000000) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-04',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Batas Pagu Metode',
                        'regulasi'              => 'Pasal 1 angka 40 & Pasal 38 ayat (3) Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Pelanggaran Batas Pagu Pengadaan Langsung Barang/Jasa Lainnya (> Rp200 Juta): Nilai paket melampaui batas tertinggi PL (Rp200.000.000).',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi E-Purchasing (jika tersedia di Katalog V6) atau Tender / Tender Cepat.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'E-Purchasing / Tender'],
                    ];
                }

                // R-04b: Peringatan Fitur Transaksional SPSE > Rp50 Juta & Reviu APIP Jasa Lainnya Perorangan (RENDAH)
                $isJasaLainnya = in_array('Jasa Lainnya', $pkg['jenis_arr'] ?? [], true);
                if ($isPL && $isJasaLainnya && $pkg['total_nilai'] > 50000000.0) {
                    $teksObjek   = strtolower((string) ($pkg['nama_paket_objek'] ?? ''));
                    if ($teksObjek === '') {
                        $teksObjek = strtolower((string) ($pkg['nama_paket_clean'] ?? ''));
                    }
                    $teksLengkap = strtolower((string) (($pkg['nama_paket_clean'] ?? '') . ' ' . ($pkg['nama_paket'] ?? '')));

                    // Anti-False Positive Exclusion R-04b (Kendaraan Perorangan, Tenaga Listrik, Tambah Daya, Alat Angkutan)
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
                            $pkgViolations[] = [
                                'rule_id'               => 'R-04b',
                                'severity'              => 'RENDAH',
                                'kategori'              => 'Bentuk Kontrak & Fitur SPSE',
                                'regulasi'              => 'Pasal 3 ayat (3), (4) huruf b, dan ayat (5) Perlem LKPP 3/2026',
                                'deskripsi_pelanggaran' => 'Peringatan Fitur Transaksional SPSE & Reviu APIP Jasa Lainnya > Rp50 Juta: Sesuai Pasal 3 ayat (3), (4), dan (5) Perlem LKPP 3/2026, Pengadaan Langsung Jasa Lainnya > Rp50 Juta wajib dilaksanakan melalui Fitur Transaksional SPSE (bentuk kontrak SPK). Pengecualian penggunaan Fitur Pencatatan untuk penyedia Jasa Lainnya Perorangan pendukung operasional layanan publik WAJIB mendapatkan Reviu APIP (Inspektorat Daerah) sebelum pemilihan Penyedia.',
                                'rekomendasi_perbaikan' => 'Gunakan Fitur Transaksional SPSE dengan bentuk kontrak SPK, atau pastikan telah memperoleh Berita Acara Reviu APIP (Inspektorat Daerah) sebelum pemilihan jika menggunakan skema Jasa Lainnya Perorangan via Fitur Pencatatan.',
                                'target_perubahan'      => ['bentuk_kontrak_saran' => 'SPK / Transaksional SPSE (Reviu APIP jika Pencatatan Perorangan)'],
                            ];
                        }
                    }
                }

                // Tahun anggaran paket pengadaan untuk kesadaran rezim regulasi (time-aware thresholding)
                $tahunAnggaran = (int) ($pkg['tahun_anggaran'] ?? 2026);
                $batasPlKonstruksi = ($tahunAnggaran > 0 && $tahunAnggaran <= 2024) ? 200000000.0 : 400000000.0;

                // R-05: PL Konstruksi > Batas Tertinggi (KRITIS)
                // Rezim Perpres 12/2021 (TA <= 2024) = Rp200 Juta; Rezim Perpres 46/2025 jo. Perlem LKPP 3/2026 (TA >= 2025) = Rp400 Juta
                if ($isPL && $isKonstruksi && ! $isKonsultansi && $pkg['total_nilai'] > $batasPlKonstruksi) {
                    $deskripsiR05 = ($tahunAnggaran > 0 && $tahunAnggaran <= 2024)
                        ? "Pelanggaran Batas Pagu Pengadaan Langsung Konstruksi Rezim Perpres 12/2021 (> Rp200 Juta): Nilai paket melampaui batas tertinggi PL Konstruksi TA {$tahunAnggaran} (Rp200.000.000)."
                        : 'Pelanggaran Batas Pagu Pengadaan Langsung Konstruksi (> Rp400 Juta): Nilai paket melampaui batas tertinggi baru PL Konstruksi (Rp400.000.000).';
                    $regulasiR05 = ($tahunAnggaran > 0 && $tahunAnggaran <= 2024)
                        ? 'Pasal 1 angka 40 Perpres 12/2021'
                        : 'Pasal 1 angka 40 Perpres 46/2025 jo. Perlem LKPP 3/2026';

                    $pkgViolations[] = [
                        'rule_id'               => 'R-05',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Batas Pagu Metode',
                        'regulasi'              => $regulasiR05,
                        'deskripsi_pelanggaran' => $deskripsiR05,
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi Tender atau E-Purchasing Katalog Konstruksi.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Tender / E-Purchasing'],
                    ];
                }

                // R-06: PL Konsultansi > Rp100 Juta (KRITIS)
                if ($isPL && $isKonsultansi && $pkg['total_nilai'] > 100000000) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-06',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Batas Pagu Metode',
                        'regulasi'              => 'Pasal 1 angka 41 & Pasal 41 ayat (3) Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Pelanggaran Batas Pagu Pengadaan Langsung Jasa Konsultansi (> Rp100 Juta): Nilai paket melampaui batas tertinggi PL Konsultansi (Rp100.000.000).',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi Seleksi atau E-Purchasing Katalog Jasa Konsultansi.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Seleksi / E-Purchasing'],
                    ];
                }

                // R-07: PL Konstruksi Rp200-400 Juta Wajib UMK & Teknologi Sederhana/Risiko Kecil (TINGGI)
                // Berlaku khusus rezim Perpres 46/2025 jo. Perlem LKPP 3/2026 (TA >= 2025)
                if (($tahunAnggaran === 0 || $tahunAnggaran >= 2025) && $isPL && $isKonstruksi && $pkg['total_nilai'] > 200000000 && $pkg['total_nilai'] <= 400000000) {
                    $isNonUmk   = ($pkg['status_umk'] !== 'Ya');
                    $isKompleks = RupTaxonomy::match((string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean']), RupTaxonomy::DICT_KONSTRUKSI_KOMPLEKS);

                    if ($isNonUmk || $isKompleks) {
                        $rekomendasi = $isKompleks
                            ? 'Ubah metode pemilihan menjadi Tender atau E-Purchasing Katalog Konstruksi karena terindikasi pekerjaan konstruksi kompleks/berisiko tidak sederhana.'
                            : 'Ubah status_umk menjadi Ya dan pastikan kualifikasi usaha kecil, atau gunakan Tender jika butuh kualifikasi non-kecil.';

                        $deskripsi = 'Pelanggaran Syarat PL Konstruksi Rp200 Juta - Rp400 Juta: ';
                        if ($isNonUmk && $isKompleks) {
                            $deskripsi .= 'status_umk bukan Usaha Kecil dan terindikasi berteknologi kompleks/risiko tidak kecil.';
                        } elseif ($isKompleks) {
                            $deskripsi .= 'Pekerjaan terindikasi berteknologi kompleks / risiko tidak kecil (memuat objek konstruksi kompleks), tidak memenuhi kualifikasi Pengadaan Langsung Rp200-400 Juta.';
                        } else {
                            $deskripsi .= 'PL Konstruksi > Rp200 Juta wajib Usaha Kecil (status_umk = Ya).';
                        }
                        $deskripsi .= ' Sesuai Perpres 46/2025 jo. Perlem LKPP 3/2026, PL Konstruksi Rp200-400 Jt hanya untuk Usaha Kecil berteknologi sederhana / risiko kecil.';

                        $pkgViolations[] = [
                            'rule_id'               => 'R-07',
                            'severity'              => 'TINGGI',
                            'kategori'              => 'Syarat PL Konstruksi Rp200-400 Juta',
                            'regulasi'              => 'Pasal 1 angka 40 Perpres 46/2025 jo. Perlem LKPP 3/2026',
                            'deskripsi_pelanggaran' => $deskripsi,
                            'rekomendasi_perbaikan' => $rekomendasi,
                            'target_perubahan'      => $isKompleks ? ['metode_pengadaan_saran' => 'Tender / E-Purchasing Katalog Konstruksi'] : ['status_umk_saran' => 'Ya'],
                        ];
                    }
                }

                // R-27: Threshold Hugging 95% - 100% Batas PL (TINGGI) - Time-Aware
                $batasPl = RupGraphClusterer::getBatasPl((string) $pkg['jenis_pengadaan'], $tahunAnggaran);
                if ($isPL && $pkg['total_nilai'] >= (0.95 * $batasPl) && $pkg['total_nilai'] <= $batasPl) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-27',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Pencegahan Pemecahan Pagu',
                        'regulasi'              => 'Pasal 20 ayat (2) Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Anomali Pagu Mepet Batas Atas Pengadaan Langsung (Threshold Hugging 95% - 100%): Berisiko tinggi rekayasa pembatasan pagu untuk menghindari Tender/Seleksi.',
                        'rekomendasi_perbaikan' => 'Lakukan reviu kertas kerja HPS/RAB; utamakan E-Purchasing atau konsolidasikan ke Tender/Seleksi.',
                        'target_perubahan'      => null,
                    ];
                }

                // R-28: Over-Method Anomaly pada Pagu Kecil (SEDANG)
                $isTender  = in_array('Tender', $pkg['metode_arr'] ?? [], true);
                $isSeleksi = in_array('Seleksi', $pkg['metode_arr'] ?? [], true);
                if (($isTender && ! $isKonsultansi && $pkg['total_nilai'] <= 200000000) ||
                    ($isSeleksi && $isKonsultansi && $pkg['total_nilai'] <= 100000000)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-28',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Efisiensi Metode',
                        'regulasi'              => 'Prinsip Efisiensi Pasal 6 & Pasal 38 Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Inefisiensi Metode Kompetitif pada Pagu Kecil (Over-Method Anomaly): Paket di bawah batas PL diinput dengan Tender/Seleksi.',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi E-Purchasing atau Pengadaan Langsung oleh Pejabat Pengadaan.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'E-Purchasing / Pengadaan Langsung'],
                    ];
                }

                // R-29: Validasi Tender Cepat (TINGGI)
                $isTenderCepat = in_array('Tender Cepat', $pkg['metode_arr'] ?? [], true);
                if ($isTenderCepat && ($isKonstruksi || $isKonsultansi || $pkg['total_nilai'] <= 200000000)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-29',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Kesesuaian Metode',
                        'regulasi'              => 'Pasal 38 ayat (4) & Pasal 50 ayat (4) Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Ketidaksesuaian Metode Tender Cepat: Tender Cepat tidak diizinkan untuk Pekerjaan Konstruksi, Konsultansi, atau pagu <= Rp200 Juta.',
                        'rekomendasi_perbaikan' => 'Gunakan E-Purchasing (jika di Katalog), Pengadaan Langsung (jika <= Rp200 Juta), atau Tender Reguler.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'E-Purchasing / Tender'],
                    ];
                }

                // R-33: Penajaman Rule Penyalahgunaan Jenis Terintegrasi (Pasal 54 Perpres PBJ) & PL Terintegrasi (TINGGI)
                $isJenisTerintegrasi = (strtolower(trim((string) ($pkg['jenis_pengadaan'] ?? ''))) === 'terintegrasi');
                $isIntegratedPackage = ! empty($pkg['is_integrated_package']);

                if ($isPL && ($isIntegratedPackage || $isJenisTerintegrasi)) {
                    // Kasus a: Paket bertipe is_integrated_package atau jenis Terintegrasi menggunakan Pengadaan Langsung
                    $pkgViolations[] = [
                        'rule_id'               => 'R-33',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Paket Terintegrasi',
                        'regulasi'              => 'Pasal 54 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Pengadaan Pekerjaan Terintegrasi Dilarang Menggunakan Metode Pengadaan Langsung: Pekerjaan Terintegrasi memiliki kompleksitas tinggi dan wajib dilaksanakan melalui Tender/Seleksi.',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi Tender/Seleksi atau pisahkan pengadaan sesuai jenis masing-masing.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Tender'],
                    ];
                } elseif ($isJenisTerintegrasi) {
                    // Kasus b: Paket memilih jenis_pengadaan == 'Terintegrasi' namun objeknya komoditas Barang/Jasa Lainnya standar
                    $stdCommodities = array_merge(
                        RupTaxonomy::DICT_04_KATALOG_STANDAR,
                        RupTaxonomy::DICT_07_MURNI_JASA_LAINNYA,
                        RupTaxonomy::DICT_08_MURNI_BARANG
                    );
                    $objekSearch = (string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '');
                    if (RupTaxonomy::match($objekSearch, $stdCommodities)) {
                        $pkgViolations[] = [
                            'rule_id'               => 'R-33',
                            'severity'              => 'TINGGI',
                            'kategori'              => 'Penyalahgunaan Jenis Terintegrasi',
                            'regulasi'              => 'Pasal 54 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                            'deskripsi_pelanggaran' => 'Penyalahgunaan Jenis Pengadaan Terintegrasi: Objek paket merupakan komoditas Barang/Jasa Lainnya standar yang tidak memenuhi kriteria Pengadaan Terintegrasi (Design & Build / EPC).',
                            'rekomendasi_perbaikan' => 'Ubah jenis_pengadaan menjadi Barang atau Jasa Lainnya sesuai objek belanja riil.',
                            'target_perubahan'      => ['jenis_pengadaan_saran' => 'Barang / Jasa Lainnya'],
                        ];
                    }
                }

                // R-32: Paket Konstruksi > Rp400 Juta Tanpa Konsultan Pengawasan (SEDANG)
                if ($isKonstruksi && $pkg['total_nilai'] > 400000000) {
                    $opdKey = $pkg['nama_opd_clean'];
                    if (($opdKonstruksiPagu[$opdKey] ?? 0.0) > 400000000 && empty($opdHasPengawasan[$opdKey])) {
                        $pkgViolations[] = [
                            'rule_id'               => 'R-32',
                            'severity'              => 'SEDANG',
                            'kategori'              => 'Kelengkapan Paket Konstruksi',
                            'regulasi'              => 'Pasal 7 ayat (1) dan ayat (5) Peraturan LKPP Nomor 3 Tahun 2026 & PP 16/2021',
                            'deskripsi_pelanggaran' => 'Paket Pekerjaan Konstruksi > Rp400 Juta Tanpa Konsultan Pengawasan/Perencanaan terdaftar di RUP OPD.',
                            'rekomendasi_perbaikan' => 'Pastikan paket Jasa Konsultansi Pengawasan/Manajemen Konstruksi telah diinput ke SIRUP, atau tetapkan Tim/Tenaga Teknis internal yang wajib menyusun laporan pengawasan konstruksi sesuai Pasal 7 ayat (5) Perlem LKPP 3/2026.',
                            'target_perubahan'      => null,
                        ];
                    }
                }
            }

            // R-08: Penunjukan Langsung Tanpa Indikasi Sah (TINGGI) & R-08b: Verifikasi Repeat Order (RENDAH)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && in_array('Penunjukan Langsung', $pkg['metode_arr'] ?? [], true)) {
                $isSahDikecualikan = RupTaxonomy::match((string) $pkg['nama_paket_clean'], RupTaxonomy::DICT_01_DIKECUALIKAN);
                $isSahKondisi      = RupTaxonomy::match((string) $pkg['nama_paket_clean'], RupTaxonomy::DICT_11_PENUNJUKAN_LANGSUNG_SAH);

                // Pemisahan Kewenangan Pejabat Pengadaan vs Pokja Pemilihan (Lampiran I & II Angka 1.4 Perlem LKPP 3/2026)
                $isKonsultansiPL = in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true);
                $batasPpPL       = $isKonsultansiPL ? 100000000.0 : 200000000.0;
                $nilaiPaketPL    = (float) ($pkg['total_nilai'] ?? 0.0);
                $isKewenanganPokjaPL = ($nilaiPaketPL > $batasPpPL);

                $kewenanganTeksPL = $isKewenanganPokjaPL
                    ? 'Wajib dilaksanakan oleh Pokja Pemilihan UKPBJ (Pagu > ' . ($isKonsultansiPL ? 'Rp100 Juta' : 'Rp200 Juta') . ' sesuai Lampiran I & II Angka 1.4 Perlem LKPP 3/2026).'
                    : 'Dapat dilaksanakan oleh Pejabat Pengadaan (Pagu <= ' . ($isKonsultansiPL ? 'Rp100 Juta' : 'Rp200 Juta') . ' sesuai Lampiran I & II Angka 1.4 Perlem LKPP 3/2026).';

                $pelaksanaSaranPL = $isKewenanganPokjaPL ? 'Pokja Pemilihan UKPBJ' : 'Pejabat Pengadaan';

                if (! $isSahDikecualikan && ! $isSahKondisi) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-08',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Justifikasi Penunjukan Langsung',
                        'regulasi'              => 'Pasal 38 ayat (5), Pasal 38A, Pasal 41 ayat (5) & Pasal 41A Perpres 46/2025 jo. Lampiran I & II Angka 1.4 Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => "Penggunaan Penunjukan Langsung Tanpa Indikasi Keadaan Tertentu: Tidak menunjukkan kriteria Repeat Order, darurat, paten, atau hotel. {$kewenanganTeksPL}",
                        'rekomendasi_perbaikan' => "Verifikasi Berita Acara Justifikasi Keadaan Tertentu. Jika pengadaan reguler, ubah ke E-Purchasing / Tender / Seleksi. Jika terjustifikasi Penunjukan Langsung, proses pemilihan {$kewenanganTeksPL}",
                        'target_perubahan'      => [
                            'metode_pengadaan_saran' => 'E-Purchasing / Tender / Seleksi',
                            'pelaksana_pemilihan'    => $pelaksanaSaranPL,
                        ],
                    ];
                }

                // R-08b: Verifikasi Ketat 5 Syarat Kumulatif Repeat Order (RO) sesuai Lampiran I Angka 5.2.1 Perlem LKPP 3/2026
                $isRepeatOrder = str_contains(strtolower((string) $pkg['nama_paket_clean']), 'repeat order') || str_contains(strtolower((string) $pkg['nama_paket']), 'repeat order');
                if ($isRepeatOrder) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-08b',
                        'severity'              => 'RENDAH',
                        'kategori'              => 'Verifikasi Repeat Order',
                        'regulasi'              => 'Pasal 38 ayat (5) Perpres 46/2025 jo. Lampiran I Angka 5.2.1 Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Verifikasi 5 Syarat Kumulatif Repeat Order (RO) Lampiran I Angka 5.2.1 Perlem LKPP 3/2026: (1) Paket awal wajib hasil Tender/Seleksi; (2) HPS RO <= nilai kontrak akhir paket awal; (3) Maksimal 2x dalam 3 tahun sejak BAST akhir; (4) Kinerja penyedia Sangat Baik pada paket awal (dan Sangat Baik pada RO-1 jika RO-2); (5) Ketua Tim (Team Leader) konsultan wajib tetap sama.',
                        'rekomendasi_perbaikan' => 'Lakukan verifikasi berkas: (1) Kontrak awal hasil Tender/Seleksi; (2) HPS RO tidak melebihi nilai kontrak awal; (3) Riwayat perulangan (maksimal ke-2); (4) Nilai kinerja SIKaP penyedia berpredikat Sangat Baik; (5) Kesamaan personel Team Leader (jika Konsultansi).',
                        'target_perubahan'      => ['verifikasi_repeat_order' => '5 Syarat Kumulatif Lampiran I Angka 5.2.1 Perlem LKPP 3/2026'],
                    ];
                }
            }

            // R-09: Inkompatibilitas Metode vs Jenis (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Penyedia') {
                $isSeleksi = in_array('Seleksi', $pkg['metode_arr'] ?? [], true);
                $isTender  = in_array('Tender', $pkg['metode_arr'] ?? [], true) || in_array('Tender Cepat', $pkg['metode_arr'] ?? [], true);
                $isKontes  = in_array('Kontes', $pkg['metode_arr'] ?? [], true);
                $isKonsult = in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true);
                $onlyKonsult = $isKonsult && count($pkg['jenis_arr'] ?? []) === 1;

                if (($isSeleksi && ! $isKonsult) ||
                    ($isTender && $onlyKonsult) ||
                    ($isKontes && RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_04_KATALOG_STANDAR))) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-09',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Kompatibilitas Metode',
                        'regulasi'              => 'Pasal 38 & Pasal 41 Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Inkompatibilitas Metode Pemilihan terhadap Jenis Pengadaan: Metode Seleksi untuk non-Konsultansi atau Tender untuk murni Konsultansi.',
                        'rekomendasi_perbaikan' => 'Gunakan Seleksi untuk Konsultansi > Rp100 Juta, dan Tender/E-Purchasing untuk Barang/Konstruksi/Jasa Lainnya.',
                        'target_perubahan'      => null,
                    ];
                }
            }

            // -------------------------------------------------------------
            // KELOMPOK C: Cara Pengadaan (Penyedia vs Swakelola)
            // -------------------------------------------------------------
            // R-10: Komoditas Tarif Resmi/Utilitas non-Dikecualikan (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_01_DIKECUALIKAN)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-10',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Pengadaan Dikecualikan',
                        'regulasi'              => 'Pasal 62 ayat (1) Perpres PBJ & Perlem LKPP 5/2021',
                        'deskripsi_pelanggaran' => 'Salah Metode untuk Komoditas Tarif Resmi/Pajak/Utilitas: Tagihan listrik/air/telepon/pajak diinput sebagai Pengadaan/Penunjukan Langsung.',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi Dikecualikan atau catat pada mekanisme Swakelola.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Dikecualikan'],
                    ];
                }
            }

            // R-11: Jasa Tenaga Alih Daya Masuk Swakelola (KRITIS)
            if ($pkg['cara_pengadaan'] === 'Swakelola') {
                $teksSwakelola = (string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '');
                if (RupTaxonomy::match($teksSwakelola, RupTaxonomy::DICT_02_OUTSOURCING, RupTaxonomy::NEG_LOOKAHEAD_02)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-11',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Larangan Non-ASN / Outsourcing',
                        'regulasi'              => 'Pasal 66 UU 20/2023 (ASN) & Pasal 16-18 Perpres PBJ',
                        'deskripsi_pelanggaran' => 'Pengadaan Jasa Alih Daya (Outsourcing) Diinput sebagai Swakelola: Jasa kebersihan/keamanan/sopir/tenaga pendukung/PTHL/TKS berisiko melanggar larangan non-ASN.',
                        'rekomendasi_perbaikan' => 'Ubah cara_pengadaan menjadi Penyedia, jenis_pengadaan = Jasa Lainnya, via E-Purchasing / Tender penyedia jasa pekerja.',
                        'target_perubahan'      => [
                            'cara_pengadaan_saran'  => 'Penyedia',
                            'jenis_pengadaan_saran' => 'Jasa Lainnya',
                        ],
                    ];
                }
            }

            // R-12: Pekerjaan Penyedia/Modal/Konstruksi Masuk Swakelola (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Swakelola') {
                $isTipe1OrNull = in_array($pkg['tipe_swakelola'] ?? 'Tipe 1', ['Tipe 1', '-', '', null], true);
                $isKonsultansi = RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_06_MURNI_KONSULTANSI, RupTaxonomy::NEG_LOOKAHEAD_06);
                $isKonstruksi  = RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_05_MURNI_KONSTRUKSI, RupTaxonomy::NEG_LOOKAHEAD_05);
                $hasPihakKetiga = str_contains((string) $pkg['nama_paket_objek'], 'pihak ketiga') || str_contains((string) $pkg['nama_paket_objek'], 'kalibrasi') || str_contains((string) $pkg['nama_paket_objek'], 'belanja modal');

                if ($isTipe1OrNull && ($isKonsultansi || $isKonstruksi || $hasPihakKetiga)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-12',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Kesesuaian Swakelola',
                        'regulasi'              => 'Pasal 18 Perpres 12/2021 jo. Perpres 46/2025 & Perlem LKPP 3/2021',
                        'deskripsi_pelanggaran' => 'Pekerjaan Penyedia/Modal/Konstruksi/Konsultansi Masuk ke dalam Swakelola Tipe I.',
                        'rekomendasi_perbaikan' => 'Jika dikerjakan PTN/Ormas/Pokmas, lengkapi tipe_swakelola (Tipe 2/3/4). Jika dilaksanakan vendor swasta, ubah menjadi Penyedia.',
                        'target_perubahan'      => ['cara_pengadaan_saran' => 'Penyedia'],
                    ];
                }
            }

            // R-13: Belanja Pegawai / Administrasi Internal Diinput Penyedia (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_03_SWAKELOLA_MURNI, RupTaxonomy::NEG_LOOKAHEAD_03)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-13',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Kesesuaian Cara Pengadaan',
                        'regulasi'              => 'Perlem LKPP 11/2021 tentang Perencanaan PBJ',
                        'deskripsi_pelanggaran' => 'Belanja Pegawai/Administrasi Internal Diinput sebagai Penyedia: Objek paket berupa perjalanan dinas/honorarium/uang saku.',
                        'rekomendasi_perbaikan' => 'Pindahkan pencatatan paket dari Penyedia ke Swakelola.',
                        'target_perubahan'      => ['cara_pengadaan_saran' => 'Swakelola'],
                    ];
                }
            }

            // R-14: Validasi Belanja Hibah / Bantuan Sosial (SEDANG)
            if (RupTaxonomy::match((string) $pkg['nama_paket_clean'], RupTaxonomy::DICT_09_HIBAH_BANSOS)) {
                $isBarang = str_contains((string) $pkg['nama_paket_clean'], 'barang');
                $isUang   = str_contains((string) $pkg['nama_paket_clean'], 'uang');
                if (($isBarang && $pkg['cara_pengadaan'] === 'Swakelola') || ($isUang && $pkg['cara_pengadaan'] === 'Penyedia')) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-14',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Tata Kelola Hibah/Bansos',
                        'regulasi'              => 'Permendagri 77/2020 & Perpres PBJ',
                        'deskripsi_pelanggaran' => 'Ketidaksesuaian Mekanisme Belanja Hibah/Bansos: Hibah barang diinput Swakelola atau hibah uang diinput Penyedia.',
                        'rekomendasi_perbaikan' => 'Hibah Barang wajib melalui Penyedia (E-Purchasing/Tender). Hibah Uang (NPHD) dicatat dalam Swakelola.',
                        'target_perubahan'      => ['cara_pengadaan_saran' => $isBarang ? 'Penyedia' : 'Swakelola'],
                    ];
                }
            }

            // R-30: Validasi Khusus BLUD & DAK (TINGGI)
            if (! empty($pkg['is_blud']) && $pkg['cara_pengadaan'] === 'Penyedia' && in_array('Pengadaan Langsung', $pkg['metode_arr'] ?? [], true)) {
                $batasPl = RupGraphClusterer::getBatasPl((string) $pkg['jenis_pengadaan']);
                if ($pkg['total_nilai'] > $batasPl) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-30',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Pengadaan Khusus BLUD',
                        'regulasi'              => 'Pasal 61 Perpres 46/2025 & Perlem LKPP 5/2021',
                        'deskripsi_pelanggaran' => 'Paket BLUD di atas batas PL Perpres masih memakai label Pengadaan Langsung.',
                        'rekomendasi_perbaikan' => 'Gunakan metode Dikecualikan jika berpedoman pada Peraturan Pemimpin BLUD, atau gunakan E-Purchasing.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Dikecualikan / E-Purchasing'],
                    ];
                }
            }

            // -------------------------------------------------------------
            // KELOMPOK D: E-Purchasing, Ketepatan Jenis & Jadwal
            // -------------------------------------------------------------
            // R-15: Penghindaran Kewajiban E-Purchasing Katalog V6 (SEDANG)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('E-Purchasing', $pkg['metode_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_04_KATALOG_STANDAR)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-15',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Mandat Katalog V6',
                        'regulasi'              => 'Pasal 50 ayat (5), (5a), dan (5b) Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Penghindaran Kewajiban E-Purchasing Katalog Elektronik V6: Komoditas standar yang telah tayang di Katalog direncanakan non-Katalog.',
                        'rekomendasi_perbaikan' => 'Ubah metode pemilihan menjadi E-Purchasing Katalog V6 (atau Mini-Competition Katalog), kecuali PPK telah menyusun Dokumen Penilaian Tertulis pengecualian E-Purchasing sesuai Pasal 50 ayat (5a) Perpres 46/2025 jo. Perlem LKPP 3/2026.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'E-Purchasing Katalog V6'],
                    ];
                }
            }

            // R-16: Salah Klasifikasi Jasa Konsultansi (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_06_MURNI_KONSULTANSI, RupTaxonomy::NEG_LOOKAHEAD_06)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-16',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Klasifikasi Jenis',
                        'regulasi'              => 'Pasal 3 & Pasal 41 Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Salah Klasifikasi Jenis Pengadaan pada Jasa Konsultansi: Objek paket berupa kajian/perencanaan/pengawasan diinput non-Konsultansi.',
                        'rekomendasi_perbaikan' => 'Ubah jenis_pengadaan menjadi Jasa Konsultansi dan pastikan paket > Rp100 Juta menggunakan metode Seleksi.',
                        'target_perubahan'      => ['jenis_pengadaan_saran' => 'Jasa Konsultansi'],
                    ];
                }
            }

            // R-17: Salah Klasifikasi Pekerjaan Konstruksi (TINGGI)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true) && ! in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_05_MURNI_KONSTRUKSI, RupTaxonomy::NEG_LOOKAHEAD_05)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-17',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Klasifikasi Jenis',
                        'regulasi'              => 'Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Salah Klasifikasi Jenis Pengadaan pada Pekerjaan Konstruksi: Objek pembangunan/rehabilitasi fisik gedung/jalan diinput Barang/Jasa.',
                        'rekomendasi_perbaikan' => 'Ubah jenis_pengadaan menjadi Pekerjaan Konstruksi agar mengikuti standar kontrak konstruksi dan SBU/SKK.',
                        'target_perubahan'      => ['jenis_pengadaan_saran' => 'Pekerjaan Konstruksi'],
                    ];
                }
            }

            // R-18: Salah Klasifikasi Jasa Lainnya tertulis Barang (SEDANG)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && count($pkg['jenis_arr'] ?? []) === 1 && in_array('Barang', $pkg['jenis_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_07_MURNI_JASA_LAINNYA, RupTaxonomy::NEG_LOOKAHEAD_07)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-18',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Klasifikasi Jenis',
                        'regulasi'              => 'Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Salah Klasifikasi Jenis Pengadaan (Jasa Lainnya tertulis Barang): Objek sewa, pemeliharaan, atau kebersihan diinput Barang.',
                        'rekomendasi_perbaikan' => 'Ubah jenis_pengadaan dari Barang menjadi Jasa Lainnya.',
                        'target_perubahan'      => ['jenis_pengadaan_saran' => 'Jasa Lainnya'],
                    ];
                }
            }

            // R-19: Salah Klasifikasi Barang tertulis Non-Barang (SEDANG)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Barang', $pkg['jenis_arr'] ?? [], true)) {
                if (RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_08_MURNI_BARANG, RupTaxonomy::NEG_LOOKAHEAD_08)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-19',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Klasifikasi Jenis',
                        'regulasi'              => 'Pasal 3 Perpres 16/2018 jo. Perpres 46/2025 & Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Salah Klasifikasi Jenis Pengadaan: Pembelian peralatan/mesin modal atau habis pakai murni diinput non-Barang.',
                        'rekomendasi_perbaikan' => 'Ubah jenis_pengadaan menjadi Barang.',
                        'target_perubahan'      => ['jenis_pengadaan_saran' => 'Barang'],
                    ];
                }
            }

            // R-31: Anomali Kronologis Jadwal (TINGGI)
            if (! empty($pkg['tanggal_umumkan']) || ! empty($pkg['jadwal_pemilihan_awal'])) {
                $isLateMurni = ($pkg['budget_cycle'] === 'MURNI' && ! empty($pkg['tanggal_umumkan']) && strtotime((string) $pkg['tanggal_umumkan']) > strtotime($pkg['tahun_anggaran'] . '-03-31'));
                $isChronologyBad = (! empty($pkg['jadwal_pemilihan_awal']) && ! empty($pkg['jadwal_kontrak_awal']) && strtotime((string) $pkg['jadwal_pemilihan_awal']) > strtotime((string) $pkg['jadwal_kontrak_awal']));

                if ($isLateMurni || $isChronologyBad) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-31',
                        'severity'              => 'TINGGI',
                        'kategori'              => 'Jadwal & Waktu Pengumuman',
                        'regulasi'              => 'Pasal 22 Perpres PBJ, Indikator MCP KPK & ITKP LKPP',
                        'deskripsi_pelanggaran' => 'Anomali Kronologis Jadwal / Keterlambatan Pengumuman RUP Murni lewat 31 Maret.',
                        'rekomendasi_perbaikan' => 'Sesuaikan kembali kronologi pemilihan dan pelaksanaan kontrak agar selaras dengan kalender anggaran.',
                        'target_perubahan'      => null,
                    ];
                }
            }

            // -------------------------------------------------------------
            // KELOMPOK E: Evaluasi Klaster & Duplikasi per Paket
            // -------------------------------------------------------------
            // R-20: Pemecahan Paket Pengadaan Langsung (KRITIS) & R-22: Fragmentasi Paket (SEDANG)
            if (! empty($pkg['cluster_id']) && isset($clustersByUuid[$pkg['cluster_id']])) {
                $cls = $clustersByUuid[$pkg['cluster_id']];
                if (str_starts_with($cls['kategori_temuan'], 'INDIKASI_SPLITTING')) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-20',
                        'severity'              => 'KRITIS',
                        'kategori'              => 'Pencegahan Splitting',
                        'regulasi'              => 'Pasal 20 ayat (2) & Pasal 21 Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Indikasi Kuat Pemecahan Paket (Splitting): Paket PL sejenis dalam siklus anggaran yang sama terakumulasi melebihi batas PL.',
                        'rekomendasi_perbaikan' => 'Konsolidasikan seluruh paket pada klaster ' . $cls['cluster_uuid'] . ' menjadi satu paket Tender / E-Purchasing.',
                        'target_perubahan'      => ['metode_pengadaan_saran' => 'Tender / E-Purchasing'],
                    ];
                } elseif (str_contains($cls['kategori_temuan'], 'R-22')) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-22',
                        'severity'              => 'SEDANG',
                        'kategori'              => 'Konsolidasi Paket',
                        'regulasi'              => 'Pasal 21 Perpres 46/2025 (Kewajiban Konsolidasi PBJ) jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Fragmentasi Paket Berlebihan (>= 3 paket sejenis) atau terdapat paket bernilai mikro (< Rp500.000).',
                        'rekomendasi_perbaikan' => $cls['instruksi_konsolidasi'] ?? 'Konsolidasikan paket-paket sejenis untuk efisiensi pengadaan.',
                        'target_perubahan'      => null,
                    ];
                }
            }

            // R-21a: Duplikasi Teknis Kode RUP (TINGGI)
            $kRup = $pkg['kode_rup'];
            if (($kRup !== '' && ($kodeRupCounts[$kRup] ?? 0) > 1) || ! empty($pkg['is_scientific_truncated'])) {
                $pkgViolations[] = [
                    'rule_id'               => 'R-21a',
                    'severity'              => 'TINGGI',
                    'kategori'              => 'Integritas Data Teknis',
                    'regulasi'              => 'Validitas Data SIRUP Nasional (INAPROC)',
                    'deskripsi_pelanggaran' => 'Duplikasi Teknis Kode RUP atau Kerusakan Format Digit (.0 / 00 scientific notation).',
                    'rekomendasi_perbaikan' => 'Pastikan ekspor data memperlakukan kode_rup sebagai string murni dan hapus rekaman ganda.',
                    'target_perubahan'      => null,
                ];
            }

            // R-21b: Indikasi Double Counting Paket Identik Lintas Kode RUP (TINGGI)
            $sig = md5($pkg['nama_opd_clean'] . '|' . $pkg['nama_paket_clean'] . '|' . $pkg['sumber_dana'] . '|' . $pkg['total_nilai']);
            if (isset($opdIdenticalPackages[$sig]) && count($opdIdenticalPackages[$sig]) > 1 && $pkg['total_nilai'] >= 50000000.0) {
                $pkgViolations[] = [
                    'rule_id'               => 'R-21b',
                    'severity'              => 'TINGGI',
                    'kategori'              => 'Double Counting Pagu',
                    'regulasi'              => 'Prinsip Akurasi Anggaran SIRUP',
                    'deskripsi_pelanggaran' => 'Indikasi Double Counting Paket Identik (>= Rp50 Juta): Nama, sumber dana, dan pagu sama persis dengan kode RUP berbeda.',
                    'rekomendasi_perbaikan' => 'Verifikasi apakah merupakan double input sub-kegiatan; hapus entri ganda di SIRUP.',
                    'target_perubahan'      => null,
                ];
            }

            // -------------------------------------------------------------
            // KELOMPOK F: Afirmasi UMK (R-24) & PDN (R-25)
            // -------------------------------------------------------------
            // R-24: Afirmasi Pencadangan Usaha Mikro/Kecil & Batasan Usaha Non-Kecil
            // Sesuai Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 Perlem LKPP 3/2026
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true)) {
                $objekPkg = (string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '');
                $isPppkOrIuranOrTunda = RupTaxonomy::match($objekPkg, RupTaxonomy::DICT_PPPK_DAN_IURAN_ASN) || str_contains(strtolower($objekPkg), 'tunda bayar');

                if (! $isPppkOrIuranOrTunda) {
                    $isUtilitas = RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_01_DIKECUALIKAN);
                    $isImpor    = RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_10_TEKNOLOGI_TINGGI_IMPOR);
                    $isKonsultKonstruksi   = RupTaxonomy::isKonsultansiKonstruksi($pkg);
                    $isPekerjaanKonstruksi = in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true);

                    if ($isKonsultKonstruksi) {
                        // Kasus A: Jasa Konsultansi Konstruksi (Pasal 4 ayat (2) Perlem LKPP 3/2026)
                        // Batas Usaha Kecil: <= Rp1.000.000.000 (Rp1 Miliar)
                        if ($pkg['total_nilai'] <= 1000000000.0 && $pkg['status_umk'] !== 'Ya' && ! $isUtilitas && ! $isImpor) {
                            $pkgViolations[] = [
                                'rule_id'               => 'R-24',
                                'severity'              => 'TINGGI',
                                'kategori'              => 'Pencadangan UMK Konsultansi Konstruksi',
                                'regulasi'              => 'Pasal 4 ayat (2) huruf a Perlem LKPP 3/2026 jo. Pasal 65 Perpres 46/2025',
                                'deskripsi_pelanggaran' => 'Paket Jasa Konsultansi Konstruksi bernilai <= Rp1 Miliar wajib dicadangkan untuk Badan Usaha Kualifikasi Usaha Kecil (status_umk = Ya).',
                                'rekomendasi_perbaikan' => 'Ubah status_umk menjadi Ya untuk paket Jasa Konsultansi Konstruksi bernilai sampai dengan Rp1.000.000.000 sesuai Pasal 4 ayat (2) huruf a Perlem LKPP 3/2026.',
                                'target_perubahan'      => ['status_umk_saran' => 'Ya'],
                            ];
                        } elseif ($pkg['total_nilai'] > 1000000000.0 && $pkg['status_umk'] === 'Ya') {
                            $segmenUsaha = $pkg['total_nilai'] <= 2500000000.0 ? 'Kualifikasi Usaha Menengah (Rp1 M - Rp2,5 M)' : 'Kualifikasi Usaha Besar (> Rp2,5 M)';
                            $pkgViolations[] = [
                                'rule_id'               => 'R-24',
                                'severity'              => 'TINGGI',
                                'kategori'              => 'Batasan Usaha Konsultansi Konstruksi Non-Kecil',
                                'regulasi'              => 'Pasal 4 ayat (2) huruf b & c Perlem LKPP 3/2026',
                                'deskripsi_pelanggaran' => "Paket Jasa Konsultansi Konstruksi bernilai > Rp1 Miliar (Rp " . number_format($pkg['total_nilai'], 0, ',', '.') . ") diperuntukkan bagi {$segmenUsaha}, sehingga status_umk seharusnya 'Tidak'.",
                                'rekomendasi_perbaikan' => "Ubah status_umk menjadi Tidak karena menurut Pasal 4 ayat (2) Perlem LKPP 3/2026, Jasa Konsultansi Konstruksi > Rp1 Miliar diperuntukkan bagi {$segmenUsaha}.",
                                'target_perubahan'      => [
                                    'status_umk_saran' => 'Tidak',
                                    'segmentasi_usaha' => $segmenUsaha,
                                ],
                            ];
                        }
                    } else {
                        // Kasus B: Pengadaan Umum (Barang / Jasa Lainnya / Pekerjaan Konstruksi / Konsultansi Non-Konstruksi)
                        // 1) Paket <= 15 M non-UMK
                        if ($pkg['total_nilai'] <= 15000000000.0 && $pkg['status_umk'] !== 'Ya' && ! $isUtilitas && ! $isImpor) {
                            $targetPerubahan = ['status_umk_saran' => 'Ya'];
                            $rekomendasiSub = '';
                            if ($isPekerjaanKonstruksi) {
                                $subkual = RupTaxonomy::getSubkualifikasiKonstruksi($pkg['total_nilai']);
                                $targetPerubahan['subkualifikasi_konstruksi'] = $subkual['subkualifikasi'];
                                $targetPerubahan['afirmasi_domisili'] = $subkual['afirmasi_domisili'];
                                $rekomendasiSub = " ({$subkual['subkualifikasi']} — {$subkual['afirmasi_domisili']})";
                            }

                            $pkgViolations[] = [
                                'rule_id'               => 'R-24',
                                'severity'              => 'TINGGI',
                                'kategori'              => 'Pencadangan UMK-Koperasi',
                                'regulasi'              => 'Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 ayat (1) Perlem LKPP 3/2026',
                                'deskripsi_pelanggaran' => 'Paket Penyedia bernilai <= Rp15 Miliar wajib dicadangkan untuk Usaha Mikro, Kecil, dan Koperasi (status_umk = Ya).' . $rekomendasiSub,
                                'rekomendasi_perbaikan' => 'Ubah status_umk menjadi Ya, kecuali ada justifikasi teknis khusus yang tidak dapat dipenuhi UMK.' . ($isPekerjaanKonstruksi ? " Prioritaskan domisili penyedia sesuai subkualifikasi ({$targetPerubahan['subkualifikasi_konstruksi']})." : ''),
                                'target_perubahan'      => $targetPerubahan,
                            ];
                        }

                        // 2) Paket Kontrak Tunggal > 15 M bertanda UMK = Ya
                        $isTenderKontrakTunggal = in_array('Tender', $pkg['metode_arr'] ?? [], true) || in_array('Tender Cepat', $pkg['metode_arr'] ?? [], true) || in_array('Seleksi', $pkg['metode_arr'] ?? [], true);
                        if ($isTenderKontrakTunggal && $pkg['total_nilai'] > 15000000000.0 && $pkg['status_umk'] === 'Ya') {
                            $targetPerubahan = ['status_umk_saran' => 'Tidak'];
                            $subTeks = '';
                            if ($isPekerjaanKonstruksi) {
                                $subkual = RupTaxonomy::getSubkualifikasiKonstruksi($pkg['total_nilai']);
                                $targetPerubahan['subkualifikasi_konstruksi'] = $subkual['subkualifikasi'];
                                $targetPerubahan['afirmasi_domisili'] = $subkual['afirmasi_domisili'];
                                $subTeks = " ({$subkual['subkualifikasi']})";
                            }

                            $pkgViolations[] = [
                                'rule_id'               => 'R-24',
                                'severity'              => 'TINGGI',
                                'kategori'              => 'Batasan Usaha Non-Kecil',
                                'regulasi'              => 'Pasal 65 ayat (4) Perpres 46/2025 jo. Pasal 4 ayat (1) Perlem LKPP 3/2026',
                                'deskripsi_pelanggaran' => 'Paket Tender/Seleksi > Rp15 Miliar diperuntukkan bagi Usaha Non-Kecil (status_umk seharusnya Tidak).' . $subTeks,
                                'rekomendasi_perbaikan' => 'Ubah status_umk menjadi Tidak untuk paket kontrak tunggal bernilai di atas Rp15 Miliar.' . ($isPekerjaanKonstruksi && $pkg['total_nilai'] > 25000000000.0 ? ' Cantumkan klausul wajib subkontrak kepada Usaha Kecil sesuai Lampiran II Angka 2.3.8 Perlem LKPP 3/2026.' : ''),
                                'target_perubahan'      => $targetPerubahan,
                            ];
                        }
                    }
                }
            }

            // R-25: Verifikasi PDN & Evaluasi Produk Impor (RENDAH)
            if ($pkg['cara_pengadaan'] === 'Penyedia' && ! in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true)) {
                $isPDN = $pkg['produk_dalam_negeri'] === 'Ya';
                $isHighTech = RupTaxonomy::match((string) $pkg['nama_paket_objek'], RupTaxonomy::DICT_10_TEKNOLOGI_TINGGI_IMPOR);
                if (! $isPDN || ($pkg['status_umk'] === 'Ya' && $pkg['total_nilai'] > 200000000 && $isHighTech)) {
                    $pkgViolations[] = [
                        'rule_id'               => 'R-25',
                        'severity'              => 'RENDAH',
                        'kategori'              => 'Afirmasi PDN & Impor',
                        'regulasi'              => 'Pasal 66 & Pasal 72A Perpres 46/2025 jo. Perlem LKPP 3/2026',
                        'deskripsi_pelanggaran' => 'Verifikasi Kewajiban PDN: Paket belum bertanda PDN Ya atau instrumen berteknologi tinggi dicap UMK/PDN otomatis.',
                        'rekomendasi_perbaikan' => 'Terapkan urutan prioritas pembelian PDN ber-TKDN >= 25% sebelum memilih produk impor.',
                        'target_perubahan'      => ['produk_dalam_negeri_saran' => 'Ya'],
                    ];
                }
            }

            // Tentukan max_severity dan simpan temuan
            $severityOrder = ['AMAN' => 0, 'RENDAH' => 1, 'SEDANG' => 2, 'TINGGI' => 3, 'KRITIS' => 4];
            $currentMaxSeverity = 'AMAN';
            $maxWeight = 0;

            foreach ($pkgViolations as $v) {
                $w = $severityOrder[$v['severity']] ?? 0;
                if ($w > $maxWeight) {
                    $maxWeight = $w;
                    $currentMaxSeverity = $v['severity'];
                }
                $v['row_index_csv'] = $pkg['row_index_csv'];
                $allViolations[] = $v;
            }

            $pkg['max_severity']    = $currentMaxSeverity;
            $pkg['violation_count'] = count($pkgViolations);
        }
        unset($pkg);

        // =====================================================================
        // PASS 3: Evaluasi Macro Rules per OPD & RCI v3.1 Calculation
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
}
