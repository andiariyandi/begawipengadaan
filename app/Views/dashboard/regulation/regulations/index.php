<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('pageStyles') ?>
<script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
<link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-home.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-home.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-home.css') : '2.0' ?>" />
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
        <?php if (!empty($sliders)): ?>
            <?php foreach ($sliders as $s): ?>
                <?php
                $rawImg = $s['image_url'] ?? 'assets/main/images/backgrounds/slider-1-1.png';
                $img = str_starts_with($rawImg, 'http') ? $rawImg : base_url($rawImg);
                $btnUrl = !empty($s['button_url'] ?? $s['primary_btn_url'] ?? null) ? (str_starts_with($s['button_url'] ?? $s['primary_btn_url'], 'http') ? ($s['button_url'] ?? $s['primary_btn_url']) : site_url($s['button_url'] ?? $s['primary_btn_url'])) : null;
                $secBtnUrl = !empty($s['secondary_button_url'] ?? $s['secondary_btn_url'] ?? null) ? (str_starts_with($s['secondary_button_url'] ?? $s['secondary_btn_url'], 'http') ? ($s['secondary_button_url'] ?? $s['secondary_btn_url']) : site_url($s['secondary_button_url'] ?? $s['secondary_btn_url'])) : null;
                $headingText = $s['title'] ?? $s['heading'] ?? 'BegawiPengadaan Lampung';
                $badgeText = $s['badge_text'] ?? $s['badge_title'] ?? '';
                $descText = $s['subtitle'] ?? $s['description'] ?? '';
                $btnText = $s['button_text'] ?? $s['primary_btn_text'] ?? '';
                $secBtnText = $s['secondary_button_text'] ?? $s['secondary_btn_text'] ?? '';

                // Cek ketersediaan berkas .webp fisik di server sebelum menyertakan tag <source>
                $hasWebp = false;
                $webpImg = null;
                if (! str_starts_with($rawImg, 'http') && preg_match('/\.(png|jpg|jpeg)$/i', $rawImg)) {
                    $webpRelative = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $rawImg);
                    $fullWebpPath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $webpRelative), DIRECTORY_SEPARATOR);
                    if (is_file($fullWebpPath)) {
                        $hasWebp = true;
                        $webpImg = base_url($webpRelative);
                    }
                }
                ?>
                <div class="item">
                    <div class="main-slider__image-block">
                        <?php if ($hasWebp && $webpImg): ?>
                            <picture>
                                <source srcset="<?= esc($webpImg) ?>" type="image/webp">
                                <img src="<?= esc($img) ?>" alt="<?= esc(strip_tags($headingText)) ?>" class="main-slider__hero-img" width="1920" height="500" />
                            </picture>
                        <?php else: ?>
                            <img src="<?= esc($img) ?>" alt="<?= esc(strip_tags($headingText)) ?>" class="main-slider__hero-img" width="1920" height="500" />
                        <?php endif; ?>
                    </div>
                    <div class="main-slider__caption-block">
                        <div class="container">
                            <div class="main-slider__caption-inner">
                                <div class="main-slider__caption-text">
                                    <?php if (!empty($badgeText)): ?>
                                        <?php $badgeDisplay = str_replace(['·', '•'], '&middot;', esc($badgeText)); ?>
                                        <div class="main-slider__sub-title-box">
                                            <div class="main-slider__sub-title-shape"></div>
                                            <h5 class="main-slider__sub-title"><?= $badgeDisplay ?></h5>
                                        </div>
                                    <?php endif; ?>
                                    <h2 class="main-slider__title">
                                        <?= $headingText ?>
                                    </h2>
                                    <?php if (!empty($descText)): ?>
                                        <p class="main-slider__text">
                                            <?= esc($descText) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="main-slider__btn-box">
                                    <?php if (!empty($btnText) && $btnUrl): ?>
                                        <a href="<?= esc($btnUrl) ?>" class="thm-btn">
                                            <span class="icon-angles-right"></span><?= esc($btnText) ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($secBtnText) && $secBtnUrl): ?>
                                        <a href="<?= esc($secBtnUrl) ?>" class="thm-btn thm-btn-alt">
                                            <span class="icon-angles-right"></span><?= esc($secBtnText) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Slide 1: Transformasi & Ekosistem PBJ Lampung -->
            <div class="item">
                <div class="main-slider__image-block">
                    <picture>
                        <source srcset="<?= base_url('assets/main/images/backgrounds/slider-1-1.webp') ?>" type="image/webp">
                        <img src="<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>" alt="BegawiPengadaan Lampung - Transformasi Digital Pengadaan" class="main-slider__hero-img" width="1920" height="500" />
                    </picture>
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
        <?php endif; ?>
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
                <div class="hero-search-input-wrap d-flex align-items-center flex-grow-1 gap-2">
                    <iconify-icon icon="solar:magnifer-linear" class="text-primary text-xl flex-shrink-0"></iconify-icon>
                    <input type="text" name="q" id="heroSearchInput" placeholder="Cari regulasi Standar, materi kursus, template KAK/HPS, atau artikel..." required autocomplete="off">
                </div>
                <!-- Class 'ignore' prevents niceSelect plugin from duplicating this dropdown -->
                <select id="heroSearchTarget" class="ignore hero-search-target-select" aria-label="Target Modul Pencarian">
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
            <div class="hero-quick-tags">
                <span class="quick-tags-label"><iconify-icon icon="solar:bolt-bold" class="text-warning"></iconify-icon> Pencarian Cepat:</span>
                <a href="<?= site_url('regulasi?q=Standar+46+Tahun+2025') ?>" class="quick-tag-pill">Standar 46/2025</a>
                <a href="<?= site_url('regulasi?q=Peraturan+LKPP') ?>" class="quick-tag-pill">Peraturan LKPP</a>
                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="quick-tag-pill">Template KAK &amp; HPS</a>
                <a href="<?= site_url('arena-belajar/course') ?>" class="quick-tag-pill">Course PBJ</a>
                <a href="<?= site_url('event') ?>" class="quick-tag-pill">Agenda Bimtek &amp; Webinar</a>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" class="quick-tag-pill">Verifikasi Sertifikat</a>
            </div>

            <!-- Live KPI Counter Ribbon (4 Cards) -->
            <div class="row g-3 pt-3 border-top gateway-kpi-grid">
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon kpi-icon--warning">
                            <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                        </div>
                        <div class="gateway-kpi-info">
                            <div class="gateway-kpi-num"><?= number_format((int) ($stats['total_regulations'] ?? 0)) ?></div>
                            <div class="gateway-kpi-label">Regulasi Terdata</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon kpi-icon--info">
                            <iconify-icon icon="solar:square-academic-cap-2-bold"></iconify-icon>
                        </div>
                        <div class="gateway-kpi-info">
                            <div class="gateway-kpi-num"><?= number_format((int) ($stats['total_courses'] ?? 0)) ?><?= ((int) ($stats['total_courses'] ?? 0)) > 0 ? '+' : '' ?></div>
                            <div class="gateway-kpi-label">Kursus &amp; Materi Belajar</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon kpi-icon--success">
                            <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                        </div>
                        <div class="gateway-kpi-info">
                            <div class="gateway-kpi-num"><?= number_format((int) ($stats['total_knowledge'] ?? 0)) ?><?= ((int) ($stats['total_knowledge'] ?? 0)) > 0 ? '+' : '' ?></div>
                            <div class="gateway-kpi-label">Artikel &amp; Template Dokumen</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="gateway-kpi-item">
                        <div class="gateway-kpi-icon kpi-icon--primary">
                            <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                        </div>
                        <div class="gateway-kpi-info">
                            <div class="gateway-kpi-num"><?= number_format((int) ($stats['total_users'] ?? 0)) ?><?= ((int) ($stats['total_users'] ?? 0)) > 0 ? '+' : '' ?></div>
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="pillar-icon-box">
                            <iconify-icon icon="solar:square-academic-cap-2-bold"></iconify-icon>
                        </div>
                        <span class="badge-soft-primary px-3 py-1 radius-999 text-xs fw-bold">E-Learning &amp; Sertifikasi</span>
                    </div>
                    <h3 class="pillar-title">Arena Belajar</h3>
                    <p class="pillar-desc">Pusat pelatihan mandiri berbasis e-learning, program sertifikasi PBJ, modul berjenjang, dan kelas interaktif bersama instruktur terakreditasi LKPP.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_courses'] ?? 0) ?></strong> Kursus</span>
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_programs'] ?? 0) ?></strong> Program</span>
                    </div>
                    <a href="<?= site_url('arena-belajar') ?>" class="pillar-link mt-auto">
                        <span>Eksplorasi Pembelajaran</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 2: Bunga Rampai -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pillar-card" style="--pillar-accent: #10b981; --pillar-bg: #ecfdf5;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="pillar-icon-box">
                            <iconify-icon icon="solar:book-bookmark-bold"></iconify-icon>
                        </div>
                        <span class="badge-soft-success px-3 py-1 radius-999 text-xs fw-bold">Dokumen &amp; Studi Kasus</span>
                    </div>
                    <h3 class="pillar-title">Bunga Rampai PBJ</h3>
                    <p class="pillar-desc">Ensiklopedia literasi praktis: artikel wawasan, template dokumen kerja (KAK, HPS, Kontrak), studi kasus penanganan sanggah, dan galeri multimedia.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_knowledge'] ?? 0) ?></strong> Artikel &amp; Template</span>
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_knowledge_categories'] ?? 0) ?></strong> Topik</span>
                    </div>
                    <a href="<?= site_url('bunga-rampai') ?>" class="pillar-link mt-auto">
                        <span>Buka Bunga Rampai</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 3: Regulasi PBJ -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="pillar-card" style="--pillar-accent: #f59e0b; --pillar-bg: #fffbeb;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="pillar-icon-box">
                            <iconify-icon icon="solar:scale-bold"></iconify-icon>
                        </div>
                        <span class="badge-soft-warning px-3 py-1 radius-999 text-xs fw-bold">Produk Hukum Resmi</span>
                    </div>
                    <h3 class="pillar-title">Regulasi &amp; Payung Hukum</h3>
                    <p class="pillar-desc">Koleksi terstruktur peraturan perundang-undangan (Standar, PerLKPP, Perda Lampung, Surat Edaran), riwayat naskah versi, dan silsilah relasi aturan hukum.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_regulations'] ?? 0) ?></strong> Regulasi</span>
                        <span class="pillar-stat-chip"><strong><?= (int) ($stats['total_regulation_categories'] ?? 0) ?></strong> Kategori</span>
                    </div>
                    <a href="<?= site_url('regulasi') ?>" class="pillar-link mt-auto">
                        <span>Pusat Regulasi PBJ</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 4: Klinik Konsultasi & FAQ -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="pillar-card" style="--pillar-accent: #8b5cf6; --pillar-bg: #f5f3ff;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="pillar-icon-box">
                            <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                        </div>
                        <span class="badge-soft-primary px-3 py-1 radius-999 text-xs fw-bold" style="background: #f3e8ff !important; color: #7e22ce !important; border-color: #e9d5ff !important;">Helpdesk &amp; Mitigasi</span>
                    </div>
                    <h3 class="pillar-title">Klinik Konsultasi &amp; Tanya Jawab</h3>
                    <p class="pillar-desc">Bimbingan teknis terarah dan tanya jawab spesifik mengenai persoalan regulasi, kendala pemilihan penyedia, penyesuaian harga, serta tata cara mitigasi risiko audit kepengadaan.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat-chip"><strong><?= count($faqs) ?></strong> FAQ Aktif</span>
                        <span class="pillar-stat-chip"><strong>24/7</strong> Helpdesk</span>
                    </div>
                    <a href="<?= site_url('bunga-rampai/faq') ?>" class="pillar-link mt-auto">
                        <span>Lihat FAQ &amp; Bimbingan</span>
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Pilar 5: Verifikasi Sertifikat Digital -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="pillar-card" style="--pillar-accent: #0284c7; --pillar-bg: #f0f9ff;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="pillar-icon-box">
                            <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                        </div>
                        <span class="badge-soft-info px-3 py-1 radius-999 text-xs fw-bold">Validasi QR Code</span>
                    </div>
                    <h3 class="pillar-title">Verifikasi Sertifikat Digital</h3>
                    <p class="pillar-desc">Validasi keabsahan sertifikat pelatihan, bukti tanda lulus, dan kompetensi yang diterbitkan resmi oleh Biro Pengadaan Barang dan Jasa Provinsi Lampung.</p>
                    <div class="pillar-stat-row">
                        <span class="pillar-stat-chip"><strong>QR Code</strong> Validasi</span>
                        <span class="pillar-stat-chip"><strong>SSL</strong> Aman Terenkripsi</span>
                    </div>
                    <a href="<?= site_url('verifikasi-sertifikat') ?>" class="pillar-link mt-auto">
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
                    <iconify-icon icon="solar:square-academic-cap-2-bold"></iconify-icon>
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
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-white border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                        <iconify-icon icon="solar:mortarboard-broken" class="text-secondary-light" style="font-size: 48px;"></iconify-icon>
                        <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Kursus Terbit</h5>
                        <p class="text-muted small mb-3">Materi pembelajaran dan silabus pelatihan saat ini sedang dipersiapkan oleh instruktur.</p>
                        <a href="<?= site_url('arena-belajar') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3">Buka Arena Belajar</a>
                    </div>
                </div>
            <?php else: ?>
                <?php $ci = 0;
                foreach ($courses as $c): ?>
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
                        $durationJp = max(1, learning_jp_count((int) $c['duration_minutes']));
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
                <?php $ci++;
                endforeach; ?>
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
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-white border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                        <iconify-icon icon="solar:document-text-broken" class="text-secondary-light" style="font-size: 48px;"></iconify-icon>
                        <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Dokumen Regulasi</h5>
                        <p class="text-muted small mb-3">Repositori produk hukum dan peraturan PBJ akan segera diperbarui.</p>
                        <a href="<?= site_url('regulasi') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3">Buka Pusat Regulasi</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($regulations as $r): ?>
                    <?php $legalStatus = strtolower((string) ($r['legal_status'] ?? 'active')); ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="reg-card-modern">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <?php if (in_array($legalStatus, ['active', 'berlaku'], true)): ?>
                                    <span class="reg-status-badge badge-soft-success active">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Berlaku
                                    </span>
                                <?php elseif (in_array($legalStatus, ['amended', 'diubah'], true)): ?>
                                    <span class="reg-status-badge badge-soft-warning amended">
                                        <iconify-icon icon="solar:pen-bold"></iconify-icon> Diubah
                                    </span>
                                <?php else: ?>
                                    <span class="reg-status-badge badge-soft-danger revoked">
                                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Dicabut
                                    </span>
                                <?php endif; ?>

                                <?php if (! empty($r['document_year'])): ?>
                                    <span class="badge-soft-secondary px-2 py-1 radius-6 text-2xs fw-bold">
                                        Tahun <?= esc($r['document_year']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <span class="reg-doc-num"><?= esc($r['document_number'] ?? 'Naskah PBJ') ?></span>

                            <?php if (! empty($r['category_name'])): ?>
                                <span class="badge-soft-info px-2 py-1 radius-6 text-2xs fw-bold mb-2 d-inline-block" style="width: fit-content;">
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
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= site_url('regulasi/' . esc($r['slug'])) ?>" class="btn btn-sm btn-outline-primary px-3 py-1 radius-6" title="Lihat Detail Naskah">Naskah</a>
                                    <a href="<?= site_url('regulasi/' . esc($r['slug']) . '/unduh') ?>" class="btn btn-sm btn-primary px-3 py-1 radius-6 d-inline-flex align-items-center gap-1" title="Unduh PDF">
                                        <iconify-icon icon="solar:download-minimalistic-bold"></iconify-icon>
                                        <span>Unduh</span>
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
                <span class="tab-counter"><?= count($articles) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-tpl-btn" data-bs-toggle="pill" data-bs-target="#tab-templates" type="button" role="tab">
                <iconify-icon icon="solar:file-download-bold"></iconify-icon>
                <span>Template Dokumen</span>
                <span class="tab-counter"><?= count($templates) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-cs-btn" data-bs-toggle="pill" data-bs-target="#tab-cases" type="button" role="tab">
                <iconify-icon icon="solar:shield-warning-bold"></iconify-icon>
                <span>Studi Kasus</span>
                <span class="tab-counter"><?= count($caseStudies) ?></span>
            </button>
            <button class="knowledge-tab-btn" id="tab-faq-btn" data-bs-toggle="pill" data-bs-target="#tab-faqs" type="button" role="tab">
                <iconify-icon icon="solar:question-circle-bold"></iconify-icon>
                <span>Tanya Jawab (FAQ)</span>
                <span class="tab-counter"><?= count($faqs) ?></span>
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
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge-soft-primary px-2 py-0 text-2xs fw-bold text-uppercase">Artikel PBJ</span>
                                            <span class="text-xs text-muted"><?= (int) ($art['reading_time'] ?? 5) ?> mnt</span>
                                        </div>
                                        <h4 class="text-sm fw-bold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                            <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="text-dark text-decoration-none"><?= esc($art['title']) ?></a>
                                        </h4>
                                        <p class="text-xs text-slate-500 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;"><?= esc($art['summary'] ?? '') ?></p>
                                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                                            <small class="text-xs text-muted"><?= esc($art['author_name'] ?? 'Biro PBJ') ?></small>
                                            <a href="<?= site_url('bunga-rampai/artikel/' . esc($art['slug'])) ?>" class="text-xs text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                                                <span>Baca</span>
                                                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
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
                        <div class="col-12 text-center py-5">
                            <div class="p-4 rounded-4 bg-light border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                                <iconify-icon icon="solar:document-add-broken" class="text-secondary-light" style="font-size: 44px;"></iconify-icon>
                                <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Template Dokumen</h5>
                                <p class="text-muted small mb-0">Template KAK, HPS, dan format kontrak kerja akan segera diunggah.</p>
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
                                            <span class="badge-soft-<?= $isExcel ? 'success' : 'primary' ?> px-2 py-0 text-2xs fw-bold">
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
                        <div class="col-12 text-center py-5">
                            <div class="p-4 rounded-4 bg-light border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                                <iconify-icon icon="solar:case-round-minimalistic-broken" class="text-secondary-light" style="font-size: 44px;"></iconify-icon>
                                <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Studi Kasus</h5>
                                <p class="text-muted small mb-0">Pembahasan studi kasus nyata pengadaan akan segera ditambahkan.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($caseStudies as $cs): ?>
                            <div class="col-12 col-md-6">
                                <div class="card p-24 radius-16 border bg-light h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge-soft-info px-2 py-1 text-xs fw-bold">
                                            <?= esc($cs['procurement_stage'] ?? 'Mitigasi Risiko PBJ') ?>
                                        </span>
                                        <span class="badge-soft-secondary px-2 py-1 text-2xs fw-bold">Studi Kasus</span>
                                    </div>
                                    <h5 class="text-base fw-bold mb-2"><?= esc($cs['title']) ?></h5>
                                    <p class="text-sm text-slate-600 mb-3"><?= esc($cs['summary'] ?? '') ?></p>
                                    <a href="<?= site_url('bunga-rampai/studi-kasus/' . esc($cs['slug'])) ?>" class="text-primary text-sm fw-bold text-decoration-none d-flex align-items-center gap-1 mt-auto">
                                        <span>Pelajari Solusi Kasus</span>
                                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
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
                        <div class="col-12 text-center py-5">
                            <div class="p-4 rounded-4 bg-light border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                                <iconify-icon icon="solar:chat-round-dots-broken" class="text-secondary-light" style="font-size: 44px;"></iconify-icon>
                                <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Tanya Jawab (FAQ)</h5>
                                <p class="text-muted small mb-0">Daftar pertanyaan umum seputar pengadaan barang dan jasa akan segera dimutakhirkan.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="accordion" id="homeFaqAccordion">
                                <?php foreach ($faqs as $i => $fq): ?>
                                    <div class="accordion-item radius-12 mb-2 border overflow-hidden">
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
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-white border d-inline-block text-center shadow-xs" style="max-width: 480px;">
                        <iconify-icon icon="solar:calendar-broken" class="text-secondary-light" style="font-size: 48px;"></iconify-icon>
                        <h5 class="fw-bold text-dark mt-2 mb-1">Belum Ada Agenda Mendatang</h5>
                        <p class="text-muted small mb-3">Jadwal webinar, bimbingan teknis, dan pelatihan pengadaan akan segera diumumkan.</p>
                        <a href="<?= site_url('event') ?>" class="btn btn-sm btn-outline-primary radius-8 px-3">Buka Halaman Event</a>
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
                        'online' => ['class' => 'badge-soft-success', 'label' => 'Online • Zoom'],
                        'offline' => ['class' => 'badge-soft-primary', 'label' => 'Tatap Muka • Luring'],
                        'hybrid' => ['class' => 'badge-soft-warning', 'label' => 'Hybrid • Luring & Daring'],
                        default => ['class' => 'badge-soft-secondary', 'label' => ucfirst($mode)]
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
                                <div class="d-flex align-items-center gap-1 mb-2 flex-wrap">
                                    <span class="<?= $modeBadge['class'] ?> px-2 py-0 text-2xs fw-bold">
                                        <?= $modeBadge['label'] ?>
                                    </span>
                                    <span class="badge-soft-secondary px-2 py-0 text-2xs fw-bold">
                                        <?= esc($typeLabel) ?>
                                    </span>
                                </div>
                                <h4 class="text-base fw-bold mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                    <a href="<?= site_url('event/' . esc($evt['slug'])) ?>" class="text-dark text-decoration-none">
                                        <?= esc($evt['title']) ?>
                                    </a>
                                </h4>
                                <p class="text-xs text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
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
     10. TESTIMONI PENGGUNA (TESTIMONIAL SPOTLIGHT)
     ========================================================= -->
<?php if (!empty($testimonials)): ?>
    <section class="testimonial-one section-testimonies" id="section-testimonies">
        <div class="container">
            <!-- Section Title & Single Nav Arrows Header -->
            <div class="testimony-header-wrap mb-4">
                <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                    <div style="max-width: 820px;">
                        <div class="section-title-tag">
                            <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                            <span>Suara Pemangku Kepentingan</span>
                        </div>
                        <h2 class="section-title-main mb-1">Testimoni &amp; Pengalaman Nyata Pengguna BegawiPengadaan</h2>
                        <p class="section-title-desc mb-0">Aspirasi, pengalaman, dan evaluasi langsung dari PPK, Pokja Pemilihan, Auditor APIP, dan Rekanan Penyedia di Provinsi Lampung.</p>
                    </div>
                    <!-- Single Nav Arrow Controller (No duplicates, no number pagination) -->
                    <div class="testimony-slider-nav d-inline-flex align-items-center gap-2 ms-auto">
                        <button type="button" class="team-nav-btn testimony-nav-prev" id="testimonySliderPrev" aria-label="Testimoni Sebelumnya">
                            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                        </button>
                        <button type="button" class="team-nav-btn testimony-nav-next" id="testimonySliderNext" aria-label="Testimoni Selanjutnya">
                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>


            <!-- Testimonial Carousel (Bubble Chat Design, Multi-Card, Loop) -->
            <div class="testimonial-one__inner">
                <div class="testimonial-one__carousel owl-theme owl-carousel">
                    <?php foreach ($testimonials as $tIdx => $t): ?>
                        <?php
                        $tId = 't' . ($tIdx + 1);
                        $avatar = str_starts_with($t['avatar_url'] ?? '', 'http') ? $t['avatar_url'] : base_url($t['avatar_url'] ?? 'assets/dashboard/images/users/user1.png');
                        $rating = !empty($t['rating']) ? (float) $t['rating'] : 5.0;
                        $tRole = $t['role_title'] ?? $t['role'] ?? '';
                        $tImpact = $t['impact_statement'] ?? $t['impact_metric'] ?? '';
                        $tCat = $t['category'] ?? $t['badge_category'] ?? '';
                        ?>
                        <div class="item">
                            <div class="testimony-chat-card testimony-card spotlight-card" onclick="openTestimonyModal('<?= $tId ?>')" role="button" tabindex="0" title="Klik untuk ulasan lengkap">
                                <!-- Chat Speech Bubble -->
                                <div class="testimony-bubble">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="testimony-stars d-flex align-items-center gap-1">
                                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                                <iconify-icon icon="solar:star-bold" class="<?= $star <= round($rating) ? 'text-warning' : 'text-slate-300' ?>"></iconify-icon>
                                            <?php endfor; ?>
                                            <span class="text-xs fw-bold text-dark ms-1"><?= number_format($rating, 1) ?></span>
                                        </div>
                                        <div class="testimony-quote-icon">
                                            <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                                        </div>
                                    </div>
                                    <p class="testimony-bubble__text">
                                        &ldquo;<?= esc($t['quote'] ?? '') ?>&rdquo;
                                    </p>
                                    <?php if (!empty($tImpact)): ?>
                                        <div class="testimony-bubble__impact mt-auto">
                                            <iconify-icon icon="solar:graph-up-bold" class="text-success text-xs"></iconify-icon>
                                            <span><?= esc($tImpact) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="testimony-bubble__tail"></div>
                                </div>

                                <!-- Identitas Penulis -->
                                <div class="testimony-author-bar d-flex align-items-center gap-3">
                                    <div class="testimony-author__avatar spotlight-photo-frame">
                                        <img src="<?= esc($avatar) ?>" alt="<?= esc($t['name'] ?? '') ?>" class="testimony-author__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                    </div>
                                    <div class="testimony-author__info">
                                        <h4 class="testimony-author__name"><?= esc($t['name'] ?? '') ?></h4>
                                        <span class="testimony-author__org"><?= esc($t['organization'] ?? '') ?></span>
                                        <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                            <?php if (!empty($tRole)): ?>
                                                <span class="badge-soft-primary px-2 py-0-5 radius-999 text-2xs fw-bold">
                                                    <iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon> <?= esc($tRole) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($tCat)): ?>
                                                <span class="badge-soft-success px-2 py-0-5 radius-999 text-2xs fw-bold d-none d-sm-inline-flex">
                                                    <iconify-icon icon="solar:verified-check-bold" class="me-1"></iconify-icon> <?= esc($tCat) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================
     11. TIM INOVATOR & PENGEMBANG (TEAM SPOTLIGHT)
     ========================================================= -->
