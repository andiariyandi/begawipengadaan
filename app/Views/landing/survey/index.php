<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Page-level styles for standalone survey view */
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container">
        <div class="page-header__inner">
            <h2>Survei Kepuasan Layanan Pengguna (IKM)</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="<?= site_url('/') ?>">Beranda</a></li>
                <li><span>/</span></li>
                <li>Survei Kepuasan</li>
            </ul>
        </div>
    </div>
</section>

<!-- Standalone Survey Section -->
<section class="survey-page-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                <!-- Intro Card -->
                <div class="survey-intro-card text-center mb-4 p-4 rounded-4 shadow-sm bg-white border">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                        <i class="fas fa-award me-1"></i> Standar KemenPAN-RB No. 14 / 2017 &bull; Indeks Kepuasan Masyarakat
                    </span>
                    <h3 class="fw-bold text-dark mb-2">Evaluasi Mutu &amp; Kualitas Layanan Ekosistem BegawiPengadaan</h3>
                    <p class="text-muted mx-auto mb-3" style="max-width: 680px;">
                        Partisipasi aktif Anda menjadi tolak ukur evaluasi berkala bagi Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung untuk terus meningkatkan transparansi, kemudahan, dan efektivitas ekosistem PBJ.
                    </p>
                    
                    <div class="row g-3 justify-content-center pt-2">
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="fs-4 fw-bold text-primary mb-1"><?= esc($stats['avg_rating'] ?? '4.85') ?> / 5.0</div>
                                <div class="small text-muted">Indeks Kepuasan Rata-rata</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="fs-4 fw-bold text-success mb-1"><?= esc($stats['satisfaction_pct'] ?? '97.2') ?>%</div>
                                <div class="small text-muted">Tingkat Kepuasan Pengguna</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="fs-4 fw-bold text-warning mb-1">⏱ ±1 Menit</div>
                                <div class="small text-muted">Waktu Pengisian Survei</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="thm-btn px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                            <i class="fas fa-edit me-2"></i><span>Buka Formulir Survei Kepuasan</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Automatically open survey modal on page load for convenience
    const modalEl = document.getElementById('modalSurveyKepuasan');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        setTimeout(() => modal.show(), 400);
    }
});
</script>
<?= $this->endSection() ?>
