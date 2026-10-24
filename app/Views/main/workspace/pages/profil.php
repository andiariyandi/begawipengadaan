<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$profileTypeLabel = match ($profileType ?? 'public') {
    'asn'      => 'Aparatur Sipil Negara (ASN)',
    'provider' => 'Penyedia Barang/Jasa',
    'academic' => 'Akademisi / Perguruan Tinggi',
    'public'   => 'Masyarakat Umum',
    default    => strtoupper((string) ($profileType ?? 'PUBLIC')),
};

$fieldLabels = [
    'username'                => 'Username Akun',
    'email'                   => 'Email Utama',
    'name'                    => 'Nama Lengkap',
    'phone'                   => 'No. HP / WhatsApp',
    'province_code'           => 'Kode Provinsi',
    'regency_code'            => 'Kode Kabupaten/Kota',
    'district_code'           => 'Kode Kecamatan',
    'village_code'            => 'Kode Kelurahan/Desa',
    'address_line'            => 'Alamat Lengkap',
    'origin_type'             => 'Asal Instansi ASN',
    'employee_identifier'     => 'NIP / NIPPPK',
    'employment_status'       => 'Status Kepegawaian',
    'organization_unit_id'    => 'ID OPD Pemprov Lampung',
    'institution_name'        => 'Nama Instansi / Lembaga',
    'unit_name'               => 'Unit Kerja / OPD',
    'position_name'           => 'Jabatan',
    'rank_grade'              => 'Pangkat / Golongan',
    'official_email'          => 'Email Kedinasan',
    'business_name'           => 'Nama Badan Usaha',
    'business_form'           => 'Bentuk Badan Usaha',
    'business_field'          => 'Bidang Usaha',
    'business_identifier'     => 'NIB (Nomor Induk Berusaha)',
    'registrant_position'     => 'Jabatan Pendaftar',
    'responsible_person'      => 'Penanggung Jawab / Direktur',
    'business_email'          => 'Email Perusahaan',
    'business_address'        => 'Alamat Kantor Perusahaan',
    'institution_type'        => 'Jenis Institusi Akademik',
    'role_name'               => 'Peran Akademik',
    'expertise'               => 'Bidang Keahlian',
    'institution_email'       => 'Email Institusi',
    'professional_identifier' => 'NIDN / NIM / NIP',
    'portfolio_url'           => 'Tautan Portofolio / SINTA',
    'national_identifier'     => 'NIK (16 Digit)',
    'occupation_status'       => 'Status Pekerjaan',
    'organization_name'       => 'Organisasi / Komunitas',
    'interest_areas'          => 'Bidang Ketertarikan PBJ',
    'usage_purpose'           => 'Tujuan Penggunaan Portal',
];
?>

<!-- Header -->
<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-user-shield text-primary me-1"></i> Manajemen Profil Dinamis &amp; Approval</span>
            <h1 class="h3 fw-bold mb-1" style="color: #082f5c;"><?= esc($user['name'] ?? 'Profil Pengguna') ?></h1>
            <p class="text-muted mb-0" style="font-size: 14.5px;">
                Kelola data identitas, domisili berjenjang, dan atribut spesifik sesuai skema registrasi <strong><?= esc($profileTypeLabel) ?></strong>.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                <i class="fas fa-lock me-1"></i> Tipe: <?= esc($profileTypeLabel) ?>
            </span>
        </div>
    </div>
</div>

