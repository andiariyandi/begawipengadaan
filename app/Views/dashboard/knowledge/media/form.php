<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$row = $media ?? [];
$typeOptions = [
    'infographic'   => 'Infografis (Informasi Visual & Data PBJ)',
    'image'         => 'Gambar / Foto Umum',
    'poster'        => 'Poster Pengumuman / Sosialisasi',
    'flyer'         => 'Flyer Kegiatan / Event',
    'youtube'       => 'Video YouTube (Webinar / Podcast / Edukasi)',
    'video_file'    => 'Video File Mandiri (MP4/WebM)',
    'gallery'       => 'Galeri Kegiatan & Dokumentasi Acara',
    'social_link'   => 'Tautan Media Sosial',
    'external_link' => 'Tautan Sumber Eksternal',
];
$statusOptions = [
    'published'         => 'Dipublikasikan (Publik)',
    'draft'             => 'Draft (Konsep)',
    'in_review'         => 'Ditinjau (In Review)',
    'submitted'         => 'Diajukan',
    'approved'          => 'Disetujui',
    'revision_required' => 'Perlu Revisi',
    'rejected'          => 'Ditolak',
    'archived'          => 'Diarsipkan',
];
$visibilityOptions = [
    'public'     => 'Publik (Tampil di Landing Page & Katalog)',
    'internal'   => 'Internal (Aparatur PBJ)',
    'restricted' => 'Terbatas (Akses Khusus)',
];

$preselected = $preselectedType ?? ($row['type'] ?? 'infographic');
$currentType = $row['type'] ?? $preselected;
$currentStatus = $row['workflow_status'] ?? 'published';
$currentVis = $row['visibility'] ?? 'public';
$action = $isEdit ? route_to('dashboard.knowledge.media-gallery.edit', (string) ($mediaId ?? '')) : route_to('dashboard.knowledge.media-gallery.create');

