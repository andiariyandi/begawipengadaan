<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="admin-workspace-home">

    <!-- 1. HERO EXECUTIVE WELCOME & SPOTLIGHT BANNER -->
    <div class="ws-hero-card shadow-sm p-4 p-lg-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-crown me-1"></i> Pusat Kendali Eksekutif
                    </span>
                    <span class="badge bg-white text-navy px-3 py-2 rounded-pill small fw-bold shadow-sm">
                        <i class="fas fa-shield-alt text-success me-1"></i> <?= esc($roleLabel) ?> BegawiPengadaan
                    </span>
                </div>
                <h1 class="h2 text-white fw-bold mb-2">
                    Halo, <?= esc($userName) ?>! 👋
                </h1>
                <p class="text-white-50 mb-0 fs-6" style="max-width: 650px;">
                    Pusat ringkasan dan pengawasan menyeluruh aktivitas pembelajaran, operasional pengguna, penugasan pengajar, pelaksanaan agenda event, hingga integritas penerbitan sertifikat resmi Pemerintah Provinsi Lampung.
                </p>
            </div>

            <!-- Spotlight Box (Glassmorphism DNA) -->
            <div class="col-lg-5">
                <div class="ws-hero-glass-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-warning small fw-bold text-uppercase">
                            <i class="fas fa-chart-line me-1"></i> Status Ekosistem PBJ
                        </span>
                        <span class="badge bg-success text-white small px-2 py-1 rounded-pill">
                            <?= number_format((int) ($stats['activeUsers'] ?? 0)) ?> Pengguna Aktif
                        </span>
                    </div>
                    <h3 class="h6 text-white fw-bold text-truncate mb-1">
                        Tata Kelola & Pengawasan Terpadu
                    </h3>
                    <p class="small text-white-50 text-truncate mb-3">
                        <?= (int) ($stats['activeClasses'] ?? 0) ?> Kelas Berjalan &middot; <?= number_format((int) ($stats['totalCerts'] ?? 0)) ?> Sertifikat Terbit &middot; <?= (int) ($stats['activeEvents'] ?? 0) ?> Event
                    </p>
                    <div class="d-flex gap-2">
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill flex-grow-1 py-2 shadow-sm">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard WowDash
                        </a>
                        <a href="<?= route_to('workspace.operator.home') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                            <i class="fas fa-cogs me-1"></i> Ruang Operasional
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. 6 EXECUTIVE KPI STAT CARDS (FiStudy DNA) -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Pengguna -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #eff6ff; color: #082f5c; width: 44px; height: 44px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Total Pengguna</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($stats['totalUsers'] ?? 0)) ?></div>
                        <small class="text-success d-block" style="font-size: 0.68rem;"><?= (int) ($stats['activeUsers'] ?? 0) ?> aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Antrean Approval -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fefce8; color: #ca8a04; width: 44px; height: 44px;">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Pending Approval</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) (($stats['pendingVerif'] ?? 0) + ($stats['pendingApprov'] ?? 0))) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><?= (int) ($stats['pendingVerif'] ?? 0) ?> verif user</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Kelas Aktif Pembelajaran -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #e0f2fe; color: #0284c7; width: 44px; height: 44px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Kelas Aktif</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeClasses'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><?= (int) ($stats['totalEnroll'] ?? 0) ?> peserta</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Pengajar / Trainer Bertugas -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #f0fdf4; color: #16a34a; width: 44px; height: 44px;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Trainer Bertugas</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeTrainers'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">instruktur aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Event & Agenda Berjalan -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fef2f2; color: #dc2626; width: 44px; height: 44px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Event Aktif</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeEvents'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><?= (int) ($stats['totalEventReg'] ?? 0) ?> pendaftar</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Total Sertifikat Terbit -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fef3c7; color: #d97706; width: 44px; height: 44px;">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Sertifikat Terbit</span>
                        <div class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($stats['totalCerts'] ?? 0)) ?></div>
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><?= (int) ($stats['pendingCerts'] ?? 0) ?> antrean</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. EXECUTIVE HEALTH RADAR (4 DOMAIN PBJ) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill small">
                        <i class="fas fa-heartbeat me-1"></i> Health Radar
                    </span>
                    <h2 class="h5 fw-bold text-navy mb-0">Executive Health Radar (4 Domain PBJ)</h2>
                </div>
                <small class="text-muted">Diagnostik kesehatan kelulusan peserta, efektivitas evaluasi trainer, kecepatan penerbitan sertifikat, dan indeks presensi acara</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small fw-semibold">
                    <i class="fas fa-check-circle me-1"></i> Sistem Berjalan Sehat
                </span>
            </div>
        </div>

        <div class="row g-3">
            <!-- Radar 1: Kelulusan Akademik -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="ws-radar-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="ws-radar-avatar" style="background: #eff6ff; color: #2563eb;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <span class="badge <?= esc($healthRadar['completion']['badge'] ?? 'bg-primary text-white') ?> rounded-pill small px-2 py-1">
                                <i class="<?= esc($healthRadar['completion']['icon'] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($healthRadar['completion']['status'] ?? 'Normal') ?>
                            </span>
                        </div>
                        <span class="text-muted small fw-semibold d-block" style="font-size: 0.78rem;">Kelulusan Akademik</span>
                        <div class="h3 fw-bold text-navy mb-0"><?= number_format((float) ($healthRadar['completion']['rate'] ?? 0), 1) ?><span class="fs-6 fw-normal text-muted ms-1">%</span></div>
                        <div class="ws-radar-meter">
                            <div class="ws-radar-meter-fill bg-primary" style="width: <?= min(100, max(5, (float) ($healthRadar['completion']['rate'] ?? 0))) ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <p class="small text-muted mb-1" style="font-size: 0.72rem; line-height: 1.3; min-height: 28px;">
                            <?= esc($healthRadar['completion']['note'] ?? '') ?>
                        </p>
                        <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size: 0.7rem;">
                            <span>Peserta Lulus:</span>
                            <strong class="text-navy"><?= number_format((int) ($healthRadar['completion']['completed'] ?? 0)) ?> / <?= number_format((int) ($healthRadar['completion']['total'] ?? 0)) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Radar 2: Efektivitas Evaluasi Penilaian -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="ws-radar-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="ws-radar-avatar" style="background: #fefce8; color: #ca8a04;">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <span class="badge <?= esc($healthRadar['turnaround']['badge'] ?? 'bg-warning text-dark') ?> rounded-pill small px-2 py-1">
                                <i class="<?= esc($healthRadar['turnaround']['icon'] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($healthRadar['turnaround']['status'] ?? 'Moderat') ?>
                            </span>
                        </div>
                        <span class="text-muted small fw-semibold d-block" style="font-size: 0.78rem;">Evaluasi Tugas Trainer</span>
                        <div class="h3 fw-bold text-navy mb-0"><?= number_format((float) ($healthRadar['turnaround']['rate'] ?? 0), 1) ?><span class="fs-6 fw-normal text-muted ms-1">%</span></div>
                        <div class="ws-radar-meter">
                            <div class="ws-radar-meter-fill bg-warning" style="width: <?= min(100, max(5, (float) ($healthRadar['turnaround']['rate'] ?? 0))) ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <p class="small text-muted mb-1" style="font-size: 0.72rem; line-height: 1.3; min-height: 28px;">
                            <?= esc($healthRadar['turnaround']['note'] ?? '') ?>
                        </p>
                        <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size: 0.7rem;">
                            <span>Tugas Dinilai:</span>
                            <strong class="text-navy"><?= number_format((int) ($healthRadar['turnaround']['graded'] ?? 0)) ?> (<?= number_format((int) ($healthRadar['turnaround']['pending'] ?? 0)) ?> antrean)</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Radar 3: Integritas & Kecepatan Sertifikat -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="ws-radar-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="ws-radar-avatar" style="background: #f0fdf4; color: #16a34a;">
                                <i class="fas fa-award"></i>
                            </div>
                            <span class="badge <?= esc($healthRadar['certVelocity']['badge'] ?? 'bg-success text-white') ?> rounded-pill small px-2 py-1">
                                <i class="<?= esc($healthRadar['certVelocity']['icon'] ?? 'fas fa-check-circle') ?> me-1"></i><?= esc($healthRadar['certVelocity']['status'] ?? 'Terkendali') ?>
                            </span>
                        </div>
                        <span class="text-muted small fw-semibold d-block" style="font-size: 0.78rem;">Penerbitan Sertifikat</span>
                        <div class="h3 fw-bold text-navy mb-0"><?= number_format((float) ($healthRadar['certVelocity']['rate'] ?? 0), 1) ?><span class="fs-6 fw-normal text-muted ms-1">%</span></div>
                        <div class="ws-radar-meter">
                            <div class="ws-radar-meter-fill bg-success" style="width: <?= min(100, max(5, (float) ($healthRadar['certVelocity']['rate'] ?? 0))) ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <p class="small text-muted mb-1" style="font-size: 0.72rem; line-height: 1.3; min-height: 28px;">
                            <?= esc($healthRadar['certVelocity']['note'] ?? '') ?>
                        </p>
                        <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size: 0.7rem;">
                            <span>Bulan Ini:</span>
                            <strong class="text-success">+<?= number_format((int) ($healthRadar['certVelocity']['thisMonth'] ?? 0)) ?> terbit</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Radar 4: Partisipasi & Presensi Event -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="ws-radar-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="ws-radar-avatar" style="background: #fef2f2; color: #dc2626;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <span class="badge <?= esc($healthRadar['attendance']['badge'] ?? 'bg-danger text-white') ?> rounded-pill small px-2 py-1">
                                <i class="<?= esc($healthRadar['attendance']['icon'] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($healthRadar['attendance']['status'] ?? 'Sedang') ?>
                            </span>
                        </div>
                        <span class="text-muted small fw-semibold d-block" style="font-size: 0.78rem;">Presensi Kegiatan Event</span>
                        <div class="h3 fw-bold text-navy mb-0"><?= number_format((float) ($healthRadar['attendance']['rate'] ?? 0), 1) ?><span class="fs-6 fw-normal text-muted ms-1">%</span></div>
                        <div class="ws-radar-meter">
                            <div class="ws-radar-meter-fill bg-danger" style="width: <?= min(100, max(5, (float) ($healthRadar['attendance']['rate'] ?? 0))) ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <p class="small text-muted mb-1" style="font-size: 0.72rem; line-height: 1.3; min-height: 28px;">
                            <?= esc($healthRadar['attendance']['note'] ?? '') ?>
                        </p>
                        <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="font-size: 0.7rem;">
                            <span>Kehadiran Sah:</span>
                            <strong class="text-navy"><?= number_format((int) ($healthRadar['attendance']['attended'] ?? 0)) ?> / <?= number_format((int) ($healthRadar['attendance']['total'] ?? 0)) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MONITORING UTILISASI ASET & ROLE SIMULATOR -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Utilisasi Berkas & Aset Pembelajaran -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-database text-primary me-2"></i>Utilisasi Berkas & Aset Pembelajaran
                        </h2>
                        <small class="text-muted">Kapasitas penyimpanan dokumen materi, kuis, dan file sertifikat resmi</small>
                    </div>
                    <span class="badge bg-light text-navy border px-3 py-1.5 rounded-pill small fw-bold">
                        <i class="fas fa-hdd me-1 text-primary"></i> <?= esc($assetStats['storageFormatted'] ?? '0 MB') ?> Terpakai
                    </span>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <i class="fas fa-file-alt text-primary fs-3 mb-2"></i>
                            <span class="text-muted small d-block" style="font-size: 0.75rem;">Berkas Terkelola</span>
                            <h4 class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($assetStats['totalFiles'] ?? 0)) ?></h4>
                            <small class="text-muted" style="font-size: 0.68rem;">dokumen diunggah</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <i class="fas fa-book-open text-success fs-3 mb-2"></i>
                            <span class="text-muted small d-block" style="font-size: 0.75rem;">Materi & Lesson</span>
                            <h4 class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($assetStats['totalLessons'] ?? 0)) ?></h4>
                            <small class="text-muted" style="font-size: 0.68rem;">materi kurikulum</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <i class="fas fa-question-circle text-warning fs-3 mb-2"></i>
                            <span class="text-muted small d-block" style="font-size: 0.75rem;">Instrumen Kuis</span>
                            <h4 class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($assetStats['totalQuizzes'] ?? 0)) ?></h4>
                            <small class="text-muted" style="font-size: 0.68rem;">evaluasi asesmen</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <i class="fas fa-certificate text-danger fs-3 mb-2"></i>
                            <span class="text-muted small d-block" style="font-size: 0.75rem;">PDF Sertifikat</span>
                            <h4 class="h5 fw-bold text-navy mb-0"><?= number_format((int) ($stats['totalCerts'] ?? 0)) ?></h4>
                            <small class="text-muted" style="font-size: 0.68rem;">arsip digital</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Simulator Tinjau Peran (Role Preview Mode) -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="mb-3 pb-2 border-bottom">
                    <h2 class="h5 fw-bold text-navy mb-0">
                        <i class="fas fa-user-secret text-warning me-2"></i>Role Simulator (Pratinjau)
                    </h2>
                    <small class="text-muted">Tinjau ruang kerja dari sudut pandang peran lain</small>
                </div>
                <p class="small text-muted mb-3" style="font-size: 0.75rem;">
                    Buka ruang belajar atau kendali pengajar secara langsung untuk memvalidasi kendala teknis pengguna:
                </p>
                <div class="d-flex flex-column gap-2">
                    <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-sm btn-outline-primary text-start rounded-pill px-3 py-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-graduation-cap me-2 text-primary"></i>Sudut Pandang Peserta</span>
                        <i class="fas fa-arrow-right small text-muted"></i>
                    </a>
                    <a href="<?= route_to('workspace.trainer.home') ?>" class="btn btn-sm btn-outline-success text-start rounded-pill px-3 py-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-chalkboard-teacher me-2 text-success"></i>Sudut Pandang Pengajar</span>
                        <i class="fas fa-arrow-right small text-muted"></i>
                    </a>
                    <a href="<?= route_to('workspace.operator.home') ?>" class="btn btn-sm btn-outline-warning text-dark text-start rounded-pill px-3 py-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-cogs me-2 text-warning"></i>Sudut Pandang Operator</span>
                        <i class="fas fa-arrow-right small text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. PINTASAN AKSES CEPAT EKSEKUTIF (FiStudy Action Cards) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-compass text-primary me-2"></i>Pintasan Akses Cepat Eksekutif
                </h2>
                <small class="text-muted">Akses kilat monitoring, verifikasi, evaluasi, dan pengelolaan sistem</small>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="<?= route_to('workspace.operator.approvals') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-stamp"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Pusat Approval</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Setujui akun & peran</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= route_to('workspace.operator.learning-monitoring') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-info text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Monitoring Belajar</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Pengawasan seluruh kelas</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-success text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Penugasan Trainer</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Atur pengajar kelas</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= route_to('workspace.operator.certificates') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Pusat Sertifikat</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Verifikasi kelayakan</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 6. 2 KOLOM: TOP CLASSES vs SYSTEM AUDIT LOG -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Kelas Teraktif & Ringkasan Partisipasi -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-chalkboard text-primary me-2"></i>Kelas Pembelajaran Teraktif
                        </h2>
                        <small class="text-muted">Kelas dengan jumlah partisipasi peserta tertinggi di ekosistem</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.learning-monitoring') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        Semua Kelas
                    </a>
                </div>

                <?php if (empty($topClasses)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fs-2 mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada kelas aktif terdata.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3 ws-paginated-container" data-page-size="4">
                        <?php foreach ($topClasses as $tc): ?>
                            <div class="col-12 col-md-6 ws-paginated-item">
                                <div class="ws-class-card p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-secondary-subtle text-secondary font-monospace small px-2 py-1 rounded-pill">
                                                <?= esc($tc['code']) ?>
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary small rounded-pill px-2 py-1 fw-bold">
                                                <i class="fas fa-users me-1"></i> <?= (int) ($tc['total_students'] ?? 0) ?> Peserta
                                            </span>
                                        </div>
                                        <h3 class="h6 fw-bold text-navy mb-1" style="min-height: 2.2rem; line-height: 1.3;">
                                            <?= esc($tc['name']) ?>
                                        </h3>
                                        <p class="small text-muted mb-3 text-truncate"><?= esc($tc['course_title'] ?? 'Course PBJ') ?></p>
                                    </div>
                                    <div>
                                        <a href="<?= route_to('workspace.trainer.class.detail', (string) $tc['id']) ?>" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold">
                                            <i class="fas fa-eye me-1"></i> Ruang Kendali Kelas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Log Aktivitas Audit Sistem -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-shield-alt text-success me-2"></i>Audit Log Sistem
                        </h2>
                        <small class="text-muted">Jejak aktivitas operasional eksekutif terakhir</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.activity-monitoring') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        Semua Log
                    </a>
                </div>

                <?php if (empty($recentAuditLogs)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fs-2 mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada catatan log aktivitas.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="5">
                        <?php foreach ($recentAuditLogs as $log): ?>
                            <div class="list-group-item px-0 py-2 border-bottom ws-paginated-item">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="text-navy small text-truncate" style="max-width: 65%;">
                                        <?= esc(ucwords(str_replace('_', ' ', (string) ($log['action'] ?? 'Aktivitas')))) ?>
                                    </strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="far fa-clock me-1"></i> <?= ! empty($log['created_at']) ? date('d/m H:i', strtotime($log['created_at'])) : '-' ?>
                                    </small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate" style="font-size: 0.75rem; max-width: 75%;">
                                        Oleh: <strong><?= esc($log['actor_name'] ?? 'Sistem') ?></strong> (<?= esc($log['resource_type'] ?? '-') ?> #<?= (int) ($log['resource_id'] ?? 0) ?>)
                                    </small>
                                    <span class="badge bg-success-subtle text-success small rounded-pill" style="font-size: 0.68rem;">Sukses</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
