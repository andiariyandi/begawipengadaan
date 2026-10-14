<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<style>
    :root {
        --evt-navy-dark: #052244;
        --evt-navy: #082f5c;
        --evt-blue: #0c5aa6;
        --evt-blue-light: #eff6ff;
        --evt-gold: #f59e0b;
        --evt-gold-light: #fef3c7;
        --evt-emerald: #10b981;
        --evt-slate-900: #0f172a;
        --evt-slate-800: #1e293b;
        --evt-slate-700: #334155;
        --evt-slate-600: #475569;
        --evt-slate-500: #64748b;
        --evt-slate-400: #94a3b8;
        --evt-slate-200: #e2e8f0;
        --evt-slate-50: #f8fafc;
    }

    /* Reliable Iconify display */
    iconify-icon {
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        width: 1.15em;
        height: 1.15em;
    }

    /* Universal high-contrast text and border utilities */
    .text-slate-900 {
        color: #0f172a !important;
    }

    .text-slate-800 {
        color: #1e293b !important;
    }

    .text-slate-700 {
        color: #334155 !important;
    }

    .text-slate-600 {
        color: #475569 !important;
    }

    .text-slate-500 {
        color: #64748b !important;
    }

    .text-slate-400 {
        color: #94a3b8 !important;
    }

    .text-slate-300 {
        color: #cbd5e1 !important;
    }

    .text-xs {
        font-size: 12px !important;
        line-height: 1.45;
    }

    .text-sm {
        font-size: 14px !important;
        line-height: 1.5;
    }

    .text-base {
        font-size: 16px !important;
    }

    .text-lg {
        font-size: 18px !important;
    }

    .text-xl {
        font-size: 20px !important;
    }

    .text-2xl {
        font-size: 24px !important;
    }

    .radius-6 {
        border-radius: 6px !important;
    }

    .radius-8 {
        border-radius: 8px !important;
    }

    .radius-10 {
        border-radius: 10px !important;
    }

    .radius-12 {
        border-radius: 12px !important;
    }

    .radius-14 {
        border-radius: 14px !important;
    }

    .radius-16 {
        border-radius: 16px !important;
    }

    .radius-18 {
        border-radius: 18px !important;
    }

    .radius-999 {
        border-radius: 9999px !important;
    }

    /* High-contrast sub badges */
    .bg-primary-subtle {
        background-color: #eff6ff !important;
        color: #0c5aa6 !important;
        border: 1px solid #bfdbfe;
    }

    .bg-success-subtle {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a;
    }

    .bg-info-subtle {
        background-color: #f0f9ff !important;
        color: #0284c7 !important;
        border: 1px solid #bae6fd;
    }

    .bg-secondary-subtle {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
    }

    /* Detail Header Banner - 215px gives safe clearance below absolute header */
    .evt-detail-header-section {
        background: linear-gradient(135deg, var(--evt-navy-dark) 0%, var(--evt-navy) 70%, var(--evt-blue) 100%);
        padding: 215px 0 65px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .evt-detail-header-section::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .evt-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: #f1f5f9;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .evt-breadcrumb a {
        color: #fde68a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evt-breadcrumb a:hover {
        color: #ffffff;
    }

    .evt-detail-title {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.3;
        color: #ffffff;
        margin-bottom: 22px;
        letter-spacing: -0.4px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .evt-detail-meta-bar {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 14px;
        color: #e2e8f0;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .evt-detail-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Main Container */
    .evt-detail-container {
        margin-top: -30px;
        position: relative;
        z-index: 10;
        margin-bottom: 60px;
    }

    .evt-detail-card {
        background: #ffffff;
        border: 1px solid var(--evt-slate-200);
        border-radius: 20px;
        padding: 36px;
        box-shadow: 0 10px 30px rgba(8, 47, 92, 0.06);
        margin-bottom: 30px;
    }

    /* Countdown Widget */
    .evt-countdown-card {
        background: linear-gradient(135deg, #052244 0%, #0f172a 100%);
        border: 1px solid #1e293b;
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 12px 30px rgba(5, 34, 68, 0.15);
    }

    .evt-countdown-units {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .evt-countdown-unit {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 10px;
        padding: 8px 14px;
        text-align: center;
        min-width: 62px;
    }

    .evt-countdown-num {
        font-size: 24px;
        font-weight: 800;
        color: var(--evt-gold);
        line-height: 1;
        display: block;
    }

    .evt-countdown-lbl {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #cbd5e1;
        margin-top: 4px;
        display: block;
    }

    /* Registration Widget */
    .evt-register-widget {
        background: #ffffff;
        border: 2px solid #bfdbfe;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 12px 30px rgba(12, 90, 166, 0.08);
        margin-bottom: 28px;
    }

    .evt-seat-bar-wrap {
        background: #f1f5f9;
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
        margin: 12px 0 18px;
        border: 1px solid #e2e8f0;
    }

    .evt-seat-bar-fill {
        background: linear-gradient(90deg, #10b981 0%, #0c5aa6 100%);
        height: 100%;
        border-radius: 999px;
    }

    /* Rundown Table */
    .evt-rundown-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--evt-slate-200);
        border-radius: 12px;
        overflow: hidden;
        margin: 16px 0 32px;
    }

    .evt-rundown-table th {
        background: #f8fafc;
        padding: 14px 18px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--evt-navy-dark);
        border-bottom: 1px solid var(--evt-slate-200);
    }

    .evt-rundown-table td {
        padding: 16px 18px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        color: var(--evt-slate-700);
    }

    .evt-rundown-table tr:last-child td {
        border-bottom: none;
    }

    /* Facilities List */
    .evt-facility-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #f8fafc;
        border: 1px solid var(--evt-slate-200);
        border-radius: 12px;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--evt-navy-dark);
    }

    /* Share Bar */
    .evt-share-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .evt-share-btn:hover {
        transform: translateY(-2px);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .evt-share-wa {
        background: #25d366;
    }

    .evt-share-tg {
        background: #0088cc;
    }

    .evt-share-tw {
        background: #000000;
    }

    .evt-share-fb {
        background: #1877f2;
    }

    .evt-share-in {
        background: #0077b5;
    }

    .evt-share-copy {
        background: #334155;
        border: none;
        cursor: pointer;
    }

    #copyToast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--evt-navy-dark);
        color: #ffffff;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        display: none;
        z-index: 99999;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 991px) {
        .evt-detail-header-section {
            padding: 185px 0 55px;
        }

        .evt-detail-title {
            font-size: 26px;
        }

        .evt-detail-card {
            padding: 24px;
        }
    }

    @media (max-width: 768px) {
        .evt-detail-header-section {
            padding: 165px 0 50px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. DETAIL HEADER BANNER -->
<section class="evt-detail-header-section">
    <div class="container">
        <div class="evt-breadcrumb">
            <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
            <span>/</span>
            <a href="<?= site_url('event') ?>">Event &amp; Agenda PBJ</a>
            <span>/</span>
            <span class="text-white"><?= esc($typeLabel) ?></span>
        </div>

        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($typeLabel) ?></span>
            <span class="badge bg-success-subtle text-success px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($modeLabel) ?></span>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($statusLabel) ?></span>
            <?php if (! empty($event['certificate_enabled'])): ?>
                <span class="badge bg-info-subtle text-info px-3 py-1 radius-6 text-xs fw-bold text-uppercase d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                    <span>E-Sertifikat Tersedia</span>
                </span>
            <?php endif; ?>
        </div>

        <h1 class="evt-detail-title">
            <?= esc($event['title']) ?>
        </h1>

        <div class="evt-detail-meta-bar">
            <div class="evt-detail-meta-item">
                <iconify-icon icon="solar:calendar-date-bold" class="text-warning text-lg"></iconify-icon>
                <strong><?= date('l, d F Y', $startTime) ?></strong>
            </div>
            <div class="evt-detail-meta-item">
                <iconify-icon icon="solar:clock-circle-bold" class="text-info text-lg"></iconify-icon>
                <span><?= date('H:i', $startTime) ?> - <?= date('H:i', $endTime) ?> WIB</span>
            </div>
            <div class="evt-detail-meta-item">
                <iconify-icon icon="solar:map-point-bold" class="text-danger text-lg"></iconify-icon>
                <span><?= esc($event['location'] ?: ($event['mode'] === 'online' ? 'Daring via Zoom / Platform Online' : 'Pemerintah Provinsi Lampung')) ?></span>
            </div>
            <div class="evt-detail-meta-item ms-lg-auto">
                <iconify-icon icon="solar:users-group-rounded-bold" class="text-success text-lg"></iconify-icon>
                <span>
                    <?php if ($isUnlimited): ?>
                        Kapasitas: <strong>Terbuka (<?= $registeredCount ?> Terdaftar)</strong>
                    <?php else: ?>
                        Tersedia: <strong><?= $remainingSeats ?> / <?= $capacity ?> Kuota</strong>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- 2. DETAIL CONTENT & REGISTRATION SIDEBAR -->
<div class="container evt-detail-container">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 radius-12 p-3 mb-4 shadow-sm border border-success-subtle">
            <iconify-icon icon="solar:check-circle-bold" class="text-2xl text-success flex-shrink-0"></iconify-icon>
            <div class="fw-medium text-slate-800"><?= session()->getFlashdata('message') ?></div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 radius-12 p-3 mb-4 shadow-sm border border-danger-subtle">
            <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl text-danger flex-shrink-0"></iconify-icon>
            <div class="fw-medium text-slate-800"><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <!-- Countdown Timer Card (If Upcoming) -->
    <?php if (! $isPast && ! $isOngoing): ?>
        <div class="evt-countdown-card">
            <div>
                <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-1">Pengingat Waktu Kegiatan</span>
                <h4 class="text-lg fw-bold mb-0 text-white">Kegiatan Dimulai Dalam:</h4>
            </div>
            <div class="evt-countdown-units" id="countdownTimer" data-target="<?= $startTime ?>">
                <div class="evt-countdown-unit">
                    <span class="evt-countdown-num" id="cdDays">00</span>
                    <span class="evt-countdown-lbl">Hari</span>
                </div>
                <div class="evt-countdown-unit">
                    <span class="evt-countdown-num" id="cdHours">00</span>
                    <span class="evt-countdown-lbl">Jam</span>
                </div>
                <div class="evt-countdown-unit">
                    <span class="evt-countdown-num" id="cdMinutes">00</span>
                    <span class="evt-countdown-lbl">Menit</span>
                </div>
                <div class="evt-countdown-unit">
                    <span class="evt-countdown-num" id="cdSeconds">00</span>
                    <span class="evt-countdown-lbl">Detik</span>
                </div>
            </div>
        </div>
    <?php elseif ($isOngoing): ?>
        <div class="alert alert-success p-3 radius-12 d-flex align-items-center gap-3 mb-4 border border-success-subtle shadow-sm">
            <iconify-icon icon="solar:broadcast-bold" class="text-3xl text-success flex-shrink-0"></iconify-icon>
            <div>
                <strong class="d-block text-success">Kegiatan Sedang Berlangsung Hari Ini!</strong>
                <span class="text-xs text-slate-700">Silakan bergabung tepat waktu sesuai ruang pertemuan atau lokasi acara yang telah ditentukan.</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Active Presensi Callout for Logged In Registered Participant -->
    <?php if ($isRegistered): ?>
        <?php if (! empty($userAttendance) && ($userAttendance['status'] ?? '') === 'present'): ?>
            <div class="alert alert-success p-3 radius-12 d-flex align-items-center gap-3 mb-4 border border-success-subtle shadow-sm">
                <iconify-icon icon="solar:verified-check-bold" class="text-3xl text-success flex-shrink-0"></iconify-icon>
                <div>
                    <strong class="text-success d-block">Presensi Kehadiran Sah &amp; Terverifikasi</strong>
                    <small class="text-slate-600">Terima kasih atas partisipasi aktif Anda pada agenda ini. Hak e-sertifikat dapat diakses setelah kegiatan selesai.</small>
                </div>
            </div>
        <?php elseif ($isPresensiOpen): ?>
            <div class="alert alert-warning p-3 radius-12 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 border border-warning shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <iconify-icon icon="solar:bell-bing-bold" class="text-3xl text-warning flex-shrink-0"></iconify-icon>
                    <div>
                        <strong class="text-slate-900 d-block">Sesi Presensi Kehadiran Sedang Dibuka!</strong>
                        <small class="text-slate-700">Panitia telah membuka sesi presensi digital. Masukkan kode kehadiran melalui Ruang Peserta Anda.</small>
                    </div>
                </div>
                <div>
                    <a href="<?= site_url('workspace/peserta/event/' . $event['slug']) ?>" class="btn btn-warning btn-sm radius-8 fw-bold px-3 py-2 text-dark d-inline-flex align-items-center gap-1 shadow-sm">
                        <iconify-icon icon="solar:key-bold"></iconify-icon>
                        <span>Isi Presensi di Workspace</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left: Event Information & Dynamic Sections -->
        <div class="col-lg-8">
            <article class="evt-detail-card">
                <!-- Cover Banner -->
                <div class="rounded-3 overflow-hidden mb-4" style="max-height: 420px; background: var(--evt-navy-dark);">
                    <?php if (! empty($event['cover_path'])): ?>
                        <img src="<?= base_url($event['cover_path']) ?>"
                            alt="<?= esc($event['title']) ?>"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="p-5 text-center text-white" style="background: linear-gradient(135deg, #052244 0%, #082f5c 50%, #0c5aa6 100%);">
                            <iconify-icon icon="solar:calendar-date-bold" style="font-size: 64px; color: var(--evt-gold); margin-bottom: 16px;"></iconify-icon>
                            <span class="badge bg-warning text-dark text-xs fw-bold px-3 py-1 radius-6 mb-2"><?= esc($typeLabel) ?></span>
                            <h3 class="fw-bold text-white mb-2"><?= esc($event['title']) ?></h3>
                            <p class="text-slate-300 text-xs mb-0">Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Summary Callout -->
                <?php if (! empty($event['summary'])): ?>
                    <div class="p-3 bg-light border-start border-4 border-primary radius-8 mb-4">
                        <strong class="d-block text-slate-900 mb-1 fw-bold">Ikhtisar &amp; Urgensi Kegiatan:</strong>
                        <p class="text-slate-700 mb-0" style="line-height: 1.6;"><?= esc($event['summary']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Full Description Body -->
                <h3 class="h5 fw-bold text-slate-900 mb-3">Deskripsi &amp; Latar Belakang</h3>
                <div class="text-slate-700 mb-4" style="line-height: 1.8; font-size: 15px;">
                    <?= nl2br(esc($event['description'] ?: 'Kegiatan pembinaan, sosialisasi regulasi, dan peningkatan kompetensi pengadaan barang dan jasa Pemerintah Provinsi Lampung.')) ?>
                </div>

                <!-- Susunan Acara (Rundown Kegiatan) - Strictly Real from Event Timestamps -->
                <h3 class="h5 fw-bold text-slate-900 mb-2">Susunan Acara (Rundown Kegiatan)</h3>
                <p class="text-xs text-slate-500 mb-3">Jadwal resmi pelaksanaan sesi agenda sesuai ketetapan panitia penyelenggara.</p>
                <table class="evt-rundown-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Waktu (WIB)</th>
                            <th>Sesi &amp; Agenda Pelaksanaan</th>
                            <th>Keterangan / Fasilitator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?= date('H:i', $startTime) ?> WIB</strong></td>
                            <td>
                                <strong>Pembukaan Acara &amp; Registrasi Peserta</strong>
                                <small class="text-slate-500 d-block mt-1">Verifikasi identitas dan penyambutan peserta <?= esc($typeLabel) ?></small>
                            </td>
                            <td>Panitia Biro PBJ Setda Lampung</td>
                        </tr>
                        <tr>
                            <td><strong><?= date('H:i', $startTime + 1800) ?> WIB</strong></td>
                            <td>
                                <strong>Pemaparan Materi Pokok &amp; Pembahasan Tematik</strong>
                                <small class="text-slate-500 d-block mt-1"><?= esc($event['title']) ?></small>
                            </td>
                            <td>Narasumber &amp; Tim Fasilitator Ahli PBJ</td>
                        </tr>
                        <tr>
                            <td><strong><?= date('H:i', max($startTime + 3600, $endTime - 2700)) ?> WIB</strong></td>
                            <td>
                                <strong>Sesi Tanya Jawab, Diskusi &amp; Evaluasi Kasus</strong>
                                <small class="text-slate-500 d-block mt-1">Konsultasi interaktif langsung bersama fasilitator</small>
                            </td>
                            <td>Moderator &amp; Seluruh Peserta</td>
                        </tr>
                        <tr>
                            <td><strong><?= date('H:i', $endTime) ?> WIB</strong></td>
                            <td>
                                <strong>Rangkuman Hasil, Penutupan &amp; Validasi Presensi</strong>
                                <small class="text-slate-500 d-block mt-1">Perekaman rekam jejak kehadiran dan penerbitan sertifikat</small>
                            </td>
                            <td>Penyelenggara Resmi BegawiPengadaan</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Narasumber & Fasilitator Ahli - Dynamic to Event Creator & Official Bureau -->
                <h3 class="h5 fw-bold text-slate-900 mb-3">Narasumber &amp; Fasilitator Ahli</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light border radius-12 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 18px;">
                                <iconify-icon icon="solar:user-speak-bold"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-slate-900"><?= esc($event['creator_name']) ?></h6>
                                <small class="text-slate-600">Penyelenggara Resmi Biro PBJ Lampung</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light border radius-12 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 48px; height: 48px; font-size: 18px;">
                                <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-slate-900">Instruktur &amp; Narasumber PBJ Terakreditasi</h6>
                                <small class="text-slate-600">Fasilitator Berstandar Nasional LKPP</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Peserta - Dynamically reflects dashboard fields -->
                <h3 class="h5 fw-bold text-slate-900 mb-3">Fasilitas Peserta</h3>
                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <?php if (! empty($event['certificate_enabled'])): ?>
                                <iconify-icon icon="solar:diploma-verified-bold" class="text-success text-xl"></iconify-icon>
                                <span>E-Sertifikat Digital Terverifikasi QR (TTE)</span>
                            <?php else: ?>
                                <iconify-icon icon="solar:document-bold" class="text-secondary text-xl"></iconify-icon>
                                <span>Surat Keterangan Keikutsertaan Resmi</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:document-text-bold" class="text-primary text-xl"></iconify-icon>
                            <span>Bahan Pembelajaran &amp; Materi Tayang Digital</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <?php if ($event['mode'] === 'online'): ?>
                                <iconify-icon icon="solar:videocamera-record-bold" class="text-info text-xl"></iconify-icon>
                                <span>Akses Video Conference Interaktif (Zoom)</span>
                            <?php elseif ($event['mode'] === 'offline'): ?>
                                <iconify-icon icon="solar:city-bold" class="text-primary text-xl"></iconify-icon>
                                <span>Ruang Pertemuan Representatif di Lokasi</span>
                            <?php else: ?>
                                <iconify-icon icon="solar:laptop-minimalistic-bold" class="text-warning text-xl"></iconify-icon>
                                <span>Akses Fleksibel Hybrid (Daring &amp; Luring)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:chat-round-dots-bold" class="text-warning text-xl"></iconify-icon>
                            <span>Konsultasi &amp; Tanya Jawab Langsung Ahli PBJ</span>
                        </div>
                    </div>
                </div>

                <!-- Social Share Bar -->
                <div class="d-flex align-items-center justify-content-between p-3 bg-light border radius-12 flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:share-circle-bold" class="text-primary text-xl"></iconify-icon>
                        <span class="text-sm fw-bold text-slate-800">Bagikan Kegiatan Ini:</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($event['title'] . ' ' . $shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-wa" title="Bagikan ke WhatsApp">
                            <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                        </a>
                        <a href="https://telegram.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($event['title']) ?>" target="_blank" class="evt-share-btn evt-share-tg" title="Bagikan ke Telegram">
                            <iconify-icon icon="logos:telegram"></iconify-icon>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($event['title']) ?>&url=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-tw" title="Bagikan ke X">
                            <iconify-icon icon="ri:twitter-x-fill"></iconify-icon>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-fb" title="Bagikan ke Facebook">
                            <iconify-icon icon="logos:facebook"></iconify-icon>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-in" title="Bagikan ke LinkedIn">
                            <iconify-icon icon="logos:linkedin-icon"></iconify-icon>
                        </a>
                        <button type="button" class="evt-share-btn evt-share-copy" id="btnCopyLink" title="Salin Tautan Kegiatan">
                            <iconify-icon icon="solar:copy-bold"></iconify-icon>
                        </button>
                    </div>
                </div>
            </article>

            <!-- Related Upcoming Events -->
            <?php if (! empty($relatedEvents)): ?>
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="h5 fw-bold text-slate-900 mb-0">Agenda PBJ Terkait Lainnya</h4>
                        <a href="<?= site_url('event') ?>" class="text-xs fw-bold text-primary text-decoration-none">Lihat Semua Kegiatan &rarr;</a>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($relatedEvents as $rel): ?>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border radius-12 h-100 d-flex flex-column shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-primary-subtle text-primary text-xs text-uppercase fw-bold"><?= esc($rel['type']) ?></span>
                                        <?php if (! empty($rel['certificate_enabled'])): ?>
                                            <span class="badge bg-success-subtle text-success text-xs">Sertifikat</span>
                                        <?php endif; ?>
                                    </div>
                                    <h6 class="fw-bold mb-2">
                                        <a href="<?= site_url('event/' . $rel['slug']) ?>" class="text-slate-900 text-decoration-none">
                                            <?= esc($rel['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-slate-500 mt-auto d-block">
                                        <iconify-icon icon="solar:calendar-date-bold" class="text-primary"></iconify-icon> <?= date('d M Y', strtotime($rel['start_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Registration Action Box -->
        <div class="col-lg-4">
            <div class="evt-register-widget">
                <span class="badge bg-primary text-white text-xs fw-bold px-2 py-1 mb-2">Pendaftaran Peserta</span>
                <h4 class="h5 fw-bold text-slate-900 mb-1">Status Partisipasi</h4>
                <p class="text-xs text-slate-600 mb-3">Pastikan Anda telah masuk dengan akun BegawiPengadaan untuk konfirmasi otomatis.</p>

                <!-- Quota Counter & Bar -->
                <?php if ($isUnlimited): ?>
                    <div class="p-3 bg-light border radius-10 mb-3">
                        <div class="d-flex align-items-center justify-content-between text-xs font-medium">
                            <span class="text-slate-600">Kapasitas Peserta:</span>
                            <span class="badge bg-success-subtle text-success fw-bold">Terbuka / Tanpa Batas</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Total pendaftar saat ini: <strong><?= $registeredCount ?> orang</strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-between text-xs fw-bold text-slate-700">
                        <span>Terdaftar: <?= $registeredCount ?></span>
                        <span>Kapasitas: <?= $capacity ?></span>
                    </div>
                    <?php
                    $percent = $capacity > 0 ? min(100, round(($registeredCount / $capacity) * 100)) : 0;
                    ?>
                    <div class="evt-seat-bar-wrap">
                        <div class="evt-seat-bar-fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                    <div class="text-xs text-slate-600 mb-4">
                        Sisa Kuota: <strong><?= $remainingSeats ?> kursi</strong> (<?= max(0, 100 - $percent) ?>% tersisa)
                    </div>
                <?php endif; ?>

                <!-- Registration Decision Box -->
                <?php if ($isRegistered): ?>
                    <div class="p-3 bg-success-subtle border border-success-subtle radius-10 mb-3 text-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-3xl text-success d-block mb-1 mx-auto"></iconify-icon>
                        <strong class="text-success d-block mb-1">Anda Sudah Terdaftar!</strong>
                        <p class="text-xs text-slate-700 mb-0">Status: <span class="badge bg-success text-white text-uppercase"><?= esc($userRegData['status'] ?? 'registered') ?></span></p>
                    </div>

                    <?php if (! empty($event['meeting_url']) && ! $isPast): ?>
                        <a href="<?= esc($event['meeting_url']) ?>" target="_blank" class="btn btn-primary w-100 py-2 radius-10 mb-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <iconify-icon icon="solar:videocamera-record-bold" class="text-lg"></iconify-icon>
                            <span>Buka Ruang Pertemuan (Zoom)</span>
                        </a>
                    <?php endif; ?>
                <?php elseif ($isPast): ?>
                    <div class="p-3 bg-secondary-subtle border radius-10 mb-3 text-center">
                        <strong class="text-slate-700 d-block">Kegiatan Telah Selesai</strong>
                        <small class="text-slate-600">Pendaftaran untuk kegiatan ini telah ditutup.</small>
                    </div>
                <?php elseif (! $isRegOpen): ?>
                    <div class="p-3 bg-warning-subtle border radius-10 mb-3 text-center">
                        <strong class="text-warning d-block">Pendaftaran Ditutup</strong>
                        <small class="text-slate-700">
                            <?php if ($regOpenAt && time() < $regOpenAt): ?>
                                Pendaftaran akan dibuka pada <?= date('d M Y H:i', $regOpenAt) ?> WIB.
                            <?php else: ?>
                                Batas waktu pendaftaran telah berakhir.
                            <?php endif; ?>
                        </small>
                    </div>
                <?php else: ?>
                    <form action="<?= site_url('event/' . $event['slug'] . '/daftar') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if ($isUnlimited || $remainingSeats > 0): ?>
                            <button type="submit" class="btn btn-primary w-100 py-3 radius-10 fw-bold d-flex align-items-center justify-content-center gap-2 mb-2 shadow-sm">
                                <span>Daftar Sekarang (Gratis)</span>
                                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                            </button>
                            <small class="text-xs text-center text-slate-500 d-block">Konfirmasi instan ke akun terdaftar Anda.</small>
                        <?php else: ?>
                            <?php if (! empty($event['waitlist_enabled'])): ?>
                                <button type="submit" class="btn btn-warning w-100 py-3 radius-10 fw-bold text-dark d-flex align-items-center justify-content-center gap-2 mb-2 shadow-sm">
                                    <span>Daftar Antrean (Waitlist)</span>
                                    <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                                </button>
                                <small class="text-xs text-center text-slate-600 d-block">Kuota utama penuh. Anda akan otomatis dipromosikan bila ada pembatalan.</small>
                            <?php else: ?>
                                <button type="button" disabled class="btn btn-secondary w-100 py-3 radius-10 fw-bold mb-2">
                                    <span>Kuota Telah Penuh</span>
                                </button>
                                <small class="text-xs text-center text-danger d-block">Mohon maaf, antrian waitlist tidak dibuka untuk agenda ini.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-xs text-uppercase text-slate-500 mb-2">Akses &amp; Lokasi Acara:</h6>
                    <p class="text-xs text-slate-700 mb-2">
                        <iconify-icon icon="solar:map-point-bold" class="text-danger"></iconify-icon>
                        <strong><?= esc($event['location'] ?: ($event['mode'] === 'online' ? 'Daring via Zoom Cloud Meetings' : 'Pemerintah Provinsi Lampung')) ?></strong>
                    </p>
                    <?php if (! empty($event['meeting_url']) && ! $isRegistered): ?>
                        <div class="p-2 bg-light border radius-8 text-xs text-slate-600 mb-2 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:lock-bold" class="text-warning"></iconify-icon>
                            <span>Tautan pertemuan daring terbuka untuk peserta terdaftar.</span>
                        </div>
                    <?php endif; ?>
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?= urlencode($event['title']) ?>&dates=<?= date('Ymd\THis', $startTime) ?>/<?= date('Ymd\THis', $endTime) ?>&details=<?= urlencode($event['summary'] ?: $event['title']) ?>&location=<?= urlencode($event['location'] ?: 'Lampung') ?>"
                        target="_blank" class="btn btn-sm btn-outline-secondary w-100 radius-8 fw-semibold mt-1">
                        <iconify-icon icon="solar:calendar-add-bold"></iconify-icon> Tambahkan ke Google Calendar
                    </a>
                </div>
            </div>

            <!-- Organizer Info Card -->
            <div class="p-4 bg-light border radius-18 shadow-sm">
                <span class="badge bg-primary text-white text-xs fw-bold px-2 py-1 mb-2">Penyelenggara Resmi</span>
                <h6 class="fw-bold text-slate-900 mb-1">Biro Pengadaan Barang dan Jasa</h6>
                <p class="text-xs text-slate-600 mb-3" style="line-height: 1.6;">
                    Sekretariat Daerah Provinsi Lampung, Kompleks Kantor Gubernur Lampung Jl. Wolter Monginsidi No.69, Bandar Lampung.
                </p>
                <div class="text-xs text-slate-700">
                    <span class="d-block mb-1">Email: <a href="mailto:biropbj@lampungprov.go.id" class="text-primary fw-semibold text-decoration-none">biropbj@lampungprov.go.id</a></span>
                    <span>Helpdesk Pengadaan: <strong class="text-slate-900">+62 851-1777-9783</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Link Toast Feedback -->
<div id="copyToast">
    <iconify-icon icon="solar:check-circle-bold" class="text-success text-xl"></iconify-icon>
    <span>Tautan acara berhasil disalin ke clipboard!</span>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy link handler
        var btnCopy = document.getElementById('btnCopyLink');
        var toast = document.getElementById('copyToast');
        if (btnCopy) {
            btnCopy.addEventListener('click', function() {
                var url = '<?= $shareUrl ?>';
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function() {
                        showToast();
                    }).catch(function() {
                        fallbackCopy(url);
                    });
                } else {
                    fallbackCopy(url);
                }
            });
        }

        function showToast() {
            if (toast) {
                toast.style.display = 'flex';
                setTimeout(function() {
                    toast.style.display = 'none';
                }, 3000);
            }
        }

        function fallbackCopy(text) {
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            try {
                document.execCommand('copy');
                showToast();
            } catch (err) {
                alert('Salin tautan: ' + text);
            }
            document.body.removeChild(tempInput);
        }

        // Countdown timer handler
        var timerContainer = document.getElementById('countdownTimer');
        if (timerContainer) {
            var targetSec = parseInt(timerContainer.getAttribute('data-target'), 10) * 1000;
            var elDays = document.getElementById('cdDays');
            var elHours = document.getElementById('cdHours');
            var elMinutes = document.getElementById('cdMinutes');
            var elSeconds = document.getElementById('cdSeconds');

            function updateCountdown() {
                var now = new Date().getTime();
                var diff = targetSec - now;

                if (diff <= 0) {
                    if (elDays) elDays.innerText = '00';
                    if (elHours) elHours.innerText = '00';
                    if (elMinutes) elMinutes.innerText = '00';
                    if (elSeconds) elSeconds.innerText = '00';
                    return;
                }

                var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (elDays) elDays.innerText = String(days).padStart(2, '0');
                if (elHours) elHours.innerText = String(hours).padStart(2, '0');
                if (elMinutes) elMinutes.innerText = String(minutes).padStart(2, '0');
                if (elSeconds) elSeconds.innerText = String(seconds).padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Bulletproof zero-jitter clamping at scroll extremities for ultra-smooth scrolling
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
    });
</script>
<?= $this->endSection() ?>