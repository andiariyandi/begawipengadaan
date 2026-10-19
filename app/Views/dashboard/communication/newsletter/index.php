<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Daftar Pelanggan Newsletter') ?></h6>
        <p class="text-secondary-light mb-0">Pengguna yang berlangganan warta dan info pembaruan regulasi PBJ melalui formulir footer portal.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/komunikasi/newsletter/export') ?>" class="btn btn-outline-success radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:file-download-bold" class="text-xl"></iconify-icon>
            <span>Ekspor CSV</span>
        </a>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Alamat Email</th>
                        <th>Status Langganan</th>
                        <th>Waktu Pendaftaran</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada pelanggan newsletter terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($subscribers as $sub): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-sm text-dark"><?= esc($sub['email']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success-main border border-success-subtle px-10 py-6 radius-4 text-xs">
                                        <?= esc(ucfirst($sub['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-secondary text-xs"><?= date('d F Y, H:i', strtotime($sub['created_at'])) ?> WIB</small>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/komunikasi/newsletter/hapus/' . $sub['id']) ?>" onclick="return confirm('Hapus pelanggan email ini?')" class="btn btn-outline-danger btn-sm radius-8 px-8 py-4" title="Hapus Pelanggan">
                                        <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                    </a>
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
