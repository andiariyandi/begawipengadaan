<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$flashMsg = session()->getFlashdata('message');
$flashErr = session()->getFlashdata('error');
?>

<!-- Alerts -->
<?php if ($flashMsg): ?>
    <div class="alert alert-success bg-success-50 text-success-600 border-success-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashMsg) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($flashErr): ?>
    <div class="alert alert-danger bg-danger-50 text-danger-600 border-danger-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashErr) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Persetujuan') ?></h6>
        <p class="text-secondary-light mb-0">
            ID Permohonan: <strong class="text-primary-600">#<?= esc((string) $approvalId) ?></strong> &bull; Diajukan pada: <?= esc($createdDate) ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-12">
        <a href="<?= site_url('dashboard/sistem/approval') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Antrean
        </a>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Resource & Requester Details -->
    <div class="col-xl-8">
        <!-- Card 1: Informasi Permohonan -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 mb-24">
                    <div class="d-flex align-items-center gap-12">
                        <span class="w-48-px h-48-px radius-12 bg-primary-50 text-primary-600 d-inline-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:shield-check-bold" class="text-2xl"></iconify-icon>
                        </span>
                        <div>
                            <span class="text-xs text-secondary-light fw-medium">JENIS PERMINTAAN</span>
                            <h5 class="text-lg fw-semibold mb-0"><?= esc($typeLabel) ?></h5>
                        </div>
                    </div>
                    <div>
                        <?= view('dashboard/components/status_badge', [
                            'status' => $approval['status'],
                        ]) ?>
                    </div>
                </div>

                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Pemohon</p>
                        <h6 class="text-md fw-semibold mb-0"><?= esc($approval['requested_by_name'] ?: 'Sistem / Otomatis') ?></h6>
                        <span class="text-xs text-secondary-light"><?= esc($approval['requested_by_email'] ?: '-') ?></span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Waktu Pengajuan</p>
                        <h6 class="text-md fw-semibold mb-0"><?= esc($createdDate) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Tindakan Diminta</p>
                        <span class="badge bg-neutral-200 text-primary-light font-monospace py-4 px-8 radius-4">
                            <?= esc($approval['requested_action'] ?: 'review') ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Identitas Objek Terkait</p>
                        <h6 class="text-md fw-semibold mb-0 font-monospace">
                            <?= esc($approval['resource_type']) ?> #<?= esc((string) $approval['resource_id']) ?>
                        </h6>
                    </div>
                </div>

                <?php if (! empty($approval['notes'])): ?>
                    <hr class="my-20">
                    <p class="text-secondary-light text-sm mb-4">Catatan Pemohon</p>
                    <div class="p-16 bg-neutral-50 border border-neutral-200 radius-8">
                        <p class="text-primary-light mb-0"><?= nl2br(esc($approval['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card 2: Konteks Objek (Resource Context) -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between mb-16">
                    <div>
                        <span class="text-xs text-secondary-light fw-medium">KONTEKS DATA OBJEK</span>
                        <h6 class="text-lg fw-semibold mb-0"><?= esc($resourceContext['category']) ?></h6>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-6 text-xs fw-semibold">
                        <?= esc($resourceContext['score']) ?>
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle">
                        <tbody>
                            <?php foreach (($resourceContext['details'] ?? []) as $label => $val): ?>
                                <tr>
                                    <td class="text-secondary-light fw-medium" style="width: 35%;"><?= esc($label) ?></td>
                                    <td class="text-primary-light fw-semibold"><?= esc($val) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card 3: Keputusan Sebelumnya (Jika Telah Diproses) -->
        <?php if (! $isPending): ?>
            <div class="card">
                <div class="card-body p-24">
                    <h6 class="text-lg fw-semibold mb-16 d-flex align-items-center gap-8">
                        <iconify-icon icon="<?= $isApproved ? 'solar:check-circle-bold' : 'solar:close-circle-bold' ?>" class="<?= $isApproved ? 'text-success-main' : 'text-danger-main' ?> text-xl"></iconify-icon>
                        Keputusan Administrator
                    </h6>
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <p class="text-secondary-light text-sm mb-4">Diproses Oleh</p>
                            <h6 class="text-md fw-semibold mb-0"><?= esc($approval['reviewed_by_name'] ?: 'Administrator') ?></h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-secondary-light text-sm mb-4">Waktu Keputusan</p>
                            <h6 class="text-md fw-semibold mb-0"><?= esc($reviewedDate ?: '-') ?></h6>
                        </div>
                        <div class="col-12">
                            <p class="text-secondary-light text-sm mb-4"><?= $isApproved ? 'Catatan Persetujuan' : 'Alasan Penolakan' ?></p>
                            <div class="p-16 <?= $isApproved ? 'bg-success-50 border-success-200' : 'bg-danger-50 border-danger-200' ?> border radius-8">
                                <p class="<?= $isApproved ? 'text-success-700' : 'text-danger-700' ?> mb-0">
                                    <?= nl2br(esc($isApproved ? ($approval['notes'] ?: 'Disetujui tanpa catatan khusus.') : ($approval['reason'] ?: 'Ditolak tanpa catatan rincian.'))) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Approval Action Form -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg fw-semibold mb-16">Tindakan Persetujuan</h6>

                <?php if ($isPending): ?>
                    <p class="text-secondary-light text-sm mb-24">
                        Pilih tindakan untuk permohonan ini. Menyetujui permohonan akan mengeksekusi sistem target secara langsung.
                    </p>

                    <!-- Form Setujui -->
                    <form method="post" action="<?= site_url('dashboard/sistem/approval/' . $approvalId . '/setujui') ?>" class="mb-24">
                        <?= csrf_field() ?>
                        <div class="mb-16">
                            <label class="form-label text-sm fw-medium text-primary-light mb-8">Catatan Persetujuan (Opsional)</label>
                            <textarea name="notes" class="form-control radius-8" rows="3" placeholder="Masukkan catatan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success-600 text-white w-100 radius-8 py-11 d-inline-flex align-items-center justify-content-center gap-8 fw-semibold" data-confirm="Apakah Anda yakin ingin menyetujui permohonan otorisasi ini?" data-type="success" data-title="Konfirmasi Persetujuan Permohonan" data-btn-text="Ya, Setujui Permohonan">
                            <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon> Setujui Permohonan
                        </button>
                    </form>

                    <div class="d-flex align-items-center my-20">
                        <hr class="flex-grow-1 border-neutral-200 my-0">
                        <span class="px-12 text-xs text-secondary-light uppercase">ATAU</span>
                        <hr class="flex-grow-1 border-neutral-200 my-0">
                    </div>

                    <!-- Form Tolak -->
                    <form method="post" action="<?= site_url('dashboard/sistem/approval/' . $approvalId . '/tolak') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-16">
                            <label class="form-label text-sm fw-medium text-primary-light mb-8">Alasan Penolakan <span class="text-danger-main">*</span></label>
                            <textarea name="reason" class="form-control radius-8" rows="3" required placeholder="Jelaskan alasan penolakan agar diketahui pemohon..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger-600 w-100 radius-8 py-11 d-inline-flex align-items-center justify-content-center gap-8 fw-semibold" data-confirm="Apakah Anda yakin ingin menolak permohonan ini? Alasan penolakan akan dicatat dan dikirimkan ke pemohon." data-type="danger" data-title="Konfirmasi Penolakan Permohonan" data-btn-text="Ya, Tolak Permohonan">
                            <iconify-icon icon="solar:close-circle-bold" class="text-xl"></iconify-icon> Tolak Permohonan
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center py-20">
                        <span class="w-64-px h-64-px <?= $isApproved ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' ?> rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
                            <iconify-icon icon="<?= $isApproved ? 'solar:check-circle-bold' : 'solar:close-circle-bold' ?>" class="text-3xl"></iconify-icon>
                        </span>
                        <h6 class="text-md fw-semibold mb-6">Permohonan Telah Diproses</h6>
                        <p class="text-secondary-light text-sm mb-20">
                            Status permohonan ini saat ini adalah <strong><?= esc($approval['status']) ?></strong> dan tidak memerlukan tindakan lanjutan.
                        </p>
                        <a href="<?= site_url('dashboard/sistem/approval') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-10 w-100">
                            Lihat Antrean Lainnya
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
