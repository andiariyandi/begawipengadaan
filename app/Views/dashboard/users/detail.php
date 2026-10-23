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

<?php if (session('success')): ?>
    <div class="alert alert-success mb-20 radius-8 p-16 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:check-circle-outline" class="text-xl flex-shrink-0"></iconify-icon>
        <span><?= esc(session('success')) ?></span>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger mb-20 radius-8 p-16 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:danger-circle-outline" class="text-xl flex-shrink-0"></iconify-icon>
        <span><?= esc(session('error')) ?></span>
    </div>
<?php endif; ?>

<?php if (in_array($user['account_status'] ?? '', ['pending_approval', 'pending_verification', 'submitted', 'revision_required'], true)): ?>
    <div class="alert alert-warning mb-24 radius-12 p-20 d-flex flex-wrap align-items-center justify-content-between gap-3 border-warning-200 bg-warning-50">
        <div class="d-flex align-items-center gap-16">
            <div class="w-48-px h-48-px rounded-circle bg-warning-500 text-white d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="text-md fw-semibold text-warning-900 mb-1">Akun &amp; Berkas Profil Menunggu Persetujuan (Verifikasi)</h6>
                <p class="text-sm text-warning-800 mb-0">Pengguna ini sedang menunggu peninjauan administrator. Menyetujui akun akan otomatis mengaktifkan akun dan mengesahkan profil dalam satu proses terpadu.</p>
            </div>
        </div>
        <div class="d-flex gap-8">
            <button type="button" class="btn btn-success-600 radius-8 px-20 py-10 d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmationActionModal"
                    data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/aktifkan') ?>"
                    data-method="POST"
                    data-title="Setujui Akun &amp; Profil Pengguna"
                    data-message="Apakah Anda yakin ingin menyetujui akun dan memverifikasi profil <?= esc($user['name'] ?? $user['username']) ?>? Akun akan langsung aktif dan berkas profil disahkan secara terpadu."
                    data-btn-text="Ya, Setujui Akun & Profil"
                    data-btn-class="btn-success-600"
                    data-icon="solar:user-check-rounded-bold"
                    data-icon-color="success">
                <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                <span>Setujui Akun &amp; Profil</span>
            </button>
            <button type="button" class="btn btn-danger-600 radius-8 px-16 py-10 d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmationActionModal"
                    data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/tolak') ?>"
                    data-method="POST"
                    data-title="Tolak Verifikasi Akun &amp; Profil"
                    data-message="Apakah Anda yakin ingin menolak akun <?= esc($user['name'] ?? $user['username']) ?> beserta berkas profilnya?"
                    data-btn-text="Ya, Tolak"
                    data-btn-class="btn-danger-600"
                    data-require-input="true"
                    data-input-label="Alasan Penolakan *"
                    data-input-name="reason"
                    data-input-placeholder="Tulis alasan penolakan..."
                    data-icon="solar:close-circle-bold"
                    data-icon-color="danger">
                <iconify-icon icon="solar:close-circle-outline" class="text-lg"></iconify-icon>
                <span>Tolak</span>
            </button>
        </div>
    </div>
