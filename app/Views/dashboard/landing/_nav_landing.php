<?php

/**
 * Reusable Operational Sub-Navigation for Kelola Landing Page
 * Displays all 10 sub-features as responsive, modern navigation tabs with mobile-friendly horizontal swipe.
 *
 * @var string $section 'promo'|'profile'|'portal'|'team' (retained for backward compatibility)
 * @var string $activeSub current active tab
 */
$section   = $section ?? 'promo';
$activeSub = $activeSub ?? 'slider';

$tabs = [
    [
        'id'    => 'slider',
        'url'   => site_url('dashboard/landing/slider'),
        'icon'  => 'solar:slider-vertical-outline',
        'label' => 'Slider Hero',
    ],
    [
        'id'    => 'sorotan-carousel',
        'url'   => site_url('dashboard/landing/sorotan-carousel'),
        'icon'  => 'solar:stars-minimalistic-outline',
        'label' => 'Sorotan & Carousel',
    ],
    [
        'id'    => 'pengumuman',
        'url'   => site_url('dashboard/landing/pengumuman'),
        'icon'  => 'solar:bell-bing-outline',
        'label' => 'Pengumuman',
    ],
    [
        'id'    => 'profil-lembaga',
        'url'   => site_url('dashboard/landing/profil-lembaga'),
        'icon'  => 'solar:city-outline',
        'label' => 'Profil Lembaga',
    ],
    [
        'id'    => 'pilar-hierarki',
        'url'   => site_url('dashboard/landing/pilar-hierarki'),
        'icon'  => 'solar:widget-2-outline',
        'label' => 'Pilar & Hierarki',
    ],
    [
        'id'    => 'portal-eksternal',
        'url'   => site_url('dashboard/landing/portal-eksternal'),
        'icon'  => 'solar:link-circle-outline',
        'label' => 'Portal Mitra',
    ],
    [
        'id'    => 'tim',
        'url'   => site_url('dashboard/landing/tim'),
        'icon'  => 'solar:users-group-two-rounded-outline',
        'label' => 'Tim Inovator',
    ],
    [
        'id'    => 'testimoni',
        'url'   => site_url('dashboard/landing/testimoni'),
        'icon'  => 'solar:chat-round-line-outline',
        'label' => 'Testimoni',
    ],
];
?>

<div class="card border radius-12 shadow-none bg-base mb-24 p-12">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="landing-tabs-scroll w-100 overflow-x-auto pb-1 pb-md-0" style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
            <ul class="nav nav-pills gap-2 flex-nowrap mb-0" id="landingNavPills" style="white-space: nowrap;">
                <?php foreach ($tabs as $index => $tab): ?>
                    <?php $isActive = ($activeSub === $tab['id']); ?>
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-2 <?= $isActive ? 'active bg-primary-600 text-white shadow-xs' : 'text-secondary-light border border-neutral-200 bg-neutral-50 hover-bg-neutral-100 hover-text-primary-600' ?>" 
                           href="<?= $tab['url'] ?>" 
                           data-tab-id="<?= esc($tab['id']) ?>">
                            <iconify-icon icon="<?= esc($tab['icon']) ?>" class="text-base flex-shrink-0"></iconify-icon>
                            <span><?= esc($tab['label']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="d-none d-xl-flex align-items-center flex-shrink-0 ps-2 border-start">
            <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 text-xs d-flex align-items-center gap-1" title="Buka Beranda Publik di Tab Baru">
                <iconify-icon icon="solar:square-top-down-outline" class="text-sm"></iconify-icon>
                <span>Lihat Beranda</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-scroll active tab into view horizontally on mobile
    const activeTab = document.querySelector('#landingNavPills .nav-link.active');
    if (activeTab) {
        activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
});
</script>
