<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
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

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="slider-hero" data-page-length="10">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 140px;">Pratinjau Gambar</th>
                        <th>Judul & Subjudul</th>
                        <th>Tombol Aksi (CTA)</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center no-sort no-export action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sliders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary-light">Belum ada slide hero. Klik "Tambah Slide" untuk membuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sliders as $s): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($s['sort_order']) ?></td>
                                <td>
                                    <?php if (! empty($s['image_url'])): ?>
                                        <img src="<?= base_url($s['image_url']) ?>" alt="Slide" class="radius-8 object-fit-cover border" style="width: 120px; height: 60px;" onerror="this.src='<?= base_url('assets/main/images/backgrounds/slider-1-1.png') ?>'">
                                    <?php else: ?>
                                        <div class="bg-neutral-100 text-neutral-400 d-flex align-items-center justify-content-center radius-8" style="width: 120px; height: 60px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($s['badge_text'])): ?>
                                        <span class="badge bg-primary-100 text-primary-600 radius-4 text-2xs mb-1"><?= esc($s['badge_text']) ?></span>
                                    <?php endif; ?>
                                    <h6 class="text-sm fw-semibold mb-1 text-primary-light"><?= strip_tags($s['title']) ?></h6>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 420px;"><?= esc($s['subtitle']) ?></p>
                                </td>
                                <td>
                                    <?php if (! empty($s['button_text'])): ?>
                                        <div class="text-xs mb-1">
                                            <span class="fw-semibold text-primary-600">CTA 1:</span> <?= esc($s['button_text']) ?> &rarr; <code class="text-2xs"><?= esc($s['button_url']) ?></code>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (! empty($s['secondary_button_text'])): ?>
                                        <div class="text-xs">
                                            <span class="fw-semibold text-secondary-light">CTA 2:</span> <?= esc($s['secondary_button_text']) ?> &rarr; <code class="text-2xs"><?= esc($s['secondary_button_url']) ?></code>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/slider/toggle/' . $s['id']) ?>" class="badge <?= $s['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/slider/ubah/' . $s['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Slide">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/slider/hapus/' . $s['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus slide ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Slide">
                                            <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                        </a>
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

<!-- Modal Live Simulator Pratinjau Hero -->
<div class="modal fade" id="heroLivePreviewModal" tabindex="-1" aria-labelledby="heroLivePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow bg-base">
            <div class="modal-header border-bottom py-16 px-24 bg-base">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:laptop-minimalistic-outline" class="text-primary-600 text-2xl"></iconify-icon>
                    <div>
                        <h6 class="modal-title fs-6 fw-bold mb-0" id="heroLivePreviewModalLabel">Simulasi Responsif Slider Hero</h6>
                        <span class="text-xs text-secondary-light">Pratinjau tampilan slider beranda pada berbagai resolusi perangkat</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <ul class="nav nav-pills justify-content-center gap-2 mb-20" id="previewDeviceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active radius-8 px-16 py-8 text-xs fw-semibold" id="desktop-tab" data-bs-toggle="pill" data-bs-target="#desktop-view" type="button" role="tab">
                            <iconify-icon icon="solar:laptop-outline" class="me-1"></iconify-icon> Desktop (16:9)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link radius-8 px-16 py-8 text-xs fw-semibold" id="mobile-tab" data-bs-toggle="pill" data-bs-target="#mobile-view" type="button" role="tab">
                            <iconify-icon icon="solar:smartphone-outline" class="me-1"></iconify-icon> Ponsel Mobile (9:16)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="previewDeviceTabContent">
                    <!-- Desktop View -->
                    <div class="tab-pane fade show active" id="desktop-view" role="tabpanel">
                        <div class="border radius-12 overflow-hidden shadow-sm bg-neutral-900 text-white position-relative" style="min-height: 380px;">
                            <?php $activeSlider = ! empty($sliders) ? $sliders[0] : null; ?>
                            <?php if ($activeSlider): ?>
                                <img src="<?= ! empty($activeSlider['image_url']) ? base_url($activeSlider['image_url']) : base_url('assets/main/images/backgrounds/slider-1-1.png') ?>" alt="Preview" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 opacity-40">
                                <div class="position-relative p-40 d-flex flex-column justify-content-center" style="min-height: 380px; z-index: 2;">
                                    <?php if (! empty($activeSlider['badge_text'])): ?>
                                        <span class="badge bg-primary text-white radius-4 px-12 py-6 text-xs d-inline-block mb-16 align-self-start fw-bold">
                                            <?= esc($activeSlider['badge_text']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <h2 class="text-3xl fw-bold text-white mb-12 max-w-700-px"><?= strip_tags($activeSlider['title']) ?></h2>
                                    <p class="text-neutral-200 text-sm mb-24 max-w-600-px"><?= esc($activeSlider['subtitle']) ?></p>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (! empty($activeSlider['button_text'])): ?>
                                            <a href="javascript:void(0)" class="btn btn-primary radius-8 px-20 py-10 fw-semibold text-sm"><?= esc($activeSlider['button_text']) ?></a>
                                        <?php endif; ?>
                                        <?php if (! empty($activeSlider['secondary_button_text'])): ?>
                                            <a href="javascript:void(0)" class="btn btn-outline-light radius-8 px-20 py-10 fw-semibold text-sm"><?= esc($activeSlider['secondary_button_text']) ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="p-40 text-center text-secondary-light">Belum ada slide aktif untuk dipratinjau.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Mobile View -->
                    <div class="tab-pane fade" id="mobile-view" role="tabpanel">
                        <div class="d-flex justify-content-center">
                            <div class="border border-3 border-neutral-700 radius-24 overflow-hidden shadow bg-neutral-900 text-white position-relative" style="width: 320px; min-height: 520px;">
                                <div class="w-100 bg-neutral-800 text-center py-6 text-2xs text-secondary-light border-bottom border-neutral-700">
                                    <iconify-icon icon="solar:camera-outline" class="me-1"></iconify-icon> Layar Ponsel
                                </div>
                                <?php if ($activeSlider): ?>
                                    <img src="<?= ! empty($activeSlider['image_url']) ? base_url($activeSlider['image_url']) : base_url('assets/main/images/backgrounds/slider-1-1.png') ?>" alt="Preview" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 opacity-30">
                                    <div class="position-relative p-20 d-flex flex-column justify-content-center text-center" style="min-height: 480px; z-index: 2;">
                                        <?php if (! empty($activeSlider['badge_text'])): ?>
                                            <span class="badge bg-primary text-white radius-4 px-10 py-4 text-2xs d-inline-block mb-12 align-self-center fw-bold">
                                                <?= esc($activeSlider['badge_text']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <h4 class="text-xl fw-bold text-white mb-8"><?= strip_tags($activeSlider['title']) ?></h4>
                                        <p class="text-neutral-200 text-xs mb-20"><?= esc($activeSlider['subtitle']) ?></p>
                                        <div class="d-flex flex-column gap-2">
                                            <?php if (! empty($activeSlider['button_text'])): ?>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm radius-8 py-8 fw-semibold text-xs"><?= esc($activeSlider['button_text']) ?></a>
                                            <?php endif; ?>
                                            <?php if (! empty($activeSlider['secondary_button_text'])): ?>
                                                <a href="javascript:void(0)" class="btn btn-outline-light btn-sm radius-8 py-8 fw-semibold text-xs"><?= esc($activeSlider['secondary_button_text']) ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-12 px-24 bg-base justify-content-between">
                <span class="text-xs text-secondary-light">Simulasi menggunakan data slide urutan teratas aktif.</span>
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs" data-bs-dismiss="modal">Tutup Simulasi</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
