<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>
    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Lengkapi Profil Penyedia Barang/Jasa') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 3 dari 4 — Lengkapi data spesifik untuk profil penyedia / vendor.') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', [
    'currentStep' => $currentStep ?? 3,
    'currentKey'  => $currentKey ?? 'profil',
]) ?>

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

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="nik">
            Nomor Induk Kependudukan (NIK Pendaftar) <span class="text-secondary-light fw-normal">(16 digit)</span>
        </label>
        <div class="icon-field">
            <span class="icon top-50 translate-middle-y">
                <iconify-icon icon="solar:card-linear" class="text-lg text-secondary-light"></iconify-icon>
            </span>
            <input
                type="text"
                class="form-control h-56-px bg-neutral-50 radius-12 ps-48"
                id="nik"
                name="nik"
                placeholder="NIK pendaftar (16 digit)"
                maxlength="16">
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="company_name">
            Nama Badan Usaha / Perusahaan <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="company_name"
            name="company_name"
            placeholder="Contoh: PT Lampung Berjaya Konstruksi"
            required>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="business_entity_type">
            Bentuk Badan Usaha <span class="text-danger">*</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="business_entity_type" id="business_entity_type" required>
            <option value="">-- Pilih Bentuk Badan Usaha --</option>
            <option value="perseorangan">Perseorangan / Usaha Mikro</option>
            <option value="cv">CV (Commanditaire Vennootschap)</option>
            <option value="pt">PT (Perseroan Terbatas)</option>
            <option value="koperasi">Koperasi</option>
            <option value="firma">Firma</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="registrant_position">
            Jabatan Pendaftar di Perusahaan <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="registrant_position"
            name="registrant_position"
            placeholder="Contoh: Direktur, Pemilik, Staf Tender PBJ"
            required>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="responsible_person">
            Nama Penanggung Jawab Resmi <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="responsible_person"
            name="responsible_person"
            placeholder="Nama lengkap pimpinan / direktur penanggung jawab"
            required>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="nib">
            Nomor Induk Berusaha (NIB) <span class="text-secondary-light fw-normal">(Disarankan)</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="nib"
            name="nib"
            placeholder="Nomor NIB OSS (13 digit)">
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="company_email">
            Email Resmi Perusahaan <span class="text-secondary-light fw-normal">(Disarankan)</span>
        </label>
        <input
            type="email"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="company_email"
            name="company_email"
            placeholder="kontak@perusahaan.co.id">
    </div>

    <div class="mb-24">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="company_address">
            Alamat Administratif Perusahaan <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <textarea
            class="form-control bg-neutral-50 radius-12"
            rows="3"
            id="company_address"
            name="company_address"
            placeholder="Alamat kantor / operasional badan usaha"></textarea>
    </div>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
            <span>Kembali</span>
        </a>
        <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <span>Lanjut: Tinjau Data</span>
            <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
        </button>
    </div>

    <p class="text-secondary-light text-xs mt-20 mb-0 text-center">
        <iconify-icon icon="solar:shield-check-linear" class="align-middle me-1"></iconify-icon>
        NIK dan NIB adalah data terlindungi. Unggah dokumen legalitas tidak diwajibkan pada registrasi awal.
    </p>
</form>

<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>