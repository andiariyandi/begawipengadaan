<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$article = $article ?? [];
$statusLabel = [
    'draft' => 'Draft', 'submitted' => 'Diajukan', 'in_review' => 'Ditinjau',
    'approved' => 'Disetujui', 'published' => 'Dipublikasikan',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak', 'archived' => 'Diarsipkan',
][$article['workflow_status'] ?? 'draft'] ?? 'Draft';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Artikel</h6>
        <p class="text-secondary-light mb-0">ID Artikel: <?= (int) ($articleId ?? 0) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.knowledge.articles.edit', (string) ($articleId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Artikel</a>
        <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-8">
                <p class="text-secondary-light mb-4">Judul</p>
                <h5 class="mb-0"><?= esc($article['title'] ?? '-') ?></h5>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Status</p>
                <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabel) ?></span>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Kategori</p>
                <?php $cats = array_column($article['categories'] ?? [], 'name'); ?>
                <h6 class="mb-0"><?= esc($cats ? implode(', ', $cats) : '—') ?></h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Tag</p>
                <?php $tagNames = array_column($article['tags'] ?? [], 'name'); ?>
                <h6 class="mb-0"><?= esc($tagNames ? implode(', ', $tagNames) : '—') ?></h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Penulis</p>
                <h6 class="mb-0"><?= esc($article['author_name'] ?? '-') ?></h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Visibilitas</p>
                <h6 class="mb-0"><?= esc($article['visibility'] ?? '-') ?></h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Diperbarui</p>
                <h6 class="mb-0"><?= esc($article['updated_at'] ?? '-') ?></h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Dipublikasikan</p>
                <h6 class="mb-0"><?= esc($article['published_at'] ?? '-') ?></h6>
            </div>
            <div class="col-12">
                <hr class="my-12">
                <p class="text-secondary-light mb-8">Ringkasan</p>
                <p class="mb-0"><?= nl2br(esc($article['summary'] ?? '-')) ?></p>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-8">Isi Artikel</p>
                <div class="border rounded p-16 bg-light-subtle"><?= nl2br(esc($article['content'] ?? '(Belum ada konten)')) ?></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>