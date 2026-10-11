<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?php
$items       = $items ?? [];
$catalogType = $catalogType ?? 'articles';
$totalItems  = $totalItems ?? count($items);
$pager       = $pager ?? service('pager');
$q           = $q ?? '';
$featuredFilter = $featuredFilter ?? '';
$stats       = $stats ?? [
    'total_articles'  => 0,
    'total_faqs'      => 0,
    'total_cases'     => 0,
    'total_templates' => 0,
    'total_media'     => 0,
    'total_videos'    => 0,
    'total_gallery'   => 0,
    'total_all'       => 0,
];

// Contextual Hero Text per Sub-Menu
$heroHeadings = [
    'articles' => [
        'tag'   => 'Wawasan & Analisis Regulasi Pengadaan',
        'title' => 'Katalog Artikel PBJ',
        'desc'  => 'Kumpulan artikel analisis regulasi, tata kelola pengadaan, mitigasi risiko audit, dan kiat teknis dari praktisi kepengadaan.',
        'searchPlaceholder' => 'Cari judul artikel, topik regulasi, atau isi konten...',
        'searchUrl' => site_url('bunga-rampai/artikel'),
    ],
    'faqs' => [
        'tag'   => 'Pusat Tanya Jawab Pengadaan Pemerintah',
        'title' => 'Tanya Jawab (FAQ) PBJ',
        'desc'  => 'Jawaban ringkas dan berlandaskan regulasi LKPP atas problematika teknis yang sering dihadapi PPK, Pokja, dan penyedia.',
        'searchPlaceholder' => 'Cari pertanyaan atau kata kunci regulasi...',
        'searchUrl' => site_url('bunga-rampai/faq'),
    ],
    'cases' => [
        'tag'   => 'Pembelajaran Berharga dari Dinamika Riil',
        'title' => 'Studi Kasus Lapangan',
        'desc'  => 'Kajian studi kasus penanganan sanggahan tender, pengendalian kontrak kritis, sengketa konstruksi, dan mitigasi temuan audit.',
        'searchPlaceholder' => 'Cari kasus pengadaan atau topik sengketa...',
        'searchUrl' => site_url('bunga-rampai/studi-kasus'),
    ],
    'templates' => [
        'tag'   => 'Format Baku & Formulir Siap Pakai',
        'title' => 'Template Dokumen PBJ',
        'desc'  => 'Standar format KAK, kertas kerja penyusunan HPS, surat perjanjian/SPK, formulir evaluasi pokja, dan BAST siap unduh.',
        'searchPlaceholder' => 'Cari nama dokumen atau jenis formulir...',
        'searchUrl' => site_url('bunga-rampai/template-dokumen'),
    ],
    'media' => [
        'tag'   => 'Infografik & Materi Visual Edukatif',
        'title' => 'Media &amp; Infografik PBJ',
        'desc'  => 'Diagram alir proses pengadaan, infografik siklus tender, cheat sheet TKDN, dan materi visual kepengadaan yang mudah dipahami.',
        'searchPlaceholder' => 'Cari infografik atau bagan alur...',
        'searchUrl' => site_url('bunga-rampai/media'),
    ],
    'videos' => [
        'tag'   => 'Sajian Video Interaktif & Webinar',
        'title' => 'Video Pembelajaran PBJ',
        'desc'  => 'Tutorial teknis aplikasi SPSE & e-Katalog, rekaman webinar bedah regulasi, dan materi bimbingan teknis daring.',
        'searchPlaceholder' => 'Cari judul video tutorial atau webinar...',
        'searchUrl' => site_url('bunga-rampai/video'),
    ],
    'gallery' => [
        'tag'   => 'Dokumentasi Kolaborasi Ekosistem PBJ',
        'title' => 'Galeri Dokumentasi Kegiatan',
        'desc'  => 'Dokumentasi foto kegiatan bimbingan teknis, workshop e-katalog UMKM, rapat koordinasi, dan monitoring pengadaan se-Provinsi Lampung.',
        'searchPlaceholder' => 'Cari dokumentasi agenda atau nama kegiatan...',
        'searchUrl' => site_url('bunga-rampai/galeri'),
    ],
];

$heroMeta = $heroHeadings[$catalogType] ?? [
    'tag'   => 'Pusat Pengetahuan PBJ Lampung',
    'title' => esc($pageTitle),
    'desc'  => esc($description),
    'searchPlaceholder' => 'Cari di repositori Bunga Rampai...',
    'searchUrl' => current_url(),
];

