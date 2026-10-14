<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?php
$stats = $stats ?? [
    'total_programs' => 0,
    'total_courses'  => 0,
    'total_classes'  => 0,
    'total_trainers' => 0,
];
$featuredPrograms = $featuredPrograms ?? [];
$popularCourses   = $popularCourses ?? [];
$activeClasses    = $activeClasses ?? [];
$featuredTrainers = $featuredTrainers ?? [];
$isLoggedIn       = session()->get('isLoggedIn') === true;
?>

<!-- =========================================================================
     PAGE HEADER / HERO ARENA BELAJAR
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-graduation-cap me-1"></i> Arena Belajar BegawiPengadaan Lampung
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                Pusat Pembelajaran &amp; Kompetensi Pengadaan
            </h1>
            <p class="mx-auto mb-4" style="max-width: 680px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                Tingkatkan kompetensi Pengadaan Barang/Jasa Pemerintah melalui kurikulum terstruktur, materi mandiri komprehensif, dan kelas interaktif bersama fasilitator tersertifikasi.
            </p>

            <!-- Live Search Bar -->
            <div class="arena-search-bar mb-4">
                <form action="<?= site_url('arena-belajar/course') ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
                    <input type="text" name="q" placeholder="Cari course, topik regulasi, modul kontrak, atau kelas PBJ..." autocomplete="off">
                    <button type="submit">
                        <i class="fas fa-search me-1"></i>
                        <span>Cari</span>
                    </button>
                </form>
            </div>

            <!-- Sub-Navigation Pills -->
            <nav class="arena-nav-pills" aria-label="Kategori Arena Belajar">
                <a href="<?= site_url('arena-belajar') ?>" class="arena-nav-pill active">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Eksplorasi</span>
                </a>
                <a href="<?= site_url('arena-belajar/program') ?>" class="arena-nav-pill">
                    <i class="fas fa-layer-group"></i>
                    <span>Program Pembelajaran</span>
                    <span class="badge"><?= (int) $stats['total_programs'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/course') ?>" class="arena-nav-pill">
                    <i class="fas fa-book-open"></i>
                    <span>Course PBJ</span>
                    <span class="badge"><?= (int) $stats['total_courses'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/kelas') ?>" class="arena-nav-pill">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Kelas &amp; Batch</span>
                    <span class="badge"><?= (int) $stats['total_classes'] ?></span>
                </a>
                <a href="<?= site_url('arena-belajar/pengajar') ?>" class="arena-nav-pill">
                    <i class="fas fa-user-tie"></i>
                    <span>Instruktur &amp; Pengajar</span>
                    <span class="badge"><?= (int) $stats['total_trainers'] ?></span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- =========================================================================
     SHOWCASE 1: PROGRAM PEMBELAJARAN UNGGULAN
     ========================================================================= -->
<section class="py-5" style="background: #fafbfc;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-award me-1"></i> Kurikulum Komprehensif Berjenjang
                </span>
                <h3 class="arena-section-head__title">Program Pembelajaran Unggulan</h3>
                <p class="arena-section-head__desc mb-0">
                    Jalur pelatihan terpadu yang dirancang dari pemahaman dasar, regulasi, hingga keahlian manajerial PBJ.
                </p>
            </div>
            <a href="<?= site_url('arena-belajar/program') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                <span>Lihat Semua Program (<?= (int) $stats['total_programs'] ?>)</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if ($featuredPrograms === []): ?>
            <div class="alert alert-light text-center border p-4 radius-12">Belum ada program unggulan yang dipublikasikan.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($featuredPrograms as $index => $prog): ?>
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
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <article class="arena-card arena-program-card h-100">
                            <div class="arena-program-card__cover position-relative" style="height: 155px;">
                                <img src="<?= $coverImg ?>" alt="<?= esc($prog['title']) ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.85); transition: transform 0.4s ease;" loading="lazy">
                                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8,47,92,0.3) 0%, rgba(4,24,48,0.85) 100%);"></div>
                                <div class="arena-card__badge-row">
                                    <span class="arena-pill-badge arena-pill-badge--accent">
                                        <i class="fas fa-star me-1"></i> Unggulan
                                    </span>
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
                                    <a href="<?= route_to('public.learning.program.detail', $prog['slug']) ?>" class="hover-primary">
                                        <?= esc($prog['title']) ?>
                                    </a>
                                </h4>
                                <p class="arena-card__text mb-2">
                                    <?= esc(mb_strimwidth($prog['summary'] ?: ($prog['target_audience'] ?: 'Program kurikulum kompetensi pengadaan barang dan jasa.'), 0, 95, '…')) ?>
                                </p>
                                <?php if (! empty($prog['target_audience'])): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-muted border text-truncate d-inline-block" style="max-width: 100%; font-size: 11px;" title="<?= esc($prog['target_audience']) ?>">
                                            <i class="fas fa-users text-primary me-1"></i> <?= esc(mb_strimwidth($prog['target_audience'], 0, 32, '…')) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="arena-card__meta">
                                    <span><i class="fas fa-book-open"></i> <?= (int) ($prog['course_count'] ?? 0) ?> Course</span>
                                    <span><i class="fas fa-shield-alt"></i> LKPP Ready</span>
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
</section>

