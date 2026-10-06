<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $tag ?? [];
$action = $isEdit ? route_to('dashboard.knowledge.tags.edit', (string) ($tagId ?? '')) : route_to('dashboard.knowledge.tags.create');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tag') ?></h6><p class="text-secondary-light mb-0">Form tag Bunga Rampai.</p></div>
    <a href="<?= route_to('dashboard.knowledge.tags') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Tag</label>
                <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required maxlength="255">
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.tags') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Tag</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>