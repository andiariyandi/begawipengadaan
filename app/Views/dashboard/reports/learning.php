<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Laporan Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Ringkasan pembelajaran, partisipasi, dan capaian peserta.</p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <select class="form-select form-select-sm w-auto radius-8">
            <option>Tahun 2026</option>
            <option>Tahun 2025</option>
        </select>
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">
            <iconify-icon icon="solar:download-minimalistic-outline" class="me-6"></iconify-icon> Export Laporan
        </button>
    </div>
</div>

<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col"><div class="card shadow-none border bg-gradient-start-1 h-100"><div class="card-body p-20"><div class="d-flex align-items-center justify-content-between gap-3"><div><p class="fw-medium text-primary-light mb-1">Kelas Aktif</p><h6 class="mb-0">24</h6></div><span class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:book-2-outline" class="text-white text-2xl"></iconify-icon></span></div><p class="text-sm text-primary-light mt-12 mb-0">12 kelas baru bulan ini</p></div></div></div>
    <div class="col"><div class="card shadow-none border bg-gradient-start-2 h-100"><div class="card-body p-20"><div class="d-flex align-items-center justify-content-between gap-3"><div><p class="fw-medium text-primary-light mb-1">Total Peserta</p><h6 class="mb-0">1.248</h6></div><span class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl"></iconify-icon></span></div><p class="text-sm text-primary-light mt-12 mb-0">Peserta terdaftar</p></div></div></div>
    <div class="col"><div class="card shadow-none border bg-gradient-start-3 h-100"><div class="card-body p-20"><div class="d-flex align-items-center justify-content-between gap-3"><div><p class="fw-medium text-primary-light mb-1">Tingkat Penyelesaian</p><h6 class="mb-0">78%</h6></div><span class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:chart-2-outline" class="text-white text-2xl"></iconify-icon></span></div><p class="text-sm text-primary-light mt-12 mb-0">Rata-rata seluruh kelas</p></div></div></div>
    <div class="col"><div class="card shadow-none border bg-gradient-start-5 h-100"><div class="card-body p-20"><div class="d-flex align-items-center justify-content-between gap-3"><div><p class="fw-medium text-primary-light mb-1">Lulus Evaluasi</p><h6 class="mb-0">824</h6></div><span class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl"></iconify-icon></span></div><p class="text-sm text-primary-light mt-12 mb-0">Peserta memenuhi nilai minimal</p></div></div></div>
</div>

<div class="row gy-4 mt-1">
    <div class="col-xxl-8"><div class="card h-100"><div class="card-body p-24"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div><h6 class="text-lg mb-0">Tren Partisipasi Pembelajaran</h6><p class="text-secondary-light text-sm mb-0 mt-4">Jumlah peserta pembelajaran per bulan.</p></div><select class="form-select form-select-sm w-auto radius-8"><option>Peserta</option><option>Penyelesaian</option></select></div><div id="learningReportChart" class="pt-28"></div></div></div></div>
    <div class="col-xxl-4"><div class="card h-100"><div class="card-body p-24"><h6 class="text-lg mb-20">Status Pembelajaran</h6><div id="learningStatusChart"></div><div class="d-flex flex-column gap-12 mt-16"><div class="d-flex justify-content-between"><span class="text-secondary-light"><span class="w-10-px h-10-px bg-primary-600 rounded-circle d-inline-block me-6"></span>Selesai</span><strong>824</strong></div><div class="d-flex justify-content-between"><span class="text-secondary-light"><span class="w-10-px h-10-px bg-warning-main rounded-circle d-inline-block me-6"></span>Berlangsung</span><strong>302</strong></div><div class="d-flex justify-content-between"><span class="text-secondary-light"><span class="w-10-px h-10-px bg-secondary-light rounded-circle d-inline-block me-6"></span>Belum Mulai</span><strong>122</strong></div></div></div></div></div>
</div>

<div class="card mt-24"><div class="card-body p-24"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20"><div><h6 class="text-lg mb-0">Performa Kelas</h6><p class="text-secondary-light text-sm mb-0 mt-4">Pratinjau data dummy untuk dashboard internal.</p></div><input type="search" class="form-control form-control-sm w-auto radius-8" placeholder="Cari kelas"></div><div class="table-responsive scroll-sm"><table class="table bordered-table sm-table mb-0"><thead><tr><th>Kelas</th><th>Kategori</th><th>Peserta</th><th>Penyelesaian</th><th>Nilai Rata-rata</th><th class="text-center">Status</th></tr></thead><tbody><?php foreach ([['name' => 'Dasar-Dasar Pengadaan Barang/Jasa', 'category' => 'Pengadaan', 'participants' => '312', 'completion' => '82%', 'score' => '84,6', 'status' => 'Aktif'], ['name' => 'Manajemen Kontrak Pemerintah', 'category' => 'Kontrak', 'participants' => '238', 'completion' => '76%', 'score' => '81,2', 'status' => 'Aktif'], ['name' => 'Perencanaan Pengadaan Strategis', 'category' => 'Perencanaan', 'participants' => '176', 'completion' => '71%', 'score' => '79,8', 'status' => 'Selesai']] as $class): ?><tr><td><h6 class="text-md fw-medium mb-0"><?= esc($class['name']) ?></h6></td><td><?= esc($class['category']) ?></td><td><?= esc($class['participants']) ?></td><td><?= esc($class['completion']) ?></td><td><?= esc($class['score']) ?></td><td class="text-center"><?= view('dashboard/components/status_badge', ['status' => strtolower($class['status']) === 'aktif' ? 'active' : 'approved', 'label' => $class['status']]) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const learningReportTarget = document.querySelector('#learningReportChart');
    if (learningReportTarget && window.ApexCharts) {
        new ApexCharts(learningReportTarget, { chart: { type: 'area', height: 320, toolbar: { show: false } }, series: [{ name: 'Peserta', data: [136, 184, 172, 218, 241, 297] }], colors: ['#487FFF'], stroke: { curve: 'smooth', width: 3 }, fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } }, dataLabels: { enabled: false }, xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'] }, grid: { borderColor: '#E9ECEF', strokeDashArray: 4 } }).render();
    }
    const learningStatusTarget = document.querySelector('#learningStatusChart');
    if (learningStatusTarget && window.ApexCharts) {
        new ApexCharts(learningStatusTarget, { chart: { type: 'donut', height: 245 }, series: [824, 302, 122], labels: ['Selesai', 'Berlangsung', 'Belum Mulai'], colors: ['#487FFF', '#FF9F29', '#98A2B3'], dataLabels: { enabled: false }, legend: { show: false }, plotOptions: { pie: { donut: { size: '68%' } } } }).render();
    }
</script>
<?= $this->endSection() ?>
