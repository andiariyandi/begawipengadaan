<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Ringkasan Dashboard</h6>
        <p class="text-secondary-light mb-0">Ikhtisar operasional BegawiPBJ Provinsi Lampung.</p>
    </div>
    <ul class="d-flex align-items-center gap-2 mb-0">
        <li class="fw-medium"><a href="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary"><iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a></li>
        <li>-</li>
        <li class="fw-medium">Ringkasan</li>
    </ul>
</div>

<div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                        <h6 class="mb-0">1.248</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 8,4%</span> dari bulan lalu</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Akun Aktif</p>
                        <h6 class="mb-0">1.084</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:user-check-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 5,2%</span> dari bulan lalu</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Aktif</p>
                        <h6 class="mb-0">36</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:book-2-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 6</span> kelas baru bulan ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0">587</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 42</span> bulan ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Approval</p>
                        <h6 class="mb-0">18</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:clock-circle-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-danger-main"><iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon> Perlu</span> tindak lanjut</p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mt-1">
    <div class="col-xxl-8 col-xl-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h6 class="text-lg mb-0">Tren Pendaftaran dan Enrollment</h6>
                        <p class="text-secondary-light text-sm mb-0 mt-4">Data dummy untuk pratinjau UI.</p>
                    </div>
                    <select class="form-select bg-base form-select-sm w-auto radius-8">
                        <option>Tahun berjalan</option>
                        <option>6 bulan terakhir</option>
                        <option>30 hari terakhir</option>
                    </select>
                </div>
                <div id="begawiEnrollmentChart" class="pt-28"></div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4 col-xl-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <h6 class="mb-0 fw-bold text-lg">Status Akun</h6><a href="<?= site_url('dashboard/akses/pengguna') ?>" class="text-primary-600 text-sm">Lihat pengguna</a>
                </div>
                <div id="begawiAccountStatusChart" class="pt-20"></div>
                <ul class="d-flex flex-wrap align-items-center justify-content-between mt-3 gap-3 mb-0">
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-primary-600"></span><span class="text-secondary-light text-sm">Aktif: <strong class="text-primary-light">1.084</strong></span></li>
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-yellow"></span><span class="text-secondary-light text-sm">Pending: <strong class="text-primary-light">92</strong></span></li>
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-red"></span><span class="text-secondary-light text-sm">Lainnya: <strong class="text-primary-light">72</strong></span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xxl-7 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center gap-1 justify-content-between mb-16">
                    <h6 class="mb-0 text-lg">Antrean Persetujuan</h6><a href="<?= site_url('dashboard/antrean-persetujuan') ?>" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">Lihat Semua <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon></a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Pemohon</th>
                                <th>Diajukan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Verifikasi Profil ASN</td>
                                <td>Rina Oktaviani</td>
                                <td>Hari ini, 09:24</td>
                                <td class="text-center"><span class="bg-warning-focus text-warning-main px-16 py-4 rounded-pill fw-medium text-sm">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td>Publikasi Regulasi</td>
                                <td>Tim Regulasi</td>
                                <td>Hari ini, 08:10</td>
                                <td class="text-center"><span class="bg-warning-focus text-warning-main px-16 py-4 rounded-pill fw-medium text-sm">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td>Penerbitan Sertifikat</td>
                                <td>Pelatihan PBJ Dasar</td>
                                <td>Kemarin, 15:42</td>
                                <td class="text-center"><span class="bg-info-focus text-info-main px-16 py-4 rounded-pill fw-medium text-sm">Ditinjau</span></td>
                            </tr>
                            <tr>
                                <td>Permohonan Role Trainer</td>
                                <td>Andi Pratama</td>
                                <td>Kemarin, 13:15</td>
                                <td class="text-center"><span class="bg-warning-focus text-warning-main px-16 py-4 rounded-pill fw-medium text-sm">Menunggu</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-5 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <h6 class="mb-0 text-lg">Aktivitas Terbaru</h6><a href="<?= site_url('dashboard/aktivitas-terbaru') ?>" class="text-primary-600 hover-text-primary">Lihat Semua</a>
                </div>
                <div class="d-flex flex-column gap-20">
                    <div class="d-flex gap-12"><span class="w-40-px h-40-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"><iconify-icon icon="solar:user-check-outline" class="text-xl"></iconify-icon></span>
                        <div>
                            <h6 class="text-md mb-4">Profil pengguna diverifikasi</h6>
                            <p class="text-sm text-secondary-light mb-0">Profil ASN atas nama Rina Oktaviani telah disetujui.</p><span class="text-xs text-secondary-light">10 menit lalu</span>
                        </div>
                    </div>
                    <div class="d-flex gap-12"><span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"><iconify-icon icon="solar:book-2-outline" class="text-xl"></iconify-icon></span>
                        <div>
                            <h6 class="text-md mb-4">Kelas baru diterbitkan</h6>
                            <p class="text-sm text-secondary-light mb-0">Kelas Pengadaan Barang/Jasa Tingkat Dasar tersedia.</p><span class="text-xs text-secondary-light">45 menit lalu</span>
                        </div>
                    </div>
                    <div class="d-flex gap-12"><span class="w-40-px h-40-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"><iconify-icon icon="solar:medal-ribbons-star-outline" class="text-xl"></iconify-icon></span>
                        <div>
                            <h6 class="text-md mb-4">Sertifikat menunggu approval</h6>
                            <p class="text-sm text-secondary-light mb-0">Sebanyak 8 sertifikat siap ditinjau.</p><span class="text-xs text-secondary-light">1 jam lalu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const enrollmentTarget = document.querySelector('#begawiEnrollmentChart');
    if (enrollmentTarget && window.ApexCharts) {
        new ApexCharts(enrollmentTarget, {
            chart: {
                type: 'area',
                height: 330,
                toolbar: {
                    show: false
                }
            },
            series: [{
                    name: 'Pendaftaran',
                    data: [52, 68, 61, 84, 73, 98, 110, 96, 122, 136, 128, 149]
                },
                {
                    name: 'Enrollment',
                    data: [34, 42, 39, 57, 48, 66, 75, 69, 81, 94, 88, 103]
                }
            ],
            colors: ['#487FFF', '#16A34A'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.28,
                    opacityTo: 0.04,
                    stops: [0, 100]
                }
            },
            grid: {
                borderColor: '#E9ECEF',
                strokeDashArray: 4
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            },
            yaxis: {
                labels: {
                    formatter: value => Math.round(value)
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            tooltip: {
                shared: true
            }
        }).render();
    }

    const accountStatusTarget = document.querySelector('#begawiAccountStatusChart');
    if (accountStatusTarget && window.ApexCharts) {
        new ApexCharts(accountStatusTarget, {
            chart: {
                type: 'donut',
                height: 250
            },
            series: [1084, 92, 72],
            labels: ['Aktif', 'Pending', 'Lainnya'],
            colors: ['#487FFF', '#FF9F29', '#EF4A4A'],
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: () => '1.248'
                            }
                        }
                    }
                }
            }
        }).render();
    }
</script>
<?= $this->endSection() ?>