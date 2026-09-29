<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Kelas Saya</h6>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (empty($enrollments)): ?>
            <p class="text-secondary-light">Anda belum terdaftar di kelas manapun, atau pendaftaran masih menunggu persetujuan.
                <a href="<?= route_to('learning.enrollments.browse') ?>">Cari kelas</a>.
            </p>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td><?= esc($enrollment['course_title']) ?></td>
                                <td class="text-center"><?= esc(date('d M Y', strtotime($enrollment['start_date']))) ?></td>
                                <td class="text-center">
                                    <?php if ($enrollment['final_status'] === 'passed'): ?>
                                        <span class="badge bg-success-focus text-success-600 px-16 py-6 radius-4">Lulus</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-focus text-warning-600 px-16 py-6 radius-4">Sedang Berjalan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= route_to('learning.study.modules', $enrollment['id']) ?>" class="btn btn-primary btn-sm px-16 py-8 radius-8">Buka Materi</a>
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