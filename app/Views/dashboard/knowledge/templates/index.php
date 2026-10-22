<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$categories  = $categories ?? [];
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$category    = $filterCategory !== null ? (string) $filterCategory : '';
$stats       = $stats ?? ['total' => count($items), 'published' => 0, 'draft' => 0, 'in_review' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Template Dokumen PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola format dokumen standar, KAK, HPS, dan rancangan kontrak PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.document-templates.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Template
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/knowledge/_nav_knowledge', ['section' => 'articles', 'activeSub' => 'templates']) ?>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Template</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:file-text-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Dipublikasikan</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['published'] ?? 0) ?></h5>
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
                    <span class="text-secondary-light text-sm mb-1 d-block">Dalam Peninjauan</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['in_review'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Draft Dokumen</span>
                    <h5 class="fw-semibold text-neutral-600 mb-0"><?= number_format($stats['draft'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-neutral-100 text-neutral-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= route_to('dashboard.knowledge.document-templates') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Nama Dokumen</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama template...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Status Publikasi</label>
                    <select name="status" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>Ditinjau</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Kategori</label>
                    <select name="category" class="form-select radius-8">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ($category === (string) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Portal Grid Cards -->
<?php if (empty($items)): ?>
    <?= view('dashboard/components/empty_state', [
        'icon'        => 'solar:file-text-outline',
        'title'       => 'Belum Ada Template Dokumen',
        'message'     => 'Belum ada format atau template dokumen PBJ yang terdaftar.',
        'actionUrl'   => route_to('dashboard.knowledge.document-templates.create'),
        'actionText'  => 'Tambah Template Baru',
    ]) ?>
<?php else: ?>
    <div class="row gy-4 mb-24">
        <?php foreach ($items as $row): ?>
            <?php
                $statusKey = $row['workflow_status'] ?? 'draft';
                $hasFile = !empty($row['cover_file_id']);
            ?>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100 p-0 radius-12 border overflow-hidden bg-base d-flex flex-column shadow-sm">
                    <div class="card-body p-20 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-16">
                            <div class="d-flex align-items-center gap-1">
                                <span class="px-10 py-3 bg-primary-50 text-primary-700 rounded-pill text-2xs fw-semibold text-uppercase">
                                    <?= esc($row['document_category'] ?? 'Template') ?>
                                </span>
                                <span class="px-8 py-3 bg-neutral-100 text-neutral-700 rounded-pill text-2xs font-monospace">
                                    <?= esc($row['document_version'] ?? 'v1.0') ?>
                                </span>
                                <?php if ($hasFile): ?>
                                    <span class="badge bg-success-50 text-success-700 radius-4 px-6 py-2 text-2xs">
                                        <i class="fas fa-check-circle me-1"></i> File Siap
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                            </div>
                        </div>

                        <h6 class="mb-12">
                            <a href="<?= route_to('dashboard.knowledge.document-templates.detail', (string) $row['id']) ?>" class="text-line-2 text-hover-primary-600 text-lg fw-semibold transition-2">
                                <?= esc($row['title']) ?>
                            </a>
                        </h6>

                        <p class="text-line-3 text-secondary-light text-sm mb-20 flex-grow-1">
                            <?= esc($row['summary'] ?: 'Format acuan dokumen pengadaan barang/jasa pemerintah yang dapat diunduh atau dipelajari.') ?>
                        </p>

                        <div class="pt-16 border-top d-flex align-items-center justify-content-between">
                            <span class="text-xs text-secondary-light">
                                <iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>
                                <?= esc(date('d M Y', strtotime($row['updated_at'] ?? 'now'))) ?>
                            </span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                    <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.knowledge.document-templates.detail', (string) $row['id']) ?>">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.knowledge.document-templates.edit', (string) $row['id']) ?>">
                                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Ubah
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-sm text-danger-600 d-flex align-items-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                            data-action="<?= route_to('dashboard.knowledge.document-templates.delete', (string) $row['id']) ?>"
                                            data-method="POST"
                                            data-title="Arsipkan Template Dokumen"
                                            data-message="Apakah Anda yakin ingin mengarsipkan atau menghapus template dokumen ini?"
                                            data-btn-text="Arsipkan"
                                            data-btn-class="btn-danger-600"
                                            data-icon="solar:trash-bin-trash-outline"
                                            data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon> Arsipkan
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>