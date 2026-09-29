<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Profil Saya') ?></h6>
        <p class="text-secondary-light mb-0">Kelola informasi akun dan preferensi pribadi Anda.</p>
    </div>
    <a href="<?= site_url('dashboard/profile/edit') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">
        <iconify-icon icon="solar:pen-new-square-outline" class="me-6"></iconify-icon> Ubah Profil
    </a>
</div>

<div class="row gy-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24 text-center">
                <span class="w-96-px h-96-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-3xl fw-bold">AD</span>
                <h5 class="mt-16 mb-4">Administrator</h5>
                <p class="text-secondary-light mb-16">Administrator Sistem</p>
                <?= view('dashboard/components/status_badge', ['status' => 'active', 'label' => 'Akun Aktif']) ?>
                <hr class="my-24">
                <div class="text-start d-flex flex-column gap-16">
                    <div><p class="text-secondary-light text-sm mb-4">Email</p><h6 class="text-md mb-0 text-break">administrator@begawipbj.go.id</h6></div>
                    <div><p class="text-secondary-light text-sm mb-4">Nomor Telepon</p><h6 class="text-md mb-0">(0721) 123456</h6></div>
                    <div><p class="text-secondary-light text-sm mb-4">Bergabung Sejak</p><h6 class="text-md mb-0">01 Januari 2026</h6></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-12 mb-20">
                    <h6 class="text-lg mb-0">Informasi Profil</h6>
                    <a href="<?= site_url('dashboard/profile/edit') ?>" class="text-primary-600">Ubah</a>
                </div>
                <div class="row gy-20">
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Nama Lengkap</p><h6 class="mb-0">Administrator</h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Nama Pengguna</p><h6 class="mb-0">administrator</h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Role</p><h6 class="mb-0">Administrator Sistem</h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Unit/OPD</p><h6 class="mb-0">Biro Pengadaan Barang dan Jasa</h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Jabatan</p><h6 class="mb-0">Administrator Platform</h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Login Terakhir</p><h6 class="mb-0">29 Juni 2026, 10:15 WIB</h6></div>
                </div>
            </div>
        </div>
        <div class="card mt-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Keamanan Akun</h6>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 pb-20 border-bottom border-neutral-200">
                    <div><h6 class="text-md mb-4">Kata Sandi</h6><p class="text-secondary-light text-sm mb-0">Terakhir diperbarui 30 hari yang lalu.</p></div>
                    <a href="<?= site_url('dashboard/profile/password') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11">Ubah Kata Sandi</a>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 pt-20">
                    <div><h6 class="text-md mb-4">Preferensi Akun</h6><p class="text-secondary-light text-sm mb-0">Atur bahasa, tampilan, dan preferensi notifikasi pribadi.</p></div>
                    <a href="<?= site_url('dashboard/profile/preferences') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11">Atur Preferensi</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>