<!-- Status Pengajuan Perubahan Profil Terakhir -->
<?php if (! empty($latestRequest)): ?>
    <?php
    $reqStatus = $latestRequest['status'] ?? 'submitted';
    $reqPayload = json_decode((string) ($latestRequest['requested_payload'] ?? '{}'), true) ?: [];
    ?>
    <?php if (in_array($reqStatus, ['submitted', 'in_review'], true)): ?>
        <div class="alert alert-warning border-warning shadow-xs rounded-4 p-3 p-md-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <span class="w-44-px h-44-px rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-xs">
                    <i class="fas fa-hourglass-half fs-5"></i>
                </span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <h6 class="fw-bold text-dark mb-0">Menunggu Persetujuan (Pending Approval)</h6>
                        <span class="badge bg-warning text-dark px-3 py-1 text-xs fw-bold border border-dark-subtle">
                            <i class="fas fa-clock me-1"></i>Status: <?= $reqStatus === 'in_review' ? 'Sedang Ditinjau Verifikator' : 'Menunggu Persetujuan Admin / Operator' ?>
                        </span>
                    </div>
                    <p class="text-secondary small mb-2">
                        Permohonan perubahan profil Anda diajukan pada <strong><?= date('d M Y, H:i', strtotime($latestRequest['created_at'])) ?> WIB</strong>.
                        Data baru di bawah ini akan otomatis aktif setelah disetujui oleh Administrator atau Operator.
                    </p>
                    <?php if (! empty($latestRequest['reason'])): ?>
                        <div class="p-2 px-3 bg-white rounded-3 border text-xs text-dark mb-2">
                            <strong><i class="fas fa-comment-alt text-warning me-1"></i>Alasan Pengajuan:</strong> <?= esc($latestRequest['reason']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($reqPayload)): ?>
                        <div class="table-responsive bg-white rounded-3 border mt-2">
                            <table class="table table-sm mb-0 align-middle text-xs" data-ws-enhanced="true">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Atribut yang Diajukan</th>
                                        <th>Nilai Baru (Menunggu Approval)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reqPayload as $k => $v): ?>
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark"><?= esc($fieldLabels[$k] ?? ucwords(str_replace('_', ' ', (string) $k))) ?></td>
                                            <td class="font-monospace text-primary fw-semibold"><?= esc(is_array($v) ? json_encode($v) : (string) $v) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($reqStatus === 'revision_required'): ?>
        <div class="alert alert-danger border-danger-subtle shadow-xs rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <span class="w-40-px h-40-px rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-danger mb-0">Perbaikan / Revisi Diperlukan</h6>
                    <span class="badge bg-danger text-white px-2-5 py-1 text-2xs fw-semibold">Perlu Revisi</span>
                </div>
                <p class="text-secondary small mb-2">
                    Verifikator meminta revisi terhadap pengajuan perubahan profil Anda.
                </p>
                <div class="p-2 bg-white rounded-3 border text-xs text-danger mb-2">
                    <strong>Catatan Revisi:</strong> <?= esc($latestRequest['review_notes'] ?: 'Mohon periksa dan perbarui data yang valid.') ?>
                </div>
                <small class="text-muted">Silakan sesuaikan data pada formulir di bawah ini kemudian klik <strong>Ajukan Perubahan Profil</strong> kembali.</small>
            </div>
        </div>
    <?php elseif ($reqStatus === 'rejected'): ?>
        <div class="alert alert-secondary border shadow-xs rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <span class="w-40-px h-40-px rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <i class="fas fa-times-circle"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 class="fw-bold text-dark mb-0">Pengajuan Perubahan Terakhir Ditolak</h6>
                    <span class="badge bg-secondary text-white px-2-5 py-1 text-2xs">Ditolak</span>
                </div>
                <p class="text-secondary small mb-0">
                    Alasan penolakan: <em><?= esc($latestRequest['reason'] ?: 'Data tidak sesuai ketentuan.') ?></em>. Anda dapat mengajukan permohonan baru dengan data yang valid.
                </p>
            </div>
        </div>
    <?php elseif ($reqStatus === 'approved'): ?>
        <div class="alert alert-success border-success-subtle shadow-xs rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <span class="w-36-px h-36-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="flex-grow-1">
                <span class="text-success small fw-semibold">Perubahan profil terakhir telah disetujui dan diterapkan pada <?= date('d M Y, H:i', strtotime($latestRequest['applied_at'] ?? $latestRequest['updated_at'])) ?> WIB.</span>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Profile Body -->
