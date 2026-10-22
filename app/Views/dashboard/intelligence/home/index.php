<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'home']) ?>
<?php
$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Dashboard Government Intelligence') ?></h6>
        <p class="text-secondary-light mb-0">Intelijen strategis ketersediaan aparatur JF PPBJ dan analisis gap kompetensi pengadaan se-Provinsi Lampung.</p>
    </div>
    <div class="d-flex flex-wrap gap-12 align-items-center">
        <a href="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-8 d-inline-flex align-items-center gap-6">
            <iconify-icon icon="solar:chart-2-bold" class="text-lg"></iconify-icon> Matriks Gap OPD
        </a>
    </div>
</div>

<!-- 4 KPI Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">OPD / Unit Terpetakan</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['total_opd'] ?? 0)) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan text-white rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:buildings-2-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Unit organisasi aktif Pemprov</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">SDM JF PPBJ</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['total_personnel'] ?? 0)) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple text-white rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Pejabat fungsional terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Kompetensi</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['total_certificates'] ?? 0)) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info text-white rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Sertifikat aktif terverifikasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Gap Kebutuhan</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['total_gap'] ?? 0)) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-red text-white rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="text-sm text-primary-light mt-12 mb-0">Kebutuhan formasi belum terpenuhi</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row gy-4 mt-1">
    <!-- Personnel Distribution by Level -->
    <div class="col-xxl-8 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <div>
                        <h6 class="text-lg mb-2">Distribusi Personel per Jenjang Jabatan</h6>
                        <p class="text-secondary-light text-sm mb-0">Struktur kompetensi berjenjang aparatur fungsional PBJ Pemprov Lampung.</p>
                    </div>
                    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="text-sm text-primary-600 fw-semibold">
                        Lihat Personel &rarr;
                    </a>
                </div>
                <div id="intelligencePersonnelChart" class="pt-10"></div>
            </div>
        </div>
    </div>

    <!-- Competency Composition Donut -->
    <div class="col-xxl-4 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-4">Komposisi Standar Kompetensi</h6>
                <p class="text-secondary-light text-sm mb-16">Distribusi jenis sertifikasi keahlian terbit.</p>
                
                <div id="intelligenceCompetencyChart" class="d-flex justify-content-center"></div>

                <ul class="d-flex flex-column gap-12 mt-20 mb-0">
                    <?php 
                    $colors = ['#487FFF', '#16A34A', '#FF9F29', '#EF4A4A', '#8B5CF6'];
                    foreach (($competencyPercentages ?? []) as $idx => $comp): 
                        $cColor = $colors[$idx % count($colors)];
                    ?>
                        <li class="d-flex justify-content-between align-items-center text-sm">
                            <span class="text-secondary-light d-flex align-items-center">
                                <span class="w-10-px h-10-px rounded-circle d-inline-block me-8" style="background-color: <?= $cColor ?>;"></span>
                                <?= esc($comp['name']) ?>
                            </span>
                            <span class="fw-semibold text-primary-light">
                                <?= (int) $comp['count'] ?> (<?= (int) $comp['percent'] ?>%)
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Radar Kematangan PBJ Daerah -->
<div class="card mt-24 mb-4 radius-12 border bg-base shadow-none">
    <div class="card-body p-24">
        <div class="row align-items-center gy-4">
            <div class="col-lg-5">
                <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 text-2xs fw-bold text-uppercase mb-8 d-inline-block">
                    Standar LKPP &bull; Kematangan PBJ Daerah
                </span>
                <h5 class="fw-bold text-primary-light mb-8">Radar Kesiapan 5 Dimensi PBJ</h5>
                <p class="text-secondary-light text-sm mb-16">
                    Penilaian kapabilitas menyeluruh unit pengadaan Pemerintah Provinsi Lampung dalam memastikan integritas, transparansi, dan kecakapan aparatur fungsional.
                </p>
                <div class="d-flex flex-column gap-10 text-xs">
                    <div class="d-flex justify-content-between p-8 radius-6 bg-neutral-50">
                        <span class="text-secondary-light">1. Kualifikasi SDM Bersertifikat:</span>
                        <strong class="text-primary-600">82% (Memadai)</strong>
                    </div>
                    <div class="d-flex justify-content-between p-8 radius-6 bg-neutral-50">
                        <span class="text-secondary-light">2. Keterisian Formasi JF PPBJ:</span>
                        <strong class="text-warning-main">68% (Perlu Penguatan)</strong>
                    </div>
                    <div class="d-flex justify-content-between p-8 radius-6 bg-neutral-50">
                        <span class="text-secondary-light">3. Kepatuhan Regulasi &amp; SOP:</span>
                        <strong class="text-success-main">92% (Sangat Baik)</strong>
                    </div>
                    <div class="d-flex justify-content-between p-8 radius-6 bg-neutral-50">
                        <span class="text-secondary-light">4. Akses Platform Digital:</span>
                        <strong class="text-primary-600">88% (Memadai)</strong>
                    </div>
                    <div class="d-flex justify-content-between p-8 radius-6 bg-neutral-50">
                        <span class="text-secondary-light">5. Responsivitas Helpdesk PBJ:</span>
                        <strong class="text-success-main">85% (Baik)</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div id="pbjMaturityRadarChart" class="d-flex justify-content-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Top Gap Table & Certificate Status -->
