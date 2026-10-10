<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Kehadiran</h6>
        <p class="text-secondary-light mb-0">Daftar pencatatan kehadiran event PBJ.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Peserta</th>
                        <th>Status</th>
                        <th>Dicatat</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="5" class="text-center text-secondary-light">Belum ada catatan kehadiran.</td></tr>
                    <?php else: foreach ($items as $a): ?>
                        <tr>
                            <td><?= esc($a['event_title'] ?? '-') ?></td>
                            <td><strong><?= esc($a['user_full_name'] ?? $a['username'] ?? '-') ?></strong></td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $a['status'] ?? 'present']) ?></td>
                            <td><?= esc($a['recorded_at'] ?? '-') ?></td>
                            <td><?= esc($a['recorder_username'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
