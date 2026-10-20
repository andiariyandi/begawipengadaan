<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Preferensi Akun') ?></h6>
        <p class="text-secondary-light mb-0">Atur kustomisasi antarmuka, bahasa sistem, dan preferensi notifikasi pribadi Anda.</p>
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
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/password') ?>">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg"></iconify-icon>
                    <span>Ubah Kata Sandi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/preferences') ?>">
                    <iconify-icon icon="solar:settings-bold" class="text-lg"></iconify-icon>
                    <span>Preferensi Akun</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/profile/preferences') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row gy-24">
                <!-- Section 1: Tampilan & Bahasa -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 pb-12 border-bottom">
                        <iconify-icon icon="solar:monitor-smartphone-bold" class="text-xl text-primary"></iconify-icon>
                        <h6 class="text-base fw-bold mb-0">Tampilan &amp; Lokalisasi</h6>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="prefLanguage">
                        Bahasa Antarmuka
                    </label>
                    <select id="prefLanguage" name="language" class="form-select radius-8">
                        <option value="id" <?= ($preferences['language'] ?? 'id') === 'id' ? 'selected' : '' ?>>
                            🇮🇩 Bahasa Indonesia (Default)
                        </option>
                        <option value="en" <?= ($preferences['language'] ?? 'id') === 'en' ? 'selected' : '' ?>>
                            🇬🇧 English
                        </option>
                    </select>
                    <small class="text-secondary-light text-2xs">Bahasa utama yang digunakan pada menu dan navigasi dashboard.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="prefTheme">
                        Tema Visual Dashboard
                    </label>
                    <select id="prefTheme" name="theme" class="form-select radius-8">
                        <option value="system" <?= ($preferences['theme'] ?? 'system') === 'system' ? 'selected' : '' ?>>
                            🖥️ Gunakan Tema Sistem (Otomatis)
                        </option>
                        <option value="light" <?= ($preferences['theme'] ?? 'system') === 'light' ? 'selected' : '' ?>>
                            ☀️ Mode Terang (Light Mode)
                        </option>
                        <option value="dark" <?= ($preferences['theme'] ?? 'system') === 'dark' ? 'selected' : '' ?>>
                            🌙 Mode Gelap (Dark Mode)
                        </option>
                    </select>
                    <small class="text-secondary-light text-2xs">Pilihan skema warna antarmuka kerja Anda.</small>
                </div>

                <div class="col-12">
                    <hr class="my-8">
                </div>

                <!-- Section 2: Notifikasi Pribadi -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 pb-12 border-bottom">
                        <iconify-icon icon="solar:bell-bing-bold" class="text-xl text-warning"></iconify-icon>
                        <h6 class="text-base fw-bold mb-0">Preferensi Notifikasi Pribadi</h6>
                    </div>
                </div>

                <!-- Notifikasi Email -->
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12 p-16 bg-neutral-50 border radius-12">
                        <div>
                            <label class="form-check-label fw-bold text-primary-light text-sm mb-1" for="profileEmailNotification">
                                Notifikasi Melalui Email
                            </label>
                            <p class="text-secondary-light text-xs mb-0">
                                Terima pemberitahuan mengenai permohonan, verifikasi profil, tiket konsultasi, dan aktivitas penting akun ke email Anda.
                            </p>
                        </div>
                        <input class="form-check-input flex-shrink-0" 
                               type="checkbox" 
                               name="email_notification" 
                               value="1" 
                               id="profileEmailNotification" 
                               <?= ! empty($preferences['email_notification']) ? 'checked' : '' ?>>
                    </div>
                </div>

                <!-- Notifikasi Dalam Aplikasi -->
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12 p-16 bg-neutral-50 border radius-12">
                        <div>
                            <label class="form-check-label fw-bold text-primary-light text-sm mb-1" for="profileInAppNotification">
                                Notifikasi Dalam Aplikasi (In-App)
                            </label>
                            <p class="text-secondary-light text-xs mb-0">
                                Tampilkan lonceng pemberitahuan dan pop-up pemberitahuan langsung pada dashboard BegawiPengadaan saat login.
                            </p>
                        </div>
                        <input class="form-check-input flex-shrink-0" 
                               type="checkbox" 
                               name="in_app_notification" 
                               value="1" 
                               id="profileInAppNotification" 
                               <?= ! empty($preferences['in_app_notification']) ? 'checked' : '' ?>>
                    </div>
                </div>

                <!-- Ringkasan Laporan Berkala -->
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12 p-16 bg-neutral-50 border radius-12">
                        <div>
                            <label class="form-check-label fw-bold text-primary-light text-sm mb-1" for="profileReportNotification">
                                Ringkasan Laporan Mingguan &amp; Status Operasional
                            </label>
                            <p class="text-secondary-light text-xs mb-0">
                                Dapatkan ringkasan statistik layanan, antrean verifikasi, dan progres pelatihan secara periodik setiap awal pekan.
                            </p>
                        </div>
                        <input class="form-check-input flex-shrink-0" 
                               type="checkbox" 
                               name="report_notification" 
                               value="1" 
                               id="profileReportNotification" 
                               <?= ! empty($preferences['report_notification']) ? 'checked' : '' ?>>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 d-flex justify-content-end gap-12 pt-16 border-top">
                    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Preferensi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>