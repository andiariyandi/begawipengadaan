<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
/**
 * Halaman pilih event untuk pengajuan sertifikat batch.
 * Sumber: CertificateEventController::select()
 */
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Pengajuan Sertifikat Event</h6>
        <p class="text-secondary-light mb-0">Pilih event yang sudah selesai untuk membuat pengajuan sertifikat (approval) batch.</p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/approval') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
        Lihat Antrean Approval
    </a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Pelaksanaan</th>
                        <th>Status Event</th>
                        <th>Pending Approval</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary-light py-24">
                            Belum ada event yang memenuhi syarat pengajuan sertifikat
                            (<code>certificate_enabled = 1</code> dan <code>end_at &lt; NOW()</code>).
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($ev['title']) ?></h6>
                                <small class="text-secondary-light"><?= esc($ev['slug']) ?></small>
                            </td>
                            <td>
                                <small>
                                    <?= ! empty($ev['start_at']) ? esc(date('d M Y H:i', strtotime($ev['start_at']))) : '—' ?>
                                    –
                                    <?= ! empty($ev['end_at']) ? esc(date('d M Y H:i', strtotime($ev['end_at']))) : '—' ?>
                                </small>
                            </td>
                            <td>
                                <span class="text-sm"><?= esc($ev['status'] ?? '—') ?></span>
                            </td>
                            <td class="text-center">
                                <?= (int) ($ev['pending_approval_count'] ?? 0) > 0
                                    ? '<span class="badge bg-warning-focus text-warning-main">' . (int) $ev['pending_approval_count'] . '</span>'
                                    : '<span class="text-secondary-light text-sm">0</span>' ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.certificates.event-approval.preview', $ev['id']) ?>"
                                   class="btn btn-outline-neutral-300 btn-sm radius-8">
                                    Preview Peserta
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