$fileImgUrl = ! empty($row['file_id']) ? base_url('file/' . (int) $row['file_id']) : null;
$thumbImgUrl = ! empty($row['thumbnail_file_id']) ? base_url('file/' . (int) $row['thumbnail_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Media & Galeri') ?></h6>
        <p class="text-secondary-light mb-0">Kelola publikasi materi visual, infografik, video edukasi, dan dokumentasi kegiatan PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Form Utama Konten Visual -->
        <div class="col-xl-8 col-lg-7">
            <!-- Informasi Pokok Media -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Informasi Konten Visual &amp; Media</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row g-3 mb-20">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Konten Visual <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $row['title'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Alur Pengadaan Barang Jasa Melalui E-Katalog Versi 6">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Format Media <span class="text-danger">*</span></label>
                            <select name="type" id="mediaTypeSelect" class="form-select radius-8" required onchange="handleMediaTypeChange(this.value)">
                                <?php foreach ($typeOptions as $k => $l): ?>
                                    <option value="<?= $k ?>" <?= $currentType === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- URL Eksternal / Video YouTube -->
                    <div class="mb-20" id="externalUrlContainer">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            <span id="externalUrlLabel">Tautan Video YouTube / Link Eksternal</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-neutral-100 text-secondary-light">
                                <iconify-icon icon="solar:link-bold"></iconify-icon>
                            </span>
                            <input type="url" name="external_url" class="form-control radius-8" value="<?= esc(old('external_url', $row['external_url'] ?? '')) ?>" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <span class="text-xs text-secondary-light mt-1 d-block" id="externalUrlHelp">
                            Masukkan tautan penuh YouTube atau URL platform sumber media.
                        </span>
                    </div>

                    <!-- Caption & Narasi Sosial -->
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi / Keterangan Teks (Caption)</label>
                        <textarea name="caption" class="form-control radius-8" rows="4" placeholder="Tuliskan keterangan lengkap, pengantar infografik, atau rangkuman video..."><?= esc(old('caption', $row['caption'] ?? '')) ?></textarea>
                    </div>

                    <!-- Alt Text Aksesibilitas -->
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Teks Alternatif (Alt Text Aksesibilitas &amp; SEO)</label>
                        <input type="text" name="alt_text" class="form-control radius-8" value="<?= esc(old('alt_text', $row['alt_text'] ?? '')) ?>" maxlength="500" placeholder="Deskripsikan gambar secara ringkas untuk pembaca layar">
                    </div>

                    <!-- Spesifik Galeri & Video Parameters -->
                    <div class="row g-3">
                        <div class="col-md-6" id="locationContainer">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lokasi Kegiatan (Khusus Galeri)</label>
                            <input type="text" name="location" class="form-control radius-8" value="<?= esc(old('location', $row['location'] ?? '')) ?>" placeholder="Contoh: Gedung Pusiban Kantor Gubernur Lampung">
                        </div>
                        <div class="col-md-3" id="eventDateContainer">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Kegiatan</label>
                            <input type="date" name="event_date" class="form-control radius-8" value="<?= esc(old('event_date', $row['event_date'] ?? '')) ?>">
                        </div>
                        <div class="col-md-3" id="durationContainer">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Durasi Video</label>
                            <input type="text" name="duration" class="form-control radius-8" value="<?= esc(old('duration', $row['duration'] ?? '')) ?>" placeholder="Contoh: 15:30">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interaksi Sosial Media & Tagar -->
            <div class="card border-0 radius-16 shadow-sm bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:heart-bold" class="text-danger-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Pengaturan Interaksi Sosial Media &amp; Tagar</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tagar Sosial Media (Hashtags)</label>
                        <input type="text" name="hashtags" class="form-control radius-8" value="<?= esc(old('hashtags', $row['hashtags'] ?? '#PBJLampung #PengadaanBarangJasa')) ?>" placeholder="#PBJLampung #PengadaanBarangJasa #InfografikPBJ">
                        <span class="text-xs text-secondary-light mt-1 d-block">Pisahkan tagar dengan spasi atau tanda pagar (#). Tagar ini akan ditampilkan di katalog feed visual.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Suka (Likes)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-danger"><i class="fas fa-heart"></i></span>
                                <input type="number" name="likes_count" min="0" class="form-control radius-8" value="<?= (int) old('likes_count', $row['likes_count'] ?? 24) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Dilihat (Views)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-primary"><i class="fas fa-eye"></i></span>
                                <input type="number" name="views_count" min="0" class="form-control radius-8" value="<?= (int) old('views_count', $row['views_count'] ?? 150) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Dibagikan (Shares)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-info"><i class="fas fa-share-alt"></i></span>
                                <input type="number" name="shares_count" min="0" class="form-control radius-8" value="<?= (int) old('shares_count', $row['shares_count'] ?? 12) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Upload File Media & Rekomendasi Ukuran -->
        <div class="col-xl-4 col-lg-5">
            <!-- Box Upload Media Utama -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:upload-track-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Unggah File Media Gambar / Video</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Dynamic Recommendation Banner -->
                    <div class="alert alert-info bg-info-50 border-info-200 radius-12 p-16 mb-16" id="sizeRecommendationAlert">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-info-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-info-900 mb-1" id="recTitle">Rekomendasi Format Infografis:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light" id="recList">
                                    <li>Ukuran Rekomendasi: <strong class="text-info-800">1080 x 1080 px (1:1 Kotak)</strong> atau <strong class="text-info-800">1080 x 1350 px (4:5 Sosmed)</strong></li>
                                    <li>Format: <strong>JPG, PNG, WEBP</strong></li>
                                    <li>Maksimal Ukuran File: <strong>5 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Media Utama -->
                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 180px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="mediaFilePreview" src="<?= esc($fileImgUrl ?? '') ?>" alt="Preview Media" class="w-100 radius-8 object-fit-contain <?= empty($fileImgUrl) ? 'd-none' : '' ?>" style="max-height: 220px;">
                        <div id="mediaFilePlaceholder" class="<?= ! empty($fileImgUrl) ? 'd-none' : '' ?> text-secondary-light py-24">
                            <iconify-icon icon="solar:gallery-broken" class="text-5xl mb-2 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada file media dipilih</span>
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Media Utama</label>
                        <input type="file" name="file" id="mediaFileInput" class="form-control radius-8" accept="image/*,video/mp4" onchange="previewImageFile(this, 'mediaFilePreview', 'mediaFilePlaceholder')">
                        <?php if (! empty($row['file_id'])): ?>
                            <span class="text-xs text-success mt-1 d-block">
                                <i class="fas fa-check-circle me-1"></i> File aktif: Asset #<?= (int) $row['file_id'] ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnail Khusus (Opsional jika ingin custom) -->
                    <div class="pt-16 border-top">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Thumbnail Custom (Opsional)
                        </label>
                        <span class="text-xs text-secondary-light d-block mb-12">Rekomendasi Thumbnail: <strong>600 x 600 px (1:1)</strong> atau <strong>1280 x 720 px (16:9)</strong></span>

                        <?php if (! empty($thumbImgUrl)): ?>
                            <div class="mb-12">
                                <img src="<?= esc($thumbImgUrl) ?>" alt="Thumb" class="w-48-px h-48-px radius-8 object-fit-cover border">
                            </div>
                        <?php endif; ?>

                        <input type="file" name="thumbnail" class="form-control radius-8" accept="image/jpeg,image/png,image/webp">
                    </div>
                </div>
            </div>

            <!-- Pengaturan Status & Urutan -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status &amp; Penataan</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Alur Kerja <span class="text-danger">*</span></label>
                        <select name="status" class="form-select radius-8" required>
                            <?php foreach ($statusOptions as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Akses Visibilitas</label>
                        <select name="visibility" class="form-select radius-8">
                            <?php foreach ($visibilityOptions as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $currentVis === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Prioritas</label>
                        <input type="number" min="0" name="sort_order" class="form-control radius-8" value="<?= (int) old('sort_order', $row['sort_order'] ?? 0) ?>" placeholder="0 = Prioritas teratas">
                    </div>

                    <div class="p-16 border radius-12 bg-neutral-50">
                        <div class="form-check form-switch mb-12">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($row['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="featured">
                                <i class="fas fa-star text-warning me-1"></i> Konten Visual Unggulan
                            </label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($row['carousel_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="carousel_enabled">
                                Tampilkan di Carousel Beranda
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Media' : 'Publikasikan Media Baru' ?></span>
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.media-gallery') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImageFile(input, imgElementId, placeholderId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgElementId);
            const ph = document.getElementById(placeholderId);
            if (img) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            if (ph) {
                ph.classList.add('d-none');
            }
        };
        reader.readAsDataURL(file);
    }
}

function handleMediaTypeChange(type) {
    const alertBox = document.getElementById('sizeRecommendationAlert');
    const recTitle = document.getElementById('recTitle');
    const recList  = document.getElementById('recList');

    if (type === 'youtube' || type === 'video_file') {
        alertBox.className = 'alert alert-danger bg-danger-50 border-danger-200 radius-12 p-16 mb-16';
        recTitle.textContent = 'Rekomendasi Video & Banner:';
        recList.innerHTML = `
            <li>Rasio Standar: <strong class="text-danger-800">16:9 Widescreen HD</strong></li>
            <li>Resolusi Thumbnail: <strong class="text-danger-800">1280 x 720 px</strong></li>
            <li>Format Banner: <strong>JPG, PNG, WEBP</strong> (Maks 3 MB)</li>
        `;
    } else if (type === 'gallery') {
        alertBox.className = 'alert alert-dark bg-dark bg-opacity-10 border-dark border-opacity-25 radius-12 p-16 mb-16';
        recTitle.textContent = 'Rekomendasi Foto Galeri Kegiatan:';
        recList.innerHTML = `
            <li>Resolusi Optimal: <strong class="text-dark">1200 x 800 px (3:2)</strong> atau <strong class="text-dark">1080 x 1080 px (1:1)</strong></li>
            <li>Format: <strong>JPG, PNG, WEBP</strong></li>
            <li>Maksimal Ukuran File: <strong>8 MB</strong></li>
        `;
    } else {
        alertBox.className = 'alert alert-info bg-info-50 border-info-200 radius-12 p-16 mb-16';
        recTitle.textContent = 'Rekomendasi Format Infografis:';
        recList.innerHTML = `
            <li>Ukuran Rekomendasi: <strong class="text-info-800">1080 x 1080 px (1:1 Persegi)</strong> atau <strong class="text-info-800">1080 x 1350 px (4:5 Sosmed)</strong></li>
            <li>Format: <strong>JPG, PNG, WEBP</strong></li>
            <li>Maksimal Ukuran File: <strong>5 MB</strong></li>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('mediaTypeSelect');
    if (typeSelect) {
        handleMediaTypeChange(typeSelect.value);
    }
});
</script>
<?= $this->endSection() ?>