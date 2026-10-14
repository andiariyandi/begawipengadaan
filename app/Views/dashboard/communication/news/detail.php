<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$item     = $item ?? [];
$coverSrc = ! empty($item['cover_path']) ? base_url('file/' . (int) $item['cover_file_id']) : null;
$author   = $item['author_name'] ?? 'Redaksi Begawi';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-bold text-neutral-900 mb-1">Detail Informasi Berita</h6>
        <div class="d-flex align-items-center gap-2 text-neutral-700 text-xs fw-medium">
            <span>ID: #<?= (int) ($newsId ?? $item['id'] ?? 0) ?></span>
            <span>·</span>
            <span>Status: <strong class="text-neutral-900"><?= esc(ucfirst($item['workflow_status'] ?? 'draft')) ?></strong></span>
            <span>·</span>
            <span>Visibilitas: <strong class="text-neutral-900"><?= esc(ucfirst($item['visibility'] ?? 'public')) ?></strong></span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if (! empty($item['slug'])): ?>
            <a href="<?= site_url('berita/' . esc($item['slug'])) ?>" target="_blank" class="btn btn-outline-info-600 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-semibold">
                <iconify-icon icon="solar:link-external-linear" class="text-lg"></iconify-icon> Buka di Web Publik
            </a>
        <?php endif; ?>
        <a href="<?= route_to('dashboard.communication.news.edit', (string) ($newsId ?? $item['id'] ?? '')) ?>" class="btn btn-primary-600 radius-8 px-18 py-10 d-flex align-items-center gap-2 text-sm fw-semibold">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon> Ubah Berita
        </a>
        <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 text-sm fw-medium text-neutral-700">
            Kembali
        </a>
    </div>
</div>

