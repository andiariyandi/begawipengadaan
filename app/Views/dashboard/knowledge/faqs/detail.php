<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$faq = $faq ?? [];
$statusLabel = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
][$faq['workflow_status'] ?? 'draft'] ?? 'Draft';
$cats = array_column($faq['categories'] ?? [], 'name');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail FAQ</h6>
        <p class="text-secondary-light mb-0">ID FAQ: <?= (int) ($faqId ?? 0) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.knowledge.faqs.edit', (string) ($faqId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah FAQ</a>
        <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="card"><div class="card-body p-24">
    <p class="text-secondary-light mb-4">Pertanyaan</p>
    <h5 class="mb-20"><?= esc($faq['title'] ?? '-') ?></h5>
    <div class="row gy-20">
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Kategori</p>
            <h6 class="mb-0"><?= esc($cats ? implode(', ', $cats) : '—') ?></h6>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Status</p>
            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span>
        </div>
        <div class="col-md-4">
            <p class="text-secondary-light mb-4">Diperbarui</p>
            <h6 class="mb-0"><?= esc($faq['updated_at'] ?? '-') ?></h6>
        </div>
    </div>
    <hr class="my-24">
    <p class="text-secondary-light mb-8">Jawaban</p>
    <div class="border rounded p-16 bg-light-subtle"><?= nl2br(esc($faq['content'] ?? '(Belum ada jawaban)')) ?></div>
</div></div>
<?= $this->endSection() ?>