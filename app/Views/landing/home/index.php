<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-home.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$stats          = $stats ?? ['total_regulations' => 0, 'total_courses' => 0, 'total_classes' => 0, 'total_knowledge' => 0, 'total_downloads' => 0, 'total_users' => 0];
$courses        = $courses ?? [];
$regulations    = $regulations ?? [];
$articles       = $articles ?? [];
$templates      = $templates ?? [];
$caseStudies    = $caseStudies ?? [];
$faqs           = $faqs ?? [];
$mediaItems     = $mediaItems ?? [];
$stages         = $stages ?? [];
$upcomingEvents = $upcomingEvents ?? [];
$visitorStats   = $visitorStats ?? [
    'home' => [
        'today'      => 0,
        'this_week'  => 0,
        'this_month' => 0,
        'this_year'  => 0,
        'total'      => 0,
    ],
    'features' => [],
    'total_feature_hits' => 0,
];

// Indonesian month mapping helper
$bulanIndo = [
    1 => 'Jan',
    2 => 'Feb',
    3 => 'Mar',
    4 => 'Apr',
    5 => 'Mei',
    6 => 'Jun',
    7 => 'Jul',
    8 => 'Agu',
    9 => 'Sep',
    10 => 'Okt',
    11 => 'Nov',
    12 => 'Des'
];
?>

<!-- =========================================================
     1. HEADER HERO SLIDER PALING ATAS (FULL GAMBAR & VARIASI KOMPOSISI)
     ========================================================= -->
<section class="main-slider">
    <div class="main-slider__carousel owl-carousel owl-theme">
        <!-- Slide 1: Transformasi & Ekosistem PBJ Lampung -->
        <div class="item">
            <div class="main-slider__image-block">
                <img src="<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>" alt="BegawiPengadaan Lampung - Transformasi Digital Pengadaan" class="main-slider__hero-img" width="1920" height="500" />
            </div>
            <div class="main-slider__caption-block">
                <div class="container">
                    <div class="main-slider__caption-inner">
                        <div class="main-slider__caption-text">
                            <div class="main-slider__sub-title-box">
                                <div class="main-slider__sub-title-shape"></div>
                                <h5 class="main-slider__sub-title">Pemerintah Provinsi Lampung &middot; Biro PBJ Setda</h5>
                            </div>
                            <h2 class="main-slider__title">
                                Transformasi Digital Terpadu <span>Pengadaan Barang &amp; Jasa</span>
                            </h2>
                            <p class="main-slider__text">
                                Integrasi Arena Belajar aparatur, repositori dokumen Bunga Rampai, dan pusat regulasi PBJ terpadu.
                            </p>
                        </div>
                        <div class="main-slider__btn-box">
                            <a href="<?= site_url('regulasi') ?>" class="thm-btn">
                                <span class="icon-angles-right"></span>Pusat Regulasi PBJ
                            </a>
                            <a href="<?= site_url('arena-belajar') ?>" class="thm-btn thm-btn-alt">
                                <span class="icon-angles-right"></span>Arena Belajar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2: Arena Belajar PBJ -->
        <div class="item">
            <div class="main-slider__image-block">
                <img src="<?= base_url('assets/main/images/backgrounds/slider-1-2.png') ?>" alt="BegawiPengadaan Lampung - Arena Belajar" class="main-slider__hero-img" width="1920" height="500" />
            </div>
            <div class="main-slider__caption-block">
                <div class="container">
                    <div class="main-slider__caption-inner">
                        <div class="main-slider__caption-text">
                            <div class="main-slider__sub-title-box">
                                <div class="main-slider__sub-title-shape"></div>
                                <h5 class="main-slider__sub-title">Arena Belajar &middot; Sertifikasi Kompetensi PBJ</h5>
                            </div>
                            <h2 class="main-slider__title">
                                Tingkatkan Keahlian PBJ <span>Bersama Instruktur Ahli LKPP</span>
                            </h2>
                            <p class="main-slider__text">
                                Modul berjenjang terakreditasi LKPP, pembelajaran terjadwal, dan sertifikat resmi Pemprov Lampung.
                            </p>
                        </div>
                        <div class="main-slider__btn-box">
                            <a href="<?= site_url('arena-belajar/course') ?>" class="thm-btn">
                                <span class="icon-angles-right"></span>Katalog Kursus
                            </a>
                            <a href="<?= site_url('arena-belajar/program') ?>" class="thm-btn thm-btn-alt">
                                <span class="icon-angles-right"></span>Sertifikasi PBJ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3: Bunga Rampai & Regulasi -->
        <div class="item">
            <div class="main-slider__image-block">
                <img src="<?= base_url('assets/main/images/backgrounds/slider-1-3.png') ?>" alt="BegawiPengadaan Lampung - Bunga Rampai &amp; Regulasi" class="main-slider__hero-img" width="1920" height="500" />
            </div>
            <div class="main-slider__caption-block">
                <div class="container">
                    <div class="main-slider__caption-inner">
                        <div class="main-slider__caption-text">
                            <div class="main-slider__sub-title-box">
                                <div class="main-slider__sub-title-shape"></div>
                                <h5 class="main-slider__sub-title">Bunga Rampai &middot; Dokumen &amp; Praktik Baik</h5>
                            </div>
                            <h2 class="main-slider__title">
                                Template Dokumen KAK, HPS <span>&amp; Mitigasi Risiko Audit Pengadaan</span>
                            </h2>
                            <p class="main-slider__text">
                                Unduh format dokumen kerja siap pakai (DOCX/XLSX), telaah sengketa sanggahan, dan alur tata kelola PBJ.
                            </p>
                        </div>
                        <div class="main-slider__btn-box">
                            <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="thm-btn">
                                <span class="icon-angles-right"></span>Template Dokumen
                            </a>
                            <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="thm-btn thm-btn-alt">
                                <span class="icon-angles-right"></span>Studi Kasus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     2. GATEWAY PENCARIAN TERPADU & LIVE KPI COUNTER
     ========================================================= -->
