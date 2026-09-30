<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="text-center">
    <a href="<?= site_url('/') ?>" class="mb-40 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>

    <span class="w-80-px h-80-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center text-4xl">
        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
    </span>

    <h4 class="mt-24 mb-12"><?= esc($pageTitle ?? 'Pendaftaran Berhasil Dikirim') ?></h4>
    <p class="text-secondary-light text-lg mb-32"><?= esc($pageDescription ?? 'Akun Anda sedang menunggu verifikasi.') ?></p>
</div>

<div class="border border-neutral-200 radius-12 p-20 mb-24">
    <div class="d-flex gap-12 mb-20"><span class="w-32-px h-32-px flex-shrink-0 bg-primary-50 text-primary-600 rounded-circle d-inline-flex justify-content-center align-items-center fw-bold">1</span>
        <div>
            <h6 class="text-sm mb-4">Verifikasi Email</h6>
            <p class="text-secondary-light text-sm mb-0">Periksa email Anda untuk instruksi verifikasi akun.</p>
        </div>
    </div>
    <div class="d-flex gap-12 mb-20"><span class="w-32-px h-32-px flex-shrink-0 bg-warning-focus text-warning-main rounded-circle d-inline-flex justify-content-center align-items-center fw-bold">2</span>
        <div>
            <h6 class="text-sm mb-4">Pemeriksaan Operator/Admin</h6>
            <p class="text-secondary-light text-sm mb-0">Data pendaftaran akan diperiksa sesuai tipe profil yang dipilih.</p>
        </div>
    </div>
    <div class="d-flex gap-12"><span class="w-32-px h-32-px flex-shrink-0 bg-neutral-100 text-secondary-light rounded-circle d-inline-flex justify-content-center align-items-center fw-bold">3</span>
        <div>
            <h6 class="text-sm mb-4">Akun Aktif</h6>
            <p class="text-secondary-light text-sm mb-0">Jika disetujui, akun menjadi aktif dengan role awal public_user dan participant.</p>
        </div>
    </div>
</div>

<div class="alert alert-info d-flex align-items-start gap-10 mb-24"><iconify-icon icon="solar:info-circle-outline" class="text-xl mt-2"></iconify-icon><span class="text-sm">Jika data memerlukan perbaikan, status akun akan menjadi <strong>revision_required</strong>. Anda dapat melengkapi data tanpa membuat akun baru.</span></div>

<a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary text-sm px-12 py-16 w-100 radius-12">Kembali ke Masuk</a>
<a href="<?= site_url('/') ?>" class="d-inline-block w-100 text-center text-secondary-light text-sm mt-24">Kembali ke Beranda</a>
<?= $this->endSection() ?>