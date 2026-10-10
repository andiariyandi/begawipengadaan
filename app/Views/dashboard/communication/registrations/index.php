<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $items = $items ?? []; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Registrasi Event</h6>
        <p class="text-secondary-light mb-0">Daftar pendaftar event PBJ.</p>
    </div>
</div>

<?= $this->include('dashboard/components/filter_bar') ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Peserta</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="5" class="text-center text-secondary-light">Belum ada registrasi.</td></tr>
                    <?php else: foreach ($items as $r): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($r['event_title'] ?? '-') ?></h6>
                                <small class="text-secondary-light"><?= ! empty($r['event_start_at']) ? esc(date('d M Y H:i', strtotime($r['event_start_at']))) : '-' ?></small>
                            </td>
                            <td>
                                <strong><?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?></strong><br>
                                <small class="text-secondary-light"><?= esc($r['email'] ?? '-') ?></small>
                            </td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $r['status'] ?? 'registered']) ?></td>
                            <td><?= ! empty($r['registered_at']) ? esc(date('d M Y H:i', strtotime($r['registered_at']))) : '-' ?></td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.communication.event-registrations.detail', (string) $r['id']) ?>" class="text-primary-600">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
