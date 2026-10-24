<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($slider);
$actionUrl = $isEdit ? site_url('dashboard/landing/slider/ubah/' . $slider['id']) : site_url('dashboard/landing/slider/simpan');
$currentTitle = old('title', $slider['title'] ?? '');
$currentSubtitle = old('subtitle', $slider['subtitle'] ?? '');
$currentBadge = old('badge_text', $slider['badge_text'] ?? 'Pemerintah Provinsi Lampung · Biro PBJ Setda');
$currentBtnText = old('button_text', $slider['button_text'] ?? 'Pusat Regulasi PBJ');
$currentBtnUrl = old('button_url', $slider['button_url'] ?? 'regulasi');
$currentSecBtnText = old('secondary_button_text', $slider['secondary_button_text'] ?? 'Arena Belajar');
$currentSecBtnUrl = old('secondary_button_url', $slider['secondary_button_url'] ?? 'arena-belajar');
$currentOrder = old('sort_order', $slider['sort_order'] ?? ($nextOrder ?? 1));
$currentActive = old('is_active', $slider['is_active'] ?? 1);
$currentImageUrl = old('image_url', $slider['image_url'] ?? 'assets/main/images/backgrounds/slider-1-1.png');
$curImgSrc = str_starts_with($currentImageUrl, 'http') ? $currentImageUrl : base_url($currentImageUrl);
?>

