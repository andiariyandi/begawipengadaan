<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('containerClass') ?>max-w-634-px<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>

    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Tinjau Data Pendaftaran') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 4 dari 4 — Pastikan seluruh data pendaftaran akun Anda sudah benar dan sesuai.') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', [
    'currentStep' => $currentStep ?? 4,
    'currentKey'  => 'tinjau',
]) ?>

<?php
$registrationData    = $registrationData ?? [];
$registrationAddress = $registrationAddress ?? [];
$registrationProfile = $registrationProfile ?? [];
$profileLabels       = [
    'asn'      => 'Aparatur Sipil Negara (ASN)',
    'provider' => 'Penyedia Barang/Jasa (Vendor)',
    'academic' => 'Akademisi / Peneliti',
    'public'   => 'Masyarakat Umum',
];

$profileSegment = $profileSegment ?? match($registrationData['profile_type'] ?? '') {
    'provider' => 'penyedia',
    'academic' => 'akademisi',
    'public'   => 'masyarakat',
    default    => 'asn',
};
?>

<!-- Flash Alerts -->
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
            <strong class="text-sm">Kendala Validasi:</strong>
        </div>
        <ul class="mb-0 ps-20 text-xs">
            <?php foreach ((array) $errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Section 1: Akun Dasar -->
<div class="border border-neutral-200 radius-12 p-20 mb-16 bg-neutral-50 shadow-none">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-12">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:user-bold" class="text-primary-600 text-lg"></iconify-icon>
            <h6 class="mb-0 text-sm fw-bold text-primary-light">1. Akun Dasar</h6>
        </div>
        <a href="<?= site_url('dashboard/daftar') ?>" class="text-primary-600 text-xs fw-semibold d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:pen-linear"></iconify-icon>
            <span>Ubah</span>
        </a>
    </div>
    <div class="d-flex flex-column gap-8 text-sm">
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Nama Lengkap:</span>
            <strong class="text-primary-light"><?= esc($registrationData['full_name'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Username:</span>
            <strong class="text-primary-light"><?= esc($registrationData['username'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Email:</span>
            <strong class="text-primary-light"><?= esc($registrationData['email'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Telepon:</span>
            <strong class="text-primary-light"><?= esc($registrationData['phone'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Tipe Profil:</span>
            <span class="badge bg-primary-50 text-primary-600 border border-primary-100 text-xs px-8 py-2 radius-4">
                <?= esc($profileLabels[$registrationData['profile_type'] ?? ''] ?? strtoupper((string) ($registrationData['profile_type'] ?? '-'))) ?>
            </span>
        </div>
    </div>
</div>

<!-- Section 2: Wilayah dan Alamat -->
<div class="border border-neutral-200 radius-12 p-20 mb-16 bg-neutral-50 shadow-none">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-12">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:map-point-bold" class="text-primary-600 text-lg"></iconify-icon>
            <h6 class="mb-0 text-sm fw-bold text-primary-light">2. Wilayah Domisili</h6>
        </div>
        <a href="<?= site_url('dashboard/daftar/wilayah') ?>" class="text-primary-600 text-xs fw-semibold d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:pen-linear"></iconify-icon>
            <span>Ubah</span>
        </a>
    </div>
    <div class="d-flex flex-column gap-8 text-sm">
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Provinsi:</span>
            <strong class="text-primary-light"><?= esc($registrationAddress['province'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Kabupaten/Kota:</span>
            <strong class="text-primary-light"><?= esc($registrationAddress['regency'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Kecamatan:</span>
            <strong class="text-primary-light"><?= esc($registrationAddress['district'] ?? '-') ?></strong>
        </div>
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Alamat Ringkas:</span>
            <strong class="text-primary-light text-end"><?= esc($registrationAddress['address'] ?? '-') ?></strong>
        </div>
    </div>
</div>

<!-- Section 3: Profil Spesifik -->
<div class="border border-neutral-200 radius-12 p-20 mb-20 bg-neutral-50 shadow-none">
    <div class="d-flex justify-content-between align-items-center gap-12 mb-12">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-lg"></iconify-icon>
            <h6 class="mb-0 text-sm fw-bold text-primary-light">3. Detail Profil Spesifik</h6>
        </div>
        <a href="<?= site_url('dashboard/daftar/profil/' . $profileSegment) ?>" class="text-primary-600 text-xs fw-semibold d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:pen-linear"></iconify-icon>
            <span>Ubah</span>
        </a>
    </div>
    <div class="d-flex flex-column gap-8 text-sm">
        <div class="d-flex justify-content-between gap-12">
            <span class="text-secondary-light">Kategori:</span>
            <strong class="text-primary-light"><?= esc($registrationProfile['label'] ?? '-') ?></strong>
        </div>
        <?php $pFields = $registrationProfile['fields'] ?? []; ?>
        <?php if (($registrationProfile['profile_type'] ?? '') === 'asn'): ?>
            <div class="d-flex justify-content-between gap-12">
                <span class="text-secondary-light">Asal Instansi:</span>
                <strong class="text-primary-light text-end">
                    <?= esc($pFields['asn_origin'] === 'pemprov_lampung' ? 'Pemerintah Provinsi Lampung' : ucwords(str_replace('_', ' ', (string) ($pFields['asn_origin'] ?? '-')))) ?>
                </strong>
            </div>
            <?php if (! empty($pFields['nip_nippk'])): ?>
                <div class="d-flex justify-content-between gap-12">
                    <span class="text-secondary-light">NIP / NIPPK:</span>
                    <strong class="text-primary-light text-end"><?= esc($pFields['nip_nippk']) ?></strong>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between gap-12">
                <span class="text-secondary-light">Status Kepegawaian:</span>
                <strong class="text-primary-light text-end"><?= esc(strtoupper((string) ($pFields['employment_status'] ?? '-'))) ?></strong>
            </div>
            <div class="d-flex justify-content-between gap-12">
                <span class="text-secondary-light">OPD / Unit Kerja:</span>
                <strong class="text-primary-light text-end"><?= esc($pFields['opd_name'] ?? $pFields['external_unit_name'] ?? $pFields['institution_name'] ?? '-') ?></strong>
            </div>
            <div class="d-flex justify-content-between gap-12">
                <span class="text-secondary-light">Jabatan Utama:</span>
                <strong class="text-primary-light text-end"><?= esc($pFields['position'] ?? '-') ?></strong>
            </div>
            <?php if (! empty($pFields['official_email'])): ?>
                <div class="d-flex justify-content-between gap-12">
                    <span class="text-secondary-light">Email Kedinasan:</span>
                    <strong class="text-primary-light text-end"><?= esc($pFields['official_email']) ?></strong>
                </div>
            <?php endif; ?>
            <?php if (! empty($pFields['procurement_role_names'])): ?>
                <div class="d-flex flex-column gap-6 mt-4 pt-8 border-top">
                    <span class="text-secondary-light">Peran Pengadaan yang Diajukan:</span>
                    <div class="d-flex flex-wrap gap-6">
                        <?php foreach ($pFields['procurement_role_names'] as $rName): ?>
                            <span class="badge bg-primary-50 text-primary-600 border border-primary-100 text-xs px-8 py-4 radius-4">
                                <iconify-icon icon="solar:shield-user-bold" class="align-middle me-1"></iconify-icon><?= esc($rName) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between gap-12">
                    <span class="text-secondary-light">Peran Pengadaan:</span>
                    <span class="text-secondary-light">Tidak ada penugasan peran PBJ spesifik</span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if (! empty($pFields)): ?>
                <?php foreach ($pFields as $fKey => $fVal): ?>
                    <?php
                    if (is_array($fVal) || empty($fVal)) continue;
                    $displayVal = (string) $fVal;
                    if (str_starts_with($displayVal, '[') && str_ends_with($displayVal, ']')) {
                        $arr = json_decode($displayVal, true);
                        if (is_array($arr)) {
                            $displayVal = implode(', ', array_map(static fn($v) => ucwords(str_replace('_', ' ', (string) $v)), $arr));
                        }
                    } else {
                        $displayVal = ucwords(str_replace('_', ' ', $displayVal));
                    }
                    ?>
                    <div class="d-flex justify-content-between gap-12">
                        <span class="text-secondary-light"><?= esc(ucwords(str_replace('_', ' ', (string) $fKey))) ?>:</span>
                        <strong class="text-primary-light text-end"><?= esc($displayVal) ?></strong>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<form action="<?= site_url('dashboard/daftar/tinjau') ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-check style-check d-flex align-items-start mb-12">
        <input class="form-check-input border border-neutral-300 mt-4 flex-shrink-0" type="checkbox" value="1" id="terms" name="terms" required>
        <label class="form-check-label text-sm ms-2" for="terms">
            Saya menyetujui seluruh ketentuan pada <a href="<?= site_url('tentang') ?>" class="text-primary-600 fw-semibold" target="_blank">Syarat Penggunaan Layanan</a>.
        </label>
    </div>

    <div class="form-check style-check d-flex align-items-start mb-20">
        <input class="form-check-input border border-neutral-300 mt-4 flex-shrink-0" type="checkbox" value="1" id="privacy" name="privacy" required>
        <label class="form-check-label text-sm ms-2" for="privacy">
            Saya mengonfirmasi bahwa data yang diisi benar serta menyetujui <a href="<?= site_url('tentang') ?>" class="text-primary-600 fw-semibold" target="_blank">Kebijakan Privasi</a>.
        </label>
    </div>

    <!-- Security Verification Card -->
    <div class="border border-success-200 bg-success-50 radius-12 p-16 mb-24 text-center">
        <div class="d-inline-flex align-items-center justify-content-center w-40-px h-40-px bg-success-100 text-success-main rounded-circle mb-8">
            <iconify-icon icon="solar:shield-check-bold" class="text-2xl"></iconify-icon>
        </div>
        <p class="fw-semibold text-sm mb-2 text-primary-light">Verifikasi Keamanan Terjamin</p>
        <p class="text-secondary-light text-xs mb-0">
            Pendaftaran akun BegawiPengadaan dilindungi proteksi sesi terenkripsi dan pencatatan audit log integritas.
        </p>
    </div>

    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/daftar/profil/' . $profileSegment) ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
            <span>Kembali</span>
        </a>
        <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <span>Kirim Pendaftaran</span>
            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
        </button>
    </div>
</form>
<?= $this->endSection() ?>