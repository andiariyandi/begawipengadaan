<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Template Sertifikat</h6>
        <p class="text-secondary-light mb-0">Kelola template desain sertifikat. Hanya yang berstatus <strong>Aktif</strong> akan dipakai generator.</p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/template/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">
        Tambah Template
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="card">
        <div class="card-body p-24 text-center text-secondary-light">
            Belum ada template sertifikat. Klik <em>Tambah Template</em> untuk mulai.
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe Sumber</th>
                            <th>Status</th>
                            <th>Diperbarui</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $row): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($row['name']) ?></h6>
                            </td>
                            <td>
                                <span class="text-sm"><?= esc($row['source_type']) ?></span>
                            </td>
                            <td>
                                <?= view('dashboard/components/status_badge', ['status' => (int) $row['is_active'] === 1 ? 'active' : 'inactive']) ?>
                            </td>
                            <td>
                                <small class="text-secondary-light">
                                    <?= ! empty($row['updated_at']) ? esc(date('d M Y H:i', strtotime($row['updated_at']))) : '—' ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id']) ?>" class="text-primary-600 fw-semibold me-12">
                                    Lihat
                                </a>
                                <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/ubah') ?>" class="text-secondary-light fw-semibold me-12">
                                    Ubah
                                </a>
                                <?php if ((int) $row['is_active'] === 1): ?>
                                    <form method="post"
                                          action="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/nonaktifkan') ?>"
                                          class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-link p-0 text-warning-600">Nonaktifkan</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post"
                                          action="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/aktifkan') ?>"
                                          class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-link p-0 text-success-600">Aktifkan</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
