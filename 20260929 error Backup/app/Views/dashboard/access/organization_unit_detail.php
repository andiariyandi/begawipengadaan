<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Unit Organisasi</h6>
        <p class="text-secondary-light mb-0">ID Unit: <?= esc($organizationId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Kode Unit</p>
                <h6 class="mb-0 text-primary-600">BPPBJ</h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Tipe Unit</p>
                <h6 class="mb-0">Biro</h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Nama Unit</p>
                <h6 class="mb-0">Biro Pengadaan Barang dan Jasa</h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Unit Induk</p>
                <h6 class="mb-0">Sekretariat Daerah</h6>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>