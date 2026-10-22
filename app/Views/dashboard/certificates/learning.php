<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/certificates/_nav_certificate', ['activeSub' => 'learning']) ?>
<?php
$classes  = $classes ?? [];
$programs = $programs ?? [];
$courses  = $courses ?? [];
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Learning Certificate</h6>
        <p class="text-secondary-light mb-0">Kelola sertifikasi kelulusan peserta pembelajaran per program, course, dan kelas.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat/batch?tab=learning') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:layers-minimalistic-outline" class="text-lg"></iconify-icon>
            Batch Generator Kelas
        </a>
    </div>
</div>

<!-- Hierarchy Banner -->
<div class="card p-16 radius-12 border bg-base shadow-none mb-24">
    <div class="d-flex align-items-center gap-2 flex-wrap text-xs fw-medium text-secondary-light">
        <span class="badge bg-primary-50 text-primary-600 px-10 py-5 radius-4">Hirarki Belajar</span>
        <span class="text-primary-light fw-semibold">PROGRAM</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="text-primary-light fw-semibold">COURSE</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="text-primary-light fw-semibold">CLASS</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="text-primary-light fw-semibold">ENROLLMENT</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="text-primary-light fw-semibold">PROGRESS 100%</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="badge bg-success-50 text-success-600 px-10 py-5 radius-4 fw-bold">SERTIFIKAT</span>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 border shadow-none mb-24">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/sertifikat/learning') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Kelas / Course</label>
                    <div class="position-relative">
                        <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nama kelas atau course..." value="<?= esc($keyword ?? '') ?>">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Program</label>
                    <select name="program_id" class="form-select radius-8">
                        <option value="">Semua Program</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?= $prog['id'] ?>" <?= ((int) ($selectedProg ?? 0) === (int) $prog['id']) ? 'selected' : '' ?>>
                                <?= esc($prog['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Course</label>
                    <select name="course_id" class="form-select radius-8">
                        <option value="">Semua Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ((int) ($selectedCourse ?? 0) === (int) $c['id']) ? 'selected' : '' ?>>
                                <?= esc($c['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-10 w-100 d-flex align-items-center justify-content-center gap-1 text-sm">
                        <iconify-icon icon="solar:filter-linear"></iconify-icon>
                        Filter
                    </button>
                    <a href="<?= site_url('dashboard/sertifikat/learning') ?>" class="btn btn-outline-secondary radius-8 px-12 py-10" title="Reset filter">
                        <iconify-icon icon="solar:restart-linear"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table of Classes -->
<div class="card radius-12 border shadow-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">KELAS & COURSE</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">PROGRAM</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">JADWAL PELAKSANAAN</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">TOTAL ENROLLED</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">LULUS (100%)</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">SERTIFIKAT TERBIT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:square-academic-cap-2-outline" class="text-4xl mb-2 d-block mx-auto text-secondary-light"></iconify-icon>
                                Tidak ada kelas pembelajaran yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $cl): ?>
                            <tr>
                                <td>
                                    <span class="d-block fw-semibold text-sm text-primary-light mb-1">
                                        <?= esc($cl['class_name'] ?? '—') ?>
                                    </span>
                                    <span class="badge bg-primary-50 text-primary-600 px-8 py-2 radius-4 text-xs">
                                        <?= esc($cl['course_title'] ?? 'Course') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light">
                                        <?= esc($cl['program_title'] ?? '—') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light d-block">
                                        Mulai: <?= ! empty($cl['start_date']) ? date('d M Y', strtotime($cl['start_date'])) : '—' ?>
                                    </span>
                                    <span class="text-xs text-secondary-light d-block">
                                        Selesai: <?= ! empty($cl['end_date']) ? date('d M Y', strtotime($cl['end_date'])) : '—' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-sm text-secondary-light">
                                        <?= number_format((int) ($cl['total_enrolled'] ?? 0)) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 fw-semibold text-xs">
                                        <?= number_format((int) ($cl['total_completed'] ?? 0)) ?> Peserta
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-4 fw-semibold text-xs">
                                        <?= number_format((int) ($cl['total_certified'] ?? 0)) ?> Terbit
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/sertifikat/learning/kelas/' . $cl['id']) ?>" class="btn btn-outline-primary-600 radius-8 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:users-group-rounded-linear"></iconify-icon>
                                        Kelola Peserta
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
