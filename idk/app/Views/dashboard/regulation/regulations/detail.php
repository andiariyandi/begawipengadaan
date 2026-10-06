<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $regulation = $regulation ?? []; $versions = $regulation['versions'] ?? []; $relations = $relations ?? []; $competencies = $competencies ?? []; $transitions = $transitions ?? []; $workflowStatuses = $workflowStatuses ?? []; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Regulasi</h6>
        <p class="text-secondary-light mb-0">ID Regulasi: <?= esc($regulation['id'] ?? '-') ?></p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/regulasi/dokumen') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <a href="<?= site_url('dashboard/regulasi/dokumen/' . (int) ($regulation['id'] ?? 0) . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
    </div>
</div>

<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-body p-24">
                <h5 class="mb-4"><?= esc($regulation['document_number'] ?? '-') ?></h5>
                <h6 class="mb-2 fw-medium"><?= esc($regulation['title'] ?? '-') ?></h6>
                <p class="text-secondary-light mb-20"><?= esc($regulation['summary'] ?? '-') ?></p>

                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kategori</p>
                        <h6 class="mb-0"><?= esc($regulation['category_name'] ?? '-') ?></h6>
                    </div>
                    <div class="col-md-3">
                        <p class="text-secondary-light mb-4">Tahun</p>
                        <h6 class="mb-0"><?= esc($regulation['document_year'] ?? '-') ?></h6>
                    </div>
                    <div class="col-md-3">
                        <p class="text-secondary-light mb-4">Versi Aktif</p>
                        <h6 class="mb-0">v<?= esc($regulation['current_version_number'] ?? '0') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Penerbit</p>
                        <h6 class="mb-0"><?= esc($regulation['issuer_name'] ?? '-') ?: '-' ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Workflow Status</p>
                        <?= view('dashboard/components/status_badge', ['status' => $regulation['workflow_status'] ?? 'draft']) ?>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Tanggal Resmi</p>
                        <h6 class="mb-0"><?= esc($regulation['official_date'] ? substr((string) $regulation['official_date'], 0, 10) : '-') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Tanggal Berlaku</p>
                        <h6 class="mb-0"><?= esc($regulation['effective_date'] ? substr((string) $regulation['effective_date'], 0, 10) : '-') ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Riwayat Versi</h6>
                <?php if (empty($versions)): ?>
                    <p class="text-secondary-light mb-0">Belum ada versi.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr><th>Versi</th><th>File</th><th>Ukuran</th><th>Status</th><th>Tanggal</th><th class="text-center">Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($versions as $v): ?>
                                    <tr>
                                        <td>v<?= esc($v['version_number']) ?><?= $v['is_current'] ? ' <span class="badge bg-success-50 text-success-600 ms-2">Aktif</span>' : '' ?></td>
                                        <td><?= esc($v['file_original_name'] ?? '-') ?></td>
                                        <td><?= isset($v['file_size']) ? esc(round($v['file_size'] / 1024)) . ' KB' : '-' ?></td>
                                        <td><?= view('dashboard/components/status_badge', ['status' => $v['is_current'] ? 'current' : 'inactive']) ?></td>
                                        <td><?= esc($v['created_at'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <?php if (! $v['is_current']): ?>
                                                <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi/current') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="version_id" value="<?= (int) $v['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-primary-600 btn-sm radius-8">Set Aktif</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <hr class="my-24">
                <h6 class="text-lg mb-16">Tambah Versi Baru</h6>
                <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/versi') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-12">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">File (PDF/DOC)</label>
                            <input type="file" class="form-control radius-8" name="file" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Perubahan</label>
                            <input type="text" class="form-control radius-8" name="changelog" placeholder="Apa yang berubah...">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Upload Versi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Relasi Regulasi</h6>
                <?php if (empty($relations)): ?>
                    <p class="text-secondary-light mb-0">Belum ada relasi.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead><tr><th>Sumber</th><th>Tipe</th><th>Target</th></tr></thead>
                            <tbody>
                                <?php foreach ($relations as $r): ?>
                                    <tr>
                                        <td><?= esc($r['source_title'] ?? '-') ?></td>
                                        <td><span class="badge bg-primary-50 text-primary-600 px-12 py-8 radius-8"><?= esc($r['relation_type']) ?></span></td>
                                        <td><?= esc($r['target_title'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="mt-16">
                    <a href="<?= site_url('dashboard/regulasi/relasi') ?>" class="btn btn-outline-primary-600 btn-sm radius-8">Kelola Relasi</a>
                </div>
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Kompetensi Terkait</h6>
                <?php if (empty($competencies)): ?>
                    <p class="text-secondary-light mb-0">Belum ada kompetensi terkait.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($competencies as $c): ?>
                            <li><span class="badge bg-info-50 text-info-600 px-12 py-8 radius-8 me-6"><?= esc($c['key']) ?></span> <?= esc($c['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/kompetensi') ?>" method="post" class="mt-16">
                    <?= csrf_field() ?>
                    <div class="row g-12 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tambah Kompetensi</label>
                            <select class="form-select radius-8" name="competency_id">
                                <option value="">Pilih kompetensi</option>
                                <?php foreach ((new \App\Models\Regulation\CompetencyModel())->listActive() as $comp): ?>
                                    <option value="<?= (int) $comp['id'] ?>"><?= esc($comp['name']) ?> (<?= esc($comp['key']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Attach</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Riwayat Workflow</h6>
                <?php if (empty($transitions)): ?>
                    <p class="text-secondary-light mb-0">Belum ada transisi.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead><tr><th>Waktu</th><th>Action</th><th>From → To</th><th>Alasan</th></tr></thead>
                            <tbody>
                                <?php foreach ($transitions as $t): ?>
                                    <tr>
                                        <td><?= esc($t['created_at']) ?></td>
                                        <td><?= esc($t['action']) ?></td>
                                        <td><?= esc(($t['from_state'] ?? '-') . ' → ' . ($t['to_state'] ?? '-')) ?></td>
                                        <td><?= esc($t['reason'] ?? '-') ?: '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Tindakan Workflow</h6>

                <?php if (($regulation['workflow_status'] ?? '') === 'draft'): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/submit') ?>" method="post" class="mb-12">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary-600 w-100 radius-8">Submit untuk Review</button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($regulation['workflow_status'] ?? '', ['submitted', 'in_review'], true)): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/approve') ?>" method="post" class="mb-12">
                        <?= csrf_field() ?>
                        <textarea class="form-control radius-8 mb-12" name="notes" rows="2" placeholder="Catatan approval..."></textarea>
                        <button type="submit" class="btn btn-success-600 w-100 radius-8">Setujui</button>
                    </form>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/revision') ?>" method="post" class="mb-12">
                        <?= csrf_field() ?>
                        <textarea class="form-control radius-8 mb-12" name="note" rows="2" placeholder="Catatan revisi..." required></textarea>
                        <button type="submit" class="btn btn-warning-600 w-100 radius-8">Minta Revisi</button>
                    </form>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/reject') ?>" method="post">
                        <?= csrf_field() ?>
                        <textarea class="form-control radius-8 mb-12" name="reason" rows="2" placeholder="Alasan menolak..." required></textarea>
                        <button type="submit" class="btn btn-danger-600 w-100 radius-8">Tolak</button>
                    </form>
                <?php endif; ?>

                <?php if (($regulation['workflow_status'] ?? '') === 'approved'): ?>
                    <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/publish') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary-600 w-100 radius-8">Publikasikan</button>
                    </form>
                <?php endif; ?>

                <?php if (($regulation['workflow_status'] ?? '') === 'published'): ?>
                    <div class="alert alert-success mb-0">Regulasi sudah dipublikasikan.</div>
                <?php endif; ?>

                <hr class="my-20">
                <form action="<?= site_url('dashboard/regulasi/dokumen/' . (int) $regulation['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus regulasi ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger-600 w-100 radius-8">Hapus (Soft)</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>