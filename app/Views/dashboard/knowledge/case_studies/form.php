<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $caseStudy ?? [];
$categories = $categories ?? [];
$selectedCats = $selectedCategories ?? [];
$statusOptions = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
$visibilityOptions = ['public' => 'Publik', 'internal' => 'Internal', 'restricted' => 'Restricted'];
$action = $isEdit ? route_to('dashboard.knowledge.case-studies.edit', (string) ($caseStudyId ?? '')) : route_to('dashboard.knowledge.case-studies.create');
$currentStatus = $row['workflow_status'] ?? 'draft';
$currentVis = $row['visibility'] ?? 'internal';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Studi Kasus') ?></h6><p class="text-secondary-light mb-0">Form studi kasus Bunga Rampai.</p></div>
    <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24">
    <form method="post" action="<?= $action ?>">
        <?= csrf_field() ?>
        <div class="row gy-20">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul</label>
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
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                <select name="visibility" class="form-select radius-8">
                    <?php foreach ($visibilityOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $currentVis === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
                <select name="categories[]" class="form-select radius-8" multiple size="5">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan / Latar Belakang</label>
                <textarea name="summary" class="form-control radius-8" rows="4"><?= esc(old('summary', $row['summary'] ?? '')) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Konten / Pembelajaran Utama</label>
                <textarea name="content" class="form-control radius-8" rows="10"><?= esc(old('content', $row['content'] ?? '')) ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-12">
                <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Studi Kasus</button>
            </div>
        </div>
    </form>
</div></div>
<?= $this->endSection() ?>