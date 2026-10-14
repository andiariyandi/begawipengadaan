<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Scoped News Detail Styling for High Contrast & Clean Layout */
.news-detail-container {
    color: #1e293b;
}
.news-detail-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.news-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
}
.news-meta-chip strong {
    color: #0f172a;
}
.news-content-article {
    color: #1e293b;
    font-size: 15px;
    line-height: 1.8;
}
.news-content-article p {
    margin-bottom: 1.25rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$item     = $item ?? [];
$coverSrc = ! empty($item['cover_path']) ? base_url('file/' . (int) $item['cover_file_id']) : null;
$author   = $item['author_name'] ?? 'Redaksi Begawi';
?>

<div class="news-detail-container">
    <!-- Top Bar Navigation -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1">Detail Informasi Berita</h6>
            <div class="d-flex align-items-center gap-2 text-neutral-600 text-xs fw-semibold">
                <span>ID: <strong style="color: #0f172a;">#<?= (int) ($newsId ?? $item['id'] ?? 0) ?></strong></span>
                <span>·</span>
                <span>Status: <strong style="color: #0f172a;"><?= esc(ucfirst($item['workflow_status'] ?? 'draft')) ?></strong></span>
                <span>·</span>
                <span>Visibilitas: <strong style="color: #0f172a;"><?= esc(ucfirst($item['visibility'] ?? 'public')) ?></strong></span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if (! empty($item['slug'])): ?>
                <a href="<?= site_url('berita/' . esc($item['slug'])) ?>" target="_blank" class="btn btn-outline-info-600 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm fw-bold shadow-sm" style="background: #ffffff;">
                    <iconify-icon icon="solar:link-external-linear" class="text-lg"></iconify-icon> Buka di Web Publik
                </a>
            <?php endif; ?>
            <a href="<?= route_to('dashboard.communication.news.edit', (string) ($newsId ?? $item['id'] ?? '')) ?>" class="btn btn-primary-600 radius-8 px-18 py-10 d-flex align-items-center gap-2 text-sm fw-bold shadow-sm">
                <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon> Ubah Berita
            </a>
            <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 text-sm fw-bold text-neutral-700 d-flex align-items-center gap-1" style="background: #ffffff; border-color: #cbd5e1;">
                <iconify-icon icon="solar:arrow-left-linear" class="text-base text-primary-600"></iconify-icon> Kembali
            </a>
        </div>
    </div>

    <div class="row gy-4">
        <!-- Main Content (8 cols) -->
        <div class="col-xl-8">
            <div class="news-detail-card overflow-hidden mb-24">
                <!-- Cover Banner Area -->
                <div class="position-relative bg-neutral-100 overflow-hidden d-flex align-items-center justify-content-center" style="height: 320px;">
                    <?php if ($coverSrc): ?>
                        <img src="<?= esc($coverSrc) ?>" alt="<?= esc($item['title'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div class="text-center p-24">
                            <iconify-icon icon="solar:gallery-wide-outline" class="text-neutral-400 text-6xl mb-2"></iconify-icon>
                            <span class="d-block text-neutral-600 text-sm fw-bold">Belum ada gambar sampul diunggah</span>
                        </div>
                    <?php endif; ?>

                    <!-- Badges Overlay -->
                    <div class="position-absolute top-0 start-0 m-16 d-flex flex-wrap gap-2">
                        <span class="badge shadow-sm rounded-pill text-xs fw-bold px-12 py-6"
                              style="background: rgba(255,255,255,0.95); backdrop-filter: blur(4px); color: #0f172a; border: 1px solid rgba(0,0,0,0.08);">
                            <iconify-icon icon="solar:folder-with-files-bold" class="text-primary-600 me-1"></iconify-icon>
                            <?= esc($item['category_name'] ?? 'Umum') ?>
                        </span>
                        <?php if (! empty($item['is_featured'])): ?>
                            <span class="badge shadow-sm rounded-pill text-xs fw-bold px-10 py-6"
                                  style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                <iconify-icon icon="solar:star-bold"></iconify-icon> Berita Utama (Headline)
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="position-absolute top-0 end-0 m-16">
                        <?= view('dashboard/components/status_badge', ['status' => $item['workflow_status'] ?? 'draft']) ?>
                    </div>
                </div>

                <?php if (! empty($item['cover_caption'])): ?>
                    <div class="px-24 py-10 bg-neutral-50 border-bottom text-xs fst-italic fw-medium d-flex align-items-center gap-2" style="border-color: #e2e8f0; color: #475569;">
                        <iconify-icon icon="solar:camera-outline" class="text-neutral-500 text-base"></iconify-icon>
                        <span>Foto: <?= esc($item['cover_caption']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="p-24">
                    <h3 class="mb-16 fw-bold text-neutral-900 lh-base" style="color: #0f172a; font-size: 22px;">
                        <?= esc($item['title'] ?? '(Tanpa judul)') ?>
                    </h3>

                    <!-- Meta Chips -->
                    <div class="d-flex flex-wrap align-items-center gap-16 pb-16 mb-20 border-bottom" style="border-color: #e2e8f0 !important;">
                        <span class="news-meta-chip">
                            <iconify-icon icon="solar:user-bold" class="text-primary-600 text-base"></iconify-icon>
                            <strong><?= esc($author) ?></strong>
                        </span>
                        <span class="news-meta-chip">
                            <iconify-icon icon="solar:calendar-bold" class="text-info-600 text-base"></iconify-icon>
                            <?= ! empty($item['published_at']) ? esc(date('d M Y H:i', strtotime($item['published_at']))) : 'Belum dipublikasikan' ?>
                        </span>
                        <?php if (! empty($item['reading_time'])): ?>
                            <span class="news-meta-chip">
                                <iconify-icon icon="solar:clock-circle-bold" class="text-warning-600 text-base" style="color: #d97706;"></iconify-icon>
                                <?= esc($item['reading_time']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="news-meta-chip">
                            <iconify-icon icon="solar:eye-bold" class="text-success-600 text-base"></iconify-icon>
                            <strong><?= number_format((int) ($item['view_count'] ?? 0)) ?></strong> Kali Dibaca
                        </span>
                    </div>

                    <!-- Excerpt / Lead -->
                    <?php if (! empty($item['summary'])): ?>
                        <div class="p-16 rounded-10 mb-24" style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0c4a6e; font-size: 14.5px; line-height: 1.6; font-weight: 500;">
                            <?= nl2br(esc($item['summary'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Full Content -->
                    <div class="news-content-article mb-24">
                        <?= ! empty($item['content']) ? nl2br(esc($item['content'])) : '<em class="text-neutral-500">Konten naskah berita belum diisi.</em>' ?>
                    </div>

                    <!-- Tags / Keywords -->
                    <?php if (! empty($item['tags'])): ?>
                        <div class="pt-16 border-top" style="border-color: #e2e8f0 !important;">
                            <span class="text-xs fw-bold mb-2 d-block" style="color: #0f172a;">Tagar / Kata Kunci:</span>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (explode(',', $item['tags']) as $t): ?>
                                    <span class="badge px-10 py-5 text-xs font-monospace fw-bold"
                                          style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                                        #<?= esc(trim($t)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Source Attribution -->
                    <?php if (! empty($item['source_name'])): ?>
                        <div class="mt-20 p-14 rounded-8 border text-xs" style="background: #f8fafc; border-color: #e2e8f0; color: #334155;">
                            <strong style="color: #0f172a;">Sumber Liputan:</strong> <?= esc($item['source_name']) ?>
                            <?php if (! empty($item['source_url'])): ?>
                                — <a href="<?= esc($item['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-underline font-monospace fw-bold" style="color: #0284c7;">
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
            <div class="news-detail-card p-20 mb-20">
                <h6 class="fw-bold text-neutral-900 mb-16 pb-10 border-bottom text-sm d-flex align-items-center gap-2" style="border-color: #e2e8f0 !important;">
                    <iconify-icon icon="solar:info-square-bold" class="text-primary-600 text-lg"></iconify-icon>
                    <span>Informasi Metadata</span>
                </h6>

                <div class="mb-14">
                    <span class="text-xs fw-bold d-block mb-1" style="color: #475569;">Slug URL</span>
                    <code class="px-8 py-4 rounded-4 d-block text-xs font-monospace text-break border fw-bold"
                          style="background: #f8fafc; color: #0284c7; border-color: #e2e8f0;"><?= esc($item['slug'] ?? '-') ?></code>
                </div>

                <div class="mb-14">
                    <span class="text-xs fw-bold d-block mb-1" style="color: #475569;">Visibilitas Akses</span>
                    <span class="fw-bold text-xs" style="color: #0f172a;"><?= esc(ucfirst($item['visibility'] ?? '-')) ?></span>
                </div>

                <div class="mb-14">
                    <span class="text-xs fw-bold d-block mb-1" style="color: #475569;">Status Sorotan</span>
                    <?php if (! empty($item['is_featured'])): ?>
                        <span class="badge text-xs fw-bold px-10 py-5" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                            <iconify-icon icon="solar:star-bold" class="me-1"></iconify-icon> Berita Utama (1 dari 5 Slot)
                        </span>
                    <?php else: ?>
                        <span class="text-xs fw-semibold" style="color: #64748b;">Standar (Bukan Headline)</span>
                    <?php endif; ?>
                </div>

                <div class="mb-14">
                    <span class="text-xs fw-bold d-block mb-1" style="color: #475569;">Dibuat Pada</span>
                    <span class="text-xs fw-semibold" style="color: #0f172a;"><?= esc($item['created_at'] ?? '-') ?></span>
                </div>

                <div class="mb-14">
                    <span class="text-xs fw-bold d-block mb-1" style="color: #475569;">Pembaruan Terakhir</span>
                    <span class="text-xs fw-semibold" style="color: #0f172a;"><?= esc($item['updated_at'] ?? '-') ?></span>
                </div>

                <div class="pt-14 border-top" style="border-color: #e2e8f0 !important;">
                    <button type="button" class="btn btn-outline-danger w-100 radius-8 d-flex align-items-center justify-content-center gap-2 text-xs py-10 fw-bold"
                            style="border-color: #fca5a5; color: #dc2626; background: #fff5f5;"
                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                            data-action="<?= route_to('dashboard.communication.news.delete', (string) ($newsId ?? $item['id'] ?? '')) ?>"
                            data-method="POST"
                            data-title="Hapus Berita"
                            data-message="Apakah Anda yakin ingin menghapus berita ini secara permanen?"
                            data-btn-text="Hapus Berita"
                            data-btn-class="btn-danger-600">
                        <iconify-icon icon="solar:trash-bin-trash-bold" class="text-base"></iconify-icon> Hapus Berita
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
