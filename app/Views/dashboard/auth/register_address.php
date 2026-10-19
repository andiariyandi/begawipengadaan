<?= $this->extend('dashboard/auth/layouts/auth') ?>

<?= $this->section('content') ?>
<div class="mb-32">
    <a href="<?= site_url('/') ?>" class="mb-24 d-inline-block text-decoration-none">
        <span class="auth-brand-text">BegawiPengadaan</span>
        <span class="auth-brand-subtext">Pemerintah Provinsi Lampung</span>
    </a>
    <h4 class="mb-8 fw-bold text-primary-light"><?= esc($pageTitle ?? 'Wilayah dan Alamat Ringkas') ?></h4>
    <p class="mb-0 text-secondary-light text-sm"><?= esc($pageDescription ?? 'Langkah 2 dari 4 — Tentukan provinsi, kabupaten/kota, dan alamat domisili Anda.') ?></p>
</div>

<?= view('dashboard/auth/components/register_steps', ['currentStep' => $currentStep ?? 2, 'currentKey' => $currentKey ?? 'wilayah']) ?>

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
            <strong class="text-sm">Kendala Validasi Wilayah:</strong>
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
$old = session()->getFlashdata('_ci_old_input')['post'] ?? [];
$registrationAddress = $registrationAddress ?? [];
$value = static fn(string $key): string => esc($old[$key] ?? $registrationAddress[$key] ?? '');
?>
<form action="<?= site_url('dashboard/daftar/wilayah') ?>" method="post" id="registration-address-form">
    <?= csrf_field() ?>
    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="province">Provinsi <span class="text-danger">*</span></label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="province" id="province" data-selected="<?= $value('province') ?>" required>
            <option value="">Pilih provinsi</option>
            <?php foreach (($provinces ?? []) as $province): ?><option value="<?= esc($province['kode']) ?>" <?= ($old['province'] ?? '') === $province['kode'] ? 'selected' : '' ?>><?= esc($province['nama']) ?></option><?php endforeach; ?>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="regency">Kabupaten/Kota <span class="text-danger">*</span></label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="regency" id="regency" data-selected="<?= $value('regency') ?>" disabled required>
            <option value="">Pilih kabupaten/kota</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="district">Kecamatan <span class="text-danger">*</span></label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="district" id="district" data-selected="<?= $value('district') ?>" disabled required>
            <option value="">Pilih kecamatan</option>
        </select>
    </div>

    <div class="mb-16">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="village">Desa/Kelurahan <span class="text-secondary-light fw-normal">(Opsional)</span></label>
        <select class="form-select h-56-px bg-neutral-50 radius-12" name="village" id="village" data-selected="<?= $value('village') ?>" disabled>
            <option value="">Pilih desa/kelurahan</option>
        </select>
    </div>

    <div class="mb-24">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="address">Alamat Ringkas</label>
        <textarea class="form-control bg-neutral-50 radius-12" id="address" name="address" rows="3" placeholder="Nama jalan, nomor, RT/RW, lingkungan, atau keterangan domisili"><?= $value('address') ?></textarea>
    </div>

    <div class="d-flex gap-12 mt-32">
        <a href="<?= site_url('dashboard/daftar') ?>" class="btn btn-outline-neutral-300 text-sm px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
            <span>Kembali</span>
        </a>
        <button type="submit" class="btn btn-primary text-sm fw-semibold px-12 py-16 w-50 radius-12 text-center d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <span>Lanjut: Profil</span>
            <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
        </button>
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
            const response = await fetch(endpoint + '/' + encodeURIComponent(parentCode), {
                headers: {
                    'Accept': 'application/json'
                }
            });
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