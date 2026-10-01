<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div>
    <a href="<?= site_url('/') ?>" class="mb-32 d-inline-block text-decoration-none"><span class="auth-brand-text">BegawiPBJ</span><span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span></a>
    <h4 class="mb-8"><?= esc($pageTitle ?? 'Wilayah dan Alamat Ringkas') ?></h4>
    <p class="mb-24 text-secondary-light text-md"><?= esc($pageDescription ?? '') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 2, 'currentKey' => $currentKey ?? 'wilayah']) ?>

<?php if ($errors = session()->getFlashdata('errors')): ?><div class="alert alert-danger mb-20"><?= esc(implode(' ', $errors)) ?></div><?php endif; ?>
<?php
$old = session()->getFlashdata('_ci_old_input')['post'] ?? [];
$registrationAddress = $registrationAddress ?? [];
$value = static fn (string $key): string => esc($old[$key] ?? $registrationAddress[$key] ?? '');
?>
<form action="<?= site_url('dashboard/daftar/wilayah') ?>" method="post" id="registration-address-form">
    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="province" id="province" data-selected="<?= $value('province') ?>" required>
        <option value="">Pilih provinsi</option>
        <?php foreach (($provinces ?? []) as $province): ?><option value="<?= esc($province['kode']) ?>" <?= ($old['province'] ?? '') === $province['kode'] ? 'selected' : '' ?>><?= esc($province['nama']) ?></option><?php endforeach; ?>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten/Kota</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="regency" id="regency" data-selected="<?= $value('regency') ?>" disabled required>
        <option value="">Pilih kabupaten/kota</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan</label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="district" id="district" data-selected="<?= $value('district') ?>" disabled required>
        <option value="">Pilih kecamatan</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Desa/Kelurahan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
    <select class="form-select h-56-px bg-neutral-50 radius-12 mb-16" name="village" id="village" data-selected="<?= $value('village') ?>" disabled>
        <option value="">Pilih desa/kelurahan</option>
    </select>

    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Ringkas</label>
    <textarea class="form-control bg-neutral-50 radius-12" name="address" rows="3" placeholder="Nama jalan, nomor, lingkungan, atau keterangan alamat"><?= $value('address') ?></textarea>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center">Kembali</a>
        <button type="submit" class="btn btn-primary text-sm px-12 py-16 w-50 radius-12 w-50">Lanjut</button>
    </div>

</form>

<script>
(() => {
    const endpoint = <?= json_encode(site_url('dashboard/daftar/wilayah/options')) ?>;
    const selects = [
        document.getElementById('province'),
        document.getElementById('regency'),
        document.getElementById('district'),
        document.getElementById('village')
    ];
    const labels = ['Pilih provinsi', 'Pilih kabupaten/kota', 'Pilih kecamatan', 'Pilih desa/kelurahan'];

    async function loadChildren(index, parentCode, selectedCode) {
        const select = selects[index];
        if (!select) return;
        select.innerHTML = '<option value="">' + labels[index] + '</option>';
        select.disabled = !parentCode;
        if (!parentCode) return;
        const response = await fetch(endpoint + '/' + encodeURIComponent(parentCode), {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error('Wilayah tidak dapat dimuat.');
        const rows = await response.json();
        rows.forEach((row) => {
            const option = new Option(row.name, row.code, false, row.code === selectedCode);
            select.add(option);
        });
        select.disabled = false;
    }

    selects.forEach((select, index) => {
        select.addEventListener('change', async () => {
            for (let child = index + 1; child < selects.length; child++) {
                selects[child].innerHTML = '<option value="">' + labels[child] + '</option>';
                selects[child].disabled = true;
            }
            if (index >= selects.length - 1) return;
            try {
                await loadChildren(index + 1, select.value, '');
            } catch (error) {
                select.setCustomValidity(error.message);
            }
        });
    });

    (async () => {
        try {
            await loadChildren(1, selects[0].value, selects[1].dataset.selected);
            await loadChildren(2, selects[1].value, selects[2].dataset.selected);
            await loadChildren(3, selects[2].value, selects[3].dataset.selected);
        } catch (error) {
            selects[0].setCustomValidity(error.message);
        }
    })();
})();
</script>

<?= $this->endSection() ?>

<!-- switch ($profileType) {
case 'asn':
return redirect()->to(site_url('dashboard/daftar/profil/asn'));

case 'provider':
return redirect()->to(site_url('dashboard/daftar/profil/penyedia'));

case 'academic':
return redirect()->to(site_url('dashboard/daftar/profil/akademisi'));

default:
return redirect()->to(site_url('dashboard/daftar/profil/masyarakat'));
} -->