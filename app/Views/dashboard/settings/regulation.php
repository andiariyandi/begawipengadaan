<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/settings/_nav_settings', ['activeSetting' => 'regulation']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Regulasi') ?></h6>
        <p class="text-secondary-light mb-0">Atur parameter repositori hukum, hierarki peraturan PBJ, dan slider sorotan publik.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sistem/pengaturan/regulation') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-lg"></iconify-icon>
                        <span>Konfigurasi Slider Sorotan Regulasi (Landing Page)</span>
                    </h6>
                    <p class="text-secondary-light text-xs mb-16">
                        Tentukan mode tampilan dan kuota regulasi yang diutamakan pada banner hero carousel di landing page <code>/regulasi</code>. Pengelola dapat menampilkan semua kategori atau mengkhususkan kategori peraturan tertentu.
                    </p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_mode">Mode Tampilan Sorotan Default</label>
                    <?php $spMode = $settings['spotlight_mode'] ?? 'all'; ?>
                    <select id="spotlight_mode" name="spotlight_mode" class="form-select radius-8">
                        <option value="all" <?= $spMode === 'all' ? 'selected' : '' ?>>Semua Kategori (Memprioritaskan Sorotan Hero &amp; Paling Banyak Diunduh)</option>
                        <option value="featured" <?= $spMode === 'featured' ? 'selected' : '' ?>>Khusus Regulasi Unggulan / Sorotan Utama Terpilih</option>
                        <optgroup label="Berdasarkan Kategori Regulasi Spesifik:">
                            <?php if (! empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['slug']) ?>" <?= $spMode === $cat['slug'] ? 'selected' : '' ?>>
                                        Khusus <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="peraturan-presiden" <?= $spMode === 'peraturan-presiden' ? 'selected' : '' ?>>Khusus Peraturan Presiden (Perpres)</option>
                                <option value="peraturan-lkpp" <?= $spMode === 'peraturan-lkpp' ? 'selected' : '' ?>>Khusus Peraturan LKPP</option>
                                <option value="peraturan-daerah-gubernur" <?= $spMode === 'peraturan-daerah-gubernur' ? 'selected' : '' ?>>Khusus Peraturan Daerah &amp; Pergub Lampung</option>
                                <option value="undang-undang" <?= $spMode === 'undang-undang' ? 'selected' : '' ?>>Khusus Undang-Undang (UU)</option>
                            <?php endif; ?>
                        </optgroup>
                    </select>
                    <small class="text-secondary-light text-xs">Pilihan ini menentukan dokumen hukum yang dimuat ke dalam headline carousel regulasi.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_limit">Batas Maksimal Item Sorotan</label>
                    <input id="spotlight_limit" type="number" min="3" max="15" name="spotlight_limit" class="form-control radius-8" value="<?= esc($settings['spotlight_limit'] ?? '6') ?>" required>
                    <small class="text-secondary-light text-xs">Jumlah slide maksimal yang ditampilkan dalam headline carousel (3 - 15 slide).</small>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-16">
                    <a href="<?= site_url('dashboard/sistem/pengaturan-regulasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
