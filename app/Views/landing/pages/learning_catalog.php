<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
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
$isLoggedIn = session()->get('isLoggedIn') === true;

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
        'title' => 'Kelas &amp; Batch Pembelajaran',
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
     PAGE HERO ARENA BELAJAR (COMPACT & UNIFIED)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center">
            <span class="arena-section-head__tag arena-hero__tag text-white mb-2" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-graduation-cap me-1"></i> Arena Belajar BegawiPengadaan Lampung
            </span>
            <h1 class="arena-hero__title text-white mb-2">
                <?= $heroMeta['title'] ?>
            </h1>
            <p class="arena-hero__desc">
                <?= $heroMeta['desc'] ?>
            </p>

            <!-- Live Search Bar -->
            <div class="arena-search-bar mb-2">
                <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 15px;"></i>
                    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="<?= esc($heroMeta['searchPlaceholder']) ?>" autocomplete="off">
                    <button type="submit">
                        <i class="fas fa-search me-1"></i>
                        <span>Cari</span>
                    </button>
                    <?php if ($q !== ''): ?>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-link text-muted ms-1" title="Reset Pencarian">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Sub-Navigation Pills -->
            <nav class="arena-nav-pills" aria-label="Kategori Arena Belajar">
                <a href="<?= site_url('arena-belajar') ?>" class="arena-nav-pill">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Eksplorasi</span>
                </a>
                <a href="<?= site_url('arena-belajar/program') ?>" class="arena-nav-pill <?= $catalogType === 'programs' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Program Pembelajaran</span>
                    <span class="badge"><?= (int) $stats['total_programs'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/course') ?>" class="arena-nav-pill <?= $catalogType === 'courses' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i>
                    <span>Course PBJ</span>
                    <span class="badge"><?= (int) $stats['total_courses'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/kelas') ?>" class="arena-nav-pill <?= $catalogType === 'classes' ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Kelas &amp; Batch</span>
                    <span class="badge"><?= (int) $stats['total_classes'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/pengajar') ?>" class="arena-nav-pill <?= $catalogType === 'trainers' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Instruktur &amp; Pengajar</span>
                    <span class="badge"><?= (int) $stats['total_trainers'] ?></span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- =========================================================================
     CATALOG CONTENT SECTION (COMPACT & TAILORED)
     ========================================================================= -->
<section class="py-4 learning-catalog position-relative" style="background: #f8fafc; min-height: 60vh;">
    <div class="container">
        <?php if ($catalogType === 'programs'): ?>
            <!-- Interactive Curriculum Pathway Roadmap -->
            <div class="arena-program-roadmap card border-0 shadow-2xs radius-12 p-3 p-md-4 mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div>
                        <span class="badge bg-primary text-white px-2 py-1 radius-12 small text-uppercase" style="letter-spacing: 0.5px; font-size: 12px;">
                            <i class="fas fa-route me-1"></i> Jalur Kompetensi Standar LKPP
                        </span>
                        <h4 class="fw-bold mt-1 mb-1" style="color: #082f5c; font-size: 21px;">
                            Roadmap Kurikulum Pengadaan Barang &amp; Jasa Berjenjang
                        </h4>
                        <p class="text-muted small mb-0" style="max-width: 700px; font-size: 14.5px; line-height: 1.6;">
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
                                <span class="badge fw-bold" style="background: #e0f2fe; color: #0369a1; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12.5px;">1</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Tingkat Dasar</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.5;">Prinsip, etika &amp; kerangka hukum Perpres PBJ.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="arena-roadmap-step" style="border-left: 3px solid #d97706 !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #fef3c7; color: #b45309; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12.5px;">2</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Perencanaan &amp; HPS</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.5;">Penyusunan RUP, spesifikasi teknis &amp; survei pasar.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="arena-roadmap-step" style="border-left: 3px solid #16a34a !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #dcfce7; color: #15803d; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12.5px;">3</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Pemilihan Penyedia</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.5;">Tender, e-purchasing, e-katalog &amp; negosiasi.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="arena-roadmap-step" style="border-left: 3px solid #9333ea !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #f3e8ff; color: #7e22ce; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 12.5px;">4</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Kontrak &amp; BAST</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.5;">Pengendalian kontrak, mitigasi risiko &amp; serah terima.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sub-Menu Filter / Context Bar -->
        <div class="arena-filter-bar">
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
                    <h3 class="fw-bold mt-1 mb-0" style="font-size: 21px; color: #0f172a;">
                        <?= esc($pageTitle) ?>
                    </h3>
                    <p class="text-muted small mb-0" style="font-size: 14px;"><?= esc($description) ?></p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($q !== '' || ! empty($featuredFilter) || ! empty($selectedProgram) || ! empty($selectedLevel) || ! empty($selectedStatus) || ! empty($selectedCourse) || ! empty($selectedProfile)): ?>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-2 py-1" style="font-size: 13px;">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-12 fw-semibold" style="font-size: 12.5px;">
                        <?= count($items) ?> materi tampil
                    </span>
                </div>
            </div>

            <!-- Program Specific Quick Filter Tabs -->
            <?php if ($catalogType === 'programs'): ?>
                <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                    <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-filter me-1"></i> Kategori Kurikulum:</span>
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
                <!-- Level Filter Row -->
                <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                    <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-signal me-1"></i> Tingkat Kesulitan:</span>
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
                        <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-layer-group me-1"></i> Induk Program:</span>
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
                    <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-calendar-check me-1"></i> Status Pelaksanaan:</span>
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

                <!-- Course Filter Row for Classes -->
                <?php if (! empty($coursesList)): ?>
                    <div class="d-flex flex-wrap align-items-center gap-1 pt-2 mt-2 border-top">
                        <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-book-open me-1"></i> Filter Course Induk:</span>
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
                    <span class="small fw-bold text-muted me-2" style="font-size: 13.5px;"><i class="fas fa-user-tag me-1"></i> Tipe Instruktur:</span>
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

        <!-- Catalog Items Grid -->
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
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                            <article class="arena-card h-100">
                                <div class="arena-card__thumb">
                                    <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" loading="lazy">
                                    <div class="arena-card__badge-row">
                                        <span class="arena-pill-badge <?= $levelConfig['badge'] ?>">
                                            <i class="fas <?= $levelConfig['icon'] ?> me-1"></i> <?= esc($levelConfig['label']) ?>
                                        </span>
                                    </div>
                                    <div class="arena-card-floating-bookmark">
                                        <?= view('landing/components/bookmark_button', [
                                            'type'     => 'course',
                                            'id'       => (int) ($item['id'] ?? 0),
                                            'title'    => $item['title'] ?? null,
                                            'iconOnly' => true,
                                            'size'     => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="arena-card__body">
                                    <?php if (! empty($item['program_slug'])): ?>
                                        <a href="<?= route_to('public.learning.program.detail', $item['program_slug']) ?>" class="arena-card__category text-decoration-none hover-primary">
                                            <i class="fas fa-layer-group text-primary me-1"></i> <?= esc($item['program_title'] ?? 'Program Terstruktur') ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="arena-card__category"><i class="fas fa-layer-group text-primary me-1"></i> <?= esc($item['program_title'] ?? 'Materi Terstruktur') ?></span>
                                    <?php endif; ?>

                                    <h4 class="arena-card__title">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" title="<?= esc($item['title'] ?? '') ?>">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>
                                    <p class="arena-card__text">
                                        <?= esc(mb_strimwidth($item['summary'] ?: ($item['target_audience'] ?: 'Materi pembelajaran pengadaan barang/jasa.'), 0, 95, '…')) ?>
                                    </p>

                                    <?php if (! empty($item['target_audience'])): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-light text-muted border text-truncate d-inline-block" style="max-width: 100%; font-size: 12.5px;" title="<?= esc($item['target_audience']) ?>">
                                                <i class="fas fa-users text-primary me-1"></i> <?= esc(mb_strimwidth($item['target_audience'], 0, 32, '…')) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex flex-wrap align-items-center gap-2 py-1 mb-2 border-top border-bottom text-muted" style="font-size: 13px;">
                                        <span class="fw-semibold">
                                            <i class="far fa-clock text-warning me-1"></i> <?= ($item['duration_minutes'] ?? null) !== null ? esc((string) $item['duration_minutes']) . ' mnt' : 'Mandiri' ?>
                                        </span>
                                        <span>&middot;</span>
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($item['class_count'] ?? 0) ?> Batch Kelas
                                        </span>
                                        <?php if (! empty($item['enrolled_count'])): ?>
                                            <span>&middot;</span>
                                            <span class="text-info fw-semibold">
                                                <i class="fas fa-users me-1"></i> <?= (int) $item['enrolled_count'] ?> Peserta
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="arena-card__actions mt-auto">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                            <span>Lihat Course</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'course',
                                            'id'    => (int) ($item['id'] ?? 0),
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </article>
                        </div>

                    <!-- ==================== 2. CLASSES SUB-MENU ==================== -->
                    <?php elseif ($catalogType === 'classes'): ?>
                        <?php
                        $imgIdx = (($index + 2) % 6) + 1;
                        $defaultImg = "assets/main/images/resources/course-list-img-{$imgIdx}.jpg";
                        $imgSrc = ! empty($item['course_cover_url'])
                            ? (str_starts_with($item['course_cover_url'], 'http') ? $item['course_cover_url'] : base_url($item['course_cover_url']))
                            : base_url($defaultImg);
                        $classStatus = strtolower((string) ($item['status'] ?? 'scheduled'));
                        $statusBadge = $classStatus === 'active'
                            ? ['label' => 'Aktif Berjalan', 'badge' => 'bg-success text-white', 'icon' => 'fa-play-circle']
                            : ['label' => 'Jadwal Terbuka', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-clock'];
                        $method = strtolower((string) ($item['learning_method'] ?? 'online'));
                        $methodBadge = match ($method) {
                            'offline' => ['label' => 'Tatap Muka', 'badge' => 'bg-info text-white', 'icon' => 'fa-building'],
                            'hybrid'  => ['label' => 'Hybrid', 'badge' => 'bg-purple text-white', 'icon' => 'fa-network-wired'],
                            default   => ['label' => 'Online LMS', 'badge' => 'bg-primary text-white', 'icon' => 'fa-laptop'],
                        };
                        $startDate = ! empty($item['start_at']) ? date('d M Y', strtotime($item['start_at'])) : '-';
                        $endDate   = ! empty($item['end_at']) ? date('d M Y', strtotime($item['end_at'])) : '-';
                        $cCapacity = (int) ($item['capacity'] ?? 0);
                        $cEnrolled = (int) ($item['enrolled_count'] ?? 0);
                        $cQuotaPercent = $cCapacity > 0 ? min(100, round(($cEnrolled / $cCapacity) * 100)) : 0;
                        ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                            <article class="arena-card h-100">
                                <div class="arena-card__thumb">
                                    <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" loading="lazy">
                                    <div class="arena-card__badge-row">
                                        <span class="arena-pill-badge <?= $statusBadge['badge'] ?>">
                                            <i class="fas <?= $statusBadge['icon'] ?> me-1"></i> <?= esc($statusBadge['label']) ?>
                                        </span>
                                    </div>
                                    <div class="arena-card-floating-bookmark">
                                        <?= view('landing/components/bookmark_button', [
                                            'type'     => 'learning_class',
                                            'id'       => (int) ($item['id'] ?? 0),
                                            'title'    => $item['name'] ?? $item['title'] ?? null,
                                            'iconOnly' => true,
                                            'size'     => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="arena-card__body">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="arena-card__category mb-0">
                                            <i class="fas fa-hashtag text-primary me-1"></i> Batch: <?= esc($item['code'] ?? '') ?>
                                        </span>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge <?= $methodBadge['badge'] ?> px-2 py-1 radius-4 small" style="font-size: 12px;">
                                                <i class="fas <?= $methodBadge['icon'] ?> me-1"></i> <?= esc($methodBadge['label']) ?>
                                            </span>
                                            <?php if (! empty($item['program_title'])): ?>
                                                <span class="badge bg-light text-secondary border small text-truncate" style="max-width: 120px;" title="<?= esc($item['program_title']) ?>">
                                                    <?= esc($item['program_title']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <h4 class="arena-card__title">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" title="<?= esc($item['title'] ?? '') ?>">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>

                                    <!-- Preserved Course label for tests & clarity -->
                                    <p class="learning-catalog__text mb-1 text-muted small" style="font-size: 13.5px;">
                                        <i class="fas fa-book-open text-primary me-1"></i> Course: <a href="<?= ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : '#' ?>" class="text-secondary fw-semibold text-decoration-none hover-primary"><?= esc($item['course_title'] ?? '-') ?></a>
                                    </p>

                                    <?php if (! empty($item['description'])): ?>
                                        <p class="text-muted small mb-2 text-truncate" style="font-size: 13.5px;">
                                            <?= esc(mb_strimwidth($item['description'], 0, 75, '…')) ?>
                                        </p>
                                    <?php endif; ?>

                                    <!-- Timeline & Quota Strip -->
                                    <div class="py-1 my-1 border-top border-bottom text-muted" style="font-size: 13px;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span><i class="far fa-calendar-alt text-primary me-1"></i> <?= $startDate ?> - <?= $endDate ?></span>
                                            <span class="fw-semibold text-dark"><i class="fas fa-users text-info me-1"></i> <?= $cEnrolled ?>/<?= $cCapacity ?></span>
                                        </div>
                                        <?php if ($cCapacity > 0): ?>
                                            <div class="progress" style="height: 5px; border-radius: 4px; background: #e2e8f0;">
                                                <div class="progress-bar <?= $cQuotaPercent >= 90 ? 'bg-danger' : ($cQuotaPercent >= 70 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $cQuotaPercent ?>%;"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (! empty($item['trainers'])): ?>
                                        <div class="d-flex align-items-center gap-1 my-1 small text-muted text-truncate" style="font-size: 12.5px;">
                                            <i class="fas fa-user-tie text-primary me-1"></i>
                                            <span class="fw-semibold">Fasilitator:</span>
                                            <?php foreach (array_slice($item['trainers'], 0, 2) as $t): ?>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    <?= esc($t['name'] ?? $t['username']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="arena-card__actions mt-auto pt-2">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                            <span>Lihat Detail &amp; Daftar</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'learning_class',
                                            'id'    => (int) ($item['id'] ?? 0),
                                            'title' => $item['name'] ?? $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </article>
                        </div>

                    <!-- ==================== 3. TRAINERS SUB-MENU ==================== -->
                    <?php elseif ($catalogType === 'trainers'): ?>
                        <?php
                        $imgIdx = ($index % 5) + 1;
                        $imgSrc = base_url("assets/main/images/resources/course-list-client-img-{$imgIdx}.jpg");
                        $profType = strtolower((string) ($item['profile_type'] ?? 'public'));
                        $profBadge = match ($profType) {
                            'asn'     => ['label' => 'ASN PBJ Lampung', 'badge' => 'bg-info-subtle text-info border border-info-subtle', 'icon' => 'fa-landmark'],
                            'public'  => ['label' => 'Praktisi & Fasilitator', 'badge' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-user-tie'],
                            default   => ['label' => ucfirst($profType), 'badge' => 'bg-primary-subtle text-primary border border-primary-subtle', 'icon' => 'fa-chalkboard-teacher'],
                        };
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <article class="arena-trainer-card">
                                <div class="arena-trainer-card__avatar">
                                    <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="mb-1">
                                    <span class="badge <?= $profBadge['badge'] ?> px-2 py-1 radius-12" style="font-size: 12px;">
                                        <i class="fas <?= $profBadge['icon'] ?> me-1"></i> <?= esc($profBadge['label']) ?>
                                    </span>
                                </div>
                                <h5 class="arena-trainer-card__name">
                                    <a href="<?= route_to($detailRoute, $item['slug']) ?>">
                                        <?= esc($item['title'] ?? '-') ?>
                                    </a>
                                </h5>
                                <span class="arena-trainer-card__role">Fasilitator PBJ Lampung</span>

                                <!-- Micro-Stats Strip -->
                                <div class="d-flex align-items-center justify-content-around py-1 mb-2 radius-8 bg-light border" style="font-size: 13px;">
                                    <div class="text-center px-1">
                                        <strong class="d-block text-primary"><?= (int) ($item['total_classes'] ?? count($item['classes'] ?? [])) ?></strong>
                                        <span class="text-muted" style="font-size: 12px;">Batch</span>
                                    </div>
                                    <div class="border-end" style="height: 18px;"></div>
                                    <div class="text-center px-1">
                                        <strong class="d-block text-warning-emphasis"><?= (int) ($item['total_courses'] ?? count($item['courses'] ?? [])) ?></strong>
                                        <span class="text-muted" style="font-size: 12px;">Course</span>
                                    </div>
                                    <div class="border-end" style="height: 18px;"></div>
                                    <div class="text-center px-1">
                                        <strong class="d-block text-success"><?= (int) ($item['total_students'] ?? 0) ?></strong>
                                        <span class="text-muted" style="font-size: 12px;">Peserta</span>
                                    </div>
                                </div>

                                <div class="mt-auto pt-1">
                                    <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 w-100 fw-semibold py-1 d-flex align-items-center justify-content-center gap-1" style="font-size: 13.5px;">
                                        <span>Lihat Profil &amp; Jadwal</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>

                    <!-- ==================== 4. PROGRAMS SUB-MENU ==================== -->
                    <?php else: ?>
                        <?php
                        $bgImages = [
                            'course-details-img-1.jpg',
                            'course-list-img-1.jpg',
                            'course-list-img-2.jpg',
                            'course-list-img-3.jpg',
                            'course-list-img-4.jpg',
                            'course-list-img-5.jpg',
                            'course-list-img-6.jpg',
                        ];
                        $defaultCover = 'assets/main/images/resources/' . $bgImages[($index % count($bgImages))];
                        $coverImg = ! empty($item['cover_url'])
                            ? (str_starts_with($item['cover_url'], 'http') ? $item['cover_url'] : base_url($item['cover_url']))
                            : base_url($defaultCover);
                        ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                            <article class="arena-card arena-program-card h-100">
                                <div class="arena-program-card__cover">
                                    <img src="<?= $coverImg ?>" alt="<?= esc($item['title'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.85); transition: transform 0.4s ease;" loading="lazy">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8,47,92,0.2) 0%, rgba(4,24,48,0.85) 100%);"></div>

                                    <div class="position-absolute top-0 start-0 p-2 d-flex flex-wrap gap-1 z-2">
                                        <?php if (! empty($item['featured'])): ?>
                                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-12 shadow-xs" style="font-size: 12px;">
                                                <i class="fas fa-star me-1 text-dark"></i> Kurikulum Unggulan
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-white text-primary fw-bold px-2 py-1 radius-12 shadow-xs" style="font-size: 12px;">
                                            <i class="fas fa-shield-alt me-1"></i> Standar LKPP
                                        </span>
                                    </div>

                                    <div class="arena-card-floating-bookmark">
                                        <?= view('landing/components/bookmark_button', [
                                            'type'     => 'program',
                                            'id'       => (int) ($item['id'] ?? 0),
                                            'title'    => $item['title'] ?? null,
                                            'iconOnly' => true,
                                            'size'     => 'sm',
                                        ]) ?>
                                    </div>

                                    <div class="position-absolute bottom-0 start-0 p-2 z-2 w-100">
                                        <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 12px;">
                                            Program Terstruktur
                                        </span>
                                    </div>
                                </div>

                                <div class="arena-card__body">
                                    <h4 class="arena-card__title">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="hover-primary" title="<?= esc($item['title'] ?? '') ?>">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>

                                    <p class="arena-card__text">
                                        <?= esc(mb_strimwidth($item['summary'] ?: ($item['target_audience'] ?: 'Kurikulum terpadu pengadaan barang/jasa.'), 0, 95, '…')) ?>
                                    </p>

                                    <?php if (! empty($item['target_audience'])): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-light text-muted border text-truncate d-inline-block" style="max-width: 100%; font-size: 12.5px;" title="<?= esc($item['target_audience']) ?>">
                                                <i class="fas fa-users text-primary me-1"></i> <?= esc(mb_strimwidth($item['target_audience'], 0, 32, '…')) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex flex-wrap align-items-center gap-2 py-1 mb-2 border-top border-bottom text-muted" style="font-size: 13px;">
                                        <span class="text-primary fw-semibold">
                                            <i class="fas fa-book-open me-1"></i> <?= (int) ($item['course_count'] ?? 0) ?> Course PBJ
                                        </span>
                                        <span>&middot;</span>
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($item['class_count'] ?? 0) ?> Batch Kelas
                                        </span>
                                    </div>

                                    <div class="arena-card__actions mt-auto pt-2">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                            <span>Lihat Kurikulum</span>
                                            <i class="fas fa-arrow-right"></i>
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
</section>

<?= $this->endSection() ?>