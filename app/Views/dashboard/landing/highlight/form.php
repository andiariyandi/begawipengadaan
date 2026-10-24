<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($item);
$actionUrl = $isEdit ? site_url('dashboard/landing/pilar-hierarki/ubah/' . $item['id']) : site_url('dashboard/landing/pilar-hierarki/simpan');
$section = $item['section_key'] ?? $currentSection;

$defaultColor = $item['color_hex'] ?? ($sectionMeta['color'] ?? '#0c5aa6');
$defaultIcon  = $item['icon_class'] ?? ($sectionMeta['icon'] ?? 'solar:star-bold');
$defaultSort  = $item['sort_order'] ?? ($nextOrder ?? 1);
?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Highlight Fitur') ?></h6>
        <p class="text-secondary-light mb-0">Kelola judul, subjudul, ikon Solar Iconify, kode badge, tautan dan warna aksen.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($section)) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $actionUrl ?>" id="highlightForm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= esc($item['id']) ?>">
    <?php endif; ?>

    <div class="row g-24">
        <!-- Form Left Column -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:pen-new-square-bold" class="text-primary-600 text-xl"></iconify-icon>
                            <span>Informasi & Konten Highlight</span>
                        </h6>
                        <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-xs fw-semibold">
                            <?= $isEdit ? 'Mode Ubah Data' : 'Mode Item Baru' ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Kelompok Fitur (Section) -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Kategori Ekosistem (Section) <span class="text-danger">*</span>
                            </label>
                            <select name="section_key" id="sectionKeyInput" class="form-select radius-8" <?= $isEdit ? '' : '' ?>>
                                <?php foreach ($allSections as $sKey => $sMeta): ?>
                                    <option value="<?= esc($sKey) ?>" 
                                            data-color="<?= esc($sMeta['color']) ?>"
                                            data-icon="<?= esc($sMeta['icon']) ?>"
                                            <?= $section === $sKey ? 'selected' : '' ?>>
                                        <?= esc($sMeta['name']) ?> (<?= esc($sMeta['tag']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-secondary-light text-2xs d-block mt-4">
                                Menentukan di blok dan grup mana kartu highlight ini akan ditampilkan pada beranda/tentang.
                            </small>
                        </div>

                        <!-- Kode Singkat & Badge -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Kode Singkat
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="codeInput" 
                                   class="form-control radius-8 font-monospace fw-bold" 
                                   value="<?= esc($item['code'] ?? '') ?>" 
                                   placeholder="Misal: 01, P, L1, UU" 
                                   maxlength="20">
                            <small class="text-secondary-light text-2xs d-block mt-4">Tampil sebagai identitas nomor/huruf unik.</small>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Badge / Label Tag
                            </label>
                            <input type="text" 
                                   name="badge_text" 
                                   id="badgeInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($item['badge_text'] ?? '') ?>" 
                                   placeholder="Misal: Pilar 01, Tingkat I, Kompetensi Dasar, Kriptografi" 
                                   maxlength="50">
                            <small class="text-secondary-light text-2xs d-block mt-4">Label tag mini di atas atau samping judul.</small>
                        </div>

                        <!-- Judul Utama -->
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Judul Utama <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="titleCharCount">0/255</span>
                            </div>
                            <input type="text" 
                                   name="title" 
                                   id="titleInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($item['title'] ?? '') ?>" 
                                   placeholder="Misal: Arena Belajar LMS" 
                                   required 
                                   maxlength="255">
                        </div>

                        <!-- Subjudul -->
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Subjudul / Tagline Ringkas
                                </label>
                                <span class="text-2xs text-secondary-light" id="subtitleCharCount">0/255</span>
                            </div>
                            <input type="text" 
                                   name="subtitle" 
                                   id="subtitleInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($item['subtitle'] ?? '') ?>" 
                                   placeholder="Misal: Silabus LKPP Terintegrasi" 
                                   maxlength="255">
                        </div>

                        <!-- Deskripsi Lengkap -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Uraian / Deskripsi Lengkap
                                </label>
                                <span class="text-2xs text-secondary-light" id="descCharCount">0/500</span>
                            </div>
                            <textarea name="description" 
                                      id="descInput" 
                                      class="form-control radius-8" 
                                      rows="4" 
                                      placeholder="Jelaskan peran, fungsi, atau definisi dari pilar/fitur pengadaan ini..."><?= esc($item['description'] ?? '') ?></textarea>
                            <small class="text-secondary-light text-2xs d-block mt-4">
                                Uraian yang jelas membantu pengguna dan auditor memahami peran strategis fitur ini.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Visual Styling & Navigation -->
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:pallete-2-bold" class="text-primary-600 text-xl"></iconify-icon>
                        <span>Personalisasi Visual, Warna & Tautan</span>
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Warna Aksen Hex & Preset Palette -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Warna Aksen Tema (Hex)
                            </label>
                            <div class="input-group mb-10">
                                <input type="color" 
                                       class="form-control form-control-color radius-8 p-1" 
                                       id="colorPicker" 
                                       value="<?= esc($defaultColor) ?>" 
                                       style="max-width: 48px; height: 42px;">
                                <input type="text" 
                                       name="color_hex" 
                                       id="colorHexInput" 
                                       class="form-control radius-8 font-monospace fw-semibold" 
                                       value="<?= esc($defaultColor) ?>" 
                                       placeholder="#0c5aa6" 
                                       maxlength="7">
                            </div>

                            <!-- Palette Chips -->
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <small class="text-secondary-light text-2xs me-1">Pilihan Cepat:</small>
                                <?php 
                                $palettes = [
                                    '#0c5aa6' => 'Theme Blue',
                                    '#0284c7' => 'Sky Blue',
                                    '#10b981' => 'Emerald',
                                    '#f59e0b' => 'Amber',
                                    '#ef4444' => 'Crimson',
                                    '#6366f1' => 'Indigo',
                                    '#8b5cf6' => 'Purple',
                                    '#475569' => 'Slate',
                                ];
                                foreach ($palettes as $hex => $label): ?>
                                    <button type="button" 
                                            class="color-chip-btn border-0 rounded-circle transition-2" 
                                            style="width: 24px; height: 24px; background: <?= $hex ?>;" 
                                            data-color="<?= $hex ?>" 
                                            title="<?= $label ?> (<?= $hex ?>)"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Ikon Solar Iconify & Quick Chips -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Nama Ikon Iconify
                            </label>
                            <div class="input-group mb-10">
                                <span class="input-group-text bg-neutral-100 border-end-0 radius-start-8" id="iconPreviewBadge">
                                    <iconify-icon icon="<?= esc($defaultIcon) ?>" class="text-xl" id="iconInputLivePreview"></iconify-icon>
                                </span>
                                <input type="text" 
                                       name="icon_class" 
                                       id="iconInput" 
                                       class="form-control radius-end-8 font-monospace text-sm" 
                                       value="<?= esc($defaultIcon) ?>" 
                                       placeholder="solar:star-bold">
                            </div>

                            <!-- Quick Icon Presets -->
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <small class="text-secondary-light text-2xs me-1">Ikon Populer:</small>
                                <?php
                                $iconPresets = [
                                    'solar:square-academic-cap-bold' => 'Diklat',
                                    'solar:folder-with-files-bold'   => 'Bunga Rampai',
                                    'solar:document-text-bold'        => 'Regulasi',
                                    'solar:chat-round-dots-bold'      => 'Konsultasi',
                                    'solar:verified-check-bold'       => 'Verifikasi',
                                    'solar:shield-check-bold'         => 'Keamanan',
                                    'solar:lock-keyhole-bold'         => 'Kunci/Privasi',
                                    'solar:star-bold'                 => 'Bintang',
                                    'solar:medal-ribbons-star-bold'   => 'Medali',
                                    'solar:city-bold'                 => 'Daerah',
                                ];
                                foreach ($iconPresets as $iconCode => $iconName): ?>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-neutral-300 radius-4 px-6 py-2 icon-preset-btn text-2xs d-inline-flex align-items-center gap-1" 
                                            data-icon="<?= $iconCode ?>" 
                                            title="<?= $iconName ?>">
                                        <iconify-icon icon="<?= $iconCode ?>"></iconify-icon>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Tautan Navigasi (URL) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Tautan Navigasi (URL / Rute)
                            </label>
                            <input type="text" 
                                   name="link_url" 
                                   id="linkUrlInput" 
                                   class="form-control radius-8 font-monospace text-sm" 
                                   value="<?= esc($item['link_url'] ?? '') ?>" 
                                   placeholder="Misal: arena-belajar atau regulasi">
                            
                            <!-- Quick URL chips -->
                            <div class="d-flex align-items-center gap-1 flex-wrap mt-8">
                                <small class="text-secondary-light text-2xs me-1">Rute Begawi:</small>
                                <?php
                                $urlPresets = ['arena-belajar', 'bunga-rampai', 'regulasi', 'kontak', 'verifikasi-sertifikat', 'statistik'];
                                foreach ($urlPresets as $u): ?>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-neutral-200 text-secondary-light radius-4 px-6 py-2 url-preset-btn text-2xs" 
                                            data-url="<?= $u ?>">
                                        <?= $u ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Urutan Tampil & Status Tayang -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Urutan Tampil (Sort)
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sortOrderInput" 
                                   class="form-control radius-8 fw-bold text-center" 
                                   value="<?= esc($defaultSort) ?>" 
                                   min="1" 
                                   max="99">
                            <small class="text-secondary-light text-2xs d-block mt-4">Angka kecil tampil di awal.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Status Publikasi
                            </label>
                            <select name="is_active" id="isActiveInput" class="form-select radius-8">
                                <option value="1" <?= ($item['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Tayang)</option>
                                <option value="0" <?= ($item['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Draf)</option>
                            </select>
                            <small class="text-secondary-light text-2xs d-block mt-4">Draf disembunyikan.</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-20 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($section)) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-10 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:diskette-bold" class="text-xl"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan' : 'Buat Item Baru' ?></span>
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
                                Live Interactive
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-24 bg-light">
                        <div class="text-center mb-16">
                            <small class="text-secondary-light text-2xs">
                                Visualisasi kartu di portal publik sesuai input form Anda:
                            </small>
                        </div>

                        <!-- Card Preview Container -->
                        <div class="card border-0 shadow-sm radius-12 p-24 transition-2 mx-auto" 
                             id="liveCardPreview" 
                             style="background: #ffffff; border-top: 4px solid <?= esc($defaultColor) ?> !important; max-width: 360px;">
                            
                            <div class="d-flex align-items-center justify-content-between mb-16">
                                <div class="radius-12 d-flex align-items-center justify-content-center text-white shadow-xs" 
                                     id="liveCardIconBox"
                                     style="width: 52px; height: 52px; background: <?= esc($defaultColor) ?>;">
                                    <iconify-icon icon="<?= esc($defaultIcon) ?>" class="text-2xl" id="liveCardIcon"></iconify-icon>
                                </div>
                                
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="fw-bold font-monospace text-xs px-8 py-3 radius-4" 
                                          id="liveCardCodeBadge"
                                          style="background: <?= esc($defaultColor) ?>15; color: <?= esc($defaultColor) ?>;">
                                        <?= esc($item['code'] ?? '01') ?>
                                    </span>
                                    <span class="badge radius-4 text-2xs px-8 py-2 fw-medium" 
                                          id="liveCardTagBadge"
                                          style="background: <?= esc($defaultColor) ?>15; color: <?= esc($defaultColor) ?>;">
                                        <?= esc($item['badge_text'] ?? 'Tag Label') ?>
                                    </span>
                                </div>
                            </div>

                            <h6 class="fw-bold text-neutral-900 mb-4" id="liveCardTitle">
                                <?= esc($item['title'] ?? 'Judul Fitur Highlight') ?>
                            </h6>
                            <small class="fw-medium text-secondary-light d-block mb-12" id="liveCardSubtitle">
                                <?= esc($item['subtitle'] ?? 'Subjudul / Tagline') ?>
                            </small>
                            <p class="text-secondary-light text-xs mb-16" id="liveCardDesc" style="line-height: 1.6;">
                                <?= esc($item['description'] ?? 'Uraian ringkas tentang pilar atau modul pengadaan barang dan jasa ini.') ?>
                            </p>

                            <div class="mt-auto pt-12 border-top" id="liveCardFooter">
                                <span class="text-decoration-none text-xs fw-semibold d-inline-flex align-items-center gap-1" 
                                      id="liveCardLink"
                                      style="color: <?= esc($defaultColor) ?>;">
                                    <span>Akses Layanan</span>
                                    <iconify-icon icon="solar:arrow-right-linear" class="text-sm"></iconify-icon>
                                </span>
                            </div>
                        </div>

                        <!-- Card Specifications Summary -->
                        <div class="mt-20 p-16 radius-8 bg-white border">
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Warna Aksen:</span>
                                <span class="font-monospace fw-bold" id="liveSummaryColor"><?= esc($defaultColor) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Ikon:</span>
                                <span class="font-monospace text-2xs" id="liveSummaryIcon"><?= esc($defaultIcon) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 text-xs">
                                <span class="text-secondary-light">Urutan / Status:</span>
                                <span class="fw-semibold" id="liveSummaryStatus">
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
    // Elements
    const sectionSelect      = document.getElementById('sectionKeyInput');
    const codeInput          = document.getElementById('codeInput');
    const badgeInput         = document.getElementById('badgeInput');
    const titleInput         = document.getElementById('titleInput');
    const subtitleInput      = document.getElementById('subtitleInput');
    const descInput          = document.getElementById('descInput');
    const colorPicker        = document.getElementById('colorPicker');
    const colorHexInput      = document.getElementById('colorHexInput');
    const iconInput          = document.getElementById('iconInput');
    const iconInputLive      = document.getElementById('iconInputLivePreview');
    const linkUrlInput       = document.getElementById('linkUrlInput');
    const sortOrderInput     = document.getElementById('sortOrderInput');
    const isActiveInput      = document.getElementById('isActiveInput');

    // Live Preview Elements
    const liveCardPreview    = document.getElementById('liveCardPreview');
    const liveCardIconBox    = document.getElementById('liveCardIconBox');
    const liveCardIcon       = document.getElementById('liveCardIcon');
    const liveCardCodeBadge  = document.getElementById('liveCardCodeBadge');
    const liveCardTagBadge   = document.getElementById('liveCardTagBadge');
    const liveCardTitle      = document.getElementById('liveCardTitle');
    const liveCardSubtitle   = document.getElementById('liveCardSubtitle');
    const liveCardDesc       = document.getElementById('liveCardDesc');
    const liveCardLink       = document.getElementById('liveCardLink');

    // Summary Elements
    const liveSummaryColor   = document.getElementById('liveSummaryColor');
    const liveSummaryIcon    = document.getElementById('liveSummaryIcon');
    const liveSummaryOrder   = document.getElementById('liveSummaryOrder');
    const liveSummaryActive  = document.getElementById('liveSummaryActive');

    // Counters
    const titleCharCount     = document.getElementById('titleCharCount');
    const subtitleCharCount  = document.getElementById('subtitleCharCount');
    const descCharCount      = document.getElementById('descCharCount');

    function updateLivePreview() {
        const title    = titleInput.value.trim() || 'Judul Fitur Highlight';
        const subtitle = subtitleInput.value.trim() || 'Subjudul / Tagline';
        const desc     = descInput.value.trim() || 'Uraian ringkas tentang pilar atau modul pengadaan barang dan jasa ini.';
        const code     = codeInput.value.trim();
        const badge    = badgeInput.value.trim();
        const color    = colorHexInput.value.trim() || '#0c5aa6';
        const icon     = iconInput.value.trim() || 'solar:star-bold';
        const order    = sortOrderInput.value || '1';
        const active   = (isActiveInput.value === '1');

        // Text & Labels
        liveCardTitle.textContent = title;
        liveCardSubtitle.textContent = subtitle;
        liveCardDesc.textContent = desc;

        if (code) {
            liveCardCodeBadge.textContent = code;
            liveCardCodeBadge.style.display = '';
        } else {
            liveCardCodeBadge.style.display = 'none';
        }

        if (badge) {
            liveCardTagBadge.textContent = badge;
            liveCardTagBadge.style.display = '';
        } else {
            liveCardTagBadge.style.display = 'none';
        }

        // Color updates
        liveCardPreview.style.borderTopColor = color;
        liveCardIconBox.style.backgroundColor = color;
        liveCardCodeBadge.style.backgroundColor = color + '18';
        liveCardCodeBadge.style.color = color;
        liveCardTagBadge.style.backgroundColor = color + '18';
        liveCardTagBadge.style.color = color;
        liveCardLink.style.color = color;

        // Icons
        liveCardIcon.setAttribute('icon', icon);
        if (iconInputLive) {
            iconInputLive.setAttribute('icon', icon);
        }

        // Summary
        liveSummaryColor.textContent = color;
        liveSummaryIcon.textContent = icon;
        liveSummaryOrder.textContent = order;
        if (active) {
            liveSummaryActive.textContent = 'Aktif';
            liveSummaryActive.className = 'text-success-main fw-semibold';
        } else {
            liveSummaryActive.textContent = 'Nonaktif';
            liveSummaryActive.className = 'text-warning-main fw-semibold';
        }

        // Char counts
        if (titleCharCount) titleCharCount.textContent = `${titleInput.value.length}/255`;
        if (subtitleCharCount) subtitleCharCount.textContent = `${subtitleInput.value.length}/255`;
        if (descCharCount) descCharCount.textContent = `${descInput.value.length}/500`;
    }

    // Input Listeners
    [titleInput, subtitleInput, descInput, codeInput, badgeInput, sortOrderInput, isActiveInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLivePreview);
            el.addEventListener('change', updateLivePreview);
        }
    });

    // Color picker syncing
    if (colorPicker && colorHexInput) {
        colorPicker.addEventListener('input', function () {
            colorHexInput.value = colorPicker.value;
            updateLivePreview();
        });
        colorHexInput.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(colorHexInput.value)) {
                colorPicker.value = colorHexInput.value;
            }
            updateLivePreview();
        });
    }

    // Palette Chips
    const colorChips = document.querySelectorAll('.color-chip-btn');
    colorChips.forEach(chip => {
        chip.addEventListener('click', function () {
            const hex = this.getAttribute('data-color');
            colorHexInput.value = hex;
            colorPicker.value = hex;
            updateLivePreview();
        });
    });

    // Icon input & chips
    if (iconInput) {
        iconInput.addEventListener('input', updateLivePreview);
    }
    const iconPresets = document.querySelectorAll('.icon-preset-btn');
    iconPresets.forEach(btn => {
        btn.addEventListener('click', function () {
            const icon = this.getAttribute('data-icon');
            iconInput.value = icon;
            updateLivePreview();
        });
    });

    // URL presets
    const urlPresets = document.querySelectorAll('.url-preset-btn');
    urlPresets.forEach(btn => {
        btn.addEventListener('click', function () {
            linkUrlInput.value = this.getAttribute('data-url');
            updateLivePreview();
        });
    });

    // Section change listener
    if (sectionSelect) {
        sectionSelect.addEventListener('change', function () {
            const selectedOpt = sectionSelect.options[sectionSelect.selectedIndex];
            const secColor = selectedOpt.getAttribute('data-color');
            const secIcon  = selectedOpt.getAttribute('data-icon');

            // If user hasn't explicitly customized, suggest default colors/icons
            if (secColor && (!colorHexInput.value || colorHexInput.value === '#0c5aa6')) {
                colorHexInput.value = secColor;
                colorPicker.value = secColor;
            }
            if (secIcon && (!iconInput.value || iconInput.value === 'solar:star-bold')) {
                iconInput.value = secIcon;
            }
            updateLivePreview();
        });
    }

    // Initial render
    updateLivePreview();
});
</script>

<?= $this->endSection() ?>
