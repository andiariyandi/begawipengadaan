<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode      = $formMode ?? 'create';
$isEdit        = $formMode === 'edit';
$regulations   = $regulations ?? [];
$relationTypes = $relationTypes ?? [
    'amends', 'amended_by', 'revokes', 'revoked_by',
    'replaces', 'replaced_by', 'implements', 'implemented_by',
    'references', 'referenced_by',
];

$typeLabels = [
    'amends'         => 'Mengubah (Amends)',
    'amended_by'     => 'Diubah Oleh (Amended By)',
    'revokes'        => 'Mencabut (Revokes)',
    'revoked_by'     => 'Dicabut Oleh (Revoked By)',
    'replaces'       => 'Menggantikan (Replaces)',
    'replaced_by'    => 'Digantikan Oleh (Replaced By)',
    'implements'     => 'Melaksanakan Pedoman Teknis (Implements)',
    'implemented_by' => 'Dilaksanakan Oleh (Implemented By)',
    'references'     => 'Mendasarkan / Mengacu (References)',
    'referenced_by'  => 'Diacu Oleh (Referenced By)',
];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tambah Relasi Antar-Regulasi</h6>
        <p class="text-secondary-light mb-0">Hubungkan dua naskah hukum untuk membentuk silsilah keterikatan regulasi (mengubah, mencabut, melaksanakan, atau mengacu).</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
        <span>Kembali ke Matriks</span>
    </a>
</div>

<div class="card border-0 radius-16 shadow-sm bg-white">
    <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
        <iconify-icon icon="solar:link-circle-bold" class="text-primary-600 text-xl"></iconify-icon>
        <h6 class="text-base fw-bold mb-0">Formulir Keterikatan Regulasi</h6>
    </div>
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/regulasi/relasi/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Regulasi Sumber (Induk / Dokumen A) <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="source_regulation_id" required>
                        <option value="">-- Pilih Regulasi Sumber --</option>
                        <?php foreach ($regulations as $r): ?>
                            <option value="<?= (int) $r['id'] ?>">
                                [<?= esc($r['document_number'] ?? '-') ?>] <?= esc($r['title'] ?? '-') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-xs text-secondary-light mt-1 d-block">Dokumen yang menjadi subjek relasi.</span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Hubungan / Silsilah Hukum <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="relation_type" required>
                        <option value="">-- Pilih Tipe Relasi --</option>
                        <?php foreach ($relationTypes as $t): ?>
                            <option value="<?= esc($t) ?>">
                                <?= esc($typeLabels[$t] ?? ucwords(str_replace('_', ' ', $t))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Regulasi Target (Dokumen B yang Dihubungkan) <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="target_regulation_id" required>
                        <option value="">-- Pilih Regulasi Terkait --</option>
                        <?php foreach ($regulations as $r): ?>
                            <option value="<?= (int) $r['id'] ?>">
                                [<?= esc($r['document_number'] ?? '-') ?>] <?= esc($r['title'] ?? '-') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-xs text-secondary-light mt-1 d-block">Dokumen yang menjadi objek relasi (tidak boleh sama dengan dokumen sumber).</span>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Substansi Relasi (Opsional)</label>
                    <textarea class="form-control radius-8" name="notes" rows="3" placeholder="Contoh: Mengubah ketentuan Pasal 13 dan Lampiran II mengenai pengadaan barang khusus..."><?= esc(old('notes', '')) ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 pt-12 border-top">
                    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-bold" class="text-lg"></iconify-icon>
                        <span>Simpan Relasi Regulasi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>