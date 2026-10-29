<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="rup-audit-detail">

    <!-- 1. BATCH HEADER & SUMMARY STRIP -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
                            Batch: <?= esc($batch['batch_uuid']) ?>
                        </span>
                        <span class="badge bg-light text-navy border px-2 py-1 small">
                            TA <?= esc($batch['tahun_anggaran']) ?>
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 small">
                            <?= esc($batch['schema_mode']) ?>
                        </span>
                        <span class="badge bg-info-subtle text-info-emphasis border px-2 py-1 small">
                            <i class="fas fa-balance-scale me-1"></i> Perpres 46/2025 &middot; UU 20/2023 &middot; Perlem LKPP 3/2026
                        </span>
                    </div>
                    <h1 class="h4 fw-bold text-navy mb-1">
                        Hasil Audit Kepatuhan: <?= esc($batch['nama_instansi']) ?>
                    </h1>
                    <p class="text-muted small mb-0">
                        Berkas: <code><?= esc($batch['filename']) ?></code> &middot; Diaudit pada <?= date('d M Y H:i', strtotime($batch['created_at'])) ?> WIB
                        <?php if ($batch['previous_batch_id']): ?>
                            &middot; <span class="text-primary fw-semibold"><i class="fas fa-code-branch"></i> Diff terhubung ke Batch #<?= esc($batch['previous_batch_id']) ?></span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('workspace/audit-rup/export-excel/' . esc($batch['batch_uuid'])) ?>" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-file-excel me-2"></i> Unduh Excel 5-Sheet (.xlsx)
                    </a>
                    <a href="<?= site_url('workspace/audit-rup') ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Mini Highlight Bar -->
            <div class="row g-2 pt-2 border-top">
                <div class="col-6 col-md-3">
                    <span class="text-muted small">Total OPD:</span>
                    <strong class="text-navy ms-1"><?= number_format($batch['total_opd']) ?> Satker</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small">Total Paket &amp; Pagu:</span>
                    <strong class="text-navy ms-1"><?= number_format($batch['total_packages']) ?> (Rp <?= number_format($batch['total_pagu'], 0, ',', '.') ?>)</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small">Pagu Penyedia:</span>
                    <strong class="text-navy ms-1">Rp <?= number_format($batch['total_pagu_penyedia'], 0, ',', '.') ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small">Rata-rata RCI:</span>
                    <strong class="text-primary ms-1"><?= number_format($batch['avg_rci_score'], 1) ?> / 100</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- MANDATORI EVALUASI PEMILIK MANFAAT (BENEFICIAL OWNERSHIP) PASAL 8 PERLEM LKPP 3/2026 -->
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start gap-3 p-3">
        <span class="fs-4 text-warning"><i class="fas fa-exclamation-triangle"></i></span>
        <div>
            <div class="fw-bold text-dark">
                Pemberitahuan Mandatori &middot; Pasal 8 Peraturan LKPP Nomor 3 Tahun 2026 (Evaluasi Pemilik Manfaat / Beneficial Ownership)
            </div>
            <div class="small text-secondary mt-1">
                Untuk seluruh paket pengadaan yang tahap pengumuman/undangannya dimulai sejak <strong>1 November 2026 s.d. 31 Desember 2026</strong> (dan seterusnya), Pokja Pemilihan / Pejabat Pengadaan <strong>wajib melakukan evaluasi atas data Pemilik Manfaat (<em>Beneficial Ownership</em>)</strong> pada SPSE. Peserta pemilihan yang tidak mengunggah data atau memiliki kesamaan pemilik manfaat dengan peserta lain dalam paket yang sama <strong>wajib digugurkan</strong>.
            </div>
        </div>
    </div>

    <!-- 2. NAVIGASI 5 TAB ANALITIK -->
    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm mb-4" id="rupAuditTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link rounded-pill fw-bold <?= $activeTab === 'leaderboard' ? 'active' : '' ?>"
               href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=leaderboard') ?>">
                <i class="fas fa-trophy me-1"></i> Tab 1: Leaderboard OPD
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link rounded-pill fw-bold <?= $activeTab === 'action-sheet' ? 'active' : '' ?>"
               href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=action-sheet') ?>">
                <i class="fas fa-list-alt me-1"></i> Tab 2: Kertas Kerja Paket
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link rounded-pill fw-bold <?= $activeTab === 'clusters' ? 'active' : '' ?>"
               href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=clusters') ?>">
                <i class="fas fa-project-diagram me-1"></i> Tab 3: Matriks Klaster Splitting
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link rounded-pill fw-bold <?= $activeTab === 'diff-tracking' ? 'active' : '' ?>"
               href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=diff-tracking') ?>">
                <i class="fas fa-code-branch me-1"></i> Tab 4: Diff-Tracking Monitor
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link rounded-pill fw-bold <?= $activeTab === 'export-docs' ? 'active' : '' ?>"
               href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=export-docs') ?>">
                <i class="fas fa-print me-1"></i> Tab 5: Ekspor &amp; Berita Acara
            </a>
        </li>
    </ul>

    <!-- 3. KONTEN TAB AKTIF -->
    <div class="tab-content" id="rupAuditTabsContent">

        <!-- =================================================================== -->
        <!-- TAB 1: LEADERBOARD KEPATUHAN SELURUH OPD                             -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'leaderboard'): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-chart-bar text-primary me-2"></i> Multi-OPD Executive Compliance Leaderboard</h2>
                    <p class="text-muted small mb-0">Peringkat kepatuhan seluruh OPD Pemerintah Provinsi Lampung diurutkan dari skor RCI terendah (risiko tertinggi).</p>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Satuan Kerja (OPD) &amp; Kode SIPD</th>
                                <th class="text-center">Skor RCI v3.1</th>
                                <th class="text-center">Zona Risiko</th>
                                <th class="text-center">Paket</th>
                                <th class="text-end">Total Pagu (Rp)</th>
                                <th class="text-center">% E-Purch</th>
                                <th class="text-center">% UMK</th>
                                <th class="text-center">% PDN</th>
                                <th class="text-end">Pagu Kritis/Tinggi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            foreach ($opdLeaderboard as $opd):
                                $rci = (float) $opd['skor_rci'];
                                $badgeClass = match ($opd['predikat_risiko']) {
                                    'HIJAU'  => 'bg-success text-white',
                                    'KUNING' => 'bg-warning text-dark',
                                    'ORANYE' => 'bg-orange text-white',
                                    default  => 'bg-danger text-white',
                                };
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $rank++ ?></td>
                                <td>
                                    <strong class="text-navy d-block"><?= esc($opd['nama_opd_clean']) ?></strong>
                                    <small class="text-muted">Kode: <?= esc($opd['kode_satker_sipd'] ?: '-') ?></small>
                                    <?php
                                    $flags = json_decode((string) ($opd['macro_flags'] ?? '[]'), true);
                                    if (! empty($flags)): ?>
                                        <div class="mt-1">
                                            <?php foreach ($flags as $fl): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 10px;"><?= esc($fl) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="h6 fw-bold text-navy mb-0"><?= number_format($rci, 1) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1 fw-bold">
                                        <?= esc($opd['predikat_risiko']) ?>
                                    </span>
                                </td>
                                <td class="text-center fw-semibold"><?= number_format($opd['total_paket']) ?></td>
                                <td class="text-end fw-semibold">Rp <?= number_format($opd['total_pagu'], 0, ',', '.') ?></td>
                                <td class="text-center <?= $opd['persen_epurchasing'] < 30.0 ? 'text-danger fw-bold' : 'text-success' ?>">
                                    <?= number_format($opd['persen_epurchasing'], 1) ?>%
                                </td>
                                <td class="text-center <?= ($opd['persen_umk_penyedia'] < 40.0 && $opd['persen_umk_eligible_le15m'] < 80.0) ? 'text-danger fw-bold' : 'text-primary' ?>"
                                    title="Eligible <=15M: <?= number_format($opd['persen_umk_eligible_le15m'], 1) ?>%">
                                    <?= number_format($opd['persen_umk_penyedia'], 1) ?>%
                                </td>
                                <td class="text-center text-muted"><?= number_format($opd['persen_pdn'], 1) ?>%</td>
                                <td class="text-end <?= $opd['pagu_bermasalah_kritis_tinggi'] > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                                    Rp <?= number_format($opd['pagu_bermasalah_kritis_tinggi'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=action-sheet&opd=' . urlencode($opd['nama_opd_clean'])) ?>"
                                           class="btn btn-outline-primary" title="Lihat Paket OPD">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        <a href="<?= site_url('workspace/audit-rup/export-pdf/' . esc($batch['batch_uuid']) . '/' . urlencode($opd['kode_satker_sipd'] ?: $opd['nama_opd_clean'])) ?>"
                                           target="_blank" class="btn btn-outline-danger" title="Cetak Berita Acara PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================================================================== -->
        <!-- TAB 2: KERTAS KERJA BARIS PAKET (ROW-LEVEL ACTION SHEET)           -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'action-sheet'): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-clipboard-list text-primary me-2"></i> Kertas Kerja Audit per Baris Paket (Row-Level Action Sheet)</h2>
                <p class="text-muted small mb-3">Tinjau seluruh paket pengadaan terdeteksi anomali, rekomendasi koreksi target SIRUP, dan rincian pelanggaran regulasi.</p>

                <!-- Filter Controls -->
                <form method="get" action="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid'])) ?>" class="row g-2 align-items-center mb-3">
                    <input type="hidden" name="tab" value="action-sheet">

                    <div class="col-md-3">
                        <select name="opd" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">-- Semua Satuan Kerja (OPD) --</option>
                            <?php foreach ($opdList as $opdName): ?>
                                <option value="<?= esc($opdName) ?>" <?= $filters['opd'] === $opdName ? 'selected' : '' ?>>
                                    <?= esc($opdName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="severity" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="ALL">-- Semua Tingkat Risiko --</option>
                            <option value="KRITIS" <?= $filters['severity'] === 'KRITIS' ? 'selected' : '' ?>>KRITIS (Kerah Merah)</option>
                            <option value="TINGGI" <?= $filters['severity'] === 'TINGGI' ? 'selected' : '' ?>>TINGGI (Oranye)</option>
                            <option value="SEDANG" <?= $filters['severity'] === 'SEDANG' ? 'selected' : '' ?>>SEDANG (Kuning)</option>
                            <option value="RENDAH" <?= $filters['severity'] === 'RENDAH' ? 'selected' : '' ?>>RENDAH (Biru/Hijau)</option>
                            <option value="AMAN"   <?= $filters['severity'] === 'AMAN' ? 'selected' : '' ?>>AMAN (Patuh)</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="cycle" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="ALL">-- Semua Siklus --</option>
                            <option value="MURNI" <?= $filters['budget_cycle'] === 'MURNI' ? 'selected' : '' ?>>APBD Murni</option>
                            <option value="PERUBAHAN" <?= $filters['budget_cycle'] === 'PERUBAHAN' ? 'selected' : '' ?>>APBD-P (Perubahan)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="q" class="form-control form-control-sm rounded-3" placeholder="Cari nama paket / kode RUP..." value="<?= esc($filters['search']) ?>">
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=action-sheet') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Reset Filter">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body p-4 pt-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Menampilkan <strong><?= count($packages) ?></strong> dari <strong><?= number_format($totalPackagesFiltered) ?></strong> paket hasil filter</small>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Kode RUP</th>
                                <th>Satuan Kerja (OPD)</th>
                                <th>Nama Paket Pengadaan</th>
                                <th>Cara &middot; Metode &middot; Jenis</th>
                                <th class="text-end">Pagu RUP (Rp)</th>
                                <th class="text-center">Risiko</th>
                                <th class="text-center">Status Diff</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $pkg):
                                $sev = $pkg['max_severity'];
                                $badgeClass = match ($sev) {
                                    'KRITIS' => 'bg-danger text-white',
                                    'TINGGI' => 'bg-orange text-white',
                                    'SEDANG' => 'bg-warning text-dark',
                                    'RENDAH' => 'bg-info text-dark',
                                    default  => 'bg-success text-white',
                                };
                            ?>
                            <tr>
                                <td>
                                    <code class="fw-bold text-navy"><?= esc($pkg['kode_rup']) ?></code>
                                    <span class="d-block small text-muted"><?= esc($pkg['budget_cycle']) ?></span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate fw-semibold text-navy" style="max-width: 180px;" title="<?= esc($pkg['nama_opd_clean']) ?>">
                                        <?= esc($pkg['nama_opd_clean']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-navy"><?= esc($pkg['nama_paket']) ?></strong>
                                    <?php if ($pkg['nama_paket_objek'] && $pkg['nama_paket_objek'] !== $pkg['nama_paket_clean']): ?>
                                        <small class="d-block text-muted">Objek Inti: <em><?= esc($pkg['nama_paket_objek']) ?></em></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-navy border"><?= esc($pkg['cara_pengadaan']) ?></span>
                                    <span class="d-block small text-muted"><?= esc($pkg['metode_pengadaan']) ?> &middot; <?= esc($pkg['jenis_pengadaan']) ?></span>
                                </td>
                                <td class="text-end fw-bold text-navy">
                                    Rp <?= number_format($pkg['total_nilai'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1">
                                        <?= esc($sev) ?>
                                    </span>
                                    <small class="d-block text-muted"><?= $pkg['violation_count'] ?> Temuan</small>
                                </td>
                                <td class="text-center">
                                    <?php if ($pkg['diff_status'] === 'PERSISTENT'): ?>
                                        <span class="badge bg-danger-subtle text-danger border">PERSISTENT</span>
                                    <?php elseif ($pkg['diff_status'] === 'FIXED'): ?>
                                        <span class="badge bg-success-subtle text-success border">FIXED</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border">NEW</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-detail-paket"
                                            data-package-id="<?= esc($pkg['id']) ?>">
                                        <i class="fas fa-info-circle me-1"></i> Rincian
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <small class="text-muted">Halaman <?= $currentPage ?> dari <?= $totalPages ?></small>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                                <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid']) . '?tab=action-sheet&page=' . $p . '&opd=' . urlencode($filters['opd']) . '&severity=' . $filters['severity'] . '&cycle=' . $filters['budget_cycle'] . '&q=' . urlencode($filters['search'])) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================================================================== -->
        <!-- TAB 3: MATRIKS KLASTER PEMACAHAN PAKET & KONSOLIDASI                -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'clusters'): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-project-diagram text-primary me-2"></i> Matriks Klaster Deteksi Splitting &amp; Konsolidasi Paket</h2>
                    <p class="text-muted small mb-0">Hasil pengelompokan kemiripan teks Jaccard (&ge;0.78) untuk mengidentifikasi indikasi pemecahan paket (<em>splitting</em>) dan potensi konsolidasi.</p>
                </div>
            </div>

            <div class="card-body p-4">
                <?php if (empty($clusters)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h5 class="fw-bold text-navy">Tidak Terdeteksi Pemecahan Paket (Splitting)</h5>
                        <p class="text-muted small">Seluruh paket Pengadaan Langsung berada di bawah batas akumulasi regulasi yang sah.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($clusters as $cls):
                            $isSplitting = str_contains($cls['kategori_temuan'], 'SPLITTING');
                            $borderClass = $isSplitting ? 'border-danger' : 'border-warning';
                            $headerClass = $isSplitting ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-dark';
                        ?>
                        <div class="col-lg-6">
                            <div class="card h-100 rounded-4 shadow-sm border <?= $borderClass ?> overflow-hidden">
                                <div class="card-header py-3 px-4 <?= $headerClass ?> d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-white text-navy border fw-bold me-1"><?= esc($cls['cluster_uuid']) ?></span>
                                        <strong><?= esc($cls['kategori_temuan']) ?></strong>
                                    </div>
                                    <span class="badge <?= $cls['severity'] === 'KRITIS' ? 'bg-danger' : 'bg-warning text-dark' ?> rounded-pill px-3">
                                        <?= esc($cls['severity']) ?>
                                    </span>
                                </div>
                                <div class="card-body p-4">
                                    <h4 class="h6 fw-bold text-navy mb-1"><?= esc($cls['nama_komoditas_klaster']) ?></h4>
                                    <p class="small text-muted mb-2">Satker: <strong><?= esc($cls['nama_opd_clean']) ?></strong> &middot; Siklus: <?= esc($cls['budget_cycle']) ?> &middot; Jenis: <?= esc($cls['jenis_pengadaan']) ?></p>

                                    <div class="bg-light p-3 rounded-3 mb-3">
                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Jumlah Paket</small>
                                                <strong class="text-navy fs-6"><?= number_format($cls['jumlah_paket_terpecah']) ?> Paket</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Total Pagu Klaster</small>
                                                <strong class="text-danger fs-6">Rp <?= number_format($cls['total_pagu_klaster'], 0, ',', '.') ?></strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Batas PL Regulasi</small>
                                                <strong class="text-navy fs-6">Rp <?= number_format($cls['batas_regulasi_pl'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <span class="text-muted small fw-semibold">Rekomendasi Tindak Lanjut:</span>
                                        <p class="small text-dark mb-0"><?= esc($cls['instruksi_konsolidasi']) ?></p>
                                    </div>

                                    <div>
                                        <span class="text-muted small fw-semibold">Daftar Kode RUP Terpecah:</span>
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            <?php
                                            $rups = json_decode((string) $cls['daftar_kode_rup'], true) ?: [];
                                            foreach ($rups as $r): ?>
                                                <span class="badge bg-white text-navy border small"><code><?= esc($r) ?></code></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================================================================== -->
        <!-- TAB 4: DIFF-TRACKING & FOLLOW-UP MONITOR                           -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'diff-tracking'):
            $diff = $macroSummary['diff_tracking'] ?? [];
            $hasDiff = ! empty($diff['is_comparison_active']);
        ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-code-branch text-primary me-2"></i> Diff-Tracking &amp; Follow-Up Monitor</h2>
                <p class="text-muted small mb-0">Pelacakan progres perbaikan data RUP antara versi audit lama vs versi audit saat ini.</p>
            </div>

            <div class="card-body p-4">
                <?php if (! $hasDiff): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle text-primary fa-3x mb-3"></i>
                        <h5 class="fw-bold text-navy">Tidak Ada Batch Pembanding Sebelumnya</h5>
                        <p class="text-muted small" style="max-width: 500px; margin: 0 auto;">
                            Sesi audit ini diproses sebagai baseline (audit awal). Untuk mengaktifkan fitur Diff-Tracking, pilih batch ini sebagai acuan saat mengunggah berkas CSV RUP versi revisi berikutnya.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success-subtle text-success h-100">
                                <span class="small fw-semibold">Telah Diperbaiki (FIXED)</span>
                                <h3 class="h3 fw-bold mt-1 mb-0"><?= number_format($diff['total_anomali_telah_diperbaiki_fixed'] ?? 0) ?></h3>
                                <small>Paket bermasalah berhasil disesuaikan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger-subtle text-danger h-100">
                                <span class="small fw-semibold">Masih Bertahan (PERSISTENT)</span>
                                <h3 class="h3 fw-bold mt-1 mb-0"><?= number_format($diff['total_anomali_masih_bertahan_persistent'] ?? 0) ?></h3>
                                <small>Belum dilakukan perbaikan di SIRUP</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary-subtle text-primary h-100">
                                <span class="small fw-semibold">Anomali Baru (NEW)</span>
                                <h3 class="h3 fw-bold mt-1 mb-0"><?= number_format($diff['total_anomali_baru_muncul_new'] ?? 0) ?></h3>
                                <small>Muncul pasca revisi / penambahan paket</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light text-navy border h-100">
                                <span class="small fw-semibold">Perubahan Skor RCI Instansi</span>
                                <h3 class="h3 fw-bold mt-1 mb-0 text-primary"><?= esc($diff['delta_skor_rci_instansi'] ?? '+0.00') ?></h3>
                                <small>Baseline: <?= number_format($diff['prev_rci_score'] ?? 0, 1) ?> &rarr; Kini: <?= number_format($batch['avg_rci_score'], 1) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================================================================== -->
        <!-- TAB 5: EKSPOR & DOKUMEN RESMI                                      -->
        <!-- =================================================================== -->
        <?php if ($activeTab === 'export-docs'): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-file-export text-primary me-2"></i> Ekspor Kertas Kerja &amp; Dokumen Resmi Reviu</h2>
                <p class="text-muted small mb-0">Hasilkan berkas kerja audit format Excel 5-Sheet dan dokumen Berita Acara Reviu PDF siap tanda tangan.</p>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Box 1: Excel 5-Sheet -->
                    <div class="col-lg-6">
                        <div class="card h-100 border rounded-4 p-4 shadow-sm bg-light">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="w-48-px h-48-px rounded-circle bg-success text-white d-flex align-items-center justify-content-center fs-4 shadow-sm">
                                    <i class="fas fa-file-excel"></i>
                                </span>
                                <div>
                                    <h3 class="h6 fw-bold text-navy mb-0">Kertas Kerja Lengkap Excel 5-Sheet (.xlsx)</h3>
                                    <small class="text-muted">Kompatibel penuh dengan Microsoft Excel &amp; Google Sheets</small>
                                </div>
                            </div>
                            <p class="small text-muted mb-3">
                                Memuat 5 lembar kerja terstruktur: (1) Leaderboard Kepatuhan RCI OPD, (2) Temuan Kritis &amp; Tinggi, (3) Matriks Klaster Splitting &amp; Konsolidasi, (4) Daftar Lengkap Seluruh Paket, dan (5) Log Diff-Tracking.
                            </p>
                            <a href="<?= site_url('workspace/audit-rup/export-excel/' . esc($batch['batch_uuid'])) ?>" class="btn btn-success rounded-pill px-4 py-2 fw-semibold w-100">
                                <i class="fas fa-download me-1"></i> Unduh Berkas Excel (.xlsx)
                            </a>
                        </div>
                    </div>

                    <!-- Box 2: Cetak Berita Acara PDF -->
                    <div class="col-lg-6">
                        <div class="card h-100 border rounded-4 p-4 shadow-sm bg-light">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="w-48-px h-48-px rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fs-4 shadow-sm">
                                    <i class="fas fa-file-pdf"></i>
                                </span>
                                <div>
                                    <h3 class="h6 fw-bold text-navy mb-0">Cetak Berita Acara Hasil Reviu RUP (.pdf)</h3>
                                    <small class="text-muted">Dokumen resmi per OPD siap tanda tangan Pimpinan</small>
                                </div>
                            </div>
                            <p class="small text-muted mb-3">
                                Pilih satuan kerja (OPD) untuk mengunduh dokumen Berita Acara resmi berkop surat Biro PBJ Setda Provinsi Lampung lengkap dengan tabel anomali prioritas dan lembar pengesahan berpedoman pada <strong>Perpres 46/2025</strong>, <strong>UU 20/2023</strong>, dan <strong>Perlem LKPP 3/2026</strong>.
                            </p>

                            <div class="input-group">
                                <select id="selectOpdPdf" class="form-select rounded-start-pill">
                                    <?php foreach ($opdList as $opdName): ?>
                                        <option value="<?= urlencode($opdName) ?>"><?= esc($opdName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-danger rounded-end-pill px-3" id="btnCetakPdf">
                                    <i class="fas fa-print me-1"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>

<!-- 4. MODAL DETAIL TEMUAN PAKET (AJAX) -->
<div class="modal fade" id="modalDetailPaket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-navy text-white border-0 py-3 px-4">
                <h5 class="modal-title h6 fw-bold mb-0">
                    <i class="fas fa-search-plus text-warning me-2"></i> Rincian Temuan &amp; Rekomendasi SIRUP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalDetailPaketBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Memuat rincian paket...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2 px-4">
                <button type="button" class="btn btn-secondary rounded-pill btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tombol Cetak PDF OPD
    var btnPdf = document.getElementById('btnCetakPdf');
    if (btnPdf) {
        btnPdf.addEventListener('click', function () {
            var opdVal = document.getElementById('selectOpdPdf').value;
            if (opdVal) {
                var url = "<?= site_url('workspace/audit-rup/export-pdf/' . esc($batch['batch_uuid'])) ?>/" + opdVal;
                window.open(url, '_blank');
            }
        });
    }

    // Modal Detail Temuan Paket via AJAX
    var modalEl = document.getElementById('modalDetailPaket');
    var modalObj = modalEl ? new bootstrap.Modal(modalEl) : null;
    var modalBody = document.getElementById('modalDetailPaketBody');

    document.querySelectorAll('.btn-detail-paket').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pkgId = this.getAttribute('data-package-id');
            if (!pkgId) return;

            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted small mt-2">Memuat rincian paket...</p></div>';
            modalObj.show();

            fetch("<?= site_url('workspace/audit-rup/api-row-detail') ?>/" + pkgId)
                .then(function (res) { return res.json(); })
                .then(function (res) {
                    if (!res.success) {
                        modalBody.innerHTML = '<div class="alert alert-danger mb-0">' + (res.message || 'Gagal memuat rincian paket.') + '</div>';
                        return;
                    }

                    var d = res.data;
                    var html = '';

                    html += '<div class="mb-3 pb-3 border-bottom">';
                    html += '  <h5 class="h6 fw-bold text-navy mb-1">' + d.nama_paket + '</h5>';
                    html += '  <p class="small text-muted mb-1">Satker: <strong>' + d.nama_opd_clean + '</strong> &middot; Kode RUP: <code>' + d.kode_rup + '</code></p>';
                    html += '  <div class="d-flex flex-wrap gap-2">';
                    html += '    <span class="badge bg-light text-navy border">Cara: ' + d.cara_pengadaan + '</span>';
                    html += '    <span class="badge bg-light text-navy border">Metode: ' + d.metode_pengadaan + '</span>';
                    html += '    <span class="badge bg-light text-navy border">Jenis: ' + d.jenis_pengadaan + '</span>';
                    html += '    <span class="badge bg-primary text-white">Pagu: Rp ' + Number(d.total_nilai).toLocaleString('id-ID') + '</span>';
                    html += '  </div>';
                    html += '</div>';

                    html += '<h6 class="fw-bold text-navy mb-2"><i class="fas fa-exclamation-triangle text-danger me-1"></i> Daftar Temuan Regulasi (' + d.violations.length + ' Aturan):</h6>';

                    if (d.violations.length === 0) {
                        html += '<div class="alert alert-success small mb-0">Paket ini memenuhi standar regulasi (tidak ada pelanggaran terdeteksi).</div>';
                    } else {
                        html += '<div class="d-flex flex-column gap-3">';
                        d.violations.forEach(function (v) {
                            var bClass = v.severity === 'KRITIS' ? 'bg-danger' : (v.severity === 'TINGGI' ? 'bg-orange text-white' : 'bg-warning text-dark');
                            html += '<div class="border rounded-3 p-3 bg-light">';
                            html += '  <div class="d-flex justify-content-between align-items-center mb-1">';
                            html += '    <strong class="text-navy">' + v.rule_id + ': ' + v.kategori + '</strong>';
                            html += '    <span class="badge ' + bClass + '">' + v.severity + '</span>';
                            html += '  </div>';
                            html += '  <p class="small text-dark mb-2">' + v.deskripsi_pelanggaran + '</p>';
                            html += '  <div class="bg-white p-2 rounded border small text-primary mb-1">';
                            html += '    <strong>Rekomendasi Koreksi:</strong> ' + v.rekomendasi_perbaikan;
                            html += '  </div>';
                            html += '  <small class="text-muted d-block">Dasar Regulasi: <em>' + v.regulasi + '</em></small>';
                            html += '</div>';
                        });
                        html += '</div>';
                    }

                    modalBody.innerHTML = html;
                })
                .catch(function (err) {
                    modalBody.innerHTML = '<div class="alert alert-danger mb-0">Gagal menghubungi server: ' + err + '</div>';
                });
        });
    });
});
</script>

<style>
.bg-orange {
    background-color: #f97316 !important;
}
.w-48-px {
    width: 48px;
}
.h-48-px {
    height: 48px;
}
</style>

<?= $this->endSection() ?>
