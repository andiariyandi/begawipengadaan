<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb & Top Bar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelola Slider Hero Beranda') ?></h6>
        <p class="text-secondary-light mb-0">Manajemen tayangan slide hero banner, judul, tombol aksi, dan urutan rotasi pada homepage.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#heroLivePreviewModal">
            <iconify-icon icon="solar:eye-outline" class="text-xl"></iconify-icon>
            <span>Simulasi Pratinjau Hero</span>
        </button>
        <a href="<?= site_url('dashboard/landing/slider/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Slide</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'promo', 'activeSub' => 'slider']) ?>

<!-- KPI Metric Cards (WowDash Gradient Cards) -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <!-- Card 1: Total Slides -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Slide Hero</p>
                        <h6 class="mb-0 text-primary-light"><?= number_format($stats['total'] ?? count($sliders)) ?> Slide</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center shadow-xs">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-primary-600">
                        <iconify-icon icon="solar:gallery-wide-bold" class="text-xs"></iconify-icon> Beranda Utama
                    </span>
                    Rotasi hero landing page
                </p>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Slides -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Slide Tayang Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['active'] ?? 0) ?> Tayang</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center shadow-xs">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-success-100 text-success-600 radius-4 text-2xs px-8 py-2">
                        <?= ($stats['total'] ?? 0) > 0 ? round((($stats['active'] ?? 0) / $stats['total']) * 100) : 0 ?>% dari total
                    </span>
                    Ditampilkan kepada publik
                </p>
            </div>
        </div>
    </div>

    <!-- Card 3: Inactive / Draft Slides -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Draf / Nonaktif</p>
                        <h6 class="mb-0 text-secondary-light"><?= number_format($stats['inactive'] ?? 0) ?> Draf</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-neutral-400 rounded-circle d-flex justify-content-center align-items-center shadow-xs">
                        <iconify-icon icon="solar:eye-closed-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-secondary-light">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-xs"></iconify-icon> Siap aktivasi
                    </span>
                    Disimpan tanpa tayang
                </p>
            </div>
        </div>
    </div>

    <!-- Card 4: Rotasi Otomatis Carousel -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Status Rotasi Slider</p>
                        <h6 class="mb-0 text-primary-600">Auto-Rotate (6 Detik)</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center shadow-xs">
                        <iconify-icon icon="solar:refresh-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="text-2xs text-secondary-light">
                        Pembaruan terakhir: <?= ! empty($stats['latest_updated']) ? date('d M Y, H:i', strtotime($stats['latest_updated'])) : '-' ?> WIB
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card basic-data-table border radius-12 shadow-none">
    <div class="card-header border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3 bg-base">
        <div>
            <h6 class="text-base fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:slider-vertical-bold" class="text-primary-600 text-xl"></iconify-icon>
                Daftar Slide Hero Beranda
            </h6>
            <span class="text-xs text-secondary-light">Gunakan tombol panah untuk menukar urutan tayang slide secara instan</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-neutral-100 text-secondary-light px-10 py-6 radius-6 text-xs">
                Total: <?= count($sliders) ?> slide
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="slider-hero" data-page-length="10">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Urutan</th>
                        <th style="width: 140px;">Gambar Banner</th>
                        <th>Judul &amp; Subjudul</th>
                        <th>Tombol Aksi (CTA)</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 150px;" class="text-center no-sort no-export action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sliders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-40">
                                <div class="py-20 text-center">
                                    <div class="w-64-px h-64-px bg-neutral-100 rounded-circle d-inline-flex align-items-center justify-content-center text-secondary-light mb-12">
                                        <iconify-icon icon="solar:slider-vertical-outline" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="text-sm fw-semibold text-primary-light mb-4">Belum Ada Slide Hero</h6>
                                    <p class="text-xs text-secondary-light mb-16">Buat slide hero pertama untuk mempercantik halaman beranda BegawiPengadaan.</p>
                                    <a href="<?= site_url('dashboard/landing/slider/tambah') ?>" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8">
                                        <iconify-icon icon="solar:add-circle-outline" class="me-1"></iconify-icon> Tambah Slide Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sliders as $index => $s): ?>
                            <?php
                            $imgSrc = ! empty($s['image_url']) ? (str_starts_with($s['image_url'], 'http') ? $s['image_url'] : base_url($s['image_url'])) : base_url('assets/main/images/backgrounds/slider-1-1.png');
                            $hasSecondBtn = ! empty($s['secondary_button_text']);
                            ?>
                            <tr id="slider-row-<?= $s['id'] ?>">
                                <!-- Urutan & Quick Reorder -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                        <span class="badge bg-neutral-100 text-primary-light fw-bold font-monospace px-8 py-4 radius-4 text-xs">
                                            #<?= esc($s['sort_order']) ?>
                                        </span>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <?php if ($index > 0): ?>
                                                <form method="post" action="<?= site_url('dashboard/landing/slider/update-order') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="btn btn-xs btn-outline-neutral-300 p-2 radius-4 text-2xs" title="Geser Naik (Urutan Lebih Awal)">
                                                        <iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($index < count($sliders) - 1): ?>
                                                <form method="post" action="<?= site_url('dashboard/landing/slider/update-order') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="btn btn-xs btn-outline-neutral-300 p-2 radius-4 text-2xs" title="Geser Turun (Urutan Setelahnya)">
                                                        <iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Thumbnail Pratinjau dengan Modal Zoom -->
                                <td>
                                    <div class="position-relative overflow-hidden radius-8 border bg-neutral-900 group-hover-zoom" style="width: 130px; height: 68px;">
                                        <img src="<?= esc($imgSrc) ?>" 
                                             alt="Slide #<?= esc($s['id']) ?>" 
                                             class="w-100 h-100 object-fit-cover cursor-pointer hover-scale-105 transition-all" 
                                             onclick="openImageLightbox('<?= esc($imgSrc) ?>', '<?= esc(strip_tags($s['title'])) ?>')"
                                             onerror="this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'">
                                        <span class="position-absolute bottom-0 end-0 bg-dark-subtle text-white text-3xs px-4 py-2 radius-top-left-4 opacity-75">
                                            <iconify-icon icon="solar:magnifer-zoom-in-linear"></iconify-icon>
                                        </span>
                                    </div>
                                </td>

                                <!-- Judul & Deskripsi -->
                                <td>
                                    <?php if (! empty($s['badge_text'])): ?>
                                        <span class="badge bg-primary-100 text-primary-600 radius-4 text-3xs px-8 py-3 mb-1 fw-semibold text-uppercase">
                                            <iconify-icon icon="solar:bookmark-bold" class="me-1"></iconify-icon><?= esc($s['badge_text']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <h6 class="text-sm fw-bold mb-1 text-primary-light">
                                        <?= strip_tags($s['title']) ?>
                                    </h6>
                                    <?php if (str_contains($s['title'], '<span>')): ?>
                                        <small class="badge bg-warning-100 text-warning-main text-3xs px-6 py-2 mb-1 radius-4 d-inline-block">
                                            <iconify-icon icon="solar:stars-bold" class="me-1"></iconify-icon>Aksen Emas Aktif
                                        </small>
                                    <?php endif; ?>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 420px;"><?= esc($s['subtitle']) ?></p>
                                </td>

                                <!-- Call to Action Buttons -->
                                <td>
                                    <?php if (! empty($s['button_text'])): ?>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="badge bg-primary-50 text-primary-600 border border-primary-200 radius-4 text-3xs px-6 py-2">CTA 1</span>
                                            <strong class="text-xs text-primary-light"><?= esc($s['button_text']) ?></strong>
                                            <code class="text-3xs text-secondary-light ms-1">&rarr; <?= esc($s['button_url']) ?></code>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($hasSecondBtn): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-neutral-100 text-secondary-light border radius-4 text-3xs px-6 py-2">CTA 2</span>
                                            <span class="text-xs text-secondary-light"><?= esc($s['secondary_button_text']) ?></span>
                                            <code class="text-3xs text-secondary-light ms-1">&rarr; <?= esc($s['secondary_button_url']) ?></code>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($s['button_text']) && ! $hasSecondBtn): ?>
                                        <span class="text-secondary-light text-xs fst-italic">Tanpa tombol aksi</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Aktif Toggle -->
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/landing/slider/toggle/' . $s['id']) ?>" 
                                       class="badge <?= $s['is_active'] ? 'bg-success-100 text-success-600 border border-success-200' : 'bg-neutral-100 text-secondary-light border' ?> px-10 py-6 radius-6 text-xs d-inline-flex align-items-center gap-1 text-decoration-none hover-shadow-xs" 
                                       title="Klik untuk mengubah status tayang">
                                        <iconify-icon icon="<?= $s['is_active'] ? 'solar:check-circle-bold' : 'solar:close-circle-bold' ?>" class="text-sm"></iconify-icon>
                                        <span><?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                    </a>
                                </td>

                                <!-- Aksi CRUD (Edit, Duplicate, Delete) -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Edit -->
                                        <a href="<?= site_url('dashboard/landing/slider/ubah/' . $s['id']) ?>" 
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-all shadow-none" 
                                           title="Ubah Slide">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>

                                        <!-- Duplicate -->
                                        <a href="<?= site_url('dashboard/landing/slider/duplikat/' . $s['id']) ?>" 
                                           onclick="return confirm('Duplikasi slide ini sebagai draf baru?')"
                                           class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-info-main hover-text-white transition-all shadow-none" 
                                           title="Duplikat Slide">
                                            <iconify-icon icon="solar:copy-linear"></iconify-icon>
                                        </a>

                                        <!-- Delete Modal Trigger -->
                                        <button type="button" 
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white transition-all border-0 shadow-none" 
                                                onclick="confirmDeleteSlider(<?= $s['id'] ?>, '<?= esc(strip_tags($s['title']), 'js') ?>')"
                                                title="Hapus Slide">
                                            <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 1: Simulasi Pratinjau Hero Responsif (Desktop & Mobile) -->
