<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="text-center mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <div>
        <span class="w-80-px h-80-px bg-success-50 text-success-main border border-2 border-success-200 rounded-circle d-inline-flex align-items-center justify-content-center text-4xl shadow-sm mb-16">
            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
        </span>
    </div>

    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Pendaftaran Berhasil Dikirim') ?></h4>
    <p class="text-secondary-light text-sm mb-0"><?= esc($pageDescription ?? 'Permohonan akun Anda telah tersimpan dan masuk antrean verifikasi.') ?></p>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-24 bg-neutral-50">
    <h6 class="text-xs fw-bold text-uppercase text-secondary-light tracking-wider mb-16">Tahapan Verifikasi Akun:</h6>

    <div class="d-flex gap-16 mb-20 align-items-start">
        <span class="w-32-px h-32-px flex-shrink-0 bg-success-600 text-white rounded-circle d-inline-flex justify-content-center align-items-center fw-bold text-xs shadow-sm">
            <iconify-icon icon="solar:check-read-linear"></iconify-icon>
        </span>
        <div>
            <h6 class="text-sm fw-bold mb-2 text-primary-light">1. Data Pendaftaran Diterima</h6>
            <p class="text-secondary-light text-xs mb-0">Informasi identitas, domisili, dan profil spesifik Anda telah tercatat aman di basis data.</p>
        </div>
    </div>

    <div class="d-flex gap-16 mb-20 align-items-start">
        <span class="w-32-px h-32-px flex-shrink-0 bg-warning-600 text-white rounded-circle d-inline-flex justify-content-center align-items-center fw-bold text-xs shadow-sm">
            2
        </span>
        <div>
            <h6 class="text-sm fw-bold mb-2 text-primary-light">2. Validasi Operator & Admin</h6>
            <p class="text-secondary-light text-xs mb-0">Tim verifikator Biro Pengadaan Barang & Jasa akan memverifikasi kesesuaian data identitas Anda.</p>
        </div>
    </div>

    <div class="d-flex gap-16 align-items-start">
        <span class="w-32-px h-32-px flex-shrink-0 bg-neutral-200 text-secondary-light rounded-circle d-inline-flex justify-content-center align-items-center fw-bold text-xs">
            3
        </span>
        <div>
            <h6 class="text-sm fw-bold mb-2 text-primary-light">3. Akun Aktif & Siap Digunakan</h6>
            <p class="text-secondary-light text-xs mb-0">Setelah disetujui, akun langsung berstatus aktif dan dapat mengakses layanan pembelajaran serta agenda.</p>
        </div>
    </div>
</div>

<div class="alert alert-info d-flex align-items-start gap-12 mb-24 radius-8 py-12 px-16">
    <iconify-icon icon="solar:info-circle-bold" class="text-xl text-info-main flex-shrink-0 mt-1"></iconify-icon>
    <div class="text-xs text-secondary-light">
        Jika terdapat berkas yang memerlukan revisi, status akun akan ditandai sebagai <strong>Perlu Perbaikan (revision_required)</strong> dan dapat diperbarui tanpa mendaftar ulang.
    </div>
</div>

<div class="d-flex flex-column gap-10">
    <a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-100 radius-12 text-center d-flex align-items-center justify-content-center gap-2 shadow-sm">
        <span>Ke Halaman Masuk</span>
        <iconify-icon icon="solar:login-2-bold" class="text-lg"></iconify-icon>
    </a>
    <a href="<?= site_url('/') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-14 w-100 radius-12 text-center text-secondary-light">
        Kembali ke Beranda Utama
    </a>
</div>
<?= $this->endSection() ?>