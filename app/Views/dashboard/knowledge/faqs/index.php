<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">FAQ</h6>
        <p class="text-secondary-light mb-0">Kelola pertanyaan yang sering diajukan.</p>
    </div><a href="<?= site_url('dashboard/bunga-rampai/faq/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah FAQ</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th>Kategori</th>
                        <th>Urutan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['id' => 'FAQ-001', 'question' => 'Bagaimana cara mengikuti kelas di Arena Belajar?', 'category' => 'Pembelajaran', 'order' => '1', 'status' => 'active'], ['id' => 'FAQ-002', 'question' => 'Bagaimana cara memverifikasi sertifikat?', 'category' => 'Sertifikat', 'order' => '2', 'status' => 'active'], ['id' => 'FAQ-003', 'question' => 'Bagaimana mengajukan perubahan profil?', 'category' => 'Akun', 'order' => '3', 'status' => 'draft']] as $faq): ?><tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($faq['question']) ?></h6>
                            </td>
                            <td><?= esc($faq['category']) ?></td>
                            <td><?= esc($faq['order']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $faq['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/bunga-rampai/faq/' . $faq['id']) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>