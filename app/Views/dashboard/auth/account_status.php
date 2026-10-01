<?= $this->extend('dashboard/auth/layouts/auth') ?>
<?= $this->section('content') ?>
<h4 class="mb-12">Status Akun</h4>
<?php if ($message = session()->getFlashdata('message')): ?><div class="alert alert-success mb-20"><?= esc($message) ?></div><?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger mb-20"><?= esc($error) ?></div><?php endif; ?>
<?php if (! empty($user)): ?>
    <p class="text-secondary-light">Status akun Anda saat ini:</p>
    <h5 class="text-primary-600"><?= esc($user['account_status']) ?></h5>
<?php else: ?>
    <p class="text-secondary-light">Silakan masuk untuk melihat status akun.</p>
<?php endif; ?>
<a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary w-100 radius-12 mt-24 py-16">Kembali ke Masuk</a>
<?= $this->endSection() ?>
