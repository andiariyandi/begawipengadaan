<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $template ?? [];
$categories = $categories ?? [];
$selectedCats = $selectedCategories ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$action = $isEdit ? route_to('dashboard.knowledge.document-templates.edit', (string) ($templateId ?? '')) : route_to('dashboard.knowledge.document-templates.create');
$currentStatus = $row['workflow_status'] ?? 'draft';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Template Dokumen') ?></h6><p class="text-secondary-light mb-0">Form template dokumen Bunga Rampai.</p></div>
    <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Template</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $row['title'] ?? '')) ?>" required maxlength="255">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                <select name="status" class="form-select radius-8" required>
                    <?php foreach ($statusOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
                <select name="categories[]" class="form-select radius-8" multiple size="5">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">File Template</label>
                <input type="file" name="template_file" class="form-control radius-8">
                <?php if (! empty($row['cover_file_id'])): ?><small class="text-secondary-light d-block mt-1">File saat ini tersimpan (file_id #<?= (int) $row['cover_file_id'] ?>). Unggah baru akan mengganti.</small><?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                <textarea name="summary" class="form-control radius-8" rows="4"><?= esc(old('summary', $row['summary'] ?? '')) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Konten Pendukung</label>
                <textarea name="content" class="form-control radius-8" rows="6"><?= esc(old('content', $row['content'] ?? '')) ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Template</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>