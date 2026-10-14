<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items          = $items ?? [];
$categories     = $categories ?? [];
$status         = $filterStatus ?? null;
$keyword        = $filterKeyword ?? null;
$category       = $filterCategory !== null ? (int) $filterCategory : null;
$featured       = $filterFeatured ?? null;
$viewMode       = $filterView ?? 'grid';
$sort           = $filterSort ?? 'newest';
$pager          = $pagerData ?? ['page' => 1, 'totalPages' => 1, 'totalRows' => count($items), 'hasPrev' => false, 'hasNext' => false];
$stats          = $stats ?? ['total' => count($items), 'published' => 0, 'in_review' => 0, 'draft' => 0, 'featured' => 0, 'total_views' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Berita &amp; Publikasi PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola warta, siaran pers, artikel kebijakan, dan pembaruan ekosistem pengadaan barang/jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:folder-with-files-outline" class="text-lg"></iconify-icon>
            Kelola Kategori
        </a>
        <a href="<?= route_to('dashboard.communication.news.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2 fw-medium text-sm">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Berita Baru
        </a>
    </div>
</div>

<!-- KPI Metrics Ribbon -->
<div class="row row-cols-xxl-6 row-cols-md-3 row-cols-2 g-3 mb-24">
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Total Berita</span>
                    <h5 class="fw-bold mb-0 text-dark"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-40-px h-40-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="solar:newspaper-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Dipublikasikan</span>
                    <h5 class="fw-bold text-success-main mb-0"><?= number_format($stats['published'] ?? 0) ?></h5>
                </div>
                <div class="w-40-px h-40-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Dalam Peninjauan</span>
                    <h5 class="fw-bold text-warning-main mb-0"><?= number_format($stats['in_review'] ?? 0) ?></h5>
                </div>
                <div class="w-40-px h-40-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Draft Tersimpan</span>
                    <h5 class="fw-bold text-neutral-800 mb-0"><?= number_format($stats['draft'] ?? 0) ?></h5>
                </div>
                <div class="w-40-px h-40-px bg-neutral-100 text-neutral-700 rounded-circle d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Berita Utama</span>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold text-amber mb-0" style="color:#d97706;"><?= number_format($stats['featured'] ?? 0) ?></h5>
                        <small class="text-neutral-600 text-xs fw-semibold">/ 5 Slot</small>
                    </div>
                </div>
                <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-xl" style="background:#fef3c7;color:#d97706;">
                    <iconify-icon icon="solar:star-fall-2-bold"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-16 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-neutral-700 text-xs mb-1 d-block fw-semibold">Total Dibaca</span>
                    <h5 class="fw-bold text-primary-600 mb-0"><?= number_format($stats['total_views'] ?? 0) ?></h5>
                </div>
                <div class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
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
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Kata Kunci</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40 text-sm" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul atau tagar...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-neutral-500 text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Status Publikasi</label>
                    <select name="status" class="form-select radius-8 text-sm">
                        <option value="">Semua Status</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>Ditinjau</option>
                        <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Kategori</label>
                    <select name="category" class="form-select radius-8 text-sm">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ($category === (int) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Sorotan</label>
                    <select name="featured" class="form-select radius-8 text-sm">
                        <option value="">Semua Berita</option>
                        <option value="1" <?= $featured === 1 ? 'selected' : '' ?>>Berita Utama Saja (Maks. 5)</option>
                        <option value="0" <?= $featured === 0 ? 'selected' : '' ?>>Bukan Utama</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1 text-sm py-10 fw-semibold">
                        <iconify-icon icon="solar:filter-outline" class="text-base"></iconify-icon> Filter
                    </button>
                    <!-- View Mode Toggle -->
                    <div class="btn-group" role="group">
                        <a href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['view' => 'grid'])) ?>"
                           class="btn btn-outline-neutral-300 radius-8 py-10 px-12 <?= $viewMode === 'grid' ? 'active bg-neutral-200' : '' ?>" title="Tampilan Kartu">
                            <iconify-icon icon="solar:widget-2-outline" class="text-base"></iconify-icon>
                        </a>
                        <a href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['view' => 'table'])) ?>"
                           class="btn btn-outline-neutral-300 radius-8 py-10 px-12 <?= $viewMode === 'table' ? 'active bg-neutral-200' : '' ?>" title="Tampilan Tabel">
                            <iconify-icon icon="solar:list-linear" class="text-base"></iconify-icon>
                        </a>
                    </div>
                    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 py-10 px-12" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-base"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- News Content -->
