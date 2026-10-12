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
    'public' => 'Publik (Dapat diakses umum)', 'internal' => 'Internal (Hanya pengguna login)', 'restricted' => 'Terbatas (Hak akses khusus)',
];
$action = $isEdit ? route_to('dashboard.knowledge.articles.edit', (string) ($articleId ?? '')) : route_to('dashboard.knowledge.articles.create');
$tagsCsv = implode(', ', $selectedTags);
$currentStatus = $article['workflow_status'] ?? 'draft';
$currentVisibility = $article['visibility'] ?? 'public';
$coverImgUrl = ! empty($article['cover_file_id']) ? base_url('file/' . (int) $article['cover_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Artikel PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan publikasikan artikel edukasi, praktik baik, dan wawasan pengadaan barang/jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Konten Utama -->
        <div class="col-xl-8 col-lg-7">
            <!-- Informasi Utama -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Konten Artikel</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $article['title'] ?? '')) ?>" required maxlength="255" placeholder="Contoh: Strategi Efektif Penyusunan HPS Sesuai Perpres 12/2021">
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan Eksekutif</label>
                        <textarea name="summary" class="form-control radius-8" rows="3" placeholder="Tuliskan 2-3 kalimat ringkasan penting untuk ditampilkan di kartu landing page..."><?= esc(old('summary', $article['summary'] ?? '')) ?></textarea>
                        <span class="text-xs text-secondary-light mt-1 d-block">Ringkasan akan muncul pada cuplikan beranda dan meta deskripsi pencarian.</span>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Isi Lengkap Artikel <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control radius-8" rows="14" placeholder="Tuliskan konten artikel lengkap di sini..." required><?= esc(old('content', $article['content'] ?? '')) ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Estimasi Waktu Baca (Menit)</label>
                            <div class="input-group">
                                <input type="number" name="reading_time" class="form-control radius-8" min="1" max="120" value="<?= esc(old('reading_time', $article['reading_time'] ?? 5)) ?>">
                                <span class="input-group-text bg-neutral-100 text-secondary-light">Menit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Sumber / Tautan Referensi</label>
                            <input type="url" name="source_url" class="form-control radius-8" value="<?= esc(old('source_url', $article['source_url'] ?? '')) ?>" placeholder="https://lkpp.go.id/...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Taksonomi & Tagar -->
            <div class="card border-0 radius-16 shadow-sm bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:tag-bold" class="text-warning-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Taksonomi &amp; Kategori</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Artikel</label>
                        <select name="categories[]" class="form-select radius-8" multiple size="4">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= in_array((int) $c['id'], array_map('intval', $selectedCats), true) ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-1 d-block">Pilih satu atau lebih pilar kategori (tahan tombol Ctrl/Cmd untuk multi-pilih).</span>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tagar &amp; Kata Kunci</label>
                        <input type="text" name="tags" class="form-control radius-8" value="<?= esc($tagsCsv) ?>" placeholder="Contoh: HPS, Pengadaan Barang, LKPP, E-Katalog">
                        <span class="text-xs text-secondary-light mt-1 d-block">Pisahkan tag dengan tanda koma. Tag baru akan otomatis dibuat jika belum ada.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Cover Upload, Workflow & Pengaturan -->
        <div class="col-xl-4 col-lg-5">
            <!-- Upload Cover Banner -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:camera-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Cover Banner Artikel</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Rekomendasi Ukuran Visual Alert -->
                    <div class="alert alert-primary bg-primary-50 border-primary-200 radius-12 p-16 mb-16">
                        <div class="d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs">
                                <strong class="d-block text-primary-900 mb-1">Rekomendasi Format Cover:</strong>
                                <ul class="mb-0 ps-16 text-secondary-light">
                                    <li>Ukuran Resolusi: <strong class="text-primary-700">1200 x 630 px</strong> (Rasio 16:9 / Landscape)</li>
                                    <li>Format: <strong>JPG, PNG, WEBP</strong></li>
                                    <li>Maksimal Ukuran File: <strong>3 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div class="border radius-12 p-12 text-center bg-neutral-50 mb-16 position-relative" style="min-height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="articleCoverPreview" src="<?= esc($coverImgUrl ?? '') ?>" alt="Preview Cover" class="w-100 radius-8 object-fit-cover <?= empty($coverImgUrl) ? 'd-none' : '' ?>" style="max-height: 200px;">
                        <div id="articleCoverPlaceholder" class="<?= ! empty($coverImgUrl) ? 'd-none' : '' ?> text-secondary-light py-20">
                            <iconify-icon icon="solar:gallery-broken" class="text-4xl mb-1 text-neutral-400"></iconify-icon>
                            <span class="d-block text-xs">Belum ada gambar cover dipilih</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas Gambar</label>
                        <input type="file" name="cover" id="articleCoverInput" class="form-control radius-8" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewImageFile(this, 'articleCoverPreview', 'articleCoverPlaceholder')">
                        <?php if (! empty($article['cover_file_id'])): ?>
                            <span class="text-xs text-success mt-1 d-block">
                                <i class="fas fa-check-circle me-1"></i> File cover aktif: Asset #<?= (int) $article['cover_file_id'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Status & Workflow -->
            <div class="card border-0 radius-16 shadow-sm mb-24 bg-white">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Status &amp; Publikasi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Alur Kerja <span class="text-danger">*</span></label>
                        <select name="status" class="form-select radius-8" required>
                            <?php foreach ($statusOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $currentStatus === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Akses Visibilitas</label>
                        <select name="visibility" class="form-select radius-8">
                            <?php foreach ($visibilityOptions as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $currentVisibility === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="p-16 border radius-12 bg-neutral-50 mb-16">
                        <div class="form-check form-switch mb-12">
                            <input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" <?= ! empty($article['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="featured">
                                <i class="fas fa-star text-warning me-1"></i> Konten Unggulan (Pilihan Editor)
                            </label>
                        </div>
                        <span class="text-xs text-secondary-light d-block mb-16">Artikel unggulan diprioritaskan tampil pada etalase utama Bunga Rampai.</span>

                        <div class="form-check form-switch mb-8">
                            <input class="form-check-input" type="checkbox" name="carousel_enabled" value="1" id="carousel_enabled" <?= ! empty($article['carousel_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm text-dark" for="carousel_enabled">
                                Tampilkan di Slider Banner Carousel
                            </label>
                        </div>
                        <div class="mt-8">
                            <label class="form-label text-xs fw-semibold text-secondary-light mb-4">Urutan Tampil Carousel:</label>
                            <input type="number" name="carousel_order" min="0" class="form-control radius-8 form-control-sm" value="<?= esc(old('carousel_order', $article['carousel_order'] ?? '')) ?>" placeholder="0 = Urutan teratas">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card border-0 radius-16 shadow-sm bg-white p-20">
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-12 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan Artikel' : 'Publikasikan Artikel Baru' ?></span>
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-center">
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
</script>
<?= $this->endSection() ?>