<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Kelas Tersedia</h6>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($classes)): ?>
            <p class="text-secondary-light">Belum ada kelas yang dibuka.</p>
        <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Trainer</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center">Kuota</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= esc($class['course_title']) ?></td>
                        <td><?= esc($class['trainer_name']) ?></td>
                        <td class="text-center"><?= esc(date('d M Y', strtotime($class['start_date']))) ?></td>
                        <td class="text-center"><?= esc($class['quota']) ?></td>
                        <td class="text-center">
                            <?php if ($class['is_enrolled']): ?>
                                <span class="text-secondary-light text-sm">Sudah Terdaftar</span>
                            <?php elseif ($class['is_full']): ?>
                                <span class="text-danger-600 text-sm">Kuota Penuh</span>
                            <?php else: ?>
                                <form action="<?= route_to('learning.enrollments.store', $class['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary btn-sm px-16 py-8 radius-8">Daftar</button>
                                </form>
                            <?php endif; ?>
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
