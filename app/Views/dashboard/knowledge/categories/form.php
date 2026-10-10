<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $category ?? [];
$parents = $parents ?? [];
$action = $isEdit ? route_to('dashboard.knowledge.categories.edit', (string) ($categoryId ?? '')) : route_to('dashboard.knowledge.categories.create');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori') ?></h6><p class="text-secondary-light mb-0">Form kategori Bunga Rampai.</p></div>
    <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama</label>
                <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required maxlength="255">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Parent</label>
                <select name="parent_id" class="form-select radius-8">
                    <option value="">— Tidak ada (Root) —</option>
                    <?php foreach ($parents as $p): ?>
                        <option value="<?= (int) $p['id'] ?>" <?= (int) ($row['parent_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan</label>
                <input type="number" min="0" name="sort_order" class="form-control radius-8" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?= empty($row) || ! empty($row['is_active']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                <textarea name="description" class="form-control radius-8" rows="3"><?= esc(old('description', $row['description'] ?? '')) ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kategori</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>