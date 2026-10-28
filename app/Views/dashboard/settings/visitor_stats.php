<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/settings/_nav_settings', ['activeSetting' => 'visitor_stats']) ?>

<!-- Flash Message Notifications -->
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show radius-8 mb-24 d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
        <div><?= session()->getFlashdata('message') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show radius-8 mb-24 d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl"></iconify-icon>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Statistik Trafik & Kunjungan Portal') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan sesuaikan angka statistik pengunjung beranda serta 5 modul fitur unggulan.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?= site_url('/#visitor-stats') ?>" target="_blank" class="btn btn-outline-primary radius-8 px-16 py-9 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-right-up-linear" class="text-lg"></iconify-icon>
            <span>Pratinjau Landing Page</span>
        </a>
        <button type="button" class="btn btn-outline-danger radius-8 px-16 py-9 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#resetDataModal">
            <iconify-icon icon="solar:trash-bin-trash-bold" class="text-lg"></iconify-icon>
            <span>Opsi Reset Data</span>
        </button>
    </div>
</div>

<!-- Info Banner: IP Tracking Mode -->
<div class="card border-0 radius-12 bg-primary-50 border border-primary-100 p-20 mb-24">
    <div class="d-flex align-items-start gap-3">
        <div class="w-44-px h-44-px rounded-circle bg-primary-600 text-white d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
            <iconify-icon icon="solar:shield-check-bold" class="text-2xl"></iconify-icon>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h6 class="text-md fw-bold text-primary-900 mb-0">Metode Perekaman Aktif: Berbasis Keunikan IP Client (Unique Daily IP Visits)</h6>
                <span class="badge bg-success text-white px-2 py-1 radius-4 text-2xs fw-semibold">
                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>Aktif & Terproteksi
                </span>
            </div>
            <p class="text-xs text-primary-800 mb-2" style="max-width: 900px; line-height: 1.5;">
                Sistem secara otomatis memfilter lonjakan hit berulang dari perangkat yang sama. Satu alamat IP Client hanya dihitung <strong>maksimal 1 kali per hari per fitur</strong>. Kunjungan atau refresh ulang pada hari yang sama tidak akan mendistorsi statistik.
            </p>
            <div class="d-flex align-items-center gap-3 text-xs text-primary-700 flex-wrap">
                <span class="d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:laptop-minimalistic-bold"></iconify-icon>
                    IP Anda Saat Ini: <strong class="badge bg-white text-primary border border-primary-200"><?= esc($currentIp) ?></strong>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                    IP Unik Hari Ini: <strong class="text-primary-light"><?= number_format($ipSummary['unique_today'] ?? 0) ?></strong>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:calendar-mark-bold"></iconify-icon>
                    IP Unik Bulan Ini: <strong class="text-primary-light"><?= number_format($ipSummary['unique_month'] ?? 0) ?></strong>
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:database-bold"></iconify-icon>
                    Total Log IP Terdata: <strong class="text-primary-light"><?= number_format($ipSummary['total_logs'] ?? 0) ?></strong>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- FORM 1: TRAFIK KUNJUNGAN BERANDA (HOME) - TERISOLASI MANDIRI -->
