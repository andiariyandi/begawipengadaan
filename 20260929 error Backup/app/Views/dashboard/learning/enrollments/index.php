<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Enrollment</h6>
        <p class="text-secondary-light mb-0">Monitor pendaftaran peserta pada kelas.</p>
    </div>
</div><?= $this->include('dashboard/components/filter_bar') ?><div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas</th>
                        <th>Course</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'ENR-001', 'name' => 'Rina Oktaviani', 'class' => 'PBJ Dasar Batch 1', 'course' => 'Dasar-Dasar PBJ', 'date' => '02 Sep 2026', 'status' => 'approved'], ['id' => 'ENR-002', 'name' => 'Budi Santoso', 'class' => 'PBJ Dasar Batch 1', 'course' => 'Dasar-Dasar PBJ', 'date' => '04 Sep 2026', 'status' => 'pending'], ['id' => 'ENR-003', 'name' => 'Siti Nurhaliza', 'class' => 'Manajemen Kontrak Batch 2', 'course' => 'Manajemen Kontrak', 'date' => '12 Sep 2026', 'status' => 'approved']] as $enrollment): ?><tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($enrollment['name']) ?></h6>
                            </td>
                            <td><?= esc($enrollment['class']) ?></td>
                            <td><?= esc($enrollment['course']) ?></td>
                            <td><?= esc($enrollment['date']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $enrollment['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/pembelajaran/enrollment/' . $enrollment['id']) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>