<section class="portal-gateway-section">
    <div class="container">
        <div class="portal-gateway-card">
            <!-- Search Form -->
            <form action="<?= site_url('regulasi') ?>" method="get" id="heroSearchForm" class="hero-search-box">
                <iconify-icon icon="solar:magnifer-linear" class="text-slate-400 text-xl"></iconify-icon>
                <input type="text" name="q" id="heroSearchInput" placeholder="Cari regulasi perpres, materi kursus, template KAK/HPS, atau artikel..." required>
                <!-- Class 'ignore' prevents niceSelect plugin from duplicating this dropdown -->
                <select id="heroSearchTarget" class="ignore hero-search-target-select">
                    <option value="regulasi">Regulasi PBJ</option>
                    <option value="arena-belajar/course">Arena Belajar</option>
                    <option value="bunga-rampai/artikel">Bunga Rampai</option>
                    <option value="event">Event &amp; Agenda</option>
                    <option value="verifikasi-sertifikat">Cek Sertifikat</option>
                </select>
                <button type="submit" class="btn-search">
                    <span>Cari</span>
                    <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                </button>
            </form>

            <!-- Quick Keyword Tags -->
            <div class="hero-quick-tags mb-3">
                <span class="text-dark fw-bold">Pencarian Cepat:</span>
                <a href="<?= site_url('regulasi?q=Perpres+12+Tahun+2021') ?>">Perpres 12/2021</a>
                <a href="<?= site_url('regulasi?q=Peraturan+LKPP') ?>">Peraturan LKPP</a>
                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>">Template KAK &amp; HPS</a>
                <a href="<?= site_url('arena-belajar/course') ?>">Course PBJ</a>
                <a href="<?= site_url('event') ?>">Agenda Bimtek &amp; Webinar</a>
                <a href="<?= site_url('verifikasi-sertifikat') ?>">Verifikasi Sertifikat</a>
            </div>

            <!-- Live KPI Counter Ribbon (4 Cards) -->
            <div class="row g-3 pt-3 border-top">
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon text-warning bg-warning-subtle">
                            <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gateway-kpi-num"><?= number_format($stats['total_regulations'] > 0 ? $stats['total_regulations'] : 24) ?></div>
                            <div class="gateway-kpi-label">Regulasi PBJ Terbit</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon text-info bg-info-subtle">
                            <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gateway-kpi-num"><?= number_format($stats['total_courses'] > 0 ? $stats['total_courses'] : 18) ?>+</div>
                            <div class="gateway-kpi-label">Kursus &amp; Materi Belajar</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon text-success bg-success-subtle">
                            <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gateway-kpi-num"><?= number_format($stats['total_knowledge'] > 0 ? $stats['total_knowledge'] : 45) ?>+</div>
                            <div class="gateway-kpi-label">Artikel &amp; Template Dokumen</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon text-primary bg-primary-subtle">
                            <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gateway-kpi-num"><?= number_format($stats['total_users'] > 0 ? $stats['total_users'] : 150) ?>+</div>
                            <div class="gateway-kpi-label">Aparatur &amp; Peserta Terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     3. PILAR EKOSISTEM BEGAWIPENGADAAN (THE 5 CORE PILLARS)
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:layers-bold"></iconify-icon>
                <span>Pilar Ekosistem BegawiPengadaan</span>
            </div>
            <h2 class="section-title-main">Satu Pintu Layanan Digital Pengadaan Barang/Jasa</h2>
            <p class="section-title-desc mx-auto">Dirancang untuk memenuhi seluruh siklus kebutuhan Pejabat Pembuat Komitmen (PPK), Pokja Pemilihan, Pejabat Pengadaan, Penyedia Barang/Jasa, dan Auditor di Provinsi Lampung.</p>
        </div>

        <div class="row g-3 g-md-4">
            <!-- Pilar 1: Arena Belajar -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pillar-card" style="--pillar-accent: #0c5aa6; --pillar-bg: #eff6ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                    </div>
                    <span class="pillar-badge">E-Learning &amp; Sertifikasi</span>
                    <h3 class="pillar-title">Arena Belajar</h3>
                    <p class="pillar-desc">Pusat pelatihan mandiri berbasis e-learning, program sertifikasi PBJ, modul berjenjang, dan kelas interaktif bersama instruktur terakreditasi LKPP.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat"><strong><?= max(18, $stats['total_courses'] ?? 0) ?></strong> Kursus</span>
                        <span class="pillar-stat"><strong>4</strong> Program</span>
                    </div>
                    <a href="<?= site_url('arena-belajar') ?>" class="pillar-link">
                        <span>Eksplorasi Pembelajaran</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 2: Bunga Rampai -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pillar-card" style="--pillar-accent: #10b981; --pillar-bg: #ecfdf5;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                    </div>
                    <span class="pillar-badge">Dokumen &amp; Studi Kasus</span>
                    <h3 class="pillar-title">Bunga Rampai PBJ</h3>
                    <p class="pillar-desc">Ensiklopedia literasi praktis: artikel wawasan, template dokumen kerja (KAK, HPS, Kontrak), studi kasus penanganan sanggah, dan galeri multimedia.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat"><strong><?= max(45, $stats['total_knowledge'] ?? 0) ?></strong> Artikel &amp; Template</span>
                        <span class="pillar-stat"><strong>5</strong> Topik</span>
                    </div>
                    <a href="<?= site_url('bunga-rampai') ?>" class="pillar-link">
                        <span>Buka Bunga Rampai</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 3: Regulasi PBJ -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pillar-card" style="--pillar-accent: #f59e0b; --pillar-bg: #fffbeb;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:scale-bold"></iconify-icon>
                    </div>
                    <span class="pillar-badge">Produk Hukum Resmi</span>
                    <h3 class="pillar-title">Regulasi &amp; Payung Hukum</h3>
                    <p class="pillar-desc">Koleksi terstruktur peraturan perundang-undangan (Perpres, PerLKPP, Perda Lampung, Surat Edaran), riwayat naskah versi, dan silsilah relasi aturan hukum.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat"><strong><?= max(24, $stats['total_regulations'] ?? 0) ?></strong> Regulasi</span>
                        <span class="pillar-stat"><strong>5</strong> Kategori</span>
                    </div>
                    <a href="<?= site_url('regulasi') ?>" class="pillar-link">
                        <span>Pusat Regulasi PBJ</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 4: Klinik Konsultasi & FAQ -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="pillar-card" style="--pillar-accent: #8b5cf6; --pillar-bg: #f5f3ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                    </div>
                    <span class="pillar-badge">Helpdesk &amp; Mitigasi</span>
                    <h3 class="pillar-title">Klinik Konsultasi &amp; Tanya Jawab</h3>
                    <p class="pillar-desc">Bimbingan teknis terarah dan tanya jawab spesifik mengenai persoalan regulasi, kendala pemilihan penyedia, penyesuaian harga, serta tata cara mitigasi risiko audit kepengadaan.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat"><strong><?= max(5, count($faqs)) ?></strong> FAQ Aktif</span>
                        <span class="pillar-stat"><strong>24/7</strong> Helpdesk</span>
                    </div>
                    <a href="<?= site_url('bunga-rampai/faq') ?>" class="pillar-link">
                        <span>Lihat FAQ &amp; Bimbingan</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 5: Verifikasi Sertifikat Digital -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="pillar-card" style="--pillar-accent: #0284c7; --pillar-bg: #f0f9ff;">
                    <div class="pillar-icon-box">
                        <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                    </div>
                    <span class="pillar-badge">Validasi QR Code</span>
                    <h3 class="pillar-title">Verifikasi Sertifikat Digital</h3>
                    <p class="pillar-desc">Validasi keabsahan sertifikat pelatihan, bukti tanda lulus, dan kompetensi yang diterbitkan resmi oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat"><strong>QR Code</strong> Validasi</span>
                        <span class="pillar-stat"><strong>SSL</strong> Aman</span>
                    </div>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="pillar-link">
                        <span>Cek Keabsahan Sertifikat</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     4. ARENA BELAJAR: KURSUS & KELAS TERPILIH
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:mortarboard-bold"></iconify-icon>
                    <span>Arena Belajar PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Materi &amp; Kursus Terpopuler</h2>
                <p class="section-title-desc mb-0">Tingkatkan kompetensi Anda melalui kurikulum berjenjang terstandar LKPP dan instruktur ahli.</p>
            </div>
            <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Lihat Seluruh Kursus</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php if (empty($courses)): ?>
                <?php
                $mockCourses = [
                    ['title' => 'Dasar-Dasar Pengadaan Barang/Jasa Pemerintah (Level-1)', 'level' => 'Pemula', 'hours' => '24 JP', 'program' => 'Sertifikasi PBJ', 'slug' => 'dasar-dasar-pengadaan-level-1'],
                    ['title' => 'Teknik Penyusunan HPS & Spesifikasi Teknis yang Akuntabel', 'level' => 'Menengah', 'hours' => '16 JP', 'program' => 'Teknis PPK', 'slug' => 'penyusunan-hps-spesifikasi'],
                    ['title' => 'Tata Kelola Pengadaan Melalui E-Katalog & E-Purchasing V6', 'level' => 'Pemula', 'hours' => '12 JP', 'program' => 'Katalog PBJ', 'slug' => 'tata-kelola-e-katalog'],
                ];
                $ci = 0;
                foreach ($mockCourses as $c): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="course-card-modern">
                            <div class="course-thumb-wrap">
                                <img src="<?= base_url('assets/main/images/resources/courses-1-1.jpg') ?>" alt="<?= esc($c['title']) ?>">
                                <?php if ($ci === 0): ?>
                                    <span class="course-popular-ribbon"><iconify-icon icon="solar:star-bold"></iconify-icon> Populer</span>
                                <?php endif; ?>
                                <span class="course-level-badge"><?= esc($c['level']) ?></span>
                                <span class="course-duration-badge">
                                    <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                    <?= esc($c['hours']) ?>
                                </span>
                                <div class="course-overlay-cta">
                                    <iconify-icon icon="solar:play-circle-bold"></iconify-icon>
                                    <span>Lihat Detail</span>
                                </div>
                            </div>
                            <div class="course-body-modern">
                                <span class="course-program-tag"><?= esc($c['program']) ?></span>
                                <h3 class="course-title-modern">
                                    <a href="<?= site_url('arena-belajar/course') ?>"><?= esc($c['title']) ?></a>
                                </h3>
                                <p class="course-summary-snippet">Panduan komprehensif bagi PPK dan Pokja dalam menerapkan regulasi terbaru.</p>
                                <div class="course-meta-row">
                                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:users-group-rounded-linear"></iconify-icon> 120+ Peserta
                                    </span>
                                    <a href="<?= site_url('arena-belajar/course') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Pelajari</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $ci++; endforeach; ?>
            <?php else: ?>
                <?php $ci = 0; foreach ($courses as $c): ?>
                    <?php
                    $lvl = strtolower((string) ($c['level'] ?? 'basic'));
                    $lvlText = match ($lvl) {
                        'basic', 'pemula' => 'Pemula',
                        'intermediate', 'menengah' => 'Menengah',
                        'advanced', 'lanjutan' => 'Lanjutan',
                        default => 'Semua Tingkat'
                    };
                    $durationJp = 4;
                    if (! empty($c['duration_minutes'])) {
                        $durationJp = max(1, round((int) $c['duration_minutes'] / 45));
                    } elseif (! empty($c['duration_hours'])) {
                        $durationJp = (int) $c['duration_hours'];
                    }
                    $enrolled = (int) ($c['enrollments_count'] ?? 0);
                    $thumbImg = ! empty($c['cover_asset_path'])
                        ? base_url('file/' . (int) ($c['cover_file_id'] ?? 0))
                        : (! empty($c['cover_url']) ? $c['cover_url'] : base_url('assets/main/images/resources/courses-1-1.jpg'));
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="course-card-modern">
                            <div class="course-thumb-wrap">
                                <img src="<?= esc($thumbImg) ?>" alt="<?= esc($c['title']) ?>" onerror="this.src='<?= base_url('assets/main/images/resources/courses-1-1.jpg') ?>'">
                                <?php if ($ci === 0): ?>
                                    <span class="course-popular-ribbon"><iconify-icon icon="solar:star-bold"></iconify-icon> Populer</span>
                                <?php endif; ?>
                                <span class="course-level-badge"><?= esc($lvlText) ?></span>
                                <span class="course-duration-badge">
                                    <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                    <?= $durationJp ?> JP
                                </span>
                                <a href="<?= site_url('arena-belajar/course/' . esc($c['slug'])) ?>" class="course-overlay-cta">
                                    <iconify-icon icon="solar:play-circle-bold"></iconify-icon>
                                    <span>Lihat Detail</span>
                                </a>
                            </div>
                            <div class="course-body-modern">
                                <span class="course-program-tag"><?= esc($c['program_title'] ?? 'Arena Belajar') ?></span>
                                <h3 class="course-title-modern">
                                    <a href="<?= site_url('arena-belajar/course/' . esc($c['slug'])) ?>"><?= esc($c['title']) ?></a>
                                </h3>
                                <p class="course-summary-snippet"><?= esc(! empty($c['summary']) ? $c['summary'] : 'Materi pembelajaran terstruktur berbasis silabus standar kompetensi LKPP.') ?></p>
                                <div class="course-meta-row">
                                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:users-group-rounded-linear"></iconify-icon>
                                        <?= $enrolled > 0 ? $enrolled . ' Peserta' : 'Modul Mandiri' ?>
                                    </span>
                                    <a href="<?= site_url('arena-belajar/course/' . esc($c['slug'])) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Buka Materi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $ci++; endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     5. TAHAPAN SIKLUS PENGADAAN INTERAKTIF (TIMELINE)
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:refresh-circle-bold"></iconify-icon>
                <span>Siklus Pengadaan Barang/Jasa</span>
            </div>
            <h2 class="section-title-main">Panduan Alur Kerja dari Perencanaan hingga Serah Terima</h2>
            <p class="section-title-desc mx-auto">Telusuri regulasi, standar dokumen, dan materi pembelajaran yang spesifik disesuaikan dengan setiap tahapan siklus PBJ.</p>
        </div>

        <div class="timeline-rail-wrap">
            <div class="timeline-progress-rail" aria-hidden="true"></div>
            <div class="row g-3 row-cols-lg-5 row-cols-md-3 row-cols-sm-2 row-cols-1 timeline-grid">
                <!-- Tahap 1 -->
                <div class="col">
                    <div class="stage-card-step timeline-step" style="--step-accent: #0c5aa6; --step-accent-bg: #eff6ff;">
                        <div class="stage-number-badge">1</div>
                        <span class="stage-step-tag">SiRUP &amp; Identifikasi</span>
                        <h4 class="stage-step-title">Perencanaan Pengadaan</h4>
                        <p class="stage-step-desc">Identifikasi kebutuhan, penetapan barang/jasa, cara pengadaan, jadwal, dan pengumuman SiRUP.</p>
                        <a href="<?= site_url('regulasi?stage_id=1') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="col">
                    <div class="stage-card-step timeline-step" style="--step-accent: #0284c7; --step-accent-bg: #f0f9ff;">
                        <div class="stage-number-badge">2</div>
                        <span class="stage-step-tag">HPS &amp; Spesifikasi/KAK</span>
                        <h4 class="stage-step-title">Persiapan Pengadaan</h4>
                        <p class="stage-step-desc">Penyusunan Spesifikasi Teknis/KAK, Harga Perkiraan Sendiri (HPS), dan rancangan kontrak.</p>
                        <a href="<?= site_url('regulasi?stage_id=2') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="col">
                    <div class="stage-card-step timeline-step" style="--step-accent: #10b981; --step-accent-bg: #ecfdf5;">
                        <div class="stage-number-badge">3</div>
                        <span class="stage-step-tag">E-Purchasing &amp; Tender</span>
                        <h4 class="stage-step-title">Pemilihan Penyedia</h4>
                        <p class="stage-step-desc">Pelaksanaan E-Purchasing (Katalog), Tender, Seleksi, Pengadaan Langsung, dan Evaluasi Penawaran.</p>
                        <a href="<?= site_url('regulasi?stage_id=3') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                    </div>
                </div>

                <!-- Tahap 4 -->
                <div class="col">
                    <div class="stage-card-step timeline-step" style="--step-accent: #f59e0b; --step-accent-bg: #fffbeb;">
                        <div class="stage-number-badge">4</div>
                        <span class="stage-step-tag">SPPBJ &amp; Kontrak</span>
                        <h4 class="stage-step-title">Pelaksanaan Kontrak</h4>
                        <p class="stage-step-desc">Penerbitan SPPBJ, SPMK, pengawasan pekerjaan, addendum kontrak, dan mitigasi risiko.</p>
                        <a href="<?= site_url('regulasi?stage_id=4') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                    </div>
                </div>

                <!-- Tahap 5 -->
                <div class="col">
                    <div class="stage-card-step timeline-step" style="--step-accent: #d97706; --step-accent-bg: #fef3c7;">
                        <div class="stage-number-badge">5</div>
                        <span class="stage-step-tag">BAST &amp; Pembayaran</span>
                        <h4 class="stage-step-title">Serah Terima Hasil</h4>
                        <p class="stage-step-desc">Pemeriksaan hasil pekerjaan, Berita Acara Serah Terima (BAST), pembayaran, dan evaluasi kinerja penyedia.</p>
                        <a href="<?= site_url('regulasi?stage_id=5') ?>" class="btn btn-sm btn-outline-primary w-100 radius-6">Regulasi Terkait</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     6. PUSAT REGULASI PBJ TERBARU & UNGGULAN
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:scale-bold"></iconify-icon>
                    <span>Bank Naskah Hukum PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Regulasi &amp; Produk Hukum Terbaru</h2>
                <p class="section-title-desc mb-0">Arsip resmi Peraturan Presiden, Peraturan LKPP, dan Peraturan Daerah Provinsi Lampung dengan status hukum terverifikasi.</p>
            </div>
            <a href="<?= site_url('regulasi') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Buka Pusat Regulasi</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php if (empty($regulations)): ?>
                <?php
                $mockRegs = [
                    ['num' => 'Perpres No. 12 Tahun 2021', 'issuer' => 'Presiden RI', 'title' => 'Perubahan atas Peraturan Presiden Nomor 16 Tahun 2018 tentang Pengadaan Barang/Jasa Pemerintah', 'status' => 'active', 'year' => '2021', 'slug' => 'perpres-12-tahun-2021'],
                    ['num' => 'Peraturan LKPP No. 12 Tahun 2021', 'issuer' => 'LKPP', 'title' => 'Pedoman Pelaksanaan Pengadaan Barang/Jasa Pemerintah Melalui Penyedia', 'status' => 'active', 'year' => '2021', 'slug' => 'perlkpp-12-tahun-2021'],
                    ['num' => 'Peraturan Gubernur Lampung No. 34 Tahun 2023', 'issuer' => 'Gubernur Lampung', 'title' => 'Tata Cara Pelaksanaan E-Purchasing dan Pengadaan Langsung di Lingkungan Pemerintah Provinsi Lampung', 'status' => 'active', 'year' => '2023', 'slug' => 'pergub-lampung-34-2023'],
                ];
                foreach ($mockRegs as $r): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="reg-card-modern">
                            <span class="reg-corner-ribbon"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku</span>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="reg-status-badge active">
                                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku
                                </span>
                                <span class="text-xs text-muted fw-semibold"><?= esc($r['year']) ?></span>
                            </div>
                            <span class="reg-doc-num"><?= esc($r['num']) ?></span>
                            <h3 class="reg-title-modern">
                                <a href="<?= site_url('regulasi') ?>"><?= esc($r['title']) ?></a>
                            </h3>
                            <p class="reg-summary-text">Pedoman acuan teknis hukum pengadaan barang/jasa bagi aparatur sipil negara dan pelaku usaha.</p>
                            <div class="reg-actions-row">
                                <span class="text-xs text-muted"><?= esc($r['issuer']) ?></span>
                                <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-outline-primary px-3 py-1 radius-6">Lihat Naskah</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($regulations as $r): ?>
                    <?php $legalStatus = strtolower((string) ($r['legal_status'] ?? 'active')); ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="reg-card-modern">
                            <?php if (in_array($legalStatus, ['active', 'berlaku'], true)): ?>
                                <span class="reg-corner-ribbon ribbon-active"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku</span>
                            <?php elseif (in_array($legalStatus, ['amended', 'diubah'], true)): ?>
                                <span class="reg-corner-ribbon ribbon-amended"><iconify-icon icon="solar:pen-bold"></iconify-icon> Diubah</span>
                            <?php else: ?>
                                <span class="reg-corner-ribbon ribbon-revoked"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Dicabut</span>
                            <?php endif; ?>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <?php if (in_array($legalStatus, ['active', 'berlaku'], true)): ?>
                                    <span class="reg-status-badge active"><iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku</span>
                                <?php elseif (in_array($legalStatus, ['amended', 'diubah'], true)): ?>
                                    <span class="reg-status-badge amended"><iconify-icon icon="solar:pen-bold"></iconify-icon> Diubah</span>
                                <?php else: ?>
                                    <span class="reg-status-badge revoked"><iconify-icon icon="solar:close-circle-bold"></iconify-icon> Dicabut</span>
                                <?php endif; ?>

                                <?php if (! empty($r['document_year'])): ?>
                                    <span class="text-xs text-muted fw-semibold">Tahun <?= esc($r['document_year']) ?></span>
                                <?php endif; ?>
                            </div>

                            <span class="reg-doc-num"><?= esc($r['document_number'] ?? 'Naskah PBJ') ?></span>

                            <?php if (! empty($r['category_name'])): ?>
                                <span class="badge bg-light text-primary border text-2xs fw-bold px-2 py-1 radius-4 mb-2 d-inline-block" style="width: fit-content;">
                                    <?= esc($r['category_name']) ?>
                                </span>
                            <?php endif; ?>

                            <h3 class="reg-title-modern">
                                <a href="<?= site_url('regulasi/' . esc($r['slug'])) ?>"><?= esc($r['title']) ?></a>
                            </h3>
                            <p class="reg-summary-text"><?= esc(! empty($r['summary']) ? $r['summary'] : 'Dokumen regulasi resmi pengadaan barang/jasa Pemerintah Provinsi Lampung.') ?></p>

                            <div class="reg-actions-row">
                                <span class="text-xs text-muted d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:shield-check-linear"></iconify-icon>
                                    <?= esc($r['issuer_name'] ?? 'Biro PBJ') ?>
                                </span>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="<?= site_url('regulasi/' . esc($r['slug'])) ?>" class="btn btn-sm btn-outline-primary px-2 py-1 radius-6" title="Lihat Detail &amp; Silsilah">Naskah</a>
                                    <a href="<?= site_url('regulasi/' . esc($r['slug']) . '/unduh') ?>" class="btn btn-sm btn-primary px-2 py-1 radius-6" title="Unduh PDF">Unduh</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     7. BUNGA RAMPAI: MULTI-TAB KNOWLEDGE HUB
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="section-title-wrap text-center">
            <div class="section-title-tag">
                <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                <span>Bunga Rampai Pengetahuan</span>
            </div>
            <h2 class="section-title-main">Pusat Literasi, Template Dokumen &amp; Studi Kasus</h2>
            <p class="section-title-desc mx-auto">Akses instan ribuan referensi praktis, format template terstandar, dan pembahasan solusi kasus pengadaan nyata.</p>
        </div>

        <!-- Tab Controls -->
        <div class="knowledge-tab-nav" role="tablist">
            <button class="knowledge-tab-btn active" id="tab-art-btn" data-bs-toggle="pill" data-bs-target="#tab-articles" type="button" role="tab">
                <iconify-icon icon="solar:document-text-bold"></iconify-icon>
                <span>Artikel PBJ</span>
                <span class="tab-counter"><?= max(4, count($articles)) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-tpl-btn" data-bs-toggle="pill" data-bs-target="#tab-templates" type="button" role="tab">
                <iconify-icon icon="solar:file-download-bold"></iconify-icon>
                <span>Template Dokumen</span>
                <span class="tab-counter"><?= max(4, count($templates)) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-cs-btn" data-bs-toggle="pill" data-bs-target="#tab-cases" type="button" role="tab">
                <iconify-icon icon="solar:shield-warning-bold"></iconify-icon>
                <span>Studi Kasus</span>
                <span class="tab-counter"><?= max(4, count($caseStudies)) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-faq-btn" data-bs-toggle="pill" data-bs-target="#tab-faqs" type="button" role="tab">
                <iconify-icon icon="solar:question-circle-bold"></iconify-icon>
                <span>Tanya Jawab (FAQ)</span>
                <span class="tab-counter"><?= max(5, count($faqs)) ?></span>
            </button>
        </div>

        <!-- Tab Panes -->
        <div class="tab-content">
            <!-- Pane 1: Artikel -->
            <div class="tab-pane fade show active" id="tab-articles" role="tabpanel">
                <div class="row g-3 g-md-4">
                    <?php if (empty($articles)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">Koleksi artikel wawasan PBJ dapat ditelusuri di portal Bunga Rampai.</p>
                            <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-primary radius-8 px-20">Lihat Semua Artikel</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($articles as $art): ?>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="article-card-modern">
                                    <div class="article-thumb-modern">
                                        <img src="<?= ! empty($art['cover_path']) ? base_url('file/' . (int) ($art['cover_file_id'] ?? 0)) : base_url('assets/main/images/blog/blog-1-1.jpg') ?>" alt="<?= esc($art['title']) ?>" onerror="this.src='<?= base_url('assets/main/images/blog/blog-1-1.jpg') ?>'">
                                    </div>
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-xs text-primary fw-bold text-uppercase">Artikel PBJ</span>
                                            <span class="text-xs text-muted"><?= (int) ($art['reading_time'] ?? 5) ?> mnt</span>
                                        </div>
                                        <h4 class="text-sm fw-bold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="text-dark text-decoration-none"><?= esc($art['title']) ?></a>
                                        </h4>
                                        <p class="text-xs text-slate-500 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= esc($art['summary'] ?? '') ?></p>
                                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                                            <small class="text-xs text-muted"><?= esc($art['author_name'] ?? 'Biro PBJ') ?></small>
                                            <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="text-xs text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                                                Baca <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/artikel') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Jelajahi Seluruh Artikel PBJ</a>
                </div>
            </div>

            <!-- Pane 2: Template Dokumen -->
            <div class="tab-pane fade" id="tab-templates" role="tabpanel">
                <div class="row g-3 g-md-3">
                    <?php if (empty($templates)): ?>
                        <div class="col-12 col-md-6">
                            <div class="template-item-card">
                                <div class="template-file-icon"><iconify-icon icon="solar:document-bold"></iconify-icon></div>
                                <div class="flex-grow-1">
                                    <h5 class="text-sm fw-bold mb-1">Format Kerangka Acuan Kerja (KAK) Jasa Konsultansi</h5>
                                    <span class="text-xs text-muted">Format DOCX &middot; Sesuai PerLKPP 12/2021</span>
                                </div>
                                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="template-item-card">
                                <div class="template-file-icon excel"><iconify-icon icon="solar:file-spreadsheet-bold"></iconify-icon></div>
                                <div class="flex-grow-1">
                                    <h5 class="text-sm fw-bold mb-1">Kertas Kerja Rincian Perhitungan HPS Pekerjaan Konstruksi</h5>
                                    <span class="text-xs text-muted">Format XLSX &middot; Dilengkapi formula analisa</span>
                                </div>
                                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($templates as $tpl): ?>
                            <?php
                            $isExcel = str_contains(strtolower($tpl['title'] . ($tpl['mime_type'] ?? '')), 'xlsx')
                                || str_contains(strtolower($tpl['title']), 'kertas kerja')
                                || str_contains(strtolower($tpl['title']), 'hps');
                            ?>
                            <div class="col-12 col-md-6">
                                <div class="template-item-card">
                                    <div class="template-file-icon <?= $isExcel ? 'excel' : '' ?>">
                                        <iconify-icon icon="<?= $isExcel ? 'solar:file-spreadsheet-bold' : 'solar:document-bold' ?>"></iconify-icon>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge <?= $isExcel ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> text-2xs fw-bold px-2 py-0">
                                                <?= $isExcel ? 'Spreadsheet (XLSX)' : 'Dokumen (DOCX)' ?>
                                            </span>
                                        </div>
                                        <h5 class="text-sm fw-bold mb-1"><?= esc($tpl['title']) ?></h5>
                                        <span class="text-xs text-muted"><?= esc($tpl['summary'] ?? 'Template Dokumen Kerja SPSE') ?></span>
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        <a href="<?= site_url('bunga-rampai/template-dokumen/' . esc($tpl['slug']) . '/unduh') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Unduh</a>
                                        <a href="<?= site_url('bunga-rampai/template-dokumen/' . esc($tpl['slug'])) ?>" class="btn btn-sm btn-outline-secondary px-2 py-0 text-2xs radius-6">Detail</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Lihat Semua Template Dokumen</a>
                </div>
            </div>

            <!-- Pane 3: Studi Kasus -->
            <div class="tab-pane fade" id="tab-cases" role="tabpanel">
                <div class="row g-3 g-md-4">
                    <?php if (empty($caseStudies)): ?>
                        <div class="col-12 col-md-6">
                            <div class="card p-24 radius-12 border bg-light h-100">
                                <span class="badge bg-warning-50 text-warning-700 px-2 py-1 text-xs fw-bold mb-2" style="width: fit-content;">Kasus Pemilihan</span>
                                <h5 class="text-base fw-bold mb-2">Penanganan Sanggahan Terkait Sertifikat Badan Usaha (SBU) yang Habis Masa Berlaku</h5>
                                <p class="text-sm text-slate-500 mb-3">Analisis yuridis mitigasi risiko pembatalan tender dan panduan pembuktian kualifikasi bagi Pokja.</p>
                                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-primary text-sm fw-bold text-decoration-none">Pelajari Solusi Kasus &rarr;</a>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card p-24 radius-12 border bg-light h-100">
                                <span class="badge bg-info-50 text-info-700 px-2 py-1 text-xs fw-bold mb-2" style="width: fit-content;">Kasus Kontrak</span>
                                <h5 class="text-base fw-bold mb-2">Pemberian Kesempatan 50 Hari Kalender &amp; Mekanisme Denda Keterlambatan</h5>
                                <p class="text-sm text-slate-500 mb-3">Penerapan Pasal 56 Perpres 16/2018 jo Perpres 12/2021 pada akhir tahun anggaran tanpa temuan BPK.</p>
                                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="text-primary text-sm fw-bold text-decoration-none">Pelajari Solusi Kasus &rarr;</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($caseStudies as $cs): ?>
                            <div class="col-12 col-md-6">
                                <div class="card p-24 radius-12 border bg-light h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-info-subtle text-info px-2 py-1 text-xs fw-bold">
                                            <?= esc($cs['procurement_stage'] ?? 'Mitigasi Risiko PBJ') ?>
                                        </span>
                                        <span class="text-xs text-muted">Studi Kasus</span>
                                    </div>
                                    <h5 class="text-base fw-bold mb-2"><?= esc($cs['title']) ?></h5>
                                    <p class="text-sm text-slate-600 mb-3"><?= esc($cs['summary'] ?? '') ?></p>
                                    <a href="<?= site_url('bunga-rampai/studi-kasus/' . esc($cs['slug'])) ?>" class="text-primary text-sm fw-bold text-decoration-none d-flex align-items-center gap-1 mt-auto">
                                        Pelajari Solusi Kasus <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Jelajahi Seluruh Studi Kasus</a>
                </div>
            </div>

            <!-- Pane 4: FAQ -->
            <div class="tab-pane fade" id="tab-faqs" role="tabpanel">
                <div class="row g-3">
                    <?php if (empty($faqs)): ?>
                        <div class="col-12">
                            <div class="accordion" id="homeFaqAccordion">
                                <div class="accordion-item radius-10 mb-2 border">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq1">
                                            Kapan Pejabat Pembuat Komitmen (PPK) wajib menyusun Dokumen HPS?
                                        </button>
                                    </h2>
                                    <div id="hfaq1" class="accordion-collapse collapse show" data-bs-parent="#homeFaqAccordion">
                                        <div class="accordion-body text-slate-600 text-sm">
                                            HPS disusun paling lama 28 hari kerja sebelum batas akhir pemasukan penawaran untuk pemilihan dengan pascakualifikasi, atau sebelum pemasukan dokumen kualifikasi untuk prakualifikasi. HPS tidak bersifat rahasia, namun rincian HPS bersifat rahasia.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item radius-10 mb-2 border">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq2">
                                            Apakah pelatihan di Arena Belajar BegawiPengadaan mendapatkan sertifikat resmi?
                                        </button>
                                    </h2>
                                    <div id="hfaq2" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                                        <div class="accordion-body text-slate-600 text-sm">
                                            Ya. Seluruh peserta yang menyelesaikan program pelatihan bersertifikasi dan lulus evaluasi kelulusan akan mendapatkan e-Sertifikat resmi ber-QR Code dari Biro PBJ Setda Provinsi Lampung yang keabsahannya dapat diverifikasi langsung.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="accordion" id="homeFaqAccordion">
                                <?php foreach ($faqs as $i => $fq): ?>
                                    <div class="accordion-item radius-10 mb-2 border">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?> fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#hfaq_<?= $i ?>">
                                                <?= esc($fq['title']) ?>
                                            </button>
                                        </h2>
                                        <div id="hfaq_<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#homeFaqAccordion">
                                            <div class="accordion-body text-slate-600 text-sm">
                                                <?= esc(! empty($fq['content']) ? strip_tags($fq['content']) : ($fq['summary'] ?? 'Informasi tanya jawab regulasi pengadaan.')) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= site_url('bunga-rampai/faq') ?>" class="btn btn-outline-primary px-24 py-10 radius-8">Buka Halaman Tanya Jawab Lengkap</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     8. AGENDA & EVENT PBJ MENDATANG
     ========================================================= -->
<section class="section-spacious bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-title-tag">
                    <iconify-icon icon="solar:calendar-bold"></iconify-icon>
                    <span>Agenda &amp; Pelatihan PBJ</span>
                </div>
                <h2 class="section-title-main mb-1">Event, Webinar &amp; Bimtek Mendatang</h2>
                <p class="section-title-desc mb-0">Ikuti kegiatan peningkatan kapasitas pengadaan bersama pakar LKPP dan praktisi senior.</p>
            </div>
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Lihat Seluruh Event</span>
                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php if (empty($upcomingEvents)): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">28</span>
                            <span class="event-date-month">Okt</span>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success text-xs fw-bold mb-1">Online &middot; Zoom</span>
                            <h4 class="text-base fw-bold mb-1">Webinar Nasional: Optimalisasi E-Katalog V6 bagi Pelaku Usaha Mikro &amp; Kecil</h4>
                            <p class="text-xs text-muted mb-3">Sosialisasi teknis pendaftaran etalase dan transaksi e-purchasing Pemprov Lampung.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Daftar Gratis</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">12</span>
                            <span class="event-date-month">Nov</span>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary text-xs fw-bold mb-1">Tatap Muka &middot; Bandar Lampung</span>
                            <h4 class="text-base fw-bold mb-1">Bimbingan Teknis: Penyusunan Dokumen Pemilihan &amp; Mitigasi Risiko Sanggah</h4>
                            <p class="text-xs text-muted mb-3">Pelatihan intensif khusus Pokja Pemilihan UKPBJ Kabupaten/Kota se-Lampung.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Daftar Peserta</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="event-card-modern">
                        <div class="event-date-badge">
                            <span class="event-date-day">20</span>
                            <span class="event-date-month">Nov</span>
                        </div>
                        <div>
                            <span class="badge bg-warning-subtle text-warning text-xs fw-bold mb-1">Hybrid &middot; Gedung Pusiban</span>
                            <h4 class="text-base fw-bold mb-1">Sosialisasi Surat Edaran Pencegahan Korupsi &amp; Gratifikasi Pengadaan</h4>
                            <p class="text-xs text-muted mb-3">Kolaborasi bersama Inspektorat Daerah Provinsi Lampung dan APIP.</p>
                            <a href="<?= site_url('event') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6">Detail Acara</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($upcomingEvents as $evt): ?>
                    <?php
                    $eventTime = ! empty($evt['start_at']) && $evt['start_at'] !== '0000-00-00 00:00:00' ? strtotime($evt['start_at']) : time();
                    $dayNum    = date('d', $eventTime);
                    $monthNum  = (int) date('n', $eventTime);
                    $monthText = $bulanIndo[$monthNum] ?? date('M', $eventTime);
                    $mode      = strtolower((string) ($evt['mode'] ?? 'online'));
                    $modeBadge = match ($mode) {
                        'online' => ['class' => 'bg-success-subtle text-success', 'label' => 'Online • Zoom'],
                        'offline' => ['class' => 'bg-primary-subtle text-primary', 'label' => 'Tatap Muka • Luring'],
                        'hybrid' => ['class' => 'bg-warning-subtle text-warning-emphasis', 'label' => 'Hybrid • Luring & Daring'],
                        default => ['class' => 'bg-secondary-subtle text-secondary', 'label' => ucfirst($mode)]
                    };
                    $typeLabel = ucfirst((string) ($evt['type'] ?? 'Webinar'));
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="event-card-modern">
                            <div class="event-date-badge">
                                <span class="event-date-day"><?= $dayNum ?></span>
                                <span class="event-date-month"><?= $monthText ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                    <span class="badge <?= $modeBadge['class'] ?> text-2xs fw-bold">
                                        <?= $modeBadge['label'] ?>
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs">
                                        <?= esc($typeLabel) ?>
                                    </span>
                                </div>
                                <h4 class="text-base fw-bold mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="<?= site_url('event/' . esc($evt['slug'])) ?>" class="text-dark text-decoration-none">
                                        <?= esc($evt['title']) ?>
                                    </a>
                                </h4>
                                <p class="text-xs text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc(! empty($evt['summary']) ? $evt['summary'] : ($evt['location'] ?? 'Kegiatan peningkatan kapasitas pengadaan Provinsi Lampung.')) ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <span class="text-xs text-muted">
                                        <?= ! empty($evt['capacity']) ? 'Kuota: ' . (int) $evt['capacity'] : 'Terbuka' ?>
                                    </span>
                                    <a href="<?= site_url('event/' . esc($evt['slug'])) ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6 d-inline-flex align-items-center gap-1">
                                        <span>Detail Acara</span>
                                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     9. STATISTIK PENGUNJUNG & PEMANFAATAN PORTAL (COMPACT PORTAL STYLE)
     ========================================================= -->
<section class="visitor-stats-section">
    <div class="container">
        <!-- Compact Section Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-1">
            <div>
                <div class="d-inline-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white border text-primary px-2 py-1 radius-20 text-2xs fw-bold">
                        <iconify-icon icon="solar:chart-2-bold" class="me-1"></iconify-icon>
                        Transparansi Akses Publik
                    </span>
                    <span class="visitor-pulse-indicator">
                        <span class="visitor-pulse-dot"></span>
                        <span>Real-Time Tracker</span>
                    </span>
                </div>
                <h3 class="text-xl fw-bold text-dark mb-0">Statistik Trafik &amp; Kunjungan Portal</h3>
            </div>
            <p class="text-xs text-muted mb-0 d-none d-md-block" style="max-width: 420px; line-height: 1.4;">
                Pantauan berkala akses dan pemanfaatan sistem pengadaan terpadu Provinsi Lampung secara terbuka dan akuntabel.
            </p>
        </div>

        <div class="row g-3 g-md-3 align-items-stretch">
            <!-- Kolom 1: Akses Landing Page Utama (Home) -->
            <div class="col-12 col-lg-5">
                <div class="visitor-compact-card card-home">
                    <div>
                        <!-- Header -->
                        <div class="visitor-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="visitor-card-header-icon" style="background: #eff6ff; color: #0c5aa6;">
                                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                                </div>
                                <div>
                                    <h4 class="visitor-card-title">Trafik Kunjungan Beranda</h4>
                                    <p class="visitor-card-subtitle">Frekuensi pembukaan halaman utama BegawiPengadaan</p>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs fw-bold px-2 py-1 radius-999 d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="solar:verified-check-bold"></iconify-icon> Akumulatif
                            </span>
                        </div>

                        <!-- Summary Total Box -->
                        <div class="visitor-home-summary-box">
                            <div>
                                <div class="visitor-home-summary-label">Total Akumulasi Pembukaan</div>
                                <div class="visitor-home-summary-num">
                                    <?= number_format($visitorStats['home']['total'] ?? 0) ?>
                                    <span class="fs-6 fw-semibold text-muted ms-1">kali dibuka</span>
                                </div>
                            </div>
                            <div class="p-2 rounded-circle" style="background: #eff6ff; color: #0c5aa6; font-size: 20px;">
                                <iconify-icon icon="solar:fire-bold"></iconify-icon>
                            </div>
                        </div>

                        <!-- 4 Interval Grid: Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini -->
                        <div class="visitor-intervals-grid">
                            <!-- Hari Ini -->
                            <div class="visitor-interval-box">
                                <div class="visitor-interval-icon" style="background: #fffbeb; color: #d97706;">
                                    <iconify-icon icon="solar:sun-2-bold"></iconify-icon>
                                </div>
                                <div>
                                    <div class="visitor-interval-val">
                                        <?= number_format($visitorStats['home']['today'] ?? 0) ?>
                                    </div>
                                    <div class="visitor-interval-lbl">Hari Ini</div>
                                </div>
                            </div>

                            <!-- Minggu Ini -->
                            <div class="visitor-interval-box">
                                <div class="visitor-interval-icon" style="background: #eff6ff; color: #0284c7;">
                                    <iconify-icon icon="solar:calendar-linear"></iconify-icon>
                                </div>
                                <div>
                                    <div class="visitor-interval-val">
                                        <?= number_format($visitorStats['home']['this_week'] ?? 0) ?>
                                    </div>
                                    <div class="visitor-interval-lbl">Minggu Ini</div>
                                </div>
                            </div>

                            <!-- Bulan Ini -->
                            <div class="visitor-interval-box">
                                <div class="visitor-interval-icon" style="background: #ecfdf5; color: #059669;">
                                    <iconify-icon icon="solar:calendar-mark-bold"></iconify-icon>
                                </div>
                                <div>
                                    <div class="visitor-interval-val">
                                        <?= number_format($visitorStats['home']['this_month'] ?? 0) ?>
                                    </div>
                                    <div class="visitor-interval-lbl">Bulan Ini (<?= ($bulanIndo[(int) date('n')] ?? date('M')) ?>)</div>
                                </div>
                            </div>

                            <!-- Tahun Ini -->
                            <div class="visitor-interval-box">
                                <div class="visitor-interval-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <iconify-icon icon="solar:chart-square-bold"></iconify-icon>
                                </div>
                                <div>
                                    <div class="visitor-interval-val">
                                        <?= number_format($visitorStats['home']['this_year'] ?? 0) ?>
                                    </div>
                                    <div class="visitor-interval-lbl">Tahun Ini (<?= date('Y') ?>)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="visitor-card-footer">
                        <iconify-icon icon="solar:shield-check-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                        <span>Dihitung akumulatif tanpa batasan sesi pengguna (non-unique page views).</span>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Distribusi Pemanfaatan Fitur Unggulan (5 Fitur) -->
            <div class="col-12 col-lg-7">
                <div class="visitor-compact-card card-features">
                    <div>
                        <!-- Header -->
                        <div class="visitor-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="visitor-card-header-icon" style="background: #ecfdf5; color: #059669;">
                                    <iconify-icon icon="solar:chart-2-bold"></iconify-icon>
                                </div>
                                <div>
                                    <h4 class="visitor-card-title">Frekuensi Akses Fitur Unggulan</h4>
                                    <p class="visitor-card-subtitle">Aktivitas pembukaan modul portal pengadaan</p>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-2xs fw-bold px-2 py-1 radius-999">
                                Total <?= number_format($visitorStats['total_feature_hits'] ?? 0) ?> Akses
                            </span>
                        </div>

                        <!-- 5 Feature Items List -->
                        <div class="visitor-features-list">
                            <?php if (! empty($visitorStats['features'])): ?>
                                <?php foreach ($visitorStats['features'] as $fKey => $fData): ?>
                                    <a href="<?= esc($fData['url']) ?>" class="visitor-feature-row" style="--feat-color: <?= esc($fData['color']) ?>; --feat-border: <?= esc($fData['border']) ?>;">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <div class="visitor-feature-icon-box" style="background: <?= esc($fData['bg']) ?>; color: <?= esc($fData['color']) ?>;">
                                                    <iconify-icon icon="<?= esc($fData['icon']) ?>"></iconify-icon>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="visitor-feature-name text-truncate">
                                                        <?= esc($fData['label']) ?>
                                                        <span class="badge bg-light text-muted border text-2xs ms-1"><?= esc($fData['percentage']) ?>%</span>
                                                    </div>
                                                    <div class="visitor-feature-desc text-truncate">
                                                        <?= esc($fData['description']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end flex-shrink-0">
                                                <div class="fw-bold text-dark text-xs">
                                                    <?= number_format($fData['hits_total']) ?> <small class="text-muted fw-normal">tayang</small>
                                                </div>
                                                <span class="badge bg-success-subtle text-success text-2xs px-1 py-0">
                                                    +<?= number_format($fData['hits_today']) ?> hari ini
                                                </span>
                                            </div>
                                        </div>
                                        <div class="visitor-progress-track">
                                            <div class="visitor-progress-fill" style="width: <?= esc($fData['percentage']) ?>%; background: <?= esc($fData['color']) ?>;"></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="visitor-card-footer">
                        <iconify-icon icon="solar:info-circle-bold" class="text-primary text-base flex-shrink-0"></iconify-icon>
                        <span>Tercatat otomatis pada setiap kunjungan modul terkait secara transparan.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     10. TESTIMONI PENGGUNA (FISTUDY SIGNATURE TESTIMONIAL ONE)
     ========================================================= -->
<section class="testimonial-one section-testimonies" id="section-testimonies">
    <div class="testimonial-one__shape-1 float-bob-x">
        <img src="<?= base_url('assets/main/images/shapes/testimonial-one-shape-1.png') ?>" alt="" loading="lazy">
    </div>
    <div class="testimonial-one__shape-2">
        <img src="<?= base_url('assets/main/images/shapes/testimonial-one-shape-2.png') ?>" alt="" loading="lazy">
    </div>
    <div class="container">
        <!-- FiStudy Section Title -->
        <div class="section-title text-left sec-title-animation animation-style2">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape"></div>
                <span class="section-title__tagline">Suara Pemangku Kepentingan</span>
            </div>
            <h2 class="section-title__title title-animation">
                Testimoni &amp; Pengalaman Nyata Pengguna BegawiPengadaan <br>
                <span>Provinsi Lampung <img src="<?= base_url('assets/main/images/shapes/section-title-shape-1.png') ?>" alt=""></span>
            </h2>
        </div>

        <!-- Testimony KPI Summary Strip -->
        <!-- <div class="testimony-kpi-strip">
            <div class="row g-3 align-items-center">
                <div class="col-6 col-md-3">
                    <div class="testimony-kpi-item">
                        <div class="testimony-kpi-val text-primary">98.4%</div>
                        <div class="testimony-kpi-lbl">Kepuasan Pengguna</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="testimony-kpi-item border-start">
                        <div class="testimony-kpi-val text-success">1.250+</div>
                        <div class="testimony-kpi-lbl">Aparatur PBJ Terbantu</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="testimony-kpi-item border-start-md">
                        <div class="testimony-kpi-val text-info">55%</div>
                        <div class="testimony-kpi-lbl">Efisiensi Dokumen PBJ</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="testimony-kpi-item border-start">
                        <div class="testimony-kpi-val text-warning">100%</div>
                        <div class="testimony-kpi-lbl">Bebas Sanggahan Banding</div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- FiStudy Testimonial Carousel (1 Card per slide) -->
        <div class="testimonial-one__inner">
            <div class="testimonial-one__carousel owl-theme owl-carousel">
                <!-- Slide 1: Ahmad Zulkarnaen, S.T., M.T. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-1-1.jpg') ?>" alt="Ahmad Zulkarnaen, S.T., M.T." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user2.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                    <iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon> Pejabat Pembuat Komitmen (PPK)
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:verified-check-bold" class="me-1"></iconify-icon> Terverifikasi SPSE
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t1')">Ahmad Zulkarnaen, S.T., M.T.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">PPK Dinas Bina Marga &amp; Bina Konstruksi Provinsi Lampung</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;Penyusunan HPS dan spesifikasi teknis untuk paket infrastruktur jalan kini jauh lebih terstandarisasi berkat template dokumen di Bunga Rampai dan sinkronisasi regulasi terpusat. Risiko deviasi hukum saat audit probity berkurang drastis, sehingga eksekusi program fisik berjalan tepat sasaran.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:graph-up-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> Memangkas waktu persiapan lelang hingga 55% &amp; nihil temuan administratif saat audit probity.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t1')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Rina Widyastuti, S.E., M.Si. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-1-2.jpg') ?>" alt="Rina Widyastuti, S.E., M.Si." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user4.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                    <iconify-icon icon="solar:diploma-verified-bold" class="me-1"></iconify-icon> Pokja Pemilihan / UKPBJ
                                </span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:shield-check-bold" class="me-1"></iconify-icon> Akreditasi LKPP
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t2')">Rina Widyastuti, S.E., M.Si.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">Anggota Pokja Pemilihan Biro PBJ Setda Provinsi Lampung</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;Arena Belajar dan simulasi evaluasi penawaran sangat membantu peningkatan kapasitas anggota Pokja. Ujian sertifikasi dan materi terstruktur meningkatkan kepercayaan diri kami dalam tender bernilai strategis di tingkat provinsi tanpa hambatan sanggahan banding.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:shield-check-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> Evaluasi tender selesai tepat waktu 100% dan bebas sanggahan banding pemohon.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t2')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Hendra Kusuma, S.Kom. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-2-1.jpg') ?>" alt="Hendra Kusuma, S.Kom." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user6.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                    <iconify-icon icon="solar:shop-2-bold" class="me-1"></iconify-icon> Mitra Penyedia &amp; UMKM
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> E-Katalog Aktif
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t3')">Hendra Kusuma, S.Kom.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">Direktur PT Lampung Cipta Mandiri (Penyedia E-Katalog)</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;Sebagai pelaku usaha lokal di Lampung, portal BegawiPengadaan membuka transparansi penuh. Kami mudah memahami kriteria kualifikasi, etika penawaran, hingga tata cara etalase produk pada e-katalog lokal Pemprov Lampung secara adil.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:cart-large-4-bold" class="text-warning text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> Produk lokal UMKM terserap optimal dalam belanja APBD Pemprov Lampung.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t3')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Drs. Bambang Irawan, Ak., CA. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-1-3.jpg') ?>" alt="Drs. Bambang Irawan, Ak., CA." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-3 py-1 radius-999 text-xs fw-bold" style="background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;">
                                    <iconify-icon icon="solar:shield-check-bold" class="me-1"></iconify-icon> Pengawas APIP &amp; Auditor
                                </span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:document-text-bold" class="me-1"></iconify-icon> Auditor Probity
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t4')">Drs. Bambang Irawan, Ak., CA.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">Pengendali Teknis Inspektorat Provinsi Lampung</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;BegawiPengadaan memfasilitasi audit kepatuhan secara objektif. Riwayat versi regulasi dan standar dokumen baku memudahkan kami memverifikasi kesesuaian prosedur dari tahap perencanaan sampai serah terima hasil pekerjaan.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:shield-check-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> Penguatan integritas sistem dan pencegahan dini potensi deviasi PBJ.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t4')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 5: dr. Nurul Hidayati, M.Kes. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-2-2.jpg') ?>" alt="dr. Nurul Hidayati, M.Kes." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user3.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                    <iconify-icon icon="solar:heart-pulse-bold" class="me-1"></iconify-icon> PPK Pengadaan Alkes &amp; Obat
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:hospital-bold" class="me-1"></iconify-icon> RSUD Abdul Moeloek
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t5')">dr. Nurul Hidayati, M.Kes.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">PPK Pengadaan Alkes RSUD Dr. H. Abdul Moeloek</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;Pengadaan obat dan alat medis memerlukan ketepatan waktu serta kepatuhan e-katalog nasional yang sangat ketat. Fitur konsultasi dan referensi regulasi di BegawiPengadaan memastikan ketersediaan logistik rumah sakit selalu terjamin.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:heart-check-bold" class="text-danger text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> Ketepatan waktu pasokan medis mencapai 99,2% dengan tata kelola e-katalog akurat.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t5')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 6: Surya Darmawan, S.T. -->
                <div class="item">
                    <div class="testimonial-one__single testimony-card spotlight-card">
                        <div class="testimonial-one__img-inner spotlight-photo-frame">
                            <div class="testimonial-one__img">
                                <img src="<?= base_url('assets/main/images/testimonial/testimonial-2-3.jpg') ?>" alt="Surya Darmawan, S.T." loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user5.png') ?>'">
                                <div class="testimonial-one__icon">
                                    <span class="icon-quote"></span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-one__content">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                    <iconify-icon icon="solar:hammer-bold" class="me-1"></iconify-icon> Tim Teknis &amp; Pengawas
                                </span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-999 text-2xs fw-bold">
                                    <iconify-icon icon="solar:home-bold" class="me-1"></iconify-icon> Dinas PKP Lampung
                                </span>
                            </div>
                            <div class="testimonial-one__client-info">
                                <h3 class="testimonial-one__client-name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('t6')">Surya Darmawan, S.T.</a>
                                </h3>
                                <p class="testimonial-one__client-sub-title">Tim Teknis Dinas Perumahan &amp; Kawasan Permukiman Lampung</p>
                            </div>
                            <p class="testimonial-one__text">
                                &ldquo;Standarisasi berita acara dan format pemeriksaan pekerjaan di Bunga Rampai mempermudah tim teknis melakukan serah terima fisik di lapangan secara presisi dan sesuai kontrak kerja.&rdquo;
                            </p>
                            <div class="fistudy-impact-pill mb-3 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-info text-base flex-shrink-0"></iconify-icon>
                                <span><strong>Dampak Terukur:</strong> 100% berita acara serah terima terdokumentasi rapi dan terverifikasi digital.</span>
                            </div>
                            <div class="testimonial-one__ratting-and-social">
                                <ul class="testimonial-one__ratting list-unstyled">
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                    <li><span class="icon-star"></span></li>
                                </ul>
                                <div class="testimonial-one__social ms-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('t6')">
                                        <span>Detail Ulasan</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     11. TIM INOVATOR & PENGEMBANG (FISTUDY SIGNATURE TEAM ONE)
     ========================================================= -->
