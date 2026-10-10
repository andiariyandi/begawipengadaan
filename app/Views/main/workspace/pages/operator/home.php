<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-home-wrapper">
    <!-- Hero Operational Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0d3b66 0%, #001f3f 100%);">
        <div class="card-body p-4 p-lg-5 text-white position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-2">
                        <i class="fas fa-cogs me-1"></i> Pusat Operasional Harian Sistem
                    </span>
                    <h1 class="h2 text-white fw-bold mb-2">Dashboard Operasional, <?= esc($userName ?? 'Operator') ?>!</h1>
                    <p class="text-white-50 mb-0 fs-6">
                        Pantau antrean persetujuan, verifikasi profil pengguna, penugasan trainer ke kelas, operasional event, dan kelayakan sertifikat.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= route_to('workspace.operator.approvals') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-stamp me-1"></i> Antrean Approval (<?= (int) ($stats['pendingApproval'] ?? 0) ?>)
                    </a>
                    <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-light fw-semibold px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-chalkboard-teacher text-primary me-1"></i> Penugasan Trainer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 7 Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Pending Approval -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Pending Approval</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingApproval'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. User Pending Verification -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Verifikasi User</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['userPendingVerif'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Kelas Aktif -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Aktif</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeClasses'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Trainer Aktif -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Trainer Aktif</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['activeTrainers'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Event Berjalan -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Event Berjalan</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['ongoingEvents'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Presensi Pending -->
        <div class="col-6 col-md-6 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-secondary-subtle text-secondary fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Presensi Pending</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingAttendance'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Sertifikat Pending -->
        <div class="col-12 col-md-6 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary text-white fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Sertifikat Pending</span>
                        <span class="h4 fw-bold text-navy mb-0"><?= sprintf('%02d', (int) ($stats['pendingCertificates'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Shortcuts (FiStudy DNA) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <h2 class="h5 fw-bold text-navy mb-3"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat Operasional</h2>
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 44px; height: 44px;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Assign Trainer</strong>
                            <small class="text-muted">Hubungkan pengajar ke kelas PBJ</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.profile-verifications') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-danger text-white" style="width: 44px; height: 44px;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Verifikasi User</strong>
                            <small class="text-muted">Tinjau berkas profil peserta baru</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.certificates') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-success text-white" style="width: 44px; height: 44px;">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Verifikasi Sertifikat</strong>
                            <small class="text-muted">Proses kelulusan & terbitkan sertifikat</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="<?= route_to('workspace.operator.evaluations') ?>" class="card text-decoration-none border rounded-3 p-3 h-100 bg-light-hover">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-warning text-dark" style="width: 44px; height: 44px;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <strong class="text-navy d-block">Evaluasi & Override</strong>
                            <small class="text-muted">Koreksi nilai & kasus khusus</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Operational Activities Feeds -->
    <div class="row g-4">
        <!-- Kolom Kiri: Approval Terbaru & Assignment Trainer Terbaru -->
        <div class="col-12 col-lg-6">
            <!-- Approval Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-stamp text-warning me-2"></i>Approval Terbaru</h2>
                    <a href="<?= route_to('workspace.operator.approvals') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                </div>
                <?php if (empty($recentApprovals)): ?>
                    <p class="text-muted small py-3 mb-0 text-center">Tidak ada permohonan approval terkini.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentApprovals as $appr): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($appr['requested_action'] ?? '-') ?> &middot; #<?= esc($appr['id']) ?></strong>
                                    <small class="text-muted">Pemohon: <?= esc($appr['requester_name'] ?? 'Sistem') ?> &middot; <?= date('d/m/Y H:i', strtotime($appr['created_at'])) ?></small>
                                </div>
                                <span class="badge <?= ($appr['status'] === 'approved') ? 'bg-success' : (($appr['status'] === 'rejected') ? 'bg-danger' : 'bg-warning text-dark') ?> rounded-pill px-2 py-1 small">
                                    <?= esc(ucfirst($appr['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Assignment Trainer Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Penugasan Trainer Terbaru</h2>
                    <a href="<?= route_to('workspace.operator.class-trainers') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Kelola Penugasan</a>
                </div>
                <?php if (empty($recentAssignments)): ?>
                    <p class="text-muted small py-3 mb-0 text-center">Belum ada riwayat penugasan trainer.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentAssignments as $asn): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($asn['trainer_name'] ?? 'Trainer') ?></strong>
                                    <small class="text-muted">Kelas: <?= esc($asn['class_name'] ?? '-') ?> (<?= esc($asn['class_code'] ?? '-') ?>)</small>
                                </div>
                                <span class="badge <?= ($asn['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-2 py-1 small">
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-calendar-alt text-success me-2"></i>Event PBJ Terbaru</h2>
                    <a href="<?= route_to('workspace.operator.events') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Event</a>
                </div>
                <?php if (empty($recentEvents)): ?>
                    <p class="text-muted small py-3 mb-0 text-center">Belum ada event yang dijadwalkan.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentEvents as $ev): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($ev['title']) ?></strong>
                                    <small class="text-muted"><?= esc($ev['type'] ?? '-') ?> &middot; Mulai: <?= $ev['start_at'] ? date('d/m/Y H:i', strtotime($ev['start_at'])) : '-' ?></small>
                                </div>
                                <span class="badge <?= ($ev['status'] === 'published' || $ev['status'] === 'ongoing') ? 'bg-success' : 'bg-warning text-dark' ?> rounded-pill px-2 py-1 small">
                                    <?= esc(ucfirst($ev['status'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Override Terbaru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-user-shield text-danger me-2"></i>Log Override Terbaru</h2>
                    <a href="<?= route_to('workspace.operator.evaluations') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Evaluasi</a>
                </div>
                <?php if (empty($recentOverrides)): ?>
                    <p class="text-muted small py-3 mb-0 text-center">Belum ada tindakan override operasional.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentOverrides as $ov): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-navy small d-block"><?= esc($ov['action']) ?> #<?= esc($ov['resource_id'] ?? '-') ?></strong>
                                    <small class="text-muted">Operator: <?= esc($ov['actor_name'] ?? 'Sistem') ?> &middot; <?= date('d/m/Y H:i', strtotime($ov['created_at'])) ?></small>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">
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
