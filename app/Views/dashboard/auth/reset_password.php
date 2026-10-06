<?= $this->extend('dashboard/auth/layouts/auth') ?>
<?= $this->section('content') ?>
<h4 class="mb-12">Reset Kata Sandi</h4>
<p class="mb-24 text-secondary-light">Buat kata sandi baru dengan minimal 12 karakter.</p>
<?php if ($errors = session()->getFlashdata('errors')): ?><div class="alert alert-danger mb-20"><?= esc(implode(' ', $errors)) ?></div><?php endif; ?>
<form action="<?= site_url('dashboard/reset-kata-sandi/' . $token) ?>" method="post">
    <input type="password" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="password" placeholder="Kata sandi baru" autocomplete="new-password">
    <input type="password" class="form-control h-56-px bg-neutral-50 radius-12" name="password_confirmation" placeholder="Konfirmasi kata sandi" autocomplete="new-password">
    <button type="submit" class="btn btn-primary w-100 radius-12 mt-32 py-16">Simpan Kata Sandi</button>
</form>
<?= $this->endSection() ?>
