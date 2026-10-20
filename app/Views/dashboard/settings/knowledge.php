<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Bunga Rampai') ?></h6>
        <p class="text-secondary-light mb-0">Atur parameter etalase pengetahuan, repositori dokumen, dan slider sorotan publik.</p>
    </div>
    <a href="<?= site_url('dashboard/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/pengaturan/knowledge') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-lg"></iconify-icon>
                        <span>Konfigurasi Slider Sorotan Bunga Rampai (Landing Page)</span>
                    </h6>
                    <p class="text-secondary-light text-xs mb-16">
                        Tentukan jenis konten yang diutamakan pada slider sorotan hero di landing page <code>/bunga-rampai</code>. Pengelola dapat memilih menampilkan konten gabungan terpadu atau fokus pada fitur spesifik.
                    </p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_mode">Mode Tampilan Sorotan Default</label>
                    <?php $spMode = $settings['spotlight_mode'] ?? 'all'; ?>
                    <select id="spotlight_mode" name="spotlight_mode" class="form-select radius-8">
                        <option value="all" <?= $spMode === 'all' ? 'selected' : '' ?>>Kombinasi Semua Fitur (Artikel, Studi Kasus, Template, dan Multimedia)</option>
                        <option value="article" <?= $spMode === 'article' ? 'selected' : '' ?>>Khusus Artikel PBJ</option>
                        <option value="case_study" <?= $spMode === 'case_study' ? 'selected' : '' ?>>Khusus Studi Kasus Lapangan</option>
                        <option value="template" <?= $spMode === 'template' ? 'selected' : '' ?>>Khusus Template Dokumen</option>
                        <option value="multimedia" <?= $spMode === 'multimedia' ? 'selected' : '' ?>>Khusus Multimedia &amp; Infografik PBJ</option>
                    </select>
                    <small class="text-secondary-light text-xs">Pilihan ini menentukan kategori konten yang dimuat ke dalam headline carousel Bunga Rampai.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_limit">Batas Maksimal Item Sorotan</label>
                    <input id="spotlight_limit" type="number" min="3" max="15" name="spotlight_limit" class="form-control radius-8" value="<?= esc($settings['spotlight_limit'] ?? '6') ?>" required>
                    <small class="text-secondary-light text-xs">Jumlah slide maksimal yang ditampilkan dalam headline carousel (3 - 15 slide).</small>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-16">
                    <a href="<?= site_url('dashboard/pengaturan/knowledge') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
