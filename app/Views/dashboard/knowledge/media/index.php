<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$type = $filterType ?? null;
$status = $filterStatus ?? null;
$keyword = $filterKeyword ?? null;
$scope = $scope ?? 'all';
$stats = $stats ?? [];

$typeOptions = [
    'infographic'   => 'Infografis',
    'image'         => 'Gambar',
    'poster'        => 'Poster',
    'flyer'         => 'Flyer',
    'youtube'       => 'Video YouTube',
    'video_file'    => 'Video File',
    'gallery'       => 'Galeri Kegiatan',
    'social_link'   => 'Social Link',
    'external_link' => 'External Link',
];

$statusLabels = [
    'published'         => 'Dipublikasikan',
    'draft'             => 'Draft',
    'in_review'         => 'Ditinjau',
    'submitted'         => 'Diajukan',
    'approved'          => 'Disetujui',
    'revision_required' => 'Perlu Revisi',
    'rejected'          => 'Ditolak',
    'archived'          => 'Diarsipkan',
];

$createParam = match ($scope) {
    'infographics' => '?type=infographic',
    'videos'       => '?type=youtube',
    'gallery'      => '?type=gallery',
    default        => '',
};
?>

<!-- Header Toolbar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Media & Galeri') ?></h6>
        <p class="text-secondary-light mb-0">Kelola kurasi visual, infografik, video edukasi pengadaan, dan album kegiatan resmi.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('bunga-rampai/' . ($scope === 'infographics' ? 'media' : ($scope === 'videos' ? 'video' : ($scope === 'gallery' ? 'galeri' : 'media')))) ?>" target="_blank" class="btn btn-outline-primary radius-8 px-16 py-11 text-xs d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:eye-bold"></iconify-icon>
            Pratinjau Publik
        </a>
        <a href="<?= site_url('dashboard/bunga-rampai/media-galeri/tambah' . $createParam) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
            <span>+ Tambah Media</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/knowledge/_nav_knowledge', ['section' => 'media', 'activeSub' => ($scope === 'all' ? 'media-gallery' : $scope)]) ?>