<?php if (empty($items)): ?>
    <?= view('dashboard/components/empty_state', [
        'icon'        => 'solar:newspaper-outline',
        'title'       => 'Belum Ada Berita',
        'message'     => 'Belum ada warta atau siaran berita yang sesuai dengan kriteria filter pencarian.',
        'actionUrl'   => route_to('dashboard.communication.news.create'),
        'actionText'  => 'Tambah Berita Baru',
    ]) ?>
<?php else: ?>

    <?php if ($viewMode === 'table'): ?>
        <!-- TABLE VIEW -->
        <div class="card radius-12 border overflow-hidden mb-24">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle">
                        <thead class="bg-neutral-100 text-neutral-800 fw-bold">
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul &amp; Kategori</th>
                                <th>Penulis &amp; Tanggal</th>
                                <th class="text-center">Sorotan Utama</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Dibaca</th>
                                <th class="text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $n): ?>
                                <?php
                                    $coverUrl = ! empty($n['cover_path']) ? base_url('file/' . (int) $n['cover_file_id']) : null;
                                    $authorName = $n['author_name'] ?? 'Redaksi Begawi';
                                ?>
                                <tr>
                                    <td>
                                        <div class="w-64-px h-48-px rounded-8 overflow-hidden bg-neutral-100 border d-flex align-items-center justify-content-center">
                                            <?php if ($coverUrl): ?>
                                                <img src="<?= esc($coverUrl) ?>" alt="Cover" style="width:100%;height:100%;object-fit:cover;">
                                            <?php else: ?>
                                                <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-500 text-xl"></iconify-icon>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <span class="badge bg-primary-50 text-primary-700 rounded-pill text-xs fw-bold px-8 py-3 border border-primary-200">
                                                <?= esc($n['category_name'] ?? 'Umum') ?>
                                            </span>
                                            <?php if (! empty($n['reading_time'])): ?>
                                                <span class="text-neutral-700 text-xs fw-medium d-inline-flex align-items-center gap-1">
                                                    <iconify-icon icon="solar:clock-circle-outline" class="text-neutral-600 text-xs"></iconify-icon>
                                                    <?= esc($n['reading_time']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-2 d-block">
                                            <?= esc($n['title'] ?? '(Tanpa judul)') ?>
                                        </a>
                                        <small class="text-neutral-600 font-monospace text-xs">/berita/<?= esc($n['slug']) ?></small>
                                    </td>
                                    <td>
                                        <span class="text-xs fw-bold text-neutral-900 d-block"><?= esc($authorName) ?></span>
                                        <span class="text-xs text-neutral-600"><?= ! empty($n['published_at']) ? esc(date('d M Y', strtotime($n['published_at']))) : 'Belum Terbit' ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (! empty($n['is_featured'])): ?>
                                            <span class="badge bg-warning-50 text-warning-main border border-warning-200 rounded-pill px-8 py-4 text-xs fw-bold">
                                                <iconify-icon icon="solar:star-bold"></iconify-icon> Headline (Utama)
                                            </span>
                                        <?php else: ?>
                                            <span class="text-neutral-400 text-xs">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $n['workflow_status'] ?? 'draft']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-50 text-info-700 border border-info-200 px-8 py-4 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            <?= number_format((int) ($n['view_count'] ?? 0)) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                                <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                                <li>
                                                    <a class="dropdown-item text-xs d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>">
                                                        <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-xs d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>">
                                                        <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Edit Berita
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-xs text-danger-main d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                            data-action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>"
                                                            data-method="POST"
                                                            data-title="Hapus Berita"
                                                            data-message="Apakah Anda yakin ingin menghapus berita &quot;<?= esc($n['title'] ?? '') ?>&quot;?"
                                                            data-btn-text="Ya, Hapus"
                                                            data-btn-class="btn-danger-600">
                                                        <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                        <span>Hapus Berita</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- GRID VIEW -->
        <div class="row gy-4 mb-24">
            <?php foreach ($items as $n): ?>
                <?php
                    $coverUrl   = ! empty($n['cover_path']) ? base_url('file/' . (int) $n['cover_file_id']) : null;
                    $statusKey  = $n['workflow_status'] ?? 'draft';
                    $authorName = $n['author_name'] ?? 'Redaksi Begawi';
                    $initials   = strtoupper(substr($authorName, 0, 2));
                ?>
                <div class="col-xxl-4 col-md-6">
                    <div class="card h-100 p-0 radius-12 border overflow-hidden bg-base d-flex flex-column shadow-none">
                        <!-- Cover Image Area -->
                        <div class="position-relative h-180-px bg-neutral-100 overflow-hidden d-flex align-items-center justify-content-center">
                            <?php if ($coverUrl): ?>
                                <img src="<?= esc($coverUrl) ?>" alt="<?= esc($n['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-500 text-5xl"></iconify-icon>
                            <?php endif; ?>

                            <!-- Badges Overlay -->
                            <div class="position-absolute top-0 start-0 m-12 d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark shadow-sm rounded-pill text-xs fw-bold px-10 py-4 border">
                                    <?= esc($n['category_name'] ?? 'Umum') ?>
                                </span>
                                <?php if (! empty($n['is_featured'])): ?>
                                    <span class="badge bg-warning text-dark shadow-sm rounded-pill text-xs fw-bold px-8 py-4 border border-warning">
                                        <iconify-icon icon="solar:star-bold"></iconify-icon> Headline
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="position-absolute top-0 end-0 m-12">
                                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-20 d-flex flex-column flex-grow-1">
                            <!-- Meta Bar -->
                            <div class="d-flex align-items-center justify-content-between text-neutral-700 text-xs fw-medium mb-10">
                                <span><iconify-icon icon="solar:calendar-outline" class="me-1 text-primary-600 align-middle"></iconify-icon><?= ! empty($n['published_at']) ? esc(date('d M Y', strtotime($n['published_at']))) : 'Draft' ?></span>
                                <span><iconify-icon icon="solar:eye-outline" class="me-1 text-info-600 align-middle"></iconify-icon><strong class="text-neutral-900"><?= number_format((int) ($n['view_count'] ?? 0)) ?></strong> views</span>
                            </div>

                            <!-- Title -->
                            <h6 class="mb-10">
                                <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="text-line-2 text-hover-primary-600 text-base fw-bold transition-2 text-neutral-900">
                                    <?= esc($n['title'] ?? '(Tanpa judul)') ?>
                                </a>
                            </h6>

                            <!-- Excerpt -->
                            <p class="text-line-3 text-neutral-700 text-xs mb-16 flex-grow-1" style="line-height: 1.6;">
                                <?= esc($n['summary'] ?: 'Tidak ada ringkasan teks. Klik detail untuk melihat naskah lengkap.') ?>
                            </p>

                            <!-- Tags Pills if present -->
                            <?php if (! empty($n['tags'])): ?>
                                <div class="d-flex flex-wrap gap-1 mb-16">
                                    <?php foreach (explode(',', $n['tags']) as $tag): ?>
                                        <span class="badge bg-neutral-100 text-neutral-700 border text-xs fw-medium"><?= esc(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Footer Author & Actions -->
                            <div class="pt-14 border-top d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-28-px h-28-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold">
                                        <?= esc($initials) ?>
                                    </div>
                                    <span class="text-xs fw-medium text-neutral-800"><?= esc($authorName) ?></span>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                        <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                        <li>
                                            <a class="dropdown-item text-xs d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>">
                                                <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-xs d-flex align-items-center gap-2" href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>">
                                                <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Edit Berita
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-xs text-danger-main d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                    data-action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>"
                                                    data-method="POST"
                                                    data-title="Hapus Berita"
                                                    data-message="Apakah Anda yakin ingin menghapus berita &quot;<?= esc($n['title'] ?? '') ?>&quot;?"
                                                    data-btn-text="Ya, Hapus"
                                                    data-btn-class="btn-danger-600">
                                                <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                <span>Hapus Berita</span>
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

    <!-- Pagination Controls -->
    <?php if ($pager['totalPages'] > 1): ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-12 border-top">
            <span class="text-secondary-light text-xs">
                Menampilkan halaman <strong><?= $pager['page'] ?></strong> dari <strong><?= $pager['totalPages'] ?></strong> (Total <strong><?= number_format($pager['totalRows']) ?></strong> artikel)
            </span>
            <ul class="pagination pagination-sm mb-0 gap-1">
                <li class="page-item <?= ! $pager['hasPrev'] ? 'disabled' : '' ?>">
                    <a class="page-link radius-6 px-12 py-6 text-xs" href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $pager['prevPage']])) ?>">
                        <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $pager['totalPages']; $p++): ?>
                    <?php if ($p === 1 || $p === $pager['totalPages'] || abs($p - $pager['page']) <= 2): ?>
                        <li class="page-item <?= $p === $pager['page'] ? 'active' : '' ?>">
                            <a class="page-link radius-6 px-12 py-6 text-xs fw-bold" href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php elseif (abs($p - $pager['page']) === 3): ?>
                        <li class="page-item disabled"><span class="page-link radius-6 px-8 py-6 text-xs">...</span></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li class="page-item <?= ! $pager['hasNext'] ? 'disabled' : '' ?>">
                    <a class="page-link radius-6 px-12 py-6 text-xs" href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $pager['nextPage']])) ?>">
                        <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>