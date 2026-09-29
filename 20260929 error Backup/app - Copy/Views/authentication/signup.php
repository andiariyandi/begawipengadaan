<!DOCTYPE html>
<html lang="id" data-theme="light">
<?= $this->include('partials/head') ?>
<body>
<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center w-100">
        <div class="max-w-700-px mx-auto w-100">
            <h4 class="mb-12">Buat Akun Begawipbj</h4>
            <p class="mb-24 text-secondary-light">Pilih satu kategori profil utama. Role aplikasi dapat ditambahkan kemudian oleh administrator.</p>
            <form action="<?= route_to('register.attempt') ?>" method="post">
                <?= csrf_field() ?>
                <?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endif; ?>
                <?php if ($errors = session()->getFlashdata('errors')): ?><div class="alert alert-danger"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?>
                <div class="mb-16"><label class="form-label">Kategori Profil</label><select name="profile_type" id="profile-type" class="form-select" required><option value="">Pilih kategori</option><?php foreach ($options->profileTypes as $value => $label): ?><option value="<?= esc($value) ?>" <?= old('profile_type') === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></div>
                <div class="row">
                    <div class="col-md-6 mb-16"><label class="form-label">Username</label><input name="username" class="form-control" required value="<?= esc(old('username')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Email Akun</label><input type="email" name="email" class="form-control" required value="<?= esc(old('email')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Nama Lengkap</label><input name="full_name" class="form-control" required value="<?= esc(old('full_name')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Gelar Depan</label><input name="title_prefix" class="form-control" value="<?= esc(old('title_prefix')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Gelar Belakang</label><input name="title_suffix" class="form-control" value="<?= esc(old('title_suffix')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">No. HP / WhatsApp</label><input name="phone" class="form-control" required value="<?= esc(old('phone')) ?>"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Provinsi Domisili</label><select name="province_code" id="province-code" class="form-select" required><option value="">Pilih provinsi</option><?php foreach ($provinces as $region): ?><option value="<?= esc($region['kode']) ?>"><?= esc($region['nama']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Kabupaten / Kota</label><select name="regency_code" id="regency-code" class="form-select" required disabled><option value="">Pilih kabupaten/kota</option></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Kecamatan</label><select name="district_code" id="district-code" class="form-select" required disabled><option value="">Pilih kecamatan</option></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Desa / Kelurahan</label><select name="village_code" id="village-code" class="form-select" disabled><option value="">Pilih desa/kelurahan (opsional)</option></select></div>
                </div>
                <div id="profile-asn" class="profile-fields row d-none">
                    <div class="col-md-6 mb-16"><label class="form-label">Jenis Identitas ASN</label><select name="identity_type" class="form-select"><?php foreach ($options->identityTypes as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">NIP / NI PPPK</label><input name="identity_number" class="form-control"></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Status ASN</label><select name="asn_status" class="form-select"><?php foreach ($options->asnStatuses as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Instansi</label><select name="institution" class="form-select"><?php foreach ($options->institutions as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Unit Kerja</label><select name="work_unit" class="form-select"><?php foreach ($options->workUnits as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Jabatan</label><select name="position" class="form-select"><?php foreach ($options->positions as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Golongan</label><select name="grade" class="form-select"><option value="">Pilih</option><?php foreach ($options->grades as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6 mb-16"><label class="form-label">Peran PBJ (opsional)</label><select name="pbj_role" class="form-select"><option value="">Tidak ada</option><?php foreach ($options->pbjRoles as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div>
                </div>
                <div id="profile-provider" class="profile-fields row d-none"><?php foreach (['nib' => 'NIB', 'business_name' => 'Nama Perusahaan / Usaha', 'responsible_person' => 'Nama Penanggung Jawab', 'company_email' => 'Email Perusahaan', 'company_phone' => 'No. HP / WhatsApp Perusahaan'] as $name => $label): ?><div class="col-md-6 mb-16"><label class="form-label"><?= $label ?></label><input name="<?= $name ?>" class="form-control" <?= in_array($name, ['company_email'], true) ? 'type="email"' : '' ?>></div><?php endforeach; ?></div>
                <div id="profile-academic_practitioner" class="profile-fields row d-none"><div class="col-md-6 mb-16"><label class="form-label">Instansi / Universitas / Organisasi</label><select name="institution" class="form-select"><?php foreach ($options->institutions as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div><div class="col-md-6 mb-16"><label class="form-label">Bidang Keahlian</label><select name="expertise" class="form-select"><?php foreach ($options->expertise as $value): ?><option><?= esc($value) ?></option><?php endforeach; ?></select></div><div class="col-md-6 mb-16"><label class="form-label">Email Kontak</label><input type="email" name="email_contact" class="form-control"></div><div class="col-md-6 mb-16"><label class="form-label">No. HP / WhatsApp</label><input name="phone_contact" class="form-control"></div></div>
                <div id="profile-public" class="profile-fields row d-none"><div class="col-md-6 mb-16"><label class="form-label">NIK</label><input name="nik" class="form-control"></div><div class="col-md-6 mb-16"><label class="form-label">Email Kontak</label><input type="email" name="email_contact" class="form-control"></div><div class="col-md-6 mb-16"><label class="form-label">No. HP / WhatsApp</label><input name="phone_contact" class="form-control"></div></div>
                <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Daftar</button>
                <a href="<?= route_to('signin') ?>" class="btn btn-outline-secondary px-24 py-12 radius-8">Sudah punya akun</a>
            </form>
        </div>
    </div>
</section>
<script>
const type = document.getElementById('profile-type');
function toggleProfile() { document.querySelectorAll('.profile-fields').forEach(el => el.classList.add('d-none')); const active = document.getElementById('profile-' + type.value); if (active) active.classList.remove('d-none'); }
type.addEventListener('change', toggleProfile); toggleProfile();
async function loadRegions(parent, target, placeholder) {
    target.innerHTML = '<option value="">Memuat...</option>';
    target.disabled = true;
    if (!parent) return;
    const response = await fetch('<?= route_to('reference.wilayah.children', 'CODE') ?>'.replace('CODE', encodeURIComponent(parent)));
    if (!response.ok) return;
    target.innerHTML = '<option value="">' + placeholder + '</option>';
    (await response.json()).forEach(region => target.add(new Option(region.nama, region.kode)));
    target.disabled = false;
}
document.getElementById('province-code').addEventListener('change', event => {
    document.getElementById('district-code').innerHTML = '<option value="">Pilih kecamatan</option>';
    document.getElementById('district-code').disabled = true;
    document.getElementById('village-code').innerHTML = '<option value="">Pilih desa/kelurahan (opsional)</option>';
    document.getElementById('village-code').disabled = true;
    loadRegions(event.target.value, document.getElementById('regency-code'), 'Pilih kabupaten/kota');
});
document.getElementById('regency-code').addEventListener('change', event => loadRegions(event.target.value, document.getElementById('district-code'), 'Pilih kecamatan'));
document.getElementById('district-code').addEventListener('change', event => loadRegions(event.target.value, document.getElementById('village-code'), 'Pilih desa/kelurahan (opsional)'));
</script>
<?= $this->include('partials/scripts') ?>
</body>
</html>
