<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $caseStudy ?? [];
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$row['workflow_status'] ?? 'draft'] ?? 'Draft';
$cats = array_column($row['categories'] ?? [], 'name');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Studi Kasus</h6>
        <p class="text-secondary-light mb-0">ID Studi Kasus: <?= (int) ($caseStudyId ?? 0) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.knowledge.case-studies.edit', (string) ($caseStudyId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
        <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="card"><div class="card-body p-24">
    <h5 class="mb-20"><?= esc($row['title'] ?? '-') ?></h5>
    <div class="row gy-20">
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Kategori</p>
            <h6 class="mb-0"><?= esc($cats ? implode(', ', $cats) : '—') ?></h6>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Penulis</p>
            <h6 class="mb-0"><?= esc($row['author_name'] ?? '-') ?></h6>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Status</p>
            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span>
        </div>
    </div>
    <hr class="my-24">
    <p class="text-secondary-light mb-8">Latar Belakang / Ringkasan</p>
    <p><?= nl2br(esc($row['summary'] ?? '-')) ?></p>
    <p class="text-secondary-light mb-8 mt-20">Pembelajaran Utama</p>
    <div class="border rounded p-16 bg-light-subtle"><?= nl2br(esc($row['content'] ?? '-')) ?></div>
</div></div>
<?= $this->endSection() ?>