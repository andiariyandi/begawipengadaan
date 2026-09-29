<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <h6 class="mb-0">Assign Role</h6>
        </div>

        <div class="card-body p-24">
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border input-form-dark" type="checkbox" name="checkbox" id="selectAll">
                                    </div>
                                    S.L
                                </div>
                            </th>
                            <th scope="col">Username</th>
                            <th scope="col" class="text-center">Role Aplikasi</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
                                    </div>
                                    <?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-md mb-0 fw-normal text-secondary-light"><?= esc($user['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?= esc($user['role_names'] ?? '-') ?>
                                <div class="text-secondary-light text-sm"><?= esc(strtoupper($user['user_type'])) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary-600 not-active px-18 py-11 dropdown-toggle toggle-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">Assign Role</button>
                                    <ul class="dropdown-menu" style="">
                                        <?php foreach ($allRoles as $roleOption): ?>
                                        <li>
                                            <form action="<?= route_to('admin.roles.assign.store') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="role_id" value="<?= $roleOption['id'] ?>">
                                                <button type="submit" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 border-0 bg-transparent w-100 text-start">
                                                    <?= esc($roleOption['name']) ?>
                                                </button>
                                            </form>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>
