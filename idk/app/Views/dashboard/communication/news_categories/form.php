<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori Berita') ?></h6>
        <p class="text-secondary-light mb-0">Form kategori berita.</p>
    </div>
    <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/komunikasi/kategori-berita/' . (int) ($catId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/kategori-berita/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $item['name'] ?? '')) ?>" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control radius-8" value="<?= esc(old('slug', $item['slug'] ?? '')) ?>" required maxlength="255" pattern="[a-z0-9-]+">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea name="description" class="form-control radius-8" rows="3"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
