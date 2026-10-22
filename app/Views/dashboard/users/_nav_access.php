<?php
/**
 * Reusable Sub-Navigation for Manajemen Akses & RBAC
 *
 * @var string $activeSub 'users'|'roles'|'role-requests'|'verification'|'profile-changes'|'organizations'|'procurement-roles'
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
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['roles', 'role-requests'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/role') ?>">
                    <iconify-icon icon="solar:shield-user-outline" class="text-base"></iconify-icon>
                    <span>Role &amp; Hak Akses</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['verification', 'profile-changes'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/verifikasi-profil') ?>">
                    <iconify-icon icon="solar:user-check-outline" class="text-base"></iconify-icon>
                    <span>Verifikasi Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['organizations', 'procurement-roles'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/akses/unit-organisasi') ?>">
                    <iconify-icon icon="solar:buildings-2-outline" class="text-base"></iconify-icon>
                    <span>Unit &amp; Peran PBJ</span>
                </a>
            </li>
        </ul>
        <div class="d-none d-xl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:lock-keyhole-outline" class="align-middle me-1"></iconify-icon>
            Pusat Otoritas &amp; Keamanan Identitas
        </div>
    </div>
</div>
