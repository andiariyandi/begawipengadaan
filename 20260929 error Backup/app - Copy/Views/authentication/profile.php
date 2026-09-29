<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<div class="row g-4">
<div class="col-lg-8"><div class="card p-24 radius-12"><h6 class="mb-8">Profil Saya</h6><p class="text-secondary-light mb-24">Kategori: <?= esc($options->profileTypes[$profileType] ?? $profileType) ?></p>
<?php if ($message = session()->getFlashdata('message')): ?><div class="alert alert-success"><?= esc($message) ?></div><?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endif; ?>
<form action="<?= route_to('profile.update') ?>" method="post"><?= csrf_field() ?>
<div class="row">
<?php foreach (['username' => 'Username', 'email' => 'Email', 'full_name' => 'Nama Lengkap', 'title_prefix' => 'Gelar Depan', 'title_suffix' => 'Gelar Belakang', 'phone' => 'No. HP / WhatsApp'] as $field => $label): ?><div class="col-md-6 mb-16"><label class="form-label"><?= $label ?></label><input name="<?= $field ?>" class="form-control" <?= $field === 'email' ? 'type="email"' : '' ?> value="<?= esc($user[$field] ?? $profile[$field] ?? '') ?>" <?= in_array($field, ['username', 'email', 'full_name', 'phone'], true) ? 'required' : '' ?>></div><?php endforeach; ?>
<div class="col-md-6 mb-16"><label class="form-label">Provinsi Domisili</label><select name="province_code" id="province-code" class="form-select" required><option value="">Pilih provinsi</option><?php foreach ($provinces as $region): ?><option value="<?= esc($region['kode']) ?>" <?= ($profile['province_code'] ?? '') === $region['kode'] ? 'selected' : '' ?>><?= esc($region['nama']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6 mb-16"><label class="form-label">Kabupaten / Kota</label><select name="regency_code" id="regency-code" class="form-select" required><option value="">Pilih kabupaten/kota</option></select></div>
<div class="col-md-6 mb-16"><label class="form-label">Kecamatan</label><select name="district_code" id="district-code" class="form-select" required><option value="">Pilih kecamatan</option></select></div>
<div class="col-md-6 mb-16"><label class="form-label">Desa / Kelurahan</label><select name="village_code" id="village-code" class="form-select"><option value="">Pilih desa/kelurahan (opsional)</option></select></div>
<?php $detailFields = $profileType === 'asn' ? ['identity_type' => 'Jenis Identitas', 'identity_number' => 'NIP / NI PPPK', 'asn_status' => 'Status ASN', 'institution' => 'Instansi', 'work_unit' => 'Unit Kerja', 'position' => 'Jabatan', 'grade' => 'Golongan', 'pbj_role' => 'Peran PBJ'] : ($profileType === 'provider' ? ['nib' => 'NIB', 'business_name' => 'Nama Perusahaan / Usaha', 'responsible_person' => 'Penanggung Jawab', 'company_email' => 'Email Perusahaan', 'company_phone' => 'Telepon Perusahaan'] : ($profileType === 'academic_practitioner' ? ['institution' => 'Instansi / Universitas / Organisasi', 'expertise' => 'Bidang Keahlian', 'email_contact' => 'Email Kontak', 'phone_contact' => 'Telepon Kontak'] : ['nik' => 'NIK', 'email_contact' => 'Email Kontak', 'phone_contact' => 'Telepon Kontak'])); ?>
<?php foreach ($detailFields as $field => $label): ?><div class="col-md-6 mb-16"><label class="form-label"><?= $label ?></label><input name="<?= $field ?>" class="form-control" value="<?= esc($detail[$field] ?? '') ?>" <?= in_array($field, ['identity_number', 'nib', 'nik'], true) ? 'required' : '' ?>></div><?php endforeach; ?>
</div><button class="btn btn-primary px-24 py-12 radius-8">Simpan Profil</button></form></div></div>
<div class="col-lg-4"><div class="card p-24 radius-12"><h6 class="mb-24">Ubah Password</h6><form action="<?= route_to('profile.password.update') ?>" method="post"><?= csrf_field() ?><div class="mb-16"><label class="form-label">Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div><div class="mb-16"><label class="form-label">Password Baru</label><input type="password" name="password" class="form-control" minlength="8" required></div><div class="mb-24"><label class="form-label">Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div><button class="btn btn-outline-primary px-24 py-12 radius-8">Ubah Password</button></form></div></div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
async function loadProfileRegions(parent, target, placeholder, selected) {
    if (!parent) return;
    const response = await fetch('<?= route_to('reference.wilayah.children', 'CODE') ?>'.replace('CODE', encodeURIComponent(parent)));
    if (!response.ok) return;
    target.innerHTML = '<option value="">' + placeholder + '</option>';
    (await response.json()).forEach(region => target.add(new Option(region.nama, region.kode, false, region.kode === selected)));
    target.disabled = false;
}
const provinceCode = document.getElementById('province-code');
const regencyCode = document.getElementById('regency-code');
const districtCode = document.getElementById('district-code');
const villageCode = document.getElementById('village-code');
provinceCode.addEventListener('change', () => loadProfileRegions(provinceCode.value, regencyCode, 'Pilih kabupaten/kota', ''));
regencyCode.addEventListener('change', () => loadProfileRegions(regencyCode.value, districtCode, 'Pilih kecamatan', ''));
districtCode.addEventListener('change', () => loadProfileRegions(districtCode.value, villageCode, 'Pilih desa/kelurahan (opsional)', ''));
loadProfileRegions(provinceCode.value, regencyCode, 'Pilih kabupaten/kota', '<?= esc($profile['regency_code'] ?? '') ?>').then(() => loadProfileRegions(regencyCode.value, districtCode, 'Pilih kecamatan', '<?= esc($profile['district_code'] ?? '') ?>')).then(() => loadProfileRegions(districtCode.value, villageCode, 'Pilih desa/kelurahan (opsional)', '<?= esc($profile['village_code'] ?? '') ?>'));
</script>
<?= $this->endSection(); ?>
