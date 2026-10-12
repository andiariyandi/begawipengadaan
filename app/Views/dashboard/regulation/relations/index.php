<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$relations     = $relations ?? [];
$relationTypes = $relationTypes ?? [];

$typeLabels = [
    'amends'         => ['label' => 'Mengubah', 'class' => 'bg-warning-50 text-warning-700 border-warning-200'],
    'amended_by'     => ['label' => 'Diubah Oleh', 'class' => 'bg-warning-50 text-warning-700 border-warning-200'],
    'revokes'        => ['label' => 'Mencabut', 'class' => 'bg-danger-50 text-danger-700 border-danger-200'],
    'revoked_by'     => ['label' => 'Dicabut Oleh', 'class' => 'bg-danger-50 text-danger-700 border-danger-200'],
    'replaces'       => ['label' => 'Menggantikan', 'class' => 'bg-info-50 text-info-700 border-info-200'],
    'replaced_by'    => ['label' => 'Digantikan Oleh', 'class' => 'bg-info-50 text-info-700 border-info-200'],
    'implements'     => ['label' => 'Melaksanakan', 'class' => 'bg-success-50 text-success-700 border-success-200'],
    'implemented_by' => ['label' => 'Dilaksanakan Oleh', 'class' => 'bg-success-50 text-success-700 border-success-200'],
    'references'     => ['label' => 'Mendasarkan', 'class' => 'bg-primary-50 text-primary-700 border-primary-200'],
    'referenced_by'  => ['label' => 'Diacu Oleh', 'class' => 'bg-primary-50 text-primary-700 border-primary-200'],
];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Relasi Regulasi</h6>
        <p class="text-secondary-light mb-0">Kelola matriks hubungan silsilah hukum antar dokumen perundang-undangan PBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/relasi/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Relasi Baru</span>
    </a>
</div>

<div class="card radius-12 border bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 240px;">Regulasi Sumber</th>
                        <th class="text-center" style="min-width: 140px;">Tipe Hubungan</th>
                        <th style="min-width: 240px;">Regulasi Terkait</th>
                        <th>Catatan Hubungan</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($relations)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-32 text-secondary-light">
                                <iconify-icon icon="solar:link-broken" class="text-4xl d-block mb-2 text-neutral-400"></iconify-icon>
                                Belum ada relasi regulasi yang tercatat dalam sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($relations as $r): ?>
                        <?php
                        $tInfo = $typeLabels[$r['relation_type'] ?? ''] ?? [
                            'label' => ucwords(str_replace('_', ' ', $r['relation_type'] ?? '-')),
                            'class' => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                        ];
                        ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($r['source_regulation_id'] ?? 0)) ?>" class="fw-bold text-sm text-primary-700 text-hover-primary-900 d-block mb-1">
                                    <?= esc($r['source_number'] ?? '-') ?>
                                </a>
                                <span class="text-xs text-secondary-light text-line-2"><?= esc($r['source_title'] ?? '-') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $tInfo['class'] ?> border radius-6 px-10 py-6 text-xs fw-bold">
                                    <iconify-icon icon="solar:link-circle-outline" class="align-middle me-1"></iconify-icon>
                                    <?= esc($tInfo['label']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($r['target_regulation_id'] ?? 0)) ?>" class="fw-bold text-sm text-primary-700 text-hover-primary-900 d-block mb-1">
                                    <?= esc($r['target_number'] ?? '-') ?>
                                </a>
                                <span class="text-xs text-secondary-light text-line-2"><?= esc($r['target_title'] ?? '-') ?></span>
                            </td>
                            <td><span class="text-xs text-secondary-light text-line-2"><?= esc($r['notes'] ?? '-') ?: '-' ?></span></td>
                            <td><span class="text-xs text-dark fw-medium"><?= esc($r['creator_username'] ?? 'Admin') ?></span></td>
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