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
        // Guardrail resource limit untuk data multi-OPD provinsi
        @set_time_limit(0);
        ini_set('memory_limit', '512M');

        // 1. Parsing CSV
        $parser = new RupCsvParser();
        $parseResult = $parser->parse($csvFilePath);
        if (! $parseResult['success']) {
            return [
                'success' => false,
                'error'   => $parseResult['error'] ?? 'Gagal memproses berkas CSV RUP.',
            ];
        }

        $packages    = $parseResult['rows'];
        $rawRowsCount = $parseResult['total_raw_rows'];
        $schemaMode  = $parseResult['schema_mode'];

        if (empty($packages)) {
            return [
                'success' => false,
                'error'   => 'Berkas CSV tidak memuat baris data pengadaan yang dapat diaudit.',
            ];
        }

        // 2. Graph-Based Clustering (Jaccard >= 0.78)
        $clusterer = new RupGraphClusterer();
        $clusters  = $clusterer->cluster($packages);

        // 3. Multi-Tier Rule Engine Evaluation & Macro Indicators
        $ruleEngine = new RupRuleEngine();
        $evaluation = $ruleEngine->evaluateAll($packages, $clusters);

        $violations   = $evaluation['violations'];
        $opdSummaries = $evaluation['opd_summaries'];
        $macroSummary = $evaluation['macro_institution'];

        // 4. Diff-Tracking vs Previous Batch
        $diffTracker  = new RupDiffTracker();
        $prevBatchId  = isset($meta['previous_batch_id']) && $meta['previous_batch_id'] > 0 ? (int) $meta['previous_batch_id'] : null;
        $diffSummary  = $diffTracker->compare($packages, $prevBatchId);

        if ($diffSummary['is_comparison_active'] && isset($diffSummary['prev_rci_score'])) {
            $delta = round($macroSummary['rata_rata_rci_instansi'] - $diffSummary['prev_rci_score'], 2);
            $diffSummary['delta_skor_rci_instansi'] = ($delta >= 0 ? '+' : '') . number_format($delta, 2);
        }

        $macroSummary['diff_tracking'] = $diffSummary;

        // 5. Persistensi Data ke Database dalam Transaksi
        $this->db->transStart();

        $batchUuid = 'RUP-' . date('Ymd') . '-' . substr(bin2hex(random_bytes(6)), 0, 8);
        $firstPkg  = $packages[0];

        $batchData = [
            'batch_uuid'            => $batchUuid,
            'user_id'               => $meta['user_id'] ?? null,
            'filename'              => $meta['filename'] ?? basename($csvFilePath),
            'file_size'             => $meta['file_size'] ?? filesize($csvFilePath),
            'total_rows_raw'        => $rawRowsCount,
            'total_packages'        => count($packages),
            'total_pagu'            => array_sum(array_column($packages, 'total_nilai')),
            'total_pagu_penyedia'   => $macroSummary['pagu_penyedia'] ?? 0.0,
            'total_pagu_swakelola'  => $macroSummary['pagu_swakelola'] ?? 0.0,
            'total_opd'             => count($opdSummaries),
            'avg_rci_score'         => $macroSummary['rata_rata_rci_instansi'] ?? 0.0,
            'status'                => 'completed',
            'previous_batch_id'     => $prevBatchId,
            'macro_summary'         => json_encode($macroSummary),
            'schema_mode'           => $schemaMode,
            'tahun_anggaran'        => (int) ($firstPkg['tahun_anggaran'] ?? date('Y')),
            'nama_instansi'         => $firstPkg['nama_instansi'] ?? 'Pemerintah Provinsi Lampung',
            'notes'                 => $meta['notes'] ?? null,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        $this->batchModel->insert($batchData);
        $batchId = (int) $this->db->insertID();

        // 5.1 Insert OPD Summaries & Build Map (nama_opd_clean => opd_summary_id)
        $opdSummaryMap = [];
        foreach ($opdSummaries as $opdName => $sumData) {
            $sumData['batch_id'] = $batchId;
            $this->opdModel->insert($sumData);
            $opdSummaryMap[$opdName] = (int) $this->db->insertID();
        }

        // 5.2 Insert Packages in Chunks (500 per batch)
        $packageChunk = [];
        $now = date('Y-m-d H:i:s');
        foreach ($packages as $pkg) {
            $opdClean = $pkg['nama_opd_clean'];
            $packageChunk[] = [
                'batch_id'               => $batchId,
                'opd_summary_id'         => $opdSummaryMap[$opdClean] ?? null,
                'row_index_csv'          => $pkg['row_index_csv'],
                'kode_rup'               => $pkg['kode_rup'],
                'nama_instansi'          => $pkg['nama_instansi'],
                'nama_satuan_kerja'      => $pkg['nama_satuan_kerja'],
                'nama_opd_clean'         => $pkg['nama_opd_clean'],
                'kode_satker_sipd'       => $pkg['kode_satker_sipd'],
                'tahun_anggaran'         => $pkg['tahun_anggaran'],
                'cara_pengadaan'         => $pkg['cara_pengadaan'],
                'metode_pengadaan'       => $pkg['metode_pengadaan'],
                'metode_arr'             => json_encode($pkg['metode_arr']),
                'jenis_pengadaan'        => $pkg['jenis_pengadaan'],
                'jenis_arr'              => json_encode($pkg['jenis_arr']),
                'nama_paket'             => $pkg['nama_paket'],
                'nama_paket_clean'       => $pkg['nama_paket_clean'],
                'nama_paket_objek'       => $pkg['nama_paket_objek'],
                'nama_paket_core'        => $pkg['nama_paket_core'],
                'lokasi_extracted'       => $pkg['lokasi_extracted'],
                'sumber_dana'            => $pkg['sumber_dana'],
                'sumber_dana_arr'        => json_encode($pkg['sumber_dana_arr']),
                'budget_cycle'           => $pkg['budget_cycle'],
                'is_blud'                => $pkg['is_blud'],
                'is_dak'                 => $pkg['is_dak'],
                'is_multiyears'          => $pkg['is_multiyears'],
                'is_scientific_truncated'=> $pkg['is_scientific_truncated'],
                'is_integrated_package'  => $pkg['is_integrated_package'],
                'produk_dalam_negeri'    => $pkg['produk_dalam_negeri'],
                'status_umk'             => $pkg['status_umk'],
                'total_nilai'            => $pkg['total_nilai'],
                'cluster_id'             => $pkg['cluster_id'],
                'max_severity'           => $pkg['max_severity'],
                'violation_count'        => $pkg['violation_count'],
                'diff_status'            => $pkg['diff_status'],
                'tanggal_umumkan'        => $pkg['tanggal_umumkan'],
                'jadwal_pemilihan_awal'  => $pkg['jadwal_pemilihan_awal'],
                'jadwal_kontrak_awal'    => $pkg['jadwal_kontrak_awal'],
                'jadwal_kontrak_akhir'   => $pkg['jadwal_kontrak_akhir'],
                'tipe_swakelola'         => $pkg['tipe_swakelola'],
                'lokasi_pekerjaan'       => $pkg['lokasi_pekerjaan'],
                'kode_rekening_mak'      => $pkg['kode_rekening_mak'],
                'pagu_pengadaan_dpa'     => $pkg['pagu_pengadaan_dpa'],
                'created_at'             => $now,
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

        // 5.3 MAPPING PACKAGE_ID: Ambil mapping id berbasis row_index_csv (INSTRUKSI UTAMA PENGGUNA)
        $idRows = $this->db->table('rup_audit_packages')
            ->select('id, row_index_csv')
            ->where('batch_id', $batchId)
            ->get()->getResultArray();

        $rowIndexToPackageId = [];
        foreach ($idRows as $ir) {
            $rowIndexToPackageId[(int) $ir['row_index_csv']] = (int) $ir['id'];
        }

        // 5.4 Insert Violations in Chunks
        $violationChunk = [];
        foreach ($violations as $v) {
            $rIndex = (int) ($v['row_index_csv'] ?? 0);
            $pkgId  = $rowIndexToPackageId[$rIndex] ?? null;
            if (! $pkgId) {
                continue;
            }

            $violationChunk[] = [
                'batch_id'              => $batchId,
                'package_id'            => $pkgId,
                'rule_id'               => $v['rule_id'],
                'severity'              => $v['severity'],
                'kategori'              => $v['kategori'],
                'regulasi'              => $v['regulasi'],
                'deskripsi_pelanggaran' => $v['deskripsi_pelanggaran'],
                'rekomendasi_perbaikan' => $v['rekomendasi_perbaikan'],
                'target_perubahan'      => ! empty($v['target_perubahan']) ? json_encode($v['target_perubahan']) : null,
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

        // 5.5 Insert Clusters
        $clusterChunk = [];
        foreach ($clusters as $cls) {
            $cls['batch_id'] = $batchId;
            $clusterChunk[]  = $cls;
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
        if (is_numeric($idOrUuid)) {
            return $this->batchModel->find((int) $idOrUuid);
        }
        return $this->batchModel->where('batch_uuid', $idOrUuid)->first();
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

        return $builder->orderBy('skor_rci', 'ASC')->get()->getResultArray();
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
            // Hierarki warna risiko: KRITIS (5) > TINGGI (4) > SEDANG (3) > RENDAH (2) > AMAN (1)
            $builder->orderBy("CASE p.max_severity WHEN 'KRITIS' THEN 5 WHEN 'TINGGI' THEN 4 WHEN 'SEDANG' THEN 3 WHEN 'RENDAH' THEN 2 WHEN 'AMAN' THEN 1 ELSE 0 END", $dir)
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

        return $builder->orderBy('total_pagu_klaster', 'DESC')->get()->getResultArray();
    }

    /**
     * Ambil rincian pelanggaran per paket untuk modal AJAX.
     */
    public function getPackageViolations(int $packageId): array
    {
        return $this->db->table('rup_audit_violations')
            ->where('package_id', $packageId)
            ->get()->getResultArray();
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
