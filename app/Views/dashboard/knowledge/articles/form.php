<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$article = $article ?? [];
$selectedCats = $selectedCategories ?? [];
$selectedTags = $selectedTags ?? [];
$categories = $categories ?? [];
$tags = $tags ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$visibilityOptions = [
    'public' => 'Publik', 'internal' => 'Internal', 'restricted' => 'Restricted',
];
$action = $isEdit ? route_to('dashboard.knowledge.articles.edit', (string) ($articleId ?? '')) : route_to('dashboard.knowledge.articles.create');
$tagsCsv = implode(', ', $selectedTags);
$currentStatus = $article['workflow_status'] ?? 'draft';
$currentVisibility = $article['visibility'] ?? 'internal';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Artikel') ?></h6><p class="text-secondary-light mb-0">Form artikel Bunga Rampai.</p></div>
    <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $article['title'] ?? '')) ?>" required maxlength="255">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                <select name="status" class="form-select radius-8" required>
                    <?php foreach ($statusOptions as $k => $label): ?>
                        <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label>
                <textarea name="summary" class="form-control radius-8" rows="3"><?= esc(old('summary', $article['summary'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                <select name="visibility" class="form-select radius-8">
                    <?php foreach ($visibilityOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentVisibility === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
                <select name="categories[]" class="form-select radius-8" multiple size="5">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-secondary-light">Tahan Ctrl/Cmd untuk memilih lebih dari satu.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tag</label>
                <input type="text" name="tags" class="form-control radius-8" value="<?= esc($tagsCsv) ?>" placeholder="pisahkan dengan koma">
                <small class="text-secondary-light">Tag baru akan otomatis dibuat.</small>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Konten</label>
                <textarea name="content" class="form-control radius-8" rows="12"><?= esc(old('content', $article['content'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Cover Image</label>
                <input type="file" name="cover" class="form-control radius-8" accept="image/*">
                <?php if (! empty($article['cover_file_id'])): ?><small class="text-secondary-light d-block mt-1">Cover saat ini: file_id #<?= (int) $article['cover_file_id'] ?></small><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Konten Unggulan</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($article['featured']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="featured">Tampilkan sebagai konten unggulan</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Carousel</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($article['carousel_enabled']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="carousel_enabled">Aktifkan di carousel</label>
                </div>
                <input type="number" name="carousel_order" min="0" class="form-control radius-8 mt-2" value="<?= esc(old('carousel_order', $article['carousel_order'] ?? '')) ?>" placeholder="Urutan tampil">
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Artikel</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>