<!-- =========================================================================
     SHOWCASE 2: COURSE PBJ TERPOPULER
     ========================================================================= -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-fire me-1"></i> Pembelajaran Mandiri &amp; Fleksibel
                </span>
                <h3 class="arena-section-head__title">Course PBJ Terpopuler &amp; Pilihan</h3>
                <p class="arena-section-head__desc mb-0">
                    Pelajari regulasi, perencanaan, pemilihan penyedia, penyusunan kontrak, dan evaluasi PBJ sesuai kebutuhan.
                </p>
            </div>
            <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                <span>Jelajahi Semua Course (<?= (int) $stats['total_courses'] ?>)</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if ($popularCourses === []): ?>
            <div class="alert alert-light text-center border p-4 radius-12">Belum ada course yang dipublikasikan.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($popularCourses as $index => $course): ?>
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
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <article class="arena-card">
                            <div class="arena-card__thumb">
                                <img src="<?= $coverImg ?>" alt="<?= esc($course['title']) ?>" loading="lazy" style="object-fit: cover; width: 100%; height: 100%;">
                                <div class="arena-card__badge-row">
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
                                    <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>">
                                        <?= esc($course['title']) ?>
                                    </a>
                                </h4>
                                <p class="arena-card__text mb-2">
                                    <?= esc(mb_strimwidth($course['summary'] ?: ($course['target_audience'] ?: 'Pelajari topik regulasi dan penerapan praktis dalam pengadaan barang dan jasa pemerintah.'), 0, 110, '…')) ?>
                                </p>
                                <?php if (! empty($course['target_audience'])): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-muted border text-truncate d-inline-block" style="max-width: 100%; font-size: 11px;" title="<?= esc($course['target_audience']) ?>">
                                            <i class="fas fa-users text-primary me-1"></i> <?= esc(mb_strimwidth($course['target_audience'], 0, 35, '…')) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="arena-card__meta">
                                    <span>
                                        <i class="far fa-clock"></i>
                                        <?= ($course['duration_minutes'] ?? null) !== null ? esc((string) $course['duration_minutes']) . ' menit' : 'Belajar Mandiri' ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-signal"></i> <?= esc($levelBadge['label']) ?>
                                    </span>
                                </div>
                                <div class="arena-card__actions">
                                    <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>" class="arena-btn-primary">
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
</section>

