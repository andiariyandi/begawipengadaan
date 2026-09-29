<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Verifikasi Profil</h6>
        <p class="text-secondary-light mb-0">Tinjau kelengkapan dan validitas profil pengguna.</p>
    </div>
</div><?= $this->include('dashboard/components/filter_bar') ?><div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Tipe Profil</th>
                        <th>Diajukan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'PV-001', 'name' => 'Andi Pratama', 'profile' => 'Penyedia Barang/Jasa', 'date' => '28 Sep 2026', 'status' => 'pending'], ['id' => 'PV-002', 'name' => 'Siti Nurhaliza', 'profile' => 'ASN', 'date' => '27 Sep 2026', 'status' => 'review']] as $item): ?><tr>
                            <td>
                                <h6 class="text-md mb-0"><?= esc($item['name']) ?></h6>
                            </td>
                            <td><?= esc($item['profile']) ?></td>
                            <td><?= esc($item['date']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $item['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/verifikasi-profil/' . $item['id']) ?>" class="text-primary-600">Tinjau</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>