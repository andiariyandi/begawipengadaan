<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Organisasi') ?></h6>
        <p class="text-secondary-light mb-0">Data instansi utama yang ditampilkan pada dokumen dan komunikasi sistem.</p>
    </div><a href="<?= site_url('dashboard/settings') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Instansi</label><input type="text" class="form-control radius-8" value="Pemerintah Provinsi Lampung"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Pengelola</label><input type="text" class="form-control radius-8" value="Biro Pengadaan Barang dan Jasa"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Resmi</label><input type="email" class="form-control radius-8" value="biropbj@lampungprov.go.id"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Telepon</label><input type="text" class="form-control radius-8" value="+62 851-1777-9783"></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label><textarea class="form-control radius-8" rows="3">Kompleks Kantor Gubernur Provinsi Lampung, Bandar Lampung.</textarea></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Logo Instansi</label><input type="file" class="form-control radius-8"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Website Resmi</label><input type="url" class="form-control radius-8" value="https://lampungprov.go.id"></div>
                <div class="col-12 d-flex justify-content-end"><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>