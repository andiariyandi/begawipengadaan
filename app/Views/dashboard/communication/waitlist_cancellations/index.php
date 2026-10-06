<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$waiting   = $waiting   ?? [];
$cancelled = $cancelled ?? [];
$promoted  = $promoted  ?? [];
$totalWaiting   = count($waiting);
$totalCancelled = count($cancelled);
$totalPromoted  = count($promoted);
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Waitlist dan Pembatalan</h6>
        <p class="text-secondary-light mb-0">Ringkasan antrian waitlist, pembatalan, dan promosi dari waitlist.</p>
    </div>
</div>

<div class="row gy-4">
    <div class="col-md-4">
        <div class="card"><div class="card-body p-24 text-center">
            <h2 class="text-primary-600 mb-0"><?= $totalWaiting ?></h2>
            <p class="text-secondary-light mb-0">Peserta Waitlist</p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body p-24 text-center">
            <h2 class="text-danger-600 mb-0"><?= $totalCancelled ?></h2>
            <p class="text-secondary-light mb-0">Pembatalan</p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body p-24 text-center">
            <h2 class="text-success-600 mb-0"><?= $totalPromoted ?></h2>
            <p class="text-secondary-light mb-0">Promoted (dari waitlist)</p>
        </div></div>
    </div>
</div>

<div class="card mt-16">
    <div class="card-header bg-base py-12 px-24">
        <h6 class="text-md mb-0">Waitlist</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Event</th><th>Peserta</th><th>Tanggal Daftar</th></tr></thead>
                <tbody>
                    <?php if (empty($waiting)): ?>
                        <tr><td colspan="3" class="text-center text-secondary-light p-16">Tidak ada peserta di waitlist.</td></tr>
                    <?php else: foreach ($waiting as $r): ?>
                        <tr>
                            <td><?= esc($r['event_title'] ?? '-') ?></td>
                            <td><?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?></td>
                            <td><?= esc($r['registered_at'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-16">
    <div class="card-header bg-base py-12 px-24">
        <h6 class="text-md mb-0">Pembatalan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Event</th><th>Peserta</th><th>Tanggal Batal</th><th>Alasan</th></tr></thead>
                <tbody>
                    <?php if (empty($cancelled)): ?>
                        <tr><td colspan="4" class="text-center text-secondary-light p-16">Tidak ada pembatalan.</td></tr>
                    <?php else: foreach ($cancelled as $r): ?>
                        <tr>
                            <td><?= esc($r['event_title'] ?? '-') ?></td>
                            <td><?= esc($r['user_full_name'] ?? $r['username'] ?? '-') ?></td>
                            <td><?= esc($r['cancelled_at'] ?? '-') ?></td>
                            <td><?= esc($r['status_reason'] ?? '-') ?: '-' ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
