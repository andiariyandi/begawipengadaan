<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>
    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Lengkapi Profil Akademisi') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 3 dari 4 — Lengkapi data spesifik untuk profil akademisi / peneliti.') ?></p>
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
            <strong class="text-sm">Kendala Validasi Profil Akademisi:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/daftar/profil/akademisi') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="nik">
            Nomor Induk Kependudukan (NIK) <span class="text-secondary-light fw-normal">(16 digit)</span>
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
                placeholder="NIK (16 digit)"
                maxlength="16">
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="institution_name">
            Nama Perguruan Tinggi / Lembaga <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="institution_name"
            name="institution_name"
            placeholder="Contoh: Universitas Lampung / Institut Teknologi Sumatera"
            required>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="institution_type">
            Jenis Institusi <span class="text-danger">*</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="institution_type" id="institution_type" required>
            <option value="">-- Pilih Jenis Institusi --</option>
            <option value="perguruan_tinggi">Perguruan Tinggi</option>
            <option value="lembaga_riset">Lembaga Riset / Litbang</option>
            <option value="lembaga_pelatihan">Lembaga Pelatihan / Diklat</option>
            <option value="organisasi_profesi">Organisasi Profesi PBJ</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="academic_role">
            Jabatan / Peran Akademik <span class="text-danger">*</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="academic_role" id="academic_role" required>
            <option value="">-- Pilih Jabatan / Peran --</option>
            <option value="dosen">Dosen / Tenaga Pengajar</option>
            <option value="peneliti">Peneliti / Peneliti Madya/Utama</option>
            <option value="praktisi">Praktisi / Narasumber</option>
            <option value="konsultan">Konsultan / Tenaga Ahli</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="academic-expertise">
            Bidang Keahlian PBJ <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="expertise" id="academic-expertise">
            <option value="">-- Pilih Bidang Keahlian Utama --</option>
            <option value="semua">Semua Bidang Pengadaan</option>
            <option value="perencanaan_pengadaan">Perencanaan Pengadaan</option>
            <option value="pemilihan_penyedia">Pemilihan Penyedia / Tender</option>
            <option value="manajemen_kontrak">Manajemen Kontrak</option>
            <option value="pengadaan_berkelanjutan">Pengadaan Berkelanjutan</option>
            <option value="digital_procurement">Pengadaan Digital (E-Katalog / SPSE)</option>
            <option value="hukum_pengadaan">Aspek Hukum Pengadaan PBJ</option>
            <option value="audit_pengawasan">Audit & Pengawasan Pengadaan</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="institution_email">
            Email Institusi <span class="text-secondary-light fw-normal">(Disarankan)</span>
        </label>
        <input
            type="email"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="institution_email"
            name="institution_email"
            placeholder="nama@institusi.ac.id">
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="professional_identifier">
            NIDN / NUPTK / Identitas Profesi <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="professional_identifier"
            name="professional_identifier"
            placeholder="NIDN, NUPTK, atau nomor registrasi profesi">
    </div>

    <div class="mb-24">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="portfolio_url">
            Portofolio URL / Profil Google Scholar <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <input
            type="url"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="portfolio_url"
            name="portfolio_url"
            placeholder="https://scholar.google.com/citations?user=...">
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
        CV dan bukti kompetensi tidak diwajibkan pada tahap registrasi awal.
    </p>
</form>

<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>