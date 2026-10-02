<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$program = $program ?? [];
$isEdit = ($formMode ?? 'create') === 'edit';
$formAction = $isEdit
    ? route_to('dashboard.learning.programs.update', $program['id'])
    : route_to('dashboard.learning.programs.store');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Program') ?></h6>
        <p class="text-secondary-light mb-0">Kelola data program pembelajaran.</p>
    </div>
    <a href="<?= route_to('dashboard.learning.programs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc($formAction) ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="title">Judul Program</label>
                    <input id="title" type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $program['title'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" class="form-control radius-8" value="<?= esc(old('slug', $program['slug'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="status">Status</label>
                    <select id="status" name="status" class="form-select radius-8">
                        <?php $status = old('status', $program['status'] ?? 'draft'); ?>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="visibility">Visibilitas</label>
                    <input id="visibility" type="text" name="visibility" class="form-control radius-8" value="<?= esc(old('visibility', $program['visibility'] ?? '')) ?>" placeholder="public">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="summary">Ringkasan</label>
                    <textarea id="summary" name="summary" class="form-control radius-8" rows="2"><?= esc(old('summary', $program['summary'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control radius-8" rows="5"><?= esc(old('description', $program['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="cover_file_id">ID File Cover</label>
                    <input id="cover_file_id" type="number" name="cover_file_id" class="form-control radius-8" value="<?= esc(old('cover_file_id', $program['cover_file_id'] ?? '')) ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check mb-8">
                        <input id="featured" type="checkbox" name="featured" value="1" class="form-check-input" <?= old('featured', $program['featured'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="featured">Tampilkan sebagai program unggulan</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= route_to('dashboard.learning.programs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Program</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
