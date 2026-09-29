<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Permohonan Role</h6>
        <p class="text-secondary-light mb-0">Tinjau permohonan penambahan role pengguna.</p>
    </div>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Role Diminta</th>
                        <th>Diajukan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Andi Pratama</td>
                        <td>Trainer</td>
                        <td>28 Sep 2026</td>
                        <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => 'pending']) ?></td>
                        <td class="text-center"><a href="<?= site_url('dashboard/akses/permohonan-role/RR-001') ?>" class="text-primary-600">Tinjau</a></td>
                    </tr>
                    <tr>
                        <td>Dr. Maya Sari</td>
                        <td>Evaluator</td>
                        <td>26 Sep 2026</td>
                        <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => 'review']) ?></td>
                        <td class="text-center"><a href="<?= site_url('dashboard/akses/permohonan-role/RR-002') ?>" class="text-primary-600">Tinjau</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>