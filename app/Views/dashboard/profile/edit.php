<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ubah Profil') ?></h6>
        <p class="text-secondary-light mb-0">Perbarui informasi identitas, kontak, dan penugasan kedinasan akun Anda.</p>
    </div>
    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Kembali ke Profil</span>
    </a>
</div>

<!-- Navigation Tabs -->
<div class="card mb-24">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile') ?>">
                    <iconify-icon icon="solar:user-bold" class="text-lg"></iconify-icon>
                    <span>Ringkasan Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/edit') ?>">
                    <iconify-icon icon="solar:pen-new-square-bold" class="text-lg"></iconify-icon>
                    <span>Ubah Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/password') ?>">
                    <iconify-icon icon="solar:lock-password-bold" class="text-lg"></iconify-icon>
                    <span>Ubah Kata Sandi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/preferences') ?>">
                    <iconify-icon icon="solar:settings-bold" class="text-lg"></iconify-icon>
                    <span>Preferensi Akun</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 mb-24 radius-8" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="text-2xl text-success-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1">
            <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 mb-24 radius-8" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl text-danger-main flex-shrink-0"></iconify-icon>
        <div class="flex-grow-1">
            <strong>Perhatian:</strong> <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-24 radius-8" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl text-danger-main"></iconify-icon>
            <strong>Terdapat kesalahan pada input Anda:</strong>
        </div>
        <ul class="mb-0 ps-20 text-sm">
            <?php foreach (session()->getFlashdata('errors') as $fieldErr): ?>
                <li><?= esc($fieldErr) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Edit Profile Form Card -->
<div class="card">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/profile/edit') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Avatar Header Block -->
            <div class="d-flex flex-wrap align-items-center gap-20 p-20 bg-neutral-50 border radius-12 mb-24">
                <span class="w-80-px h-80-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-2xl fw-bold border border-2 border-primary-200 flex-shrink-0 shadow-sm">
                    <?= esc($initials ?? 'AD') ?>
                </span>
                <div class="flex-grow-1">
                    <h6 class="text-base fw-bold mb-1"><?= esc($user['name'] ?? 'Administrator') ?></h6>
                    <p class="text-secondary-light text-xs mb-2">ID Akun #<?= esc($user['id'] ?? 1) ?> &middot; Status: <span class="text-success fw-semibold"><?= esc(ucfirst((string) ($user['account_status'] ?? 'Aktif'))) ?></span></p>
                    <span class="badge bg-white text-primary border text-2xs px-2 py-1 radius-4">
                        Inisial Otomatis dari Nama Lengkap
                    </span>
                </div>
            </div>

            <div class="row gy-20">
                <!-- Nama Lengkap -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputName">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <div class="position-relative">
                        <input type="text" 
                               id="inputName" 
                               name="name" 
                               class="form-control radius-8 <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                               value="<?= esc(old('name', $user['name'] ?? '')) ?>" 
                               placeholder="Masukkan nama lengkap beserta gelar jika ada" 
                               required>
                    </div>
                    <?php if (session('errors.name')): ?>
                        <div class="text-danger text-2xs mt-1"><?= session('errors.name') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nama Pengguna -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputUsername">
                        Nama Pengguna (Username) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-100 text-secondary-light border-end-0 radius-8-start">@</span>
                        <input type="text" 
                               id="inputUsername" 
                               name="username" 
                               class="form-control radius-8-end <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                               value="<?= esc(old('username', $user['username'] ?? '')) ?>" 
                               placeholder="username" 
                               required>
                    </div>
                    <?php if (session('errors.username')): ?>
                        <div class="text-danger text-2xs mt-1"><?= session('errors.username') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Alamat Email -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputEmail">
                        Alamat Email Kedinasan <span class="text-danger">*</span>
                    </label>
                    <div class="position-relative">
                        <input type="email" 
                               id="inputEmail" 
                               name="email" 
                               class="form-control radius-8 <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               value="<?= esc(old('email', $user['email'] ?? '')) ?>" 
                               placeholder="email@begawipbj.go.id" 
                               required>
                    </div>
                    <?php if (session('errors.email')): ?>
                        <div class="text-danger text-2xs mt-1"><?= session('errors.email') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nomor Telepon / WA -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputPhone">
                        Nomor Kontak / WhatsApp
                    </label>
                    <div class="position-relative">
                        <input type="text" 
                               id="inputPhone" 
                               name="phone" 
                               class="form-control radius-8 <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                               value="<?= esc(old('phone', $user['phone'] ?? '')) ?>" 
                               placeholder="08xxxxxxxxxx">
                    </div>
                    <?php if (session('errors.phone')): ?>
                        <div class="text-danger text-2xs mt-1"><?= session('errors.phone') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Unit Kerja / OPD -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputUnit">
                        Unit Kerja / Instansi / OPD
                    </label>
                    <div class="position-relative">
                        <input type="text" 
                               id="inputUnit" 
                               name="unit_name" 
                               class="form-control radius-8" 
                               value="<?= esc(old('unit_name', $unitName ?? 'Biro Pengadaan Barang dan Jasa')) ?>" 
                               placeholder="Unit Kerja / Instansi">
                    </div>
                    <small class="text-secondary-light text-2xs">Satuan kerja penugasan pada Pemerintah Provinsi Lampung.</small>
                </div>

                <!-- Jabatan Kedinasan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputPosition">
                        Jabatan Kedinasan
                    </label>
                    <div class="position-relative">
                        <input type="text" 
                               id="inputPosition" 
                               name="position_name" 
                               class="form-control radius-8" 
                               value="<?= esc(old('position_name', $positionName ?? 'Administrator Platform')) ?>" 
                               placeholder="Jabatan / Posisi">
                    </div>
                    <small class="text-secondary-light text-2xs">Posisi fungsional atau struktural yang diemban.</small>
                </div>

                <!-- Alamat Domisili / Kantor -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="inputAddress">
                        Alamat Kantor / Domisili
                    </label>
                    <textarea id="inputAddress" 
                              name="address_line" 
                              class="form-control radius-8" 
                              rows="3" 
                              placeholder="Masukkan alamat instansi atau domisili tugas"><?= esc(old('address_line', $profile['address_line'] ?? 'Kompleks Kantor Gubernur Lampung, Jl. Wolter Monginsidi No. 69, Bandar Lampung')) ?></textarea>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="col-12 d-flex justify-content-end gap-12 pt-16 border-top">
                    <a href="<?= site_url('dashboard/profile') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>