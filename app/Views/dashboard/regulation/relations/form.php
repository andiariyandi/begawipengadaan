<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $formMode = $formMode ?? 'create'; $isEdit = $formMode === 'edit'; $regulations = $regulations ?? []; $relationTypes = $relationTypes ?? []; ?>

<?php if (! empty($errors ?? [])): ?>
    <div class="alert alert-danger mb-16"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tambah Relasi</h6>
        <p class="text-secondary-light mb-0">Hubungkan dua regulasi dengan relasi tertentu.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/regulasi/relasi/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Regulasi Sumber <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="source_regulation_id" required>
                        <option value="">Pilih regulasi</option>
                        <?php foreach ($regulations as $r): ?>
                            <option value="<?= (int) $r['id'] ?>"><?= esc($r['title'] ?? '-') ?> (<?= esc($r['document_number'] ?? '-') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Relasi <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="relation_type" required>
                        <option value="">Pilih tipe</option>
                        <?php foreach ($relationTypes as $t): ?>
                            <option value="<?= esc($t) ?>"><?= esc(ucwords(str_replace('_', ' ', $t))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Regulasi Target <span class="text-danger">*</span></label>
                    <select class="form-select radius-8" name="target_regulation_id" required>
                        <option value="">Pilih regulasi</option>
                        <?php foreach ($regulations as $r): ?>
                            <option value="<?= (int) $r['id'] ?>"><?= esc($r['title'] ?? '-') ?> (<?= esc($r['document_number'] ?? '-') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan</label>
                    <textarea class="form-control radius-8" name="notes" rows="3" placeholder="Catatan relasi..."></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Relasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>