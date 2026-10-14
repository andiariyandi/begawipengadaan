<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$item = $item ?? [];
$regId = (int) ($regId ?? ($item['id'] ?? 0));
$eventId = (int) ($item['event_id'] ?? 0);
$regStatus = $item['status'] ?? 'registered';
$attStatus = $item['attendance_status'] ?? null;
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;">Detail Registrasi Peserta</h6>
        <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">
            ID Registrasi: <strong>#<?= esc($regId) ?></strong> &bull; Peserta: <strong><?= esc($item['user_full_name'] ?? $item['username'] ?? '-') ?></strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 fw-semibold text-sm text-neutral-700 d-flex align-items-center gap-2" style="background:#ffffff; border-color:#cbd5e1;">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
        <?php if (! empty($eventId)): ?>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId) ?>" class="btn btn-primary-600 radius-8 px-18 py-10 fw-bold text-sm d-flex align-items-center gap-2 shadow-sm">
                <iconify-icon icon="solar:calendar-bold" class="text-lg"></iconify-icon>
                <span>Lihat Agenda Event</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Participant & Event Info -->
    <div class="col-xl-8">
        <!-- Participant Info Card -->
        <div class="card radius-12 border bg-white mb-24 shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-xl">
                        <iconify-icon icon="solar:user-bold"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Profil Peserta</h6>
                        <small class="text-neutral-600 font-medium" style="color: #475569;">Data identitas dan kontak akun yang terdaftar.</small>
                    </div>
                </div>
                <div>
                    <?php if ($regStatus === 'registered'): ?>
                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-10 py-5 radius-6 text-xs fw-bold">Terdaftar</span>
                    <?php elseif ($regStatus === 'attended'): ?>
                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-5 radius-6 text-xs fw-bold">Hadir</span>
                    <?php elseif ($regStatus === 'waiting'): ?>
                        <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-10 py-5 radius-6 text-xs fw-bold">Waitlist</span>
                    <?php elseif ($regStatus === 'promoted'): ?>
                        <span class="badge bg-teal-50 text-teal-700 border border-teal-200 px-10 py-5 radius-6 text-xs fw-bold">Promoted</span>
                    <?php elseif ($regStatus === 'cancelled'): ?>
                        <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-10 py-5 radius-6 text-xs fw-bold">Dibatalkan</span>
                    <?php else: ?>
                        <span class="badge bg-neutral-100 text-neutral-700 border border-neutral-300 px-10 py-5 radius-6 text-xs fw-medium"><?= esc(ucfirst($regStatus)) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="row gy-3">
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">NAMA LENGKAP:</span>
                        <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;"><?= esc($item['user_full_name'] ?? '-') ?></h6>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">USERNAME AKUN:</span>
                        <p class="mb-0 fw-semibold text-neutral-900 font-monospace" style="color: #0f172a;">@<?= esc($item['username'] ?? '-') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">ALAMAT EMAIL:</span>
                        <p class="mb-0 fw-semibold text-primary-600"><?= esc($item['email'] ?? '-') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">NOMOR TELEPON / WA:</span>
                        <p class="mb-0 fw-semibold text-neutral-900 font-mono"><?= esc($item['phone'] ?? '-') ?: '-' ?></p>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">WAKTU PENDAFTARAN:</span>
                        <p class="mb-0 fw-semibold text-neutral-900"><?= ! empty($item['registered_at']) ? esc(date('d M Y, H:i', strtotime($item['registered_at']))) : '-' ?> WIB</p>
                    </div>
                    <?php if (! empty($item['cancelled_at'])): ?>
                        <div class="col-sm-6">
                            <span class="text-xs fw-bold text-danger-700 text-uppercase d-block mb-1">WAKTU PEMBATALAN:</span>
                            <p class="mb-0 fw-semibold text-danger-700"><?= esc(date('d M Y, H:i', strtotime($item['cancelled_at']))) ?> WIB</p>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($item['status_reason'])): ?>
                        <div class="col-12">
                            <div class="p-12 radius-8 border bg-light mt-2" style="border-color: #e2e8f0;">
                                <span class="text-xs fw-bold text-neutral-700 d-block mb-1" style="color: #475569;">Catatan / Alasan Status:</span>
                                <p class="text-neutral-800 text-xs mb-0 font-medium" style="color: #334155;"><?= nl2br(esc($item['status_reason'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Event Info Card -->
        <div class="card radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center text-xl">
                        <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Agenda Kegiatan Terkait</h6>
                        <small class="text-neutral-600 font-medium" style="color: #475569;">Informasi sesi, lokasi, dan jadwal kegiatan yang didaftarkan.</small>
                    </div>
                </div>
                <?php if (! empty($eventId)): ?>
                    <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . $eventId) ?>" class="btn btn-outline-primary-600 btn-sm radius-8 px-12 py-6 text-xs fw-bold">
                        Buka Agenda
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-24">
                <h5 class="fw-bold text-neutral-900 mb-12" style="color: #0f172a;"><?= esc($item['event_title'] ?? '-') ?></h5>
                <div class="row gy-3 text-sm">
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">JADWAL PELAKSANAAN:</span>
                        <div class="fw-semibold text-neutral-900" style="color: #0f172a;">
                            <?= ! empty($item['event_start_at']) ? esc(date('d M Y, H:i', strtotime($item['event_start_at']))) : '-' ?> s/d
                            <?= ! empty($item['event_end_at']) ? esc(date('d M Y, H:i', strtotime($item['event_end_at']))) : '-' ?> WIB
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">MODE &amp; TIPE:</span>
                        <div class="fw-semibold text-neutral-900" style="color: #0f172a;">
                            <?= esc(ucfirst($item['event_mode'] ?? 'online')) ?> &bull; <?= esc(ucfirst($item['event_type'] ?? 'webinar')) ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">LOKASI / TAUTAN:</span>
                        <div class="fw-semibold text-neutral-900" style="color: #0f172a;">
                            <iconify-icon icon="solar:map-point-bold" class="text-danger-600 me-1"></iconify-icon>
                            <?= esc($item['event_location'] ?: ($item['meeting_url'] ?: 'Online / Daring')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Actions & Presence Logging -->
    <div class="col-xl-4">
        <!-- Status Action Card -->
        <div class="card radius-12 border bg-white mb-24 shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom">
                <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Kelola Status Registrasi</h6>
            </div>
            <div class="card-body p-24">
                <form action="<?= route_to('dashboard.communication.event-registrations.updateStatus', (string) $regId) ?>" method="post">
                    <?= csrf_field() ?>
                    <label class="form-label text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">Status Pendaftaran:</label>
                    <select class="form-select radius-8 fw-semibold text-neutral-900 mb-16" name="status" style="border-color: #cbd5e1;">
                        <option value="registered" <?= $regStatus === 'registered' ? 'selected' : '' ?>>Terdaftar (Confirmed)</option>
                        <option value="attended" <?= $regStatus === 'attended' ? 'selected' : '' ?>>Hadir (Attended)</option>
                        <option value="waiting" <?= $regStatus === 'waiting' ? 'selected' : '' ?>>Waitlist (Antrian)</option>
                        <option value="promoted" <?= $regStatus === 'promoted' ? 'selected' : '' ?>>Promoted (Dari Waitlist)</option>
                        <option value="no_show" <?= $regStatus === 'no_show' ? 'selected' : '' ?>>Tidak Hadir (No-Show)</option>
                        <option value="cancelled" <?= $regStatus === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>

                    <button type="submit" class="btn btn-primary-600 w-100 radius-8 py-10 fw-bold text-sm shadow-sm">
                        Perbarui Status Pendaftaran
                    </button>
                </form>
            </div>
        </div>

        <!-- Attendance Record Card -->
        <div class="card radius-12 border bg-white mb-24 shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom">
                <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Catatan Kehadiran (Presensi)</h6>
            </div>
            <div class="card-body p-24">
                <div class="p-14 radius-8 border mb-16" style="background: #f8fafc; border-color: #e2e8f0;">
                    <span class="text-xs fw-bold text-neutral-600 text-uppercase d-block mb-1" style="color: #64748b;">STATUS KEHADIRAN SAAT INI:</span>
                    <?php if ($attStatus === 'present'): ?>
                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-5 radius-6 text-xs fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Hadir Sah
                        </span>
                    <?php elseif ($attStatus === 'excused'): ?>
                        <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-5 radius-6 text-xs fw-bold">
                            <i class="fas fa-info-circle me-1"></i> Izin Sah
                        </span>
                    <?php elseif ($attStatus === 'absent'): ?>
                        <span class="badge bg-danger-50 text-danger-700 border border-danger-200 px-10 py-5 radius-6 text-xs fw-bold">
                            <i class="fas fa-times-circle me-1"></i> Tidak Hadir (Alfa)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-neutral-100 text-neutral-600 border border-neutral-300 px-10 py-5 radius-6 text-xs fw-medium">
                            <i class="far fa-clock me-1"></i> Belum Presensi
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($item['attendance_recorded_at'])): ?>
                        <small class="text-neutral-600 d-block mt-2 text-xs" style="color: #475569;">
                            Dicatat pada: <strong><?= esc(date('d/m/Y H:i', strtotime($item['attendance_recorded_at']))) ?></strong>
                            <?php if (! empty($item['recorder_name'])): ?>
                                oleh <strong><?= esc($item['recorder_name']) ?></strong>
                            <?php endif; ?>
                        </small>
                    <?php endif; ?>
                </div>

                <form action="<?= site_url('dashboard/komunikasi/kehadiran/' . $regId . '/catatan') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                    <label class="form-label text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">Catat Presensi Manual:</label>
                    <select class="form-select radius-8 fw-semibold text-neutral-900 mb-12" name="status" style="border-color: #cbd5e1;">
                        <option value="present" <?= $attStatus === 'present' ? 'selected' : '' ?>>Hadir (Present)</option>
                        <option value="excused" <?= $attStatus === 'excused' ? 'selected' : '' ?>>Izin (Excused)</option>
                        <option value="absent" <?= $attStatus === 'absent' ? 'selected' : '' ?>>Tidak Hadir (Absent)</option>
                    </select>

                    <label class="form-label text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">Catatan / Keterangan:</label>
                    <textarea name="notes" class="form-control radius-8 text-neutral-900 font-medium mb-16" rows="2" placeholder="Contoh: Hadir tepat waktu di ruang Zoom..." style="border-color: #cbd5e1;"><?= esc($item['attendance_notes'] ?? '') ?></textarea>

                    <button type="submit" class="btn btn-outline-success w-100 radius-8 py-9 fw-bold text-sm">
                        Simpan Catatan Presensi
                    </button>
                </form>
            </div>
        </div>

        <!-- Certificate Status Card -->
        <div class="card radius-12 border bg-white shadow-none" style="border-color: #e2e8f0;">
            <div class="card-header bg-white p-20 border-bottom">
                <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">E-Sertifikat Event</h6>
            </div>
            <div class="card-body p-20">
                <?php if (! empty($item['certificate_number'])): ?>
                    <div class="p-14 radius-8 border mb-12 text-center" style="background: #f0fdf4; border-color: #bbf7d0;">
                        <span class="text-xs fw-bold text-success-800 d-block mb-1">SERTIFIKAT TELAH DITERBITKAN:</span>
                        <div class="fw-bold text-success-700 font-monospace text-base mb-1"><?= esc($item['certificate_number']) ?></div>
                        <small class="text-neutral-600 text-xs d-block">Tanggal Terbit: <?= ! empty($item['certificate_issued_at']) ? esc(date('d/m/Y', strtotime($item['certificate_issued_at']))) : '-' ?></small>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 text-neutral-500 font-medium text-xs">
                        <iconify-icon icon="solar:diploma-outline" class="fs-1 text-neutral-400 mb-2"></iconify-icon>
                        <p class="mb-0">Sertifikat belum diterbitkan untuk peserta ini.</p>
                        <small class="text-neutral-400 d-block mt-1">Diterbitkan otomatis saat batch penerbitan sertifikat event.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
