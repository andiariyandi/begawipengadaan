<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-news.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-news.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-news.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-events.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-events.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-events.css') : '1.0' ?>" />
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-learning.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-learning.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-learning.css') : '1.0' ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$item = $item ?? [];
$breadcrumb = $breadcrumb ?? [];

$enrollStatus = $item['enrollment_status'] ?? null;
$isLoggedIn   = session()->get('isLoggedIn') === true;
$success      = session()->getFlashdata('success');
$err          = session()->getFlashdata('error');
?>

<?php if ($catalogType !== 'programs' && $catalogType !== 'courses' && $catalogType !== 'classes' && $catalogType !== 'trainers'): ?>
    <?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

    <?php if ($breadcrumb !== []): ?>
        <nav aria-label="breadcrumb" class="container pt-3">
            <ol class="breadcrumb mb-0 py-2">
                <?php foreach ($breadcrumb as $i => $crumb): ?>
                    <?php if ($i > 0): ?><li class="breadcrumb-separator mx-2 text-muted">/</li><?php endif; ?>
                    <li class="breadcrumb-item <?= empty($crumb['url']) ? 'active fw-bold' : '' ?>">
                        <?php if (! empty($crumb['url'])): ?>
                            <a href="<?= esc($crumb['url']) ?>" class="text-decoration-none"><?= esc($crumb['label']) ?></a>
                        <?php else: ?>
                            <?= esc($crumb['label']) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php if ($catalogType === 'courses'): ?>
    <?php
    $levelVal = strtolower((string) ($item['level'] ?? 'basic'));
    $levelConfig = [
        'basic' => ['label' => 'Tingkat Dasar', 'badge' => 'bg-info text-white', 'icon' => 'fa-shield-alt'],
        'intermediate' => ['label' => 'Tingkat Menengah', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-chart-line'],
        'advanced' => ['label' => 'Tingkat Lanjutan', 'badge' => 'bg-danger text-white', 'icon' => 'fa-award'],
    ][$levelVal] ?? ['label' => ucfirst($levelVal), 'badge' => 'bg-primary text-white', 'icon' => 'fa-book'];

    $durationFormatted = ($item['duration_minutes'] ?? null) !== null
        ? esc((string) $item['duration_minutes']) . ' Menit'
        : 'Mandiri / Fleksibel';
    ?>

    <!-- =========================================================================
     COURSE DETAIL — Clean Magazine Benchmark
     ========================================================================= -->
    <section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
        <div class="container">
            <!-- Compact Breadcrumb Navigation -->
            <div class="arena-breadcrumb evt-breadcrumb mb-3">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar/course') ?>">Course PBJ</a>
                <span class="sep">/</span>
                <span class="current text-truncate"><?= esc($item['title'] ?? $itemTitle) ?></span>
            </div>

            <!-- Compact Course Detail Hero Banner (arena-hero class preserved for test) -->
            <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11.5px;">
                                <i class="fas fa-book-open me-1"></i> Course PBJ
                            </span>
                            <span class="badge <?= $levelConfig['badge'] ?> px-2 py-1 radius-8 small" style="font-size: 11.5px;">
                                <i class="fas <?= $levelConfig['icon'] ?> me-1"></i> <?= esc($levelConfig['label']) ?>
                            </span>
                            <?php if (! empty($item['program_slug'])): ?>
                                <a href="<?= route_to('public.learning.program.detail', $item['program_slug']) ?>" class="badge bg-white text-primary fw-bold px-2 py-1 radius-8 shadow-sm text-decoration-none" style="font-size: 11.5px;">
                                    <i class="fas fa-layer-group me-1"></i> Program: <?= esc($item['program_title'] ?? 'Kurikulum Terpadu') ?>
                                </a>
                            <?php endif; ?>
                            <span class="badge px-2 py-1 radius-8 text-white" style="font-size: 11.5px; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32);">
                                <i class="far fa-clock me-1"></i> <?= $durationFormatted ?>
                            </span>
                        </div>

                        <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; line-height: 1.3;">
                            <?= esc($item['title'] ?? $itemTitle) ?>
                        </h1>

                        <p class="mb-3 text-white-90" style="font-size: 14px; line-height: 1.55; max-width: 720px; color: rgba(255,255,255,0.9);">
                            <?= esc($item['summary'] ?? 'Pelajari modul terstruktur pengadaan barang dan jasa pemerintah Provinsi Lampung.') ?>
                        </p>

                        <!-- Hero Quick Action Buttons -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="#daftar-kelas" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-1 radius-8 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Jadwal &amp; Batch Kelas (<?= (int) ($totalClasses ?? count($classes)) ?>)</span>
                            </a>
                            <a href="#silabus-course" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-list-ul"></i>
                                <span>Silabus Materi</span>
                            </a>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'course',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['title'] ?? $itemTitle,
                                'size'  => 'sm',
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <!-- Glass Quick Stat Card in Hero -->
                        <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                            <span class="text-white-50 small fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px; font-size: 11px;">Ringkasan Informasi:</span>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-signal me-1 text-warning"></i> Level Kesulitan</span>
                                <span class="fw-bold"><?= esc($levelConfig['label']) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="far fa-clock me-1 text-info"></i> Durasi Belajar</span>
                                <span class="fw-bold"><?= $durationFormatted ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-chalkboard-teacher me-1 text-success"></i> Batch Kelas</span>
                                <span class="fw-bold fs-6"><?= (int) ($totalClasses ?? count($classes)) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-white small">
                                <span><i class="fas fa-certificate me-1 text-warning"></i> Sertifikat</span>
                                <span class="badge bg-success text-white" style="font-size: 10px;">Resmi LKPP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Main Column (8 cols) -->
                <div class="col-xl-8 col-lg-7">
                    <?php
                    $courseCover = ! empty($item['cover_url'])
                        ? (str_starts_with($item['cover_url'], 'http') ? $item['cover_url'] : base_url($item['cover_url']))
                        : base_url('assets/main/images/resources/course-details-img-1.jpg');
                    ?>
                    <!-- 1. Cover Card -->
                    <div class="card border-0 shadow-sm radius-16 overflow-hidden mb-4 bg-white">
                        <div class="position-relative" style="max-height: 380px; overflow: hidden;">
                            <img src="<?= esc($courseCover) ?>" alt="<?= esc($itemTitle) ?>" class="w-100" style="object-fit: cover; max-height: 380px;">
                            <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(to top, rgba(8,47,92,0.85), transparent);">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white">
                                    <span class="small fw-semibold"><i class="fas fa-shield-alt text-warning me-1"></i> Standar Akreditasi LKPP &amp; Pemprov Lampung</span>
                                    <span class="small opacity-75"><i class="fas fa-check-circle text-success me-1"></i> Kurikulum Resmi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Ringkasan & Deskripsi Lengkap Course -->
                    <div id="ringkasan-course" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary-subtle text-primary radius-20 px-3 py-1 small fw-bold text-uppercase">
                                <i class="fas fa-info-circle me-1"></i> Deskripsi Pembelajaran
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-3" style="font-size: 22px;">Tentang Course Ini</h3>

                        <?php if (! empty($item['summary'])): ?>
                            <p class="lead text-dark mb-3" style="font-size: 16px; line-height: 1.6; font-weight: 500;">
                                <?= esc($item['summary']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="course-description mb-4" style="line-height: 1.8; color: #475569; font-size: 14.5px;">
                            <?= nl2br(esc($item['description'] ?? 'Course ini dirancang untuk membekali aparatur pengadaan dan praktisi publik dengan wawasan teknis serta kemampuan terapan sesuai prinsip efisien, efektif, terbuka, transparan, adil, dan akuntabel.')) ?>
                        </div>

                        <!-- Target Capaian Kompetensi PBJ -->
                        <div class="pt-3 border-top">
                            <h5 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                                <i class="fas fa-award text-primary me-2"></i> Capaian Pembelajaran &amp; Target Kompetensi
                            </h5>
                            <?php
                            $customObjectives = ! empty($item['learning_objectives'])
                                ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $item['learning_objectives'])))
                                : [];
                            ?>
                            <?php if (! empty($customObjectives)): ?>
                                <div class="row g-2">
                                    <?php foreach ($customObjectives as $obj): ?>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light radius-10 border h-100">
                                                <div class="d-flex align-items-start gap-2">
                                                    <i class="fas fa-check-circle text-success mt-1"></i>
                                                    <div>
                                                        <span class="text-dark small fw-semibold"><?= esc($obj) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light radius-10 border h-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="fas fa-check-circle text-success mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Regulasi &amp; Etika PBJ</strong>
                                                    <span class="text-muted small" style="font-size: 13.5px; line-height: 1.5;">Memahami prinsip pengadaan, etika, dan mitigasi benturan kepentingan sesuai Perpres.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light radius-10 border h-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="fas fa-check-circle text-success mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Perencanaan &amp; HPS</strong>
                                                    <span class="text-muted small" style="font-size: 13.5px; line-height: 1.5;">Penyusunan spesifikasi teknis, KAK, serta perhitungan harga perkiraan sendiri akuntabel.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-12 p-3 bg-light radius-10 border h-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="fas fa-check-circle text-success mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Pemilihan &amp; E-Katalog</strong>
                                                    <span class="text-muted small" style="font-size: 13.5px; line-height: 1.5;">Penguasaan alur tender/seleksi, negosiasi teknis, dan transaksi belanja katalog elektronik.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light radius-10 border h-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="fas fa-check-circle text-success mt-1"></i>
                                                <div>
                                                    <strong class="d-block text-dark small">Manajemen Kontrak &amp; BAST</strong>
                                                    <span class="text-muted small" style="font-size: 13.5px; line-height: 1.5;">Pengendalian mutu pekerjaan, mitigasi risiko sengketa, dan serah terima hasil pekerjaan.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (! empty($item['target_audience']) || ! empty($item['prerequisites'])): ?>
                                <div class="row g-3 mt-2 pt-3 border-top">
                                    <?php if (! empty($item['target_audience'])): ?>
                                        <div class="col-md-<?= ! empty($item['prerequisites']) ? '6' : '12' ?>">
                                            <div class="p-3 radius-10 border bg-light h-100">
                                                <h6 class="fw-bold text-dark mb-2" style="font-size: 13.5px;">
                                                    <i class="fas fa-user-tag text-primary me-2"></i> Sasaran &amp; Target Peserta
                                                </h6>
                                                <p class="text-muted small mb-0" style="line-height: 1.6;">
                                                    <?= nl2br(esc($item['target_audience'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (! empty($item['prerequisites'])): ?>
                                        <div class="col-md-<?= ! empty($item['target_audience']) ? '6' : '12' ?>">
                                            <div class="p-3 radius-10 border bg-light h-100">
                                                <h6 class="fw-bold text-dark mb-2" style="font-size: 13.5px;">
                                                    <i class="fas fa-clipboard-check text-warning me-2"></i> Persyaratan / Prasyarat
                                                </h6>
                                                <p class="text-muted small mb-0" style="line-height: 1.6;">
                                                    <?= nl2br(esc($item['prerequisites'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. Silabus & Rencana Pembelajaran -->
                    <div id="silabus-course" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge bg-primary-subtle text-primary radius-20 px-3 py-1 small fw-bold text-uppercase">
                                    <i class="fas fa-list-ul me-1"></i> Rencana Pembelajaran
                                </span>
                                <h3 class="fw-bold text-dark mt-2 mb-1" style="font-size: 22px;">Silabus &amp; Modul Course</h3>
                                <p class="text-muted small mb-0">Rangkaian modul digital interaktif yang dirancang untuk memperkuat penguasaan substansi PBJ.</p>
                            </div>
                        </div>

                        <?php if (! empty($modules)): ?>
                            <div class="d-flex flex-column gap-3 mt-2">
                                <?php foreach ($modules as $mIdx => $mod): ?>
                                    <div class="p-3 radius-12 border bg-light">
                                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                            <div>
                                                <span class="badge bg-primary text-white radius-6 px-2 py-1 small mb-1">
                                                    Modul <?= $mIdx + 1 ?>
                                                </span>
                                                <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">
                                                    <?= esc($mod['title']) ?>
                                                </h5>
                                                <?php if (! empty($mod['summary'])): ?>
                                                    <p class="text-muted small mb-2"><?= esc($mod['summary']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge bg-white text-muted border small"><i class="fas fa-file-alt text-primary me-1"></i> Modul</span>
                                                <span class="badge bg-white text-muted border small"><i class="fas fa-tasks text-warning me-1"></i> Quiz</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Standard Curriculum Blueprint for Course -->
                            <div class="d-flex flex-column gap-3 mt-2">
                                <div class="p-3 radius-12 border bg-light">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div>
                                            <span class="badge bg-primary text-white radius-6 px-2 py-1 small mb-1">Modul 1</span>
                                            <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Ketentuan Umum, Regulasi, &amp; Prinsip Dasar PBJ</h5>
                                            <p class="text-muted small mb-2">Kerangka hukum Perpres Pengadaan Barang/Jasa Pemerintah, peran para pihak, dan etika pengadaan.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-book-reader text-primary me-1"></i> Materi</span>
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-tasks text-warning me-1"></i> Quiz</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 radius-12 border bg-light">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div>
                                            <span class="badge bg-primary text-white radius-6 px-2 py-1 small mb-1">Modul 2</span>
                                            <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Perencanaan Pengadaan &amp; Penyusunan HPS</h5>
                                            <p class="text-muted small mb-2">Penyusunan RUP, identifikasi kebutuhan, spesifikasi teknis/KAK, dan survei pasar penentuan HPS.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-book-reader text-primary me-1"></i> Materi</span>
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-tasks text-warning me-1"></i> Quiz</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 radius-12 border bg-light">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div>
                                            <span class="badge bg-primary text-white radius-6 px-2 py-1 small mb-1">Modul 3</span>
                                            <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Metode Pemilihan Penyedia &amp; Pelaksanaan E-Purchasing</h5>
                                            <p class="text-muted small mb-2">Praktek tender, seleksi, negosiasi teknis dan biaya, serta transaksi e-katalog LKPP.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-book-reader text-primary me-1"></i> Materi</span>
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-tasks text-warning me-1"></i> Quiz</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 radius-12 border bg-light">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div>
                                            <span class="badge bg-primary text-white radius-6 px-2 py-1 small mb-1">Modul 4</span>
                                            <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Pengendalian Kontrak, Mitigasi Risiko, &amp; BAST</h5>
                                            <p class="text-muted small mb-2">Monitoring kemajuan pekerjaan, adendum kontrak, serah terima hasil pengadaan, dan evaluasi penyedia.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-book-reader text-primary me-1"></i> Materi</span>
                                            <span class="badge bg-white text-muted border small"><i class="fas fa-tasks text-warning me-1"></i> Quiz</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Batch Kelas Siap Daftar Card (with pagination & search) -->
                    <div id="daftar-kelas" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge bg-success-subtle text-success radius-20 px-3 py-1 small fw-bold text-uppercase">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Batch Pelatihan Interaktif
                                </span>
                                <h3 class="fw-bold text-dark mt-2 mb-1" style="font-size: 22px;">Jadwal Kelas &amp; Batch Pembelajaran</h3>
                                <p class="text-muted small mb-0">Daftar kelas aktif dengan pendampingan fasilitator, tugas studi kasus, evaluasi sertifikasi, dan sertifikat resmi.</p>
                            </div>
                            <div>
                                <span class="badge bg-primary text-white px-3 py-2 radius-12 fw-semibold">
                                    <?= (int) ($totalClasses ?? count($classes)) ?> Batch Terbuka
                                </span>
                            </div>
                        </div>

                        <!-- Search Batch Classes within this course -->
                        <form action="<?= current_url() ?>#daftar-kelas" method="get" class="d-flex align-items-center gap-2 mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 radius-8"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="q_class" value="<?= esc($classSearch ?? '') ?>" class="form-control border-start-0" placeholder="Cari nama atau kode batch kelas..." style="font-size: 14px;">
                                <button type="submit" class="btn btn-primary radius-8 px-3">
                                    <span>Cari Batch</span>
                                </button>
                            </div>
                            <?php if (! empty($classSearch)): ?>
                                <a href="<?= current_url() ?>#daftar-kelas" class="btn btn-outline-secondary radius-8 px-3" title="Reset Pencarian">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>

                        <!-- List of Classes -->
                        <?php if (empty($classes)): ?>
                            <div class="alert alert-light border radius-12 p-4 text-center my-2">
                                <i class="fas fa-calendar-times text-muted mb-2" style="font-size: 32px; opacity: 0.5;"></i>
                                <h5 class="fw-bold mb-1">Belum Ada Batch Kelas Aktif</h5>
                                <p class="text-muted small mb-0">
                                    <?= ! empty($classSearch) ? 'Tidak ditemukan batch dengan kata kunci "' . esc($classSearch) . '".' : 'Saat ini belum ada jadwal kelas aktif untuk course ini. Silakan simpan (bookmark) untuk mendapatkan pembaruan jadwal.' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($classes as $cls): ?>
                                    <article class="p-3 p-md-4 radius-12 border bg-light hover-border-primary transition">
                                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="badge bg-dark text-white px-2 py-1 radius-6 small fw-semibold">
                                                    Batch: <?= esc($cls['code'] ?? '-') ?>
                                                </span>
                                                <?= view('landing/components/status_badge', ['status' => $cls['status'] ?? '-']) ?>
                                                <?php if (! empty($cls['learning_method'])): ?>
                                                    <?php
                                                    $methodConfig = match ($cls['learning_method']) {
                                                        'online'  => ['label' => 'Online LMS', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
                                                        'offline' => ['label' => 'Tatap Muka', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                                        'hybrid'  => ['label' => 'Hybrid', 'class' => 'bg-purple-subtle text-purple border border-purple-subtle', 'style' => 'background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;'],
                                                        default   => null,
                                                    };
                                                    ?>
                                                    <?php if ($methodConfig): ?>
                                                        <span class="badge radius-6 small fw-medium <?= $methodConfig['class'] ?>" <?= isset($methodConfig['style']) ? 'style="' . $methodConfig['style'] . '"' : '' ?>>
                                                            <i class="fas fa-chalkboard me-1"></i><?= $methodConfig['label'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'     => 'learning_class',
                                                'id'       => (int) ($cls['id'] ?? 0),
                                                'title'    => $cls['name'] ?? $cls['title'] ?? null,
                                                'iconOnly' => true,
                                                'size'     => 'sm',
                                            ]) ?>
                                        </div>

                                        <h4 class="fw-bold mb-2" style="font-size: 17px;">
                                            <a href="<?= route_to('public.learning.class.detail', $cls['code']) ?>" class="text-dark text-decoration-none hover-primary">
                                                <?= esc($cls['name'] ?? '-') ?>
                                            </a>
                                        </h4>

                                        <!-- Meta Strip -->
                                        <div class="d-flex flex-wrap align-items-center gap-3 py-2 my-2 border-top border-bottom" style="font-size: 13px;">
                                            <span class="text-muted">
                                                <i class="far fa-calendar-alt text-primary me-1"></i>
                                                <?= esc(($cls['start_at'] ?? '-') . ' s/d ' . ($cls['end_at'] ?? '-')) ?>
                                            </span>
                                            <span class="text-muted">&middot;</span>
                                            <span class="text-muted">
                                                <i class="fas fa-users text-info me-1"></i>
                                                Kapasitas: <?= esc((string) ($cls['capacity'] ?? '-')) ?> peserta
                                            </span>
                                            <?php if (isset($cls['remaining_quota'])): ?>
                                                <span class="text-muted">&middot;</span>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    <i class="fas fa-user-check me-1"></i> Sisa <?= (int) $cls['remaining_quota'] ?> Slot
                                                </span>
                                            <?php endif; ?>
                                            <?php if (! empty($cls['location_or_link'])): ?>
                                                <span class="text-muted">&middot;</span>
                                                <span class="text-muted" title="<?= esc($cls['location_or_link']) ?>">
                                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                    <?= esc(character_limiter($cls['location_or_link'], 35)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Trainers Preview if any -->
                                        <?php if (! empty($cls['trainers'])): ?>
                                            <div class="d-flex align-items-center flex-wrap gap-2 mb-3 small text-muted">
                                                <i class="fas fa-chalkboard-teacher text-primary"></i>
                                                <span>Fasilitator:</span>
                                                <?php foreach ($cls['trainers'] as $t): ?>
                                                    <span class="badge bg-white text-dark border">
                                                        <?= esc($t['name'] ?? $t['username']) ?>
                                                        <?php if (! empty($t['role']) && $t['role'] !== 'primary'): ?>
                                                            <small class="text-muted ms-1">(<?= $t['role'] === 'co_trainer' ? 'Co-Trainer' : 'Narasumber' ?>)</small>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Action Buttons -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
                                            <a class="btn btn-sm btn-primary radius-8 px-3 fw-semibold" href="<?= route_to('public.learning.class.detail', $cls['code']) ?>">
                                                <span>Lihat Jadwal &amp; Daftar Kelas</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'learning_class',
                                                'id'    => (int) ($cls['id'] ?? 0),
                                                'title' => $cls['name'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <!-- Class Pagination -->
                            <?php if (! empty($classPager)): ?>
                                <?php $classLinks = $classPager->links('classes', 'default_full') ?: $classPager->links('default', 'default_full'); ?>
                                <?php if ($classLinks): ?>
                                    <div class="row mt-4">
                                        <div class="col-12 d-flex justify-content-center">
                                            <?= $classLinks ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- 5. Related Courses in Same Program -->
                    <?php if (! empty($relatedCourses)): ?>
                        <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h4 class="fw-bold text-dark mb-0" style="font-size: 18px;">
                                    <i class="fas fa-layer-group text-primary me-2"></i> Course Terkait dalam Program Ini
                                </h4>
                                <?php if (! empty($item['program_slug'])): ?>
                                    <a href="<?= route_to('public.learning.program.detail', $item['program_slug']) ?>" class="small text-primary text-decoration-none fw-semibold">
                                        Lihat Seluruh Kurikulum <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="row g-3">
                                <?php foreach ($relatedCourses as $rcIdx => $rc): ?>
                                    <?php
                                    $rcImg = ! empty($rc['cover_url'])
                                        ? (str_starts_with($rc['cover_url'], 'http') ? $rc['cover_url'] : base_url($rc['cover_url']))
                                        : base_url("assets/main/images/resources/course-list-img-" . (($rcIdx % 6) + 1) . ".jpg");
                                    ?>
                                    <div class="col-md-4">
                                        <div class="card h-100 border radius-12 overflow-hidden shadow-2xs hover-border-primary transition">
                                            <div style="height: 110px; overflow: hidden;">
                                                <img src="<?= esc($rcImg) ?>" alt="<?= esc($rc['title']) ?>" class="w-100 h-100" style="object-fit: cover;">
                                            </div>
                                            <div class="p-3 d-flex flex-column flex-grow-1">
                                                <span class="badge bg-light text-muted border radius-20 small align-self-start mb-1" style="font-size: 12px;">
                                                    <?= esc(ucfirst($rc['level'] ?? 'Dasar')) ?>
                                                </span>
                                                <h6 class="fw-bold mb-2" style="font-size: 15px; line-height: 1.4;">
                                                    <a href="<?= route_to('public.learning.course.detail', $rc['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                                        <?= esc($rc['title']) ?>
                                                    </a>
                                                </h6>
                                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                                    <span class="text-muted" style="font-size: 12.5px;">
                                                        <i class="far fa-clock me-1"></i> <?= ($rc['duration_minutes'] ?? null) ? esc((string) $rc['duration_minutes']) . ' mnt' : 'Mandiri' ?>
                                                    </span>
                                                    <a href="<?= route_to('public.learning.course.detail', $rc['slug']) ?>" class="text-primary fw-semibold text-decoration-none" style="font-size: 13px;">
                                                        Buka <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Sticky Sidebar Column (4 cols) -->
                <div class="col-xl-4 col-lg-5">
                    <!-- 1. Sticky Course Summary Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 sticky-top mb-4 bg-white" style="top: 100px;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 18px;">Informasi Course</h4>
                            <span class="badge bg-primary text-white radius-20 px-3 py-1 small">Aktif</span>
                        </div>

                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4" style="font-size: 13.5px;">
                            <li class="d-flex align-items-start justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-layer-group text-primary me-2"></i> Induk Kurikulum</span>
                                <span class="fw-semibold text-end" style="max-width: 170px;">
                                    <?php if (! empty($item['program_slug'])): ?>
                                        <a href="<?= route_to('public.learning.program.detail', $item['program_slug']) ?>" class="text-primary text-decoration-none">
                                            <?= esc($item['program_title'] ?? 'Program PBJ') ?>
                                        </a>
                                    <?php else: ?>
                                        <?= esc($item['program_title'] ?? 'Program PBJ') ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-signal text-warning me-2"></i> Tingkat Kesulitan</span>
                                <span class="fw-semibold"><?= esc($levelConfig['label']) ?></span>
                            </li>
                            <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="far fa-clock text-info me-2"></i> Durasi Belajar</span>
                                <span class="fw-semibold"><?= $durationFormatted ?></span>
                            </li>
                            <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-laptop text-success me-2"></i> Metode Belajar</span>
                                <span class="fw-semibold">Hybrid / Asynchronous</span>
                            </li>
                            <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-users text-primary me-2"></i> Akses Peserta</span>
                                <span class="badge bg-light text-dark border"><?= ! empty($item['target_audience']) ? esc(character_limiter($item['target_audience'], 25)) : 'ASN &amp; Publik' ?></span>
                            </li>
                            <li class="d-flex align-items-center justify-content-between">
                                <span class="text-muted"><i class="fas fa-certificate text-warning me-2"></i> Sertifikat Resmi</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Tersedia di Kelas</span>
                            </li>
                        </ul>

                        <div class="d-flex flex-column gap-2">
                            <a href="#daftar-kelas" class="btn btn-primary w-100 radius-8 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-calendar-check"></i>
                                <span>Pilih Batch &amp; Daftar</span>
                            </a>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'course',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['title'] ?? $itemTitle,
                                'size'  => 'md',
                            ]) ?>
                        </div>
                    </div>

                    <!-- 2. Pengajar & Fasilitator Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i> Instruktur &amp; Fasilitator
                        </h5>
                        <?php if (! empty($trainers)): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($trainers as $trn): ?>
                                    <div class="d-flex align-items-center gap-3 p-2 radius-10 bg-light border">
                                        <div class="radius-50 overflow-hidden flex-shrink-0" style="width: 44px; height: 44px; background: #e2e8f0; border-radius: 50%;">
                                            <img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt="<?= esc($trn['name'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">
                                                <a href="<?= route_to('public.learning.trainer.detail', $trn['username']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($trn['name'] ?? $trn['username']) ?>
                                                </a>
                                            </h6>
                                            <span class="text-muted small" style="font-size: 13.5px;"><?= esc($trn['profile_type'] ?? 'Pengajar PBJ') ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-3 radius-10 bg-light border">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; background: #e2e8f0;">
                                        <img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt="Tim Fasilitator" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Tim Fasilitator PBJ Lampung</h6>
                                        <span class="text-muted small" style="font-size: 13.5px;">Widyaiswara &amp; Pejabat Fungsional PBJ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Bantuan & FAQ Singkat -->
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 17.5px;">
                            <i class="fas fa-question-circle text-info me-2"></i> Bantuan &amp; Panduan
                        </h5>
                        <div class="d-flex flex-column gap-3" style="font-size: 14px;">
                            <div>
                                <strong class="d-block text-dark mb-1">Bagaimana alur pendaftaran?</strong>
                                <p class="text-muted mb-0">Buat akun di BegawiPengadaan, lengkapi profil, lalu pilih batch kelas yang sedang dibuka untuk diajukan enrollment.</p>
                            </div>
                            <div class="pt-2 border-top">
                                <strong class="d-block text-dark mb-1">Apakah mendapatkan sertifikat?</strong>
                                <p class="text-muted mb-0">Ya, kelulusan pada batch kelas dengan memenuhi standar nilai kuis, tugas, dan post-test akan menerbitkan sertifikat resmi ber-QR Code.</p>
                            </div>
                            <div class="pt-2 border-top text-center">
                                <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary radius-8 w-100">
                                    <i class="fas fa-headset me-1"></i> Hubungi Helpdesk BegawiPengadaan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php elseif ($catalogType === 'programs'): ?>
    <!-- =========================================================================
     PROGRAM DETAIL — Clean Magazine Benchmark
     ========================================================================= -->
    <section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
        <div class="container">
            <!-- Compact Breadcrumb Navigation -->
            <div class="arena-breadcrumb evt-breadcrumb mb-3">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar/program') ?>">Program Pembelajaran</a>
                <span class="sep">/</span>
                <span class="current text-truncate"><?= esc($item['title'] ?? $itemTitle) ?></span>
            </div>

            <!-- Compact Program Detail Hero Banner (arena-hero class preserved for test) -->
            <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11.5px;">
                                <i class="fas fa-graduation-cap me-1"></i> Program Terstruktur PBJ
                            </span>
                            <?php if (! empty($item['featured'])): ?>
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-8 shadow-sm" style="font-size: 11.5px;">
                                    <i class="fas fa-star me-1 text-dark"></i> Kurikulum Unggulan
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-white text-primary fw-bold px-2 py-1 radius-8 shadow-sm" style="font-size: 11.5px;">
                                <i class="fas fa-shield-alt me-1"></i> Standar LKPP
                            </span>
                            <span class="badge bg-success text-white px-2 py-1 radius-8" style="font-size: 11.5px;">
                                <i class="fas fa-check-circle me-1"></i> Terbuka untuk Publik &amp; ASN
                            </span>
                        </div>

                        <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; line-height: 1.3;">
                            <?= esc($item['title'] ?? $itemTitle) ?>
                        </h1>

                        <p class="mb-3 text-white-90" style="font-size: 14px; line-height: 1.55; max-width: 720px; color: rgba(255,255,255,0.9);">
                            <?= esc($item['summary'] ?? 'Jalur kurikulum terpadu pengadaan barang dan jasa pemerintah Provinsi Lampung.') ?>
                        </p>

                        <!-- Hero Quick Action Buttons -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="#daftar-course" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-1 radius-8 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-book-open"></i>
                                <span>Silabus &amp; Course PBJ (<?= (int) ($totalCourses ?? count($courses)) ?>)</span>
                            </a>
                            <?php if (! empty($classes)): ?>
                                <a href="#jadwal-kelas" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Batch Kelas Terbuka (<?= count($classes) ?>)</span>
                                </a>
                            <?php endif; ?>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'program',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['title'] ?? $itemTitle,
                                'size'  => 'sm',
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <!-- Glass Quick Stat Card in Hero -->
                        <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                            <span class="text-white-50 small fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px; font-size: 11px;">Ringkasan Kurikulum:</span>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-book-open me-1 text-warning"></i> Total Course</span>
                                <span class="fw-bold fs-6"><?= (int) ($totalCourses ?? count($courses)) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-chalkboard-teacher me-1 text-info"></i> Batch Kelas</span>
                                <span class="fw-bold fs-6"><?= count($classes) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-user-graduate me-1 text-success"></i> Peserta Aktif</span>
                                <span class="fw-bold fs-6"><?= (int) ($enrolled_count ?? 0) ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-white small">
                                <span><i class="fas fa-certificate me-1 text-warning"></i> Sertifikat</span>
                                <span class="badge bg-success text-white" style="font-size: 10px;">Tersedia Resmi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Main Column (8 cols) -->
                <div class="col-xl-8 col-lg-8">
                    <!-- 1. Quick Navigation Anchor Bar -->
                    <div class="card border-0 shadow-xs radius-12 p-2 mb-4 bg-white d-flex flex-row flex-wrap gap-2">
                        <a href="#tentang" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                            <i class="fas fa-info-circle me-1 text-primary"></i> Tentang Program
                        </a>
                        <a href="#daftar-course" class="btn btn-sm btn-primary radius-8 px-3 fw-semibold text-white">
                            <i class="fas fa-book-open me-1"></i> Silabus Course (<?= (int) ($totalCourses ?? count($courses)) ?>)
                        </a>
                        <?php if (! empty($classes)): ?>
                            <a href="#jadwal-kelas" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                                <i class="fas fa-calendar-check me-1 text-success"></i> Jadwal Kelas (<?= count($classes) ?>)
                            </a>
                        <?php endif; ?>
                        <a href="#kompetensi" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                            <i class="fas fa-award me-1 text-warning"></i> Kompetensi &amp; Sertifikat
                        </a>
                    </div>

                    <!-- 2. Program Overview / Description -->
                    <div class="card border-0 shadow-sm radius-16 p-4 p-md-5 mb-4 bg-white" id="tentang">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <span class="arena-section-head__tag m-0">
                                <i class="fas fa-file-alt me-1"></i> Deskripsi &amp; Sasaran
                            </span>
                            <span class="badge bg-light text-muted border">ID Kurikulum: #<?= (int) ($item['id'] ?? 0) ?></span>
                        </div>

                        <h3 class="fw-bold mb-3" style="font-size: 24px; color: #082f5c;">
                            Tentang Kurikulum Pembelajaran
                        </h3>

                        <?php if (! empty($item['summary'])): ?>
                            <p class="lead mb-3" style="font-size: 16px; color: #334155; line-height: 1.7;">
                                <?= esc($item['summary']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="text-secondary" style="font-size: 15px; line-height: 1.8;">
                            <?= nl2br(esc($item['description'] ?? 'Kurikulum komprehensif ini dirancang untuk membekali aparatur sipil negara dan praktisi pengadaan dengan kompetensi teknis, manajerial, serta pemahaman regulasi terkini dalam Pengadaan Barang/Jasa Pemerintah.')) ?>
                        </div>

                        <!-- 4 Competency Pillars for this Curriculum or Custom Learning Objectives -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                                <i class="fas fa-award text-primary me-2"></i> Capaian &amp; Target Kompetensi Program
                            </h5>
                            <?php
                            $customProgObjectives = ! empty($item['learning_objectives'])
                                ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $item['learning_objectives'])))
                                : [];
                            ?>
                            <?php if (! empty($customProgObjectives)): ?>
                                <div class="row g-2">
                                    <?php foreach ($customProgObjectives as $obj): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start gap-2 p-3 radius-12 bg-light border h-100">
                                                <i class="fas fa-check-circle text-success mt-1"></i>
                                                <span class="small text-dark fw-semibold"><?= esc($obj) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 radius-12 bg-light border">
                                            <span class="badge bg-primary text-white p-2 radius-8"><i class="fas fa-balance-scale"></i></span>
                                            <div>
                                                <h6 class="fw-bold mb-1" style="font-size: 14px;">Kepatuhan Regulasi</h6>
                                                <p class="text-muted small mb-0">Sesuai Perpres No. 16/2018 jo. Perpres No. 12/2021.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 radius-12 bg-light border">
                                            <span class="badge bg-success text-white p-2 radius-8"><i class="fas fa-chart-line"></i></span>
                                            <div>
                                                <h6 class="fw-bold mb-1" style="font-size: 14px;">Studi Kasus &amp; Praktik</h6>
                                                <p class="text-muted small mb-0">Latihan penyusunan HPS, spesifikasi, dan dokumen pemilihan.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 radius-12 bg-light border">
                                            <span class="badge bg-warning text-dark p-2 radius-8"><i class="fas fa-file-contract"></i></span>
                                            <div>
                                                <h6 class="fw-bold mb-1" style="font-size: 14px;">Manajemen Kontrak</h6>
                                                <p class="text-muted small mb-0">Pengendalian SPK, mitigasi sengketa, dan serah terima BAST.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 radius-12 bg-light border">
                                            <span class="badge bg-info text-white p-2 radius-8"><i class="fas fa-qrcode"></i></span>
                                            <div>
                                                <h6 class="fw-bold mb-1" style="font-size: 14px;">Sertifikat Terverifikasi</h6>
                                                <p class="text-muted small mb-0">Sertifikat digital terverifikasi QR-Code resmi BegawiPengadaan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (! empty($item['target_audience']) || ! empty($item['prerequisites'])): ?>
                                <div class="row g-3 mt-2 pt-3 border-top">
                                    <?php if (! empty($item['target_audience'])): ?>
                                        <div class="col-md-<?= ! empty($item['prerequisites']) ? '6' : '12' ?>">
                                            <div class="p-3 radius-10 border bg-light h-100">
                                                <h6 class="fw-bold text-dark mb-2" style="font-size: 13.5px;">
                                                    <i class="fas fa-user-tag text-primary me-2"></i> Sasaran &amp; Target Peserta
                                                </h6>
                                                <p class="text-muted small mb-0" style="line-height: 1.6;">
                                                    <?= nl2br(esc($item['target_audience'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (! empty($item['prerequisites'])): ?>
                                        <div class="col-md-<?= ! empty($item['target_audience']) ? '6' : '12' ?>">
                                            <div class="p-3 radius-10 border bg-light h-100">
                                                <h6 class="fw-bold text-dark mb-2" style="font-size: 13.5px;">
                                                    <i class="fas fa-clipboard-check text-warning me-2"></i> Prasyarat Mengikuti Kurikulum
                                                </h6>
                                                <p class="text-muted small mb-0" style="line-height: 1.6;">
                                                    <?= nl2br(esc($item['prerequisites'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. Courses in this Program (WITH SEARCH & PAGINATION) -->
                    <div class="card border-0 shadow-sm radius-16 p-4 p-md-5 mb-4 bg-white" id="daftar-course">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                            <div>
                                <span class="arena-section-head__tag m-0">
                                    <i class="fas fa-layer-group me-1"></i> Struktur Materi
                                </span>
                                <h3 class="fw-bold mt-2 mb-1" style="font-size: 22px; color: #082f5c;">
                                    Daftar Course PBJ Terhubung
                                </h3>
                                <p class="text-muted small mb-0">
                                    Menampilkan <?= count($courses) ?> dari total <?= (int) ($totalCourses ?? count($courses)) ?> course dalam kurikulum ini.
                                </p>
                            </div>

                            <!-- Course Search within Program -->
                            <div style="min-width: 250px;">
                                <form action="<?= current_url() ?>#daftar-course" method="get" class="d-flex gap-2">
                                    <input type="text" name="q_course" value="<?= esc($courseSearch ?? '') ?>" class="form-control form-control-sm radius-8" placeholder="Cari judul course...">
                                    <button type="submit" class="btn btn-sm btn-primary radius-8 px-3">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (! empty($courseSearch)): ?>
                                        <a href="<?= current_url() ?>#daftar-course" class="btn btn-sm btn-outline-secondary radius-8" title="Reset Pencarian Course">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <?php if ($courses === []): ?>
                            <div class="alert alert-light text-center border p-4 radius-12">
                                <i class="fas fa-search text-muted mb-2" style="font-size: 32px; opacity: 0.4;"></i>
                                <p class="text-muted mb-0">
                                    <?= ! empty($courseSearch) ? 'Tidak ada course dengan kata kunci "' . esc($courseSearch) . '".' : 'Belum ada course yang terdaftar dalam program ini.' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($courses as $index => $course): ?>
                                    <?php
                                    $coverCourse = ! empty($course['cover_url'])
                                        ? (str_starts_with($course['cover_url'], 'http') ? $course['cover_url'] : base_url($course['cover_url']))
                                        : base_url("assets/main/images/resources/course-list-img-" . (($index % 6) + 1) . ".jpg");
                                    ?>
                                    <div class="col-12">
                                        <article class="p-3 radius-14 border bg-white shadow-2xs hover-border-primary transition-all d-flex flex-column flex-md-row gap-3 align-items-md-center">
                                            <div class="radius-10 overflow-hidden flex-shrink-0 position-relative" style="width: 100%; max-width: 160px; height: 110px;">
                                                <img src="<?= esc($coverCourse) ?>" alt="<?= esc($course['title']) ?>" class="w-100 h-100" style="object-fit: cover;" loading="lazy">
                                                <span class="badge bg-primary text-white position-absolute top-0 start-0 m-2" style="font-size: 12px;">
                                                    <?= esc(ucfirst($course['level'] ?? 'PBJ')) ?>
                                                </span>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                                    <span class="badge bg-light text-primary border" style="font-size: 12.5px;">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?= ($course['duration_minutes'] ?? null) !== null ? esc((string) $course['duration_minutes']) . ' Menit' : 'Mandiri' ?>
                                                    </span>
                                                    <?= view('landing/components/bookmark_button', [
                                                        'type'  => 'course',
                                                        'id'    => (int) ($course['id'] ?? 0),
                                                        'title' => $course['title'] ?? null,
                                                        'size'  => 'sm',
                                                    ]) ?>
                                                </div>

                                                <h5 class="fw-bold mb-1" style="font-size: 16px;">
                                                    <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>" class="text-dark hover-primary text-decoration-none">
                                                        <?= esc($course['title'] ?? '-') ?>
                                                    </a>
                                                </h5>

                                                <p class="text-muted small mb-2" style="line-height: 1.5;">
                                                    <?= esc(mb_strimwidth($course['summary'] ?? $course['description'] ?? 'Materi pengadaan barang/jasa.', 0, 120, '…')) ?>
                                                </p>

                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-semibold">
                                                        <span>Buka Materi Course</span>
                                                        <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Course Pagination Links -->
                            <?php if (isset($coursePager)): ?>
                                <?php $pLinks = $coursePager->links('courses', 'default_full'); ?>
                                <?php if ($pLinks): ?>
                                    <div class="pagination-wrapper text-center mt-4 pt-3 border-top">
                                        <div class="d-flex justify-content-center">
                                            <?= $pLinks ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Active Classes & Batches for this Program -->
                    <?php if (! empty($classes)): ?>
                        <div class="card border-0 shadow-sm radius-16 p-4 p-md-5 mb-4 bg-white" id="jadwal-kelas">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                <div>
                                    <span class="arena-section-head__tag m-0">
                                        <i class="fas fa-calendar-alt me-1"></i> Pelatihan Interaktif
                                    </span>
                                    <h3 class="fw-bold mt-2 mb-1" style="font-size: 22px; color: #082f5c;">
                                        Kelas &amp; Batch Pembelajaran Siap Daftar
                                    </h3>
                                    <p class="text-muted small mb-0">
                                        Pilih jadwal kelas aktif yang didampingi oleh fasilitator tersertifikasi.
                                    </p>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 radius-20 fw-semibold">
                                    <?= count($classes) ?> Kelas Terbuka
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($classes as $cl): ?>
                                    <div class="p-3 p-md-4 radius-14 border bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div>
                                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                                <span class="badge bg-primary text-white radius-6" style="font-size: 12px;"><?= esc($cl['code']) ?></span>
                                                <span class="badge bg-success text-white radius-6" style="font-size: 12px;"><?= esc(ucfirst($cl['status'])) ?></span>
                                                <?php if (! empty($cl['learning_method'])): ?>
                                                    <?php
                                                    $clMethod = match ($cl['learning_method']) {
                                                        'online'  => ['label' => 'Online LMS', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
                                                        'offline' => ['label' => 'Tatap Muka', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                                        'hybrid'  => ['label' => 'Hybrid', 'class' => 'bg-purple-subtle text-purple border border-purple-subtle', 'style' => 'background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;'],
                                                        default   => null,
                                                    };
                                                    ?>
                                                    <?php if ($clMethod): ?>
                                                        <span class="badge radius-6 small <?= $clMethod['class'] ?>" style="font-size: 12px; <?= $clMethod['style'] ?? '' ?>">
                                                            <i class="fas fa-chalkboard me-1"></i><?= $clMethod['label'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <h5 class="fw-bold mb-1" style="font-size: 17px; color: #0f172a;">
                                                <?= esc($cl['name']) ?>
                                            </h5>
                                            <p class="text-muted small mb-2">
                                                Course: <strong><?= esc($cl['course_title'] ?? '-') ?></strong>
                                            </p>
                                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                                <span><i class="far fa-calendar-alt text-primary me-1"></i> <?= esc($cl['start_at']) ?> s/d <?= esc($cl['end_at']) ?></span>
                                                <span><i class="fas fa-users text-primary me-1"></i> Kapasitas: <?= esc((string) ($cl['capacity'] ?? '30')) ?> Peserta</span>
                                                <?php if (! empty($cl['location_or_link'])): ?>
                                                    <span title="<?= esc($cl['location_or_link']) ?>"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc(character_limiter($cl['location_or_link'], 30)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 d-flex flex-column gap-2">
                                            <a href="<?= route_to('public.learning.class.detail', $cl['code']) ?>" class="btn btn-primary radius-8 px-4 fw-semibold text-nowrap">
                                                <span>Lihat Jadwal &amp; Daftar</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                            <?= view('landing/components/bookmark_button', [
                                                'type'  => 'learning_class',
                                                'id'    => (int) ($cl['id'] ?? 0),
                                                'title' => $cl['name'] ?? null,
                                                'size'  => 'sm',
                                            ]) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 5. Competency & Certification Guarantee -->
                    <div class="card border-0 shadow-sm radius-16 p-4 p-md-5 mb-4 bg-white" id="kompetensi" style="border-left: 5px solid #16a34a !important;">
                        <span class="arena-section-head__tag m-0 text-success" style="background: #f0fdf4;">
                            <i class="fas fa-certificate me-1"></i> Jaminan Mutu Pembelajaran
                        </span>
                        <h3 class="fw-bold mt-2 mb-2" style="font-size: 20px; color: #082f5c;">
                            Ketentuan Kelulusan &amp; Sertifikat Resmi
                        </h3>
                        <p class="text-secondary small mb-3" style="line-height: 1.7;">
                            Peserta yang terdaftar pada batch kelas dalam kurikulum ini wajib menyelesaikan seluruh modul materi, mengumpulkan tugas praktik studi kasus, serta mencapai ambang batas nilai kelulusan (*passing grade*) pada Post-Test akhir.
                        </p>
                        <div class="p-3 radius-12 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-award text-warning fs-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Sertifikat Digital Kompetensi PBJ</h6>
                                    <p class="text-muted small mb-0">Terakreditasi BPSDM &amp; Biro Pengadaan Barang dan Jasa Provinsi Lampung</p>
                                </div>
                            </div>
                            <span class="badge bg-success text-white px-3 py-2 radius-20">Verifikasi QR-Code</span>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar Column (4 cols) -->
                <div class="col-xl-4 col-lg-4">
                    <!-- 1. Sticky Program Info Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 mb-4 sticky-top bg-white" style="top: 100px;">
                        <?php
                        $progCover = ! empty($item['cover_url'])
                            ? (str_starts_with($item['cover_url'], 'http') ? $item['cover_url'] : base_url($item['cover_url']))
                            : base_url('assets/main/images/resources/course-details-img-1.jpg');
                        ?>
                        <!-- Cover Image -->
                        <div class="radius-12 overflow-hidden mb-3 position-relative" style="height: 180px;">
                            <img src="<?= esc($progCover) ?>" alt="<?= esc($itemTitle) ?>" class="w-100 h-100" style="object-fit: cover;">
                            <div class="position-absolute top-0 end-0 p-3">
                                <?= view('landing/components/bookmark_button', [
                                    'type'     => 'program',
                                    'id'       => (int) ($item['id'] ?? 0),
                                    'title'    => $item['title'] ?? $itemTitle,
                                    'iconOnly' => true,
                                    'size'     => 'sm',
                                ]) ?>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-3" style="font-size: 18px; color: #082f5c;">
                            Informasi Kurikulum
                        </h4>

                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted small"><i class="fas fa-layer-group text-primary me-2"></i> Kategori</span>
                                <span class="fw-semibold small">Kurikulum Berjenjang</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted small"><i class="fas fa-book-open text-warning me-2"></i> Jumlah Course</span>
                                <span class="fw-semibold small"><?= (int) ($totalCourses ?? count($courses)) ?> Course</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted small"><i class="fas fa-chalkboard-teacher text-success me-2"></i> Batch Kelas</span>
                                <span class="fw-semibold small"><?= count($classes) ?> Batch</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted small"><i class="fas fa-users text-info me-2"></i> Peserta Terdaftar</span>
                                <span class="fw-semibold small"><?= (int) ($enrolled_count ?? 0) ?> Peserta</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted small"><i class="fas fa-globe text-secondary me-2"></i> Akses</span>
                                <span class="fw-semibold small text-success"><?= ! empty($item['target_audience']) ? esc(character_limiter($item['target_audience'], 20)) : 'Publik &amp; Terbuka' ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-muted small"><i class="fas fa-certificate text-warning me-2"></i> Sertifikat</span>
                                <span class="fw-semibold small text-primary">Tersedia Resmi</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <?php if ($isLoggedIn): ?>
                                <a href="#daftar-course" class="btn btn-primary radius-8 py-2 fw-semibold w-100">
                                    <i class="fas fa-play-circle me-1"></i> Mulai Pembelajaran
                                </a>
                            <?php else: ?>
                                <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-primary radius-8 py-2 fw-semibold w-100">
                                    <i class="fas fa-user-plus me-1"></i> Daftar Akun &amp; Belajar
                                </a>
                            <?php endif; ?>

                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'program',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['title'] ?? $itemTitle,
                                'size'  => 'md',
                            ]) ?>
                        </div>

                        <!-- Trainers Directory if available -->
                        <?php if (! empty($trainers)): ?>
                            <div class="mt-4 pt-3 border-top">
                                <h5 class="fw-bold mb-3" style="font-size: 15px;">
                                    <i class="fas fa-user-tie text-primary me-1"></i> Tim Fasilitator PBJ
                                </h5>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($trainers as $tr): ?>
                                        <div class="d-flex align-items-center gap-2 p-2 radius-8 bg-light">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                                                <?= strtoupper(substr($tr['name'], 0, 1)) ?>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate small"><?= esc($tr['name']) ?></div>
                                                <div class="text-muted" style="font-size: 12.5px;"><?= esc($tr['profile_type'] ?? 'Pengajar PBJ') ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Helpdesk Card -->
                        <div class="mt-4 pt-3 border-top text-center">
                            <p class="text-muted small mb-2">Ada pertanyaan tentang kurikulum ini?</p>
                            <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 w-100">
                                <i class="fas fa-headset me-1 text-primary"></i> Hubungi Helpdesk BegawiPengadaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php elseif ($catalogType === 'classes'):
    $trainers         = $item['trainers'] ?? [];
    $modules          = $modules ?? [];
    $otherClasses     = $otherClasses ?? [];
    $classStatus      = strtolower((string) ($item['status'] ?? 'scheduled'));
    $enrollable       = in_array($classStatus, ['scheduled', 'active'], true);
    $statusConfig     = $classStatus === 'active'
        ? ['label' => 'Sedang Berjalan (Aktif)', 'badge' => 'bg-success text-white', 'icon' => 'fa-play-circle']
        : ['label' => 'Jadwal Terbuka (Segera Dimulai)', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-clock'];

    $startDate = ! empty($item['start_at']) ? date('d M Y', strtotime($item['start_at'])) : '-';
    $endDate   = ! empty($item['end_at']) ? date('d M Y', strtotime($item['end_at'])) : '-';
    $regOpen   = ! empty($item['registration_open_at']) ? date('d M Y', strtotime($item['registration_open_at'])) : null;
    $regClose  = ! empty($item['registration_close_at']) ? date('d M Y', strtotime($item['registration_close_at'])) : null;

    $capacity       = (int) ($item['capacity'] ?? 0);
    $enrolledCount  = (int) ($item['enrolled_count'] ?? 0);
    $remainingQuota = isset($item['remaining_quota']) ? (int) $item['remaining_quota'] : max(0, $capacity - $enrolledCount);
    $quotaPct       = $capacity > 0 ? min(100, round(($enrolledCount / $capacity) * 100)) : 0;

    $passingGrade     = (float) ($item['passing_grade'] ?? 70);
    $quizWeight       = (float) ($item['quiz_weight'] ?? 30);
    $assignmentWeight = (float) ($item['assignment_weight'] ?? 30);
    $postTestWeight   = (float) ($item['post_test_weight'] ?? 40);
?>

    <!-- =========================================================================
     CLASS DETAIL — Clean Magazine Benchmark
     ========================================================================= -->
    <section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
        <div class="container">
            <!-- Compact Breadcrumb Navigation -->
            <div class="arena-breadcrumb evt-breadcrumb mb-3">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar/kelas') ?>">Kelas &amp; Batch PBJ</a>
                <span class="sep">/</span>
                <span class="current text-truncate"><?= esc($item['name'] ?? $itemTitle) ?></span>
            </div>

            <!-- Compact Class Detail Hero Banner (arena-hero class preserved for test) -->
            <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <!-- Badges Row -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11.5px;">
                                <i class="fas fa-chalkboard-teacher me-1"></i> Kelas &amp; Batch PBJ
                            </span>
                            <span class="badge <?= $statusConfig['badge'] ?> px-2 py-1 radius-8 small" style="font-size: 11.5px;">
                                <i class="fas <?= $statusConfig['icon'] ?> me-1"></i> <?= esc($statusConfig['label']) ?>
                            </span>
                            <span class="badge bg-white text-dark fw-bold font-monospace px-2 py-1 radius-8 shadow-sm" style="font-size: 11.5px;">
                                <i class="fas fa-hashtag text-primary me-1"></i> Batch: <?= esc($item['code'] ?? '-') ?>
                            </span>
                            <?php if (! empty($item['course_slug'])): ?>
                                <a href="<?= route_to('public.learning.course.detail', $item['course_slug']) ?>" class="badge bg-primary text-white fw-bold px-2 py-1 radius-8 shadow-sm text-decoration-none" style="font-size: 11.5px;">
                                    <i class="fas fa-book-open me-1"></i> Course: <?= esc($item['course_title'] ?? '-') ?>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-dark px-2 py-1 radius-8" style="font-size: 11.5px;">
                                    Course: <?= esc($item['course_title'] ?? '-') ?>
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-success text-white px-2 py-1 radius-8" style="font-size: 11.5px;">
                                <i class="fas fa-certificate me-1"></i> Sertifikat Resmi
                            </span>
                        </div>

                        <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; line-height: 1.3;">
                            <?= esc($item['name'] ?? $itemTitle) ?>
                        </h1>

                        <p class="mb-3 text-white-90" style="font-size: 14px; line-height: 1.55; max-width: 720px; color: rgba(255,255,255,0.9);">
                            Kelas intensif pengadaan barang/jasa pemerintah dengan bimbingan instruktur berkompeten, kurikulum modular terstruktur, penugasan studi kasus, dan evaluasi berbasis standar LKPP.
                        </p>

                        <!-- Hero Quick Action Buttons -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="#status-pendaftaran" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-1 radius-8 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-user-plus"></i>
                                <span>Status &amp; Pendaftaran Kelas</span>
                            </a>
                            <a href="#kurikulum-kelas" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-stream"></i>
                                <span>Kurikulum Pembelajaran (<?= count($modules) ?> Modul)</span>
                            </a>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'learning_class',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['name'] ?? $itemTitle,
                                'size'  => 'sm',
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <!-- Glass Quick Stat Card in Hero -->
                        <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                            <span class="text-white-50 small fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px; font-size: 11px;">Ringkasan Batch:</span>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-calendar-alt me-1 text-warning"></i> Periode Belajar</span>
                                <span class="fw-bold fs-6"><?= $startDate ?> - <?= $endDate ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-users me-1 text-info"></i> Sisa Kuota</span>
                                <span class="fw-bold fs-6 text-warning"><?= $remainingQuota ?> / <?= $capacity ?> kursi</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-award me-1 text-success"></i> Standar Kelulusan</span>
                                <span class="fw-bold fs-6"><?= (int) $passingGrade ?> / 100</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-white small">
                                <span><i class="fas fa-laptop-code me-1 text-primary"></i> Fasilitator</span>
                                <span class="badge bg-light text-dark fw-bold"><?= count($trainers) > 0 ? count($trainers) . ' Instruktur' : 'Tim Widyaiswara' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Nav Anchor Bar -->
            <div class="card border-0 shadow-xs radius-12 p-2 mb-4 bg-white d-flex flex-row flex-wrap gap-2">
                <a href="#ikhtisar" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-info-circle me-1 text-primary"></i> Ikhtisar Kelas
                </a>
                <a href="#tahapan-belajar" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-route me-1 text-info"></i> Tahapan Pembelajaran
                </a>
                <a href="#kurikulum-kelas" class="btn btn-sm btn-primary radius-8 px-3 fw-semibold text-white">
                    <i class="fas fa-stream me-1"></i> Modul &amp; Silabus (<?= count($modules) ?>)
                </a>
                <a href="#standar-kelulusan" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-balance-scale me-1 text-warning"></i> Standar Kelulusan
                </a>
                <?php if (! empty($otherClasses)): ?>
                    <a href="#batch-lainnya" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                        <i class="fas fa-calendar-alt me-1 text-success"></i> Batch Lainnya (<?= count($otherClasses) ?>)
                    </a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <!-- Left Main Column (col-xl-8 col-lg-7) -->
                <div class="col-xl-8 col-lg-7">
                    <!-- 1. Ikhtisar & Hubungan Kurikulum -->
                    <div id="ikhtisar" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 20px;">
                                <i class="fas fa-graduation-cap text-primary me-2"></i> Ikhtisar &amp; Hubungan Kurikulum
                            </h4>
                            <span class="badge <?= $statusConfig['badge'] ?> px-3 py-1 radius-20 small">
                                <?= esc($statusConfig['label']) ?>
                            </span>
                        </div>

                        <!-- Course Induk Banner Alert -->
                        <div class="p-3 radius-12 mb-4 border d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 48px; height: 48px; border-radius: 12px; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
                                    <i class="fas fa-book-open text-primary fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 12px; letter-spacing: 0.5px;">Induk Course PBJ</small>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">
                                        <?= esc($item['course_title'] ?? '-') ?>
                                    </h5>
                                    <?php if (! empty($item['program_title'])): ?>
                                        <small class="text-secondary">Program: <?= esc($item['program_title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (! empty($item['course_slug'])): ?>
                                <a href="<?= route_to('public.learning.course.detail', $item['course_slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 fw-bold">
                                    <span>Lihat Induk Course</span>
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">Deskripsi &amp; Sasaran Pelaksanaan</h5>
                        <div class="text-secondary mb-4" style="line-height: 1.7; font-size: 14.5px;">
                            <?= ! empty($item['description']) ? nl2br(esc($item['description'])) : 'Kelas <strong>' . esc($item['name']) . '</strong> (Kode Batch: <code class="fw-bold text-primary">' . esc($item['code']) . '</code>) dirancang khusus untuk memfasilitasi pendalaman materi pengadaan barang dan jasa secara terstruktur. Peserta akan mengikuti tahapan pembelajaran terarah, diskusi kasus nyata pengadaan di lingkungan Provinsi Lampung, serta uji kompetensi evaluatif.' ?>
                        </div>

                        <?php if (! empty($item['target_audience'])): ?>
                            <div class="p-3 radius-10 border bg-light mb-4">
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">
                                    <i class="fas fa-user-tag text-primary me-2"></i> Sasaran &amp; Target Peserta Batch Ini
                                </h6>
                                <p class="text-muted small mb-0" style="line-height: 1.6;">
                                    <?= nl2br(esc($item['target_audience'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Key Metrics Grid -->
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <i class="far fa-calendar-check text-primary fs-4 mb-2"></i>
                                    <span class="d-block text-muted small">Periode Kelas</span>
                                    <strong class="text-dark small d-block"><?= $startDate ?></strong>
                                    <small class="text-muted">s/d <?= $endDate ?></small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <i class="fas fa-users text-info fs-4 mb-2"></i>
                                    <span class="d-block text-muted small">Kapasitas Kelas</span>
                                    <strong class="text-dark fs-6 d-block"><?= $capacity ?> Peserta</strong>
                                    <small class="text-success fw-semibold">Sisa <?= $remainingQuota ?> Kursi</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <i class="fas fa-laptop text-success fs-4 mb-2"></i>
                                    <span class="d-block text-muted small">Metode Belajar</span>
                                    <strong class="text-dark small d-block"><?= ! empty($item['learning_method']) ? match ($item['learning_method']) {
                                                                                'online' => 'Online LMS',
                                                                                'offline' => 'Tatap Muka',
                                                                                'hybrid' => 'Hybrid',
                                                                                default => 'Online / Hybrid'
                                                                            } : 'Online LMS &amp; Hybrid' ?></strong>
                                    <small class="text-muted"><?= ! empty($item['location_or_link']) ? esc(character_limiter($item['location_or_link'], 20)) : 'Asynchronous &amp; Sesi' ?></small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <i class="fas fa-award text-warning fs-4 mb-2"></i>
                                    <span class="d-block text-muted small">Passing Grade</span>
                                    <strong class="text-dark fs-6 d-block"><?= (int) $passingGrade ?> / 100</strong>
                                    <small class="text-muted">Sertifikat Kelulusan</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Tahapan Pembelajaran (Roadmap) -->
                    <div id="tahapan-belajar" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 20px;">
                            <i class="fas fa-route text-info me-2"></i> Tahapan Pembelajaran Batch
                        </h4>
                        <p class="text-muted small mb-4">
                            Alur pengerjaan yang harus dilalui oleh peserta selama periode kelas berlangsung:
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-primary text-white" style="width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                        <h6 class="fw-bold mb-0 text-dark">Orientasi &amp; Pre-Test</h6>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Pengenalan silabus dan asesmen diagnostik awal guna memetakan pemahaman dasar peserta sebelum memasuki modul materi.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-info text-white" style="width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                        <h6 class="fw-bold mb-0 text-dark">Modul Pembelajaran &amp; Kuis</h6>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Mempelajari materi regulasi, studi kasus LKPP, video tutorial, serta menyelesaikan kuis formatif pada setiap modul.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-warning text-dark" style="width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                        <h6 class="fw-bold mb-0 text-dark">Tugas Praktik &amp; Evaluasi</h6>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Menyusun dokumen praktikum (RUP, HPS, spesifikasi teknis, atau draft kontrak) yang dievaluasi langsung oleh instruktur fasilitator.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-success text-white" style="width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">4</span>
                                        <h6 class="fw-bold mb-0 text-dark">Post-Test &amp; Penerbitan Sertifikat</h6>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Ujian komprehensif penentu kelulusan. Peserta yang melampaui passing grade berhak mengunduh sertifikat resmi ber-QR Code verifikasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Kurikulum & Modul Kelas (#kurikulum-kelas) -->
                    <div id="kurikulum-kelas" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                            <div>
                                <h4 class="fw-bold text-dark mb-0" style="font-size: 20px;">
                                    <i class="fas fa-stream text-primary me-2"></i> Kurikulum &amp; Silabus Modul
                                </h4>
                                <small class="text-muted">Struktur materi pembelajaran yang dipelajari pada kelas ini</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 radius-20 fw-semibold">
                                <?= count($modules) ?> Modul Tersedia
                            </span>
                        </div>

                        <?php if (empty($modules)): ?>
                            <div class="text-center py-4 px-3 radius-12 bg-light border">
                                <div style="width: 54px; height: 54px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Kurikulum Modul Sedang Dipersiapkan</h5>
                                <p class="text-muted small mb-0" style="max-width: 480px; margin: 0 auto;">
                                    Silabus dan modul pembelajaran lengkap akan otomatis disinkronkan saat kelas dimulai dan dapat diakses langsung oleh peserta terdaftar di Workspace.
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($modules as $mIdx => $mod): ?>
                                    <div class="p-3 radius-12 border bg-light">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <div style="width: 38px; height: 38px; border-radius: 10px; background: #eff6ff; color: var(--begawi-primary); display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                                                    <?= $mIdx + 1 ?>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">
                                                        <?= esc($mod['title']) ?>
                                                    </h6>
                                                    <?php if (! empty($mod['description'])): ?>
                                                        <p class="text-muted small mb-2" style="font-size: 13px; line-height: 1.5;">
                                                            <?= esc($mod['description']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <div class="d-flex flex-wrap align-items-center gap-2 small">
                                                        <?php if (! empty($mod['lesson_count'])): ?>
                                                            <span class="badge bg-white text-secondary border">
                                                                <i class="fas fa-book-open text-primary me-1"></i> <?= (int) $mod['lesson_count'] ?> Materi Bacaan / Video
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (! empty($mod['quiz_count'])): ?>
                                                            <span class="badge bg-white text-secondary border">
                                                                <i class="fas fa-question-circle text-warning me-1"></i> <?= (int) $mod['quiz_count'] ?> Kuis Formatif
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Standar Kelulusan & Bobot Penilaian (#standar-kelulusan) -->
                    <div id="standar-kelulusan" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 20px;">
                            <i class="fas fa-balance-scale text-warning me-2"></i> Standar Kelulusan &amp; Bobot Penilaian
                        </h4>
                        <p class="text-muted small mb-4">
                            Penilaian akhir dihitung secara kumulatif berdasarkan komponen-komponen berikut. Nilai akhir harus mencapai atau melampaui Passing Grade untuk dinyatakan LULUS.
                        </p>

                        <div class="p-3 radius-12 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #fefce8; border: 1px solid #fef08a;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #fef08a; color: #a16207; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Ambang Batas Nilai (Passing Grade)</h6>
                                    <small class="text-muted">Syarat minimal penerbitan sertifikat resmi</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark fs-5 fw-bold px-3 py-2 radius-12">
                                    <?= (int) $passingGrade ?> / 100
                                </span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 radius-20 mb-2">Kuis Modul</span>
                                    <h4 class="fw-bold text-dark mb-1"><?= (int) $quizWeight ?>%</h4>
                                    <small class="text-muted">Evaluasi pemahaman tiap materi modular</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 radius-20 mb-2">Tugas &amp; Praktik</span>
                                    <h4 class="fw-bold text-dark mb-1"><?= (int) $assignmentWeight ?>%</h4>
                                    <small class="text-muted">Penugasan dokumen pengadaan riil</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 bg-light border text-center h-100">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 radius-20 mb-2">Post-Test / Ujian</span>
                                    <h4 class="fw-bold text-dark mb-1"><?= (int) $postTestWeight ?>%</h4>
                                    <small class="text-muted">Ujian komprehensif akhir kelas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Batch Lainnya untuk Course Ini (#batch-lainnya) -->
                    <?php if (! empty($otherClasses)): ?>
                        <div id="batch-lainnya" class="card border-0 shadow-sm radius-16 p-4 bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                                <div>
                                    <h4 class="fw-bold text-dark mb-0" style="font-size: 20px;">
                                        <i class="fas fa-calendar-alt text-success me-2"></i> Jadwal Batch Lainnya untuk Course Ini
                                    </h4>
                                    <small class="text-muted">Pilihan batch alternatif jika jadwal batch saat ini belum sesuai</small>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-1 radius-20">
                                    <?= count($otherClasses) ?> Pilihan Batch
                                </span>
                            </div>

                            <div class="row g-3">
                                <?php foreach ($otherClasses as $oCls): ?>
                                    <?php
                                    $oStart = ! empty($oCls['start_at']) ? date('d M Y', strtotime($oCls['start_at'])) : '-';
                                    $oEnd   = ! empty($oCls['end_at']) ? date('d M Y', strtotime($oCls['end_at'])) : '-';
                                    ?>
                                    <div class="col-md-6">
                                        <div class="p-3 radius-12 border bg-light h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge bg-primary text-white font-monospace px-2 py-1 radius-8 small">
                                                        <?= esc($oCls['code']) ?>
                                                    </span>
                                                    <span class="badge bg-secondary-subtle text-secondary small">
                                                        Kapasitas: <?= esc((string) ($oCls['capacity'] ?? '-')) ?>
                                                    </span>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-2" style="font-size: 15px;">
                                                    <?= esc($oCls['name']) ?>
                                                </h6>
                                                <p class="text-muted small mb-3">
                                                    <i class="far fa-calendar-alt me-1 text-primary"></i> <?= $oStart ?> - <?= $oEnd ?>
                                                </p>
                                            </div>
                                            <div>
                                                <a href="<?= route_to('public.learning.class.detail', $oCls['code']) ?>" class="btn btn-sm btn-outline-primary radius-8 w-100 fw-semibold">
                                                    Lihat Detail Batch <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Sticky Sidebar Column (col-xl-4 col-lg-5) -->
                <div class="col-xl-4 col-lg-5">
                    <!-- 1. Sticky Enrollment & Quota Card (#status-pendaftaran) -->
                    <div id="status-pendaftaran" class="card border-0 shadow-sm radius-16 p-4 sticky-top mb-4 bg-white" style="top: 100px;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 18px;">Status Pendaftaran</h4>
                            <span class="badge <?= $statusConfig['badge'] ?> px-3 py-1 radius-20 small">
                                <?= esc($statusConfig['label']) ?>
                            </span>
                        </div>

                        <?php 
                        $effectiveClassCover = ! empty($item['cover_url']) ? $item['cover_url'] : ($item['course_cover_url'] ?? '');
                        if (! empty($effectiveClassCover)): ?>
                            <?php
                            $classCover = str_starts_with($effectiveClassCover, 'http') ? $effectiveClassCover : base_url($effectiveClassCover);
                            ?>
                            <div class="radius-12 overflow-hidden mb-3" style="max-height: 160px;">
                                <img src="<?= esc($classCover) ?>" alt="<?= esc($item['name']) ?>" class="w-100" style="object-fit: cover; max-height: 160px;">
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?><div class="alert alert-success small radius-8 p-2 mb-3"><?= esc($success) ?></div><?php endif; ?>
                        <?php if ($err): ?><div class="alert alert-danger small radius-8 p-2 mb-3"><?= esc($err) ?></div><?php endif; ?>

                        <!-- Quota Progress Indicator -->
                        <div class="p-3 radius-12 bg-light mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1 small">
                                <span class="text-muted fw-semibold">Kapasitas Kursi:</span>
                                <span class="fw-bold text-dark"><?= $enrolledCount ?> / <?= $capacity ?> Peserta</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                                <div class="progress-bar <?= $quotaPct >= 90 ? 'bg-danger' : ($quotaPct >= 70 ? 'bg-warning' : 'bg-primary') ?>" role="progressbar" style="width: <?= $quotaPct ?>%;" aria-valuenow="<?= $quotaPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Sisa Kuota:</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                    <?= $remainingQuota ?> kursi tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Date & Class Meta Details -->
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-3 small">
                            <li class="d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="far fa-calendar-alt text-primary me-2"></i> Periode Kelas</span>
                                <span class="fw-semibold text-end"><?= $startDate ?> - <?= $endDate ?></span>
                            </li>
                            <?php if ($regOpen || $regClose): ?>
                                <li class="d-flex justify-content-between pb-2 border-bottom">
                                    <span class="text-muted"><i class="far fa-calendar-check text-info me-2"></i> Masa Pendaftaran</span>
                                    <span class="fw-semibold text-end"><?= $regOpen ?? '-' ?> s/d <?= $regClose ?? '-' ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-barcode text-secondary me-2"></i> Kode Batch</span>
                                <span class="fw-bold font-monospace text-primary"><?= esc($item['code'] ?? '-') ?></span>
                            </li>
                            <li class="d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-chalkboard text-info me-2"></i> Metode Belajar</span>
                                <span class="fw-semibold text-end">
                                    <?= match ($item['learning_method'] ?? 'online') {
                                        'offline' => 'Tatap Muka',
                                        'hybrid' => 'Hybrid',
                                        default => 'Online LMS'
                                    } ?>
                                </span>
                            </li>
                            <?php if (! empty($item['location_or_link'])): ?>
                                <li class="d-flex justify-content-between pb-2 border-bottom">
                                    <span class="text-muted"><i class="fas fa-map-marker-alt text-danger me-2"></i> Lokasi / Media</span>
                                    <span class="fw-semibold text-end" style="max-width: 170px;"><?= esc($item['location_or_link']) ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-award text-warning me-2"></i> Passing Grade</span>
                                <span class="fw-semibold"><?= (int) $passingGrade ?> / 100</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-certificate text-success me-2"></i> Sertifikat</span>
                                <span class="badge bg-success text-white">Resmi LKPP</span>
                            </li>
                        </ul>

                        <!-- EXACT CTA Logic Preserved for Compatibility & E2E Tests -->
                        <div class="d-grid gap-2">
                            <?php if (! $enrollable): ?>
                                <button type="button" class="btn btn-secondary radius-8 py-2 w-100" disabled>
                                    <i class="fas fa-ban me-1"></i> Pendaftaran Ditutup
                                </button>
                            <?php elseif (! $isLoggedIn): ?>
                                <a href="<?= route_to('dashboard.auth.login') ?>" class="btn btn-primary radius-8 py-2 fw-semibold w-100">
                                    <i class="fas fa-sign-in-alt me-1"></i> Masuk untuk Mendaftar
                                </a>
                            <?php elseif ($enrollStatus === null): ?>
                                <form action="<?= site_url('arena-belajar/kelas/' . ($item['code'] ?? '') . '/daftar') ?>" method="post" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary w-100 radius-8 py-2 fw-semibold shadow-sm">
                                        <i class="fas fa-paper-plane me-1"></i> Daftar Kelas Ini
                                    </button>
                                </form>
                            <?php elseif ($enrollStatus === 'pending'): ?>
                                <div class="alert alert-warning text-center small mb-0 radius-8">
                                    <i class="fas fa-clock me-1"></i> Pendaftaran Menunggu Persetujuan Operator
                                </div>
                            <?php elseif ($enrollStatus === 'active'): ?>
                                <a href="<?= route_to('workspace.participant.class.detail', $item['id']) ?>" class="btn btn-success radius-8 py-2 fw-semibold w-100 shadow-sm">
                                    <i class="fas fa-play me-1"></i> Mulai Belajar di Workspace
                                </a>
                            <?php elseif ($enrollStatus === 'completed'): ?>
                                <div class="alert alert-success text-center small mb-0 radius-8">
                                    <i class="fas fa-check-circle me-1"></i> Anda Telah Menyelesaikan Kelas Ini
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger text-center small mb-0 radius-8">
                                    <i class="fas fa-times-circle me-1"></i> Pendaftaran Ditolak
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Bookmark Button Row -->
                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="small text-muted fw-semibold">Simpan Kelas:</span>
                            <?= view('landing/components/bookmark_button', [
                                'type'  => 'learning_class',
                                'id'    => (int) ($item['id'] ?? 0),
                                'title' => $item['name'] ?? null,
                                'size'  => 'sm',
                            ]) ?>
                        </div>

                        <!-- Preserved Course and Program references for compatibility -->
                        <div class="mt-3 text-muted small pt-2 border-top">
                            <span>Course: <?= esc($item['course_title'] ?? '-') ?></span><br>
                            <span>Program: <?= esc($item['program_title'] ?? '-') ?></span>
                        </div>
                    </div>

                    <!-- 2. Instruktur & Fasilitator Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i> Instruktur &amp; Fasilitator
                        </h5>
                        <?php if (! empty($trainers)): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($trainers as $trn): ?>
                                    <div class="d-flex align-items-center gap-3 p-2 radius-10 bg-light border">
                                        <div class="radius-50 overflow-hidden flex-shrink-0" style="width: 44px; height: 44px; background: #e2e8f0; border-radius: 50%;">
                                            <img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt="<?= esc($trn['name'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">
                                                <a href="<?= route_to('public.learning.trainer.detail', $trn['username']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($trn['name'] ?? $trn['username']) ?>
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                                <span class="text-muted small" style="font-size: 12px;"><?= esc($trn['profile_type'] ?? 'Fasilitator Kelas PBJ') ?></span>
                                                <?php if (! empty($trn['role'])): ?>
                                                    <?php
                                                    $roleBadge = match ($trn['role']) {
                                                        'primary'    => ['text' => 'Utama', 'badge' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                                        'co_trainer' => ['text' => 'Co-Trainer', 'badge' => 'bg-info-subtle text-info border border-info-subtle'],
                                                        'guest'      => ['text' => 'Narasumber', 'badge' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                                                        default      => null,
                                                    };
                                                    ?>
                                                    <?php if ($roleBadge): ?>
                                                        <span class="badge radius-6 small <?= $roleBadge['badge'] ?>" style="font-size: 12px;"><?= $roleBadge['text'] ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-3 radius-10 bg-light border">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; background: #e2e8f0;">
                                        <img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt="Tim Fasilitator" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">Tim Fasilitator PBJ Lampung</h6>
                                        <span class="text-muted small" style="font-size: 12px;">Widyaiswara &amp; Pejabat Fungsional PBJ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Panduan Pendaftaran & Helpdesk Card -->
                    <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                            <i class="fas fa-question-circle text-info me-2"></i> Panduan &amp; Helpdesk
                        </h5>
                        <div class="d-flex flex-column gap-3" style="font-size: 13px;">
                            <div>
                                <strong class="d-block text-dark mb-1">Apakah pendaftaran langsung aktif?</strong>
                                <p class="text-muted mb-0">Pengajuan pendaftaran akan ditinjau oleh operator pelatihan untuk memastikan ketersediaan kuota sebelum diaktivasi.</p>
                            </div>
                            <div class="pt-2 border-top">
                                <strong class="d-block text-dark mb-1">Kapan akses Workspace dibuka?</strong>
                                <p class="text-muted mb-0">Setelah disetujui, modul dan kuis dapat diakses seketika melalui Workspace Pembelajaran Anda.</p>
                            </div>
                            <div class="pt-2 border-top text-center">
                                <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary radius-8 w-100">
                                    <i class="fas fa-headset me-1"></i> Hubungi Helpdesk BegawiPengadaan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php elseif ($catalogType === 'trainers'):
    $classes       = $classes ?? $item['detailedClasses'] ?? [];
    $coursesTaught = $coursesTaught ?? $item['coursesTaught'] ?? [];
    $totalStudents = (int) ($totalStudents ?? $item['total_students'] ?? 0);
    $otherTrainers = $otherTrainers ?? [];
    $trainerName   = $item['name'] ?? $item['title'] ?? $itemTitle;

    $profType = strtolower((string) ($item['profile_type'] ?? 'public'));
    $profBadge = match ($profType) {
        'asn'     => ['label' => 'ASN PBJ Lampung / LPSE', 'badge' => 'bg-info-subtle text-info border border-info-subtle', 'hero_badge' => 'bg-info text-white', 'icon' => 'fa-landmark'],
        'public'  => ['label' => 'Praktisi & Fasilitator PBJ', 'badge' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'hero_badge' => 'bg-warning text-dark', 'icon' => 'fa-user-tie'],
        default   => ['label' => ucfirst($profType), 'badge' => 'bg-primary-subtle text-primary border border-primary-subtle', 'hero_badge' => 'bg-light text-dark', 'icon' => 'fa-chalkboard-teacher'],
    };

    $avatarIdx = (((int) ($item['id'] ?? 1)) % 5) + 1;
    $avatarImg = base_url("assets/main/images/resources/course-list-client-img-{$avatarIdx}.jpg");
?>

    <!-- =========================================================================
     TRAINER DETAIL — Clean Magazine Benchmark
     ========================================================================= -->
    <section class="berita-main-section evt-main-section pt-2 pt-md-3 pt-lg-4 pb-5" style="background: #fafbfc; min-height: 75vh;">
        <div class="container">
            <!-- Compact Breadcrumb Navigation -->
            <div class="arena-breadcrumb evt-breadcrumb mb-3">
                <a href="<?= site_url('/') ?>"><iconify-icon icon="solar:home-2-bold" class="align-middle"></iconify-icon> Beranda</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a>
                <span class="sep">/</span>
                <a href="<?= site_url('arena-belajar/pengajar') ?>">Instruktur &amp; Pengajar</a>
                <span class="sep">/</span>
                <span class="current text-truncate"><?= esc($trainerName) ?></span>
            </div>

            <!-- Compact Trainer Detail Hero Banner (arena-hero class preserved for test) -->
            <div class="arena-hero evt-detail-hero radius-16 p-3 p-md-4 mb-4 text-white position-relative" style="background: linear-gradient(135deg, #052244 0%, #0a3d74 100%); overflow: hidden;">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <!-- Badges Row -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 radius-8 small text-uppercase" style="letter-spacing: 0.5px; font-size: 11.5px;">
                                <i class="fas fa-user-tie me-1"></i> Instruktur &amp; Pengajar PBJ
                            </span>
                            <span class="badge bg-success text-white px-2 py-1 radius-8 small" style="font-size: 11.5px;">
                                <i class="fas fa-check-circle me-1"></i> Fasilitator Terverifikasi
                            </span>
                            <span class="badge <?= $profBadge['hero_badge'] ?> px-2 py-1 radius-8 small" style="font-size: 11.5px;">
                                <i class="fas <?= $profBadge['icon'] ?> me-1"></i> <?= esc($profBadge['label']) ?>
                            </span>
                            <?php if (! empty($item['username'])): ?>
                                <span class="badge bg-white text-dark fw-bold font-monospace px-2 py-1 radius-8 shadow-sm" style="font-size: 11.5px;">
                                    @<?= esc($item['username']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Title & Identity -->
                        <h1 class="text-white mb-2" style="font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; line-height: 1.3;">
                            <?= esc($trainerName) ?>
                        </h1>

                        <p class="mb-3 text-white-90" style="font-size: 14px; line-height: 1.55; max-width: 680px; color: rgba(255,255,255,0.9);">
                            Fasilitator dan pengajar resmi Pengadaan Barang/Jasa Pemerintah pada platform BegawiPengadaan. Memandu kelas pembelajaran berstandar kompetensi LKPP dengan fokus pada pemahaman regulasi, studi kasus praktis, dan akuntabilitas pengadaan.
                        </p>

                        <!-- Action CTAs inside Hero -->
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="#kelas-diampu" class="btn btn-primary fw-bold btn-sm px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Kelas yang Diampu (<?= count($classes) ?>)</span>
                            </a>
                            <a href="#profil-kompetensi" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8 d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <i class="fas fa-user-check"></i>
                                <span>Profil &amp; Keahlian</span>
                            </a>
                            <a href="<?= site_url('arena-belajar/pengajar') ?>" class="btn btn-outline-light btn-sm fw-semibold px-3 py-1 radius-8" style="font-size: 12.5px;">
                                <i class="fas fa-arrow-left me-1"></i> Semua Pengajar
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <!-- Glass Quick Stat Card in Hero -->
                        <div class="p-3 radius-12 text-start" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom text-white" style="border-color: rgba(255,255,255,0.15) !important;">
                                <div style="width: 46px; height: 46px; border-radius: 50%; overflow: hidden; border: 2px solid #ffffff; flex-shrink: 0;">
                                    <img src="<?= $avatarImg ?>" alt="<?= esc($trainerName) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="text-truncate">
                                    <span class="badge bg-success text-white px-2 py-0 small mb-1" style="font-size: 10px;">FASILITATOR AKTIF</span>
                                    <h6 class="fw-bold mb-0 text-white text-truncate small" style="max-width: 180px;"><?= esc($trainerName) ?></h6>
                                    <small class="text-white-50" style="font-size: 11px;"><?= esc($profBadge['label']) ?></small>
                                </div>
                            </div>

                            <span class="text-white-50 small fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px; font-size: 11px;">Statistik Instruktur:</span>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-chalkboard-teacher me-1 text-warning"></i> Batch Kelas</span>
                                <span class="fw-bold fs-6"><?= count($classes) ?> Kelas</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom text-white small" style="border-color: rgba(255,255,255,0.15) !important;">
                                <span><i class="fas fa-book-open me-1 text-info"></i> Course Difasilitasi</span>
                                <span class="fw-bold fs-6"><?= count($coursesTaught) ?> Course</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-white small">
                                <span><i class="fas fa-users me-1 text-success"></i> Peserta Terlatih</span>
                                <span class="fw-bold fs-6 text-warning"><?= number_format($totalStudents) ?> orang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Nav Anchor Bar -->
            <div class="card border-0 shadow-xs radius-12 p-2 mb-4 bg-white d-flex flex-row flex-wrap gap-2">
                <a href="#profil-kompetensi" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-id-card me-1 text-primary"></i> Profil &amp; Kompetensi
                </a>
                <a href="#kelas-diampu" class="btn btn-sm btn-primary radius-8 px-3 fw-semibold text-white">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Kelas yang Diampu (<?= count($classes) ?>)
                </a>
                <a href="#course-terkait" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-book-open me-1 text-info"></i> Materi Course (<?= count($coursesTaught) ?>)
                </a>
                <a href="#metode-bimbingan" class="btn btn-sm btn-light radius-8 px-3 fw-semibold">
                    <i class="fas fa-graduation-cap me-1 text-success"></i> Metode Bimbingan
                </a>
            </div>

            <div class="row g-4">
                <!-- Left Main Column (col-xl-8 col-lg-7) -->
                <div class="col-xl-8 col-lg-7">
                    <!-- 1. Profil & Kompetensi Keahlian Card -->
                    <div id="profil-kompetensi" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 20px;">
                                <i class="fas fa-id-card text-primary me-2"></i> Profil Profesional &amp; Bidang Keahlian
                            </h4>
                            <span class="badge <?= $profBadge['badge'] ?> px-3 py-1 radius-20 small">
                                <i class="fas <?= $profBadge['icon'] ?> me-1"></i> <?= esc($profBadge['label']) ?>
                            </span>
                        </div>

                        <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 15px;">
                            <strong><?= esc($trainerName) ?></strong> merupakan praktisi dan fasilitator terverifikasi dalam ekosistem pembelajaran Pengadaan Barang/Jasa Pemerintah (PBJ). Berpengalaman dalam mendampingi aparatur sipil negara, pokja pemilihan, pejabat pengadaan, dan pelaku usaha dalam penerapan tata kelola pengadaan yang tertib, efektif, dan bebas benturan kepentingan.
                        </p>

                        <!-- 4 Pilar Keahlian PBJ Grid -->
                        <h5 class="fw-bold text-dark mb-3" style="font-size: 17px;">
                            <i class="fas fa-medal text-warning me-2"></i> 4 Pilar Keahlian &amp; Fokus Pelatihan
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-primary text-white" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">1</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 15.5px;">Regulasi &amp; Kebijakan PBJ</h6>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Pemahaman mendalam Perpres No. 16/2018 jo. No. 12/2021, mitigasi risiko sengketa, prinsip integritas, dan persiapan pengawasan APIP/BPKP.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-warning text-dark" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">2</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 15.5px;">Perencanaan &amp; Penyusunan HPS</h6>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Identifikasi kebutuhan organisasi, riset harga pasar wajar, perumusan spesifikasi teknis/KAK, dan kalkulasi HPS yang akuntabel.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-success text-white" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">3</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 15.5px;">Pemilihan Penyedia &amp; E-Katalog</h6>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Pelaksanaan tender dan seleksi, e-purchasing LKPP, mini kompetisi katalog elektronik, evaluasi kualifikasi penawaran, dan negosiasi.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge fw-bold bg-info text-white" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">4</span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 15.5px;">Pengendalian Kontrak &amp; BAST</h6>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Penyusunan naskah kontrak (SPK/surat perjanjian), monitoring progres fisik, penanganan adendum/perubahan kontrak, dan berita acara serah terima.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Verified Badge Banner -->
                        <div class="p-3 radius-12 border d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0 !important;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06); flex-shrink: 0;">
                                <i class="fas fa-shield-alt text-success fs-5"></i>
                            </div>
                            <div>
                                <strong class="text-success-emphasis d-block" style="font-size: 13.5px;">Fasilitator Terdaftar &amp; Teruji BegawiPengadaan</strong>
                                <small class="text-secondary" style="font-size: 12px;">Profil pengajar telah diverifikasi administratif dan kompetensinya oleh tim koordinator pelatihan BegawiPengadaan.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Kelas & Batch yang Diampu Card -->
                    <div id="kelas-diampu" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <h4 class="fw-bold text-dark mb-1" style="font-size: 20px;">
                                    <i class="fas fa-chalkboard-teacher text-success me-2"></i> Kelas &amp; Batch yang Diampu
                                </h4>
                                <p class="text-muted small mb-0">Daftar kelas pembelajaran aktif dan terjadwal yang dibimbing langsung.</p>
                            </div>
                            <span class="badge bg-primary text-white px-3 py-2 radius-20 fw-bold">
                                <?= count($classes) ?> Batch Kelas
                            </span>
                        </div>

                        <?php if ($classes === []): ?>
                            <div class="p-4 text-center radius-12 bg-light border">
                                <i class="fas fa-calendar-times text-muted fs-1 mb-2"></i>
                                <h6 class="fw-bold text-dark">Belum Ada Batch Kelas Aktif</h6>
                                <p class="text-muted small mb-3">Saat ini belum ada batch kelas terbuka yang dijadwalkan untuk pengajar ini.</p>
                                <a href="<?= site_url('arena-belajar/kelas') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3">
                                    Eksplorasi Semua Kelas
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($classes as $cls): ?>
                                    <?php
                                    $clsStatus = strtolower((string) ($cls['status'] ?? 'scheduled'));
                                    $clsStatusBadge = match ($clsStatus) {
                                        'active'    => ['label' => 'Sedang Berjalan (Aktif)', 'badge' => 'bg-success text-white', 'icon' => 'fa-play-circle'],
                                        'completed' => ['label' => 'Selesai', 'badge' => 'bg-secondary text-white', 'icon' => 'fa-check-double'],
                                        default     => ['label' => 'Jadwal Terbuka', 'badge' => 'bg-warning text-dark', 'icon' => 'fa-clock'],
                                    };
                                    $startD = ! empty($cls['start_at']) ? date('d M Y', strtotime($cls['start_at'])) : '-';
                                    $endD   = ! empty($cls['end_at']) ? date('d M Y', strtotime($cls['end_at'])) : '-';
                                    $enrolled = (int) ($cls['enrolled_count'] ?? 0);
                                    $capacity = (int) ($cls['capacity'] ?? 0);
                                    $remaining = isset($cls['remaining_quota']) ? (int) $cls['remaining_quota'] : max(0, $capacity - $enrolled);
                                    ?>
                                    <div class="p-3 p-md-4 radius-16 border bg-light transition-all hover-shadow-sm">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="badge bg-white text-dark border font-monospace px-3 py-1 radius-20 fw-bold" style="font-size: 12.5px;">
                                                    <i class="fas fa-hashtag text-primary me-1"></i> Batch: <?= esc($cls['code']) ?>
                                                </span>
                                                <span class="badge <?= $clsStatusBadge['badge'] ?> px-3 py-1 radius-20" style="font-size: 12.5px;">
                                                    <i class="fas <?= $clsStatusBadge['icon'] ?> me-1"></i> <?= esc($clsStatusBadge['label']) ?>
                                                </span>
                                                <?php if (! empty($cls['trainer_role'] ?? $cls['role'])): ?>
                                                    <?php
                                                    $tRole = $cls['trainer_role'] ?? $cls['role'];
                                                    $roleLabel = match ($tRole) {
                                                        'primary'    => ['text' => 'Instruktur Utama', 'badge' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                                        'co_trainer' => ['text' => 'Co-Trainer', 'badge' => 'bg-info-subtle text-info border border-info-subtle'],
                                                        'guest'      => ['text' => 'Narasumber Tamu', 'badge' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                                                        default      => null,
                                                    };
                                                    ?>
                                                    <?php if ($roleLabel): ?>
                                                        <span class="badge radius-20 small <?= $roleLabel['badge'] ?>" style="font-size: 12.5px;">
                                                            <i class="fas fa-user-tie me-1"></i><?= $roleLabel['text'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (! empty($cls['learning_method'])): ?>
                                                    <?php
                                                    $methodLabel = match ($cls['learning_method']) {
                                                        'online'  => ['label' => 'Online LMS', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
                                                        'offline' => ['label' => 'Tatap Muka', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                                        'hybrid'  => ['label' => 'Hybrid', 'class' => 'bg-purple-subtle text-purple border border-purple-subtle', 'style' => 'background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;'],
                                                        default   => null,
                                                    };
                                                    ?>
                                                    <?php if ($methodLabel): ?>
                                                        <span class="badge radius-20 small <?= $methodLabel['class'] ?>" style="font-size: 12.5px; <?= $methodLabel['style'] ?? '' ?>">
                                                            <i class="fas fa-chalkboard me-1"></i><?= $methodLabel['label'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (! empty($cls['program_title'])): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-8 small text-truncate" style="max-width: 200px;">
                                                    <?= esc($cls['program_title']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h5 class="fw-bold mb-1" style="font-size: 17px;">
                                            <a href="<?= route_to('public.learning.class.detail', $cls['code']) ?>" class="text-dark text-decoration-none hover-primary">
                                                <?= esc($cls['name']) ?>
                                            </a>
                                        </h5>

                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-book-open text-primary me-1"></i> Induk Course:
                                            <?php if (! empty($cls['course_slug'])): ?>
                                                <a href="<?= route_to('public.learning.course.detail', $cls['course_slug']) ?>" class="text-secondary fw-semibold text-decoration-none hover-primary"><?= esc($cls['course_title'] ?? '-') ?></a>
                                            <?php else: ?>
                                                <span class="text-secondary fw-semibold"><?= esc($cls['course_title'] ?? '-') ?></span>
                                            <?php endif; ?>
                                        </p>

                                        <!-- Class Info Strip & CTA -->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top" style="border-color: #e2e8f0 !important;">
                                            <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small" style="font-size: 12.5px;">
                                                <span>
                                                    <i class="far fa-calendar-alt text-primary me-1"></i> <?= $startD ?> - <?= $endD ?>
                                                </span>
                                                <span>&middot;</span>
                                                <span>
                                                    <i class="fas fa-users text-info me-1"></i> <?= $enrolled ?> / <?= $capacity ?> Peserta
                                                </span>
                                                <?php if ($remaining > 0 && $clsStatus !== 'completed'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        Sisa <?= $remaining ?> Kursi
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (! empty($cls['location_or_link'])): ?>
                                                    <span>&middot;</span>
                                                    <span title="<?= esc($cls['location_or_link']) ?>">
                                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= esc(character_limiter($cls['location_or_link'], 28)) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <a href="<?= route_to('public.learning.class.detail', $cls['code']) ?>" class="btn btn-sm btn-primary radius-8 px-3 fw-bold d-inline-flex align-items-center gap-1">
                                                <span>Detail Kelas</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Materi & Course Terkait Card -->
                    <?php if ($coursesTaught !== []): ?>
                        <div id="course-terkait" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1" style="font-size: 20px;">
                                        <i class="fas fa-book-open text-info me-2"></i> Induk Course yang Difasilitasi
                                    </h4>
                                    <p class="text-muted small mb-0">Kurikulum materi PBJ yang dibawakan dalam batch pelatihan.</p>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-1 radius-20 small">
                                    <?= count($coursesTaught) ?> Course
                                </span>
                            </div>

                            <div class="row g-3">
                                <?php foreach ($coursesTaught as $crs): ?>
                                    <?php
                                    $crsLevel = strtolower((string) ($crs['level'] ?? 'basic'));
                                    $levelBadge = match ($crsLevel) {
                                        'intermediate' => ['label' => 'Menengah', 'badge' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                                        'advanced'     => ['label' => 'Lanjutan', 'badge' => 'bg-danger-subtle text-danger border border-danger-subtle'],
                                        default        => ['label' => 'Dasar (Basic)', 'badge' => 'bg-info-subtle text-info border border-info-subtle'],
                                    };
                                    ?>
                                    <div class="col-md-6">
                                        <div class="p-3 radius-12 border bg-light h-100 d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <span class="badge <?= $levelBadge['badge'] ?> px-2 py-1 radius-12 small" style="font-size: 12.5px;">
                                                    <?= esc($levelBadge['label']) ?>
                                                </span>
                                                <span class="badge bg-white text-muted border small" style="font-size: 12.5px;">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i> <?= (int) ($crs['class_count'] ?? 1) ?> Batch
                                                </span>
                                            </div>

                                            <h6 class="fw-bold mb-2" style="font-size: 15px; line-height: 1.4;">
                                                <a href="<?= route_to('public.learning.course.detail', $crs['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= esc($crs['title']) ?>
                                                </a>
                                            </h6>

                                            <?php if (! empty($crs['program_title'])): ?>
                                                <small class="text-muted mb-3 d-block">
                                                    <i class="fas fa-graduation-cap text-primary me-1"></i> <?= esc($crs['program_title']) ?>
                                                </small>
                                            <?php endif; ?>

                                            <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i> <?= (int) ($crs['duration'] ?? 120) ?> Menit
                                                </small>
                                                <a href="<?= route_to('public.learning.course.detail', $crs['slug']) ?>" class="btn btn-sm btn-outline-primary radius-8 px-2 py-1 fw-bold" style="font-size: 12px;">
                                                    <span>Buka Course</span>
                                                    <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 4. Metode & Standar Bimbingan Card -->
                    <div id="metode-bimbingan" class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                        <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 20px;">
                            <i class="fas fa-graduation-cap text-success me-2"></i> Metode &amp; Komitmen Pengajaran
                        </h4>
                        <p class="text-muted small mb-4">
                            Prinsip fasilitasi yang diterapkan instruktur untuk memastikan penyerapan materi maksimal dan aplikatif:
                        </p>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border bg-light h-100 text-center">
                                    <div class="mx-auto mb-2 text-primary" style="font-size: 28px;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">Studi Kasus Kontekstual</h6>
                                    <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.55;">
                                        Pembahasan contoh riil proses pemilihan, sanggah, dan kendala lapangan PBJ Pemda Lampung &amp; instansi pemerintah.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border bg-light h-100 text-center">
                                    <div class="mx-auto mb-2 text-warning" style="font-size: 28px;">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">Feedback Lembar Kerja</h6>
                                    <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.55;">
                                        Pemeriksaan teliti terhadap tugas penyusunan HPS, RUP, dan draf kontrak yang diunggah peserta di portal LMS.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 radius-12 border bg-light h-100 text-center">
                                    <div class="mx-auto mb-2 text-info" style="font-size: 28px;">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">Forum Diskusi Aktif</h6>
                                    <p class="text-muted small mb-0" style="font-size: 13.5px; line-height: 1.55;">
                                        Ruang interaksi responsif melalui forum LMS dan sesi tatap maya untuk memecahkan keraguan regulasi peserta.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar Column (Sticky) (col-xl-4 col-lg-5) -->
                <div class="col-xl-4 col-lg-5">
                    <div class="sticky-top" style="top: 110px; z-index: 10;">
                        <!-- 1. Trainer Profile Highlight Card -->
                        <div class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white text-center">
                            <div class="mx-auto position-relative mb-3" style="width: 100px; height: 100px;">
                                <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 4px solid #ffffff; box-shadow: 0 6px 18px rgba(12,90,166,0.18);">
                                    <img src="<?= $avatarImg ?>" alt="<?= esc($trainerName) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <span class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center border border-white" style="width: 28px; height: 28px; font-size: 12px;" title="Terverifikasi">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>

                            <h4 class="fw-bold text-dark mb-1" style="font-size: 18px;">
                                <?= esc($trainerName) ?>
                            </h4>
                            <span class="badge <?= $profBadge['badge'] ?> px-3 py-1 radius-20 small mb-3">
                                <i class="fas <?= $profBadge['icon'] ?> me-1"></i> <?= esc($profBadge['label']) ?>
                            </span>

                            <!-- Stats Bar in Sidebar -->
                            <div class="d-flex align-items-center justify-content-around py-2 mb-4 radius-12 bg-light border text-center" style="font-size: 13.5px;">
                                <div>
                                    <strong class="d-block text-primary fs-6"><?= count($classes) ?></strong>
                                    <span class="text-muted small">Batch Kelas</span>
                                </div>
                                <div class="border-end" style="height: 24px;"></div>
                                <div>
                                    <strong class="d-block text-warning-emphasis fs-6"><?= count($coursesTaught) ?></strong>
                                    <span class="text-muted small">Course PBJ</span>
                                </div>
                                <div class="border-end" style="height: 24px;"></div>
                                <div>
                                    <strong class="d-block text-success fs-6"><?= number_format($totalStudents) ?></strong>
                                    <span class="text-muted small">Peserta</span>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="d-flex flex-column gap-2 mb-2">
                                <?php if ($classes !== []): ?>
                                    <a href="#kelas-diampu" class="btn btn-primary fw-bold radius-8 py-2 w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Pilih Kelas Pengajar Ini</span>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= site_url('arena-belajar/pengajar') ?>" class="btn btn-outline-secondary fw-semibold radius-8 py-2 w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-users"></i>
                                    <span>Lihat Semua Pengajar</span>
                                </a>
                            </div>
                        </div>

                        <!-- 2. Other Trainers Recommendations Card -->
                        <?php if (! empty($otherTrainers)): ?>
                            <div class="card border-0 shadow-sm radius-16 p-4 mb-4 bg-white">
                                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 16px;">
                                    <i class="fas fa-user-friends text-primary me-2"></i> Rekomendasi Pengajar Lainnya
                                </h5>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($otherTrainers as $idx => $ot): ?>
                                        <?php
                                        $otImgIdx = (((int) ($ot['id'] ?? $idx)) % 5) + 1;
                                        $otImg = base_url("assets/main/images/resources/course-list-client-img-{$otImgIdx}.jpg");
                                        $otSlug = ! empty($ot['username']) ? $ot['username'] : (string) $ot['id'];
                                        $otName = $ot['name'] ?: $ot['username'];
                                        $otProf = ($ot['profile_type'] ?? '') === 'asn' ? 'ASN Pemerintah' : 'Praktisi PBJ';
                                        ?>
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 42px; height: 42px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; flex-shrink: 0;">
                                                    <img src="<?= $otImg ?>" alt="<?= esc($otName) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0" style="font-size: 13.5px;">
                                                        <a href="<?= route_to('public.learning.trainer.detail', $otSlug) ?>" class="text-dark text-decoration-none hover-primary">
                                                            <?= esc($otName) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted" style="font-size: 13px;"><?= esc($otProf) ?> &middot; <?= (int) ($ot['total_classes'] ?? 0) ?> Kelas</small>
                                                </div>
                                            </div>
                                            <a href="<?= route_to('public.learning.trainer.detail', $otSlug) ?>" class="btn btn-sm btn-outline-primary radius-8 px-2 py-1" style="font-size: 12.5px;">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Helpdesk & Konsultasi Card -->
                        <div class="card border-0 shadow-sm radius-16 p-4 bg-white">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-headset text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Bantuan &amp; Konsultasi</h6>
                                    <small class="text-muted">Layanan Pelatihan PBJ</small>
                                </div>
                            </div>
                            <p class="text-secondary small mb-3" style="font-size: 13.5px; line-height: 1.6;">
                                Ingin berkonsultasi mengenai pemilihan kelas atau jadwal pendampingan khusus instansi Anda?
                            </p>
                            <a href="<?= site_url('kontak') ?>" class="btn btn-sm btn-outline-primary radius-8 w-100 fw-semibold">
                                <i class="fas fa-envelope me-1"></i> Hubungi Tim Helpdesk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- Fallback Detail -->
    <section class="about-three learning-detail py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="p-4 bg-white radius-16 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <span class="arena-section-head__tag m-0">Arena Belajar BegawiPengadaan</span>
                            <?php
                            $bmType = match ($catalogType) {
                                'programs' => 'program',
                                'courses'  => 'course',
                                'classes'  => 'learning_class',
                                default    => null,
                            };
                            ?>
                            <?php if ($bmType !== null && ! empty($item['id'])): ?>
                                <?= view('landing/components/bookmark_button', [
                                    'type'  => $bmType,
                                    'id'    => (int) $item['id'],
                                    'title' => $itemTitle,
                                    'size'  => 'md',
                                ]) ?>
                            <?php endif; ?>
                        </div>
                        <h2 class="fw-bold mb-3"><?= esc($itemTitle) ?></h2>
                        <p class="lead mb-4"><?= esc($item['summary'] ?? '') ?></p>
                        <ul class="list-unstyled">
                            <li class="d-flex gap-2 mb-2"><i class="fas fa-check text-success mt-1"></i>
                                <div><strong>Tujuan pembelajaran:</strong> Memahami konsep dan tahapan relevan.</div>
                            </li>
                            <li class="d-flex gap-2 mb-2"><i class="fas fa-check text-success mt-1"></i>
                                <div><strong>Materi utama:</strong> Regulasi, studi kasus, dan latihan reflektif.</div>
                            </li>
                            <li class="d-flex gap-2"><i class="fas fa-check text-success mt-1"></i>
                                <div><strong>Hasil:</strong> Mampu menerapkan pengetahuan secara tertib dan transparan.</div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="p-4 bg-white radius-16 border shadow-sm">
                        <h5 class="fw-bold mb-3 pb-2 border-bottom">Ringkasan Materi</h5>
                        <p class="mb-1 text-muted small">Program</p>
                        <p class="fw-semibold mb-3"><?= esc($item['program_title'] ?? '-') ?></p>
                        <p class="mb-1 text-muted small">Level</p>
                        <p class="fw-semibold mb-3"><?= esc($item['level'] ?? '-') ?></p>
                        <p class="mb-1 text-muted small">Status</p>
                        <p class="fw-semibold mb-0"><?= esc($item['status'] ?? 'published') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>