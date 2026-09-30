<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Lengkapi Profil ASN') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 3, 'currentKey' => $currentKey ?? 'profil']) ?>

<form action="#" method="post" id="asn-register-form">
    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Asal Instansi ASN</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-20" name="asn_origin" id="asn-origin">
        <option value="">Pilih asal instansi</option>
        <option value="pemprov_lampung">ASN Pemprov Lampung</option>
        <option value="kementerian_lembaga">ASN Kementerian/Lembaga</option>
        <option value="pemerintah_daerah_lain">ASN Pemerintah Daerah Lain</option>
        <option value="pemerintah_desa">ASN Pemerintah Desa</option>
        <option value="bumn_bumd">ASN BUMN/BUMD</option>
        <option value="lainnya">ASN Instansi Lainnya</option>
    </select>

    <div class="icon-field mb-16">
        <span class="icon top-50 translate-middle-y"><iconify-icon icon="solar:card-linear"></iconify-icon></span>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" name="nip_nippk" placeholder="NIP atau NIPPK">
    </div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Kepegawaian</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="employment_status">
        <option value="">Pilih status kepegawaian</option>
        <option value="pns">PNS</option>
        <option value="pppk">PPPK</option>
    </select>

    <div id="asn-pemprov-fields" class="d-none">
        <div class="mb-16">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Instansi Asal</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" value="Pemerintah Daerah Provinsi Lampung" readonly>
        </div>
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">OPD/Unit Kerja</label>
        <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="organization_unit_id">
            <option value="">Pilih OPD/unit kerja</option>
            <option value="biro-pbj">Biro Pengadaan Barang dan Jasa</option>
            <option value="dinas-pu">Dinas Pekerjaan Umum</option>
            <option value="dinas-kesehatan">Dinas Kesehatan</option>
            <option value="dinas-pendidikan">Dinas Pendidikan</option>
        </select>
    </div>

    <div id="asn-luar-fields" class="d-none">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Instansi Induk</label>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_name" placeholder="Nama instansi induk">

        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama OPD/Unit Kerja</label>
        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="external_unit_name" placeholder="Nama OPD atau unit kerja">

        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi Instansi</label>
        <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="institution_province">
            <option value="">Pilih provinsi instansi</option>
            <option value="lampung">Lampung</option>
            <option value="lainnya">Provinsi lainnya</option>
        </select>

        <div id="asn-luar-region-fields" class="d-none">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten/Kota Instansi</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_regency" placeholder="Kabupaten/kota instansi">

            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan/Desa Instansi</label>
            <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="institution_district_village" placeholder="Diisi bila diperlukan">
        </div>
    </div>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan Utama</label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="position" placeholder="Contoh: Analis Pengadaan Barang/Jasa">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pangkat/Golongan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <input type="text" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="rank" placeholder="Contoh: Pembina / IV-a">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email Kedinasan <span class="text-secondary-light fw-normal">(Disarankan)</span></label>
    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12 mb-16" name="official_email" placeholder="nama@instansi.go.id">

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Memiliki Peran Pengadaan?</label>
    <div class="d-flex gap-20 mb-16">
        <div class="form-check style-check d-flex align-items-center"><input class="form-check-input border border-neutral-300" type="radio" name="has_procurement_role" value="yes" id="procurement-role-yes"><label class="form-check-label" for="procurement-role-yes">Ya</label></div>
        <div class="form-check style-check d-flex align-items-center"><input class="form-check-input border border-neutral-300" type="radio" name="has_procurement_role" value="no" id="procurement-role-no"><label class="form-check-label" for="procurement-role-no">Tidak</label></div>
    </div>

    <div id="procurement-role-fields" class="d-none border border-neutral-200 radius-12 p-16 mb-16">
        <p class="fw-semibold text-primary-light text-sm mb-12">Pilih Peran Pengadaan</p>
        <div class="row gy-10">
            <?php foreach (
                [
                    'PA' => 'Pengguna Anggaran',
                    'KPA' => 'Kuasa Pengguna Anggaran',
                    'PPK' => 'Pejabat Pembuat Komitmen',
                    'PP' => 'Pejabat Pengadaan',
                    'POKJA_PEMILIHAN' => 'Kelompok Kerja Pemilihan',
                    'PENGELOLA_PENGADAAN' => 'Pengelola PBJ',
                    'PPTK' => 'Pejabat Pelaksana Teknis Kegiatan',
                    'PEJABAT_PENERIMA_HASIL' => 'Pejabat/Panitia Penerima Hasil Pekerjaan',
                    'TIM_TEKNIS' => 'Tim Teknis',
                    'TIM_PENDUKUNG' => 'Tim Pendukung',
                    'PENGAWAS_KONTRAK' => 'Pengawas Kontrak',
                    'APIP' => 'Aparat Pengawasan Intern Pemerintah',
                    'LAINNYA' => 'Peran Lainnya',
                ] as $value => $label
            ): ?>
                <div class="col-md-6">
                    <div class="form-check style-check d-flex align-items-start"><input class="form-check-input border border-neutral-300 mt-4" type="checkbox" name="procurement_roles[]" value="<?= esc($value) ?>" id="role-<?= esc(strtolower($value)) ?>"><label class="form-check-label text-sm" for="role-<?= esc(strtolower($value)) ?>"><?= esc($label) ?></label></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a>
        <a href="<?= site_url('dashboard/daftar/tinjau') ?>" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 text-center">Tinjau Data</a>
    </div>

    <p class="text-secondary-light text-xs mt-20 mb-0">NIP/NIPPK dan data kepegawaian adalah data restricted. Tidak ada upload SK atau bukti penugasan pada registrasi awal.</p>
</form>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
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

        pemprovFields.classList.toggle('d-none', !isPemprov);
        luarFields.classList.toggle('d-none', !hasExternalOrigin);
        luarRegionFields.classList.toggle('d-none', !needsRegionalFields);
    }

    function updateProcurementRoles() {
        const checked = document.querySelector('input[name="has_procurement_role"]:checked');
        procurementRoles.classList.toggle('d-none', !checked || checked.value !== 'yes');
    }

    asnOrigin?.addEventListener('change', updateAsnOriginFields);
    document.querySelectorAll('input[name="has_procurement_role"]').forEach((input) => {
        input.addEventListener('change', updateProcurementRoles);
    });
</script>
<?= $this->endSection() ?>