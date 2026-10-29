<?php

namespace App\Services\RupAudit;

class RupRciCalculator
{
    /**
     * Hitung formula matematis RCI v3.1 secara langsung.
     */
    public static function calculateFormula(
        float $totalPagu,
        int $totalPaket,
        float $paguKritis = 0.0,
        float $paguTinggi = 0.0,
        float $paguSedang = 0.0,
        int $cntKritis = 0,
        int $cntTinggi = 0,
        int $cntSedang = 0,
        int $cntRendah = 0,
        float $penaltiMakro = 0.0
    ): array {
        if ($totalPaket <= 0 || $totalPagu <= 0.0) {
            return [
                'e_pagu'    => 0.0,
                'e_paket'   => 0.0,
                'rci_score' => 0.0,
                'risk_zone' => 'MERAH',
            ];
        }

        $bobotPagu = $paguKritis + (0.50 * $paguTinggi) + (0.15 * $paguSedang);
        $ePagu     = min(100.0, ($bobotPagu / $totalPagu) * 100.0);

        $bobotPaket = (10.0 * $cntKritis) + (5.0 * $cntTinggi) + (2.0 * $cntSedang) + (0.5 * $cntRendah);
        $ePaket     = min(100.0, ($bobotPaket / $totalPaket) * 10.0);

        $deduction = (0.50 * $ePagu) + (0.35 * $ePaket) + $penaltiMakro;
        $skorRci   = round(max(0.0, 100.0 - $deduction), 2);

        $predikat = 'HIJAU';
        if ($skorRci < 50.00) {
            $predikat = 'MERAH';
        } elseif ($skorRci < 70.00) {
            $predikat = 'ORANYE';
        } elseif ($skorRci < 85.00) {
            $predikat = 'KUNING';
        }

        return [
            'e_pagu'    => round($ePagu, 2),
            'e_paket'   => round($ePaket, 2),
            'rci_score' => $skorRci,
            'risk_zone' => $predikat,
        ];
    }

