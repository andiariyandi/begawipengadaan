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
$visitors           = $visitors ?? [];

$usersByProfile     = $usersByProfile ?? [];
$usersByAccount     = $usersByAccount ?? [];
$accountStatusDonut = $accountStatusDonut ?? ['series' => [0, 0, 0], 'labels' => ['Aktif', 'Pending', 'Lainnya'], 'colors' => ['#487FFF', '#FF9F29', '#EF4A4A'], 'total' => 0];
$enrollmentMonthly  = $enrollmentMonthly ?? ['categories' => [], 'registration' => [], 'enrollment' => [], 'eventRegistration' => []];
$approvalQueue      = $approvalQueue ?? [];
$recentActivity     = $recentActivity ?? [];

// Access / Users
$totalUsers           = (int) ($kpis['totalUsers'] ?? ($access['totalUsers'] ?? 0));
$activeUsers          = (int) ($kpis['activeUsers'] ?? ($access['activeUsers'] ?? 0));
$pendingUsers         = (int) ($kpis['pendingUsers'] ?? ($access['pendingUsers'] ?? 0));
$otherUsers           = (int) ($otherUsers ?? ($access['otherUsers'] ?? 0));
$pendingVerifications = (int) ($access['pendingVerifications'] ?? 0);
$newUsersThisMonth    = (int) ($kpis['newUsersThisMonth'] ?? ($access['newUsersThisMonth'] ?? 0));

// Arena Belajar
$totalPrograms        = (int) ($learning['totalPrograms'] ?? 0);
$totalCourses         = (int) ($learning['totalCourses'] ?? 0);
$totalModules         = (int) ($learning['totalModules'] ?? 0);
$activeClasses        = (int) ($learning['activeClasses'] ?? 0);
$activeEnrollments    = (int) ($learning['activeEnrollments'] ?? ($kpis['activeEnrollments'] ?? 0));
$completedEnrollments = (int) ($learning['completedEnrollments'] ?? 0);
$totalEnrollments     = (int) ($learning['totalEnrollments'] ?? 0);
$completionRate       = (float) ($learning['completionRate'] ?? 0.0);
$newEnrollmentsMonth  = (int) ($learning['newEnrollmentsThisMonth'] ?? 0);
$activeClassList      = $learning['activeClassList'] ?? [];

// Bunga Rampai (Knowledge)
$totalArticles        = (int) ($knowledge['totalArticles'] ?? 0);
$totalFaqs            = (int) ($knowledge['totalFaqs'] ?? 0);
$totalCaseStudies     = (int) ($knowledge['totalCaseStudies'] ?? 0);
$totalTemplates       = (int) ($knowledge['totalTemplates'] ?? 0);
$totalMedia           = (int) ($knowledge['totalMedia'] ?? 0);
$totalKnowledgeCat    = (int) ($knowledge['totalCategories'] ?? 0);
$latestArticles       = $knowledge['latestArticles'] ?? [];
$latestTemplates      = $knowledge['latestTemplates'] ?? [];

// Regulasi PBJ
$totalRegulations     = (int) ($regulations['totalRegulations'] ?? 0);
$totalRegulationCat   = (int) ($regulations['totalCategories'] ?? 0);
$totalVersions        = (int) ($regulations['totalVersions'] ?? 0);
$activeRegulations    = (int) ($regulations['activeRegulations'] ?? 0);
$revokedRegulations   = (int) ($regulations['revokedRegulations'] ?? 0);
$latestRegulations    = $regulations['latestRegulations'] ?? [];

// Komunikasi & Event
$totalNews            = (int) ($communication['totalNews'] ?? 0);
$totalNewsCat         = (int) ($communication['totalNewsCat'] ?? 0);
$totalEvents          = (int) ($communication['totalEvents'] ?? 0);
$totalRegistrasiEvent = (int) ($communication['totalRegistrasi'] ?? 0);
$totalBroadcasts      = (int) ($communication['totalBroadcasts'] ?? 0);
$newEventRegsMonth    = (int) ($learning['newEventRegistrationsThisMonth'] ?? 0);
$latestNews           = $communication['latestNews'] ?? [];
$upcomingEvents       = $communication['upcomingEvents'] ?? [];

// Sertifikat PBJ
$issuedCertificates   = (int) ($certificate['issuedCertificates'] ?? ($kpis['issuedCertificates'] ?? 0));
$pendingApprovals     = (int) ($certificate['pendingApprovals'] ?? ($kpis['pendingApprovals'] ?? 0));
$newCertificatesMonth = (int) ($certificate['newCertificatesThisMonth'] ?? 0);
$latestCertificates   = $certificate['latestCertificates'] ?? [];

// Visitor Landing Page
$visitorHome          = $visitors['home'] ?? ['today' => 0, 'this_week' => 0, 'this_month' => 0, 'this_year' => 0, 'total' => 0];
$visitorFeatures      = $visitors['features'] ?? [];
$totalFeatureHits     = (int) ($visitors['total_feature_hits'] ?? 0);

$fmt = static fn(int|float $n): string => number_format((float) $n, 0, ',', '.');

$profileLabelMap = [
    'asn'                   => 'ASN / Instansi',
    'provider'              => 'Penyedia Barang/Jasa',
    'academic_practitioner' => 'Akademisi / Praktisi',
    'public'                => 'Masyarakat Umum',
];

