<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pilar, Budaya & Hierarki Regulasi') ?></h6>
        <p class="text-secondary-light mb-0">Kelola 5 Pilar BegawiPBJ, Budaya Kerja PATIK, 4 Tingkat Hierarki Hukum, Roadmap LKPP, dan Keamanan Digital Sertifikat.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#highlightPreviewModal">
            <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            <span>Pratinjau Publik</span>
        </button>
        <a href="<?= site_url('dashboard/landing/pilar-hierarki/tambah?section=' . urlencode($currentSection)) ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
            <span>Tambah Item</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'profile', 'activeSub' => 'pilar-hierarki']) ?>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Highlight</p>
                        <h6 class="mb-0 fw-bold"><?= number_format($stats['total_items'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Semua kategori ekosistem</small>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:widget-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Highlight Aktif</p>
                        <h6 class="mb-0 fw-bold text-success-main"><?= number_format($stats['active_items'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Tayang di portal beranda & tentang</small>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Draf / Nonaktif</p>
                        <h6 class="mb-0 fw-bold text-warning-main"><?= number_format($stats['inactive_items'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Disembunyikan dari publik</small>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:shield-warning-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kategori Ekosistem</p>
                        <h6 class="mb-0 fw-bold text-primary-main"><?= number_format($stats['total_sections'] ?? 5) ?></h6>
                        <small class="text-secondary-light text-xs">Pilar, Nilai, Regulasi & Keamanan</small>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:layers-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation for Feature Sections -->
<div class="card mb-20 border">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap">
            <?php foreach ($allSections as $key => $meta): ?>
                <?php 
                $count = $stats['section_counts'][$key]['total'] ?? 0;
                $isActiveTab = ($currentSection === $key);
                ?>
                <li class="nav-item">
                    <a class="nav-link radius-8 px-16 py-10 d-inline-flex align-items-center gap-2 text-sm fw-medium transition-2 <?= $isActiveTab ? 'active bg-primary-600 text-white shadow-sm' : 'text-secondary-light bg-neutral-100 hover-bg-neutral-200' ?>" 
                       href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($key)) ?>">
                        <iconify-icon icon="<?= esc($meta['icon']) ?>" class="text-lg <?= $isActiveTab ? 'text-white' : 'text-primary-600' ?>"></iconify-icon>
                        <span><?= esc($meta['name']) ?></span>
                        <span class="badge rounded-pill <?= $isActiveTab ? 'bg-white text-primary-600' : 'bg-neutral-300 text-neutral-700' ?> text-2xs px-8 py-3">
                            <?= $count ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Current Section Banner -->
<div class="card mb-24 border" style="border-left: 4px solid <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?> !important;">
    <div class="card-body p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-48-px h-48-px rounded-12 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="background: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>;">
                    <iconify-icon icon="<?= esc($sectionMeta['icon'] ?? 'solar:layers-bold') ?>" class="text-2xl"></iconify-icon>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge radius-4 px-8 py-3 text-2xs fw-semibold" style="background: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>15; color: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>;">
                            <?= esc($sectionMeta['tag'] ?? 'Arsitektur') ?>
                        </span>
                        <h6 class="fw-semibold text-primary-light mb-0"><?= esc($sectionMeta['name']) ?></h6>
                    </div>
                    <p class="text-secondary-light text-xs mb-0"><?= esc($sectionMeta['subtitle']) ?></p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <?php if (empty($items)): ?>
                    <a href="<?= site_url('dashboard/landing/pilar-hierarki/muat-rekomendasi/' . urlencode($currentSection)) ?>" class="btn btn-warning-600 radius-8 px-16 py-8 d-flex align-items-center gap-2 text-xs">
                        <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                        <span>Muat Rekomendasi Standar</span>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-14 py-8 d-flex align-items-center gap-2 text-xs" data-bs-toggle="modal" data-bs-target="#highlightPreviewModal">
                    <iconify-icon icon="solar:laptop-minimalistic-bold" class="text-base text-primary-600"></iconify-icon>
                    <span>Simulasi Tampilan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="card border">
    <div class="card-header bg-white border-bottom p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Filter Pills -->
            <div class="d-flex align-items-center gap-2 flex-wrap" id="statusFilterGroup">
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 active px-12 py-6 filter-btn" data-status="all">
                    Semua (<span id="countAll"><?= count($items) ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-status="active">
                    Aktif (<span id="countActive"><?= count(array_filter($items, fn($i) => (int)($i['is_active'] ?? 0) === 1)) ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-status="inactive">
                    Nonaktif (<span id="countInactive"><?= count(array_filter($items, fn($i) => (int)($i['is_active'] ?? 0) === 0)) ?></span>)
                </button>
            </div>

            <!-- Search box -->
            <div class="position-relative" style="min-width: 260px;">
                <input type="text" id="tableSearchInput" class="form-control form-control-sm radius-8 ps-36 pe-32" placeholder="Cari item highlight...">
                <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-base"></iconify-icon>
                <button type="button" id="clearSearchBtn" class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-10 text-secondary-light d-none" title="Bersihkan pencarian">
                    <iconify-icon icon="solar:close-circle-bold" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="highlightTable">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Urutan</th>
                        <th style="width: 60px;" class="text-center">Ikon</th>
                        <th style="width: 140px;">Kode & Label</th>
                        <th style="min-width: 220px;">Judul & Subjudul</th>
                        <th style="min-width: 280px;">Deskripsi & Tautan</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 130px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="highlightTableBody">
                    <?php if (empty($items)): ?>
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-48">
                                <div class="max-w-400 mx-auto text-center">
                                    <div class="w-64-px h-64-px rounded-circle bg-neutral-100 d-inline-flex align-items-center justify-content-center text-neutral-400 mb-16">
                                        <iconify-icon icon="solar:box-minimalistic-linear" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="fw-semibold text-primary-light mb-8">Belum Ada Item pada Kategori Ini</h6>
                                    <p class="text-secondary-light text-xs mb-16">Mulai tambahkan item baru atau muat rekomendasi standar PBJ untuk kategori <?= esc($sectionMeta['name']) ?>.</p>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/muat-rekomendasi/' . urlencode($currentSection)) ?>" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                                            <span>Muat Rekomendasi Standar</span>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/tambah?section=' . urlencode($currentSection)) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:add-circle-bold" class="text-base"></iconify-icon>
                                            <span>Tambah Manual</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php 
                            $statusClass = $item['is_active'] ? 'active-row' : 'inactive-row';
                            $itemColor = $item['color_hex'] ?: ($sectionMeta['color'] ?? '#0c5aa6');
                            $itemIcon = $item['icon_class'] ?: ($sectionMeta['icon'] ?? 'solar:star-bold');
                            ?>
                            <tr class="highlight-row <?= $statusClass ?>" data-id="<?= $item['id'] ?>" data-status="<?= $item['is_active'] ? 'active' : 'inactive' ?>">
                                <!-- Urutan -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <input type="number" 
                                               class="form-control form-control-sm text-center fw-bold order-input px-1" 
                                               style="width: 50px;" 
                                               value="<?= esc($item['sort_order']) ?>" 
                                               min="1" 
                                               max="99" 
                                               data-id="<?= $item['id'] ?>"
                                               title="Ubah urutan lalu tekan Enter">
                                        <span class="order-saved-check d-none text-success-main" title="Tersimpan!">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                        </span>
                                    </div>
                                </td>

                                <!-- Ikon -->
                                <td class="text-center">
                                    <div class="radius-12 d-inline-flex align-items-center justify-content-center border shadow-xs" 
                                         style="width: 44px; height: 44px; background: <?= esc($itemColor) ?>15; color: <?= esc($itemColor) ?>;" 
                                         title="Aksen: <?= esc($itemColor) ?>">
                                        <iconify-icon icon="<?= esc($itemIcon) ?>" class="text-2xl"></iconify-icon>
                                    </div>
                                </td>

                                <!-- Kode & Badge -->
                                <td>
                                    <?php if (! empty($item['code'])): ?>
                                        <span class="badge bg-neutral-200 text-neutral-800 font-monospace text-xs mb-1 px-8 py-3 radius-4 fw-bold">
                                            <?= esc($item['code']) ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <?php if (! empty($item['badge_text'])): ?>
                                        <span class="badge radius-4 text-2xs px-8 py-3 fw-medium" style="background: <?= esc($itemColor) ?>18; color: <?= esc($itemColor) ?>;">
                                            <?= esc($item['badge_text']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-2xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Judul & Subjudul -->
                                <td>
                                    <strong class="text-sm d-block text-primary-light item-title mb-1"><?= esc($item['title']) ?></strong>
                                    <?php if (! empty($item['subtitle'])): ?>
                                        <small class="text-secondary-light item-subtitle d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:tag-horizontal-linear" class="text-xs"></iconify-icon>
                                            <span><?= esc($item['subtitle']) ?></span>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <!-- Deskripsi & Link -->
                                <td>
                                    <p class="text-xs text-secondary-light mb-1 item-desc" style="max-width: 440px; line-height: 1.5;">
                                        <?= esc($item['description'] ?: '-') ?>
                                    </p>
                                    <?php if (! empty($item['link_url'])): ?>
                                        <div class="d-flex align-items-center gap-1 mt-4">
                                            <iconify-icon icon="solar:link-round-linear" class="text-primary-600 text-xs"></iconify-icon>
                                            <a href="<?= filter_var($item['link_url'], FILTER_VALIDATE_URL) ? esc($item['link_url']) : site_url(esc($item['link_url'])) ?>" 
                                               target="_blank" 
                                               class="text-primary-600 hover-text-primary-800 text-2xs fw-medium text-decoration-none">
                                                <code><?= esc($item['link_url']) ?></code>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Toggle -->
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 <?= $item['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-neutral-600' ?>" 
                                            data-id="<?= $item['id'] ?>" 
                                            data-active="<?= $item['is_active'] ? '1' : '0' ?>"
                                            title="Klik untuk mengubah status tayang">
                                        <span class="status-label"><?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                    </button>
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/ubah/' . $item['id']) ?>" 
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2" 
                                           title="Ubah Item">
                                            <iconify-icon icon="solar:pen-new-square-linear" class="text-base"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/duplikat/' . $item['id']) ?>" 
                                           class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-info-main hover-text-white transition-2" 
                                           title="Duplikat sebagai Draf">
                                            <iconify-icon icon="solar:copy-linear" class="text-base"></iconify-icon>
                                        </a>
                                        <button type="button" 
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white border-0 transition-2 btn-delete-item" 
                                                data-id="<?= $item['id'] ?>" 
                                                data-title="<?= esc($item['title']) ?>"
                                                title="Hapus Item">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
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

<!-- Public Section Simulator Modal -->
<div class="modal fade" id="highlightPreviewModal" tabindex="-1" aria-labelledby="highlightPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header bg-neutral-50 px-24 py-16 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px rounded-8 d-flex align-items-center justify-content-center text-white" style="background: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>;">
                        <iconify-icon icon="<?= esc($sectionMeta['icon'] ?? 'solar:layers-bold') ?>" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-primary-light mb-0" id="highlightPreviewModalLabel">
                            Pratinjau Publik: <?= esc($sectionMeta['name']) ?>
                        </h6>
                        <small class="text-secondary-light">Tampilan live sebagaimana pengunjung melihatnya di portal resmi BegawiPengadaan.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-32 bg-light">
                <div class="text-center max-w-700 mx-auto mb-32">
                    <span class="badge radius-4 px-12 py-6 text-xs fw-semibold mb-8" style="background: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>18; color: <?= esc($sectionMeta['color'] ?? '#0c5aa6') ?>;">
                        <iconify-icon icon="<?= esc($sectionMeta['icon'] ?? 'solar:layers-bold') ?>" class="me-1"></iconify-icon>
                        <?= esc($sectionMeta['tag'] ?? 'Arsitektur Layanan') ?>
                    </span>
                    <h3 class="fw-bold text-neutral-900 mb-8"><?= esc($sectionMeta['name']) ?></h3>
                    <p class="text-secondary-light text-sm mb-0"><?= esc($sectionMeta['subtitle']) ?></p>
                </div>

                <div class="row g-20 justify-content-center">
                    <?php 
                    $activeDisplayItems = array_filter($items, fn($i) => (int)($i['is_active'] ?? 0) === 1);
                    ?>
                    <?php if (empty($activeDisplayItems)): ?>
                        <div class="col-12 text-center py-32 text-secondary-light">
                            <p class="mb-0">Tidak ada item aktif untuk ditampilkan dalam pratinjau publik saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeDisplayItems as $previewItem): ?>
                            <?php 
                            $cHex = $previewItem['color_hex'] ?: ($sectionMeta['color'] ?? '#0c5aa6');
                            $cIcon = $previewItem['icon_class'] ?: ($sectionMeta['icon'] ?? 'solar:star-bold');
                            ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-0 shadow-sm radius-12 p-24 transition-2" style="background: #ffffff; border-top: 3px solid <?= esc($cHex) ?> !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-16">
                                        <div class="radius-12 d-flex align-items-center justify-content-center text-white shadow-xs" 
                                             style="width: 48px; height: 48px; background: <?= esc($cHex) ?>;">
                                            <iconify-icon icon="<?= esc($cIcon) ?>" class="text-2xl"></iconify-icon>
                                        </div>
                                        <?php if (! empty($previewItem['code'])): ?>
                                            <span class="fw-bold font-monospace text-sm px-8 py-3 radius-4" style="background: <?= esc($cHex) ?>15; color: <?= esc($cHex) ?>;">
                                                <?= esc($previewItem['code']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h6 class="fw-bold text-neutral-900 mb-4"><?= esc($previewItem['title']) ?></h6>
                                    <?php if (! empty($previewItem['subtitle'])): ?>
                                        <small class="fw-medium text-secondary-light d-block mb-10"><?= esc($previewItem['subtitle']) ?></small>
                                    <?php endif; ?>
                                    <p class="text-secondary-light text-xs mb-16" style="line-height: 1.6;">
                                        <?= esc($previewItem['description'] ?: 'Deskripsi belum diset.') ?>
                                    </p>
                                    <?php if (! empty($previewItem['link_url'])): ?>
                                        <div class="mt-auto pt-10 border-top">
                                            <a href="<?= filter_var($previewItem['link_url'], FILTER_VALIDATE_URL) ? esc($previewItem['link_url']) : site_url(esc($previewItem['link_url'])) ?>" 
                                               target="_blank" 
                                               class="text-decoration-none text-xs fw-semibold d-inline-flex align-items-center gap-1" 
                                               style="color: <?= esc($cHex) ?>;">
                                                <span>Akses Layanan</span>
                                                <iconify-icon icon="solar:arrow-right-linear" class="text-sm"></iconify-icon>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer bg-white px-24 py-16 border-top">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form with CSRF -->
<form id="deleteItemForm" method="post" action="" class="d-none">
    <?= csrf_field() ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfHash      = '<?= csrf_hash() ?>';

    // 1. Live Filter Tabs (Semua, Aktif, Nonaktif)
    const filterBtns = document.querySelectorAll('.filter-btn');
    const tableRows  = document.querySelectorAll('.highlight-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active', 'btn-primary-600', 'text-white'));
            this.classList.add('active', 'btn-primary-600', 'text-white');
            this.classList.remove('btn-outline-neutral-300');

            const status = this.getAttribute('data-status');
            applyFilters();
        });
    });

    // 2. Quick Search with Clear button
    const searchInput = document.getElementById('tableSearchInput');
    const clearBtn    = document.getElementById('clearSearchBtn');

    if (searchInput && clearBtn) {
        searchInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            applyFilters();
        });

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            applyFilters();
            searchInput.focus();
        });
    }

    function applyFilters() {
        const activeBtn = document.querySelector('.filter-btn.active');
        const targetStatus = activeBtn ? activeBtn.getAttribute('data-status') : 'all';
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        tableRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowText = row.innerText.toLowerCase();

            const statusMatch = (targetStatus === 'all' || rowStatus === targetStatus);
            const queryMatch  = (query === '' || rowText.includes(query));

            if (statusMatch && queryMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 3. Inline Order Update on Enter or Blur
    const orderInputs = document.querySelectorAll('.order-input');
    orderInputs.forEach(input => {
        const handleOrderSave = function () {
            const id = input.getAttribute('data-id');
            const newOrder = input.value;
            const checkIcon = input.nextElementSibling;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('order', newOrder);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('dashboard/landing/pilar-hierarki/update-order') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (checkIcon) {
                        checkIcon.classList.remove('d-none');
                        setTimeout(() => checkIcon.classList.add('d-none'), 1800);
                    }
                }
            })
            .catch(err => console.error(err));
        };

        input.addEventListener('change', handleOrderSave);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            }
        });
    });

    // 4. AJAX Toggle Status
    const toggleBtns = document.querySelectorAll('.btn-toggle-status');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const row = this.closest('tr');
            const labelSpan = this.querySelector('.status-label');

            btn.disabled = true;

            fetch('<?= site_url('dashboard/landing/pilar-hierarki/toggle/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    const isActive = (data.is_active === 1);
                    btn.setAttribute('data-active', isActive ? '1' : '0');
                    if (row) {
                        row.setAttribute('data-status', isActive ? 'active' : 'inactive');
                        row.classList.toggle('active-row', isActive);
                        row.classList.toggle('inactive-row', !isActive);
                    }
                    if (isActive) {
                        btn.className = 'btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 bg-success-100 text-success-600';
                        if (labelSpan) labelSpan.textContent = 'Aktif';
                    } else {
                        btn.className = 'btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 bg-neutral-200 text-neutral-600';
                        if (labelSpan) labelSpan.textContent = 'Nonaktif';
                    }
                    updateFilterCounts();
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error(err);
            });
        });
    });

    function updateFilterCounts() {
        const allRows = document.querySelectorAll('.highlight-row');
        let activeCount = 0;
        let inactiveCount = 0;

        allRows.forEach(r => {
            if (r.getAttribute('data-status') === 'active') activeCount++;
            else inactiveCount++;
        });

        const elAll = document.getElementById('countAll');
        const elActive = document.getElementById('countActive');
        const elInactive = document.getElementById('countInactive');

        if (elAll) elAll.textContent = allRows.length;
        if (elActive) elActive.textContent = activeCount;
        if (elInactive) elInactive.textContent = inactiveCount;
    }

    // 5. SweetAlert2 Delete Confirmation
    const deleteBtns = document.querySelectorAll('.btn-delete-item');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Item Highlight?',
                    text: `Anda yakin ingin menghapus "${title}"? Tindakan ini tidak dapat dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeDelete(id);
                    }
                });
            } else {
                if (confirm(`Anda yakin ingin menghapus "${title}"?`)) {
                    executeDelete(id);
                }
            }
        });
    });

    function executeDelete(id) {
        const form = document.getElementById('deleteItemForm');
        form.action = '<?= site_url('dashboard/landing/pilar-hierarki/hapus/') ?>' + id;
        form.submit();
    }
});
</script>

<?= $this->endSection() ?>