<!-- Header & Nav Back -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? ($isEdit ? 'Ubah Slide Hero' : 'Tambah Slide Hero')) ?></h6>
        <p class="text-secondary-light mb-0">Atur konten visual headline, badge instansi, tombol aksi (CTA), dan unggah gambar hero beranda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/slider') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="sliderHeroForm">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- LEFT COLUMN: Form Inputs -->
        <div class="col-xl-7 col-lg-6">
            <!-- 1. KONTEN TEKS & HEADLINE -->
            <div class="card border radius-12 shadow-none mb-24">
                <div class="card-header border-bottom py-16 px-24 bg-base d-flex align-items-center justify-content-between">
                    <h6 class="text-base fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:text-bold" class="text-primary-600 text-xl"></iconify-icon>
                        1. Konten Headline &amp; Teks Promosi
                    </h6>
                    <span class="badge bg-primary-subtle text-primary text-2xs">Langkah 1</span>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Badge / Subtitle Kecil -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                Badge / Kategori Tagline Atas
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-secondary-light border">
                                    <iconify-icon icon="solar:bookmark-bold"></iconify-icon>
                                </span>
                                <input type="text" 
                                       name="badge_text" 
                                       id="input_badge_text" 
                                       class="form-control radius-end-8" 
                                       value="<?= esc($currentBadge) ?>" 
                                       placeholder="Pemerintah Provinsi Lampung · Biro PBJ Setda">
                            </div>
                            <small class="text-muted text-xs d-block mt-4">
                                Tampil sebagai tag identitas di atas judul utama (gunakan tanda <code>&middot;</code> atau <code>·</code> untuk pemisah).
                            </small>
                        </div>

                        <!-- Judul Slide (Headline) with Yellow Accent Helper -->
                        <div class="col-12">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-6">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Judul Utama Slide (Headline) <span class="text-danger">*</span>
                                </label>
                                <button type="button" 
                                        class="btn btn-xs btn-outline-warning text-dark border-warning radius-6 px-10 py-4 d-inline-flex align-items-center gap-1 shadow-none" 
                                        id="btnInsertSpanHighlight" 
                                        title="Bungkus teks terseleksi dalam tag <span>...</span> untuk aksen emas">
                                    <iconify-icon icon="solar:stars-bold" class="text-warning-main"></iconify-icon>
                                    <span class="fw-bold text-2xs">+ Aksen Emas <code>&lt;span&gt;</code></span>
                                </button>
                            </div>

                            <textarea name="title" 
                                      id="input_title" 
                                      class="form-control radius-8 fw-semibold" 
                                      rows="2" 
                                      placeholder="Contoh: Transformasi Digital Terpadu <span>Pengadaan Barang & Jasa</span>" 
                                      required><?= esc($currentTitle) ?></textarea>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-6">
                                <small class="text-muted text-xs">
                                    <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                                    Teks yang dibungkus tag <code>&lt;span&gt;...&lt;/span&gt;</code> otomatis tampil berwarna kuning keemasan khas portal publik.
                                </small>
                            </div>

                            <!-- Headline Suggestions Chips -->
                            <div class="mt-12 p-10 radius-8 border bg-neutral-50">
                                <span class="text-3xs fw-bold text-secondary-light text-uppercase d-block mb-6">Template Inspirasi Headline:</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($presets['headlines'] ?? [] as $hlPreset): ?>
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-neutral-300 bg-white radius-6 px-8 py-4 text-3xs text-secondary-light text-start text-truncate preset-headline-btn" 
                                                style="max-width: 100%;" 
                                                onclick="applyHeadlineTemplate('<?= esc($hlPreset, 'js') ?>')">
                                            <?= strip_tags($hlPreset) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi / Subjudul -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                Deskripsi Ringkas / Uraian Hero
                            </label>
                            <textarea name="subtitle" 
                                      id="input_subtitle" 
                                      class="form-control radius-8" 
                                      rows="3" 
                                      placeholder="Uraian singkat 1-2 kalimat mengenai modul, layanan, atau manfaat sistem"><?= esc($currentSubtitle) ?></textarea>
                            <small class="text-muted text-xs d-block mt-4">Disarankan 1-2 kalimat (maks. 180 karakter) agar nyaman dibaca di layar ponsel.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TOMBOL AKSI (CALL TO ACTION) -->
            <div class="card border radius-12 shadow-none mb-24">
                <div class="card-header border-bottom py-16 px-24 bg-base d-flex align-items-center justify-content-between">
                    <h6 class="text-base fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:cursor-bold" class="text-primary-600 text-xl"></iconify-icon>
                        2. Konfigurasi Tombol Aksi (Call To Action)
                    </h6>
                    <span class="badge bg-primary-subtle text-primary text-2xs">Langkah 2</span>
                </div>
                <div class="card-body p-24">
                    <!-- Quick Destination Chips -->
                    <div class="mb-20 p-12 radius-8 border bg-neutral-50">
                        <span class="text-3xs fw-bold text-secondary-light text-uppercase d-block mb-8">
                            <iconify-icon icon="solar:bolt-bold" class="text-warning-main me-1"></iconify-icon>
                            Pilihan Tautan Cepat Halaman Begawi:
                        </span>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($presets['routes'] ?? [] as $pr): ?>
                                <button type="button" 
                                        class="btn btn-xs btn-outline-primary-600 bg-white radius-6 px-10 py-4 text-3xs fw-medium shadow-none" 
                                        onclick="applyCtaPreset('<?= esc($pr['btn'], 'js') ?>', '<?= esc($pr['url'], 'js') ?>')">
                                    <?= esc($pr['label']) ?> (<code><?= esc($pr['url']) ?></code>)
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row gy-20">
                        <!-- CTA 1: Tombol Utama -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                Label Tombol Utama (Primary)
                            </label>
                            <input type="text" 
                                   name="button_text" 
                                   id="input_btn_text" 
                                   class="form-control radius-8" 
                                   value="<?= esc($currentBtnText) ?>" 
                                   placeholder="Contoh: Pusat Regulasi PBJ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                URL Tujuan Tombol Utama
                            </label>
                            <input type="text" 
                                   name="button_url" 
                                   id="input_btn_url" 
                                   class="form-control radius-8 font-monospace text-xs" 
                                   value="<?= esc($currentBtnUrl) ?>" 
                                   placeholder="regulasi atau https://...">
                        </div>

                        <!-- CTA 2: Tombol Sekunder -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary-light text-sm mb-6">
                                Label Tombol Kedua (Secondary)
                            </label>
                            <input type="text" 
                                   name="secondary_button_text" 
                                   id="input_sec_btn_text" 
                                   class="form-control radius-8" 
                                   value="<?= esc($currentSecBtnText) ?>" 
                                   placeholder="Contoh: Arena Belajar">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary-light text-sm mb-6">
                                URL Tujuan Tombol Kedua
                            </label>
                            <input type="text" 
                                   name="secondary_button_url" 
                                   id="input_sec_btn_url" 
                                   class="form-control radius-8 font-monospace text-xs" 
                                   value="<?= esc($currentSecBtnUrl) ?>" 
                                   placeholder="arena-belajar atau https://...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. VISIBILITAS & URUTAN TAMPIL -->
            <div class="card border radius-12 shadow-none mb-24">
                <div class="card-header border-bottom py-16 px-24 bg-base d-flex align-items-center justify-content-between">
                    <h6 class="text-base fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:tuning-bold" class="text-primary-600 text-xl"></iconify-icon>
                        3. Visibilitas &amp; Urutan Rotasi
                    </h6>
                    <span class="badge bg-primary-subtle text-primary text-2xs">Langkah 3</span>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                Nomor Urutan Tampil (Sort Order)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-secondary-light border">
                                    <iconify-icon icon="solar:sort-vertical-linear"></iconify-icon>
                                </span>
                                <input type="number" 
                                       name="sort_order" 
                                       class="form-control radius-end-8" 
                                       value="<?= esc($currentOrder) ?>" 
                                       min="1" 
                                       max="99" 
                                       required>
                            </div>
                            <small class="text-muted text-xs d-block mt-4">Angka lebih kecil tampil terlebih dahulu pada rotasi slide.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                Status Penayangan Publik
                            </label>
                            <select name="is_active" class="form-select radius-8">
                                <option value="1" <?= $currentActive == 1 ? 'selected' : '' ?>>Aktif (Langsung Tayang di Beranda)</option>
                                <option value="0" <?= $currentActive == 0 ? 'selected' : '' ?>>Nonaktif (Simpan Sebagai Draf)</option>
                            </select>
                            <small class="text-muted text-xs d-block mt-4">Slide nonaktif dapat diaktifkan sewaktu-waktu melalui tombol toggle.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Sticky Live Visualizer & Media Upload -->
        <div class="col-xl-5 col-lg-6">
            <div class="sticky-top" style="top: 90px; z-index: 10;">
                <!-- LIVE REAL-TIME VISUALIZER CARD -->
                <div class="card border radius-12 shadow-sm mb-24 overflow-hidden">
                    <div class="card-header border-bottom py-14 px-20 bg-base d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="w-10-px h-10-px rounded-circle bg-success-main animate-pulse"></span>
                            <h6 class="text-sm fw-bold text-primary-light mb-0">Pratinjau Langsung (Live Visualizer)</h6>
                        </div>
                        <span class="text-3xs text-secondary-light">Desktop Hero View</span>
                    </div>

                    <!-- Simulator Canvas -->
                    <div class="position-relative bg-neutral-900 overflow-hidden text-white p-24" style="min-height: 280px;" id="liveVisualizerContainer">
                        <!-- Background Canvas -->
                        <img id="livePreviewBgImg" 
                             src="<?= esc($curImgSrc) ?>" 
                             alt="Live Hero Preview" 
                             class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 opacity-40 transition-all"
                             onerror="this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(8, 23, 48, 0.94) 0%, rgba(8, 23, 48, 0.72) 65%, rgba(8, 23, 48, 0.35) 100%);"></div>

                        <!-- Live Content Overlay -->
                        <div class="position-relative d-flex flex-column justify-content-center h-100" style="z-index: 2; min-height: 240px;">
                            <!-- Live Badge -->
                            <div class="mb-10" id="livePreviewBadgeWrap">
                                <span class="badge bg-primary text-white radius-4 px-10 py-4 text-3xs fw-bold text-uppercase" id="livePreviewBadge">
                                    <?= esc($currentBadge) ?>
                                </span>
                            </div>

                            <!-- Live Headline -->
                            <h4 class="text-lg fw-bold text-white mb-10 lh-sm" id="livePreviewTitle">
                                <?= str_ireplace('<span>', '<span style="color: #ffc107; font-weight: 700;">', $currentTitle ?: 'Judul Slide Hero Beranda') ?>
                            </h4>

                            <!-- Live Subtitle -->
                            <p class="text-neutral-300 text-2xs mb-18 text-truncate-3" id="livePreviewSubtitle" style="line-height: 1.5;">
                                <?= esc($currentSubtitle ?: 'Uraian ringkas atau manfaat sistem yang akan dipromosikan kepada pengunjung.') ?>
                            </p>

                            <!-- Live Buttons -->
                            <div class="d-flex flex-wrap align-items-center gap-2" id="livePreviewBtnsWrap">
                                <span class="btn btn-warning text-dark btn-sm radius-6 px-14 py-6 text-3xs fw-bold shadow-xs" id="livePreviewBtn1">
                                    <?= esc($currentBtnText ?: 'Tombol Utama') ?> &rarr;
                                </span>
                                <span class="btn btn-outline-light btn-sm radius-6 px-14 py-6 text-3xs fw-medium" id="livePreviewBtn2">
                                    <?= esc($currentSecBtnText ?: 'Tombol Kedua') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer py-10 px-20 bg-neutral-50 border-top d-flex align-items-center justify-content-between text-3xs text-secondary-light">
                        <span><iconify-icon icon="solar:stars-bold" class="text-warning-main me-1"></iconify-icon>Warna emas terpicu oleh tag <code>&lt;span&gt;</code></span>
                        <span>Rasio 1920x500 px</span>
                    </div>
                </div>

                <!-- MEDIA UPLOAD & BACKGROUND CARD -->
                <div class="card border radius-12 shadow-none mb-24">
                    <div class="card-header border-bottom py-16 px-24 bg-base d-flex align-items-center justify-content-between">
                        <h6 class="text-base fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                            Unggah Gambar Banner Hero
                        </h6>
                    </div>
                    <div class="card-body p-20">
                        <!-- Drag & Drop Zone -->
                        <div class="border-2 border-dashed border-neutral-300 radius-12 p-20 text-center bg-neutral-50 hover-bg-neutral-100 transition-all cursor-pointer position-relative mb-16" id="dropZoneArea">
                            <input type="file" 
                                   name="image_file" 
                                   id="image_file" 
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                   accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" 
                                   onchange="handleHeroFileChange(this)">
                            
                            <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-8">
                                <iconify-icon icon="solar:upload-track-2-bold" class="text-2xl"></iconify-icon>
                            </div>
                            <h6 class="text-sm fw-semibold text-primary-light mb-2">Klik atau Seret File Gambar ke Sini</h6>
                            <p class="text-3xs text-secondary-light mb-0">Format: PNG, JPG, JPEG, WEBP, atau SVG (Maks. 5 MB)</p>
                            <p class="text-3xs text-primary-600 fw-semibold mt-1 mb-0">Resolusi Ideal: 1920 x 500 px (Lanskap Lebar)</p>
                        </div>

                        <!-- File Selected Indicator & Reset -->
                        <div id="fileSelectedInfo" class="d-none align-items-center justify-content-between p-10 radius-8 border bg-success-50 text-success-700 text-xs mb-16">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                <span id="fileNameLabel" class="fw-semibold text-truncate"></span>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-danger p-2 radius-4" onclick="resetHeroFileInput()" title="Batal pilih file baru">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                            </button>
                        </div>

                        <!-- Galeri Gambar Preset Begawi -->
                        <div class="p-12 radius-8 border bg-white mb-16">
                            <span class="text-3xs fw-bold text-secondary-light text-uppercase d-block mb-8">
                                Atau Pilih Latar Preset Begawi:
                            </span>
                            <div class="row g-2">
                                <?php foreach ($presets['backgrounds'] ?? [] as $bg): ?>
                                    <div class="col-4">
                                        <div class="border radius-6 overflow-hidden cursor-pointer hover-border-primary transition-all position-relative preset-bg-card" 
                                             onclick="applyPresetBg('<?= esc($bg['path'], 'js') ?>')">
                                            <img src="<?= base_url($bg['path']) ?>" alt="<?= esc($bg['label']) ?>" class="w-100 object-fit-cover" style="height: 50px;" onerror="this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'">
                                            <span class="d-block text-center text-3xs text-truncate p-1 bg-neutral-100 fw-medium text-secondary-light">
                                                <?= esc($bg['label']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Path Gambar Manual / Accordion -->
                        <div>
                            <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseManualUrl" role="button">
                                <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                <span>Path URL Gambar Statis</span>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-3xs"></iconify-icon>
                            </a>
                            <div class="collapse mt-8" id="collapseManualUrl">
                                <input type="text" 
                                       name="image_url" 
                                       id="image_url" 
                                       class="form-control form-control-sm radius-8 font-monospace text-xs" 
                                       value="<?= esc($currentImageUrl) ?>" 
                                       placeholder="assets/main/images/backgrounds/slider-1-1.png"
                                       oninput="updatePreviewFromManualPath(this.value)">
                                <small class="text-muted text-3xs d-block mt-4">Kosongkan jika telah mengunggah file baru di atas.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS CARD -->
                <div class="card border radius-12 shadow-sm">
                    <div class="card-body p-16 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <a href="<?= site_url('dashboard/landing/slider') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 text-sm">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 text-sm fw-semibold d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon>
                            <span><?= $isEdit ? 'Perbarui Slide Hero' : 'Simpan Slide Hero' ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Real-time synchronization between form inputs and live visualizer
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('input_title');
    const badgeInput = document.getElementById('input_badge_text');
    const subtitleInput = document.getElementById('input_subtitle');
    const btnTextInput = document.getElementById('input_btn_text');
    const secBtnTextInput = document.getElementById('input_sec_btn_text');

    const previewTitle = document.getElementById('livePreviewTitle');
    const previewBadge = document.getElementById('livePreviewBadge');
    const previewBadgeWrap = document.getElementById('livePreviewBadgeWrap');
    const previewSubtitle = document.getElementById('livePreviewSubtitle');
    const previewBtn1 = document.getElementById('livePreviewBtn1');
    const previewBtn2 = document.getElementById('livePreviewBtn2');

    function syncLivePreview() {
        // Title with gold span transform
        const rawTitle = titleInput.value.trim() || 'Judul Slide Hero Beranda';
        const formattedTitle = rawTitle.replace(/<span>/gi, '<span style="color: #ffc107; font-weight: 700;">');
        previewTitle.innerHTML = formattedTitle;

        // Badge
        const badgeVal = badgeInput.value.trim();
        if (badgeVal) {
            previewBadgeWrap.style.display = 'block';
            previewBadge.innerText = badgeVal;
        } else {
            previewBadgeWrap.style.display = 'none';
        }

        // Subtitle
        previewSubtitle.innerText = subtitleInput.value.trim() || 'Uraian ringkas atau manfaat sistem yang akan dipromosikan.';

        // Buttons
        const b1 = btnTextInput.value.trim();
        if (b1) {
            previewBtn1.style.display = 'inline-block';
            previewBtn1.innerText = b1 + ' →';
        } else {
            previewBtn1.style.display = 'none';
        }

        const b2 = secBtnTextInput.value.trim();
        if (b2) {
            previewBtn2.style.display = 'inline-block';
            previewBtn2.innerText = b2;
        } else {
            previewBtn2.style.display = 'none';
        }
    }

    titleInput.addEventListener('input', syncLivePreview);
    badgeInput.addEventListener('input', syncLivePreview);
    subtitleInput.addEventListener('input', syncLivePreview);
    btnTextInput.addEventListener('input', syncLivePreview);
    secBtnTextInput.addEventListener('input', syncLivePreview);

    // Initial sync
    syncLivePreview();

    // Helper: Wrap selected text or insert <span> in Title textarea
    const insertSpanBtn = document.getElementById('btnInsertSpanHighlight');
    if (insertSpanBtn) {
        insertSpanBtn.addEventListener('click', function () {
            const start = titleInput.selectionStart;
            const end = titleInput.selectionEnd;
            const text = titleInput.value;
            const selected = text.substring(start, end);

            let replacement = '';
            if (selected.length > 0) {
                replacement = `<span>${selected}</span>`;
            } else {
                replacement = '<span>Pengadaan Barang & Jasa</span>';
            }

            titleInput.value = text.substring(0, start) + replacement + text.substring(end);
            titleInput.focus();
            titleInput.setSelectionRange(start, start + replacement.length);
            syncLivePreview();
        });
    }
});

