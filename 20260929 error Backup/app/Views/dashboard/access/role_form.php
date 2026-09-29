<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Role') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Role</label><input type="text" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'Super Admin' : '' ?>" placeholder="Contoh: Admin Konten"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Role</label><input type="text" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'super-admin' : '' ?>" placeholder="contoh-admin-konten"></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi</label><textarea class="form-control radius-8" rows="3" placeholder="Deskripsi role"></textarea></div>
                <div class="col-12">
                    <h6 class="text-lg mb-16">Permission</h6>
                    <div class="row gy-12"><?php foreach (['users.manage', 'roles.manage', 'learning.manage', 'knowledge.manage', 'regulations.manage', 'communication.manage', 'certificates.manage', 'intelligence.view', 'reports.export', 'settings.manage'] as $permission): ?><div class="col-md-4">
                                <div class="form-check style-check"><input class="form-check-input" type="checkbox" id="<?= esc($permission) ?>"><label class="form-check-label" for="<?= esc($permission) ?>"><?= esc($permission) ?></label></div>
                            </div><?php endforeach; ?></div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Role</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>