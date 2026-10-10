<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$events  = $events ?? [];
$keyword = $keyword ?? '';
$statusFilter = $statusFilter ?? 'all';
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Event / Agenda Certificate</h6>
        <p class="text-secondary-light mb-0">Kelola sertifikasi kegiatan, seminar, workshop, dan agenda PBJ berdasarkan presensi kehadiran peserta.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat/batch?tab=event') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:layers-minimalistic-outline" class="text-lg"></iconify-icon>
            Batch Generator Event
        </a>
    </div>
</div>

<!-- Hierarchy Banner -->
<div class="card p-16 radius-12 border bg-base shadow-none mb-24">
    <div class="d-flex align-items-center gap-2 flex-wrap text-xs fw-medium text-secondary-light">
        <span class="badge bg-info-50 text-info-600 px-10 py-5 radius-4">Hirarki Event</span>
        <span class="text-primary-light fw-semibold">AGENDA / EVENT</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="text-primary-light fw-semibold">REGISTRASI PESERTA</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="badge bg-warning-50 text-warning-600 px-10 py-5 radius-4">PRESENSI VALID (HADIR)</span>
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        <span class="badge bg-success-50 text-success-600 px-10 py-5 radius-4 fw-bold">SERTIFIKAT RESMI</span>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 border shadow-none mb-24">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/sertifikat/event') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Judul Event</label>
                    <div class="position-relative">
                        <input type="text" name="keyword" class="form-control radius-8 ps-40" placeholder="Cari nama atau judul event..." value="<?= esc($keyword) ?>">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Status Event</label>
                    <select name="status" class="form-select radius-8">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-10 w-100 d-flex align-items-center justify-content-center gap-1 text-sm">
                        <iconify-icon icon="solar:filter-linear"></iconify-icon>
                        Filter
                    </button>
                    <a href="<?= site_url('dashboard/sertifikat/event') ?>" class="btn btn-outline-secondary radius-8 px-12 py-10" title="Reset filter">
                        <iconify-icon icon="solar:restart-linear"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table of Events -->
<div class="card radius-12 border shadow-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">JUDUL EVENT & MODE</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">WAKTU PELAKSANAAN</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">TOTAL TERDAFTAR</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">PRESENSI HADIR</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">SERTIFIKAT TERBIT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:calendar-minimalistic-outline" class="text-4xl mb-2 d-block mx-auto text-secondary-light"></iconify-icon>
                                Tidak ada event yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $ev): ?>
                            <tr>
                                <td>
                                    <span class="d-block fw-semibold text-sm text-primary-light mb-1">
                                        <?= esc($ev['title'] ?? '—') ?>
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-neutral-100 text-secondary-light px-8 py-2 radius-4 text-xs">
                                            <?= esc(strtoupper($ev['mode'] ?? 'OFFLINE')) ?>
                                        </span>
                                        <?php if (! empty($ev['location'])): ?>
                                            <span class="text-xs text-secondary-light text-truncate" style="max-width: 200px;">
                                                <iconify-icon icon="solar:map-point-linear" class="me-1"></iconify-icon><?= esc($ev['location']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light d-block">
                                        Mulai: <?= ! empty($ev['start_at']) ? date('d M Y, H:i', strtotime($ev['start_at'])) : '—' ?>
                                    </span>
                                    <span class="text-xs text-secondary-light d-block">
                                        Selesai: <?= ! empty($ev['end_at']) ? date('d M Y, H:i', strtotime($ev['end_at'])) : '—' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-sm text-secondary-light">
                                        <?= number_format((int) ($ev['total_registered'] ?? 0)) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 fw-semibold text-xs">
                                        <?= number_format((int) ($ev['total_present'] ?? 0)) ?> Hadir
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-50 text-info-600 px-10 py-4 radius-4 fw-semibold text-xs">
                                        <?= number_format((int) ($ev['total_certified'] ?? 0)) ?> Terbit
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/sertifikat/event/' . $ev['id']) ?>" class="btn btn-outline-info-600 radius-8 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:user-check-rounded-linear"></iconify-icon>
                                        Presensi & Sertifikat
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
