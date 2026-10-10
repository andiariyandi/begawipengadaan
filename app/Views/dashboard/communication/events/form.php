<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Event') ?></h6>
        <p class="text-secondary-light mb-0">Form event PBJ.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isEdit && !empty($eventId)): ?>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId) ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
                <iconify-icon icon="solar:eye-outline" class="me-1"></iconify-icon> Lihat Detail
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9">Kembali</a>
    </div>
</div>

<?php if ($isEdit && !empty($eventId)): ?>
<?php
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
<!-- Presensi Event Section (Edit Mode) -->
<div class="card mb-24 border-primary-200 shadow-sm">
    <div class="card-header bg-primary-50 d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
        <div class="d-flex align-items-center gap-12">
            <div class="w-40-px h-40-px rounded-circle bg-primary-600 text-white d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:qr-code-outline" class="fs-4"></iconify-icon>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-primary-light">Presensi Event</h6>
                <p class="text-xs text-secondary-light mb-0">Kelola status dan kode sandi kehadiran peserta untuk event ini.</p>
            </div>
        </div>
        <div>
            <?= $presensiBadge ?>
        </div>
    </div>
    <div class="card-body p-24">
        <div class="row gy-3 align-items-center">
            <div class="col-md-7">
                <label class="form-label text-xs text-secondary-light fw-bold text-uppercase mb-2">Kode Presensi:</label>
                <?php if (!empty($code)): ?>
                    <div class="p-16 bg-neutral-50 rounded-12 border border-neutral-200 text-center mb-12">
                        <div class="display-6 fw-bold text-primary-600 font-monospace letter-spacing-2 user-select-all" id="editCodeText"><?= esc($code) ?></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-7" onclick="navigator.clipboard.writeText('<?= esc($code) ?>'); alert('Kode presensi disalin: <?= esc($code) ?>');">
                            <iconify-icon icon="solar:copy-outline" class="me-1"></iconify-icon> Salin Kode
                        </button>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int)$eventId . '/generate-kode-presensi') ?>" method="post" class="d-inline" onsubmit="return confirm('Regenerate kode akan mengganti kode sandi presensi. Lanjutkan?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-neutral-400 btn-sm radius-8 px-14 py-7 text-secondary">
                                <iconify-icon icon="solar:restart-outline" class="me-1"></iconify-icon> Regenerate Kode
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="p-16 bg-neutral-50 rounded-12 border border-neutral-200 text-center mb-12">
                        <p class="text-secondary-light mb-2 text-sm">Kode belum dibuat.</p>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int)$eventId . '/generate-kode-presensi') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8 text-sm">
                                <iconify-icon icon="solar:key-outline" class="me-1"></iconify-icon> Generate Code
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-5 border-start-md ps-md-4">
                <span class="text-xs text-secondary-light d-block mb-1">Status Presensi:</span>
                <div class="mb-12"><?= $presensiBadge ?></div>
                <div class="d-grid gap-2">
                    <?php if ($presensiStatus === 'aktif'): ?>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int)$eventId . '/tutup-presensi') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger-600 w-100 radius-8 py-8 text-sm">
                                <iconify-icon icon="solar:close-circle-outline" class="me-1"></iconify-icon> Tutup Presensi
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int)$eventId . '/buka-presensi') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success-600 w-100 radius-8 py-8 text-sm">
                                <iconify-icon icon="solar:play-circle-outline" class="me-1"></iconify-icon> Buka Presensi
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/komunikasi/event-agenda/' . (int) ($eventId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/event-agenda/tambah') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Event <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $item['title'] ?? '')) ?>" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Event <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="type" required>
                        <option value="webinar" <?= in_array(old('type', $item['type'] ?? 'webinar'), ['webinar', 'online'], true) ? 'selected' : '' ?>>Webinar</option>
                        <option value="workshop" <?= old('type', $item['type'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                        <option value="seminar" <?= old('type', $item['type'] ?? '') === 'seminar' ? 'selected' : '' ?>>Seminar</option>
                        <option value="bimtek" <?= old('type', $item['type'] ?? '') === 'bimtek' ? 'selected' : '' ?>>Bimbingan Teknis (Bimtek)</option>
                        <option value="sosialisasi" <?= old('type', $item['type'] ?? '') === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
                        <option value="pelatihan" <?= old('type', $item['type'] ?? '') === 'pelatihan' ? 'selected' : '' ?>>Pelatihan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mode Pelaksanaan <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="mode" required>
                        <option value="online" <?= in_array(old('mode', $item['mode'] ?? 'online'), ['online', 'open'], true) ? 'selected' : '' ?>>Online (Daring)</option>
                        <option value="offline" <?= old('mode', $item['mode'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                        <option value="hybrid" <?= old('mode', $item['mode'] ?? '') === 'hybrid' ? 'selected' : '' ?>>Hybrid (Online &amp; Offline)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="draft" <?= old('status', $item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="coming_soon" <?= old('status', $item['status'] ?? '') === 'coming_soon' ? 'selected' : '' ?>>Akan Datang</option>
                        <option value="open_registration" <?= old('status', $item['status'] ?? '') === 'open_registration' ? 'selected' : '' ?>>Pendaftaran Dibuka</option>
                        <option value="full" <?= old('status', $item['status'] ?? '') === 'full' ? 'selected' : '' ?>>Penuh</option>
                        <option value="finished" <?= old('status', $item['status'] ?? '') === 'finished' ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= old('status', $item['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                    <select class="form-select radius-8" name="visibility">
                        <option value="public" <?= old('visibility', $item['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Publik</option>
                        <option value="internal" <?= old('visibility', $item['visibility'] ?? '') === 'internal' ? 'selected' : '' ?>>Internal</option>
                        <option value="restricted" <?= old('visibility', $item['visibility'] ?? '') === 'restricted' ? 'selected' : '' ?>>Restricted</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mulai</label>
                    <input type="datetime-local" name="start_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['start_at']) ? date('Y-m-d\TH:i', strtotime($item['start_at'])) : old('start_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Selesai</label>
                    <input type="datetime-local" name="end_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['end_at']) ? date('Y-m-d\TH:i', strtotime($item['end_at'])) : old('end_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lokasi</label>
                    <input type="text" name="location" class="form-control radius-8"
                           value="<?= esc(old('location', $item['location'] ?? '')) ?>" placeholder="Zoom Meeting atau Bandar Lampung">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Meeting URL</label>
                    <input type="url" name="meeting_url" class="form-control radius-8"
                           value="<?= esc(old('meeting_url', $item['meeting_url'] ?? '')) ?>" placeholder="https://...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pendaftaran Dibuka</label>
                    <input type="datetime-local" name="registration_open_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_open_at'])) : old('registration_open_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pendaftaran Ditutup</label>
                    <input type="datetime-local" name="registration_close_at" class="form-control radius-8"
                           value="<?= esc(! empty($item['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_close_at'])) : old('registration_close_at')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas</label>
                    <input type="number" min="0" name="capacity" class="form-control radius-8"
                           value="<?= esc(old('capacity', $item['capacity'] ?? '')) ?>" placeholder="100">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="waitlist_enabled" id="waitlist" value="1" <?= ! empty($item['waitlist_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="waitlist">Waitlist</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="certificate_enabled" id="cert" value="1" <?= ! empty($item['certificate_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cert">Sertifikat</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label>
                    <textarea name="summary" class="form-control radius-8" rows="2"><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label>
                    <textarea name="description" class="form-control radius-8" rows="5"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Cover</label>
                    <input type="file" name="cover" class="form-control radius-8" accept="image/*">
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Event' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
