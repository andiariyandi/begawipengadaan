<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($portal);
$actionUrl = $isEdit ? site_url('dashboard/landing/portal-eksternal/ubah/' . $portal['id']) : site_url('dashboard/landing/portal-eksternal/simpan');

$defaultSort = $portal['sort_order'] ?? ($nextOrder ?? 1);
$curCategory = $portal['category'] ?? 'nasional';
$curLogo     = $portal['logo_url'] ?? 'solar:server-square-bold';
$isImg       = str_contains($curLogo, '/') || str_contains($curLogo, '.');
$curLogoSrc  = $isImg ? (str_starts_with($curLogo, 'http') ? $curLogo : base_url($curLogo)) : '';
?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Portal Ekosistem PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola nama sistem pengadaan, tautan langsung ke portal resmi, kategori, dan ikon representasi.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/portal-eksternal') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="portalForm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= esc($portal['id']) ?>">
    <?php endif; ?>

    <div class="row g-24">
        <!-- Form Left Column -->
        <div class="col-xl-8 col-lg-7">
            <!-- Preset Quick Selector Card -->
            <div class="card border mb-24 bg-gradient-start-4">
                <div class="card-body p-20">
                    <div class="d-flex align-items-center justify-content-between mb-12 flex-wrap gap-2">
                        <span class="badge bg-primary-600 text-white text-2xs px-8 py-3 radius-4 fw-semibold">
                            <iconify-icon icon="solar:magic-stick-3-bold" class="me-1"></iconify-icon> Template Cepat
                        </span>
                        <small class="text-secondary-light text-2xs">Klik untuk mengisi data standar secara otomatis:</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="LPSE Provinsi Lampung" data-acronym="SPSE" data-url="https://lpse.lampungprov.go.id" 
                                data-cat="daerah" data-desc="Sistem Pengadaan Secara Elektronik (SPSE) tender, seleksi, dan non-tender Pemprov Lampung." 
                                data-icon="solar:server-square-bold">
                            <iconify-icon icon="solar:server-square-bold" class="text-info me-1"></iconify-icon> LPSE Lampung
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="SiRUP LKPP" data-acronym="SiRUP" data-url="https://sirup.lkpp.go.id" 
                                data-cat="nasional" data-desc="Sistem Informasi Rencana Umum Pengadaan seluruh paket belanja anggaran Pemprov Lampung." 
                                data-icon="solar:document-add-bold">
                            <iconify-icon icon="solar:document-add-bold" class="text-success me-1"></iconify-icon> SiRUP LKPP
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="Katalog Elektronik (E-Katalog LKPP)" data-acronym="E-Katalog" data-url="https://e-katalog.lkpp.go.id" 
                                data-cat="nasional" data-desc="Aplikasi belanja e-purchasing komoditas nasional, sektoral, dan etalase lokal Lampung." 
                                data-icon="solar:cart-large-4-bold">
                            <iconify-icon icon="solar:cart-large-4-bold" class="text-warning me-1"></iconify-icon> E-Katalog LKPP
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="Bela Pengadaan & Toko Daring" data-acronym="Bela" data-url="https://belapengadaan.lkpp.go.id" 
                                data-cat="nasional" data-desc="Pasar daring pengadaan langsung kebutuhan operasional pemerintah bersama mitra PPMSE terverifikasi." 
                                data-icon="solar:shop-2-bold">
                            <iconify-icon icon="solar:shop-2-bold" class="text-danger me-1"></iconify-icon> Toko Daring / Bela
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="Portal Resmi LKPP RI" data-acronym="LKPP" data-url="https://www.lkpp.go.id" 
                                data-cat="nasional" data-desc="Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah selaku instansi pembina PBJ nasional." 
                                data-icon="solar:city-bold">
                            <iconify-icon icon="solar:city-bold" class="text-primary me-1"></iconify-icon> Portal LKPP RI
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-portal-btn shadow-xs" 
                                data-name="JDIH Pemerintah Provinsi Lampung" data-acronym="JDIH" data-url="https://jdih.lampungprov.go.id" 
                                data-cat="daerah" data-desc="Jaringan Dokumentasi dan Informasi Hukum Produk Hukum Daerah Pemprov Lampung." 
                                data-icon="solar:book-bookmark-bold">
                            <iconify-icon icon="solar:book-bookmark-bold" class="text-info me-1"></iconify-icon> JDIH Lampung
                        </button>
                    </div>
                </div>
            </div>

            <!-- Portal Information Card -->
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:pen-new-square-bold" class="text-primary-600 text-xl"></iconify-icon>
                            <span>Informasi Portal Mitra</span>
                        </h6>
                        <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-xs fw-semibold">
                            <?= $isEdit ? 'Mode Ubah Data' : 'Mode Portal Baru' ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Nama Portal -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Nama Portal Resmi <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="nameCharCount">0/150</span>
                            </div>
                            <input type="text" 
                                   name="name" 
                                   id="portalNameInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($portal['name'] ?? '') ?>" 
                                   placeholder="Misal: LPSE Provinsi Lampung" 
                                   required 
                                   maxlength="150">
                        </div>

                        <!-- Akronim -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Akronim / Singkatan
                            </label>
                            <input type="text" 
                                   name="acronym" 
                                   id="portalAcronymInput" 
                                   class="form-control radius-8 font-monospace fw-bold" 
                                   value="<?= esc($portal['acronym'] ?? '') ?>" 
                                   placeholder="Misal: SPSE, SiRUP" 
                                   maxlength="30">
                            <small class="text-secondary-light text-2xs d-block mt-4">Label tag mini di samping nama.</small>
                        </div>

                        <!-- URL Tautan Resmi -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                URL Tautan Resmi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 border-end-0 radius-start-8">
                                    <iconify-icon icon="solar:link-round-linear" class="text-secondary-light"></iconify-icon>
                                </span>
                                <input type="url" 
                                       name="url" 
                                       id="portalUrlInput" 
                                       class="form-control radius-end-8 font-monospace text-sm" 
                                       value="<?= esc($portal['url'] ?? '') ?>" 
                                       placeholder="https://lpse.lampungprov.go.id" 
                                       required 
                                       maxlength="255">
                            </div>
                            <small class="text-secondary-light text-2xs d-block mt-4">Gunakan protokol <code>https://</code> agar aman.</small>
                        </div>

                        <!-- Kategori Ekosistem -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Kategori Ekosistem
                            </label>
                            <select name="category" id="portalCategoryInput" class="form-select radius-8">
                                <option value="nasional" <?= $curCategory === 'nasional' ? 'selected' : '' ?>>Nasional (LKPP RI)</option>
                                <option value="daerah" <?= $curCategory === 'daerah' ? 'selected' : '' ?>>Daerah (Pemprov Lampung)</option>
                            </select>
                            <small class="text-secondary-light text-2xs d-block mt-4">Warna aksen kartu otomatis menyesuaikan.</small>
                        </div>

                        <!-- Deskripsi Layanan -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Deskripsi Ringkas Layanan
                                </label>
                                <span class="text-2xs text-secondary-light" id="descCharCount">0/255</span>
                            </div>
                            <textarea name="description" 
                                      id="portalDescInput" 
                                      class="form-control radius-8" 
                                      rows="3" 
                                      placeholder="Jelaskan secara ringkas peran atau layanan sistem pengadaan ini..."><?= esc($portal['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo & Icon Configuration Card -->
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                        <span>Logo / Ikon Representasi Portal</span>
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Option A: File Upload -->
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="logo_file">
                                Unggah File Logo Gambar (Opsional)
                            </label>
                            <input type="file" 
                                   name="logo_file" 
                                   id="logoFileInput" 
                                   class="form-control radius-8 text-sm" 
                                   accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            <small class="text-secondary-light text-2xs d-block mt-4">
                                Disarankan format PNG transparan persegi (rasio 1:1, maks. 5MB).
                            </small>

                            <div class="mt-12">
                                <label class="form-label fw-semibold text-primary-light text-xs mb-4">
                                    Atau Nama Ikon Iconify / Path Gambar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-neutral-100 border-end-0 radius-start-8" id="iconBadgeSpan">
                                        <iconify-icon icon="<?= esc($curLogo) ?>" class="text-lg text-primary-600" id="iconInputLivePreview"></iconify-icon>
                                    </span>
                                    <input type="text" 
                                           name="logo_url" 
                                           id="logoUrlInput" 
                                           class="form-control radius-end-8 font-monospace text-sm" 
                                           value="<?= esc($curLogo) ?>" 
                                           placeholder="solar:server-square-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Option B: Quick Icon Chips -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Pilihan Ikon Populer PBJ
                            </label>
                            <div class="d-flex align-items-center gap-1 flex-wrap p-12 radius-8 bg-neutral-50 border">
                                <?php
                                $iconList = [
                                    'solar:server-square-bold' => 'Server / LPSE',
                                    'solar:document-add-bold'  => 'Dokumen / SiRUP',
                                    'solar:cart-large-4-bold'  => 'Troli / E-Katalog',
                                    'solar:shop-2-bold'        => 'Toko / Bela',
                                    'solar:city-bold'          => 'Instansi / LKPP',
                                    'solar:book-bookmark-bold' => 'Buku / JDIH',
                                    'solar:global-bold'        => 'Global / Web',
                                    'solar:shield-check-bold'  => 'Keamanan',
                                    'solar:chart-2-bold'       => 'Statistik / AMEL',
                                    'solar:user-speak-bold'    => 'Helpdesk / LPSE',
                                ];
                                foreach ($iconList as $ico => $icoName): ?>
                                    <button type="button" 
                                            class="btn btn-xs btn-white border radius-4 p-8 icon-chip-btn transition-2" 
                                            data-icon="<?= $ico ?>" 
                                            title="<?= $icoName ?>">
                                        <iconify-icon icon="<?= $ico ?>" class="text-xl text-primary-600"></iconify-icon>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-secondary-light text-2xs d-block mt-4">Klik salah satu ikon di atas untuk langsung menerapkannya.</small>
                        </div>

                        <!-- Urutan Tampil & Status Tayang -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Urutan Tampil (Sort Order)
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="portalSortInput" 
                                   class="form-control radius-8 fw-bold text-center" 
                                   value="<?= esc($defaultSort) ?>" 
                                   min="1" 
                                   max="99">
                            <small class="text-secondary-light text-2xs d-block mt-4">Angka kecil tampil lebih dahulu di kartu beranda.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Status Publikasi
                            </label>
                            <select name="is_active" id="portalActiveInput" class="form-select radius-8">
                                <option value="1" <?= ($portal['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan di Beranda)</option>
                                <option value="0" <?= ($portal['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-20 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/landing/portal-eksternal') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-10 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:diskette-bold" class="text-xl"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan' : 'Buat Portal Baru' ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Real-Time Simulator Column (Right Side Sticky) -->
        <div class="col-xl-4 col-lg-5">
            <div class="position-sticky" style="top: 90px; z-index: 10;">
                <div class="card border shadow-sm">
                    <div class="card-header bg-white border-bottom p-16">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-primary-light mb-0 text-sm d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:eye-bold" class="text-primary-600 text-lg"></iconify-icon>
                                <span>Pratinjau Kartu Real-Time</span>
                            </h6>
                            <span class="badge bg-success-100 text-success-600 radius-4 text-2xs px-8 py-3 fw-semibold">
                                Live Preview
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-24 bg-light">
                        <div class="text-center mb-16">
                            <small class="text-secondary-light text-2xs">
                                Tampilan tombol tautan di blok ekosistem beranda publik:
                            </small>
                        </div>

                        <!-- Live Component Preview -->
                        <div class="card border-0 shadow-sm radius-12 p-16 transition-2 mx-auto" 
                             id="livePortalCard" 
                             style="background: #ffffff; border: 1.5px solid #0284c7 !important; max-width: 380px;">
                            
                            <div class="d-flex align-items-center gap-3">
                                <!-- Icon / Image Box -->
                                <div class="radius-10 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     id="liveIconBox"
                                     style="width: 48px; height: 48px; background: #eff6ff; color: #0284c7;">
                                    <img id="liveImgPreview" src="<?= esc($curLogoSrc) ?>" alt="Logo" class="<?= $isImg ? '' : 'd-none' ?>" style="width: 26px; height: 26px; object-fit: contain;">
                                    <iconify-icon id="liveIconPreview" icon="<?= esc($curLogo) ?>" class="text-2xl <?= $isImg ? 'd-none' : '' ?>"></iconify-icon>
                                </div>

                                <!-- Text Content -->
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <strong class="d-block text-neutral-900 text-sm text-truncate mb-0" id="livePortalName">
                                            <?= esc($portal['name'] ?? 'Nama Portal Eksternal') ?>
                                        </strong>
                                        <span class="badge bg-neutral-200 text-neutral-800 font-monospace text-3xs px-6 py-2 radius-4 fw-bold" id="livePortalAcronym">
                                            <?= esc($portal['acronym'] ?? 'AKR') ?>
                                        </span>
                                    </div>
                                    <small class="text-secondary-light text-2xs d-block text-truncate" id="livePortalDesc">
                                        <?= esc($portal['description'] ?? 'Deskripsi fungsi dan layanan sistem ini.') ?>
                                    </small>
                                </div>

                                <!-- Arrow Action -->
                                <div class="flex-shrink-0 text-secondary-light">
                                    <iconify-icon icon="solar:arrow-right-up-linear" class="text-base" id="livePortalArrow"></iconify-icon>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Summary -->
                        <div class="mt-20 p-16 radius-8 bg-white border">
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Kategori:</span>
                                <span class="badge bg-info-100 text-info-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold" id="liveSummaryCategory">
                                    <?= esc($curCategory) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Tipe Ikon:</span>
                                <span class="font-monospace text-2xs" id="liveSummaryLogoType">
                                    <?= $isImg ? 'File Gambar' : 'Solar Iconify' ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 text-xs">
                                <span class="text-secondary-light">Urutan &amp; Status:</span>
                                <span class="fw-semibold">
                                    #<span id="liveSummaryOrder"><?= esc($defaultSort) ?></span> &bull; 
                                    <span id="liveSummaryActive" class="text-success-main">Aktif</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Input Elements
    const nameInput      = document.getElementById('portalNameInput');
    const acronymInput   = document.getElementById('portalAcronymInput');
    const urlInput       = document.getElementById('portalUrlInput');
    const categoryInput  = document.getElementById('portalCategoryInput');
    const descInput      = document.getElementById('portalDescInput');
    const logoFileInput  = document.getElementById('logoFileInput');
    const logoUrlInput   = document.getElementById('logoUrlInput');
    const iconInputLive  = document.getElementById('iconInputLivePreview');
    const sortInput      = document.getElementById('portalSortInput');
    const activeInput    = document.getElementById('portalActiveInput');

    // Live Preview Elements
    const liveCard       = document.getElementById('livePortalCard');
    const liveIconBox    = document.getElementById('liveIconBox');
    const liveImg        = document.getElementById('liveImgPreview');
    const liveIcon       = document.getElementById('liveIconPreview');
    const liveName       = document.getElementById('livePortalName');
    const liveAcronym    = document.getElementById('livePortalAcronym');
    const liveDesc       = document.getElementById('livePortalDesc');
    const liveArrow      = document.getElementById('livePortalArrow');

    // Summary Elements
    const summaryCategory = document.getElementById('liveSummaryCategory');
    const summaryLogoType = document.getElementById('liveSummaryLogoType');
    const summaryOrder    = document.getElementById('liveSummaryOrder');
    const summaryActive   = document.getElementById('liveSummaryActive');

    // Counters
    const nameCounter = document.getElementById('nameCharCount');
    const descCounter = document.getElementById('descCharCount');

    let uploadedLocalSrc = '<?= $isImg ? esc($curLogoSrc) : '' ?>';

    function updateLiveVisualizer() {
        const name     = nameInput.value.trim() || 'Nama Portal Eksternal';
        const acronym  = acronymInput.value.trim();
        const desc     = descInput.value.trim() || 'Deskripsi fungsi dan layanan sistem ini.';
        const category = categoryInput.value;
        const iconCode = logoUrlInput.value.trim() || 'solar:server-square-bold';
        const sortVal  = sortInput.value || '1';
        const isActive = (activeInput.value === '1');

        // Texts
        liveName.textContent = name;
        liveDesc.textContent = desc;

        if (acronym) {
            liveAcronym.textContent = acronym;
            liveAcronym.style.display = '';
        } else {
            liveAcronym.style.display = 'none';
        }

        // Color theme based on category
        const isDaerah = (category === 'daerah');
        const accentColor = isDaerah ? '#0284c7' : '#10b981';
        const accentBg    = isDaerah ? '#eff6ff' : '#ecfdf5';

        liveCard.style.borderColor = accentColor;
        liveIconBox.style.backgroundColor = accentBg;
        liveIconBox.style.color = accentColor;
        liveArrow.style.color = accentColor;

        // Image vs Icon
        if (uploadedLocalSrc) {
            liveImg.src = uploadedLocalSrc;
            liveImg.classList.remove('d-none');
            liveIcon.classList.add('d-none');
            if (summaryLogoType) summaryLogoType.textContent = 'File Gambar';
        } else if (iconCode.includes('/') || iconCode.includes('.')) {
            liveImg.src = iconCode;
            liveImg.classList.remove('d-none');
            liveIcon.classList.add('d-none');
            if (summaryLogoType) summaryLogoType.textContent = 'Path Gambar';
        } else {
            liveImg.classList.add('d-none');
            liveIcon.classList.remove('d-none');
            liveIcon.setAttribute('icon', iconCode);
            if (iconInputLive) iconInputLive.setAttribute('icon', iconCode);
            if (summaryLogoType) summaryLogoType.textContent = 'Solar Iconify';
        }

        // Summary
        if (summaryCategory) {
            summaryCategory.textContent = category;
            summaryCategory.className = `badge ${isDaerah ? 'bg-info-100 text-info-700' : 'bg-warning-100 text-warning-800'} text-2xs text-uppercase radius-4 px-8 py-3 fw-bold`;
        }
        if (summaryOrder) summaryOrder.textContent = sortVal;
        if (summaryActive) {
            if (isActive) {
                summaryActive.textContent = 'Aktif';
                summaryActive.className = 'text-success-main fw-semibold';
            } else {
                summaryActive.textContent = 'Nonaktif';
                summaryActive.className = 'text-warning-main fw-semibold';
            }
        }

        // Counters
        if (nameCounter) nameCounter.textContent = `${nameInput.value.length}/150`;
        if (descCounter) descCounter.textContent = `${descInput.value.length}/255`;
    }

    // Input Listeners
    [nameInput, acronymInput, urlInput, categoryInput, descInput, logoUrlInput, sortInput, activeInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLiveVisualizer);
            el.addEventListener('change', updateLiveVisualizer);
        }
    });

    // File Upload Listener
    if (logoFileInput) {
        logoFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    uploadedLocalSrc = e.target.result;
                    updateLiveVisualizer();
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Icon Chips
    const iconChips = document.querySelectorAll('.icon-chip-btn');
    iconChips.forEach(btn => {
        btn.addEventListener('click', function () {
            const icon = this.getAttribute('data-icon');
            uploadedLocalSrc = '';
            if (logoFileInput) logoFileInput.value = '';
            logoUrlInput.value = icon;
            updateLiveVisualizer();
        });
    });

    // Preset Portal Buttons
    const presetBtns = document.querySelectorAll('.preset-portal-btn');
    presetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            nameInput.value = this.getAttribute('data-name');
            acronymInput.value = this.getAttribute('data-acronym');
            urlInput.value = this.getAttribute('data-url');
            categoryInput.value = this.getAttribute('data-cat');
            descInput.value = this.getAttribute('data-desc');
            logoUrlInput.value = this.getAttribute('data-icon');
            uploadedLocalSrc = '';
            if (logoFileInput) logoFileInput.value = '';

            updateLiveVisualizer();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Template Portal Dimuat',
                    text: `Data ${this.getAttribute('data-name')} berhasil diisikan ke formulir.`,
                    timer: 1400,
                    showConfirmButton: false
                });
            }
        });
    });

    // Initial render
    updateLiveVisualizer();
});
</script>

<?= $this->endSection() ?>
