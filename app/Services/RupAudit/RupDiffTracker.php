<?php

namespace App\Services\RupAudit;

class RupDiffTracker
{
    /**
     * Membandingkan paket pada batch baru terhadap batch sebelumnya.
     * Menerapkan Two-Stage Diff Matching:
     * - Stage 1: Exact `kode_rup` matching (NEW, PERSISTENT, FIXED, CHANGED).
     * - Stage 2: Fallback matching untuk kode RUP baru dengan OPD + nama paket sama (CHANGED / Re-issued).
     *
     * @param array<int, array> $newPackages Reference ke paket batch baru
     * @param int|null $previousBatchId ID batch acuan sebelumnya
     * @return array Ringkasan statistik diff-tracking
     */
    public function compare(array &$newPackages, ?int $previousBatchId): array
    {
        if (! $previousBatchId || $previousBatchId <= 0) {
            foreach ($newPackages as &$pkg) {
                $pkg['diff_status'] = ($pkg['max_severity'] !== 'AMAN') ? 'NEW' : 'FIXED';
            }
            unset($pkg);

            return [
                'is_comparison_active'                    => false,
                'total_anomali_batch_lama'               => 0,
                'total_anomali_telah_diperbaiki_fixed'    => 0,
                'total_anomali_masih_bertahan_persistent'=> 0,
                'total_anomali_baru_muncul_new'          => count(array_filter($newPackages, static fn($p) => ($p['max_severity'] ?? 'AMAN') !== 'AMAN')),
                'total_packages_changed'                 => 0,
                'delta_skor_rci_instansi'                 => '+0.00',
            ];
        }

        $db = \Config\Database::connect();

        // Ambil data paket dari batch lama
        $prevPackages = $db->table('rup_audit_packages')
            ->select('id, kode_rup, nama_opd_clean, nama_paket_clean, max_severity, total_nilai, metode_pengadaan, jenis_pengadaan, sumber_dana, status_umk, produk_dalam_negeri')
            ->where('batch_id', $previousBatchId)
            ->get()->getResultArray();

        $prevByCode = [];
        $prevByName = [];
        $totalAnomaliLama = 0;

        foreach ($prevPackages as $prev) {
            $kRup = trim((string) $prev['kode_rup']);
            if ($kRup !== '') {
                $prevByCode[$kRup] = $prev;
            }
            $nameKey = md5(($prev['nama_opd_clean'] ?? '') . '|' . ($prev['nama_paket_clean'] ?? ''));
            $prevByName[$nameKey][] = $prev;

            if (($prev['max_severity'] ?? 'AMAN') !== 'AMAN') {
                $totalAnomaliLama++;
            }
        }

        $countPersistent = 0;
        $countNew        = 0;
        $countChanged    = 0;
        $countFixed      = 0;
        $matchedPrevCodes = [];

        // Stage 1: Exact kode_rup match
        foreach ($newPackages as &$pkg) {
            $kRup = trim((string) ($pkg['kode_rup'] ?? ''));
            $hasAnomaliNow = (($pkg['max_severity'] ?? 'AMAN') !== 'AMAN');

            if ($kRup !== '' && isset($prevByCode[$kRup])) {
                $matchedPrevCodes[$kRup] = true;
                $prev = $prevByCode[$kRup];
                $hadAnomaliBefore = (($prev['max_severity'] ?? 'AMAN') !== 'AMAN');

                // Cek apakah nilai atau atribut paket berubah
                $isValChanged = abs((float) $prev['total_nilai'] - (float) ($pkg['total_nilai'] ?? 0.0)) > 0.01;
                $isAttrChanged = ($prev['metode_pengadaan'] !== ($pkg['metode_pengadaan'] ?? ''))
                    || ($prev['jenis_pengadaan'] !== ($pkg['jenis_pengadaan'] ?? ''))
                    || ($prev['sumber_dana'] !== ($pkg['sumber_dana'] ?? ''));

                if ($isValChanged || $isAttrChanged) {
                    $pkg['diff_status'] = 'CHANGED';
                    $countChanged++;
                    if ($hasAnomaliNow) {
                        $countPersistent++;
                    }
                } elseif ($hadAnomaliBefore && $hasAnomaliNow) {
                    $pkg['diff_status'] = 'PERSISTENT';
                    $countPersistent++;
                } elseif (! $hadAnomaliBefore && $hasAnomaliNow) {
                    $pkg['diff_status'] = 'NEW';
                    $countNew++;
                } else {
                    $pkg['diff_status'] = 'FIXED';
                    if ($hadAnomaliBefore) {
                        $countFixed++;
                    }
                }
            } else {
                // Stage 2: Fallback matching berdasarkan OPD + nama_paket_clean
                $nameKey = md5(($pkg['nama_opd_clean'] ?? '') . '|' . ($pkg['nama_paket_clean'] ?? ''));
                $matchedIndex = null;
                if (isset($prevByName[$nameKey]) && ! empty($prevByName[$nameKey])) {
                    foreach ($prevByName[$nameKey] as $cIdx => $cand) {
                        $cCode = trim((string) ($cand['kode_rup'] ?? ''));
                        if (! isset($matchedPrevCodes[$cCode])) {
                            $matchedIndex = $cIdx;
                            break;
                        }
                    }
                }

                if ($matchedIndex !== null) {
                    $matchedOld = $prevByName[$nameKey][$matchedIndex];
                    unset($prevByName[$nameKey][$matchedIndex]); // consume
                    $oldCode = trim((string) ($matchedOld['kode_rup'] ?? ''));
                    if ($oldCode !== '') {
                        $matchedPrevCodes[$oldCode] = true;
                    }
                    $pkg['previous_kode_rup'] = $matchedOld['kode_rup'];
                    $pkg['diff_status'] = 'CHANGED';
                    $countChanged++;
                    if ($hasAnomaliNow) {
                        $countPersistent++;
                    }
                } else {
                    $pkg['diff_status'] = $hasAnomaliNow ? 'NEW' : 'FIXED';
                    if ($hasAnomaliNow) {
                        $countNew++;
                    }
                }
            }
        }
        unset($pkg);

        // Hitung paket batch lama yang terselesaikan atau dihapus
        foreach ($prevByCode as $k => $p) {
            if (($p['max_severity'] ?? 'AMAN') !== 'AMAN') {
                if (! isset($matchedPrevCodes[$k])) {
                    $countFixed++; // Paket bermasalah dihapus atau kode RUP diperbaiki
                }
            }
        }

        $prevBatchRow = $db->table('rup_audit_batches')->select('avg_rci_score')->where('id', $previousBatchId)->get()->getRowArray();
        $prevScore    = (float) ($prevBatchRow['avg_rci_score'] ?? 0.0);

        return [
            'is_comparison_active'                    => true,
            'previous_batch_id'                       => $previousBatchId,
            'total_anomali_batch_lama'               => $totalAnomaliLama,
            'total_anomali_telah_diperbaiki_fixed'    => $countFixed,
            'total_anomali_masih_bertahan_persistent'=> $countPersistent,
            'total_anomali_baru_muncul_new'          => $countNew,
            'total_packages_changed'                 => $countChanged,
            'prev_rci_score'                          => $prevScore,
        ];
    }

    /**
     * Membandingkan dua batch yang tersimpan di basis data (batch lama vs batch baru).
     */
    public static function compareBatches(int $batchIdOld, int $batchIdNew): array
    {
        $db = \Config\Database::connect();
        $newPackages = $db->table('rup_audit_packages')->where('batch_id', $batchIdNew)->get()->getResultArray();
        $tracker = new self();
        $summary = $tracker->compare($newPackages, $batchIdOld);
        return [
            'summary' => $summary,
            'stats'   => [
                'fixed'      => $summary['total_anomali_telah_diperbaiki_fixed'] ?? 0,
                'persistent' => $summary['total_anomali_masih_bertahan_persistent'] ?? 0,
                'new'        => $summary['total_anomali_baru_muncul_new'] ?? 0,
                'changed'    => $summary['total_packages_changed'] ?? 0,
            ],
            'packages' => $newPackages,
        ];
    }
}
