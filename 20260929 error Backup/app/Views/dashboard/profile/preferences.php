<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Preferensi Akun') ?></h6>
        <p class="text-secondary-light mb-0">Atur preferensi pribadi untuk pengalaman penggunaan dashboard.</p>
    </div>
    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-24">
                <div class="col-12">
                    <h6 class="text-lg mb-0">Tampilan</h6>
                </div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Bahasa</label><select class="form-select radius-8">
                        <option>Bahasa Indonesia</option>
                        <option>English</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tema Dashboard</label><select class="form-select radius-8">
                        <option>Gunakan Tema Sistem</option>
                        <option>Terang</option>
                        <option>Gelap</option>
                    </select></div>
                <div class="col-12">
                    <hr class="my-0">
                </div>
                <div class="col-12">
                    <h6 class="text-lg mb-0">Notifikasi Pribadi</h6>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12">
                        <div><label class="form-check-label fw-semibold" for="profileEmailNotification">Notifikasi Email</label>
                            <p class="text-secondary-light text-sm mb-0">Terima informasi penting melalui email.</p>
                        </div><input class="form-check-input" type="checkbox" id="profileEmailNotification" checked>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12">
                        <div><label class="form-check-label fw-semibold" for="profileInAppNotification">Notifikasi Dalam Aplikasi</label>
                            <p class="text-secondary-light text-sm mb-0">Tampilkan pemberitahuan pada dashboard.</p>
                        </div><input class="form-check-input" type="checkbox" id="profileInAppNotification" checked>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center justify-content-between gap-12">
                        <div><label class="form-check-label fw-semibold" for="profileReportNotification">Ringkasan Laporan Berkala</label>
                            <p class="text-secondary-light text-sm mb-0">Terima ringkasan aktivitas dan laporan mingguan.</p>
                        </div><input class="form-check-input" type="checkbox" id="profileReportNotification">
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Preferensi</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>