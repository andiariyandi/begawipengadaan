<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$stats = $stats ?? [
    'total_programs'     => 0,
    'total_courses'      => 0,
    'total_classes'      => 0,
    'total_trainers'     => 0,
    'total_participants' => 0,
    'total_certificates' => 0,
];
$featuredPrograms = $featuredPrograms ?? [];
$popularCourses   = $popularCourses ?? [];
$activeClasses    = $activeClasses ?? [];
$featuredTrainers = $featuredTrainers ?? [];
$isLoggedIn       = session()->get('isLoggedIn') === true;

// Assemble Hero Slider Items (Layout Agenda Style: Programs, Courses, and Classes)
$featuredPrograms = $featuredPrograms ?? [];
$featuredCourses  = $featuredCourses ?? [];
$popularCourses   = $popularCourses ?? [];
$activeClasses    = $activeClasses ?? [];
$featuredTrainers = $featuredTrainers ?? [];
$isLoggedIn       = session()->get('isLoggedIn') === true;
$spotlightMode    = $spotlightMode ?? 'all';
$spotlightLimit   = (int) ($spotlightLimit ?? 6);

$sliderItems      = $sliderItems ?? [];
$bgSliderCovers = [
    base_url('assets/main/images/course/course-details-img-1.jpg'),
    base_url('assets/main/images/course/course-list-img-1.jpg'),
    base_url('assets/main/images/course/course-list-img-2.jpg'),
    base_url('assets/main/images/course/course-list-img-3.jpg'),
];

if (empty($sliderItems)) {
$progItems = [];
if (! empty($featuredPrograms)) {
    foreach ($featuredPrograms as $fIdx => $fProg) {
        $cover = ! empty($fProg['cover_url'])
            ? (str_starts_with($fProg['cover_url'], 'http') ? $fProg['cover_url'] : base_url($fProg['cover_url']))
            : $bgSliderCovers[$fIdx % count($bgSliderCovers)];
        $progItems[] = [
            'category'    => 'program',
            'title'       => $fProg['title'],
            'url'         => route_to('public.learning.program.detail', $fProg['slug']),
            'cover_url'   => $cover,
            'tag_class'   => 'evt-badge-flag--featured',
            'status_tag'  => '<iconify-icon icon="solar:star-bold" class="align-middle"></iconify-icon> Kurikulum Unggulan',
            'type_label'  => 'Program Kurikulum',
            'badge_extra' => 'UKPBJ Prov. Lampung',
            'meta_1'      => '<iconify-icon icon="solar:layers-minimalistic-bold" class="text-warning align-middle"></iconify-icon> ' . (int) ($fProg['course_count'] ?? 0) . ' Course Terpadu',
            'meta_2'      => '<iconify-icon icon="solar:shield-check-bold" class="text-info align-middle"></iconify-icon> Jalur Belajar Terstruktur',
            'meta_3'      => '<iconify-icon icon="solar:diploma-verified-bold" class="text-success align-middle"></iconify-icon> Evaluasi Pemahaman &amp; Sertifikat Belajar',
            'action_text' => 'Eksplorasi Kurikulum',
        ];
    }
}

$courseItems = [];
if (! empty($featuredCourses)) {
    foreach ($featuredCourses as $cIdx => $fCourse) {
        $cover = ! empty($fCourse['cover_url'])
            ? (str_starts_with($fCourse['cover_url'], 'http') ? $fCourse['cover_url'] : base_url($fCourse['cover_url']))
            : $bgSliderCovers[($cIdx + 1) % count($bgSliderCovers)];
        $isFeat = (int) ($fCourse['featured'] ?? 0) === 1;
        $courseItems[] = [
            'category'    => 'course',
            'title'       => $fCourse['title'],
            'url'         => route_to('public.learning.course.detail', $fCourse['slug']),
            'cover_url'   => $cover,
            'tag_class'   => $isFeat ? 'evt-badge-flag--course' : 'evt-badge-flag--coming',
            'status_tag'  => '<iconify-icon icon="solar:book-bookmark-bold" class="align-middle"></iconify-icon> ' . ($isFeat ? 'Course Pilihan' : 'Course Mandiri'),
            'type_label'  => 'Course PBJ',
            'badge_extra' => ucfirst($fCourse['level'] ?? 'Dasar') . ' Level',
            'meta_1'      => '<iconify-icon icon="solar:clock-circle-bold" class="text-warning align-middle"></iconify-icon> ' . format_learning_jp($fCourse['duration_minutes'] ?? null),
            'meta_2'      => '<iconify-icon icon="solar:folder-with-files-bold" class="text-info align-middle"></iconify-icon> ' . esc($fCourse['program_title'] ?? 'Kurikulum PBJ'),
            'meta_3'      => '<iconify-icon icon="solar:laptop-minimalistic-bold" class="text-success align-middle"></iconify-icon> Pembelajaran Digital LMS',
            'action_text' => 'Pelajari Course Ini',
        ];
    }
}

$classItems = [];
if (! empty($activeClasses)) {
    foreach ($activeClasses as $kIdx => $aCls) {
        $effectiveClassCover = ! empty($aCls['cover_url']) ? $aCls['cover_url'] : ($aCls['course_cover_url'] ?? '');
        $cover = ! empty($effectiveClassCover)
            ? (str_starts_with($effectiveClassCover, 'http') ? $effectiveClassCover : base_url($effectiveClassCover))
            : $bgSliderCovers[($kIdx + 2) % count($bgSliderCovers)];

        $classItems[] = [
            'category'    => 'class',
            'title'       => $aCls['name'],
            'url'         => route_to('public.learning.class.detail', $aCls['code']),
            'cover_url'   => $cover,
            'tag_class'   => 'evt-badge-flag--open',
            'status_tag'  => '<span class="dot"></span> ' . ($aCls['status'] === 'active' ? 'Sedang Berjalan' : 'Pendaftaran Dibuka'),
            'type_label'  => 'Batch Kelas Terbuka',
            'badge_extra' => strtoupper($aCls['learning_method'] ?? 'Online') . ' Pelatihan',
            'meta_1'      => '<iconify-icon icon="solar:calendar-date-bold" class="text-warning align-middle"></iconify-icon> Mulai: ' . (! empty($aCls['start_at']) ? date('d M Y', strtotime($aCls['start_at'])) : 'Segera'),
            'meta_2'      => '<iconify-icon icon="solar:notebook-bold" class="text-info align-middle"></iconify-icon> ' . esc($aCls['course_title'] ?? 'Course PBJ'),
            'meta_3'      => '<iconify-icon icon="solar:users-group-rounded-bold" class="text-success align-middle"></iconify-icon> ' . ((int) ($aCls['capacity'] ?? 0) > 0 ? 'Kuota ' . (int) $aCls['capacity'] . ' Peserta' : 'Kuota Fleksibel'),
            'action_text' => 'Detail &amp; Daftar Kelas',
        ];
    }
}

// Assemble based on administrator spotlight mode & limit
if ($spotlightMode === 'program') {
    $sliderItems = array_slice($progItems, 0, $spotlightLimit);
} elseif ($spotlightMode === 'course') {
    $sliderItems = array_slice($courseItems, 0, $spotlightLimit);
} elseif ($spotlightMode === 'class') {
    $sliderItems = array_slice($classItems, 0, $spotlightLimit);
} else {
    // Interleave all three categories up to $spotlightLimit
    $sliderItems      = $sliderItems ?? [];
    $maxCount = max(count($progItems), count($courseItems), count($classItems));
    for ($i = 0; $i < $maxCount; $i++) {
        if (isset($progItems[$i]) && count($sliderItems) < $spotlightLimit) {
            $sliderItems[] = $progItems[$i];
        }
        if (isset($courseItems[$i]) && count($sliderItems) < $spotlightLimit) {
            $sliderItems[] = $courseItems[$i];
        }
        if (isset($classItems[$i]) && count($sliderItems) < $spotlightLimit) {
            $sliderItems[] = $classItems[$i];
        }
    }
}
}
?>

