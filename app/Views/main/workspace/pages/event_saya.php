<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header Section -->
<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-calendar-alt"></i> <?= esc($sectionContent['eyebrow'] ?? 'Kegiatan & Pelatihan') ?></span>
            <h1><?= esc($pageTitle ?? 'Event Saya') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Daftar seminar, workshop, webinar, dan bimbingan teknis yang Anda ikuti.') ?></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Cari Event Baru: HANYA eksplorasi yang diarahkan ke Landing Page Event Publik -->
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-search me-1"></i> Cari Event Baru
            </a>
        </div>
    </div>
</div>

<!-- Stat KPI Cards -->
<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-2 g-md-3 mb-3">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-1" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- FORM PRESENSI CEPAT (TETAP DI WORKSPACE) -->
<div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-3 bg-white">
    <div class="row align-items-center g-3">
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(8,47,92,0.08); color: #082f5c;">
                    <i class="fas fa-qrcode fs-5"></i>
                </div>
                <div>
                    <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">Presensi Kehadiran</h2>
                    <small class="text-muted">Masukkan kode kehadiran yang dibagikan oleh panitia/penyelenggara</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Pastikan Anda telah mendaftar pada event dan sesi presensi sedang dibuka oleh penyelenggara.</p>
        </div>
        <div class="col-lg-6">
            <form action="<?= route_to('workspace.participant.attendance.checkin') ?>" method="post" class="d-flex flex-column flex-sm-row gap-2">
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-key"></i></span>
                    <input type="text" name="attendance_code" class="form-control text-uppercase font-monospace border-start-0" placeholder="Kode Presensi (Contoh: BGW-EVT-82K91)" required autocomplete="off" style="letter-spacing: 1px;">
                </div>
                <button type="submit" class="btn btn-primary px-3 rounded-3 text-nowrap fw-semibold">
                    <i class="fas fa-check-circle me-1"></i> Konfirmasi Kehadiran
                </button>
            </form>
        </div>
    </div>
</div>

