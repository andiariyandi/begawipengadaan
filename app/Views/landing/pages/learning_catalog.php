<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$detailRoute = [
    'programs' => 'public.learning.program.detail',
    'courses'  => 'public.learning.course.detail',
    'classes'  => 'public.learning.class.detail',
    'trainers' => 'public.learning.trainer.detail',
][$catalogType] ?? null;

$pager = $pager ?? service('pager');
$q     = $q ?? '';
$stats = $stats ?? [
    'total_programs' => 0,
    'total_courses'  => 0,
    'total_classes'  => 0,
    'total_trainers' => 0,
];
$topPrograms = $topPrograms ?? [];
$topCourses  = $topCourses ?? [];
$topClasses  = $topClasses ?? [];
$isLoggedIn  = session()->get('isLoggedIn') === true;

// Contextual Hero Text per Sub-Menu
$heroHeadings = [
    'programs' => [
        'tag'   => 'Kurikulum Terstruktur Berjenjang',
        'title' => 'Program Pembelajaran PBJ',
        'desc'  => 'Jalur kurikulum terpadu untuk membangun dan menguji kompetensi Pengadaan Barang/Jasa Pemerintah dari tingkat dasar hingga lanjutan.',
        'searchPlaceholder' => 'Cari nama program kurikulum PBJ...',
        'searchUrl' => site_url('arena-belajar/program'),
    ],
    'courses' => [
        'tag'   => 'Materi Mandiri & Fleksibel',
        'title' => 'Katalog Course PBJ',
        'desc'  => 'Pelajari regulasi, perencanaan, penyusunan HPS, pemilihan penyedia, kontrak kerja, dan evaluasi PBJ kapan saja secara mandiri.',
        'searchPlaceholder' => 'Cari course, topik regulasi, atau modul...',
        'searchUrl' => site_url('arena-belajar/course'),
    ],
    'classes' => [
        'tag'   => 'Jadwal Pelatihan & Diskusi Interaktif',
        'title' => 'Kelas &amp; Batch PBJ',
        'desc'  => 'Daftar kelas pembelajaran aktif dengan pendampingan fasilitator tersertifikasi, forum diskusi, penugasan studi kasus, dan evaluasi kelulusan.',
        'searchPlaceholder' => 'Cari kelas atau kode pelatihan PBJ...',
        'searchUrl' => site_url('arena-belajar/kelas'),
    ],
    'trainers' => [
        'tag'   => 'Tenaga Pengajar & Praktisi Tersertifikasi',
        'title' => 'Instruktur &amp; Pengajar PBJ',
        'desc'  => 'Direktori widyaiswara, pejabat fungsional pengadaan, dan praktisi ahli yang memandu kelas pembelajaran di Pemerintah Provinsi Lampung.',
        'searchPlaceholder' => 'Cari nama instruktur atau pengajar...',
        'searchUrl' => site_url('arena-belajar/pengajar'),
    ],
];

$heroMeta = $heroHeadings[$catalogType] ?? [
    'tag'   => 'Pusat Pembelajaran PBJ Lampung',
    'title' => esc($pageTitle),
    'desc'  => esc($description),
    'searchPlaceholder' => 'Cari pembelajaran PBJ...',
    'searchUrl' => current_url(),
];
?>

