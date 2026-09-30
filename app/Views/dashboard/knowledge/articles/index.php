<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Artikel</h6>
        <p class="text-secondary-light mb-0">Kelola artikel dan praktik baik PBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/bunga-rampai/artikel/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Artikel</a>
</div>
<?= $this->include('dashboard/components/filter_bar') ?>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Artikel</th><th>Kategori</th><th>Penulis</th><th>Diperbarui</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ([
                        ['id' => 'ART-001', 'title' => 'Prinsip Pengadaan Barang/Jasa Pemerintah', 'category' => 'Dasar PBJ', 'author' => 'Admin Bunga Rampai', 'date' => '28 Sep 2026', 'status' => 'active'],
                        ['id' => 'ART-002', 'title' => 'Strategi Perencanaan Pengadaan yang Efektif', 'category' => 'Perencanaan', 'author' => 'Tim Pembelajaran', 'date' => '26 Sep 2026', 'status' => 'review'],
                        ['id' => 'ART-003', 'title' => 'Praktik Baik Pengelolaan Kontrak', 'category' => 'Manajemen Kontrak', 'author' => 'Editor BegawiPBJ', 'date' => '22 Sep 2026', 'status' => 'draft'],
                    ] as $article): ?>
                        <tr>
                            <td><h6 class="text-md mb-0 fw-medium"><?= esc($article['title']) ?></h6><span class="text-sm text-secondary-light"><?= esc($article['id']) ?></span></td>
                            <td><?= esc($article['category']) ?></td>
                            <td><?= esc($article['author']) ?></td>
                            <td><?= esc($article['date']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $article['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/bunga-rampai/artikel/' . $article['id']) ?>" class="text-primary-600 me-12">Detail</a><a href="<?= site_url('dashboard/bunga-rampai/artikel/' . $article['id'] . '/ubah') ?>" class="text-warning-main">Ubah</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>