<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-4">Kelas — <?= esc($course['title']) ?></h6>
            <a href="<?= route_to('learning.courses') ?>" class="text-secondary-light text-sm">&larr; Kembali ke Program Pelatihan</a>
        </div>
        <a href="<?= route_to('learning.classes.create', $course['id']) ?>" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
            <iconify-icon icon="ic:baseline-plus"></iconify-icon> Tambah Kelas
        </a>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($classes)): ?>
            <p class="text-secondary-light">Belum ada kelas untuk program pelatihan ini.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-center">Kuota</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Modul</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= esc(date('d M Y', strtotime($class['start_date']))) ?> &ndash; <?= esc(date('d M Y', strtotime($class['end_date']))) ?></td>
                        <td class="text-center"><?= esc($class['quota']) ?> peserta</td>
                        <td class="text-center">
                            <?php
                                $statusBadge = [
                                    'scheduled' => 'bg-info-focus text-info-600 border-info-main',
                                    'ongoing'   => 'bg-success-focus text-success-600 border-success-main',
                                    'completed' => 'bg-neutral-200 text-neutral-600 border-neutral-400',
                                    'cancelled' => 'bg-danger-focus text-danger-600 border-danger-main',
                                ][$class['status']] ?? 'bg-neutral-200 text-neutral-600 border-neutral-400';
                            ?>
                            <span class="<?= $statusBadge ?> border px-24 py-4 radius-4 fw-medium text-sm"><?= esc(ucfirst($class['status'])) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="<?= route_to('learning.modules', $class['id']) ?>" class="text-primary-600 fw-medium">Kelola Modul</a>
                        </td>
                        <td class="text-center">
                            <form action="<?= route_to('learning.classes.delete', $course['id'], $class['id']) ?>" method="post" onsubmit="return confirm('Hapus kelas ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