<!-- Search & Filter Card -->
<div class="card radius-12 mb-24 border bg-base shadow-sm">
    <div class="card-body p-20">
        <form method="get" action="<?= current_url() ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-xs fw-semibold text-secondary-light mb-1">Pencarian Kata Kunci</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari judul, keterangan, lokasi...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>

                <?php if ($scope === 'all'): ?>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-semibold text-secondary-light mb-1">Tipe Format</label>
                        <select name="type" class="form-select radius-8">
                            <option value="">— Semua Tipe —</option>
                            <?php foreach ($typeOptions as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="<?= $scope === 'all' ? 'col-md-2' : 'col-md-4' ?>">
                    <label class="form-label text-xs fw-semibold text-secondary-light mb-1">Status Publikasi</label>
                    <select name="status" class="form-select radius-8">
                        <option value="">— Semua Status —</option>
                        <?php foreach ($statusLabels as $k => $l): ?>
                            <option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="<?= $scope === 'all' ? 'col-md-2' : 'col-md-3' ?> d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= current_url() ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Visual Cards Grid -->
<?php if (empty($items)): ?>
    <?= view('dashboard/components/empty_state', [
        'icon'        => 'solar:gallery-broken',
        'title'       => 'Belum Ada Konten Media',
        'message'     => 'Belum ada konten media atau galeri yang sesuai dengan kriteria filter saat ini.',
        'actionUrl'   => site_url('dashboard/bunga-rampai/media-galeri/tambah' . $createParam),
        'actionText'  => 'Tambah Media Baru',
    ]) ?>
<?php else: ?>
    <div class="row g-4 mb-24">
        <?php foreach ($items as $m): ?>
            <?php
                // Resolve preview thumbnail
                $thumbSrc = null;
                if (! empty($m['thumb_path'])) {
                    $thumbSrc = base_url('file/' . (int) $m['thumbnail_file_id']);
                } elseif (! empty($m['file_path'])) {
                    $thumbSrc = base_url('file/' . (int) $m['file_id']);
                } elseif (! empty($m['external_url'])) {
                    // Check if YouTube
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $m['external_url'], $matches)) {
                        $thumbSrc = 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
                    } else {
                        $thumbSrc = $m['external_url'];
                    }
                }

                $isVideo = in_array($m['type'] ?? '', ['youtube', 'video_file', 'video'], true);
                $isGallery = ($m['type'] ?? '') === 'gallery';
                $statusKey = $m['workflow_status'] ?? 'draft';

                $typeBadge = match ($m['type']) {
                    'youtube', 'video_file', 'video' => 'bg-danger text-white',
                    'gallery'                        => 'bg-dark text-white',
                    'infographic'                    => 'bg-info-50 text-info-700',
                    default                          => 'bg-primary-50 text-primary-700',
                };
            ?>
            <div class="col-xxl-4 col-lg-6 col-md-6">
                <div class="card h-100 border border-neutral-200 radius-16 overflow-hidden bg-white shadow-sm d-flex flex-column">
                    <!-- Media Showcase Container -->
                    <div class="position-relative" style="height: 200px; background: #082f5c; overflow: hidden;">
                        <?php if ($thumbSrc): ?>
                            <img src="<?= esc($thumbSrc) ?>" alt="<?= esc($m['alt_text'] ?? $m['title'] ?? '') ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
                                <iconify-icon icon="solar:gallery-broken" class="text-5xl"></iconify-icon>
                            </div>
                        <?php endif; ?>

                        <!-- Top Badges -->
                        <div class="position-absolute top-0 start-0 m-3 z-1">
                            <span class="badge <?= esc($typeBadge) ?> radius-6 px-8 py-4 text-2xs fw-bold">
                                <?= $isVideo ? '<i class="fab fa-youtube me-1"></i> Video' : ($isGallery ? '<i class="fas fa-camera me-1"></i> Galeri' : '<i class="fas fa-image me-1"></i> ' . esc($typeOptions[$m['type']] ?? $m['type'])) ?>
                            </span>
                        </div>

                        <div class="position-absolute top-0 end-0 m-3 z-1">
                            <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                        </div>

                        <!-- Video Play Indicator -->
                        <?php if ($isVideo): ?>
                            <div class="position-absolute top-50 start-50 translate-middle z-1">
                                <div class="w-44-px h-44-px radius-50 bg-danger text-white d-flex align-items-center justify-content-center shadow-lg">
                                    <iconify-icon icon="solar:play-bold" class="text-xl ms-1"></iconify-icon>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Card Body -->
                    <div class="p-20 d-flex flex-column flex-grow-1">
                        <!-- Engagement & Location Bar -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-12 text-xs text-secondary-light">
                            <div class="d-flex align-items-center gap-3">
                                <span><i class="fas fa-heart text-danger me-1"></i><?= (int) ($m['likes_count'] ?? 0) ?> Suka</span>
                                <span><i class="fas fa-eye text-primary me-1"></i><?= (int) ($m['views_count'] ?? 0) ?> Tayangan</span>
                            </div>
                            <?php if (! empty($m['location'])): ?>
                                <span class="text-truncate" style="max-width: 140px;" title="<?= esc($m['location']) ?>">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i><?= esc($m['location']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Title -->
                        <h6 class="fw-bold mb-8 text-primary-900" style="font-size: 15px; line-height: 1.4;">
                            <a href="<?= route_to('dashboard.knowledge.media-gallery.detail', (string) $m['id']) ?>" class="text-decoration-none text-reset text-line-2 hover-primary">
                                <?= esc($m['title'] ?: '(Tanpa Judul)') ?>
                            </a>
                        </h6>

                        <!-- Caption Excerpt -->
                        <p class="text-secondary-light text-xs mb-16 text-line-2 flex-grow-1" style="line-height: 1.5;">
                            <?= esc($m['caption'] ?: 'Tidak ada keterangan caption teks.') ?>
                        </p>

                        <!-- Hashtags -->
                        <?php if (! empty($m['hashtags'])): ?>
                            <div class="mb-16 text-truncate text-2xs text-primary-700 fw-semibold">
                                <?= esc($m['hashtags']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Footer Actions -->
                        <div class="pt-16 border-top d-flex align-items-center justify-content-between mt-auto">
                            <a href="<?= route_to('dashboard.knowledge.media-gallery.detail', (string) $m['id']) ?>" class="btn btn-sm btn-outline-primary radius-6 px-12 py-5 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="solar:eye-bold"></iconify-icon> Detail Preview
                            </a>

                            <div class="d-flex align-items-center gap-2">
                                <a href="<?= route_to('dashboard.knowledge.media-gallery.edit', (string) $m['id']) ?>" class="btn btn-sm btn-outline-warning-600 text-warning-700 radius-6 px-12 py-5 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:pen-bold"></iconify-icon> Ubah
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger radius-6 px-10 py-5 text-xs"
                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                    data-action="<?= route_to('dashboard.knowledge.media-gallery.delete', (string) $m['id']) ?>"
                                    data-method="POST"
                                    data-title="Hapus Media"
                                    data-message="Apakah Anda yakin ingin menghapus media &quot;<?= esc($m['title'] ?: 'media ini') ?>&quot;?"
                                    data-btn-text="Hapus Media"
                                    data-btn-class="btn-danger-600"
                                    data-icon="solar:trash-bin-trash-outline"
                                    data-icon-color="danger">
                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>