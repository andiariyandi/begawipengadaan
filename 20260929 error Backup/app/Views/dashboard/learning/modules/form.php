<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Materi dan Modul') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/pembelajaran/materi-modul') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Modul</label><input type="text" class="form-control radius-8" placeholder="Masukkan nama modul"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Course</label><select class="form-select radius-8">
                        <option>Dasar-Dasar Pengadaan Barang/Jasa</option>
                        <option>Manajemen Kontrak Pengadaan</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Modul</label><input type="number" class="form-control radius-8" placeholder="1"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label><select class="form-select radius-8">
                        <option>Draft</option>
                        <option>Aktif</option>
                    </select></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label><textarea class="form-control radius-8" rows="4" placeholder="Deskripsi materi atau modul"></textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/pembelajaran/materi-modul') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Modul</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>