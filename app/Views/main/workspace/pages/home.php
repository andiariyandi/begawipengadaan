<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Welcome Banner -->
<div class="bg-white border rounded-4 p-4 p-lg-5 mb-4 shadow-sm position-relative overflow-hidden">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="fas fa-sparkles me-1"></i> Ruang Kerja <?= esc($roleLabel) ?>
            </span>
            <h1 class="h2 fw-bold mb-2" style="color: #082f5c;">Selamat Datang, <?= esc($userName) ?>!</h1>
            <p class="text-muted mb-0">
                Kelola aktivitas pembelajaran, tugas, assessment, dan pengembangan kompetensi pengadaan barang/jasa Anda secara efektif.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <?php if ($primaryRole === 'participant'): ?>
                <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-graduation-cap me-2"></i> Kelas Saya
                </a>
            <?php elseif ($primaryRole === 'trainer'): ?>
                <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Kelola Kelas
                </a>
            <?php elseif ($primaryRole === 'operator'): ?>
                <a href="<?= route_to('workspace.operator.profile-verifications') ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-user-check me-2"></i> Verifikasi User
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- KPI Stat Cards -->
<?php if (!empty($kpiCards)): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($kpiCards as $stat): ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted small fw-semibold text-uppercase"><?= esc($stat['title']) ?></span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fff3ee; color: #ff6b35;">
                            <i class="<?= esc($stat['icon'] ?? 'fas fa-chart-bar') ?> fs-5"></i>
                        </div>
                    </div>
                    <div class="display-6 fw-bold mb-1" style="color: #082f5c;"><?= esc($stat['value']) ?></div>
                    <small class="text-muted"><?= esc($stat['meta']) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Main Dashboard Grid -->
<div class="row g-4 mb-4">
    <!-- Left Column: Classes / Penugasan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">
                        <?= $primaryRole === 'trainer' ? 'Kelas yang Anda Ampu' : 'Pembelajaran Terkini' ?>
                    </h2>
                    <small class="text-muted">Pantau progres dan kelanjutan pembelajaran Anda</small>
                </div>
                <?php if ($primaryRole === 'participant'): ?>
                    <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">Lihat Semua</a>
                <?php elseif ($primaryRole === 'trainer'): ?>
                    <a href="<?= route_to('workspace.trainer.classes') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">Lihat Semua</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($recentClasses)): ?>
                <div class="row g-3">
                    <?php foreach ($recentClasses as $cl): ?>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-secondary-subtle text-secondary small"><?= esc($cl['class_code'] ?? $cl['code'] ?? 'PBJ') ?></span>
                                        <?php if (isset($cl['progress_percent'])): ?>
                                            <span class="badge bg-success-subtle text-success small"><?= (int) $cl['progress_percent'] ?>% Selesai</span>
                                        <?php elseif (isset($cl['student_count'])): ?>
                                            <span class="badge bg-info-subtle text-info small"><?= (int) $cl['student_count'] ?> Peserta</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="h6 fw-bold mb-1" style="color: #082f5c;">
                                        <?= esc($cl['course_title'] ?? $cl['class_name'] ?? $cl['name'] ?? 'Kelas Pembelajaran') ?>
                                    </h3>
                                    <p class="small text-muted mb-3"><?= esc($cl['class_name'] ?? $cl['name'] ?? '') ?></p>
                                </div>
                                <div>
                                    <?php if (isset($cl['progress_percent'])): ?>
                                        <div class="progress mb-3" style="height: 6px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= (int) $cl['progress_percent'] ?>%" aria-valuenow="<?= (int) $cl['progress_percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <a href="<?= route_to('workspace.participant.study', $cl['class_id']) ?>" class="btn btn-primary btn-sm w-100 rounded-pill">
                                            Lanjutkan Belajar <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    <?php elseif ($primaryRole === 'trainer'): ?>
                                        <a href="<?= route_to('workspace.trainer.class.detail', $cl['id']) ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                            Kelola Kelas <i class="fas fa-cog ms-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-graduation-cap fs-1 mb-3 text-secondary opacity-50"></i>
                    <p class="mb-2">Belum ada kelas pembelajaran aktif saat ini.</p>
                    <?php if ($primaryRole === 'participant'): ?>
                        <a href="<?= site_url('katalog') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Jelajahi Katalog Pembelajaran</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Tasks / Quick Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Tugas & Agenda</h2>
                    <small class="text-muted">Aktivitas pembelajaran mendesak</small>
                </div>
                <?php if ($primaryRole === 'participant'): ?>
                    <a href="<?= route_to('workspace.participant.all-assignments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">Semua</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($recentTasks)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentTasks as $task): ?>
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="small text-truncate" style="color: #082f5c; max-width: 70%;"><?= esc($task['title']) ?></strong>
                                <span class="badge bg-warning-subtle text-warning-emphasis small">Tugas</span>
                            </div>
                            <small class="text-muted d-block mb-2"><?= esc($task['class_name']) ?></small>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-danger small"><i class="far fa-clock me-1"></i> <?= esc($task['due_at'] ?? 'Tidak ada batas') ?></small>
                                <a href="<?= route_to('workspace.participant.assignments', $task['class_id']) ?>" class="btn btn-sm btn-link text-primary p-0">Kerjakan</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-clipboard-check fs-2 mb-2 text-success opacity-50"></i>
                    <p class="mb-0 small">Semua tugas saat ini telah terselesaikan!</p>
                </div>
            <?php endif; ?>

            <!-- Quick Links -->
            <div class="mt-4 pt-3 border-top">
                <h3 class="small fw-bold text-uppercase text-muted mb-3">Tautan Cepat</h3>
                <div class="d-grid gap-2">
                    <a href="<?= route_to('workspace.notifications') ?>" class="btn btn-light btn-sm text-start rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-bell text-warning me-2"></i> Notifikasi Saya</span>
                        <i class="fas fa-chevron-right text-muted small"></i>
                    </a>
                    <a href="<?= route_to('workspace.bookmarks.index') ?>" class="btn btn-light btn-sm text-start rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-bookmark text-primary me-2"></i> Bookmark Tersimpan</span>
                        <i class="fas fa-chevron-right text-muted small"></i>
                    </a>
                    <a href="<?= route_to('workspace.profile.index') ?>" class="btn btn-light btn-sm text-start rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-user-shield text-success me-2"></i> Data Profil Pengguna</span>
                        <i class="fas fa-chevron-right text-muted small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities / Audit Trail -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Aktivitas Terakhir Saya</h2>
            <small class="text-muted">Catatan jejak aktivitas Workspace terbaru</small>
        </div>
        <a href="<?= route_to('workspace.activity') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">Lihat Riwayat Lengkap</a>
    </div>

    <?php if (!empty($recentActivities)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted">Aksi</th>
                        <th class="small text-uppercase text-muted">Objek / Modul</th>
                        <th class="small text-uppercase text-muted">Hasil</th>
                        <th class="small text-uppercase text-muted">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivities as $act): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= esc($act['action']) ?></td>
                            <td><span class="text-muted"><?= esc($act['resource_type'] ? $act['resource_type'] . ' #' . $act['resource_id'] : '-') ?></span></td>
                            <td>
                                <span class="badge <?= ($act['result'] ?? '') === 'success' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= esc($act['result'] ?? 'selesai') ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= esc(date('d/m/Y H:i', strtotime($act['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-4 text-muted">
            <p class="mb-0 small">Belum ada catatan aktivitas baru yang tercatat.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
