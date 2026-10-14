<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$kpis               = $kpis ?? [];
$access             = $access ?? [];
$learning           = $learning ?? [];
$knowledge          = $knowledge ?? [];
$regulations        = $regulations ?? [];
$communication      = $communication ?? [];
$certificate        = $certificate ?? [];

$usersByProfile     = $usersByProfile ?? [];
$usersByAccount     = $usersByAccount ?? [];
$accountStatusDonut = $accountStatusDonut ?? ['series' => [0, 0, 0], 'labels' => ['Aktif', 'Pending', 'Lainnya'], 'colors' => ['#487FFF', '#FF9F29', '#EF4A4A'], 'total' => 0];
$enrollmentMonthly  = $enrollmentMonthly ?? ['categories' => [], 'registration' => [], 'enrollment' => []];
$approvalQueue      = $approvalQueue ?? [];
$recentActivity     = $recentActivity ?? [];

$totalUsers             = (int) ($kpis['totalUsers'] ?? ($access['totalUsers'] ?? 0));
$activeUsers            = (int) ($kpis['activeUsers'] ?? ($access['activeUsers'] ?? 0));
$pendingUsers           = (int) ($kpis['pendingUsers'] ?? ($access['pendingUsers'] ?? 0));
$otherUsers             = (int) ($otherUsers ?? ($access['otherUsers'] ?? 0));
$pendingVerifications   = (int) ($access['pendingVerifications'] ?? 0);

$totalPrograms          = (int) ($learning['totalPrograms'] ?? 0);
$totalCourses           = (int) ($learning['totalCourses'] ?? 0);
$activeClasses          = (int) ($learning['activeClasses'] ?? 0);
$activeEnrollments      = (int) ($learning['activeEnrollments'] ?? ($kpis['activeEnrollments'] ?? 0));
$completedEnrollments   = (int) ($learning['completedEnrollments'] ?? 0);
$completionRate         = (float) ($learning['completionRate'] ?? 0.0);
$newEnrollmentsMonth    = (int) ($learning['newEnrollmentsThisMonth'] ?? 0);

$totalArticles          = (int) ($knowledge['totalArticles'] ?? 0);
$totalFaqs              = (int) ($knowledge['totalFaqs'] ?? 0);
$totalCaseStudies       = (int) ($knowledge['totalCaseStudies'] ?? 0);
$latestArticles         = $knowledge['latestArticles'] ?? [];

$totalRegulations       = (int) ($regulations['totalRegulations'] ?? 0);
$latestRegulations      = $regulations['latestRegulations'] ?? [];

$totalNews              = (int) ($communication['totalNews'] ?? 0);
$totalEvents            = (int) ($communication['totalEvents'] ?? 0);
$latestNews             = $communication['latestNews'] ?? [];
$upcomingEvents         = $communication['upcomingEvents'] ?? [];

$issuedCertificates     = (int) ($certificate['issuedCertificates'] ?? ($kpis['issuedCertificates'] ?? 0));
$pendingApprovals       = (int) ($certificate['pendingApprovals'] ?? ($kpis['pendingApprovals'] ?? 0));

$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');

$profileLabelMap = [
    'asn'                  => 'ASN / Instansi',
    'provider'             => 'Penyedia Barang/Jasa',
    'academic_practitioner' => 'Akademisi / Praktisi',
    'public'               => 'Masyarakat Umum',
];

$accountLabelMap = [
    'active'              => 'Aktif',
    'pending_verification' => 'Pending Verifikasi',
    'submitted'           => 'Diajukan',
    'revision_required'   => 'Perlu Revisi',
    'rejected'            => 'Ditolak',
    'inactive'            => 'Tidak Aktif',
    'suspended'           => 'Ditangguhkan',
    'archived'            => 'Diarsipkan',
    'draft'               => 'Draf',
];

$auth = new \App\Services\Identity\AuthorizationService();
$isSuperAdmin = $auth->isSuperAdmin();
$roleBadge = $isSuperAdmin ? 'Super Administrator' : 'Administrator Back-Office';
?>

