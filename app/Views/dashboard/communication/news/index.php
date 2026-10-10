<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$categories  = $categories ?? [];
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$category    = $filterCategory !== null ? (int) $filterCategory : null;
$stats       = $stats ?? ['total' => count($items), 'published' => 0, 'in_review' => 0, 'draft' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Berita & Publikasi</h6>
        <p class="text-secondary-light mb-0">Kelola warta, siaran pers, dan pembaruan informasi seputar ekosistem PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.communication.news.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Berita
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Berita</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:newspaper-outline"></iconify-icon>
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
                    <span class="text-secondary-light text-sm mb-1 d-block">Draft Tersimpan</span>
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
        <form method="get" action="<?= route_to('dashboard.communication.news') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Kata Kunci</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul berita...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Status Publikasi</label>
                    <select name="status" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>Ditinjau</option>
                        <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Kategori</label>
                    <select name="category" class="form-select radius-8">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ($category === (int) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
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
        'icon'        => 'solar:newspaper-outline',
        'title'       => 'Belum Ada Berita',
        'message'     => 'Belum ada warta atau siaran berita yang sesuai dengan filter pencarian.',
        'actionUrl'   => route_to('dashboard.communication.news.create'),
        'actionText'  => 'Tambah Berita Baru',
    ]) ?>
<?php else: ?>
    <div class="row gy-4 mb-24">
        <?php foreach ($items as $n): ?>
            <?php
                $statusKey = $n['workflow_status'] ?? 'draft';
                $authorName = $n['author_name'] ?? 'Redaksi Begawi';
                $initials = strtoupper(substr($authorName, 0, 2));
            ?>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100 p-0 radius-12 border overflow-hidden bg-base d-flex flex-column">
                    <div class="card-body p-20 d-flex flex-column flex-grow-1">
                        <!-- Top Meta & Status -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-16">
                            <span class="px-12 py-4 bg-primary-50 text-primary-600 rounded-pill text-xs fw-medium">
                                <?= esc($n['category_name'] ?? 'Umum') ?>
                            </span>
                            <div>
                                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                            </div>
                        </div>

                        <!-- Title -->
                        <h6 class="mb-12">
                            <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="text-line-2 text-hover-primary-600 text-lg fw-semibold transition-2">
                                <?= esc($n['title'] ?? '(Tanpa judul)') ?>
                            </a>
                        </h6>

                        <!-- Summary / Excerpt -->
                        <p class="text-line-3 text-secondary-light text-sm mb-20 flex-grow-1">
                            <?= esc($n['summary'] ?: 'Tidak ada ringkasan teks. Klik detail untuk membaca keseluruhan naskah berita.') ?>
                        </p>

                        <!-- Footer Info & Actions -->
                        <div class="pt-16 border-top d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold">
                                    <?= esc($initials) ?>
                                </div>
                                <div>
                                    <span class="text-xs fw-medium text-neutral-800 d-block"><?= esc($authorName) ?></span>
                                    <span class="text-2xs text-secondary-light"><?= ! empty($n['published_at']) ? esc(date('d M Y', strtotime($n['published_at']))) : 'Belum Terbit' ?></span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                    <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>">
                                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Edit Berita
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-sm text-danger-main d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmationActionModal"
                                                data-action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>"
                                                data-method="POST"
                                                data-title="Hapus Warta / Berita"
                                                data-message="Apakah Anda yakin ingin menghapus artikel berita &quot;<?= esc($n['title'] ?? '') ?>&quot;?"
                                                data-btn-text="Ya, Hapus"
                                                data-btn-class="btn-danger-600"
                                                data-icon="fluent:delete-24-regular"
                                                data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                            <span>Hapus</span>
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