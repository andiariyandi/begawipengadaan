<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $media ?? [];
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$row['workflow_status'] ?? 'draft'] ?? 'Draft';

// Determine preview source: thumbnail → file → external_url
$imgSrc = null;
if (! empty($row['thumb_path'])) {
    $imgSrc = base_url('file/' . (int) $row['thumbnail_file_id']);
} elseif (! empty($row['file_path'])) {
    $imgSrc = base_url('file/' . (int) $row['file_id']);
} elseif (! empty($row['external_url'])) {
    $imgSrc = $row['external_url'];
}
$isImage = ! empty($row['file_mime_type']) && str_starts_with((string) $row['file_mime_type'], 'image/');
$isVideo = ($row['type'] ?? '') === 'video_file' && ! $isImage;
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Media</h6>
        <p class="text-secondary-light mb-0">ID Media: <?= (int) ($mediaId ?? 0) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.knowledge.media-gallery.edit', (string) ($mediaId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
        <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="h-320-px bg-neutral-100 d-flex align-items-center justify-content-center overflow-hidden">
                    <?php if ($imgSrc && $isImage): ?>
                        <img src="<?= esc($imgSrc) ?>" alt="<?= esc($row['alt_text'] ?? $row['title'] ?? '') ?>"
                             style="width:100%;height:100%;object-fit:contain;">
                    <?php elseif ($imgSrc && $isVideo): ?>
                        <video src="<?= esc($imgSrc) ?>" controls style="max-width:100%;max-height:100%;"></video>
                    <?php elseif ($imgSrc): ?>
                        <a href="<?= esc($imgSrc) ?>" target="_blank" rel="noopener" class="text-primary-600">
                            <iconify-icon icon="solar:link-outline" class="text-6xl"></iconify-icon>
                        </a>
                    <?php else: ?>
                        <iconify-icon icon="solar:gallery-wide-outline" class="text-primary-600 text-6xl"></iconify-icon>
                    <?php endif; ?>
                </div>
                <div class="p-24">
                    <h5 class="mb-16"><?= esc($row['title'] ?? '(Tanpa judul)') ?></h5>
                    <p class="text-secondary-light mb-0"><?= esc($row['caption'] ?? '—') ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-body p-24">
            <p class="text-secondary-light mb-4">Tipe</p>
            <h6 class="mb-20"><?= esc($row['type'] ?? '-') ?></h6>
            <p class="text-secondary-light mb-4">Status</p>
            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span>
            <p class="text-secondary-light mb-4 mt-20">Visibilitas</p>
            <h6 class="mb-20"><?= esc($row['visibility'] ?? '-') ?></h6>
            <p class="text-secondary-light mb-4">Diperbarui</p>
            <h6 class="mb-20"><?= esc($row['updated_at'] ?? '-') ?></h6>
            <?php if (! empty($row['external_url'])): ?>
                <p class="text-secondary-light mb-4">External URL</p>
                <p class="mb-0"><a href="<?= esc($row['external_url']) ?>" target="_blank" rel="noopener"><?= esc($row['external_url']) ?></a></p>
            <?php endif; ?>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>