<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $formMode = $formMode ?? 'create'; $isEdit = $formMode === 'edit'; $stage = $stage ?? []; $stgId = $stgId ?? null; ?>

<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger mb-16"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tahapan PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Form <?= $isEdit ? 'Ubah' : 'Tambah' ?> Tahapan.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/regulasi/tahapan-pbj/' . (int) $stgId . '/ubah') : site_url('dashboard/regulasi/tahapan-pbj/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan</label>
                    <input type="number" min="0" class="form-control radius-8" name="sort_order" value="<?= esc(old('sort_order') ?? $stage['sort_order'] ?? 0) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="key" value="<?= esc(old('key') ?? $stage['key'] ?? '') ?>" required maxlength="64" pattern="[A-Z0-9_]+" placeholder="PLAN">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Tahapan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="name" value="<?= esc(old('name') ?? $stage['name'] ?? '') ?>" required maxlength="255">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea class="form-control radius-8" name="description" rows="3"><?= esc(old('description') ?? $stage['description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($stage['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Tahapan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>