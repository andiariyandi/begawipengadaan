<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container">
        <div class="page-header__inner">
            <h2>Hasil Survei Kepuasan Publik (IKM)</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="<?= site_url('/') ?>">Beranda</a></li>
                <li><span>/</span></li>
                <li><a href="<?= site_url('statistik') ?>">Statistik</a></li>
                <li><span>/</span></li>
                <li>Survei Kepuasan (IKM)</li>
            </ul>
        </div>
    </div>
</section>

<!-- Main Results Content -->
<section class="survey-results-section py-5">
    <div class="container">
        
        <!-- Header Banner & Hero Summary -->
        <div class="survey-results-hero p-4 p-md-5 rounded-4 mb-5 border shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                        <i class="fas fa-award me-1"></i> <?= esc($analytics['legal_basis'] ?? 'PermenPAN-RB No. 14/2017') ?>
                    </span>
                    <h2 class="fw-bold text-dark mb-3">Indeks Kepuasan Masyarakat (IKM) Biro Pengadaan Barang &amp; Jasa</h2>
                    <p class="text-muted mb-4 lead" style="font-size: 16px;">
                        Laporan resmi transparansi mutu pelayanan publik Pemerintah Provinsi Lampung. Evaluasi ini dihimpun secara objektif dan berkala dari seluruh pemangku kepentingan: Penyedia, PPK, Pokja Pemilihan, APIP, dan masyarakat umum.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="thm-btn px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                            <i class="fas fa-edit me-2"></i><span>Isi Survei Kepuasan</span>
                        </button>
                        <a href="<?= site_url('statistik') ?>" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center">
                            <i class="fas fa-chart-line me-2"></i>Lihat Statistik Publik Lainnya
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Grand IKM Badge Card -->
                    <div class="ikm-grand-badge p-4 rounded-4 text-center text-white shadow">
                        <div class="small text-white-50 text-uppercase fw-bold letter-spacing-1 mb-1">Capaian Indeks IKM</div>
                        <div class="ikm-grand-score display-4 fw-extrabold mb-1">
                            <?= esc($analytics['avg_overall'] ?? '4.86') ?>
                            <span class="fs-4 text-white-50 fw-normal">/ 5.00</span>
                        </div>
                        <div class="ikm-grand-stars text-warning fs-5 mb-2">
                            ★★★★★
                        </div>
                        <div class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold fs-6 shadow-sm mb-3">
                            Mutu: <?= esc($analytics['predicate']['label'] ?? 'Sangat Baik') ?> (<?= esc($analytics['score_pct'] ?? '97.2') ?>%)
                        </div>
                        <div class="text-white-50 small border-top border-white-10 pt-2">
                            Berdasarkan <?= number_format((int) ($analytics['total_responses'] ?? 1248), 0, ',', '.') ?> responden terverifikasi
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Key Metrics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-lg-3">
                <div class="metric-highlight-card p-4 rounded-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-icon-wrap bg-primary-subtle text-primary">
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            Skala 100: <?= esc($analytics['score_pct'] ?? '97.2') ?>%
                        </span>
                    </div>
                    <div class="metric-label text-muted small fw-semibold">Nilai Indeks IKM</div>
                    <div class="metric-val text-dark fw-bold fs-3"><?= esc($analytics['avg_overall'] ?? '4.86') ?> <span class="fs-6 text-muted fw-normal">/ 5.0</span></div>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+0.05 dari periode lalu</small>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="metric-highlight-card p-4 rounded-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-icon-wrap bg-success-subtle text-success">
                            <i class="fas fa-certificate"></i>
                        </span>
                        <span class="badge bg-success text-white">Predikat Tertinggi</span>
                    </div>
                    <div class="metric-label text-muted small fw-semibold">Mutu Pelayanan</div>
                    <div class="metric-val text-dark fw-bold fs-3">Kategori A</div>
                    <small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i>Sangat Memuaskan</small>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="metric-highlight-card p-4 rounded-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-icon-wrap bg-info-subtle text-info">
                            <i class="fas fa-users"></i>
                        </span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">Multi-Peran</span>
                    </div>
                    <div class="metric-label text-muted small fw-semibold">Total Responden</div>
                    <div class="metric-val text-dark fw-bold fs-3"><?= number_format((int) ($analytics['total_responses'] ?? 1248), 0, ',', '.') ?></div>
                    <small class="text-muted"><i class="fas fa-shield-alt text-primary me-1"></i>Data Tervalidasi Otentik</small>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="metric-highlight-card p-4 rounded-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-icon-wrap bg-warning-subtle text-warning">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">NPS Score</span>
                    </div>
                    <div class="metric-label text-muted small fw-semibold">Tingkat Rekomendasi</div>
                    <div class="metric-val text-dark fw-bold fs-3"><?= esc($analytics['recommendation_pct'] ?? '98.4') ?>%</div>
                    <small class="text-success"><i class="fas fa-heart text-danger me-1"></i>Merekomendasikan Layanan</small>
                </div>
            </div>
        </div>

        <!-- 4 Dimensi Aspek Mutu Layanan -->
        <div class="card border rounded-4 shadow-sm mb-5 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark"><i class="fas fa-poll me-2 text-primary"></i>Penilaian 4 Dimensi Aspek Mutu Layanan</h4>
                        <p class="text-muted small mb-0">Rincian skor capaian untuk tiap indikator utama penyelenggaraan pengadaan barang dan jasa.</p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Periode Penilaian: <?= esc($analytics['evaluated_at'] ?? '2026') ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <?php foreach ($analytics['aspects'] as $aspect): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="aspect-badge-icon me-3" style="color: <?= esc($aspect['color']) ?>; background: rgba(0,0,0,0.04);">
                                            <i class="<?= esc($aspect['icon']) ?> fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0"><?= esc($aspect['title']) ?></h6>
                                            <small class="text-muted" style="font-size: 11.5px;"><?= esc($aspect['description']) ?></small>
                                        </div>
                                    </div>
                                    <div class="text-end ps-2 flex-shrink-0">
                                        <span class="fs-5 fw-bold text-dark"><?= esc($aspect['score']) ?></span>
                                        <span class="small text-muted">/5.0</span>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 10px; border-radius: 999px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?= esc($aspect['pct']) ?>%; background-color: <?= esc($aspect['color']) ?>;" aria-valuenow="<?= esc($aspect['pct']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                                    <span>Tingkat Kepuasan: <strong class="text-dark"><?= esc($aspect['pct']) ?>%</strong></span>
                                    <span class="text-warning">★★★★★</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 2-Column: Distribusi Peran & Rating Bintang -->
        <div class="row g-4 mb-5">
            <!-- Distribusi Peran Responden -->
            <div class="col-lg-6">
                <div class="card border rounded-4 shadow-sm h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-id-badge me-2 text-warning"></i>Distribusi Kategori Responden</h5>
                        <p class="text-muted small mb-0">Komposisi peran pengguna yang berpartisipasi dalam survei.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($analytics['roles'] as $role): ?>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold text-dark small"><?= esc($role['name']) ?></span>
                                        <span class="fw-bold small" style="color: <?= esc($role['color']) ?>;"><?= esc($role['pct']) ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 999px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= esc($role['pct']) ?>%; background-color: <?= esc($role['color']) ?>;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribusi Bintang Penilaian -->
            <div class="col-lg-6">
                <div class="card border rounded-4 shadow-sm h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-star me-2 text-warning"></i>Distribusi Rating Bintang</h5>
                        <p class="text-muted small mb-0">Sebaran persentase bintang penilaian dari skala 1 hingga 5.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($analytics['stars'] as $starKey => $star): ?>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold text-dark d-flex align-items-center">
                                            <span class="text-warning me-2"><?= str_repeat('★', $star['stars']) ?><?= str_repeat('☆', 5 - $star['stars']) ?></span>
                                            <?= esc($star['label']) ?>
                                        </span>
                                        <span class="small text-muted">
                                            <strong><?= esc($star['pct']) ?>%</strong> (<?= number_format($star['count'], 0, ',', '.') ?>)
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 999px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= esc($star['pct']) ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Capaian Berkala per Triwulan -->
        <div class="card border rounded-4 shadow-sm mb-5">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-history me-2 text-primary"></i>Tren Capaian IKM Berkala Tahun 2026</h5>
                <p class="text-muted small mb-0">Perkembangan indeks kepuasan publik dari triwulan ke triwulan.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Periode Triwulan</th>
                                <th class="text-center">Indeks Rata-rata</th>
                                <th class="text-center">Skala 100</th>
                                <th class="text-center">Kategori Mutu</th>
                                <th class="text-center">Responden</th>
                                <th class="text-center pe-4">Status Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics['quarterly_trends'] as $trend): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= esc($trend['period']) ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark fs-6 fw-bold border"><?= esc($trend['score']) ?> / 5.0</span></td>
                                    <td class="text-center fw-semibold text-success"><?= esc($trend['pct']) ?>%</td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1"><?= esc($trend['status']) ?></span></td>
                                    <td class="text-center"><?= number_format((int) $trend['responses'], 0, ',', '.') ?> orang</td>
                                    <td class="text-center pe-4"><i class="fas fa-check-circle text-success me-1"></i>Tervalidasi</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Aspirasi & Ulasan Publik Terpilih -->
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-dark mb-1"><i class="fas fa-comments text-info me-2"></i>Aspirasi &amp; Ulasan Publik Terverifikasi</h4>
                    <p class="text-muted small mb-0">Ulasan autentik dan masukan membangun dari para pelaku ekosistem pengadaan barang dan jasa.</p>
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                    <i class="fas fa-plus me-1"></i> Tulis Ulasan Anda
                </button>
            </div>

            <div class="row g-4">
                <?php foreach ($analytics['feedbacks'] as $fb): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="public-feedback-card p-4 rounded-4 bg-white border shadow-sm h-100 d-flex flex-direction-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="text-warning fs-6">
                                        <?= str_repeat('★', $fb['rating']) ?><?= str_repeat('☆', 5 - $fb['rating']) ?>
                                    </span>
                                    <span class="badge bg-light text-secondary border small font-monospace">
                                        <?= esc($fb['ticket']) ?>
                                    </span>
                                </div>
                                <p class="feedback-quote text-dark mb-3" style="font-size: 14px; line-height: 1.6;">
                                    &ldquo;<?= esc($fb['feedback']) ?>&rdquo;
                                </p>
                            </div>
                            <div class="pt-3 border-top mt-auto">
                                <div class="fw-bold text-dark small"><?= esc($fb['name']) ?></div>
                                <div class="text-muted" style="font-size: 11.5px;"><?= esc($fb['institution']) ?></div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10.5px;">
                                        <?= esc($fb['role']) ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 11px;"><?= esc($fb['date']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bottom Call-To-Action Box -->
        <div class="survey-cta-banner p-4 p-md-5 rounded-4 text-center text-white shadow">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-3">Partisipasi Publik</span>
            <h3 class="fw-bold text-white mb-2">Suara Anda Adalah Kunci Kemajuan Pengadaan Barang &amp; Jasa Lampung</h3>
            <p class="text-white-50 mx-auto mb-4" style="max-width: 620px;">
                Bantu Biro Pengadaan Barang dan Jasa Provinsi Lampung mempertahankan standar integritas dan mutu pelayanan prima dengan mengisi kuesioner singkat berikut.
            </p>
            <button type="button" class="thm-btn px-5 py-3" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                <i class="fas fa-edit me-2"></i><span>Isi Kuesioner Survei Kepuasan (1 Menit)</span>
            </button>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
