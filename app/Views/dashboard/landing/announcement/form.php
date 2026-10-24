<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($announcement);
$actionUrl = $isEdit ? site_url('dashboard/landing/pengumuman/ubah/' . $announcement['id']) : site_url('dashboard/landing/pengumuman/simpan');
$pageHeader = $isEdit ? 'Ubah Slide Pengumuman Popup' : 'Tambah Slide Pengumuman Popup';
$fallbackImg = base_url('assets/main/images/event/event-details-top-img-1.jpg');
$curImage = !empty($announcement['image_url']) ? (str_starts_with($announcement['image_url'], 'http') ? $announcement['image_url'] : base_url($announcement['image_url'])) : $fallbackImg;
?>

<!-- Breadcrumbs Header (WowDash Standard) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1 text-primary-light"><?= esc($pageHeader) ?></h6>
        <p class="text-secondary-light mb-0 text-sm">Kelola banner promosi, badge kategori pill, teks pengumuman, dan tombol navigasi tujuan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/pengumuman') ?>" class="btn btn-outline-neutral-300 radius-8 px-18 py-10 d-flex align-items-center gap-2 text-secondary-light hover-text-primary">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'promo', 'activeSub' => 'pengumuman']) ?>

<!-- Validation Error Alert -->
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger radius-8 mb-24 py-12 px-16 d-flex align-items-center gap-2 text-sm fw-medium border-danger-200">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl flex-shrink-0"></iconify-icon>
        <div>
            <span class="fw-bold">Harap periksa kembali isian formulir:</span>
            <ul class="mb-0 ps-16 mt-4">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- Split Layout: Form (Left) & Real-time Visualizer (Right) -->
