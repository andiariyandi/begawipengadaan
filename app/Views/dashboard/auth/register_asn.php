<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPengadaan</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Lengkapi Profil ASN') ?></h4>
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
            <strong class="text-sm">Kendala Validasi Profil ASN:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
$regProfile = $registrationProfile ?? session('registration_profile.fields') ?? [];
$selectedOrigin = old('asn_origin', $regProfile['asn_origin'] ?? '');
$selectedEmployment = old('employment_status', $regProfile['employment_status'] ?? '');
$selectedUnitId = (string) old('organization_unit_id', $regProfile['organization_unit_id'] ?? '');
$hasProcRole = old('has_procurement_role', $regProfile['has_procurement_role'] ?? 'no');
$selectedRoles = old('procurement_roles', $regProfile['procurement_roles'] ?? []);
if (is_string($selectedRoles)) {
    $decoded = json_decode($selectedRoles, true);
    $selectedRoles = is_array($decoded) ? $decoded : [$selectedRoles];
}
$selectedRoles = array_map('strval', (array) $selectedRoles);
?>

<form action="<?= site_url('dashboard/daftar/profil/asn') ?>" method="post" id="asn-register-form">
    <?= csrf_field() ?>
    
    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
        Asal Instansi ASN <span class="text-danger">*</span>
    </label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-20" name="asn_origin" id="asn-origin" required>
        <option value="">Pilih Asal Instansi</option>
        <option value="pemprov_lampung" <?= $selectedOrigin === 'pemprov_lampung' ? 'selected' : '' ?>>ASN Pemerintah Provinsi Lampung</option>
        <option value="kementerian_lembaga" <?= $selectedOrigin === 'kementerian_lembaga' ? 'selected' : '' ?>>ASN Kementerian / Lembaga</option>
        <option value="pemerintah_daerah_lain" <?= $selectedOrigin === 'pemerintah_daerah_lain' ? 'selected' : '' ?>>ASN Pemerintah Daerah Lain (Kab/Kota/Prov Lain)</option>
        <option value="pemerintah_desa" <?= $selectedOrigin === 'pemerintah_desa' ? 'selected' : '' ?>>Aparatur Pemerintah Desa</option>
        <option value="bumn_bumd" <?= $selectedOrigin === 'bumn_bumd' ? 'selected' : '' ?>>Pegawai BUMN / BUMD</option>
        <option value="lainnya" <?= $selectedOrigin === 'lainnya' ? 'selected' : '' ?>>ASN / Pegawai Instansi Lainnya</option>
    </select>

    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:card-linear"></iconify-icon></span>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="nip_nippk"
               value="<?= esc(old('nip_nippk', $regProfile['nip_nippk'] ?? '')) ?>"
               placeholder="Nomor Identitas Pegawai (NIP / NIPPK)">
    </div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
        Status Kepegawaian <span class="text-danger">*</span>
    </label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="employment_status" required>
        <option value="">Pilih Status Kepegawaian</option>
        <option value="pns" <?= $selectedEmployment === 'pns' ? 'selected' : '' ?>>PNS (Pegawai Negeri Sipil)</option>
        <option value="pppk" <?= $selectedEmployment === 'pppk' ? 'selected' : '' ?>>PPPK (Pegawai Pemerintah dengan Perjanjian Kerja)</option>
    </select>

    <!-- Section khusus ASN Pemprov Lampung (Terhubung langsung ke Master OPD Database) -->
    <div id="asn-pemprov-fields" class="d-none">
        <div class="mb-16">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Instansi Asal</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" value="Pemerintah Daerah Provinsi Lampung" readonly>
        </div>
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
            OPD / Perangkat Daerah Provinsi Lampung <span class="text-danger">*</span>
        </label>
        <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="organization_unit_id" id="organization-unit-id">
            <option value="">— Pilih OPD / Perangkat Daerah (Sesuai Database) —</option>
            <?php foreach (($organizationUnits ?? []) as $unit): ?>
                <option value="<?= esc($unit['id']) ?>" <?= $selectedUnitId === (string) $unit['id'] ? 'selected' : '' ?>>
                    <?= esc($unit['name']) ?><?= !empty($unit['code']) ? ' (' . esc($unit['code']) . ')' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="text-secondary-light text-xs d-block mb-16 mt-n12">
            <iconify-icon icon="solar:info-circle-linear" class="align-middle"></iconify-icon>
            Daftar Perangkat Daerah disinkronkan secara langsung dari master data sistem BegawiPengadaan.
        </span>
    </div>

    <!-- Section untuk ASN Luar Pemprov Lampung -->
    <div id="asn-luar-fields" class="d-none">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Instansi Induk</label>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_name"
               value="<?= esc(old('institution_name', $regProfile['institution_name'] ?? '')) ?>"
               placeholder="Contoh: Kementerian PUPR / Pemerintah Kota Bandar Lampung">

        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama OPD / Unit Kerja</label>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="external_unit_name"
               value="<?= esc(old('external_unit_name', $regProfile['external_unit_name'] ?? '')) ?>"
               placeholder="Contoh: Dinas Bina Marga / Bagian Pengadaan Barang & Jasa">

        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi Instansi</label>
        <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="institution_province">
            <option value="">Pilih Provinsi Instansi</option>
            <option value="lampung" <?= old('institution_province', $regProfile['institution_province'] ?? '') === 'lampung' ? 'selected' : '' ?>>Lampung</option>
            <option value="lainnya" <?= old('institution_province', $regProfile['institution_province'] ?? '') === 'lainnya' ? 'selected' : '' ?>>Provinsi Lainnya</option>
        </select>

        <div id="asn-luar-region-fields" class="d-none">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten/Kota Instansi</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_regency"
                   value="<?= esc(old('institution_regency', $regProfile['institution_regency'] ?? '')) ?>"
                   placeholder="Kabupaten/kota instansi">

            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan/Desa Instansi</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_district_village"
                   value="<?= esc(old('institution_district_village', $regProfile['institution_district_village'] ?? '')) ?>"
                   placeholder="Diisi bila diperlukan">
        </div>
    </div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
        Jabatan Utama <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="position"
           value="<?= esc(old('position', $regProfile['position'] ?? '')) ?>"
           placeholder="Contoh: Pengelola Pengadaan Barang/Jasa Ahli Muda" required>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pangkat/Golongan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="rank"
           value="<?= esc(old('rank', $regProfile['rank'] ?? '')) ?>"
           placeholder="Contoh: Pembina / IV-a atau Penata Muda / III-a">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Kedinasan <span class="text-secondary-light fw-normal">(Disarankan)</span></label>
    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="official_email"
           value="<?= esc(old('official_email', $regProfile['official_email'] ?? '')) ?>"
           placeholder="nama@lampungprov.go.id atau instansi.go.id">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Memiliki Peran Pengadaan?</label>
    <div class="d-flex gap-20 mb-16">
        <div class="form-check style-check d-flex align-items-center">
            <input class="form-check-input border border-neutral-300" type="radio" name="has_procurement_role" value="yes" id="procurement-role-yes" <?= $hasProcRole === 'yes' ? 'checked' : '' ?>>
            <label class="form-check-label" for="procurement-role-yes">Ya</label>
        </div>
        <div class="form-check style-check d-flex align-items-center">
            <input class="form-check-input border border-neutral-300" type="radio" name="has_procurement_role" value="no" id="procurement-role-no" <?= $hasProcRole !== 'yes' ? 'checked' : '' ?>>
            <label class="form-check-label" for="procurement-role-no">Tidak</label>
        </div>
    </div>

    <!-- Section Peran Pengadaan (Terhubung langsung ke Master Peran Database) -->
    <div id="procurement-role-fields" class="d-none border border-neutral-200 radius-12 p-16 mb-16 bg-neutral-50">
        <div class="d-flex align-items-center justify-content-between mb-12">
            <p class="fw-semibold text-primary-light text-sm mb-0">Pilih Peran Pengadaan yang Dimiliki / Ditugaskan</p>
            <span class="badge bg-primary-50 text-primary-600 border border-primary-100 text-xs px-8 py-2 radius-4">Database Peran PBJ</span>
        </div>
        <p class="text-secondary-light text-xs mb-16">
            Pilihlah satu atau lebih peran pengadaan sesuai surat keputusan (SK) atau penugasan Anda. Peran yang dipilih akan diverifikasi oleh Operator/Admin.
        </p>
        <div class="row gy-12">
            <?php foreach (($procurementRoles ?? []) as $role): ?>
                <?php
                $roleIdStr = (string) $role['id'];
                $roleKeyStr = (string) ($role['key'] ?? '');
                $isChecked = in_array($roleIdStr, $selectedRoles, true) || in_array($roleKeyStr, $selectedRoles, true);
                ?>
                <div class="col-md-6">
                    <div class="p-12 radius-8 bg-white border border-neutral-200 h-100">
                        <div class="form-check style-check d-flex align-items-start gap-2">
                            <input class="form-check-input border border-neutral-300 mt-4 flex-shrink-0"
                                   type="checkbox"
                                   name="procurement_roles[]"
                                   value="<?= esc($role['id']) ?>"
                                   id="role-<?= esc($role['id']) ?>"
                                   <?= $isChecked ? 'checked' : '' ?>>
                            <label class="form-check-label text-sm flex-grow-1" for="role-<?= esc($role['id']) ?>">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <strong class="text-dark"><?= esc($role['name']) ?></strong>
                                    <?php if (!empty($role['key'])): ?>
                                        <span class="badge bg-neutral-100 text-secondary-light border border-neutral-200 text-xs px-6 py-0 radius-4"><?= esc($role['key']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($role['description'])): ?>
                                    <small class="text-secondary-light text-xs d-block"><?= esc(mb_strimwidth($role['description'], 0, 85, '...')) ?></small>
                                <?php endif; ?>
                            </label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a>
        <button type="submit" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Tinjau Data</button>
    </div>

    <p class="text-secondary-light text-xs mt-20 mb-0">NIP/NIPPK dan data kepegawaian adalah data restricted. Tidak ada upload SK atau bukti penugasan pada registrasi awal.</p>
