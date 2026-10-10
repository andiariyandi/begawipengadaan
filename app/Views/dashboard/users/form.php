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
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? ($isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna')) ?></h6>
        <p class="text-secondary-light mb-0">Kelola informasi akun, tipe profil pengadaan, dan hak akses pengguna.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger mb-20 radius-8 p-16">
        <div class="d-flex align-items-start gap-2">
            <iconify-icon icon="solar:danger-circle-outline" class="text-xl flex-shrink-0 mt-2"></iconify-icon>
            <div>
                <strong class="d-block mb-4">Terdapat kesalahan pengisian:</strong>
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

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="text-md mb-0"><?= $isEdit ? 'Formulir Perubahan Data Pengguna' : 'Formulir Pembuatan Akun Baru' ?></h6>
    </div>

    <div class="card-body p-24">
        <form action="<?= esc($actionUrl) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

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
                           placeholder="nama@domain.go.id atau instansi">
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Kata Sandi <?= $isEdit ? '<span class="text-secondary-light fw-normal">(kosongkan jika tidak diubah)</span>' : '<span class="text-danger-main">*</span>' ?>
                    </label>
                    <input name="password" <?= $isEdit ? '' : 'required' ?> type="password" class="form-control radius-8" minlength="12"
                           placeholder="Minimal 12 karakter dengan huruf besar, kecil, angka">
                    <span class="text-secondary-light text-xs mt-4 d-block">Kombinasi huruf besar, huruf kecil, dan angka.</span>
                </div>

                <!-- Profile Type -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Tipe Profil <span class="text-danger-main">*</span>
                    </label>
                    <select name="profile_type" required class="form-select radius-8">
                        <?php foreach (['asn' => 'Aparatur Sipil Negara (ASN)', 'provider' => 'Penyedia Barang/Jasa', 'academic' => 'Akademisi / Peneliti', 'public' => 'Masyarakat Umum'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('profile_type', $user['profile_type'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Account Status -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Status Akun <span class="text-danger-main">*</span>
                    </label>
                    <select name="account_status" required class="form-select radius-8">
                        <?php foreach ([
                            'active'               => 'Aktif',
                            'pending_approval'     => 'Menunggu Persetujuan',
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
                        Role Akses
                    </label>
                    <select name="role_id" class="form-select radius-8">
                        <option value="">— Tanpa Role Spesifik —</option>
                        <?php foreach (($roles ?? []) as $role): ?>
                            <option value="<?= (int) $role['id'] ?>" <?= (string) old('role_id', $user['role_id'] ?? '') === (string) $role['id'] ? 'selected' : '' ?>>
                                <?= esc($role['name']) ?> (<?= esc($role['key']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-secondary-light text-xs mt-4 d-block">Penetapan role platform untuk pengguna ini.</span>
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

                <!-- Buttons -->
                <div class="col-12 d-flex justify-content-end gap-12 mt-16 pt-16 border-top">
                    <a href="<?= site_url('dashboard/akses/pengguna') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-check-outline" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan' : 'Buat Pengguna' ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
