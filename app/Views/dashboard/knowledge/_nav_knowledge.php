<?php
/**
 * Reusable Operational Sub-Navigation for Bunga Rampai (Knowledge Management)
 *
 * @var string $section 'articles'|'media'|'taxonomy'
 * @var string $activeSub current active tab
 */
$section   = $section ?? 'articles';
$activeSub = $activeSub ?? '';
?>

<?php if ($section === 'articles'): ?>
    <div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'articles' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/artikel') ?>">
                        <iconify-icon icon="solar:document-text-outline" class="text-base"></iconify-icon>
                        <span>Artikel PBJ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'case-studies' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/studi-kasus') ?>">
                        <iconify-icon icon="solar:case-outline" class="text-base"></iconify-icon>
                        <span>Studi Kasus</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'faqs' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/faq') ?>">
                        <iconify-icon icon="solar:question-circle-outline" class="text-base"></iconify-icon>
                        <span>Tanya Jawab (FAQ)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'templates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/template-dokumen') ?>">
                        <iconify-icon icon="solar:file-download-outline" class="text-base"></iconify-icon>
                        <span>Template Dokumen</span>
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-block text-2xs text-secondary-light">
                <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                Konsol Naskah &amp; Pembelajaran PBJ
            </div>
        </div>
    </div>
<?php elseif ($section === 'media'): ?>
    <div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'media-gallery' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>">
                        <iconify-icon icon="solar:gallery-wide-outline" class="text-base"></iconify-icon>
                        <span>Semua Media &amp; Galeri</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'infographics' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/media') ?>">
                        <iconify-icon icon="solar:gallery-outline" class="text-base"></iconify-icon>
                        <span>Media &amp; Infografik</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'videos' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/video') ?>">
                        <iconify-icon icon="solar:play-circle-outline" class="text-base"></iconify-icon>
                        <span>Video PBJ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'gallery' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/galeri') ?>">
                        <iconify-icon icon="solar:camera-outline" class="text-base"></iconify-icon>
                        <span>Galeri Kegiatan</span>
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-block text-2xs text-secondary-light">
                <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                Pustaka Media Terpadu
            </div>
        </div>
    </div>
<?php elseif ($section === 'taxonomy'): ?>
    <div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'categories' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/kategori') ?>">
                        <iconify-icon icon="solar:folder-with-files-outline" class="text-base"></iconify-icon>
                        <span>Kategori Bunga Rampai</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'tags' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/tag') ?>">
                        <iconify-icon icon="solar:tag-outline" class="text-base"></iconify-icon>
                        <span>Tagar &amp; Topik</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'bookmarks' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/bunga-rampai/bookmark') ?>">
                        <iconify-icon icon="solar:bookmark-outline" class="text-base"></iconify-icon>
                        <span>Bookmark Koleksi</span>
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-block text-2xs text-secondary-light">
                <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                Taksonomi &amp; Kurasi
            </div>
        </div>
    </div>
<?php endif; ?>
