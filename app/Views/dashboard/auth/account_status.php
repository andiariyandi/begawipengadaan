<?= $this->extend('dashboard/auth/layouts/auth') ?>
<?= $this->section('content') ?>
<h4 class="mb-12"><?= esc($pageTitle ?? 'Status Akun') ?></h4>
<p class="text-secondary-light mb-20"><?= esc($pageDescription ?? 'Ringkasan status akun Anda.') ?></p>

<?php if ($message = session()->getFlashdata('message')): ?>
    <div class="alert alert-success mb-20"><?= esc($message) ?></div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-20"><?= esc($error) ?></div>
<?php endif; ?>

<?php if (! empty($user)): ?>
    <?php
    $status = $user['account_status'] ?? 'draft';
    $badgeClass = match ($status) {
        'active' => 'bg-success-focus text-success-main border-success',
        'pending_verification', 'submitted', 'draft' => 'bg-warning-focus text-warning-main border-warning',
        'revision_required' => 'bg-info-focus text-info-main border-info',
        'rejected', 'inactive', 'suspended', 'archived' => 'bg-danger-focus text-danger-main border-danger',
        default => 'bg-neutral-100 text-neutral-600 border-neutral-300',
    };
    $statusLabel = match ($status) {
        'active' => 'Akun Aktif',
        'pending_verification' => 'Menunggu Verifikasi',
        'submitted' => 'Telah Diajukan',
        'draft' => 'Draft Pendaftaran',
        'revision_required' => 'Perlu Perbaikan Data',
        'rejected' => 'Pendaftaran Ditolak',
        'inactive' => 'Akun Tidak Aktif',
        'suspended' => 'Akun Ditangguhkan',
        'archived' => 'Akun Diarsipkan',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
    $statusDesc = match ($status) {
        'active' => 'Akun Anda sudah aktif. Silakan masuk kembali untuk melanjutkan ke Workspace.',
        'pending_verification', 'submitted', 'draft' => 'Pendaftaran akun Anda telah diterima dan sedang dalam antrean verifikasi oleh Operator atau Administrator. Anda akan dapat masuk setelah akun diverifikasi.',
        'revision_required' => 'Data pendaftaran Anda membutuhkan perbaikan atau kelengkapan tambahan. Silakan hubungi tim verifikator atau tunggu instruksi perbaikan melalui email.',
        'rejected' => 'Mohon maaf, pengajuan akun Anda ditolak. Silakan hubungi administrator layanan BegawiPengadaan untuk informasi lebih rinci.',
        'inactive', 'suspended' => 'Akun Anda sedang dinonaktifkan sementara. Silakan hubungi administrator jika Anda memerlukan bantuan akses.',
        default => 'Status akun saat ini: ' . $status . '.',
    };
    ?>

    <div class="card border radius-12 p-20 mb-24 bg-neutral-50">
        <div class="d-flex align-items-center justify-content-between mb-12">
            <span class="text-secondary-light text-sm">Nama Akun:</span>
            <strong class="text-primary-light"><?= esc($user['name'] ?? $user['username'] ?? '-') ?></strong>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-16">
            <span class="text-secondary-light text-sm">Status Saat Ini:</span>
            <span class="badge px-12 py-6 border radius-8 fw-semibold <?= $badgeClass ?>"><?= esc($statusLabel) ?></span>
        </div>
        <p class="text-sm text-secondary-light mb-0 border-top pt-12"><?= esc($statusDesc) ?></p>
    </div>
<?php else: ?>
    <p class="text-secondary-light">Silakan masuk untuk melihat status akun.</p>
<?php endif; ?>

<div class="d-flex gap-12">
    <a href="<?= site_url('/') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Beranda</a>
    <a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Kembali ke Masuk</a>
</div>
<?= $this->endSection() ?>