<section class="team-one section-team" id="section-team">
    <div class="team-one__shape-1 shapemover">
        <img src="<?= base_url('assets/main/images/shapes/team-one-shape-1.png') ?>" alt="" loading="lazy">
    </div>
    <div class="team-one__shape-2 float-bob-y">
        <img src="<?= base_url('assets/main/images/shapes/team-one-shape-2.png') ?>" alt="" loading="lazy">
    </div>
    <div class="team-one__shape-3">
        <img src="<?= base_url('assets/main/images/shapes/team-one-shape-3.png') ?>" alt="" loading="lazy">
    </div>
    <div class="team-one__shape-4 float-bob-x">
        <img src="<?= base_url('assets/main/images/shapes/team-one-shape-4.png') ?>" alt="" loading="lazy">
    </div>
    <div class="container">
        <!-- FiStudy Section Title -->
        <div class="section-title text-left sec-title-animation animation-style2">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape"></div>
                <span class="section-title__tagline">Kolaborator &amp; Pengembang</span>
            </div>
            <h2 class="section-title__title title-animation">
                Tim Inovator di Balik Ekosistem BegawiPengadaan <br>
                <span>Provinsi Lampung <img src="<?= base_url('assets/main/images/shapes/section-title-shape-1.png') ?>" alt=""></span>
            </h2>
        </div>

        <!-- FiStudy Team Spotlight Carousel (1 Card per slide) -->
        <div class="team-one__inner">
            <div class="team-spotlight__carousel owl-theme owl-carousel">
                <!-- Slide 1: Dr. Ir. Fahrizal Darminto, M.A. -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-1.jpg') ?>" alt="Dr. Ir. Fahrizal Darminto, M.A." class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:crown-star-bold" class="text-warning"></iconify-icon>
                                <span>Pengarah Strategis PBJ</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                        <iconify-icon icon="solar:crown-star-bold" class="me-1"></iconify-icon> Pengarah Kebijakan Makro
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:buildings-bold" class="text-primary me-1"></iconify-icon> Pimpinan Daerah
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm1')">Dr. Ir. Fahrizal Darminto, M.A.</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Sekretaris Daerah Provinsi Lampung</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Penguatan tata kelola pengadaan barang dan jasa berbasis digital merupakan komitmen Pemprov Lampung guna memastikan belanja APBD yang transparan, akuntabel, dan berdampak langsung pada kesejahteraan masyarakat.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:shield-check-bold" class="text-primary text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Peran Kunci:</strong> Pengarah kebijakan makro regulasi daerah dan akuntabilitas belanja APBD.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:verified-check-bold" class="text-success me-1"></iconify-icon> Setda Provinsi Lampung</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm1')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Slamet Riadi, S.Sos., M.M. -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-2.jpg') ?>" alt="Slamet Riadi, S.Sos., M.M." class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user3.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:star-bold" class="text-warning"></iconify-icon>
                                <span>Kepala Biro PBJ Setda</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                        <iconify-icon icon="solar:star-bold" class="me-1"></iconify-icon> Inisiator &amp; Pembina
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:shield-check-bold" class="text-info me-1"></iconify-icon> Biro PBJ Setda
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm2')">Slamet Riadi, S.Sos., M.M.</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Kepala Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Menginisiasi arsitektur ekosistem BegawiPengadaan, memimpin transformasi UKPBJ Lampung menuju Level 3 (Proaktif) dan pusat keunggulan PBJ berstandar nasional yang transparan dan akuntabel.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:medal-ribbon-star-bold" class="text-primary text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Fokus Pembinaan:</strong> Akselerasi kelembagaan UKPBJ Lampung meraih akreditasi Level 3 Proaktif LKPP.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:verified-check-bold" class="text-success me-1"></iconify-icon> Biro PBJ Setda Lampung</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm2')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Software Engineering & UI/UX Crew -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-5.jpg') ?>" alt="Software Engineering &amp; UI/UX Crew" class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user5.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:laptop-bold" class="text-warning"></iconify-icon>
                                <span>Software Engineering &amp; UX</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                        <iconify-icon icon="solar:laptop-bold" class="me-1"></iconify-icon> Rekayasa Perangkat Lunak
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:code-circle-bold" class="text-warning me-1"></iconify-icon> Full-Stack CI4
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm3')">Software Engineering &amp; UI/UX Crew</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Tim Transformasi Digital BegawiPengadaan</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Merancang arsitektur MVC CodeIgniter 4 yang gesit, integrasi database MySQL teroptimasi, tata letak responsif modern WowDash, serta antarmuka interaktif yang mudah diakses oleh seluruh OPD di Lampung.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:code-bold" class="text-warning text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Keunggulan Sistem:</strong> 100% Full-Stack Responsif, performa cepat, dan kepatuhan standar BSSN.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:server-square-bold" class="me-1"></iconify-icon> Perekayasa Sistem SPSE</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm3')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Tim LPSE & Infrastruktur SPSE -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-4.jpg') ?>" alt="Tim LPSE &amp; Pengelola SPSE" class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user4.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:server-bold" class="text-info"></iconify-icon>
                                <span>Tim LPSE &amp; Keamanan SPSE</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                        <iconify-icon icon="solar:server-bold" class="me-1"></iconify-icon> Infrastruktur &amp; Layanan LPSE
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:shield-check-bold" class="text-info me-1"></iconify-icon> Standar ISO 27001
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm4')">Tim LPSE &amp; Infrastruktur SPSE</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Pengelola Sistem Pengadaan Secara Elektronik Biro PBJ</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Menjamin ketersediaan server 24/7 dengan standardisasi ISO 27001 dan rekomendasi BSSN, memastikan kelancaran seluruh proses tender elektronik tanpa downtime di seluruh kabupaten/kota se-Lampung.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:shield-check-bold" class="text-info text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Keandalan:</strong> 99,9% Server Uptime dan perlindungan data transaksi pengadaan terenkripsi.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:database-bold" class="me-1"></iconify-icon> Data Center Pemprov Lampung</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm4')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 5: Tim Pembinaan & Advokasi Hukum PBJ -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-3.jpg') ?>" alt="Tim Pembinaan &amp; Advokasi Regulasi PBJ" class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user6.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:document-text-bold" class="text-purple"></iconify-icon>
                                <span>Advokasi &amp; Harmonisasi Regulasi</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-3 py-1 radius-999 text-xs fw-bold" style="background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;">
                                        <iconify-icon icon="solar:scale-bold" class="me-1"></iconify-icon> Advokasi &amp; Hukum PBJ
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:document-bold" class="text-primary me-1"></iconify-icon> Mitigasi Risiko
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm5')">Tim Pembinaan &amp; Advokasi Hukum PBJ</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Biro Pengadaan Barang &amp; Jasa Setda Provinsi Lampung</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Menyusun harmonisasi peraturan gubernur, telaah hukum pengadaan, mitigasi risiko sanggahan tender, serta penerbitan panduan teknis yang selaras dengan Perpres No. 12 Tahun 2021 dan LKPP.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:shield-check-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Fokus:</strong> Kepastian hukum penuh bagi seluruh Pejabat Pembuat Komitmen dan Pokja Pemilihan.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:diploma-bold" class="me-1"></iconify-icon> Pendampingan Hukum &amp; Kontrak</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm5')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 6: Tim Fasilitator & Master Trainer PBJ -->
                <div class="item">
                    <div class="team-spotlight__single team-card spotlight-card">
                        <div class="team-spotlight__img-box spotlight-photo-frame">
                            <img src="<?= base_url('assets/main/images/team/team-1-6.jpg') ?>" alt="Tim Fasilitator &amp; Master Trainer PBJ" class="team-spotlight__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user2.png') ?>'">
                            <div class="team-spotlight__overlay"></div>
                            <div class="team-spotlight__tag">
                                <iconify-icon icon="solar:graduation-cap-bold" class="text-success"></iconify-icon>
                                <span>Fasilitator &amp; Master Trainer LKPP</span>
                            </div>
                        </div>
                        <div class="team-spotlight__content">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 radius-999 text-xs fw-bold">
                                        <iconify-icon icon="solar:medal-ribbon-bold" class="me-1"></iconify-icon> Instruktur &amp; Penguji
                                    </span>
                                    <span class="badge bg-light text-dark border text-2xs fw-bold px-2 py-1 ms-auto">
                                        <iconify-icon icon="solar:diploma-verified-bold" class="text-success me-1"></iconify-icon> Sertifikasi LKPP
                                    </span>
                                </div>
                                <h3 class="team-spotlight__name">
                                    <a href="javascript:void(0)" onclick="openTestimonyModal('tm6')">Tim Fasilitator &amp; Master Trainer PBJ</a>
                                </h3>
                                <div class="team-spotlight__sub-title">Instruktur PBJ Terakreditasi LKPP Lampung</div>
                                <p class="team-spotlight__text">
                                    &ldquo;Menyusun silabus pembelajaran berbasis kompetensi di Arena Belajar, bank soal ujian sertifikasi level-1, materi bimtek interaktif, serta panduan praktis penanganan sanggahan bagi aparatur se-Lampung.&rdquo;
                                </p>
                            </div>
                            <div>
                                <div class="team-spotlight__impact-box">
                                    <iconify-icon icon="solar:book-bookmark-bold" class="text-secondary text-base flex-shrink-0"></iconify-icon>
                                    <span><strong>Dampak:</strong> Standardisasi kompetensi ratusan ASN pengadaan di lingkungan Pemprov Lampung.</span>
                                </div>
                                <div class="team-spotlight__actions">
                                    <span><iconify-icon icon="solar:users-group-two-rounded-bold" class="me-1"></iconify-icon> Lembaga Sertifikasi Aparatur</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" onclick="openTestimonyModal('tm6')">
                                        <span>Lihat Profil Tim</span>
                                        <span class="icon-arrow-up-right-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Values & Commitment Banner -->
        <div class="team-values-banner">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="team-value-item">
                        <div class="team-value-icon">
                            <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-1 text-sm">Budaya Kerja PATIK</h5>
                            <p class="text-slate-300 mb-0 text-xs">Profesional, Akuntabel, Transparan, Integritas, dan Kredibel tanpa gratifikasi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="team-value-item">
                        <div class="team-value-icon">
                            <iconify-icon icon="solar:medal-star-bold"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-1 text-sm">Standar Mutu Level 3 LKPP</h5>
                            <p class="text-slate-300 mb-0 text-xs">UKPBJ Lampung bertransformasi proaktif sebagai pusat keunggulan pengadaan nasional.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="team-value-item">
                        <div class="team-value-icon">
                            <iconify-icon icon="solar:shop-2-bold"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-1 text-sm">Pemberdayaan UMKM Lokal</h5>
                            <p class="text-slate-300 mb-0 text-xs">Mendorong penyerapan optimal produk buatan daerah dalam etalase e-katalog lokal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Modal Detail Testimoni Interaktif -->
