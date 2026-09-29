<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-4">Role &amp; Access</h6>
            <p class="text-secondary-light mb-0">Kelola role yang digunakan untuk mengatur akses pengguna.</p>
        </div>
        <button type="button" class="btn btn-primary text-sm px-12 py-12 radius-8" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            Tambah Role
        </button>
    </div>

    <div class="card-body p-24">
        <?php if ($message = session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc($message) ?></div>
        <?php endif; ?>
        <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc($error) ?></div>
        <?php endif; ?>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Dibuat</th>
                        <th scope="col">Role</th>
                        <th scope="col">Slug</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($roles === []): ?>
                        <tr>
                            <td colspan="5" class="text-center text-secondary-light py-24">Belum ada role.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roles as $i => $role): ?>
                            <?php $protected = in_array($role['slug'] ?? '', ['super_administrator', 'administrator', 'operator'], true); ?>
                            <tr>
                                <td><?= esc($i + 1) ?></td>
                                <td><?= esc(! empty($role['created_at']) ? date('d M Y', strtotime($role['created_at'])) : '-') ?></td>
                                <td class="fw-medium"><?= esc($role['name']) ?></td>
                                <td><code><?= esc($role['slug']) ?></code></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= route_to('role.permissions', $role['id']) ?>" class="btn btn-sm btn-outline-primary radius-8">Permission</a>
                                        <?php if ($protected): ?>
                                            <span class="text-secondary-light text-sm align-self-center">Role inti</span>
                                        <?php else: ?>
                                            <form action="<?= route_to('admin.roles.delete', $role['id']) ?>" method="post" onsubmit="return confirm('Hapus role ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-8">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24">
                <h5 class="modal-title" id="createRoleModalLabel">Tambah Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= route_to('admin.roles.store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <label for="role-name" class="form-label fw-semibold">Nama Role</label>
                    <input id="role-name" type="text" name="name" class="form-control radius-8" maxlength="50" required>
                </div>
                <div class="modal-footer px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary radius-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
