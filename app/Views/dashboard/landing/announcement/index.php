<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Breadcrumbs Header (WowDash Standard) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1 text-primary-light"><?= esc($pageTitle ?? 'Kelola Popup Pengumuman &amp; Promo') ?></h6>
        <p class="text-secondary-light mb-0 text-sm">Dialog modal interaktif berbasis carousel yang menyapa pengunjung saat pertama kali membuka beranda.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/pengumuman/tambah') ?>" class="btn btn-primary-600 radius-8 px-18 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
            <span>Tambah Slide Pengumuman</span>
        </a>
        <button type="button" class="btn btn-outline-info-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#popupSimulatorModal">
            <iconify-icon icon="solar:play-stream-bold" class="text-lg"></iconify-icon>
            <span>Simulasi Modal Popup</span>
        </button>
        <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-neutral-200 text-secondary-light radius-8 px-14 py-10 d-flex align-items-center gap-2 hover-text-primary">
            <iconify-icon icon="solar:link-external-linear" class="text-lg"></iconify-icon>
            <span class="d-none d-sm-inline">Pratinjau di Beranda</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'promo', 'activeSub' => 'pengumuman']) ?>

<!-- KPI Metrics (Native WowDash Gradient Cards) -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Slide Popup</p>
                        <h6 class="mb-0 text-primary-light" id="kpiTotalSlide"><?= number_format($stats['total'] ?? 0) ?> Slide</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="solar:check-circle-bold" class="text-xs"></iconify-icon> Carousel
                    </span>
                    Slide promosi &amp; warta resmi
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Status Modal Beranda</p>
                        <h6 class="mb-0 text-primary-light">
                            <span class="badge radius-6 px-10 py-4 text-xs <?= $popupEnabled === '1' ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-secondary-light' ?>" id="kpiPopupStatusBadge">
                                <?= $popupEnabled === '1' ? 'Aktif Menayangkan' : 'Dinonaktifkan' ?>
                            </span>
                        </h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:bell-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-info-main">
                        <iconify-icon icon="solar:shield-check-bold" class="text-xs"></iconify-icon> Global
                    </span>
                    Kontrol penayangan di portal publik
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Slide Aktif Tayang</p>
                        <h6 class="mb-0 text-primary-light" id="kpiActiveCount"><?= number_format($stats['active_count'] ?? 0) ?> Slide</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-success-main">
                        <iconify-icon icon="solar:play-circle-bold" class="text-xs"></iconify-icon> Tayang
                    </span>
                    Muncul saat pengunjung membuka web
                </p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Draf / Nonaktif</p>
                        <h6 class="mb-0 text-primary-light" id="kpiInactiveCount"><?= number_format($stats['inactive_count'] ?? 0) ?> Slide</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:archive-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center gap-1 text-warning-main">
                        <iconify-icon icon="solar:pause-circle-bold" class="text-xs"></iconify-icon> Tersimpan
                    </span>
                    Draf sementara siap diaktifkan
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Global Popup Configuration Card (Native WowDash) -->
<div class="card mb-24">
    <div class="card-header border-bottom bg-base py-14 px-20 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-xl"></iconify-icon>
            <h6 class="text-sm fw-semibold text-primary-light mb-0">Konfigurasi Saklar &amp; Header Popup Beranda</h6>
        </div>
        <span class="text-secondary-light text-xs">Pengaturan otomatis tersinkronisasi ke landing page</span>
    </div>
    <div class="card-body p-20">
        <form method="post" action="<?= site_url('dashboard/landing/pengumuman/pengaturan') ?>" id="popupSettingsForm">
            <?= csrf_field() ?>
            <div class="row align-items-center gy-3">
                <div class="col-lg-3 col-md-4">
                    <div class="form-switch switch-primary d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="popup_enabled" value="1" id="popupEnabledSwitch" <?= $popupEnabled === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer" for="popupEnabledSwitch">
                            Tayangkan Modal Popup
                        </label>
                    </div>
                    <small class="text-secondary-light text-xs d-block mt-4">Jika dinonaktifkan, modal tidak akan muncul di beranda.</small>
                </div>
                <div class="col-lg-7 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-100 text-xs fw-semibold text-secondary-light">Teks Badge Header</span>
                        <input type="text" name="popup_badge" id="popupBadgeInput" class="form-control" value="<?= esc($popupBadge) ?>" placeholder="PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG">
                    </div>
                    <!-- Quick Preset Badge Chips -->
                    <div class="d-flex flex-wrap align-items-center gap-1 mt-6">
                        <span class="text-2xs text-secondary-light me-1">Pilihan Cepat:</span>
                        <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 preset-badge-chip" data-text="PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG">Resmi PBJ</button>
                        <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 preset-badge-chip" data-text="AGENDA &amp; BIMTEK 2026">Agenda Bimtek</button>
                        <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 preset-badge-chip" data-text="INFORMASI PENTING TENDER STRATEGIS">Tender Strategis</button>
                        <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 text-2xs py-2 px-6 preset-badge-chip" data-text="UPDATE REGULASI &amp; KEBIJAKAN LKPP">Regulasi LKPP</button>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 text-md-end">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-18 py-10 text-sm fw-semibold w-100" id="btnSaveSettings">
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Main Table Card with Filter Toolbar & Search -->
<div class="card basic-data-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-md fw-semibold text-primary-light mb-1 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:slider-vertical-bold" class="text-primary-600 text-xl"></iconify-icon>
                <span>Daftar Slide Pengumuman Popup</span>
            </h6>
            <p class="text-secondary-light text-xs mb-0">Atur urutan penayangan carousel, banner visual, teks penjelasan, dan tautan aksi.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="position-relative">
                <input type="text" class="form-control form-control-sm radius-8 pe-36" id="announcementSearch" placeholder="Cari judul atau uraian..." style="width: 240px;">
                <button type="button" class="btn p-0 position-absolute end-0 top-50 translate-middle-y me-10 text-secondary-light d-none border-0 bg-transparent" id="searchClearBtn">
                    <iconify-icon icon="solar:close-circle-bold" class="text-sm"></iconify-icon>
                </button>
            </div>
            <span class="text-xs text-secondary-light fw-medium" id="searchCountBadge">Menampilkan <strong class="text-primary-light" id="visibleCount"><?= count($announcements) ?></strong> slide</span>
        </div>
    </div>

    <!-- Filter Pills Bar -->
    <div class="card-body p-20 pt-16 border-bottom bg-neutral-50 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2 filter-pills-bar">
            <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-14 py-6 text-xs fw-semibold filter-pill active" data-filter="all">
                Semua Slide (<?= count($announcements) ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold filter-pill" data-filter="active">
                Hanya Aktif (<?= (int) ($stats['active_count'] ?? 0) ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-white text-secondary-light radius-8 px-14 py-6 text-xs fw-semibold filter-pill" data-filter="inactive">
                Hanya Draf / Nonaktif (<?= (int) ($stats['inactive_count'] ?? 0) ?>)
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="announcementTable">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">Urutan</th>
                        <th style="width: 140px;">Media Banner</th>
                        <th>Kategori &amp; Judul Slide</th>
                        <th>Deskripsi &amp; Tautan Aksi</th>
                        <th style="width: 120px;" class="text-center">Masa Berlaku</th>
                        <th style="width: 100px;" class="text-center">Status</th>
                        <th style="width: 130px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr class="empty-state-row">
                            <td colspan="7" class="text-center py-40">
                                <div class="text-secondary-light mb-8"><iconify-icon icon="solar:bell-bing-bold" class="text-4xl"></iconify-icon></div>
                                <h6 class="fw-semibold text-primary-light mb-1">Belum Ada Slide Pengumuman</h6>
                                <p class="text-secondary-light text-xs mb-16">Tambahkan slide baru untuk menyapa pengunjung beranda dengan pengumuman penting.</p>
                                <a href="<?= site_url('dashboard/landing/pengumuman/tambah') ?>" class="btn btn-primary-600 btn-sm radius-8 px-16 py-8">
                                    <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> Tambah Slide Sekarang
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $a): ?>
                            <?php
                            $isActive = (int) ($a['is_active'] ?? 0) === 1;
                            $fallbackImg = base_url('assets/main/images/event/event-details-top-img-1.jpg');
                            $imgSrc = !empty($a['image_url']) ? (str_starts_with($a['image_url'], 'http') ? $a['image_url'] : base_url($a['image_url'])) : $fallbackImg;
                            ?>
                            <tr class="announcement-row"
                                data-id="<?= (int) $a['id'] ?>"
                                data-status="<?= $isActive ? 'active' : 'inactive' ?>">
                                
                                <!-- Sort Order with Inline Stepper -->
                                <td class="text-center align-middle">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <input type="number" min="1" max="99"
                                               class="form-control form-control-sm text-center fw-bold order-input radius-8"
                                               style="width: 60px;"
                                               data-id="<?= (int) $a['id'] ?>"
                                               value="<?= (int) ($a['sort_order'] ?? 1) ?>"
                                               title="Ketik nomor urut lalu klik luar untuk simpan">
                                    </div>
                                </td>

                                <!-- Banner Image with Lightbox Zoom Trigger -->
                                <td class="align-middle">
                                    <div class="w-120-px h-64-px radius-8 overflow-hidden bg-neutral-100 flex-shrink-0 d-flex align-items-center justify-content-center border border-neutral-200 position-relative cursor-pointer"
                                         onclick="openAnnouncementImagePreview('<?= esc($imgSrc) ?>', '<?= esc($a['title']) ?>', '<?= esc($a['badge_label'] ?? 'PENGUMUMAN') ?>')"
                                         title="Klik untuk perbesar banner">
                                        <img src="<?= esc($imgSrc) ?>" alt="<?= esc($a['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= $fallbackImg ?>';">
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-40 text-white opacity-0 hover-opacity-100 transition-2">
                                            <iconify-icon icon="solar:magnifer-zoom-in-bold" class="text-sm"></iconify-icon>
                                        </div>
                                    </div>
                                </td>

                                <!-- Badge Category & Title -->
                                <td class="align-middle">
                                    <div class="d-flex flex-column gap-1">
                                        <div>
                                            <span class="badge bg-warning-100 text-warning-main radius-4 text-2xs fw-semibold px-8 py-2 text-uppercase">
                                                <?= esc($a['badge_label'] ?: 'PENGUMUMAN') ?>
                                            </span>
                                        </div>
                                        <h6 class="text-sm fw-semibold mb-0 text-primary-light">
                                            <?= esc($a['title']) ?>
                                        </h6>
                                    </div>
                                </td>

                                <!-- Description & CTA Link -->
                                <td class="align-middle">
                                    <p class="text-xs text-secondary-light mb-1 text-truncate" style="max-width: 380px;" title="<?= esc($a['description'] ?? '') ?>">
                                        <?= esc(mb_strimwidth(strip_tags((string) ($a['description'] ?? '')), 0, 95, '...')) ?>
                                    </p>
                                    <?php if (! empty($a['cta_label'])): ?>
                                        <div class="text-xs text-primary-600 fw-semibold d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:link-circle-bold"></iconify-icon>
                                            <span><?= esc($a['cta_label']) ?></span>
                                            <span class="text-secondary-light">&rarr;</span>
                                            <code class="text-2xs bg-neutral-100 text-primary-light px-6 py-2 radius-4"><?= esc($a['cta_url'] ?? '/') ?></code>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Scheduling Badge (Start & End Date) -->
                                <td class="text-center align-middle">
                                    <?php if (! empty($a['start_date']) || ! empty($a['end_date'])): ?>
                                        <span class="badge bg-neutral-100 text-secondary-light text-2xs radius-4 px-8 py-4 d-inline-flex align-items-center gap-1" title="Periode tayang aktif">
                                            <iconify-icon icon="solar:calendar-bold" class="text-primary-600"></iconify-icon>
                                            <?= !empty($a['start_date']) ? date('d M Y', strtotime($a['start_date'])) : 'Awal' ?> -
                                            <?= !empty($a['end_date']) ? date('d M Y', strtotime($a['end_date'])) : 'Seterusnya' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-50 text-secondary-light text-2xs radius-4 px-8 py-4">
                                            Tanpa Batas
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Active Status Switch -->
                                <td class="text-center align-middle">
                                    <div class="form-switch switch-primary d-inline-flex justify-content-center align-items-center">
                                        <input class="form-check-input announcement-toggle-switch cursor-pointer" type="checkbox"
                                               data-id="<?= (int) $a['id'] ?>"
                                               <?= $isActive ? 'checked' : '' ?>
                                               title="Aktifkan / Nonaktifkan slide">
                                    </div>
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-end text-nowrap align-middle pe-24">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <!-- Preview Single Slide in Simulator -->
                                        <button type="button" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center border-0 hover-bg-info-main hover-text-white transition-2"
                                                onclick="previewSpecificAnnouncement(<?= (int) $a['id'] ?>)" title="Pratinjau di Simulator">
                                            <iconify-icon icon="solar:eye-bold" class="text-sm"></iconify-icon>
                                        </button>
                                        <!-- Edit Slide -->
                                        <a href="<?= site_url('dashboard/landing/pengumuman/ubah/' . $a['id']) ?>"
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2"
                                           title="Ubah Slide">
                                            <iconify-icon icon="solar:pen-new-square-linear" class="text-sm"></iconify-icon>
                                        </a>
                                        <!-- Duplicate Slide -->
                                        <a href="<?= site_url('dashboard/landing/pengumuman/duplikat/' . $a['id']) ?>"
                                           class="w-32-px h-32-px bg-primary-focus text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-primary-600 hover-text-white transition-2"
                                           title="Duplikat sebagai Draf">
                                            <iconify-icon icon="solar:copy-bold" class="text-sm"></iconify-icon>
                                        </a>
                                        <!-- Delete Slide -->
                                        <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0 hover-bg-danger-main hover-text-white transition-2 btn-delete-announcement"
                                                data-id="<?= (int) $a['id'] ?>"
                                                data-title="<?= esc($a['title']) ?>"
                                                title="Hapus Slide">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" class="text-sm"></iconify-icon>
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

<!-- Modal 1: Public Popup Simulator Modal (100% Mirror of Homepage Popup) -->
<div class="modal fade" id="popupSimulatorModal" tabindex="-1" aria-labelledby="popupSimulatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:play-stream-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title text-md fw-semibold text-primary-light mb-0" id="popupSimulatorModalLabel">Simulasi Tampilan Popup Beranda</h6>
                        <span class="text-secondary-light text-xs">Pratinjau dialog modal yang muncul saat pengguna pertama kali membuka portal utama.</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 bg-neutral-100 text-center">
                
                <!-- Dialog Popup Simulator Card -->
                <div class="mx-auto radius-16 overflow-hidden shadow-2xl border border-neutral-300 text-start bg-white" style="max-width: 580px;" id="simCard">
                    <!-- Popup Header Bar -->
                    <div class="p-14 px-20 border-bottom d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="w-8-px h-8-px rounded-circle bg-success-main animate-pulse"></span>
                            <span class="text-xs fw-bold text-white text-uppercase tracking-wider" id="simPopupBadgeText">
                                <?= esc($popupBadge) ?>
                            </span>
                        </div>
                        <button type="button" class="btn p-0 text-white opacity-70 hover-opacity-100 border-0 bg-transparent" data-bs-dismiss="modal">
                            <iconify-icon icon="solar:close-circle-bold" class="text-lg"></iconify-icon>
                        </button>
                    </div>

                    <!-- Popup Media Banner -->
                    <div class="position-relative overflow-hidden bg-dark" style="height: 240px;">
                        <img id="simImg" src="<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>" class="w-100 h-100 object-fit-cover" alt="Banner">
                        <div class="position-absolute bottom-0 start-0 w-100 h-50" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.65) 100%);"></div>
                        <span id="simBadgeLabel" class="position-absolute top-0 start-0 m-16 badge bg-warning-main text-white text-xs px-12 py-6 radius-6 fw-bold">
                            AGENDA &amp; BIMTEK
                        </span>
                    </div>

                    <!-- Popup Body Content -->
                    <div class="p-20">
                        <h4 id="simTitle" class="text-lg fw-bold text-primary-light mb-8">Judul Slide Pengumuman</h4>
                        <p id="simDesc" class="text-xs text-secondary-light mb-20 line-clamp-3">Deskripsi pengumuman yang menginformasikan rincian kegiatan atau pesan prioritas kepada pengunjung.</p>
                        
                        <div class="d-flex align-items-center justify-content-between gap-3 pt-12 border-top">
                            <a href="#" id="simCtaBtn" class="btn btn-primary-600 radius-8 px-20 py-10 text-xs fw-semibold d-inline-flex align-items-center gap-2" target="_blank">
                                <iconify-icon icon="solar:calendar-check-bold" class="text-sm"></iconify-icon>
                                <span id="simCtaText">Lihat Detail</span>
                            </a>
                            <button type="button" class="btn btn-neutral-200 text-secondary-light radius-8 px-16 py-10 text-xs fw-semibold" data-bs-dismiss="modal">
                                Tutup
                            </button>
                        </div>
                    </div>

                    <!-- Simulator Slide Stepper Controls -->
                    <div class="p-10 px-20 bg-neutral-50 border-top d-flex align-items-center justify-content-between text-xs text-secondary-light">
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-6 px-10 py-4 text-xs" id="simPrevSlideBtn">
                            &larr; Slide Sebelumnya
                        </button>
                        <span id="simSlideIndicator">Slide 1 dari <?= max(1, count($announcements)) ?></span>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-6 px-10 py-4 text-xs" id="simNextSlideBtn">
                            Slide Berikutnya &rarr;
                        </button>
                    </div>
                </div>

            </div>
            <div class="modal-footer py-14 px-24 border-top">
                <button type="button" class="btn btn-outline-neutral-300 text-sm px-20 py-10 radius-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Lightbox Image Viewer Modal -->
