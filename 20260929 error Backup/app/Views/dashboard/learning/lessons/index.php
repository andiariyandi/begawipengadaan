<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Lesson</h6>
        <p class="text-secondary-light mb-0">Kelola lesson dan materi pembelajaran.</p>
    </div><a href="<?= site_url('dashboard/pembelajaran/lesson/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Lesson</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Lesson</th>
                        <th>Modul</th>
                        <th>Tipe</th>
                        <th>Durasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'LSN-001', 'name' => 'Prinsip Dasar Pengadaan', 'module' => 'Pengantar PBJ Pemerintah', 'type' => 'Video', 'duration' => '20 menit', 'status' => 'active'], ['id' => 'LSN-002', 'name' => 'Aktor dalam PBJ', 'module' => 'Pengantar PBJ Pemerintah', 'type' => 'Materi', 'duration' => '15 menit', 'status' => 'active'], ['id' => 'LSN-003', 'name' => 'Regulasi Pengadaan', 'module' => 'Pengantar PBJ Pemerintah', 'type' => 'Dokumen', 'duration' => '25 menit', 'status' => 'draft']] as $lesson): ?><tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($lesson['name']) ?></h6>
                            </td>
                            <td><?= esc($lesson['module']) ?></td>
                            <td><?= esc($lesson['type']) ?></td>
                            <td><?= esc($lesson['duration']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $lesson['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/pembelajaran/lesson/' . $lesson['id']) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>