$accountLabelMap = [
    'active'               => 'Aktif',
    'pending_verification' => 'Pending Verifikasi',
    'submitted'            => 'Diajukan',
    'revision_required'    => 'Perlu Revisi',
    'rejected'             => 'Ditolak',
    'inactive'             => 'Tidak Aktif',
    'suspended'            => 'Ditangguhkan',
    'archived'             => 'Diarsipkan',
    'draft'                => 'Draf',
];

$auth = new \App\Services\Identity\AuthorizationService();
$isSuperAdmin = $auth->isSuperAdmin();
$roleBadge = $isSuperAdmin ? 'Super Administrator' : 'Administrator Back-Office';
?>

<!-- Header Dashboard Tunggal -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h5 class="fw-bold mb-0 text-primary-light">Dashboard Utama</h5>
            <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 fw-semibold text-xs">
                <i class="ri-shield-user-line me-1"></i><?= esc($roleBadge) ?>
            </span>
            <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-6 fw-semibold text-xs">
                <i class="ri-pulse-line me-1"></i>Sistem Berjalan Normal
            </span>
        </div>
        <p class="text-secondary-light mb-0 text-sm">Pusat komando terpadu dan analitik operasional seluruh modul ekosistem BegawiPengadaan Provinsi Lampung.</p>
    </div>
    <ul class="d-flex align-items-center gap-2 mb-0">
        <li class="fw-semibold text-primary-600 d-flex align-items-center gap-1">
            <iconify-icon icon="solar:home-smile-angle-bold" class="icon text-lg"></iconify-icon> Dashboard
        </li>
    </ul>
</div>

<!-- Quick Action & Spotlight Bar -->
<div class="card border-0 shadow-none bg-base radius-12 mb-24 p-16 border border-neutral-200">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="w-36-px h-36-px bg-warning-50 text-warning-600 rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:bolt-bold" class="text-xl"></iconify-icon>
            </span>
            <div>
                <span class="text-sm fw-bold text-primary-light d-block">Aksi Cepat Administrator</span>
                <span class="text-2xs text-secondary-light">Pintasan modul utama untuk percepatan operasional</span>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= site_url('dashboard/akses/pengguna/tambah') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                <iconify-icon icon="solar:user-plus-bold" class="text-sm"></iconify-icon>
                <span>+ Pengguna</span>
            </a>
            <a href="<?= site_url('dashboard/pembelajaran/kelas/tambah') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                <iconify-icon icon="solar:book-2-bold" class="text-sm"></iconify-icon>
                <span>+ Kelas PBJ</span>
            </a>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda/tambah') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                <iconify-icon icon="solar:calendar-add-bold" class="text-sm"></iconify-icon>
                <span>+ Event</span>
            </a>
            <a href="<?= site_url('dashboard/regulasi/dokumen/tambah') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                <iconify-icon icon="solar:document-add-bold" class="text-sm"></iconify-icon>
                <span>+ Regulasi</span>
            </a>
            <a href="<?= site_url('dashboard/komunikasi/berita/tambah') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                <iconify-icon icon="solar:pen-new-square-bold" class="text-sm"></iconify-icon>
                <span>+ Berita</span>
            </a>
            <button type="button" class="btn btn-sm btn-neutral-200 text-secondary-light radius-8 px-12 py-6 d-inline-flex align-items-center gap-1 text-xs fw-medium" data-bs-toggle="modal" data-bs-target="#adminSpotlightModal">
                <iconify-icon icon="ion:search-outline" class="text-sm"></iconify-icon>
                <span>Pencarian Cepat (Ctrl+K)</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. STATISTIK PENGUNJUNG LANDING PAGE (REAL TRAFFIC ANALYTICS)              -->
