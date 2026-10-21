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

    <!-- 3. PINTASAN AKSES CEPAT EKSEKUTIF (FiStudy Action Cards) -->
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

    <!-- 4. 2 KOLOM: TOP CLASSES vs SYSTEM AUDIT LOG -->
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