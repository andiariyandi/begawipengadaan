<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    /**
     * Antrean approval sertifikat.
     * items[i] adalah row dari buildApprovalQueue() di CertificateApprovalController.
     *   - id              → approvals.id
     *   - source_type     → 'learning_enrollment' | 'event_registration'
     *   - source_label    → nama course / event
     *   - user_name       → nama peserta
     */
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Approval / Pengajuan Sertifikat</h6>
        <p class="text-secondary-light mb-0">Tinjau pengajuan sertifikat dari hasil deteksi kelayakan (learning & event).</p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/pengajuan-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
        Pengajuan Event
    </a>
</div>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Sumber</th>
                        <th>Peserta</th>
                        <th>Kursus / Event</th>
                        <th>Diajukan</th>
                        <th>Pemohon</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary-light py-24">
                            Tidak ada pengajuan sertifikat yang menunggu.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $row): ?>
                        <tr>
                            <td>
                                <span class="text-secondary-light text-sm">
                                    <?= $row['source_type'] === 'event_registration' ? 'Event' : 'Learning' ?>
                                    #<?= esc($row['resource_id']) ?>
                                </span>
                            </td>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($row['user_name'] ?? '—') ?></h6>
                                <small class="text-secondary-light"><?= esc($row['user_email'] ?? '') ?></small>
                            </td>
                            <td>
                                <?= esc($row['course_title'] ?? $row['event_title'] ?? $row['source_label'] ?? '—') ?>
                            </td>
                            <td>
                                <small class="text-secondary-light">
                                    <?= ! empty($row['created_at']) ? esc(date('d M Y H:i', strtotime($row['created_at']))) : '—' ?>
                                </small>
                            </td>
                            <td>
                                <small class="text-secondary-light"><?= esc($row['requested_by_name'] ?? 'system') ?></small>
                            </td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => $row['status']]) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.certificates.approvals.detail', $row['id']) ?>" class="text-primary-600 fw-semibold">
                                    Tinjau
                                </a>
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
