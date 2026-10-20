<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<!-- Page Header & Action -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Profil Saya') ?></h6>
        <p class="text-secondary-light mb-0">Kelola informasi akun, data identitas, dan pengaturan keamanan pribadi Anda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/profile/edit') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-new-square-bold" class="text-lg"></iconify-icon>
            <span>Ubah Profil</span>
        </a>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="card mb-24">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link active px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile') ?>">
                    <iconify-icon icon="solar:user-bold" class="text-lg"></iconify-icon>
                    <span>Ringkasan Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary-light px-20 py-10 radius-8 fw-semibold d-inline-flex align-items-center gap-2" href="<?= site_url('dashboard/profile/edit') ?>">
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

<!-- Main Profile Content Grid -->
<div class="row gy-4">
    <!-- Col 1: Profile Summary Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card h-100">
            <div class="card-body p-24 text-center">
                <div class="position-relative d-inline-block">
                    <span class="w-96-px h-96-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-3xl fw-bold border border-2 border-primary-100 shadow-sm">
                        <?= esc($initials ?? 'AD') ?>
                    </span>
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success-600 border border-white border-2 rounded-circle" title="Akun Aktif" style="width: 18px; height: 18px;"></span>
                </div>
                
                <h5 class="mt-16 mb-4 fw-bold text-primary-light"><?= esc($user['name'] ?? 'Administrator') ?></h5>
                <p class="text-secondary-light mb-12 text-sm">@<?= esc($user['username'] ?? 'admin') ?></p>
                
                <div class="d-flex align-items-center justify-content-center gap-2 mb-16 flex-wrap">
                    <?= view('dashboard/components/status_badge', [
                        'status' => $user['account_status'] ?? 'active',
                        'label'  => ($user['account_status'] ?? 'active') === 'active' ? 'Akun Aktif' : ucfirst((string) ($user['account_status'] ?? 'active')),
                    ]) ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle radius-20 text-xs px-2 py-1">
                        <?= esc(strtoupper((string) ($user['profile_type'] ?? 'ASN'))) ?>
                    </span>
                </div>

                <!-- Roles Pills -->
                <div class="d-flex flex-wrap justify-content-center gap-1 mb-20">
                    <?php foreach ($roles as $r): ?>
                        <span class="badge bg-neutral-100 text-primary-light border text-xs px-2 py-1">
                            <iconify-icon icon="solar:shield-check-bold" class="text-primary me-1"></iconify-icon>
                            <?= esc($r['name'] ?? 'Administrator') ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <hr class="my-20">

                <!-- User Meta Information -->
                <div class="text-start d-flex flex-column gap-16">
                    <div>
                        <p class="text-secondary-light text-xs text-uppercase tracking-wider fw-semibold mb-4">Email Resmi</p>
                        <h6 class="text-sm fw-semibold mb-0 text-break text-primary-light d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:letter-bold" class="text-primary"></iconify-icon>
                            <?= esc($user['email'] ?? 'admin@begawipbj.go.id') ?>
                        </h6>
                    </div>
                    <div>
                        <p class="text-secondary-light text-xs text-uppercase tracking-wider fw-semibold mb-4">Nomor Kontak / WhatsApp</p>
                        <h6 class="text-sm fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:phone-bold" class="text-success"></iconify-icon>
                            <?= esc(! empty($user['phone']) ? $user['phone'] : '+62 851-1777-9783') ?>
                        </h6>
                    </div>
                    <div>
                        <p class="text-secondary-light text-xs text-uppercase tracking-wider fw-semibold mb-4">Bergabung Sejak</p>
                        <h6 class="text-sm fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:calendar-bold" class="text-warning"></iconify-icon>
                            <?= ! empty($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : '01 Januari 2026' ?>
                        </h6>
                    </div>
                    <div>
                        <p class="text-secondary-light text-xs text-uppercase tracking-wider fw-semibold mb-4">Sesi Terakhir</p>
                        <h6 class="text-sm fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:clock-circle-bold" class="text-info"></iconify-icon>
                            <?= ! empty($user['last_login_at']) ? date('d F Y, H:i', strtotime($user['last_login_at'])) . ' WIB' : 'Sesi Aktif' ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Col 2: Profile Detail and Security Cards -->
    <div class="col-xl-8 col-lg-7">
        <!-- Informasi Profil Card -->
        <div class="card">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-12 mb-20 pb-12 border-bottom">
                    <div>
                        <h6 class="text-lg fw-bold mb-1">Informasi Identitas &amp; Kedinasan</h6>
                        <p class="text-secondary-light text-xs mb-0">Rincian data akun dan penetapan unit kerja pengguna.</p>
                    </div>
                    <a href="<?= site_url('dashboard/profile/edit') ?>" class="btn btn-sm btn-outline-primary radius-8 px-14 py-6 d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:pen-2-bold"></iconify-icon>
                        <span>Ubah Data</span>
                    </a>
                </div>

                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Nama Lengkap</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light"><?= esc($user['name'] ?? 'Administrator') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Nama Pengguna (Username)</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light"><?= esc($user['username'] ?? 'admin') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Unit Kerja / OPD</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light"><?= esc($unitName ?? 'Biro Pengadaan Barang dan Jasa') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Jabatan Kedinasan</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light"><?= esc($positionName ?? 'Administrator Platform') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Peran Akses Utama</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light">
                            <?= esc(! empty($roles[0]['name']) ? $roles[0]['name'] : 'Administrator Sistem') ?>
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Wilayah Tugas</p>
                        <h6 class="text-sm fw-bold mb-0 text-primary-light">Pemerintah Provinsi Lampung</h6>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light text-xs mb-4">Alamat Kantor / Domisili</p>
                        <h6 class="text-sm fw-semibold mb-0 text-primary-light">
                            <?= esc(! empty($profile['address_line']) ? $profile['address_line'] : 'Kompleks Kantor Gubernur Lampung, Jl. Wolter Monginsidi No. 69, Bandar Lampung') ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keamanan Akun Card -->
        <div class="card mt-24">
            <div class="card-body p-24">
                <div class="mb-20 pb-12 border-bottom">
                    <h6 class="text-lg fw-bold mb-1">Keamanan &amp; Preferensi Akun</h6>
                    <p class="text-secondary-light text-xs mb-0">Kelola kredensial akses dan kustomisasi dashboard.</p>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 pb-20 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-44-px h-44-px rounded-12 bg-warning-50 text-warning-600 d-inline-flex align-items-center justify-content-center text-xl flex-shrink-0">
                            <iconify-icon icon="solar:lock-password-bold"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="text-sm fw-bold mb-2">Kata Sandi Akun</h6>
                            <p class="text-secondary-light text-xs mb-0">Terlindungi dengan enkripsi standar industri. Disarankan memperbarui kata sandi secara berkala.</p>
                        </div>
                    </div>
                    <a href="<?= site_url('dashboard/profile/password') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-8 text-xs fw-semibold">
                        Ubah Kata Sandi
                    </a>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 pt-20">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-44-px h-44-px rounded-12 bg-info-50 text-info-600 d-inline-flex align-items-center justify-content-center text-xl flex-shrink-0">
                            <iconify-icon icon="solar:settings-bold"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="text-sm fw-bold mb-2">Preferensi &amp; Notifikasi Pribadi</h6>
                            <p class="text-secondary-light text-xs mb-0">Atur bahasa tampilan, tema antarmuka (terang/gelap), dan notifikasi sistem.</p>
                        </div>
                    </div>
                    <a href="<?= site_url('dashboard/profile/preferences') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-8 text-xs fw-semibold">
                        Atur Preferensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>