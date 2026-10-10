<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $relations = $relations ?? []; $relationTypes = $relationTypes ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Relasi Regulasi</h6>
        <p class="text-secondary-light mb-0">Kelola hubungan antar dokumen regulasi.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/relasi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Relasi</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Regulasi Sumber</th>
                        <th>Tipe Relasi</th>
                        <th>Regulasi Terkait</th>
                        <th>Catatan</th>
                        <th>Pembuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($relations)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada relasi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($relations as $r): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($r['source_title'] ?? '-') ?></h6>
                                <small class="text-secondary-light"><?= esc($r['source_number'] ?? '-') ?></small>
                            </td>
                            <td><span class="badge bg-primary-50 text-primary-600 px-12 py-8 radius-8"><?= esc($r['relation_type']) ?></span></td>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($r['target_title'] ?? '-') ?></h6>
                                <small class="text-secondary-light"><?= esc($r['target_number'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($r['notes'] ?? '-') ?: '-' ?></td>
                            <td><?= esc($r['creator_username'] ?? '-') ?></td>
                            <td class="text-center">
                                <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0"
                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                    data-action="<?= site_url('dashboard/regulasi/relasi/' . (int) $r['id'] . '/hapus') ?>"
                                    data-method="POST"
                                    data-title="Hapus Relasi Regulasi"
                                    data-message="Apakah Anda yakin ingin menghapus relasi regulasi ini?"
                                    data-btn-text="Hapus Relasi"
                                    data-btn-class="btn-danger-600"
                                    data-icon="fluent:delete-24-regular"
                                    data-icon-color="danger"
                                    title="Hapus Relasi">
                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>