    /**
     * Menghitung ringkasan kepatuhan dan skor RCI v3.1 per OPD.
     *
     * @param array<int, array> $packages
     * @param array<int, array> $clusters
     * @return array<string, array>
     */
    public function calculateOpdSummaries(array $packages, array $clusters): array
    {
        $opdGroups = [];

        foreach ($packages as $pkg) {
            $opd = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';
            if (! isset($opdGroups[$opd])) {
                $opdGroups[$opd] = [
                    'kode_satker_sipd'              => $pkg['kode_satker_sipd'] ?? '',
                    'nama_opd_clean'                => $opd,
                    'total_paket'                   => 0,
                    'total_pagu'                    => 0.0,
                    'pagu_penyedia'                 => 0.0,
                    'pagu_swakelola'                => 0.0,
                    'pagu_dpa'                      => 0.0,
                    'pagu_penyedia_non_dikecualikan'=> 0.0,
                    'pagu_umk_penyedia'             => 0.0,
                    'pagu_penyedia_le15m'           => 0.0,
                    'pagu_umk_penyedia_le15m'       => 0.0,
                    'pagu_umk_total_rup'            => 0.0,
                    'pagu_epurchasing'              => 0.0,
                    'pagu_penunjukan_langsung'      => 0.0,
                    'pagu_pdn'                      => 0.0,
                    'temuan_kritis'                 => 0,
                    'temuan_tinggi'                 => 0,
                    'temuan_sedang'                 => 0,
                    'temuan_rendah'                 => 0,
                    'pagu_kritis'                   => 0.0,
                    'pagu_tinggi'                   => 0.0,
                    'pagu_sedang'                   => 0.0,
                    'pagu_rendah'                   => 0.0,
                    'total_konstruksi'              => 0.0,
                    'count_pengawasan_konsultansi'  => 0,
                ];
            }

            $g = &$opdGroups[$opd];
            $g['total_paket']++;
            $val = (float) $pkg['total_nilai'];
            $g['total_pagu'] += $val;

            if (! empty($pkg['pagu_pengadaan_dpa']) && $pkg['pagu_pengadaan_dpa'] > $g['pagu_dpa']) {
                $g['pagu_dpa'] = (float) $pkg['pagu_pengadaan_dpa'];
            }

            $isPenyedia = ($pkg['cara_pengadaan'] === 'Penyedia');
            if ($isPenyedia) {
                $g['pagu_penyedia'] += $val;
                $isDikecualikan = in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true);
                if (! $isDikecualikan) {
                    $g['pagu_penyedia_non_dikecualikan'] += $val;
                    if ($pkg['status_umk'] === 'Ya') {
                        $g['pagu_umk_penyedia'] += $val;
                    }

                    $isKonsultansiKonstruksi = RupTaxonomy::isKonsultansiKonstruksi($pkg);
                    $objekPkg = (string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '');
                    $isPppkOrIuranOrTunda = RupTaxonomy::match($objekPkg, RupTaxonomy::DICT_PPPK_DAN_IURAN_ASN) || str_contains(strtolower($objekPkg), 'tunda bayar');

                    // Sesuai Pasal 4 ayat (2) Perlem LKPP 3/2026, Jasa Konsultansi Konstruksi > Rp1 Miliar
                    // wajib untuk Usaha Menengah/Besar, serta PPPK / Iuran ASN / Tunda Bayar,
                    // sehingga dikecualikan dari penyebut eligible UMK (R-26.1).
                    if (! $isPppkOrIuranOrTunda) {
                        $isEligibleUmkLe15m = $isKonsultansiKonstruksi ? ($val <= 1000000000.0) : ($val <= 15000000000.0);

                        if ($isEligibleUmkLe15m) {
                            $g['pagu_penyedia_le15m'] += $val;
                            if ($pkg['status_umk'] === 'Ya') {
                                $g['pagu_umk_penyedia_le15m'] += $val;
                            }
                        }
                    }
                }

                if (in_array('E-Purchasing', $pkg['metode_arr'] ?? [], true)) {
                    $g['pagu_epurchasing'] += $val;
                }
                if (in_array('Penunjukan Langsung', $pkg['metode_arr'] ?? [], true)) {
                    $g['pagu_penunjukan_langsung'] += $val;
                }
                if ($pkg['produk_dalam_negeri'] === 'Ya') {
                    $g['pagu_pdn'] += $val;
                }
            } else {
                $g['pagu_swakelola'] += $val;
            }

            if ($pkg['status_umk'] === 'Ya') {
                $g['pagu_umk_total_rup'] += $val;
            }

            // Konstruksi & Pengawasan check (R-32)
            if (in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true)) {
                $g['total_konstruksi'] += $val;
            }
            if (in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true)) {
                $cLower = strtolower((string) $pkg['nama_paket_clean']);
                if (preg_match('/(pengawasan|perencanaan|manajemen konstruksi|ded)/', $cLower)) {
                    $g['count_pengawasan_konsultansi']++;
                }
            }

            // Severity counts & Pagu deduplication by max_severity
            switch ($pkg['max_severity']) {
                case 'KRITIS':
                    $g['temuan_kritis']++;
                    $g['pagu_kritis'] += $val;
                    break;
                case 'TINGGI':
                    $g['temuan_tinggi']++;
                    $g['pagu_tinggi'] += $val;
                    break;
                case 'SEDANG':
                    $g['temuan_sedang']++;
                    $g['pagu_sedang'] += $val;
                    break;
                case 'RENDAH':
                    $g['temuan_rendah']++;
                    $g['pagu_rendah'] += $val;
                    break;
            }
        }
        unset($g);

        $results = [];

        foreach ($opdGroups as $opd => $data) {
            $totalPaket = $data['total_paket'];
            $paguValid  = max(0.0, $data['total_pagu']);

            // 1. Zero-division guard
            if ($totalPaket === 0 || $paguValid <= 0.0) {
                $results[$opd] = array_merge($data, [
                    'skor_rci'                     => 0.00,
                    'predikat_risiko'              => 'MERAH',
                    'persen_cakupan_dpa'           => 0.00,
                    'persen_epurchasing'           => 0.00,
                    'persen_umk_penyedia'          => 0.00,
                    'persen_umk_eligible_le15m'    => 0.00,
                    'persen_umk_total_rup'         => 0.00,
                    'persen_pdn'                   => 0.00,
                    'rasio_penunjukan_langsung'    => 0.00,
                    'pagu_bermasalah_kritis_tinggi'=> 0.00,
                    'macro_flags'                  => ['R-01'],
                    'created_at'                   => date('Y-m-d H:i:s'),
                ]);
                continue;
            }

            // 2. Hitung persentase indikator makro
            $persenEpurchasing = $data['pagu_penyedia'] > 0 ? round(($data['pagu_epurchasing'] / $data['pagu_penyedia']) * 100, 2) : 0.00;
            $persenPlg         = $data['pagu_penyedia'] > 0 ? round(($data['pagu_penunjukan_langsung'] / $data['pagu_penyedia']) * 100, 2) : 0.00;
            $persenPdn         = $data['pagu_penyedia'] > 0 ? round(($data['pagu_pdn'] / $data['pagu_penyedia']) * 100, 2) : 0.00;

            $persenUmkPenyedia = $data['pagu_penyedia_non_dikecualikan'] > 0 ? round(($data['pagu_umk_penyedia'] / $data['pagu_penyedia_non_dikecualikan']) * 100, 2) : 0.00;
            $persenUmkLe15m    = $data['pagu_penyedia_le15m'] > 0 ? round(($data['pagu_umk_penyedia_le15m'] / $data['pagu_penyedia_le15m']) * 100, 2) : 100.00;
            $persenUmkTotalRup = round(($data['pagu_umk_total_rup'] / $paguValid) * 100, 2);

            $persenCakupanDpa  = 100.00;
            if ($data['pagu_dpa'] > 0) {
                $persenCakupanDpa = round(($data['total_pagu'] / $data['pagu_dpa']) * 100, 2);
            }

            // 3. Evaluasi Macro Flags (R-26.1, R-26.2, R-26.3, R-26.4, R-32)
            $macroFlags = [];
            $penaltiMakro = 0.0;

            // R-26.1: Dual-Check UMK (Harus gagal di kedua syarat untuk memicu penalti)
            if ($persenUmkPenyedia < 40.0 && $persenUmkLe15m < 80.0) {
                $macroFlags[] = 'R-26.1';
                $penaltiMakro += 15.0;
            }

            // R-26.2: Rasio Penunjukan Langsung > 5%
            if ($persenPlg > 5.0) {
                $macroFlags[] = 'R-26.2';
                $penaltiMakro += 10.0;
            }

            // R-26.3: Rasio E-Purchasing < 30%
            if ($persenEpurchasing < 30.0 && $data['pagu_penyedia'] > 0) {
                $macroFlags[] = 'R-26.3';
                $penaltiMakro += 5.0;
            }

            // R-26.4: Cakupan DPA < 95% jika pagu DPA tersedia
            if ($data['pagu_dpa'] > 0 && $persenCakupanDpa < 95.0) {
                $macroFlags[] = 'R-26.4';
                $penaltiMakro += 15.0;
            }

            // R-32: Konstruksi > 400 Juta tanpa konsultan pengawasan
            if ($data['total_konstruksi'] > 400000000.0 && $data['count_pengawasan_konsultansi'] === 0) {
                $macroFlags[] = 'R-32';
            }

            // 4. Hitung Rasio Eksposur Pagu (E_pagu)
            $bobotPagu = $data['pagu_kritis'] + (0.50 * $data['pagu_tinggi']) + (0.15 * $data['pagu_sedang']);
            $ePagu = min(100.0, ($bobotPagu / $paguValid) * 100.0);

            // 5. Hitung Rasio Kepadatan Paket (E_paket)
            $bobotPaket = (10.0 * $data['temuan_kritis']) + (5.0 * $data['temuan_tinggi']) + (2.0 * $data['temuan_sedang']) + (0.5 * $data['temuan_rendah']);
            $ePaket = min(100.0, ($bobotPaket / $totalPaket) * 10.0);

            // 6. Skor Akhir RCI v3.1
            $deduction = (0.50 * $ePagu) + (0.35 * $ePaket) + $penaltiMakro;
            $skorRci   = round(max(0.0, 100.0 - $deduction), 2);

            // 7. Zona Predikat
            $predikat = 'HIJAU';
            if ($skorRci < 50.00) {
                $predikat = 'MERAH';
            } elseif ($skorRci < 70.00) {
                $predikat = 'ORANYE';
            } elseif ($skorRci < 85.00) {
                $predikat = 'KUNING';
            }

            $paguBermasalahKritisTinggi = $data['pagu_kritis'] + $data['pagu_tinggi'];

            $results[$opd] = [
                'kode_satker_sipd'              => $data['kode_satker_sipd'],
                'nama_opd_clean'                => $opd,
                'total_paket'                   => $totalPaket,
                'total_pagu'                    => $data['total_pagu'],
                'pagu_penyedia'                 => $data['pagu_penyedia'],
                'pagu_swakelola'                => $data['pagu_swakelola'],
                'pagu_dpa'                      => $data['pagu_dpa'],
                'persen_cakupan_dpa'           => $persenCakupanDpa,
                'persen_epurchasing'            => $persenEpurchasing,
                'persen_umk_penyedia'          => $persenUmkPenyedia,
                'persen_umk_eligible_le15m'    => $persenUmkLe15m,
                'persen_umk_total_rup'         => $persenUmkTotalRup,
                'persen_pdn'                   => $persenPdn,
                'rasio_penunjukan_langsung'    => $persenPlg,
                'temuan_kritis'                 => $data['temuan_kritis'],
                'temuan_tinggi'                 => $data['temuan_tinggi'],
                'temuan_sedang'                 => $data['temuan_sedang'],
                'temuan_rendah'                 => $data['temuan_rendah'],
                'pagu_bermasalah_kritis_tinggi'=> $paguBermasalahKritisTinggi,
                'skor_rci'                     => $skorRci,
                'predikat_risiko'              => $predikat,
                'macro_flags'                  => json_encode($macroFlags),
                'created_at'                   => date('Y-m-d H:i:s'),
            ];
        }

        // Urutkan leaderboard dari skor RCI terendah (paling berisiko)
        uasort($results, static fn($a, $b) => $a['skor_rci'] <=> $b['skor_rci']);

        return $results;
    }

    /**
     * Menghitung ringkasan agregat tingkat institusi / pemerintah daerah.
     */
    public function calculateInstitutionSummary(array $opdSummaries, array $packages): array
    {
        $totalPagu      = 0.0;
        $paguPenyedia   = 0.0;
        $paguSwakelola  = 0.0;
        $paguEpurch     = 0.0;
        $paguPdn        = 0.0;
        $paguUmkNonDikecualikan = 0.0;
        $paguPenyediaNonDikecualikan = 0.0;
        $paguUmkLe15m   = 0.0;
        $paguPenyediaLe15m = 0.0;
        $paguUmkTotal   = 0.0;

        $distribusiTemuan = [
            'kritis' => ['jumlah_paket' => 0, 'total_pagu_terdampak' => 0.0],
            'tinggi' => ['jumlah_paket' => 0, 'total_pagu_terdampak' => 0.0],
            'sedang' => ['jumlah_paket' => 0, 'total_pagu_terdampak' => 0.0],
            'rendah' => ['jumlah_paket' => 0, 'total_pagu_terdampak' => 0.0],
        ];

        foreach ($packages as $pkg) {
            $val = (float) $pkg['total_nilai'];
            $totalPagu += $val;

            if ($pkg['cara_pengadaan'] === 'Penyedia') {
                $paguPenyedia += $val;
                $isDikecualikan = in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true);
                if (! $isDikecualikan) {
                    $paguPenyediaNonDikecualikan += $val;
                    if ($pkg['status_umk'] === 'Ya') {
                        $paguUmkNonDikecualikan += $val;
                    }
                    $isKonsultansiKonstruksi = RupTaxonomy::isKonsultansiKonstruksi($pkg);
                    $isEligibleUmkLe15m = $isKonsultansiKonstruksi ? ($val <= 1000000000.0) : ($val <= 15000000000.0);

                    if ($isEligibleUmkLe15m) {
                        $paguPenyediaLe15m += $val;
                        if ($pkg['status_umk'] === 'Ya') {
                            $paguUmkLe15m += $val;
                        }
                    }
                }

                if (in_array('E-Purchasing', $pkg['metode_arr'] ?? [], true)) {
                    $paguEpurch += $val;
                }
                if ($pkg['produk_dalam_negeri'] === 'Ya') {
                    $paguPdn += $val;
                }
            } else {
                $paguSwakelola += $val;
            }

            if ($pkg['status_umk'] === 'Ya') {
                $paguUmkTotal += $val;
            }

            switch ($pkg['max_severity']) {
                case 'KRITIS':
                    $distribusiTemuan['kritis']['jumlah_paket']++;
                    $distribusiTemuan['kritis']['total_pagu_terdampak'] += $val;
                    break;
                case 'TINGGI':
                    $distribusiTemuan['tinggi']['jumlah_paket']++;
                    $distribusiTemuan['tinggi']['total_pagu_terdampak'] += $val;
                    break;
                case 'SEDANG':
                    $distribusiTemuan['sedang']['jumlah_paket']++;
                    $distribusiTemuan['sedang']['total_pagu_terdampak'] += $val;
                    break;
                case 'RENDAH':
                    $distribusiTemuan['rendah']['jumlah_paket']++;
                    $distribusiTemuan['rendah']['total_pagu_terdampak'] += $val;
                    break;
            }
        }

        $scores = array_column($opdSummaries, 'skor_rci');
        $avgRci = ! empty($scores) ? round(array_sum($scores) / count($scores), 2) : 0.00;

        return [
            'pagu_penyedia'                                => $paguPenyedia,
            'pagu_swakelola'                               => $paguSwakelola,
            'persen_e_purchasing_penyedia'                 => $paguPenyedia > 0 ? round(($paguEpurch / $paguPenyedia) * 100, 2) : 0.00,
            'persen_pdn_penyedia'                          => $paguPenyedia > 0 ? round(($paguPdn / $paguPenyedia) * 100, 2) : 0.00,
            'persen_umk_terhadap_penyedia_non_dikecualikan'=> $paguPenyediaNonDikecualikan > 0 ? round(($paguUmkNonDikecualikan / $paguPenyediaNonDikecualikan) * 100, 2) : 0.00,
            'persen_umk_eligible_le15m'                    => $paguPenyediaLe15m > 0 ? round(($paguUmkLe15m / $paguPenyediaLe15m) * 100, 2) : 100.00,
            'persen_umk_terhadap_total_rup'                => $totalPagu > 0 ? round(($paguUmkTotal / $totalPagu) * 100, 2) : 0.00,
            'distribusi_temuan_unik'                       => $distribusiTemuan,
            'rata_rata_rci_instansi'                       => $avgRci,
        ];
    }
}
