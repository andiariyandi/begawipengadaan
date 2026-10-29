<?php

namespace App\Services\RupAudit;

class RupGraphClusterer
{
    public const TH_JACCARD_SIMILARITY = 0.78;

    /**
     * Ambang batas Pengadaan Langsung regulasi Perpres 12/2021 (TA <= 2024) atau Perpres 46/2025 (TA >= 2025).
     */
    public static function getBatasPl(string $jenisPengadaan, int $tahunAnggaran = 2026): float
    {
        $jenis = strtolower(trim($jenisPengadaan));
        if (str_contains($jenis, 'konstruksi')) {
            if ($tahunAnggaran > 0 && $tahunAnggaran <= 2024) {
                return 200000000.00; // Rp200 Juta untuk TA <= 2024 (Rezim Perpres 12/2021)
            }
            return 400000000.00; // Rp400 Juta untuk TA >= 2025 (Rezim Perpres 46/2025 jo. Perlem LKPP 3/2026)
        }
        if (str_contains($jenis, 'konsultansi')) {
            return 100000000.00; // Rp100 Juta
        }
        return 200000000.00; // Rp200 Juta untuk Barang & Jasa Lainnya
    }

    /**
     * Jalankan Graph-Based Fuzzy Clustering pada array paket hasil parsing.
     * Mengelompokkan paket dan memperbarui atribut `cluster_id` pada setiap paket,
     * serta mengembalikan daftar entitas klaster `rup_audit_clusters`.
     *
     * @param array<int, array> $packages Reference ke parsed packages
     * @return array<int, array> Daftar klaster komoditas
     */
    public function cluster(array &$packages): array
    {
        // 1. Partisi index baris per (nama_opd_clean, budget_cycle, jenis_pengadaan)
        $partitions = [];
        foreach ($packages as $idx => $pkg) {
            // Hanya paket Penyedia yang dievaluasi untuk klaster splitting
            if (($pkg['cara_pengadaan'] ?? '') !== 'Penyedia') {
                continue;
            }

            // Pengecualian Tarif Resmi/Utilitas/PPPK (DICT_01_DIKECUALIKAN) dari klaster splitting R-20
            $objSearch = (string) ($pkg['nama_paket_objek'] ?? $pkg['nama_paket_clean'] ?? '');
            if (RupTaxonomy::match($objSearch, RupTaxonomy::DICT_01_DIKECUALIKAN)) {
                continue;
            }

            $opd    = $pkg['nama_opd_clean'] ?? 'UNKNOWN';
            $cycle  = $pkg['budget_cycle'] ?? 'MURNI';
            $jenis  = $pkg['jenis_pengadaan'] ?? 'Barang';
            $locKey = 'ALL';

            // Khusus pekerjaan konstruksi, lokasi memisahkan klaster pemaketan fisik
            if (str_contains(strtolower($jenis), 'konstruksi')) {
                $locKey = strtolower(trim((string) ($pkg['lokasi_extracted'] ?: ($pkg['lokasi_pekerjaan'] ?? 'LOKASI_UMUM'))));
            }

            $partKey = "{$opd}|{$cycle}|{$jenis}|{$locKey}";
            $partitions[$partKey][] = $idx;
        }

        $clustersResult = [];

        // 2. Evaluasi graf kemiripan pada setiap partisi secara terisolasi
        foreach ($partitions as $partKey => $indices) {
            // Jika hanya 1 paket di partisi ini, tidak mungkin membentuk klaster splitting
            if (count($indices) < 2) {
                continue;
            }

            // Bangun Adjacency List untuk graf
            $adj = [];
            foreach ($indices as $i) {
                $adj[$i] = [];
            }

            $count = count($indices);
            for ($a = 0; $a < $count; $a++) {
                $idxA = $indices[$a];
                $tokensA = $packages[$idxA]['tokens'] ?? [];
                $coreA   = $packages[$idxA]['nama_paket_core'] ?? '';

                for ($b = $a + 1; $b < $count; $b++) {
                    $idxB = $indices[$b];
                    $tokensB = $packages[$idxB]['tokens'] ?? [];
                    $coreB   = $packages[$idxB]['nama_paket_core'] ?? '';

                    $sim = $this->calculateJaccard($tokensA, $tokensB, $coreA, $coreB);
                    if ($sim >= self::TH_JACCARD_SIMILARITY) {
                        $adj[$idxA][] = $idxB;
                        $adj[$idxB][] = $idxA;
                    }
                }
            }

            // 3. Cari Connected Components via BFS
            $visited = [];
            foreach ($indices as $startNode) {
                if (isset($visited[$startNode])) {
                    continue;
                }

                $component = [];
                $queue = [$startNode];
                $visited[$startNode] = true;

                while (! empty($queue)) {
                    $curr = array_shift($queue);
                    $component[] = $curr;

                    foreach ($adj[$curr] as $neighbor) {
                        if (! isset($visited[$neighbor])) {
                            $visited[$neighbor] = true;
                            $queue[] = $neighbor;
                        }
                    }
                }

                // Jika komponen memiliki >= 2 paket, bentuk Klaster Komoditas
                if (count($component) >= 2) {
                    $clusterUuid = 'CLS-' . substr(md5($partKey . '-' . implode('-', $component)), 0, 12);
                    $clusterData = $this->buildClusterSummary($component, $packages, $clusterUuid);

                    if ($clusterData !== null) {
                        $clustersResult[] = $clusterData;
                        // Tempel cluster_id ke tiap paket anggotanya
                        foreach ($component as $memberIdx) {
                            $packages[$memberIdx]['cluster_id'] = $clusterUuid;
                        }
                    }
                }
            }
        }

        return $clustersResult;
    }

