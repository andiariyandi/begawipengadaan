<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php $item = $item ?? []; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Registrasi</h6>
        <p class="text-secondary-light mb-0">ID: <?= (int) ($regId ?? 0) ?></p>
    </div>
    <a href="<?= route_to('dashboard.communication.event-registrations') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card"><div class="card-body p-24">
            <h5 class="mb-12"><?= esc($item['user_full_name'] ?? $item['username'] ?? '-') ?></h5>
            <p class="text-secondary-light mb-16"><?= esc($item['email'] ?? '-') ?></p>
            <p class="mb-16"><strong>Event:</strong> <?= esc($item['event_title'] ?? '-') ?></p>
            <p class="mb-16"><strong>Jadwal Event:</strong> <?= ! empty($item['event_start_at']) ? esc(date('d M Y H:i', strtotime($item['event_start_at']))) : '-' ?> s/d <?= ! empty($item['event_end_at']) ? esc(date('d M Y H:i', strtotime($item['event_end_at']))) : '-' ?></p>
            <p class="mb-16"><strong>Status:</strong> <?= view('dashboard/components/status_badge', ['status' => $item['status'] ?? 'registered']) ?></p>
        </div></div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-body p-24">
            <p class="text-secondary-light mb-4">Aksi Status</p>
            <form action="<?= route_to('dashboard.communication.event-registrations.updateStatus', (string) $regId) ?>" method="post" class="d-grid gap-2">
                <?= csrf_field() ?>
                <select class="form-select radius-8" name="status">
                    <?php foreach (['registered' => 'Terdaftar', 'attended' => 'Hadir', 'no_show' => 'Tidak Hadir', 'cancelled' => 'Dibatalkan', 'promoted' => 'Promoted (dari waitlist)'] as $k => $l): ?>
                        <option value="<?= $k ?>" <?= ($item['status'] ?? '') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary-600 radius-8">Perbarui Status</button>
            </form>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
