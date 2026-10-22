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

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'certificates']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">Rekapitulasi penerbitan sertifikat digital, verifikasi keaslian, dan masa berlaku dokumen.</p>
    </div>
    <div class="d-flex gap-12 align-items-center">
        <select class="form-select form-select-sm w-auto radius-8" onchange="location.href='?year=' + this.value">
            <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>Tahun 2026</option>
            <option value="2025" <?= $year === 2025 ? 'selected' : '' ?>>Tahun 2025</option>
            <option value="2024" <?= $year === 2024 ? 'selected' : '' ?>>Tahun 2024</option>
        </select>
        <a href="<?= site_url('dashboard/laporan/sertifikat?export=csv&year=' . $year) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center">
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
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0"><?= $fmt($issued_certs ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Sepanjang tahun <?= $year ?></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Verifikasi</p>
                        <h6 class="mb-0"><?= $fmt($pending_approvals ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Antrean approval admin</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Aktif</p>
                        <h6 class="mb-0"><?= $fmt($active_certs ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Dokumen sah & berlaku</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Akan Berakhir</p>
                        <h6 class="mb-0"><?= $fmt($expiring_soon ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-danger-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:danger-triangle-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Dalam 90 hari mendatang</p>
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
                        <h6 class="text-lg mb-0">Tren Penerbitan Sertifikat</h6>
                        <p class="text-secondary-light text-sm mt-4 mb-0">Jumlah sertifikat diterbitkan setiap bulan pada tahun <?= $year ?>.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                        Tahun <?= $year ?>
                    </span>
                </div>
                <div id="certificateReportChart" class="pt-16"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Status Sertifikat</h6>
                <div class="d-flex flex-column gap-20">
                    <?php
                    $totalC = max(1, ($active_certs ?? 0) + ($pending_approvals ?? 0) + ($expiring_soon ?? 0));
                    $actPct = min(100, round((($active_certs ?? 0) / $totalC) * 100));
                    $pndPct = min(100, round((($pending_approvals ?? 0) / $totalC) * 100));
                    $expPct = min(100, round((($expiring_soon ?? 0) / $totalC) * 100));
                    ?>
                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-secondary-light">Aktif & Sah</span>
                            <strong><?= $fmt($active_certs ?? 0) ?></strong>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-success-main rounded-pill" style="width: <?= $actPct ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-secondary-light">Menunggu Approval</span>
                            <strong><?= $fmt($pending_approvals ?? 0) ?></strong>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-warning-main rounded-pill" style="width: <?= $pndPct ?>%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-secondary-light">Masa Berlaku Kritis (90 Hari)</span>
                            <strong><?= $fmt($expiring_soon ?? 0) ?></strong>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-danger-main rounded-pill" style="width: <?= $expPct ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
            <h6 class="text-lg mb-0">Rekap Sertifikat per Program / Kursus</h6>
            <a href="<?= site_url('dashboard/laporan/sertifikat?export=csv&year=' . $year) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon> Unduh CSV Sertifikat
            </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Program / Kursus</th>
                        <th>Sertifikat Terbit</th>
                        <th>Terverifikasi</th>
                        <th>Menunggu</th>
                        <th>Rata-rata Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programs)): ?>
                        <tr><td colspan="5" class="text-center text-secondary-light py-20">Belum ada data sertifikat program.</td></tr>
                    <?php else: ?>
                        <?php foreach ($programs as $p): ?>
                            <tr>
                                <td><h6 class="text-md fw-medium mb-0"><?= esc($p['program']) ?></h6></td>
                                <td><?= esc((string) $p['issued']) ?></td>
                                <td><span class="text-success-main fw-semibold"><?= esc((string) $p['verified']) ?></span></td>
                                <td><span class="text-warning-main fw-semibold"><?= esc((string) $p['pending']) ?></span></td>
                                <td><?= esc((string) $p['score']) ?></td>
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
    const certificateReportTarget = document.querySelector('#certificateReportChart');
    if (certificateReportTarget && window.ApexCharts) {
        new ApexCharts(certificateReportTarget, {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            series: [{ name: 'Sertifikat Terbit', data: <?= json_encode($monthly_trend['data'] ?? [0,0,0,0,0,0]) ?> }],
            colors: ['#487FFF'],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($monthly_trend['categories'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 }
        }).render();
    }
</script>
<?= $this->endSection() ?>
