<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Program Pelatihan</h6>
        <a href="<?= route_to('learning.courses.create') ?>" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
            <iconify-icon icon="ic:baseline-plus"></iconify-icon> Tambah Program
        </a>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($courses)): ?>
            <p class="text-secondary-light">Belum ada program pelatihan.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= esc($course['title']) ?></td>
                        <td><?= esc($course['category'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">
                                <?= esc(ucfirst($course['status'])) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?= route_to('learning.classes', $course['id']) ?>" class="text-primary-600 fw-medium">Kelola Kelas</a>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                <a href="<?= route_to('learning.courses.edit', $course['id']) ?>" class="bg-success-focus text-success-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                </a>
                                <form action="<?= route_to('learning.courses.delete', $course['id']) ?>" method="post" onsubmit="return confirm('Hapus program ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                        <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                    </button>
                                </form>
                            </div>
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
