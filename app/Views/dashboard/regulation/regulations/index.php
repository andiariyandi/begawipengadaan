<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$regulations      = $regulations ?? [];
$categories       = $categories ?? [];
$filters          = $filters ?? [];
$workflowStatuses = $workflowStatuses ?? [];
$legalStatuses    = $legalStatuses ?? ['active', 'amended', 'revoked', 'expired'];
$stats            = $stats ?? ['total' => count($regulations), 'active' => 0, 'published' => 0, 'in_review' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Regulasi PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola naskah hukum, perpres, peraturan LKPP, peraturan daerah, riwayat versi, dan relasi regulasi.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('regulasi') ?>" target="_blank" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:square-top-down-outline" class="text-lg"></iconify-icon>
            <span>Lihat Portal Publik</span>
        </a>
        <a href="<?= site_url('dashboard/regulasi/dokumen/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Regulasi</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Regulasi</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:diploma-verified-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Status Berlaku</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['active'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Dipublikasikan</span>
                    <h5 class="fw-semibold text-primary-600 mb-0"><?= number_format($stats['published'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:share-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Proses Review / Draft</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['in_review'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form action="<?= site_url('dashboard/regulasi/dokumen') ?>" method="get">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Naskah</label>
                    <div class="position-relative">
                        <input type="text" class="form-control radius-8 ps-40" name="keyword" value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nomor naskah, judul, kata kunci...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Kategori Dokumen</label>
                    <select class="form-select radius-8" name="category_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) ($filters['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Status Workflow</label>
                    <select class="form-select radius-8" name="workflow_status">
                        <option value="">Semua Workflow</option>
                        <?php foreach ($workflowStatuses as $ws): ?>
                            <option value="<?= esc($ws) ?>" <?= ($filters['workflow_status'] ?? '') === $ws ? 'selected' : '' ?>>
                                <?= esc(ucwords(str_replace('_', ' ', $ws))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Status Legal</label>
                    <select class="form-select radius-8" name="legal_status">
                        <option value="">Semua Legalitas</option>
                        <option value="active" <?= ($filters['legal_status'] ?? '') === 'active' ? 'selected' : '' ?>>Berlaku</option>
                        <option value="amended" <?= ($filters['legal_status'] ?? '') === 'amended' ? 'selected' : '' ?>>Diubah</option>
                        <option value="revoked" <?= ($filters['legal_status'] ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut</option>
                        <option value="expired" <?= ($filters['legal_status'] ?? '') === 'expired' ? 'selected' : '' ?>>Kedaluwarsa</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Sorotan</label>
                    <select class="form-select radius-8" name="is_featured">
                        <option value="">Semua</option>
                        <option value="1" <?= (string) ($filters['is_featured'] ?? '') === '1' ? 'selected' : '' ?>>Ya</option>
                        <option value="0" <?= (string) ($filters['is_featured'] ?? '') === '0' ? 'selected' : '' ?>>Tidak</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Datatable Card -->
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($regulations)): ?>
            <?= view('dashboard/components/empty_state', [
                'icon'        => 'solar:diploma-verified-outline',
                'title'       => 'Belum Ada Dokumen Regulasi',
                'message'     => 'Belum ada dokumen regulasi PBJ yang terdaftar atau cocok dengan kriteria filter.',
                'actionUrl'   => site_url('dashboard/regulasi/dokumen/tambah'),
                'actionText'  => 'Tambah Regulasi Baru',
            ]) ?>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 320px;">Naskah &amp; Judul Regulasi</th>
                            <th>Kategori</th>
                            <th>Tahapan PBJ</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Versi</th>
                            <th class="text-center">Workflow</th>
                            <th class="text-center">Status Legal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($regulations as $r): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Thumbnail Cover atau Icon -->
                                        <?php if (! empty($r['cover_file_id'])): ?>
                                            <div class="w-48-px h-48-px radius-8 overflow-hidden flex-shrink-0 border bg-neutral-100">
                                                <img src="<?= base_url('file/' . (int) $r['cover_file_id']) ?>" alt="Cover" class="w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="w-44-px h-44-px bg-primary-50 text-primary-600 radius-8 d-flex align-items-center justify-content-center flex-shrink-0">
                                                <iconify-icon icon="solar:document-text-outline" class="text-xl"></iconify-icon>
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h6 class="text-sm mb-0 fw-bold text-neutral-900"><?= esc($r['document_number'] ?? '-') ?></h6>
                                                <?php if (! empty($r['is_featured'])): ?>
                                                    <span class="badge bg-warning-50 text-warning-700 border border-warning-200 radius-4 px-6 py-2 text-xs">
                                                        <iconify-icon icon="solar:star-bold" class="align-middle me-1"></iconify-icon>Sorotan
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>" class="text-xs text-secondary-light text-line-2 text-hover-primary-600 mb-1 d-block">
                                                <?= esc($r['title'] ?? '-') ?>
                                            </a>
                                            <?php if (! empty($r['issuer_name'])): ?>
                                                <span class="text-xs text-neutral-500 d-block">
                                                    <iconify-icon icon="solar:buildings-2-linear" class="align-middle me-1"></iconify-icon><?= esc($r['issuer_name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 text-xs fw-semibold">
                                        <?= esc($r['category_name'] ?? 'Umum') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (! empty($r['stages'])): ?>
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 220px;">
                                            <?php foreach (array_slice($r['stages'], 0, 2) as $st): ?>
                                                <span class="badge bg-info-50 text-info-700 radius-4 px-6 py-2 text-xs">
                                                    <?= esc($st['stage_name'] ?? $st['name'] ?? '-') ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($r['stages']) > 2): ?>
                                                <span class="badge bg-neutral-100 text-neutral-600 radius-4 px-6 py-2 text-xs">
                                                    +<?= count($r['stages']) - 2 ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-sm text-neutral-800"><?= esc($r['document_year'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-50 text-info-600 radius-6 px-8 py-2 text-xs fw-semibold">
                                        v<?= esc($r['current_version_number'] ?? '1.0') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $r['workflow_status'] ?? 'draft']) ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $r['legal_status'] ?? 'active']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Lihat Detail</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id'] . '/ubah') ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Regulasi</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/regulasi/relasi') ?>?regulation_id=<?= (int) $r['id'] ?>">
                                                    <iconify-icon icon="solar:link-circle-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Relasi Regulasi</span>
                                                </a>
                                            </li>
                                            <?php if (! empty($r['slug'])): ?>
                                                <li><hr class="dropdown-divider my-4"></li>
                                                <li>
                                                    <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('regulasi/' . esc($r['slug'])) ?>" target="_blank">
                                                        <iconify-icon icon="solar:square-top-down-outline" class="text-lg text-success-main"></iconify-icon>
                                                        <span>Buka di Landing Page</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('regulations', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>