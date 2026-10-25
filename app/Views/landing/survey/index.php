<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Page-level styles for standalone survey view */
.survey-intro-card {
    border-radius: 20px;
    border: 1px solid #e2e8f0;
}
.survey-kpi-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
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
<section class="survey-page-section py-5" style="background: #f8fafc; min-height: 70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                <!-- Intro Card -->
                <div class="survey-intro-card text-center mb-4 p-4 p-md-5 rounded-4 shadow-sm bg-white border">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                        <i class="fas fa-award me-1"></i> Standar KemenPAN-RB No. 14 / 2017 &bull; Indeks Kepuasan Masyarakat
                    </span>
                    <h3 class="fw-bold text-dark mb-2">Evaluasi Mutu &amp; Kualitas Layanan Ekosistem BegawiPengadaan</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 680px; font-size: 15px; line-height: 1.6;">
                        Partisipasi aktif Anda menjadi tolok ukur evaluasi berkala bagi Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung untuk terus meningkatkan transparansi, kemudahan, dan efektivitas ekosistem PBJ.
                    </p>
                    
                    <?php
                    $surveyTotal = (int) ($stats['total_responses'] ?? 0);
                    $surveyAvg   = (float) ($stats['avg_rating'] ?? 0);
                    $surveyPct   = (float) ($stats['satisfaction_pct'] ?? 0);
                    ?>
                    <div class="row g-3 justify-content-center pt-2">
                        <div class="col-sm-4">
                            <div class="p-3 survey-kpi-badge text-center">
                                <div class="fs-4 fw-bold text-primary mb-1"><?= $surveyTotal > 0 ? number_format($surveyAvg, 2) : '0.00' ?> / 5.0</div>
                                <div class="small text-muted">Indeks Kepuasan Rata-rata</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 survey-kpi-badge text-center">
                                <div class="fs-4 fw-bold text-success mb-1"><?= $surveyTotal > 0 ? number_format($surveyPct, 1) : '0' ?>%</div>
                                <div class="small text-muted">Tingkat Kepuasan Pengguna</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 survey-kpi-badge text-center">
                                <div class="fs-4 fw-bold text-warning mb-1"><i class="far fa-clock me-1"></i>&plusmn;1 Menit</div>
                                <div class="small text-muted">Estimasi Waktu Pengisian</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 d-flex flex-wrap justify-content-center gap-2">
                        <button type="button" class="thm-btn px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                            <i class="fas fa-edit me-2"></i><span>Buka Formulir Survei Kepuasan</span>
                        </button>
                        <a href="<?= site_url('statistik/survei-kepuasan') ?>" class="btn btn-outline-primary fw-semibold px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                            <i class="fas fa-chart-line"></i><span>Lihat Laporan &amp; Indeks IKM Resmi</span>
                        </a>
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
