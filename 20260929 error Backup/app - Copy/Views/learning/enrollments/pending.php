<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Persetujuan Pendaftaran Kelas</h6>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($enrollments)): ?>
            <p class="text-secondary-light">Tidak ada pendaftaran yang menunggu persetujuan.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Program</th>
                        <th class="text-center">Tanggal Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?= esc($enrollment['username']) ?></td>
                        <td><?= esc($enrollment['course_title']) ?></td>
                        <td class="text-center"><?= esc(date('d M Y', strtotime($enrollment['created_at']))) ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                <form action="<?= route_to('learning.enrollments.verify', $enrollment['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="decision" value="approve">
                                    <button type="submit" class="bg-success-focus text-success-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                        <iconify-icon icon="lucide:check"></iconify-icon>
                                    </button>
                                </form>
                                <form action="<?= route_to('learning.enrollments.verify', $enrollment['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="decision" value="reject">
                                    <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                        <iconify-icon icon="lucide:x"></iconify-icon>
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