<div class="row g-4">
    <!-- Left Column: Form Dinamis Sesuai Skema Register Bertahap -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-2">
                <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">
                    <i class="fas fa-id-card text-primary me-2"></i>1. Identitas Akun &amp; Tipe Profil
                </h2>
                <span class="badge bg-light text-secondary border text-2xs">
                    <i class="fas fa-shield-alt text-primary me-1"></i>Dilindungi Sistem Approval
                </span>
            </div>

            <form action="<?= route_to('workspace.profile.update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <!-- Kunci Statis: Tipe Profil (Readonly & Disabled) -->
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark d-flex align-items-center gap-2">
                            <span>Tipe Profil Pengguna</span>
                            <span class="badge bg-secondary-subtle text-secondary border text-2xs">
                                <i class="fas fa-lock me-1"></i>Terkunci Permanen
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="text" class="form-control ws-locked-field border-start-0 fw-bold" value="<?= esc($profileTypeLabel) ?> (<?= esc(strtoupper($profileType)) ?>)" readonly disabled tabindex="-1">
                        </div>
                        <div class="ws-form-hint">
                            <i class="fas fa-info-circle text-primary"></i>
                            Tipe profil ditentukan saat registrasi awal dan dikunci secara statis untuk menjaga integritas struktur data.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Nama Lengkap (Beserta Gelar) *</label>
                        <input type="text" name="full_name" class="form-control rounded-3" value="<?= esc($user['name'] ?? '') ?>" required>
                        <div class="ws-form-hint"><i class="fas fa-certificate"></i> Digunakan sebagai nama resmi pada penerbitan E-Sertifikat.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Username Akun *</label>
                        <input type="text" name="username" class="form-control rounded-3" value="<?= esc($user['username'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Alamat Email Utama *</label>
                        <input type="email" name="email" class="form-control rounded-3" value="<?= esc($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Nomor HP / WhatsApp Aktif *</label>
                        <input type="text" name="phone" class="form-control rounded-3" value="<?= esc($user['phone'] ?? '') ?>" required>
                    </div>

                    <!-- Langkah 2 Register Bertahap: Wilayah & Alamat Domisili Berjenjang -->
                    <div class="col-12 mt-4">
                        <h3 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                            <i class="fas fa-map-marked-alt text-primary me-2"></i>2. Wilayah &amp; Alamat Domisili Berjenjang
                        </h3>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Provinsi</label>
                        <select name="province_code" id="wsProvinceSelect" class="form-select rounded-3">
                            <option value="">-- Pilih Provinsi --</option>
                            <?php foreach ($provinces ?? [] as $p): ?>
                                <option value="<?= esc($p['kode']) ?>" <?= (($profile['province_code'] ?? '') === $p['kode']) ? 'selected' : '' ?>>
                                    <?= esc($p['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Kabupaten / Kota</label>
                        <select name="regency_code" id="wsRegencySelect" class="form-select rounded-3">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            <?php foreach ($regencies ?? [] as $r): ?>
                                <option value="<?= esc($r['kode']) ?>" <?= (($profile['regency_code'] ?? '') === $r['kode']) ? 'selected' : '' ?>>
                                    <?= esc($r['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Kecamatan</label>
                        <select name="district_code" id="wsDistrictSelect" class="form-select rounded-3">
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($districts ?? [] as $d): ?>
                                <option value="<?= esc($d['kode']) ?>" <?= (($profile['district_code'] ?? '') === $d['kode']) ? 'selected' : '' ?>>
                                    <?= esc($d['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Kelurahan / Desa</label>
                        <select name="village_code" id="wsVillageSelect" class="form-select rounded-3">
                            <option value="">-- Pilih Kelurahan/Desa (Opsional) --</option>
                            <?php foreach ($villages ?? [] as $v): ?>
                                <option value="<?= esc($v['kode']) ?>" <?= (($profile['village_code'] ?? '') === $v['kode']) ? 'selected' : '' ?>>
                                    <?= esc($v['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Alamat Lengkap Domisili</label>
                        <textarea name="address_line" class="form-control rounded-3" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, atau kompleks..."><?= esc($profile['address_line'] ?? '') ?></textarea>
                    </div>

                    <!-- Langkah 3 Register Bertahap: Atribut Spesifik Sesuai Tipe Profil -->
                    <?php if ($profileType === 'asn'): ?>
                        <?php
                        $currentOrigin = $detail['origin_type'] ?? 'pemprov_lampung';
                        ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                                <i class="fas fa-landmark text-primary me-2"></i>3. Data Kepegawaian &amp; Instansi ASN
                            </h3>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Asal Instansi ASN</label>
                            <select name="origin_type" id="wsAsnOriginSelect" class="form-select rounded-3">
                                <option value="pemprov_lampung" <?= $currentOrigin === 'pemprov_lampung' ? 'selected' : '' ?>>Pemerintah Provinsi Lampung</option>
                                <option value="pemerintah_daerah_lain" <?= $currentOrigin === 'pemerintah_daerah_lain' ? 'selected' : '' ?>>Pemerintah Kabupaten/Kota / Provinsi Lain</option>
                                <option value="kementerian_lembaga" <?= $currentOrigin === 'kementerian_lembaga' ? 'selected' : '' ?>>Kementerian / Lembaga Pusat</option>
                                <option value="bumn_bumd" <?= $currentOrigin === 'bumn_bumd' ? 'selected' : '' ?>>BUMN / BUMD</option>
                                <option value="pemerintah_desa" <?= $currentOrigin === 'pemerintah_desa' ? 'selected' : '' ?>>Pemerintah Desa</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Status Kepegawaian</label>
                            <?php $empStatus = $detail['employment_status'] ?? 'PNS'; ?>
                            <select name="employment_status" class="form-select rounded-3">
                                <option value="PNS" <?= strcasecmp($empStatus, 'PNS') === 0 ? 'selected' : '' ?>>PNS (Pegawai Negeri Sipil)</option>
                                <option value="PPPK" <?= strcasecmp($empStatus, 'PPPK') === 0 ? 'selected' : '' ?>>PPPK (Pegawai Pemerintah dengan Perjanjian Kerja)</option>
                                <option value="Non-ASN" <?= strcasecmp($empStatus, 'Non-ASN') === 0 ? 'selected' : '' ?>>Non-ASN / Tenaga Kontrak Kedinasan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NIP / NIPPPK</label>
                            <input type="text" name="employee_identifier" class="form-control rounded-3" value="<?= esc($detail['employee_identifier'] ?? '') ?>" placeholder="18 digit NIP / NIPPPK">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pangkat / Golongan Ruang</label>
                            <input type="text" name="rank_grade" class="form-control rounded-3" value="<?= esc($detail['rank_grade'] ?? '') ?>" placeholder="Contoh: Penata Tk. I (III/d)">
                        </div>

                        <!-- Pilihan OPD Pemprov Lampung -->
                        <div class="col-12 <?= $currentOrigin === 'pemprov_lampung' ? '' : 'd-none' ?>" id="wsOpdLampungWrap">
                            <label class="form-label small fw-bold text-dark">OPD / Perangkat Daerah Pemprov Lampung</label>
                            <select name="organization_unit_id" class="form-select rounded-3">
                                <option value="">-- Pilih OPD Pemerintah Provinsi Lampung --</option>
                                <?php foreach ($organizationUnits ?? [] as $opd): ?>
                                    <option value="<?= (int) $opd['id'] ?>" <?= ((int) ($detail['organization_unit_id'] ?? 0) === (int) $opd['id']) ? 'selected' : '' ?>>
                                        <?= esc($opd['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="ws-form-hint"><i class="fas fa-building"></i> Jika Anda berasal dari Pemprov Lampung, pilih OPD resmi dari daftar di atas.</div>
                        </div>

                        <!-- Input Instansi Luar Pemprov -->
                        <div class="col-md-6 <?= $currentOrigin === 'pemprov_lampung' ? 'd-none' : '' ?>" id="wsExternalInstWrap">
                            <label class="form-label small fw-bold text-dark">Nama Instansi / Pemerintah Daerah</label>
                            <input type="text" name="institution_name" class="form-control rounded-3" value="<?= esc($detail['institution_name'] ?? '') ?>" placeholder="Contoh: Pemerintah Kabupaten Lampung Selatan">
                        </div>
                        <div class="col-md-6 <?= $currentOrigin === 'pemprov_lampung' ? 'd-none' : '' ?>" id="wsExternalUnitWrap">
                            <label class="form-label small fw-bold text-dark">Unit Kerja / Satuan Kerja</label>
                            <input type="text" name="unit_name" class="form-control rounded-3" value="<?= esc($detail['unit_name'] ?? '') ?>" placeholder="Contoh: Bagian Pengadaan Barang dan Jasa">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jabatan Struktural / Fungsional</label>
                            <input type="text" name="position_name" class="form-control rounded-3" value="<?= esc($detail['position_name'] ?? '') ?>" placeholder="Contoh: Pengelola Pengadaan Barang/Jasa Ahli Muda">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Email Kedinasan (Opsional)</label>
                            <input type="email" name="official_email" class="form-control rounded-3" value="<?= esc($detail['official_email'] ?? '') ?>" placeholder="nama@lampungprov.go.id / @go.id">
                        </div>

                    <?php elseif ($profileType === 'provider'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                                <i class="fas fa-building text-primary me-2"></i>3. Data Badan Usaha / Penyedia Barang &amp; Jasa
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Badan Usaha / Perusahaan</label>
                            <input type="text" name="business_name" class="form-control rounded-3" value="<?= esc($detail['business_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Bentuk Badan Usaha</label>
                            <?php $bForm = $detail['business_form'] ?? ''; ?>
                            <select name="business_form" class="form-select rounded-3">
                                <option value="">-- Pilih Bentuk Usaha --</option>
                                <?php foreach (['PT (Perseroan Terbatas)', 'CV (Commanditaire Vennootschap)', 'Firma / Koperasi', 'UD / Usaha Perorangan', 'BUMN / BUMD', 'Lainnya'] as $bf): ?>
                                    <option value="<?= esc($bf) ?>" <?= $bForm === $bf ? 'selected' : '' ?>><?= esc($bf) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nomor Induk Berusaha (NIB)</label>
                            <input type="text" name="business_identifier" class="form-control rounded-3" value="<?= esc($detail['business_identifier'] ?? '') ?>" placeholder="13 digit NIB OSS">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Bidang Usaha Utama</label>
                            <input type="text" name="business_field" class="form-control rounded-3" value="<?= esc($detail['business_field'] ?? '') ?>" placeholder="Contoh: Konstruksi, Pengadaan IT, Jasa Konsultansi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Penanggung Jawab / Direktur</label>
                            <input type="text" name="responsible_person" class="form-control rounded-3" value="<?= esc($detail['responsible_person'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jabatan Pendaftar di Perusahaan</label>
                            <input type="text" name="registrant_position" class="form-control rounded-3" value="<?= esc($detail['registrant_position'] ?? '') ?>" placeholder="Contoh: Direktur / Admin E-Katalog">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Email Resmi Perusahaan</label>
                            <input type="email" name="business_email" class="form-control rounded-3" value="<?= esc($detail['business_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Alamat Kantor Perusahaan</label>
                            <input type="text" name="business_address" class="form-control rounded-3" value="<?= esc($detail['business_address'] ?? '') ?>">
                        </div>

                    <?php elseif ($profileType === 'academic'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                                <i class="fas fa-university text-primary me-2"></i>3. Data Institusi Akademik &amp; Keahlian
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Perguruan Tinggi / Institusi</label>
                            <input type="text" name="institution_name" class="form-control rounded-3" value="<?= esc($detail['institution_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jenis Institusi</label>
                            <?php $instType = $detail['institution_type'] ?? ''; ?>
                            <select name="institution_type" class="form-select rounded-3">
                                <option value="">-- Pilih Jenis Institusi --</option>
                                <?php foreach (['Universitas', 'Institut', 'Politeknik', 'Sekolah Tinggi', 'Lembaga Riset / Pelatihan', 'Lainnya'] as $it): ?>
                                    <option value="<?= esc($it) ?>" <?= $instType === $it ? 'selected' : '' ?>><?= esc($it) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Peran / Jabatan Akademik</label>
                            <input type="text" name="role_name" class="form-control rounded-3" value="<?= esc($detail['role_name'] ?? '') ?>" placeholder="Contoh: Dosen / Peneliti / Mahasiswa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NIDN / NIP / NIM</label>
                            <input type="text" name="professional_identifier" class="form-control rounded-3" value="<?= esc($detail['professional_identifier'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Email Institusi (.ac.id)</label>
                            <input type="email" name="institution_email" class="form-control rounded-3" value="<?= esc($detail['institution_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tautan Portofolio / SINTA / Google Scholar</label>
                            <input type="url" name="portfolio_url" class="form-control rounded-3" value="<?= esc($detail['portfolio_url'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Bidang Keahlian / Riset</label>
                            <input type="text" name="expertise" class="form-control rounded-3" value="<?= esc($detail['expertise'] ?? '') ?>" placeholder="Contoh: Manajemen Rantai Pasok, Hukum Kontrak Pengadaan">
                        </div>

                    <?php elseif ($profileType === 'public'): ?>
                        <div class="col-12 mt-4">
                            <h3 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                                <i class="fas fa-users text-primary me-2"></i>3. Data Profil Masyarakat Umum
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="national_identifier" class="form-control rounded-3" value="<?= esc($detail['national_identifier'] ?? '') ?>" placeholder="16 digit NIK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Status Pekerjaan Saat Ini</label>
                            <input type="text" name="occupation_status" class="form-control rounded-3" value="<?= esc($detail['occupation_status'] ?? '') ?>" placeholder="Contoh: Karyawan Swasta / Konsultan Mandiri / Wiraswasta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Organisasi / Asosiasi / Komunitas</label>
                            <input type="text" name="organization_name" class="form-control rounded-3" value="<?= esc($detail['organization_name'] ?? '') ?>" placeholder="Kosongkan bila perorangan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tujuan Penggunaan Portal</label>
                            <input type="text" name="usage_purpose" class="form-control rounded-3" value="<?= esc($detail['usage_purpose'] ?? '') ?>" placeholder="Contoh: Mengikuti Pelatihan PBJ & Akses Regulasi">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Bidang Ketertarikan PBJ</label>
                            <input type="text" name="interest_areas" class="form-control rounded-3" value="<?= esc($detail['interest_areas'] ?? '') ?>" placeholder="Contoh: Pemilihan Penyedia, Kontrak, E-Purchasing">
                        </div>
                    <?php endif; ?>

                    <!-- Alasan Pengajuan Perubahan -->
                    <div class="col-12 mt-4">
                        <h3 class="h6 fw-bold mb-2 pb-1 border-bottom" style="color: #082f5c;">
                            <i class="fas fa-clipboard-check text-primary me-2"></i>4. Catatan Pengajuan Approval Perubahan
                        </h3>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Alasan Perubahan Data Profil</label>
                        <textarea name="reason" class="form-control rounded-3" rows="2" placeholder="Jelaskan alasan pembaruan data (misal: mutasi OPD baru, kenaikan pangkat, atau pembaruan alamat domisili)..."></textarea>
                        <div class="ws-form-hint">
                            <i class="fas fa-user-check text-success"></i>
                            Setiap perubahan data pada formulir di atas akan masuk ke status <strong>Menunggu Persetujuan (Pending Approval)</strong> untuk diverifikasi Admin/Operator.
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="text-muted small" style="max-width: 440px;">
                                <i class="fas fa-shield-alt text-primary me-1"></i>
                                <span>Data yang belum disetujui tidak akan menimpa sertifikat aktif sampai diverifikasi oleh Admin/Operator.</span>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2-5 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-xs">
                                <i class="fas fa-paper-plane"></i>
                                <span>Ajukan Perubahan Profil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Keamanan, Peran Pengadaan & Riwayat Approval -->
    <div class="col-lg-4">
        <!-- Status Akun & Tipe Profil -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-shield-alt text-success me-2"></i>Ringkasan Status Akun
            </h2>
            <div class="list-group list-group-flush small">
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Tipe Profil (Statis):</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="fas fa-lock me-1"></i><?= esc(strtoupper($profileType)) ?>
                    </span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status Akun:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif Terverifikasi</span>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Terdaftar Sejak:</span>
                    <span class="fw-semibold text-dark"><?= ! empty($user['created_at']) ? esc(date('d M Y', strtotime($user['created_at']))) : '-' ?></span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <a href="<?= route_to('workspace.profile.role-requests') ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2 fw-semibold">
                    <i class="fas fa-user-plus me-1"></i> Pengajuan Peran Fungsional / Trainer
                </a>
            </div>
        </div>

        <!-- Peran Pengadaan ASN (Jika ada) -->
        <?php if ($profileType === 'asn' && ! empty($userProcurementRoles)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
                <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                    <i class="fas fa-briefcase text-primary me-2"></i>Peran Pengadaan PBJ
                </h2>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($userProcurementRoles as $upr): ?>
                        <?php
                        $vStatus = $upr['verification_status'] ?? 'pending';
                        $vBadge = match ($vStatus) {
                            'verified', 'approved' => 'bg-success-subtle text-success border-success-subtle',
                            'rejected'             => 'bg-danger-subtle text-danger border-danger-subtle',
                            default                => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                        };
                        ?>
                        <div class="p-2 px-3 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                            <strong class="text-dark small"><?= esc($upr['role_name'] ?? 'Pelaku Pengadaan') ?></strong>
                            <span class="badge border <?= $vBadge ?> text-2xs"><?= esc(ucfirst($vStatus)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Keamanan Password -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-key text-warning me-2"></i>Ubah Kata Sandi
            </h2>
            <form action="<?= route_to('workspace.profile.password.update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control rounded-3" required autocomplete="current-password">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="form-control rounded-3" required autocomplete="new-password">
                    <div class="ws-form-hint"><i class="fas fa-lock"></i> Minimal 8 karakter.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="confirm_password" class="form-control rounded-3" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2 fw-bold">
                    <i class="fas fa-lock me-1"></i> Perbarui Kata Sandi
                </button>
            </form>
        </div>

        <!-- Riwayat Pengajuan Perubahan Profil (Roadmap/Log) -->
        <?php if (! empty($requestHistory)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
                <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                    <i class="fas fa-history text-primary me-2"></i>Riwayat Approval Profil
                </h2>
                <div class="ws-roadmap">
                    <?php foreach (array_slice($requestHistory, 0, 5) as $idx => $rh): ?>
                        <?php
                        $st = $rh['status'] ?? 'submitted';
                        $stepCls = match ($st) {
                            'approved'             => 'is-done',
                            'submitted', 'in_review' => 'is-active',
                            default                => 'is-pending',
                        };
                        $stLabel = match ($st) {
                            'approved'          => 'Disetujui',
                            'submitted'         => 'Menunggu Approval',
                            'in_review'         => 'Ditinjau',
                            'revision_required' => 'Perlu Revisi',
                            'rejected'          => 'Ditolak',
                            default             => ucfirst($st),
                        };
                        ?>
                        <div class="ws-roadmap-step <?= $stepCls ?>" style="padding-bottom: 0.75rem;">
                            <span class="ws-roadmap-dot">
                                <i class="fas <?= $st === 'approved' ? 'fa-check' : ($st === 'rejected' ? 'fa-times' : 'fa-clock') ?>"></i>
                            </span>
                            <div class="ws-roadmap-card" style="padding: 0.55rem 0.75rem;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark" style="font-size: 0.76rem;"><?= esc($stLabel) ?></strong>
                                    <small class="text-muted" style="font-size: 0.68rem;"><?= date('d M Y, H:i', strtotime($rh['created_at'])) ?></small>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.72rem;"><?= esc($rh['reason'] ?: 'Pembaruan data profil') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = <?= json_encode(site_url('dashboard/daftar/wilayah/options')) ?>;
    const provSelect = document.getElementById('wsProvinceSelect');
    const regSelect  = document.getElementById('wsRegencySelect');
    const distSelect = document.getElementById('wsDistrictSelect');
    const vilSelect  = document.getElementById('wsVillageSelect');

    async function loadChildren(parentCode, targetSelect, placeholder) {
        if (!targetSelect) return;
        targetSelect.innerHTML = '<option value="">' + placeholder + '</option>';
        if (!parentCode) return;
        try {
            const res = await fetch(baseUrl + '/' + encodeURIComponent(parentCode));
            if (!res.ok) return;
            const list = await res.json();
            list.forEach(function (item) {
                const opt = document.createElement('option');
                opt.value = item.code;
                opt.textContent = item.name;
                targetSelect.appendChild(opt);
            });
        } catch (e) {}
    }

    if (provSelect) {
        provSelect.addEventListener('change', function () {
            loadChildren(this.value, regSelect, '-- Pilih Kabupaten/Kota --');
            if (distSelect) distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            if (vilSelect) vilSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa (Opsional) --</option>';
        });
    }
    if (regSelect) {
        regSelect.addEventListener('change', function () {
            loadChildren(this.value, distSelect, '-- Pilih Kecamatan --');
            if (vilSelect) vilSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa (Opsional) --</option>';
        });
    }
    if (distSelect) {
        distSelect.addEventListener('change', function () {
            loadChildren(this.value, vilSelect, '-- Pilih Kelurahan/Desa (Opsional) --');
        });
    }

    const asnOriginSelect = document.getElementById('wsAsnOriginSelect');
    const opdWrap = document.getElementById('wsOpdLampungWrap');
    const extInstWrap = document.getElementById('wsExternalInstWrap');
    const extUnitWrap = document.getElementById('wsExternalUnitWrap');

    if (asnOriginSelect) {
        asnOriginSelect.addEventListener('change', function () {
            const isLampung = this.value === 'pemprov_lampung';
            if (opdWrap) opdWrap.classList.toggle('d-none', !isLampung);
            if (extInstWrap) extInstWrap.classList.toggle('d-none', isLampung);
            if (extUnitWrap) extUnitWrap.classList.toggle('d-none', isLampung);
        });
    }
});
</script>
<?= $this->endSection() ?>
