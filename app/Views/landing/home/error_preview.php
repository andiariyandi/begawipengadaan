<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="py-5 my-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-4" style="width: 96px; height: 96px;">
                    <span class="fw-bold fs-1"><?= esc($statusCode ?? '404') ?></span>
                </div>
                <h1 class="fw-bold mb-3" style="color: #082f5c;"><?= esc($pageTitle ?? 'Halaman Tidak Ditemukan') ?></h1>
                <p class="text-secondary fs-5 mb-4">
                    <?= esc($message ?? 'Maaf, halaman yang Anda tuju tidak tersedia atau telah dipindahkan.') ?>
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= site_url('/') ?>" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                        <i class="fas fa-home me-2"></i> Kembali ke Beranda
                    </a>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold">
                        <i class="fas fa-headset me-2"></i> Hubungi Helpdesk
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
