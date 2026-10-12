<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $tag ?? [];
$action = $isEdit ? route_to('dashboard.knowledge.tags.edit', (string) ($tagId ?? '')) : route_to('dashboard.knowledge.tags.create');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tagar Konten') ?></h6>
        <p class="text-secondary-light mb-0">Kelola kata kunci tagar untuk pengelompokan konten Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.tags') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Kembali ke Tagar</span>
    </a>
</div>

<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
        <iconify-icon icon="solar:hashtag-bold" class="text-primary-600 text-xl"></iconify-icon>
        <h6 class="text-base fw-bold mb-0"><?= $isEdit ? 'Ubah Tagar' : 'Tambah Tagar Baru' ?></h6>
    </div>
    <div class="card-body p-24">
        <form method="post" action="<?= $action ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Tagar (Hashtag) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-100 text-primary-600 fw-bold">#</span>
                        <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: E-Katalog, RegulasiPBJ, PengadaanBarang">
                    </div>
                    <span class="text-xs text-secondary-light mt-1 d-block">Slug URL akan dibuat secara otomatis dari nama tagar.</span>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12 pt-20 border-top">
                    <a href="<?= route_to('dashboard.knowledge.tags') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Tagar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>