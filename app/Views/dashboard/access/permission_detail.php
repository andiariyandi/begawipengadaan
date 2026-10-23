<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'roles']) ?>

<?php
$permission      = $permission ?? [];
$roles           = $roles ?? [];
$unassignedRoles = $unassignedRoles ?? [];
$userCount       = $userCount ?? 0;
$permissionId    = $permissionId ?? ($permission['id'] ?? 0);
$isActive        = $isActive ?? (! empty($roles));
$isSystem        = ! empty($permission['is_system']);

$domainTitles = [
    'dashboard'     => 'Dashboard Utama',
    'landing'       => 'Landing Page (CMS)',
    'access'        => 'Manajemen Akses & Pengguna',
    'learning'      => 'Arena Belajar (LMS)',
    'bunga'         => 'Bunga Rampai & Pengetahuan (KMS)',
    'regulation'    => 'Repositori Regulasi PBJ',
    'communication' => 'Komunikasi, Berita, & Event',
    'certificate'   => 'Sertifikat & Kelulusan',
    'intelligence'  => 'Government Intelligence SDM PBJ',
    'reports'       => 'Pusat Laporan Terpadu',
    'survey'        => 'Survei Kepuasan (IKM)',
    'system'        => 'Sistem, Approval, & Keamanan',
];
$domainName = $domainTitles[$permission['domain'] ?? ''] ?? ucfirst($permission['domain'] ?? 'Umum');
?>

<!-- Header & Navigasi Aksi -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h6 class="fw-semibold mb-0"><?= esc($permission['name'] ?? 'Detail Permission') ?></h6>
            <?php if ($isSystem): ?>
                <span class="badge bg-neutral-200 text-secondary-dark text-xs px-8 py-2 radius-4" title="Permission bawaan platform yang dilindungi">
                    <iconify-icon icon="solar:shield-check-bold" class="align-middle me-1"></iconify-icon> Sistem
                </span>
            <?php else: ?>
                <span class="badge bg-primary-50 text-primary-600 border border-primary-200 text-xs px-8 py-2 radius-4">
                    Kustom
                </span>
            <?php endif; ?>
        </div>
        <p class="text-secondary-light mb-0 font-monospace text-sm">
            <iconify-icon icon="solar:key-outline" class="align-middle me-1"></iconify-icon>
            <?= esc($permission['key'] ?? '-') ?> &bull; ID #<?= esc($permissionId) ?>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-12 align-items-center">
        <a href="<?= site_url('dashboard/akses/permission') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali ke Daftar</span>
        </a>
        <a href="<?= site_url('dashboard/akses/role/matrix') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:widget-add-outline" class="text-lg"></iconify-icon>
            <span>Matriks Hak Akses</span>
        </a>
        <button type="button" class="btn btn-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editPermissionModal">
            <iconify-icon icon="solar:pen-new-square-outline" class="text-lg"></iconify-icon>
            <span>Ubah Data</span>
        </button>
    </div>
</div>

<!-- Alert Notifikasi Flash -->
<?php if (session('success')): ?>
    <div class="alert alert-success mb-24 radius-8 p-16 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:check-circle-outline" class="text-xl flex-shrink-0 text-success-600"></iconify-icon>
        <span class="text-sm"><?= esc(session('success')) ?></span>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger mb-24 radius-8 p-16 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:danger-circle-outline" class="text-xl flex-shrink-0 text-danger-600"></iconify-icon>
        <span class="text-sm"><?= esc(session('error')) ?></span>
    </div>
<?php endif; ?>

