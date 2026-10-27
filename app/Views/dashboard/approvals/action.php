<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isApprove = ($action ?? '') === 'approve';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Konfirmasi Tindakan Persetujuan') ?></h6>
        <p class="text-secondary-light mb-0">ID Permohonan: <strong class="text-primary-600">#<?= esc((string) $approvalId) ?></strong> &bull; <?= esc($typeLabel ?? 'Permohonan Sistem') ?></p>
    </div>
    <a href="<?= site_url('dashboard/sistem/approval/' . ($approvalId ?? '')) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Detail
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card">
            <div class="card-body p-32">
                <div class="text-center">
                    <span class="w-80-px h-80-px <?= $isApprove ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' ?> rounded-circle d-inline-flex align-items-center justify-content-center mb-20">
                        <iconify-icon icon="<?= $isApprove ? 'solar:check-circle-bold' : 'solar:close-circle-bold' ?>" class="text-5xl"></iconify-icon>
                    </span>
                    <h5 class="fw-semibold mb-8">
                        Konfirmasi <?= $isApprove ? 'Persetujuan' : 'Penolakan' ?> Permohonan
                    </h5>
                    <p class="text-secondary-light mb-28">
                        <?= $isApprove
                            ? 'Menyetujui permohonan ini akan memperbarui status menjadi disetujui dan memicu proses otomatis pada sistem target.'
                            : 'Menolak permohonan ini akan mencatat alasan penolakan dan menghentikan proses permohonan.' ?>
                    </p>
                </div>

                <form method="post" action="<?= site_url('dashboard/sistem/approval/' . $approvalId . '/' . ($isApprove ? 'setujui' : 'tolak')) ?>">
                    <?= csrf_field() ?>

                    <?php if ($isApprove): ?>
                        <div class="mb-24">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan Persetujuan (Opsional)</label>
                            <textarea name="notes" class="form-control radius-8" rows="4" placeholder="Masukkan instruksi atau catatan khusus jika diperlukan..."></textarea>
                        </div>
                    <?php else: ?>
                        <div class="mb-24">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Alasan Penolakan <span class="text-danger-main">*</span>
                            </label>
                            <textarea name="reason" class="form-control radius-8" rows="4" required placeholder="Jelaskan alasan penolakan secara jelas agar diketahui pemohon..."></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-12 mt-28">
                        <a href="<?= site_url('dashboard/sistem/approval/' . ($approvalId ?? '')) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                            Batal
                        </a>
                        <button type="submit" class="btn <?= $isApprove ? 'btn-success-600 text-white' : 'btn-danger-600' ?> radius-8 px-24 py-11 d-inline-flex align-items-center gap-8 fw-semibold">
                            <iconify-icon icon="<?= $isApprove ? 'solar:check-circle-bold' : 'solar:close-circle-bold' ?>"></iconify-icon>
                            <?= $isApprove ? 'Konfirmasi & Setujui' : 'Konfirmasi & Tolak' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
