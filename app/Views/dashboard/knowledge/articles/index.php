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
        <h6 class="fw-semibold mb-1">Artikel & Praktik Baik</h6>
        <p class="text-secondary-light mb-0">Kelola kurasi pengetahuan, artikel analitis, dan praktik baik pengadaan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.articles.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah Artikel
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/knowledge/_nav_knowledge', ['section' => 'articles', 'activeSub' => 'articles']) ?>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Artikel</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:document-text-outline"></iconify-icon>
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
        <form method="get" action="<?= route_to('dashboard.knowledge.articles') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Kata Kunci</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul atau ringkasan...">
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
                        <option value="revision_required" <?= $status === 'revision_required' ? 'selected' : '' ?>>Perlu Revisi</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
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
                    <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
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
        'icon'        => 'solar:document-text-outline',
        'title'       => 'Belum Ada Artikel',
        'message'     => 'Belum ada konten artikel atau praktik baik yang sesuai dengan kriteria filter saat ini.',
        'actionUrl'   => route_to('dashboard.knowledge.articles.create'),
        'actionText'  => 'Tambah Artikel Baru',
    ]) ?>
<?php else: ?>
    <div class="row gy-4 mb-24">
        <?php foreach ($items as $article): ?>
            <?php
                $catRows = db_connect()->table('knowledge_article_categories kac')
                    ->select('kc.name')
                    ->join('knowledge_categories kc', 'kc.id = kac.category_id')
                    ->where('kac.article_id', (int) $article['id'])
                    ->get()->getResultArray();
                $catNames = array_map(static fn ($r) => $r['name'], $catRows);
                $statusKey = $article['workflow_status'] ?? 'draft';
                $authorName = $article['author_name'] ?? 'Penyunting PBJ';
                $initials = strtoupper(substr($authorName, 0, 2));
            ?>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100 p-0 radius-12 border overflow-hidden bg-base d-flex flex-column shadow-sm">
                    <?php if (! empty($article['cover_file_id'])): ?>
                        <div class="position-relative" style="height: 150px; overflow: hidden; background: #082f5c;">
                            <img src="<?= base_url('file/' . (int) $article['cover_file_id']) ?>" alt="<?= esc($article['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                            <?php if ((int) ($article['featured'] ?? 0) === 1): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-warning-50 text-warning-700 border border-warning-200 radius-4 px-8 py-3 text-2xs fw-bold">
                                        <i class="fas fa-star me-1"></i> Unggulan
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-20 d-flex flex-column flex-grow-1">
                        <!-- Top Meta & Status -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-16">
                            <div class="d-flex align-items-center gap-1">
                                <span class="px-12 py-4 bg-neutral-100 rounded-pill text-neutral-600 text-xs fw-medium">
                                    <?= esc(!empty($catNames) ? $catNames[0] : 'Umum') ?>
                                </span>
                                <?php if (! empty($article['reading_time'])): ?>
                                    <span class="px-8 py-4 bg-primary-50 rounded-pill text-primary-600 text-2xs fw-semibold">
                                        <i class="far fa-clock me-1"></i><?= (int) $article['reading_time'] ?> mnt
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                            </div>
                        </div>

                        <!-- Title -->
                        <h6 class="mb-12">
                            <a href="<?= route_to('dashboard.knowledge.articles.detail', (string) $article['id']) ?>" class="text-line-2 text-hover-primary-600 text-lg fw-semibold transition-2">
                                <?= esc($article['title']) ?>
                            </a>
                        </h6>

                        <!-- Summary / Excerpt -->
                        <p class="text-line-3 text-secondary-light text-sm mb-20 flex-grow-1">
                            <?= esc($article['summary'] ?: 'Tidak ada ringkasan teks untuk artikel ini. Klik untuk membaca naskah lengkap.') ?>
                        </p>

                        <!-- Footer Info & Actions -->
                        <div class="pt-16 border-top d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xs fw-bold">
                                    <?= esc($initials) ?>
                                </div>
                                <div>
                                    <span class="text-xs fw-medium text-neutral-800 d-block"><?= esc($authorName) ?></span>
                                    <span class="text-2xs text-secondary-light"><?= esc(date('d M Y', strtotime($article['updated_at'] ?? 'now'))) ?></span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown">
                                    <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8">
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.knowledge.articles.detail', (string) $article['id']) ?>">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-sm d-flex align-items-center gap-2" href="<?= route_to('dashboard.knowledge.articles.edit', (string) $article['id']) ?>">
                                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Edit Artikel
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-sm text-danger-main d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmationActionModal"
                                                data-action="<?= route_to('dashboard.knowledge.articles.delete', (string) $article['id']) ?>"
                                                data-method="POST"
                                                data-title="Arsipkan Artikel"
                                                data-message="Apakah Anda yakin ingin mengarsipkan artikel &quot;<?= esc($article['title']) ?>&quot;?"
                                                data-btn-text="Ya, Arsipkan"
                                                data-btn-class="btn-danger-600"
                                                data-icon="solar:archive-bold"
                                                data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                            <span>Arsipkan</span>
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