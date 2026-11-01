<?php

namespace App\Services\RupAudit;

class RupGraphClusterer
{
    public const TH_JACCARD_SIMILARITY = 0.78;
    public const TH_CENTROID_SIMILARITY = 0.70;

    /**
     * Ambang batas Pengadaan Langsung regulasi Perpres 12/2021 (TA <= 2024) atau Perpres 46/2025 (TA >= 2025).
     */
    public static function getBatasPl(string $jenisPengadaan, int $tahunAnggaran = 2026): float
    {
        $jenis = strtolower(trim($jenisPengadaan));
        if (str_contains($jenis, 'konstruksi')) {
            if ($tahunAnggaran > 0 && $tahunAnggaran <= 2024) {
                return RupTaxonomy::PL_THRESHOLD_2021_KONSTRUKSI; // Rp200 Juta untuk TA <= 2024
            }
            return RupTaxonomy::PL_THRESHOLD_2026_KONSTRUKSI; // Rp400 Juta untuk TA >= 2025
        }
        if (str_contains($jenis, 'konsultansi')) {
            return RupTaxonomy::PL_THRESHOLD_2026_KONSULTANSI; // Rp100 Juta
        }
        return RupTaxonomy::PL_THRESHOLD_2026_BARANG; // Rp200 Juta untuk Barang & Jasa Lainnya
    }

    /**
     * Jalankan Graph-Based Fuzzy Clustering pada array paket hasil parsing.
     * Mengelompokkan paket dan memperbarui atribut `cluster_id` pada setiap paket,
     * serta mengembalikan daftar entitas klaster `rup_audit_clusters` v4.0.
     *
     * @param array<int, array> $packages Reference ke parsed packages
     * @return array<int, array> Daftar klaster komoditas
     */
    public function cluster(array &$packages): array
    {
        // 1. Partisi awal ketat:
        // (nama_opd_clean | jenis_pengadaan | source_funding_profile | location_identity_key)
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

            $opd     = $pkg['nama_opd_clean'] ?? 'UNKNOWN';
            $jenis   = $pkg['jenis_pengadaan'] ?? 'Barang';
            $funding = $pkg['source_funding_profile'] ?? ($pkg['sumber_dana'] ?? 'APBD');
            $locKey  = 'GLOBAL_LOC';

            // Khusus pekerjaan konstruksi, lokasi/nomor ruas link memisahkan klaster pemaketan fisik
            if (str_contains(strtolower($jenis), 'konstruksi')) {
                $locKey = strtolower(trim((string) ($pkg['location_identity_key'] ?: ($pkg['lokasi_extracted'] ?: 'LOKASI_UMUM'))));
            }

            $partKey = "{$opd}|{$jenis}|{$funding}|{$locKey}";
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
                $cleanA  = $packages[$idxA]['nama_paket_clean'] ?? '';

                for ($b = $a + 1; $b < $count; $b++) {
                    $idxB = $indices[$b];
                    $tokensB = $packages[$idxB]['tokens'] ?? [];
                    $coreB   = $packages[$idxB]['nama_paket_core'] ?? '';
                    $cleanB  = $packages[$idxB]['nama_paket_clean'] ?? '';

                    $sim = $this->calculateJaccard($tokensA, $tokensB, $coreA, $coreB, $cleanA, $cleanB);
                    if ($sim >= self::TH_JACCARD_SIMILARITY) {
                        $adj[$idxA][] = $idxB;
                        $adj[$idxB][] = $idxA;
                    }
                }
            }

