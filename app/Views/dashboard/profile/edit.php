<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ubah Profil') ?></h6>
        <p class="text-secondary-light mb-0">Perbarui informasi dasar pada akun Anda.</p>
    </div>
    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center gap-16"><span class="w-80-px h-80-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-2xl fw-bold">AD</span>
                        <div>
                            <h6 class="text-md mb-4">Foto Profil</h6>
                            <p class="text-secondary-light text-sm mb-10">JPG atau PNG. Maksimum 2 MB.</p><input type="file" class="form-control form-control-sm radius-8" accept="image/png,image/jpeg">
                        </div>
                    </div>
                </div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap</label><input type="text" class="form-control radius-8" value="Administrator"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Pengguna</label><input type="text" class="form-control radius-8" value="administrator"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Email</label><input type="email" class="form-control radius-8" value="administrator@begawipbj.go.id"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Telepon</label><input type="text" class="form-control radius-8" value="+62 851-1777-9783"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit/OPD</label><input type="text" class="form-control radius-8" value="Biro Pengadaan Barang dan Jasa" readonly></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan</label><input type="text" class="form-control radius-8" value="Administrator Platform" readonly></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Bio Singkat</label><textarea class="form-control radius-8" rows="4" placeholder="Masukkan deskripsi singkat">Administrator platform BegawiPengadaan.</textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>