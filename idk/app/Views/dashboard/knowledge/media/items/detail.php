<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $row = $mediaItem ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Item Media</h6>
        <p class="text-secondary-light mb-0">ID Item: <?= (int) ($mediaItemId ?? 0) ?></p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.media-items') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24">
    <h5 class="mb-16"><?= esc($row['title'] ?? '(Tanpa judul)') ?></h5>
    <p class="text-secondary-light mb-0"><?= esc($row['caption'] ?? '—') ?></p>
    <hr class="my-24">
    <div class="row gy-20">
        <div class="col-md-4"><p class="text-secondary-light mb-4">Tipe</p><h6 class="mb-0"><?= esc($row['type'] ?? '-') ?></h6></div>
        <div class="col-md-4"><p class="text-secondary-light mb-4">Status</p><h6 class="mb-0"><?= esc($row['workflow_status'] ?? '-') ?></h6></div>
        <div class="col-md-4"><p class="text-secondary-light mb-4">Urutan</p><h6 class="mb-0"><?= (int) ($row['sort_order'] ?? 0) ?></h6></div>
    </div>
</div></div>
<?= $this->endSection() ?>