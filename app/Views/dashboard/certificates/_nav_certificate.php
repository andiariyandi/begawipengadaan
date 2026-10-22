<?php
/**
 * Reusable Operational Sub-Navigation for Sertifikat
 *
 * @var string $activeSub 'overview'|'riwayat'|'batch'|'templates'|'verification'
 */
$activeSub = $activeSub ?? 'riwayat';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'riwayat' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sertifikat/riwayat') ?>">
                    <iconify-icon icon="solar:diploma-outline" class="text-base"></iconify-icon>
                    <span>Master Arsip Sertifikat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'batch' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sertifikat/batch') ?>">
                    <iconify-icon icon="solar:layers-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Penerbitan Batch Masal</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'templates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sertifikat/template') ?>">
                    <iconify-icon icon="solar:palette-outline" class="text-base"></iconify-icon>
                    <span>Desain Template Sertifikat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-2 text-primary-light hover-bg-neutral-100" href="<?= site_url('dashboard/sistem/approval') ?>">
                    <iconify-icon icon="solar:shield-check-outline" class="text-base text-warning-main"></iconify-icon>
                    <span>Sentral Approval Hub</span>
                </a>
            </li>
        </ul>
        <div class="d-none d-md-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
            Pusat Kredensial &amp; Sertifikasi
        </div>
    </div>
</div>