<!-- ========================================================================= -->
<div class="card radius-12 border border-neutral-200 mb-24 overflow-hidden">
    <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="w-36-px h-36-px bg-info-50 text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            </span>
            <div>
                <h6 class="mb-0 text-base fw-bold text-primary-light">Statistik Pengunjung Landing Page</h6>
                <span class="text-2xs text-secondary-light">Trafik kunjungan publik landing page dan per modul portal BegawiPengadaan</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-50 text-success-600 radius-6 px-10 py-4 text-xs fw-semibold">
                <i class="ri-radio-button-line me-1"></i>Real-time Hit Logger
            </span>
            <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-sm btn-outline-primary py-4 px-10 radius-6 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                <span>Lihat Portal Publik</span>
                <iconify-icon icon="solar:arrow-right-up-outline" class="text-xs"></iconify-icon>
            </a>
        </div>
    </div>
    <div class="card-body p-20">
        <!-- 4 Top Cards Traffic Summary -->
        <div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-3 mb-20">
            <div class="col">
                <div class="p-16 radius-10 border border-neutral-200 bg-neutral-50 h-100">
                    <span class="text-xs text-secondary-light fw-medium d-block mb-1">Pengunjung Hari Ini</span>
                    <div class="d-flex align-items-baseline gap-2">
                        <h4 class="mb-0 fw-bold text-primary-light"><?= $fmt($visitorHome['today'] ?? 0) ?></h4>
                        <span class="text-2xs text-success-main fw-semibold"><i class="ri-arrow-up-line"></i> Hari Ini</span>
                    </div>
                    <span class="text-2xs text-secondary-light mt-1 d-block">Akses landing page beranda</span>
                </div>
            </div>
            <div class="col">
                <div class="p-16 radius-10 border border-neutral-200 bg-neutral-50 h-100">
                    <span class="text-xs text-secondary-light fw-medium d-block mb-1">Pengunjung Minggu Ini</span>
                    <div class="d-flex align-items-baseline gap-2">
                        <h4 class="mb-0 fw-bold text-primary-light"><?= $fmt($visitorHome['this_week'] ?? 0) ?></h4>
                        <span class="text-2xs text-primary-600 fw-semibold">7 Hari</span>
                    </div>
                    <span class="text-2xs text-secondary-light mt-1 d-block">Sejak hari Senin pekan berjalan</span>
                </div>
            </div>
            <div class="col">
                <div class="p-16 radius-10 border border-neutral-200 bg-neutral-50 h-100">
                    <span class="text-xs text-secondary-light fw-medium d-block mb-1">Pengunjung Bulan Ini</span>
                    <div class="d-flex align-items-baseline gap-2">
                        <h4 class="mb-0 fw-bold text-primary-light"><?= $fmt($visitorHome['this_month'] ?? 0) ?></h4>
                        <span class="text-2xs text-info-main fw-semibold"><?= date('F Y') ?></span>
                    </div>
                    <span class="text-2xs text-secondary-light mt-1 d-block">Agregat bulan aktif berjalan</span>
                </div>
            </div>
            <div class="col">
                <div class="p-16 radius-10 border border-neutral-200 bg-neutral-50 h-100">
                    <span class="text-xs text-secondary-light fw-medium d-block mb-1">Total Kunjungan Akumulatif</span>
                    <div class="d-flex align-items-baseline gap-2">
                        <h4 class="mb-0 fw-bold text-primary-600"><?= $fmt($visitorHome['total'] ?? 0) ?></h4>
                        <span class="text-2xs text-secondary-light">Hits Total</span>
                    </div>
                    <span class="text-2xs text-secondary-light mt-1 d-block">Sejak sistem diluncurkan</span>
                </div>
            </div>
        </div>

        <!-- Modul Traffic Breakdown -->
        <h6 class="text-xs fw-bold text-secondary-light text-uppercase mb-12 letter-spacing-1">Distribusi Trafik Fitur Portal Publik</h6>
        <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-3">
            <?php foreach ($visitorFeatures as $fKey => $feat): ?>
                <div class="col">
                    <div class="p-14 radius-8 border border-neutral-200 bg-base h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-28-px h-28-px rounded-circle d-flex justify-content-center align-items-center" style="background-color: <?= esc($feat['bg']) ?>; color: <?= esc($feat['color']) ?>;">
                                        <iconify-icon icon="<?= esc($feat['icon']) ?>" class="text-sm"></iconify-icon>
                                    </span>
                                    <span class="text-xs fw-bold text-primary-light"><?= esc($feat['label']) ?></span>
                                </div>
                                <span class="badge radius-4 px-6 py-2 text-2xs fw-bold" style="background-color: <?= esc($feat['bg']) ?>; color: <?= esc($feat['color']) ?>;">
                                    <?= esc((string) $feat['percentage']) ?>%
                                </span>
                            </div>
                            <div class="progress h-6-px bg-neutral-100 radius-4 my-8">
                                <div class="progress-bar radius-4" role="progressbar" style="width: <?= esc((string) $feat['percentage']) ?>%; background-color: <?= esc($feat['color']) ?>;" aria-valuenow="<?= esc((string) $feat['percentage']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-2xs text-secondary-light mt-6 pt-6 border-top border-neutral-100">
                            <span>Hari ini: <strong class="text-primary-light"><?= $fmt($feat['hits_today']) ?></strong></span>
                            <span>Bulan: <strong class="text-primary-light"><?= $fmt($feat['hits_month']) ?></strong></span>
                            <span>Total: <strong class="text-primary-600"><?= $fmt($feat['hits_total']) ?></strong></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. EKSEKUTIF KPI SUMMARY CARDS (6 PILAR BEGAWI)                            -->
