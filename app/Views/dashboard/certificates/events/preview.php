<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
/**
 * Preview peserta event sebelum melakukan generate.
 * - participants[i].eligible = 1 jika attendance = present
 * - participants[i].pending_approval = 1 jika sudah punya approval pending (idempoten)
 * - participants[i].existing_certificate = 1 jika certificate sudah pernah diterbitkan
 *
 * Tombol "Generate Pengajuan" HANYA akan membuat baris approvals
 * (status pending). Certificate row TIDAK dibuat sampai approval
 * disetujui di CertificateApprovalController::issue().
 */
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Preview Peserta Event</h6>
        <p class="text-secondary-light mb-0">
            <?= esc($event['title'] ?? '—') ?>
            <?php if (! empty($event['start_at'])): ?>
                · <?= esc(date('d M Y H:i', strtotime($event['start_at']))) ?>
            <?php endif; ?>
        </p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/pengajuan-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= route_to('dashboard.certificates.event-approval.generate', $eventId) ?>" class="mb-24">
            <?= csrf_field() ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-12">
                <div>
                    <h6 class="mb-8">Peserta Eligible (kehadiran: Present)</h6>
                    <p class="mb-0 text-secondary-light text-sm">
                        Sistem hanya akan membuat approval untuk peserta yang Attendance <code>present</code>.
                        Approvals bertipe <code>certificate</code> dengan status <code>pending</code>.
                    </p>
                </div>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">
                    Generate Pengajuan
                </button>
            </div>
        </form>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Status Registrasi</th>
                        <th>Status Kehadiran</th>
                        <th>Eligible</th>
                        <th>Approval Pending</th>
                        <th>Certificate Existing</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($participants)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary-light py-24">
                            Belum ada peserta terdaftar untuk event ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($participants as $p): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($p['user_name'] ?? '—') ?></h6>
                                <small class="text-secondary-light"><?= esc($p['user_email'] ?? '') ?></small>
                            </td>
                            <td>
                                <span class="text-sm"><?= esc($p['registration_status'] ?? '—') ?></span>
                            </td>
                            <td>
                                <span class="text-sm"><?= esc($p['attendance_status'] ?? '— (belum ada)') ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ((int) ($p['eligible'] ?? 0) === 1): ?>
                                    <span class="badge bg-success-focus text-success-main">Ya</span>
                                <?php else: ?>
                                    <span class="text-secondary-light text-sm">Tidak</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= (int) ($p['pending_approval'] ?? 0) > 0
                                    ? '<span class="badge bg-warning-focus text-warning-main">' . (int) $p['pending_approval'] . '</span>'
                                    : '<span class="text-secondary-light text-sm">0</span>' ?>
                            </td>
                            <td class="text-center">
                                <?= (int) ($p['existing_certificate'] ?? 0) > 0
                                    ? '<span class="badge bg-info-focus text-info-main">Ada</span>'
                                    : '<span class="text-secondary-light text-sm">—</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
