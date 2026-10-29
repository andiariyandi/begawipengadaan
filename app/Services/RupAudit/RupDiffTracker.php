<?php

namespace App\Services\RupAudit;

class RupDiffTracker
{
    /**
     * Membandingkan paket pada batch baru terhadap batch sebelumnya.
     * Mengisi atribut `diff_status` ('NEW', 'PERSISTENT', 'FIXED') pada setiap paket baru
     * dan menghasilkan ringkasan perbandingan diff.
     *
     * @param array<int, array> $newPackages Reference ke paket batch baru
     * @param int|null $previousBatchId ID batch acuan sebelumnya
     * @return array Ringkasan statistik diff-tracking
     */
    public function compare(array &$newPackages, ?int $previousBatchId): array
    {
        if (! $previousBatchId || $previousBatchId <= 0) {
            // Jika tidak ada batch pembanding, seluruh paket anomali berstatus 'NEW'
            foreach ($newPackages as &$pkg) {
                $pkg['diff_status'] = ($pkg['max_severity'] !== 'AMAN') ? 'NEW' : 'FIXED';
            }
            unset($pkg);

            return [
                'is_comparison_active'                    => false,
                'total_anomali_batch_lama'               => 0,
                'total_anomali_telah_diperbaiki_fixed'    => 0,
                'total_anomali_masih_bertahan_persistent'=> 0,
                'total_anomali_baru_muncul_new'          => count(array_filter($newPackages, static fn($p) => $p['max_severity'] !== 'AMAN')),
                'delta_skor_rci_instansi'                 => '+0.00',
            ];
        }

        $db = \Config\Database::connect();

        // Ambil data paket dari batch lama
        $prevPackages = $db->table('rup_audit_packages')
            ->select('kode_rup, nama_opd_clean, nama_paket_clean, max_severity, total_nilai')
            ->where('batch_id', $previousBatchId)
            ->get()->getResultArray();

        $prevMap = [];
        $totalAnomaliLama = 0;
        foreach ($prevPackages as $prev) {
            $key = $prev['kode_rup'] !== '' ? $prev['kode_rup'] : md5($prev['nama_opd_clean'] . '|' . $prev['nama_paket_clean'] . '|' . $prev['total_nilai']);
            $prevMap[$key] = $prev;
            if ($prev['max_severity'] !== 'AMAN') {
                $totalAnomaliLama++;
            }
        }

        $countPersistent = 0;
        $countNew        = 0;
        $matchedPrevKeys = [];

        foreach ($newPackages as &$pkg) {
            $key = $pkg['kode_rup'] !== '' ? $pkg['kode_rup'] : md5($pkg['nama_opd_clean'] . '|' . $pkg['nama_paket_clean'] . '|' . $pkg['total_nilai']);
            $hasAnomaliNow = ($pkg['max_severity'] !== 'AMAN');

            if (isset($prevMap[$key])) {
                $matchedPrevKeys[$key] = true;
                $hadAnomaliBefore = ($prevMap[$key]['max_severity'] !== 'AMAN');

                if ($hadAnomaliBefore && $hasAnomaliNow) {
                    $pkg['diff_status'] = 'PERSISTENT';
                    $countPersistent++;
                } elseif (! $hadAnomaliBefore && $hasAnomaliNow) {
                    $pkg['diff_status'] = 'NEW';
                    $countNew++;
                } else {
                    $pkg['diff_status'] = 'FIXED';
                }
            } else {
                $pkg['diff_status'] = $hasAnomaliNow ? 'NEW' : 'FIXED';
                if ($hasAnomaliNow) {
                    $countNew++;
                }
            }
        }
        unset($pkg);

        // Hitung paket batch lama yang memiliki anomali dan tidak lagi memiliki anomali di batch baru atau telah dihapus
        $countFixed = 0;
        foreach ($prevMap as $k => $p) {
            if ($p['max_severity'] !== 'AMAN') {
                // Jika tidak ada di batch baru atau di batch baru sudah AMAN
                if (! isset($matchedPrevKeys[$k])) {
                    $countFixed++; // Paket bermasalah dihapus dari SIRUP
                }
            }
        }

        // Ambil delta skor RCI instansi
        $prevBatchRow = $db->table('rup_audit_batches')->select('avg_rci_score')->where('id', $previousBatchId)->get()->getRowArray();
        $prevScore = (float) ($prevBatchRow['avg_rci_score'] ?? 0.0);

        return [
            'is_comparison_active'                    => true,
            'previous_batch_id'                       => $previousBatchId,
            'total_anomali_batch_lama'               => $totalAnomaliLama,
            'total_anomali_telah_diperbaiki_fixed'    => $countFixed,
            'total_anomali_masih_bertahan_persistent'=> $countPersistent,
            'total_anomali_baru_muncul_new'          => $countNew,
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
            ],
            'packages' => $newPackages,
        ];
    }
}
