<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Keamanan Sistem') ?></h6>
        <p class="text-secondary-light mb-0">Kebijakan akses, kata sandi, dan pengelolaan sesi pengguna.</p>
    </div>
    <a href="<?= site_url('dashboard/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="row gy-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body p-24">
                <form method="post" action="<?= site_url('dashboard/pengaturan/security') ?>">
                    <?= csrf_field() ?>
                    <h6 class="text-lg mb-20">Kebijakan Kata Sandi</h6>
                    <div class="row gy-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang Minimum</label>
                            <div class="input-group">
                                <input type="number" name="min_password_length" class="form-control radius-8" value="<?= esc($settings['min_password_length'] ?? '8') ?>" required>
                                <span class="input-group-text">Karakter</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Waktu Sesi Aktif</label>
                            <div class="input-group">
                                <input type="number" name="session_lifetime_minutes" class="form-control radius-8" value="<?= esc($settings['session_lifetime_minutes'] ?? '120') ?>" required>
                                <span class="input-group-text">Menit</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-switch switch-primary d-flex align-items-center gap-12">
                                <input type="hidden" name="require_numbers" value="0">
                                <input class="form-check-input" type="checkbox" name="require_numbers" value="1" id="requireNumbers" <?= ($settings['require_numbers'] ?? '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="requireNumbers">Wajibkan minimal satu digit angka pada kata sandi</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-switch switch-primary d-flex align-items-center gap-12">
                                <input type="hidden" name="require_special_chars" value="0">
                                <input class="form-check-input" type="checkbox" name="require_special_chars" value="1" id="requireSpecial" <?= ($settings['require_special_chars'] ?? '0') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="requireSpecial">Wajibkan karakter khusus / simbol pada kata sandi</label>
                            </div>
                        </div>
                        <hr class="my-8">
                        <h6 class="text-lg mb-4">Manajemen Percobaan Login</h6>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Maksimum Percobaan Gagal</label>
                            <input type="number" name="max_login_attempts" class="form-control radius-8" value="<?= esc($settings['max_login_attempts'] ?? '5') ?>" required>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-12">
                            <a href="<?= site_url('dashboard/pengaturan/security') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kebijakan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Status Keamanan</h6>
                <div class="d-flex flex-column gap-20">
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light">HTTPS Session Enforced</span>
                        <span class="badge bg-success-focus text-success-main px-12 py-4 radius-4">Aktif</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light">CSRF Protection</span>
                        <span class="badge bg-success-focus text-success-main px-12 py-4 radius-4">Aktif</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light">Password Policy</span>
                        <span class="badge bg-success-focus text-success-main px-12 py-4 radius-4">Terkonfigurasi</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary-light">Rate Limiter / Brute-force Guard</span>
                        <span class="badge bg-success-focus text-success-main px-12 py-4 radius-4">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