<!-- =========================================================================
     MAIN LEARNING CONTENT SECTION — Following Event & News Clean Benchmark
     ========================================================================= -->
<section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-4" id="kategori-liputan-section" style="background: #fafbfc;">
    <div class="container">

        <!-- Compact Breadcrumb + Header Title & Unified Search -->
        <div class="evt-hero-banner mb-2 mb-md-3">
            <div class="arena-breadcrumb evt-breadcrumb">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <span class="current">Arena Belajar BegawiPengadaan Lampung</span>
            </div>

            <div class="row align-items-center justify-content-between g-3 mb-2">
                <div class="col-lg-7">
                    <h1 class="evt-hero-title mb-2">
                        Pusat Kegiatan Belajar &amp; Peningkatan Wawasan PBJ <br>
                        <span>Pemerintah Provinsi Lampung</span>
                    </h1>
                    <p class="evt-hero-desc mb-0">
                        Akselerasi kapasitas SDM pengadaan melalui kurikulum berjenjang terpadu, materi mandiri komprehensif, dan pendampingan kelas interaktif bersama fasilitator tersertifikasi.
                    </p>
                </div>
                <div class="col-lg-5">
                    <!-- Search Form -->
                    <form action="<?= site_url('arena-belajar/course') ?>" method="get" class="evt-search-box arena-search-bar ms-lg-auto">
                        <iconify-icon icon="solar:magnifer-linear" class="text-slate-500 text-xl"></iconify-icon>
                        <input type="text" name="q" placeholder="Cari course, materi regulasi, modul, atau kelas PBJ...">
                        <button type="submit">
                            <span>Cari Materi</span>
                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- HERO SLIDER (SEPERTI LAYOUT AGENDA) -->
        <?php if (! empty($sliderItems)): ?>
            <div class="evt-headline mb-4">
                <div id="arenaHeadlineCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-inner radius-16 overflow-hidden">
                        <?php foreach ($sliderItems as $sIndex => $sItem): ?>
                            <div class="carousel-item <?= $sIndex === 0 ? 'active' : '' ?>">
                                <div class="evt-headline__inner">
                                    <div class="evt-headline__media">
                                        <img src="<?= esc($sItem['cover_url']) ?>" alt="<?= esc($sItem['title']) ?>" loading="<?= $sIndex === 0 ? 'eager' : 'lazy' ?>">
                                        <div class="evt-headline__badges">
                                            <span class="evt-badge-flag <?= $sItem['tag_class'] ?>">
                                                <?= $sItem['status_tag'] ?>
                                            </span>
                                            <span class="evt-badge-pill"><?= esc($sItem['type_label']) ?></span>
                                            <?php if (! empty($sItem['badge_extra'])): ?>
                                                <span class="evt-badge-pill evt-badge-pill--cert">
                                                    <iconify-icon icon="solar:diploma-verified-bold" class="align-middle"></iconify-icon> <?= esc($sItem['badge_extra']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="evt-headline__counter">
                                            <span class="dot"></span>
                                            <span class="d-none d-sm-inline">Sorotan Pembelajaran </span><span>#<?= $sIndex + 1 ?> / <?= count($sliderItems) ?></span>
                                        </div>
                                        <div class="evt-headline__caption">
                                            <div class="evt-headline__meta-row">
                                                <span><?= $sItem['meta_1'] ?></span>
                                                <span>&bull;</span>
                                                <span><?= $sItem['meta_2'] ?></span>
                                                <span>&bull;</span>
                                                <span><?= $sItem['meta_3'] ?></span>
                                            </div>
                                            <h2 class="evt-headline__title">
                                                <a href="<?= esc($sItem['url']) ?>">
                                                    <?= esc($sItem['title']) ?>
                                                </a>
                                            </h2>
                                            <div class="evt-headline__action">
                                                <a href="<?= esc($sItem['url']) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-8 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                                    <span><?= esc($sItem['action_text']) ?></span>
                                                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($sliderItems) > 1): ?>
                        <button class="evt-headline__nav prev" type="button" data-bs-target="#arenaHeadlineCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                            <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                        </button>
                        <button class="evt-headline__nav next" type="button" data-bs-target="#arenaHeadlineCarousel" data-bs-slide="next" aria-label="Selanjutnya">
                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                        </button>
                        <div class="carousel-indicators evt-headline__indicators">
                            <?php foreach ($sliderItems as $sIndex => $sItem): ?>
                                <button type="button" data-bs-target="#arenaHeadlineCarousel" data-bs-slide-to="<?= $sIndex ?>" class="<?= $sIndex === 0 ? 'active' : '' ?>" aria-current="<?= $sIndex === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $sIndex + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-3 g-lg-4">
            <!-- Left Column: Main Learning Content (col-lg-8) -->
            <div class="col-lg-8">

                <!-- Showcase 1: Kelas & Batch Siap Daftar -->
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag">
                                <i class="fas fa-calendar-check me-1"></i> Pelatihan Interaktif &amp; Pendampingan
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Kelas &amp; Batch Siap Daftar</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Ikuti kelas terjadwal dengan pendampingan fasilitator pengadaan, kuis evaluasi, dan sertifikasi.
                            </p>
                        </div>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                            <span>Semua Kelas</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <?php if ($activeClasses === []): ?>
                        <div class="alert alert-light text-center border p-4 radius-12">Belum ada kelas aktif yang terjadwal.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach (array_slice($activeClasses, 0, 4) as $index => $class): ?>
                                <?php
                                $imgIndex = (($index + 2) % 6) + 1;
                                $defaultImg = "assets/main/images/resources/course-list-img-{$imgIndex}.jpg";
                                $effectiveClassCover = ! empty($class['cover_url']) ? $class['cover_url'] : ($class['course_cover_url'] ?? '');
                                $coverImg = ! empty($effectiveClassCover)
                                    ? (str_starts_with($effectiveClassCover, 'http') ? $effectiveClassCover : base_url($effectiveClassCover))
                                    : base_url($defaultImg);

                                $method = strtolower((string) ($class['learning_method'] ?? 'online'));
                                $methodBadge = match ($method) {
                                    'offline' => ['label' => 'Tatap Muka', 'badge' => 'bg-info text-white', 'icon' => 'fa-building'],
                                    'hybrid'  => ['label' => 'Hybrid', 'badge' => 'bg-purple text-white', 'icon' => 'fa-network-wired'],
                                    default   => ['label' => 'Online LMS', 'badge' => 'bg-primary text-white', 'icon' => 'fa-laptop'],
                                };

                                $capacity = (int) ($class['capacity'] ?? 0);
                                $enrolled = (int) ($class['enrolled_count'] ?? 0);
                                $pct = $capacity > 0 ? min(100, round(($enrolled / $capacity) * 100)) : 0;
                                ?>
                                <div class="col-12">
                                    <article class="arena-class-card">
                                        <div class="arena-class-card__thumb">
                                            <img src="<?= $coverImg ?>" alt="<?= esc($class['name']) ?>" loading="lazy">
                                            <div class="arena-card__badge-row">
                                                <?php if ((int) ($class['featured'] ?? 0) === 1): ?>
                                                    <span class="arena-pill-badge bg-warning text-dark">
                                                        <i class="fas fa-star me-1"></i> Unggulan
                                                    </span>
                                                <?php endif; ?>
                                                <span class="arena-pill-badge arena-pill-badge--accent">
                                                    <?= esc(ucfirst($class['status'] ?? 'Aktif')) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'      => 'learning_class',
                                                    'id'        => (int) $class['id'],
                                                    'title'     => $class['name'] ?? null,
                                                    'iconOnly'  => true,
                                                    'size'      => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-class-card__body">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="arena-card__category mb-0 text-truncate" style="max-width: 220px;">
                                                    Course: <?= esc($class['course_title'] ?? 'PBJ') ?>
                                                </span>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge <?= $methodBadge['badge'] ?> px-2 py-1 radius-4 small" style="font-size: 11.5px;">
                                                        <i class="fas <?= $methodBadge['icon'] ?> me-1"></i> <?= esc($methodBadge['label']) ?>
                                                    </span>
                                                    <span class="badge bg-light text-dark border px-2 py-1 radius-4 font-monospace" style="font-size: 11.5px;">
                                                        <?= esc($class['code']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <h4 class="fw-bold mb-1" style="font-size: 17px; line-height: 1.4;">
                                                <a href="<?= route_to('public.learning.class.detail', $class['code']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($class['name']) ?>
                                                </a>
                                            </h4>

                                            <?php if (! empty($class['description'])): ?>
                                                <p class="small text-muted mb-2 text-truncate" style="font-size: 13.5px;">
                                                    <?= esc(mb_strimwidth($class['description'], 0, 75, '…')) ?>
                                                </p>
                                            <?php endif; ?>

                                            <!-- Quota & Progress Strip -->
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center justify-content-between text-xs text-muted mb-1" style="font-size: 12.5px;">
                                                    <span><i class="fas fa-users text-primary me-1"></i> <?= $enrolled ?>/<?= $capacity ?> Peserta</span>
                                                    <span class="fw-semibold <?= $pct >= 90 ? 'text-danger' : 'text-success' ?>"><?= $pct ?>% Kuota</span>
                                                </div>
                                                <div class="progress" style="height: 5px; border-radius: 4px; background: #e2e8f0;">
                                                    <div class="progress-bar <?= $pct >= 90 ? 'bg-danger' : 'bg-success' ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between py-1 my-1 border-top border-bottom text-muted" style="font-size: 12.5px;">
                                                <span>
                                                    <i class="far fa-calendar-alt text-warning me-1"></i>
                                                    <?= esc(($class['start_at'] ?? '-') . ' s/d ' . ($class['end_at'] ?? '-')) ?>
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-2">
                                                <a href="<?= route_to('public.learning.class.detail', $class['code']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                                    <span>Detail &amp; Daftar</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'learning_class',
                                                    'id'    => (int) $class['id'],
                                                    'title' => $class['name'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Showcase 2: Program Pembelajaran Unggulan -->
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag">
                                <i class="fas fa-award me-1"></i> Kurikulum Komprehensif Berjenjang
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Program Pembelajaran Unggulan</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Jalur kurikulum pelatihan terpadu yang dirancang dari pemahaman dasar hingga manajerial PBJ.
                            </p>
                        </div>
                        <a href="<?= site_url('arena-belajar/program') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                            <span>Lihat Semua Program</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <?php if ($featuredPrograms === []): ?>
                        <div class="alert alert-light text-center border p-4 radius-12">Belum ada program unggulan yang dipublikasikan.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach (array_slice($featuredPrograms, 0, 4) as $index => $prog): ?>
                                <?php
                                $bgImages = [
                                    'course-details-img-1.jpg',
                                    'course-list-img-1.jpg',
                                    'course-list-img-2.jpg',
                                    'course-list-img-3.jpg',
                                ];
                                $defaultCover = 'assets/main/images/resources/' . $bgImages[($index % count($bgImages))];
                                $coverImg = ! empty($prog['cover_url'])
                                    ? (str_starts_with($prog['cover_url'], 'http') ? $prog['cover_url'] : base_url($prog['cover_url']))
                                    : base_url($defaultCover);
                                ?>
                                <div class="col-md-6">
                                    <article class="arena-card arena-program-card h-100">
                                        <div class="arena-program-card__cover">
                                            <img src="<?= $coverImg ?>" alt="<?= esc($prog['title']) ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.85); transition: transform 0.4s ease;" loading="lazy">
                                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8,47,92,0.15) 0%, rgba(4,24,48,0.85) 100%);"></div>
                                            <div class="arena-card__badge-row">
                                                <?php if ((int) ($prog['featured'] ?? 0) === 1): ?>
                                                    <span class="arena-pill-badge arena-pill-badge--accent">
                                                        <i class="fas fa-star me-1"></i> Unggulan
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="arena-card-floating-bookmark">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'      => 'program',
                                                    'id'        => (int) $prog['id'],
                                                    'title'     => $prog['title'] ?? null,
                                                    'iconOnly'  => true,
                                                    'size'      => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body">
                                            <span class="arena-card__category">Program Terstruktur</span>
                                            <h4 class="arena-card__title">
                                                <a href="<?= route_to('public.learning.program.detail', $prog['slug']) ?>" class="hover-primary" title="<?= esc($prog['title']) ?>">
                                                    <?= esc($prog['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text">
                                                <?= esc(mb_strimwidth($prog['summary'] ?: ($prog['target_audience'] ?: 'Program kegiatan belajar dan peningkatan wawasan pengadaan barang dan jasa.'), 0, 85, '…')) ?>
                                            </p>
                                            <div class="arena-card__meta">
                                                <span><i class="fas fa-book-open text-primary"></i> <?= (int) ($prog['course_count'] ?? 0) ?> Course</span>
                                                <span><i class="fas fa-shield-alt text-success"></i> Standar</span>
                                            </div>
                                            <div class="arena-card__actions mt-auto">
                                                <a href="<?= route_to('public.learning.program.detail', $prog['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                                    <span>Pelajari</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'program',
                                                    'id'    => (int) $prog['id'],
                                                    'title' => $prog['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Showcase 3: Course PBJ Terpopuler & Pilihan -->
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                        <div class="arena-section-head mb-0">
                            <span class="arena-section-head__tag">
                                <i class="fas fa-fire me-1"></i> Pembelajaran Mandiri &amp; Fleksibel
                            </span>
                            <h3 class="arena-section-head__title" style="font-size: 22px;">Course PBJ Terpopuler &amp; Pilihan</h3>
                            <p class="arena-section-head__desc mb-0" style="font-size: 14.5px;">
                                Pelajari regulasi, perencanaan, pemilihan penyedia, penyusunan kontrak, dan evaluasi PBJ.
                            </p>
                        </div>
                        <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-primary btn-sm radius-8 px-3 py-1 mt-2 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13px;">
                            <span>Semua Course</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Quick Level Filter Tabs -->
                    <div class="d-flex flex-wrap align-items-center gap-1 mb-3 pb-2 border-bottom">
                        <span class="small fw-bold text-muted me-2" style="font-size: 13px;"><i class="fas fa-filter me-1"></i> Level:</span>
                        <button type="button" class="btn btn-sm btn-primary radius-20 px-3 py-1 course-filter-btn active" data-filter="all" onclick="filterCourseCards('all', this)" style="font-size: 12.5px;">
                            Semua Level
                        </button>
                        <button type="button" class="btn btn-sm btn-light border radius-20 px-3 py-1 course-filter-btn" data-filter="basic" onclick="filterCourseCards('basic', this)" style="font-size: 12.5px;">
                            <i class="fas fa-shield-alt text-info me-1"></i> Dasar
                        </button>
                        <button type="button" class="btn btn-sm btn-light border radius-20 px-3 py-1 course-filter-btn" data-filter="intermediate" onclick="filterCourseCards('intermediate', this)" style="font-size: 12.5px;">
                            <i class="fas fa-chart-line text-warning me-1"></i> Menengah
                        </button>
                        <button type="button" class="btn btn-sm btn-light border radius-20 px-3 py-1 course-filter-btn" data-filter="advanced" onclick="filterCourseCards('advanced', this)" style="font-size: 12.5px;">
                            <i class="fas fa-award text-danger me-1"></i> Lanjutan
                        </button>
                    </div>

                    <?php if ($popularCourses === []): ?>
                        <div class="alert alert-light text-center border p-4 radius-12">Belum ada course yang dipublikasikan.</div>
                    <?php else: ?>
                        <div class="row g-3" id="courseCardsGrid">
                            <?php foreach (array_slice($popularCourses, 0, 4) as $index => $course): ?>
                                <?php
                                $imgIndex = ($index % 6) + 1;
                                $defaultImg = "assets/main/images/resources/course-list-img-{$imgIndex}.jpg";
                                $coverImg = ! empty($course['cover_url'])
                                    ? (str_starts_with($course['cover_url'], 'http') ? $course['cover_url'] : base_url($course['cover_url']))
                                    : base_url($defaultImg);
                                $levelVal = strtolower((string) ($course['level'] ?? 'basic'));
                                $levelBadge = match ($levelVal) {
                                    'intermediate' => ['label' => 'Menengah', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-chart-line'],
                                    'advanced'     => ['label' => 'Lanjutan', 'badge' => 'bg-danger text-white', 'icon' => 'fa-award'],
                                    default        => ['label' => 'Dasar', 'badge' => 'bg-info text-white', 'icon' => 'fa-shield-alt'],
                                };
                                ?>
                                <div class="col-md-6 course-card-wrapper" data-level="<?= esc($levelVal) ?>">
                                    <article class="arena-card h-100">
                                        <div class="arena-card__thumb">
                                            <img src="<?= $coverImg ?>" alt="<?= esc($course['title']) ?>" loading="lazy">
                                            <div class="arena-card__badge-row">
                                                <?php if ((int) ($course['featured'] ?? 0) === 1): ?>
                                                    <span class="arena-pill-badge bg-warning text-dark">
                                                        <i class="fas fa-star me-1"></i> Unggulan
                                                    </span>
                                                <?php endif; ?>
                                                <span class="arena-pill-badge <?= $levelBadge['badge'] ?>">
                                                    <i class="fas <?= $levelBadge['icon'] ?> me-1"></i> <?= esc($levelBadge['label']) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card-floating-bookmark">
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'      => 'course',
                                                    'id'        => (int) $course['id'],
                                                    'title'     => $course['title'] ?? null,
                                                    'iconOnly'  => true,
                                                    'size'      => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="arena-card__body">
                                            <span class="arena-card__category"><?= esc($course['program_title'] ?? 'Materi Mandiri') ?></span>
                                            <h4 class="arena-card__title">
                                                <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>" class="hover-primary" title="<?= esc($course['title']) ?>">
                                                    <?= esc($course['title']) ?>
                                                </a>
                                            </h4>
                                            <p class="arena-card__text">
                                                <?= esc(mb_strimwidth($course['summary'] ?: ($course['target_audience'] ?: 'Pelajari topik regulasi dan penerapan praktis dalam pengadaan barang dan jasa pemerintah.'), 0, 95, '…')) ?>
                                            </p>
                                            <div class="arena-card__meta">
                                                <span>
                                                    <i class="far fa-clock text-warning"></i>
                                                    <?= format_learning_jp($course['duration_minutes'] ?? null) ?>
                                                </span>
                                                <span>
                                                    <i class="fas fa-signal text-info"></i> <?= esc($levelBadge['label']) ?>
                                                </span>
                                            </div>
                                            <div class="arena-card__actions mt-auto">
                                                <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>" class="arena-btn-primary flex-grow-1 justify-content-center">
                                                    <span>Lihat Silabus</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'course',
                                                    'id'    => (int) $course['id'],
                                                    'title' => $course['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FAQ Accordion -->
                <div class="card border-0 radius-16 p-4 shadow-sm bg-white mb-4">
                    <div class="arena-section-head mb-3">
                        <span class="arena-section-head__tag">
                            <i class="fas fa-question-circle me-1"></i> Bantuan &amp; Informasi
                        </span>
                        <h3 class="arena-section-head__title" style="font-size: 20px;">Pertanyaan Umum Seputar Arena Belajar (FAQ)</h3>
                    </div>
                    <div class="accordion border-0" id="arenaFaqAccordion">
                        <div class="accordion-item radius-10 border mb-2 overflow-hidden shadow-2xs">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button fw-bold text-dark py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1" style="font-size: 14.5px;">
                                    <i class="fas fa-book-open text-primary me-2"></i> Apa perbedaan antara Program, Course Mandiri, dan Kelas Terjadwal?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#arenaFaqAccordion">
                                <div class="accordion-body px-3 pb-3 pt-1 text-secondary" style="font-size: 14px; line-height: 1.6;">
                                    <strong>Program</strong> adalah rangkaian kurikulum berjenjang. <strong>Course</strong> adalah modul pembelajaran mandiri fleksibel. Sedangkan <strong>Kelas / Batch</strong> adalah sesi pelatihan terjadwal dengan pendampingan langsung oleh instruktur pengajar, diskusi interaktif, dan evaluasi kelulusan.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item radius-10 border mb-2 overflow-hidden shadow-2xs">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed fw-bold text-dark py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2" style="font-size: 14.5px;">
                                    <i class="fas fa-user-check text-primary me-2"></i> Siapa saja yang dapat mendaftar dan mengikuti pelatihan?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#arenaFaqAccordion">
                                <div class="accordion-body px-3 pb-3 pt-1 text-secondary" style="font-size: 14px; line-height: 1.6;">
                                    Seluruh Aparatur Sipil Negara (ASN) di lingkungan Pemerintah Provinsi Lampung dan Kabupaten/Kota, PPK, Pejabat Pengadaan, Pokja Pemilihan, serta Penyedia Barang/Jasa dan akademisi dapat mendaftar akun dan mengikuti pembelajaran.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item radius-10 border overflow-hidden shadow-2xs">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed fw-bold text-dark py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3" style="font-size: 14.5px;">
                                    <i class="fas fa-certificate text-primary me-2"></i> Apakah peserta akan mendapatkan sertifikat resmi setelah lulus kelas?
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#arenaFaqAccordion">
                                <div class="accordion-body px-3 pb-3 pt-1 text-secondary" style="font-size: 14px; line-height: 1.6;">
                                    Ya. Peserta yang memenuhi evaluasi kelulusan akan diterbitkan <strong>Sertifikat Digital Resmi</strong> ber-QR Code yang langsung tersinkronisasi dan dapat diverifikasi validitasnya melalui laman verifikasi publik BegawiPengadaan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Sidebar (col-lg-4) — Modeled after Clean News Landing Benchmark -->
            <div class="col-lg-4">

                <!-- Sidebar Widget 1: Quick Search Box -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-search"></i>
                        <span>Cari Materi PBJ</span>
                    </h4>
                    <form action="<?= site_url('arena-belajar/course') ?>" method="get" class="berita-searchbox news-search-box">
                        <input type="text" name="q" placeholder="Cari course, regulasi, materi...">
                        <button type="submit" aria-label="Cari"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="d-flex flex-wrap gap-1 mt-3">
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Perpres 46/2025')) ?>" class="badge bg-light text-dark border text-decoration-none">Perpres 46/2025</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('HPS')) ?>" class="badge bg-light text-dark border text-decoration-none">HPS</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('E-Katalog')) ?>" class="badge bg-light text-dark border text-decoration-none">E-Katalog</a>
                        <a href="<?= site_url('arena-belajar/course?q=' . urlencode('Kontrak')) ?>" class="badge bg-light text-dark border text-decoration-none">Kontrak</a>
                    </div>
                </div>

                <!-- Sidebar Widget 2: Kategori Pembelajaran -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-folder-open" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Kategori Pembelajaran</span>
                    </h4>
                    <nav class="arena-nav-pills berita-catlist" aria-label="Kategori Pembelajaran">
                        <a href="<?= site_url('arena-belajar') ?>" class="arena-nav-pill is-active">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Semua Eksplorasi</span>
                            <span class="count">PBJ</span>
                        </a>
                        <a href="<?= site_url('arena-belajar/program') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0284c7"></span>Program Kurikulum</span>
                            <span class="count"><?= (int) $stats['total_programs'] ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/course') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#0891b2"></span>Course Mandiri PBJ</span>
                            <span class="count"><?= (int) $stats['total_courses'] ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#16a34a"></span>Kelas &amp; Batch Siap Daftar</span>
                            <span class="count"><?= (int) $stats['total_classes'] ?></span>
                        </a>
                        <a href="<?= site_url('arena-belajar/pengajar') ?>" class="arena-nav-pill">
                            <span class="left"><span class="dot" style="background:#9333ea"></span>Instruktur &amp; Pengajar</span>
                            <span class="count"><?= (int) $stats['total_trainers'] ?></span>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Widget 3: Peningkatan Pengetahuan & Wawasan PBJ Lampung (Dipindahkan setelah Kategori) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-route" style="background: #e0f2fe; color: #0284c7;"></i>
                        <span>Jalur Belajar &amp; Peningkatan Pengetahuan</span>
                    </h4>
                    <p class="text-muted small mb-3" style="font-size: 13px; line-height: 1.55;">
                        Roadmap Jalur Belajar Pengadaan Barang &amp; Jasa Berjenjang dari dasar regulasi hingga mitigasi risiko:
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 radius-8 border-start border-3 border-info bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold radius-12" style="background: #e0f2fe; color: #0369a1; font-size: 11px;">1</span>
                                <strong style="font-size: 13px; color: #0f172a;">Tingkat Dasar (Basic)</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12px; line-height: 1.45;">Etika, kepatuhan &amp; kerangka hukum Perpres PBJ.</small>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-warning bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold radius-12" style="background: #fef3c7; color: #b45309; font-size: 11px;">2</span>
                                <strong style="font-size: 13px; color: #0f172a;">Perencanaan &amp; HPS</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12px; line-height: 1.45;">Penyusunan RUP, spesifikasi teknis &amp; survei pasar.</small>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 border-success bg-light">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold radius-12" style="background: #dcfce7; color: #15803d; font-size: 11px;">3</span>
                                <strong style="font-size: 13px; color: #0f172a;">Pemilihan Penyedia</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12px; line-height: 1.45;">E-Purchasing, E-Katalog, tender &amp; negosiasi.</small>
                        </div>
                        <div class="p-2 radius-8 border-start border-3 bg-light" style="border-left-color: #9333ea !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge fw-bold radius-12" style="background: #f3e8ff; color: #7e22ce; font-size: 11px;">4</span>
                                <strong style="font-size: 13px; color: #0f172a;">Kontrak &amp; Serah Terima</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12px; line-height: 1.45;">SPK, pengendalian mutu, BAST &amp; audit PBJ.</small>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 4: Program Pembelajaran Terbaru (3) -->
                <?php if (! empty($featuredPrograms)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-layer-group" style="background: #e0f2fe; color: #0284c7;"></i>
                            <span>Program Pembelajaran Terbaru</span>
                        </h4>
                        <?php foreach (array_slice($featuredPrograms, 0, 3) as $pRank => $pItem): ?>
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
                                        <span><i class="fas fa-book-open text-primary me-1"></i> <?= (int) ($pItem['course_count'] ?? 0) ?> Course</span>
                                        <span>&middot;</span>
                                        <span class="text-success fw-semibold"><i class="fas fa-certificate me-1"></i> Standar</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/program') ?>" class="btn btn-sm btn-outline-primary w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Program (<?= (int) $stats['total_programs'] ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 5: Course Mandiri PBJ Terbaru (3) -->
                <?php if (! empty($popularCourses)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-book-open" style="background: #cffafe; color: #0891b2;"></i>
                            <span>Course Mandiri PBJ Pilihan</span>
                        </h4>
                        <?php foreach (array_slice($popularCourses, 0, 3) as $cRank => $crsItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #e0f2fe; color: #0369a1;">
                                    <?= $cRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.learning.course.detail', $crsItem['slug']) ?>">
                                            <?= esc($crsItem['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="badge bg-light text-dark border" style="font-size: 10.5px;"><?= ucfirst((string) ($crsItem['level'] ?? 'Dasar')) ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1"></i> <?= format_learning_jp($crsItem['duration_minutes'] ?? 60) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-sm btn-outline-info w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Course (<?= (int) $stats['total_courses'] ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 6: Kelas & Batch Terdekat (3) -->
                <?php if (! empty($activeClasses)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-chalkboard-teacher" style="background: #dcfce7; color: #16a34a;"></i>
                            <span>Kelas &amp; Batch Siap Daftar</span>
                        </h4>
                        <?php foreach (array_slice($activeClasses, 0, 3) as $kRank => $kItem): ?>
                            <div class="berita-trend">
                                <div class="berita-trend__rank" style="background: #dcfce7; color: #15803d;">
                                    <?= $kRank + 1 ?>
                                </div>
                                <div class="berita-trend__body">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-primary-subtle text-primary fw-bold font-monospace" style="font-size: 10.5px;"><?= esc($kItem['code']) ?></span>
                                        <small class="text-muted fw-semibold" style="font-size: 11px;"><?= esc($kItem['start_at'] ?? 'Segera') ?></small>
                                    </div>
                                    <h5 class="berita-trend__title">
                                        <a href="<?= route_to('public.learning.class.detail', $kItem['code']) ?>">
                                            <?= esc($kItem['name']) ?>
                                        </a>
                                    </h5>
                                    <div class="berita-trend__meta">
                                        <span class="text-truncate" style="max-width: 140px;"><?= esc($kItem['course_title'] ?? 'Course PBJ') ?></span>
                                        <span>&middot;</span>
                                        <a href="<?= route_to('public.learning.class.detail', $kItem['code']) ?>" class="fw-bold text-primary text-decoration-none">Detail &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-sm btn-outline-success w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Kelas (<?= (int) $stats['total_classes'] ?>) &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 7: Instruktur Terverifikasi -->
                <?php if (! empty($featuredTrainers)): ?>
                    <div class="berita-widget">
                        <h4 class="berita-widget__title">
                            <i class="fas fa-user-tie" style="background: #f3e8ff; color: #9333ea;"></i>
                            <span>Instruktur Terverifikasi</span>
                        </h4>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach (array_slice($featuredTrainers, 0, 3) as $tIndex => $tItem):
                                $avatarIdx = ($tIndex % 5) + 1;
                                $avatarUrl = base_url("assets/main/images/resources/course-list-client-img-{$avatarIdx}.jpg");
                                $trainerSlug = $tItem['username'] ?: (string) $tItem['id'];
                                $profLabel = match ($tItem['profile_type'] ?? '') {
                                    'asn'      => 'Pejabat Fungsional PBJ',
                                    'provider' => 'Praktisi Penyedia PBJ',
                                    default    => 'Fasilitator Terverifikasi',
                                };
                            ?>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $avatarUrl ?>" alt="<?= esc($tItem['name']) ?>" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover; flex-shrink: 0;" loading="lazy">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="fw-bold mb-0 text-truncate" style="font-size: 13.5px;">
                                            <a href="<?= route_to('public.learning.trainer.detail', $trainerSlug) ?>" class="text-dark text-decoration-none hover-primary">
                                                <?= esc($tItem['name'] ?: $tItem['username']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted d-block text-truncate" style="font-size: 12px;"><?= esc($profLabel) ?></small>
                                    </div>
                                    <a href="<?= route_to('public.learning.trainer.detail', $trainerSlug) ?>" class="btn btn-sm btn-light border radius-6 p-1 text-primary" title="Lihat Profil">
                                        <i class="fas fa-chevron-right" style="font-size: 11px;"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= site_url('arena-belajar/pengajar') ?>" class="btn btn-sm btn-outline-secondary w-100 radius-8 fw-semibold mt-3" style="font-size: 12.5px;">
                            Lihat Semua Instruktur (<?= (int) $stats['total_trainers'] ?>)
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sidebar Widget 8: Keunggulan BegawiPengadaan (Dipindahkan setelah Instruktur) -->
                <div class="berita-widget">
                    <h4 class="berita-widget__title">
                        <i class="fas fa-award" style="background: #fef3c7; color: #d97706;"></i>
                        <span>Keunggulan BegawiPengadaan</span>
                    </h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary text-xs fw-bold px-2 py-1">Pilar Edukasi</span>
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">Dari Regulasi Menuju Praktik Nyata Pengadaan</span>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-primary mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Kurikulum Terpadu UKPBJ Lampung</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Dirancang berjenjang oleh Biro PBJ Pemprov Lampung sesuai kebutuhan pengadaan daerah.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Fasilitator Terverifikasi</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Dibimbing langsung widyaiswara dan pejabat fungsional PBJ bersertifikasi.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-info mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Fleksibel &amp; Mandiri</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Akses modul mandiri, studi kasus kontekstual, dan forum tanya jawab 24/7.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 p-2 radius-8 bg-light border">
                            <i class="fas fa-check-circle text-warning mt-1" style="font-size: 13px;"></i>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 12.5px;">Sertifikat Digital QR</strong>
                                <small class="text-muted" style="font-size: 11.5px; line-height: 1.4;">Tervalidasi resmi dan terintegrasi di sistem verifikasi BegawiPengadaan.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Widget 9: Bantuan & Informasi (Dipindahkan setelah Instruktur) -->
                <div class="berita-widget p-4 text-white" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%) !important; border-radius: 16px;">
                    <span class="badge bg-warning text-dark text-xs fw-bold px-2 py-1 mb-2">Biro PBJ Setda Lampung</span>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 16px;">Bantuan &amp; Informasi</h5>
                    <p class="text-white-80 mb-3" style="font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                        Seluruh kurikulum pembelajaran dan kelas batch diselenggarakan resmi di bawah koordinasi Biro Pengadaan Barang dan Jasa Provinsi Lampung.
                    </p>
                    <div class="text-warning fw-semibold d-flex align-items-center gap-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-phone-alt"></i>
                        <span>Helpdesk PBJ: +62 851-1777-9783</span>
                    </div>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="btn btn-sm btn-outline-light w-100 radius-8 fw-semibold" style="font-size: 12.5px;">
                        <i class="fas fa-qrcode me-1"></i> Verifikasi Sertifikat Kelulusan
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- =========================================================================
     SECTION: CLOSING CALL TO ACTION BANNER (COMPACT)
     ========================================================================= -->
<section class="arena-cta-section py-4" style="background: linear-gradient(135deg, #031527 0%, #082f5c 50%, #0c5aa6 100%); color: #ffffff;">
    <div class="container text-center py-2">
        <span class="badge bg-warning text-dark px-3 py-1 radius-20 text-uppercase fw-bold mb-2" style="letter-spacing: 0.5px; font-size: 12.5px;">
            <i class="fas fa-rocket me-1"></i> Mulai Pembelajaran Hari Ini
        </span>
        <h2 class="text-white mb-2" style="font-size: 26px; font-weight: 800; line-height: 1.3;">
            Siap Memperdalam Pengetahuan &amp; Wawasan Pengadaan Anda?
        </h2>
        <p class="mx-auto mb-3 text-white-80" style="max-width: 620px; font-size: 14.5px; line-height: 1.65; color: rgba(255,255,255,0.85);">
            Bergabunglah bersama ribuan aparatur sipil negara dan pelaku usaha pengadaan di Provinsi Lampung untuk mewujudkan pengadaan yang transparan, akuntabel, dan profesional.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php if (! $isLoggedIn): ?>
                <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 radius-8 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="fas fa-user-plus"></i> Daftar Akun Gratis
                </a>
                <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-light radius-8 px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13.5px;">
                    <i class="fas fa-book-open"></i> Eksplorasi Katalog Course
                </a>
            <?php else: ?>
                <a href="<?= route_to('workspace.participant.home') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2 radius-8 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="fas fa-laptop-code"></i> Masuk ke Workspace Belajar
                </a>
                <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-outline-light radius-8 px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 13.5px;">
                    <i class="fas fa-calendar-alt"></i> Lihat Jadwal Kelas Aktif
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     INTERACTIVE JAVASCRIPT FOR ARENA BELAJAR
     ========================================================================= -->
<script>
    function applyQuickSearch(term) {
        const searchInput = document.getElementById('arenaHeroSearch');
        if (searchInput) {
            searchInput.value = term;
            searchInput.closest('form').submit();
        }
    }

    function filterCourseCards(level, btnElement) {
        const buttons = document.querySelectorAll('.course-filter-btn');
        buttons.forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-light', 'border');
        });

        if (btnElement) {
            btnElement.classList.add('active', 'btn-primary');
            btnElement.classList.remove('btn-light', 'border');
        }

        const cards = document.querySelectorAll('.course-card-wrapper');
        cards.forEach(card => {
            const cardLevel = card.getAttribute('data-level');
            if (level === 'all' || cardLevel === level) {
                card.style.display = 'block';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.25s ease';
                    card.style.opacity = '1';
                }, 30);
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var carouselEl = document.getElementById('arenaHeadlineCarousel');
        if (!carouselEl) return;

        // Initialize or get Bootstrap carousel instance safely across all Bootstrap 5 versions (including 5.0.0-beta3)
        var myCarousel = null;
        if (window.bootstrap && bootstrap.Carousel) {
            if (typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                myCarousel = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
                    interval: 6000,
                    ride: 'carousel',
                    wrap: true,
                    pause: 'hover'
                });
            } else if (typeof bootstrap.Carousel.getInstance === 'function') {
                myCarousel = bootstrap.Carousel.getInstance(carouselEl);
                if (!myCarousel) {
                    try {
                        myCarousel = new bootstrap.Carousel(carouselEl, {
                            interval: 6000,
                            ride: 'carousel',
                            wrap: true,
                            pause: 'hover'
                        });
                    } catch (err) {}
                }
            } else {
                try {
                    myCarousel = new bootstrap.Carousel(carouselEl, {
                        interval: 6000,
                        ride: 'carousel',
                        wrap: true,
                        pause: 'hover'
                    });
                } catch (err) {}
            }
        }

        var indicatorButtons = carouselEl.querySelectorAll('.evt-headline__indicators button, .carousel-indicators button');

        function syncIndicatorDots(targetIdx) {
            if (!indicatorButtons || indicatorButtons.length === 0) return;
            indicatorButtons.forEach(function(btn, idx) {
                if (idx === targetIdx) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-current', 'true');
                } else {
                    btn.classList.remove('active');
                    btn.removeAttribute('aria-current');
                }
            });
        }

        // Listen for slide start and slide complete events to guarantee 100% synchronization
        carouselEl.addEventListener('slide.bs.carousel', function(e) {
            if (typeof e.to !== 'undefined') {
                syncIndicatorDots(e.to);
            }
        });

        carouselEl.addEventListener('slid.bs.carousel', function(e) {
            if (typeof e.to !== 'undefined') {
                syncIndicatorDots(e.to);
            }
        });

        // Click handler for dots to ensure explicit navigation
        indicatorButtons.forEach(function(btn, idx) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (myCarousel && typeof myCarousel.to === 'function') {
                    myCarousel.to(idx);
                } else if (window.jQuery && typeof jQuery(carouselEl).carousel === 'function') {
                    jQuery(carouselEl).carousel(idx);
                }
                syncIndicatorDots(idx);
            });
        });
    });
</script>
<?= $this->endSection() ?>