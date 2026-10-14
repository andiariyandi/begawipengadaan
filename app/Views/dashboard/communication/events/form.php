<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
.form-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.form-section-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.form-section-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.form-section-body {
    padding: 24px;
}
.cover-preview-box {
    width: 100%;
    max-width: 480px;
    aspect-ratio: 16 / 9;
    border-radius: 10px;
    border: 2px dashed #cbd5e1;
    background: #f8fafc;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cover-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item = $item ?? [];
$coverUrl = ! empty($item['cover_file_id']) ? base_url('file/' . (int) $item['cover_file_id']) : null;
?>

<!-- Header Toolbar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-bold text-neutral-900 mb-1" style="color: #0f172a;"><?= esc($pageTitle ?? 'Event & Agenda') ?></h6>
        <p class="text-neutral-600 mb-0 font-medium" style="color: #475569;">
            <?= $isEdit ? 'Perbarui informasi agenda, jadwal pelaksanaan, kuota peserta, dan materi kegiatan.' : 'Lengkapi formulir untuk mempublikasikan agenda workshop, webinar, bimtek, atau seminar pengadaan baru.' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isEdit && ! empty($eventId)): ?>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId) ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-10 fw-semibold text-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:eye-bold" class="text-lg"></iconify-icon>
                <span>Lihat Detail &amp; Peserta</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 fw-semibold text-sm text-neutral-700 d-flex align-items-center gap-2" style="background:#ffffff; border-color:#cbd5e1;">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<?php if ($isEdit && ! empty($eventId)): ?>
<?php
$code = (string) ($item['attendance_code'] ?? '');
$openAt = ! empty($item['attendance_open_at']) ? strtotime($item['attendance_open_at']) : null;
$closeAt = ! empty($item['attendance_close_at']) ? strtotime($item['attendance_close_at']) : null;
$now = time();

if (empty($openAt) || empty($code)) {
    $presensiStatus = 'belum_dibuka';
    $presensiBadge = '<span class="badge bg-neutral-200 text-neutral-800 px-12 py-6 radius-6 fw-bold border border-neutral-300"><i class="fas fa-clock me-1"></i> Belum Dibuka</span>';
} elseif ($closeAt !== null && $closeAt <= $now) {
    $presensiStatus = 'ditutup';
    $presensiBadge = '<span class="badge bg-danger-50 text-danger-700 px-12 py-6 radius-6 fw-bold border border-danger-200"><i class="fas fa-times-circle me-1"></i> Sesi Ditutup</span>';
} else {
    $presensiStatus = 'aktif';
    $presensiBadge = '<span class="badge bg-success-50 text-success-700 px-12 py-6 radius-6 fw-bold border border-success-200"><i class="fas fa-check-circle me-1"></i> Sesi Aktif</span>';
}
?>

