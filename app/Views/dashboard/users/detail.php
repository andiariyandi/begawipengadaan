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

    <a href="<?= site_url('dashboard/akses/pengguna/' . ($userId ?? '1') . '/ubah') ?>"
        class="btn btn-primary-600 radius-8 px-20 py-11">
        Ubah Pengguna
    </a>
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


<?= $this->endSection() ?>