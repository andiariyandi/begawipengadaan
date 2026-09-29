<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Pengguna</h6>
        <p class="text-secondary-light mb-0">ID Pengguna: <?= esc($userId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/akses/pengguna/' . ($userId ?? 'USR-001') . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Pengguna</a>
</div>
<div class="row gy-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24 text-center"><span class="w-96-px h-96-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-3xl fw-bold">RO</span>
                <h5 class="mt-16 mb-4">Rina Oktaviani</h5>
                <p class="text-secondary-light mb-16">rina.oktaviani@lampungprov.go.id</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
                <hr class="my-24">
                <div class="text-start">
                    <p class="mb-8 text-secondary-light">Tipe Profil</p>
                    <h6 class="mb-16">ASN</h6>
                    <p class="mb-8 text-secondary-light">Role Aktif</p>
                    <h6 class="mb-0">Public User, Peserta</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Informasi Akun</h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Nama Lengkap</p>
                        <h6 class="text-md mb-0">Rina Oktaviani</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Email</p>
                        <h6 class="text-md mb-0">rina.oktaviani@lampungprov.go.id</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status Email</p>
                        <h6 class="text-md mb-0 text-success-main">Terverifikasi</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Bergabung Sejak</p>
                        <h6 class="text-md mb-0">12 Januari 2026</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Unit Organisasi</p>
                        <h6 class="text-md mb-0">Biro Pengadaan Barang dan Jasa</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Peran Pengadaan</p>
                        <h6 class="text-md mb-0">Pejabat Pengadaan</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>