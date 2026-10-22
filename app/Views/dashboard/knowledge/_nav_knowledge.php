<?php

/**
 * Reusable Operational Sub-Navigation for Bunga Rampai (Knowledge Management)
 * Displays all 8 sub-features as active navigation pills so no feature is hidden.
 *
 * @var string $section 'articles'|'media'|'taxonomy' (retained for backward compatibility)
 * @var string $activeSub current active tab
 */
$section   = $section ?? 'articles';
$activeSub = $activeSub ?? 'articles';

$isMediaScope = in_array($activeSub, ['media-gallery', 'infographics', 'videos', 'gallery', 'media'], true);
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <!-- 1. Artikel PBJ -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'articles' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/artikel') ?>">
                    <iconify-icon icon="solar:document-text-outline" class="text-base"></iconify-icon>
                    <span>Artikel PBJ</span>
                </a>
            </li>

            <!-- 2. Studi Kasus -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'case-studies' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/studi-kasus') ?>">
                    <iconify-icon icon="solar:case-outline" class="text-base"></iconify-icon>
                    <span>Studi Kasus</span>
                </a>
            </li>

            <!-- 3. Tanya Jawab (FAQ) -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'faqs' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/faq') ?>">
                    <iconify-icon icon="solar:question-circle-outline" class="text-base"></iconify-icon>
                    <span>Tanya Jawab (FAQ)</span>
                </a>
            </li>

            <!-- 4. Template Dokumen -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'templates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/template-dokumen') ?>">
                    <iconify-icon icon="solar:file-download-outline" class="text-base"></iconify-icon>
                    <span>Template Dokumen</span>
                </a>
            </li>

            <!-- 5. Media & Galeri -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $isMediaScope ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>">
                    <iconify-icon icon="solar:gallery-wide-outline" class="text-base"></iconify-icon>
                    <span>Media &amp; Galeri</span>
                </a>
            </li>

            <!-- 6. Kategori Bunga Rampai -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'categories' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/kategori') ?>">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-base"></iconify-icon>
                    <span>Kategori</span>
                </a>
            </li>

            <!-- 7. Tagar & Topik -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'tags' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/tag') ?>">
                    <iconify-icon icon="solar:tag-outline" class="text-base"></iconify-icon>
                    <span>Tagar &amp; Topik</span>
                </a>
            </li>

            <!-- 8. Bookmark Koleksi -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'bookmarks' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/bookmark') ?>">
                    <iconify-icon icon="solar:bookmark-outline" class="text-base"></iconify-icon>
                    <span>Bookmark</span>
                </a>
            </li>
        </ul>
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
            Pusat Pengetahuan &amp; Bunga Rampai PBJ
        </div> -->
    </div>

    <?php if ($isMediaScope): ?>
        <hr class="my-10 border-neutral-200">
        <div class="d-flex flex-wrap align-items-center gap-2 pt-2">
            <span class="text-2xs fw-bold text-secondary-light text-uppercase tracking-wider me-2">Format Media:</span>
            <a class="badge radius-6 px-12 py-6 text-xs fw-semibold <?= $activeSub === 'media-gallery' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-primary-light hover-bg-neutral-200' ?>" href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>">
                Semua Format
            </a>
            <a class="badge radius-6 px-12 py-6 text-xs fw-semibold <?= in_array($activeSub, ['infographics', 'media'], true) ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-primary-light hover-bg-neutral-200' ?>" href="<?= site_url('dashboard/bunga-rampai/media') ?>">
                Infografik &amp; Brosur
            </a>
            <a class="badge radius-6 px-12 py-6 text-xs fw-semibold <?= $activeSub === 'videos' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-primary-light hover-bg-neutral-200' ?>" href="<?= site_url('dashboard/bunga-rampai/video') ?>">
                Video Pembelajaran
            </a>
            <a class="badge radius-6 px-12 py-6 text-xs fw-semibold <?= $activeSub === 'gallery' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-primary-light hover-bg-neutral-200' ?>" href="<?= site_url('dashboard/bunga-rampai/galeri') ?>">
                Galeri Foto Kegiatan
            </a>
        </div>
    <?php endif; ?>
</div>