<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$regulation       = $regulation ?? [];
$versions         = $regulation['versions'] ?? [];
$relations        = $relations ?? [];
$competencies     = $competencies ?? [];
$transitions      = $transitions ?? [];
$stages           = $regulation['stages'] ?? [];
$workflowStatuses = $workflowStatuses ?? [];
$coverImgUrl      = ! empty($regulation['cover_file_id']) ? base_url('file/' . (int) $regulation['cover_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Dokumen Regulasi</h6>
        <p class="text-secondary-light mb-0">Nomor Registrasi: #<?= esc($regulation['id'] ?? '-') ?> &middot; Kode Slug: <code><?= esc($regulation['slug'] ?? '-') ?></code></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (! empty($regulation['slug'])): ?>
            <a href="<?= site_url('regulasi/' . esc($regulation['slug'])) ?>" target="_blank" class="btn btn-outline-success-600 radius-8 px-16 py-10 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:square-top-down-outline" class="text-lg"></iconify-icon>
                <span>Halaman Publik</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($regulation['id'] ?? 0) . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            <span>Ubah Regulasi</span>
        </a>
        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<!-- Header Information Card -->
<div class="card radius-16 border shadow-sm mb-24 bg-white overflow-hidden">
    <?php if ($coverImgUrl): ?>
        <div class="position-relative w-100 bg-neutral-900" style="max-height: 260px; overflow: hidden;">
            <img src="<?= esc($coverImgUrl) ?>" alt="Cover Regulasi" class="w-100 h-100 object-fit-cover" style="opacity: 0.92; max-height: 260px;">
            <div class="position-absolute bottom-0 start-0 w-100 p-20" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.75) 100%);">
                <span class="badge bg-white text-primary-900 radius-6 px-12 py-6 text-xs fw-bold mb-1 shadow-sm">
                    <iconify-icon icon="solar:gallery-bold" class="me-1"></iconify-icon> Cover Banner Resmi
                </span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div class="d-flex align-items-start gap-3">
                <div class="w-56-px h-56-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0 mt-1">
                    <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-primary-50 text-primary-700 radius-6 px-12 py-4 text-xs fw-semibold">
                            <?= esc($regulation['category_name'] ?? 'Regulasi PBJ') ?>
                        </span>
                        <?php if (! empty($regulation['is_featured'])): ?>
                            <span class="badge bg-warning-50 text-warning-700 border border-warning-200 radius-6 px-10 py-4 text-xs fw-bold">
                                <iconify-icon icon="solar:star-bold" class="me-1"></iconify-icon>Regulasi Sorotan Utama
                            </span>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold mb-1 text-primary-light"><?= esc($regulation['document_number'] ?? '-') ?></h4>
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
        <div class="card radius-12 border bg-white shadow-sm">
            <div class="card-header bg-base border-bottom p-0">
                <ul class="nav border-gradient-tab nav-pills px-24 pt-12 gap-4 flex-wrap" id="regTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-summary-btn" data-bs-toggle="pill" data-bs-target="#tab-summary" type="button" role="tab">
                            <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                            Naskah &amp; Ringkasan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="tab-stages-btn" data-bs-toggle="pill" data-bs-target="#tab-stages" type="button" role="tab">
                            <iconify-icon icon="solar:route-outline" class="text-lg"></iconify-icon>
                            Tahapan PBJ (<?= count($stages) ?>)
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
                    <!-- Tab 1: Ringkasan & Metadata -->
                    <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">
                        <h6 class="text-md fw-semibold mb-12 text-primary-light">Ringkasan &amp; Pokok Substansi</h6>
                        <div class="p-16 radius-8 bg-neutral-50 border mb-24">
                            <p class="text-secondary-light mb-0 text-sm lh-lg">
                                <?= nl2br(esc($regulation['summary'] ?: 'Tidak ada ringkasan atau abstrak naskah regulasi.')) ?>
                            </p>
                        </div>

                        <?php if (! empty($regulation['keywords'])): ?>
                            <div class="mb-24">
                                <h6 class="text-xs fw-semibold text-secondary-light text-uppercase mb-8">Kata Kunci / Tag Pencarian:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach (array_filter(array_map('trim', explode(',', $regulation['keywords']))) as $kw): ?>
                                        <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 text-xs fw-medium">
                                            #<?= esc($kw) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($regulation['official_source_url'])): ?>
                            <div class="p-16 radius-8 border d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-50">
                                <div>
                                    <span class="text-xs text-secondary-light d-block mb-1">Tautan Sumber Resmi / JDIH:</span>
                                    <span class="text-sm fw-medium text-neutral-800 text-break"><?= esc($regulation['official_source_url']) ?></span>
                                </div>
                                <a href="<?= esc($regulation['official_source_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary-600 btn-sm radius-8 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:square-top-down-outline"></iconify-icon>
                                    <span>Buka Sumber</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 2: Tahapan PBJ -->
                    <div class="tab-pane fade" id="tab-stages" role="tabpanel">
                        <h6 class="text-md fw-semibold mb-12 text-primary-light">Tahapan Pengadaan Barang/Jasa yang Diatur</h6>
                        <?php if (empty($stages)): ?>
                            <p class="text-secondary-light mb-0">Belum ada tahapan PBJ yang ditautkan ke regulasi ini. Anda dapat mengaturnya pada form ubah regulasi.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($stages as $st): ?>
                                    <div class="col-md-6">
                                        <div class="p-16 radius-8 border bg-neutral-50 h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-info-100 text-info-700 radius-4 px-8 py-2 text-xs fw-bold">
                                                    <?= esc($st['stage_key'] ?? $st['key'] ?? 'PBJ') ?>
                                                </span>
                                                <h6 class="text-sm fw-bold mb-0 text-dark"><?= esc($st['stage_name'] ?? $st['name'] ?? '-') ?></h6>
                                            </div>
                                            <p class="text-xs text-secondary-light mb-0"><?= esc($st['description'] ?? 'Tahapan pelaksanaan proses pengadaan barang/jasa pemerintah.') ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Versi -->
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
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <?php if (! empty($v['file_id'])): ?>
                                                            <a href="<?= base_url('file/' . (int) $v['file_id']) ?>" target="_blank" class="btn btn-outline-neutral-300 btn-sm radius-6 px-8 py-4 text-xs" title="Pratinjau Berkas">
                                                                <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (! $v['is_current']): ?>
                                                            <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi/current') ?>" method="post" class="d-inline">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="version_id" value="<?= (int) $v['id'] ?>">
                                                                <button type="submit" class="btn btn-outline-primary-600 btn-sm radius-8 text-xs py-4">Set Aktif</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="badge bg-success-50 text-success-700 radius-4 text-2xs py-4 px-8 fw-semibold">Versi Utama</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Version Form -->
                        <div class="p-20 radius-8 border bg-neutral-50">
                            <h6 class="text-sm fw-semibold mb-12 text-primary-light">Unggah Versi Baru Dokumen</h6>
                            <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-medium mb-1">File Dokumen (PDF/DOC, Maks 20 MB)</label>
                                        <input type="file" class="form-control radius-8 form-control-sm" name="file" required accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-medium mb-1">Catatan Perubahan Versi (Changelog)</label>
                                        <input type="text" class="form-control radius-8 form-control-sm" name="changelog" placeholder="Contoh: Pembaruan naskah salinan resmi beserta lampiran...">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8">
                                            <iconify-icon icon="solar:upload-outline"></iconify-icon> Unggah Versi Baru
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tab 4: Relasi -->
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
                            <iconify-icon icon="solar:settings-outline"></iconify-icon> Kelola Matriks Relasi Regulasi
                        </a>
                    </div>

                    <!-- Tab 5: Kompetensi -->
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

                    <!-- Tab 6: Audit Transisi -->
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

    <!-- Right Sidebar Column: Workflow Action Panel & Pengaturan -->
    <div class="col-xl-4">
        <div class="card radius-12 border mb-24 bg-white shadow-sm">
            <div class="card-header bg-base border-bottom py-16 px-20">
                <h6 class="text-md fw-semibold mb-0">Tindakan Workflow &amp; Status</h6>
            </div>
            <div class="card-body p-20">
                <?php if (($regulation['workflow_status'] ?? '') === 'draft'): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/submit') ?>" method="post" class="mb-12">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:plain-2-outline"></iconify-icon>
                            <span>Submit untuk Peninjauan</span>
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
                            <span>Publikasikan Dokumen</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (($regulation['workflow_status'] ?? '') === 'published'): ?>
                    <div class="alert alert-success radius-8 py-12 px-16 mb-0 d-flex align-items-center gap-2 text-sm">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xl flex-shrink-0"></iconify-icon>
                        Dokumen telah aktif dan dipublikasikan ke portal.
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

        <!-- Metadata Card -->
        <div class="card radius-12 border bg-white shadow-sm">
            <div class="card-header bg-base border-bottom py-16 px-20">
                <h6 class="text-md fw-semibold mb-0">Ringkasan Sistem</h6>
            </div>
            <div class="card-body p-20">
                <div class="mb-12">
                    <span class="text-xs text-secondary-light d-block mb-1">Dibuat Oleh:</span>
                    <span class="text-sm fw-medium text-dark"><?= esc($regulation['creator_name'] ?: 'Administrator') ?></span>
                </div>
                <div class="mb-12">
                    <span class="text-xs text-secondary-light d-block mb-1">Waktu Pembuatan:</span>
                    <span class="text-sm fw-medium text-dark"><?= esc($regulation['created_at'] ? date('d M Y H:i', strtotime((string) $regulation['created_at'])) : '-') ?></span>
                </div>
                <div class="mb-12">
                    <span class="text-xs text-secondary-light d-block mb-1">Akses Visibilitas:</span>
                    <span class="badge bg-neutral-100 text-neutral-800 radius-4 text-xs"><?= esc(ucfirst($regulation['visibility'] ?? 'internal')) ?></span>
                </div>
                <?php if (! empty($regulation['publisher_name'])): ?>
                    <div class="mb-0">
                        <span class="text-xs text-secondary-light d-block mb-1">Dipublikasikan Oleh:</span>
                        <span class="text-sm fw-medium text-dark"><?= esc($regulation['publisher_name']) ?> (<?= esc($regulation['published_at'] ? date('d M Y', strtotime((string) $regulation['published_at'])) : '-') ?>)</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>