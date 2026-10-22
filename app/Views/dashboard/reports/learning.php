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

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'learning']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Ringkasan analitik pembelajaran, partisipasi, dan capaian peserta diklat PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <select class="form-select form-select-sm w-auto radius-8" onchange="location.href='?year=' + this.value">
            <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>Tahun 2026</option>
            <option value="2025" <?= $year === 2025 ? 'selected' : '' ?>>Tahun 2025</option>
            <option value="2024" <?= $year === 2024 ? 'selected' : '' ?>>Tahun 2024</option>
        </select>
        <a href="<?= site_url('dashboard/laporan/pembelajaran?export=csv&year=' . $year) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center">
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
                        <p class="fw-medium text-primary-light mb-1">Kelas Aktif</p>
                        <h6 class="mb-0"><?= $fmt($active_classes ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-2-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Kelas terbuka & berjalan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Peserta</p>
                        <h6 class="mb-0"><?= $fmt($total_participants ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Peserta terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Tingkat Penyelesaian</p>
                        <h6 class="mb-0"><?= esc((string) ($completion_rate ?? 0)) ?>%</h6>
                    </div>
                    <span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:chart-2-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Rata-rata seluruh kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Lulus Evaluasi</p>
                        <h6 class="mb-0"><?= $fmt($passed_evaluation ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Peserta memenuhi passing grade</p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mt-1">
    <div class="col-xxl-8">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
                    <div>
                        <h6 class="text-lg mb-0">Tren Partisipasi Pembelajaran</h6>
                        <p class="text-secondary-light text-sm mb-0 mt-4">Jumlah peserta pembelajaran per bulan pada tahun <?= $year ?>.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                        Tahun <?= $year ?>
                    </span>
                </div>
                <div id="learningReportChart" class="pt-16"></div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Status Pembelajaran</h6>
                <div id="learningStatusChart"></div>
                <div class="d-flex flex-column gap-12 mt-16">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light"><span class="w-10-px h-10-px bg-primary-600 rounded-circle d-inline-block me-6"></span>Selesai</span>
                        <strong><?= $fmt($status_donut['series'][0] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light"><span class="w-10-px h-10-px bg-warning-main rounded-circle d-inline-block me-6"></span>Berlangsung</span>
                        <strong><?= $fmt($status_donut['series'][1] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light"><span class="w-10-px h-10-px bg-neutral-400 rounded-circle d-inline-block me-6"></span>Belum Mulai</span>
                        <strong><?= $fmt($status_donut['series'][2] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
            <div>
                <h6 class="text-lg mb-0">Performa Kelas Pelatihan</h6>
                <p class="text-secondary-light text-sm mb-0 mt-4">Rekapitulasi partisipasi dan tingkat kelulusan tiap kelas.</p>
            </div>
            <a href="<?= site_url('dashboard/laporan/pembelajaran?export=csv&year=' . $year) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon> Unduh CSV Kelas
            </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Kategori</th>
                        <th>Peserta</th>
                        <th>Penyelesaian</th>
                        <th>Nilai Rata-rata</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light py-20">Belum ada data performa kelas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><h6 class="text-md fw-medium mb-0"><?= esc($class['name']) ?></h6></td>
                                <td><?= esc($class['category']) ?></td>
                                <td><?= esc((string) $class['participants']) ?></td>
                                <td><?= esc((string) $class['completion']) ?></td>
                                <td><?= esc((string) $class['score']) ?></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => strtolower($class['status']) === 'aktif' ? 'active' : 'approved', 'label' => $class['status']]) ?></td>
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
    const learningReportTarget = document.querySelector('#learningReportChart');
    if (learningReportTarget && window.ApexCharts) {
        new ApexCharts(learningReportTarget, {
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            series: [{ name: 'Peserta', data: <?= json_encode($monthly_trend['data'] ?? [0,0,0,0,0,0]) ?> }],
            colors: ['#487FFF'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($monthly_trend['categories'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 }
        }).render();
    }
    const learningStatusTarget = document.querySelector('#learningStatusChart');
    if (learningStatusTarget && window.ApexCharts) {
        new ApexCharts(learningStatusTarget, {
            chart: { type: 'donut', height: 245 },
            series: <?= json_encode($status_donut['series'] ?? [0,0,0]) ?>,
            labels: <?= json_encode($status_donut['labels'] ?? ['Selesai', 'Berlangsung', 'Belum Mulai']) ?>,
            colors: ['#487FFF', '#FF9F29', '#98A2B3'],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: { pie: { donut: { size: '68%' } } }
        }).render();
    }
</script>
<?= $this->endSection() ?>