</form>
<?= view('dashboard/auth/components/restore_registration_profile', ['registrationProfile' => $registrationProfile ?? []]) ?>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const asnOrigin = document.querySelector('#asn-origin');
        const pemprovFields = document.querySelector('#asn-pemprov-fields');
        const luarFields = document.querySelector('#asn-luar-fields');
        const luarRegionFields = document.querySelector('#asn-luar-region-fields');
        const procurementRoles = document.querySelector('#procurement-role-fields');

        function updateAsnOriginFields() {
            if (!asnOrigin) return;
            const isPemprov = asnOrigin.value === 'pemprov_lampung';
            const hasExternalOrigin = asnOrigin.value !== '' && !isPemprov;
            const needsRegionalFields = ['pemerintah_daerah_lain', 'pemerintah_desa'].includes(asnOrigin.value);

            if (pemprovFields) pemprovFields.classList.toggle('d-none', !isPemprov);
            if (luarFields) luarFields.classList.toggle('d-none', !hasExternalOrigin);
            if (luarRegionFields) luarRegionFields.classList.toggle('d-none', !needsRegionalFields);
        }

        function updateProcurementRoles() {
            const checked = document.querySelector('input[name="has_procurement_role"]:checked');
            if (procurementRoles) {
                procurementRoles.classList.toggle('d-none', !checked || checked.value !== 'yes');
            }
        }

        asnOrigin?.addEventListener('change', updateAsnOriginFields);
        document.querySelectorAll('input[name="has_procurement_role"]').forEach((input) => {
            input.addEventListener('change', updateProcurementRoles);
        });

        // Trigger on initial page load / restored state
        updateAsnOriginFields();
        updateProcurementRoles();
    });
</script>
<?= $this->endSection() ?>