            // 3. Cari Connected Components via BFS dengan validasi homogenitas / centroid check
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
                            // Centroid / Representative check: pastikan neighbor mirip dengan anggota lain
                            if ($this->validateHomogeneity($neighbor, $component, $packages)) {
                                $visited[$neighbor] = true;
                                $queue[] = $neighbor;
                            }
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
     * Validasi homogenitas untuk mencegah transitive chaining A-B-C yang tidak seragam
     */
    private function validateHomogeneity(int $candidateIdx, array $currentMembers, array $packages): bool
    {
        if (empty($currentMembers)) {
            return true;
        }

        $tokensCand = $packages[$candidateIdx]['tokens'] ?? [];
        $coreCand   = $packages[$candidateIdx]['nama_paket_core'] ?? '';
        $cleanCand  = $packages[$candidateIdx]['nama_paket_clean'] ?? '';

        $totalSim = 0.0;
        foreach ($currentMembers as $mIdx) {
            $tokensM = $packages[$mIdx]['tokens'] ?? [];
            $coreM   = $packages[$mIdx]['nama_paket_core'] ?? '';
            $cleanM  = $packages[$mIdx]['nama_paket_clean'] ?? '';
            $sim = $this->calculateJaccard($tokensCand, $tokensM, $coreCand, $coreM, $cleanCand, $cleanM);
            $totalSim += $sim;
        }

        $avgSim = $totalSim / count($currentMembers);
        return $avgSim >= self::TH_CENTROID_SIMILARITY;
    }

