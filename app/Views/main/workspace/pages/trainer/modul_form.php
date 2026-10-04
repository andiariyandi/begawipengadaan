<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2"><?= esc($mode === 'edit' ? 'Ubah Modul' : 'Tambah Modul') ?></h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <form method="post" action="<?= ($mode ?? 'create') === 'edit' ? route_to('workspace.trainer.module.edit', $classId, $module['id'] ?? 0) : route_to('workspace.trainer.module.create', $classId) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Judul</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc($module['title'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Urutan</label>
                <input type="number" name="sort_order" class="form-control radius-8" value="<?= (int) ($module['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Status</label>
                <select name="status" class="form-select radius-8">
                    <?php foreach (['draft', 'published', 'archived'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($module['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Deskripsi</label>
                <textarea name="description" class="form-control radius-8" rows="3"><?= esc($module['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-8">
                <a href="<?= route_to('workspace.trainer.modules', $classId) ?>" class="btn btn-outline-neutral-300 radius-8">Batal</a>
                <button type="submit" class="btn btn-primary radius-8">Simpan</button>
            </div>
        </div>
    </form>
</div>