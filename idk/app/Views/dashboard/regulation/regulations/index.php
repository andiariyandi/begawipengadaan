<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $regulations = $regulations ?? []; $categories = $categories ?? []; $filters = $filters ?? []; $workflowStatuses = $workflowStatuses ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Regulasi PBJ</h6>
        <p class="text-secondary-light mb-0">Kelola dokumen regulasi Pengadaan Barang/Jasa.</p>
    </div>
    <a href="<?= site_url('dashboard/regulasi/dokumen/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Regulasi</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form action="<?= site_url('dashboard/regulasi/dokumen') ?>" method="get" class="mb-24">
    <div class="row g-12 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kata Kunci</label>
            <input type="text" class="form-control radius-8" name="keyword" value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Judul atau nomor...">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label>
            <select class="form-select radius-8" name="category_id">
                <option value="">Semua</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($filters['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                        <?= esc($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Workflow</label>
            <select class="form-select radius-8" name="workflow_status">
                <option value="">Semua</option>
                <?php foreach ($workflowStatuses as $ws): ?>
                    <option value="<?= esc($ws) ?>" <?= ($filters['workflow_status'] ?? '') === $ws ? 'selected' : '' ?>>
                        <?= esc(ucwords(str_replace('_', ' ', $ws))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Filter</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Regulasi</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th>Versi Aktif</th>
                        <th class="text-center">Status Workflow</th>
                        <th class="text-center">Status Legal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($regulations)): ?>
                        <tr><td colspan="7" class="text-center text-secondary-light">Belum ada regulasi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($regulations as $r): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-4 fw-medium"><?= esc($r['document_number'] ?? '-') ?></h6>
                                <span class="text-sm text-secondary-light"><?= esc($r['title'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($r['category_name'] ?? '-') ?></td>
                            <td><?= esc($r['document_year'] ?? '-') ?></td>
                            <td>v<?= esc($r['current_version_number'] ?? '0') ?></td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => $r['workflow_status'] ?? 'draft']) ?>
                            </td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => $r['legal_status'] ?? 'active']) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Detail">
                                    <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                </a>
                                <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) $r['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center ms-6" title="Ubah">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>