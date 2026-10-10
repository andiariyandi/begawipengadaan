<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php $user = $user ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Pengguna</h6>
        <p class="text-secondary-light mb-0">
            ID Pengguna: <?= esc($userId ?? '-') ?>
        </p>
    </div>

    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/akses/pengguna') ?>"
           class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <a href="<?= site_url('dashboard/akses/pengguna/' . ($userId ?? '1') . '/ubah') ?>"
           class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Pengguna</a>
    </div>
</div>


<div class="row gy-4">

    <!-- Summary User -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24 text-center">

                <span class="w-96-px h-96-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-3xl fw-bold">
                    <?= esc(strtoupper(substr($user['name'] ?? $user['username'] ?? 'U', 0, 2))) ?>
                </span>

                <h5 class="mt-16 mb-4">
                    <?= esc($user['name'] ?? $user['username'] ?? '-') ?>
                </h5>

                <p class="text-secondary-light mb-16">
                    <?= esc($user['email'] ?? '-') ?>
                </p>

                <?= view('dashboard/components/status_badge', [
                    'status' => $user['account_status'] ?? 'active'
                ]) ?>


                <hr class="my-24">


                <div class="text-start">

                    <p class="mb-8 text-secondary-light">
                        Tipe Profil
                    </p>

                    <h6 class="mb-16">
                        <?= esc($user['profile_type'] ?? '-') ?>
                    </h6>


                    <p class="mb-8 text-secondary-light">
                        Role Aktif
                    </p>

                    <h6 class="mb-0">
                        <?= esc($user['roles'] ?? '-') ?>
                    </h6>

                </div>

            </div>
        </div>
    </div>



    <!-- Detail Account -->
    <div class="col-xl-8">

        <div class="card">

            <div class="card-body p-24">

                <h6 class="text-lg mb-20">
                    Informasi Akun
                </h6>


                <div class="row gy-20">


                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Nama Lengkap
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['name'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Username
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['username'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Email
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['email'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Nomor Telepon
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['phone'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Status Email
                        </p>

                        <h6 class="text-md mb-0">
                            <?= !empty($user['email_verified_at'])
                                ? 'Terverifikasi'
                                : 'Belum terverifikasi'
                            ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Bergabung Sejak
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['created_at'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Status Akun
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['account_status'] ?? '-') ?>
                        </h6>
                    </div>



                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">
                            Login Terakhir
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['last_login_at'] ?? '-') ?>
                        </h6>

                    </div>

                    <div class="col-md-12">
                        <p class="text-secondary-light mb-4">
                            Wilayah
                        </p>

                        <h6 class="text-md mb-0">
                            <?= esc($user['province_name'] ?? '-') ?>
                            <?php if (!empty($user['regency_name'])): ?>
                                <br><?= esc($user['regency_name']) ?>
                            <?php endif; ?>

                            <?php if (!empty($user['district_name'])): ?>
                                <br><?= esc($user['district_name']) ?>
                            <?php endif; ?>

                            <?php if (!empty($user['village_name'])): ?>
                                <br><?= esc($user['village_name']) ?>
                            <?php endif; ?>
                        </h6>
                    </div>
                </div>

            </div>

        </div>

    </div>


</div>

<!-- Action panel -->
<div class="row gy-4 mt-16">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Tindakan Akun</h6>
                <div class="row gy-16">

                    <?php if (($user['account_status'] ?? '') !== 'active'): ?>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/aktifkan') ?>"
                                data-method="POST"
                                data-title="Aktifkan Akun Pengguna"
                                data-message="Apakah Anda yakin ingin mengaktifkan akun <?= esc($user['name'] ?? $user['username']) ?>?"
                                data-btn-text="Ya, Aktifkan"
                                data-btn-class="btn-success-600"
                                data-icon="solar:user-check-rounded-bold"
                                data-icon-color="success">
                            <iconify-icon icon="solar:check-circle-outline" class="me-6"></iconify-icon>
                            Aktifkan Akun
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (($user['account_status'] ?? '') !== 'suspended'): ?>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/suspend') ?>"
                                data-method="POST"
                                data-title="Tangguhkan Akun Pengguna"
                                data-message="Apakah Anda yakin ingin menangguhkan akun pengguna ini? Pengguna tidak akan dapat mengakses platform."
                                data-btn-text="Ya, Tangguhkan"
                                data-btn-class="btn-warning-600"
                                data-icon="solar:user-block-rounded-bold"
                                data-icon-color="warning"
                                data-require-input="true"
                                data-input-label="Alasan Penangguhan *"
                                data-input-name="reason"
                                data-input-placeholder="Tulis alasan penangguhan akun...">
                            <iconify-icon icon="solar:pause-circle-outline" class="me-6"></iconify-icon>
                            Suspend Akun
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-info-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/reset-password') ?>"
                                data-method="POST"
                                data-title="Kirim Reset Password"
                                data-message="Apakah Anda yakin ingin mengirim permintaan reset password untuk akun <?= esc($user['name'] ?? $user['username']) ?>?"
                                data-btn-text="Kirim Reset"
                                data-btn-class="btn-info-600"
                                data-icon="solar:lock-password-bold"
                                data-icon-color="info">
                            <iconify-icon icon="solar:lock-password-outline" class="me-6"></iconify-icon>
                            Kirim Reset Password
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/ubah') ?>" class="btn btn-outline-primary-600 w-100 radius-8">
                            <iconify-icon icon="solar:shield-keyhole-outline" class="me-6"></iconify-icon>
                            Kelola Role (via Edit)
                        </a>
                    </div>

                </div>
                <p class="text-secondary-light text-xs mt-16 mb-0">
                    <iconify-icon icon="solar:info-circle-outline"></iconify-icon>
                    Password tidak dapat diubah langsung oleh admin. Reset password mengirim instruksi ke email pengguna.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>