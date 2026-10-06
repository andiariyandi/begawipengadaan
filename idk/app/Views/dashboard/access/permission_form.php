<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$permission = $permission ?? [];
$action = $isEdit ? site_url('dashboard/akses/permission/' . (int) ($permissionId ?? 0) . '/ubah') : site_url('dashboard/akses/permission/tambah');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Permission') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Ubah permission' : 'Tambah permission baru' ?>.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/permission') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Key <span class="text-danger">*</span></label>
                    <input type="text" name="key" class="form-control radius-8"
                           value="<?= esc(old('key', $permission['key'] ?? '')) ?>" required maxlength="128" pattern="[a-z0-9._]+" placeholder="contoh: learning.course.manage">
                    <small class="text-secondary-light">Huruf kecil, angka, dot, underscore. Tidak bisa diubah setelah disimpan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Domain</label>
                    <input type="text" name="domain" class="form-control radius-8"
                           value="<?= esc(old('domain', $permission['domain'] ?? '')) ?>" maxlength="64" placeholder="contoh: learning">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Permission <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8"
                           value="<?= esc(old('name', $permission['name'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Kelola Course">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea name="description" class="form-control radius-8" rows="4" placeholder="Jelaskan kegunaan permission"><?= esc(old('description', $permission['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/akses/permission') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Permission' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