<div class="modal fade" id="announcementImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="card radius-16 overflow-hidden bg-base border border-neutral-300">
                <div class="card-header py-12 px-20 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <span id="announcementImgBadge" class="badge bg-warning-100 text-warning-main text-xs px-10 py-4 radius-4 fw-medium mb-1">Banner</span>
                        <h6 id="announcementImgTitle" class="text-sm fw-semibold text-primary-light mb-0">Judul Cover</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="card-body p-0 bg-dark text-center" style="max-height: 520px; overflow: hidden;">
                    <img id="announcementImgTag" src="" alt="Banner Full" class="w-100 h-100 object-fit-contain" style="max-height: 520px;">
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
window.ANNOUNCEMENT_ITEMS = <?= json_encode($announcements) ?>;

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------------------
    // 1. Filter Pills & Quick Search Handling
    // -------------------------------------------------------------------------
    const searchInput = document.getElementById('announcementSearch');
    const searchClearBtn = document.getElementById('searchClearBtn');
    const visibleCountEl = document.getElementById('visibleCount');
    const tableRows = document.querySelectorAll('.announcement-row');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const activePill = document.querySelector('.filter-pill.active');
        const filterVal = activePill ? activePill.dataset.filter : 'all';

        let count = 0;
        tableRows.forEach(row => {
            const textMatch = query === '' || row.textContent.toLowerCase().includes(query);
            let statusMatch = true;

            if (filterVal === 'active') {
                statusMatch = row.dataset.status === 'active';
            } else if (filterVal === 'inactive') {
                statusMatch = row.dataset.status === 'inactive';
            }

            if (textMatch && statusMatch) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCountEl) visibleCountEl.textContent = count;
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            if (searchClearBtn) {
                searchClearBtn.classList.toggle('d-none', this.value.trim().length === 0);
            }
            filterTable();
        });
    }

    if (searchClearBtn) {
        searchClearBtn.addEventListener('click', function () {
            searchInput.value = '';
            searchClearBtn.classList.add('d-none');
            filterTable();
            searchInput.focus();
        });
    }

    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.filter-pill').forEach(p => {
                p.classList.remove('active', 'btn-primary-600');
                p.classList.add('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');
            });
            this.classList.add('active', 'btn-primary-600');
            this.classList.remove('btn-outline-neutral-300', 'bg-white', 'text-secondary-light');
            filterTable();
        });
    });

    // -------------------------------------------------------------------------
    // 2. Preset Badge Chips
    // -------------------------------------------------------------------------
    document.querySelectorAll('.preset-badge-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const badgeInput = document.getElementById('popupBadgeInput');
            if (badgeInput) {
                badgeInput.value = this.dataset.text;
                badgeInput.focus();
            }
        });
    });

    // -------------------------------------------------------------------------
    // 3. AJAX Global Settings Form Submission
    // -------------------------------------------------------------------------
    const settingsForm = document.getElementById('popupSettingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveSettings');
            if (btn) btn.disabled = true;

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (btn) btn.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                    // Update KPI badge
                    const kpiBadge = document.getElementById('kpiPopupStatusBadge');
                    const isEnabled = data.data.popup_enabled === '1';
                    if (kpiBadge) {
                        kpiBadge.className = `badge radius-6 px-10 py-4 text-xs ${isEnabled ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-secondary-light'}`;
                        kpiBadge.textContent = isEnabled ? 'Aktif Menayangkan' : 'Dinonaktifkan';
                    }
                    const simBadgeText = document.getElementById('simPopupBadgeText');
                    if (simBadgeText) {
                        simBadgeText.textContent = data.data.popup_badge;
                    }
                } else {
                    showToast(data.message || 'Gagal menyimpan pengaturan.', 'danger');
                }
            })
            .catch(err => {
                if (btn) btn.disabled = false;
                showToast('Terjadi kesalahan koneksi server.', 'danger');
            });
        });
    }

    // -------------------------------------------------------------------------
    // 4. AJAX Toggle Switch for is_active
    // -------------------------------------------------------------------------
    document.addEventListener('change', function (e) {
        if (!e.target.matches('.announcement-toggle-switch')) return;

        const toggleEl = e.target;
        const id       = toggleEl.dataset.id;
        const row      = toggleEl.closest('tr');

        toggleEl.disabled = true;

        fetch(`<?= site_url('dashboard/landing/pengumuman/toggle') ?>/${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(res => res.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.success) {
                showToast(data.message, 'success');
                if (row) {
                    row.dataset.status = data.is_active ? 'active' : 'inactive';
                }
            } else {
                toggleEl.checked = !toggleEl.checked;
                showToast(data.message || 'Gagal mengubah status.', 'danger');
            }
        })
        .catch(err => {
            toggleEl.disabled = false;
            toggleEl.checked = !toggleEl.checked;
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    });

    // -------------------------------------------------------------------------
    // 5. AJAX Reorder Sort Order
    // -------------------------------------------------------------------------
    document.addEventListener('change', function (e) {
        if (!e.target.matches('.order-input')) return;

        const inputEl = e.target;
        const id      = inputEl.dataset.id;
        const order   = inputEl.value;

        inputEl.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('order', order);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= site_url('dashboard/landing/pengumuman/update-order') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            inputEl.style.opacity = '1';
            if (data.success) {
                inputEl.classList.add('bg-success-100', 'border-success-400');
                setTimeout(() => inputEl.classList.remove('bg-success-100', 'border-success-400'), 1200);
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal memperbarui urutan.', 'danger');
            }
        })
        .catch(err => {
            inputEl.style.opacity = '1';
            showToast('Terjadi kesalahan koneksi server.', 'danger');
        });
    });

    // -------------------------------------------------------------------------
    // 6. Delete Confirmation with SweetAlert2
    // -------------------------------------------------------------------------
    document.addEventListener('click', function (e) {
        const delBtn = e.target.closest('.btn-delete-announcement');
        if (!delBtn) return;

        const id    = delBtn.dataset.id;
        const title = delBtn.dataset.title;

        const executeDelete = () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `<?= site_url('dashboard/landing/pengumuman/hapus') ?>/${id}`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Slide Pengumuman?',
                html: `Anda akan menghapus pengumuman <strong>"${title}"</strong>.<br><small class="text-secondary-light">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Slide',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm(`Hapus slide pengumuman "${title}"?`)) {
                executeDelete();
            }
        }
    });

    // -------------------------------------------------------------------------
    // 7. Lightbox Image Viewer
    // -------------------------------------------------------------------------
    window.openAnnouncementImagePreview = function (imgUrl, title, badgeLabel) {
        document.getElementById('announcementImgTag').src = imgUrl;
        document.getElementById('announcementImgTitle').textContent = title || 'Pratinjau Banner';
        document.getElementById('announcementImgBadge').textContent = badgeLabel || 'PENGUMUMAN';

        const modalEl = document.getElementById('announcementImageModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };

    // -------------------------------------------------------------------------
    // 8. Public Popup Simulator Logic
    // -------------------------------------------------------------------------
    let currentSimIndex = 0;
    const items = window.ANNOUNCEMENT_ITEMS || [];

    function renderSimulator(index) {
        if (!items || items.length === 0) return;
        if (index < 0) index = items.length - 1;
        if (index >= items.length) index = 0;
        currentSimIndex = index;

        const item = items[currentSimIndex];
        const fallback = '<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>';
        const imgSrc = item.image_url ? (item.image_url.startsWith('http') ? item.image_url : `<?= base_url() ?>/${item.image_url}`) : fallback;

        document.getElementById('simImg').src = imgSrc;
        document.getElementById('simBadgeLabel').textContent = item.badge_label || 'PENGUMUMAN RESMI';
        document.getElementById('simTitle').textContent = item.title || 'Judul Pengumuman';
        document.getElementById('simDesc').textContent = item.description || 'Deskripsi detail pengumuman.';
        document.getElementById('simCtaText').textContent = item.cta_label || 'Lihat Detail';
        document.getElementById('simCtaBtn').href = item.cta_url ? (item.cta_url.startsWith('http') ? item.cta_url : `<?= site_url() ?>/${item.cta_url}`) : '#';
        document.getElementById('simSlideIndicator').textContent = `Slide ${currentSimIndex + 1} dari ${items.length}`;
    }

    const prevBtn = document.getElementById('simPrevSlideBtn');
    const nextBtn = document.getElementById('simNextSlideBtn');
    if (prevBtn) prevBtn.addEventListener('click', () => renderSimulator(currentSimIndex - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => renderSimulator(currentSimIndex + 1));

    window.previewSpecificAnnouncement = function (id) {
        const foundIdx = items.findIndex(i => parseInt(i.id) === parseInt(id));
        if (foundIdx !== -1) {
            currentSimIndex = foundIdx;
        }
        renderSimulator(currentSimIndex);
        const modalEl = document.getElementById('popupSimulatorModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };

    const simModalEl = document.getElementById('popupSimulatorModal');
    if (simModalEl) {
        simModalEl.addEventListener('shown.bs.modal', () => renderSimulator(currentSimIndex));
    }

    // -------------------------------------------------------------------------
    // 9. Toast Notification System
    // -------------------------------------------------------------------------
    function showToast(message, type) {
        let container = document.getElementById('announcementToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'announcementToastContainer';
            container.style.position = 'fixed';
            container.style.bottom = '24px';
            container.style.right = '24px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const isSuccess = type === 'success';
        const alertClass = isSuccess ? 'bg-success-100 text-success-600 border-success-200' : 'bg-danger-100 text-danger-600 border-danger-200';
        const iconName = isSuccess ? 'solar:check-circle-bold' : 'solar:danger-triangle-bold';

        const toast = document.createElement('div');
        toast.className = `alert ${alertClass} border shadow-lg py-12 px-16 radius-8 d-flex align-items-center gap-2 mb-2 text-sm fw-medium`;
        toast.style.minWidth = '280px';
        toast.innerHTML = `<iconify-icon icon="${iconName}" class="text-xl"></iconify-icon><span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
<?= $this->endSection() ?>