<div class="row gy-4">
    <!-- Main Content (8 cols) -->
    <div class="col-xl-8">
        <div class="card radius-12 border overflow-hidden mb-24">
            <!-- Cover Banner Area -->
            <div class="position-relative h-320-px bg-neutral-100 overflow-hidden d-flex align-items-center justify-content-center">
                <?php if ($coverSrc): ?>
                    <img src="<?= esc($coverSrc) ?>" alt="<?= esc($item['title'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <div class="text-center p-24">
                        <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-400 text-6xl mb-2"></iconify-icon>
                        <span class="d-block text-neutral-600 text-sm fw-medium">Belum ada gambar sampul diunggah</span>
                    </div>
                <?php endif; ?>

                <!-- Badges -->
                <div class="position-absolute top-0 start-0 m-16 d-flex flex-wrap gap-2">
                    <span class="badge bg-white text-dark shadow-sm rounded-pill text-xs fw-bold px-12 py-6">
                        <iconify-icon icon="solar:folder-with-files-bold" class="text-primary-600 me-1"></iconify-icon>
                        <?= esc($item['category_name'] ?? 'Umum') ?>
                    </span>
                    <?php if (! empty($item['is_featured'])): ?>
                        <span class="badge bg-warning text-dark shadow-sm rounded-pill text-xs fw-bold px-10 py-6">
                            <iconify-icon icon="solar:star-bold"></iconify-icon> Berita Utama (Headline)
                        </span>
                    <?php endif; ?>
                </div>

                <div class="position-absolute top-0 end-0 m-16">
                    <?= view('dashboard/components/status_badge', ['status' => $item['workflow_status'] ?? 'draft']) ?>
                </div>
            </div>

            <?php if (! empty($item['cover_caption'])): ?>
                <div class="px-24 py-10 bg-neutral-50 border-bottom text-neutral-700 text-xs fst-italic fw-medium">
                    <iconify-icon icon="solar:camera-outline" class="me-1 text-neutral-500"></iconify-icon>
                    Foto: <?= esc($item['cover_caption']) ?>
                </div>
            <?php endif; ?>

            <div class="p-24">
                <h4 class="mb-14 fw-bold text-neutral-900 lh-base"><?= esc($item['title'] ?? '(Tanpa judul)') ?></h4>

                <!-- Meta Chips -->
                <div class="d-flex flex-wrap align-items-center gap-16 text-neutral-700 text-xs pb-16 mb-20 border-bottom fw-medium">
                    <span class="d-flex align-items-center gap-1"><iconify-icon icon="solar:user-outline" class="text-primary-600 text-sm"></iconify-icon> <?= esc($author) ?></span>
                    <span class="d-flex align-items-center gap-1"><iconify-icon icon="solar:calendar-outline" class="text-info-600 text-sm"></iconify-icon> <?= ! empty($item['published_at']) ? esc(date('d M Y H:i', strtotime($item['published_at']))) : 'Belum dipublikasikan' ?></span>
                    <?php if (! empty($item['reading_time'])): ?>
                        <span class="d-flex align-items-center gap-1"><iconify-icon icon="solar:clock-circle-outline" class="text-warning-600 text-sm"></iconify-icon> <?= esc($item['reading_time']) ?></span>
                    <?php endif; ?>
                    <span class="d-flex align-items-center gap-1"><iconify-icon icon="solar:eye-outline" class="text-success-600 text-sm"></iconify-icon> <strong class="text-neutral-900"><?= number_format((int) ($item['view_count'] ?? 0)) ?></strong> Kali Dibaca</span>
                </div>

                <!-- Excerpt / Lead -->
                <?php if (! empty($item['summary'])): ?>
                    <div class="p-16 bg-primary-50 rounded-8 border border-primary-100 text-neutral-800 text-sm fw-medium mb-24 lh-relaxed">
                        <?= nl2br(esc($item['summary'])) ?>
                    </div>
                <?php endif; ?>

                <!-- Full Content -->
                <div class="text-neutral-800 text-base lh-lg mb-24">
                    <?= ! empty($item['content']) ? nl2br(esc($item['content'])) : '<em class="text-neutral-500">Konten naskah berita belum diisi.</em>' ?>
                </div>

                <!-- Tags / Keywords -->
                <?php if (! empty($item['tags'])): ?>
                    <div class="pt-16 border-top">
                        <span class="text-neutral-800 text-xs fw-bold mb-2 d-block">Tagar / Kata Kunci:</span>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (explode(',', $item['tags']) as $t): ?>
                                <span class="badge bg-neutral-100 text-neutral-700 border border-neutral-300 px-10 py-4 text-xs font-monospace fw-medium">
                                    #<?= esc(trim($t)) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Source Attribution -->
                <?php if (! empty($item['source_name'])): ?>
                    <div class="mt-20 p-14 bg-neutral-50 rounded-8 border text-xs text-neutral-700">
                        <strong class="text-neutral-900">Sumber Liputan:</strong> <?= esc($item['source_name']) ?>
                        <?php if (! empty($item['source_url'])): ?>
                            — <a href="<?= esc($item['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary-600 text-decoration-underline font-monospace fw-medium">
                                <?= esc($item['source_url']) ?> <iconify-icon icon="solar:link-external-linear" class="ms-1"></iconify-icon>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Meta (4 cols) -->
    <div class="col-xl-4">
        <div class="card radius-12 border p-20 mb-20">
            <h6 class="fw-bold text-neutral-900 mb-16 pb-10 border-bottom text-sm">Informasi Metadata</h6>

            <div class="mb-14">
                <span class="text-neutral-700 text-xs fw-semibold d-block mb-1">Slug URL</span>
                <code class="text-primary-700 bg-neutral-100 px-8 py-4 rounded-4 d-block text-xs font-monospace text-break border border-neutral-200"><?= esc($item['slug'] ?? '-') ?></code>
            </div>

            <div class="mb-14">
                <span class="text-neutral-700 text-xs fw-semibold d-block mb-1">Visibilitas Akses</span>
                <span class="fw-bold text-neutral-900 text-xs"><?= esc(ucfirst($item['visibility'] ?? '-')) ?></span>
            </div>

            <div class="mb-14">
                <span class="text-neutral-700 text-xs fw-semibold d-block mb-1">Status Sorotan</span>
                <?php if (! empty($item['is_featured'])): ?>
                    <span class="badge bg-warning-subtle text-warning-800 border border-warning-300 text-xs fw-bold px-8 py-4">
                        <iconify-icon icon="solar:star-bold" class="me-1"></iconify-icon> Berita Utama (Headline)
                    </span>
                <?php else: ?>
                    <span class="text-neutral-600 text-xs fw-medium">Standar (Bukan Headline)</span>
                <?php endif; ?>
            </div>

            <div class="mb-14">
                <span class="text-neutral-700 text-xs fw-semibold d-block mb-1">Dibuat Pada</span>
                <span class="text-neutral-900 text-xs fw-medium"><?= esc($item['created_at'] ?? '-') ?></span>
            </div>

            <div class="mb-14">
                <span class="text-neutral-700 text-xs fw-semibold d-block mb-1">Pembaruan Terakhir</span>
                <span class="text-neutral-900 text-xs fw-medium"><?= esc($item['updated_at'] ?? '-') ?></span>
            </div>

            <div class="pt-14 border-top">
                <button type="button" class="btn btn-outline-danger-600 w-100 radius-8 d-flex align-items-center justify-content-center gap-2 text-xs py-10"
                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                        data-action="<?= route_to('dashboard.communication.news.delete', (string) ($newsId ?? $item['id'] ?? '')) ?>"
                        data-method="POST"
                        data-title="Hapus Berita"
                        data-message="Apakah Anda yakin ingin menghapus berita ini secara permanen?"
                        data-btn-text="Hapus Berita"
                        data-btn-class="btn-danger-600">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon> Hapus Berita
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
