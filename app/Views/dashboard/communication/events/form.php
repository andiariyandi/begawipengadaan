<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
    /* Bulletproof Jitter-Free & Shake-Free Scroll at Viewport Extremities */
    html {
        scroll-behavior: auto !important;
        overflow-x: clip !important;
        overflow-y: scroll !important;
        overscroll-behavior: none !important;
        overscroll-behavior-y: none !important;
        overflow-anchor: none !important;
    }

    body {
        overflow-x: clip !important;
        overflow-y: visible !important;
        overscroll-behavior: none !important;
        overscroll-behavior-y: none !important;
        overflow-anchor: none !important;
        min-height: 100vh;
    }

    .dashboard-main {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-inline-start 0.3s ease, margin-left 0.3s ease !important;
    }

    .dashboard-main-body {
        flex: 1 0 auto;
    }

    .d-footer {
        margin-block-start: 24px !important;
        position: relative;
        z-index: 1;
    }

    /* High-contrast and legibility overrides for Admin Dashboard */
    .text-secondary-light,
    .text-muted,
    .text-neutral-500 {
        color: #475569 !important;
    }

    .text-neutral-600 {
        color: #334155 !important;
    }

    .text-neutral-700 {
        color: #1e293b !important;
    }

    .text-neutral-800,
    .text-neutral-900 {
        color: #0f172a !important;
    }

    .form-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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