<!-- =========================================================================
     SHOWCASE 3: KELAS & BATCH AKTIF SIAP DAFTAR
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div class="arena-section-head mb-0">
                <span class="arena-section-head__tag">
                    <i class="fas fa-calendar-check me-1"></i> Pelatihan Interaktif &amp; Pendampingan
                </span>
                <h3 class="arena-section-head__title">Kelas &amp; Batch Siap Daftar</h3>
                <p class="arena-section-head__desc mb-0">
                    Ikuti kelas terjadwal dengan pendampingan fasilitator pengadaan, kuis evaluasi, penugasan studi kasus, dan penerbitan sertifikat resmi.
                </p>
            </div>
            <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                <span>Lihat Semua Jadwal Kelas (<?= (int) $stats['total_classes'] ?>)</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if ($activeClasses === []): ?>
            <div class="alert alert-light text-center border p-4 radius-12">Belum ada kelas aktif yang terjadwal.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($activeClasses as $index => $class): ?>
                    <?php
                    $imgIndex = (($index + 2) % 6) + 1;
                    $defaultImg = "assets/main/images/resources/course-list-img-{$imgIndex}.jpg";
                    $coverImg = ! empty($class['course_cover_url'])
                        ? (str_starts_with($class['course_cover_url'], 'http') ? $class['course_cover_url'] : base_url($class['course_cover_url']))
                        : base_url($defaultImg);

                    $method = strtolower((string) ($class['learning_method'] ?? 'online'));
                    $methodBadge = match ($method) {
                        'offline' => ['label' => 'Tatap Muka', 'badge' => 'bg-info text-white', 'icon' => 'fa-building'],
                        'hybrid'  => ['label' => 'Hybrid', 'badge' => 'bg-purple text-white', 'icon' => 'fa-network-wired'],
                        default   => ['label' => 'Online LMS', 'badge' => 'bg-primary text-white', 'icon' => 'fa-laptop'],
                    };
                    ?>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <article class="arena-card flex-md-row" style="min-height: 220px;">
                            <div class="arena-card__thumb flex-shrink-0" style="width: 220px; height: auto; min-height: 200px;">
                                <img src="<?= $coverImg ?>" alt="<?= esc($class['name']) ?>" loading="lazy" style="object-fit: cover; width: 100%; height: 100%;">
                                <div class="arena-card__badge-row">
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
                            <div class="arena-card__body">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="arena-card__category mb-0">Course: <?= esc($class['course_title'] ?? 'PBJ') ?></span>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge <?= $methodBadge['badge'] ?> px-2 py-1 radius-4 small" style="font-size: 10.5px;">
                                            <i class="fas <?= $methodBadge['icon'] ?> me-1"></i> <?= esc($methodBadge['label']) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border px-2 py-1 radius-4 font-monospace" style="font-size: 11px;">
                                            <?= esc($class['code']) ?>
                                        </span>
                                    </div>
                                </div>
                                <h4 class="arena-card__title" style="font-size: 17px;">
                                    <a href="<?= route_to('public.learning.class.detail', $class['code']) ?>">
                                        <?= esc($class['name']) ?>
                                    </a>
                                </h4>

                                <?php if (! empty($class['description'])): ?>
                                    <p class="small text-muted mb-2 text-truncate" style="font-size: 12.5px;">
                                        <?= esc(mb_strimwidth($class['description'], 0, 80, '…')) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (! empty($class['trainers'])): ?>
                                    <div class="d-flex align-items-center gap-1 mb-2 text-truncate" style="font-size: 11.5px;">
                                        <i class="fas fa-user-tie text-primary me-1"></i>
                                        <span class="text-muted">Fasilitator:</span>
                                        <?php foreach (array_slice($class['trainers'], 0, 2) as $t): ?>
                                            <span class="badge bg-light text-dark border"><?= esc($t['name'] ?? $t['username']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="arena-card__meta py-2 my-2" style="font-size: 12px; border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0;">
                                    <span>
                                        <i class="far fa-calendar-alt"></i>
                                        <?= esc(($class['start_at'] ?? '-') . ' s/d ' . ($class['end_at'] ?? '-')) ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-users"></i>
                                        Kapasitas: <?= esc((string) ($class['capacity'] ?? '-')) ?>
                                    </span>
                                    <?php if (! empty($class['location_or_link']) && $method !== 'online'): ?>
                                        <span class="text-truncate" style="max-width: 140px;" title="<?= esc($class['location_or_link']) ?>">
                                            <i class="fas fa-map-marker-alt text-danger"></i> <?= esc($class['location_or_link']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="arena-card__actions mt-auto">
                                    <a href="<?= route_to('public.learning.class.detail', $class['code']) ?>" class="arena-btn-primary">
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
</section>

<!-- =========================================================================
     SHOWCASE 4: INSTRUKTUR & FASILITATOR PBJ
     ========================================================================= -->
<?php if ($featuredTrainers !== []): ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
                <div class="arena-section-head mb-0">
                    <span class="arena-section-head__tag">
                        <i class="fas fa-chalkboard-teacher me-1"></i> Praktisi &amp; Widyaiswara PBJ
                    </span>
                    <h3 class="arena-section-head__title">Instruktur &amp; Fasilitator Terakreditasi</h3>
                    <p class="arena-section-head__desc mb-0">
                        Didampingi oleh praktisi dan pejabat fungsional pengadaan barang/jasa berpengalaman dari Pemerintah Provinsi Lampung.
                    </p>
                </div>
                <a href="<?= site_url('arena-belajar/pengajar') ?>" class="btn btn-outline-primary radius-8 px-4 py-2 mt-3 mt-md-0 d-inline-flex align-items-center gap-2 fw-semibold">
                    <span>Direktori Instruktur</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($featuredTrainers as $idx => $tr): ?>
                    <?php
                    $avatarIdx = ($idx % 5) + 1;
                    $avatarUrl = base_url("assets/main/images/resources/course-list-client-img-{$avatarIdx}.jpg");
                    $trainerSlug = $tr['username'] ?: (string) $tr['id'];
                    ?>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="card h-100 border text-center p-4 radius-16 shadow-sm transition-all" style="background: #ffffff;">
                            <div class="mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid #eff6ff;">
                                <img src="<?= $avatarUrl ?>" alt="<?= esc($tr['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1" style="font-size: 16px;">
                                <a href="<?= route_to('public.learning.trainer.detail', $trainerSlug) ?>" class="text-dark">
                                    <?= esc($tr['name'] ?: $tr['username']) ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-2"><?= esc($tr['profile_type'] ?: 'Fasilitator PBJ Lampung') ?></p>
                            <div class="mt-auto pt-2">
                                <span class="badge bg-light text-primary border px-3 py-1 radius-20" style="font-size: 11px;">
                                    <i class="fas fa-chalkboard me-1"></i> <?= (int) ($tr['total_classes'] ?? 1) ?> Kelas Aktif
                                </span>
                                <div class="mt-3">
                                    <a href="<?= route_to('public.learning.trainer.detail', $trainerSlug) ?>" class="btn btn-sm btn-outline-secondary radius-6 px-3">
                                        Lihat Profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================================
     SECTION 5: PILAR KEUNGGULAN ARENA BELAJAR
     ========================================================================= -->
<section class="why-choose-one py-5" style="background: #082f5c; color: #ffffff;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-xl-6">
                <div class="why-choose-one__left">
                    <span class="arena-section-head__tag text-white mb-2" style="background: rgba(255,255,255,0.18);">
                        <i class="fas fa-check-circle me-1"></i> Keunggulan BegawiPengadaan
                    </span>
                    <h2 class="text-white mb-3" style="font-size: 32px; font-weight: 800; line-height: 1.3;">
                        Dari Regulasi Menuju Praktik Nyata Pengadaan
                    </h2>
                    <p class="text-white-80 mb-4" style="font-size: 15px; line-height: 1.7; color: rgba(255,255,255,0.85);">
                        Arena Belajar membantu peserta memahami regulasi, prosedur, analisis pasar, hingga mitigasi risiko audit secara kontekstual, terarah, dan dapat diterapkan langsung dalam tugas kepengadaan sehari-hari.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <?php if (! $isLoggedIn): ?>
                            <a href="<?= route_to('dashboard.auth.register') ?>" class="thm-btn">
                                <i class="fas fa-user-plus me-1"></i> Mulai Belajar Sekarang
                            </a>
                            <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-light radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
                                <i class="fas fa-book-open"></i> Eksplorasi Materi
                            </a>
                        <?php else: ?>
                            <a href="<?= route_to('workspace.participant.home') ?>" class="thm-btn">
                                <i class="fas fa-laptop-code me-1"></i> Buka Workspace Belajar Saya
                            </a>
                            <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-light radius-8 px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
                                <i class="fas fa-search"></i> Jelajahi Course Lain
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="why-choose-one__right">
                    <ul class="why-choose-one__list list-unstyled m-0">
                        <li class="d-flex gap-3 mb-4 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                            <span class="icon-check text-warning" style="font-size: 24px; flex-shrink: 0;"></span>
                            <div>
                                <h4 class="text-white fw-bold mb-1" style="font-size: 17px;">Sesuai Standar LKPP &amp; Perpres Terkini</h4>
                                <p class="text-white-80 mb-0" style="font-size: 13.5px; color: rgba(255,255,255,0.75);">Kurikulum selalu dimutakhirkan mengikuti ketentuan perundang-undangan dan petunjuk teknis pengadaan terbaru.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 mb-4 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                            <span class="icon-check text-warning" style="font-size: 24px; flex-shrink: 0;"></span>
                            <div>
                                <h4 class="text-white fw-bold mb-1" style="font-size: 17px;">Studi Kasus Nyata Pemerintah Daerah</h4>
                                <p class="text-white-80 mb-0" style="font-size: 13.5px; color: rgba(255,255,255,0.75);">Latihan, kuis, dan tugas aplikatif yang mencerminkan kondisi riil di lingkungan OPD dan unit kerja.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 p-3 radius-12" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                            <span class="icon-check text-warning" style="font-size: 24px; flex-shrink: 0;"></span>
                            <div>
                                <h4 class="text-white fw-bold mb-1" style="font-size: 17px;">Sertifikat Digital &amp; Verifikasi Resmi</h4>
                                <p class="text-white-80 mb-0" style="font-size: 13.5px; color: rgba(255,255,255,0.75);">Kelulusan disertai penerbitan sertifikat digital yang dapat diverifikasi keasliannya melalui sistem publik.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>