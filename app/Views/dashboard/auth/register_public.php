<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Lengkapi Profil Masyarakat Umum') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 3, 'currentKey' => $currentKey ?? 'profil']) ?>

<form action="#" method="post">
    <div class="icon-field mb-16"><span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:card-linear"></iconify-icon></span><input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="nik" placeholder="NIK (16 digit)"></div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pekerjaan/Status Saat Ini</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="occupation_status">
        <option value="">Pilih pekerjaan/status</option>
        <option value="mahasiswa">Mahasiswa</option>
        <option value="pegawai_swasta">Pegawai Swasta</option>
        <option value="wiraswasta">Wiraswasta</option>
        <option value="pencari_kerja">Pencari Kerja</option>
        <option value="perangkat_desa">Perangkat Desa</option>
        <option value="masyarakat">Masyarakat</option>
        <option value="lainnya">Lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Institusi/Organisasi <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="organization_name" placeholder="Nama institusi atau organisasi bila ada">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bidang Minat PBJ <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="procurement_interest[]" multiple size="5">
        <option value="pembelajaran_pbj">Pembelajaran PBJ</option>
        <option value="regulasi">Regulasi Pengadaan</option>
        <option value="perencanaan">Perencanaan Pengadaan</option>
        <option value="pemilihan">Pemilihan Penyedia</option>
        <option value="kontrak">Manajemen Kontrak</option>
        <option value="pengadaan_berkelanjutan">Pengadaan Berkelanjutan</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mt-16 mb-8">Tujuan Penggunaan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <select class="form-select h-56-px bg-neutral-50 radius-12" name="usage_purpose">
        <option value="">Pilih tujuan penggunaan</option>
        <option value="belajar">Belajar</option>
        <option value="event">Mengikuti Event</option>
        <option value="regulasi">Mencari Regulasi</option>
        <option value="referensi_dokumen">Referensi Dokumen</option>
        <option value="lainnya">Lainnya</option>
    </select>

    <div class="d-flex gap-12 mt-32"><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a><a href="<?= site_url('dashboard/daftar/tinjau') ?>" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Tinjau Data</a></div>
    <p class="text-secondary-light text-xs mt-20 mb-0">Tidak ada upload dokumen wajib pada registrasi awal.</p>
</form>
<?= $this->endSection() ?>