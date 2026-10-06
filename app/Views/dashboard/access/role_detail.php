<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $role = $role ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24"><div><h6 class="fw-semibold mb-1">Detail Role</h6><p class="text-secondary-light mb-0">Role: <?= esc($role['key'] ?? $roleId ?? '-') ?></p></div><a href="<?= site_url('dashboard/akses/role/' . ($roleId ?? '1') . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Role</a></div><div class="card"><div class="card-body p-24"><div class="row gy-20"><div class="col-md-6"><p class="text-secondary-light mb-4">Nama Role</p><h6 class="mb-0"><?= esc($role['name'] ?? '-') ?></h6></div><div class="col-md-6"><p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?></div><div class="col-12"><p class="text-secondary-light mb-4">Deskripsi</p><p class="mb-0"><?= esc($role['description'] ?? '-') ?></p></div></div><hr class="my-24"><h6 class="text-lg mb-16">Permission Terpasang</h6><div class="row gy-12"><?php foreach (($permissions ?? []) as $permission): ?><div class="col-md-4"><div class="border radius-8 px-16 py-12"><iconify-icon icon="solar:check-circle-outline" class="text-success-main me-6"></iconify-icon><?= esc($permission) ?></div></div><?php endforeach; ?></div></div></div>
<?php
$role = $role ?? [];
$permissions = $permissions ?? [];
$permsWithDomain = [];
foreach ($permissions as $p) {
    $domain = $p['domain'] ?? 'other';
    $permsWithDomain[$domain][] = $p;
}
?><?= $this->endSection() ?>