<div class="row gy-4">
    
    <!-- LEFT COLUMN: Main Form -->
    <div class="col-xl-7 col-lg-7">
        <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:pen-new-square-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-md fw-semibold text-primary-light mb-0"><?= $isEdit ? 'Form Perubahan Slide' : 'Form Pembuatan Slide Baru' ?></h6>
                </div>
                <span class="badge bg-primary-50 text-primary-600 text-xs radius-4 px-10 py-4 fw-medium">
                    <?= $isEdit ? 'Mode Edit #' . (int) $announcement['id'] : 'Slide Baru' ?>
                </span>
            </div>
            <div class="card-body p-24">
                <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="announcementForm">
                    <?= csrf_field() ?>
                    <div class="row gy-20">
                        
                        <!-- 1. Judul Pengumuman -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="titleInput">
                                Judul Pengumuman <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="titleInput" class="form-control radius-8"
                                   value="<?= esc(old('title', $announcement['title'] ?? '')) ?>"
                                   placeholder="Contoh: Bimbingan Teknis & Uji Sertifikasi PBJ Terpadu 2026" required>
                            <small class="text-secondary-light text-2xs mt-4 d-block">Judul utama yang langsung dibaca oleh pengunjung saat modal muncul.</small>
                        </div>

                        <!-- 2. Label Kategori Pill -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="badgeLabelInput">
                                Label Kategori Pill (Badge Kuning)
                            </label>
                            <input type="text" name="badge_label" id="badgeLabelInput" class="form-control radius-8"
                                   value="<?= esc(old('badge_label', $announcement['badge_label'] ?? 'AGENDA & BIMTEK 2026')) ?>"
                                   placeholder="AGENDA & BIMTEK 2026">
                            <!-- Preset Badge Chips -->
                            <div class="d-flex flex-wrap align-items-center gap-1 mt-6">
                                <span class="text-2xs text-secondary-light me-1">Pilihan Cepat:</span>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-8 form-preset-badge" data-text="AGENDA &amp; BIMTEK 2026">Agenda &amp; Bimtek</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-8 form-preset-badge" data-text="PENGUMUMAN RESMI">Pengumuman Resmi</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-8 form-preset-badge" data-text="INFO TENDER STRATEGIS">Tender Strategis</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-8 form-preset-badge" data-text="SERTIFIKASI LEVEL 1">Sertifikasi Level 1</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-8 form-preset-badge" data-text="UPDATE REGULASI LKPP">Regulasi LKPP</button>
                            </div>
                        </div>

                        <!-- 3. Deskripsi Pengumuman -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="descInput">
                                Uraian / Deskripsi Lengkap Pengumuman
                            </label>
                            <textarea name="description" id="descInput" class="form-control radius-8" rows="3"
                                      placeholder="Uraikan detail pengumuman yang ingin disampaikan secara ringkas, jelas, dan memikat"><?= esc(old('description', $announcement['description'] ?? '')) ?></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <small class="text-secondary-light text-2xs">Disarankan 1-3 kalimat informatif (maks. 300 karakter).</small>
                                <small class="text-secondary-light text-2xs" id="descCharCount">0 karakter</small>
                            </div>
                        </div>

                        <!-- 4. Upload Banner Media -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                <iconify-icon icon="solar:gallery-wide-bold" class="text-primary-600 me-1"></iconify-icon>
                                Banner Media Gambar Popup
                            </label>

                            <!-- Modern Dropzone Box -->
                            <div class="border-2 border-dashed border-neutral-300 radius-12 p-20 text-center bg-neutral-50 hover-bg-neutral-100 transition-2 position-relative cursor-pointer" id="dropzoneBox">
                                <input type="file" name="image_file" id="imageFileInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="d-flex flex-column align-items-center justify-content-center pointer-events-none">
                                    <div class="w-48-px h-48-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center mb-8">
                                        <iconify-icon icon="solar:upload-track-2-bold" class="text-2xl"></iconify-icon>
                                    </div>
                                    <h6 class="text-sm fw-semibold text-primary-light mb-2">Tarik &amp; lepas file ke sini, atau klik untuk memilih</h6>
                                    <p class="text-secondary-light text-xs mb-0">Format: JPG, JPEG, PNG, WEBP. Rekomendasi: <strong>800 &times; 350 px</strong> (maks. 5 MB).</p>
                                </div>
                            </div>

                            <!-- Preset Banners Quick Selector -->
                            <div class="mt-12">
                                <label class="text-2xs fw-semibold text-secondary-light mb-6 d-block">Atau Pilih Gambar Preset Bawaan:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white radius-8 p-6 d-flex align-items-center gap-2 form-preset-banner" data-src="assets/main/images/event/event-details-top-img-1.jpg">
                                        <img src="<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>" class="w-32-px h-20-px object-fit-cover radius-4">
                                        <span class="text-2xs fw-medium text-primary-light">Bimtek PBJ</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white radius-8 p-6 d-flex align-items-center gap-2 form-preset-banner" data-src="assets/main/images/slider/banner-1.jpg">
                                        <img src="<?= base_url('assets/main/images/slider/banner-1.jpg') ?>" class="w-32-px h-20-px object-fit-cover radius-4" onerror="this.style.display='none'">
                                        <span class="text-2xs fw-medium text-primary-light">Gedung Lampung</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white radius-8 p-6 d-flex align-items-center gap-2 form-preset-banner" data-src="assets/main/images/event/event-grid-img-1.jpg">
                                        <img src="<?= base_url('assets/main/images/event/event-grid-img-1.jpg') ?>" class="w-32-px h-20-px object-fit-cover radius-4" onerror="this.style.display='none'">
                                        <span class="text-2xs fw-medium text-primary-light">Webinar Virtual</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white radius-8 p-6 d-flex align-items-center gap-2 form-preset-banner" data-src="assets/main/images/event/event-grid-img-2.jpg">
                                        <img src="<?= base_url('assets/main/images/event/event-grid-img-2.jpg') ?>" class="w-32-px h-20-px object-fit-cover radius-4" onerror="this.style.display='none'">
                                        <span class="text-2xs fw-medium text-primary-light">Workshop LKPP</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Path Alternatif Input (Collapsible) -->
                            <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                                <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseAnnounceUrl" role="button" aria-expanded="false">
                                    <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                    <span>Gunakan Path Gambar Statis / URL Eksternal</span>
                                    <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                                </a>
                                <div class="collapse mt-8" id="collapseAnnounceUrl">
                                    <input type="text" name="image_url" id="imageUrlInput" class="form-control radius-8 text-sm"
                                           value="<?= esc(old('image_url', $announcement['image_url'] ?? 'assets/main/images/event/event-details-top-img-1.jpg')) ?>"
                                           placeholder="assets/main/images/event/event-details-top-img-1.jpg">
                                    <small class="text-secondary-light text-2xs mt-2 d-block">Otomatis terisi jika mengunggah file baru di atas.</small>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Masa Berlaku Penjadwalan (Start Date & End Date) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="startDateInput">
                                Tanggal Mulai Tayang
                            </label>
                            <input type="date" name="start_date" id="startDateInput" class="form-control radius-8"
                                   value="<?= esc(old('start_date', $announcement['start_date'] ?? '')) ?>">
                            <small class="text-secondary-light text-2xs mt-2 d-block">Kosongkan jika aktif sejak sekarang.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="endDateInput">
                                Tanggal Berakhir Tayang
                            </label>
                            <input type="date" name="end_date" id="endDateInput" class="form-control radius-8"
                                   value="<?= esc(old('end_date', $announcement['end_date'] ?? '')) ?>">
                            <small class="text-secondary-light text-2xs mt-2 d-block">Kosongkan jika berlaku tanpa batas waktu.</small>
                        </div>

                        <!-- 6. Urutan & Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="sortOrderInput">
                                Urutan Slide (Nomor)
                            </label>
                            <input type="number" name="sort_order" id="sortOrderInput" class="form-control radius-8"
                                   value="<?= esc(old('sort_order', $announcement['sort_order'] ?? 1)) ?>" min="1" max="99">
                            <small class="text-secondary-light text-2xs mt-2 d-block">Urutan 1 tampil paling awal di carousel.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="isActiveSelect">
                                Status Penayangan
                            </label>
                            <select name="is_active" id="isActiveSelect" class="form-select radius-8">
                                <option value="1" <?= old('is_active', $announcement['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Tayang di Modal Beranda)</option>
                                <option value="0" <?= old('is_active', $announcement['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Simpan sebagai Draf)</option>
                            </select>
                        </div>

                        <!-- 7. Tombol Aksi (CTA) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="ctaLabelInput">
                                Label Tombol Aksi (CTA)
                            </label>
                            <input type="text" name="cta_label" id="ctaLabelInput" class="form-control radius-8"
                                   value="<?= esc(old('cta_label', $announcement['cta_label'] ?? 'Lihat Jadwal Agenda')) ?>"
                                   placeholder="Lihat Jadwal Agenda">
                            <!-- Preset CTA Chips -->
                            <div class="d-flex flex-wrap gap-1 mt-4">
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 form-preset-cta" data-label="Lihat Jadwal Agenda" data-url="event">Agenda</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 form-preset-cta" data-label="Daftar Pelatihan" data-url="arena-belajar">Pelatihan</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 form-preset-cta" data-label="Pelajari Regulasi" data-url="regulasi">Regulasi</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 form-preset-cta" data-label="Baca Selengkapnya" data-url="berita">Berita</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="ctaUrlInput">
                                URL Tujuan Tombol Aksi
                            </label>
                            <input type="text" name="cta_url" id="ctaUrlInput" class="form-control radius-8"
                                   value="<?= esc(old('cta_url', $announcement['cta_url'] ?? 'event')) ?>"
                                   placeholder="event atau https://...">
                            <small class="text-secondary-light text-2xs mt-2 d-block">Tautan internal (misal: <code>event</code>, <code>regulasi</code>) atau URL eksternal.</small>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 d-flex flex-wrap justify-content-end gap-12 pt-16 border-top mt-16">
                            <a href="<?= site_url('dashboard/landing/pengumuman') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 text-sm fw-medium">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 text-sm fw-semibold d-inline-flex align-items-center gap-2">
                                <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                                <span><?= $isEdit ? 'Perbarui Slide Pengumuman' : 'Simpan Slide Pengumuman' ?></span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Real-Time Interactive Live Visualizer -->
    <div class="col-xl-5 col-lg-5">
        <div class="card position-sticky" style="top: 24px;">
            <div class="card-header border-bottom bg-base py-16 px-20 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:eye-bold" class="text-info-600 text-xl"></iconify-icon>
                    <h6 class="text-md fw-semibold text-primary-light mb-0">Live Pratinjau Modal Popup</h6>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-neutral-300 active preview-device-btn" data-device="desktop" title="Desktop View">
                        <iconify-icon icon="solar:laptop-bold"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-neutral-300 preview-device-btn" data-device="mobile" title="Mobile View">
                        <iconify-icon icon="solar:smartphone-bold"></iconify-icon>
                    </button>
                </div>
            </div>
            
            <div class="card-body p-20 bg-neutral-100 text-center" id="visualizerWrapper">
                <div class="mx-auto radius-16 overflow-hidden shadow-2xl border border-neutral-300 text-start bg-white transition-2" style="max-width: 100%;" id="visualizerCard">
                    
                    <!-- Live Header Bar -->
                    <div class="p-12 px-16 border-bottom d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="w-8-px h-8-px rounded-circle bg-success-main animate-pulse"></span>
                            <span class="text-2xs fw-bold text-white text-uppercase tracking-wider">
                                <?= esc(setting('announcement.popup_badge', 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG')) ?>
                            </span>
                        </div>
                        <span class="text-white opacity-60">
                            <iconify-icon icon="solar:close-circle-bold" class="text-base"></iconify-icon>
                        </span>
                    </div>

                    <!-- Live Media Banner -->
                    <div class="position-relative overflow-hidden bg-dark" style="height: 180px;">
                        <img id="liveBannerImg" src="<?= esc($curImage) ?>" class="w-100 h-100 object-fit-cover" alt="Banner Preview" onerror="this.src='<?= $fallbackImg ?>'">
                        <div class="position-absolute bottom-0 start-0 w-100 h-50" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.65) 100%);"></div>
                        <span id="liveCategoryBadge" class="position-absolute top-0 start-0 m-12 badge bg-warning-main text-white text-2xs px-10 py-4 radius-4 fw-bold">
                            <?= esc($announcement['badge_label'] ?? 'AGENDA & BIMTEK 2026') ?>
                        </span>
                    </div>

                    <!-- Live Body Content -->
                    <div class="p-16">
                        <h5 id="liveTitle" class="text-md fw-bold text-primary-light mb-6">
                            <?= esc($announcement['title'] ?? 'Judul Pengumuman Preview') ?>
                        </h5>
                        <p id="liveDesc" class="text-xs text-secondary-light mb-16 line-clamp-3">
                            <?= esc($announcement['description'] ?? 'Uraian pengumuman yang menginformasikan pesan prioritas kepada pengunjung.') ?>
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-10 border-top">
                            <button type="button" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-2 pointer-events-none">
                                <iconify-icon icon="solar:calendar-check-bold" class="text-sm"></iconify-icon>
                                <span id="liveCtaLabel"><?= esc($announcement['cta_label'] ?? 'Lihat Jadwal Agenda') ?></span>
                            </button>
                            <button type="button" class="btn btn-neutral-200 text-secondary-light radius-8 px-12 py-8 text-xs fw-semibold pointer-events-none">
                                Tutup
                            </button>
                        </div>
                    </div>

                    <!-- Live Footer Status Bar -->
                    <div class="p-8 px-16 bg-neutral-50 border-top d-flex align-items-center justify-content-between text-2xs text-secondary-light">
                        <span id="liveScheduleIndicator">
                            <?php if (!empty($announcement['start_date']) || !empty($announcement['end_date'])): ?>
                                <?= !empty($announcement['start_date']) ? date('d M Y', strtotime($announcement['start_date'])) : 'Awal' ?> -
                                <?= !empty($announcement['end_date']) ? date('d M Y', strtotime($announcement['end_date'])) : 'Seterusnya' ?>
                            <?php else: ?>
                                Tayang Tanpa Batas Waktu
                            <?php endif; ?>
                        </span>
                        <span class="badge radius-4 px-6 py-2 <?= (!isset($announcement['is_active']) || $announcement['is_active'] == 1) ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-secondary-light' ?>" id="liveStatusBadge">
                            <?= (!isset($announcement['is_active']) || $announcement['is_active'] == 1) ? 'Status: Aktif' : 'Status: Draf' ?>
                        </span>
                    </div>

                </div>
            </div>
            <div class="card-footer py-12 px-20 border-top bg-base">
                <p class="text-secondary-light text-2xs mb-0 text-center">
                    <iconify-icon icon="solar:info-circle-linear" class="me-1 text-primary-600"></iconify-icon>
                    Tampilan ini adalah simulasi langsung dialog modal popup pada beranda utama.
                </p>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------------------
    // 1. Real-time Visualizer Binding
    // -------------------------------------------------------------------------
    const titleInput      = document.getElementById('titleInput');
    const badgeLabelInput = document.getElementById('badgeLabelInput');
    const descInput       = document.getElementById('descInput');
    const ctaLabelInput   = document.getElementById('ctaLabelInput');
    const ctaUrlInput     = document.getElementById('ctaUrlInput');
    const startDateInput  = document.getElementById('startDateInput');
    const endDateInput    = document.getElementById('endDateInput');
    const isActiveSelect  = document.getElementById('isActiveSelect');
    const imageUrlInput   = document.getElementById('imageUrlInput');
    const imageFileInput  = document.getElementById('imageFileInput');

    const liveTitle       = document.getElementById('liveTitle');
    const liveCategoryBadge = document.getElementById('liveCategoryBadge');
    const liveDesc        = document.getElementById('liveDesc');
    const liveCtaLabel    = document.getElementById('liveCtaLabel');
    const liveBannerImg   = document.getElementById('liveBannerImg');
    const liveScheduleIndicator = document.getElementById('liveScheduleIndicator');
    const liveStatusBadge = document.getElementById('liveStatusBadge');
    const descCharCount   = document.getElementById('descCharCount');

    function updateLiveVisualizer() {
        if (titleInput && liveTitle) {
            liveTitle.textContent = titleInput.value.trim() || 'Judul Pengumuman Preview';
        }
        if (badgeLabelInput && liveCategoryBadge) {
            liveCategoryBadge.textContent = badgeLabelInput.value.trim() || 'PENGUMUMAN';
        }
        if (descInput && liveDesc) {
            const val = descInput.value.trim();
            liveDesc.textContent = val || 'Uraian pengumuman yang menginformasikan pesan prioritas kepada pengunjung.';
            if (descCharCount) descCharCount.textContent = `${val.length} karakter`;
        }
        if (ctaLabelInput && liveCtaLabel) {
            liveCtaLabel.textContent = ctaLabelInput.value.trim() || 'Lihat Detail';
        }
        if (isActiveSelect && liveStatusBadge) {
            const isAct = isActiveSelect.value === '1';
            liveStatusBadge.className = `badge radius-4 px-6 py-2 ${isAct ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-secondary-light'}`;
            liveStatusBadge.textContent = isAct ? 'Status: Aktif' : 'Status: Draf';
        }
        if (liveScheduleIndicator && startDateInput && endDateInput) {
            const s = startDateInput.value;
            const e = endDateInput.value;
            if (s || e) {
                liveScheduleIndicator.textContent = `${s ? s : 'Awal'} s.d. ${e ? e : 'Seterusnya'}`;
            } else {
                liveScheduleIndicator.textContent = 'Tayang Tanpa Batas Waktu';
            }
        }
    }

    [titleInput, badgeLabelInput, descInput, ctaLabelInput, startDateInput, endDateInput, isActiveSelect].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLiveVisualizer);
            el.addEventListener('change', updateLiveVisualizer);
        }
    });

    updateLiveVisualizer();

    // -------------------------------------------------------------------------
    // 2. Preset Badge Chips & Preset CTA Chips
    // -------------------------------------------------------------------------
    document.querySelectorAll('.form-preset-badge').forEach(btn => {
        btn.addEventListener('click', function () {
            if (badgeLabelInput) {
                badgeLabelInput.value = this.dataset.text;
                updateLiveVisualizer();
            }
        });
    });

    document.querySelectorAll('.form-preset-cta').forEach(btn => {
        btn.addEventListener('click', function () {
            if (ctaLabelInput) ctaLabelInput.value = this.dataset.label;
            if (ctaUrlInput) ctaUrlInput.value = this.dataset.url;
            updateLiveVisualizer();
        });
    });

    // -------------------------------------------------------------------------
    // 3. Preset Banner Image Click
    // -------------------------------------------------------------------------
    document.querySelectorAll('.form-preset-banner').forEach(btn => {
        btn.addEventListener('click', function () {
            const src = this.dataset.src;
            if (imageUrlInput) imageUrlInput.value = src;
            if (liveBannerImg) liveBannerImg.src = `<?= base_url() ?>/${src}`;
            if (imageFileInput) imageFileInput.value = '';
        });
    });

    // -------------------------------------------------------------------------
    // 4. File Dropzone & Instant File Preview
    // -------------------------------------------------------------------------
    if (imageFileInput) {
        imageFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (liveBannerImg) liveBannerImg.src = e.target.result;
                    if (imageUrlInput) imageUrlInput.value = file.name;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (imageUrlInput) {
        imageUrlInput.addEventListener('input', function () {
            const val = this.value.trim();
            if (val && liveBannerImg) {
                liveBannerImg.src = val.startsWith('http') ? val : `<?= base_url() ?>/${val}`;
            }
        });
    }

    // -------------------------------------------------------------------------
    // 5. Device Toggle for Visualizer (Desktop vs Mobile)
    // -------------------------------------------------------------------------
    document.querySelectorAll('.preview-device-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.preview-device-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const device = this.dataset.device;
            const card = document.getElementById('visualizerCard');
            if (device === 'mobile') {
                card.style.maxWidth = '320px';
            } else {
                card.style.maxWidth = '100%';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
