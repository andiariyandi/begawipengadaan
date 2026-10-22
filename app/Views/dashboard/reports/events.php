<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="print-only mb-24 pb-16 border-bottom border-dark text-center">
    <h5 class="fw-bold mb-1 text-uppercase text-primary-light">PEMERINTAH PROVINSI LAMPUNG</h5>
    <h6 class="fw-semibold mb-1 text-uppercase text-primary-light">BIRO PENGADAAN BARANG DAN JASA</h6>
    <p class="text-xs mb-0 text-muted">SISTEM INFORMASI BEGAWIPENGADAAN &bull; LAPORAN EKSEKUTIF RESMI</p>
    <div class="text-2xs text-secondary-light mt-1">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</div>
</div>
<?php
$year = (int) ($year ?? date('Y'));
$fmt = static fn($n): string => number_format((float) $n, 0, ',', '.');
?>

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'events']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Event') ?></h6>
        <p class="text-secondary-light mb-0">Monitoring pelaksanaan event, registrasi pendaftar, dan tingkat kehadiran peserta.</p>
    </div>
    <div class="d-flex gap-12 align-items-center">
        <select class="form-select form-select-sm w-auto radius-8" onchange="location.href='?year=' + this.value">
            <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>Tahun 2026</option>
            <option value="2025" <?= $year === 2025 ? 'selected' : '' ?>>Tahun 2025</option>
            <option value="2024" <?= $year === 2024 ? 'selected' : '' ?>>Tahun 2024</option>
        </select>
        <a href="<?= site_url('dashboard/laporan/event?export=csv&year=' . $year) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center">
            <iconify-icon icon="solar:download-minimalistic-outline" class="me-6 text-lg"></iconify-icon> Export CSV
        </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
    </div>
</div>

<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Event</p>
                        <h6 class="mb-0"><?= $fmt($total_events ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:calendar-date-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Event sepanjang tahun <?= $year ?></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Event Mendatang</p>
                        <h6 class="mb-0"><?= $fmt($upcoming_events ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Menunggu pelaksanaan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Registrasi</p>
                        <h6 class="mb-0"><?= $fmt($total_registrations ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Peserta terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kehadiran Rata-rata</p>
                        <h6 class="mb-0"><?= esc((string) ($avg_attendance_rate ?? 0)) ?>%</h6>
                    </div>
                    <span class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Dari seluruh pendaftar</p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mt-1">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
                    <div>
                        <h6 class="text-lg mb-0">Registrasi dan Kehadiran Event</h6>
                        <p class="text-secondary-light text-sm mt-4 mb-0">Perbandingan registrasi dan kehadiran per bulan.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                        Tahun <?= $year ?>
                    </span>
                </div>
                <div id="eventReportChart" class="pt-16"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Kategori Event</h6>
                <div id="eventCategoryChart"></div>
                <div class="d-flex flex-column gap-12 mt-16">
                    <?php foreach (($cat_breakdown ?? []) as $cb): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary-light"><?= esc($cb['label']) ?></span>
                            <strong><?= $fmt($cb['count']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
            <h6 class="text-lg mb-0">Rekapitulasi Event & Agenda</h6>
            <a href="<?= site_url('dashboard/laporan/event?export=csv&year=' . $year) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon> Unduh CSV Event
            </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Registrasi</th>
                        <th>Hadir</th>
                        <th>Kehadiran</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr><td colspan="7" class="text-center text-secondary-light py-20">Belum ada data event.</td></tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><h6 class="text-md fw-medium mb-0"><?= esc($event['name']) ?></h6></td>
                                <td><?= esc($event['date']) ?></td>
                                <td><?= esc($event['category']) ?></td>
                                <td><?= esc((string) $event['registered']) ?></td>
                                <td><?= esc((string) $event['attended']) ?></td>
                                <td><?= esc((string) $event['rate']) ?></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $event['status'] === 'Selesai' ? 'approved' : 'review', 'label' => $event['status']]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const eventReportTarget = document.querySelector('#eventReportChart');
    if (eventReportTarget && window.ApexCharts) {
        new ApexCharts(eventReportTarget, {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Registrasi', data: <?= json_encode($reg_trend['data'] ?? [0,0,0,0,0,0]) ?> },
                { name: 'Kehadiran', data: <?= json_encode($att_trend['data'] ?? [0,0,0,0,0,0]) ?> }
            ],
            colors: ['#487FFF', '#16A34A'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '52%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($reg_trend['categories'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' }
        }).render();
    }
    const eventCategoryTarget = document.querySelector('#eventCategoryChart');
    if (eventCategoryTarget && window.ApexCharts) {
        new ApexCharts(eventCategoryTarget, {
            chart: { type: 'donut', height: 245 },
            series: <?= json_encode($cat_series ?? [1,1,1]) ?>,
            labels: <?= json_encode($cat_labels ?? ['Pelatihan', 'Webinar', 'Sosialisasi']) ?>,
            colors: ['#487FFF', '#16A34A', '#FF9F29', '#7F56D9'],
            dataLabels: { enabled: false },
            legend: { show: false }
        }).render();
    }
</script>
<?= $this->endSection() ?>
