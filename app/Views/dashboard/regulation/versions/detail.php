<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $version = $version ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Versi</h6>
        <p class="text-secondary-light mb-0">ID Versi: <?= esc($version['id'] ?? '-') ?></p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/versi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Regulasi</p>
                <h6 class="mb-0"><?= esc($version['regulation_title'] ?? '-') ?></h6>
                <small class="text-secondary-light"><?= esc($version['document_number'] ?? '-') ?></small>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Versi</p>
                <h6 class="mb-0">v<?= esc($version['version_number']) ?></h6>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Status</p>
                <?= view('dashboard/components/status_badge', ['status' => $version['is_current'] ? 'current' : 'inactive']) ?>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">File</p>
                <h6 class="mb-0"><?= esc($version['file_original_name'] ?? '-') ?: '-' ?></h6>
                <small class="text-secondary-light"><?= esc($version['mime_type'] ?? '-') ?> <?= isset($version['file_size']) ? '· ' . esc(round($version['file_size'] / 1024)) . ' KB' : '' ?></small>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Diupload oleh</p>
                <h6 class="mb-0"><?= esc($version['uploader_name'] ?? '-') ?></h6>
                <small class="text-secondary-light"><?= esc($version['created_at'] ?? '-') ?></small>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-4">Changelog</p>
                <p class="mb-0"><?= esc($version['changelog'] ?? '-') ?: '-' ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>