    /**
     * Hitung Jaccard similarity antara dua himpunan token.
     */
    private function calculateJaccard(array $tokensA, array $tokensB, string $coreA, string $coreB): float
    {
        // Jika nama core persis sama, similarity = 1.0
        if ($coreA !== '' && $coreA === $coreB) {
            return 1.0;
        }

        if (empty($tokensA) || empty($tokensB)) {
            return 0.0;
        }

        $setA = array_unique($tokensA);
        $setB = array_unique($tokensB);

        $intersection = array_intersect($setA, $setB);
        $union        = array_unique(array_merge($setA, $setB));

        if (empty($union)) {
            return 0.0;
        }

        return count($intersection) / count($union);
    }

    /**
     * Bentuk rekap data klaster untuk tabel rup_audit_clusters.
     */
    private function buildClusterSummary(array $componentIndices, array $packages, string $clusterUuid): ?array
    {
        $first = $packages[$componentIndices[0]];
        $opdClean   = $first['nama_opd_clean'];
        $sipd       = $first['kode_satker_sipd'];
        $cycle      = $first['budget_cycle'];
        $jenis      = $first['jenis_pengadaan'];
        $batasPl    = self::getBatasPl($jenis, (int) ($first['tahun_anggaran'] ?? 2026));

        $totalPagu    = 0.0;
        $minVal       = PHP_FLOAT_MAX;
        $maxVal       = 0.0;
        $kodeRupList  = [];
        $coreNames    = [];
        $metodeCounts = [];
        $hasPl        = false;
        $hasMicro     = false;

        foreach ($componentIndices as $idx) {
            $pkg = $packages[$idx];
            $val = (float) $pkg['total_nilai'];
            $totalPagu += $val;
            if ($val < $minVal) {
                $minVal = $val;
            }
            if ($val > $maxVal) {
                $maxVal = $val;
            }

            if ($pkg['kode_rup'] !== '') {
                $kodeRupList[] = $pkg['kode_rup'];
            }

            $cName = $pkg['nama_paket_core'] ?: $pkg['nama_paket_objek'];
            if ($cName !== '') {
                $coreNames[] = $cName;
            }

            $metode = $pkg['metode_pengadaan'] ?: '-';
            $metodeCounts[$metode] = ($metodeCounts[$metode] ?? 0) + 1;

            if (in_array('Pengadaan Langsung', $pkg['metode_arr'] ?? [], true)) {
                $hasPl = true;
            }
            if ($val > 0 && $val < 500000.0) {
                $hasMicro = true;
            }
        }

        arsort($metodeCounts);
        $metodeDominan = array_key_first($metodeCounts) ?: '-';

        // Tentukan nama komoditas klaster paling representatif
        $cValues = array_count_values($coreNames);
        arsort($cValues);
        $namaKomoditas = array_key_first($cValues) ?: 'Komoditas Gabungan';

        $jumlahPaket = count($componentIndices);

        // Evaluasi Kategori Temuan & Severity
        $kategoriTemuan = 'KONSOLIDASI_PAKET (R-22)';
        $severity       = 'SEDANG';
        $instruksi      = "Konsolidasikan {$jumlahPaket} paket pengadaan sejenis ini menjadi satu paket pengadaan terpadu untuk efisiensi anggaran dan skala ekonomi.";

        // Jika metode dominan / ada Pengadaan Langsung dan akumulasi pagu melampaui batas PL -> R-20 (Splitting Kritis)
        if ($hasPl && $totalPagu > $batasPl) {
            $kategoriTemuan = 'INDIKASI_SPLITTING (R-20)';
            $severity       = 'KRITIS';
            $batasFmt       = number_format($batasPl, 0, ',', '.');
            $paguFmt        = number_format($totalPagu, 0, ',', '.');
            $instruksi      = "Indikasi kuat pemecahan paket (splitting) untuk menghindari Tender/Seleksi. Akumulasi pagu (Rp {$paguFmt}) melampaui batas Pengadaan Langsung (Rp {$batasFmt}). Wajib digabung dan dilelangkan melalui E-Purchasing / Tender.";
        } elseif ($hasPl && ($maxVal >= 0.95 * $batasPl && $maxVal <= $batasPl)) {
            $kategoriTemuan = 'THRESHOLD_HUGGING (R-27)';
            $severity       = 'TINGGI';
            $instruksi      = "Anomali pagu mepet batas maksimal Pengadaan Langsung (95% - 100%). Periksa kewajaran HPS/RAB dan gabungkan paket ke E-Purchasing atau Tender.";
        } elseif ($jumlahPaket >= 3 || $hasMicro) {
            $kategoriTemuan = 'FRAGMENTASI_PAKET (R-22)';
            $severity       = 'SEDANG';
            $instruksi      = "Fragmentasi paket berlebihan (>= 3 paket sejenis) atau terdapat paket bernilai mikro (< Rp500.000). Konsolidasikan untuk efisiensi kontrak.";
        }

        return [
            'cluster_uuid'           => $clusterUuid,
            'kode_satker_sipd'       => $sipd,
            'nama_opd_clean'         => $opdClean,
            'budget_cycle'           => $cycle,
            'nama_komoditas_klaster' => ucwords($namaKomoditas),
            'lokasi_klaster'         => $first['lokasi_extracted'] ?: 'ALL',
            'jenis_pengadaan'        => $jenis,
            'metode_dominan'         => $metodeDominan,
            'jumlah_paket_terpecah'  => $jumlahPaket,
            'nilai_paket_terkecil'   => $minVal === PHP_FLOAT_MAX ? 0.0 : $minVal,
            'nilai_paket_terbesar'   => $maxVal,
            'total_pagu_klaster'     => $totalPagu,
            'batas_regulasi_pl'      => $batasPl,
            'kategori_temuan'        => $kategoriTemuan,
            'severity'               => $severity,
            'daftar_kode_rup'        => json_encode($kodeRupList),
            'instruksi_konsolidasi'  => $instruksi,
            'created_at'             => date('Y-m-d H:i:s'),
        ];
    }
}
