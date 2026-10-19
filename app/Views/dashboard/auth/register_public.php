<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>
    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Lengkapi Profil Masyarakat Umum') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 3 dari 4 — Lengkapi data spesifik untuk profil masyarakat umum.') ?></p>
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
            <strong class="text-sm">Kendala Validasi Profil Masyarakat Umum:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= site_url('dashboard/daftar/profil/masyarakat') ?>" method="post">
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
                placeholder="Contoh: 1871012345670001"
                maxlength="16">
        </div>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="occupation_status">
            Pekerjaan / Status Saat Ini <span class="text-danger">*</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="occupation_status" id="occupation_status" required>
            <option value="">-- Pilih Pekerjaan / Status Saat Ini --</option>
            <option value="mahasiswa">Mahasiswa / Pelajar</option>
            <option value="pegawai_swasta">Pegawai Swasta</option>
            <option value="wiraswasta">Wiraswasta / Wirausaha</option>
            <option value="pencari_kerja">Pencari Kerja</option>
            <option value="perangkat_desa">Perangkat Desa</option>
            <option value="masyarakat">Masyarakat Umum</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="organization_name">
            Institusi / Organisasi <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <input
            type="text"
            class="form-control h-56-px bg-neutral-50 radius-12"
            id="organization_name"
            name="organization_name"
            placeholder="Nama instansi, komunitas, atau organisasi">
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="procurement_interest">
            Bidang Minat PBJ <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="procurement_interest" id="procurement_interest">
            <option value="">-- Pilih Bidang Minat PBJ --</option>
            <option value="semua">Semua Bidang Pengadaan (Umum)</option>
            <option value="pembelajaran_pbj">Pembelajaran & Pelatihan PBJ</option>
            <option value="regulasi">Regulasi & Kebijakan Pengadaan</option>
            <option value="perencanaan">Perencanaan Pengadaan</option>
            <option value="pemilihan">Pemilihan Penyedia (Tender / Seleksi)</option>
            <option value="kontrak">Manajemen Kontrak</option>
            <option value="pengadaan_berkelanjutan">Pengadaan Berkelanjutan & Ramah Lingkungan</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-24">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="usage_purpose">
            Tujuan Penggunaan <span class="text-secondary-light fw-normal">(Opsional)</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="usage_purpose" id="usage_purpose">
            <option value="">-- Pilih Tujuan Penggunaan Portal --</option>
            <option value="belajar">Belajar Mandiri Pengadaan</option>
            <option value="event">Mengikuti Event & Workshop</option>
            <option value="regulasi">Mencari Regulasi & Aturan PBJ</option>
            <option value="referensi_dokumen">Mencari Referensi Dokumen & Template</option>
            <option value="lainnya">Lainnya</option>
        </select>
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
        Tidak ada dokumen wajib diunggah pada tahap registrasi awal masyarakat umum.
    </p>
</form>

<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>