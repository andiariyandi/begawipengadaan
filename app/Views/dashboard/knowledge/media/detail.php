<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$row = $media ?? [];
$statusKey = $row['workflow_status'] ?? 'draft';

// Determine preview source
$imgSrc = null;
if (! empty($row['file_path'])) {
    $imgSrc = base_url('file/' . (int) $row['file_id']);
} elseif (! empty($row['thumb_path'])) {
    $imgSrc = base_url('file/' . (int) $row['thumbnail_file_id']);
} elseif (! empty($row['external_url'])) {
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $row['external_url'], $matches)) {
        $imgSrc = 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
    } else {
        $imgSrc = $row['external_url'];
    }
}

$isYouTube = in_array($row['type'] ?? '', ['youtube', 'video_file', 'video'], true) && ! empty($row['external_url']);
$youtubeId = null;
if ($isYouTube && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $row['external_url'], $matches)) {
    $youtubeId = $matches[1];
}

$isGallery = ($row['type'] ?? '') === 'gallery';
$isInfographic = in_array($row['type'] ?? '', ['infographic', 'image', 'poster', 'flyer'], true);
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Media &amp; Pratinjau Visual</h6>
        <p class="text-secondary-light mb-0">ID Media: #<?= (int) ($mediaId ?? ($row['id'] ?? 0)) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.media-gallery.edit', (string) ($mediaId ?? ($row['id'] ?? ''))) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah Media
        </a>
        <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<div class="row gy-24">
    <!-- Kolom Kiri: Panggung Visual Media -->
    <div class="col-xl-8">
        <div class="card border-0 radius-16 shadow-sm overflow-hidden bg-white mb-24">
            <!-- Stage Display -->
            <div class="position-relative bg-dark d-flex align-items-center justify-content-center" style="min-height: 380px; max-height: 520px; overflow: hidden;">
                <?php if ($youtubeId): ?>
                    <div class="ratio ratio-16x9 w-100">
                        <iframe src="https://www.youtube.com/embed/<?= esc($youtubeId) ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border:0;"></iframe>
                    </div>
                <?php elseif ($imgSrc): ?>
                    <img src="<?= esc($imgSrc) ?>" alt="<?= esc($row['alt_text'] ?? $row['title'] ?? '') ?>" class="w-100 h-100 object-fit-contain" style="max-height: 500px;">
                <?php else: ?>
                    <div class="text-white-50 text-center py-48">
                        <iconify-icon icon="solar:gallery-broken" class="text-6xl mb-2"></iconify-icon>
                        <p class="mb-0">Tidak ada berkas media terunggah</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-12">
                    <span class="badge bg-primary-50 text-primary-700 radius-6 px-10 py-5 text-xs text-uppercase fw-semibold">
                        <?= esc($row['type'] ?? 'Media') ?>
                    </span>
                    <div class="d-flex align-items-center gap-3 text-secondary-light text-xs">
                        <span><i class="fas fa-heart text-danger me-1"></i><strong><?= (int) ($row['likes_count'] ?? 0) ?></strong> Suka</span>
                        <span><i class="fas fa-eye text-primary me-1"></i><strong><?= (int) ($row['views_count'] ?? 0) ?></strong> Tayangan</span>
                        <span><i class="fas fa-share-alt text-info me-1"></i><strong><?= (int) ($row['shares_count'] ?? 0) ?></strong> Dibagikan</span>
                    </div>
                </div>

                <h4 class="fw-bold mb-12 text-primary-900"><?= esc($row['title'] ?: '(Tanpa Judul)') ?></h4>

                <p class="text-secondary-light text-sm mb-16" style="line-height: 1.65;">
                    <?= nl2br(esc($row['caption'] ?? 'Tidak ada deskripsi caption.')) ?>
                </p>

                <?php if (! empty($row['hashtags'])): ?>
                    <div class="p-12 radius-8 bg-neutral-50 border border-neutral-200 text-xs text-primary-700 fw-semibold mb-16">
                        <?= esc($row['hashtags']) ?>
                    </div>
                <?php endif; ?>

                <?php if (! empty($row['alt_text'])): ?>
                    <div class="text-xs text-secondary-light">
                        <strong class="text-dark">Alt Text Aksesibilitas:</strong> <?= esc($row['alt_text']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Parameter & Sumber -->
    <div class="col-xl-4">
        <!-- Status & Pengaturan -->
        <div class="card border-0 radius-16 shadow-sm bg-white mb-24">
            <div class="card-header border-bottom p-20 bg-white">
                <h6 class="text-base fw-bold mb-0">Parameter &amp; Publikasi</h6>
            </div>
            <div class="card-body p-20">
                <div class="mb-16 pb-16 border-bottom d-flex align-items-center justify-content-between">
                    <span class="text-secondary-light text-xs">Status Alur Kerja</span>
                    <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                </div>

                <div class="mb-16 pb-16 border-bottom">
                    <span class="text-secondary-light text-xs d-block mb-1">Akses Visibilitas</span>
                    <strong class="text-dark text-sm text-capitalize"><?= esc($row['visibility'] ?? 'public') ?></strong>
                </div>

                <?php if (! empty($row['location'])): ?>
                    <div class="mb-16 pb-16 border-bottom">
                        <span class="text-secondary-light text-xs d-block mb-1">Lokasi Acara</span>
                        <span class="text-dark text-sm"><i class="fas fa-map-marker-alt text-danger me-1"></i><?= esc($row['location']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($row['event_date'])): ?>
                    <div class="mb-16 pb-16 border-bottom">
                        <span class="text-secondary-light text-xs d-block mb-1">Tanggal Kegiatan</span>
                        <span class="text-dark text-sm"><?= esc(date('d F Y', strtotime($row['event_date']))) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($row['duration'])): ?>
                    <div class="mb-16 pb-16 border-bottom">
                        <span class="text-secondary-light text-xs d-block mb-1">Durasi Video</span>
                        <span class="text-dark text-sm"><i class="far fa-clock text-primary me-1"></i><?= esc($row['duration']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($row['external_url'])): ?>
                    <div class="mb-16 pb-16 border-bottom">
                        <span class="text-secondary-light text-xs d-block mb-1">Tautan Platform Asli</span>
                        <a href="<?= esc($row['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-6 text-xs w-100 d-inline-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:link-circle-bold"></iconify-icon> Buka Tautan Eksternal
                        </a>
                    </div>
                <?php endif; ?>

                <div>
                    <span class="text-secondary-light text-xs d-block mb-1">Terakhir Diperbarui</span>
                    <span class="text-dark text-xs"><?= esc(date('d M Y, H:i', strtotime($row['updated_at'] ?? 'now'))) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>