// File input handler with FileReader live background update
function handleHeroFileChange(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            const previewImg = document.getElementById('livePreviewBgImg');
            if (previewImg) {
                previewImg.src = e.target.result;
            }

            // Show selected filename info
            const fileInfo = document.getElementById('fileSelectedInfo');
            const fileNameLabel = document.getElementById('fileNameLabel');
            if (fileInfo && fileNameLabel) {
                fileNameLabel.innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                fileInfo.classList.remove('d-none');
                fileInfo.classList.add('d-flex');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Reset file input
function resetHeroFileInput() {
    const input = document.getElementById('image_file');
    if (input) input.value = '';
    const fileInfo = document.getElementById('fileSelectedInfo');
    if (fileInfo) {
        fileInfo.classList.add('d-none');
        fileInfo.classList.remove('d-flex');
    }

    // Revert to manual path or default
    const manualPath = document.getElementById('image_url').value;
    const base = '<?= base_url() ?>';
    const previewImg = document.getElementById('livePreviewBgImg');
    if (previewImg) {
        previewImg.src = manualPath.startsWith('http') ? manualPath : base + '/' + manualPath.replace(/^\/+/, '');
    }
}

// Apply preset background path
function applyPresetBg(relPath) {
    const inputUrl = document.getElementById('image_url');
    if (inputUrl) inputUrl.value = relPath;

    // Clear file upload input so static path is used
    resetHeroFileInput();

    const base = '<?= base_url() ?>';
    const previewImg = document.getElementById('livePreviewBgImg');
    if (previewImg) {
        previewImg.src = base + '/' + relPath.replace(/^\/+/, '');
    }
}

// Update preview from manual text path input
function updatePreviewFromManualPath(path) {
    if (! path) return;
    const base = '<?= base_url() ?>';
    const previewImg = document.getElementById('livePreviewBgImg');
    if (previewImg) {
        previewImg.src = path.startsWith('http') ? path : base + '/' + path.replace(/^\/+/, '');
    }
}

// Quick CTA Preset click
function applyCtaPreset(btnLabel, btnUrl) {
    const btnInput = document.getElementById('input_btn_text');
    const urlInput = document.getElementById('input_btn_url');
    if (btnInput) btnInput.value = btnLabel;
    if (urlInput) urlInput.value = btnUrl;

    // Trigger preview update
    btnInput.dispatchEvent(new Event('input'));
}

// Apply headline template
function applyHeadlineTemplate(headline) {
    const titleInput = document.getElementById('input_title');
    if (titleInput) {
        titleInput.value = headline;
        titleInput.dispatchEvent(new Event('input'));
    }
}
</script>

<?= $this->endSection() ?>
