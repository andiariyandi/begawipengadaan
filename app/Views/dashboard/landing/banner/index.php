<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Banner Hero & Closing CTA Halaman') ?></h6>
        <p class="text-secondary-light mb-0">Kelola judul hero utama, subjudul, serta banner penutup (Call-to-Action) di 10 halaman portal publik.</p>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'promo', 'activeSub' => 'banner-halaman']) ?>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="banner-halaman-publik" data-page-length="10">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Halaman Publik</th>
                        <th>Hero Title & Subtitle</th>
                        <th>Closing CTA Banner</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary-light">Belum ada banner halaman terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($banners as $b): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-primary-100 text-primary-600 radius-4 text-xs mb-1 font-monospace">/<?= esc($b['page_key']) ?></span>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($pageNames[$b['page_key']] ?? $b['page_key']) ?></strong>
                                    <small class="text-secondary-light text-2xs">Badge: <?= esc($b['hero_badge']) ?></small>
                                </td>
                                <td>
                                    <h6 class="text-xs fw-semibold mb-1 text-primary-light"><?= strip_tags($b['hero_title']) ?></h6>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 380px;"><?= esc($b['hero_subtitle']) ?></p>
                                </td>
                                <td>
                                    <strong class="text-xs text-primary-light d-block"><?= esc($b['cta_title']) ?></strong>
                                    <small class="text-primary-600 text-2xs d-block">Tombol: <?= esc($b['cta_primary_btn_text']) ?> &rarr; <?= esc($b['cta_primary_btn_url']) ?></small>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/landing/banner-halaman/ubah/' . $b['page_key']) ?>" class="btn btn-primary-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1" title="Sesuaikan Banner">
                                        <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        <span>Kelola</span>
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
<?= $this->endSection() ?>
