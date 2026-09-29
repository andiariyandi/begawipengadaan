<?php $activeMenu = $activeMenu ?? ''; ?>
<aside class="sidebar">
    <button type="button" class="sidebar-close-btn"><i class="ri-close-line"></i></button>
    <div>
        <a href="<?= site_url('dashboard') ?>" class="sidebar-logo">
            <span class="fw-bold fs-5">BegawiPBJ</span>
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="<?= $activeMenu === 'dashboard-home' ? 'active-page' : '' ?>"><a href="<?= site_url('dashboard') ?>"><iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon><span>Dashboard</span></a></li>

            <li class="sidebar-menu-group-title">Administrasi</li>
            <li><a href="<?= site_url('dashboard/pengguna') ?>"><iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon><span>Pengguna</span></a></li>
            <li><a href="<?= site_url('dashboard/approval') ?>"><iconify-icon icon="solar:clipboard-check-outline" class="menu-icon"></iconify-icon><span>Approval</span></a></li>
            <li><a href="<?= site_url('dashboard/rbac/role') ?>"><iconify-icon icon="solar:shield-user-outline" class="menu-icon"></iconify-icon><span>Role & Permission</span></a></li>
            <li><a href="<?= site_url('dashboard/organisasi') ?>"><iconify-icon icon="solar:buildings-2-outline" class="menu-icon"></iconify-icon><span>Organisasi</span></a></li>
            <li><a href="<?= site_url('dashboard/audit') ?>"><iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon><span>Audit Trail</span></a></li>

            <li class="sidebar-menu-group-title">Modul</li>
            <li><a href="<?= site_url('dashboard/pembelajaran/program') ?>"><iconify-icon icon="solar:book-2-outline" class="menu-icon"></iconify-icon><span>Arena Belajar</span></a></li>
            <li><a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>"><iconify-icon icon="solar:notebook-bookmark-outline" class="menu-icon"></iconify-icon><span>Bunga Rampai</span></a></li>
            <li><a href="<?= site_url('dashboard/regulasi') ?>"><iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon><span>Regulasi</span></a></li>
            <li><a href="<?= site_url('dashboard/komunikasi/event') ?>"><iconify-icon icon="solar:calendar-outline" class="menu-icon"></iconify-icon><span>Berita & Event</span></a></li>
            <li><a href="<?= site_url('dashboard/sertifikat') ?>"><iconify-icon icon="solar:diploma-verified-outline" class="menu-icon"></iconify-icon><span>Sertifikat</span></a></li>
            <li><a href="<?= site_url('dashboard/intelligence') ?>"><iconify-icon icon="solar:chart-2-outline" class="menu-icon"></iconify-icon><span>Intelligence</span></a></li>

            <li class="sidebar-menu-group-title">Sistem</li>
            <li><a href="<?= site_url('dashboard/laporan') ?>"><iconify-icon icon="solar:graph-up-outline" class="menu-icon"></iconify-icon><span>Laporan</span></a></li>
            <li><a href="<?= site_url('dashboard/pengaturan') ?>"><iconify-icon icon="solar:settings-outline" class="menu-icon"></iconify-icon><span>Pengaturan</span></a></li>
        </ul>
    </div>
</aside>