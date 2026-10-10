<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    /**
     * Detail approval sertifikat.
     *   $approval       row dari approvals + requested_by_name/email
     *   $source         hasil loadSourceContext() — row enrollment / event_registration
     */
    $a = $approval ?? [];
    $s = $source ?? [];
    $isPending = ($a['status'] ?? '') === 'pending';
    $sourceTypeLabel = $s['source_type'] === 'event_registration'
        ? 'Event Registration'
        : ($s['source_type'] === 'learning_enrollment' ? 'Learning Enrollment' : '—');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Approval Sertifikat</h6>
        <p class="text-secondary-light mb-0">Approval #<?= esc($approvalId) ?> · <?= esc($sourceTypeLabel) ?> #<?= esc($a['resource_id'] ?? '—') ?></p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/approval') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Data Pengajuan</h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status Approval</p>
                        <h6 class="mb-0"><?= view('dashboard/components/status_badge', ['status' => $a['status'] ?? 'pending']) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Aksi</p>
                        <h6 class="mb-0"><?= esc($a['requested_action'] ?? '—') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Tipe Sumber</p>
                        <h6 class="mb-0"><?= esc($sourceTypeLabel) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">ID Sumber</p>
                        <h6 class="mb-0"><?= esc($a['resource_id'] ?? '—') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Pemilik</p>
                        <h6 class="mb-0"><?= esc($s['user_name'] ?? '—') ?></h6>
                        <small class="text-secondary-light"><?= esc($s['user_email'] ?? '') ?></small>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Diajukan Oleh</p>
                        <h6 class="mb-0"><?= esc($a['requested_by_name'] ?? 'system') ?></h6>
                        <small class="text-secondary-light"><?= esc($a['requested_by_email'] ?? '') ?></small>
                    </div>
                    <?php if (! empty($s['course_title'])): ?>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kursus / Kelas</p>
                        <h6 class="mb-0"><?= esc($s['course_title']) ?></h6>
                        <small class="text-secondary-light"><?= esc($s['class_name'] ?? '') ?></small>
                    </div>
                    <?php endif; ?>
                    <?php if (! empty($s['event_title'])): ?>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Event</p>
                        <h6 class="mb-0"><?= esc($s['event_title']) ?></h6>
                        <small class="text-secondary-light">
                            <?php if (! empty($s['start_at'])): ?>
                                <?= esc(date('d M Y H:i', strtotime($s['start_at']))) ?> – <?= esc(date('d M Y H:i', strtotime($s['end_at']))) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php endif; ?>
                    <?php if (! empty($s['attendance_status'])): ?>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Kehadiran</p>
                        <h6 class="mb-0"><?= esc(ucfirst($s['attendance_status'])) ?></h6>
                    </div>
                    <?php endif; ?>
                    <?php if (! empty($a['notes'])): ?>
                    <div class="col-12">
                        <p class="text-secondary-light mb-4">Catatan Pengajuan</p>
                        <div class="p-12 radius-8" style="background:#f8fafc;border:1px solid #e5e7eb;"><?= esc($a['notes']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (! empty($a['reason'])): ?>
                    <div class="col-12">
                        <p class="text-secondary-light mb-4">Alasan Penolakan</p>
                        <div class="p-12 radius-8" style="background:#fef2f2;border:1px solid #fecaca;"><?= esc($a['reason']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Tindakan Approval</h6>
                <?php if ($isPending): ?>
                    <form method="post" action="<?= route_to('dashboard.certificates.approvals.issue', $approvalId) ?>" class="mb-16">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success-600 w-100 radius-8">
                            Setujui & Terbitkan
                        </button>
                        <small class="text-secondary-light d-block mt-2">
                            Approval akan ditandai approved dan diteruskan ke CertificateGenerator untuk membuat sertifikat + PDF + QR.
                        </small>
                    </form>
                    <hr/>
                    <form method="post" action="<?= route_to('dashboard.certificates.approvals.reject', $approvalId) ?>">
                        <?= csrf_field() ?>
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alasan Penolakan</label>
                        <textarea name="reason" class="form-control radius-8" rows="3" required minlength="5" maxlength="1000" placeholder="Wajib diisi (min. 5 karakter)"></textarea>
                        <button type="submit" class="btn btn-danger-600 w-100 radius-8 mt-12">
                            Tolak Pengajuan
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        Approval ini sudah diproses (status: <?= esc($a['status']) ?>).
                        Tidak ada tindakan yang dapat dilakukan.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
