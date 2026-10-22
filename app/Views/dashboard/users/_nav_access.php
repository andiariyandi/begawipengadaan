<?php

/**
 * Reusable Sub-Navigation for Manajemen Akses & RBAC
 *
 * @var string $activeSub 'users'|'roles'|'role-requests'|'verification'|'profile-changes'|'organizations'|'procurement-roles'|'procurement-assignments'
 */
$activeSub = $activeSub ?? 'users';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'users' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/pengguna') ?>">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="text-base"></iconify-icon>
                    <span>Pengguna</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'verification' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/verifikasi-profil') ?>">
                    <iconify-icon icon="solar:user-check-outline" class="text-base"></iconify-icon>
                    <span>Verifikasi Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'profile-changes' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>">
                    <iconify-icon icon="solar:user-hand-up-outline" class="text-base"></iconify-icon>
                    <span>Perubahan Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['roles', 'permissions', 'matrix'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/role') ?>">
                    <iconify-icon icon="solar:shield-user-outline" class="text-base"></iconify-icon>
                    <span>Role &amp; Hak Akses</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'role-requests' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/permohonan-role') ?>">
                    <iconify-icon icon="solar:diploma-outline" class="text-base"></iconify-icon>
                    <span>Permohonan Role</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'organizations' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/unit-organisasi') ?>">
                    <iconify-icon icon="solar:buildings-2-outline" class="text-base"></iconify-icon>
                    <span>Unit Organisasi (OPD)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'procurement-roles' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/peran-pengadaan') ?>">
                    <iconify-icon icon="solar:medal-star-outline" class="text-base"></iconify-icon>
                    <span>Peran Pengadaan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'procurement-assignments' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/penugasan-pengadaan') ?>">
                    <iconify-icon icon="solar:user-id-outline" class="text-base"></iconify-icon>
                    <span>Penugasan Peran</span>
                </a>
            </li>
        </ul>
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:lock-keyhole-outline" class="align-middle me-1"></iconify-icon>
            Pusat Otoritas &amp; Keamanan Identitas
        </div> -->
    </div>
</div>