<!-- ========================================================================= -->
<div class="row row-cols-xxxl-6 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <!-- Card 1: Pengguna Platform -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Total Pengguna</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($totalUsers) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-success-main fw-bold"><i class="ri-user-follow-line"></i> <?= $fmt($activeUsers) ?></span> aktif
                    &middot; <span class="text-warning-main fw-bold"><?= $fmt($pendingUsers) ?></span> pending
                </p>
            </div>
        </div>
    </div>

    <!-- Card 2: Arena Belajar -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Kelas Berjalan</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($activeClasses) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:square-academic-cap-2-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-success-main fw-bold"><?= $completionRate ?>%</span> kelulusan
                    &middot; <?= $fmt($totalEnrollments) ?> peserta
                </p>
            </div>
        </div>
    </div>

    <!-- Card 3: Sertifikat PBJ -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Sertifikat Terbit</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($issuedCertificates) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-ribbons-star-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-primary-600 fw-bold">+<?= $fmt($newCertificatesMonth) ?></span> bulan ini
                    <?php if ($pendingApprovals > 0): ?>
                        &middot; <span class="text-danger-main fw-bold"><?= $fmt($pendingApprovals) ?></span> antrean
                    <?php else: ?>
                        &middot; <span class="text-success-main">Antrean nihil</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Card 4: Regulasi PBJ -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Regulasi PBJ</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($totalRegulations) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:scale-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-success-main fw-bold"><?= $fmt($activeRegulations) ?></span> berlaku
                    &middot; <?= $fmt($totalRegulationCat) ?> kategori
                </p>
            </div>
        </div>
    </div>

    <!-- Card 5: Bunga Rampai -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Bunga Rampai</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($totalArticles + $totalTemplates + $totalCaseStudies) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-emerald rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-bookmark-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-primary-600 fw-bold"><?= $fmt($totalArticles) ?></span> artikel
                    &middot; <span class="text-info-main fw-bold"><?= $fmt($totalTemplates) ?></span> template
                </p>
            </div>
        </div>
    </div>

    <!-- Card 6: Komunikasi & Event -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1 text-xs">Event & Agenda</p>
                        <h5 class="mb-0 fw-bold"><?= $fmt($totalEvents) ?></h5>
                    </div>
                    <div class="w-44-px h-44-px bg-orange rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:calendar-bold" class="text-white text-xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-2xs text-primary-light mt-12 mb-0 d-flex align-items-center gap-1">
                    <span class="text-success-main fw-bold"><?= $fmt($totalRegistrasiEvent) ?></span> pendaftar
                    &middot; <?= $fmt($totalNews) ?> berita
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. TREN PENDAFTARAN AKUN, ENROLLMENT KELAS & EVENT (12 BULAN) + STATUS AKUN -->
<!-- ========================================================================= -->
<div class="row gy-4 mb-24">
    <!-- Chart Multi-Series 12 Bulan -->
    <div class="col-xxl-8 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-20">
                    <div>
                        <h6 class="text-base fw-bold mb-1 text-primary-light">Tren Pendaftaran Akun, Enrollment Kelas &amp; Event</h6>
                        <p class="text-secondary-light text-xs mb-0">Dinamika pertumbuhan pengguna, keikutsertaan kelas pelatihan, dan pendaftaran event PBJ (12 Bulan Berjalan).</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-4 text-xs fw-semibold">
                            <i class="ri-user-add-line me-1"></i>+<?= $fmt($newUsersThisMonth) ?> Akun Bulan Ini
                        </span>
                        <span class="badge bg-success-50 text-success-600 radius-6 px-10 py-4 text-xs fw-semibold">
                            <i class="ri-graduation-cap-line me-1"></i>+<?= $fmt($newEnrollmentsMonth) ?> Kelas
                        </span>
                        <span class="badge bg-warning-50 text-warning-600 radius-6 px-10 py-4 text-xs fw-semibold">
                            <i class="ri-calendar-event-line me-1"></i>+<?= $fmt($newEventRegsMonth) ?> Event
                        </span>
                    </div>
                </div>
                <div id="begawiEnrollmentChart" class="pt-10"></div>
            </div>
        </div>
    </div>

    <!-- Donut Chart & Komposisi Akun & Profil Pengguna -->
    <div class="col-xxl-4 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-body p-24 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-16">
                        <div>
                            <h6 class="mb-1 fw-bold text-base text-primary-light">Status Akun Pengguna</h6>
                            <p class="text-secondary-light text-xs mb-0">Komposisi status autentikasi di sistem.</p>
                        </div>
                        <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="text-primary-600 text-xs hover-text-primary fw-semibold d-inline-flex align-items-center gap-1">
                            Kelola <iconify-icon icon="solar:arrow-right-linear" class="text-xs"></iconify-icon>
                        </a>
                    </div>
                    <div id="begawiAccountStatusChart" class="pt-6"></div>
                    <ul class="d-flex flex-wrap align-items-center justify-content-between mt-16 gap-2 mb-16 p-12 bg-neutral-50 radius-8 border">
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-10-px h-10-px rounded-circle bg-primary-600"></span>
                            <span class="text-secondary-light text-xs">Aktif: <strong class="text-primary-light"><?= $fmt($activeUsers) ?></strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-10-px h-10-px rounded-circle bg-yellow"></span>
                            <span class="text-secondary-light text-xs">Pending: <strong class="text-primary-light"><?= $fmt($pendingUsers) ?></strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-10-px h-10-px rounded-circle bg-red"></span>
                            <span class="text-secondary-light text-xs">Lainnya: <strong class="text-primary-light"><?= $fmt($otherUsers) ?></strong></span>
                        </li>
                    </ul>
                </div>
                <!-- Mini Table: Distribusi Tipe Profil -->
                <div class="border-top border-neutral-200 pt-16">
                    <span class="text-2xs fw-bold text-secondary-light text-uppercase mb-8 d-block letter-spacing-1">Distribusi Berdasarkan Tipe Profil</span>
                    <div class="d-flex flex-column gap-6">
                        <?php foreach ($usersByProfile as $pKey => $pTotal): ?>
                            <div class="d-flex align-items-center justify-content-between text-xs">
                                <span class="text-secondary-light d-flex align-items-center gap-1">
                                    <i class="ri-checkbox-blank-circle-fill text-primary-600 font-size-6"></i>
                                    <?= esc($profileLabelMap[$pKey] ?? ucfirst((string) $pKey)) ?>
                                </span>
                                <span class="fw-bold text-primary-light"><?= $fmt($pTotal) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. ARENA BELAJAR (LMS PELATIHAN PBJ)                                       -->
