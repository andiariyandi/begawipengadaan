<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelola Slider Hero Beranda') ?></h6>
        <p class="text-secondary-light mb-0">Manajemen tayangan slide hero banner, judul, tombol aksi, dan urutan rotasi pada homepage.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/slider/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Slide</span>
    </a>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 140px;">Pratinjau Gambar</th>
                        <th>Judul & Subjudul</th>
                        <th>Tombol Aksi (CTA)</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
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
<?= $this->endSection() ?>