<div class="row gy-4 mt-1">
    <!-- Top OPD Needs -->
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-20">
                    <div>
                        <h6 class="text-lg mb-2">OPD dengan Kebutuhan SDM PBJ Tertinggi</h6>
                        <p class="text-secondary-light text-sm mb-0">Prioritas pemenuhan aparatur bersertifikasi berdasarkan gap kuota.</p>
                    </div>
                    <a href="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-6">
                        Buka Matriks Gap
                    </a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>OPD / Unit Kerja</th>
                                <th class="text-center">Standar Kebutuhan</th>
                                <th class="text-center">Tersedia</th>
                                <th class="text-center">Gap Defisit</th>
                                <th class="text-center">Prioritas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topOpdGaps)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-20 text-secondary-light">Data kebutuhan seluruh unit kerja terpenuhi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topOpdGaps as $row): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/intelligence/opd-unit/' . $row['opd_id']) ?>" class="fw-semibold text-primary-light">
                                                <?= esc($row['unit']) ?>
                                            </a>
                                            <span class="text-xs text-secondary-light d-block"><?= esc($row['code']) ?></span>
                                        </td>
                                        <td class="text-center fw-medium"><?= (int) $row['need'] ?> Org</td>
                                        <td class="text-center fw-semibold text-success-main"><?= (int) $row['available'] ?> Org</td>
                                        <td class="text-center fw-bold text-danger-main"><?= (int) $row['gap'] ?> Org</td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', [
                                                'status' => $row['priority_status'],
                                                'label'  => $row['priority'],
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Status Progress -->
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-20">
                    <div>
                        <h6 class="text-lg mb-2">Masa Berlaku Sertifikasi PBJ</h6>
                        <p class="text-secondary-light text-sm mb-0">Pemantauan siklus resertifikasi aparatur.</p>
                    </div>
                    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="text-sm text-primary-600 fw-semibold">
                        Semua Sertifikat &rarr;
                    </a>
                </div>

                <div class="d-flex flex-column gap-24 mt-16">
                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-sm fw-medium text-secondary-light d-flex align-items-center gap-6">
                                <span class="w-8-px h-8-px bg-success-main rounded-circle"></span> Aktif Valid (&gt; 6 Bulan)
                            </span>
                            <span class="text-sm fw-bold text-primary-light"><?= (int) ($certificateStatus['active'] ?? 0) ?> Dok (<?= (int) ($certificateStatus['active_pct'] ?? 0) ?>%)</span>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-success-main rounded-pill" style="width: <?= (int) ($certificateStatus['active_pct'] ?? 0) ?>%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-sm fw-medium text-secondary-light d-flex align-items-center gap-6">
                                <span class="w-8-px h-8-px bg-warning-main rounded-circle"></span> Perlu Resertifikasi (&lt; 6 Bulan)
                            </span>
                            <span class="text-sm fw-bold text-primary-light"><?= (int) ($certificateStatus['expiring'] ?? 0) ?> Dok (<?= (int) ($certificateStatus['expiring_pct'] ?? 0) ?>%)</span>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-warning-main rounded-pill" style="width: <?= (int) ($certificateStatus['expiring_pct'] ?? 0) ?>%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-sm fw-medium text-secondary-light d-flex align-items-center gap-6">
                                <span class="w-8-px h-8-px bg-danger-main rounded-circle"></span> Melewati Masa Berlaku (Kedaluwarsa)
                            </span>
                            <span class="text-sm fw-bold text-primary-light"><?= (int) ($certificateStatus['expired'] ?? 0) ?> Dok (<?= (int) ($certificateStatus['expired_pct'] ?? 0) ?>%)</span>
                        </div>
                        <div class="progress progress-sm rounded-pill">
                            <div class="progress-bar bg-danger-main rounded-pill" style="width: <?= (int) ($certificateStatus['expired_pct'] ?? 0) ?>%;"></div>
                        </div>
                    </div>
                </div>

                <div class="p-16 bg-neutral-50 border border-neutral-200 radius-8 mt-28">
                    <p class="text-xs text-secondary-light mb-0">
                        <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 align-middle"></iconify-icon>
                        Aparatur yang mendekati masa kedaluwarsa sertifikat dapat diprioritaskan pada pelatihan Arena Belajar PBJ.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const personnelTarget = document.querySelector('#intelligencePersonnelChart');
    if (personnelTarget && window.ApexCharts) {
        new ApexCharts(personnelTarget, {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{ name: 'Personel Aktif', data: <?= json_encode($personnelChart['series'] ?? []) ?> }],
            colors: ['#487FFF'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '40%' } },
            dataLabels: { enabled: true, style: { fontSize: '12px' } },
            xaxis: { categories: <?= json_encode($personnelChart['categories'] ?? []) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
            yaxis: { labels: { formatter: val => parseInt(val) } }
        }).render();
    }

    const competencyTarget = document.querySelector('#intelligenceCompetencyChart');
    if (competencyTarget && window.ApexCharts) {
            const radarTarget = document.querySelector('#pbjMaturityRadarChart');
    if (radarTarget && window.ApexCharts) {
        new ApexCharts(radarTarget, {
            chart: { type: 'radar', height: 320, toolbar: { show: false } },
            series: [{
                name: 'Tingkat Kematangan (%)',
                data: [82, 68, 92, 88, 85]
            }],
            labels: [
                'Kualifikasi SDM',
                'Keterisian Formasi',
                'Kepatuhan Regulasi',
                'Akses Platform',
                'Respons Layanan'
            ],
            colors: ['#487FFF'],
            markers: { size: 4 },
            yaxis: { max: 100, min: 0, tickAmount: 5 }
        }).render();
    }

        new ApexCharts(competencyTarget, {
            chart: { type: 'donut', height: 260 },
            series: <?= json_encode($competencyChart['series'] ?? []) ?>,
            labels: <?= json_encode($competencyChart['labels'] ?? []) ?>,
            colors: ['#487FFF', '#16A34A', '#FF9F29', '#EF4A4A', '#8B5CF6'],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: { pie: { donut: { size: '68%' } } }
        }).render();
    }
});
</script>
<?= $this->endSection() ?>
