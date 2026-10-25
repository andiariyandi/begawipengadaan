<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/settings/_nav_settings', ['activeSetting' => 'notification']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Notifikasi') ?></h6>
        <p class="text-secondary-light mb-0">Tentukan kanal komunikasi, identitas pengirim, dan preferensi notifikasi sistem.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/sistem/pengaturan/notification') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-lg mb-4">Kanal Komunikasi Sistem</h6>
                    <p class="text-secondary-light text-sm mb-0">Atur kanal aktif yang digunakan sistem untuk mengirimkan pemberitahuan ke pengguna.</p>
                </div>

                <div class="col-md-6">
                    <div class="border border-neutral-200 radius-8 p-16 h-100">
                        <div class="form-switch switch-primary d-flex align-items-center gap-12">
                            <input type="hidden" name="email_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="email_enabled" value="1" id="emailChannel" <?= ($settings['email_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="emailChannel">Kanal Email</label>
                        </div>
                        <p class="text-secondary-light text-sm mb-0 mt-8">Kirim salinan notifikasi ke alamat email resmi terdaftar pengguna.</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border border-neutral-200 radius-8 p-16 h-100">
                        <div class="form-switch switch-primary d-flex align-items-center gap-12">
                            <input type="hidden" name="in_app_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="in_app_enabled" value="1" id="inAppChannel" <?= ($settings['in_app_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="inAppChannel">Kanal In-App (Dashboard)</label>
                        </div>
                        <p class="text-secondary-light text-sm mb-0 mt-8">Tampilkan notifikasi secara langsung pada dashboard dan workspace pengguna.</p>
                    </div>
                </div>

                <div class="col-12">
                    <hr class="my-8">
                </div>

                <div class="col-12">
                    <div class="alert alert-primary bg-primary-50 border-primary-100 text-primary-900 radius-8 p-16 d-flex align-items-start gap-12 mb-0">
                        <iconify-icon icon="solar:shield-check-bold" class="text-2xl flex-shrink-0 text-primary-600 mt-2"></iconify-icon>
                        <div class="text-sm">
                            <strong>Kebijakan Pengiriman Email Manual (Zero Automated Email):</strong> Notifikasi utama sistem berjalan secara real-time melalui <em>Kanal In-App</em>. Pengiriman email berstatus opsional dan <strong>100% dikendalikan secara manual oleh Administrator</strong> (melalui antrean dan tombol kirim manual) untuk mencegah spam dan melindungi batas kuota 500 email/hari Google Mail.
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h6 class="text-lg mb-4 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:letter-bold" class="text-primary-600 text-xl"></iconify-icon>
                        Konfigurasi Akun Google Mail (Gmail SMTP)
                    </h6>
                    <p class="text-secondary-light text-sm mb-0">Atur akun Gmail biasa dan Sandi Aplikasi Google (App Password) untuk pengiriman email resmi secara manual.</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="smtp_user">Alamat Akun Gmail Pengirim <span class="text-danger-600">*</span></label>
                    <input type="email" id="smtp_user" name="smtp_user" class="form-control radius-8" value="<?= esc($emailConfig['smtp_user'] ?? $settings['sender_email'] ?? '') ?>" placeholder="contoh@gmail.com" required>
                    <span class="text-xs text-secondary-light mt-4 d-block">Gunakan alamat Gmail biasa pengelola (misal: begawipbj.lampung@gmail.com)</span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="smtp_pass">Sandi Aplikasi Google (App Password 16-Karakter) <span class="text-danger-600">*</span></label>
                    <input type="password" id="smtp_pass" name="smtp_pass" class="form-control radius-8" value="<?= esc($emailConfig['smtp_pass'] ?? '') ?>" placeholder="16 karakter sandi aplikasi" autocomplete="new-password">
                    <span class="text-xs text-secondary-light mt-4 d-block">Dibuat via Akun Google &gt; Keamanan &gt; Verifikasi 2 Langkah &gt; <strong>Sandi Aplikasi</strong> (Bukan kata sandi login biasa).</span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="from_name">Nama Pengirim Resmi <span class="text-danger-600">*</span></label>
                    <input type="text" id="from_name" name="from_name" class="form-control radius-8" value="<?= esc($emailConfig['from_name'] ?? $settings['sender_name'] ?? 'BegawiPengadaan Provinsi Lampung') ?>" required>
                    <span class="text-xs text-secondary-light mt-4 d-block">Nama resmi instansi yang muncul di inbox penerima</span>
                </div>

                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="smtp_port">Port SMTP</label>
                            <input type="number" id="smtp_port" name="smtp_port" class="form-control radius-8" value="<?= esc($emailConfig['smtp_port'] ?? 587) ?>">
                            <span class="text-xxs text-secondary-light mt-2 d-block">587 (TLS) atau 465 (SSL)</span>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="smtp_crypto">Protokol Keamanan</label>
                            <select id="smtp_crypto" name="smtp_crypto" class="form-select radius-8">
                                <option value="tls" <?= ($emailConfig['smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
                                <option value="ssl" <?= ($emailConfig['smtp_crypto'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="smtp_host" value="smtp.gmail.com">
                <input type="hidden" name="sender_email" value="<?= esc($emailConfig['smtp_user'] ?? $settings['sender_email'] ?? '') ?>">
                <input type="hidden" name="sender_name" value="<?= esc($emailConfig['from_name'] ?? $settings['sender_name'] ?? 'BegawiPengadaan') ?>">

                <div class="col-12">
                    <hr class="my-8">
                </div>

                <div class="col-12">
                    <h6 class="text-lg mb-4">Frekuensi & Ringkasan</h6>
                    <p class="text-secondary-light text-sm mb-0">Kebijakan pengiriman berkala notifikasi.</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="digest_frequency">Frekuensi Pengiriman</label>
                    <select id="digest_frequency" name="digest_frequency" class="form-select radius-8">
                        <option value="instant" <?= ($settings['digest_frequency'] ?? 'instant') === 'instant' ? 'selected' : '' ?>>Instan (Langsung saat kejadian)</option>
                        <option value="daily" <?= ($settings['digest_frequency'] ?? 'instant') === 'daily' ? 'selected' : '' ?>>Harian (Ringkasan setiap malam)</option>
                        <option value="weekly" <?= ($settings['digest_frequency'] ?? 'instant') === 'weekly' ? 'selected' : '' ?>>Mingguan (Ringkasan awal pekan)</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center gap-12">
                        <input type="hidden" name="daily_digest" value="0">
                        <input class="form-check-input" type="checkbox" name="daily_digest" value="1" id="dailyDigest" <?= ($settings['daily_digest'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label text-sm fw-medium" for="dailyDigest">Gabungkan notifikasi non-prioritas ke dalam ringkasan harian (Daily Digest)</label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-3 mt-12">
                    <a href="<?= site_url('dashboard/sistem/notifikasi') ?>" class="btn btn-outline-neutral-400 radius-8 px-20 py-11">Buka Log Notifikasi</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-read-outline" class="text-xl"></iconify-icon>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <hr class="my-24">

        <!-- Form Uji Coba Pengiriman Email -->
        <div class="bg-neutral-50 border border-neutral-200 radius-8 p-20">
            <h6 class="text-base fw-bold mb-4 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:shield-check-bold" class="text-success-main text-xl"></iconify-icon>
                Uji Koneksi Google Mail SMTP (Test Connection)
            </h6>
            <p class="text-xs text-secondary-light mb-16">
                Kirim 1 email pengujian formal untuk memverifikasi apakah akun Gmail dan sandi aplikasi 16-karakter sudah dapat melakukan handshake TLS dengan sukses.
            </p>
            <form action="<?= site_url('dashboard/sistem/pengaturan-notifikasi/test-email') ?>" method="post" class="row g-3 align-items-center">
                <?= csrf_field() ?>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><iconify-icon icon="solar:letter-linear"></iconify-icon></span>
                        <input type="email" name="test_target_email" class="form-control radius-end-8" placeholder="Masukkan email penerima uji coba..." value="<?= esc(session()->get('email') ?? $emailConfig['smtp_user'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-success radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 text-sm w-100 justify-content-center">
                        <iconify-icon icon="solar:plain-bold" class="text-base"></iconify-icon>
                        Kirim Email Uji Coba Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>