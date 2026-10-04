<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $verification = $verification ?? []; $user = $user ?? []; $typeProfile = $typeProfile ?? null; $history = $history ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Verifikasi Profil</h6>
        <p class="text-secondary-light mb-0">ID Verifikasi: <?= esc($verification['id'] ?? '-') ?></p>
    </div>
    <a href="<?= site_url('dashboard/akses/verifikasi-profil') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
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
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Nama</p><h6 class="mb-0"><?= esc($user['name'] ?? '-') ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Email</p><h6 class="mb-0"><?= esc($user['email'] ?? '-') ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Tipe Profil</p><h6 class="mb-0"><?= esc(ucfirst($user['profile_type'] ?? '-')) ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Status Akun</p><?= view('dashboard/components/status_badge', ['status' => $user['account_status'] ?? 'draft']) ?></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Wilayah</p><h6 class="mb-0"><?= esc(($user['province_name'] ?? '-') . ' / ' . ($user['regency_name'] ?? '-')) ?></h6></div>
                    <div class="col-md-6"><p class="text-secondary-light mb-4">Alamat</p><h6 class="mb-0"><?= esc($user['address_line'] ?? '-') ?></h6></div>
                </div>
            </div>
        </div>

        <?php if ($typeProfile): ?>
        <div class="card mb-24">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Profil Spesifik</h6>
                <div class="row gy-20">
                    <?php foreach ($typeProfile as $key => $value): ?>
                        <?php if (in_array($key, ['id', 'user_id', 'created_at', 'updated_at'], true)) continue; ?>
                        <div class="col-md-6">
                            <p class="text-secondary-light mb-4"><?= esc(ucwords(str_replace('_', ' ', $key))) ?></p>
                            <h6 class="mb-0"><?= esc(is_array($value) ? implode(', ', $value) : (string) $value ?: '-') ?></h6>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                <h6 class="text-lg mb-20">Tindakan Verifikasi</h6>

                <?php if (in_array($verification['status'] ?? '', ['pending', 'in_review', 'revision_required'], true)): ?>

                <form action="<?= site_url('dashboard/akses/verifikasi-profil/' . (int) ($verification['id'] ?? 0) . '/setujui') ?>" method="post" class="mb-16">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Approve (opsional)</label>
                    <textarea class="form-control radius-8 mb-12" name="notes" rows="2" placeholder="Catatan approval..."></textarea>
                    <button type="submit" class="btn btn-success-600 w-100 radius-8">Setujui Verifikasi</button>
                </form>

                <form action="<?= site_url('dashboard/akses/verifikasi-profil/' . (int) ($verification['id'] ?? 0) . '/revisi') ?>" method="post" class="mb-16">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Revisi <span class="text-danger">*</span></label>
                    <textarea class="form-control radius-8 mb-12" name="revision_note" rows="2" placeholder="Apa yang perlu direvisi?" required></textarea>
                    <button type="submit" class="btn btn-warning-600 w-100 radius-8">Minta Revisi</button>
                </form>

                <form action="<?= site_url('dashboard/akses/verifikasi-profil/' . (int) ($verification['id'] ?? 0) . '/tolak') ?>" method="post">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control radius-8 mb-12" name="reason" rows="2" placeholder="Alasan menolak..." required></textarea>
                    <button type="submit" class="btn btn-danger-600 w-100 radius-8">Tolak Verifikasi</button>
                </form>

                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        Status verifikasi sudah terminal: <strong><?= esc($verification['status'] ?? '-') ?></strong>. Tidak ada tindakan lagi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>