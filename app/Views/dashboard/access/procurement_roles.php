<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Peran Pengadaan</h6>
        <p class="text-secondary-light mb-0">Master data peran pengadaan barang/jasa yang terhubung ke pendaftaran ASN dan penugasan peran.</p>
    </div>
    <a href="<?= site_url('dashboard/akses/peran-pengadaan/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
        <span>Tambah Peran</span>
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Peran Pengadaan</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 110px;">Pengguna</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-24 text-secondary-light">Belum ada peran pengadaan yang terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                            <?php $usersCount = (int) ($role['users_count'] ?? 0); ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary-50 text-primary-600 border border-primary-100 fw-semibold px-8 py-4 radius-4">
                                        <?= esc($role['key']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary-light d-block"><?= esc($role['name']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-secondary-light"><?= esc(mb_strimwidth((string) ($role['description'] ?? '-'), 0, 70, '...')) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($usersCount > 0): ?>
                                        <a href="<?= site_url('dashboard/akses/penugasan-pengadaan') ?>" class="badge bg-info-subtle text-info border border-info-subtle radius-pill px-8 py-4" title="Lihat daftar pengguna dengan peran ini">
                                            <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-xs me-1"></iconify-icon>
                                            <?= $usersCount ?> User
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-500 radius-pill px-8 py-4 text-xs">
                                            0 User
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => !empty($role['is_active']) ? 'active' : 'inactive']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <!-- Detail Button -->
                                        <a href="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id']) ?>" class="btn btn-sm btn-outline-neutral-300 radius-6 px-8 py-4" title="Detail Peran">
                                            <iconify-icon icon="solar:eye-linear"></iconify-icon>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/ubah') ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-8 py-4" title="Ubah Peran">
                                            <iconify-icon icon="solar:pen-linear"></iconify-icon>
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <?php if (!empty($role['is_active'])): ?>
                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/nonaktifkan') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan peran ini? Peran tidak akan muncul di form registrasi ASN.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning-main radius-6 px-8 py-4" title="Nonaktifkan Peran">
                                                    <iconify-icon icon="solar:power-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/aktifkan') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success-main radius-6 px-8 py-4" title="Aktifkan Peran">
                                                    <iconify-icon icon="solar:restart-bold"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete Button with Smart Check -->
                                        <?php if ($usersCount > 0): ?>
                                            <!-- Peran ada user yang pakai: cegah hapus, arahkan untuk nonaktifkan -->
                                            <button type="button" class="btn btn-sm btn-outline-danger radius-6 px-8 py-4" data-bs-toggle="modal" data-bs-target="#modalRoleInUse<?= (int) $role['id'] ?>" title="Hapus Peran (Terkait <?= $usersCount ?> Pengguna)">
                                                <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                            </button>
                                        <?php else: ?>
                                            <!-- Peran tidak ada user yang pakai: izinkan hapus permanen -->
                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran <?= esc($role['name']) ?> secara permanen? Peran ini tidak memiliki pengguna terdaftar.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-4" title="Hapus Peran">
                                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($usersCount > 0): ?>
                                        <!-- Modal Peringatan & Pengalihan Nonaktifkan Peran -->
                                        <div class="modal fade text-start" id="modalRoleInUse<?= (int) $role['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= (int) $role['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-12 border-0 shadow">
                                                    <div class="modal-header bg-warning-50 border-bottom border-warning-100 py-16 px-24">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="w-32-px h-32-px rounded-circle bg-warning-100 text-warning-main d-flex justify-content-center align-items-center">
                                                                <iconify-icon icon="solar:shield-warning-bold" class="text-xl"></iconify-icon>
                                                            </span>
                                                            <h6 class="modal-title fw-bold text-warning-main mb-0" id="modalLabel<?= (int) $role['id'] ?>">Peran Sedang Digunakan</h6>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <p class="text-secondary-light mb-16">
                                                            Peran <strong><?= esc($role['name']) ?> (<?= esc($role['key']) ?>)</strong> saat ini sedang digunakan oleh <strong><?= $usersCount ?> pengguna</strong>.
                                                        </p>
                                                        <div class="alert alert-warning border-warning-200 bg-warning-50 text-warning-main p-16 radius-8 text-sm mb-0">
                                                            <div class="d-flex gap-2">
                                                                <iconify-icon icon="solar:info-circle-bold" class="text-xl flex-shrink-0 mt-2"></iconify-icon>
                                                                <div>
                                                                    Untuk menjaga integritas dan riwayat data pengadaan, peran yang memiliki riwayat pengguna <strong>tidak dapat dihapus</strong>.
                                                                    Anda diarahkan untuk <strong>menonaktifkan</strong> peran ini agar tidak dapat dipilih lagi di masa mendatang.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-12 px-24 justify-content-between">
                                                        <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8" data-bs-dismiss="modal">Tutup</button>
                                                        <?php if (!empty($role['is_active'])): ?>
                                                            <form action="<?= site_url('dashboard/akses/peran-pengadaan/' . (int) $role['id'] . '/nonaktifkan') ?>" method="post" class="m-0">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-warning-main radius-8 px-20 py-8 d-inline-flex align-items-center gap-2 text-white shadow-sm">
                                                                    <iconify-icon icon="solar:power-bold"></iconify-icon>
                                                                    <span>Nonaktifkan Peran Ini</span>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="badge bg-neutral-200 text-neutral-600 px-12 py-8 radius-pill">Peran Sudah Nonaktif</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (($pager ?? null) && $pager->getPageCount('procurement_roles') > 1): ?>
    <div class="card-footer mt-16"><?= $pager->links('procurement_roles', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
