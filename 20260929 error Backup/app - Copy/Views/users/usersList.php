<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
    <script>
            $(".remove-item-btn").on("click", function() {
                $(this).closest("tr").addClass("d-none")
            });
    </script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?> 

    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <h6 class="mb-0">Manajemen User</h6>
        </div>
        <div class="card-body p-24">
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form method="get" class="row g-3 mb-24">
                <div class="col-md-5">
                    <input type="search" name="q" class="form-control" placeholder="Cari username, email, atau instansi..." value="<?= esc($keyword ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <?php foreach (['active' => 'Active', 'pending' => 'Pending', 'inactive' => 'Inactive', 'rejected' => 'Rejected'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($status ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="user_type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <?php foreach (['asn' => 'ASN', 'provider' => 'Provider', 'academic_practitioner' => 'Akademisi/Praktisi', 'public' => 'Public'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($userType ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>

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
                            <th scope="col">Join Date</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Instansi</th>
                            <th scope="col">Tipe User</th>
                            <th scope="col" class="text-center">Status</th>
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
                            <td><?= esc(date('d M Y', strtotime($user['created_at']))) ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-md mb-0 fw-normal text-secondary-light"><?= esc($user['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-md mb-0 fw-normal text-secondary-light"><?= esc($user['email']) ?></span></td>
                            <td><?= esc($user['instansi'] ?? '-') ?></td>
                            <td><?= esc(strtoupper($user['user_type'])) ?></td>
                            <td class="text-center">
                                <?php
                                    $statusBadge = [
                                        'active'   => 'bg-success-focus text-success-600 border-success-main',
                                        'pending'  => 'bg-warning-focus text-warning-600 border-warning-main',
                                        'inactive' => 'bg-neutral-200 text-neutral-600 border-neutral-400',
                                        'rejected' => 'bg-danger-focus text-danger-600 border-danger-main',
                                    ][$user['status']] ?? 'bg-neutral-200 text-neutral-600 border-neutral-400';
                                ?>
                                <span class="<?= $statusBadge ?> border px-24 py-4 radius-4 fw-medium text-sm"><?= esc(ucfirst($user['status'])) ?></span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <?php if ($user['status'] === 'pending'): ?>
                                        <form action="<?= route_to('admin.users.verify', $user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="decision" value="approve">
                                            <button type="submit" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0" title="Approve">
                                                <iconify-icon icon="lucide:check" class="menu-icon"></iconify-icon>
                                            </button>
                                        </form>
                                        <form action="<?= route_to('admin.users.verify', $user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0" title="Reject">
                                                <iconify-icon icon="lucide:x" class="menu-icon"></iconify-icon>
                                            </button>
                                        </form>
                                    <?php elseif ($user['id'] !== session()->get('user_id') && in_array($user['status'], ['active', 'inactive'], true)): ?>
                                        <form action="<?= route_to('admin.users.status', $user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="<?= $user['status'] === 'active' ? 'inactive' : 'active' ?>">
                                            <button type="submit" class="<?= $user['status'] === 'active' ? 'bg-warning-focus text-warning-600 bg-hover-warning-200' : 'bg-success-focus text-success-600 bg-hover-success-200' ?> fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0" title="<?= $user['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <iconify-icon icon="<?= $user['status'] === 'active' ? 'lucide:pause' : 'lucide:play' ?>" class="menu-icon"></iconify-icon>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
