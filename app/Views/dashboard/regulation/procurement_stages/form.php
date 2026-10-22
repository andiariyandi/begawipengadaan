<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode = $formMode ?? 'create';
$isEdit   = $formMode === 'edit';
$stage    = $stage ?? [];
$stgId    = $stgId ?? null;
?>

<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger radius-12 p-16 mb-20">
        <ul class="mb-0 ps-16 text-sm">
            <?php foreach ($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tahapan PBJ') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Ubah data tahapan siklus Pengadaan Barang/Jasa.' : 'Tambahkan tahapan siklus pengadaan baru untuk penataan regulasi.' ?></p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
        <iconify-icon icon="solar:route-bold" class="text-primary-600 text-xl"></iconify-icon>
        <h6 class="text-base fw-bold mb-0">Formulir Tahapan PBJ</h6>
    </div>
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/regulasi/tahapan-pbj/' . (int) $stgId . '/ubah') : site_url('dashboard/regulasi/tahapan-pbj/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil</label>
                    <input type="number" min="0" class="form-control radius-8" name="sort_order" value="<?= esc(old('sort_order', $stage['sort_order'] ?? 0)) ?>">
                    <span class="text-xs text-secondary-light mt-1 d-block">Urutan siklus (1, 2, 3...)</span>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode / Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="key" value="<?= esc(old('key', $stage['key'] ?? '')) ?>" required maxlength="64" pattern="[A-Z0-9_]+" placeholder="PLANNING">
                    <span class="text-xs text-secondary-light mt-1 d-block">Huruf kapital & underscore.</span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Tahapan PBJ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="name" value="<?= esc(old('name', $stage['name'] ?? '')) ?>" placeholder="Contoh: Perencanaan Pengadaan" required maxlength="255">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi &amp; Ruang Lingkup Tahapan</label>
                    <textarea class="form-control radius-8" name="description" rows="3" placeholder="Uraian kegiatan pengadaan yang tercakup pada tahapan ini..."><?= esc(old('description', $stage['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($stage['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-sm text-primary-light cursor-pointer ms-2" for="is_active">
                            Tahapan Aktif
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12 pt-12 border-top">
                    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Tahapan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>