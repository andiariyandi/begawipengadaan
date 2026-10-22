<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'levels']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Jenjang Jabatan JF PPBJ') ?></h6>
        <p class="text-secondary-light mb-0">Klasifikasi hierarki jabatan fungsional Pengelola Pengadaan Barang/Jasa sesuai PermenPAN-RB dan standar BKN.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
        Daftar Personel JF
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th>Kode Kunci</th>
                        <th>Jenjang Jabatan Fungsional</th>
                        <th class="text-center">Jumlah Personel Aktif</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($levels)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-24 text-secondary-light">Data jenjang jabatan belum tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($levels as $lvl): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= (int) $lvl['sort_order'] ?></td>
                                <td>
                                    <span class="text-primary-600 fw-bold font-monospace"><?= esc($lvl['key']) ?></span>
                                </td>
                                <td>
                                    <h6 class="text-md mb-0 fw-semibold text-primary-light"><?= esc($lvl['name']) ?></h6>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-6 fs-6 fw-bold">
                                        <?= (int) ($lvl['personnel_count'] ?? 0) ?> Orang
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $lvl['is_active'] ? 'active' : 'inactive',
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
