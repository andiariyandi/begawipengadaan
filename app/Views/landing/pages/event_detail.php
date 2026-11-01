<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- MAIN DETAIL SECTION — Following Landing News Clean Benchmark -->
<section class="berita-main-section evt-detail-section">
    <div class="container evt-detail-container">
        <!-- Breadcrumb Navigation (Clean & Minimalist Matching Berita) -->
        <nav class="news-detail-breadcrumb-clean mb-3" aria-label="Breadcrumb">
            <a href="<?= site_url('/') ?>"><i class="fas fa-home"></i> Beranda</a>
            <span class="sep">/</span>
            <a href="<?= site_url('event') ?>">Event &amp; Agenda PBJ</a>
            <span class="sep">/</span>
            <a href="<?= site_url('event?tipe=' . urlencode($event['type'])) ?>" class="badge bg-primary text-white text-uppercase px-2 py-1 radius-6 text-xs fw-bold"><?= esc($typeLabel) ?></a>
            <span class="sep">/</span>
            <span class="current text-truncate"><?= esc(mb_strimwidth($event['title'] ?? '', 0, 52, '...')) ?></span>
        </nav>

        <!-- TOP EVENT COVER IMAGE — "yang paling atas adalah gambar dari event, buat clean" -->
        <div class="evt-detail-cover mb-4">
            <div class="evt-detail-cover__media">
                <?php if (! empty($coverUrl)): ?>
                    <img src="<?= esc($coverUrl) ?>" alt="<?= esc($event['title']) ?>" loading="eager" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>';">
                <?php elseif (! empty($event['cover_path'])): ?>
                    <img src="<?= base_url($event['cover_path']) ?>" alt="<?= esc($event['title']) ?>" loading="eager" onerror="this.onerror=null;this.src='<?= base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>';">
                <?php else: ?>
                    <img src="<?= base_url('assets/main/images/blog/blog-details-img-1.jpg') ?>" alt="<?= esc($event['title']) ?>" loading="eager">
                <?php endif; ?>
                <div class="evt-detail-cover__badges">
                    <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($typeLabel) ?></span>
                    <span class="badge bg-success text-white px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($modeLabel) ?></span>
                    <span class="badge bg-primary text-white px-3 py-1 radius-6 text-xs fw-bold text-uppercase"><?= esc($statusLabel) ?></span>
                    <?php if (! empty($event['certificate_enabled'])): ?>
                        <span class="badge bg-info text-white px-3 py-1 radius-6 text-xs fw-bold text-uppercase d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                            <span>E-Sertifikat Tersedia</span>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="evt-detail-cover__caption">
                    <iconify-icon icon="solar:map-point-bold" class="text-danger text-base"></iconify-icon>
                    <span><?= esc($event['location'] ?: ($event['mode'] === 'online' ? 'Daring via Zoom / Platform Online' : 'Pemerintah Provinsi Lampung')) ?></span>
                </div>
            </div>
        </div>

        <!-- Clean Event Title & Meta Bar -->
        <h1 class="evt-detail-title-clean">
            <?= esc($event['title']) ?>
        </h1>

        <div class="evt-detail-meta-clean">
            <div class="meta-item">
                <iconify-icon icon="solar:calendar-date-bold" class="text-warning text-lg"></iconify-icon>
                <strong><?= date('l, d F Y', $startTime) ?></strong>
            </div>
            <span class="meta-divider d-none d-sm-inline">&middot;</span>
            <div class="meta-item">
                <iconify-icon icon="solar:clock-circle-bold" class="text-info text-lg"></iconify-icon>
                <span><?= date('H:i', $startTime) ?> - <?= date('H:i', $endTime) ?> WIB</span>
            </div>
            <span class="meta-divider d-none d-sm-inline">&middot;</span>
            <div class="meta-item">
                <iconify-icon icon="solar:map-point-bold" class="text-danger text-lg"></iconify-icon>
                <span><?= esc($event['location'] ?: ($event['mode'] === 'online' ? 'Daring' : 'Pemerintah Provinsi Lampung')) ?></span>
            </div>
            <span class="meta-divider d-none d-md-inline">&middot;</span>
            <div class="meta-item ms-md-auto">
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
            <div class="evt-countdown-card mb-4">
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

        <!-- 2-COLUMN MAIN CONTENT GRID -->
        <div class="row g-4">
            <!-- Left: Event Information & Content Details -->
            <div class="col-lg-8">
                <article class="evt-detail-card">
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
                        <?= nl2br(esc($event['description'] ?: 'Kegiatan pembinaan, sosialisasi, dan peningkatan wawasan serta pengetahuan pengadaan barang dan jasa Pemerintah Provinsi Lampung.')) ?>
                    </div>

                    <!-- Susunan Acara (Rundown Kegiatan) - Dinamis Berbasis Jam -->
                    <h3 class="h5 fw-bold text-slate-900 mb-2">Susunan Acara (Rundown Kegiatan)</h3>
                    <p class="text-xs text-slate-500 mb-3">Jadwal resmi pelaksanaan sesi agenda sesuai ketetapan panitia penyelenggara (berbasis jam pelaksanaan).</p>
                    <table class="evt-rundown-table mb-4">
                        <thead>
                            <tr>
                                <th style="width: 155px;">Waktu (WIB)</th>
                                <th>Sesi &amp; Agenda Pelaksanaan</th>
                                <th>Keterangan / Fasilitator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($rundownItems)): ?>
                                <?php foreach ($rundownItems as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary fw-bold font-monospace px-2 py-1" style="font-size: 13px;">
                                                <iconify-icon icon="solar:clock-circle-bold" class="me-1 align-middle"></iconify-icon>
                                                <?= esc($item['start_time']) ?><?= ! empty($item['end_time']) ? ' - ' . esc($item['end_time']) : '' ?> WIB
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-slate-900 d-block"><?= esc($item['title']) ?></strong>
                                            <?php if (! empty($item['description'])): ?>
                                                <small class="text-slate-600 d-block mt-1" style="line-height: 1.5;"><?= nl2br(esc($item['description'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-slate-700 fw-medium d-inline-flex align-items-center gap-1">
                                                <iconify-icon icon="solar:user-circle-bold" class="text-slate-400"></iconify-icon>
                                                <?= esc($item['speaker'] ?: 'Panitia Penyelenggara') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-slate-500 py-3">Susunan acara akan segera diperbarui oleh panitia penyelenggara.</td>
                                </tr>
                            <?php endif; ?>
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
                                    <small class="text-slate-600">Fasilitator Berkompeten UKPBJ Provinsi Lampung</small>
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
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($event['title'] . ' ' . $shareUrl) ?>" target="_blank" rel="noopener noreferrer" class="evt-share-btn evt-share-wa" title="Bagikan ke WhatsApp">
                                <iconify-icon icon="logos:whatsapp-icon"></iconify-icon>
                            </a>
                            <a href="https://telegram.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($event['title']) ?>" target="_blank" rel="noopener noreferrer" class="evt-share-btn evt-share-tg" title="Bagikan ke Telegram">
                                <iconify-icon icon="logos:telegram"></iconify-icon>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($event['title']) ?>&url=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener noreferrer" class="evt-share-btn evt-share-tw" title="Bagikan ke X">
                                <iconify-icon icon="ri:twitter-x-fill"></iconify-icon>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener noreferrer" class="evt-share-btn evt-share-fb" title="Bagikan ke Facebook">
                                <iconify-icon icon="logos:facebook"></iconify-icon>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener noreferrer" class="evt-share-btn evt-share-in" title="Bagikan ke LinkedIn">
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

            <!-- Right: Registration Action Box & Host Info -->
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
                        <a href="<?= site_url('workspace/peserta/event-saya/' . $event['slug']) ?>" class="btn btn-outline-primary w-100 py-2 radius-10 mb-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <iconify-icon icon="solar:folder-with-files-bold" class="text-lg"></iconify-icon>
                            <span>Buka Event di Workspace</span>
                        </a>
                    <?php elseif ($isPast && ! empty($isSelfPacedOpen)): ?>
                        <div class="p-3 bg-primary-subtle border border-primary-subtle radius-10 mb-3 text-start">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-white text-xs px-2 py-1 radius-4">Mode Pembelajaran Mandiri</span>
                            </div>
                            <strong class="text-primary-900 d-block font-semibold mb-1" style="font-size: 13px;">Kegiatan Dibuka untuk Akses Mandiri</strong>
                            <p class="text-xs text-slate-700 mb-0">Sesi tatap muka telah selesai, namun kegiatan diperpanjang untuk belajar mandiri melalui rekaman video pemateri, materi tayang, serta asesmen untuk memperoleh E-Sertifikat resmi.</p>
                        </div>
                        <form action="<?= site_url('event/' . $event['slug'] . '/daftar') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary w-100 py-3 radius-10 fw-bold d-flex align-items-center justify-content-center gap-2 mb-2 shadow-sm">
                                <span>Daftar Pembelajaran Mandiri</span>
                                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                            </button>
                            <small class="text-xs text-center text-slate-500 d-block">Gratis &bull; Akses video rekaman &amp; sertifikat mandiri.</small>
                        </form>
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

                <!-- Organizer Info Widget (Consistent with Berita Redaksi Widget) -->
                <div class="berita-widget">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-bold" style="font-size: 11.5px;">PENYELENGGARA RESMI</span>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px; line-height: 1.35;">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</h6>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.6;">
                        Sekretariat Daerah Provinsi Lampung, Kompleks Kantor Gubernur Lampung Jl. Wolter Monginsidi No.69, Bandar Lampung.
                    </p>
                    <div class="text-xs text-muted mb-3">
                        <div class="mb-1"><i class="fas fa-envelope text-primary me-1"></i> <a href="mailto:biropbj@lampungprov.go.id" class="text-primary fw-semibold text-decoration-none">biropbj@lampungprov.go.id</a></div>
                        <div><i class="fas fa-headset text-success me-1"></i> Helpdesk: <strong class="text-dark">+62 851-1777-9783</strong></div>
                    </div>
                    <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-bold" style="font-size: 13px;">
                        <i class="fas fa-envelope-open-text me-1"></i> Kontak PPID Biro PBJ
                    </a>
                </div>
            </div>
        </div>

        <!-- Closing CTA Banner (Matching Berita Detail Style) -->
        <div class="detail-cta mt-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="detail-cta__eyebrow">
                        <i class="fas fa-calendar-check"></i> Pembinaan Berkelanjutan
                    </span>
                    <h3 class="detail-cta__title text-white">Butuh Fasilitasi Bimbingan Teknis atau Klinik Pengadaan?</h3>
                    <p class="detail-cta__lead text-white" style="opacity: 0.92;">
                        Tim Pembinaan dan Advokasi Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung siap memberikan pendampingan teknis dan fasilitasi bagi seluruh Organisasi Perangkat Daerah dan pelaku ekosistem pengadaan.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="detail-cta__actions">
                        <a href="<?= site_url('kontak') ?>" class="detail-cta__btn detail-cta__btn--primary">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Hubungi Kami</span>
                        </a>
                        <a href="<?= site_url('event') ?>" class="detail-cta__btn detail-cta__btn--ghost">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Lihat Agenda Lain</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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