<div class="modal fade" id="testimonyDetailModal" tabindex="-1" aria-labelledby="testimonyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 radius-999 text-xs fw-bold" id="modalRoleBadge">Peran</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-999 text-2xs fw-bold"><iconify-icon icon="solar:verified-check-bold"></iconify-icon> Terverifikasi</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="" id="modalAvatarImg" class="showcase-avatar-photo" alt="" style="width: 54px; height: 54px; display: none;">
                    <div class="testimony-avatar" id="modalAvatar" style="width: 54px; height: 54px; font-size: 18px; background: #0c5aa6;">AZ</div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" id="modalAuthorName">Nama Pengguna</h5>
                        <p class="text-xs text-muted mb-0" id="modalAuthorOrg">Instansi / Organisasi</p>
                    </div>
                </div>
                <div class="testimony-stars mb-3 text-warning">
                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                </div>
                <blockquote class="p-3 bg-light radius-10 border mb-3 text-slate-700" id="modalQuoteText" style="font-size: 14px; line-height: 1.7; border-left: 4px solid #0c5aa6 !important;">
                    Kutipan ulasan...
                </blockquote>
                <div class="p-3 bg-success-subtle radius-10 border border-success-subtle">
                    <div class="text-2xs fw-bold text-uppercase text-success mb-1"><iconify-icon icon="solar:graph-up-bold" class="me-1"></iconify-icon> Dampak Nyata Terukur:</div>
                    <div class="text-xs fw-bold text-dark" id="modalImpactText">Dampak terukur</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary radius-8 px-4 text-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="<?= site_url('arena-belajar') ?>" class="btn btn-primary radius-8 px-4 text-sm d-inline-flex align-items-center gap-2">
                    <span>Eksplorasi Modul</span>
                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     12. BIRO PBJ SETDA PROVINSI LAMPUNG & TAUTAN EKOSISTEM
     ========================================================= -->
