<?php

/**
 * Reusable Operational Sub-Navigation for System Settings (Pusat Pengaturan)
 *
 * @var string $activeSetting current active setting group
 */
$activeSetting = $activeSetting ?? 'general';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <!-- Hub / Kembali -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'index' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan') ?>">
                    <iconify-icon icon="solar:widget-outline" class="text-base"></iconify-icon>
                    <span>Semua Modul</span>
                </a>
            </li>

            <!-- 1. Umum -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'general' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-umum') ?>">
                    <iconify-icon icon="solar:settings-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Umum</span>
                </a>
            </li>

            <!-- 2. Organisasi -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'organization' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-organisasi') ?>">
                    <iconify-icon icon="solar:buildings-2-outline" class="text-base"></iconify-icon>
                    <span>Organisasi</span>
                </a>
            </li>

            <!-- 3. Pembelajaran -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'learning' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-pembelajaran') ?>">
                    <iconify-icon icon="solar:book-2-outline" class="text-base"></iconify-icon>
                    <span>Pembelajaran</span>
                </a>
            </li>

            <!-- 4. Bunga Rampai -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'knowledge' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-bunga-rampai') ?>">
                    <iconify-icon icon="solar:document-text-outline" class="text-base"></iconify-icon>
                    <span>Bunga Rampai</span>
                </a>
            </li>

            <!-- 5. Regulasi -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'regulation' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-regulasi') ?>">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-base"></iconify-icon>
                    <span>Regulasi</span>
                </a>
            </li>

            <!-- 6. Berita -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'news' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-berita') ?>">
                    <iconify-icon icon="solar:newspaper-outline" class="text-base"></iconify-icon>
                    <span>Berita</span>
                </a>
            </li>

            <!-- 7. Event -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'event' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-event') ?>">
                    <iconify-icon icon="solar:calendar-date-outline" class="text-base"></iconify-icon>
                    <span>Event</span>
                </a>
            </li>

            <!-- 8. Sertifikat -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'certificate' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-sertifikat') ?>">
                    <iconify-icon icon="solar:medal-star-outline" class="text-base"></iconify-icon>
                    <span>Sertifikat</span>
                </a>
            </li>

            <!-- 9. Notifikasi -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'notification' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-notifikasi') ?>">
                    <iconify-icon icon="solar:bell-bing-outline" class="text-base"></iconify-icon>
                    <span>Notifikasi</span>
                </a>
            </li>

            <!-- 10. Integrasi -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'integration' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-integrasi') ?>">
                    <iconify-icon icon="solar:link-circle-outline" class="text-base"></iconify-icon>
                    <span>Integrasi</span>
                </a>
            </li>

            <!-- 11. Keamanan -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'security' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/pengaturan-keamanan') ?>">
                    <iconify-icon icon="solar:shield-check-outline" class="text-base"></iconify-icon>
                    <span>Keamanan</span>
                </a>
            </li>

            <!-- 12. Maintenance -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'maintenance' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/maintenance') ?>">
                    <iconify-icon icon="solar:traffic-economy-outline" class="text-base"></iconify-icon>
                    <span>Maintenance</span>
                </a>
            </li>

            <!-- 13. Statistik Trafik & Kunjungan -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-12 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSetting === 'visitor_stats' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/sistem/statistik-kunjungan') ?>">
                    <iconify-icon icon="solar:chart-2-outline" class="text-base"></iconify-icon>
                    <span>Statistik Trafik</span>
                </a>
            </li>
        </ul>
    </div>
</div>
