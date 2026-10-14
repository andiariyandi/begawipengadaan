<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPengadaan</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Lengkapi Profil Akademisi') ?></h4>
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
    <div class="icon-field mb-16"><span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:card-linear"></iconify-icon></span><input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="nik" placeholder="NIK (16 digit)"></div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Institusi</label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_name" placeholder="Perguruan tinggi, lembaga riset, atau organisasi profesi">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Institusi</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="institution_type">
        <option value="">Pilih jenis institusi</option>
        <option value="perguruan_tinggi">Perguruan Tinggi</option>
        <option value="lembaga_riset">Lembaga Riset</option>
        <option value="lembaga_pelatihan">Lembaga Pelatihan</option>
        <option value="organisasi_profesi">Organisasi Profesi</option>
        <option value="lainnya">Lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan/Peran</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="academic_role">
        <option value="">Pilih jabatan/peran</option>
        <option value="dosen">Dosen</option>
        <option value="peneliti">Peneliti</option>
        <option value="praktisi">Praktisi</option>
        <option value="konsultan">Konsultan</option>
        <option value="lainnya">Lainnya</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bidang Keahlian</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="expertise[]" multiple size="5">
        <option value="perencanaan_pengadaan">Perencanaan Pengadaan</option>
        <option value="pemilihan_penyedia">Pemilihan Penyedia</option>
        <option value="manajemen_kontrak">Manajemen Kontrak</option>
        <option value="pengadaan_berkelanjutan">Pengadaan Berkelanjutan</option>
        <option value="digital_procurement">Pengadaan Digital</option>
        <option value="lainnya">Lainnya</option>
    </select>
    <p class="text-xs text-secondary-light mt-n8 mb-16">Gunakan Ctrl/Cmd untuk memilih lebih dari satu bidang keahlian.</p>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Institusi <span class="text-secondary-light fw-normal">(Disarankan)</span></label>
    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_email" placeholder="nama@institusi.ac.id">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIDN/NUPTK/Identitas Profesi <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="professional_identifier" placeholder="NIDN, NUPTK, atau identitas profesi">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Portofolio URL <span class="text-secondary-light fw-normal">(Opsional, HTTPS)</span></label>
    <input type="url" class="form-control h-56-px bg-neutral-50 radius-12" name="portfolio_url" placeholder="https://contoh.id/portofolio">

    <div class="d-flex gap-12 mt-32"><a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a><button type="submit" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Tinjau Data</button></div>
    <p class="text-secondary-light text-xs mt-20 mb-0">CV dan bukti kompetensi tidak diwajibkan pada registrasi awal.</p>
</form>
<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>