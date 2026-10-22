<?php

/**
 * Reusable Operational Sub-Navigation for Kelola Landing Page
 * Displays all 10 sub-features as active navigation pills so no feature is hidden.
 *
 * @var string $section 'promo'|'profile'|'portal'|'team' (retained for backward compatibility)
 * @var string $activeSub current active tab
 */
$section   = $section ?? 'promo';
$activeSub = $activeSub ?? 'slider';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <!-- 1. Slider & Hero -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'slider' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/slider') ?>">
                    <iconify-icon icon="solar:slider-vertical-outline" class="text-base"></iconify-icon>
                    <span>Slider Hero</span>
                </a>
            </li>

            <!-- 2. Pusat Carousel & Sorotan -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'sorotan-carousel' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/sorotan-carousel') ?>">
                    <iconify-icon icon="solar:stars-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Sorotan &amp; Carousel</span>
                </a>
            </li>

            <!-- 3. Popup Pengumuman -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'pengumuman' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/pengumuman') ?>">
                    <iconify-icon icon="solar:bell-bing-outline" class="text-base"></iconify-icon>
                    <span>Pengumuman</span>
                </a>
            </li>

            <!-- 4. Banner Halaman Publik -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'banner-halaman' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/banner-halaman') ?>">
                    <iconify-icon icon="solar:gallery-wide-outline" class="text-base"></iconify-icon>
                    <span>Banner Halaman</span>
                </a>
            </li>

            <!-- 5. Profil Lembaga & Nilai PATIK -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'profil-lembaga' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/profil-lembaga') ?>">
                    <iconify-icon icon="solar:city-outline" class="text-base"></iconify-icon>
                    <span>Profil Lembaga</span>
                </a>
            </li>

            <!-- 6. Pilar & Hierarki Hukum -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'pilar-hierarki' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/pilar-hierarki') ?>">
                    <iconify-icon icon="solar:widget-2-outline" class="text-base"></iconify-icon>
                    <span>Pilar &amp; Hierarki</span>
                </a>
            </li>

            <!-- 7. Portal Eksternal Mitra -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'portal-eksternal' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/portal-eksternal') ?>">
                    <iconify-icon icon="solar:link-circle-outline" class="text-base"></iconify-icon>
                    <span>Portal Mitra</span>
                </a>
            </li>

            <!-- 8. Kategori Layanan -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'kategori-layanan' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/kategori-layanan') ?>">
                    <iconify-icon icon="solar:layers-outline" class="text-base"></iconify-icon>
                    <span>Kategori Layanan</span>
                </a>
            </li>

            <!-- 9. Tim Inovator -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'tim' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/tim') ?>">
                    <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-base"></iconify-icon>
                    <span>Tim Inovator</span>
                </a>
            </li>

            <!-- 10. Testimoni Pengguna -->
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'testimoni' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/landing/testimoni') ?>">
                    <iconify-icon icon="solar:chat-round-line-outline" class="text-base"></iconify-icon>
                    <span>Testimoni</span>
                </a>
            </li>
        </ul>
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:info-circle-outline" class="align-middle me-1"></iconify-icon>
            Kelola Landing Page Publik
        </div> -->
    </div>
</div>