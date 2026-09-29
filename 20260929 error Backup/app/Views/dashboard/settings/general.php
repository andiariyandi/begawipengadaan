<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Umum') ?></h6>
        <p class="text-secondary-light mb-0">Konfigurasi dasar identitas dan preferensi aplikasi.</p>
    </div><a href="<?= site_url('dashboard/settings') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Aplikasi</label><input type="text" class="form-control radius-8" value="BegawiPBJ"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Singkat</label><input type="text" class="form-control radius-8" value="BegawiPBJ"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Bahasa Default</label><select class="form-select radius-8">
                        <option>Bahasa Indonesia</option>
                        <option>English</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Zona Waktu</label><select class="form-select radius-8">
                        <option>Asia/Jakarta (WIB)</option>
                        <option>Asia/Makassar (WITA)</option>
                        <option>Asia/Jayapura (WIT)</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Format Tanggal</label><select class="form-select radius-8">
                        <option>DD MMMM YYYY</option>
                        <option>DD/MM/YYYY</option>
                        <option>YYYY-MM-DD</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Pengirim Sistem</label><input type="email" class="form-control radius-8" value="noreply@begawipbj.go.id"></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi Aplikasi</label><textarea class="form-control radius-8" rows="4">Platform pembelajaran dan pengelolaan pengadaan barang/jasa Pemerintah Provinsi Lampung.</textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</button><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>