<form method="post" action="<?= site_url('dashboard/sistem/statistik-kunjungan') ?>" id="formHomeStats">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="home">

    <!-- CARD 1: TRAFIK KUNJUNGAN BERANDA (HOME) -->
    <div class="card radius-12 border-0 shadow-none border mb-24" id="card-home">
        <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="w-32-px h-32-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:home-2-bold" class="text-lg"></iconify-icon>
                </span>
                <div>
                    <h6 class="text-sm fw-bold text-primary-light mb-0">1. Trafik Kunjungan Beranda (Halaman Utama)</h6>
                    <p class="text-2xs text-secondary-light mb-0">Sesuaikan total akumulasi dan sebaran interval waktu pada kartu beranda landing page</p>
                </div>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-xs fw-bold px-2 py-1 radius-4">
                Total Beranda: <?= number_format($stats['home']['total'] ?? 0) ?> Kali Dibuka
            </span>
        </div>

        <div class="card-body p-24">
            <div class="row g-3">
                <!-- Total Akumulasi -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8 d-flex align-items-center gap-1">
                        <span>Total Akumulasi Pembukaan</span>
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-base text-secondary-light"><iconify-icon icon="solar:fire-bold"></iconify-icon></span>
                        <input type="number" name="home_total" class="form-control radius-end-8 fw-bold text-primary" min="0" value="<?= esc($stats['home']['total'] ?? 0) ?>" required>
                    </div>
                    <small class="text-secondary-light text-2xs mt-1 d-block">Angka utama kartu beranda landing page ("kali dibuka")</small>
                </div>

                <!-- Hari Ini -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Hari Ini</label>
                    <div class="input-group">
                        <span class="input-group-text bg-base text-secondary-light"><iconify-icon icon="solar:sun-2-bold"></iconify-icon></span>
                        <input type="number" name="home_today" class="form-control radius-end-8" min="0" value="<?= esc($stats['home']['today'] ?? 0) ?>" required>
                    </div>
                    <small class="text-secondary-light text-2xs mt-1 d-block">Kunjungan per hari ini</small>
                </div>

                <!-- Minggu Ini -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Minggu Ini</label>
                    <div class="input-group">
                        <span class="input-group-text bg-base text-secondary-light"><iconify-icon icon="solar:calendar-linear"></iconify-icon></span>
                        <input type="number" name="home_week" class="form-control radius-end-8" min="0" value="<?= esc($stats['home']['this_week'] ?? 0) ?>" required>
                    </div>
                    <small class="text-secondary-light text-2xs mt-1 d-block">Senin s/d hari ini</small>
                </div>

                <!-- Bulan Ini -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bulan Ini</label>
                    <div class="input-group">
                        <span class="input-group-text bg-base text-secondary-light"><iconify-icon icon="solar:calendar-mark-bold"></iconify-icon></span>
                        <input type="number" name="home_month" class="form-control radius-end-8" min="0" value="<?= esc($stats['home']['this_month'] ?? 0) ?>" required>
                    </div>
                    <small class="text-secondary-light text-2xs mt-1 d-block">Tanggal 1 s/d hari ini</small>
                </div>

                <!-- Tahun Ini -->
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Ini</label>
                    <div class="input-group">
                        <span class="input-group-text bg-base text-secondary-light"><iconify-icon icon="solar:chart-square-bold"></iconify-icon></span>
                        <input type="number" name="home_year" class="form-control radius-end-8" min="0" value="<?= esc($stats['home']['this_year'] ?? 0) ?>" required>
                    </div>
                    <small class="text-secondary-light text-2xs mt-1 d-block">Tahun kalender <?= date('Y') ?></small>
                </div>
            </div>

            <div class="alert alert-neutral-50 border border-neutral-200 radius-8 p-12 mt-16 mb-0 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:info-circle-linear" class="text-primary text-base flex-shrink-0"></iconify-icon>
                <span class="text-2xs text-secondary">
                    <strong>Catatan Hierarki:</strong> Sistem secara otomatis memastikan konsistensi: <code>Hari Ini &le; Minggu Ini &le; Bulan Ini &le; Tahun Ini &le; Total Akumulasi</code>. Nilai yang Anda simpan akan terdistribusi rapi pada catatan log harian.
                </span>
            </div>
        </div>

        <div class="card-footer bg-base border-top py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="text-2xs text-secondary-light">
                <iconify-icon icon="solar:shield-check-bold" class="align-middle me-1 text-primary"></iconify-icon>
                Menyimpan bagian ini <strong>hanya memperbarui Trafik Beranda</strong>. Frekuensi 5 fitur unggulan tetap terisolasi dan tidak tersentuh.
            </span>
            <button type="submit" class="btn btn-primary radius-8 px-24 py-10 d-flex align-items-center gap-2"
                    data-confirm="Simpan pembaruan angka statistik Trafik Kunjungan Beranda secara presisten? Data 5 fitur unggulan tidak akan berubah."
                    data-title="Konfirmasi Simpan Trafik Beranda"
                    data-type="primary"
                    data-btn-text="Ya, Simpan Trafik Beranda"
                    data-icon="solar:disk-bold">
                <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                <span>Simpan Trafik Beranda</span>
            </button>
        </div>
    </div>
</form>