<!-- ========================================================================= -->
<div class="card radius-12 border border-neutral-200 mb-24 overflow-hidden">
    <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="w-36-px h-36-px bg-purple-50 text-purple-600 rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:square-academic-cap-2-bold" class="text-xl"></iconify-icon>
            </span>
            <div>
                <h6 class="mb-0 text-base fw-bold text-primary-light">Arena Belajar (LMS Pelatihan PBJ)</h6>
                <span class="text-2xs text-secondary-light">Pemantauan kelas aktif, silabus kurikulum, tingkat kelulusan, dan enrollment peserta</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-purple-50 text-purple-600 radius-6 px-10 py-4 text-xs fw-semibold">
                <?= $fmt($activeClasses) ?> Kelas Berjalan
            </span>
            <a href="<?= site_url('dashboard/pembelajaran/kelas') ?>" class="btn btn-sm btn-outline-primary py-4 px-10 radius-6 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                <span>Kelola Kelas</span>
                <iconify-icon icon="solar:arrow-right-linear" class="text-xs"></iconify-icon>
            </a>
        </div>
    </div>
    <div class="card-body p-20">
        <!-- Mini Stats Bar -->
        <div class="row row-cols-xxxl-5 row-cols-lg-5 row-cols-sm-2 row-cols-1 gy-3 mb-20">
            <div class="col">
                <div class="p-12 radius-8 border border-neutral-200 bg-neutral-50 text-center">
                    <span class="text-2xs text-secondary-light d-block">Total Program</span>
                    <h5 class="mb-0 fw-bold text-primary-light"><?= $fmt($totalPrograms) ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="p-12 radius-8 border border-neutral-200 bg-neutral-50 text-center">
                    <span class="text-2xs text-secondary-light d-block">Kursus / Silabus</span>
                    <h5 class="mb-0 fw-bold text-primary-light"><?= $fmt($totalCourses) ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="p-12 radius-8 border border-neutral-200 bg-neutral-50 text-center">
                    <span class="text-2xs text-secondary-light d-block">Modul Belajar</span>
                    <h5 class="mb-0 fw-bold text-primary-light"><?= $fmt($totalModules) ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="p-12 radius-8 border border-neutral-200 bg-neutral-50 text-center">
                    <span class="text-2xs text-secondary-light d-block">Total Enrollment</span>
                    <h5 class="mb-0 fw-bold text-purple-600"><?= $fmt($totalEnrollments) ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="p-12 radius-8 border border-neutral-200 bg-neutral-50 text-center">
                    <span class="text-2xs text-secondary-light d-block">Tingkat Kelulusan</span>
                    <h5 class="mb-0 fw-bold text-success-main"><?= $completionRate ?>%</h5>
                </div>
            </div>
        </div>

        <!-- Table Kelas Berjalan -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kode &amp; Nama Kelas</th>
                        <th>Kursus Induk</th>
                        <th class="text-center">Passing Grade</th>
                        <th>Peserta Terdaftar / Kuota</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activeClassList)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-20 text-secondary-light">
                                <iconify-icon icon="solar:book-2-outline" class="text-2xl d-block mb-1"></iconify-icon>
                                Belum ada kelas pembelajaran yang dijadwalkan / aktif.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activeClassList as $cls): ?>
                            <?php
                            $cap = (int) ($cls['capacity'] ?? 0);
                            $enr = (int) ($cls['enrolled_count'] ?? 0);
                            $pct = $cap > 0 ? min(100, round(($enr / $cap) * 100)) : 0;
                            ?>
                            <tr>
                                <td>
                                    <span class="badge bg-neutral-100 text-neutral-800 text-2xs px-6 py-2 radius-4 me-1 font-monospace"><?= esc($cls['code'] ?? '-') ?></span>
                                    <strong class="text-primary-light text-sm"><?= esc($cls['name'] ?? 'Kelas PBJ') ?></strong>
                                </td>
                                <td class="text-sm text-secondary-light">
                                    <?= esc($cls['course_title'] ?? 'Kursus Umum') ?>
                                </td>
                                <td class="text-center fw-semibold text-sm">
                                    <span class="badge bg-info-50 text-info-600 radius-4 text-xs"><?= esc((string) round((float) ($cls['passing_grade'] ?? 70))) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress h-6-px bg-neutral-100 radius-4 flex-grow-1" style="min-width: 60px;">
                                            <div class="progress-bar bg-primary-600 radius-4" role="progressbar" style="width: <?= $pct ?>%;"></div>
                                        </div>
                                        <span class="text-xs fw-semibold text-primary-light flex-shrink-0"><?= $enr ?> / <?= $cap > 0 ? $cap : '∞' ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $cls['status']]) ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('dashboard/pembelajaran/kelas/' . (int) $cls['id'] . '/ubah') ?>" class="btn btn-xs btn-outline-primary py-2 px-8 radius-4 text-2xs">
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

