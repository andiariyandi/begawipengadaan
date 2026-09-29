<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Unit Organisasi</h6>
        <p class="text-secondary-light mb-0">Master data organisasi dan OPD.</p>
    </div><a href="<?= site_url('dashboard/akses/unit-organisasi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Unit</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Unit</th>
                        <th>Tipe</th>
                        <th>Induk</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'ORG-001', 'code' => 'BPPBJ', 'name' => 'Biro Pengadaan Barang dan Jasa', 'type' => 'Biro', 'parent' => 'Sekretariat Daerah'], ['id' => 'ORG-002', 'code' => 'INS', 'name' => 'Inspektorat Provinsi Lampung', 'type' => 'Inspektorat', 'parent' => 'Pemerintah Provinsi Lampung'], ['id' => 'ORG-003', 'code' => 'BKD', 'name' => 'Badan Kepegawaian Daerah', 'type' => 'Badan', 'parent' => 'Pemerintah Provinsi Lampung']] as $unit): ?><tr>
                            <td><span class="fw-semibold text-primary-600"><?= esc($unit['code']) ?></span></td>
                            <td><?= esc($unit['name']) ?></td>
                            <td><?= esc($unit['type']) ?></td>
                            <td><?= esc($unit['parent']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => 'active']) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/unit-organisasi/' . $unit['id']) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>