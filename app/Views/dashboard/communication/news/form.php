<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
$cats = $newsCategories ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Berita') ?></h6>
        <p class="text-secondary-light mb-0">Form berita BegawiPBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? route_to('dashboard.communication.news.edit', (string) ($newsId ?? '')) : route_to('dashboard.communication.news.create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8"
                           value="<?= esc(old('title', $item['title'] ?? '')) ?>" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
                    <select class="form-select radius-8" name="category_id">
                        <option value="">— Tanpa kategori —</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) ($item['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Publikasi</label>
                    <select class="form-select radius-8" name="workflow_status">
                        <?php foreach (['draft' => 'Draft', 'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui', 'published' => 'Dipublikasikan', 'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak', 'archived' => 'Diarsipkan'] as $k => $l): ?>
                            <option value="<?= $k ?>" <?= ($item['workflow_status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                    <select class="form-select radius-8" name="visibility">
                        <?php foreach (['public' => 'Publik', 'internal' => 'Internal', 'restricted' => 'Restricted'] as $k => $l): ?>
                            <option value="<?= $k ?>" <?= ($item['visibility'] ?? 'public') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Publikasi</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($item['scheduled_at'])) : old('scheduled_at')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label>
                    <textarea name="summary" class="form-control radius-8" rows="3"><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Isi Berita</label>
                    <textarea name="content" class="form-control radius-8" rows="12"><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Gambar Sampul</label>
                    <input type="file" name="cover" class="form-control radius-8" accept="image/*">
                    <?php if (! empty($item['cover_file_id'])): ?>
                        <small class="text-secondary-light d-block mt-1">Cover saat ini: file_id #<?= (int) $item['cover_file_id'] ?> (<?= esc($item['cover_original_name'] ?? '') ?>)</small>
                    <?php endif; ?>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Berita</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>