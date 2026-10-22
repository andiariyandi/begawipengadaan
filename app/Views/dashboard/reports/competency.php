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

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'competency']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Kompetensi') ?></h6>
        <p class="text-secondary-light mb-0">Rekapitulasi penguasaan kompetensi dan pemetaan kesenjangan (gap) SDM PBJ pada OPD Provinsi Lampung.</p>
    </div>
    <div class="d-flex gap-12 align-items-center">
        <select class="form-select form-select-sm w-auto radius-8" onchange="location.href='?year=' + this.value">
            <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>Tahun 2026</option>
            <option value="2025" <?= $year === 2025 ? 'selected' : '' ?>>Tahun 2025</option>
            <option value="2024" <?= $year === 2024 ? 'selected' : '' ?>>Tahun 2024</option>
        </select>
        <a href="<?= site_url('dashboard/laporan/kompetensi?export=csv&year=' . $year) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center">
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
                        <p class="fw-medium text-primary-light mb-1">SDM Terpetakan</p>
                        <h6 class="mb-0"><?= $fmt($mapped_personnel ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Personel JF / Pengelola PBJ</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terpenuhi</p>
                        <h6 class="mb-0"><?= $fmt($available_competencies ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-star-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Unit kompetensi tervalidasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kesenjangan (Gap)</p>
                        <h6 class="mb-0"><?= $fmt($competency_gap ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:danger-triangle-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Kebutuhan perlu diklat</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">OPD Prioritas</p>
                        <h6 class="mb-0"><?= $fmt($priority_opds ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-danger-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:buildings-2-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Fokus pelatihan daerah</p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mt-1">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
                    <div>
                        <h6 class="text-lg mb-0">Analisis Kebutuhan vs Ketersediaan</h6>
                        <p class="text-secondary-light text-sm mt-4 mb-0">Perbandingan kebutuhan kompetensi pada OPD sampel.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                        Tahun <?= $year ?>
                    </span>
                </div>
                <div id="competencyReportChart" class="pt-16"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Komposisi Domain Kompetensi</h6>
                <div id="competencyDistributionChart"></div>
                <div class="d-flex flex-column gap-12 mt-16">
                    <?php foreach (($comp_labels ?? []) as $idx => $label): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary-light"><?= esc($label) ?></span>
                            <strong><?= $fmt($comp_series[$idx] ?? 0) ?></strong>
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
            <h6 class="text-lg mb-0">Rekomendasi Prioritas Pengembangan OPD</h6>
            <a href="<?= site_url('dashboard/laporan/kompetensi?export=csv&year=' . $year) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon> Unduh CSV Kompetensi
            </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>OPD / Unit Kerja</th>
                        <th>Domain Kompetensi</th>
                        <th>Kebutuhan</th>
                        <th>Tersedia</th>
                        <th>Gap</th>
                        <th class="text-center">Prioritas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($gap_table)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light py-20">Belum ada data analisis kesenjangan OPD.</td></tr>
                    <?php else: ?>
                        <?php foreach ($gap_table as $gap): ?>
                            <tr>
                                <td><h6 class="text-md fw-medium mb-0"><?= esc($gap['unit']) ?></h6></td>
                                <td><?= esc($gap['competency']) ?></td>
                                <td><?= esc((string) $gap['need']) ?></td>
                                <td><?= esc((string) $gap['available']) ?></td>
                                <td><span class="text-danger-main fw-semibold"><?= esc((string) $gap['gap']) ?></span></td>
                                <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $gap['status'], 'label' => $gap['priority']]) ?></td>
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
    const competencyReportTarget = document.querySelector('#competencyReportChart');
    if (competencyReportTarget && window.ApexCharts) {
        <?php
        $chartCategories = [];
        $chartNeed = [];
        $chartAvail = [];
        foreach (array_slice($gap_table ?? [], 0, 5) as $gt) {
            $chartCategories[] = mb_substr($gt['unit'], 0, 16);
            $chartNeed[] = (int) $gt['need'];
            $chartAvail[] = (int) $gt['available'];
        }
        if (empty($chartCategories)) {
            $chartCategories = ['Biro PBJ', 'Dinas PU', 'Dinas Kesehatan', 'Bappeda'];
            $chartNeed = [18, 14, 12, 10];
            $chartAvail = [15, 9, 8, 8];
        }
        ?>
        new ApexCharts(competencyReportTarget, {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Kebutuhan', data: <?= json_encode($chartNeed) ?> },
                { name: 'Tersedia', data: <?= json_encode($chartAvail) ?> }
            ],
            colors: ['#EF4A4A', '#487FFF'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($chartCategories) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' }
        }).render();
    }
    const competencyDistributionTarget = document.querySelector('#competencyDistributionChart');
    if (competencyDistributionTarget && window.ApexCharts) {
        new ApexCharts(competencyDistributionTarget, {
            chart: { type: 'donut', height: 245 },
            series: <?= json_encode(array_values($comp_series ?? [34, 28, 22, 16])) ?>,
            labels: <?= json_encode(array_values($comp_labels ?? ['Perencanaan', 'Pemilihan', 'Kontrak', 'Lainnya'])) ?>,
            colors: ['#487FFF', '#16A34A', '#FF9F29', '#EF4A4A'],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: { pie: { donut: { size: '68%' } } }
        }).render();
    }
</script>
<?= $this->endSection() ?>
