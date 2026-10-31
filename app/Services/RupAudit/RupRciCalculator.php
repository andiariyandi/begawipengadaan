<?php

namespace App\Services\RupAudit;

class RupRciCalculator
{
    /**
     * Hitung formula matematis RCI v3.1 secara langsung (retained for test/backward compatibility).
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
     * Menghitung RUP Risk Profile dan ringkasan per OPD.
     * Mengelompokkan temuan berdasarkan status v4.0 (POTENSI_TIDAK_SESUAI, PERLU_VERIFIKASI, SINYAL_RISIKO, ANOMALI_DATA).
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
                    // Status temuan v4.0
                    'count_potensi_tidak_sesuai'    => 0,
                    'pagu_potensi_tidak_sesuai'     => 0.0,
                    'count_perlu_verifikasi'        => 0,
                    'pagu_perlu_verifikasi'         => 0.0,
                    'count_sinyal_risiko'           => 0,
                    'pagu_sinyal_risiko'            => 0.0,
                    'count_anomali_data'            => 0,
                    'pagu_anomali_data'             => 0.0,
                    'count_informatif'              => 0,
                    'total_konstruksi'              => 0.0,
                    'count_pengawasan_konsultansi'  => 0,
                    'source_funding_counts'         => [],
                    'metode_counts'                 => [],
                    'jenis_counts'                  => [],
                ];
            }

            $g = &$opdGroups[$opd];
            $g['total_paket']++;
            $val = (float) ($pkg['total_nilai'] ?? 0.0);
            $g['total_pagu'] += $val;

            $cara   = $pkg['cara_pengadaan'] ?? 'Penyedia';
            $metode = $pkg['metode_pengadaan'] ?? '-';
            $jenis  = $pkg['jenis_pengadaan'] ?? '-';
            $sumber = $pkg['source_funding_profile'] ?? $pkg['sumber_dana'] ?? 'APBD';

            $g['metode_counts'][$metode] = ($g['metode_counts'][$metode] ?? 0) + 1;
            $g['jenis_counts'][$jenis]   = ($g['jenis_counts'][$jenis] ?? 0) + 1;
            $g['source_funding_counts'][$sumber] = ($g['source_funding_counts'][$sumber] ?? 0) + 1;

            if (! empty($pkg['pagu_pengadaan_dpa']) && $pkg['pagu_pengadaan_dpa'] > $g['pagu_dpa']) {
                $g['pagu_dpa'] = (float) $pkg['pagu_pengadaan_dpa'];
            }

            $isPenyedia = ($cara === 'Penyedia');
            if ($isPenyedia) {
                $g['pagu_penyedia'] += $val;
                $isDikecualikan = in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true);
                if (! $isDikecualikan) {
                    $g['pagu_penyedia_non_dikecualikan'] += $val;
                    if (($pkg['status_umk'] ?? '') === 'Ya') {
                        $g['pagu_umk_penyedia'] += $val;
                    }

                    if ($val <= 15000000000.0) {
                        $g['pagu_penyedia_le15m'] += $val;
                        if (($pkg['status_umk'] ?? '') === 'Ya') {
                            $g['pagu_umk_penyedia_le15m'] += $val;
                        }
                    }
                }

                if (in_array('E-Purchasing', $pkg['metode_arr'] ?? [], true)) {
                    $g['pagu_epurchasing'] += $val;
                }
                if (in_array('Penunjukan Langsung', $pkg['metode_arr'] ?? [], true)) {
                    $g['pagu_penunjukan_langsung'] += $val;
                }
                if (($pkg['produk_dalam_negeri'] ?? '') === 'Ya') {
                    $g['pagu_pdn'] += $val;
                }
            } else {
                $g['pagu_swakelola'] += $val;
            }

            if (($pkg['status_umk'] ?? '') === 'Ya') {
                $g['pagu_umk_total_rup'] += $val;
            }

            if (in_array('Pekerjaan Konstruksi', $pkg['jenis_arr'] ?? [], true)) {
                $g['total_konstruksi'] += $val;
            }
            if (in_array('Jasa Konsultansi', $pkg['jenis_arr'] ?? [], true)) {
                $cLower = strtolower((string) ($pkg['nama_paket_clean'] ?? ''));
                if (preg_match('/(pengawasan|perencanaan|manajemen konstruksi|ded)/', $cLower)) {
                    $g['count_pengawasan_konsultansi']++;
                }
            }

            // Severity counts
            switch ($pkg['max_severity'] ?? 'AMAN') {
                case 'KRITIS':
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

        // Agregasi status temuan dari array paket jika memiliki violation
        foreach ($packages as $pkg) {
            $opd = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';
            if (! isset($opdGroups[$opd])) {
                continue;
            }
            $val = (float) ($pkg['total_nilai'] ?? 0.0);
            $maxSev = $pkg['max_severity'] ?? 'AMAN';
            if ($maxSev === 'TINGGI' || $maxSev === 'KRITIS') {
                $opdGroups[$opd]['count_potensi_tidak_sesuai']++;
                $opdGroups[$opd]['pagu_potensi_tidak_sesuai'] += $val;
            } elseif ($maxSev === 'SEDANG') {
                $opdGroups[$opd]['count_perlu_verifikasi']++;
                $opdGroups[$opd]['pagu_perlu_verifikasi'] += $val;
            } elseif ($maxSev === 'RENDAH') {
                $opdGroups[$opd]['count_sinyal_risiko']++;
                $opdGroups[$opd]['pagu_sinyal_risiko'] += $val;
            }
        }

        $results = [];

        foreach ($opdGroups as $opd => $data) {
            $totalPaket = $data['total_paket'];
            $paguValid  = max(0.0, $data['total_pagu']);

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
                    'macro_flags'                  => json_encode(['R-01']),
                    'risk_profile_json'            => json_encode([]),
                    'created_at'                   => date('Y-m-d H:i:s'),
                ]);
                continue;
            }

            // Metrik indikator CSV
            $persenEpurchasing = $data['pagu_penyedia'] > 0 ? round(($data['pagu_epurchasing'] / $data['pagu_penyedia']) * 100, 2) : 0.00;
            $persenPlg         = $data['pagu_penyedia'] > 0 ? round(($data['pagu_penunjukan_langsung'] / $data['pagu_penyedia']) * 100, 2) : 0.00;
            $persenPdn         = $data['pagu_penyedia'] > 0 ? round(($data['pagu_pdn'] / $data['pagu_penyedia']) * 100, 2) : 0.00;
            $persenUmkPenyedia = $data['pagu_penyedia_non_dikecualikan'] > 0 ? round(($data['pagu_umk_penyedia'] / $data['pagu_penyedia_non_dikecualikan']) * 100, 2) : 0.00;
            $persenUmkLe15m    = $data['pagu_penyedia_le15m'] > 0 ? round(($data['pagu_umk_penyedia_le15m'] / $data['pagu_penyedia_le15m']) * 100, 2) : 100.00;
            $persenUmkTotalRup = round(($data['pagu_umk_total_rup'] / $paguValid) * 100, 2);
            $persenCakupanDpa  = $data['pagu_dpa'] > 0 ? round(($data['total_pagu'] / $data['pagu_dpa']) * 100, 2) : 100.00;

            // RUP Risk Attention Score (prioritas perhatian reviu, bukan kepatuhan absolut)
            // Menghilangkan penalti rasio 5%, 30%, 80%, dan DPA 95%
            $bobotPagu  = $data['pagu_potensi_tidak_sesuai'] + (0.40 * $data['pagu_perlu_verifikasi']) + (0.10 * $data['pagu_sinyal_risiko']);
            $ePagu      = min(100.0, ($bobotPagu / $paguValid) * 100.0);
            $bobotPaket = (5.0 * $data['count_potensi_tidak_sesuai']) + (2.0 * $data['count_perlu_verifikasi']) + (0.5 * $data['count_sinyal_risiko']);
            $ePaket     = min(100.0, ($bobotPaket / $totalPaket) * 10.0);
            $deduction  = (0.60 * $ePagu) + (0.40 * $ePaket);
            $skorRci    = round(max(0.0, 100.0 - $deduction), 2);

            $predikat = 'HIJAU';
            if ($skorRci < 50.00) {
                $predikat = 'MERAH';
            } elseif ($skorRci < 70.00) {
                $predikat = 'ORANYE';
            } elseif ($skorRci < 85.00) {
                $predikat = 'KUNING';
            }

            $paguBermasalahKritisTinggi = $data['pagu_potensi_tidak_sesuai'];

            $riskProfile = [
                'total_paket'                 => $totalPaket,
                'total_pagu'                  => $data['total_pagu'],
                'pagu_penyedia'               => $data['pagu_penyedia'],
                'pagu_swakelola'              => $data['pagu_swakelola'],
                'pagu_potensi_tidak_sesuai'   => $data['pagu_potensi_tidak_sesuai'],
                'count_potensi_tidak_sesuai'  => $data['count_potensi_tidak_sesuai'],
                'pagu_perlu_verifikasi'       => $data['pagu_perlu_verifikasi'],
                'count_perlu_verifikasi'      => $data['count_perlu_verifikasi'],
                'pagu_sinyal_risiko'          => $data['pagu_sinyal_risiko'],
                'count_sinyal_risiko'         => $data['count_sinyal_risiko'],
                'persen_umk_penyedia'         => $persenUmkPenyedia,
                'persen_pdn'                  => $persenPdn,
                'persen_epurchasing'          => $persenEpurchasing,
                'metode_composition'          => $data['metode_counts'],
                'jenis_composition'           => $data['jenis_counts'],
                'source_funding_composition'  => $data['source_funding_counts'],
            ];

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
                'macro_flags'                  => json_encode([]),
                'risk_profile_json'            => json_encode($riskProfile),
                'created_at'                   => date('Y-m-d H:i:s'),
            ];
        }

        // Urutkan OPD berdasarkan eksposur POTENSI_TIDAK_SESUAI tertinggi
        uasort($results, static function ($a, $b) {
            if ($a['pagu_bermasalah_kritis_tinggi'] !== $b['pagu_bermasalah_kritis_tinggi']) {
                return $b['pagu_bermasalah_kritis_tinggi'] <=> $a['pagu_bermasalah_kritis_tinggi'];
            }
            return $a['skor_rci'] <=> $b['skor_rci'];
        });

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
            'info'   => ['jumlah_paket' => 0, 'total_pagu_terdampak' => 0.0],
        ];

        $sourceFundingTotals = [];

        foreach ($packages as $pkg) {
            $val = (float) ($pkg['total_nilai'] ?? 0.0);
            $totalPagu += $val;
            $sumber = $pkg['source_funding_profile'] ?? $pkg['sumber_dana'] ?? 'APBD';
            $sourceFundingTotals[$sumber] = ($sourceFundingTotals[$sumber] ?? 0.0) + $val;

            if (($pkg['cara_pengadaan'] ?? 'Penyedia') === 'Penyedia') {
                $paguPenyedia += $val;
                $isDikecualikan = in_array('Dikecualikan', $pkg['metode_arr'] ?? [], true);
                if (! $isDikecualikan) {
                    $paguPenyediaNonDikecualikan += $val;
                    if (($pkg['status_umk'] ?? '') === 'Ya') {
                        $paguUmkNonDikecualikan += $val;
                    }
                    if ($val <= 15000000000.0) {
                        $paguPenyediaLe15m += $val;
                        if (($pkg['status_umk'] ?? '') === 'Ya') {
                            $paguUmkLe15m += $val;
                        }
                    }
                }

                if (in_array('E-Purchasing', $pkg['metode_arr'] ?? [], true)) {
                    $paguEpurch += $val;
                }
                if (($pkg['produk_dalam_negeri'] ?? '') === 'Ya') {
                    $paguPdn += $val;
                }
            } else {
                $paguSwakelola += $val;
            }

            if (($pkg['status_umk'] ?? '') === 'Ya') {
                $paguUmkTotal += $val;
            }

            $sev = strtolower((string) ($pkg['max_severity'] ?? 'info'));
            if ($sev === 'kritis') {
                $sev = 'tinggi'; // normalisasi ke tinggi pada v4.0
            }
            if (isset($distribusiTemuan[$sev])) {
                $distribusiTemuan[$sev]['jumlah_paket']++;
                $distribusiTemuan[$sev]['total_pagu_terdampak'] += $val;
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
            'source_funding_distribution'                  => $sourceFundingTotals,
            'rata_rata_rci_instansi'                       => $avgRci,
        ];
    }
}
