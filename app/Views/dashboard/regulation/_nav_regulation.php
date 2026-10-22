<?php

/**
 * Reusable Operational Sub-Navigation for Regulasi PBJ
 *
 * @var string $activeSub 'dokumen'|'categories'|'versi'|'relasi'|'tahapan-pbj'|'kompetensi-terkait'|'log-download'
 */
$activeSub = $activeSub ?? 'dokumen';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'dokumen' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/dokumen') ?>">
                    <iconify-icon icon="solar:document-text-outline" class="text-base"></iconify-icon>
                    <span>Naskah Dokumen</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['categories', 'kategori'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/kategori') ?>">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-base"></iconify-icon>
                    <span>Kategori Regulasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'versi' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/versi') ?>">
                    <iconify-icon icon="solar:history-outline" class="text-base"></iconify-icon>
                    <span>Riwayat Versi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'relasi' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/relasi') ?>">
                    <iconify-icon icon="solar:git-commit-outline" class="text-base"></iconify-icon>
                    <span>Relasi Silsilah</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'tahapan-pbj' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>">
                    <iconify-icon icon="solar:route-outline" class="text-base"></iconify-icon>
                    <span>Tahapan Siklus PBJ</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'kompetensi-terkait' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>">
                    <iconify-icon icon="solar:medal-star-outline" class="text-base"></iconify-icon>
                    <span>Kompetensi Terkait</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'log-download' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/log-download') ?>">
                    <iconify-icon icon="solar:file-download-outline" class="text-base"></iconify-icon>
                    <span>Log Unduhan</span>
                </a>
            </li>
        </ul>
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
            Konsol Regulasi &amp; Siklus Hukum
        </div> -->
    </div>
</div>