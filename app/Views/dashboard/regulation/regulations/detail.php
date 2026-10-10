<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$regulation       = $regulation ?? [];
$versions         = $regulation['versions'] ?? [];
$relations        = $relations ?? [];
$competencies     = $competencies ?? [];
$transitions      = $transitions ?? [];
$workflowStatuses = $workflowStatuses ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Dokumen Regulasi</h6>
        <p class="text-secondary-light mb-0">Nomor Registrasi: #<?= esc($regulation['id'] ?? '-') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($regulation['id'] ?? 0) . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah Regulasi
        </a>
        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<!-- Header Information Card -->
<div class="card radius-12 border mb-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div class="d-flex align-items-center gap-3">
                <div class="w-56-px h-56-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                    <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                </div>
                <div>
                    <span class="badge bg-primary-50 text-primary-600 radius-6 px-12 py-4 text-xs fw-semibold mb-1 d-inline-block">
                        <?= esc($regulation['category_name'] ?? 'Regulasi PBJ') ?>
                    </span>
                    <h5 class="fw-bold mb-1 text-primary-light"><?= esc($regulation['document_number'] ?? '-') ?></h5>
                    <p class="text-secondary-light text-md fw-medium mb-0"><?= esc($regulation['title'] ?? '-') ?></p>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?= view('dashboard/components/status_badge', ['status' => $regulation['legal_status'] ?? 'active']) ?>
                <?= view('dashboard/components/status_badge', ['status' => $regulation['workflow_status'] ?? 'draft']) ?>
            </div>
        </div>

        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 pt-20 border-top">
            <div class="col">
                <span class="text-secondary-light text-xs d-block mb-1">Tahun Dokumen</span>
                <span class="fw-semibold text-neutral-800 text-sm"><?= esc($regulation['document_year'] ?? '-') ?></span>
            </div>
            <div class="col">
                <span class="text-secondary-light text-xs d-block mb-1">Instansi Penerbit</span>
                <span class="fw-semibold text-neutral-800 text-sm"><?= esc($regulation['issuer_name'] ?: 'Pemerintah Republik Indonesia') ?></span>
            </div>
            <div class="col">
                <span class="text-secondary-light text-xs d-block mb-1">Tanggal Penetapan</span>
                <span class="fw-semibold text-neutral-800 text-sm"><?= esc($regulation['official_date'] ? date('d M Y', strtotime((string) $regulation['official_date'])) : '-') ?></span>
            </div>
            <div class="col">
                <span class="text-secondary-light text-xs d-block mb-1">Tanggal Berlaku Efektif</span>
                <span class="fw-semibold text-neutral-800 text-sm"><?= esc($regulation['effective_date'] ? date('d M Y', strtotime((string) $regulation['effective_date'])) : '-') ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Main Column: Gradient Tabs -->
    <div class="col-xl-8">
        <div class="card radius-12 border">
            <div class="card-header bg-base border-bottom p-0">
                <ul class="nav border-gradient-tab nav-pills px-24 pt-12 gap-4" id="regTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-summary-btn" data-bs-toggle="pill" data-bs-target="#tab-summary" type="button" role="tab">
                            <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                            Naskah & Ringkasan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-versions-btn" data-bs-toggle="pill" data-bs-target="#tab-versions" type="button" role="tab">
                            <iconify-icon icon="solar:history-outline" class="text-lg"></iconify-icon>
                            Riwayat Versi (<?= count($versions) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-relations-btn" data-bs-toggle="pill" data-bs-target="#tab-relations" type="button" role="tab">
                            <iconify-icon icon="solar:link-circle-outline" class="text-lg"></iconify-icon>
                            Relasi Hukum (<?= count($relations) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-competency-btn" data-bs-toggle="pill" data-bs-target="#tab-competency" type="button" role="tab">
                            <iconify-icon icon="solar:medal-star-outline" class="text-lg"></iconify-icon>
                            Kompetensi (<?= count($competencies) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-audit-btn" data-bs-toggle="pill" data-bs-target="#tab-audit" type="button" role="tab">
                            <iconify-icon icon="solar:shield-check-outline" class="text-lg"></iconify-icon>
                            Audit Workflow
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-24">
                <div class="tab-content" id="regTabContent">
                    <!-- Tab 1: Ringkasan -->
                    <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">
                        <h6 class="text-md fw-semibold mb-12 text-primary-light">Ringkasan & Pokok Substansi</h6>
                        <div class="p-16 radius-8 bg-neutral-50 border mb-24">
                            <p class="text-secondary-light mb-0 text-sm">
                                <?= nl2br(esc($regulation['summary'] ?: 'Tidak ada ringkasan atau abstrak regulasi.')) ?>
                            </p>
                        </div>

                        <?php if (!empty($regulation['official_source_url'])): ?>
                            <div class="p-16 radius-8 border d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-xs text-secondary-light d-block mb-1">Tautan Sumber Resmi:</span>
                                    <span class="text-sm fw-medium text-neutral-800"><?= esc($regulation['official_source_url']) ?></span>
                                </div>
                                <a href="<?= esc($regulation['official_source_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary-600 btn-sm radius-8 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:square-top-down-outline"></iconify-icon> Buka Sumber
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 2: Versi -->
                    <div class="tab-pane fade" id="tab-versions" role="tabpanel">
                        <?php if (empty($versions)): ?>
                            <p class="text-secondary-light mb-24">Belum ada versi file dokumen yang diunggah.</p>
                        <?php else: ?>
                            <div class="table-responsive mb-24">
                                <table class="table bordered-table sm-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Versi</th>
                                            <th>File Dokumen</th>
                                            <th>Ukuran</th>
                                            <th>Tanggal</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($versions as $v): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4">v<?= esc($v['version_number']) ?></span>
                                                    <?php if ($v['is_current']): ?>
                                                        <span class="badge bg-success-50 text-success-600 radius-6 px-8 py-2 ms-1">Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <iconify-icon icon="solar:file-download-outline" class="text-primary-600 text-lg"></iconify-icon>
                                                        <span class="text-sm fw-medium text-neutral-900"><?= esc($v['file_original_name'] ?? 'Naskah Regulasi') ?></span>
                                                    </div>
                                                </td>
                                                <td><span class="text-xs text-secondary-light"><?= isset($v['file_size']) ? esc(round($v['file_size'] / 1024)) . ' KB' : '-' ?></span></td>
                                                <td><span class="text-xs text-secondary-light"><?= esc(date('d M Y', strtotime($v['created_at'] ?? 'now'))) ?></span></td>
                                                <td class="text-center">
                                                    <?php if (! $v['is_current']): ?>
                                                        <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi/current') ?>" method="post" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="version_id" value="<?= (int) $v['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-primary-600 btn-sm radius-8 text-xs py-4">Set Aktif</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-2xs text-success-main fw-medium">Versi Default</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Version Form -->
                        <div class="p-20 radius-8 border bg-neutral-50">
                            <h6 class="text-sm fw-semibold mb-12 text-primary-light">Unggah Versi Baru</h6>
                            <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-medium mb-1">File Dokumen (PDF/DOC)</label>
                                        <input type="file" class="form-control radius-8 form-control-sm" name="file" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-medium mb-1">Catatan Perubahan Versi</label>
                                        <input type="text" class="form-control radius-8 form-control-sm" name="changelog" placeholder="Contoh: Lampiran diperbarui">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8">
                                            <iconify-icon icon="solar:upload-outline"></iconify-icon> Unggah Versi
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tab 3: Relasi -->
                    <div class="tab-pane fade" id="tab-relations" role="tabpanel">
                        <?php if (empty($relations)): ?>
                            <p class="text-secondary-light mb-16">Belum ada keterkaitan relasi hukum dengan regulasi lain.</p>
                        <?php else: ?>
                            <div class="table-responsive mb-16">
                                <table class="table bordered-table sm-table mb-0 align-middle">
                                    <thead><tr><th>Regulasi Sumber</th><th>Tipe Hubungan</th><th>Regulasi Target</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($relations as $r): ?>
                                            <tr>
                                                <td><span class="fw-medium text-sm text-neutral-900"><?= esc($r['source_title'] ?? '-') ?></span></td>
                                                <td><span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6"><?= esc($r['relation_type']) ?></span></td>
                                                <td><span class="fw-medium text-sm text-neutral-900"><?= esc($r['target_title'] ?? '-') ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-primary-600 btn-sm radius-8">
                            <iconify-icon icon="solar:settings-outline"></iconify-icon> Kelola Relasi Regulasi
                        </a>
                    </div>

                    <!-- Tab 4: Kompetensi -->
                    <div class="tab-pane fade" id="tab-competency" role="tabpanel">
                        <div class="mb-24">
                            <h6 class="text-sm fw-semibold mb-12 text-primary-light">Kompetensi yang Terhubung</h6>
                            <?php if (empty($competencies)): ?>
                                <p class="text-secondary-light mb-0">Belum ada kompetensi PBJ yang ditautkan.</p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($competencies as $c): ?>
                                        <span class="badge bg-info-50 text-info-700 px-12 py-8 radius-8 border border-info-200">
                                            <strong class="me-1">[<?= esc($c['key']) ?>]</strong> <?= esc($c['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-16 radius-8 border bg-neutral-50">
                            <h6 class="text-xs fw-semibold mb-8 text-neutral-800">Tautkan Kompetensi Baru</h6>
                            <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/kompetensi') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-9">
                                        <select class="form-select form-select-sm radius-8" name="competency_id">
                                            <option value="">Pilih kompetensi PBJ...</option>
                                            <?php foreach ((new \App\Models\Regulation\CompetencyModel())->listActive() as $comp): ?>
                                                <option value="<?= (int) $comp['id'] ?>"><?= esc($comp['name']) ?> (<?= esc($comp['key']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary-600 btn-sm radius-8 w-100">Tautkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tab 5: Audit Transisi -->
                    <div class="tab-pane fade" id="tab-audit" role="tabpanel">
                        <?php if (empty($transitions)): ?>
                            <p class="text-secondary-light mb-0">Belum ada riwayat transisi workflow tercatat.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table bordered-table sm-table mb-0 align-middle">
                                    <thead><tr><th>Waktu</th><th>Tindakan</th><th>Perubahan Status</th><th>Catatan</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($transitions as $t): ?>
                                            <tr>
                                                <td><span class="text-xs text-secondary-light"><?= esc($t['created_at']) ?></span></td>
                                                <td><span class="badge bg-neutral-100 text-neutral-800 radius-6"><?= esc($t['action']) ?></span></td>
                                                <td><span class="text-xs fw-medium"><?= esc(($t['from_state'] ?? '-') . ' → ' . ($t['to_state'] ?? '-')) ?></span></td>
                                                <td><span class="text-xs text-secondary-light"><?= esc($t['reason'] ?? '-') ?: '-' ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Column: Workflow Action Panel -->
    <div class="col-xl-4">
        <div class="card radius-12 border mb-24">
            <div class="card-header bg-base border-bottom py-16 px-20">
                <h6 class="text-md fw-semibold mb-0">Tindakan Workflow</h6>
            </div>
            <div class="card-body p-20">
                <?php if (($regulation['workflow_status'] ?? '') === 'draft'): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/submit') ?>" method="post" class="mb-12">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:plain-2-outline"></iconify-icon>
                            Submit untuk Peninjauan
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($regulation['workflow_status'] ?? '', ['submitted', 'in_review'], true)): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/approve') ?>" method="post" class="mb-16">
                        <?= csrf_field() ?>
                        <label class="form-label text-xs fw-medium mb-1">Catatan Persetujuan</label>
                        <textarea class="form-control radius-8 mb-8 text-sm" name="notes" rows="2" placeholder="Catatan persetujuan..."></textarea>
                        <button type="submit" class="btn btn-success-600 w-100 radius-8 py-8 d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:check-circle-outline"></iconify-icon> Setujui Naskah
                        </button>
                    </form>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/revision') ?>" method="post" class="mb-16">
                        <?= csrf_field() ?>
                        <label class="form-label text-xs fw-medium mb-1">Catatan Revisi</label>
                        <textarea class="form-control radius-8 mb-8 text-sm" name="note" rows="2" placeholder="Catatan revisi..." required></textarea>
                        <button type="submit" class="btn btn-warning-600 w-100 radius-8 py-8 d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Minta Revisi
                        </button>
                    </form>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/reject') ?>" method="post">
                        <?= csrf_field() ?>
                        <label class="form-label text-xs fw-medium mb-1">Alasan Penolakan</label>
                        <textarea class="form-control radius-8 mb-8 text-sm" name="reason" rows="2" placeholder="Alasan menolak..." required></textarea>
                        <button type="submit" class="btn btn-danger-600 w-100 radius-8 py-8 d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:close-circle-outline"></iconify-icon> Tolak Regulasi
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($regulation['workflow_status'] ?? '') === 'approved'): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/publish') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:share-circle-outline"></iconify-icon>
                            Publikasikan Dokumen
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($regulation['workflow_status'] ?? '') === 'published'): ?>
                    <div class="alert alert-success radius-8 py-12 px-16 mb-0 d-flex align-items-center gap-2 text-sm">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xl flex-shrink-0"></iconify-icon>
                        Dokumen telah aktif dan dipublikasikan.
                    </div>
                <?php endif; ?>

                <hr class="my-16">
                <button type="button" class="btn btn-outline-danger-600 w-100 radius-8 py-8 text-sm d-flex align-items-center justify-content-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                    data-action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/hapus') ?>"
                    data-method="POST"
                    data-title="Hapus / Arsipkan Regulasi"
                    data-message="Apakah Anda yakin ingin menghapus atau mengarsipkan dokumen regulasi ini?"
                    data-btn-text="Hapus Regulasi"
                    data-btn-class="btn-danger-600"
                    data-icon="fluent:delete-24-regular"
                    data-icon-color="danger">
                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon> Hapus Regulasi
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>