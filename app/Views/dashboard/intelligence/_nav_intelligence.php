<?php

/**
 * Reusable Sub-Navigation for Government Intelligence (SDM PBJ & Kematangan Daerah)
 *
 * @var string $activeSub 'home'|'opd'|'personnel'|'gap'|'levels'|'certificates'|'certificate-types'|'histories'
 */
$activeSub = $activeSub ?? 'home';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'home' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence') ?>">
                    <iconify-icon icon="solar:radar-2-outline" class="text-base"></iconify-icon>
                    <span>Radar Eksekutif</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'opd' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/opd-unit') ?>">
                    <iconify-icon icon="solar:buildings-2-outline" class="text-base"></iconify-icon>
                    <span>Perangkat Daerah (OPD)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'personnel' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>">
                    <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-base"></iconify-icon>
                    <span>SDM JF PPBJ</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'gap' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>">
                    <iconify-icon icon="solar:chart-square-outline" class="text-base"></iconify-icon>
                    <span>Analisis Gap &amp; Formasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'levels' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/jenjang') ?>">
                    <iconify-icon icon="solar:ranking-outline" class="text-base"></iconify-icon>
                    <span>Jenjang Jabatan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'certificates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-base"></iconify-icon>
                    <span>Sertifikat Kompetensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'certificate-types' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/jenis-sertifikat-kompetensi') ?>">
                    <iconify-icon icon="solar:medal-ribbons-star-outline" class="text-base"></iconify-icon>
                    <span>Jenis Sertifikat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'histories' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/intelligence/riwayat') ?>">
                    <iconify-icon icon="solar:history-outline" class="text-base"></iconify-icon>
                    <span>Riwayat Portofolio</span>
                </a>
            </li>
        </ul>
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:crown-line-duotone" class="align-middle me-1"></iconify-icon>
            Intelijen Pengadaan Daerah
        </div> -->
    </div>
</div>