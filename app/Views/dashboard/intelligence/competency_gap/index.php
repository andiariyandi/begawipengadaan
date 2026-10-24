<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'gap']) ?>
<?php
$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Analisis Gap Kompetensi OPD') ?></h6>
        <p class="text-secondary-light mb-0">Evaluasi komparatif antara standar kebutuhan aparatur pengadaan dan ketersediaan SDM bersertifikasi per unit kerja.</p>
    </div>
    <form method="get" action="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>" class="d-flex align-items-center gap-12">
        <select name="year" class="form-select form-select-sm w-auto radius-8" onchange="this.form.submit()">
            <option value="2026" <?= ($year == 2026) ? 'selected' : '' ?>>Tahun Anggaran 2026</option>
            <option value="2025" <?= ($year == 2025) ? 'selected' : '' ?>>Tahun Anggaran 2025</option>
        </select>
    </form>
    <form method="post" action="<?= site_url('dashboard/intelligence/gap-kompetensi/snapshot') ?>" class="d-inline"><?= csrf_field() ?><input type="hidden" name="year" value="<?= esc((string) ($year ?? 2026)) ?>"><button type="submit" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8 d-inline-flex align-items-center gap-6"><iconify-icon icon="solar:camera-bold"></iconify-icon> Rekam Snapshot Periode Ini</button></form>
</div>

<!-- Top Analytics Chart & Summary -->
<div class="row gy-4">
    <!-- ApexChart Kebutuhan vs Tersedia -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-20">
                    <div>
                        <h6 class="text-lg mb-2">Kebutuhan vs Ketersediaan SDM PBJ (Top OPD)</h6>
                        <p class="text-secondary-light text-sm mb-0">Perbandingan jumlah formasi ideal dengan pejabat fungsional yang tersedia.</p>
                    </div>
                </div>
                <div id="competencyGapChart"></div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics Card -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Ringkasan Pemenuhan Provinsi</h6>
                
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Total Formasi Kebutuhan</span>
                    <strong class="text-primary-light"><?= $fmt((int) ($summary['total_need'] ?? 0)) ?> Orang</strong>
                </div>
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Personel JF PBJ Tersedia</span>
                    <strong class="text-success-main"><?= $fmt((int) ($summary['total_available'] ?? 0)) ?> Orang</strong>
                </div>
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Total Defisit Gap Formasi</span>
                    <strong class="text-danger-main fs-6">-<?= $fmt((int) ($summary['total_gap'] ?? 0)) ?> Orang</strong>
                </div>
                <div class="d-flex justify-content-between mb-24">
                    <span class="text-secondary-light">Tingkat Keterisian (Coverage)</span>
                    <strong class="text-primary-600 fs-6"><?= (int) ($summary['coverage_pct'] ?? 0) ?>%</strong>
                </div>

                <div class="progress progress-sm rounded-pill mb-24">
                    <div class="progress-bar bg-primary-600 rounded-pill" style="width: <?= (int) ($summary['coverage_pct'] ?? 0) ?>%;"></div>
                </div>

                <div class="p-16 bg-neutral-50 border border-neutral-200 radius-8 mb-24">
                    <p class="text-xs text-secondary-light mb-0">
                        <iconify-icon icon="solar:lightbulb-bolt-bold" class="text-warning-main align-middle me-4"></iconify-icon>
                        OPD dengan kategori prioritas <strong>Tinggi</strong> disarankan membuka formasi perpindahan jabatan ke JF PPBJ atau menugaskan staf untuk pelatihan sertifikasi.
                    </p>
                </div>

                <a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-outline-primary-600 w-100 radius-8 py-10">
                    Buka Direktori OPD / Unit
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Simulation: Alokasi & Rekrutmen Formasi JF PPBJ -->
<div class="card mt-24 radius-12 border bg-base shadow-none">
    <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1 text-primary-light">Simulasi Alokasi &amp; Rekrutmen Formasi JF PPBJ</h6>
            <p class="text-secondary-light text-xs mb-0">Kalkulator interaktif proyeksi tingkat pemenuhan formasi aparatur pengadaan se-Provinsi Lampung.</p>
        </div>
        <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 text-xs fw-semibold">Interactive Tool</span>
    </div>
    <div class="card-body p-24">
        <div class="row align-items-center gy-4">
            <div class="col-lg-4">
                <label class="form-label text-xs fw-bold text-secondary-light text-uppercase mb-6">Target Tambahan Aparatur (+Orang):</label>
                <div class="input-group">
                    <input type="number" id="simAddedPersonnel" class="form-control radius-8 font-monospace fw-bold text-center" value="10" min="1" max="200" oninput="runFormationSimulation()">
                    <span class="input-group-text bg-neutral-100 text-xs">Orang</span>
                </div>
                <small class="text-2xs text-secondary-light mt-1 d-block">Simulasikan penambahan kuota melalui inpassing atau rekrutmen CPNS/PPPK.</small>
            </div>
            <div class="col-lg-8">
                <div class="row row-cols-sm-3 row-cols-1 g-3 text-center">
                    <div class="col">
                        <div class="p-16 radius-8 bg-neutral-50 border">
                            <span class="text-2xs text-secondary-light d-block mb-1">PROYEKSI PERSONEL TERSEDIA</span>
                            <h4 class="fw-bold text-success-main mb-0" id="simProjAvailable">-</h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-16 radius-8 bg-neutral-50 border">
                            <span class="text-2xs text-secondary-light d-block mb-1">PROYEKSI SISA GAP DEFISIT</span>
                            <h4 class="fw-bold text-danger-main mb-0" id="simProjGap">-</h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-16 radius-8 bg-primary-50 border border-primary-200">
                            <span class="text-2xs text-primary-600 d-block mb-1">PROYEKSI COVERAGE TINGKAT</span>
                            <h4 class="fw-bold text-primary-600 mb-0" id="simProjCoverage">-</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Matrix Table -->
