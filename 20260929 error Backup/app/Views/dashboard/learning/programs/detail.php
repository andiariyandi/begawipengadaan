<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Program</h6>
        <p class="text-secondary-light mb-0">ID Program: <?= esc($programId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/pembelajaran/program/' . ($programId ?? 'PRG-001') . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Program</a>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Program Peningkatan Kompetensi PBJ 2026</h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kode Program</p>
                        <h6 class="mb-0 text-primary-600">PKPBJ-2026</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Periode</p>
                        <h6 class="mb-0">Januari - Desember 2026</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Jumlah Course</p>
                        <h6 class="mb-0">6 Course</h6>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light mb-4">Deskripsi</p>
                        <p class="mb-0">Program pengembangan kompetensi pengelola Pengadaan Barang/Jasa Pemerintah di lingkungan Provinsi Lampung.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Ringkasan</h6>
                <div class="d-flex justify-content-between mb-16"><span class="text-secondary-light">Kelas Aktif</span><strong>8</strong></div>
                <div class="d-flex justify-content-between mb-16"><span class="text-secondary-light">Peserta</span><strong>324</strong></div>
                <div class="d-flex justify-content-between"><span class="text-secondary-light">Kelulusan</span><strong>82%</strong></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>