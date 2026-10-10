<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $roles = $roles ?? []; $permissions = $permissions ?? []; $matrix = $matrix ?? []; ?>
<?php $pager = $pager ?? null; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Role vs Permission Matrix</h6>
        <p class="text-secondary-light mb-0">Sinkronkan permission untuk seluruh role sekaligus.</p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/akses/role/matrix') ?>" method="post">
            <?= csrf_field() ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th style="min-width:200px">Permission</th>
                            <?php foreach ($roles as $role): ?>
                                <th class="text-center" style="min-width:120px"><?= esc($role['name']) ?><br><small class="text-secondary-light"><?= esc($role['key']) ?></small></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Group permissions by domain for readability
                        $byDomain = [];
                        foreach ($permissions as $perm) {
                            $domain = $perm['domain'] ?? 'other';
                            $byDomain[$domain][] = $perm;
                        }
                        ?>
                        <?php foreach ($byDomain as $domain => $perms): ?>
                            <tr>
                                <td colspan="<?= count($roles) + 1 ?>" class="fw-semibold bg-neutral-50">
                                    <?= esc(ucfirst($domain)) ?>
                                </td>
                            </tr>
                            <?php foreach ($perms as $perm): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-primary-600"><?= esc($perm['key']) ?></span><br>
                                        <small class="text-secondary-light"><?= esc($perm['name']) ?></small>
                                    </td>
                                    <?php foreach ($roles as $role): ?>
                                        <td class="text-center">
                                            <div class="form-check style-check d-flex justify-content-center">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="matrix[<?= (int) $role['id'] ?>][]"
                                                       id="m-<?= (int) $role['id'] ?>-<?= (int) $perm['id'] ?>"
                                                       value="<?= (int) $perm['id'] ?>"
                                                    <?= in_array((int) $perm['id'], (array) ($matrix[$role['id']] ?? []), true) ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-24 gap-12">
                <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Sinkron Matrix</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>