<section class="section-spacious biro-showcase-bg">
    <div class="container">
        <div class="row align-items-center g-3 g-md-4">
            <div class="col-12 col-lg-6">
                <div class="section-title-tag">
                    <iconify-icon icon="solar:city-bold"></iconify-icon>
                    <span>Tentang Penyelenggara</span>
                </div>
                <h2 class="section-title-main">Biro Pengadaan Barang &amp; Jasa Sekretariat Daerah Provinsi Lampung</h2>
                <p class="text-slate-600 mb-3" style="line-height: 1.7;">
                    Sebagai Unit Kerja Pengadaan Barang/Jasa (UKPBJ) Pemerintah Provinsi Lampung, kami berkomitmen mewujudkan pengadaan yang <strong>kredibel, transparan, akuntabel, dan bebas gratifikasi</strong> berlandaskan budaya kerja PATIK (Profesional, Akuntabel, Transparan, Integritas, Kredibel).
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="patik-chip"><iconify-icon icon="solar:star-bold" class="text-primary"></iconify-icon><strong>P</strong> &middot; Profesional</span>
                    <span class="patik-chip"><iconify-icon icon="solar:shield-check-bold" class="text-info"></iconify-icon><strong>A</strong> &middot; Akuntabel</span>
                    <span class="patik-chip"><iconify-icon icon="solar:eye-bold" class="text-success"></iconify-icon><strong>T</strong> &middot; Transparan</span>
                    <span class="patik-chip"><iconify-icon icon="solar:lock-keyhole-bold" class="text-warning"></iconify-icon><strong>I</strong> &middot; Integritas</span>
                    <span class="patik-chip"><iconify-icon icon="solar:medal-ribbon-star-bold" class="text-danger"></iconify-icon><strong>K</strong> &middot; Kredibel</span>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border radius-12">
                            <div class="text-warning text-2xl"><iconify-icon icon="solar:shield-check-bold"></iconify-icon></div>
                            <div>
                                <strong class="d-block text-sm text-dark">Zona Integritas</strong>
                                <small class="text-slate-500">Bebas Korupsi &amp; Gratifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white border radius-12">
                            <div class="text-info text-2xl"><iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon></div>
                            <div>
                                <strong class="d-block text-sm text-dark">Layanan Konsultasi</strong>
                                <small class="text-slate-500">Pendampingan Pokja &amp; PPK</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="ecosystem-portal-card">
                    <div class="ecosystem-portal-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ecosystem-portal-header-icon">
                                <iconify-icon icon="solar:global-bold"></iconify-icon>
                            </div>
                            <div>
                                <h4 class="ecosystem-portal-title">Portal Mitra Ekosistem Nasional &amp; Daerah</h4>
                                <p class="ecosystem-portal-subtitle">Akses langsung ke sistem layanan pengadaan resmi pemerintah</p>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs fw-bold px-2 py-1 radius-999 d-none d-sm-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:verified-check-bold"></iconify-icon> Resmi
                        </span>
                    </div>

                    <div class="ecosystem-items-list">
                        <!-- Portal 1: LPSE Lampung -->
                        <a href="https://lpse.lampungprov.go.id" target="_blank" rel="noopener noreferrer" class="ecosystem-item-link" style="--item-accent: #0284c7; --item-bg: #eff6ff;">
                            <div class="ecosystem-item-icon">
                                <iconify-icon icon="solar:server-square-bold"></iconify-icon>
                            </div>
                            <div class="ecosystem-item-content">
                                <strong class="ecosystem-item-name">LPSE Provinsi Lampung</strong>
                                <span class="ecosystem-item-desc">Sistem Pengadaan Secara Elektronik (SPSE)</span>
                            </div>
                            <div class="ecosystem-item-action">
                                <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                            </div>
                        </a>

                        <!-- Portal 2: SiRUP LKPP -->
                        <a href="https://sirup.lkpp.go.id" target="_blank" rel="noopener noreferrer" class="ecosystem-item-link" style="--item-accent: #10b981; --item-bg: #ecfdf5;">
                            <div class="ecosystem-item-icon">
                                <iconify-icon icon="solar:document-add-bold"></iconify-icon>
                            </div>
                            <div class="ecosystem-item-content">
                                <strong class="ecosystem-item-name">SiRUP LKPP</strong>
                                <span class="ecosystem-item-desc">Sistem Informasi Rencana Umum Pengadaan</span>
                            </div>
                            <div class="ecosystem-item-action">
                                <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                            </div>
                        </a>

                        <!-- Portal 3: E-Katalog LKPP -->
                        <a href="https://e-katalog.lkpp.go.id" target="_blank" rel="noopener noreferrer" class="ecosystem-item-link" style="--item-accent: #f59e0b; --item-bg: #fffbeb;">
                            <div class="ecosystem-item-icon">
                                <iconify-icon icon="solar:cart-large-4-bold"></iconify-icon>
                            </div>
                            <div class="ecosystem-item-content">
                                <strong class="ecosystem-item-name">Katalog Elektronik (E-Katalog LKPP)</strong>
                                <span class="ecosystem-item-desc">Belanja pengadaan daring komoditas nasional &amp; lokal</span>
                            </div>
                            <div class="ecosystem-item-action">
                                <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                            </div>
                        </a>
                    </div>

                    <div class="ecosystem-portal-footer">
                        <iconify-icon icon="solar:shield-check-bold" class="text-success text-base flex-shrink-0"></iconify-icon>
                        <span>Tautan resmi terenkripsi SSL/TLS langsung ke domain pemerintah (.go.id).</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     10. CALL TO ACTION (CTA) BANNER
     ========================================================= -->
