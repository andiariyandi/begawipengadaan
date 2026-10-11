<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('pageStyles') ?>
<style>
    .ws-hero-card {
        background: linear-gradient(135deg, #082f5c 0%, #052244 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .ws-hero-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.18) 0%, rgba(255, 107, 53, 0) 70%);
        pointer-events: none;
    }
    .ws-stat-card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #eef2f7;
    }
    .ws-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(8, 47, 92, 0.08) !important;
    }
    .ws-class-card {
        border-radius: 16px;
        border: 1px solid #eef2f7;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .ws-class-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(8, 47, 92, 0.08);
        transform: translateY(-2px);
    }
    .ws-event-date-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        line-height: 1.1;
        flex-shrink: 0;
    }
    .ws-event-date-box small {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 600;
        opacity: 0.85;
    }
    .ws-cert-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        transition: all 0.2s ease;
    }
    .ws-cert-card:hover {
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.12);
        transform: translateY(-2px);
    }
    .text-navy {
        color: #082f5c !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="participant-workspace-home">

    <!-- 1. HERO WELCOME & RESUME LEARNING BANNER -->
    <div class="ws-hero-card shadow-sm p-4 p-lg-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-graduation-cap me-1"></i> Ruang Kerja Peserta
                    </span>
                    <span class="badge bg-white-50 text-white px-3 py-2 rounded-pill small">
                        <i class="fas fa-check-circle text-success me-1"></i> Terverifikasi BegawiPBJ
                    </span>
                </div>
                <h1 class="h2 text-white fw-bold mb-2">
                    Halo, <?= esc($userName) ?>! 👋
                </h1>
                <p class="text-white-50 mb-0 fs-6" style="max-width: 620px;">
                    Pusat aktivitas pembelajaran komprehensif Anda. Pantau kemajuan modul, kerjakan tugas & ujian kompetensi, hadiri agenda workshop, dan raih sertifikat resmi PBJ Pemerintah Provinsi Lampung.
                </p>
            </div>

            <!-- Quick Resume Learning Box -->
            <div class="col-lg-5">
                <?php if (!empty($resumeClass)): ?>
                    <div class="p-3 p-md-4 rounded-4" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-warning small fw-bold text-uppercase">
                                <i class="fas fa-play-circle me-1"></i> Lanjutkan Belajar
                            </span>
                            <span class="badge bg-success text-white small px-2 py-1 rounded-pill">
                                <?= (int) ($resumeClass['progress_percent'] ?? 0) ?>% Tuntas
                            </span>
                        </div>
                        <h3 class="h6 text-white fw-bold text-truncate mb-1">
                            <?= esc($resumeClass['course_title'] ?? $resumeClass['class_name'] ?? 'Kelas Pembelajaran') ?>
                        </h3>
                        <p class="small text-white-50 text-truncate mb-3"><?= esc($resumeClass['class_name'] ?? '') ?></p>
                        
                        <div class="progress rounded-pill mb-3" style="height: 6px; background: rgba(255, 255, 255, 0.2);">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= (int) ($resumeClass['progress_percent'] ?? 0) ?>%"></div>
                        </div>

                        <a href="<?= route_to('workspace.participant.study', $resumeClass['class_id']) ?>" class="btn btn-warning text-dark fw-bold btn-sm w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-arrow-right me-1"></i> Buka Ruang Belajar Kelas
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-4 rounded-4 text-center" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-compass fa-2x text-warning mb-2"></i>
                        <h3 class="h6 text-white fw-bold mb-1">Mulai Perjalanan Belajar Anda</h3>
                        <p class="small text-white-50 mb-3">Pilih kelas dan kembangkan kompetensi pengadaan barang dan jasa sekarang.</p>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-4 py-2">
                            <i class="fas fa-search me-1"></i> Jelajahi Kelas Pembelajaran
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. 4 SUMMARY KPI STAT CARDS -->
    <?php if (!empty($kpiCards)): ?>
        <div class="row g-3 mb-4">
            <?php foreach ($kpiCards as $stat): ?>
                <div class="col-6 col-lg-3">
                    <div class="card ws-stat-card border-0 shadow-sm p-3 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #eff6ff; color: #082f5c;">
                                <i class="<?= esc($stat['icon'] ?? 'fas fa-chart-bar') ?>"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-medium d-block" style="font-size: 0.78rem;"><?= esc($stat['title']) ?></span>
                                <div class="h4 fw-bold text-navy mb-0"><?= esc($stat['value']) ?></div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($stat['meta']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 3. PEMBELAJARAN SEDANG BERJALAN -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-book-reader text-primary me-2"></i>Pembelajaran Sedang Berjalan
                </h2>
                <small class="text-muted">Kelas yang Anda ikuti dan kelanjutan proses belajarnya</small>
            </div>
            <a href="<?= route_to('workspace.participant.classes') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                Lihat Semua Kelas (<?= count($recentClasses) ?>)
            </a>
        </div>

        <?php if (!empty($recentClasses)): ?>
            <div class="row g-3 ws-paginated-container" data-page-size="4">
                <?php foreach ($recentClasses as $cl): ?>
                    <div class="col-md-6 col-lg-3 ws-paginated-item">
                        <div class="ws-class-card p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary-subtle text-primary fw-bold small rounded-pill px-2 py-1">
                                        <?= esc($cl['class_code'] ?? 'PBJ') ?>
                                    </span>
                                    <span class="badge <?= ($cl['enrollment_status'] ?? '') === 'completed' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' ?> small rounded-pill">
                                        <?= (int) ($cl['progress_percent'] ?? 0) ?>% Selesai
                                    </span>
                                </div>
                                <h3 class="h6 fw-bold text-navy mb-1" style="min-height: 2.4rem; line-height: 1.3;">
                                    <?= esc($cl['course_title'] ?? $cl['class_name'] ?? 'Kelas Pembelajaran') ?>
                                </h3>
                                <p class="small text-muted mb-2 text-truncate"><?= esc($cl['class_name'] ?? '') ?></p>

                                <div class="d-flex flex-wrap gap-2 text-muted small mb-3" style="font-size: 0.72rem;">
                                    <span><i class="fas fa-layer-group me-1 text-primary"></i> <?= (int) ($cl['module_count'] ?? 0) ?> Modul</span>
                                    <span>&middot;</span>
                                    <span><i class="fas fa-check-circle me-1 text-success"></i> KKM <?= (int) ($cl['passing_grade'] ?? 70) ?>%</span>
                                </div>
                            </div>

                            <div>
                                <div class="progress rounded-pill mb-3" style="height: 6px; background: #eef2f7;">
                                    <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= (int) ($cl['progress_percent'] ?? 0) ?>%"></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= route_to('workspace.participant.study', $cl['class_id']) ?>" class="btn btn-primary btn-sm flex-grow-1 rounded-pill fw-semibold">
                                        Belajar <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                    <a href="<?= route_to('workspace.participant.class.detail', $cl['class_id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Detail Silabus">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-graduation-cap fs-1 mb-3 text-secondary opacity-50"></i>
                <p class="mb-2 fw-medium">Belum ada kelas pembelajaran aktif saat ini.</p>
                <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-search me-1"></i> Jelajahi Kelas Pembelajaran
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. 2 KOLOM: TUGAS & ASSESSMENT vs EVENT & AGENDA -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Tugas & Evaluasi Pembelajaran -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-tasks text-warning me-2"></i>Tugas & Assessment
                        </h2>
                        <small class="text-muted">Aktivitas evaluasi dan ujian pembelajaran</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="<?= route_to('workspace.participant.all-assignments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Tugas
                        </a>
                        <a href="<?= route_to('workspace.participant.all-assessments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Assessment
                        </a>
                    </div>
                </div>

                <!-- List Tugas -->
                <?php if (!empty($recentTasks)): ?>
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem;">
                            <i class="fas fa-clipboard-list text-primary me-1"></i> Tugas Pembelajaran
                        </span>
                        <div class="list-group list-group-flush ws-paginated-container" data-page-size="4">
                            <?php foreach ($recentTasks as $task): ?>
                                <div class="list-group-item px-0 py-2 border-bottom ws-paginated-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-truncate text-navy" style="max-width: 70%;"><?= esc($task['title']) ?></strong>
                                        <?php if (($task['submission_status'] ?? '') === 'graded'): ?>
                                            <span class="badge bg-success-subtle text-success small rounded-pill">Nilai: <?= (int) ($task['submission_score'] ?? 0) ?></span>
                                        <?php elseif (($task['submission_status'] ?? '') === 'submitted' || ($task['submission_status'] ?? '') === 'in_review'): ?>
                                            <span class="badge bg-info-subtle text-info small rounded-pill">Dalam Review</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis small rounded-pill">Belum Dikirim</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><?= esc($task['class_name']) ?></small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-danger small" style="font-size: 0.75rem;">
                                             <i class="far fa-clock me-1"></i> Batas: <?= esc(!empty($task['due_at']) ? date('d/m/Y H:i', strtotime($task['due_at'])) : 'Tidak ada batas') ?>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.assignments', $task['class_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            <?= ($task['submission_status'] ?? '') === 'graded' ? 'Lihat Nilai' : 'Kerjakan' ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- List Kuis / Assessment -->
                <?php if (!empty($recentQuizzes)): ?>
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem;">
                            <i class="fas fa-check-double text-success me-1"></i> Ujian & Kuis Modul
                        </span>
                        <div class="list-group list-group-flush ws-paginated-container" data-page-size="4">
                            <?php foreach ($recentQuizzes as $qz): ?>
                                <div class="list-group-item px-0 py-2 border-bottom ws-paginated-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-truncate text-navy" style="max-width: 70%;"><?= esc($qz['title']) ?></strong>
                                        <span class="badge bg-secondary-subtle text-secondary small rounded-pill"><?= esc(strtoupper($qz['type'] ?? 'QUIZ')) ?></span>
                                    </div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><?= esc($qz['class_name']) ?></small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="fas fa-check-circle me-1 text-success"></i> Standar Kelulusan: <?= (int) ($qz['passing_grade'] ?? 70) ?>%
                                            <?php if (isset($qz['highest_score']) && $qz['highest_score'] !== null): ?>
                                                &middot; <strong class="text-success">Skor: <?= (int) $qz['highest_score'] ?></strong>
                                            <?php endif; ?>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.quiz', $qz['class_id']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            Ikuti Ujian
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($recentTasks) && empty($recentQuizzes)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-check fs-2 mb-2 text-success opacity-50"></i>
                        <p class="mb-0 small fw-medium">Semua tugas dan kuis saat ini telah selesai dikerjakan!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Event & Agenda Saya -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-navy mb-0">
                            <i class="fas fa-calendar-check text-info me-2"></i>Event & Agenda Terdaftar
                        </h2>
                        <small class="text-muted">Kegiatan pelatihan dan seminar yang diikuti</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="<?= route_to('workspace.participant.events') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 small">
                            Semua Event
                        </a>
                        <a href="<?= route_to('workspace.participant.attendance') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 small">
                            Presensi
                        </a>
                    </div>
                </div>

                <?php if (!empty($myEvents)): ?>
                    <div class="list-group list-group-flush ws-paginated-container" data-page-size="3">
                        <?php foreach ($myEvents as $ev): ?>
                            <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-start gap-3 ws-paginated-item">
                                <div class="ws-event-date-box">
                                    <span class="fs-5"><?= esc(date('d', strtotime($ev['start_at'] ?? 'now'))) ?></span>
                                    <small><?= esc(date('M', strtotime($ev['start_at'] ?? 'now'))) ?></small>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="small text-navy text-truncate" style="max-width: 75%;">
                                            <?= esc($ev['event_title'] ?? 'Event BegawiPBJ') ?>
                                        </strong>
                                        <span class="badge <?= ($ev['attendance_status'] ?? '') === 'present' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> small rounded-pill">
                                            <?= ($ev['attendance_status'] ?? '') === 'present' ? 'Hadir' : 'Terdaftar' ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 text-muted small mb-2" style="font-size: 0.75rem;">
                                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?= esc($ev['delivery_mode'] ?? $ev['location'] ?? 'Online') ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1 text-primary"></i> <?= esc(date('H:i', strtotime($ev['start_at'] ?? 'now'))) ?> WIB</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted small" style="font-size: 0.75rem;">
                                            Presensi: <strong><?= ($ev['attendance_status'] ?? '') === 'present' ? 'Tercatat Hadir' : 'Belum Presensi' ?></strong>
                                        </small>
                                        <a href="<?= route_to('workspace.participant.attendance') ?>" class="btn btn-sm btn-link text-primary p-0 fw-semibold" style="font-size: 0.75rem;">
                                            Catat Kehadiran <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fs-2 mb-2 text-secondary opacity-50"></i>
                        <p class="mb-2 small fw-medium">Belum ada agenda event yang Anda daftarkan.</p>
                        <a href="<?= site_url('event') ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            <i class="fas fa-compass me-1"></i> Eksplorasi Event PBJ
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 5. SERTIFIKAT KELULUSAN & CAPAIAN KOMPETENSI RESMI -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-award text-success me-2"></i>Sertifikat Kelulusan & Kompetensi Saya
                </h2>
                <small class="text-muted">Bukti capaian kompetensi resmi yang diterbitkan oleh BegawiPBJ</small>
            </div>
            <a href="<?= route_to('workspace.participant.certificates') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                Lihat Semua Sertifikat
            </a>
        </div>

        <?php if (!empty($myCertificates)): ?>
            <div class="row g-3 ws-paginated-container" data-page-size="3">
                <?php foreach ($myCertificates as $cert): ?>
                    <div class="col-md-6 col-lg-4 ws-paginated-item">
                        <div class="ws-cert-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success-subtle text-success small rounded-pill px-3 py-1 fw-bold">
                                        <i class="fas fa-shield-alt me-1"></i> Resmi Terverifikasi
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= esc(date('d M Y', strtotime($cert['issued_at'] ?? 'now'))) ?>
                                    </small>
                                </div>
                                <h3 class="h6 fw-bold text-navy mb-1" style="min-height: 2.2rem;">
                                    <?= esc($cert['title'] ?? 'Sertifikat Kompetensi PBJ') ?>
                                </h3>
                                <p class="small text-muted mb-2 text-truncate">
                                    <?= esc($cert['class_name'] ?? $cert['event_title'] ?? 'Program Pembelajaran PBJ') ?>
                                </p>
                                <p class="small text-muted font-monospace mb-3 p-2 bg-light rounded-3" style="font-size: 0.75rem;">
                                    <i class="fas fa-barcode me-1 text-secondary"></i> <?= esc($cert['certificate_number'] ?? '-') ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= route_to('workspace.participant.certificate.detail', $cert['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-semibold">
                                    <i class="fas fa-eye me-1"></i> Rincian
                                </a>
                                <a href="<?= route_to('workspace.participant.certificate.download', $cert['id']) ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-semibold">
                                    <i class="fas fa-download me-1"></i> Unduh PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-award fs-2 mb-2 text-warning opacity-50"></i>
                <p class="mb-0 small">Sertifikat akan otomatis diterbitkan setelah Anda menyelesaikan seluruh modul dan lulus evaluasi kelas atau menghadiri event resmi.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 6. CATATAN AKTIVITAS TERAKHIR SAYA -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-history text-secondary me-2"></i>Aktivitas Terakhir Saya
                </h2>
                <small class="text-muted">Catatan jejak aktivitas pembelajaran dan interaksi Workspace Anda</small>
            </div>
            <a href="<?= route_to('workspace.activity') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                Riwayat Lengkap
            </a>
        </div>

        <?php if (!empty($recentActivities)): ?>
            <div class="table-responsive ws-paginated-container" data-page-size="5">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted">Aksi</th>
                            <th class="small text-uppercase text-muted">Modul / Objek</th>
                            <th class="small text-uppercase text-muted">Status</th>
                            <th class="small text-uppercase text-muted">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivities as $act): ?>
                            <tr class="ws-paginated-item">
                                <td class="fw-semibold text-navy">
                                    <?php
                                    $actionLabel = match ($act['action'] ?? '') {
                                        'login'               => 'Masuk ke Sistem',
                                        'enroll'              => 'Mendaftar Kelas Pembelajaran',
                                        'complete_lesson'     => 'Menyelesaikan Materi Pembelajaran',
                                        'submit_assignment'   => 'Mengumpulkan Tugas',
                                        'submit_quiz'         => 'Menyelesaikan Ujian / Kuis',
                                        'register_event'      => 'Mendaftar Event / Workshop',
                                        'submit_attendance'   => 'Mencatat Presensi Kehadiran',
                                        'issue_certificate'   => 'Penerbitan Sertifikat',
                                        default               => ucfirst(str_replace('_', ' ', (string) ($act['action'] ?? 'Aktivitas')))
                                    };
                                    ?>
                                    <?= esc($actionLabel) ?>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?= esc($act['resource_type'] ? ucfirst($act['resource_type']) . ' #' . $act['resource_id'] : '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= ($act['result'] ?? '') === 'success' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill">
                                        <?= esc($act['result'] ?? 'selesai') ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1 text-muted"></i>
                                    <?= esc(date('d/m/Y H:i', strtotime($act['created_at']))) ?>
                                </td>
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

</div>
<?= $this->endSection() ?>
