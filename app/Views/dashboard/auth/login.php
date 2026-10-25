<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<?php
$loginNum1 = rand(2, 9);
$loginNum2 = rand(1, 9);
session()->set('login_captcha_answer', $loginNum1 + $loginNum2);
$loginCaptchaQuestion = "{$loginNum1} + {$loginNum2}";
?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Masuk ke Akun Anda') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Silakan masukkan email dan kata sandi Anda untuk mengakses dashboard.') ?></p>
</div>

<!-- Flash Feedback Messages -->
<?php if ($success = session()->getFlashdata('success') ?: session()->getFlashdata('message')): ?>
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
            <strong class="text-sm">Kendala Validasi Masuk:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/masuk') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Honeypot Anti-Bot Field -->
    <div style="display:none !important;" aria-hidden="true">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off" value="">
    </div>

    <div class="mb-20">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="login-email">Alamat Email</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:letter-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="email"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="login-email"
                name="email"
                value="<?= esc(old('email')) ?>"
                placeholder="nama@email.com"
                autocomplete="email"
                required>
        </div>
    </div>

    <div class="mb-20">
        <div class="d-flex align-items-center justify-content-between mb-8">
            <label class="form-label fw-semibold text-primary-light text-sm mb-0" for="login-password">Kata Sandi</label>
            <a href="<?= site_url('dashboard/lupa-kata-sandi') ?>" class="text-primary-600 text-xs fw-semibold">Lupa kata sandi?</a>
        </div>
        <div class="position-relative">
            <div class="icon-field">
                <span class="icon top-50 translate-middle-y">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg text-secondary-light"></iconify-icon>
                </span>
                <input
                    type="password"
                    class="form-control h-56-px bg-neutral-50 radius-12 ps-48 pe-48"
                    id="login-password"
                    name="password"
                    placeholder="Masukkan kata sandi Anda"
                    autocomplete="current-password"
                    required>
            </div>
            <span
                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                data-toggle="#login-password"
                title="Tampilkan / Sembunyikan Kata Sandi"></span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-20">
        <div class="form-check style-check d-flex align-items-center gap-2">
            <input class="form-check-input border border-neutral-300" type="checkbox" value="1" id="remember" name="remember">
            <label class="form-check-label text-sm text-secondary-light" for="remember">Ingat sesi saya</label>
        </div>
    </div>

    <!-- Simple Math Captcha Anti-Spam -->
    <div class="p-16 bg-neutral-50 border border-neutral-200 radius-12 mb-24">
        <div class="d-flex align-items-center justify-content-between mb-8">
            <label class="form-label fw-semibold text-primary-light text-sm mb-0" for="login-captcha">
                <iconify-icon icon="solar:shield-check-bold" class="text-primary-600 align-middle me-1"></iconify-icon>
                Verifikasi Keamanan (Captcha) <span class="text-danger">*</span>
            </label>
            <span class="badge bg-primary-50 text-primary-600 border border-primary-200 font-monospace px-2 py-1 fs-6">
                <?= $loginCaptchaQuestion ?> = ?
            </span>
        </div>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:calculator-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="number"
                class="form-control h-52-px bg-white radius-8 ps-48 font-monospace"
                id="login-captcha"
                name="captcha_answer"
                placeholder="Tulis hasil penjumlahan angka di atas..."
                autocomplete="off"
                required>
        </div>
        <small class="text-secondary-light text-2xs d-block mt-1">Hitung angka penjumlahan di atas untuk memverifikasi Anda bukan bot spam.</small>
    </div>

    <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-100 radius-12 d-flex align-items-center justify-content-center gap-2 shadow-sm">
        <span>Masuk ke Dashboard</span>
        <iconify-icon icon="solar:login-2-bold" class="text-lg"></iconify-icon>
    </button>

    <div class="mt-28 p-16 bg-neutral-50 border border-neutral-200 radius-12 text-center text-sm">
        <p class="mb-0 text-secondary-light">
            Belum memiliki akun?
            <a href="<?= site_url('dashboard/daftar') ?>" class="text-primary-600 fw-bold ms-1">Daftar Akun Baru</a>
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
