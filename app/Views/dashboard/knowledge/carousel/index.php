<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$articles = $articles ?? [];
$media = $media ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Carousel dan Konten Unggulan</h6>
        <p class="text-secondary-light mb-0">Kelola kurasi artikel dan materi media yang disorot pada slider banner utama portal Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.carousel.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon>
        <span>Tambah ke Carousel</span>
    </a>
</div>

<!-- Banner Rekomendasi Ukuran Carousel -->
<div class="alert alert-info bg-info-50 border-info-200 radius-12 p-16 mb-24">
    <div class="d-flex align-items-start gap-3">
        <iconify-icon icon="solar:gallery-wide-bold" class="text-info-600 text-2xl flex-shrink-0 mt-1"></iconify-icon>
        <div>
            <strong class="text-info-900 d-block mb-1">Panduan Ukuran Cover / Banner untuk Slider Carousel:</strong>
            <p class="text-xs text-info-800 mb-2">
                Konten yang dimasukkan ke dalam Carousel akan ditampilkan sebagai hero banner utama di halaman beranda Bunga Rampai. Pastikan konten memiliki cover gambar dengan spesifikasi berikut:
            </p>
            <div class="d-flex flex-wrap gap-2 text-xs">
                <span class="badge bg-white text-info-800 border border-info-300 px-10 py-6 radius-6">
                    <i class="fas fa-expand me-1"></i> Rekomendasi Banner: <strong>1920 x 600 px</strong> (Panoramic 3.2:1)
                </span>
                <span class="badge bg-white text-info-800 border border-info-300 px-10 py-6 radius-6">
                    <i class="fas fa-image me-1"></i> Alternatif Landscape: <strong>1200 x 630 px</strong> (16:9)
                </span>
                <span class="badge bg-white text-info-800 border border-info-300 px-10 py-6 radius-6">
                    <i class="fas fa-file-image me-1"></i> Format: <strong>WEBP, JPG, PNG</strong> (Maks 3 MB)
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Artikel di Carousel -->
<div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-xl"></iconify-icon>
            <h6 class="text-base fw-bold mb-0">Artikel PBJ di Carousel (<?= count($articles) ?>)</h6>
        </div>
        <span class="badge bg-primary-50 text-primary-600 border border-primary-100 px-12 py-6 radius-6 text-xs">
            Slider Artikel
        </span>
    </div>
    <div class="card-body p-24">
        <?php if (empty($articles)): ?>
            <div class="text-center py-32 text-secondary-light">
                <iconify-icon icon="solar:gallery-broken" class="text-5xl mb-2 text-neutral-400"></iconify-icon>
                <p class="mb-0 text-sm">Belum ada artikel yang diaktifkan untuk carousel slider.</p>
                <small class="d-block text-xs mt-1">Gunakan tombol "Tambah ke Carousel" untuk memilih artikel unggulan.</small>
            </div>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">Banner Cover</th>
                        <th>Judul Artikel</th>
                        <th style="width: 130px;">Tipe Konten</th>
                        <th style="width: 90px;" class="text-center">Urutan</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                        <?php 
                        $coverUrl = ! empty($a['cover_file_id']) ? base_url('file/' . (int) $a['cover_file_id']) : null;
                        ?>
                        <tr>
                            <td>
                                <?php if ($coverUrl): ?>
                                    <img src="<?= esc($coverUrl) ?>" alt="Cover" class="w-80-px h-48-px radius-8 object-fit-cover border shadow-xs">
                                <?php else: ?>
                                    <div class="w-80-px h-48-px radius-8 bg-neutral-100 border d-flex align-items-center justify-content-center text-secondary-light text-xs">
                                        <iconify-icon icon="solar:image-linear" class="text-lg"></iconify-icon>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= route_to('dashboard.knowledge.articles.detail', (string) $a['id']) ?>" class="fw-semibold text-dark hover-primary text-sm d-block mb-1">
                                    <?= esc($a['title']) ?>
                                </a>
                                <small class="text-secondary-light text-xs">ID #<?= (int) $a['id'] ?> &bull; Rekomendasi: 1920x600 px</small>
                            </td>
                            <td>
                                <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xs text-capitalize">
                                    <?= esc(str_replace('_', ' ', $a['type'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-50 text-primary-600 border border-primary-200 px-10 py-4 radius-6 fw-bold">
                                    <?= (int) $a['carousel_order'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-4 radius-6 text-xs text-capitalize">
                                    <?= esc($a['workflow_status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="<?= route_to('dashboard.knowledge.articles.edit', (string) $a['id']) ?>" class="btn btn-outline-warning-600 btn-sm radius-8 p-6" title="Ganti Cover / Edit">
                                        <iconify-icon icon="solar:pen-linear" class="text-base"></iconify-icon>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 px-10 py-6 d-inline-flex align-items-center gap-1 text-xs"
                                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                        data-action="<?= route_to('dashboard.knowledge.carousel.disable-item', $a['type'], (string) $a['id']) ?>"
                                        data-method="POST"
                                        data-title="Nonaktifkan dari Carousel"
                                        data-message="Apakah Anda yakin ingin melepas artikel '<?= esc($a['title']) ?>' dari carousel utama?"
                                        data-btn-text="Lepas Carousel"
                                        data-btn-class="btn-danger-600"
                                        data-icon="solar:close-circle-linear"
                                        data-icon-color="danger">
                                        <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                                        <span>Lepas</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Daftar Media di Carousel -->
<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:gallery-bold" class="text-info-600 text-xl"></iconify-icon>
            <h6 class="text-base fw-bold mb-0">Konten Media &amp; Visual di Carousel (<?= count($media) ?>)</h6>
        </div>
        <span class="badge bg-info-50 text-info-700 border border-info-200 px-12 py-6 radius-6 text-xs">
            Slider Media
        </span>
    </div>
    <div class="card-body p-24">
        <?php if (empty($media)): ?>
            <div class="text-center py-32 text-secondary-light">
                <iconify-icon icon="solar:gallery-broken" class="text-5xl mb-2 text-neutral-400"></iconify-icon>
                <p class="mb-0 text-sm">Belum ada konten media atau infografik yang diaktifkan untuk carousel slider.</p>
                <small class="d-block text-xs mt-1">Gunakan tombol "Tambah ke Carousel" untuk memilih materi visual.</small>
            </div>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">Banner Cover</th>
                        <th>Judul Media</th>
                        <th style="width: 130px;">Tipe Format</th>
                        <th style="width: 90px;" class="text-center">Urutan</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($media as $m): ?>
                        <?php 
                        $mCover = ! empty($m['thumbnail_file_id']) 
                            ? base_url('file/' . (int) $m['thumbnail_file_id']) 
                            : (! empty($m['file_id']) ? base_url('file/' . (int) $m['file_id']) : null);
                        ?>
                        <tr>
                            <td>
                                <?php if ($mCover): ?>
                                    <img src="<?= esc($mCover) ?>" alt="Cover" class="w-80-px h-48-px radius-8 object-fit-cover border shadow-xs">
                                <?php else: ?>
                                    <div class="w-80-px h-48-px radius-8 bg-neutral-100 border d-flex align-items-center justify-content-center text-secondary-light text-xs">
                                        <iconify-icon icon="solar:gallery-linear" class="text-lg"></iconify-icon>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= route_to('dashboard.knowledge.media-gallery.detail', (string) $m['id']) ?>" class="fw-semibold text-dark hover-primary text-sm d-block mb-1">
                                    <?= esc($m['title'] ?: '(Tanpa judul)') ?>
                                </a>
                                <small class="text-secondary-light text-xs">ID #<?= (int) $m['id'] ?> &bull; Rekomendasi: 1920x600 px</small>
                            </td>
                            <td>
                                <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xs text-capitalize">
                                    <?= esc($m['type']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-4 radius-6 fw-bold">
                                    <?= (int) $m['sort_order'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-4 radius-6 text-xs text-capitalize">
                                    <?= esc($m['workflow_status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="<?= route_to('dashboard.knowledge.media-gallery.edit', (string) $m['id']) ?>" class="btn btn-outline-warning-600 btn-sm radius-8 p-6" title="Ganti Cover / Edit">
                                        <iconify-icon icon="solar:pen-linear" class="text-base"></iconify-icon>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 px-10 py-6 d-inline-flex align-items-center gap-1 text-xs"
                                        data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                        data-action="<?= route_to('dashboard.knowledge.carousel.disable-item', 'media', (string) $m['id']) ?>"
                                        data-method="POST"
                                        data-title="Nonaktifkan dari Carousel"
                                        data-message="Apakah Anda yakin ingin melepas media '<?= esc($m['title'] ?: 'Item #' . $m['id']) ?>' dari carousel utama?"
                                        data-btn-text="Lepas Carousel"
                                        data-btn-class="btn-danger-600"
                                        data-icon="solar:close-circle-linear"
                                        data-icon-color="danger">
                                        <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                                        <span>Lepas</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>