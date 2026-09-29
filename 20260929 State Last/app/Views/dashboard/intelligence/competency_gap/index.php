<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24"><div><h6 class="fw-semibold mb-1">Gap Kompetensi</h6><p class="text-secondary-light mb-0">Analisis kebutuhan dan ketersediaan kompetensi PBJ per OPD/Unit.</p></div><select class="form-select form-select-sm w-auto radius-8"><option>Tahun 2026</option><option>Tahun 2025</option></select></div>
<div class="row gy-4"><div class="col-xl-8"><div class="card"><div class="card-body p-24"><div class="d-flex justify-content-between align-items-center mb-20"><h6 class="text-lg mb-0">Kebutuhan dan Ketersediaan Kompetensi</h6><span class="text-secondary-light text-sm">Data dummy UI-only</span></div><div id="competencyGapChart"></div></div></div></div><div class="col-xl-4"><div class="card"><div class="card-body p-24"><h6 class="text-lg mb-20">Ringkasan Gap</h6><div class="d-flex justify-content-between mb-16"><span class="text-secondary-light">Kebutuhan Total</span><strong>163</strong></div><div class="d-flex justify-content-between mb-16"><span class="text-secondary-light">Kompetensi Tersedia</span><strong>126</strong></div><div class="d-flex justify-content-between"><span class="text-secondary-light">Gap Kompetensi</span><strong class="text-danger-main">37</strong></div><hr class="my-24"><a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-outline-primary-600 w-100 radius-8">Lihat OPD/Unit</a></div></div></div></div>
<div class="card mt-24"><div class="card-body p-24"><div class="table-responsive scroll-sm"><table class="table bordered-table sm-table mb-0"><thead><tr><th>OPD/Unit</th><th>Kompetensi</th><th>Kebutuhan</th><th>Tersedia</th><th>Gap</th><th class="text-center">Prioritas</th></tr></thead><tbody><?php foreach ([['unit' => 'Dinas Pekerjaan Umum', 'competency' => 'Manajemen Kontrak', 'need' => '18', 'available' => '11', 'gap' => '7', 'priority' => 'Tinggi', 'status' => 'rejected'], ['unit' => 'Dinas Kesehatan', 'competency' => 'Perencanaan Pengadaan', 'need' => '14', 'available' => '9', 'gap' => '5', 'priority' => 'Tinggi', 'status' => 'rejected'], ['unit' => 'Dinas Pendidikan', 'competency' => 'Pemilihan Penyedia', 'need' => '12', 'available' => '8', 'gap' => '4', 'priority' => 'Menengah', 'status' => 'review'], ['unit' => 'Biro PBJ', 'competency' => 'Etika Pengadaan', 'need' => '20', 'available' => '18', 'gap' => '2', 'priority' => 'Rendah', 'status' => 'approved']] as $gap): ?><tr><td><h6 class="text-md mb-0 fw-medium"><?= esc($gap['unit']) ?></h6></td><td><?= esc($gap['competency']) ?></td><td><?= esc($gap['need']) ?></td><td><?= esc($gap['available']) ?></td><td><span class="text-danger-main fw-semibold"><?= esc($gap['gap']) ?></span></td><td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $gap['status'], 'label' => $gap['priority']]) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const gapTarget = document.querySelector('#competencyGapChart');
    if (gapTarget && window.ApexCharts) {
        new ApexCharts(gapTarget, {
            chart: { type: 'bar', height: 340, toolbar: { show: false } },
            series: [
                { name: 'Kebutuhan', data: [18, 14, 12, 20] },
                { name: 'Tersedia', data: [11, 9, 8, 18] }
            ],
            colors: ['#EF4A4A', '#487FFF'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: ['Dinas PU', 'Dinas Kesehatan', 'Dinas Pendidikan', 'Biro PBJ'] },
            grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' }
        }).render();
    }
</script>
<?= $this->endSection() ?>