<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$regulations      = $regulations ?? [];
$categories       = $categories ?? [];
$filters          = $filters ?? [];
$workflowStatuses = $workflowStatuses ?? [];
$stats            = $stats ?? ['total' => count($regulations), 'active' => 0, 'published' => 0, 'in_review' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Regulasi PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola naskah hukum, perpres, peraturan LKPP, dan regulasi daerah pengadaan barang/jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/regulasi/dokumen/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Regulasi
        </a>
    </div>
</div>

<!-- KPI Cards -->
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
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Naskah</label>
                    <div class="position-relative">
                        <input type="text" class="form-control radius-8 ps-40" name="keyword" value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nomor naskah atau judul regulasi...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Status Workflow</label>
                    <select class="form-select radius-8" name="workflow_status">
                        <option value="">Semua Status Workflow</option>
                        <?php foreach ($workflowStatuses as $ws): ?>
                            <option value="<?= esc($ws) ?>" <?= ($filters['workflow_status'] ?? '') === $ws ? 'selected' : '' ?>>
                                <?= esc(ucwords(str_replace('_', ' ', $ws))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Datatable -->
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($regulations)): ?>
            <?= view('dashboard/components/empty_state', [
                'icon'        => 'solar:diploma-verified-outline',
                'title'       => 'Belum Ada Regulasi',
                'message'     => 'Belum ada dokumen regulasi PBJ yang terdaftar atau cocok dengan kriteria filter.',
                'actionUrl'   => site_url('dashboard/regulasi/dokumen/tambah'),
                'actionText'  => 'Tambah Regulasi Baru',
            ]) ?>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Naskah Regulasi</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Versi Aktif</th>
                            <th class="text-center">Workflow</th>
                            <th class="text-center">Status Legal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($regulations as $r): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                            <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                                        </div>
                                        <div>
                                            <h6 class="text-md mb-2 fw-semibold text-neutral-900"><?= esc($r['document_number'] ?? '-') ?></h6>
                                            <span class="text-xs text-secondary-light text-line-1 max-w-400-px"><?= esc($r['title'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4"><?= esc($r['category_name'] ?? '-') ?></span></td>
                                <td><span class="fw-medium text-sm"><?= esc($r['document_year'] ?? '-') ?></span></td>
                                <td><span class="badge bg-info-50 text-info-600 radius-6 px-8 py-2">v<?= esc($r['current_version_number'] ?? '0') ?></span></td>
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
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url('dashboard/regulasi/hubungan') ?>?regulation_id=<?= (int) $r['id'] ?>">
                                                    <iconify-icon icon="solar:link-circle-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Hubungan Regulasi</span>
                                                </a>
                                            </li>
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