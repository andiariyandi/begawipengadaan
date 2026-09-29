<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Penilaian</h6>
        <p class="text-secondary-light mb-0">Monitor dan tinjau hasil penilaian peserta.</p>
    </div>
</div><?= $this->include('dashboard/components/filter_bar') ?><div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kelas</th>
                        <th>Komponen</th>
                        <th>Nilai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'GRD-001', 'name' => 'Rina Oktaviani', 'class' => 'PBJ Dasar Batch 1', 'component' => 'Assignment 1', 'score' => '88', 'status' => 'approved'], ['id' => 'GRD-002', 'name' => 'Budi Santoso', 'class' => 'PBJ Dasar Batch 1', 'component' => 'Assignment 1', 'score' => '-', 'status' => 'pending'], ['id' => 'GRD-003', 'name' => 'Siti Nurhaliza', 'class' => 'Manajemen Kontrak Batch 2', 'component' => 'Quiz Modul 2', 'score' => '92', 'status' => 'approved']] as $grading): ?><tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($grading['name']) ?></h6>
                            </td>
                            <td><?= esc($grading['class']) ?></td>
                            <td><?= esc($grading['component']) ?></td>
                            <td><span class="fw-semibold"><?= esc($grading['score']) ?></span></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $grading['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/pembelajaran/penilaian/' . $grading['id']) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>