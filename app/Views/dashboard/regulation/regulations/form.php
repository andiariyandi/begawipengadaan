<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode = $formMode ?? 'create';
$isEdit = $formMode === 'edit';
$regulation = $regulation ?? [];
$regId = $regId ?? null;
$categories = $categories ?? [];
$workflowStatuses = $workflowStatuses ?? [];
$legalStatuses = $legalStatuses ?? [];
$visibilities = $visibilities ?? [];
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger mb-16"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Regulasi') ?></h6>
        <p class="text-secondary-light mb-0">Form <?= $isEdit ? 'Ubah' : 'Tambah' ?> Regulasi.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/regulasi/dokumen/' . (int) $regId . '/ubah') : site_url('dashboard/regulasi/dokumen') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Regulasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="title" value="<?= esc(old('title') ?? $regulation['title'] ?? '') ?>" required maxlength="255">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun <span class="text-danger">*</span></label>
                    <input type="number" min="1900" max="2100" class="form-control radius-8" name="document_year" value="<?= esc(old('document_year') ?? $regulation['document_year'] ?? date('Y')) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8" name="document_number" value="<?= esc(old('document_number') ?? $regulation['document_number'] ?? '') ?>" placeholder="Contoh: Perpres 12/2021" required maxlength="255">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="category_id" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) ($regulation['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Penerbit / Issuer</label>
                    <input type="text" class="form-control radius-8" name="issuer_name" value="<?= esc(old('issuer_name') ?? $regulation['issuer_name'] ?? '') ?>" maxlength="255">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Resmi</label>
                    <input type="date" class="form-control radius-8" name="official_date" value="<?= esc(old('official_date') ?? (isset($regulation['official_date']) ? substr((string) $regulation['official_date'], 0, 10) : '')) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Berlaku</label>
                    <input type="date" class="form-control radius-8" name="effective_date" value="<?= esc(old('effective_date') ?? (isset($regulation['effective_date']) ? substr((string) $regulation['effective_date'], 0, 10) : '')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Legal</label>
                    <select class="form-select radius-8" name="legal_status">
                        <?php foreach ($legalStatuses as $ls): ?>
                            <option value="<?= esc($ls) ?>" <?= ($regulation['legal_status'] ?? 'active') === $ls ? 'selected' : '' ?>><?= esc(ucfirst($ls)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Visibilitas</label>
                    <select class="form-select radius-8" name="visibility">
                        <?php foreach ($visibilities as $v): ?>
                            <option value="<?= esc($v) ?>" <?= ($regulation['visibility'] ?? 'internal') === $v ? 'selected' : '' ?>><?= esc(ucfirst($v)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">URL Sumber Resmi</label>
                    <input type="url" class="form-control radius-8" name="official_source_url" value="<?= esc(old('official_source_url') ?? $regulation['official_source_url'] ?? '') ?>" placeholder="https://...">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ringkasan</label>
                    <textarea class="form-control radius-8" name="summary" rows="4"><?= esc(old('summary') ?? $regulation['summary'] ?? '') ?></textarea>
                </div>

                <?php if (! $isEdit): ?>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Dokumen Versi Awal (PDF/DOC, maks 10 MB)</label>
                    <input type="file" class="form-control radius-8" name="file" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                </div>
                <?php endif; ?>

                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Regulasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>