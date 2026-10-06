<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$kpis = $kpis ?? [];
$usersByProfile    = $usersByProfile ?? [];
$usersByAccount    = $usersByAccount ?? [];
$accountStatusDonut = $accountStatusDonut ?? ['series' => [0, 0, 0], 'labels' => ['Aktif', 'Pending', 'Lainnya'], 'colors' => ['#487FFF', '#FF9F29', '#EF4A4A'], 'total' => 0];
$enrollmentMonthly = $enrollmentMonthly ?? ['categories' => [], 'registration' => [], 'enrollment' => []];
$approvalQueue     = $approvalQueue ?? [];
$recentActivity    = $recentActivity ?? [];

$totalUsers             = (int) ($kpis['totalUsers']             ?? 0);
$activeUsers            = (int) ($kpis['activeUsers']            ?? 0);
$pendingUsers           = (int) ($kpis['pendingUsers']           ?? 0);
$activeEnrollments      = (int) ($kpis['activeEnrollments']      ?? 0);
$issuedCertificates     = (int) ($kpis['issuedCertificates']     ?? 0);
$pendingApprovals       = (int) ($kpis['pendingApprovals']       ?? 0);
$newUsersThisMonth      = (int) ($kpis['newUsersThisMonth']      ?? 0);
$newEnrollmentsThisMonth= (int) ($kpis['newEnrollmentsThisMonth']?? 0);
$newCertificatesThisMonth= (int)($kpis['newCertificatesThisMonth']?? 0);

$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');

$profileLabelMap = [
    'asn'                  => 'ASN',
    'provider'             => 'Penyedia',
    'academic_practitioner'=> 'Akademisi',
    'public'               => 'Masyarakat',
];

$accountLabelMap = [
    'active'              => 'Aktif',
    'pending_verification'=> 'Pending Verifikasi',
    'submitted'           => 'Submitted',
    'revision_required'   => 'Perlu Revisi',
    'rejected'            => 'Ditolak',
    'inactive'            => 'Inactive',
    'suspended'           => 'Suspended',
    'archived'            => 'Archived',
    'draft'               => 'Draft',
];
?>

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
                        <h6 class="mb-0"><?= $fmt($totalUsers) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> <?= $fmt($newUsersThisMonth) ?></span> bulan ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Akun Aktif</p>
                        <h6 class="mb-0"><?= $fmt($activeUsers) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:user-check-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-secondary-light"><?= $fmt($pendingUsers) ?> pending</span></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Enrollment Aktif</p>
                        <h6 class="mb-0"><?= $fmt($activeEnrollments) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:book-2-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> <?= $fmt($newEnrollmentsThisMonth) ?></span> bulan ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0"><?= $fmt($issuedCertificates) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl mb-0"></iconify-icon></div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2"><span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> <?= $fmt($newCertificatesThisMonth) ?></span> bulan ini</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Approval</p>
                        <h6 class="mb-0"><?= $fmt($pendingApprovals) ?></h6>
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
                        <p class="text-secondary-light text-sm mb-0 mt-4">12 bulan terakhir dari database.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 radius-8">Tahun berjalan</span>
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
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-primary-600"></span><span class="text-secondary-light text-sm">Aktif: <strong class="text-primary-light"><?= $fmt($activeUsers) ?></strong></span></li>
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-yellow"></span><span class="text-secondary-light text-sm">Pending: <strong class="text-primary-light"><?= $fmt($pendingUsers) ?></strong></span></li>
                    <li class="d-flex align-items-center gap-2"><span class="w-12-px h-12-px radius-2 bg-red"></span><span class="text-secondary-light text-sm">Lainnya: <strong class="text-primary-light"><?= $fmt($otherUsers) ?></strong></span></li>
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
                            <?php if ($approvalQueue === []): ?>
                                <tr><td colspan="4" class="text-center text-secondary-light">Tidak ada antrean persetujuan saat ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($approvalQueue as $row): ?>
                                    <tr>
                                        <td><?= esc(($row['resource_type'] ?? '-') . ' / ' . ($row['requested_action'] ?? '-')) ?></td>
                                        <td><?= esc($row['requester']) ?></td>
                                        <td><?= esc(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                                        <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $row['status']]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                    <?php if ($recentActivity === []): ?>
                        <p class="text-secondary-light text-sm mb-0">Belum ada aktivitas tercatat.</p>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $entry): ?>
                            <div class="d-flex gap-12">
                                <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"><iconify-icon icon="solar:history-2-outline" class="text-xl"></iconify-icon></span>
                                <div>
                                    <h6 class="text-md mb-4"><?= esc($entry['action']) ?></h6>
                                    <p class="text-sm text-secondary-light mb-0">
                                        <?= esc($entry['actor']) ?>
                                        <?php if ($entry['resource_type'] !== ''): ?>
                                            &middot; <?= esc($entry['resource_type']) ?>#<?= esc((string) $entry['resource_id']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <span class="text-xs text-secondary-light"><?= esc(date('d M Y H:i', strtotime($entry['created_at']))) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="mb-16 text-lg">Distribusi Tipe Profil</h6>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead><tr><th>Tipe Profil</th><th class="text-end">Jumlah</th></tr></thead>
                        <tbody>
                            <?php if ($usersByProfile === []): ?>
                                <tr><td colspan="2" class="text-center text-secondary-light">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($usersByProfile as $profileType => $total): ?>
                                    <tr>
                                        <td><?= esc($profileLabelMap[$profileType] ?? $profileType) ?></td>
                                        <td class="text-end"><strong><?= $fmt((int) $total) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="mb-16 text-lg">Distribusi Status Akun</h6>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead><tr><th>Status</th><th class="text-end">Jumlah</th></tr></thead>
                        <tbody>
                            <?php if ($usersByAccount === []): ?>
                                <tr><td colspan="2" class="text-center text-secondary-light">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($usersByAccount as $status => $total): ?>
                                    <tr>
                                        <td><?= esc($accountLabelMap[$status] ?? $status) ?></td>
                                        <td class="text-end"><strong><?= $fmt((int) $total) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    (function () {
        const enrollmentTarget = document.querySelector('#begawiEnrollmentChart');
        const monthly = <?= json_encode($enrollmentMonthly, JSON_UNESCAPED_UNICODE) ?>;
        if (enrollmentTarget && window.ApexCharts && monthly && Array.isArray(monthly.registration)) {
            new ApexCharts(enrollmentTarget, {
                chart: {
                    type: 'area',
                    height: 330,
                    toolbar: { show: false }
                },
                series: [
                    { name: 'Pendaftaran', data: monthly.registration },
                    { name: 'Enrollment', data: monthly.enrollment }
                ],
                colors: ['#487FFF', '#16A34A'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.28, opacityTo: 0.04, stops: [0, 100] }
                },
                grid: { borderColor: '#E9ECEF', strokeDashArray: 4 },
                xaxis: { categories: monthly.categories },
                yaxis: { labels: { formatter: value => Math.round(value) } },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { shared: true }
            }).render();
        }

        const accountStatusTarget = document.querySelector('#begawiAccountStatusChart');
        const donut = <?= json_encode($accountStatusDonut, JSON_UNESCAPED_UNICODE) ?>;
        if (accountStatusTarget && window.ApexCharts && donut) {
            new ApexCharts(accountStatusTarget, {
                chart: { type: 'donut', height: 250 },
                series: donut.series,
                labels: donut.labels,
                colors: donut.colors,
                legend: { show: false },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: () => String(donut.total)
                                }
                            }
                        }
                    }
                }
            }).render();
        }
    })();
</script>
<?= $this->endSection() ?>