<!-- 4 Metrik Ringkasan Status -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Status Hak Akses</p>
                        <?php if ($isActive): ?>
                            <h6 class="mb-0 text-success-main d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                <span>Aktif Digunakan</span>
                            </h6>
                        <?php else: ?>
                            <h6 class="mb-0 text-danger-main d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                                <span>Nonaktif (Dicabut)</span>
                            </h6>
                        <?php endif; ?>
                    </div>
                    <div class="w-50-px h-50-px <?= $isActive ? 'bg-success-main' : 'bg-danger-main' ?> rounded-circle d-flex justify-content-center align-items-center text-white text-2xl">
                        <iconify-icon icon="<?= $isActive ? 'solar:shield-check-outline' : 'solar:shield-cross-outline' ?>"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">
                    <?= $isActive ? 'Terpasang pada ' . count($roles) . ' peran pengguna' : 'Belum dipasang ke peran manapun' ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Modul Domain</p>
                        <h6 class="mb-0 text-neutral-800"><?= esc($domainName) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center text-white text-2xl">
                        <iconify-icon icon="solar:layers-minimalistic-outline"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0 font-monospace">
                    domain: <?= esc($permission['domain'] ?? 'other') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Klasifikasi Izin</p>
                        <h6 class="mb-0 text-purple"><?= $isSystem ? 'Sistem Inti Platform' : 'Kustom Pengguna' ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center text-white text-2xl">
                        <iconify-icon icon="solar:lock-keyhole-outline"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">
                    <?= $isSystem ? 'Dilindungi dari penghapusan' : 'Dapat diedit dan dihapus' ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Pengguna Terdampak</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($userCount) ?> Akun</h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center text-white text-2xl">
                        <iconify-icon icon="solar:users-group-two-rounded-outline"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">
                    Akun aktif dengan peran berizin ini
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Kolom Kiri: Informasi Konfigurasi & Tindakan Cepat -->
    <div class="col-xl-6">
        <div class="card border radius-12 h-100">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-md mb-0">Konfigurasi Metadata Permission</h6>
                <button type="button" class="btn btn-sm btn-outline-primary radius-6 px-12 py-4 text-xs" data-bs-toggle="modal" data-bs-target="#editPermissionModal">
                    <iconify-icon icon="solar:pen-new-square-outline" class="me-1"></iconify-icon> Edit
                </button>
            </div>
            <div class="card-body p-24">
                <div class="row gy-20">
                    <div class="col-12">
                        <p class="text-secondary-light text-xs mb-4">Kode Kunci Unik (Permission Key)</p>
                        <div class="p-12 radius-8 bg-neutral-50 border font-monospace text-primary-600 fw-semibold fs-6">
                            <?= esc($permission['key'] ?? '-') ?>
                        </div>
                        <small class="text-secondary-light text-xxs mt-4 d-block">
                            Digunakan dalam pengecekan <code>$authService->can('<?= esc($permission['key'] ?? '') ?>')</code> di seluruh controller dan template.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Nama Izin</p>
                        <h6 class="text-sm mb-0 fw-semibold text-neutral-800"><?= esc($permission['name'] ?? '-') ?></h6>
                    </div>

                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Modul / Domain</p>
                        <span class="badge bg-info-focus text-info-main px-12 py-4 rounded-pill fw-medium text-xs">
                            <?= esc($domainName) ?> (<?= esc($permission['domain'] ?? '-') ?>)
                        </span>
                    </div>

                    <div class="col-12">
                        <p class="text-secondary-light text-xs mb-4">Deskripsi Fungsional &amp; Hak Akses</p>
                        <div class="p-16 radius-8 bg-neutral-50 border text-neutral-800 text-sm">
                            <?= esc($permission['description'] ?: 'Tidak ada deskripsi rinci untuk permission ini.') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Waktu Dibuat</p>
                        <h6 class="text-xs mb-0 text-neutral-700">
                            <?= ! empty($permission['created_at']) && $permission['created_at'] !== '0000-00-00 00:00:00' ? esc(date('d M Y H:i', strtotime($permission['created_at']))) : 'Seeded / Sistem Awal' ?>
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Terakhir Diperbarui</p>
                        <h6 class="text-xs mb-0 text-neutral-700">
                            <?= ! empty($permission['updated_at']) && $permission['updated_at'] !== '0000-00-00 00:00:00' ? esc(date('d M Y H:i', strtotime($permission['updated_at']))) : 'Belum pernah diubah' ?>
                        </h6>
                    </div>
                </div>

                <!-- Panel Tombol Opsi Deactivate & Kontrol Sistem -->
                <hr class="my-24">
                <h6 class="text-sm fw-semibold mb-12">Kontrol Status Operasional</h6>
                <div class="d-flex flex-wrap gap-12">
                    <?php if ($isActive): ?>
                        <button type="button" class="btn btn-warning-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2 text-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/nonaktifkan') ?>"
                                data-method="POST"
                                data-title="Nonaktifkan Permission Ini"
                                data-message="Apakah Anda yakin ingin menonaktifkan permission '<?= esc($permission['key']) ?>'? Seluruh hak akses permission ini akan dicabut dari seluruh peran di sistem."
                                data-btn-text="Ya, Nonaktifkan"
                                data-btn-class="btn-warning-600"
                                data-icon="solar:shield-cross-bold"
                                data-icon-color="warning">
                            <iconify-icon icon="solar:shield-cross-outline" class="text-lg"></iconify-icon>
                            <span>Nonaktifkan Permission (Deactive)</span>
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-success-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2 text-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/aktifkan') ?>"
                                data-method="POST"
                                data-title="Aktifkan Permission Kembali"
                                data-message="Pasang kembali hak akses permission '<?= esc($permission['key']) ?>' ke peran Administrator dan Super Administrator?"
                                data-btn-text="Ya, Aktifkan"
                                data-btn-class="btn-success-600"
                                data-icon="solar:shield-check-bold"
                                data-icon-color="success">
                            <iconify-icon icon="solar:shield-check-outline" class="text-lg"></iconify-icon>
                            <span>Aktifkan ke Administrator</span>
                        </button>
                    <?php endif; ?>

                    <?php if (! $isSystem): ?>
                        <button type="button" class="btn btn-danger-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2 text-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmationActionModal"
                                data-action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/hapus') ?>"
                                data-method="POST"
                                data-title="Hapus Permission Kustom"
                                data-message="Apakah Anda yakin ingin menghapus permission kustom '<?= esc($permission['key']) ?>'? Tindakan ini tidak dapat dibatalkan."
                                data-btn-text="Ya, Hapus"
                                data-btn-class="btn-danger-600"
                                data-icon="solar:trash-bin-trash-bold"
                                data-icon-color="danger">
                            <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon>
                            <span>Hapus Permission</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Peran (Role) yang Menggunakan Izin Ini -->
    <div class="col-xl-6">
        <div class="card border radius-12 h-100">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="text-md mb-0">Peran yang Memiliki Izin Ini</h6>
                    <span class="text-secondary-light text-xs"><?= count($roles) ?> peran aktif terpasang</span>
                </div>
                <a href="<?= site_url('dashboard/akses/role/matrix') ?>" class="btn btn-xs btn-outline-primary radius-6 px-10 py-5 text-xs">
                    <iconify-icon icon="solar:widget-add-outline" class="me-1"></iconify-icon> Buka Matriks
                </a>
            </div>

            <div class="card-body p-24">
                <!-- Quick Grant Tool: Tambah Izin ke Peran Lain -->
                <?php if (! empty($unassignedRoles)): ?>
                    <form action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/grant-role') ?>" method="post" class="mb-20 p-16 radius-8 bg-neutral-50 border">
                        <?= csrf_field() ?>
                        <label class="form-label fw-semibold text-neutral-800 text-xs mb-8">
                            <iconify-icon icon="solar:user-plus-outline" class="align-middle me-1 text-primary-600"></iconify-icon>
                            Berikan Hak Akses Ini ke Peran Lain:
                        </label>
                        <div class="d-flex gap-8">
                            <select name="role_id" class="form-select form-select-sm radius-6 text-sm" required>
                                <option value="">-- Pilih Peran yang Belum Memiliki Izin Ini --</option>
                                <?php foreach ($unassignedRoles as $ur): ?>
                                    <option value="<?= (int) $ur['id'] ?>">
                                        <?= esc($ur['name']) ?> (<?= esc($ur['key']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary-600 radius-6 px-16 text-xs flex-shrink-0">
                                Berikan Izin
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- Tabel Peran Terpasang -->
                <?php if (empty($roles)): ?>
                    <div class="p-24 text-center border radius-8 bg-neutral-50">
                        <div class="w-48-px h-48-px rounded-circle bg-warning-50 text-warning-600 d-inline-flex align-items-center justify-content-center text-2xl mb-12">
                            <iconify-icon icon="solar:shield-warning-outline"></iconify-icon>
                        </div>
                        <h6 class="text-sm fw-semibold mb-4">Permission Ini Sedang Dinonaktifkan</h6>
                        <p class="text-secondary-light text-xs mb-16">
                            Tidak ada peran yang memiliki hak akses ini saat ini. Seluruh aksi dan halaman yang dilindungi kode <code><?= esc($permission['key'] ?? '') ?></code> tidak dapat diakses oleh siapapun (kecuali Super Admin).
                        </p>
                        <form action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/aktifkan') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-success-600 radius-6 px-16 py-6 text-xs">
                                <iconify-icon icon="solar:shield-check-outline" class="me-1"></iconify-icon>
                                Aktifkan Kembali ke Administrator
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0 align-middle">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th>Nama Peran (Role)</th>
                                    <th>Key Peran</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $r): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/akses/role/' . (int) $r['id']) ?>" class="fw-semibold text-primary-600 text-sm">
                                                <?= esc($r['name']) ?>
                                            </a>
                                            <?php if (! empty($r['is_system'])): ?>
                                                <span class="badge bg-neutral-100 text-secondary-dark text-xxs border ms-1">Sistem</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <code class="text-xs"><?= esc($r['key']) ?></code>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-xs btn-outline-danger px-8 py-4 radius-4 text-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmationActionModal"
                                                    data-action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/revoke-role/' . (int) $r['id']) ?>"
                                                    data-method="POST"
                                                    data-title="Cabut Izin dari Role"
                                                    data-message="Apakah Anda yakin ingin mencabut izin '<?= esc($permission['key']) ?>' dari peran <?= esc($r['name']) ?>?"
                                                    data-btn-text="Ya, Cabut Izin"
                                                    data-btn-class="btn-danger-600"
                                                    data-icon="solar:shield-cross-bold"
                                                    data-icon-color="danger"
                                                    title="Cabut hak akses ini dari peran <?= esc($r['name']) ?>">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon> Cabut
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Data Permission -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 shadow">
            <form action="<?= site_url('dashboard/akses/permission/' . (int) $permissionId . '/ubah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fs-5" id="editPermissionModalLabel">
                        <iconify-icon icon="solar:pen-new-square-outline" class="text-primary-600 me-2"></iconify-icon>
                        Ubah Data Permission
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-neutral-800 text-sm mb-8">Kode Kunci (Key)</label>
                        <input type="text" class="form-control radius-8 font-monospace bg-neutral-100" value="<?= esc($permission['key'] ?? '') ?>" readonly>
                        <small class="text-secondary-light text-xxs">Kode unik sistem tidak dapat diubah setelah dibuat.</small>
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-neutral-800 text-sm mb-8">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control radius-8" value="<?= esc($permission['name'] ?? '') ?>" required maxlength="255">
                    </div>

                    <div class="mb-20">
                        <label class="form-label fw-semibold text-neutral-800 text-sm mb-8">Domain Modul <span class="text-danger">*</span></label>
                        <select name="domain" class="form-select radius-8" required>
                            <?php foreach ($domainTitles as $domKey => $domLabel): ?>
                                <option value="<?= esc($domKey) ?>" <?= ($permission['domain'] ?? '') === $domKey ? 'selected' : '' ?>>
                                    <?= esc($domLabel) ?> (<?= esc($domKey) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-neutral-800 text-sm mb-8">Deskripsi Fungsional</label>
                        <textarea name="description" class="form-control radius-8" rows="3"><?= esc($permission['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">
                        <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
