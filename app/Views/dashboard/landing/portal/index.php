<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Portal Ekosistem PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Kelola tautan resmi sistem pengadaan nasional dan daerah (SPSE LPSE Lampung, SiRUP, E-Katalog, Toko Daring, Portal LKPP, JDIH).</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#portalPreviewModal">
            <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            <span>Pratinjau Publik</span>
        </button>
        <?php if (empty($portals)): ?>
            <a href="<?= site_url('dashboard/landing/portal-eksternal/muat-rekomendasi') ?>" class="btn btn-warning-600 radius-8 px-16 py-10 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:magic-stick-3-bold" class="text-xl"></iconify-icon>
                <span>Muat Rekomendasi PBJ</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/landing/portal-eksternal/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
            <span>Tambah Portal</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'portal', 'activeSub' => 'portal-eksternal']) ?>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Portal Mitra</p>
                        <h6 class="mb-0 fw-bold"><?= number_format($stats['total'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Sistem ekosistem terintegrasi</small>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:global-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
                        <p class="fw-medium text-primary-light mb-1">Portal Aktif</p>
                        <h6 class="mb-0 fw-bold text-success-main"><?= number_format($stats['active_count'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Tayang di beranda publik</small>
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
                        <h6 class="mb-0 fw-bold text-warning-main"><?= number_format($stats['inactive_count'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Disembunyikan dari beranda</small>
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
                        <p class="fw-medium text-primary-light mb-1">Nasional &amp; Daerah</p>
                        <h6 class="mb-0 fw-bold text-primary-main">
                            <?= $stats['nasional_count'] ?? 0 ?> <span class="text-secondary-light fw-normal text-xs">Nas</span> / <?= $stats['daerah_count'] ?? 0 ?> <span class="text-secondary-light fw-normal text-xs">Dae</span>
                        </h6>
                        <small class="text-secondary-light text-xs">Komposisi cakupan layanan</small>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:layers-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card Container -->
<div class="card border">
    <div class="card-header bg-white border-bottom p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Filter Pills -->
            <div class="d-flex align-items-center gap-2 flex-wrap" id="portalFilterGroup">
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 active px-12 py-6 filter-btn" data-filter="all">
                    Semua (<span id="countAll"><?= count($portals) ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="nasional">
                    Nasional (<span id="countNasional"><?= $stats['nasional_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="daerah">
                    Daerah (<span id="countDaerah"><?= $stats['daerah_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="active">
                    Aktif (<span id="countActive"><?= $stats['active_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="inactive">
                    Nonaktif (<span id="countInactive"><?= $stats['inactive_count'] ?></span>)
                </button>
            </div>

            <!-- Quick Search Input -->
            <div class="position-relative" style="min-width: 260px;">
                <input type="text" id="portalSearchInput" class="form-control form-control-sm radius-8 ps-36 pe-32" placeholder="Cari nama, akronim, URL...">
                <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-base"></iconify-icon>
                <button type="button" id="clearPortalSearchBtn" class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-10 text-secondary-light d-none" title="Bersihkan pencarian">
                    <iconify-icon icon="solar:close-circle-bold" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="portalTable">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Urutan</th>
                        <th style="width: 60px;" class="text-center">Logo / Ikon</th>
                        <th style="min-width: 200px;">Nama &amp; Akronim</th>
                        <th style="width: 120px;">Kategori</th>
                        <th style="min-width: 260px;">Tautan Resmi (URL)</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 130px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="portalTableBody">
                    <?php if (empty($portals)): ?>
                        <tr id="emptyPortalRow">
                            <td colspan="7" class="text-center py-48">
                                <div class="max-w-400 mx-auto text-center">
                                    <div class="w-64-px h-64-px rounded-circle bg-neutral-100 d-inline-flex align-items-center justify-content-center text-neutral-400 mb-16">
                                        <iconify-icon icon="solar:global-linear" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="fw-semibold text-primary-light mb-8">Belum Ada Portal Mitra Ekosistem</h6>
                                    <p class="text-secondary-light text-xs mb-16">Tambahkan portal eksternal pengadaan atau muat rekomendasi standar PBJ nasional dan daerah.</p>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/muat-rekomendasi') ?>" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                                            <span>Muat Rekomendasi PBJ</span>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/tambah') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:add-circle-bold" class="text-base"></iconify-icon>
                                            <span>Tambah Manual</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($portals as $p): ?>
                            <?php 
                            $isImg = str_contains($p['logo_url'], '/') || str_contains($p['logo_url'], '.');
                            $logoSrc = $isImg ? (str_starts_with($p['logo_url'], 'http') ? $p['logo_url'] : base_url($p['logo_url'])) : '';
                            $isDaerah = ($p['category'] === 'daerah');
                            $catBadge = $isDaerah ? 'bg-info-100 text-info-700' : 'bg-warning-100 text-warning-800';
                            $statusClass = $p['is_active'] ? 'active-row' : 'inactive-row';
                            ?>
                            <tr class="portal-row <?= $statusClass ?>" 
                                data-id="<?= $p['id'] ?>" 
                                data-status="<?= $p['is_active'] ? 'active' : 'inactive' ?>" 
                                data-category="<?= esc($p['category']) ?>">
                                
                                <!-- Urutan -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <input type="number" 
                                               class="form-control form-control-sm text-center fw-bold order-input px-1" 
                                               style="width: 50px;" 
                                               value="<?= esc($p['sort_order']) ?>" 
                                               min="1" 
                                               max="99" 
                                               data-id="<?= $p['id'] ?>" 
                                               title="Ubah urutan lalu tekan Enter">
                                        <span class="order-saved-check d-none text-success-main" title="Tersimpan!">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                        </span>
                                    </div>
                                </td>

                                <!-- Logo / Ikon -->
                                <td class="text-center">
                                    <div class="radius-8 d-inline-flex align-items-center justify-content-center border shadow-xs bg-white" style="width: 44px; height: 44px;">
                                        <?php if ($isImg): ?>
                                            <img src="<?= esc($logoSrc) ?>" alt="<?= esc($p['name']) ?>" style="max-width: 32px; max-height: 32px; object-fit: contain;">
                                        <?php else: ?>
                                            <iconify-icon icon="<?= esc($p['logo_url'] ?: 'solar:global-bold') ?>" class="text-2xl text-primary-600"></iconify-icon>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Nama & Akronim -->
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <strong class="text-sm text-primary-light portal-name"><?= esc($p['name']) ?></strong>
                                        <?php if (! empty($p['acronym'])): ?>
                                            <span class="badge bg-neutral-200 text-neutral-800 font-monospace text-2xs px-6 py-2 radius-4 fw-bold">
                                                <?= esc($p['acronym']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (! empty($p['description'])): ?>
                                        <small class="text-secondary-light text-xs d-block portal-desc" style="max-width: 360px; line-height: 1.4;">
                                            <?= esc($p['description']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <!-- Kategori -->
                                <td>
                                    <span class="badge <?= $catBadge ?> text-xs text-uppercase radius-4 px-8 py-3 fw-semibold">
                                        <?= esc($p['category']) ?>
                                    </span>
                                </td>

                                <!-- URL Tautan Resmi -->
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:link-round-linear" class="text-primary-600 text-sm flex-shrink-0"></iconify-icon>
                                        <a href="<?= esc($p['url']) ?>" target="_blank" rel="noopener noreferrer" 
                                           class="text-primary-600 hover-text-primary-800 text-xs text-decoration-none text-truncate fw-medium portal-url" 
                                           style="max-width: 280px;" 
                                           title="<?= esc($p['url']) ?>">
                                            <?= esc($p['url']) ?>
                                        </a>
                                        <a href="<?= esc($p['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-secondary-light hover-text-primary-600 ms-1" title="Buka tautan di tab baru">
                                            <iconify-icon icon="solar:arrow-right-up-linear" class="text-xs"></iconify-icon>
                                        </a>
                                    </div>
                                </td>

                                <!-- Status Toggle -->
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 <?= $p['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-neutral-600' ?>" 
                                            data-id="<?= $p['id'] ?>" 
                                            data-active="<?= $p['is_active'] ? '1' : '0' ?>"
                                            title="Klik untuk mengubah status tayang">
                                        <span class="status-label"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                    </button>
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/ubah/' . $p['id']) ?>" 
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2" 
                                           title="Ubah Portal">
                                            <iconify-icon icon="solar:pen-new-square-linear" class="text-base"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/portal-eksternal/duplikat/' . $p['id']) ?>" 
                                           class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-info-main hover-text-white transition-2" 
                                           title="Duplikat sebagai Draf">
                                            <iconify-icon icon="solar:copy-linear" class="text-base"></iconify-icon>
                                        </a>
                                        <button type="button" 
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white border-0 transition-2 btn-delete-portal" 
                                                data-id="<?= $p['id'] ?>" 
                                                data-name="<?= esc($p['name']) ?>"
                                                title="Hapus Portal">
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

<!-- Public Ecosystem Simulator Modal -->
<div class="modal fade" id="portalPreviewModal" tabindex="-1" aria-labelledby="portalPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header bg-neutral-50 px-24 py-16 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px rounded-8 bg-primary-600 d-flex align-items-center justify-content-center text-white">
                        <iconify-icon icon="solar:global-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-primary-light mb-0" id="portalPreviewModalLabel">
                            Pratinjau Publik: Kartu Portal Ekosistem PBJ
                        </h6>
                        <small class="text-secondary-light">Tampilan live widget portal mitra ekosistem di halaman beranda BegawiPengadaan.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-32 bg-light">
                <div class="card border-0 shadow-sm radius-16 p-24 bg-white max-w-900 mx-auto">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-16 mb-20">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-44-px h-44-px rounded-12 bg-primary-100 text-primary-600 d-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:global-bold" class="text-2xl"></iconify-icon>
                            </div>
                            <div>
                                <h5 class="fw-bold text-neutral-900 mb-2">Portal Mitra Ekosistem Nasional &amp; Daerah</h5>
                                <p class="text-secondary-light text-xs mb-0">Akses langsung ke sistem layanan pengadaan resmi pemerintah</p>
                            </div>
                        </div>
                        <span class="badge bg-success-100 text-success-600 border border-success-200 text-2xs fw-bold px-10 py-5 radius-8 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:verified-check-bold"></iconify-icon> Tautan Resmi
                        </span>
                    </div>

                    <div class="row g-16">
                        <?php 
                        $activePortals = array_filter($portals, fn($p) => (int)($p['is_active'] ?? 0) === 1);
                        ?>
                        <?php if (empty($activePortals)): ?>
                            <div class="col-12 text-center py-32 text-secondary-light">
                                <p class="mb-0">Tidak ada portal eksternal aktif untuk ditampilkan dalam pratinjau publik saat ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($activePortals as $ap): ?>
                                <?php
                                $apImg = str_contains($ap['logo_url'], '/') || str_contains($ap['logo_url'], '.');
                                $apSrc = $apImg ? (str_starts_with($ap['logo_url'], 'http') ? $ap['logo_url'] : base_url($ap['logo_url'])) : '';
                                $apIsDaerah = ($ap['category'] === 'daerah');
                                $accentColor = $apIsDaerah ? '#0284c7' : '#10b981';
                                $accentBg = $apIsDaerah ? '#eff6ff' : '#ecfdf5';
                                ?>
                                <div class="col-md-6">
                                    <a href="<?= esc($ap['url']) ?>" target="_blank" rel="noopener noreferrer" 
                                       class="d-flex align-items-center gap-3 p-16 radius-12 border text-decoration-none transition-2 h-100" 
                                       style="background: #ffffff; border-color: #e2e8f0 !important;"
                                       onmouseover="this.style.borderColor='<?= $accentColor ?>'; this.style.transform='translateY(-2px)';"
                                       onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)';">
                                        
                                        <div class="radius-10 d-flex align-items-center justify-content-center flex-shrink-0" 
                                             style="width: 44px; height: 44px; background: <?= $accentBg ?>; color: <?= $accentColor ?>;">
                                            <?php if ($apImg): ?>
                                                <img src="<?= esc($apSrc) ?>" alt="<?= esc($ap['name']) ?>" style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php else: ?>
                                                <iconify-icon icon="<?= esc($ap['logo_url'] ?: 'solar:global-bold') ?>" class="text-2xl"></iconify-icon>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex-grow-1 overflow-hidden">
                                            <strong class="d-block text-neutral-900 text-sm text-truncate mb-1"><?= esc($ap['name']) ?></strong>
                                            <small class="text-secondary-light text-2xs d-block text-truncate"><?= esc($ap['description'] ?: $ap['url']) ?></small>
                                        </div>

                                        <div class="flex-shrink-0 text-secondary-light">
                                            <iconify-icon icon="solar:arrow-right-up-linear" class="text-base"></iconify-icon>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mt-20 pt-16 border-top d-flex align-items-center gap-2 text-2xs text-secondary-light">
                        <iconify-icon icon="solar:shield-check-bold" class="text-success-main text-base flex-shrink-0"></iconify-icon>
                        <span>Tautan resmi terenkripsi SSL/TLS langsung ke domain pemerintah (.go.id).</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white px-24 py-16 border-top">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form with CSRF -->
<form id="deletePortalForm" method="post" action="" class="d-none">
    <?= csrf_field() ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfHash      = '<?= csrf_hash() ?>';

    // 1. Filter Pills
    const filterBtns = document.querySelectorAll('.filter-btn');
    const rows       = document.querySelectorAll('.portal-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active', 'btn-primary-600', 'text-white'));
            this.classList.add('active', 'btn-primary-600', 'text-white');
            this.classList.remove('btn-outline-neutral-300');

            applyPortalFilters();
        });
    });

    // 2. Search Box with Clear Button
    const searchInput = document.getElementById('portalSearchInput');
    const clearBtn    = document.getElementById('clearPortalSearchBtn');

    if (searchInput && clearBtn) {
        searchInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            applyPortalFilters();
        });

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            applyPortalFilters();
            searchInput.focus();
        });
    }

    function applyPortalFilters() {
        const activeBtn = document.querySelector('.filter-btn.active');
        const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        const query  = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            const status   = row.getAttribute('data-status');
            const category = row.getAttribute('data-category');
            const text     = row.innerText.toLowerCase();

            let matchesFilter = true;
            if (filter === 'nasional') matchesFilter = (category === 'nasional');
            else if (filter === 'daerah') matchesFilter = (category === 'daerah');
            else if (filter === 'active') matchesFilter = (status === 'active');
            else if (filter === 'inactive') matchesFilter = (status === 'inactive');

            const matchesQuery = (query === '' || text.includes(query));

            if (matchesFilter && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 3. Inline Order Update
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

            fetch('<?= site_url('dashboard/landing/portal-eksternal/update-order') ?>', {
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

            fetch('<?= site_url('dashboard/landing/portal-eksternal/toggle/') ?>' + id, {
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
                    updateFilterPillCounts();
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error(err);
            });
        });
    });

    function updateFilterPillCounts() {
        const allRows = document.querySelectorAll('.portal-row');
        let activeCount = 0;
        let inactiveCount = 0;

        allRows.forEach(r => {
            if (r.getAttribute('data-status') === 'active') activeCount++;
            else inactiveCount++;
        });

        const elActive = document.getElementById('countActive');
        const elInactive = document.getElementById('countInactive');

        if (elActive) elActive.textContent = activeCount;
        if (elInactive) elInactive.textContent = inactiveCount;
    }

    // 5. SweetAlert2 Delete Confirmation
    const deleteBtns = document.querySelectorAll('.btn-delete-portal');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Portal Mitra?',
                    text: `Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`,
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
                if (confirm(`Anda yakin ingin menghapus "${name}"?`)) {
                    executeDelete(id);
                }
            }
        });
    });

    function executeDelete(id) {
        const form = document.getElementById('deletePortalForm');
        form.action = '<?= site_url('dashboard/landing/portal-eksternal/hapus/') ?>' + id;
        form.submit();
    }
});
</script>

<?= $this->endSection() ?>
