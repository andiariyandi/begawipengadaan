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
        --evt-slate-700: #334155;
        --evt-slate-500: #64748b;
        --evt-slate-200: #e2e8f0;
        --evt-slate-50: #f8fafc;
    }

    /* Detail Header Banner */
    .evt-detail-header-section {
        background: linear-gradient(135deg, var(--evt-navy-dark) 0%, var(--evt-navy) 70%, var(--evt-blue) 100%);
        padding: 160px 0 60px;
        color: #ffffff;
        position: relative;
    }

    .evt-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: #e2e8f0;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
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
        margin-bottom: 20px;
        letter-spacing: -0.4px;
    }

    .evt-detail-meta-bar {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 14px;
        color: #cbd5e1;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
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
        margin-bottom: 50px;
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
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .evt-countdown-units {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .evt-countdown-unit {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 8px 14px;
        text-align: center;
        min-width: 60px;
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
        text-transform: uppercase;
        color: #94a3b8;
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
        margin: 20px 0 30px;
    }

    .evt-rundown-table th {
        background: #f8fafc;
        padding: 12px 18px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--evt-navy-dark);
        border-bottom: 1px solid var(--evt-slate-200);
    }

    .evt-rundown-table td {
        padding: 14px 18px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .evt-rundown-table tr:last-child td {
        border-bottom: none;
    }

    /* Facilities List */
    .evt-facility-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
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
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s ease;
    }

    .evt-share-btn:hover {
        transform: translateY(-2px);
        color: #ffffff;
    }

    .evt-share-wa { background: #25d366; }
    .evt-share-tg { background: #0088cc; }
    .evt-share-tw { background: #000000; }
    .evt-share-fb { background: #1877f2; }
    .evt-share-in { background: #0077b5; }
    .evt-share-copy { background: #475569; border: none; cursor: pointer; }

    #copyToast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--evt-navy-dark);
        color: #ffffff;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        display: none;
        z-index: 99999;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 991px) {
        .evt-detail-header-section {
            padding: 130px 0 50px;
        }
        .evt-detail-title {
            font-size: 26px;
        }
        .evt-detail-card {
            padding: 24px;
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
            <a href="<?= site_url('event') ?>">Event &amp; Agenda</a>
            <span>/</span>
            <span class="text-white"><?= esc($typeLabel) ?></span>
        </div>

        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($typeLabel) ?></span>
            <span class="badge bg-success-subtle text-success px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($modeLabel) ?></span>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($statusLabel) ?></span>
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
                <span><?= esc($event['location'] ?: 'Online via Zoom') ?></span>
            </div>
            <div class="evt-detail-meta-item ms-lg-auto">
                <iconify-icon icon="solar:users-group-rounded-bold" class="text-success text-lg"></iconify-icon>
                <span>Tersedia: <strong><?= $remainingSeats ?> / <?= $capacity ?> Kuota</strong></span>
            </div>
        </div>
    </div>
</section>

<!-- 2. DETAIL CONTENT & REGISTRATION SIDEBAR -->
<div class="container evt-detail-container">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 radius-12 p-3 mb-4 shadow-sm">
            <iconify-icon icon="solar:check-circle-bold" class="text-2xl text-success flex-shrink-0"></iconify-icon>
            <div><?= session()->getFlashdata('message') ?></div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 radius-12 p-3 mb-4 shadow-sm">
            <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl text-danger flex-shrink-0"></iconify-icon>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <!-- Countdown Timer Card (If Upcoming) -->
    <?php if (! $isPast && ! $isOngoing): ?>
        <div class="evt-countdown-card">
            <div>
                <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-1">Pengingat Waktu</span>
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
        <div class="alert alert-success p-3 radius-12 d-flex align-items-center gap-2 mb-4">
            <iconify-icon icon="solar:broadcast-bold" class="text-2xl text-success"></iconify-icon>
            <strong>Kegiatan Sedang Berlangsung Hari Ini!</strong> Silakan bergabung sesuai tautan atau hadir di lokasi acara.
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left: Event Information & Rundown -->
        <div class="col-lg-8">
            <article class="evt-detail-card">
                <!-- Cover Banner -->
                <div class="rounded-3 overflow-hidden mb-4" style="max-height: 400px; background: #e2e8f0;">
                    <img src="<?= ! empty($event['cover_path']) ? base_url($event['cover_path']) : base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>" 
                         alt="<?= esc($event['title']) ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>

                <!-- Summary Callout -->
                <?php if (! empty($event['summary'])): ?>
                    <div class="p-3 bg-light border-start border-4 border-primary radius-6 mb-4">
                        <strong class="d-block text-dark mb-1">Ikhtisar Kegiatan:</strong>
                        <p class="text-slate-600 mb-0"><?= esc($event['summary']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Full Description Body -->
                <h3 class="h5 fw-bold text-dark mb-3">Deskripsi &amp; Latar Belakang</h3>
                <div class="text-slate-700 mb-4" style="line-height: 1.8;">
                    <?= nl2br(esc($event['description'] ?: 'Informasi teknis dan materi kegiatan pengadaan barang/jasa Pemerintah Provinsi Lampung.')) ?>
                </div>

                <!-- Rundown Table -->
                <h3 class="h5 fw-bold text-dark mb-2">Susunan Acara (Rundown Kegiatan)</h3>
                <table class="evt-rundown-table">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Waktu (WIB)</th>
                            <th>Materi / Sesi Acara</th>
                            <th>Narasumber / Penanggung Jawab</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>08.30 - 09.00</strong></td>
                            <td>Registrasi Peserta &amp; Presensi Digital</td>
                            <td>Panitia Biro PBJ Setda Lampung</td>
                        </tr>
                        <tr>
                            <td><strong>09.00 - 09.30</strong></td>
                            <td>Pembukaan Resmi &amp; Keynote Speech</td>
                            <td>Kepala Biro PBJ Setda Provinsi Lampung</td>
                        </tr>
                        <tr>
                            <td><strong>09.30 - 11.30</strong></td>
                            <td>Pemaparan Materi Pokok &amp; Studi Kasus PBJ</td>
                            <td>Instruktur Ahli LKPP / Narasumber Khusus</td>
                        </tr>
                        <tr>
                            <td><strong>11.30 - 12.15</strong></td>
                            <td>Sesi Tanya Jawab Interaktif &amp; Diskusi Terbuka</td>
                            <td>Moderator &amp; Peserta Terdaftar</td>
                        </tr>
                        <tr>
                            <td><strong>12.15 - 12.30</strong></td>
                            <td>Rangkuman, Pengisian Evaluasi &amp; Penutup</td>
                            <td>Panitia Penyelenggara</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Narasumber & Speakers -->
                <h3 class="h5 fw-bold text-dark mb-3">Narasumber &amp; Fasilitator Ahli</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light border radius-12 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 20px;">
                                LK
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Instruktur Master Trainer LKPP</h6>
                                <small class="text-muted">Lembaga Kebijakan PBJ Pemerintah</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light border radius-12 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 20px;">
                                BP
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Tim Teknis Biro PBJ Lampung</h6>
                                <small class="text-muted">Pemerintah Provinsi Lampung</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities -->
                <h3 class="h5 fw-bold text-dark mb-3">Fasilitas Peserta</h3>
                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:diploma-verified-bold" class="text-success text-xl"></iconify-icon>
                            <span>E-Sertifikat Digital Resmi BegawiPBJ</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:document-text-bold" class="text-primary text-xl"></iconify-icon>
                            <span>Materi &amp; Bahan Tayang Digital (PDF)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:videocamera-record-bold" class="text-info text-xl"></iconify-icon>
                            <span>Rekaman Video Sesi Pembelajaran</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="evt-facility-item">
                            <iconify-icon icon="solar:chat-round-dots-bold" class="text-warning text-xl"></iconify-icon>
                            <span>Grup Komunikasi &amp; Konsultasi Pasca-Acara</span>
                        </div>
                    </div>
                </div>

                <!-- Social Share Bar -->
                <div class="d-flex align-items-center justify-content-between p-3 bg-light border radius-12 flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:share-circle-bold" class="text-primary text-xl"></iconify-icon>
                        <span class="text-sm fw-bold text-dark">Bagikan Kegiatan Ini:</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($event['title'] . ' ' . $shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-wa" title="WhatsApp">
                            <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                        </a>
                        <a href="https://telegram.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($event['title']) ?>" target="_blank" class="evt-share-btn evt-share-tg" title="Telegram">
                            <iconify-icon icon="logos:telegram"></iconify-icon>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($event['title']) ?>&url=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-tw" title="X (Twitter)">
                            <iconify-icon icon="ri:twitter-x-fill"></iconify-icon>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-fb" title="Facebook">
                            <iconify-icon icon="logos:facebook"></iconify-icon>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" target="_blank" class="evt-share-btn evt-share-in" title="LinkedIn">
                            <iconify-icon icon="logos:linkedin-icon"></iconify-icon>
                        </a>
                        <button type="button" class="evt-share-btn evt-share-copy" id="btnCopyLink" title="Salin Tautan">
                            <iconify-icon icon="solar:copy-bold"></iconify-icon>
                        </button>
                    </div>
                </div>
            </article>

            <!-- Related Upcoming Events -->
            <?php if (! empty($relatedEvents)): ?>
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="h5 fw-bold text-dark mb-0">Agenda PBJ Mendatang Lainnya</h4>
                        <a href="<?= site_url('event') ?>" class="text-xs fw-bold text-primary text-decoration-none">Lihat Semua &rarr;</a>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($relatedEvents as $rel): ?>
                            <div class="col-md-4">
                                <div class="p-3 bg-white border radius-12 h-100 d-flex flex-direction-column">
                                    <span class="badge bg-primary-subtle text-primary text-xs text-uppercase mb-2"><?= esc($rel['type']) ?></span>
                                    <h6 class="fw-bold mb-2">
                                        <a href="<?= site_url('event/' . $rel['slug']) ?>" class="text-dark text-decoration-none">
                                            <?= esc($rel['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted mt-auto d-block">
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
                <h4 class="h5 fw-bold text-dark mb-1">Status Partisipasi</h4>
                <p class="text-xs text-muted mb-3">Pastikan Anda telah memiliki akun BegawiPBJ untuk konfirmasi otomatis.</p>

                <!-- Quota Bar -->
                <div class="d-flex align-items-center justify-content-between text-xs fw-bold">
                    <span>Terdaftar: <?= $registeredCount ?></span>
                    <span>Kapasitas: <?= $capacity ?></span>
                </div>
                <?php 
                    $percent = $capacity > 0 ? min(100, round(($registeredCount / $capacity) * 100)) : 0;
                ?>
                <div class="evt-seat-bar-wrap">
                    <div class="evt-seat-bar-fill" style="width: <?= $percent ?>%;"></div>
                </div>
                <div class="text-xs text-slate-500 mb-4">
                    Sisa Kuota: <strong><?= $remainingSeats ?> kursi</strong> (<?= 100 - $percent ?>% tersisa)
                </div>

                <!-- Registration Decision Box -->
                <?php if ($isRegistered): ?>
                    <div class="p-3 bg-success-subtle border border-success-subtle radius-10 mb-3 text-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-3xl text-success d-block mb-1 mx-auto"></iconify-icon>
                        <strong class="text-success d-block mb-1">Anda Sudah Terdaftar!</strong>
                        <p class="text-xs text-muted mb-0">Status: <span class="badge bg-success text-white text-uppercase"><?= esc($userRegData['status'] ?? 'registered') ?></span></p>
                    </div>
                    <?php if (! empty($event['meeting_url']) && ! $isPast): ?>
                        <a href="<?= esc($event['meeting_url']) ?>" target="_blank" class="btn btn-primary w-100 py-2 radius-10 mb-2">
                            <iconify-icon icon="solar:videocamera-record-bold"></iconify-icon> Buka Tautan Pertemuan Zoom
                        </a>
                    <?php endif; ?>
                <?php elseif ($isPast): ?>
                    <div class="p-3 bg-secondary-subtle border radius-10 mb-3 text-center">
                        <strong class="text-secondary d-block">Kegiatan Telah Selesai</strong>
                        <small class="text-muted">Pendaftaran untuk kegiatan ini telah ditutup.</small>
                    </div>
                <?php elseif (! $isRegOpen): ?>
                    <div class="p-3 bg-warning-subtle border radius-10 mb-3 text-center">
                        <strong class="text-warning d-block">Pendaftaran Ditutup</strong>
                        <small class="text-muted">Batas waktu pendaftaran telah berakhir.</small>
                    </div>
                <?php else: ?>
                    <form action="<?= site_url('event/' . $event['slug'] . '/daftar') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if ($remainingSeats > 0): ?>
                            <button type="submit" class="btn btn-primary w-100 py-3 radius-10 fw-bold d-flex align-items-center justify-content-center gap-2 mb-2">
                                <span>Daftar Sekarang (Gratis)</span>
                                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                            </button>
                            <small class="text-xs text-center text-muted d-block">Konfirmasi instan ke akun terdaftar Anda.</small>
                        <?php else: ?>
                            <button type="submit" class="btn btn-warning w-100 py-3 radius-10 fw-bold text-dark d-flex align-items-center justify-content-center gap-2 mb-2">
                                <span>Daftar Antrean (Waitlist)</span>
                                <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                            </button>
                            <small class="text-xs text-center text-muted d-block">Kuota utama penuh. Anda akan dipromosikan jika ada pembatalan.</small>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-xs text-uppercase text-muted mb-2">Lokasi / Tautan Acara:</h6>
                    <p class="text-xs text-slate-700 mb-2">
                        <iconify-icon icon="solar:map-point-bold" class="text-danger"></iconify-icon>
                        <strong><?= esc($event['location'] ?: 'Daring via Zoom Cloud Meetings') ?></strong>
                    </p>
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?= urlencode($event['title']) ?>&dates=<?= date('Ymd\THis', $startTime) ?>/<?= date('Ymd\THis', $endTime) ?>&details=<?= urlencode($event['summary']) ?>&location=<?= urlencode($event['location']) ?>" 
                       target="_blank" class="btn btn-sm btn-outline-secondary w-100 radius-8">
                        <iconify-icon icon="solar:calendar-add-bold"></iconify-icon> Tambahkan ke Google Calendar
                    </a>
                </div>
            </div>

            <!-- Organizer Info Card -->
            <div class="p-4 bg-light border radius-18">
                <h6 class="fw-bold text-dark mb-2">Penyelenggara Resmi:</h6>
                <p class="text-xs text-slate-600 mb-3" style="line-height: 1.6;">
                    Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung, Kompleks Kantor Gubernur Lampung.
                </p>
                <div class="text-xs text-muted">
                    <span class="d-block mb-1">Email: <a href="mailto:biropbj@lampungprov.go.id">biropbj@lampungprov.go.id</a></span>
                    <span>Helpdesk: +62 851-1777-9783</span>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Copy link handler
        const btnCopy = document.getElementById('btnCopyLink');
        const toast = document.getElementById('copyToast');
        if (btnCopy) {
            btnCopy.addEventListener('click', function () {
                const url = '<?= $shareUrl ?>';
                navigator.clipboard.writeText(url).then(function () {
                    if (toast) {
                        toast.style.display = 'flex';
                        setTimeout(function () {
                            toast.style.display = 'none';
                        }, 3000);
                    }
                }).catch(function () {
                    alert('Tautan: ' + url);
                });
            });
        }

        // Countdown timer handler
        const timerContainer = document.getElementById('countdownTimer');
        if (timerContainer) {
            const targetSec = parseInt(timerContainer.getAttribute('data-target'), 10) * 1000;
            const elDays = document.getElementById('cdDays');
            const elHours = document.getElementById('cdHours');
            const elMinutes = document.getElementById('cdMinutes');
            const elSeconds = document.getElementById('cdSeconds');

            function updateCountdown() {
                const now = new Date().getTime();
                const diff = targetSec - now;

                if (diff <= 0) {
                    if (elDays) elDays.innerText = '00';
                    if (elHours) elHours.innerText = '00';
                    if (elMinutes) elMinutes.innerText = '00';
                    if (elSeconds) elSeconds.innerText = '00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (elDays) elDays.innerText = String(days).padStart(2, '0');
                if (elHours) elHours.innerText = String(hours).padStart(2, '0');
                if (elMinutes) elMinutes.innerText = String(minutes).padStart(2, '0');
                if (elSeconds) elSeconds.innerText = String(seconds).padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    });
</script>
<?= $this->endSection() ?>
