<?php
/**
 * Reusable Sub-Navigation for Pusat Laporan & Universal Export
 *
 * @var string $activeReport 'learning'|'events'|'certificates'|'competency'|'users'|'export-history'
 */
$activeReport = $activeReport ?? 'learning';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'learning' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/pembelajaran') ?>">
                    <iconify-icon icon="solar:book-2-outline" class="text-base"></iconify-icon>
                    <span>Laporan Pembelajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'events' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/event') ?>">
                    <iconify-icon icon="solar:calendar-outline" class="text-base"></iconify-icon>
                    <span>Laporan Event &amp; Agenda</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'certificates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/sertifikat') ?>">
                    <iconify-icon icon="solar:diploma-outline" class="text-base"></iconify-icon>
                    <span>Laporan Sertifikat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'competency' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/kompetensi') ?>">
                    <iconify-icon icon="solar:chart-square-outline" class="text-base"></iconify-icon>
                    <span>Laporan Kompetensi SDM</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'users' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/pengguna') ?>">
                    <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-base"></iconify-icon>
                    <span>Laporan Pengguna</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeReport === 'export-history' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/laporan/riwayat-export') ?>">
                    <iconify-icon icon="solar:history-outline" class="text-base"></iconify-icon>
                    <span>Riwayat Export</span>
                </a>
            </li>
        </ul>
        <div class="d-none d-xl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>
            Format Cetak Resmi Pemprov Lampung
        </div>
    </div>
</div>
