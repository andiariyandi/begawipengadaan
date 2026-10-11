<?= $this->extend('landing/layouts/public') ?>

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
     PAGE HERO ARENA BELAJAR (IDENTICAL UNIFIED HERO SECTION)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-graduation-cap me-1"></i> Arena Belajar BegawiPBJ Lampung
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                <?= $heroMeta['title'] ?>
            </h1>
            <p class="mx-auto mb-4" style="max-width: 680px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                <?= $heroMeta['desc'] ?>
            </p>

            <!-- Live Search Bar -->
            <div class="arena-search-bar mb-4">
                <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
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

            <!-- Sub-Navigation Pills (Identical Tab Buttons) -->
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
     CATALOG CONTENT SECTION (TAILORED & ENHANCED PER SUB-MENU)
     ========================================================================= -->
<section class="py-5 learning-catalog position-relative" style="background: #f8fafc; min-height: 60vh;">
    <div class="container">
        <?php if ($catalogType === 'programs'): ?>
            <!-- Interactive Curriculum Pathway Roadmap -->
            <div class="arena-program-roadmap card border-0 shadow-sm radius-16 p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <span class="badge bg-primary text-white px-3 py-1 radius-20 small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-route me-1"></i> Jalur Kompetensi Standar LKPP
                        </span>
                        <h4 class="fw-bold mt-2 mb-1" style="color: #082f5c; font-size: 20px;">
                            Roadmap Kurikulum Pengadaan Barang &amp; Jasa Berjenjang
                        </h4>
                        <p class="text-muted small mb-0" style="max-width: 720px;">
                            Struktur kurikulum terpadu BegawiPBJ dirancang berjenjang untuk memastikan aparatur dan praktisi menguasai seluruh siklus pengadaan pemerintah secara akuntabel.
                        </p>
                    </div>
                    <div class="d-none d-lg-block">
                        <span class="badge bg-white text-primary border shadow-sm px-3 py-2 radius-12 fw-semibold">
                            <i class="fas fa-certificate text-warning me-1"></i> Terhubung Sertifikasi Resmi
                        </span>
                    </div>
                </div>

                <!-- 4 Pathway Stages -->
                <div class="row g-2 pt-2">
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="p-3 bg-white radius-12 border h-100 shadow-2xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #e0f2fe; color: #0369a1; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">1</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Tingkat Dasar</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.4;">Prinsip, etika &amp; kerangka hukum Perpres PBJ.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="p-3 bg-white radius-12 border h-100 shadow-2xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #fef3c7; color: #b45309; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">2</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Perencanaan &amp; HPS</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.4;">Penyusunan RUP, spesifikasi teknis &amp; survei pasar.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="p-3 bg-white radius-12 border h-100 shadow-2xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #dcfce7; color: #15803d; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">3</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Pemilihan Penyedia</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.4;">Tender, e-purchasing, e-katalog &amp; negosiasi.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="p-3 bg-white radius-12 border h-100 shadow-2xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold" style="background: #f3e8ff; color: #7e22ce; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">4</span>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Kontrak &amp; BAST</h6>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.4;">Pengendalian kontrak, mitigasi risiko &amp; serah terima.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sub-Menu Filter / Context Bar -->
        <div class="card border-0 shadow-sm radius-16 p-4 mb-4" style="background: #ffffff;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="arena-section-head__tag mb-0">
                            <?= esc($heroMeta['tag']) ?>
                        </span>
                        <?php if ($q !== ''): ?>
                            <span class="badge bg-light text-dark border radius-20 px-3 py-1">
                                Pencarian: "<strong><?= esc($q) ?></strong>"
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="fw-bold mt-2 mb-1" style="font-size: 22px; color: #0f172a;">
                        <?= esc($pageTitle) ?>
                    </h3>
                    <p class="text-muted small mb-0"><?= esc($description) ?></p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($q !== '' || ! empty($featuredFilter) || ! empty($selectedProgram) || ! empty($selectedLevel) || ! empty($selectedStatus) || ! empty($selectedCourse) || ! empty($selectedProfile)): ?>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 radius-20 fw-semibold" style="font-size: 13px;">
                        Total: <?= count($items) ?> materi tampil
                    </span>
                </div>
            </div>

            <!-- Program Specific Quick Filter Tabs -->
            <?php if ($catalogType === 'programs'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                    <span class="small fw-bold text-muted me-2"><i class="fas fa-filter me-1"></i> Kategori Kurikulum:</span>
                    <a href="<?= site_url('arena-belajar/program' . ($q !== '' ? '?q=' . urlencode($q) : '')) ?>" class="badge <?= empty($featuredFilter) ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        Semua Program (<?= (int) ($stats['total_programs'] ?? count($items)) ?>)
                    </a>
                    <a href="<?= site_url('arena-belajar/program?featured=1' . ($q !== '' ? '&q=' . urlencode($q) : '')) ?>" class="badge <?= ($featuredFilter ?? '') === '1' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-star me-1 text-warning"></i> Kurikulum Unggulan
                    </a>
                </div>
            <?php endif; ?>

            <!-- Course Specific Filters (Level & Induk Program) -->
            <?php if ($catalogType === 'courses'): ?>
                <!-- Level Filter Row -->
                <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                    <span class="small fw-bold text-muted me-2"><i class="fas fa-signal me-1"></i> Tingkat Kesulitan:</span>
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
                    <a href="<?= $buildCourseUrl(null, $curProg ?: null) ?>" class="badge <?= empty($curLevel) ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        Semua Level
                    </a>
                    <a href="<?= $buildCourseUrl('basic', $curProg ?: null) ?>" class="badge <?= $curLevel === 'basic' ? 'bg-info text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-shield-alt me-1"></i> Dasar (Basic)
                    </a>
                    <a href="<?= $buildCourseUrl('intermediate', $curProg ?: null) ?>" class="badge <?= $curLevel === 'intermediate' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-chart-line me-1"></i> Menengah (Intermediate)
                    </a>
                    <a href="<?= $buildCourseUrl('advanced', $curProg ?: null) ?>" class="badge <?= $curLevel === 'advanced' ? 'bg-danger text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-award me-1"></i> Lanjutan (Advanced)
                    </a>
                </div>

                <!-- Program Filter Row for Courses -->
                <?php if (! empty($programsList)): ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 pt-2 mt-2">
                        <span class="small fw-bold text-muted me-2"><i class="fas fa-layer-group me-1"></i> Induk Program:</span>
                        <a href="<?= $buildCourseUrl($curLevel ?: null, null) ?>" class="badge <?= empty($curProg) ? 'bg-dark text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                            Semua Induk Program
                        </a>
                        <?php foreach ($programsList as $p): ?>
                            <a href="<?= $buildCourseUrl($curLevel ?: null, $p['slug']) ?>" class="badge <?= $curProg === $p['slug'] ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                                <?= esc($p['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Classes Specific Filters (Status & Induk Course) -->
            <?php if ($catalogType === 'classes'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                    <span class="small fw-bold text-muted me-2"><i class="fas fa-calendar-check me-1"></i> Status Pelaksanaan:</span>
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
                    <a href="<?= $buildClassUrl(null, $curCourse ?: null) ?>" class="badge <?= empty($curStatus) ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        Semua Status Batch
                    </a>
                    <a href="<?= $buildClassUrl('active', $curCourse ?: null) ?>" class="badge <?= $curStatus === 'active' ? 'bg-success text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-play-circle me-1"></i> Sedang Berjalan (Aktif)
                    </a>
                    <a href="<?= $buildClassUrl('scheduled', $curCourse ?: null) ?>" class="badge <?= $curStatus === 'scheduled' ? 'bg-warning text-dark' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="far fa-clock me-1"></i> Jadwal Terbuka (Segera Dimulai)
                    </a>
                </div>

                <!-- Course Filter Row for Classes -->
                <?php if (! empty($coursesList)): ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 pt-2 mt-2">
                        <span class="small fw-bold text-muted me-2"><i class="fas fa-book-open me-1"></i> Filter Course Induk:</span>
                        <a href="<?= $buildClassUrl($curStatus ?: null, null) ?>" class="badge <?= empty($curCourse) ? 'bg-dark text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                            Semua Course
                        </a>
                        <?php foreach (array_slice($coursesList, 0, 6) as $c): ?>
                            <a href="<?= $buildClassUrl($curStatus ?: null, $c['slug']) ?>" class="badge <?= $curCourse === $c['slug'] ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20 text-truncate" style="max-width: 220px;">
                                <?= esc($c['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Trainer Specific Filters (Profile Type) -->
            <?php if ($catalogType === 'trainers'): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                    <span class="small fw-bold text-muted me-2"><i class="fas fa-user-tag me-1"></i> Tipe Instruktur:</span>
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
                    <a href="<?= $buildTrainerUrl(null) ?>" class="badge <?= empty($curProf) ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        Semua Pengajar
                    </a>
                    <a href="<?= $buildTrainerUrl('asn') ?>" class="badge <?= $curProf === 'asn' ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-university me-1"></i> ASN Pemerintah
                    </a>
                    <a href="<?= $buildTrainerUrl('public') ?>" class="badge <?= $curProf === 'public' ? 'bg-primary text-white' : 'bg-light text-dark border' ?> px-3 py-2 text-decoration-none radius-20">
                        <i class="fas fa-user-tie me-1"></i> Praktisi &amp; Widyaiswara
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Catalog Items Grid -->
        <?php if ($items === []): ?>
            <div class="card border text-center p-5 radius-16 shadow-sm my-4 bg-white">
                <div class="mb-3">
                    <i class="fas fa-search text-muted" style="font-size: 48px; opacity: 0.4;"></i>
                </div>
                <h4 class="fw-bold mb-2">Tidak Ada Data Ditemukan</h4>
                <p class="text-muted mb-4">
                    <?= $q !== '' ? 'Tidak ada hasil untuk pencarian "' . esc($q) . '". Silakan gunakan kata kunci lain.' : 'Belum ada konten pembelajaran yang tersedia pada kategori ini.' ?>
                </p>
                <?php if ($q !== '' || ! empty($selectedProgram) || ! empty($selectedLevel)): ?>
                    <div>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-primary radius-8 px-4">
                            Lihat Semua <?= esc($pageTitle) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($items as $index => $item): ?>
                    <!-- ==================== 1. COURSES SUB-MENU ==================== -->
                    <?php if ($catalogType === 'courses'): ?>
                        <?php
                        $imgIdx = ($index % 6) + 1;
                        $imgSrc = base_url("assets/main/images/resources/course-list-img-{$imgIdx}.jpg");
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
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>
                                    <p class="arena-card__text">
                                        <?= esc(mb_strimwidth($item['summary'] ?? $item['text'] ?? 'Materi pembelajaran pengadaan barang/jasa.', 0, 115, '…')) ?>
                                    </p>

                                    <!-- Rich Meta Chips Row -->
                                    <div class="d-flex flex-wrap align-items-center gap-2 py-2 mb-3 border-top border-bottom" style="font-size: 12px;">
                                        <span class="text-secondary fw-semibold">
                                            <i class="far fa-clock text-warning me-1"></i> <?= ($item['duration_minutes'] ?? null) !== null ? esc((string) $item['duration_minutes']) . ' mnt' : 'Mandiri' ?>
                                        </span>
                                        <span class="text-muted">&middot;</span>
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($item['class_count'] ?? 0) ?> Batch Kelas
                                        </span>
                                        <?php if (! empty($item['enrolled_count'])): ?>
                                            <span class="text-muted">&middot;</span>
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
                        $imgSrc = base_url("assets/main/images/resources/course-list-img-{$imgIdx}.jpg");
                        $classStatus = strtolower((string) ($item['status'] ?? 'scheduled'));
                        $statusBadge = $classStatus === 'active'
                            ? ['label' => 'Aktif Berjalan', 'badge' => 'bg-success text-white', 'icon' => 'fa-play-circle']
                            : ['label' => 'Jadwal Terbuka', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-clock'];
                        ?>
                        <?php
                        $startDate = ! empty($item['start_at']) ? date('d M Y', strtotime($item['start_at'])) : '-';
                        $endDate   = ! empty($item['end_at']) ? date('d M Y', strtotime($item['end_at'])) : '-';
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
                                        <?php if (! empty($item['program_title'])): ?>
                                            <span class="badge bg-light text-secondary border small text-truncate" style="max-width: 140px;" title="<?= esc($item['program_title']) ?>">
                                                <?= esc($item['program_title']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <h4 class="arena-card__title">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>

                                    <!-- Course label preserved for test assertions & clarity -->
                                    <p class="learning-catalog__text mb-2 text-muted small">
                                        <i class="fas fa-book-open text-primary me-1"></i> Course: <a href="<?= ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : '#' ?>" class="text-secondary fw-semibold text-decoration-none hover-primary"><?= esc($item['course_title'] ?? '-') ?></a>
                                    </p>

                                    <!-- Timeline & Quota Strip -->
                                    <div class="d-flex flex-wrap align-items-center gap-2 py-2 my-2 border-top border-bottom" style="font-size: 12px;">
                                        <span class="text-secondary fw-semibold">
                                            <i class="far fa-calendar-alt text-primary me-1"></i> <?= $startDate ?> - <?= $endDate ?>
                                        </span>
                                        <span class="text-muted">&middot;</span>
                                        <span class="text-secondary">
                                            <i class="fas fa-users text-info me-1"></i> <?= esc((string) ($item['capacity'] ?? '-')) ?> Kursi
                                        </span>
                                        <?php if (isset($item['remaining_quota'])): ?>
                                            <span class="text-muted">&middot;</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fas fa-user-check me-1"></i> Sisa <?= (int) $item['remaining_quota'] ?> Slot
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Trainers Preview if available -->
                                    <?php if (! empty($item['trainers'])): ?>
                                        <div class="d-flex align-items-center gap-1 mb-2 small text-muted text-truncate" style="font-size: 11.5px;">
                                            <i class="fas fa-user-tie text-primary me-1"></i>
                                            <span class="fw-semibold">Fasilitator:</span>
                                            <?php foreach (array_slice($item['trainers'], 0, 2) as $t): ?>
                                                <span class="badge bg-light text-dark border"><?= esc($t['name'] ?? $t['username']) ?></span>
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
                            <article class="card h-100 border-0 shadow-sm radius-16 bg-white overflow-hidden transition-all hover-translate-y d-flex flex-column" style="box-shadow: 0 6px 20px rgba(15,23,42,0.06); border: 1px solid #e2e8f0 !important;">
                                <!-- Card Header Strip -->
                                <div class="px-4 pt-4 pb-0 text-center position-relative" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                                    <div class="mx-auto position-relative" style="width: 96px; height: 96px;">
                                        <div style="width: 96px; height: 96px; border-radius: 50%; overflow: hidden; border: 4px solid #ffffff; box-shadow: 0 6px 16px rgba(12,90,166,0.15);">
                                            <img src="<?= $imgSrc ?>" alt="<?= esc($item['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <span class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center border border-white" style="width: 26px; height: 26px; font-size: 11px;" title="Fasilitator Terverifikasi">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge <?= $profBadge['badge'] ?> px-3 py-1 radius-20 small">
                                            <i class="fas <?= $profBadge['icon'] ?> me-1"></i> <?= esc($profBadge['label']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-4 text-center d-flex flex-column flex-grow-1">
                                    <h4 class="fw-bold mb-1" style="font-size: 17px; line-height: 1.35;">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>
                                    <span class="text-muted small d-block mb-3" style="font-size: 12.5px;">
                                        Fasilitator Pengadaan Barang/Jasa
                                    </span>

                                    <!-- Micro-Stats Pill Strip -->
                                    <div class="d-flex align-items-center justify-content-around py-2 mb-3 radius-12 bg-light border" style="font-size: 12px;">
                                        <div class="text-center px-2">
                                            <strong class="d-block text-primary fs-6"><?= (int) ($item['total_classes'] ?? count($item['classes'] ?? [])) ?></strong>
                                            <span class="text-muted small">Batch Kelas</span>
                                        </div>
                                        <div class="border-end" style="height: 24px;"></div>
                                        <div class="text-center px-2">
                                            <strong class="d-block text-warning-emphasis fs-6"><?= (int) ($item['total_courses'] ?? count($item['courses'] ?? [])) ?></strong>
                                            <span class="text-muted small">Course PBJ</span>
                                        </div>
                                        <div class="border-end" style="height: 24px;"></div>
                                        <div class="text-center px-2">
                                            <strong class="d-block text-success fs-6"><?= (int) ($item['total_students'] ?? 0) ?></strong>
                                            <span class="text-muted small">Peserta</span>
                                        </div>
                                    </div>

                                    <!-- Recent Active Classes Preview -->
                                    <?php if (! empty($item['classes'])): ?>
                                        <div class="mb-3 text-start">
                                            <span class="text-muted small fw-bold d-block mb-1" style="font-size: 11px;">
                                                <i class="fas fa-chalkboard text-primary me-1"></i> Kelas yang Diampu:
                                            </span>
                                            <div class="d-flex flex-column gap-1">
                                                <?php foreach (array_slice($item['classes'], 0, 2) as $c): ?>
                                                    <span class="small text-truncate text-secondary" style="font-size: 11.5px;" title="<?= esc($c['name']) ?>">
                                                        <i class="fas fa-check-circle text-success me-1" style="font-size: 9px;"></i>
                                                        <?= esc($c['name']) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- CTA Button -->
                                    <div class="mt-auto pt-2">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="btn btn-outline-primary radius-8 w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2">
                                            <span>Lihat Profil &amp; Jadwal</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
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
                        $coverImg = base_url('assets/main/images/resources/' . $bgImages[($index % count($bgImages))]);
                        ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                            <article class="arena-card arena-program-card h-100">
                                <!-- Card Cover -->
                                <div class="arena-program-card__cover position-relative" style="height: 180px;">
                                    <img src="<?= $coverImg ?>" alt="<?= esc($item['title'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.85); transition: transform 0.4s ease;" loading="lazy">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8,47,92,0.35) 0%, rgba(4,24,48,0.85) 100%);"></div>

                                    <!-- Badges Top Left -->
                                    <div class="position-absolute top-0 start-0 p-3 d-flex flex-wrap gap-2 z-2">
                                        <?php if (! empty($item['featured'])): ?>
                                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-12 shadow-sm" style="font-size: 11px;">
                                                <i class="fas fa-star me-1 text-dark"></i> Kurikulum Unggulan
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-white text-primary fw-bold px-2 py-1 radius-12 shadow-sm" style="font-size: 11px;">
                                            <i class="fas fa-shield-alt me-1"></i> Standar LKPP
                                        </span>
                                    </div>

                                    <!-- Floating Bookmark Top Right -->
                                    <div class="arena-card-floating-bookmark">
                                        <?= view('landing/components/bookmark_button', [
                                            'type'     => 'program',
                                            'id'       => (int) ($item['id'] ?? 0),
                                            'title'    => $item['title'] ?? null,
                                            'iconOnly' => true,
                                            'size'     => 'sm',
                                        ]) ?>
                                    </div>

                                    <!-- Category Tag Bottom Left -->
                                    <div class="position-absolute bottom-0 start-0 p-3 z-2 w-100">
                                        <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 10.5px;">
                                            Program Terstruktur
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="arena-card__body">
                                    <h4 class="arena-card__title">
                                        <a href="<?= route_to($detailRoute, $item['slug']) ?>" class="hover-primary">
                                            <?= esc($item['title'] ?? '-') ?>
                                        </a>
                                    </h4>

                                    <p class="arena-card__text mb-2">
                                        <?= esc(mb_strimwidth($item['summary'] ?? $item['text'] ?? 'Kurikulum terpadu pengadaan barang/jasa.', 0, 115, '…')) ?>
                                    </p>

                                    <!-- Curriculum Micro-Stats Strip -->
                                    <div class="d-flex flex-wrap align-items-center gap-2 py-2 mb-3 border-top border-bottom" style="font-size: 12px;">
                                        <span class="text-primary fw-semibold">
                                            <i class="fas fa-book-open me-1"></i> <?= (int) ($item['course_count'] ?? 0) ?> Course PBJ
                                        </span>
                                        <span class="text-muted">&middot;</span>
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($item['class_count'] ?? 0) ?> Batch Kelas
                                        </span>
                                        <?php if (! empty($item['enrolled_count'])): ?>
                                            <span class="text-muted">&middot;</span>
                                            <span class="text-info fw-semibold">
                                                <i class="fas fa-users me-1"></i> <?= (int) $item['enrolled_count'] ?> Peserta
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Sample Courses Preview -->
                                    <?php if (! empty($item['sample_courses'])): ?>
                                        <div class="arena-program-course-preview mb-3">
                                            <div class="text-muted small fw-bold mb-1 d-flex align-items-center justify-content-between" style="font-size: 11.5px;">
                                                <span><i class="fas fa-stream text-primary me-1"></i> Modul Terkait:</span>
                                                <span class="badge bg-white text-muted border"><?= count($item['sample_courses']) ?> dari <?= (int) $item['course_count'] ?></span>
                                            </div>
                                            <div class="d-flex flex-column gap-1">
                                                <?php foreach ($item['sample_courses'] as $sc): ?>
                                                    <a href="<?= route_to('public.learning.course.detail', $sc['slug']) ?>" class="small text-truncate text-secondary text-decoration-none hover-primary" style="font-size: 12px;">
                                                        <i class="fas fa-check-circle text-success me-1" style="font-size: 10px;"></i>
                                                        <?= esc($sc['title']) ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Card Actions -->
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

            <!-- Pagination -->
            <?php if ($pager): ?>
                <?php $links = $pager->links($catalogType, 'default_full') ?: $pager->links('default', 'default_full'); ?>
                <?php if ($links): ?>
                    <div class="row mt-5">
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