$materials = $materials ?? [];
$assessmentConfig = $assessmentConfig ?? [
    'pre_test'   => ['enabled' => false, 'title' => 'Pre-Test Pemahaman Materi', 'duration_minutes' => 15, 'passing_score' => 70, 'questions' => []],
    'post_test'  => ['enabled' => false, 'title' => 'Post-Test Evaluasi Sesi', 'duration_minutes' => 15, 'passing_score' => 70, 'questions' => []],
    'self_paced' => [
        'enabled'             => false,
        'recording_url'       => '',
        'recording_title'     => 'Rekaman Sesi Paparan Narasumber',
        'extended_until'      => null,
        'min_post_test_score' => 70,
        'require_pre_test'    => true,
        'require_materials'   => true,
        'require_post_test'   => true,
        'instructions'        => '',
    ],
];
$coverUrl = ! empty($item['cover_file_id']) ? site_url('file/' . (int) $item['cover_file_id']) : (! empty($item['cover_path']) ? (str_starts_with($item['cover_path'], 'http') ? $item['cover_path'] : base_url($item['cover_path'])) : null);
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
        $presensiBadge = '<span class="badge bg-neutral-200 text-neutral-800 px-12 py-6 radius-6 fw-bold border border-neutral-300 d-inline-flex align-items-center gap-1"><iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Belum Dibuka</span>';
    } elseif ($closeAt !== null && $closeAt <= $now) {
        $presensiStatus = 'ditutup';
        $presensiBadge = '<span class="badge bg-danger-50 text-danger-700 px-12 py-6 radius-6 fw-bold border border-danger-200 d-inline-flex align-items-center gap-1"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Sesi Ditutup</span>';
    } else {
        $presensiStatus = 'aktif';
        $presensiBadge = '<span class="badge bg-success-50 text-success-700 px-12 py-6 radius-6 fw-bold border border-success-200 d-inline-flex align-items-center gap-1"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Sesi Aktif</span>';
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
        <div class="card-body p-24 bg-base">
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
<form action="<?= $isEdit ? site_url('dashboard/komunikasi/event-agenda/' . (int) ($eventId ?? 0) . '/ubah') : site_url('dashboard/komunikasi/event-agenda/tambah') ?>" method="post" enctype="multipart/form-data" id="eventMainForm">
    <?= csrf_field() ?>
    <input type="hidden" name="pre_test_data" id="inputPreTestData" value="<?= esc(json_encode($assessmentConfig['pre_test'] ?? [], JSON_UNESCAPED_UNICODE)) ?>">
    <input type="hidden" name="post_test_data" id="inputPostTestData" value="<?= esc(json_encode($assessmentConfig['post_test'] ?? [], JSON_UNESCAPED_UNICODE)) ?>">
    <input type="hidden" name="self_paced_data" id="inputSelfPacedData" value="<?= esc(json_encode($assessmentConfig['self_paced'] ?? [], JSON_UNESCAPED_UNICODE)) ?>">

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
                        <option value="sosialisasi" <?= old('type', $item['type'] ?? '') === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
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
                        <option value="draft" <?= old('status', $item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft (Draf Acara)</option>
                        <option value="open_registration" <?= in_array(old('status', $item['status'] ?? ''), ['open_registration', 'published']) ? 'selected' : '' ?>>Dipublikasikan (Pendaftaran Dibuka)</option>
                        <option value="coming_soon" <?= old('status', $item['status'] ?? '') === 'coming_soon' ? 'selected' : '' ?>>Segera Hadir (Coming Soon)</option>
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
                            <?php if ($formMode === 'create'): ?>
                                <div class="p-8 radius-6 bg-primary-50 border border-primary-200 text-xs text-primary-700 mt-2 d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:info-circle-bold" class="fs-6 flex-shrink-0"></iconify-icon>
                                    <span>Setelah simpan, sistem akan mengarahkan Anda ke pembuatan Template Sertifikat khusus acara ini.</span>
                                </div>
                            <?php elseif (! empty($eventId)): ?>
                                <?php if (! empty($certTemplate)): ?>
                                    <div class="p-8 radius-6 bg-success-50 border border-success-200 text-xs text-success-800 mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span class="d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-success-600 fs-6"></iconify-icon>
                                            Template Sertifikat: <strong><?= esc($certTemplate['name']) ?></strong>
                                        </span>
                                        <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $certTemplate['id'] . '/ubah') ?>" class="btn btn-outline-success btn-xs radius-4 px-8 py-2 text-xs fw-bold">
                                            Ubah Template
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="p-8 radius-6 bg-warning-50 border border-warning-200 text-xs text-warning-800 mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span class="d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:danger-circle-bold" class="text-warning-600 fs-6"></iconify-icon>
                                            Belum ada template sertifikat unik.
                                        </span>
                                        <a href="<?= site_url('dashboard/sertifikat/template/tambah?source_type=event&event_id=' . (int) $eventId) ?>" class="btn btn-warning-600 text-neutral-900 fw-semibold btn-xs radius-4 px-8 py-2 text-xs fw-bold">
                                            + Buat Template
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
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
                        Deskripsi Lengkap Acara &amp; Ketentuan Peserta
                    </label>
                    <textarea name="description" class="form-control radius-8 text-neutral-900 font-medium" rows="5"
                        placeholder="Tuliskan deskripsi lengkap, latar belakang materi, profil narasumber, tata tertib, serta persyaratan peserta..."
                        style="border-color: #cbd5e1;"><?= esc(old('description', $item['description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 5B: Susunan Acara (Rundown Kegiatan Dinamis Berbasis Jam) -->
    <div class="form-section-card" id="rundownCard">
        <div class="form-section-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="form-section-icon bg-indigo-50 text-indigo-600">
                    <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">5B. Susunan Acara (Rundown Kegiatan Dinamis)</h6>
                    <small class="text-neutral-600 font-medium" style="color: #475569;">Atur sesi jadwal per jam pelaksanaan. Data ini akan ditampilkan otomatis pada landing page event terurut berbasis jam.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-xs radius-6 px-12 py-6 fw-semibold text-xs d-inline-flex align-items-center gap-1" id="btnSortRundown">
                    <iconify-icon icon="solar:sort-from-top-to-bottom-bold"></iconify-icon> Urutkan Jam
                </button>
                <button type="button" class="btn btn-outline-primary btn-xs radius-6 px-12 py-6 fw-semibold text-xs d-inline-flex align-items-center gap-1" id="btnLoadDefaultRundown">
                    <iconify-icon icon="solar:magic-stick-3-bold"></iconify-icon> Muat Template Sesi
                </button>
                <button type="button" class="btn btn-primary-600 btn-xs radius-6 px-12 py-6 fw-bold text-xs d-inline-flex align-items-center gap-1" id="btnAddRundownRow">
                    <iconify-icon icon="solar:add-circle-bold"></iconify-icon> + Tambah Sesi
                </button>
            </div>
        </div>
        <div class="form-section-body p-0">
            <input type="hidden" name="rundown_data" id="rundownDataHidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="rundownFormTable">
                    <thead class="table-light" style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="width: 140px;" class="text-xs fw-bold text-neutral-700 ps-20">Jam Mulai <span class="text-danger">*</span></th>
                            <th style="width: 140px;" class="text-xs fw-bold text-neutral-700">Jam Selesai</th>
                            <th style="min-width: 230px;" class="text-xs fw-bold text-neutral-700">Sesi &amp; Agenda Acara <span class="text-danger">*</span></th>
                            <th style="min-width: 200px;" class="text-xs fw-bold text-neutral-700">Keterangan / Silabus</th>
                            <th style="min-width: 180px;" class="text-xs fw-bold text-neutral-700">Fasilitator / Narasumber</th>
                            <th style="width: 60px;" class="text-center text-xs fw-bold text-neutral-700 pe-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rundownTableBody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="emptyRundownNotice" class="d-none p-24 text-center text-neutral-500 text-sm">
                <iconify-icon icon="solar:calendar-minimalistic-broken" class="display-6 d-block mb-2 text-neutral-400"></iconify-icon>
                <span>Belum ada susunan acara yang ditambahkan. Klik <strong>+ Tambah Sesi</strong> atau <strong>Muat Template Sesi</strong> untuk mengisi agenda.</span>
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

    <!-- Bagian 7: Materi & Bahan Tayangan Presentasi (Opsional) -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-info-50 text-info-600">
                <iconify-icon icon="solar:folder-with-files-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">7. Materi &amp; Bahan Tayangan Presentasi (Opsional)</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Unggah dokumen presentasi, lembar kerja, atau bahan tayang pemateri (PDF, PPT, PPTX, XLS, XLSX, DOC, DOCX).</small>
            </div>
        </div>
        <div class="form-section-body">
            <!-- Guidelines Alert -->
            <div class="p-16 radius-8 border mb-20 bg-light d-flex align-items-start gap-3" style="border-color: #cbd5e1;">
                <iconify-icon icon="solar:info-circle-bold" class="text-info-600 fs-4 mt-1 flex-shrink-0"></iconify-icon>
                <div class="text-xs text-neutral-700" style="color: #334155;">
                    <strong class="d-block text-neutral-900 mb-1" style="font-size: 13px;">Panduan Bahan Tayang:</strong>
                    Materi yang diunggah akan otomatis dapat diakses dan diunduh oleh peserta yang terdaftar pada menu <strong>/workspace/peserta/event-saya/*</strong>.
                    Format file yang didukung meliputi <strong>PDF, PPT, PPTX, XLS, XLSX, DOC, DOCX</strong> dengan batas ukuran hingga <strong>30 MB per file</strong>.
                </div>
            </div>

            <?php if ($isEdit && ! empty($materials)): ?>
                <!-- Daftar Materi yang Sudah Diunggah -->
                <div class="mb-24">
                    <label class="form-label fw-bold text-neutral-900 text-sm mb-2" style="color: #0f172a;">
                        Materi yang Sudah Tersedia (<?= count($materials) ?> File):
                    </label>
                    <div class="table-responsive border rounded-8" style="border-color: #e2e8f0;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-neutral-50 text-xs text-neutral-700 border-bottom">
                                <tr>
                                    <th class="ps-16 py-12">Format</th>
                                    <th class="py-12">Judul Materi &amp; Narasumber</th>
                                    <th class="py-12">Ukuran</th>
                                    <th class="py-12">Tgl Unggah</th>
                                    <th class="pe-16 py-12 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $mat): ?>
                                    <tr>
                                        <td class="ps-16">
                                            <span class="d-inline-flex align-items-center justify-content-center w-36-px h-36-px rounded-8 bg-neutral-100 fs-5">
                                                <i class="<?= esc($mat['icon'] ?? 'fas fa-file-alt') ?>"></i>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-neutral-900 text-sm"><?= esc($mat['title']) ?></div>
                                            <div class="text-xs text-neutral-600">
                                                <?php if (! empty($mat['speaker'])): ?>
                                                    <span class="text-primary-600 fw-medium"><i class="fas fa-user-tie me-1"></i><?= esc($mat['speaker']) ?></span> &bull;
                                                <?php endif; ?>
                                                <span class="font-monospace"><?= esc($mat['original_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-xs text-neutral-600 fw-medium"><?= esc($mat['size_human']) ?></td>
                                        <td class="text-xs text-neutral-600"><?= esc(date('d M Y, H:i', strtotime($mat['created_at']))) ?></td>
                                        <td class="pe-16 text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="<?= esc($mat['download_url']) ?>" class="btn btn-outline-primary-600 btn-sm radius-6 px-10 py-6 text-xs fw-semibold" title="Unduh File">
                                                    <iconify-icon icon="solar:download-bold" class="me-1"></iconify-icon> Unduh
                                                </a>
                                                <button type="submit" formaction="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $eventId . '/materi/' . (int) $mat['id'] . '/hapus') ?>"
                                                    class="btn btn-outline-danger btn-sm radius-6 px-10 py-6 text-xs fw-semibold"
                                                    onclick="return confirm('Hapus materi \'<?= esc(addslashes($mat['title'])) ?>\'?');"
                                                    title="Hapus Materi">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Upload Materi Baru (Dynamic Rows) -->
            <label class="form-label fw-bold text-neutral-900 text-sm mb-2" style="color: #0f172a;">
                <?= $isEdit ? 'Unggah Materi Tambahan Baru:' : 'Unggah File Materi Event:' ?>
            </label>
            <div id="materialsUploadContainer" class="d-flex flex-column gap-3 mb-16">
                <!-- Initial Material Row -->
                <div class="material-upload-row p-16 rounded-8 border bg-neutral-50" style="border-color: #cbd5e1;">
                    <div class="row gy-3 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Pilih File (PDF, PPT, PPTX, XLS, DOC) <span class="text-muted">(Maks 30MB)</span></label>
                            <input type="file" name="materials[]" class="form-control form-control-sm radius-6" accept=".pdf,.ppt,.pptx,.xls,.xlsx,.doc,.docx" style="border-color: #cbd5e1;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Judul / Keterangan Dokumen</label>
                            <input type="text" name="material_titles[]" class="form-control form-control-sm radius-6" placeholder="Contoh: Slide Presentasi Kebijakan PBJ" style="border-color: #cbd5e1;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Nama Pemateri / Narasumber</label>
                            <div class="d-flex gap-2">
                                <input type="text" name="material_speakers[]" class="form-control form-control-sm radius-6" placeholder="Contoh: Tim Fasilitator LKPP" style="border-color: #cbd5e1;">
                                <button type="button" class="btn btn-outline-danger btn-sm radius-6 px-10 py-6 btn-remove-material-row d-none" title="Hapus Baris">
                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold" id="btnAddMaterialRow">
                <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> + Tambah File Materi Lainnya
            </button>
        </div>
    </div>

    <!-- Bagian 8: Aktivitas Interaktif: Pre-Test & Post-Test (Opsional - Seperti Arena Belajar) -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-success-50 text-success-600">
                <iconify-icon icon="solar:clipboard-check-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">8. Aktivitas Interaktif: Pre-Test &amp; Post-Test (Opsional - Seperti Arena Belajar)</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Fasilitas kuis asesmen pengayaan. Peserta tetap wajib mengisi presensi (kode sandi kehadiran) untuk menerbitkan sertifikat.</small>
            </div>
        </div>
        <div class="form-section-body">
            <!-- Alert Prinsip Kehadiran -->
            <div class="p-16 radius-8 border mb-24 d-flex align-items-start gap-3" style="background: #f0fdf4; border-color: #bbf7d0;">
                <iconify-icon icon="solar:shield-check-bold" class="text-success-600 fs-4 mt-1 flex-shrink-0"></iconify-icon>
                <div class="text-xs text-neutral-700" style="color: #166534;">
                    <strong class="d-block text-success-900 mb-1" style="font-size: 13px;">Sifat Fitur: Pengayaan Interaktif (Opsional)</strong>
                    Pre-Test dan Post-Test di event ini dirancang seperti fitur asesmen pada <strong>Arena Belajar PBJ</strong> agar narasumber dapat berinteraksi dan menguji pemahaman peserta.
                    Fitur ini bersifat <strong>opsional</strong>. Syarat utama dan mutlak penerbitan sertifikat tetap adalah <strong>Presensi Kehadiran Sah via Kode Presensi</strong>.
                </div>
            </div>

            <div class="row gy-4">
                <!-- Pre-Test Card -->
                <div class="col-lg-6">
                    <div class="p-20 radius-10 border h-100 d-flex flex-column justify-content-between" style="background: #ffffff; border-color: #cbd5e1 !important;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between pb-12 mb-16 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-32-px h-32-px rounded-circle bg-primary-100 text-primary-700 d-flex align-items-center justify-content-center fw-bold text-sm">
                                        1
                                    </span>
                                    <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Pre-Test (Awal Sesi)</h6>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" id="switchPreTest" <?= ! empty($assessmentConfig['pre_test']['enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-xs text-neutral-700 cursor-pointer" for="switchPreTest">Aktifkan</label>
                                </div>
                            </div>

                            <div class="mb-14">
                                <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Judul Pre-Test</label>
                                <input type="text" id="inputPreTestTitle" class="form-control form-control-sm radius-6"
                                    value="<?= esc($assessmentConfig['pre_test']['title'] ?? 'Pre-Test Pemahaman Materi') ?>"
                                    placeholder="Judul pre-test...">
                            </div>

                            <div class="row g-2 mb-14">
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Durasi (Menit)</label>
                                    <input type="number" min="1" max="180" id="inputPreTestDuration" class="form-control form-control-sm radius-6"
                                        value="<?= esc($assessmentConfig['pre_test']['duration_minutes'] ?? 15) ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Nilai Kelulusan (Passing)</label>
                                    <input type="number" min="0" max="100" id="inputPreTestPassingScore" class="form-control form-control-sm radius-6"
                                        value="<?= esc($assessmentConfig['pre_test']['passing_score'] ?? 70) ?>">
                                </div>
                            </div>

                            <div class="mb-14">
                                <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Petunjuk Pengerjaan Pre-Test</label>
                                <textarea id="inputPreTestInstructions" class="form-control form-control-sm radius-6" rows="2" placeholder="Petunjuk pengerjaan bagi peserta..."><?= esc($assessmentConfig['pre_test']['instructions'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="pt-12 border-top d-flex align-items-center justify-content-between">
                            <span id="preTestCountBadge" class="badge bg-primary-50 text-primary-700 border border-primary-200 px-10 py-6 radius-6 fw-semibold text-xs">
                                <iconify-icon icon="solar:document-text-bold" class="me-1"></iconify-icon>
                                <?= count($assessmentConfig['pre_test']['questions'] ?? []) ?> Soal Dikonfigurasi
                            </span>
                            <button type="button" class="btn btn-outline-primary-600 btn-sm radius-8 px-14 py-8 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPreTestQuestions">
                                <iconify-icon icon="solar:pen-new-square-bold" class="me-1"></iconify-icon> Kelola Soal Pre-Test
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Post-Test Card -->
                <div class="col-lg-6">
                    <div class="p-20 radius-10 border h-100 d-flex flex-column justify-content-between" style="background: #ffffff; border-color: #cbd5e1 !important;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between pb-12 mb-16 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-32-px h-32-px rounded-circle bg-info-100 text-info-700 d-flex align-items-center justify-content-center fw-bold text-sm">
                                        2
                                    </span>
                                    <h6 class="mb-0 fw-bold text-neutral-900" style="color: #0f172a;">Post-Test (Akhir Sesi)</h6>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" id="switchPostTest" <?= ! empty($assessmentConfig['post_test']['enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-xs text-neutral-700 cursor-pointer" for="switchPostTest">Aktifkan</label>
                                </div>
                            </div>

                            <div class="mb-14">
                                <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Judul Post-Test</label>
                                <input type="text" id="inputPostTestTitle" class="form-control form-control-sm radius-6"
                                    value="<?= esc($assessmentConfig['post_test']['title'] ?? 'Post-Test Evaluasi Sesi') ?>"
                                    placeholder="Judul post-test...">
                            </div>

                            <div class="row g-2 mb-14">
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Durasi (Menit)</label>
                                    <input type="number" min="1" max="180" id="inputPostTestDuration" class="form-control form-control-sm radius-6"
                                        value="<?= esc($assessmentConfig['post_test']['duration_minutes'] ?? 15) ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Nilai Kelulusan (Passing)</label>
                                    <input type="number" min="0" max="100" id="inputPostTestPassingScore" class="form-control form-control-sm radius-6"
                                        value="<?= esc($assessmentConfig['post_test']['passing_score'] ?? 70) ?>">
                                </div>
                            </div>

                            <div class="mb-14">
                                <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Petunjuk Pengerjaan Post-Test</label>
                                <textarea id="inputPostTestInstructions" class="form-control form-control-sm radius-6" rows="2" placeholder="Petunjuk pengerjaan bagi peserta..."><?= esc($assessmentConfig['post_test']['instructions'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="pt-12 border-top d-flex align-items-center justify-content-between">
                            <span id="postTestCountBadge" class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-6 radius-6 fw-semibold text-xs">
                                <iconify-icon icon="solar:document-text-bold" class="me-1"></iconify-icon>
                                <?= count($assessmentConfig['post_test']['questions'] ?? []) ?> Soal Dikonfigurasi
                            </span>
                            <button type="button" class="btn btn-outline-info-600 btn-sm radius-8 px-14 py-8 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPostTestQuestions">
                                <iconify-icon icon="solar:pen-new-square-bold" class="me-1"></iconify-icon> Kelola Soal Post-Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 9: Pembelajaran Mandiri & Perpanjangan Akses (Self-Paced Learning Setelah Selesai) -->
    <div class="form-section-card">
        <div class="form-section-header">
            <div class="form-section-icon bg-success-50 text-success-600">
                <iconify-icon icon="solar:play-circle-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-neutral-900" style="color: #0f172a;">9. Pembelajaran Mandiri / Self-Paced (Setelah Jadwal Selesai)</h6>
                <small class="text-neutral-600 font-medium" style="color: #475569;">Opsi agar kegiatan tetap dibuka untuk publik setelah melewati waktu pelaksanaan tatap muka/live. Peserta dapat mengakses rekaman video, materi, dan mengklaim sertifikat via evaluasi mandiri.</small>
            </div>
        </div>
        <div class="form-section-body">
            <!-- Alert Info -->
            <div class="p-16 radius-8 border mb-20 bg-light d-flex align-items-start gap-3" style="border-color: #cbd5e1;">
                <iconify-icon icon="solar:info-circle-bold" class="text-success-600 fs-4 mt-1 flex-shrink-0"></iconify-icon>
                <div class="text-xs text-neutral-700" style="color: #334155;">
                    <strong class="d-block text-neutral-900 mb-1" style="font-size: 13px;">Skema Pembelajaran Mandiri (Sesuai Regulasi Pelatihan):</strong>
                    Bila opsi ini diaktifkan, kegiatan yang telah melewati tanggal realisasi (status selesai) akan tetap dapat diakses dan didaftar oleh publik. E-Sertifikat untuk peserta jalur mandiri diterbitkan <strong>berdasarkan penyelesaian aktivitas mandiri (Pre-Test, menyimak rekaman/materi, dan passing Post-Test)</strong> tanpa memerlukan kode presensi sesi live. Peserta live yang sudah presensi sebelumnya tetap aman dan tidak terpengaruh skema ini.
                </div>
            </div>

            <div class="p-20 radius-10 border bg-white mb-20" style="border-color: #cbd5e1 !important;">
                <div class="d-flex align-items-center justify-content-between pb-12 mb-16 border-bottom">
                    <div>
                        <h6 class="mb-1 fw-bold text-neutral-900" style="color: #0f172a;">Status Pembelajaran Mandiri</h6>
                        <small class="text-neutral-500 text-xs">Aktifkan untuk membuka akses rekaman dan klaim sertifikat mandiri bagi peserta.</small>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" id="switchSelfPaced" <?= ! empty($assessmentConfig['self_paced']['enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-xs text-neutral-700 cursor-pointer" for="switchSelfPaced">Buka Jalur Mandiri</label>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="mb-14">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-1">
                                Link Rekaman Video Sesi (YouTube / Google Drive / Zoom Cloud)
                            </label>
                            <input type="url" id="inputSelfPacedRecordingUrl" class="form-control form-control-sm radius-6"
                                placeholder="https://www.youtube.com/watch?v=... atau link Drive rekaman"
                                value="<?= esc($assessmentConfig['self_paced']['recording_url'] ?? '') ?>">
                            <small class="text-neutral-500 text-xs d-block mt-1">Bila URL YouTube, video player interaktif akan otomatis tertanam pada ruang belajar peserta.</small>
                        </div>

                        <div class="mb-14">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-1">
                                Judul / Label Rekaman Video
                            </label>
                            <input type="text" id="inputSelfPacedRecordingTitle" class="form-control form-control-sm radius-6"
                                placeholder="Contoh: Rekaman Sesi Paparan Narasumber PBJ 2026"
                                value="<?= esc($assessmentConfig['self_paced']['recording_title'] ?? 'Rekaman Sesi Paparan Narasumber') ?>">
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-1">
                                Batas Waktu Perpanjangan Mandiri (Opsional)
                            </label>
                            <input type="datetime-local" id="inputSelfPacedExtendedUntil" class="form-control form-control-sm radius-6"
                                value="<?= ! empty($assessmentConfig['self_paced']['extended_until']) ? date('Y-m-d\TH:i', strtotime($assessmentConfig['self_paced']['extended_until'])) : '' ?>">
                            <small class="text-neutral-500 text-xs d-block mt-1">Kosongkan jika ingin dibuka secara permanen tanpa batas waktu penutupan.</small>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-14">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-1">
                                Passing Grade Post-Test untuk Sertifikat Mandiri (0 - 100)
                            </label>
                            <input type="number" min="0" max="100" id="inputSelfPacedMinScore" class="form-control form-control-sm radius-6"
                                value="<?= esc($assessmentConfig['self_paced']['min_post_test_score'] ?? 70) ?>">
                            <small class="text-neutral-500 text-xs d-block mt-1">Nilai minimal yang harus dicapai peserta agar tombol klaim sertifikat mandiri terbuka.</small>
                        </div>

                        <div class="mb-14 p-12 radius-8 bg-neutral-50 border border-neutral-200">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-2 d-block">Syarat Wajib Sertifikat Mandiri:</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkSelfPacedReqPreTest" <?= ! empty($assessmentConfig['self_paced']['require_pre_test']) ? 'checked' : '' ?>>
                                <label class="form-check-label text-xs text-neutral-700" for="checkSelfPacedReqPreTest">Wajib menyelesaikan Pre-Test</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkSelfPacedReqMaterials" <?= ! empty($assessmentConfig['self_paced']['require_materials']) ? 'checked' : '' ?>>
                                <label class="form-check-label text-xs text-neutral-700" for="checkSelfPacedReqMaterials">Wajib membuka/mempelajari bahan tayang &amp; rekaman</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="checkSelfPacedReqPostTest" <?= ! empty($assessmentConfig['self_paced']['require_post_test']) ? 'checked' : '' ?>>
                                <label class="form-check-label text-xs text-neutral-700" for="checkSelfPacedReqPostTest">Wajib lulus Post-Test sesuai passing grade</label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Petunjuk Pembelajaran Mandiri Peserta</label>
                            <textarea id="inputSelfPacedInstructions" class="form-control form-control-sm radius-6" rows="2" placeholder="Petunjuk alur belajar mandiri bagi peserta..."><?= esc($assessmentConfig['self_paced']['instructions'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 10: Pengaturan Sorotan & Hero Slider (Landing Page) -->
    <div class="card mb-24 radius-12 border overflow-hidden">
        <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center gap-3">
            <div class="w-40-px h-40-px rounded-circle bg-warning-100 text-warning-main d-flex align-items-center justify-content-center">
                <iconify-icon icon="solar:star-bold" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-semibold mb-1 text-primary-light">10. Sorotan &amp; Hero Slider (Landing Page)</h6>
                <p class="text-secondary-light text-xs mb-0">Atur apakah agenda ini ditampilkan pada headline hero carousel dan sebagai agenda unggulan.</p>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="row gy-3">
                <div class="col-md-6">
                    <div class="p-16 radius-8 border border-neutral-200 bg-neutral-50 h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-8">
                            <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer d-flex align-items-center gap-2" for="featuredSwitch">
                                <iconify-icon icon="solar:star-bold" class="text-warning-main"></iconify-icon>
                                <span>Jadikan Event Unggulan</span>
                            </label>
                            <div class="form-switch switch-warning d-inline-flex">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="is_featured" value="1" id="featuredSwitch" <?= ! empty($item['is_featured']) ? 'checked' : '' ?>>
                            </div>
                        </div>
                        <p class="text-secondary-light text-xs mb-0">
                            Event unggulan mendapatkan badge khusus dan diprioritaskan pada filter kurasi sorotan di portal publik.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-16 radius-8 border border-neutral-200 bg-neutral-50 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-8">
                            <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer d-flex align-items-center gap-2" for="carouselSwitch">
                                <iconify-icon icon="solar:slider-vertical-bold" class="text-primary-600"></iconify-icon>
                                <span>Tampilkan di Hero Slider</span>
                            </label>
                            <div class="form-switch switch-primary d-inline-flex">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="carousel_enabled" value="1" id="carouselSwitch" <?= ! empty($item['carousel_enabled']) ? 'checked' : '' ?>>
                            </div>
                        </div>
                        <p class="text-secondary-light text-xs mb-12">
                            Agenda akan dimuat ke dalam banner hero carousel di landing page <code>/event</code>.
                        </p>
                        <div class="pt-10 border-top border-neutral-200">
                            <label class="form-label text-xs fw-semibold text-primary-light mb-1" for="carousel_order">Urutan Slide (Order):</label>
                            <input type="number" min="1" max="99" id="carousel_order" name="carousel_order" class="form-control form-control-sm radius-8" value="<?= esc($item['carousel_order'] ?? '') ?>" placeholder="Urutan (cth: 1, 2, 3...)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Bar -->
    <div class="card p-20 radius-12 border bg-base d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24 shadow-sm">
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

<!-- MODAL: KELOLA SOAL PRE-TEST -->
<div class="modal fade" id="modalPreTestQuestions" tabindex="-1" aria-labelledby="modalPreTestQuestionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content radius-12 border-0 shadow">
            <div class="modal-header bg-primary-50 border-bottom py-16 px-20">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px rounded-circle bg-primary-600 text-white d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:clipboard-check-bold" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-neutral-900 mb-0" id="modalPreTestQuestionsLabel">Kelola Soal Pre-Test (Opsional)</h6>
                        <small class="text-xs text-neutral-600 font-medium">Buat butir soal pilihan ganda (A-D) dengan kunci jawaban dan pembahasan.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-20 bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-16">
                    <div class="text-xs text-neutral-600">
                        Total Soal: <strong id="preTestTotalModalLabel" class="text-primary-700">0</strong> butir
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-neutral-300 btn-sm radius-6 text-xs text-neutral-700 bg-white" id="btnLoadPreTestSample">
                            <iconify-icon icon="solar:magic-stick-bold" class="me-1 text-warning-main"></iconify-icon> Muat Contoh Soal PBJ
                        </button>
                        <button type="button" class="btn btn-primary-600 btn-sm radius-6 text-xs fw-bold" id="btnAddPreTestQuestion">
                            <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> + Tambah Soal
                        </button>
                    </div>
                </div>

                <div id="preTestQuestionsList" class="d-flex flex-column gap-3">
                    <!-- Dynamic Question Items Rendered by JS -->
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-12 px-20 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-neutral-300 btn-sm radius-8 px-16 text-neutral-700" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary-600 btn-sm radius-8 px-20 fw-bold" id="btnSavePreTestQuestions" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> Terapkan &amp; Simpan Soal Pre-Test
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: KELOLA SOAL POST-TEST -->
<div class="modal fade" id="modalPostTestQuestions" tabindex="-1" aria-labelledby="modalPostTestQuestionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content radius-12 border-0 shadow">
            <div class="modal-header bg-info-50 border-bottom py-16 px-20">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px rounded-circle bg-info-600 text-white d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:diploma-verified-bold" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-neutral-900 mb-0" id="modalPostTestQuestionsLabel">Kelola Soal Post-Test (Opsional)</h6>
                        <small class="text-xs text-neutral-600 font-medium">Buat butir soal evaluasi pemahaman akhir peserta sesi kegiatan.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-20 bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-16">
                    <div class="text-xs text-neutral-600">
                        Total Soal: <strong id="postTestTotalModalLabel" class="text-info-700">0</strong> butir
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-neutral-300 btn-sm radius-6 text-xs text-neutral-700 bg-white" id="btnLoadPostTestSample">
                            <iconify-icon icon="solar:magic-stick-bold" class="me-1 text-warning-main"></iconify-icon> Muat Contoh Soal PBJ
                        </button>
                        <button type="button" class="btn btn-info-600 text-white btn-sm radius-6 text-xs fw-bold" id="btnAddPostTestQuestion">
                            <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> + Tambah Soal
                        </button>
                    </div>
                </div>

                <div id="postTestQuestionsList" class="d-flex flex-column gap-3">
                    <!-- Dynamic Question Items Rendered by JS -->
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-12 px-20 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-neutral-300 btn-sm radius-8 px-16 text-neutral-700" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info-600 text-white btn-sm radius-8 px-20 fw-bold" id="btnSavePostTestQuestions" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> Terapkan &amp; Simpan Soal Post-Test
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Cover Image Preview Handler
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

        // 2. Dynamic Materials Upload Rows Handler
        const materialsContainer = document.getElementById('materialsUploadContainer');
        const btnAddMaterialRow = document.getElementById('btnAddMaterialRow');

        function updateRemoveButtons() {
            const rows = materialsContainer.querySelectorAll('.material-upload-row');
            rows.forEach(function(r) {
                const btn = r.querySelector('.btn-remove-material-row');
                if (btn) {
                    if (rows.length > 1) {
                        btn.classList.remove('d-none');
                    } else {
                        btn.classList.add('d-none');
                    }
                }
            });
        }

        if (btnAddMaterialRow && materialsContainer) {
            btnAddMaterialRow.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'material-upload-row p-16 rounded-8 border bg-neutral-50';
                newRow.style.borderColor = '#cbd5e1';
                newRow.innerHTML = `
                <div class="row gy-3 align-items-center">
                    <div class="col-md-5">
                        <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Pilih File (PDF, PPT, PPTX, XLS, DOC) <span class="text-muted">(Maks 30MB)</span></label>
                        <input type="file" name="materials[]" class="form-control form-control-sm radius-6" accept=".pdf,.ppt,.pptx,.xls,.xlsx,.doc,.docx" style="border-color: #cbd5e1;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Judul / Keterangan Dokumen</label>
                        <input type="text" name="material_titles[]" class="form-control form-control-sm radius-6" placeholder="Contoh: Modul & Handout Peserta" style="border-color: #cbd5e1;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-bold text-neutral-700 mb-1">Nama Pemateri / Narasumber</label>
                        <div class="d-flex gap-2">
                            <input type="text" name="material_speakers[]" class="form-control form-control-sm radius-6" placeholder="Contoh: Narasumber Ahli" style="border-color: #cbd5e1;">
                            <button type="button" class="btn btn-outline-danger btn-sm radius-6 px-10 py-6 btn-remove-material-row" title="Hapus Baris">
                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            `;
                materialsContainer.appendChild(newRow);
                updateRemoveButtons();
            });

            materialsContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.btn-remove-material-row');
                if (removeBtn) {
                    const row = removeBtn.closest('.material-upload-row');
                    if (row && materialsContainer.querySelectorAll('.material-upload-row').length > 1) {
                        row.remove();
                        updateRemoveButtons();
                    }
                }
            });
        }

        // 3. Pre-Test & Post-Test State Management
        let preTestData = <?= json_encode($assessmentConfig['pre_test'] ?? [
                                'enabled' => false,
                                'title' => 'Pre-Test Pemahaman Materi',
                                'duration_minutes' => 15,
                                'passing_score' => 70,
                                'instructions' => '',
                                'questions' => []
                            ], JSON_UNESCAPED_UNICODE) ?>;

        let postTestData = <?= json_encode($assessmentConfig['post_test'] ?? [
                                'enabled' => false,
                                'title' => 'Post-Test Evaluasi Sesi',
                                'duration_minutes' => 15,
                                'passing_score' => 70,
                                'instructions' => '',
                                'questions' => []
                            ], JSON_UNESCAPED_UNICODE) ?>;

        let selfPacedData = <?= json_encode($assessmentConfig['self_paced'] ?? [
                                'enabled'             => false,
                                'recording_url'       => '',
                                'recording_title'     => 'Rekaman Sesi Paparan Narasumber',
                                'extended_until'      => null,
                                'min_post_test_score' => 70,
                                'require_pre_test'    => true,
                                'require_materials'   => true,
                                'require_post_test'   => true,
                                'instructions'        => '',
                            ], JSON_UNESCAPED_UNICODE) ?>;

        if (!Array.isArray(preTestData.questions)) preTestData.questions = [];
        if (!Array.isArray(postTestData.questions)) postTestData.questions = [];

        // Helper: Render Question List inside Modal
        function renderQuestionList(type) {
            const isPre = (type === 'pre_test');
            const container = document.getElementById(isPre ? 'preTestQuestionsList' : 'postTestQuestionsList');
            const questions = isPre ? preTestData.questions : postTestData.questions;
            const totalLabel = document.getElementById(isPre ? 'preTestTotalModalLabel' : 'postTestTotalModalLabel');

            if (totalLabel) totalLabel.textContent = questions.length;
            if (!container) return;

            if (questions.length === 0) {
                container.innerHTML = `
                <div class="text-center p-30 border rounded-8 bg-white text-muted">
                    <iconify-icon icon="solar:clipboard-list-broken" class="display-5 text-neutral-400 mb-2"></iconify-icon>
                    <p class="mb-2 fw-semibold text-neutral-700">Belum ada butir soal yang ditambahkan.</p>
                    <p class="text-xs text-neutral-500 mb-0">Klik tombol <strong>+ Tambah Soal</strong> atau <strong>Muat Contoh Soal PBJ</strong> di atas.</p>
                </div>
            `;
                return;
            }

            let html = '';
            questions.forEach(function(q, idx) {
                const qNum = idx + 1;
                const options = Array.isArray(q.options) ? q.options : ['', '', '', ''];
                while (options.length < 4) options.push('');

                const selectedAns = parseInt(q.answer || 0, 10);

                html += `
                <div class="card border rounded-8 p-16 bg-white question-card" data-index="${idx}">
                    <div class="d-flex justify-content-between align-items-center mb-12 pb-8 border-bottom">
                        <span class="badge ${isPre ? 'bg-primary-100 text-primary-700' : 'bg-info-100 text-info-700'} fw-bold text-xs">
                            Nomor Soal ${qNum}
                        </span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-delete-question" data-type="${type}" data-index="${idx}" title="Hapus Soal Ini">
                            <iconify-icon icon="solar:trash-bin-trash-bold" class="fs-5"></iconify-icon> Hapus
                        </button>
                    </div>

                    <div class="mb-12">
                        <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Pertanyaan / Soal Kasus:</label>
                        <textarea class="form-control form-control-sm q-text" rows="2" placeholder="Tuliskan pertanyaan disini...">${escapeHtml(q.question || '')}</textarea>
                    </div>

                    <div class="mb-12">
                        <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Pilihan Jawaban (Pilih Radio untuk Kunci Jawaban Benar):</label>
                        <div class="d-flex flex-column gap-2">
            `;

                ['A', 'B', 'C', 'D'].forEach(function(letter, optIdx) {
                    const optVal = escapeHtml(options[optIdx] || '');
                    const isChecked = (selectedAns === optIdx) ? 'checked' : '';
                    html += `
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <input class="form-check-input mt-0 q-correct-radio" type="radio" name="correct_${type}_${idx}" value="${optIdx}" ${isChecked}>
                            <strong class="ms-1">${letter}</strong>
                        </span>
                        <input type="text" class="form-control q-opt-${optIdx}" value="${optVal}" placeholder="Pilihan ${letter}...">
                    </div>
                `;
                });

                html += `
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs fw-bold text-neutral-800 mb-1">Pembahasan / Penjelasan Jawaban (Opsional):</label>
                        <input type="text" class="form-control form-control-sm q-explanation" value="${escapeHtml(q.explanation || '')}" placeholder="Penjelasan singkat acuan regulasi atau alasan...">
                    </div>
                </div>
            `;
            });

            container.innerHTML = html;
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Helper: Collect Questions from Modal Container
        function collectQuestionsFromModal(type) {
            const isPre = (type === 'pre_test');
            const container = document.getElementById(isPre ? 'preTestQuestionsList' : 'postTestQuestionsList');
            if (!container) return [];

            const questionCards = container.querySelectorAll('.question-card');
            const collected = [];

            questionCards.forEach(function(card, idx) {
                const qText = (card.querySelector('.q-text')?.value || '').trim();
                if (!qText) return;

                const opt0 = (card.querySelector('.q-opt-0')?.value || '').trim();
                const opt1 = (card.querySelector('.q-opt-1')?.value || '').trim();
                const opt2 = (card.querySelector('.q-opt-2')?.value || '').trim();
                const opt3 = (card.querySelector('.q-opt-3')?.value || '').trim();

                const options = [opt0 || 'Opsi A', opt1 || 'Opsi B', opt2 || 'Opsi C', opt3 || 'Opsi D'];
                const checkedRadio = card.querySelector(`input[name="correct_${type}_${idx}"]:checked`);
                const answer = checkedRadio ? parseInt(checkedRadio.value, 10) : 0;
                const explanation = (card.querySelector('.q-explanation')?.value || '').trim();

                collected.push({
                    id: idx + 1,
                    question: qText,
                    options: options,
                    answer: answer,
                    explanation: explanation
                });
            });

            return collected;
        }

        // Sync state to hidden inputs and badges
        function syncToHiddenInputs() {
            const switchPre = document.getElementById('switchPreTest');
            const switchPost = document.getElementById('switchPostTest');

            preTestData.enabled = switchPre ? switchPre.checked : false;
            preTestData.title = (document.getElementById('inputPreTestTitle')?.value || '').trim();
            preTestData.duration_minutes = parseInt(document.getElementById('inputPreTestDuration')?.value || 15, 10);
            preTestData.passing_score = parseInt(document.getElementById('inputPreTestPassingScore')?.value || 70, 10);
            preTestData.instructions = (document.getElementById('inputPreTestInstructions')?.value || '').trim();

            postTestData.enabled = switchPost ? switchPost.checked : false;
            postTestData.title = (document.getElementById('inputPostTestTitle')?.value || '').trim();
            postTestData.duration_minutes = parseInt(document.getElementById('inputPostTestDuration')?.value || 15, 10);
            postTestData.passing_score = parseInt(document.getElementById('inputPostTestPassingScore')?.value || 70, 10);
            postTestData.instructions = (document.getElementById('inputPostTestInstructions')?.value || '').trim();

            const inputPre = document.getElementById('inputPreTestData');
            const inputPost = document.getElementById('inputPostTestData');

            if (inputPre) inputPre.value = JSON.stringify(preTestData);
            if (inputPost) inputPost.value = JSON.stringify(postTestData);

            // Self-Paced Learning Sync
            const switchSelf = document.getElementById('switchSelfPaced');
            selfPacedData.enabled = switchSelf ? switchSelf.checked : false;
            selfPacedData.recording_url = (document.getElementById('inputSelfPacedRecordingUrl')?.value || '').trim();
            selfPacedData.recording_title = (document.getElementById('inputSelfPacedRecordingTitle')?.value || '').trim();
            selfPacedData.extended_until = (document.getElementById('inputSelfPacedExtendedUntil')?.value || '').trim() || null;
            selfPacedData.min_post_test_score = parseInt(document.getElementById('inputSelfPacedMinScore')?.value || 70, 10);
            selfPacedData.require_pre_test = document.getElementById('checkSelfPacedReqPreTest')?.checked ?? true;
            selfPacedData.require_materials = document.getElementById('checkSelfPacedReqMaterials')?.checked ?? true;
            selfPacedData.require_post_test = document.getElementById('checkSelfPacedReqPostTest')?.checked ?? true;
            selfPacedData.instructions = (document.getElementById('inputSelfPacedInstructions')?.value || '').trim();

            const inputSelfPaced = document.getElementById('inputSelfPacedData');
            if (inputSelfPaced) inputSelfPaced.value = JSON.stringify(selfPacedData);

            const badgePre = document.getElementById('preTestCountBadge');
            if (badgePre) {
                badgePre.innerHTML = `<iconify-icon icon="solar:document-text-bold" class="me-1"></iconify-icon> ${preTestData.questions.length} Soal Dikonfigurasi`;
            }

            const badgePost = document.getElementById('postTestCountBadge');
            if (badgePost) {
                badgePost.innerHTML = `<iconify-icon icon="solar:document-text-bold" class="me-1"></iconify-icon> ${postTestData.questions.length} Soal Dikonfigurasi`;
            }
        }

        // Modal Event: Render on Open
        const modalPreEl = document.getElementById('modalPreTestQuestions');
        if (modalPreEl) {
            modalPreEl.addEventListener('show.bs.modal', function() {
                renderQuestionList('pre_test');
            });
        }

        const modalPostEl = document.getElementById('modalPostTestQuestions');
        if (modalPostEl) {
            modalPostEl.addEventListener('show.bs.modal', function() {
                renderQuestionList('post_test');
            });
        }

        // Add New Question Buttons
        const btnAddPreQ = document.getElementById('btnAddPreTestQuestion');
        if (btnAddPreQ) {
            btnAddPreQ.addEventListener('click', function() {
                preTestData.questions = collectQuestionsFromModal('pre_test');
                preTestData.questions.push({
                    id: preTestData.questions.length + 1,
                    question: '',
                    options: ['', '', '', ''],
                    answer: 0,
                    explanation: ''
                });
                renderQuestionList('pre_test');
            });
        }

        const btnAddPostQ = document.getElementById('btnAddPostTestQuestion');
        if (btnAddPostQ) {
            btnAddPostQ.addEventListener('click', function() {
                postTestData.questions = collectQuestionsFromModal('post_test');
                postTestData.questions.push({
                    id: postTestData.questions.length + 1,
                    question: '',
                    options: ['', '', '', ''],
                    answer: 0,
                    explanation: ''
                });
                renderQuestionList('post_test');
            });
        }

        // Delete Question Handlers
        document.addEventListener('click', function(e) {
            const delBtn = e.target.closest('.btn-delete-question');
            if (delBtn) {
                const type = delBtn.getAttribute('data-type');
                const idx = parseInt(delBtn.getAttribute('data-index'), 10);
                if (type === 'pre_test') {
                    preTestData.questions = collectQuestionsFromModal('pre_test');
                    preTestData.questions.splice(idx, 1);
                    renderQuestionList('pre_test');
                } else {
                    postTestData.questions = collectQuestionsFromModal('post_test');
                    postTestData.questions.splice(idx, 1);
                    renderQuestionList('post_test');
                }
            }
        });

        // Save Buttons inside Modals
        const btnSavePre = document.getElementById('btnSavePreTestQuestions');
        if (btnSavePre) {
            btnSavePre.addEventListener('click', function() {
                preTestData.questions = collectQuestionsFromModal('pre_test');
                syncToHiddenInputs();
            });
        }

        const btnSavePost = document.getElementById('btnSavePostTestQuestions');
        if (btnSavePost) {
            btnSavePost.addEventListener('click', function() {
                postTestData.questions = collectQuestionsFromModal('post_test');
                syncToHiddenInputs();
            });
        }

        // Load Sample Procurement Questions Template
        const sampleQuestions = [{
                id: 1,
                question: "Manakah di bawah ini yang merupakan salah satu prinsip dasar Pengadaan Barang/Jasa Pemerintah menurut Peraturan Presiden?",
                options: [
                    "Efisien, Efektif, Transparan, Terbuka, Bersaing, Adil, dan Akuntabel",
                    "Cepat, Tanpa Evaluasi, Fleksibel, dan Mandiri",
                    "Mengutamakan Rekanan Tertentu Demi Kelancaran Proyek",
                    "Tertutup untuk Umum dan Hanya Menggunakan Penunjukan Langsung"
                ],
                answer: 0,
                explanation: "Sesuai Pasal 6 Perpres PBJ, prinsip pengadaan meliputi Efisien, Efektif, Transparan, Terbuka, Bersaing, Adil/Tidak Diskriminatif, dan Akuntabel."
            },
            {
                id: 2,
                question: "Apa fungsi utama penyusunan Harga Perkiraan Sendiri (HPS) oleh Pejabat Pembuat Komitmen (PPK)?",
                options: [
                    "Alat untuk menggugurkan semua penawaran peserta",
                    "Alat untuk menilai kewajaran harga penawaran dan batas tertinggi penawaran pengadaan",
                    "Menentukan pemenang lelang sejak awal sebelum proses tender dimulai",
                    "Sebagai dasar penetapan besaran pajak penghasilan penyedia"
                ],
                answer: 1,
                explanation: "HPS berfungsi sebagai alat untuk menilai kewajaran harga penawaran dan batas tertinggi penawaran yang sah untuk tender/seleksi."
            }
        ];

        const btnLoadPreSample = document.getElementById('btnLoadPreTestSample');
        if (btnLoadPreSample) {
            btnLoadPreSample.addEventListener('click', function() {
                if (confirm('Muat 2 butir contoh soal template PBJ ke Pre-Test?')) {
                    preTestData.questions = JSON.parse(JSON.stringify(sampleQuestions));
                    renderQuestionList('pre_test');
                }
            });
        }

        const btnLoadPostSample = document.getElementById('btnLoadPostTestSample');
        if (btnLoadPostSample) {
            btnLoadPostSample.addEventListener('click', function() {
                if (confirm('Muat 2 butir contoh soal template PBJ ke Post-Test?')) {
                    postTestData.questions = JSON.parse(JSON.stringify(sampleQuestions));
                    renderQuestionList('post_test');
                }
            });
        }

        // Self-Paced Inputs Live Sync Listeners
        ['switchSelfPaced', 'checkSelfPacedReqPreTest', 'checkSelfPacedReqMaterials', 'checkSelfPacedReqPostTest'].forEach(function(id) {
            document.getElementById(id)?.addEventListener('change', syncToHiddenInputs);
        });
        ['inputSelfPacedRecordingUrl', 'inputSelfPacedRecordingTitle', 'inputSelfPacedExtendedUntil', 'inputSelfPacedMinScore', 'inputSelfPacedInstructions'].forEach(function(id) {
            document.getElementById(id)?.addEventListener('input', syncToHiddenInputs);
        });

        // -------------------------------------------------------------
        // DYNAMIC RUNDOWN BUILDER (Zero Database Migration)
        // -------------------------------------------------------------
        const rundownTbody = document.getElementById('rundownTableBody');
        const emptyRundownNotice = document.getElementById('emptyRundownNotice');
        const rundownDataHidden = document.getElementById('rundownDataHidden');
        const btnAddRundownRow = document.getElementById('btnAddRundownRow');
        const btnSortRundown = document.getElementById('btnSortRundown');
        const btnLoadDefaultRundown = document.getElementById('btnLoadDefaultRundown');

        let rundownList = <?= json_encode(! empty($rundown) ? $rundown : (old('rundown') ?: [])) ?>;
        if (!Array.isArray(rundownList)) {
            rundownList = [];
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function renderRundownRows() {
            if (!rundownTbody) return;
            rundownTbody.innerHTML = '';

            if (rundownList.length === 0) {
                if (emptyRundownNotice) emptyRundownNotice.classList.remove('d-none');
            } else {
                if (emptyRundownNotice) emptyRundownNotice.classList.add('d-none');
                rundownList.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.dataset.index = index;
                    tr.innerHTML = `
                        <td class="ps-20">
                            <input type="time" class="form-control form-control-sm radius-6 fw-semibold text-neutral-800 font-monospace rd-start-time" value="${item.start_time || ''}" required>
                        </td>
                        <td>
                            <input type="time" class="form-control form-control-sm radius-6 fw-semibold text-neutral-800 font-monospace rd-end-time" value="${item.end_time || ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm radius-6 fw-medium text-neutral-900 rd-title" value="${escapeHtml(item.title || '')}" placeholder="Nama Sesi / Agenda..." required>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm radius-6 text-neutral-800 rd-desc" value="${escapeHtml(item.description || '')}" placeholder="Keterangan / topik pembahasan...">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm radius-6 text-neutral-800 rd-speaker" value="${escapeHtml(item.speaker || '')}" placeholder="Fasilitator / narasumber...">
                        </td>
                        <td class="text-center pe-20">
                            <button type="button" class="btn btn-outline-danger btn-xs radius-6 p-6 btn-del-rundown" title="Hapus Sesi">
                                <iconify-icon icon="solar:trash-bin-trash-bold" class="fs-6"></iconify-icon>
                            </button>
                        </td>
                    `;
                    rundownTbody.appendChild(tr);
                });
            }
            syncRundownData();
        }

        function collectRundownFromDom() {
            if (!rundownTbody) return [];
            const rows = rundownTbody.querySelectorAll('tr');
            const items = [];
            rows.forEach((r) => {
                const startTime = r.querySelector('.rd-start-time')?.value?.trim() || '';
                const endTime = r.querySelector('.rd-end-time')?.value?.trim() || '';
                const title = r.querySelector('.rd-title')?.value?.trim() || '';
                const desc = r.querySelector('.rd-desc')?.value?.trim() || '';
                const speaker = r.querySelector('.rd-speaker')?.value?.trim() || '';

                if (startTime || title) {
                    items.push({
                        start_time: startTime,
                        end_time: endTime,
                        title: title,
                        description: desc,
                        speaker: speaker
                    });
                }
            });
            return items;
        }

        function syncRundownData() {
            const items = collectRundownFromDom();
            rundownList = items;
            if (rundownDataHidden) {
                rundownDataHidden.value = JSON.stringify(items);
            }
        }

        function sortRundownByTime() {
            const items = collectRundownFromDom();
            items.sort((a, b) => (a.start_time || '99:99').localeCompare(b.start_time || '99:99'));
            rundownList = items;
            renderRundownRows();
        }

        if (btnAddRundownRow) {
            btnAddRundownRow.addEventListener('click', function() {
                syncRundownData();
                rundownList.push({
                    start_time: '',
                    end_time: '',
                    title: '',
                    description: '',
                    speaker: ''
                });
                renderRundownRows();
            });
        }

        if (btnSortRundown) {
            btnSortRundown.addEventListener('click', function() {
                sortRundownByTime();
            });
        }

        if (btnLoadDefaultRundown) {
            btnLoadDefaultRundown.addEventListener('click', function() {
                if (rundownList.length > 0 && !confirm('Muat template susunan acara standar? Ini akan menggantikan sesi yang sedang diedit.')) {
                    return;
                }
                rundownList = [
                    {
                        start_time: '08:00',
                        end_time: '08:30',
                        title: 'Pembukaan Acara & Registrasi Peserta',
                        description: 'Verifikasi identitas dan penyambutan peserta',
                        speaker: 'Panitia Biro PBJ Setda Lampung'
                    },
                    {
                        start_time: '08:30',
                        end_time: '10:00',
                        title: 'Pemaparan Materi Pokok & Pembahasan Tematik',
                        description: 'Pemaparan materi utama sosialisasi regulasi pengadaan barang dan jasa',
                        speaker: 'Narasumber & Tim Fasilitator Ahli PBJ'
                    },
                    {
                        start_time: '10:00',
                        end_time: '11:30',
                        title: 'Sesi Tanya Jawab, Diskusi & Evaluasi Kasus',
                        description: 'Konsultasi interaktif langsung bersama narasumber/fasilitator',
                        speaker: 'Moderator & Seluruh Peserta'
                    },
                    {
                        start_time: '11:30',
                        end_time: '12:00',
                        title: 'Rangkuman Hasil, Penutupan & Validasi Presensi',
                        description: 'Perekaman rekam jejak kehadiran dan penerbitan sertifikat',
                        speaker: 'Penyelenggara Resmi BegawiPengadaan'
                    }
                ];
                renderRundownRows();
            });
        }

        if (rundownTbody) {
            rundownTbody.addEventListener('click', function(e) {
                const delBtn = e.target.closest('.btn-del-rundown');
                if (delBtn) {
                    const row = delBtn.closest('tr');
                    if (row) {
                        row.remove();
                        syncRundownData();
                        if (rundownTbody.querySelectorAll('tr').length === 0 && emptyRundownNotice) {
                            emptyRundownNotice.classList.remove('d-none');
                        }
                    }
                }
            });

            rundownTbody.addEventListener('input', function() {
                syncRundownData();
            });
        }

        // Render initial rundown rows
        renderRundownRows();

        // Form Submit Event: Ensure latest sync and sorted rundown
        const eventMainForm = document.getElementById('eventMainForm');
        if (eventMainForm) {
            eventMainForm.addEventListener('submit', function() {
                sortRundownByTime();
                syncRundownData();
                syncToHiddenInputs();
            });
        }

        // Initialize sync on page load
        syncToHiddenInputs();
    });
</script>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    (function() {
        // Bulletproof zero-jitter clamping at scroll extremities (especially for Windows mouse wheel)
        var isClamping = false;
        window.addEventListener('scroll', function() {
            var maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
            if (window.scrollY > maxScroll) {
                if (!isClamping) {
                    isClamping = true;
                    window.scrollTo(0, maxScroll);
                    requestAnimationFrame(function() {
                        isClamping = false;
                    });
                }
            }
        }, {
            passive: true
        });
    })();
</script>
<?= $this->endSection() ?>