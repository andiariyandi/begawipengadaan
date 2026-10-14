<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Scoped News Dashboard Styling for Ultimate Readability & Tidiness */
.news-dashboard-container {
    color: #1e293b;
}

/* KPI Cards */
.kpi-news-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px;
    padding: 16px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-news-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px -2px rgba(0,0,0,0.05);
}

/* Status Badges Override for Crystal-Clear Visibility */
.news-dashboard-container .bg-neutral-200.text-secondary-light,
.news-dashboard-container .bg-neutral-200 {
    background-color: #f1f5f9 !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}
.news-dashboard-container .bg-warning-focus.text-warning-main,
.news-dashboard-container .bg-warning-50.text-warning-main {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border: 1px solid #fde68a !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}
.news-dashboard-container .bg-info-focus.text-info-main {
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}
.news-dashboard-container .bg-success-focus.text-success-main {
    background-color: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}
.news-dashboard-container .bg-danger-focus.text-danger-main {
    background-color: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}

/* Action Dropdown Button */
.btn-action-dropdown {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}
.btn-action-dropdown:hover,
.btn-action-dropdown:focus {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-action-dropdown:hover iconify-icon,
.btn-action-dropdown:focus iconify-icon {
    color: #ffffff !important;
}
.btn-action-dropdown iconify-icon {
    color: #1e293b !important;
    font-size: 18px !important;
}

/* Grid Card Polish */
.news-grid-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.news-grid-card:hover {
    border-color: #93c5fd;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -3px rgba(0,0,0,0.07);
}
.news-grid-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 8px;
    transition: color 0.2s;
}
.news-grid-title:hover {
    color: #0284c7;
}
.news-grid-excerpt {
    font-size: 12.5px;
    color: #475569;
    line-height: 1.5;
    height: 56px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 12px;
}
.news-grid-tags {
    height: 26px;
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 12px;
}
.news-grid-footer {
    border-top: 1px solid #f1f5f9;
    padding-top: 12px;
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Filter Card */
.news-filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

/* Table Card */
.news-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.news-table thead th {
    background: #f8fafc !important;
    color: #1e293b !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.news-table tbody td {
    padding: 12px 16px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
.news-table tbody tr:hover {
    background: #f8fafc !important;
}
</style>
<?= $this->endSection() ?>

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

<div class="news-dashboard-container">
    <!-- Header Page -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1">Berita &amp; Publikasi PBJ</h6>
            <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">Kelola warta, siaran pers, artikel kebijakan, dan pembaruan ekosistem pengadaan barang/jasa.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-semibold text-neutral-700" style="background:#ffffff; border-color:#cbd5e1;">
                <iconify-icon icon="solar:folder-with-files-outline" class="text-lg text-primary-600"></iconify-icon>
                Kelola Kategori
            </a>
            <a href="<?= route_to('dashboard.communication.news.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2 fw-bold text-sm shadow-sm">
                <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
                Tambah Berita Baru
            </a>
        </div>
    </div>

    <!-- KPI Metrics Ribbon (6 Uniform Equal-Height Cards) -->
    <div class="row row-cols-xxl-6 row-cols-md-3 row-cols-2 g-3 mb-24">
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Berita</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #e0f2fe; color: #0284c7;">
                        <iconify-icon icon="solar:newspaper-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;"><?= number_format($stats['total'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Dipublikasikan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #dcfce7; color: #16a34a;">
                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #15803d;"><?= number_format($stats['published'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Dalam Peninjauan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fef3c7; color: #d97706;">
                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #b45309;"><?= number_format($stats['in_review'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Draft Tersimpan</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #f1f5f9; color: #475569;">
                        <iconify-icon icon="solar:pen-new-square-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0 text-neutral-800" style="color: #334155;"><?= number_format($stats['draft'] ?? 0) ?></h4>
            </div>
        </div>
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Berita Utama</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #fef3c7; color: #b45309;">
                        <iconify-icon icon="solar:star-bold"></iconify-icon>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-1">
                    <h4 class="fw-bold mb-0" style="color: #b45309;"><?= number_format($stats['featured'] ?? 0) ?></h4>
                    <small class="text-xs fw-bold text-neutral-600" style="color: #64748b;">/ 5 Slot</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="kpi-news-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-neutral-700" style="color: #475569;">Total Dibaca</span>
                    <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center text-lg" style="background: #e0e7ff; color: #4f46e5;">
                        <iconify-icon icon="solar:eye-bold"></iconify-icon>
                    </div>
                </div>
                <h4 class="fw-bold mb-0" style="color: #4338ca;"><?= number_format($stats['total_views'] ?? 0) ?></h4>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="news-filter-card mb-24">
        <form method="get" action="<?= route_to('dashboard.communication.news') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-2">Kata Kunci</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 text-sm fw-medium"
                               style="padding-left: 42px !important; border-color: #cbd5e1; color: #0f172a;"
                               value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul atau tagar...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-14 text-neutral-700 text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-2">Status Publikasi</label>
                    <select name="status" class="form-select radius-8 text-sm fw-medium" style="border-color: #cbd5e1; color: #0f172a;">
                        <option value="">Semua Status</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>Ditinjau</option>
                        <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-2">Kategori</label>
                    <select name="category" class="form-select radius-8 text-sm fw-medium" style="border-color: #cbd5e1; color: #0f172a;">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ($category === (int) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-2">Sorotan</label>
                    <select name="featured" class="form-select radius-8 text-sm fw-medium" style="border-color: #cbd5e1; color: #0f172a;">
                        <option value="">Semua Berita</option>
                        <option value="1" <?= $featured === 1 ? 'selected' : '' ?>>Berita Utama Saja (Maks. 5)</option>
                        <option value="0" <?= $featured === 0 ? 'selected' : '' ?>>Bukan Utama</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-sm py-10 fw-bold">
                        <iconify-icon icon="solar:filter-bold" class="text-base"></iconify-icon> Filter
                    </button>
                    <!-- View Mode Toggle -->
                    <div class="btn-group" role="group">
                        <a href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['view' => 'grid'])) ?>"
                           class="btn radius-8 py-10 px-12 fw-bold <?= $viewMode === 'grid' ? 'btn-primary-600' : 'btn-outline-neutral-300 text-neutral-700' ?>"
                           style="<?= $viewMode !== 'grid' ? 'background:#ffffff; border-color:#cbd5e1;' : '' ?>"
                           title="Tampilan Kartu (Grid)">
                            <iconify-icon icon="solar:widget-2-outline" class="text-base"></iconify-icon>
                        </a>
                        <a href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['view' => 'table'])) ?>"
                           class="btn radius-8 py-10 px-12 fw-bold <?= $viewMode === 'table' ? 'btn-primary-600' : 'btn-outline-neutral-300 text-neutral-700' ?>"
                           style="<?= $viewMode !== 'table' ? 'background:#ffffff; border-color:#cbd5e1;' : '' ?>"
                           title="Tampilan Tabel (Table)">
                            <iconify-icon icon="solar:list-linear" class="text-base"></iconify-icon>
                        </a>
                    </div>
                    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 py-10 px-12 text-neutral-700 fw-bold"
                       style="background:#ffffff; border-color:#cbd5e1;" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-base text-neutral-700"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
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
            <div class="news-table-card mb-24">
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle news-table">
                        <thead>
                            <tr>
                                <th style="width: 85px;">Cover</th>
                                <th>Judul &amp; Kategori</th>
                                <th style="width: 170px;">Penulis &amp; Tanggal</th>
                                <th class="text-center" style="width: 140px;">Sorotan Utama</th>
                                <th class="text-center" style="width: 130px;">Status</th>
                                <th class="text-center" style="width: 100px;">Dibaca</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
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
                                        <div class="w-64-px h-48-px rounded-8 overflow-hidden bg-neutral-100 border d-flex align-items-center justify-content-center" style="border-color: #cbd5e1 !important;">
                                            <?php if ($coverUrl): ?>
                                                <img src="<?= esc($coverUrl) ?>" alt="Cover" style="width:100%;height:100%;object-fit:cover;">
                                            <?php else: ?>
                                                <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-500 text-xl"></iconify-icon>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary-50 text-primary-700 rounded-pill text-xs fw-bold px-8 py-3 border border-primary-200">
                                                <?= esc($n['category_name'] ?? 'Umum') ?>
                                            </span>
                                            <?php if (! empty($n['reading_time'])): ?>
                                                <span class="text-neutral-700 text-xs fw-semibold d-inline-flex align-items-center gap-1" style="color: #475569;">
                                                    <iconify-icon icon="solar:clock-circle-outline" class="text-neutral-600 text-xs"></iconify-icon>
                                                    <?= esc($n['reading_time']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="fw-bold text-neutral-900 text-hover-primary-600 text-sm line-clamp-1 mb-1 d-block" style="color: #0f172a;">
                                            <?= esc($n['title'] ?? '(Tanpa judul)') ?>
                                        </a>
                                        <small class="text-neutral-600 font-monospace text-xs" style="color: #64748b;">/berita/<?= esc($n['slug']) ?></small>
                                    </td>
                                    <td>
                                        <span class="text-xs fw-bold text-neutral-900 d-block" style="color: #0f172a;"><?= esc($authorName) ?></span>
                                        <span class="text-xs text-neutral-600 fw-medium" style="color: #64748b;"><?= ! empty($n['published_at']) ? esc(date('d M Y', strtotime($n['published_at']))) : 'Belum Terbit' ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (! empty($n['is_featured'])): ?>
                                            <span class="badge rounded-pill px-10 py-5 text-xs fw-bold d-inline-flex align-items-center gap-1" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                                <iconify-icon icon="solar:star-bold"></iconify-icon> Headline (Utama)
                                            </span>
                                        <?php else: ?>
                                            <span class="text-neutral-400 text-xs font-semibold">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $n['workflow_status'] ?? 'draft']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-10 py-5 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            <?= number_format((int) ($n['view_count'] ?? 0)) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Opsi">
                                                <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 py-6">
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold text-neutral-800 d-flex align-items-center gap-2 py-8 px-14" href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>">
                                                        <iconify-icon icon="solar:eye-outline" class="text-primary-600 text-base"></iconify-icon> Lihat Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-xs fw-semibold text-neutral-800 d-flex align-items-center gap-2 py-8 px-14" href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>">
                                                        <iconify-icon icon="solar:pen-2-outline" class="text-info-600 text-base"></iconify-icon> Edit Berita
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-xs fw-semibold text-danger d-flex align-items-center gap-2 py-8 px-14 border-0 bg-transparent w-100 text-start"
                                                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                            data-action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>"
                                                            data-method="POST"
                                                            data-title="Hapus Berita"
                                                            data-message="Apakah Anda yakin ingin menghapus berita &quot;<?= esc($n['title'] ?? '') ?>&quot;?"
                                                            data-btn-text="Ya, Hapus"
                                                            data-btn-class="btn-danger-600">
                                                        <iconify-icon icon="solar:trash-bin-trash-outline" class="text-danger text-base"></iconify-icon>
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
        <?php else: ?>
            <!-- GRID VIEW (Clean Cards with Uniform Baseline) -->
            <div class="row gy-4 mb-24">
                <?php foreach ($items as $n): ?>
                    <?php
                        $coverUrl   = ! empty($n['cover_path']) ? base_url('file/' . (int) $n['cover_file_id']) : null;
                        $statusKey  = $n['workflow_status'] ?? 'draft';
                        $authorName = $n['author_name'] ?? 'Redaksi Begawi';
                        $initials   = strtoupper(substr($authorName, 0, 2));
                    ?>
                    <div class="col-xxl-4 col-md-6">
                        <div class="news-grid-card">
                            <!-- Cover Image Area -->
                            <div class="position-relative" style="height: 185px; background: #f1f5f9; overflow: hidden;">
                                <?php if ($coverUrl): ?>
                                    <img src="<?= esc($coverUrl) ?>" alt="<?= esc($n['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                <?php else: ?>
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: #e2e8f0;">
                                        <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-500 text-5xl"></iconify-icon>
                                    </div>
                                <?php endif; ?>

                                <!-- Badges Overlay -->
                                <div class="position-absolute top-0 start-0 m-12 d-flex flex-wrap gap-1">
                                    <span class="badge shadow-sm rounded-pill text-xs fw-bold px-10 py-4"
                                          style="background: rgba(255,255,255,0.95); backdrop-filter: blur(4px); color: #0f172a; border: 1px solid rgba(0,0,0,0.08);">
                                        <?= esc($n['category_name'] ?? 'Umum') ?>
                                    </span>
                                    <?php if (! empty($n['is_featured'])): ?>
                                        <span class="badge shadow-sm rounded-pill text-xs fw-bold px-8 py-4"
                                              style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
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
                                <div class="d-flex align-items-center justify-content-between text-xs fw-semibold mb-10" style="color: #475569;">
                                    <span><iconify-icon icon="solar:calendar-outline" class="me-1 text-primary-600 align-middle"></iconify-icon><?= ! empty($n['published_at']) ? esc(date('d M Y', strtotime($n['published_at']))) : 'Draft' ?></span>
                                    <span><iconify-icon icon="solar:eye-outline" class="me-1 text-info-600 align-middle"></iconify-icon><strong style="color: #0f172a;"><?= number_format((int) ($n['view_count'] ?? 0)) ?></strong> views</span>
                                </div>

                                <!-- Title (Fixed 2 lines) -->
                                <a href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>" class="news-grid-title" title="<?= esc($n['title'] ?? '') ?>">
                                    <?= esc($n['title'] ?? '(Tanpa judul)') ?>
                                </a>

                                <!-- Excerpt (Fixed 3 lines) -->
                                <p class="news-grid-excerpt">
                                    <?= esc($n['summary'] ?: 'Tidak ada ringkasan teks. Klik lihat detail untuk meninjau naskah warta lengkap.') ?>
                                </p>

                                <!-- Tags Pills (Uniform slot) -->
                                <div class="news-grid-tags">
                                    <?php if (! empty($n['tags'])): ?>
                                        <?php foreach (array_slice(explode(',', $n['tags']), 0, 3) as $tag): ?>
                                            <span class="badge text-xs fw-semibold px-8 py-3 radius-4"
                                                  style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                                                #<?= esc(trim($tag)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Footer Author & Actions (Sticky Bottom) -->
                                <div class="news-grid-footer">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-28-px h-28-px rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold"
                                             style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                            <?= esc($initials) ?>
                                        </div>
                                        <span class="text-xs fw-bold text-neutral-800" style="color: #0f172a;"><?= esc($authorName) ?></span>
                                    </div>
                                    <div class="dropdown">
                                        <button type="button" class="btn-action-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Opsi">
                                            <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 py-6">
                                            <li>
                                                <a class="dropdown-item text-xs fw-semibold text-neutral-800 d-flex align-items-center gap-2 py-8 px-14" href="<?= route_to('dashboard.communication.news.detail', (string) $n['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-primary-600 text-base"></iconify-icon> Lihat Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-xs fw-semibold text-neutral-800 d-flex align-items-center gap-2 py-8 px-14" href="<?= route_to('dashboard.communication.news.edit', (string) $n['id']) ?>">
                                                    <iconify-icon icon="solar:pen-2-outline" class="text-info-600 text-base"></iconify-icon> Edit Berita
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-xs fw-semibold text-danger d-flex align-items-center gap-2 py-8 px-14 border-0 bg-transparent w-100 text-start"
                                                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                        data-action="<?= route_to('dashboard.communication.news.delete', (string) $n['id']) ?>"
                                                        data-method="POST"
                                                        data-title="Hapus Berita"
                                                        data-message="Apakah Anda yakin ingin menghapus berita &quot;<?= esc($n['title'] ?? '') ?>&quot;?"
                                                        data-btn-text="Ya, Hapus"
                                                        data-btn-class="btn-danger-600">
                                                    <iconify-icon icon="solar:trash-bin-trash-outline" class="text-danger text-base"></iconify-icon>
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
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-14 border-top" style="border-color: #e2e8f0 !important;">
                <span class="text-xs fw-semibold" style="color: #475569;">
                    Menampilkan halaman <strong style="color: #0f172a;"><?= $pager['page'] ?></strong> dari <strong style="color: #0f172a;"><?= $pager['totalPages'] ?></strong> (Total <strong style="color: #0f172a;"><?= number_format($pager['totalRows']) ?></strong> artikel berita)
                </span>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item <?= ! $pager['hasPrev'] ? 'disabled' : '' ?>">
                        <a class="page-link radius-6 px-12 py-6 text-xs fw-bold" style="color: #1e293b; border-color: #cbd5e1;" href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $pager['prevPage']])) ?>">
                            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                        </a>
                    </li>
                    <?php for ($p = 1; $p <= $pager['totalPages']; $p++): ?>
                        <?php if ($p === 1 || $p === $pager['totalPages'] || abs($p - $pager['page']) <= 2): ?>
                            <li class="page-item <?= $p === $pager['page'] ? 'active' : '' ?>">
                                <a class="page-link radius-6 px-12 py-6 text-xs fw-bold"
                                   style="<?= $p === $pager['page'] ? 'background:#0284c7; border-color:#0284c7; color:#ffffff;' : 'color:#1e293b; border-color:#cbd5e1;' ?>"
                                   href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php elseif (abs($p - $pager['page']) === 3): ?>
                            <li class="page-item disabled"><span class="page-link radius-6 px-8 py-6 text-xs text-neutral-500" style="border-color:#cbd5e1;">...</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= ! $pager['hasNext'] ? 'disabled' : '' ?>">
                        <a class="page-link radius-6 px-12 py-6 text-xs fw-bold" style="color: #1e293b; border-color: #cbd5e1;" href="<?= route_to('dashboard.communication.news') . '?' . http_build_query(array_merge($_GET, ['page' => $pager['nextPage']])) ?>">
                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>