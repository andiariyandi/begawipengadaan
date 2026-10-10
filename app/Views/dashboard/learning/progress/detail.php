<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$enrollment  = $enrollment ?? [];
$modules     = $modules ?? [];
$certificate = $certificate ?? null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Progres Peserta') ?></h6>
        <p class="text-secondary-light mb-0">Rincian aktivitas modul, penyelesaian materi, dan evaluasi hasil belajar peserta.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (! empty($certificate)): ?>
            <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>" class="btn btn-outline-success radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
                <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                <span>Lihat Sertifikat (<?= esc($certificate['certificate_number']) ?>)</span>
            </a>
        <?php elseif (! empty($enrollment['certificate_eligible_at']) || in_array($enrollment['status'], ['completed', 'passed'], true)): ?>
            <form action="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat/generate/' . $enrollment['id']) ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:diploma-outline" class="text-lg"></iconify-icon>
                    <span>Terbitkan Sertifikat</span>
                </button>
            </form>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/pembelajaran/progres') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9">Kembali</a>
    </div>
</div>

<!-- Participant Info Banner -->
<div class="card mb-24">
    <div class="card-body p-24">
        <div class="row gy-20">
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Nama Peserta</p>
                <h6 class="mb-0 text-neutral-900"><?= esc($enrollment['student_name']) ?></h6>
                <span class="text-xs text-secondary-light">@<?= esc($enrollment['username']) ?> &bull; <?= esc($enrollment['student_email']) ?></span>
            </div>
            <div class="col-md-6">
                <p class="text-secondary-light mb-4">Kelas & Course</p>
                <h6 class="mb-0 text-primary-600"><?= esc($enrollment['class_name']) ?></h6>
                <span class="text-xs text-secondary-light"><?= esc($enrollment['course_title'] ?? '—') ?> (Kode: <?= esc($enrollment['class_code']) ?>)</span>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Tanggal Pendaftaran</p>
                <span class="fw-semibold text-sm"><?= ! empty($enrollment['enrolled_at']) ? date('d M Y, H:i', strtotime($enrollment['enrolled_at'])) : '—' ?></span>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Status Enrollment</p>
                <?= view('dashboard/components/status_badge', ['status' => $enrollment['status']]) ?>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Nilai Akhir</p>
                <h6 class="mb-0 text-success-main font-monospace"><?= $enrollment['final_score'] !== null ? number_format((float) $enrollment['final_score'], 1) : '—' ?></h6>
            </div>
            <div class="col-md-3">
                <p class="text-secondary-light mb-4">Passing Grade Kelas</p>
                <span class="fw-semibold text-sm"><?= number_format((float) ($enrollment['passing_grade'] ?? 70), 1) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Modules Timeline -->
<h6 class="fw-semibold mb-16 text-lg">Silabus & Progres Pembelajaran Modular</h6>
<div class="d-flex flex-column gap-20">
    <?php if (empty($modules)): ?>
        <div class="card p-24 text-center text-secondary-light">
            Belum ada data modul untuk kelas ini.
        </div>
    <?php else: ?>
        <?php foreach ($modules as $mIndex => $mod): ?>
            <div class="card shadow-none border radius-12">
                <div class="card-header bg-base py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-12">
                        <span class="w-28-px h-28-px rounded-circle bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center fw-bold text-xs">
                            <?= esc($mod['sort_order']) ?>
                        </span>
                        <h6 class="text-md fw-semibold mb-0"><?= esc($mod['title']) ?></h6>
                    </div>
                    <div>
                        <?= view('dashboard/components/status_badge', ['status' => $mod['status']]) ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Materi (Lesson)</th>
                                    <th>Tipe Konten</th>
                                    <th>Waktu Selesai</th>
                                    <th class="text-center">Status Materi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mod['lessons'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary-light py-16">Tidak ada materi pada modul ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mod['lessons'] as $les): ?>
                                        <?php $isDone = ($les['progress_status'] ?? '') === 'completed'; ?>
                                        <tr>
                                            <td class="text-center fw-semibold text-secondary-light"><?= esc($les['sort_order']) ?></td>
                                            <td>
                                                <h6 class="text-sm fw-medium mb-0"><?= esc($les['title']) ?></h6>
                                                <?= ! empty($les['is_required']) ? '<span class="text-xs text-warning-main">Wajib</span>' : '' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-neutral-100 text-neutral-800 radius-4 px-8 py-2 text-xs">
                                                    <?= esc(ucfirst($les['content_type'] ?? 'text')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= ! empty($les['completed_at']) ? date('d M Y, H:i', strtotime($les['completed_at'])) : '<span class="text-secondary-light text-xs">—</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($isDone): ?>
                                                    <span class="badge bg-success-50 text-success-600 radius-4 px-8 py-4 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                        Selesai (100%)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-neutral-100 text-secondary-light radius-4 px-8 py-4 text-xs">
                                                        Belum Selesai
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
