<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Peran Pengadaan</h6>
        <p class="text-secondary-light mb-0">ID Peran: <?= esc($procurementRoleId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/akses/peran-pengadaan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Kode</p>
                <h6 class="mb-0 text-primary-600">PPK</h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Nama Peran</p>
                <h6 class="mb-0">Pejabat Pembuat Komitmen</h6>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-4">Deskripsi</p>
                <p class="mb-0">Pejabat yang bertanggung jawab atas pelaksanaan pengadaan barang dan jasa sesuai ketentuan yang berlaku.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>