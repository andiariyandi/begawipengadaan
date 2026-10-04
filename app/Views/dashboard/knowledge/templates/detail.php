<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $template ?? [];
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$row['workflow_status'] ?? 'draft'] ?? 'Draft';
$fileMeta = $fileMeta ?? null;
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Template Dokumen</h6>
        <p class="text-secondary-light mb-0">ID Template: <?= (int) ($templateId ?? 0) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.knowledge.document-templates.edit', (string) ($templateId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
        <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="card"><div class="card-body p-24">
    <div class="row gy-20">
        <div class="col-md-8">
            <p class="text-secondary-light mb-4">Nama Template</p>
            <h5 class="mb-0"><?= esc($row['title'] ?? '-') ?></h5>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Status</p>
            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Diperbarui</p>
            <h6 class="mb-0"><?= esc($row['updated_at'] ?? '-') ?></h6>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Dipublikasikan</p>
            <h6 class="mb-0"><?= esc($row['published_at'] ?? '-') ?></h6>
        </div>
        <div class="col-12">
            <p class="text-secondary-light mb-4">Deskripsi</p>
            <p class="mb-0"><?= nl2br(esc($row['summary'] ?? '-')) ?></p>
        </div>
    </div>
    <hr class="my-24">
    <p class="text-secondary-light mb-4">File Lampiran</p>
    <?php if ($fileMeta !== null): ?>
        <p class="mb-0">
            <strong><?= esc($fileMeta['original_name']) ?></strong>
            <small class="text-secondary-light">(<?= esc($fileMeta['extension']) ?> · <?= esc($fileMeta['size_bytes']) ?> bytes)</small>
        </p>
    <?php else: ?>
        <p class="mb-0 text-secondary-light">Belum ada file terlampir.</p>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>