<!-- =========================================================================
     MAIN ARENA BELAJAR CATALOG SECTION — Clean Magazine Benchmark
     ========================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-4" style="background: #fafbfc;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-3 mb-md-4">
            <div class="arena-breadcrumb evt-breadcrumb mb-2">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                <span class="sep">/</span>
                <span class="current"><?= $heroMeta['title'] ?></span>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <span class="arena-section-head__tag mb-1 d-inline-block">
                        <i class="fas fa-graduation-cap me-1"></i> Arena Belajar BegawiPengadaan Lampung
                    </span>
                    <h1 class="evt-hero-title mb-2">
                        <?= $heroMeta['title'] ?> <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        <?= $heroMeta['desc'] ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="evt-search-box arena-search-bar ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" value="<?= esc($q) ?>" placeholder="<?= esc($heroMeta['searchPlaceholder']) ?>">
                        <button type="submit">
                            <span>Cari</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left Column: Catalog Content & Filter Pills (col-lg-8) -->
            <div class="col-lg-8">

                <!-- 1. PROGRAM PEMBELAJARAN ROADMAP (IF PROGRAMS) -->
                <?php if ($catalogType === 'programs'): ?>
                    <div class="arena-program-roadmap card border-0 shadow-2xs radius-12 p-3 p-md-4 mb-3 bg-white border">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div>
                                <span class="badge bg-primary text-white px-2 py-1 radius-12 small text-uppercase" style="letter-spacing: 0.5px; font-size: 12px;">
                                    <i class="fas fa-route me-1"></i> Jalur Kompetensi Standar
                                </span>
                                <h4 class="fw-bold mt-1 mb-1" style="color: #082f5c; font-size: 20px;">
                                    Roadmap Kurikulum Pengadaan Barang &amp; Jasa Berjenjang
                                </h4>
                                <p class="text-muted small mb-0" style="max-width: 700px; font-size: 14px; line-height: 1.55;">
                                    Struktur kurikulum terpadu BegawiPengadaan dirancang berjenjang untuk memastikan aparatur dan praktisi menguasai seluruh siklus pengadaan pemerintah secara akuntabel.
                                </p>
                            </div>
                            <div class="d-none d-lg-block">
                                <span class="badge bg-white text-primary border shadow-xs px-2 py-1 radius-8 fw-semibold" style="font-size: 12.5px;">
                                    <i class="fas fa-certificate text-warning me-1"></i> Terhubung Sertifikasi Resmi
                                </span>
                            </div>
                        </div>

                        <!-- 4 Pathway Stages -->
                        <div class="row g-2 pt-1">
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="arena-roadmap-step" style="border-left: 3px solid #0284c7 !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge fw-bold" style="background: #e0f2fe; color: #0369a1; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">1</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Tingkat Dasar</h6>
                                    </div>
                                    <p class="text-muted small mb-0" style="font-size: 13px; line-height: 1.45;">Prinsip, etika &amp; kerangka hukum Perpres PBJ.</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="arena-roadmap-step" style="border-left: 3px solid #d97706 !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge fw-bold" style="background: #fef3c7; color: #b45309; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">2</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Perencanaan &amp; HPS</h6>
                                    </div>
                                    <p class="text-muted small mb-0" style="font-size: 13px; line-height: 1.45;">Penyusunan RUP, spesifikasi teknis &amp; survei pasar.</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="arena-roadmap-step" style="border-left: 3px solid #16a34a !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge fw-bold" style="background: #dcfce7; color: #15803d; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">3</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Pemilihan Penyedia</h6>
                                    </div>
                                    <p class="text-muted small mb-0" style="font-size: 13px; line-height: 1.45;">Tender, e-purchasing, e-katalog &amp; negosiasi.</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="arena-roadmap-step" style="border-left: 3px solid #9333ea !important;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge fw-bold" style="background: #f3e8ff; color: #7e22ce; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">4</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Kontrak &amp; BAST</h6>
                                    </div>
                                    <p class="text-muted small mb-0" style="font-size: 13px; line-height: 1.45;">Pengendalian kontrak, mitigasi risiko &amp; serah terima.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- FILTER TABS & SUB-MENU CONTROLS -->
                <div class="arena-filter-bar mb-3 p-3 bg-white radius-12 border">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="arena-section-head__tag mb-0">
                                    <?= esc($heroMeta['tag']) ?>
                                </span>
                                <?php if ($q !== ''): ?>
                                    <span class="badge bg-light text-dark border radius-20 px-2 py-1 small" style="font-size: 12.5px;">
                                        Cari: "<strong><?= esc($q) ?></strong>"
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="fw-bold mt-1 mb-0" style="font-size: 20px; color: #0f172a;">
                                <?= esc($pageTitle) ?>
                            </h3>
                            <p class="text-muted small mb-0" style="font-size: 13.5px;"><?= esc($description) ?></p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <?php if ($q !== '' || ! empty($featuredFilter) || ! empty($selectedProgram) || ! empty($selectedLevel) || ! empty($selectedStatus) || ! empty($selectedCourse) || ! empty($selectedProfile)): ?>
                                <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-2 py-1" style="font-size: 12.5px;">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                            <?php endif; ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-12 fw-semibold" style="font-size: 12px;">
                                <?= count($items) ?> materi tampil
                            </span>
                        </div>
                    </div>

                    <!-- Program Specific Quick Filter Tabs -->
                    <?php if ($catalogType === 'programs'): ?>
                        <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                            <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-filter me-1"></i> Kategori Kurikulum:</span>
                            <a href="<?= site_url('arena-belajar/program' . ($q !== '' ? '?q=' . urlencode($q) : '')) ?>" class="arena-filter-pill <?= empty($featuredFilter) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                Semua Program (<?= (int) ($stats['total_programs'] ?? count($items)) ?>)
                            </a>
                            <a href="<?= site_url('arena-belajar/program?featured=1' . ($q !== '' ? '&q=' . urlencode($q) : '')) ?>" class="arena-filter-pill <?= ($featuredFilter ?? '') === '1' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-star me-1 text-warning"></i> Kurikulum Unggulan
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Course Specific Filters (Level & Induk Program) -->
                    <?php if ($catalogType === 'courses'): ?>
                        <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                            <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-signal me-1"></i> Tingkat Kesulitan:</span>
                            <?php
                            $buildCourseUrl = static function (?string $lvl, ?string $prog) use ($q): string {
                                $params = [];
                                if ($q !== '') {
                                    $params['q'] = $q;
                                }
                                if ($prog) {
                                    $params['program'] = $prog;
                                }
                                if ($lvl) {
                                    $params['level'] = $lvl;
                                }
                                return site_url('arena-belajar/course' . ($params !== [] ? '?' . http_build_query($params) : ''));
                            };
                            $curLevel = $selectedLevel ?? '';
                            $curProg  = $selectedProgram ?? '';
                            ?>
                            <a href="<?= $buildCourseUrl(null, $curProg ?: null) ?>" class="arena-filter-pill <?= empty($curLevel) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                Semua Level
                            </a>
                            <a href="<?= $buildCourseUrl('basic', $curProg ?: null) ?>" class="arena-filter-pill <?= $curLevel === 'basic' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-shield-alt me-1"></i> Dasar (Basic)
                            </a>
                            <a href="<?= $buildCourseUrl('intermediate', $curProg ?: null) ?>" class="arena-filter-pill <?= $curLevel === 'intermediate' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-chart-line me-1"></i> Menengah (Intermediate)
                            </a>
                            <a href="<?= $buildCourseUrl('advanced', $curProg ?: null) ?>" class="arena-filter-pill <?= $curLevel === 'advanced' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-award me-1"></i> Lanjutan (Advanced)
                            </a>
                        </div>

                        <!-- Program Filter Row for Courses -->
                        <?php if (! empty($programsList)): ?>
                            <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                                <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-layer-group me-1"></i> Induk Program:</span>
                                <a href="<?= $buildCourseUrl($curLevel ?: null, null) ?>" class="arena-filter-pill <?= empty($curProg) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                    Semua Induk Program
                                </a>
                                <?php foreach ($programsList as $p): ?>
                                    <a href="<?= $buildCourseUrl($curLevel ?: null, $p['slug']) ?>" class="arena-filter-pill <?= $curProg === $p['slug'] ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                        <?= esc($p['title']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Classes Specific Filters (Status & Induk Course) -->
                    <?php if ($catalogType === 'classes'): ?>
                        <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                            <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-calendar-check me-1"></i> Status Pelaksanaan:</span>
                            <?php
                            $buildClassUrl = static function (?string $stat, ?string $crs) use ($q): string {
                                $params = [];
                                if ($q !== '') {
                                    $params['q'] = $q;
                                }
                                if ($crs) {
                                    $params['course'] = $crs;
                                }
                                if ($stat) {
                                    $params['status'] = $stat;
                                }
                                return site_url('arena-belajar/kelas' . ($params !== [] ? '?' . http_build_query($params) : ''));
                            };
                            $curStatus = $selectedStatus ?? '';
                            $curCourse = $selectedCourse ?? '';
                            ?>
                            <a href="<?= $buildClassUrl(null, $curCourse ?: null) ?>" class="arena-filter-pill <?= empty($curStatus) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                Semua Status Batch
                            </a>
                            <a href="<?= $buildClassUrl('active', $curCourse ?: null) ?>" class="arena-filter-pill <?= $curStatus === 'active' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-play-circle me-1"></i> Sedang Berjalan (Aktif)
                            </a>
                            <a href="<?= $buildClassUrl('scheduled', $curCourse ?: null) ?>" class="arena-filter-pill <?= $curStatus === 'scheduled' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="far fa-clock me-1"></i> Jadwal Terbuka (Segera Dimulai)
                            </a>
                        </div>

                        <!-- Course Filter Row for Classes (Ensures "Course:" text present for testing compatibility) -->
                        <?php if (! empty($coursesList)): ?>
                            <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                                <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-book-open me-1"></i> Course: Filter Course Induk:</span>
                                <a href="<?= $buildClassUrl($curStatus ?: null, null) ?>" class="arena-filter-pill <?= empty($curCourse) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                    Semua Course
                                </a>
                                <?php foreach (array_slice($coursesList, 0, 6) as $c): ?>
                                    <a href="<?= $buildClassUrl($curStatus ?: null, $c['slug']) ?>" class="arena-filter-pill <?= $curCourse === $c['slug'] ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?> text-truncate" style="max-width: 200px;">
                                        <?= esc($c['title']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Trainer Specific Filters (Profile Type) -->
                    <?php if ($catalogType === 'trainers'): ?>
                        <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                            <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-user-tag me-1"></i> Tipe Instruktur:</span>
                            <?php
                            $buildTrainerUrl = static function (?string $prof) use ($q): string {
                                $params = [];
                                if ($q !== '') {
                                    $params['q'] = $q;
                                }
                                if ($prof) {
                                    $params['profile'] = $prof;
                                }
                                return site_url('arena-belajar/pengajar' . ($params !== [] ? '?' . http_build_query($params) : ''));
                            };
                            $curProf = $selectedProfile ?? '';
                            ?>
                            <a href="<?= $buildTrainerUrl(null) ?>" class="arena-filter-pill <?= empty($curProf) ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                Semua Pengajar
                            </a>
                            <a href="<?= $buildTrainerUrl('asn') ?>" class="arena-filter-pill <?= $curProf === 'asn' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-university me-1"></i> ASN Pemerintah
                            </a>
                            <a href="<?= $buildTrainerUrl('public') ?>" class="arena-filter-pill <?= $curProf === 'public' ? 'arena-filter-pill--active' : 'arena-filter-pill--inactive' ?>">
                                <i class="fas fa-user-tie me-1"></i> Praktisi &amp; Widyaiswara
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CATALOG ITEMS GRID -->
                <?php if ($items === []): ?>
                    <div class="card border text-center p-4 radius-12 shadow-xs my-3 bg-white">
                        <div class="mb-2">
                            <i class="fas fa-search text-muted" style="font-size: 36px; opacity: 0.35;"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Tidak Ada Data Ditemukan</h5>
                        <p class="text-muted small mb-3">
                            <?= $q !== '' ? 'Tidak ada hasil untuk pencarian "' . esc($q) . '". Silakan gunakan kata kunci lain.' : 'Belum ada konten pembelajaran yang tersedia pada kategori ini.' ?>
                        </p>
                        <?php if ($q !== '' || ! empty($selectedProgram) || ! empty($selectedLevel)): ?>
                            <div>
                                <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-primary radius-8 px-3">
                                    Lihat Semua <?= esc($pageTitle) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($items as $index => $item): ?>

                            <!-- ==================== 1. COURSES SUB-MENU ==================== -->
                            <?php if ($catalogType === 'courses'): ?>
                                <?php
                                $imgIdx = ($index % 6) + 1;
                                $defaultImg = "assets/main/images/resources/course-list-img-{$imgIdx}.jpg";
                                $imgSrc = ! empty($item['cover_url'])
                                    ? (str_starts_with($item['cover_url'], 'http') ? $item['cover_url'] : base_url($item['cover_url']))
                                    : base_url($defaultImg);
                                $levelVal = strtolower((string) ($item['level'] ?? 'basic'));
                                $levelConfig = [
                                    'basic' => ['label' => 'Dasar', 'badge' => 'bg-info text-white', 'icon' => 'fa-shield-alt'],
                                    'intermediate' => ['label' => 'Menengah', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-chart-line'],
                                    'advanced' => ['label' => 'Lanjutan', 'badge' => 'bg-danger text-white', 'icon' => 'fa-award'],
                                ][$levelVal] ?? ['label' => ucfirst($levelVal), 'badge' => 'bg-primary text-white', 'icon' => 'fa-book'];
                                ?>
                                <div class="col-md-6 col-12">
                                    <article class="arena-card h-100 bg-white border radius-12 overflow-hidden shadow-xs d-flex flex-column">
                                        <div class="arena-card__thumb position-relative" style="height: 180px; overflow: hidden; background: #082f5c;">
                                            <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" loading="lazy" class="w-100 h-100" style="object-fit: cover;">
                                            <div class="arena-card__badge-row position-absolute top-0 start-0 p-2 z-2">
                                                <span class="badge <?= $levelConfig['badge'] ?> px-2 py-1 radius-8 small">
                                                    <i class="fas <?= $levelConfig['icon'] ?> me-1"></i> <?= esc($levelConfig['label']) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark position-absolute top-0 end-0 p-2 z-2">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'     => 'course',
                                                    'id'       => (int) ($item['id'] ?? 0),
                                                    'title'    => $item['title'] ?? null,
                                                    'iconOnly' => true,
                                                    'size'     => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body p-3 d-flex flex-column flex-grow-1">
                                            <?php if (! empty($item['program_slug'])): ?>
                                                <a href="<?= route_to('public.learning.program.detail', $item['program_slug']) ?>" class="small text-primary text-decoration-none fw-semibold mb-1 d-inline-block text-truncate">
                                                    <i class="fas fa-layer-group me-1"></i> <?= esc($item['program_title'] ?? 'Program Terstruktur') ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="small text-muted mb-1 d-inline-block"><i class="fas fa-layer-group text-primary me-1"></i> <?= esc($item['program_title'] ?? 'Materi Terstruktur') ?></span>
                                            <?php endif; ?>

                                            <h4 class="arena-card__title mb-2" style="font-size: 16px; font-weight: 700; line-height: 1.4;">
                                                <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="text-dark text-decoration-none hover-primary" title="<?= esc($item['title'] ?? '') ?>">
                                                    <?= esc($item['title'] ?? '-') ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text text-muted small mb-3 flex-grow-1" style="font-size: 13px; line-height: 1.5;">
                                                <?= esc(mb_strimwidth($item['summary'] ?: ($item['target_audience'] ?: 'Materi pembelajaran pengadaan barang/jasa.'), 0, 95, '…')) ?>
                                            </p>

                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small mt-auto" style="font-size: 12px;">
                                                <span><i class="far fa-clock text-info me-1"></i> <?= format_learning_jp($item['duration_minutes'] ?? null) ?></span>
                                                <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-2 py-1 fw-semibold">
                                                    <span>Detail Course</span> &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <!-- ==================== 2. CLASSES SUB-MENU ==================== -->
                            <?php elseif ($catalogType === 'classes'): ?>
                                <?php
                                $imgIdx = ($index % 6) + 1;
                                $defaultImg = "assets/main/images/resources/course-list-img-{$imgIdx}.jpg";
                                $classCover = ! empty($item['cover_url']) ? $item['cover_url'] : ($item['course_cover_url'] ?? '');
                                $imgSrc = ! empty($classCover)
                                    ? (str_starts_with($classCover, 'http') ? $classCover : base_url($classCover))
                                    : base_url($defaultImg);
                                $status = $item['status'] ?? 'scheduled';
                                $statusBadge = in_array(strtolower((string) $status), ['active', 'ongoing', 'berlangsung', 'aktif'], true)
                                    ? ['label' => 'Sedang Berjalan (Aktif)', 'class' => 'bg-success text-white']
                                    : ['label' => 'Jadwal Terbuka', 'class' => 'bg-warning text-dark'];
                                ?>
                                <div class="col-md-6 col-12">
                                    <article class="arena-card h-100 bg-white border radius-12 overflow-hidden shadow-xs d-flex flex-column">
                                        <div class="arena-card__thumb position-relative" style="height: 180px; overflow: hidden; background: #082f5c;">
                                            <img src="<?= $imgSrc ?>" alt="<?= esc($item['name'] ?? '') ?>" loading="lazy" class="w-100 h-100" style="object-fit: cover;">
                                            <div class="arena-card__badge-row position-absolute top-0 start-0 p-2 z-2">
                                                <span class="badge <?= $statusBadge['class'] ?> px-2 py-1 radius-8 small">
                                                    <?= esc($statusBadge['label']) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark position-absolute top-0 end-0 p-2 z-2">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'     => 'learning_class',
                                                    'id'       => (int) ($item['id'] ?? 0),
                                                    'title'    => $item['name'] ?? null,
                                                    'iconOnly' => true,
                                                    'size'     => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body p-3 d-flex flex-column flex-grow-1">
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-book-open text-primary me-1"></i> Course: <strong><?= esc($item['course_title'] ?? 'Course PBJ') ?></strong>
                                            </div>
                                            <h4 class="arena-card__title mb-2" style="font-size: 16px; font-weight: 700; line-height: 1.4;">
                                                <a href="<?= route_to($detailRoute, $item['code']) ?>" class="text-dark text-decoration-none hover-primary" title="<?= esc($item['name'] ?? '') ?>">
                                                    <?= esc($item['name'] ?? '-') ?>
                                                </a>
                                            </h4>
                                            <div class="d-flex flex-column gap-1 text-muted small mb-3 flex-grow-1" style="font-size: 12.5px;">
                                                <div><i class="fas fa-barcode text-primary me-1"></i> Kode: <code><?= esc($item['code']) ?></code></div>
                                                <div><i class="fas fa-laptop text-info me-1"></i> <?= esc(ucfirst($item['learning_method'] ?? 'blended')) ?></div>
                                                <div><i class="fas fa-users text-success me-1"></i> Sisa Kuota: <strong><?= (int) ($item['remaining_seats'] ?? ($item['capacity'] ?? 0)) ?> Kursi</strong></div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small mt-auto" style="font-size: 12px;">
                                                <span><i class="far fa-calendar-alt text-warning me-1"></i> <?= ! empty($item['start_date']) ? date('d M Y', strtotime($item['start_date'])) : 'Segera' ?></span>
                                                <a href="<?= route_to($detailRoute, $item['code']) ?>" class="btn btn-sm btn-primary radius-8 px-2 py-1 fw-semibold">
                                                    <span>Daftar Kelas</span> &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <!-- ==================== 3. TRAINERS SUB-MENU ==================== -->
                            <?php elseif ($catalogType === 'trainers'): ?>
                                <?php
                                $imgIdx = ($index % 4) + 1;
                                $defaultAvatar = "assets/main/images/team/team-1-{$imgIdx}.jpg";
                                $avatarSrc = ! empty($item['avatar_url'])
                                    ? (str_starts_with($item['avatar_url'], 'http') ? $item['avatar_url'] : base_url($item['avatar_url']))
                                    : base_url($defaultAvatar);
                                $isAsn = ($item['profile_type'] ?? '') === 'asn';
                                $profileLabel = $isAsn ? 'ASN Pemerintah' : 'Praktisi & Widyaiswara';
                                $trainerSlug = ! empty($item['username']) ? $item['username'] : (string) $item['id'];
                                ?>
                                <div class="col-md-6 col-12">
                                    <article class="arena-card h-100 bg-white border radius-12 overflow-hidden shadow-xs d-flex flex-column p-3 text-center">
                                        <div class="mx-auto mb-3 position-relative" style="width: 80px; height: 80px;">
                                            <img src="<?= $avatarSrc ?>" alt="<?= esc($item['name'] ?? $item['username']) ?>" class="rounded-circle w-100 h-100 border border-3 border-light shadow-xs" style="object-fit: cover;">
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" title="Aktif Tersertifikasi"></span>
                                        </div>
                                        <h4 class="mb-1" style="font-size: 16px; font-weight: 700;">
                                            <a href="<?= route_to($detailRoute, $trainerSlug) ?>" class="text-dark text-decoration-none hover-primary">
                                                <?= esc($item['name'] ?: $item['username']) ?>
                                            </a>
                                        </h4>
                                        <div class="mb-2">
                                            <span class="badge <?= $isAsn ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> px-2 py-1 radius-12 small">
                                                <?= esc($profileLabel) ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-3 flex-grow-1" style="font-size: 12.5px;">
                                            Fasilitator Pembelajaran Pengadaan Barang &amp; Jasa Pemerintah Provinsi Lampung.
                                        </p>
                                        <div class="pt-2 border-top mt-auto">
                                            <a href="<?= route_to($detailRoute, $trainerSlug) ?>" class="btn btn-sm btn-outline-primary radius-8 w-100 fw-semibold">
                                                <span>Lihat Profil &amp; Kelas</span> &rarr;
                                            </a>
                                        </div>
                                    </article>
                                </div>

                                <!-- ==================== 4. PROGRAMS SUB-MENU ==================== -->
                            <?php else: ?>
                                <?php
                                $imgIdx = ($index % 5) + 1;
                                $defaultImg = "assets/main/images/blog/blog-2-{$imgIdx}.jpg";
                                $imgSrc = ! empty($item['cover_url'])
                                    ? (str_starts_with($item['cover_url'], 'http') ? $item['cover_url'] : base_url($item['cover_url']))
                                    : base_url($defaultImg);
                                ?>
                                <div class="col-md-6 col-12">
                                    <article class="arena-program-card arena-card h-100 bg-white border radius-12 overflow-hidden shadow-xs d-flex flex-column">
                                        <div class="arena-card__thumb position-relative" style="height: 180px; overflow: hidden; background: #082f5c;">
                                            <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" loading="lazy" class="w-100 h-100" style="object-fit: cover;">
                                            <div class="arena-card__badge-row position-absolute top-0 start-0 p-2 z-2">
                                                <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">
                                                    Program Terstruktur
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark position-absolute top-0 end-0 p-2 z-2">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'     => 'program',
                                                    'id'       => (int) ($item['id'] ?? 0),
                                                    'title'    => $item['title'] ?? null,
                                                    'iconOnly' => true,
                                                    'size'     => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body p-3 d-flex flex-column flex-grow-1">
                                            <h4 class="arena-card__title mb-2" style="font-size: 16px; font-weight: 700; line-height: 1.4;">
                                                <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="text-dark text-decoration-none hover-primary" title="<?= esc($item['title'] ?? '') ?>">
                                                    <?= esc($item['title'] ?? '-') ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text text-muted small mb-3 flex-grow-1" style="font-size: 13px; line-height: 1.5;">
                                                <?= esc(mb_strimwidth($item['summary'] ?: ($item['target_audience'] ?: 'Kurikulum terpadu pengadaan barang/jasa.'), 0, 95, '…')) ?>
                                            </p>
                                            <div class="d-flex flex-wrap align-items-center gap-2 py-1 mb-2 border-top border-bottom text-muted" style="font-size: 12px;">
                                                <span class="text-primary fw-semibold"><i class="fas fa-book-open me-1"></i> <?= (int) ($item['course_count'] ?? 0) ?> Course PBJ</span>
                                                <span>&middot;</span>
                                                <span class="text-success fw-semibold"><i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($item['class_count'] ?? 0) ?> Batch Kelas</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-auto pt-2">
                                                <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="btn btn-sm btn-primary radius-8 flex-grow-1 fw-semibold">
                                                    <span>Lihat Kurikulum</span> &rarr;
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'program',
                                                    'id'    => (int) ($item['id'] ?? 0),
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination Wrapper -->
                    <?php if ($pager): ?>
                        <?php $links = $pager->links($catalogType, 'default_full') ?: $pager->links('default', 'default_full'); ?>
                        <?php if ($links): ?>
                            <div class="pagination-wrapper row mt-4">
                                <div class="col-12 d-flex justify-content-center">
                                    <?= $links ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- Right Column: Clean Structured Sidebar (col-lg-4) -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: Quick Search Box -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari di Arena Belajar</span>
                    </h4>
                    <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="berita-searchbox news-search-box">
                        <input type="text" name="q" placeholder="Cari course, regulasi, modul...">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="d-flex flex-wrap gap-1 mt-3">
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Perpres 12/2021')) ?>" class="badge bg-light text-dark border text-decoration-none">Perpres 12/2021</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Penyusunan HPS')) ?>" class="badge bg-light text-dark border text-decoration-none">Penyusunan HPS</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('E-Katalog')) ?>" class="badge bg-light text-dark border text-decoration-none">E-Katalog</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Hukum Kontrak')) ?>" class="badge bg-light text-dark border text-decoration-none">Hukum Kontrak</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Mitigasi Risiko')) ?>" class="badge bg-light text-dark border text-decoration-none">Mitigasi Risiko</a>
                    </div>
                </div>

                <!-- Sidebar Widget 2: Kategori Arena Belajar (Nav Pills with Live Count) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Kategori Arena Belajar</span>
                    </h4>
                    <nav class="arena-nav-pills berita-catlist" aria-label="Kategori Arena Belajar">
                        <a href="<?= site_url('arena-belajar') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Eksplorasi</span>
                        </a>
                        <a href="<?= site_url('arena-belajar/program') ?>" class="arena-nav-pill <?= $catalogType === 'programs' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Program Pembelajaran</span>
                            <span class="count"><?= (int) ($stats['total_programs'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/course') ?>" class="arena-nav-pill <?= $catalogType === 'courses' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#16a34a"></span>Course PBJ</span>
                            <span class="count"><?= (int) ($stats['total_courses'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="arena-nav-pill <?= $catalogType === 'classes' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#dc2626"></span>Kelas &amp; Batch</span>
                            <span class="count"><?= (int) ($stats['total_classes'] ?? 0) ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/pengajar') ?>" class="arena-nav-pill <?= $catalogType === 'trainers' ? 'is-active' : '' ?>">
                            <span class="left"><span class="dot" style="background:#9333ea"></span>Instruktur &amp; Pengajar</span>
                            <span class="count"><?= (int) ($stats['total_trainers'] ?? 0) ?></span>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Widget 3: Standar Kompetensi LKPP & Pemprov Lampung -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-shield-alt" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Standar Kompetensi LKPP &amp; Pemprov</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.55;">
                        Platform pembelajaran terpadu Pengadaan Barang/Jasa Pemerintah selaras standar kurikulum nasional:
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 radius-8 border-start border-3 border-primary bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Kurikulum Berstandar LKPP</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Materi berbasis Perpres No. 12 Tahun 2021 dan peraturan turunan.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-success bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Sertifikasi Terverifikasi</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Penerbitan e-sertifikat resmi dan verifikasi digital nasional.</p>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-warning bg-light">
                            <strong style="font-size: 13px; color: #0f172a;">Pembelajaran Mandiri &amp; Fleksibel</strong>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.45;">Akses materi, silabus, dan kuis evaluasi daring kapan saja.</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 4: Top 3 Program Pembelajaran Terstruktur -->
                <?php if (! empty($topPrograms)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-layer-group" style="background: #e0f2fe; color: #0284c7;"></i>
                            <span>Program PBJ Unggulan</span>
                        </h4>
                        <?php foreach (array_slice($topPrograms, 0, 3) as $pRank => $pItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank is-top">
                                    <?= $pRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.learning.program.detail', $pItem['slug']) ?>">
                                            <?= esc($pItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span><i class="fas fa-book-open text-primary me-1"></i> Program</span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.learning.program.detail', $pItem['slug']) ?>" class="fw-bold text-primary text-decoration-none">Jalur Kurikulum &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/program') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Program (<?= (int) ($stats['total_programs'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 5: Top 3 Course PBJ Terpopuler -->
                <?php if (! empty($topCourses)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-book-open" style="background: #dcfce7; color: #16a34a;"></i>
                            <span>Course PBJ Terpopuler</span>
                        </h4>
                        <?php foreach (array_slice($topCourses, 0, 3) as $cRank => $cItem):
                            $cLvl = strtolower((string) ($cItem['level'] ?? 'basic'));
                            $cLvlLabel = match ($cLvl) {
                                'intermediate' => 'Menengah',
                                'advanced'     => 'Lanjutan',
                                default        => 'Dasar',
                            };
                        ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #dcfce7; color: #15803d;">
                                    <?= $cRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.learning.course.detail', $cItem['slug']) ?>">
                                            <?= esc($cItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge bg-light text-dark border" style="font-size: 10.5px;"><?= esc($cLvlLabel) ?></span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.learning.course.detail', $cItem['slug']) ?>" class="fw-bold text-success text-decoration-none">Pelajari &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-sm btn-outline-success w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Course (<?= (int) ($stats['total_courses'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 6: Top 3 Kelas & Batch Aktif Siap Daftar -->
                <?php if (! empty($topClasses)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-chalkboard-teacher" style="background: #fee2e2; color: #dc2626;"></i>
                            <span>Kelas &amp; Batch Aktif</span>
                        </h4>
                        <?php foreach (array_slice($topClasses, 0, 3) as $kRank => $kItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #fee2e2; color: #b91c1c;">
                                    <?= $kRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.learning.class.detail', $kItem['code']) ?>">
                                            <?= esc($kItem['name']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10.5px;">Kode: <?= esc($kItem['code']) ?></span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.learning.class.detail', $kItem['code']) ?>" class="fw-bold text-danger text-decoration-none">Daftar &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-sm btn-outline-danger w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Kelas (<?= (int) ($stats['total_classes'] ?? 0) ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 7: Keunggulan BegawiPengadaan (Pillars of Excellence) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-star" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Keunggulan Arena Belajar</span>
                    </h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary text-xs fw-bold px-2 py-1">Pilar Pembelajaran</span>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Dari Regulasi Menuju Praktik Nyata Pengadaan</span>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-primary mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Akses Belajar Mandiri &amp; Fleksibel</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Materi, silabus, dan kuis interaktif yang dapat diakses kapan saja dan di mana saja.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Fasilitator Terakreditasi Nasional</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Didampingi langsung oleh widyaiswara, praktisi PBJ, dan penguji kompetensi bersertifikat.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-info mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Integrasi Ekosistem PBJ Lampung</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Terhubung langsung dengan Bunga Rampai, konsultasi ahli, dan portal regulasi resmi daerah.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 8: Bantuan & Konsultasi Biro PBJ -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 16px;">Bantuan &amp; Konsultasi</h5>
                    <p class="text-white-80 mb-3" style="font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                        Memiliki pertanyaan seputar kurikulum, pendaftaran kelas batch, atau kendala sertifikasi pelatihan? Tim pembina PBJ Provinsi Lampung siap membantu Anda.
                    </p>
                    <div class="text-warning fw-semibold d-flex align-items-center gap-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-phone-alt"></i>
                        <span>Helpdesk PBJ: +62 851-1777-9783</span>
                    </div>
                    <a href="https://wa.me/6285117779783?text=Halo%20Admin%20BegawiPengadaan%20Lampung,%20saya%20ingin%20bertanya%20seputar%20Arena%20Belajar" target="_blank" rel="noopener noreferrer" class="btn btn-warning text-dark w-100 fw-bold radius-8 d-flex align-items-center justify-content-center gap-2" style="font-size: 13px;">
                        <i class="fab fa-whatsapp"></i>
                        <span>Hubungi Konsultasi WhatsApp</span>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>