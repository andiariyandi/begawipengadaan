<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-dark"><?= esc($pageTitle ?? 'Buat Akun BegawiPengadaan') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 1 dari 4 — Daftarkan data akun dasar dan pilih tipe profil Anda.') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', [
    'currentStep' => $currentStep ?? 1,
    'currentKey'  => $currentKey ?? 'akun',
]) ?>

<?php $registrationData = $registrationData ?? []; ?>

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
            <strong class="text-sm">Kendala Validasi Pendaftaran:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/daftar') ?>" method="post" class="auth-register-form">
    <?= csrf_field() ?>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="full_name">Nama Lengkap</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:user-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="text"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="full_name"
                name="full_name"
                value="<?= esc(old('full_name', $registrationData['full_name'] ?? '')) ?>"
                placeholder="Nama lengkap sesuai identitas resmi"
                autocomplete="name"
                required>
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="username">Username Akun</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:user-id-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="text"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="username"
                name="username"
                value="<?= esc(old('username', $registrationData['username'] ?? '')) ?>"
                placeholder="Username akun unik (huruf/angka)"
                autocomplete="username"
                required>
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="email">Alamat Email</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:letter-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="email"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="email"
                name="email"
                value="<?= esc(old('email', $registrationData['email'] ?? '')) ?>"
                placeholder="nama@instansi.go.id"
                autocomplete="email"
                required>
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="phone">Nomor Telepon / WhatsApp</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:phone-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="tel"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="phone"
                name="phone"
                value="<?= esc(old('phone', $registrationData['phone'] ?? '')) ?>"
                placeholder="Contoh: 081234567890"
                autocomplete="tel"
                required>
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="register-password">Kata Sandi</label>
        <div class="position-relative">
            <div class="icon-field">
                <span class="icon top-50 translate-middle-y">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg text-secondary-light"></iconify-icon>
                </span>
                <input
                    type="password"
                    class="form-control h-56-px bg-neutral-50 radius-12 ps-48 pe-48"
                    id="register-password"
                    name="password"
                    placeholder="Minimal 12 karakter aman"
                    autocomplete="new-password"
                    required>
            </div>
            <span
                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                data-toggle="#register-password"
                title="Tampilkan / Sembunyikan Kata Sandi"></span>
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="register-password-confirmation">Konfirmasi Kata Sandi</label>
        <div class="position-relative">
            <div class="icon-field">
                <span class="icon top-50 translate-middle-y">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg text-secondary-light"></iconify-icon>
                </span>
                <input
                    type="password"
                    class="form-control h-56-px bg-neutral-50 radius-12 ps-48 pe-48"
                    id="register-password-confirmation"
                    name="password_confirmation"
                    placeholder="Ketik ulang kata sandi"
                    autocomplete="new-password"
                    required>
            </div>
            <span
                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                data-toggle="#register-password-confirmation"
                title="Tampilkan / Sembunyikan Kata Sandi"></span>
        </div>
    </div>

    <!-- Password Requirement Help Box -->
    <div class="p-12 bg-neutral-50 border border-neutral-200 radius-8 mb-20 text-xs text-secondary-light d-flex align-items-center gap-2">
        <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-base flex-shrink-0"></iconify-icon>
        <span>Password minimal 12 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.</span>
    </div>

    <div class="mb-24">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="profile-type">Tipe Profil Pengguna</label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <?php $selType = old('profile_type', $registrationData['profile_type'] ?? ''); ?>
            <select class="form-select h-56-px bg-neutral-50 radius-12 ps-48" name="profile_type" id="profile-type" required>
                <option value="">-- Pilih Tipe Profil Pendaftar --</option>
                <option value="asn" <?= $selType === 'asn' ? 'selected' : '' ?>>Aparatur Sipil Negara (ASN)</option>
                <option value="provider" <?= $selType === 'provider' ? 'selected' : '' ?>>Penyedia Barang / Jasa (Vendor)</option>
                <option value="academic" <?= $selType === 'academic' ? 'selected' : '' ?>>Akademisi / Peneliti / Mahasiswa</option>
                <option value="public" <?= $selType === 'public' ? 'selected' : '' ?>>Masyarakat Umum / Pemerhati Pengadaan</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-100 radius-12 d-flex align-items-center justify-content-center gap-2 shadow-sm">
        <span>Lanjut ke Langkah 2: Wilayah Domisili</span>
        <iconify-icon icon="solar:arrow-right-linear" class="text-lg"></iconify-icon>
    </button>

    <div class="mt-28 p-16 bg-neutral-50 border border-neutral-200 radius-12 text-center text-sm">
        <p class="mb-0 text-secondary-light">
            Sudah memiliki akun terdaftar?
            <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-bold ms-1">Masuk ke Akun</a>
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