<!-- FORM 2: FREKUENSI AKSES 5 FITUR UNGGULAN - TERISOLASI MANDIRI -->
<form method="post" action="<?= site_url('dashboard/sistem/statistik-kunjungan') ?>" id="formFeaturesStats">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="features">

    <!-- CARD 2: FREKUENSI AKSES 5 FITUR UNGGULAN -->
    <div class="card radius-12 border-0 shadow-none border mb-24" id="card-features">
        <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="w-32-px h-32-px rounded-circle bg-success-50 text-success-600 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:chart-2-bold" class="text-lg"></iconify-icon>
                </span>
                <div>
                    <h6 class="text-sm fw-bold text-primary-light mb-0">2. Frekuensi Akses 5 Fitur Unggulan Portal</h6>
                    <p class="text-2xs text-secondary-light mb-0">Kelola angka pembukaan modul yang ditampilkan pada kolom kedua landing page</p>
                </div>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle text-xs fw-bold px-2 py-1 radius-4">
                Total 5 Fitur: <?= number_format($stats['total_feature_hits'] ?? 0) ?> Akses
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-base border-bottom text-xs text-secondary-light">
                        <tr>
                            <th class="ps-24 py-12" style="width: 35%;">Modul / Fitur Portal</th>
                            <th class="py-12" style="width: 25%;">Tayang Hari Ini</th>
                            <th class="py-12" style="width: 25%;">Total Tayang Akumulasi</th>
                            <th class="pe-24 py-12 text-end" style="width: 15%;">Pangsa Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php if (! empty($stats['features'])): ?>
                            <?php foreach ($stats['features'] as $fKey => $fData): ?>
                                <tr>
                                    <td class="ps-24 py-16">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="background: <?= esc($fData['bg']) ?>; color: <?= esc($fData['color']) ?>;">
                                                <iconify-icon icon="<?= esc($fData['icon']) ?>" class="text-lg"></iconify-icon>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary-light"><?= esc($fData['label']) ?></div>
                                                <div class="text-2xs text-secondary-light text-truncate" style="max-width: 260px;"><?= esc($fData['description']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-16">
                                        <div class="input-group input-group-sm" style="max-width: 180px;">
                                            <span class="input-group-text bg-base text-success text-2xs fw-bold">+</span>
                                            <input type="number" name="features[<?= esc($fKey) ?>][today]" class="form-control radius-end-8 text-sm" min="0" value="<?= esc($fData['hits_today'] ?? 0) ?>" required>
                                            <span class="input-group-text bg-base text-secondary-light text-2xs">hari ini</span>
                                        </div>
                                    </td>
                                    <td class="py-16">
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <input type="number" name="features[<?= esc($fKey) ?>][total]" class="form-control radius-start-8 fw-semibold text-sm" min="0" value="<?= esc($fData['hits_total'] ?? 0) ?>" required>
                                            <span class="input-group-text bg-base text-secondary-light text-2xs">tayang</span>
                                        </div>
                                    </td>
                                    <td class="pe-24 py-16 text-end">
                                        <span class="badge bg-light text-primary-light border px-2 py-1 text-xs fw-bold">
                                            <?= esc($fData['percentage'] ?? 0) ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-base border-top py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="text-2xs text-secondary-light">
                <iconify-icon icon="solar:shield-check-bold" class="align-middle me-1 text-success-600"></iconify-icon>
                Menyimpan bagian ini <strong>hanya memperbarui Frekuensi 5 Fitur Unggulan</strong>. Angka Trafik Beranda tetap terisolasi dan tidak berubah.
            </span>
            <button type="submit" class="btn btn-success-600 text-white radius-8 px-24 py-10 d-flex align-items-center gap-2"
                    data-confirm="Simpan pembaruan angka Frekuensi Akses 5 Fitur Unggulan secara presisten? Data trafik kunjungan beranda tidak akan berubah."
                    data-title="Konfirmasi Simpan 5 Fitur Unggulan"
                    data-type="success"
                    data-btn-text="Ya, Simpan Fitur Unggulan"
                    data-icon="solar:disk-bold">
                <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                <span>Simpan Frekuensi 5 Fitur Unggulan</span>
            </button>
        </div>
    </div>
</form>

<!-- CARD 3: LOG REKAMAN IP PENGUNJUNG TERAKHIR -->
<div class="card radius-12 border-0 shadow-none border">
    <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="w-32-px h-32-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center">
                <iconify-icon icon="solar:history-bold" class="text-lg"></iconify-icon>
            </span>
            <div>
                <h6 class="text-sm fw-bold text-primary-light mb-0">3. Audit Log Riwayat Kunjungan IP Client Terkini (Real-Time Tracker)</h6>
                <p class="text-2xs text-secondary-light mb-0">Menampilkan 25 catatan entri IP unik pengunjung terakhir yang terekam sistem</p>
            </div>
        </div>
        <span class="badge bg-info-subtle text-info border border-info-subtle text-2xs px-2 py-1 radius-4">
            Total Log: <?= number_format($ipSummary['total_logs'] ?? 0) ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-base border-bottom text-xs text-secondary-light">
                    <tr>
                        <th class="ps-24 py-12" style="width: 18%;">Waktu Akses</th>
                        <th class="py-12" style="width: 18%;">Alamat IP Client</th>
                        <th class="py-12" style="width: 20%;">Fitur yang Dibuka</th>
                        <th class="py-12" style="width: 30%;">User Agent / Peramban</th>
                        <th class="pe-24 py-12 text-end" style="width: 14%;">Status Tracker</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php if (! empty($ipLogs)): ?>
                        <?php foreach ($ipLogs as $log): ?>
                            <tr>
                                <td class="ps-24 py-12 text-xs text-secondary">
                                    <div class="fw-semibold text-primary-light"><?= esc(date('d/m/Y H:i:s', strtotime($log['created_at']))) ?></div>
                                    <small class="text-muted"><?= esc($log['visit_date']) ?></small>
                                </td>
                                <td class="py-12">
                                    <span class="badge bg-light text-primary-light border font-monospace text-xs px-2 py-1">
                                        <?= esc($log['ip_address']) ?>
                                    </span>
                                </td>
                                <td class="py-12">
                                    <?php
                                    $badgeClass = match ($log['feature']) {
                                        'home'          => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'arena_belajar' => 'bg-info-subtle text-info border-info-subtle',
                                        'bunga_rampai'  => 'bg-success-subtle text-success border-success-subtle',
                                        'regulasi'      => 'bg-warning-subtle text-warning border-warning-subtle',
                                        'berita'        => 'bg-danger-subtle text-danger border-danger-subtle',
                                        'event'         => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                        default         => 'bg-light text-primary-light',
                                    };
                                    ?>
                                    <span class="badge border <?= $badgeClass ?> px-2 py-1 text-2xs fw-bold">
                                        <?= esc(ucwords(str_replace('_', ' ', $log['feature']))) ?>
                                    </span>
                                </td>
                                <td class="py-12 text-2xs text-secondary-light text-truncate" style="max-width: 320px;" title="<?= esc($log['user_agent'] ?? '—') ?>">
                                    <?= esc($log['user_agent'] ?: '—') ?>
                                </td>
                                <td class="pe-24 py-12 text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs px-2 py-1 radius-4 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Terhitung (1 Hit)
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-24 text-secondary-light text-sm">
                                <iconify-icon icon="solar:database-linear" class="text-3xl text-muted mb-2 d-block mx-auto"></iconify-icon>
                                Belum ada log IP pengunjung yang tercatat pada sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Opsi Reset & Pembersihan Data Pengunjung -->
<div class="modal fade" id="resetDataModal" tabindex="-1" aria-labelledby="resetDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <form method="post" action="<?= site_url('dashboard/sistem/statistik-kunjungan/reset') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title text-base fw-bold text-danger d-flex align-items-center gap-2 mb-0" id="resetDataModalLabel">
                        <iconify-icon icon="solar:trash-bin-trash-bold" class="text-xl"></iconify-icon>
                        Opsi Reset &amp; Pembersihan Data Pengunjung
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-24">
                    <?php
                    $bulanNames = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $currentBulanText = ($bulanNames[(int) date('n')] ?? date('F')) . ' ' . date('Y');
                    ?>

                    <p class="text-sm text-secondary-light mb-20">
                        Gunakan opsi di bawah ini untuk mengatur siklus pembersihan data riwayat pengunjung portal, baik untuk pembersihan rutin bulanan maupun reset counter harian.
                    </p>

                    <!-- Pilihan Cakupan Waktu (Scope) -->
                    <label class="form-label text-sm fw-bold text-primary-light mb-12">
                        1. Pilih Kebijakan Pembersihan Waktu:
                    </label>

                    <div class="row g-3 mb-20">
                        <!-- Opsi 1: Hanya Ambil Data Bulan Ini (Hapus sebelum bulan ini) -->
                        <div class="col-12">
                            <label class="d-flex align-items-start gap-3 p-16 rounded-3 border border-primary bg-primary-50 cursor-pointer">
                                <input type="radio" name="scope" value="before_this_month" class="form-check-input mt-1 flex-shrink-0" checked>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                        <div class="fw-bold text-primary-900 text-sm">
                                            Hanya Ambil Data Bulan Ini (Hapus data bulan-bulan sebelumnya)
                                        </div>
                                        <span class="badge bg-primary text-white text-2xs px-2 py-1 radius-4">Pilihan Utama / Direkomendasikan</span>
                                    </div>
                                    <div class="text-xs text-primary-800" style="line-height: 1.4;">
                                        Menghapus seluruh rekaman statistik dan log IP sebelum tanggal <strong>01 <?= $currentBulanText ?></strong>. Data bulan berjalan (<strong><?= $currentBulanText ?></strong>) dan data hari ini tetap <strong>100% aman dan dipertahankan</strong>.
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 2: Hanya Ambil Data Tahun Ini -->
                        <div class="col-12">
                            <label class="d-flex align-items-start gap-3 p-16 rounded-3 border bg-base hover-bg-neutral-50 cursor-pointer">
                                <input type="radio" name="scope" value="before_this_year" class="form-check-input mt-1 flex-shrink-0">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-primary-light text-sm mb-1">
                                        Hanya Ambil Data Tahun Ini (Hapus riwayat tahun-tahun sebelumnya)
                                    </div>
                                    <div class="text-xs text-secondary-light" style="line-height: 1.4;">
                                        Menghapus seluruh rekaman statistik dan log IP sebelum tanggal <strong>01 Januari <?= date('Y') ?></strong>. Seluruh data tahun berjalan (<?= date('Y') ?>) tetap tersimpan utuh.
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 3: Reset Hit Hari Ini Saja -->
                        <div class="col-12">
                            <label class="d-flex align-items-start gap-3 p-16 rounded-3 border bg-base hover-bg-neutral-50 cursor-pointer">
                                <input type="radio" name="scope" value="today_only" class="form-check-input mt-1 flex-shrink-0">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-primary-light text-sm mb-1">
                                        Reset Hit Hari Ini Saja (Tanggal <?= date('d/m/Y') ?>)
                                    </div>
                                    <div class="text-xs text-secondary-light" style="line-height: 1.4;">
                                        Mengembalikan counter hit hari ini menjadi <code>0</code> serta membersihkan log IP hari ini. Data historis hari-hari sebelumnya sama sekali tidak terpengaruh.
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 4: Reset Total Keseluruhan -->
                        <div class="col-12">
                            <label class="d-flex align-items-start gap-3 p-16 rounded-3 border border-danger-subtle bg-danger-subtle hover-bg-danger-subtle cursor-pointer">
                                <input type="radio" name="scope" value="all_history" class="form-check-input mt-1 flex-shrink-0">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-danger text-sm mb-1 d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:danger-bold"></iconify-icon>
                                        Reset Total Keseluruhan (Mulai dari Nol)
                                    </div>
                                    <div class="text-xs text-danger-emphasis" style="line-height: 1.4;">
                                        Menghapus seluruh riwayat trafik dan seluruh log IP. Semua metrik (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini, dan Total) akan dimulai kembali dari <code>0</code>.
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Pilihan Cakupan Modul / Fitur -->
                    <div class="mb-20">
                        <label class="form-label text-sm fw-bold text-primary-light mb-8">
                            2. Pilih Cakupan Modul:
                        </label>
                        <select name="feature" class="form-select radius-8 text-sm">
                            <option value="all">Semua Modul (Halaman Beranda + 5 Fitur Unggulan)</option>
                            <option value="home">Halaman Beranda (Home) Saja</option>
                            <option value="arena_belajar">Arena Belajar Saja</option>
                            <option value="bunga_rampai">Bunga Rampai PBJ Saja</option>
                            <option value="regulasi">Regulasi PBJ Saja</option>
                            <option value="berita">Berita &amp; Informasi Saja</option>
                            <option value="event">Event &amp; Agenda Saja</option>
                        </select>
                    </div>

                    <!-- Konfirmasi Keamanan -->
                    <div class="alert alert-warning border border-warning-subtle radius-8 p-14 mb-0">
                        <div class="form-check d-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input flex-shrink-0 mt-0" type="checkbox" id="confirmResetCheck" required>
                            <label class="form-check-label text-xs fw-semibold text-warning-emphasis" for="confirmResetCheck">
                                Saya memahami bahwa data statistik historis yang dihapus sesuai cakupan di atas bersifat permanen.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-14 px-24 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger radius-8 px-20 py-9 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:trash-bin-trash-bold" class="text-base"></iconify-icon>
                        <span>Terapkan Pembersihan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
