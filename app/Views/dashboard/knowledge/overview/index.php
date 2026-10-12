<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb & Top Bar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Bunga Rampai') ?></h6>
        <p class="text-secondary-light mb-0">Pusat kendali pengetahuan, artikel praktik baik, FAQ, regulasi, template, dan media interaktif PBJ.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('bunga-rampai') ?>" target="_blank" class="btn btn-outline-primary radius-8 px-16 py-9 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:link-circle-bold" class="text-lg"></iconify-icon>
            <span>Lihat Halaman Publik</span>
        </a>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="card border-0 radius-16 shadow-sm mb-24 bg-primary-50 border-primary-100">
    <div class="card-body p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="w-36-px h-36-px bg-primary-600 text-white radius-8 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:bolt-bold" class="text-xl"></iconify-icon>
                </div>
                <div>
                    <h6 class="text-sm fw-bold text-primary-900 mb-0">Aksi Cepat Tambah Konten</h6>
                    <span class="text-xs text-secondary-light">Pilih modul untuk mempublikasikan materi baru</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= route_to('dashboard.knowledge.articles.create') ?>" class="btn btn-primary-600 radius-8 px-14 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:document-add-bold" class="text-base"></iconify-icon>
                + Tulis Artikel
            </a>
            <a href="<?= route_to('dashboard.knowledge.faqs.create') ?>" class="btn btn-outline-primary radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:question-circle-bold" class="text-base"></iconify-icon>
                + Tambah FAQ
            </a>
            <a href="<?= route_to('dashboard.knowledge.case-studies.create') ?>" class="btn btn-outline-danger radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:danger-bold" class="text-base"></iconify-icon>
                + Studi Kasus
            </a>
            <a href="<?= route_to('dashboard.knowledge.document-templates.create') ?>" class="btn btn-outline-success radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:file-download-bold" class="text-base"></iconify-icon>
                + Unggah Template
            </a>
            <a href="<?= site_url('dashboard/bunga-rampai/media-galeri/tambah?type=infographic') ?>" class="btn btn-outline-info radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:gallery-bold" class="text-base"></iconify-icon>
                + Media &amp; Infografik
            </a>
            <a href="<?= site_url('dashboard/bunga-rampai/media-galeri/tambah?type=youtube') ?>" class="btn btn-outline-danger radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:play-circle-bold" class="text-base"></iconify-icon>
                + Video PBJ
            </a>
            <a href="<?= site_url('dashboard/bunga-rampai/media-galeri/tambah?type=gallery') ?>" class="btn btn-outline-secondary radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="solar:camera-bold" class="text-base"></iconify-icon>
                + Galeri Kegiatan
            </a>
            <a href="<?= route_to('dashboard.knowledge.carousel') ?>" class="btn btn-outline-warning text-dark radius-8 px-14 py-8 text-xs fw-semibold bg-white d-inline-flex align-items-center gap-1 shadow-sm ms-auto">
                <iconify-icon icon="solar:star-bold" class="text-base text-warning"></iconify-icon>
                Atur Carousel Unggulan
            </a>
        </div>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="row row-cols-xxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-24 mb-24">
    <!-- 1. Artikel PBJ -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Artikel PBJ</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($articleStats['total'] ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:document-text-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="badge bg-success-50 text-success-600 radius-4 px-8 py-3"><?= (int) ($articleStats['published'] ?? 0) ?> Rilis</span>
                <span class="badge bg-warning-50 text-warning-600 radius-4 px-8 py-3"><?= (int) ($articleStats['in_review'] ?? 0) ?> Review</span>
                <span class="badge bg-neutral-100 text-neutral-600 radius-4 px-8 py-3"><?= (int) ($articleStats['draft'] ?? 0) ?> Draft</span>
                <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="ms-auto text-primary-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 2. Tanya Jawab (FAQ) -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Tanya Jawab (FAQ)</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($faqStats['total'] ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:question-circle-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="badge bg-success-50 text-success-600 radius-4 px-8 py-3"><?= (int) ($faqStats['published'] ?? 0) ?> Publik</span>
                <span class="text-secondary-light ms-1">Bantuan Regulasi PBJ</span>
                <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="ms-auto text-info-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 3. Studi Kasus -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Studi Kasus PBJ</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($caseStats['total'] ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-danger-50 text-danger-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:shield-warning-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="badge bg-success-50 text-success-600 radius-4 px-8 py-3"><?= (int) ($caseStats['published'] ?? 0) ?> Rilis</span>
                <span class="text-secondary-light ms-1">Mitigasi Risiko Pengadaan</span>
                <a href="<?= route_to('dashboard.knowledge.case-studies') ?>" class="ms-auto text-danger-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 4. Template Dokumen -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Template Dokumen</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($templateStats['total'] ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:file-download-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="badge bg-success-50 text-success-600 radius-4 px-8 py-3"><?= (int) ($templateStats['published'] ?? 0) ?> Siap Unduh</span>
                <span class="text-secondary-light ms-1">Word, Excel, PDF</span>
                <a href="<?= route_to('dashboard.knowledge.document-templates') ?>" class="ms-auto text-success-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 5. Media & Infografik -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Media &amp; Infografik</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($infographicsCount ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:gallery-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="text-secondary-light">Visual, Flyer &amp; Panduan</span>
                <a href="<?= site_url('dashboard/bunga-rampai/media') ?>" class="ms-auto text-info-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 6. Video PBJ -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Video PBJ</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($videosCount ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-danger-50 text-danger-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:play-circle-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="text-secondary-light">Webinar &amp; Podcast PBJ</span>
                <a href="<?= site_url('dashboard/bunga-rampai/video') ?>" class="ms-auto text-danger-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 7. Galeri Kegiatan -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Galeri Kegiatan</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($galleryCount ?? 0) ?></h4>
                </div>
                <div class="w-48-px h-48-px bg-purple-50 text-purple-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:camera-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="text-secondary-light">Foto Dokumentasi Resmi</span>
                <a href="<?= site_url('dashboard/bunga-rampai/galeri') ?>" class="ms-auto text-purple-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 8. Kategori & Topik -->
    <div class="col">
        <div class="card h-100 border-0 radius-16 shadow-sm p-20 bg-white position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-16">
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block mb-1 text-uppercase">Taksonomi Konten</span>
                    <h4 class="fw-bold mb-0 text-primary-900"><?= (int) ($categoriesCount ?? 0) ?> <span class="text-xs text-secondary-light fw-normal">Kategori</span></h4>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 radius-12 d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:tag-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 pt-12 border-top text-xs">
                <span class="badge bg-warning-50 text-warning-700 radius-4 px-8 py-3"><?= (int) ($tagsCount ?? 0) ?> Tagar</span>
                <span class="badge bg-primary-50 text-primary-700 radius-4 px-8 py-3"><?= (int) ($featuredCount ?? 0) ?> Unggulan</span>
                <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="ms-auto text-warning-600 text-decoration-none fw-semibold">Kelola &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Two-Column Showcase: Recent Articles/Cases & Recent Media/Videos -->
<div class="row gy-24 mb-24">
    <!-- Left: Konten Artikel & Kasus & Template Terbaru -->
    <div class="col-xl-7">
        <div class="card border-0 radius-16 shadow-sm h-100 bg-white">
            <div class="card-header border-bottom p-20 d-flex align-items-center justify-content-between bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-32-px h-32-px bg-primary-50 text-primary-600 radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:documents-minimalistic-bold" class="text-lg"></iconify-icon>
                    </div>
                    <h6 class="text-base fw-bold mb-0 text-primary-900">Konten Pengetahuan Terbaru</h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= route_to('dashboard.knowledge.articles') ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-12 py-6 text-xs">Semua Artikel</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table text-sm mb-0 align-middle">
                        <thead class="table-light text-secondary-light text-xs text-uppercase">
                            <tr>
                                <th class="px-20 py-12">Konten</th>
                                <th class="py-12">Tipe</th>
                                <th class="py-12">Status</th>
                                <th class="py-12">Penulis / Waktu</th>
                                <th class="pe-20 py-12 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentArticles)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-32 text-secondary-light">Belum ada konten pengetahuan tersimpan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentArticles as $art):
                                    $coverImg = ! empty($art['cover_path'])
                                        ? base_url('file/' . (int) $art['cover_file_id'])
                                        : base_url('assets/main/images/blog/blog-2-1.jpg');
                                    $typeBadge = match ($art['type']) {
                                        'case_study' => ['bg' => 'bg-danger-50 text-danger-600 border-danger-100', 'label' => 'Studi Kasus'],
                                        'template'   => ['bg' => 'bg-success-50 text-success-600 border-success-100', 'label' => 'Template'],
                                        'faq'        => ['bg' => 'bg-info-50 text-info-600 border-info-100', 'label' => 'FAQ'],
                                        default      => ['bg' => 'bg-primary-50 text-primary-600 border-primary-100', 'label' => 'Artikel'],
                                    };
                                    $statusBadge = match ($art['workflow_status']) {
                                        'published' => 'bg-success-50 text-success-600 border-success-200',
                                        'draft'     => 'bg-neutral-100 text-neutral-600 border-neutral-300',
                                        default     => 'bg-warning-50 text-warning-600 border-warning-200',
                                    };
                                    $editUrl = match ($art['type']) {
                                        'case_study' => route_to('dashboard.knowledge.case-studies.edit', (string) $art['id']),
                                        'template'   => route_to('dashboard.knowledge.document-templates.edit', (string) $art['id']),
                                        'faq'        => route_to('dashboard.knowledge.faqs.edit', (string) $art['id']),
                                        default      => route_to('dashboard.knowledge.articles.edit', (string) $art['id']),
                                    };
                                ?>
                                    <tr class="border-bottom">
                                        <td class="px-20 py-12">
                                            <div class="d-flex align-items-center gap-12">
                                                <img src="<?= esc($coverImg) ?>" alt="Cover" class="w-48-px h-36-px radius-6 object-fit-cover flex-shrink-0 border" onerror="this.src='<?= base_url('assets/main/images/blog/blog-2-1.jpg') ?>';">
                                                <div style="max-width: 260px;">
                                                    <a href="<?= esc($editUrl) ?>" class="fw-semibold text-primary-900 text-decoration-none text-truncate d-block hover-primary" style="font-size: 13.5px;">
                                                        <?= esc($art['title']) ?>
                                                    </a>
                                                    <?php if ((int) ($art['featured'] ?? 0) === 1): ?>
                                                        <span class="badge bg-warning-50 text-warning-700 px-6 py-1 radius-4 text-2xs mt-1">
                                                            <i class="fas fa-star text-warning me-1"></i> Unggulan
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-12">
                                            <span class="badge <?= esc($typeBadge['bg']) ?> border radius-4 px-8 py-3 text-2xs font-semibold">
                                                <?= esc($typeBadge['label']) ?>
                                            </span>
                                        </td>
                                        <td class="py-12">
                                            <span class="badge <?= esc($statusBadge) ?> border radius-4 px-8 py-3 text-2xs font-semibold text-capitalize">
                                                <?= esc($art['workflow_status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-12 text-secondary-light text-xs">
                                            <div class="fw-medium text-dark"><?= esc($art['author_name'] ?? 'Biro PBJ') ?></div>
                                            <div><?= date('d M Y', strtotime($art['updated_at'])) ?></div>
                                        </td>
                                        <td class="pe-20 py-12 text-end">
                                            <a href="<?= esc($editUrl) ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-10 py-5 text-xs">
                                                <iconify-icon icon="solar:pen-bold"></iconify-icon> Ubah
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
    </div>

    <!-- Right: Visual Media & Galeri Feed -->
    <div class="col-xl-5">
        <div class="card border-0 radius-16 shadow-sm h-100 bg-white">
            <div class="card-header border-bottom p-20 d-flex align-items-center justify-content-between bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-32-px h-32-px bg-info-50 text-info-600 radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:gallery-wide-bold" class="text-lg"></iconify-icon>
                    </div>
                    <h6 class="text-base fw-bold mb-0 text-primary-900">Media, Video &amp; Galeri Terbaru</h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('dashboard/bunga-rampai/media-galeri') ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-12 py-6 text-xs">Semua Media</a>
                </div>
            </div>
            <div class="card-body p-20">
                <?php if (empty($recentMedia)): ?>
                    <div class="text-center py-32 text-secondary-light">
                        <iconify-icon icon="solar:gallery-broken" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                        <p class="mb-0">Belum ada konten media, video, atau galeri.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($recentMedia as $med):
                            $previewImg = ! empty($med['file_id'])
                                ? base_url('file/' . (int) $med['file_id'])
                                : (! empty($med['thumbnail_file_id'])
                                    ? base_url('file/' . (int) $med['thumbnail_file_id'])
                                    : base_url('assets/main/images/gallery/gallery-page-1-1.jpg'));
                            $isVideo = in_array($med['type'], ['youtube', 'video_file', 'video'], true);
                            $editUrl = route_to('dashboard.knowledge.media-gallery.edit', (string) $med['id']);
                        ?>
                            <div class="col-6">
                                <div class="card border border-neutral-200 radius-12 overflow-hidden h-100 shadow-none position-relative group-hover">
                                    <div class="position-relative" style="height: 120px; background: #0b1f3a;">
                                        <img src="<?= esc($previewImg) ?>" alt="<?= esc($med['title'] ?? '') ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/main/images/gallery/gallery-page-1-1.jpg') ?>';">
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge radius-4 px-6 py-2 text-2xs <?= $isVideo ? 'bg-danger text-white' : ($med['type'] === 'gallery' ? 'bg-dark text-white' : 'bg-info text-dark') ?>">
                                                <?= $isVideo ? 'Video' : ($med['type'] === 'gallery' ? 'Galeri' : 'Infografis') ?>
                                            </span>
                                        </div>
                                        <?php if ($isVideo): ?>
                                            <div class="position-absolute top-50 start-50 translate-middle">
                                                <div class="w-32-px h-32-px radius-50 bg-danger text-white d-flex align-items-center justify-content-center shadow-sm">
                                                    <iconify-icon icon="solar:play-bold" class="text-sm ms-1"></iconify-icon>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-12 d-flex flex-column justify-content-between flex-grow-1">
                                        <div>
                                            <h6 class="text-xs fw-bold mb-1 text-truncate" title="<?= esc($med['title'] ?? '') ?>">
                                                <?= esc($med['title'] ?? '(Tanpa Judul)') ?>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-secondary-light" style="font-size: 11px;">
                                                <span><i class="far fa-heart text-danger me-1"></i><?= (int) ($med['likes_count'] ?? 0) ?></span>
                                                <span><i class="far fa-eye text-primary me-1"></i><?= (int) ($med['views_count'] ?? 0) ?></span>
                                            </div>
                                        </div>
                                        <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between">
                                            <span class="badge radius-4 px-6 py-2 text-2xs <?= ($med['workflow_status'] ?? '') === 'published' ? 'bg-success-50 text-success-600' : 'bg-neutral-100 text-neutral-600' ?>">
                                                <?= esc($med['workflow_status'] ?? 'draft') ?>
                                            </span>
                                            <a href="<?= esc($editUrl) ?>" class="text-primary-600 text-xs fw-semibold text-decoration-none">
                                                Ubah
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Category Distribution -->
<div class="card border-0 radius-16 shadow-sm bg-white mb-24">
    <div class="card-header border-bottom p-20 d-flex align-items-center justify-content-between bg-white">
        <div class="d-flex align-items-center gap-2">
            <div class="w-32-px h-32-px bg-warning-50 text-warning-600 radius-8 d-flex align-items-center justify-content-center">
                <iconify-icon icon="solar:layers-bold" class="text-lg"></iconify-icon>
            </div>
            <div>
                <h6 class="text-base fw-bold mb-0 text-primary-900">Pilar Kategori Pengetahuan PBJ</h6>
                <span class="text-xs text-secondary-light">Kategori taksonomi aktif yang mengelompokkan artikel, FAQ, dan media</span>
            </div>
        </div>
        <a href="<?= route_to('dashboard.knowledge.categories') ?>" class="btn btn-sm btn-outline-warning text-dark radius-6 px-12 py-6 text-xs">
            + Kelola Kategori
        </a>
    </div>
    <div class="card-body p-20">
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="p-16 border border-neutral-200 radius-12 bg-neutral-50 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary-100 text-primary-700 radius-4 px-8 py-2 text-2xs fw-semibold">Pilar PBJ</span>
                                <span class="text-xs text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                            </div>
                            <h6 class="text-sm fw-bold mb-1 text-primary-900"><?= esc($cat['name']) ?></h6>
                            <p class="text-secondary-light text-xs mb-0" style="line-height: 1.4;"><?= esc($cat['description'] ?? 'Pilar panduan pengadaan barang/jasa.') ?></p>
                        </div>
                        <div class="pt-12 mt-12 border-top border-neutral-200 d-flex align-items-center justify-content-between">
                            <span class="text-2xs text-secondary-light font-monospace"><?= esc($cat['slug']) ?></span>
                            <a href="<?= route_to('dashboard.knowledge.categories.edit', (string) $cat['id']) ?>" class="text-primary-600 text-xs fw-semibold text-decoration-none">
                                Edit &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
