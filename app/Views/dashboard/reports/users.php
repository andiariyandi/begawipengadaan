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

<?= view('dashboard/reports/_nav_reports', ['activeReport' => 'users']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Pengguna') ?></h6>
        <p class="text-secondary-light mb-0">Demografi pengguna, sebaran tipe profil ASN/Penyedia, pertumbuhan akun, dan sebaran unit kerja.</p>
    </div>
    <div class="d-flex gap-12 align-items-center">
        <select class="form-select form-select-sm w-auto radius-8" onchange="location.href='?year=' + this.value">
            <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>Tahun 2026</option>
            <option value="2025" <?= $year === 2025 ? 'selected' : '' ?>>Tahun 2025</option>
            <option value="2024" <?= $year === 2024 ? 'selected' : '' ?>>Tahun 2024</option>
        </select>
        <a href="<?= site_url('dashboard/laporan/pengguna?export=csv&year=' . $year) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center">
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
                        <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                        <h6 class="mb-0"><?= $fmt($total_users ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Akun terdaftar di sistem</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Pengguna Aktif</p>
                        <h6 class="mb-0"><?= $fmt($active_users ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-check-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Akun berstatus aktif</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Pengguna Baru</p>
                        <h6 class="mb-0"><?= $fmt($new_users ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-plus-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Dalam 30 hari terakhir</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Akun Non-Aktif / Draf</p>
                        <h6 class="mb-0"><?= $fmt($inactive_users ?? 0) ?></h6>
                    </div>
                    <span class="w-50-px h-50-px bg-danger-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:user-cross-rounded-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Menunggu verifikasi / suspend</p>
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
                        <h6 class="text-lg mb-0">Pertumbuhan Pengguna Terdaftar</h6>
                        <p class="text-secondary-light text-sm mt-4 mb-0">Pengguna baru yang terdaftar per bulan pada tahun <?= $year ?>.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                        Tahun <?= $year ?>
                    </span>
                </div>
                <div id="userGrowthChart" class="pt-16"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Komposisi Profil Pengguna</h6>
                <div id="userRoleChart"></div>
                <div class="d-flex flex-column gap-12 mt-16">
                    <?php foreach (($profile_labels ?? []) as $idx => $pLabel): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary-light"><?= esc($pLabel) ?></span>
                            <strong><?= $fmt($profile_series[$idx] ?? 0) ?></strong>
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
            <h6 class="text-lg mb-0">Sebaran Pengguna per Unit Kerja / OPD</h6>
            <a href="<?= site_url('dashboard/laporan/pengguna?export=csv&year=' . $year) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon> Unduh CSV Pengguna
            </a>
        <button type="button" class="btn btn-outline-secondary radius-8 px-16 py-11 d-inline-flex align-items-center gap-1 d-print-none" onclick="window.print()">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon> Cetak Laporan
        </button>
        </div>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Unit Organisasi / Instansi</th>
                        <th>Total Pengguna</th>
                        <th>Aktif</th>
                        <th>Tidak Aktif</th>
                        <th>Registrasi Baru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($opd_users)): ?>
                        <tr><td colspan="5" class="text-center text-secondary-light py-20">Belum ada data sebaran unit organisasi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($opd_users as $u): ?>
                            <tr>
                                <td><h6 class="text-md fw-medium mb-0"><?= esc($u['unit']) ?></h6></td>
                                <td><?= esc((string) $u['total']) ?></td>
                                <td><span class="text-success-main fw-semibold"><?= esc((string) $u['active']) ?></span></td>
                                <td><span class="text-danger-main fw-semibold"><?= esc((string) $u['inactive']) ?></span></td>
                                <td><?= esc((string) $u['new']) ?></td>
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
    const userGrowthTarget = document.querySelector('#userGrowthChart');
    if (userGrowthTarget && window.ApexCharts) {
        new ApexCharts(userGrowthTarget, {
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            series: [{ name: 'Pengguna Baru', data: <?= json_encode($monthly_growth['data'] ?? [0,0,0,0,0,0]) ?> }],
            colors: ['#487FFF'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($monthly_growth['categories'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 }
        }).render();
    }
    const userRoleTarget = document.querySelector('#userRoleChart');
    if (userRoleTarget && window.ApexCharts) {
        new ApexCharts(userRoleTarget, {
            chart: { type: 'donut', height: 245 },
            series: <?= json_encode(array_values($profile_series ?? [1, 0, 0])) ?>,
            labels: <?= json_encode(array_values($profile_labels ?? ['ASN', 'Penyedia', 'Umum'])) ?>,
            colors: ['#487FFF', '#16A34A', '#FF9F29', '#7F56D9'],
            dataLabels: { enabled: false },
            legend: { show: false }
        }).render();
    }
</script>
<?= $this->endSection() ?>
