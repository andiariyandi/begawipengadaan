<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/settings/_nav_settings', ['activeSetting' => 'news']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Berita & Artikel') ?></h6>
        <p class="text-secondary-light mb-0">Atur parameter publikasi berita, artikel pengadaan, dan slider sorotan publik.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sistem/pengaturan/news') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-lg"></iconify-icon>
                        <span>Konfigurasi Slider Sorotan Berita (Landing Page)</span>
                    </h6>
                    <p class="text-secondary-light text-xs mb-16">
                        Tentukan mode kurasi dan batas artikel berita yang ditampilkan pada headline carousel di landing page <code>/berita</code>. Pengelola dapat menampilkan semua kategori atau mengkhususkan kategori berita tertentu.
                    </p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_mode">Mode Tampilan Sorotan Default</label>
                    <?php $spMode = $settings['spotlight_mode'] ?? 'all'; ?>
                    <select id="spotlight_mode" name="spotlight_mode" class="form-select radius-8">
                        <option value="all" <?= $spMode === 'all' ? 'selected' : '' ?>>Semua Berita (Memprioritaskan Sorotan Hero &amp; Terpopuler)</option>
                        <option value="featured" <?= $spMode === 'featured' ? 'selected' : '' ?>>Khusus Berita Utama / Sorotan Terpilih (is_featured = 1)</option>
                        <optgroup label="Berdasarkan Kategori Berita Spesifik:">
                            <?php if (! empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['slug']) ?>" <?= $spMode === $cat['slug'] ? 'selected' : '' ?>>
                                        Khusus <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="pengumuman" <?= $spMode === 'pengumuman' ? 'selected' : '' ?>>Khusus Pengumuman</option>
                                <option value="kebijakan" <?= $spMode === 'kebijakan' ? 'selected' : '' ?>>Khusus Kebijakan</option>
                                <option value="opini" <?= $spMode === 'opini' ? 'selected' : '' ?>>Khusus Opini &amp; Wawasan</option>
                            <?php endif; ?>
                        </optgroup>
                    </select>
                    <small class="text-secondary-light text-xs">Pilihan ini menentukan artikel yang dimuat ke dalam headline carousel landing page Berita.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_limit">Batas Maksimal Item Sorotan</label>
                    <input id="spotlight_limit" type="number" min="3" max="15" name="spotlight_limit" class="form-control radius-8" value="<?= esc($settings['spotlight_limit'] ?? '5') ?>" required>
                    <small class="text-secondary-light text-xs">Jumlah slide maksimal yang ditampilkan dalam headline carousel (3 - 15 slide).</small>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-16">
                    <a href="<?= site_url('dashboard/sistem/pengaturan-berita') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
