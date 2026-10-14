<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-dark"><?= esc($pageTitle ?? 'Atur Ulang Kata Sandi') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Buat kata sandi baru yang kuat untuk menjaga keamanan akun Anda.') ?></p>
</div>

<!-- Flash Alerts -->
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
            <strong class="text-sm">Kendala Validasi Kata Sandi:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/reset-kata-sandi/' . $token) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-20">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="reset-password">Kata Sandi Baru</label>
        <div class="position-relative">
            <div class="icon-field">
                <span class="icon top-50 translate-middle-y">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg text-secondary-light"></iconify-icon>
                </span>
                <input
                    type="password"
                    class="form-control h-56-px bg-neutral-50 radius-12 ps-48 pe-48"
                    id="reset-password"
                    name="password"
                    placeholder="Minimal 12 karakter aman"
                    autocomplete="new-password"
                    required>
            </div>
            <span
                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                data-toggle="#reset-password"
                title="Tampilkan / Sembunyikan Kata Sandi"></span>
        </div>
    </div>

    <div class="mb-20">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="reset-password-confirmation">Konfirmasi Kata Sandi Baru</label>
        <div class="position-relative">
            <div class="icon-field">
                <span class="icon top-50 translate-middle-y">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg text-secondary-light"></iconify-icon>
                </span>
                <input
                    type="password"
                    class="form-control h-56-px bg-neutral-50 radius-12 ps-48 pe-48"
                    id="reset-password-confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi baru"
                    autocomplete="new-password"
                    required>
            </div>
            <span
                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                data-toggle="#reset-password-confirmation"
                title="Tampilkan / Sembunyikan Kata Sandi"></span>
        </div>
    </div>

    <!-- Password Requirements Checklist -->
    <div class="p-16 bg-neutral-50 border border-neutral-200 radius-12 mb-24">
        <p class="fw-semibold text-xs text-primary-light mb-8 d-flex align-items-center gap-1">
            <iconify-icon icon="solar:shield-check-bold" class="text-primary-600 text-sm"></iconify-icon>
            <span>Kriteria Keamanan Kata Sandi:</span>
        </p>
        <ul class="text-xs text-secondary-light ps-16 mb-0 d-flex flex-column gap-1">
            <li>Minimal 12 karakter</li>
            <li>Memuat minimal 1 huruf besar (A-Z) dan 1 huruf kecil (a-z)</li>
            <li>Memuat minimal 1 angka (0-9)</li>
            <li>Memuat minimal 1 simbol atau karakter khusus (!@#$%^&*...)</li>
        </ul>
    </div>

    <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-100 radius-12 d-flex align-items-center justify-content-center gap-2 shadow-sm">
        <span>Simpan Kata Sandi Baru</span>
        <iconify-icon icon="solar:check-read-bold" class="text-lg"></iconify-icon>
    </button>

    <div class="mt-28 p-16 bg-neutral-50 border border-neutral-200 radius-12 text-center text-sm">
        <p class="mb-0 text-secondary-light">
            Kembali ke halaman login?
            <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-bold ms-1">Masuk Sekarang</a>
        </p>
    </div>
</form>
<?= $this->endSection() ?>
