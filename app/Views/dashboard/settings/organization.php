<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/settings/_nav_settings', ['activeSetting' => 'organization']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Organisasi') ?></h6>
        <p class="text-secondary-light mb-0">Data instansi utama yang ditampilkan pada dokumen dan komunikasi sistem.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sistem/pengaturan/organization') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Instansi</label><input type="text" name="agency_name" class="form-control radius-8" value="<?= esc($settings['agency_name'] ?? 'Pemerintah Provinsi Lampung') ?>"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Pengelola</label><input type="text" name="managing_unit" class="form-control radius-8" value="<?= esc($settings['managing_unit'] ?? 'Biro Pengadaan Barang dan Jasa') ?>"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Resmi</label><input type="email" name="official_email" class="form-control radius-8" value="<?= esc($settings['official_email'] ?? 'biropbj@lampungprov.go.id') ?>"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Telepon</label><input type="text" name="phone_number" class="form-control radius-8" value="<?= esc($settings['phone_number'] ?? '+62 851-1777-9783') ?>"></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label><textarea name="address" class="form-control radius-8" rows="3"><?= esc($settings['address'] ?? 'Kompleks Kantor Gubernur Provinsi Lampung, Bandar Lampung.') ?></textarea></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Website Resmi</label><input type="url" name="official_website" class="form-control radius-8" value="<?= esc($settings['official_website'] ?? 'https://lampungprov.go.id') ?>"></div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/sistem/pengaturan-organisasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
