<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Course</h6>
        <p class="text-secondary-light mb-0">Kelola course pada Arena Belajar.</p>
    </div><a href="<?= site_url('dashboard/pembelajaran/course/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Course</a>
</div><?= $this->include('dashboard/components/filter_bar') ?><div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Program</th>
                        <th>Durasi</th>
                        <th>Level</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'CRS-001', 'name' => 'Dasar-Dasar Pengadaan Barang/Jasa', 'program' => 'PKPBJ 2026', 'duration' => '20 JP', 'level' => 'Dasar', 'status' => 'active'], ['id' => 'CRS-002', 'name' => 'Manajemen Kontrak Pengadaan', 'program' => 'PKPBJ 2026', 'duration' => '16 JP', 'level' => 'Menengah', 'status' => 'active'], ['id' => 'CRS-003', 'name' => 'Transformasi Digital PBJ', 'program' => 'Orientasi Digital', 'duration' => '12 JP', 'level' => 'Dasar', 'status' => 'draft']] as $course): ?><tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($course['name']) ?></h6>
                            </td>
                            <td><?= esc($course['program']) ?></td>
                            <td><?= esc($course['duration']) ?></td>
                            <td><?= esc($course['level']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $course['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/pembelajaran/course/' . $course['id']) ?>" class="text-primary-600 me-12">Detail</a><a href="<?= site_url('dashboard/pembelajaran/course/' . $course['id'] . '/ubah') ?>" class="text-warning-main">Ubah</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>