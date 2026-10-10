<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$class         = $class ?? [];
$trainers      = $trainers ?? [];
$participants  = $participants ?? [];
$eligibleCount = $eligibleCount ?? 0;
?>

<!-- Header & Navigation -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= site_url('dashboard/sertifikat/learning') ?>" class="w-40-px h-40-px radius-8 border bg-base d-flex align-items-center justify-content-center text-secondary-light hover-bg-neutral-100">
            <iconify-icon icon="solar:arrow-left-outline" class="text-xl"></iconify-icon>
        </a>
        <div>
            <h6 class="fw-semibold mb-1"><?= esc($class['name'] ?? 'Detail Kelas') ?></h6>
            <p class="text-secondary-light mb-0">
                <?= esc($class['program_title'] ?? '') ?> &bull; <?= esc($class['course_title'] ?? '') ?>
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($eligibleCount > 0): ?>
            <form method="post" action="<?= site_url('dashboard/sertifikat/learning/kelas/' . $class['id'] . '/batch') ?>" onsubmit="return confirm('Terbitkan sertifikat untuk <?= $eligibleCount ?> peserta yang layak?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                    Terbitkan Batch untuk Peserta Layak (<?= $eligibleCount ?>)
                </button>
            </form>
        <?php else: ?>
            <button type="button" class="btn btn-secondary radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm disabled" disabled>
                <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                Tidak Ada Peserta Layak Menunggu
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Class Info Card -->
<div class="card radius-12 border bg-base shadow-none mb-24">
    <div class="card-body p-20">
        <div class="row g-3">
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">PROGRAM PEMBELAJARAN</span>
                <span class="fw-semibold text-sm text-primary-light"><?= esc($class['program_title'] ?? '—') ?></span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">COURSE</span>
                <span class="fw-semibold text-sm text-primary-light"><?= esc($class['course_title'] ?? '—') ?> (<?= esc($class['course_code'] ?? '—') ?>)</span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">JADWAL KELAS</span>
                <span class="text-sm text-secondary-light">
                    <?= ! empty($class['start_date']) ? date('d M Y', strtotime($class['start_date'])) : '—' ?> s/d
                    <?= ! empty($class['end_date']) ? date('d M Y', strtotime($class['end_date'])) : '—' ?>
                </span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">TRAINER PENGAJAR</span>
                <span class="text-sm text-secondary-light">
                    <?php if (! empty($trainers)): ?>
                        <?= esc(implode(', ', array_column($trainers, 'name'))) ?>
                    <?php else: ?>
                        Belum ada trainer
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Participants Table -->
<div class="card radius-12 border shadow-none">
    <div class="card-header py-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="card-title fw-semibold mb-0">Daftar Peserta & Status Kelayakan Sertifikat</h6>
            <span class="text-secondary-light text-xs">Kelayakan diberikan jika peserta berstatus completed atau progress pembelajaran 100%</span>
        </div>
        <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-4 text-xs fw-medium">
            Total: <?= count($participants) ?> Peserta
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">PESERTA</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">PROGRESS BELAJAR</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">NILAI AKHIR</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">STATUS KELAYAKAN</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">INFORMASI SERTIFIKAT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:users-group-rounded-linear" class="text-4xl mb-2 d-block mx-auto text-secondary-light"></iconify-icon>
                                Belum ada peserta terdaftar di kelas ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-36-px h-36-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center fw-semibold text-xs flex-shrink-0">
                                            <?= strtoupper(substr((string) ($p['user_name'] ?? 'U'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="d-block fw-semibold text-sm text-primary-light"><?= esc($p['user_name'] ?? '—') ?></span>
                                            <span class="d-block text-xs text-secondary-light"><?= esc($p['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1" style="max-width: 180px;">
                                        <div class="progress w-100 h-6-px bg-neutral-200 radius-4">
                                            <div class="progress-bar <?= ((float) ($p['progress_percent'] ?? 0) >= 100.0 || $p['enrollment_status'] === 'completed') ? 'bg-success-main' : 'bg-primary-600' ?>" style="width: <?= min(100, max(0, (float) ($p['progress_percent'] ?? 0))) ?>%"></div>
                                        </div>
                                        <span class="text-xs fw-semibold text-secondary-light"><?= number_format((float) ($p['progress_percent'] ?? 0), 0) ?>%</span>
                                    </div>
                                    <span class="text-xs text-secondary-light">
                                        Status: <span class="fw-medium <?= $p['enrollment_status'] === 'completed' ? 'text-success-main' : 'text-primary-light' ?>"><?= esc(ucfirst($p['enrollment_status'] ?? 'in_progress')) ?></span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-sm text-primary-light">
                                        <?= ! empty($p['final_score']) ? number_format((float) $p['final_score'], 1) : '—' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['eligibility_status'] === 'certified'): ?>
                                        <span class="badge bg-primary-50 text-primary-600 px-10 py-5 radius-4 fw-semibold text-xs">
                                            <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                                            Sudah Terbit
                                        </span>
                                    <?php elseif ($p['eligibility_status'] === 'eligible'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-5 radius-4 fw-semibold text-xs">
                                            <iconify-icon icon="solar:verified-check-bold" class="me-1"></iconify-icon>
                                            Layak Diterbitkan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-50 text-warning-600 px-10 py-5 radius-4 fw-semibold text-xs">
                                            Belum Layak (< 100%)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($p['certificate_number'])): ?>
                                        <a href="<?= site_url('dashboard/sertifikat/' . $p['certificate_id']) ?>" class="font-monospace text-xs text-primary-600 hover-underline fw-semibold d-block">
                                            <?= esc($p['certificate_number']) ?>
                                        </a>
                                        <span class="text-xs text-secondary-light">
                                            Terbit: <?= ! empty($p['certificate_issued_at']) ? date('d M Y', strtotime($p['certificate_issued_at'])) : '—' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-secondary-light">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['eligibility_status'] === 'eligible'): ?>
                                        <form method="post" action="<?= site_url('dashboard/sertifikat/learning/issue/' . $p['enrollment_id']) ?>" onsubmit="return confirm('Terbitkan sertifikat untuk <?= esc($p['user_name']) ?>?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-primary-600 radius-8 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                                                <iconify-icon icon="solar:diploma-outline"></iconify-icon>
                                                Terbitkan
                                            </button>
                                        </form>
                                    <?php elseif ($p['eligibility_status'] === 'certified'): ?>
                                        <a href="<?= site_url('dashboard/sertifikat/' . $p['certificate_id']) ?>" class="btn btn-outline-primary-600 radius-8 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            Lihat
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-secondary radius-8 px-12 py-6 text-xs disabled" disabled>
                                            Belum Siap
                                        </button>
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

<?= $this->endSection() ?>
