<?php
/**
 * Reusable Operational Sub-Navigation for Regulasi PBJ
 *
 * @var string $section 'regulations'|'stages'
 * @var string $activeSub current active tab
 */
$section   = $section ?? 'regulations';
$activeSub = $activeSub ?? '';
?>

<?php if ($section === 'regulations'): ?>
    <div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'dokumen' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/dokumen') ?>">
                        <iconify-icon icon="solar:document-text-outline" class="text-base"></iconify-icon>
                        <span>Naskah Dokumen Regulasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'versi' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/versi') ?>">
                        <iconify-icon icon="solar:history-outline" class="text-base"></iconify-icon>
                        <span>Riwayat Versi Dokumen</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'relasi' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/relasi') ?>">
                        <iconify-icon icon="solar:git-commit-outline" class="text-base"></iconify-icon>
                        <span>Relasi Silsilah Hukum</span>
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-block text-2xs text-secondary-light">
                <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                Konsol Regulasi Terpadu
            </div>
        </div>
    </div>
<?php elseif ($section === 'stages'): ?>
    <div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'tahapan-pbj' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/tahapan-pbj') ?>">
                        <iconify-icon icon="solar:route-outline" class="text-base"></iconify-icon>
                        <span>Tahapan Siklus PBJ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'kompetensi-terkait' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>">
                        <iconify-icon icon="solar:medal-star-outline" class="text-base"></iconify-icon>
                        <span>Kompetensi Terkait</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'log-download' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/regulasi/log-download') ?>">
                        <iconify-icon icon="solar:file-download-outline" class="text-base"></iconify-icon>
                        <span>Log Unduhan Dokumen</span>
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-block text-2xs text-secondary-light">
                <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
                Pemetaan Siklus &amp; Log
            </div>
        </div>
    </div>
<?php endif; ?>
