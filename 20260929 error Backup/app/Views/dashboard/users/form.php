<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengguna') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#" method="post">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap</label><input type="text" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'Rina Oktaviani' : '' ?>" placeholder="Masukkan nama lengkap"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label><input type="email" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'rina.oktaviani@lampungprov.go.id' : '' ?>" placeholder="nama@instansi.go.id"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Profil</label><select class="form-select radius-8">
                        <option>ASN</option>
                        <option>Penyedia Barang/Jasa</option>
                        <option>Akademisi</option>
                        <option>Masyarakat Umum</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Akun</label><select class="form-select radius-8">
                        <option>Aktif</option>
                        <option>Pending</option>
                        <option>Inactive</option>
                        <option>Suspended</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Role</label><select class="form-select radius-8">
                        <option>Public User</option>
                        <option>Peserta</option>
                        <option>Trainer</option>
                        <option>Evaluator</option>
                        <option>Operator</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Organisasi</label><select class="form-select radius-8">
                        <option>Biro Pengadaan Barang dan Jasa</option>
                        <option>Inspektorat Provinsi Lampung</option>
                        <option>Badan Kepegawaian Daerah</option>
                    </select></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Admin</label><textarea class="form-control radius-8" rows="4" placeholder="Tambahkan catatan jika diperlukan"></textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>