<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$eventId = (int) ($eventId ?? ($item['id'] ?? 0));
$code = (string) ($item['attendance_code'] ?? '');
$openAt = ! empty($item['attendance_open_at']) ? strtotime($item['attendance_open_at']) : null;
$closeAt = ! empty($item['attendance_close_at']) ? strtotime($item['attendance_close_at']) : null;
$now = time();

if (empty($openAt) || empty($code)) {
    $presensiStatus = 'belum_dibuka';
    $presensiBadge = '<span class="badge bg-neutral-200 text-neutral-800 px-12 py-6 radius-4">Belum Dibuka</span>';
} elseif ($closeAt !== null && $closeAt <= $now) {
    $presensiStatus = 'ditutup';
    $presensiBadge = '<span class="badge bg-danger-subtle text-danger px-12 py-6 radius-4 fw-semibold">Ditutup</span>';
} else {
    $presensiStatus = 'aktif';
    $presensiBadge = '<span class="badge bg-success-subtle text-success px-12 py-6 radius-4 fw-semibold"><i class="fas fa-check-circle me-1"></i> Aktif</span>';
}
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($item['title'] ?? 'Detail Event') ?></h6>
        <p class="text-secondary-light mb-0">ID Event: #<?= esc($eventId) ?> &bull; Mode: <?= esc(ucfirst($item['mode'] ?? 'online')) ?> &bull; Tipe: <?= esc(ucfirst($item['type'] ?? 'webinar')) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9">
            <iconify-icon icon="solar:arrow-left-outline" class="me-1"></iconify-icon> Kembali
        </a>
        <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-16 py-9">
            <iconify-icon icon="solar:pen-new-square-outline" class="me-1"></iconify-icon> Ubah Event
        </a>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Event Overview & Presensi Section -->
    <div class="col-xl-8">
        <!-- Event Overview Card -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16 pb-16 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary-light"><?= esc($item['title'] ?? '-') ?></h5>
                    <div class="d-flex gap-2">
                        <?= view('dashboard/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?>
                        <span class="badge bg-primary-subtle text-primary radius-4 px-10 py-4"><?= esc(ucfirst($item['visibility'] ?? 'public')) ?></span>
                    </div>
                </div>

                <div class="row gy-3 mb-20 text-sm text-secondary-light">
                    <div class="col-sm-6">
                        <span class="d-flex align-items-center gap-2 mb-2">
                            <iconify-icon icon="solar:calendar-date-outline" class="text-primary-600 fs-5"></iconify-icon>
                            <strong>Mulai:</strong> <?= !empty($item['start_at']) ? esc(date('d M Y, H:i', strtotime($item['start_at']))) : '-' ?> WIB
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:calendar-outline" class="text-secondary fs-5"></iconify-icon>
                            <strong>Selesai:</strong> <?= !empty($item['end_at']) ? esc(date('d M Y, H:i', strtotime($item['end_at']))) : '-' ?> WIB
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <span class="d-flex align-items-center gap-2 mb-2">
                            <iconify-icon icon="solar:map-point-outline" class="text-danger-600 fs-5"></iconify-icon>
                            <strong>Lokasi:</strong> <?= esc($item['location'] ?: 'Daring / Online') ?>
                        </span>
                        <?php if (!empty($item['meeting_url'])): ?>
                            <span class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:videocamera-record-outline" class="text-success-600 fs-5"></iconify-icon>
                                <strong>Meeting URL:</strong> <a href="<?= esc($item['meeting_url']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 220px;"><?= esc($item['meeting_url']) ?></a>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($item['summary'])): ?>
                    <div class="p-16 bg-neutral-50 rounded-8 mb-16">
                        <h6 class="text-sm fw-semibold mb-1">Ringkasan:</h6>
                        <p class="text-secondary-light text-sm mb-0"><?= nl2br(esc($item['summary'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($item['description'])): ?>
                    <div>
                        <h6 class="text-sm fw-semibold mb-2">Deskripsi Lengkap:</h6>
                        <div class="text-secondary-light text-sm"><?= nl2br(esc($item['description'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section: PRESENSI EVENT -->
        <div class="card mb-24 border-primary-200 shadow-sm">
            <div class="card-header bg-primary-50 d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-600 text-white d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:qr-code-outline" class="fs-4"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-primary-light">Presensi Event</h6>
                        <p class="text-xs text-secondary-light mb-0">Mekanisme check-in presensi peserta berbasis kode sandi unik.</p>
                    </div>
                </div>
                <div>
                    <?= $presensiBadge ?>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="row gy-4 align-items-center">
                    <!-- Kode Presensi Display -->
                    <div class="col-md-7">
                        <label class="form-label text-xs text-secondary-light fw-bold text-uppercase mb-2">Kode Presensi Event</label>
                        <?php if (!empty($code)): ?>
                            <div class="p-20 bg-neutral-50 rounded-12 border border-neutral-200 text-center mb-12">
                                <div class="text-xs text-secondary-light mb-1">KODE SANDI KEHADIRAN:</div>
                                <div class="display-6 fw-bold text-primary-600 font-monospace letter-spacing-2 user-select-all" id="attendanceCodeText"><?= esc($code) ?></div>
                                <small class="text-muted d-block mt-1">Bagikan kode ini ke peserta saat sesi event berlangsung.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-7" onclick="copyCode()">
                                    <iconify-icon icon="solar:copy-outline" class="me-1"></iconify-icon> Salin Kode
                                </button>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/generate-kode-presensi') ?>" method="post" class="d-inline" onsubmit="return confirm('Regenerate akan membuat kode sandi baru. Kode sebelumnya tidak dapat digunakan lagi. Lanjutkan?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-neutral-400 btn-sm radius-8 px-14 py-7 text-secondary">
                                        <iconify-icon icon="solar:restart-outline" class="me-1"></iconify-icon> Regenerate Kode
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="p-20 bg-neutral-50 rounded-12 border border-neutral-200 text-center mb-12">
                                <iconify-icon icon="solar:lock-keyhole-outline" class="fs-1 text-neutral-400 mb-2"></iconify-icon>
                                <p class="text-secondary-light mb-3 text-sm">Kode presensi belum di-generate untuk event ini.</p>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/generate-kode-presensi') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-9 text-sm">
                                        <iconify-icon icon="solar:key-outline" class="me-1"></iconify-icon> Generate Code
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Kontrol Presensi -->
                    <div class="col-md-5 border-start-md ps-md-4">
                        <h6 class="text-sm fw-semibold mb-12 text-primary-light">Kontrol Sesi Presensi</h6>
                        <ul class="list-unstyled text-xs text-secondary-light mb-16">
                            <li class="mb-2 d-flex justify-content-between">
                                <span>Mulai Dibuka:</span>
                                <strong><?= !empty($item['attendance_open_at']) ? esc(date('d/m/Y H:i', strtotime($item['attendance_open_at']))) : '-' ?></strong>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span>Waktu Ditutup:</span>
                                <strong><?= !empty($item['attendance_close_at']) ? esc(date('d/m/Y H:i', strtotime($item['attendance_close_at']))) : '-' ?></strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Peserta Hadir:</span>
                                <strong class="text-success"><?= (int) $presentCount ?> / <?= (int) $registeredCount ?> Peserta</strong>
                            </li>
                        </ul>

                        <div class="d-grid gap-2">
                            <?php if ($presensiStatus === 'aktif'): ?>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/tutup-presensi') ?>" method="post" onsubmit="return confirm('Tutup presensi sekarang? Peserta tidak akan bisa check-in lagi setelah ditutup.')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger-600 w-100 radius-8 py-9">
                                        <iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon> Tutup Presensi
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/buka-presensi') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success-600 w-100 radius-8 py-9">
                                        <iconify-icon icon="solar:play-circle-outline" class="me-1"></iconify-icon> Buka Presensi
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: BATCH CERTIFICATE EVENT -->
        <div class="card mb-24">
            <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-success-subtle text-success d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="fs-4"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-primary-light">Penerbitan Sertifikat Event (Batch)</h6>
                        <p class="text-xs text-secondary-light mb-0">Hanya peserta yang telah presensi sah (status: present) yang akan diterbitkan sertifikat.</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="row align-items-center gy-3">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <span class="text-secondary-light text-xs d-block">PESERTA HADIR:</span>
                                <h4 class="mb-0 fw-bold text-success"><?= (int) $presentCount ?> <span class="text-xs text-muted fw-normal">Orang</span></h4>
                            </div>
                            <div class="border-start ps-3">
                                <span class="text-secondary-light text-xs d-block">TELAH DITERBITKAN:</span>
                                <h4 class="mb-0 fw-bold text-primary"><?= (int) $certifiedCount ?> <span class="text-xs text-muted fw-normal">Sertifikat</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId . '/batch-certificates') ?>" method="post" onsubmit="return confirm('Terbitkan sertifikat batch untuk seluruh peserta yang hadir pada event ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success radius-8 px-20 py-10" <?= $presentCount === 0 ? 'disabled' : '' ?>>
                                <iconify-icon icon="solar:diploma-outline" class="me-1"></iconify-icon> Terbitkan Sertifikat Batch
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participant List Table -->
        <div class="card">
            <div class="card-header bg-white p-20 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary-light">Daftar Peserta & Status Presensi</h6>
                <span class="badge bg-light text-dark border"><?= count($participants ?? []) ?> Peserta Terdaftar</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase text-xs text-secondary-light ps-24">Peserta</th>
                                <th class="text-uppercase text-xs text-secondary-light">Status Reg</th>
                                <th class="text-uppercase text-xs text-secondary-light">Kehadiran</th>
                                <th class="text-uppercase text-xs text-secondary-light">Waktu Hadir</th>
                                <th class="text-uppercase text-xs text-secondary-light pe-24">Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($participants)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-24 text-secondary-light">Belum ada peserta yang mendaftar pada event ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($participants as $p): ?>
                                    <tr>
                                        <td class="ps-24">
                                            <strong class="d-block text-primary-light"><?= esc($p['user_name'] ?? 'Peserta') ?></strong>
                                            <span class="text-xs text-secondary-light"><?= esc($p['user_email'] ?? '-') ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info radius-4 px-8 py-3 text-xs">
                                                <?= esc(ucfirst($p['registration_status'] ?? 'registered')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($p['attendance_status'] ?? '') === 'present'): ?>
                                                <span class="badge bg-success-subtle text-success radius-4 px-8 py-3 text-xs fw-semibold">
                                                    <i class="fas fa-check me-1"></i> Hadir
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-200 text-neutral-600 radius-4 px-8 py-3 text-xs">
                                                    Belum Hadir
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary-light text-xs">
                                            <?= !empty($p['attendance_recorded_at']) ? esc(date('d/m/Y H:i', strtotime($p['attendance_recorded_at']))) : '-' ?>
                                        </td>
                                        <td class="pe-24">
                                            <?php if (!empty($p['certificate_number'])): ?>
                                                <span class="badge bg-primary-subtle text-primary radius-4 px-8 py-3 text-xs font-monospace">
                                                    <?= esc($p['certificate_number']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-muted">-</span>
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
    </div>

    <!-- Right Column: Registration Summary & Capacity -->
    <div class="col-xl-4">
        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20 text-primary-light fw-bold">Ringkasan Kapasitas</h6>
                <?php
                $capacity = (int) ($item['capacity'] ?? 0);
                $percent = $capacity > 0 ? min(100, round(($registeredCount / $capacity) * 100)) : 0;
                ?>
                <h3 class="mb-8 fw-bold text-primary-light">
                    <?= (int) $registeredCount ?> 
                    <span class="text-secondary-light text-lg fw-normal">/ <?= $capacity > 0 ? $capacity : '&infin;' ?> Peserta</span>
                </h3>
                <div class="progress progress-sm rounded-pill mb-12">
                    <div class="progress-bar bg-primary-600 rounded-pill" style="width: <?= $percent ?>%;"></div>
                </div>
                <p class="text-secondary-light text-sm mb-20"><?= $percent ?>% kapasitas pendaftaran terisi.</p>

                <div class="d-flex justify-content-between py-8 border-top text-sm">
                    <span class="text-secondary-light">Status Event:</span>
                    <strong><?= esc(ucfirst($item['status'] ?? 'draft')) ?></strong>
                </div>
                <div class="d-flex justify-content-between py-8 border-top text-sm">
                    <span class="text-secondary-light">Sertifikat Aktif:</span>
                    <strong><?= !empty($item['certificate_enabled']) ? 'Ya' : 'Tidak' ?></strong>
                </div>
                <div class="d-flex justify-content-between py-8 border-top text-sm">
                    <span class="text-secondary-light">Presensi Sah:</span>
                    <strong class="text-success"><?= (int) $presentCount ?> Hadir</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode() {
    const codeEl = document.getElementById('attendanceCodeText');
    if (codeEl) {
        navigator.clipboard.writeText(codeEl.innerText.trim()).then(function() {
            alert('Kode presensi ' + codeEl.innerText.trim() + ' berhasil disalin ke clipboard!');
        });
    }
}
</script>
<?= $this->endSection() ?>