$detailRouteMap = [
    'articles'  => 'public.knowledge.article.detail',
    'cases'     => 'public.knowledge.case-study.detail',
    'templates' => 'public.knowledge.templates.detail',
    'faqs'      => null,
    'media'     => null,
    'videos'    => null,
    'gallery'   => null,
];
$detailRoute = $detailRouteMap[$catalogType] ?? null;
?>

<!-- =========================================================================
     PAGE HERO BUNGA RAMPAI (UNIFIED FI-STUDY DNA)
     ========================================================================= -->
<section class="arena-hero position-relative">
    <div class="arena-hero__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="arena-hero__overlay"></div>
    <div class="container">
        <div class="arena-hero__inner text-center pt-2 pt-md-4">
            <span class="arena-section-head__tag text-white mb-3" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32); backdrop-filter: blur(6px);">
                <i class="fas fa-book-reader me-1"></i> <?= esc($heroMeta['tag']) ?>
            </span>
            <h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                <?= $heroMeta['title'] ?>
            </h1>
            <p class="mx-auto mb-4" style="max-width: 720px; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.92); text-shadow: 0 1px 5px rgba(0,0,0,0.35);">
                <?= $heroMeta['desc'] ?>
            </p>

            <!-- Search Bar -->
            <div class="arena-search-bar mb-4">
                <form action="<?= esc($heroMeta['searchUrl']) ?>" method="get" class="d-flex align-items-center">
                    <i class="fas fa-search text-muted me-2" style="font-size: 16px;"></i>
                    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="<?= esc($heroMeta['searchPlaceholder']) ?>" autocomplete="off">
                    <?php if ($featuredFilter === '1'): ?>
                        <input type="hidden" name="featured" value="1">
                    <?php endif; ?>
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
            <nav class="arena-nav-pills" aria-label="Kategori Bunga Rampai">
                <a href="<?= site_url('bunga-rampai') ?>" class="arena-nav-pill">
                    <i class="fas fa-th-large"></i>
                    <span>Semua Eksplorasi</span>
                    <span class="badge"><?= (int) ($stats['total_all'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/artikel') ?>" class="arena-nav-pill <?= $catalogType === 'articles' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Artikel PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_articles'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/faq') ?>" class="arena-nav-pill <?= $catalogType === 'faqs' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>Tanya Jawab (FAQ)</span>
                    <span class="badge"><?= (int) ($stats['total_faqs'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/studi-kasus') ?>" class="arena-nav-pill <?= $catalogType === 'cases' ? 'active' : '' ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Studi Kasus</span>
                    <span class="badge"><?= (int) ($stats['total_cases'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/template-dokumen') ?>" class="arena-nav-pill <?= $catalogType === 'templates' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Template Dokumen</span>
                    <span class="badge"><?= (int) ($stats['total_templates'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/media') ?>" class="arena-nav-pill <?= $catalogType === 'media' ? 'active' : '' ?>">
                    <i class="fas fa-photo-video"></i>
                    <span>Media &amp; Infografik</span>
                    <span class="badge"><?= (int) ($stats['total_media'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/video') ?>" class="arena-nav-pill <?= $catalogType === 'videos' ? 'active' : '' ?>">
                    <i class="fas fa-video"></i>
                    <span>Video PBJ</span>
                    <span class="badge"><?= (int) ($stats['total_videos'] ?? 0) ?></span>
                </a>
                <a href="<?= site_url('bunga-rampai/galeri') ?>" class="arena-nav-pill <?= $catalogType === 'gallery' ? 'active' : '' ?>">
                    <i class="fas fa-images"></i>
                    <span>Galeri Kegiatan</span>
                    <span class="badge"><?= (int) ($stats['total_gallery'] ?? 0) ?></span>
                </a>
            </nav>
        </div>
    </div>
</section>

<!-- =========================================================================
     FILTER & CONTROLS TOOLBAR
     ========================================================================= -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">
                    Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $totalItems ?></strong> <?= esc($pageTitle) ?>
                </span>
                <?php if ($q !== ''): ?>
                    <span class="badge bg-light text-dark border">Kata kunci: "<?= esc($q) ?>"</span>
                <?php endif; ?>
                <?php if ($featuredFilter === '1'): ?>
                    <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i> Hanya Unggulan</span>
                <?php endif; ?>
            </div>

            <!-- Filter Pills (for articles, cases, templates) -->
            <?php if (in_array($catalogType, ['articles', 'cases', 'templates'], true)): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small me-1">Filter:</span>
                    <a href="<?= esc($heroMeta['searchUrl']) . ($q !== '' ? '?q=' . urlencode($q) : '') ?>" class="btn btn-sm <?= $featuredFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?> radius-20 px-3 py-1">
                        Semua
                    </a>
                    <a href="<?= esc($heroMeta['searchUrl']) . '?featured=1' . ($q !== '' ? '&q=' . urlencode($q) : '') ?>" class="btn btn-sm <?= $featuredFilter === '1' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> radius-20 px-3 py-1">
                        <i class="fas fa-star me-1"></i> Pilihan Editor
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     CATALOG CONTENT SECTION (TAILORED PER SUB-MENU)
     ========================================================================= -->
<section class="py-5" style="background: #f8fafc; min-height: 55vh;">
    <div class="container">
        <?php if ($msg = session()->getFlashdata('success')): ?>
            <div class="alert alert-success radius-8 mb-4"><?= esc($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-8 mb-4"><?= esc($msg) ?></div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <!-- Polished Empty State -->
            <div class="card border-0 shadow-sm radius-16 p-5 text-center my-4 bg-white">
                <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3 radius-50" style="width: 72px; height: 72px; background: #f1f5f9; color: #94a3b8; font-size: 32px;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: #082f5c;">Tidak Ada Konten Ditemukan</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 14.5px;">
                    <?php if ($q !== ''): ?>
                        Tidak ada konten yang sesuai dengan pencarian "<strong><?= esc($q) ?></strong>". Silakan coba kata kunci lain.
                    <?php else: ?>
                        Belum ada konten yang dipublikasikan pada kategori <?= esc($pageTitle) ?>.
                    <?php endif; ?>
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <?php if ($q !== '' || $featuredFilter !== ''): ?>
                        <a href="<?= esc($heroMeta['searchUrl']) ?>" class="btn btn-primary radius-8 px-4 py-2 fw-semibold">
                            <i class="fas fa-undo me-1"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                    <a href="<?= site_url('bunga-rampai') ?>" class="btn btn-outline-secondary radius-8 px-4 py-2 fw-semibold">
                        Kembali ke Beranda Bunga Rampai
                    </a>
                </div>
            </div>

        <?php else: ?>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 1: ARTIKEL PBJ
                 ----------------------------------------------------------- -->
            <?php if ($catalogType === 'articles'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $index => $item):
                        $coverImg = ! empty($item['cover_file_id'])
                            ? site_url('file/' . (int) $item['cover_file_id'])
                            : base_url('assets/main/images/blog/blog-2-' . (((int) $item['id'] % 4) + 1) . '.jpg');
                        $detailUrl = route_to('public.knowledge.article.detail', $item['slug']);
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                                <div class="position-relative" style="height: 190px; overflow: hidden; background: #082f5c;">
                                    <img src="<?= esc($coverImg) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-primary text-white radius-6 px-2 py-1 small fw-semibold">
                                            <i class="fas fa-newspaper me-1"></i> Artikel PBJ
                                        </span>
                                    </div>
                                    <?php if ((int) ($item['featured'] ?? 0) === 1): ?>
                                        <div class="position-absolute top-0 end-0 m-3">
                                            <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                                <i class="fas fa-star text-warning me-1"></i> Unggulan
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 12.5px;">
                                        <span><i class="fas fa-user-circle me-1"></i> <?= esc($item['author_name'] ?? 'Biro PBJ') ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-calendar-alt me-1"></i> <?= ! empty($item['published_at']) ? esc(date('d M Y', strtotime($item['published_at']))) : 'Terbaru' ?></span>
                                        <span>&middot;</span>
                                        <span><i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt</span>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="font-size: 17px; line-height: 1.45; color: #082f5c;">
                                        <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h4>
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['summary'] ?? '') ?>
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-primary radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                            <span>Baca Artikel</span>
                                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                        </a>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_article',
                                            'id'    => (int) $item['id'],
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 2: TANYA JAWAB (FAQ)
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'faqs'): ?>
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="accordion d-flex flex-column gap-3" id="faqCatalogAccordion">
                            <?php foreach ($items as $index => $item):
                                $collapseId = 'catalogFaq' . (int) $item['id'];
                                $headingId  = 'catalogHeading' . (int) $item['id'];
                            ?>
                                <div class="card border-0 shadow-sm radius-12 overflow-hidden bg-white">
                                    <div class="card-header bg-white border-0 p-3 p-md-4" id="<?= esc($headingId) ?>">
                                        <button class="btn btn-link w-100 text-start text-decoration-none p-0 d-flex align-items-center justify-content-between <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= esc($collapseId) ?>">
                                            <span class="fw-bold pe-3" style="color: #082f5c; font-size: 16px; line-height: 1.45;">
                                                <i class="fas fa-question-circle text-primary me-2"></i> <?= esc($item['title']) ?>
                                            </span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 14px;"></i>
                                        </button>
                                    </div>
                                    <div id="<?= esc($collapseId) ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#faqCatalogAccordion">
                                        <div class="card-body pt-0 px-3 px-md-4 pb-3 pb-md-4 text-muted" style="font-size: 14.5px; line-height: 1.65; border-top: 1px dashed #edf2f7;">
                                            <div class="mb-3">
                                                <?= nl2br(esc($item['content'] ?? $item['summary'] ?? 'Belum ada penjelasan jawaban.')) ?>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                                <span class="badge bg-light text-primary border px-2 py-1 small">
                                                    <i class="fas fa-check-circle me-1"></i> Regulasi PBJ
                                                </span>
                                                <?= view('landing/components/bookmark_button', [
                                                    'type'  => 'knowledge_faq',
                                                    'id'    => (int) $item['id'],
                                                    'title' => $item['title'] ?? null,
                                                    'size'  => 'sm',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- FAQ Helpdesk Banner -->
                        <div class="card border-0 radius-16 p-4 mt-5 text-white" style="background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h4 class="text-white fw-bold mb-1" style="font-size: 18px;">Punya Pertanyaan Spesifik Terkait Pengadaan?</h4>
                                    <p class="mb-0 text-white-80 small" style="color: rgba(255,255,255,0.85);">Tim Pembinaan &amp; Advokasi PBJ Biro Pengadaan Provinsi Lampung siap membantu konsultasi Anda.</p>
                                </div>
                                <a href="<?= site_url('kontak') ?>" class="btn btn-warning text-dark fw-bold radius-8 px-4 py-2">
                                    <i class="fas fa-headset me-1"></i> Hubungi Konsultasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 3: STUDI KASUS
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'cases'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $item):
                        $detailUrl = route_to('public.knowledge.case-study.detail', $item['slug']);
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #edf2f7 !important;">
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="badge radius-6 px-2 py-1 small fw-semibold" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3;">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Kasus Lapangan
                                        </span>
                                        <span class="text-muted small" style="font-size: 12px;">
                                            <i class="far fa-clock me-1"></i> <?= esc($item['reading_time'] ?? 5) ?> mnt
                                        </span>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="font-size: 17px; line-height: 1.45; color: #082f5c;">
                                        <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h4>
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['summary'] ?? '') ?>
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-danger radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                            <span>Pelajari Kasus</span>
                                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                        </a>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_case',
                                            'id'    => (int) $item['id'],
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 4: TEMPLATE DOKUMEN
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'templates'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $item):
                        $ext = strtoupper($item['file_extension'] ?? 'DOCX');
                        $extBadgeColor = match ($ext) {
                            'XLSX', 'XLS' => 'background: #dcfce7; color: #166534;',
                            'PDF'         => 'background: #fee2e2; color: #991b1b;',
                            default       => 'background: #e0e7ff; color: #3730a3;',
                        };
                        $detailUrl   = route_to('public.knowledge.templates.detail', $item['slug']);
                        $downloadUrl = route_to('public.knowledge.templates.download', $item['slug']);
                    ?>
                        <div class="col-xl-6">
                            <article class="card h-100 border-0 shadow-sm radius-16 p-4 d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-center radius-12 flex-shrink-0 fw-bold shadow-sm" style="width: 54px; height: 54px; <?= esc($extBadgeColor) ?>; font-size: 14px;">
                                        <?= esc($ext) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-light text-primary border px-2 py-0 small" style="font-size: 11px;">Standar PBJ</span>
                                            <?php if (! empty($item['cover_file_id'])): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0 small" style="font-size: 11px;">
                                                    <i class="fas fa-check-circle me-1"></i> File Siap Unduh
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="fw-bold mb-1" style="font-size: 16.5px; color: #082f5c; line-height: 1.4;">
                                            <a href="<?= esc($detailUrl) ?>" class="text-decoration-none text-reset hover-primary">
                                                <?= esc($item['title']) ?>
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($item['summary'] ?? '') ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm btn-primary radius-8 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-download"></i> Unduh Berkas
                                        </a>
                                        <a href="<?= esc($detailUrl) ?>" class="btn btn-sm btn-outline-secondary radius-8 px-3 py-2 fw-semibold">
                                            <i class="fas fa-info-circle me-1"></i> Detail
                                        </a>
                                    </div>
                                    <?= view('landing/components/bookmark_button', [
                                        'type'  => 'knowledge_template',
                                        'id'    => (int) $item['id'],
                                        'title' => $item['title'] ?? null,
                                        'size'  => 'sm',
                                    ]) ?>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 5: MEDIA & INFOGRAFIK
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'media'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $item): ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                                <div class="position-relative d-flex align-items-center justify-content-center text-white" style="height: 190px; background: linear-gradient(135deg, #082f5c 0%, #1e3a8a 100%);">
                                    <i class="fas fa-chart-pie" style="font-size: 52px; opacity: 0.35;"></i>
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-info text-dark radius-6 px-2 py-1 small">
                                        <i class="fas fa-image me-1"></i> Infografik PBJ
                                    </span>
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <h4 class="fw-bold mb-2" style="font-size: 16.5px; line-height: 1.45; color: #082f5c;">
                                        <?= esc($item['title'] ?? '(Tanpa judul)') ?>
                                    </h4>
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['caption'] ?? '') ?>
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <span class="badge bg-light text-muted border px-2 py-1 small">Materi Visual</span>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_media',
                                            'id'    => (int) $item['id'],
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 6: VIDEO PBJ
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'videos'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $item): ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                                <div class="position-relative d-flex align-items-center justify-content-center text-white" style="height: 190px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                                    <div class="d-flex align-items-center justify-content-center radius-50" style="width: 58px; height: 58px; background: rgba(220, 53, 69, 0.95); box-shadow: 0 0 20px rgba(220, 53, 69, 0.5);">
                                        <i class="fas fa-play text-white ms-1" style="font-size: 20px;"></i>
                                    </div>
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-danger text-white radius-6 px-2 py-1 small">
                                        <i class="fab fa-youtube me-1"></i> Video PBJ
                                    </span>
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <h4 class="fw-bold mb-2" style="font-size: 16.5px; line-height: 1.45; color: #082f5c;">
                                        <?= esc($item['title'] ?? '(Tanpa judul)') ?>
                                    </h4>
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= esc($item['caption'] ?? '') ?>
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <?php if (! empty($item['external_url'])): ?>
                                            <a href="<?= esc($item['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger radius-8 px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                                <i class="fas fa-play"></i> Tonton di YouTube
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Tautan belum tersedia</span>
                                        <?php endif; ?>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_media',
                                            'id'    => (int) $item['id'],
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- -----------------------------------------------------------
                 CATALOG TYPE 7: GALERI DOKUMENTASI
                 ----------------------------------------------------------- -->
            <?php elseif ($catalogType === 'gallery'): ?>
                <div class="row g-4">
                    <?php foreach ($items as $index => $item):
                        $thumb = base_url('assets/main/images/gallery/gallery-page-1-' . (($index % 4) + 1) . '.jpg');
                    ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden d-flex flex-column transition-hover bg-white" style="border: 1px solid #edf2f7 !important;">
                                <div class="position-relative" style="height: 200px; overflow: hidden;">
                                    <img src="<?= esc($thumb) ?>" alt="<?= esc($item['title'] ?? 'Galeri') ?>" class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-dark bg-opacity-75 text-white radius-6 px-2 py-1 small">
                                            <i class="fas fa-camera me-1"></i> Dokumentasi
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="fw-bold mb-2" style="font-size: 15px; line-height: 1.4; color: #082f5c;">
                                        <?= esc($item['title'] ?? '(Tanpa judul)') ?>
                                    </h5>
                                    <?php if (! empty($item['caption'])): ?>
                                        <p class="text-muted small mb-3 flex-grow-1" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($item['caption']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                        <span class="text-muted small" style="font-size: 11.5px;"><i class="far fa-calendar-alt me-1"></i> Kegiatan PBJ</span>
                                        <?= view('landing/components/bookmark_button', [
                                            'type'  => 'knowledge_media',
                                            'id'    => (int) $item['id'],
                                            'title' => $item['title'] ?? null,
                                            'size'  => 'sm',
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <!-- Fallback generic list -->
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($items as $item): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm radius-16 p-4 bg-white">
                                <h4 class="fw-bold mb-2" style="font-size: 17px; color: #082f5c;"><?= esc($item['title'] ?? '') ?></h4>
                                <p class="text-muted small mb-4"><?= esc($item['summary'] ?? $item['caption'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination Bar -->
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