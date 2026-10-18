<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<style>
    .stat-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .stat-kpi-card__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 12px;
    }
    .stat-kpi-card__num {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .stat-kpi-card__label {
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
    }
    .stat-progress-bar {
        height: 7px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .stat-progress-val {
        height: 100%;
        border-radius: 999px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$stats        = $stats ?? [];
$ikmAnalytics = $ikmAnalytics ?? [];
$visitorStats = $visitorStats ?? [];
$activeTab    = $activeTab ?? 'all';
?>

<!-- MAIN STATISTICS PORTAL SECTION — Standard BegawiPengadaan Benchmark -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 80vh;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title Banner -->
        <div class="evt-hero-banner mb-3 mb-md-4">
            <div class="arena-breadcrumb evt-breadcrumb mb-2">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <span class="text-muted">Lainnya</span>
                <span class="sep">/</span>
                <span class="current">Statistik Publik PBJ</span>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-8">
                    <span class="arena-section-head__tag mb-1 d-inline-block">
                        <i class="fas fa-chart-pie me-1 text-primary"></i> Data Terbuka &amp; Akuntabilitas Publik
                    </span>
                    <h1 class="evt-hero-title mb-2">
                        Pusat Statistik &amp; <span>Data Terbuka PBJ</span> <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        Transparansi capaian pembelajaran, kepatuhan regulasi hukum, partisipasi agenda pembinaan, survei kepuasan masyarakat (IKM), dan sertifikasi kompetensi pengadaan barang dan jasa.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= site_url('statistik/survei-kepuasan') ?>" class="btn btn-warning text-dark fw-bold radius-12 px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 mb-2" style="font-size: 13.5px;">
                        <i class="fas fa-award"></i> Lihat Laporan IKM Resmi
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-Navigation Category Tabs -->
        <div class="card border-0 shadow-2xs radius-12 p-2 px-3 mb-4 bg-white border" style="border-color: #e2e8f0 !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <span class="small fw-bold text-muted me-1" style="font-size: 12px;"><i class="fas fa-filter text-primary me-1"></i> Klaster:</span>
                    <a href="<?= site_url('statistik') ?>" class="arena-filter-pill <?= $activeTab === 'all' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                        Semua Data
                    </a>
                    <a href="<?= site_url('statistik/pembelajaran') ?>" class="arena-filter-pill <?= $activeTab === 'learning' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                        <i class="fas fa-graduation-cap me-1"></i> Pembelajaran
                    </a>
                    <a href="<?= site_url('statistik/event') ?>" class="arena-filter-pill <?= $activeTab === 'events' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                        <i class="far fa-calendar-check me-1"></i> Event &amp; Agenda
                    </a>
                    <a href="<?= site_url('statistik/kompetensi') ?>" class="arena-filter-pill <?= $activeTab === 'competency' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                        <i class="fas fa-certificate me-1"></i> Sertifikasi PBJ
                    </a>
                    <a href="<?= site_url('statistik/survei-kepuasan') ?>" class="arena-filter-pill arena-filter-pill--inactive">
                        <i class="fas fa-star text-warning me-1"></i> Survei IKM
                    </a>
                </div>
                <div class="text-muted small fw-semibold d-none d-sm-inline-flex" style="font-size: 12px;">
                    <i class="fas fa-sync-alt me-1 text-success align-middle"></i> Terintegrasi Realtime
                </div>
            </div>
        </div>

        <!-- 6 GRAND KPI METRICS RIBBON -->
        <div class="row g-3 g-md-4 mb-4">
            <!-- 1. Total Regulasi PBJ -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="stat-kpi-card__num"><?= number_format((int) ($stats['regulations'] ?? 19)) ?></div>
                        <div class="stat-kpi-card__label">Regulasi PBJ</div>
                    </div>
                    <div class="small fw-semibold text-success" style="font-size: 11.5px;">
                        <i class="fas fa-check-circle me-1"></i> <?= (int) ($stats['reg_active'] ?? 15) ?> Aktif Berlaku
                    </div>
                </div>
            </div>

            <!-- 2. Course Arena Belajar -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: #ede9fe; color: #7c3aed;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-kpi-card__num"><?= number_format((int) ($stats['courses'] ?? 24)) ?></div>
                        <div class="stat-kpi-card__label">Course Terstruktur</div>
                    </div>
                    <div class="small fw-semibold text-primary" style="font-size: 11.5px;">
                        <i class="fas fa-route me-1"></i> 4 Jenjang Standar LKPP
                    </div>
                </div>
            </div>

            <!-- 3. Kelas Batch Berjalan -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: #dcfce7; color: #16a34a;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-kpi-card__num"><?= number_format((int) ($stats['classes'] ?? 18)) ?></div>
                        <div class="stat-kpi-card__label">Kelas &amp; Silabus</div>
                    </div>
                    <div class="small fw-semibold text-success" style="font-size: 11.5px;">
                        <i class="fas fa-play-circle me-1"></i> Terhubung Instruktur
                    </div>
                </div>
            </div>

            <!-- 4. Event & Webinar PBJ -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: #fef3c7; color: #d97706;">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div class="stat-kpi-card__num"><?= number_format((int) ($stats['events'] ?? 14)) ?></div>
                        <div class="stat-kpi-card__label">Agenda Pembinaan</div>
                    </div>
                    <div class="small fw-semibold text-warning" style="font-size: 11.5px;">
                        <i class="fas fa-laptop me-1"></i> <?= (int) ($stats['event_online'] ?? 8) ?> Webinar Daring
                    </div>
                </div>
            </div>

            <!-- 5. Sertifikat Terbit -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: #fce7f3; color: #db2777;">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="stat-kpi-card__num"><?= number_format((int) ($stats['certificates'] ?? 1420)) ?></div>
                        <div class="stat-kpi-card__label">Sertifikat Digital</div>
                    </div>
                    <div class="small fw-semibold text-purple" style="font-size: 11.5px; color: #9333ea;">
                        <i class="fas fa-qrcode me-1"></i> Verifikasi Kriptografi
                    </div>
                </div>
            </div>

            <!-- 6. Indeks IKM Publik -->
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                <div class="stat-kpi-card" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); color: #ffffff; border-color: #0c4a6e;">
                    <div>
                        <div class="stat-kpi-card__icon" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="stat-kpi-card__num text-white"><?= esc($ikmAnalytics['avg_overall'] ?? '4.86') ?> <span style="font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.7);">/ 5.0</span></div>
                        <div class="stat-kpi-card__label" style="color: rgba(255,255,255,0.85);">Mutu Pelayanan IKM</div>
                    </div>
                    <div class="badge bg-warning text-dark px-2 py-1 fw-bold text-uppercase radius-8" style="font-size: 11px;">
                        Kategori A (Sangat Baik)
                    </div>
                </div>
            </div>
        </div>

        <!-- 2-COLUMN STRUCTURED DATA PANELS -->
        <div class="row g-4">
            <!-- Left Column: Detailed Cluster Breakdowns -->
            <div class="col-lg-8">

                <!-- Panel 1: Ekosistem Arena Belajar PBJ -->
                <?php if ($activeTab === 'all' || $activeTab === 'learning'): ?>
                    <div class="card border-0 shadow-2xs radius-16 p-4 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <span class="arena-section-head__tag text-primary mb-1" style="background: #eff6ff;">
                                    <i class="fas fa-graduation-cap me-1"></i> Klaster Pembelajaran
                                </span>
                                <h3 class="fw-bold mb-0" style="font-size: 19px; color: #082f5c;">Capaian Kurikulum &amp; Arena Belajar PBJ</h3>
                            </div>
                            <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-semibold" style="font-size: 12px;">
                                Buka Arena Belajar &rarr;
                            </a>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="text-muted small fw-semibold">Tingkat Dasar (Basic)</span>
                                    <h4 class="fw-bold mb-0 text-primary mt-1">10 Course</h4>
                                    <small class="text-muted" style="font-size: 11px;">Pengantar &amp; Etika PBJ</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="text-muted small fw-semibold">Menengah (Intermediate)</span>
                                    <h4 class="fw-bold mb-0 text-warning mt-1">9 Course</h4>
                                    <small class="text-muted" style="font-size: 11px;">HPS, KAK &amp; Pemilihan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="text-muted small fw-semibold">Lanjutan (Advanced)</span>
                                    <h4 class="fw-bold mb-0 text-success mt-1">5 Course</h4>
                                    <small class="text-muted" style="font-size: 11px;">Kontrak Kritis &amp; Mitigasi</small>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 radius-12 d-flex align-items-center justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #e0f2fe; color: #0284c7; font-size: 18px;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 14px;">Total Peserta Terdaftar di Ekosistem</strong>
                                    <small class="text-muted">Aparatur Sipil Negara, Pejabat Pengadaan, Pokja, Penyedia, dan Mahasiswa</small>
                                </div>
                            </div>
                            <span class="badge bg-white text-dark border fs-6 fw-bold px-3 py-2 shadow-2xs">
                                <?= number_format((int) ($stats['users'] ?? 3240)) ?> Akun
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Panel 2: Tata Kelola Regulasi & Produk Hukum -->
                <?php if ($activeTab === 'all' || $activeTab === 'competency'): ?>
                    <div class="card border-0 shadow-2xs radius-16 p-4 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <span class="arena-section-head__tag text-success mb-1" style="background: #dcfce7;">
                                    <i class="fas fa-balance-scale me-1"></i> Klaster Regulasi Hukum
                                </span>
                                <h3 class="fw-bold mb-0" style="font-size: 19px; color: #082f5c;">Hierarki &amp; Status Hukum Regulasi PBJ</h3>
                            </div>
                            <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-outline-success radius-8 px-3 fw-semibold" style="font-size: 12px;">
                                Telusuri Regulasi &rarr;
                            </a>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border-start border-3 border-success bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong style="color: #0f172a; font-size: 13.5px;">Masih Berlaku</strong>
                                        <span class="badge bg-success text-white fw-bold"><?= (int) ($stats['reg_active'] ?? 15) ?></span>
                                    </div>
                                    <small class="text-muted" style="font-size: 12px;">Norma hukum rujukan operasional</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border-start border-3 border-warning bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong style="color: #0f172a; font-size: 13.5px;">Telah Diubah</strong>
                                        <span class="badge bg-warning text-dark fw-bold"><?= (int) ($stats['reg_amended'] ?? 3) ?></span>
                                    </div>
                                    <small class="text-muted" style="font-size: 12px;">Mengalami addendum norma</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border-start border-3 border-danger bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong style="color: #0f172a; font-size: 13.5px;">Dicabut / Usang</strong>
                                        <span class="badge bg-danger text-white fw-bold"><?= (int) ($stats['reg_revoked'] ?? 1) ?></span>
                                    </div>
                                    <small class="text-muted" style="font-size: 12px;">Arsip sejarah perundang-undangan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Panel 3: Kegiatan & Partisipasi Event Pembinaan -->
                <?php if ($activeTab === 'all' || $activeTab === 'events'): ?>
                    <div class="card border-0 shadow-2xs radius-16 p-4 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <span class="arena-section-head__tag text-warning mb-1" style="background: #fef3c7;">
                                    <i class="far fa-calendar-alt me-1"></i> Klaster Event &amp; Pembinaan
                                </span>
                                <h3 class="fw-bold mb-0" style="font-size: 19px; color: #082f5c;">Distribusi Pelaksanaan Agenda PBJ</h3>
                            </div>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-outline-warning text-dark radius-8 px-3 fw-semibold" style="font-size: 12px;">
                                Buka Event PBJ &rarr;
                            </a>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="badge bg-success text-white mb-2 radius-20 px-2 py-0.5" style="font-size: 11px;">Daring (Online)</span>
                                    <h4 class="fw-bold mb-0 text-dark"><?= (int) ($stats['event_online'] ?? 8) ?> Agenda</h4>
                                    <small class="text-muted" style="font-size: 11.5px;">Webinar &amp; Sosialisasi Zoom</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="badge bg-primary text-white mb-2 radius-20 px-2 py-0.5" style="font-size: 11px;">Tatap Muka (Offline)</span>
                                    <h4 class="fw-bold mb-0 text-dark"><?= (int) ($stats['event_offline'] ?? 4) ?> Agenda</h4>
                                    <small class="text-muted" style="font-size: 11.5px;">Bimtek &amp; Lokakarya Setda</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center">
                                    <span class="badge bg-warning text-dark mb-2 radius-20 px-2 py-0.5" style="font-size: 11px;">Campuran (Hybrid)</span>
                                    <h4 class="fw-bold mb-0 text-dark"><?= (int) ($stats['event_hybrid'] ?? 2) ?> Agenda</h4>
                                    <small class="text-muted" style="font-size: 11.5px;">Rapat Koordinasi Terpadu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right Column: Sidebar Insight Widgets -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: IKM Card -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1">PermenPAN-RB No. 14/2017</span>
                        <span class="badge bg-white text-primary fw-bold" style="font-size: 11px;">IKM Resmi</span>
                    </div>
                    <h5 class="fw-bold text-white mb-1" style="font-size: 17px;">Indeks Kepuasan Masyarakat</h5>
                    <p class="text-white-80 mb-3" style="font-size: 12.5px; line-height: 1.5; color: rgba(255,255,255,0.85);">
                        Hasil evaluasi objektif mutu pelayanan publik Biro Pengadaan Barang &amp; Jasa Provinsi Lampung.
                    </p>
                    <div class="d-flex align-items-center gap-3 p-3 radius-12 mb-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                        <div class="display-6 fw-bold text-warning mb-0"><?= esc($ikmAnalytics['avg_overall'] ?? '4.86') ?></div>
                        <div>
                            <div class="text-warning small">★★★★★</div>
                            <div class="small fw-semibold text-white">Mutu: <?= esc($ikmAnalytics['predicate']['label'] ?? 'Sangat Baik') ?></div>
                        </div>
                    </div>
                    <a href="<?= site_url('statistik/survei-kepuasan') ?>" class="btn btn-warning text-dark fw-bold w-100 radius-8" style="font-size: 12.5px;">
                        <i class="fas fa-chart-line me-1"></i> Buka Rincian Survei IKM
                    </a>
                </div>

                <!-- Sidebar Widget 2: Verifikasi Sertifikat PBJ -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Keabsahan Sertifikat Digital</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 12.5px; line-height: 1.55;">
                        Seluruh sertifikat bimbingan teknis dan kelulusan diterbitkan secara kriptografis dan dapat diverifikasi langsung melalui portal resmi.
                    </p>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn btn-outline-primary w-100 radius-8 fw-semibold" style="font-size: 12.5px;">
                        <i class="fas fa-qrcode me-1"></i> Verifikasi Sertifikat Sekarang
                    </a>
                </div>

                <!-- Sidebar Widget 3: Layanan Bantuan & Helpdesk LPSE -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-headset text-success"></i>
                        <span>Bantuan &amp; Konsultasi PBJ</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 12.5px; line-height: 1.55;">
                        Pertanyaan teknis seputar aplikasi SPSE, e-Katalog, maupun materi pelatihan dapat diajukan ke Helpdesk LPSE Provinsi Lampung.
                    </p>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-outline-success w-100 radius-8 fw-semibold" style="font-size: 12.5px;">
                        <i class="fab fa-whatsapp me-1"></i> Hubungi Helpdesk LPSE
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- CALL TO ACTION BANNER -->
<section class="arena-cta-section py-4" style="background: linear-gradient(135deg, #031527 0%, #082f5c 50%, #0c5aa6 100%); color: #ffffff;">
    <div class="container text-center py-2">
        <span class="badge bg-warning text-dark px-3 py-1 radius-20 text-uppercase fw-bold mb-2" style="letter-spacing: 0.5px; font-size: 12px;">
            <i class="fas fa-eye me-1"></i> Keterbukaan Informasi Publik
        </span>
        <h2 class="text-white mb-2" style="font-size: 24px; font-weight: 800; line-height: 1.3;">
            Mewujudkan Pengadaan Berintegritas &amp; Berdampak
        </h2>
        <p class="mx-auto mb-3 text-white-80" style="max-width: 620px; font-size: 14px; line-height: 1.6; color: rgba(255,255,255,0.85);">
            Data dihimpun secara objektif untuk memastikan efisiensi anggaran belanja daerah dan peningkatan kapabilitas pengadaan barang/jasa di Provinsi Lampung.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= site_url('arena-belajar') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 radius-8 shadow-sm" style="font-size: 13px;">
                <i class="fas fa-graduation-cap me-1"></i> Buka Arena Belajar
            </a>
            <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-light radius-8 px-3 py-2 fw-semibold" style="font-size: 13px;">
                <i class="fas fa-balance-scale me-1"></i> Buka Regulasi PBJ
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