<section class="section-spacious bg-white">
    <div class="container">
        <div class="cta-banner-modern">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-1 radius-6 text-xs fw-bold mb-3 text-uppercase">Mari Bergabung Bersama BegawiPengadaan</span>
                    <h3 class="text-3xl fw-bold text-white mb-3">Tingkatkan Profesionalisme &amp; Kepatuhan Pengadaan Anda</h3>
                    <p class="text-slate-300 mb-0" style="max-width: 600px; font-size: 16px;">
                        Daftar akun sebagai aparatur pemerintah, staf pengadaan, atau penyedia barang/jasa untuk mengikuti kelas terstruktur, mengunduh template dokumen, dan memperoleh sertifikat kompetensi.
                    </p>
                </div>
                <div class="col-12 col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end">
                        <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-warning px-24 py-12 radius-10 fw-bold text-dark d-inline-flex align-items-center gap-2">
                            <span>Daftar Akun Baru</span>
                            <iconify-icon icon="solar:user-plus-bold"></iconify-icon>
                        </a>
                        <a href="<?= site_url('kontak') ?>" class="btn btn-outline-light px-20 py-12 radius-10 fw-semibold d-inline-flex align-items-center gap-2">
                            <span>Hubungi Kami</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->include('landing/components/home_promo_popup') ?>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic search form target switcher
        const searchForm = document.getElementById('heroSearchForm');
        const targetSelect = document.getElementById('heroSearchTarget');
        const searchInput = document.getElementById('heroSearchInput');

        if (searchForm && targetSelect) {
            targetSelect.addEventListener('change', function() {
                const base = '<?= site_url('') ?>';
                searchForm.action = base + '/' + this.value;
                if (this.value === 'regulasi') {
                    searchInput.name = 'q';
                    searchInput.placeholder = 'Cari nomor regulasi, kata kunci, atau perpres...';
                } else if (this.value === 'arena-belajar/course') {
                    searchInput.name = 'keyword';
                    searchInput.placeholder = 'Cari topik kursus, kelas, atau silabus PBJ...';
                } else if (this.value === 'event') {
                    searchInput.name = 'q';
                    searchInput.placeholder = 'Cari webinar, bimtek, atau agenda PBJ...';
                } else if (this.value === 'verifikasi-sertifikat') {
                    searchInput.name = 'search';
                    searchInput.placeholder = 'Masukkan nomor sertifikat (BGW-...) atau NIK...';
                } else {
                    searchInput.name = 'q';
                    searchInput.placeholder = 'Cari artikel, template KAK/HPS, atau studi kasus...';
                }
            });
        }
    });

    // Testimonial & Team Data Store for Interactive Modal
    const testimonyData = {
        't1': {
            name: 'Ahmad Zulkarnaen, S.T., M.T.',
            org: 'PPK Dinas Bina Marga & Bina Konstruksi Provinsi Lampung',
            role: 'Pejabat Pembuat Komitmen (PPK)',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-1-1.jpg') ?>',
            avatar: 'AZ',
            bg: '#0284c7',
            quote: 'Penyusunan HPS dan spesifikasi teknis untuk paket infrastruktur jalan kini jauh lebih terstandarisasi berkat template dokumen di Bunga Rampai dan sinkronisasi regulasi terpusat. Risiko deviasi hukum saat audit probity berkurang drastis, sehingga eksekusi program fisik berjalan tepat sasaran.',
            impact: 'Memangkas waktu persiapan lelang hingga 55% & nihil temuan administratif saat audit probity.'
        },
        't2': {
            name: 'Rina Widyastuti, S.E., M.Si.',
            org: 'Anggota Pokja Pemilihan UKPBJ Setda Provinsi Lampung',
            role: 'Pokja Pemilihan / UKPBJ',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-1-2.jpg') ?>',
            avatar: 'RW',
            bg: '#10b981',
            quote: 'Arena Belajar dan simulasi evaluasi penawaran sangat membantu peningkatan kapasitas anggota Pokja. Ujian sertifikasi dan materi terstruktur meningkatkan kepercayaan diri kami dalam tender bernilai strategis di tingkat provinsi tanpa hambatan sanggahan banding.',
            impact: 'Evaluasi tender selesai tepat waktu 100% dan bebas sanggahan banding pemohon.'
        },
        't3': {
            name: 'Hendra Kusuma, S.Kom.',
            org: 'Direktur PT Lampung Cipta Mandiri (Penyedia E-Katalog)',
            role: 'Mitra Penyedia & UMKM',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-2-1.jpg') ?>',
            avatar: 'HK',
            bg: '#f59e0b',
            quote: 'Sebagai pelaku usaha lokal di Lampung, portal BegawiPengadaan membuka transparansi penuh. Kami mudah memahami kriteria kualifikasi, etika penawaran, hingga tata cara etalase produk pada e-katalog lokal Pemprov Lampung secara adil.',
            impact: 'Produk lokal UMKM terserap optimal dalam belanja APBD Pemprov Lampung.'
        },
        't4': {
            name: 'Drs. Bambang Irawan, Ak., CA.',
            org: 'Pengendali Teknis Inspektorat Provinsi Lampung',
            role: 'Pengawas APIP & Auditor',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-1-3.jpg') ?>',
            avatar: 'BI',
            bg: '#7c3aed',
            quote: 'BegawiPengadaan memfasilitasi audit kepatuhan secara objektif. Riwayat versi regulasi dan standar dokumen baku memudahkan kami memverifikasi kesesuaian prosedur dari tahap perencanaan sampai serah terima hasil pekerjaan.',
            impact: 'Penguatan integritas sistem dan pencegahan dini potensi deviasi PBJ.'
        },
        't5': {
            name: 'dr. Nurul Hidayati, M.Kes.',
            org: 'PPK Pengadaan Alkes RSUD Dr. H. Abdul Moeloek',
            role: 'PPK Sektor Kesehatan',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-2-2.jpg') ?>',
            avatar: 'NH',
            bg: '#ef4444',
            quote: 'Pengadaan obat dan alat medis memerlukan ketepatan waktu serta kepatuhan e-katalog nasional yang sangat ketat. Fitur konsultasi dan referensi regulasi di BegawiPengadaan memastikan ketersediaan logistik rumah sakit selalu terjamin.',
            impact: 'Ketepatan waktu pasokan medis mencapai 99,2% dengan tata kelola e-katalog akurat.'
        },
        't6': {
            name: 'Surya Darmawan, S.T.',
            org: 'Tim Teknis Dinas Perumahan & Kawasan Permukiman Lampung',
            role: 'Tim Teknis & Pengawas',
            photo: '<?= base_url('assets/main/images/testimonial/testimonial-2-3.jpg') ?>',
            avatar: 'SD',
            bg: '#06b6d4',
            quote: 'Standarisasi berita acara dan format pemeriksaan pekerjaan di Bunga Rampai mempermudah tim teknis melakukan serah terima fisik di lapangan secara presisi dan sesuai kontrak kerja.',
            impact: '100% berita acara serah terima terdokumentasi rapi dan terverifikasi digital.'
        },
        'tm1': {
            name: 'Dr. Ir. Fahrizal Darminto, M.A.',
            org: 'Sekretaris Daerah Provinsi Lampung',
            role: 'Pengarah Strategis PBJ',
            photo: '<?= base_url('assets/main/images/team/team-1-1.jpg') ?>',
            avatar: 'FD',
            bg: '#052244',
            quote: 'Penguatan tata kelola pengadaan barang dan jasa berbasis digital merupakan komitmen Pemprov Lampung guna memastikan belanja APBD yang transparan, akuntabel, dan berdampak langsung pada kesejahteraan masyarakat.',
            impact: 'Legitimasi kebijakan makro dan penguatan integritas belanja APBD Provinsi Lampung.'
        },
        'tm2': {
            name: 'Slamet Riadi, S.Sos., M.M.',
            org: 'Kepala Biro Pengadaan Barang & Jasa Setda Provinsi Lampung',
            role: 'Inisiator & Pembina Ekosistem',
            photo: '<?= base_url('assets/main/images/team/team-1-2.jpg') ?>',
            avatar: 'SR',
            bg: '#0369a1',
            quote: 'Menginisiasi arsitektur ekosistem BegawiPengadaan, memimpin transformasi UKPBJ Lampung menuju Level 3 (Proaktif) dan pusat keunggulan PBJ berstandar nasional yang transparan dan adaptif.',
            impact: 'Akselerasi kelembagaan UKPBJ Lampung meraih akreditasi Level 3 Proaktif LKPP.'
        },
        'tm3': {
            name: 'Software Engineering & UI/UX Crew',
            org: 'Tim Transformasi Digital BegawiPengadaan',
            role: 'Software Engineering & UX System',
            photo: '<?= base_url('assets/main/images/team/team-1-5.jpg') ?>',
            avatar: 'SE',
            bg: '#d97706',
            quote: 'Merancang arsitektur MVC CodeIgniter 4 yang gesit, integrasi database MySQL teroptimasi, tata letak responsif modern WowDash, serta antarmuka interaktif yang mudah diakses oleh seluruh OPD di Lampung.',
            impact: '100% Full-Stack Responsif, performa tinggi, dan kepatuhan standar keamanan siber SPSE.'
        },
        'tm4': {
            name: 'Tim LPSE & Pengelola SPSE',
            org: 'Biro Pengadaan Barang & Jasa Setda Provinsi Lampung',
            role: 'Infrastruktur & Layanan LPSE',
            photo: '<?= base_url('assets/main/images/team/team-1-4.jpg') ?>',
            avatar: 'LP',
            bg: '#0284c7',
            quote: 'Menjamin ketersediaan server 24/7 dengan standardisasi ISO 27001 dan rekomendasi BSSN, memastikan kelancaran seluruh proses tender elektronik tanpa downtime di seluruh kabupaten/kota se-Lampung.',
            impact: '99,9% Server Uptime dan perlindungan data transaksi pengadaan terenkripsi.'
        },
        'tm5': {
            name: 'Tim Pembinaan & Advokasi Hukum PBJ',
            org: 'Biro Pengadaan Barang & Jasa & Biro Hukum Setda Lampung',
            role: 'Advokasi & Regulasi PBJ',
            photo: '<?= base_url('assets/main/images/team/team-1-3.jpg') ?>',
            avatar: 'AH',
            bg: '#7c3aed',
            quote: 'Menyusun harmonisasi peraturan gubernur, telaah hukum pengadaan, mitigasi risiko sanggahan tender, serta penerbitan panduan teknis yang selaras dengan Perpres No. 12 Tahun 2021 dan aturan turunan LKPP.',
            impact: 'Kepastian hukum penuh bagi seluruh Pejabat Pembuat Komitmen dan Pokja Pemilihan.'
        },
        'tm6': {
            name: 'Tim Fasilitator & Master Trainer PBJ',
            org: 'Instruktur PBJ Terakreditasi LKPP Lampung',
            role: 'Instruktur & Penjamin Mutu',
            photo: '<?= base_url('assets/main/images/team/team-1-6.jpg') ?>',
            avatar: 'TF',
            bg: '#10b981',
            quote: 'Menyusun silabus pembelajaran berbasis kompetensi di Arena Belajar, bank soal ujian sertifikasi level-1, simulasi tender interaktif, serta panduan praktis penanganan sanggahan bagi aparatur se-Lampung.',
            impact: 'Standardisasi kompetensi ASN pengadaan di lingkungan Pemprov Lampung.'
        }
    };

    function openTestimonyModal(id) {
        const data = testimonyData[id];
        if (!data) return;

        document.getElementById('modalAuthorName').textContent = data.name;
        document.getElementById('modalAuthorOrg').textContent = data.org;
        document.getElementById('modalRoleBadge').textContent = data.role;
        document.getElementById('modalQuoteText').textContent = '“' + data.quote + '”';
        document.getElementById('modalImpactText').textContent = data.impact;

        const avatarImg = document.getElementById('modalAvatarImg');
        const avatarFallback = document.getElementById('modalAvatar');
        if (data.photo && avatarImg) {
            avatarImg.src = data.photo;
            avatarImg.style.display = 'block';
            if (avatarFallback) avatarFallback.style.display = 'none';
        } else if (avatarFallback) {
            avatarFallback.textContent = data.avatar || 'AZ';
            avatarFallback.style.background = data.bg || '#0c5aa6';
            avatarFallback.style.display = 'flex';
            if (avatarImg) avatarImg.style.display = 'none';
        }

        const modalEl = document.getElementById('testimonyDetailModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    // FiStudy Carousel Initialization for Testimonials & Team
    $(document).ready(function() {
        if (typeof $.fn.owlCarousel !== 'undefined') {
            if ($('.testimonial-one__carousel').length && !$('.testimonial-one__carousel').hasClass('owl-loaded')) {
                $('.testimonial-one__carousel').owlCarousel({
                    loop: true,
                    margin: 24,
                    nav: true,
                    dots: true,
                    smartSpeed: 500,
                    autoplay: true,
                    autoplayTimeout: 7500,
                    navText: [
                        '<span class="icon-arrow-left-up"></span>',
                        '<span class="icon-arrow-up-right-2"></span>',
                    ],
                    responsive: {
                        0: {
                            items: 1
                        },
                        768: {
                            items: 1
                        },
                        992: {
                            items: 1
                        },
                        1200: {
                            items: 1
                        },
                        1320: {
                            items: 1
                        }
                    }
                });
            }

            if ($('.team-spotlight__carousel').length && !$('.team-spotlight__carousel').hasClass('owl-loaded')) {
                $('.team-spotlight__carousel').owlCarousel({
                    loop: true,
                    margin: 24,
                    nav: true,
                    dots: true,
                    smartSpeed: 500,
                    autoplay: true,
                    autoplayTimeout: 8000,
                    navText: [
                        '<span class="icon-arrow-left-up"></span>',
                        '<span class="icon-arrow-up-right-2"></span>',
                    ],
                    responsive: {
                        0: {
                            items: 1
                        },
                        768: {
                            items: 1
                        },
                        992: {
                            items: 1
                        },
                        1200: {
                            items: 1
                        },
                        1320: {
                            items: 1
                        }
                    }
                });
            }
        }
    });
</script>
<?= $this->endSection() ?>