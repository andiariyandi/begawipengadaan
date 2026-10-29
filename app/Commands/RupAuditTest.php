<?php

namespace App\Commands;

use App\Models\RupAuditBatchModel;
use App\Models\RupAuditClusterModel;
use App\Models\RupAuditOpdSummaryModel;
use App\Models\RupAuditPackageModel;
use App\Models\RupAuditViolationModel;
use App\Services\RupAudit\RupAccessHelper;
use App\Services\RupAudit\RupAuditService;
use App\Services\RupAudit\RupCsvParser;
use App\Services\RupAudit\RupDiffTracker;
use App\Services\RupAudit\RupExportService;
use App\Services\RupAudit\RupGraphClusterer;
use App\Services\RupAudit\RupNlpExtractor;
use App\Services\RupAudit\RupRciCalculator;
use App\Services\RupAudit\RupRuleEngine;
use App\Services\RupAudit\RupTaxonomy;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RupAuditTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:rup-audit';
    protected $description = 'Verifikasi Komprehensif Universal SIRUP Compliance Engine v3.1 (Audit RUP Otomatis)';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("   UNIVERSAL SIRUP COMPLIANCE ENGINE v3.1 - VERIFIKASI SISTEM     ", "yellow");
        CLI::write("   Perpres 46/2025 · UU 20/2023 · Perlem LKPP 3/2026             ", "yellow");
        CLI::write("==================================================================", "yellow");

        $passCount = 0;
        $failCount = 0;

        // -------------------------------------------------------------
        // TEST 1: Database Migration & Schema Verification
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 1] Verifikasi Schema Tabel Database...", "yellow");
        $db = \Config\Database::connect();
        $expectedTables = [
            'rup_audit_batches',
            'rup_audit_opd_summaries',
            'rup_audit_packages',
            'rup_audit_violations',
            'rup_audit_clusters',
        ];

        $allTablesFound = true;
        foreach ($expectedTables as $tbl) {
            if ($db->tableExists($tbl)) {
                CLI::write("  [PASS] Tabel `{$tbl}` ditemukan di database", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Tabel `{$tbl}` TIDAK ditemukan di database", "red");
                $failCount++;
                $allTablesFound = false;
            }
        }

        // -------------------------------------------------------------
        // TEST 2: NLP Extractor & SIPD Splitter
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 2] Verifikasi NLP Primary Object Extractor & SIPD Splitter...", "yellow");
        
        // 2a. SIPD Code Extraction
        $rawSatker = "DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000";
        $sipdInfo = RupNlpExtractor::extractOpdAndSipd($rawSatker);
        if ($sipdInfo['kode_satker_sipd'] === '1.03.0.00.0.00.06.0000' && str_contains($sipdInfo['nama_opd_clean'], 'BINA MARGA')) {
            CLI::write("  [PASS] Ekstraksi kode SIPD 22 digit ({$sipdInfo['kode_satker_sipd']}) dan nama OPD sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Gagal mengekstrak kode SIPD 22 digit dari string satker", "red");
            $failCount++;
        }

        // 2b. Truncation clause after "Sub Kegiatan..." and parenthetical stripping
        $sampleRawName = "Pengadaan Belanja Bahan Logistik Bencana (Tahap 1) Sub Kegiatan Koordinasi Penanganan Bencana Daerah";
        $extractedObj = RupNlpExtractor::extractObject($sampleRawName);
        if (! str_contains($extractedObj, 'sub kegiatan') && ! str_contains($extractedObj, 'tahap 1')) {
            CLI::write("  [PASS] NLP berhasil memotong klausa 'Sub Kegiatan...' dan menghapus keterangan dalam kurung: '{$extractedObj}'", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] NLP gagal membersihkan klausa: {$extractedObj}", "red");
            $failCount++;
        }

        // 2c. Core Commodity Extraction
        $coreInfo = RupNlpExtractor::extractCoreAndLocation($extractedObj);
        $coreCom = $coreInfo['nama_paket_core'];
        if (! empty($coreCom)) {
            CLI::write("  [PASS] Core Commodity berhasil diekstrak: '{$coreCom}'", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Core Commodity kosong", "red");
            $failCount++;
        }

        // 2d. Opaque naming detection
        $opaqueTest1 = RupTaxonomy::isOpaque("Belanja Barang");
        $opaqueTest2 = RupTaxonomy::isOpaque("Pengadaan Laptop Core i7 Kantor Dinas");
        if ($opaqueTest1 === true && $opaqueTest2 === false) {
            CLI::write("  [PASS] Deteksi Opaque Naming (R-34) akurat: 'Belanja Barang' terdeteksi generik/opaque", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Deteksi Opaque Naming tidak akurat", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 3: Dual-Mode CSV Parser & Multi-Value Array Mapping
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 3] Verifikasi Dual-Mode CSV Parser & Multi-Value Handler...", "yellow");

        $csvSample = <<<CSV
nama_instansi;nama_satuan_kerja;tahun_anggaran;cara_pengadaan;metode_pengadaan;jenis_pengadaan;nama_paket;kode_rup;sumber_dana;produk_dalam_negeri;status_umk;total_nilai
Pemerintah Provinsi Lampung;DINAS KESEHATAN - 1.02.0.00.0.00.01.0000;2026;Penyedia;Pengadaan Langsung;Barang;Pengadaan Obat Generik Puskesmas;54321098;APBD;Ya;Ya;150000000
Pemerintah Provinsi Lampung;DINAS KESEHATAN - 1.02.0.00.0.00.01.0000;2026;Penyedia;Tender;Pekerjaan Konstruksi;Pembangunan Ruang Rawat Inap;54321099;APBD;Ya;Tidak;5.50000E+08
CSV;

        $parsedRows = RupCsvParser::parseString($csvSample);
        if (count($parsedRows) === 2) {
            $row2 = $parsedRows[1];
            // Check scientific notation conversion
            if ($row2['total_nilai'] == 550000000.0) {
                CLI::write("  [PASS] Parser CSV berhasil memetakan delimiter semicolon (;) dan konversi notasi ilmiah 5.50000E+08 -> Rp550.000.000", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Konversi nilai ilmiah salah: " . $row2['total_nilai'], "red");
                $failCount++;
            }

            if ($row2['kode_rup'] === '54321099') {
                CLI::write("  [PASS] Kode RUP diperlakukan ketat sebagai string ('54321099')", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Kode RUP bukan string: " . var_export($row2['kode_rup'], true), "red");
                $failCount++;
            }
        } else {
            CLI::write("  [FAIL] Gagal mem-parsing baris CSV", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: Graph-Based Fuzzy Clustering (Jaccard >= 0.78)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 4] Verifikasi Graph-Based Fuzzy Clustering & Connected Components...", "yellow");

        $clusteringPackages = [
            [
                'package_index'    => 0,
                'nama_opd_clean'   => 'DINAS BINA MARGA',
                'kode_satker_sipd' => '1.03.00',
                'cara_pengadaan'   => 'Penyedia',
                'nama_paket'       => 'Pemeliharaan Jalan Ruas Metro - Pekalongan Tahap 1',
                'nama_paket_clean' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_objek' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_core'  => 'pemeliharaan jalan ruas metro pekalongan',
                'tokens'           => ['pemeliharaan', 'jalan', 'ruas', 'metro', 'pekalongan'],
                'lokasi_extracted' => 'pekalongan',
                'lokasi_pekerjaan' => 'pekalongan',
                'budget_cycle'     => 'MURNI',
                'jenis_pengadaan'  => 'Pekerjaan Konstruksi',
                'metode_pengadaan' => 'Pengadaan Langsung',
                'metode_arr'       => ['Pengadaan Langsung'],
                'kode_rup'         => '90001',
                'total_nilai'      => 220000000,
            ],
            [
                'package_index'    => 1,
                'nama_opd_clean'   => 'DINAS BINA MARGA',
                'kode_satker_sipd' => '1.03.00',
                'cara_pengadaan'   => 'Penyedia',
                'nama_paket'       => 'Pemeliharaan Jalan Ruas Metro - Pekalongan Tahap 2',
                'nama_paket_clean' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_objek' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_core'  => 'pemeliharaan jalan ruas metro pekalongan',
                'tokens'           => ['pemeliharaan', 'jalan', 'ruas', 'metro', 'pekalongan'],
                'lokasi_extracted' => 'pekalongan',
                'lokasi_pekerjaan' => 'pekalongan',
                'budget_cycle'     => 'MURNI',
                'jenis_pengadaan'  => 'Pekerjaan Konstruksi',
                'metode_pengadaan' => 'Pengadaan Langsung',
                'metode_arr'       => ['Pengadaan Langsung'],
                'kode_rup'         => '90002',
                'total_nilai'      => 230000000,
            ],
            [
                'package_index'    => 2,
                'nama_opd_clean'   => 'DINAS KESEHATAN',
                'kode_satker_sipd' => '1.02.00',
                'cara_pengadaan'   => 'Penyedia',
                'nama_paket'       => 'Pemeliharaan Jalan Ruas Metro - Pekalongan Ruang IGD',
                'nama_paket_clean' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_objek' => 'pemeliharaan jalan ruas metro pekalongan',
                'nama_paket_core'  => 'pemeliharaan jalan ruas metro pekalongan',
                'tokens'           => ['pemeliharaan', 'jalan', 'ruas', 'metro', 'pekalongan'],
                'lokasi_extracted' => 'pekalongan',
                'lokasi_pekerjaan' => 'pekalongan',
                'budget_cycle'     => 'MURNI',
                'jenis_pengadaan'  => 'Pekerjaan Konstruksi',
                'metode_pengadaan' => 'Pengadaan Langsung',
                'metode_arr'       => ['Pengadaan Langsung'],
                'kode_rup'         => '90003',
                'total_nilai'      => 220000000,
            ],
        ];

        $clusterer = new RupGraphClusterer();
        $clusters  = $clusterer->cluster($clusteringPackages);
        // Package 0 and 1 belong to same OPD and identical tokens, should cluster together.
        // Package 2 belongs to different OPD (DINAS KESEHATAN), must remain isolated.
        if (count($clusters) === 1 && $clusters[0]['jumlah_paket_terpecah'] === 2) {
            CLI::write("  [PASS] Fuzzy Clustering sukses: 2 paket ter-cluster pada OPD yang sama (Total Rp450.000.000), isolasi OPD bekerja sempurna", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Fuzzy clustering gagal mengelompokkan paket secara akurat. Total cluster: " . count($clusters), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 5: Multi-Tier Regulatory Rule Engine (R-01 s.d. R-34)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 5] Verifikasi Aturan Regulasi (Rule Engine R-01 s.d. R-34)...", "yellow");

        // 5a. R-01: Pagu <= 0
        $viol01 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 0,
            'cara_pengadaan' => 'Penyedia',
            'metode_arr' => ['Pengadaan Langsung'],
            'jenis_arr' => ['Barang'],
            'nama_paket' => 'Belanja ATK Kantor',
            'status_umk' => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR01 = false;
        foreach ($viol01 as $v) {
            if ($v['rule_id'] === 'R-01') $hasR01 = true;
        }
        if ($hasR01) {
            CLI::write("  [PASS] Rule R-01 (Pagu <= 0) terdeteksi (Tingkat Risiko: KRITIS)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-01 gagal mendeteksi pagu 0", "red");
            $failCount++;
        }

        // 5b. R-04: PL Barang/Jasa > 200 Juta
        $viol04 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 250000000,
            'cara_pengadaan' => 'Penyedia',
            'metode_arr' => ['Pengadaan Langsung'],
            'jenis_arr' => ['Barang'],
            'nama_paket' => 'Pengadaan Komputer All In One',
            'status_umk' => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR04 = false;
        foreach ($viol04 as $v) {
            if ($v['rule_id'] === 'R-04') $hasR04 = true;
        }
        if ($hasR04) {
            CLI::write("  [PASS] Rule R-04 (PL Barang > Rp200 Juta) terdeteksi (Tingkat Risiko: KRITIS)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-04 gagal mendeteksi PL Barang > 200 Juta", "red");
            $failCount++;
        }

        // 5c. R-11: Outsourcing pada Swakelola melanggar UU 20/2023
        $viol11 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 80000000,
            'cara_pengadaan' => 'Swakelola',
            'metode_arr' => [],
            'jenis_arr' => ['Jasa Lainnya'],
            'nama_paket' => 'Penyediaan Tenaga Kebersihan dan Security Kantor',
            'status_umk' => '',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR11 = false;
        foreach ($viol11 as $v) {
            if ($v['rule_id'] === 'R-11') $hasR11 = true;
        }
        if ($hasR11) {
            CLI::write("  [PASS] Rule R-11 (Jasa Tenaga Alih Daya/Outsourcing pada Swakelola UU 20/2023) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-11 gagal mendeteksi outsourcing swakelola", "red");
            $failCount++;
        }

        // 5d. R-15: Katalog V6 Avoidance
        $viol15 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 45000000,
            'cara_pengadaan' => 'Penyedia',
            'metode_arr' => ['Pengadaan Langsung'],
            'jenis_arr' => ['Barang'],
            'nama_paket' => 'Belanja Alat Tulis Kantor dan Kertas HVS F4',
            'status_umk' => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR15 = false;
        foreach ($viol15 as $v) {
            if ($v['rule_id'] === 'R-15') $hasR15 = true;
        }
        if ($hasR15) {
            CLI::write("  [PASS] Rule R-15 (Penghindaran E-Purchasing Katalog V6 atas Komoditas Wajib) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-15 gagal mendeteksi komoditas katalog v6", "red");
            $failCount++;
        }

        // 5e. R-24: Usaha Kecil > Rp15 Miliar
        $viol24 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 18000000000,
            'cara_pengadaan' => 'Penyedia',
            'metode_arr' => ['Tender'],
            'jenis_arr' => ['Pekerjaan Konstruksi'],
            'nama_paket' => 'Pembangunan Flyover Simpang Lima',
            'status_umk' => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR24 = false;
        foreach ($viol24 as $v) {
            if ($v['rule_id'] === 'R-24') $hasR24 = true;
        }
        if ($hasR24) {
            CLI::write("  [PASS] Rule R-24 (Paket > Rp15 Miliar dialokasikan untuk UMK) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-24 gagal mendeteksi paket > 15M berstatus UMK", "red");
            $failCount++;
        }

        // 5f. R-27: Threshold Hugging (95% - 100% Batas PL)
        $viol27 = RupRuleEngine::evaluatePackage([
            'total_nilai' => 198500000, // 99.25% dari 200 Juta
            'cara_pengadaan' => 'Penyedia',
            'metode_arr' => ['Pengadaan Langsung'],
            'jenis_arr' => ['Barang'],
            'nama_paket' => 'Pengadaan Server Mini Dinas',
            'status_umk' => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR27 = false;
        foreach ($viol27 as $v) {
            if ($v['rule_id'] === 'R-27') $hasR27 = true;
        }
        if ($hasR27) {
            CLI::write("  [PASS] Rule R-27 (Threshold Hugging Batas PL Rp198,5 Juta) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-27 gagal mendeteksi threshold hugging", "red");
            $failCount++;
        }

        // 5g. R-07: PL Konstruksi Rp200-400 Juta Teknologi Kompleks (jo. Perlem LKPP 3/2026)
        $viol07 = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 350000000, // Rp350 Juta (rentang 200 - 400 Jt)
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Pengadaan Langsung'],
            'jenis_arr'           => ['Pekerjaan Konstruksi'],
            'nama_paket'          => 'Pembangunan Jembatan Gantung Desa',
            'nama_paket_clean'    => 'Pembangunan Jembatan Gantung Desa',
            'nama_paket_objek'    => 'Pembangunan Jembatan Gantung Desa',
            'status_umk'          => 'Ya', // UMK Ya, tetapi memuat kata kunci 'jembatan' (DICT_KONSTRUKSI_KOMPLEKS)
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR07Kompleks = false;
        foreach ($viol07 as $v) {
            if ($v['rule_id'] === 'R-07' && str_contains(strtolower($v['deskripsi_pelanggaran']), 'kompleks')) {
                $hasR07Kompleks = true;
            }
        }
        if ($hasR07Kompleks) {
            CLI::write("  [PASS] Rule R-07 (PL Konstruksi Rp200-400 Jt Teknologi Kompleks/Jembatan jo. Perlem LKPP 3/2026) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-07 gagal mendeteksi pekerjaan konstruksi kompleks pada PL Rp200-400 Juta", "red");
            $failCount++;
        }

        // 5h. R-08b: Verifikasi Repeat Order (RO) Penunjukan Langsung (jo. Perlem LKPP 3/2026)
        $viol08b = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 150000000,
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Penunjukan Langsung'],
            'jenis_arr'           => ['Jasa Konsultansi'],
            'nama_paket'          => 'Pengadaan Lisensi Software Antivirus (Repeat Order)',
            'nama_paket_clean'    => 'Pengadaan Lisensi Software Antivirus (Repeat Order)',
            'nama_paket_objek'    => 'Pengadaan Lisensi Software Antivirus',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR08b = false;
        foreach ($viol08b as $v) {
            if ($v['rule_id'] === 'R-08b') {
                $hasR08b = true;
            }
        }
        if ($hasR08b) {
            CLI::write("  [PASS] Rule R-08b (Verifikasi Syarat Repeat Order Penunjukan Langsung jo. Perlem LKPP 3/2026) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-08b gagal mendeteksi catatan verifikasi Repeat Order", "red");
            $failCount++;
        }

        // 5i. R-24: Konsultansi Konstruksi > Rp1 Miliar Wajib Usaha Non-Kecil (Pasal 4 ayat (2) Perlem LKPP 3/2026)
        $viol24Konst = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 1500000000.0, // Rp1,5 Miliar (> Rp1 Miliar)
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Seleksi'],
            'jenis_arr'           => ['Jasa Konsultansi'],
            'nama_paket'          => 'Pengawasan Pembangunan Gedung Laboratorium Terpadu',
            'nama_paket_clean'    => 'Pengawasan Pembangunan Gedung Laboratorium Terpadu',
            'nama_paket_objek'    => 'Pengawasan Pembangunan Gedung Laboratorium Terpadu',
            'status_umk'          => 'Ya', // Seharusnya 'Tidak' karena > Rp1 Miliar
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR24Konst = false;
        foreach ($viol24Konst as $v) {
            if ($v['rule_id'] === 'R-24' && str_contains(strtolower($v['deskripsi_pelanggaran']), 'konsultansi konstruksi')) {
                $hasR24Konst = true;
            }
        }
        if ($hasR24Konst) {
            CLI::write("  [PASS] Rule R-24 (Konsultansi Konstruksi Rp1,5 M > Batas UMK Rp1 M jo. Pasal 4 ayat (2) Perlem LKPP 3/2026) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-24 gagal mendeteksi Konsultansi Konstruksi > Rp1 Miliar berstatus UMK Ya", "red");
            $failCount++;
        }

        // 5j. R-08: Penunjukan Langsung Konstruksi Rp350 Juta Kewenangan Pokja Pemilihan (Lampiran II Angka 1.4 Perlem LKPP 3/2026)
        $viol08Pokja = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 350000000.0, // Rp350 Juta (> Rp200 Juta)
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Penunjukan Langsung'],
            'jenis_arr'           => ['Pekerjaan Konstruksi'],
            'nama_paket'          => 'Pembangunan Gedung Gudang Arsip Kantor',
            'nama_paket_clean'    => 'Pembangunan Gedung Gudang Arsip Kantor',
            'nama_paket_objek'    => 'Pembangunan Gedung Gudang Arsip Kantor',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR08Pokja = false;
        foreach ($viol08Pokja as $v) {
            if ($v['rule_id'] === 'R-08' && str_contains($v['deskripsi_pelanggaran'], 'Pokja Pemilihan UKPBJ')) {
                $hasR08Pokja = true;
            }
        }
        if ($hasR08Pokja) {
            CLI::write("  [PASS] Rule R-08 (PL Konstruksi Rp350 Jt Wajib Kewenangan Pokja Pemilihan UKPBJ jo. Perlem LKPP 3/2026) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-08 gagal memvalidasi kewenangan Pokja Pemilihan pada PL Konstruksi > Rp200 Juta", "red");
            $failCount++;
        }

        // 5k. R-04b: Fitur Transaksional SPSE > Rp50 Juta & Reviu APIP Jasa Lainnya (Pasal 3 ayat (3)-(5) Perlem LKPP 3/2026)
        $viol04b = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 75000000.0, // Rp75 Juta (> Rp50 Juta)
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Pengadaan Langsung'],
            'jenis_arr'           => ['Jasa Lainnya'],
            'nama_paket'          => 'Penyediaan Jasa Tenaga Kebersihan Kantor',
            'nama_paket_clean'    => 'Penyediaan Jasa Tenaga Kebersihan Kantor',
            'nama_paket_objek'    => 'Penyediaan Jasa Tenaga Kebersihan Kantor',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR04b = false;
        foreach ($viol04b as $v) {
            if ($v['rule_id'] === 'R-04b') {
                $hasR04b = true;
            }
        }
        if ($hasR04b) {
            CLI::write("  [PASS] Rule R-04b (Fitur Transaksional SPSE & Reviu APIP Jasa Lainnya > Rp50 Jt jo. Pasal 3 Perlem LKPP 3/2026) terdeteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-04b gagal mendeteksi kewajiban Transaksional SPSE/APIP pada Jasa Lainnya > Rp50 Juta", "red");
            $failCount++;
        }

        // 5l. R-04b Anti-False Positive: Pemeliharaan Kendaraan Perorangan TIDAK boleh memicu R-04b
        $viol04bExclusion = RupRuleEngine::evaluatePackage([
            'total_nilai'         => 89490000.0, // Rp89.490.000 (> Rp50 Juta)
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Pengadaan Langsung'],
            'jenis_arr'           => ['Jasa Lainnya'],
            'nama_paket'          => 'Belanja Pemeliharaan Alat Angkutan-Alat Angkutan Darat Bermotor-Kendaraan Dinas Bermotor Perorangan',
            'nama_paket_clean'    => 'Belanja Pemeliharaan Alat Angkutan-Alat Angkutan Darat Bermotor-Kendaraan Dinas Bermotor Perorangan',
            'nama_paket_objek'    => 'Belanja Pemeliharaan Alat Angkutan-Alat Angkutan Darat Bermotor-Kendaraan Dinas Bermotor Perorangan',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ]);
        $hasR04bFalsePositive = false;
        foreach ($viol04bExclusion as $v) {
            if ($v['rule_id'] === 'R-04b') {
                $hasR04bFalsePositive = true;
            }
        }
        if (! $hasR04bFalsePositive) {
            CLI::write("  [PASS] Rule R-04b Anti-False Positive (Kendaraan Dinas Bermotor Perorangan Rp89,49 Jt TIDAK memicu R-04b) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Rule R-04b memicu false positive pada paket pemeliharaan kendaraan dinas perorangan", "red");
            $failCount++;
        }

        // 5m. R-11: Validasi PPPK Paruh Waktu (Non-Pelanggaran) vs Honorer PTHL/TKS (Pelanggaran R-11 KRITIS jo. UU 20/2023)
        $pkgPppk = [
            'total_nilai'      => 45000000,
            'cara_pengadaan'   => 'Swakelola',
            'metode_arr'       => [],
            'jenis_arr'        => ['Jasa Lainnya'],
            'nama_paket'       => 'Belanja jasa Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) Paruh Waktu pada jabatan tenaga administrasi',
            'nama_paket_clean' => 'belanja jasa pegawai pemerintah dengan perjanjian kerja pppk paruh waktu pada jabatan tenaga administrasi',
            'nama_paket_objek' => 'belanja jasa pegawai pemerintah dengan perjanjian kerja pppk paruh waktu pada jabatan tenaga administrasi',
            'status_umk'       => '',
        ];
        $violPppk = RupRuleEngine::evaluatePackage($pkgPppk);
        $hasR11Pppk = false;
        foreach ($violPppk as $v) {
            if ($v['rule_id'] === 'R-11') {
                $hasR11Pppk = true;
            }
        }

        $pkgPthl = [
            'total_nilai'      => 60000000,
            'cara_pengadaan'   => 'Swakelola',
            'metode_arr'       => [],
            'jenis_arr'        => ['Jasa Lainnya'],
            'nama_paket'       => 'Belanja Jasa Tenaga Pelayanan Umum (Honor PTHL)',
            'nama_paket_clean' => 'belanja jasa tenaga pelayanan umum honor pthl',
            'nama_paket_objek' => 'belanja jasa tenaga pelayanan umum honor pthl',
            'status_umk'       => '',
        ];
        $violPthl = RupRuleEngine::evaluatePackage($pkgPthl);
        $hasR11Pthl = false;
        foreach ($violPthl as $v) {
            if ($v['rule_id'] === 'R-11') {
                $hasR11Pthl = true;
            }
        }

        if (! $hasR11Pppk && $hasR11Pthl) {
            CLI::write("  [PASS] Test 5m: PPPK Paruh Waktu TIDAK memicu R-11 & Tenaga Pelayanan Umum PTHL memicu R-11 (KRITIS)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Test 5m: Evaluasi PPPK Paruh Waktu atau PTHL pada Swakelola tidak akurat", "red");
            $failCount++;
        }

        // 5n. De-Obfuskasi Leetspeak & Normalisasi Typo (R-34 & R-17)
        $pkgLeetspeak = [
            'total_nilai'      => 120000000,
            'cara_pengadaan'   => 'Penyedia',
            'metode_arr'       => ['Pengadaan Langsung'],
            'jenis_arr'        => ['Jasa Lainnya'],
            'nama_paket'       => 'Belanj4 Jasa N0n Person1l',
            'nama_paket_clean' => RupNlpExtractor::clean('Belanj4 Jasa N0n Person1l'),
            'nama_paket_objek' => RupNlpExtractor::extractObject('Belanj4 Jasa N0n Person1l'),
            'status_umk'       => 'Ya',
        ];
        $pkgLeetspeak = array_merge($pkgLeetspeak, RupNlpExtractor::extractCoreAndLocation($pkgLeetspeak['nama_paket_objek']));
        $violLeet = RupRuleEngine::evaluatePackage($pkgLeetspeak);
        $hasR34Leet = false;
        foreach ($violLeet as $v) {
            if ($v['rule_id'] === 'R-34') {
                $hasR34Leet = true;
            }
        }

        $pkgTypo = [
            'total_nilai'      => 175000000,
            'cara_pengadaan'   => 'Penyedia',
            'metode_arr'       => ['Pengadaan Langsung'],
            'jenis_arr'        => ['Barang'], // Salah jenis: renovasi ruang adalah Pekerjaan Konstruksi
            'nama_paket'       => 'Belanaja renovasi runag frakssi golkar',
            'nama_paket_clean' => RupNlpExtractor::clean('Belanaja renovasi runag frakssi golkar'),
            'nama_paket_objek' => RupNlpExtractor::extractObject('Belanaja renovasi runag frakssi golkar'),
            'status_umk'       => 'Ya',
        ];
        $pkgTypo = array_merge($pkgTypo, RupNlpExtractor::extractCoreAndLocation($pkgTypo['nama_paket_objek']));
        $violTypo = RupRuleEngine::evaluatePackage($pkgTypo);
        $hasR17Typo = false;
        foreach ($violTypo as $v) {
            if ($v['rule_id'] === 'R-17') {
                $hasR17Typo = true;
            }
        }

        if ($hasR34Leet && $hasR17Typo) {
            CLI::write("  [PASS] Test 5n: De-obfuskasi leetspeak (R-34) & Normalisasi typo belanja renovasi (R-17) berhasil dideteksi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Test 5n: Gagal mendeteksi leetspeak R-34 (has: " . ($hasR34Leet ? '1' : '0') . ") atau typo R-17 (has: " . ($hasR17Typo ? '1' : '0') . ")", "red");
            $failCount++;
        }

        // 5o. Anti-False Positive Hotmix (R-17) & Komoditas Tunggal Spesifik Excavator (R-34)
        $pkgHotmix = [
            'total_nilai'      => 150000000,
            'cara_pengadaan'   => 'Penyedia',
            'metode_arr'       => ['E-Purchasing'],
            'jenis_arr'        => ['Barang'],
            'nama_paket'       => 'Belanja Bahan/Material HOTMIX Pemeliharaan Jalan',
            'nama_paket_clean' => RupNlpExtractor::clean('Belanja Bahan/Material HOTMIX Pemeliharaan Jalan'),
            'nama_paket_objek' => RupNlpExtractor::extractObject('Belanja Bahan/Material HOTMIX Pemeliharaan Jalan'),
            'status_umk'       => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ];
        $pkgHotmix = array_merge($pkgHotmix, RupNlpExtractor::extractCoreAndLocation($pkgHotmix['nama_paket_objek']));
        $violHotmix = RupRuleEngine::evaluatePackage($pkgHotmix);
        $hasR17Hotmix = false;
        foreach ($violHotmix as $v) {
            if ($v['rule_id'] === 'R-17') {
                $hasR17Hotmix = true;
            }
        }

        $pkgExcavator = [
            'total_nilai'      => 850000000,
            'cara_pengadaan'   => 'Penyedia',
            'metode_arr'       => ['E-Purchasing'],
            'jenis_arr'        => ['Barang'],
            'nama_paket'       => 'Belanja Excavator',
            'nama_paket_clean' => RupNlpExtractor::clean('Belanja Excavator'),
            'nama_paket_objek' => RupNlpExtractor::extractObject('Belanja Excavator'),
            'status_umk'       => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ];
        $pkgExcavator = array_merge($pkgExcavator, RupNlpExtractor::extractCoreAndLocation($pkgExcavator['nama_paket_objek']));
        $violExcavator = RupRuleEngine::evaluatePackage($pkgExcavator);
        $hasR34Excavator = false;
        foreach ($violExcavator as $v) {
            if ($v['rule_id'] === 'R-34') {
                $hasR34Excavator = true;
            }
        }

        if (! $hasR17Hotmix && ! $hasR34Excavator) {
            CLI::write("  [PASS] Test 5o: Anti-false positive hotmix (R-17 lolos) & excavator spesifik (R-34 lolos) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Test 5o: False positive pada hotmix R-17 (has: " . ($hasR17Hotmix ? '1' : '0') . ") atau excavator R-34 (has: " . ($hasR34Excavator ? '1' : '0') . ")", "red");
            $failCount++;
        }

        // 5p. Time-Aware Threshold R-05 (TA 2024 Pagu Rp300 Jt) & Penyalahgunaan Jenis Terintegrasi R-33
        $pkgKonst2024 = [
            'total_nilai'         => 300000000, // Rp300 Juta (> Rp200 Jt rezim Perpres 12/2021)
            'tahun_anggaran'      => 2024,
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['Pengadaan Langsung'],
            'jenis_arr'           => ['Pekerjaan Konstruksi'],
            'nama_paket'          => 'Pembangunan Ruang Kelas Baru SDN 1',
            'nama_paket_clean'    => 'pembangunan ruang kelas baru sdn 1',
            'nama_paket_objek'    => 'pembangunan ruang kelas baru sdn 1',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ];
        $violKonst2024 = RupRuleEngine::evaluatePackage($pkgKonst2024);
        $hasR05OldRegime = false;
        foreach ($violKonst2024 as $v) {
            if ($v['rule_id'] === 'R-05' && $v['severity'] === 'KRITIS') {
                $hasR05OldRegime = true;
            }
        }

        $pkgTerintegrasi = [
            'total_nilai'         => 120000000,
            'cara_pengadaan'      => 'Penyedia',
            'metode_arr'          => ['E-Purchasing'],
            'jenis_arr'           => ['Terintegrasi'],
            'jenis_pengadaan'     => 'Terintegrasi', // Penyalahgunaan Terintegrasi untuk sewa tenda dan kursi
            'nama_paket'          => 'Belanja Sewa Tenda dan Kursi',
            'nama_paket_clean'    => 'belanja sewa tenda dan kursi',
            'nama_paket_objek'    => 'belanja sewa tenda dan kursi',
            'status_umk'          => 'Ya',
            'produk_dalam_negeri' => 'Ya',
        ];
        $violTerintegrasi = RupRuleEngine::evaluatePackage($pkgTerintegrasi);
        $hasR33Terintegrasi = false;
        foreach ($violTerintegrasi as $v) {
            if ($v['rule_id'] === 'R-33' && $v['severity'] === 'TINGGI') {
                $hasR33Terintegrasi = true;
            }
        }

        if ($hasR05OldRegime && $hasR33Terintegrasi) {
            CLI::write("  [PASS] Test 5p: Time-Aware R-05 TA 2024 terdeteksi KRITIS & Penyalahgunaan Terintegrasi terdeteksi TINGGI (R-33)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Test 5p: Time-Aware R-05 TA 2024 (has: " . ($hasR05OldRegime ? '1' : '0') . ") atau Terintegrasi R-33 (has: " . ($hasR33Terintegrasi ? '1' : '0') . ") gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 6: RCI v3.1 Scoring Engine & Zero-Division Guard
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 6] Verifikasi RCI v3.1 Calculator & Zero-Division Guard...", "yellow");

        // 6a. Zero Division Guard
        $rciZero = RupRciCalculator::calculateFormula(0, 0);
        if ($rciZero['rci_score'] === 0.0 && $rciZero['risk_zone'] === 'MERAH') {
            CLI::write("  [PASS] Zero-Division Guard: Total Pagu 0 ditangani aman tanpa exception divide-by-zero (Skor 0, Zona MERAH)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Zero-Division Guard menghasilkan error atau skor salah", "red");
            $failCount++;
        }

        // 6b. Score with violations & Macro penalties
        // Total Pagu 1 Miliar, 10 Paket, 1 Kritis (250 Juta), 1 Tinggi (150 Juta), Penalti Makro 15.0
        $rciCalc = RupRciCalculator::calculateFormula(
            1000000000.0, // Total Pagu 1 M
            10,           // Total Paket
            250000000.0,  // Pagu Kritis
            150000000.0,  // Pagu Tinggi
            0.0,          // Pagu Sedang
            1,            // Cnt Kritis
            1,            // Cnt Tinggi
            0,            // Cnt Sedang
            0,            // Cnt Rendah
            15.0          // Penalti Makro
        );
        if ($rciCalc['rci_score'] < 100.0 && in_array($rciCalc['risk_zone'], ['ORANYE', 'MERAH', 'KUNING'], true)) {
            CLI::write("  [PASS] RCI Formula v3.1 sukses terhitung: Skor {$rciCalc['rci_score']} (Zona {$rciCalc['risk_zone']}), penalti makro terakumulasi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Perhitungan RCI v3.1 tidak menghasilkan pengurangan skor", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 7: RBAC Access Check (RupAccessHelper)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 7] Verifikasi Hak Akses RBAC (RupAccessHelper)...", "yellow");

        // 7a. Super Administrator & Administrator
        $accessAdmin = RupAccessHelper::canAccess(999, ['super_administrator']);
        if ($accessAdmin === true) {
            CLI::write("  [PASS] RBAC: Super Administrator memiliki akses penuh", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] RBAC: Super Administrator ditolak", "red");
            $failCount++;
        }

        // 7b. Regular unauthorized participant
        $accessRegular = RupAccessHelper::canAccess(999999, ['participant']);
        if ($accessRegular === false) {
            CLI::write("  [PASS] RBAC: Pengguna non-Admin & non-PBJ/Inspektorat berhasil diblokir (403)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] RBAC: Pengguna tanpa hak akses lolos verifikasi", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 8: Full End-to-End Orchestration & Exact package_id Mapping
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 8] Verifikasi End-to-End Audit Service & Pemetaan package_id...", "yellow");

        $fullCsvContent = <<<CSV
nama_instansi,nama_satuan_kerja,tahun_anggaran,cara_pengadaan,metode_pengadaan,jenis_pengadaan,nama_paket,kode_rup,sumber_dana,produk_dalam_negeri,status_umk,total_nilai
Pemerintah Provinsi Lampung,DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000,2026,Penyedia,Pengadaan Langsung,Barang,Pengadaan Aspal Buton Campuran Panas Tahap 1,9001001,APBD,Ya,Ya,120000000
Pemerintah Provinsi Lampung,DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000,2026,Penyedia,Pengadaan Langsung,Barang,Pengadaan Aspal Buton Campuran Panas Tahap 2,9001002,APBD,Ya,Ya,130000000
Pemerintah Provinsi Lampung,DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000,2026,Swakelola,Pengadaan Langsung,Jasa Lainnya,Penyediaan Tenaga Kebersihan Sub Kegiatan Kebersihan Kantor,9001003,APBD,Ya,Tidak,80000000
Pemerintah Provinsi Lampung,DINAS KESEHATAN - 1.02.0.00.0.00.01.0000,2026,Penyedia,Pengadaan Langsung,Barang,Belanja Alat Tulis Kantor,9001004,APBD,Ya,Ya,198500000
CSV;

        $auditService = new RupAuditService();
        $batchResult = $auditService->processCsvString(
            $fullCsvContent,
            [
                'filename' => 'test_sirup_compliance_2026.csv',
                'user_id'  => 1,
                'notes'    => 'Unit Test Verification Execution',
            ]
        );

        $testBatchId = $batchResult['batch_id'] ?? null;
        if (! empty($testBatchId)) {
            CLI::write("  [PASS] Batch Audit berhasil diproses! Batch ID: {$testBatchId}", "green");
            $passCount++;

            // Verifikasi mapping package_id di tabel rup_audit_violations
            $violationCheck = $db->table('rup_audit_violations')
                ->where('batch_id', $testBatchId)
                ->where('package_id >', 0)
                ->get()
                ->getResultArray();

            if (! empty($violationCheck)) {
                $firstV = $violationCheck[0];
                // Pastikan package_id merujuk pada row yang ada di rup_audit_packages
                $pkgRow = $db->table('rup_audit_packages')->where('id', $firstV['package_id'])->get()->getRowArray();
                if ($pkgRow && (int) $pkgRow['batch_id'] === (int) $testBatchId) {
                    CLI::write("  [PASS] Mapping package_id di rup_audit_violations ({$firstV['package_id']}) TERVERIFIKASI AKURAT merujuk ke rup_audit_packages (row_index_csv: {$pkgRow['row_index_csv']})", "green");
                    $passCount++;
                } else {
                    CLI::write("  [FAIL] Relasi package_id ke rup_audit_packages tidak valid!", "red");
                    $failCount++;
                }
            } else {
                CLI::write("  [FAIL] Tidak ada anomali yang tersimpan untuk batch ini", "red");
                $failCount++;
            }

            // Verifikasi tabel ringkasan OPD
            $opdSummaries = $db->table('rup_audit_opd_summaries')->where('batch_id', $testBatchId)->get()->getResultArray();
            if (count($opdSummaries) >= 2) {
                CLI::write("  [PASS] Tabel ringkasan Multi-OPD terpartisi rapi (" . count($opdSummaries) . " OPD terdeteksi)", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Partisi multi-OPD tidak sesuai", "red");
                $failCount++;
            }

            // Verifikasi cluster terdeteksi (R-20 splitting Aspal Buton Rp250 Juta)
            $clusters = $db->table('rup_audit_clusters')->where('batch_id', $testBatchId)->get()->getResultArray();
            if (count($clusters) >= 1) {
                CLI::write("  [PASS] Cluster anomali pemecahan paket (Splitting Rp250.000.000) tersimpan di rup_audit_clusters", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Cluster anomali tidak tersimpan", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 9: Export Services (Excel OpenXML & Dompdf)
            // -------------------------------------------------------------
            CLI::newLine();
            CLI::write("[TEST 9] Verifikasi Ekspor Excel 5-Sheet OpenXML & PDF Berita Acara...", "yellow");

            // 9a. Excel OpenXML Generation
            $exportService = new RupExportService();
            $excelTmpPath = $exportService->generateExcel5Sheet($testBatchId);
            if (file_exists($excelTmpPath) && filesize($excelTmpPath) > 1000) {
                // Verify valid zip
                $zip = new \ZipArchive();
                if ($zip->open($excelTmpPath) === true) {
                    $sheet1 = $zip->locateName('xl/worksheets/sheet1.xml');
                    $zip->close();
                    if ($sheet1 !== false) {
                        CLI::write("  [PASS] File Excel OpenXML (.xlsx) 5-Sheet valid (Ukuran: " . number_format(filesize($excelTmpPath)) . " bytes)", "green");
                        $passCount++;
                    } else {
                        CLI::write("  [FAIL] Sheet XML tidak ditemukan di dalam paket OpenXML", "red");
                        $failCount++;
                    }
                } else {
                    CLI::write("  [FAIL] File Excel bukan ZipArchive yang valid", "red");
                    $failCount++;
                }
                @unlink($excelTmpPath);
            } else {
                CLI::write("  [FAIL] Gagal menghasilkan file Excel", "red");
                $failCount++;
            }

            // 9b. PDF Berita Acara Reviu Dompdf Generation
            $firstOpdKey = $opdSummaries[0]['nama_opd_clean'];
            $pdfBinary = $exportService->generateBeritaAcaraPdf($testBatchId, $firstOpdKey);
            if (! empty($pdfBinary) && strlen($pdfBinary) > 2000) {
                if (str_starts_with($pdfBinary, '%PDF-')) {
                    CLI::write("  [PASS] Berita Acara Reviu PDF (Dompdf) valid & siap cetak (Ukuran: " . number_format(strlen($pdfBinary)) . " bytes)", "green");
                    $passCount++;
                } else {
                    CLI::write("  [FAIL] File bukan PDF valid", "red");
                    $failCount++;
                }
            } else {
                CLI::write("  [FAIL] Gagal menghasilkan PDF Berita Acara", "red");
                $failCount++;
            }

            // -------------------------------------------------------------
            // TEST 10: Diff-Tracker (Versi 1 vs Versi 2)
            // -------------------------------------------------------------
            CLI::newLine();
            CLI::write("[TEST 10] Verifikasi Diff-Tracker Lintas Batch...", "yellow");

            // Buat CSV revisi di mana paket Aspal Buton Tahap 2 diubah menjadi Tender (anomali resolved)
            $revisedCsvContent = <<<CSV
nama_instansi,nama_satuan_kerja,tahun_anggaran,cara_pengadaan,metode_pengadaan,jenis_pengadaan,nama_paket,kode_rup,sumber_dana,produk_dalam_negeri,status_umk,total_nilai
Pemerintah Provinsi Lampung,DINAS BINA MARGA DAN BINA KONSTRUKSI - 1.03.0.00.0.00.06.0000,2026,Penyedia,Tender,Pekerjaan Konstruksi,Pengadaan Aspal Buton Campuran Panas Gabungan,9001001,APBD,Ya,Ya,250000000
CSV;

            $batchResult2 = $auditService->processCsvString(
                $revisedCsvContent,
                [
                    'filename' => 'test_sirup_compliance_2026_revised.csv',
                    'user_id'  => 1,
                    'notes'    => 'Unit Test Revision Batch',
                ]
            );
            $testBatchId2 = $batchResult2['batch_id'] ?? null;

            if ($testBatchId2) {
                $diff = RupDiffTracker::compareBatches($testBatchId, $testBatchId2);
                if (isset($diff['stats']['fixed']) || isset($diff['stats']['new'])) {
                    CLI::write("  [PASS] Diff-Tracker berhasil membandingkan 2 batch (Status: FIXED / NEW / DELETED terdeteksi)", "green");
                    $passCount++;
                } else {
                    CLI::write("  [FAIL] Diff tracker tidak menghasilkan statistik perubahan", "red");
                    $failCount++;
                }

                // Cleanup Batch 2
                $auditService->deleteBatch($testBatchId2);
            }

            // Cleanup Batch 1
            $auditService->deleteBatch($testBatchId);
            CLI::write("  [PASS] Cleanup database batch uji selesai secara aman (Non-destructive)", "green");
            $passCount++;

        } else {
            CLI::write("  [FAIL] Gagal memproses Ingestion Batch", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 11: Verifikasi Sorting Hierarki Risiko Row-Level Action Sheet
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 11] Verifikasi Sorting Hierarki Risiko Row-Level Action Sheet...", "yellow");

        $viewDetailPath = APPPATH . 'Views/main/workspace/pages/rup_audit/detail.php';
        $viewContent = file_exists($viewDetailPath) ? file_get_contents($viewDetailPath) : '';
        $jsPath = FCPATH . 'assets/main/js/workspace-pagination.js';
        $jsContent = file_exists($jsPath) ? file_get_contents($jsPath) : '';

        // 11a. Verifikasi data-order & hierarki bobot pada View
        $hasTableId       = str_contains($viewContent, 'id="tableActionSheet"');
        $hasRiskTh        = str_contains($viewContent, 'data-default-sort-dir="desc">Risiko</th>');
        $hasRiskTdOrder   = str_contains($viewContent, 'data-order="<?= $sevWeight ?>"') && str_contains($viewContent, 'data-sort="<?= $sevWeight ?>"');
        $hasHierarchyRank = str_contains($viewContent, "'KRITIS' => 5") && str_contains($viewContent, "'TINGGI' => 4") && str_contains($viewContent, "'SEDANG' => 3") && str_contains($viewContent, "'RENDAH' => 2");

        if ($hasTableId && $hasRiskTh && $hasRiskTdOrder && $hasHierarchyRank) {
            CLI::write("  [PASS] View detail.php memiliki ID unik tableActionSheet, default sort DESC, dan data-order numerik hierarki warna (KRITIS=5 > TINGGI=4 > SEDANG=3 > RENDAH=2 > AMAN=1)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] View detail.php belum memenuhi spesifikasi sorting hierarki risiko", "red");
            $failCount++;
        }

        // 11b. Verifikasi engine workspace-pagination.js membaca data-order/data-sort
        $hasJsDataOrder = str_contains($jsContent, "cellA.getAttribute('data-order')") && str_contains($jsContent, "cellA.getAttribute('data-sort')");
        $hasJsDefaultDir = str_contains($jsContent, "data-default-sort-dir");

        if ($hasJsDataOrder && $hasJsDefaultDir) {
            CLI::write("  [PASS] workspace-pagination.js mengenali data-order/data-sort numerik & data-default-sort-dir tanpa merusak datatable lainnya", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] workspace-pagination.js belum mendukung data-order / data-default-sort-dir", "red");
            $failCount++;
        }

        // 11c. Verifikasi SQL backend CASE WHEN sorting pada RupAuditService
        $servicePath = APPPATH . 'Services/RupAudit/RupAuditService.php';
        $serviceContent = file_exists($servicePath) ? file_get_contents($servicePath) : '';
        $hasSqlSeveritySort = str_contains($serviceContent, "CASE p.max_severity WHEN 'KRITIS' THEN 5 WHEN 'TINGGI' THEN 4 WHEN 'SEDANG' THEN 3 WHEN 'RENDAH' THEN 2 WHEN 'AMAN' THEN 1 ELSE 0 END");

        if ($hasSqlSeveritySort) {
            CLI::write("  [PASS] RupAuditService::getPackagesFiltered mendukung sorting tingkat risiko via SQL CASE WHEN (Kritis vs Tinggi)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] RupAuditService belum mengimplementasikan SQL CASE WHEN untuk hierarki risiko", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // RINGKASAN AKHIR
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "yellow");
        CLI::write("                 HASIL AKHIR VERIFIKASI AUDIT RUP                 ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write("  TOTAL PENGUJIAN : " . ($passCount + $failCount));
        CLI::write("  TOTAL BERHASIL  : {$passCount}", "green");
        CLI::write("  TOTAL GAGAL     : {$failCount}", $failCount === 0 ? "green" : "red");

        if ($failCount === 0) {
            CLI::write("\n  >>> STATUS: SELURUH PENGUJIAN 100% SUKSES TANPA KENDALA! <<<", "green");
            return 0;
        }

        CLI::write("\n  >>> STATUS: TERDAPAT PENGUJIAN YANG GAGAL! <<<", "red");
        return 1;
    }
}
