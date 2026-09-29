<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="card p-24 radius-12">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-24">
        <div>
            <h6 class="mb-4">Permission Role: <?= esc($role['name']) ?></h6>
            <p class="text-secondary-light mb-0">Pilih akses yang dapat digunakan oleh role ini.</p>
        </div>
        <a href="<?= route_to('roleAaccess') ?>" class="btn btn-outline-secondary btn-sm radius-8">Kembali</a>
    </div>

    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success radius-12 mb-16"><?= esc($message) ?></div>
    <?php endif; ?>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc($error) ?></div>
    <?php endif; ?>

    <form action="<?= route_to('role.permissions.update', $role['id']) ?>" method="post">
        <?= csrf_field() ?>
        <?php $grouped = []; ?>
        <?php foreach ($permissions as $permission): ?>
            <?php $grouped[$permission['module'] ?: 'general'][] = $permission; ?>
        <?php endforeach; ?>

        <?php if ($grouped === []): ?>
            <p class="text-secondary-light">Belum ada permission yang tersedia.</p>
        <?php else: ?>
            <?php foreach ($grouped as $module => $modulePermissions): ?>
                <div class="border rounded-3 p-16 mb-16">
                    <h6 class="mb-12"><?= esc(ucwords(str_replace(['_', '-'], ' ', $module))) ?></h6>
                    <div class="row g-3">
                        <?php foreach ($modulePermissions as $permission): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="permission_ids[]"
                                        value="<?= esc($permission['id']) ?>"
                                        id="permission-<?= esc($permission['id']) ?>"
                                        <?= in_array((int) $permission['id'], $assignedIds, true) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="permission-<?= esc($permission['id']) ?>">
                                        <?= esc($permission['name']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan Permission</button>
        <?php endif; ?>
    </form>
</div>
<?= $this->endSection(); ?>
