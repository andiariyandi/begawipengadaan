<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Wilayah dan Alamat Ringkas') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 2, 'currentKey' => $currentKey ?? 'wilayah']) ?>

<form action="#" method="post">
    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="province">
        <option value="">Pilih provinsi</option>
        <option value="lampung">Lampung</option>
        <option value="dki-jakarta">DKI Jakarta</option>
        <option value="jawa-barat">Jawa Barat</option>
        <option value="lainnya">Provinsi lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten/Kota</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="regency">
        <option value="">Pilih kabupaten/kota</option>
        <option value="bandar-lampung">Kota Bandar Lampung</option>
        <option value="metro">Kota Metro</option>
        <option value="lampung-selatan">Kabupaten Lampung Selatan</option>
        <option value="lainnya">Kabupaten/kota lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="district">
        <option value="">Pilih kecamatan</option>
        <option value="contoh">Pilih setelah kabupaten/kota</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Desa/Kelurahan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="village">
        <option value="">Pilih desa/kelurahan</option>
        <option value="contoh">Pilih setelah kecamatan</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Ringkas</label>
    <textarea class="form-control bg-neutral-50 radius-12" name="address" rows="3" placeholder="Nama jalan, nomor, lingkungan, atau keterangan alamat"></textarea>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a>
        <a href="<?= site_url('dashboard/daftar/profil/asn') ?>" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Lanjut</a>
    </div>

    <p class="text-center text-secondary-light text-xs mt-20 mb-0">Dropdown wilayah masih dummy. Implementasi produksi wajib memakai master wilayah dan relasi bertingkat.</p>
</form>


<?= $this->endSection() ?>

<!-- switch ($profileType) {
case 'asn':
return redirect()->to(site_url('dashboard/daftar/profil/asn'));

case 'provider':
return redirect()->to(site_url('dashboard/daftar/profil/penyedia'));

case 'academic':
return redirect()->to(site_url('dashboard/daftar/profil/akademisi'));

default:
return redirect()->to(site_url('dashboard/daftar/profil/masyarakat'));
} -->