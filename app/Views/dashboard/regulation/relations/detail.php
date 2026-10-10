<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $relation = $relation ?? []; $relationTypes = $relationTypes ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Relasi</h6>
        <p class="text-secondary-light mb-0">ID: <?= esc($relation['id'] ?? '-') ?></p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card mb-24">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-5">
                <p class="text-secondary-light mb-4">Regulasi Sumber</p>
                <h6 class="mb-0"><?= esc($relation['source_title'] ?? '-') ?></h6>
                <small class="text-secondary-light"><?= esc($relation['source_number'] ?? '-') ?></small>
            </div>
            <div class="col-md-2 text-center d-flex align-items-end justify-content-center">
                <span class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:arrow-right-outline" class="text-2xl"></iconify-icon>
                </span>
            </div>
            <div class="col-md-5">
                <p class="text-secondary-light mb-4">Regulasi Target</p>
                <h6 class="mb-0"><?= esc($relation['target_title'] ?? '-') ?></h6>
                <small class="text-secondary-light"><?= esc($relation['target_number'] ?? '-') ?></small>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Tipe Relasi</p>
                <span class="badge bg-primary-50 text-primary-600 px-12 py-8 radius-8"><?= esc($relation['relation_type'] ?? '-') ?></span>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Pembuat</p>
                <h6 class="mb-0"><?= esc($relation['creator_username'] ?? '-') ?></h6>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-4">Catatan</p>
                <p class="mb-0"><?= esc($relation['notes'] ?? '-') ?: '-' ?></p>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-outline-danger-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2"
    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
    data-action="<?= site_url('dashboard/regulasi/relasi/' . (int) $relation['id'] . '/hapus') ?>"
    data-method="POST"
    data-title="Hapus Relasi Regulasi"
    data-message="Apakah Anda yakin ingin menghapus relasi regulasi ini?"
    data-btn-text="Hapus Relasi"
    data-btn-class="btn-danger-600"
    data-icon="fluent:delete-24-regular"
    data-icon-color="danger">
    <iconify-icon icon="solar:trash-bin-trash-linear" class="text-xl"></iconify-icon>
    Hapus Relasi
</button>
<?= $this->endSection() ?>