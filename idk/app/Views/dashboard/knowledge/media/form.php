<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $media ?? [];
$typeOptions = [
    'image' => 'Gambar', 'poster' => 'Poster', 'flyer' => 'Flyer',
    'infographic' => 'Infografis', 'gallery' => 'Galeri',
    'video_file' => 'Video File', 'youtube' => 'YouTube',
    'social_link' => 'Social Link', 'external_link' => 'External Link',
];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$visibilityOptions = ['public' => 'Publik', 'internal' => 'Internal', 'restricted' => 'Restricted'];
$action = $isEdit ? route_to('dashboard.knowledge.media-gallery.edit', (string) ($mediaId ?? '')) : route_to('dashboard.knowledge.media-gallery.create');
$currentType = $row['type'] ?? 'image';
$currentStatus = $row['workflow_status'] ?? 'draft';
$currentVis = $row['visibility'] ?? 'internal';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Media') ?></h6><p class="text-secondary-light mb-0">Form media dan galeri.</p></div>
    <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<?php if ($msg = session()->getFlashdata('error')): ?><div class="alert alert-danger radius-8 mb-3"><?= esc($msg) ?></div><?php endif; ?>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $row['title'] ?? '')) ?>" maxlength="255">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe</label>
                <select name="type" class="form-select radius-8" required>
                    <?php foreach ($typeOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentType === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                <select name="status" class="form-select radius-8" required>
                    <?php foreach ($statusOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                <select name="visibility" class="form-select radius-8">
                    <?php foreach ($visibilityOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentVis === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan</label>
                <input type="number" min="0" name="sort_order" class="form-control radius-8" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">File</label>
                <input type="file" name="file" class="form-control radius-8">
                <?php if (! empty($row['file_id'])): ?><small class="text-secondary-light d-block mt-1">File saat ini tersimpan (file_id #<?= (int) $row['file_id'] ?>).</small><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Thumbnail</label>
                <input type="file" name="thumbnail" class="form-control radius-8">
                <?php if (! empty($row['thumbnail_file_id'])): ?><small class="text-secondary-light d-block mt-1">Thumbnail saat ini (file_id #<?= (int) $row['thumbnail_file_id'] ?>).</small><?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">External URL (untuk tipe youtube/social_link/external_link)</label>
                <input type="url" name="external_url" class="form-control radius-8" value="<?= esc(old('external_url', $row['external_url'] ?? '')) ?>" placeholder="https://">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Caption</label>
                <textarea name="caption" class="form-control radius-8" rows="3"><?= esc(old('caption', $row['caption'] ?? '')) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alt Text</label>
                <input type="text" name="alt_text" class="form-control radius-8" value="<?= esc(old('alt_text', $row['alt_text'] ?? '')) ?>" maxlength="500">
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($row['featured']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="featured">Konten Unggulan</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($row['carousel_enabled']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="carousel_enabled">Aktifkan di Carousel</label>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Media</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>