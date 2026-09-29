<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Unit Organisasi') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Unit</label><input type="text" class="form-control radius-8" placeholder="Contoh: BPPBJ"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Unit</label><select class="form-select radius-8">
                        <option>Biro</option>
                        <option>Badan</option>
                        <option>Dinas</option>
                        <option>Inspektorat</option>
                        <option>UPT</option>
                    </select></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Unit Organisasi</label><input type="text" class="form-control radius-8" placeholder="Masukkan nama unit organisasi"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Induk</label><select class="form-select radius-8">
                        <option>Pemerintah Provinsi Lampung</option>
                        <option>Sekretariat Daerah</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label><select class="form-select radius-8">
                        <option>Aktif</option>
                        <option>Tidak Aktif</option>
                    </select></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/akses/unit-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Unit</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>