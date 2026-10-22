<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Umum') ?></h6>
        <p class="text-secondary-light mb-0">Konfigurasi dasar identitas dan preferensi aplikasi.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sistem/pengaturan/general') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Aplikasi</label>
                    <input type="text" name="app_name" class="form-control radius-8" value="<?= esc($settings['app_name'] ?? 'BegawiPengadaan') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Singkat</label>
                    <input type="text" name="app_short_name" class="form-control radius-8" value="<?= esc($settings['app_short_name'] ?? 'BegawiPengadaan') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bahasa Default</label>
                    <select name="default_locale" class="form-select radius-8">
                        <option value="id" <?= ($settings['default_locale'] ?? '') === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                        <option value="en" <?= ($settings['default_locale'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Zona Waktu</label>
                    <select name="timezone" class="form-select radius-8">
                        <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar" <?= ($settings['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Format Tanggal</label>
                    <select name="date_format" class="form-select radius-8">
                        <option value="d F Y" <?= ($settings['date_format'] ?? '') === 'd F Y' ? 'selected' : '' ?>>DD MMMM YYYY (contoh: 17 Agustus 2026)</option>
                        <option value="d/m/Y" <?= ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                        <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Pengirim Sistem</label>
                    <input type="email" name="sender_email" class="form-control radius-8" value="<?= esc($settings['sender_email'] ?? 'noreply@begawipbj.lampungprov.go.id') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Aplikasi</label>
                    <textarea name="app_description" class="form-control radius-8" rows="4"><?= esc($settings['app_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/sistem/pengaturan-umum') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>