<!-- ======================================================== -->
<div class="modal fade" id="heroLivePreviewModal" tabindex="-1" aria-labelledby="heroLivePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg bg-base">
            <div class="modal-header border-bottom py-16 px-24 bg-base">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:laptop-minimalistic-outline" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fs-6 fw-bold mb-0 text-primary-light" id="heroLivePreviewModalLabel">Simulasi Responsif Slider Hero Publik</h6>
                        <span class="text-xs text-secondary-light">Tampilan nyata slide beranda publik dengan aksen emas dan rasio layar responsif</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <!-- Toolbar Simulator: Device Tabs & Slide Selector -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20 p-12 radius-10 border bg-neutral-50">
                    <!-- Device Switcher -->
                    <ul class="nav nav-pills gap-2 mb-0" id="previewDeviceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-1" id="desktop-tab" data-bs-toggle="pill" data-bs-target="#desktop-view" type="button" role="tab">
                                <iconify-icon icon="solar:laptop-outline" class="text-base"></iconify-icon>
                                <span>Desktop Monitor (16:9)</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link radius-8 px-16 py-8 text-xs fw-semibold d-flex align-items-center gap-1" id="mobile-tab" data-bs-toggle="pill" data-bs-target="#mobile-view" type="button" role="tab">
                                <iconify-icon icon="solar:smartphone-outline" class="text-base"></iconify-icon>
                                <span>Ponsel Mobile (9:16)</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Slide Selector Dropdown -->
                    <?php if (! empty($sliders)): ?>
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-xs text-secondary-light mb-0 fw-semibold text-nowrap">Pilih Slide Simulasi:</label>
                            <select id="simSlideSelector" class="form-select form-select-sm radius-8" style="max-width: 260px;" onchange="switchSimulatedSlide(this.value)">
                                <?php foreach ($sliders as $idx => $sl): ?>
                                    <option value="<?= $sl['id'] ?>" <?= $idx === 0 ? 'selected' : '' ?>>
                                        #<?= $sl['sort_order'] ?> - <?= esc(mb_strimwidth(strip_tags($sl['title']), 0, 30, '...')) ?> (<?= $sl['is_active'] ? 'Aktif' : 'Draf' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Simulation Displays -->
                <?php if (! empty($sliders)): ?>
                    <?php foreach ($sliders as $idx => $s): ?>
                        <?php
                        $simImg = ! empty($s['image_url']) ? (str_starts_with($s['image_url'], 'http') ? $s['image_url'] : base_url($s['image_url'])) : base_url('assets/main/images/backgrounds/slider-1-1.png');
                        $rawTitle = $s['title'];
                        // Ensure <span> inside title gets golden styling in simulator
                        $styledTitle = str_ireplace('<span>', '<span style="color: #ffc107; font-weight: 700;">', $rawTitle);
                        ?>
                        <div class="sim-slide-container <?= $idx === 0 ? '' : 'd-none' ?>" id="sim-slide-<?= $s['id'] ?>">
                            <div class="tab-content">
                                <!-- Desktop Simulation -->
                                <div class="tab-pane fade show active" id="desktop-view-<?= $s['id'] ?>" role="tabpanel">
                                    <div class="position-relative radius-16 overflow-hidden shadow-sm bg-neutral-900 border" style="min-height: 400px;">
                                        <!-- Background Image with Gradient Overlay -->
                                        <img src="<?= esc($simImg) ?>" alt="Hero Desktop" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 opacity-40">
                                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(8, 23, 48, 0.92) 0%, rgba(8, 23, 48, 0.65) 60%, rgba(8, 23, 48, 0.3) 100%);"></div>

                                        <!-- Content Block -->
                                        <div class="position-relative p-40 p-lg-48 d-flex flex-column justify-content-center text-white" style="min-height: 400px; z-index: 2;">
                                            <?php if (! empty($s['badge_text'])): ?>
                                                <div class="d-inline-flex align-items-center gap-2 mb-16 align-self-start">
                                                    <span class="badge bg-primary text-white radius-4 px-12 py-6 text-xs fw-semibold text-uppercase tracking-wider">
                                                        <iconify-icon icon="solar:shield-check-bold" class="me-1"></iconify-icon>
                                                        <?= esc($s['badge_text']) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                            <h1 class="text-3xl text-lg-4xl fw-bold text-white mb-16 lh-sm" style="max-width: 720px;">
                                                <?= $styledTitle ?>
                                            </h1>

                                            <p class="text-neutral-200 text-sm text-lg-base mb-28" style="max-width: 620px; line-height: 1.6;">
                                                <?= esc($s['subtitle']) ?>
                                            </p>

                                            <!-- CTA Buttons -->
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <?php if (! empty($s['button_text'])): ?>
                                                    <a href="javascript:void(0)" class="btn btn-warning text-dark fw-bold radius-8 px-24 py-12 text-sm shadow-sm d-inline-flex align-items-center gap-2">
                                                        <span><?= esc($s['button_text']) ?></span>
                                                        <iconify-icon icon="solar:alt-arrow-right-bold"></iconify-icon>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (! empty($s['secondary_button_text'])): ?>
                                                    <a href="javascript:void(0)" class="btn btn-outline-light radius-8 px-24 py-12 text-sm fw-semibold d-inline-flex align-items-center gap-2">
                                                        <span><?= esc($s['secondary_button_text']) ?></span>
                                                        <iconify-icon icon="solar:arrow-right-up-linear"></iconify-icon>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile Phone Frame Simulation -->
                                <div class="tab-pane fade" id="mobile-view-<?= $s['id'] ?>" role="tabpanel">
                                    <div class="d-flex justify-content-center py-10">
                                        <div class="border border-4 border-neutral-800 radius-28 overflow-hidden shadow-lg bg-neutral-900 text-white position-relative" style="width: 340px; min-height: 560px;">
                                            <!-- Phone Speaker Notch -->
                                            <div class="w-100 bg-neutral-800 text-center py-6 text-3xs text-secondary-light border-bottom border-neutral-700 d-flex align-items-center justify-content-center gap-2">
                                                <div class="w-40-px h-4-px bg-neutral-600 rounded-pill"></div>
                                                <div class="w-6-px h-6-px bg-neutral-600 rounded-circle"></div>
                                            </div>

                                            <img src="<?= esc($simImg) ?>" alt="Hero Mobile" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 opacity-30">
                                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(8, 23, 48, 0.95) 0%, rgba(8, 23, 48, 0.7) 100%);"></div>

                                            <div class="position-relative p-24 d-flex flex-column justify-content-center text-center text-white" style="min-height: 520px; z-index: 2;">
                                                <?php if (! empty($s['badge_text'])): ?>
                                                    <span class="badge bg-primary text-white radius-4 px-10 py-4 text-3xs align-self-center mb-12 fw-bold">
                                                        <?= esc($s['badge_text']) ?>
                                                    </span>
                                                <?php endif; ?>

                                                <h3 class="text-xl fw-bold text-white mb-12 lh-sm">
                                                    <?= $styledTitle ?>
                                                </h3>

                                                <p class="text-neutral-300 text-xs mb-24" style="line-height: 1.5;">
                                                    <?= esc($s['subtitle']) ?>
                                                </p>

                                                <div class="d-flex flex-column gap-2 w-100">
                                                    <?php if (! empty($s['button_text'])): ?>
                                                        <a href="javascript:void(0)" class="btn btn-warning text-dark btn-sm fw-bold radius-8 py-10 text-xs">
                                                            <?= esc($s['button_text']) ?> &rarr;
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (! empty($s['secondary_button_text'])): ?>
                                                        <a href="javascript:void(0)" class="btn btn-outline-light btn-sm radius-8 py-10 text-xs">
                                                            <?= esc($s['secondary_button_text']) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-40 text-center text-secondary-light">Belum ada slide hero untuk dipratinjau.</div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-top py-12 px-24 bg-base justify-content-between">
                <span class="text-xs text-secondary-light">
                    <iconify-icon icon="solar:info-circle-outline" class="me-1"></iconify-icon>
                    Simulasi menggunakan aset CSS publik BegawiPengadaan asli.
                </span>
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs" data-bs-dismiss="modal">Tutup Simulasi</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: Lightbox Zoom Pratinjau Gambar Banner           -->
<!-- ======================================================== -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 border-0 bg-dark text-white shadow-lg overflow-hidden">
            <div class="modal-header border-bottom border-neutral-700 py-12 px-20">
                <h6 class="modal-title fs-6 fw-bold text-white mb-0" id="lightboxTitle">Pratinjau Banner Hero</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <img id="lightboxImage" src="" alt="Banner Hero Full" class="w-100 object-fit-contain" style="max-height: 500px;">
            </div>
            <div class="modal-footer border-top border-neutral-700 py-10 px-20 justify-content-between text-2xs text-neutral-400">
                <span>Rekomendasi rasio: 1920x500 px (Lanskap Tajam)</span>
                <button type="button" class="btn btn-sm btn-outline-light radius-6 px-12 py-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: Konfirmasi Hapus Slide Hero                      -->
