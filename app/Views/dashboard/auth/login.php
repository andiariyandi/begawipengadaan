<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-40 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPBJ</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-12"><?= esc($pageTitle ?? 'Masuk ke Akun Anda') ?></h4>
    <p class="mb-32 text-secondary-light text-lg"><?= esc($pageDescription ?? 'Silakan masukkan email dan kata sandi Anda.') ?></p>
</div>

<?php if ($message = session()->getFlashdata('message')): ?>
    <div class="alert alert-success mb-20"><?= esc($message) ?></div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-20"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-20"><?= esc(implode(' ', $errors)) ?></div>
<?php endif; ?>

<form action="<?= site_url('dashboard/masuk') ?>" method="post">
    <?= csrf_field() ?>
    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y">
            <iconify-icon icon="mage:email"></iconify-icon>
        </span>
        <input
            type="email"
            class="form-control h-56-px bg-neutral-50 radius-12"
            name="email"
            placeholder="Alamat email"
            autocomplete="email">
    </div>

    <div class="position-relative mb-20">
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
            </span>
            <input
                type="password"
                class="form-control h-56-px bg-neutral-50 radius-12"
                id="login-password"
                name="password"
                placeholder="Kata sandi"
                autocomplete="current-password">
        </div>
        <span
            class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
            data-toggle="#login-password"></span>
    </div>

    <div class="d-flex justify-content-between gap-2">
        <div class="form-check style-check d-flex align-items-center">
            <input class="form-check-input border border-neutral-300" type="checkbox" value="1" id="remember" name="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>
        <a href="<?= site_url('dashboard/lupa-kata-sandi') ?>" class="text-primary-600 fw-medium">Lupa kata sandi?</a>
    </div>

    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
        Masuk
    </button>

    <div class="mt-32 text-center text-sm">
        <p class="mb-0">
            Belum memiliki akun?
            <a href="<?= site_url('dashboard/daftar') ?>" class="text-primary-600 fw-semibold">Daftar</a>
        </p>
    </div>

    <div class="mt-24 text-center text-sm">
        <a href="<?= site_url('/') ?>" class="text-secondary-light">Kembali ke Beranda</a>
    </div>

</form>
<?= $this->endSection() ?>