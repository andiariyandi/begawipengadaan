<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-dark"><?= esc($pageTitle ?? 'Status Akun Pengguna') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Informasi tahapan dan status verifikasi akun Anda di platform BegawiPengadaan.') ?></p>
</div>

<!-- Flash Alerts -->
<?php if ($message = session()->getFlashdata('message') ?: session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-success-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($message) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl text-danger-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($error) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (! empty($user)): ?>
    <?php
    $status = $user['account_status'] ?? 'draft';
    $badgeClass = match ($status) {
        'active' => 'bg-success-50 text-success-main border border-success-subtle',
        'pending_verification', 'submitted', 'draft' => 'bg-warning-50 text-warning-main border border-warning-subtle',
        'revision_required' => 'bg-info-50 text-info-main border border-info-subtle',
        'rejected', 'inactive', 'suspended', 'archived' => 'bg-danger-50 text-danger-main border border-danger-subtle',
        default => 'bg-neutral-100 text-neutral-600 border border-neutral-300',
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
        default => ucfirst(str_replace('_', ' ', (string) $status)),
    };
    $statusDesc = match ($status) {
        'active' => 'Akun Anda telah berstatus aktif. Anda dapat langsung masuk untuk menggunakan fitur pembelajaran, sertifikasi, dan layanan pengadaan.',
        'pending_verification', 'submitted', 'draft' => 'Pendaftaran akun Anda telah tersimpan dan sedang menunggu peninjauan oleh tim Operator / Administrator Biro Pengadaan Barang dan Jasa Provinsi Lampung.',
        'revision_required' => 'Terdapat data profil pendaftaran yang memerlukan perbaikan. Silakan periksa pesan petunjuk atau hubungi tim bantuan.',
        'rejected' => 'Mohon maaf, permohonan pendaftaran akun Anda belum dapat disetujui. Hubungi administrator layanan untuk informasi lebih lanjut.',
        'inactive', 'suspended' => 'Akun Anda dinonaktifkan sementara. Silakan hubungi administrator bila Anda membutuhkan akses.',
        default => 'Status akun Anda saat ini: ' . $status . '.',
    };
    ?>

    <div class="card border border-neutral-200 radius-12 p-24 mb-24 bg-neutral-50 shadow-none">
        <div class="d-flex align-items-center justify-content-between mb-16 pb-12 border-bottom">
            <span class="text-secondary-light text-sm">Nama Akun:</span>
            <strong class="text-dark"><?= esc($user['name'] ?? $user['username'] ?? '-') ?></strong>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-16 pb-12 border-bottom">
            <span class="text-secondary-light text-sm">Alamat Email:</span>
            <span class="text-dark fw-medium text-sm"><?= esc($user['email'] ?? '-') ?></span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-16">
            <span class="text-secondary-light text-sm">Status Saat Ini:</span>
            <span class="badge px-12 py-6 radius-8 fw-semibold text-xs <?= $badgeClass ?>"><?= esc($statusLabel) ?></span>
        </div>
        <div class="p-16 bg-white border border-neutral-200 radius-8">
            <p class="text-xs text-secondary-light mb-0 lh-base"><?= esc($statusDesc) ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-neutral mb-24 p-16 radius-8 text-center text-sm text-secondary-light">
        Silakan masuk terlebih dahulu untuk memeriksa status akun Anda.
    </div>
<?php endif; ?>

<div class="d-flex gap-12">
    <a href="<?= site_url('/') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-14 w-50 radius-12 text-center text-secondary-light d-flex align-items-center justify-content-center gap-1">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Beranda</span>
    </a>
    <a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary text-sm fw-semibold px-12 py-14 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-1 shadow-sm">
        <span>Ke Halaman Masuk</span>
        <iconify-icon icon="solar:login-2-bold"></iconify-icon>
    </a>
</div>
<?= $this->endSection() ?>