<!-- ========================================================================= -->
<!-- 5. DAFTAR EVENT & AGENDA PBJ MENDATANG                                    -->
<!-- ========================================================================= -->
<div class="card radius-12 border border-neutral-200 mb-24 overflow-hidden">
    <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="w-36-px h-36-px bg-orange-50 text-orange-600 rounded-circle d-flex justify-content-center align-items-center">
                <iconify-icon icon="solar:calendar-bold" class="text-xl"></iconify-icon>
            </span>
            <div>
                <h6 class="mb-0 text-base fw-bold text-primary-light">Daftar Event &amp; Agenda Mendatang</h6>
                <span class="text-2xs text-secondary-light">Jadwal bimtek, workshop, webinar, dan sosialisasi pengadaan barang/jasa</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning-50 text-warning-600 radius-6 px-10 py-4 text-xs fw-semibold">
                <?= $fmt($totalEvents) ?> Total Event Terdaftar
            </span>
            <a href="<?= site_url('dashboard/komunikasi/event-agenda') ?>" class="btn btn-sm btn-outline-primary py-4 px-10 radius-6 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                <span>Kelola Event</span>
                <iconify-icon icon="solar:arrow-right-linear" class="text-xs"></iconify-icon>
            </a>
        </div>
    </div>
    <div class="card-body p-20">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Judul Event &amp; Tipe</th>
                        <th>Mode &amp; Lokasi</th>
                        <th>Jadwal Pelaksanaan</th>
                        <th class="text-center">Peserta / Kuota</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($upcomingEvents)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-20 text-secondary-light">
                                <iconify-icon icon="solar:calendar-outline" class="text-2xl d-block mb-1"></iconify-icon>
                                Belum ada event atau agenda yang dijadwalkan saat ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($upcomingEvents as $evt): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-50 text-primary-600 text-2xs px-6 py-2 radius-4 text-uppercase fw-bold">
                                            <?= esc($evt['type'] ?? 'Event') ?>
                                        </span>
                                        <strong class="text-primary-light text-sm"><?= esc($evt['title']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-neutral-800 text-2xs px-6 py-2 radius-4 text-uppercase">
                                        <?= esc($evt['mode'] ?? 'Online') ?>
                                    </span>
                                    <span class="text-xs text-secondary-light ms-1"><?= esc($evt['location'] ?: 'Zoom / Daring') ?></span>
                                </td>
                                <td class="text-xs text-secondary-light">
                                    <i class="ri-time-line me-1"></i><?= esc(date('d M Y H:i', strtotime($evt['start_at'] ?? 'now'))) ?> WIB
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-50 text-success-600 radius-4 text-xs fw-semibold">
                                        <?= (int) ($evt['registered_count'] ?? 0) ?> / <?= !empty($evt['capacity']) ? (int) $evt['capacity'] : '∞' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $evt['status']]) ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('dashboard/komunikasi/event-agenda/' . (int) $evt['id'] . '/peserta') ?>" class="btn btn-xs btn-outline-primary py-2 px-8 radius-4 text-2xs">
                                        Peserta
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

<!-- ========================================================================= -->
<!-- 6. BUNGA RAMPAI & REGULASI PBJ (2 KOLOM BERDAMPINGAN)                     -->
<!-- ========================================================================= -->
<div class="row gy-4 mb-24">
    <!-- Kolom Kiri: Bunga Rampai (Knowledge Management) -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="w-32-px h-32-px bg-emerald-50 text-emerald-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-bookmark-bold" class="text-lg"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="mb-0 text-sm fw-bold text-primary-light">Bunga Rampai (Knowledge Base)</h6>
                        <span class="text-2xs text-secondary-light">Artikel literasi, tanya jawab, studi kasus &amp; template KAK/HPS</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold">
                        Kelola <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-20">
                <!-- Mini Stats Bar KMS -->
                <div class="d-flex flex-wrap align-items-center justify-content-between p-12 bg-neutral-50 radius-8 border border-neutral-200 mb-16 gap-2">
                    <div class="text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Artikel</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalArticles) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">FAQ</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalFaqs) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Studi Kasus</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalCaseStudies) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Template PBJ</span>
                        <strong class="text-sm text-emerald-600"><?= $fmt($totalTemplates) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Media Visual</span>
                        <strong class="text-sm text-purple-600"><?= $fmt($totalMedia) ?></strong>
                    </div>
                </div>

                <!-- Latest KMS Entries List -->
                <div class="d-flex flex-column gap-10">
                    <?php if (empty($latestArticles)): ?>
                        <p class="text-secondary-light text-xs text-center py-16 mb-0">Belum ada konten artikel / template di Bunga Rampai.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($latestArticles, 0, 5) as $art): ?>
                            <div class="p-10 radius-8 border border-neutral-200 bg-base d-flex align-items-start gap-10">
                                <span class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:document-text-outline" class="text-md"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <h6 class="text-xs fw-semibold text-truncate mb-0 text-primary-light">
                                            <?= esc($art['title']) ?>
                                        </h6>
                                        <span class="badge bg-neutral-100 text-neutral-800 text-2xs flex-shrink-0 radius-4 text-uppercase">
                                            <?= esc($art['type']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-2xs text-secondary-light">
                                        <span>Penulis: <?= esc($art['author_name'] ?? 'Admin') ?></span>
                                        <span><?= esc(date('d M Y', strtotime($art['created_at']))) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Regulasi PBJ (Legal Docs) -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="w-32-px h-32-px bg-info-50 text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:scale-bold" class="text-lg"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="mb-0 text-sm fw-bold text-primary-light">Regulasi PBJ (Repositori Hukum)</h6>
                        <span class="text-2xs text-secondary-light">Perpres, Perlem LKPP, Pergub Lampung, dan Standar Dokumen</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold">
                        Kelola <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-20">
                <!-- Mini Stats Bar Regulasi -->
                <div class="d-flex flex-wrap align-items-center justify-content-between p-12 bg-neutral-50 radius-8 border border-neutral-200 mb-16 gap-2">
                    <div class="text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Total Dokumen</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalRegulations) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Berlaku</span>
                        <strong class="text-sm text-success-main"><?= $fmt($activeRegulations) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Dicabut / Diubah</span>
                        <strong class="text-sm text-warning-main"><?= $fmt($revokedRegulations) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Kategori</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalRegulationCat) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Versi / Naskah</span>
                        <strong class="text-sm text-info-main"><?= $fmt($totalVersions) ?></strong>
                    </div>
                </div>

                <!-- Latest Regulations List -->
                <div class="d-flex flex-column gap-10">
                    <?php if (empty($latestRegulations)): ?>
                        <p class="text-secondary-light text-xs text-center py-16 mb-0">Belum ada dokumen regulasi yang dipublikasikan.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($latestRegulations, 0, 5) as $reg): ?>
                            <div class="p-10 radius-8 border border-neutral-200 bg-base d-flex align-items-start gap-10">
                                <span class="w-32-px h-32-px bg-info-50 text-info-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:clipboard-text-outline" class="text-md"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <h6 class="text-xs fw-semibold text-truncate mb-0 text-primary-light">
                                            <?= esc($reg['title']) ?>
                                        </h6>
                                        <?php if (($reg['legal_status'] ?? '') === 'active'): ?>
                                            <span class="badge bg-success-50 text-success-600 text-2xs radius-4">Berlaku</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-50 text-warning-600 text-2xs radius-4"><?= esc(ucfirst((string) ($reg['legal_status'] ?? 'Diubah'))) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-2xs text-secondary-light">
                                        <span>No. <?= esc($reg['document_number'] ?? '-') ?> (<?= esc((string) ($reg['document_year'] ?? '-')) ?>)</span>
                                        <span><?= esc($reg['category_name'] ?? 'Regulasi') ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 7. KOMUNIKASI & SERTIFIKAT PBJ (2 KOLOM BERDAMPINGAN)                     -->
