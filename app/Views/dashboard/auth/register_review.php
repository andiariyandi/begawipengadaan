<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPengadaan</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Tinjau Data Pendaftaran') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 4, 'currentKey' => $currentKey ?? 'tinjau']) ?>
<?php
$registrationData = $registrationData ?? [];
$registrationAddress = $registrationAddress ?? [];
$registrationProfile = $registrationProfile ?? [];
$profileLabels = ['asn' => 'ASN', 'provider' => 'Penyedia Barang/Jasa', 'academic' => 'Akademisi', 'public' => 'Masyarakat Umum'];
?>

<div class="border border-neutral-200 radius-12 p-20 mb-16">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Akun Dasar</h6><a href="<?= site_url('dashboard/daftar') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Nama lengkap</span><strong><?= esc($registrationData['full_name'] ?? '-') ?></strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Email</span><strong><?= esc($registrationData['email'] ?? '-') ?></strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Tipe profil</span><strong><?= esc($profileLabels[$registrationData['profile_type'] ?? ''] ?? '-') ?></strong></div>
    </div>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-16">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Wilayah dan Alamat</h6><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Provinsi</span><strong><?= esc($registrationAddress['province'] ?? '-') ?></strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Kabupaten/Kota</span><strong><?= esc($registrationAddress['regency'] ?? '-') ?></strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Alamat</span><strong class="text-end"><?= esc($registrationAddress['address'] ?? '-') ?></strong></div>
    </div>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-24">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Profil Spesifik</h6><a href="<?= site_url('dashboard/daftar/profil/asn') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Profil</span><strong><?= esc($profileLabels[$registrationData['profile_type'] ?? ''] ?? '-') ?></strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Data profil</span><strong class="text-end"><?= esc(implode(', ', array_map(static fn($value) => is_array($value) ? implode(', ', $value) : (string) $value, array_slice($registrationProfile, 0, 3)))) ?></strong></div>
    </div>
</div>

<?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger mb-20"><?= esc($error) ?></div><?php endif; ?>
<form action="<?= site_url('dashboard/daftar/tinjau') ?>" method="post">
    <div class="form-check style-check d-flex align-items-start mb-16"><input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="1" id="terms" name="terms"><label class="form-check-label text-sm" for="terms">Saya menyetujui <a href="#" class="text-primary-600 fw-semibold">Syarat Penggunaan</a>.</label></div>
    <div class="form-check style-check d-flex align-items-start mb-16"><input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="1" id="privacy" name="privacy"><label class="form-check-label text-sm" for="privacy">Saya menyetujui <a href="#" class="text-primary-600 fw-semibold">Kebijakan Privasi</a>.</label></div>

    <div class="border border-dashed border-neutral-300 radius-12 p-16 mb-20 text-center bg-neutral-50"><iconify-icon icon="solar:shield-check-outline" class="text-primary-600 text-2xl"></iconify-icon>
        <p class="fw-semibold text-sm mb-4 mt-8">CAPTCHA / Anti-spam</p>
        <p class="text-secondary-light text-xs mb-0">Placeholder UI. CAPTCHA dan rate limiting harus diterapkan pada endpoint submit produksi.</p>
    </div>

    <div class="d-flex gap-12"><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a><button type="submit" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Kirim Pendaftaran</button></div>
</form>
<?= $this->endSection() ?>