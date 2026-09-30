<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Artikel</h6>
        <p class="text-secondary-light mb-0">ID Artikel: <?= esc($articleId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/bunga-rampai/artikel/' . ($articleId ?? 'ART-001') . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Artikel</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-8">
                <p class="text-secondary-light mb-4">Judul</p>
                <h5 class="mb-0">Prinsip Pengadaan Barang/Jasa Pemerintah</h5>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'active']) ?>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Kategori</p>
                <h6 class="mb-0">Dasar PBJ</h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Penulis</p>
                <h6 class="mb-0">Admin Bunga Rampai</h6>
            </div>
            <div class="col-md-4">
                <p class="text-secondary-light mb-4">Diperbarui</p>
                <h6 class="mb-0">28 September 2026</h6>
            </div>
            <div class="col-12">
                <hr class="my-12">
                <p class="text-secondary-light mb-8">Ringkasan</p>
                <p class="mb-0">Artikel pengantar mengenai prinsip dasar dan nilai utama dalam pengadaan barang/jasa pemerintah.</p>
            </div>
            <div class="col-12">
                <p class="text-secondary-light mb-8">Isi Artikel</p>
                <p class="mb-0">Konten artikel ditampilkan sebagai pratinjau UI. Editor dan penyimpanan konten akan diintegrasikan pada tahap backend.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>