<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$type = $filterType ?? null;
$status = $filterStatus ?? null;
$keyword = $filterKeyword ?? null;
$typeOptions = [
    'image' => 'Gambar', 'poster' => 'Poster', 'flyer' => 'Flyer',
    'infographic' => 'Infografis', 'gallery' => 'Galeri',
    'video_file' => 'Video File', 'youtube' => 'YouTube',
    'social_link' => 'Social Link', 'external_link' => 'External Link',
];
$statusLabels = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'submitted' => 'Diajukan', 'in_review' => 'Ditinjau', 'approved' => 'Disetujui',
    'revision_required' => 'Perlu Revisi', 'rejected' => 'Ditolak',
];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Media dan Galeri</h6>
        <p class="text-secondary-light mb-0">Kelola konten media visual dan galeri Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.media-gallery.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Media</a>
</div>
<form method="get" action="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="card mb-3">
    <div class="card-body p-16">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Pencarian</label>
                <input type="text" name="q" class="form-control radius-8" value="<?= esc($keyword ?? '') ?>" placeholder="Judul/caption/alt">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Tipe</label>
                <select name="type" class="form-select radius-8">
                    <option value="">— Semua —</option>
                    <?php foreach ($typeOptions as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select radius-8">
                    <option value="">— Semua —</option>
                    <?php foreach ($statusLabels as $k => $l): ?>
                        <option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1">Filter</button>
                <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8">Reset</a>
            </div>
        </div>
    </div>
</form>
<div class="row gy-4">
    <?php if (empty($items)): ?>
        <div class="col-12"><div class="card"><div class="card-body p-24"><p class="text-secondary-light mb-0">Belum ada media. Klik "Tambah Media".</p></div></div></div>
    <?php else: foreach ($items as $m): ?>
        <?php
            // Resolve thumbnail → file → external url, in priority order.
            $thumbSrc = null;
            if (! empty($m['thumb_path'])) {
                $thumbSrc = base_url('file/' . (int) $m['thumbnail_file_id']);
            } elseif (! empty($m['file_path'])) {
                $thumbSrc = base_url('file/' . (int) $m['file_id']);
            } elseif (! empty($m['external_url'])) {
                $thumbSrc = $m['external_url'];
            }
            $isImage = ! empty($m['file_mime_type']) && str_starts_with((string) $m['file_mime_type'], 'image/');
            $isVideo = ($m['type'] ?? '') === 'video_file' && $isImage === false;
        ?>
        <div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-0">
                    <div class="h-180-px bg-neutral-100 d-flex align-items-center justify-content-center overflow-hidden">
                        <?php if ($thumbSrc): ?>
                            <img src="<?= esc($thumbSrc) ?>" alt="<?= esc($m['alt_text'] ?? $m['title'] ?? '') ?>"
                                 style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <iconify-icon icon="solar:gallery-wide-outline" class="text-primary-600 text-5xl"></iconify-icon>
                        <?php endif; ?>
                    </div>
                    <div class="p-24">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                    <h6 class="text-md mb-6"><?= esc($m['title'] ?: '(Tanpa judul)') ?></h6>
                                    <p class="text-secondary-light text-sm mb-12"><?= esc($typeOptions[$m['type']] ?? $m['type']) ?> · <?= esc($m['updated_at'] ?? '-') ?></p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($statusLabels[$m['workflow_status'] ?? 'draft'] ?? ucfirst($m['workflow_status'] ?? 'draft')) ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-16 pt-16 border-top">
                            <a href="<?= route_to('dashboard.knowledge.media-gallery.detail', (string) $m['id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:eye-outline"></iconify-icon> Detail
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?= route_to('dashboard.knowledge.media-gallery.edit', (string) $m['id']) ?>" class="btn btn-outline-warning-main btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Ubah
                                </a>
                                <button type="button" class="btn btn-outline-danger-600 btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                    data-action="<?= route_to('dashboard.knowledge.media-gallery.delete', (string) $m['id']) ?>"
                                    data-method="POST"
                                    data-title="Hapus Media"
                                    data-message="Apakah Anda yakin ingin menghapus media ini?"
                                    data-btn-text="Hapus Media"
                                    data-btn-class="btn-danger-600"
                                    data-icon="fluent:delete-24-regular"
                                    data-icon-color="danger">
                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>
<?= $this->endSection() ?>