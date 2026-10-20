<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ubah Kata Sandi') ?></h6>
        <p class="text-secondary-light mb-0">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan akses dashboard.</p>
    </div>
    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Kembali ke Profil</span>
    </a>
</div>

<!-- Navigation Tabs -->
<div class="card mb-24">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile') ?>">
                    <iconify-icon icon="solar:user-bold" class="text-lg"></iconify-icon>
                    <span>Ringkasan Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/edit') ?>">
                    <iconify-icon icon="solar:pen-new-square-bold" class="text-lg"></iconify-icon>
                    <span>Ubah Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/password') ?>">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg"></iconify-icon>
                    <span>Ubah Kata Sandi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/preferences') ?>">
                    <iconify-icon icon="solar:settings-bold" class="text-lg"></iconify-icon>
                    <span>Preferensi Akun</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Form Card -->
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card">
            <div class="card-body p-24">
                <div class="mb-24 pb-16 border-bottom">
                    <h6 class="text-lg fw-bold mb-1">Keamanan Kredensial</h6>
                    <p class="text-secondary-light text-xs mb-0">Pastikan Anda menggunakan kata sandi unik yang kuat dan tidak digunakan pada aplikasi lain.</p>
                </div>

                <form action="<?= site_url('dashboard/profile/password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="d-flex flex-column gap-20">
                        <!-- Kata Sandi Saat Ini -->
                        <div>
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="current_password">
                                Kata Sandi Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control radius-8 pe-48 <?= session('errors.current_password') ? 'is-invalid' : '' ?>"
                                    placeholder="Masukkan kata sandi saat ini"
                                    required>
                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-12 text-secondary-light p-0 border-0 bg-transparent btn-toggle-pwd"
                                    data-target="current_password"
                                    aria-label="Tampilkan kata sandi">
                                    <iconify-icon icon="solar:eye-linear" class="text-lg"></iconify-icon>
                                </button>
                            </div>
                            <?php if (session('errors.current_password')): ?>
                                <div class="text-danger text-2xs mt-1"><?= session('errors.current_password') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Kata Sandi Baru -->
                        <div>
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="password">
                                Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password"
                                    id="password"
                                    name="password"
                                    class="form-control radius-8 pe-48 <?= session('errors.password') ? 'is-invalid' : '' ?>"
                                    placeholder="Minimal 8 karakter"
                                    required>
                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-12 text-secondary-light p-0 border-0 bg-transparent btn-toggle-pwd"
                                    data-target="password"
                                    aria-label="Tampilkan kata sandi">
                                    <iconify-icon icon="solar:eye-linear" class="text-lg"></iconify-icon>
                                </button>
                            </div>
                            <?php if (session('errors.password')): ?>
                                <div class="text-danger text-2xs mt-1"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Konfirmasi Kata Sandi Baru -->
                        <div>
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="password_confirmation">
                                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control radius-8 pe-48 <?= session('errors.password_confirmation') ? 'is-invalid' : '' ?>"
                                    placeholder="Ketik ulang kata sandi baru"
                                    required>
                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-12 text-secondary-light p-0 border-0 bg-transparent btn-toggle-pwd"
                                    data-target="password_confirmation"
                                    aria-label="Tampilkan kata sandi">
                                    <iconify-icon icon="solar:eye-linear" class="text-lg"></iconify-icon>
                                </button>
                            </div>
                            <?php if (session('errors.password_confirmation')): ?>
                                <div class="text-danger text-2xs mt-1"><?= session('errors.password_confirmation') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Syarat Kata Sandi Box -->
                        <div class="border border-neutral-200 bg-neutral-50 radius-8 p-16">
                            <h6 class="text-sm fw-bold mb-8 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:shield-check-bold" class="text-primary"></iconify-icon>
                                <span>Ketentuan Kata Sandi:</span>
                            </h6>
                            <ul class="mb-0 ps-20 text-secondary-light text-xs d-flex flex-column gap-1">
                                <li>Panjang minimal <strong>8 karakter</strong>.</li>
                                <li>Kombinasi huruf besar (A-Z), huruf kecil (a-z), dan angka (0-9).</li>
                                <li>Dianjurkan menyertakan karakter simbol (!@#$%^&*).</li>
                                <li>Hindari penggunaan nama lengkap, username, atau tanggal lahir.</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-12 pt-16 border-top">
                            <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                                <iconify-icon icon="solar:lock-check-bold" class="text-lg"></iconify-icon>
                                <span>Perbarui Kata Sandi</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-toggle-pwd').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                var input = document.getElementById(targetId);
                if (!input) return;
                var icon = this.querySelector('iconify-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) icon.setAttribute('icon', 'solar:eye-closed-linear');
                } else {
                    input.type = 'password';
                    if (icon) icon.setAttribute('icon', 'solar:eye-linear');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>