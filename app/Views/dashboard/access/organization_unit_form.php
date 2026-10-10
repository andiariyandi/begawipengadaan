<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode = $formMode ?? 'create';
$unit = $unit ?? [];
$units = $units ?? [];
$unitId = $unitId ?? null;
$isEdit = $formMode === 'edit';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Unit Organisasi') ?></h6>
        <p class="text-secondary-light mb-0">Form <?= $isEdit ? 'Ubah' : 'Tambah' ?> Unit Organisasi.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/akses/unit-organisasi/' . (int) $unitId . '/ubah') : site_url('dashboard/akses/unit-organisasi/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Unit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="code"
                           value="<?= esc(old('code') ?? $unit['code'] ?? '') ?>" placeholder="Contoh: BPPBJ" required maxlength="64">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Unit <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="type" required>
                        <option value="">Pilih tipe</option>
                        <?php foreach (['Provinsi', 'OPD', 'Biro', 'Badan', 'Dinas', 'Inspektorat', 'UPT', 'Unit Kerja'] as $t): ?>
                            <option value="<?= esc($t) ?>" <?= ($unit['type'] ?? old('type')) === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Unit Organisasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="name"
                           value="<?= esc(old('name') ?? $unit['name'] ?? '') ?>" placeholder="Masukkan nama unit organisasi" required maxlength="255">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Induk</label>
                    <select class="form-select radius-8" name="parent_id">
                        <option value="">— Tidak ada —</option>
                        <?php foreach ($units as $u): ?>
                            <?php if ($isEdit && (int) ($u['id'] ?? 0) === (int) $unitId) continue; ?>
                            <option value="<?= (int) ($u['id'] ?? 0) ?>" <?= (int) ($unit['parent_id'] ?? 0) === (int) ($u['id'] ?? 0) ? 'selected' : '' ?>>
                                <?= esc($u['name']) ?> (<?= esc($u['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Sort Order</label>
                    <input type="number" min="0" class="form-control radius-8" name="sort_order"
                           value="<?= esc(old('sort_order') ?? $unit['sort_order'] ?? 0) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="is_active">
                        <option value="1" <?= (int) ($unit['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= (int) ($unit['is_active'] ?? 1) === 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                    <textarea class="form-control radius-8" name="address" rows="2" placeholder="Alamat unit (opsional)"><?= esc(old('address') ?? $unit['address'] ?? '') ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Unit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>