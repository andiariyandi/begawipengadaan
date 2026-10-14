<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-40 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-12"><?= esc($pageTitle ?? 'Lupa Kata Sandi') ?></h4>
    <p class="mb-32 text-secondary-light text-lg"><?= esc($pageDescription ?? 'Masukkan email akun Anda untuk menerima instruksi pengaturan ulang.') ?></p>
</div>

<?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger mb-20"><?= esc($error) ?></div><?php endif; ?>
<?php if ($message = session()->getFlashdata('message')): ?><div class="alert alert-success mb-20"><?= esc($message) ?></div><?php endif; ?>
<form action="<?= site_url('dashboard/lupa-kata-sandi') ?>" method="post">
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

    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
        Kirim Instruksi
    </button>

    <div class="text-center">
        <a href="<?= site_url('dashboard/masuk') ?>" class="text-primary-600 fw-bold mt-24 d-inline-block">Kembali ke Masuk</a>
    </div>

</form>
<?= $this->endSection() ?>