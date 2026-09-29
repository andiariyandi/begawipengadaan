<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Penilaian</h6>
        <p class="text-secondary-light mb-0">ID Penilaian: <?= esc($gradingId ?? '-') ?></p>
    </div><a href="<?= site_url('dashboard/pembelajaran/penilaian') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Assignment 1: Analisis Perencanaan Pengadaan</h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Peserta</p>
                        <h6 class="mb-0">Rina Oktaviani</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kelas</p>
                        <h6 class="mb-0">PBJ Dasar Batch 1</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Nilai</p>
                        <h6 class="mb-0">88</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => 'approved']) ?>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light mb-4">Catatan Evaluator</p>
                        <p class="mb-0">Analisis sudah sesuai dengan prinsip perencanaan pengadaan. Perlu memperkuat referensi regulasi pada bagian akhir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Tindakan</h6><button type="button" class="btn btn-primary-600 w-100 radius-8 mb-12">Ubah Nilai</button><button type="button" class="btn btn-outline-primary-600 w-100 radius-8">Lihat Submission</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>