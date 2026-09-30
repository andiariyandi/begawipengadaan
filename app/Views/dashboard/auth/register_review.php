<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Tinjau Data Pendaftaran') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 4, 'currentKey' => $currentKey ?? 'tinjau']) ?>

<div class="border border-neutral-200 radius-12 p-20 mb-16">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Akun Dasar</h6><a href="<?= site_url('dashboard/daftar') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Nama lengkap</span><strong>Nama Pengguna</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Email</span><strong>email@example.com</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Tipe profil</span><strong>ASN</strong></div>
    </div>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-16">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Wilayah dan Alamat</h6><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Provinsi</span><strong>Lampung</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Kabupaten/Kota</span><strong>Kota Bandar Lampung</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Alamat</span><strong class="text-end">Belum tersimpan (UI-only)</strong></div>
    </div>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-24">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-16">
        <h6 class="mb-0">Profil Spesifik</h6><a href="<?= site_url('dashboard/daftar/profil/asn') ?>" class="text-primary-600 text-sm fw-semibold">Ubah</a>
    </div>
    <div class="d-flex flex-column gap-10 text-sm">
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Profil</span><strong>ASN</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">NIP/NIPPK</span><strong>••••••••••••••••</strong></div>
        <div class="d-flex justify-content-between gap-12"><span class="text-secondary-light">Instansi</span><strong>Pemerintah Provinsi Lampung</strong></div>
    </div>
</div>

<form action="#" method="post">
    <div class="form-check style-check d-flex align-items-start mb-16"><input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="1" id="terms" name="terms"><label class="form-check-label text-sm" for="terms">Saya menyetujui <a href="#" class="text-primary-600 fw-semibold">Syarat Penggunaan</a>.</label></div>
    <div class="form-check style-check d-flex align-items-start mb-16"><input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="1" id="privacy" name="privacy"><label class="form-check-label text-sm" for="privacy">Saya menyetujui <a href="#" class="text-primary-600 fw-semibold">Kebijakan Privasi</a>.</label></div>

    <div class="border border-dashed border-neutral-300 radius-12 p-16 mb-20 text-center bg-neutral-50"><iconify-icon icon="solar:shield-check-outline" class="text-primary-600 text-2xl"></iconify-icon>
        <p class="fw-semibold text-sm mb-4 mt-8">CAPTCHA / Anti-spam</p>
        <p class="text-secondary-light text-xs mb-0">Placeholder UI. CAPTCHA dan rate limiting harus diterapkan pada endpoint submit produksi.</p>
    </div>

    <div class="d-flex gap-12"><a href="<?= site_url('dashboard/daftar/profil/asn') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a><a href="<?= site_url('dashboard/daftar/berhasil') ?>" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Kirim Pendaftaran</a></div>
</form>
<?= $this->endSection() ?>