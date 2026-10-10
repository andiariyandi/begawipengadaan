<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$stats = $stats ?? [];
$recentClasses = $recentClasses ?? [];
$recentParticipants = $recentParticipants ?? [];
$popularCourses = $popularCourses ?? [];
$recentActivities = $recentActivities ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Overview Arena Belajar') ?></h6>
        <p class="text-secondary-light mb-0">Pusat kendali dan monitoring eksekutif pembelajaran, kurikulum, serta penerbitan sertifikat.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-3">
        <a href="<?= site_url('dashboard/pembelajaran/kelas/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Buka Kelas Baru</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>" class="btn btn-outline-success radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:diploma-verified-outline" class="text-xl"></iconify-icon>
            <span>Kelayakan Sertifikat</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Program</p>
                        <h6 class="mb-0"><?= number_format($stats['total_programs'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:folder-with-files-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Tingkat kurikulum induk</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Course</p>
                        <h6 class="mb-0 text-primary-600"><?= number_format($stats['total_courses'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-bookmark-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Materi kursus terstruktur</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Aktif</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['active_classes'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Batch berjalan saat ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Trainer Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['active_trainers'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-speak-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Pengajar bertugas di kelas</p>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Peserta Aktif</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['active_participants'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Peserta dalam pembelajaran</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Completion Rate</p>
                        <h6 class="mb-0 text-primary-600"><?= esc($stats['completion_rate'] ?? 0) ?>%</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:chart-square-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <div class="progress mt-12" style="height: 6px;">
                    <div class="progress-bar bg-primary-600" role="progressbar" style="width: <?= min(100, max(0, (float) ($stats['completion_rate'] ?? 0))) ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Eligible Sertifikat</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['eligible_certificates'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Peserta memenuhi syarat</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['issued_certificates'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Dokumen terverifikasi QR</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row gy-4 mb-24">
    <!-- Left Column: Kelas Terbaru & Course Populer -->
    <div class="col-xxl-8 col-lg-7">
        <!-- Kelas Terbaru -->
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Kelas Pembelajaran Terbaru</h6>
                <a href="<?= site_url('dashboard/pembelajaran/kelas') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium d-flex align-items-center gap-1">
                    Lihat Semua <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Nama Kelas / Batch</th>
                                <th>Course</th>
                                <th>Kapasitas</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentClasses)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary-light py-24">Belum ada kelas pembelajaran.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentClasses as $cls): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $cls['id']) ?>" class="fw-semibold text-primary-600">
                                                <?= esc($cls['name']) ?>
                                            </a>
                                            <div class="text-xs text-secondary-light mt-1">Kode: <?= esc($cls['code']) ?></div>
                                        </td>
                                        <td><?= esc($cls['course_title'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge bg-neutral-100 text-neutral-800 border">
                                                <?= number_format($cls['enrolled_count'] ?? 0) ?> / <?= ! empty($cls['capacity']) ? number_format($cls['capacity']) : '∞' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= view('dashboard/components/status_badge', ['status' => $cls['status']]) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $cls['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-4 px-12 py-6">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Course Populer -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Course Terpopuler</h6>
                <a href="<?= site_url('dashboard/pembelajaran/course') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium d-flex align-items-center gap-1">
                    Kelola Course <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Judul Course</th>
                                <th>Program</th>
                                <th>Jumlah Kelas</th>
                                <th>Total Peserta</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($popularCourses)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary-light py-24">Belum ada course aktif.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($popularCourses as $c): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/pembelajaran/course/' . $c['id']) ?>" class="fw-semibold text-primary-600">
                                                <?= esc($c['title']) ?>
                                            </a>
                                            <div class="text-xs text-secondary-light">Level: <?= esc(ucfirst($c['level'] ?? 'Dasar')) ?></div>
                                        </td>
                                        <td><?= esc($c['program_title'] ?? '—') ?></td>
                                        <td><span class="badge bg-info-50 text-info-600"><?= number_format($c['class_count'] ?? 0) ?> Kelas</span></td>
                                        <td><span class="badge bg-success-50 text-success-600"><?= number_format($c['student_count'] ?? 0) ?> Peserta</span></td>
                                        <td><?= view('dashboard/components/status_badge', ['status' => $c['status']]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Peserta Baru & Aktivitas Belajar -->
    <div class="col-xxl-4 col-lg-5">
        <!-- Pendaftar / Peserta Baru -->
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Peserta Terbaru</h6>
                <a href="<?= site_url('dashboard/pembelajaran/enrollment') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium d-flex align-items-center gap-1">
                    Semua <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="card-body p-20">
                <?php if (empty($recentParticipants)): ?>
                    <p class="text-center text-secondary-light py-12 mb-0">Belum ada peserta terdaftar.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-16">
                        <?php foreach ($recentParticipants as $rp): ?>
                            <div class="d-flex align-items-center justify-content-between p-12 radius-8 bg-neutral-50 border">
                                <div class="d-flex align-items-center gap-12">
                                    <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex justify-content-center align-items-center fw-semibold">
                                        <?= esc(strtoupper(substr($rp['user_name'] ?? 'U', 0, 1))) ?>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1"><?= esc($rp['user_name']) ?></h6>
                                        <p class="text-xs text-secondary-light mb-0"><?= esc($rp['class_name']) ?></p>
                                    </div>
                                </div>
                                <div>
                                    <?= view('dashboard/components/status_badge', ['status' => $rp['status']]) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Aktivitas Belajar Terakhir -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Aktivitas Belajar</h6>
                <a href="<?= site_url('dashboard/pembelajaran/progres') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium d-flex align-items-center gap-1">
                    Monitoring <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="card-body p-20">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-center text-secondary-light py-12 mb-0">Belum ada progres pembelajaran.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-16">
                        <?php foreach ($recentActivities as $act): ?>
                            <div class="d-flex align-items-start gap-12 pb-12 border-bottom last-border-0">
                                <div class="w-32-px h-32-px rounded-circle bg-success-50 text-success-600 d-flex justify-content-center align-items-center flex-shrink-0 mt-4">
                                    <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-xs text-secondary-light mb-1">
                                        <?= ! empty($act['updated_at']) ? date('d M Y, H:i', strtotime($act['updated_at'])) : '—' ?>
                                    </p>
                                    <h6 class="text-sm fw-semibold mb-1"><?= esc($act['user_name']) ?></h6>
                                    <p class="text-xs text-secondary mb-0">
                                        Menyelesaikan materi <span class="fw-medium text-neutral-900"><?= esc($act['lesson_title']) ?></span> pada <?= esc($act['class_name']) ?>
                                    </p>
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
