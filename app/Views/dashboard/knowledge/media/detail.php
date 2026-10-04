<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $media ?? [];
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$row['workflow_status'] ?? 'draft'] ?? 'Draft';
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
                <div class="h-320-px bg-primary-50 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:gallery-wide-outline" class="text-primary-600 text-6xl"></iconify-icon>
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