<!-- NAVIGASI 4 FUNGSI UTAMA EVENT SAYA -->
<div class="card border-0 shadow-sm rounded-4 p-2 p-md-3 mb-3 bg-white">
    <ul class="nav nav-pills gap-2 flex-wrap" id="eventTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-3 py-1-5 small fw-semibold" id="tab-aktif-btn" data-bs-toggle="pill" data-bs-target="#tab-aktif" type="button" role="tab" aria-controls="tab-aktif" aria-selected="true">
                <i class="fas fa-calendar-check me-1"></i> 1. Event Aktif Saya (<?= count($activeEvents ?? []) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1-5 small fw-semibold" id="tab-riwayat-btn" data-bs-toggle="pill" data-bs-target="#tab-riwayat" type="button" role="tab" aria-controls="tab-riwayat" aria-selected="false">
                <i class="fas fa-history me-1"></i> 2. Riwayat Event (<?= count($historyEvents ?? []) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1-5 small fw-semibold" id="tab-presensi-btn" data-bs-toggle="pill" data-bs-target="#tab-presensi" type="button" role="tab" aria-controls="tab-presensi" aria-selected="false">
                <i class="fas fa-user-check me-1"></i> 3. Presensi Saya (<?= count($attendanceEvents ?? []) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1-5 small fw-semibold" id="tab-sertifikat-btn" data-bs-toggle="pill" data-bs-target="#tab-sertifikat" type="button" role="tab" aria-controls="tab-sertifikat" aria-selected="false">
                <i class="fas fa-award me-1"></i> 4. Sertifikat Event (<?= count($certificateEvents ?? []) ?>)
            </button>
        </li>
    </ul>
</div>

<!-- TAB CONTENTS -->
<div class="tab-content" id="eventTabContent">
    <!-- TAB 1: EVENT AKTIF SAYA -->
    <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel" aria-labelledby="tab-aktif-btn">
        <?php if (empty($activeEvents)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <i class="fas fa-calendar-check fs-2 text-muted opacity-50 mb-2"></i>
                <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Tidak Ada Event Aktif</h2>
                <p class="text-muted small mb-3">Anda tidak memiliki event yang sedang berjalan atau akan datang saat ini.</p>
                <div>
                    <a href="<?= site_url('event') ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-1"></i> Jelajahi &amp; Daftar Event Baru
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-2 g-md-3">
                <?php foreach ($activeEvents as $ev): ?>
                    <?= view('main/workspace/pages/partials/event_card', ['ev' => $ev]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 2: RIWAYAT EVENT -->
    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel" aria-labelledby="tab-riwayat-btn">
        <?php if (empty($historyEvents)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white text-center">
                <i class="fas fa-history fs-2 text-muted opacity-50 mb-2"></i>
                <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Riwayat Event</h2>
                <p class="text-muted small mb-0">Event yang telah selesai atau telah Anda hadiri akan tersimpan di sini sebagai arsip.</p>
            </div>
        <?php else: ?>
            <div class="row g-2 g-md-3">
                <?php foreach ($historyEvents as $ev): ?>
                    <?= view('main/workspace/pages/partials/event_card', ['ev' => $ev]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 3: PRESENSI SAYA -->
    <div class="tab-pane fade" id="tab-presensi" role="tabpanel" aria-labelledby="tab-presensi-btn">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Daftar Kehadiran Event</h2>
                    <small class="text-muted">Riwayat presensi resmi yang telah Anda submit</small>
                </div>
                <a href="<?= route_to('workspace.participant.attendance') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-external-link-alt me-1"></i> Buka Presensi Saya Lengkap
                </a>
            </div>

            <?php if (empty($attendanceEvents)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-check fs-2 text-muted opacity-50 mb-2"></i>
                    <p class="mb-0 small">Belum ada catatan presensi event yang tercatat untuk akun Anda.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted">Judul Event</th>
                                <th class="small text-uppercase text-muted">Waktu Pelaksanaan</th>
                                <th class="small text-uppercase text-muted">Status Kehadiran</th>
                                <th class="small text-uppercase text-muted">Waktu Check-in</th>
                                <th class="small text-uppercase text-muted text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceEvents as $att): ?>
                                <tr>
                                    <td>
                                        <strong class="d-block" style="color: #082f5c;"><?= esc($att['title']) ?></strong>
                                        <small class="text-muted"><i class="fas fa-tag me-1"></i> <?= esc(strtoupper($att['type'] ?? 'EVENT')) ?></small>
                                    </td>
                                    <td>
                                        <span class="d-block text-dark"><?= esc(date('d M Y', strtotime($att['start_at']))) ?></span>
                                        <small class="text-muted"><?= esc(date('H:i', strtotime($att['start_at']))) ?> WIB</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i> Hadir (Terverifikasi)
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="far fa-clock me-1"></i>
                                        <?= !empty($att['attendance_recorded_at']) ? esc(date('d/m/Y H:i', strtotime($att['attendance_recorded_at']))) . ' WIB' : '-' ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= route_to('workspace.participant.event.detail', (string) $att['event_id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            Detail Event
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB 4: SERTIFIKAT EVENT -->
    <div class="tab-pane fade" id="tab-sertifikat" role="tabpanel" aria-labelledby="tab-sertifikat-btn">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Sertifikat Kegiatan Terbit</h2>
                    <small class="text-muted">Sertifikat resmi dari event dan webinar yang telah Anda selesaikan</small>
                </div>
                <a href="<?= route_to('workspace.participant.certificates') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-award me-1"></i> Buka Semua Sertifikat
                </a>
            </div>

            <?php if (empty($certificateEvents)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-award fs-2 text-muted opacity-50 mb-2"></i>
                    <p class="mb-1 small">Belum ada sertifikat event yang diterbitkan.</p>
                    <small class="text-muted">Pastikan Anda telah mengisi presensi pada kegiatan yang menyediakan sertifikat.</small>
                </div>
            <?php else: ?>
                <div class="row g-2 g-md-3">
                    <?php foreach ($certificateEvents as $cev): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 border bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-semibold mb-2">
                                        <i class="fas fa-award me-1"></i> Sertifikat Resmi
                                    </span>
                                    <h3 class="h6 fw-bold mb-2" style="color: #082f5c;"><?= esc($cev['title']) ?></h3>
                                    <div class="p-2 bg-white rounded-3 border mb-3">
                                        <small class="text-muted d-block">Nomor Dokumen:</small>
                                        <code class="fw-bold text-dark fs-6"><?= esc($cev['certificate_number']) ?></code>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <a href="<?= route_to('workspace.participant.certificate.download', $cev['certificate_id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> Unduh PDF
                                    </a>
                                    <a href="<?= route_to('workspace.participant.event.detail', (string) $cev['event_id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