<?php if (!empty($teamMembers)): ?>
    <section class="team-one section-team" id="section-team">
        <div class="container">
            <!-- Section Title & Single Nav Arrows Header -->
            <div class="team-header-wrap mb-4">
                <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                    <div style="max-width: 820px;">
                        <div class="section-title-tag">
                            <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                            <span>Kolaborator &amp; Pengembang</span>
                        </div>
                        <h2 class="section-title-main mb-1">Tim Inovator di Balik Ekosistem BegawiPengadaan</h2>
                        <p class="section-title-desc mb-0">Sinergi kepemimpinan daerah, birokrasi profesional, tim perekayasa perangkat lunak, dan fasilitator ahli pengadaan barang/jasa.</p>
                    </div>
                    <!-- Single Nav Arrow Controller (No duplicates, no number pagination) -->
                    <div class="team-slider-nav d-inline-flex align-items-center gap-2 ms-auto">
                        <button type="button" class="team-nav-btn team-nav-prev" id="teamSliderPrev" aria-label="Slide Tim Sebelumnya">
                            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                        </button>
                        <button type="button" class="team-nav-btn team-nav-next" id="teamSliderNext" aria-label="Slide Tim Selanjutnya">
                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Team Photo Card Slider Carousel (4-5 Cards Desktop, Loop, Photos with Badges) -->
            <div class="team-one__inner">
                <div class="team-spotlight__carousel owl-theme owl-carousel">
                    <?php foreach ($teamMembers as $mIdx => $m): ?>
                        <?php
                        $tmId = 'tm' . ($mIdx + 1);
                        $photo = str_starts_with($m['photo_url'] ?? '', 'http') ? $m['photo_url'] : base_url($m['photo_url'] ?? 'assets/dashboard/images/users/user1.png');
                        $soc = !empty($m['social_links']) ? (is_array($m['social_links']) ? $m['social_links'] : json_decode($m['social_links'], true)) : [];
                        $mBadge = $m['badge_category'] ?? $soc['badge'] ?? '';
                        $mRole = $m['role_title'] ?? $m['role'] ?? '';
                        ?>
                        <div class="item">
                            <div class="team-photo-card team-card spotlight-card" onclick="openTestimonyModal('<?= $tmId ?>')" role="button" tabindex="0" title="Klik untuk profil lengkap">
                                <div class="team-photo-card__frame spotlight-photo-frame">
                                    <img src="<?= esc($photo) ?>" alt="<?= esc($m['name'] ?? '') ?>" class="team-photo-card__img" loading="lazy" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                    <?php if (!empty($mBadge)): ?>
                                        <div class="team-photo-card__badges">
                                            <span class="team-photo-card__badge badge-soft-primary">
                                                <iconify-icon icon="solar:star-bold" class="text-warning"></iconify-icon>
                                                <span><?= esc($mBadge) ?></span>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="team-photo-card__overlay">
                                        <div class="team-photo-card__meta">
                                            <h4 class="team-photo-card__name"><?= esc($m['name'] ?? '') ?></h4>
                                            <span class="team-photo-card__role"><?= esc($mRole) ?></span>
                                        </div>
                                        <div class="team-photo-card__action">
                                            <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
