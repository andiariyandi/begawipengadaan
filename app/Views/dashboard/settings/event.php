<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Event & Agenda') ?></h6>
        <p class="text-secondary-light mb-0">Konfigurasi standar registrasi, kehadiran, dan slider sorotan hero event di landing page.</p>
    </div>
    <a href="<?= site_url('dashboard/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/pengaturan/event') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:slider-vertical-bold" class="text-lg"></iconify-icon>
                        <span>Konfigurasi Slider Sorotan Event &amp; Agenda (Landing Page)</span>
                    </h6>
                    <p class="text-secondary-light text-xs mb-16">
                        Tentukan mode tampilan dan kuota agenda/kegiatan yang diutamakan pada banner hero carousel di landing page <code>/event</code>.
                    </p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_mode">Mode Tampilan Sorotan Default</label>
                    <?php $spMode = $settings['spotlight_mode'] ?? 'all'; ?>
                    <select id="spotlight_mode" name="spotlight_mode" class="form-select radius-8">
                        <option value="all" <?= $spMode === 'all' ? 'selected' : '' ?>>Semua Event Aktif (Memprioritaskan Sorotan Hero &amp; Tanggal Terdekat)</option>
                        <option value="featured" <?= $spMode === 'featured' ? 'selected' : '' ?>>Khusus Event Sorotan / Unggulan Terpilih (is_featured = 1)</option>
                        <option value="upcoming" <?= $spMode === 'upcoming' ? 'selected' : '' ?>>Khusus Agenda Mendatang (Upcoming)</option>
                        <optgroup label="Berdasarkan Tipe Kegiatan:">
                            <option value="webinar" <?= $spMode === 'webinar' ? 'selected' : '' ?>>Khusus Webinar PBJ</option>
                            <option value="workshop" <?= $spMode === 'workshop' ? 'selected' : '' ?>>Khusus Workshop / Lokakarya</option>
                            <option value="bimbingan_teknis" <?= $spMode === 'bimbingan_teknis' ? 'selected' : '' ?>>Khusus Bimbingan Teknis (Bimtek)</option>
                            <option value="sosialisasi" <?= $spMode === 'sosialisasi' ? 'selected' : '' ?>>Khusus Sosialisasi Regulasi</option>
                            <option value="sertifikasi" <?= $spMode === 'sertifikasi' ? 'selected' : '' ?>>Khusus Ujian Sertifikasi</option>
                            <option value="rapat_koordinasi" <?= $spMode === 'rapat_koordinasi' ? 'selected' : '' ?>>Khusus Rapat Koordinasi (Rakor)</option>
                        </optgroup>
                    </select>
                    <small class="text-secondary-light text-xs">Pilihan ini menentukan agenda kegiatan yang dimuat ke dalam headline carousel landing page Event.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="spotlight_limit">Batas Maksimal Item Sorotan</label>
                    <input id="spotlight_limit" type="number" min="3" max="15" name="spotlight_limit" class="form-control radius-8" value="<?= esc($settings['spotlight_limit'] ?? '5') ?>" required>
                    <small class="text-secondary-light text-xs">Jumlah slide maksimal yang ditampilkan dalam headline carousel (3 - 15 slide).</small>
                </div>

                <div class="col-12 mt-16">
                    <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:calendar-date-bold" class="text-lg"></iconify-icon>
                        <span>Parameter Umum Pelaksanaan Kegiatan</span>
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="default_capacity">Kapasitas Default Peserta</label>
                    <input id="default_capacity" type="number" min="1" name="default_capacity" class="form-control radius-8" value="<?= esc($settings['default_capacity'] ?? '100') ?>" required>
                    <small class="text-secondary-light text-xs">Kapasitas default saat membuat form event baru jika tidak ditentukan.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="reminder_hours_before">Pengingat Otomatis (Jam Sebelum Acara)</label>
                    <div class="input-group">
                        <input id="reminder_hours_before" type="number" min="1" max="168" name="reminder_hours_before" class="form-control radius-8" value="<?= esc($settings['reminder_hours_before'] ?? '24') ?>">
                        <span class="input-group-text">Jam Sebelum Acara</span>
                    </div>
                    <small class="text-secondary-light text-xs">Waktu notifikasi otomatis dikirimkan sebelum acara dimulai.</small>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch switch-primary d-flex align-items-center gap-12">
                        <input class="form-check-input" type="checkbox" name="waitlist_enabled" value="1" id="waitlist_enabled" <?= (! empty($settings['waitlist_enabled']) && $settings['waitlist_enabled'] === '1') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium text-secondary-light" for="waitlist_enabled">Aktifkan antrean daftar tunggu (waitlist) secara otomatis jika kuota event telah penuh</label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch switch-primary d-flex align-items-center gap-12">
                        <input class="form-check-input" type="checkbox" name="auto_attendance" value="1" id="auto_attendance" <?= (! empty($settings['auto_attendance']) && $settings['auto_attendance'] === '1') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium text-secondary-light" for="auto_attendance">Aktifkan verifikasi presensi kehadiran otomatis via kode QR / token peserta</label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-16">
                    <a href="<?= site_url('dashboard/pengaturan/event') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
