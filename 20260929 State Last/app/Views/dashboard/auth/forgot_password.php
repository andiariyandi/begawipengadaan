<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-40 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPBJ</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-12"><?= esc($pageTitle ?? 'Lupa Kata Sandi') ?></h4>
    <p class="mb-32 text-secondary-light text-lg"><?= esc($pageDescription ?? 'Masukkan email akun Anda untuk menerima instruksi pengaturan ulang.') ?></p>
</div>

<form action="#" method="post">
    <div class="icon-field">
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

    <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
        Kirim Instruksi
    </button>

    <div class="text-center">
        <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-bold mt-24 d-inline-block">Kembali ke Masuk</a>
    </div>

    <p class="text-center text-secondary-light text-xs mt-32 mb-0">Form lupa kata sandi masih UI-only dan belum mengirim email reset.</p>
</form>
<?= $this->endSection() ?>