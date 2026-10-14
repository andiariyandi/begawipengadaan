<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-dark"><?= esc($pageTitle ?? 'Lupa Kata Sandi') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Masukkan alamat email akun Anda untuk menerima instruksi pemulihan kata sandi.') ?></p>
</div>

<!-- Flash Alerts -->
<?php if ($success = session()->getFlashdata('message') ?: session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-success-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($success) ?></div>
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

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-20 radius-8 py-12 px-16" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl text-danger-main flex-shrink-0"></iconify-icon>
            <strong class="text-sm">Kendala Validasi Email:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/lupa-kata-sandi') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-20">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="reset-email">Alamat Email Terdaftar</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:letter-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="email"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="reset-email"
                name="email"
                value="<?= esc(old('email')) ?>"
                placeholder="nama@instansi.go.id"
                autocomplete="email"
                required>
        </div>
    </div>

    <div class="p-16 bg-neutral-50 border border-neutral-200 radius-12 mb-24 d-flex align-items-start gap-3">
        <iconify-icon icon="solar:shield-warning-bold" class="text-xl text-primary-600 flex-shrink-0 mt-1"></iconify-icon>
        <p class="mb-0 text-xs text-secondary-light">
            Demi keamanan akun Anda, tautan pengaturan ulang kata sandi hanya berlaku selama <strong>1 jam</strong> sejak permohonan diajukan.
        </p>
    </div>

    <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-100 radius-12 d-flex align-items-center justify-content-center gap-2 shadow-sm">
        <span>Kirim Tautan Pemulihan</span>
        <iconify-icon icon="solar:plain-bold" class="text-lg"></iconify-icon>
    </button>

    <div class="mt-28 p-16 bg-neutral-50 border border-neutral-200 radius-12 text-center text-sm">
        <p class="mb-0 text-secondary-light">
            Sudah ingat kata sandi Anda?
            <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-bold ms-1">Masuk Sekarang</a>
        </p>
    </div>

    <div class="mt-20 text-center text-sm">
        <a href="<?= site_url('/') ?>" class="text-secondary-light d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali ke Beranda Utama</span>
        </a>
    </div>

</form>
<?= $this->endSection() ?>