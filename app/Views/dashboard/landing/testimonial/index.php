<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Testimoni Pengguna & Mitra PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Ulasan nyata pimpinan daerah, PPK dinas, Pokja UKPBJ, auditor APIP, dan pelaku usaha penyedia barang/jasa beserta dampak nyata terukur.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#testimonialPreviewModal">
            <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            <span>Pratinjau Publik</span>
        </button>
        <?php if (empty($testimonials)): ?>
            <a href="<?= site_url('dashboard/landing/testimoni/muat-rekomendasi') ?>" class="btn btn-warning-600 radius-8 px-16 py-10 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:magic-stick-3-bold" class="text-xl"></iconify-icon>
                <span>Muat Rekomendasi Testimoni</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/landing/testimoni/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
            <span>Tambah Testimoni</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'team', 'activeSub' => 'testimoni']) ?>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Testimoni</p>
                        <h6 class="mb-0 fw-bold"><?= number_format($stats['total'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Ulasan stakeholder PBJ</small>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:chat-round-dots-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
                        <p class="fw-medium text-primary-light mb-1">Testimoni Aktif</p>
                        <h6 class="mb-0 fw-bold text-success-main"><?= number_format($stats['active_count'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Tayang di carousel beranda</small>
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
                        <small class="text-secondary-light text-xs">Disembunyikan sementara</small>
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
                        <p class="fw-medium text-primary-light mb-1">Rating &amp; Stakeholder</p>
                        <h6 class="mb-0 fw-bold text-warning-main d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:star-bold" class="text-warning-main"></iconify-icon>
                            <span><?= esc($stats['avg_rating'] ?? '5.0') ?></span>
                            <span class="text-secondary-light fw-normal text-xs ms-1">/ 5.0</span>
                        </h6>
                        <small class="text-secondary-light text-xs">
                            <?= $stats['opd_count'] ?? 0 ?> OPD &bull; <?= $stats['pokja_count'] ?? 0 ?> Pokja &bull; <?= $stats['penyedia_count'] ?? 0 ?> Mitra
                        </small>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:star-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
            <div class="d-flex align-items-center gap-2 flex-wrap" id="testimonialFilterGroup">
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 active px-12 py-6 filter-btn" data-filter="all">
                    Semua (<span id="countAll"><?= count($testimonials) ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="opd">
                    PPK / OPD (<span id="countOpd"><?= $stats['opd_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="pokja">
                    Pokja UKPBJ (<span id="countPokja"><?= $stats['pokja_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="penyedia">
                    Penyedia / UMKM (<span id="countPenyedia"><?= $stats['penyedia_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="auditor">
                    Auditor APIP (<span id="countAuditor"><?= $stats['auditor_count'] ?></span>)
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
                <input type="text" id="testimonialSearchInput" class="form-control form-control-sm radius-8 ps-36 pe-32" placeholder="Cari responden, instansi, kutipan...">
                <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-base"></iconify-icon>
                <button type="button" id="clearTestimonialSearchBtn" class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-10 text-secondary-light d-none" title="Bersihkan pencarian">
                    <iconify-icon icon="solar:close-circle-bold" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="testimonialTable">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Urutan</th>
                        <th style="width: 70px;" class="text-center">Avatar</th>
                        <th style="min-width: 200px;">Responden &amp; Instansi</th>
                        <th style="min-width: 280px;">Kutipan Ulasan &amp; Rating</th>
                        <th style="min-width: 220px;">Dampak Nyata Terukur</th>
                        <th style="width: 120px;">Kategori</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="testimonialTableBody">
                    <?php if (empty($testimonials)): ?>
                        <tr id="emptyTestimonialRow">
                            <td colspan="8" class="text-center py-48">
                                <div class="max-w-400 mx-auto text-center">
                                    <div class="w-64-px h-64-px rounded-circle bg-neutral-100 d-inline-flex align-items-center justify-content-center text-neutral-400 mb-16">
                                        <iconify-icon icon="solar:chat-round-dots-linear" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="fw-semibold text-primary-light mb-8">Belum Ada Testimoni Pengguna</h6>
                                    <p class="text-secondary-light text-xs mb-16">Tambahkan testimoni nyata stakeholder atau muat rekomendasi standar ulasan ekosistem PBJ.</p>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/testimoni/muat-rekomendasi') ?>" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                                            <span>Muat Rekomendasi Testimoni</span>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/testimoni/tambah') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:add-circle-bold" class="text-base"></iconify-icon>
                                            <span>Tambah Manual</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($testimonials as $t): ?>
                            <?php
                            $avatar = $t['avatar_url'] ?: 'assets/main/images/testimonial/testimonial-1-1.jpg';
                            $avatarSrc = str_starts_with($avatar, 'http') ? $avatar : base_url($avatar);
                            $rating = (int) ($t['rating'] ?: 5);

                            $rawCat = strtolower($t['category'] ?? 'opd');
                            $catGroup = match(true) {
                                str_contains($rawCat, 'opd') || str_contains($rawCat, 'ppk') || str_contains($rawCat, 'teknis') || str_contains($rawCat, 'pimpinan') => 'opd',
                                str_contains($rawCat, 'pokja') => 'pokja',
                                str_contains($rawCat, 'penyedia') || str_contains($rawCat, 'umkm') => 'penyedia',
                                str_contains($rawCat, 'auditor') || str_contains($rawCat, 'apip') => 'auditor',
                                default => 'opd'
                            };

                            $catBadge = match($catGroup) {
                                'opd'      => 'bg-primary-100 text-primary-700',
                                'pokja'    => 'bg-purple-100 text-purple-700',
                                'penyedia' => 'bg-info-100 text-info-700',
                                'auditor'  => 'bg-warning-100 text-warning-800',
                                default    => 'bg-neutral-100 text-neutral-700'
                            };
                            $catLabel = match($catGroup) {
                                'opd'      => 'PPK / OPD',
                                'pokja'    => 'Pokja UKPBJ',
                                'penyedia' => 'Penyedia UMKM',
                                'auditor'  => 'Auditor APIP',
                                default    => ucfirst($rawCat)
                            };
                            $statusClass = $t['is_active'] ? 'active-row' : 'inactive-row';
                            ?>
                            <tr class="testimonial-row <?= $statusClass ?>" 
                                data-id="<?= $t['id'] ?>" 
                                data-status="<?= $t['is_active'] ? 'active' : 'inactive' ?>" 
                                data-category="<?= esc($catGroup) ?>">
                                
                                <!-- Urutan -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <input type="number" 
                                               class="form-control form-control-sm text-center fw-bold order-input px-1" 
                                               style="width: 50px;" 
                                               value="<?= esc($t['sort_order']) ?>" 
                                               min="1" 
                                               max="99" 
                                               data-id="<?= $t['id'] ?>" 
                                               title="Ubah urutan lalu tekan Enter">
                                        <span class="order-saved-check d-none text-success-main" title="Tersimpan!">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                        </span>
                                    </div>
                                </td>

                                <!-- Avatar -->
                                <td class="text-center">
                                    <div class="w-44-px h-44-px rounded-circle overflow-hidden border border-2 border-white shadow-xs bg-neutral-100 mx-auto">
                                        <img src="<?= esc($avatarSrc) ?>" 
                                             alt="<?= esc($t['name']) ?>" 
                                             class="w-100 h-100 object-fit-cover testimonial-avatar-img"
                                             loading="lazy"
                                             onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                    </div>
                                </td>

                                <!-- Responden & Instansi -->
                                <td>
                                    <strong class="text-sm text-primary-light d-block mb-1 testimonial-name"><?= esc($t['name']) ?></strong>
                                    <small class="text-primary-600 d-block fw-semibold text-2xs mb-1"><?= esc($t['role_title']) ?></small>
                                    <span class="text-secondary-light text-2xs d-block"><?= esc($t['organization']) ?></span>
                                </td>

                                <!-- Kutipan Ulasan & Rating -->
                                <td>
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        <div class="d-flex align-items-center text-warning-main text-xs">
                                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                                <iconify-icon icon="solar:star-bold" class="<?= $star <= $rating ? 'text-warning-main' : 'text-neutral-300' ?>"></iconify-icon>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="badge bg-warning-50 text-warning-800 text-3xs px-6 py-2 radius-4 fw-bold">
                                            <?= number_format($rating, 1) ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-secondary-light mb-0 fst-italic line-clamp-2" style="max-width: 320px;" title="<?= esc($t['quote']) ?>">
                                        &ldquo;<?= esc($t['quote']) ?>&rdquo;
                                    </p>
                                </td>

                                <!-- Dampak Nyata Terukur -->
                                <td>
                                    <?php if (! empty($t['impact_statement'])): ?>
                                        <div class="p-8 bg-success-50 text-success-700 radius-8 border border-success-200 text-xs fw-semibold d-inline-flex align-items-center gap-1" style="max-width: 250px;">
                                            <iconify-icon icon="solar:graph-up-bold" class="text-success-main text-sm flex-shrink-0"></iconify-icon>
                                            <span class="text-truncate"><?= esc($t['impact_statement']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-2xs fst-italic">- Belum dicantumkan -</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Kategori -->
                                <td>
                                    <span class="badge <?= $catBadge ?> text-3xs text-uppercase px-8 py-3 radius-4 fw-bold">
                                        <?= esc($catLabel) ?>
                                    </span>
                                </td>

                                <!-- Status Tayang -->
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 <?= $t['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-neutral-600' ?>"
                                            data-id="<?= $t['id'] ?>"
                                            data-active="<?= $t['is_active'] ? '1' : '0' ?>"
                                            title="Klik untuk ubah status tayang">
                                        <span class="status-label"><?= $t['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                    </button>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Duplikat -->
                                        <a href="<?= site_url('dashboard/landing/testimoni/duplikat/' . $t['id']) ?>" 
                                           class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-info-main hover-text-white transition-2" 
                                           title="Duplikat sebagai Draf">
                                            <iconify-icon icon="solar:copy-linear" class="text-base"></iconify-icon>
                                        </a>

                                        <!-- Ubah -->
                                        <a href="<?= site_url('dashboard/landing/testimoni/ubah/' . $t['id']) ?>" 
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2" 
                                           title="Ubah Testimoni">
                                            <iconify-icon icon="solar:pen-new-square-linear" class="text-base"></iconify-icon>
                                        </a>

                                        <!-- Hapus -->
                                        <button type="button" 
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white transition-2 border-0 btn-delete-testimonial"
                                                data-id="<?= $t['id'] ?>" 
                                                data-name="<?= esc($t['name']) ?>" 
                                                title="Hapus Testimoni">
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

<!-- =========================================================
     MODAL PRATINJAU PUBLIK CAROUSEL TESTIMONI
     ========================================================= -->
<div class="modal fade" id="testimonialPreviewModal" tabindex="-1" aria-labelledby="testimonialPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 radius-16 overflow-hidden">
            <div class="modal-header bg-primary-600 text-white p-20">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:chat-round-dots-bold" class="text-2xl text-warning-main"></iconify-icon>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="testimonialPreviewModalLabel">Simulator Carousel Testimoni Pengguna</h6>
                        <small class="text-white-50 text-2xs">Pratinjau kartu gelembung obrolan (*speech bubble*) sebagaimana tampil di beranda publik</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 bg-neutral-50">
                <div class="p-24 radius-12 bg-white border">
                    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-24 border-bottom pb-16">
                        <div>
                            <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-2xs fw-semibold mb-2 d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                                Testimoni &amp; Dampak Nyata
                            </span>
                            <h5 class="fw-bold text-primary-light mb-1">Pengalaman Pengguna &amp; Mitra BegawiPengadaan</h5>
                            <p class="text-secondary-light text-xs mb-0">Ulasan autentik dari pimpinan daerah, pejabat pembuat komitmen, kelompok kerja pemilihan, auditor pengawas, dan pelaku usaha mitra pengadaan.</p>
                        </div>
                    </div>

                    <!-- Simulator Carousel Grid -->
                    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-1 g-24" id="previewTestimonialGrid">
                        <?php 
                        $activeTestimonials = array_filter($testimonials, fn($t) => (int) ($t['is_active'] ?? 0) === 1);
                        ?>
                        <?php if (empty($activeTestimonials)): ?>
                            <div class="col-12 py-32 text-center text-secondary-light">
                                <iconify-icon icon="solar:info-circle-linear" class="text-2xl mb-1"></iconify-icon>
                                <p class="text-xs mb-0">Tidak ada testimoni yang aktif untuk ditampilkan pada pratinjau publik.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($activeTestimonials as $t): ?>
                                <?php
                                $avatar = $t['avatar_url'] ?: 'assets/main/images/testimonial/testimonial-1-1.jpg';
                                $avatarSrc = str_starts_with($avatar, 'http') ? $avatar : base_url($avatar);
                                $rating = (int) ($t['rating'] ?: 5);
                                ?>
                                <div class="col">
                                    <div class="sim-testimony-card h-100 d-flex flex-column">
                                        <!-- Speech Bubble -->
                                        <div class="sim-testimony-bubble radius-16 p-20 bg-white border shadow-xs position-relative mb-20 flex-grow-1 d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between mb-12">
                                                <div class="d-flex align-items-center gap-1 text-warning-main text-xs">
                                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                                        <iconify-icon icon="solar:star-bold" class="<?= $s <= $rating ? 'text-warning-main' : 'text-neutral-300' ?>"></iconify-icon>
                                                    <?php endfor; ?>
                                                    <span class="text-xs fw-bold text-dark ms-1"><?= number_format($rating, 1) ?></span>
                                                </div>
                                                <iconify-icon icon="solar:chat-round-dots-bold" class="text-primary-300 text-xl"></iconify-icon>
                                            </div>
                                            <p class="text-xs text-secondary mb-16 flex-grow-1" style="line-height: 1.6;">
                                                &ldquo;<?= esc($t['quote']) ?>&rdquo;
                                            </p>
                                            <?php if (! empty($t['impact_statement'])): ?>
                                                <div class="p-8 bg-success-50 text-success-700 radius-8 border border-success-200 text-3xs fw-semibold d-inline-flex align-items-center gap-1 mt-auto">
                                                    <iconify-icon icon="solar:graph-up-bold" class="text-success-main"></iconify-icon>
                                                    <span><?= esc($t['impact_statement']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <!-- Speech Bubble Tail -->
                                            <div class="sim-bubble-tail"></div>
                                        </div>

                                        <!-- Author Bar -->
                                        <div class="d-flex align-items-center gap-12 ps-12">
                                            <div class="w-44-px h-44-px rounded-circle overflow-hidden border border-2 border-white shadow-xs bg-neutral-100 flex-shrink-0">
                                                <img src="<?= esc($avatarSrc) ?>" alt="<?= esc($t['name']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-primary-light mb-0 text-xs text-truncate"><?= esc($t['name']) ?></h6>
                                                <span class="text-secondary-light text-3xs d-block text-truncate"><?= esc($t['organization']) ?></span>
                                                <span class="badge bg-primary-50 text-primary-700 text-3xs radius-999 px-8 py-2 mt-1 fw-bold d-inline-block">
                                                    <?= esc($t['role_title']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-16">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs" data-bs-dismiss="modal">Tutup</button>
                <a href="<?= site_url('/#section-testimonies') ?>" target="_blank" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:external-link-bold" class="text-sm"></iconify-icon>
                    <span>Kunjungi Beranda Asli</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.sim-testimony-bubble {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.sim-testimony-card:hover .sim-testimony-bubble {
    border-color: #0c5aa6 !important;
    box-shadow: 0 14px 35px rgba(12, 90, 166, 0.12) !important;
    transform: translateY(-3px);
}
.sim-bubble-tail {
    position: absolute;
    bottom: -8px;
    left: 28px;
    width: 15px;
    height: 15px;
    background: #ffffff;
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    transform: rotate(45deg);
}
.sim-testimony-card:hover .sim-bubble-tail {
    border-color: #0c5aa6 !important;
}
.testimonial-avatar-img {
    transition: transform 0.25s ease;
}
.testimonial-row:hover .testimonial-avatar-img {
    transform: scale(1.1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfHash      = '<?= csrf_hash() ?>';

    // 1. Filter Pills
    const filterBtns = document.querySelectorAll('.filter-btn');
    const rows       = document.querySelectorAll('.testimonial-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active', 'btn-primary-600', 'text-white'));
            this.classList.add('active', 'btn-primary-600', 'text-white');
            this.classList.remove('btn-outline-neutral-300');

            applyTestimonialFilters();
        });
    });

    // 2. Search Box with Clear Button
    const searchInput = document.getElementById('testimonialSearchInput');
    const clearBtn    = document.getElementById('clearTestimonialSearchBtn');

    if (searchInput && clearBtn) {
        searchInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            applyTestimonialFilters();
        });

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            applyTestimonialFilters();
            searchInput.focus();
        });
    }

    function applyTestimonialFilters() {
        const activeBtn = document.querySelector('.filter-btn.active');
        const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        const query  = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            const status   = row.getAttribute('data-status');
            const category = row.getAttribute('data-category');
            const text     = row.innerText.toLowerCase();

            let matchesFilter = true;
            if (filter === 'opd') matchesFilter = (category === 'opd');
            else if (filter === 'pokja') matchesFilter = (category === 'pokja');
            else if (filter === 'penyedia') matchesFilter = (category === 'penyedia');
            else if (filter === 'auditor') matchesFilter = (category === 'auditor');
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

            fetch('<?= site_url('dashboard/landing/testimoni/update-order') ?>', {
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

            fetch('<?= site_url('dashboard/landing/testimoni/toggle/') ?>' + id, {
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
        const allRows = document.querySelectorAll('.testimonial-row');
        let activeCount = 0;
        let inactiveCount = 0;

        allRows.forEach(r => {
            if (r.getAttribute('data-status') === 'active') activeCount++;
            else inactiveCount++;
        });

        const countActiveEl = document.getElementById('countActive');
        const countInactiveEl = document.getElementById('countInactive');
        if (countActiveEl) countActiveEl.textContent = activeCount;
        if (countInactiveEl) countInactiveEl.textContent = inactiveCount;
    }

    // 5. SweetAlert2 Delete Confirmation
    const deleteBtns = document.querySelectorAll('.btn-delete-testimonial');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const row = this.closest('tr');

            const proceedDelete = () => {
                fetch('<?= site_url('dashboard/landing/testimoni/hapus/') ?>' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        if (row) {
                            row.remove();
                            updateAllCounts();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal!', data.message || 'Tidak dapat menghapus data.', 'error');
                        }
                    }
                })
                .catch(err => {
                    window.location.href = '<?= site_url('dashboard/landing/testimoni/hapus/') ?>' + id;
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Testimoni?',
                    text: `Ulasan dari "${name}" akan dihapus secara permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedDelete();
                    }
                });
            } else {
                if (confirm(`Hapus testimoni dari "${name}"?`)) {
                    proceedDelete();
                }
            }
        });
    });

    function updateAllCounts() {
        const remainingRows = document.querySelectorAll('.testimonial-row');
        const total = remainingRows.length;
        let active = 0, inactive = 0, opd = 0, pokja = 0, penyedia = 0, auditor = 0;

        remainingRows.forEach(r => {
            const status = r.getAttribute('data-status');
            const cat = r.getAttribute('data-category');
            if (status === 'active') active++; else inactive++;
            if (cat === 'opd') opd++;
            else if (cat === 'pokja') pokja++;
            else if (cat === 'penyedia') penyedia++;
            else if (cat === 'auditor') auditor++;
        });

        const elAll = document.getElementById('countAll');
        const elOpd = document.getElementById('countOpd');
        const elPokja = document.getElementById('countPokja');
        const elPenyedia = document.getElementById('countPenyedia');
        const elAuditor = document.getElementById('countAuditor');
        const elActive = document.getElementById('countActive');
        const elInactive = document.getElementById('countInactive');

        if (elAll) elAll.textContent = total;
        if (elOpd) elOpd.textContent = opd;
        if (elPokja) elPokja.textContent = pokja;
        if (elPenyedia) elPenyedia.textContent = penyedia;
        if (elAuditor) elAuditor.textContent = auditor;
        if (elActive) elActive.textContent = active;
        if (elInactive) elInactive.textContent = inactive;
    }
});
</script>
<?= $this->endSection() ?>