<div class="card mt-24">
    <div class="card-body p-24">
        <div class="d-flex justify-content-between align-items-center mb-16">
            <div>
                <h6 class="text-lg mb-2">Matriks Kebutuhan dan Gap per OPD</h6>
                <p class="text-secondary-light text-sm mb-0">Daftar lengkap seluruh satuan kerja dengan status prioritas intervensi.</p>
            </div>
            <span class="text-sm text-secondary-light">Menampilkan <?= count($matrix ?? []) ?> unit organisasi</span>
        </div>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="gap-kompetensi-opd" data-page-length="15">
                <thead>
                    <tr>
                        <th>OPD / Unit Kerja</th>
                        <th>Bidang Fokus Kompetensi</th>
                        <th class="text-center">Standar Ideal</th>
                        <th class="text-center">Tersedia</th>
                        <th class="text-center">Gap Defisit</th>
                        <th class="text-center">Prioritas Penanganan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($matrix)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-32 text-secondary-light">Data matriks belum tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($matrix as $row): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('dashboard/intelligence/opd-unit/' . $row['opd_id']) ?>" class="fw-semibold text-primary-light">
                                        <?= esc($row['unit']) ?>
                                    </a>
                                    <span class="text-xs text-secondary-light d-block"><?= esc($row['code']) ?> &bull; <?= esc($row['type']) ?></span>
                                </td>
                                <td>
                                    <span class="text-sm text-primary-light"><?= esc($row['competency']) ?></span>
                                </td>
                                <td class="text-center fw-medium"><?= (int) $row['need'] ?> Org</td>
                                <td class="text-center fw-semibold text-success-main"><?= (int) $row['available'] ?> Org</td>
                                <td class="text-center fw-bold text-danger-main">
                                    <?= $row['gap'] > 0 ? "-{$row['gap']} Org" : 'Lengkap' ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $row['priority_status'],
                                        'label'  => $row['priority'],
                                    ]) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/intelligence/opd-unit/' . $row['opd_id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-10 py-2 text-xs">
                                        Detail
                                    </a>
                                </td>
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
document.addEventListener('DOMContentLoaded', function () {
    const gapTarget = document.querySelector('#competencyGapChart');
    if (gapTarget && window.ApexCharts) {
        new ApexCharts(gapTarget, {
            chart: { type: 'bar', height: 340, toolbar: { show: false } },
            series: [
                { name: 'Standar Kebutuhan', data: <?= json_encode($chart['need'] ?? []) ?> },
                { name: 'Personel Tersedia', data: <?= json_encode($chart['available'] ?? []) ?> }
            ],
            colors: ['#EF4A4A', '#487FFF'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: <?= json_encode($chart['categories'] ?? []) ?> },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' },
            yaxis: { labels: { formatter: val => parseInt(val) } }
        }).render();
    }
});
</script>
<script>
function runFormationSimulation() {
    var baseNeed = <?= (int) ($summary['total_need'] ?? 100) ?>;
    var baseAvail = <?= (int) ($summary['total_available'] ?? 60) ?>;
    var addInput = parseInt(document.getElementById('simAddedPersonnel').value) || 0;

    var projAvail = baseAvail + addInput;
    var projGap = Math.max(0, baseNeed - projAvail);
    var projCoverage = baseNeed > 0 ? Math.min(100, Math.round((projAvail / baseNeed) * 100)) : 100;

    document.getElementById('simProjAvailable').textContent = projAvail + ' Org';
    document.getElementById('simProjGap').textContent = '-' + projGap + ' Org';
    document.getElementById('simProjCoverage').textContent = projCoverage + '%';
}
document.addEventListener('DOMContentLoaded', runFormationSimulation);
</script>
<?= $this->endSection() ?>