<?php endif; ?>

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

        <?php if (! empty($typeProfile)): ?>
        <div class="card mt-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between mb-20">
                    <h6 class="text-lg mb-0">
                        Data Profil Spesifik (<?= esc(strtoupper($user['profile_type'] ?? 'Profil')) ?>)
                    </h6>
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-4 text-xs fw-semibold">
                        Verifikasi Terpadu
                    </span>
                </div>
                <div class="row gy-20">
                    <?php foreach ($typeProfile as $key => $value): ?>
                        <?php if (in_array($key, ['id', 'user_id', 'created_at', 'updated_at'], true)) continue; ?>
                        <div class="col-md-6">
                            <p class="text-secondary-light mb-4"><?= esc(ucwords(str_replace('_', ' ', $key))) ?></p>
                            <h6 class="text-md mb-0"><?= esc((is_array($value) ? implode(', ', $value) : (string) $value) ?: '-') ?></h6>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (! empty($verifications)): ?>
        <div class="card mt-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Riwayat Verifikasi Profil Pengguna</h6>
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Waktu Pengajuan</th>
                                <th>Status</th>
                                <th>Ditinjau Oleh</th>
                                <th>Waktu Ditinjau</th>
                                <th>Catatan Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($verifications as $v): ?>
                                <tr>
                                    <td><?= esc(date('d M Y H:i', strtotime($v['created_at']))) ?></td>
                                    <td><?= view('dashboard/components/status_badge', ['status' => $v['status'] ?? 'pending']) ?></td>
                                    <td><?= esc($v['reviewed_by'] ? 'User #' . $v['reviewed_by'] : '-') ?></td>
                                    <td><?= ! empty($v['reviewed_at']) ? esc(date('d M Y H:i', strtotime($v['reviewed_at']))) : '-' ?></td>
                                    <td><?= esc($v['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                                data-title="Setujui Akun &amp; Profil Pengguna"
                                data-message="Apakah Anda yakin ingin menyetujui akun dan memverifikasi profil <?= esc($user['name'] ?? $user['username']) ?>? Akun akan langsung aktif dan berkas profil disahkan secara terpadu."
                                data-btn-text="Ya, Setujui Akun & Profil"
                                data-btn-class="btn-success-600"
                                data-icon="solar:user-check-rounded-bold"
                                data-icon-color="success">
                            <iconify-icon icon="solar:check-circle-outline" class="me-6"></iconify-icon>
                            Setujui Akun &amp; Profil
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array($user['account_status'] ?? '', ['pending_approval', 'pending_verification', 'submitted', 'revision_required'], true)): ?>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/tolak') ?>"
                                data-method="POST"
                                data-title="Tolak Verifikasi Akun &amp; Profil"
                                data-message="Apakah Anda yakin ingin menolak akun <?= esc($user['name'] ?? $user['username']) ?> beserta berkas profilnya?"
                                data-btn-text="Ya, Tolak"
                                data-btn-class="btn-danger-600"
                                data-require-input="true"
                                data-input-label="Alasan Penolakan *"
                                data-input-name="reason"
                                data-input-placeholder="Tulis alasan penolakan..."
                                data-icon="solar:close-circle-bold"
                                data-icon-color="danger">
                            <iconify-icon icon="solar:close-circle-outline" class="me-6"></iconify-icon>
                            Tolak Akun &amp; Profil
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
                                data-input-placeholder="Tulis alasan penangguhan akun...">
                            <iconify-icon icon="solar:pause-circle-outline" class="me-6"></iconify-icon>
                            Suspend Akun
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#adminResetPasswordModal">
                            <iconify-icon icon="solar:key-square-outline" class="me-6"></iconify-icon>
                            Reset Password Langsung
                        </button>
                    </div>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-info-600 w-100 radius-8"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/reset-password') ?>"
                                data-method="POST"
                                data-title="Kirim Reset Password via Email"
                                data-message="Apakah Anda yakin ingin mengirim instruksi tautan reset password ke email <?= esc($user['email'] ?? $user['name'] ?? $user['username']) ?>?"
                                data-btn-text="Kirim Email Reset"
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
                    Administrator dapat melakukan reset kata sandi langsung dengan audit kepatuhan atau mengirimkan tautan instruksi ke email pengguna.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password Administratif Langsung -->
<div class="modal fade" id="adminResetPasswordModal" tabindex="-1" aria-labelledby="adminResetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom px-24 py-16">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-lg">
                        <iconify-icon icon="solar:key-square-bold"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fs-6 fw-semibold mb-0" id="adminResetPasswordModalLabel">Reset Kata Sandi Administratif</h6>
                        <span class="text-xs text-secondary-light">Ubah langsung kata sandi akun pengguna</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= site_url('dashboard/akses/pengguna/' . (int) ($userId ?? 0) . '/reset-password') ?>" method="POST" id="adminResetPasswordForm">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="p-12 radius-8 bg-neutral-50 border mb-20">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-xs text-secondary-light">Nama Akun:</span>
                            <span class="text-xs fw-semibold text-neutral-900"><?= esc($user['name'] ?? $user['username'] ?? '-') ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="text-xs text-secondary-light">Email:</span>
                            <span class="text-xs fw-semibold text-neutral-900"><?= esc($user['email'] ?? '-') ?></span>
                        </div>
                    </div>

                    <div class="mb-20">
                        <label for="adminNewPassword" class="form-label text-sm fw-medium mb-8">Kata Sandi Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control radius-8" id="adminNewPassword" name="new_password" required minlength="8" placeholder="Masukkan kata sandi baru...">
                            <button class="btn btn-outline-neutral-300 px-12" type="button" id="toggleAdminPasswordBtn" title="Lihat/Sembunyikan Kata Sandi">
                                <iconify-icon icon="solar:eye-outline" id="toggleAdminPasswordIcon"></iconify-icon>
                            </button>
                        </div>
                        <span class="text-2xs text-secondary-light mt-4 d-block">Minimal 8 karakter kombinasi huruf dan angka.</span>
                    </div>

                    <div class="mb-20">
                        <label for="adminConfirmPassword" class="form-label text-sm fw-medium mb-8">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control radius-8" id="adminConfirmPassword" name="confirm_password" required minlength="8" placeholder="Ulangi kata sandi baru...">
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 p-12 bg-primary-50 rounded-8 border border-primary-100 mb-20">
                        <span class="text-xs text-primary-700 fw-medium">Buat sandi acak aman otomatis:</span>
                        <button type="button" class="btn btn-xs btn-primary-600 radius-6 px-12 py-6 d-flex align-items-center gap-1 text-xs" id="btnGeneratePassword">
                            <iconify-icon icon="solar:magic-stick-3-outline"></iconify-icon>
                            <span>Generate Acak</span>
                        </button>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-0 p-12 radius-8 text-xs">
                        <iconify-icon icon="solar:shield-warning-outline" class="text-base flex-shrink-0 mt-2"></iconify-icon>
                        <div>
                            Tindakan ini dicatat pada log audit kepatuhan dan akan memperbarui versi sesi akun. Pastikan memberikan kredensial baru melalui saluran aman.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-24 py-16">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                        <span>Simpan Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('adminNewPassword');
    const confirmInput = document.getElementById('adminConfirmPassword');
    const toggleBtn = document.getElementById('toggleAdminPasswordBtn');
    const toggleIcon = document.getElementById('toggleAdminPasswordIcon');
    const generateBtn = document.getElementById('btnGeneratePassword');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = pwdInput.getAttribute('type') === 'password';
            const newType = isPassword ? 'text' : 'password';
            pwdInput.setAttribute('type', newType);
            if (confirmInput) confirmInput.setAttribute('type', newType);
            if (toggleIcon) {
                toggleIcon.setAttribute('icon', isPassword ? 'solar:eye-closed-outline' : 'solar:eye-outline');
            }
        });
    }

    if (generateBtn && pwdInput && confirmInput) {
        generateBtn.addEventListener('click', function () {
            const charset = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
            let generated = 'Begawi';
            for (let i = 0; i < 6; i++) {
                generated += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            pwdInput.value = generated;
            confirmInput.value = generated;
            pwdInput.setAttribute('type', 'text');
            confirmInput.setAttribute('type', 'text');
            if (toggleIcon) toggleIcon.setAttribute('icon', 'solar:eye-closed-outline');
        });
    }
});
</script>

<?= $this->endSection() ?>