<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Notifikasi') ?></h6>
        <p class="text-secondary-light mb-0">Tentukan kanal komunikasi dan preferensi notifikasi sistem.</p>
    </div><a href="<?= site_url('dashboard/settings') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-lg mb-16">Kanal Notifikasi</h6>
                </div>
                <div class="col-md-6">
                    <div class="border border-neutral-200 radius-8 p-16">
                        <div class="form-switch switch-primary d-flex align-items-center gap-12"><input class="form-check-input" type="checkbox" id="emailChannel" checked><label class="form-check-label fw-semibold" for="emailChannel">Email</label></div>
                        <p class="text-secondary-light text-sm mb-0 mt-8">Kirim notifikasi ke alamat email pengguna.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border border-neutral-200 radius-8 p-16">
                        <div class="form-switch switch-primary d-flex align-items-center gap-12"><input class="form-check-input" type="checkbox" id="inAppChannel" checked><label class="form-check-label fw-semibold" for="inAppChannel">In-App Notification</label></div>
                        <p class="text-secondary-light text-sm mb-0 mt-8">Tampilkan notifikasi pada dashboard pengguna.</p>
                    </div>
                </div>
                <div class="col-12">
                    <hr class="my-8">
                </div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Pengirim</label><input type="email" class="form-control radius-8" value="noreply@begawipbj.go.id"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Pengirim</label><input type="text" class="form-control radius-8" value="BegawiPengadaan"></div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center gap-12"><input class="form-check-input" type="checkbox" id="dailyDigest"><label class="form-check-label" for="dailyDigest">Kirim ringkasan notifikasi harian untuk aktivitas non-prioritas</label></div>
                </div>
                <div class="col-12 d-flex justify-content-end"><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>