<!-- ======================================================== -->
<div class="modal fade" id="deleteSliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-body p-24 text-center">
                <div class="w-56-px h-56-px bg-danger-100 text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
                    <iconify-icon icon="solar:trash-bin-trash-bold" class="text-3xl"></iconify-icon>
                </div>
                <h6 class="text-base fw-bold text-primary-light mb-8">Hapus Slide Hero?</h6>
                <p class="text-xs text-secondary-light mb-20">
                    Slide <strong id="deleteSliderTitle" class="text-primary-light"></strong> akan dihapus permanen dari basis data beranda.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs fw-semibold flex-grow-1" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="deleteSliderConfirmBtn" class="btn btn-danger-600 radius-8 px-16 py-8 text-xs fw-semibold flex-grow-1">Hapus Permanen</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Switch between slides in Simulator Modal
function switchSimulatedSlide(slideId) {
    document.querySelectorAll('.sim-slide-container').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('sim-slide-' + slideId);
    if (target) {
        target.classList.remove('d-none');
    }
}

// Lightbox modal opener
function openImageLightbox(imgSrc, title) {
    const img = document.getElementById('lightboxImage');
    const titleEl = document.getElementById('lightboxTitle');
    if (img) img.src = imgSrc;
    if (titleEl) titleEl.innerText = title || 'Pratinjau Banner Hero';
    const modalEl = document.getElementById('imageLightboxModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Confirm Delete Modal Opener
function confirmDeleteSlider(id, title) {
    const titleEl = document.getElementById('deleteSliderTitle');
    const btn = document.getElementById('deleteSliderConfirmBtn');
    if (titleEl) titleEl.innerText = '"' + title + '"';
    if (btn) btn.href = '<?= site_url('dashboard/landing/slider/hapus/') ?>' + id;
    const modalEl = document.getElementById('deleteSliderModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Handle Device Tab switching in simulator
document.addEventListener('DOMContentLoaded', function () {
    const desktopTab = document.getElementById('desktop-tab');
    const mobileTab = document.getElementById('mobile-tab');
    if (desktopTab && mobileTab) {
        desktopTab.addEventListener('click', function () {
            document.querySelectorAll('[id^="desktop-view-"]').forEach(el => el.classList.add('show', 'active'));
            document.querySelectorAll('[id^="mobile-view-"]').forEach(el => el.classList.remove('show', 'active'));
        });
        mobileTab.addEventListener('click', function () {
            document.querySelectorAll('[id^="mobile-view-"]').forEach(el => el.classList.add('show', 'active'));
            document.querySelectorAll('[id^="desktop-view-"]').forEach(el => el.classList.remove('show', 'active'));
        });
    }
});
</script>

<?= $this->endSection() ?>