<!-- ========================================================================= -->
<div class="row gy-4 mb-24">
    <!-- Kolom Kiri: Komunikasi (Berita & Siaran Pers) -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="w-32-px h-32-px bg-pink-50 text-pink-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:newspaper-bold" class="text-lg"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="mb-0 text-sm fw-bold text-primary-light">Komunikasi &amp; Siaran Berita</h6>
                        <span class="text-2xs text-secondary-light">Warta pengadaan, siaran pers, dan rilis kebijakan Pemprov</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('dashboard/komunikasi/berita') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold">
                        Kelola <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-20">
                <!-- Mini Stats Bar Berita -->
                <div class="d-flex flex-wrap align-items-center justify-content-between p-12 bg-neutral-50 radius-8 border border-neutral-200 mb-16 gap-2">
                    <div class="text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Berita Terbit</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalNews) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Kategori Berita</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($totalNewsCat) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Event Terlaksana</span>
                        <strong class="text-sm text-purple-600"><?= $fmt($totalEvents) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Broadcast Notif</span>
                        <strong class="text-sm text-info-main"><?= $fmt($totalBroadcasts) ?></strong>
                    </div>
                </div>

                <!-- Latest News List -->
                <div class="d-flex flex-column gap-10">
                    <?php if (empty($latestNews)): ?>
                        <p class="text-secondary-light text-xs text-center py-16 mb-0">Belum ada rilis berita kepengadaan.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($latestNews, 0, 5) as $news): ?>
                            <div class="p-10 radius-8 border border-neutral-200 bg-base d-flex align-items-start gap-10">
                                <span class="w-32-px h-32-px bg-pink-50 text-pink-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-md"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <h6 class="text-xs fw-semibold text-truncate mb-0 text-primary-light">
                                            <?= esc($news['title']) ?>
                                        </h6>
                                        <span class="badge bg-pink-50 text-pink-600 text-2xs flex-shrink-0 radius-4">
                                            <?= esc($news['category_name'] ?? 'Berita') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-2xs text-secondary-light">
                                        <span>Alur: <?= esc($news['workflow_status']) ?></span>
                                        <span><?= esc(date('d M Y', strtotime($news['created_at']))) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Sertifikat PBJ & Verifikasi QR -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-header bg-base border-bottom border-neutral-200 p-20 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:medal-ribbons-star-bold" class="text-lg"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="mb-0 text-sm fw-bold text-primary-light">Sertifikat PBJ &amp; Integritas QR</h6>
                        <span class="text-2xs text-secondary-light">Sertifikat kelulusan kelas pelatihan dan kehadiran event</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('dashboard/sertifikat') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold">
                        Kelola <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-20">
                <!-- Mini Stats Bar Sertifikat -->
                <div class="d-flex flex-wrap align-items-center justify-content-between p-12 bg-neutral-50 radius-8 border border-neutral-200 mb-16 gap-2">
                    <div class="text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Total Terbit</span>
                        <strong class="text-sm text-primary-light"><?= $fmt($issuedCertificates) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Bulan Ini</span>
                        <strong class="text-sm text-success-main">+<?= $fmt($newCertificatesMonth) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Antrean Approval</span>
                        <strong class="text-sm <?= $pendingApprovals > 0 ? 'text-danger-main' : 'text-success-main' ?>"><?= $fmt($pendingApprovals) ?></strong>
                    </div>
                    <div class="border-start border-neutral-200 ps-12 text-center flex-grow-1">
                        <span class="text-2xs text-secondary-light d-block">Verifikasi QR</span>
                        <strong class="text-sm text-primary-600">100% Valid</strong>
                    </div>
                </div>

                <!-- Latest Issued Certificates List -->
                <div class="d-flex flex-column gap-10">
                    <?php if (empty($latestCertificates)): ?>
                        <p class="text-secondary-light text-xs text-center py-16 mb-0">Belum ada sertifikat yang diterbitkan.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($latestCertificates, 0, 5) as $cert): ?>
                            <div class="p-10 radius-8 border border-neutral-200 bg-base d-flex align-items-start gap-10">
                                <span class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:diploma-outline" class="text-md"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <h6 class="text-xs fw-semibold text-truncate mb-0 text-primary-light">
                                            <?= esc($cert['recipient_name'] ?? $cert['recipient_email'] ?? 'Peserta') ?>
                                        </h6>
                                        <span class="badge bg-success-50 text-success-600 text-2xs flex-shrink-0 radius-4">
                                            <?= esc($cert['status']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-2xs text-secondary-light">
                                        <span class="font-monospace"><?= esc($cert['certificate_number']) ?></span>
                                        <span><?= esc(date('d M Y', strtotime($cert['issued_at'] ?? 'now'))) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 8. ANTREAN PERSETUJUAN & LOG AUDIT AKTIVITAS TERKINI                       -->
<!-- ========================================================================= -->
<div class="row gy-4 mb-24">
    <!-- Antrean Persetujuan Terkini -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between mb-16">
                    <div>
                        <h6 class="mb-1 text-base fw-bold text-primary-light">Antrean Persetujuan &amp; Approval</h6>
                        <p class="text-secondary-light text-xs mb-0">Permohonan berkas, sertifikat, dan akun yang memerlukan tindakan persetujuan.</p>
                    </div>
                    <a href="<?= site_url('dashboard/sistem/approval') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold d-flex align-items-center gap-1">
                        Pusat Approval <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
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
                                <?php foreach (array_slice($approvalQueue, 0, 5) as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-primary-light text-xs"><?= esc(ucfirst((string) $row['resource_type'])) ?></span>
                                            <span class="text-2xs text-secondary-light d-block"><?= esc(str_replace('_', ' ', (string) $row['requested_action'])) ?></span>
                                        </td>
                                        <td class="text-xs">
                                            <?= esc($row['requester_name'] ?? $row['requester_username'] ?? $row['requester_email'] ?? '-') ?>
                                        </td>
                                        <td class="text-2xs text-secondary-light">
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

    <!-- Aktivitas Sistem & Audit Trail -->
    <div class="col-xxl-6 col-xl-12">
        <div class="card h-100 radius-12 border border-neutral-200">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-16">
                    <div>
                        <h6 class="mb-1 text-base fw-bold text-primary-light">Aktivitas Sistem &amp; Log Audit</h6>
                        <p class="text-secondary-light text-xs mb-0">Log aksi operasional terakhir pengguna dan jejak keamanan di back-office.</p>
                    </div>
                    <a href="<?= site_url('dashboard/sistem/audit-trail') ?>" class="text-primary-600 hover-text-primary text-xs fw-semibold d-flex align-items-center gap-1">
                        Audit Trail Lengkap <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>

                <div class="d-flex flex-column gap-12">
                    <?php if (empty($recentActivity)): ?>
                        <p class="text-secondary-light text-xs mb-0 text-center py-20">Belum ada aktivitas tercatat di audit log.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($recentActivity, 0, 5) as $entry): ?>
                            <div class="d-flex gap-10 align-items-start p-10 radius-8 border border-neutral-200 bg-neutral-50">
                                <span class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mt-2">
                                    <iconify-icon icon="solar:history-2-outline" class="text-md"></iconify-icon>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <h6 class="text-xs fw-semibold mb-1 text-primary-light text-truncate">
                                            <?= esc($entry['action']) ?>
                                        </h6>
                                        <span class="text-2xs text-secondary-light flex-shrink-0">
                                            <?= esc(date('d M H:i', strtotime($entry['created_at']))) ?>
                                        </span>
                                    </div>
                                    <p class="text-2xs text-secondary-light mb-0">
                                        Oleh: <strong class="text-neutral-700"><?= esc($entry['actor'] ?? ($entry['actor_username'] ?? 'Sistem')) ?></strong>
                                        <?php if (! empty($entry['resource_type'])): ?>
                                            &middot; <?= esc($entry['resource_type']) ?>#<?= esc((string) ($entry['resource_id'] ?? '')) ?>
                                        <?php endif; ?>
                                        <?php if (! empty($entry['result'])): ?>
                                            &middot; <span class="text-success-main fw-semibold"><?= esc($entry['result']) ?></span>
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

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    (function() {
        // Multi-Series Area Chart: Tren Pendaftaran Akun, Enrollment Kelas & Event
        const enrollmentTarget = document.querySelector('#begawiEnrollmentChart');
        const monthly = <?= json_encode($enrollmentMonthly, JSON_UNESCAPED_UNICODE) ?>;
        if (enrollmentTarget && window.ApexCharts && monthly && Array.isArray(monthly.registration)) {
            new ApexCharts(enrollmentTarget, {
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                        name: 'Pendaftaran Akun',
                        data: monthly.registration || []
                    },
                    {
                        name: 'Enrollment Kelas',
                        data: monthly.enrollment || []
                    },
                    {
                        name: 'Pendaftaran Event',
                        data: monthly.eventRegistration || []
                    }
                ],
                colors: ['#487FFF', '#16A34A', '#F59E0B'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: [3, 3, 2.5]
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
                    categories: monthly.categories || [],
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: value => Math.round(value),
                        style: {
                            colors: '#6B7280',
                            fontSize: '11px'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontWeight: 500,
                    fontSize: '12px',
                    markers: {
                        radius: 12
                    }
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
                    height: 230,
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
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Akun',
                                    formatter: () => String(donut.total),
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 700,
                                        color: '#374151'
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
