<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPBJ</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8"><?= esc($pageTitle ?? 'Buat Akun BegawiPBJ') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', [
    'currentStep' => $currentStep ?? 1,
    'currentKey' => $currentKey ?? 'akun',
]) ?>

<form action="#" method="post">
    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="f7:person"></iconify-icon></span>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="full_name" placeholder="Nama lengkap" autocomplete="name">
    </div>

    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:user-id-outline"></iconify-icon></span>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="username" placeholder="Username" autocomplete="username">
    </div>

    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="mage:email"></iconify-icon></span>
        <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" name="email" placeholder="Alamat email" autocomplete="email">
    </div>

    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:phone-outline"></iconify-icon></span>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="phone" placeholder="Nomor telepon Indonesia" autocomplete="tel">
    </div>

    <div class="position-relative mb-16">
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:lock-password-outline"></iconify-icon></span>
            <input type="password" class="form-control h-56-px bg-neutral-50 radius-12" id="register-password" name="password" placeholder="Kata sandi" autocomplete="new-password">
        </div>
        <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#register-password"></span>
    </div>

    <div class="position-relative mb-8">
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:lock-password-outline"></iconify-icon></span>
            <input type="password" class="form-control h-56-px bg-neutral-50 radius-12" id="register-password-confirmation" name="password_confirmation" placeholder="Konfirmasi kata sandi" autocomplete="new-password">
        </div>
        <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#register-password-confirmation"></span>
    </div>

    <p class="text-xs text-secondary-light mb-20">Password minimal 12 karakter dan wajib memuat huruf besar, huruf kecil, angka, serta simbol.</p>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe profil</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12" name="profile_type" id="profile-type">
        <option value="">Pilih tipe profil</option>
        <option value="asn">ASN</option>
        <option value="provider">Penyedia Barang/Jasa</option>
        <option value="academic">Akademisi</option>
        <option value="public">Masyarakat Umum</option>
    </select>

    <a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-primary text-sm px-12 py-16 w-100 radius-12 mt-32 text-center">
        Lanjut ke Wilayah
        <iconify-icon icon="solar:arrow-right-linear" class="ms-6"></iconify-icon>
    </a>

    <div class="mt-24 text-center text-sm">
        <p class="mb-0">Sudah memiliki akun? <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-semibold">Masuk</a></p>
    </div>

    <div class="mt-16 text-center text-sm"><a href="<?= site_url('/') ?>" class="text-secondary-light">Kembali ke Beranda</a></div>
    <p class="text-center text-secondary-light text-xs mt-20 mb-0">Data pada tahap ini belum disimpan. Form registrasi masih UI-only.</p>
</form>
<?= $this->endSection() ?>