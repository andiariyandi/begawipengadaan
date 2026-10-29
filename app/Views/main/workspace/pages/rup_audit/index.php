<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="rup-audit-index">

    <!-- 1. HERO EXECUTIVE WELCOME BANNER -->
    <div class="ws-hero-card shadow-sm p-4 p-lg-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-microscope me-1"></i> Universal SIRUP Compliance Engine v3.1
                    </span>
                    <span class="badge bg-white text-navy px-3 py-2 rounded-pill small fw-bold shadow-sm">
                        <i class="fas fa-balance-scale text-primary me-1"></i> Perpres 46/2025 &middot; UU 20/2023 &middot; Perlem LKPP 3/2026
                    </span>
                </div>
                <h1 class="h2 text-white fw-bold mb-2">
                    Audit Kepatuhan RUP Otomatis
                </h1>
                <p class="text-white-50 mb-0 fs-6" style="max-width: 650px;">
                    Sistem audit 100% populasi paket RUP Pemerintah Provinsi Lampung untuk deteksi dini pemecahan paket (<em>splitting</em>), anomali pagu mepet batas, kewajiban Katalog V6, afirmasi UMK &ge;40%, serta kalkulasi skor <strong>RUP Compliance Index (RCI v3.1)</strong>.
                </p>
            </div>

            <div class="col-lg-5 text-lg-end">
                <a href="#cardUploadRup" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-3 shadow-sm me-2">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Unggah CSV RUP Baru
                </a>
                <?php if ($latestBatch): ?>
                    <a href="<?= site_url('workspace/audit-rup/detail/' . esc($latestBatch['batch_uuid'])) ?>" class="btn btn-outline-light rounded-pill px-4 py-3">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard Terkini
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. EXECUTIVE KPI CARDS -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total OPD -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary small fw-semibold">Total OPD</span>
                <h3 class="h4 fw-bold text-navy mt-1 mb-0"><?= number_format($kpi['total_opd']) ?></h3>
                <small class="text-muted">Satuan Kerja Teraudit</small>
            </div>
        </div>

        <!-- Card 2: Total Paket & Pagu -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary small fw-semibold">Pagu &amp; Paket RUP</span>
                <h3 class="h4 fw-bold text-navy mt-1 mb-0">Rp <?= number_format($kpi['total_pagu'], 0, ',', '.') ?></h3>
                <small class="text-muted"><?= number_format($kpi['total_paket']) ?> Paket Pengadaan</small>
            </div>
        </div>

        <!-- Card 3: % E-Purchasing -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary small fw-semibold">E-Purchasing</span>
                <h3 class="h4 fw-bold text-success mt-1 mb-0"><?= number_format($kpi['persen_epurchasing'], 1) ?>%</h3>
                <small class="text-muted">Target: &ge; 30% Penyedia</small>
            </div>
        </div>

        <!-- Card 4: % UMK Penyedia & Eligible -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary small fw-semibold">Alokasi UMK</span>
                <h3 class="h4 fw-bold text-primary mt-1 mb-0"><?= number_format($kpi['persen_umk_penyedia'], 1) ?>%</h3>
                <small class="text-muted">&le;15M: <?= number_format($kpi['persen_umk_eligible'], 1) ?>% (Target &ge;40%)</small>
            </div>
        </div>

        <!-- Card 5: Pagu Berisiko Kritis/Tinggi -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-secondary small fw-semibold">Pagu Berisiko Kritis/Tinggi</span>
                <h3 class="h4 fw-bold text-danger mt-1 mb-0">Rp <?= number_format($kpi['pagu_kritis_tinggi'], 0, ',', '.') ?></h3>
                <small class="text-muted">Prioritas Reviu Probity</small>
            </div>
        </div>
    </div>

    <!-- 3. FORM UNGGAH CSV RUP -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="cardUploadRup">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-file-csv text-primary me-2"></i> Unggah Data RUP (CSV SIRUP)</h2>
                <p class="text-muted small mb-0">Mendukung file CSV 1 OPD maupun Multi-OPD (~8.000+ baris) dengan pemisahan otomatis kode 22 digit SIPD.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                Skema Dual-Mode (12 Wajib + 8 Ekstensi)
            </span>
        </div>

        <div class="card-body p-4">
            <form action="<?= site_url('workspace/audit-rup/upload') ?>" method="post" enctype="multipart/form-data" id="formUploadRup">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label fw-bold text-navy small">Pilih Berkas CSV Ekspor SIRUP <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control rounded-3 py-2" accept=".csv,text/csv" required>
                        <small class="text-muted d-block mt-1">Pemisah koma (<code>,</code>) atau titik koma (<code>;</code>) dideteksi otomatis. Enkoding UTF-8 didukung penuh.</small>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-bold text-navy small">Bandingkan dengan Batch Sebelumnya (Diff-Tracking)</label>
                        <select name="previous_batch_id" class="form-select rounded-3 py-2">
                            <option value="">-- Tanpa Perbandingan (Audit Pertama) --</option>
                            <?php foreach ($batches as $b): ?>
                                <option value="<?= esc($b['id']) ?>">
                                    <?= esc($b['batch_uuid']) ?> &middot; <?= esc($b['filename']) ?> (RCI: <?= number_format($b['avg_rci_score'], 1) ?> - <?= date('d/m/Y', strtotime($b['created_at'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-1">Opsional: Pilih batch sebelumnya untuk mendeteksi anomali yang sudah diperbaiki (FIXED) vs menetap (PERSISTENT).</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Catatan Pemeriksaan / Siklus APBD</label>
                        <input type="text" name="notes" class="form-control rounded-3 py-2" placeholder="Contoh: Audit RUP Pra-DPA APBD Murni TA 2026 Seluruh OPD Pemprov Lampung">
                    </div>

                    <div class="col-12 text-end pt-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
                                data-confirm="Mulai eksekusi audit otomatis kepatuhan RUP terhadap seluruh paket dalam berkas CSV ini?"
                                data-title="Konfirmasi Mulai Audit RUP v3.1"
                                data-type="primary"
                                data-btn-text="Ya, Jalankan Audit">
                            <i class="fas fa-play-circle me-1"></i> Jalankan Engine Audit v3.1
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. RIWAYAT BATCH AUDIT -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 fw-bold text-navy mb-1"><i class="fas fa-history text-secondary me-2"></i> Riwayat Sesi Audit RUP</h2>
                <p class="text-muted small mb-0">Daftar batch unggahan audit yang pernah diproses pada sistem BegawiPengadaan.</p>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                Total: <?= count($batches) ?> Batch Tersimpan
            </span>
        </div>

        <div class="card-body p-4">
            <?php if (empty($batches)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                    <h5 class="fw-bold text-navy">Belum Ada Sesi Audit RUP</h5>
                    <p class="text-muted small">Silakan unggah berkas CSV ekspor SIRUP melalui formulir di atas untuk memulai analisis kepatuhan otomatis.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal &amp; Waktu</th>
                                <th>Batch UUID</th>
                                <th>Nama Berkas</th>
                                <th class="text-center">OPD</th>
                                <th class="text-center">Paket</th>
                                <th class="text-end">Total Pagu (Rp)</th>
                                <th class="text-center">Rata-rata RCI</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($batches as $b):
                                $rci = (float) $b['avg_rci_score'];
                                $badgeClass = 'bg-success';
                                if ($rci < 50.0) $badgeClass = 'bg-danger';
                                elseif ($rci < 70.0) $badgeClass = 'bg-warning text-dark';
                                elseif ($rci < 85.0) $badgeClass = 'bg-info text-dark';
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-navy d-block"><?= date('d M Y', strtotime($b['created_at'])) ?></span>
                                    <small class="text-muted"><?= date('H:i:s', strtotime($b['created_at'])) ?> WIB</small>
                                </td>
                                <td>
                                    <code class="fw-bold text-primary"><?= esc($b['batch_uuid']) ?></code>
                                    <?php if ($b['previous_batch_id']): ?>
                                        <span class="badge bg-light text-muted border ms-1" title="Memiliki perbandingan diff"><i class="fas fa-code-branch"></i> Diff</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 220px;" title="<?= esc($b['filename']) ?>">
                                        <?= esc($b['filename']) ?>
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-navy border"><?= number_format($b['total_opd']) ?></span></td>
                                <td class="text-center"><span class="badge bg-light text-navy border"><?= number_format($b['total_packages']) ?></span></td>
                                <td class="text-end fw-semibold">Rp <?= number_format($b['total_pagu'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1 fw-bold">
                                        <?= number_format($rci, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('workspace/audit-rup/detail/' . esc($b['batch_uuid'])) ?>" class="btn btn-outline-primary" title="Buka Dashboard Analitik 5 Tab">
                                            <i class="fas fa-eye"></i> Buka
                                        </a>
                                        <a href="<?= site_url('workspace/audit-rup/export-excel/' . esc($b['batch_uuid'])) ?>" class="btn btn-outline-success" title="Unduh Excel 5-Sheet">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                        <a href="<?= site_url('workspace/audit-rup/delete/' . esc($b['batch_uuid'])) ?>" class="btn btn-outline-danger" title="Hapus Riwayat"
                                           data-confirm="Hapus riwayat sesi audit RUP <?= esc($b['batch_uuid']) ?> beserta seluruh data rekapitulasinya?"
                                           data-title="Konfirmasi Hapus Batch"
                                           data-type="danger"
                                           data-btn-text="Ya, Hapus Permanen">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