<!-- Presensi Quick Management Panel (Edit Mode Only) -->
<div class="card mb-24 border radius-12 shadow-sm" style="border-color: #93c5fd !important; background: #f0f9ff;">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom" style="background: #e0f2fe; border-color: #bae6fd !important;">
        <div class="d-flex align-items-center gap-12">
            <div class="w-40-px h-40-px rounded-circle bg-primary-600 text-white d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:qr-code-bold" class="fs-4"></iconify-icon>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0c4a6e;">Kontrol Presensi &amp; Kode Sandi Kehadiran</h6>
                <p class="text-xs text-neutral-700 mb-0 font-medium" style="color: #0369a1;">Kelola status buka/tutup sesi check-in dan kode unik kehadiran peserta untuk event ini.</p>
            </div>
        </div>
        <div>
            <?= $presensiBadge ?>
        </div>
    </div>
    <div class="card-body p-24 bg-white">
        <div class="row gy-3 align-items-center">
            <div class="col-md-7">
                <label class="form-label text-xs fw-bold text-neutral-700 text-uppercase mb-2" style="color: #475569;">Kode Presensi Peserta:</label>
                <?php if (! empty($code)): ?>
                    <div class="p-16 rounded-12 border text-center mb-12" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                        <span class="text-xs text-neutral-600 d-block mb-1 font-semibold" style="color: #64748b;">KODE SANDI CHECK-IN</span>
                        <div class="display-6 fw-bold text-primary-600 font-monospace letter-spacing-2 user-select-all" id="editCodeText"><?= esc($code) ?></div>
                        <small class="text-neutral-600 d-block mt-1" style="color:#64748b;">Berikan kode ini kepada peserta saat sesi agenda berlangsung.</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold" onclick="navigator.clipboard.writeText('<?= esc($code) ?>'); alert('Kode presensi disalin: <?= esc($code) ?>');">
                            <iconify-icon icon="solar:copy-bold" class="me-1"></iconify-icon> Salin Kode
                        </button>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId . '/generate-kode-presensi') ?>" method="post" class="d-inline" onsubmit="return confirm('Regenerate kode akan mengganti kode sandi presensi. Lanjutkan?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-neutral-300 btn-sm radius-8 px-14 py-8 fw-semibold text-neutral-700" style="border-color:#cbd5e1;">
                                <iconify-icon icon="solar:restart-bold" class="me-1"></iconify-icon> Regenerate Kode
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="p-20 rounded-12 border text-center mb-12" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                        <p class="text-neutral-600 mb-3 text-sm fw-medium" style="color: #475569;">Kode presensi belum di-generate untuk agenda ini.</p>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId . '/generate-kode-presensi') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-18 py-9 text-sm fw-bold">
                                <iconify-icon icon="solar:key-bold" class="me-1"></iconify-icon> Generate Kode Presensi
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-5 border-start ps-md-4" style="border-color: #e2e8f0 !important;">
                <span class="text-xs fw-bold text-neutral-700 text-uppercase d-block mb-1" style="color: #475569;">Status Sesi:</span>
                <div class="mb-14"><?= $presensiBadge ?></div>
                <div class="d-grid gap-2">
                    <?php if ($presensiStatus === 'aktif'): ?>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId . '/tutup-presensi') ?>" method="post" onsubmit="return confirm('Tutup sesi presensi sekarang? Peserta tidak akan bisa check-in.')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger-600 w-100 radius-8 py-9 text-sm fw-bold shadow-sm">
                                <iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> Tutup Sesi Presensi
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId . '/buka-presensi') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success-600 w-100 radius-8 py-9 text-sm fw-bold shadow-sm">
                                <iconify-icon icon="solar:play-circle-bold" class="me-1"></iconify-icon> Buka Sesi Presensi
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Form -->
<form action="<?= $isEdit ? site_url('dashboard/komunikasi/event-agenda/' . (int) ($eventId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/event-agenda/tambah') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Bagian 1: Informasi Utama Kegiatan -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-primary-50 text-primary-600">
                <iconify-icon icon="solar:document-text-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">1. Informasi Utama Kegiatan</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Judul event, jenis kegiatan, mode pelaksanaan, dan visibilitas agenda.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20">
                <div class="col-12">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Nama Kegiatan / Event PBJ <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" class="form-control radius-8 fw-semibold text-neutral-900"
                           value="<?= esc(old('title', $item['title'] ?? '')) ?>" required maxlength="255"
                           placeholder="Contoh: Bimbingan Teknis Tata Kelola Pengadaan Barang/Jasa Berkelanjutan"
                           style="border-color: #cbd5e1; font-size: 15px;">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Tipe Event <span class="text-danger">*</span>
                    </label>
                    <select class="form-select radius-8 fw-semibold text-neutral-800" name="type" required style="border-color: #cbd5e1;">
                        <option value="webinar" <?= in_array(old('type', $item['type'] ?? 'webinar'), ['webinar', 'online'], true) ? 'selected' : '' ?>>Webinar</option>
                        <option value="workshop" <?= old('type', $item['type'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                        <option value="seminar" <?= old('type', $item['type'] ?? '') === 'seminar' ? 'selected' : '' ?>>Seminar</option>
                        <option value="bimtek" <?= old('type', $item['type'] ?? '') === 'bimtek' ? 'selected' : '' ?>>Bimbingan Teknis (Bimtek)</option>
                        <option value="sosialisasi" <?= old('type', $item['type'] ?? '') === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi Regulasi</option>
                        <option value="pelatihan" <?= old('type', $item['type'] ?? '') === 'pelatihan' ? 'selected' : '' ?>>Pelatihan</option>
                        <option value="rakor" <?= old('type', $item['type'] ?? '') === 'rakor' ? 'selected' : '' ?>>Rapat Koordinasi (Rakor)</option>
                        <option value="klinik" <?= old('type', $item['type'] ?? '') === 'klinik' ? 'selected' : '' ?>>Klinik Pengadaan</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Mode Pelaksanaan <span class="text-danger">*</span>
                    </label>
                    <select class="form-select radius-8 fw-semibold text-neutral-800" name="mode" required style="border-color: #cbd5e1;">
                        <option value="online" <?= in_array(old('mode', $item['mode'] ?? 'online'), ['online', 'open'], true) ? 'selected' : '' ?>>Online (Daring)</option>
                        <option value="offline" <?= old('mode', $item['mode'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                        <option value="hybrid" <?= old('mode', $item['mode'] ?? '') === 'hybrid' ? 'selected' : '' ?>>Hybrid (Online &amp; Offline)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Status Publikasi <span class="text-danger">*</span>
                    </label>
                    <select class="form-select radius-8 fw-semibold text-neutral-800" name="status" style="border-color: #cbd5e1;">
                        <option value="draft" <?= old('status', $item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="coming_soon" <?= old('status', $item['status'] ?? '') === 'coming_soon' ? 'selected' : '' ?>>Segera Hadir</option>
                        <option value="open_registration" <?= old('status', $item['status'] ?? '') === 'open_registration' ? 'selected' : '' ?>>Pendaftaran Dibuka</option>
                        <option value="published" <?= old('status', $item['status'] ?? '') === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="full" <?= old('status', $item['status'] ?? '') === 'full' ? 'selected' : '' ?>>Kuota Penuh</option>
                        <option value="finished" <?= old('status', $item['status'] ?? '') === 'finished' ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= old('status', $item['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Visibilitas Akses
                    </label>
                    <select class="form-select radius-8 fw-semibold text-neutral-800" name="visibility" style="border-color: #cbd5e1;">
                        <option value="public" <?= old('visibility', $item['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Publik (Semua Pengunjung)</option>
                        <option value="internal" <?= old('visibility', $item['visibility'] ?? '') === 'internal' ? 'selected' : '' ?>>Internal ASN / Lembaga</option>
                        <option value="restricted" <?= old('visibility', $item['visibility'] ?? '') === 'restricted' ? 'selected' : '' ?>>Terbatas (Undangan Khusus)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 2: Waktu & Jadwal Pelaksanaan -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-info-50 text-info-600">
                <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">2. Waktu &amp; Jadwal Pelaksanaan</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Jadwal sesi kegiatan serta periode pembukaan dan penutupan pendaftaran peserta.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Waktu Mulai Acara <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="start_at" class="form-control radius-8 text-neutral-900 fw-medium"
                           value="<?= esc(! empty($item['start_at']) ? date('Y-m-d\TH:i', strtotime($item['start_at'])) : old('start_at')) ?>"
                           style="border-color: #cbd5e1;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Waktu Selesai Acara <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="end_at" class="form-control radius-8 text-neutral-900 fw-medium"
                           value="<?= esc(! empty($item['end_at']) ? date('Y-m-d\TH:i', strtotime($item['end_at'])) : old('end_at')) ?>"
                           style="border-color: #cbd5e1;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Pendaftaran Dibuka
                    </label>
                    <input type="datetime-local" name="registration_open_at" class="form-control radius-8 text-neutral-900 fw-medium"
                           value="<?= esc(! empty($item['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_open_at'])) : old('registration_open_at')) ?>"
                           style="border-color: #cbd5e1;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Pendaftaran Ditutup
                    </label>
                    <input type="datetime-local" name="registration_close_at" class="form-control radius-8 text-neutral-900 fw-medium"
                           value="<?= esc(! empty($item['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($item['registration_close_at'])) : old('registration_close_at')) ?>"
                           style="border-color: #cbd5e1;">
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 3: Lokasi & Akses Pertemuan -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-warning-50 text-warning-600">
                <iconify-icon icon="solar:map-point-wave-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">3. Lokasi &amp; Akses Pertemuan</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Alamat fisik gedung kegiatan serta tautan daring platform video conference.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Lokasi / Tempat Fisik (Untuk Offline / Hybrid)
                    </label>
                    <input type="text" name="location" class="form-control radius-8 text-neutral-900 fw-medium"
                           value="<?= esc(old('location', $item['location'] ?? '')) ?>"
                           placeholder="Contoh: Gedung Pusiban Kantor Gubernur Lampung, Jl. Wolter Monginsidi No.69"
                           style="border-color: #cbd5e1;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Tautan Meeting Online (Zoom / Google Meet / Streaming)
                    </label>
                    <input type="url" name="meeting_url" class="form-control radius-8 text-neutral-900 fw-medium font-monospace"
                           value="<?= esc(old('meeting_url', $item['meeting_url'] ?? '')) ?>"
                           placeholder="https://zoom.us/j/... atau https://meet.google.com/..."
                           style="border-color: #cbd5e1;">
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 4: Kuota, Waitlist & Sertifikat -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-success-50 text-success-600">
                <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">4. Kuota, Waitlist &amp; Sertifikat</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Batas kapasitas peserta, pengelolaan antrian waitlist, dan penyediaan sertifikat.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Kapasitas Peserta (Kuota)
                    </label>
                    <div class="position-relative">
                        <input type="number" min="0" name="capacity" class="form-control radius-8 text-neutral-900 fw-semibold"
                               value="<?= esc(old('capacity', $item['capacity'] ?? '')) ?>"
                               placeholder="Contoh: 100 (kosongkan jika tidak terbatas)"
                               style="border-color: #cbd5e1;">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-16 text-xs text-neutral-500 font-medium">Orang</span>
                    </div>
                    <small class="text-neutral-500 text-xs mt-1 d-block">Biarkan kosong atau isi 0 jika kuota tidak dibatasi.</small>
                </div>

                <div class="col-md-6">
                    <div class="p-16 radius-10 border bg-light d-flex flex-column gap-3" style="border-color: #e2e8f0;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="waitlist_enabled" id="waitlist" value="1" <?= ! empty($item['waitlist_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-neutral-900 text-sm" for="waitlist" style="color: #0f172a;">
                                Aktifkan Antrian Waitlist
                            </label>
                            <small class="text-neutral-600 d-block text-xs" style="color: #475569;">Bila kuota penuh, pendaftar baru otomatis masuk ke antrian waitlist.</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="certificate_enabled" id="cert" value="1" <?= ! empty($item['certificate_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-neutral-900 text-sm" for="cert" style="color: #0f172a;">
                                Terbitkan E-Sertifikat Kehadiran
                            </label>
                            <small class="text-neutral-600 d-block text-xs" style="color: #475569;">Peserta yang terbukti hadir (presensi sah) berhak mendapatkan e-sertifikat terverifikasi QR.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 5: Konten & Deskripsi Agenda -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-purple-50 text-purple-600">
                <iconify-icon icon="solar:notes-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">5. Konten &amp; Deskripsi Agenda</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Ringkasan singkat, narasumber/pembicara, dan silabus atau rundown acara.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20">
                <div class="col-12">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Ringkasan Singkat Kegiatan (Summary)
                    </label>
                    <textarea name="summary" class="form-control radius-8 text-neutral-900 font-medium" rows="3"
                              placeholder="Tuliskan rangkuman 1-2 paragraf mengenai urgensi, topik inti, dan narasumber kegiatan..."
                              style="border-color: #cbd5e1;"><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Deskripsi Lengkap, Rundown &amp; Ketentuan Acara
                    </label>
                    <textarea name="description" class="form-control radius-8 text-neutral-900 font-medium" rows="7"
                              placeholder="Tuliskan deskripsi lengkap, latar belakang materi, profil narasumber, susunan acara (rundown), tata tertib, serta persyaratan peserta..."
                              style="border-color: #cbd5e1;"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 6: Banner & Gambar Cover (Rasio 16:9) -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-teal-50 text-teal-600">
                <iconify-icon icon="solar:gallery-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">6. Banner &amp; Gambar Cover (Rasio 16:9)</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Banner visual kegiatan yang tampil pada landing page, katalog agenda, dan media sosial.</small>
            </div>
        </div>
        <div class="form-section-body">
            <div class="row gy-20 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Unggah Gambar Cover Baru
                    </label>
                    <input type="file" name="cover" id="eventCoverInput" class="form-control radius-8 mb-2" accept="image/jpeg,image/png,image/webp" style="border-color: #cbd5e1;">

                    <!-- Guidelines Alert -->
                    <div class="p-12 radius-8 border mb-12" style="background: #f8fafc; border-color: #cbd5e1;">
                        <span class="text-xs fw-bold text-neutral-800 d-flex align-items-center gap-1 mb-1" style="color: #1e293b;">
                            <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-sm"></iconify-icon>
                            Panduan Rekomendasi Banner:
                        </span>
                        <ul class="text-xs text-neutral-600 mb-0 ps-16" style="color: #475569;">
                            <li>Rasio Aspek: <strong>16:9</strong> (Landscape)</li>
                            <li>Dimensi Optimal: <strong>1200 &times; 675 piksel</strong> (atau 1920 &times; 1080 px)</li>
                            <li>Format yang didukung: <strong>JPG, JPEG, PNG, WEBP</strong></li>
                            <li>Ukuran Maksimal: <strong>5 MB</strong></li>
                        </ul>
                    </div>

                    <div id="fileUploadInfo" class="d-none alert alert-info py-8 px-12 text-xs d-flex align-items-center justify-content-between mb-0 radius-8">
                        <span id="fileNameSize" class="text-truncate me-2 fw-semibold"></span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" id="clearFileBtn" title="Hapus Gambar Pilihan">
                            <iconify-icon icon="solar:trash-bin-trash-bold" class="text-base"></iconify-icon>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-6" style="color: #0f172a;">
                        Pratinjau Cover (Preview)
                    </label>
                    <div class="cover-preview-box mx-auto" id="coverPreviewContainer">
                        <?php if ($coverUrl): ?>
                            <img src="<?= esc($coverUrl) ?>" alt="Cover Pratinjau" class="cover-preview-img" id="coverPreviewImg">
                        <?php else: ?>
                            <div class="text-center p-20" id="coverPlaceholder">
                                <iconify-icon icon="solar:gallery-wide-bold" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                                <p class="text-xs text-neutral-500 mb-0">Belum ada gambar cover dipilih.<br>Rasio standar 16:9</p>
                            </div>
                            <img src="" alt="Pratinjau Gambar" class="cover-preview-img d-none" id="coverPreviewImg">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Bar -->
    <div class="card p-20 radius-12 border bg-white d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24 shadow-sm" style="border-color: #cbd5e1;">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 fw-semibold text-sm text-neutral-700" style="border-color: #cbd5e1;">
                Batal
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 fw-bold text-sm shadow-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                <span><?= $isEdit ? 'Simpan Perubahan Event' : 'Simpan &amp; Publikasikan Event' ?></span>
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('eventCoverInput');
    const previewContainer = document.getElementById('coverPreviewContainer');
    const previewImg = document.getElementById('coverPreviewImg');
    const placeholder = document.getElementById('coverPlaceholder');
    const fileUploadInfo = document.getElementById('fileUploadInfo');
    const fileNameSize = document.getElementById('fileNameSize');
    const clearBtn = document.getElementById('clearFileBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file melebihi 5MB! Harap pilih gambar yang lebih kecil.');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewImg.classList.remove('d-none');
                    if (placeholder) placeholder.classList.add('d-none');
                };
                reader.readAsDataURL(file);

                const sizeKb = (file.size / 1024).toFixed(1);
                fileNameSize.textContent = `${file.name} (${sizeKb} KB)`;
                fileUploadInfo.classList.remove('d-none');
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            fileInput.value = '';
            fileUploadInfo.classList.add('d-none');
            <?php if ($coverUrl): ?>
                previewImg.src = '<?= esc($coverUrl) ?>';
                previewImg.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            <?php else: ?>
                previewImg.src = '';
                previewImg.classList.add('d-none');
                if (placeholder) placeholder.classList.remove('d-none');
            <?php endif; ?>
        });
    }
});
</script>

<?= $this->endSection() ?>
