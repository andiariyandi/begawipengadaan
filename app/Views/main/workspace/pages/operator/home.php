<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-workspace-home">

    <!-- 1. HERO OPERATIONAL WELCOME & SPOTLIGHT BANNER -->
    <div class="ws-hero-card shadow-sm p-4 p-lg-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-cogs me-1"></i> Pusat Operasional Harian Sistem
                    </span>
                    <span class="badge bg-white-50 text-white px-3 py-2 rounded-pill small">
                        <i class="fas fa-check-double text-success me-1"></i> Otoritas Operasional BegawiPengadaan
                    </span>
                </div>
                <h1 class="h2 text-white fw-bold mb-2">
                    Halo, <?= esc($userName ?? 'Operator') ?>! 👋
                </h1>
                <p class="text-white-50 mb-0 fs-6" style="max-width: 620px;">
                    Pusat komando operasional platform. Kelola antrean persetujuan, verifikasi profil peserta baru, pantau penugasan trainer, atur kegiatan event workshop, dan kawal kelayakan penerbitan sertifikat resmi.
                </p>
            </div>

            <!-- Spotlight Box (Glassmorphism DNA) -->
            <div class="col-lg-5">
                <?php
                $urgentCount = (int) ($stats['pendingApproval'] ?? 0) + (int) ($stats['userPendingVerif'] ?? 0);
                if ($urgentCount > 0):
                ?>
                    <div class="ws-hero-glass-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-warning small fw-bold text-uppercase">
                                <i class="fas fa-bell me-1"></i> Antrean Prioritas Operasional
                            </span>
                            <span class="badge bg-danger text-white small px-2 py-1 rounded-pill">
                                <?= $urgentCount ?> Permohonan
                            </span>
                        </div>
                        <h3 class="h6 text-white fw-bold text-truncate mb-1">
                            Verifikasi Akun & Permohonan Approval
                        </h3>
                        <p class="small text-white-50 text-truncate mb-3">
                            <?= (int) ($stats['userPendingVerif'] ?? 0) ?> profil user baru & <?= (int) ($stats['pendingApproval'] ?? 0) ?> approval menunggu persetujuan Anda.
                        </p>
                        <a href="<?= route_to('workspace.operator.approvals') ?>" class="btn btn-warning text-dark fw-bold btn-sm w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-stamp me-1"></i> Tinjau Antrean Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <div class="ws-hero-glass-box text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h3 class="h6 text-white fw-bold mb-1">Semua Antrean Bersih</h3>
                        <p class="small text-white-50 mb-3">Tidak ada permohonan persetujuan mendesak. Seluruh sistem berjalan normal.</p>
                        <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-light fw-bold btn-sm rounded-pill px-4 py-2">
                            <i class="fas fa-chalkboard-teacher me-1 text-primary"></i> Kelola Penugasan Trainer
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. 7 SUMMARY KPI STAT CARDS (FiStudy DNA) -->
    <div class="row g-3 mb-4">
        <!-- 1. Pending Approval -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Pending Approval</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingApproval'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. User Pending Verif -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fef2f2; color: #dc2626;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Verifikasi User</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['userPendingVerif'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Kelas Aktif -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #eff6ff; color: #082f5c;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Kelas Aktif</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeClasses'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Trainer Aktif -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Trainer Bertugas</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeTrainers'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Event Berjalan -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Event Aktif</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['ongoingEvents'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Presensi Pending -->
        <div class="col-6 col-md-6 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #f1f5f9; color: #475569;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Presensi Pending</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingAttendance'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Sertifikat Pending -->
        <div class="col-12 col-md-6 col-xl">
            <div class="card ws-stat-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="ws-stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block" style="font-size: 0.72rem;">Sertifikat Pending</span>
                        <div class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingCertificates'] ?? 0)) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. AKSI CEPAT OPERASIONAL (FiStudy Elevated Action Cards) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat Operasional
                </h2>
                <small class="text-muted">Pintasan navigasi langsung tugas-tugas inti operasional harian</small>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Assign Trainer</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Hubungkan pengajar ke kelas</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.profile-verifications') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-danger text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Verifikasi User</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Tinjau berkas pendaftar baru</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.certificates') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-success text-white" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Verifikasi Sertifikat</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Proses kelulusan & terbitkan</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.evaluations') ?>" class="card ws-action-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block small">Evaluasi & Override</strong>
                            <small class="text-muted" style="font-size: 0.72rem;">Koreksi nilai & penyesuaian</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. 4 OPERATIONAL ACTIVITIES FEEDS -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Approval Terbaru & Assignment Trainer Terbaru -->
        <div class="col-12 col-lg-6">
            <!-- Approval Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-stamp text-warning me-2"></i>Approval Terbaru
                        </h2>
                        <small class="text-muted">Permohonan persetujuan akun & perubahan status</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.approvals') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        Lihat Semua
                    </a>
                </div>
                <?php if (empty($recentApprovals)): ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0 small">Tidak ada permohonan approval terkini.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="5">
                        <?php foreach ($recentApprovals as $appr): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom ws-paginated-item">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($appr['requested_action'] ?? '-') ?> &middot; #<?= esc($appr['id']) ?></strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Pemohon: <strong><?= esc($appr['requester_name'] ?? 'Sistem') ?></strong> &middot; <?= date('d/m/Y H:i', strtotime($appr['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge <?= ($appr['status'] === 'approved') ? 'bg-success-subtle text-success' : (($appr['status'] === 'rejected') ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning-emphasis') ?> rounded-pill px-3 py-1 small">
                                    <?= esc(ucfirst($appr['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Assignment Trainer Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Penugasan Trainer Terbaru
                        </h2>
                        <small class="text-muted">Riwayat penugasan instruktur ke kelas pembelajaran</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        Kelola Penugasan
                    </a>
                </div>
                <?php if (empty($recentAssignments)): ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0 small">Belum ada riwayat penugasan trainer.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="5">
                        <?php foreach ($recentAssignments as $asn): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom ws-paginated-item">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($asn['trainer_name'] ?? 'Trainer') ?></strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Kelas: <?= esc($asn['class_name'] ?? '-') ?> <span class="badge bg-light text-secondary font-monospace"><?= esc($asn['class_code'] ?? '-') ?></span>
                                    </small>
                                </div>
                                <span class="badge <?= ($asn['status'] === 'active') ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-3 py-1 small">
                                    <?= esc(ucfirst($asn['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Event Terbaru & Override Terbaru -->
        <div class="col-12 col-lg-6">
            <!-- Event Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-calendar-alt text-success me-2"></i>Event PBJ Terbaru
                        </h2>
                        <small class="text-muted">Agenda kegiatan workshop dan seminar PBJ</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.events') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        Lihat Event
                    </a>
                </div>
                <?php if (empty($recentEvents)): ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0 small">Belum ada event yang dijadwalkan.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="5">
                        <?php foreach ($recentEvents as $ev): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom ws-paginated-item">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($ev['title']) ?></strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= esc($ev['type'] ?? 'Event') ?> &middot; Mulai: <?= $ev['start_at'] ? date('d/m/Y H:i', strtotime($ev['start_at'])) : '-' ?> WIB
                                    </small>
                                </div>
                                <span class="badge <?= ($ev['status'] === 'published' || $ev['status'] === 'ongoing') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill px-3 py-1 small">
                                    <?= esc(ucfirst($ev['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Override Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-user-shield text-danger me-2"></i>Log Override Terbaru
                        </h2>
                        <small class="text-muted">Audit trail penyesuaian khusus dan koreksi operasional</small>
                    </div>
                    <a href="<?= route_to('workspace.operator.evaluations') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        Lihat Evaluasi
                    </a>
                </div>
                <?php if (empty($recentOverrides)): ?>
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0 small">Belum ada tindakan override operasional.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="5">
                        <?php foreach ($recentOverrides as $ov): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom ws-paginated-item">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($ov['action']) ?> #<?= esc($ov['resource_id'] ?? '-') ?></strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Operator: <strong><?= esc($ov['actor_name'] ?? 'Sistem') ?></strong> &middot; <?= date('d/m/Y H:i', strtotime($ov['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 small">
                                    Override
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>