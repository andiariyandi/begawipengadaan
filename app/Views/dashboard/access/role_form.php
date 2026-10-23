<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$role  = $role ?? [];
$allPermissions = $allPermissions ?? $permissions ?? [];
$assigned = $assigned ?? $assignedKeys ?? [];
$action = $isEdit ? site_url('dashboard/akses/role/' . (int) ($roleId ?? 0) . '/ubah') : site_url('dashboard/akses/role/tambah');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Role') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Ubah role' : 'Tambah role baru' ?>.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Key <span class="text-danger">*</span></label>
                    <input type="text" name="key" class="form-control radius-8" value="<?= esc(old('key', $role['key'] ?? '')) ?>" required maxlength="64" pattern="[a-z0-9_]+" placeholder="contoh: admin_konten" <?= $isEdit ? 'readonly' : '' ?>>
                    <small class="text-secondary-light">Huruf kecil, angka, dan underscore saja. Tidak bisa diubah setelah disimpan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $role['name'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Admin Konten">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea name="description" class="form-control radius-8" rows="3" placeholder="Deskripsi role"><?= esc(old('description', $role['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <h6 class="text-lg mb-16">Permission</h6>
                    <div class="row gy-12">
                        <?php if (empty($allPermissions)): ?>
                            <p class="text-secondary-light">Belum ada permission. <a href="<?= site_url('dashboard/akses/permission/tambah') ?>">Tambah permission dulu</a>.</p>
                        <?php else: foreach ($allPermissions as $p): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permission_ids[]" value="<?= (int) ($p['id'] ?? 0) ?>" id="perm-<?= (int) ($p['id'] ?? 0) ?>" <?= in_array((int) ($p['id'] ?? 0), array_map('intval', $assigned), true) || in_array($p['key'] ?? '', (array) $assigned, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm-<?= (int) ($p['id'] ?? 0) ?>">
                                        <code><?= esc($p['key']) ?></code>
                                        <small class="text-secondary-light d-block"><?= esc($p['name'] ?? $p['domain'] ?? '') ?></small>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Role' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
