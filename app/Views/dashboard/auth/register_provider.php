<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPengadaan</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Lengkapi Profil Penyedia Barang/Jasa') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 3, 'currentKey' => $currentKey ?? 'profil']) ?>

<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-20 radius-8 py-12 px-16" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl text-danger-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1 text-sm"><?= esc($error) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-20 radius-8 py-12 px-16" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl text-danger-main flex-shrink-0"></iconify-icon>
            <strong class="text-sm">Kendala Validasi Profil Penyedia:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/daftar/profil/penyedia') ?>" method="post">
    <?= csrf_field() ?>
    <div class="icon-field mb-16"><span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:card-linear"></iconify-icon></span><input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="nik" placeholder="NIK (16 digit)"></div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Badan Usaha</label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="company_name" placeholder="Nama perusahaan atau badan usaha">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bentuk Badan Usaha</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="business_entity_type">
        <option value="">Pilih bentuk badan usaha</option>
        <option value="perseorangan">Perseorangan</option>
        <option value="cv">CV</option>
        <option value="pt">PT</option>
        <option value="koperasi">Koperasi</option>
        <option value="firma">Firma</option>
        <option value="lainnya">Lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan Pendaftar</label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="registrant_position" placeholder="Contoh: Direktur, Pemilik, Staf Tender">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Penanggung Jawab</label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="responsible_person" placeholder="Nama kontak penanggung jawab">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIB <span class="text-secondary-light fw-normal">(Disarankan)</span></label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="nib" placeholder="Nomor Induk Berusaha">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Badan Usaha <span class="text-secondary-light fw-normal">(Disarankan)</span></label>
    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="company_email" placeholder="email@perusahaan.co.id">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Badan Usaha <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <textarea class="form-control bg-neutral-50 radius-12" rows="3" name="company_address" placeholder="Alamat administratif badan usaha"></textarea>

    <div class="d-flex gap-12 mt-32"><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a><button type="submit" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Tinjau Data</button></div>
    <p class="text-secondary-light text-xs mt-20 mb-0">NIK dan NIB adalah data restricted. Upload dokumen tidak diwajibkan pada registrasi awal.</p>
</form>
<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>