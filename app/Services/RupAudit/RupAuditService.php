<?php

namespace App\Services\RupAudit;

use App\Models\RupAuditBatchModel;
use App\Models\RupAuditClusterModel;
use App\Models\RupAuditOpdSummaryModel;
use App\Models\RupAuditPackageModel;
use App\Models\RupAuditViolationModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class RupAuditService
{
    protected BaseConnection $db;
    protected RupAuditBatchModel $batchModel;
    protected RupAuditOpdSummaryModel $opdModel;
    protected RupAuditPackageModel $pkgModel;
    protected RupAuditViolationModel $violModel;
    protected RupAuditClusterModel $clusterModel;

    public function __construct()
    {
        $this->db           = Database::connect();
        $this->batchModel   = new RupAuditBatchModel();
        $this->opdModel     = new RupAuditOpdSummaryModel();
        $this->pkgModel     = new RupAuditPackageModel();
        $this->violModel    = new RupAuditViolationModel();
        $this->clusterModel = new RupAuditClusterModel();
    }

    /**
     * Memproses konten CSV dari string mentah.
     *
     * @param string $csvContent
     * @param array $meta
     * @return array
     */
    public function processCsvString(string $csvContent, array $meta = []): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'rup_str_');
        file_put_contents($tmp, $csvContent);
        if (empty($meta['filename'])) {
            $meta['filename'] = 'sirup_import_' . date('Ymd_His') . '.csv';
        }
        if (empty($meta['file_size'])) {
            $meta['file_size'] = strlen($csvContent);
        }
        $res = $this->processUpload($tmp, $meta);
        @unlink($tmp);
        return $res;
    }

    /**
     * Menjalankan seluruh alur audit kepatuhan RUP secara end-to-end.
     *
     * @param string $csvFilePath Lokasi absolut berkas CSV
     * @param array $meta Metadata tambahan (user_id, previous_batch_id, filename, file_size, notes)
     * @return array{success: bool, batch_id?: int, batch_uuid?: string, error?: string}
     */
    public function processUpload(string $csvFilePath, array $meta = []): array
    {
        @set_time_limit(0);
        ini_set('memory_limit', '512M');

        $fileSha256 = file_exists($csvFilePath) ? hash_file('sha256', $csvFilePath) : '';

        // 1. Parsing CSV dengan 12 Kolom Kontrak Ketat
        $parser = new RupCsvParser();
        $parseResult = $parser->parse($csvFilePath);
        if (! $parseResult['success']) {
            return [
                'success' => false,
                'error'   => $parseResult['error'] ?? 'Gagal memproses berkas CSV RUP.',
            ];
        }

        $packages     = $parseResult['rows'];
        $rawRowsCount = $parseResult['total_raw_rows'];
        $schemaMode   = $parseResult['schema_mode'];
        $schemaReport = $parseResult['schema_report'] ?? [];

        if (empty($packages)) {
            return [
                'success' => false,
                'error'   => 'Berkas CSV tidak memuat baris data pengadaan yang dapat diaudit.',
            ];
        }

        // 2. Graph-Based Clustering dengan Partisi Ketat & Centroid Check
        $clusterer = new RupGraphClusterer();
        $clusters  = $clusterer->cluster($packages);

        // 3. Multi-Tier Rule Engine Evaluation & Macro Indicators v4.0
        $ruleEngine = new RupRuleEngine();
        $evaluation = $ruleEngine->evaluateAll($packages, $clusters);

        $violations   = $evaluation['violations'];
        $opdSummaries = $evaluation['opd_summaries'];
        $macroSummary = $evaluation['macro_institution'];

        // 4. Two-Stage Diff-Tracking vs Previous Batch
        $diffTracker  = new RupDiffTracker();
        $prevBatchId  = isset($meta['previous_batch_id']) && $meta['previous_batch_id'] > 0 ? (int) $meta['previous_batch_id'] : null;
        $diffSummary  = $diffTracker->compare($packages, $prevBatchId);

        if ($diffSummary['is_comparison_active'] && isset($diffSummary['prev_rci_score'])) {
            $delta = round($macroSummary['rata_rata_rci_instansi'] - $diffSummary['prev_rci_score'], 2);
            $diffSummary['delta_skor_rci_instansi'] = ($delta >= 0 ? '+' : '') . number_format($delta, 2);
        }

        $macroSummary['diff_tracking'] = $diffSummary;

        // 5. Persistensi Data ke Basis Data
        $this->db->transStart();

        $batchUuid = 'RUP-' . date('Ymd') . '-' . substr(bin2hex(random_bytes(6)), 0, 8);
        $firstPkg  = $packages[0];

        $dataQualitySummary = [
            'total_rows_read'       => $rawRowsCount,
            'valid_packages'        => count($packages),
            'header_columns_count'  => count($schemaReport['detected_headers'] ?? []),
            'delimiter'             => $schemaReport['detected_delimiter'] ?? ',',
            'has_bom'               => ! empty($schemaReport['has_bom']),
            'extra_columns_ignored' => $schemaReport['extra_columns_ignored'] ?? [],
        ];

        // Simpan metadata v4 ke dalam macro_summary JSON (Zero DB Migration)
        $macroSummary['input_schema_version']      = '4.0';
        $macroSummary['rule_set_version']          = '4.0.0';
        $macroSummary['file_sha256']               = $fileSha256;
        $macroSummary['source_row_count']          = $rawRowsCount;
        $macroSummary['data_quality_summary']      = $dataQualitySummary;

        $batchData = [
            'batch_uuid'                 => $batchUuid,
            'user_id'                    => $meta['user_id'] ?? null,
            'filename'                   => $meta['filename'] ?? basename($csvFilePath),
            'file_size'                  => $meta['file_size'] ?? (file_exists($csvFilePath) ? filesize($csvFilePath) : 0),
            'total_rows_raw'             => $rawRowsCount,
            'total_packages'             => count($packages),
            'total_pagu'                 => array_sum(array_column($packages, 'total_nilai')),
            'total_pagu_penyedia'        => $macroSummary['pagu_penyedia'] ?? 0.0,
            'total_pagu_swakelola'       => $macroSummary['pagu_swakelola'] ?? 0.0,
            'total_opd'                  => count($opdSummaries),
            'avg_rci_score'              => $macroSummary['rata_rata_rci_instansi'] ?? 0.0,
            'status'                     => 'completed',
            'previous_batch_id'          => $prevBatchId,
            'macro_summary'              => json_encode($macroSummary),
            'schema_mode'                => $schemaMode,
            'tahun_anggaran'             => (int) ($firstPkg['tahun_anggaran'] ?? date('Y')),
            'nama_instansi'              => $firstPkg['nama_instansi'] ?? 'Pemerintah Daerah',
            'notes'                      => $meta['notes'] ?? null,
            'created_at'                 => date('Y-m-d H:i:s'),
            'updated_at'                 => date('Y-m-d H:i:s'),
        ];

        $this->batchModel->insert($batchData);
        $batchId = (int) $this->db->insertID();

        // 5.1 Insert OPD Summaries
        $opdSummaryMap = [];
        foreach ($opdSummaries as $opdName => $sumData) {
            $sumData['batch_id'] = $batchId;
            if (isset($sumData['risk_profile_json'])) {
                $mFlags = ! empty($sumData['macro_flags']) ? (is_array($sumData['macro_flags']) ? $sumData['macro_flags'] : json_decode((string) $sumData['macro_flags'], true)) : [];
                $mFlags['risk_profile'] = is_array($sumData['risk_profile_json']) ? $sumData['risk_profile_json'] : json_decode((string) $sumData['risk_profile_json'], true);
                $sumData['macro_flags'] = json_encode($mFlags);
                unset($sumData['risk_profile_json']);
            }
            $this->opdModel->insert($sumData);
            $opdSummaryMap[$opdName] = (int) $this->db->insertID();
        }

        // 5.2 Insert Packages in Chunks (500 per batch)
        $packageChunk = [];
        $now = date('Y-m-d H:i:s');
        foreach ($packages as $pkg) {
            $opdClean = $pkg['nama_opd_clean'] ?? 'OPD_UNKNOWN';

            // Map diff_status to valid enum Batch 16
            $diffStatus = $pkg['diff_status'] ?? 'NEW';
            if (! in_array($diffStatus, ['NEW', 'PERSISTENT', 'FIXED', 'DELETED'], true)) {
                $diffStatus = 'PERSISTENT';
            }

            // Map max_severity to valid enum Batch 16
            $maxSev = $pkg['max_severity'] ?? 'AMAN';
            if (! in_array($maxSev, ['AMAN', 'RENDAH', 'SEDANG', 'TINGGI', 'KRITIS'], true)) {
                $maxSev = 'AMAN';
            }

            $packageChunk[] = [
                'batch_id'                      => $batchId,
                'opd_summary_id'                => $opdSummaryMap[$opdClean] ?? null,
                'row_index_csv'                 => $pkg['row_index_csv'],
                'kode_rup'                      => (string) ($pkg['kode_rup'] ?? ''),
                'nama_instansi'                 => $pkg['nama_instansi'] ?? '',
                'nama_satuan_kerja'             => $pkg['nama_satuan_kerja'] ?? '',
                'nama_opd_clean'                => $pkg['nama_opd_clean'] ?? '',
                'kode_satker_sipd'              => $pkg['kode_satker_sipd'] ?? '',
                'tahun_anggaran'                => $pkg['tahun_anggaran'] ?? date('Y'),
                'cara_pengadaan'                => $pkg['cara_pengadaan'] ?? 'Penyedia',
                'metode_pengadaan'              => $pkg['metode_pengadaan'] ?? '-',
                'metode_arr'                    => json_encode($pkg['metode_arr'] ?? []),
                'jenis_pengadaan'               => $pkg['jenis_pengadaan'] ?? '-',
                'jenis_arr'                     => json_encode($pkg['jenis_arr'] ?? []),
                'nama_paket'                    => $pkg['nama_paket'] ?? '',
                'nama_paket_clean'              => $pkg['nama_paket_clean'] ?? '',
                'nama_paket_objek'              => $pkg['nama_paket_objek'] ?? '',
                'nama_paket_core'               => $pkg['nama_paket_core'] ?? '',
                'lokasi_extracted'              => $pkg['lokasi_extracted'] ?? ($pkg['location_identity_key'] ?? null),
                'sumber_dana'                   => $pkg['source_funding_profile'] ?? $pkg['sumber_dana'] ?? 'APBD',
                'sumber_dana_arr'               => json_encode($pkg['sumber_dana_arr'] ?? []),
                'budget_cycle'                  => $pkg['budget_cycle'] ?? 'MURNI',
                'is_blud'                       => ! empty($pkg['is_blud']) ? 1 : 0,
                'is_dak'                        => ! empty($pkg['is_dak']) ? 1 : 0,
                'is_multiyears'                 => ! empty($pkg['is_multiyears']) ? 1 : 0,
                'is_scientific_truncated'       => ! empty($pkg['is_scientific_truncated']) ? 1 : 0,
                'is_integrated_package'         => ! empty($pkg['is_integrated_package']) ? 1 : 0,
                'produk_dalam_negeri'           => $pkg['produk_dalam_negeri'] ?? 'Ya',
                'status_umk'                    => $pkg['status_umk'] ?? 'Ya',
                'total_nilai'                   => (float) ($pkg['total_nilai'] ?? 0.0),
                'cluster_id'                    => $pkg['cluster_id'] ?? null,
                'max_severity'                  => $maxSev,
                'violation_count'               => (int) ($pkg['violation_count'] ?? 0),
                'diff_status'                   => $diffStatus,
                'tanggal_umumkan'               => $pkg['tanggal_umumkan'] ?? null,
                'jadwal_pemilihan_awal'         => $pkg['jadwal_pemilihan_awal'] ?? null,
                'jadwal_kontrak_awal'           => $pkg['jadwal_kontrak_awal'] ?? null,
                'jadwal_kontrak_akhir'          => $pkg['jadwal_kontrak_akhir'] ?? null,
                'tipe_swakelola'                => $pkg['tipe_swakelola'] ?? null,
                'lokasi_pekerjaan'              => $pkg['lokasi_pekerjaan'] ?? null,
                'kode_rekening_mak'             => $pkg['kode_rekening_mak'] ?? null,
                'pagu_pengadaan_dpa'            => $pkg['pagu_pengadaan_dpa'] ?? null,
                'created_at'                    => $now,
            ];

            if (count($packageChunk) >= 500) {
                $this->db->table('rup_audit_packages')->insertBatch($packageChunk);
                $packageChunk = [];
            }
        }
        if (! empty($packageChunk)) {
            $this->db->table('rup_audit_packages')->insertBatch($packageChunk);
            $packageChunk = [];
        }

        // 5.3 MAPPING PACKAGE_ID berbasis row_index_csv
        $idRows = $this->db->table('rup_audit_packages')
            ->select('id, row_index_csv')
            ->where('batch_id', $batchId)
            ->get()->getResultArray();

        $rowIndexToPackageId = [];
        foreach ($idRows as $ir) {
            $rowIndexToPackageId[(int) $ir['row_index_csv']] = (int) $ir['id'];
        }

        // 5.4 Insert Violations in Chunks v4.0 (Zero DB Migration: Metadata v4 dipaketkan di target_perubahan)
        $violationChunk = [];
        foreach ($violations as $v) {
            $rIndex = (int) ($v['row_index_csv'] ?? 0);
            $pkgId  = $rowIndexToPackageId[$rIndex] ?? null;
            if (! $pkgId) {
                continue;
            }

            $v4Payload = [
                'finding_status'            => $v['finding_status'] ?? 'POTENSI_TIDAK_SESUAI',
                'severity'                  => $v['severity'],
                'confidence_level'          => $v['confidence_level'] ?? 'TINGGI',
                'evidence'                  => is_string($v['evidence_json'] ?? null) ? json_decode($v['evidence_json'], true) : ($v['evidence_json'] ?? null),
                'limitation_note'           => $v['limitation_note'] ?? null,
                'regulatory_basis'          => is_string($v['regulatory_basis_json'] ?? null) ? json_decode($v['regulatory_basis_json'], true) : ($v['regulatory_basis_json'] ?? null),
                'recommendation_text'       => $v['recommendation_text'] ?? $v['rekomendasi_perbaikan'] ?? null,
                'suggested_correction'      => is_string($v['suggested_correction_json'] ?? null) ? json_decode($v['suggested_correction_json'], true) : ($v['suggested_correction_json'] ?? ($v['target_perubahan'] ?? null)),
            ];

            $sevEnum = in_array($v['severity'], ['KRITIS', 'TINGGI', 'SEDANG', 'RENDAH'], true) ? $v['severity'] : 'RENDAH';

            $violationChunk[] = [
                'batch_id'              => $batchId,
                'package_id'            => $pkgId,
                'rule_id'               => $v['rule_id'],
                'severity'              => $sevEnum,
                'kategori'              => $v['kategori'],
                'regulasi'              => $v['regulasi'],
                'deskripsi_pelanggaran' => $v['deskripsi_pelanggaran'],
                'rekomendasi_perbaikan' => $v['rekomendasi_perbaikan'],
                'target_perubahan'      => json_encode($v4Payload),
                'created_at'            => $now,
            ];

            if (count($violationChunk) >= 500) {
                $this->db->table('rup_audit_violations')->insertBatch($violationChunk);
                $violationChunk = [];
            }
        }
        if (! empty($violationChunk)) {
            $this->db->table('rup_audit_violations')->insertBatch($violationChunk);
            $violationChunk = [];
        }

        // 5.5 Insert Clusters v4.0 (Zero DB Migration)
        $clusterChunk = [];
        foreach ($clusters as $cls) {
            $daftarKodeRup = [
                'rups'                    => is_string($cls['daftar_kode_rup'] ?? '') ? json_decode($cls['daftar_kode_rup'], true) : ($cls['daftar_kode_rup'] ?? []),
                'cluster_type'            => $cls['cluster_type'] ?? 'KOMODITAS_SEJENIS',
                'cluster_confidence'      => $cls['cluster_confidence'] ?? 'SEDANG',
                'serial_family_count'     => $cls['serial_family_count'] ?? 0,
                'cluster_limitation_note' => $cls['cluster_limitation_note'] ?? null,
            ];

            $sevCls = in_array($cls['severity'] ?? '', ['KRITIS', 'TINGGI', 'SEDANG'], true) ? $cls['severity'] : 'TINGGI';

            $clusterChunk[] = [
                'batch_id'               => $batchId,
                'cluster_uuid'           => $cls['cluster_uuid'],
                'kode_satker_sipd'       => $cls['kode_satker_sipd'] ?? '',
                'nama_opd_clean'         => $cls['nama_opd_clean'],
                'budget_cycle'           => $cls['budget_cycle'] ?? 'MURNI',
                'nama_komoditas_klaster' => $cls['nama_komoditas_klaster'],
                'lokasi_klaster'         => $cls['lokasi_klaster'] ?? 'ALL',
                'jenis_pengadaan'        => $cls['jenis_pengadaan'],
                'metode_dominan'         => $cls['metode_dominan'],
                'jumlah_paket_terpecah'  => (int) ($cls['jumlah_paket_terpecah'] ?? 0),
                'nilai_paket_terkecil'   => (float) ($cls['nilai_paket_terkecil'] ?? 0.0),
                'nilai_paket_terbesar'   => (float) ($cls['nilai_paket_terbesar'] ?? 0.0),
                'total_pagu_klaster'     => (float) ($cls['total_pagu_klaster'] ?? 0.0),
                'batas_regulasi_pl'      => (float) ($cls['batas_regulasi_pl'] ?? 200000000.0),
                'kategori_temuan'        => $cls['kategori_temuan'],
                'severity'               => $sevCls,
                'daftar_kode_rup'        => json_encode($daftarKodeRup),
                'instruksi_konsolidasi'  => $cls['instruksi_konsolidasi'],
                'created_at'             => $now,
            ];

            if (count($clusterChunk) >= 500) {
                $this->db->table('rup_audit_clusters')->insertBatch($clusterChunk);
                $clusterChunk = [];
            }
        }
        if (! empty($clusterChunk)) {
            $this->db->table('rup_audit_clusters')->insertBatch($clusterChunk);
            $clusterChunk = [];
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return [
                'success' => false,
                'error'   => 'Terjadi kesalahan basis data saat menyimpan hasil audit RUP.',
            ];
        }

        return [
            'success'    => true,
            'batch_id'   => $batchId,
            'batch_uuid' => $batchUuid,
        ];
    }

    /**
     * Ambil riwayat batch audit.
     */
    public function getBatches(int $limit = 20): array
    {
        return $this->batchModel
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Ambil detail batch berdasarkan ID atau UUID.
     */
    public function getBatch(int|string $idOrUuid): ?array
    {
        $batch = is_numeric($idOrUuid)
            ? $this->batchModel->find((int) $idOrUuid)
            : $this->batchModel->where('batch_uuid', $idOrUuid)->first();

        if ($batch && ! empty($batch['macro_summary'])) {
            $ms = json_decode((string) $batch['macro_summary'], true);
            if (is_array($ms)) {
                $batch['file_sha256']               = $batch['file_sha256'] ?? $ms['file_sha256'] ?? '';
                $batch['input_schema_version']      = $batch['input_schema_version'] ?? $ms['input_schema_version'] ?? '4.0';
                $batch['rule_set_version']          = $batch['rule_set_version'] ?? $ms['rule_set_version'] ?? '4.0.0';
                $batch['source_row_count']          = $batch['source_row_count'] ?? $ms['source_row_count'] ?? $batch['total_rows_raw'];
                $batch['data_quality_summary_json'] = $batch['data_quality_summary_json'] ?? (! empty($ms['data_quality_summary']) ? json_encode($ms['data_quality_summary']) : '{}');
            }
        }

        return $batch;
    }

    /**
     * Ambil rekapitulasi OPD untuk Tab 1 Leaderboard.
     */
    public function getOpdLeaderboard(int $batchId, ?string $predikat = null): array
    {
        $builder = $this->db->table('rup_audit_opd_summaries')
            ->where('batch_id', $batchId);

        if ($predikat && in_array(strtoupper($predikat), ['HIJAU', 'KUNING', 'ORANYE', 'MERAH'], true)) {
            $builder->where('predikat_risiko', strtoupper($predikat));
        }

        $rows = $builder->orderBy('pagu_bermasalah_kritis_tinggi', 'DESC')
            ->orderBy('skor_rci', 'ASC')
            ->get()->getResultArray();

        foreach ($rows as &$r) {
            if (! empty($r['macro_flags'])) {
                $mf = json_decode((string) $r['macro_flags'], true);
                if (is_array($mf) && isset($mf['risk_profile'])) {
                    if (empty($r['risk_profile_json'])) {
                        $r['risk_profile_json'] = json_encode($mf['risk_profile']);
                    }
                    unset($mf['risk_profile']);
                    $r['macro_flags'] = json_encode(array_values(array_filter($mf, static fn($v) => is_string($v))));
                }
            }
        }
        unset($r);

        return $rows;
    }

    /**
     * Ambil data paket untuk Tab 2 Action Sheet dengan pagination, filter, dan sorting hierarki risiko.
     */
    public function getPackagesFiltered(int $batchId, array $filters = [], int $limit = 100, int $offset = 0, string $sortBy = 'total_nilai', string $sortDir = 'DESC'): array
    {
        $builder = $this->db->table('rup_audit_packages p')
            ->select('p.*, s.skor_rci as opd_skor_rci, s.predikat_risiko as opd_predikat')
            ->join('rup_audit_opd_summaries s', 's.id = p.opd_summary_id', 'left')
            ->where('p.batch_id', $batchId);

        if (! empty($filters['opd'])) {
            $builder->where('p.nama_opd_clean', $filters['opd']);
        }
        if (! empty($filters['severity']) && $filters['severity'] !== 'ALL') {
            $builder->where('p.max_severity', $filters['severity']);
        }
        if (! empty($filters['budget_cycle']) && $filters['budget_cycle'] !== 'ALL') {
            $builder->where('p.budget_cycle', $filters['budget_cycle']);
        }
        if (! empty($filters['diff_status']) && $filters['diff_status'] !== 'ALL') {
            $builder->where('p.diff_status', $filters['diff_status']);
        }
        if (! empty($filters['cara']) && $filters['cara'] !== 'ALL') {
            $builder->where('p.cara_pengadaan', $filters['cara']);
        }
        if (! empty($filters['metode']) && $filters['metode'] !== 'ALL') {
            $builder->where('p.metode_pengadaan', $filters['metode']);
        }
        if (! empty($filters['jenis']) && $filters['jenis'] !== 'ALL') {
            $builder->where('p.jenis_pengadaan', $filters['jenis']);
        }
        if (! empty($filters['search'])) {
            $term = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('p.nama_paket', $term)
                ->orLike('p.kode_rup', $term)
                ->orLike('p.nama_opd_clean', $term)
                ->groupEnd();
        }

        $dir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
        if ($sortBy === 'severity' || $sortBy === 'risiko') {
            $builder->orderBy("CASE p.max_severity WHEN 'KRITIS' THEN 4 WHEN 'TINGGI' THEN 4 WHEN 'SEDANG' THEN 3 WHEN 'RENDAH' THEN 2 WHEN 'INFO' THEN 1 ELSE 0 END", $dir)
                ->orderBy('p.violation_count', $dir)
                ->orderBy('p.total_nilai', 'DESC');
        } elseif ($sortBy === 'pagu' || $sortBy === 'total_nilai') {
            $builder->orderBy('p.total_nilai', $dir);
        } else {
            $builder->orderBy('p.total_nilai', 'DESC');
        }

        return $builder->limit($limit, $offset)
            ->get()->getResultArray();
    }

    /**
     * Hitung total baris paket untuk pagination.
     */
    public function countPackagesFiltered(int $batchId, array $filters = []): int
    {
        $builder = $this->db->table('rup_audit_packages p')
            ->where('p.batch_id', $batchId);

        if (! empty($filters['opd'])) {
            $builder->where('p.nama_opd_clean', $filters['opd']);
        }
        if (! empty($filters['severity']) && $filters['severity'] !== 'ALL') {
            $builder->where('p.max_severity', $filters['severity']);
        }
        if (! empty($filters['budget_cycle']) && $filters['budget_cycle'] !== 'ALL') {
            $builder->where('p.budget_cycle', $filters['budget_cycle']);
        }
        if (! empty($filters['diff_status']) && $filters['diff_status'] !== 'ALL') {
            $builder->where('p.diff_status', $filters['diff_status']);
        }
        if (! empty($filters['cara']) && $filters['cara'] !== 'ALL') {
            $builder->where('p.cara_pengadaan', $filters['cara']);
        }
        if (! empty($filters['metode']) && $filters['metode'] !== 'ALL') {
            $builder->where('p.metode_pengadaan', $filters['metode']);
        }
        if (! empty($filters['jenis']) && $filters['jenis'] !== 'ALL') {
            $builder->where('p.jenis_pengadaan', $filters['jenis']);
        }
        if (! empty($filters['search'])) {
            $term = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('p.nama_paket', $term)
                ->orLike('p.kode_rup', $term)
                ->orLike('p.nama_opd_clean', $term)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Ambil daftar klaster untuk Tab 3 Cluster Matrix.
     */
    public function getClusters(int $batchId, ?string $opd = null): array
    {
        $builder = $this->db->table('rup_audit_clusters')
            ->where('batch_id', $batchId);

        if (! empty($opd)) {
            $builder->where('nama_opd_clean', $opd);
        }

        $rows = $builder->orderBy('total_pagu_klaster', 'DESC')->get()->getResultArray();

        foreach ($rows as &$cls) {
            if (! empty($cls['daftar_kode_rup'])) {
                $dk = json_decode((string) $cls['daftar_kode_rup'], true);
                if (is_array($dk) && isset($dk['cluster_type'])) {
                    $cls['cluster_type']            = $cls['cluster_type'] ?? $dk['cluster_type'];
                    $cls['cluster_confidence']      = $cls['cluster_confidence'] ?? $dk['cluster_confidence'];
                    $cls['serial_family_count']     = $cls['serial_family_count'] ?? $dk['serial_family_count'];
                    $cls['cluster_limitation_note'] = $cls['cluster_limitation_note'] ?? $dk['cluster_limitation_note'];
                    $cls['daftar_kode_rup']         = json_encode($dk['rups'] ?? []);
                }
            }
        }
        unset($cls);

        return $rows;
    }

    /**
     * Ambil rincian pelanggaran per paket untuk modal AJAX.
     */
    public function getPackageViolations(int $packageId): array
    {
        $raw = $this->db->table('rup_audit_violations')
            ->where('package_id', $packageId)
            ->get()->getResultArray();

        foreach ($raw as &$v) {
            if (! empty($v['target_perubahan'])) {
                $payload = json_decode((string) $v['target_perubahan'], true);
                if (is_array($payload)) {
                    $v['finding_status']           = $v['finding_status'] ?? $payload['finding_status'] ?? 'POTENSI_TIDAK_SESUAI';
                    $v['confidence_level']         = $v['confidence_level'] ?? $payload['confidence_level'] ?? 'SEDANG';
                    $v['evidence_json']            = $v['evidence_json'] ?? (! empty($payload['evidence']) ? json_encode($payload['evidence']) : null);
                    $v['limitation_note']          = $v['limitation_note'] ?? $payload['limitation_note'] ?? null;
                    $v['regulatory_basis_json']    = $v['regulatory_basis_json'] ?? (! empty($payload['regulatory_basis']) ? json_encode($payload['regulatory_basis']) : null);
                    $v['recommendation_text']      = $v['recommendation_text'] ?? $payload['recommendation_text'] ?? $v['rekomendasi_perbaikan'] ?? null;
                    $v['suggested_correction_json']= $v['suggested_correction_json'] ?? (! empty($payload['suggested_correction']) ? json_encode($payload['suggested_correction']) : null);
                }
            }
        }
        unset($v);

        return $raw;
    }

    /**
     * Ambil satu baris paket beserta pelanggarannya.
     */
    public function getPackageDetail(int $packageId): ?array
    {
        $pkg = $this->pkgModel->find($packageId);
        if (! $pkg) {
            return null;
        }

        $nlpData = RupNlpExtractor::extractSerialAndLocation($pkg['nama_paket'] ?? '');
        $pkg['serial_sequence_number'] = $pkg['serial_sequence_number'] ?? $nlpData['serial_sequence_number'];
        $pkg['location_identity_key']  = $pkg['location_identity_key'] ?? ($nlpData['location_identity_key'] ?? $pkg['lokasi_extracted']);
        $pkg['source_funding_profile'] = $pkg['source_funding_profile'] ?? ($pkg['sumber_dana'] ?? 'APBD');
        $pkg['regime_status']          = $pkg['regime_status'] ?? ((int) ($pkg['tahun_anggaran'] ?? 2026) === 2026 ? 'KETENTUAN_2026_PERPRES_46_2025' : 'KETENTUAN_TRANSISI');

        $pkg['violations'] = $this->getPackageViolations($packageId);
        return $pkg;
    }

    /**
     * Hapus batch audit beserta seluruh data relasinya.
     */
    public function deleteBatch(int $batchId): bool
    {
        return (bool) $this->batchModel->delete($batchId);
    }
}