<?php endif; ?>

<!-- Modal Detail Testimoni Interaktif -->
<div class="modal fade" id="testimonyDetailModal" tabindex="-1" aria-labelledby="testimonyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-soft-primary px-3 py-1 radius-999 text-xs fw-bold" id="modalRoleBadge">Peran</span>
                    <span class="badge-soft-success px-2 py-1 radius-999 text-2xs fw-bold"><iconify-icon icon="solar:verified-check-bold"></iconify-icon> Terverifikasi</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="" id="modalAvatarImg" class="showcase-avatar-photo" alt="" style="width: 54px; height: 54px; display: none; object-fit: cover; border-radius: 50%;">
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
                        <?php if (!empty($externalPortals)): ?>
                            <?php foreach ($externalPortals as $p): ?>
                                <?php
                                $pName = $p['name'] ?? $p['portal_name'] ?? '';
                                $pDesc = $p['description'] ?? '';
                                $pUrl = $p['url'] ?? '#';
                                $pIcon = !empty($p['logo_url']) ? $p['logo_url'] : (!empty($p['icon']) ? $p['icon'] : 'solar:global-bold');
                                $pCat = $p['category'] ?? 'nasional';
                                $pAccent = !empty($p['accent_color']) ? $p['accent_color'] : ($pCat === 'daerah' ? '#0284c7' : '#10b981');
                                $pBg = !empty($p['bg_color']) ? $p['bg_color'] : ($pCat === 'daerah' ? '#eff6ff' : '#ecfdf5');
                                ?>
                                <a href="<?= esc($pUrl) ?>" target="_blank" rel="noopener noreferrer" class="ecosystem-item-link" style="--item-accent: <?= esc($pAccent) ?>; --item-bg: <?= esc($pBg) ?>;">
                                    <div class="ecosystem-item-icon">
                                        <?php if (str_contains($pIcon, '/') || str_contains($pIcon, '.')): ?>
                                            <img src="<?= esc(str_starts_with($pIcon, 'http') ? $pIcon : base_url($pIcon)) ?>" alt="<?= esc($pName) ?>" style="width: 22px; height: 22px; object-fit: contain;">
                                        <?php else: ?>
                                            <iconify-icon icon="<?= esc($pIcon) ?>"></iconify-icon>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ecosystem-item-content">
                                        <strong class="ecosystem-item-name"><?= esc($pName) ?></strong>
                                        <span class="ecosystem-item-desc"><?= esc($pDesc) ?></span>
                                    </div>
                                    <div class="ecosystem-item-action">
                                        <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
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
                        <?php endif; ?>
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
                    searchInput.placeholder = 'Cari nomor regulasi, kata kunci, atau Standar...';
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
    <?php
    $modalItems = [];
    if (!empty($testimonials)) {
        foreach ($testimonials as $tIdx => $t) {
            $key = 't' . ($tIdx + 1);
            $avatar = str_starts_with($t['avatar_url'] ?? '', 'http') ? $t['avatar_url'] : base_url($t['avatar_url'] ?? 'assets/dashboard/images/users/user1.png');
            $words = explode(' ', trim($t['name'] ?? ''));
            $initials = strtoupper(substr($words[0] ?? 'A', 0, 1) . substr($words[1] ?? 'Z', 0, 1));
            $modalItems[$key] = [
                'name'   => $t['name'] ?? '',
                'org'    => $t['organization'] ?? '',
                'role'   => $t['role_title'] ?? $t['role'] ?? '',
                'photo'  => $avatar,
                'avatar' => $initials,
                'bg'     => '#0284c7',
                'quote'  => $t['quote'] ?? '',
                'impact' => $t['impact_statement'] ?? $t['impact_metric'] ?? '',
            ];
        }
    }
    if (!empty($teamMembers)) {
        foreach ($teamMembers as $mIdx => $m) {
            $key = 'tm' . ($mIdx + 1);
            $photo = str_starts_with($m['photo_url'] ?? '', 'http') ? $m['photo_url'] : base_url($m['photo_url'] ?? 'assets/dashboard/images/users/user1.png');
            $words = explode(' ', trim($m['name'] ?? ''));
            $initials = strtoupper(substr($words[0] ?? 'A', 0, 1) . substr($words[1] ?? 'Z', 0, 1));
            $modalItems[$key] = [
                'name'   => $m['name'] ?? '',
                'org'    => $m['organization'] ?? $m['institution'] ?? 'Biro PBJ Setda Prov. Lampung',
                'role'   => $m['role_title'] ?? $m['role'] ?? '',
                'photo'  => $photo,
                'avatar' => $initials,
                'bg'     => '#052244',
                'quote'  => $m['bio'] ?? $m['quote'] ?? '',
                'impact' => $m['impact_statement'] ?? $m['impact_metric'] ?? '',
            ];
        }
    }
    ?>
    const testimonyData = <?= json_encode($modalItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

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
            if ($('.testimonial-one__carousel').length) {
                if ($('.testimonial-one__carousel').hasClass('owl-loaded')) {
                    $('.testimonial-one__carousel').trigger('destroy.owl.carousel');
                    $('.testimonial-one__carousel').removeClass('owl-loaded');
                }
                const testimonyCarousel = $('.testimonial-one__carousel').owlCarousel({
                    loop: true,
                    margin: 20,
                    nav: false,
                    dots: false,
                    smartSpeed: 600,
                    autoplay: true,
                    autoplayTimeout: 4500,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 1,
                            margin: 14
                        },
                        576: {
                            items: 1.2,
                            margin: 16
                        },
                        768: {
                            items: 2,
                            margin: 18
                        },
                        1024: {
                            items: 3,
                            margin: 20
                        },
                        1400: {
                            items: 3,
                            margin: 24
                        }
                    }
                });

                $('#testimonySliderPrev').on('click', function() {
                    testimonyCarousel.trigger('prev.owl.carousel');
                });
                $('#testimonySliderNext').on('click', function() {
                    testimonyCarousel.trigger('next.owl.carousel');
                });
            }

            if ($('.team-spotlight__carousel').length) {
                if ($('.team-spotlight__carousel').hasClass('owl-loaded')) {
                    $('.team-spotlight__carousel').trigger('destroy.owl.carousel');
                    $('.team-spotlight__carousel').removeClass('owl-loaded');
                }
                const teamCarousel = $('.team-spotlight__carousel').owlCarousel({
                    loop: true,
                    margin: 18,
                    nav: false,
                    dots: false,
                    smartSpeed: 600,
                    autoplay: true,
                    autoplayTimeout: 4000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 1.2,
                            margin: 12
                        },
                        480: {
                            items: 2,
                            margin: 14
                        },
                        768: {
                            items: 3,
                            margin: 16
                        },
                        992: {
                            items: 4,
                            margin: 18
                        },
                        1200: {
                            items: 5,
                            margin: 20
                        }
                    }
                });

                $('#teamSliderPrev').on('click', function() {
                    teamCarousel.trigger('prev.owl.carousel');
                });
                $('#teamSliderNext').on('click', function() {
                    teamCarousel.trigger('next.owl.carousel');
                });
            }
        }
    });
</script>
<?= $this->endSection() ?>