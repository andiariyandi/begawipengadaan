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
                    <h6 class="text-lg mb-4">Identitas Pengirim Email</h6>
                    <p class="text-secondary-light text-sm mb-0">Informasi identitas resmi yang tertera pada pengiriman email sistem.</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="sender_email">Email Pengirim <span class="text-danger-600">*</span></label>
                    <input type="email" id="sender_email" name="sender_email" class="form-control radius-8" value="<?= esc($settings['sender_email'] ?? 'noreply@begawipbj.lampungprov.go.id') ?>" required>
                    <span class="text-xs text-secondary-light mt-4 d-block">Contoh: noreply@begawipbj.lampungprov.go.id</span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="sender_name">Nama Pengirim <span class="text-danger-600">*</span></label>
                    <input type="text" id="sender_name" name="sender_name" class="form-control radius-8" value="<?= esc($settings['sender_name'] ?? 'BegawiPengadaan') ?>" required>
                    <span class="text-xs text-secondary-light mt-4 d-block">Nama institusi atau sistem pemancar email</span>
                </div>

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
    </div>
</div>
<?= $this->endSection() ?>