<!-- Header Dashboard -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h6 class="fw-semibold mb-0">Ringkasan Dashboard</h6>
            <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 fw-semibold text-xs">
                <i class="ri-shield-user-line me-1"></i><?= esc($roleBadge) ?>
            </span>
        </div>
        <p class="text-secondary-light mb-0">Ikhtisar operasional dan pemantauan modul BegawiPengadaan Provinsi Lampung.</p>
    </div>
    <ul class="d-flex align-items-center gap-2 mb-0">
        <li class="fw-medium">
            <a href="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary text-secondary-light">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
            </a>
        </li>
        <li class="text-secondary-light">-</li>
        <li class="fw-medium text-primary-600">Ringkasan</li>
    </ul>
</div>

<!-- Row 1: KPI Stat Cards Per Modul Utama -->
<div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
    <!-- Card 1: Arena Belajar -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelas Aktif</p>
                        <h6 class="mb-0"><?= $fmt($activeClasses) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-2-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> <?= $completionRate ?>%
                    </span> kelulusan (<?= $fmt($completedEnrollments) ?> peserta)
                </p>
            </div>
        </div>
    </div>

    <!-- Card 2: Peserta Aktif -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Peserta Aktif</p>
                        <h6 class="mb-0"><?= $fmt($activeEnrollments) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> <?= $fmt($newEnrollmentsMonth) ?>
                    </span> enrollment bulan ini
                </p>
            </div>
        </div>
    </div>

    <!-- Card 3: Sertifikat Diterbitkan -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sertifikat Terbit</p>
                        <h6 class="mb-0"><?= $fmt($issuedCertificates) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-ribbons-star-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <?php if ($pendingApprovals > 0): ?>
                        <span class="d-inline-flex align-items-center gap-1 text-danger-main">
                            <iconify-icon icon="solar:clock-circle-bold" class="text-xs"></iconify-icon> <?= $fmt($pendingApprovals) ?>
                        </span> menunggu persetujuan
                    <?php else: ?>
                        <span class="d-inline-flex align-items-center gap-1 text-success-main">
                            <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> Lengkap
                        </span> antrean nihil
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Card 4: Dokumen Regulasi -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Regulasi PBJ</p>
                        <h6 class="mb-0"><?= $fmt($totalRegulations) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clipboard-text-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-primary-light">
                        <iconify-icon icon="solar:document-text-outline" class="text-xs"></iconify-icon> <?= $fmt($totalArticles) ?> Artikel
                    </span> di Bunga Rampai
                </p>
            </div>
        </div>
    </div>

    <!-- Card 5: Manajemen Pengguna -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Pengguna</p>
                        <h6 class="mb-0"><?= $fmt($totalUsers) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <?php if ($pendingVerifications > 0): ?>
                        <span class="d-inline-flex align-items-center gap-1 text-danger-main">
                            <iconify-icon icon="solar:shield-warning-bold" class="text-xs"></iconify-icon> <?= $fmt($pendingVerifications) ?>
                        </span> verifikasi pending
                    <?php else: ?>
                        <span class="d-inline-flex align-items-center gap-1 text-success-main">
                            <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> <?= $fmt($activeUsers) ?>
                        </span> akun aktif
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Visual Charts & Analytics (ApexCharts) -->
<div class="row gy-4 mt-1">
    <!-- Chart Area: Tren Pendaftaran & Enrollment -->
    <div class="col-xxl-8 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-20">
                    <div>
                        <h6 class="text-lg fw-semibold mb-1">Tren Pendaftaran Pengguna & Enrollment Kelas</h6>
                        <p class="text-secondary-light text-sm mb-0">Statistik riil pergerakan peserta selama 12 bulan terakhir.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 radius-8 px-12 py-6 fw-medium text-xs">
                        <i class="ri-calendar-line me-1"></i>12 Bulan Berjalan
                    </span>
                </div>
                <div id="begawiEnrollmentChart" class="pt-10"></div>
            </div>
        </div>
    </div>

    <!-- Chart Donut: Komposisi Akun Pengguna -->
    <div class="col-xxl-4 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-16">
                    <div>
                        <h6 class="mb-1 fw-semibold text-lg">Status Akun Pengguna</h6>
                        <p class="text-secondary-light text-xs mb-0">Komposisi status autentikasi di sistem.</p>
                    </div>
                    <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="text-primary-600 text-sm hover-text-primary fw-medium">
                        Kelola <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
                <div id="begawiAccountStatusChart" class="pt-10"></div>
                <ul class="d-flex flex-wrap align-items-center justify-content-between mt-20 gap-2 mb-0 p-12 bg-neutral-50 radius-8 border">
                    <li class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px radius-2 bg-primary-600"></span>
                        <span class="text-secondary-light text-xs">Aktif: <strong class="text-primary-light"><?= $fmt($activeUsers) ?></strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px radius-2 bg-yellow"></span>
                        <span class="text-secondary-light text-xs">Pending: <strong class="text-primary-light"><?= $fmt($pendingUsers) ?></strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px radius-2 bg-red"></span>
                        <span class="text-secondary-light text-xs">Lainnya: <strong class="text-primary-light"><?= $fmt($otherUsers) ?></strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Antrean Persetujuan & Verifikasi Profil -->
