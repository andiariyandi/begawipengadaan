<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $category ?? [];
$parents = $parents ?? [];
$action = $isEdit ? route_to('dashboard.knowledge.categories.edit', (string) ($categoryId ?? '')) : route_to('dashboard.knowledge.categories.create');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kategori Konten') ?></h6>
        <p class="text-secondary-light mb-0">Atur struktur pengelompokan materi dan artikel Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Kembali ke Kategori</span>
    </a>
</div>

<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
        <iconify-icon icon="solar:folder-bold" class="text-primary-600 text-xl"></iconify-icon>
        <h6 class="text-base fw-bold mb-0"><?= $isEdit ? 'Ubah Informasi Kategori' : 'Tambah Kategori Baru' ?></h6>
    </div>
    <div class="card-body p-24">
        <form method="post" action="<?= $action ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Perencanaan Pengadaan">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Induk (Parent Hierarchy)</label>
                    <select name="parent_id" class="form-select radius-8">
                        <option value="">— Tingkat Utama (Root) —</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?= (int) $p['id'] ?>" <?= (int) ($row['parent_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Prioritas</label>
                    <input type="number" min="0" name="sort_order" class="form-control radius-8" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>" placeholder="0">
                </div>
                <div class="col-md-3 d-flex align-items-center pt-28">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?= empty($row) || ! empty($row['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-primary-light text-sm" for="is_active">Aktifkan Kategori</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Ringkas</label>
                    <textarea name="description" class="form-control radius-8" rows="3" placeholder="Jelaskan cakupan topik materi pada kategori ini..."><?= esc(old('description', $row['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12 pt-20 border-top">
                    <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Kategori</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>