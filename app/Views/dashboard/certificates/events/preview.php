<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$event            = $event ?? [];
$participants     = $participants ?? [];
$eligibleCount    = $eligibleCount ?? 0;
$attendanceFilter = $attendanceFilter ?? 'all';
?>

<!-- Header & Back Navigation -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= site_url('dashboard/sertifikat/event') ?>" class="w-40-px h-40-px radius-8 border bg-base d-flex align-items-center justify-content-center text-secondary-light hover-bg-neutral-100">
            <iconify-icon icon="solar:arrow-left-outline" class="text-xl"></iconify-icon>
        </a>
        <div>
            <h6 class="fw-semibold mb-1"><?= esc($event['title'] ?? 'Detail Event') ?></h6>
            <p class="text-secondary-light mb-0">
                Mode: <?= esc(strtoupper($event['mode'] ?? 'OFFLINE')) ?> &bull;
                <?= ! empty($event['start_at']) ? date('d M Y, H:i', strtotime($event['start_at'])) : '—' ?>
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($eligibleCount > 0): ?>
            <form method="post" action="<?= site_url('dashboard/sertifikat/event/' . $event['id'] . '/batch') ?>" onsubmit="return confirm('Terbitkan sertifikat untuk <?= $eligibleCount ?> peserta yang hadir?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                    Terbitkan Batch Peserta Hadir (<?= $eligibleCount ?>)
                </button>
            </form>
        <?php else: ?>
            <button type="button" class="btn btn-secondary radius-8 px-16 py-10 d-flex align-items-center gap-2 text-sm disabled" disabled>
                <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                Tidak Ada Peserta Hadir Menunggu
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Event Info Card -->
<div class="card radius-12 border bg-base shadow-none mb-24">
    <div class="card-body p-20">
        <div class="row g-3">
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">JUDUL AGENDA</span>
                <span class="fw-semibold text-sm text-primary-light"><?= esc($event['title'] ?? '—') ?></span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">MODE & LOKASI</span>
                <span class="text-sm text-primary-light fw-medium">
                    <?= esc(ucfirst($event['mode'] ?? 'Offline')) ?> &bull; <?= esc($event['location'] ?? 'Lokasi belum diset') ?>
                </span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">JADWAL EVENT</span>
                <span class="text-sm text-secondary-light">
                    <?= ! empty($event['start_at']) ? date('d M Y, H:i', strtotime($event['start_at'])) : '—' ?> s/d
                    <?= ! empty($event['end_at']) ? date('d M Y, H:i', strtotime($event['end_at'])) : '—' ?>
                </span>
            </div>
            <div class="col-md-3">
                <span class="text-xs text-secondary-light d-block mb-1">STATUS SERTIFIKAT</span>
                <span class="badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs">
                    Certificate Enabled (<?= $eligibleCount ?> Peserta Hadir Layak)
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar: Kehadiran -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-16">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/sertifikat/event/' . $event['id']) ?>" class="btn radius-8 px-14 py-7 text-xs <?= $attendanceFilter === 'all' ? 'btn-primary-600' : 'btn-outline-neutral-300' ?>">
            Semua Peserta (<?= count($participants) ?>)
        </a>
        <a href="<?= site_url('dashboard/sertifikat/event/' . $event['id'] . '?attendance=present') ?>" class="btn radius-8 px-14 py-7 text-xs <?= $attendanceFilter === 'present' ? 'btn-success-600 text-white' : 'btn-outline-success-600' ?>">
            <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
            Hadir Saja (Present)
        </a>
        <a href="<?= site_url('dashboard/sertifikat/event/' . $event['id'] . '?attendance=absent') ?>" class="btn radius-8 px-14 py-7 text-xs <?= $attendanceFilter === 'absent' ? 'btn-danger-600 text-white' : 'btn-outline-danger-600' ?>">
            <iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon>
            Tidak Hadir
        </a>
    </div>
    <span class="text-xs text-secondary-light">
        Hanya peserta berstatus <strong class="text-success-main">Hadir (Present)</strong> yang berhak mendapatkan sertifikat.
    </span>
</div>

<!-- Table of Participants -->
<div class="card radius-12 border shadow-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">PESERTA</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">STATUS REGISTRASI</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">PRESENSI KEHADIRAN</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">STATUS KELAYAKAN</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold">SERTIFIKAT</th>
                        <th scope="col" class="text-secondary-light text-xs fw-semibold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:user-block-outline" class="text-4xl mb-2 d-block mx-auto text-secondary-light"></iconify-icon>
                                Tidak ada peserta terdaftar sesuai kriteria filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-36-px h-36-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center fw-semibold text-xs flex-shrink-0">
                                            <?= strtoupper(substr((string) ($p['user_name'] ?? 'U'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="d-block fw-semibold text-sm text-primary-light"><?= esc($p['user_name'] ?? '—') ?></span>
                                            <span class="d-block text-xs text-secondary-light"><?= esc($p['user_email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-secondary-light px-8 py-3 radius-4 text-xs fw-medium">
                                        <?= esc(ucfirst($p['registration_status'] ?? 'registered')) ?>
                                    </span>
                                    <span class="d-block text-xs text-secondary-light mt-1">
                                        Daftar: <?= ! empty($p['registered_at']) ? date('d M Y', strtotime($p['registered_at'])) : '—' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['attendance_status'] === 'present'): ?>
                                        <span class="badge bg-success-50 text-success-600 px-10 py-5 radius-4 fw-semibold text-xs">
                                            <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                                            Hadir (Present)
                                        </span>
                                        <span class="d-block text-xs text-secondary-light mt-1">
                                            <?= ! empty($p['attended_at']) ? date('H:i, d M Y', strtotime($p['attended_at'])) : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-50 text-danger-600 px-10 py-5 radius-4 fw-semibold text-xs">
                                            <iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon>
                                            Tidak Hadir
                                        </span>
                                    <?php endif; ?>
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
                                            Layak (Hadir)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-secondary-light px-10 py-5 radius-4 fw-semibold text-xs">
                                            Tidak Layak (Absen)
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
                                        <span class="text-xs text-secondary-light">Belum Diterbitkan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['eligibility_status'] === 'eligible'): ?>
                                        <form method="post" action="<?= site_url('dashboard/sertifikat/event/issue/' . $p['registration_id']) ?>" onsubmit="return confirm('Terbitkan sertifikat event untuk <?= esc($p['user_name']) ?>?');">
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
                                        <button type="button" class="btn btn-outline-secondary radius-8 px-12 py-6 text-xs disabled" disabled title="Hanya peserta hadir yang berhak">
                                            Tidak Hadir
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
