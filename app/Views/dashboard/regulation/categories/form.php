<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $formMode = $formMode ?? 'create'; $isEdit = $formMode === 'edit'; $category = $category ?? []; $catId = $catId ?? null; ?>

<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger mb-16"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori') ?></h6>
        <p class="text-secondary-light mb-0">Form <?= $isEdit ? 'Ubah' : 'Tambah' ?> Kategori Regulasi.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/regulasi/kategori/' . (int) $catId . '/ubah') : site_url('dashboard/regulasi/kategori/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="name" value="<?= esc(old('name') ?? $category['name'] ?? '') ?>" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="slug" value="<?= esc(old('slug') ?? $category['slug'] ?? '') ?>" required maxlength="255" pattern="[a-z0-9-]+" placeholder="huruf-kecil-dan-strip">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea class="form-control radius-8" name="description" rows="3"><?= esc(old('description') ?? $category['description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/regulasi/kategori') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kategori</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>