<div class="row gy-4 mt-1">
    <!-- Antrean Persetujuan Sertifikat / Event -->
    <div class="col-xxl-7 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between mb-16">
                    <div>
                        <h6 class="mb-1 text-lg fw-semibold">Antrean Persetujuan & Approval</h6>
                        <p class="text-secondary-light text-xs mb-0">Permohonan penerbitan sertifikat dan approval yang perlu ditindaklanjuti.</p>
                    </div>
                    <a href="<?= site_url('dashboard/sertifikat/approval') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium d-flex align-items-center gap-1">
                        Lihat Semua <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                    </a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Jenis / Aksi</th>
                                <th>Pemohon</th>
                                <th>Diajukan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($approvalQueue)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-20 text-secondary-light">
                                        <iconify-icon icon="solar:check-circle-bold" class="text-success-main text-2xl d-block mb-1"></iconify-icon>
                                        Tidak ada antrean persetujuan yang menunggu saat ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($approvalQueue as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-medium text-primary-light"><?= esc(ucfirst((string) $row['resource_type'])) ?></span>
                                            <span class="text-xs text-secondary-light d-block"><?= esc(str_replace('_', ' ', (string) $row['requested_action'])) ?></span>
                                        </td>
                                        <td>
                                            <?= esc($row['requester_name'] ?? $row['requester_username'] ?? $row['requester_email'] ?? '-') ?>
                                        </td>
                                        <td class="text-sm text-secondary-light">
                                            <?= esc(date('d M Y H:i', strtotime($row['created_at']))) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', ['status' => $row['status']]) ?>
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

    <!-- Antrean Verifikasi Profil Pengguna Baru -->
    <div class="col-xxl-5 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-16">
                    <div>
                        <h6 class="mb-1 text-lg fw-semibold">Verifikasi Pengguna Baru</h6>
                        <p class="text-secondary-light text-xs mb-0">Registrasi mandiri yang menunggu verifikasi berkas operator/admin.</p>
                    </div>
                    <a href="<?= site_url('dashboard/akses/verifikasi-profil') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium">
                        Buka Antrean <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Tipe Profil</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $recentVerifs = $access['recentVerifications'] ?? []; ?>
                            <?php if (empty($recentVerifs)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-20 text-secondary-light">
                                        <iconify-icon icon="solar:shield-check-bold" class="text-success-main text-2xl d-block mb-1"></iconify-icon>
                                        Seluruh pengguna baru telah diverifikasi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentVerifs as $v): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-medium text-primary-light d-block"><?= esc($v['name'] ?? 'User') ?></span>
                                            <span class="text-xs text-secondary-light"><?= esc($v['email'] ?? '') ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-neutral-100 text-neutral-800 text-xs radius-4">
                                                <?= esc($profileLabelMap[$v['profile_type']] ?? $v['profile_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', ['status' => $v['status']]) ?>
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
</div>

<!-- Row 4: Konten Terbaru (Bunga Rampai & Regulasi PBJ) & Aktivitas Audit -->
<div class="row gy-4 mt-1">
    <!-- Konten Terbaru Bunga Rampai & Regulasi PBJ -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div>
                        <h6 class="mb-1 text-lg fw-semibold">Publikasi Regulasi & Bunga Rampai</h6>
                        <p class="text-secondary-light text-xs mb-0">Pembaruan dokumen hukum dan artikel knowledge terkini.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-sm btn-outline-primary py-4 px-10 text-xs radius-6">Regulasi</a>
                        <a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>" class="btn btn-sm btn-outline-primary py-4 px-10 text-xs radius-6">Artikel</a>
                    </div>
                </div>

                <div class="d-flex flex-column gap-16">
                    <!-- Regulasi Terkini -->
                    <?php if (! empty($latestRegulations)): ?>
                        <?php foreach (array_slice($latestRegulations, 0, 3) as $reg): ?>
                            <div class="d-flex align-items-start gap-12 p-12 bg-neutral-50 radius-8 border">
                                <span class="w-36-px h-36-px bg-info-50 text-info-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:clipboard-text-outline" class="text-lg"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <h6 class="text-sm fw-semibold text-truncate mb-2 text-primary-light">
                                            <?= esc($reg['title']) ?>
                                        </h6>
                                        <span class="badge bg-info-50 text-info-600 text-xs flex-shrink-0 radius-4">Regulasi</span>
                                    </div>
                                    <p class="text-xs text-secondary-light mb-0">
                                        No. <?= esc($reg['document_number'] ?? '-') ?> &middot; Tahun <?= esc((string) ($reg['document_year'] ?? '-')) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Artikel Terkini -->
                    <?php if (! empty($latestArticles)): ?>
                        <?php foreach (array_slice($latestArticles, 0, 2) as $art): ?>
                            <div class="d-flex align-items-start gap-12 p-12 bg-neutral-50 radius-8 border">
                                <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <h6 class="text-sm fw-semibold text-truncate mb-2 text-primary-light">
                                            <?= esc($art['title']) ?>
                                        </h6>
                                        <span class="badge bg-primary-50 text-primary-600 text-xs flex-shrink-0 radius-4">Bunga Rampai</span>
                                    </div>
                                    <p class="text-xs text-secondary-light mb-0">
                                        Penulis: <?= esc($art['author_name'] ?? 'Admin') ?> &middot; <?= esc(date('d M Y', strtotime($art['created_at']))) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (empty($latestRegulations) && empty($latestArticles)): ?>
                        <p class="text-secondary-light text-sm mb-0 text-center py-20">Belum ada dokumen atau artikel yang dipublikasikan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Sistem & Audit Trail -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div>
                        <h6 class="mb-1 text-lg fw-semibold">Aktivitas Sistem & Audit Trail</h6>
                        <p class="text-secondary-light text-xs mb-0">Log aksi operasional terakhir pengguna di back-office.</p>
                    </div>
                    <a href="<?= site_url('dashboard/sistem/audit-trail') ?>" class="text-primary-600 hover-text-primary text-sm fw-medium">
                        Log Lengkap <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>

                <div class="d-flex flex-column gap-16">
                    <?php if (empty($recentActivity)): ?>
                        <p class="text-secondary-light text-sm mb-0 text-center py-20">Belum ada aktivitas tercatat di audit log.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($recentActivity, 0, 5) as $entry): ?>
                            <div class="d-flex gap-12 align-items-start">
                                <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:history-2-outline" class="text-lg"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <h6 class="text-sm fw-semibold mb-1 text-primary-light text-truncate">
                                            <?= esc($entry['action']) ?>
                                        </h6>
                                        <span class="text-xs text-secondary-light flex-shrink-0">
                                            <?= esc(date('d M H:i', strtotime($entry['created_at']))) ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-secondary-light mb-0">
                                        Oleh: <strong class="text-neutral-700"><?= esc($entry['actor'] ?? ($entry['actor_username'] ?? 'Sistem')) ?></strong>
                                        <?php if (! empty($entry['resource_type'])): ?>
                                            &middot; <?= esc($entry['resource_type']) ?>#<?= esc((string) ($entry['resource_id'] ?? '')) ?>
                                        <?php endif; ?>
                                        <?php if (! empty($entry['result'])): ?>
                                            &middot; <span class="text-success-main"><?= esc($entry['result']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Matriks Distribusi Profil & Status -->
<div class="row gy-4 mt-1">
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between mb-16">
                    <h6 class="mb-0 text-md fw-semibold">Distribusi Tipe Profil Peserta</h6>
                    <span class="badge bg-neutral-100 text-neutral-800 text-xs radius-4"><?= $fmt($totalUsers) ?> Total</span>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Kategori Profil</th>
                                <th class="text-end">Jumlah Pengguna</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usersByProfile)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-secondary-light">Belum ada data profil.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usersByProfile as $profileType => $total): ?>
                                    <tr>
                                        <td>
                                            <i class="ri-user-star-line text-primary-600 me-2"></i>
                                            <?= esc($profileLabelMap[$profileType] ?? ucfirst(str_replace('_', ' ', $profileType))) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-primary-light"><?= $fmt((int) $total) ?></td>
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
                <div class="d-flex align-items-center justify-content-between mb-16">
                    <h6 class="mb-0 text-md fw-semibold">Distribusi Status Akun Sistem</h6>
                    <span class="badge bg-neutral-100 text-neutral-800 text-xs radius-4"><?= $fmt($totalUsers) ?> Terdaftar</span>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Status Akun</th>
                                <th class="text-end">Jumlah Pengguna</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usersByAccount)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-secondary-light">Belum ada data status.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usersByAccount as $status => $total): ?>
                                    <tr>
                                        <td>
                                            <?= view('dashboard/components/status_badge', ['status' => $status]) ?>
                                            <span class="ms-2 text-sm text-secondary-light"><?= esc($accountLabelMap[$status] ?? ucfirst($status)) ?></span>
                                        </td>
                                        <td class="text-end fw-semibold text-primary-light"><?= $fmt((int) $total) ?></td>
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
    (function() {
        // Area Chart: Tren Pendaftaran & Enrollment 12 Bulan
        const enrollmentTarget = document.querySelector('#begawiEnrollmentChart');
        const monthly = <?= json_encode($enrollmentMonthly, JSON_UNESCAPED_UNICODE) ?>;
        if (enrollmentTarget && window.ApexCharts && monthly && Array.isArray(monthly.registration)) {
            new ApexCharts(enrollmentTarget, {
                chart: {
                    type: 'area',
                    height: 310,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                        name: 'Pendaftaran Akun',
                        data: monthly.registration
                    },
                    {
                        name: 'Enrollment Kelas',
                        data: monthly.enrollment
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
                    categories: monthly.categories,
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: value => Math.round(value),
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontWeight: 500
                },
                tooltip: {
                    shared: true,
                    theme: 'light'
                }
            }).render();
        }

        // Donut Chart: Komposisi Akun Pengguna
        const accountStatusTarget = document.querySelector('#begawiAccountStatusChart');
        const donut = <?= json_encode($accountStatusDonut, JSON_UNESCAPED_UNICODE) ?>;
        if (accountStatusTarget && window.ApexCharts && donut) {
            new ApexCharts(accountStatusTarget, {
                chart: {
                    type: 'donut',
                    height: 240,
                    fontFamily: 'inherit'
                },
                series: donut.series,
                labels: donut.labels,
                colors: donut.colors,
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Akun',
                                    formatter: () => String(donut.total),
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 600,
                                        color: '#4B5563'
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: 'light'
                }
            }).render();
        }
    })();
</script>
<?= $this->endSection() ?>