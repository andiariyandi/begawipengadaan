<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items = $items ?? [];
$statusLabels = [
    'draft' => 'Draft', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan',
    'coming_soon' => 'Akan Datang', 'open_registration' => 'Pendaftaran Dibuka',
    'full' => 'Penuh', 'finished' => 'Selesai', 'cancelled' => 'Dibatalkan',
];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Event dan Agenda</h6>
        <p class="text-secondary-light mb-0">Kelola agenda, webinar, sosialisasi, workshop, dan kegiatan PBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.communication.events.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Event</a>
</div>

<?= $this->include('dashboard/components/filter_bar') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Tipe</th>
                        <th>Jadwal</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center text-secondary-light">Belum ada event. Klik "Tambah Event".</td></tr>
                    <?php else: foreach ($items as $e): ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($e['title'] ?? '-') ?></h6>
                                <span class="text-sm text-secondary-light">#<?= (int) $e['id'] ?> · <?= esc($e['slug'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($e['type'] ?? '-') ?> / <?= esc($e['mode'] ?? '-') ?></td>
                            <td>
                                <?= ! empty($e['start_at']) ? esc(date('d M Y H:i', strtotime($e['start_at']))) : '-' ?><br>
                                <?= ! empty($e['end_at']) ? '<small>s/d ' . esc(date('d M Y H:i', strtotime($e['end_at']))) . '</small>' : '' ?>
                            </td>
                            <td><?= esc($e['location'] ?: ($e['meeting_url'] ?? '-')) ?></td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $e['status'] ?? 'draft']) ?></td>
                            <td class="text-center">
                                <a href="<?= route_to('dashboard.communication.events.detail', (string) $e['id']) ?>" class="text-primary-600 me-12">Detail</a>
                                <a href="<?= route_to('dashboard.communication.events.edit', (string) $e['id']) ?>" class="text-warning-main me-12">Ubah</a>
                                <form action="<?= route_to('dashboard.communication.events.delete', (string) $e['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus event?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-link text-danger-main p-0">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