    /**
     * Hitung Jaccard similarity antara dua himpunan token dengan perlindungan kesamaan teks utuh.
     */
    private function calculateJaccard(array $tokensA, array $tokensB, string $coreA, string $coreB, string $cleanA = '', string $cleanB = ''): float
    {
        // Jika nama core atau nama clean persis sama, similarity = 1.0
        if (($coreA !== '' && $coreA === $coreB) || ($cleanA !== '' && $cleanA === $cleanB)) {
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
     * Bentuk rekap data klaster untuk tabel rup_audit_clusters v4.0.
     */
    private function buildClusterSummary(array $componentIndices, array $packages, string $clusterUuid): ?array
    {
        $first = $packages[$componentIndices[0]];
        $opdClean   = $first['nama_opd_clean'];
        $sipd       = $first['kode_satker_sipd'];
        $cycle      = $first['budget_cycle'];
        $jenis      = $first['jenis_pengadaan'];
        $batasPl    = self::getBatasPl($jenis, (int) ($first['tahun_anggaran'] ?? 2026));

        $totalPagu      = 0.0;
        $minVal         = PHP_FLOAT_MAX;
        $maxVal         = 0.0;
        $kodeRupList    = [];
        $memberRowList  = [];
        $coreNames      = [];
        $metodeCounts   = [];
        $hasPl          = false;
        $hasMicro       = false;
        $serialFamCount = 0;

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
            $memberRowList[] = $pkg['row_index_csv'] ?? $idx;

            if (! empty($pkg['serial_sequence_number'])) {
                $serialFamCount++;
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

        // Evaluasi Batas Efektif v4.0.1: Jika Konstruksi namun objek riil bahan material / sewa alat, terapkan batas PL efektif Rp200 Jt
        $catatanBatasEfektif = '';
        if ($jenis === 'Pekerjaan Konstruksi') {
            $isBahanMaterial = RupTaxonomy::match($namaKomoditas, RupTaxonomy::DICT_08_MURNI_BARANG, RupTaxonomy::NEG_LOOKAHEAD_08);
            $isSewaAlat      = RupTaxonomy::match($namaKomoditas, RupTaxonomy::DICT_07_MURNI_JASA_LAINNYA, RupTaxonomy::NEG_LOOKAHEAD_07);
            if (! $isBahanMaterial && ! $isSewaAlat) {
                foreach ($coreNames as $cn) {
                    if (RupTaxonomy::match($cn, RupTaxonomy::DICT_08_MURNI_BARANG, RupTaxonomy::NEG_LOOKAHEAD_08)) {
                        $isBahanMaterial = true;
                        break;
                    }
                    if (RupTaxonomy::match($cn, RupTaxonomy::DICT_07_MURNI_JASA_LAINNYA, RupTaxonomy::NEG_LOOKAHEAD_07)) {
                        $isSewaAlat = true;
                        break;
                    }
                }
            }
            if ($isBahanMaterial || $isSewaAlat) {
                $batasPl = RupTaxonomy::PL_THRESHOLD_2026_BARANG; // Efektif Rp200 Juta
                $tipeObjek = $isBahanMaterial ? 'Bahan Material' : 'Sewa Alat';
                $catatanBatasEfektif = " (Batas Efektif Rp200 Juta - Objek {$tipeObjek})";
            }
        }

        $jumlahPaket = count($componentIndices);

        // Evaluasi Kategori Temuan & Severity v4.0
        $kategoriTemuan    = 'KONSOLIDASI_PAKET (R-22)';
        $clusterType       = 'PELUANG_KONSOLIDASI';
        $clusterConfidence = 'SEDANG';
        $severity          = 'SEDANG';
        $instruksi         = "Konsolidasikan {$jumlahPaket} paket pengadaan sejenis ini menjadi satu paket pengadaan terpadu untuk efisiensi anggaran dan skala ekonomi.";

        // Jika metode dominan / ada Pengadaan Langsung dan akumulasi pagu melampaui batas PL -> R-20 (Splitting Kritis / Tinggi)
        if ($hasPl && $totalPagu > $batasPl) {
            $kategoriTemuan    = 'INDIKASI_SPLITTING (R-20)';
            $clusterType       = 'SERIAL_SPLITTING_CANDIDATE';
            $clusterConfidence = ($serialFamCount >= 2) ? 'TINGGI' : 'SEDANG';
            $severity          = 'TINGGI';
            $batasFmt          = number_format($batasPl, 0, ',', '.');
            $paguFmt           = number_format($totalPagu, 0, ',', '.');
            $instruksi         = "Indikasi pemecahan paket (splitting) untuk menghindari Tender/Seleksi. Akumulasi pagu (Rp {$paguFmt}) melampaui batas Pengadaan Langsung (Rp {$batasFmt}{$catatanBatasEfektif}). Disarankan reviu kesatuan perencanaan dan konsolidasikan ke Tender/E-Purchasing.";
        } elseif ($hasPl && ($maxVal >= 0.95 * $batasPl && $maxVal <= $batasPl)) {
            $kategoriTemuan    = 'THRESHOLD_HUGGING (R-27)';
            $clusterType       = 'THRESHOLD_HUGGING_CLUSTER';
            $clusterConfidence = 'SEDANG';
            $severity          = 'SEDANG';
            $instruksi         = "Anomali pagu mendekati batas maksimal Pengadaan Langsung (95% - 100%). Periksa kewajaran HPS/RAB dan kaji peluang konsolidasi.";
        } elseif ($jumlahPaket >= 3 || $hasMicro) {
            $kategoriTemuan    = 'FRAGMENTASI_PAKET (R-22)';
            $clusterType       = 'PELUANG_KONSOLIDASI';
            $clusterConfidence = 'SEDANG';
            $severity          = 'RENDAH';
            $instruksi         = "Fragmentasi paket berulang (>= 3 paket sejenis). Disarankan evaluasi konsolidasi kontrak.";
        }

        $limitationNote = "Klaster ini merupakan indikasi analitik berbasis kesamaan atribut CSV dan nomor urut RUP; bukan pembuktian hukum bahwa paket sengaja dipecah untuk menghindari tender. Kebutuhan lapangan, jadwal, dan kesiapan teknis perlu diverifikasi oleh pejabat berwenang.";

        return [
            'cluster_uuid'            => $clusterUuid,
            'kode_satker_sipd'        => $sipd,
            'nama_opd_clean'          => $opdClean,
            'budget_cycle'            => $cycle,
            'nama_komoditas_klaster'  => ucwords($namaKomoditas),
            'lokasi_klaster'          => $first['location_identity_key'] ?: ($first['lokasi_extracted'] ?: 'ALL'),
            'jenis_pengadaan'         => $jenis,
            'metode_dominan'          => $metodeDominan,
            'cluster_type'            => $clusterType,
            'cluster_confidence'      => $clusterConfidence,
            'jumlah_paket_terpecah'   => $jumlahPaket,
            'serial_family_count'     => $serialFamCount,
            'nilai_paket_terkecil'    => $minVal === PHP_FLOAT_MAX ? 0.0 : $minVal,
            'nilai_paket_terbesar'    => $maxVal,
            'total_pagu_klaster'      => $totalPagu,
            'batas_regulasi_pl'       => $batasPl,
            'kategori_temuan'         => $kategoriTemuan,
            'severity'                => $severity,
            'daftar_kode_rup'         => json_encode($kodeRupList),
            'member_package_ids_json' => json_encode($memberRowList),
            'instruksi_konsolidasi'   => $instruksi,
            'cluster_limitation_note' => $limitationNote,
            'created_at'              => date('Y-m-d H:i:s'),
        ];
    }
}
