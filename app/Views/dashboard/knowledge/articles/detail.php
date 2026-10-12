<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$article = $article ?? [];
$statusKey = $article['workflow_status'] ?? 'draft';
$authorName = $article['author_name'] ?? 'Penyunting PBJ';
$initials = strtoupper(substr($authorName, 0, 2));
$cats = array_column($article['categories'] ?? [], 'name');
$tags = array_column($article['tags'] ?? [], 'name');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Artikel</h6>
        <p class="text-secondary-light mb-0">ID Naskah: #<?= (int) ($articleId ?? ($article['id'] ?? 0)) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.articles.edit', (string) ($articleId ?? ($article['id'] ?? ''))) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah Artikel
        </a>
        <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<!-- Header Info Card -->
<div class="card radius-12 border mb-24 overflow-hidden">
    <?php if (! empty($article['cover_file_id'])): ?>
        <div class="position-relative" style="max-height: 280px; overflow: hidden; background: #082f5c;">
            <img src="<?= base_url('file/' . (int) $article['cover_file_id']) ?>" alt="<?= esc($article['title']) ?>" class="w-100 object-fit-cover" style="max-height: 280px;">
            <?php if ((int) ($article['featured'] ?? 0) === 1): ?>
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge bg-warning text-dark radius-6 px-12 py-6 text-xs fw-bold shadow-sm">
                        <i class="fas fa-star me-1"></i> Konten Unggulan (Pilihan Editor)
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div class="d-flex align-items-center gap-3">
                <div class="w-56-px h-56-px bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-xl fw-bold flex-shrink-0">
                    <?= esc($initials) ?>
                </div>
                <div>
                    <h5 class="fw-semibold mb-1 text-primary-light"><?= esc($article['title'] ?? '-') ?></h5>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-secondary-light text-sm">
                        <span><iconify-icon icon="solar:user-outline" class="me-1"></iconify-icon><?= esc($authorName) ?></span>
                        <span>•</span>
                        <span><iconify-icon icon="solar:clock-circle-outline" class="me-1"></iconify-icon><?= (int) ($article['reading_time'] ?? 5) ?> Menit Baca</span>
                        <span>•</span>
                        <span><iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>Diperbarui: <?= esc(date('d M Y, H:i', strtotime($article['updated_at'] ?? 'now'))) ?></span>
                        <span>•</span>
                        <span><iconify-icon icon="solar:eye-outline" class="me-1"></iconify-icon>Visibilitas: <?= ucfirst(esc($article['visibility'] ?? 'public')) ?></span>
                    </div>
                </div>
            </div>
            <div>
                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-16 border-top">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-secondary-light text-sm me-2">Kategori:</span>
                <?php if (!empty($cats)): ?>
                    <?php foreach ($cats as $cat): ?>
                        <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-12 py-6"><?= esc($cat) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-secondary-light text-sm">Umum</span>
                <?php endif; ?>
            </div>
            <?php if (! empty($article['source_url'])): ?>
                <div>
                    <a href="<?= esc($article['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary radius-6 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:link-bold"></iconify-icon> Buka Sumber Referensi
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tabs Content -->
<div class="card radius-12 border">
    <div class="card-header bg-base border-bottom p-0">
        <ul class="nav border-gradient-tab nav-pills px-24 pt-12 gap-4" id="articleTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="content-tab" data-bs-toggle="pill" data-bs-target="#content-pane" type="button" role="tab">
                    <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                    Naskah & Ringkasan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-16 py-12 fw-medium text-sm d-flex align-items-center gap-2" id="meta-tab" data-bs-toggle="pill" data-bs-target="#meta-pane" type="button" role="tab">
                    <iconify-icon icon="solar:tag-outline" class="text-lg"></iconify-icon>
                    Metadata & Audit Log
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-24">
        <div class="tab-content" id="articleTabContent">
            <!-- Tab 1: Konten -->
            <div class="tab-pane fade show active" id="content-pane" role="tabpanel">
                <?php if (!empty($article['summary'])): ?>
                    <div class="p-16 radius-8 bg-neutral-50 border mb-24">
                        <span class="fw-semibold text-neutral-800 d-block mb-1 text-sm">Ringkasan Eksekutif:</span>
                        <p class="text-secondary-light mb-0 text-sm"><?= nl2br(esc($article['summary'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="article-body-content text-neutral-800">
                    <h6 class="text-md fw-semibold mb-16 text-primary-light">Naskah Lengkap</h6>
                    <div class="p-20 radius-8 border bg-base">
                        <?= nl2br(esc($article['content'] ?? '(Belum ada naskah konten)')) ?>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Meta -->
            <div class="tab-pane fade" id="meta-pane" role="tabpanel">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border">
                            <span class="text-secondary-light text-xs d-block mb-1">Tag PBJ Terkait</span>
                            <div class="d-flex flex-wrap gap-1">
                                <?php if (!empty($tags)): ?>
                                    <?php foreach ($tags as $tag): ?>
                                        <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-4"><?= esc($tag) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-secondary-light text-sm">Tidak ada tag</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border">
                            <span class="text-secondary-light text-xs d-block mb-1">Slug URL</span>
                            <span class="fw-medium text-sm text-neutral-900"><?= esc($article['slug'] ?? '-') ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-16 radius-8 border">
                            <span class="text-secondary-light text-xs d-block mb-1">Dibuat Pada</span>
                            <span class="fw-medium text-sm text-neutral-900"><?= esc($article['created_at'] ?? '-') ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-16 radius-8 border">
                            <span class="text-secondary-light text-xs d-block mb-1">Terakhir Diperbarui</span>
                            <span class="fw-medium text-sm text-neutral-900"><?= esc($article['updated_at'] ?? '-') ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-16 radius-8 border">
                            <span class="text-secondary-light text-xs d-block mb-1">Waktu Publikasi</span>
                            <span class="fw-medium text-sm text-neutral-900"><?= esc($article['published_at'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>