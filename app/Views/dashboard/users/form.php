<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$user = $user ?? $item ?? [];
$userId = $userId ?? ($user['id'] ?? null);
$formMode = $formMode ?? 'create';
$errors = session('errors') ?? [];
$isEdit = $formMode === 'edit';
$actionUrl = $isEdit
    ? site_url('dashboard/akses/pengguna/' . $userId . '/ubah')
    : site_url('dashboard/akses/pengguna/tambah');

$asnProfile        = $asnProfile ?? [];
$providerProfile   = $providerProfile ?? [];
$academicProfile   = $academicProfile ?? [];
$publicProfile     = $publicProfile ?? [];
$userProfile       = $userProfile ?? [];
$organizationUnits = $organizationUnits ?? [];

// Helper untuk mengambil value json array / string dengan aman
$decodeJsonString = static function ($val): string {
    if (is_array($val)) {
        return implode(', ', $val);
    }
    if (is_string($val) && str_starts_with(trim($val), '[')) {
        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return implode(', ', $decoded);
        }
    }
    return (string) ($val ?? '');
};

$currentProfileType = old('profile_type', $user['profile_type'] ?? 'asn');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? ($isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna')) ?></h6>
        <p class="text-secondary-light mb-0">Kelola akun pengguna, hak akses peran, dan detail profil pengadaan spesifik.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar Pengguna</span>
        </a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger mb-20 radius-8 p-16">
        <div class="d-flex align-items-start gap-2">
            <iconify-icon icon="solar:danger-circle-outline" class="text-xl flex-shrink-0 mt-2"></iconify-icon>
            <div>
                <strong class="d-block mb-4">Terdapat kesalahan pengisian formulir:</strong>
                <ul class="mb-0 ps-16">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger mb-20 radius-8 p-16 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:danger-circle-outline" class="text-xl flex-shrink-0"></iconify-icon>
        <span><?= esc(session('error')) ?></span>
    </div>
<?php endif; ?>

<form action="<?= esc($actionUrl) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- ========================================================================= -->
    <!-- CARD 1: INFORMASI AKUN DASAR & HAK AKSES                                  -->
    <!-- ========================================================================= -->
    <div class="card mb-24 radius-12 border">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-lg">
                    <iconify-icon icon="solar:user-circle-bold"></iconify-icon>
                </div>
                <div>
                    <h6 class="text-md mb-0"><?= $isEdit ? 'Formulir Perubahan Data Pengguna' : 'Formulir Pembuatan Akun Baru' ?></h6>
                    <small class="text-secondary-light">Kredensial login, peran platform, dan penentuan tipe profil.</small>
                </div>
            </div>
            <span class="badge bg-neutral-100 text-secondary-dark px-10 py-4 radius-6 text-xs fw-semibold">
                <?= $isEdit ? 'ID #' . (int) $userId : 'Akun Baru' ?>
            </span>
        </div>

        <div class="card-body p-24">
            <div class="row gy-20">
                <!-- Username -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Username <span class="text-danger-main">*</span>
                    </label>
                    <input name="username" required type="text" class="form-control radius-8"
                           value="<?= esc(old('username', $user['username'] ?? '')) ?>"
                           placeholder="contoh: joko_pbj">
                    <span class="text-secondary-light text-xs mt-4 d-block">Minimal 3 karakter tanpa spasi.</span>
                </div>

                <!-- Full Name -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Nama Lengkap <span class="text-danger-main">*</span>
                    </label>
                    <input name="name" required type="text" class="form-control radius-8"
                           value="<?= esc(old('name', $user['name'] ?? '')) ?>"
                           placeholder="Nama lengkap beserta gelar (jika ada)">
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Alamat Email <span class="text-danger-main">*</span>
                    </label>
                    <input name="email" required type="email" class="form-control radius-8"
                           value="<?= esc(old('email', $user['email'] ?? '')) ?>"
                           placeholder="nama@domain.go.id atau instansi/perusahaan">
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Kata Sandi <?= $isEdit ? '<span class="text-secondary-light fw-normal">(kosongkan jika tidak diubah)</span>' : '<span class="text-danger-main">*</span>' ?>
                    </label>
                    <input name="password" <?= $isEdit ? '' : 'required' ?> type="password" class="form-control radius-8" minlength="12"
                           placeholder="Minimal 12 karakter kombinasi huruf besar, kecil, angka">
                    <span class="text-secondary-light text-xs mt-4 d-block">Gunakan kombinasi minimal 12 karakter untuk standar keamanan tinggi.</span>
                </div>

                <!-- Profile Type Selector -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        <iconify-icon icon="solar:user-id-bold" class="text-primary-600 me-1"></iconify-icon>
                        Tipe Profil <span class="text-danger-main">*</span>
                    </label>
                    <select name="profile_type" id="field_profile_type" required class="form-select radius-8 fw-semibold border-primary-300">
                        <option value="asn" <?= $currentProfileType === 'asn' ? 'selected' : '' ?>>Aparatur Sipil Negara (ASN)</option>
                        <option value="provider" <?= $currentProfileType === 'provider' ? 'selected' : '' ?>>Penyedia Barang/Jasa</option>
                        <option value="academic" <?= $currentProfileType === 'academic' ? 'selected' : '' ?>>Akademisi / Peneliti</option>
                        <option value="public" <?= $currentProfileType === 'public' ? 'selected' : '' ?>>Masyarakat Umum</option>
                    </select>
                    <span class="text-primary-600 text-xs mt-4 d-block fw-medium">
                        <iconify-icon icon="solar:info-circle-outline" class="me-1"></iconify-icon>
                        Mengubah tipe profil akan otomatis menyesuaikan formulir isian di bawah.
                    </span>
                </div>

                <!-- Account Status -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Status Akun <span class="text-danger-main">*</span>
                    </label>
                    <select name="account_status" required class="form-select radius-8">
                        <?php foreach ([
                            'active'               => 'Aktif (Dapat Mengakses Platform)',
                            'pending_approval'     => 'Menunggu Persetujuan Operator',
                            'pending_verification' => 'Menunggu Verifikasi Profil',
                            'draft'                => 'Draft',
                            'inactive'             => 'Tidak Aktif',
                            'suspended'            => 'Ditangguhkan (Suspended)',
                        ] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('account_status', $user['account_status'] ?? 'active') === $value ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Role Assignment -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Role Akses Platform
                    </label>
                    <select name="role_id" class="form-select radius-8">
                        <option value="">— Tanpa Role Spesifik (Default Peserta) —</option>
                        <?php foreach (($roles ?? []) as $role): ?>
                            <option value="<?= (int) $role['id'] ?>" <?= (string) old('role_id', $user['role_id'] ?? '') === (string) $role['id'] ? 'selected' : '' ?>>
                                <?= esc($role['name']) ?> (<?= esc($role['key']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-secondary-light text-xs mt-4 d-block">Hak akses RBAC platform untuk akun ini.</span>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Nomor Kontak / WhatsApp
                    </label>
                    <input name="phone" type="text" class="form-control radius-8"
                           value="<?= esc(old('phone', $user['phone'] ?? '')) ?>"
                           placeholder="0812xxxxxxxx">
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CARD 2: INFORMASI PROFIL KHUSUS BERDASARKAN TIPE PROFIL (DYNAMIC SWITCH) -->
    <!-- ========================================================================= -->
    <div class="card mb-24 radius-12 border">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-lg">
                    <iconify-icon icon="solar:diploma-verified-bold"></iconify-icon>
                </div>
                <div>
                    <h6 class="text-md mb-0">2. Informasi Profil Khusus Pengadaan</h6>
                    <small class="text-secondary-light">Formulir otomatis berganti dan menyesuaikan dengan tipe profil yang dipilih.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-secondary-light">Profil Aktif:</span>
                <span id="active-profile-badge" class="badge bg-primary-50 text-primary-700 border border-primary-200 px-10 py-5 radius-6 text-xs fw-bold">
                    Aparatur Sipil Negara (ASN)
                </span>
            </div>
        </div>

        <div class="card-body p-24">
            <!-- ---------------------------------------------------- -->
            <!-- SECTION A: PROFIL ASN                                -->
            <!-- ---------------------------------------------------- -->
            <div id="section-profile-asn" class="profile-section <?= $currentProfileType === 'asn' ? '' : 'd-none' ?>">
                <div class="alert alert-primary bg-primary-50 text-primary-900 border-primary-200 radius-8 p-16 mb-20 d-flex align-items-center gap-12">
                    <iconify-icon icon="solar:shield-user-outline" class="text-primary-600 text-2xl flex-shrink-0"></iconify-icon>
                    <div class="text-sm">
                        <strong>Profil Pegawai ASN:</strong> Terintegrasi dengan Satuan Kerja (OPD) dan unit kerja Pemerintah Daerah Provinsi Lampung atau instansi pemerintah terkait.
                    </div>
                </div>

                <div class="row gy-20">
                    <!-- NIP / Employee Identifier -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            NIP / Nomor Induk Pegawai
                        </label>
                        <input name="employee_identifier" type="text" class="form-control radius-8"
                               value="<?= esc(old('employee_identifier', $asnProfile['employee_identifier'] ?? '')) ?>"
                               placeholder="198701012010011001 (18 digit)">
                    </div>

                    <!-- Employment Status -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Status Kepegawaian
                        </label>
                        <?php $empStatus = old('employment_status', $asnProfile['employment_status'] ?? 'pns'); ?>
                        <select name="employment_status" class="form-select radius-8">
                            <option value="pns" <?= $empStatus === 'pns' ? 'selected' : '' ?>>Pegawai Negeri Sipil (PNS)</option>
                            <option value="p3k" <?= $empStatus === 'p3k' ? 'selected' : '' ?>>Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)</option>
                            <option value="non_asn" <?= $empStatus === 'non_asn' ? 'selected' : '' ?>>Non-ASN / Tenaga Kontrak / Honorer</option>
                        </select>
                    </div>

                    <!-- Asal Instansi (Origin Type) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Asal Instansi (Origin Type) <span class="text-danger-main">*</span>
                        </label>
                        <?php $originType = old('origin_type', $asnProfile['origin_type'] ?? 'pemprov_lampung'); ?>
                        <select name="origin_type" class="form-select radius-8">
                            <option value="pemprov_lampung" <?= $originType === 'pemprov_lampung' ? 'selected' : '' ?>>Pemerintah Daerah Provinsi Lampung</option>
                            <option value="kementerian_lembaga" <?= $originType === 'kementerian_lembaga' ? 'selected' : '' ?>>Kementerian / Lembaga Pemerintah Non-Kementerian</option>
                            <option value="pemerintah_daerah_lain" <?= $originType === 'pemerintah_daerah_lain' ? 'selected' : '' ?>>Pemerintah Daerah Lain (Kabupaten / Kota / Provinsi Lain)</option>
                            <option value="pemerintah_desa" <?= $originType === 'pemerintah_desa' ? 'selected' : '' ?>>Pemerintah Desa</option>
                            <option value="bumn_bumd" <?= $originType === 'bumn_bumd' ? 'selected' : '' ?>>BUMN / BUMD</option>
                            <option value="lainnya" <?= $originType === 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <!-- Dropdown OPD Pemprov Lampung (Organization Unit) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Pilihan OPD Pemprov Lampung
                        </label>
                        <?php $currentOuId = (int) old('organization_unit_id', $asnProfile['organization_unit_id'] ?? 0); ?>
                        <select name="organization_unit_id" id="asn_select_opd" class="form-select radius-8">
                            <option value="">— Pilih OPD Pemprov Lampung —</option>
                            <?php foreach ($organizationUnits as $ou): ?>
                                <option value="<?= (int) $ou['id'] ?>" data-name="<?= esc($ou['name']) ?>" <?= $currentOuId === (int) $ou['id'] ? 'selected' : '' ?>>
                                    <?= esc($ou['name']) ?> (<?= esc($ou['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nama Unit Kerja / OPD (Teks) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nama Satuan Kerja / Unit Kerja (unit_name)
                        </label>
                        <input name="unit_name" id="asn_input_unit_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('unit_name', $asnProfile['unit_name'] ?? '')) ?>"
                               placeholder="Biro Pengadaan Barang dan Jasa / Dinas...">
                        <span class="text-secondary-light text-xs mt-4 d-block">Terisi otomatis saat memilih OPD di samping, atau ketik nama instansi Anda.</span>
                    </div>

                    <!-- Nama Lembaga / Instansi Induk -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nama Lembaga / Instansi Induk
                        </label>
                        <input name="institution_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('institution_name', $asnProfile['institution_name'] ?? 'Pemerintah Provinsi Lampung')) ?>"
                               placeholder="Pemerintah Provinsi Lampung">
                    </div>

                    <!-- Position Name (Jabatan) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Jabatan / Penugasan PBJ
                        </label>
                        <input name="position_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('position_name', $asnProfile['position_name'] ?? '')) ?>"
                               placeholder="Pejabat Pembuat Komitmen (PPK) / Pengelola PBJ...">
                    </div>

                    <!-- Rank / Grade (Pangkat Golongan) -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Pangkat / Golongan
                        </label>
                        <input name="rank_grade" type="text" class="form-control radius-8"
                               value="<?= esc(old('rank_grade', $asnProfile['rank_grade'] ?? '')) ?>"
                               placeholder="Penata Muda (III/a)">
                    </div>

                    <!-- Official Email -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Email Kedinasan Resmi
                        </label>
                        <input name="official_email" type="email" class="form-control radius-8"
                               value="<?= esc(old('official_email', $asnProfile['official_email'] ?? '')) ?>"
                               placeholder="pegawai@lampungprov.go.id">
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- SECTION B: PROFIL PENYEDIA (PROVIDER)                -->
            <!-- ---------------------------------------------------- -->
            <div id="section-profile-provider" class="profile-section <?= $currentProfileType === 'provider' ? '' : 'd-none' ?>">
                <div class="alert alert-info bg-info-50 text-info-900 border-info-200 radius-8 p-16 mb-20 d-flex align-items-center gap-12">
                    <iconify-icon icon="solar:shop-2-outline" class="text-info-600 text-2xl flex-shrink-0"></iconify-icon>
                    <div class="text-sm">
                        <strong>Profil Penyedia Barang/Jasa:</strong> Digunakan untuk badan usaha, rekanan tender, e-katalog, dan pelaku usaha PBJ.
                    </div>
                </div>

                <div class="row gy-20">
                    <!-- Business Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nama Perusahaan / Badan Usaha
                        </label>
                        <input name="business_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('business_name', $providerProfile['business_name'] ?? '')) ?>"
                               placeholder="PT Lampung Mega Konstruksi">
                    </div>

                    <!-- Business Form -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Bentuk Usaha
                        </label>
                        <?php $bForm = old('business_form', $providerProfile['business_form'] ?? 'PT'); ?>
                        <select name="business_form" class="form-select radius-8">
                            <option value="PT" <?= $bForm === 'PT' ? 'selected' : '' ?>>Perseroan Terbatas (PT)</option>
                            <option value="CV" <?= $bForm === 'CV' ? 'selected' : '' ?>>Commanditaire Vennootschap (CV)</option>
                            <option value="Firma" <?= $bForm === 'Firma' ? 'selected' : '' ?>>Firma</option>
                            <option value="Koperasi" <?= $bForm === 'Koperasi' ? 'selected' : '' ?>>Koperasi</option>
                            <option value="Perorangan" <?= $bForm === 'Perorangan' ? 'selected' : '' ?>>Usaha Perorangan (UD/PO)</option>
                            <option value="Lainnya" <?= $bForm === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <!-- Business Identifier (NIB/NPWP) -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nomor Induk Berusaha (NIB)
                        </label>
                        <input name="business_identifier" type="text" class="form-control radius-8"
                               value="<?= esc(old('business_identifier', $providerProfile['business_identifier'] ?? '')) ?>"
                               placeholder="13 digit NIB / NPWP">
                    </div>

                    <!-- Business Field -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Bidang Usaha / Klasifikasi (KBLI)
                        </label>
                        <input name="business_field" type="text" class="form-control radius-8"
                               value="<?= esc(old('business_field', $providerProfile['business_field'] ?? '')) ?>"
                               placeholder="Konstruksi Gedung, IT Solution, Pengadaan ATK...">
                    </div>

                    <!-- Registrant Position -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Jabatan Pengguna
                        </label>
                        <input name="registrant_position" type="text" class="form-control radius-8"
                               value="<?= esc(old('registrant_position', $providerProfile['registrant_position'] ?? 'Direktur')) ?>"
                               placeholder="Direktur Utama / PIC Tender">
                    </div>

                    <!-- Responsible Person -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Penanggung Jawab Perusahaan
                        </label>
                        <input name="responsible_person" type="text" class="form-control radius-8"
                               value="<?= esc(old('responsible_person', $providerProfile['responsible_person'] ?? '')) ?>"
                               placeholder="Nama Pimpinan Badan Usaha">
                    </div>

                    <!-- Business Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Email Resmi Perusahaan
                        </label>
                        <input name="business_email" type="email" class="form-control radius-8"
                               value="<?= esc(old('business_email', $providerProfile['business_email'] ?? '')) ?>"
                               placeholder="corporate@ptlampung.co.id">
                    </div>

                    <!-- Business Address -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Alamat Kantor Perusahaan
                        </label>
                        <input name="business_address" type="text" class="form-control radius-8"
                               value="<?= esc(old('business_address', $providerProfile['business_address'] ?? '')) ?>"
                               placeholder="Jl. Soekarno-Hatta No. 45, Bandar Lampung">
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- SECTION C: PROFIL AKADEMISI / PENELITI               -->
            <!-- ---------------------------------------------------- -->
            <div id="section-profile-academic" class="profile-section <?= $currentProfileType === 'academic' ? '' : 'd-none' ?>">
                <div class="alert alert-success bg-success-50 text-success-900 border-success-200 radius-8 p-16 mb-20 d-flex align-items-center gap-12">
                    <iconify-icon icon="solar:diploma-outline" class="text-success-600 text-2xl flex-shrink-0"></iconify-icon>
                    <div class="text-sm">
                        <strong>Profil Akademisi / Peneliti:</strong> Dikhususkan bagi dosen, peneliti, dan akademisi yang terlibat dalam kajian regulasi dan keilmuan PBJ.
                    </div>
                </div>

                <div class="row gy-20">
                    <!-- Institution Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nama Universitas / Institusi Pendidikan
                        </label>
                        <input name="institution_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('institution_name', $academicProfile['institution_name'] ?? 'Universitas Lampung')) ?>"
                               placeholder="Universitas Lampung / Itera...">
                    </div>

                    <!-- Institution Type -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Jenis Institusi
                        </label>
                        <?php $instType = old('institution_type', $academicProfile['institution_type'] ?? 'Perguruan Tinggi Negeri'); ?>
                        <select name="institution_type" class="form-select radius-8">
                            <option value="Perguruan Tinggi Negeri" <?= $instType === 'Perguruan Tinggi Negeri' ? 'selected' : '' ?>>Perguruan Tinggi Negeri (PTN)</option>
                            <option value="Perguruan Tinggi Swasta" <?= $instType === 'Perguruan Tinggi Swasta' ? 'selected' : '' ?>>Perguruan Tinggi Swasta (PTS)</option>
                            <option value="Lembaga Riset / Balitbang" <?= $instType === 'Lembaga Riset / Balitbang' ? 'selected' : '' ?>>Lembaga Riset / Balitbang</option>
                            <option value="Lainnya" <?= $instType === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <!-- Role Name -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Peran / Profesi
                        </label>
                        <?php $acadRole = old('role_name', $academicProfile['role_name'] ?? 'Dosen'); ?>
                        <select name="role_name" class="form-select radius-8">
                            <option value="Dosen" <?= $acadRole === 'Dosen' ? 'selected' : '' ?>>Dosen / Pengajar</option>
                            <option value="Peneliti" <?= $acadRole === 'Peneliti' ? 'selected' : '' ?>>Peneliti (Researcher)</option>
                            <option value="Mahasiswa" <?= $acadRole === 'Mahasiswa' ? 'selected' : '' ?>>Mahasiswa (S1/S2/S3)</option>
                            <option value="Lainnya" <?= $acadRole === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <!-- Professional Identifier (NIDN/NIM) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nomor Identitas Akademik (NIDN / NIP / NIM)
                        </label>
                        <input name="professional_identifier" type="text" class="form-control radius-8"
                               value="<?= esc(old('professional_identifier', $academicProfile['professional_identifier'] ?? '')) ?>"
                               placeholder="NIDN atau nomor registrasi akademisi">
                    </div>

                    <!-- Expertise -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Bidang Keahlian / Fokus Riset
                        </label>
                        <input name="expertise" type="text" class="form-control radius-8"
                               value="<?= esc(old('expertise', $decodeJsonString($academicProfile['expertise'] ?? '')) ?: '') ?>"
                               placeholder="Hukum Kontrak Pengadaan, Logistik Publik, Manajemen Risiko">
                    </div>

                    <!-- Institution Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Email Kampus / Institusi
                        </label>
                        <input name="institution_email" type="email" class="form-control radius-8"
                               value="<?= esc(old('institution_email', $academicProfile['institution_email'] ?? '')) ?>"
                               placeholder="dosen@unila.ac.id">
                    </div>

                    <!-- Portfolio URL -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Tautan Profil / Google Scholar / Sinta
                        </label>
                        <input name="portfolio_url" type="url" class="form-control radius-8"
                               value="<?= esc(old('portfolio_url', $academicProfile['portfolio_url'] ?? '')) ?>"
                               placeholder="https://scholar.google.com/citations?user=...">
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- SECTION D: PROFIL MASYARAKAT UMUM (PUBLIC)           -->
            <!-- ---------------------------------------------------- -->
            <div id="section-profile-public" class="profile-section <?= $currentProfileType === 'public' ? '' : 'd-none' ?>">
                <div class="alert alert-warning bg-warning-50 text-warning-900 border-warning-200 radius-8 p-16 mb-20 d-flex align-items-center gap-12">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="text-warning-600 text-2xl flex-shrink-0"></iconify-icon>
                    <div class="text-sm">
                        <strong>Profil Masyarakat Umum:</strong> Akses partisipatif untuk masyarakat, profesional independen, dan pemerhati pengadaan.
                    </div>
                </div>

                <div class="row gy-20">
                    <!-- National Identifier (NIK) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nomor Induk Kependudukan (NIK)
                        </label>
                        <input name="national_identifier" type="text" maxlength="16" class="form-control radius-8"
                               value="<?= esc(old('national_identifier', $publicProfile['national_identifier'] ?? '')) ?>"
                               placeholder="16 digit NIK KTP">
                    </div>

                    <!-- Occupation Status -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Status Pekerjaan / Profesi
                        </label>
                        <input name="occupation_status" type="text" class="form-control radius-8"
                               value="<?= esc(old('occupation_status', $publicProfile['occupation_status'] ?? 'Karyawan Swasta')) ?>"
                               placeholder="Karyawan Swasta / Wiraswasta / Profesional Mandiri">
                    </div>

                    <!-- Organization Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Nama Lembaga / Komunitas / Tempat Kerja
                        </label>
                        <input name="organization_name" type="text" class="form-control radius-8"
                               value="<?= esc(old('organization_name', $publicProfile['organization_name'] ?? '')) ?>"
                               placeholder="Nama komunitas / kantor tempat bekerja saat ini">
                    </div>

                    <!-- Interest Areas -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Bidang Minat Pengadaan
                        </label>
                        <input name="interest_areas" type="text" class="form-control radius-8"
                               value="<?= esc(old('interest_areas', $decodeJsonString($publicProfile['interest_areas'] ?? '')) ?: '') ?>"
                               placeholder="Pengadaan Barang, Jasa Konstruksi, E-Katalog...">
                    </div>

                    <!-- Usage Purpose -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                            Tujuan Pemanfaatan Platform
                        </label>
                        <input name="usage_purpose" type="text" class="form-control radius-8"
                               value="<?= esc(old('usage_purpose', $decodeJsonString($publicProfile['usage_purpose'] ?? '')) ?: '') ?>"
                               placeholder="Pembelajaran mandiri PBJ, persiapan sertifikasi, pemantauan keterbukaan regulasi...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $provinces = $provinces ?? (new \App\Models\WilayahModel())->provinces();
    $selectedProv = (string) old('province_code', old('province', $userProfile['province_code'] ?? ''));
    $selectedReg  = (string) old('regency_code', old('regency', $userProfile['regency_code'] ?? ''));
    $selectedDist = (string) old('district_code', old('district', $userProfile['district_code'] ?? ''));
    $selectedVill = (string) old('village_code', old('village', $userProfile['village_code'] ?? ''));
    $selectedAddr = (string) old('address_line', old('address', $userProfile['address_line'] ?? ''));
    ?>
    <!-- ========================================================================= -->
    <!-- CARD 3: DOMISILI & ALAMAT LENGKAP                                         -->
    <!-- ========================================================================= -->
    <div class="card mb-24 radius-12 border">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center gap-2">
            <div class="w-32-px h-32-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-lg">
                <iconify-icon icon="solar:map-point-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="text-md mb-0">3. Domisili &amp; Alamat Lengkap</h6>
                <small class="text-secondary-light">Pilihan wilayah administratif berjenjang (Provinsi, Kab/Kota, Kecamatan, Desa/Kelurahan) dan rincian alamat.</small>
            </div>
        </div>

        <div class="card-body p-24">
            <div class="row gy-20">
                <!-- Provinsi -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="province_code">
                        Provinsi <span class="text-secondary-light fw-normal">(Pilih untuk memuat kabupaten/kota)</span>
                    </label>
                    <select class="form-select radius-8" name="province_code" id="province_code" data-selected="<?= esc($selectedProv) ?>">
                        <option value="">Pilih provinsi</option>
                        <?php foreach (($provinces ?? []) as $prov): ?>
                            <option value="<?= esc($prov['kode']) ?>" <?= $selectedProv === (string) $prov['kode'] ? 'selected' : '' ?>>
                                <?= esc($prov['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kabupaten / Kota -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="regency_code">
                        Kabupaten / Kota
                    </label>
                    <select class="form-select radius-8" name="regency_code" id="regency_code" data-selected="<?= esc($selectedReg) ?>" disabled>
                        <option value="">Pilih kabupaten/kota</option>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="district_code">
                        Kecamatan
                    </label>
                    <select class="form-select radius-8" name="district_code" id="district_code" data-selected="<?= esc($selectedDist) ?>" disabled>
                        <option value="">Pilih kecamatan</option>
                    </select>
                </div>

                <!-- Desa / Kelurahan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="village_code">
                        Desa / Kelurahan <span class="text-secondary-light fw-normal">(Opsional)</span>
                    </label>
                    <select class="form-select radius-8" name="village_code" id="village_code" data-selected="<?= esc($selectedVill) ?>" disabled>
                        <option value="">Pilih desa/kelurahan</option>
                    </select>
                </div>

                <!-- Alamat Domisili Lengkap -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="address_line">
                        Alamat Domisili Lengkap / Ringkas
                    </label>
                    <textarea name="address_line" id="address_line" rows="3" class="form-control radius-8"
                              placeholder="Nama jalan, nomor rumah, RT/RW, gedung, lingkungan, atau catatan domisili lainnya..."><?= esc($selectedAddr) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card p-20 radius-12 border mb-24">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-secondary-light text-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:shield-check-outline" class="text-success-600 text-xl"></iconify-icon>
                <span>Perubahan profil akan terekam di log audit sistem. Profil lama akan otomatis dibersihkan saat tipe profil diganti.</span>
            </div>
            <div class="d-flex align-items-center gap-12">
                <?php if ($isEdit && (int) $userId !== 13 && (int) $userId !== 14 && (int) $userId !== (int) session()->get('user_id')): ?>
                    <button type="button" class="btn btn-outline-danger radius-8 px-20 py-11 d-inline-flex align-items-center gap-2 btn-delete-user"
                            data-user-id="<?= (int) $userId ?>"
                            data-user-name="<?= esc($user['name'] ?? $user['username'] ?? 'Pengguna') ?>"
                            data-user-email="<?= esc($user['email'] ?? '-') ?>"
                            data-user-profile-type="<?= esc($user['profile_type'] ?? 'asn') ?>"
                            data-action-url="<?= site_url('dashboard/akses/pengguna/' . (int) $userId . '/hapus') ?>"
                            data-summary-url="<?= site_url('dashboard/akses/pengguna/' . (int) $userId . '/impact-summary') ?>">
                        <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon>
                        <span>Hapus Pengguna</span>
                    </button>
                <?php endif; ?>
                <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:disk-check-outline" class="text-lg"></iconify-icon>
                    <span><?= $isEdit ? 'Simpan Perubahan Pengguna' : 'Buat Pengguna Baru' ?></span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const profileTypeSelect = document.getElementById('field_profile_type');
    const badgeEl = document.getElementById('active-profile-badge');

    const sections = {
        asn: document.getElementById('section-profile-asn'),
        provider: document.getElementById('section-profile-provider'),
        academic: document.getElementById('section-profile-academic'),
        public: document.getElementById('section-profile-public')
    };

    const typeLabels = {
        asn: 'Aparatur Sipil Negara (ASN)',
        provider: 'Penyedia Barang/Jasa',
        academic: 'Akademisi / Peneliti',
        public: 'Masyarakat Umum'
    };

    function switchProfile(type) {
        const selected = type || 'asn';
        for (const [key, section] of Object.entries(sections)) {
            if (section) {
                if (key === selected) {
                    section.classList.remove('d-none');
                } else {
                    section.classList.add('d-none');
                }
            }
        }
        if (badgeEl) {
            badgeEl.textContent = typeLabels[selected] || selected.toUpperCase();
        }
    }

    if (profileTypeSelect) {
        profileTypeSelect.addEventListener('change', function () {
            switchProfile(this.value);
        });
        // Initial activation on page load
        switchProfile(profileTypeSelect.value);
    }

    // OPD select autofills unit_name
    const opdSelect = document.getElementById('asn_select_opd');
    const unitNameInput = document.getElementById('asn_input_unit_name');
    if (opdSelect && unitNameInput) {
        opdSelect.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.dataset && selectedOpt.dataset.name) {
                unitNameInput.value = selectedOpt.dataset.name;
            }
        });
    }

    // Wilayah Cascading Dropdowns (Provinsi -> Kab/Kota -> Kecamatan -> Kelurahan/Desa)
    (function () {
        const endpoint = <?= json_encode(site_url('dashboard/daftar/wilayah/options'), JSON_UNESCAPED_SLASHES) ?>;
        const selects = [
            document.getElementById('province_code'),
            document.getElementById('regency_code'),
            document.getElementById('district_code'),
            document.getElementById('village_code')
        ];
        const labels = ['Pilih provinsi', 'Pilih kabupaten/kota', 'Pilih kecamatan', 'Pilih desa/kelurahan'];

        if (!selects[0] || !selects[1] || !selects[2] || !selects[3]) return;

        async function loadChildren(index, parentCode, selectedCode) {
            const select = selects[index];
            if (!select) return;
            select.innerHTML = '<option value="">' + labels[index] + '</option>';
            select.disabled = !parentCode;
            if (!parentCode) return;
            try {
                const response = await fetch(endpoint + '/' + encodeURIComponent(parentCode), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) return;
                const rows = await response.json();
                rows.forEach((row) => {
                    const isSelected = String(row.code) === String(selectedCode);
                    const option = new Option(row.name, row.code, false, isSelected);
                    select.add(option);
                });
                if (selectedCode) {
                    select.value = selectedCode;
                }
                select.disabled = false;
            } catch (err) {
                console.error('Gagal memuat wilayah:', err);
            }
        }

        selects.forEach((select, index) => {
            select.addEventListener('change', async () => {
                for (let child = index + 1; child < selects.length; child++) {
                    selects[child].innerHTML = '<option value="">' + labels[child] + '</option>';
                    selects[child].disabled = true;
                    selects[child].dataset.selected = '';
                }
                if (index >= selects.length - 1) return;
                try {
                    await loadChildren(index + 1, select.value, '');
                } catch (error) {
                    console.error('Gagal memuat data turunan wilayah:', error);
                }
            });
        });

        // Initial cascading load for existing values (e.g. edit mode or validation redisplay)
        (async () => {
            try {
                if (selects[0].value) {
                    await loadChildren(1, selects[0].value, selects[1].dataset.selected);
                    if (selects[1].value) {
                        await loadChildren(2, selects[1].value, selects[2].dataset.selected);
                        if (selects[2].value) {
                            await loadChildren(3, selects[2].value, selects[3].dataset.selected);
                        }
                    }
                }
            } catch (error) {
                console.error('Inisialisasi cascading wilayah gagal:', error);
            }
        })();
    })();
});
</script>

<?= view('dashboard/users/_modal_delete') ?>

<?= $this->endSection() ?>
