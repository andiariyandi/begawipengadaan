<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Artikel') ?></h6><p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p></div>
    <a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card"><div class="card-body p-24"><form action="#"><div class="row gy-20">
    <div class="col-md-8"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Artikel</label><input type="text" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'Prinsip Pengadaan Barang/Jasa Pemerintah' : '' ?>" placeholder="Masukkan judul artikel"></div>
    <div class="col-md-4"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label><select class="form-select radius-8"><option>Dasar PBJ</option><option>Perencanaan</option><option>Manajemen Kontrak</option><option>Regulasi</option></select></div>
    <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Penulis</label><input type="text" class="form-control radius-8" placeholder="Nama penulis"></div>
    <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Publikasi</label><select class="form-select radius-8"><option>Draft</option><option>Ditinjau</option><option>Aktif</option></select></div>
    <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label><textarea class="form-control radius-8" rows="3" placeholder="Ringkasan artikel"></textarea></div>
    <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Isi Artikel</label><textarea class="form-control radius-8" rows="10" placeholder="Tulis isi artikel di sini"></textarea></div>
    <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Artikel</button></div>
</div></form></div></div>
<?= $this->endSection() ?>