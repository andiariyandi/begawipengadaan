<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $request = $request ?? []; $payload = $payload ?? []; $current = $current ?? []; $history = $history ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Permohonan Perubahan Profil</h6>
        <p class="text-secondary-light mb-0">ID Permohonan: <?= esc($request['id'] ?? '-') ?></p>
    </div>
    <a href="<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Data Pemohon</h6>
                <div class="row gy-20">
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Nama</p><h6 class="mb-0"><?= esc($request['user_name'] ?? '-') ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Email</p><h6 class="mb-0"><?= esc($request['email'] ?? '-') ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Tipe Profil</p><h6 class="mb-0"><?= esc(ucfirst($request['profile_type'] ?? '-')) ?></h6></div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status</p>
                        <?= view('dashboard/components/status_badge', ['status' => $request['status'] ?? 'draft']) ?>
                    </div>
                    <?php if (! empty($request['reason'])): ?>
                        <div class="col-12">
                            <p class="text-secondary-light mb-4">Alasan Pengajuan</p>
                            <p class="mb-0"><?= esc($request['reason']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Perbandingan Data Lama vs Baru</h6>
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Data Lama</th>
                                <th>Data Baru (Diajukan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allKeys = array_unique(array_merge(array_keys($current), array_keys($payload)));
                            $skipKeys = ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at'];
                            $rows = 0;
                            ?>
                            <?php foreach ($allKeys as $key): ?>
                                <?php if (in_array($key, $skipKeys, true)) continue; ?>
                                <?php
                                $oldVal = $current[$key] ?? null;
                                $newVal = $payload[$key] ?? null;
                                $isArray = is_array($newVal);
                                if ($isArray) { $newVal = implode(', ', $newVal); }
                                if (is_array($oldVal)) { $oldVal = implode(', ', $oldVal); }
                                $changed = (string) $oldVal !== (string) $newVal;
                                ?>
                                <tr class="<?= $changed ? 'bg-warning-50' : '' ?>">
                                    <td class="fw-semibold"><?= esc(ucwords(str_replace('_', ' ', $key))) ?></td>
                                    <td><?= esc((string) ($oldVal ?? '-') ?: '-') ?></td>
                                    <td><strong><?= esc((string) ($newVal ?? '-') ?: '-') ?></strong></td>
                                </tr>
                                <?php $rows++; ?>
                            <?php endforeach; ?>
                            <?php if ($rows === 0): ?>
                                <tr><td colspan="3" class="text-center text-secondary-light">Tidak ada perubahan diajukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16">Riwayat Workflow</h6>
                <?php if (empty($history)): ?>
                    <p class="text-secondary-light mb-0">Belum ada transisi workflow.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead><tr><th>Waktu</th><th>Action</th><th>From → To</th><th>Alasan</th></tr></thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td><?= esc($h['created_at']) ?></td>
                                        <td><?= esc($h['action']) ?></td>
                                        <td><?= esc(($h['from_state'] ?? '-') . ' → ' . ($h['to_state'] ?? '-')) ?></td>
                                        <td><?= esc($h['reason'] ?? '-') ?></td>
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
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Tindakan</h6>

                <?php if (in_array($request['status'] ?? '', ['submitted', 'in_review', 'revision_required'], true)): ?>

                <form action="<?= site_url('dashboard/akses/permohonan-perubahan-profil/' . (int) ($request['id'] ?? 0) . '/setujui') ?>" method="post" class="mb-16">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan (opsional)</label>
                    <textarea class="form-control radius-8 mb-12" name="notes" rows="2" placeholder="Catatan approval..."></textarea>
                    <button type="submit" class="btn btn-success-600 w-100 radius-8">Setujui & Terapkan</button>
                </form>

                <form action="<?= site_url('dashboard/akses/permohonan-perubahan-profil/' . (int) ($request['id'] ?? 0) . '/revisi') ?>" method="post" class="mb-16">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Revisi <span class="text-danger">*</span></label>
                    <textarea class="form-control radius-8 mb-12" name="revision_note" rows="2" placeholder="Apa yang perlu direvisi?" required></textarea>
                    <button type="submit" class="btn btn-warning-600 w-100 radius-8">Minta Revisi</button>
                </form>

                <form action="<?= site_url('dashboard/akses/permohonan-perubahan-profil/' . (int) ($request['id'] ?? 0) . '/tolak') ?>" method="post">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control radius-8 mb-12" name="reason" rows="2" placeholder="Alasan menolak..." required></textarea>
                    <button type="submit" class="btn btn-danger-600 w-100 radius-8">Tolak Permohonan</button>
                </form>

                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        Status permohonan sudah terminal: <strong><?= esc($request['status'] ?? '-') ?></strong>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>